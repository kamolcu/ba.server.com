<?php
class SegmentManager
{
    public function updateData($name, $inputs, $rules = array(), $datasetId) {
        Log::debug('SegmentManager::UpdateData inputs = ' . print_r($inputs, true));
        $exists = App::make('Helper')->isSegmentExists($name, $datasetId);
        if (!$exists) {
            App::make('Helper')->create('GoalFunnel', $inputs, $rules);
        }
    }
}
