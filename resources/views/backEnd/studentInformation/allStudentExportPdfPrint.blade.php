<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@lang('student.export_to_pdf')</title>
    <style>
        body{
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact;
        }
        table {
            border-collapse: collapse;
        }
        h1,h2,h3,h4,h5,h6{
            margin: 0;
            color: #00273d;
        }
        .invoice_wrapper{
            max-width: 1200px;
            margin: auto;
            background: #fff;
            padding: 20px;
        }
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }
        .border_none{
            border: 0px solid transparent;
            border-top: 0px solid transparent !important;
        }
        .invoice_part_iner{
            background-color: #fff;
        }
        .invoice_part_iner h4{
            font-size: 30px;
            font-weight: 500;
            margin-bottom: 40px;
    
        }
        .invoice_part_iner h3{
            font-size:25px;
            font-weight: 500;
            margin-bottom: 5px;
    
        }
        .table_border thead{
            background-color: #F6F8FA;
        }
        .table td, .table th {
            padding: 5px 0;
            vertical-align: top;
            border-top: 0 solid transparent;
            color: #79838b;
        }
        .table td , .table th {
            padding: 5px 0;
            vertical-align: top;
            border-top: 0 solid transparent;
            color: #79838b;
        }
        .table_border tr{
            border-bottom: 1px solid #000 !important;
        }
        th p span, td p span{
            color: #212E40;
        }
        .table th {
            color: #00273d;
            font-weight: 300;
            border-bottom: 1px solid #f1f2f3 !important;
            background-color: #fafafa;
        }
        p{
            font-size: 14px;
        }
        h5{
            font-size: 12px;
            font-weight: 500;
        }
        h6{
            font-size: 10px;
            font-weight: 300;
        }
        .mt_40{
            margin-top: 40px;
        }
        .table_style th, .table_style td{
            padding: 20px;
        }
        .invoice_info_table td{
            font-size: 10px;
            padding: 0px;
        }
        .invoice_info_table td h6{
            color: #6D6D6D;
            font-weight: 400;
            }

        .text_right{
            text-align: right;
        }
        .virtical_middle{
            vertical-align: middle !important;
        }
        .school_header_table {
            width: auto;
            margin: 0 auto 22px;
        }
        .school_header_table td {
            border: 0;
            padding: 0;
            vertical-align: middle;
        }
        .thumb_logo {
            width: 110px;
            max-width: 110px;
        }
        .thumb_logo img{
            width: 100%;
            max-height: 55px;
            object-fit: contain;
            display: block;
        }
        .line_grid{
            display: grid;
            grid-template-columns: 140px auto;
            grid-gap: 10px;
        }
        .line_grid span{
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .line_grid span:first-child{
            font-weight: 600;
            color: #79838b;
        }
        p{
            margin: 0;
        }
        .font_18 {
            font-size: 18px;
        }
        .mb-0{
            margin-bottom: 0;
        }
        .mb_30{
            margin-bottom: 30px !important;
        }
        .border_table thead tr th {
            padding: 12px 10px;
        }
        .border_table tbody tr td {
            border-bottom: 1px solid rgba(0, 0, 0,.05);
            text-align: center;
            padding: 10px;
        }
        .logo_img h3{
            font-size: 22px;
            line-height: 1.2;
            margin-bottom: 4px;
            color: #79838b;
        }
        .logo_img h5{
            font-size: 12px;
            line-height: 1.35;
            margin-bottom: 0;
            color: #79838b;
        }
        .company_info{
            padding-left: 18px;
            max-width: 360px;
            text-align: left;
            vertical-align: middle;
        }
        .table_title{
            text-align: center;
        }
        .table_title h3{
            font-size: 35px;
            font-weight: 600;
            text-transform: uppercase;
            padding-bottom: 3px;
            display: inline-block;
            margin-bottom: 40px;
            color: #79838b;
        }
        .gray_header_table thead th{
            background: #515151 !important;
            color: #fff;
            border: 1px solid #515151;
        }
        .gray_header_table{
            border: 1px solid var(--border_color);
        }
        .gray_header_table tbody td, .gray_header_table tbody th {
            border: 1px solid var(--border_color);
        }
        .gray_header_table tbody tr:nth-of-type(2n+1) td {
            background-color: #EEEEEE !important;
        }
        .max-width-400{
            width: 400px;
        }
        .max-width-500{
            width: 500px;
        }
        .ml_auto{
            margin-left: auto;
            margin-right: 0;
        }
        .mr_auto{
            margin-left: 0;
            margin-right: auto;
        }
        .margin-auto{
          margin: auto;
        }

        .thumb.text-right {
        text-align: right;
    }
    </style>
</head>
@php 
    $generalSetting = generalSetting();
    $school_name = $generalSetting->school_name ?? 'EduAI';
    $site_title = $generalSetting->site_title ?? '';
    $school_code = $generalSetting->school_code ?? '';
    $address = $generalSetting->address ?? 'School Address';
    $phone = $generalSetting->phone ?? '';
    $logo = $generalSetting->logo ?? 'public/backEnd/img/logo.png';
@endphp
<script>
    var is_chrome = function () { return Boolean(window.chrome); }
    if(is_chrome) 
    {
       window.print();
    // setTimeout(function(){window.close();}, 10000); 
    //give them 10 seconds to print, then close
    }
    else
    {
       window.print();
    }
</script>
<body onLoad="loadHandler();">
    <div class="invoice_wrapper">
        <!-- invoice print part here -->
        <div class="invoice_print mb_30">
            <div class="container">
                <div class="invoice_part_iner">
                    <table class="school_header_table" align="center" style="margin: 0 auto; text-align: center; border: none;">
                        <tbody>
                            <tr>
                                <td class="thumb_logo" style="text-align: center; border: none; padding-bottom: 10px;">
                                    <img src="{{assetPath($logo)}}" alt="{{$school_name}}" style="max-height: 55px; margin: 0 auto;">
                                </td>
                            </tr>
                            <tr>
                                <td class="company_info logo_img" style="text-align: center; padding-left: 0; border: none;">
                                    <h3 style="margin: 0 0 5px 0;">{{$school_name ?? 'EduAI'}}</h3>
                                    <h5 style="margin: 0; font-weight: 400;">{!! nl2br(e($address ?? 'School Address')) !!}</h5>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="table_title">
                        <h3>@lang('student.all_student')</h3>
                    </div>
                    <!-- middle content  -->
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>
                                   <!-- single table  -->
                                   <table class="mb_30 max-width-500 mr_auto">
                                       <tbody>
                                           <tr>
                                               <td>
                                                <p class="line_grid" >
                                                    <span>
                                                        <span>@lang('common.academic_year')</span>
                                                        <span>:</span>
                                                    </span>
                                                    {{$academiYear->year}} ({{$academiYear->title}})
                                                </p>
                                            </td>
                                            <td>
                                                <p class="line_grid" >
                                                </p>
                                            </td>
                                           </tr>
                                           <tr>
                                                <td>
                                                    <p class="line_grid" >
                                                    </p>
                                                </td>
                                                <td>
                                                    <p class="line_grid" >
                                                        <span>
                                                        </span>
                                                    </p>
                                                </td>
                                           </tr>
                                           <tr>
                                                
                                                <td>
                                                    <p class="line_grid" >
                                                        <span>
                                                        </span>
                                                    </p>
                                                </td>
                                                
                                           </tr>
                                       </tbody>
                                   </table>
                                   <!--/ single table  -->
                                </td>
                                <td>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- invoice print part end -->
        
        <table class="table border_table gray_header_table mb_30" >
            <thead>
              <tr>
                <th>@lang('common.sl')</th>
                <th>@lang('student.admission_no')</th>
                <th>@lang('student.student_name')</th>
                <th>@lang('common.class') (@lang('common.section'))</th>
              </tr>
            </thead>
            <tbody>
                @foreach ($students as $key=>$student)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td>{{$student->admission_no}}</td>
                        <td>{{$student->full_name}}</td>
                        <td>
                            @foreach(@$student->studentRecords as $classSection)
                                {{$classSection->class->class_name}} ({{$classSection->section->section_name}}) @if(count($student->studentRecords) > 1), @endif  
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </tbody>
      </table>
    </div>
</body>
</html>
