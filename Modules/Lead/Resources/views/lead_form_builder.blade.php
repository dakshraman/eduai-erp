@extends('backEnd.master')

@section('title')
    @lang('lead::lead.lead')
@endsection
<style>


.form-rendered #build-wrap {
  display: none;
}

.render-wrap {
  display: none;
}

.form-rendered .render-wrap {
  display: block;
}

#edit-form {
  display: none;
  float: right;
}

.form-rendered #edit-form {
  display: block;
}
.cb-wrap li.disabled {
    pointer-events: none;
    opacity: .6;
    background: #eef1f6;
}

.form-wrap.form-builder .frmb li {
    border: 2px solid transparent;
    padding: 6px;
    box-shadow: none !important;
}

.form-wrap.form-builder .frmb>li:hover {
    border: 2px dashed #d6d6d6 !important;
    box-shadow: none !important;
}
a#frmb-1636541146635-fld-1-copy {
    display: none;
}
.form-wrap.form-builder .frmb .field-actions .btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
}
.form-wrap.form-builder .frmb .field-actions .btn.copy-button{
    display: none !important;
}
.form-wrap.form-builder .stage-wrap {
    padding: 0 20px;
}
.form-wrap.form-builder .frmb .legend
{
    font-size: 18px;
    font-weight: 500;
    color: var(--base_color);
}
.form-wrap.form-builder .frmb .field-label{
    font-size: 18px !important;
    font-weight: 400 !important;
    color: var(--base_color) !important;
}
.form-wrap.form-builder .frmb-control li {
    font-size: 16px;
    font-weight: 400;
    color: var(--base_color);
}
.save-template{
    color: white !important;
    height: 35px !important;
}
span.label.label-default {
    background: 0 0;
    border: 1px;
    solid #d2d5dc;
    color: #63686f;
    font-size: 12px;
    font-weight: 400;
    padding: 0.3em 0.7em  0.3em;
    margin-left: 10px;
}
.form-wrap.form-builder .form-control{
    padding: 3px  12px !important;
}
.form-wrap.form-builder .cb-wrap.pull-left .form-actions {   
    padding-top: 20px;
    float: left;
}
.radio{
    margin-bottom: 10px;
}
</style>
@section('mainContent')
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('lead::lead.lead')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('lead::lead.lead')</a>
                    <a href="#">@lang('lead::lead.formbuilder')</a>
                </div>
            </div>
        </div>
    </section>
    <input type="hidden" id="module" value="{{ moduleStatusCheck('University') }}" />
    <input type="hidden" name="web_form_id" value="{{ $leadForm->id }}" />
    <input type="hidden" name="form_id" value="{{ $leadForm->buildForm->id }}" />
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0"> 
            <div class="row">
                <div class="offset-lg-8 col-lg-4 text-right col-md-12 mb-20">
                    <a class="primary-btn small fix-gr-bg" target="_blank"  href="{{ route('lead.integration',[$leadForm->buildForm->id]) }}">@lang('lead::lead.integration_code')</a>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box">
                    <div id="build-wrap"></div>
                  
                   </div>
                </div>
            </div>
        </div>
    </section>

@endsection
@include('backEnd.partials.date_picker_css_js')
@section('script')
@if (count($errors) > 0)
<script type="text/javascript">
    $('#addLead').modal('show');
