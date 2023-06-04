<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Mail\EmailTest;
use App\Models\Setting;
use App\Models\Settings;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Artisan;

class SettingsController extends Controller
{
    public function index()
    {
        $user = \Auth::user();
        if($user->can('manage-setting'))
        {
            $lang         = $user->languages();
            $customFields = CustomField::orderBy('order')->get();
            $setting      = Utility::settings();

            return view('admin.users.setting', compact('lang', 'customFields', 'setting'));
        }
        else
        {
            return view('403');
        }

    }

    public function store(Request $request)
    {
        // dd($request->all());
        $user = \Auth::user();
        $post = [];
        if($user->can('manage-setting'))
        {
            if($request->favicon)
            {
                // $request->validate(['favicon' => 'required|image|mimes:jpeg,jpg,png|max:204800']);
                // $request->favicon->storeAs('logo', 'favicon.png');

                $request->validate(
                    [
                        'favicon' => 'image',
                        ]
                    );
                $favicon = 'favicon.png';
                $dir = 'uploads/logo/';
                $validation =[
                    'mimes:'.'png',
                    'max:'.'20480',
                ];
                $path = Utility::upload_file($request,'favicon',$favicon,$dir,$validation);
                if($path['flag'] == 1){
                    $favicon = $path['url'];
                }else{
                    return redirect()->back()->with('error', __($path['msg']));
                }

            }
            if(!empty($request->logo))
            {
                // $request->validate(['logo' => 'required|image|mimes:jpeg,jpg,png|max:204800']);
                // $request->logo->storeAs('logo', 'logo-dark.png');

                $request->validate(
                    [
                        'logo' => 'image',
                        ]
                    );
                $logoName = 'logo-dark.png';
                $dir = 'uploads/logo/';
                $validation =[
                    'mimes:'.'png',
                    'max:'.'20480',
                ];
                $path = Utility::upload_file($request,'logo',$logoName,$dir,$validation);
                if($path['flag'] == 1){
                    $logo = $path['url'];
                }else{
                    return redirect()->back()->with('error', __($path['msg']));
                }

            }

            if($request->white_logo)
            {
                // $request->validate(['white_logo' => 'required|image|mimes:jpeg,jpg,png|max:204800']);
                // $request->white_logo->storeAs('logo', 'logo-light.png');
                $request->validate(
                    [
                        'white_logo' => 'image',
                        ]
                );
                $lightlogoName = 'logo-light.png';
                $dir = 'uploads/logo/';
                $validation =[
                    'mimes:'.'png',
                    'max:'.'20480',
                ];
                $path = Utility::upload_file($request,'white_logo',$lightlogoName,$dir,$validation);

                if($path['flag'] == 1){
                    $white_logo = $path['url'];
                }else{
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }

            $rules = [
                'APP_NAME' => 'required|string|max:50',
                'default_language' => 'required|string|max:50',
                'footer_text' => 'required|string|max:50',
            ];

            $validator = \Validator::make(
                $request->all(), $rules
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $arrEnv = [
                'APP_NAME' => $request->APP_NAME,
            ];

            $default_language = $request->has('default_language') ? $request-> default_language : 'en';
            $post['DEFAULT_LANG'] = $default_language;
            // dd($request->site_rtl);
            $site_rtl = $request->has('site_rtl') ? $request->site_rtl : 'off';
            $post['SITE_RTL'] = $site_rtl;
            $post['SITE_RTL'] = $request->has('site_rtl') && $site_rtl == "on" ? $request-> site_rtl : '';

            $footer_text = $request->has('footer_text') ? $request-> footer_text : '';
            $post['FOOTER_TEXT'] = $footer_text;

            $gdpr_cookie = $request->has('gdpr_cookie') ? $request-> gdpr_cookie : 'off';
            $post['gdpr_cookie'] = $gdpr_cookie;
            $post['cookie_text'] = $request->has('cookie_text') && $gdpr_cookie == "on" ? $request-> cookie_text : '';

            $faq = $request->has('faq') ? $request-> faq : 'off';
            $post['FAQ'] = $faq;
            $post['FAQ'] = $request->has('faq') && $faq == "on" ? $request-> faq : '';


            $knowledge_base = $request->has('knowledge') ? $request-> knowledge : 'off';
            $post['Knowlwdge_Base'] = $knowledge_base;

            $color = $request->has('color') ? $request-> color : 'theme-3';
            $post['color'] = $color;

            $cust_theme_bg = (!empty($request->cust_theme_bg)) ? 'on' : 'off';
            $post['cust_theme_bg'] = $cust_theme_bg;

            $cust_darklayout = !empty($request->cust_darklayout) ? 'on' : 'off';
            $post['cust_darklayout'] = $cust_darklayout;

            if (isset($post) && !empty($post) && count($post) > 0) {
                $created_at = $updated_at = date('Y-m-d H:i:s');
                foreach ($post as $key => $data) {
                    \DB::insert(
                        'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ',
                        [$data, $key, Auth::user()->id, $created_at, $updated_at,]
                    );
                }
            }
            return redirect()->back()->with('success', __('Setting updated successfully'));

            Artisan::call('config:cache');
	        Artisan::call('config:clear');

        }
        else
        {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

    public function emailSettingStore(Request $request)
    {
        $user = \Auth::user();
        if($user->can('manage-setting'))
        {
            $rules = [
                'mail_driver' => 'required|string|max:50',
                'mail_host' => 'required|string|max:50',
                'mail_port' => 'required|string|max:50',
                'mail_username' => 'required|string|max:50',
                'mail_password' => 'required|string|max:255',
                'mail_encryption' => 'required|string|max:50',
                'mail_from_address' => 'required|string|max:50',
                'mail_from_name' => 'required|string|max:50',
            ];

            $validator = \Validator::make(
                $request->all(), $rules
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $arrEnv = [
                'MAIL_DRIVER' => $request->mail_driver,
                'MAIL_HOST' => $request->mail_host,
                'MAIL_PORT' => $request->mail_port,
                'MAIL_USERNAME' => $request->mail_username,
                'MAIL_PASSWORD' => $request->mail_password,
                'MAIL_ENCRYPTION' => $request->mail_encryption,
                'MAIL_FROM_ADDRESS' => $request->mail_from_address,
                'MAIL_FROM_NAME' => $request->mail_from_name,
            ];

            if($this->setEnvironmentValue($arrEnv))
            {
                return redirect()->back()->with('success', __('Email Settings updated successfully'));
            }
            else
            {
                return redirect()->back()->with('error', __('Something is wrong'));
            }

            Artisan::call('config:cache');
	        Artisan::call('config:clear');

        }
        else
        {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

    public function recaptchaSettingStore(Request $request)
    {
        $user = \Auth::user();
        if($user->can('manage-setting'))
        {
            $rules = [];

            if($request->recaptcha_module == 'yes')
            {
                $rules['google_recaptcha_key'] = 'required|string|max:50';
                $rules['google_recaptcha_secret'] = 'required|string|max:50';
            }

            $validator = \Validator::make(
                $request->all(), $rules
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $arrEnv = [
                'RECAPTCHA_MODULE' => $request->recaptcha_module ?? 'no',
                'NOCAPTCHA_SITEKEY' => $request->google_recaptcha_key,
                'NOCAPTCHA_SECRET' => $request->google_recaptcha_secret,
            ];

            if($this->setEnvironmentValue($arrEnv))
            {
                return redirect()->back()->with('success', __('Recaptcha Settings updated successfully'));
            }
            else
            {
                return redirect()->back()->with('error', __('Something is wrong'));
            }

            return redirect()->back()->with('success', __('Recaptcha Settings updated successfully'));

        }
        else
        {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

    public function pusherSettingStore(Request $request)
    {
        $user = \Auth::user();
        if($user->can('manage-setting'))
        {
            $rules = [];

            if($request->enable_chat == 'yes')
            {
                $rules['pusher_app_id']      = 'required|string|max:50';
                $rules['pusher_app_key']     = 'required|string|max:50';
                $rules['pusher_app_secret']  = 'required|string|max:50';
                $rules['pusher_app_cluster'] = 'required|string|max:50';
            }

            $validator = \Validator::make(
                $request->all(), $rules
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();
                return redirect()->back()->with('error', $messages->first());
            }

            $post = [];

            $enable_chat = (!empty($request->enable_chat)) ? 'yes' : 'no';
            $post['CHAT_MODULE'] = $enable_chat;

            $pusher_app_id = $request->has('pusher_app_id') ? $request-> pusher_app_id : '';
            $post['PUSHER_APP_ID'] = $pusher_app_id;

            $pusher_app_key = $request->has('pusher_app_key') ? $request-> pusher_app_key : '';
            $post['PUSHER_APP_KEY'] = $pusher_app_key;

            $pusher_app_secret = $request->has('pusher_app_secret') ? $request-> pusher_app_secret : '';
            $post['PUSHER_APP_SECRET'] = $pusher_app_secret;

            $pusher_app_cluster = $request->has('pusher_app_cluster') ? $request-> pusher_app_cluster : '';
            $post['PUSHER_APP_CLUSTER'] = $pusher_app_cluster;


            if(isset($post) && !empty($post) && count($post) > 0)
            {
                $created_at = $updated_at = date('Y-m-d H:i:s');

                foreach($post as $key => $data)
                {

                    \DB::insert(
                        'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [$data, $key, Auth::user()->id, $created_at, $updated_at, ]
                    );
                }
            }
            return redirect()->back()->with('success', __('Pusher Settings updated successfully'));
        }
        else
        {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

    public static function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str     = file_get_contents($envFile);
        if(count($values) > 0)
        {
            foreach($values as $envKey => $envValue)
            {
                $keyPosition       = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine           = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

                // If key does not exist, add it
                if(!$keyPosition || !$endOfLinePosition || !$oldLine)
                {
                    $str .= "{$envKey}='{$envValue}'\n";
                }
                else
                {
                    $str = str_replace($oldLine, "{$envKey}='{$envValue}'", $str);
                }
            }
        }
        $str = substr($str, 0, -1);
        $str .= "\n";

        return file_put_contents($envFile, $str) ? true : false;
    }

    public function testEmail(Request $request)
    {
        $user = \Auth::user();
        if($user->can('manage-setting'))
        {
            $data                      = [];
            $data['mail_driver']       = $request->mail_driver;
            $data['mail_host']         = $request->mail_host;
            $data['mail_port']         = $request->mail_port;
            $data['mail_username']     = $request->mail_username;
            $data['mail_password']     = $request->mail_password;
            $data['mail_encryption']   = $request->mail_encryption;
            $data['mail_from_address'] = $request->mail_from_address;
            $data['mail_from_name']    = $request->mail_from_name;

            return view('admin.users.test_email', compact('data'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function testEmailSend(Request $request)
    {
        $validator = \Validator::make(
            $request->all(), [
                               'email' => 'required|email',
                               'mail_driver' => 'required',
                               'mail_host' => 'required',
                               'mail_port' => 'required',
                               'mail_username' => 'required',
                               'mail_password' => 'required',
                               'mail_from_address' => 'required',
                               'mail_from_name' => 'required',
                           ]
        );
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        try
        {
            config(
                [
                    'mail.driver' => $request->mail_driver,
                    'mail.host' => $request->mail_host,
                    'mail.port' => $request->mail_port,
                    'mail.encryption' => $request->mail_encryption,
                    'mail.username' => $request->mail_username,
                    'mail.password' => $request->mail_password,
                    'mail.from.address' => $request->mail_from_address,
                    'mail.from.name' => $request->mail_from_name,
                ]
            );
            Mail::to($request->email)->send(new EmailTest());
        }
        catch(\Exception $e)
        {
            return response()->json(
                [
                    'is_success' => false,
                    'message' => $e->getMessage(),
                ]
            );
        }

        return response()->json(
            [
                'is_success' => true,
                'message' => __('Email send Successfully'),
            ]
        );
    }


    public function storeCustomFields(Request $request)
    {
        $rules      = [
            'fields' => 'required|present|array',
        ];
        $attributes = [];

        if($request->fields)
        {
            foreach($request->fields as $key => $val)
            {
                $rules['fields.' . $key . '.name']      = 'required|max:255';
                $attributes['fields.' . $key . '.name'] = __('Field Name');
            }
        }

        $validator = \Validator::make($request->all(), $rules, [], $attributes);
        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }


        $field_ids = CustomField::orderBy('order')->pluck('id')->toArray();

        $order = 0;

        foreach($request->fields as $key => $field)
        {
            $fieldObj = new CustomField();
            if(isset($field['id']) && !empty($field['id']))
            {
                $fieldObj = CustomField::find($field['id']);
                if(($key = array_search($fieldObj->id, $field_ids)) !== false)
                {
                    unset($field_ids[$key]);
                }
            }
            $fieldObj->name        = $field['name'];
            $fieldObj->placeholder = $field['placeholder'];
            if(isset($field['type']) && !empty($field['type']))
            {
                if(isset($fieldObj->id) && $fieldObj->id > 6)
                {
                    $fieldObj->type = $field['type'];
                }
                elseif(!isset($fieldObj->id))
                {
                    $fieldObj->type = $field['type'];
                }
            }
            $fieldObj->width  = (isset($field['width'])) ? $field['width'] : '12';
            $fieldObj->status = 1;
            if(isset($field['is_required']))
            {
                if(isset($fieldObj->id) && $fieldObj->id > 6)
                {
                    $fieldObj->is_required = $field['is_required'];
                }
                elseif(!isset($fieldObj->id))
                {
                    $fieldObj->is_required = $field['is_required'];
                }
            }
            $fieldObj->created_by = Auth::id();
            $fieldObj->order      = $order++;
            $fieldObj->save();
        }

        if(!empty($field_ids) && count($field_ids) > 0)
        {
            CustomField::whereIn('id', $field_ids)->where('status', 1)->delete();
        }

        return redirect()->back()->with('success', __('Fields Saves Successfully.!'));
    }

    public function saveCompanySettings(Request $request)
    {
        if(\Auth::user()->can('manage-company-settings'))
        {
            $user = \Auth::user();
            $request->validate(
                [
                    'company_name' => 'required|string|max:50',
                    'company_email' => 'required',
                    'company_email_from_name' => 'required|string',
                ]
            );
            $post = $request->all();
            unset($post['_token']);

            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            foreach($post as $key => $data)
            {
                \DB::insert(
                    'INSERT INTO settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ', [
                                                                                                                                                                                                                      $data,
                                                                                                                                                                                                                      $key,
                                                                                                                                                                                                                      \Auth::user()->createId(),
                                                                                                                                                                                                                      $created_at,
                                                                                                                                                                                                                      $updated_at,
                                                                                                                                                                                                                  ]
                );
            }

            return redirect()->back()->with('success', __('Setting successfully updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function storageSettingStore(Request $request)
    {

        if(isset($request->storage_setting) && $request->storage_setting == 'local')
        {
            $request->validate(
                [

                    'local_storage_validation' => 'required',
                    'local_storage_max_upload_size' => 'required',
                ]
            );

            $post['storage_setting'] = $request->storage_setting;
            $local_storage_validation = implode(',', $request->local_storage_validation);
            $post['local_storage_validation'] = $local_storage_validation;
            $post['local_storage_max_upload_size'] = $request->local_storage_max_upload_size;

        }

        if(isset($request->storage_setting) && $request->storage_setting == 's3')
        {
            $request->validate(
                [
                    's3_key'                  => 'required',
                    's3_secret'               => 'required',
                    's3_region'               => 'required',
                    's3_bucket'               => 'required',
                    's3_url'                  => 'required',
                    's3_endpoint'             => 'required',
                    's3_max_upload_size'      => 'required',
                    's3_storage_validation'   => 'required',
                ]
            );
            $post['storage_setting']            = $request->storage_setting;
            $post['s3_key']                     = $request->s3_key;
            $post['s3_secret']                  = $request->s3_secret;
            $post['s3_region']                  = $request->s3_region;
            $post['s3_bucket']                  = $request->s3_bucket;
            $post['s3_url']                     = $request->s3_url;
            $post['s3_endpoint']                = $request->s3_endpoint;
            $post['s3_max_upload_size']         = $request->s3_max_upload_size;
            $s3_storage_validation              = implode(',', $request->s3_storage_validation);
            $post['s3_storage_validation']      = $s3_storage_validation;
        }

        if(isset($request->storage_setting) && $request->storage_setting == 'wasabi')
        {
            $request->validate(
                [
                    'wasabi_key'                    => 'required',
                    'wasabi_secret'                 => 'required',
                    'wasabi_region'                 => 'required',
                    'wasabi_bucket'                 => 'required',
                    'wasabi_url'                    => 'required',
                    'wasabi_root'                   => 'required',
                    'wasabi_max_upload_size'        => 'required',
                    'wasabi_storage_validation'     => 'required',
                ]
            );
            $post['storage_setting']            = $request->storage_setting;
            $post['wasabi_key']                 = $request->wasabi_key;
            $post['wasabi_secret']              = $request->wasabi_secret;
            $post['wasabi_region']              = $request->wasabi_region;
            $post['wasabi_bucket']              = $request->wasabi_bucket;
            $post['wasabi_url']                 = $request->wasabi_url;
            $post['wasabi_root']                = $request->wasabi_root;
            $post['wasabi_max_upload_size']     = $request->wasabi_max_upload_size;
            $wasabi_storage_validation          = implode(',', $request->wasabi_storage_validation);
            $post['wasabi_storage_validation']  = $wasabi_storage_validation;
        }

        foreach($post as $key => $data)
        {

            $arr = [
                $data,
                $key,
                \Auth::user()->id,
            ];

            \DB::insert(
                'insert into settings (`value`, `name`,`created_by`) values (?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`) ', $arr
            );
        }

        return redirect()->back()->with('success', 'Storage setting successfully updated.');

    }

}
