"use strict";

function adjustImageWidth() {
    var width = $(window).width();
    var image = $("#image_paybox_style");

    if (width < 611) {
        image.css("width", "100%");
    } else {
        image.css("width", "100%");
    }
}

adjustImageWidth();

$(window).resize(function () { 
    adjustImageWidth();
});

$(document).ready(function () {
    const redoButton = $('<button class="redoButton d-none">X</button>');

    $('.video_palyer_box').append(redoButton);

    redoButton.css({
        position: 'absolute',  
        top: '10px',          
        right: '10px',        
        backgroundColor: '#415094',
        color: '#fff',        
        padding: '8px 12px',   
        border: 'none',        
        borderRadius: '4px',  
        cursor: 'pointer',     
        fontSize: '14px',     
        zIndex: 9999,        
        transition: 'background-color 0.3s ease' 
    });

    
    redoButton.hover(function () {
        $(this).css('background-color', '#415086');
    }, function () {
        $(this).css('background-color', '#415034'); 
    });

    $('.videoList_toggleBtn').on('click', function () {
        const videoList = $('.video_palyer_list_wrapper');
        const videoPlayerBox = $('.video_palyer_box');

        if (videoList.css('display') === 'none') {
            videoList.css('display', 'flex');
            videoPlayerBox.css('width', 'calc(100% - 420px)');
            $(this).text('='); 

            redoButton.removeClass('d-none');
        } else {
            videoList.css('display', 'none');
            videoPlayerBox.css('width', '100%');
            $(this).text('Show Video List'); 

            redoButton.removeClass('d-none');
        }
    });

    redoButton.on('click', function () {
        const videoList = $('.video_palyer_list_wrapper');
        const videoPlayerBox = $('.video_palyer_box');

        videoList.css('display', 'flex');
        videoPlayerBox.css('width', 'calc(100% - 420px)');

        $('.videoList_toggleBtn').text('=');

        $(this).addClass('d-none');
    });
});

$("#autoNext").change(function () {
    if ($(this).is(':checked')) {
        localStorage.setItem('autoNext', 1);
    } else {
        localStorage.setItem('autoNext', 0);

    }

});

if (localStorage.getItem('autoNext') == 0) {
    $("#autoNext").prop('checked', false);
}
$("#autoNext").trigger('change');

