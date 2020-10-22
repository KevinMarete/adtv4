<?php helper('form'); ?>
<style>
    #regimen_drug_listing {
        width: 90%;
        margin: 10px auto;
    }
    a {
        text-decoration: none;
    }
    .enable_user {
        color: green;
        font-weight: bold;
    }
    .disable_user {
        color: red;
        font-weight: bold;
    }
    .edit_user {
        color: blue;
        font-weight: bold;
    }
    .ui-multiselect-menu{
        zoom:1;
        display:none; 
        padding:3px; 
        position:fixed; 
        z-index:10000; 
        text-align: left 
    }
    .enabled{
        color: green;
    }
    .disabled{
        color: red;
    }

</style>
<script type="text/javascript">
    // regimen_drug_management/disable

    function toggleDrugRegimen(regimen_drug) {

        if ($('.reg_drug_name_' + regimen_drug).hasClass('enabled')) {
            // disable drug
            $.get(base_url + "regimen_drug_management/disable/" + regimen_drug, function (data, status) {
                $('.reg_drug_name_' + regimen_drug).removeClass('enabled');
                $('.reg_drug_name_' + regimen_drug).addClass('disabled');
            });
        } else {
            // enable drug
            $.get(base_url + "regimen_drug_management/enable/" + regimen_drug, function (data, status) {
                $('.reg_drug_name_' + regimen_drug).removeClass('disabled');
                $('.reg_drug_name_' + regimen_drug).addClass('enabled');
                // alert(data);
            });
        }
    }

    $(document).ready(function () {
        $("#drugid").multiselect().multiselectfilter();
        /*Prevent Double Click*/
        $('input_form').submit(function () {
            $(this).find(':submit').attr('disabled', 'disabled');
        });
    });
    //process drugs form drug multiselect
    function process_drugs() {
        var drugs = $("select#drugid").multiselect("getChecked").map(function () {
            return this.value;
        }).get();
        $("#drugs_holder").val(drugs);
    }
</script>
<div id="view_content">
    <div class="container-fluid">
        <div class="row-fluid row">
            <!-- Side bar menus -->
            <?php echo view('\Modules\ADT\Views\\settings_side_bar_menus_v.php'); ?>
            <!-- SIde bar menus end -->
            <div class="span12 span-fixed-sidebar">
                <div class="hero-unit">
                    <?php //echo validation_errors('<p class="error">', '</p>'); ?>
                    <a href="#entry_form" role="button" id="new_regimen_drug" class="btn" data-toggle="modal"><i class="icon-plus icon-black"></i>New Regimen Drug</a>	
                    <table class="table table-bordered table-hover table-striped setting_table " id="brand_name_table">
                        <thead>
                            <tr>

                                <th>Regimens</th>
                                <th>Lines</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $access_level = session()->get('user_indicator');

                            foreach ($regimens as $regimen) {
                                // dd($regimen);

                                if ($regimen->regimen_desc != "") {
                                    ?>
                                    <tr>
                                        <td><?php echo "<b>" . $regimen->regimen_code . "</b> | " . $regimen->regimen_desc; ?></td>
                                        <td><?php echo $regimen->Regimen_service_type->name ?></td>
                                        <td><a href="#show_drugs_<?php echo $regimen->id ?>" data-toggle="modal">View List of drugs</a></td>
                                    </tr>
                                    <!-- Hide list of drugs for each regimen -->
                                <div style="width:680px;margin-left:-340px" id="show_drugs_<?php echo $regimen->id ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        <h3>Drug List for Regimen <?php echo $regimen->regimen_code; ?></h3>
                                    </div>
                                    <div class="modal-body">
                                        <small><span style="color: red;">red = disabled</span>; <span style="color: green;">green = enabled</span></small>
                                        <div class="row-fluid">
                                            <div class="span8 reg_drug_name f_left"><strong>Drug</strong></div>
                                            <div class="span4 reg_drug_name f_right"><strong>Option</strong></div>
                                        </div>
                                        <div><hr size='1'></div>
                                        <?php
                                        foreach ($regimen->Regimen_Drug as $k => $drug) {
                                            ?>
                                            <div class="row-fluid">
                                                <?php
                                                $drug_id = $drug->id;

                                                if ($drug->id != "") {
                                                    $class = ( $drug->active == 1) ? 'enabled' : 'disabled';
                                                    ?>
                                                    <div class="span8 reg_drug_name_<?= $drug_id . ' ' . $class; ?> f_left"><?php echo $drug->Drugcode->drug ?? 'N/A'; ?></div>
                                                    <div class="span4 reg_drug_name f_right"><?php
                                    if ($drug->active == 1) {
                                        echo "<input type='checkbox' onClick='toggleDrugRegimen($drug_id)' checked>";
                                    } else {
                                        $drug_id = $drug->id;
                                        echo "<input type='checkbox' onClick='toggleDrugRegimen($drug_id)'>";
                                    }
                                                    ?>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                            <?php
                                        }
                                        ?>


                                    </div>
                                    <div class="modal-footer">

                                    </div>
                                </div>
                                <!-- Hide list of drugs for each regimen end -->
                                <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>

                </div>
            </div><!--/span-->
        </div><!--/row-->
    </div><!--/.fluid-container-->

    <div id="entry_form" title="New Regimen Drug" class="modal hide fade cyan" tabindex="-1" role="dialog" aria-labelledby="NewDrug" aria-hidden="true">
        <?php
        $attributes = array(
            'class' => 'input_form',
            'onsubmit' => 'return process_drugs()');
        echo form_open('regimen_drug_management/save', $attributes);
        ?>
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="NewDrug">Drug details</h3>
        </div>
        <div class="modal-body">
            <table>
                <tr><td><strong class="label">Select Regimen</strong></td>
                    <td>
                        <select class="input-xlarge" id="regimen" name="regimen">
                            <?php
                            foreach ($regimens_enabled as $regimen) {
                                ?>
                                <option value="<?php echo $regimen->id; ?>"><?php echo $regimen->regimen_code . " | " . $regimen->regimen_desc; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr><td><strong class="label">Select Drug</strong></td>
                    <td>
                        <input type="hidden" id="drugs_holder" name="drugs_holder" />
                        <select class="input-xlarge multiselect" multiple="multiple" style="width:400px;" id="drugid" name="drug" required>
                            <?php
                            foreach ($drug_codes_enabled as $drug) {
                                ?>
                                <option value="<?php echo $drug ['id']; ?>"><?php echo $drug['Drug']; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </table>
        </div>	

        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
            <input type="submit" value="Save" class="btn btn-primary " />
        </div>
        <?php echo form_close(); ?>
    </div>

</div>