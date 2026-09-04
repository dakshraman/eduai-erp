function uploadImage(id) {
    "use strict"
  $(".show_file_name" + id).html("File Selected");
  var select_image = $("#question_image" + id);
  var file = document.getElementById("question_image" + id).files[0];
  if (file) {
    if (
      file.type == "image/jpeg" ||
      file.type == "image/png" ||
      file.type == "image/jpg"
    ) {
      var img = new Image();

      img.src = window.URL.createObjectURL(file);

      img.onload = function () {
        var width = img.naturalWidth,
          height = img.naturalHeight;
        window.URL.revokeObjectURL(img.src);
        if (width <= 650 && height <= 450) {
          $(".show_file_name" + id).html(file.name.substr(0, 10));
        } else {
          $(".show_file_name" + id).html("Invalid image dimension");
          $("#question_image" + id).val(null);
        }
      };
    } else {
      $(".show_file_name" + id).html("Invalid file type");
      $("#question_image" + id).val(null);
    }
  }
}

(function ($) {
  "use strict";

  $(document).ready(function () {
    $(document).ready(function () {
      $('.multiple_images input[type="file"]').change(function () {
        console.log(
          $(this).closest(".multiple_images").find(".show_file_name")
        );
        $(this)
          .closest(".multiple_images")
          .find(".show_file_name")
          .html("File Selected");
      });
    });

    $("#question_bank_submit").click(function (e) {
      e.preventDefault();
      console.log(e);
      var ck_box = $('.multiple-images input[type="checkbox"]:checked').length;
      var answer_type = $("#answer_type").val();
      var question_type = $("#question-type").val();
      console.log(answer_type);

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
        if (question_type != "MI") {
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

      if (answer_type == "radio") {
        $(".common-checkbox").prop("checked", false);
        $(this).prop("checked", true);
      }
    });

    $(document).on("click", ".feature_image_ck", function () {
      console.log(this.src);
     
      $(".question_image_url").attr("src", this.src);
      $(".question_image_preview").modal("show");
    });

    $("div.dataTables_wrapper img").css({
      width: "100px",
      height: "100%",
    });

    $(document).on('click','.delete',function(){
        let contentId = $(this).attr('data-target');
        let modal = $(contentId);
        modal.modal('show');
    });

  });
})(jQuery);
