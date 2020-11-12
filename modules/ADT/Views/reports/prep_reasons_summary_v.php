<div id="wrapperd">

	<div id="patient_prep_content" class="full-content">
		<?php echo view("\Modules\ADT\Views\\reports\\reports_top_menus_v"); ?>
		<h4 style="text-align: center" id='report_title'>PREP Reasons Summary From <span id="start_date"><?php echo $from; ?></span> to <span id="end_date"><?php echo $to; ?></span></h4>
		<hr size="1" style="width:80%">
		<?php echo  $dyn_table; ?>
		<a href="<?= base_url() ?>/report_management/get_prep_reasons_patients/<?= $from; ?>/<?= $to; ?>" class="btn btn-warning btn-sm">see patients list</a>
	</div>
</div>