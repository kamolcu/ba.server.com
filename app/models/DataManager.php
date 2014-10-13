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
    }
}
