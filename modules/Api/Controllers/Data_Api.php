<?php

namespace Modules\Api\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Ftp;
use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;
use App\Libraries\Mysqldump;
use App\Libraries\Zip;
use CodeIgniter\HTTP\Response;
use Exception;
use \Modules\Api\Models\Api_model;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client;
use Illuminate\Database\Capsule\Manager as DB;
use Modules\ADT\Models\Patient;
use Modules\ADT\Models\PatientSource;
use Modules\ADT\Models\Regimen;

class Data_Api extends BaseController {

    /**
     * Migrator main controller.
     *
     * @author Kevin Marete
     */
    var $success = 200;
    var $error = 400;
    var $exists = 409;
    var $session;
    var $db;
    var $table;
    var $api_model;

    public function __construct() {
        ini_set("max_execution_time", "100000");
        ini_set("memory_limit", '2048M');

        $this->session = \Config\Services::session();
        $this->db = \Config\Database::connect();
    }

    function createPatient() {
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        $PEP_REASON = '';
        $internal_patient = $this->getPatientDA($data->patient_number);
        // getPatientInternalID($external_id,$ASSIGNING_AUTHORITY)
        if ($internal_patient) {
            $this->getApiResponse($this->exists, 'Patient already exists');
        }



        if (!empty($data->pep)) {
            foreach ($data->prep as $p) {
                $PEP_REASON = $p->pep_reason;
            }
        };




        $ccc_no = (empty($data->patient_number) ? $this->writeLog('PATIENT', 'CCC Missing') : $data->patient_number);
        $medical_record_no = $data->medical_record_no;


        $FIRST_NAME = (empty($data->first_name) ? '' : $data->first_name);
        $MIDDLE_NAME = (empty($data->other_name) ? '' : $data->other_name);
        $LAST_NAME = (empty($data->last_name) ? '' : $data->last_name);
        $PLACE_OF_BIRTH = (empty($data->place_of_birth) ? '' : $data->place_of_birth);
        $SENDING_FACILITY = (empty($data->mfl_code) ?  $this->getApiResponse($this->error, 'Please set facility code') : $data->mfl_code);


        //$MOTHER_NAME = empty($data->last_name')) ? '' : $data->last_name');
        $DATE_OF_BIRTH = (empty($data->date_of_birth) ? $this->writeLog('PATIENT', 'DOB Missing') : $data->date_of_birth);
        $SEX = empty($data->gender) ? $this->getApiResponse($this->error, 'Gender missing') : $data->gender;
        $VILLAGE = (empty($data->place_of_birth) ? '' : $data->place_of_birth);
        $WARD = (empty($data->place_of_birth) ? '' : $data->place_of_birth);
        $SUB_COUNTY = (empty($data->subcounty) ? '' : $data->subcounty);
        $COUNTY = (empty($data->county) ? '' : $data->county);
        $POSTAL_ADDRESS = (empty($data->address) ? '' : $data->address);
        $PHONE_NUMBER = (empty($data->phone) ? '' : $data->phone);
        $MARITAL_STATUS = (empty($data->last_name) ? '' : $data->last_name);
        $DEATH_DATE = (empty($data->last_name) ? '' : $data->last_name);
        $DEATH_INDICATOR = (empty($data->last_name) ? '' : $data->last_name);

        $ENROLLMENT_DATE = empty($data->enrollment_date) ? '' : $data->enrollment_date;


        $START_HEIGHT = (empty($data->height) ? $this->getApiResponse($this->error, 'Start Heght missing') : $data->height);
        $START_WEIGHT = (empty($data->weight) ? $this->getApiResponse($this->error, 'Start Weight missing') : $data->weight);

        $IS_PREGNANT = (empty($data->pregnant) ? '' : $data->pregnant);
        $PARTNER_STATUS = (empty($data->partner_status) ? '' : $data->partner_status);
        $CURRENT_REGIMEN = (empty($data->start_regimen) ? '' : $data->start_regimen);
        $IS_SMOKER = (empty($data->smoke) ? '' : $data->smoke);
        $IS_ALCOHOLIC = (empty($data->alcohol) ? '' : $data->alcohol);
        $WHO_STAGE = (empty($data->who_stage) ? '' : $data->who_stage);
        $ART_START = (empty($data->start_regimen_date) ? '' : $data->start_regimen_date);

        $new_patient = [
            'facility_code' => $SENDING_FACILITY,
            'medical_record_number' => $medical_record_no,
            'dob' => $DATE_OF_BIRTH, // substr($DATE_OF_BIRTH, 0, 4) . '-' . substr($DATE_OF_BIRTH, 4, 2) . '-' . substr($DATE_OF_BIRTH, -2),
            'first_name' => $FIRST_NAME,
            'gender' => ($data->gender) == 'M' ? 1 : 2,
            'last_name' => $LAST_NAME,
            'other_name' => $MIDDLE_NAME,
            'patient_number_ccc' => $ccc_no,
            'phone' => $PHONE_NUMBER,
            'physical' => $POSTAL_ADDRESS,
            'pob' => $VILLAGE,
            'pob' => $WARD,
            'pob' => $SUB_COUNTY,
            'pob' => $COUNTY,
            'alcohol' => $IS_ALCOHOLIC,
            'current_regimen' => $this->getRegimenId($CURRENT_REGIMEN),
            'height' => $START_HEIGHT,
            'pregnant' => $IS_PREGNANT,
            'smoke' => $IS_SMOKER,
            'start_height' => $START_HEIGHT,
            'start_regimen' => $this->getRegimenId($CURRENT_REGIMEN),
            'start_weight' => $START_WEIGHT,
            'active' => 1,
            'service' => 1,
            'date_enrolled' => $ENROLLMENT_DATE, //substr($ENROLLMENT_DATE, 0, 4) . '-' . substr($ENROLLMENT_DATE, 4, 2) . '-' . substr($ENROLLMENT_DATE, -2),
            'current_status' => $data->current_status,
            'weight' => $START_WEIGHT,
            'who_stage' => $WHO_STAGE,
            'start_regimen_date' => $ART_START, // substr($ART_START, 0, 4) . '-' . substr($ART_START, 4, 2) . '-' . substr($ART_START, -2),
            'source' => $this->getSource($data->source),
            'partner_status' => $this->partnerStatus($PARTNER_STATUS),
            'fplan' => $data->family_planning,
            'pep_reason' => $PEP_REASON,
        ];

        /* if (!empty($data->prep)) {
          foreach ($data->prep as $p) {
          $PREP_REASON = $p->prep_reason;
          $PREP_TEST_ANSWER = $p->prep_test_answer;
          $PREP_TEST_DATE = $p->prep_test_date;
          $PREP_TEST_RESULT = $p->prep_test_result;
          }


          $insert_id = DB::table('patient')->insert($data);
          } */



        $this->writeLog('msg', json_encode($new_patient));
        $internal_patient_id = $this->savePatient($new_patient);
        $this->writeLog('internal_patient_id ', json_encode($internal_patient_id));
        $this->getApiResponse($this->success, 'Patient Indormation Saved');
    }

