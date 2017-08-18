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

Route::get('/', function () {
    return view('dashboard');
})->middleware('auth');

Auth::routes();

Route::resource('employee-management', 'EmployeeManagementController');
Route::post('employee-management/search', 'EmployeeManagementController@search')->name('employee-management.search');

Route::resource('user-management', 'UserManagementController');
Route::post('user-management/search', [
    'as' => 'user-management.search',
    'uses' => 'UserManagementController@search'
]);

Route::get('avatars/{name}', 'EmployeeManagementController@load');

Route::get('/home', 'HomeController@index');
Route::get('sendEmail', 'HomeController@email')->name('sendEmail');
