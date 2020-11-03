<?php

/* Default route */
$routes->get('/', '\Modules\ADT\Controllers\User_management::login');
$routes->post('initialize', '\Modules\Setup\Controllers\Setup::initialize');

/* Authentication Routes */
// $routes->get('/', '\Modules\ADT\Controllers\System_management::index');
$routes->get('login', '\Modules\ADT\Controllers\User_management::login');
$routes->post('user_management/authenticate', '\Modules\ADT\Controllers\User_management::authenticate');
$routes->get('home', '\Modules\ADT\Controllers\Home_controller::home');
$routes->get('logout/(:any)', '\Modules\ADT\Controllers\User_management::logout/$1');

/* Notification Management */
$routes->get('error_notification', '\Modules\ADT\Controllers\Notification_management::error_notification');
$routes->get('reporting_notification', '\Modules\ADT\Controllers\Notification_management::reporting_notification');
$routes->get('defaulter_notification', '\Modules\ADT\Controllers\Notification_management::defaulter_notification');
$routes->get('missed_appointments_notification', '\Modules\ADT\Controllers\Notification_management::missed_appointments_notification');
$routes->get('followup_notification', '\Modules\ADT\Controllers\Notification_management::followup_notification');
$routes->get('prescriptions_notification_view', '\Modules\ADT\Controllers\Notification_management::prescriptions_notification_view');
$routes->get('update_notification', '\Modules\ADT\Controllers\Notification_management::update_notification');
$routes->get('ontime_notification', '\Modules\ADT\Controllers\Notification_management::ontime_notification');
$routes->get('missed_appointments_notification', '\Modules\ADT\Controllers\Notification_management::missed_appointments_notification');
$routes->get('followup_notification', '\Modules\ADT\Controllers\Notification_management::followup_notification');
$routes->get('prescriptions_notification_view', '\Modules\ADT\Controllers\Notification_management::prescriptions_notification_view');
$routes->get('update_notification', '\Modules\ADT\Controllers\Notification_management::update_notification');

/* Home Dashboard */
$routes->get('getExpiringDrugs/(:any)/(:any)', '\Modules\ADT\Controllers\Facilitydashboard_Management::getExpiringDrugs/$1/$2');
$routes->get('getPatientEnrolled/(:any)/(:any)', '\Modules\ADT\Controllers\Facilitydashboard_Management::getPatientEnrolled/$1/$2');
$routes->get('getExpectedPatients/(:any)/(:any)', '\Modules\ADT\Controllers\Facilitydashboard_Management::getExpectedPatients/$1/$2');
$routes->get('getStockSafetyQty', '\Modules\ADT\Controllers\Facilitydashboard_Management::getStockSafetyQty');
$routes->get('getStockSafetyQty/(:any)', '\Modules\ADT\Controllers\Facilitydashboard_Management::getStockSafetyQty/$1');
$routes->post('drillAccessLevel', '\Modules\ADT\Controllers\Admin_management::drillAccessLevel');
$routes->get('get_faq', '\Modules\ADT\Controllers\Home_controller::get_faq');
$routes->get('getPatientMasterList', '\Modules\ADT\Controllers\Facilitydashboard_Management::getPatientMasterList');

/* Admin Dashboard */
$routes->get('admin_management/getSystemUsage/(:any)', '\Modules\ADT\Controllers\Admin_management::getSystemUsage/$1');
$routes->get('admin_management/getWeeklySumary/(:any)(:any)', '\Modules\ADT\Controllers\Admin_management::getWeeklySumary/$1/2');
$routes->get('admin_management/drillAccessLevel', '\Modules\ADT\Controllers\Admin_management::drillAccessLevel');
$routes->post('admin_management/admin_management/getWeeklySumaryPerUser', '\Modules\ADT\Controllers\Admin_management::getWeeklySumaryPerUser');
$routes->post('admin_management/(:any)', '\Modules\ADT\Controllers\Admin_management::$1');
$routes->post('admin_management/inactive_users', '\Modules\ADT\Controllers\Admin_management::inactive_users');
$routes->post('admin_management/online_users', '\Modules\ADT\Controllers\Admin_management::online_users');
$routes->post('admin_management/online', '\Modules\ADT\Controllers\Admin_management::online');
$routes->post('admin_management/inactive', '\Modules\ADT\Controllers\Admin_management::inactive');


/* Inventory Stock Management */
$routes->get('stock_transaction/(:any)', '\Modules\ADT\Controllers\Inventory_management::stock_transaction/$1');
$routes->post('getAllDrugs', '\Modules\ADT\Controllers\Inventory_management::getAllDrugs');
$routes->post('getDrugDetails', '\Modules\ADT\Controllers\Inventory_management::getDrugDetails');
$routes->post('checkConnection', '\Modules\ADT\Controllers\System_management::checkConnection');
$routes->post('getStockDrugs', '\Modules\ADT\Controllers\Inventory_management::getStockDrugs');
$routes->post('getBacthes', '\Modules\ADT\Controllers\Inventory_management::getBacthes');
$routes->post('getBacthDetails', '\Modules\ADT\Controllers\Inventory_management::getBacthDetails');
$routes->post('inventory_management/save', '\Modules\ADT\Controllers\Inventory_management::save');
$routes->get('inventory_management', '\Modules\ADT\Controllers\Inventory_management::index');
$routes->get('stock_listing/(:any)', '\Modules\ADT\Controllers\Inventory_management::stock_listing/$1');
$routes->get('getDrugBinCard/(:any)/(:any)', '\Modules\ADT\Controllers\Inventory_management::getDrugBinCard/$1/$2');
$routes->get('getDrugTransactions/(:any)/(:any)', '\Modules\ADT\Controllers\Inventory_management::getDrugTransactions/$1/$2');
$routes->get('inventory_management/getIsoniazid/(:any)', '\Modules\ADT\Controllers\Inventory_management::getIsoniazid');
$routes->post('inventory_management/getAllDrugsBatches/(:any)', '\Modules\ADT\Controllers\Inventory_management::getAllDrugsBatches/(:any)');
$routes->post('inventory_management/getAllBacthDetails', '\Modules\ADT\Controllers\Inventory_management::getAllBacthDetails');
$routes->get('inventory_management/serverStatus', '\Modules\ADT\Controllers\Inventory_management::serverStatus');