    function getApiResponse($code, $message) {
        echo json_encode(['code' => $code, 'message' => $message]);
        die;
    }

    function getPatientDA($internal_id) {

        $cond = '';
        $query_str = "SELECT p.*,ps.name as patient_status,pso.name as patient_source ,g.name as patient_gender FROM patient p " .
                "left join patient_status ps on p.current_status = ps.id " .
                "left join patient_source pso on p.source = pso.id " .
                "left join gender g on g.id = p.gender " .
                "WHERE p.patient_number_ccc = '" . $internal_id . "' ";

        // do left join in the case of patient created on adt and not already on IL


        $query = DB::select($query_str);

        if (count($query) > 0) {
            $returnable = $query[0];
        } else {
            $returnable = false;
        }
        return $returnable;
    }

    function savePatient($patient) {
        $insert_id = DB::table('patient')->insertGetId($patient);
        return $insert_id;
    }

    function processPatientUpdate($patient) {

        $identification = array();
        foreach ($patient->PATIENT_IDENTIFICATION->INTERNAL_PATIENT_ID as $id) {
            $identification[$id->IDENTIFIER_TYPE] = $id->ID;
        }
        $ccc_no = ($identification['CCC_NUMBER']);

        $internal_patient = $this->api_model->getPatient($ccc_no);
        if (!$internal_patient) {
            $this->processPatientRegistration($patient);
            die;
            // registration successful exit(0)
        }

        $internal_patient_id = $internal_patient->id;
        $FIRST_NAME = $patient->PATIENT_IDENTIFICATION->PATIENT_NAME->FIRST_NAME;
        $MIDDLE_NAME = $patient->PATIENT_IDENTIFICATION->PATIENT_NAME->MIDDLE_NAME;
        $LAST_NAME = $patient->PATIENT_IDENTIFICATION->PATIENT_NAME->LAST_NAME;

        $MOTHER_NAME = $patient->PATIENT_IDENTIFICATION->MOTHER_NAME;
        $DATE_OF_BIRTH = $patient->PATIENT_IDENTIFICATION->DATE_OF_BIRTH;
        $SEX = ( $patient->PATIENT_IDENTIFICATION->SEX == 'M') ? 1 : 2;
        $VILLAGE = $patient->PATIENT_IDENTIFICATION->PATIENT_ADDRESS->PHYSICAL_ADDRESS->VILLAGE;
        // var_dump($patient);die;
        $WARD = $patient->PATIENT_IDENTIFICATION->PATIENT_ADDRESS->PHYSICAL_ADDRESS->WARD;
        $SUB_COUNTY = $patient->PATIENT_IDENTIFICATION->PATIENT_ADDRESS->PHYSICAL_ADDRESS->SUB_COUNTY;
        $COUNTY = $patient->PATIENT_IDENTIFICATION->PATIENT_ADDRESS->PHYSICAL_ADDRESS->COUNTY;
        $POSTAL_ADDRESS = $patient->PATIENT_IDENTIFICATION->PATIENT_ADDRESS->POSTAL_ADDRESS;
        $PHONE_NUMBER = $patient->PATIENT_IDENTIFICATION->PHONE_NUMBER;
        $MARITAL_STATUS = $patient->PATIENT_IDENTIFICATION->MARITAL_STATUS;
        $DEATH_DATE = $patient->PATIENT_IDENTIFICATION->DEATH_DATE;
        $DEATH_INDICATOR = $patient->PATIENT_IDENTIFICATION->DEATH_INDICATOR;

        $ENROLLMENT_DATE = $patient->PATIENT_VISIT->HIV_CARE_ENROLLMENT_DATE;

        $patient = array(
            'dob' => substr($DATE_OF_BIRTH, 0, 4) . '-' . substr($DATE_OF_BIRTH, 4, 2) . '-' . substr($DATE_OF_BIRTH, -2),
            'first_name' => $FIRST_NAME,
            'gender' => $SEX,
            'last_name' => $LAST_NAME,
            'other_name' => $MIDDLE_NAME,
            'patient_number_ccc' => $ccc_no,
            'phone' => $PHONE_NUMBER,
            'physical' => $POSTAL_ADDRESS,
            'pob' => $VILLAGE,
            'pob' => $WARD,
            'pob' => $SUB_COUNTY,
            'pob' => $COUNTY,
            'alcohol' => ' ',
            'current_regimen' => ' ',
            'height' => ' ',
            'pregnant' => ' ',
            'smoke' => ' ',
            'start_height' => ' ',
            'start_regimen' => ' ',
            'start_weight' => ' ',
            'active' => 1,
            'date_enrolled' => substr($ENROLLMENT_DATE, 0, 4) . '-' . substr($ENROLLMENT_DATE, 4, 2) . '-' . substr($ENROLLMENT_DATE, -2),
            // 'current_status' => 1,
            'current_status' => $this->api_model->getActivePatientStatus()->id,
            'weight' => ' '
        );

        $result = $this->api_model->updatePatient($patient, $internal_patient_id);
        var_dump($result);
    }

    function processObservation($obx) {
        $identification = array();

        foreach ($obx->PATIENT_IDENTIFICATION->INTERNAL_PATIENT_ID as $id) {
            $identification[$id->IDENTIFIER_TYPE] = $id->ID;
        }
        $ccc_no = ($identification['CCC_NUMBER']);
        $internal_patient = $this->api_model->getPatient($ccc_no);

        if (!$internal_patient) {
            $this->writeLog('ORU Error ', "patient does not exist. Can't process observation");
            die;
        }

        $internal_patient_id = $internal_patient->id;
        $SENDING_FACILITY = $obx->MESSAGE_HEADER->SENDING_FACILITY;

        // Observation Result(s) - Array of Objects
        $observations = [];
        foreach ($obx->OBSERVATION_RESULT as $ob) {
            $observations[$ob->OBSERVATION_IDENTIFIER] = $ob->OBSERVATION_VALUE;
        }
        $START_HEIGHT = (isset($observations['START_HEIGHT'])) ? $observations['START_HEIGHT'] : false;
        $START_WEIGHT = (isset($observations['START_WEIGHT'])) ? $observations['START_WEIGHT'] : false;

        $IS_PREGNANT = (isset($observations['IS_PREGNANT'])) ? $observations['IS_PREGNANT'] : false;
        $PREGNANT_EDD = (isset($observations['PREGNANT_EDD'])) ? $observations['PREGNANT_EDD'] : false;
        $CURRENT_REGIMEN = (isset($observations['CURRENT_REGIMEN'])) ? $observations['CURRENT_REGIMEN'] : false;
        $IS_SMOKER = (isset($observations['IS_SMOKER'])) ? $observations['IS_SMOKER'] : false;
        $IS_ALCOHOLIC = (isset($observations['IS_ALCOHOLIC'])) ? $observations['IS_ALCOHOLIC'] : false;
        $REGIMEN_CHANGE_REASON = (isset($observations['REGIMEN_CHANGE_REASON'])) ? $observations['REGIMEN_CHANGE_REASON'] : false;
        if ($REGIMEN_CHANGE_REASON) {
            // do regimen change/ drug stop
            // var_dump($REGIMEN_CHANGE_REASON);die;
        }

        $observation = array('facility_code' => $SENDING_FACILITY,
            'patient_number_ccc' => $ccc_no,
            'pregnant' => $IS_PREGNANT,
            'smoke' => $IS_SMOKER,
            'height' => $START_HEIGHT,
            'start_height' => $START_HEIGHT,
            'start_regimen' => $CURRENT_REGIMEN,
            'start_weight' => $START_WEIGHT,
            'weight' => $START_HEIGHT);
        $result = $this->api_model->updatePatient($observation, $internal_patient_id);
    }

