<?php

namespace Modules\ADT\Controllers;

use App\Libraries\Ftp;
use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;
use App\Libraries\Mysqldump;
use App\Libraries\Encrypt;
use App\Libraries\Zip;

use App\Models\BaseModel;
use \Modules\ADT\Models\User;
use \Modules\ADT\Models\Facilities;
use \Modules\ADT\Models\Access_log;
use \Modules\ADT\Models\Transaction_type;
use \Modules\ADT\Models\Drug_source;
use \Modules\ADT\Models\Drugcode;
use \Modules\ADT\Models\Drug_Destination;
use \Modules\ADT\Models\CCC_store_service_point;
use \Modules\ADT\Models\Drug_Stock_Movement;
use \Modules\ADT\Models\Regimen_Change_Purpose;
use \Modules\ADT\Models\Non_adherence_reasons;
use \Modules\ADT\Models\Visit_Purpose;
use Illuminate\Database\Capsule\Manager as DB;
use Modules\ADT\Models\PatientVisit;
use Mpdf\Mpdf;

class Inventory_management extends \App\Controllers\BaseController {

    var $db;

    function __construct() {
        $this->db = \Config\Database::connect();
    }

    public function index() {
        $this->listing();
    }

    public function listing($stock_type = 1) {
        $data['active'] = "";
        //Make pharmacy inventory active
        if ($stock_type == 2) {
            $data['active'] = 'pharmacy_btn';
        }
        //Make store inventory active
        else {
            $data['active'] = 'store_btn';
        }
        $data['content_view'] = "\Modules\ADT\Views\\inventory_listing_v";
        $this->base_params($data);
    }

    function getIsoniazid($patientid = null) {
        $patientid = $this->uri->getSegment(3);
        $ids = [];
        $res = DB::select(DB::raw("SELECT * FROM drugcode WHERE drug LIKE '%ISONIAZID%'"));
        foreach ($res as $i){
            $ids[] = $i->id;
        }
        $isocount = PatientVisit::where('patient_id', $patientid)->whereIn('drug_id', $ids)->sum('quantity');
        echo json_encode(['iso_count' => $isocount]);
    }

