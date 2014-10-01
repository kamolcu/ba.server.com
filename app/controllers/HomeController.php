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
    |	Route::get('/', 'HomeController@showWelcome');
    |
    */

    public function showWelcome() {
        return View::make('hello');
    }

    public function home() {
        $client = null;
        if (!Session::has('client')) {
            $client = new Google_Client();
            $client->setApplicationName("Test Application");

            $client->setClientId('137589562420-65fhcns4kqiu1o2rukbf37289tqunjet.apps.googleusercontent.com');
            $client->setClientSecret('pEmBCrl9AhmqSthR2I1qi1oH');
            $client->setRedirectUri('http://ba-server.com/oauth2callback');
            //$client->setDeveloperKey('AIzaSyDGlZpsatv30xAitjk1U2Ra78zrTbbtzQs');
            $client->setScopes(array(
                'https://www.googleapis.com/auth/analytics.readonly'
            ));
        } else {
            $client = unserialize(Session::get('client'));
        }

        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $client->setAccessToken($_SESSION['access_token']);
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
            return Redirect::to('http://ba-server.com/info');
        }
    }
    public function info() {
        $startDate = Input::get('start_date');
        $endDate = Input::get('end_date');

        $client = unserialize(Session::get('client'));
        if ($client->getAccessToken()) {
            $analytics = new Google_Service_Analytics($client);

            $result = $analytics->data_ga->get('ga:68738788', $startDate, $endDate, 'ga:sessions,ga:percentNewSessions,ga:newUsers,ga:bounceRate,ga:pageviewsPerSession,ga:avgSessionDuration,ga:transactions,ga:transactionRevenue,ga:transactionsPerSession', array(
                'dimensions' => 'ga:channelGrouping',
                'sort' => '-ga:sessions'
            ));
            s($result);

            $result = $analytics->data_ga->get('ga:68738788', $startDate, $endDate, 'ga:sessions,ga:percentNewSessions,ga:newUsers,ga:bounceRate,ga:pageviewsPerSession,ga:avgSessionDuration,ga:transactions,ga:transactionRevenue,ga:transactionsPerSession', array(
                'dimensions' => 'ga:source',
                'sort' => '-ga:sessions',
                'filters' => 'ga:channelGrouping=@other'
            ));
            s($result);

            $paths = array(
                'ga:landingPagePath=@/product',
                'ga:landingPagePath=@/line',
                'ga:landingPagePath==/',
                'ga:landingPagePath=@/category',
                'ga:landingPagePath=@/search',
                'ga:landingPagePath=@/everyday-wow'
            );
            foreach ($paths as $path) {
                $result = $analytics->data_ga->get('ga:68738788', $startDate, $endDate, 'ga:sessions,ga:percentNewSessions,ga:newUsers,ga:bounceRate,ga:pageviewsPerSession,ga:avgSessionDuration,ga:transactions,ga:transactionRevenue,ga:transactionsPerSession', array(
                    'dimensions' => 'ga:landingPagePath',
                    'sort' => '-ga:sessions',
                    'filters' => $path
                ));
                s($result);
            }

            $result = $analytics->data_ga->get('ga:68738788', $startDate, $endDate, 'ga:sessions,ga:percentNewSessions,ga:newUsers,ga:bounceRate,ga:pageviewsPerSession,ga:avgSessionDuration,ga:transactions,ga:transactionRevenue,ga:transactionsPerSession', array(
                'dimensions' => 'ga:deviceCategory',
                'sort' => '-ga:sessions'
            ));
            s($result);

        }
    }
}
