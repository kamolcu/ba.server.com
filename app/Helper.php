<?php
use Carbon\Carbon;

class Helper
{
    // Not exceed 10 matrics
    public $matrix_device = 'ga:sessions,ga:bounceRate,ga:transactionsPerSession';
    public $matrix_channels = 'ga:sessions,ga:bounceRate,ga:transactionsPerSession';
    public $matrixs = 'ga:sessions,ga:percentNewSessions,ga:newUsers,ga:bounceRate,ga:pageviewsPerSession,ga:avgSessionDuration,ga:transactions,ga:transactionRevenue,ga:transactionsPerSession';
    public $matrix_segment = 'ga:goal1Starts,ga:goal1Completions,ga:sessions';
    public $landing_list = array(
        'Landing Product',
        'Landing Line',
        'Landing Direct',
        'Landing Category',
        'Landing Search',
        'Landing Everyday-wow',
    );
    public function preLoad($start, $end, $historyStart, $historyEnd) {
        $deviceDataSet = App::make('Helper')->getDataSet('Device', $start, $end);
        $historyDeviceDataSet = App::make('Helper')->getDataSet('Device', $historyStart, $historyEnd);
        $channelDataset = App::make('Helper')->getDataSet('Channels', $start, $end);
        $historyChannelDataSet = App::make('Helper')->getDataSet('Channels', $historyStart, $historyEnd);
        $otherChannelDataset = App::make('Helper')->getDataSet('Other Channels', $start, $end);
        $historyOtherChannelDataSet = App::make('Helper')->getDataSet('Other Channels', $historyStart, $historyEnd);
        $product = App::make('Helper')->getDataSet('Landing Product', $start, $end);
        $history_product = App::make('Helper')->getDataSet('Landing Product', $historyStart, $historyEnd);

        $funnel = App::make('Helper')->getDataSet('Segment', $start, $end);
        $history_funnel = App::make('Helper')->getDataSet('Segment', $historyStart, $historyEnd);

        $completed_order = App::make('Helper')->getDataSet('Completed Order', $start, $end);
        $completed_order_history = App::make('Helper')->getDataSet('Completed Order', $historyStart, $historyEnd);

        $paid_order = App::make('Helper')->getDataSet('Paid Order', $start, $end);
        $paid_order_history = App::make('Helper')->getDataSet('Paid Order', $historyStart, $historyEnd);

        foreach ($this->landing_list as $list) {
            $landing = App::make('Helper')->getDataSet($list, $start, $end);
            $history_landing = App::make('Helper')->getDataSet($list, $historyStart, $historyEnd);
            Session::put($list, $landing->id);
            Session::put('history_' . $list, $history_landing->id);
        }

        Session::put('main_start', $start);
        Session::put('main_end', $end);
        Session::put('history_start', $historyStart);
        Session::put('history_end', $historyEnd);

        Session::put('product_funnel_id', $funnel->id);
        Session::put('history_product_funnel_id', $history_funnel->id);
        Session::put('product_data_set_id', $product->id);
        Session::put('history_product_data_set_id', $history_product->id);
        Session::put('device_data_set_id', $deviceDataSet->id);
        Session::put('history_device_data_set_id', $historyDeviceDataSet->id);
        Session::put('channel_data_set_id', $channelDataset->id);
        Session::put('history_channel_data_set_id', $historyChannelDataSet->id);
        Session::put('channel_other_data_set_id', $otherChannelDataset->id);
        Session::put('history_other_channel_data_set_id', $historyOtherChannelDataSet->id);
        Session::put('completed_order_id', $completed_order->id);
        Session::put('history_completed_order_id', $completed_order_history->id);
        Session::put('paid_order_id', $paid_order->id);
        Session::put('history_paid_order_id', $paid_order_history->id);
    }
    public function isDataSetFinished($startDate, $endDate) {
        $result = FinishedDataset::where('start_date', '=', $startDate)->where('end_date', '=', $endDate)->first();
        return !empty($result);
    }
    public function isDeviceExists($name, $datasetId) {
        $row = $this->getDevice($name, $datasetId);
        return !empty($row);
    }
    public function getDevice($name, $datasetId) {
        $row = Device::whereName($name)->where('dataset_id', '=', $datasetId)->first();
        return $row;
    }
    public function isSegmentExists($name, $datasetId) {
        $row = $this->getSegment($name, $datasetId);
        return !empty($row);
    }
    public function getSegment($name, $datasetId) {
        $row = GoalFunnel::whereName($name)->where('dataset_id', '=', $datasetId)->first();
        return $row;
    }
    public function isLandingExists($name, $datasetId) {
        $row = $this->getLanding($name, $datasetId);
        return !empty($row);
    }
    public function getLanding($name, $datasetId) {
        $row = Landing::whereName($name)->where('dataset_id', '=', $datasetId)->first();
        return $row;
    }
    public function isChannelExists($name, $datasetId) {
        $row = $this->getChannel($name, $datasetId);
        return !empty($row);
    }
    public function getChannel($name, $datasetId) {
        $row = Channel::whereName($name)->where('dataset_id', '=', $datasetId)->first();
        return $row;
    }
    public function isCompletedOrderExists($name, $datasetId) {
        $row = $this->getCompletedOrder($name, $datasetId);
        return !empty($row);
    }
    public function getCompletedOrder($name, $datasetId) {
        $row = CompleteOrder::whereName($name)->where('dataset_id', '=', $datasetId)->first();
        return $row;
    }
    public function getCompletedOrders($datasetId) {
        $rows = CompleteOrder::where('dataset_id', '=', $datasetId)->get();
        return $rows;
    }
    public function getPaidOrders($datasetId) {
        $rows = PaidOrder::where('dataset_id', '=', $datasetId)->get();
        return $rows;
    }
    public function isDatasetExists($name, $startDate, $endDate) {
        $row = $this->getDataSet($name, $startDate, $endDate);
        return !empty($row);
    }
    public function getDataSet($name, $startDate, $endDate) {
        $row = DataSet::whereName($name)->where('start_date', '=', $startDate)->where('end_date', '=', $endDate)->first();
        return $row;
    }
    public function getDefaultHistoryStartDate($now = null) {
        return $this->getDefaultStartDate()->subDays(7);
    }
    public function getDefaultHistoryEndDate($now = null) {
        return $this->getDefaultEndDate()->subDays(7);
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
    public function getSegmentData($analytics, $startDate, $endDate, $segments) {
        $dim = array(
            'dimensions' => 'ga:deviceCategory'
        );
        $sort = array(
            'sort' => '-ga:sessions'
        );
        return $this->getGAData($analytics, $startDate, $endDate, $this->matrix_segment, $dim, $sort, array() , $segments);
    }
    public function getLandingData($analytics, $startDate, $endDate, $filters) {
        $dim = array(
            'dimensions' => 'ga:landingPagePath'
        );
        $sort = array(
            'sort' => '-ga:sessions'
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
        return $this->getGAData($analytics, $startDate, $endDate, $this->matrix_channels, $dim, $sort);
    }
    public function getDeviceData($analytics, $startDate, $endDate) {
        $dim = array(
            'dimensions' => 'ga:deviceCategory'
        );
        $sort = array(
            'sort' => '-ga:sessions'
        );
        return $this->getGAData($analytics, $startDate, $endDate, $this->matrix_device, $dim, $sort);
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

    public function formatDecimal($value, $comma = ',') {
        return number_format($value, 2, '.', $comma);
    }
    public function formatInteger($value, $comma = ',') {
        return number_format($value, 0, '.', $comma);
    }
    public function padSpace($input, $length) {
        return str_pad($input, $length, '0', STR_PAD_LEFT);
    }
    public function formatPercent($input) {
        return $this->padSpace(($this->formatDecimal($input, '')) , 5) . '%';
    }
    public function test($input) {
        return '&nbsp;' . $input;
    }
}
