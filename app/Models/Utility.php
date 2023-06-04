<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommonEmailTemplate;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;
use Date;

class Utility extends Model
{
    public static function getValByName($key)
    {
        $setting = Utility::settings();

        if(!isset($_ENV[$key]) || empty($_ENV[$key]))
        {
            $_ENV[$key] = '';
        }

        return $_ENV[$key];
    }

    public static function getSettingValByName($key)
    {
        $setting = self::settings();

        if(!isset($setting[$key]) || empty($setting[$key]))
        {
            $setting[$key] = '';
        }

        return $setting[$key];
    }

    public static function settings()
    {
        $user = \Auth::user();


        $data = DB::table('settings')->get();

        $settings = [
            "Knowlwdge_Base" => "on",
            "FAQ" => "off",
            "SITE_RTL" => "off",
            "gdpr_cookie" => "",
            "cookie_text" => "",
            "dark_logo" => "logo-dark.png",
            "light_logo" => "logo-light.png",
            "color" => "theme-3",
            'DEFAULT_LANG' => 'en',
            'CHAT_MODULE' => "yes",
            'FOOTER_TEXT' => "© Copyright TicketGo",
            'company_name' => "",
            'company_email' => "",
            'cust_theme_bg'=> "on",
            "storage_setting" => "local",
            "local_storage_validation" => "jpg,jpeg,png,xlsx,xls,csv,pdf",
            "local_storage_max_upload_size" => "2048000",
            "s3_key" => "",
            "s3_secret" => "",
            "s3_region" => "",
            "s3_bucket" => "",
            "s3_url"    => "",
            "s3_endpoint" => "",
            "s3_max_upload_size" => "",
            "s3_storage_validation" => "",
            "wasabi_key" => "",
            "wasabi_secret" => "",
            "wasabi_region" => "",
            "wasabi_bucket" => "",
            "wasabi_url" => "",
            "wasabi_root" => "",
            "wasabi_max_upload_size" => "",
            "wasabi_storage_validation" => "",
            "company_name" => "",
            "company_address" => "",
            "company_city" => "",
            "company_state" => "",
            "company_zipcode" => "",
            "company_country" => "",
            "company_telephone" => "",
            "company_email" => "",
            "company_email_from_name" => "",
        ];

        foreach($data as $row)
        {
            $settings[$row->name] = $row->value;
        }
        return $settings;
    }

    public static function addNewData()
    {
        \Artisan::call('cache:forget spatie.permission.cache');
        \Artisan::call('cache:clear');

        $usr            = \Auth::user();
        $arrPermissions = [

            "manage-knowledge",
            "create-knowledge",
            "edit-knowledge",
            "delete-knowledge",
            "manage-knowledgecategory",
            "create-knowledgecategory",
            "edit-knowledgecategory",
            "delete-knowledgecategory",

        ];
        foreach ($arrPermissions as $ap) {
            // check if permission is not created then create it.
            $permission = Permission::where('name', 'LIKE', $ap)->first();
            if (empty($permission)) {
                Permission::create(['name' => $ap]);
            }
        }
        $adminRole          = Role::where('name', 'LIKE', 'Admin')->first();
        $adminPermissions   = $adminRole->getPermissionNames()->toArray();
        $adminNewPermission = [
            "manage-knowledge",
            "create-knowledge",
            "edit-knowledge",
            "delete-knowledge",
            "manage-knowledgecategory",
            "create-knowledgecategory",
            "edit-knowledgecategory",
            "delete-knowledgecategory",
        ];
        foreach ($adminNewPermission as $op) {
            // check if permission is not assign to owner then assign.
            if (!in_array($op, $adminPermissions)) {
                $permission = Permission::findByName($op);
                $adminRole->givePermissionTo($permission);
            }
        }
        $agentRole          = Role::where('name', 'LIKE', 'Agent')->first();
        $agentPermissions   = $agentRole->getPermissionNames()->toArray();
        $agentNewPermission = [

        ];
        foreach ($agentNewPermission as $op) {
            // check if permission is not assign to owner then assign.
            if (!in_array($op, $agentPermissions)) {
                $permission = Permission::findByName($op);
                $agentRole->givePermissionTo($permission);
            }
        }
    }

