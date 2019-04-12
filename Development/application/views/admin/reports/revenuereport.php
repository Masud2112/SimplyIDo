<?php
//show report on main report screen
if (!$detailed_report) {
    ?>
    <h4 class="sub-title"><?php echo _l('revenue_title'); ?></h4>
    <div class="pull-right">
        <input type="hidden" name="reportrevenueid" id="reportrevenueid" value="<?php echo $reportconfigurationid; ?>">
        <div class="disInline repSetting">
            <a href="javascript:void(0)" data-toggle="modal" id="revenue_display_report_settings"
               data-reportrevenueid="<?php echo $reportconfigurationid; ?>"><i class="fa fa-cog" aria-hidden="true"></i></a>
        </div>
        <div class="disInline selectfilter">
            <?php echo render_select('revenue-filter', $filters, array('filtervalue', 'filtername'), '', (isset($report_data->saved_filter) ? $report_data->saved_filter : '')); ?>
        </div>
        <div class="disInline cusfilter">
            <input type="text" class="form-control hide" id="revenue_date" name="revenue_date"
                   data-reportrevenueid="<?php echo $reportconfigurationid; ?>" placeholder="Select Dates"/>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        <?php
        if ($has_permission_revenue == 1) {
            if ($report_data->is_visible == 1) {
                $is_revenue = 1;
                $totalrevenue = 0;
                foreach ($revenues as $revenue) {
                    $totalrevenue += $revenue['total_revenue'];
                    if ($revenue['status'] == 1) {
                        $status = _l('invoice_status_unpaid');
                    } elseif ($revenue['status'] == 2) {
                        $status = _l('invoice_status_paid');
                    } elseif ($revenue['status'] == 3) {
                        $status = _l('invoice_status_not_paid_completely');
                    } elseif ($revenue['status'] == 4) {
                        $status = _l('invoice_status_overdue');
                    } elseif ($revenue['status'] == 5) {
                        $status = _l('invoice_status_cancelled');
                    } else {
                        // status 6
                        $status = _l('invoice_status_draft');
                    }
                    $revenue_name = $status . " : " . $revenue['no_of_revenues'] . "<br/> ( " . $revenue['revenue_percent'] . "% ) ";
                    $revenue_data[] = "['" . $revenue_name . "', " . $revenue['revenue_percent'] . "]";
                }
                $totalrevenue_title = "$" . $totalrevenue . "<br/><small>TOTAL VALUE</small>";
                $Charttotalrevenue_title = "$" . number_format($totalrevenue) . " TOTAL VALUE";
                //$Charttotalrevenue_title = "$" . number_format(1000) . " TOTAL VALUE";
                ?>
                <input type="hidden" name="reportconfigurationid" id="reportconfigurationid"
                       value="<?php echo $reportconfigurationid; ?>">
                <div id="revenue-container" class="chart"></div>
                <div id="addText3" style="position:absolute; left:0px; top:0px;"></div>
                <div class="widget-heading">
                    <div class="more-setting">
                        <a href="javascript:void(0)" id="expand-revenue" data-target="#revenue-table"
                           id="revenue-collapse"><span id="revenue-span">HIDE DETAIL </span><i
                                    class="fa fa-caret-up"></i></a>
                    </div>
                </div>
                <h3 class="text-center"><?php echo $Charttotalrevenue_title; ?></h3>
                <div id="revenue-table">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th class="text-center"><?php echo strtoupper(_l('revenue_title')); ?></th>
                            <th class="text-center">#</th>
                            <th class="text-center">%</th>
                            <th class="text-center"><?php echo strtoupper(_l('value')); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (count($revenues) > 0) {
                            foreach ($revenues as $revenue) {
                                if ($revenue['status'] == 1) {
                                    $status = _l('invoice_status_unpaid');
                                } elseif ($revenue['status'] == 2) {
                                    $status = _l('invoice_status_paid');
                                } elseif ($revenue['status'] == 3) {
                                    $status = _l('invoice_status_not_paid_completely');
                                } elseif ($revenue['status'] == 4) {
                                    $status = _l('invoice_status_overdue');
                                } elseif ($revenue['status'] == 5) {
                                    $status = _l('invoice_status_cancelled');
                                } else {
                                    // status 6
                                    $status = _l('invoice_status_draft');
                                }
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $status; ?></td>
                                    <td class="text-center"><?php echo $revenue['no_of_revenues']; ?></td>
                                    <td class="text-center"><?php echo $revenue['revenue_percent'] . "%"; ?></td>
                                    <td class="text-center"><?php echo $revenue['total_revenue']; ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="4" class="text-center"><?php echo _l('no_records_found'); ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    <div>
                        <a class="btn btn-info" name="btnrevenuestatus" id="btnrevenuestatus"
                           href="<?php echo admin_url('reports/revenue'); ?>"><i class="fa fa-expand"></i>&nbsp;Expanded
                            Report</a>
                    </div>
                </div>
            <?php } else { ?>
                <div>Report is not visible. Please click on <a href="<?php echo admin_url("reports/config"); ?>">Change
                        Setting</a></div>
            <?php }
        } else { ?>
            <div>You do not have permission to view report.</div>
        <?php } ?>
    </div>
    <script type="text/javascript">
        <?php
        if(isset($is_revenue)) {
        ?>
        //for revenue
        $('#expand-revenue').click(function () {
            var target = $(this).attr('data-target');
            $(target).slideToggle()
            $('#revenue-span').text($('#revenue-span').text() == 'HIDE DETAIL ' ? 'SHOW DETAIL ' : 'HIDE DETAIL ');
            $(this).find('i').toggleClass('fa-caret-up fa-caret-down');
        });
        <?php } ?>

        <?php if(isset($report_data->saved_filter)) { ?>
        $('#revenue-filter').val('<?php echo $report_data->saved_filter; ?>');
        $('.selectpicker').selectpicker('refresh')
        <?php } ?>

        <?php

        if(isset($is_revenue) && !empty($revenue_data)) {
        ?>

        $(function () {
            (function (H) {
                H.wrap(H.Renderer.prototype, 'label', function (proceed, str, x, y, shape, anchorX, anchorY, useHTML) {
                    if (/class="fa/.test(str)) useHTML = true;
                    // Run original proceed method
                    return proceed.apply(this, [].slice.call(arguments, 1));
                });
            }(Highcharts));

            var chart = new Highcharts.chart('revenue-container', {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    title: {
                        text: ''
                    },
                    plotOptions: {
                        pie: {
                            innerSize: '55%',
                            startAngle: 90,
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>'
                            },
                            point: {
                                events: {
                                    // mouseOver: function(){
                                    //   this.series.chart.innerText.attr({text: this.y});
                                    // },
                                    mouseOut: function () {
                                        this.series.chart.innerText.attr({text: '<?php //echo $totalrevenue_title; ?>'});
                                    }
                                }
                            }
                        }
                    },
                    navigation: {
                        buttonOptions: {
                            theme: {
                                style: {
                                    color: '#039',
                                    textDecoration: 'underline'
                                }
                            }
                        }
                    },
                    exporting: {
                        allowHTML: true,
                        buttons: {
                            contextButton: {
                                enabled: false
                            },
                            printButton: {
                                text: '<i class="fa fa-print"></i>',
                                onclick: function () {
                                    this.print();
                                }
                            }
                        }
                    },
                    series: [{
                        name: 'Revenue',
                        data: [<?php echo join($revenue_data, ',') ?>]
                    }]
                },
                function (chart) { // on complete

                    <?php if(is_mobile()) {?>
                    // on complete
                    var textX = chart.plotLeft + (chart.plotWidth * 0.5);
                    var textY = chart.plotTop + (chart.plotHeight * 0.5);

                    var span = '<span id="pieChartInfoText3" style="position:absolute; text-align:center;font-size: 10px;color :#000000;font-weight:bold";>';
                    span += '<?php // echo $Charttotalrevenue_title; ?>';
                    span += '</span>';

                    $("#addText3").append(span);
                    span = $('#pieChartInfoText3');
                    span.css('left', textX + (span.width() * -0.3));
                    span.css('top', textY + (span.height() * 0));

                    <?php }else{ ?>

                    var xpos = '55%';
                    var ypos = '58%';
                    var circleradius = 200;

                    // Render the text
                    chart.innerText = chart.renderer.text('<?php //echo $totalrevenue_title; ?>', 200, 200).css({
                        width: circleradius * 2,
                        color: '#000',
                        fontSize: '16px',
                        fontWeight: 600,
                        textAlign: 'center'
                    }).attr({
                        // why doesn't zIndex get the text in front of the chart?
                        zIndex: 999
                    }).add();


                    <?php } ?>

                });
        });
        <?php } else { ?>
        $('#revenue-container').html('<span class="chart-norecord">No Records Found</span>');
        <?php } ?>

        <?php if($report_data->saved_filter == 'custom') { ?>
        $('input[name="revenue_date"]').daterangepicker({
            locale: {
                format: 'MM/DD/YYYY'
            },
            clearBtn: true,
            startDate: '<?php echo date("m/d/Y", strtotime($report_data->start_date)); ?>',
            endDate: '<?php echo date("m/d/Y", strtotime($report_data->end_date)); ?>'
        });
        <?php } ?>

        //revenue filter change
        $('#revenue-filter').change(function () {
            var filter_val = $(this).val();
            var configid = $('#reportrevenueid').val();
            //if filter is predefined
            if (filter_val != 'custom') {
                $.ajax({
                    url: "<?php echo admin_url('reports/filter_revenue'); ?>",
                    method: "POST",
                    type: "html",
                    data: "saved_filter=" + filter_val + '&reportconfigurationid=' + configid
                }).done(function (data) {
                    $("#revenue-div").html(data);
                    $("#revenue-filter").val(filter_val);
                    $(".selectpicker").selectpicker('refresh');
                });
            } else {
                //if filter is custom, get start date and endate
                $('#revenue_date').removeClass('hide');
                $('input[name="revenue_date"]').daterangepicker({
                    locale: {
                        format: 'MM/DD/YYYY'
                    },
                    clearBtn: true
                });

                /**
                 * to clear view revenue date filter on cancel button
                 */
                $('#revenue_date').on('cancel.daterangepicker', function (ev, picker) {
                    //do something, like clearing an input
                    $('#revenue_date').val('');
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_revenue'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=all&reportconfigurationid=' + configid
                    }).done(function (data) {
                        //console.log(data);
                        $("#revenue-div").html(data);
                        $("#revenue-filter").val('all');
                        $(".selectpicker").selectpicker('refresh');
                    });
                });

                $('.applyBtn').click(function () {
                    //generate chart and table on setting start and end date
                    var configid = $('#revenue_date').attr('data-reportrevenueid');
                    var startDate = $('#revenue_date').data('daterangepicker').startDate._d;
                    var endDate = $('#revenue_date').data('daterangepicker').endDate._d;
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_revenue'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=custom&reportconfigurationid=' + configid + '&startDate=' + startDate + '&endDate=' + endDate
                    }).done(function (data) {
                        //console.log(data);
                        $("#revenue-div").html(data);
                        $("#revenue-filter").val(filter_val);
                        $(".selectpicker").selectpicker('refresh');
                        $('#revenue_date').removeClass('hide');
                        $('input[name="revenue_date"]').daterangepicker({
                            locale: {
                                format: 'MM/DD/YYYY'
                            },
                            clearBtn: true,
                            startDate: startDate,
                            endDate: endDate
                        });
                    });
                });
            }
        });

        initReportDataTableOffline('.table-revenue', <?php echo $report_data->default_records; ?>);
    </script>
