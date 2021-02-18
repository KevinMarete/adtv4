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
<div class="container" style="background-color: #fde8e7;border: solid thick #2b597e;padding: 30px; margin-top: 130px; margin-bottom: 130px; border-radius: 20px;">
    <a href="<?= base_url(); ?>/pqmp/0/0" class="btn btn-default" > Back </a>
    <div class="container">
        <div class="row">
            <div class="text-center">
                <img src="<?php echo base_url() . '/assets/images/top_logo.png'; ?>">
                <h4 style="color: #2e3092;">
                    MINISTRY OF HEALTH <br />
                    THE PHARMACY AND POISONS BOARD<br />
                    DEPARTMENT OF PHARMACOVIGILANCE<br />
                </h4>
                <h3 style="color:#3b8476;">FORM FOR REPORTING POOR QUALITY MEDICINAL PRODUCTS</h3>
            </div>
        </div>

    </div>
    <form name="pqmp-form" id="PQMS" method="POST"   action="<?= base_url(); ?>/inventory_management/save_pqm_for_synch">
        <center>UNIQUE ID: <input type="text" name="uniqueid" id="uniqueid" readonly class="form-control" value="<?= $uniqueid; ?>"></center>
        <table border="1" style="">
            <tr>
                <td colspan="2">Name of Facility : <input type="text" name="facility_name" id="facility_name" class="form-control" value="<?= $facility_name; ?>"> </td>
                <td colspan="3">County: <!--input type="text" name="district_name" id="district_name" class="form-control"-->
                    <select name="county_id" id="county_id" class="" id="PqmpCountyId">
                        <option value="<?php echo session()->get('county_id'); ?>" selected="selected"><?php echo session()->get('facility_county'); ?></option>
                    </select>
                </td>
                <td colspan="3">Sub County: <!--input type="text" name="province_name" id="province_name" class="form-control"-->
                    <select name="sub_county_id" id="sub_county_id" class="" id="PqmpSubCountyId">
                        <option value="<?php echo session()->get('subcounty_id'); ?>"><?php echo session()->get('facility_subcounty'); ?></option>                  
                    </select>

                </td>
            </tr>
            <tr>
                <td colspan="2">Facility Address: <input type="text" name="facility_address" id="facility_address" class="form-control" value="<?= $facility_address; ?>"></td>
                <td colspan="3">Facility Telephone: <input type="text" name="facility_phone" id="facility_phone" class="form-control" value="<?= $facility_phone; ?>"></td>
                <td colspan="3">Facility Code: <input type="text" name="facility_code" id="facility_code" class="form-control" value="<?= $facility_code; ?>"></td>
            </tr>
            <tr>
                <td colspan="8" align="center" style="background-color: lightblue;"><h3>PRODUCT IDENTITY</h3></td>
            </tr>
            <tr>
                <td>Brand Name</td>
                <td colspan="3"><input type="text" name="brand_name" id="brand_name" class="form-control"> </td>
                <td>Generic Name</td>

                <td colspan="3">
                    <input type="hidden" name="generic_name" id="GenName"/>
                    <select name="" class="fil" id="genname" id="genname">
                        <option value="">-Select Name-</option>
                        <?php foreach ($drug_data as $d) { ?>
                            <option value="<?php echo $d->id; ?>"><?php echo $d->drug; ?></option>
                        <?php } ?>
                    </select> 
                </td>
            </tr>
            <tr>
                <td>Batch/Lot Number</td>
                <td><input type="text" name="batch_no" id="batch_no" class="form-control"></td>
                <td>Date of Manufacture</td>
                <td><input type="text" name="manufacture_date" id="manufacture_date" class=""></td>
                <td>Date of Expiry</td>
                <td><input type="text" name="expiry_date" id="expiry_date" class=""></td>
                <td>Date of Receipt</td>
                <td><input type="text" name="receipt_date" id="receipt_date" class=""></td>
            </tr>
            <tr>
                <td>Name of Manufacturer</td>
                <td colspan="3"><input type="text" name="manufacturer_name" id="manufacturer_name" class="form-control"> </td>
                <td>Country of Origin</td>
                <td colspan="3"><!--input type="text" name="origin_county" id="origin_county" class="form-control"-->

                    <select name="country_id" id="country_id" class="fil" id="PqmpCountryId">
                        <option value=""></option>
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
                        <option value="115" selected="selected">Kenya</option>
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
                <td colspan="2"><input type="text" name="supplier_name" id="supplier_name" class="form-control"> </td>
                <td>Distributor/Supplier's Address</td>
                <td colspan="4"><input type="text" name="supplier_address" id="supplier_address" class="form-control"> </td>
            </tr>
            <tr>
                <td colspan="4" align="center" style="background-color:lightblue;"><h4>PRODUCT FORMULATION (Tick appropriate box)</h4></td>
                <td colspan="4" align="center" style="background-color: lightblue;"><h4>COMPLAINT (Tick appropriate box)</h4></td>
            </tr>
            <tr>
                <td colspan="4" align="">
                    <div>
                        <input name="product_formulation"  value="Oral tablets / capsules" type="radio">Oral tablets / capsules<br>
                        <input name="product_formulation"  value="Oral suspension / syrup" type="radio">Oral suspension / syrup<br>
                        <input name="product_formulation"  value="Injection" type="radio">Injection<br>
                        <input name="product_formulation"  value="Diluent" type="radio">Diluent<br>
                        <input name="product_formulation"  value="Powder for Reconstitution of Suspension" type="radio">Powder for Reconstitution of Suspensions<br>
                        <input name="product_formulation"  value="Powder for Reconstitution of Injection" type="radio">Powder for Reconstitution of Injection<br>
                        <input name="product_formulation"  value="Eye drops" type="radio">Eye drops<br>
                        <input name="product_formulation"  value="Ear drops" type="radio">Ear drops<br>
                        <input name="product_formulation"  value="Nebuliser solution" type="radio">Nebuliser solution<br>
                        <input name="product_formulation"  value="Cream / Ointment / Liniment / Paste" type="radio">Cream / Ointment / Liniment / Paste<br>
                        <input name="product_formulation"  value="Cream / Ointment / Liniment / Paste" type="radio">Other<br>
                        <input type="text" name="formulation_other" id="formulation_other">
                    </div>

                </td>
                <td colspan="4" align="">
                    <input type="text" name="spam" id="spam" style="background: #fde8e7; border-color: #fde8e7; width: 1px; height: 1px;"/><br>
                    <input type="checkbox" class="checkbox" name="colour_change" value="1"   id="PqmpComplaint" data-parsley-mincheck="2"/>Colour change <br>
                    <input type="checkbox"  class="checkbox"  name="separating" value="1"   id="PqmpComplaint"/>Separating	<br>
                    <input type="checkbox"  class="checkbox" name="powdering" value="1"    id="PqmpComplaint"/>Powdering / crumbling <br>
                    <input type="checkbox"  class="checkbox" name="caking" value="1"   id="PqmpComplaint"/>Caking<br>
                    <input type="checkbox"  class="checkbox" name="moulding"   value="1"  id="PqmpComplaint"/>Moulding <br>
                    <input type="checkbox"  class="checkbox" name="odour_change" value="1"   id="PqmpComplaint"/>Change of odour<br>
                    <input type="checkbox"  class="checkbox" name="mislabeling"  value="1"   id="PqmpComplaint"/>Mislabeling <br>
                    <input type="checkbox"  class="checkbox" name="incomplete_pack" value="1"    id="PqmpComplaint"/>Incomplete pack<br>
                    <input type="checkbox"  class="checkbox" name="complaint_other"  value="1"   id="PqmpComplaint"/>Other<br>
                    <input type="text" name="complaint_other_specify" value=""  id="complaint_other">


                </td>
            </tr>
            <tr>
                <td colspan="8">Describe in detail
                    <textarea name="description" id="description" style="width: 100%;"></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="3">Does the product require refrigiration?</td>
                <td><input type="radio" name="product_refrigiration" class="product_refrigiration" value="Yes">Yes</td>
                <td><input type="radio" name="product_refrigiration" class="product_refrigiration" value="No">No</td>
                <td colspan="3" rowspan="4"></td>
            </tr>
            <tr>
                <td colspan="3">Was product available at facility?</td>
                <td><input type="radio" name="product_availability" class="product_availability" value="Yes">Yes</td>
                <td><input type="radio" name="product_availability" class="product_availability" value="No">No</td>
            </tr>
            <tr>
                <td colspan="3">Was dispensed and returned by client?</td>
                <td><input type="radio" name="product_returned" class="product_returned" value="Yes">Yes</td>
                <td><input type="radio" name="product_returned" class="product_returned" value="No">No</td>
            </tr>
            <tr>
                <td colspan="3">Was product stored according to manufacturer/MOH recommendations?</td>
                <td><input type="radio" name="product_storage" class="product_storage" value="Yes">Yes</td>
                <td><input type="radio" name="product_storage" class="product_storage" value="No">No</td>
            </tr>
            <tr>
                <td colspan="8">Comments (if any)
                    <textarea style="width: 100%;" id="comments" name="comments"></textarea>
                </td>
            </tr>
            <tr>

                <td colspan="4">Name of reporter : <input type="text" name="reporter_name" id="reporter_name" value="<?= $user_full_name; ?>" class="form-control"> </td>
                <td colspan="4">Contact Number : <input type="text" name="reporter_phone" id="reporter_phone" value="<?= $user_phone; ?>" class="form-control"> </td>
            </tr>
            <tr>
                <td colspan="4">Cadre Job Title : <!--input type="text" name="reporter_title" id="reporter_title" class="form-control"--> 

                    <select name="designation_id" class="fil" id="designation_id">
                        <option value="1">physician</option>
                        <option value="2">pharmacist</option>
                        <option value="3" selected="selected">other professional</option>
                        <option value="5">consumer or other non health professional</option>
                    </select>
                </td>
                <td colspan="4">Reporter E-mail : <input type="text" name="reporter_signature" id="reporter_signature" value="<?= $user_email; ?>" class="form-control"> </td>
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
        <div class="mid-row">
            <input type="submit" class="btn btn-primary btn-sm" id="SUBMITDATA"  value="Submit">
            <button>cancel</button>
        </div>
    </form>
