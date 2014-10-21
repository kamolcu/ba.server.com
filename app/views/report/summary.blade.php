@extends('layout.default')

<?php
    // Session::put('main_start', $main_start);
    //     Session::put('main_end', $main_end);
    //     Session::put('history_start', $history_start);
    //     Session::put('history_end', $history_end);
    $devices = App::make('DeviceManager')->getDevicesList(Session::get('device_data_set_id'));
    $results = array();
    foreach($devices as $device){
        $results[$device->name] = App::make('ReportManager')->getDeviceStats($device->name, Session::get('main_start'), Session::get('main_end'), Session::get('history_start'), Session::get('history_end'));
    }
    $devices = (object)$results;
    $total_device_sessions = App::make('ReportManager')->getDeviceAllSessionsCount(Session::get('main_start'), Session::get('main_end'));
?>
@section('content')
    <div class="row text-center">
        <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="img_container">
            <img alt="funnel_bg" width="100%" src="{{ URL::to('/images/Funnel_bg.png') }}" />
            <div class="device_name_desktop">{{ studly_case($devices->desktop['name']) }}</div>
            <div class="device_name_left device_name_mobile">{{ studly_case($devices->mobile['name']) }}</div>
            <div class="device_name_left device_name_tablet">{{ studly_case($devices->tablet['name']) }}</div>
            <div class="total_device_sessions">{{ App::make('Helper')->formatInteger($total_device_sessions) }}</div>
            @if($devices->desktop['change']['momentum'] == 1)
                <?php $desktop_sign = 'up'; ?>
                <img class="desktop_sign" alt="up" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @else
                <?php $desktop_sign = 'down'; ?>
                <img class="up_side_down desktop_sign" alt="down" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
            @endif
            @if($devices->mobile['change']['momentum'] == 1)
                <?php $mobile_sign = 'up'; ?>
                <img class="mobile_sign" alt="up" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @else
                <?php $mobile_sign = 'down'; ?>
                <img class="up_side_down mobile_sign" alt="down" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
            @endif
            @if($devices->tablet['change']['momentum'] == 1)
                <?php $tablet_sign = 'up'; ?>
                <img class="tablet_sign" alt="up" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @else
                <?php $tablet_sign = 'down'; ?>
                <img class="up_side_down tablet_sign" alt="down" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
            @endif
            <div class="desktop_percent_change {{ $desktop_sign }}">{{ App::make('Helper')->formatPercent($devices->desktop['change']['percent']) }}</div>
            <div class="mobile_percent_change {{ $mobile_sign }}">{{ App::make('Helper')->formatPercent($devices->mobile['change']['percent']) }}</div>
            <div class="tablet_percent_change {{ $tablet_sign }}">{{ App::make('Helper')->formatPercent($devices->tablet['change']['percent']) }}</div>

            <div class="desktop_sessions">{{ App::make('Helper')->formatInteger($devices->desktop['sessions']) }}</div>
            <div class="mobile_sessions">{{ App::make('Helper')->formatInteger($devices->mobile['sessions']) }}</div>
            <div class="tablet_sessions">{{ App::make('Helper')->formatInteger($devices->tablet['sessions']) }}</div>

            <div class="desktop_percent">{{ App::make('Helper')->formatPercent($devices->desktop['percent']) }}</div>
            <div class="mobile_percent">{{ App::make('Helper')->formatPercent($devices->mobile['percent']) }}</div>
            <div class="tablet_percent">{{ App::make('Helper')->formatPercent($devices->tablet['percent']) }}</div>

            <div class="desktop_bounce">{{ App::make('Helper')->formatPercent($devices->desktop['bounce_rate']) }}</div>
            <div class="mobile_bounce">{{ App::make('Helper')->formatPercent($devices->mobile['bounce_rate']) }}</div>
            <div class="tablet_bounce">{{ App::make('Helper')->formatPercent($devices->tablet['bounce_rate']) }}</div>

            <div class="desktop_conv">{{ App::make('Helper')->formatPercent($devices->desktop['conversion_rate']) }}</div>
            <div class="mobile_conv">{{ App::make('Helper')->formatPercent($devices->mobile['conversion_rate']) }}</div>
            <div class="tablet_conv">{{ App::make('Helper')->formatPercent($devices->tablet['conversion_rate']) }}</div>

            <div class="">{{ App::make('Helper')->test($devices->tablet['conversion_rate']) }}</div>


        </div>
    </div>
@stop

@section('page-js-script')
@stop