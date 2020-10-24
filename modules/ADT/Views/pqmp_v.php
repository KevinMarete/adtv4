<?php
//print_r(@$pqmp_data);
?>
<style type="text/css">
    /*@media (min-width: 992px)*/
    /*.modal-lg {*/
    /*width: 900px;*/
    /*}*/
    table{
        width: 100%; border-color: #0697d9;
    }

    td{
        padding: 4px;
    }
    input{
        border: solid thin #fff !important;
        max-width: 150px !important;
    }
</style>
<div class="container" style="background-color: #fde8e7;border: solid thick #2b597e;padding: 30px; margin-top: 130px; margin-bottom: 130px;border-radius: 20px;">
    <a href="<?= base_url().'/public/'; ?>pqmp/0/0" class="btn btn-default" > Back </a>
 


    <a href="<?= base_url().'/public/'; ?>inventory_management/export_pqmp/<?= $record_no; ?>/export" class="btn btn-default" > Export(.xls) </a>
    <div class="container">
        <div class="row">
            <div class="text-center">
                <img src="../../assets/images/top_logo.png">
                <h4 style="color: #2e3092;">
                    MINISTRY OF HEALTH <br />
                    THE PHARMACY AND POISONS BOARD<br />
                    DEPARTMENT OF PHARMACOVIGILANCE<br />
                </h4>
                <h3 style="color:#3b8476;">FORM FOR REPORTING POOR QUALITY MEDICINAL PRODUCTS</h3>
            </div>
        </div>
    </div>
    <form name="pqmp-form" id="pqmp-form" method="POST" action="<?= base_url().'/public/'; ?>inventory_management/pqmp/<?= $record_no; ?>">
        <table border="1" style="">
            <tr>
                <td colspan="2">Name of Facility : <input type="text" name="facility_name" id="facility_name" class="form-control" value="<?= @$pqmp_data[0]['facility_name']; ?>"> </td>
                <td colspan="3">County: <!--input type="text" name="district_name" id="district_name" class="form-control"-->
                    <select name="county_id" id="county_id" class="" id="PqmpCountyId">
                        <option value="<?php echo session()->get('county_id'); ?>" selected="selected"><?php echo session()->get('facility_county'); ?></option>
                    </select>
                </td>
                <td colspan="3">Sub County: <!--input type="text" name="province_name" id="province_name" class="form-control"-->
                    <select name="sub_county_id" id="sub_county_id" class="" id="PqmpSubCountyId">
                        <option value="<?php echo @session()->get('subcounty_id'); ?>"><?php echo @session()->get('facility_subcounty'); ?></option>                  
                    </select>

                </td>
            </tr>
            <tr>
                <td colspan="2">Facility Address: <input type="text" name="facility_address" id="facility_address" class="form-control" value="<?= @$pqmp_data[0]['facility_address']; ?>"></td>
                <td colspan="3">Facility Telephone: <input type="text" name="facility_phone" id="facility_phone" class="form-control" value="<?= @$pqmp_data[0]['facility_phone']; ?>"></td>
                <td colspan="3">Facility Code: <input type="text" name="facility_code" id="facility_code" class="form-control" value="<?= @$pqmp_data[0]['facility_code']; ?>"></td>
            </tr>
            <tr>
                <td colspan="8" align="center" style="background-color: lightblue;"><h3>PRODUCT IDENTITY</h3></td>
            </tr>
            <tr>
                <td>Brand Name</td>
                <td colspan="3"><input type="text" name="brand_name" id="brand_name" value="<?= @$pqmp_data[0]['brand_name']; ?>" class="form-control"> </td>
                <td>Generic Name</td>
                <td colspan="3"><input type="text" name="generic_name" id="generic_name" value="<?= @$pqmp_data[0]['generic_name']; ?>" class="form-control"> </td>
            </tr>
            <tr>
                <td>Batch/Lot Number</td>
                <td><input type="text" name="batch_no" id="batch_no" value="<?= @$pqmp_data[0]['batch_number']; ?>" class="form-control"></td>
                <td>Date of Manufacture</td>
                <td><input type="text" name="manufacture_date" value="<?= @$pqmp_data[0]['manufacture_date']; ?>" id="manufacture_date" class=""></td>
                <td>Date of Expiry</td>
                <td><input type="text" name="expiry_date" value="<?= @$pqmp_data[0]['expiry_date']; ?>" id="expiry_date" class=""></td>
                <td>Date of Receipt</td>
                <td><input type="text" name="receipt_date" value="<?= @$pqmp_data[0]['receipt_date']; ?>" id="receipt_date" class=""></td>
            </tr>
            <tr>
                <td>Name of Manufacturer</td>
                <td colspan="3"><input type="text" name="manufacturer_name" value="<?= @$pqmp_data[0]['name_of_manufacturer']; ?>" id="manufacturer_name" class="form-control"> </td>
                <td>Country of Origin</td>
                <td colspan="3">
                    <select name="country_id" id="country_id" class="fil" id="PqmpCountryId">
                        <option value="<?= @$pqmp_data[0]['country_of_origin']; ?>" selected><?= @$pqmp_data[0]['country']; ?></option>
                        <option value="1">Andorra</option>
                        <option value="2">United Arab Emirates</option>
                        <option value="3">Afghanistan</option>
                        <option value="4">Antigua and Barbuda</option>
                        <option value="5">Anguilla</option>
                        <option value="6">Albania</option>
                        <option value="7">Armenia</option>
                        <option value="8">Angola</option>
                        <option value="9">Antarctica</option>
                        <option value="10">Argentina</option>
                        <option value="11">American Samoa</option>
                        <option value="12">Austria</option>
                        <option value="13">Australia</option>
                        <option value="14">Aruba</option>
                        <option value="16">Azerbaijan</option>
                        <option value="17">Bosnia and Herzegovina</option>
                        <option value="18">Barbados</option>
                        <option value="19">Bangladesh</option>
                        <option value="20">Belgium</option>
                        <option value="21">Burkina Faso</option>
                        <option value="22">Bulgaria</option>
                        <option value="23">Bahrain</option>
                        <option value="24">Burundi</option>
                        <option value="25">Benin</option>
                        <option value="26">Saint BarthÃ©lemy</option>
                        <option value="27">Bermuda</option>
                        <option value="28">Brunei Darussalam</option>
                        <option value="29">Bolivia</option>
                        <option value="30">Caribbean Netherlands </option>
                        <option value="31">Brazil</option>
                        <option value="32">Bahamas</option>
                        <option value="33">Bhutan</option>
                        <option value="34">Bouvet Island</option>
                        <option value="35">Botswana</option>
                        <option value="36">Belarus</option>
                        <option value="37">Belize</option>
                        <option value="38">Canada</option>
                        <option value="39">Cocos (Keeling) Islands</option>
                        <option value="40">Congo, Democratic Republic of</option>
                        <option value="41">Central African Republic</option>
                        <option value="42">Congo</option>
                        <option value="43">Switzerland</option>
                        <option value="44">CÃ´te Dâ€™Ivoire</option>
                        <option value="45">Cook Islands</option>
                        <option value="46">Chile</option>
                        <option value="47">Cameroon</option>
                        <option value="48">China</option>
                        <option value="49">Colombia</option>
                        <option value="50">Costa Rica</option>
                        <option value="51">Cuba</option>
                        <option value="52">Cape Verde</option>
                        <option value="53">CuraÃ§ao</option>
                        <option value="54">Christmas Island</option>
                        <option value="55">Cyprus</option>
                        <option value="56">Czech Republic</option>
                        <option value="57">Germany</option>
                        <option value="58">Djibouti</option>
                        <option value="59">Denmark</option>
                        <option value="60">Dominica</option>
                        <option value="61">Dominican Republic</option>
                        <option value="62">Algeria</option>
                        <option value="63">Ecuador</option>
                        <option value="64">Estonia</option>
                        <option value="65">Egypt</option>
                        <option value="66">Western Sahara</option>
                        <option value="67">Eritrea</option>
                        <option value="68">Spain</option>
                        <option value="69">Ethiopia</option>
                        <option value="70">Finland</option>
                        <option value="71">Fiji</option>
                        <option value="72">Falkland Islands</option>
                        <option value="73">Micronesia, Federated States of</option>
                        <option value="74">Faroe Islands</option>
                        <option value="75">France</option>
                        <option value="76">Gabon</option>
                        <option value="77">United Kingdom</option>
                        <option value="78">Grenada</option>
                        <option value="79">Georgia</option>
                        <option value="80">French Guiana</option>
                        <option value="81">Guernsey</option>
                        <option value="82">Ghana</option>
                        <option value="83">Gibraltar</option>
                        <option value="84">Greenland</option>
                        <option value="85">Gambia</option>
                        <option value="86">Guinea</option>
                        <option value="87">Guadeloupe</option>
                        <option value="88">Equatorial Guinea</option>
                        <option value="89">Greece</option>
                        <option value="90">South Georgia and the South Sandwich Islands</option>
                        <option value="91">Guatemala</option>
                        <option value="92">Guam</option>
                        <option value="93">Guinea-Bissau</option>
                        <option value="94">Guyana</option>
                        <option value="95">Hong Kong</option>
                        <option value="96">Heard and McDonald Islands</option>
                        <option value="97">Honduras</option>
                        <option value="98">Croatia</option>
                        <option value="99">Haiti</option>
                        <option value="100">Hungary</option>
                        <option value="101">Indonesia</option>
                        <option value="102">Ireland</option>
                        <option value="103">Israel</option>
                        <option value="104">Isle of Man</option>
                        <option value="105">India</option>
                        <option value="106">British Indian Ocean Territory</option>
                        <option value="107">Iraq</option>
                        <option value="108">Iran</option>
                        <option value="109">Iceland</option>
                        <option value="110">Italy</option>
                        <option value="111">Jersey</option>
                        <option value="112">Jamaica</option>
                        <option value="113">Jordan</option>
                        <option value="114">Japan</option>
                        <option value="115">Kenya</option>
                        <option value="116">Kyrgyzstan</option>
                        <option value="117">Cambodia</option>
                        <option value="118">Kiribati</option>
                        <option value="119">Comoros</option>
                        <option value="120">Saint Kitts and Nevis</option>
                        <option value="121">North Korea</option>
                        <option value="122">South Korea</option>
                        <option value="123">Kuwait</option>
                        <option value="124">Cayman Islands</option>
                        <option value="125">Kazakhstan</option>
                        <option value="126">Lao Peopleâ€™s Democratic Republic</option>
                        <option value="127">Lebanon</option>
                        <option value="128">Saint Lucia</option>
                        <option value="129">Liechtenstein</option>
                        <option value="130">Sri Lanka</option>
                        <option value="131">Liberia</option>
                        <option value="132">Lesotho</option>
                        <option value="133">Lithuania</option>
                        <option value="134">Luxembourg</option>
                        <option value="135">Latvia</option>
                        <option value="136">Libya</option>
                        <option value="137">Morocco</option>
                        <option value="138">Monaco</option>
                        <option value="139">Moldova</option>
                        <option value="140">Montenegro</option>
                        <option value="141">Saint-Martin (France)</option>
                        <option value="142">Madagascar</option>
                        <option value="143">Marshall Islands</option>
                        <option value="144">Macedonia</option>
                        <option value="145">Mali</option>
                        <option value="146">Myanmar</option>
                        <option value="147">Mongolia</option>
                        <option value="148">Macau</option>
                        <option value="149">Northern Mariana Islands</option>
                        <option value="150">Martinique</option>
                        <option value="151">Mauritania</option>
                        <option value="152">Montserrat</option>
                        <option value="153">Malta</option>
                        <option value="154">Mauritius</option>
                        <option value="155">Maldives</option>
                        <option value="156">Malawi</option>
                        <option value="157">Mexico</option>
                        <option value="158">Malaysia</option>
                        <option value="159">Mozambique</option>
                        <option value="160">Namibia</option>
                        <option value="161">New Caledonia</option>
                        <option value="162">Niger</option>
                        <option value="163">Norfolk Island</option>
                        <option value="164">Nigeria</option>
                        <option value="165">Nicaragua</option>
                        <option value="166">The Netherlands</option>
                        <option value="167">Norway</option>
                        <option value="168">Nepal</option>
                        <option value="169">Nauru</option>
                        <option value="170">Niue</option>
                        <option value="171">New Zealand</option>
                        <option value="172">Oman</option>
                        <option value="173">Panama</option>
                        <option value="174">Peru</option>
                        <option value="175">French Polynesia</option>
                        <option value="176">Papua New Guinea</option>
                        <option value="177">Philippines</option>
                        <option value="178">Pakistan</option>
                        <option value="179">Poland</option>
                        <option value="180">St. Pierre and Miquelon</option>
                        <option value="181">Pitcairn</option>
                        <option value="182">Puerto Rico</option>
                        <option value="183">Palestinian Territory, Occupied</option>
                        <option value="184">Portugal</option>
                        <option value="185">Palau</option>
                        <option value="186">Paraguay</option>
                        <option value="187">Qatar</option>
                        <option value="188">Reunion</option>
                        <option value="189">Romania</option>
                        <option value="190">Serbia</option>
                        <option value="191">Russian Federation</option>
                        <option value="192">Rwanda</option>
                        <option value="193">Saudi Arabia</option>
                        <option value="194">Solomon Islands</option>
                        <option value="195">Seychelles</option>
                        <option value="196">Sudan</option>
                        <option value="197">Sweden</option>
                        <option value="198">Singapore</option>
                        <option value="199">Saint Helena</option>
                        <option value="200">Slovenia</option>
                        <option value="201">Svalbard and Jan Mayen Islands</option>
                        <option value="202">Slovakia (Slovak Republic)</option>
                        <option value="203">Sierra Leone</option>
                        <option value="204">San Marino</option>
                        <option value="205">Senegal</option>
                        <option value="206">Somalia</option>
                        <option value="207">Suriname</option>
                        <option value="208">South Sudan</option>
                        <option value="209">Sao Tome and Principe</option>
                        <option value="210">El Salvador</option>
                        <option value="211">Saint-Martin (Pays-Bas)</option>
                        <option value="212">Syria</option>
                        <option value="213">Swaziland</option>
                        <option value="214">Turks and Caicos Islands</option>
                        <option value="215">Chad</option>
                        <option value="216">French Southern Territories</option>
                        <option value="217">Togo</option>
                        <option value="218">Thailand</option>
                        <option value="219">Tajikistan</option>
                        <option value="220">Tokelau</option>
                        <option value="221">Timor-Leste</option>
                        <option value="222">Turkmenistan</option>
                        <option value="223">Tunisia</option>
                        <option value="224">Tonga</option>
                        <option value="225">Turkey</option>
                        <option value="226">Trinidad and Tobago</option>
                        <option value="227">Tuvalu</option>
                        <option value="228">Taiwan</option>
                        <option value="229">Tanzania</option>
                        <option value="230">Ukraine</option>
                        <option value="231">Uganda</option>
                        <option value="232">United States Minor Outlying Islands</option>
                        <option value="233">United States</option>
                        <option value="234">Uruguay</option>
                        <option value="235">Uzbekistan</option>
                        <option value="236">Vatican</option>
                        <option value="237">Saint Vincent and the Grenadines</option>
                        <option value="238">Venezuela</option>
                        <option value="239">Virgin Islands (British)</option>
                        <option value="240">Virgin Islands (U.S.)</option>
                        <option value="241">Vietnam</option>
                        <option value="242">Vanuatu</option>
                        <option value="243">Wallis and Futuna Islands</option>
                        <option value="244">Samoa</option>
                        <option value="245">Yemen</option>
                        <option value="246">Mayotte</option>
                        <option value="247">South Africa</option>
                        <option value="248">Zambia</option>
                        <option value="249">Zimbabwe</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Name of Distributor/Supplier</td>
                <td colspan="2"><input type="text" name="supplier_name" value="<?= @$pqmp_data[0]['supplier_name']; ?>" id="supplier_name" class="form-control"> </td>
                <td>Distributor/Supplier's Address</td>
                <td colspan="4"><input type="text" name="supplier_address" value="<?= @$pqmp_data[0]['supplier_address']; ?>" id="supplier_address" class="form-control"> </td>
            </tr>
            <tr>
                <td colspan="4" align="center" style="background-color:lightblue;"><h4>PRODUCT FORMULATION (Tick appropriate box)</h4></td>
                <td colspan="4" align="center" style="background-color: lightblue;"><h4>COMPLAINT (Tick appropriate box)</h4></td>
            </tr>
            <tr>
                <td colspan="4" align="">
                    <input name="product_formulation"  value="Oral tablets / capsules" <?php
                    if (@$pqmp_data[0]['product_formulation'] == 'Oral tablets / capsules') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?> type="radio">Oral tablets / capsules<br>
                    <input name="product_formulation"  value="Oral suspension / syrup" <?php
                    if (@$pqmp_data[0]['product_formulation'] == 'Oral suspension / syrup') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?> type="radio">Oral suspension / syrup<br>
                    <input name="product_formulation"  value="Injection" <?php
                    if (@$pqmp_data[0]['product_formulation'] == 'Injection') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?> type="radio">Injection<br>
                    <input name="product_formulation"  value="Diluent" <?php
                    if (@$pqmp_data[0]['product_formulation'] == 'Diluent') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?> type="radio">Diluent<br>
                    <input name="product_formulation"  value="Powder for Reconstitution of Suspension" <?php
                    if (@$pqmp_data[0]['product_formulation'] == 'Powder for Reconstitution of Suspension') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?> type="radio">Powder for Reconstitution of Suspensions<br>
                    <input name="product_formulation"  value="Powder for Reconstitution of Injection" <?php
                    if (@$pqmp_data[0]['product_formulation'] == 'Powder for Reconstitution of Injection') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?> type="radio">Powder for Reconstitution of Injection<br>
                    <input name="product_formulation"  value="Eye drops" <?php
                    if (@$pqmp_data[0]['product_formulation'] == 'Eye drops') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?> type="radio">Eye drops<br>
                    <input name="product_formulation"  value="Ear drops" <?php
                    if (@$pqmp_data[0]['product_formulation'] == 'Ear drops') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?> type="radio">Ear drops<br>
                    <input name="product_formulation"  value="Nebuliser solution" <?php
                    if (@$pqmp_data[0]['product_formulation'] == 'Nebuliser solution') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?> type="radio">Nebuliser solution<br>
                    <input name="product_formulation"  value="Cream / Ointment / Liniment / Paste" <?php
                    if (@$pqmp_data[0]['product_formulation'] == 'Cream / Ointment / Liniment / Paste') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?> type="radio">Cream / Ointment / Liniment / Paste<br>
                    <input name="product_formulation"  value="Cream / Ointment / Liniment / Paste" <?php
                    if (@$pqmp_data[0]['product_formulation'] == 'Cream / Ointment / Liniment / Paste') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?> type="radio">Other<br>
                    <input type="text" name="formulation_other" id="formulation_other" value="<?= @$pqmp_data[0]['product_formulation_specify'] ?>">

                </td>
                <td colspan="4" align="">
                    <input type="checkbox" name="colour_change" <?php
                    if (@$pqmp_data[0]['colour_change'] == '1') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?> value="1">Colour Change <br />
                    <input type="checkbox" name="separating" <?php
                    if (@$pqmp_data[0]['separating'] == '1') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?> value="1">Separating <br />       
                    <input type="checkbox" name="powdering" <?php
                    if (@$pqmp_data[0]['powdering'] == '1') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?>value="1">Powdering/crumbling<br />
                    <input type="checkbox" name="caking" <?php
                    if (@$pqmp_data[0]['caking'] == '1') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?>value="1">Caking <br />
                    <input type="checkbox" name="moulding" <?php
                    if (@$pqmp_data[0]['moulding'] == '1') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?> value="1">Moulding <br />
                    <input type="checkbox" name="odour_change" <?php
                    if (@$pqmp_data[0]['odour_change'] == '1') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?> value="1">Change of oduor <br />
                    <input type="checkbox" name="mislabeling" <?php
                    if (@$pqmp_data[0]['mislabeling'] == '1') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?>value="1">Mislabeilng <br />
                    <input type="checkbox" name="incomplete_pack" <?php
                    if (@$pqmp_data[0]['incomplete_pack'] == '1') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?>value="1">Incomplete pack <br />
                    <input type="checkbox" name="complaint_other" <?php
                    if (@$pqmp_data[0]['complaint_other'] == '1') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?>value="1">Other
                    <input type="text" name="complaint_other_specify" id="complaint_other" value="<?= @$pqmp_data[0]['complaint_other_specify'] ?>">


                </td>
            </tr>
            <tr>
                <td colspan="8">Describe in detail
                    <textarea name="description" id="description" style="width: 100%;"><?= @$pqmp_data[0]['complaint_description'] ?></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="3">Does the product require refrigiration?</td>
                <td><input type="radio" name="product_refrigiration" class="product_refrigiration" <?php
                    if (@$pqmp_data[0]['require_refrigeration'] == 'Yes') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?> value="Yes">Yes</td>
                <td><input type="radio" name="product_refrigiration" class="product_refrigiration" <?php
                    if (@$pqmp_data[0]['require_refrigeration'] == 'No') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?> value="No">No</td>
                <td colspan="3" rowspan="4"></td>
            </tr>
            <tr>
                <td colspan="3">Was product available at facility?</td>
                <td><input type="radio" name="product_availability" class="product_availability" <?php
                    if (@$pqmp_data[0]['product_at_facility'] == 'Yes') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?>value="Yes">Yes</td>
                <td><input type="radio" name="product_availability" class="product_availability" <?php
                    if (@$pqmp_data[0]['product_at_facility'] == 'No') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?>value="No">No</td>
            </tr>
            <tr>
                <td colspan="3">Was dispensed and returned by client?</td>
                <td><input type="radio" name="product_returned" class="product_returned" <?php
                    if (@$pqmp_data[0]['returned_by_client'] == 'Yes') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?>value="Yes">Yes</td>
                <td><input type="radio" name="product_returned" class="product_returned" <?php
                    if (@$pqmp_data[0]['returned_by_client'] == 'No') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?>value="No">No</td>
            </tr>
            <tr>
                <td colspan="3">Was product stored according to manufacturer/MOH recommendations?</td>
                <td><input type="radio" name="product_storage" class="product_storage" <?php
                    if (@$pqmp_data[0]['stored_to_recommendations'] == 'Yes') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?>value="Yes">Yes</td>
                <td><input type="radio" name="product_storage" class="product_storage" <?php
                    if (@$pqmp_data[0]['stored_to_recommendations'] == 'No') {
                        echo ' checked ';
                    } else {
                        
                    }
                    ?>value="No">No</td>
            </tr>
            <tr>
                <td colspan="8">Comments (if any)
                    <textarea style="width: 100%;" id="comments" name="comments"> <?= @$pqmp_data[0]['comments'] ?></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="4">Name of reporter : <input type="text" name="reporter_name" id="reporter_name" value="<?= @$pqmp_data[0]['reporter_name'] ?>" class="form-control"> </td>
                <td colspan="4">Contact Number : <input type="text" name="reporter_phone" id="reporter_phone" value="<?= @$pqmp_data[0]['contact_number'] ?>" class="form-control"> </td>
            </tr>
            <tr>
                <td colspan="4">Cadre Job Title : 
                    <select name="designation_id" class="fil" id="designation_id">
                        <option value="<?= @$pqmp_data[0]['designation_id'] ?>"><?= @$pqmp_data[0]['designation'] ?></option>
                        <option value="1">physician</option>
                        <option value="2">pharmacist</option>
                        <option value="3" selected="selected">other professional</option>
                        <option value="5">consumer or other non health professional</option>
                    </select>
                </td>
                <td colspan="4">Signature : <input type="text" name="reporter_signature" id="reporter_signature" value="<?= @$pqmp_data[0]['reporter_name'] ?>" class="form-control"> </td>
            </tr>
            <tr>
                <td colspan="8" align="center" style="background-color: lightblue;"><h5>Once completed one copy of this form should be emailed or posted to:</h5></td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2"></td>
                <td colspan="2"></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2"></td>
                <td colspan="2"></td>
                <td colspan="2"></td>
            </tr>
            <tr>
                <td colspan="8" align="center" style="background-color: lightblue;"><span style="color: red;">Your support in this PHARMACOVIGILANCE program is appreciated </span><br /></td>
            </tr>

        </table>
        <div class="mid-row" >
            <?php if (@$pqmp_data[0]['synch'] === '0') { ?>
                <input type="submit" class="btn btn-primary" value="Submit"/>
            <?php } ?>


        </div>
    </form>
</div>
<script type="text/javascript">

    $(document).ready(function () {


        $("#manufacture_date,#expiry_date,#receipt_date").datepicker({dateFormat: "yy-mm-dd"});
        $('.fil').select2();


        $('.delete-form').click(function (e) {
            var answer = confirm('Deleting ADR. Are You Sure?');
            if (answer) {

            } else {
                e.preventDefault();
            }
        });
    });

</script>