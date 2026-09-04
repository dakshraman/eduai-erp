(function ($) {
  "use strict";
  $(document).ready(function () {
    $(document).on("click", ".print-btn", function () {
      let element = $(this).attr("data-element");
      printElement(element);
    });
    $(document).on("change", "#class_id", function () {
      let url = $(this).attr("data-url");
      let class_id = $(this).val();
      $.ajax({
        url: url,
        data: {
          class_id: class_id,
        },
        method: "get",
      }).done(function (response) {
        if (response.status == "success") {
          $("#section_id").html(response.view);
          $("#section_id").niceSelect("update");
        } else {
          toastr.error(response.message, '@lang("common.error")');
        }
      });
    });

    $(document).on("change", "#section_id", function () {
      let url = $(this).attr("data-url");
      let class_id = $("#class_id").val();
      let section_id = $("#section_id").val();
      $.ajax({
        url: url,
        data: {
          class_id: class_id,
          section_id: section_id,
        },
        method: "get",
      }).done(function (response) {
        if (response.status == "success") {
          $("#template_id").html(response.view);
          $("#template_id").niceSelect("update");
        } else {
          toastr.error(response.message, '@lang("common.error")');
        }
      });
    });

    $(document).on("change", ".select-all", function () {
      $(".item-checkbox").prop("checked", $(this).prop("checked"));
    });

    function printElement(id) {
      const element = document.getElementById(id);

      if (!element) {
        console.error("Element not found: " + id);
        return;
      }

      // Copy your entire <head> section from the current HTML page
      const headContent = document.head.innerHTML;

      const printWindow = window.open("", "_blank", "width=1200,height=900");

      printWindow.document.open();
      printWindow.document.write(`
        <html>
        <head>
            ${headContent}   <!-- your real HTML HEAD is inserted here -->

            <style>
                /* Keep design exactly */
                html, body {
                    margin: 0;
                    padding: 0;
                    background: #fff;
                }
                #printContainer {
                    width: 100%;
                }
            </style>
        </head>

        <body>
            <div id="printContainer">${element.outerHTML}</div>
        </body>
        </html>
    `);

      printWindow.document.close();

      printWindow.onload = function () {
        printWindow.focus();
        printWindow.print();
        printWindow.close();
      };
    }
  });
})(jQuery);
