<!DOCTYPE html">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
	<!--Load Header-->
	<?php $this -> load -> view('header_v');?>
</head>
<body>
	<!--Load Menu-->
	<?php	$this -> load -> view('template/external_header_v'); ?>
	<!--Main Content-->
	<div class="container">
		<!--Load Content-->
		<?php $this -> load -> view($content_view);?>
	</div>
	<!--Load footer-->
	<?php $this -> load -> view('footer_v');?>
</body>
</html>