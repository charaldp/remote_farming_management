@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{$sensor_device->at_creation?__('New Sensor Device'):__('Edit Sensor Device')}}</div>
                <div class="card-body">
                    <div class="portlet">
                        <div class="portlet-header">
                        </div>
                        <div class="portlet-body">
                            <sensor-device
                                :sensor_device_in="{{$sensor_device}}"
                                :sensor_reader_types={{json_encode(App\Models\SensorReading::$measurement_types)}}
                            >
                            </sensor-device>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
