<?php
class LandingManager
{
    public function updateData($name, $row, $datasetId) {
        Log::debug('LandingManager::UpdateData row = ' . print_r($row, true));
        $exists = App::make('Helper')->isLandingExists($name, $datasetId);
        if (!$exists) {
            $inputs = array(
                'name' => $name,
                'sessions' => $row['ga:sessions'],
                'dataset_id' => $datasetId,
            );
            $rules = array();
            App::make('Helper')->create('Landing', $inputs, $rules);
        }
    }
}
