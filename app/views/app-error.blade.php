@extends('layout.default')

@section('title')
{{ $title or 'Something is not right!' }} :: @parent
@stop

<?php

    if(Session::has('message')){
        $message = Session::get('message');
    }elseif(!isset($message)){
        $message = 'no message';
    }
?>
@section('content')
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <div class="text-center">
                <a href="{{ URL::to('/') }}">
                    <img width="455" height="425" src="{{ URL::to('/images/cool-404-errors-kitten.jpg') }}" alt="error-pic" />
                </a>
                <div>{{ \Carbon\Carbon::now()->toDateTimeString(); }}</div>
                <div>Message: {{ $message or 'N/A' }}</div>
                <br>
                {{ Form::button('Back To Home', array('class' => 'btn btn-info', 'onclick' => "window.location.href = '" . URL::to('/') . "'")) }}
            </div>
        </div>
    </div>
@stop

@section('page-js-script')
@stop