$routes->get('settings_management', '\Modules\ADT\Controllers\Settings_management::index');
$routes->post('settings/save/(:any)', '\Modules\ADT\Controllers\Settings::save/$1');
$routes->post('settings/update/(:any)', '\Modules\ADT\Controllers\Settings::update/$1');
$routes->get('settings/disable/(:any)/(:any)', '\Modules\ADT\Controllers\Settings::disable/$1/$2');
$routes->get('settings/enable/(:any)/(:any)', '\Modules\ADT\Controllers\Settings::enable/$1/$2');
/* Regimens */
$routes->get('regimen_management', '\Modules\ADT\Controllers\Regimen_management::index', ['as' => 'regimens']);
$routes->get('settings/listing/(:any)', '\Modules\ADT\Controllers\Settings::listing/$1');
$routes->get('regimen_drug_management', '\Modules\ADT\Controllers\Regimen_drug_management::listing');
$routes->get('regimenchange_management', '\Modules\ADT\Controllers\Regimenchange_management::listing');
$routes->post('regimen_management/save', '\Modules\ADT\Controllers\Regimen_management::save');
$routes->post('regimen_management/disable/(:any)', '\Modules\ADT\Controllers\Regimen_management::disable/$1');
$routes->post('regimen_management/enable/(:any)', '\Modules\ADT\Controllers\Regimen_management::enable/$1');
$routes->get('regimen_management/getNonMappedRegimens/(:any)', '\Modules\ADT\Controllers\Regimen_management::getNonMappedRegimens/$1');
$routes->post('regimen_management/updateBulkMapping', '\Modules\ADT\Controllers\Regimen_management::updateBulkMapping');
$routes->post('regimen_management/merge/(:any)', '\Modules\ADT\Controllers\Regimen_management::merge/$1');
$routes->post('regimen_management/unmerge/(:any)', '\Modules\ADT\Controllers\Regimen_management::unmerge/$1');
$routes->get('regimen_management/edit', '\Modules\ADT\Controllers\Regimen_management::unmerge/$1');
$routes->get('regimen_management/update', '\Modules\ADT\Controllers\Regimen_management::update');


$routes->post('regimenchange_management/save', '\Modules\ADT\Controllers\Regimenchange_management::save');
$routes->post('regimenchange_management/update', '\Modules\ADT\Controllers\Regimenchange_management::update');
$routes->get('regimenchange_management/disable/(:any)', '\Modules\ADT\Controllers\Regimenchange_management::disable/$1');
$routes->get('regimenchange_management/enable/(:any)', '\Modules\ADT\Controllers\Regimenchange_management::enable/$1');


$routes->post('drugcode_classification/save', '\Modules\ADT\Controllers\Drugcode_classification::save');
$routes->post('drugcode_classification/update', '\Modules\ADT\Controllers\Drugcode_classification::update');
$routes->get('drugcode_classification/disable/(:any)', '\Modules\ADT\Controllers\Drugcode_classification::disable/$1');
$routes->get('drugcode_classification/enable/(:any)', '\Modules\ADT\Controllers\Drugcode_classification::enable/$1');

$routes->get('drugcode_management/add', '\Modules\ADT\Controllers\Drugcode_management::add');
$routes->get('drugcode_management/edit', '\Modules\ADT\Controllers\Drugcode_management::edit');
$routes->post('drugcode_management/merge/(:any)', '\Modules\ADT\Controllers\Drugcode_management::merge/$1');
$routes->get('drugcode_management/disable/(:any)', '\Modules\ADT\Controllers\Drugcode_management::disable/$1');
$routes->get('drugcode_management/enable', '\Modules\ADT\Controllers\Drugcode_management::enable');
$routes->post('drugcode_management/save', '\Modules\ADT\Controllers\Drugcode_management::save');


$routes->post('dose_management/add', '\Modules\ADT\Controllers\Dose_management::add');
$routes->post('dose_management/edit', '\Modules\ADT\Controllers\Dose_management::edit');
$routes->get('dose_management/enable/(:any)', '\Modules\ADT\Controllers\Dose_management::enable/$1');
$routes->get('dose_management/disable/(:any)', '\Modules\ADT\Controllers\Dose_management::disable/$1');
$routes->post('dose_management/update', '\Modules\ADT\Controllers\Dose_management::update');
$routes->post('dose_management/save', '\Modules\ADT\Controllers\Dose_management::save');


