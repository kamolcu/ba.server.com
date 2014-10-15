<?php
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
    public function frontPage($subdomain = 'funnel') {
        if (!Session::has('client')) {
            $client = new Google_Client();
            $client->setApplicationName("Funnel Application");

            $client->setClientId('137589562420-65fhcns4kqiu1o2rukbf37289tqunjet.apps.googleusercontent.com');
            $client->setClientSecret('pEmBCrl9AhmqSthR2I1qi1oH');
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
            return Redirect::to($authUrl); // Google's page

        }

        return View::make('front');
    }
    public function compare() {
        if(!Session::has('client')) return Redirect::to('/');

        $msg = sprintf('Compare inputs = %s', print_r(Input::all() , true));
        Log::debug($msg);

        // TODO: validation of date inputs

        Session::put('main_start', $Input['main_start']);
        Session::put('main_end', $Input['main_end']);
        Session::put('history_start', $Input['history_start']);
        Session::put('history_end', $Input['history_end']);

        $client = unserialize(Session::get('client'));
        if (!empty($client) && $client->getAccessToken()) {
            try {
                $analytics = new Google_Service_Analytics($client);

                $msg = sprintf('Process dataset %s to %s', $Input['main_start'], $Input['main_end']);
                Log::debug($msg);

                App::make('DataManager')->loadData($analytics, $Input['main_start'], $Input['main_end']);

                $msg = sprintf('Finised dataset %s to %s', $Input['main_start'], $Input['main_end']);
                Log::debug($msg);

                $msg = sprintf('Process dataset %s to %s', $Input['history_start'], $Input['history_end']);
                Log::debug($msg);

                App::make('DataManager')->loadData($analytics, $Input['history_start'], $Input['history_end']);

                $msg = sprintf('Finised dataset %s to %s', $Input['history_start'], $Input['history_end']);
                Log::debug($msg);
            }
            catch(Google_Auth_Exception $gx) {
                Log::error($gx->getMessage());
                Session::clear();
                return Redirect::to('/');
            }
        }
        return View::make('report.summary');
    }
    public function summaryView() {
        return View::make('report.summary');
    }
    public function showWelcome() {
        return '';
    }

    public function home($subdomain) {
        $client = null;
        if (!Session::has('client')) {
            $client = new Google_Client();
            $client->setApplicationName("Funnel Application");

            $client->setClientId('137589562420-65fhcns4kqiu1o2rukbf37289tqunjet.apps.googleusercontent.com');
            $client->setClientSecret('pEmBCrl9AhmqSthR2I1qi1oH');
            $client->setRedirectUri(URL::route('home', $subdomain) . '/oauth2callback');
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
        if (isset($_GET['code'])) {
            $client = unserialize(Session::get('client'));
            $client->authenticate($_GET['code']);
            //$_SESSION['access_token'] = $client->getAccessToken();
            Session::put('access_token', $client->getAccessToken());
            Session::put('client', serialize($client));
            //$start = App::make('Helper')->getDefaultStartDate()->toDateString();
            //$end = App::make('Helper')->getDefaultEndDate()->toDateString();
            return Redirect::route('front', Session::get('subdomain'));
        }
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
                return Redirect::to('/');
            }
            // $result = $analytics->data_ga->get('ga:68738788', $startDate, $endDate, App::make('Helper')->matrixs, array(
            //     'dimensions' => 'ga:deviceCategory',
            //     'sort' => '-ga:sessions'
            // ));
            // s($result);

            // $segments = $analytics->management_segments->listManagementSegments();
            // s($segments);


        } else {
            Session::clear();
            return Redirect::to('/');
        }
    }

    public function errorView() {
        return View::make('app-error');
    }
}
