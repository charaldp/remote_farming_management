@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{$schedule->at_creation?__('New Schedule'):__('Edit Schedule')}}</div>
                <div class="card-body">
                    <div class="portlet">
                        <div class="portlet-header">
                        </div>
                        <div class="portlet-body">
                            <schedule
                                :schedule_in="{{$schedule}}"
                                :weekmap="{{json_encode($schedule::$weekMap)}}"
                            >
                            </schedule>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
