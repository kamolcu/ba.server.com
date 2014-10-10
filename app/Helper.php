<?php
use Carbon\Carbon;

class Helper
{
    // Not exceed 10 matrics
    public $matrixs = 'ga:sessions,ga:percentNewSessions,ga:newUsers,ga:bounceRate,ga:pageviewsPerSession,ga:avgSessionDuration,ga:transactions,ga:transactionRevenue,ga:transactionsPerSession';

    public function isChannelExists($name, $datasetId) {
        $row = $this->getChannel($name, $datasetId);
        return !empty($row);
    }
    public function getChannel($name, $datasetId) {
        $row = Channel::whereName($name)->where('dataset_id', '=', $datasetId)->first();
        return $row;
    }
    public function isDatasetExists($name, $startDate, $endDate) {
        $row = $this->getDataSet($name, $startDate, $endDate);
        return !empty($row);
    }
    public function getDataSet($name, $startDate, $endDate) {
        $row = DataSet::whereName($name)->where('start_date', '=', $startDate)->where('end_date', '=', $endDate)->first();
        return $row;
    }
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

    public function getLandingProductData($analytics, $startDate, $endDate) {
        $dim = array(
            'dimensions' => 'ga:landingPagePath'
        );
        $sort = array(
            'sort' => '-ga:sessions'
        );
        $filters = array(
            'filters' => 'ga:landingPagePath=@/product'
        );
        return $this->getGAData($analytics, $startDate, $endDate, $this->matrixs, $dim, $sort, $filters);
    }
    public function getOtherChannelsData($analytics, $startDate, $endDate) {
        $dim = array(
            'dimensions' => 'ga:source'
        );
        $sort = array(
            'sort' => '-ga:sessions'
        );
        $filters = array(
            'filters' => 'ga:channelGrouping=@other'
        );
        return $this->getGAData($analytics, $startDate, $endDate, $this->matrixs, $dim, $sort, $filters);
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

    public function create($modelName, $attributes = array() , $rules = array()) {
        $model = new $modelName();
        if (!empty($rules)) {
            $validator = Validator::make($attributes, $rules);
            if ($validator->fails()) {
                return $validator->messages()->all(':message');
            }
        }
        foreach ($attributes as $key => $value) {
            $model->{$key} = $value;
        }
        return $model->save();
    }

    public function update($modelName, $modelId, $attributes = array() , $rules = array()) {
        $model = $modelName::find($modelId);
        if ($model) {
            if (!empty($rules)) {
                $validator = Validator::make($attributes, $rules);
                if ($validator->fails()) {
                    return $validator->messages()->all(':message');
                }
            }
            foreach ($attributes as $key => $value) {
                $model->{$key} = $value;
            }
            return $model->save();
        }
    }
}
