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
<section class="student_list_grid_layout {{ generalSetting()->staff_grid_view == 1 ? 'student _view active grid' : '' }}">
    <div id="student_grid_container">
        @include('backEnd.humanResource.partial.staff-grid')
    </div>
</section>

@push('script')
<script>
    $('#student_grid_search').on('keyup', function() {
        let keyword = $(this).val();
        $.ajax({
            url: "{{ route('staffGrid') }}",
            type: "GET",
            data: {
                quick_search: keyword,
                @if (moduleStatusCheck('Branch'))
                branch_id: $('#branch_id').val(),
                @endif
                role_id: $('#role_id').val(),
                staff_no: $('#staff_no').val(),
                staff_name: $('#staff_name').val(),
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