$routes->get('dossing_chart/add', '\Modules\ADT\Controllers\Dossing_chart::add');
$routes->get('dossing_chart/edit', '\Modules\ADT\Controllers\Dossing_chart::edit');
$routes->post('dossing_chart/merge/(:any)', '\Modules\ADT\Controllers\Dossing_chart::merge/$1');
$routes->get('dossing_chart/disable/(:any)', '\Modules\ADT\Controllers\Dossing_chart::disable/$1');
$routes->get('dossing_chart/enable', '\Modules\ADT\Controllers\Dossing_chart::enable');
$routes->post('dossing_chart/save', '\Modules\ADT\Controllers\Dossing_chart::save');

$routes->post('indication_management/add', '\Modules\ADT\Controllers\Indication_management::add');
$routes->post('indication_management/edit', '\Modules\ADT\Controllers\Indication_management::edit');
$routes->get('indication_management/enable/(:any)', '\Modules\ADT\Controllers\Indication_management::enable/$1');
$routes->get('indication_management/disable/(:any)', '\Modules\ADT\Controllers\Indication_management::disable/$1');
$routes->post('indication_management/update', '\Modules\ADT\Controllers\Indication_management::update');
$routes->post('indication_management/save', '\Modules\ADT\Controllers\Indication_management::save');

$routes->post('drugsource_management/add', '\Modules\ADT\Controllers\Drugsource_management::add');
$routes->post('drugsource_management/edit', '\Modules\ADT\Controllers\Drugsource_management::edit');
$routes->get('drugsource_management/enable/(:any)', '\Modules\ADT\Controllers\Drugsource_management::enable/$1');
$routes->get('drugsource_management/disable/(:any)', '\Modules\ADT\Controllers\Drugsource_management::disable/$1');
$routes->post('drugsource_management/update', '\Modules\ADT\Controllers\Drugsource_management::update');
$routes->post('drugsource_management/save', '\Modules\ADT\Controllers\Drugsource_management::save');

$routes->post('drugdestination_management/add', '\Modules\ADT\Controllers\Drugdestination_management::add');
$routes->post('drugdestination_management/edit', '\Modules\ADT\Controllers\Drugdestination_management::edit');
$routes->get('drugdestination_management/enable/(:any)', '\Modules\ADT\Controllers\Drugdestination_management::enable/$1');
$routes->get('drugdestination_management/disable/(:any)', '\Modules\ADT\Controllers\Drugdestination_management::disable/$1');
$routes->post('drugdestination_management/update', '\Modules\ADT\Controllers\Drugdestination_management::update');
$routes->post('drugdestination_management/save', '\Modules\ADT\Controllers\Drugdestination_management::save');

$routes->post('genericname_management/add', '\Modules\ADT\Controllers\Genericname_management::add');
$routes->post('genericname_management/edit', '\Modules\ADT\Controllers\Genericname_management::edit');
$routes->get('genericname_management/enable/(:any)', '\Modules\ADT\Controllers\Genericname_management::enable/$1');
$routes->get('genericname_management/disable/(:any)', '\Modules\ADT\Controllers\Genericname_management::disable/$1');
$routes->post('genericname_management/update', '\Modules\ADT\Controllers\Genericname_management::update');
$routes->post('genericname_management/save', '\Modules\ADT\Controllers\Genericname_management::save');

$routes->post('brandname_management/add', '\Modules\ADT\Controllers\Brandname_management::add');
$routes->post('brandname_management/edit', '\Modules\ADT\Controllers\Brandname_management::edit');
$routes->get('brandname_management/enable/(:any)', '\Modules\ADT\Controllers\Brandname_management::enable/$1');
$routes->get('brandname_management/disable/(:any)', '\Modules\ADT\Controllers\Brandname_management::disable/$1');
$routes->get('brandname_management/delete/(:any)', '\Modules\ADT\Controllers\Brandname_management::delete/$1');
$routes->post('brandname_management/update', '\Modules\ADT\Controllers\Brandname_management::update');
$routes->post('brandname_management/save', '\Modules\ADT\Controllers\Brandname_management::save');

$routes->post('client_management/add', '\Modules\ADT\Controllers\Client_management::add');
$routes->post('client_management/edit', '\Modules\ADT\Controllers\Client_management::edit');
$routes->get('client_management/enable/(:any)', '\Modules\ADT\Controllers\Client_management::enable/$1');
$routes->get('client_management/disable/(:any)', '\Modules\ADT\Controllers\Client_management::disable/$1');
$routes->get('client_management/delete/(:any)', '\Modules\ADT\Controllers\Client_management::delete/$1');
$routes->post('client_management/update', '\Modules\ADT\Controllers\Client_management::update');
$routes->post('client_management/save', '\Modules\ADT\Controllers\Client_management::save');


$routes->post('Nonadherence_Management/add', '\Modules\ADT\Controllers\Nonadherence_management::add');
$routes->post('Nonadherence_Management/edit', '\Modules\ADT\Controllers\Nonadherence_management::edit');
$routes->get('Nonadherence_Management/enable/(:any)', '\Modules\ADT\Controllers\Nonadherence_management::enable/$1');
$routes->get('Nonadherence_Management/disable/(:any)', '\Modules\ADT\Controllers\Nonadherence_management::disable/$1');
$routes->get('Nonadherence_Management/delete/(:any)', '\Modules\ADT\Controllers\Nonadherence_management::delete/$1');
$routes->post('nonadherence_management/update', '\Modules\ADT\Controllers\Nonadherence_management::update');
$routes->post('nonadherence_management/save', '\Modules\ADT\Controllers\Nonadherence_management::save');