    function processAppointment($appointment) {
        $identification = array();
        foreach ($appointment->PATIENT_IDENTIFICATION->INTERNAL_PATIENT_ID as $id) {
            $identification[$id->IDENTIFIER_TYPE] = $id->ID;
        }
        $ccc_no = ($identification['CCC_NUMBER']);
        $internal_patient_ccc = $this->api_model->getPatient($ccc_no);

        if (!$internal_patient_ccc) {
            $this->writeLog('Patient not found ', $internal_patient_ccc);
            die;
        }

        $SENDING_APPLICATION = $appointment->MESSAGE_HEADER->SENDING_APPLICATION;
        $SENDING_FACILITY = $appointment->MESSAGE_HEADER->SENDING_FACILITY;
        $RECEIVING_APPLICATION = $appointment->MESSAGE_HEADER->RECEIVING_APPLICATION;
        $RECEIVING_FACILITY = $appointment->MESSAGE_HEADER->RECEIVING_FACILITY;
        $MESSAGE_DATETIME = $appointment->MESSAGE_HEADER->MESSAGE_DATETIME;
        $SECURITY = $appointment->MESSAGE_HEADER->SECURITY;
        $MESSAGE_TYPE = $appointment->MESSAGE_HEADER->MESSAGE_TYPE;
        $PROCESSING_ID = $appointment->MESSAGE_HEADER->PROCESSING_ID;



        $EXTERNAL_PATIENT_ID = $appointment->PATIENT_IDENTIFICATION->EXTERNAL_PATIENT_ID->ID;
        $INTERNAL_PATIENT_ID = $appointment->PATIENT_IDENTIFICATION->INTERNAL_PATIENT_ID[0]->ID;

        $FIRST_NAME = $appointment->PATIENT_IDENTIFICATION->PATIENT_NAME->FIRST_NAME;
        $MIDDLE_NAME = $appointment->PATIENT_IDENTIFICATION->PATIENT_NAME->MIDDLE_NAME;
        $LAST_NAME = $appointment->PATIENT_IDENTIFICATION->PATIENT_NAME->LAST_NAME;
        $PAN_NUMBER = $appointment->APPOINTMENT_INFORMATION[0]->PLACER_APPOINTMENT_NUMBER->NUMBER;
        $PAN_ENTITY = $appointment->APPOINTMENT_INFORMATION[0]->PLACER_APPOINTMENT_NUMBER->ENTITY;
        $APPOINTMENT_REASON = $appointment->APPOINTMENT_INFORMATION[0]->APPOINTMENT_REASON;
        $APPOINTMENT_TYPE = $appointment->APPOINTMENT_INFORMATION[0]->APPOINTMENT_TYPE;
        $APPOINTMENT_DATE = $appointment->APPOINTMENT_INFORMATION[0]->APPOINTMENT_DATE;
        $APPOINTMENT_DATE = substr($APPOINTMENT_DATE, 0, 4) . '-' . substr($APPOINTMENT_DATE, 4, 2) . '-' . substr($APPOINTMENT_DATE, -2);
        $APPOINTMENT_PLACING_ENTITY = $appointment->APPOINTMENT_INFORMATION[0]->APPOINTMENT_PLACING_ENTITY;
        $APPOINTMENT_LOCATION = $appointment->APPOINTMENT_INFORMATION[0]->APPOINTMENT_LOCATION;
        $ACTION_CODE = $appointment->APPOINTMENT_INFORMATION[0]->ACTION_CODE;
        $APPOINTMENT_NOTE = $appointment->APPOINTMENT_INFORMATION[0]->APPOINTMENT_NOTE;
        $APPOINTMENT_STATUS = $appointment->APPOINTMENT_INFORMATION[0]->APPOINTMENT_STATUS;

        $patient_appointment = array(
            'patient' => $internal_patient_ccc->patient_number_ccc,
            'facility' => $SENDING_FACILITY,
            'appointment' => $APPOINTMENT_DATE
        );
        $this->writeLog('saving patient appointment ', json_encode($patient_appointment));
        $this->writeLog('appointment type ', json_encode($APPOINTMENT_TYPE));

        $res = $this->api_model->saveAppointment($patient_appointment, $APPOINTMENT_TYPE);
        $this->writeLog('saved  patient appointment ', json_encode($res));
    }

