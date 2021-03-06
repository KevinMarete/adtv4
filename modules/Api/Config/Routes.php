<?php

/* Default route */

$routes->match(['post', 'get'], 'api', '\Modules\Api\Controllers\Api::index');
$routes->match(['post', 'get'], 'api/settings', '\Modules\Api\Controllers\Api::settings');
$routes->get('api/getPatient/(:any)/(:any)', '\Modules\Api\Controllers\Api::getPatient/$1/$2');
$routes->get('api/getDispensing/(:any)', '\Modules\Api\Controllers\Api::getDispensing/$1');
$routes->get('api/getPatientList', '\Modules\Api\Controllers\Api::getPatientList');
$routes->get('api/searchPatient/(:any)', '\Modules\Api\Controllers\Api::searchPatient/$1');
$routes->get('api/searchGender/(:any)', '\Modules\Api\Controllers\Api::searchGender/$1');

//$routes->post('initialize', '\Modules\Setup\Controllers\Setup::initialize');
$routes->post('api/patient', '\Modules\Api\Controllers\Data_Api::createPatient');
$routes->get('api/patients/(:any)', '\Modules\Api\Controllers\Data_Api::searchPatient/$1');
$routes->delete('api/patients/(:any)', '\Modules\Api\Controllers\Data_Api::deletePatient/$1');