$routes->get('visit_management', '\Modules\ADT\Controllers\Visit_management::index');
$routes->post('visit_management/add', '\Modules\ADT\Controllers\Visit_management::add');
$routes->post('visit_management/edit', '\Modules\ADT\Controllers\Visit_management::edit');
$routes->get('visit_management/enable/(:any)', '\Modules\ADT\Controllers\Visit_management::enable/$1');
$routes->get('visit_management/disable/(:any)', '\Modules\ADT\Controllers\Visit_management::disable/$1');
$routes->get('visit_management/delete/(:any)', '\Modules\ADT\Controllers\Visit_management::delete/$1');
$routes->post('visit_management/update', '\Modules\ADT\Controllers\Visit_management::update');
$routes->post('visit_management/save', '\Modules\ADT\Controllers\Visit_management::save');


$routes->get('viral_load_manual', '\Modules\ADT\Controllers\Viral_load_manual::index');
$routes->post('viral_load_manual/get_patient_ccc_number', '\Modules\ADT\Controllers\Viral_load_manual::get_patient_ccc_number');
$routes->get('viral_load_manual/get_viral_load', '\Modules\ADT\Controllers\Viral_load_manual::get_viral_load');
$routes->post('viral_load_manual/update', '\Modules\ADT\Controllers\Viral_load_manual::update');


$routes->post('drug_stock_balance_sync/getRunningBalance', '\Modules\ADT\Controllers\Drug_stock_balance_sync::getRunningBalance');
$routes->post('drug_stock_balance_sync/synch_balance', '\Modules\ADT\Controllers\Drug_stock_balance_sync::synch_balance');

$routes->post('dossing_chart_drugs/get_drugs', '\Modules\ADT\Controllers\Dossing_chart::get_drugs');
$routes->post('dossing_chart_dose/get_dose', '\Modules\ADT\Controllers\Dossing_chart::get_dose');

$routes->get('order_settings/fetch/(:any)', '\Modules\ADT\Controllers\Order_settings::fetch/$1');
$routes->get('order_settings/fetch/(:any)', '\Modules\ADT\Controllers\Order_settings::fetch/$1');
$routes->get('order_settings/fetch/(:any)', '\Modules\ADT\Controllers\Order_settings::fetch/$1');
$routes->get('order_settings/fetch/(:any)', '\Modules\ADT\Controllers\Order_settings::fetch/$1');
$routes->get('order_settings/fetch/(:any)', '\Modules\ADT\Controllers\Order_settings::fetch/$1');
$routes->get('order_settings/get_details/(:any)/(:any)', '\Modules\ADT\Controllers\Order_settings::get_details/$1/$2');
$routes->post('order_settings/save/(:any)', '\Modules\ADT\Controllers\Order_settings::save/$1');
$routes->post('order_settings/update/(:any)/(:any)', '\Modules\ADT\Controllers\Order_settings::update/$1/$2');
$routes->get('order_settings/disable/(:any)/(:any)', '\Modules\ADT\Controllers\Order_settings::disable/$1/$2');
$routes->get('order_settings/enable/(:any)/(:any)', '\Modules\ADT\Controllers\Order_settings::enable/$1/$2');


/* Pharmacovigilance */
$routes->get('pqmp/(:any)/(:any)', '\Modules\ADT\Controllers\Inventory_management::pqmp/$1/$2');
$routes->get('adr/(:any)/(:any)', '\Modules\ADT\Controllers\Inventory_management::adr/$1/$2');
$routes->get('inventory_management/adr/(:any)', '\Modules\ADT\Controllers\Inventory_management::adr/$1');

$routes->get('adr_view/adr/(:any)', '\Modules\ADT\Controllers\Inventory_management::adr_view/$1');

$routes->post('inventory_management/save_pqm_for_synch', '\Modules\ADT\Controllers\Inventory_management::save_pqm_for_synch');
$routes->post('inventory_management/adr/save/(:any)', '\Modules\ADT\Controllers\Inventory_management::adr_view/$1');
$routes->get('inventory_management/loadRecord/(:any)', '\Modules\ADT\Controllers\Inventory_management::loadRecord/$1');
$routes->post('inventory_management/export_pqmp/(:any)/(:any)', '\Modules\ADT\Controllers\Inventory_management::export_pqmp/$1/$2');
$routes->get('inventory_management/deladr/(:any)/delete', '\Modules\ADT\Controllers\Inventory_management::deleteAdr/$1/$2');

$routes->post('inventory_management/adr/(:any)', '\Modules\ADT\Controllers\Inventory_management::adr/$1');
$routes->get('inventory_management/loadAdrRecord/(:any)', '\Modules\ADT\Controllers\Inventory_management::loadAdrRecord/$1');
$routes->post('inventory_management/export_adr/(:any)/(:any)', '\Modules\ADT\Controllers\Inventory_management::export_adr/$1/$2');



//$routes->post('user_management/profile_update', '\Modules\ADT\Controllers\Inventory_management::profile_update/$1/$2');




