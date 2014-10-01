<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', array(
    'uses' => 'HomeController@home'
));

Route::get('oauth2callback', array(
    'uses' => 'HomeController@oAuth'
));

Route::get('info', array(
    'uses' => 'HomeController@info'
));
