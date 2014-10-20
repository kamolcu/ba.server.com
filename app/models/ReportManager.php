<?php
class ReportManager
{
    public function getDeviceStats($device, $start, $end, $historyStart, $historyEnd) {
        $main = App::make('Helper')->getDataSet('Device', $start, $end);
        $history = App::make('Helper')->getDataSet('Device', $historyStart, $historyEnd);
        $main_result = Device::where('dataset_id', '=', $main->id)->whereName($device)->first();
        $history_result = Device::where('dataset_id', '=', $history->id)->whereName($device)->first();

        $totalSessions = $this->getDeviceAllSessionsCount($start, $end);
        if(empty($totalSessions)) return array();

        // Compare sessions
        $change = App::make('StatsManager')->evalChange($history_result->sessions, $main_result->sessions);

        $output = array(
            'sessions' => $main_result->sessions,
            'bounce_rate' => App::make('Helper')->formatDecimal($main_result->bounce_rate),
            'conversion_rate' => App::make('Helper')->formatDecimal($main_result->conversion_rate),
            'change' => $change,
            'percent' => App::make('Helper')->formatDecimal($main_result->sessions / $totalSessions * 100)
        );
        return $output;
    }

    public function getDeviceAllSessionsCount($start, $end) {
        $main = App::make('Helper')->getDataSet('Device', $start, $end);
        return Device::where('dataset_id', '=', $main->id)->sum('sessions');
    }
}
