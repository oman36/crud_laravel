<div class="form-{{'checkbox' === $html ? 'check' : 'group'}}">
    @if('checkbox' !== $html && 'hidden' !== $html) <label for="field_{{$__id}}">{{$name}}</label> @endif
    <input
            type="{{$html}}"
            class="form-{{'checkbox' === $html ? 'check-input' : 'control'}}"
            id="field_{{$__id}}"
            placeholder="{{$value}}"
            value="{{$value}}"
            name="{{$name}}"
            @if(-1 !== $max) max="{{$max}}" @endif
            @if($required) required @endif
    >
        @if('checkbox' === $html) <label for="field_{{$__id}}">{{$name}}</label> @endif
</div>
