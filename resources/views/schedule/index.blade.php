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
                            <div class="caption">{{__('Name')}}</div>
                        </div>
                        <div class="portlet-body">
                            <schedule
                                :schedule_in="{{$schedule}}"
                                :weekmap="{{json_encode($schedule::$weekMap)}}"
                            >
                            </schedule>
                            {{-- <form method="POST" action="{{route('schedule.store')}}">
                                @csrf
                                <div class="form-group col-md-12">
                                    <label class="col-md-6 control-label" for="schedule_name">{{__('Schedule Name')}}</label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="name" id="schedule_name">{{$schedule->name}}</input>
                                    </div>
                                    <label class="col-md-2 control-label" for="schedule_name">{{__('')}}</label>
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="col-md-6 control-label" for="schedule_watering_weekdays">{{__('Watering Weekdays')}}</label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="watering_weekdays" id="schedule_watering_weekdays">{{$schedule->watering_weekdays}}</input>
                                    </div>
                                    <label class="col-md-2 control-label" for="schedule_watering_weekdays">{{__('')}}</label>
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="col-md-6 control-label" for="schedule_watering_weekdays_frequency">{{__('Watering Frequency')}}</label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="watering_weekdays_frequency" id="schedule_watering_weekdays_frequency">{{$schedule->watering_weekdays_frequency}}</input>
                                    </div>
                                    <label class="col-md-2 control-label" for="schedule_watering_weekdays_frequency">{{__('')}}</label>
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="col-md-6 control-label" for="schedule_watering_weekdays_time">{{__('Watering Weekdays Time')}}</label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="watering_weekdays_time" id="schedule_watering_weekdays_time">{{$schedule->watering_weekdays_time}}</input>
                                    </div>
                                    <label class="col-md-2 control-label" for="schedule_watering_weekdays_time">{{__('')}}</label>
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="col-md-6 control-label" for="schedule_watering_weekdays_duration">{{__('Watering Weekdays Duration')}}</label>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="watering_weekdays_duration" id="schedule_watering_weekdays_duration">{{$schedule->watering_weekdays_duration}}</input>
                                    </div>
                                    <label class="col-md-2 control-label" for="schedule_watering_weekdays_duration">{{__('')}}</label>
                                </div>
                                <div>
                                    <button type="submit">Create</button>
                                </div>
                            </form> --}}
                        </div>
                    </div>
                    {{-- <div class="form-group col-md-12">
                        <label class="col-md-5 control-label" for="schedule_name">{{__('Schedule Name')}}</label>
                        <label class="col-md-1 control-label" for="schedule_name" v-html="$root.__(this.symbols['length'])"></label>
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="name" id="schedule_name">{{$schedule->name}}</input>
                        </div>
                        <label class="col-md-2 control-label" for="schedule_name">{{this.$root.$refs.vessel.units.lengthSymbol}}</label>
                    </div> --}}
                    {{-- <div class="row-col-md-12">
                        <div class="col-md-5">{{__('Schedule Name')}}</div>
                        <div class="col-md-4"><label for="schedule_name">
                            <input id="schedule_name" name="name">{{$schedule->name}}</input>
                        </label>
                        <div class="col-md-3">{{__()}}</div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
