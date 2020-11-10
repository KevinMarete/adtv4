<style>
    .content{padding:10em 1% 5% 1%;background-color: #FFA8E7;zoom:0.8;}
    .dispensing-field input, .dispensing-field select,#tbl-dispensing-drugs input, #tbl-dispensing-drugs select{width: 100%;height:1.9em;border-radius:0px;}
    .dispensing-field table{margin:0px;}
    .dispensing-field label{margin:0px;line-height: 18px;font-size:12px;}
    .dispensing-field .control-group{margin-bottom:5px;}
    #tbl-dispensing-drugs{margin-top:3px;}
    #tbl-dispensing-drugs,#last_visit_data,#last_visit_data th,#last_visit_data td, #tbl-dispensing-drugs tr, #tbl-dispensing-drugs td, #tbl-dispensing-drugs th{
        border-radius: 0px;
    }
    #tbl-dispensing-drugs tr, #tbl-dispensing-drugs td, #tbl-dispensing-drugs th{padding:2px;} 
    #submit_section{text-align:right;}
    #prescription_div {overflow-y: scroll;width: 100%;max-height: 300px;margin-top: 77px;}
</style>
<?php $session = session(); ?>
<div class="container-fluid content">
    <div class="row-fluid">
        <a href="<?php echo base_url() . '/public/patients ' ?>">Patient Listing </a> <i class=" icon-chevron-right"></i><a id="patient_names" href="<?php echo base_url() . '/public/patient/load_view/details/' . @$patient_id ?>"><?php echo strtoupper(@$result['name']); ?></a> <i class=" icon-chevron-right"></i><strong>Dispensing details</strong>
        <hr size="1">
    </div>
    <form id="dispense_form"  name="dispense_form" class="dispense_form" method="post"  action="<?php echo base_url() . '/public/dispensement_management/save'; ?>" >
        <textarea name="sql" id="sql" style="display:none;"></textarea>
        <input type="hidden" id="hidden_stock" name="hidden_stock"/>
        <input type="hidden" id="days_count" name="days_count"/>
        <input type="hidden" id="age" name="age" value="<?php echo @$results[0]->age; ?>"/>
        <input type="hidden" id="stock_type_text" name="stock_type_text" value="main pharmacy" />
        <input type="hidden" id="purpose_refill_text" name="purpose_refill_text" value="" />
        <input type="hidden" id="patient_source" name="patient_source" value="<?php echo @$result['patient_source']; ?>" />
        <input type="hidden" id="prescription" name="prescription" value="<?php echo @$_GET['pid']; ?>" />
        <div class="row-fluid">
            <div class="span6">
                <legend>
                    Dispensing Information 
                </legend>
                <div class="row-fluid ">
                    <div class="span6 dispensing-field">
                        <div class="control-group">
                            <?php
                            $ccc_stores = $session->get('ccc_store');
                            $count_ccc = count($ccc_stores);
                            $selected = '';
                            if ($count_ccc > 0) {//In case on has more than one dispensing point
                                echo "<label><span class='astericks'>*</span>Select dispensing point</label>
                                <select name='ccc_store_id' id='ccc_store_id' class='validate[required]'>";
                                //Check if facility has more than one dispensing point
                                foreach ($ccc_stores as $value) {
                                    if ($session->get('ccc_store_id')) {
                                        $ccc_storeid = $session->get('ccc_store_id');
                                        if ($value->id === $ccc_storeid) {
                                            $selected = "selected";
                                        }
                                        echo "<option value='" . $value->id . "' " . $selected . ">" . $value->name . "</option>";
                                    }
                                }
                                echo "</select>";
                            }
                            ?>
                        </div>
                    </div>
                    <div class="span6 dispensing-field" style="padding-top: 17px;">
                        <input type="text" readonly="" id="age" name="age" class="" value="<?= $age; ?> Years" />
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6 dispensing-field">
                        <div class="control-group">
                            <label>Patient Number CCC</label>
                            <input type="text" readonly="" id="patient" name="patient" class="validate[required] "/>
                        </div>
                    </div>
                    <div class="span6 dispensing-field">
                        <div class="control-group">
                            <label>Patient Name</label>
                            <input type="text" readonly="" id="patient_details" name="patient_details" class=""  />
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6 dispensing-field">
                        <div class="control-group">
                            <label><span class='astericks'>*</span>Dispensing Date</label>
                            <input type="text"name="dispensing_date" id="dispensing_date" class="validate[required] ">
                        </div>
                    </div>
                    <div class="span6 dispensing-field">
                        <div class="control-group">
                            <label><span class='astericks'>*</span>Purpose of Visit</label>
                            <select  type="text"name="purpose" id="purpose" class="validate[required] " >
                                <option value="">--Select One--</option>
                                <?php
                                foreach ($purposes as $purpose) {
                                    echo "<option value='" . $purpose['id'] . "'>" . $purpose['name'] . "</option>";
                                }
                                ?>
                            </select>   
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6 dispensing-field">
                        <div class="control-group">
                            <label>Current Height(cm)</label>
                            <input  type="text"name="height" id="height" class="validate[required]">
                        </div>
                    </div>
                    <div class="span6 dispensing-field">
                        <div class="control-group">
                            <label><span class='astericks'>*</span>Current Weight(kg)</label>
                            <input  type="text"name="weight" id="weight" class="validate[required]" >
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6 dispensing-field">
                        <div class="control-group">
                            <label><span class='astericks'>*</span>Days to Next Appointment</label>
                            <input  type="text" name="days_to_next" id="days_to_next" class="validate[required]">
                        </div>
                    </div>
                    <div class="span6 dispensing-field">
                        <div class="control-group">
                            <label><span class='astericks'>*</span>Date of Next Appointment</label>
                            <input  type="text" name="next_appointment_date" id="next_appointment_date" class="validate[required]" >
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span2 dispensing-field">
                        <div class="control-group">
                            <label>Differentiated care?</label>
                            <input  type="checkbox" name="differentiated_care" id="differentiated_care"  class="">
                        </div>
                    </div>
                    <div class="span6 offset4 dispensing-field">
                        <div class="control-group" style="display:none;" id="dcm_exit_reason_container">
                            <label><span class='astericks'>*</span>Differentiated care Exit Reason</label>
                            <select type="text"name="dcm_exit_reason" id="dcm_exit_reason" >
                                <option value="">--Select One--</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="row-fluid clinical_appointment_input" style="display:none;">
                    <div class="span6 dispensing-field">
                        <div class="control-group">
                            <label><span class='astericks'>*</span>Days to Next Clinical Appointment</label>
                            <input  type="text" name="days_to_next_clinical" id="days_to_next_clinical" class="validate[required]">
                        </div>
                    </div>
                    <div class="span6 dispensing-field">
                        <div class="control-group">
                            <label><span class='astericks'>*</span>Date of Next Clinical Appointment</label>
                            <input  type="text" name="next_clinical_appointment_date" id="next_clinical_appointment_date" class="validate[required]" >
                            <input  type="hidden" name="next_clinical_appointment" id="next_clinical_appointment" class="validate[required]" >

                        </div>
                    </div>
                </div>


                <div class="row-fluid">
                    <div class="span10 dispensing-field">
                        <span id="scheduled_patients" style="display:none;background:#9CF;"></span>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6 dispensing-field">
                        <div class="control-group">
                            <label id="scheduled_patients" class="message information " style="display:none; background-color: black;"></label><label>Last Regimen Dispensed</label>
                            <input type="text"name="last_regimen_disp" value="<?php
                            $last_regimen_disp = 'N/A';
                            $last_regimen = 0;
                            foreach ($patient_appointment as $appointment):
                                $last_regimen_disp = $appointment->regimen_code . ' | ' . $appointment->regimen_desc;
                                $last_regimen = $appointment->regimen_id;
                            endforeach;
                            echo $last_regimen_disp;
                            ?>" id="last_regimen_disp" readonly="">
                            <input type="hidden" name="last_regimen" value="<?php echo $last_regimen; ?>" id="last_regimen" value="0">
                        </div>
                    </div>
                    <div class="span6 dispensing-field">
                        <div class="control-group">
                            <label><span class='astericks'>*</span>Current Regimen</label>
                            <select type="text"name="current_regimen" id="current_regimen"  class="validate[required]" style='width:100%;' >
                                <option value="">-Select One--</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6 dispensing-field">
                        <div class="control-group" style="display:none" id="regimen_change_reason_container">
                            <label><span class='astericks'>*</span>Regimen Change Reason</label>
                            <select type="text"name="regimen_change_reason" id="regimen_change_reason" >
                                <option value="">--Select One--</option>
                            </select>
                        </div>
                    </div>

                    <div class="span6 dispensing-field">
                        <div class="control-group" id="adr_popupcheckbox_container">
                            <div class="control-group">
                                <label>Fill ADR form</label>
                                <input  type="checkbox" name="adr_popupcheckbox" id="adr_popupcheckbox"  class="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6 dispensing-field">
                        <div class="control-group">
                            <label>Appointment Adherence (%)</label>
                            <input type="text" name="adherence" id="adherence"/>
                        </div>
                    </div>
                    <div class="span6 dispensing-field">
                        <div class="control-group">
                            <label> Poor/Fair Adherence Reasons </label>
                            <select type="text"name="non_adherence_reasons" id="non_adherence_reasons"  style='width:100%;'>
                                <option value="">-Select One--</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>


            <div class="span5">
                <legend>
                    Previous Patient Information
                </legend>
                <div class="row-fluid">
                    <div class="span6 dispensing-field">
                        <div class="control-group">
                            <label> Appointment Date</label>
                            <input type="text" readonly="" id="last_appointment_date" name="last_appointment_date"/>
                        </div>
                    </div>
                    <div class="span6 dispensing-field">
                        <div class="control-group">
                            <label>Previous Visit Date</label>
                            <input type="text" value="<?php echo $dated; ?>"readonly="" id="last_visit_date" name="last_visit_date"/>
                            <input type="hidden" id="last_visit"/>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12 dispensing-field">
                        <div class="control-group">
                            <label>Previously Dispensed Drugs</label>
                            <table class="table table-bordered prev_dispense" id="last_visit_data" style="float:left;width:100%;">
                                <thead><th style="width: 70%">Drug Dispensed</th><th>Qty Dispensed</th></thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <?php if (count($prescription) > 0 && $api) { ?>

                            <div id="prescription_div">
                                <div style="background: #f7e9e9;">
                                    Ordering Physician: <?= $prescription[0]->order_physician; ?>
                                    <span class="pull-right"> <?= $prescription[0]->timecreated; ?></span>

                                </div>

                                <table class="table" style="background: #fff;padding: 2px;">
                                    <thead>
                                    <th>Regimen</th>
                                    <th>Strength</th>
                                    <th>Dosage</th>
                                    <th>Frequency</th>
                                    <th>Duration</th>
                                    <th>qty</th>
                                    <th>notes</th>
                                    <th>dispense status</th>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($prescription as $ps) { ?>
                                            <tr>
                                                <td> <?= $ps->drug_name . ' '; ?></td>
                                                <td> <?= $ps->strength . ' '; ?></td>
                                                <td> <?= $ps->dosage . ' '; ?></td>
                                                <td> <?= $ps->frequency . ' '; ?></td>
                                                <td> <?= $ps->duration . ' '; ?></td>
                                                <td> <?= $ps->quantity_prescribed . ' '; ?></td>
                                                <td> <?= $ps->prescription_notes . ' '; ?></td>
                                                <td> <?= $ps->dispense_status . ' '; ?></td>
                                            </tr>    
                                        <?php } ?>
                                    </tbody>
                                </table>

                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid">
            <legend>
                Drug Details 
            </legend>
            <table class="table table-bordered" id="tbl-dispensing-drugs">
                <thead>
                    <tr>
                        <th style="width:18%">Drug</th>
                        <th style="width:10%">Unit</th>
                        <th style="width:10%">Batch No.&nbsp;</th>
                        <th style="width:9%">Expiry&nbsp;Date</th>
                        <th style="width:9%">Dose</th>
                        <th><b>Expected</b><br/>Pill Count</th>
                        <th><b>Actual</b><br/> Pill Count</th>
                        <th>Duration</th>
                        <th style="width:5%">Qty. disp</th>
                        <th style="width:8%">Stock on Hand</th>
                        <th>Indication</th>
                        <th>Comment</th>
                        <th>Missed Pills</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr drug_row="0">
                        <td><select name="drug[]" class="drug input-large span3"></select></td>
                        <td>
                            <input type="text" name="unit[]" class="unit input-small" readonly="" />
                            <input type="hidden" name="comment[]" class="comment input-small" readonly="" />
                        </td>
                        <td><select name="batch[]" class="batch input-small next_pill span2"></select></td>
                        <td>
                            <input type="text" name="expiry[]" name="expiry" class="expiry input-small expiry_date" readonly="" size="15"/>
                        </td>
                        <td class="dose_col">
                            <input  name="dose[]" list="dose" class="input-small next_pill dose icondose doselist"> 
                            <datalist id="dose" class="dose"><select name="dose1[]" class="dose"></select></datalist> 
                        </td>
                        <td>
                            <input type="text" name="pill_count[]" class="pill_count input-small" readonly="readonly" />
                        </td>
                        <td>
                            <input type="number" name="next_pill_count[]" class="next_pill_count input-small" />
                        </td>
                        <td>
                            <input type="number" name="duration[]" class="duration input-small" />
                        </td>
                        <td>
                            <input type="number" name="qty_disp[]" id="qty_disp" class="qty_disp input-small next_pill validate[requireds]"  />
                        <td>
                            <input type="text" name="soh[]" id="soh" class="soh input-small"  readonly="readonly"/>
                        </td>
                        </td>
                        <!--                        <td><select name="brand[]" class="brand input-small"></select></td>-->

                        <td>
                            <select name="indication[]" class="indication input-small">
                                <option value="0">None</option>
                            </select></td>
                        <td>
                            <input type="text" name="comment[]" class="comment input-small" />
                        </td>
                        <td>
                            <input type="number" name="missed_pills[]" class="missed_pills input-small" />
                        </td>
                        <td>
                            <a class="add btn-small">Add</a>|<a class="remove btn-small">Remove</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row-fluid" id="submit_section">
            <div class="span12">
                <input type="reset" class="btn btn-danger button_size" id="reset" value="Reset Fields" />
                <input type="button" class="btn button_size" id="print_btn" value="Print Labels" />
                <input type="submit" form="dispense_form" id="btn_submit " class="btn actual button_size" id="submit"  value="Dispense Drugs"/>
            </div>
        </div>
    </form>
    <!-- Modal -->
    <div id="open_print_label" name="open_print_label" title="Label Printer" class="container-fluid">
        <!--select all row-->
        <div class="drugrow"> 
            <form id="print_frm" method="post" action="<?php echo base_url(); ?>/public/dispensement_management/print_test">
                <div class="row label_selectall">
                    <div class="span1" style="padding: 4px 5px;">
                        <label class="checkbox inline">
                            <input type="checkbox" id="selectall" class="label_checker" value="0">All
                        </label>
                    </div>

            </form>
        </div>
    </div>
    <!--end modal-->
</div>
<script src="<?php echo base_url() . '/public/assets/scripts/bootbox/v4/bootbox.min.js'; ?>"></script>

<script type="text/javascript">
    $(document).ready(function () {
        val = '';
        thedays = 0;
        amountispensed = 0;
        $.getJSON("<?php echo base_url() . '/public/patient/requiredFields/' . $patient_id; ?>", function (resp) {
            if (resp.status === 1) {
                bootbox.confirm({
                    title: "Missing Patient Data!",
                    message: "Please fill in the missing required information for this patient:" + resp.fields,
                    buttons: {
                        cancel: {
                            label: '<i class="icon icon-times"></i> Cancel'
                        },
                        confirm: {
                            label: '<i class="icon icon-check"></i> Edit'
                        }
                    },
                    callback: function (result) {
                        if (result === true) {
                            window.location.href = "<?php echo base_url() . '/public/patient/edit/' . $patient_id ?>";
                        } else {
                            window.location.href = "<?php echo base_url() . '/public/patients' ?>";
                        }
                    }
                });
            } else {
            }
        });
<?php if (count($prescription) > 0 && $api) { ?>
            $('#current_regimen').val($("#current_regimen option:contains(<?= $prescription[0]->drug_name; ?>)").val());
<?php } ?>
        $('#ccc_store_id').val(<?= $session->get('ccc_store_id'); ?>);
        $('#ccc_store_id').attr('readonly', 'true');
        var loopcounter = 0;
        var patient_id = "<?php echo $patient_id; ?>";
        var service = "<?php echo $service_name; ?>"
        var base_url = "<?php echo base_url(); ?>";
        var link = base_url + "/public/patient/get_viral_load_info/" + patient_id;
        var request_viral_load = $.ajax({
            url: link,
            type: 'POST',
            dataType: "json",
            success: function (data) {
                if (data != 0) {

                    $.getJSON("<?= base_url(); ?>/public/inventory_management/serverStatus", function (resp) {
                        if (resp.status === 404) {

                        } else if (resp.status === 200) {
                            bootbox.alert("<h4>ViralLoad Alert!</h4>" + data);
                        }
                    });
                }
            }
        });
        //Check if 'PREP' patient has been tested
        checkIfTested(service, patient_id)

        /* -------------------------- Dispensing date, date picker settings and checks -------------------------*/
        //Attach date picker for date of dispensing
        $("#dispensing_date").datepicker({
            yearRange: "-120:+0",
            maxDate: "0D",
            dateFormat: $.datepicker.ATOM,
            changeMonth: true,
            changeYear: true
        });
        $("#dispensing_date").datepicker();
        $("#dispensing_date").datepicker("setDate", new Date());
        //function for changing dispensing date that checks if it matches last visit date
        $(document).on("change", "#dispensing_date", function () {
            var dispensing_date = $(this).val();
            var last_visit_date = $("#last_visit").val();
            checkIfDispensed(last_visit_date, dispensing_date); //Check if already dispensed

            //calculate adherence
            getAdherenceRate();
            if (typeof appointment_date !== "undefined") {
                var diffDays = checkDaysLate(appointment_date);
                $("#days_count").attr("value", diffDays);
            }
        });
        //Add datepicker for the next appointment date
        $("#next_appointment_date").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: $.datepicker.ATOM,
            onSelect: function (dateText, inst) {
                var base_date = new Date();
                var today = new Date(base_date.getFullYear(), base_date.getMonth(), base_date.getDate());
                var today_timestamp = today.getTime();
                var dispensing_date_timestamp = Date.parse($("#dispensing_date").val());
                var one_day = 1000 * 60 * 60 * 24;
                var appointment_timestamp = $("#next_appointment_date").datepicker("getDate").getTime();
                var difference = appointment_timestamp - dispensing_date_timestamp;
                var days_difference = difference / one_day;
                thedays = Math.round(days_difference);
                $("#days_to_next").attr("value", Math.round(days_difference));
                $('.duration').val(thedays);
                retrieveAppointedPatients();
                validateAppointments();
            }
        });
        // add datepicker for next clinical appointment date
        $("#next_clinical_appointment_date").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: $.datepicker.ATOM,
            onSelect: function (dateText, inst) {
                validateAppointments();
                var base_date = new Date();
                var today = new Date(base_date.getFullYear(), base_date.getMonth(), base_date.getDate());
                var today_timestamp = today.getTime();
                var dispensing_date_timestamp = Date.parse($("#dispensing_date").val());
                var one_day = 1000 * 60 * 60 * 24;
                var appointment_timestamp = $("#next_clinical_appointment_date").datepicker("getDate").getTime();
                var difference = appointment_timestamp - dispensing_date_timestamp;
                var days_difference = difference / one_day;
                $("#days_to_next_clinical").attr("value", Math.round(days_difference));
                retrieveAppointedPatients();
            }
        });
        //Add listener to the 'days_to_next' field so that the date picker can reflect the correct number of days!
        $("#days_to_next, #dispensing_date").change(function () {

            var days = $("#days_to_next").attr("value");
            if (days > 0) {
                validateAppointments();
                var base_date = new Date();
                var appointment_date = $("#next_appointment_date");
                var today = new Date(base_date.getFullYear(), base_date.getMonth(), base_date.getDate());
                var today_timestamp = today.getTime();
                var dispensing_date_timestamp = Date.parse($("#dispensing_date").val());
                var appointment_timestamp = (1000 * 60 * 60 * 24 * days) + dispensing_date_timestamp;
                appointment_date.datepicker("setDate", new Date(appointment_timestamp));
                retrieveAppointedPatients();
            } else {
                bootbox.alert("<h4>Notice!</h4>\n\<center>Days cannot be empty or negative</center>");
            }
            //Loop through Table to calculate pill counts for all rows
            $.each($(".drug"), function (i, v) {
                var row = $(this);
                var qty_disp = row.closest("tr").find(".qty_disp").val();
                var dose_val = row.closest("tr").find(".dose option:selected").attr("dose_val");
                var dose_freq = row.closest("tr").find(".dose option:selected").attr("dose_freq");
            });
        });
        //Add listener to the 'days_to_next_clinical' field so that the date picker can reflect the correct number of days!
        $("#days_to_next_clinical").change(function () {
            validateAppointments();
            var days_to_next_clinical = $("#days_to_next_clinical").attr("value");
            if (days_to_next_clinical > 0) {
                var base_date = new Date();
                var clinical_appointment_date = $("#next_clinical_appointment_date");
                var today = new Date(base_date.getFullYear(), base_date.getMonth(), base_date.getDate());
                var today_timestamp = today.getTime();
                var dispensing_date_timestamp = Date.parse($("#dispensing_date").val());
                var appointment_timestamp = (1000 * 60 * 60 * 24 * days_to_next_clinical) + dispensing_date_timestamp;
                clinical_appointment_date.datepicker("setDate", new Date(appointment_timestamp));
                // retrieveAppointedPatients();
            } else {
                bootbox.alert("<h4>Notice!</h4>\n\<center>Days cannot be empty or negative</center>");
            }
        });
        // -------------------------- Dispensing date, date picker settings and checks end--------------------------


        is_pregnant = ''; //Check if patient is pregnant
        has_tb = ''; //Check if patient has tb
        alert_qty_check = true; //Variable for qty error check

        //When pressing return/enter, tabulate
        $('form input,select,readonly').keydown(function (e) {
            if (e.keyCode == 13) {
                var inputs = $(this).parents("form").eq(0).find(":input");
                if (inputs[inputs.index(this) + 1] != null) {
                    inputs[inputs.index(this) + 1].focus();
                }
                e.preventDefault();
                return false;
            }
        });
        $('#days_to_next').change(function () {
            $('.duration').val($(this).val())
            $('.duration').trigger('change');
        });
        //If facility has more than one dispensing point, use the selected dispensing point.
        if ($("#ccc_store_id ").is(":visible")) {
            stock_type = $("#ccc_store_id ").val();
            stock_type_text = $("#ccc_store_id option:selected").text();
        } else {//If only dispensing point, use main pharmacy as the dispensing point
            stock_type = "2";
            stock_type_text = 'main pharmacy';
        }

        // ----------------------------- Printing labels ----------------------------------------

        $("#selectall").attr("checked", false);
        //function for select all
        $("#selectall").on('click', function () {
            var status = $(this).is(":checked");
            if (status == true) {
                //check all
                $(".label_checker").attr("checked", true);
                $(".label_checker").val(1);
            } else {
                //uncheck all
                $(".label_checker").attr("checked", false);
                $(".label_checker").val(0);
            }
        });
        //drug label modal settings
        $("#open_print_label").dialog({
            width: '1000',
            modal: true,
            height: '600',
            autoOpen: false,
            show: 'fold',
            buttons: {
                "Print": function () {
                    $("#print_frm").submit(); //Submit  the FORM
                },
                Cancel: function () {
                    $(this).dialog("close");
                }
            }
        });
        //print frm submit event
        $("#print_frm").on('submit', function (e) {
            var postData = $('form#print_frm').serialize();
            var formURL = $(this).attr("action");
            $.ajax({
                url: formURL,
                type: "POST",
                data: postData,
                success: function (data, textStatus, jqXHR)
                {
                    //data: return data from server
                    if (data == 0) {
                        bootbox.alert("<h4>Notice!</h4>\n\<center>You have not selected any drug label to print!</center>");
                    } else {
                        window.open(data);
                        $("#open_print_label").dialog("close");
                    }
                }
            });
            //STOP default action
            e.preventDefault();
        });
        //print drug labels functions
        $('#print_btn').on('click', function () {
            $("#open_print_label").dialog("open");
            $("#selectall").attr("checked", false);
            var label_str = '<table id="tbl_printer" class="table table-condensed table-hover"><tbody>';
            var _class = '';
            $("#tbl-dispensing-drugs > tbody > tr").each(function (i, v) {
                if ((i + 1) % 2 == 0) {
                    _class = 'even';
                } else {
                    _class = 'odd';
                }
                var row = $(v);
                var drug_id = row.closest("tr").find(".drug").val();
                if (drug_id != null) {
                    var drug_name = row.closest("tr").find(".drug option[value='" + drug_id + "']").text();
                    var drug_unit = row.closest("tr").find(".unit").val();
                    if (drug_unit.toLowerCase() == 'bottle') {
                        drug_unit = 'ml';
                    }
                    var expiry_date = row.closest("tr").find(".expiry").val();
                    var val = row.closest("tr").find('.doselist').val();
                    var dose_value = row.closest("tr").find('.dose option').filter(function () {
                        return this.value == val;
                    }).data('dose_val');
                    var dose_frequency = row.closest("tr").find('.dose option').filter(function () {
                        return this.value == val;
                    }).data('dose_freq');
                    var duration = row.find(".duration").val();
                    var qty = row.find(".qty_disp ").val();
                    var dose_hours = (24 / (dose_value * dose_frequency));
                    console.log(dose_frequency)
                    switch (dose_frequency) {
                        case 1:
                            dose_frequency = "once";
                            break;
                        case 2:
                            dose_frequency = "twice";
                            break;
                        case 3:
                            dose_frequency = "Thrice";
                            break;
                        case 4:
                            dose_frequency = "4 times";
                            break;
                        case 5:
                            day = "";
                    }
                    var patient_name = $('#patient_details').val();
                    //get instructions
                    var base_url = "<?php echo base_url(); ?>";
                    var link = base_url + "/public/dispensement_management/getInstructions/" + drug_id;
                    $.ajax({
                        url: link,
                        async: false,
                        type: 'POST',
                        success: function (data) {
                            var s = data;
                            s = s.replace(/(^\s*)|(\s*$)/gi, "");
                            s = s.replace(/[ ]{2,}/gi, " ");
                            drug_instructions = s.replace(/\n /, "\n");
                            //append data
                            label_str += '<tr class="' + _class + '">\
                        <td class="span1 ">\
                        <label class="checkbox inline">\
                        <input type="checkbox" name="print_check[' + i + ']" class="label_checker" value="0"/>\
                        </label>\
                        </td>\
                        <td class="span10">\
                        <div class="row-fluid">\
                        <div class="span9">\
                        <label class="inline">\
                        Drugname:\
                        <input type="text" name="print_drug_name[]" class="span9 label_drug" value="' + drug_name + '" required readonly/>\
                        </label>\
                        </div>\
                        <div class="span3">\
                        <label class="inline">\
                        Qty:\
                        <input type="number" name="print_qty[]" class="span3 label_qty" value="' + qty + '" required readonly/>\
                        </label>\
                        </div>\
                        </div>\
                        <div class="row-fluid">\
                        <div class="span12">\
                        <label class="inline">\
                        <input type="number" name="print_dose_value[]" class="span1 label_dose_value" value="' + dose_value + '" required/>\
                        <input type="hidden" name="print_drug_unit[]" class="span1 label_drug_unit" value="' + drug_unit + '"/>\
                        ' + drug_unit + ' to be taken\
                        ' + dose_frequency + ' a day after every\
                        <input type="hidden" name="print_dose_frequency[]" class="span1 label_dose_frequency" value="' + dose_frequency + '"/>\
                        <input type="number" name="print_dose_hours[]" class="span1 label_hours" value="' + dose_hours + '" required/> hours\
                        </label>\
                        </div>\
                        </div>\
                        <div class="row-fluid">\
                        <div class="span12">\
                        <label class="inline">\
                        Before/After Meals:\
                        <textarea name="print_drug_info[]"  row="5" class="span8 label_info">' + drug_instructions + '</textarea>\
                        </label>\
                        </div>\
                        </div>\
                        <div class="row-fluid">\
                        <div class="span4">\
                        <label class="inline">\
                        Name: <input type="text" name="print_patient_name" class="span9 label_patient" value="' + patient_name + '" required readonly/>\
                        </label>\
                        </div>\
                        <div class="span4">\
                        <label class="inline">\
                        Pharmacy: <input type="text" name="print_pharmacy[]" class="span8 label_patient" value="Pharmacy"/>\
                        </label>\
                        </div>\
                        <div class="span4">\
                        <label class="inline">\
                        Date: <input type="text" name="print_date" class="span6 label_date" value="<?php echo date('d/m/Y'); ?>" readonly/>\
                        </label>\
                        </div>\
                        </div>\
                        <div class="row-fluid">\
                        <div class="span12">\
                        <label style="text-align:center;">Keep all medicines in a cold dry place out of reach of children.</label>\
                        </div>\
                        </div>\
                        <div class="row-fluid">\
                        <div class="span6">\
                        <label class="inline">\
                        Facility Name:<input type="text" name="print_facility_name" class="span8 label_facility" value="<?php echo $session->get("facility_name"); ?>" readonly/>\
                        </label>\
                        </div>\
                        <div class="span6">\
                        <label class="inline">\
                        Facility Phone:<input type="text" name="print_facility_phone" class="span4 label_contact" value="<?php echo $session->get("facility_phone"); ?>" readonly/>\
                        </label>\
                        </div>\
                        </div>\
                        </td>\
                        <td class="span1">\
                        <label class="inline">\
                        No. of Labels\
                        <input name="print_count[]" class="span1" style="height: 2em;" type="number" value="1">\
                        </label>\
                        </td>\
                        </tr>';
                        }
                    });
                } else {
                    $("#open_print_label").dialog("close");
                }
            });
            //remove all
            $('.label_selectall').nextAll().remove();
            //insertafter
            label_str += '</tbody></table></div>';
            $(label_str).insertAfter(".label_selectall");
            //function to select individual
            $(".label_checker").on('click', function () {
                cb = $(this);
                cb.val(cb.prop('checked'));
            });
        });
        // ----------------------------- Printing labels end----------------------------------------




        //------------------------------ Validation ------------------------------------------------
        $("input,select").on('change', function (i, v) {
            var value = $(this).val();
            var id = this.id;
            if (value != '') {
                if (id == "days_to_next") {
                    $('#next_appointment_date').validationEngine('hide');
                } else if (id == "next_appointment_date") {
                    $('#days_to_next').validationEngine('hide');
                }
                $('#' + id).validationEngine('hide');
            }
        });
        //------------------------------ Validation end---------------------------------------------


        var link = "<?php echo base_url(); ?>/public/patient/get_patient_details";
        var patient_id = "<?php echo $patient_id; ?>";
        var request = $.ajax({
            url: link,
            type: 'post',
            data: {"patient_id": patient_id},
            dataType: "json"
        });
        request.done(function (data) {
            $("#patient").val(data.patient_number_ccc);
            $("#patient_details").val(data.names);
            $("#height").val(data.height);
            // $("#weight").val(data.Weight);
            $("#patient_names").text(data.names);
            $("#next_clinical_appointment_date").val(data.clinicalappointment);
            $("#next_clinical_appointment").val(data.clinicalappointment);

            $.getJSON("<?= base_url() . '/public/inventory_management/getIsoniazid/'; ?>" + data.patient_number_ccc, function (resp) {
                amountispensed = parseInt(resp.iso_count);
                // alert(amountispensed);
                if (amountispensed >= 180) {
                    bootbox.alert("<h4>ISONIAZID MAX DISPENSE ALERT!</h4>\n\<hr/><center>This patient has already been dispensed the maximum quantity(180) ISONIAZIDS</center>");
                }

            }, 'json');

            if (data.clinicalappointment !== null && data.clinicalappointment!="") {

                var base_date = new Date();
                var today = new Date(base_date.getFullYear(), base_date.getMonth(), base_date.getDate());
                var today_timestamp = today.getTime();
                var dispensing_date_timestamp = Date.parse($("#dispensing_date").val());
                var one_day = 1000 * 60 * 60 * 24;
                if ($("#next_clinical_appointment_date").val() == "") {
                    $("#next_clinical_appointment_date").val(Date.now);
                }
                var clinical_appointment_timestamp = $("#next_clinical_appointment_date").datepicker("getDate").getTime();
                var difference = clinical_appointment_timestamp - dispensing_date_timestamp;
                var days_difference = difference / one_day;
                $("#days_to_next_clinical").attr("value", days_difference);
            }


            is_pregnant = data.pregnant;
            has_tb = data.Tb;
            var age = data.age;
            patient_ccc = data.patient_number_ccc;
            //CHeck if patient is pregnant
            checkIfPregnant(is_pregnant, patient_ccc);
            //Check if still has tb
            checkIfHasTb(has_tb, patient_ccc);
            //Load regimens
            loadRegimens(age);
            loadOtherDetails(patient_ccc);
        })
        request.fail(function (jqXHR, textStatus) {
            bootbox.alert("<h4>Patient Details Alert</h4>\n\<hr/>\n\<center>Could not retrieve patient details : </center>" + textStatus);
        });
    });
    //-------------------------------- CHANGE EVENT --------------------------------------
    //store type change event
    /*$("#ccc_store_id").change(function() {
     if($(this).val()!=''){
     $("#ccc_store_id").css('border','none');
     }else{
     $("#ccc_store_id").css('border','solid 3px red');
     }
     stock_type = $("#ccc_store_id ").val();
     stock_type_text = $("#ccc_store_id option:selected").text();
     $("#stock_type_text").val(stock_type_text);
     $("#current_regimen").trigger("change");
     reinitialize();
     storeSession($(this).val());
     });*/

    //Add listener to check purpose
    $("#purpose").change(function () {
        //Ensure dispensing point is selected
        val = $('#purpose option:selected').text();
        if ($("#ccc_store_id").val() == "") {
            bootbox.alert("<h4>Dispensing point</h4>\n\<hr/>\n\<center>Please select a dispensing point first! </center>");
            $("#ccc_store_id").css('border', 'solid 3px red');
            return;
        }

        resetRoutineDrugs();
        var regimen = $("#current_regimen option:selected").attr("value");
        var last_regimen = $("#last_regimen").attr("value");
        purpose_visit = $("#purpose :selected").text().toLowerCase();
        //Check if visit != switch_regimen then current_regimen = last_regimen
        if (purpose_visit === 'switch regimen' || purpose_visit === '--select one--') {
            $("#current_regimen").val("0");
        } else {
            //Assign current_regimen = last_regimen
            $("#current_regimen").val(last_regimen);
            //Populate drugs by triggering change event
            $("#current_regimen").trigger("change");
            $("#purpose_refill_text").val('');
            //If purpose == Start ART, check if patient has WHO stage
            if (purpose_visit === 'start art') {
                $("#current_regimen").val("0");
                $("#purpose_refill_text").val(purpose_visit);
                var _url = "<?php echo base_url() . '/public/patient/getWhoStage'; ?>";
                //Get drugs
                var request = $.ajax({
                    url: _url,
                    type: 'post',
                    data: {"patient_ccc": patient_ccc},
                    dataType: "json"
                });
                request.done(function (data) {
                    if (data.patient_who == 0) {//If no WHO Stage, prompt to enter it
                        var length_who = data.who_stage.length;
                        length_who = length_who - 1;
                        var select_who = "<select id='who_stage' name='who_stage'>";
                        $.each(data.who_stage, function (i, v) {
                            select_who += "<option value='" + data.who_stage[i]['id'] + "'>" + data.who_stage[i]['name'] + "</option>";
                            if (length_who == i) {
                                select_who += '</select>';
                                bootbox.confirm({
                                    title: "WHO Stage",
                                    message: "Patient does not have a WHO Stage, Please select one " + select_who,
                                    buttons: {
                                        cancel: {
                                            label: '<i class="fa fa-times"></i> Cancel'
                                        },
                                        confirm: {
                                            label: '<i class="fa fa-check"></i> Save'
                                        }
                                    },
                                    callback: function (res) {
                                        if (res === true) {//If answer is no, update pregnancy status
                                            var who_selected = $('#who_stage').val();
                                            //Check if the current regimen is OI Medicine and if not, hide the indication field
                                            var _url = "<?php echo base_url() . '/public/patient/updateWhoStage'; ?>";
                                            //Get drugs
                                            var request = $.ajax({
                                                url: _url,
                                                type: 'post',
                                                data: {"patient_ccc": patient_ccc, "who_stage": who_selected},
                                                dataType: "json"
                                            });
                                        }
                                    }
                                });
                            }
                        });
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    bootbox.alert("<h4>Who Error </h4>\n\<hr/>\n\<center>Could not retrieve Who information : </center>" + textStatus);
                });
            }
        }
        //adherence rate
        getAdherenceRate();
    });
    //Dynamically change the list of drugs once a current regimen is selected
    $("#current_regimen").change(function () {

        var selected_regimen = $(this).val();
        //Check if the current regimen is OI Medicine and if not, hide the indication field
        var _url = "<?php echo base_url() . '/public/dispensement_management/getDrugsRegimens'; ?>";
        //Get drugs
        var request = $.ajax({
            url: _url,
            type: 'post',
            data: {"selected_regimen": selected_regimen, "stock_type": stock_type},
            dataType: "json",
            async: false
        });
        request.done(function (data) {
            resetRoutineDrugs();
            $(".drug option").remove();
            $(".drug").append($("<option value='0'>-Select Drug-</option>"));
            $.each(data, function (key, value) {
                $(".drug").append($("<option value='" + value.id + "'  arv_status='" + value.is_arv + "' >" + value.drug + "</option>"));
            });
        });
        request.fail(function (jqXHR, textStatus) {
            bootbox.alert("<h4>Drug Details Alert</h4>\n\<hr/>\n\<center>Could not retrieve drug details : </center>" + textStatus);
        });
        var regimen = $("#current_regimen option:selected").attr("value");
        var last_regimen = $("#last_regimen").attr("value");
        if (last_regimen !== "0") {
            if (regimen !== last_regimen) {
                $("#regimen_change_reason_container").show();
                $("#regimen_change_reason").addClass("validate[required]");
            } else {
                $("#regimen_change_reason").removeClass("validate[required]");
                $("#regimen_change_reason_container").hide();
                $("#regimen_change_reason").val("");
            }
            if ($("#last_regimen_disp").val().toLowerCase().indexOf("oi") == -1) {
                //contains oi

                if (purpose_visit == 'routine refill') {
                    routine_check = 1;
                    //append visits
                    if (typeof previous_dispensed_data !== "undefined") {
                        total_visits = (previous_dispensed_data.length) - 1;
                        var count = 0;
                        getRoutineDrugs(previous_dispensed_data, total_visits, count);
                    }

                }
            }
        } else {
            $("#regimen_change_reason_container").hide();
            $("#regimen_change_reason").val("");
        }

    });
    if(<?=$differentiated_care;?> == 1){
    $('#differentiated_care').attr("checked", "checked");
    }

    $('#differentiated_care').click(function (event) {
        if ($(this).is(":checked"))
        {
            $(".clinical_appointment_input").show();
            $("#dcm_exit_reason_container").hide();
            validateAppointments();
        } else
        {
            $(".clinical_appointment_input").hide();
            // show diff care exit reason
            if(<?=$differentiated_care;?> == 1){
                $("#dcm_exit_reason_container").show();
            }

        }
    });
    // on change weight, sort doses.
    $('#weight').change(function () {
        var age = $("#age").val();
        var weight = $("#weight").val();
        $(".drug").each(function (index) {
            $(this).closest('');
            var row = $(this);
            var url_drug_dose = "<?php echo base_url() . '/public/dispensement_management/getDrugDose/'; ?>";
            var new_url_dose = url_drug_dose + $(this).val();
            var request_one_dose = $.ajax({
                url: new_url_dose,
                data: {"weight": weight, "drug_id": $(this).val(), "age": age},
                type: 'post',
                dataType: "json"
            });
            request_one_dose.done(function (data_single_dose) {
                var current_dose = data_single_dose[0].dose;
                row.closest("tr").find(".dose").val(current_dose);
            });
        });
    });
    $('#adr_popupcheckbox').change(function () {
        if (this.checked) {
            window.open("<?= base_url() . "/public/dispensement_management/adr/" . $patient_id ?>");
        }
    });
    //drug change event
    $(".drug").change(function () {
        var row = $(this);
        var drug_name = row.find("option:selected").text();
        resetFields(row);
        row.closest("tr").find(".batch option").remove();
        row.closest("tr").find(".batch").append($("<option value='0'>Loading ...</option>"));
        var row = $(this);
        var selected_drug = $(this).val();
        var patient_no = $("#patient").val();
        //Check if patient allergic to selected drug
        var _url = "<?php echo base_url() . '/public/dispensement_management/drugAllergies'; ?>";
        var request = $.ajax({
            url: _url,
            type: 'post',
            dataType: "json",
            data: {
                "selected_drug": selected_drug,
                "patient_no": patient_no
            }
        });
        request.done(function (data) {
            //If patient is allergic to selected drug,alert user
            if (data == 1) {
                bootbox.alert("<h4>Allergy Alert!</h4>\n\<hr/><center>This patient is allergic to " + drug_name + "</center>");
                //Remove row
                var rows = $("#tbl-dispensing-drugs > tbody").find("tr").length;
                if (rows > 1) {
                    row.closest('tr').remove();
                } else {
                    row.closest('tr').find(".drug").val(0);
                    row.closest('tr').find(".drug").trigger("change");
                }
            } else {
                if (typeof previous_dispensed_data !== "undefined") {
                    for (var i = 0; i < previous_dispensed_data.length; i++) {
                        var prev_drug_id = previous_dispensed_data[i]['drug_id'];
                        var prev_drug_qty = previous_dispensed_data[i]['mos'];
                        var prev_qty = previous_dispensed_data[i]['quantity'];
                        var prev_date = previous_dispensed_data[i]['dispensing_date'];
                        var prev_value = previous_dispensed_data[i]['value'];
                        var prev_frequency = previous_dispensed_data[i]['frequency'];
                        if (previous_dispensed_data[i]['pill_count'] != "") {
                            var prev_pill_count = previous_dispensed_data[i]['mos']; //Previous pill count will be used to calculate expected pill count
                        } else {
                            var prev_pill_count = 0; //Previous pill count will be used to calculate expected pill count 
                        }
                        //If drug was previously dispensed
                        if (selected_drug == prev_drug_id) {
                            var base_date = new Date();
                            var today = new Date(base_date.getFullYear(), base_date.getMonth(), base_date.getDate());
                            var today_timestamp = today.getTime();
                            var dispensing_date_timestamp = Date.parse($("#dispensing_date").val());
                            var one_day = 1000 * 60 * 60 * 24;
                            var appointment_timestamp = Date.parse(prev_date);
                            var difference = dispensing_date_timestamp - appointment_timestamp;
                            var days_difference = Math.round(difference / one_day);
                            if (days_difference > 0) {
                                days_difference = days_difference.toFixed(0);
                            } else {
                                days_difference = 0;
                            }
                            /*
                             * Group A - sum of the number of drugs dispensed to patient previously and drugs brought back by patient
                             * Group B - number of drugs patient is expected to have consumed so far
                             */
                            var group_A = (parseInt(prev_qty) + parseInt(prev_pill_count));
                            var group_B = (days_difference * (prev_value * prev_frequency))
                            var prev_drug_qty = (group_A - group_B);
                            if (prev_drug_qty < 0) {
                                prev_drug_qty = 0;
                            }
                            row.closest("tr").find(".pill_count").val(prev_drug_qty);
                        }
                    }
                    ;
                }
                var dose = "";
                //Get batches that have not yet expired and have stock balance
                var _url = "<?php echo base_url() . '/public/getBacthes'; ?>";
                var request = $.ajax({
                    url: _url,
                    type: 'post',
                    data: {"selected_drug": selected_drug, "stock_type": stock_type},
                    dataType: "json",
                    async: false
                });
                request.done(function (data) {
                    //get and check age
                    var link = "<?php echo base_url(); ?>/public/patient/get_patient_details";
                    var patient_id = "<?php echo $patient_id; ?>";
                    var request = $.ajax({
                        url: link,
                        type: 'post',
                        data: {"patient_id": patient_id},
                        dataType: "json"
                    });
                    request.done(function (datas) {
                        var age = datas.Dob;
                        var weight = $("#weight").val();
                        var drug_id = selected_drug;
                        //Show alert when dispensing date is after ipt_end_date and drug is isoniazid
                        var ipt_end_date = datas.isoniazid_end_date;
                        if ($("#dispensing_date").val() > ipt_end_date && ipt_end_date != '' && drug_name.indexOf('ISONIAZID') > -1) {
                            bootbox.alert("<h4>IPT Alert!</h4> The patient has already been dispensed to the maximum number of IPT pills, treatement should have ended on: " + ipt_end_date);
                        }
                        //get facility adult age
                        var link = "<?php echo base_url(); ?>/public/dispensement_management/getFacililtyAge";
                        var request = $.ajax({
                            url: link,
                            type: 'post',
                            dataType: "json"
                        });
                        request.done(function (datas) {
                            var adult_age = datas.adult_age;
                            var url_dose = "<?php echo base_url() . '/public/dispensement_management/getDoses'; ?>";
                            //Get doses
                            var request_dose = $.ajax({
                                url: url_dose,
                                type: 'post',
                                dataType: "json"
                            });
                            request_dose.done(function (data) {
                                var url_drug_dose = "<?php echo base_url() . '/public/dispensement_management/getDrugDose/'; ?>";
                                var new_url_dose = url_drug_dose + selected_drug;
                                var request_one_dose = $.ajax({
                                    url: new_url_dose,
                                    data: {"weight": weight, "drug_id": drug_id, "age": age},
                                    type: 'post',
                                    dataType: "json"
                                });
                                request_one_dose.done(function (data_single_dose) {
                                    var current_dose = data_single_dose[0].dose;
                                    row.closest("tr").find(".dose option").remove();
                                    $.each(data, function (key, value) {
                                        row.closest("tr").find(".dose").append("<option value='" + value.Name + "'  data-dose_val='" + value.value + "' data-dose_freq='" + value.frequency + "' >" + value.Name + "</option> ");
                                    });
                                    row.closest("tr").find(".dose").val(current_dose)

                                });
                            });
                        });
                    });
                    // end of doses
                    row.closest("tr").find(".batch option").remove();
                    row.closest("tr").find(".batch").append($("<option value='0'>Select</option>"));
                    $.each(data, function (key, value) {
                        var _class = '';
                        if (routine_check == 1) {
                            //used for generating automatic table rows for drugs                                         
                            if (key == 0) {
                                _class = 'selected';
                            }
                        } else {
                            row.closest("tr").find(".dose").val(value.dose);
                            row.closest("tr").find(".duration").val(value.duration);
                            row.closest("tr").find(".qty_disp ").val(value.quantity); //Fix for not showing quantity when not routine refill
                        }

                        row.closest("tr").find(".unit").val(value.Name);
                        row.closest("tr").find(".batch").append("<option " + _class + " value='" + value.batch_number + "'>" + value.batch_number + "</option> ");
                        row.closest("tr").find(".comment").val(value.comment);
                        dose = value.dose;
                    });
                    //Get brands
                    var new_url = "<?php echo base_url() . '/public/dispensement_management/getBrands'; ?>";
                    var request_brand = $.ajax({
                        url: new_url,
                        type: 'post',
                        data: {"selected_drug": selected_drug},
                        dataType: "json"
                    });
                    request_brand.done(function (data) {
                        row.closest("tr").find(".brand option").remove();
                        row.closest("tr").find(".brand").append("<option value='0'>None</option> ");
                        $.each(data, function (key, value) {
                            row.closest("tr").find(".brand").append("<option value='" + value.id + "'>" + value.brand + "</option> ");
                        });
                    });
                    request_brand.fail(function (jqXHR, textStatus) {
                        boottbox.alert("<h4>Brands Notice</h4>\n\<hr/><center>Could not retrieve the list of brands :</center> " + textStatus);
                    });
                    //Get indications(opportunistic infections)
                    var url_indication = "<?php echo base_url() . '/public/dispensement_management/getIndications'; ?>";
                    var request_dose = $.ajax({
                        url: url_indication,
                        type: 'post',
                        dataType: "json",
                        data: {
                            "drug_id": selected_drug
                        }
                    });
                    request_dose.done(function (data) {
                        row.closest("tr").find(".indication option").remove();
                        row.closest("tr").find(".indication").append("<option value='0'>None</option> ");
                        // Check if regimen selected is OI so as to display indication
                        $.each(data, function (key, value) {
                            row.closest("tr").find(".indication").append("<option value='" + value.indication + "'>" + value.indication + " | " + value.name + "</option> ");
                        });
                    });
                });
                request.fail(function (jqXHR, textStatus) {
                    bootbox.alert("<h4>Indication Alert</h4>\n\<hr/><center>Could not retrieve the list of batches : </center>" + textStatus);
                });
                //trigger batch changes
                row.closest("tr").find(".batch").trigger("change");
            }
        });
    });
    //batch change event
    $(".batch").change(function () {
        if ($(this).prop("selectedIndex") > 1) {
            bootbox.alert("<h4>Expired Batch</h4>\
            <hr/>\n\
            <center>This is not the first expiring batch</center>");
        }
        var row = $(this);
        //Get batch details(balance,expiry date)
        if ($(this).val() != 0) {
            var batch_selected = $(this).val();
            var selected_drug = row.closest("tr").find(".drug").val();
            var _url = "<?php echo base_url() . '/public/getBacthDetails'; ?>";
            var request = $.ajax({
                url: _url,
                type: 'post',
                data: {"selected_drug": selected_drug, "stock_type": stock_type, "batch_selected": batch_selected},
                dataType: "json"
            });
            request.done(function (data) {
                row.closest("tr").find(".expiry").val(data[0].expiry_date);
                row.closest("tr").find(".soh ").val(data[0].balance);
                //duration_quantity(row); //Auto-calculate what should be displayed
                $(".qty_disp").trigger('keyup', [row]); //for error dialog
            });
            request.fail(function (jqXHR, textStatus) {
                bootbox.alert("<h4>Batch Details Alert</h4>\n\<hr/><center>Could not retrieve batch details : </center>" + textStatus);
            });
        }
    });
    //check if quantity dispensed is greater than quantity available
    $(".qty_disp").change(function () {
        var row = $(this);
        var qty_disp = parseInt(row.closest("tr").find(".qty_disp").val());
        var soh = parseInt(row.closest("tr").find(".soh").val());
        if (qty_disp > soh) {
            alert('You cannot dispense more than ' + soh);
            row.closest("tr").find(".qty_disp").val(soh);
        }

    });
    //next pill count change event
    $(".next_pill").change(function () {
        var row = $(this);
        var qty_disp = row.closest("tr").find(".qty_disp").val();
        var dose_val = row.closest("tr").find(".dose option:selected").attr("dose_val");
        var dose_freq = row.closest("tr").find(".dose option:selected").attr("dose_freq");
    });
    $(".duration,.dose").on('keyup', function () {
        duration_quantity($(this));
    });
    $(".dose").on('keyup', function () {
        duration_quantity($(this));
    });
    //function to change quantity based on the duration 
    function duration_quantity(row) {
        var duration = row.closest("tr").find(".duration").val();
        if (duration > 0) {
            var val = row.closest("tr").find('.doselist').val();
            var dose_val = row.closest("tr").find('.dose option').filter(function () {
                return this.value == val;
            }).data('dose_val');
            var dose_freq = row.closest("tr").find('.dose option').filter(function () {
                return this.value == val;
            }).data('dose_freq');
            var qty_disp = parseFloat(duration) * parseFloat(dose_val) * parseFloat(dose_freq);
            if (!isNaN(qty_disp)) {
                row.closest("tr").find(".qty_disp").val(qty_disp);
            }

            row.closest("tr").find(".duration").css("background-color", "white");
            row.closest("tr").find(".duration").removeClass("input_error");
        }
    }

    function validateAppointments() {
        var days_to_next_clinical = $("#days_to_next_clinical").attr("value");
        var days = $("#days_to_next").attr("value");
        if (parseInt(days) > parseInt(days_to_next_clinical) && $('#differentiated_care').is(":checked")) {
            setClinicalAppointment(days);
            alert('Pharmacy appointments must be on or before clinical appointment.');
        }
    }

    function setClinicalAppointment(days) {
        $("#days_to_next_clinical").val(days);
        $("#days_to_next_clinical").trigger('change');
        // $("#next_clinical_appointment_date").val($("#next_appointment_date").val());
    }

    //function to add drug row in table 
    $(".add").click(function () {
        routine_check = 0;
        var last_row = $('#tbl-dispensing-drugs tr');
        var drug_selected = last_row.find(".drug").val();
        var quantity_entered = last_row.find(".qty_disp").val();
        if (last_row.find(".qty_disp").hasClass("input_error")) {
            bootbox.alert("<h4>Excess Quantity Alert</h4>\n\<hr/><center>Error !Quantity dispensed is greater than qty available!</center>");
        } else if (drug_selected == 0) {
            bootbox.alert("<h4>Drug Alert</h4>\n\<hr/><center>You have not selected a drug!</center>");
        } else if (quantity_entered == "" || quantity_entered == 0) {
            bootbox.alert("<h4>Quantity Alert</h4>\n\<hr/><center>You have not entered any quantity!</center>");
        } else {
            var cloned_object = $('#tbl-dispensing-drugs tr:last').clone(true);
            var drug_row = cloned_object.attr("drug_row");
            var next_drug_row = parseInt(drug_row) + 1;
            var row_element = cloned_object;
            //Second thing, retrieve the respective containers in the row where the drug is
            row_element.find(".unit").attr("value", "");
            row_element.find(".batch").empty();
            //Fixing the expiry date in dispensing
            var expiry_id = "expiry_date_" + next_drug_row;
            var expiry_date = row_element.find(".expiry").attr("value", "");
            expiry_date.attr("id", expiry_id);
            var expiry_selector = "#" + expiry_id;
            $(expiry_selector).datepicker({
                defaultDate: new Date(),
                changeYear: true,
                changeMonth: true
            });
            row_element.find(".dose").attr("value", "");
            row_element.find(".duration").attr("value", thedays);
            row_element.find(".qty_disp").attr("value", "");
            row_element.find(".brand").attr("value", "");
            row_element.find(".soh").attr("value", "");
            row_element.find(".indication").attr("value", "");
            row_element.find(".pill_count").attr("value", "");
            row_element.find(".next_pill_count").attr("value", "");
            row_element.find(".comment").attr("value", "");
            row_element.find(".missed_pills").attr("value", "");
            row_element.find(".missed_pills").removeAttr("readonly");
            row_element.find(".remove").show();
            cloned_object.attr("drug_row", next_drug_row);
            cloned_object.insertAfter('#tbl-dispensing-drugs tr:last');
            showFirstRemove();
            return false;
        }
    });
    function clearForm(form) {
        // iterate over all of the inputs for the form
        // element that was passed in
        $(':input', form).each(function () {
            var type = this.type;
            var tag = this.tagName.toLowerCase(); // normalize case
            // it's ok to reset the value attr of text inputs,
            // password inputs, and textareas
            if (type == 'text' || type == 'password' || tag == 'textarea')
                if ($(':input').is('[readonly]')) {

                } else {
                    this.value = "";
                }
            // checkboxes and radios need to have their checked state cleared
            // but should *not* have their 'value' changed
            else if (type == 'checkbox' || type == 'radio')
                this.checked = false;
            // select elements need to have their 'selectedIndex' property set to -1
            // (this works for both single and multiple select elements)
            else if (tag == 'select')
                if ($(this).attr('id') != 'ccc_store_id') {
                    this.selectedIndex = -1;
                }
        });
    }
    ;
    //function to remove drug row in table 
    $(".remove").click(function () {
        var rows = $("#tbl-dispensing-drugs > tbody").find("tr").length;
        var rem_row = this;
        if (rows > 1) {

            bootbox.confirm({
                message: "<h4>Remove?</h4>\n\<hr/><center>Are you sure?</center>",
                buttons: {
                    confirm: {
                        label: 'Yes',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'No',
                        className: 'btn-danger'
                    }
                },
                callback: function (res) {
                    if (res) {
                        $(rem_row).closest('tr').remove();
                    }
                }
            });
        } else {
            bootbox.alert("<h4>Remove Alert!</h4>\n\<hr/><center>Error!Cannot Delete Last Row Try Reset!</center>");
        }
    });
    $("#reset").click(function (e) {
        e.preventDefault();
        bootbox.confirm({
            message: "<h4>Reset?</h4>\n\<hr/><center>Are you sure?</center>",
            buttons: {
                confirm: {
                    label: 'Yes',
                    className: 'btn-success'
                },
                cancel: {
                    label: 'No',
                    className: 'btn-danger'
                }
            },
            callback: function (res) {
                if (res) {
                    reinitialize();
                    clearForm("#dispense_form");
                    resetRoutineDrugs();
                }
            }
        });
    });
    //-------------------------------- ADD, REMOVE, RESET END ----------------------------


    //------------------------------ START DATA PROCESSING -------------------------------
    function retrieveFormValues() {
        //This function loops the whole form and saves all the input, select, e.t.c. elements with their corresponding values in a javascript array for processing
        var dump = Array;
        $.each($("input, select, textarea"), function (i, v) {
            var theTag = v.tagName;
            var theElement = $(v);
            var theValue = theElement.val();
            if (theElement.attr('type') == "radio") {
                var text = 'input:radio[name=' + theElement.attr('name') + ']:checked';
                dump[theElement.attr("name")] = $(text).attr("value");
            } else {
                dump[theElement.attr("name")] = theElement.attr("value");
            }
        });
        return dump;
    }
    function retrieveFormValues_Array(name) {
        var dump = new Array();
        var counter = 0;
        $.each($("input[name=" + name + "], select[name=" + name + "], select[name=" + name + "]"), function (i, v) {
            var theTag = v.tagName;
            var theElement = $(v);
            var theValue = theElement.val();
            dump[counter] = theElement.attr("value");
            counter++;
        });
        return dump;
    }


    $(document).on('change', '.dose,.duration', function () {
        isoCount = 0;
        amountToDispense = (180 - amountispensed);
        var res = $(this).closest('tr').find('.drug option:selected').text();
        var dispensed = $(this).closest('tr').find('.qty_disp').val();
        duration_quantity($(this));
        if (res.indexOf('ISONIAZID') > -1) {
            if (parseInt(dispensed) > 180) {
                bootbox.alert("<h4>ISONIAZID MAX ALERT!</h4>\n\<hr/><center>You cannot dispense more than (" + amountToDispense + ") ISONIAZIDS for this patient!</center>");
                 $(this).closest('tr').find('.qty_disp').val('');
            } else if (parseInt(dispensed) > amountToDispense) {
                bootbox.alert("<h4>ISONIAZID MAX ALERT!</h4>\n\<hr/><center>You can only dispense (" + amountToDispense + ") ISONIAZIDS to this patient!</center>");
                 $(this).closest('tr').find('.qty_disp').val('');
                return false;
            }
        } else {
           
        }

    });






    //Function to validate required fields
    function processData(form) {
        var form_selector = "#" + form;
        var validated = $(form_selector).validationEngine('validate');
        if (!validated) {
            return false;

        } else {
            return saveData();
        }

    }
    //Function to post data to the server
    function saveData() {
        $("#btn_submit").attr("readonly", "readonly");
        var timestamp = new Date().getTime();
        var all_rows = $('#tbl-dispensing-drugs>tbody>tr');
        var msg = '';
        //Loop through all rows to check values
        $.each(all_rows, function (i, v) {

            var last_row = $(this);
            var drug_name = last_row.find(".drug option:selected").text();
            if (last_row.find(".drug").val() == 0) {
                msg += 'There is no commodity selected<br/>';
            }
            if (last_row.find(".batch").val() == 0) {
                msg += '<b>' + drug_name + '</b> : There is no batch for the commodity selected<br/>';
            }
            if (last_row.find(".duration").val() == 0 || last_row.find(".duration").val() == "" || isNaN(last_row.find(".duration").val()) == true) {
                msg += '<b>' + drug_name + '</b> :  You have not entered the duration<br/>';
            }
            if (last_row.find(".qty_disp").val() == 0 || last_row.find(".qty_disp").val() == "" || isNaN(last_row.find(".qty_disp").val()) == true) {
                msg += '<b>' + drug_name + '</b> :  You have not entered the quantity being dispensed for a commodity entered<br/>';
            }
            if (last_row.find(".qty_disp").hasClass("input_error") && last_row.find(".qty_disp").val() > stock_at_hand) {
                msg += '<b>' + drug_name + '</b> :  There is a commodity that has a quantity greater than the quantity available<br/>';
            }
<?php if ($pill_count !== "0") { ?>
                if (!last_row.find(".next_pill_count").val() && last_row.find(".drug option:selected").attr('arv_status') == 1) {
                    msg += '<b>' + drug_name + '</b> : You have not entered the pill count!<br/>';
                }
                if (!last_row.find(".missed_pills ").val() && last_row.find(".drug option:selected").attr('arv_status') == 1) {
                    msg += '<b>' + drug_name + '</b> : You have not entered the missed pills!<br/>';
                }
<?php } ?>

        });
        //Show Bootbox
        if (msg != '') {
            bootbox.alert("<h4>Alert!</h4>\n\<hr/><center>" + msg + "</center>");
            return;
        }

        var rowCount = $('#drugs_table>tbody tr').length;
        return true;
    }

    //------------------------------ END DATA PROCESSING ---------------------------------



    function loadRegimens(age) {
        var link = "<?php echo base_url(); ?>/public/regimen_management/getFilteredRegiments";
        var service = "<?php echo $service_name; ?>"
        var request = $.ajax({
            url: link,
            type: 'post',
            data: {"age": <?= $age; ?> ,"service":service},
            dataType: "json"
        });
        request.done(function (data) {
            //Remove appended options to reinitialize dropdown
            $('#current_regimen option')
                    .filter(function () {
                        return this.value || $.trim(this.value).length != 0;
                    }).remove();
            $(data).each(function (i, v) {
                $("#current_regimen").append("<option value='" + v.id + "'>" + v.regimen_code + " | " + v.regimen_desc + "</option>");
            });
        });
        request.fail(function (jqXHR, textStatus) {
            bootbox.alert("<h4>Regimens Details Alert</h4>\n\<hr/>\n\<center>Could not retrieve regimens details : </center>" + textStatus);
        });
    }

    function loadOtherDetails(patient_ccc) {
        //Load Non adherence reasons, regimen change reasons previously dispensed drugs
        var link = "<?php echo base_url(); ?>/public/dispensement_management/get_other_dispensing_details";
        var request = $.ajax({
            url: link,
            type: 'post',
            data: {"patient_ccc": patient_ccc},
            dataType: "json"
        });
        request.done(function (data) {
            var non_adherence_reasons = data.non_adherence_reasons;
            var regimen_change_reason = data.regimen_changes;
            var dcm_exit_reasons = data.dcm_exit_reasons;
            var patient_appointment = data.patient_appointment;

            //Remove appended options to reinitialize dropdown
            $('#non_adherence_reasons option')
                    .filter(function () {
                        return this.value || $.trim(this.value).length != 0;
                    }).remove();
            $(non_adherence_reasons).each(function (i, v) {
                $("#non_adherence_reasons").append("<option value='" + v.id + "'>" + v.name + "</option>");
            });
            //Load regimen change reasons
            $('#regimen_change_reason option')
                    .filter(function () {
                        return this.value || $.trim(this.value).length != 0;
                    }).remove();
            $(regimen_change_reason).each(function (i, v) {
                $("#regimen_change_reason").append("<option value='" + v.id + "'>" + v.name + "</option>");
            });

             //Load dcm exit reasons
            $('#dcm_exit_reason option')
                    .filter(function () {
                        return this.value || $.trim(this.value).length != 0;
                    }).remove();
            $(dcm_exit_reasons).each(function (i, v) {
                $("#dcm_exit_reason").append("<option value='" + v.id + "'>" + v.name + "</option>");
            });
            //Appointment date, If patient presiously visited,load previous appointment date
            if (patient_appointment.length > 0) {
                appointment_date = patient_appointment[0].appointment;
                $("#last_appointment_date").val(appointment_date); //Latest appointment date
                //loadMyPreviousDispensedDrugs();
                //------------------------------- PREVIOUS VISIT DATA
                var link = "<?php echo base_url(); ?>/public/dispensement_management/getPreviouslyDispensedDrugs";
                var request = $.ajax({
                    url: link,
                    type: 'post',
                    data: {"patient_ccc": patient_ccc, "ccc_store": $("#ccc_store_id").val()},
                    dataType: "json"
                });
                request.done(function (msg) {
                    $("#last_visit_data tbody").empty();
                    $(msg).each(function (i, v) {//Load last visit data
                        previous_dispensed_data = msg;
                        if (i == 0) {//Previous dispense details
                            previous_dispensing_date = v.dispensing_date;
                            $("#last_visit_date").val(previous_dispensing_date);
                            $("#last_visit").val(previous_dispensing_date);
                            $("#last_regimen_disp").val(v.regimen_code + " | " + v.regimen_desc);
                            $("#last_regimen").val(v.regimen_id);
                            checkIfDispensed(previous_dispensing_date, $("#dispensing_date").val());
                        }
                        $("#last_visit_data tbody").append("<tr><td>" + v.drug + "</td><td>" + v.quantity + "</td></tr>");
                    });
                });
                request.fail(function (jqXHR, textStatus) {
                    bootbox.alert("<h4>Previous Dispensing Details Alert</h4>\n\<hr/>\n\<center>Could not retrieve previously dispensed details : </center>" + textStatus);
                });
            }

            if (typeof appointment_date !== "undefined") {
                var diffDays = checkDaysLate(appointment_date);
                $("#days_count").attr("value", diffDays);
            }

        });
        request.fail(function (jqXHR, textStatus) {
            bootbox.alert("<h4>Regimens Details Alert</h4>\n\<hr/>\n\<center>Could not retrieve regimens details : </center>" + textStatus);
        });
    }

    function checkIfTested(service, patient_id) {
        if (service == 'prep') {
            $.getJSON('<?php echo base_url(); ?>/public/dispensement_management/get_prep_reasons', function (reasons) {
                bootbox.prompt({
                    title: "HIV TEST (PREP) | What is the Patient's PREP Reason?",
                    inputType: 'select',
                    inputOptions: reasons,
                    callback: function (prep_reason) {
                        bootbox.prompt({
                            title: "HIV TEST (PREP) | Has the Patient Been Tested?",
                            inputType: 'select',
                            inputOptions: [
                                {
                                    text: 'No',
                                    value: '0',
                                },
                                {
                                    text: 'Yes',
                                    value: '1',
                                }
                            ],
                            callback: function (is_tested) {
                                if (is_tested == true) {
                                    bootbox.prompt({
                                        title: "HIV TEST (PREP) | When was the Test Done?",
                                        inputType: 'date',
                                        callback: function (test_date) {
                                            bootbox.prompt({
                                                title: "HIV TEST (PREP) | What was the Test Positive?",
                                                inputType: 'select',
                                                inputOptions: [
                                                    {
                                                        text: 'No',
                                                        value: '0',
                                                    },
                                                    {
                                                        text: 'Yes',
                                                        value: '1',
                                                    }
                                                ],
                                                callback: function (test_result) {
                                                    var test_result_url = "<?php echo base_url(); ?>" + '/public/dispensement_management/update_prep_test/' + patient_id + '/' + prep_reason + '/' + is_tested + '/' + test_date + '/' + test_result
                                                    $.get(test_result_url, function (msg) {
                                                        bootbox.alert("<h4>HIV TEST (PREP)</h4>\n\<hr/><center>" + msg + "</center>")
                                                    });
                                                }
                                            });
                                        }
                                    });
                                } else {
                                    bootbox.alert("<h4>HIV TEST (PREP)</h4>\n\<hr/><center>Please ensure that the client is tested for the treatment to be effective</center>")
                                }
                            }
                        });
                    }
                });
            });
        }
    }

    function checkIfPregnant(pregnancy_status, patient_ccc) {
        if (pregnancy_status == '1') {

            bootbox.confirm({
                message: "<h4>Pregnancy confirmation</h4>\n\<hr/><center>Is patient still pregnant?</center>",
                buttons: {
                    confirm: {
                        label: 'Yes',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'No',
                        className: 'btn-danger'
                    }
                },
                callback: function (res) {
                    if (res === false) {//If answer is no, update pregnancy status
                        //Check if the current regimen is OI Medicine and if not, hide the indication field
                        var _url = "<?php echo base_url() . '/public/patient/updatePregnancyStatus'; ?>";
                        //Get drugs
                        var request = $.ajax({
                            url: _url,
                            type: 'post',
                            data: {"patient_ccc": patient_ccc},
                            dataType: "json"
                        });
                    }
                }
            });
        }
    }

    function checkIfHasTb(tb_status, patient_ccc) {
        if (tb_status == 1) {

            bootbox.confirm({
                message: "<h4>TB confirmation</h4>\n\<hr/><center>Is patient still having TB?</center>",
                buttons: {
                    confirm: {
                        label: 'Yes',
                        className: 'btn-success'
                    },
                    cancel: {
                        label: 'No',
                        className: 'btn-danger'
                    }
                },
                callback: function (res) {
                    if (res === false) {//If answer is no, update tbstatus
                        var _url = "<?php echo base_url() . '/public/patient/update_tb_status'; ?>";
                        //Get drugs
                        var request = $.ajax({
                            url: _url,
                            type: 'post',
                            data: {"patient_ccc": patient_ccc},
                            dataType: "json"
                        });
                    }
                }
            });
        }
    }
    /*********TESTING FUNCTION*********/
    function loadMyPreviousDispensedDrugs() {
        var link = "<?php echo base_url(); ?>/public/dispensement_management/getPreviouslyDispensedDrugs";
        var request = $.ajax({
            url: link,
            type: 'post',
            data: {"patient_ccc": patient_ccc},
            dataType: "json"
        });
        request.done(function (msg) {
            $("#last_visit_data tbody").empty();
            $(msg).each(function (i, v) {//Load last visit data
                previous_dispensed_data = msg;
                if (i == 0) {//Previous dispense details
                    previous_dispensing_date = v.dispensing_date;
                    $("#last_visit_date").val(previous_dispensing_date);
                    $("#last_visit").val(previous_dispensing_date);
                    $("#last_regimen_disp").val(v.regimen_code + " | " + v.regimen_desc);
                    $("#last_regimen").val(v.regimen_id);
                    checkIfDispensed(previous_dispensing_date, $("#dispensing_date").val());
                }
                $("#last_visit_data tbody").append("<tr><td>" + v.drug + "</td><td>" + v.quantity + "</td></tr>");
            });
        });
    }
    /***********************************/

    function checkIfDispensed(last_visit_date, dispensing_date) {//check if patient has already been dispensed drugs for current dispensing date
        if (last_visit_date) {
            //check if dispensing date is equal to last visit date
            if (last_visit_date == dispensing_date) {
                //if equal ask for alert
                bootbox.alert("<h4>Notice!</h4>\n\<center>You have dispensed drugs to this patient!</center>");
            }
        }
    }

    //function generate routine drugs
    function getRoutineDrugs(visits, total_visits, count) {
        //loop and add rows 
        for (var count = 0; count < (visits.length - 1); count++) {
            var row = $('#tbl-dispensing-drugs tr:last');
            var cloned_row = row.clone(true);
            //show remove link for cloned row
            cloned_row.find(".remove").show();
            //insert after cloned row
            cloned_row.insertAfter('#tbl-dispensing-drugs tr:last');
        }
        //loop through rows on table and assign commodities
        var rows = $('#tbl-dispensing-drugs tbody>tr');
        $.each(rows, function (i, v) {
            var current_row = $(this);
            //select drug
            current_row.find(".drug").val(visits[i].drug_id);
            //trigger drug change event
            current_row.find(".drug").trigger("change");
            //add previous dose 
            current_row.find(".dose").val(visits[i].dose);
            //add previous duration
            current_row.find(".duration").val(visits[i].duration);
            //add previous quantity dispensed
            current_row.find(".qty_disp").val(visits[i].quantity);
            //$(".qty_disp").trigger('keyup',[current_row]);
        });
        //Make focus on first line drug
        $('#tbl-dispensing-drugs tbody>tr:first').find('.duration').select();
        showFirstRemove();
    }

    //function reset routine drugs
    function resetRoutineDrugs() {
        //remove all table tr's except first one
        $("#tbl-dispensing-drugs tbody").find('tr').slice(1).remove();
        var row = $('#tbl-dispensing-drugs tr:last');
        //default options
        row.find(".unit").val("");
        row.find(".batch option").remove();
        row.find(".expiry").val("");
        row.find(".dose option").remove();
        row.find(".dose").val("");
        row.find(".pill_count").val("");
        row.find(".duration").val("");
        row.find(".qty_disp").val("");
        row.find(".soh").val("");
        row.find(".indication option").remove();
        routine_check = 0;
        hideFirstRemove();
    }

    function resetFields(row) {
        row.closest("tr").find(".qty_disp").val("");
        row.closest("tr").find(".soh").val("");
        //row.closest("tr").find(".indication").val("");
        row.closest("tr").find(".duration").val("");
        row.closest("tr").find(".expiry").val("");
        row.closest("tr").find(".pill_count").val("");
        row.closest("tr").find(".missed_pills").val("");
    }

    function hideFirstRemove() {
        $("#drugs_table tbody > tr:first").find(".remove").hide();
    }

    function showFirstRemove() {
        $("#drugs_table tbody > tr:first").find(".remove").show();
    }

    function checkDaysLate(appointment_date) {//Check how many days the patient is late
        var diffDays = 0;
        var dispensing_date = $.datepicker.parseDate('yy-mm-dd', $("#dispensing_date").val());
        var appointment_date = $.datepicker.parseDate('yy-mm-dd', appointment_date);
        if (appointment_date != null) {
            var timeDiff = dispensing_date.getTime() - appointment_date.getTime();
            var diffDays = Math.floor(timeDiff / (1000 * 3600 * 24));
        }
        return diffDays;
    }

    //function to check the next appointment date to populate duration
    function checkAppointment() {
        if ($("#days_to_next").val() != " ")
        {
            var days = $("#days_to_next").val();
            $("#days_to_next").val();
        }

    }
    //function to calculate adherence rate(%)
    function getAdherenceRate() {
        $("#adherence").attr("value", " ");
        $("#adherence").removeAttr("readonly");
        var purpose_of_visit = $("#purpose option:selected").val();
        var day_percentage = 0;
        if (purpose_of_visit.toLowerCase().indexOf("routine") == -1 || purpose_of_visit.toLowerCase().indexOf("pmtct") == -1) {
            if (typeof previous_dispensing_date !== "undefined" && appointment_date !== "undefined") {
                var dispensing_date = $.datepicker.parseDate('yy-mm-dd', $("#dispensing_date").val()); //Current dispensing date
                var prev_visit_date = $.datepicker.parseDate('yy-mm-dd', previous_dispensing_date); //Previous dispensing date
                appoint_date = $.datepicker.parseDate('yy-mm-dd', appointment_date);
                var days_to_next_appointment = Math.floor((prev_visit_date.getTime() - appoint_date.getTime()) / (1000 * 3600 * 24));
                var days_missed_appointment = Math.floor((appoint_date.getTime() - dispensing_date.getTime()) / (1000 * 3600 * 24));
                //Formula
                day_percentage = ((days_to_next_appointment - days_missed_appointment) / days_to_next_appointment) * 100;
                if (day_percentage > 100) {
                    day_percentage = 100;
                    day_percentage = day_percentage.toFixed(2) + "%";
                } else {
                    day_percentage = day_percentage.toFixed(2) + "%";
                }

                if (day_percentage < 1) {day_percentage = 0;}

                $("#adherence").attr("value", day_percentage);
                $("#adherence").attr("readonly", "readonly");
            }
        }
    }

    function retrieveAppointedPatients() {
        $("#scheduled_patients").html("");
        $('#scheduled_patients').hide();
        //Function to Check Patient Number exists
        var base_url = "<?php echo base_url(); ?>";
        var appointment = $("#next_appointment_date").val();
        var link = base_url + "/public/patient/getAppointments/" + appointment;
        $.ajax({
            url: link,
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                var all_appointments_link = "<a class='link' target='_blank' href='<?php echo base_url() . '/public/report_management/getScheduledPatients/'; ?>" + appointment + "/" + appointment + "' style='font-weight:bold;color:red;'>View appointments</a>";
                var html = "Patients Scheduled on Date: <b>" + data[0].total_appointments + "</b> Patients" + all_appointments_link;
                var new_date = new Date(appointment);
                var formatted_date_day = new_date.getDay();
                var days_of_week = ["Sunday", "Monday", "Tuseday", "Wednesday", "Thursday", "Friday", "Saturday"];
                if (formatted_date_day == 6 ) {
                    bootbox.alert("<h4>Weekend Alert</h4>\n\<hr/><center>It will be on " + days_of_week[formatted_date_day] + " During the Weekend </center>");
                    if (parseInt(data[0].total_appointments) > parseInt(data[0].weekend_max)) {
                        bootbox.alert("<h4>Excess Appointments</h4>\n\<hr/><center>Maximum Appointments for Weekend Reached</center>");
                    }
                } else {
                    if (parseInt(data[0].total_appointments) > parseInt(data[0].weekday_max)) {
                        bootbox.alert("<h4>Excess Appointments</h4>\n\<hr/><center>Maximum Appointments for Weekday Reached</center>");
                    }
                }
                $("#scheduled_patients").append(html);
                $('#scheduled_patients').show();
            }
        });
    }

    function getPillCount(dose_qty, dose_frequency, total_actual_drugs) {
        var days_issued = $("#days_to_next").val();
        var error_message = "";
        if (!days_issued) {
            error_message += "Days to Next Appointment not Selected \r\n";
        }
        if (!dose_qty) {
            error_message += "Dose has no Value \r\n";
        }
        if (!dose_frequency) {
            error_message += "Dose has no Frequency \r\n";
        }
        if (!total_actual_drugs) {
            error_message += "No Quantity to Dispense Selected \r\n";
        }
        if (error_message) {
            // alert(error_message);
        } else {
            var drugs_per_day = (dose_qty * dose_frequency);
            var total_expected_drugs = (drugs_per_day * days_issued);
            var pill_count = (total_actual_drugs - total_expected_drugs);
            return pill_count;
        }
    }
    function getActualPillCount(days_issued, dose_qty, dose_frequency, prev_pill_count, prev_qty) {
        var error_message = "";
        if (!dose_qty) {
            error_message += "Dose has no Value \r\n";
        }
        if (!dose_frequency) {
            error_message += "Dose has no Frequency \r\n";
        }
        if (!prev_pill_count) {
            error_message += "No Quantity to Dispense Selected \r\n";
        }
        if (error_message) {
            //alert(error_message);
        } else {
            var group_A = (prev_qty - prev_pill_count);
            var group_B = (days_issued * (dose_qty * dose_frequency))
            var pill_count = (group_A - group_B);
            return pill_count;
        }
    }

    function reinitialize() {
        alert_qty_check = true;
        $(".unit").val('');
        $(".batch option").remove();
        $(".expiry").val('');
        $(".dose").val('');
        $(".dose option").remove();
        $(".doselist").val('');
        $(".pill_count ").val('');
        $(".next_pill_count ").val('');
        $(".duration").val('');
        $(".qty_disp ").val('');
        $(".soh").val('');
        $(".brand option").remove();
        $(".indication option").remove();
        $(".comment ").val('');
        $(".missed_pills ").val('');
        $(".qty_disp").css("background-color", "white");
        $(".qty_disp").removeClass("input_error");
    }

    function storeSession(ccc_id) {
        var url = "<?php echo base_url() . '/public/dispensement_management/save_session'; ?>"
        $.post(url, {'session_name': 'ccc_store_id', 'session_value': ccc_id});
    }
</script>