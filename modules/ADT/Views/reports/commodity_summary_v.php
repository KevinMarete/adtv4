<script type="text/javascript">
	$(document).ready(function() {
		var _url = <?php echo "'" . $base_url . "/report_management/getMoreHelp/" . $stock_type . "/" . $start_date . "/" . $end_date . "'"; ?>;
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
			"lengthMenu": [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ]


		});

	});
</script>
<div id="wrapperd">
	<div id="commodity_summary" class="full-content">
		<?php echo view("\Modules\ADT\Views\\reports\\reports_top_menus_v"); ?>
		<h4 style="text-align: center" id='report_title'>Monthly Report on Drug Stock for the Period From <span class="_date" id="start_date"><?php echo $start_date ?></span> To <span class="_date" id="end_date"><?php echo $end_date ?></span> - <?php echo $stock_type_n ?></h4>
		<hr size="1" style="width:80%">

		<table id="drug_table" class="dataTables" style="font-size:0.8em" border="1" width="100%">
			<thead>
				<tr>
					<th style="min-width: 300px">Drug Name</th>
					<th>Beginning Balance </th>
					<?php
					//Looping through every transaction name
					foreach ($trans_names as $trans) {
						?>
						<th><?php echo $trans['name'] ?></th>
					<?php
					}
					?>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>