    function processDrugOrder($order) {
        $identification = array();
        foreach ($order->PATIENT_IDENTIFICATION->INTERNAL_PATIENT_ID as $id) {
            $identification[$id->IDENTIFIER_TYPE] = $id->ID;
        }
        $ccc_no = ($identification['CCC_NUMBER']);

        $SENDING_FACILITY = $order->MESSAGE_HEADER->SENDING_FACILITY;
        $internal_patient_ccc = $this->api_model->getPatient($ccc_no);
        // $internal_patient_ccc = $this->parseCCC($internal_patient_ccc,$SENDING_FACILITY);

        if (!$internal_patient_ccc) {
            $this->writeLog('Patient not found ', $ccc_no);
            //$this->processPatientRegistration($order);
        }


        $SENDING_APPLICATION = $order->MESSAGE_HEADER->SENDING_APPLICATION;
        $RECEIVING_APPLICATION = $order->MESSAGE_HEADER->RECEIVING_APPLICATION;
        $RECEIVING_FACILITY = $order->MESSAGE_HEADER->RECEIVING_FACILITY;
        $MESSAGE_DATETIME = $order->MESSAGE_HEADER->MESSAGE_DATETIME;
        $SECURITY = $order->MESSAGE_HEADER->SECURITY;
        $MESSAGE_TYPE = $order->MESSAGE_HEADER->MESSAGE_TYPE;
        $PROCESSING_ID = $order->MESSAGE_HEADER->PROCESSING_ID;
        $EXTERNAL_PATIENT_ID = $order->PATIENT_IDENTIFICATION->EXTERNAL_PATIENT_ID->ID;
        $INTERNAL_PATIENT_ID = $order->PATIENT_IDENTIFICATION->INTERNAL_PATIENT_ID[0]->ID;

        $FIRST_NAME = $order->PATIENT_IDENTIFICATION->PATIENT_NAME->FIRST_NAME;
        $MIDDLE_NAME = $order->PATIENT_IDENTIFICATION->PATIENT_NAME->MIDDLE_NAME;
        $LAST_NAME = $order->PATIENT_IDENTIFICATION->PATIENT_NAME->LAST_NAME;

        $ORDER_CONTROL = $order->COMMON_ORDER_DETAILS->ORDER_CONTROL;
        $PLACER_ORDER_NUMBER = $order->COMMON_ORDER_DETAILS->PLACER_ORDER_NUMBER->NUMBER;
        $ORDER_STATUS = $order->COMMON_ORDER_DETAILS->ORDER_STATUS;
        $OP_FIRST_NAME = $order->COMMON_ORDER_DETAILS->ORDERING_PHYSICIAN->FIRST_NAME;
        $OP_MIDDLE_NAME = $order->COMMON_ORDER_DETAILS->ORDERING_PHYSICIAN->MIDDLE_NAME;
        $OP_LAST_NAME = $order->COMMON_ORDER_DETAILS->ORDERING_PHYSICIAN->LAST_NAME;
        $OP_PREFIX = $order->COMMON_ORDER_DETAILS->ORDERING_PHYSICIAN->PREFIX;
        $TRANSACTION_DATETIME = $order->COMMON_ORDER_DETAILS->TRANSACTION_DATETIME;
        $NOTES = $order->COMMON_ORDER_DETAILS->NOTES;
        $pe = [];

        $observations = [];
        if (!empty($order->OBSERVATION_RESULT)) {
            foreach ($order->OBSERVATION_RESULT as $ob) {
                $observations[$ob->OBSERVATION_IDENTIFIER] = $ob->OBSERVATION_VALUE;
            }
        }
        $HEIGHT = (isset($observations['HEIGHT'])) ? (empty($observations['HEIGHT']) ? '' : $observations['HEIGHT']) : null;
        $WEIGHT = (isset($observations['WEIGHT'])) ? (empty($observations['WEIGHT']) ? '' : $observations['WEIGHT']) : null;
        $CURRENT_REGIMEN = (isset($observations['CURRENT_REGIMEN'])) ? (empty($observations['CURRENT_REGIMEN']) ? '' : $observations['CURRENT_REGIMEN']) : false;


        // PHARMACY_ENCODED_ORDER

        $pe_order = [];
        foreach ($order->PHARMACY_ENCODED_ORDER as $eo) {
            array_push($pe_order, $eo);
        }


        $pe = array(
            'order_number' => $PLACER_ORDER_NUMBER,
            'order_status' => $ORDER_STATUS,
            'patient' => $ccc_no,
            'order_physician' => $OP_FIRST_NAME . ' ' . $OP_MIDDLE_NAME . ' ' . $OP_LAST_NAME,
            'notes' => $NOTES,
            'height' => $HEIGHT,
            'weight' => $WEIGHT,
            'current_regimen' => $CURRENT_REGIMEN
        );

        $this->writeLog('prescription ', json_encode($pe));
        $this->writeLog('prescription order ', json_encode($pe_order));

        // var_dump($pe);
        $res = $this->api_model->saveDrugPrescription($pe, $pe_order);
        $this->writeLog('res ', json_encode($res));

        # @todo check if order exists
        # if doesn't exist, create new order .
        # else update 
    }

    public function getAppointment($appointment_id) {
        $pat = $this->api_model->getPatientAppointment($appointment_id);
        $message_type = 'SIU^S12';

        $appoint['MESSAGE_HEADER'] = array(
            'SENDING_APPLICATION' => "ADT",
            'SENDING_FACILITY' => $pat->facility_code,
            'RECEIVING_APPLICATION' => "IL",
            'RECEIVING_FACILITY' => $pat->facility_code,
            'MESSAGE_DATETIME' => date('Ymdhis'),
            'SECURITY' => "",
            'MESSAGE_TYPE' => $message_type,
            'PROCESSING_ID' => "P"
        );
        $appoint['PATIENT_IDENTIFICATION'] = array(
            'EXTERNAL_PATIENT_ID' => array('ID' => '', 'IDENTIFIER_TYPE' => "GODS_NUMBER", 'ASSIGNING_AUTHORITY' => "MPI"),
            'INTERNAL_PATIENT_ID' => [
                array('ID' => $this->constructCCC($pat->patient_number_ccc, $pat->facility_code, true), 'IDENTIFIER_TYPE' => "CCC_NUMBER", 'ASSIGNING_AUTHORITY' => "CCC")
            ],
            'PATIENT_NAME' => array('FIRST_NAME' => $pat->first_name, 'MIDDLE_NAME' => $pat->last_name, 'LAST_NAME' => $pat->other_name)
        );
        $appoint['APPOINTMENT_INFORMATION'] = [array(
        'PLACER_APPOINTMENT_NUMBER' => array('NUMBER' => $appointment_id, 'ENTITY' => "ADT"),
        'APPOINTMENT_REASON' => "REGIMEN REFILL",
        'APPOINTMENT_TYPE' => "PHARMACY APPOINTMENT",
        'APPOINTMENT_DATE' => $pat->appointment,
        'APPOINTMENT_PLACING_ENTITY' => "ADT",
        'APPOINTMENT_LOCATION' => "PHARMACY",
        'ACTION_CODE' => "A",
        'APPOINTMENT_NOTE' => "TO COME BACK FOR A REFILL",
        'APPOINTMENT_STATUS' => "PENDING"
        )];


        $this->writeLog('APPOINTMENT SCHEDULE SIU^S12 ', json_encode($appoint));
        $this->tcpILRequest(null, json_encode($appoint));
    }

