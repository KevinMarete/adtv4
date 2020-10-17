<?php

namespace Modules\ADT\Controllers;

use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;
use \Modules\ADT\Models\CCC_store_service_point;
use \Modules\ADT\Models\Regimen_service_type;
use Illuminate\Database\Capsule\Manager as DB;

class Report_management extends \App\Controllers\BaseController
{

  var $db;
  var $table;

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

  public function patient_enrolled($from = "", $to = "", $supported_by = 0)
  {
    //Variables
    $facility_code = session()->get("facility");
    $from = date('Y-m-d', strtotime($from));
    $to = date('Y-m-d', strtotime($to));

    $source_total_percentage = 0;
    $source_totals = array();
    $overall_adult_male = 0;
    $overall_adult_female = 0;
    $overall_child_male = 0;
    $overall_child_female = 0;

    $total = 0;
    $overall_adult_male_art = 0;
    $overall_adult_male_pep = 0;
    $overall_adult_male_oi = 0;
    $overall_adult_male_prep = 0;

    $overall_adult_female_art = 0;
    $overall_adult_female_pep = 0;
    $overall_adult_female_pmtct = 0;
    $overall_adult_female_oi = 0;
    $overall_adult_female_prep = 0;

    $overall_child_male_art = 0;
    $overall_child_male_pep = 0;
    $overall_child_male_pmtct = 0;
    $overall_child_male_oi = 0;
    $overall_child_male_prep = 0;

    $overall_child_female_art = 0;
    $overall_child_female_pep = 0;
    $overall_child_female_pmtct = 0;
    $overall_child_female_oi = 0;
    $overall_child_female_prep = 0;

    if ($supported_by == 0) {
      $supported_query = " ";
    }
    if ($supported_by == 1) {
      $supported_query = "AND supported_by=1 ";
    }
    if ($supported_by == 2) {
      $supported_query = "AND supported_by=2 ";
    }

    $dyn_table = "<table border='1' id='patient_listing'  cellpadding='5' class='dataTables'>";
    $dyn_table .= "<thead>
                      <tr>
                      <th ></th>
                      <th >Total</th><th></th>
                      <th > Adult</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                      <th > Children </th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                      </tr>
                      <tr>
                      <th></th>
                      <th ></th>
                      <th ></th>
                      <th >Male</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                      <th >Female</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                      <th >Male</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                      <th >Female</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                      </tr>
                      <tr>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th >ART</th><th></th>
                      <th >PEP</th><th></th>
                      <th >OI</th><th></th>
                      <th >PREP</th><th></th>
                      <th >ART</th><th></th>
                      <th >PEP</th><th></th>
                      <th >PMTCT</th><th></th>
                      <th >OI</th><th></th>
                      <th >PREP</th><th></th>
                      <th >ART</th><th></th>
                      <th >PEP</th><th></th>
                      <th >PMTCT</th><th></th>
                      <th >OI</th><th></th>
                      <th >PREP</th><th></th>
                      <th >ART</th><th></th>
                      <th >PEP</th><th></th>
                      <th >PMTCT</th><th></th>
                      <th >OI</th><th></th>
                      <th >PREP</th><th></th>
                      </tr>
                      <tr>
                      <th>Source</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      </tr>
                      </thead><tbody>";

    //Get Total of all patients
    $sql = "SELECT count( * ) AS total FROM patient p LEFT JOIN patient_source ps ON ps.id = p.source WHERE date_enrolled BETWEEN '$from' AND '$to' $supported_query AND facility_code = '$facility_code' AND source !='' AND p.active='1'";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    $total = $results[0]['total'];

    //Get Totals for each Source
    $sql = "SELECT count(*) AS total,p.source,ps.name 
              FROM patient p LEFT JOIN patient_source ps ON ps.id = p.source 
              WHERE date_enrolled BETWEEN '$from' AND '$to' $supported_query AND facility_code = '$facility_code' AND source !=''  AND p.active='1' GROUP BY p.source";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    if ($results) {
      foreach ($results as $result) {
        $source_totals[$result['source']] = $result['total'];
        $source = $result['source'];
        $source_name = strtoupper($result['name']);
        $source_code = $result['source'];
        $source_total = $result['total'];
        $source_total_percentage = number_format(($source_total / $total) * 100, 1);
        $dyn_table .= "<tr><td><b>$source_name</b></td><td>$source_total</td><td>$source_total_percentage</td>";
        //SQL for Adult Male Source
        $sql = "SELECT count(*) AS total_adult_male,p.source,ps.name,p.service,rst.name as service_name FROM patient p LEFT JOIN patient_source ps ON ps.id= p.source LEFT JOIN regimen_service_type rst ON rst.id = p.service  WHERE date_enrolled BETWEEN '$from' AND '$to' $supported_query AND facility_code = '$facility_code' AND source !='' AND p.gender=1 AND FLOOR(datediff('$from',p.dob)/365)>15 AND  p.source='$source_code' GROUP BY p.source,p.service";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        $total_adult_male_art = "-";
        $total_adult_male_pep = "-";
        $total_adult_male_oi = "-";
        $total_adult_male_prep = "-";

        $total_adult_male_art_percentage = "-";
        $total_adult_male_pep_percentage = "-";
        $total_adult_male_oi_percentage = "-";
        $total_adult_male_prep_percentage = "-";

        if ($results) {
          foreach ($results as $result) {
            $total_adult_male = $result['total_adult_male'];
            $overall_adult_male += $total_adult_male;
            $service_name = $result['service_name'];
            if ($service_name == "ART") {
              $overall_adult_male_art += $total_adult_male;
              $total_adult_male_art = number_format($total_adult_male);
              $total_adult_male_art_percentage = number_format(($total_adult_male / $source_total) * 100, 1);
            } else if ($service_name == "PEP") {
              $overall_adult_male_pep += $total_adult_male;
              $total_adult_male_pep = number_format($total_adult_male);
              $total_adult_male_pep_percentage = number_format(($total_adult_male_pep / $source_total) * 100, 1);
            } else if ($service_name == "OI Only") {
              $overall_adult_male_oi += $total_adult_male;
              $total_adult_male_oi = number_format($total_adult_male);
              $total_adult_male_oi_percentage = number_format(($total_adult_male_oi / $source_total) * 100, 1);
            } else if (strtoupper($service_name) == "PREP") {
              $overall_adult_male_prep += $total_adult_male;
              $total_adult_male_prep = number_format($total_adult_male);
              $total_adult_male_prep_percentage = number_format(($total_adult_male_prep / $source_total) * 100, 1);
            }
          }
          $dyn_table .= "<td>$total_adult_male_art</td>
                  <td>$total_adult_male_art_percentage</td>
                  <td>$total_adult_male_pep</td>
                  <td>$total_adult_male_pep_percentage</td>
                  <td>$total_adult_male_oi</td>
                  <td>$total_adult_male_oi_percentage</td>
                  <td>$total_adult_male_prep</td>
                  <td>$total_adult_male_prep_percentage</td>";
        } else {
          $dyn_table .= "<td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>";
        }
        //SQL for Adult Female Source
        $sql = "SELECT count(*) AS total_adult_female,p.source,ps.name,p.service,rst.name as service_name 
    FROM patient p LEFT JOIN patient_source ps ON ps.id = p.source LEFT JOIN regimen_service_type rst ON rst.id = p.service 
    WHERE date_enrolled BETWEEN '$from' AND '$to' $supported_query AND facility_code = '$facility_code' AND source !='' AND p.gender=2 AND FLOOR(datediff('$from',p.dob)/365)>15 AND  p.source='$source_code' AND p.active=1 GROUP BY p.source,p.service";
        //die();
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        $total_adult_female_art = "-";
        $total_adult_female_pep = "-";
        $total_adult_female_pmtct = "-";
        $total_adult_female_oi = "-";
        $total_adult_female_prep = "-";

        $total_adult_female_art_percentage = "-";
        $total_adult_female_pep_percentage = "-";
        $total_adult_female_pmtct_percentage = "-";
        $total_adult_female_oi_percentage = "-";
        $total_adult_female_prep_percentage = "-";

        if ($results) {
          foreach ($results as $result) {
            $total_adult_female = $result['total_adult_female'];
            $overall_adult_female += $total_adult_female;
            $service_name = $result['service_name'];
            if ($service_name == "ART") {
              $overall_adult_female_art += $total_adult_female;
              $total_adult_female_art = number_format($total_adult_female);
              $total_adult_female_art_percentage = number_format(($total_adult_female / $source_total) * 100, 1);
            } else if ($service_name == "PEP") {
              $overall_adult_female_pep += $total_adult_female;
              $total_adult_female_pep = number_format($total_adult_female);
              $total_adult_female_pep_percentage = number_format(($total_adult_female_pep / $source_total) * 100, 1);
            } else if ($service_name == "PMTCT") {
              $overall_adult_female_pmtct += $total_adult_female;
              $total_adult_female_pmtct = number_format($total_adult_female);
              $total_adult_female_pmtct_percentage = number_format(($total_adult_female_pmtct / $source_total) * 100, 1);
            } else if ($service_name == "OI Only") {
              $overall_adult_female_oi += $total_adult_female;
              $total_adult_female_oi = number_format($total_adult_female);
              $total_adult_female_oi_percentage = number_format(($total_adult_female_oi / $source_total) * 100, 1);
            } else if (strtoupper($service_name) == "OI Only") {
              $overall_adult_female_prep += $total_adult_female;
              $total_adult_female_prep = number_format($total_adult_female);
              $total_adult_female_prep_percentage = number_format(($total_adult_female_prep / $source_total) * 100, 1);
            }
          }
          $dyn_table .= "<td>$total_adult_female_art</td>
                  <td>$total_adult_female_art_percentage</td>
                  <td>$total_adult_female_pep</td>
                  <td>$total_adult_female_pep_percentage</td>
                  <td>$total_adult_female_pmtct</td>
                  <td>$total_adult_female_pmtct_percentage</td>
                  <td>$total_adult_female_oi</td>
                  <td>$total_adult_female_oi_percentage</td>
                  <td>$total_adult_female_prep</td>
                  <td>$total_adult_female_prep_percentage</td>";
        } else {
          $dyn_table .= "
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  ";
        }
        //SQL for Child Male Source
        $sql = "SELECT count(*) AS total_child_male,p.source,ps.name,p.service,rst.name as service_name FROM patient p LEFT JOIN patient_source ps ON ps.id = p.source LEFT JOIN regimen_service_type rst ON rst.id = p.service WHERE date_enrolled BETWEEN '$from' AND '$to' $supported_query AND facility_code = '$facility_code' AND source !='' AND p.gender=1 AND FLOOR(datediff('$from',p.dob)/365)<=15 AND  p.source='$source_code' GROUP BY p.source,p.service";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        $total_child_male_art = "-";
        $total_child_male_pep = "-";
        $total_child_male_pmtct = "-";
        $total_child_male_oi = "-";
        $total_child_male_prep = "-";

        $total_child_male_art_percentage = "-";
        $total_child_male_pep_percentage = "-";
        $total_child_male_pmtct_percentage = "-";
        $total_child_male_oi_percentage = "-";
        $total_child_male_prep_percentage = "-";
        if ($results) {
          foreach ($results as $result) {
            $total_child_male = $result['total_child_male'];
            $overall_child_male += $total_child_male;
            $service_name = $result['service_name'];
            if ($service_name == "ART") {
              $overall_child_male_art += $total_child_male;
              $total_child_male_art = number_format($total_child_male);
              $total_child_male_art_percentage = number_format(($total_child_male / $source_total) * 100, 1);
            } else if ($service_name == "PEP") {
              $overall_child_male_pep += $total_child_male;
              $total_child_male_pep = number_format($total_child_male);
              $total_child_male_pep_percentage = number_format(($total_child_male_pep / $source_total) * 100, 1);
            } else if ($service_name == "PMTCT") {
              $overall_child_male_pmtct += $total_child_male;
              $total_child_male_pmtct = number_format($total_child_male);
              $total_child_male_pmtct_percentage = number_format(($total_child_male_pmtct / $source_total) * 100, 1);
            } else if ($service_name == "OI Only") {
              $overall_child_male_oi += $total_child_male;
              $total_child_male_oi = number_format($total_child_male);
              $total_child_male_oi_percentage = number_format(($total_child_male_oi / $source_total) * 100, 1);
            } else if (strtoupper($service_name) == "PREP") {
              $overall_child_male_prep += $total_child_male;
              $total_child_male_prep = number_format($total_child_male);
              $total_child_male_prep_percentage = number_format(($total_child_male_prep / $source_total) * 100, 1);
            }
          }
          $dyn_table .= "<td>$total_child_male_art</td>
                  <td>$total_child_male_art_percentage</td>
                  <td>$total_child_male_pep</td>
                  <td>$total_child_male_pep_percentage</td>
                  <td>$total_child_male_pmtct</td>
                  <td>$total_child_male_pmtct_percentage</td>
                  <td>$total_child_male_oi</td>
                  <td>$total_child_male_oi_percentage</td>
                  <td>$total_child_male_prep</td>
                  <td>$total_child_male_prep_percentage</td>";
        } else {
          $dyn_table .= "<td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  ";
        }
        //SQL for Child Female Source
        $sql = "SELECT count(*) AS total_child_female,p.source,ps.name,p.service,rst.name as service_name FROM patient p LEFT JOIN patient_source ps ON ps.id = p.source LEFT JOIN regimen_service_type rst ON rst.id = p.service WHERE date_enrolled BETWEEN '$from' AND '$to' $supported_query AND facility_code = '$facility_code' AND source !='' AND p.gender=2 AND FLOOR(datediff('$from',p.dob)/365) < 15 AND  p.source='$source_code' GROUP BY p.source,p.service";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        $total_child_female_art = "-";
        $total_child_female_pep = "-";
        $total_child_female_pmtct = "-";
        $total_child_female_oi = "-";
        $total_child_female_prep = "-";

        $total_child_female_art_percentage = "-";
        $total_child_female_pep_percentage = "-";
        $total_child_female_pmtct_percentage = "-";
        $total_child_female_oi_percentage = "-";
        $total_child_female_prep_percentage = "-";
        $overall_child_female = 0;
        $service_name = "";
        $overall_child_male = 0;
        if ($results) {
          foreach ($results as $result) {
            $total_child_female = $result['total_child_female'];
            $overall_child_female += $total_child_female;
            $service_name = $result['service_name'];
            if ($service_name == "ART") {
              $overall_child_female_art += $total_child_female;
              $total_child_female_art = number_format($total_child_female);
              $total_child_female_art_percentage = number_format(($total_child_female / $source_total) * 100, 1);
            } else if ($service_name == "PEP") {
              $overall_child_female_pep += $total_child_female;
              $total_child_female_pep = number_format($total_child_female);
              $total_child_female_pep_percentage = number_format(($total_child_female_pep / $source_total) * 100, 1);
            } else if ($service_name == "PMTCT") {
              $overall_child_female_pmtct += $total_child_female;
              $total_child_female_pmtct = number_format($total_child_female);
              $total_child_female_pmtct_percentage = number_format(($total_child_female_pmtct / $source_total) * 100, 1);
            } else if ($service_name == "OI Only") {
              $overall_child_female_oi += $total_child_female;
              $total_child_female_oi = number_format($total_child_female);
              $total_child_female_oi_percentage = number_format(($total_child_female_oi / $source_total) * 100, 1);
            } else if (strtoupper($service_name) == "PREP") {
              $overall_child_female_prep += $total_child_female;
              $total_child_female_prep = number_format($total_child_female);
              $total_child_female_prep_percentage = number_format(($total_child_female_prep / $source_total) * 100, 1);
            }
          }
          $dyn_table .= "<td>$total_child_female_art</td>
                  <td>$total_child_female_art_percentage</td>
                  <td>$total_child_female_pep</td>
                  <td>$total_child_female_pep_percentage</td>
                  <td>$total_child_female_pmtct</td>
                  <td>$total_child_female_pmtct_percentage</td>
                  <td>$total_child_female_oi</td>
                  <td>$total_child_female_oi_percentage</td>
                  <td>$total_child_female_prep</td>
                  <td>$total_child_female_prep_percentage</td>";
        } else {
          $dyn_table .= "<td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  ";
        }
      }
      $overall_art_male_percent = number_format(($overall_adult_male_art / $total) * 100, 1);
      $overall_pep_male_percent = number_format(($overall_adult_male_pep / $total) * 100, 1);
      $overall_oi_male_percent = number_format(($overall_adult_male_oi / $total) * 100, 1);
      $overall_prep_male_percent = number_format(($overall_adult_male_prep / $total) * 100, 1);

      $overall_art_female_percent = number_format(($overall_adult_female_art / $total) * 100, 1);
      $overall_pep_female_percent = number_format(($overall_adult_female_pep / $total) * 100, 1);
      $overall_pmtct_female_percent = number_format(($overall_adult_female_pmtct / $total) * 100, 1);
      $overall_oi_female_percent = number_format(($overall_adult_female_oi / $total) * 100, 1);
      $overall_prep_female_percent = number_format(($overall_adult_female_prep / $total) * 100, 1);

      $overall_art_childmale_percent = number_format(($overall_child_male_art / $total) * 100, 1);
      $overall_pep_childmale_percent = number_format(($overall_child_male_pep / $total) * 100, 1);
      $overall_pmtct_childmale_percent = number_format(($overall_child_male_pmtct / $total) * 100, 1);
      $overall_oi_childmale_percent = number_format(($overall_child_male_oi / $total) * 100, 1);
      $overall_prep_childmale_percent = number_format(($overall_child_male_prep / $total) * 100, 1);

      $overall_art_childfemale_percent = number_format(($overall_child_female_art / $total) * 100, 1);
      $overall_pep_childfemale_percent = number_format(($overall_child_female_pep / $total) * 100, 1);
      $overall_pmtct_childfemale_percent = number_format(($overall_child_female_pmtct / $total) * 100, 1);
      $overall_oi_childfemale_percent = number_format(($overall_child_female_oi / $total) * 100, 1);
      $overall_prep_childfemale_percent = number_format(($overall_child_female_prep / $total) * 100, 1);

      $dyn_table .= "</tbody><tfoot><tr><td>TOTALS</td><td>$total</td><td>100</td><td>$overall_adult_male_art</td><td>$overall_art_male_percent</td><td>$overall_adult_male_pep</td><td>$overall_pep_male_percent</td><td>$overall_adult_male_oi</td><td>$overall_oi_male_percent</td><td>$overall_adult_male_prep</td><td>$overall_prep_male_percent</td><td>$overall_adult_female_art</td><td>$overall_art_female_percent</td><td>$overall_adult_female_pep</td><td>$overall_pep_female_percent</td><td>$overall_adult_female_pmtct</td><td>$overall_pmtct_female_percent</td><td>$overall_adult_female_oi</td><td>$overall_oi_female_percent</td><td>$overall_adult_female_prep</td><td>$overall_prep_female_percent</td><td>$overall_child_male_art</td><td>$overall_art_childmale_percent</td><td>$overall_child_male_pep</td><td>$overall_pep_childmale_percent</td><td>$overall_child_male_pmtct</td><td>$overall_pmtct_childmale_percent</td><td>$overall_child_male_oi</td><td>$overall_oi_childmale_percent</td><td>$overall_child_male_prep</td><td>$overall_prep_childmale_percent</td><td>$overall_child_female_art</td><td>$overall_art_childfemale_percent</td><td>$overall_child_female_pep</td><td>$overall_pep_childfemale_percent</td><td>$overall_child_female_pmtct</td><td>$overall_pmtct_childfemale_percent</td><td>$overall_child_female_oi</td><td>$overall_oi_childfemale_percent</td><td>$overall_child_female_prep</td><td>$overall_prep_childfemale_percent</td></tr></tfoot></table>";
    } else {
      $dyn_table .= "<tbody></tbody><tfoot>";
    }
    $dyn_table .= "</tfoot></table>";