/* Drugs */
$routes->get('drugcode_classification', '\Modules\ADT\Controllers\Drugcode_classification::listing');
$routes->get('drugcode_management', '\Modules\ADT\Controllers\Drugcode_management::listing');
$routes->get('dose_management', '\Modules\ADT\Controllers\Dose_management::listing');
$routes->get('indication_management', '\Modules\ADT\Controllers\Indication_management::listing');
$routes->get('drugsource_management', '\Modules\ADT\Controllers\Drugsource_management::listing');
$routes->get('drugdestination_management', '\Modules\ADT\Controllers\Drugdestination_management::listing');
$routes->get('genericname_management', '\Modules\ADT\Controllers\Genericname_management::listing');
$routes->get('brandname_management', '\Modules\ADT\Controllers\Brandname_management::index');
$routes->get('drug_stock_balance_sync/(:any)', '\Modules\ADT\Controllers\Drug_stock_balance_sync::view_balance/$1');
$routes->get('dossing_chart', '\Modules\ADT\Controllers\Dossing_chart::index');
//$routes->get('dossing_chart_drugs', '\Modules\ADT\Controllers\Dossing_chart::index');
//$routes->get('dossing_chart_dose', '\Modules\ADT\Controllers\Dossing_chart::index');
$routes->get('update_balances', '\Modules\ADT\Controllers\Drug_stock_balance_sync::update_balances');

/* Settings Management */
$routes->get('settings_management', '\Modules\ADT\Controllers\Settings_management::index');
$routes->match(['get', 'post'], 'settings_management/getActiveAccessLevels', '\Modules\ADT\Controllers\Settings_management::getActiveAccessLevels');
$routes->match(['get', 'post'], 'settings_management/getMenus', '\Modules\ADT\Controllers\Settings_management::getMenus');

/* Auto Management */
//$routes->match(['get', 'post'], 'auto_management', '\Modules\ADT\Controllers\Auto_management::index');
$routes->get('auto_management/get_viral_load/(:any)', '\Modules\ADT\Controllers\Auto_management::get_viral_load/$1');

/* Facilities */
$routes->get('facility_management', '\Modules\ADT\Controllers\Facility_Management::index');
$routes->match(['get', 'post'], 'facility_management/getCurrent', '\Modules\ADT\Controllers\Facility_Management::getCurrent');
$routes->get('client_management', '\Modules\ADT\Controllers\Client_management::index');
$routes->post('facility_management/update', '\Modules\ADT\Controllers\Facility_Management::update');
$routes->get('order_settings/listing/(:any)', '\Modules\ADT\Controllers\Order_settings::listing/$1');
$routes->get('facilitydashboard_management/getPatientMasterList', '\Modules\ADT\Controllers\Facilitydashboard_Management::getPatientMasterList');

/* Others */
$routes->get('nonadherence_management', '\Modules\ADT\Controllers\Nonadherence_management::index');
$routes->get('patient_management/merge_list', '\Modules\ADT\Controllers\Patient_management::merge_list');
$routes->get('getPatientMergeList', '\Modules\ADT\Controllers\Patient_management::getPatientMergeList');


/* User Management */
$routes->get('user_management', '\Modules\ADT\Controllers\User_management::index');
$routes->get('user_management/get_stores', '\Modules\ADT\Controllers\User_management::get_stores');
$routes->get('user_management/edit', '\Modules\ADT\Controllers\User_management::edit_');
$routes->get('user_management/enable/(:any)', '\Modules\ADT\Controllers\User_management::enable/$1');
$routes->get('user_management/save', '\Modules\ADT\Controllers\User_management::save');
$routes->post('user_management/profile_update', '\Modules\ADT\Controllers\User_management::profile_update');
$routes->get('user_management/get_sites/(:any)', '\Modules\ADT\Controllers\User_management::get_sites/$1');
$routes->post('user_management/resend_password', '\Modules\ADT\Controllers\User_management::resendPassword');

