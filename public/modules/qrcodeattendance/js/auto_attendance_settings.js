(function($) {
    "use strict";
    $(document).ready(function(){
        

        $(document).on('change','#copy',function(){
            let time = $("#first_item").val();
            let inputs = $(".time");
            if ($(this).is(':checked')) {
                $(".time").val(time);
            } else {
                $(".time").val("");
            }
        });
        
    });
})(jQuery);