    public static function get_superadmin_logo(){
        $is_dark_mode = self::getSettingValByName('cust_darklayout');
        if($is_dark_mode == 'on'){
            return 'logo-light.png';
        }else{
            return 'logo-dark.png';
        }
    }

    public static function defaultEmail()
    {
        // Email Template
        $emailTemplate = [
            'New User',
            'New Ticket',
            'New Ticket Reply',
            // 'Lead Assign',
        ];

        foreach ($emailTemplate as $eTemp) {
            EmailTemplate::create(
                [
                    'name' => $eTemp,
                    'from' => env('APP_NAME'),
                    'slug' => strtolower(str_replace(' ', '_', $eTemp)),
                    'created_by' => 1,
                ]
            );
        }

        $defaultTemplate = [
            'New User' => [
                'subject' => 'Login Detail',
                'lang' => [
                    'ar' => '<p>مرحبا ، مرحبا بك في { app_name }.</p>
                            <p>&nbsp;</p>
                            <p>البريد الالكتروني : { mail }</p>
                            <p>كلمة السرية : { password }</p>
                            <p>{ app_url }</p>
                            <p>&nbsp;</p>
                            <p>شكرا</p>
                            <p>{ app_name }</p>',
                    'da' => '<p>Hej, velkommen til { app_name }.</p>
                            <p>&nbsp;</p>
                            <p>E-mail: { email }-</p>
                            <p>kodeord: { password }</p>
                            <p>{ app_url }</p>
                            <p>&nbsp;</p>
                            <p>Tak.</p>
                            <p>{ app_name }</p>',
                    'de' => '<p>Hallo, Willkommen bei {app_name}.</p>
                            <p>&nbsp;</p>
                            <p>E-Mail: {email}</p>
                            <p>Kennwort: {password}</p>
                            <p>{app_url}</p>
                            <p>&nbsp;</p>
                            <p>Danke,</p>
                            <p>{Anwendungsname}</p>',
                    'en' => '<p>Hello,&nbsp;<br>Welcome to {app_name}.</p><p><b>Email </b>: {email}<br><b>Password</b> : {password}</p><p>{app_url}</p><p>Thanks,<br>{app_name}</p>',
                    'es' => '<p>Hola, Bienvenido a {app_name}.</p>
                            <p>&nbsp;</p>
                            <p>Correo electr&oacute;nico: {email}</p>
                            <p>Contrase&ntilde;a: {password}</p>
                            <p>&nbsp;</p>
                            <p>{app_url}</p>
                            <p>&nbsp;</p>
                            <p>Gracias,</p>
                            <p>{app_name}</p>',
                    'fr' => '<p>Bonjour, Bienvenue dans { app_name }.</p>
                            <p>&nbsp;</p>
                            <p>E-mail: { email }</p>
                            <p>Mot de passe: { password }</p>
                            <p>{ adresse_url }</p>
                            <p>&nbsp;</p>
                            <p>Merci,</p>
                            <p>{ nom_app }</p>',
                    'it' => '<p>Ciao, Benvenuti in {app_name}.</p>
                            <p>&nbsp;</p>
                            <p>Email: {email} Password: {password}</p>
                            <p>&nbsp;</p>
                            <p>{app_url}</p>
                            <p>&nbsp;</p>
                            <p>Grazie,</p>
                            <p>{app_name}</p>',
                    'ja' => '<p>こんにちは、 {app_name}へようこそ。</p>
                            <p>&nbsp;</p>
                            <p>E メール : {email}</p>
                            <p>パスワード : {password}</p>
                            <p>{app_url}</p>
                            <p>&nbsp;</p>
                            <p>ありがとう。</p>
                            <p>{app_name}</p>',
                    'nl' => '<p>Hallo, Welkom bij { app_name }.</p>
                                <p>&nbsp;</p>
                                <p>E-mail: { email }</p>
                                <p>Wachtwoord: { password }</p>
                                <p>{ app_url }</p>
                                <p>&nbsp;</p>
                                <p>Bedankt.</p>
                                <p>{ app_name }</p>',
                    'pl' => '<p>Witaj, Witamy w aplikacji {app_name }.</p>
                            <p>&nbsp;</p>
                            <p>E-mail: {email }</p>
                            <p>Hasło: {password }</p>
                            <p>{app_url }</p>
                            <p>&nbsp;</p>
                            <p>Dziękuję,</p>
                            <p>{app_name }</p>',
                    'ru' => '<p>Здравствуйте, Добро пожаловать в { app_name }.</p>
                            <p>&nbsp;</p>
                            <p>Адрес электронной почты: { email }</p>
                            <p>Пароль: { password }</p>
                            <p>&nbsp;</p>
                            <p>{ app_url }</p>
                            <p>&nbsp;</p>
                            <p>Спасибо.</p>
                            <p>{ имя_программы }</p>',
                    'pt' => '<p>Ol&aacute;, Bem-vindo a {app_name}.</p>
                            <p>&nbsp;</p>
                            <p>E-mail: {email}</p>
                            <p>Senha: {senha}</p>
                            <p>{app_url}</p>
                            <p>&nbsp;</p>
                            <p>Obrigado,</p>
                            <p>{app_name}</p>
                            <p>{ имя_программы }</p>',

                ],
            ],
            'New Ticket' => [
                'subject' => 'Ticket Detail',
                'lang' => [
                    'ar' => '<p>مرحبا ، مرحبا بك في { app_name }.</p>
                            <p> </p>
                            <p>البريد الالكتروني : { mail }</p>
                            <p>كلمة السرية : { password }</p>
                            <p>{ app_url }</p>
                            <p> </p>
                            <p>شكرا</p>
                            <p>{ app_name }</p>',
                    'da' => '<p>Hej, velkommen til { app_name }.</p>
                            <p> </p>
                            <p>E-mail: { email }-</p>
                            <p>kodeord: { password }</p>
                            <p>{ app_url }</p>
                            <p> </p>
                            <p>Tak.</p>
                            <p>{ app_name }</p>',
                    'de' => '<p>Hallo, Willkommen bei {app_name}.</p>
                            <p> </p>
                            <p>E-Mail: {email}</p>
                            <p>Kennwort: {password}</p>
                            <p>{app_url}</p>
                            <p> </p>
                            <p>Danke,</p>
                            <p>{Anwendungsname}</p>',
                    'en' => '<p>Hello,&nbsp;<br>Welcome to {app_name}.</p><p>{ticket_name} </p><p>{ticket_id} </p><p><b>Email </b>: {email}<br><b>Password</b> : {password}</p><p>{app_url}</p><p>Thanks,<br>{app_name}</p>',
                    'es' => '<p>Hola, Bienvenido a {app_name}.</p>
                            <p> </p>
                            <p>Correo electrónico: {email}</p>
                            <p>Contraseña: {password}</p>
                            <p> </p>
                            <p>{app_url}</p>
                            <p> </p>
                            <p>Gracias,</p>
                            <p>{app_name}</p>',
                    'fr' => '<p>Bonjour, Bienvenue dans { app_name }.</p>
                            <p> </p>
                            <p>E-mail: { email }</p>
                            <p>Mot de passe: { password }</p>
                            <p>{ adresse_url }</p>
                            <p> </p>
                            <p>Merci,</p>
                            <p>{ nom_app }</p>',
                    'it' => '<p>Ciao, Benvenuti in {app_name}.</p>
                            <p> </p>
                            <p>Email: {email} Password: {password}</p>
                            <p> </p>
                            <p>{app_url}</p>
                            <p> </p>
                            <p>Grazie,</p>
                            <p>{app_name}</p>',
                    'ja' => '<p>こんにちは、 {app_name}へようこそ。</p>
                            <p> </p>
                            <p>E メール : {email}</p>
                            <p>パスワード : {password}</p>
                            <p>{app_url}</p>
                            <p> </p>
                            <p>ありがとう。</p>
                            <p>{app_name}</p>',
                    'nl' => '<p>Hallo, Welkom bij { app_name }.</p>
                            <p> </p>
                            <p>E-mail: { email }</p>
                            <p>Wachtwoord: { password }</p>
                            <p>{ app_url }</p>
                            <p> </p>
                            <p>Bedankt.</p>
                            <p>{ app_name }</p>',
                    'pl' => '<p>Witaj, Witamy w aplikacji {app_name }.</p>
                            <p> </p>
                            <p>E-mail: {email }</p>
                            <p>Hasło: {password }</p>
                            <p>{app_url }</p>
                            <p> </p>
                            <p>Dziękuję,</p>
                            <p>{app_name }</p>',
                    'ru' => '<p>Здравствуйте, Добро пожаловать в { app_name }.</p>
                            <p> </p>
                            <p>Адрес электронной почты: { email }</p>
                            <p>Пароль: { password }</p>
                            <p> </p>
                            <p>{ app_url }</p>
                            <p> </p>
                            <p>Спасибо.</p>
                            <p>{ имя_программы }</p>',
                    'pt' => '<p>Olá, Bem-vindo a {app_name}.</p>
                            <p> </p>
                            <p>E-mail: {email}</p>
                            <p>Senha: {senha}</p>
                            <p>{app_url}</p>
                            <p> </p>
                            <p>Obrigado,</p>
                            <p>{app_name}</p>
                            <p>{ имя_программы }</p>',

                ],
            ],
            'New Ticket Reply' => [
                'subject' => 'Ticket Detail',
                'lang' => [
                    'ar' => '<p>مرحبا ، مرحبا بك في { app_name }.</p>
                            <p>&nbsp;</p>
                            <p>{ ticket_name }</p>
                            <p>{ ticket_id }</p>
                            <p>&nbsp;</p>
                            <p>الوصف : { ticket_description }</p>
                            <p>&nbsp;</p>
                            <p>شكرا</p>
                            <p>{ app_name }</p>',
                    'da' => '<p>Hej, velkommen til { app_name }.</p>
                            <p>&nbsp;</p>
                            <p>{ ticket_name }</p>
                            <p>{ ticket_id }</p>
                            <p>&nbsp;</p>
                            <p>Beskrivelse: { ticket_description }</p>
                            <p>&nbsp;</p>
                            <p>Tak.</p>
                            <p>{ app_name }</p>',
                    'de' => '<p>Hallo, Willkommen bei {app_name}.</p>
                            <p>&nbsp;</p>
                            <p>{ticketname}</p>
                            <p>{ticket_id}</p>
                            <p>&nbsp;</p>
                            <p>Beschreibung: {ticket_description}</p>
                            <p>&nbsp;</p>
                            <p>Danke,</p>
                            <p>{Anwendungsname}</p>',
                    'en' => '<p>Hello,&nbsp;<br />Welcome to {app_name}.</p>
                            <p>{ticket_name}</p>
                            <p>{ticket_id}</p>
                            <p><strong>Description</strong> : {ticket_description}</p>
                            <p>Thanks,<br />{app_name}</p>',
                    'es' => '<p>Hola, Bienvenido a {app_name}.</p>
                            <p>&nbsp;</p>
                            <p>{ticket_name}</p>
                            <p>{ticket_id}</p>
                            <p>&nbsp;</p>
                            <p>Descripci&oacute;n: {ticket_description}</p>
                            <p>&nbsp;</p>
                            <p>Gracias,</p>
                            <p>{app_name}</p>',
                    'fr' => '<p>Hola, Bienvenido a {app_name}.</p>
                            <p>&nbsp;</p>
                            <p>{ticket_name}</p>
                            <p>{ticket_id}</p>
                            <p>&nbsp;</p>
                            <p>Descripci&oacute;n: {ticket_description}</p>
                            <p>&nbsp;</p>
                            <p>Gracias,</p>
                            <p>{app_name}</p>',
                    'it' => '<p>Ciao, Benvenuti in {app_name}.</p>
                            <p>&nbsp;</p>
                            <p>{ticket_name}</p>
                            <p>{ticket_id}</p>
                            <p>&nbsp;</p>
                            <p>Descrizione: {ticket_description}</p>
                            <p>&nbsp;</p>
                            <p>Grazie,</p>
                            <p>{app_name}</p>',
                    'ja' => '<p>こんにちは、 {app_name}へようこそ。</p>
                            <p>&nbsp;</p>
                            <p>{ticket_name}</p>
                            <p>{ticket_id}</p>
                            <p>&nbsp;</p>
                            <p>説明 : {ticket_description}</p>
                            <p>&nbsp;</p>
                            <p>ありがとう。</p>
                            <p>{app_name}</p>',
                    'nl' => '<p>Hallo, Welkom bij { app_name }.</p>
                            <p>&nbsp;</p>
                            <p>{ ticket_name }</p>
                            <p>{ ticket_id }</p>
                            <p>&nbsp;</p>
                            <p>Beschrijving: { ticket_description }</p>
                            <p>&nbsp;</p>
                            <p>Bedankt.</p>
                            <p>{ app_name }</p>',
                    'pl' => '<p>Witaj, Witamy w aplikacji {app_name }.</p>
                            <p>&nbsp;</p>
                            <p>{ticket_name }</p>
                            <p>{ticket_id }</p>
                            <p>&nbsp;</p>
                            <p>Opis: {ticket_description }</p>
                            <p>&nbsp;</p>
                            <p>Dziękuję,</p>
                            <p>{app_name }</p>',
                    'ru' => '<p>Здравствуйте, Добро пожаловать в { app_name }.</p>
                            <p>&nbsp;</p>
                            <p>Witaj, Witamy w aplikacji {app_name }.</p>
                            <p>&nbsp;</p>
                            <p>{ticket_name }</p>
                            <p>{ticket_id }</p>
                            <p>&nbsp;</p>
                            <p>Opis: {ticket_description }</p>
                            <p>&nbsp;</p>
                            <p>Dziękuję,</p>
                            <p>{app_name }</p>',
                    'pt' => '<p>Ol&aacute;, Bem-vindo a {app_name}.</p>
                            <p>&nbsp;</p>
                            <p>{ticket_name}</p>
                            <p>{ticket_id}</p>
                            <p>&nbsp;</p>
                            <p>Descri&ccedil;&atilde;o: {ticket_description}</p>
                            <p>&nbsp;</p>
                            <p>Obrigado,</p>
                            <p>{app_name}</p>',

                ],
            ],

        ];

        $email = EmailTemplate::all();

        foreach ($email as $e) {
            foreach ($defaultTemplate[$e->name]['lang'] as $lang => $content) {
                EmailTemplateLang::create(
                    [
                        'parent_id' => $e->id,
                        'lang' => $lang,
                        'subject' => $defaultTemplate[$e->name]['subject'],
                        'content' => $content,
                    ]
                );
            }
        }
    }

