@extends('layout')
@section('content')
    <p>
        <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false"
           aria-controls="collapseExample">
            Filters
        </a>
    </p>
    <div class="collapse" id="collapseExample">
        <form method="GET">
            @php $filterValues = request('filters'); @endphp
            @foreach($filterFields as $n => $field)
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="field_filter_{{$n}}">
                            {{$field}}
                        </label>
                    </div>
                    <input
                            type="text"
                            class="form-control"
                            id="field_filter_{{$n}}"
                            value="{{$filterValues[$field] ?? ''}}"
                            name="filters[{{$field}}]"
                    >
                </div>

            @endforeach
            <button type="submit" class="btn btn-primary">Apply</button>
            <br>
            <br>
        </form>
    </div>
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
                <td scope="col">
                    <a class="btn btn-info btn-sm" href="/{{$active_table}}/{{$row->id}}">Edit</a>
                    <form class="delete-form" data-id="{{$row->id}}" action="/{{$active_table}}/{{$row->id}}"
                          method="POST" style="display: inline">
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
        if ({{is_array(request()->input('filters'))}}) {
            $('a[href="#collapseExample"]').click()
        }
    </script>
@stop