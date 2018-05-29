<div class="input-group mb-3">
    <div class="input-group-prepend">
        <label class="input-group-text" for="field_{{$__id}}">{{$name}}</label>
    </div>
    <textarea
            class="form-control"
            id="field_{{$__id}}"
            placeholder="{{$value}}"
            name="{{$name}}"
            @if($required) required @endif
    >{{$value}}</textarea>
</div>