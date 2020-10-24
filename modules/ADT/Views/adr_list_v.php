<style type="text/css">
    /*@media (min-width: 992px)*/
    /*.modal-lg {*/
        /*width: 900px;*/
        /*}*/
        table{
            width:100%;
        }
        #pv_form{
            zoom:0.8 !important;
        }
    </style>
    <?php

    function ellipsis($string, $max_length) {
        return (strlen($string) > $max_length) ? substr($string, 0, strrpos(substr($string, 0, $max_length), ' ')) . "…" : $string;
    }
    ?>
    <div class="row" id="pv_form">
        <div class="span11" style="background: #fdfdfd;">
            <a class="button disabled" href="<?php echo base_url().'/public/' . '/pqmp/0/0'; ?>">PQMP</a>
            <a class="button info" href="<?php echo base_url().'/public/' . '/adr/0/0'; ?>">ADR</a>
            <hr>
        </div>
        <div class="span11">
            <h5>Patient ADR Reports</h5>
            <!--a href="<?php echo base_url().'/public/' . 'inventory_management/pqmp_followup/'; ?>" class="btn btn-primary pull-right" id="followup" ><i class="icon icon-refresh"></i> ADR FOLLOWUP  </a-->
            <a href="#newADR" data-toggle="modal" id="NEWADR" class="btn btn-default" > New ADR </a>
