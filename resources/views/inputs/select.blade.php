<div class="input-group mb-3">
    <div class="input-group-prepend">
        <label class="input-group-text" for="field_{{$__id}}">{{$name}}</label>
    </div>
    <select
            class="custom-select"
            id="field_{{$__id}}"
            name="{{$name}}"
            @if($required) required @endif
    >
        @if(!$required)
            <option value="" @if (null === $value) selected @endif></option>
        @endif
        @foreach($options as $oValue => $oName)
            <option value="{{$oValue}}" @if ($oValue === $value) selected @endif>
                {{$oName}}
            </option>
        @endforeach
    </select>
</div>
<script>
    $('#field_' + '{{$__id}}').select2()
</script>
