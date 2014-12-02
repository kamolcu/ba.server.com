@extends('layout.default')

<?php

    $header = App::make('ReportManager')->getReportHeader();
    // Devices
    $devices = App::make('DeviceManager')->getDevicesList(Session::get('device_data_set_id'));
    $results = array();
    foreach($devices as $device){
        $results[$device->name] = App::make('ReportManager')->getDeviceStats($device->name);
    }
    $devices = (object)$results;
    $total_device_sessions = App::make('ReportManager')->getTotalSessions('Device', Session::get('device_data_set_id'));
    $total_history_device_sessions = App::make('ReportManager')->getTotalSessions('Device', Session::get('history_device_data_set_id'));
    // ========

    // Channels
    $channels = App::make('ReportManager')->getChannelStats();
    $total_channels = App::make('ReportManager')->getTotalSessions('Channel', Session::get('channel_data_set_id'));
    // ========

    // Product
    $product_sessions = App::make('ReportManager')->getSessions('GoalFunnel', 'Product Detail',Session::get('product_funnel_id'));
    $history_product_sessions = App::make('ReportManager')->getSessions('GoalFunnel', 'Product Detail',Session::get('history_product_funnel_id'));
    $productPageChange = App::make('StatsManager')->evalChange($history_product_sessions, $product_sessions);
    $msg = sprintf('$productPageChange = %s', print_r($productPageChange, true));
    Log::debug($msg);
    // ========

    // Checkout
    $checkout = App::make('ReportManager')->getSessions('GoalFunnel', 'Login',Session::get('product_funnel_id'));
    $history_checkout = App::make('ReportManager')->getSessions('GoalFunnel', 'Login',Session::get('history_product_funnel_id'));
    $checkout_change = App::make('StatsManager')->evalChange($history_checkout, $checkout);
    $msg = sprintf('$checkout_change = %s', print_r($checkout_change, true));
    Log::debug($msg);

    $checkout_conversion = App::make('StatsManager')->getConversionRate($total_device_sessions, $checkout);
    $history_checkout_conversion = App::make('StatsManager')->getConversionRate($total_history_device_sessions, $history_checkout);
    $checkout_conversion_change = App::make('StatsManager')->evalChangePercent($history_checkout_conversion, $checkout_conversion);
    // ========

    // Landing Page
    $landing_page_change = App::make('StatsManager')->evalChange($total_history_device_sessions, $total_device_sessions);
    $msg = sprintf('$landing_page_change = %s', print_r($landing_page_change, true));
    Log::debug($msg);

    $landing_conversion = App::make('StatsManager')->getConversionRate($total_device_sessions, $product_sessions);
    $history_landing_conversion = App::make('StatsManager')->getConversionRate($total_history_device_sessions, $history_product_sessions);
    $landing_conversion_change = App::make('StatsManager')->evalChangePercent($history_landing_conversion, $landing_conversion);
    $landing_stats = App::make('ReportManager')->getLandingStats();
    // ========

    $cart_abandon = App::make('StatsManager')->evalChange($product_sessions, $checkout);
    $msg = sprintf('$cart_abandon %s', print_r($cart_abandon, true));
    Log::debug($msg);

    $history_cart_abandon = App::make('StatsManager')->evalChange($history_product_sessions, $history_checkout);
    $msg = sprintf('$history_cart_abandon %s', print_r($history_cart_abandon, true));
    Log::debug($msg);

    $cart_abandon_change = App::make('StatsManager')->evalChangePercent($history_cart_abandon['percent'], $cart_abandon['percent']);

    // Completed order data from PCMS
    $completed_orders_stats = App::make('ReportManager')->getCompletedOrderStats();
    $completed_orders = App::make('Helper')->getCompletedOrders(Session::get('completed_order_id'))->sum('count');
    $complete_orders_history = App::make('Helper')->getCompletedOrders(Session::get('history_completed_order_id'))->sum('count');

    $checkout_no_order = App::make('StatsManager')->evalChange($checkout, $completed_orders);
    $msg = sprintf('$checkout_no_order %s', print_r($checkout_no_order, true));
    Log::debug($msg);

    $complete_orders_change = App::make('StatsManager')->evalChange($complete_orders_history, $completed_orders);
    $msg = sprintf('$complete_orders_change %s', print_r($complete_orders_change, true));
    Log::debug($msg);

    $paid_orders_stats = App::make('ReportManager')->getPaidOrderStats();
    $paid_orders = App::make('Helper')->getPaidOrders(Session::get('paid_order_id'))->sum('count');
    $paid_orders_history = App::make('Helper')->getPaidOrders(Session::get('history_paid_order_id'))->sum('count');
    $payment_success = App::make('StatsManager')->evalChange($completed_orders, $paid_orders);
    $msg = sprintf('$payment_success %s', print_r($payment_success, true));
    Log::debug($msg);

    $payment_success_history = App::make('StatsManager')->evalChange($complete_orders_history, $paid_orders_history);
    $msg = sprintf('$payment_success_history %s', print_r($payment_success_history, true));
    Log::debug($msg);

    $paid_orders_change = App::make('StatsManager')->evalChange($paid_orders_history, $paid_orders);
    $msg = sprintf('$paid_orders_change %s', print_r($paid_orders_change, true));
    Log::debug($msg);

    $payment_success_change = App::make('StatsManager')->evalChangePercent($payment_success_history['percent'], $payment_success['percent']);

    $history_completed_order_conversion = App::make('StatsManager')->getConversionRate($total_history_device_sessions, $complete_orders_history);
    $completed_order_conversion = App::make('StatsManager')->getConversionRate($total_device_sessions, $completed_orders);
    $completed_order_conversion_change = App::make('StatsManager')->evalChangePercent($history_completed_order_conversion, $completed_order_conversion);

    $paid_order_conversion = App::make('StatsManager')->getConversionRate($total_device_sessions, $paid_orders);
    $history_paid_order_conversion = App::make('StatsManager')->getConversionRate($total_history_device_sessions, $paid_orders_history);
    $paid_order_conversion_change = App::make('StatsManager')->evalChangePercent($history_paid_order_conversion, $paid_order_conversion);
    // ========
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
            ?>
            @foreach($channels as $channel)
                <?php
                    $channel = (object)$channel;
                    $sign = '';
                ?>
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
            @if($landing_page_change['momentum'] == 1)
                <?php $sign = 'up'; ?>
                <img class="landing_page_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @elseif($landing_page_change['momentum'] == -1)
                <?php $sign = 'down'; ?>
                <img class="up_side_down landing_page_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
            @endif
            <div class="landing_page_change {{ $sign }}">{{ App::make('Helper')->formatPercent($landing_page_change['percent']) }}</div>

            <?php
                $counter = 0;
            ?>
            @foreach($landing_stats as $landing)
                <?php
                    $sign = '';
                 ?>
                <div class="landing_stat_container_{{ $counter }}">
                    <div class="ld_name text-left">{{ ($counter + 1) . '.' . $landing->name }}</div>
                    <div class="ld_session">{{ App::make('Helper')->formatInteger($landing->sessions) }}</div>
                    <div class="ld_percent">{{ '(' . App::make('Helper')->formatPercent($landing->percent) . ')' }}</div>
                    @if($landing->change['momentum'] == 1)
                        <?php $sign = 'up'; ?>
                        <img class="ld_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
                    @elseif($landing->change['momentum'] == -1)
                        <?php $sign = 'down'; ?>
                        <img class="up_side_down ld_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
                    @endif
                    <div class="ld_change {{ $sign }}">{{ App::make('Helper')->formatPercent($landing->change['percent']) }}</div>
                </div>
                <?php $counter++; ?>
            @endforeach
            {{-- Landing End --}}

            {{-- Product --}}
            <?php
                $sign = '';
            ?>
            <div class="product_page_sessions" title="History = {{ $history_product_sessions }}">{{ App::make('Helper')->formatInteger($product_sessions) }}</div>
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
            <div class="checkout_sessions" title="History = {{ $history_checkout }}" >{{ App::make('Helper')->formatInteger($checkout) }}</div>
            @if($checkout_change['momentum'] == 1)
                <?php $sign = 'up'; ?>
                <img class="checkout_page_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @elseif($checkout_change['momentum'] == -1)
                <?php $sign = 'down'; ?>
                <img class="up_side_down checkout_page_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
            @endif
            <div class="checkout_page_change {{ $sign }}">{{ App::make('Helper')->formatPercent($checkout_change['percent']) }}</div>

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
                <img class="cart_aban_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
            @elseif($cart_abandon_change['momentum'] == -1)
                <?php $sign = 'down_good'; ?>
                <img class="up_side_down cart_aban_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @endif
            <div class="cart_aban_change {{ $sign }}">{{ App::make('Helper')->formatPercent($cart_abandon_change['diff']) }}</div>
            {{-- Checkout End --}}

            {{-- Completed Orders --}}
            <div class="complete_order">{{ App::make('Helper')->formatInteger($completed_orders) }}</div>
            <?php
                $sign = '';
            ?>
            @if($complete_orders_change['momentum'] == 1)
                <?php $sign = 'up'; ?>
                <img class="complete_order_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @elseif($complete_orders_change['momentum'] == -1)
                <?php $sign = 'down'; ?>
                <img class="up_side_down complete_order_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
            @endif
            <div class="complete_order_change {{ $sign }}">{{ App::make('Helper')->formatPercent($complete_orders_change['percent']) }}</div>

            <div class="checkout_no_order_rate">{{ App::make('Helper')->formatPercent($checkout_no_order['percent']) }}</div>
            <?php
                $sign = '';
            ?>
            @if($cart_abandon_change['momentum'] == 1)
                <?php $sign = 'up_bad'; ?>
                <img class="checkout_no_order_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
            @elseif($cart_abandon_change['momentum'] == -1)
                <?php $sign = 'down_good'; ?>
                <img class="up_side_down checkout_no_order_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @endif
            <div class="checkout_no_order {{ $sign }}">{{ App::make('Helper')->formatPercent($cart_abandon_change['diff']) }}</div>
            <div class="completed_order_conversion">{{ App::make('Helper')->formatPercent($completed_order_conversion) }}</div>
            <?php
                $sign = '';
            ?>
            @if($completed_order_conversion_change['momentum'] == 1)
                <?php $sign = 'up'; ?>
                <img class="completed_order_conv_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @elseif($completed_order_conversion_change['momentum'] == -1)
                <?php $sign = 'down'; ?>
                <img class="up_side_down completed_order_conv_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
            @endif
            <div class="completed_order_conv_change {{ $sign }}">{{ App::make('Helper')->formatPercent($completed_order_conversion_change['diff']) }}</div>
            <?php
                $counter = 0;
             ?>
            @foreach($completed_orders_stats as $row)
                <?php
                    $sign = '';
                 ?>
                <div class="completed_container_{{ $counter }}">
                    <div class="pcms_name text-left pcms_orders">{{ ($counter + 1) . '.' . $row->name }}</div>
                    <div class="pcms_session pcms_orders">{{ App::make('Helper')->formatInteger($row->count) }}</div>
                    <div class="pcms_percent pcms_orders">{{ '(' . App::make('Helper')->formatPercent($row->percent) . ')' }}</div>
                    @if($row->change['momentum'] == 1)
                        <?php $sign = 'up'; ?>
                        <img class="pcms_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
                    @elseif($row->change['momentum'] == -1)
                        <?php $sign = 'down'; ?>
                        <img class="up_side_down pcms_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
                    @endif
                    <div class="pcms_change {{ $sign }} pcms_orders">{{ App::make('Helper')->formatPercent($row->change['percent']) }}</div>
                </div>
                <?php $counter++; ?>
            @endforeach
            {{-- Completed Orders End --}}

            {{-- Paid Orders --}}
            <div class="paid_orders">{{ App::make('Helper')->formatInteger($paid_orders) }}</div>
            <?php
                $sign = '';
            ?>
            @if($paid_orders_change['momentum'] == 1)
                <?php $sign = 'up'; ?>
                <img class="paid_orders_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @elseif($paid_orders_change['momentum'] == -1)
                <?php $sign = 'down'; ?>
                <img class="up_side_down paid_orders_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
            @endif
            <div class="paid_orders_change {{ $sign }}">{{ App::make('Helper')->formatPercent($paid_orders_change['percent']) }}</div>

            <div class="payment_success_rate">{{ App::make('Helper')->formatPercent($payment_success['percent']) }}</div>
            <?php
                $sign = '';
            ?>
            @if($payment_success_change['momentum'] == 1)
                <?php $sign = 'up'; ?>
                <img class="payment_success_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @elseif($payment_success_change['momentum'] == -1)
                <?php $sign = 'down'; ?>
                <img class="up_side_down payment_success_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
            @endif
            <div class="payment_success {{ $sign }}">{{ App::make('Helper')->formatPercent($payment_success_change['diff']) }}</div>
            <div class="paid_orders_conversion">{{ App::make('Helper')->formatPercent($paid_order_conversion) }}</div>

            <?php
                $sign = '';
            ?>
            @if($paid_order_conversion_change['momentum'] == 1)
                <?php $sign = 'up'; ?>
                <img class="paid_order_conv_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
            @elseif($paid_order_conversion_change['momentum'] == -1)
                <?php $sign = 'down'; ?>
                <img class="up_side_down paid_order_conv_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
            @endif
            <div class="paid_order_conv_change {{ $sign }}">{{ App::make('Helper')->formatPercent($paid_order_conversion_change['diff']) }}</div>

            <?php
                $counter = 0;
             ?>
            @foreach($paid_orders_stats as $row)
                <?php
                    $sign = '';
                 ?>
                <div class="paid_container_{{ $counter }}">
                    <div class="pcms_name text-left pcms_orders">{{ ($counter + 1) . '.' . $row->name }}</div>
                    <div class="pcms_session pcms_orders">{{ App::make('Helper')->formatInteger($row->count) }}</div>
                    <div class="pcms_percent pcms_orders">{{ '(' . App::make('Helper')->formatPercent($row->percent) . ')' }}</div>
                    @if(isset($row->change['momentum']) && $row->change['momentum'] == 1)
                        <?php $sign = 'up'; ?>
                        <img class="pcms_sign" alt="" height="15" src="{{ URL::to('/images/up_green_arrow.png') }}">
                    @elseif(isset($row->change['momentum']) && $row->change['momentum'] == -1)
                        <?php $sign = 'down'; ?>
                        <img class="up_side_down pcms_sign" alt="" height="15" src="{{ URL::to('/images/up_red_arrow.png') }}">
                    @endif
                    @if(isset($row->change['percent']))
                    <div class="pcms_change {{ $sign }} pcms_orders">{{ App::make('Helper')->formatPercent($row->change['percent']) }}</div>
                    @endif
                </div>
                <?php $counter++; ?>
            @endforeach
            {{-- Paid Orders End --}}

        </div>
    </div>
@stop

@section('page-js-script')
@stop