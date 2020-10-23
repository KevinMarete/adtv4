<!--Custom CSS files-->
<link href="<?php echo base_url().'/public/assets/modules/patients/listing.css'; ?>" type="text/css" rel="stylesheet"/>

<!--container-->
<div class="container-fluid center-content">
    <!--message row-->
    <div class="row-fluid">
        <div class="span12">
			<?php 
				$session = session();
				
			  	if($session->get("msg_save_transaction")){

			?>
				<?php
				if($session->get("msg_save_transaction")=="success"){
					?>
				<div class="alert alert-success">
	              <button type="button" class="close" data-dismiss="alert">&times;</button>
				    <?php echo $session->get("user_enabled");  ?>
				    <?php echo $session->getFlashdata("dispense_updated");  ?>
				</div> 	
					<?php
				}
				else{
					?>
				  <div class="alert alert-success">
	               <button type="button" class="close" data-dismiss="alert">&times;</button>
				     Your data were not saved ! Try again or contact your system administrator.
				   </div> 	
				<?php
				}
				$session->remove('msg_save_transaction');
			  }
			?>
        </div>
    </div>

    <h3 style="margin-top: 0; width: auto;">

    Filter Patients Based on Status: 
    	<select id="filter">
    		<option value="0"><strong>Active</strong></option>
    		<option value="1"><strong>Inactive</strong></option>
    		<option value="2"><strong>All</strong></option>
    	</select>

 <!--    	<button id="btn_filter">Filter</button> -->

    </h3>

    <!--table row-->
    <div class="row-fluid" style="margin-top: 0;">
        <div class="span12">
            <div class="table-responsive">
                <table class="table table-bordered table-condensed table-hover" id="patient_listing">
                    <thead>
                        <tr>
							<?php if($medical_number == '1'){ ?>
							<th>Medical Number</th>
							<th>CCC No</th>
							<?php }else {?>
							<th>CCC No</th>
							<?php } ?>
							<th>Patient Name</th>
							<th>Next Appointment</th>
							<?php if($medical_number !== '1'){ ?><th>Contact</th> <?php }?>
							<th>Current Regimen</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
	                    <tr>
							<?php if($medical_number == '1'){ ?>
							<th>Medical Number</th>
							<th>CCC No</th>
							<?php }else {?>
							<th>CCC No</th>
							<?php } ?>
							<th>Patient Name</th>
							<th>Next Appointment</th>
							<?php if($medical_number !== '1'){ ?><th>Contact</th> <?php }?>
							<th>Current Regimen</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
                    </tfoot>    	
                </table>
            </div>
        </div>
    </div>
</div>

<!--custom js-->
<script src="<?php echo base_url(); ?>/public/assets/modules/patients/listing.js"></script>

