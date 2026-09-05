<div class="container-fluid">
    <div class="col-lg-12 mb-20">
        <div class="table-responsive">
        <table class="table student_incident_report_table" cellspacing="0"
            width="100%">
            <thead>
                <tr>
                    @if (moduleStatusCheck('Branch'))
                    <th>@lang('common.branch')</th>
                    @endif
                    <th width="">@lang('behaviourRecords.title')
                    </th>
                    <th width="">@lang('behaviourRecords.point')
                    </th>
                    <th width="">@lang('behaviourRecords.session')
                    </th>
                    <th width="">@lang('behaviourRecords.date')
                    </th>
                    <th width="">@lang('behaviourRecords.description')
                    </th>
                    <th width="">@lang('behaviourRecords.assigned_by')
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($all_incident as $incident)
                    <tr>
                        @if (moduleStatusCheck('Branch'))
                        <td>{{ $incident->branch?->branch_name }}</td>
                        @endif
                        <td>{{ $incident->incident->title }}
                        </td>
                        <td>{{ $incident->incident->point }}
                        </td>
                        <td>{{ $incident->academicYear->year }} {{ $incident->academicYear->title }}
                        </td>
                        <td>{{ dateconvert($incident->incident->created_at) }}
                        </td>
                        <td>{{ $incident->incident->description }}
                        </td>
                        <td>{{ $incident->user->full_name }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
</div>
