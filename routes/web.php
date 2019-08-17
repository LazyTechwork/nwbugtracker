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

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => false, 'reset' => false]);

Route::middleware('sessioned')->group(function () {
    Route::get('home', 'HomeController@index')->name('home');
    Route::prefix('product')->name('products.')->group(function () {
        Route::get('list', 'HomeController@products')->name('index');
        Route::get('show/{id}', 'HomeController@showProduct')->name('show');
        Route::middleware('glmod')->group(function () {
            Route::get('modlist/{id}', 'HomeController@showModerators')->name('modlist');
            Route::post('addmod/{id}', 'HomeController@addModerator')->name('addmod');
            Route::get('delmod/{id}/{modid}', 'HomeController@delModerator')->name('delmod');
            Route::get('new', 'HomeController@newProductV')->name('newprodV');
            Route::get('edit/{id}', 'HomeController@editProductV')->name('editprodV');
            Route::post('new', 'HomeController@newProduct')->name('newprod');
            Route::post('edit/{id}', 'HomeController@editProduct')->name('editprod');
        });
    });
    Route::prefix('tester')->name('testers.')->group(function () {
        Route::get('list', 'HomeController@testers')->name('index');
        Route::get('show/{id}', 'HomeController@showTester')->name('show');
    });
});
Route::get('logout', 'Auth\LoginController@logout')->name('logout');
Route::get('authentificate', 'Auth\LoginController@authvk')->name('authvk');
