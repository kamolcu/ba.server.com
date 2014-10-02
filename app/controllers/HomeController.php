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
            return Redirect::to(URL::route('home') . '/info?start_date=2014-09-29&end_date=2014-09-30');
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
