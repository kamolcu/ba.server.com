<?php
use Carbon\Carbon;

class HomeController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Default Home Controller
    |--------------------------------------------------------------------------
    |
    | You may wish to use controllers instead of, or in addition to, Closure
    | based routes. That's great! Here is an example controller method to
    | get you started. To route to this controller, just add the route:
    |
    |   Route::get('/', 'HomeController@showWelcome');
    |
    */
    public function frontPage() {

        if (Input::has('dev')) {
            return View::make('front');
        }

        if (!Session::has('client')) {
            $client = new Google_Client();
            $client->setApplicationName("Funnel Application");

            $client->setClientId(Config::get('app.google_client_id'));
            $client->setClientSecret(Config::get('app.google_secret'));
            $client->setRedirectUri(URL::route('front') . '/oauth2callback');
            //$client->setDeveloperKey('AIzaSyDGlZpsatv30xAitjk1U2Ra78zrTbbtzQs');
            $client->setScopes(array(
                'https://www.googleapis.com/auth/analytics.readonly'
            ));
        } else {
            $client = unserialize(Session::get('client'));
        }

        if (Session::has('access_token')) {
            $client->setAccessToken(Session::get('access_token'));
            Session::put('client', serialize($client));
        } else {
            $authUrl = $client->createAuthUrl();
            Session::put('client', serialize($client));
            // Google's page
            return Redirect::to($authUrl);
        }

        return View::make('front');
    }
    public function compare() {
        if (!Session::has('client')) return Redirect::to('/');

        $msg = sprintf('Compare inputs = %s', print_r(Input::all() , true));
        Log::debug($msg);
        // TODO: validation of date inputs
        $main_start = Input::get('main_start');
        $main_end = Input::get('main_end');
        $history_start = Input::get('history_start');
        $history_end = Input::get('history_end');

        $main_start_date = new Carbon($main_start);
        $main_end_date = new Carbon($main_end);
        $history_start_date = new Carbon($history_start);
        $history_end_date = new Carbon($history_end);
        if ($main_start_date > $main_end_date) {
            $msg = Config::get('config.main_range') . ' ' . Config::get('config.start_date') . Config::get('config.incorrect_start_date');
            return Redirect::to('/')->with('error', $msg);
        }

        if ($history_start_date > $history_end_date) {
            $msg = Config::get('config.historical_range') . ' ' . Config::get('config.start_date') . Config::get('config.incorrect_start_date');
            return Redirect::to('/')->with('error', $msg);
        }

        Session::put('main_start', $main_start);
        Session::put('main_end', $main_end);
        Session::put('history_start', $history_start);
        Session::put('history_end', $history_end);

        $client = unserialize(Session::get('client'));
        if (!empty($client) && $client->getAccessToken()) {
            try {
                $analytics = new Google_Service_Analytics($client);

                App::make('DatasetManager')->loadData($analytics, $main_start, $main_end);
                App::make('DatasetManager')->loadData($analytics, $history_start, $history_end);

                App::make('Helper')->preLoad($main_start, $main_end, $history_start, $history_end);

                return Redirect::route('summary');
            }
            catch(Google_Auth_Exception $gx) {
                Log::error($gx->getMessage());
                Session::clear();
                return Redirect::to('/');
            }
        }
        Session::clear();
        return Redirect::to('/');
    }
    public function summaryView() {
        $client = unserialize(Session::get('client'));
        if (!empty($client) && $client->getAccessToken()) {
            return View::make('report.summary');
        } else {
            return Redirect::route('front');
        }
    }
    public function showWelcome() {
        return '';
    }

    public function home() {
        $client = null;
        if (!Session::has('client')) {
            $client = new Google_Client();
            $client->setApplicationName("Funnel Application");

            $client->setClientId('137589562420-65fhcns4kqiu1o2rukbf37289tqunjet.apps.googleusercontent.com');
            $client->setClientSecret('pEmBCrl9AhmqSthR2I1qi1oH');
            $client->setRedirectUri(URL::to('/') . '/oauth2callback');
            //$client->setDeveloperKey('AIzaSyDGlZpsatv30xAitjk1U2Ra78zrTbbtzQs');
            $client->setScopes(array(
                'https://www.googleapis.com/auth/analytics.readonly'
            ));
        } else {
            $client = unserialize(Session::get('client'));
        }

        if (Session::has('access_token')) {
            $client->setAccessToken(Session::get('access_token'));
            Session::put('client', serialize($client));
        } else {
            $authUrl = $client->createAuthUrl();
            Session::put('client', serialize($client));
            return Redirect::to($authUrl);
        }
    }
    public function oAuth() {
        $client = unserialize(Session::get('client'));
        if (isset($_GET['code']) && !empty($client)) {
            $client->authenticate($_GET['code']);
            Session::put('access_token', $client->getAccessToken());
            Session::put('client', serialize($client));
        }
        return Redirect::route('front');
    }
    public function info() {
        $startDate = Input::get('start_date');
        $endDate = Input::get('end_date');

        $client = unserialize(Session::get('client'));
        if (!empty($client) && $client->getAccessToken()) {
            try {
                $analytics = new Google_Service_Analytics($client);
                App::make('DataManager')->loadData($analytics, $startDate, $endDate);
            }
            catch(Google_Auth_Exception $gx) {
                Log::error($gx->getMessage());
                Session::clear();
                return Redirect::route('home');
            }
        } else {
            Session::clear();
            return Redirect::route('home');
        }
    }

    public function errorView() {
        return View::make('app-error');
    }
    public function clear() {
        Session::clear();
    }
}
