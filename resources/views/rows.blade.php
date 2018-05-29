@extends('layout')
@section('content')
    <a href="/{{$active_table}}/new" class="btn btn-success">Add</a>
    <br>
    <br>
    <table class="table">
        <thead>
        <tr>
            @foreach($fields as $field)
                <th scope="col">{{$field}}</th>
            @endforeach
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        <tr>
        @foreach($rows as $row)
            <tr>
                @foreach($row as $field => $value)
                    <td scope="col">{{$value}}</td>
                @endforeach
                <td  scope="col">
                    <a class="btn btn-info btn-sm" href="/{{$active_table}}/{{$row->id}}">Edit</a>
                    <form class="delete-form" data-id="{{$row->id}}" action="/{{$active_table}}/{{$row->id}}" method="POST" style="display: inline">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="submit" class="btn btn-danger btn-sm" value="Delete">
                    </form>

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @include('pagination')
    <script>
        $('.delete-form').on('submit', function (e) {
            console.log(e);
            if (confirm("You are sure? id =" + $(this).data('id'))) {
                return this;
            } else {
                e.preventDefault();
            }
        });
    </script>
@stop