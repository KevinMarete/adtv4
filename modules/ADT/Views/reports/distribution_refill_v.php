<div id="wrapperd">

	<div id="patient_enrolled_content" class="full-content">
		<?php echo view("\Modules\ADT\Views\\reports\\reports_top_menus_v"); ?>
		<h4 style="text-align: center" id='report_title'><?= $report_title; ?> As at <?php echo date('M-Y', strtotime($to)); ?> </h4>
		<hr size="1" style="width:80%">
		<table align='center' width='20%' style="font-size:16px; margin-bottom: 20px">
			<tr>
				<td colspan="2">
					<h5 class="report_title" style="text-align:center;font-size:14px;">Number of patients: <span id="total_count"><?php echo $overall_total; ?></span></h5>
				</td>
			</tr>
		</table>
		<div id="appointment_list">
			<?php echo $dyn_table; ?>


		</div>
	</div>
</div>