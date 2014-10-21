@extends('layout.default')

<?php
    // Session::put('main_start', $main_start);
    //     Session::put('main_end', $main_end);
    //     Session::put('history_start', $history_start);
    //     Session::put('history_end', $history_end);
    $header = App::make('ReportManager')->getReportHeader();
    $devices = App::make('DeviceManager')->getDevicesList(Session::get('device_data_set_id'));
    $results = array();
    foreach($devices as $device){
        $results[$device->name] = App::make('ReportManager')->getDeviceStats($device->name);
    }
    $devices = (object)$results;
    $total_device_sessions = App::make('ReportManager')->getTotalSessions('Device', Session::get('device_data_set_id'));
    $totalHistorySessions = App::make('ReportManager')->getTotalSessions('Device', Session::get('history_device_data_set_id'));
    $LandingPageChange = App::make('StatsManager')->evalChange($totalHistorySessions, $total_device_sessions);

    $channels = App::make('ReportManager')->getChannelStats();
    $total_channels = App::make('ReportManager')->getTotalSessions('Channel', Session::get('channel_data_set_id'));

    $product_sessions = App::make('ReportManager')->getSessions('GoalFunnel', 'Product Detail',Session::get('product_funnel_id'));
    $history_product_sessions = App::make('ReportManager')->getSessions('GoalFunnel', 'Product Detail',Session::get('history_product_funnel_id'));
    $productPageChange = App::make('StatsManager')->evalChange($history_product_sessions, $product_sessions);
    // Session::put('product_data_set_id', $product->id);
    // Session::put('history_product_data_set_id', $history_product->id);
    $checkout = App::make('ReportManager')->getSessions('GoalFunnel', 'Login',Session::get('product_funnel_id'));
    $history_checkout = App::make('ReportManager')->getSessions('GoalFunnel', 'Login',Session::get('history_product_funnel_id'));
    $checkoutChange = App::make('StatsManager')->evalChange($history_checkout, $checkout);

    $landing_conversion = App::make('StatsManager')->getConversionRate($total_device_sessions, $product_sessions);

    $history_landing_conversion = App::make('StatsManager')->getConversionRate($totalHistorySessions, $history_product_sessions);
    $landing_conversion_change = App::make('StatsManager')->evalChangePercent($history_landing_conversion, $landing_conversion);

    $checkout_conversion = App::make('StatsManager')->getConversionRate($total_device_sessions, $checkout);
    $history_checkout_conversion = App::make('StatsManager')->getConversionRate($totalHistorySessions, $history_checkout);
    $checkout_conversion_change = App::make('StatsManager')->evalChangePercent($history_checkout_conversion, $checkout_conversion);

    $cart_abandon = App::make('StatsManager')->evalChange($product_sessions, $checkout);
    $history_cart_abandon = App::make('StatsManager')->evalChange($history_product_sessions, $history_checkout);
    $cart_abandon_change = App::make('StatsManager')->evalChangePercent($history_cart_abandon['percent'], $cart_abandon['percent']);
