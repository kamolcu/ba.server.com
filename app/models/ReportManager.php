<?php
use \Carbon\Carbon;
class ReportManager
{
    public function getDeviceStats($device) {
        $main_result = Device::where('dataset_id', '=', Session::get('device_data_set_id'))->whereName($device)->first();
        $history_result = Device::where('dataset_id', '=', Session::get('history_device_data_set_id'))->whereName($device)->first();

        $totalSessions = $this->getTotalSessions('Device', Session::get('device_data_set_id'));
        if (empty($totalSessions)) return array();
        // Compare sessions
        $change = App::make('StatsManager')->evalChange($history_result->sessions, $main_result->sessions);

        $output = array(
            'name' => $device,
            'change' => $change,
            'sessions' => $main_result->sessions,
            'percent' => App::make('Helper')->formatDecimal($main_result->sessions / $totalSessions * 100) ,
            'bounce_rate' => App::make('Helper')->formatDecimal($main_result->bounce_rate) ,
            'conversion_rate' => App::make('Helper')->formatDecimal($main_result->conversion_rate) ,
        );
        return $output;
    }

    public function getChannelStats() {
        $output = new Illuminate\Support\Collection();
        $channels = Channel::where('dataset_id', '=', Session::get('channel_data_set_id'))->get();
        $history = Channel::where('dataset_id', '=', Session::get('history_channel_data_set_id'))->get();
        $totalSessions = $this->getTotalSessions('Channel', Session::get('channel_data_set_id'));
        if (empty($totalSessions)) return array();
        foreach ($channels as $ch) {
            if (strtolower($ch->name) == '(other)') {
                // dig into other details, get top 5
                $others = Channel::where('dataset_id', '=', Session::get('channel_other_data_set_id'))->get();
                $counter = 0;
                $limit = Config::get('config.other-channel-count', 5);
                foreach ($others as $other) {
                    if ($counter >= $limit) break;


                    $counter++;
                    $temp = array(
                        'name' => $other->name,
                        'sessions' => $other->sessions
                    );
                    $output->push($temp);
                }
            } else {
                $temp = array(
                    'name' => $ch->name,
                    'sessions' => $ch->sessions
                );
                $output->push($temp);
            }
        }
        $output = $output->sortByDesc('sessions')->take(Config::get('config.channels-take', 7));
        $counter = 0;
        foreach ($output as $item) {
            $item['percent'] = $item['sessions'] / $totalSessions * 100;
            $output->offsetSet($counter, $item);
            $counter++;
        }
        // retrieve history data
        $hhOutput = new Illuminate\Support\Collection();
        foreach ($history as $hh) {
            if (strtolower($hh->name) == '(other)') {
                $others = Channel::where('dataset_id', '=', Session::get('history_other_channel_data_set_id'))->get();

                foreach ($others as $other) {
                    $temp = array(
                        'name' => $other->name,
                        'sessions' => $other->sessions
                    );
                    $hhOutput->push($temp);
                }
            } else {
                $temp = array(
                    'name' => $hh->name,
                    'sessions' => $hh->sessions
                );
                $hhOutput->push($temp);
            }
        }
        $hhOutput = $hhOutput->sortByDesc('sessions');
        // compare
        $counter = 0;
        foreach ($output as $oo) {
            foreach ($hhOutput as $hh) {
                if ($oo['name'] == $hh['name']) {
                    // calculate change
                    $change = App::make('StatsManager')->evalChange($hh['sessions'], $oo['sessions']);
                    $oo['change'] = $change;
                    $output->offsetSet($counter, $oo);
                }
            }
            $counter++;
        }
        return $output;
    }
    public function getTotalSessions($modelName, $dataset_id) {
        return $modelName::where('dataset_id', '=', $dataset_id)->sum('sessions');
    }
    public function getReportHeader() {
        Carbon::setToStringFormat('d M Y');
        $start = new Carbon(Session::get('main_start'));
        $end = new Carbon(Session::get('main_end'));
        $history = new Carbon(Session::get('history_start'));
        $historyEnd = new Carbon(Session::get('history_end'));
        return sprintf('As of Period %s to %s compare with period %s to %s', $start, $end, $history, $historyEnd);
    }
}
