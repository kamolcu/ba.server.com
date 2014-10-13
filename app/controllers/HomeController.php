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
            $_SESSION['access_token'] = $client->getAccessToken();
            Session::put('client', serialize($client));
            $start = App::make('Helper')->getDefaultStartDate()->toDateString();
            $end = App::make('Helper')->getDefaultEndDate()->toDateString();
            return Redirect::to("/info?start_date=$start&end_date=$end");
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
}
