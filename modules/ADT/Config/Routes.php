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
$routes->get('regimen_management/', '\Modules\ADT\Controllers\Settings_management::index');

/*User Management*/
$routes->get('user_management', '\Modules\ADT\Controllers\User_management::index');
$routes->get('user_management/get_stores', '\Modules\ADT\Controllers\User_management::get_stores');

/* Report Management*/
$routes->group('report_management', ['namespace' => '\Modules\ADT\Controllers'], function ($routes) {
  $routes->get('/', 'Report_management::index');
  $routes->get('cumulative_patients/(:any)/(:any)', 'Report_management::cumulative_patients/$1//$2');
  $routes->get('patient_enrolled/(:any)/(:any)/(:any)', 'Report_management::patient_enrolled/$1/$2/$3');
  $routes->get('getStartedonART/(:any)/(:any)/(:any)', 'Report_management::getStartedonART/$1/$2/$3');
});
