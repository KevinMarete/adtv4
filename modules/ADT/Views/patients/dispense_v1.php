<!--dispensing form-->
<div class="container full-content" style="background:#FFA8E7">
	<form id="dispensing_frm" action="" method="POST">
		<input type="hidden" id="hidden_data" data-baseurl="<?php echo base_url(); ?>" data-patient_id="<?php echo $patient_id;?>">
		<!--breadcrumb & instruction row-->
	    <div class="row-fluid">
		    <div class="span12">
			    <ul class="breadcrumb">
					<li><a href="<?php echo base_url().'/public/patients'; ?>">Patients</a> <span class="divider">/</span></li>
				  	<li>
				  		<a href="<?php echo base_url().'/public/patient/load_view/details/'.$patient_id; ?>">
				  		<span class="patient_name_link"></span>
				  		</a><span class="divider">/</span> 
				  	</li>
				  	<li class="active">Dispensing</li>
				</ul>
				<div class="alert alert-info">
				    <button type="button" class="close" data-dismiss="alert">&times;</button>
					<h4>Mandatory!</h4>
					(Fields Marked with <b><span class='astericks'>*</span></b> Asterisks are required)
				</div>
		    </div>
		</div>
		<!--main information row-->
		<div class="row-fluid">
			<!--Current information row-->
		    <div class="span6">
		    	<fieldset>
					<legend>
						<h3>Dispensing Information</h3>
					</legend>
					<div class="row-fluid">
	                    <div class="span12 control-group">
							<label><span class='astericks'>*</span>Dispensing Point</label>
							<select name="ccc_store_sp" id="ccc_store_sp" class="validate[required] span12"></select>
	                    </div>
	                </div>
	                <div class="row-fluid">
	                    <div class="span6 control-group">
	                        <label>Patient CCC Number</label>
	                        <input type="text" readonly="" id="patient_id" class="span12"/>
	                    </div>
	                    <div class="span6 control-group">
	                        <label>Patient Name</label>
	                        <input type="text" readonly="" id="patient_name" class="span12 patient_name"/>
	                    </div>
	                </div>
	                <div class="row-fluid">
	                    <div class="span6 control-group">
	                        <label><span class='astericks'>*</span>Dispensing Date</label>
	                        <input type="text" name="dispensing_date" id="dispensing_date" class="validate[required] span12">
	                    </div>
	                    <div class="span6 control-group">
	                        <label><span class='astericks'>*</span>Purpose of Visit</label>
	                        <select name="visit_purpose" id="visit_purpose" class="validate[required] span12"></select>   
	                    </div>
	                </div>
	                <div class="row-fluid">
	                    <div class="span6 control-group">
	                        <label>Current Height(cm)</label>
	                        <input type="number" name="current_height" id="current_height" class="validate[required] span12" min="0"/>
	                    </div>
	                    <div class="span6 control-group">
	                        <label><span class='astericks'>*</span>Current Weight(kg)</label>
	                        <input type="number" name="current_weight" id="current_weight" class="validate[required] span12" min="0"/>
	                    </div>
	                </div>
	                <div class="row-fluid">
	                    <div class="span6 control-group">
	                        <label><span class='astericks'>*</span>Days to Next Appointment</label>
	                        <input type="number" id="days_to_next" class="validate[required] span12" min="0"/>
	                    </div>
	                    <div class="span6 control-group">
	                        <label><span class='astericks'>*</span>Date of Next Appointment</label>
	                        <input type="text" name="next_appointment_date" id="next_appointment_date" class="validate[required] span12"/>
	                    </div>
	                </div>
	                <div class="row-fluid">
	                    <div class="span12 control-group scheduled_patients" style="display:none;background:#9CF;">
	                        <span id="scheduled_patients"></span>
	                    </div>
	                </div>
					<div class="row-fluid">
	                    <div class="span6 control-group">
							<label>Last Regimen Dispensed</label>
							<select name="last_regimen" id="last_regimen" class="span12" disabled="" =""></select> 
	                    </div>
	                    <div class="span6 control-group">
							<label><span class='astericks'>*</span>Current Regimen</label>
							<select name="regimen" id="current_regimen" class="validate[required] span12"></select>
	                    </div>
	                </div>
					<div class="row-fluid">
	                    <div class="span12 control-group regimen_change_reason_container" style="display:none;">
	                        <label><span class='astericks'>*</span>Regimen Change Reason</label>
	                        <select name="regimen_change_reason" id="regimen_change_reason" class="span12"></select>
	                    </div>
	                </div>
	                <div class="row-fluid">
	                    <div class="span6 control-group">
	                        <label>Appointment Adherence (%)</label>
	                        <input type="text" name="adherence" id="adherence" class="span12" readonly=""/>
	                    </div>
	                    <div class="span6 control-group">
	                        <label> Poor/Fair Adherence Reasons </label>
	                        <select name="non_adherence_reason" id="non_adherence_reason" class="span12"></select>
	                    </div>
	                </div>      
				</fieldset>
		    </div>
			<!--Previous information row-->
			<div class = "span6">
				<fieldset>
					<legend>
						<h3>Previous Patient Information</h3>
					</legend>
					<div class="row-fluid">
	                    <div class="span6 control-group">
	                        <label>Appointment Date</label>
	                       	<input type="text" id="appointment_date" readonly=""  class="span12"/>
	                    </div>
	                    <div class="span6 control-group">
	                        <label>Previous Visit Date</label>
	                        <input type="text" id="prev_visit_date" readonly="" class="span12"/>
	                    </div>
	                </div>
	                <div class="row-fluid">
	                    <div class="span12 control-group">
	                        <table class="table table-bordered table-condensed table-hover table-striped">
								<thead>
									<tr>
										<th>Previous Drug</th>
										<th>Previous Quantity</th>
									</tr>
								</thead>
								<tbody class="prev_visit_data">
									<tr>
										<td colspan="2"><div class="text-center">No History Available</div></td>
									</tr>
								</tbody>
				            </table>
	                    </div>
	                </div>
				</fieldset>
			</div>
		</div>
		<!--drug row-->
		<div class="row-fluid">
			<div class="span12">
				<fieldset>
					<legend>
						<h3>Drug Details</h3>
					</legend>
	            	<table class="table table-bordered table-condensed table-hover table-striped" id="dispensing_drugs">
	                	<thead>
	                    	<tr>
		                        <th>Drug</th>
		                        <th>Unit</th>
		                        <th>Batch Number</th>
		                        <th>Expiry Date</th>
		                        <th>Dose</th>
		                        <th>Pill Count<br/><i>(expected)</i></th>
		                        <th>Pill Count<br/><i>(actual)</i></th>
		                        <th>Duration</th>
		                        <th>Quantity</th>
		                        <th>Stock on Hand</th>
		                        <th>Indication</th>
		                        <th>Comment</th>
		                        <th>Missed Pills</th>
		                        <th>Action</th>
	                    	</tr>
	                	</thead>
	                	<tbody>
	                		<tr>
			                    <td>
			                    	<select name="drug[]" class="drug span12"></select>
			                    </td>
			                    <td>
			                        <input type="text" name="unit[]" class="unit span12" readonly=""/>
			                    </td>
			                    <td>
			                    	<select name="batch[]" class="batch span12"></select>
			                    </td>
			                    <td>
			                        <input type="text" name="expiry[]" name="expiry" class="expiry span12" readonly=""/>
			                    </td>
			                    <td>
			                        <input list="doses" class="dose span12" name="dose[]">
									<datalist id="doses"></datalist>
			                    </td>
			                    <td>
			                        <input type="number" name="expected_pill_count[]" class="expected_pill_count span12" readonly="readonly" min="0"/>
			                    </td>
			                    <td>
			                        <input type="number" name="actual_pill_count[]" class="actual_pill_count span12" min="0"/>
			                    </td>
			                    <td>
			                        <input type="number" name="duration[]" class="duration span12" min="0"/>
			                    </td>
			                    <td>
			                        <input type="number" name="qty_disp[]" class="qty_disp span12 validate[required]" min="0"/>
			                    </td>
			                    <td>
			                        <input type="number" name="soh[]" class="soh span12" readonly="" min="0"/>
			                    </td>
			                    <td>
			                        <select name="indication[]" class="indication span12">
			                            <option value="0">None</option>
			                        </select></td>
			                    <td>
			                        <input type="text" name="comment[]" class="comment span12"/>
			                    </td>
			                    <td>
			                        <input type="number" name="missed_pills[]" class="missed_pills span12" min="0"/>
			                    </td>
			                    <td>
			                        <!--<a class="add btn-small">Add</a>|<a class="remove btn-small">Remove</a>-->
			                        <div class="btn-group">
									  	<button type="button" class="btn btn-success"><i class="icon-plus"></i></button>
									  	<button type="button" class="btn btn-danger"><i class="icon-minus"></i></button>
									</div>
			                    </td>
			                </tr>
	                	</tbody>
	                </table>
				</fieldset>
			</div>
		</div>
		<!--button row-->
		<div class="row-fluid">
			<div class="span12 control-group">
				<div class="btn-group pull-right">
				  	<button type="reset" id="reset_btn" class="btn btn-danger"><i class="icon-refresh"></i> Reset Fields</button>
				  	<button type="button" id="print_btn" class="btn btn-info"><i class="icon-print"></i> Print Labels</button>
				  	<button type="submit" id="save_btn" class="btn btn-success"><i class="icon-ok-sign"></i> Dispense Drugs</button>
				</div>
			</div>
		</div>
    </form>
</div>

<!-- custom scripts-->
<script src="<?php echo base_url().'/public/assets/modules/forms/forms.js'; ?>"></script>
<script src="<?php echo base_url().'/public/assets/modules/patients/dispensing.js'; ?>"></script>