<?php
use \Carbon\Carbon;
class ReportManager
{
    public function getPaidOrderStats() {
        $lists = App::make('Helper')->getPaidOrders(Session::get('paid_order_id'));
        $history_lists = App::make('Helper')->getPaidOrders(Session::get('history_paid_order_id'));
        return $this->buildOutputCollection($lists, $history_lists);
    }
    public function getCompletedOrderStats() {
        $lists = App::make('Helper')->getCompletedOrders(Session::get('completed_order_id'));
        $history_lists = App::make('Helper')->getCompletedOrders(Session::get('history_completed_order_id'));
        return $this->buildOutputCollection($lists, $history_lists);
    }
    public function buildOutputCollection($lists, $history_lists) {
        $output = new Illuminate\Support\Collection();
        if (empty($lists) || empty($history_lists)) {
            return $output;
        }
        $totalCount = $lists->sum('count');

        foreach ($lists as $list) {
            $name = $list->name;
            $count = $list->count;
            $found = false;
            $change = array();
            foreach ($history_lists as $history_list) {
                if ($history_list->name == $name) {
                    $history_count = $history_list->count;
                    $found = true;
                }
            }
            if ($found) {
                $change = App::make('StatsManager')->evalChange($history_count, $count);
            } else {
                $change = array(
                    'momentum' => 0,
                    'percent' => 0,
                );
            }
            $temp = array(
                'name' => $name,
                'count' => $count,
                'change' => $change,
                'percent' => App::make('Helper')->formatDecimal($count / $totalCount * 100) ,
            );
            $output->push((object)$temp);
        }
        $output = $output->sortByDesc('count');
        return $output;
    }
    public function getLandingStats() {
        $output = new Illuminate\Support\Collection();
        $sum = 0;
        $history_sum = 0;
        // Borrow total sessions from device group
        $totalSessions = $this->getTotalSessions('Device', Session::get('device_data_set_id'));
        foreach (App::make('Helper')->landing_list as $list) {
            $landing = App::make('Helper')->getLanding($list, Session::get($list));
            $history_landing = App::make('Helper')->getLanding($list, Session::get('history_' . $list));
            $name = studly_case(str_replace('landing', '', strtolower($list)));
            $change = App::make('StatsManager')->evalChange($history_landing->sessions, $landing->sessions);

            $temp = array(
                'name' => $name,
                'sessions' => $landing->sessions,
                'change' => $change,
                'percent' => App::make('Helper')->formatDecimal($landing->sessions / $totalSessions * 100) ,
            );
            $sum+= $landing->sessions;
            $history_sum+= $history_landing->sessions;
            $output->push((object)$temp);
        }
        // Calculate other
        $history_total_sessions = $this->getTotalSessions('Device', Session::get('history_device_data_set_id'));
        $other_sessions = $totalSessions - $sum;
        $history_other_sessions = $history_total_sessions - $history_sum;
        $change = App::make('StatsManager')->evalChange($history_other_sessions, $other_sessions);

        $name = 'Other';
        $temp = array(
            'name' => $name,
            'sessions' => $other_sessions,
            'change' => $change,
            'percent' => App::make('Helper')->formatDecimal($other_sessions / $totalSessions * 100) ,
        );
        $output->push((object)$temp);
        $output = $output->sortByDesc('sessions');
        return $output;
    }
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

    public function getSessions($modelName, $name, $dataset_id) {
        return $modelName::where('dataset_id', '=', $dataset_id)->whereName($name)->sum('sessions');
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