    public function getPatient($patient_id, $msg_type) {
        $pat = $this->api_model->getPatientbyID($patient_id);


        $message_type = ($msg_type == 'ADD') ? 'ADT^A04' : 'ADT^A08';
        $patient['MESSAGE_HEADER'] = array(
            'SENDING_APPLICATION' => "ADT",
            'SENDING_FACILITY' => $pat->facility_code,
            'RECEIVING_APPLICATION' => "IL",
            'RECEIVING_FACILITY' => $pat->facility_code,
            'MESSAGE_DATETIME' => date('Ymdhis'),
            'SECURITY' => "",
            'MESSAGE_TYPE' => $message_type,
            'PROCESSING_ID' => "P"
        );
        $patient['PATIENT_IDENTIFICATION'] = array(
            'EXTERNAL_PATIENT_ID' => $this->api_model->getPatientExternalID($patient_id),
            // array('ID'=>$pat->external_id, 'IDENTIFIER_TYPE' =>"GODS_NUMBER",'ASSIGNING_AUTHORITY' =>"MPI"),
            // fetch external identifications
            'INTERNAL_PATIENT_ID' => [
                ['ID' => $pat->id, 'IDENTIFIER_TYPE' => "SOURCE_SYSTEM_ID", 'ASSIGNING_AUTHORITY' => "ADT"],
                ['ID' => $this->constructCCC($pat->patient_number_ccc, $pat->facility_code, true), 'IDENTIFIER_TYPE' => "CCC_NUMBER", 'ASSIGNING_AUTHORITY' => "CCC"]
            ],
            'PATIENT_NAME' => ['FIRST_NAME' => $pat->first_name, 'MIDDLE_NAME' => $pat->other_name, 'LAST_NAME' => $pat->last_name],
            'DATE_OF_BIRTH' => date('Ymd', strtotime($pat->dob)),
            'DATE_OF_BIRTH_PRECISION' => 'EXACT',
            'SEX' => substr($pat->patient_gender, 0, 1),
            'PATIENT_ADDRESS' => ['PHYSICAL_ADDRESS' => ['VILLAGE' => '', 'WARD' => '', 'SUB_COUNTY' => '', 'COUNTY' => ''], 'POSTAL_ADDRESS' => $pat->pob],
            'PHONE_NUMBER' => $pat->phone,
            'MARITAL_STATUS' => $pat->partner_status, // if partner the nyes other wise unknown
            'DEATH_DATE' => '',
            'DEATH_INDICATOR' => ''
        );
        $patient['NEXT_OF_KIN'] = [];

        $patient['PATIENT_VISIT'] = [
            'VISIT_DATE' => date_format(date_create_from_format('Y-m-d', $pat->date_enrolled), 'Ymd'),
            'PATIENT_TYPE' => 'NEW', // TRANSFER IN, NEW = active, TRANSIT, 
            'PATIENT_SOURCE' => 'CCC',
            'HIV_CARE_ENROLLMENT_DATE' => date('Ymd', strtotime($pat->date_enrolled))
        ];

        $pat_oru = $this->api_model->getPatient($pat->patient_number_ccc);

        // construct & add observation (obx ) message
        $patient['OBSERVATION_RESULT'] = [
            [
                'SET_ID' => 1,
                'OBSERVATION_IDENTIFIER' => 'START_HEIGHT',
                'CODING_SYSTEM' => 1,
                'VALUE_TYPE' => "NM",
                'OBSERVATION_VALUE' => $pat_oru->start_height,
                'UNITS' => "CM",
                'OBSERVATION_RESULT_STATUS' => "F",
                'OBSERVATION_DATETIME' => date('Ymdhis', strtotime($pat_oru->date_enrolled)),
                'ABNORMAL_FLAGS' => "N"],
            [
                'SET_ID' => "2",
                'OBSERVATION_IDENTIFIER' => "START_WEIGHT",
                'CODING_SYSTEM' => "",
                'VALUE_TYPE' => "NM",
                'OBSERVATION_VALUE' => $pat_oru->start_weight,
                'UNITS' => "KG",
                'OBSERVATION_RESULT_STATUS' => "F",
                'OBSERVATION_DATETIME' => date('Ymdhis', strtotime($pat_oru->date_enrolled)),
                'ABNORMAL_FLAGS' => "N"
            ],
            [
                'SET_ID' => "3",
                'OBSERVATION_IDENTIFIER' => 'IS_PREGNANT',
                'CODING_SYSTEM' => "",
                'VALUE_TYPE' => "CE",
                'OBSERVATION_VALUE' => ($pat_oru->pregnant == '0') ? 'NO' : 'Yes',
                'UNITS' => "YES/NO",
                'OBSERVATION_RESULT_STATUS' => "F",
                'OBSERVATION_DATETIME' => date('Ymdhis', strtotime($pat_oru->date_enrolled)),
                'ABNORMAL_FLAGS' => "N"
            ],
            [
                'SET_ID' => "4",
                'OBSERVATION_IDENTIFIER' => "PREGNANT_EDD",
                'CODING_SYSTEM' => "",
                'VALUE_TYPE' => "D",
                'OBSERVATION_VALUE' => "20170713110000",
                'UNITS' => "DATE",
                'OBSERVATION_RESULT_STATUS' => "F",
                'OBSERVATION_DATETIME' => date('Ymdhis', strtotime($pat_oru->date_enrolled)),
                'ABNORMAL_FLAGS' => "N"
            ],
            [
                'SET_ID' => "5",
                'OBSERVATION_IDENTIFIER' => "CURRENT_REGIMEN",
                'CODING_SYSTEM' => "NASCOP_CODES",
                'VALUE_TYPE' => "CE",
                'OBSERVATION_VALUE' => $pat_oru->current_regimen,
                'UNITS' => "",
                'OBSERVATION_RESULT_STATUS' => "F",
                'OBSERVATION_DATETIME' => date('Ymdhis', strtotime($pat_oru->date_enrolled)),
                'ABNORMAL_FLAGS' => "N"
            ],
            [
                'SET_ID' => "6",
                'OBSERVATION_IDENTIFIER' => "IS_SMOKER",
                'CODING_SYSTEM' => "",
                'VALUE_TYPE' => "CE",
                'OBSERVATION_VALUE' => ($pat_oru->smoke == '0') ? 'NO' : 'Yes',
                'UNITS' => "YES/NO",
                'OBSERVATION_RESULT_STATUS' => "F",
                'OBSERVATION_DATETIME' => date('Ymdhis', strtotime($pat_oru->date_enrolled)),
                'ABNORMAL_FLAGS' => "N"
            ],
            [
                'SET_ID' => "7",
                'OBSERVATION_IDENTIFIER' => "IS_ALCOHOLIC",
                'CODING_SYSTEM' => "",
                'VALUE_TYPE' => "CE",
                'OBSERVATION_VALUE' => ($pat_oru->alcohol == '0') ? 'NO' : 'Yes',
                'UNITS' => "YES/NO",
                'OBSERVATION_RESULT_STATUS' => "F",
                'OBSERVATION_DATETIME' => date('Ymdhis', strtotime($pat_oru->date_enrolled)),
                'ABNORMAL_FLAGS' => "N"
            ],
            [
                'SET_ID' => "8",
                'OBSERVATION_IDENTIFIER' => "WHO_STAGE",
                'CODING_SYSTEM' => "",
                'VALUE_TYPE' => "N",
                'OBSERVATION_VALUE' => ($pat_oru->who_stage == '0') ? '' : $pat_oru->who_stage,
                'UNITS' => "",
                'OBSERVATION_RESULT_STATUS' => "F",
                'OBSERVATION_DATETIME' => date('Ymdhis', strtotime($pat_oru->date_enrolled)),
                'ABNORMAL_FLAGS' => "N"
            ],
            [
                'SET_ID' => "9",
                'OBSERVATION_IDENTIFIER' => "ART_START",
                'CODING_SYSTEM' => "",
                'VALUE_TYPE' => "N",
                'OBSERVATION_VALUE' => empty($pat_oru->start_regimen_date) ? '' : date('Ymd', strtotime($pat_oru->start_regimen_date)),
                'UNITS' => "",
                'OBSERVATION_RESULT_STATUS' => "F",
                'OBSERVATION_DATETIME' => date('Ymdhis', strtotime($pat_oru->date_enrolled)),
                'ABNORMAL_FLAGS' => "N"
            ]
        ];

        $this->writeLog('PATIENT ' . $msg_type . ' ' . $message_type, json_encode($patient));
        $this->tcpILRequest(null, json_encode($patient));
        // $this->getObservation($pat->patient_number_ccc);
    }