<!--            <a href="#synchdata" class="btn btn-warning pull-right" id="SYNDATA" ><i class="icon icon-refresh"></i> Synch with PPB  </a>-->
            <span id="NOCOM" style="display:none">Synch disabled, connection to the SADR server could not be established...</span>

            <span id="SYNCHLOADER" style="display:none;">
                <img src="<?= base_url().'/public/'; ?>/public/assets/images/loading_spin.gif" width="30px" height="30px"/>
                Synchronizing Please wait....<span id="SYNCHPERCENT"></span>
            </span>

            <div style="display:none" id="SYNCHMESSAGE" class="alert alert-success">
                Synchronization Successfully Completed..
            </div>

            <?php $msg = session()->get('pqmp_saved');
            if (!empty($msg)) { ?>
                <div class="alert alert-success">
                    <?= session()->get('pqmp_saved'); ?>
                </div> 
            <?php }
            ?>

            <?php $adr_error = session()->get('adr_error');
            if (!empty($adr_error)) { ?>
                <div class="alert alert-danger">
                    <?= session()->get('adr_error'); ?>
                </div> 
            <?php }
            ?>
        </div>
        <div class="span11">
            <table border="1" class="table" >
                <thead>
                    <th>Synch<br><input type="checkbox" id="CHECKALL"/>
                        <input type="checkbox" checked="checked" name="adrid" style="display:none;" value="1010101010101010"/></th>
                        <th>#</th>
                        <th>Patient</th>
                        <th>diagnosis</th>
                        <th>Severity</th>
                        <th>Outcome</th>
                        <th>Date</th>
                        <th>Action Taken</th>  
                        <th>Action </th>  

                    </thead>
                    <tbody>
                        <?php foreach ($adr_data as $key => $adr) { ?>
                            <tr>
                                <td>

                                 <?php if ($adr['synch'] == '0' || is_null($adr['synch'])) { ?>
                                    <input type="checkbox" name="adrid" value="<?= $adr['id']; ?>"

                                <?php } else { ?>
                                 <span class="">&#9989;</span> 
                             <?php }
                             ?></td>
                             <td> <?= $adr['id']; ?></td>
                             <td><?= ellipsis($adr['patient_name'], 10); ?></td>
                             <td> <?= ellipsis($adr['diagnosis'], 10); ?></td>
                             <td> <?= ellipsis($adr['severity'], 10); ?></td>
                             <td> <?= ellipsis($adr['outcome'], 10); ?></td>
                             <td> <?= ellipsis($adr['datecreated'], 10); ?></td>
                             <td> <?= ellipsis($adr['action_taken'], 10); ?></td>
                             <td style="width:50px;">
                                <?php if ($adr['synch'] == '0') { ?>

                                    <a href="<?= base_url().'/public/'; ?>inventory_management/loadAdrRecord/<?= $adr['id']; ?> "> View</a> |
                                    <a href="<?= base_url().'/public/'; ?>inventory_management/deladr/<?= $adr['id']; ?>/delete"><span style="color:red;font-weight: bold;"> Delete</span></a>
                                <?php } else { ?>
                                    <a href="<?= base_url().'/public/'; ?>inventory_management/loadAdrRecord/<?= $adr['id']; ?> "> View</a>
                                <?php } ?>
                            </td> 


                        </tr>    
                    <?php } ?>
                </tbody>

            </table>
        </div>
    </div>
    <div id="newADR" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <form action="" id="new_ADR">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Select Patient</h3>
        </div>
        <div class="modal-body">
            <div id="profile_error"></div>
            <table>
                <tr>
                    <td>
                        <label>Patient</label>
                    </td>
                    <td>
                        <span class="add-on"><i class=" icon-chevron-down icon-black"></i></span>
                        <select name="new_patient_adr" id="new_patient_adr" class="input-xlarge"  required="">
                            <?php foreach ($patients_arr as $key => $patient) { ?>
                            <option value="<?=$patient['id']?>"><?=$patient['patient_id'].'  '.$patient['first_name'].' '.$patient['last_name']?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
            <input type="submit" class="btn btn-primary" value="Create ADR" id="create_adr_btn">
        </div>
    </form>
</div>
<script type="text/javascript">

        $('#new_patient_adr').select2();

    $(document).ready(function () {
        $('#create_adr_btn').click(function (e) {
            e.preventDefault();
            window.location.href = "<?=base_url().'/public/'?>adr_view/adr/"+$('#new_patient_adr').val();

        });

        $("#CHECKALL").click(function () {
            $("input[name='adrid']:checkbox").not(this).prop('checked', this.checked);
        });


        $('#SYNDATA').click(function () {

            // if ($("input[name='adrid']:checked").length > 0) {
                $('#SYNDATA').prop('disabled', true);
                $('#NEWAD').prop('disabled', true);
                $('#SYNCHLOADER').show();
                var searchIDs = $("input[name='adrid']:checked:checked").map(function () {
                    return $(this).val();
                }).get();

                $.ajax({
                    type: 'post',
                    url: "<?= base_url().'/public/'; ?>inventory_management/getpvdata/adr_form/adr/",
                    dataType: 'json',
                    data: {ids: searchIDs},
                    success: function (resp) {
                        if (resp.status === 'success') {

                            $('#SYNCHLOADER').hide();
                            $('#SYNCHMESSAGE').show();
                            $('#SYNDATA').prop('disabled', false);
                            $('#NEWPQ').prop('disabled', false);
                            setInterval(function () {
                                window.location.href = "<?= base_url().'/public/'; ?>inventory_management/adr/";
                            }, 2000);
                        } else {
                            alert("Something went Wrong, Synchronization could not be done");
                        }

                    },
                    error: function () { },
                    progress: function (e) {
                    //make sure we can compute the length
                    if (e.lengthComputable) {
                        //calculate the percentage loaded
                        var pct = (e.loaded / e.total) * 100;
                        //log percentage loaded
                        $('#SYNCHPERCENT').text(pct);
                    }
                    //this usually happens when Content-Length isn't set
                    else {
                        console.warn('Content Length not reported!');
                    }
                }

            });



            //  } else {
            //  alert("synch Error: No data selected to synch..");
            //}
        });

        setInterval(function () {
            $.getJSON("<?= base_url().'/public/'; ?>inventory_management/serverStatus", function (resp) {
                if (resp.status === 404) {
                    $('#NOCOM').show();
                    $('#SYNDATA').hide();
                } else if (resp.status === 200) {
                    $('#NOCOM').hide();
                    $('#SYNDATA').show();
                }
            });
        }, 25000);

        var storeTable = $('table').dataTable({
            "bJQueryUI": true,
            "sPaginationType": "full_numbers",
            "sDom": '<"H"Tfr>t<"F"ip>',
            "aoColumnDefs": [{"bSortable": false, "aTargets": [0]}],
            "oTableTools": {
                "sSwfPath": base_url + "/public/scripts/datatable/copy_csv_xls_pdf.swf",
                "aButtons": ["copy", "print", "xls", "pdf"]
            },
            "bProcessing": true,
            "bServerSide": false,

        });
        $("#manufacture_date,#expiry_date,#receipt_date").datepicker();
    });

</script>