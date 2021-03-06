<?php

namespace Modules\ADT\Models;

use App\Models\BaseModel;
use Illuminate\Database\Capsule\Manager as DB;

class Patient_appointment extends BaseModel {

    protected $table = 'patient_appointment';

    /* ( public function setTableDefinition() {
      $this->hasColumn('Patient', 'varchar', 25);
      $this->hasColumn('Appointment', 'varchar', 25);
      $this->hasColumn('Facility', 'varchar', 25);
      $this->hasColumn('Machine_Code', 'varchar', 10);
      }

      public function setUp() {
      $this->setTableName('patient_appointment');
      $this->hasOne('Patient as Patient_Object', array('local' => 'Patient', 'foreign' => 'id'));
      } */

    public static function getAllScheduled($timestamp) {
        $query = DB::table("patient_appointment")->where('appointment',$timestamp)->get();
        return $query;
    }

    public function getAll() {
        $query = Doctrine_Query::create()->select("*")->from("Patient_Appointment");
        $appointments = $query->execute();
        return $appointments;
    }

    public function getTotalAppointments($facility) {
        $query = Doctrine_Query::create()->select("count(*) as Total_Appointments")->from("Patient_Appointment")->where("Facility= '$facility'");
        $total = $query->execute();
        return $total[0]['Total_Appointments'];
    }

    public function getPagedPatientAppointments($offset, $items, $machine_code, $patient_ccc, $facility, $appointment) {
        $query = Doctrine_Query::create()->select("pa.*")->from("Patient_Appointment pa")->leftJoin("Patient_Appointment pa2")->where("pa2.Patient = '$patient_ccc' and pa2.Machine_Code = '$machine_code' and pa2.Appointment = '$appointment' and pa2.Facility='$facility' and pa.Facility='$facility'")->offset($offset)->limit($items);
        $patient_appointments = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $patient_appointments;
    }

    public function getPagedFacilityPatientAppointments($offset, $items, $facility) {
        $query = Doctrine_Query::create()->select("*")->from("Patient_Appointment")->where("Facility='$facility'")->offset($offset)->limit($items);
        $patient_appointments = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $patient_appointments;
    }

    public function getAppointmentDate($patient_ccc) {
        $query = Doctrine_Query::create()->select("*")->from("Patient_Appointment")->where("patient = '$patient_ccc'")->orderBy("appointment DESC")->limit("2");
        $patient_appointments = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
        return $patient_appointments;
    }

}
