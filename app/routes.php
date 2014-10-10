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

Route::get('/{a}', array(
    'as' => 'w1',
    'uses' => 'HomeController@showWelcome'
));

Route::get('/{a}/{b}', array(
    'as' => 'w2',
    'uses' => 'HomeController@showWelcome'
));

Route::get('/{a}/{b}/{c}', array(
    'as' => 'w3',
    'uses' => 'HomeController@showWelcome'
));

Route::get('/{a}/{b}/{c}/{d}', array(
    'as' => 'w4',
    'uses' => 'HomeController@showWelcome'
));

Route::get('/{a}/{b}/{c}/{d}/{e}', array(
    'as' => 'w5',
    'uses' => 'HomeController@showWelcome'
));

Route::get('/{a}/{b}/{c}/{d}/{e}/{f}', array(
    'as' => 'w6',
    'uses' => 'HomeController@showWelcome'
));
