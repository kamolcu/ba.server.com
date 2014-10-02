<?php
use Carbon\Carbon;

class Helper
{
    public function getDefaultStartDate($now) {
        if (empty($now)) $now = Carbon::now();
        // Get nearest Monday of the current week
        if ($now->dayOfWeek == Carbon::MONDAY) {
            $now->subDays(7);
        } else {
            while ($now->dayOfWeek != Carbon::MONDAY) {
                $now->subDays(1);
            }
        }
        return $now;
    }

    public function getDefaultEndDate($now) {
        if (empty($now)) $now = Carbon::now();
        $now->subDays(1);
        return $now;
    }
}
