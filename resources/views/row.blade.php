@extends('layout')
@section('content')
    <form method="POST">
        @foreach($fields as $n => $field)
            @php
                $field['__id'] = $n;
            @endphp
            @if(in_array($field['html'], ['text', 'number', 'hidden', 'checkbox']))
                @include('inputs.input', $field)
            @else()
                @include('inputs.' . $field['html'], $field)
            @endif
        @endforeach
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
@stop