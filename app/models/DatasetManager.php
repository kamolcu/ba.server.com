<?php
class DatasetManager
{
    public function updateData($datasetName, $startDate, $endDate) {
        $exists = App::make('Helper')->isDatasetExists($datasetName, $startDate, $endDate);
        if (!$exists) {
            $inputs = array(
                'name' => $datasetName,
                'start_date' => $startDate,
                'end_date' => $endDate,
            );
            $rules = array();
            App::make('Helper')->create('DataSet', $inputs, $rules);
        }
        $ds = App::make('Helper')->getDataSet($datasetName, $startDate, $endDate);
        return $ds;
    }

    public function loadData($analytics, $start, $end) {
        $finished = App::make('Helper')->isDataSetFinished($start, $end);
        if (!$finished) {
            $msg = sprintf('Process dataset %s to %s', $start, $end);
            Log::debug($msg);

            App::make('DataManager')->loadData($analytics, $start, $end);

            $msg = sprintf('Finised dataset %s to %s', $start, $end);
            Log::debug($msg);

            $rules = array();
            $inputs = array(
                'name' => 'DataSet_' . $start . '_' . $main_end,
                'start_date' => $start,
                'end_date' => $end,
            );
            App::make('Helper')->create('FinishedDataset', $inputs, $rules);
        } else {
            $msg = sprintf('(Already done) Skip dataset %s to %s', $start, $end);
            Log::debug($msg);
        }
    }
}
