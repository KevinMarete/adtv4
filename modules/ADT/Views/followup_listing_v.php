<style type="text/css">
.dataTable {
	letter-spacing:0px;
}
.table-bordered input {
	width:9em;
}
table {
	table-layout: fixed;
	width: 100px;
}

td {
	white-space: nowrap;
	overflow: hidden;         /* <- this does seem to be required */
	text-overflow: ellipsis;
}
</style>

<script type="text/javascript">
	
	// defaulter_table
	$(document).ready(function() {
		$('.defaulter_table').dataTable({
			dom: 'lBfrtip',
			buttons: [
				'copyHtml5',
				'excelHtml5',
				'csvHtml5',
				'pdfHtml5'
			],
			pagingType: "full_numbers",
			processing: true,
			serverSide: false
		});
	});
</script>
<?php  echo $followup_patients; ?>
