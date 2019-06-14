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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

route::get('/messages', 'MessageController@index');
route::get('/allmessages', 'MessageController@all');
route::post('/create', 'MessageController@create');
route::get('/conversation/{id}', 'MessageController@conversation');
route::post('/update/{id}', 'MessageController@updates');
route::get('/allarchived', 'MessageController@archived');
route::get('/alldelete', 'MessageController@delete');
route::get('/inbox/{id}', 'MessageController@inboxStatus');
route::get('/archived/{id}', 'MessageController@ArchivedStatus');
route::get('/delete/{id}', 'MessageController@DeleteStatus');
route::get('/soft_delete/{id}', 'MessageController@softDelete');



route::get('/inbox', 'InboxController@index');
route::post('/addmessage', 'InboxController@message');
route::get('/conver','InboxController@conversation');
route::get('/talking/{id}','InboxController@talk');