<x-upload-step step="upload"/>
<style>
/* Wrapper styling */
/* .primary_input {
    background: #fff;
    border: 1px solid #e0e6ed;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
} */

/* Label styling */
.primary_input_label_sample {
    /* font-weight: 600; */
    /* font-size: 14px; */
    /* color: #2d3748; */
    display: flex;
    justify-content: space-between;
    /* margin-bottom: 12px; */
}

/* Sample file link */
.primary_input_label small a {
    font-size: 12px;
    color: #fff;
    text-decoration: none;
    transition: color 0.3s;
}

/* File input box */
.profile_uploader {
    border: 2px dashed #cbd5e0;
    background-color: #f7fafc;
    padding: 25px;
    border-radius: 8px;
    position: relative;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.3s ease;
}

.profile_uploader:hover {
    border-color: #3490dc;
}

/* Hidden file input */
.profile_uploader input[type="file"] {
    display: none;
}

/* Placeholder text */
#placeholderImportFile {
    font-size: 14px;
    color: #718096;
    display: block;
    margin-bottom: 10px;
}

/* Browse button styling */
.primary-btn {
    display: inline-flex;
    align-items: center;
    background-color: #3490dc;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.primary-btn:hover {
    background-color: #2779bd;
}

.primary-btn svg {
    margin-right: 8px;
}

/* Error messages */
.text-danger {
    color: #e3342f !important;
    font-size: 14px;
}

/* Failure message list */
.failure-list {
    margin-top: 15px;
    font-size: 13px;
    color: #e3342f;
    background: #fff5f5;
    padding: 10px;
    border-left: 3px solid #e3342f;
    border-radius: 4px;
}
.instruction ol li{
    font-size: 15px;
    line-height: 30px;
}

</style>
<form action="#" method="POST"
      enctype="multipart/form-data"
      class="">
    <input type="hidden" name="step" value="upload">
    @csrf
    <div class="row form">
        <div class="col-md-12 mb-30 instruction">
            <h4 class="">@lang('exam.import_instruction')</h4>
            <ol>
                @foreach($instructions as $key => $instruction)
                    <li>{!! ++$key . '. ' . $instruction !!}</li>
                @endforeach
            </ol>
        </div>
        {{$slot}}
        <div class="col-lg-3 mt-30-md">
            <label class="primary_input_label" for="">{{ __('common.exam') }}
                <span class="text-danger">*</span>
            </label>
            {{-- @dd($exams); --}}
            <select
                class="primary_select form-control{{ $errors->has('exam') ? ' is-invalid' : '' }}"
                name="exam" id="exam_id">
                <option data-display="@lang('exam.select_exam') *" value="">@lang('exam.select_exam') *
                </option>
                @foreach ($exams as $exam)
                    <option value="{{ @$exam->id }}">{{ @$exam->title }}</option>
                @endforeach
            </select>
            @if ($errors->has('exam'))
                <span class="text-danger invalid-select" role="alert">
                    {{ $errors->first('exam') }}
                </span>
            @endif
        </div>
        @include('backEnd.common.search_criteria', [
            'div' => 'col-lg-3',
            'mt' => 'mt-30-md',
            'required' => ['class', 'section', 'subject'],
            'visiable' => ['shift', 'class', 'section', 'subject'],
            'subject' => true,
            'class_name' => 'class',
            'section_name' => 'section',
            'subject_name' => 'subject',
        ])
        <div class="col-md-{{$col_size??12}} mb-15 mt-15">
            <div class="primary_input d-block">
                <label class="primary_input_label primary_input_label_sample"
                       for="">@lang('exam.choose_excel_file')
                       <small>
                            <a href="{{$sample}}" class="primary-btn text-uppercase bord-rad">
                                @lang('exam.download_sample_file')
                                <span class="pl ti-download"></span>
                            </a>
                        </small>
                </label>
                <label class="profile_uploader w-100 ml-0" for="import_file">
                    <input type="file" class="d-none" accept=".xlsx, .xls, .csv" name="file"
                           id="import_file" required>
                    <span
                        id="placeholderImportFile">@lang('exam.browse_file')</span>
                    <label for="import_file" class="primary-btn fix-gr-bg mb-0 px-3">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9 22H15C20 22 22 20 22 15V9C22 4 20 2 15 2H9C4 2 2 4 2 9V15C2 20 4 22 9 22Z"
                                stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <path
                                d="M9 10C10.1046 10 11 9.10457 11 8C11 6.89543 10.1046 6 9 6C7.89543 6 7 6.89543 7 8C7 9.10457 7.89543 10 9 10Z"
                                stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"></path>
                            <path
                                d="M2.67188 18.9496L7.60187 15.6396C8.39187 15.1096 9.53187 15.1696 10.2419 15.7796L10.5719 16.0696C11.3519 16.7396 12.6119 16.7396 13.3919 16.0696L17.5519 12.4996C18.3319 11.8296 19.5919 11.8296 20.3719 12.4996L22.0019 13.8996"
                                stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"></path>
                        </svg>
                        @lang('exam.browse')
                    </label>
                </label>
                <span
                    class="text-danger f_s_14 mt-1 d-block">{{$errors->first('file')}}</span>
            </div>
            @isset($failures)
                <span
                    class="text-danger f_s_14 mt-1 d-block mb-2">@lang('exam.base') :</span>
                @foreach ($failures as $failure)
                    <span
                        class="text-danger f_s_14 mt-1 d-block">{{ $failure->values()[$failure->attribute()].' : '.$failure->errors()[0].' in Row no : '. $failure->row()}}</span>
                @endforeach
            @endisset

        </div>
    </div>

    <div class="col-12 mt-20">
        <div class="submit_btn text-center ">
            <button
                class="primary-btn semi_large2   fix-gr-bg uploadSubmitBtn"
                type="button">
                @lang('exam.next')
                <i class="ti-arrow-right"></i>
            </button>
        </div>
    </div>
</form>