    public static function userDefaultData()
    {
        // Make Entry In User_Email_Template
        $allEmail = EmailTemplate::all();

        foreach ($allEmail as $email) {
            UserEmailTemplate::create(
                [
                    'template_id' => $email->id,
                    'user_id' => 1,
                    'is_active' => 1,
                ]
            );
        }
    }

    public static function sendEmailTemplate($emailTemplate, $mailTo, $obj)
    {
        $usr = \Auth::user();

        //Remove Current Login user Email don't send mail to them
        unset($mailTo[$usr->id]);

        $mailTo = array_values($mailTo);

        if($usr->type != 'Super Admin')
        {
            // find template is exist or not in our record
            $template = EmailTemplate::where('slug', $emailTemplate)->first();

            if(isset($template) && !empty($template))
            {
                // check template is active or not by company
                $is_active = UserEmailTemplate::where('template_id', '=', $template->id)->where('user_id', '=', $usr->id)->first();

                if($is_active->is_active == 1)
                {
                    $settings = self::settings();
                    // get email content language base
                    $content = EmailTemplateLang::where('parent_id', '=', $template->id)->where('lang', 'LIKE', $usr->lang)->first();

                    $content->from = $template->from;

                    if(!empty($content->content))
                    {

                        $content->content = self::replaceVariable($content->content, $obj);


                        // send email
                        try
                        {
                            // dd($mailTo,$content,$settings);
                            Mail::to($mailTo)->send(new CommonEmailTemplate($content,$settings));

                        }
                        catch(\Exception $e)
                        {
                            $error = __('E-Mail has been not sent due to SMTP configuration');
                        }

                        if(isset($error))
                        {
                            $arReturn = [
                                'is_success' => false,
                                'error' => $error,
                            ];
                        }
                        else
                        {
                            $arReturn = [
                                'is_success' => true,
                                'error' => false,
                            ];
                        }
                    }
                    else
                    {
                        $arReturn = [
                            'is_success' => false,
                            'error' => __('Mail not send, email is empty'),
                        ];
                    }

                    return $arReturn;
                }
                else
                {
                    return [
                        'is_success' => true,
                        'error' => false,
                    ];
                }
            }
            else
            {
                return [
                    'is_success' => false,
                    'error' => __('Mail not send, email not found'),
                ];
            }
        }
    }

