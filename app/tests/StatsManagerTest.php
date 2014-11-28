<?php
class StatsManagerTest extends TestCase
{
    public function testEvalChange() {

        $result = App::make('StatsManager')->evalChange(1, 0);
        $this->assertSame('100.00', $result['percent']);
        $this->assertSame(-1, $result['momentum']);

        $result = App::make('StatsManager')->evalChange(1, 1);
        $this->assertSame('0.00', $result['percent']);
        $this->assertSame(0, $result['momentum']);

        $result = App::make('StatsManager')->evalChange(207560, 569);
        $this->assertSame('99.73', $result['percent']);
        $this->assertSame(-1, $result['momentum']);
    }
    public function testGetPercentChange() {
        $result = App::make('StatsManager')->getPercentChange(1, 0);
        $this->assertSame('100.00', $result);

        $result = App::make('StatsManager')->getPercentChange(1, 1);
        $this->assertSame('0.00', $result);

        $result = App::make('StatsManager')->getPercentChange(207560, 569);
        $this->assertSame('99.73', $result);

        $result = App::make('StatsManager')->getPercentChange(569, 207560);
        $this->assertSame('36378.03', $result);
    }
    public function testGetConversionRate() {
        $result = App::make('StatsManager')->getConversionRate(1, 0);
        $this->assertSame('0.00', $result);

        $result = App::make('StatsManager')->getConversionRate(1, 1);
        $this->assertSame('100.00', $result);

        $result = App::make('StatsManager')->getConversionRate(100, 22);
        $this->assertSame('22.00', $result);

        $result = App::make('StatsManager')->getConversionRate(207560, 109905);
        $this->assertSame('52.95', $result);

        $result = App::make('StatsManager')->getConversionRate(207560, 569);
        $this->assertSame('0.27', $result);
    }

    public function testGetConversionRateInvalid() {
        try {
            App::make('StatsManager')->getConversionRate(0, 10);
        }
        catch(\InvalidArgumentException $ex) {
            $this->assertEquals('Divided by zero.', $ex->getMessage());
            return;
        }
        $this->fails('Unexpected code reached');
    }
}
