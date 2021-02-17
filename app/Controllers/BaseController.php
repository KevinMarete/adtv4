<?php

namespace App\Controllers;



/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */
use CodeIgniter\Controller;

class BaseController extends Controller {

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Constructor.
     */
    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger) {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        //--------------------------------------------------------------------
        // Preload any models, libraries, etc, here.
        //--------------------------------------------------------------------
        // E.g.:
        $this->session = \Config\Services::session();

        ini_set("max_execution_time", "100000");
        ini_set("memory_limit", '2048M');
        ini_set("SMTP", 'ssl://smtp.googlemail.com');
        ini_set("smtp_port", '465');
        ini_set("sendmail_from", 'webadt.chai@gmail.com');
        date_default_timezone_set('Africa/Nairobi');
        $this->db = \Config\Database::connect();
        service('eloquent');
        $this->uri = service('uri');
    }

    //shorten field input
    function post($field) {
        if(!empty($_POST[$field]))
            return $_POST[$field];
        else return "";
    }

    function loadChoices($table, $column) {
        $db = \Config\Database::connect();
        return (array) $db->table($table)->select($column)->groupBy($column)->get();
    }

    //ping ppb host
    function ping($host, $port, $timeout) {
        $starttime = microtime(true);
        $file = fsockopen($host, $port, $errno, $errstr, $timeout);
        $stoptime = microtime(true);
        $status = 0;
        if (!$file)
            $status = -1;  // Site is down
        else {
            fclose($file);
            $status = ($stoptime - $starttime) * 1000;
            $status = 200;
        }
        return $status;
    }

    function serverStatus() {
        @$res = $this->ping($this->IP, $this->PORT, $this->TIMEOUT);
        if ($res === 200) {
            echo json_encode(['status' => 200]);
        } else {
            echo json_encode(['status' => 404]);
        }
    }

}
