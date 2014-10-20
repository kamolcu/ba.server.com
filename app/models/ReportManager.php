<?php
class ReportManager
{
    public function getDeviceStats($device, $start, $end, $historyStart, $historyEnd) {
        $history = App::make('Helper')->getDataSet('Device', $historyStart, $historyEnd);
        $main_result = Device::where('dataset_id', '=', Session::get('device_data_set_id'))->whereName($device)->first();
        $history_result = Device::where('dataset_id', '=', Session::get('history_device_data_set_id'))->whereName($device)->first();

        $totalSessions = $this->getDeviceAllSessionsCount($start, $end);
        if (empty($totalSessions)) return array();
        // Compare sessions
        $change = App::make('StatsManager')->evalChange($history_result->sessions, $main_result->sessions);

        $output = array(
            'name' => $device,
            'change' => $change,
            'sessions' => $main_result->sessions,
            'percent' => App::make('Helper')->formatDecimal($main_result->sessions / $totalSessions * 100),
            'bounce_rate' => App::make('Helper')->formatDecimal($main_result->bounce_rate) ,
            'conversion_rate' => App::make('Helper')->formatDecimal($main_result->conversion_rate) ,
        );
        return $output;
    }

    public function getDeviceAllSessionsCount($start, $end) {
        return Device::where('dataset_id', '=', Session::get('device_data_set_id'))->sum('sessions');
    }
}
