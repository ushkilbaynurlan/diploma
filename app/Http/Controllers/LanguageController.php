<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Auth;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index($lang)
    {
        $user = \Auth::user();
        if($user->can('lang-manage'))
        {
            $dir = base_path() . '/resources/lang/' . $lang;

            $arrLabel = json_decode(file_get_contents($dir . '.json'));

            $arrFiles   = array_diff(
                scandir($dir), array(
                                 '..',
                                 '.',
                             )
            );
            $arrMessage = [];
            foreach($arrFiles as $file)
            {
                $fileName = basename($file, ".php");
                $fileData = $myArray = include $dir . "/" . $file;
                if(is_array($fileData))
                {
                    $arrMessage[$fileName] = $fileData;
                }
            }

            return view('admin.lang.index', compact('user', 'lang', 'arrLabel', 'arrMessage'));
        }
        else
        {
            return view('403');
        }
    }

    public function create()
    {
        $user = \Auth::user();

        return view('admin.lang.create', compact('user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $user     = \Auth::user();
        $file     = new Filesystem();
        $langCode = strtolower($request->code);

        $langDir = base_path() . '/resources/lang/';
        $dir     = $langDir;
        if(!is_dir($dir))
        {
            mkdir($dir);
            chmod($dir, 0777);
        }

        if(!file_exists($dir . '/en.json'))
        {
            \File::copy($langDir . 'en.json', $dir . '/en.json');
            if(!is_dir($dir . "/en"))
            {
                mkdir($dir . "/en");
                chmod($dir . "/en", 0777);
            }
            $file->copyDirectory($langDir . "en", $dir . "/en/");
        }

        $dir      = $dir . '/' . $langCode;
        $jsonFile = $dir . ".json";
        \File::copy($langDir . 'en.json', $jsonFile);

        if(!is_dir($dir))
        {
            mkdir($dir);
            chmod($dir, 0777);
        }

        $file->copyDirectory($langDir . "en", $dir . "/");

        return redirect()->route('admin.lang.index', [$langCode])->with('success', __('Language created successfully'));
    }


    public function storeData(Request $request, $lang)
    {
        $user = \Auth::user();

        $dir = base_path() . '/resources/lang';
        if(!is_dir($dir))
        {
            mkdir($dir);
            chmod($dir, 0777);
        }
        $jsonFile = $dir . "/" . $lang . ".json";

        file_put_contents($jsonFile, json_encode($request->label));

        $langFolder = $dir . "/" . $lang;
        if(!is_dir($langFolder))
        {
            mkdir($langFolder);
            chmod($langFolder, 0777);
        }

        foreach($request->message as $fileName => $fileData)
        {
            $content = "<?php return [";
            $content .= $this->buildArray($fileData);
            $content .= "];";
            file_put_contents($langFolder . "/" . $fileName . '.php', $content);
        }

        return redirect()->route('admin.lang.index', [$lang])->with('success', __('Language save successfully'));
    }

    public function buildArray($fileData)
    {
        $content = "";
        foreach($fileData as $lable => $data)
        {
            if(is_array($data))
            {
                $content .= "'$lable'=>[" . $this->buildArray($data) . "],";
            }
            else
            {
                $content .= "'$lable'=>'" . addslashes($data) . "',";
            }
        }

        return $content;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update($lang)
    {
        $user    = \Auth::user();
        $arrLang = $user->languages();
        if(in_array($lang, $arrLang))
        {
            $user->lang = $lang;
            $user->save();

            return redirect()->back()->with('success', __('Language change successfully'));
        }
        else
        {
            redirect('admin.dashboard');
        }
    }

    public function destroyLang($lang)
    {
        $default_lang = Utility::getSettingValByName('DEFAULT_LANG') ?? 'en';

        $langDir = base_path() . '/resources/lang/';
        if(is_dir($langDir))
        {
            // remove directory and file
            User::delete_directory($langDir . $lang);
            // unlink($langDir . $lang . '.json');
            // update user that has assign deleted language.
            User::where('lang', 'LIKE', $lang)->update(['lang' => $default_lang]);
        }

        return redirect()->route('admin.lang.index', [Auth::user()->lang])->with('success', __('Language Deleted Successfully!'));
    }
}
