{{-- Grid View --}}
<style>
.pagination_item a {
    padding: 6px 0;
    width: 30px;
    text-align: center;
    color: var(--text-color);
    font-size: 12px;
    margin-right: 5px;
    border-radius: 5px;
    border: 0px;
    -webkit-transition: all 0.4s ease 0s;
    -moz-transition: all 0.4s ease 0s;
    -o-transition: all 0.4s ease 0s;
    transition: all 0.4s ease 0s;
    position: relative;
    display: block;
    padding: .5rem .75rem;
    margin-left: -1px;
    line-height: 1.25;
    background-color: #fff;
}
.pagination_item a:hover,
.pagination_item a.current{
    background: linear-gradient(90deg, var(--gradient_1) 0%, var(--gradient_3) 100%);
    color: #FFFFFF;
}
.pagination_item.disabled:hover {
    color: #FFFFFF;
    cursor: not-allowed;
}
</style>
<section class="student_list_grid_layout {{ generalSetting()->student_grid_view == 1 ? 'student_view active grid' : '' }}">
    <div id="student_grid_container">
        @include('backEnd.studentInformation.partial.student-grid')
    </div>
</section>
@push('script')
<script>
    $('#student_grid_search').on('keyup', function() {
        let keyword = $(this).val();
        $.ajax({
            url: "{{ route('studentGrid') }}",
            type: "GET",
            data: {
                quick_search: keyword,
                @if (moduleStatusCheck('Branch'))
                class: $('#branch_id').val(),
                @endif
                academic_year: $('#academic_id').val(),
                class: $('#class').val(),
                section: $('#section').val(),
                shift_id: $('#shift_id').val(),
                roll_no: $('#roll').val(),
                name: $('#name').val(),
                un_session_id: $('#un_session').val(),
                un_academic_id: $('#un_academic').val(),
                un_faculty_id: $('#un_faculty').val(),
                un_department_id: $('#un_department').val(),
                un_semester_label_id: $('#un_semester_label').val(),
                un_section_id: $('#un_section').val()
            },
            success: function(data) {
                $('#student_grid_container').html(data);
            }
        });
    });
    $(document).on('click', '.pagination_item a', function (e) {
        e.preventDefault()
        let url = $(this).attr('href');
        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                $('#student_grid_container').html(data);
            },
            error: function () {
                toastr.error("Failed to load data", "Error");
            }
        });
    });
</script>
@endpush