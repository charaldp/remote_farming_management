@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <scene-component :json="{{App\Car::$vehicle_example}}"></scene-component>
                    {{-- <scene-simple></scene-simple> --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
