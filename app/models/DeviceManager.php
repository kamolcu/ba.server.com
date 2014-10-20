<?php
class DeviceManager
{
    const CHANNEL_NAME = 0;
    const SESSION = 1;
    const BOUNCE_RATE = 2;
    const TX = 3;

    public function updateData($row, $datasetId) {
        Log::debug('DeviceManager::UpdateData row = ' . print_r($row, true));
        $exists = App::make('Helper')->isDeviceExists($row[self::CHANNEL_NAME], $datasetId);
        if (!$exists) {
            $inputs = array(
                'name' => $row[self::CHANNEL_NAME],
                'sessions' => $row[self::SESSION],
                'bounce_rate' => $row[self::BOUNCE_RATE],
                'conversion_rate' => $row[self::TX],
                'dataset_id' => $datasetId,
            );
            $rules = array();
            App::make('Helper')->create('Device', $inputs, $rules);
        }
    }
    public function getDevicesList($datasetId) {
        return Device::where('dataset_id', '=', $datasetId)->orderBy('sessions', 'desc')->get();
    }
}
