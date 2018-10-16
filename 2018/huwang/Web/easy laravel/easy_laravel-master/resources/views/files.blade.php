@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                @include('flash::message')
                <ul class="list-group">
                    @foreach ($files as $file)
                        <li class="list-group-item row">
                            <div class="col-xs-10" style="padding-top:5px;"> {{ $file }} </div>
                            <div class="col-xs-2"> 
                                <form method="POST" action="{{ route('check') }}">
                                    <button type="submit" class="btn btn-link">check</button>
                                    <input type="hidden" name="filename" value="{{ str_replace('public','',$file) }}">
                                    {{ csrf_field() }}
                                </form>  
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
