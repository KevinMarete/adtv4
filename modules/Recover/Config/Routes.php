<?php

/* Defining backup routes
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$routes->get('recover', '\Modules\Recover\Controllers\Recover::index');
$routes->post('check_server', '\Modules\Recover\Controllers\Recover::check_server');
$routes->post('check_database', '\Modules\Recover\Controllers\Recover::check_database');
$routes->post('start_recovery', '\Modules\Recover\Controllers\Recover::start_recovery');

