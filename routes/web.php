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

/*
Route::get('/', function () {
    return view('welcome');
})->name('home');
*/

Route::get('/', 'HomeController@index')->name('home');

/*
-------------------------------------------------
/  For Subscriber, everyone access it
-------------------------------------------------
*/
Route::post('Subscriber', 'SubscriberController@store')->name('subscriber.store');


Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');

/*
-------------------------------------------------
/  Admin Related Route
-------------------------------------------------
*/
Route::group(['as'=>'admin.','prefix'=>'admin','namespace'=>'Admin','middleware'=>['auth','admin']], function(){

  Route::get('dashboard','DashboardController@index')->name('dashboard');

  // admin settings profile
  Route::get('settings', 'SettingsController@index')->name('settings');
  Route::put('profile-update', 'SettingsController@updateProfile')->name('profile.update');
  Route::put('password-update', 'SettingsController@updatePassword')->name('password.update');


  Route::resource('tag','TagController');
  Route::resource('category','CategoryController');
  Route::resource('post','PostController');

  Route::get('/pending/post','PostController@pending')->name('post.pending');
  // status update kortesi, thats why put
  Route::put('/post/{id}/approve', 'PostController@approval')->name('post.approve');

  // Subscriber
  Route::get('/subscriber', 'SubscriberController@index')->name('subscriber.index');
  Route::delete('/subscriber/{subscriber}', 'SubscriberController@destroy')->name('subscriber.destroy');



});

/*
-------------------------------------------------
/  Author Related Route
-------------------------------------------------
*/
Route::group(['as'=>'author.','prefix'=>'author','namespace'=>'Author','middleware'=>['auth','author']], function(){

  Route::get('dashboard','DashboardController@index')->name('dashboard');
  Route::resource('post','PostController');

});
