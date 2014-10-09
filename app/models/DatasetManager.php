<?php
class DatasetManager
{
    public function updateData() {
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
}