<?php } else { //detailed report?>
    <div class="panel-body">
        <?php
        $tot_revenue = 0;
        foreach ($revenues as $revenue) {
            if ($revenue['status'] == 1) {
                $status = _l('invoice_status_unpaid');
            } elseif ($revenue['status'] == 2) {
                $status = _l('invoice_status_paid');
            } elseif ($revenue['status'] == 3) {
                $status = _l('invoice_status_not_paid_completely');
            } elseif ($revenue['status'] == 4) {
                $status = _l('invoice_status_overdue');
            } elseif ($revenue['status'] == 5) {
                $status = _l('invoice_status_cancelled');
            } else {
                // status 6
                $status = _l('invoice_status_draft');
            }
            $tot_revenue += $revenue['total_revenue'];
            $revenue_name = $status . " : " . $revenue['total_revenue'];
            $categories[] = "['" . $status . "']";
            $data[] = "['" . $revenue_name . "', " . $revenue['total_revenue'] . "]";
        }
        ?>
        <div id="revenue-container"></div>
        <div id="revenue-table">
            <div>
                <input type="hidden" name="reportrevenueid" id="reportrevenueid"
                       value="<?php echo $reportconfigurationid; ?>">
                <div class="subHead pull-left">
                    <b><?php echo '$' . $tot_revenue . " "; ?></b><span><?php echo(isset($report_data->saved_filter) ? ucwords(str_replace('_', ' ', $report_data->saved_filter)) : 'All'); ?></span>
                </div>
                <div class="pull-right">
                    <div class="disInline repSetting">
                        <a href="javascript:void(0)" data-toggle="modal" id="revenue_display_report_settings"
                           data-reportrevenueid="<?php echo $reportconfigurationid; ?>"><i class="fa fa-cog"
                                                                                           aria-hidden="true"></i></a>
                    </div>
                    <div class="disInline selectfilter">
                        <select id="revenue-filter" name="revenue-filter" class="selectpicker" data-width="100%"
                                data-none-selected-text="Select" data-live-search="true" tabindex="-98">
                            <?php
                            foreach ($filters as $filter) {
                                ?>
                                <option value="<?php echo $filter['filtervalue']; ?>" <?php if ($report_data->saved_filter == $filter['filtervalue']) {
                                    echo 'selected="selected"';
                                } ?>><?php echo $filter['filtername']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="disInline cusfilter">
                        <?php
                        if ($report_data->saved_filter == 'custom') {
                            $class = 'form-control';
                        } else {
                            $class = 'form-control hide';
                        }
                        ?>
                        <input type="text" class="<?php echo $class; ?>" id="revenue_date" name="revenue_date"
                               data-reportrevenueid="<?php echo $reportconfigurationid; ?>" placeholder="Select Dates"/>
                    </div>
                </div>
            </div>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="text-center"><?php echo strtoupper(_l('revenue_title')); ?></th>
                    <th class="text-center">#</th>
                    <th class="text-center">%</th>
                    <th class="text-center"><?php echo strtoupper(_l('value')); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (count($revenues) > 0) {
                    $tot_revenues = $total_value = 0;
                    foreach ($revenues as $revenue) {
                        if ($revenue['status'] == 1) {
                            $status = _l('invoice_status_unpaid');
                        } elseif ($revenue['status'] == 2) {
                            $status = _l('invoice_status_paid');
                        } elseif ($revenue['status'] == 3) {
                            $status = _l('invoice_status_not_paid_completely');
                        } elseif ($revenue['status'] == 4) {
                            $status = _l('invoice_status_overdue');
                        } elseif ($revenue['status'] == 5) {
                            $status = _l('invoice_status_cancelled');
                        } else {
                            // status 6
                            $status = _l('invoice_status_draft');
                        }
                        $tot_revenues += $revenue['no_of_revenues'];
                        $total_value += $revenue['total_revenue'];
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $status; ?></td>
                            <td class="text-center"><?php echo $revenue['no_of_revenues']; ?></td>
                            <td class="text-center"><?php echo $revenue['revenue_percent'] . "%"; ?></td>
                            <td class="text-center"><?php echo "$" . $revenue['total_revenue']; ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="4" class="text-center"><?php echo _l('no_records_found'); ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
                <?php
                if (count($revenues) > 0) {
                    ?>
                    <tfoot>
                    <tr>
                        <td class="text-center"></td>
                        <td class="text-center"><?php echo $tot_revenues; ?></td>
                        <td class="text-center"></td>
                        <td class="text-center"><?php echo "$" . $total_value ?></td>
                    </tr>
                    </tfoot>
                <?php } ?>
            </table>
        </div>
    </div>
    <script type="text/javascript">
        <?php
        //Generate Chart for expanded report
        if(!empty($categories)) {
        ?>
        $(function () {
            (function (H) {
                H.wrap(H.Renderer.prototype, 'label', function (proceed, str, x, y, shape, anchorX, anchorY, useHTML) {
                    if (/class="fa/.test(str)) useHTML = true;
                    // Run original proceed method
                    return proceed.apply(this, [].slice.call(arguments, 1));
                });
            }(Highcharts));

            // Create the chart
            Highcharts.chart('revenue-container', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: [<?php echo join($categories, ',') ?>]
                },
                yAxis: {
                    title: {
                        text: 'Number'
                    }
                },
                navigation: {
                    buttonOptions: {
                        theme: {
                            style: {
                                color: '#039',
                                textDecoration: 'underline'
                            }
                        }
                    }
                },
                exporting: {
                    allowHTML: true,
                    buttons: {
                        contextButton: {
                            enabled: false
                        },
                        printButton: {
                            text: '<i class="fa fa-print"></i>',
                            onclick: function () {
                                this.print();
                            }
                        }
                    }
                },
                series: [{
                    name: 'Revenue',
                    colorByPoint: true,
                    data: [<?php echo join($data, ',') ?>]
                }]
            });
        });
        <?php } ?>

        <?php if($report_data->saved_filter == 'custom') { ?>
        $('input[name="revenue_date"]').daterangepicker({
            locale: {
                format: 'MM/DD/YYYY'
            },
            clearBtn: true,
            startDate: '<?php echo date("m/d/Y", strtotime($report_data->start_date)); ?>',
            endDate: '<?php echo date("m/d/Y", strtotime($report_data->end_date)); ?>'
        });
        <?php } ?>

        <?php if(isset($report_data->saved_filter)) { ?>
        $('#revenue-filter').val('<?php echo $report_data->saved_filter; ?>');
        $('.selectpicker').selectpicker('refresh')
        <?php } ?>

        //revenue filter change
        $('#revenue-filter').change(function () {
            var filter_val = $(this).val();
            var configid = $('#reportrevenueid').val();

            //if filter is predefined
            if (filter_val != 'custom') {
                $('#revenue_date').addClass('hide');
                $.ajax({
                    url: "<?php echo admin_url('reports/filter_revenue'); ?>",
                    method: "POST",
                    type: "html",
                    data: 'saved_filter=' + filter_val + '&reportconfigurationid=' + configid + '&type=detailed'
                }).done(function (data) {
                    //console.log(data);
                    $("#revenue-div").html(data);
                    $("#revenue-filter").val(filter_val);
                    $(".selectpicker").selectpicker('refresh');
                });
            } else {
                //if filter is custom, get start date and endate
                $('#revenue_date').removeClass('hide');
                $('input[name="revenue_date"]').daterangepicker({
                    locale: {
                        format: 'MM/DD/YYYY'
                    },
                    clearBtn: true
                });

                /**
                 * to clear view revenue date filter on cancel button
                 */
                $('#revenue_date').on('cancel.daterangepicker', function (ev, picker) {
                    //do something, like clearing an input
                    $('#revenue_date').val('');
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_revenue'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=all&reportconfigurationid=' + configid + '&type=detailed'
                    }).done(function (data) {
                        //console.log(data);
                        $("#revenue-div").html(data);
                        $("#revenue-filter").val('all');
                        $(".selectpicker").selectpicker('refresh');
                    });
                });

                $('.applyBtn').click(function () {
                    //generate chart and table on setting start and end date
                    var configid = $('#revenue_date').attr('data-reportrevenueid');
                    var startDate = $('#revenue_date').data('daterangepicker').startDate._d;
                    var endDate = $('#revenue_date').data('daterangepicker').endDate._d;
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_revenue'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=custom&reportconfigurationid=' + configid + '&startDate=' + startDate + '&endDate=' + endDate + '&type=detailed'
                    }).done(function (data) {
                        //console.log(data);
                        $("#revenue-div").html(data);
                        $("#revenue-filter").val(filter_val);
                        $(".selectpicker").selectpicker('refresh');
                        $('#revenue_date').removeClass('hide');
                        $('input[name="revenue_date"]').daterangepicker({
                            locale: {
                                format: 'MM/DD/YYYY'
                            },
                            clearBtn: true,
                            startDate: startDate,
                            endDate: endDate
                        });
                    });
                });
            }
        });
    </script>
