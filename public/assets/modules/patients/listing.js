$(function () {
    var base_url = $("#base_url").val();
    //Patient Listing DataTables
    var oTable = $('#patient_listing').dataTable({
        "bProcessing": true,
        "bDestroy": true,
        "sAjaxSource": base_url + '/get-patients',
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "bStateSave": true,
        "sDom": '<"H"T<"clear">lfr>t<"F"ip>',
        "bAutoWidth": false,
        "bDeferRender": true,
        "bInfo": true,
        "aoColumnDefs": [{"bSearchable": true, "aTargets": [0, 1, 3, 4]}, {"bSearchable": false, "aTargets": ["_all"]}]
    });

    //Filter Table
    oTable.columnFilter({
        aoColumns: [{type: "text"}, {type: "text"}, null, {type: "text"}, {type: "text"}, null]}
    );

    //Fade Out Message
    setTimeout(function () {
        $(".message").fadeOut("2000");
    }, 6000);
});

function filter(url) {
    var base_url = $("#base_url").val();
    var oTable = $('#patient_listing').dataTable({
        "bProcessing": true,
        "bDestroy": true,
        "sAjaxSource": base_url + url,
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "bStateSave": true,
        "sDom": '<"H"T<"clear">lfr>t<"F"ip>',
        "bAutoWidth": false,
        "bDeferRender": true,
        "bInfo": true,
        "aoColumnDefs": [{"bSearchable": true, "aTargets": [0, 1, 3, 4]}, {"bSearchable": false, "aTargets": ["_all"]}]
    });

    //Filter Table
    oTable.columnFilter({
        aoColumns: [{type: "text"}, {type: "text"}, null, {type: "text"}, {type: "text"}, null]}
    );

    //Fade Out Message
    setTimeout(function () {
        $(".message").fadeOut("2000");
    }, 6000);
}

$(document).ready(function () {
    $('#filter').change(function () {
        var choice = $('#filter').val();
        if (choice == 0) {
            var new_url = '/get-patients';
        } else if (choice == 1) {
            var new_url = '/get-patients/inactive';
        } else if (choice == 2) {
            var new_url = '/get-patients/all';
        }
        filter(new_url);
    });

});