<?php 
$session = session();
$side_menus = $session->get('sidemenu_items');
$side_notifications = $session->get('notifications');
?>
<div class="sidebar-content">
	<h3>Quick Links</h3>
	<ul class="nav nav-list well">
	<?php
     if ($side_menus) {
       foreach ($side_menus as $side_menu) {
         $url = $side_menu['url'];
         $text = $side_menu['text'];
    ?>
	<li><a href="<?php echo base_url($url); ?>" ><?php echo $text; ?></a></li>
	<?php
		}
	 }
	?>
	</ul>
	<h3>Notifications</h3>
	<ul class="nav nav-list well">
	<?php
     if ($side_notifications) {
       foreach ($side_notifications as $notification) {
       	 $url = $notification['url'];
         $text = $notification['text'];
    ?>
	<li><a href="<?php echo base_url($url); ?>" ><?php echo $text; ?></a></li>
	<?php
		}
	 }
	?>
	</ul>
</div>
