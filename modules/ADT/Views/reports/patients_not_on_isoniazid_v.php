<div id="wrapperd">

	<div id="patient_enrolled_content" class="full-content">
		<?php echo view("\Modules\ADT\Views\\reports\\reports_top_menus_v"); ?>
		<h4 style="text-align: center" id='report_title'>Patients who have never been on isoniazid</h4>
		<hr size="1" style="width:80%">
		<table width="50%" style="margin-bottom: 10px; margin: 0 auto;">
			<tr>
				<td colspan="3">
					<h5 class="report_title" style="text-align:center;font-size:14px;">Number of patients:
						<span id="total_count"><?php echo $all_count; ?></span></h5>
				</td>
			</tr>
		</table>
		<?php echo $dyn_table; ?>
	</div>
</div>