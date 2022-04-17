@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @foreach((Auth::user())->schedules as $schedule)
                        <table>
                            <th><b>{{$schedule->name}}</b></th>
                            <tr>
                            @foreach(($schedule->watering_weekdays) as $key => $weekday)
                                <td>
                                    {{$schedule->weekday($key)}}
                                </td>
                                @endforeach
                            </tr>
                        </table>
                    @endforeach
                    <a href="{{route('schedule.create')}}" class="btn btn-success">{{__('Add Schedule')}}<i class="fa fa-plus-square fa-fw"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
