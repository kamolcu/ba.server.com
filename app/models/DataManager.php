<?php
class DataManager
{
    public function loadData($analytics, $startDate, $endDate) {
        // Channels Acquisition => Overview
        $datasetName = 'Channels';
        $ds = App::make('DatasetManager')->updateData($datasetName, $startDate, $endDate);
        $result = App::make('Helper')->getChannelsData($analytics, $startDate, $endDate);
        foreach ($result->rows as $data) {
            App::make('ChannelManager')->updateData($data, $ds->id);
        }
        // Other Channels - dig deeper into (Other) channel
        $datasetName = 'Other Channels';
        $ds = App::make('DatasetManager')->updateData($datasetName, $startDate, $endDate);
        $result = App::make('Helper')->getOtherChannelsData($analytics, $startDate, $endDate);
        foreach ($result->rows as $data) {
            App::make('ChannelManager')->updateData($data, $ds->id);
        }
        // Behavior => Site Content => Landing Pages
        $filters_path = array(
            'Landing Product' => 'ga:landingPagePath=@/product',
            'Landing Line' => 'ga:landingPagePath=@/line',
            'Landing Direct' => 'ga:landingPagePath==/',
            'Landing Category' => 'ga:landingPagePath=@/category',
            'Landing Search' => 'ga:landingPagePath=@/search',
            'Landing Everyday-wow' => 'ga:landingPagePath=@/everyday-wow'
        );
        foreach ($filters_path as $key => $path) {
            $datasetName = $key;
            $ds = App::make('DatasetManager')->updateData($datasetName, $startDate, $endDate);
            $filters = array(
                'filters' => $path
            );
            $result = App::make('Helper')->getLandingData($analytics, $startDate, $endDate, $filters);
            $totalResults = $result->totalsForAllResults;
            App::make('LandingManager')->updateData($datasetName, $totalResults, $ds->id);
        }

        $datasetName = 'Device';
        $ds = App::make('DatasetManager')->updateData($datasetName, $startDate, $endDate);
        $result = App::make('Helper')->getDeviceData($analytics, $startDate, $endDate);
        foreach ($result->rows as $data) {
            App::make('DeviceManager')->updateData($data, $ds->id);
        }

        $this->loadSegmentData($analytics, $startDate, $endDate);

        $datasetName = 'Completed Order';
        $ds = App::make('DatasetManager')->updateData($datasetName, $startDate, $endDate);
        $data = App::make('PCMSManager')->getCompletedOrder($startDate, $endDate);
        if (!empty($data)) {
            App::make('PCMSManager')->updateData('CompleteOrder', $data, $ds->id);
        }

        $datasetName = 'Paid Order';
        $ds = App::make('DatasetManager')->updateData($datasetName, $startDate, $endDate);
        $data = App::make('PCMSManager')->getPaidOrder($startDate, $endDate);
        if (!empty($data)) {
            App::make('PCMSManager')->updateData('PaidOrder', $data, $ds->id);
        }
    }

    public function loadSegmentData($analytics, $startDate, $endDate) {
        $segments = array(
            'product_page_key' => Config::get('config.product_page_key') ,
            'step1' => Config::get('config.step1') ,
            'step1_backfill' => Config::get('config.step1_backfill') ,
            'step2' => Config::get('config.step2') ,
            'step2_backfill' => Config::get('config.step2_backfill') ,
            'step3' => Config::get('config.step3') ,
            'step3_backfill' => Config::get('config.step3_backfill') ,
        );

        $datasetName = 'Segment';
        $ds = App::make('DatasetManager')->updateData($datasetName, $startDate, $endDate);

        $extracted = array();
        foreach ($segments as $key => $segment) {
            $seg = array(
                'segment' => $segment
            );
            $result = App::make('Helper')->getSegmentData($analytics, $startDate, $endDate, $seg);
            $totalResults = $result->totalsForAllResults;
            $extracted[$key] = array(
                'goal1Starts' => $totalResults['ga:goal1Starts'],
                'goal1Completions' => $totalResults['ga:goal1Completions'],
                'sessions' => $totalResults['ga:sessions']
            );
        }
        $msg = sprintf('segment values = %s', print_r($extracted, true));
        Log::debug($msg);
        // Product Detail
        $name = 'Product Detail';
        $value = $extracted['product_page_key']['goal1Starts'];
        $inputs = array(
            'name' => $name,
            'sessions' => $value,
            'dataset_id' => $ds->id
        );
        $rules = array();
        App::make('SegmentManager')->updateData($name, $inputs, $rules, $ds->id);

        $name = 'Product Detail';
        $value = $extracted['product_page_key']['goal1Starts'];
        $inputs = array(
            'name' => $name,
            'sessions' => $value,
            'dataset_id' => $ds->id
        );
        $rules = array();
        App::make('SegmentManager')->updateData($name, $inputs, $rules, $ds->id);

        $name = 'Paid Order';
        $value = $extracted['product_page_key']['goal1Completions'];
        $inputs = array(
            'name' => $name,
            'sessions' => $value,
            'dataset_id' => $ds->id
        );
        $rules = array();
        App::make('SegmentManager')->updateData($name, $inputs, $rules, $ds->id);

        $name = 'Login';
        $value1 = $extracted['step1']['sessions'];
        $value2 = $extracted['step1_backfill']['sessions'];
        $value = $value1 + $value2;
        $inputs = array(
            'name' => $name,
            'sessions' => $value,
            'dataset_id' => $ds->id
        );
        $rules = array();
        App::make('SegmentManager')->updateData($name, $inputs, $rules, $ds->id);

        $name = 'Fill Address';
        $value1 = $extracted['step2']['sessions'];
        $value2 = $extracted['step2_backfill']['sessions'];
        $value = $value1 + $value2;
        $inputs = array(
            'name' => $name,
            'sessions' => $value,
            'dataset_id' => $ds->id
        );
        $rules = array();
        App::make('SegmentManager')->updateData($name, $inputs, $rules, $ds->id);

        $name = 'Payment Channel';
        $value1 = $extracted['step3']['sessions'];
        $value2 = $extracted['step3_backfill']['sessions'];
        $value = $value1 + $value2;
        $inputs = array(
            'name' => $name,
            'sessions' => $value,
            'dataset_id' => $ds->id
        );
        $rules = array();
        App::make('SegmentManager')->updateData($name, $inputs, $rules, $ds->id);
    }
}