    public function getObservation($patient_id) {
        echo "sending observations";
        $pat = $this->api_model->getPatient($patient_id);

        $message_type = 'ORU^R01';
        $observations['MESSAGE_HEADER'] = array(
            'SENDING_APPLICATION' => "ADT",
            'SENDING_FACILITY' => $pat->facility_code,
            'RECEIVING_APPLICATION' => "IL",
            'RECEIVING_FACILITY' => $pat->facility_code,
            'MESSAGE_DATETIME' => date('Ymdhis'),
            'SECURITY' => "",
            'MESSAGE_TYPE' => $message_type,
            'PROCESSING_ID' => "P"
        );
        $observations['PATIENT_IDENTIFICATION'] = array(
            'EXTERNAL_PATIENT_ID' => $this->api_model->getPatientExternalID($patient_id),
            // array('ID'=>$pat->external_id, 'IDENTIFIER_TYPE' =>"GODS_NUMBER",'ASSIGNING_AUTHORITY' =>"MPI"),
            // fetch external identifications
            'INTERNAL_PATIENT_ID' => [
                array('ID' => $pat->id, 'IDENTIFIER_TYPE' => "SOURCE_SYSTEM_ID", 'ASSIGNING_AUTHORITY' => "ADT"),
                array('ID' => $this->constructCCC($pat->patient_number_ccc, $pat->facility_code, true), 'IDENTIFIER_TYPE' => "CCC_NUMBER", 'ASSIGNING_AUTHORITY' => "CCC")
            ],
            'PATIENT_NAME' => array('FIRST_NAME' => $pat->first_name, 'MIDDLE_NAME' => $pat->last_name, 'LAST_NAME' => $pat->other_name)
        );

        // construct & send observation (obx ) message
        $observations['OBSERVATION_RESULT'] = array(
            array(
                'SET_ID' => 1,
                'OBSERVATION_IDENTIFIER' => 'START_HEIGHT',
                'CODING_SYSTEM' => 1,
                'VALUE_TYPE' => "NM",
                'OBSERVATION_VALUE' => $pat->start_height,
                'UNITS' => "CM",
                'OBSERVATION_RESULT_STATUS' => "F",
                'OBSERVATION_DATETIME' => date('Ymdhis', strtotime($pat->date_enrolled)),
                'ABNORMAL_FLAGS' => "N"),
            array(
                'SET_ID' => "2",
                'OBSERVATION_IDENTIFIER' => "START_WEIGHT",
                'CODING_SYSTEM' => "",
                'VALUE_TYPE' => "NM",
                'OBSERVATION_VALUE' => $pat->start_weight,
                'UNITS' => "KG",
                'OBSERVATION_RESULT_STATUS' => "F",
                'OBSERVATION_DATETIME' => date('Ymdhis', strtotime($pat->date_enrolled)),
                'ABNORMAL_FLAGS' => "N"
            ),
            array(
                'SET_ID' => "3",
                'OBSERVATION_IDENTIFIER' => 'IS_PREGNANT',
                'CODING_SYSTEM' => "",
                'VALUE_TYPE' => "CE",
                'OBSERVATION_VALUE' => ($pat->pregnant == '0') ? 'NO' : 'Yes',
                'UNITS' => "YES/NO",
                'OBSERVATION_RESULT_STATUS' => "F",
                'OBSERVATION_DATETIME' => date('Ymdhis', strtotime($pat->date_enrolled)),
                'ABNORMAL_FLAGS' => "N"
            ),
            array(
                'SET_ID' => "4",
                'OBSERVATION_IDENTIFIER' => "PREGNANT_EDD",
                'CODING_SYSTEM' => "",
                'VALUE_TYPE' => "D",
                'OBSERVATION_VALUE' => "20170713110000",
                'UNITS' => "DATE",
                'OBSERVATION_RESULT_STATUS' => "F",
                'OBSERVATION_DATETIME' => date('Ymdhis', strtotime($pat->date_enrolled)),
                'ABNORMAL_FLAGS' => "N"
            ),
            array(
                'SET_ID' => "5",
                'OBSERVATION_IDENTIFIER' => "CURRENT_REGIMEN",
                'CODING_SYSTEM' => "NASCOP_CODES",
                'VALUE_TYPE' => "CE",
                'OBSERVATION_VALUE' => $pat->current_regimen,
                'UNITS' => "",
                'OBSERVATION_RESULT_STATUS' => "F",
                'OBSERVATION_DATETIME' => date('Ymdhis', strtotime($pat->date_enrolled)),
                'ABNORMAL_FLAGS' => "N"
            ),
            array(
                'SET_ID' => "6",
                'OBSERVATION_IDENTIFIER' => "IS_SMOKER",
                'CODING_SYSTEM' => "",
                'VALUE_TYPE' => "CE",
                'OBSERVATION_VALUE' => ($pat->smoke == '0') ? 'NO' : 'Yes',
                'UNITS' => "YES/NO",
                'OBSERVATION_RESULT_STATUS' => "F",
                'OBSERVATION_DATETIME' => date('Ymdhis', strtotime($pat->date_enrolled)),
                'ABNORMAL_FLAGS' => "N"
            ),
            array(
                'SET_ID' => "6",
                'OBSERVATION_IDENTIFIER' => "IS_ALCOHOLIC",
                'CODING_SYSTEM' => "",
                'VALUE_TYPE' => "CE",
                'OBSERVATION_VALUE' => ($pat->alcohol == '0') ? 'NO' : 'Yes',
                'UNITS' => "YES/NO",
                'OBSERVATION_RESULT_STATUS' => "F",
                'OBSERVATION_DATETIME' => date('Ymdhis', strtotime($pat->date_enrolled)),
                'ABNORMAL_FLAGS' => "N"
            )
        );

        //echo "<pre>";
        //echo(json_encode($observations, JSON_PRETTY_PRINT));
        $this->writeLog('PATIENT ' . $message_type, json_encode($observations));
        $this->tcpILRequest(null, json_encode($observations));
    }