?>
@section('content')
    <div class="row text-center">
        <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="report_header">{{$header}}</div>
        <div class="img_container">
            <img alt="funnel_bg" width="100%" src="{{ URL::to('/images/Funnel_bg.png') }}" />

            {{-- Device --}}
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
            {{-- Device End --}}

            {{-- Channel --}}
            <?php
                $index = 0;
                $sign = '';
            ?>
            @foreach($channels as $channel)
            <?php $channel = (object)$channel; ?>
                <div class="channel_name_{{ $index }}">{{ $channel->name }}</div>
                <div class="channel_sessions_percent_{{ $index }}">{{ '('.App::make('Helper')->formatPercent($channel->sessions / $total_channels * 100).')' }}</div>
                <div class="channel_sessions_{{ $index }}">{{ App::make('Helper')->formatInteger($channel->sessions) }}</div>
                @if(property_exists($channel, 'change') && $channel->change['momentum'] == 1)
                    <?php $sign = 'up'; ?>
                    <img class="channel_sign_{{ $index }}" alt="{{ $sign }}" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
                    <div class="channel_change_{{ $index }} {{ $sign }}" >{{ $channel->change['percent'] }}</div>
                @elseif(property_exists($channel, 'change') && $channel->change['momentum'] == -1)
                    <?php $sign = 'down'; ?>
                    <img class="up_side_down channel_sign_{{ $index }}" alt="{{ $sign }}" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
                    <div class="channel_change_{{ $index }} {{ $sign }}" >{{ $channel->change['percent'] }}</div>
                @endif
            <?php $index++; ?>
            @endforeach
            {{-- Channel End --}}

            {{-- Landing --}}
            <?php
                $sign = '';
            ?>
            <div class="landing_page_sessions">{{ App::make('Helper')->formatInteger($total_device_sessions) }}</div>
            <div class="landing_conversion">{{ App::make('Helper')->formatPercent($landing_conversion) }}</div>
            @if($LandingPageChange['momentum'] == 1)
                <?php $sign = 'up'; ?>
                <img class="landing_page_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @elseif($LandingPageChange['momentum'] == -1)
                <?php $sign = 'down'; ?>
                <img class="up_side_down landing_page_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
            @endif
            <div class="landing_page_change {{ $sign }}">{{ App::make('Helper')->formatPercent($LandingPageChange['percent']) }}</div>
            {{-- Landing End --}}

            {{-- Product --}}
            <div class="product_page_sessions">{{ App::make('Helper')->formatInteger($product_sessions) }}</div>
            @if($productPageChange['momentum'] == 1)
                <?php $sign = 'up'; ?>
                <img class="product_page_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @elseif($productPageChange['momentum'] == -1)
                <?php $sign = 'down'; ?>
                <img class="up_side_down product_page_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
            @endif
            <div class="product_page_change {{ $sign }}">{{ App::make('Helper')->formatPercent($productPageChange['percent']) }}</div>

            <?php
                $sign = '';
            ?>
            @if($landing_conversion_change['momentum'] == 1)
                <?php $sign = 'up'; ?>
                <img class="landing_conv_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @elseif($landing_conversion_change['momentum'] == -1)
                <?php $sign = 'down'; ?>
                <img class="up_side_down landing_conv_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
            @endif
            <div class="landing_conv_change {{ $sign }}">{{ App::make('Helper')->formatPercent($landing_conversion_change['diff']) }}</div>
            {{-- Product End --}}

            {{-- Checkout --}}
            <?php
                $sign = '';
            ?>
            <div class="checkout_sessions">{{ App::make('Helper')->formatInteger($checkout) }}</div>
            @if($checkoutChange['momentum'] == 1)
                <?php $sign = 'up'; ?>
                <img class="checkout_page_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @elseif($checkoutChange['momentum'] == -1)
                <?php $sign = 'down'; ?>
                <img class="up_side_down checkout_page_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
            @endif
            <div class="checkout_page_change {{ $sign }}">{{ App::make('Helper')->formatPercent($checkoutChange['percent']) }}</div>


            <?php
                $sign = '';
            ?>
            <div class="checkout_conversion">{{ App::make('Helper')->formatPercent($checkout_conversion) }}</div>
            <div class="cart_abandon_rate">{{ App::make('Helper')->formatPercent($cart_abandon['percent']) }}</div>
            @if($checkout_conversion_change['momentum'] == 1)
                <?php $sign = 'up'; ?>
                <img class="checkout_conv_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @elseif($checkout_conversion_change['momentum'] == -1)
                <?php $sign = 'down'; ?>
                <img class="up_side_down checkout_conv_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
            @endif
            <div class="checkout_conv_change {{ $sign }}">{{ App::make('Helper')->formatPercent($checkout_conversion_change['diff']) }}</div>

            <?php
                $sign = '';
            ?>
            @if($cart_abandon_change['momentum'] == 1)
                <?php $sign = 'up_bad'; ?>
                <img class="cart_aban_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png.png') }}">
            @elseif($cart_abandon_change['momentum'] == -1)
                <?php $sign = 'down_good'; ?>
                <img class="up_side_down cart_aban_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @endif
            <div class="cart_aban_change {{ $sign }}">{{ App::make('Helper')->formatPercent($cart_abandon_change['diff']) }}</div>

            {{-- Checkout End --}}

            {{-- Completed Orders --}}
            {{-- Completed Orders End --}}

            {{-- Paid Orders --}}
            {{-- Paid Orders End --}}

        </div>
    </div>
@stop

@section('page-js-script')
@stop