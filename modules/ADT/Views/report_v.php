<?php
if (!isset($link)) {
	$link = null;
}
$access_level = session()->get('user_indicator');
$user_is_administrator = false;
$user_is_nascop = false;
$user_is_pharmacist = false;

if ($access_level == "system_administrator") {
	$user_is_administrator = true;
}
if ($access_level == "pharmacist") {
	$user_is_pharmacist = true;
}
if ($access_level == "nascop_staff") {
	$user_is_nascop = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>My Reports</title>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#default").load('<?php echo base_url('/report_management/cumulative_patients/' . date('d-M-Y') . '/2'); ?>', function() {
				$('.dataTables').dataTable({
					// "bJQueryUI": true,
					// "sPaginationType": "full_numbers",
					// "sDom": '<"H"frT>t<"F"ip>',
					// "oTableTools": {
					// 	"sSwfPath": base_url + "scripts/datatable/copy_csv_xls_pdf.swf",
					// 	"aButtons": ["copy", "print", "xls", "pdf"]
					// },
					// "bProcessing": true,
					// "bServerSide": false,
					// "bAutoWidth": false,
					// "bDeferRender": true,
					// "bInfo": true,
					// "bDestroy": true,
					// "fnInitComplete": function() {
					// 	this.css("visibility", "visible");
					// }
					dom: 'Bfrtip',
					buttons: [
						'copyHtml5',
						'excelHtml5',
						'csvHtml5',
						'pdfHtml5'
					],
					pagingType: "full_numbers"
				});
				$(".dataTables").wrap('<div class="dataTables_scroll" />');
			});

			$(window).resize(function() {
				$(".hasDatepicker").datepicker("hide");
			});
		});
	</script>

	<style type="text/css">
		.full-content {
			padding: 0;
		}

		#ui-datepicker-div {
			zoom: 1;
		}

		td {
			white-space: nowrap;
			overflow: hidden;
			/* <- this does seem to be required */
			text-overflow: ellipsis;
		}
	</style>
</head>

<body>
	<div id="wrapperd">

		<div class="center-content">
			<ul class="nav nav-tabs nav-pills">
				<li id="standard_report" class="active reports_tabs"><a href="#">Standard Reports</a> </li>
				<li id="visiting_patient" class="reports_tabs"><a href="#">Visiting Patients</a></li>
				<li id="differentiated_care" class="reports_tabs"><a href="#">Differentiated Care</a></li>
				<li id="early_warning_indicators" class="reports_tabs"><a href="#">Early Warning Indicators</a> </li>
				<li id="drug_inventory" class="reports_tabs"><a href="#">Drug Inventory</a></li>
				<li id="moh_forms" class="reports_tabs"><a href="#">MOH Forms</a></li>
				<li id="guidelines" class="reports_tabs"><a href="#">Guidelines</a></li>
			</ul>

			<div id="report_container">
				<?php echo view('\Modules\ADT\Views\\reports\\report_home_types_v');
				?>
			</div>
			<div id="default">

			</div>
		</div>
	</div>
</body>

</html>