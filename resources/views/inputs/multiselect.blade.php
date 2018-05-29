<div class="form-group">
    <label for="field_{{$__id}}">{{$name}}</label>
    <select
            multiple
            class="form-control"
            id="field_{{$__id}}"
            name="{{$name}}"
            @if($required) required @endif
    >
        @foreach($options as $oValue => $oName)
            <option value="{{$oValue}}" @if (in_array($oValue, $value, true)) selected @endif>
                {{$oName}}
            </option>
        @endforeach
    </select>
</div>