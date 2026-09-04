(function($){
    "use strict"

    $(document).ready(function(){
        $(document).on('click','.delete',function(){
            let contentId = $(this).attr('data-target');
            let modal = $(contentId);
            modal.modal('show');
        });

    });

})(jQuery);