</script>
@endif

  <script src="{{assetPath('modules/Lead/Resources/assets/js/form-builder.min.js')}}"></script>

  
  <script>            
    var module = $('#module').val();
        
    var buildWrap = document.getElementById('build-wrap');
    var formData = <?php  if($form->formData !=null){
         echo json_encode($form->formData) ;}else { 
             echo "[]";} 
             ?> ;
    
    if(formData.length){
    
      formData = formData.replace(/=\\/gm, "=''");
    }
    
    
            var fbOptions = {
                dataType: 'json',
                stickyControls: {
                    enable: false,
                }
            };
    
    
    if (formData && formData.length) {
        fbOptions.formData = formData;
    }
    
    fbOptions.disabledActionButtons = [
        'data',
        'clear'
    ];
    fbOptions.disabledAttrs = [
        'max',
        'maxlength',
        'min',
        'multiple',
        'access',
        'value',
        'type',
        'description',
        
    ];
    
    fbOptions.disableFields = [
        'autocomplete',
        'button',
        'checkbox',
        'checkbox-group',
        'date',
        'hidden',
        'number',
        'radio-group',
        'select',
        'text',
        'textarea',
        'datetime-local',
        
        
    ];
    
    fbOptions.controlPosition = 'left';
    
    fbOptions.controlOrder = [
        'header',
        'paragraph',
        'file',
    ];
    
    fbOptions.inputSets = [];

    var db_fields = [ 
       
       {
          "label":jsLang('first_name'),
          "name":"first_name",
          "fields":[
             {
                "subtype":"",
                "type":"text",
                "label":jsLang('first_name'),
                "className":"primary_input_field form-control",
                "name":"first_name",
                "required":true
             }
          ]
       },
       {
          "label":jsLang('last_name'),
          "name":"last_name",
          "fields":[
             {
                "subtype":"",
                "type":"text",
                "label":jsLang('last_name'),
                "className":"primary_input_field form-control",
                "name":"last_name",
                "required":false
             }
          ]
       },
    
       {
          "label":jsLang('email_address'),
          "name":"email",
          "fields":[
             {
                "subtype":"",
                "type":"text",
                "label":jsLang('email_address'),
                "className":"primary_input_field form-control",
                "name":"email"
             }
          ]
       },
       {
          "label":jsLang('id_number'),
          "name":"id_number",
          "fields":[
             {
                "subtype":"",
                "type":"text",
                "label":jsLang('id_number'),
                "className":"primary_input_field form-control",
                "name":"id_number"
             }
          ]
       },
       {
          "label":jsLang('phone'),
          "name":"phonenumber",
          "fields":[
             {
                "subtype":"",
                "type":"text",
                "label":"Phone",
                "className":"primary_input_field form-control",
                "name":"phonenumber"
             }
          ]
       },  



       {
       "label":jsLang('city'),
          "name":"city_id",
          "fields":[
             {
                "subtype":"",
                "type":"select",
                "label":jsLang('city'),
                "className":"primary_select  form-control",
                "name":"city_id",
                "required":false,
                "values": @json($cities)
             }
          ]
       },
     
    
       {
       "label":jsLang('blood_group'),
          "name":"blood_group_id",
          "fields":[
             {
                "subtype":"",
                "type":"select",
                "label":jsLang('blood_group'),
                "className":"primary_select  form-control",
                "name":"blood_group_id",
                "required":false,
                "values": @json($blood_groups)
             }
          ]
       },


       {
       "label":jsLang('religion'),
          "name":"religion_id",
          "fields":[
             {
                "subtype":"",
                "type":"select",
                "label":jsLang('religion'),
                "className":"primary_select  form-control",
                "name":"religion_id",
                "required":false,
                "values":@json($religions)
             }
          ]
       },
       {
          "label":jsLang('caste'),
          "name":"caste",
          "fields":[
             {
                "subtype":"",
                "type":"text",
                "label":jsLang('caste'),
                "className":"primary_input_field form-control",
                "name":"caste",
                "required":false
             }
          ]
       },
    
       {
          "label":jsLang('date_of_birth'),
          "name":"date_of_birth",
          "fields":[
             {
                "subtype":"",
                "type":"text",
                "label":jsLang('date_of_birth'),
                "className":"primary_input_field  primary_input_field date form-control form-control",
                "name":"date_of_birth",
                "required":false
             }
          ]
       },
       
       {
       "label":jsLang('gender'),
          "name":"gender_id",
          "fields":[
             {
                "subtype":"",
                "type":"select",
                "label":jsLang('gender'),
                "className":"primary_select  form-control",
                "name":"gender_id",
                "required":false,
                "values": @json($genders)
             }
          ]
       },
    
       {
          "label":jsLang('height'),
          "name":"height",
          "fields":[
             {
                "subtype":"",
                "type":"text",
                "label":jsLang('height'),
                "className":"primary_input_field form-control",
                "name":"height",
                "required":false
             }
          ]
       },
    
       {
          "label":jsLang('weight'),
          "name":"weight",
          "fields":[
             {
                "subtype":"",
                "type":"text",
                "label":jsLang('weight'),
                "className":"primary_input_field form-control",
                "name":"weight",
                "required":false
             }
          ]
       },
       {
          "label":"Description",
          "name":"description",
          "fields":[
             {
                "subtype":"",
                "type":"textarea",
                "label":"Description",
                "className":"primary_input_field form-control",
                "name":"description"
             }
          ]
       },
    
    ];


  
        
        @if(moduleStatusCheck('University'))
        db_fields = [
            ...db_fields,
                {
                "label":jsLang('session'),
                    "name":"un_session_id",
                    "fields":[
                        {
                            "subtype":"",
                            "type":"select",
                            "label":jsLang('session'),
                            "className":"primary_select  form-control",
                            "name":"un_session_id",
                            "required":false,
                            "values": @json($unSessions)
                        }
                    ]
                },
                {
                "label":jsLang('faculty'),
                    "name":"un_faculty_id",
                    "fields":[
                        {
                            "subtype":"",
                            "type":"select",
                            "label":jsLang('faculty'),
                            "className":"primary_select  form-control",
                            "name":"un_faculty_id",
                            "required":false,
                            "values": @json($unFaculties)
                        }
                    ]
                },
                {
                "label":jsLang('department'),
                    "name":"un_department_id",
                    "fields":[
                        {
                            "subtype":"",
                            "type":"select",
                            "label":jsLang('department'),
                            "className":"primary_select  form-control",
                            "name":"un_department_id",
                            "required":false,
                            "values": @json($unDepartments)
                        }
                    ]
                },
                {
                "label":jsLang('academic'),
                    "name":"un_academic_id",
                    "fields":[
                        {
                            "subtype":"",
                            "type":"select",
                            "label":jsLang('academic'),
                            "className":"primary_select  form-control",
                            "name":"un_academic_id",
                            "required":false,
                            "values": @json($unAcademics)
                        }
                    ]
                },
                {
                "label":jsLang('semester'),
                    "name":"un_semester_id",
                    "fields":[
                        {
                            "subtype":"",
                            "type":"select",
                            "label":jsLang('semester'),
                            "className":"primary_select  form-control",
                            "name":"un_semester_id",
                            "required":false,
                            "values": @json($unSemesters)
                        }
                    ]
                },
                
        ];
       
    @else
       db_fields = [
           ...db_fields,
            {
            "label":jsLang('class'),
                "name":"class_id",
                "fields":[
                    {
                        "subtype":"",
                        "type":"select",
                        "label":jsLang('class'),
                        "className":"primary_select  form-control",
                        "name":"class_id",
                        "required":false,
                        "values": @json($classes)
                    }
                ]
            },
            
       ];
     @endif
    console.log(db_fields);
     var cfields = @json($fields);


    
    $.each(db_fields, function(i, f) {
        fbOptions.inputSets.push(f);
    });
    
    if (cfields && cfields.length) {
        $.each(cfields, function(i, f) {
            fbOptions.inputSets.push(f);
        });
    }
    
    fbOptions.typeUserEvents = {
        'text': {
            onadd: function(fId) {
                do_form_field_restrictions(fId, 'input');
            },
        },
        'number': {
            onadd: function(fId) {
                do_form_field_restrictions(fId, 'input');
            },
        },
        'email': {
            onadd: function(fId) {
                do_form_field_restrictions(fId, 'input');
            },
        },
        'color': {
            onadd: function(fId) {
                do_form_field_restrictions(fId, 'input');
            },
        },
        'date': {
            onadd: function(fId) {
                do_form_field_restrictions(fId, 'input');
            },
        },
        'datetime-local': {
            onadd: function(fId) {
                do_form_field_restrictions(fId, 'datetime-local');
            },
        },
        'select': {
            onadd: function(fId) {
                do_form_field_restrictions(fId, 'select');
            },
        },
        'file': {
            onadd: function(fId) {
                do_form_field_restrictions(fId, 'file');
                // set file upload field name to be always file-input
                $(fId).find('.name-wrap .input-wrap input').val('file_input')
                // Used in delete
                setTimeout(function(){
                    $(fId).find('.fb-file input[type="file"]').attr('name','file_input')
                },500);
            },
        },
        'textarea': {
            onadd: function(fId) {
                do_form_field_restrictions(fId, 'textarea');
            },
        },
        'checkbox-group': {
            onadd: function(fId) {
                do_form_field_restrictions(fId, 'checkbox-group');
            },
        },
    }
    $(function() {
    
     
    
            $('body').on('click', '.del-button', function() {

        var _field = $(this).parents('li.form-field');

        var _preview_name;
        var s = $('.cb-wrap .ui-sortable');
        if (_field.find('.prev-holder input').length > 0) {
            _preview_name = _field.find('.prev-holder input').attr('name');
        } else if (_field.find('.prev-holder textarea').length > 0) {
            _preview_name = _field.find('.prev-holder textarea').attr('name');
        } else if (_field.find('.prev-holder select').length > 0) {
            _preview_name = _field.find('.prev-holder select').attr('name');
        }

        var pos = _preview_name.lastIndexOf('-');
        _preview_name = _preview_name.substr(0, pos);
        if (_preview_name != 'file_input') {
            $('li[data-type="' + _preview_name + '"]').removeClass('disabled')
        } else {
            setTimeout(function() {
                s.find('li').eq(2).removeClass('disabled');
            }, 50);
        }
        setTimeout(function() {
            s.sortable({ cancel: '.disabled' });
            s.sortable('refresh');
        }, 80);
    });
        $('body').on('blur', '.form-field:not([type="header"],[type="paragraph"],[type="checkbox-group"]) input[name="className"]',
            function() {
            var className = $(this).val();
            if (className.indexOf('form-control') == -1) {
                className = className.trim();
                className += ' form-control';
                className = className.trim();
                $(this).val(className);
            }
        });
    
        $('body').on('focus', '.name-wrap input', function() {
            $(this).blur();
        });
    
    });
    
    function do_form_field_restrictions(fId, type) {
        var _field = $(fId);
    
        var _preview_name;
        var s = $('.cb-wrap .ui-sortable');
    
        if (type == 'checkbox-group') {
            _preview_name = _field.find('input[type="checkbox"]').eq(0).attr('name');
        } else if (type == 'file') {
            setTimeout(function() {
                s.find('li').eq(2).addClass('disabled');
            }, 50);
        } else {
            var check = _field.find('[type="'+type+'"]');
            if(check.length == 0) {
                check = _field.find(type);
            }
            _preview_name = check.attr('name');
            // console.log(_preview_name);
        }
    
        if(type != 'file') {
            var pos = _preview_name.lastIndexOf('-');
            _preview_name = _preview_name.substr(0, pos);
            // console.log(_preview_name);
            $('[data-type="' + _preview_name + '"]:not(.form-field)').addClass('disabled');
        }
    
        $('.frmb-control li[type="'+_preview_name+'"]').removeClass('text-danger');
    
        if(typeof(mustRequiredFields) != 'undefined' && $.inArray(_preview_name,mustRequiredFields) != -1){
            _field.find('.required-wrap input[type="checkbox"]').prop('disabled',true);
        }
    
        setTimeout(function() {
            s.sortable({ cancel: '.disabled' });
            s.sortable('refresh');
        }, 80);
    }