<?php } ?>

<!--
  * Added by: Vaidehi
  * Date: 03/17/2018
  * Popup to display setting option
  -->
<div class="modal fade" id="revenue_display_settings" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('report_settings'); ?>
                </h4>
            </div>
            <?php echo form_open('admin/reports/update_setting', array('novalidate' => true, 'id' => 'report-setting')); ?>
            <input type="hidden" name="reportconfigurationid" id="reportconfigurationid"
                   value="<?php echo $reportconfigurationid; ?>">
            <input type="hidden" name="type" id="type" value="leadstatus">
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <div class="checkbox checkbox-primary mtop25">
                            <input type="checkbox" name="revenue_no_of_records" id="revenue_no_of_records">
                            <label for="default_records"><?php echo _l('default_records'); ?></label>
                        </div>
                    </div>
                </div>
                <div id="revenue-no-of-records">
                    <?php echo render_input('default_records', 'default_records', $report_data->default_records); ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info" id="save_settings"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#revenue_display_report_settings').click(function () {
        $("#reportconfigurationid").val($(this).data('reportrevenueid'));
        $('#revenue_display_settings').modal('show');
    });

    $('#revenue-no-of-records').hide();

    $('#revenue_no_of_records').click(function () {
        if ($('#revenue_no_of_records').is(':checked')) {
            $('#revenue-no-of-records').show()
        } else {
            $('#revenue-no-of-records').hide();
        }
    });
</script>