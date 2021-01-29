<?php
$ccc_stores = session()->get('ccc_store');
$first_load = $ccc_stores[0]->id; //Which store to load first
?>
<style type="text/css">
    .dataTable {
        letter-spacing:0px;
    }
    table.dataTable{
        zoom:1;	
    }

    .table-bordered input{
        width:9em;
    }

    td {
        white-space: nowrap;
        overflow: hidden;         /* <- this does seem to be required */
        text-overflow: ellipsis;
    }
</style>
<script type="text/javascript">

    $(document).ready(function () {
        loadData('<?php echo $first_load; ?>');

        /* Filter immediately after updating or saving */
        $(".store_inventory").live("click", function () {
            $(".store_inventory").removeClass('active');
            $(this).addClass("active");
            var id = $(this).attr('id');
            loadData(id);
        });

    });

    function loadData(stock_type) {
        base_url="<?php echo base_url();?>"
        var _url = base_url + "/inventory_management/stock_listing/";
        var storeTable = $('#store_table').dataTable({
            processing: true,
			serverSide: true,
			ajax: _url+stock_type,
			destroy: true,
            pagination: "full_numbers",
            stateSave: true,
			columnDefs: [{
				'searchable': false,
				'targets': [2]
			}]
        });
    }
</script>
<?php
if (session()->get("inventory_go_back")) {

    if (session()->get("inventory_go_back") == "store_table") {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#pharmacy_btn").removeClass();
                $(this).addClass("active");
                $("#pharmacy_table").hide();
                $("#pharmacy_table_wrapper").hide();
                $("#store_table").show();
                $("#store_table_wrapper").show();

            });
        </script>

        <?php
    } else if (session()->get("inventory_go_back") == "pharmacy_table") {
        ?>
        <script type="text/javascript">
            $(document).ready(function () {
                $("#store_btn").removeClass();
                $("#pharmacy_btn").addClass("active");
                $("#store_table").hide();
                $("#store_table_wrapper").css("display", "none");
                $("#pharmacy_table").show();
                $("#pharmacy_table_wrapper").show();

            });
        </script>

        <?php
    }
} else {
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $("#pharmacy_btn").removeClass();
            $(this).addClass("active");
            $("#pharmacy_table").hide();
            $("#pharmacy_table_wrapper").hide();
            $("#store_table").show();
            $("#store_table_wrapper").show();

        });
    </script>
    <?php
}
?>

<?php
session()->remove("inventory_go_back");
$access_level = session()->get('user_indicator');
$user_is_administrator = false;
$user_is_nascop = false;
$user_is_pharmacist = false;
$user_is_facilityadmin = false;

if ($access_level == "system_administrator") {
    $user_is_administrator = true;
}
if ($access_level == "pharmacist") {
    $user_is_pharmacist = true;
}
if ($access_level == "nascop_staff") {
    $user_is_nascop = true;
}
if ($access_level == "facility_administrator") {
    $user_is_facilityadmin = true;
}
?>

<div class="main-content">

    <div class="center-content">

        <div>
            <?php if (session()->get("msg_save_transaction")) {
                ?>


                <?php
                if (session()->get("msg_save_transaction") == "success") {
                    ?>
                    <p class=""><span class="message success">Your data were successfully saved !</span></p>
                    <?php
                } else {
                    ?>
                    <p class=""><span class="message error">Your data were not saved ! Try again or contact your system administrator.</span></p>
                    <?php
                }
                session()->remove('msg_save_transaction');
            }
            ?>
        </div>

        <ul class="nav nav-tabs nav-pills">
            <?php
            $x = 0;
            $class = 'store_inventory ';
            foreach ($ccc_stores as $ccc_store) {
                $name = $ccc_store->name;
                $id = $ccc_store->id;
                if ($x == 0) {
                    $class .= 'active';
                    $x++;
                } else {
                    $class = 'store_inventory ';
                }
                echo '<li id="' . $id . '" class="' . $class . '"><a  href="#">' . str_replace('(store)', '', str_replace('(pharmacy)', '', $name)) . '</a> </li>';
            }
            ?> 
        </ul> 
        <div class="table-responsive">
            <table id="store_table" class="listing_table table table-bordered table-hover table-condensed table" style="width: 100% !important">
                <thead>
                    <tr>
                        <th>Commodity</th>
                        <th>Generic Name</th>
                        <th>QTY/SOH</th>
                        <th>Unit</th>
                        <th>Pack Size</th>
                        <th>Supplier</th>
                        <th>Dose</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
