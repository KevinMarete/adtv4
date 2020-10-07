<?php

/* Defining backup routes
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$routes->get('backup', '\Modules\Backup\Controllers\Backup::index');
$routes->post('run_backup', '\Modules\Backup\Controllers\Backup::run_backup');
$routes->post('upload_backup', '\Modules\Backup\Controllers\Backup::upload_backup');
$routes->post('delete_backup', '\Modules\Backup\Controllers\Backup::delete_backup');
$routes->post('download_remote_file', '\Modules\Backup\Controllers\Backup::download_remote_file');

