<x-upload-step step="map"/>
<style>
.table {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #dee2e6;
    font-size: 15px;
    background-color: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}

.table thead th {
    background-color: #f4f6f9;
    padding: 12px 16px;
    font-weight: 600;
    color: #333;
    border-bottom: 1px solid #e0e0e0;
    text-align: left;
}

.table tbody td {
    padding: 12px 16px;
    border-bottom: 1px solid #f1f1f1;
    vertical-align: middle;
}

/* Select field */
select.primary_select.headers {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    background-color: #fff;
    font-size: 14px;
    color: #333;
    transition: border 0.3s ease;
}

select.primary_select.headers:focus {
    border-color: #3498db;
    outline: none;
}

/* Status Icons */
.hasMatch, .notMatch {
    display: none;
    font-size: 18px;
}

.hasMatch i {
    color: #28a745;
}

.notMatch i {
    color: #dc3545;
}

/* Show correct icon dynamically (handled via JS if needed) */
tr.matched .hasMatch {
    display: inline-block;
}
tr.unmatched .notMatch {
    display: inline-block;
}

/* Optional: Zebra striping */
.table tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}
</style>

<form action="#">
    <input type="hidden" name="step" value="map">

    @csrf
    <div class="row form">
        <div class="col-md-12">
            <table class="table table-bordered ">
                <thead>
                <tr>
                    <th>@lang('exam.filed')</th>
                    <th>@lang('exam.your_filed')</th>
                    <th>@lang('exam.status')</th>
                </tr>
                </thead>
                <tbody>
                @foreach($expectedHeaders as $key => $value)
                    <tr>
                        <td>{{$value}}</td>
                        <td>
                            <select class="primary_select headers "
                                    name="headers[{{convertToSnakeCase($value)}}]"
                                    data-name="{{convertToSnakeCase($value)}}"
                            >
                                <option value="">@lang('exam.not_matched')</option>
                                @foreach($filteredHeaders as $key2 => $newValue)
                                    <option value="{{$newValue}}"
                                        {{convertToSnakeCase($value)==$newValue?'selected':''}}
                                    >
                                        {{convertToTitleCase($newValue)}}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <div class="hasMatch">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <div class="notMatch">
                                <i class="fas fa-times-circle text-danger"></i>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-12 mt-20">
        <div class="submit_btn text-center ">

            <button class="primary-btn semi_large2 backToUpload fix-gr-bg "
                    type="button"><i class="ti-arrow-left"></i>@lang('exam.back')
            </button>

            <button class="primary-btn semi_large2 mapSubmitBtn fix-gr-bg "
                    type="button"><i class="ti-arrow-right"></i>@lang('exam.next')
            </button>
         </div>
    </div>
</form>

