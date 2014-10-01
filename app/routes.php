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

Route::group(array(
    'domain' => '{subdomain}.ba-server.com',
    'before' => 'isFunnel'
) , function ($subdomain) {

    Route::get('/', array(
        'as' => 'home',
        'uses' => 'HomeController@home'
    ));

    Route::get('oauth2callback', array(
        'uses' => 'HomeController@oAuth'
    ));

    Route::get('info', array(
        'uses' => 'HomeController@info'
    ));
});

Route::get('/', array(
    'as' => 'welcome',
    'uses' => 'HomeController@showWelcome'
));

