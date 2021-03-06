<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('\Modules\ADT\Controllers');
$routes->setDefaultController('User_management');
$routes->setDefaultMethod('logout');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */
// We get a performance increase by specifying the default
// route since we don't have to scan directories.
//$routes->get('/', '\Modules\ADT\Controllers\User_management::sendToLgin');
//$routes->get('/recover', 'Modules\Recover\Controllers\Recover::index');


/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}


if (file_exists(ROOTPATH . 'modules')) {
    $modulePath = ROOTPATH . 'modules/';
    $modules = scandir($modulePath);

    foreach ($modules as $module) {
        if ($module == '.' || $module === '..')
            continue;
        if (is_dir($modulePath) . '/' . $module) {
            $routPath = $modulePath . $module . '/Config/Routes.php';
            if (file_exists($routPath)) {
                require $routPath;
            } else {
                continue;
            }
        }
    }
}
