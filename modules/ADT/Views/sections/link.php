<?php
//Styles
$styles = [
  'amcharts/style.css',
  'bootstrap.css',
  'bootstrap.min.css',
  'bootstrap-responsive.min.css',
  'datatable/jquery.dataTables.css',
  'datatable/jquery.dataTables_themeroller.css',
  'datatable/demo_table.css',
  'jquery-ui.css',
  'style.css',
  'assets/jquery.multiselect.css',
  'assets/jquery.multiselect.filter.css',
  'assets/prettify.css',
  'style_report.css',
  'validator.css',
  'jquery.gritter.css',
  'toastr.min.css',
  'select2-3.4.8/select2.css'
];

foreach ($styles as $style) {
  echo '<link type="text/css" rel="stylesheet" href="' . base_url('public/assets/styles/' . $style) . '" media="screen" />';
}

//Scripts
$scripts = [
  'jquery-1.11.1.min.js',
  'jquery-1.7.2.min.js',
  'jquery-migrate-1.4.1.min.js',
  'jquery.form.js',
  'jquery.gritter.js',
  'jquery-ui.js',
  'sorttable.js',
  'datatable/jquery.dataTables.min.js',
  'datatable/columnFilter.js',
  'bootstrap/bootstrap.min.js',
  'bootstrap/paging.js',
  'jquery.multiselect.js',
  'jquery.multiselect.filter.js',
  'validator.js',
  'validationEngine-en.js',
  'menus.js',
  'jquery.blockUI.js',
  'amcharts/amcharts.js',
  'toastr.js',
  'select2-3.4.8/select2.min.js',
  'bootbox.min.js',
  'Merged_JS.js',
  'autoprefixer.js'
];

foreach ($scripts as $script) {
  echo '<script type="text/javascript" src="' . base_url('public/assets/scripts/' . $script) . '" charset="UTF-8"></script>';
}