/* Report Management */
$routes->group('report_management', ['namespace' => '\Modules\ADT\Controllers'], function ($routes) {
    $routes->get('/', 'Report_management::index');

    //Standard Reports
    $routes->get('patient_enrolled/(:any)/(:any)/(:any)', 'Report_management::patient_enrolled/$1/$2/$3');
    $routes->get('getStartedonART/(:any)/(:any)/(:any)', 'Report_management::getStartedonART/$1/$2/$3');
    $routes->get('graph_patients_enrolled_in_year/(:any)', 'Report_management::graph_patients_enrolled_in_year/$1');
    $routes->get('cumulative_patients/(:any)', 'Report_management::cumulative_patients/$1');
    $routes->get('cumulative_patients/(:any)/(:any)', 'Report_management::cumulative_patients/$1/$2');
    $routes->get('all_service_statistics/(:any)', 'Report_management::all_service_statistics/$1');
    $routes->get('getFamilyPlanning/(:any)', 'Report_management::getFamilyPlanning/$1');
    $routes->get('getIndications/(:any)/(:any)', 'Report_management::getIndications/$1/$2');
    $routes->get('getTBPatients/(:any)/(:any)', 'Report_management::getTBPatients/$1/$2');
    $routes->get('getnonisoniazidPatients/(:any)', 'Report_management::getnonisoniazidPatients/$1');
    $routes->get('disclosure_chart/(:any)/(:any)', 'Report_management::disclosure_chart/$1/$2');
    $routes->get('getADR/(:any)', 'Report_management::getADR/$1');
    $routes->get('getChronic/(:any)', 'Report_management::getChronic/$1');
    $routes->get('patients_disclosure/(:any)/(:any)', 'Report_management::patients_disclosure/$1/$2');
    $routes->get('getisoniazidPatients/(:any)/(:any)', 'Report_management::getisoniazidPatients/$1/$2');
    $routes->get('getMMDMMS/(:any)', 'Report_management::getMMDMMS/$1');
    $routes->get('getDrugs', 'Report_management::getDrugs');
    $routes->get('dispensingReport/(:any)/(:any)', 'Report_management::dispensingReport/$1/$2');
    $routes->get('getScheduledPatients/(:any)/(:any)/(:any)/(:any)', 'Report_management::getScheduledPatients/$1/$2/$3/$4');
    $routes->get('getPatientsStartedonDate/(:any)/(:any)', 'Report_management::getPatientsStartedonDate/$1/$2');
    $routes->get('getPatientMissingAppointments/(:any)/(:any)', 'Report_management::getPatientMissingAppointments/$1/$2');
    $routes->get('getPatientsforRefill/(:any)/(:any)', 'Report_management::getPatientsforRefill/$1/$2');
    $routes->get('get_viral_load_results/(:any)/(:any)', 'Report_management::get_viral_load_results/$1/$2');
    $routes->get('getPatientList/(:any)/(:any)/(:any)', 'Report_management::getPatientList/$1/$2/$3');
    $routes->get('getScheduledPatients/(:any)/(:any)', 'Report_management::getScheduledPatients/$1/$2');

    $routes->get('get_pep_reasons_patients/(:any)/(:any)', 'Report_management::get_pep_reasons_patients/$1/$2');
    $routes->get('get_pep_reasons/(:any)/(:any)', 'Report_management::get_pep_reasons/$1/$2');
    $routes->get('get_prep_patients/(:any)/(:any)', 'Report_management::get_prep_patients/$1/$2');
    $routes->get('get_prep_reasons/(:any)/(:any)', 'Report_management::get_prep_reasons/$1/$2');
    $routes->get('get_prep_reasons_patients/(:any)/(:any)', 'Report_management::get_prep_reasons_patients/$1/$2');
    //Visting Patients
    $routes->get('distribution_refill/(:any)', 'Report_management::distribution_refill/$1');
    $routes->get('getRefillDistributionPatients/(:any)/(:any)', 'Report_management::getRefillDistributionPatients/$1/$2');
    $routes->get('getPatientsStartedonDateDiffCare/(:any)/(:any)', 'Report_management::getPatientsStartedonDateDiffCare/$1/$2');
    $routes->get('getScheduledPatientsDiffCare/(:any)/(:any)', 'Report_management::getScheduledPatientsDiffCare/$1/$2');
    $routes->get('getPatientsOnDiffCare/(:any)/(:any)', 'Report_management::getPatientsOnDiffCare/$1/$2');
    //Differentiated Care

    $routes->get('patients_nonadherence/(:any)/(:any)', 'Report_management::patients_nonadherence/$1/$2');
    $routes->get('getAdherence/(:any)/(:any)/(:any)/(:any)', 'Report_management::getAdherence/$1/$2/$3/$4');
    $routes->get('patients_switched_to_second_line_regimen/(:any)/(:any)', 'Report_management::patients_switched_to_second_line_regimen/$1/$2');
    //Early Warning Indicators

    $routes->get('getPatientsforRefillDiffCare/(:any)/(:any)', 'Report_management::getPatientsforRefillDiffCare/$1/$2');
    $routes->get('patients_who_changed_regimen/(:any)/(:any)', 'Report_management::patients_who_changed_regimen/$1/$2');
    $routes->get('patients_starting/(:any)/(:any)', 'Report_management::patients_starting/$1/$2');
    $routes->get('graphical_adherence/(:any)/(:any)/(:any)', 'Report_management::graphical_adherence/$1/$2/$3');
    $routes->get('patient_consumption/(:any)/(:any)', 'Report_management::patient_consumption/$1/$2');
    $routes->get('drug_stock_on_hand/(:any)', 'Report_management::drug_stock_on_hand/$1');
    $routes->get('getMoreHelp/(:any)/(:any)/(:any)', 'Report_management::getMoreHelp/$1/$2/$3');

    $routes->get('get_lost_followup/(:any)/(:any)', 'Report_management::get_lost_followup/$1/$2');
    $routes->get('get_viral_loadsummary/(:any)', 'Report_management::get_viral_loadsummary/$1');
    //Drug Inventory
    $routes->get('drug_consumption/(:any)/(:any)', 'Report_management::drug_consumption/$1/$2');
    $routes->get('stock_report/(:any)/(:any)/(:any)', 'Report_management::stock_report/$1/$2/$3');
    $routes->get('getDrugsIssued/(:any)/(:any)/(:any)', 'Report_management::getDrugsIssued/$1/$2/$3');
    $routes->get('expired_drugs/(:any)', 'Report_management::expired_drugs/$1');
    $routes->get('expiring_drugs/(:any)', 'Report_management::expiring_drugs/$1');
    $routes->get('stock_report/(:any)/(:any)/(:any)/(:any)', 'Report_management::stock_report/$1/$2/$3/$4');
    $routes->get('getFacilityConsumption/(:any)/(:any)', 'Report_management::getFacilityConsumption/$1/$2');
    $routes->get('getDailyConsumption/(:any)/(:any)', 'Report_management::getDailyConsumption/$1/$2');
    $routes->get('getMOHForm/(:any)/(:any)/(:any)', 'Report_management::getMOHForm/$1/$2/$3');
    //MOH Forms
    $routes->get('getDrugsReceived/(:any)/(:any)/(:any)', 'Report_management::getDrugsReceived/$1/$2/$3');


    //Guidelines
});
$routes->get('load_guidelines_view', 'Report_management::load_guidelines_view');

