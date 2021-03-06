<style type="text/css">
  .spinner{
    display: block;
    margin-left: auto;
    margin-right: auto;
    width: 80px;
    height:80px;
  }
  .icon-download{
    color: #fff !important;
    margin: 9px;
    }
</style>

<div class="full-content container">
    <div class="row-fluid">
		<?php $this->load->view("reports/reports_top_menus_v");?>
	</div>
   <div class="row-fluid">
   	<div class="span6">
   		<h3>Overview <a href="<?= current_url()?>/download"><i class="icon-download pull-right"></i></a></h3>
   		<div id="overview">
       <img class="spinner">
      </div>
   	</div>
   	<div class="span6">
   		<h3>ART vs Non-ART <a href="<?= current_url()?>/download"><i class="icon-download pull-right"></i></a></h3>
   		<div id="service">
       <img class="spinner"> 
      </div>
   	</div>
   </div>
   <div class="row-fluid">
   	<div class="span6">
   		<h3>Male vs Female <a href="<?= current_url()?>/download"><i class="icon-download pull-right"></i></a></h3>
   		<div id="gender">
       <img class="spinner"> 
      </div>
   	</div>
   	<div class="span6">
   		<h3>Age <a href="<?= current_url()?>/download"><i class="icon-download pull-right"></i></a></h3>
   		<div id="age">
       <img class="spinner"> 
      </div>
   	</div>
   </div>
</div>


<script src="<?= base_url(); ?>assets/scripts/highcharts/highcharts.js"></script>
<script src="<?= base_url(); ?>assets/scripts/highcharts/modules/exporting.js"></script>
<script src="<?= base_url(); ?>assets/scripts/highcharts/modules/offline-exporting.js"></script>
<script src="<?= base_url(); ?>assets/scripts/highcharts/modules/export-csv.js"></script>

<!--custom script-->
<script type='text/javascript'>
    $(function(){
      //Add Spinner image source
      $('.spinner').attr('src',"<?php echo asset_url().'images/loading_spin.gif';?>");
      //Loop through Charts
    	var charts = ["overview","service","gender","age"];
    	$.each(charts,function(i,v){
    		var url = "<?php echo base_url().'report_management/getAdherence/'.$type.'/'.$start_date.'/'.$end_date.'/'; ?>"+v;
    		//Load charts
   	    load_charts(v,url);
    	});
    });


   function load_charts(div,url){
   	 //Load onto div
   	 $("#"+div).load(url);
   }
</script>