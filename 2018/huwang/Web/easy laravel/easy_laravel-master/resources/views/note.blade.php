@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                @include('flash::message')
                <ul class="list-group">
                    @foreach ($notes as $note)
                        <li class="list-group-item row">
                            <div class="col-xs-10"> {{ $note->content }} </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
