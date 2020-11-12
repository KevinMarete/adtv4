<?php
$chartSize = 0;
if ($resultArraySize <= 6) {
    $chartSize = '300';
}
if ($resultArraySize > 6) {
    $chartSize = '600';
}
if ($resultArraySize > 10) {
    $chartSize = '900';
}
if ($resultArraySize > 15) {
    $chartSize = '1200';
}
if ($resultArraySize > 20) {
    $chartSize = '1500';
}
if ($resultArraySize > 25) {
    $chartSize = '3000';
}
?>

<script>
    $(function () {
        period = $('#enrollment_start').val();
        text = '';
        counter = 0;
        myStorage = window.localStorage;

        $('<?php echo "#" . $container; ?>').highcharts({
            exporting: {
                chartOptions: {// specific options for the exported image
                    plotOptions: {
                        series: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    }
                },
                sourceWidth: 400,
                sourceHeight: 300,
                scale: 1,
                fallbackToExportServer: false,
                buttons: {
                    customButton: {
                        text: '< Back',
                        onclick: function () {
                            from = myStorage.getItem("fromDate");
                            end = myStorage.getItem("endDate");
                            var chart2_link = "<?php echo base_url() . 'admin_management/getWeeklySumary/'; ?>" + from + '/' + end;
                            $('#chart_area78').load(chart2_link);
                            myStorage.clear();


                        }
                    }
                }
            },
            colors: [
                '#66aaf7',
                '#f66c6f',
                '#8bbc21',
                '#910000',
                '#1aadce',
                '#492970',
                '#f28f43',
                '#77a1e5',
                '#c42525',
                '#a6c96a'
            ],
            chart: {
                height:<?php echo $chartSize; ?>,
                type: '<?php echo $chartType ?>'
            },
            title: {
                text: '<?php echo $chartTitle; ?>'
            },
            xAxis:
                    {
                        categories: <?php echo $categories; ?>,
                        title: {
                            text: null
                        }
                    },
            yAxis: {
                min: 0,
                title: {
                    text: '<?php echo $yAxix; ?>',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                }
            },
            tooltip: {
                valueSuffix: ''
            },
            plotOptions: {
                bar: {
                    dataLabels: {
                        enabled: true
                    }
                },
                series: {
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function () {

                                if (counter === 0) {
                                    $.post("<?php echo base_url() . '/admin_management/getWeeklySumaryPerUser'; ?>", {day: this.category, start: period}, function (resp) {
                                        $('#chart_area78').append(resp);

                                        counter++;
                                        //alert(counter)
                                    });

                                } else {
                                    alert('ACTION END REACHED');
                                    counter = 1;
                                    return false;
                                }

                            }
                        }
                    }
                }

            },
            legend: {
                layout: 'horizontal',
                align: 'left',
                verticalAlign: 'top',
                floating: true,
                borderWidth: 1,
                backgroundColor: '#FFFFFF',
                shadow: true
            },
            credits: {
                enabled: false
            },
            series:<?php echo $resultArray ?>
        });
    });
</script>
<div class="graph">
    <div id="<?php echo $container ?>"  style="width:98%">
    </div>
</div>

