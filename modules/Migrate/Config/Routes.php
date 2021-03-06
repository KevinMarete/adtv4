<?php

/* Defining backup routes
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$routes->get('migrate/excel', '\Modules\Migrate\Controllers\Excel_migration::index');

$routes->get('migrate/excel/template/(:any)', '\Modules\Migrate\Controllers\Excel_migration::downloadTemplate/$1');
$routes->post('migrate/excel/upload_file', '\Modules\Migrate\Controllers\Excel_migration::importExcel');

$routes->get('migrate/editt', '\Modules\Migrate\Controllers\Editt::index');
$routes->get('migrate/access', '\Modules\Migrate\Controllers\Access::index');
$routes->get('api/settings', '\Modules\Api\Controllers\Api::settings');

