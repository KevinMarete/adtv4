<?php

$routes->get('login', '\Modules\ADT\Controllers\User_management::login');
$routes->post('user_management/authenticate', '\Modules\ADT\Controllers\User_management::authenticate');
$routes->get('home', '\Modules\ADT\Controllers\Home_controller::home');
$routes->get('logout/(:any)', '\Modules\ADT\Controllers\User_management::logout');

/* Notification Management*/
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

/*Home Dashboard*/
$routes->get('getExpiringDrugs/(:any)/(:any)', '\Modules\ADT\Controllers\Facilitydashboard_Management::getExpiringDrugs');
$routes->get('getPatientEnrolled/(:any)/(:any)', '\Modules\ADT\Controllers\Facilitydashboard_Management::getPatientEnrolled');
$routes->get('getExpectedPatients/(:any)/(:any)', '\Modules\ADT\Controllers\Facilitydashboard_Management::getExpectedPatients');
$routes->get('getStockSafetyQty', '\Modules\ADT\Controllers\Facilitydashboard_Management::getStockSafetyQty');
$routes->get('getStockSafetyQty/(:any)', '\Modules\ADT\Controllers\Facilitydashboard_Management::getStockSafetyQty');
$routes->post('drillAccessLevel', '\Modules\ADT\Controllers\Admin_management::drillAccessLevel');

/*Inventory Stock Management*/
$routes->get('stock_transaction/(:any)', '\Modules\ADT\Controllers\Inventory_management::stock_transaction');
