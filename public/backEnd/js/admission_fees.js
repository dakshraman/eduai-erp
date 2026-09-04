function toggle(target) {
    "use strict";

    const $icon = $(target).find('i');
    console.log($icon);
    $icon.toggleClass('fa-plus fa-minus');
}

(function ($) {
    "use strict";

    $(document).on('click', '.fees-extend-close', function () {
        let body = $(this).attr('data-id');
        toggle(this);
        if($(body).hasClass('d-none')){
            $(body).removeClass('d-none');
        }else{
            $(body).addClass('d-none');
        }
    });

})(jQuery);