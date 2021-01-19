<style type="text/css">
	.bold {
		font-weight: bold;
	}
</style>
<div id="wrapperd">
	<div id="patient_enrolled_content" class="full-content">
		<?php echo view("\Modules\ADT\Views\\reports\\reports_top_menus_v"); ?>
		<h4 style="text-align: center" id='report_title'><?= $report_title; ?> As at <?php echo date('M-Y', strtotime($to)); ?> </h4>
		<hr size="1" style="width:80%">
		<div id="appointment_list">
			<table cellpadding="5" border="1" width="100%" style="border:1px solid #DDD;">
				<thead>
					<tr>
						<th colspan="4">
							Pharmacy Report
						</th>
					</tr>
					<tr>
						<th colspan="2">
							FACILITY MONTHLY ARV PATIENT SUMMARY (F-MAPS) Report (MoH 729B)
						</th>
						<th colspan="2">
							MoH 729B
						</th>
					</tr>
				</thead>
				<tr>
					<td class="bold">Facility Name</td>
					<td class="bold"><?= $facility['facility_name'] ?> </td>


					<td class="bold">Facility code</td>
					<td class="bold"><?= $facility['facility_code'] ?> </td>
				</tr>
				<tr>
					<td class="bold">County</td>
					<td class="bold"><?= $facility['facility_county'] ?> </td>

					<td class="bold">Sub County</td>
					<td class="bold"><?= $facility['facility_subcounty'] ?> </td>
				</tr>
				<tr>
					<td class="bold">Compiled by</td>
					<td> </td>
					<td> </td>
					<td> </td>
				</tr>
				<tr>
					<td>Designation</td>
					<td></td>
					<td>Phone Number</td>
					<td></td>
				</tr>
				<tr>
					<td colspan="4">ARV Dispensing Quantity (ARV Dispensed to patients)</td>
				</tr>
			</table>
			<br>
			<br>
			<table id="mmd_mms_report" cellpadding="5" border="1" width="100%" style="border:1px solid #DDD;">
				<thead>
					<tr>
						<th class=""></th>
						<th class="" colspan="2">&lt;1</th>
						<th class="" colspan="2">1-4</th>
						<th class="" colspan="2">5-9</th>
						<th class="" colspan="2">10-14</th>
						<th class="" colspan="2">15-19</th>
						<th class="" colspan="2">20-24</th>
						<th class="" colspan="2">25-29</th>
						<th class="" colspan="2">30-34</th>
						<th class="" colspan="2">35-39</th>
						<th class="" colspan="2">40-44</th>
						<th class="" colspan="2">45-49</th>
						<th class="" colspan="2">50+</th>
						<th class="">Sub Total</th>
					</tr>
					<tr>
						<td class=""></td>
						<td class="">M</td>
						<td class="">F</td>
						<td class="">M</td>
						<td class="">F</td>
						<td class="">M</td>
						<td class="">F</td>
						<td class="">M</td>
						<td class="">F</td>
						<td class="">M</td>
						<td class="">F</td>
						<td class="">M</td>
						<td class="">F</td>
						<td class="">M</td>
						<td class="">F</td>
						<td class="">M</td>
						<td class="">F</td>
						<td class="">M</td>
						<td class="">F</td>
						<td class="">M</td>
						<td class="">F</td>
						<td class="">M</td>
						<td class="">F</td>
						<td class="">M</td>
						<td class="">F</td>
						<td class=""></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="">1 MONTHS</td>
						<td class=""><?= $results['1MONEMONTH']; ?></td>
						<td class=""><?= $results['1FONEMONTH']; ?></td>
						<td class=""><?= $results['1-4MONEMONTH']; ?></td>
						<td class=""><?= $results['1-4MONEMONTH']; ?></td>
						<td class=""><?= $results['5-9MONEMONTH']; ?></td>
						<td class=""><?= $results['5-9FONEMONTH']; ?></td>
						<td class=""><?= $results['10-14MONEMONTH']; ?></td>
						<td class=""><?= $results['10-14FONEMONTH']; ?></td>
						<td class=""><?= $results['15-19MONEMONTH']; ?></td>
						<td class=""><?= $results['15-19FONEMONTH']; ?></td>
						<td class=""><?= $results['20-24MONEMONTH']; ?></td>
						<td class=""><?= $results['20-24FONEMONTH']; ?></td>
						<td class=""><?= $results['25-29MONEMONTH']; ?></td>
						<td class=""><?= $results['25-29FONEMONTH']; ?></td>
						<td class=""><?= $results['30-34MONEMONTH']; ?></td>
						<td class=""><?= $results['30-34FONEMONTH']; ?></td>
						<td class=""><?= $results['35-39MONEMONTH']; ?></td>
						<td class=""><?= $results['35-39FONEMONTH']; ?></td>
						<td class=""><?= $results['40-44MONEMONTH']; ?></td>
						<td class=""><?= $results['40-44FONEMONTH']; ?></td>
						<td class=""><?= $results['45-49MONEMONTH']; ?></td>
						<td class=""><?= $results['45-49FONEMONTH']; ?></td>
						<td class=""><?= $results['50MONEMONTH']; ?></td>
						<td class=""><?= $results['50FONEMONTH']; ?></td>
						<td class=""><?= $results['SUBTOTAL1MONTH']; ?></td>
					</tr>
					<tr>
						<td class="">2 MONTHS</td>
						<td class=""><?= $results['1MTWOMONTH']; ?></td>
						<td class=""><?= $results['1FTWOMONTH']; ?></td>
						<td class=""><?= $results['1-4MTWOMONTH']; ?></td>
						<td class=""><?= $results['1-4MTWOMONTH']; ?></td>
						<td class=""><?= $results['5-9MTWOMONTH']; ?></td>
						<td class=""><?= $results['5-9FTWOMONTH']; ?></td>
						<td class=""><?= $results['10-14MTWOMONTH']; ?></td>
						<td class=""><?= $results['10-14FTWOMONTH']; ?></td>
						<td class=""><?= $results['15-19MTWOMONTH']; ?></td>
						<td class=""><?= $results['15-19FTWOMONTH']; ?></td>
						<td class=""><?= $results['20-24MTWOMONTH']; ?></td>
						<td class=""><?= $results['20-24FTWOMONTH']; ?></td>
						<td class=""><?= $results['25-29MTWOMONTH']; ?></td>
						<td class=""><?= $results['25-29FTWOMONTH']; ?></td>
						<td class=""><?= $results['30-34MTWOMONTH']; ?></td>
						<td class=""><?= $results['30-34FTWOMONTH']; ?></td>
						<td class=""><?= $results['35-39MTWOMONTH']; ?></td>
						<td class=""><?= $results['35-39FTWOMONTH']; ?></td>
						<td class=""><?= $results['40-44MTWOMONTH']; ?></td>
						<td class=""><?= $results['40-44FTWOMONTH']; ?></td>
						<td class=""><?= $results['45-49MTWOMONTH']; ?></td>
						<td class=""><?= $results['45-49FTWOMONTH']; ?></td>
						<td class=""><?= $results['50MTWOMONTH']; ?></td>
						<td class=""><?= $results['50FTWOMONTH']; ?></td>
						<td class=""><?= $results['SUBTOTAL2MONTH']; ?></td>
					</tr>
					<tr>
						<td class="">3 MONTHS</td>
						<td class=""><?= $results['1MTHREEMONTH']; ?></td>
						<td class=""><?= $results['1FTHREEMONTH']; ?></td>
						<td class=""><?= $results['1-4MTHREEMONTH']; ?></td>
						<td class=""><?= $results['1-4FTHREEMONTH']; ?></td>
						<td class=""><?= $results['5-9MTHREEMONTH']; ?></td>
						<td class=""><?= $results['5-9FTHREEMONTH']; ?></td>
						<td class=""><?= $results['10-14MTHREEMONTH']; ?></td>
						<td class=""><?= $results['10-14FTHREEMONTH']; ?></td>
						<td class=""><?= $results['15-19MTHREEMONTH']; ?></td>
						<td class=""><?= $results['15-19FTHREEMONTH']; ?></td>
						<td class=""><?= $results['20-24MTHREEMONTH']; ?></td>
						<td class=""><?= $results['20-24FTHREEMONTH']; ?></td>
						<td class=""><?= $results['25-29MTHREEMONTH']; ?></td>
						<td class=""><?= $results['25-29FTHREEMONTH']; ?></td>
						<td class=""><?= $results['30-34MTHREEMONTH']; ?></td>
						<td class=""><?= $results['30-34FTHREEMONTH']; ?></td>
						<td class=""><?= $results['35-39MTHREEMONTH']; ?></td>
						<td class=""><?= $results['35-39FTHREEMONTH']; ?></td>
						<td class=""><?= $results['40-44MTHREEMONTH']; ?></td>
						<td class=""><?= $results['40-44FTHREEMONTH']; ?></td>
						<td class=""><?= $results['45-49MTHREEMONTH']; ?></td>
						<td class=""><?= $results['45-49FTHREEMONTH']; ?></td>
						<td class=""><?= $results['50MTHREEMONTH']; ?></td>
						<td class=""><?= $results['50FTHREEMONTH']; ?></td>
						<td class=""><?= $results['SUBTOTAL3MONTH']; ?></td>
					</tr>
					<tr>
						<td class="">4 MONTHS</td>
						<td class=""><?= $results['1MFOURMONTH']; ?></td>
						<td class=""><?= $results['1FFOURMONTH']; ?></td>
						<td class=""><?= $results['1-4MFOURMONTH']; ?></td>
						<td class=""><?= $results['1-4FFOURMONTH']; ?></td>
						<td class=""><?= $results['5-9MFOURMONTH']; ?></td>
						<td class=""><?= $results['5-9FFOURMONTH']; ?></td>
						<td class=""><?= $results['10-14MFOURMONTH']; ?></td>
						<td class=""><?= $results['10-14FFOURMONTH']; ?></td>
						<td class=""><?= $results['15-19MFOURMONTH']; ?></td>
						<td class=""><?= $results['15-19FFOURMONTH']; ?></td>
						<td class=""><?= $results['20-24MFOURMONTH']; ?></td>
						<td class=""><?= $results['20-24FFOURMONTH']; ?></td>
						<td class=""><?= $results['25-29MFOURMONTH']; ?></td>
						<td class=""><?= $results['25-29FFOURMONTH']; ?></td>
						<td class=""><?= $results['30-34MFOURMONTH']; ?></td>
						<td class=""><?= $results['30-34FFOURMONTH']; ?></td>
						<td class=""><?= $results['35-39MFOURMONTH']; ?></td>
						<td class=""><?= $results['35-39FFOURMONTH']; ?></td>
						<td class=""><?= $results['40-44MFOURMONTH']; ?></td>
						<td class=""><?= $results['40-44FFOURMONTH']; ?></td>
						<td class=""><?= $results['45-49MFOURMONTH']; ?></td>
						<td class=""><?= $results['45-49FFOURMONTH']; ?></td>
						<td class=""><?= $results['50MFOURMONTH']; ?></td>
						<td class=""><?= $results['50FFOURMONTH']; ?></td>
						<td class=""><?= $results['SUBTOTAL4MONTH']; ?></td>
					</tr>
					<tr>
						<td class="">5 MONTHS</td>
						<td class=""><?= $results['1MFIVEMONTH']; ?></td>
						<td class=""><?= $results['1FFIVEMONTH']; ?></td>
						<td class=""><?= $results['1-4MFIVEMONTH']; ?></td>
						<td class=""><?= $results['1-4FFIVEMONTH']; ?></td>
						<td class=""><?= $results['5-9MFIVEMONTH']; ?></td>
						<td class=""><?= $results['5-9FFIVEMONTH']; ?></td>
						<td class=""><?= $results['10-14MFIVEMONTH']; ?></td>
						<td class=""><?= $results['10-14FFIVEMONTH']; ?></td>
						<td class=""><?= $results['15-19MFIVEMONTH']; ?></td>
						<td class=""><?= $results['15-19FFIVEMONTH']; ?></td>
						<td class=""><?= $results['20-24MFIVEMONTH']; ?></td>
						<td class=""><?= $results['20-24FFIVEMONTH']; ?></td>
						<td class=""><?= $results['25-29MFIVEMONTH']; ?></td>
						<td class=""><?= $results['25-29FFIVEMONTH']; ?></td>
						<td class=""><?= $results['30-34MFIVEMONTH']; ?></td>
						<td class=""><?= $results['30-34FFIVEMONTH']; ?></td>
						<td class=""><?= $results['35-39MFIVEMONTH']; ?></td>
						<td class=""><?= $results['35-39FFIVEMONTH']; ?></td>
						<td class=""><?= $results['40-44MFIVEMONTH']; ?></td>
						<td class=""><?= $results['40-44FFIVEMONTH']; ?></td>
						<td class=""><?= $results['45-49MFIVEMONTH']; ?></td>
						<td class=""><?= $results['45-49FFIVEMONTH']; ?></td>
						<td class=""><?= $results['50MFIVEMONTH']; ?></td>
						<td class=""><?= $results['50FFIVEMONTH']; ?></td>
						<td class=""><?= $results['SUBTOTAL5MONTH']; ?></td>
					</tr>
					<tr>
						<td class="">6 MONTHS</td>
						<td class=""><?= $results['1MSIXMONTH']; ?></td>
						<td class=""><?= $results['1FSIXMONTH']; ?></td>
						<td class=""><?= $results['1-4MSIXMONTH']; ?></td>
						<td class=""><?= $results['1-4FSIXMONTH']; ?></td>
						<td class=""><?= $results['5-9MSIXMONTH']; ?></td>
						<td class=""><?= $results['5-9FSIXMONTH']; ?></td>
						<td class=""><?= $results['10-14MSIXMONTH']; ?></td>
						<td class=""><?= $results['10-14FSIXMONTH']; ?></td>
						<td class=""><?= $results['15-19MSIXMONTH']; ?></td>
						<td class=""><?= $results['15-19FSIXMONTH']; ?></td>
						<td class=""><?= $results['20-24MSIXMONTH']; ?></td>
						<td class=""><?= $results['20-24FSIXMONTH']; ?></td>
						<td class=""><?= $results['25-29MSIXMONTH']; ?></td>
						<td class=""><?= $results['25-29FFIVEMONTH']; ?></td>
						<td class=""><?= $results['30-34MSIXMONTH']; ?></td>
						<td class=""><?= $results['30-34FSIXMONTH']; ?></td>
						<td class=""><?= $results['35-39MSIXMONTH']; ?></td>
						<td class=""><?= $results['35-39FSIXMONTH']; ?></td>
						<td class=""><?= $results['40-44MSIXMONTH']; ?></td>
						<td class=""><?= $results['40-44FSIXMONTH']; ?></td>
						<td class=""><?= $results['45-49MSIXMONTH']; ?></td>
						<td class=""><?= $results['45-49FSIXMONTH']; ?></td>
						<td class=""><?= $results['50MSIXMONTH']; ?></td>
						<td class=""><?= $results['50FSIXMONTH']; ?></td>
						<td class=""><?= $results['SUBTOTAL6MONTH']; ?></td>
					</tr>
					<tr>
						<td class="">Total</td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""></td>
						<td class=""><?= $results['TOTALMONTHS']; ?></td>
					</tr>
					<tr>
						<td class="">MMS</td>
						<td class=""><?= $results['MMSMLESS1YEAR']; ?></td>
						<td class=""><?= $results['MMSFLESS1YEAR']; ?></td>
						<td class=""><?= $results['MMS1-4M']; ?></td>
						<td class=""><?= $results['MMS1-4F']; ?></td>
						<td class=""><?= $results['MMS5-9M']; ?></td>
						<td class=""><?= $results['MMS5-9F']; ?></td>
						<td class=""><?= $results['MMS10-14M']; ?></td>
						<td class=""><?= $results['MMS10-14F']; ?></td>
						<td class=""><?= $results['MMS15-19M']; ?></td>
						<td class=""><?= $results['MMS15-19F']; ?></td>
						<td class=""><?= $results['MMS20-24M']; ?></td>
						<td class=""><?= $results['MMS20-24F']; ?></td>
						<td class=""><?= $results['MMS25-29M']; ?></td>
						<td class=""><?= $results['MMS25-29F']; ?></td>
						<td class=""><?= $results['MMS30-34M']; ?></td>
						<td class=""><?= $results['MMS30-34F']; ?></td>
						<td class=""><?= $results['MMS35-39M']; ?></td>
						<td class=""><?= $results['MMS35-39F']; ?></td>
						<td class=""><?= $results['MMS40-44M']; ?></td>
						<td class=""><?= $results['MMS40-44F']; ?></td>
						<td class=""><?= $results['MMS45-49M']; ?></td>
						<td class=""><?= $results['MMS45-49F']; ?></td>
						<td class=""><?= $results['MMSOVER50M']; ?></td>
						<td class=""><?= $results['MMSOVER50F']; ?></td>
						<td class=""><?= $results['MMSTOTAL']; ?></td>
					</tr>
				</tbody>
			</table>
			<br>
			<br>
			<br>
		</div>
	</div>
</div>

<script src="<?= base_url('/assets/scripts/datatable/dt.1.10.21.jquery.dataTables.min.js'); ?>"></script>
<script src="<?= base_url('/assets/scripts/datatable/dataTables.buttons.min.js'); ?>"></script>
<script src="<?= base_url('/assets/scripts/datatable/jszip.min.js'); ?>"></script>
<script src="<?= base_url('/assets/scripts/datatable/pdfmake.min.js'); ?>"></script>
<script src="<?= base_url('/assets/scripts/datatable/vfs_fonts.js'); ?>"></script>
<script src="<?= base_url('/assets/scripts/datatable/buttons.html5.min.js'); ?>"></script>
<script>
	$(document).ready(function() {
		$('#mmd_mms_report').DataTable({
			dom: 'Bfrtip',
			buttons: [
				'copyHtml5',
				'excelHtml5',
				'csvHtml5',
				'pdfHtml5'
			],
			pagingType: "full_numbers"
		});
	});
</script>