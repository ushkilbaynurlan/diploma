<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FaqKnwlController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ConversionController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\KnowledgebaseCategoryController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\EmailTemplateLangController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


require __DIR__.'/auth.php';


Route::get('/', [HomeController::class, 'index']);

Route::controller(HomeController::class)->group(function(){

    Route::get('home', 'index')->name('home');
    Route::post('home', 'store')->name('home.store');
    Route::get('search', 'search')->name('search');
    Route::post('search', 'ticketSearch')->name('ticket.search');
    Route::get('tickets/{id}', 'view')->name('home.view');
    Route::post('ticket/{id}', 'reply')->name('home.reply');
    Route::get('faq', 'faq')->name('faq');
    Route::get('knowledge', 'knowledge')->name('knowledge');
    Route::get('knowledgedesc', 'knowledgeDescription')->name('knowledgedesc');

});


Route::name('admin.')->prefix('admin')->middleware(['auth','XSS'])->group(function() {

    // Route::get('dashboard', 'DashboardController')->name('dashboard');
    Route::get('dashboard', [DashboardController::class,'index'])->name('dashboard');

    // Route::resource('users', 'UserController', ['names' => ['index' => 'users' ]]);
    Route::resource('users', UserController::class, ['names' => ['index' => 'users']]);

    Route::get('lang/clear', [LanguageController::class, 'clear'])->name('lang.clear');
    Route::get('lang/create', [LanguageController::class, 'create'])->name('lang.create');
    Route::post('lang/create', [LanguageController::class, 'store'])->name('lang.store');
    Route::get('lang/{lang}', [LanguageController::class, 'index'])->name('lang.index');
    Route::post('lang/{lang}', [LanguageController::class, 'storeData'])->name('lang.store.data');
    Route::get('lang/change/{lang}', [LanguageController::class, 'update'])->name('lang.update');
    Route::delete('lang/{lang}', [LanguageController::class, 'destroyLang'])->name('lang.destroy');


    Route::get('category/create', [CategoryController::class, 'create'])->name('category.create');
    Route::post('category', [CategoryController::class, 'store'])->name('category.store');
    Route::get('category', [CategoryController::class, 'index'])->name('category');
    Route::get('category/{id}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::delete('category/{id}/destroy', [CategoryController::class, 'destroy'])->name('category.destroy');
    Route::put('category/{id}/update', [CategoryController::class, 'update'])->name('category.update');

    // Message Route

    Route::get('chat', [MessageController::class, 'index'])->name('chats');
    Route::get('message/{id}', [MessageController::class, 'getMessage'])->name('message');
    Route::delete('delete-user-message/{id}', [MessageController::class, 'deleteUserMessage'])->name('delete.user.message');
    Route::post('message', [MessageController::class, 'sendMessage']);

    // End Message Route

    Route::post('/custom-fields', [SettingsController::class, 'storeCustomFields'])->name('custom-fields.store');

});

Route::any('users-reset-password/{id}', [UserController::class, 'userPassword'])->name('user.reset');
Route::post('users-reset-password/{id}', [UserController::class, 'userPasswordReset'])->name('user.password.update');


Route::name('admin.')->prefix('admin')->middleware(['auth','XSS'])->group(function() {

    Route::get('ticket/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('ticket', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('ticket/{status?}', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('ticket/{id}/edit', [TicketController::class, 'editTicket'])->name('tickets.edit');
    Route::delete('ticket/{id}/destroy', [TicketController::class, 'destroy'])->name('tickets.destroy');
    Route::delete('ticket-attachment/{tid}/destroy/{id}', [TicketController::class, 'attachmentDestroy'])->name('tickets.attachment.destroy');
    Route::put('ticket/{id}/update', [TicketController::class, 'updateTicket'])->name('tickets.update');

    Route::get('faq/create', [FaqController::class, 'create'])->name('faq.create');
    Route::post('faq', [FaqController::class, 'store'])->name('faq.store');
    Route::get('faq', [FaqController::class, 'index'])->name('faq');
    Route::get('faq/{id}/edit', [FaqController::class, 'edit'])->name('faq.edit');
    Route::delete('faq/{id}/destroy', [FaqController::class, 'destroy'])->name('faq.destroy');
    Route::put('faq/{id}/update', [FaqController::class, 'update'])->name('faq.update');

    Route::post('ticket/{id}/conversion', [ConversionController::class, 'store'])->name('conversion.store');

    Route::post('ticket/{id}/note', [TicketController::class, 'storeNote'])->name('note.store');

    Route::get('knowledge', [KnowledgeController::class, 'index'])->name('knowledge');
    Route::get('knowledge/create', [KnowledgeController::class, 'create'])->name('knowledge.create');
    Route::post('knowledge', [KnowledgeController::class, 'store'])->name('knowledge.store');
    Route::get('knowledge/{id}/edit', [KnowledgeController::class, 'edit'])->name('knowledge.edit');
    Route::delete('knowledge/{id}/destroy', [KnowledgeController::class, 'destroy'])->name('knowledge.destroy');
    Route::put('knowledge/{id}/update', [KnowledgeController::class, 'update'])->name('knowledge.update');

    Route::get('knowledgecategory', [KnowledgebaseCategoryController::class, 'index'])->name('knowledgecategory');
    Route::get('knowledgecategory/create', [KnowledgebaseCategoryController::class, 'create'])->name('knowledgecategory.create');
    Route::post('knowledgecategory', [KnowledgebaseCategoryController::class, 'store'])->name('knowledgecategory.store');
    Route::get('knowledgecategory/{id}/edit', [KnowledgebaseCategoryController::class, 'edit'])->name('knowledgecategory.edit');
    Route::delete('knowledgecategory/{id}/destroy', [KnowledgebaseCategoryController::class, 'destroy'])->name('knowledgecategory.destroy');
    Route::put('knowledgecategory/{id}/update', [KnowledgebaseCategoryController::class, 'update'])->name('knowledgecategory.update');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store');
    Route::post('/email-settings', [SettingsController::class, 'emailSettingStore'])->name('email.settings.store');
    Route::post('/payment-settings', [SettingsController::class, 'paymentSettingStore'])->name('payment.settings.store');
    Route::post('/pusher-settings', [SettingsController::class, 'pusherSettingStore'])->name('pusher.settings.store');
    Route::post('/recaptcha-settings', [SettingsController::class, 'recaptchaSettingStore'])->name('recaptcha.settings.store');
    Route::post('/test', [SettingsController::class, 'testEmail'])->name('test.email');
    Route::post('/test/send', [SettingsController::class, 'testEmailSend'])->name('test.email.send');


});

Route::post('storage-settings', [SettingsController::class, 'storageSettingStore'])->name('storage.setting.store')->middleware(['auth','XSS']);
Route::post('company-settings', [SettingsController::class, 'saveCompanySettings'])->name('company.settings');
Route::get('get_message', [MessageController::class, 'getFloatingMessage'])->name('get_message')->middleware(['XSS']);
Route::post('message_form', [MessageController::class, 'store'])->name('chat_form.store')->middleware(['XSS']);
Route::post('floating_message', [MessageController::class, 'sendFloatingMessage'])->name('floating_message')->middleware(['XSS']);


//Export
Route::get('export/tickets', [TicketController::class, 'export'])->name('tickets.export');

// Email Templates

Route::get('email_template_lang/{id}/{lang?}', [EmailTemplateController::class, 'manageEmailLang'])->name('manage.email.language')->middleware(['auth','XSS']);
Route::post('email_template_store/{pid}', [EmailTemplateController::class, 'storeEmailLang'])->name('store.email.language')->middleware(['auth']);
Route::post('email_template_status/{pid}', [EmailTemplateController::class, 'updateStatus'])->name('status.email.language')->middleware(['auth']);

Route::resource('email_template', EmailTemplateController::class)->middleware(['auth','XSS','revalidate']);

Route::resource('email_template_lang', EmailTemplateLangController::class)->middleware(['auth','XSS','revalidate']);
