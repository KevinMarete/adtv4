<?php $session = session(); ?>
<style type="text/css">
    #ui-datepicker-div{
        zoom:1;
    }
    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        padding-top: 100px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
        position: relative;
        background-color: #fefefe;
        margin: auto;
        padding: 0;
        border: 1px solid #888;
        width: 80%;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
        -webkit-animation-name: animatetop;
        -webkit-animation-duration: 0.4s;
        animation-name: animatetop;
        animation-duration: 0.4s
    }

    /* Add Animation */
    @-webkit-keyframes animatetop {
        from {top:-300px; opacity:0} 
        to {top:0; opacity:1}
    }

    @keyframes animatetop {
        from {top:-300px; opacity:0}
        to {top:0; opacity:1}
    }

    /* The Close Button */
    .close {
        color: white;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    .modal-header {
        padding: 2px 16px;
        background-color: #5cb85c;
        color: white;
    }

    .modal-body {padding: 2px 16px;}

    .modal-footer {
        padding: 2px 16px;
        background-color: #5cb85c;
        color: white;
    }
</style>
<div class="center-content">
    <div>
        <div>
            <ul class="breadcrumb">
                <li><a href="<?php echo base_url() . '/home' ?>"  id='goHome'><i class="icon-home"></i><strong>Home</strong></a> 
                    <span class="divider">/</span></li>
                <li class="active" id="actual_page"></li>
            </ul>
        </div>
        <div>
            <?php
            if (session()->get("msg_success")) {
                ?>
                <div class="alert alert-block alert-success">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4>Saved!</h4>
                    <?php echo session()->get("msg_success"); ?>
                </div>
                <?php
                session()->remove("msg_success");
            } else if (session()->get("msg_error")) {
                ?>
                <div class="alert alert-block alert-danger">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <h4>Error!</h4>
                    <?php echo session()->get("msg_error"); ?>
                </div>
                <?php
                session()->remove("msg_error");
            }
            ?>
        </div>
    </div>
    <div id="display_content">
        <div class="tile" id="drugs-chart">
            <input type="hidden" id="uservalue"/>
            <h3>System Usage Summary <br/>For the last
                <select style="width:auto" class="period" id="usage_period">
                    <option value="7">7 Days</option>
                    <option value="14">14 Days</option>
                    <option value="30" selected=selected>1 Month</option>
                    <option value="90">3 Months</option>
                    <option value="180">6 Months</option>
                    <option value="356">1 Year</option>
                </select>
                <button class="generate btn btn-warning" id="usage_btn">Go</button>
                <button class="btn btn-success more" id="drugs-more">Larger</button>
                <button class="btn btn-danger less" id="drugs-less">Smaller</button>
            </h3>

            <div id="chart_area77">
                <div class="loadingDiv" style="margin:20% 0 20% 0;" ><img style="width: 30px;margin-left:50%" src="<?php echo base_url() . '/images//loading_spin.gif' ?>"></div>
            </div>

        </div>
        <div class="tile" id="enrollment-chart">
            <h3>Weekly Access Log Summary <br/>From
                <input type="text" placeholder="Start" class="span3" id="enrollment_start"/> To
                <input type="text" placeholder="End" class="span3" id="enrollment_end" readonly="readonly"/>
                <button class="btn btn-warning generate" id="access_btn">Go</button>
                <button class="btn btn-success more" id="enrollment-more">Larger</button>
                <button class="btn btn-danger less" id="enrollment-less">Smaller</button>
            </h3>
            <div id="chart_area78">
                <div class="loadingDiv" style="margin:20% 0 20% 0;"  ><img style="width: 30px;margin-left:50%" src="<?php echo base_url() . '/images/loading_spin.gif' ?>"></div>
            </div>
        </div>
    </div>		
</div>
<div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        myStorage = window.localStorage;
        myStorage.clear();
        var base_url = "<?php echo base_url().'/'; ?>";
        var default_link = "<?php echo session()->get('default_link'); ?>";




        //Get Today's Date and Upto Saturday
        var someDate = new Date();
        var dd = ("0" + someDate.getDate()).slice(-2);
        var mm = ("0" + (someDate.getMonth() + 1)).slice(-2);
        var y = someDate.getFullYear();

        var fromDate = "<?php echo $monday = date('Y-m-d', strtotime('monday this week')); ?>";
        var numberOfDaysToAdd = 5;
        var to_date = new Date(someDate.setDate(someDate.getDate() + numberOfDaysToAdd));
        var dd = ("0" + to_date.getDate()).slice(-2);
        var mm = ("0" + (to_date.getMonth() + 1)).slice(-2);
        var y = to_date.getFullYear();
        var endDate = y + '-' + mm + '-' + dd;
        //Load Charts	
        var period = 30;
        var chart1_link = "<?php echo base_url() . '/admin_management/getSystemUsage/'; ?>" + period
        var chart2_link = "<?php echo base_url() . '/admin_management/getWeeklySumary/'; ?>" + fromDate + '/' + endDate;
        myStorage.setItem("fromDate", fromDate);
        myStorage.setItem("endDate", endDate);


        $('#chart_area77').load(chart1_link);
        $('#chart_area78').load(chart2_link);


        if (default_link) {
            default_link = base_url + "admin_management/" + default_link;
            $.blockUI({message: '<h3><img width="30" height="30" src="<?php echo base_url() . '/images/loading_spin.gif' ?>" /> Working...</h3>'});
            $("#display_content").load(default_link, function () {
                $('.dataTables').dataTable({
                    "bJQueryUI": true,
                    "sPaginationType": "full_numbers",
                    "bStateSave": true,
                    "sDom": '<"H"<"clear">lfrT>t<"F"ip>',
                    "bProcessing": true,
                    "bServerSide": false,
                    "bAutoWidth": true,
                    "bDeferRender": true,
                    "bInfo": true,
                    "bScrollCollapse": true,
                    "bDestroy": true,
                    "fnInitComplete": function () {
                        this.css("visibility", "visible");
                        $.unblockUI();
                    }
                });
            });
        }

        $("#goHome").click(function () {
<?php
session()->set('default_link', "");
?>
        });

        $(".admin_link").click(function () {
            $("#display_content").empty();
            $.blockUI({message: '<h3><img width="30" height="30" src="<?php echo base_url() . '/images/loading_spin.gif' ?>" /> Working...</h3>'});
            var link = $(this).attr("id");
            var link = "<?php echo base_url();?>/admin_management/" + link;
            $("#display_content").load(link, function () {
                $('.dataTables').dataTable({
                    "bJQueryUI": true,
                    "sPaginationType": "full_numbers",
                    "bStateSave": true,
                    "sDom": '<"H"<"clear">lfrT>t<"F"ip>',
                    "bProcessing": true,
                    "bServerSide": false,
                    "bAutoWidth": true,
                    "bDeferRender": true,
                    "bInfo": true,
                    "bScrollCollapse": true,
                    "bDestroy": true,
                    "fnInitComplete": function () {
                        this.css("visibility", "visible");
                        $.unblockUI();
                    }
                });
            });
        });
        setTimeout(function () {
            $(".message").fadeOut("2000");
        }, 6000);


    });
</script>
