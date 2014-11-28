<?php
use Carbon\Carbon;

class HelperTest extends TestCase
{
    public function testGetDefaultStartDate() {
        // Thursday --> have to answer 2014-09-29 as Monday
        $date = App::make('Helper')->getDefaultStartDate(Carbon::createFromDate(2014, 10, 2));
        $this->assertSame('2014-09-29', $date->toDateString());

        $date = App::make('Helper')->getDefaultStartDate(Carbon::createFromDate(2014, 9, 29));
        $this->assertSame('2014-09-22', $date->toDateString());
    }

    public function testGetDefaultEndDate() {
        $date = App::make('Helper')->getDefaultEndDate(Carbon::createFromDate(2014, 10, 2));
        $this->assertSame('2014-10-01', $date->toDateString());

        $date = App::make('Helper')->getDefaultEndDate(Carbon::createFromDate(2014, 9, 29));
        $this->assertSame('2014-09-28', $date->toDateString());
    }
}
