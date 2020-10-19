<?php
/*Default route*/
$routes->get('/', '\Modules\ADT\Controllers\User_management::login');

/*Authentication Routes*/
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
/* Settings Management */
$routes->get('settings_management', '\Modules\ADT\Controllers\Settings_management::index');

/* Regimens */
$routes->get('regimen_management', '\Modules\ADT\Controllers\Regimen_management::index');
$routes->get('settings/listing/(:any)', '\Modules\ADT\Controllers\Settings::listing/$1');
$routes->get('regimen_drug_management', '\Modules\ADT\Controllers\Regimen_drug_management::listing');
$routes->get('regimenchange_management', '\Modules\ADT\Controllers\Regimenchange_management::listing');

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
$routes->get('dossing_chart_drugs', '\Modules\ADT\Controllers\Dossing_chart::index');
$routes->get('dossing_chart_dose', '\Modules\ADT\Controllers\Dossing_chart::index');
$routes->get('update_balances', '\Modules\ADT\Controllers\Drug_stock_balance_sync::update_balances');

/* Facilities */
$routes->get('facility_management', '\Modules\ADT\Controllers\Facility_Management::index');
$routes->get('client_management', '\Modules\ADT\Controllers\Client_management::index');
$routes->get('order_settings/listing/(:any)', '\Modules\ADT\Controllers\Order_settings::listing/$1');

/* Others */
$routes->get('nonadherence_management', '\Modules\ADT\Controllers\Nonadherence_management::index');
$routes->get('patient_management/merge_list', '\Modules\ADT\Controllers\Patient_management::merge_list');
$routes->get('getPatientMergeList', '\Modules\ADT\Controllers\Patient_management::getPatientMergeList');


/* User Management */
$routes->get('user_management', '\Modules\ADT\Controllers\User_management::index');
$routes->get('user_management/get_stores', '\Modules\ADT\Controllers\User_management::get_stores');

