@props([
    'step'=>'upload'
])
<div>
    <style>
        .timeline {
            counter-reset: step 0;
            position: relative;
            display: flex;
            justify-content: space-between;
            padding: 0;
        }

        .timeline li {
            list-style: none;
            position: relative;
            text-align: center;
            text-transform: uppercase;
            font-size: 13px;
            display: flex;
            align-items: center;
            flex-direction: column;
            gap: 10px;
        }

        ul:nth-child(1) {
            color: #4caf50;
        }

        .timeline li:before {
            counter-increment: step;
            content: counter(step);
            width: 30px;
            height: 30px;
            border: 1.5px solid #dddddd;
            border-radius: 50%;
            display: block;
            text-align: center;
            line-height: 30px;
            margin: 0;
            background: #fff;
            color: #000;
            transition: all ease-in-out 0.3s;
            cursor: pointer;
            font-size: 12px;
            z-index: 2;
            position: relative;
        }

        .timeline:after {
            content: "";
            position: absolute;
            width: calc(100% - 30px);
            height: 1.5px;
            background-color: #dddddd;
            top: 14px;
            transition: all ease-in-out 0.3s;
            z-index: 1;
            left: 50%;
            transform: translateX(-50%);
            right: 0;
        }

        .timeline li:first-child:after {
            content: none;
        }

        .timeline li.active {
            color: #555555;
        }

        .timeline li.active:before {
            background: #4caf50;
            color: #f1f1f1;
        }

        .timeline li.active + li:after {
            background: #dddddd;
        }

    </style>
    <ul class="timeline">
        <li class="{{$step == 'upload' ? 'active' : ''}}">@lang('exam.upload')</li>
        <li class="{{$step == 'map' ? 'active' : ''}}">@lang('exam.map_data')</li>
        <li class="{{$step== 'import' ? 'active' : ''}}">@lang('exam.import')</li>
    </ul>
    <hr>
</div>