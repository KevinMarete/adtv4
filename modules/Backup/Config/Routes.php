<?php

/* Defining backup routes
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$routes->get('backup', '\Modules\Backup\Controllers\Backup::index');
$routes->post('run_backup', '\Modules\Backup\Controllers\Backup::run_backup');
$routes->post('upload_backup', '\Modules\Backup\Controllers\Backup::upload_backup');
$routes->post('upload_backup_mg/(:any)', '\Modules\Backup\Controllers\Backup::upload_backup_mg/$1');
$routes->post('delete_backup', '\Modules\Backup\Controllers\Backup::delete_backup');
$routes->post('download_remote_file', '\Modules\Backup\Controllers\Backup::download_remote_file');
$routes->get('auto_backup', '\Modules\ADT\Controllers\Auto_management::auto_backup');

