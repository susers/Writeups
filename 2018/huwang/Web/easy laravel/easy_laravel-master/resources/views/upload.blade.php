@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                @include('flash::message')
                <div class="panel panel-default">
                    <div class="panel-heading">upload </div>
                    <div class="panel-body">
                        <form method="POST" class="row" action="{{ route('upload') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="col-xs-10">
                                    <input type="file" name="file" class="form-control">
                            </div>
                            <div class="col-xs-2">
                                    <button type="submit" class="btn btn-primary">上 传</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
