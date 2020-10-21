<?php
/*Authentication Routes*/
// $routes->get('/', '\Modules\ADT\Controllers\System_management::index');
$routes->get('login', '\Modules\ADT\Controllers\User_management::login');
$routes->post('user_management/authenticate', '\Modules\ADT\Controllers\User_management::authenticate');
$routes->get('home', '\Modules\ADT\Controllers\Home_controller::home');
$routes->get('logout/(:any)', '\Modules\ADT\Controllers\User_management::logout');

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
$routes->get('getExpiringDrugs/(:any)/(:any)', '\Modules\ADT\Controllers\Facilitydashboard_Management::getExpiringDrugs');
$routes->get('getPatientEnrolled/(:any)/(:any)', '\Modules\ADT\Controllers\Facilitydashboard_Management::getPatientEnrolled');
$routes->get('getExpectedPatients/(:any)/(:any)', '\Modules\ADT\Controllers\Facilitydashboard_Management::getExpectedPatients');
$routes->get('getStockSafetyQty', '\Modules\ADT\Controllers\Facilitydashboard_Management::getStockSafetyQty');
$routes->get('getStockSafetyQty/(:any)', '\Modules\ADT\Controllers\Facilitydashboard_Management::getStockSafetyQty');
$routes->post('drillAccessLevel', '\Modules\ADT\Controllers\Admin_management::drillAccessLevel');

/* Inventory Stock Management */
$routes->get('stock_transaction/(:any)', '\Modules\ADT\Controllers\Inventory_management::stock_transaction');
$routes->post('getAllDrugs', '\Modules\ADT\Controllers\Inventory_management::getAllDrugs');
$routes->post('getDrugDetails', '\Modules\ADT\Controllers\Inventory_management::getDrugDetails');
$routes->post('checkConnection', '\Modules\ADT\Controllers\System_management::checkConnection');
$routes->post('getStockDrugs', '\Modules\ADT\Controllers\Inventory_management::getStockDrugs');
$routes->post('getBacthes', '\Modules\ADT\Controllers\Inventory_management::getBacthes');
$routes->post('getBacthDetails', '\Modules\ADT\Controllers\Inventory_management::getBacthDetails');
$routes->post('inventory_management/save', '\Modules\ADT\Controllers\Inventory_management::save');
$routes->get('inventory_management', '\Modules\ADT\Controllers\Inventory_management::index');
$routes->get('stock_listing/(:any)', '\Modules\ADT\Controllers\Inventory_management::stock_listing');
$routes->get('getDrugBinCard/(:any)/(:any)', '\Modules\ADT\Controllers\Inventory_management::getDrugBinCard');
$routes->get('getDrugTransactions/(:any)/(:any)', '\Modules\ADT\Controllers\Inventory_management::getDrugTransactions');
$routes->get('inventory_management/getIsoniazid/(:any)', '\Modules\ADT\Controllers\Inventory_management::getIsoniazid');
$routes->post('inventory_management/getAllDrugsBatches/(:any)', '\Modules\ADT\Controllers\Inventory_management::getAllDrugsBatches/(:any)');
$routes->post('inventory_management/getAllBacthDetails', '\Modules\ADT\Controllers\Inventory_management::getAllBacthDetails');



/* Settings Management */
$routes->get('settings_management', '\Modules\ADT\Controllers\Settings_management::index');
$routes->get('regimen_management/', '\Modules\ADT\Controllers\Settings_management::index');

/* Auto Management */
$routes->get('auto_management', '\Modules\ADT\Controllers\Auto_management::index');
$routes->get('auto_management/get_viral_load/(:any)', '\Modules\ADT\Controllers\Auto_management::get_viral_load');



/*User Management*/
$routes->get('user_management', '\Modules\ADT\Controllers\User_management::index');


