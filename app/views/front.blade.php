@extends('layout.default')
<?php
    $default_days_buffer = Config::get('config.days-buffer', 7);
?>
@section('content')
    <div class="row text-center head">
    {{ Form::open(array('route' => 'compare', 'class' => 'form-horizontal', 'files' => false, 'id' => 'form_compare')) }}
        <div class="col-xs-12 col-sm-12 col-md-5 col-md-offset-1 panel panel-info">
            <h3 class="panel-heading"><span class="glyphicon glyphicon glyphicon-star">&nbsp;{{ Config::get('config.main_range')}}</h3>
            <dl class="dl-horizontal">
                <dt>{{ Form::label('main_start', Config::get('config.start_date'), array('class' => 'left-label') ) }}:</dt>
                <dd>{{ Form::text('main_start', App::make('Helper')->getDefaultStartDate()->toDateString(), array('id' => 'main_start', 'class' => 'form-control left-label', 'placeholder' => 'yyyy-mm-dd', 'readonly') ) }}</dd>
            </dl>
            <dl class="dl-horizontal">
                <dt>{{ Form::label('main_end', Config::get('config.end_date'), array('class' => 'left-label') ) }}:</dt>
                <dd>{{ Form::text('main_end', App::make('Helper')->getDefaultEndDate()->toDateString(), array('id' => 'main_end', 'class' => 'form-control left-label', 'placeholder' => 'yyyy-mm-dd', 'readonly') ) }}</dd>
            </dl>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-5 panel panel-info">
            <h3 class="panel-heading"><span class="glyphicon glyphicon glyphicon-time">&nbsp;{{ Config::get('config.historical_range')}} (historical data)</h3>
            <dl class="dl-horizontal">
                <dt>{{ Form::label('history_start', Config::get('config.start_date'), array('class' => 'left-label') ) }}:</dt>
                <dd>{{ Form::text('history_start', App::make('Helper')->getDefaultHistoryStartDate()->toDateString(), array('id' => 'history_start', 'class' => 'form-control left-label', 'placeholder' => 'yyyy-mm-dd', 'readonly') ) }}</dd>
            </dl>
            <dl class="dl-horizontal">
                <dt>{{ Form::label('history_end', Config::get('config.end_date'), array('class' => 'left-label') ) }}:</dt>
                <dd>{{ Form::text('history_end', App::make('Helper')->getDefaultHistoryEndDate()->toDateString(), array('id' => 'history_end', 'class' => 'form-control left-label', 'placeholder' => 'yyyy-mm-dd', 'readonly') ) }}</dd>
            </dl>
        </div>
    </div>
    {{ Form::close() }}
    <div class="row text-center">
        <button class="btn btn-primary" onclick="return compare();">เปรียบเทียบ</button>
    </div>
@stop

@section('page-js-script')
<link rel="stylesheet" href="{{{ asset('css/datepicker3.css') }}}">
<script src="{{{ asset('js/bootstrap-datepicker.js') }}}"></script>
<script src="{{{ asset('js/bootstrap-datepicker-thai.js') }}}" charset="UTF-8"></script>
<script src="{{{ asset('js/locales/bootstrap-datepicker.th.js') }}}" charset="UTF-8"></script>
<script type="text/javascript">
    function compare(){
        // Add precondition here
        $('#form_compare').submit();
    }
    function addDays(theDate, days) {
        return new Date(theDate.getTime() + days*24*60*60*1000);
    }
    function formatTwoDigits(input){
        return ("0" + input).slice(-2);
    }

    $('#main_start').bind('change', function(){
        //console.log($(this).val());
        var myRegex = /([0-9]{4})-([0-9]{2})-([0-9]{2})/;
        var match = myRegex.exec($(this).val());
        var year = match[1];
        var month = match[2];
        var day = match[3];
        var dd = new Date(year, month - 1, day, 0, 0, 0, 0);
        var newDate = addDays(dd, {{ $default_days_buffer }});
        $('#main_end').val(newDate.getFullYear() + '-' + formatTwoDigits(newDate.getMonth() + 1) + '-' + formatTwoDigits(newDate.getDate()));
    });

    $('#history_start').bind('change', function(){
        //console.log($(this).val());
        var myRegex = /([0-9]{4})-([0-9]{2})-([0-9]{2})/;
        var match = myRegex.exec($(this).val());
        var year = match[1];
        var month = match[2];
        var day = match[3];
        var dd = new Date(year, month - 1, day, 0, 0, 0, 0);
        var newDate = addDays(dd, {{ $default_days_buffer }});
        $('#history_end').val(newDate.getFullYear() + '-' + formatTwoDigits(newDate.getMonth() + 1) + '-' + formatTwoDigits(newDate.getDate()));
    });

    $('#main_start').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true
    });

    $('#main_end').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true
    });

    $('#history_start').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true
    });

    $('#history_end').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        todayHighlight: true
    });
</script>
@stop