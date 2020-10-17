<?php

namespace Modules\ADT\Controllers;

use \Modules\Tables\Controllers\Tables;
use \Modules\Template\Controllers\Template;
use \Modules\ADT\Models\CCC_store_service_point;
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
    $facility_code = $this->session->userdata('facility');
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

    $services_data = Regimen_Service_Type::getHydratedAll();
    foreach ($services_data as $service) {
      $services[] = $service['Name'];
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
        $results = $query->result_array();
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
    $data['graphs'] = $this->load->view('graph_v', $data, TRUE);
    $data['title'] = "webADT | Reports";
    $data['hide_side_menu'] = 1;
    $data['banner_text'] = "Facility Reports";
    $data['selected_report_type_link'] = "standard_report_row";
    $data['selected_report_type'] = "Standard Reports";
    $data['report_title'] = "Graph of Number of Patients Enrolled Per Month in a Given Year";
    $data['facility_name'] = $this->session->userdata('facility_name');
    $data['content_view'] = 'reports/graphs_on_patients_v';
    $this->load->view('template', $data);
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
