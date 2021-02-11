<style type="text/css">
    .dataTable {
        letter-spacing:0px;
    }
    .table-bordered input {
        width:9em;
    }
    table {
        table-layout: fixed;
        width: 100px;
    }

    td {
        white-space: nowrap;
        overflow: hidden;         /* <- this does seem to be required */
        text-overflow: ellipsis;
    }
</style>
<script type="text/javascript">
    $(document).ready(function () {
        base_url = "<?php echo base_url() . '/'; ?>";
        var link = base_url + "notification_management/error_generator";
        var error_list = "<?php echo $first_error; ?>";
        var start_reg_error = error_list.indexOf("Patients without Start Regimen");
        var pat_reg_error = error_list.indexOf("Patients with the wrong Regimen");
        var lost_to_followup = error_list.indexOf("Patients without Current Regimen");
        var start_regimen_date_error = error_list.indexOf("Patients without Start Regimen");
        if (start_reg_error != -1 || lost_to_followup != -1 || start_regimen_date_error != -1) {
            $('#error_fix_btn').show();
        } else {
            $('#error_fix_btn').hide();
        }
        /*Auto Load First Error*/
        $.ajax({
            url: link,
            type: 'POST',
            data: {"array_text": error_list},
            success: function (data) {
                $("#error_display").empty();
                $("#error_display").append(data);
                $('.dataTables').dataTable({
                    // "bJQueryUI": true,
                    // "sPaginationType": "full_numbers",
                    // "sDom": '<"H"Tfr>t<"F"ip>',
                    // "oTableTools": {
                    //     "sSwfPath": base_url + "assets/scripts/datatable/copy_csv_xls_pdf.swf",
                    //     "aButtons": ["copy", "print", "xls", "pdf"]
                    // },
                    // "bProcessing": true,
                    // "bServerSide": false,
                    dom: 'Bfrtip',
                    buttons: [
                        'copyHtml5',
                        'excelHtml5',
                        'csvHtml5',
                        'pdfHtml5'
                    ]
                });
            }
        });

        $("#error_list").trigger("change");

        /*Onchange of Error List */
        $("#error_list").change(function () {
            var error_list = $(this).val();
            var start_reg_error = error_list.indexOf("Patients without Start Regimen");
            var lost_to_followup = error_list.indexOf("Patients without Current Regimen");
            var start_regimen_date_error = error_list.indexOf("Patients without Start Regimen");
            if (start_reg_error != -1 || lost_to_followup != -1 || start_regimen_date_error != -1) {
                $('#error_fix_btn').show();


            } else {
                $('#error_fix_btn').hide();
            }
            $.ajax({
                url: link,
                type: 'POST',
                data: {"array_text": error_list},
                success: function (data) {
                    $("#error_display").empty();
                    $("#error_display").append(data);
                    $('.dataTables').dataTable({
                        // "bJQueryUI": true,
                        // "sPaginationType": "full_numbers",
                        // "sDom": '<"H"Tfr>t<"F"ip>',
                        // "oTableTools": {
                        //     "sSwfPath": base_url + "assets/scripts/datatable/copy_csv_xls_pdf.swf",
                        //     "aButtons": ["copy", "print", "xls", "pdf"]
                        // },
                        // "bProcessing": true,
                        // "bServerSide": false,
                        dom: 'Bfrtip',
                        buttons: [
                            'copyHtml5',
                            'excelHtml5',
                            'csvHtml5',
                            'pdfHtml5'
                        ]
                    });
                }
            });



        });

        //startregimen
        $('#error_fix_btn').click(function () {
            $('#loadingDiv').show();
            var link = base_url + "notification_management/startRegimen_Error";
            $.ajax({
                url: link,
                success: function () {
                    $("#loadingDiv").hide();
                    location.reload();
                }
            });
        });
        //lost to followup
        $('#error_fix_btn').click(function () {
            $('#loadingDiv').show();
            var link = base_url + "notification_management/lost_to_followup";
            $.ajax({
                url: link,
                success: function () {
                    $("#loadingDiv").hide();
                    location.reload();
                }
            });
        });
        //patient without start regimen date
        $('#error_fix_btn').click(function () {
            $('#loadingDiv').show();
            var link = base_url + "notification_management/start_regimen_date_error";
            $.ajax({
                url: link,
                success: function () {
                    $("#loadingDiv").hide();
                    location.reload();
                }
            });
        });
    });

</script>
<div class="main-content">

    <div class="center-content">
        <div>
            <?php
            if (session()->get("msg_success")) {
                ?>
                <span class="message success"><?php echo session()->get("msg_success") ?></span>
                <?php
                session()->remove("msg_success");
            } elseif (session()->get("msg_error")) {
                ?>
                <span class="message error"><?php echo session()->get("msg_error") ?></span>
                <?php
                session()->remove("msg_error");
            }
            ?>
        </div>

        <div>
            <?php if (session()->get("msg_save_transaction")) {
                ?>

                <script type="text/javascript">
                    setTimeout(function () {
                        $(".info").fadeOut("2000");
                    }, 6000)
                </script>
                <?php
                if (session()->get("msg_save_transaction") == "success") {
                    ?>
                    <div class="message success">Your data were successfully saved !</div>
                    <?php
                } else {
                    ?>
                    <div class="message error">Your data were not saved ! Try again or contact your system administrator.</div>
                    <?php
                }
                session()->remove('msg_save_transaction');
            }
            ?>
        </div>
        <div>
            <ul class="breadcrumb">
<!--                <li><a href="<?php echo base_url() . '/notification_management/error_fix' ?>">Errors</a> </li>-->
                <li>
                    <select style="width:auto;color:#000;font-weight:bold" id="error_list">
                        <?php
                        foreach ($errors as $error => $error_array) {
                            echo "<option value='" . $error . "'>" . $error . "</option>";
                        }
                        ?>
                    </select>
                </li>
                <input type="button" id="error_fix_btn" value="Fix Error"/>

                <div id="loadingDiv" style="display:none; float: right"><img style="width: 20px" src="<?php echo base_url() . '/images/loading_spin.gif' ?>"></div>
            </ul>

        </div>
        <div id='error_display'>

        </div>	
    </div>
</div>