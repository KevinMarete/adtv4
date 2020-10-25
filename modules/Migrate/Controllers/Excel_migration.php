<?php
namespace Modules\Migrate\Controllers;

use App\Controllers\BaseController;
use Modules\ADT\Models\Patient;
use Modules\ADT\Models\Regimen;
use \Modules\Template\Controllers\Template;
use Illuminate\Database\Capsule\Manager as DB;

class Excel_migration extends BaseController {
    var $db;

	function __construct() {
		ini_set("max_execution_time", "100000");
        ini_set("memory_limit", '2048M');
        $this->db = \Config\Database::connect();
	}

	public function index() {

		//migration view
		$data['content_view'] = "\Modules\Migrate\Views/excel_view";
		$data['banner_text'] = "Data Import";
		$data['active_menu'] = 3;
		$this->template($data);
	}
	
	public function checkDB($dbname) {//Check if database selected can be migrated
		$sql = "show tables from $dbname like '%tblarvdrugstockmain%';";
		$query = $this->db->query($sql);
		$results = $query->getResultArray();
		if ($results) {//If database can be migrated
			$temp = 1;
		} else {
			$temp = 0;
		}
		echo $temp;
    }
    
    public function importExcel(){
        $upload_type = $this->post('upload_type');
        $file = $this->request->getFile('excel_file');
        $facilty_code = $this->post('mflcode');

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = array_chunk($worksheet->toArray(), 100);

        // Patient list
        if($upload_type == 'patient_list'){
            foreach($data as $chunk){
                $this->importPatientHistory($chunk, $facilty_code);
            }

            $this->session->setFlashdata('msg', 'Patient data has been uploaded successfully.');
            $this->session->setFlashdata('msg_type', 'success');
        }
        // Patient history
        else if($upload_type == 'patient_history'){
            foreach($data as $chunk){
                $this->importPatientList($chunk, $facilty_code);
            }

            $this->session->setFlashdata('msg', 'Patient history import has not been implemented yet.');
            $this->session->setFlashdata('msg_type', 'warning');
        }

        return redirect()->to(base_url().'/public/migrate/excel');
    }

    public function downloadTemplate($template){
        if(!empty($template)){
            return $this->response->download('./assets/templates/data_import/'.$template.'.xlsx', null);
        }
    }
	
	public function template($data) {
		$data['show_menu'] = 0;
		$data['show_sidemenu'] = 0;
		$data['title'] = 'Tools | Excel Import';
		$template = new Template();
        $template->index($data);
    }
    
    public function importPatientHistory($data, $facilty_code){
        //
    }

    public function importPatientList($data, $facilty_code){
        $insert_data = [];
        foreach($data as $row){
            $insert_data[] = [
                'patient_number_ccc' => trim($row[0]),
                'first_name' => empty($row[1]) ? '' : trim($row[1]),
                'other_name' => empty($row[2]) ? '' : trim($row[2]),
                'last_name' => empty($row[3]) ? '' : trim($row[3]),
                'dob' => empty($row[4]) ? '' : trim($row[4]),
                'facility_code' => $facilty_code,
                'gender' => $this->getSex(trim($row[6])),
                'weight' => empty($row[7]) ? '' : trim($row[7]),
                'height' => empty($row[8]) ? '' : trim($row[8]),
                'date_enrolled' => empty($row[9]) ? '' : trim($row[9]),
                'source' => $this->getSource(trim($row[10])),
                'service' => $this->getService(trim($row[11])),
                'start_regimen' => $this->getRegimen(trim($row[12])),
                'start_regimen_date' => empty($row[13]) ? '' : $row[13],
                'current_status' => $this->getStatus(trim($row[14])),
                'current_regimen' => $this->getRegimen(trim($row[15])),
                'nextappointment' => empty($row[16]) ? '' : $row[16],
                'start_height' => empty($row[19]) ? '' : trim($row[19]),
                'start_weight' => empty($row[20]) ? '' : trim($row[20]),
                'drug_prophylaxis' => $this->getProphylaxis(trim($row[22])),
                'isoniazid_start_date' => empty($row[23]) ? '' : $row[23],
                'isoniazid_end_date' => empty($row[24]) ? '' : $row[24],
                'rifap_isoniazid_start_date' => empty($row[25]) ? '' : $row[25],
                'rifap_isoniazid_end_date' => empty($row[26]) ? '' : $row[26],
                'differentiated_care' => $this->getDifferentiatedCare(trim($row[27]))
            ];
        }

        DB::table('patient')->insert($insert_data);
    }

    function getSex($value){
        $result = '';
        if(!empty($value)){
            if(strtolower($value) == 'male' || strtolower($value) == 'm') $result = 1;
            else if(strtolower($value) == 'female' || strtolower($value) == 'f') $result = 2;
        }
        return $result;
    }

    function getSource($value){
        $result = '';
        if(!empty($value)){
            if(strtolower($value) == 'transfer in') $result = 3;
            else if(strtolower($value) == 'ccc') $result = 2;
        }
        return $result;
    }

    function getService($value){
        $result = '';
        if(!empty($value)){
            if(strtolower($value) == 'art clinic' || strtolower($value) == 'ccc') $result = 1;
            else if(strtolower($value) == 'anc and pmtct services') $result = 3;
        }
        return $result;
    }

    function getRegimen($value){
        $result = '';
        if(!empty($value)) {
            $regimen = Regimen::where("regimen_code", "like", "%".$value."%")->first();
            if(!empty($regimen)){
                $result = $regimen->id;
            }
        }
        return $result;
    }

    function getStatus($value){
        $result = '';
        if(!empty($value)){
            if(strtolower($value) == 'active') $result = 1;
            else if(strtolower($value) == 'lost' || strtolower($value) == 'lost - not care-ended') $result = 5;
            if(strtolower($value) == 'transfer' || strtolower($value) == 'referred to another facility') $result = 8;
            if(strtolower($value) == 'death') $result = 2;
        }
        return $result;
    }

    function getProphylaxis($value){
        $result = '';
        if(!empty($value)){
            if(
                strpos(strtolower($value),'ctx') !== false ||
                strpos(strtolower($value),'cotrimoxazole') !== false ||
                strpos(strtolower($value),'co-trimoxazole') !== false ||
                strpos(strtolower($value),'cotrimoxazole') !== false
            ) return 1;
            elseif(strpos(strtolower($value), 'fluconazole') !== false) return 4;
            elseif(strpos(strtolower($value), 'dapsone') !== false) return 2;
            elseif(
                strpos(strtolower($value),'rifapentine') !== false &&
                strpos(strtolower($value),'isoniazid') !== false
            ) return 5;
            elseif(strpos(strtolower($value), 'isoniazid') !== false) return 3;
        }
        return $result;
    }

    function getDifferentiatedCare($value){
        $result = 0;
        if(!empty($value)){
            $result = 1;
        }
        return $result;
    }

}