/*Patients*/
$routes->addRedirect('patient_management', 'public/patients');
$routes->addRedirect('addpatient_show', 'public/patients/add');
$routes->get('patients', '\Modules\ADT\Controllers\Patient_management::index');
$routes->get('patients/listing', '\Modules\ADT\Controllers\Patient_management::listing');
$routes->get('patients/add', '\Modules\ADT\Controllers\Patient_management::addpatient_show');
$routes->match(['get', 'post'], 'patient/save', '\Modules\ADT\Controllers\Patient_management::save');
$routes->get('patient/disable', '\Modules\ADT\Controllers\Patient_management::disable');
$routes->get('patient/edit/(:any)', '\Modules\ADT\Controllers\Patient_management::edit');
$routes->post('patient/update/(:any)', '\Modules\ADT\Controllers\Patient_management::update');
$routes->get('get-patients', '\Modules\ADT\Controllers\Patient_management::get_patients');
$routes->get('get-patients/(:any)', '\Modules\ADT\Controllers\Patient_management::get_patients');
$routes->get('patient/load_view/(:any)/(:any)', '\Modules\ADT\Controllers\Patient_management::load_view');
$routes->get('patient/load_patient/(:any)', '\Modules\ADT\Controllers\Patient_management::load_patient');
$routes->get('patient/get_visits/(:any)', '\Modules\ADT\Controllers\Patient_management::get_visits');
$routes->get('patient/load_form/(:any)', '\Modules\ADT\Controllers\Patient_management::load_form');
$routes->get('patient/load_summary/(:any)', '\Modules\ADT\Controllers\Patient_management::load_summary');
$routes->get('patient/checkpatient_no/(:any)', '\Modules\ADT\Controllers\Patient_management::checkpatient_no');
$routes->match(['get', 'post'], 'patient/getSixMonthsDispensing/(:any)', '\Modules\ADT\Controllers\Patient_management::getSixMonthsDispensing');
$routes->match(['get', 'post'], 'patient/getRegimenChange/(:any)', '\Modules\ADT\Controllers\Patient_management::getRegimenChange');
$routes->match(['get', 'post'], 'patient/getAppointmentHistory/(:any)', '\Modules\ADT\Controllers\Patient_management::getAppointmentHistory');
$routes->get('patient/get_Last_vl_result/(:any)', '\Modules\ADT\Controllers\Patient_management::get_Last_vl_result');
$routes->match(['get', 'post'], 'patient/get_viral_load_info/(:any)', '\Modules\ADT\Controllers\Patient_management::get_viral_load_info');
$routes->get('patient/viewDetails/(:any)', '\Modules\ADT\Controllers\Patient_management::viewDetails');
$routes->get('patient/requiredFields/(:any)', '\Modules\ADT\Controllers\Patient_management::requiredFields');
$routes->match(['get', 'post'], 'patient/get_patient_details', '\Modules\ADT\Controllers\Patient_management::get_patient_details');
$routes->match(['get', 'post'], 'patient/getAppointments/(:any)', '\Modules\ADT\Controllers\Patient_management::getAppointments');

/* Dispensement */
$routes->get('dispensement_management/dispense/(:any)', '\Modules\ADT\Controllers\Dispensement_management::dispense');
$routes->match(['get', 'post'], 'dispensement_management/save', '\Modules\ADT\Controllers\Dispensement_management::save');
$routes->get('dispensement_management/edit/(:any)', '\Modules\ADT\Controllers\Dispensement_management::edit');
$routes->get('dispensement_management/getPrescriptions/(:any)', '\Modules\ADT\Controllers\Dispensement_management::getPrescriptions');
$routes->get('dispensement_management/get_patient_data/(:any)', '\Modules\ADT\Controllers\Dispensement_management::get_patient_data');
$routes->match(['get', 'post'], 'dispensement_management/get_other_dispensing_details', '\Modules\ADT\Controllers\Dispensement_management::get_other_dispensing_details');
$routes->match(['get', 'post'], 'dispensement_management/getDrugsRegimens', '\Modules\ADT\Controllers\Dispensement_management::getDrugsRegimens');
$routes->match(['get', 'post'], 'dispensement_management/getDrugDose/(:any)', '\Modules\ADT\Controllers\Dispensement_management::getDrugDose');
$routes->match(['get', 'post'], 'dispensement_management/drugAllergies', '\Modules\ADT\Controllers\Dispensement_management::drugAllergies');
$routes->post('dispensement_management/getBrands', '\Modules\ADT\Controllers\Dispensement_management::getBrands');
$routes->post('dispensement_management/getIndications', '\Modules\ADT\Controllers\Dispensement_management::getIndications');
$routes->post('dispensement_management/getFacililtyAge', '\Modules\ADT\Controllers\Dispensement_management::getFacililtyAge');
$routes->post('dispensement_management/getInstructions/(:any)', '\Modules\ADT\Controllers\Dispensement_management::getInstructions');
$routes->post('dispensement_management/print_test', '\Modules\ADT\Controllers\Dispensement_management::print_test');
$routes->post('dispensement_management/getPreviouslyDispensedDrugs', '\Modules\ADT\Controllers\Dispensement_management::getPreviouslyDispensedDrugs');
$routes->post('dispensement_management/getDoses', '\Modules\ADT\Controllers\Dispensement_management::getDoses');

