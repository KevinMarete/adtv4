<?php

namespace Modules\ADT\Controllers;

use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;
use \Modules\ADT\Models\CCC_store_service_point;
use Illuminate\Database\Capsule\Manager as DB;

class Report_management extends \App\Controllers\BaseController
{

  var $db;

  function __construct()
  {
    $this->db = \Config\Database::connect();
  }

  public function index()
  {
    $ccc_stores = CCC_store_service_point::getAllActive();
    session()->set('ccc_store', $ccc_stores);
    $this->listing();
  }

  public function listing($data = [])
  {
    $data['content_view'] = "\Modules\ADT\Views\\report_v";
    $this->base_params($data);
  }

  public function base_params($data)
  {
    $data['reports'] = true;
    $data['title'] = "webADT | Reports";
    $data['banner_text'] = "Facility Reports";
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function cumulative_patients($from = "", $type = '1')
  {
    //Variables
    $facility_code = session()->get("facility");
    $from = date('Y-m-d', strtotime($from));
    $status_totals = array();
    $row_string = "";
    $total_adult_male_art = 0;
    $total_adult_male_pep = 0;
    $total_adult_male_oi = 0;
    $total_adult_male_prep = 0;
    $total_adult_female_art = 0;
    $total_adult_female_pep = 0;
    $total_adult_female_pmtct = 0;
    $total_adult_female_oi = 0;
    $total_adult_female_prep = 0;
    $total_child_male_art = 0;
    $total_child_male_pep = 0;
    $total_child_male_pmtct = 0;
    $total_child_male_oi = 0;
    $total_child_male_prep = 0;
    $total_child_female_art = 0;
    $total_child_female_pep = 0;
    $total_child_female_pmtct = 0;
    $total_child_female_oi = 0;
    $total_child_female_prep = 0;

    //Get Total Count of all patients
    $sql = "select count(p.id) as total,p.current_status,ps.name 
      from patient p
    left join patient_status ps on ps.id=p.current_status 
    left join regimen_service_type rst on p.service=rst.id
    left join gender g  on p.gender=g.id 
      where(p.date_enrolled <= '$from') 
    and facility_code='$facility_code'";

    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    $patient_total = $results[0]['total'];

    $row_string = "<table border='1' cellpadding='5' id='tblcumulpatients' class='dataTables'>
                    <thead>
                    <tr>
                    <th style='width:15%;'>Current Status</th>
                    <th>Total</th><th>Total</th>
                    <th>Adult</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                    <th>Children</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                    </tr>
                    <tr>
                    <th>-</th>
                    <th>No.</th>
                    <th>%</th>
                    <th>Male</th><th></th><th></th><th></th>
                    <th>Female</th><th></th><th></th><th></th><th></th>
                    <th>Male</th><th></th><th></th><th></th><th></th>
                    <th>Female</th><th></th><th></th><th></th><th></th>
                    </tr>
                    <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>ART</th>
                    <th>PEP</th>
                    <th>OI</th>
                    <th>PREP</th>
                    <th>ART</th>
                    <th>PEP</th>
                    <th>PMTCT</th>
                    <th>OI</th>
                    <th>PREP</th>
                    <th>ART</th>
                    <th>PEP</th>
                    <th>PMTCT</th>
                    <th>OI</th>
                    <th>PREP</th>
                    <th>ART</th>
                    <th>PEP</th>
                    <th>PMTCT</th>
                    <th>OI</th>
                    <th>PREP</th>
                    </tr></thead><tbody>";

    //Get Totals for each Status
    //$sql = "select count(p.id) as total,current_status,ps.name from patient p,patient_status ps where(date_enrolled <= '$from' or date_enrolled='') and facility_code='$facility_code' and ps.id = current_status and current_status!='' and service!='' and gender !='' group by p.current_status";
    $sql = "select count(p.id) as total,p.current_status,ps.name from patient p,patient_status ps,regimen_service_type rst,gender g where(p.date_enrolled <= '$from' or p.date_enrolled='') and ps.id=p.current_status and p.service=rst.id and p.gender=g.id and facility_code='$facility_code' and p.active='1' group by p.current_status";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    if ($results) {

      foreach ($results as $result) {
        $status_totals[$result['current_status']] = $result['total'];
        $current_status = $result['current_status'];
        $status_name = $result['name'];
        $patient_percentage = number_format(($status_totals[$current_status] / $patient_total) * 100, 1);
        $row_string .= "<tr><td>$status_name</td><td>$status_totals[$current_status]</td><td>$patient_percentage</td>";
        //SQL for Adult Male Status
        $service_list = array('ART', 'PEP', 'OI Only', 'PREP');
        $sql = "SELECT count(*) as total_adult_male, ps.Name,ps.id as current_status,r.name AS Service FROM patient p,patient_status ps,regimen_service_type r WHERE  p.current_status=ps.id AND p.service=r.id AND p.current_status='$current_status' AND p.facility_code='$facility_code' AND p.gender=1 AND FLOOR(datediff('$from',p.dob)/365)>15 and p.active='1' GROUP BY service";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        $i = 0;
        $j = 0;
        if ($results) {
          while ($j < sizeof($service_list)) {
            $patient_current_total = @$results[$i]['total_adult_male'];
            $service = @$results[$i]['Service'];
            if ($service == @$service_list[$j]) {
              $row_string .= "<td>$patient_current_total</td>";
              if ($service == "ART") {
                $total_adult_male_art += $patient_current_total;
              } else if ($service == "PEP") {
                $total_adult_male_pep += $patient_current_total;
              } else if ($service == "OI Only") {
                $total_adult_male_oi += $patient_current_total;
              } else if (strtoupper($service) == "PREP") {
                $total_adult_male_prep += $patient_current_total;
              }
              $i++;
              $j++;
            } else {
              $row_string .= "<td>-</td>
                          ";
              $j++;
            }
          }
        } else {
          $row_string .= "<td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  ";
        }
        //SQL for Adult Female Status
        $service_list = array('ART', 'PEP', 'PMTCT', 'OI Only', 'PREP');
        $sql = "SELECT count(*) as total_adult_female, ps.Name,ps.id as current_status,r.name AS Service FROM patient p,patient_status ps,regimen_service_type r WHERE  p.current_status=ps.id AND p.service=r.id AND p.current_status='$current_status' AND p.facility_code='$facility_code' AND p.gender=2  AND FLOOR(datediff('$from',p.dob)/365)>15 and p.active='1' GROUP BY service";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        $i = 0;
        $j = 0;
        if ($results) {
          while ($j < sizeof($service_list)) {
            $patient_current_total = @$results[$i]['total_adult_female'];
            $service = @$results[$i]['Service'];
            if ($service == @$service_list[$j]) {
              $row_string .= "<td>$patient_current_total</td>";
              if ($service == "ART") {
                $total_adult_female_art += $patient_current_total;
              } else if ($service == "PEP") {
                $total_adult_female_pep += $patient_current_total;
              } else if ($service == "PMTCT") {
                $total_adult_female_pmtct += $patient_current_total;
              } else if ($service == "OI Only") {
                $total_adult_female_oi += $patient_current_total;
              } else if ($service == "PREP") {
                $total_adult_female_prep += $patient_current_total;
              }
              $i++;
              $j++;
            } else {
              $row_string .= "<td>-</td>
                          ";
              $j++;
            }
          }
        } else {
          $row_string .= "<td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  ";
        }
        //SQL for Child Male Status
        $service_list = array('ART', 'PEP', 'PMTCT', 'OI Only', 'PREP');
        $sql = "SELECT count(*) as total_child_male, ps.Name,ps.id as current_status,r.name AS Service FROM patient p,patient_status ps,regimen_service_type r WHERE  p.current_status=ps.id AND p.service=r.id AND p.current_status='$current_status' AND p.facility_code='$facility_code' AND p.gender=1  AND FLOOR(datediff('$from',p.dob)/365)<=15 and p.active='1' GROUP BY service";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        $i = 0;
        $j = 0;
        if ($results) {
          while ($j < sizeof($service_list)) {
            $patient_current_total = @$results[$i]['total_child_male'];
            $service = @$results[$i]['Service'];
            if ($service == @$service_list[$j]) {
              $row_string .= "<td>$patient_current_total</td>";
              if ($service == "ART") {
                $total_child_male_art += $patient_current_total;
              } else if ($service == "PEP") {
                $total_child_male_pep += $patient_current_total;
              } else if ($service == "PMTCT") {
                $total_child_male_pmtct += $patient_current_total;
              } else if ($service == "OI Only") {
                $total_child_male_oi += $patient_current_total;
              } else if ($service == "PREP") {
                $total_child_male_prep += $patient_current_total;
              }
              $i++;
              $j++;
            } else {
              $row_string .= "<td>-</td>
                          ";
              $j++;
            }
          }
        } else {
          $row_string .= "<td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  ";
        }
        //SQL for Child Female Status
        $service_list = array('ART', 'PEP', 'PMTCT', 'OI Only', 'PREP');
        $sql = "SELECT count(*) as total_child_female, ps.Name,ps.id as current_status,r.name AS Service FROM patient p,patient_status ps,regimen_service_type r WHERE  p.current_status=ps.id AND p.service=r.id AND p.current_status='$current_status' AND p.facility_code='$facility_code' AND p.gender=2  AND FLOOR(datediff('$from',p.dob)/365)<=15 and p.active='1' GROUP BY service";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        $i = 0;
        $j = 0;
        if ($results) {
          while ($j < sizeof($service_list)) {
            $patient_current_total = @$results[$i]['total_child_female'];
            $service = @$results[$i]['Service'];
            if ($service == @$service_list[$j]) {
              $row_string .= "<td>$patient_current_total</td>";
              if ($service == "ART") {
                $total_child_female_art += $patient_current_total;
              } else if ($service == "PEP") {
                $total_child_female_pep += $patient_current_total;
              } else if ($service == "PMTCT") {
                $total_child_female_pmtct += $patient_current_total;
              } else if ($service == "OI Only") {
                $total_child_female_oi += $patient_current_total;
              } else if ($service == "PREP") {
                $total_child_female_prep += $patient_current_total;
              }
              $i++;
              $j++;
            } else {
              $row_string .= "<td>-</td>
                          ";
              $j++;
            }
          }
        } else {
          $row_string .= "<td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  ";
        }
        $row_string .= "</tr>";
      }
      $row_string .= "</tbody><tfoot><tr class='tfoot'><td><b>Total:</b></td><td><b>$patient_total</b></td><td><b>100</b></td><td><b>$total_adult_male_art</b></td><td><b>$total_adult_male_pep</b></td><td><b>$total_adult_male_oi</b></td><td><b>$total_adult_male_prep</b></td><td><b>$total_adult_female_art</b></td><td><b>$total_adult_female_pep</b></td><td><b>$total_adult_female_pmtct</b></td><td><b>$total_adult_female_oi</b></td><td><b>$total_adult_female_prep</b></td><td><b>$total_child_male_art</b></td><td><b>$total_child_male_pep</b></td><td><b>$total_child_male_pmtct</b></td><td><b>$total_child_male_oi</b></td><td><b>$total_child_male_prep</b></td><td><b>$total_child_female_art</b></td><td><b>$total_child_female_pep</b></td><td><b>$total_child_female_pmtct</b></td><td><b>$total_child_female_oi</b></td><td><b>$total_child_female_prep</b></td></tr>";
      $row_string .= "</tfoot></table>";
    }
    $data['from'] = date('d-M-Y', strtotime($from));
    $data['dyn_table'] = $row_string;
    $data['title'] = "Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "Cumulative Number of Patients to Date";
    $data['facility_name'] = session()->get('facility_name');
    $data['repo_type'] = $type;
    $data['content_view'] = '\Modules\ADT\Views\reports\\cumulative_patients_v';
    if ($type == 1) {
      echo view('\Modules\ADT\Views\\template', $data);
    } else {
      echo view('\Modules\ADT\Views\reports\\cumulative_patients_v', $data);
    }
  }
}
