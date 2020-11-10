<?php 
/* Default route */
<<<<<<< HEAD
$routes->post('api', '\Modules\Api\Controllers\Api::index');
=======
$routes->match(['post','get'], 'api', '\Modules\Api\Controllers\Api::index');
>>>>>>> 4473e35ba94c74a2c085d2d14f69892a0465bd91
$routes->get('api/settings', '\Modules\Api\Controllers\Api::settings');
$routes->get('api/getPatient/(:any)/(:any)', '\Modules\Api\Controllers\Api::getPatient/$1/$2');
//$routes->post('initialize', '\Modules\Setup\Controllers\Setup::initialize');