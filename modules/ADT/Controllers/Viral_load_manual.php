<?php

namespace Modules\ADT\Controllers;

ob_start();

use \Modules\ADT\Models\Brand;
use \Modules\ADT\Models\PatientSource;
use \Modules\ADT\Models\VisitPurpose;
use Illuminate\Database\Capsule\Manager as DB;

class Viral_load_manual extends \App\Controllers\BaseController {

    var $db;
    var $table;
    var $session;

    function __construct() {
        session()->set("link_id", "index");
        session()->set("linkSub", "viral_load_manual");
        session()->set("linkTitle", "Viral Load Results");
        $this->db = \Config\Database::connect();
        $this->table = new \CodeIgniter\View\Table();
        $this->session = \Config\Services::session();
    }

    public function index() {
        $this->listing();
    }

    public function listing() {
        $access_level = $this->session->get('user_indicator');
        $data = array();
        //get viral load from the database
        $sql = "select * from patient_viral_load limit 10";
        $query = $this->db->query($sql);
        $viral_results = $query->getResultArray();
        $tmpl = array('table_open' => '<table class="vl_results table table-bordered table-striped">');
        $this->table->setTemplate($tmpl);
        $this->table->setHeading('id', 'Patient CCC Number', 'Date Collected', 'Test Date', 'Result', 'Justification', 'Options');
        $data['viral_result'] = $this->table->generate();
        $this->base_params($data);
    }

    function get_viral_load() {

        $data = array();
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
         * you want to insert a non-database field (for example a counter or static image)
         */
        $aColumns = array('id', 'patient_ccc_number', 'date_collected', 'test_date', 'result', 'justification', 'id');
        $iDisplayStart = @$_GET['iDisplayStart'];
        $iDisplayLength = @$_GET['iDisplayLength'];
        $iSortCol_0 = @$_GET['iSortCol_0'];
        $iSortingCols = @$_GET['iSortingCols'];
        $sSearch = @$_GET['sSearch'];
        $sEcho = @$_GET['sEcho'];
        /*
         * Paging
         * */
        $sLimit = "";
        if (isset($iDisplayStart) && $iDisplayLength != '-1') {
            $sLimit = "LIMIT " . intval($iDisplayStart) . ", " . intval($iDisplayLength);
        }


        /*
         * Ordering
         */
        $sOrder = "";
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY  ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    $sOrder .= "`" . $aColumns[intval($_GET['iSortCol_' . $i])] . "` " . ($_GET['sSortDir_' . $i] === 'asc' ? 'asc' : 'desc') . ", ";
                }
            }

            $sOrder = substr_replace($sOrder, "", -2);
            if ($sOrder == "ORDER BY") {
                $sOrder = "";
            }
        }

        /*
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $sFilter = "";
        $c = 0;
        if (isset($sSearch) && !empty($sSearch)) {
            $sFilter = "AND ( ";
            for ($i = 0; $i < count($aColumns); $i++) {
                $bSearchable = $_GET['bSearchable_' . $i];

                // Individual column filtering
                if (isset($bSearchable) && $bSearchable == 'true') {
                    if ($aColumns[$i] != 'drug_unit') {
                        if ($c != 0) {
                            $sFilter .= " OR ";
                        }
                        $c = 1;
                        $sSearch = mysql_real_escape_string($sSearch);
                        $sFilter .= "`" . $aColumns[$i] . "` LIKE '%" . $sSearch . "%'";
                    }
                }
            }
            $sFilter .= " )";
            if ($sFilter == "AND ( )") {
                $sFilter = "";
            }
        }


        $iFilteredTotal = count($this->db->query('select *  from patient_viral_load')->getResultArray());
        $string_sql = "select patient_ccc_number,date_collected,test_date,result,justification, CONCAT('<a href=#edit_form id=',id,' role=button class = edit_user data-toggle=modal name=',patient_ccc_number,'>Edit<a/>') AS id from patient_viral_load WHERE 1 $sFilter $sOrder $sLimit";
        $rResult = $this->db->query($string_sql);

        // Data set length after filtering
        //Total number of drugs that are displayed
        $iFilteredTotal = count($rResult->getResultArray());

        $query = $this->db->query('SELECT COUNT(patient_ccc_number) AS found_rows  from  patient_viral_load')->getResult();
        $iTotal = $query[0]->found_rows;

        // Output
        $output = array('sEcho' => intval($sEcho), 'iTotalRecords' => $iTotal, 'iTotalDisplayRecords' => $iFilteredTotal, 'aaData' => array());

        foreach ($rResult->getResultArray()as $aRow) {
            $row = array();
            $x = 0;
            foreach ($aColumns as $col) {
                $x++;
                //Format soh
                $row[] = $aRow[$col];
            }
            $id = $aRow['id'];
            $output['aaData'][] = $row;
        }

        echo json_encode($output);
    }

    public function get_patient_ccc_number() {
        $sql = "select patient_number_ccc as patient_ccc_number from patient";
        $query = $this->db->query($sql);
        $ccc_result = $query->getResultArray();
        echo json_encode($ccc_result);
    }

    public function update() {
        $id = $this->request->getPost('id');
        $patient_ccc_number = $this->request->getPost('patient_ccc_number');
        $query = $this->db->query("UPDATE patient_viral_load SET patient_ccc_number='$patient_ccc_number' WHERE id='$id'");
        $this->session->set('msg_success', $this->request->getPost('patient_ccc_number') . ' was Updated');
        $this->session->setFlashdata('filter_datatable', $this->request->getPost('patient_ccc_number'));
        //Filter datatable
        return redirect()->to(base_url() . '/settings_management');
    }

    private function _submit_validate() {
        // validation rules
        $this->form_validation->set_rules('patient_ccc_number', 'Patient CCC Number', 'trim|required|min_length[2]|max_length[100]');

        return $this->form_validation->run();
    }

    public function base_params($data) {
        $data['quick_link'] = "indications";
        echo view('\Modules\ADT\Views\\viral_load_manual_v', $data);
    }

}

?>