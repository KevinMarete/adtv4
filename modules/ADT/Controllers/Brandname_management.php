<?php

namespace Modules\ADT\Controllers;

ob_start();

use \Modules\ADT\Models\Brand;
use \Modules\ADT\Models\Drugcode;
use Illuminate\Database\Capsule\Manager as DB;

class Brandname_management extends \App\Controllers\BaseController {

    var $db;
    var $table;
    var $session;

    function __construct() {
        session()->set("link_id", "index");
        session()->set("linkSub", "brandname_management");
        session()->set("linkTitle", "Brand Name Management");
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
        $this->session = \Config\Services::session();
    }

    public function index() {
        $this->listing();
    }

    public function listing() {
        $data['drug_codes'] = Drugcode::where('enabled', '1')->get();
//        foreach ($data['drug_codes'] as $k) {
//            foreach ($k->Brand as $brands) {
//                dd($brands);
//            }
//        }
//      //  dd($data['drug_codes']);
        $this->base_params($data);
    }

    public function add() {
        //class::method name
        $drugsandcodes = Drugcode::getDrugCodes();
        $data['content_view'] = "brandname_add_v";
        $data['title'] = "Add New Brand Name";
        //view data
        $data['drugcodes'] = $drugsandcodes;

        $this->base_params($data);
    }

    public function delete($id) {
        $brand = Brand::getBrandName($id);
        $rowdelete = Drugcode::deleteBrand($id);
        //If query succeeds
        if ($rowdelete > 0) {
            //$this -> session -> set('message_counter', '1');
            $this->session->set('msg_error', $brand->brand . ' was deleted !');
        } else {
            //$this -> session -> set('message_counter', '2');
            $this->session->set('msg_error', 'An error occured while deleting the brand. Try again !');
        }
        return redirect()->to(base_url() . '/public/settings_management');
    }

    public function save() {
        //validation call
        //$valid = $this->_validate_submission();
        $valid = true;
        if ($valid == false) {
            $data['content_view'] = "brandname_add_v";
            $this->base_params($data);
        } else {
            $drugid = $this->request->getPost("drugid");
            $brandname = $this->request->getPost("brandname");

            $brand = new Brand();
            $brand->Drug_Id = $drugid;
            $brand->Brand = $brandname;

            $brand->save();
            //$this -> session -> set('message_counter', '1');
            $this->session->set('msg_success', $this->request->getPost('brandname') . ' was Added');
            $this->session->setFlashdata('filter_datatable', $this->request->getPost('brandname')); //Filter datatable
            return redirect()->to(base_url() . '/public/settings_management');
        }
    }

    private function _validate_submission() {
        //check for select
        $this->form_validation->set_rules('brandname', 'Brand Name', 'trim|required|min_length[2]|max_length[25]');

        return $this->form_validation->run();
    }

    public function base_params($data) {
        $data['styles'] = array("jquery-ui.css");
        $data['scripts'] = array("jquery-ui.js");
        $data['quick_link'] = "brand";
        $data['title'] = "Brand Management";
        $data['banner_text'] = "Brand Management";
        $data['link'] = "settings_management";

        echo view('\Modules\ADT\Views\\brandname_listing_v', $data);
    }

}
