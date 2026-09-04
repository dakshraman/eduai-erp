"use strict";

$(document).ready(function() {
    $('form[id="courseFileForm"]').validate({
            rules: {                
                file_fileName: {            
                    required: true, 
                },  
                file_status:{
                    required: true, 
                },
                file_lock:{
                    required: true,
                },                 
        
            }
        });       

});
