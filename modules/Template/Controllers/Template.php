<?php
namespace Modules\Template\Controllers;


class Template extends \CodeIgniter\Controller {



    public function index($data) {
        // $user_session = $this -> check_session();
        // if ($user_session) {
        $data['banner_title'] ='one';// $this->config->item('banner_title');
        $data['banner_subtitle'] = 'two';//$this->config->item('banner_subtitle');
        $data['firm_name'] ='three';// $this->config->item('firm_name');
        $data['default_home_controller'] ='home';//= $this->config->item('default_home_controller');
        echo view('\Modules\Template\Views\template_v', $data);
        
        // } else {
        // 	redirect("login");
        // }
    }

    public function check_session() {
        $current_url = $this->router->class;
        if ($current_url == "recover" || $current_url == "github") {
            return true;
        } else {
            if ($current_url != "login" && $this->session->userdata("id") == null) {
                return false;
            } else if ($current_url == "login" && $this->session->userdata("id") != null) {
                redirect($this->config->item('module_after_login'));
            } else {
                return true;
            }
        }
    }

}
