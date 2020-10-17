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


/*Manual/AutoScript*/
$routes->get('auto_management/index/(:any)', '\Modules\ADT\Controllers\Auto_management::index/$1');