    public function getDispensing($order_id) {
        $pats = $this->api_model->getDispensing($order_id);

        $message_type = 'RDS^O13';

        $dispense['MESSAGE_HEADER'] = [
            'SENDING_APPLICATION' => "ADT",
            'SENDING_FACILITY' => empty($pats[0]->facility_code) ? '' : $pats[0]->facility_code,
            'RECEIVING_APPLICATION' => "IL",
            'RECEIVING_FACILITY' => empty($pats[0]->facility_code) ? '' : $pats[0]->facility_code,
            'MESSAGE_DATETIME' => date('Ymdhis'),
            'SECURITY' => "",
            'MESSAGE_TYPE' => $message_type,
            'PROCESSING_ID' => "P"
        ];
        $dispense['PATIENT_IDENTIFICATION'] = [
            'EXTERNAL_PATIENT_ID' => ['ID' => empty($pats[0]->external_id) ? '' : $pats[0]->external_id, 'IDENTIFIER_TYPE' => "GODS_NUMBER", 'ASSIGNING_AUTHORITY' => "MPI"],
            'INTERNAL_PATIENT_ID' => [
                ['ID' => empty($pats[0]->patient_number_ccc) ? '' : $pats[0]->patient_number_ccc, 'IDENTIFIER_TYPE' => "CCC_NUMBER", 'ASSIGNING_AUTHORITY' => "CCC"]
            ],
            'PATIENT_NAME' => ['FIRST_NAME' => empty($pats[0]->first_name) ? '' : $pats[0]->first_name, 'MIDDLE_NAME' => empty($pats[0]->other_name) ? '' : $pats[0]->other_name, 'LAST_NAME' => empty($pats[0]->last_name) ? '' : $pats[0]->last_name]
        ];
        $dispense['COMMON_ORDER_DETAILS'] = [
            'ORDER_CONTROL' => "NW",
            'PLACER_ORDER_NUMBER' => ['NUMBER' => empty($pats[0]->order_number) ? '' : $pats[0]->order_number, 'ENTITY' => "IQCARE"],
            'FILLER_ORDER_NUMBER' => ['NUMBER' => empty($pats[0]->visit_id) ? '' : $pats[0]->visit_id, 'ENTITY' => "ADT"],
            'ORDER_STATUS' => "NW",
            'ORDERING_PHYSICIAN' => ['FIRST_NAME' => empty($pats[0]->order_physician) ? '' : $pats[0]->order_physician, 'MIDDLE_NAME' => "", 'LAST_NAME' => "", 'PREFIX' => "DR"],
            'TRANSACTION_DATETIME' => empty($pats[0]->timecreated) ? '' : $pats[0]->timecreated,
            'NOTES' => empty($pats[0]->notes) ? '' : $pats[0]->notes
        ];

        /* Loop Drugs */
        foreach ($pats as $key => $pat) {
            $dispense['PHARMACY_ENCODED_ORDER'][$key] = [
                'PRESCRIPTION_NUMBER' => empty($pat->prescription_number) ? '' : $pat->prescription_number,
                'DRUG_NAME' => empty($pat->drug_name) ? '' : $pat->drug_name,
                'CODING_SYSTEM' => "NASCOP_CODES",
                'STRENGTH' => empty($pat->drug_strength) ? '' : $pat->drug_strength,
                'DOSAGE' => empty($pat->dosage) ? '' : $pat->dosage,
                'FREQUENCY' => empty($pat->frequency) ? '' : $pat->frequency,
                'DURATION' => empty($pat->disp_duration) ? '' : $pat->disp_duration,
                'QUANTITY_PRESCRIBED' => empty($pat->quantity_prescribed) ? '' : $pat->quantity_prescribed,
                'PRESCRIPTION_NOTES' => empty($pat->prescription_notes) ? '' : $pat->prescription_notes
            ];
        }
        $sql = "SELECT *, dpd.strength as drug_strength, pv.id visit_id, DATE_FORMAT(timecreated, '%Y%m%d%h%i%s') timecreated, pv.duration disp_duration, TRIM(d.drug) drugcode, pv.quantity disp_quantity, pv.dose disp_dose, dpd.prescription_number
				FROM patient_visit pv 
				INNER JOIN drug_prescription_details_visit dpdv ON dpdv.visit_id = pv.id
				INNER JOIN drug_prescription_details dpd ON dpd.id = dpdv.drug_prescription_details_id
				INNER JOIN drug_prescription dp ON dp.id = dpd.drug_prescriptionid AND pv.patient_id = dp.patient
				INNER JOIN patient p ON p.patient_number_ccc = pv.patient_id
				INNER JOIN drugcode d ON d.id = pv.drug_id
				WHERE dp.id = '$order_id' group by pv.drug_id ";
        $res = DB::select($sql);

        foreach ($res as $pat) {

            $dispense['PHARMACY_DISPENSE'][] = [
                'PRESCRIPTION_NUMBER' => empty($pat->prescription_number) ? '' : $pat->prescription_number,
                'DRUG_NAME' => empty($pat->drug_name) ? '' : $pat->drug_name,
                'CODING_SYSTEM' => "NASCOP_CODES",
                'ACTUAL_DRUGS' => empty($pat->drugcode) ? '' : $pat->drugcode,
                'STRENGTH' => empty($pat->drug_strength) ? '' : $pat->drug_strength,
                'DOSAGE' => empty($pat->disp_dose) ? '' : $pat->disp_dose,
                'FREQUENCY' => empty($pat->frequency) ? '' : $pat->frequency,
                'DURATION' => empty($pat->disp_duration) ? '' : $pat->disp_duration,
                'QUANTITY_DISPENSED' => empty($pat->disp_quantity) ? '' : $pat->disp_quantity,
                'DISPENSING_NOTES' => empty($pat->comment) ? '' : $pat->comment
            ];
        }

        $this->writeLog('PHARMACY DISPENSE RDS^O13 ', json_encode($dispense));
        $this->tcpILRequest(null, json_encode($dispense));
    }

