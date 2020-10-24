<div class="external-content">
    <div class="container">
        <div class="row">

            <div class="col col-md-2">
                <form class="form-horizontal" id="backup_frm">
                    <div class="form-group">
                    </div>
                    <div class="form-group">
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary" id="backup_btn">Run Backup</button>
                        </div>
                    </div>
                </form>
                <div id="progress-panel" style="display: none;">
                    <span id="progress-text"></span>
                    <img src="<?= base_url() ?>/public/assets/img/loader.gif" >
                </div>
                <hr />
            </div>
            <div class="col col-md-10">
                <div class="alert alert-info" role="alert" style="display: none;"> 
                </div>
            </div>

        </div>
    </div>
    <div class="browser">
        <div id="dvContents" class="dvContents">
            <?php
            echo $backup_files;
            ?>
        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {
<?= $ftp_status; ?>
        $("td:contains('testadt_blank.sql.zip')").next().html('');

        $('#dyn_table').DataTable({
            "bJQueryUI": false,
            "sPaginationType": "full_numbers",
            "aaSorting": [[0, 'desc']]
        });

        $("#backup_frm").on('submit', function (e) {
            //disable button when submitted
            $("#backup_btn, .btn").attr("disabled", true);
            // do ajax request to backup


            var upload = $('input[name=upload]:checked').val();

            var data = {
                upload: upload,
                stage: "make_order"
            };


            $('#progress-panel').show();

            $.ajax({
                type: "POST",
                url: "run_backup",
                data: data,
                success: (function (resp) {
                    $("#backup_btn, .btn").attr("disabled", false);
                    $('#progress-text').text(resp);
                    // $('.modal form').hide();
                    $('#progress-panel').hide();
                    $('.alert').show();
                    $('.alert').text("" + resp);
                    window.location.href = "";

                }),
                error: (function (resp) {
                    // $("#backup_btn").attr("disabled",false);
                    // $('.modal form').hide();
                    console.log(resp)
                    //alert("Error: Failed to perform backup.");
                     window.location.href = "";
                })


                        // dataType: dataType
            });

            e.preventDefault();
        });





        $(".upload").click(function () {
            $('#progress-panel').show();
            $('#progress-text').text("Uploading backup.");
            $(".upload, .btn").addClass("disabled");
            var current_row = $(this).closest('tr').children('td');
            var file_name = current_row.eq(0).text();

            var file = {
                "file_name": file_name
            }

            var link = "<?php echo base_url();?>/public/upload_backup"
            $.ajax({
                url: link,
                type: 'POST',
                // dataType : 'json',
                data: file,
                success: function (response) {
                    $('#progress-panel').hide();

                    $(".upload, .btn").removeClass("disabled");
                    $('.alert').show();
                    console.log(response);
                    $('.alert').text(response);
                    // alert(response);
                    $(this).closest('.upload')
                       window.location.href = "";
                },
                error: function (response) {
                    $(".upload, .btn").removeClass("disabled");
                    $('#progress-panel').hide();
                    $('.alert').show();
                    console.log(response);
                    $('.alert').text(response);
                     window.location.href = "";
                }
            });
        });

        $(".download").click(function () {
            $('#progress-panel').show();
            $('#progress-text').text("Downloading copy of backup from remote server.");
            $(".download, .btn").addClass("disabled");
            var current_row = $(this).closest('tr').children('td');
            var remote_path = current_row.eq(0).text();

            var file = {
                "remote_path": remote_path
            }

            var link = "<?php echo base_url() . '/public/download_remote_file' ?>"
            $.ajax({
                url: link,
                type: 'POST',
                // dataType : 'json',
                data: file,
                success: function (response) {
                    $('#progress-panel').hide();

                    $(".download, .btn").removeClass("disabled");
                    $('.alert').show();
                    console.log(response);
                    $('.alert').text(response);
                    window.location.href = "";
                    // alert(response);
                },
                error: function (response) {
                    $(".download, .btn").removeClass("disabled");
                    $('#progress-panel').hide();
                    $('.alert').show();
                    console.log(response);
                    $('.alert').text(response);
                    window.location.href = "";
                    // alert(response);
                }
            });
        });




        $(".delete").click(function (e) {
          
            $('#progress-panel').show();
            $('#progress-text').text("Deleting Backup");
            $(".upload, .btn").addClass("disabled");
            var current_row = $(this).closest('tr').children('td');
            var file_name = current_row.eq(0).text();
            var file = {'file_name': file_name};
            var link = "<?php echo base_url() . '/public/delete_backup' ?>"
             
            $.ajax({
                url: link,
                type: 'POST',
                data: file,
                success: function (response) {
                    
                    $('#progress-panel').hide();

                    $(".upload, .btn").removeClass("disabled");
                    $('.alert').show();
                    console.log(response);
                    $('.alert').text(response);
                    window.location.href = "";
                },
                error: function (response) {
                    $(".upload, .btn").removeClass("disabled");
                    $('#progress-panel').hide();
                    $('.alert').show();
                    console.log(response);
                    $('.alert').text(response);
                    window.location.href = "";
                }
            });
        });


    });

</script>