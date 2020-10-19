<?php

class MY_Controller extends \CodeIgniter\Controller {

    public $SERVER_URL = 'http://197.248.7.226/api/public/api/v1/';
    public $IP = '197.248.7.226';
    public $PORT = '80';
    public $TIMEOUT = '10';

    function loadChoices($table, $column) {
        $db = \Config\Database::connect();
        return $db->select($column)->group_by($column)->get($table)->result_array();
    }

    function getDrugData($id) {
       
        $data = $this->db
                ->where('drug', $id)
                ->where('transaction_type', '5')
                ->order_by('id', 'desc')
                ->limit(1)
                ->get('drug_stock_movement')
                ->result();
        echo json_encode($data);
    }

    //shorten field input
    function _p($field) {
        return $this->input->post($field);
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

    //get pv data from local repository in ADT
    function getPvData($table, $type, $id = '') {
        $newid = '';
        $iddata = $this->input->post('ids');
        foreach ($iddata as $i):
            $newid .= $i . ',';
        endforeach;
        $final = rtrim($newid, ',');


        if ((int) $this->ping($this->IP, $this->PORT, $this->TIMEOUT) == 200) {
            $adr = [];
            if ($type == 'pqm') {
                $query = $this->db->query("SELECT * FROM `$table` WHERE id IN($final)")->result();
                $data = json_encode($query);
                $this->push($this->SERVER_URL . 'spqm/send', $data, $table, $final, $type);
            } else if ($type == 'adr') {
                $query = $this->db->query("SELECT * FROM `adr_form` WHERE id IN($final)")->result();
                $query2 = $this->db->query("SELECT * FROM adr_form_details WHERE adr_id IN($final)")->result();
                array_push($adr, $query);
                array_push($adr, $query2);
                $data = json_encode($adr);
                // print_r($data);
                $this->push($this->SERVER_URL . 'sadr/send', $data, $table, $final, $type);
            }
        } else {

            if ($type == 'pqm') {
                $this->session->set_flashdata('pqmp_error', 'Error: Suspected Poor Quality Medicine(SPQM) destination API server cannot be reached at the moment!');
                redirect('inventory_management/pqmp/');
            } else if ($type == 'adr') {
                $this->session->set_flashdata('adr_error', 'Error: Suspected Adverse Drug Reaction (SADR) destination API server cannot be reached at the moment!');
                redirect('inventory_management/adr/');
            }
        }
    }

    //push and synchronize data to remote ppb pv database

    function push($url, $data, $table, $final, $type) {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //curl error SSL certificate problem, verify that the CA cert is OK

        $result = curl_exec($curl);

        $response = json_decode($result, TRUE);



        if ((int) $response['status'] === 1):

            $this->db->query("UPDATE `$table` SET synch=1 WHERE id IN($final)");

            if ($type == 'pqm') {
                $this->checkUpdatePv($type);
            } else if ($type == 'adr') {
                $this->db->query("UPDATE `adr_form` SET synch=1 WHERE id IN($final)");
                $this->checkUpdatePv($type);
            } else {
                
            }
        else:
            echo json_encode(['status' => 'error']);

        endif;
    }

    //Check and get the number of new updates of reports from ppb pv data
    function checkUpdatePv($pv) {
        $facility_id = $this->session->userdata('facility');
        //  $la = $this->db->get('api_update')->result();
        //  $newupdates = "";

        $data = "sadr_id=$facility_id" .
                "&sadr_list_id=$facility_id" .
                "&spqm_id=$facility_id";



        $curl = curl_init($this->SERVER_URL . 'sadr/update');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($curl);
        $re = json_decode($result, TRUE);

        // echo '<pre>';
        // print_r($re);
        // echo '</pre>';
        // exit;
        if ($pv == 'pqm') {
            $adt_pqms_ids = $this->db->select('ppid as id')->where('ppid is NOT NULL', NULL, FALSE)->get('pqms')->result_array();
            $pqmdiff = $this->array_compare($re['spqm'], $adt_pqms_ids);
            $pqmpids = $this->extract_ids($pqmdiff);
            $this->fetchSynchData($pqmpids, $pv);
        } else if ($pv == 'adr') {
            $adt_adr_ids = $this->db->select('ppid as id')->where('ppid is NOT NULL', NULL, FALSE)->get('adr_form')->result_array();
            $adrdiff = $this->array_compare($re['sadr'], $adt_adr_ids);
            $adrids = $this->extract_ids($adrdiff);

            $this->fetchSynchData($adrids, $pv);
        }
    }

    function array_compare($first_array, $second_array) {
        $diff = array_udiff($first_array, $second_array, function ($obj_a, $obj_b) {
            return $obj_a['id'] - $obj_b['id'];
        });

        return $diff;
    }

    function extract_ids($array) {
        $newid = '';
        foreach ($array as $i):
            $newid .= $i['id'] . ',';
        endforeach;
        return rtrim($newid, ',');
    }

    //Featch data of new reports from ppb pv database and synch to local database ADT
    function fetchSynchData($pqmps, $pv) {

        $data = 'sadr_id=' . $pqmps .
                '&sadr_list_id=' . $pqmps .
                '&spqm_id=' . $pqmps;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->SERVER_URL . 'sadr/updata');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        $insert_data = json_decode($server_output, TRUE);
        if ($pv == 'pqm') {
            $this->save_pqm($insert_data['spqm']);
            echo json_encode(['status' => 'success']);
        } else if ($pv == 'adr') {
            $this->save_adr($insert_data['sadr']);
            $this->save_adr_druglist($insert_data['sdrugs']);
            echo json_encode(['status' => 'success']);
        }

        curl_close($ch);
    }

