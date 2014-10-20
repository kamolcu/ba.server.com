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
    //sd($devices);
?>
@section('content')
    <div class="row text-center">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <img alt="funnel_bg" width="1105" height="800" src="{{ URL::to('/images/Funnel_bg.png') }}" />

            <?php $counter = 0; ?>
            @foreach($devices as $device)
                <?php
                    $counter++;
                    $device = (object)$device;
                ?>
                <div class="device_name_left device_name_{{ $counter }}">{{ studly_case($device->name) }}</div>
                <div class=""></div>
            @endforeach
        </div>
    </div>
@stop

@section('page-js-script')
@stop