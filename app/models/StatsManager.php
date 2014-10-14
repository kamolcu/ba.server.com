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
        return App::make('Helper')->formatDecimal($diff / $val1 * 100);
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
        $result['percent'] = $this->getPercentChange($val1, $val2);
        return $result;
    }
}