    //Save PMPQ Data from PPB remote Database
    function save_pqm($p) {


        error_reporting(E_ALL);


        for ($i = 0; $i < count($p); $i++) {
            $pmpq = array(
                'user_id' => $this->session->userdata("user_id"),
                'county_id' => $p[$i]['county_id'],
                'sub_county_id' => $p[$i]['sub_county_id'],
                'country_id' => $p[$i]['country_id'],
                'designation_id' => $p[$i]['designation_id'],
                'facility_name' => $p[$i]['facility_name'],
                'facility_code' => $p[$i]['facility_code'],
                'facility_address' => $p[$i]['facility_address'],
                'facility_phone' => $p[$i]['facility_phone'],
                'brand_name' => $p[$i]['brand_name'],
                'generic_name' => $p[$i]['generic_name'],
                'batch_number' => $p[$i]['batch_number'],
                'manufacture_date' => $p[$i]['manufacture_date'],
                'expiry_date' => $p[$i]['expiry_date'],
                'receipt_date' => $p[$i]['receipt_date'],
                'name_of_manufacturer' => $p[$i]['name_of_manufacturer'],
                'supplier_name' => $p[$i]['supplier_name'],
                'supplier_address' => $p[$i]['supplier_address'],
                'product_formulation' => $p[$i]['product_formulation'],
                'product_formulation_specify' => $p[$i]['product_formulation_specify'],
                'colour_change' => $p[$i]['colour_change'],
                'separating' => $p[$i]['separating'],
                'powdering' => $p[$i]['powdering'],
                'caking' => $p[$i]['caking'],
                'moulding' => $p[$i]['moulding'],
                'odour_change' => $p[$i]['odour_change'],
                'mislabeling' => $p[$i]['mislabeling'],
                'incomplete_pack' => $p[$i]['incomplete_pack'],
                'complaint_other' => $p[$i]['complaint_other'],
                'complaint_other_specify' => $p[$i]['complaint_other_specify'],
                'complaint_description' => $p[$i]['complaint_description'],
                'require_refrigeration' => $p[$i]['require_refrigeration'],
                'product_at_facility' => $p[$i]['product_at_facility'],
                'returned_by_client' => $p[$i]['returned_by_client'],
                'stored_to_recommendations' => $p[$i]['stored_to_recommendations'],
                'other_details' => $p[$i]['other_details'],
                'comments' => $p[$i]['comments'],
                'reporter_name' => $p[$i]['reporter_name'],
                'reporter_email' => $p[$i]['reporter_email'],
                'contact_number' => $p[$i]['contact_number'],
                'emails' => 1,
                'submitted' => 1,
                'active' => 1,
                'device' => 1,
                'notified' => 1,
                'synch' => 2,
                'created' => $p[$i]['created'],
                'ppid' => $p[$i]['id']
            );

            $this->db->insert('pqms', $pmpq);
        }
    }

