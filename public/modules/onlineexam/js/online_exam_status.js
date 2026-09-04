(function($){
    "use strict"
    $(document).ready(function(){

        
        var divs = document.querySelectorAll('.show_status');

        [].forEach.call(divs, function(div) {
            // do whatever
            var element_id = div.id;

            var today = new Date();
            var date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();
            var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();


            var count_date = $('#' + element_id).attr("data-end_date");
            var count_start_time = date + ' ' + time;
            var count_end_time = $('#' + element_id).attr("data-start_time");

            var countEndTime = new Date(count_end_time).getTime();

            // Update the count down every 1 second
            var currentTime = setInterval(function() {
              
                var countStartTime = new Date().getTime();

                // Find the distance between now and the count down date
                var distance = countEndTime - countStartTime;

                // Time calculations for days, hours, minutes and seconds
                var days = Math.floor(distance / (1000 * 3600 * 24));
              
                var hours = Math.floor(
                    (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
                );
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                console.log(days +
                    " days " +
                    hours +
                    " hours " +
                    minutes +
                    " min " +
                    seconds +
                    " sec");
         
                if (distance < 0) {
                    clearInterval(currentTime);
                    var audio = new Audio(
                        "{{ assetPath('modules/onlineexam/pristine-609.mp3') }}");
                    audio.play()
                    var select_exam = $('#' + element_id).text('Take Exam');
                    var url = 'take-online-exam/' + element_id;
                    select_exam.text('Take Exam');
                    select_exam.css("background-color", "green");
                    select_exam.attr('href', url);
                    console.log('time over');
                }
            }, 1000);

        });

    });
})(jQuery)