<div class="external-content" style="zoom:1">
	<div class="browser">
		<div id="dvContents" class="dvContents">
			<div class="content">
				<script>
					$(function() {
						$("#wizard").steps({
							headerTag : "h2",
							bodyTag : "section",
							transitionEffect : "slideLeft",
							stepsOrientation : "vertical",
							onStepChanging : function(event, currentIndex, newIndex) {
								if(currentIndex == 0) {
									var server_status = $("#log1_status").val();
									if(server_status == 1) {
										return true;
									} else {
										return false;
									}
								} else if(currentIndex == 1) {
									var server_status = $("#log2_status").val();
									if(server_status == 1) {
										return true;
									} else {
										return false;
									}
								}
							},
							onFinishing:function(event, currentIndex)  {
								var server_status = $("#log3_status").val();
								if(server_status == 1) {
									alert("Recovery Complete");
									return true;
								} else {
									alert("Recovery not complete");
									return false;
								}
							}
						});
					});

				</script>
				<div id="wizard">
					<h2>Server Configuration</h2>
					<section>
						<form class="form-horizontal" id="checkServerFrm">
							<div class="control-group">
								<label class="control-label" for="inputHost">Database Hostname</label>
								<div class="controls">
									<input type="hidden" id="log1_status" name="log1_status" value="0" />
									<input type="text" id="inputHost" name="inputHost" placeholder="localhost" value="<?php echo $sys_hostname; ?>" />
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="inputPort">Database Port</label>
								<div class="controls">
									<input type="hidden" id="log12_status" name="log12_status" value="0" />
									<input type="text" id="inputPort" name="inputPort" placeholder="localhost" value="<?php echo $sys_hostport; ?>" />
								</div>
							</div>
														<div class="control-group">
								<label class="control-label" for="inputUser">Database User</label>
								<div class="controls">
									<input type="text" id="inputUser" name="inputUser" placeholder="root" value="<?php echo $sys_username; ?>" required/>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="inputPassword">Database Password</label>
								<div class="controls">
									<input type="password" id="inputPassword" name="inputPassword" value="<?php echo $sys_password;?>"  placeholder=".....">
								</div>
							</div>
							<div class="control-group form-actions">
								<label class="control-label"></label>
								<div class="controls">
									<button class="btn btn-primary">
										Test Connection
									</button>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="inputLog1">Database Log</label>
								<div class="controls">
									<textarea rows="7" style="width:100%" id="inputLog1" readonly></textarea>
								</div>
							</div>
						</form>
					</section>
					<h2>Database Configuration</h2>
					<section>
						<form class="form-horizontal" id="checkDatabaseFrm">
							<div class="control-group">
								<label class="control-label" for="inputDb">Database Name</label>
								<div class="controls">
									<input type="hidden" id="log2_status" name="log2_status" value="0" />
									<input type="text" id="inputDb" name="inputDb" placeholder="testdb" required/>
								</div>
							</div>
							<div class="control-group form-actions">
								<label class="control-label"></label>
								<div class="controls">
									<button type="submit" class="btn btn-primary">
										Check Database
									</button>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="inputLog2">Database Log</label>
								<div class="controls">
									<textarea rows="7" style="width:100%" id="inputLog2" readonly></textarea>
								</div>
							</div>
						</form>
					</section>
					<h2>Recovery Setup</h2>
					<section>
						<form class="form-horizontal" id="checkRecoveryFrm">
							<div class="control-group">
								<label class="control-label" for="inputUpload">Recovery Upload</label>
								<div class="controls">
									<input type="hidden" id="log3_status" name="log3_status" value="0" />
									<span class="btn btn-success fileinput-button">
								        <i class="glyphicon glyphicon-plus"></i>
								        <span>Select files...</span>
								        <!-- The file input field used as target for the file upload widget -->
								        <input id="fileupload" type="file" name="files[]" multiple>
								    </span>
								    <br>
								    <br>
								    <!-- The global progress bar -->
								    <div id="progress" class="progress">
								        <div class="progress-bar progress-bar-success"></div>
								    </div>
								</div>
							</div>

							<div id="progress-panel" style="display: none;">
								<span id="progress-text">Performing Recovery. May take some time.</span>
								<br />  
								<img src="<?= base_url() ?>public/assets/img/loader.gif" >
							</div>

							<div class="control-group form-actions">
								<label class="control-label"></label>
								<div class="controls" id="backup_files">
									<?php echo $backup_files;?>
								</div>
							</div>
						</form>
					</section>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	<?php $timestamp = time();?>
	$(function() {
		//Submit Server Configuarion Form
		$('#checkServerFrm').on('submit', function(e) {
			$.ajax({
				type : 'post',
				url : 'recover/check_server',
				data : $('form').serialize(),
				success : function(data) {
					if(data == 0) {
						var mystatus = "Connection Failed!";
					} else {
						var mystatus = "Connection Success!";
					}
					$("#log1_status").val(data);
					$("#inputLog1").text(mystatus);
				}
			});
			e.preventDefault();
		});
        //Submit Database Configuarion Form
        $('#checkDatabaseFrm').on('submit', function(e) {
        	$.ajax({
        		type : 'post',
        		url : 'recover/check_database',
        		data : $('form').serialize(),
        		success : function(data) {
        			if(data == 0) {
        				var data = "Database does not exist! \nError creating database!";
        				$("#log2_status").val(0);
        			} else {
        				$("#log2_status").val(1);
        			}
        			$("#inputLog2").val($.trim(data));
        		}
        	});
        	e.preventDefault();
        });
	   	//Start Recovery Process
	    $('.recover').live('click', function(e) {
	    	$('#progress-panel').show();
	    	$('.recover').addClass('disabled');

	    	var current_row = $(this).closest('tr').children('td');
	    	var file_name = current_row.eq(1).text();
	    	var link='recover/start_recovery/';
	    	$.ajax({
	    		url : link,
	    		type : 'POST',
	    		data : {
	    			"file_name" : file_name
	    		},
	    		success : function(data) {
	    		
	    			if(data==1){
	    				alert("Recovery Successful!");
	    				$("#log3_status").val(1);
	    				$('.recover').removeClass('disabled');
	    				$('#progress-panel').hide();

	    			}else{
	    				alert("Recovery Failed!");
	    				$("#log3_status").val(0);
	    				$('.recover').removeClass('disabled');
	    				$('#progress-panel').hide();
	    			}
	    		}
	    	});
	    	e.preventDefault();
	    });
	    //Upload Recovery Files
	    $('#fileupload').fileupload({
	        url: 'recover/start_database',
	        done: function (e, data) {
	            $.each(data.files, function (index, file) {
	                alert('The file ' + file.name + ' was successfully');
	            });

	            $("#backup_files").load("recover/showdir",function(){
					$('.dataTables').dataTable({
						"bJQueryUI" : false,
						"sPaginationType" : "full_numbers",
						"bProcessing" : true,
						"bServerSide" : false,
					});
					//Reset progressbar
					$('#progress .progress-bar').css('width', '0%');
				});
	        },
	        progressall: function (e, data) {
	            var progress = parseInt(data.loaded / data.total * 100, 10);
	            $('#progress .progress-bar').css(
	                'width',
	                progress + '%'
	            );
	        }
	    }).prop('disabled', !$.support.fileInput)
	        .parent().addClass($.support.fileInput ? undefined : 'disabled');
	});
</script>