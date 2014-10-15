@extends('layout.default')

@section('content')
    <div class="row text-center head">
    {{ Form::open(array('route' => 'compare', 'class' => 'form-horizontal', 'files' => false, 'id' => 'form_compare')) }}
        <div class="col-xs-12 col-sm-12 col-md-5 col-md-offset-1 panel panel-info">
            <h3 class="panel-heading"><span class="glyphicon glyphicon glyphicon-star">&nbsp;ช่วงเวลาหลัก</h3>
            <dl class="dl-horizontal">
                <dt>{{ Form::label('main_start', 'วันเริ่ม', array('class' => 'left-label') ) }}:</dt>
                <dd>{{ Form::text('main_start', App::make('Helper')->getDefaultStartDate()->toDateString(), array('id' => 'main_start', 'class' => 'form-control left-label', 'placeholder' => 'yyyy-mm-dd', 'readonly') ) }}</dd>
            </dl>
            <dl class="dl-horizontal">
                <dt>{{ Form::label('main_end', 'วันสิ้นสุด', array('class' => 'left-label') ) }}:</dt>
                <dd>{{ Form::text('main_end', App::make('Helper')->getDefaultEndDate()->toDateString(), array('id' => 'main_end', 'class' => 'form-control left-label', 'placeholder' => 'yyyy-mm-dd', 'readonly') ) }}</dd>
            </dl>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-5 panel panel-info">
            <h3 class="panel-heading"><span class="glyphicon glyphicon glyphicon-time">&nbsp;ช่วงเวลาเปรียบเทียบ (historical data)</h3>
            <dl class="dl-horizontal">
                <dt>{{ Form::label('history_start', 'วันเริ่ม', array('class' => 'left-label') ) }}:</dt>
                <dd>{{ Form::text('history_start', App::make('Helper')->getDefaultHistoryStartDate()->toDateString(), array('id' => 'history_start', 'class' => 'form-control left-label', 'placeholder' => 'yyyy-mm-dd', 'readonly') ) }}</dd>
            </dl>
            <dl class="dl-horizontal">
                <dt>{{ Form::label('history_end', 'วันสิ้นสุด', array('class' => 'left-label') ) }}:</dt>
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