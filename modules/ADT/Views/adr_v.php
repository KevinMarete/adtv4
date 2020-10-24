<style type="text/css">
    /*@media (min-width: 992px)*/
    /*.modal-lg {*/
    /*width: 900px;*/
    /*}*/

    .f-input{
        width: 100% !important;

    }
    textarea{
        width: 100% !important;
    }
</style>

<div class="container" style="background-color: #fffacc;border: solid thick #2b597e;padding: 30px; margin-top: 130px; margin-bottom: 130px;border-radius:20px;">

    <a href="<?= base_url(); ?>/public/inventory_management/adr" class="btn btn-default" > Back </a>
    <?php if (!\is_null($adr_data[0]['ppid']) || $adr_data[0]['synch'] === '1') { ?>

    <?php } else { ?>
        <a href="<?= base_url(); ?>/public/inventory_management/adr/<?= $record_no; ?>/delete" class="btn btn-danger delete-form" > Delete  </a>  
    <?php } ?>

    <a href="<?= base_url(); ?>/public/inventory_management/export_adr/<?= $record_no; ?>/export" class="btn btn-default" > Export(.xls) </a>
    <form name="adr_form" action="<?= base_url(); ?>/public/inventory_management/adr/<?= $record_no; ?>" method="post">
        <div class="container">
            <div class="row">
                <div class="text-center">
                    <img src="<?php echo base_url() . '/public/assets/images/top_logo.png'; ?>">
                    <h4>
                        MINISTRY OF HEALTH <br />
                        THE PHARMACY AND POISONS BOARD<br />
                        P. O. Box 27663-00506 NAIROBI<br />
                        Tel: (020)-2716905 / 6 Ext 114    Fax: (020) 2713431/2713409.<br />
                        Email: pv@pharmacyboardkenya.org

                    </h4>
                    <h3 style="color:red; font-family: 'Comic Sans MS';">SUSPECTED ADVERSE DRUG REACTION REPORTING FORM</h3>
                </div>
            </div>
        </div>

        <div class="max-row">
            <div class="mid-row">
                <label><span class='astericks'>*</span>REPORT TITLE </label>
                <input type="text" name="report_title" id="report_title" value="<?= $adr_data[0]['report_title']; ?>">
            </div>
            <div class="mid-row">
                <input type="hidden" name="adr_id" value="<?= $adr_data[0]['id']; ?>" class="form-control f-input">

                <label><span class='astericks'>*</span>NAME OF INSTITUTION: </label>
                <input type="text" name="institution" id="institution" value="<?= $adr_data[0]['institution_name']; ?>" class="validate[required] f-input">
            </div>
            <div class="mid-row">
                <label ><span class='astericks'>*</span>INSTITUTION CODE:</label>
                <input  type="text" name="institutioncode" id="institutioncode" value="<?= $adr_data[0]['institution_code']; ?>" class="validate[required] f-input">
            </div>
        </div>

        <div class="max-row">
            <div class="mid-row">
                <label><span class='astericks'>*</span>ADDRESS </label>
                <input type="text" name="address" id="address" value="<?= $adr_data[0]['address']; ?>" class="validate[required] f-input">
            </div>
            <div class="mid-row">
                <label ><span class='astericks'>*</span>CONTACT</label>
                <input  type="text" name="contact" id="contact" value="<?= $adr_data[0]['contact']; ?>" class="validate[required] f-input">
            </div>
        </div>
        <div class="max-row">
            <div class="mid-row">

                <label ><span class='astericks'>*</span>COUNTY:</label>
                <select class="fi" class="" name="county_id"  id="county_id">
                    <option value="<?php echo session()->get('county_id'); ?>" selected="selected"><?php echo session()->get('facility_county'); ?></option>

                </select>

            </div>
            <div class="mid-row">

                <label ><span class='astericks'>*</span>SUB-COUNTY</label>
                <select class="fi" name="sub_county_id" id="sub_county_id"  class="" id="SadrSubCountyId">
                    <option value="<?php echo session()->get('subcounty_id'); ?>"><?php echo session()->get('facility_subcounty'); ?></option>                  

                </select>

            </div>

            <div class="mid-row">
            </div>

        </div>

        <div class="max-row">
            <div class="mid-row">
            </div>
            <div class="mid-row">
            </div>

            <div class="mid-row">
            </div>

        </div>

        <div class="max-row">

            <table border="0" style="width: 100%;">
                <tr>
                    <td> <label><span class='astericks'>*</span>PATIENT’S NAME/ INITIAL </label>
                        <input type="text" name="patientname" id="patientname" value="<?= $adr_data[0]['patient_name']; ?>" class="validate[required] f-input">
                    </td>
                    <td> <label ><span class='astericks'>*</span>IP/OP. NO</label>
                        <input  type="text" name="ip_no" id="ip_no" value="<?= $adr_data[0]['ip_no'] ?>" class="validate[required] f-input">
                    </td>
                    <td> <label ><span class='astericks'>*</span>D.O.B</label>
                        <input  type="text" name="dob" id="dob" value="<?= $adr_data[0]['dob'] ?>" class="validate[required] f-input">
                    </td>
                </tr>
                <tr>
                    <td>  <label><span class='astericks'>*</span>PATIENT’S ADDRESS </label>
                        <input type="text" name="patientaddress" id="patientaddress"  value="<?= $adr_data[0]['patient_address'] ?>" class="validate[required] f-input"></td>
                    <td> <label ><span class='astericks'>*</span>WARD/CLINIC</label>
                        <input  type="text" name="clinic" id="clinic" value="<?= $adr_data[0]['ward_clinic'] ?>" class="validate[required] f-input">
                    </td>
                    <td>
                        <label id="dcs" >GENDER</label>
                        <input  type="radio"  name="gender" <?php
                        if ($adr_data[0]['gender'] == 'MALE') {
                            echo ' checked ';
                        } else {
                            
                        }
                        ?> value="MALE">
                        male 
                        <input  type="radio"  name="gender" <?php
                        if ($adr_data[0]['gender'] == 'FEMALE') {
                            echo ' checked ';
                        } else {
                            
                        }
                        ?>  value="FEMALE">
                        female
                    </td> 
                </tr>
                <tr>
                    <td rowspan="2">
                        <label id="dcs" >ANY KNOWN  ALLERGY</label>
                        <input  type="radio"  name="allergy" class="allergy" <?php
                        if ($adr_data[0]['is_alergy'] == '0') {
                            echo ' checked ';
                        } else {
                            
                        }
                        ?> value="0">
                        no<br />
                        <input  type="radio"  name="allergy" class="allergy" <?php
                        if ($adr_data[0]['is_alergy'] == '1') {
                            echo ' checked ';
                        } else {
                            
                        }
                        ?> value="1">
                        yes<br />
                        if yes, specify
                        <input  type="text" name="allergydesc" id="allergydesc" class="validate[required] f-input"  value="<?= $adr_data[0]['alergy_desc'] ?>">
                    </td>
                    <td rowspan="2">
                        <label id="dcs" >PREGNANCY STATUS</label>
                        <input  type="radio"  name="pregnancystatus" <?php
                        if ($adr_data[0]['is_pregnant'] == 'NotPregnant') {
                            echo ' checked ';
                        } else {
                            
                        }
                        ?> value="NotPregnant">
                        Not Pregnant<br />
                        <input  type="radio"  name="pregnancystatus" <?php
                        if ($adr_data[0]['is_pregnant'] == '1stTrimester') {
                            echo ' checked ';
                        } else {
                            
                        }
                        ?> value="1stTrimester">
                        1st Trimester<br />
                        <input  type="radio"  name="pregnancystatus" <?php
                        if ($adr_data[0]['is_pregnant'] == '2ndTrimester') {
                            echo ' checked ';
                        } else {
                            
                        }
                        ?> value="2ndTrimester">
                        2nd Trimester<br />
                        <input  type="radio"  name="pregnancystatus" <?php
                        if ($adr_data[0]['is_pregnant'] == '3rdTrimester') {
                            echo ' checked ';
                        } else {
                            
                        }
                        ?> value="3rdTrimester">
                        3rd Trimester 
                    </td>
                    <td>
                        <label><span class='astericks'>*</span>WEIGHT (kg): </label>
                        <input type="text" name="patientweight" id="patientweight" value="<?= $adr_data[0]['weight'] ?>" class="validate[required] f-input">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label ><span class='astericks'>*</span>HEIGHT (cm):</label>
                        <input  type="text" name="patientheight" id="patientheight" value="<?= $adr_data[0]['height'] ?>" class="validate[required] f-input">
                    </td>

                </tr>
                <tr>
                    <td colspan="3">
                        <label><span class='astericks'>*</span>DIAGNOSIS: (What was the patient treated for): </label>
                        <select class="fil" name='diagnosis' id='diagnosis' style="width:150px;">
                            <option value="<?= $adr_data[0]['diagnosis'] ?>"><?= $adr_data[0]['diagnosis'] ?></option>
                            <?php
                            foreach ($diagnosis as $d) {
                                ;
                                ?>
                                <option value="<?= str_replace(['Anti', 'Family Planning medicine', 'Essential drug'], '', $d->name); ?>"><?= str_replace(['Anti', 'Family Planning medicine', 'Essential drug'], '', $d->name); ?></option>
<?php }; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <label ><span class='astericks'>*</span>BRIEF DESCRIPTION OF REACTION:</label>
                        <textarea name="reaction" id="reaction" class=""><?= $adr_data[0]['reaction_description'] ?></textarea>         
                    </td>
                </tr>
            </table>
        </div>

        <table class="table table-bordered table-hover" id="tab_logic">
            <thead>
                <tr>
                    <th colspan="2" style="width: 25%;" class="required tooltipper"
                        title="This is the generic name"
                        data-content="">
                        <label class="help-block required">	(include OTC and herbals)  </label>
                        <span class="label label-info">Generic Name</span>
                    </th>
                    <th style="width: 7%;">BRAND NAME</th>
                    <th colspan="2" style="width: 13%;" class="required tooltipper"
                        title="Dosage"
                        data-content="">
                        <label class="required">DOSE <span style="color:red;">*</span></label>
                        <label class="help-block required">	 </label>
                    </th>
                    <th colspan="2" style="width: 23%;" class="required "
                        title=""
                        data-content="">
                        <label class="required">ROUTE AND FREQUENCY <span style="color:red;">*</span></label>
                        <label class="help-block required">	 </label>
                    </th>
                    <th style="width: 10%;" class="required "
                        title=""
                        data-content="">
                        <label class="required">DATE STARTED <span style="color:red;">*</span></label>
                        <label class="help-block required">	(dd-mm-yyyy) </label>
                    </th>
                    <th style="width: 10%;">DATE STOPPED<p class="help-block">	(dd-mm-yyyy) </p></th>
                    <th style="width: 7%;" >
                        <label>INDICATION </span></label>
                    </th>
                    <th colspan="2" style="width: 10%;" class="required tooltipper"
                        title="Drug suspected to cause reaction"
                        data-content="At least one option must be selected<br>">
                        <label class="required">TICK (&#x2713;)  <br/> SUSPECTED DRUG(S) <span style="color:red;">*</span></label>
                        <label class="help-block required">(select at least one) </label>
                        <input type="hidden" name="list" class="" value="" id="SadrList"/>					</th>
                </tr>
            </thead>
            <tbody>


                <?php
                $i = 1;
                foreach ($adr_details as $pv) {
                    ?>
                    <tr>
                        <td> <?= $i; ?></td>
                        <td title="Generic Name *"><div class="control-group required"><input name="drug_name[]" maxlength="100" value="<?= $pv['drug'] ?>" type="text" style="width:130px !important;" /></div></td>
                        <td title="Brand Name">
                            <div class="control-group"><input name="brand_name[]" value="<?= $pv['brand'] ?>" maxlength="100" style="width:50px !important;" type="text"/></div></td>
                        <td  title="Dose *">

                            <div class="control-group required"><input name="dose[]" maxlength="100" value="<?= $pv['dose'] ?>"  style="width:40px !important;" type="text" /></div></div></td>
                        <td>
                            <div class="control-group required">
                                <select class="fil" name="dose_id[]"  style="width:95px !important;">
                                    <option value="<?= $pv['dose_id'] ?>" selected="selected"><?= $pv['dose_unit'] ?></option>
                                    <option value="2">mg</option>
                                    <option value="3">ml</option>
                                    <option value="4">µg</option>
                                    <option value="5">g</option>
                                    <option value="6">Iu</option>
                                    <option value="7">DF dosage form</option>
                                    <option value="8">Gtt drop(s)</option>
                                    <option value="9">mmol</option>
                                    <option value="10">meq</option>
                                    <option value="11">%</option>
                                    <option value="12">µCi</option>
                                    <option value="13">µg/kg</option>
                                    <option value="14">µg/m2</option>
                                    <option value="15">µl</option>
                                    <option value="16">µmol</option>
                                    <option value="17">Bq</option>
                                    <option value="18">Ci curie(s)</option>
                                    <option value="19">GBq</option>
                                    <option value="20">iu/kg</option>
                                    <option value="21">Kbq</option>
                                    <option value="22">kg</option>
                                    <option value="23">Kiu</option>
                                    <option value="24">l</option>
                                    <option value="25">MBq</option>
                                    <option value="26">mCi</option>
                                    <option value="27">mg/kg</option>
                                    <option value="28">mg/m2</option>
                                    <option value="29">Miu</option>
                                    <option value="30">Mol</option>
                                    <option value="31">nCi</option>
                                    <option value="32">ng</option>
                                    <option value="33">pg</option>
                                </select></div>	</td>
                        <td title="Route *"><div class="control-group required">
                                <select class="fil" name="route_id[]" style="width:110px !important;" >

                                    <option value="<?= $pv['route'] ?>" selected="selected"><?= $pv['route_name'] ?></option>
                                    <option value="2">oral</option>
                                    <option value="3">intravenous drip</option>
                                    <option value="4">intravenous bolus</option>
                                    <option value="5">subcutaneous</option>
                                    <option value="6">nasal</option>
                                    <option value="7">sublingual</option>
                                    <option value="8">topical</option>
                                    <option value="9">rectal</option>
                                    <option value="10">intra-articular</option>
                                    <option value="11">intrathecal</option>
                                    <option value="12">intra-arterial</option>
                                    <option value="13">auricular (otic)</option>
                                    <option value="14">buccal</option>
                                    <option value="15">cutaneous</option>
                                    <option value="16">dental</option>
                                    <option value="17">endocervical</option>
                                    <option value="18">endosinusial</option>
                                    <option value="19">endotracheal</option>
                                    <option value="20">epidural</option>
                                    <option value="21">extra-amniotic</option>
                                    <option value="22">hemodialysis</option>
                                    <option value="23">intra corpus cavernosum</option>
                                    <option value="24">intra-amniotic</option>
                                    <option value="25">intracardiac</option>
                                    <option value="26">intracavernous</option>
                                    <option value="27">intracerebral</option>
                                    <option value="28">intracervical</option>
                                    <option value="29">intracisternal</option>
                                    <option value="30">intracorneal</option>
                                    <option value="31">intracoronary</option>
                                    <option value="32">intradermal</option>
                                    <option value="33">intradiscal (intraspinal)</option>
                                    <option value="34">intrahepatic</option>
                                    <option value="35">intralesional</option>
                                    <option value="36">intralymphatic</option>
                                    <option value="37">intramedullar (bone marrow)</option>
                                    <option value="38">intrameningeal</option>
                                    <option value="39">intramuscular</option>
                                    <option value="40">intraocular</option>
                                    <option value="41">intrapericardial</option>
                                    <option value="42">intraperitoneal</option>
                                    <option value="43">intrapleural</option>
                                    <option value="44">intrasynovial</option>
                                    <option value="45">intrathoracic</option>
                                    <option value="46">intratracheal</option>
                                    <option value="47">intratumor</option>
                                    <option value="48">intra-uterine</option>
                                    <option value="49">intravenous (nos)</option>
                                    <option value="50">intravesical</option>
                                    <option value="51">iontophoresis</option>
                                    <option value="52">occlusive dressing technique</option>
                                    <option value="53">ophthalmic</option>
                                    <option value="54">oropharingeal</option>
                                    <option value="55">other</option>
                                    <option value="56">parenteral</option>
                                    <option value="57">periarticular</option>
                                    <option value="58">perineural</option>
                                    <option value="59">respiratory (inhalation)</option>
                                    <option value="60">retrobulbar</option>
                                    <option value="61">subdermal</option>
                                    <option value="62">sunconjunctival</option>
                                    <option value="63">transdermal</option>
                                    <option value="64">transmammary</option>
                                    <option value="65">transplacental</option>
                                    <option value="66">unknown</option>
                                    <option value="67">urethral</option>
                                    <option value="68">vaginal</option>
                                </select></div>				
                        </td>
                        <td  title="Frequency *"><div class="control-group required">
                                <select class="fil" name="frequency_id[]" style="width:160px !important;">
                                    <option value="<?= $pv['route_freq'] ?>" selected="selected"><?= $pv['freq_name'] ?></option>
                                    <option value="2">OD (once daily)</option>
                                    <option value="3">BD (twice daily)</option>
                                    <option value="4">TID. (three times a day)</option>
                                    <option value="5">QID|QDS (four times a day)</option>
                                    <option value="6">PRN PRN (as needed)</option>
                                    <option value="7">MANE (in the morning)</option>
                                    <option value="8">NOCTE (at night)</option>
                                    <option value="9">STAT (immediately)</option>
                                </select></div></td>
                        <td title="Date Started (dd-mm-yyyy) *">
                            <div class="control-group required"><input name="dispensing_date[]" class="ddate" type="text" value="<?= $pv['date_started'] ?>" /></div></td>
                        <td title="Date Stopped (dd-mm-yyyy)">
                            <div class="control-group"><input name="date_stopped[]" type="text" class="sdate" value="<?= $pv['date_stopped']; ?>"/></div></td>
                        <td title="Indication">
                            <div class="control-group"><input name="indication[]" maxlength="100" type="text" value="<?= $pv['indication'] ?>" /></div></td>
                        <td title="Suspected Drug?">
                            <input type="hidden" name="visitid[]" value="<?= $pv['visitid'] ?>" class="form-control f-input">
                            <input type="hidden" name="adr_id[]" value="<?= $pv['id'] ?>" class="form-control f-input">

                            <div class="control-group"><input type="checkbox" name="suspecteddrug[]"  <?php
                                if ($pv['suspecteddrug'] == '1') {
                                    echo ' checked ';
                                } else {
                                    
                                }
                                ?> value="1" /></div></td>

                    </tr>

                    <?php
                    $i++;
                }
                ?>

            </tbody>
        </table>

        <div class="max-row">

            <div class="mid-row">
                <label id="dcs" for="severity" ><b>Severity of reaction (refer to scale overleaf)</b></label>
                <input  type="radio" class="severity"  name="severity" <?php
                if ($adr_data[0]['severity'] == 'Mild') {
                    echo 'checked';
                } else {
                    
                }
                ?> value="Mild">
                Mild <br /> 
                <input  type="radio" class="severity"  name="severity" <?php
                if ($adr_data[0]['severity'] == 'Moderate') {
                    echo 'checked';
                } else {
                    
                }
                ?>  value="Moderate">
                Moderate <br /> 
                <input  type="radio" class="severity"  name="severity" <?php
                if ($adr_data[0]['severity'] == 'Severe') {
                    echo 'checked';
                } else {
                    
                }
                ?>  value="Severe">
                Severe <br /> 
                <input  type="radio" class="severity"  name="severity" <?php
                if ($adr_data[0]['severity'] == 'Fatal') {
                    echo 'checked';
                } else {
                    
                }
                ?>  value="Fatal">
                Fatal <br /> 
                <input  type="radio" class="severity"  name="severity" <?php
                if ($adr_data[0]['severity'] == 'Unknown') {
                    echo 'checked';
                } else {
                    
                }
                ?> value="Unknown">
                Unknown
            </div>


            <div class="mid-row">
                <label id="" for="action" ><b>ACTION TAKEN</b></label>
                <input  type="radio"  name="action" class="action" <?php
                if ($adr_data[0]['action_taken'] == 'Drug withdrawn') {
                    echo 'checked';
                } else {
                    
                }
                ?> value="Drug withdrawn">
                Drug withdrawn<br />
                <input  type="radio"  name="action" class="action" <?php
                if ($adr_data[0]['action_taken'] == 'Dose increased') {
                    echo 'checked';
                } else {
                    
                }
                ?>  value="Dose increased">
                Dose increased<br />
                <input  type="radio"  name="action" class="action" <?php
                if ($adr_data[0]['action_taken'] == 'Dose reduced') {
                    echo 'checked';
                } else {
                    
                }
                ?>  value="Dose reduced">
                Dose reduced<br />
                <input  type="radio"  name="action" class="action" <?php
                if ($adr_data[0]['action_taken'] == 'Dose not changed') {
                    echo 'checked';
                } else {
                    
                }
                ?>  value="Dose not changed">
                Dose not changed<br />
                <input  type="radio"  name="action" class="action" <?php
                if ($adr_data[0]['action_taken'] == 'Unknown') {
                    echo 'checked';
                } else {
                    
                }
                ?>  value="Unknown">
                Unknown
            </div>


            <div class="mid-row">
                <label id="dcs" for="outcome" ><b>OUTCOME</b></label>
                <input  type="radio"  name="outcome" class="outcome" <?php
                if ($adr_data[0]['outcome'] == 'Recovering / resolving') {
                    echo 'checked';
                } else {
                    
                }
                ?> value="Recovering / resolving">
                Recovering / resolving<br />
                <input  type="radio"  name="outcome" class="outcome" <?php
                if ($adr_data[0]['outcome'] == 'Recovered / resolved') {
                    echo 'checked';
                } else {
                    
                }
                ?>  value="Recovered / resolved">
                Recovered / resolved<br />
                <input  type="radio"  name="outcome" class="outcome" <?php
                if ($adr_data[0]['outcome'] == 'Requires or prolongs hospitalization') {
                    echo 'checked';
                } else {
                    
                }
                ?>  value="Requires or prolongs hospitalization">
                Requires or prolongs hospitalization<br />
                <input  type="radio"  name="outcome" class="outcome" <?php
                if ($adr_data[0]['outcome'] == 'Causes a congenital anomaly"') {
                    echo 'checked';
                } else {
                    
                }
                ?>  value="Causes a congenital anomaly">
                Causes a congenital anomaly<br />
                <input  type="radio"  name="outcome" class="outcome" <?php
                if ($adr_data[0]['outcome'] == 'Requires intervention to prevent permanent damage') {
                    echo 'checked';
                } else {
                    
                }
                ?>  value="Requires intervention to prevent permanent damage">
                Requires intervention to prevent permanent damage <br />
                <input  type="radio"  name="outcome" class="outcome" <?php
                if ($adr_data[0]['outcome'] == 'Unknown') {
                    echo 'checked';
                } else {
                    
                }
                ?>  value="Unknown">
                Unknown
            </div>


            <div class="mid-row">
                <label id="dcs" for="casuality" ><b>CAUSALITY OF REACTION (refer to scale overleaf)</b></label>
                <input  type="radio"  name="casuality" class="casuality" <?php
                if ($adr_data[0]['reaction_casualty'] == 'Certain') {
                    echo 'checked';
                } else {
                    
                }
                ?>   value="Certain">
                Certain <br />
                <input  type="radio"  name="casuality" class="casuality" <?php
                if ($adr_data[0]['reaction_casualty'] == 'Probable / Likely') {
                    echo 'checked';
                } else {
                    
                }
                ?>   value="Probable / Likely">
                Probable / Likely <br />
                <input  type="radio"  name="casuality" class="casuality" <?php
                if ($adr_data[0]['reaction_casualty'] == 'Possible') {
                    echo 'checked';
                } else {
                    
                }
                ?>   value="Possible">
                Possible <br />
                <input  type="radio"  name="casuality" class="casuality" <?php
                if ($adr_data[0]['reaction_casualty'] == 'Unlikely') {
                    echo 'checked';
                } else {
                    
                }
                ?>  value="Unlikely">
                Unlikely <br />
                <input  type="radio"  name="casuality" class="casuality" <?php
                if ($adr_data[0]['reaction_casualty'] == 'Conditional / Unclassified') {
                    echo 'checked';
                } else {
                    
                }
                ?>  value="Conditional / Unclassified">
                Conditional / Unclassified <br />
                <input  type="radio"  name="casuality" class="casuality" <?php
                if ($adr_data[0]['reaction_casualty'] == 'Unassessable / Unclassifiable') {
                    echo 'checked';
                } else {
                    
                }
                ?>   value="Unassessable / Unclassifiable">
                Unassessable / Unclassifiable
            </div>

        </div>
        <div class="max-row">
            <div class="mid-row">
                <label class="blue" for="othercomment">ANY OTHER COMMENT : </label>
                <textarea name="othercomment" class="" id="othercomment"><?= $adr_data[0]['other_comment']; ?></textarea>

            </div>
        </div>


        <div class="max-row">
            <div class="mid-row">
                <label><span class='astericks'>*</span>NAME OF PERSON REPORTING </label>
                <input type="text" name="officername" id="officername" value="<?= $adr_data[0]['reporting_officer']; ?>" class="validate[required] f-input">
            </div>
            <div class="mid-row">
                <label ><span class='astericks'>*</span>DATE:</label>
                <input  type="text" name="reportingdate" id="reportingdate" class="validate[required] f-input adrdate" value="<?= $adr_data[0]['reporting_date']; ?>">
            </div>
            <div class="mid-row">
                <label><span class='astericks'>*</span>Email ADDRESS </label>
                <input type="text" name="officeremail" id="officeremail" value="<?= $adr_data[0]['email_address']; ?>" class="validate[required] f-input">
            </div>
            <div class="mid-row">
                <label><span class='astericks'>*</span>Office Phone </label>
                <input type="text" name="officerphone" id="officerphone" value="<?= $adr_data[0]['office_phone']; ?>" class="validate[required] f-input">
            </div>
            <div class="mid-row">
                <label><span class='astericks'>*</span>Designation</label>
                <select name="officerdesignation" class="fil" id="officerdesignation">
                    <option value="<?= $adr_data[0]['designation']; ?>"selected="selected"><?= $adr_data[0]['designation_d']; ?></option>
                    <option value="1">physician</option>
                    <option value="2">pharmacist</option>
                    <option value="3">other professional</option>
                    <option value="5">consumer or other non health professional</option>
                </select>
            </div>
            <div class="mid-row">
                <label><span class='astericks'>*</span>Signature </label>
                <input type="text" name="officersignature" id="officersignature" class="validate[required] f-input" value="<?= $adr_data[0]['signature']; ?>">
            </div>
            <div class="mid-row" id="submit-container" >
                <?php if ($adr_data[0]['synch'] !== '1') { ?>
                    <button type="submit" class="btn btn-primary" value="Submit">Submit</button>
<?php } ?>

            </div>
        </div>
    </form>
</div>

<script type="text/javascript">

    $(document).ready(function () {
        $('.fil').select2();

        $("#adr_form").validate({
            rules: {
                facility_name: "required",
                county_id: "required",
                sub_county_id: "required",
                facility_address: "required",
                facility_phone: "required",
                facility_code: "required",
                brand_name: "required",
                genname: "required",
                batch_no: "required",
                manufacture_date: "required",
                expiry_date: "required",
                receipt_date: "required",
                manufacturer_name: "required",
                country_id: "required",
                supplier_name: "required",
                supplier_address: "required",
                product_formulation: {required: true, minlength: 1},
                spam: "required",
                description: "required",
                product_refrigiration: "required",
                product_availability: "required",
                product_returned: "required",
                product_storage: "required",
                reporter_name: "required",
                reporter_phone: "required",
                reporter_signature: "required"

            },
            messages: {
                facility_name: "Please enter facility name",
                county_id: "Please select county",
                sub_county_id: "Please select sub county",
                facility_address: "Please enter facility address",
                facility_phone: "Please enter facility phone",
                facility_code: "Please enter facility code",
                brand_name: "Please enter  brand name",
                genname: "Please enter  generic name",
                batch_no: "Please enter  batch no",
                manufacture_date: "Please enter mfg date",
                expiry_date: "Please enter expiry date",
                receipt_date: "Please enter receipt date",
                country_id: "Please select country",
                manufacturer_name: "Please enter Manufacturer",
                supplier_name: "Please enter supplier name",
                supplier_address: "Please enter supplier address",
                product_formulation: "Please select atleast 1",
                PqmpComplaint: "Please select atleast 1",
                spam: "Please select atleast 1",
                description: "Please enter description",
                product_refrigiration: "Please select one",
                product_availability: "Please select one",
                product_returned: "Please select one",
                product_storage: "Please select one",
                reporter_name: "Please enter name",
                reporter_phone: "Please enter phone",
                reporter_signature: "Please signature",
            }
        });






        $('.delete-form').click(function (e) {
            var answer = confirm('Deleting ADR. Are You Sure?');
            if (answer) {

            } else {
                e.preventDefault();
            }
        });



        $(".adrdate,#dob,.ddate,.sdate").datepicker({dateFormat: 'yy-mm-dd'});

        $('.drugs').change(function (e) {
            var row_id = $(this).parent().parent().attr('rowid');
            console.log(row_id);
        });


    });

</script>


