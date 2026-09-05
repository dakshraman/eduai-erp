<div class="row row-gap-24">
    @forelse($all_staffs as $staff)
        @php
            $staffPhoto = $staff->staff_photo ? assetPath($staff->staff_photo) : assetPath('public/backEnd/assets/img/avatar.png');
        @endphp

        <div class="col-lxx-3 col-xxl-4 col-xl-6 col-sm-6">
            <div class="student_list_grid_item">
                <div class="student_list_grid_item_head">
                    <div class="student_list_grid_item_id">#{{ $staff->staff_no }}</div>
                    <div class="student_list_grid_item_photo">
                        <img src="{{$staffPhoto}}" class="student_list_grid_item_photo_img" alt="student photo"/>
                        <img class="student_list_grid_item_photo_bg" src="{{assetPath('public/backEnd/assets/img/photo_bg.svg')}}" alt="photo bg"/>
                    </div>
                    <div class="student_list_grid_item_action">
                        <div class="dropdown">
                            <a class="dropdown_toggler" href="#" role="button" data-toggle="dropdown">
                                <svg width="2" height="10" viewBox="0 0 2 10"><rect width="2" height="2" rx="1" fill="#1A2945"/><rect y="4" width="2" height="2" rx="1" fill="#1A2945"/><rect y="8" width="2" height="2" rx="1" fill="#1A2945"/></svg>
                            </a>
                            <ul class="dropdown-menu">
                                @if(userPermission('viewStaff'))
                                <li><a class="dropdown-item" target="_blank" href="{{ route('viewStaff', [$staff->id]) }}">View</a></li>
                                @endif
                                @if(userPermission('editStaff'))
                                <li><a class="dropdown-item" href="{{ route('editStaff', [$staff->id]) }}">Edit</a></li>
                                @endif
                                @if(userPermission('deleteStaff'))
                                <li><a class="dropdown-item" onclick="deleteStaff({{$staff->id}})" data-toggle="modal" data-target="#deleteStudentModal" data-id="{{$staff->id}}">Delete</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="student_list_grid_item_body">
                    <a href="{{ route('viewStaff', [$staff->id]) }}" target="_blank" class="student_list_grid_item_name d-block">
                        {{ $staff->first_name.' '.$staff->last_name }}
                    </a>

                    <table class="student_list_grid_info">
                        <tbody>
                            @if (moduleStatusCheck('Branch'))
                            <tr><td>Branch</td><td>:</td><td>{{ $staff->branch?->branch_name }}</td></tr>
                            @endif
                            <tr><td>Role</td><td>:</td><td>{{ $staff->roles?->name }}</td></tr>
                            <tr><td>Department</td><td>:</td><td>{{ $staff->departments?->name }}</td></tr>
                            <tr><td>Designation</td><td>:</td><td>{{ $staff->designations?->title }}</td></tr>
                            <tr><td>Phone</td><td>:</td><td>{{ $staff->mobile }}</td></tr>
                            <tr><td>Email</td><td>:</td><td>{{ $staff->email }}</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @empty
        <div class="col-lg-12 text-center">
            <p>No matching records found</p>
        </div>
    @endforelse
</div>
@if($all_staffs->hasPages())
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
            {{ $all_staffs->appends(request()->query())->links() }}
        </div>
    </div>
@endif