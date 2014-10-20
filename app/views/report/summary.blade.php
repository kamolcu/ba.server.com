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
            <div class="total_device_sessions">{{ number_format($total_device_sessions, 0, '.', ',') }}</div>

        </div>
    </div>
@stop

@section('page-js-script')
@stop