</script>
<script>

   $(function(){
    $('body').on('blur', '.form-field.editing', function () {
        $.Shortcuts.start();
    });

    $('body').on('focus', '.form-field.editing', function () {
        $.Shortcuts.stop();
    });

     var formBuilder = $(buildWrap).formBuilder(fbOptions);

     setTimeout(function(){
         $(".form-builder-save" ).wrap( "<div class='btn-bottom-toolbar text-right'></div>" );
         $btnToolbar = $('body').find('#tab_form_build .btn-bottom-toolbar');
         $btnToolbar = $('#tab_form_build').append($btnToolbar);
         $btnToolbar.find('.btn').addClass('btn-info');
     },100);
    
    $(document).on('click','.save-template',function() {
                var url     = $("#url").val();
                // console.log(formBuilder.actions.getData());
                $(".save-template").html('Saving...')
                $.post(url+'/lead/save-form-data',{
                    formData:formBuilder.formData,
                
                    id:$('input[name="web_form_id"]').val(),
                    form_id:$('input[name="form_id"]').val()
                }).done(function(response){
                    $(".save-template").html('Save')
                    setTimeout(function() {
                                toastr.success(response.message, "Success", {
                                    timeOut: 5000,
                                });
                            }, 500);
                    // alert(response.message);
                    // response = JSON.parse(response);
                    // toastr.success("Note Delete Operation Successfully", "Success", {
                    //                 timeOut: 3000,
                    //             });
                    // if(response.success == true){
                    // alert('success',response.message);
                    // }
                });
         });
     });
    </script>

@endsection
