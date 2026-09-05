function updateBulkCertificatePrintUrl() {
    var certificate = $("#certificate").val();
    var printUrl = $("#bulk_certificate_print_url").val();
    var gridGap = $("#certificate_grid_gap").val();
    var sList = "";

    $(".generate-certificate-print:checked").each(function() {
        if ($(this).val() != "") {
            sList += sList == "" ? $(this).val() : "-" + $(this).val();
        }
    });

    if (sList != "" && printUrl) {
        $("#bulk-genearte-certificate-print-button").attr(
            "href",
            printUrl
                .replace("__USER_IDS__", sList)
                .replace("__CERTIFICATE_ID__", certificate) +
                (gridGap ? "?grid_gap=" + encodeURIComponent(gridGap) : "")
        );
        $("#bulk-genearte-certificate-print-button").attr("target", "_blank");
    } else {
        $("#bulk-genearte-certificate-print-button").attr("href", "javascript:;");
        $("#bulk-genearte-certificate-print-button").removeAttr("target");
    }
}

$(document).on("click", ".bulk-generate-certificate-print-all", function(event) {
    $(".generate-certificate-print").prop("checked", $(this).prop("checked"));
    updateBulkCertificatePrintUrl();
});

$(document).on("click", ".generate-certificate-print", function(event) {
    var total = $(".generate-certificate-print").length;
    var selected = $(".generate-certificate-print:checked").length;

    $(".bulk-generate-certificate-print-all").prop("checked", total > 0 && total === selected);
    updateBulkCertificatePrintUrl();
});
