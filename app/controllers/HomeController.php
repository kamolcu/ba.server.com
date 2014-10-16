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
            Session::put('client', serialize($client));
            return Redirect::to($authUrl); // Google's page


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

        Session::put('main_start', $main_start);
        Session::put('main_end', $main_end);
        Session::put('history_start', $history_start);
        Session::put('history_end', $history_end);

        $client = unserialize(Session::get('client'));
        if (!empty($client) && $client->getAccessToken()) {
            try {
                $analytics = new Google_Service_Analytics($client);

                $finished = App::make('Helper')->isDataSetFinished($main_start , $main_end);
                if (!$finished) {
                    $msg = sprintf('Process dataset %s to %s', $main_start , $main_end);
                    Log::debug($msg);

                    App::make('DataManager')->loadData($analytics, $main_start , $main_end);

                    $msg = sprintf('Finised dataset %s to %s', $main_start , $main_end);
                    Log::debug($msg);

                    $rules = array();
                    $inputs = array(
                        'name' => 'DataSet_' . $main_start . '_' . $main_end ,
                        'start_date' => $main_start ,
                        'end_date' => $main_end ,
                    );
                    App::make('Helper')->create('FinishedDataset', $inputs, $rules);
                } else {
                    $msg = sprintf('(Already done) Skip dataset %s to %s', $main_start , $main_end);
                    Log::debug($msg);
                }

                $finished = App::make('Helper')->isDataSetFinished($history_start , $history_end);
                if (!$finished) {
                    $msg = sprintf('Process dataset %s to %s', $history_start , $history_end);
                    Log::debug($msg);

                    App::make('DataManager')->loadData($analytics, $history_start , $history_end);

                    $msg = sprintf('Finised dataset %s to %s', $history_start , $history_end);
                    Log::debug($msg);

                    $rules = array();
                    $inputs = array(
                        'name' => 'DataSet_' . $history_start . '_' . $history_end ,
                        'start_date' => $history_start ,
                        'end_date' => $history_end ,
                    );
                    App::make('Helper')->create('FinishedDataset', $inputs, $rules);
                } else {
                    $msg = sprintf('(Already done) Skip dataset %s to %s', $main_start , $main_end);
                    Log::debug($msg);
                }

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
