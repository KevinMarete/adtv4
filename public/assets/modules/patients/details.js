$(function () {
    //Get data from hidden element in form
    // var base_url = $("#hidden_data").data("baseurl");
    base_url = getBaseUrlLinux();
    var patient_id = $("#hidden_data").data("patient");
    var patient_status = $("#hidden_data").data("status");
    var relations_msg = $("#hidden_data").data("message");

    //Disable these Buttons
    $("#dispense_btn").bind('click', false);
    $("#dispense_btn").attr("disabled", "disabled");
    $("#patient_info").attr("disabled", "disabled");
    $("#viral_load").attr("disabled", "disabled");

    //Display Relations Message
    if (relations_msg != '')
    {
        bootbox.alert("<h4>Dependant/Spouse Message </h4>\n\<hr/><span>" + relations_msg + "</span>");
    }
    //Check Patient Status
    if (patient_status != "active") {
        bootbox.alert("<h4>Status Not Active</h4>\n\<hr/><center>Cannot Dispense to Patient</center>");
        $("#dispense_btn").bind('click', false);
        $("#dispense_btn").attr("disabled", "disabled");
    } else {
        $("#dispense_btn").unbind('click', false);
        $("#dispense_btn").attr("disabled", false);
    }

    //Define resources for requests
    var page_url = base_url + "patient/load_form/patient_details";
    var patient_url = base_url + "patient/load_patient/" + patient_id;
    var visits_url = base_url + "patient/get_visits/" + patient_id;
    var summary_url = base_url + "patient/load_summary/" + patient_id;
    var spinner_url = base_url + "assets/images/loading_spin.gif";

    //Load Page Data(form.js) then load Patient Data(details.js) after that sanitize form (details.js)
    getPageData(page_url).always(function () {
        getPatientData(patient_url).always(function () {
            sanitizeForm();
        });
    });

    //Setup Dispensing History Datatable
    createTable("#dispensing_history", visits_url, 0, 'desc');

    //Patient Info Modal
    $("#patient_details").dialog({
        width: 1200,
        modal: true,
        height: 600,
        autoOpen: false,
        show: 'fold'
    });

    //Viral Load Modal
    $("#viral_load_details").dialog({
        width: 700,
        modal: true,
        height: 400,
        autoOpen: false,
        show: 'fold'
    });

    $("#patient_prescriptions").dialog({
        width: 1100,
        modal: true,
        height: 400,
        autoOpen: false,
        show: 'fold'
    });

    //Open Viral Load Modal
    $("#viral_load").on('click', function () {
        getViralLoad();
        $("#viral_load_details").dialog("open");
    });

    $("#view_prescription").click(function () {

        $("#patient_prescriptions").dialog("open");
        //function to get last viral load for this patient
        get_patient_prescriptions($("#patient_number_ccc").val())
    });


    //Show Patient Summary
    $("#patient_info").on('click', function () {
        //Load Spinner
        var spinner = '<center><img style="width:30px;" src="' + spinner_url + '"></center>';
        $(".spinner_loader").html(spinner);

        //Open Modal
        $("#patient_details").dialog("open");
        var patient_number_ccc = $("#patient_number_ccc").val()
        $("#details_patient_number_ccc").text(patient_number_ccc);
        $("#details_first_name").text($("#first_name").val());
        $("#details_last_name").text($("#last_name").val());
        $("#details_gender").text($("#gender").text());
        $("#details_current_age").text($("#age").val());
        $("#details_date_enrolled").text($("#date_enrolled").val());
        $("#details_current_status").text($("#current_status").text());

        getDispensing();
        getRegimenChange();
        getAppointmentHistory();
        getViralResult(patient_number_ccc);
    });

});