    public static function replaceVariable($content, $obj)
    {
        $arrVariable = [
            '{app_name}' ,
            '{company_name}',
            '{ticket_name}' ,
            '{ticket_id}' ,
            '{ticket_description}',
            '{app_url}' ,
            '{email}' ,
            '{password}' ,
        ];

        $arrValue    = [
            'app_name' => '-',
            'company_name' => '-',
            'ticket_name' => '-',
            'ticket_id' => '-',
            'ticket_description' => '-',
            'app_url' => '-',
            'email' => '-',
            'password' => '-',
        ];

        foreach($obj as $key => $val)
        {
            $arrValue[$key] = $val;
        }

        $settings = Utility::settings();
        $company_name = $settings['company_name'];

        $arrValue['app_name']     =  $company_name;
        $arrValue['company_name'] = self::settings()['company_name'];
        $arrValue['app_url']      = '<a href="' . env('APP_URL') . '" target="_blank">' . env('APP_URL') . '</a>';

        return str_replace($arrVariable, array_values($arrValue), $content);
    }

    public static function languages()
    {
        $dir     = base_path() . '/resources/lang/';
        $glob    = glob($dir . "*", GLOB_ONLYDIR);
        $arrLang = array_map(
            function ($value) use ($dir){
                return str_replace($dir, '', $value);
            }, $glob
        );
        $arrLang = array_map(
            function ($value) use ($dir){
                return preg_replace('/[0-9]+/', '', $value);
            }, $arrLang
        );
        $arrLang = array_filter($arrLang);

        return $arrLang;
    }

