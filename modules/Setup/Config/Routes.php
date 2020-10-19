<?php

/* Defining backup routes
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$routes->get('setup', '\Modules\Setup\Controllers\Setup::index');
$routes->get('getfacilities', '\Modules\Setup\Controllers\Setup::getFacilities');
$routes->post('initialize', '\Modules\Setup\Controllers\Setup::initialize');


