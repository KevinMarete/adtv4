<div class="center-content">
	<div class="row-fluid">
		<div class="span8 offset2">
			<?php echo $this -> session -> flashdata('login_message');?>
			<?php echo form_open('order/authenticate_user');?>
			<?php echo form_fieldset('', array('id' => 'login_legend'));?>
			<legend id="login_legend">
				<i class="fa fa-info-circle"></i>DHIS Log In
			</legend>
			<?php echo $this -> session -> flashdata('error_message');?>
			<div class="item">
				<?php echo form_error('username', '<div class="error_message">', '</div>');?>
				<?php echo form_label('Username:', 'username');?>
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-user"></i></span>
					<?php echo form_input(array('name' => 'username', 'required' => 'required', 'id' => 'username', 'class' => 'textfield form-control', 'placeholder' => 'username'));?>
				</div>
			</div>
			<div class="item">
				<?php echo form_error('password', '<div class="error_message">', '</div>');?>
				<?php echo form_label('Password:', 'password');?>
				<div class="input-group">
					<span class="input-group-addon"><i class="fa fa-key"></i></span>
					<?php echo form_password(array('name' => 'password', 'required' => 'required', 'id' => 'password', 'class' => 'textfield form-control', 'placeholder' => '********'));?>
				</div>
			</div>
			<div style="margin-top:1em;">
				<?php echo form_fieldset_close();?>
				<?php echo form_fieldset('', array('class' => 'tblFooters'));?>
				<?php echo form_submit('input_go', 'Sign In');?> <?php echo form_fieldset_close();?>
				<?php echo form_close();?>
			</div>
		</div>
	</div>
</div>