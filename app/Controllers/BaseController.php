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
        if (session()->get('user_id')=='') {
          // echo 'This session'.session()->get('user_id');
           //header('Location :'.base_url().'/public/login');
           // return redirect()->to(site_url('/public/login'));
        }else{
           return redirect()->to(base_url().'/public/login');
            
        }
    }

    //shorten field input
    function post($field) {
        if(!empty($_POST[$field]))
            return $_POST[$field];
        else return "";
    }

}
