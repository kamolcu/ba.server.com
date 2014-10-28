<?php
class StatsManager
{
    public function getConversionRate($phase1, $phase2) {
        if ($phase1 == 0) throw new \InvalidArgumentException('Divided by zero.');
        return App::make('Helper')->formatDecimal((1 - ($phase1 - $phase2) / $phase1) * 100);
    }
    public function getPercentChange($val1, $val2) {
        if ($val1 == 0) throw new \InvalidArgumentException('Divided by zero.');
        $diff = abs($val1 - $val2);
        return App::make('Helper')->formatDecimal($diff / $val1 * 100, '');
    }
    public function evalChange($val1, $val2) {
        $result = array();
        if ($val1 == $val2) {
            $result['momentum'] = 0;
        } elseif ($val1 < $val2) {
            $result['momentum'] = 1;
        } else {
            $result['momentum'] = - 1;
        }

        try {
            $result['percent'] = $this->getPercentChange($val1, $val2);
        }
        catch(Exception $ex) {
            $msg = sprintf('evalChange() exception =  %s, v1 = %s, v2 = %s.', $ex->getMessage(), $val1, $val2);
            Log::error($msg);
            $result = array(
                'momentum' => 0,
                'percent' => 0,
            );
        }

        return $result;
    }
    public function evalChangePercent($percent1, $percent2) {
        $result = array();
        if ($percent1 == $percent2) {
            $result['momentum'] = 0;
        } elseif ($percent1 < $percent2) {
            $result['momentum'] = 1;
        } else {
            $result['momentum'] = - 1;
        }
        $result['diff'] = abs($percent1 - $percent2);
        return $result;
    }
}
