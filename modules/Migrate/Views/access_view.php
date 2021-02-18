<style type="text/css">
    .progress{
        margin-top:0;
        width:100%;
    }
    .label{
        font-size: 1em !important;
    }
    #table_progress{
        height:10em;
        overflow-y:scroll;
        padding:0.4em;
    }
    em.invalid{
        display: block;
    }
    .ticket-title{
        display: block;
        margin: 3px;
    }
    .label{
        font-size: .9em !important;
        font-weight: bold;
        display: block;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
        color: #036;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 col-md-12" id="migration_complete_msg"></div>
    </div>
    <form id="fmMigration" action="access/migrate" method="post">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <h3>Access to webADT Migration</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="facility_code">Facility</label><br />
                    <input type="text" id="facility_code" name="facility_code" class=" validate" style="width:90%">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="source_database">Database</label>
                    <select id="source_database" name="source_database" class=" validate" style="width:90%">
                        <option value=""> Select Database </option>
                        <?php
                        foreach ($databases as $database) {
                            ?>
                            <option value="<?php echo $database['Database']; ?>"><?php echo $database['Database']; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group" id="fg_ccc_pharmacy">
                    <label for="ccc_pharmacy">Pharmacy</label><br />
                    <select id="ccc_pharmacy" name="ccc_pharmacy" class="validate" style="width:90%;">
                        <option value=""> Select Pharmacy </option>
                        <?php
                        foreach ($stores as $store) {
                            ?>
                            <option value="<?php echo $store['ccc_id']; ?>"><?php echo $store['ccc_name']; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-6 col-md-6">
                <div class="form-group">
                    <label for="table" id="lbltable" name="lbltable">Tables</label> <br />
                    <select class=" form-control multiselect col-md-12 validate" id="table" name="table" multiple="multiple" required="required">
                        <?php
                        foreach ($tables as $table => $table_config) {
                            ?>
                            <option value="<?php echo $table; ?>"><?php echo $table; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-sm-6 col-md-6">
                <button type="submit" id="migrate_btn" class="btn btn-primary">Start Migration</button>

            </div>
        </div>

        <!--bottom-->
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <h3>Migration Progress</h3>
            </div>
        </div>
        <!--overall progress-->
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <label>Overall Progress</label>
                <div id="overall_progress_bar" class="progress active">
                    <div id="migration_overall_progress" class="progress-bar" style="width:0%;"></div>
                </div>
            </div>
        </div>
        <!--line separator-->
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <p></p>
                <hr size="2">
                <p></p>
            </div>
        </div>
        <!--table progress-->
        <div class="row">
            <div class="col-sm-12 col-md-12" id="table_progress">
                <div id="migrate_table_result_holder">
                </div>
            </div>
        </div>
</div>

</form>
</div>
<link href="<?= base_url() . '/css/bootstrap-multiselect.css'; ?>" type="text/css" rel="stylesheet"/>
<link href="<?= base_url() . '/css/select2.css'; ?>" type="text/css" rel="stylesheet"/>
<!--scripts-->
<script src="<?= base_url(); ?>/js/bootstrap-multiselect.js"></script>
<script src="<?= base_url(); ?>/js/jquery.validate.min.js"></script>
<script src="<?= base_url(); ?>/js/select2.js"></script>
<script src="<?= base_url() ?>/js/access-migration.js"></script>