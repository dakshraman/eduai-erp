@php
    $grid_class = isset($grid_class) ? $grid_class : 'col-lg-3';
    $mt = isset($mt) ? $mt : ' ';
    $required = isset($required) ? $required : false;
    $shift_id = isset($editData) ? $editData : null;
    $label = isset($label) ? $label : null;
    $name = isset($name) ? $name : 'shift';
    $disabled = isset($disabled) ? $disabled : false;
    $id = isset($id) ? $id : 'common_shift';
@endphp

<div class="{{ $grid_class }} {{ $mt }}">
    <div class="primary_input " id="">
        @if (isset($label))
            <label class="primary_input_label" for="">{{ @$label }} @if ($required == true)
                    <span class="text-danger"> *</span>
                @endif </label>
        @endif
        <select class="primary_select select_shift form-control{{ $errors->has('shift') ? ' is-invalid' : '' }}"
            name="{{ $name }}" {{ $disabled == true ? 'disabled' : '' }} id="{{ $id }}">
            <option data-display="@lang('admin.select_shift') @if ($required == true) * @endif" value="">
                @lang('admin.select_shift')
                @if ($required == true)
                    *
                @endif
            </option>
            @foreach (shifts() as $shift)
                <option value="{{ $shift->id }}" {{ isset($editData) && $shift_id == $shift->id ? 'selected' : '' }}>
                    {{ $shift->name }}
                </option>
            @endforeach
        </select>

        @if ($errors->has('shift'))
            <span class="text-danger">
                {{ $errors->first('shift') }}
            </span>
        @endif
    </div>
</div>
<input type="text" value="{{assetPath('public/backEnd/img/demo_wait.gif')}}" hidden id="class_loader">

