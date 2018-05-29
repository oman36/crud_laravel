<div class="input-group mb-3">
    <div class="input-group-prepend">
        <label class="input-group-text">{{$name}}</label>
    </div>
    <div class="form-control">
        <input type="radio" name="{{$name}}" id="field_no_{{$__id}}" value="0"
               @if($value === 0) checked @endif>
        <label class="form-check-label" for="field_no_{{$__id}}">No</label>
        <input type="radio" name="{{$name}}" id="field_yes_{{$__id}}" value="1"
               @if($value === 1) checked @endif>
        <label class="form-check-label" for="field_yes_{{$__id}}">Yes</label>
        <input type="radio" name="{{$name}}" id="field_null_{{$__id}}" value=""
               @if($value === null) checked @endif @if($required) disabled @endif >
        <label class="form-check-label" for="field_null_{{$__id}}">Null</label>
    </div>
</div>