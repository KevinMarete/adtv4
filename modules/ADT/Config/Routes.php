<?php

/* Authentication Routes */
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
$routes->get('getExpectedPatients/(:any)/(:any)', '\Modules\ADT\Controllers\Facilitydashboard_Management::getExpectedPatients');
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


/* Settings Management */
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

/* Facilities */
$routes->get('facility_management', '\Modules\ADT\Controllers\Facility_Management::index');
$routes->get('client_management', '\Modules\ADT\Controllers\Client_management::index');
$routes->post('facility_management/update', '\Modules\ADT\Controllers\Facility_Management::update');
$routes->get('order_settings/listing/(:any)', '\Modules\ADT\Controllers\Order_settings::listing/$1');

/* Others */
$routes->get('nonadherence_management', '\Modules\ADT\Controllers\Nonadherence_management::index');
$routes->get('patient_management/merge_list', '\Modules\ADT\Controllers\Patient_management::merge_list');
$routes->get('getPatientMergeList', '\Modules\ADT\Controllers\Patient_management::getPatientMergeList');


/* User Management */
$routes->get('user_management', '\Modules\ADT\Controllers\User_management::index');
$routes->get('user_management/save', '\Modules\ADT\Controllers\User_management::save');
$routes->get('user_management/enable/(:any)', '\Modules\ADT\Controllers\User_management::enable/$1');
$routes->get('user_management/edit', '\Modules\ADT\Controllers\User_management::edit_');


/* Manual/AutoScript */
$routes->get('auto_management/index/(:any)', '\Modules\ADT\Controllers\Auto_management::index/$1');
$routes->get('regimen_drug_management/enable/(:any)', '\Modules\ADT\Controllers\Brandname_management::enable/$1');
$routes->get('regimen_drug_management/disable/(:any)', '\Modules\ADT\Controllers\Brandname_management::disable/$1');


