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

Route::get('/home', function(){
    return redirect('/dashboard');
});

//website routes
Route::get('/request/{string}', 'WebsiteController@request_page');
Route::post('/request_search', 'WebsiteController@request_search');



Route::get('logouts', function () {
    Auth::logout();
    return redirect('/login');
});
Auth::routes();

Route::group(['middleware' => ['auth']], function() {
    Route::get('/dashboardpanel', 'WebsiteController@panel');
//    Route::get('/search_result', 'WebsiteController@index_page');

    Route::get('/dashboard', 'AdminpanelController@dashboard');
    Route::get('/properties', 'AdminpanelController@properties');
    Route::get('/properties_detail/{id}', 'AdminpanelController@properties_detail');
    Route::get('/properties_delete/{id}', 'AdminpanelController@properties_delete');
    Route::get('/property_image_delete/{id}/{string}', 'AdminpanelController@property_image_delete');
    Route::get('/property_update_get/{id}', 'AdminpanelController@property_update_get');
    Route::post('/property_update_store/{id}', 'AdminpanelController@property_update_store');
    Route::get('/properties_status/{id}/{string}', 'AdminpanelController@properties_status');
    Route::get('/add_property', 'AdminpanelController@add_property');
    Route::post('/add_property_store', 'AdminpanelController@add_property_store');

    Route::get('/customer_history', 'AdminpanelController@customer_history');
    Route::get('/customer_history_detail/{id}', 'AdminpanelController@customer_history_detail');
});

