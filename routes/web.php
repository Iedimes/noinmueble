<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('brackets/admin-auth::admin.auth.login');
});

// Route::get('/', 'App\Http\Controllers\Admin\HomeController@dashboard');

Route::get('/verificacion/{cedula}', 'App\Http\Controllers\Admin\BeneficiarioController@verificacion');


/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::prefix('admin-users')->name('admin-users/')->group(static function() {
            Route::get('/',                                             'AdminUsersController@index')->name('index');
            Route::get('/create',                                       'AdminUsersController@create')->name('create');
            Route::post('/',                                            'AdminUsersController@store')->name('store');
            Route::get('/{adminUser}/impersonal-login',                 'AdminUsersController@impersonalLogin')->name('impersonal-login');
            Route::get('/{adminUser}/edit',                             'AdminUsersController@edit')->name('edit');
            Route::post('/{adminUser}',                                 'AdminUsersController@update')->name('update');
            Route::delete('/{adminUser}',                               'AdminUsersController@destroy')->name('destroy');
            Route::get('/{adminUser}/resend-activation',                'AdminUsersController@resendActivationEmail')->name('resendActivationEmail');
        });
    });
});

/* Auto-generated admin routes */
Route::middleware(['auth:' . config('admin-auth.defaults.guard'), 'admin'])->group(static function () {
    Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
        Route::get('/profile',                                      'ProfileController@editProfile')->name('edit-profile');
        Route::post('/profile',                                     'ProfileController@updateProfile')->name('update-profile');
        Route::get('/password',                                     'ProfileController@editPassword')->name('edit-password');
        Route::post('/password',                                    'ProfileController@updatePassword')->name('update-password');
    });
});

Route::prefix('admin')->namespace('App\Http\Controllers\Admin')->name('admin/')->group(static function() {
    Route::prefix('beneficiarios')->name('beneficiarios/')->group(static function() {
        Route::get('/', 'BeneficiarioController@show')->name('show');
        Route::get('/{PerCod}', 'BeneficiarioController@index')->name('index');
        Route::get('/create', 'BeneficiarioController@create')->name('create');
        Route::post('/', 'BeneficiarioController@store')->name('store');
        Route::get('/{beneficiario}/edit', 'BeneficiarioController@edit')->name('edit');
        Route::post('/bulk-destroy', 'BeneficiarioController@bulkDestroy')->name('bulk-destroy');
        Route::post('/{beneficiario}', 'BeneficiarioController@update')->name('update');
        Route::delete('/{beneficiario}', 'BeneficiarioController@destroy')->name('destroy');
        Route::get('{PerCod}/constanciapdf', 'BeneficiarioController@createPDF');
    });
});