    function save_adr($ad) {
        for ($i = 0; $i < count($ad); $i++) {
            $adr = array(
                'id' => $ad[$i]['id'],
                'report_title' => $ad[$i]['report_title'],
                'report_type' => $ad[$i]['report_type'],
                'institution_name' => $ad[$i]['name_of_institution'],
                'institution_code' => $ad[$i]['institution_code'],
                'county' => $ad[$i]['county_id'],
                'sub_county' => $ad[$i]['sub_county_id'],
                'address' => $ad[$i]['address'],
                'contact' => $ad[$i]['contact'],
                'patient_name' => $ad[$i]['patient_name'],
                'ip_no' => $ad[$i]['ip_no'],
                'dob' => $ad[$i]['date_of_birth'],
                'patient_address' => $ad[$i]['patient_address'],
                'ward_clinic' => $ad[$i]['ward'],
                'gender' => $ad[$i]['gender'],
                'is_alergy' => $ad[$i]['known_allergy'],
                // 'alergy_desc' => $ad[$i]['known_allergy_spacify'],
                'is_pregnant' => $ad[$i]['pregnant'],
                'weight' => $ad[$i]['weight'],
                'height' => $ad[$i]['height'],
                'diagnosis' => $ad[$i]['diagnosis'],
                'date_of_onset_of_reaction' => $ad[$i]['date_of_onset_of_reaction'],
                'reaction_description' => $ad[$i]['description_of_reaction'],
                'severity' => $ad[$i]['severity'],
                'action_taken' => $ad[$i]['action_taken'],
                'outcome' => $ad[$i]['outcome'],
                'reaction_casualty' => $ad[$i]['causality'],
                'other_comment' => $ad[$i]['any_other_comment'],
                'reporting_officer' => $ad[$i]['reporter_name'],
                'reporting_officer' => $ad[$i]['reporter_name'],
                'email_address' => $ad[$i]['reporter_email'],
                'office_phone' => $ad[$i]['reporter_phone'],
                'designation' => $ad[$i]['designation_id'],
                'signature' => 'pv system ppb',
                'synch' => 2,
                'ppid' => $ad[$i]['id'],
                'datecreated' => $ad[$i]['created'],
            );

            $this->db->insert('adr_form', $adr);
        }
    }

    function save_adr_druglist($d) {

        for ($i = 0; $i < count($d); $i++) {
            $adr_details = array(
                'adr_id' => $d[$i]['sadr_id'],
                'drug' => $d[$i]['drug_name'],
                'brand' => $d[$i]['brand_name'],
                'dose_id' => $d[$i]['dose_id'],
                'route' => $d[$i]['route_id'],
                'dose' => $d[$i]['dose'],
                'route_freq' => $d[$i]['frequency_id'],
                'date_started' => $d[$i]['start_date'],
                'date_stopped' => $d[$i]['stop_date'],
                'indication' => $d[$i]['indication'],
                'suspecteddrug' => $d[$i]['suspected_drug'],
                'visitid' => '0000000'
            );
            $this->db->insert('adr_form_details', $adr_details);
        }
    }

}
