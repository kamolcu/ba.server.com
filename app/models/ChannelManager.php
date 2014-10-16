<?php
class ChannelManager
{
    const CHANNEL_NAME = 0;
    const SESSION = 1;
    const BOUNCE_RATE = 2;
    const TX = 3;

    public function updateData($row, $datasetId) {
        Log::debug('ChannelManager::UpdateData row = ' . print_r($row, true));
        $channel_exists = App::make('Helper')->isChannelExists($row[self::CHANNEL_NAME], $datasetId);
        if (!$channel_exists) {
            $inputs = array(
                'name' => $row[self::CHANNEL_NAME],
                'sessions' => $row[self::SESSION],
                'bounce_rate' => $row[self::BOUNCE_RATE],
                'conversion_rate' => $row[self::TX],
                'dataset_id' => $datasetId,
            );
            $rules = array();
            App::make('Helper')->create('Channel', $inputs, $rules);
        }
    }
}
