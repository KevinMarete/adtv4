<!DOCTYPE html>
<html>
    <head>
        <!--title-->
        <title><?php echo $title; ?></title>
        <!--favicon-->
        <link rel="shortcut icon" type="text/css" href="<?php echo base_url() . '/public/assets/images/favicon.ico'; ?>">
        <!--smartwizard-css-->
        <link href="<?php echo base_url() . '/public/lib/smartwizard/css/smart_wizard.css'; ?>" rel="stylesheet" type="text/css">
        <!--select2-css-->
        <link href="<?php echo base_url() . '/public/lib/select2/css/select2.min.css'; ?>" rel="stylesheet" type="text/css">
        <!--datatables-css-->
        <link href="<?php echo base_url() . '/public/lib/datatables/css/jquery.dataTables.min.css'; ?>" rel="stylesheet" type="text/css">
        <!--migrator-css-->
        <link href="<?php echo base_url() . '/public/lib/migrator/css/migrator.css'; ?>" rel="stylesheet" type="text/css">
        <!--boostrap_css-->
        <link href="<?php echo base_url() . '/public/assets/styles/bootstrap/bootstrap.min.css' ?>"  type="text/css" rel="stylesheet" media="all">
        <!--jquery-->
        <script type="text/javascript" src="<?php echo base_url() . '/public/js/jquery.min.js'; ?>"></script>
        <!--smartwizard-js-->
        <script type="text/javascript" src="<?php echo base_url() . '/public/lib/smartwizard/js/jquery.smartWizard.js'; ?>"></script>
        <!--select2-js-->
        <script type="text/javascript" src="<?php echo base_url() . '/public/lib/select2/js/select2.full.min.js'; ?>"></script>
        <!--datatables-js-->
        <script type="text/javascript" src="<?php echo base_url() . '/public/lib/datatables/js/jquery.dataTables.min.js'; ?>"></script>
        <!--datatables-select-js-->
        <script type="text/javascript" src="<?php echo base_url() . '/public/lib/datatables/js/dataTables.select.min.js'; ?>"></script>
        <!--progressbar-->
        <script type="text/javascript" src="<?php echo base_url() . '/public/lib/progressbar/js/progressbar.min.js'; ?>"></script>
        <!--boostrap_js-->
        <script src="<?php echo base_url() . '/public/assets/scripts/bootstrap/bootstrap.min.js' ?>" type="text/javascript"></script>
        <!--migrator-js-->
        <script type="text/javascript" src="<?php echo base_url() . '/public/lib/migrator/js/migrator.js'; ?>"></script>
    </head>
    <body>
        <?php echo view('\Modules\Template\Views\\external_header_v'); ?>
        <div class="external-content" style="margin-top: 47px;">

            <table align="center" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td> 
                        <form id="migration_frm" action="<?php echo base_url() . 'migrate/editt/start_migration'; ?>" method="POST">
                            <input type='hidden' name="issubmit" value="1">
                            <!-- Tabs -->
                            <div id="wizard" class="swMain">
                                <!--Top section-->
                                <ul>
                                    <li>
                                        <a href="#step-1">
                                            <span class="stepNumber">1</span>
                                            <span class="stepDesc">
                                                Source DB<br />
                                                <small>Fill source database details</small>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#step-2">
                                            <span class="stepNumber">2</span>
                                            <span class="stepDesc">
                                                Target DB<br />
                                                <small>Fill target database details</small>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#step-3">
                                            <span class="stepNumber">3</span>
                                            <span class="stepDesc">
                                                Facility Details<br />
                                                <small>Fill your facility details</small>
                                            </span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#step-4">
                                            <span class="stepNumber">4</span>
                                            <span class="stepDesc">
                                                Table Details<br />
                                                <small>Select tables to migrate</small>
                                            </span>
                                        </a>
                                    </li>
                                </ul>
                                <!--source_details-->
                                <div id="step-1">	
                                    <h2 class="StepTitle">Step 1: Source DB Details</h2>
                                    <table cellspacing="3" cellpadding="3" align="center">
                                        <tr>
                                            <td align="center" colspan="3">&nbsp;</td>
                                        </tr>        
                                        <tr>
                                            <td align="right">Hostname :</td>
                                            <td align="left">
                                                <input type="text" id="source_hostname" name="source_hostname" value="<?php echo @$source_hostname; ?>" class="txtBox">
                                            </td>
                                            <td align="left"><span id="msg_source_hostname"></span>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Port :</td>
                                            <td align="left">
                                                <input type="text" id="source_port" name="source_port" value="<?php echo @$source_port; ?>" class="txtBox">
                                            </td>
                                            <td align="left"><span id="msg_source_port"></span>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="right">User :</td>
                                            <td align="left">
                                                <input type="text" id="source_username" name="source_username" value="<?php echo @$source_username; ?>" class="txtBox">
                                            </td>
                                            <td align="left"><span id="msg_source_username"></span>&nbsp;</td>
                                        </tr> 
                                        <tr>
                                            <td align="right">Password :</td>
                                            <td align="left">
                                                <input type="password" id="source_password" name="source_password" value="<?php echo @$source_password; ?>" class="txtBox">
                                            </td>
                                            <td align="left"><span id="msg_source_password"></span>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Database Name :</td>
                                            <td align="left">
                                                <input type="text" id="source_database" name="source_database" value="<?php echo @$source_database; ?>" class="txtBox">
                                            </td>
                                            <td align="left"><span id="msg_source_database"></span>&nbsp;</td>
                                        </tr> 
                                        <tr>
                                            <td align="right">Database Driver :</td>
                                            <td align="left">
                                                <input type="text" id="source_driver" name="source_driver" value="<?php echo @$source_driver; ?>" class="txtBox">
                                            </td>
                                            <td align="left"><span id="msg_source_driver"></span>&nbsp;</td>
                                        </tr>                                    			
                                    </table>          			
                                </div>
                                <!--target_db_details-->
                                <div id="step-2">
                                    <h2 class="StepTitle">Step 2: Target DB Details</h2>	
                                    <table cellspacing="3" cellpadding="3" align="center">
                                        <tr>
                                            <td align="center" colspan="3">&nbsp;</td>
                                        </tr>        
                                        <tr>
                                            <td align="right">Hostname :</td>
                                            <td align="left">
                                                <input type="text" id="target_hostname" name="target_hostname" value="<?php echo @$target_hostname; ?>" class="txtBox">
                                            </td>
                                            <td align="left"><span id="msg_target_hostname"></span>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Port :</td>
                                            <td align="left">
                                                <input type="text" id="target_port" name="target_port" value="<?php echo @$target_port; ?>" class="txtBox">
                                            </td>
                                            <td align="left"><span id="msg_target_port"></span>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="right">User :</td>
                                            <td align="left">
                                                <input type="text" id="target_username" name="target_username" value="<?php echo @$target_username; ?>" class="txtBox">
                                            </td>
                                            <td align="left"><span id="msg_target_username"></span>&nbsp;</td>
                                        </tr> 
                                        <tr>
                                            <td align="right">Password :</td>
                                            <td align="left">
                                                <input type="password" id="target_password" name="target_password" value="<?php echo @$target_password; ?>" class="txtBox">
                                            </td>
                                            <td align="left"><span id="msg_target_password"></span>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Database Name :</td>
                                            <td align="left">
                                                <input type="text" id="target_database" name="target_database" value="<?php echo @$target_database; ?>" class="txtBox">
                                            </td>
                                            <td align="left"><span id="msg_target_database"></span>&nbsp;</td>
                                        </tr> 
                                        <tr>
                                            <td align="right">Database Driver :</td>
                                            <td align="left">
                                                <input type="text" id="target_driver" name="target_driver" value="<?php echo @$target_driver; ?>" class="txtBox">
                                            </td>
                                            <td align="left"><span id="msg_target_driver"></span>&nbsp;</td>
                                        </tr>                                    			
                                    </table>         
                                </div>                      
                                <!--facility_details-->
                                <div id="step-3">
                                    <h2 class="StepTitle">Step 3: Facility Details</h2>	
                                    <table cellspacing="3" cellpadding="3" align="center">
                                        <tr>
                                            <td align="center" colspan="3">&nbsp;</td>
                                        </tr>        
                                        <tr>
                                            <td align="right">Facility :</td>
                                            <td align="left">
                                                <select id="facility" name="facility" class="txtBox">
                                                    <option value="">Select one</option>
                                                </select>
                                            </td>
                                            <td align="left"><span id="msg_facility"></span>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td align="right">Store :</td>
                                            <td align="left">
                                                <select id="store" name="store" class="txtBox">
                                                    <option value="">Select one</option>
                                                </select>
                                            </td>
                                            <td align="left"><span id="msg_store"></span>&nbsp;</td>
                                        </tr>          			                                 			
                                    </table>               				          
                                </div>
                                <!--table_details-->
                                <div id="step-4">
                                    <h2 class="StepTitle">Step 4: Table Details</h2>	
                                    <table cellspacing="3" cellpadding="3" align="center" width="90%">      
                                        <tr>
                                            <td align="center">
                                                <table id="migration_tbl" class="display select" cellspacing="0" width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th class="check_all_heading">
                                                                <div class="block">
                                                                    <input name="select_all" value="1" type="checkbox" class="check_all_input">
                                                                    <label>Select All</label>
                                                                </div>
                                                            </th>
                                                            <th>TableName</th>
                                                            <th>Progress</th>
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </td>
                                        </tr>                               			
                                    </table>                 			
                                </div>
                            </div><!-- </tabs>-->  		
                        </form> 		
                    </td>
                </tr>
            </table> 
        </div>
    </body>
</html>