function getPatientData(url) {
    var checkbox = ["sms_consent", "disclosure", "differentiated_care"];
    var multiselect = ["fplan", "other_illnesses", "drug_allergies", "drug_prophylaxis"];

    //Get JSON data for patient details page
    return  $.getJSON(url, function (resp) {
        $.each(resp, function (index, value) {
            //Append JSON elements to DOM
            if (jQuery.inArray(index, checkbox) != -1) {
                //Select checkbox
                addToCheckbox(value);

            } else if (jQuery.inArray(index, multiselect) != -1) {
                //MultiSelectBox
                addToMultiSelect(index, value);

            } else {
                $("#" + index).val(value);
                if (index == 'age') {
                    checkGuardian(value)
                }
            }
        });
    });
}

function checkGuardian(age) {
    var age = parseInt(age)
    var facility_age_url = "../../../dispensement/getFacililtyAge";
    $.getJSON(facility_age_url, function (data) {
        var adult_age = parseInt(data[0].adult_age);
        if (age < adult_age) {
            $(".parent_view").show();
        } else {
            $(".parent_view").hide();
        }
    });
}

function addToCheckbox(div) {
    $("#" + div).attr("checked", "true");
}

function addToMultiSelect(div, data) {
    var values = data.split(",");
    $("#" + div).val(values);

}

function sanitizeForm() {
    //Remove none selected options
    $("#details_frm select option:not(:selected)").remove();
    //Disable Elements
    $("input[type='text'],select,textarea").attr("disabled", 'disabled');

    //Enable DataTable Elements
    $(".dataTables_filter input").attr("disabled", false);
    $(".dataTables_length select").attr("disabled", false);

    //Enable these Buttons
    $("#patient_info").attr("disabled", false);
    $("#viral_load").attr("disabled", false);
    $("#dispensing_history_filter input").attr("disabled", false);
    $("#dispensing_history_length select").attr("disabled", false);

    //Show/Hide isoniazid view
    $(".isoniazid").css("display", "none");
    $("#drug_prophylaxis > option").each(function () {
        var drug_prophylaxis = $("#drug_prophylaxis").val();
        if (drug_prophylaxis != null || drug_prophylaxis != " ") {
            for (var i = 0; i < drug_prophylaxis.length; i++) {
                var selected_obj = $('input[name="drug_prophylaxis"][type="checkbox"][value="' + drug_prophylaxis[i] + '"]');
                selected_obj.attr('checked', true);
                if (drug_prophylaxis[i] == 3) {
                    $(".isoniazid").show();
                }
            }
        }
    });

    //Show/Hide pep/prep div depending on the patient type of service
    $(".pep_reason").css("display", "none");
    $(".prep_reason").css("display", "none");
    $(".prep_test_question").css("display", "none");
    $("#service > option").each(function () {
        if (this.text === "PEP") {
            $(".pep_reason").show();
            $(".who_stage").hide();
            $(".drug_prophylaxis").hide();
        } else if (this.text === "PREP") {
            $(".prep_reason").show();
            $(".prep_test_question").show();
            $(".who_stage").hide();
            $(".drug_prophylaxis").hide();
        }
    });

    //Show/Hide tb category and phase
    $(".tb_category_phase").css("display", "none");
    $(".tb_period").css("display", "none");
    if ($("#tb").val() == 1) {
        $(".tb_category_phase").show();
        $(".tb_period").show();
    }

    //Hide match spouse
    $("#partner_status > option").each(function () {
        if (this.text === "No Partner") {
            $(".secondary_spouse").hide();
        }
    });

    //Hide family planning
    if ($("#fplan").val() == "" || $("#fplan").val() == null) {
        $("#fplan").hide();
    }

    //hide chronic diseases div
    if ($("#other_illnesses").val() == "" || $("#other_illnesses").val() == null) {
        $("#other_illnesses").hide();
    }

    //Hide other chronic diseases div
    if ($("#other_chronic").val() == "" || $("#other_chronic").val() == null) {
        $("#other_chronic").hide();
    }

    //Hide transfer_from div
    $(".transfer_from").hide();
    if ($("#source").text().toLowerCase().replace(' ', '_') == 'transfer_in') {
        $(".transfer_from").show();
    }

    //Show/Hide pregnant_view
    $("#pregnant_view").hide()
    if ($("#gender option:selected").text().toLowerCase() == 'female') {
        $("#pregnant_view").show()
    }
}

