@extends('backEnd.master')

@section('title')
    @lang('lead::lead.lead')
@endsection
@section('mainContent')
    <section class="sms-breadcrumb mb-20 up_breadcrumb">
        <div class="container-fluid">
            <div class="row justify-content-between">
                <h1>@lang('lead::lead.lead')</h1>
                <div class="bc-pages">
                    <a href="{{ route('dashboard') }}">@lang('common.dashboard')</a>
                    <a href="#">@lang('lead::lead.lead')</a>
                    <a href="#">@lang('lead::lead.lead')</a>
                </div>
            </div>
        </div>
    </section>
    <input type="hidden" name="web_form_id" value="" />
    <section class="admin-visitor-area up_admin_visitor">
        <div class="container-fluid p-0"> 
            <div class="row">
                <div class="col-lg-12">
                    <div class="white-box">
                        {{-- <div id="markup"></div> --}}
                  
                   </div>
                </div>
            </div>
        </div>
    </section>

@endsection





@section('script')
 
    {{-- <script src="{{assetPath('modules/Lead/Resources/assets/js/form-builder.min.js')}}"></script>
    <script src="{{assetPath('modules/Lead/Resources/assets/js/form-render.min.js')}}"></script>
    <script>
        
        jQuery($ => {
        const escapeEl = document.createElement("textarea");
        const code = document.getElementById("markup");
        const formData =<?php  echo ($form->formData); ?>
            '[{"type":"textarea","label":"Text Area","className":"form-control","name":"textarea-1492616908223","subtype":"textarea"},{"type":"select","label":"Select","className":"form-control","name":"select-1492616913781","values":[{"label":"Option 1","value":"option-1","selected":true},{"label":"Option 2","value":"option-2"},{"label":"Option 3","value":"option-3"}]},{"type":"checkbox-group","label":"Checkbox Group","name":"checkbox-group-1492616915392","values":[{"label":"Option 1","value":"option-1","selected":true}]}]';
        const addLineBreaks = html => html.replace(new RegExp("><", "g"), ">\n<");

        // Grab markup and escape it
        const $markup = $("<div/>");
        $markup.formRender({ formData });

        // set < code > innerText with escaped markup
        code.innerText = addLineBreaks($markup.formRender("html"));

        hljs.highlightBlock(code);
        });

        
    </script> --}}
@endsection