</div>
<script type="text/javascript">

    $(document).ready(function () {

        $("#manufacture_date,#expiry_date,#receipt_date").datepicker();

        $('.fil').select2();
        $('#genname').change(function () {
            var val = $(this).val();
            $('#GenName').val($('#genname option:selected').text());
            $.getJSON("<?= base_url(); ?>inventory_management/getDrugData/" + val, function (resp) {
                $('#batch_no').val(resp[0].batch_number);
                $('#expiry_date').val(resp[0].expiry_date);
                $('#receipt_date').val(resp[0].transaction_date);
            });
        });
        $("#PQMS").validate({
            rules: {
                facility_name: "required",
                county_id: "required",
                sub_county_id: "required",
                facility_address: "required",
                facility_phone: "required",
                facility_code: "required",
                brand_name: "required",
                genname: "required",
                batch_no: "required",
                manufacture_date: "required",
                expiry_date: "required",
                receipt_date: "required",
                manufacturer_name: "required",
                country_id: "required",
                supplier_name: "required",
                supplier_address: "required",
                product_formulation: {required: true, minlength: 1},
                spam: "required",
                description: "required",
                product_refrigiration: "required",
                product_availability: "required",
                product_returned: "required",
                product_storage: "required",
                reporter_name: "required",
                reporter_phone: "required",
                reporter_signature: "required"

            },
            messages: {
                facility_name: "Please enter facility name",
                county_id: "Please select county",
                sub_county_id: "Please select sub county",
                facility_address: "Please enter facility address",
                facility_phone: "Please enter facility phone",
                facility_code: "Please enter facility code",
                brand_name: "Please enter  brand name",
                genname: "Please enter  generic name",
                batch_no: "Please enter  batch no",
                manufacture_date: "Please enter mfg date",
                expiry_date: "Please enter expiry date",
                receipt_date: "Please enter receipt date",
                country_id: "Please select country",
                manufacturer_name: "Please enter Manufacturer",
                supplier_name: "Please enter supplier name",
                supplier_address: "Please enter supplier address",
                product_formulation: "Please select atleast 1",
                PqmpComplaint: "Please select atleast 1",
                spam: "Please select atleast 1",
                description: "Please enter description",
                product_refrigiration: "Please select one",
                product_availability: "Please select one",
                product_returned: "Please select one",
                product_storage: "Please select one",
                reporter_name: "Please enter name",
                reporter_phone: "Please enter phone",
                reporter_signature: "Please signature",
            }
        });
        $('#spam').val("");
        $('.checkbox').click(function () {
            if ($('.checkbox:checked').length < 1) {
                $('#spam').val("");
            } else {
                $('#spam').val("1");
            }

        });

        $.validator.setDefaults({
            submitHandler: function () {
                alert("submitted!");
            }
        });
    });



</script>