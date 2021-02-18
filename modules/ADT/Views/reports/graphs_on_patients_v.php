<script src="<?= base_url('/assets/scripts/highcharts/highcharts.js') ?>"></script>
<style type="text/css">
	.graph {
		height: auto !important;
	}
</style>
<div class="full-content container">
	<div class="row-fluid">
		<?php echo view("\Modules\ADT\Views\\reports\\reports_top_menus_v"); ?>
	</div>
	<div class="row-fluid">
		<div id="chartdiv" class="span12">
			<?php echo $graphs; ?>
		</div>
	</div>
</div>