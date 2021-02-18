<h1 style="background: #2b597e; padding: 10px; color: white; box-shadow: 3px 3px 3px #000000; border-radius: 5px;">Patient Master List Generator</h1>

<div class="col-md-6 col-md-12" style="background: #EEEEEE; border-radius: 5px;">
    <style type="text/css">
        .tg  {border-collapse:collapse;border-spacing:0;border:none;margin:0px auto;}
        .tg td{font-family:Arial, sans-serif;font-size:14px;padding:8px 8px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;}
        .tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:8px 8px;border-style:solid;border-width:0px;overflow:hidden;word-break:normal;}

        @media screen and (max-width: 767px) {.tg {width: auto !important;}.tg col {width: auto !important;}.tg-wrap {overflow-x: auto;-webkit-overflow-scrolling: touch;margin: auto 0px;}}</style>


    <div class="tg-wrap">
        <form id="reportForm" method="POST" action="<?php echo base_url(); ?>patient_management/generateReport">
            <!--input type="checkbox" value="ALL" name="ALL" style="margin: 10px; "/> Apply No Filter-->
            <table class="tg">

                <tr>
                    <th class="tg-s6z2">
                        Start Date: <input type="text" class="form-control input-medium datePicker" id="fromDate" name="from" placeholder="FROM"/>
                        End Date: <input type="text" class="form-control input-medium datePicker" id="toDate" name="to" placeholder="TO"/></
                    <th>
                    <th class="tg-s6z2">

                    </th>
                </tr>

                <tr>
                    <td class="tg-s6z2">
                        <!--label>Enrollment Date</label-->
                        <input type="hidden" disabled="" style="height:30px !important;" class="form-control datePicker" id="date_enrolled" name="dateEnrolled" placeholder="Date Enrolled"/>


                    </td>
                    <td class="tg-s6z2">                    		
                    <td> Maturity:</td> <td><input type="radio" name="agegroup" value="Adult" ><span> Adult</span></td><td><input type="radio" name="agegroup" value="Paediatric"/ ><span> Paediatric</span></td>

                    </td>
                </tr>
                <tr>
                    <td class="tg-h0x1">
                        <select  name="gender" class="select2" >
                            <option value=""> - Gender - </option>
                            <option value="MALE">MALE</option>
                            <option value="FEMALE">FEMALE</option>
                        </select>

                    </td>
                    <td class="tg-h0x1">

                    <td> Smokes:</td> <td> <input type="radio" name="smokes" value="YES" ><span> Yes</span></td> <td>  <input type="radio" name="smokes" value="NO"/ ><span> No</span></td> 

                    </td>
                </tr>
                <tr>
                    <td class="tg-s6z2">
                        <select name="transferedFrom" class="select2" > 
                            <option value=""> - Transfered From - </option>
                            <?php foreach ($transfered as $t) { ?>
                                <option value="<?= $t->name; ?>"><?= $t->name; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td class="tg-s6z2">
                    <td>Drinks: </td><td> <input type="radio" name="alcohol" value="YES" ><span> Yes</span></td> <td>  <input type="radio" name="drinks" value="NO"/ ><span> No</span> </td> 

                    </td>
                </tr>
                <tr>
                    <td class="tg-h0x1">
                        <select name="service"  class="select2">
                            <option value=""> - Service - </option>
                            <?php foreach ($service as $t) { ?>
                                <option value="<?= $t->name; ?>"><?= $t->name; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td class="tg-h0x1">
                    <td> Pregnant:</td> <td>  <input type="radio" name="pregnant" value="YES" ><span> Yes</span> </td> <td> <input type="radio" name="pregnant" value="NO" ><span> No</span></td> 

                </tr>
                <tr>
                    <td class="tg-s6z2">
                        <select  name="startRegimen" class="select2">
                            <option value=""> - Regimen - </option>
                            <?php foreach ($startreg as $t) { ?>
                                <option value="<?= $t->regimen_desc; ?>"><?= $t->regimen_desc; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td class="tg-s6z2">
                    <td>Tb: </td><td> <input type="radio" name="tb" value="YES" ><span> Yes</span></td> <td>  <input type="radio" name="tb" value="NO" ><span> No</span> </td> 


                    </td>
                </tr>
                <tr>
                    <td class="tg-h0x1">
                        <select name="currentRegimen" class="select2" >
                            <option value=""> - Current Regimen - </option>
                            <?php foreach ($startreg as $t) { ?>
                                <option value="<?= $t->regimen_desc; ?>"><?= $t->regimen_desc; ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td class="tg-h0x1">
                    <td>Disclosure: </td><td> <input type="radio" name="disclosure" value="YES" ><span> Yes</span></td> <td>  <input type="radio" name="disclosure" value="NO" ><span> No</span> </td> 

                    </td>
                </tr>
                <tr>
                    <td class="tg-s6z2">
                        <select name="currentStatus" class="select2" >
                            <option value=""> - Current Status - </option>
                            <?php foreach ($currstat as $t) { ?>
                                <option value="<?= $t->Name; ?>"><?= strtoupper($t->Name); ?></option>
                            <?php } ?>
                        </select>
                    </td>
                    <td class="tg-s6z2">
                    <td>Differentiated Care</td><td><input type="radio" name="diffCare" value="notdifferentiated" ><span> Not Differentiated</span></td><td> <input type="radio" name="diffCare" value="ifferentiated" ><span> Differentiated</span></td>

                    </td>
                </tr>

                <tr>
                    <td class="tg-h0x1">
                        <input class="btn btn-md btn-primary" type="submit" id="genForm" value="Generate"/>
                    </td>
                    <td class="tg-h0x1"></td>
                </tr>

            </table>
        </form>
    </div>
</div>


<script>
    $(document).ready(function () {
  
        $('#genForm1111111111').click(function () {
            $.post("<?php echo base_url(); ?>patient_management/generateReport", $('#reportForm').serialize(), function () {

            });
        });

        $('.datePicker').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy-mm-dd'
        });
        $('.select2').select2();
    });
</script>