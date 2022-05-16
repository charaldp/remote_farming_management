@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3><strong>{{__('messages.weekly_schedules')}}<strong></h3>
                </div>
                <div class="card-body">
                    @foreach((Auth::user())->schedules as $schedule)
                        <table>
                            <th><a href="{{route('schedule.edit', $schedule->id)}}"><b>{{$schedule->name}}</b></a></th>
                            <tr>
                            @foreach($schedule->weekdays() as $key => $weekday)
                                <td>
                                    {{$schedule->weekday($key)}}
                                </td>
                                @endforeach
                            </tr>
                        </table>
                    @endforeach
                    <a href="{{route('schedule.create')}}" class="btn btn-success">{{__('messages.add_schedule')}}<i class="fa fa-plus-square fa-fw"></i></a>
                </div>
            </div>
            <br/>
            <div class="card">
                <div class="card-header">
                    <h3><strong>{{__('messages.control_devices')}}<strong></h3>
                </div>
                <div class="card-body">
                    @foreach((Auth::user())->control_devices as $control_device)
                        <table>
                            <th><a href="{{route('control_device.edit', $control_device->id)}}"><b>{{$control_device->name}}</b></a></th>
                            <tr>
                            </tr>
                        </table>
                    @endforeach
                    <a href="{{route('control_device.create')}}" class="btn btn-success">{{__('messages.add_control_device')}}<i class="fa fa-plus-square fa-fw"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
