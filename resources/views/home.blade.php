@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    <a href="{{route('schedule.create')}}" class="btn btn-success">{{__('Add Schedule')}}<i class="fa fa-plus-square fa-fw"></i></a>

                    @foreach((Auth::user())->schedules as $schedule)
                        {{$schedule->name}}

                        @foreach($schedule->watering_weekdays as $key => $weekday)
                            {{$schedule->weekday($key)}}
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