    public static function updateUserDefaultEmailTempData()
    {
        $UserEmailTemp = UserEmailTemplate::groupBy('user_id')->pluck('user_id');
        $allUser = User::where('name','Admin')->whereNotIn('id',$UserEmailTemp)->get();

        foreach ($allUser as $user) {

            $allEmail = EmailTemplate::all();

            foreach ($allEmail as $email) {
                UserEmailTemplate::create(
                    [
                        'template_id' => $email->id,
                        'user_id' => $user->id,
                        'is_active' => 1,
                    ]
                );
            }
        }
    }

    public static function upload_file($request,$key_name,$name,$path,$custom_validation =[]){
        try{
            $settings = Utility::settings();
            if(!empty($settings['storage_setting'])){

                if($settings['storage_setting'] == 'wasabi'){

                    config(
                        [
                            'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                            'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                            'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                            'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                            'filesystems.disks.wasabi.endpoint' => 'https://s3.'.$settings['wasabi_region'].'.wasabisys.com'
                        ]
                    );

                    $max_size = !empty($settings['wasabi_max_upload_size'])? $settings['wasabi_max_upload_size']:'2048';
                    $mimes =  !empty($settings['wasabi_storage_validation'])? $settings['wasabi_storage_validation']:'';

                }else if($settings['storage_setting'] == 's3'){
                    config(
                        [
                            'filesystems.disks.s3.key' => $settings['s3_key'],
                            'filesystems.disks.s3.secret' => $settings['s3_secret'],
                            'filesystems.disks.s3.region' => $settings['s3_region'],
                            'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                            'filesystems.disks.s3.use_path_style_endpoint' => false,
                        ]
                    );
                    $max_size = !empty($settings['s3_max_upload_size'])? $settings['s3_max_upload_size']:'2048';
                    $mimes =  !empty($settings['s3_storage_validation'])? $settings['s3_storage_validation']:'';


                }else{
                    $max_size = !empty($settings['local_storage_max_upload_size'])? $settings['local_storage_max_upload_size']:'2048';

                    $mimes =  !empty($settings['local_storage_validation'])? $settings['local_storage_validation']:'';
                }


                $file = $request->$key_name;


                if(count($custom_validation) > 0){
                    $validation =$custom_validation;
                }else{

                    $validation =[
                        'mimes:'.$mimes,
                        'max:'.$max_size,
                    ];

                }
                $validator = \Validator::make($request->all(), [
                    $key_name =>$validation
                ]);

                if($validator->fails()){
                    $res = [
                        'flag' => 0,
                        'msg' => $validator->messages()->first(),
                    ];
                    return $res;
                } else {

                    $name = $name;

                    if($settings['storage_setting']=='local')
                    {
                        $request->$key_name->move(storage_path($path), $name);
                        $path = $path.$name;

                    }else if($settings['storage_setting'] == 'wasabi'){

                        $path = \Storage::disk('wasabi')->putFileAs(
                            $path,
                            $file,
                            $name
                        );


                        // $path = $path.$name;

                    }else if($settings['storage_setting'] == 's3'){

                        $path = \Storage::disk('s3')->putFileAs(
                            $path,
                            $file,
                            $name
                        );
                        // $path = $path.$name;
                        // dd($path);
                    }


                    $res = [
                        'flag' => 1,
                        'msg'  =>'success',
                        'url'  => $path
                    ];
                    return $res;
                }

            }else{
                $res = [
                    'flag' => 0,
                    'msg' => __('Please set proper configuration for storage.'),
                ];
                return $res;
            }

        }catch(\Exception $e){
            $res = [
                'flag' => 0,
                'msg' => $e->getMessage(),
            ];
            return $res;
        }
    }

