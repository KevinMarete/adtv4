
(function($) {
    if (!$.setCookie) {
        $.extend({
            setCookie: function(c_name, value, exdays) {
                try {
                    if (!c_name) return false;
                    var exdate = new Date();
                    exdate.setDate(exdate.getDate() + exdays);
                    var c_value = escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
                    document.cookie = c_name + "=" + c_value;
                }
                catch(err) {
                    return false;
                };
                return true;
            }
        });
    };
    if (!$.getCookie) {
        $.extend({
            getCookie: function(c_name) {
                try {
                    var i, x, y,
                    ARRcookies = document.cookie.split(";");
                    for (i = 0; i < ARRcookies.length; i++) {
                        x = ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
                        y = ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
                        x = x.replace(/^\s+|\s+$/g,"");
                        if (x == c_name) return unescape(y);
                    };
                }
                catch(err) {
                    return false;
                };
                return false;
            }
        });
    };
})(jQuery);

$(function(){

// And to retrieve your cookie
// $.setCookie("nameOfCookie",'',-1); // unseting a cookie
// alert($.getCookie("nameOfCookie"));
applyCookies();
$('input').change(function(e){
    pickCookies();
});


$( "#add_patient_form" ).submit(function( event ) {
// if validationEngine then unset cookies
  // event.preventDefault();
  unsetCookies();
  

});


})


