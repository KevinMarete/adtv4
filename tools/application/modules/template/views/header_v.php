<meta name="description" content="national dashboard for nascop kenya">
<meta name="keywords" content="dashboard,hiv,kenya,nascop,commodities">
<meta name="author" content="criat">
<meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title><?php echo $title; ?></title>

<!--Load style files-->
<link href="<?php echo base_url().'public/assets/styles/bootstrap/bootstrap.min.css'?>"  type="text/css" rel="stylesheet" media="all">
<link href="<?php echo base_url().'public/assets/styles/font-awesome/css/font-awesome.css'?>"  type="text/css" rel="stylesheet" media="all">
<link href="<?php echo base_url().'public/assets/styles/datatable/jquery.dataTables.css'?>"  type="text/css" rel="stylesheet" media="all">
<link href="<?php echo base_url().'public/assets/styles/jquery-ui.min.css'?>"  type="text/css" rel="stylesheet" media="all">
<!-- <link href="<?php //echo base_url().'public/assets/styles/nascop.css'?>"  type="text/css" rel="stylesheet" media="all"> -->
<link href="<?php echo base_url().'public/assets/styles/jquery.steps.css'?>"  type="text/css" rel="stylesheet" media="all">
<link href="<?php echo base_url().'public/assets/styles/sticky-footer.css'?>"  type="text/css" rel="stylesheet" media="all">
<link href="<?php echo base_url().'public/assets/styles/sticky-footer-navbar.css'?>"  type="text/css" rel="stylesheet" media="all">
<link href="<?php echo base_url().'public/assets/styles/datatable/dataTables.bootstrap.css'?>"  type="text/css" rel="stylesheet" media="all">
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="<?php echo base_url().'public/assets/public/lib/fileuploader/css/jquery.fileupload.css'; ?>">
<link href="<?php echo base_url().'public/assets/styles/adt-tools.css'?>"  type="text/css" rel="stylesheet" media="all">
<?php if (isset($css)){foreach ($css as $script) {?>
<link href="<?= base_url().$script?>"  type="text/css" rel="stylesheet" media="all">
<?php }} ?>

<link href="<?php echo base_url().'public/assets/images/favicon.ico'?>" rel="shortcut icon">

<!--Load script files-->
<script src="<?php echo base_url().'public/assets/scripts/modernizr-2.6.2.min.js'?>" type="text/javascript"></script>
<script src="<?php echo base_url().'public/assets/scripts/jquery-1.10.2.min.js'?>" type="text/javascript"></script>
<script src="<?php echo base_url().'public/assets/scripts/jquery.cookie-1.3.1.js'?>" type="text/javascript"></script>
<script src="<?php echo base_url().'public/assets/scripts/jquery.steps.js'?>" type="text/javascript"></script>
<script src="<?php echo base_url().'public/assets/scripts/jquery-migrate-1.2.1.min.js'?>" type="text/javascript"></script>
<script src="<?php echo base_url().'public/assets/scripts/jquery-ui/jquery-ui.min.js'?>" type="text/javascript"></script>
<script src="<?php echo base_url().'public/assets/scripts/bootstrap/bootstrap.min.js'?>" type="text/javascript"></script>
<script src="<?php echo base_url().'public/assets/scripts/datatable/jquery.dataTables.min.js'?>" type="text/javascript"></script>
<script src="<?php echo base_url().'public/assets/scripts/nascop.js'?>" type="text/javascript"></script>
<!--<script src="<?php //echo base_url().'public/assets/scripts/datatable/dataTables.bootstrap.js'?>" type="text/javascript"></script>-->
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="<?php echo base_url().'public/assets/public/lib/fileuploader/js/vendor/jquery.ui.widget.js';?>"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="<?php echo base_url().'public/assets/public/lib/fileuploader/js/jquery.iframe-transport.js';?>"></script>
<!-- The basic File Upload plugin -->
<script src="<?php echo base_url().'public/assets/public/lib/fileuploader/js/jquery.fileupload.js';?>"></script>
<?php if(isset($js)) {foreach ($js as $script) {?>
<script src="<?= base_url().$script;?>"></script>
<?php }} ?>