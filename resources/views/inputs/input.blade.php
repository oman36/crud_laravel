<div class="input-group mb-3">
    @if($html !== 'hidden')
    <div class="input-group-prepend">
        <label class="input-group-text"  for="field_{{$__id}}">{{$name}}</label>
    </div>
    @endif
    <input
            type="{{$html}}"
            class="form-control"
            id="field_{{$__id}}"
            placeholder="{{$value}}"
            value="{{$value}}"
            name="{{$name}}"
            @if(-1 !== $max) max="{{$max}}" @endif
            @if($required) required @endif
    >
</div>