/* Manual/AutoScript */
/* Manual/AutoScript */
$routes->get('auto_management/index/(:any)', '\Modules\ADT\Controllers\Auto_management::index/$1');
$routes->post('regimen_drug_management/save', '\Modules\ADT\Controllers\Regimen_drug_management::save');
$routes->get('regimen_drug_management/enable/(:any)', '\Modules\ADT\Controllers\Regimen_drug_management::enable/$1');
$routes->get('regimen_drug_management/disable/(:any)', '\Modules\ADT\Controllers\Regimen_drug_management::disable/$1');

/* Patients */
$routes->addRedirect('patient_management', 'public/patients');
$routes->addRedirect('addpatient_show', 'public/patients/add');
$routes->get('patients', '\Modules\ADT\Controllers\Patient_management::index');
$routes->get('patients/listing', '\Modules\ADT\Controllers\Patient_management::listing');
$routes->get('patients/add', '\Modules\ADT\Controllers\Patient_management::addpatient_show');
$routes->match(['get', 'post'], 'patient/save', '\Modules\ADT\Controllers\Patient_management::save');
$routes->match(['get', 'post'], 'patient/delete/(:any)', '\Modules\ADT\Controllers\Patient_management::delete/$1');
$routes->get('patient/disable/(:any)', '\Modules\ADT\Controllers\Patient_management::disable/$1');
$routes->get('patient/enable/(:any)', '\Modules\ADT\Controllers\Patient_management::enable/$1');
$routes->get('patient/edit/(:any)', '\Modules\ADT\Controllers\Patient_management::edit');
$routes->post('patient/update/(:any)', '\Modules\ADT\Controllers\Patient_management::update');
$routes->get('get-patients', '\Modules\ADT\Controllers\Patient_management::get_patients');
$routes->get('get-patients/(:any)', '\Modules\ADT\Controllers\Patient_management::get_patients/$1');
$routes->get('patient/load_view/(:any)/(:any)', '\Modules\ADT\Controllers\Patient_management::load_view');
$routes->get('patient/load_patient/(:any)', '\Modules\ADT\Controllers\Patient_management::load_patient');
$routes->get('patient/get_visits/(:any)', '\Modules\ADT\Controllers\Patient_management::get_visits');
$routes->get('patient/load_form/(:any)', '\Modules\ADT\Controllers\Patient_management::load_form');
$routes->get('patient/load_summary/(:any)', '\Modules\ADT\Controllers\Patient_management::load_summary');
$routes->get('patient/checkpatient_no/(:any)', '\Modules\ADT\Controllers\Patient_management::checkpatient_no');
$routes->match(['get', 'post'], 'patient/getSixMonthsDispensing/(:any)', '\Modules\ADT\Controllers\Patient_management::getSixMonthsDispensing/$1');
$routes->match(['get', 'post'], 'patient/getRegimenChange/(:any)', '\Modules\ADT\Controllers\Patient_management::getRegimenChange/$1');
$routes->match(['get', 'post'], 'patient/getAppointmentHistory/(:any)', '\Modules\ADT\Controllers\Patient_management::getAppointmentHistory/$1');
$routes->get('patient/get_Last_vl_result/(:any)', '\Modules\ADT\Controllers\Patient_management::get_Last_vl_result/$1');
$routes->match(['get', 'post'], 'patient/get_viral_load_info/(:any)', '\Modules\ADT\Controllers\Patient_management::get_viral_load_info');
$routes->get('patient/viewDetails/(:any)', '\Modules\ADT\Controllers\Patient_management::viewDetails');
$routes->get('patient/requiredFields/(:any)', '\Modules\ADT\Controllers\Patient_management::requiredFields');
$routes->match(['get', 'post'], 'patient/get_patient_details', '\Modules\ADT\Controllers\Patient_management::get_patient_details');
$routes->match(['get', 'post'], 'patient/getAppointments/(:any)', '\Modules\ADT\Controllers\Patient_management::getAppointments');
$routes->match(['get', 'post'], 'patient/getWhoStage', '\Modules\ADT\Controllers\Patient_management::getWhoStage');

