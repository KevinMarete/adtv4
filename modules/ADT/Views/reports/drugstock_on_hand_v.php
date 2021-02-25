<script type="text/javascript">
	$(document).ready(function() {
		//"sDom": 'T<"clear">lfrtip',
		var base_url = '<?php echo $base_url ?>';
		var stock_type = <?php echo $stock_type; ?>;
		var _url = <?php echo "'" . $base_url . "/report_management/drug_stock_on_hand/" . $stock_type . "'"; ?>;
		var report_title = $("#report_title").text();
		var facility = $("#facility_name").text();
		$('#drug_table').dataTable({
			// "iDisplayLength": 10,
			dom: 'lBfrtip',
			ajax: _url,
			processing: true,
			serverSide: true,
			buttons: [
				'copyHtml5',
				{
					extend: 'excelHtml5',
					title: report_title+" ("+facility+")"
				},
				'csvHtml5',
				{
					extend: 'pdf',
					title: report_title+" ("+facility+")",
					pageSize: 'A3',
					orientation: 'landscape'
				}
			],
			pagingType: "full_numbers",
			"columnDefs": [{
				'sortable': false,
				'targets': [2, 3, 4, 5, 6]
			}],
			"lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ]
		});

	});
</script>
<div id="wrapperd">
	<div id="drugstock_on_hand" class="full-content">
		<?php echo view("\Modules\ADT\Views\\reports\\reports_top_menus_v"); ?>
		<h4 style="text-align: center" id='report_title'>Report on Inventory Status as of <span id="date_of_appointment"><?php echo date('d-M-Y') ?></span> - <?php if ($stock_type == 1) {
																																																																														echo "Main Store";
																																																																													} elseif ($stock_type == 2) {
																																																																														echo "Pharmacy";
																																																																													}; ?></h4>
		<hr size="1" style="width:80%">

		<table id="drug_table" class="table table-bordered table-striped listing_table" style="font-size:0.8em">
			<thead>
				<tr>
					<th style="min-width: 300px">Drug</th>
					<th>Unit</th>
					<th>Pack Size</th>
					<th>SOH (Units)</th>
					<th>SOH (Packs)</th>
					<th>Safety Stock</th>
					<th>Stock Status</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>