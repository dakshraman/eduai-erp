// add new Note
validateAddNewNote = () => {

    var note = document.getElementsByClassName("leadNote").value;


    var i = 0;

    if (note == "") {
        document.getElementsByClassName("add_note_error").innerHTML =
            "Note  field is required";
        i++;
    } else {
        document.getElementById("add_note_error").innerHTML = "";
    }

    if (i > 0) {
        return false;
    }
};
$(document).ready(function() {
    //Lead validation
    $('form[id="createLeadForm"]').validate({
        rules: {
            source_id: {
                required: true,
            },
            status_id:{
                required: true,
            },
            first_name:{
                required: true,
            },
            last_name:{
                required: false,
            },
            phone:{
                required: true,
            },
            email:{
                required: false,
            }

        }
    });



    $('form[id="add_note_form"]').validate({
        rules: {
            note: {
                required: true,
            },

        }
    });


});
