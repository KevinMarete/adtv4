<?php

/* Default route */
$routes->get('api', '\Modules\Api\Controllers\Api::index');
$routes->get('api/settings', '\Modules\Api\Controllers\Api::settings');
//$routes->post('initialize', '\Modules\Setup\Controllers\Setup::initialize');
