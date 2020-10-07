$(function(){
    //Get data from hidden element in form
	var base_url = $("#hidden_data").data("baseurl");
	var patient_id = $("#hidden_data").data("patient_id");

	//Disable these Buttons
	$("#save_btn").bind('click', false);
	$("#save_btn").attr("disabled", "disabled");
	$("#print_btn").attr("disabled", "disabled");
	$("#reset_btn").attr("disabled", "disabled");
        
    //Define resources for requests
	var page_url = base_url + "patient_management/load_form/dispensing_frm";
	var patient_url = base_url + "dispensement_management/get_patient_data/" + patient_id ;
	var visits_url = base_url + "patient_management/get_visits/" + patient_id;
	var summary_url = base_url + "patient_management/load_summary/" + patient_id;
	var spinner_url = base_url + "assets/images/loading_spin.gif";

	//Load Page Data(form.js) then load Patient Data(details.js) after that sanitize form (details.js)
    getPageData(page_url).always( function(){
	    getPatientData(patient_url).always( function(){
            //sanitizeForm();
	    });
	});

	//Setup Dispensing History Datatable
	/*
	createTable("#dispensing_history",visits_url,0,'desc');

	//Patient Info Modal
	$("#patient_details").dialog({
        width : 1200,
        modal : true,
        height: 600,
        autoOpen : false,
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

    //Open Viral Load Modal
    $("#viral_load").on('click', function() {
		getViralLoad();
		$("#viral_load_details").dialog("open");
	});

    //Show Patient Summary
	$("#patient_info").on('click',function() {
		//Load Spinner
		var spinner ='<center><img style="width:30px;" src="'+spinner_url+'"></center>';
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
	*/

});

function getPatientData(url){
	//Get JSON data for patient details page
	return  $.getJSON( url ,function( resp ) {
			    $.each( resp , function( index , value ) {
			        //Append JSON elements to DOM
		            $( "#"+index ).val( value );
		            $( "."+index ).html( value );
			    });
			});
}