    public static function get_file($path){
        $settings = Utility::settings();

        try {
            if($settings['storage_setting'] == 'wasabi'){

                config(
                    [
                        'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                        'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                        'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                        'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                        'filesystems.disks.wasabi.endpoint' => 'https://s3.'.$settings['wasabi_region'].'.wasabisys.com'
                    ]
                );

            }elseif($settings['storage_setting'] == 's3'){
                config(
                    [
                        'filesystems.disks.s3.key' => $settings['s3_key'],
                        'filesystems.disks.s3.secret' => $settings['s3_secret'],
                        'filesystems.disks.s3.region' => $settings['s3_region'],
                        'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                        'filesystems.disks.s3.use_path_style_endpoint' => false,
                    ]
                );
            }

            return \Storage::disk($settings['storage_setting'])->url($path);
        } catch (\Throwable $th) {
            return '';
        }
    }

    public static function getStorageSetting()
    {

        $data = DB::table('settings');
        $data = $data->where('created_by', '=', 1);
        $data     = $data->get();
        $settings = [
            "storage_setting" => "",
            "local_storage_validation" => "",
            "local_storage_max_upload_size" => "",
            "s3_key" => "",
            "s3_secret" => "",
            "s3_region" => "",
            "s3_bucket" => "",
            "s3_url"    => "",
            "s3_endpoint" => "",
            "s3_max_upload_size" => "",
            "s3_storage_validation" => "",
            "wasabi_key" => "",
            "wasabi_secret" => "",
            "wasabi_region" => "",
            "wasabi_bucket" => "",
            "wasabi_url" => "",
            "wasabi_root" => "",
            "wasabi_max_upload_size" => "",
            "wasabi_storage_validation" => "",
        ];

        foreach($data as $row)
        {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }

    public static function multipalFileUpload($request,$key_name,$name,$path,$data_key,$custom_validation =[])
    {
        $multifile = [
            $key_name => $request->file($key_name)[$data_key],
        ];


        try{
            $settings = Utility::settings();

            if(!empty($settings['storage_setting'])){

                if($settings['storage_setting'] == 'wasabi'){

                    config(
                        [
                            'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                            'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                            'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                            'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                            'filesystems.disks.wasabi.endpoint' => 'https://s3.'.$settings['wasabi_region'].'.wasabisys.com'
                        ]
                    );

                    $max_size = !empty($settings['wasabi_max_upload_size'])? $settings['wasabi_max_upload_size']:'2048';
                    $mimes =  !empty($settings['wasabi_storage_validation'])? $settings['wasabi_storage_validation']:'';

                }else if($settings['storage_setting'] == 's3'){
                    config(
                        [
                            'filesystems.disks.s3.key' => $settings['s3_key'],
                            'filesystems.disks.s3.secret' => $settings['s3_secret'],
                            'filesystems.disks.s3.region' => $settings['s3_region'],
                            'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                            'filesystems.disks.s3.use_path_style_endpoint' => false,
                        ]
                    );
                    $max_size = !empty($settings['s3_max_upload_size'])? $settings['s3_max_upload_size']:'2048';
                    $mimes =  !empty($settings['s3_storage_validation'])? $settings['s3_storage_validation']:'';


                }else{
                    $max_size = !empty($settings['local_storage_max_upload_size'])? $settings['local_storage_max_upload_size']:'2048';

                    $mimes =  !empty($settings['local_storage_validation'])? $settings['local_storage_validation']:'';
                }


                $file = $request->$key_name;


                if(count($custom_validation) > 0){
                    $validation =$custom_validation;
                }else{

                    $validation =[
                        'mimes:'.$mimes,
                        'max:'.$max_size,
                    ];

                }
                $validator = \Validator::make($multifile, [
                    $key_name =>$validation
                ]);

                if($validator->fails()){
                    $res = [
                        'flag' => 0,
                        'msg' => $validator->messages()->first(),
                    ];
                    return $res;
                } else {
                    $name = $name;
                    if($settings['storage_setting']=='local'){
                        \Storage::disk()->putFileAs(
                            $path,
                            $request->file($key_name)[$data_key],
                            $name
                        );

                        $path = $name;
                    }else if($settings['storage_setting'] == 'wasabi'){

                        $path = \Storage::disk('wasabi')->putFileAs(
                            $path,
                            $file,
                            $name
                        );

                        // $path = $path.$name;

                    }else if($settings['storage_setting'] == 's3'){

                        $path = \Storage::disk('s3')->putFileAs(
                            $path,
                            $file,
                            $name
                        );
                    }

                    $res = [
                        'flag' => 1,
                        'msg'  =>'success',
                        'url'  => $path
                    ];
                    return $res;
                }

            }else{
                $res = [
                    'flag' => 0,
                    'msg' => __('Please set proper configuration for storage.'),
                ];
                return $res;
            }

        }catch(\Exception $e){
            $res = [
                'flag' => 0,
                'msg' => $e->getMessage(),
            ];
            return $res;
        }
    }

}