    function postILRequest($request) {
        // echo $request;
        $this->init_api_values();

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->il_ip . ':' . $this->il_port);

        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => $request
        ));


        $json_data = curl_exec($ch);
        if (empty($json_data)) {
            $message = "cURL Error: " . curl_error($ch) . "<br/>";
        } else {
            // message sent successfully
        }
    }

    function tcpILRequest($request_type, $request) {
        $this->init_api_values();
        $client = new Client();
        $dataoff = [
            'datetime' => date('Y-m-d H:i:s'),
            'payload' => $request,
            'attempts' => 0
        ];

        $host = $this->il_ip;

        $test_client = new Client(['verify' => false]);
        try {
            $response = $test_client->get($host);
            $result = $response->getStatusCode();
        } catch (\Exception $e) {
            $result = 0;
        }

        // if ($result == 200) {
        try {

            $response = $client->post($this->il_ip, [
                'debug' => FALSE,
                'body' => $request,
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);

            $m_body = json_decode($response->getBody());
            if ($m_body->msg == 'successfully received by the Interoperability Layer (IL)') {
                $dataon = [
                    'datetime' => date('Y-m-d H:i:s'),
                    'payload' => $request,
                    'il_response' => $m_body->msg
                ];
                $this->db->table('il_processed_jobs')->insert($dataon);
            } else {

                $this->db->table('il_jobs')->insert($dataoff);
            }
        } catch (Exception $e) {
            // } else {
            $this->db->table('il_jobs')->insert($dataoff);
        }
    }

    function writeLog($logtype, $msg) {

        $path = WRITEPATH . 'DATA-api.log';
        $fp = fopen($path, (file_exists($path)) ? 'a' : 'w');

        fwrite($fp, date('Y-m-d H:i:s') . ' ' . $logtype . ' : ' . $msg . "\r\n");

        fclose($fp);
    }

    public function init_api_values() {
        //  $CI = &get_instance();
        // $CI->load->database();

        $sql = "SELECT * FROM api_config";
        $query = $this->db->query($sql);
        $api_config = $query->getResultArray();

        $conf = array();
        foreach ($api_config as $ob) {
            $conf[$ob['config']] = $ob['value'];
        }

        $this->api = ($conf['api_status'] == 'on') ? TRUE : FALSE;
        $this->patient_module = ($conf['api_patients_module'] == 'on') ? TRUE : FALSE;
        $this->dispense_module = ($conf['api_dispense_module'] == 'on') ? TRUE : FALSE;
        $this->appointment_module = ($conf['api_appointments_module'] == 'on') ? TRUE : FALSE;
        $this->adt_url = (strlen($conf['api_adt_url']) > 2) ? $conf['api_adt_url'] : FALSE;
        $this->adt_port = (strlen($conf['api_adt_port']) > 1) ? $conf['api_adt_port'] : FALSE;
        $this->il_ip = (strlen($conf['api_il_ip']) > 1) ? $conf['api_il_ip'] : FALSE;
        $this->il_port = (strlen($conf['api_il_port']) > 1) ? $conf['api_il_port'] : FALSE;
        $this->logging = $conf['api_logging'] == 'on' ? TRUE : FALSE;

        return $api_config;
    }

    public function parseCCC($ccc, $mfl_code = false) {

        /*
          Strip CCC of any apprearance of mfl code & special characters and return padded ccc number
         */
        $ccc = str_replace($mfl_code, "", $ccc);
        $ccc = str_replace(" ", "", $ccc);
        $ccc = str_replace("-", "", $ccc);

        return $ccc;
    }

    public function constructCCC($ccc, $mfl_code = false, $hyphen = false) {
        $hyphen = ($hyphen) ? '-' : '';

        for ($i = strlen($ccc); $i < 5; $i++) {
            $ccc = '0' . $ccc;
        }
        for ($i = strlen($mfl_code); $i < 5; $i++) {
            $ccc = '0' . $mfl_code;
        }
        // return ($mfl_code.$hyphen.$ccc);
        return ($ccc);
    }

    public function settings() {
        if ($_POST) {
            if ($this->api_model->saveAPIConfig($_POST)) {
                $data['message'] = 'Settings Saved successfully';
            }
        }

        $data['api_config'] = $this->init_api_values();
        $data['active_menu'] = 8;
        $data['content_view'] = "\Modules\Api\Views\\settings_view";
        $data['title'] = "Dashboard | API Settings";
        $this->template($data);
    }

    //patient list

    public function getPatientList() {
        # code...
        //  $data=[
        // 'name'=>'karanja',
        // 'age'=>'64',
        // 'jjjgg'=>'yfgdhh'
        //  ];

        $sql = "SELECT `id`, `medical_record_number`, `patient_number_ccc`, `first_name`, `last_name`, `other_name`, `dob`, `pob`, `gender`,`pregnant` FROM patient";
        $query = $this->db->query($sql);
        $api_config = $query->getResultArray();

        $data = $api_config;

        return json_encode($data);
    }

    public function searchPatient($ccc) {

        $query_str = "SELECT p.*,ps.name as patient_status,pso.name as patient_source ,g.name as patient_gender "
                . "FROM patient p " .
                "left join patient_status ps on p.current_status = ps.id " .
                "left join patient_source pso on p.source = pso.id " .
                "left join gender g on g.id = p.gender " .
                "WHERE p.patient_number_ccc = '" . $ccc . "' ";
        $query = $this->db->query($query_str);
        $api_config = $query->getResultArray();
        if (!empty($api_config)) {
            return (json_encode($api_config, JSON_PRETTY_PRINT));
        } else {
            return (json_encode(['code' => $this->error, 'message' => 'Patient not Found'], JSON_PRETTY_PRINT));
        }
    }

    public function deletePatient($ccc) {

        $query_str = "DELETE  FROM patient WHERE patient_number_ccc = '$ccc'";
        $query = $this->db->query($query_str);
        if ($query) {
            return (json_encode(['code' => $this->success, 'message' => 'Patient deleted successfully'], JSON_PRETTY_PRINT));
        } else {
            return (json_encode(['code' => $this->error, 'message' => 'Error occurred while deleting patient'], JSON_PRETTY_PRINT));
        }
    }

    public function searchGender($pgender) {
        # code...
        //  $data=[
        // 'name'=>'karanja',
        // 'age'=>'64',
        // 'jjjgg'=>'yfgdhh',
        // 'ccc'=>$ccc
        //  ]
        $sql = "SELECT `id`, `medical_record_number`, `patient_number_ccc`, `first_name`, `last_name`, `other_name`, `dob`, `pob`, `gender`,`pregnant` FROM patient where gender= '$pgender' ";
        $query = $this->db->query($sql);
        $api_config = $query->getResultArray();

        $data = $api_config;

        //return json_encode($data);
        return (json_encode($data, JSON_PRETTY_PRINT));
    }

    public function getRegimenId($code) {
        $result = null;
        $regimen = Regimen::where('regimen_code', $code)->first();
        if (!empty($regimen)) {
            $result = $regimen->id;
        }

        return $result;
    }

    public function getSource($source) {
        $result = null;
        $patient_source = PatientSource::where('name', $source)->first();
        if (!empty($patient_source)) {
            $result = $patient_source->id;
        } else {
            if (PatientSource::where('name', 'CCC')->exists()) {
                $patient_source = PatientSource::where('name', 'CCC')->first();
                $result = $patient_source->id;
            }
        }
        return $result;
    }

    public function partnerStatus($source) {
        $partnerstatus = [
            'No Partner' => 0,
            'HIV Positive' => 1,
            'Unknown' => 3,
            'HIV Negative' => 4
        ];
        return $partnerstatus[$source];
    }

    public function template($data) {
        error_reporting(1);
        $data['show_menu'] = 0;
        $data['show_sidemenu'] = 0;
        $template = new Template();
        $template->index($data);
    }

}
