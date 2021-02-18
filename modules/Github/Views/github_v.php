<div class="external-content">
	<div class="browser">
		<div id="dvContents" class="dvContents">

		<?php foreach ($git_releases as $key => $release) {?>
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="heading<?= $key; ?>">
						<h4 class="panel-title">
							<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?= $key; ?>" aria-expanded="true" aria-controls="collapse<?= $key; ?>">
								ADT Release <?= $release->name; ?> 
							</a>
						</h4>
					</div>
					<div id="collapse<?= $key; ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading<?= $key; ?>">
						<div class="panel-body">
								<p class="text-left">
								<?= str_replace("#", "<br />#", $release->body); ?>
								</p>
								<a href="<?= $release->zipball_url; ?>" download="Web-ADT" class="btn btn-xs btn-primary">Download</a>
						</div>
					</div>
				</div>

			</div>
			<?php }?>
		</div>
	</div>
</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('.panel-title a').first().append('<span class="badge">latest</span>');
	});

</script>