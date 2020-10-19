<style type="text/css">

.cmn-toggle {position: absolute;margin-left: -9999px;visibility: hidden;}
.cmn-toggle + label {display: block;position: relative;cursor: pointer;outline: none;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;}
input.cmn-toggle-round-flat + label {padding: 2px;width: 75px;height: 30px;background-color: #dddddd;-webkit-border-radius: 60px;-moz-border-radius: 60px;-ms-border-radius: 60px;-o-border-radius: 60px;border-radius: 60px;-webkit-transition: background 0.4s;-moz-transition: background 0.4s;-o-transition: background 0.4s;transition: background 0.4s;}
input.cmn-toggle-round-flat + label:before, input.cmn-toggle-round-flat + label:after {display: block;position: absolute;content: "";}
input.cmn-toggle-round-flat + label:before {top: 2px;left: 2px;bottom: 2px;right: 2px;background-color: #fff;-webkit-border-radius: 60px;-moz-border-radius: 60px;-ms-border-radius: 60px;-o-border-radius: 60px;border-radius: 60px;-webkit-transition: background 0.4s;-moz-transition: background 0.4s;-o-transition: background 0.4s;transition: background 0.4s;}
input.cmn-toggle-round-flat + label:after {top: 4px;left: 4px;bottom: 4px;width: 22px;background-color: #dddddd;-webkit-border-radius: 52px;-moz-border-radius: 52px;-ms-border-radius: 52px;-o-border-radius: 52px;border-radius: 52px;-webkit-transition: margin 0.4s, background 0.4s;-moz-transition: margin 0.4s, background 0.4s;-o-transition: margin 0.4s, background 0.4s;transition: margin 0.4s, background 0.4s;}
input.cmn-toggle-round-flat:checked + label {background-color: #27A1CA;}
input.cmn-toggle-round-flat:checked + label:after {margin-left: 45px;background-color: #27A1CA;}

</style>
<div class="external-content">
	<div class="container">
		<div class="row">
			<form class="form-horizontal" method="POST">
				<fieldset>
					<!-- Form Name -->
					<legend>API Settings</legend>

					<!-- Text input-->
					<?php foreach ($api_config as $key => $conf) {
						$type = ($conf['type'] == 'toggle') ? 'checkbox' : '' ;
						$type = ($conf['type'] == 'char') ? 'text' : $type ;
						$checked = ($conf['value']=='on') ? 'checked' : '' ;
						?>
						<div class="form-group">
							<label class="col-md-4 control-label" for="<?= $conf['config']?>"><?= ucwords(str_replace('_', ' ', $conf['config'])); ?></label>  
							<div class="col-md-4">
								<?php if($type =='checkbox'){?>
								<div class="switch">
									<input id="<?= $conf['config']?>" class="cmn-toggle cmn-toggle-round-flat" name="<?= $conf['config']?>"  type="checkbox" <?= $checked; ?>>
									<label for="<?= $conf['config']?>"></label>
								</div>
								<?php }else{?>
								<input id="<?= $conf['config']?>" name="<?= $conf['config']?>" class="form-control input-md" type="<?= $type; ?>" value="<?= $conf['value'] ?>" >
								<?php }?>
							</div>
						</div>
						<?php }?>
						<!-- Button -->
						<div class="form-group">
							<!-- <label class="col-md-4 control-label" for="singlebutton">Single Button</label> -->
							<div class="col-md-4">&nbsp;</div>
							<div class="col-md-4 offset-4">
								<button id="submit" name="" class="btn btn-primary">Save Settings</button>
							</div>
						</div>

					</fieldset>
				</form>
			</div>
		</div>
		<div class="browser">
			<div id="dvContents" class="dvContents">

				<?php echo $guidelines_list;?>

			</div>
		</div>
	</div>
	<script type="text/javascript">
		function toggleValidate(){
			if($("input[name=api_status]").attr('checked') == 'checked')
			{
				$("input[name=api_patients_module]").attr('disabled',false);
				$("input[name=api_dispense_module]").attr('disabled',false);
				$("input[name=api_appointments_module]").attr('disabled',false);
				$("input[name=api_logging]").attr('disabled',false);
			}else{
				$("input[name=api_patients_module]").attr('disabled',true);
				$("input[name=api_dispense_module]").attr('disabled',true);
				$("input[name=api_appointments_module]").attr('disabled',true);
				$("input[name=api_logging]").attr('disabled',true);
			}

		}
		$(function(){
			toggleValidate();
			
			$("input[name=api_status]").change(function(e){
				toggleValidate();
			});



		});
	</script>