function getViralLoad() {
    var patient_no = $("#patient_number_ccc").val();
    var link = base_url + "auto_management/get_viral_load/" + patient_no;
    var table = '';

    $.getJSON(link, function (data) {
        if (data && data.items && data.items.length == 0) {
            table += '<tr><td colspan="3">No Data Available</td></tr>';
        } else {
            $.each(data, function (i, vldata) {
                table += '<tr><td>' + vldata.date_collected + '</td><td>' + vldata.test_date + '</td><td>' + vldata.result + '</td><td>' + vldata.justification + '</td></tr>';
            });
            $("#viral_load_data tbody").empty();
            $("#viral_load_data tbody").append(table);
        }
    });
}

function getDispensing() {
    var patient_no = $("#patient_number_ccc").val();
    patient_no = patient_no.toString().trim();

    $.ajax({
        url: "../../../patient/getSixMonthsDispensing/" + patient_no,
        type: 'POST',
        success: function (data) {
            $("#patient_pill_count>tbody").empty();
            $("#patient_pill_count").append(data);
        }
    });
}

function getRegimenChange() {
    var patient_no = $("#patient_number_ccc").val();
    patient_no = patient_no.toString().trim();

    $.ajax({
        url: "../../../patient/getRegimenChange/" + patient_no,
        type: 'POST',
        success: function (data) {
            $("#patient_regimen_history>tbody").empty();
            $("#patient_regimen_history").append(data);
        }
    });
}

function getAppointmentHistory() {
    var patient_no = $("#patient_number_ccc").val();
    patient_no = patient_no.toString().trim();
    $.ajax({
        url: "../../../patient/getAppointmentHistory/" + patient_no,
        type: 'POST',
        success: function (data) {
            $("#patient_appointment_history>tbody").empty();
            $("#patient_appointment_history").append(data);

        }
    });

}


function getViralResult(ccc_no) {
    var data_source =  "../../../patient/get_Last_vl_result/" + ccc_no;
    $("#viral_load_date").text('N/A');
    $("#viral_load_result").text('N/A');
    $.getJSON(data_source, function (data) {

        $.each(data, function (key, val) {
            $("#viral_load_date").text(val.test_date);
            $("#viral_load_result").text(val.result)
        });

    });
}

function get_patient_prescriptions(ccc_no) {
    var data_source ="../../../dispensement_management/getPrescriptions/" + ccc_no;
    var patient_id = $("#hidden_data").data("patient");

    $.getJSON(data_source, function (data) {
        $('#prescriptions_data tbody').empty();

        $.each(data.prescription_details, function (key, val) {

            $('#prescriptions_data tbody').append('<tr><td>' + val.drug_name + '</td>' +
                    '<td>' + val.strength + '</td>' +
                    '<td>' + val.dosage + '</td>' +
                    '<td>' + val.frequency + '</td>' +
                    '<td>' + val.duration + '</td>' +
                    '<td>' + val.quantity_prescribed + '</td>' +
                    '<td>' + val.prescription_notes + '</td><tr>');

            $('#dispense_prescription_btn').show();
            $('#dispense_prescription_btn').attr('href', '../../../dispensement_management/dispense/' + patient_id + '?pid=' + val.drug_prescriptionid);
        });

        $('#prescriptions_data table').append('<a id="dispense_btn" class="btn btn-default" href="' + base_url + 'dispensement_management/dispense/' + patient_id + '"><strong>Dispense to Patient</strong></a>');
    });

}

function getBaseUrlLinux() {
    return location.protocol + "//" + location.host + '/ADT/public/';
}