    $data['dyn_table'] = $dyn_table;
    $data['from'] = date('d-M-Y', strtotime($from));
    $data['to'] = date('d-M-Y', strtotime($to));
    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "Number of Patients Enrolled in Period";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\reports\\no_of_patients_enrolled_v';
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function getStartedonART($from = "", $to = "", $supported_by = 0)
  {
    //Variables
    $patient_total = 0;
    $facility_code = session()->get("facility");
    $supported_query = "and facility_code='$facility_code'";
    $from = date('Y-m-d', strtotime($from));
    $to = date('Y-m-d', strtotime($to));
    $regimen_totals = array();
    $overall_child_male = 0;
    $overall_child_female = 0;
    $overall_adult_male = 0;
    $overall_adult_female = 0;

    $overall_adult_male_art = 0;
    $overall_adult_male_pep = 0;
    $overall_adult_male_oi = 0;

    $overall_adult_female_art = 0;
    $overall_adult_female_pep = 0;
    $overall_adult_female_pmtct = 0;
    $overall_adult_female_oi = 0;

    $overall_child_male_art = 0;
    $overall_child_male_pep = 0;
    $overall_child_male_pmtct = 0;
    $overall_child_male_oi = 0;

    $overall_child_female_art = 0;
    $overall_child_female_pep = 0;
    $overall_child_female_pmtct = 0;
    $overall_child_female_oi = 0;

    if ($supported_by == 1) {
      $supported_query = "and supported_by=1";
    } else if ($supported_by == 2) {
      $supported_query = "and supported_by=2";
    }

    //Get Patient Totals
    $sql = "select count(*) as total 
      from patient p,gender g,regimen_service_type rs,regimen r,patient_status ps 
      where start_regimen_date between '$from' and '$to' and 
      p.gender=g.id and p.service=rs.id and p.start_regimen=r.id 
      and ps.id=p.current_status and ps.name LIKE '%active%'
      and rs.name LIKE '%art%' and p.facility_code='$facility_code'";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    $source_total = $results[0]['total'];
    $total = $source_total;
    $other_total = 0;
    //Get Totals for each regimen
    $sql = "select count(*) as total, r.regimen_desc,r.regimen_code,p.start_regimen from patient p,gender g,regimen_service_type rs,regimen r where start_regimen_date between '$from' and '$to' and p.gender=g.id and p.service=rs.id and p.start_regimen=r.id and rs.name LIKE '%art%' and p.facility_code='$facility_code' group by p.start_regimen ORDER BY r.regimen_code ASC";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    $row_string = "<table border='1'  cellpadding='5' class='dataTables'>
                      <thead>
                      <tr>
                      <th ></th>
                      <th >Total</th><th></th>
                      <th> Adult</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                      <th> Children </th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                      </tr>
                      <tr>
                      <th></th>
                      <th ></th>
                      <th ></th>
                      <th>Male</th><th></th><th></th><th></th><th></th><th></th>
                      <th>Female</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                      <th>Male</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                      <th>Female</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
                      </tr>
                      <tr>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th >ART</th><th></th>
                      <th >PEP</th><th></th>
                      <th >OI</th><th></th>
                      <th >ART</th><th></th>
                      <th >PEP</th><th></th>
                      <th >PMTCT</th><th></th>
                      <th >OI</th><th></th>
                      <th >ART</th><th></th>
                      <th >PEP</th><th></th>
                      <th >PMTCT</th><th></th>
                      <th >OI</th><th></th>
                      <th >ART</th><th></th>
                      <th >PEP</th><th></th>
                      <th >PMTCT</th><th></th>
                      <th >OI</th><th></th>
                      </tr>
                      <tr>
                      <th>Regimen</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      <th>No.</th>
                      <th>%</th>
                      </tr>
                      </thead><tbody>";
    if ($source_total == 0) {
      $source_total = 1;
    }
    if ($results) {
      foreach ($results as $result) {
        $regimen_totals[$result['start_regimen']] = $result['total'];
        $start_regimen = $result['start_regimen'];
        $regimen_name = $result['regimen_desc'];
        $regimen_code = $result['regimen_code'];
        $regimen_total = $result['total'];
        $other_total += $regimen_total;
        $regimen_total_percentage = number_format(($regimen_total / $source_total) * 100, 1);
        $row_string .= "<tr><td><b>$regimen_code</b> | $regimen_name</td><td>$regimen_total</td><td>$regimen_total_percentage</td>";
        //SQL for Adult Male Regimens
        $sql = "select count(*) as total_adult_male, r.regimen_desc,r.regimen_code,p.start_regimen,p.service,rs.name as service_name from patient p,gender g,regimen_service_type rs,regimen r where start_regimen_date between '$from' and '$to' and p.gender=g.id and p.service=rs.id and p.start_regimen=r.id and FLOOR(datediff('$to',p.dob)/365)>15 and p.gender='1' and start_regimen='$start_regimen' and p.service='1' and p.facility_code='$facility_code' group by p.start_regimen,p.service";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        $total_adult_male_art = "-";
        $total_adult_male_pep = "-";
        $total_adult_male_oi = "-";
        $total_adult_male_art_percentage = "-";
        $total_adult_male_pep_percentage = "-";
        $total_adult_male_oi_percentage = "-";

        if ($results) {
          foreach ($results as $result) {
            $total_adult_male = $result['total_adult_male'];
            $overall_adult_male += $total_adult_male;
            $service_name = $result['service_name'];
            if ($service_name == "ART") {
              $overall_adult_male_art += $total_adult_male;
              $total_adult_male_art = number_format($total_adult_male);
              $total_adult_male_art_percentage = number_format(($total_adult_male / $source_total) * 100, 1);
            } else if ($service_name == "PEP") {
              $oaverall_adult_male_pep += $total_adult_male;
              $total_adult_male_pep = number_format($total_adult_male);
              $total_adult_male_pep_percentage = number_format(($total_adult_male_pep / $source_total) * 100, 1);
            } else if ($service_name == "OI Only") {
              $overall_adult_male_oi += $total_adult_male;
              $total_adult_male_oi = number_format($total_adult_male);
              $total_adult_male_oi_percentage = number_format(($total_adult_male_oi / $source_total) * 100, 1);
            }
          }
          if ($result['start_regimen'] != null) {
            $row_string .= "<td>$total_adult_male_art</td><td>$total_adult_male_art_percentage</td><td>$total_adult_male_pep</td><td>$total_adult_male_pep_percentage</td><td>$total_adult_male_oi</td><td>$total_adult_male_oi_percentage</td>";
          }
        } else {
          $row_string .= "<td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  ";
        }

        //SQL for Adult Female Regimens
        $sql = "select count(*) as total_adult_female, r.regimen_desc,r.regimen_code,p.start_regimen,p.service,rs.name as service_name from patient p,gender g,regimen_service_type rs,regimen r where start_regimen_date between '$from' and '$to' and p.gender=g.id and p.service=rs.id and p.start_regimen=r.id and FLOOR(datediff('$to',p.dob)/365)>15 and p.gender='2' and p.service='1' and start_regimen='$start_regimen' and p.facility_code='$facility_code' group by p.start_regimen,p.service";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        $total_adult_female_art = "-";
        $total_adult_female_pep = "-";
        $total_adult_female_pmtct = "-";
        $total_adult_female_oi = "-";
        $total_adult_female_art_percentage = "-";
        $total_adult_female_pep_percentage = "-";
        $total_adult_female_pmtct_percentage = "-";
        $total_adult_female_oi_percentage = "-";

        if ($results) {
          foreach ($results as $result) {
            $total_adult_female = $result['total_adult_female'];
            $overall_adult_female += $total_adult_female;
            $service_name = $result['service_name'];
            if ($service_name == "ART") {
              $overall_adult_female_art += $total_adult_female;
              $total_adult_female_art = number_format($total_adult_female);
              $total_adult_female_art_percentage = number_format(($total_adult_female / $source_total) * 100, 1);
            } else if ($service_name == "PEP") {
              $overall_adult_female_pep += $total_adult_female;
              $total_adult_female_pep = number_format($total_adult_female);
              $total_adult_female_pep_percentage = number_format(($total_adult_female_pep / $source_total) * 100, 1);
            } else if ($service_name == "PMTCT") {
              $overall_adult_female_pmtct += $total_adult_female;
              $total_adult_female_pmtct = number_format($total_adult_female);
              $total_adult_female_pmtct_percentage = number_format(($total_adult_female_pmtct / $source_total) * 100, 1);
            } else if ($service_name == "OI Only") {
              $overall_adult_female_oi += $total_adult_female;
              $total_adult_female_oi = number_format($total_adult_female);
              $total_adult_female_oi_percentage = number_format(($total_adult_female_oi / $source_total) * 100, 1);
            }
          }
          if ($result['start_regimen'] != null) {
            $row_string .= "<td>$total_adult_female_art</td><td>$total_adult_female_art_percentage</td><td>$total_adult_female_pep</td><td>$total_adult_female_pep_percentage</td><td>$total_adult_female_pmtct</td><td>$total_adult_female_pmtct_percentage</td><td>$total_adult_female_oi</td><td>$total_adult_female_oi_percentage</td>";
          }
        } else {
          $row_string .= "<td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  ";
        }
        //SQL for Child Male Regimens
        $sql = "select count(*) as total_child_male, r.regimen_desc,r.regimen_code,p.start_regimen,p.service,rs.name as service_name from patient p,gender g,regimen_service_type rs,regimen r where start_regimen_date between '$from' and '$to' and p.gender=g.id and p.service=rs.id and p.start_regimen=r.id and FLOOR(datediff('$to',p.dob)/365)<=15 and p.gender='1' and p.service='1' and start_regimen='$start_regimen' and p.facility_code='$facility_code' group by p.start_regimen,p.service";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        $total_child_male_art = "-";
        $total_child_male_pep = "-";
        $total_child_male_pmtct = "-";
        $total_child_male_oi = "-";
        $total_child_male_art_percentage = "-";
        $total_child_male_pep_percentage = "-";
        $total_child_male_pmtct_percentage = "-";
        $total_child_male_oi_percentage = "-";
        if ($results) {
          foreach ($results as $result) {
            $total_child_male = $result['total_child_male'];
            $service_name = $result['service_name'];
            $overall_child_male += $total_child_male;
            if ($service_name == "ART") {
              $overall_child_male_art += $total_child_male;
              $total_child_male_art = number_format($total_child_male);
              $total_child_male_art_percentage = number_format(($total_child_male / $source_total) * 100, 1);
            } else if ($service_name == "PEP") {
              $overall_child_male_pep += $total_child_male;
              $total_child_male_pep = number_format($total_child_male);
              $total_child_male_pep_percentage = number_format(($total_child_male_pep / $source_total) * 100, 1);
            } else if ($service_name == "PMTCT") {
              $overall_child_male_pmtct += $total_child_male;
              $total_child_male_pmtct = number_format($total_child_male);
              $total_child_male_pmtct_percentage = number_format(($total_child_male_pmtct / $source_total) * 100, 1);
            } else if ($service_name == "OI Only") {
              $overall_child_male_oi += $total_child_male;
              $total_child_male_oi = number_format($total_child_male);
              $total_child_male_oi_percentage = number_format(($total_child_male_oi / $source_total) * 100, 1);
            }
          }
          if ($result['start_regimen'] != null) {
            $row_string .= "<td>$total_child_male_art</td><td>$total_child_male_art_percentage</td><td>$total_child_male_pep</td><td>$total_child_male_pep_percentage</td><td>$total_child_male_pmtct</td><td>$total_child_male_pmtct_percentage</td><td>$total_child_male_oi</td><td>$total_child_male_oi_percentage</td>";
          }
        } else {
          $row_string .= "<td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  ";
        }
        //SQL for Child Female Regimens
        $sql = "select count(*) as total_child_female, r.regimen_desc,r.regimen_code,p.start_regimen,p.service,rs.name as service_name from patient p,gender g,regimen_service_type rs,regimen r where start_regimen_date between '$from' and '$to' and p.gender=g.id and p.service=rs.id and p.start_regimen=r.id and FLOOR(datediff('$to',p.dob)/365)<=15 and p.gender='2' and p.service='1' and start_regimen='$start_regimen' and p.facility_code='$facility_code' group by p.start_regimen,p.service";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        $total_child_female_art = "-";
        $total_child_female_pep = "-";
        $total_child_female_pmtct = "-";
        $total_child_female_oi = "-";
        $total_child_female_art_percentage = "-";
        $total_child_female_pep_percentage = "-";
        $total_child_female_pmtct_percentage = "-";
        $total_child_female_oi_percentage = "-";
        if ($results) {
          foreach ($results as $result) {
            $total_child_female = $result['total_child_female'];
            $overall_child_female += $total_child_female;
            if ($service_name == "ART") {
              $overall_child_female_art += $total_child_female;
              $total_child_female_art = number_format($total_child_female);
              $total_child_female_art_percentage = number_format(($total_child_female / $source_total) * 100, 1);
            } else if ($service_name == "PEP") {
              $overall_child_female_pep += $total_child_female;
              $total_child_female_pep = number_format($total_child_female);
              $total_child_female_pep_percentage = number_format(($total_child_female_pep / $source_total) * 100, 1);
            } else if ($service_name == "PMTCT") {
              $overall_child_female_pmtct += $total_child_female;
              $total_child_female_pmtct = number_format($total_child_female);
              $total_child_female_pmtct_percentage = number_format(($total_child_female_pmtct / $source_total) * 100, 1);
            } else if ($service_name == "OI Only") {
              $overall_child_female_oi += $total_child_female;
              $total_child_female_oi = number_format($total_child_female);
              $total_child_female_oi_percentage = number_format(($total_child_female_oi / $source_total) * 100, 1);
            }
          }
          if ($result['start_regimen'] != null) {
            $row_string .= "<td>$total_child_female_art</td><td>$total_child_female_art_percentage</td><td>$total_child_female_pep</td><td>$total_child_female_pep_percentage</td><td>$total_child_female_pmtct</td><td>$total_child_female_pmtct_percentage</td><td>$total_child_female_oi</td><td>$total_child_female_oi_percentage</td>";
          }
        } else {
          $row_string .= "<td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  <td>-</td>
                  ";
        }
        $row_string .= "</tr>";
      }
      if ($total == 0) {
        $total = 1;
      }
      $overall_art_male_percent = number_format(($overall_adult_male_art / $total) * 100, 1);
      $overall_pep_male_percent = number_format(($overall_adult_male_pep / $total) * 100, 1);
      $overall_oi_male_percent = number_format(($overall_adult_male_oi / $total) * 100, 1);

      $overall_art_female_percent = number_format(($overall_adult_female_art / $total) * 100, 1);
      $overall_pep_female_percent = number_format(($overall_adult_female_pep / $total) * 100, 1);
      $overall_pmtct_female_percent = number_format(($overall_adult_female_pmtct / $total) * 100, 1);
      $overall_oi_female_percent = number_format(($overall_adult_female_oi / $total) * 100, 1);

      $overall_art_childmale_percent = number_format(($overall_child_male_art / $total) * 100, 1);
      $overall_pep_childmale_percent = number_format(($overall_child_male_pep / $total) * 100, 1);
      $overall_oi_childmale_percent = number_format(($overall_child_male_pmtct / $total) * 100, 1);
      $overall_pmtct_childmale_percent = number_format(($overall_child_male_oi / $total) * 100, 1);

      $overall_art_childfemale_percent = number_format(($overall_child_female_art / $total) * 100, 1);
      $overall_pep_childfemale_percent = number_format(($overall_child_female_pep / $total) * 100, 1);
      $overall_pmtct_childfemale_percent = number_format(($overall_child_female_pmtct / $total) * 100, 1);
      $overall_oi_childfemale_percent = number_format(($overall_child_female_oi / $total) * 100, 1);

      $row_string .= "</tbody><tfoot><tr><td>TOTALS</td><td>$other_total</td><td>100</td><td>$overall_adult_male_art</td><td>$overall_art_male_percent</td><td>$overall_adult_male_pep</td><td>$overall_pep_male_percent</td><td>$overall_adult_male_oi</td><td>$overall_oi_male_percent</td><td>$overall_adult_female_art</td><td>$overall_art_female_percent</td><td>$overall_adult_female_pep</td><td>$overall_pep_female_percent</td><td>$overall_adult_female_pmtct</td><td>$overall_pmtct_female_percent</td><td>$overall_adult_female_oi</td><td>$overall_oi_female_percent</td><td>$overall_child_male_art</td><td>$overall_art_childmale_percent</td><td>$overall_child_male_pep</td><td>$overall_pep_childmale_percent</td><td>$overall_child_male_pmtct</td><td>$overall_pmtct_childmale_percent</td><td>$overall_child_male_oi</td><td>$overall_oi_childmale_percent</td><td>$overall_child_female_art</td><td>$overall_art_childfemale_percent</td><td>$overall_child_female_pep</td><td>$overall_pep_childfemale_percent</td><td>$overall_child_female_pmtct</td><td>$overall_pmtct_childfemale_percent</td><td>$overall_child_female_oi</td><td>$overall_oi_childfemale_percent</td></tr></tfoot></table>";
      $row_string .= "</tfoot></table>";
    } else {
      $row_string = "<h4 style='text-align: center'><span >No Data Available</span></h4>";
    }

    $data['from'] = date('d-M-Y', strtotime($from));
    $data['to'] = date('d-M-Y', strtotime($to));
    $data['dyn_table'] = $row_string;
    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "Number of Patients Started on ART in the Period";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\\patients_started_on_art_v';
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function graph_patients_enrolled_in_year($year = "")
  {
    $main_array = array();
    $facility_code = session()->get('facility');
    $months = array(
      '1' => 'Jan',
      '2' => 'Feb',
      '3' => 'Mar',
      '4' => 'Apr',
      '5' => 'May',
      '6' => 'Jun',
      '7' => 'Jul',
      '8' => 'Aug',
      '9' => 'Sep',
      '10' => 'Oct',
      '11' => 'Nov',
      '12' => 'Dec'
    );

    $services_data = Regimen_service_type::getHydratedAll();
    foreach ($services_data as $service) {
      $services[] = $service['name'];
    }

    //Loop through all services
    foreach ($services as $service) {
      $service_array = array();
      $month_data = array();
      $service_array['name'] = $service;
      //Loop through all months
      foreach ($months as $month => $month_name) {
        $sql = "SELECT COUNT(*) AS total
      FROM patient p 
      LEFT JOIN regimen_service_type rst ON p.service=rst.id
      WHERE YEAR(p.date_enrolled)='$year' 
      AND MONTH(p.date_enrolled)='$month'
      AND rst.name LIKE '%$service%'
      AND p.facility_code='$facility_code'
      AND p.active = '1'";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        if ($results) {
          $month_data[] = @(int) $results[0]['total'];
        } else {
          $month_data[] = 0;
        }
      }
      $service_array['data'] = $month_data;
      //append service data to main array
      $main_array[] = $service_array;
    }
    //chart data
    $resultArray = json_encode($main_array);
    $categories = json_encode(array_values($months));
    //chart settings
    $data['resultArraySize'] = 7;
    $data['container'] = 'chart_sales';
    $data['chartType'] = 'line';
    $data['title'] = 'Chart';
    $data['chartTitle'] = 'Listing of Patients Enrolled for the Year: ' . $year;
    $data['categories'] = $categories;
    $data['xAxix'] = 'Months of the Year';
    $data['suffix'] = '';
    $data['yAxix'] = 'Totals';
    $data['resultArray'] = $resultArray;
    $data['graphs'] = view('\Modules\ADT\Views\\graph_v', $data);
    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "Graph of Number of Patients Enrolled Per Month in a Given Year";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\\graphs_on_patients_v';
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

  public function all_service_statistics($start_date = "", $end_date = "")
  {
    //Variables
    $facility_code = session()->get("facility");
    $data['from'] = $start_date;
    $from = date('Y-m-d', time());
    $regimen_totals = array();
    $data = array();
    $total = 0;
    $overall_adult_male_art = 0;
    $overall_adult_male_pep = 0;
    $overall_adult_male_oi = 0;
    $overall_adult_male_prep = 0;

    $overall_adult_female_art = 0;
    $overall_adult_female_pep = 0;
    $overall_adult_female_pmtct = 0;
    $overall_adult_female_oi = 0;
    $overall_adult_female_prep = 0;

    $overall_child_male_art = 0;
    $overall_child_male_pep = 0;
    $overall_child_male_pmtct = 0;
    $overall_child_male_oi = 0;
    $overall_child_male_prep = 0;

    $overall_child_female_art = 0;
    $overall_child_female_pep = 0;
    $overall_child_female_pmtct = 0;
    $overall_child_female_oi = 0;
    $overall_child_female_prep = 0;

    //Get Total of all patients
    $sql = "SELECT p.current_regimen,count(*) as total FROM patient p 
          LEFT JOIN regimen r ON r.id = p.current_regimen 
          LEFT JOIN regimen_service_type rst ON rst.id = p.service 
          LEFT JOIN patient_status ps ON ps.id = p.current_status
          WHERE p.date_enrolled <='$from' AND ps.name ='active' AND p.facility_code = '$facility_code' 
          AND p.current_regimen != '' AND p.current_status != ''";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    $total = $results[0]['total'];

    //Get Totals for each regimen
    $sql = "SELECT count(*) as total, r.regimen_desc,r.regimen_code,p.current_regimen FROM patient p 
          LEFT JOIN regimen r ON r.id = p.current_regimen LEFT JOIN regimen_service_type rst ON rst.id = p.service 
          LEFT JOIN patient_status ps ON ps.id = p.current_status
          WHERE p.date_enrolled <='$from' AND ps.name ='active' AND p.facility_code = '$facility_code' 
          AND p.current_regimen != '' AND p.current_status != '' GROUP BY p.current_regimen ORDER BY r.regimen_code ASC";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();

    if ($results) {
      $dyn_table = "<table id='patient_listingh' border='1' cellpadding='5' class='dataTables'><thead>
              <tr>
              <th></th>
              <th>Total</th><th></th>
              <th>Adult</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
              <th>Children</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
              <th></th><th></th><th></th><th></th>
              </tr>
              <tr>
              <th></th>
              <th></th>
              <th></th>
              <th>Male</th><th></th><th></th><th></th><th></th><th></th><th></th>
              <th>Female</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
              <th>Male</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
              <th>Female</th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
              </tr>
              <tr>
              <th></th>
              <th></th>
              <th></th>
              <th>ART</th><th></th>
              <th>PEP</th><th></th>
              <th>OI</th><th></th>
              <th>PREP</th><th></th>
              <th>ART</th><th></th>
              <th>PEP</th><th></th>
              <th>PMTCT</th><th></th>
              <th>OI</th><th></th>
              <th>PREP</th><th></th>
              <th>ART</th><th></th>
              <th>PEP</th><th></th>
              <th>PMTCT</th><th></th>
              <th>OI</th><th></th>
              <th>PREP</th><th></th>
              <th>ART</th><th></th>
              <th>PEP</th><th></th>
              <th>PMTCT</th><th></th>
              <th>OI</th><th></th>
              <th>PREP</th><th></th>
              </tr>
              <tr>
              <th>Regimen</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              <th>No.</th>
              <th>%</th>
              </tr>
              </thead>
              <tbody>";
      foreach ($results as $result) {
        $regimen_totals[$result['current_regimen']] = $result['total'];
        $current_regimen = $result['current_regimen'];
        $regimen_name = $result['regimen_desc'];
        $regimen_code = $result['regimen_code'];
        $regimen_total = $result['total'];
        $regimen_total_percentage = number_format(($regimen_total / $total) * 100, 1);
        $dyn_table .= "<tr><td><b>$regimen_code</b> | $regimen_name</td><td>$regimen_total</td><td>$regimen_total_percentage</td>";

        //SQL for Adult Male Regimens
        $sql = "SELECT count(*) as total,p.service as service_id,rst.name FROM patient p 
                  LEFT JOIN regimen r ON r.id = p.current_regimen LEFT JOIN regimen_service_type rst ON rst.id = p.service 
                  LEFT JOIN patient_status ps ON ps.id = p.current_status
                  WHERE p.date_enrolled <='$from' AND ps.name ='active' 
                  AND p.facility_code = '$facility_code' AND p.current_regimen != '' 
                  AND p.current_status != '' AND p.gender=1 AND p.current_regimen='$current_regimen' AND FLOOR(datediff('$from',p.dob)/365)>15 
                  GROUP BY p.service ORDER BY rst.id ASC";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        $total_adult_male_art = "-";
        $total_adult_male_pep = "-";
        $total_adult_male_oi = "-";
        $total_adult_male_prep = "-";

        $total_adult_male_art_percentage = "-";
        $total_adult_male_pep_percentage = "-";
        $total_adult_male_oi_percentage = "-";
        $total_adult_male_prep_percentage = "-";
        if ($results) {
          foreach ($results as $result) {
            $total_adult_male = $result['total'];
            $service_code = $result['service_id'];
            $service_name = $result['name'];
            if ($service_name == "ART") {
              $overall_adult_male_art += $total_adult_male;
              $total_adult_male_art = number_format($total_adult_male);
              $total_adult_male_art_percentage = number_format(($total_adult_male / $total) * 100, 1);
            } else if ($service_name == "PEP") {
              $overall_adult_male_pep += $total_adult_male;
              $total_adult_male_pep = number_format($total_adult_male);
              $total_adult_male_pep_percentage = number_format(($total_adult_male_pep / $total) * 100, 1);
            } else if ($service_name == "OI Only") {
              $overall_adult_male_oi += $total_adult_male;
              $total_adult_male_oi = number_format($total_adult_male);
              $total_adult_male_oi_percentage = number_format(($total_adult_male_oi / $total) * 100, 1);
            } else if (strtoupper($service_name) == "PREP") {
              $overall_adult_male_prep += $total_adult_male;
              $total_adult_male_prep = number_format($total_adult_male);
              $total_adult_male_prep_percentage = number_format(($total_adult_male_prep / $total) * 100, 1);
            }
          }
          $dyn_table .= "<td>$total_adult_male_art</td><td>$total_adult_male_art_percentage</td><td>$total_adult_male_pep</td><td>$total_adult_male_pep_percentage</td><td>$total_adult_male_oi</td><td>$total_adult_male_oi_percentage</td><td>$total_adult_male_prep</td><td>$total_adult_male_prep_percentage</td>";
        } else {
          $dyn_table .= "<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>";
        }

        //SQL for Adult Female Regimens
        $sql = "SELECT count(*) as total,p.service as service_id,rst.name FROM patient p LEFT JOIN regimen r ON r.id = p.current_regimen LEFT JOIN regimen_service_type rst ON rst.id = p.service WHERE p.date_enrolled <='$from' AND p.current_status =1 AND p.facility_code = '$facility_code' AND p.current_regimen != '' AND p.current_status != '' AND p.gender=2 AND p.current_regimen='$current_regimen' AND FLOOR(datediff('$from',p.dob)/365)>15 GROUP BY p.service ORDER BY rst.id ASC";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        $total_adult_female_art = "-";
        $total_adult_female_pep = "-";
        $total_adult_female_pmtct = "-";
        $total_adult_female_oi = "-";
        $total_adult_female_prep = "-";

        $total_adult_female_art_percentage = "-";
        $total_adult_female_pep_percentage = "-";
        $total_adult_female_pmtct_percentage = "-";
        $total_adult_female_oi_percentage = "-";
        $total_adult_female_prep_percentage = "-";
        if ($results) {
          foreach ($results as $result) {
            $total_adult_female = $result['total'];
            $service_code = $result['service_id'];
            $service_name = $result['name'];
            if ($service_name == "ART") {
              $overall_adult_female_art += $total_adult_female;
              $total_adult_female_art = number_format($total_adult_female);
              $total_adult_female_art_percentage = number_format(($total_adult_female / $total) * 100, 1);
            } else if ($service_name == "PEP") {
              $overall_adult_female_pep += $total_adult_female;
              $total_adult_female_pep = number_format($total_adult_female);
              $total_adult_female_pep_percentage = number_format(($total_adult_female_pep / $total) * 100, 1);
            } else if ($service_name == "PMTCT") {
              $overall_adult_female_pmtct += $total_adult_female;
              $total_adult_female_pmtct = number_format($total_adult_female);
              $total_adult_female_pmtct_percentage = number_format(($total_adult_female_pmtct / $total) * 100, 1);
            } else if ($service_name == "OI Only") {
              $overall_adult_female_oi += $total_adult_female;
              $total_adult_female_oi = number_format($total_adult_female);
              $total_adult_female_oi_percentage = number_format(($total_adult_female_oi / $total) * 100, 1);
            } else if (strtoupper($service_name) == "PREP") {
              $overall_adult_female_prep += $total_adult_female;
              $total_adult_female_prep = number_format($total_adult_female);
              $total_adult_female_prep_percentage = number_format(($total_adult_female_prep / $total) * 100, 1);
            }
          }
          $dyn_table .= "<td>$total_adult_female_art</td><td>$total_adult_female_art_percentage</td><td>$total_adult_female_pep</td><td>$total_adult_female_pep_percentage</td><td>$total_adult_female_pmtct</td><td>$total_adult_female_pmtct_percentage</td><td>$total_adult_female_oi</td><td>$total_adult_female_oi_percentage</td><td>$total_adult_female_prep</td><td>$total_adult_female_prep_percentage</td>";
        } else {
          $dyn_table .= "<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>";
        }

        //SQL for Child Male Regimens
        $sql = "SELECT count(*) as total,p.service as service_id,rst.name FROM patient p LEFT JOIN regimen r ON r.id = p.current_regimen LEFT JOIN regimen_service_type rst ON rst.id = p.service WHERE p.date_enrolled <='$from' AND p.current_status =1 AND p.facility_code = '$facility_code' AND p.current_regimen != '' AND p.current_status != '' AND p.gender=1 AND p.current_regimen='$current_regimen' AND FLOOR(datediff('$from',p.dob)/365)<=15 GROUP BY p.service ORDER BY rst.id ASC";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        $total_child_male_art = "-";
        $total_child_male_pep = "-";
        $total_child_male_pmtct = "-";
        $total_child_male_oi = "-";
        $total_child_male_prep = "-";

        $total_child_male_art_percentage = "-";
        $total_child_male_pep_percentage = "-";
        $total_child_male_pmtct_percentage = "-";
        $total_child_male_oi_percentage = "-";
        $total_child_male_prep_percentage = "-";
        if ($results) {
          foreach ($results as $result) {
            $total_child_male = $result['total'];
            $service_code = $result['service_id'];
            $service_name = $result['name'];
            if ($service_name == "ART") {
              $overall_child_male_art += $total_child_male;
              $total_child_male_art = number_format($total_child_male);
              $total_child_male_art_percentage = number_format(($total_child_male / $total) * 100, 1);
            } else if ($service_name == "PEP") {
              $overall_child_male_pep += $total_child_male;
              $total_child_male_pep = number_format($total_child_male);
              $total_child_male_pep_percentage = number_format(($total_child_male_pep / $total) * 100, 1);
            } else if ($service_name == "PMTCT") {
              $overall_child_male_pmtct += $total_child_male;
              $total_child_male_pmtct = number_format($total_child_male);
              $total_child_male_pmtct_percentage = number_format(($total_child_male_pmtct / $total) * 100, 1);
            } else if ($service_name == "OI Only") {
              $overall_child_male_oi += $total_child_male;
              $total_child_male_oi = number_format($total_child_male);
              $total_child_male_oi_percentage = number_format(($total_child_male_oi / $total) * 100, 1);
            } else if (strtoupper($service_name) == "PREP") {
              $overall_child_male_prep += $total_child_male;
              $total_child_male_prep = number_format($total_child_male);
              $total_child_male_prep_percentage = number_format(($total_child_male_prep / $total) * 100, 1);
            }
          }
          $dyn_table .= "<td>$total_child_male_art</td><td>$total_child_male_art_percentage</td><td>$total_child_male_pep</td><td>$total_child_male_pep_percentage</td><td>$total_child_male_pmtct</td><td>$total_child_male_pmtct_percentage</td><td>$total_child_male_oi</td><td>$total_child_male_oi_percentage</td><td>$total_child_male_prep</td><td>$total_child_male_prep_percentage</td>";
        } else {
          $dyn_table .= "<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>";
        }

        //SQL for Child Female Regimens
        $sql = "SELECT count(*) as total,p.service as service_id,rst.name FROM patient p LEFT JOIN regimen r ON r.id = p.current_regimen LEFT JOIN regimen_service_type rst ON rst.id = p.service WHERE p.date_enrolled <='$from' AND p.current_status =1 AND p.facility_code = '$facility_code' AND p.current_regimen != '' AND p.current_status != '' AND p.gender=2 AND p.current_regimen='$current_regimen' AND FLOOR(datediff('$from',p.dob)/365)<=15 GROUP BY p.service ORDER BY rst.id ASC";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        $total_child_female_art = "-";
        $total_child_female_pep = "-";
        $total_child_female_pmtct = "-";
        $total_child_female_oi = "-";
        $total_child_female_prep = "-";

        $total_child_female_art_percentage = "-";
        $total_child_female_pep_percentage = "-";
        $total_child_female_pmtct_percentage = "-";
        $total_child_female_oi_percentage = "-";
        $total_child_female_prep_percentage = "-";
        if ($results) {
          foreach ($results as $result) {
            $total_child_female = $result['total'];
            $service_code = $result['service_id'];
            $service_name = $result['name'];
            if ($service_name == "ART") {
              $overall_child_female_art += $total_child_female;
              $total_child_female_art = number_format($total_child_female);
              $total_child_female_art_percentage = number_format(($total_child_female / $total) * 100, 1);
            } else if ($service_name == "PEP") {
              $overall_child_female_pep += $total_child_female;
              $total_child_female_pep = number_format($total_child_female);
              $total_child_female_pep_percentage = number_format(($total_child_female_pep / $total) * 100, 1);
            } else if ($service_name == "PMTCT") {
              $overall_child_female_pmtct += $total_child_female;
              $total_child_female_pmtct = number_format($total_child_female);
              $total_child_female_pmtct_percentage = number_format(($total_child_female_pmtct / $total) * 100, 1);
            } else if ($service_name == "OI Only") {
              $overall_child_female_oi += $total_child_female;
              $total_child_female_oi = number_format($total_child_female);
              $total_child_female_oi_percentage = number_format(($total_child_female_oi / $total) * 100, 1);
            } else if (strtoupper($service_name) == "PREP") {
              $overall_child_female_prep += $total_child_female;
              $total_child_female_prep = number_format($total_child_female);
              $total_child_female_prep_percentage = number_format(($total_child_female_prep / $total) * 100, 1);
            }
          }
          $dyn_table .= "<td>$total_child_female_art</td><td>$total_child_female_art_percentage</td><td>$total_child_female_pep</td><td>$total_child_female_pep_percentage</td><td>$total_child_female_pmtct</td><td>$total_child_female_pmtct_percentage</td><td>$total_child_female_oi</td><td>$total_child_female_oi_percentage</td><td>$total_child_female_prep</td><td>$total_child_female_prep_percentage</td>";
        } else {
          $dyn_table .= "<td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td>";
        }
        $dyn_table .= "</tr>";
      }
      $overall_art_male_percent = number_format(($overall_adult_male_art / $total) * 100, 1);
      $overall_pep_male_percent = number_format(($overall_adult_male_pep / $total) * 100, 1);
      $overall_oi_male_percent = number_format(($overall_adult_male_oi / $total) * 100, 1);
      $overall_prep_male_percent = number_format(($overall_adult_male_prep / $total) * 100, 1);

      $overall_art_female_percent = number_format(($overall_adult_female_art / $total) * 100, 1);
      $overall_pep_female_percent = number_format(($overall_adult_female_pep / $total) * 100, 1);
      $overall_pmtct_female_percent = number_format(($overall_adult_female_pmtct / $total) * 100, 1);
      $overall_oi_female_percent = number_format(($overall_adult_female_oi / $total) * 100, 1);
      $overall_prep_female_percent = number_format(($overall_adult_female_prep / $total) * 100, 1);

      $overall_art_childmale_percent = number_format(($overall_child_male_art / $total) * 100, 1);
      $overall_pep_childmale_percent = number_format(($overall_child_male_pep / $total) * 100, 1);
      $overall_oi_childmale_percent = number_format(($overall_child_male_pmtct / $total) * 100, 1);
      $overall_pmtct_childmale_percent = number_format(($overall_child_male_oi / $total) * 100, 1);
      $overall_prep_childmale_percent = number_format(($overall_child_male_prep / $total) * 100, 1);

      $overall_art_childfemale_percent = number_format(($overall_child_female_art / $total) * 100, 1);
      $overall_pep_childfemale_percent = number_format(($overall_child_female_pep / $total) * 100, 1);
      $overall_pmtct_childfemale_percent = number_format(($overall_child_female_pmtct / $total) * 100, 1);
      $overall_oi_childfemale_percent = number_format(($overall_child_female_oi / $total) * 100, 1);
      $overall_prep_childfemale_percent = number_format(($overall_child_female_prep / $total) * 100, 1);

      $dyn_table .= "</tbody><tfoot><tr><td>TOTALS</td><td>$total</td><td>100</td><td>$overall_adult_male_art</td><td>$overall_art_male_percent</td><td>$overall_adult_male_pep</td><td>$overall_pep_male_percent</td><td>$overall_adult_male_oi</td><td>$overall_oi_male_percent</td><td>$overall_adult_male_prep</td><td>$overall_prep_male_percent</td><td>$overall_adult_female_art</td><td>$overall_art_female_percent</td><td>$overall_adult_female_pep</td><td>$overall_pep_female_percent</td><td>$overall_adult_female_pmtct</td><td>$overall_pmtct_female_percent</td><td>$overall_adult_female_oi</td><td>$overall_oi_female_percent</td><td>$overall_adult_female_prep</td><td>$overall_prep_female_percent</td><td>$overall_child_male_art</td><td>$overall_art_childmale_percent</td><td>$overall_child_male_pep</td><td>$overall_pep_childmale_percent</td><td>$overall_child_male_pmtct</td><td>$overall_pmtct_childmale_percent</td><td>$overall_child_male_oi</td><td>$overall_oi_childmale_percent</td><td>$overall_child_male_prep</td><td>$overall_prep_childmale_percent</td><td>$overall_child_female_art</td><td>$overall_art_childfemale_percent</td><td>$overall_child_female_pep</td><td>$overall_pep_childfemale_percent</td><td>$overall_child_female_pmtct</td><td>$overall_pmtct_childfemale_percent</td><td>$overall_child_female_oi</td><td>$overall_oi_childfemale_percent</td><td>$overall_child_female_prep</td><td>$overall_prep_childfemale_percent</td></tr></tfoot></table>";
    } else {
      $dyn_table = "<h4 style='text-align: center'><span >No Data Available</span></h4>";
    }
    $data['from'] = date('d-M-Y', strtotime($from));
    $data['dyn_table'] = $dyn_table;
    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "early_warning_report_select";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "Active Patients By Regimen ";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\\active_patients_receiving_art_byregimen_v';
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function getFamilyPlanning($start_date = "")
  {
    $data['from'] = $start_date;
    $start_date = date('Y-m-d', strtotime($start_date));
    //$end_date = date('Y-m-d', strtotime($end_date));
    $facility_code = session()->get('facility');
    $arr = array();
    $total = 0;
    $sql = "select fplan from patient LEFT JOIN patient_status ps ON ps.id=current_status where date_enrolled <= '$start_date' AND ps.Name like '%active%' and gender='2' and gender !='' and facility_code='$facility_code' AND fplan != '' AND fplan != 'null' AND FLOOR(DATEDIFF(curdate(),dob)/365)>15 AND FLOOR(DATEDIFF(curdate(),dob)/365)<=49";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();

    if ($results) {
      $dyn_str = "<table border='1' id='patient_listing' class='dataTables' cellpadding='5'><thead><tr><th>Method</th><th>No. Of Women on Method</th><th>Percentage Proportion(%)</th></tr></thead><tbody>";
      foreach ($results as $result) {
        if (strstr($result['fplan'], ',', true)) {
          $values = explode(",", $result['fplan']);
          foreach ($values as $value) {
            $arr[] = $value;
          }
        } else {
          $arr[] = $result['fplan'];
        }
      }
      $family_planning = array_count_values($arr);
      foreach ($family_planning as $family_plan => $index) {
        $sql = "select name from family_planning where indicator='$family_plan'";
        $query = $this->db->query($sql);
        $results = $query->getResultArray();
        if ($results) {
          foreach ($results as $result) {
            $family[$result['name']] = $index;
          }
        }
        $total += $index;
      }

      foreach ($family as $farm => $index) {
        $dyn_str .= "<tr><td>" . $farm . "</td><td>" . $index . "</td><td>" . number_format(($index / $total) * 100, 1) . "%</td></tr>";
      }
      $dyn_str .= "</tbody><tfoot><tr><td><b>TOTALS</b></td><td><b>$total</b></td><td><b>100%</b></td></tr>";
      $dyn_str .= "</tfoot></table>";
    } else {
      $dyn_str = "<h4 style='text-align: center'><span >No Data Available</span></h4>";
    }

    $data['dyn_table'] = $dyn_str;
    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "Family Planning Summary";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\\family_planning_v';
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function getIndications($start_date = "", $end_date = "")
  {
    $data['from'] = $start_date;
    $data['to'] = $end_date;
    $start_date = date('Y-m-d', strtotime($start_date));
    $end_date = date('Y-m-d', strtotime($end_date));
    $facility_code = session()->get('facility');
    $sql = "select CONCAT_WS(' | ',oi.indication,oi.name) as indication_name,IF(FLOOR(DATEDIFF(curdate(),p.dob)/365)>15 and p.gender='1',count(*),'0') as adult_male,IF(FLOOR(DATEDIFF(curdate(),p.dob)/365)>15 and p.gender='2',count(*),'0') as adult_female,IF(FLOOR(DATEDIFF(curdate(),p.dob)/365)<=15 ,count(*),'0') as child from (select patient_id,indication from patient_visit where dispensing_date between '$start_date' and '$end_date' and facility='$facility_code' and indication !='0')as pv left join patient p on p.patient_number_ccc=pv.patient_id,opportunistic_infection oi where (oi.id=pv.indication or oi.indication=pv.indication) group by indication_name";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    $total = 0;
    $children = 0;
    $adult_male = 0;
    $adult_female = 0;
    $overall_adult_male = 0;
    $overall_adult_female = 0;
    $overall_children = 0;
    $dyn_table = "";
    $dyn_table .= "<table id='patient_listing' border='1' cellpadding='5' class='dataTables'><thead><tr><th>Indication</th><th>Adult Male</th><th>Adult Female</th><th>Children</th></tr></thead>";
    if ($results) {
      $dyn_table .= "<tbody>";
      foreach ($results as $result) {
        $indication = $result['indication_name'];
        $adult_male = $result['adult_male'];
        $adult_female = $result['adult_female'];
        $children = $result['child'];
        $overall_adult_male += $adult_male;
        $overall_adult_female += $adult_female;
        $overall_children += $children;
        $dyn_table .= "<tr><td><b>$indication <b></td><td>" . number_format($adult_male) . "</td><td>" . number_format($adult_female) . "</td><td>" . number_format($children) . "</td></tr>";
      }
      $total = $overall_adult_male + $overall_adult_female + $overall_children;
      $total = number_format($total);
      $dyn_table .= "</tbody><tfoot><tr><td><b>TOTALS ($total) </b></td><td><b>" . number_format($overall_adult_male) . "</b></td><td><b>" . number_format($overall_adult_female) . "</b></td><td><b>" . number_format($overall_children) . "</b></td></tr>";
      $dyn_table .= "</tfoot>";
    }
    $dyn_table .= "</table>";
    $data['dyn_table'] = $dyn_table;
    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "Patient Indication Summary";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\\patient_indication_v';
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function getTBPatients($start_date = "", $end_date = "")
  {
    $data['from'] = $start_date;
    $data['to'] = $end_date;
    $start_date = date('Y-m-d', strtotime($start_date));
    $end_date = date('Y-m-d', strtotime($end_date));
    $facility_code = session()->get('facility');
    $one_adult_male = 0;
    $one_child_male = 0;
    $one_adult_female = 0;
    $one_child_female = 0;
    $two_adult_male = 0;
    $two_child_male = 0;
    $two_adult_female = 0;
    $two_child_female = 0;
    $three_adult_male = 0;
    $three_child_male = 0;
    $three_adult_female = 0;
    $three_child_female = 0;

    $sql = "update patient set tbphase='0' where tbphase='un' or tbphase=''";
    $query = $this->db->query($sql);
    $sql = "select gender,FLOOR(DATEDIFF(curdate(),dob)/365) as age,tbphase from patient LEFT JOIN patient_status ps ON ps.id=current_status where date_enrolled between '$start_date' and '$end_date' AND ps.Name like '%active%' and facility_code='$facility_code' and gender !='' and tb='1' and tbphase !='0'";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    $strXML = array();
    if ($results) {
      foreach ($results as $result) {
        if ($result['tbphase'] == 1) {
          if ($result['gender'] == 1) {
            if ($result['age'] >= 15) {
              $one_adult_male++;
            } else if ($result['age'] < 15) {
              $one_child_male++;
            }
          } else if ($result['gender'] == 2) {
            if ($result['age'] >= 15) {
              $one_adult_female++;
            } else if ($result['age'] < 15) {
              $one_child_female++;
            }
          }
        } else if ($result['tbphase'] == 2) {
          if ($result['gender'] == 1) {
            if ($result['age'] >= 15) {
              $two_adult_male++;
            } else if ($result['age'] < 15) {
              $two_child_male++;
            }
          } else if ($result['gender'] == 2) {
            if ($result['age'] >= 15) {
              $two_adult_female++;
            } else if ($result['age'] < 15) {
              $two_child_female++;
            }
          }
        } else if ($result['tbphase'] == 3) {
          if ($result['gender'] == 1) {
            if ($result['age'] >= 15) {
              $three_adult_male++;
            } else if ($result['age'] < 15) {
              $three_child_male++;
            }
          } else if ($result['gender'] == 2) {
            if ($result['age'] >= 15) {
              $three_adult_female++;
            } else if ($result['age'] < 15) {
              $three_child_female++;
            }
          }
        }
      }
    }
    $dyn_table = "<table border='1' cellpadding='5' class='dataTables'><thead>
                  <tr>
                  <th></th><th>Adults</th><th></th><th>Children</th><th></th>
                  </tr>
                  <tr><th>Stages</th><th>No. of Males(TB)</th><th>No. of Females(TB)</th><th>No. of Males(TB)</th><th>No. of Females(TB)</th></tr></thead><tbody>";
    $dyn_table .= "<tr><td>Intensive</td><td>" . number_format($one_adult_male) . "</td><td>" . number_format($one_adult_female) . "</td><td>" . number_format($one_child_male) . "</td><td>" . number_format($one_child_female) . "</td></tr>";
    $dyn_table .= "<tr><td>Continuation</td><td>" . number_format($two_adult_male) . "</td><td>" . number_format($two_adult_female) . "</td><td>" . number_format($two_child_male) . "</td><td>" . number_format($two_child_female) . "</td></tr>";
    $dyn_table .= "<tr><td>Completed</td><td>" . number_format($three_adult_male) . "</td><td>" . number_format($three_adult_female) . "</td><td>" . number_format($three_child_male) . "</td><td>" . number_format($three_child_female) . "</td></tr>";
    $dyn_table .= "</tbody><tfoot><tr><td><b>TOTALS</b></td><td><b>" . number_format($one_adult_male + $two_adult_male + $three_adult_male) . "</b></td><td><b>" . number_format($one_adult_female + $two_adult_female + $three_adult_female) . "</b></td><td><b>" . number_format($one_child_male + $two_child_male + $three_child_male) . "</b></td><td><b>" . number_format($one_child_female + $two_child_female + $three_child_female) . "</b></td></tr>";
    $dyn_table .= "</tfoot></table>";
    $data['dyn_table'] = $dyn_table;
    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "TB Stages Summary";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\\tb_stages_v';
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function getChronic($start_date = "")
  {
    $data['from'] = $start_date;
    $start_date = date('Y-m-d', strtotime($start_date));
    $facility_code = session()->get('facility');
    $total = 0;
    $total_male_tb = 0;
    $total_female_tb = 0;
    $total_children_tb = 0;
    $adult_male = array();
    $adult_female = array();
    $child = array();
    $sql = "SELECT other_illnesses, FLOOR( DATEDIFF( curdate( ) , dob ) /365 ) AS age,gender FROM patient LEFT JOIN patient_status ps ON ps.id=current_status WHERE date_enrolled <= '$start_date' AND ps.Name like '%active%' AND gender != '' AND facility_code = '$facility_code' AND other_illnesses != '' AND other_illnesses != ',' AND other_illnesses != 'null'";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    if ($results) {
      foreach ($results as $result) {
        if (trim(strtoupper($result['other_illnesses'])) != null && trim(strtoupper($result['other_illnesses'])) != 'NULL') {

          if (strstr($result['other_illnesses'], ',', true)) {
            $values = explode(",", $result['other_illnesses']);
            foreach ($values as $value) {
              $arr[] = trim(strtoupper($value));
            }
          } else {
            $arr[] = trim(strtoupper($result['other_illnesses']));
          }
          if ($result['gender'] == 1) { //Check Male
            if ($result['age'] >= 15) { //Check Adult
              if (strstr(trim($result['other_illnesses']), ',', true)) {
                $values = explode(",", $result['other_illnesses']);
                foreach ($values as $value) {
                  $adult_male[] = trim(strtoupper($value));
                }
              } else {
                $adult_male[] = trim(strtoupper($result['other_illnesses']));
              }
            } else if ($result['age'] < 15) { //Check Child
              if (strstr(trim($result['other_illnesses']), ',', true)) {
                $values = explode(",", $result['other_illnesses']);
                foreach ($values as $value) {
                  $child[] = trim(strtoupper($value));
                }
              } else {
                $child[] = trim(strtoupper($result['other_illnesses']));
              }
            }
          } else if ($result['gender'] == 2) { //Check Female
            if ($result['age'] >= 15) { //Check Adult
              if (strstr(trim($result['other_illnesses']), ',', true)) {
                $values = explode(",", $result['other_illnesses']);
                foreach ($values as $value) {
                  $adult_female[] = trim(strtoupper($value));
                }
              } else {
                $adult_female[] = trim(strtoupper($result['other_illnesses']));
              }
            } else if ($result['age'] < 15) { //Check Child
              if (strstr(trim($result['other_illnesses']), ',', true)) {
                $values = explode(",", $result['other_illnesses']);
                foreach ($values as $value) {
                  $child[] = trim(strtoupper($value));
                }
              } else {
                $child[] = trim(strtoupper($result['other_illnesses']));
              }
            }
          }
        }
      }
      $other_illnesses = array_count_values($arr);
      $other_illnesses_male = array_count_values($adult_male);
      $other_illnesses_female = array_count_values($adult_female);
      $other_illnesses_child = array_count_values($child);
      $values = array();

      foreach ($other_illnesses as $other_illness => $index) {
        if (array_key_exists($other_illness, $other_illnesses_male)) {
          $values[$other_illness]['male'] = $index;
        } else {
          $values[$other_illness]['male'] = 0;
        }
        if (array_key_exists($other_illness, $other_illnesses_female)) {
          $values[$other_illness]['female'] = $index;
        } else {
          $values[$other_illness]['female'] = 0;
        }
        if (array_key_exists($other_illness, $other_illnesses_child)) {
          $values[$other_illness]['child'] = $index;
        } else {
          $values[$other_illness]['child'] = 0;
        }
        $total += $index;
      }
      foreach ($values as $value => $index) {
        foreach ($index as $key => $val) {
          $sql = "select * from other_illnesses where indicator='$value'";
          $query = $this->db->query($sql);
          $results = $query->getResultArray();
          if ($results) {
            foreach ($results as $result) {
              $answer = strtoupper($result['name']);
            }
            $values[$answer][$key] = $val;
            unset($values[$value]);
          }
        }
      }
    }
    //Get TB Numbers
    $sql = "select FLOOR( DATEDIFF( curdate( ) , dob ) /365 ) AS age,gender from patient WHERE date_enrolled <= '$start_date'  AND gender != '' AND facility_code = '$facility_code' AND tb='1' AND dob !='' AND gender !=''";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    if ($results) {
      foreach ($results as $result) {
        if ($result['age'] >= 15) {
          if ($result['gender'] == 1) {
            $total_male_tb++;
          } else if ($result['gender'] == 2) {
            $total_female_tb++;
          }
        } else if ($result['age'] < 15) {
          $total_children_tb++;
        }
      }
    }
    //Initialize tb
    $values['TB']['male'] = $total_male_tb;
    $values['TB']['female'] = $total_female_tb;
    $values['TB']['child'] = $total_children_tb;

    $overall_male = 0;
    $overall_female = 0;
    $overall_child = 0;

    $dyn_table = "<table border='1' cellpadding='5' class='dataTables'>
                    <thead><tr><th>Chronic Diseases</th><th>Adult Male</th><th>Adult Female</th><th>Children</th></tr></thead><tbody>";

    foreach ($values as $value => $indices) {
      $dyn_table .= "<tr><td><b>$value</b></td>";
      foreach ($indices as $index => $newval) {
        if ($index == "male") {
          $overall_male += $newval;
        } else if ($index == "female") {
          $overall_female += $newval;
        } else if ($index == "child") {
          $overall_child += $newval;
        }

        $val = number_format($newval);
        $dyn_table .= "<td>$val</td>";
      }
      $dyn_table .= "</tr>";
    }
    $dyn_table .= "</tbody><tfoot><tr><td><b>TOTALS</b></td><td><b>" . number_format($overall_male) . "</b></td><td><b>" . number_format($overall_female) . "</b></td><td><b>" . number_format($overall_child) . "</b></td></tr>";
    $dyn_table .= "</tfoot></table>";
    $data['dyn_table'] = $dyn_table;
    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "Chronic Illnesses Summary";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\\chronic_v';
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function getADR($start_date = "")
  {
    $data['from'] = $start_date;
    //$data['to'] = $end_date;
    $start_date = date('Y-m-d', strtotime($start_date));
    //$end_date = date('Y-m-d', strtotime($end_date));
    $facility_code = session()->get('facility');
    $male_adr = 0;
    $female_adr = 0;
    $male_noadr = 0;
    $female_noadr = 0;

    //Get Those With ADR
    $sql = "select gender,count(*)as total from patient LEFT JOIN patient_status ps ON ps.id=current_status WHERE date_enrolled <= '$start_date' AND ps.Name like '%active%' and facility_code='$facility_code' and adr !='' and adr !='null' and adr is not null and gender !='' group by gender";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    if ($results) {
      foreach ($results as $result) {
        if ($result['gender'] == 1) {
          $male_adr = $result['total'];
        } else if ($result['gender'] == 2) {
          $female_adr = $result['total'];
        }
      }
    }

    //Get Those Without ADR
    $sql = "select gender,count(*)as total from patient WHERE date_enrolled <= '$start_date'  and facility_code='$facility_code' and adr ='' or adr ='null' or adr is  null and gender !='' group by gender";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    if ($results) {
      foreach ($results as $result) {
        if ($result['gender'] == 1) {
          $male_noadr = $result['total'];
        } else if ($result['gender'] == 2) {
          $female_noadr = $result['total'];
        }
      }
    }

    $percentage_adr = 0;
    $percentage_noadr = 0;
    $total_adr_noadr = 0;
    $total_adr_noadr = $male_adr + $female_adr + $male_noadr + $female_noadr;
    if ($total_adr_noadr > 0) {
      $percentage_adr = (($male_adr + $female_adr) / ($total_adr_noadr)) * 100;
      $percentage_noadr = (($male_noadr + $female_noadr) / ($total_adr_noadr)) * 100;
    }

    $dyn_table = "<table border='1' cellpadding='5' class='dataTables'>";
    $dyn_table .= "<thead>";
    $dyn_table .= "<tr><th>Patients with Allergy</th><th></th><th>Patients without Allergy</th><th></th><th>Percentage with Allergy</th><th>Percentage without Allergy</th></tr>";
    $dyn_table .= "<tr><th>Male</th><th>Female</th><th>Male</th><th>Female</th><th>((Male +Female)/total)*100%</th><th>((Male +Female)/total)*100%</th></tr>";
    $dyn_table .= "</thead>";
    $dyn_table .= "<tbody>";
    $dyn_table .= "<tr><td>" . number_format($male_adr) . "</td><td>" . number_format($female_adr) . "</td><td>" . number_format($male_noadr) . "</td><td>" . number_format($female_noadr) . "</td><td>" . number_format($percentage_adr, 1) . "%</td><td>" . number_format($percentage_noadr, 1) . "%</td></tr>";
    $dyn_table .= "</tbody>";
    $dyn_table .= "</table>";
    $data['dyn_table'] = $dyn_table;
    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "Patient Allergies Summary";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\\allergy_v';
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function disclosure_chart($start_date = "", $end_date = "")
  {
    $data['from'] = $start_date;
    $data['to'] = $end_date;
    $heading = "Patient Disclosure Between $start_date and $end_date";
    $start_date = date('Y-m-d', strtotime($start_date));
    $end_date = date('Y-m-d', strtotime($end_date));
    $facility_code = session()->get('facility');
    $sql = "SELECT gender, disclosure, count( * ) AS total FROM `patient` LEFT JOIN patient_status ps ON ps.id=current_status where date_enrolled between '$start_date' and '$end_date' AND ps.Name like '%active%' and partner_status = '2' AND gender != '' AND disclosure != '2' AND facility_code='$facility_code' GROUP BY gender, disclosure";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    $strXML = array();
    $strXML['Male Disclosure(NO)'] = 0;
    $strXML['Male Disclosure(YES)'] = 0;
    $strXML['Female Disclosure(NO)'] = 0;
    $strXML['Female Disclosure(YES)'] = 0;
    if ($results) {
      foreach ($results as $result) {
        if ($result['gender'] == '1' && $result['disclosure'] == 0) {
          $strXML['Male Disclosure(NO)'] = (int) $result['total'];
        } else if ($result['gender'] == '1' && $result['disclosure'] == 1) {
          $strXML['Male Disclosure(YES)'] = (int) $result['total'];
        } else if ($result['gender'] == '2' && $result['disclosure'] == 0) {
          $strXML['Female Disclosure(NO)'] = (int) $result['total'];
        } else if ($result['gender'] == '2' && $result['disclosure'] == 1) {
          $strXML['Female Disclosure(YES)'] = (int) $result['total'];
        }
      }
    }
    $strXML = implode($strXML, ",");
    $strXML = array_map('intval', explode(",", $strXML));
    $resultArray = array();
    $nameArray = array("Male Disclosure(NO)", "Male Disclosure(YES)", "Female Disclosure(NO)", "Female Disclosure(YES)");
    $resultArray[] = array('name' => "Disclosure Status", 'data' => $strXML);
    $categories = json_encode($nameArray);
    $resultArray = json_encode($resultArray);
    $data['resultArraySize'] = 6;
    $data['container'] = "chart_div";
    $data['chartType'] = 'bar';
    $data['chartTitle'] = 'Patients Disclosure';
    $data['yAxix'] = 'Status';
    $data['categories'] = $categories;
    $data['resultArray'] = $resultArray;
    echo view('\Modules\ADT\Views\\chart_v', $data);
  }

  public function patients_disclosure($start_date = "", $end_date = "")
  {
    $data['from'] = $start_date;
    $data['to'] = $end_date;
    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_select";
    $data['selected_report_type'] = "Patient Status &amp; Disclosure";
    $data['report_title'] = "Summary";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\\patient_disclosure_v';
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function getBMI($start_date = "")
  {
    /*
        Formula BMI= weight(kg)/(height(m)*height(m))

        Stages of Obesity
        --------------------
       * Very Severely Underweight <15.0
       * Severely Underweight 15.0-16
       * Underweight 16.0-18.5
       * Normal 18.5-25.0
       * Overweight 25.0-30.0
       * Obese Class 1(Moderately Obese) 30.0-35.0
       * Obese Class 2(Severely Obese) 35.0-40.0
       * Obese Class 3(Very Severely Obese) >40.0
       */
    $data['from'] = $start_date;
    $start_date = date('Y-m-d', strtotime($start_date));
    $facility_code = session()->get('facility');
    $bmi_temp = array();

    $sql = "SELECT gender,rst.Name,ROUND((((weight)*10000)/(height*height)),1) AS BMI 
   FROM patient p 
   LEFT JOIN gender g ON g.id=p.gender 
   LEFT JOIN regimen_service_type rst ON rst.id=p.service 
   LEFT JOIN patient_status ps ON ps.id=p.current_status 
   WHERE p.date_enrolled<='$start_date' 
   AND p.facility_code='$facility_code' 
   AND ps.Name LIKE '%active%' 
   GROUP BY patient_number_ccc";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();


    $bmi_temp['ART']['Very Severely Underweight']['Male'] = 0;
    $bmi_temp['ART']['Severely Underweight']['Male'] = 0;
    $bmi_temp['ART']['Underweight']['Male'] = 0;
    $bmi_temp['ART']['Normal']['Male'] = 0;
    $bmi_temp['ART']['Overweight']['Male'] = 0;
    $bmi_temp['ART']['Moderately Obese']['Male'] = 0;
    $bmi_temp['ART']['Severely Obese']['Male'] = 0;
    $bmi_temp['ART']['Very Severely Obese']['Male'] = 0;

    $bmi_temp['ART']['Very Severely Underweight']['Female'] = 0;
    $bmi_temp['ART']['Severely Underweight']['Female'] = 0;
    $bmi_temp['ART']['Underweight']['Female'] = 0;
    $bmi_temp['ART']['Normal']['Female'] = 0;
    $bmi_temp['ART']['Overweight']['Female'] = 0;
    $bmi_temp['ART']['Moderately Obese']['Female'] = 0;
    $bmi_temp['ART']['Severely Obese']['Female'] = 0;
    $bmi_temp['ART']['Very Severely Obese']['Female'] = 0;

    $bmi_temp['PEP']['Very Severely Underweight']['Male'] = 0;
    $bmi_temp['PEP']['Severely Underweight']['Male'] = 0;
    $bmi_temp['PEP']['Underweight']['Male'] = 0;
    $bmi_temp['PEP']['Normal']['Male'] = 0;
    $bmi_temp['PEP']['Overweight']['Male'] = 0;
    $bmi_temp['PEP']['Moderately Obese']['Male'] = 0;
    $bmi_temp['PEP']['Severely Obese']['Male'] = 0;
    $bmi_temp['PEP']['Very Severely Obese']['Male'] = 0;

    $bmi_temp['PEP']['Very Severely Underweight']['Female'] = 0;
    $bmi_temp['PEP']['Severely Underweight']['Female'] = 0;
    $bmi_temp['PEP']['Underweight']['Female'] = 0;
    $bmi_temp['PEP']['Normal']['Female'] = 0;
    $bmi_temp['PEP']['Overweight']['Female'] = 0;
    $bmi_temp['PEP']['Moderately Obese']['Female'] = 0;
    $bmi_temp['PEP']['Severely Obese']['Female'] = 0;
    $bmi_temp['PEP']['Very Severely Obese']['Female'] = 0;

    $bmi_temp['PMTCT']['Very Severely Underweight']['Male'] = 0;
    $bmi_temp['PMTCT']['Severely Underweight']['Male'] = 0;
    $bmi_temp['PMTCT']['Underweight']['Male'] = 0;
    $bmi_temp['PMTCT']['Normal']['Male'] = 0;
    $bmi_temp['PMTCT']['Overweight']['Male'] = 0;
    $bmi_temp['PMTCT']['Moderately Obese']['Male'] = 0;
    $bmi_temp['PMTCT']['Severely Obese']['Male'] = 0;
    $bmi_temp['PMTCT']['Very Severely Obese']['Male'] = 0;

    $bmi_temp['PMTCT']['Very Severely Underweight']['Female'] = 0;
    $bmi_temp['PMTCT']['Severely Underweight']['Female'] = 0;
    $bmi_temp['PMTCT']['Underweight']['Female'] = 0;
    $bmi_temp['PMTCT']['Normal']['Female'] = 0;
    $bmi_temp['PMTCT']['Overweight']['Female'] = 0;
    $bmi_temp['PMTCT']['Moderately Obese']['Female'] = 0;
    $bmi_temp['PMTCT']['Severely Obese']['Female'] = 0;
    $bmi_temp['PMTCT']['Very Severely Obese']['Female'] = 0;

    $bmi_temp['OI']['Very Severely Underweight']['Male'] = 0;
    $bmi_temp['OI']['Severely Underweight']['Male'] = 0;
    $bmi_temp['OI']['Underweight']['Male'] = 0;
    $bmi_temp['OI']['Normal']['Male'] = 0;
    $bmi_temp['OI']['Overweight']['Male'] = 0;
    $bmi_temp['OI']['Moderately Obese']['Male'] = 0;
    $bmi_temp['OI']['Severely Obese']['Male'] = 0;
    $bmi_temp['OI']['Very Severely Obese']['Male'] = 0;

    $bmi_temp['OI']['Very Severely Underweight']['Female'] = 0;
    $bmi_temp['OI']['Severely Underweight']['Female'] = 0;
    $bmi_temp['OI']['Underweight']['Female'] = 0;
    $bmi_temp['OI']['Normal']['Female'] = 0;
    $bmi_temp['OI']['Overweight']['Female'] = 0;
    $bmi_temp['OI']['Moderately Obese']['Female'] = 0;
    $bmi_temp['OI']['Severely Obese']['Female'] = 0;
    $bmi_temp['OI']['Very Severely Obese']['Female'] = 0;

    $male_Very_Severely_Underweight = 0;
    $female_Very_Severely_Underweight = 0;
    $male_Severely_Underweight = 0;
    $female_Severely_Underweight = 0;
    $male_Underweight = 0;
    $female_Underweight = 0;
    $male_Normal = 0;
    $female_Normal = 0;
    $male_Overweight = 0;
    $female_Overweight = 0;
    $male_Moderately_Obese = 0;
    $female_Moderately_Obese = 0;
    $male_Severely_Obese = 0;
    $female_Severely_Obese = 0;
    $male_Very_Severely_Obese = 0;
    $female_Very_Severely_Obese = 0;

    if ($results) {
      foreach ($results as $result) {
        $temp_string = strtoupper($result['Name']);
        if ($temp_string != "") {
          //Check if ART
          $art_check = strpos(strtoupper("art"), $temp_string);
          //Check if PEP
          $pep_check = strpos(strtoupper("pep"), $temp_string);
          //Check if PMTCT
          $pmtct_check = strpos(strtoupper("pmtct"), $temp_string);
          //Check if OI
          $oi_check = strpos(strtoupper("oi only"), $temp_string);


          if ($art_check !== false) {
            if ($result['gender'] == 1) {
              if ($result['BMI'] >= 0 && $result['BMI'] < 15) {
                $bmi_temp['ART']['Very Severely Underweight']['Male']++;
                $male_Very_Severely_Underweight++;
              } else if ($result['BMI'] >= 15 && $result['BMI'] < 16) {
                $bmi_temp['ART']['Severely Underweight']['Male']++;
                $male_Severely_Underweight++;
              } else if ($result['BMI'] >= 16 && $result['BMI'] < 18.5) {
                $bmi_temp['ART']['Underweight']['Male']++;
                $male_Underweight++;
              } else if ($result['BMI'] >= 18.5 && $result['BMI'] < 25) {
                $bmi_temp['ART']['Normal']['Male']++;
                $male_Normal++;
              } else if ($result['BMI'] >= 25 && $result['BMI'] < 30) {
                $bmi_temp['ART']['Overweight']['Male']++;
                $male_Overweight++;
              } else if ($result['BMI'] >= 30 && $result['BMI'] < 35) {
                $bmi_temp['ART']['Moderately Obese']['Male']++;
                $male_Moderately_Obese++;
              } else if ($result['BMI'] >= 35 && $result['BMI'] < 40) {
                $bmi_temp['ART']['Severely Obese']['Male']++;
                $male_Severely_Obese++;
              } else if ($result['BMI'] >= 40) {
                $bmi_temp['ART']['Very Severely Obese']['Male']++;
                $male_Very_Severely_Obese++;
              }
            } else if ($result['gender'] == 2) {
              if ($result['BMI'] >= 0 && $result['BMI'] < 15) {
                $bmi_temp['ART']['Very Severely Underweight']['Female']++;
                $female_Very_Severely_Underweight++;
              } else if ($result['BMI'] >= 15 && $result['BMI'] < 16) {
                $bmi_temp['ART']['Severely Underweight']['Female']++;
                $female_Severely_Underweight++;
              } else if ($result['BMI'] >= 16 && $result['BMI'] < 18.5) {
                $bmi_temp['ART']['Underweight']['Female']++;
                $female_Underweight++;
              } else if ($result['BMI'] >= 18.5 && $result['BMI'] < 25) {
                $bmi_temp['ART']['Normal']['Female']++;
                $female_Normal++;
              } else if ($result['BMI'] >= 25 && $result['BMI'] < 30) {
                $bmi_temp['ART']['Overweight']['Female']++;
                $female_Overweight++;
              } else if ($result['BMI'] >= 30 && $result['BMI'] < 35) {
                $bmi_temp['ART']['Moderately Obese']['Female']++;
                $female_Moderately_Obese++;
              } else if ($result['BMI'] >= 35 && $result['BMI'] < 40) {
                $bmi_temp['ART']['Severely Obese']['Female']++;
                $female_Severely_Obese++;
              } else if ($result['BMI'] >= 40) {
                $bmi_temp['ART']['Very Severely Obese']['Female']++;
                $female_Very_Severely_Obese++;
              }
            }
          } else if ($pep_check !== false) {
            if ($result['gender'] == 1) {
              if ($result['BMI'] >= 0 && $result['BMI'] < 15) {
                $bmi_temp['PEP']['Very Severely Underweight']['Male']++;
                $male_Very_Severely_Underweight++;
              } else if ($result['BMI'] >= 15 && $result['BMI'] < 16) {
                $bmi_temp['PEP']['Severely Underweight']['Male']++;
                $male_Severely_Underweight++;
              } else if ($result['BMI'] >= 16 && $result['BMI'] < 18.5) {
                $bmi_temp['PEP']['Underweight']['Male']++;
                $male_Underweight++;
              } else if ($result['BMI'] >= 18.5 && $result['BMI'] < 25) {
                $bmi_temp['PEP']['Normal']['Male']++;
                $male_Normal++;
              } else if ($result['BMI'] >= 25 && $result['BMI'] < 30) {
                $bmi_temp['PEP']['Overweight']['Male']++;
                $male_Overweight++;
              } else if ($result['BMI'] >= 30 && $result['BMI'] < 35) {
                $bmi_temp['PEP']['Moderately Obese']['Male']++;
                $male_Moderately_Obese++;
              } else if ($result['BMI'] >= 35 && $result['BMI'] < 40) {
                $bmi_temp['PEP']['Severely Obese']['Male']++;
                $male_Severely_Obese++;
              } else if ($result['BMI'] >= 40) {
                $bmi_temp['PEP']['Very Severely Obese']['Male']++;
                $male_Very_Severely_Obese++;
              }
            } else if ($result['gender'] == 2) {
              if ($result['BMI'] >= 0 && $result['BMI'] < 15) {
                $bmi_temp['PEP']['Very Severely Underweight']['Female']++;
                $female_Very_Severely_Underweight++;
              } else if ($result['BMI'] >= 15 && $result['BMI'] < 16) {
                $bmi_temp['PEP']['Severely Underweight']['Female']++;
                $female_Severely_Underweight++;
              } else if ($result['BMI'] >= 16 && $result['BMI'] < 18.5) {
                $bmi_temp['PEP']['Underweight']['Female']++;
                $female_Underweight++;
              } else if ($result['BMI'] >= 18.5 && $result['BMI'] < 25) {
                $bmi_temp['PEP']['Normal']['Female']++;
                $female_Normal++;
              } else if ($result['BMI'] >= 25 && $result['BMI'] < 30) {
                $bmi_temp['PEP']['Overweight']['Female']++;
                $female_Overweight++;
              } else if ($result['BMI'] >= 30 && $result['BMI'] < 35) {
                $bmi_temp['PEP']['Moderately Obese']['Female']++;
                $female_Moderately_Obese++;
              } else if ($result['BMI'] >= 35 && $result['BMI'] < 40) {
                $bmi_temp['PEP']['Severely Obese']['Female']++;
                $female_Severely_Obese++;
              } else if ($result['BMI'] >= 40) {
                $bmi_temp['PEP']['Very Severely Obese']['Female']++;
                $female_Very_Severely_Obese++;
              }
            }
          } else if ($pmtct_check !== false) {
            if ($result['gender'] == 1) {
              if ($result['BMI'] >= 0 && $result['BMI'] < 15) {
                $bmi_temp['PMTCT']['Very Severely Underweight']['Male']++;
                $male_Very_Severely_Underweight++;
              } else if ($result['BMI'] >= 15 && $result['BMI'] < 16) {
                $bmi_temp['PMTCT']['Severely Underweight']['Male']++;
                $male_Severely_Underweight++;
              } else if ($result['BMI'] >= 16 && $result['BMI'] < 18.5) {
                $bmi_temp['PMTCT']['Underweight']['Male']++;
                $male_Underweight++;
              } else if ($result['BMI'] >= 18.5 && $result['BMI'] < 25) {
                $bmi_temp['PMTCT']['Normal']['Male']++;
                $male_Normal++;
              } else if ($result['BMI'] >= 25 && $result['BMI'] < 30) {
                $bmi_temp['PMTCT']['Overweight']['Male']++;
                $male_Overweight++;
              } else if ($result['BMI'] >= 30 && $result['BMI'] < 35) {
                $bmi_temp['PMTCT']['Moderately Obese']['Male']++;
                $male_Moderately_Obese++;
              } else if ($result['BMI'] >= 35 && $result['BMI'] < 40) {
                $bmi_temp['PMTCT']['Severely Obese']['Male']++;
                $male_Severely_Obese++;
              } else if ($result['BMI'] >= 40) {
                $bmi_temp['PMTCT']['Very Severely Obese']['Male']++;
                $male_Very_Severely_Obese++;
              }
            } else if ($result['gender'] == 2) {
              if ($result['BMI'] >= 0 && $result['BMI'] < 15) {
                $bmi_temp['PMTCT']['Very Severely Underweight']['Female']++;
                $female_Very_Severely_Underweight++;
              } else if ($result['BMI'] >= 15 && $result['BMI'] < 16) {
                $bmi_temp['PMTCT']['Severely Underweight']['Female']++;
                $female_Severely_Underweight++;
              } else if ($result['BMI'] >= 16 && $result['BMI'] < 18.5) {
                $bmi_temp['PMTCT']['Underweight']['Female']++;
                $female_Underweight++;
              } else if ($result['BMI'] >= 18.5 && $result['BMI'] < 25) {
                $bmi_temp['PMTCT']['Normal']['Female']++;
                $female_Normal++;
              } else if ($result['BMI'] >= 25 && $result['BMI'] < 30) {
                $bmi_temp['PMTCT']['Overweight']['Female']++;
                $female_Overweight++;
              } else if ($result['BMI'] >= 30 && $result['BMI'] < 35) {
                $bmi_temp['PMTCT']['Moderately Obese']['Female']++;
                $female_Moderately_Obese++;
              } else if ($result['BMI'] >= 35 && $result['BMI'] < 40) {
                $bmi_temp['PMTCT']['Severely Obese']['Female']++;
                $female_Severely_Obese++;
              } else if ($result['BMI'] >= 40) {
                $bmi_temp['PMTCT']['Very Severely Obese']['Female']++;
                $female_Very_Severely_Obese++;
              }
            }
          } else if ($oi_check !== false) {
            if ($result['gender'] == 1) {
              if ($result['BMI'] >= 0 && $result['BMI'] < 15) {
                $bmi_temp['OI']['Very Severely Underweight']['Male']++;
                $male_Very_Severely_Underweight++;
              } else if ($result['BMI'] >= 15 && $result['BMI'] < 16) {
                $bmi_temp['OI']['Severely Underweight']['Male']++;
                $male_Severely_Underweight++;
              } else if ($result['BMI'] >= 16 && $result['BMI'] < 18.5) {
                $bmi_temp['OI']['Underweight']['Male']++;
                $male_Underweight++;
              } else if ($result['BMI'] >= 18.5 && $result['BMI'] < 25) {
                $bmi_temp['OI']['Normal']['Male']++;
                $male_Normal++;
              } else if ($result['BMI'] >= 25 && $result['BMI'] < 30) {
                $bmi_temp['OI']['Overweight']['Male']++;
                $male_Overweight++;
              } else if ($result['BMI'] >= 30 && $result['BMI'] < 35) {
                $bmi_temp['OI']['Moderately Obese']['Male']++;
                $male_Moderately_Obese++;
              } else if ($result['BMI'] >= 35 && $result['BMI'] < 40) {
                $bmi_temp['OI']['Severely Obese']['Male']++;
                $male_Severely_Obese++;
              } else if ($result['BMI'] >= 40) {
                $bmi_temp['OI']['Very Severely Obese']['Male']++;
                $male_Very_Severely_Obese++;
              }
            } else if ($result['gender'] == 2) {
              if ($result['BMI'] >= 0 && $result['BMI'] < 15) {
                $bmi_temp['OI']['Very Severely Underweight']['Female']++;
                $female_Very_Severely_Underweight++;
              } else if ($result['BMI'] >= 15 && $result['BMI'] < 16) {
                $bmi_temp['OI']['Severely Underweight']['Female']++;
                $female_Severely_Underweight++;
              } else if ($result['BMI'] >= 16 && $result['BMI'] < 18.5) {
                $bmi_temp['OI']['Underweight']['Female']++;
                $female_Underweight++;
              } else if ($result['BMI'] >= 18.5 && $result['BMI'] < 25) {
                $bmi_temp['OI']['Normal']['Female']++;
                $female_Normal++;
              } else if ($result['BMI'] >= 25 && $result['BMI'] < 30) {
                $bmi_temp['OI']['Overweight']['Female']++;
                $female_Overweight++;
              } else if ($result['BMI'] >= 30 && $result['BMI'] < 35) {
                $bmi_temp['OI']['Moderately Obese']['Female']++;
                $female_Moderately_Obese++;
              } else if ($result['BMI'] >= 35 && $result['BMI'] < 40) {
                $bmi_temp['OI']['Severely Obese']['Female']++;
                $female_Severely_Obese++;
              } else if ($result['BMI'] >= 40) {
                $bmi_temp['OI']['Very Severely Obese']['Female']++;
                $female_Very_Severely_Obese++;
              }
            }
          }
        }
      }
    }
    $dyn_table = "<table border='1' cellpadding='5' class='dataTables'><thead>";
    $dyn_table .= "<tr><th></th><th>Very Severely Underweight</th><th></th><th>Severely Underweight</th><th></th><th>Underweight</th><th></th><th>Normal</th><th></th><th>Overweight</th><th></th><th>Moderately Obese</th><th></th><th>Severely Obese</th><th></th><th>Very Severely Obese</th><th></th></tr>";
    $dyn_table .= "<tr><th>Type of Service</th><th>Male</th><th>Female</th><th>Male</th><th>Female</th><th>Male</th><th>Female</th><th>Male</th><th>Female</th><th>Male</th><th>Female</th><th>Male</th><th>Female</th><th>Male</th><th>Female</th><th>Male</th><th>Female</th></tr><tbody>";
    foreach ($bmi_temp as $temp_values => $temp_value) {
      $dyn_table .= "<tr><td>$temp_values</td>";
      foreach ($temp_value as $temp_data => $temp_code) {
        foreach ($temp_code as $code) {
          $dyn_table .= "<td>$code</td>";
        }
      }
      $dyn_table .= "</tr>";
    }
    $dyn_table .= "</tbody><tfoot><tr class='tfoot'><td><b>TOTALS</b></td><td><b>" . number_format($male_Very_Severely_Underweight) . "</b></td><td><b>" . number_format($female_Very_Severely_Underweight) . "</b></td><td><b>" . number_format($male_Severely_Underweight) . "</b></td><td><b>" . number_format($female_Severely_Underweight) . "</b></td><td><b>" . number_format($male_Underweight) . "</b></td><td><b>" . number_format($female_Underweight) . "</b></td><td><b>" . number_format($male_Normal) . "</b></td><td><b>" . number_format($female_Normal) . "</b></td><td><b>" . number_format($male_Overweight) . "</b></td><td><b>" . number_format($female_Overweight) . "</b></td><td><b>" . number_format($male_Moderately_Obese) . "</b></td><td><b>" . number_format($female_Moderately_Obese) . "</b></td><td><b>" . number_format($male_Severely_Obese) . "</b></td><td><b>" . number_format($female_Severely_Obese) . "</b></td><td><b>" . number_format($male_Very_Severely_Obese) . "</b></td><td><b>" . number_format($female_Very_Severely_Obese) . "</b></td></tr>";
    $dyn_table .= "</tfoot></table>";

    $data['overall'] = $male_Very_Severely_Underweight + $female_Very_Severely_Underweight + $male_Severely_Underweight + $female_Severely_Underweight + $male_Underweight + $female_Underweight + $male_Normal + $female_Normal + $male_Overweight + $female_Overweight + $male_Moderately_Obese + $female_Moderately_Obese + $male_Severely_Obese + $female_Severely_Obese + $male_Very_Severely_Obese + $female_Very_Severely_Obese;
    $data['dyn_table'] = $dyn_table;
    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_select";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "Patient BMI Summary";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\\patient_bmi_v';
    echo view('\Modules\ADT\Views\\template', $data);
    //End
  }

  public function getisoniazidPatients($from = "", $to = "")
  {
    //Variables

    $row_string = "";
    $status = "";
    $overall_total = 0;
    $today = date('Y-m-d');
    $late_by = "";
    $facility_code = session()->get("facility");
    $from = date('Y-m-d', strtotime($from));
    $to = date('Y-m-d', strtotime($to));

    //Get all patients who have apppointments on the selected date range
    //Routine Isoniazid
    //male adult
    $sql1 = "SELECT * FROM patient WHERE (isoniazid_end_date >= '$to') AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query1 = $this->db->query($sql1);
    $result = count($query1->getResultArray());

    //female adult
    $sql2 = "SELECT * FROM patient WHERE (isoniazid_end_date >= '$to') AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query2 = $this->db->query($sql2);
    $result1 = count($query2->getResultArray());

    //male child
    $sql3 = "SELECT * FROM patient WHERE (isoniazid_end_date >= '$to') AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query3 = $this->db->query($sql3);
    $result2 = count($query3->getResultArray());

    //female child
    $sql4 = "SELECT * FROM patient WHERE (isoniazid_end_date >= '$to') AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query4 = $this->db->query($sql4);
    $result3 = count($query4->getResultArray());

    //Started on isoniazid
    //male adult
    $sql5 = "SELECT * FROM patient WHERE (isoniazid_start_date >= '$from') AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query5 = $this->db->query($sql5);
    $result4 = count($query5->getResultArray());

    //female adult
    $sql6 = "SELECT * FROM patient WHERE (isoniazid_start_date >= '$from') AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query6 = $this->db->query($sql6);
    $result5 = count($query6->getResultArray());

    //male child
    $sql7 = "SELECT * FROM patient WHERE (isoniazid_start_date >= '$from') AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query7 = $this->db->query($sql7);
    $result6 = count($query7->getResultArray());

    //female child
    $sql8 = "SELECT * FROM patient WHERE (isoniazid_start_date >= '$from') AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query8 = $this->db->query($sql8);
    $result7 = count($query8->getResultArray());

    //Completed on isoniazid
    //male adult
    $sql9 = "SELECT * FROM patient WHERE (isoniazid_end_date >= '$from') AND (isoniazid_end_date < '$to') AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query9 = $this->db->query($sql9);
    $result8 = count($query9->getResultArray());

    //female adult
    $sql10 = "SELECT * FROM patient WHERE (isoniazid_end_date >= '$from') AND (isoniazid_end_date < '$to') AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query10 = $this->db->query($sql10);
    $result9 = count($query10->getResultArray());

    //male child
    $sql11 = "SELECT * FROM patient WHERE (isoniazid_end_date >= '$from') AND (isoniazid_end_date < '$to') AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query11 = $this->db->query($sql11);
    $result10 = count($query11->getResultArray());

    //female child
    $sql12 = "SELECT * FROM patient WHERE (isoniazid_end_date >= '$from') AND (isoniazid_end_date < '$to') AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query12 = $this->db->query($sql12);
    $result11 = count($query12->getResultArray());

    //Cotrimoxazole
    //male adult
    $sql13 = "SELECT * FROM patient WHERE drug_prophylaxis like '%1%' AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query13 = $this->db->query($sql13);
    $result12 = count($query13->getResultArray());

    //female adult
    $sql14 = "SELECT * FROM patient WHERE drug_prophylaxis like '%1%' AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query14 = $this->db->query($sql14);
    $result13 = count($query14->getResultArray());

    //male child
    $sql15 = "SELECT * FROM patient WHERE drug_prophylaxis like '%1%' AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query15 = $this->db->query($sql15);
    $result14 = count($query15->getResultArray());

    //female child
    $sql16 = "SELECT * FROM patient WHERE drug_prophylaxis like '%1%' AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query16 = $this->db->query($sql16);
    $result15 = count($query16->getResultArray());

    //Dapsone
    //male adult
    $sql17 = "SELECT * FROM patient WHERE drug_prophylaxis like '%2%' AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query17 = $this->db->query($sql17);
    $result16 = count($query17->getResultArray());

    //female adult
    $sql18 = "SELECT * FROM patient WHERE drug_prophylaxis like '%2%' AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query18 = $this->db->query($sql18);
    $result17 = count($query18->getResultArray());

    //male child
    $sql19 = "SELECT * FROM patient WHERE drug_prophylaxis like '%2%' AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query19 = $this->db->query($sql19);
    $result18 = count($query19->getResultArray());

    //female child
    $sql20 = "SELECT * FROM patient WHERE drug_prophylaxis like '%2%' AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query20 = $this->db->query($sql20);
    $result19 = count($query20->getResultArray());

    //Fluconazole
    //male adult
    $sql21 = "SELECT * FROM patient WHERE drug_prophylaxis like '%4%' AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query21 = $this->db->query($sql21);
    $result20 = count($query21->getResultArray());

    //female adult
    $sql22 = "SELECT * FROM patient WHERE drug_prophylaxis like '%4%' AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query22 = $this->db->query($sql22);
    $result21 = count($query22->getResultArray());

    //male child
    $sql23 = "SELECT * FROM patient WHERE drug_prophylaxis like '%4%' AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query23 = $this->db->query($sql23);
    $result22 = count($query23->getResultArray());

    //female child
    $sql24 = "SELECT * FROM patient WHERE drug_prophylaxis like '%4%' AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query24 = $this->db->query($sql24);
    $result23 = count($query24->getResultArray());

    //Completed on Rifapentine/Isoniazid
    //male adult
    $sql25 = "SELECT * FROM patient WHERE drug_prophylaxis like '%5%' AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query25 = $this->db->query($sql25);
    $result24 = count($query25->getResultArray());

    //female adult
    $sql26 = "SELECT * FROM patient WHERE drug_prophylaxis like '%5%' AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query26 = $this->db->query($sql26);
    $result25 = count($query26->getResultArray());

    //male child
    $sql27 = "SELECT * FROM patient WHERE drug_prophylaxis like '%5%' AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query27 = $this->db->query($sql27);
    $result26 = count($query27->getResultArray());

    //female child
    $sql28 = "SELECT * FROM patient WHERE drug_prophylaxis like '%5%' AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query28 = $this->db->query($sql28);
    $result27 = count($query28->getResultArray());

    //Started on rifapentine/isoniazid
    //male adult
    $sql29 = "SELECT * FROM patient WHERE (rifap_isoniazid_start_date >= '$from') AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query29 = $this->db->query($sql29);
    $result28 = count($query29->getResultArray());

    //female adult
    $sql30 = "SELECT * FROM patient WHERE (rifap_isoniazid_start_date >= '$from') AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query30 = $this->db->query($sql30);
    $result29 = count($query30->getResultArray());

    //male child
    $sql31 = "SELECT * FROM patient WHERE (rifap_isoniazid_start_date >= '$from') AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query31 = $this->db->query($sql31);
    $result30 = count($query31->getResultArray());

    //female child
    $sql32 = "SELECT * FROM patient WHERE (rifap_isoniazid_start_date >= '$from') AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query32 = $this->db->query($sql32);
    $result31 = count($query32->getResultArray());


    //Routine rifapentine/isoniazid
    //male adult
    $sql33 = "SELECT * FROM patient WHERE (rifap_isoniazid_end_date >= '$to') AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query33 = $this->db->query($sql33);
    $result32 = count($query33->getResultArray());

    //female adult
    $sql34 = "SELECT * FROM patient WHERE (rifap_isoniazid_end_date >= '$to') AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND current_status=1";
    $query34 = $this->db->query($sql34);
    $result33 = count($query34->getResultArray());

    //male child
    $sql35 = "SELECT * FROM patient WHERE (rifap_isoniazid_end_date >= '$to') AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query35 = $this->db->query($sql35);
    $result34 = count($query35->getResultArray());

    //female child
    $sql36 = "SELECT * FROM patient WHERE (rifap_isoniazid_end_date >= '$to') AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND current_status=1";
    $query36 = $this->db->query($sql36);
    $result35 = count($query36->getResultArray());

    $adults_isoniazid_total = $result8 + $result9;
    $adults_cotrimoxazole_total = $result12 + $result13;
    $adults_dapsone_total = $result16 + $result17;
    $adults_fluconazole_total = $result20 + $result21;
    $adults_rifap_isoniazid_total  = $result24 + $result25;
    $adults_routine_isoniazid_total = $result + $result1;
    $adults_routine_rifapentine_isoniazid_total = $result32 + $result33;
    $adults_patients_started_on_isoniazid_total = $result4 + $result5;
    $adults_patients_started_on_rifapentine_isoniazid_total = $result28 + $result29;

    $children_isoniazid_total = $result10 + $result11;
    $children_cotrimoxazole_total = $result14 + $result15;
    $children_dapsone_total = $result18 + $result19;
    $children_fluconazole_total = $result22 + $result23;
    $children_rifap_isoniazid_total = $result26 + $result27;
    $children_routine_isoniazid_total = $result2 + $result3;
    $children_routine_rifapentine_isoniazid_total = $result34 + $result35;
    $children_patients_started_on_isoniazid_total = $result6 + $result7;
    $children_patients_started_on_rifapentine_isoniazid_total = $result30 + $result31;


    $isoniazid_total = $result8 + $result9 + $result10 + $result11;
    $cotrimoxazole_total = $result12 + $result13 + $result14 + $result15;
    $dapsone_total = $result16 + $result17 + $result18 + $result19;
    $fluconazole_total = $result20 + $result21 + $result22 + $result23;
    $rifap_isoniazid_total  = $result24 + $result25 + $result26 + $result27;
    $routine_isoniazid_total = $result + $result1 + $result2 + $result3;
    $routine_rifapentine_isoniazid_total = $result32 + $result33 + $result34 + $result35;
    $patients_started_on_isoniazid_total = $result4 + $result5 + $result6 + $result7;
    $patients_started_on_rifapentine_isoniazid_total = $result28 + $result29 + $result30 + $result31;

    $male_adults_total = $result + $result12 + $result16 + $result20 + $result24;
    $female_adults_total = $result1 + $result13 + $result17 + $result21 + $result25;
    $adults_total = $male_adults_total + $female_adults_total;

    $male_children_total = $result2 + $result14 + $result18 + $result22 + $result26;
    $female_children_total = $result3 + $result15 + $result19 + $result23 + $result27;
    $children_total = $male_children_total + $female_children_total;


    $total_patients = $adults_total + $children_total;


    $row_string = "
                      <table border='1' class='dataTables'>
                      <thead >
                      <tr>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th>Adults</th>
                      <th></th>
                      <th></th>
                      <th>Children</th>
                      <th></th>
                      </tr>
                      <tr>
                      <th> </th>
                      <th> <b>Total</b></th>
                      <th> Male </th>
                      <th> Female </th>
                      <th> Total </th>

                      <th> Male  </th>
                      <th> Female </th>
                      <th> Total </th>



                      </tr></thead><tbody>

                      <tr>
                      <td>No of patients on Cotrimoxazole</td>
                      <td><b>" . $cotrimoxazole_total . "</b></td>
                      <td>" . $result12 . "</td>
                      <td>" . $result13 . "</td>
                      <td>" . $adults_cotrimoxazole_total . "</td>
                      <td>" . $result14 . "</td>
                      <td>" . $result15 . "</td>
                      <td>" . $children_cotrimoxazole_total . "</td>
                      </tr>

                      <tr>
                      <td>No of patients on Dapsone</td>
                      <td><strong>" . $dapsone_total . "</strong></td>
                      <td>" . $result16 . "</td>
                      <td>" . $result17 . "</td>
                      <td>" . $adults_dapsone_total . "</td>
                      <td>" . $result18 . "</td>
                      <td>" . $result19 . "</td>
                      <td>" . $children_dapsone_total . "</td>
                      </tr>

                      <tr>
                      <td>No of patients on Fluconazole</td>
                      <td><b>" . $fluconazole_total . "</b></td>
                      <td>" . $result20 . "</td>
                      <td>" . $result21 . "</td>
                      <td>" . $adults_fluconazole_total . "</td>
                      <td>" . $result22 . "</td>
                      <td>" . $result23 . "</td>
                      <td>" . $children_fluconazole_total . "</td>
                      </tr>

                      <tr>
                      <td>No of patients started on Isoniazid </td>
                      <td><b>" . $patients_started_on_isoniazid_total . "</b></td>
                      <td>" . $result4 . "</td>
                      <td>" . $result5 . "</td>
                      <td>" . $adults_patients_started_on_isoniazid_total . "</td>
                      <td>" . $result6 . "</td>
                      <td>" . $result7 . "</td>
                      <td>" . $children_patients_started_on_isoniazid_total . "</td>
                      </tr>

                      <tr>
                      <td>No of patients started on Rifapentine/Isoniazid </td>
                      <td><b>" . $patients_started_on_rifapentine_isoniazid_total . "</b></td>
                      <td>" . $result28 . "</td>
                      <td>" . $result29 . "</td>
                      <td>" . $adults_patients_started_on_rifapentine_isoniazid_total . "</td>
                      <td>" . $result30 . "</td>
                      <td>" . $result31 . "</td>
                      <td>" . $children_patients_started_on_rifapentine_isoniazid_total . "</td>
                      </tr>

                      <tr>
                      <td>No of patients on routine Isoniazid </td>
                      <td><b>" . $routine_isoniazid_total . "</b></td>
                      <td>" . $result . "</td>
                      <td>" . $result1 . "</td>
                      <td>" . $adults_routine_isoniazid_total . "</td>
                      <td>" . $result2 . "</td>
                      <td>" . $result3 . "</td>
                      <td>" . $children_routine_isoniazid_total . "</td>
                      </tr>

                      <tr>
                      <td>No of patients on routine Rifapentine/Isoniazid </td>
                      <td><b>" . $routine_rifapentine_isoniazid_total . "</b></td>
                      <td>" . $result32 . "</td>
                      <td>" . $result33 . "</td>
                      <td>" . $adults_routine_rifapentine_isoniazid_total . "</td>
                      <td>" . $result34 . "</td>
                      <td>" . $result35 . "</td>
                      <td>" . $children_routine_rifapentine_isoniazid_total . "</td>
                      </tr>

                      <tr>
                      <td>No of patients completed  Isoniazid </td>
                      <td><b>" . $isoniazid_total . "</b></td>
                      <td>" . $result8 . "</td>
                      <td>" . $result9 . "</td>
                      <td>" . $adults_isoniazid_total . "</td>
                      <td>" . $result10 . "</td>
                      <td>" . $result11 . "</td>
                      <td>" . $children_isoniazid_total . "</td>
                      </tr>

                      <tr>
                      <td>No of patients completed Rifapentine/Isoniazid</td>
                      <td><b>" . $rifap_isoniazid_total . "</b></td>
                      <td>" . $result24 . "</td>
                      <td>" . $result25 . "</td>
                      <td>" . $adults_rifap_isoniazid_total . "</td>
                      <td>" . $result26 . "</td>
                      <td>" . $result27 . "</td>
                      <td>" . $children_rifap_isoniazid_total . "</td>
                      </tr>

                      </tbody>
                      <tfoot>
                      <tr>
                      <th>Total</th>
                      <th><b>" . $total_patients . "</b></th>
                      <th>" . $male_adults_total . "</th>
                      <th>" . $female_adults_total . "</th>
                      <th>" . $adults_total . "</th>
                      <th>" . $male_children_total . "</th>
                      <th>" . $female_children_total . "</th>
                      <th>" . $children_total . "</th>
                      </tr>
                      </tfoot>

                      ";


    $row_string .= "</table>";
    $data['from'] = date('d-M-Y', strtotime($from));
    $data['to'] = date('d-M-Y', strtotime($to));
    $data['dyn_table'] = $row_string;
    $data['visited_later'] = 0;

    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "List of Patients not on isoniazid";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\\patients_on_isoniazid_v';
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function getnonisoniazidPatients($from = "", $list = null)
  {
    //Variables

    $row_string = "";
    $status = "";
    $overall_total = 0;
    $today = date('Y-m-d');
    $late_by = "";
    $facility_code = session()->get("facility");
    $from = date('Y-m-d', strtotime($from));

    //Get all patients who have never been on isoniazid 
    //Isoniazid
    //male adult
    $sql1 = "SELECT * FROM patient left join patient_status on patient_status.id = patient.current_status WHERE (isoniazid_start_date = '' OR isoniazid_start_date IS NULL) AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND patient_status.name like '%active%' AND date_enrolled <= '$from'";
    // print_r($sql1);die;

    $query1 = $this->db->query($sql1);
    $result = count($query1->getResultArray());
    //$count=$result['COUNT(*)'];
    //female adult
    $sql2 = "SELECT * FROM patient left join patient_status on patient_status.id = patient.current_status WHERE (isoniazid_start_date = '' OR isoniazid_start_date IS NULL) AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)>=15 AND patient_status.name like '%active%' AND date_enrolled <= '$from'";

    $query2 = $this->db->query($sql2);
    $result1 = count($query2->getResultArray());
    //male child
    $sql3 = "SELECT * FROM patient left join patient_status on patient_status.id = patient.current_status WHERE (isoniazid_start_date = '' OR isoniazid_start_date IS NULL) AND gender=1 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND patient_status.name like '%active%' AND date_enrolled <= '$from'";

    $query3 = $this->db->query($sql3);
    $result3 = count($query3->getResultArray());
    //female adult
    $sql4 = "SELECT * FROM patient left join patient_status on patient_status.id = patient.current_status WHERE (isoniazid_start_date = '' OR isoniazid_start_date IS NULL) AND gender=2 AND FLOOR(DATEDIFF('$from',dob)/365)<15 AND patient_status.name like '%active%' AND date_enrolled <= '$from'";

    $query4 = $this->db->query($sql4);
    $result4 = count($query4->getResultArray());
    $adults_routine_isoniazid_total = $result + $result1;

    $children_non_isoniazid_total = $result3 + $result4;
    $non_isoniazid_total = $result + $result1 + $result3 + $result4;

    $row_string = "
                      <table border='1' class='dataTables'>
                      <thead >
                      <tr>
                      <th></th>
                      <th></th>
                      <th></th>
                      <th>Adults</th>
                      <th></th>
                      <th></th>
                      <th>Children</th>
                      <th></th>
                      </tr>
                      <tr>
                      <th> </th>
                      <th> <b>Total (All)</b></th>
                      <th> Male </th>
                      <th> Female </th>
                      <th> Total </th>

                      <th> Male  </th>
                      <th> Female </th>
                      <th> Total </th>



                      </tr></thead><tbody>
                      <tr>
                      <td><a href='../getnonisoniazidPatientslist/" . $from . "'>No of patients not on isoniazid</a> </td>
                      <td><b>" . $non_isoniazid_total . "</b></td>
                      <td>" . $result . "</td>
                      <td>" . $result1 . "</td>
                      <td>" . $adults_routine_isoniazid_total . "</td>
                      <td>" . $result3 . "</td>
                      <td>" . $result4 . "</td>
                      <td>" . $children_non_isoniazid_total . "</td>
                      </tr>
                      </tbody>
                      <tfoot>
                      <tr>
                      <th>Total</th>
                      <th><b>" . $non_isoniazid_total . "</b></th>
                      <th>" . $result . "</th>
                      <th>" . $result1 . "</th>
                      <th>" . $adults_routine_isoniazid_total . "</th>
                      <th>" . $result3 . "</th>
                      <th>" . $result4 . "</th>
                      <th>" . $children_non_isoniazid_total . "</th>
                      </tr>
                      </tfoot>";


    $row_string .= "</table>";
    $data['from'] = date('d-M-Y', strtotime($from));
    $data['to'] = date('d-M-Y', strtotime($from));
    $data['dyn_table'] = $row_string;
    $data['visited_later'] = 0;

    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";

    $data['all_count'] = $non_isoniazid_total;
    $data['report_title'] = "List of Patients not on isoniazid";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\\patients_not_on_isoniazid_v';
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function getnonisoniazidPatientslist1($to = null)
  {

    $row_string = "";
    $status = "";
    $overall_total = 0;
    $today = date('Y-m-d');
    $late_by = "";
    $facility_code = session()->get("facility");
    $to = date('Y-m-d', strtotime($to));

    //Get all patients who have never been on isoniazid 
    //Isoniazid
    //male adult
    $sql1 = "SELECT * FROM patient 
              inner join gender on patient.gender = gender.id 
              left join regimen on patient.start_regimen	= regimen.id
              left join patient_status on patient_status.id = patient.current_status
              WHERE (isoniazid_start_date = '' OR isoniazid_start_date IS NULL)
              AND patient_status.name LIKE '%active%' AND date_enrolled <= '$to'
              AND dob != '' ";

    $query1 = $this->db->query($sql1);
    $result = $query1->result();
    // echo "<pre>";						print_r($result);die;
    $tr = "";


    foreach ($result as $patient) {
      $tr .= "<tr>
                  <td>" . $patient->patient_number_ccc . " </td>
                  <td>" . $patient->medical_record_number . " </td>
                  <td>" . $patient->first_name . " " . $patient->last_name . " </td>
                  <td>" . $patient->name . " </td>
                  <td>" . $patient->nextappointment . " </td>
                  <td>" . $patient->regimen_desc . " </td>
                  </tr>";
    }
    $row_string = "
                  <table border='1' class='dataTables'>
                  <thead >
                  <tr>
                  <th> patient ccc number</th>
                  <th> medical recordno. </th>
                  <th> name </th>
                  <th> gender </th>
                  <th> next appointment </th>
                  <th> current regimen </th>
                  </tr>
                  </thead>
                  <tbody>					
                  $tr
                  </tbody>
                  <tfoot>
                  <tr>
                  <th> patient ccc number</th>
                  <th> medical recordno. </th>
                  <th> name </th>
                  <th> gender </th>
                  <th> next appointment </th>
                  <th> current regimen </th>
                  </tr>
                  </tfoot>";


    $row_string .= "</table>";

    $data['from'] = date('d-M-Y', strtotime($to));
    $data['to'] = date('d-M-Y', strtotime($to));
    $data['dyn_table'] = $row_string;
    $data['visited_later'] = 0;

    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['all_count'] = count($result);

    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['report_title'] = "List of Patients not on isoniazid";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\\patients_not_on_isoniazid_list_v';
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function get_prep_patients($period_start = "", $period_end = "")
  {
    $report_items = array(
      "enrollment_in_prep" => "SELECT 
			COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 15 AND (YEAR(CURDATE())-YEAR(dob)) < 20 , 1, NULL)) as male_15,
			COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 20 AND (YEAR(CURDATE())-YEAR(dob)) < 25 , 1, NULL)) as male_20,
			COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 25 AND (YEAR(CURDATE())-YEAR(dob)) < 30 , 1, NULL)) as male_25,
			COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 30  , 1, NULL)) as male_30,
			COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 15 AND (YEAR(CURDATE())-YEAR(dob)) < 20 , 1, NULL)) as female_15,
			COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 20 AND (YEAR(CURDATE())-YEAR(dob)) < 25 , 1, NULL)) as female_20,
			COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 25 AND (YEAR(CURDATE())-YEAR(dob)) < 30 , 1, NULL)) as female_25,
			COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 30  , 1, NULL)) as female_30
			FROM patient p
			LEFT JOIN gender g ON g.id = p.gender
			LEFT JOIN regimen_service_type rst ON rst.id = p.service
			WHERE date_enrolled BETWEEN  ? AND ?
			AND rst.name LIKE '%prep%'",
      "tested_positive" => "SELECT 
			COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 15 AND (YEAR(CURDATE())-YEAR(dob)) < 20 , 1, NULL)) as male_15,
			COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 20 AND (YEAR(CURDATE())-YEAR(dob)) < 25 , 1, NULL)) as male_20,
			COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 25 AND (YEAR(CURDATE())-YEAR(dob)) < 30 , 1, NULL)) as male_25,
			COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 30  , 1, NULL)) as male_30,
			COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 15 AND (YEAR(CURDATE())-YEAR(dob)) < 20 , 1, NULL)) as female_15,
			COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 20 AND (YEAR(CURDATE())-YEAR(dob)) < 25 , 1, NULL)) as female_20,
			COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 25 AND (YEAR(CURDATE())-YEAR(dob)) < 30 , 1, NULL)) as female_25,
			COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 30  , 1, NULL)) as female_30
			FROM patient p
			LEFT JOIN gender g ON g.id = p.gender
			LEFT JOIN patient_prep_test pst ON pst.patient_id = p.id
			WHERE pst.test_date BETWEEN  ? AND ?
			AND pst.test_result = '1'",
      "currently_on_prep" => "SELECT 
			COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 15 AND (YEAR(CURDATE())-YEAR(dob)) < 20 , 1, NULL)) as male_15,
			COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 20 AND (YEAR(CURDATE())-YEAR(dob)) < 25 , 1, NULL)) as male_20,
			COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 25 AND (YEAR(CURDATE())-YEAR(dob)) < 30 , 1, NULL)) as male_25,
			COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 30  , 1, NULL)) as male_30,
			COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 15 AND (YEAR(CURDATE())-YEAR(dob)) < 20 , 1, NULL)) as female_15,
			COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 20 AND (YEAR(CURDATE())-YEAR(dob)) < 25 , 1, NULL)) as female_20,
			COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 25 AND (YEAR(CURDATE())-YEAR(dob)) < 30 , 1, NULL)) as female_25,
			COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 30  , 1, NULL)) as female_30
			FROM patient p
			LEFT JOIN gender g ON g.id = p.gender
			LEFT JOIN regimen_service_type rst ON rst.id = p.service
			LEFT JOIN patient_status ps ON p.current_status = ps.id
			WHERE p.date_enrolled <= ?
			AND rst.name LIKE '%prep%'
			AND ps.Name LIKE '%active%'",
      "cumulative_ever_on_prep" => "SELECT 
			COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 15 AND (YEAR(CURDATE())-YEAR(dob)) < 20 , 1, NULL)) as male_15,
			COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 20 AND (YEAR(CURDATE())-YEAR(dob)) < 25 , 1, NULL)) as male_20,
			COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 25 AND (YEAR(CURDATE())-YEAR(dob)) < 30 , 1, NULL)) as male_25,
			COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 30  , 1, NULL)) as male_30,
			COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 15 AND (YEAR(CURDATE())-YEAR(dob)) < 20 , 1, NULL)) as female_15,
			COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 20 AND (YEAR(CURDATE())-YEAR(dob)) < 25 , 1, NULL)) as female_20,
			COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 25 AND (YEAR(CURDATE())-YEAR(dob)) < 30 , 1, NULL)) as female_25,
			COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 30  , 1, NULL)) as female_30
			FROM patient p
			LEFT JOIN gender g ON g.id = p.gender
			LEFT JOIN regimen_service_type rst ON rst.id = p.service
			WHERE p.date_enrolled <= ?
			AND rst.name LIKE '%prep%'"
    );


    $dyn_table = "<table border='1' id='patient_listing'  cellpadding='5' class='dataTables'>";
    $dyn_table .= "<thead>
                        <tr>
                        <th>Description</th>
                        <th>Total</th>
                        <th colspan='4'>Male</th>
                        <th colspan='4'>Female</th>
                        </tr>
                        <tr>
                        <th></th>
                        <th></th>
                        <th>15 - 19</th>
                        <th>20 - 24</th>
                        <th>25 - 30</th>
                        <th>30 & Above</th>

                        <th>15 - 19</th>
                        <th>20 - 24</th>
                        <th>25 - 30</th>
                        <th>30 & Above</th>
                        </tr>
                        </thead>
                        <tbody>";

    foreach ($report_items as $report => $sql) {
      // print_r($sql);die;
      if (!in_array($report, array('currently_on_prep', 'cumulative_ever_on_prep'))) {
        $query = $this->db->query($sql, array($period_start, $period_end));
      } else {
        $query = $this->db->query($sql, array($period_end));
      }
      $result = $query->getRowArray();
      $report = ucwords(str_ireplace("_", " ", $report));
      $total = ($result['male_15'] + $result['male_20'] + $result['male_25'] + $result['male_30'] + $result['female_15'] + $result['female_20'] + $result['female_25'] + $result['female_30']);

      $dyn_table .= "<tr><td>"
        . $report . "</td><td>"
        . $total . "</td><td>"
        . $result['male_15'] . "</td><td>"
        . $result['male_20'] . "</td><td>"
        . $result['male_25'] . "</td><td>"
        . $result['male_30'] . "</td><td>"
        . $result['female_15'] . "</td><td>"
        . $result['female_20'] . "</td><td>"
        . $result['female_25'] . "</td><td>"
        . $result['female_30'] . "</td></tr>";
    }
    $dyn_table .= "</tbody><tfoot></tfoot></table>";

    $data['dyn_table'] = $dyn_table;
    $data['from'] = date('d-M-Y', strtotime($period_start));
    $data['to'] = date('d-M-Y', strtotime($period_end));
    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "Patients PREP Summary";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\\patient_prep_summary_v';
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function get_pep_reasons($period_start = "", $period_end = "")
  {

    $period_start = date('Y-m-d', strtotime($period_start));
    $period_end = date('Y-m-d', strtotime($period_end));

    $sql = "SELECT  pr.name,pr.id,
                COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 15 AND (YEAR(CURDATE())-YEAR(dob)) < 20 , 1, NULL)) as male_15,
                COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 20 AND (YEAR(CURDATE())-YEAR(dob)) < 25 , 1, NULL)) as male_20,
                COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 25 AND (YEAR(CURDATE())-YEAR(dob)) < 30 , 1, NULL)) as male_25,
                COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 30  , 1, NULL)) as male_30,

                COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 15 AND (YEAR(CURDATE())-YEAR(dob)) < 20 , 1, NULL)) as female_15,
                COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 20 AND (YEAR(CURDATE())-YEAR(dob)) < 25 , 1, NULL)) as female_20,
                COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 25 AND (YEAR(CURDATE())-YEAR(dob)) < 30 , 1, NULL)) as female_25,
                COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 30  , 1, NULL)) as female_30
                FROM patient p
                INNER JOIN gender g ON g.id = p.gender
                INNER join pep_reason pr on 
                pr.id = p.pep_reason	
                INNER join regimen_service_type rst on rst.id = p.service WHERE LOWER(rst.name) = 'pep'
                AND p.date_enrolled between '$period_start' and '$period_end'
                group by pep_reason
                union 
                SELECT 'Total',pr.id,
                COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 15 AND (YEAR(CURDATE())-YEAR(dob)) < 20 , 1, NULL)) as male_15,
                COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 20 AND (YEAR(CURDATE())-YEAR(dob)) < 25 , 1, NULL)) as male_20,
                COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 25 AND (YEAR(CURDATE())-YEAR(dob)) < 30 , 1, NULL)) as male_25,
                COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 30  , 1, NULL)) as male_30,

                COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 15 AND (YEAR(CURDATE())-YEAR(dob)) < 20 , 1, NULL)) as female_15,
                COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 20 AND (YEAR(CURDATE())-YEAR(dob)) < 25 , 1, NULL)) as female_20,
                COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 25 AND (YEAR(CURDATE())-YEAR(dob)) < 30 , 1, NULL)) as female_25,
                COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 30  , 1, NULL)) as female_30
                FROM patient p
                INNER JOIN gender g ON g.id = p.gender
                INNER join pep_reason pr on 
                pr.id = p.pep_reason
                INNER join regimen_service_type rst on rst.id = p.service WHERE LOWER(rst.name) = 'pep'
                AND p.date_enrolled between '$period_start' and '$period_end'
                ";


    $dyn_table = "<table border='1' id='patient_listing'  cellpadding='5' class='dataTables'>";
    $dyn_table .= "<thead>
                        <tr>
                        <th>Description</th>
                        <th>Total</th>
                        <th>Male</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Female</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        </tr>
                        <tr>
                        <th></th>
                        <th></th>
                        <th>15 - 19</th>
                        <th>20 - 24</th>
                        <th>25 - 30</th>
                        <th>30 & Above</th>

                        <th>15 - 19</th>
                        <th>20 - 24</th>
                        <th>25 - 30</th>
                        <th>30 & Above</th>
                        </tr>
                        </thead>
                        <tbody>";


    //$sql = "SELECT count( * ) AS total FROM patient p LEFT JOIN patient_source ps ON ps.id = p.source WHERE date_enrolled BETWEEN '$from' AND '$to' $supported_query AND facility_code = '$facility_code' AND source !='' AND p.active='1'";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    // echo "<pre>";
    foreach ($results as $res) {
      $total = ($res['male_15'] + $res['male_20'] + $res['male_25'] + $res['male_30'] + $res['female_15'] + $res['female_20'] + $res['female_25'] + $res['female_30']);

      $dyn_table .= "<tr><td>"
        . $res['name'] . "</td><td>"
        . $total . "</td><td>"
        . $res['male_15'] . "</td><td>"
        . $res['male_20'] . "</td><td>"
        . $res['male_25'] . "</td><td>"
        . $res['male_30'] . "</td><td>"
        . $res['female_15'] . "</td><td>"
        . $res['female_20'] . "</td><td>"
        . $res['female_25'] . "</td><td>"
        . $res['female_30'] . "</td></tr>";
    }

    $dyn_table .= "</tbody><tfoot></tfoot></table>";

    $data['dyn_table'] = $dyn_table;
    $data['from'] = date('d-M-Y', strtotime($period_start));
    $data['to'] = date('d-M-Y', strtotime($period_end));
    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "PEP Reasons Summary";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\\pep_reasons_summary_v';
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function get_pep_reasons_patients($period_start = "", $period_end = "")
  {
    $sql = "SELECT p.patient_number_ccc,p.first_name,p.last_name,g.name as gender, FLOOR(DATEDIFF(now(),dob)/365) as age, pr.name as pep_reason
              FROM patient p
              INNER JOIN gender g ON g.id = p.gender
              INNER join pep_reason pr on 
              pr.id = p.pep_reason	
              INNER join regimen_service_type rst on rst.id = p.service WHERE LOWER(rst.name) = 'pep'
              AND p.date_enrolled between '$period_start' and '$period_end'	";

    $dyn_table = "<table border='1' id='patient_listing'  cellpadding='5' class='dataTables'>";
    $dyn_table .= "<thead>
              <tr>
              <th>CCC Number</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Gender </th>
              <th>Age</th>
              <th>Prep Reason</th>
              </tr>
              </thead>
              <tbody>";

    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    foreach ($results as $res) {

      $dyn_table .= "<tr><td>"
        . $res['patient_number_ccc'] . "</td><td>"
        . $res['first_name'] . "</td><td>"
        . $res['last_name'] . "</td><td>"
        . $res['gender'] . "</td><td>"
        . $res['age'] . "</td><td>"
        . $res['pep_reason'] . "</td></tr>";
    }

    $dyn_table .= "</tbody><tfoot></tfoot></table>";

    $data['dyn_table'] = $dyn_table;
    $data['from'] = date('d-M-Y', strtotime($period_start));
    $data['to'] = date('d-M-Y', strtotime($period_end));
    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "PEP Reasons Patients";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\pep_reasons_patients_v';
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function get_prep_reasons($period_start = "", $period_end = "")
  {
    $sql = "SELECT  pr.name,pr.id,
                COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 15 AND (YEAR(CURDATE())-YEAR(dob)) < 20 , 1, NULL)) as male_15,
                COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 20 AND (YEAR(CURDATE())-YEAR(dob)) < 25 , 1, NULL)) as male_20,
                COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 25 AND (YEAR(CURDATE())-YEAR(dob)) < 30 , 1, NULL)) as male_25,
                COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 30  , 1, NULL)) as male_30,

                COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 15 AND (YEAR(CURDATE())-YEAR(dob)) < 20 , 1, NULL)) as female_15,
                COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 20 AND (YEAR(CURDATE())-YEAR(dob)) < 25 , 1, NULL)) as female_20,
                COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 25 AND (YEAR(CURDATE())-YEAR(dob)) < 30 , 1, NULL)) as female_25,
                COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 30  , 1, NULL)) as female_30
                FROM patient p
                INNER JOIN gender g ON g.id = p.gender
                inner join patient_prep_test ppt on p.id = ppt.patient_id
                inner join prep_reason pr on pr.id = ppt.prep_reason_id
                INNER join regimen_service_type rst on rst.id = p.service WHERE LOWER(rst.name) = 'prep'
                AND p.date_enrolled between '$period_start' and '$period_end'
                group by pr.name
                union 
                SELECT 'Total',pr.id,
                COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 15 AND (YEAR(CURDATE())-YEAR(dob)) < 20 , 1, NULL)) as male_15,
                COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 20 AND (YEAR(CURDATE())-YEAR(dob)) < 25 , 1, NULL)) as male_20,
                COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 25 AND (YEAR(CURDATE())-YEAR(dob)) < 30 , 1, NULL)) as male_25,
                COUNT(IF(LOWER(g.name) = 'male' AND (YEAR(CURDATE())-YEAR(dob)) >= 30  , 1, NULL)) as male_30,

                COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 15 AND (YEAR(CURDATE())-YEAR(dob)) < 20 , 1, NULL)) as female_15,
                COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 20 AND (YEAR(CURDATE())-YEAR(dob)) < 25 , 1, NULL)) as female_20,
                COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 25 AND (YEAR(CURDATE())-YEAR(dob)) < 30 , 1, NULL)) as female_25,
                COUNT(IF(LOWER(g.name) = 'female' AND (YEAR(CURDATE())-YEAR(dob)) >= 30  , 1, NULL)) as female_30
                FROM patient p
                INNER JOIN gender g ON g.id = p.gender
                inner join patient_prep_test ppt on p.id = ppt.patient_id
                inner join prep_reason pr on pr.id = ppt.prep_reason_id
                INNER join regimen_service_type rst on rst.id = p.service WHERE LOWER(rst.name) = 'prep'
                AND p.date_enrolled between '$period_start' and '$period_end'	
                ";

    $dyn_table = "<table border='1' id='patient_listing'  cellpadding='5' class='dataTables'>";
    $dyn_table .= "<thead>
                        <tr>
                        <th>Description</th>
                        <th>Total</th>
                        <th>Male</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Female</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        </tr>
                        <tr>
                        <th></th>
                        <th></th>
                        <th>15 - 19</th>
                        <th>20 - 24</th>
                        <th>25 - 30</th>
                        <th>30 & Above</th>

                        <th>15 - 19</th>
                        <th>20 - 24</th>
                        <th>25 - 30</th>
                        <th>30 & Above</th>
                        </tr>
                        </thead>
                        <tbody>";


    //$sql = "SELECT count( * ) AS total FROM patient p LEFT JOIN patient_source ps ON ps.id = p.source WHERE date_enrolled BETWEEN '$from' AND '$to' $supported_query AND facility_code = '$facility_code' AND source !='' AND p.active='1'";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    // echo "<pre>";
    foreach ($results as $res) {
      $total = ($res['male_15'] + $res['male_20'] + $res['male_25'] + $res['male_30'] + $res['female_15'] + $res['female_20'] + $res['female_25'] + $res['female_30']);

      $dyn_table .= "<tr><td>"
        . $res['name'] . "</td><td>"
        . $total . "</td><td>"
        . $res['male_15'] . "</td><td>"
        . $res['male_20'] . "</td><td>"
        . $res['male_25'] . "</td><td>"
        . $res['male_30'] . "</td><td>"
        . $res['female_15'] . "</td><td>"
        . $res['female_20'] . "</td><td>"
        . $res['female_25'] . "</td><td>"
        . $res['female_30'] . "</td></tr>";
    }

    $dyn_table .= "</tbody><tfoot></tfoot></table>";

    $data['dyn_table'] = $dyn_table;
    $data['from'] = date('d-M-Y', strtotime($period_start));
    $data['to'] = date('d-M-Y', strtotime($period_end));
    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "PREP Reasons Summary";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\\prep_reasons_summary_v';
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function get_prep_reasons_patients($period_start = "", $period_end = "")
  {
    $sql = "SELECT p.patient_number_ccc,p.first_name,p.last_name,g.name as gender, FLOOR(DATEDIFF(now(),dob)/365) as age, pr.name as prep_reason
              FROM patient p
              INNER JOIN gender g ON g.id = p.gender
              INNER join patient_prep_test ppt on p.id = ppt.patient_id
              INNER join prep_reason pr on pr.id = ppt.prep_reason_id
              INNER join regimen_service_type rst on rst.id = p.service WHERE LOWER(rst.name) = 'prep'
              AND p.date_enrolled between '$period_start' and '$period_end'	";

    $dyn_table = "<table border='1' id='patient_listing'  cellpadding='5' class='dataTables'>";
    $dyn_table .= "<thead>
              <tr>
              <th>CCC Number</th>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Gender </th>
              <th>Age</th>
              <th>Prep Reason</th>
              </tr>
              </thead>
              <tbody>";

    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    foreach ($results as $res) {

      $dyn_table .= "<tr><td>"
        . $res['patient_number_ccc'] . "</td><td>"
        . $res['first_name'] . "</td><td>"
        . $res['last_name'] . "</td><td>"
        . $res['gender'] . "</td><td>"
        . $res['age'] . "</td><td>"
        . $res['prep_reason'] . "</td></tr>";
    }

    $dyn_table .= "</tbody><tfoot></tfoot></table>";

    $data['dyn_table'] = $dyn_table;
    $data['from'] = date('d-M-Y', strtotime($period_start));
    $data['to'] = date('d-M-Y', strtotime($period_end));
    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "PREP Reasons Patients";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\reports\\prep_reasons_patients_v';
    echo view('\Modules\ADT\Views\\template', $data);
  }

  public function getDrugs()
  {
    $sql = "SELECT id, drug FROM drugcode WHERE enabled = '1'";
    $query = $this->db->query($sql);
    $results = $query->getResultArray();
    echo json_encode($results);
  }

  public function load_guidelines_view()
  {
    helper('filesystem');

    $dir = realpath($_SERVER['DOCUMENT_ROOT']);
    $files = directory_map($dir . '/adtv4/public/assets/guidelines/');

    $columns = array('#', 'File Name', 'Action');
    $tmpl = array('table_open' => '<table class="table table-bordered table-hover table-condensed table-striped dataTables" >');
    $this->table = new \CodeIgniter\View\Table();
    $this->table->setTemplate($tmpl);
    $this->table->setHeading($columns);

    foreach ($files as $file) {
      $links = "<a href='" . base_url() . "/public/assets/Guidelines/" . $file . "'target='_blank'>View</a>";
      $this->table->addRow("", $file, $links);
    }
    $data['guidelines_list'] = $this->table->generate();
    $data['hide_side_menu'] = 1;
    $data['selected_report_type_link'] = "guidelines_report_row";
    $data['selected_report_type'] = "List of Guidelines";
    $data['report_title'] = "List of Guidelines";
    $data['facility_name'] = session()->get('facility_name');
    $data['content_view'] = '\Modules\ADT\Views\\guidelines_listing_v';
    $this->base_params($data);
  }
}