function pickCookies(){

    $.setCookie("medical_record_number", $('#medical_record_number').val(),30);
    $.setCookie("patient_number", $('#patient_number').val(),30);
    $.setCookie("last_name", $('#last_name').val(),30);
    $.setCookie("first_name", $('#first_name').val(),30);
    $.setCookie("other_name", $('#other_name').val(),30);
    $.setCookie("dob", $('#dob').val(),30);
    $.setCookie("pob", $('#pob').val(),30);
    $.setCookie("match_parent", $('#match_parent').val(),30);
    $.setCookie("age_in_years", $('#age_in_years').val(),30);
    $.setCookie("age_in_months", $('#age_in_months').val(),30);
    $.setCookie("gender", $('#gender').val(),30);
    $.setCookie("pregnant_view", $('#pregnant_view').val(),30);
    $.setCookie("pregnant_container", $('#pregnant_container').val(),30);
    $.setCookie("pregnant", $('#pregnant').val(),30);
    $.setCookie("pregnant_container", $('#pregnant_container').val(),30);
    $.setCookie("breastfeeding", $('#breastfeeding').val(),30);
    $.setCookie("weight", $('#weight').val(),30);
    $.setCookie("height", $('#height').val(),30);
    $.setCookie("surface_area", $('#surface_area').val(),30);
    $.setCookie("start_bmi", $('#start_bmi').val(),30);
    $.setCookie("phone", $('#phone').val(),30);
    $.setCookie("physical", $('#physical').val(),30);
    $.setCookie("alternate", $('#alternate').val(),30);
    $.setCookie("support_group", $('#support_group').val(),30);
    $.setCookie("support_group_listing", $('#support_group_listing').val(),30);
    $.setCookie("colmnTwo", $('#colmnTwo').val(),30);
    $.setCookie("tstatus", $('#tstatus').val(),30);
    $.setCookie("partner_status", $('#partner_status').val(),30);
    $.setCookie("dcs", $('#dcs').val(),30);
    $.setCookie("match_spouse", $('#match_spouse').val(),30);
    $.setCookie("family_planning_holder", $('#family_planning_holder').val(),30);
    $.setCookie("family_planning", $('#family_planning').val(),30);
    $.setCookie("other_allergies", $('#other_allergies').val(),30);
    $.setCookie("other_allergies_listing", $('#other_allergies_listing').val(),30);
    $.setCookie("smoke", $('#smoke').val(),30);
    $.setCookie("alcohol", $('#alcohol').val(),30);
    $.setCookie("tested_tb", $('#tested_tb').val(),30);
    $.setCookie("tb", $('#tb').val(),30);
    $.setCookie("tbcategory_view", $('#tbcategory_view').val(),30);
    $.setCookie("tbcategory", $('#tbcategory').val(),30);
    $.setCookie("tbphase_view", $('#tbphase_view').val(),30);
    $.setCookie("tbstats", $('#tbstats').val(),30);
    $.setCookie("tbphase", $('#tbphase').val(),30);
    $.setCookie("fromphase_view", $('#fromphase_view').val(),30);
    $.setCookie("ttphase", $('#ttphase').val(),30);
    $.setCookie("fromphase", $('#fromphase').val(),30);
    $.setCookie("tophase_view", $('#tophase_view').val(),30);
    $.setCookie("endp", $('#endp').val(),30);
    $.setCookie("tophase", $('#tophase').val(),30);
    $.setCookie("columnThree", $('#columnThree').val(),30);
    $.setCookie("enrolled", $('#enrolled').val(),30);
    $.setCookie("current_status", $('#current_status').val(),30);
    $.setCookie("source", $('#source').val(),30);
    $.setCookie("patient_source_listing", $('#patient_source_listing').val(),30);
    $.setCookie("transfer_source", $('#transfer_source').val(),30);
    $.setCookie("service", $('#service').val(),30);
    $.setCookie("pep_reason_listing", $('#pep_reason_listing').val(),30);
    $.setCookie("pep_reason", $('#pep_reason').val(),30);
    $.setCookie("prep_reason_listing", $('#prep_reason_listing').val(),30);
    $.setCookie("prep_reason", $('#prep_reason').val(),30);
    $.setCookie("prep_test_question", $('#prep_test_question').val(),30);
    $.setCookie("prep_test_answer", $('#prep_test_answer').val(),30);
    $.setCookie("prep_test_date_view", $('#prep_test_date_view').val(),30);
    $.setCookie("prep_test_date", $('#prep_test_date').val(),30);
    $.setCookie("prep_test_result_view", $('#prep_test_result_view').val(),30);
    $.setCookie("prep_test_result", $('#prep_test_result').val(),30);
    $.setCookie("start_of_regimen", $('#start_of_regimen').val(),30);
    $.setCookie("regimen", $('#regimen').val(),30);
    $.setCookie("servicestartedcontent", $('#servicestartedcontent').val(),30);
    $.setCookie("date_service_started", $('#date_service_started').val(),30);
    $.setCookie("service_started", $('#service_started').val(),30);
    $.setCookie("who_listing", $('#who_listing').val(),30);
    $.setCookie("who_stage", $('#who_stage').val(),30);
    $.setCookie("drug_prophylax", $('#drug_prophylax').val(),30);
    $.setCookie("drug_prophylaxis_holder", $('#drug_prophylaxis_holder').val(),30);
    $.setCookie("drug_prophylaxis", $('#drug_prophylaxis').val(),30);
    $.setCookie("isoniazid_view", $('#isoniazid_view').val(),30);
    $.setCookie("isoniazid_start_date_view", $('#isoniazid_start_date_view').val(),30);
    $.setCookie("iso_start_date", $('#iso_start_date').val(),30);
    $.setCookie("isoniazid_end_date_view", $('#isoniazid_end_date_view').val(),30);
    $.setCookie("iso_end_date", $('#iso_end_date').val(),30);
    $.setCookie("direction", $('#direction').val(),30);
}
function applyCookies(){



    $.getCookie("medical_record_number") ===  false ? void 0  : $('#medical_record_number').val($.getCookie("medical_record_number")) ;
    $.getCookie("patient_number") ===  false ? void 0  : $('#patient_number').val($.getCookie("patient_number")) ;
    $.getCookie("last_name") ===  false ? void 0  : $('#last_name').val($.getCookie("last_name")) ;
    $.getCookie("first_name") ===  false ? void 0  : $('#first_name').val($.getCookie("first_name")) ;
    $.getCookie("other_name") ===  false ? void 0  : $('#other_name').val($.getCookie("other_name")) ;
    $.getCookie("dob") ===  false ? void 0  : $('#dob').val($.getCookie("dob")) ;
    $.getCookie("pob") ===  false ? void 0  : $('#pob').val($.getCookie("pob")) ;
    $.getCookie("match_parent") ===  false ? void 0  : $('#match_parent').val($.getCookie("match_parent")) ;
    $.getCookie("age_in_years") ===  false ? void 0  : $('#age_in_years').val($.getCookie("age_in_years")) ;
    $.getCookie("age_in_months") ===  false ? void 0  : $('#age_in_months').val($.getCookie("age_in_months")) ;
    $.getCookie("gender") ===  false ? void 0  : $('#gender').val($.getCookie("gender")) ;
    $.getCookie("pregnant_view") ===  false ? void 0  : $('#pregnant_view').val($.getCookie("pregnant_view")) ;
    $.getCookie("pregnant_container") ===  false ? void 0  : $('#pregnant_container').val($.getCookie("pregnant_container")) ;
    $.getCookie("pregnant") ===  false ? void 0  : $('#pregnant').val($.getCookie("pregnant")) ;
    $.getCookie("pregnant_container") ===  false ? void 0  : $('#pregnant_container').val($.getCookie("pregnant_container")) ;
    $.getCookie("breastfeeding") ===  false ? void 0  : $('#breastfeeding').val($.getCookie("breastfeeding")) ;
    $.getCookie("weight") ===  false ? void 0  : $('#weight').val($.getCookie("weight")) ;
    $.getCookie("height") ===  false ? void 0  : $('#height').val($.getCookie("height")) ;
    $.getCookie("surface_area") ===  false ? void 0  : $('#surface_area').val($.getCookie("surface_area")) ;
    $.getCookie("start_bmi") ===  false ? void 0  : $('#start_bmi').val($.getCookie("start_bmi")) ;
    $.getCookie("phone") ===  false ? void 0  : $('#phone').val($.getCookie("phone")) ;
    $.getCookie("physical") ===  false ? void 0  : $('#physical').val($.getCookie("physical")) ;
    $.getCookie("alternate") ===  false ? void 0  : $('#alternate').val($.getCookie("alternate")) ;
    $.getCookie("support_group") ===  false ? void 0  : $('#support_group').val($.getCookie("support_group")) ;
    $.getCookie("support_group_listing") ===  false ? void 0  : $('#support_group_listing').val($.getCookie("support_group_listing")) ;
    $.getCookie("colmnTwo") ===  false ? void 0  : $('#colmnTwo').val($.getCookie("colmnTwo")) ;
    $.getCookie("tstatus") ===  false ? void 0  : $('#tstatus').val($.getCookie("tstatus")) ;
    $.getCookie("partner_status") ===  false ? void 0  : $('#partner_status').val($.getCookie("partner_status")) ;
    $.getCookie("dcs") ===  false ? void 0  : $('#dcs').val($.getCookie("dcs")) ;
    $.getCookie("match_spouse") ===  false ? void 0  : $('#match_spouse').val($.getCookie("match_spouse")) ;
    $.getCookie("family_planning_holder") ===  false ? void 0  : $('#family_planning_holder').val($.getCookie("family_planning_holder")) ;
    $.getCookie("family_planning") ===  false ? void 0  : $('#family_planning').val($.getCookie("family_planning")) ;
    $.getCookie("other_allergies") ===  false ? void 0  : $('#other_allergies').val($.getCookie("other_allergies")) ;
    $.getCookie("other_allergies_listing") ===  false ? void 0  : $('#other_allergies_listing').val($.getCookie("other_allergies_listing")) ;
    $.getCookie("smoke") ===  false ? void 0  : $('#smoke').val($.getCookie("smoke")) ;
    $.getCookie("alcohol") ===  false ? void 0  : $('#alcohol').val($.getCookie("alcohol")) ;
    $.getCookie("tested_tb") ===  false ? void 0  : $('#tested_tb').val($.getCookie("tested_tb")) ;
    $.getCookie("tb") ===  false ? void 0  : $('#tb').val($.getCookie("tb")) ;
    $.getCookie("tbcategory_view") ===  false ? void 0  : $('#tbcategory_view').val($.getCookie("tbcategory_view")) ;
    $.getCookie("tbcategory") ===  false ? void 0  : $('#tbcategory').val($.getCookie("tbcategory")) ;
    $.getCookie("tbphase_view") ===  false ? void 0  : $('#tbphase_view').val($.getCookie("tbphase_view")) ;
    $.getCookie("tbstats") ===  false ? void 0  : $('#tbstats').val($.getCookie("tbstats")) ;
    $.getCookie("tbphase") ===  false ? void 0  : $('#tbphase').val($.getCookie("tbphase")) ;
    $.getCookie("fromphase_view") ===  false ? void 0  : $('#fromphase_view').val($.getCookie("fromphase_view")) ;
    $.getCookie("ttphase") ===  false ? void 0  : $('#ttphase').val($.getCookie("ttphase")) ;
    $.getCookie("fromphase") ===  false ? void 0  : $('#fromphase').val($.getCookie("fromphase")) ;
    $.getCookie("tophase_view") ===  false ? void 0  : $('#tophase_view').val($.getCookie("tophase_view")) ;
    $.getCookie("endp") ===  false ? void 0  : $('#endp').val($.getCookie("endp")) ;
    $.getCookie("tophase") ===  false ? void 0  : $('#tophase').val($.getCookie("tophase")) ;
    $.getCookie("columnThree") ===  false ? void 0  : $('#columnThree').val($.getCookie("columnThree")) ;
    $.getCookie("enrolled") ===  false ? void 0  : $('#enrolled').val($.getCookie("enrolled")) ;
    $.getCookie("current_status") ===  false ? void 0  : $('#current_status').val($.getCookie("current_status")) ;
    $.getCookie("source") ===  false ? void 0  : $('#source').val($.getCookie("source")) ;
    $.getCookie("patient_source_listing") ===  false ? void 0  : $('#patient_source_listing').val($.getCookie("patient_source_listing")) ;
    $.getCookie("transfer_source") ===  false ? void 0  : $('#transfer_source').val($.getCookie("transfer_source")) ;
    $.getCookie("service") ===  false ? void 0  : $('#service').val($.getCookie("service")) ;
    $.getCookie("pep_reason_listing") ===  false ? void 0  : $('#pep_reason_listing').val($.getCookie("pep_reason_listing")) ;
    $.getCookie("pep_reason") ===  false ? void 0  : $('#pep_reason').val($.getCookie("pep_reason")) ;
    $.getCookie("prep_reason_listing") ===  false ? void 0  : $('#prep_reason_listing').val($.getCookie("prep_reason_listing")) ;
    $.getCookie("prep_reason") ===  false ? void 0  : $('#prep_reason').val($.getCookie("prep_reason")) ;
    $.getCookie("prep_test_question") ===  false ? void 0  : $('#prep_test_question').val($.getCookie("prep_test_question")) ;
    $.getCookie("prep_test_answer") ===  false ? void 0  : $('#prep_test_answer').val($.getCookie("prep_test_answer")) ;
    $.getCookie("prep_test_date_view") ===  false ? void 0  : $('#prep_test_date_view').val($.getCookie("prep_test_date_view")) ;
    $.getCookie("prep_test_date") ===  false ? void 0  : $('#prep_test_date').val($.getCookie("prep_test_date")) ;
    $.getCookie("prep_test_result_view") ===  false ? void 0  : $('#prep_test_result_view').val($.getCookie("prep_test_result_view")) ;
    $.getCookie("prep_test_result") ===  false ? void 0  : $('#prep_test_result').val($.getCookie("prep_test_result")) ;
    $.getCookie("start_of_regimen") ===  false ? void 0  : $('#start_of_regimen').val($.getCookie("start_of_regimen")) ;
    $.getCookie("regimen") ===  false ? void 0  : $('#regimen').val($.getCookie("regimen")) ;
    $.getCookie("servicestartedcontent") ===  false ? void 0  : $('#servicestartedcontent').val($.getCookie("servicestartedcontent")) ;
    $.getCookie("date_service_started") ===  false ? void 0  : $('#date_service_started').val($.getCookie("date_service_started")) ;
    $.getCookie("service_started") ===  false ? void 0  : $('#service_started').val($.getCookie("service_started")) ;
    $.getCookie("who_listing") ===  false ? void 0  : $('#who_listing').val($.getCookie("who_listing")) ;
    $.getCookie("who_stage") ===  false ? void 0  : $('#who_stage').val($.getCookie("who_stage")) ;
    $.getCookie("drug_prophylax") ===  false ? void 0  : $('#drug_prophylax').val($.getCookie("drug_prophylax")) ;
    $.getCookie("drug_prophylaxis_holder") ===  false ? void 0  : $('#drug_prophylaxis_holder').val($.getCookie("drug_prophylaxis_holder")) ;
    $.getCookie("drug_prophylaxis") ===  false ? void 0  : $('#drug_prophylaxis').val($.getCookie("drug_prophylaxis")) ;
    $.getCookie("isoniazid_view") ===  false ? void 0  : $('#isoniazid_view').val($.getCookie("isoniazid_view")) ;
    $.getCookie("isoniazid_start_date_view") ===  false ? void 0  : $('#isoniazid_start_date_view').val($.getCookie("isoniazid_start_date_view")) ;
    $.getCookie("iso_start_date") ===  false ? void 0  : $('#iso_start_date').val($.getCookie("iso_start_date")) ;
    $.getCookie("isoniazid_end_date_view") ===  false ? void 0  : $('#isoniazid_end_date_view').val($.getCookie("isoniazid_end_date_view")) ;
    $.getCookie("iso_end_date") ===  false ? void 0  : $('#iso_end_date').val($.getCookie("iso_end_date")) ;
    $.getCookie("direction") ===  false ? void 0  : $('#direction').val($.getCookie("direction")) ;

    $('#dob').trigger('change');
}
function unsetCookies(){

    $.setCookie("medical_record_number", $('#medical_record_number').val(),-1);
    $.setCookie("patient_number", $('#patient_number').val(),-1);
    $.setCookie("last_name", $('#last_name').val(),-1);
    $.setCookie("first_name", $('#first_name').val(),-1);
    $.setCookie("other_name", $('#other_name').val(),-1);
    $.setCookie("dob", $('#dob').val(),-1);
    $.setCookie("pob", $('#pob').val(),-1);
    $.setCookie("match_parent", $('#match_parent').val(),-1);
    $.setCookie("age_in_years", $('#age_in_years').val(),-1);
    $.setCookie("age_in_months", $('#age_in_months').val(),-1);
    $.setCookie("gender", $('#gender').val(),-1);
    $.setCookie("pregnant_view", $('#pregnant_view').val(),-1);
    $.setCookie("pregnant_container", $('#pregnant_container').val(),-1);
    $.setCookie("pregnant", $('#pregnant').val(),-1);
    $.setCookie("pregnant_container", $('#pregnant_container').val(),-1);
    $.setCookie("breastfeeding", $('#breastfeeding').val(),-1);
    $.setCookie("weight", $('#weight').val(),-1);
    $.setCookie("height", $('#height').val(),-1);
    $.setCookie("surface_area", $('#surface_area').val(),-1);
    $.setCookie("start_bmi", $('#start_bmi').val(),-1);
    $.setCookie("phone", $('#phone').val(),-1);
    $.setCookie("physical", $('#physical').val(),-1);
    $.setCookie("alternate", $('#alternate').val(),-1);
    $.setCookie("support_group", $('#support_group').val(),-1);
    $.setCookie("support_group_listing", $('#support_group_listing').val(),-1);
    $.setCookie("colmnTwo", $('#colmnTwo').val(),-1);
    $.setCookie("tstatus", $('#tstatus').val(),-1);
    $.setCookie("partner_status", $('#partner_status').val(),-1);
    $.setCookie("dcs", $('#dcs').val(),-1);
    $.setCookie("match_spouse", $('#match_spouse').val(),-1);
    $.setCookie("family_planning_holder", $('#family_planning_holder').val(),-1);
    $.setCookie("family_planning", $('#family_planning').val(),-1);
    $.setCookie("other_allergies", $('#other_allergies').val(),-1);
    $.setCookie("other_allergies_listing", $('#other_allergies_listing').val(),-1);
    $.setCookie("smoke", $('#smoke').val(),-1);
    $.setCookie("alcohol", $('#alcohol').val(),-1);
    $.setCookie("tested_tb", $('#tested_tb').val(),-1);
    $.setCookie("tb", $('#tb').val(),-1);
    $.setCookie("tbcategory_view", $('#tbcategory_view').val(),-1);
    $.setCookie("tbcategory", $('#tbcategory').val(),-1);
    $.setCookie("tbphase_view", $('#tbphase_view').val(),-1);
    $.setCookie("tbstats", $('#tbstats').val(),-1);
    $.setCookie("tbphase", $('#tbphase').val(),-1);
    $.setCookie("fromphase_view", $('#fromphase_view').val(),-1);
    $.setCookie("ttphase", $('#ttphase').val(),-1);
    $.setCookie("fromphase", $('#fromphase').val(),-1);
    $.setCookie("tophase_view", $('#tophase_view').val(),-1);
    $.setCookie("endp", $('#endp').val(),-1);
    $.setCookie("tophase", $('#tophase').val(),-1);
    $.setCookie("columnThree", $('#columnThree').val(),-1);
    $.setCookie("enrolled", $('#enrolled').val(),-1);
    $.setCookie("current_status", $('#current_status').val(),-1);
    $.setCookie("source", $('#source').val(),-1);
    $.setCookie("patient_source_listing", $('#patient_source_listing').val(),-1);
    $.setCookie("transfer_source", $('#transfer_source').val(),-1);
    $.setCookie("service", $('#service').val(),-1);
    $.setCookie("pep_reason_listing", $('#pep_reason_listing').val(),-1);
    $.setCookie("pep_reason", $('#pep_reason').val(),-1);
    $.setCookie("prep_reason_listing", $('#prep_reason_listing').val(),-1);
    $.setCookie("prep_reason", $('#prep_reason').val(),-1);
    $.setCookie("prep_test_question", $('#prep_test_question').val(),-1);
    $.setCookie("prep_test_answer", $('#prep_test_answer').val(),-1);
    $.setCookie("prep_test_date_view", $('#prep_test_date_view').val(),-1);
    $.setCookie("prep_test_date", $('#prep_test_date').val(),-1);
    $.setCookie("prep_test_result_view", $('#prep_test_result_view').val(),-1);
    $.setCookie("prep_test_result", $('#prep_test_result').val(),-1);
    $.setCookie("start_of_regimen", $('#start_of_regimen').val(),-1);
    $.setCookie("regimen", $('#regimen').val(),-1);
    $.setCookie("servicestartedcontent", $('#servicestartedcontent').val(),-1);
    $.setCookie("date_service_started", $('#date_service_started').val(),-1);
    $.setCookie("service_started", $('#service_started').val(),-1);
    $.setCookie("who_listing", $('#who_listing').val(),-1);
    $.setCookie("who_stage", $('#who_stage').val(),-1);
    $.setCookie("drug_prophylax", $('#drug_prophylax').val(),-1);
    $.setCookie("drug_prophylaxis_holder", $('#drug_prophylaxis_holder').val(),-1);
    $.setCookie("drug_prophylaxis", $('#drug_prophylaxis').val(),-1);
    $.setCookie("isoniazid_view", $('#isoniazid_view').val(),-1);
    $.setCookie("isoniazid_start_date_view", $('#isoniazid_start_date_view').val(),-1);
    $.setCookie("iso_start_date", $('#iso_start_date').val(),-1);
    $.setCookie("isoniazid_end_date_view", $('#isoniazid_end_date_view').val(),-1);
    $.setCookie("iso_end_date", $('#iso_end_date').val(),-1);
    $.setCookie("direction", $('#direction').val(),-1);
}