    public function stock_listing($stock_type = 1) {
        $facility_code = session()->get('facility');
        $data = [];
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
         * you want to insert a non-database field (for example a counter or static image)
         */
        $aColumns = ['drug', 'generic_name', 'stock_level', 'drug_unit', 'pack_size', 'supported_by', 'dose'];
        $iDisplayStart = @$_GET['start'];
        $iDisplayLength = @$_GET['length'];
        $iSortCol_0 = @$_GET['order'][0]['column'];
        $iSortingCols = @$_GET['order'];
        $sSearch = @$_GET['search']['value'];
        $sEcho = @$_GET['draw'];
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
        if (isset($iSortCol_0)) {
            $sOrder = "ORDER BY  ";
            for ($i = 0; $i < intval($iSortingCols); $i++) {
                $iSortCol = $this->request->getGetPost('order')[$i]['column'];
                $bSortable = $this->request->getGetPost('columns')[intval($iSortCol)]['orderable'];
                $sSortDir = $this->request->getGetPost('order')[$i]['dir'];

                if ($bSortable == "true") {
                    $sOrder .= "`" . $aColumns[intval($iSortCol)] . "` " . ($sSortDir === 'asc' ? 'asc' : 'desc') . ", ";
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
                $bSearchable = $this->request->getGetPost('columns')[$i]['searchable'];

                // Individual column filtering
                if (isset($bSearchable) && $bSearchable == 'true') {
                    if ($aColumns[$i] != 'drug_unit') {
                        if ($c != 0) {
                            $sFilter .= " OR ";
                        }
                        $c = 1;
                        // $sSearch = mysql_real_escape_string($sSearch);
                        $sFilter .= "`" . $aColumns[$i] . "` LIKE '%" . $sSearch . "%'";
                    }
                }
            }
            $sFilter .= " )";
            if ($sFilter == "AND ( )") {
                $sFilter = "";
            }
        }

        // Select Data
        $sql = "SELECT dc.id,UPPER( dc.drug ) AS drug, du.Name AS drug_unit,d.Name as dose, s.name AS supported_by, dc.pack_size, UPPER( g.Name ) AS generic_name, IF( SUM( balance ) >0, SUM( balance ) ,  '0' ) AS stock_level
        FROM drugcode dc
        LEFT OUTER JOIN generic_name g ON g.id = dc.generic_name
        LEFT OUTER JOIN suppliers s ON s.id = dc.supported_by
        LEFT OUTER JOIN dose d ON d.Name = dc.dose
        LEFT OUTER JOIN drug_unit du ON du.id = dc.unit
        LEFT OUTER JOIN (
        SELECT * 
        FROM drug_stock_balance
        WHERE facility_code =  '$facility_code'
        AND expiry_date > CURDATE()
        AND stock_type =  '$stock_type'
        ) AS dsb ON dsb.drug_id = dc.id
        WHERE dc.enabled =  '1' " . $sFilter . "
        GROUP BY dc.id " . $sOrder . " " . $sLimit;
        $q = $this->db->query($sql);
        $rResult = $q;
        //echo $iDisplayLength;die();
        // Data set length after filtering
        $res = $this->db->query('SELECT COUNT(id) AS found_rows from drugcode dc where dc.enabled=1 ' . $sFilter)->getResult();
        $iFilteredTotal = $res[0]->found_rows;

        //Total number of drugs that are displayed
        $res2 = $this->db->query('SELECT COUNT(id) AS found_rows from drugcode dc where dc.enabled=1')->getResult();
        $iTotal = $res2[0]->found_rows;
        //$iFilteredTotal = $iTotal;
        // Output
        $output = ['sEcho' => intval($sEcho), 'iTotalRecords' => $iTotal, 'iTotalDisplayRecords' => $iFilteredTotal, 'aaData' => []];

        foreach ($rResult->getResultArray() as $aRow) {
            $row = [];
            $x = 0;
            foreach ($aColumns as $col) {
                $x++;
                //Format soh
                if ($col == "stock_level") {
                    $row[] = '<b style="color:green">' . number_format($aRow['stock_level']) . '</b>';
                } else {
                    $row[] = $aRow[$col];
                }
            }
            $id = $aRow['id'];
            $row[] = "<a href='" . base_url() . "/getDrugBinCard/" . $id . "/" . $stock_type . "'>View Bin Card</a>";

            $output['aaData'][] = $row;
        }
        
        
        echo json_encode($output);
    }
  
    public function getDrugBinCard($drug_id = '', $ccc_id = '') {
        $uri = $this->request->uri;

        //CCC Store Name
        $ccc = CCC_store_service_point::getCCC($uri->getSegment(3));
        $ccc_name = $ccc['name'];

        $pack_size = 0;
        //get drug information
        $drug = Drugcode::getDrug($uri->getSegment(2), $uri->getSegment(3));
        $data['commodity'] = '';
        $data['unit'] = '';
        $drug_map = '';
        if ($drug) {
            $data['commodity'] = $drug['drug'];
            $drug_map = $drug['map'];
            $data['unit'] = $drug['drugunit'];
            $pack_size = $drug['pack_size'];
        }
        $total_stock = 0;
        //get batch information
        $drug_batches = array();
        $today = date('Y-m-d');
        $facility_code = session()->get('facility');
        $batches = Drugcode::getDrugBatches($uri->getSegment(2), $uri->getSegment(3), $facility_code, $today);
        if ($batches) {//Check if batches exist
            foreach ($batches as $counter => $batch) {
                $drug_batches[$counter]['drug'] = $batches[$counter]['drugname'];
                $drug_batches[$counter]['packsize'] = $batches[$counter]['pack_size'];
                $drug_batches[$counter]['batchno'] = $batches[$counter]['batch_number'];
                $drug_batches[$counter]['balance'] = $batches[$counter]['balance'];
                $drug_batches[$counter]['expiry_date'] = $batches[$counter]['expiry_date'];
                $total_stock = $total_stock + $batches[$counter]['balance'];
            }
        }

        //Consumption
        $three_months_consumption = 0;
        $transaction_type = '';
        if (stripos($ccc_name, "pharmacy")) {
            $transaction_type = Transaction_Type::getTransactionType('dispense', 0);
            $transaction_type = $transaction_type['id'];
        } else if (stripos($ccc_name, "store")) {
            $transaction_type = Transaction_Type::getTransactionType('issue', 0);
            $transaction_type = $transaction_type['id'];
        }

        $consumption = Drug_Stock_Movement::getDrugConsumption($uri->getSegment(2), $facility_code, $uri->getSegment(3), $transaction_type);
        foreach ($consumption as $value) {
            $three_months_consumption += $value->total_out;
        }

        // echo "<pre>";print_r($data);die;
        //3 Months consumption using facility orders
        $data['maximum_consumption'] = number_format($three_months_consumption);
        $data['avg_consumption'] = number_format(($three_months_consumption) / 3);
        $monthly_consumption = number_format(($three_months_consumption) / 3);
        $min_consumption = $three_months_consumption * (0.5);
        $data['minimum_consumption'] = number_format($min_consumption);
        $data['stock_val'] = $uri->getSegment(3);
        $data['hide_sidemenu'] = '';
        $data['total_stock'] = $total_stock;
        $data['batches'] = $drug_batches;
        $data['hide_side_menu'] = '1';
        $data['store'] = $ccc_name;
        $data['drug_id'] = $uri->getSegment(2);
        $data['content_view'] = '\Modules\ADT\Views\\bin_card_v';

        $this->base_params($data);
    }

    public function getDrugTransactions($drug_id = '', $ccc_id = '') {
        $db = \Config\Database::connect();

        /* Added Limit as there are issues */
        ini_set("memory_limit", -1);
        $iDisplayStart = @$_GET['iDisplayStart'];
        $iDisplayLength = @$_GET['iDisplayLength'];
        $iSortCol_0 = @$_GET['iSortCol_0'];
        $iSortingCols = @$_GET['iSortingCols'];
        $sSearch = @$_GET['sSearch'];
        $sEcho = @$_GET['sEcho'];
        $where = "";

        //columns
        $aColumns = [
            'order_number',
            'transaction_date',
            't.name as transaction_type',
            't.effect',
            'batch_number',
            'd.name as destination_name',
            's.name as source_name',
            'source_destination',
            'expiry_date',
            'pack_size',
            'packs',
            'ds.quantity',
            'ds.quantity_out',
            'machine_code',
            'unit_cost',
            'amount'
        ];

        $builder = $db->table("drug_stock_movement as ds");

        // Paging
        if (isset($iDisplayStart) && $iDisplayLength != '-1') {
            $builder->limit($iDisplayLength, $iDisplayStart);
        }

        // Ordering
        if (isset($iSortCol_0)) {
            for ($i = 0; $i < intval($iSortingCols); $i++) {
                $iSortCol = @$_GET['iSortCol_' . $i];
                $bSortable = @$_GET['bSortable_' . intval($iSortCol)];
                $sSortDir = @$_GET['sSortDir_' . $i];

                if ($bSortable == 'true') {
                    $builder->orderBy($aColumns[intval($iSortCol)], $sSortDir);
                }
            }
        }
        if (isset($sSearch) && !empty($sSearch)) {
            $column_count = 0;
            //new columns
            $newColumns = ['order_number',
                'transaction_date',
                't.name',
                't.effect',
                'batch_number',
                'd.name',
                's.name',
                'source_destination',
                'expiry_date',
                'pack_size',
                'packs',
                'ds.quantity',
                'ds.quantity_out',
                'machine_code',
                'unit_cost',
                'amount'];
            for ($i = 0; $i < count($newColumns); $i++) {
                $bSearchable = @$_GET['bSearchable_' . $i];

                // Individual column filtering
                if (isset($bSearchable) && $bSearchable == 'true') {
                    if ($column_count == 0) {
                        $where .= "(";
                    } else {
                        $where .= " OR ";
                    }
                    $where .= $newColumns[$i] . " LIKE '%$sSearch%'";
                    $column_count++;
                }
            }
        }

        //data
        $builder->select('ds.*, t.name as transaction_type_name, s.name as source_name, d.name as destination_name, t.effect, dc.pack_size');
        $builder->join("drugcode as dc", "dc.id = ds.drug", "left");
        $builder->join("transaction_type as t", "t.id = ds.transaction_type", "left");
        $builder->join("drug_source as s", "s.id = ds.source_destination", "left");
        $builder->join("drug_destination as d", "d.id = ds.source_destination", "left");
        $builder->where("ds.drug", $drug_id);
        $builder->where("ds.ccc_store_sp", $ccc_id);
        //Filtering
        //search sql clause
        if ($where != "") {
            $where .= ")";
            $builder->where($where);
        }
        $builder->orderBy('ds.id', 'desc');
        $rResult = $builder->get();

        // Data set length after filtering
        $iFilteredTotal = $builder->countAll();

        // Total data set length
        $iTotal = Drug_Stock_Movement::where(['drug'=>$drug_id, 'ccc_store_sp'=>$ccc_id])->count();
        // Output
        $output = ['sEcho' => intval($sEcho),'iTotalRecords' => $iTotal,'iTotalDisplayRecords' => $iFilteredTotal, 'aaData' => []];

        //loop through data to change transaction type
        foreach ($rResult->getResult() as $drug_transaction) {
            $row = [];
            if ($drug_transaction->effect == 1) {
                //quantity_out & source (means adds stock to system)
                $transaction_type = $drug_transaction->transaction_type_name;
                $qty = $drug_transaction->quantity;
                if ($drug_transaction->source_name != "" || $drug_transaction->source_name != 0) {
                    $transaction_type = $drug_transaction->transaction_type_name . " (" . $drug_transaction->source_name . ")";
                } else if (!is_numeric($drug_transaction->Source_Destination)) {
                    $transaction_type = $drug_transaction->transaction_type_name . " (" . $drug_transaction->Source_Destination . ")";
                }
            } else {
                //quantity & destination (means removes stock from system)
                $transaction_type = $drug_transaction->transaction_type_name;
                $qty = $drug_transaction->quantity_out;
                if ($drug_transaction->destination_name != "" || $drug_transaction->destination_name != 0) {
                    $transaction_type = $drug_transaction->transaction_type_name . " (" . $drug_transaction->destination_name . ")";
                } else if (!is_numeric($drug_transaction->Source_Destination)) {
                    $transaction_type = $drug_transaction->transaction_type_name . " (" . $drug_transaction->Source_Destination . ")";
                }
            }

            $row[] = $drug_transaction->order_number;
            $row[] = date('d-M-Y', strtotime($drug_transaction->transaction_date));
            $row[] = $transaction_type;
            $row[] = $drug_transaction->batch_number;
            $row[] = date('d-M-Y', strtotime($drug_transaction->expiry_date));
            $row[] = $drug_transaction->pack_size;
            $row[] = $drug_transaction->packs;
            $row[] = $qty;
            if (!empty($drug_transaction->machine_code)) {
                $row[] = number_format($drug_transaction->machine_code);
            } else {
                $row[] = "";
            }
            $row[] = $drug_transaction->unit_cost;
            $row[] = $drug_transaction->amount;
            $output['aaData'][] = $row;
        }
        echo json_encode($output, JSON_PRETTY_PRINT);
    }

    public function stock_transaction($stock_type = 1) {
        error_reporting(1);
        $data['hide_side_menu'] = 1;
        $facility_code = session()->get('facility');
        $user_id = session()->get('user_id');
        $access_level = session()->get('user_indicator');
        if ($access_level == "facility_administrator") {
            $transaction_type = Transaction_Type::getAll();
        } else {
            $transaction_type = Transaction_Type::getAllNonAdjustments();
        }


        $drug_source = Drug_source::getAll();
        $facility_detail = facilities::getSupplier($facility_code);
        $drug_destination = Drug_Destination::getAll();
        //Check facility type(satelitte, standalone or central)
        $facility_type = Facilities::getType($facility_code);
        $get_list = array();
        $data['list_facility'] = "";
        if ($facility_type == 0) {//Satellite
            $central_code = facilities::getCentralCode($facility_code);
            $get_list = facilities::getCentralName($central_code);
            $data['list_facility'] = "Central Site";
        } else if ($facility_type == 1) {//Standalone
            $get_list = array();
            $data['list_facility'] = "";
        } else if ($facility_type > 1) {//Central
            $get_list = facilities::getSatellites($facility_code);
            $data['list_facility'] = "Satelitte Sites";
        }

        $name = CCC_store_service_point::getCCC($stock_type);

        $name = $name['name'];
        // dd($drug_source);




        $data['supplier_name'] = $facility_detail->supplier_name;
        $data['picking_lists'] = "";
        $data['get_list'] = $get_list;
        $data['user_id'] = $user_id;
        $data['ccc_stores'] = json_decode(session()->get("ccc_store"), TRUE);
        $data['facility'] = $facility_code;
        $data['stock_type'] = $stock_type;
        $data['transaction_types'] = $transaction_type;
        $data['drug_sources'] = $drug_source;
        $data['drug_destinations'] = $drug_destination;
        $data['store'] = strtoupper($name);
        $data['content_view'] = "\Modules\ADT\Views\\stock_transaction_v";
        $this->base_params($data);
    }

    public function sendemail($email) {

        $config['mailtype'] = "html";
        $config['protocol'] = "smtp";
        $config['smtp_host'] = "ssl://smtp.googlemail.com";
        $config['smtp_port'] = "465";
        $config['smtp_user'] = stripslashes('webadt.chai@gmail.com');
        $config['smtp_pass'] = stripslashes('WebAdt_052013');

        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->from('webadt.chai@gmail.com', "WEB_ADT CHAI");
        $this->email->to($email);
        $this->email->subject('Reciept of drugs');
        $this->email->message('Dear Sir/Madam we have recieved the drugs sent');

        if (@$this->email->send()) {
            echo "The email was successfully sent";
        } else {
            echo "The email was not sent";
        }
    }

    function loadRecord($id, $action = '') {
        $data = [];
        if ($action == 'export') {
            $this->export_pqmp($id);
        }

        if ($action == 'delete') {
            $this->db->query('delete from pqms where id = ' . $id);
            // redirect('inventory_management/pqmp');
            die;
        }
        $data['content_view'] = '\Modules\ADT\Views\\pqmp_v';
        $data['record_no'] = $id;
        $data['patient_id'] = $id;
        $data['hide_side_menu'] = 0;
        $data['pqmp_data'] = $this->db->query("SELECT p.*,co.county_name,su.sub_county_name,de.name designation , cou.name country
                                                FROM pqms p 
                                                LEFT JOIN pv_counties co ON p.county_id = co.id 
                                                LEFT JOIN pv_countries cou ON cou.id = p.country_of_origin
                                                LEFT JOIN pv_sub_counties su ON p.sub_county_id = su.id 
                                                LEFT JOIN pv_designations de ON p.designation_id = de.id WHERE p.id='$id'")->getResultArray();
        $this->base_params($data);
    }

    function loadAdrRecord($id, $action = '') {
        $data = [];
        if ($action == 'export') {
            $this->export_adr($id);
        }

        if ($action == 'delete') {
            $this->db->query('delete from adr_form where id = ' . $id);
            $this->db->query('delete from adr_form_details where adr_id = ' . $id);
            return redirect()->to(base_url() . '/adr/0/0');
            die;
        }
        $data['content_view'] = '\Modules\ADT\Views\\adr_v';
        $data['record_no'] = $id;
        $data['patient_id'] = $id;
        $data['hide_side_menu'] = 0;
        $data['diagnosis'] = Non_adherence_reasons::getAll();
        $data['adr_data'] = $this->db->query("SELECT p.*,co.county_name county_name,su.sub_county_name sub_county_name,de.name designation_d FROM adr_form p LEFT JOIN pv_counties co ON p.county = co.id LEFT JOIN pv_sub_counties su ON p.sub_county = su.id LEFT JOIN pv_designations de ON p.designation = de.id WHERE p.id='$id'")->getResultArray();
        $data['adr_details'] = $this->db->query("SELECT afd.id,afd.dose_id, afd.route_freq, afd.adr_id,afd.visitid,afd.dose,afd.route,d.value dose_unit, r.name route_name, f.name freq_name, afd.drug, afd.brand,afd.date_started,afd.date_stopped,afd.indication, afd.suspecteddrug FROM adr_form_details afd LEFT JOIN pv_doses d ON d.id = afd.dose_id LEFT JOIN pv_frequencies f ON f.id = afd.route_freq LEFT JOIN pv_routes r ON r.id = afd.route WHERE afd.adr_id='$id'")->getResultArray();
        $this->base_params($data);
    }

    public function pqmp($record_no = null, $action = null) {
        $id = DB::select('SELECT MAX(id) id FROM pqms');
        $newid = (int) $id[0]->id + 1;



        if ($this->request->getPost("facility_name")) {
            $pqmp_data = [
                'county_id' => $this->request->getPost('county_id'),
                'sub_county_id' => $this->request->getPost('sub_county_id'),
                'country_id' => $this->request->getPost('country_id'),
                'designation_id' => $this->request->getPost('designation_id'),
                'facility_name' => $this->request->getPost('facility_name'),
                'facility_code' => $this->request->getPost('facility_code'),
                'facility_address' => $this->request->getPost('facility_address'),
                'facility_phone' => $this->request->getPost('facility_phone'),
                'brand_name' => $this->request->getPost('brand_name'),
                'generic_name' => $this->request->getPost('generic_name'),
                'batch_number' => $this->request->getPost('batch_no'),
                'manufacture_date' => date('Y-m-d', strtotime($this->request->getPost('manufacture_date'))),
                'expiry_date' => date('Y-m-d', strtotime($this->request->getPost('expiry_date'))),
                'receipt_date' => date('Y-m-d', strtotime($this->request->getPost('receipt_date'))),
                'name_of_manufacturer' => $this->request->getPost('manufacturer_name'),
                'country_of_origin' => $this->request->getPost('country_id'),
                'supplier_name' => $this->request->getPost('supplier_name'),
                'supplier_address' => $this->request->getPost('supplier_address'),
                'product_formulation' => $this->request->getPost('product_formulation'),
                'product_formulation_specify' => $this->request->getPost('formulation_other'),
                'colour_change' => $this->request->getPost('colour_change'),
                'separating' => $this->request->getPost('separating'),
                'powdering' => $this->request->getPost('powdering'),
                'caking' => $this->request->getPost('caking'),
                'moulding' => $this->request->getPost('moulding'),
                'odour_change' => $this->request->getPost('odour_change'),
                'mislabeling' => $this->request->getPost('mislabeling'),
                'incomplete_pack' => $this->request->getPost('incomplete_pack'),
                'complaint_other' => $this->request->getPost('complaint_other'),
                'complaint_other_specify' => $this->request->getPost('complaint_other_specify'),
                'complaint_description' => $this->request->getPost('description'),
                'require_refrigeration' => $this->request->getPost('product_refrigiration'),
                'product_at_facility' => $this->request->getPost('product_availability'),
                'returned_by_client' => $this->request->getPost('product_returned'),
                'stored_to_recommendations' => $this->request->getPost('product_storage'),
                'other_details' => $this->request->getPost(''),
                'comments' => $this->request->getPost('comments'),
                'reporter_name' => $this->request->getPost('reporter_name'),
                'reporter_email' => $this->request->getPost(''),
                'contact_number' => $this->request->getPost('reporter_phone'),
            ];
            $builder = $this->db->table('pqms');
            $builder->where('id', $record_no);
            $builder->update($pqmp_data);
            $this->session->setFlashdata('pqmp_saved', 'Pharmacovigilance form was saved successfully!');

            return redirect()->to(base_url() . '/inventory_management/loadRecord/' . $record_no);
        }

        $data = [];
        $content_view = '\Modules\ADT\Views\\pqmp_list_v';

        if (@$record_no + 0 > 0) {
            $pqmp1_data = $this->db->query("SELECT p.*,co.county county_name,su.sub_county_name,de.name designation FROM pqms p INNER JOIN counties co ON p.county_id = co.id INNER JOIN sub_counties su ON p.sub_county_id = su.id INNER JOIN designations de ON p.designation_id = de.id");
            $content_view = '\Modules\ADT\Views\\pqmp_v';
            $data['hide_side_menu'] = 0;
        }

        $pqmp_data = $this->db->table('pqms')->orderBy('id', 'desc')->get();
        $data['pqmp_data'] = $pqmp_data->getResultArray();
        // dd($data['pqmp_data']); 




        if ($action == 'export') {
            $this->export_pqmp($record_no);
        }

        if ($action == 'delete') {
            $this->db->query('delete from pqms where id = ' . $record_no);
            return redirect()->to(base_url() . '/pqmp/0/0');
        }

        $data['facility_code'] = session()->get('facility');
        $data['facility_name'] = session()->get('facility_name');
        $data['facility_phone'] = session()->get('facility_phone');
        $data['record_no'] = $record_no;

        $dispensing_date = "";
        $data['last_regimens'] = "";
        $data['visits'] = "";
        $data['appointments'] = "";
        $dispensing_date = date('Y-m-d');

        $dated = NULL;
        $service_name = NULL;

        $data['dated'] = $dated;
        $data['patient_id'] = $record_no;
        $data['service_name'] = $service_name;
        $data['content_view'] = $content_view;
        $this->base_params($data);
    }

    // pqmp view list, view one, edit one
    public function new_pqmp($record_no = NULL) {
        $id = DB::select('SELECT MAX(id) id FROM pqms');
        $newid = (int) $id[0]->id + 1;
        if ($this->request->getPost("facility_name")) {
            $pqmp_data = array(
                'facility_name' => $this->request->getPost('facility_name'),
                'district_name' => $this->request->getPost('district_name'),
                'province_name' => $this->request->getPost('province_name'),
                'facility_address' => $this->request->getPost('facility_address'),
                'facility_phone' => $this->request->getPost('facility_phone'),
                'brand_name' => $this->request->getPost('brand_name'),
                'generic_name' => $this->request->getPost('generic_name'),
                'batch_no' => $this->request->getPost('batch_no'),
                'manufacture_date' => date('Y-m-d', strtotime($this->request->getPost('manufacture_date'))),
                'expiry_date' => date('Y-m-d', strtotime($this->request->getPost('expiry_date'))),
                'receipt_date' => date('Y-m-d', strtotime($this->request->getPost('receipt_date'))),
                'manufacturer_name' => $this->request->getPost('manufacturer_name'),
                'origin_county' => $this->request->getPost('origin_county'),
                'supplier_name' => $this->request->getPost('supplier_name'),
                'supplier_address' => $this->request->getPost('supplier_address'),
                'formulation_oral' => $this->request->getPost('formulation_oral'),
                'formulation_injection' => $this->request->getPost('formulation_injection'),
                'formulation_diluent' => $this->request->getPost('formulation_diluent'),
                'formulation_powdersuspension' => $this->request->getPost('formulation_powdersuspension'),
                'formulation_powderinjection' => $this->request->getPost('formulation_powderinjection'),
                'formulation_eyedrops' => $this->request->getPost('formulation_eyedrops'),
                'formulation_eardrops' => $this->request->getPost('formulation_eardrops'),
                'formulation_nebuliser' => $this->request->getPost('formulation_nebuliser'),
                'formulation_cream' => $this->request->getPost('formulation_cream'),
                'other_formulation' => $this->request->getPost('other_formulation'),
                'formulation_other' => $this->request->getPost('formulation_other'),
                'complaint_colour' => $this->request->getPost('complaint_colour'),
                'complaint_separating' => $this->request->getPost('complaint_separating'),
                'complaint_powdering' => $this->request->getPost('complaint_powdering'),
                'complaint_caking' => $this->request->getPost('complaint_caking'),
                'complaint_moulding' => $this->request->getPost('complaint_moulding'),
                'complaint_change' => $this->request->getPost('complaint_change'),
                'complaint_mislabeilng' => $this->request->getPost('complaint_mislabeilng'),
                'complaint_incomplete' => $this->request->getPost('complaint_incomplete'),
                'other_complaint' => $this->request->getPost('other_complaint'),
                'complaint_other' => $this->request->getPost('complaint_other'),
                'description' => $this->request->getPost('description'),
                'comments' => $this->request->getPost('comments'),
                'product_refrigiration' => $this->request->getPost('product_refrigiration'),
                'product_availability' => $this->request->getPost('product_availability'),
                'product_returned' => $this->request->getPost('product_returned'),
                'product_returned' => $this->request->getPost('product_returned'),
                'product_storage' => $this->request->getPost('product_storage'),
                'product_storage' => $this->request->getPost('product_storage'),
                'reporter_name' => $this->request->getPost('reporter_name'),
                'reporter_phone' => $this->request->getPost('reporter_phone'),
                'reporter_title' => $this->request->getPost('reporter_title'),
                'reporter_signature' => $this->request->getPost('reporter_signature')
            );
            $bulider = $this->db->table('pqmp');
            $bulider->insert($pqmp_data);
            //$this->db->insert('pqmp', $pqmp_data);
            $this->session->setFlashdata('pqmp_saved', 'Pharmacovigilance form was saved successfully!');

            return redirect()->to(base_url() . '/loadRecord/' . $record_no);


            die;
        }

        $data = array();
        $data['facility_code'] = session()->get('facility');
        $data['facility_address'] = session()->get('email');
        $data['facility_name'] = session()->get('facility_name');
        $data['facility_phone'] = session()->get('facility_phone');
        $data['drug_data'] = $this->getGenericName();


        $data['user_full_name'] = session()->get('full_name');
        $data['user_email'] = session()->get('Email_Address');
        $data['user_phone'] = session()->get('Phone_Number');


        $dispensing_date = "";
        $data['last_regimens'] = "";
        $data['visits'] = "";
        $data['uniqueid'] = $newid;
        $data['appointments'] = "";
        $dispensing_date = date('Y-m-d');





        $data['patient_id'] = $record_no;
        $data['hide_side_menu'] = 1;
        $data['content_view'] = "\Modules\ADT\Views\\pqmp_form_v";
        // var_dump($data);
        $this->base_params($data);
    }

    function getGenericName() {
        return Drugcode::all();
    }

    /* -----------------SAVE NEW PQM TO SYNCH WITH DATABASE PPB*------------------- */

    function save_pqm_for_synch() {
        error_reporting(E_ALL);

        $pmpq = array(
            'user_id' => session()->get("user_id"),
            'county_id' => $this->request->getPost('county_id'),
            'sub_county_id' => $this->request->getPost('sub_county_id'),
            'country_id' => $this->request->getPost('country_id'),
            'designation_id' => $this->request->getPost('designation_id'),
            'facility_name' => $this->request->getPost('facility_name'),
            'facility_code' => $this->request->getPost('facility_code'),
            'facility_address' => $this->request->getPost('facility_address'),
            'facility_phone' => $this->request->getPost('facility_phone'),
            'brand_name' => $this->request->getPost('brand_name'),
            'generic_name' => $this->request->getPost('generic_name'),
            'batch_number' => $this->request->getPost('batch_no'),
            'manufacture_date' => date('Y-m-d', strtotime($this->request->getPost('manufacture_date'))),
            'expiry_date' => date('Y-m-d', strtotime($this->request->getPost('expiry_date'))),
            'receipt_date' => date('Y-m-d', strtotime($this->request->getPost('receipt_date'))),
            'name_of_manufacturer' => $this->request->getPost('manufacturer_name'),
            'country_of_origin' => $this->request->getPost('country_id'),
            'supplier_name' => $this->request->getPost('supplier_name'),
            'supplier_address' => $this->request->getPost('supplier_address'),
            'product_formulation' => $this->request->getPost('product_formulation'),
            'product_formulation_specify' => $this->request->getPost('formulation_other'),
            'colour_change' => $this->request->getPost('colour_change'),
            'separating' => $this->request->getPost('separating'),
            'powdering' => $this->request->getPost('powdering'),
            'caking' => $this->request->getPost('caking'),
            'moulding' => $this->request->getPost('moulding'),
            'odour_change' => $this->request->getPost('odour_change'),
            'mislabeling' => $this->request->getPost('mislabeling'),
            'incomplete_pack' => $this->request->getPost('incomplete_pack'),
            'complaint_other' => $this->request->getPost('complaint_other'),
            'complaint_other_specify' => $this->request->getPost('complaint_other_specify'),
            'complaint_description' => $this->request->getPost('description'),
            'require_refrigeration' => $this->request->getPost('product_refrigiration'),
            'product_at_facility' => $this->request->getPost('product_availability'),
            'returned_by_client' => $this->request->getPost('product_returned'),
            'stored_to_recommendations' => $this->request->getPost('product_storage'),
            'other_details' => $this->request->getPost(''),
            'comments' => $this->request->getPost('comments'),
            'reporter_name' => $this->request->getPost('reporter_name'),
            'reporter_email' => $this->request->getPost(''),
            'contact_number' => $this->request->getPost('reporter_phone'),
            'emails' => $this->request->getPost(''),
            'submitted' => 1,
            'active' => 1,
            'device' => 1,
            'notified' => 1,
            'created' => date('Y-m-d'),
            'modified' => date('Y-m-d')
        );


        $builder = $this->db->table('pqms');
        $builder->insert($pmpq);
        // $this->output->enable_profiler(TRUE);

        $this->session->setFlashdata('pqmp_saved', 'Pharmacovigilance SPQMs was saved successfully!');

        return redirect()->to(base_url() . '/pqmp/0/0');
    }

    function _p($val) {
        return $this->request->getPost($val);
    }

    public function adr_view($record_no = null) {
        $dated = '';
        $id = DB::select('SELECT MAX(id) id FROM adr_form');
        $newid = (int) $id[0]->id + 1;
        if ($_POST) {
            $adr = array(
                'id' => $newid,
                'report_title' => $this->_p('report_title'),
                'institution_name' => $this->_p('institution'),
                'institution_code' => $this->_p('institutioncode'),
                'county' => $this->_p('county_id'),
                'sub_county' => $this->_p('sub_county_id'),
                'address' => $this->_p('address'),
                'contact' => $this->_p('contact'),
                'patient_name' => $this->_p('patientname'),
                'ip_no' => $this->_p('ip_no'),
                'dob' => $this->_p('dob'),
                'patient_address' => $this->_p('patientaddress'),
                'ward_clinic' => $this->_p('clinic'),
                'gender' => $this->_p('gender'),
                'is_alergy' => $this->_p('allergy'),
                'alergy_desc' => $this->_p('allergydesc'),
                'is_pregnant' => $this->_p('pregnancystatus'),
                'weight' => $this->_p('patientweight'),
                'height' => $this->_p('patientheight'),
                'diagnosis' => $this->_p('diagnosis'),
                'reaction_description' => $this->_p('reaction'),
                // 'severity' => (isset($this->_p('severity'))) ? $this->_p('severity') : false,
                'severity' => $this->_p('severity'),
                'action_taken' => $this->_p('action'),
                'outcome' => $this->_p('outcome'),
                'reaction_casualty' => $this->_p('casuality'),
                'other_comment' => $this->_p('othercomment'),
                'reporting_officer' => $this->_p('officername'),
                'reporting_officer' => $this->_p('reportingdate'),
                'email_address' => $this->_p('officeremail'),
                'office_phone' => $this->_p('officerphone'),
                'designation' => $this->_p('designation_id'),
                'signature' => $this->_p('officersignature')
            );

            $builder = $this->db->table('adr_form');
            $builder->insert($adr);
            //$adr_id = $this->db->insert_id();
            if (count($_POST['drug_name']) > 0) {

                foreach ($_POST['drug_name'] as $key => $drug) {
                    $adr_details = array(
                        'adr_id' => $newid,
                        'drug' => $_POST['drug_name'][$key],
                        'brand' => $_POST['brand_name'][$key],
                        'dose_id' => $_POST['dose_id'][$key],
                        'route' => $_POST['route_id'][$key],
                        'dose' => $_POST['dose'][$key],
                        'route_freq' => $_POST['frequency_id'][$key],
                        'date_started' => $_POST['dispensing_date'][$key],
                        'date_stopped' => $_POST['date_stopped'][$key],
                        'indication' => $_POST['indication'][$key],
                        'suspecteddrug' => (isset($_POST['suspecteddrug'][$key])) ? $_POST['suspecteddrug'][$key] : false,
                        'visitid' => $_POST['visitid'][$key]
                    );
                    $builder2 = $this->db->table('adr_form_details');
                    $builder2->insert($adr_details);
                }
                return redirect()->to(base_url() . '/adr/0/0');
            } else {
                echo "No drugs selected";
                // no drugs selected
                // Form saved successfully
                die;
            }

            die;
        }
        $dispense_date = '';

        $facility_code = session()->get('facility');

        $data = array();
        $dispensing_date = "";
        $data['last_regimens'] = "";
        $data['visits'] = "";
        $data['appointments'] = "";
        $data['uniqueid'] = $newid;
        $data['user_full_name'] = session()->get('full_name');
        $data['user_email'] = session()->get('Email_Address');
        $data['user_phone'] = session()->get('Phone_Number');
        // last visit id by patient
        $sql = "select dispensing_date from vw_patient_list vpv,patient_visit pv WHERE pv.patient_id = vpv.ccc_number and vpv.patient_id = $record_no order by dispensing_date desc  limit 1";
        $query = $this->db->query($sql);
        if ($query->getResultArray()) {
            $dispense_date = $query->getResultArray()[0]['dispensing_date'];
        }

        // Facility Details
        $sql = "select * from facilities WHERE facilitycode = $facility_code";
        $query = $this->db->query($sql);
        if ($query->getResultArray()) {
            $data['facility_details'] = $query->getResultArray()[0];
        }

        $sql = "select * from vw_patient_list WHERE patient_id = $record_no";
        $query = $this->db->query($sql);
        if ($query->getResultArray()) {
            $data['patient_details'] = $query->getResultArray()[0];
        }

        //Patient History

        $sql = "select  v_v.dispensing_date,
		v_v.visit_purpose_name AS visit, 
		v_v.dose, 
		v_v.duration, 
		v_v.patient_visit_id AS record_id, 
		D.drug, 
		v_v.quantity, 
		v_v.current_weight, 
		R.regimen_desc, 
		v_v.batch_number, 
		v_v.pill_count, 
		v_v.adherence, 
		v_v.indication, 
		v_v.frequency, 
		v_v.user,
                do.value,
		v_v.regimen_change_reason AS regimen_change_reason 
		from v_patient_visits as v_v
		INNER JOIN regimen as R ON R.id = v_v.current_regimen
		INNER JOIN drugcode as D ON D.id = v_v.drug_id
                LEFT JOIN dose as do ON do.id = D.unit
		WHERE v_v.id = $record_no
		AND v_v.pv_active = 1
		AND dispensing_date = '$dispense_date'
		GROUP BY v_v.drug_id,v_v.dispensing_date
		ORDER BY v_v.dispensing_date DESC";

        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        if ($results) {
            $data['patient_visits'] = $results;
            // dd($results);
        } else {
            $data['patient_visits'] = "";
        }

        $dispensing_date = date('Y-m-d');

        $sql = "select ps.name as patient_source,p.patient_number_ccc,FLOOR(DATEDIFF(CURDATE(),p.dob)/365) as age, LOWER(rst.name) as service_name , p.clinicalappointment from patient p 
		LEFT JOIN patient_source ps ON ps.id = p.source
		LEFT JOIN regimen_service_type rst ON rst.id = p.service
		where p.id='$record_no' and facility_code='$facility_code'
		";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();

        if ($results) {
            $patient_no = $results[0]['patient_number_ccc'];
            $age = @$results[0]['age'];
            $service_name = $results[0]['service_name'];
            $data['results'] = $results;
        }


        $sql = "SELECT *
		FROM patient_visit pv
		left join dose d on pv.dose = d.name
		left join drugcode dc on pv.drug_id = dc.id
		WHERE patient_id = '$patient_no'
		ORDER BY dispensing_date DESC";

        $query = $this->db->query($sql);
        $results = $query->getResultArray();

        $username = (session()->get('username'));
        $sql = "select ccc_store_sp from users where Username = '$username'";
        $query = $this->db->query($sql);
        $store_results = $query->getResultArray();
        if ($store_results) {
            $data['ccc_store'] = $store_results[0]['ccc_store_sp'];
            // $data['ccc_store'] = $this -> session -> userdata('ccc_store')[0]['id'];
        }
        //$data['diagnosis'] = $this->db->get('drug_classification')->result();

        $data['diagnosis'] = $data['non_adherence_reasons'] = Non_adherence_reasons::getAll();
        //dd($data['diagnosis']);
        $data['regimen_changes'] = Regimen_change_purpose::getAllHydrated();
        $data['dated'] = $dated;
        $data['patient_id'] = $record_no;
        $data['service_name'] = $service_name;
        $data['purposes'] = \Modules\ADT\Models\VisitPurpose::getActive();
        $data['patient_appointment'] = $results;
        $data['hide_side_menu'] = 1;
        $data['content_view'] = "\Modules\ADT\Views\\patients/dispense_adr_v";
        $this->base_params($data);
    }

    public function adr($record_no = null, $action = null) {


        if ($_POST) {
            // adr_form

            $adr = array(
                'report_title' => $this->_p('report_title'),
                'institution_name' => $this->_p('institution'),
                'institution_code' => $this->_p('institutioncode'),
                'county' => $this->_p('county_id'),
                'sub_county' => $this->_p('sub_county_id'),
                'address' => $this->_p('address'),
                'contact' => $this->_p('contact'),
                'patient_name' => $this->_p('patientname'),
                'ip_no' => $this->_p('ip_no'),
                'dob' => $this->_p('dob'),
                'patient_address' => $this->_p('patientaddress'),
                'ward_clinic' => $this->_p('clinic'),
                'gender' => $this->_p('gender'),
                'is_alergy' => $this->_p('allergy'),
                'alergy_desc' => $this->_p('allergydesc'),
                'is_pregnant' => $this->_p('pregnancystatus'),
                'weight' => $this->_p('patientweight'),
                'height' => $this->_p('patientheight'),
                'diagnosis' => $this->_p('diagnosis'),
                'reaction_description' => $this->_p('reaction'),
                'severity' => $this->_p('severity'),
                'action_taken' => $this->_p('action'),
                'outcome' => $this->_p('outcome'),
                'reaction_casualty' => $this->_p('casuality'),
                'other_comment' => $this->_p('othercomment'),
                'reporting_officer' => $this->_p('officername'),
                'reporting_date' => $this->_p('reportingdate'),
                'email_address' => $this->_p('officeremail'),
                'office_phone' => $this->_p('officerphone'),
                'designation' => $this->_p('officerdesignation'),
                'signature' => $this->_p('officersignature')
            );

            $builder = $this->db->table('adr_form');
            $builder->where('id', $record_no);
            $builder->update($adr);




            if (count($_POST['drug_name']) > 0) {
                foreach ($_POST['drug_name'] as $key => $drug) {
                    $adr_details = array(
                        'id' => $_POST['adr_id'][$key],
                        'drug' => $_POST['drug_name'][$key],
                        'brand' => $_POST['brand_name'][$key],
                        'dose_id' => $_POST['dose_id'][$key],
                        'route' => $_POST['route_id'][$key],
                        'dose' => $_POST['dose'][$key],
                        'route_freq' => $_POST['frequency_id'][$key],
                        'date_started' => $_POST['dispensing_date'][$key],
                        'date_stopped' => $_POST['date_stopped'][$key],
                        'indication' => $_POST['indication'][$key],
                        'suspecteddrug' => @$this->_p('suspecteddrug')[$key],
                    );

                    $builder = $this->db->table('adr_form_details');
                    $builder->where('id', $_POST['adr_id'][$key]);
                    $builder->update($adr_details);

                    $builder2 = $this->db->table('adr_form_details');
                    $builder2->where('id', $_POST['adr_id'][$key]);
                    $builder2->update($adr_details);
                }

                return redirect()->to(base_url() . '/inventory_management/loadAdrRecord/' . $record_no);
            } else {
                echo "No drugs selected";
                // no drugs selected
                // Form saved successfully
                die;
            }

            die;
        }

        $six_months_ago_date = date('Y-m-d', strtotime('-6 month'));
        $data = array();
        $sql = "SELECT p.id,pv.patient_id, first_name  ,last_name
                                        FROM patient_visit pv ,patient p
                                        where pv.patient_id = p.patient_number_ccc
                                        and dispensing_date >= '$six_months_ago_date'
                                        group by  p.id,pv.patient_id, p.first_name  ,p.last_name
                                        ";
        $patients = DB::select($sql);
        $data['patients_arr'] = BaseModel::resultSet($patients);
        $content_view = '\Modules\ADT\Views\\adr_list_v';
        // if ($record_no + 0 > 0) {
        //$this->db->where('adr_form.id', $record_no);
        //$content_view = '\Modules\ADT\Views\\adr_v';
        // $data['hide_side_menu'] = 0;
        // $this->db->join('adr_form_details', 'adr_form.id = adr_form_details.adr_id', 'left');
        // $this->db->join('patient_visit', 'adr_form_details.visitid = patient_visit.id ', 'left');
        //$this->db->join('drugcode', 'patient_visit.drug_id = drugcode.id', 'left');
        // $this->db->select('*,adr_form_detail.id as adr_details_id');
        // }


        $adr_data = DB::table('adr_form')->get();
        $data['adr_data'] = BaseModel::resultSet($adr_data);


        $data['record_no'] = $record_no;
        $data['facility_code'] = session()->get('facility');
        $data['facility_name'] = session()->get('facility_name');
        $data['facility_phone'] = session()->get('facility_phone');

        $dispensing_date = "";
        $data['last_regimens'] = "";
        $data['visits'] = "";
        $data['appointments'] = "";
        $dispensing_date = date('Y-m-d');
        $data['diagnosis'] = DB::table('drug_classification')->get();
        $dated = NULL;
        $service_name = NULL;

        $data['dated'] = $dated;
        $data['patient_id'] = $record_no;
        $data['service_name'] = $service_name;
        $data['content_view'] = $content_view;
        $this->base_params($data);
    }

    function deleteAdr($recordno) {
      
        $this->db->query("DELETE FROM adr_form WHERE id='$recordno'");
        return redirect()->to(base_url() . '/adr/0/0');
    }

    public function export_pqmp($id) {

        $adr = $this->db->query("SELECT p.*,co.county_name,su.sub_county_name,de.name designation , cou.name country ".
                    "FROM pqms p ".
                    "INNER JOIN pv_counties co ON p.county_id = co.id ".
                    "INNER JOIN pv_countries cou ON cou.id = p.country_of_origin ".
                    "INNER JOIN pv_sub_counties su ON p.sub_county_id = su.id ".
                    "INNER JOIN pv_designations de ON p.designation_id = de.id WHERE p.id='".$id."'")->getResultArray();





       // $this->load->library('PHPExcel');
        $dir = "assets/download";

        $inputFileType = 'Excel5';
        $inputFileName = 'assets/templates/moh_forms/PQMP_form.xls';
        
        $objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);



        $objPHPExcel->getActiveSheet()->SetCellValue('D7', $adr[0]['facility_name']);
        $objPHPExcel->getActiveSheet()->SetCellValue('J7', $adr[0]['county_name']);
        $objPHPExcel->getActiveSheet()->SetCellValue('Q7', $adr[0]['sub_county_name']);
        $objPHPExcel->getActiveSheet()->SetCellValue('D8', $adr[0]['facility_address']);
        $objPHPExcel->getActiveSheet()->SetCellValue('J8', $adr[0]['facility_phone']);

        $objPHPExcel->getActiveSheet()->SetCellValue('F10', $adr[0]['brand_name']);
        $objPHPExcel->getActiveSheet()->SetCellValue('F11', $adr[0]['batch_number']);
        $objPHPExcel->getActiveSheet()->SetCellValue('F12', $adr[0]['name_of_manufacturer']);
        $objPHPExcel->getActiveSheet()->SetCellValue('F13', $adr[0]['supplier_name']);
        $objPHPExcel->getActiveSheet()->SetCellValue('P10', $adr[0]['generic_name']);
        $objPHPExcel->getActiveSheet()->SetCellValue('L11', $adr[0]['manufacture_date']);
        $objPHPExcel->getActiveSheet()->SetCellValue('P11', $adr[0]['expiry_date']);
        $objPHPExcel->getActiveSheet()->SetCellValue('T11', $adr[0]['receipt_date']);
        $objPHPExcel->getActiveSheet()->SetCellValue('P12', $adr[0]['country']);
        $objPHPExcel->getActiveSheet()->SetCellValue('L13', $adr[0]['supplier_address']);

        $objPHPExcel->getActiveSheet()->SetCellValue('F15', ($adr[0]['product_formulation'] == 'Oral tablets / capsules') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('F16', ($adr[0]['product_formulation'] == 'Oral suspension / syrup') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('F17', ($adr[0]['product_formulation'] == 'Injection') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('F18', ($adr[0]['product_formulation'] == 'Diluent') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('F19', ($adr[0]['product_formulation'] == 'Powder for Reconstitution of Suspension') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('F20', ($adr[0]['product_formulation'] == 'Powder for Reconstitution of Injection') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('F21', ($adr[0]['product_formulation'] == 'Eye drops') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('F22', ($adr[0]['product_formulation'] == 'Ear drops') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('F23', ($adr[0]['product_formulation'] == 'Nebuliser solution') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('F24', ($adr[0]['product_formulation'] == 'Cream / Ointment / Liniment / Paste') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('F24', ($adr[0]['product_formulation'] == 'Cream / Ointment / Liniment / Paste') ? 'Yes' : 'No');
        // $objPHPExcel -> getActiveSheet() -> SetCellValue('B10', $adr[0]['other_formulation']);

        $objPHPExcel->getActiveSheet()->SetCellValue('P15', ($adr[0]['colour_change'] == '1') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('P16', ($adr[0]['separating'] == '1') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('P17', ($adr[0]['powdering'] == '1') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('P18', ($adr[0]['caking'] == '1') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('P19', ($adr[0]['moulding'] == '1') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('P20', ($adr[0]['odour_change'] == '1') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('P21', ($adr[0]['mislabeling'] == '1') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('P22', ($adr[0]['incomplete_pack'] == '1') ? 'Yes' : 'No');
        $objPHPExcel->getActiveSheet()->SetCellValue('P23', ($adr[0]['complaint_other'] == '1') ? 'Yes' : 'No');
        //$objPHPExcel->getActiveSheet()->SetCellValue('P24', ($adr[0]['complaint_other'] == '1') ? 'Yes' : 'No');

        $objPHPExcel->getActiveSheet()->SetCellValue('B26', $adr[0]['complaint_description']);

        $objPHPExcel->getActiveSheet()->SetCellValue('I28', ($adr[0]['require_refrigeration'] == 'No') ? 'No' : 'Yes');
        $objPHPExcel->getActiveSheet()->SetCellValue('I29', ($adr[0]['product_at_facility'] == 'No') ? 'No' : 'Yes');
        $objPHPExcel->getActiveSheet()->SetCellValue('I30', ($adr[0]['returned_by_client'] == 'No') ? 'No' : 'Yes');
        $objPHPExcel->getActiveSheet()->SetCellValue('I31', ($adr[0]['stored_to_recommendations'] == 'No') ? 'no' : 'Yes');

        $objPHPExcel->getActiveSheet()->SetCellValue('B33', $adr[0]['comments']);

        $objPHPExcel->getActiveSheet()->SetCellValue('D35', $adr[0]['reporter_name']);
        $objPHPExcel->getActiveSheet()->SetCellValue('D36', $adr[0]['designation']);

        ob_start();

        $original_filename = strtoupper('pqmp') . "_" . $id . ".xls";

        $filename = $dir . "/" . urldecode($original_filename);
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, "Xlsx");
        $objWriter->save($filename);
        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);
        if (file_exists($filename)) {
            $filename = str_replace("#", "%23", $filename);
            return $this->response->download($filename, null);
        }
    }

    public function export_adr($id) {

        $adr = $this->db->query("SELECT p.*,co.county_name county_name,su.sub_county_name sub_county_name,de.name designation_d FROM adr_form p LEFT JOIN pv_counties co ON p.county = co.id LEFT JOIN pv_sub_counties su ON p.sub_county = su.id LEFT JOIN pv_designations de ON p.designation = de.id WHERE p.id='$id'")->getResultArray();
        $adr_details = $this->db->query("SELECT afd.id,afd.dose_id, afd.route_freq, afd.adr_id,afd.visitid,afd.dose,afd.route,d.value dose_unit, r.name route_name, f.name freq_name, afd.drug, afd.brand,afd.date_started,afd.date_stopped,afd.indication, afd.suspecteddrug FROM adr_form_details afd LEFT JOIN pv_doses d ON d.id = afd.dose_id LEFT JOIN pv_frequencies f ON f.id = afd.route_freq LEFT JOIN pv_routes r ON r.id = afd.route WHERE afd.adr_id='$id'")->getResultArray();

        $dir = "assets/download";
        $inputFileType = 'Excel5';
        $inputFileName = 'assets/templates/moh_forms/ADR_form.xls';
        $objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFileName);

        $objPHPExcel->getActiveSheet()->SetCellValue('B10', $adr[0]['institution_name']);
        $objPHPExcel->getActiveSheet()->SetCellValue('I10', $adr[0]['institution_code']);
        $objPHPExcel->getActiveSheet()->SetCellValue('B12', $adr[0]['address']);
        $objPHPExcel->getActiveSheet()->SetCellValue('I12', $adr[0]['contact']);

        $objPHPExcel->getActiveSheet()->SetCellValue('B15', $adr[0]['patient_name']);
        $objPHPExcel->getActiveSheet()->SetCellValue('I15', $adr[0]['ip_no']);
        $objPHPExcel->getActiveSheet()->SetCellValue('O15', $adr[0]['dob']);
        $objPHPExcel->getActiveSheet()->SetCellValue('B17', $adr[0]['patient_address']);
        $objPHPExcel->getActiveSheet()->SetCellValue('I17', $adr[0]['ward_clinic']);
        $objPHPExcel->getActiveSheet()->SetCellValue('P16', $adr[0]['gender']);
        $objPHPExcel->getActiveSheet()->SetCellValue('E18', $adr[0]['is_alergy']);
        $objPHPExcel->getActiveSheet()->SetCellValue('B22', $adr[0]['alergy_desc']);
        $objPHPExcel->getActiveSheet()->SetCellValue('L18', $adr[0]['is_pregnant']);
        $objPHPExcel->getActiveSheet()->SetCellValue('O19', $adr[0]['weight']);
        $objPHPExcel->getActiveSheet()->SetCellValue('O21', $adr[0]['height']);
        $objPHPExcel->getActiveSheet()->SetCellValue('B24', $adr[0]['diagnosis']);
        $objPHPExcel->getActiveSheet()->SetCellValue('B28', $adr[0]['reaction_description']);

        $objPHPExcel->getActiveSheet()->SetCellValue('M39', $adr[0]['action_taken']);
        $objPHPExcel->getActiveSheet()->SetCellValue('G39', $adr[0]['severity']);
        $objPHPExcel->getActiveSheet()->SetCellValue('G45', $adr[0]['outcome']);
        $objPHPExcel->getActiveSheet()->SetCellValue('O45', $adr[0]['casuality']);


        $objPHPExcel->getActiveSheet()->SetCellValue('B53', $adr[0]['other_comment']);
        $objPHPExcel->getActiveSheet()->SetCellValue('B58', $adr[0]['reporting_officer']);
        $objPHPExcel->getActiveSheet()->SetCellValue('J58', $adr[0]['datecreated']);
        $objPHPExcel->getActiveSheet()->SetCellValue('B60', $adr[0]['email_address']);
        $objPHPExcel->getActiveSheet()->SetCellValue('J60', $adr[0]['office_phone']);
        $objPHPExcel->getActiveSheet()->SetCellValue('B62', $adr[0]['designation_d']);
        $objPHPExcel->getActiveSheet()->SetCellValue('J62', $adr[0]['signature']);
        // $objPHPExcel -> getActiveSheet() -> SetCellValue('I12', $adr[0]['severity']);

        $row = 33;
        for ($i = 0; $i < count($adr_details); $i++) {
            $objPHPExcel->getActiveSheet()->SetCellValue('C' . $row, $adr_details[$i]['drug']);
            $objPHPExcel->getActiveSheet()->SetCellValue('E' . $row, $adr_details[$i]['dose'] . $adr_details[$i]['dose_unit']);
            $objPHPExcel->getActiveSheet()->SetCellValue('F' . $row, $adr_details[$i]['route_name'] . " " . $adr_details[$i]['freq_name']);
            $objPHPExcel->getActiveSheet()->SetCellValue('I' . $row, $adr_details[$i]['date_started']);
            $objPHPExcel->getActiveSheet()->SetCellValue('K' . $row, $adr_details[$i]['date_stopped']);
            $objPHPExcel->getActiveSheet()->SetCellValue('M' . $row, $adr_details[$i]['indication']);
            $objPHPExcel->getActiveSheet()->SetCellValue('O' . $row, ($adr_details[$i]['suspecteddrug'] == '1') ? 'yes' : 'no');
            $row = $row + 1;
        }

        ob_start();
        // $period_start = date("F-Y", strtotime($period_start));
        $original_filename = "" . strtoupper('adr') . "-" . $adr[0]['id'] . ".xls";


        $filename = $dir . "/" . urldecode($original_filename);
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, "Xlsx");
        $objWriter->save($filename);
        $objPHPExcel->disconnectWorksheets();
        unset($objPHPExcel);
        if (file_exists($filename)) {
            $filename = str_replace("#", "%23", $filename);
            redirect($filename);
        }
    }

    public function getStockDrugs() {
        $stock_type = $_POST['stock_type'];
        $facility_code = session()->get('facility');

        $drugs_sql = $this->db->query("SELECT DISTINCT(d.id),d.drug FROM drugcode d LEFT JOIN drug_stock_balance dsb on dsb.drug_id=d.id WHERE dsb.facility_code='$facility_code' AND dsb.stock_type='$stock_type' AND dsb.balance>0 AND dsb.expiry_date>=CURDATE() AND d.enabled='1' ORDER BY d.drug asc");
        $drugs_array = $drugs_sql->getResultArray();
        echo json_encode($drugs_array);
    }

    public function getAllDrugs() {
        $facility_code = session()->get('facility');
        $drugs_sql = $this->db->query("SELECT DISTINCT(d.id),d.drug FROM drugcode d  WHERE d.enabled='1' ORDER BY d.drug asc");
        $drugs_array = $drugs_sql->getResultArray();
        echo json_encode($drugs_array);
    }

    public function getBacthes() {
        $facility_code = session()->get('facility');
        $stock_type = $_POST['stock_type'];
        $selected_drug = $_POST["selected_drug"];
        $sql = "SELECT  
		DISTINCT d.pack_size,
		d.comment,
		d.duration,
		d.quantity,
		u.Name,
		dsb.batch_number,
		dsb.expiry_date,
		d.dose as dose,
		do.Name as dose_id 
		FROM drugcode d 
		LEFT JOIN drug_stock_balance dsb ON d.id=dsb.drug_id 
		LEFT JOIN drug_unit u ON u.id=d.unit 
		LEFT JOIN dose do ON d.dose=do.id  
		WHERE d.enabled=1 
		AND dsb.facility_code='$facility_code' 
		AND dsb.stock_type='$stock_type' 
		AND dsb.drug_id='$selected_drug' 
		AND dsb.balance > 0 
		AND dsb.expiry_date > CURDATE() 
		ORDER BY dsb.expiry_date ASC";
        $batches_array = DB::select($sql);
        echo json_encode($batches_array);
    }

    public function getBacthDetails() {
        $facility_code = session()->get('facility');
        $stock_type = $_POST["stock_type"];
        $selected_drug = $_POST["selected_drug"];
        $batch_selected = $_POST["batch_selected"];
        $sql = "SELECT 
		dsb.balance, 
		dsb.expiry_date 
		FROM drug_stock_balance dsb  
		WHERE dsb.facility_code = '$facility_code' 
		AND dsb.stock_type = '$stock_type' 
		AND dsb.drug_id = '$selected_drug' 
		AND dsb.batch_number = '$batch_selected' 
		AND dsb.balance > 0 
		AND dsb.expiry_date > CURDATE() 
		ORDER BY dsb.expiry_date ASC
		LIMIT 1";
        $batch_sql = $this->db->query($sql);
        $batches_array = $batch_sql->getResultArray();
        echo json_encode($batches_array);
    }

    public function getAllBacthDetails() {
        $facility_code = session()->get('facility');
        $stock_type = $this->request->getPost("stock_type");
        $selected_drug = $this->request->getPost("selected_drug");
        $batch_selected = $this->request->getPost("batch_selected");
        $sql = "SELECT 
		dsb.balance, 
		dsb.expiry_date 
		FROM drug_stock_balance dsb  
		WHERE dsb.facility_code='$facility_code' 
		AND dsb.stock_type='$stock_type' 
		AND dsb.drug_id='$selected_drug' 
		AND dsb.batch_number='$batch_selected'  
		ORDER BY last_update DESC,dsb.expiry_date ASC 
		LIMIT 1";
        $batch_sql = $this->db->query($sql);
        $batches_array = $batch_sql->getResultArray();
        echo json_encode($batches_array);
    }

    //Get balance details
    public function getBalanceDetails() {
        $facility_code = session()->get('facility');
        $stock_type = $this->request->getPost("stock_type");
        $selected_drug = $this->request->getPost("selected_drug");
        $batch_selected = $this->request->getPost("batch_selected");
        $expiry_date = $this->request->getPost("expiry_date");
        $sql = "SELECT 
		dsb.balance, 
		dsb.expiry_date 
		FROM drug_stock_balance dsb  
		WHERE dsb.facility_code = '$facility_code' 
		AND dsb.stock_type = '$stock_type' 
		AND dsb.drug_id = '$selected_drug' 
		AND dsb.batch_number = '$batch_selected' 
		AND dsb.balance > 0 
		AND dsb.expiry_date > CURDATE() 
		AND dsb.expiry_date='$expiry_date' 
		ORDER BY last_update DESC,dsb.expiry_date ASC 
		LIMIT 1";
        $batch_sql = $this->db->query($sql);
        $batches_array = $batch_sql->getResultArray();
        echo json_encode($batches_array);
    }

    public function getDrugDetails() {
        $selected_drug = $_POST["selected_drug"];
        $sql = "SELECT 
		d.pack_size,
		u.Name 
		FROM drugcode d 
		LEFT JOIN drug_unit u ON u.id=d.unit 
		WHERE d.enabled=1 
		AND d.id='$selected_drug'";
        $drug_details_sql = $this->db->query($sql);
        $drug_details_array = $drug_details_sql->getResultArray();
        echo json_encode($drug_details_array);
    }

    public function save() {
        /*
         * Get posted data from the client
         */
        $balance = "";
        $facility = session()->get("facility");
        // $facility_detail = Facilities::getSupplier($facility);
        // $supplier_name = $facility_detail->supplier->name;
        $get_user = session()->get("user_id");
        $cdrr_id = $this->post('cdrr_id');
        $get_qty_choice = $this->post('quantity_choice');
        $get_qty_out_choice = $this->post('quantity_out_choice');
        $get_source = $this->post('source');
        $get_source_name = $this->post('source_name');
        $get_destination_name = $this->post('destination_name');
        $get_destination = $this->post('destination');
        $get_transaction_date = date('Y-m-d', strtotime($this->post('transaction_date')));
        $get_ref_number = $this->post('reference_number');
        $get_transaction_type = $this->post('transaction_type');
        $transaction_type_name = $this->post('trans_type');
        $transaction_effect = $this->post('trans_effect');
        $get_drug_id = $this->post('drug_id');
        $get_batch = $this->post('batch');
        $get_expiry = $this->post('expiry');
        $get_packs = $this->post('packs');
        $get_qty = $this->post('quantity');
        $get_available_qty = $this->post('available_qty');
        $get_unit_cost = $this->post('unit_cost');
        $get_amount = $this->post('amount');
        $get_comment = $this->post('comment');
        $get_stock_type = $this->post('stock_type');
        $stock_type_name = $this->post('stock_transaction'); //Name of kind of transaction being carried
        $all_drugs_supplied = $this->post('all_drugs_supplied');
        $time_stamp = $this->post('time_stamp');
        $email = $this->post('emailaddress');
        $balance = 0;
        $pharma_balance = 0;
        $store_balance = 0;
        $sql_queries = "";
        $source_destination = $this->post('source_destination');
        $check_optgroup = $this->post('optgroup'); //Check if store selected as source or destination
        $source_dest_type = '';
        $running_balance = 0;
        $other_running_balance = 0; //For other store
        // If email is not empty
        if ($email != "") {
            $this->sendemail($email);
        }


        // STEP 1, GET BALANCES FROM DRUG STOCK BALANCE TABLE
        //Get running balance in drug stock movement
        $sql_run_balance = $this->db->query("SELECT machine_code as balance FROM drug_stock_movement WHERE drug ='$get_drug_id' AND ccc_store_sp ='$get_stock_type' AND expiry_date >=CURDATE() ORDER BY id DESC  LIMIT 1");

        $run_balance_array = $sql_run_balance->getResultArray();
        if (count($run_balance_array) > 0) {
            $run_balance = $run_balance_array[0]["balance"];
        } else {
            //If drug does not exist, initialise the balance to zero
            $run_balance = 0;
        }
        //If transaction has positive effect to current transaction type
        if (stripos($transaction_type_name, "received") === 0 || stripos($transaction_type_name, "balance") === 0 || (stripos($transaction_type_name, "returns") === 0 && $transaction_effect == 1) || (stripos($transaction_type_name, "adjustment") === 0 && $transaction_effect == 1) || stripos($transaction_type_name, "startingstock") === 0 || stripos($transaction_type_name, "physicalcount") === 0) {
            $source_dest_type = $get_source;
            //Get remaining balance for the drug
            $get_balance_sql = $this->db->query("SELECT dsb.balance FROM drug_stock_balance dsb  WHERE dsb.facility_code='$facility' AND dsb.stock_type='$get_stock_type' AND dsb.drug_id='$get_drug_id' AND dsb.batch_number='$get_batch' AND dsb.balance>0 AND dsb.expiry_date>=CURDATE() AND dsb.expiry_date='$get_expiry' LIMIT 1");
            $balance_array = $get_balance_sql->getResultArray();
            //Check if drug exists in the drug_stock_balance table
            if (count($balance_array) > 0) {
                $bal = $balance_array[0]["balance"];
            } else {
                //If drug does not exist, initialise the balance to zero
                $bal = 0;
            }


            //If many transactions from the same drug, set balances to zero only once
            if ((session()->get("updated_dsb")) && (session()->get("updated_dsb") == $get_drug_id)) {
                
            } else {
                //If transaction is physical count, set actual quantity as physical count
                if (stripos($transaction_type_name, "startingstock") === 0 || stripos($transaction_type_name, "physicalcount") === 0) {
                    $bal = 0;
                    $run_balance = 0;
                    //Set all balances fro each batch of the drug to be zero in drug_stock_balance for physical count transaction type
                    $sql = "UPDATE drug_stock_balance SET balance =0 WHERE drug_id='$get_drug_id' AND stock_type='$get_stock_type' AND facility_code='$facility'";
                    $set_bal_zero = $this->db->query($sql);
                    session()->set("updated_dsb", $get_drug_id);
                }
            }

            //If stock coming in from another store, get current store 
            if ($check_optgroup == 'Stores') {
                $source_dest_type = $get_source;
                //If transaction type is returns from(+), 
                if (stripos($transaction_type_name, "returns") === 0 && $transaction_effect == 1) {
                    $source_dest_type = $get_destination;
                }

                //Get remaining balance for the drug
                $get_balance_sql = $this->db->query("SELECT dsb.balance FROM drug_stock_balance dsb  
					WHERE dsb.facility_code='$facility' AND dsb.stock_type='" . $source_dest_type . "' AND dsb.drug_id='$get_drug_id' AND dsb.batch_number='$get_batch' 
					AND dsb.balance>0 AND dsb.expiry_date>=CURDATE() AND dsb.expiry_date='$get_expiry' LIMIT 1");
                $balance_array = $get_balance_sql->getResultArray();
                //Check if drug exists in the drug_stock_balance table
                if (count($balance_array) > 0) {
                    $bal_pharma = $balance_array[0]["balance"];
                } else {
                    //If drug does not exist, initialise the balance to zero
                    $bal_pharma = 0;
                }

                //Get running balance in drug stock movement
                $sql_run_balance = $this->db->query("SELECT machine_code as balance FROM drug_stock_movement WHERE drug ='$get_drug_id' AND ccc_store_sp ='$source_dest_type' AND expiry_date >=CURDATE() ORDER BY id DESC  LIMIT 1");
                $run_balance_array = $sql_run_balance->getResultArray();
                if (count($run_balance_array) > 0) {
                    $other_run_balance = $run_balance_array[0]["balance"];
                } else {
                    //If drug does not exist, initialise the balance to zero
                    $other_run_balance = 0;
                }

                $pharma_balance = $bal_pharma - $get_qty; //New balance
                $other_running_balance = $other_run_balance - $get_qty;
            }

            $balance = $get_qty + $bal;  //Current store balance
            $running_balance = $get_qty + $run_balance;
        } else {//If transaction has negative effect (Issuing, returns(-) ...)
            //If issuing to a store(Pharmacy or Main Store), get remaining balance in destination
            if ($check_optgroup == 'Stores') {
                $source_dest_type = $get_destination;
                //If transaction type is returns to(-), get use source instead of destination as where the transaction came from
                if (stripos($transaction_type_name, "returns") === 0 && $transaction_effect == 0) {
                    $source_dest_type = $get_source;
                }

                //Get remaining balance for the drug
                $get_balance_sql = $this->db->query("SELECT dsb.balance FROM drug_stock_balance dsb  
					WHERE dsb.facility_code='$facility' AND dsb.stock_type='" . $source_dest_type . "' AND dsb.drug_id='$get_drug_id' AND dsb.batch_number='$get_batch' 
					AND dsb.balance>0 AND dsb.expiry_date>=CURDATE() AND dsb.expiry_date='$get_expiry' LIMIT 1");
                $balance_array = $get_balance_sql->getResultArray();
                //Check if drug exists in the drug_stock_balance table
                if (count($balance_array) > 0) {
                    $bal_pharma = $balance_array[0]["balance"];
                } else {
                    //If drug does not exist, initialise the balance to zero
                    $bal_pharma = 0;
                }

                //Get running balance in drug stock movement
                $sql_run_balance = $this->db->query("SELECT machine_code as balance FROM drug_stock_movement WHERE drug ='$get_drug_id' AND ccc_store_sp ='$source_dest_type' AND expiry_date >=CURDATE() ORDER BY id DESC  LIMIT 1");
                $run_balance_array = $sql_run_balance->getResultArray();
                if (count($run_balance_array) > 0) {
                    $other_run_balance = $run_balance_array[0]["balance"];
                } else {
                    //If drug does not exist, initialise the balance to zero
                    $other_run_balance = 0;
                }

                $pharma_balance = $bal_pharma + $get_qty; //New balance
                $other_running_balance = $other_run_balance + $get_qty;
            }

            //Substract balance from qty going out
            $balance = $get_available_qty - $get_qty;
            $running_balance = $run_balance - $get_qty;
        }

        /*
         * Get transaction source and destination depending on type of transaction
         */

        // STEP 2, SET SOURCE AND DESTINATION
        //Check if stock type is store or pharmacy
        $s_d = "";
        if ($check_optgroup == 'Stores') {
            $source_destination = $get_source_name;
            if (stripos($stock_type_name, "pharmacy")) {//If pharmacy transaction, source and destinations is facility code
                $source = $facility;
                $destination = $facility;

                //Check if transaction is coming in or going out to find what to put in source and destination
                //If transaction is coming, destination is current store
                if ($transaction_effect == 1) {
                    $source_destination = $get_source_name;
                    if (stripos($transaction_type_name, "returns") === 0 && $transaction_effect == 1) {//If transaction is returns from(+), source is current store
                        $source_destination = $get_destination_name;
                    }
                } else if ($transaction_effect == 0) {//If transaction is going out, current store is sources
                    $source_destination = $get_destination_name;
                    if (stripos($transaction_type_name, "returns") === 0 && $transaction_effect == 0) {//If transaction is returns from(-), destination is current store
                        $source_destination = $get_source_name;
                    }
                } else {//Transaction does not have effect ( Error)
                    $time = date("Y-m-d H:is:s");
                    $error[] = 'An error occured while saving your data ! No transaction effect found! (' . $time . ')';
                }
            } elseif (stripos($stock_type_name, "store")) {//If store transaction, source or destination is facility code
                //Check if transaction is coming in or going out to find what to put in source and destination
                //If transaction is coming, destination is current store
                if ($transaction_effect == 1) {
                    //If transaction is coming in, destination is current store
                    $source = $get_source_name;
                    $destination = $facility;
                    $source_destination = $get_source_name;
                    if (stripos($transaction_type_name, "returns") === 0 && $transaction_effect == 1) {//If transaction is returns from(+), source is current store
                        $source = $facility;
                        $destination = $get_destination_name;
                        $source_destination = $get_destination_name;
                    }
                } else if ($transaction_effect == 0) {//If transaction is going out, current store is sources
                    $source = $facility;
                    $destination = $get_destination_name;
                    $source_destination = $get_destination_name;
                    if (stripos($transaction_type_name, "returns") === 0 && $transaction_effect == 0) {//If transaction is returns from(-), destination is current store
                        $source = $get_source_name;
                        $destination = $facility;
                        $source_destination = $get_source_name;
                    }
                } else {//Transaction does not have effect ( Error)
                    $time = date("Y-m-d H:is:s");
                    $error[] = 'An error occured while saving your data ! No transaction effect found! (' . $time . ')';
                }
            }
        } else {
            if (stripos($stock_type_name, "pharmacy")) {//If pharmacy transaction, source and destinations is facility code
                $source = $facility;
                $destination = $facility;
                if ($transaction_effect == 1) {
                    $source_destination = $get_source;
                    $s_d = 's';
                    if (stripos($transaction_type_name, "returns") === 0 && $transaction_effect == 1) {//If transaction is returns from(+), source is current store
                        $source_destination = $get_destination;
                        $s_d = 'd';
                    }
                } else if ($transaction_effect == 0) {//If transaction is going out, current store is sources
                    $source_destination = $get_destination;
                    $s_d = 'd';
                    if (stripos($transaction_type_name, "returns") === 0 && $transaction_effect == 0) {//If transaction is returns from(-), destination is current store
                        $source_destination = $get_source;
                        $s_d = 's';
                    }
                } else {//Transaction does not have effect ( Error)
                    $time = date("Y-m-d H:is:s");
                    $error[] = 'An error occured while saving your data ! No transaction effect found! (' . $time . ')';
                }
            } elseif (stripos($stock_type_name, "store")) {//If store transaction, source or destination is facility code
                if ($transaction_effect == 1) {
                    //If transaction is coming in, destination is current store
                    $source = $get_source;
                    $destination = $facility;
                    $source_destination = $get_source;
                    $s_d = 's';
                    if (stripos($transaction_type_name, "returns") === 0 && $transaction_effect == 1) {//If transaction is returns from(+), source is current store
                        $source = $facility;
                        $destination = $get_destination;
                        $source_destination = $get_destination;
                        $s_d = 'd';
                    }
                } else if ($transaction_effect == 0) {//If transaction is going out, current store is sources
                    $source = $facility;
                    $destination = $get_destination;
                    $source_destination = $get_destination;
                    $s_d = 'd';
                    if (stripos($transaction_type_name, "returns") === 0 && $transaction_effect == 0) {//If transaction is returns from(-), destination is current store
                        $source = $get_source;
                        $destination = $facility;
                        $source_destination = $get_source;
                        $s_d = 's';
                    }
                } else {//Transaction does not have effect ( Error)
                    $time = date("Y-m-d H:is:s");
                    $error[] = 'An error occured while saving your data ! No transaction effect found! (' . $time . ')';
                }
            }
        }

        //Sanitize by removing (store) or (pharmacy)
        $source_destination = str_ireplace('(store)', '', $source_destination);
        $source_destination = str_ireplace('(pharmacy)', '', $source_destination);

        //If source or destination is central site or satellite, insert exact name instead of IDs
        if ($check_optgroup == 'Central Site' || $check_optgroup == 'Satelitte Sites') {
            if ($s_d == 'd') {
                $source_destination = $get_destination_name;
            } elseif ($s_d == 's') {
                $source_destination = $get_source_name;
            }
        }
        //echo json_encode($running_balance ." -- ".$other_running_balance);die();
        //echo json_encode($source_destination);die();
        // STEP 3, INSERT TRANSACTION IN DRUG STOCK MOVEMENT FOR CURRENT STORES
        $drug_stock_mvt_transact = [
            'drug' => $get_drug_id,
            'transaction_date' => $get_transaction_date,
            'batch_number' => $get_batch,
            'transaction_type' => $get_transaction_type,
            'source' => $source,
            'destination' => $destination,
            'expiry_date' => $get_expiry,
            'packs' => $get_packs,
            $get_qty_choice => $get_qty,
            $get_qty_out_choice => '0',
            'balance' => $balance,
            'unit_cost' => $get_unit_cost,
            'amount' => $get_amount,
            'remarks' => $get_comment,
            'operator' => $get_user,
            'order_number' => $get_ref_number,
            'facility' => $facility,
            'Source_Destination' => $source_destination,
            'timestamp' => $time_stamp,
            'machine_code' => $running_balance,
            'ccc_store_sp' => $get_stock_type
        ];

        $this->db->table('drug_stock_movement')->insert($drug_stock_mvt_transact);

        //check if query inserted
        $inserted = $this->db->affectedRows();
        if ($inserted < 1) {//If query did not insert
            $time = date("Y-m-d H:is:s");
            $errNo = $this->db->error();
            $errMess = $this->db->getErrorMessage();
            $remaining_drugs = $_POST['remaining_drugs'];
            $error[] = 'An error occured while saving your data(Drug Transaction 1) ! Error  ' . $errNo . ' : ' . $errMess . ' (' . $time . ')';
            echo json_encode($error);
            die();
        }

        //STEP 4, UPDATE DRUG STOCK BALANCE FOR CURRENT STORE

        if ($transaction_effect == 1) {
            $balance_sql = "INSERT INTO drug_stock_balance(drug_id,batch_number,expiry_date,stock_type,facility_code,balance,ccc_store_sp) VALUES('" . $get_drug_id . "','" . $get_batch . "','" . $get_expiry . "','" . $get_stock_type . "','" . $facility . "','" . $get_qty . "','" . $get_stock_type . "') ON DUPLICATE KEY UPDATE balance=balance + " . $get_qty . ";";
            if (stripos($transaction_type_name, "physical")) {//Physical Count
                $balance_sql = "INSERT INTO drug_stock_balance(drug_id,batch_number,expiry_date,stock_type,facility_code,balance,ccc_store_sp) VALUES('" . $get_drug_id . "','" . $get_batch . "','" . $get_expiry . "','" . $get_stock_type . "','" . $facility . "','" . $get_qty . "','" . $get_stock_type . "') ON DUPLICATE KEY UPDATE balance=" . $get_qty . ";";
            }
        } else if ($transaction_effect == 0) {
            $balance_sql = "UPDATE drug_stock_balance SET balance=balance - " . $get_qty . " WHERE drug_id='" . $get_drug_id . "' AND batch_number='" . $get_batch . "' AND expiry_date='" . $get_expiry . "' AND stock_type='" . $get_stock_type . "' AND facility_code='" . $facility . "';";
        }
        $sql_dsb_current_store = $this->db->query($balance_sql);

        $inserted = $this->db->affectedRows();
        if ($inserted < 1) {//If query did not insert
            $time = date("Y-m-d H:is:s");
            $errNo = $this->db->getErrorCode();
            $errMess = $this->db->getErrorMessage();
            $remaining_drugs = $_POST["remaining_drugs"];
            $error[] = 'An error occured while saving your data (Drug Balance)! Error  ' . $errNo . ' : ' . $errMess . ' (' . $time . ')';
            echo json_encode($error);
            die();
        }

        //STEP 5, IF STORE TRANSACTIONS, UPDATE OTHER STORE DETAILS

        if ($check_optgroup == 'Stores') {// If transaction if from one store to another, update drug stock balance in the other store
            //STEP 6, UPDATE DRUG STOCK MOVEMENT FOR THE OTHER STORE
            if (stripos($source_destination, "pharmacy")) {//If pharmacy transaction, source and destinations is facility code
                $source = $facility;
                $destination = $facility;
            }

            $source_destination = $stock_type_name;
            //Get corresponding transaction types
            $sql = "";
            if (stripos($transaction_type_name, "receive") === 0) {//If transaction is received, insert an issued to
                $sql = "SELECT id FROM transaction_type WHERE name LIKE '%issued%' LIMIT 1";
            } else if (stripos($transaction_type_name, "issued") === 0) {//Issued, insert a received
                $sql = "SELECT id FROM transaction_type WHERE name LIKE '%received%' LIMIT 1";
            } else if (stripos($transaction_type_name, "returns") === 0 && $transaction_effect == 1) {//Returns froms(+), insert an returns to (-)
                $sql = "SELECT id FROM transaction_type WHERE name LIKE '%returns%' AND effect='0' LIMIT 1";
            } else if (stripos($transaction_type_name, "returns") === 0 && $transaction_effect == 0) {//Returns to(-), insert an returns from (+)
                $sql = "SELECT id FROM transaction_type WHERE name LIKE '%returns%' AND effect='1' LIMIT 1";
            }
            $get_trans_id = $this->db->query($sql);
            $get_trans_id = $get_trans_id->getResultArray();
            $transaction_type = $get_trans_id[0]['id'];

            //Sanitize by removing (store) or (pharmacy)
            $source_destination = str_ireplace('(store)', '', $source_destination);
            $source_destination = str_ireplace('(pharmacy)', '', $source_destination);

            $drug_stock_mvt_other_trans = array(
                'drug' => $get_drug_id,
                'transaction_date' => $get_transaction_date,
                'batch_number' => $get_batch,
                'transaction_type' => $transaction_type,
                'source' => $source,
                'destination' => $destination,
                'expiry_date' => $get_expiry,
                'packs' => $get_packs,
                $get_qty_choice => '0',
                $get_qty_out_choice => $get_qty,
                'balance' => $pharma_balance,
                'unit_cost' => $get_unit_cost,
                'amount' => $get_amount,
                'remarks' => $get_comment,
                'operator' => $get_user,
                'order_number' => $get_ref_number,
                'facility' => $facility,
                'Source_Destination' => $source_destination,
                'timestamp' => $time_stamp,
                'machine_code' => $other_running_balance,
                'ccc_store_sp' => $source_dest_type
            );


            $this->db->table('drug_stock_movement')->insert($drug_stock_mvt_other_trans);
            //echo json_encode($source_destination);die();
            //check if query inserted
            $inserted = $this->db->affectedRows();
            if ($inserted < 1) {//If query did not insert
                $time = date("Y-m-d H:is:s");
                $errNo = $this->db->getErrorCode();
                $errMess = $this->db->getErrorMessage();
                $remaining_drugs = $_POST["remaining_drugs"];
                $error[] = 'An error occured while saving your data(Drug Transaction 2) ! Error  ' . $errNo . ' : ' . $errMess . ' (' . $time . ')';
                echo json_encode($error);
                die();
            }

            //STEP 7, UPDATE DRUG STOCK BALANCE FOR THE OTHER STORE
            //If transaction has a positive effect on current store, it will have a negative effect on the other store
            if ($transaction_effect == 1) {
                //If transaction has a positive effect, substract balance in the other store
                $balance_sql = "UPDATE drug_stock_balance SET balance=balance - " . $get_qty . " WHERE drug_id='" . $get_drug_id . "' AND batch_number='" . $get_batch . "' AND expiry_date='" . $get_expiry . "' AND stock_type='" . $get_source . "' AND facility_code='" . $facility . "';";
                if (stripos($transaction_type_name, "returns") === 0 && $transaction_effect == 1) {//If returns from(+), substract from other store
                    $balance_sql = "INSERT INTO drug_stock_balance(drug_id,batch_number,expiry_date,stock_type,facility_code,balance,ccc_store_sp) VALUES('" . $get_drug_id . "','" . $get_batch . "','" . $get_expiry . "','" . $get_destination . "','" . $facility . "','" . $get_qty . "','" . $get_stock_type . "') ON DUPLICATE KEY UPDATE balance=balance - " . $get_qty . ";";
                }
            } else if ($transaction_effect == 0) {//If transaction has negative effect, add to balance in the other store
                $balance_sql = "INSERT INTO drug_stock_balance(drug_id,batch_number,expiry_date,stock_type,facility_code,balance,ccc_store_sp) VALUES('" . $get_drug_id . "','" . $get_batch . "','" . $get_expiry . "','" . $get_destination . "','" . $facility . "','" . $get_qty . "','" . $get_stock_type . "') ON DUPLICATE KEY UPDATE balance=balance + " . $get_qty . ";";
                if (stripos($transaction_type_name, "returns") === 0 && $transaction_effect == 0) {//If returns to(-), add to drug stock balance in the other store
                    $balance_sql = "UPDATE drug_stock_balance SET balance=balance + " . $get_qty . " WHERE drug_id='" . $get_drug_id . "' AND batch_number='" . $get_batch . "' AND expiry_date='" . $get_expiry . "' AND stock_type='" . $get_source . "' AND facility_code='" . $facility . "';";
                }
            }
            $sql_dsb_store = $this->db->query($balance_sql);
            $inserted = $this->db->affectedRows();
            if ($inserted < 1) {//If query did not insert
                $time = date("Y-m-d H:is:s");
                $errNo = $this->db->getErrorCode();
                $errMess = $this->db->getErrorMessage();
                $remaining_drugs = $_POST['remaining_drugs'];
                $error[] = 'An error occured while saving your data(Drug Balance 2) ! Error  ' . $errNo . ' : ' . $errMess . ' (' . $time . ')';
                echo json_encode($error);
                die();
            }
        }


        //Check if transaction came from picking list and not all drugs where supplied
        if ($all_drugs_supplied == 0) {
            //Update supplied drugs
            $sql = "UPDATE cdrr_item SET publish='1' WHERE id='$cdrr_id'";
            $this->db->query($sql);
        }

        //Get drug_name
        $drug_det = Drugcode::getDrugCodeHydrated($get_drug_id);
        $drug_name = $drug_det[0]->drug;
        echo json_encode($drug_name);
        die();
    }

    //Print Issue transactions
    public function print_issues() {
        $facility = session()->get('facility_name');
        $source = $this->request->getPost("source");
        $destination = $this->request->getPost("destination");
        $drug = $this->request->getPost("drug");
        $unit = $this->request->getPost("unit");
        $batch = $this->request->getPost("batch");
        $pack_size = $this->request->getPost("pack_size");
        $expiry = date('Y-m-d', strtotime($this->request->getPost("expiry")));
        $pack = $this->request->getPost("pack");
        $quantity = $this->request->getPost("quantity");
        $counter = $this->request->getPost("counter");
        $total = $this->request->getPost("total");

        //Build table
        $string = "<style>table{border-collapse: collapse;color: #3a3434;} table>td{color: #3a3434;padding: 9px 8px 0;}</style>
		<table border='0' style='width:100%;'>
		<tr>
		<td>#" . date('U') . " </td>
		<td align='right'> COUNTER REQUISITION AND ISSUE VOUCHER</td>
		</tr>
		<tr>
		<td>Name of facility " . $facility . "</td>
		</tr>
		<tr>
		<td>Requested by (Unit Requesting) <u>" . strtoupper($destination) . "</u> </td>
		<td align='right'>Issued by (Unit issuing)<u>" . strtoupper($source) . "</u></td>
		</tr>
		</table>
		<br />
		";

        if ($counter == 0) {

            //$this -> mpdf -> addPage();
            $string .= '<table border="1" align="center" width="100%" style="border-collapse:collapse">
			<thead>
			<tr>
			<th>Item Description</th>
			<th>Unit of issue</th>
			<th>Quantity Requested</th>
			<th>Quantity Issued</th>
			<th>Expiry Date</th>
			<th>Batch Number</th>
			<th>Quantity Received</th>
			<th>Remarks</th>
			</tr>
			</thead>
			<tbody>
			';
            session()->set('string', $string);
        }
        if ($counter == ($total - 1)) {//If las row
            $string = session()->get('string') . '
	   	<tr>
	   	<td>' . $drug . '</td>
	   	<td>' . $unit . '</td>
	   	<td> </td>
	   	<td>' . $quantity . '</td>
	   	<td>' . $expiry . '</td>
	   	<td>' . $batch . '</td>
	   	<td> </td>
	   	<td> </td>
	   	</tr>

	   	</tbody>
	   	</table>

	   	<br />
	   	<table border="0" width="100%">
	   	<tr>
	   	<td>Requisitioning Officer:……………………………………………………</td>
	   	<td>Designation: …………………………………………… </td>
	   	<td>Date: …………………………………………………… </td>
	   	<td>Sign: ……………………………………………………</td>
	   	</tr>

	   	<tr>
	   	<td> Issued by: ……………………………………………………</td>
	   	<td>Designation: …………………………………………… </td>
	   	<td>Date: …………………………………………………… </td>
	   	<td>Sign: ……………………………………………………</td>
	   	</tr>

	   	<tr>
	   	<td>Received by: ……………………………………………………</td>
	   	<td>Designation: …………………………………………… </td>
	   	<td>Date: …………………………………………………… </td>
	   	<td>Sign ……………………………………………………</td>
	   	</tr>
	   	</table>

	   	<br />
	   	' . date('D j M Y');
            //write to page
            $this->mpdf = new Mpdf(['c', 'B4']);
            $this->mpdf->ignore_invalid_utf8 = true;
            $this->mpdf->simpleTables = true;
            $this->mpdf->WriteHTML($string);
            $this->mpdf->SetFooter("{DATE D j M Y }|{PAGENO}/{nb}| Issues_" . date('U') . "  , source Web ADT");

            $file_name = 'assets/download/Issues_' . date('U') . '.pdf';
            @$this->mpdf->Output($file_name, 'F');
            echo (base_url() . $file_name);
            die();
        } else {
            $string = session()->get('string') . '<<tr>
	   	<td>' . $drug . '</td>
	   	<td>' . $unit . '</td>
	   	<td> </td>
	   	<td>' . $quantity . '</td>
	   	<td>' . $expiry . '</td>
	   	<td>' . $batch . '</td>
	   	<td> </td>
	   	<td> </td>
	   	</tr>';
            session()->set('string', $string);
        }
        echo json_encode($counter . '-' . $total);
        die();
    }

    public function set_transaction_session() {
        $drugs_transacted = $this->request->getPost("list_drugs_transacted");
        $remaining_drugs = $this->request->getPost("remaining_drugs");
        //$this->session->set_userdata('filter_datatable',$drugs_transacted);
        if ($remaining_drugs == 0) {
            session()->set("msg_save_transaction", "success");
            session()->remove("updated_dsb");
        } else {
            session()->set("msg_save_transaction", "failure");
        }
    }

    public function save_edit() {
        $sql = $this->request->getPost("sql");
        $queries = explode(";", $sql);
        foreach ($queries as $query) {
            if (strlen($query) > 0) {
                $this->db->query($query);
            }
        }
    }

    public function getDrugsBatches($drug) {
        $today = date('Y-m-d');
        $sql = "select drug_stock_balance.batch_number,drug_unit.Name as unit,dose.Name as dose,drugcode.quantity,drugcode.duration from drug_stock_balance,drugcode,drug_unit,dose where drug_id='$drug' and drugcode.id=drug_stock_balance.drug_id  and drug_unit.id=drugcode.unit and dose.id= drugcode.dose and expiry_date>'$today' and balance>0 group by batch_number order by drug_stock_balance.expiry_date asc";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        if ($results) {
            echo json_encode($results);
        }
    }

    public function getAllDrugsBatches($drug) {
        $drug = $this->uri->getSegment(3);
        $today = date('Y-m-d');
        $sql = "select drug_stock_balance.batch_number,drug_unit.Name as unit,dose.Name as dose,drugcode.quantity,drugcode.duration from drug_stock_balance inner join drugcode on drugcode.id=drug_stock_balance.drug_id ".
		"left join drug_unit on drug_unit.id=drugcode.unit ".
		"left join dose on dose.id= drugcode.dose ".
		"where drug_id='".$drug."' group by batch_number order by drug_stock_balance.expiry_date asc";
        $results = DB::select($sql);
        if ($results) {
            echo json_encode($results);
        }
    }

    public function getBatchInfo($drug, $batch) {
        $sql = "select * from drug_stock_balance where drug_id='$drug' and batch_number='$batch'";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        if ($results) {
            echo json_encode($results);
        }
    }

    public function getDrugsBrands($drug) {
        $sql = "select * from brand where drug_id='$drug' group by brand";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        if ($results) {
            echo json_encode($results);
        }
    }

    //Get orders for a picking list
    public function getOrderDetails() {
        $order_id = $this->request->getPost("order_id");
        $sql = $this->db->query("SELECT ci.id as cdrr_id,dc.id,u.Name as unit,dc.pack_size,ci.drug_id,ci.newresupply,ci.resupply FROM cdrr_item ci LEFT JOIN drugcode dc ON dc.drug=ci.drug_id LEFT JOIN facility_order fo ON fo.unique_id=ci.cdrr_id LEFT JOIN drug_unit u ON dc.unit=u.id  WHERE fo.id='$order_id' AND ci.publish=0");
        $order_list = $sql->getResultArray();
        echo json_encode($order_list);
    }

    //Set order status
    public function set_order_status() {
        $order_id = $this->request->getPost("order_id");
        $status = $this->request->getPost("status");
        $updated_on = date("U");
        $this->db->query("UPDATE facility_order SET status='$status',updated='$updated_on' WHERE id='$order_id'");
    }

    public function base_params($data) {
        $data['title'] = "webADT | Inventory";
        $data['banner_text'] = "Inventory Management";
        $data['link'] = "inventory";
        echo view('\Modules\ADT\Views\\template', $data);
    }

}

?>