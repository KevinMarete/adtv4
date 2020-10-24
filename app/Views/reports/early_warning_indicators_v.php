<div id="wrapperd">
	
	<div id="patient_enrolled_content" class="full-content">
		<?php $this->load->view("reports/reports_top_menus_v") ?>
		<h4 style="text-align: center">Listing of HIV Drugs Resistance Early Warning Indicators Between <span class="green"><?php echo $from; ?></span> And <span class="green"><?php echo $to; ?></span></h4>
		<hr size="1" style="width:80%">
		<div class="patient_percentage">
			<h3 style="text-align: center;margin:0 auto;">Percentage of patients started on first line regimens [Target 100%]</h3>
			<table class="listing_table" id="percentage_patients" cellspacing="5" border='1'>
				<tr>
					<th> Enrolled</th>
					<th> Started </th>
					<th> % Starting standard 1st Line regimens</th>
					<th> % Starting alternative 1st Line regimens</th>
				</tr>
				<tr>
					<td align="center"><?php echo $tot_patients ?></td>
					<td align="center"><?php echo $first_line ?></td>
					<td align="center"><?php echo number_format($percentage_firstline,1); ?>
					</td><td align="center"><?php echo number_format($percentage_onotherline,1); ?></td></tr>
			</table>
		</div>
		
		<div class="retention_percentage">
			<h3 style="text-align: center;margin:0 auto;"> Percentage of patients retained on first line at month 12 [Target >95%]</h3>
			<table class="listing_table" id="percentage_retention" cellpadding="5" border="1">
				<tr>
					<th> Still on first line </th>
					<th> % Still on first line </th>
					<th> % Alternative first Line regimens</th>
					<th> % Second line</th>
				</tr>
				<tr><td align="center"><?php echo $stil_in_first_line; ?></td><td align="center"><?php echo $total_from_period;?></td><td align="center"><?php echo $percentage_stillfirstline ?></td></tr>
			</table>
		</div>
		<div class="lost_to_follow_up_percentage" cellpadding="5">
			<h3 style="text-align: center;margin:0 auto;">Periodic cohort retention</h3>
			<table class="listing_table" id="percentage_lost_to_follow_up" border="1">
				<tr>
					<th colspan="2">3 Months </th>
					<th colspan="2">6 Months back </th>
					<th colspan="2">12 Months back </th>
					<th colspan="2">24 Months back </th>	                           
				</tr>
				<tr>
					<th>Started</th>
					<th>Retained</th>
					<th>Started</th>
					<th>Retained</th>
					<th>Started</th>
					<th>Retained</th>
					<th>Started</th>
					<th>Retained</th>
				</tr>
				<tbody>
					<tr>
						<td>Started</td>
						<td>Retained</td>
						<td>Started</td>
						<td>Retained</td>
						<td>Started</td>
						<td>Retained</td>
						<td>Started</td>
						<td>Retained</td>
					</tr>
				</tbody>

				
			</table>
		</div>
		
	</div>
</div>
