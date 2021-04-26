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
		$('.il_errors_table').dataTable({
			dom: 'lrtip',
			pagingType: "full_numbers",
            order: [[ 0, "desc" ]]
		});
	});
</script>

<div style="padding:10px;">
    <table class="table table-bordered table-hover table-condensed table-striped il_errors_table">
        <thead>
            <tr>
                <th>Date</th>
                <th style="width:65%;">Error</th>
                <th>Sending System</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($il_errors as $error): ?>
            <tr>
                <td><?= $error->created_at ?></td>
                <td><?= $error->error ?></td>
                <td><?= $error->sending_system ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- <?php  echo $il_errors; ?> -->
