@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{$control_device->at_creation?__('New Schedule'):__('Edit Schedule')}}</div>
                <div class="card-body">
                    <div class="portlet">
                        <div class="portlet-header">
                            <div class="caption">{{__('Name')}}</div>
                        </div>
                        <div class="portlet-body">
                            <control-device
                                :control_device_in="{{$control_device}}"
                            >
                            </control-device>
                        </div>
                    </div>
                </div>
            </div>
            @if (!$control_device->at_creation)
            <div class="card">
                <div class="card-header">
                    <h3><strong>{{__('messages.sensors')}}<strong></h3>
                </div>
                <div class="card-body">
                    @foreach((Auth::user()->sensor_devices) as $sensor_device)
                        <table>
                            <th><a href="{{route('sensor_device.edit', $control_device_id, $sensor_device->id)}}"><b>{{$sensor_device->name}}</b></a></th>
                        </table>
                    @endforeach
                    <a href="{{route('sensor_device.create', $control_device_id)}}" class="btn btn-success">{{__('messages.add_sensor')}}<i class="fa fa-plus-square fa-fw"></i></a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
