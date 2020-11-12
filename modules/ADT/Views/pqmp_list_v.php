<style type="text/css">
    /*@media (min-width: 992px)*/
    /*.modal-lg {*/
    /*width: 900px;*/
    /*}*/
    table{
        width:100%;
    }

</style>
<?php

function ellipsis($string, $max_length) {
    return (strlen($string) > $max_length) ? substr($string, 0, strrpos(substr($string, 0, $max_length), ' ')) . "…" : $string;
}
?>
<div class="row">
    <div class="span11" style="background: #fdfdfd;">
        <a class="button info" href="<?php echo base_url().'/pqmp/0/0'; ?>">PQMP</a>
        <a class="button disabled" href="<?php echo base_url() . '/adr/0/0'; ?>">ADR</a>
        <hr>
    </div>
    <div class="span11">
        <h5>PQMP Reports</h5>
    </div>
    <div class="span11">
        <a href="<?= base_url(); ?>/inventory_management/new_pqmp"  id="NEWPQ" class="btn btn-default" > New PQMQ </a>
<!--        <a href="#synchdata" class="btn btn-warning pull-right" id="SYNDATA" ><i class="icon icon-refresh"></i> Synch with PPB </a>-->
        <span id="NOCOM" style="display:none">Synch disabled, connection to the SPQM server could not be established...</span>
        <span id="SYNCHLOADER" style="display:none;">
            <img src="<?= base_url(); ?>/assets/images/loading_spin.gif" width="30px" height="30px"/>
            Synchronizing Please wait....
        </span>

        <div style="display:none" id="SYNCHMESSAGE" class="alert alert-success">
            Synchronization Successfully Completed..
        </div>
        <div style="display:none" id="SYNCHMESSAGENU" class="alert alert-success">
            Synchronization Successfully Completed. No New updates found...
        </div>

        <?php
        $pqmp_saved = session()->getFlashdata('pqmp_saved');
        if (!empty($pqmp_saved)) {
            ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('pqmp_saved'); ?>
            </div> 
        <?php }
        ?>

        <?php
        $pqmp_error = session()->getFlashdata('pqmp_error');
        if (!empty($pqmp_error)) {
            ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('pqmp_error'); ?>
            </div> 
        <?php }
        ?>



        <table border="1" class="table" >
            <thead>
            <th>
                Synch<br><input type="checkbox" id="CHECKALL"/>
                <input type="checkbox" checked="checked" name="pmpqid" style="display:none;" value="1010101010101010"/>
            </th>


            <th>Brand Name</th>
            <th>Generic Name</th>
            <th>Batch No</th>
            <th>Manufacturer Name</th>
            <th>Receipt Date</th>
            <th>Supplier Name</th> 
            <th>Action</th>

            </thead>
            <tbody>
                <?php foreach ($pqmp_data as $key => $pqmp) { ?>
                    <tr>
                        <td>
                            <?php if ($pqmp['synch'] == '0') { ?>
                                <input type="checkbox" class="synvals" value="<?= $pqmp['id']; ?>" name="pmpqid">
                            <?php } else { ?>
                                <span class=""> <span class="">&#9989;</span> </span> 
                            <?php }
                            ?>
                        </td>
                        <td> <?= ellipsis($pqmp['brand_name'], 20); ?></td>
                        <td> <?= ellipsis($pqmp['generic_name'], 20); ?></td>
                        <td> <?= ellipsis($pqmp['batch_number'], 20); ?></td>
                        <td> <?= ellipsis($pqmp['name_of_manufacturer'], 10); ?></td>
                        <td> <?= ellipsis($pqmp['receipt_date'], 20); ?></td>
                        <td> <?= ellipsis($pqmp['supplier_name'], 20); ?></td>               
                        <td style="width:50px;">
                            <?php if ($pqmp['synch'] == '0') { ?>
                        
                                <a href="<?= base_url(); ?>/inventory_management/loadRecord/<?= $pqmp['id']; ?> "> View</a> |
                                <a href="<?= base_url(); ?>/pqmp/<?= $pqmp['id']; ?>/delete"><span style="color:red;font-weight: bold;"> Delete</span></a>
                            <?php } else { ?>
                                <a href="<?= base_url(); ?>/inventory_management/loadRecord/<?= $pqmp['id']; ?> "> View</a>
                            <?php }?>
                        </td> 



                    </tr>    
                <?php } ?>
            </tbody>

        </table>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {

        $("#CHECKALL").click(function () {
            $("input[name='pmpqid']:checkbox").not(this).prop('checked', this.checked);
        });

        $('#SYNDATA').click(function () {

            $('#SYNDATA').prop('disabled', true);
            $('#NEWPQ').prop('disabled', true);
            $('#SYNCHLOADER').show();
            var searchIDs = $("input[name='pmpqid']:checked:checked").map(function () {
                return $(this).val();
            }).get();

            $.post("<?= base_url(); ?>/inventory_management/getpvdata/pqms/pqm/", {ids: searchIDs}, function (resp) {

                if (resp.status === 'success') {

                    $('#SYNCHLOADER').hide();
                    $('#SYNCHMESSAGE').show();
                    $('#SYNDATA').prop('disabled', false);
                    $('#NEWPQ').prop('disabled', false);
                    setInterval(function () {
                        window.location.href = "<?= base_url(); ?>/inventory_management/pqmp/";
                    }, 2000);

                } else if (resp.status === 'none') {
                    $('#SYNCHMESSAGENU').show();
                } else {
                    alert("Something went Wrong, Synchronization could not be done");
                }
            }, 'json');


        });


        setInterval(function () {
            $.getJSON("<?= base_url(); ?>/inventory_management/serverStatus", function (resp) {
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
                "sSwfPath": base_url + "scripts/datatable/copy_csv_xls_pdf.swf",
                "aButtons": ["copy", "print", "xls", "pdf"]
            },
            "bProcessing": true,
            "bServerSide": false,
        });


        $("#manufacture_date,#expiry_date,#receipt_date").datepicker();

    });

</script>