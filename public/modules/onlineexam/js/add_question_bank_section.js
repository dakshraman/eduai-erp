function uploadImage(id) {
  "use strict";
  
  $(".show_file_name" + id).html("File Selected");
  var select_image = $("#question_image" + id);
  console.log("initial image value " + select_image.val());
  var file = document.getElementById("question_image" + id).files[0];
  if (file) {
    if (
      file.type == "image/jpeg" ||
      file.type == "image/png" ||
      file.type == "image/jpg"
    ) {
      var reader = new FileReader();
      reader.onload = function (e) {
        $("#image_preview" + id).attr("src", e.target.result);
      };
      console.log("#image_preview" + id);
      reader.readAsDataURL(file); // convert to base64 string
    } else {
      $(".show_file_name" + id).html("Invalid file type");
      $("#question_image" + id).val(null);
    }
  }
}

(function ($) {
  "use strict";

  $(document).ready(function () {
    $(document).on('change','.multiple_images input[type="file"]',function () {
      $(this).closest(".multiple_images").find(".show_file_name");
      $(this)
        .closest(".multiple_images")
        .find(".show_file_name")
        .html("File Selected");
    });

    $(document).on('click',"#question_bank_submit", function (e) {
      e.preventDefault();
      var ck_box = $('.multiple-images input[type="checkbox"]:checked').length;
      var answer_type = $("#answer_type").val();
      var question_type = $("#question-type").val();

      if (ck_box > 0) {
        if ($("input[name='images[]']").val() == "") {
        
          toastr.warning("Please Select Valid Option Images", "Warning", {
            timeOut: 5000,
          });
        } else {
          if (answer_type == "radio" && ck_box > 1) {
            toastr.warning("Please Select One Correct Answer", "Warning", {
              timeOut: 5000,
            });
          } else {
            $("#question_bank").submit();
          }
        }
      } else {
        if (
          question_type != "MI" ||
          question_type != "PM" ||
          question_type != "IMQ"
        ) {
          $("#question_bank").submit();
        } else {
          toastr.warning("Please Select Correct  Answer", "Warning", {
            timeOut: 5000,
          });
        }
      }
    });

    $(document).on("click", ".common-checkbox", function () {
      var answer_type = $("#answer_type").val();
      console.log(answer_type);
      if (answer_type == "radio") {
        $(".common-checkbox").prop("checked", false);
        $(this).prop("checked", true);
      }
    });
  });
})(jQuery);
