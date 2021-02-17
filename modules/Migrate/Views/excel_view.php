<div class="container-fluid">
	<div class="row">
		<div class="col-sm-12 col-md-12" id="migration_complete_msg"></div>
	</div>
	<div class="row">
		<div class="col-sm-12 col-md-12">
			<h3>Excel to webADT Import</h3>
		</div>
    </div>
    <?php if(session()->getFlashdata('msg')){ ?>
    <div class="alert alert-<?= session()->getFlashdata('msg_type') ?> alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong><?= ucfirst(session()->getFlashdata('msg_type')) ?>!</strong> <?= ucfirst(session()->getFlashdata('msg')) ?>
    </div>
    <?php } ?>
	
		<div class="row">
			<div class="col-sm-6 col-md-4">
                <form id="fmMigration" action="excel/upload_file" method="post" enctype="multipart/form-data">
				<div class="form-group">
					<label for="upload_type">Upload type</label>
					<select id="upload_type" name="upload_type" class="validate" style="width:90%" required>
						<option value="" disabled selected>Select one</option>
                        <option value="patient_list">Patient list</option>
                        <option value="patient_history">Patient history</option>
					</select>
                </div>

				<div class="form-group">
					<label for="mflcode">Facility code (mflcode)</label>
					<input type="text" id="mflcode" name="mflcode" class="validate" style="width:90%" required />
                </div>
                
                <div class="form-group" id="fg_ccc_pharmacy">
                    <label for="excel_file">Upload excel file</label><br />
                    <input type="file" id="excel_file" name="excel_file" accept=".xlsx" />
                </div>

                <button type="submit" id="submit" class="btn btn-primary">Start Data Import</button>
                </form>
            </div>
            <div class="col"></div>
            <div class="col-md-3 pull-right">
                <h3>Templates</h3>
                <ul style="list-style: none; padding-left: 0;">
                    <li>
                        <a href="<?= base_url() ?>/migrate/excel/template/patient_list">Patient list</a>
                    </li>
                    <li>
                        <a href="<?= base_url() ?>/migrate/excel/template/patient_history">Patient history</a>
                    </li>
                </ul>
            </div>
		</div>
</div>