function positionMobile(ev) {
  "use strict";

  mobile_last_move = ev;
}

function allowDrop(ev) {
  "use strict";

  ev.preventDefault();
}

function drag(ev) {
  "use strict";

  if (ev.type === "touchstart") {
    mobile_item_selec = ev.target
      .closest(".drag-drawflow")
      .getAttribute("data-node");
  } else {
    ev.dataTransfer.setData("node", ev.target.getAttribute("data-node"));
  }
}

function drop(ev) {
  "use strict";

  if (ev.type === "touchend") {
    let parentdrawflow = document
      .elementFromPoint(
        mobile_last_move.touches[0].clientX,
        mobile_last_move.touches[0].clientY
      )
      .closest("#drawflow");
    if (parentdrawflow != null) {
      addNodeToDrawFlow(
        mobile_item_selec,
        mobile_last_move.touches[0].clientX,
        mobile_last_move.touches[0].clientY
      );
    }
    mobile_item_selec = "";
  } else {
    ev.preventDefault();
    let data = ev.dataTransfer.getData("node");
    addNodeToDrawFlow(data, ev.clientX, ev.clientY);
  }
}

function checkConnection(connection, status) {
  "use strict";

  let input_id = connection.input_id;
  let output_id = connection.output_id;  
  let inputOptionType = $("#node-" + input_id).find(".optionType");
  let outOptionType = $("#node-" + output_id).find(".optionType");
  let inputIndex = inputOptionType.data("index");
  let outputIndex = outOptionType.data("index");
  let output =  outputIndex + "-" + output_id + "|" + inputIndex + "-" + input_id;

  if (status == "add") {
    options.push(output);
  } else if (status == "remove") {
    options = options.filter(function (elem) {
      return elem != output;
    });
  }
  $("#connection").val(options);
}

(function ($) {
  "use strict";

  $(document).ready(function () {

    $(document).on("click", "#create-qus-option", function (event) {
      let qusItem = $("#number_of_qus");
      let qusTitle = '';

      let qus = editor.getNodesFromName("qus");
      $.each(qus, function (index, val) {
        $("#node-" + val)
          .closest(".parent-node")
          .remove();
      });

      for (i = 0; i < qusItem.val(); i++) {
        addNodeToDrawFlow("qus", 600, 400, qusTitle, i);
      }
    });

    $(document).on("click", "#create-ans-option", function (event) {
      let ansItem = $("#number_of_ans");
      let ansTitle = '';

      let ans = editor.getNodesFromName("ans");
      $.each(ans, function (index, val) {
        $("#node-" + val)
          .closest(".parent-node")
          .remove();
      });

      for (i = 0; i < ansItem.val(); i++) {
        addNodeToDrawFlow("ans", 900, 400, ansTitle, i);
      }
    });

  });

})(jQuery);