/* Dispensement */
$routes->get('dispensement_management/dispense/(:any)', '\Modules\ADT\Controllers\Dispensement_management::dispense');
$routes->match(['get', 'post'], 'dispensement_management/save', '\Modules\ADT\Controllers\Dispensement_management::save');
$routes->get('dispensement_management/edit/(:any)', '\Modules\ADT\Controllers\Dispensement_management::edit');
$routes->get('dispensement_management/getPrescriptions/(:any)', '\Modules\ADT\Controllers\Dispensement_management::getPrescriptions');
$routes->match(['get', 'post'], 'dispensement_management/get_other_dispensing_details', '\Modules\ADT\Controllers\Dispensement_management::get_other_dispensing_details');
$routes->get('dispensement_management/get_patient_data/(:any)', '\Modules\ADT\Controllers\Dispensement_management::get_patient_data');
$routes->match(['get', 'post'], 'dispensement_management/getDrugsRegimens', '\Modules\ADT\Controllers\Dispensement_management::getDrugsRegimens');
$routes->match(['get', 'post'], 'dispensement_management/drugAllergies', '\Modules\ADT\Controllers\Dispensement_management::drugAllergies');
$routes->post('dispensement_management/getIndications', '\Modules\ADT\Controllers\Dispensement_management::getIndications');
$routes->post('dispensement_management/getInstructions/(:any)', '\Modules\ADT\Controllers\Dispensement_management::getInstructions');
$routes->post('dispensement_management/getBrands', '\Modules\ADT\Controllers\Dispensement_management::getBrands');
$routes->match(['get', 'post'], 'dispensement_management/getDrugDose/(:any)', '\Modules\ADT\Controllers\Dispensement_management::getDrugDose');
$routes->post('dispensement_management/getDoses', '\Modules\ADT\Controllers\Dispensement_management::getDoses');
$routes->post('dispensement_management/getPreviouslyDispensedDrugs', '\Modules\ADT\Controllers\Dispensement_management::getPreviouslyDispensedDrugs');
$routes->post('dispensement_management/print_test', '\Modules\ADT\Controllers\Dispensement_management::print_test');
$routes->post('dispensement_management/getFacililtyAge', '\Modules\ADT\Controllers\Dispensement_management::getFacililtyAge');
$routes->post('patient_management/merge', '\Modules\ADT\Controllers\Patient_management::merge');
$routes->post('patient_management/unmerge', '\Modules\ADT\Controllers\Patient_management::unmerge');

$routes->match(['get', 'post'], 'regimen_management/getAllDrugs', '\Modules\ADT\Controllers\Regimen_management::getAllDrugs');
$routes->match(['get', 'post'], 'regimen_management/getFilteredRegiments', '\Modules\ADT\Controllers\Regimen_management::getFilteredRegiments');
$routes->post('regimen_management/getRegimenLine/(:any)/(:any)', '\Modules\ADT\Controllers\Regimen_management::getRegimenLine');
$routes->match(['get', 'post'], 'regimen_management/getRegimenLine/(:any)', '\Modules\ADT\Controllers\Regimen_management::getRegimenLine');
/* Regimens */
$routes->post('order/authenticate_user', '\Modules\ADT\Controllers\Order::authenticate_user');
$routes->get('order', '\Modules\ADT\Controllers\Order::index');
/* Order */

$routes->match(['get', 'post'], 'order/create_order/(:any)/(:any)', '\Modules\ADT\Controllers\Order::create_order');
$routes->post('order/save/(:any)/(:any)', '\Modules\ADT\Controllers\Order::save');
$routes->post('order/save/(:any)/(:any)/(:any)', '\Modules\ADT\Controllers\Order::save');
$routes->get('order/read_order/(:any)/(:any)', '\Modules\ADT\Controllers\Order::read_order');
$routes->get('order/view_order/(:any)/(:any)', '\Modules\ADT\Controllers\Order::view_order');
$routes->get('order/logout', '\Modules\ADT\Controllers\Order::logout');
$routes->match(['get', 'post'], 'order/update_order/(:any)/(:any)', '\Modules\ADT\Controllers\Order::update_order');


$routes->get('order/download_order/(:any)/(:any)', '\Modules\ADT\Controllers\Order::download_order');
$routes->post('order/getCentralDataMaps/(:any)/(:any)/(:any)', '\Modules\ADT\Controllers\Order::getCentralDataMaps');
$routes->match(['get', 'post'], 'order/getoiPatients', '\Modules\ADT\Controllers\Order::getoiPatients');
$routes->post('order/getNotMappedRegimenPatients/(:any)/(:any)', '\Modules\ADT\Controllers\Order::getNotMappedRegimenPatients');
$routes->match(['get', 'post'], 'order/getItems', '\Modules\ADT\Controllers\Order::getItems');
$routes->get('order/get_dhis_data/(:any)', '\Modules\ADT\Controllers\Order::get_dhis_data/(:any)');
/** Order Settings */
$routes->get('order_settings/fetch/(:any)', '\Modules\ADT\Controllers\Order_settings::fetch');
$routes->post('order/getExpectedActualReport', '\Modules\ADT\Controllers\Order::getExpectedActualReport');
$routes->match(['get', 'post'], 'order/get_aggregated_fmaps/(:any)/(:any)', '\Modules\ADT\Controllers\Order::get_aggregated_fmaps');
$routes->post('order/getPeriodRegimenPatients/(:any)/(:any)', '\Modules\ADT\Controllers\Order::getPeriodRegimenPatients');
/** Order Settings */
$routes->get('admin_management/inactive_users', '\Modules\ADT\Controllers\Admin_management::inactive_users');
$routes->get('admin_management/online_users', '\Modules\ADT\Controllers\Admin_management::online_users');
$routes->get('admin_management/addCounty', '\Modules\ADT\Controllers\Admin_management::addCounty');


$routes->get('github', '\Modules\Github\Controllers\Github::index');
$routes->get('help', '\Modules\Help\Controllers\Help::index');
$routes->get('help', '\Modules\Help\Controllers\Help::index');


