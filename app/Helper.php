<?php
use Carbon\Carbon;

class Helper
{
    // Not exceed 10 matrics
    public $matrixs = 'ga:sessions,ga:percentNewSessions,ga:newUsers,ga:bounceRate,ga:pageviewsPerSession,ga:avgSessionDuration,ga:transactions,ga:transactionRevenue,ga:transactionsPerSession';

    public function getDefaultStartDate($now = null) {
        if (empty($now)) $now = Carbon::now();
        // Get nearest Monday of the current week
        if ($now->dayOfWeek == Carbon::MONDAY) {
            $now->subDays(7);
        } else {
            while ($now->dayOfWeek != Carbon::MONDAY) {
                $now->subDays(1);
            }
        }
        return $now;
    }

    public function getDefaultEndDate($now = null) {
        if (empty($now)) $now = Carbon::now();
        $now->subDays(1);
        return $now;
    }

    public function getChannelsData($analytics, $startDate, $endDate) {
        $dim = array(
            'dimensions' => 'ga:channelGrouping'
        );
        $sort = array(
            'sort' => '-ga:sessions'
        );
        return $this->getGAData($analytics, $startDate, $endDate, $this->matrixs, $dim, $sort);
    }

    public function getGAData($analytics, $startDate, $endDate, $matrixs, $dimensions, $sort = array() , $filters = array() , $segment = array()) {
        $options = array_merge($dimensions, $sort, $filters, $segment);
        $result = $analytics->data_ga->get(Config::get('config.ga-profile') , $startDate, $endDate, $matrixs, $options);
        return $result;
    }
}
