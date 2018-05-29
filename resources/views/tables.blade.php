@extends('layout')
@section('content')
    <ul class="list-group">
        @foreach($tables as $table)
            <li class="list-group-item">
                <a href="/{{ $table }}">{{$table}}</a>
            </li>
        @endforeach
    </ul>
@stop