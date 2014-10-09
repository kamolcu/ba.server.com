<?php
class ChannelManager
{
    const CHANNEL_NAME = 0;
    const SESSION = 1;
    const BOUNCE_RATE = 4;
    const TX = 7;

    public function updateData($row, $datasetId) {
        Log::debug('ChannelManager::UpdateData row = ' . print_r($row, true));
        $channel_exists = App::make('Helper')->isChannelExists($row[self::CHANNEL_NAME], $datasetId);
        if (!$channel_exists) {
            $inputs = array(
                'name' => $data[self::CHANNEL_NAME],
                'sessions' => $data[self::SESSION],
                'bounce_rate' => $data[self::BOUNCE_RATE],
                'conversion_rate' => $data[self::TX],
                'dataset_id' => $datasetId,
            );
            $rules = array();
            App::make('Helper')->create('Channel', $inputs, $rules);
        }
    }
}