/* Regimens */
$routes->match(['get', 'post'], 'regimen_management/getRegimenLine/(:any)', '\Modules\ADT\Controllers\Regimen_management::getRegimenLine');
$routes->post('regimen_management/getRegimenLine/(:any)/(:any)', '\Modules\ADT\Controllers\Regimen_management::getRegimenLine');
$routes->match(['get', 'post'], 'regimen_management/getFilteredRegiments', '\Modules\ADT\Controllers\Regimen_management::getFilteredRegiments');
$routes->match(['get', 'post'], 'regimen_management/getAllDrugs', '\Modules\ADT\Controllers\Regimen_management::getAllDrugs');

/* Order */
$routes->get('order', '\Modules\ADT\Controllers\Order::index');
$routes->post('order/authenticate_user', '\Modules\ADT\Controllers\Order::authenticate_user');
$routes->get('order/logout', '\Modules\ADT\Controllers\Order::logout');
$routes->get('order/view_order/(:any)/(:any)', '\Modules\ADT\Controllers\Order::view_order');
$routes->get('order/read_order/(:any)/(:any)', '\Modules\ADT\Controllers\Order::read_order');
$routes->match(['get', 'post'], 'order/update_order/(:any)/(:any)', '\Modules\ADT\Controllers\Order::update_order');
$routes->post('order/save/(:any)/(:any)/(:any)', '\Modules\ADT\Controllers\Order::save');
$routes->post('order/save/(:any)/(:any)', '\Modules\ADT\Controllers\Order::save');
$routes->match(['get', 'post'], 'order/create_order/(:any)/(:any)', '\Modules\ADT\Controllers\Order::create_order');
$routes->match(['get', 'post'], 'order/get_aggregated_fmaps/(:any)/(:any)', '\Modules\ADT\Controllers\Order::get_aggregated_fmaps');
$routes->post('order/getExpectedActualReport', '\Modules\ADT\Controllers\Order::getExpectedActualReport');
$routes->post('order/getPeriodRegimenPatients/(:any)/(:any)', '\Modules\ADT\Controllers\Order::getPeriodRegimenPatients');
$routes->post('order/getNotMappedRegimenPatients/(:any)/(:any)', '\Modules\ADT\Controllers\Order::getNotMappedRegimenPatients');
$routes->match(['get', 'post'], 'order/getoiPatients', '\Modules\ADT\Controllers\Order::getoiPatients');
$routes->match(['get', 'post'], 'order/getItems', '\Modules\ADT\Controllers\Order::getItems');
$routes->post('order/getCentralDataMaps/(:any)/(:any)/(:any)', '\Modules\ADT\Controllers\Order::getCentralDataMaps');
$routes->get('order/download_order/(:any)/(:any)', '\Modules\ADT\Controllers\Order::download_order');
$routes->get('order/get_dhis_data/(:any)', '\Modules\ADT\Controllers\Order::get_dhis_data/(:any)');

/** Order Settings */
$routes->get('order_settings/fetch/(:any)', '\Modules\ADT\Controllers\Order_settings::fetch');