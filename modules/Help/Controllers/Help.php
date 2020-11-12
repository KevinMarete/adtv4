<?php

namespace Modules\Help\Controllers;

use App\Libraries\Ftp;
use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;
use App\Libraries\Mysqldump;
use App\Libraries\Zip;

class Help extends \CodeIgniter\Controller {

    var $backup_dir = "./backup_db";
    var $config = array(
        'hostname' => 'commodities.nascop.org',
        'username' => 'ftpuser',
        'password' => 'ftpuser',
        'debug' => FALSE);
    
    var $ftp;
    var $table;
    var $session;

    function __construct() {
        $this->ftp = new \Ftp();
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
        $this->session = \Config\Services::session();
    }

    public function index() {
        $dir = "./assets/manuals";
        // $data['backup_files'] = $this -> checkdir();
        $data['active_menu'] = 6;
        $data['content_view'] = "\Modules\Help\Views\\help_v";
        $data['title'] = "Dashboard | System Recovery";

        $data['ftp_status'] = '';

        helper('filesystem');

        //$dir = realpath($_SERVER['DOCUMENT_ROOT']);
        $files = directory_map ($dir);
     

        $columns = array('#', 'File Name', 'Action');
        $tmpl = array('table_open' => '<table class="table table-bordered table-hover table-condensed table-striped dataTables" >');
        $this->table->setTemplate($tmpl);
        $this->table->setHeading($columns);

        foreach ($files as $file) {

            $links = "<a href='" . str_replace("tools/", "", base_url()) . "/assets/manuals/" . $file . "'target='_blank'>View</a>";


            $this->table->addRow("", $file, $links);
        }
        $data['guidelines_list'] = $this->table->generate();
        $data['hide_side_menu'] = 1;
        $data['selected_report_type_link'] = "guidelines_report_row";
        $data['selected_report_type'] = "List of Guidelines";
        $data['report_title'] = "List of Guidelines";
        $data['facility_name'] = $this->session->get('facility_name');
        // $data['content_view']='guidelines_listing_v';
        // $this -> base_params($data);
        // $table = '<table id="dyn_table" class="table table-striped table-condensed table-bordered" cellspacing="0" width="100%">';
        // $table .= '<thead><th>Manual</th>		<th>action</th>		<th>local</th>		<th>remote</th>		</thead>';
        // $table .= '<tbody>';
        // $table .='</tbody>';
        // $table .='</table>';
        // // echo $table;die;
        // $data['backup_files'] = $table;
        $this->template($data);
    }

    public function template($data) {
        $data['show_menu'] = 0;
        $data['show_sidemenu'] = 0;
        $template = new Template();
        $template->index($data);
    }

}
