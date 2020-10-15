
<div id="wrapperd">
	<div id="patient_enrolled_content" class="full-content">
		<?php $this->load->view("reports/reports_top_menus_v") ?>
		<h4 style="text-align: center" id='report_title'><?= $report_title;?></h4>
		<hr size="1" style="width:80%">
         <table align='center'  width='50%' style="margin-bottom: 10px">
			<tr>
				<td colspan="3"><h5 class="report_title" style="text-align:center;font-size:14px;">Number of patients: <span id="total_count"><?php echo $all_count;?></span></h5></td>
			</tr>
		</table>
         <?php echo $dyn_table;?>
	</div>
</div>
