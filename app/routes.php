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
        'as' => 'front',
        'uses' => 'HomeController@frontPage'
    ));

    Route::get('oauth2callback', array(
        'uses' => 'HomeController@oAuth'
    ));

    Route::get('info', array(
        'uses' => 'HomeController@info'
    ));

    Route::get('summary', array(
        'as' => 'summary',
        'uses' => 'HomeController@summaryView'
    ));

    Route::any('/compare', array(
        'as' => 'compare',
        'uses' => 'HomeController@compare'
    ));
});

Route::any('home', array(
    'as' => 'home',
    'uses' => 'HomeController@home'
));

Route::get('/something-is-not-right', array(
    'as' => 'error.handler',
    'uses' => 'HomeController@errorView'
));

Route::get('oauth2callback', array(
    'uses' => 'HomeController@oAuth'
));

Route::get('/', array(
    'as' => 'front',
    'uses' => 'HomeController@frontPage'
));

Route::any('/compare', array(
    'as' => 'compare',
    'uses' => 'HomeController@compare'
));

Route::get('summary', array(
    'as' => 'summary',
    'uses' => 'HomeController@summaryView'
));

Route::get('clear', array(
    'as' => 'clear',
    'uses' => 'HomeController@clear'
));

Route::get('/download/{filePath}', array(
    'as' => 'file.download',
    function ($filePath) {
        $fullPath = public_path() . '/download/' . $filePath;
        if (file_exists($fullPath)) {
            return Response::download($fullPath);
        } else {
            return 'Error 404 - File not found. (' . $filePath .')';
        }
    }
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
