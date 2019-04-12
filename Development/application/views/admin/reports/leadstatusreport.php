<?php
//show report on main report screen
if (!$detailed_report) {
    ?>
    <h4 class="sub-title"><?php echo _l('bookingsuccess_title'); ?></h4>
    <div class="pull-right">
        <input type="hidden" name="reportid" id="reportid" value="<?php echo $reportconfigurationid; ?>">
        <div class="disInline repSetting">
            <a href="javascript: void(0);" data-toggle="modal" id="display_report_settings"
               data-reportid="<?php echo $reportconfigurationid; ?>"><i class="fa fa-cog" aria-hidden="true"></i></a>
        </div>
        <div class="disInline selectfilter">
            <?php echo render_select('leadstatus-filter', $filters, array('filtervalue', 'filtername'), '', (isset($report_data->saved_filter) ? $report_data->saved_filter : '')); ?>
        </div>
        <div class="disInline cusfilter">
            <?php
            if ($report_data->saved_filter == 'custom') {
                $class = 'form-control';
            } else {
                $class = 'form-control hide';
            }
            ?>
            <input type="text" class="<?php echo $class; ?>" id="leadstatus_date" name="leadstatus_date"
                   data-reportid="<?php echo $reportconfigurationid; ?>" placeholder="Select Dates"/>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        <?php
        //if report is shown in configuration
        if ($has_permission_booking_stage == 1) {
            if ($report_data->is_visible == 1) {
                $is_lead_statuses = 1;
                $tot_booking_percent = 0;
                if (count($lead_statuses) > 0) {
                    foreach ($lead_statuses as $statuses) {
                        $booking_percent = !empty($statuses['booking_percent']) ? $statuses['booking_percent'] : 0;
                        $status_name = $statuses['name'] . " : " . $statuses['no_of_leads'] . "<br/> ( " . $booking_percent . "% ) ";
                        if ($statuses['no_of_leads'] > 0) {
                            $status_data[] = "['" . $status_name . "', " . $booking_percent . "]";
                        }
                        $tot_booking_percent += $booking_percent;
                    }
                    $totalleadstatus_title = floor(($tot_booking_percent / count($lead_statuses))) . "% <br/><small>SUCCESS RATE</small>";
                    $Charttotalleadsource_title = floor(($tot_booking_percent / count($lead_statuses))) . "% SUCCESS RATE";
                } else {
                    $totalleadstatus_title = "";
                    $Charttotalleadsource_title = '';
                }
                ?>
                <!--Report in Chart-->
                <div id="lead-status-container" class="chart"></div>
                <div id="addText2" style="position:absolute; left:0px; top:0px;"></div>
                <div class="widget-heading">
                    <div class="more-setting">
                        <a href="javascript: void(0);" id="expand-leadstatus" data-target="#leadstatus-table"
                           id="leadstatus-collapse"><span id="leadstatus-span">HIDE DETAIL </span><i
                                    class="fa fa-caret-up"></i></a>
                    </div>
                </div>
                <!--Report in Table Form-->
                <h3 class="text-center"><?php echo $Charttotalleadsource_title; ?></h3>
                <div id="leadstatus-table">
                    <div class="table-responsive col-sm-12">
                        <table class="table table-striped table-leadstatus">
                            <thead>
                            <tr>
                                <th><?php echo strtoupper(_l('bookingsuccess_title')); ?></th>
                                <th>#</th>
                                <th class="text-center">%</th>
                                <th class="text-center"><?php echo strtoupper(_l('avg_time_to_booking')); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (count($lead_statuses) > 0) {
                                foreach ($lead_statuses as $statuses) {
                                    ?>
                                    <tr>
                                        <td><?php echo $statuses['name']; ?></td>
                                        <td><?php echo $statuses['no_of_leads']; ?></td>
                                        <td class="text-center"><?php echo $statuses['total_booking'] . " (" . $statuses['booking_percent'] . "%" . ")"; ?></td>
                                        <td class="text-center"><?php echo $statuses['avg_time_to_booking']; ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td class="text-center" colspan="4"><?php echo _l('no_records_found'); ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <?php
                        if (count($lead_statuses) > 0) {
                            ?>
                            <!-- <div class="loadmore_section">
                  <a href="#" id="leadstatus_loadMore" class="btn btn-info pull-left active display-block mright10 loadMore">(<?php //echo count($lead_statuses); ?>)<span class="all_pin_data_only_count">Load More</a>
                </div> -->
                            <!-- <button id="button" type="button">Page +5</button>  -->
                        <?php } ?>
                        <a class="btn btn-info" name="btnleadstatus" id="btnleadstatus"
                           href="<?php echo admin_url('reports/leadstatus'); ?>"><i class="fa fa-expand"></i>&nbsp;Expanded
                            Report</a>
                    </div>
                </div>
            <?php } else {
                //if report is hidden in configuration
                ?>
                <div>Report is not visible. Please click on <a href="<?php echo admin_url("reports/config"); ?>">Change
                        Setting</a></div>
            <?php }
        } else { ?>
            <div>You do not have permission to view report.</div>
        <?php } ?>
    </div>
    <script type="text/javascript"
            src="//gyrocode.github.io/jquery-datatables-pageLoadMore/1.0.0/js/dataTables.pageLoadMore.min.js"></script>
    <script type="text/javascript">
        // $('#button').on( 'click', function () {
        //   var VisibleRows = $('.table-leadstatus>tbody>tr:visible').length;
        //   var i = VisibleRows + 5;
        //   dataTable.page.len( i ).draw();
        // });

        <?php
        //collapse-expand report
        if(isset($is_lead_statuses)) {
        ?>
        //for lead status
        $('#expand-leadstatus').click(function () {
            var target = $(this).attr('data-target');
            $(target).slideToggle()
            $('#leadstatus-span').text($('#leadstatus-span').text() == 'HIDE DETAIL ' ? 'SHOW DETAIL ' : 'HIDE DETAIL ');
            $(this).find('i').toggleClass('fa-caret-up fa-caret-down');
        });
        <?php } ?>

        <?php
        //generate dynamic chart
        if(isset($is_lead_statuses) && !empty($status_data)) {
        ?>
        $(function () {
            (function (H) {
                H.wrap(H.Renderer.prototype, 'label', function (proceed, str, x, y, shape, anchorX, anchorY, useHTML) {
                    if (/class="fa/.test(str)) useHTML = true;
                    // Run original proceed method
                    return proceed.apply(this, [].slice.call(arguments, 1));
                });
            }(Highcharts));
            //for lead status
            var chart = new Highcharts.chart('lead-status-container', {
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
                            innerSize: '65%',
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
                                        this.series.chart.innerText.attr({text: '<?php //echo $totalleadstatus_title; ?>'});
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
                        name: 'Booking Success',
                        data: [<?php echo join($status_data, ',') ?>]
                    }]
                },
                function (chart) { // on complete


                    <?php if(is_mobile()){ ?>



                    var textX = chart.plotLeft + (chart.plotWidth * 0.5);
                    var textY = chart.plotTop + (chart.plotHeight * 0.5);

                    var span = '<span id="pieChartInfoText2" style="position:absolute; text-align:center;font-size: 10px;color :#000000;font-weight:bold";>';
                    span += '<?php //echo $Charttotalleadsource_title; ?>';
                    span += '</span>';

                    $("#addText2").append(span);
                    span = $('#pieChartInfoText2');
                    span.css('left', textX + (span.width() * 0));
                    span.css('top', textY + (span.height() * 0));



                    <?php } else { ?>

                    var xpos = '55%';
                    var ypos = '58%';
                    var circleradius = 200;

                    // Render the text
                    chart.innerText = chart.renderer.text('<?php //echo $totalleadstatus_title; ?>', 200, 200).css({
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
        $('#lead-status-container').html('<span class="chart-norecord">No Records Found</span>');
        <?php } ?>

        <?php if(isset($report_data->saved_filter)) { ?>
        $('#leadstatus-filter').val('<?php echo $report_data->saved_filter; ?>');
        $('.selectpicker').selectpicker('refresh')
        <?php } ?>

        <?php if($report_data->saved_filter == 'custom') { ?>
        $('input[name="leadstatus_date"]').daterangepicker({
            locale: {
                format: 'MM/DD/YYYY'
            },
            clearBtn: true,
            startDate: '<?php echo date("m/d/Y", strtotime($report_data->start_date)); ?>',
            endDate: '<?php echo date("m/d/Y", strtotime($report_data->end_date)); ?>'
        });
        <?php } ?>

        //generate chart and table based on lead status filter change
        $('#leadstatus-filter').change(function () {
            var filter_val = $(this).val();
            var configid = $('#reportid').val();

            //if filter is predefined
            if (filter_val != 'custom') {
                $.ajax({
                    url: "<?php echo admin_url('reports/filter_leadstatus'); ?>",
                    method: "POST",
                    type: "html",
                    data: "saved_filter=" + filter_val + '&reportconfigurationid=' + configid
                }).done(function (data) {
                    //console.log(data);
                    $("#leadstatus-div").html(data);
                    $("#leadstatus-filter").val(filter_val);
                    $(".selectpicker").selectpicker('refresh');
                });
            } else {
                //if filter is custom, get start date and endate
                $('#leadstatus_date').removeClass('hide');
                $('input[name="leadstatus_date"]').daterangepicker({
                    locale: {
                        format: 'MM/DD/YYYY'
                    },
                    clearBtn: true
                });

                /**
                 * to clear view leadstatus date filter on cancel button
                 */
                $('#leadstatus_date').on('cancel.daterangepicker', function (ev, picker) {
                    //do something, like clearing an input
                    $('#leadstatus_date').val('');
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_leadstatus'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=all&reportconfigurationid=' + configid
                    }).done(function (data) {
                        //console.log(data);
                        $("#leadstatus-div").html(data);
                        $("#leadstatus-filter").val('all');
                        $(".selectpicker").selectpicker('refresh');
                    });
                });

                $('.applyBtn').click(function () {
                    //generate chart and table on setting start and end date
                    var configid = $('#leadstatus_date').attr('data-reportid');
                    var startDate = $('#leadstatus_date').data('daterangepicker').startDate._d;
                    var endDate = $('#leadstatus_date').data('daterangepicker').endDate._d;
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_leadstatus'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=custom&reportconfigurationid=' + configid + '&startDate=' + startDate + '&endDate=' + endDate
                    }).done(function (data) {
                        //console.log(data);
                        $("#leadstatus-div").html(data);
                        $("#leadstatus-filter").val(filter_val);
                        $(".selectpicker").selectpicker('refresh');
                        $('#leadstatus_date').removeClass('hide');
                        $('input[name="leadstatus_date"]').daterangepicker({
                            locale: {
                                format: 'MM/DD/YYYY'
                            },
                            clearBtn: true,
                            startDate: startDate,
                            endDate: endDate
                        });
                    });
                });

                // ;
            }
        });

        initReportDataTableOffline('.table-leadstatus', <?php echo $report_data->default_records; ?>);
    </script>
<?php } else { //detailed report?>
    <div class="panel-body">
        <?php
        $leads = 0;
        foreach ($lead_statuses as $statuses) {
            $leads += $statuses['no_of_leads'];
            $status_name = $statuses['name'] . " : " . $statuses['no_of_leads'];
            $categories[] = "['" . $statuses['name'] . "']";
            $booking_percent = !empty($statuses['booking_percent']) ? $statuses['booking_percent'] : 0;

            if ($statuses['no_of_leads'] > 0) {
                $data[] = "['" . $status_name . "', " . $booking_percent . "]";
            }
        }
        ?>
        <!-- Generate Chart -->
        <div id="lead-status-container"></div>

        <!-- Generate Table -->
        <div id="leadstatus-table">
            <div>
                <input type="hidden" name="reportid" id="reportid" value="<?php echo $reportconfigurationid; ?>">
                <div class="subHead pull-left">
                    <b><?php echo $leads . " "; ?> </b><span><?php echo(isset($report_data->saved_filter) ? ucwords(str_replace('_', ' ', $report_data->saved_filter)) : 'All'); ?></span>
                </div>
                <div class="pull-right">
                    <div class="disInline repSetting">
                        <a href="javascript: void(0);" data-toggle="modal" id="display_report_settings"
                           data-reportid="<?php echo $reportconfigurationid; ?>"><i class="fa fa-cog"
                                                                                    aria-hidden="true"></i></a>
                    </div>
                    <div class="disInline selectfilter">
                        <select id="leadstatus-filter" name="leadstatus-filter" class="selectpicker" data-width="100%"
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
                        <input type="text" class="<?php echo $class; ?>" id="leadstatus_date" name="leadstatus_date"
                               data-reportid="<?php echo $reportconfigurationid; ?>" placeholder="Select Dates"/>
                    </div>
                </div>
            </div>
            <div class="table-responsive col-sm-12 ">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th class="text-center"><?php echo strtoupper(_l('bookingsuccess_title')); ?></th>
                        <th class="text-center">#</th>
                        <th class="text-center"><?php echo strtoupper(_l('booked')); ?></th>
                        <th class="text-center"><?php echo strtoupper(_l('booking')) . " %"; ?></th>
                        <th class="text-center"><?php echo strtoupper(_l('avg_time_to_booking')); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (count($lead_statuses) > 0) {
                        $tot_leads = $tot_booking = $avg_time_booking = 0;
                        foreach ($lead_statuses as $statuses) {
                            $tot_leads += $statuses['no_of_leads'];
                            $tot_booking += $statuses['total_booking'];
                            $avg_time_booking += $statuses['avg_time_to_booking'];
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $statuses['name']; ?></td>
                                <td class="text-center"><?php echo $statuses['no_of_leads']; ?></td>
                                <td class="text-center"><?php echo $statuses['total_booking']; ?></td>
                                <td class="text-center"><?php echo(!empty($statuses['booking_percent']) ? $statuses['booking_percent'] . "%" : "0%"); ?></td>
                                <td class="text-center"><?php echo $statuses['avg_time_to_booking'] . " days"; ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="5" class="text-center"><?php echo _l('no_records_found'); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                    <?php if (!empty($tot_leads) && !empty($tot_booking) && !empty($avg_time_booking)) { ?>
                        <tfoot>
                        <tr>
                            <td class="text-center"></td>
                            <td class="text-center"><?php echo(!empty($tot_leads) ? $tot_leads : 0); ?></td>
                            <td class="text-center"><?php echo(!empty($tot_booking) ? $tot_booking : 0); ?></td>
                            <td class="text-center"></td>
                            <td class="text-center"><?php echo(!empty($avg_time_booking) ? $avg_time_booking . " days" : 0); ?></td>
                        </tr>
                        </tfoot>
                    <?php } ?>
                </table>
            </div>

        </div>
    </div>
    <script type="text/javascript">
        <?php
        //Generate Chart for expanded report
        if(count($lead_statuses) > 0) {
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
            Highcharts.chart('lead-status-container', {
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
                    name: 'Booking Success',
                    colorByPoint: true,
                    data: [<?php echo join($data, ',') ?>]
                }]
            });
        });
        <?php } ?>

        <?php if(isset($report_data->saved_filter)) { ?>
        $('#leadstatus-filter').val('<?php echo $report_data->saved_filter; ?>');
        $('.selectpicker').selectpicker('refresh')
        <?php } ?>

        <?php if($report_data->saved_filter == 'custom') { ?>
        $('input[name="leadstatus_date"]').daterangepicker({
            locale: {
                format: 'MM/DD/YYYY'
            },
            clearBtn: true,
            startDate: '<?php echo date("m/d/Y", strtotime($report_data->start_date)); ?>',
            endDate: '<?php echo date("m/d/Y", strtotime($report_data->end_date)); ?>'
        });
        <?php } ?>

        //lead status filter change
        $('#leadstatus-filter').change(function () {
            var filter_val = $(this).val();
            var configid = $('#reportid').val();

            //if filter is predefined
            if (filter_val != 'custom') {
                $('#leadstatus_date').addClass('hide');
                $.ajax({
                    url: "<?php echo admin_url('reports/filter_leadstatus'); ?>",
                    method: "POST",
                    type: "html",
                    data: 'saved_filter=' + filter_val + '&reportconfigurationid=' + configid + '&type=detailed'
                }).done(function (data) {
                    //console.log(data);
                    $("#leadstatus-div").html(data);
                    $("#leadstatus-filter").val(filter_val);
                    $(".selectpicker").selectpicker('refresh');
                });
            } else {
                //if filter is custom, get start date and endate
                $('#leadstatus_date').removeClass('hide');
                $('input[name="leadstatus_date"]').daterangepicker({
                    locale: {
                        format: 'MM/DD/YYYY'
                    },
                    clearBtn: true
                });

                /**
                 * to clear view leadstatus date filter on cancel button
                 */
                $('#leadstatus_date').on('cancel.daterangepicker', function (ev, picker) {
                    //do something, like clearing an input
                    $('#leadstatus_date').val('');
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_leadstatus'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=all&reportconfigurationid=' + configid + '&type=detailed'
                    }).done(function (data) {
                        //console.log(data);
                        $("#leadstatus-div").html(data);
                        $("#leadstatus-filter").val('all');
                        $(".selectpicker").selectpicker('refresh');
                    });
                });

                $('.applyBtn').click(function () {
                    //generate chart and table on setting start and end date
                    var configid = $('#leadstatus_date').attr('data-reportid');
                    var startDate = $('#leadstatus_date').data('daterangepicker').startDate._d;
                    var endDate = $('#leadstatus_date').data('daterangepicker').endDate._d;
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_leadstatus'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=custom&reportconfigurationid=' + configid + '&startDate=' + startDate + '&endDate=' + endDate + '&type=detailed'
                    }).done(function (data) {
                        //console.log(data);
                        $("#leadstatus-div").html(data);
                        $("#leadstatus-filter").val(filter_val);
                        $(".selectpicker").selectpicker('refresh');
                        $('#leadstatus_date').removeClass('hide');
                        $('input[name="leadstatus_date"]').daterangepicker({
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
<div class="modal fade" id="display_settings" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                <?php
                $sesssion_data = get_session_data();
                if ($sesssion_data['user_type'] == 1) {
                    ?>
                    <div class="row">
                        <div class="form-group">
                            <div class="checkbox checkbox-primary ">
                                <input type="checkbox" name="report_permission" id="report_permission">
                                <label for="include_share_permission"><?php echo _l('include_share_permission'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div id="sharing-permission">
                        <div class="row">
                            <div class="form-group">
                                <select id="sharing_permission" name="sharing_permission[]" class="selectpicker"
                                        multiple="multiple">
                                    <option value=""></option>
                                    <?php
                                    $role_arr = explode(",", $report_data->sharing_permission);
                                    foreach ($roles as $role) {
                                        if (in_array(trim($role['roleid']), $role_arr)) {
                                            $selected = 'selected="selected"';
                                        } else {
                                            $selected = '';
                                        }
                                        ?>
                                        <option value="<?php echo $role['roleid']; ?>" <?php echo $selected; ?>><?php echo $role['name']; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="row">
                    <div class="form-group">
                        <div class="checkbox checkbox-primary ">
                            <input type="checkbox" name="no_of_records" id="no_of_records">
                            <label for="default_records"><?php echo _l('default_records'); ?></label>
                        </div>
                    </div>
                </div>
                <div id="no-of-records">
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
    $('#display_report_settings').click(function () {
        $("#reportconfigurationid").val($(this).data('reportid'));
        $('#display_settings').modal('show');
    });

    $('#no-of-records').hide();

    <?php
    $sesssion_data = get_session_data();
    if($sesssion_data['user_type'] == 1) {
    ?>
    $('#sharing-permission').hide();

    $('#report_permission').click(function () {
        if ($('#report_permission').is(':checked')) {
            $('#sharing-permission').show()
        } else {
            $('#sharing-permission').hide();
        }
    });
    <?php } ?>

    $('#no_of_records').click(function () {
        if ($('#no_of_records').is(':checked')) {
            $('#no-of-records').show()
        } else {
            $('#no-of-records').hide();
        }
    });
</script>