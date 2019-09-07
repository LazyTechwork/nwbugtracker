<?php

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

use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false, 'reset' => false]);

Route::get('/', 'Auth\LoginController@welcome');
Route::get('terms', 'HomeController@terms')->name('terms');

Route::middleware('sessioned')->group(function () {
    Route::get('home', 'HomeController@index')->name('home');
    Route::get('settings', 'HomeController@settings')->name('settings');
    Route::middleware('glmod')->group(function () {
        Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
        Route::get('apanel', 'HomeController@apanel')->name('apanel');
        Route::get('letpoints', 'HomeController@letPoints')->name('letpts');
    });

    Route::middleware('ismod')->prefix('mpanel')->name('mpanel.')->group(function () {
        Route::get('panel', 'HomeController@modpanel')->name('mpanel');
    });
    Route::prefix('product')->name('products.')->group(function () {
        Route::get('list', 'HomeController@products')->name('index');
        Route::get('show/{id}', 'HomeController@showProduct')->name('show');
        Route::get('bugs/{id}', 'HomeController@productBugs')->name('bugs');
        Route::middleware('glmod')->group(function () {
            Route::get('modlist/{id}', 'HomeController@showModerators')->name('modlist');
            Route::post('addmod/{id}', 'HomeController@addModerator')->name('addmod');
            Route::get('delmod/{id}/{modid}', 'HomeController@delModerator')->name('delmod');
            Route::get('new', 'HomeController@newProductV')->name('newprodV');
            Route::get('edit/{id}', 'HomeController@editProductV')->name('editprodV');
            Route::post('new', 'HomeController@newProduct')->name('newprod');
            Route::post('edit/{id}', 'HomeController@editProduct')->name('editprod');
            Route::get('block/{id}', 'HomeController@blockProduct')->name('blockprod');
        });
        Route::prefix('update')->group(function () {
            Route::get('new/{id}', 'HomeController@newUpdateV')->name('newupdV');
            Route::post('new/{id}', 'HomeController@newUpdate')->name('newupd');
            Route::get('delete/{id}/{updateid}', 'HomeController@delUpdate')->name('delupd');
        });
    });
    Route::prefix('tester')->name('testers.')->group(function () {
        Route::get('list', 'HomeController@testers')->name('index');
        Route::get('show/{id}', 'HomeController@showTester')->name('show');
        Route::get('bugs/{id}', 'HomeController@testerBugs')->name('bugs');
    });

    Route::prefix('bug')->name('bugs.')->group(function () {
        Route::get('list', 'HomeController@bugs')->name('index');
        Route::get('show/{id}', 'HomeController@showBug')->name('show');
        Route::get('new/{productid}', 'HomeController@newBugV')->name('newbugV');
        Route::post('new/{productid}', 'HomeController@newBug')->name('newbug');
        Route::get('edit/{id}', 'HomeController@editBugV')->name('editbugV');
        Route::post('edit/{id}', 'HomeController@editBug')->name('editbug');
        Route::post('statusupdate/{id}', 'HomeController@updateBug')->name('updateStatus');
        Route::get('actualitychange/{id}/{actual}', 'HomeController@actualize')->name('actualityChange');
        Route::get('my', 'HomeController@myBugs')->name('my');
        Route::get('delete/{id}', 'HomeController@deleteBug')->name('delbug');
    });

    Route::prefix('shop')->name('shop.')->group(function () {
        Route::get('shop', 'HomeController@shopIndex')->name('index');
    });
});
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('authentificate', 'Auth\LoginController@authvk')->name('authvk');
