<script type="text/javascript">
	$(document).ready(function() {

		var stock_type = <?php echo $stock_type; ?>; //Year
		var base_url = '<?php echo $base_url ?>';
		var _url = <?php echo "'" . $base_url . "/report_management/drug_consumption/" . $stock_type . "/" . $pack_unit . "'"; ?>;
		var report_title = $("#report_title").text();
		var facility = $("#facility_name").text();
		$('#drug_table').dataTable({
			// "iDisplayLength": 10,
			dom: 'lBfrtip',
			ajax: _url,
			processing: true,
			serverSide: true,
			destroy: true,
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
				'orderable': false,
				'targets': [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13]
			}],
			"lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
			"scrollCollapse": true,
			pageLength: 10
		});

	});
</script>
<div id="wrapperd">
	<div id="drug_consumption" class="full-content">
		<?php echo view("\Modules\ADT\Views\\reports\\reports_top_menus_v"); ?>
		<h4 style="text-align: center;" id="report_title">Listing of Drug Consumption Report for <span class="_date" id="_year"><?php echo @$year . '  (' . strtoupper($pack_unit) . ')' ?></span> </h4>
		<hr size="1" style="width:80%">

		<table id="drug_table" class="table table-bordered table-striped dataTables " style="font-size:0.8em">
			<thead>
				<tr>
					<th style="width:30% !important">Drug</th>
					<th>Unit</th>
					<th>Jan</th>
					<th>Feb</th>
					<th>Mar</th>
					<th>Apr</th>
					<th>May</th>
					<th>Jun</th>
					<th>Jul</th>
					<th>Aug</th>
					<th>Sep</th>
					<th>Oct</th>
					<th>Nov</th>
					<th>Dec</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
	</div>
</div>