/* Report Management*/
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
  $routes->get('getChronic/(:any)', 'Report_management::getChronic/$1');
  $routes->get('getADR/(:any)', 'Report_management::getADR/$1');
  $routes->get('disclosure_chart/(:any)/(:any)', 'Report_management::disclosure_chart/$1/$2');
  $routes->get('patients_disclosure/(:any)/(:any)', 'Report_management::patients_disclosure/$1/$2');
  $routes->get('getBMI/(:any)', 'Report_management::getBMI/$1');
  $routes->get('getisoniazidPatients/(:any)/(:any)', 'Report_management::getisoniazidPatients/$1/$2');
  $routes->get('getnonisoniazidPatients/(:any)', 'Report_management::getnonisoniazidPatients/$1');
  $routes->get('get_prep_patients/(:any)/(:any)', 'Report_management::get_prep_patients/$1/$2');
  $routes->get('get_pep_reasons/(:any)/(:any)', 'Report_management::get_pep_reasons/$1/$2');
  $routes->get('get_pep_reasons_patients/(:any)/(:any)', 'Report_management::get_pep_reasons_patients/$1/$2');
  $routes->get('get_prep_reasons/(:any)/(:any)', 'Report_management::get_prep_reasons/$1/$2');
  $routes->get('get_prep_reasons_patients/(:any)/(:any)', 'Report_management::get_prep_reasons_patients/$1/$2');

  //Visting Patients
  $routes->get('getScheduledPatients/(:any)/(:any)', 'Report_management::getScheduledPatients/$1/$2');
  $routes->get('getScheduledPatients/(:any)/(:any)/(:any)/(:any)', 'Report_management::getScheduledPatients/$1/$2/$3/$4');
  $routes->get('getPatientsStartedonDate/(:any)/(:any)', 'Report_management::getPatientsStartedonDate/$1/$2');
  $routes->get('getPatientsforRefill/(:any)/(:any)', 'Report_management::getPatientsforRefill/$1/$2');
  $routes->get('getPatientMissingAppointments/(:any)/(:any)', 'Report_management::getPatientMissingAppointments/$1/$2');
  $routes->get('dispensingReport/(:any)/(:any)', 'Report_management::dispensingReport/$1/$2');
  $routes->get('get_viral_load_results/(:any)/(:any)', 'Report_management::get_viral_load_results/$1/$2');
  $routes->get('getDrugs', 'Report_management::getDrugs');
  $routes->get('getPatientList/(:any)/(:any)/(:any)', 'Report_management::getPatientList/$1/$2/$3');
  $routes->get('getMMDMMS/(:any)', 'Report_management::getMMDMMS/$1');
  $routes->get('getRefillDistributionPatients/(:any)/(:any)', 'Report_management::getRefillDistributionPatients/$1/$2');
  $routes->get('distribution_refill/(:any)', 'Report_management::distribution_refill/$1');

  //Differentiated Care
  $routes->get('getPatientsOnDiffCare/(:any)/(:any)', 'Report_management::getPatientsOnDiffCare/$1/$2');
  $routes->get('getScheduledPatientsDiffCare/(:any)/(:any)', 'Report_management::getScheduledPatientsDiffCare/$1/$2');
  $routes->get('getPatientsStartedonDateDiffCare/(:any)/(:any)', 'Report_management::getPatientsStartedonDateDiffCare/$1/$2');
  $routes->get('getPatientsforRefillDiffCare/(:any)/(:any)', 'Report_management::getPatientsforRefillDiffCare/$1/$2');

  //Early Warning Indicators
  $routes->get('patients_who_changed_regimen/(:any)/(:any)', 'Report_management::patients_who_changed_regimen/$1/$2');
  $routes->get('patients_switched_to_second_line_regimen/(:any)/(:any)', 'Report_management::patients_switched_to_second_line_regimen/$1/$2');
  $routes->get('patients_starting/(:any)/(:any)', 'Report_management::patients_starting/$1/$2');
  $routes->get('getAdherence/(:any)/(:any)/(:any)/(:any)', 'Report_management::getAdherence/$1/$2/$3/$4');
  $routes->get('graphical_adherence/(:any)/(:any)/(:any)', 'Report_management::graphical_adherence/$1/$2/$3');
  $routes->get('patients_nonadherence/(:any)/(:any)', 'Report_management::patients_nonadherence/$1/$2');
  $routes->get('get_lost_followup/(:any)/(:any)', 'Report_management::get_lost_followup/$1/$2');
  $routes->get('get_viral_loadsummary/(:any)', 'Report_management::get_viral_loadsummary/$1');

  //Drug Inventory
  $routes->get('getMoreHelp/(:any)/(:any)/(:any)', 'Report_management::getMoreHelp/$1/$2/$3');
  $routes->get('drug_consumption/(:any)/(:any)', 'Report_management::drug_consumption/$1/$2');
  $routes->get('drug_stock_on_hand/(:any)', 'Report_management::drug_stock_on_hand/$1');
  $routes->get('stock_report/(:any)/(:any)/(:any)', 'Report_management::stock_report/$1/$2/$3');
  $routes->get('patient_consumption/(:any)/(:any)', 'Report_management::patient_consumption/$1/$2');
  $routes->get('stock_report/(:any)/(:any)/(:any)/(:any)', 'Report_management::stock_report/$1/$2/$3/$4');
  $routes->get('expiring_drugs/(:any)', 'Report_management::expiring_drugs/$1');
  $routes->get('expired_drugs/(:any)', 'Report_management::expired_drugs/$1');
  $routes->get('getFacilityConsumption/(:any)/(:any)', 'Report_management::getFacilityConsumption/$1/$2');
  $routes->get('getDailyConsumption/(:any)/(:any)', 'Report_management::getDailyConsumption/$1/$2');
  $routes->get('getDrugsIssued/(:any)/(:any)/(:any)', 'Report_management::getDrugsIssued/$1/$2/$3');
  $routes->get('getDrugsReceived/(:any)/(:any)/(:any)', 'Report_management::getDrugsReceived/$1/$2/$3');

  //MOH Forms
  $routes->get('getMOHForm/(:any)/(:any)/(:any)', 'Report_management::getMOHForm/$1/$2/$3');

  //Guidelines
  $routes->get('load_guidelines_view', 'Report_management::load_guidelines_view');
});

/*Manual/AutoScript*/
$routes->get('auto_management/index/(:any)', '\Modules\ADT\Controllers\Auto_management::index/$1');
