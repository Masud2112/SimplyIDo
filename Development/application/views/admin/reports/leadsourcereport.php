<?php
//show report on main report screen
if(!$detailed_report) {
    ?>
    <h4 class="sub-title"><?php echo _l('leadsource_title'); ?></h4>
    <div class="pull-right">
        <input type="hidden" name="reportleadid" id="reportleadid" value="<?php echo $reportconfigurationid; ?>">
        <div class="disInline repSetting">
            <a href="javascript:void(0)" data-toggle="modal" id="source_display_report_settings" data-reportleadid="<?php echo $reportconfigurationid; ?>"><i class="fa fa-cog" aria-hidden="true"></i></a>
        </div>
        <div class="disInline selectfilter">
            <?php echo render_select('leadsource-filter', $filters, array('filtervalue','filtername'), '', (isset($report_data->saved_filter) ? $report_data->saved_filter : '')); ?>
        </div>
        <div class="disInline cusfilter">
            <input type="text" class="form-control hide" id="leadsource_date" name="leadsource_date" data-reportleadid="<?php echo $reportconfigurationid; ?>" placeholder="Select Dates" />
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        <?php
        //if report is shown in configuration
        if($has_permission_lead_source == 1) {
            if($report_data->is_visible == 1) {
                $is_lead_source = 1;
                if(count($lead_sources) > 0) {
                    $tot_leads = 0;
                    foreach ($lead_sources as $sources) {
                        $tot_leads += $sources['no_of_leads'];
                        $source_name = $sources['name'] . " : ". $sources['no_of_leads'] . "<br/> ( " . $sources['lead_percent'] . "% ) ";

                        if($sources['no_of_leads'] > 0) {
                            $data[] = "['" . $source_name . "', " . $sources['lead_percent'] . "]";
                        }
                    }
                    $totalleadsource_title = $tot_leads . " <br/><small>LEADS</small>";
                    $Charttotalleadsource_title = $tot_leads . " LEADS";
                }else {
                    $Charttotalleadsource_title = '';
                }
                ?>
                <!--Report in Chart-->
                <div id="lead-source-container" class="chart"></div>
                <div id="addText" style="position:absolute; left:0px; top:0px;"></div>
                <div class="widget-heading">
                    <div class="more-setting">
                        <a href="javascript:void(0)" id="expand-leadsource" data-target="#leadsource-table" id="leadsource-collapse"><span id="leadsource-span">HIDE DETAIL </span><i class="fa fa-caret-up"></i></a>
                    </div>
                </div>
                <!--Report in Table Form-->
                <h3 class="text-center"><?php echo $Charttotalleadsource_title; ?></h3>
                <div id="leadsource-table">
                    <div  class="table-responsive">
                        <table class="table table-striped table-leadsource">
                            <thead>
                            <tr>
                                <th class="text-center"><?php echo strtoupper(_l('leadsource_title')); ?></th>
                                <th class="text-center">#</th>
                                <th class="text-center">%</th>
                                <th class="text-center"><?php echo strtoupper(_l('booking'))." %"; ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(count($lead_sources) > 0) {
                                foreach ($lead_sources as $sources) {
                                    ?>
                                    <tr class="load-leadsource">
                                        <td class="text-center"><?php echo $sources['name']; ?></td>
                                        <td class="text-center"><?php echo $sources['no_of_leads']; ?></td>
                                        <td class="text-center"><?php echo $sources['lead_percent']; ?></td>
                                        <td class="text-center"><?php echo $sources['booking_percent']."%"; ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr><td colspan="4" class="text-center"><?php echo _l('no_records_found'); ?></td></tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>

                    </div>
                    <div>
                        <?php
                        if(count($lead_sources) > 0) {
                            ?>
                            <!-- <div class="pinned_item_button_section">
                  <a href="#" id="leadsource_loadMore" class="btn btn-info pull-left active display-block mright10 loadMore">(<?php //echo count($lead_sources); ?>)<span class="all_pin_data_only_count">Load More</a>
                </div> -->
                        <?php } ?>
                        <a class="btn btn-info" name="btnleadsource" id="btnleadsource" href="<?php echo admin_url('reports/leadsource'); ?>"><i class="fa fa-expand"></i>&nbsp;Expanded Report</a>
                    </div>
                </div>
            <?php } else {
                //if report is hidden in configuration
                ?>
                <div>Report is not visible. Please click on <a href="<?php echo admin_url("reports/config"); ?>">Change Setting</a></div>
            <?php } } else {?>
            <div>You do not have permission to view report.</div>
        <?php } ?>
    </div>
    <script type="text/javascript">
        <?php
        if(isset($is_lead_source)) {
        ?>
        //for lead source
        $('#expand-leadsource').click(function () {
            var target = $(this).attr('data-target');
            $(target).slideToggle()
            $('#leadsource-span').text($('#leadsource-span').text() == 'HIDE DETAIL ' ? 'SHOW DETAIL ' : 'HIDE DETAIL ');
            $(this).find('i').toggleClass('fa-caret-up fa-caret-down');
        });
        <?php } ?>

        <?php
        if(isset($is_lead_source) && !empty($data)) {
        ?>
        $(function() {
            (function (H) {
                H.wrap(H.Renderer.prototype, 'label', function (proceed, str, x, y, shape, anchorX, anchorY, useHTML) {
                    if(/class="fa/.test(str)) useHTML = true;
                    // Run original proceed method
                    return proceed.apply(this, [].slice.call(arguments, 1));
                });
            }(Highcharts));
            //for lead source
            var chart = new Highcharts.chart('lead-source-container', {
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
                                    mouseOut: function(){
                                        this.series.chart.innerText.attr({text: '<?php //echo $totalleadsource_title; ?>'});
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
                        name: 'Lead Source',
                        data: [<?php echo join($data, ',') ?>]
                    }]
                },

                function(chart) { // on complete
                    <?php if(is_mobile()) { ?>
                    var textX = chart.plotLeft + (chart.plotWidth  * 0.5);
                    var textY = chart.plotTop  + (chart.plotHeight * 0.5);
                    var span = '<span id="pieChartInfoText" style="position:absolute; text-align:center;font-size:12px;color: #000; text-align: center; font-weight:600">';
                    span += '<?php //echo $Charttotalleadsource_title; ?>';
                    span += '</span>';
                    $("#addText").append(span);
                    span = $('#pieChartInfoText');
                    span.css('left', textX + (span.width() * -0.6));
                    span.css('top', textY + (span.height() * -0.3));
                    <?php }else{ ?>
                    var xpos = '55%';
                    var ypos = '58%';
                    var circleradius = 200;

                    // Render the text
                    chart.innerText = chart.renderer.text('<?php //echo $totalleadsource_title; ?>', 200, 200).css({
                        width: '400px',
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
        $('#lead-source-container').html('<span class="chart-norecord">No Records Found</span>');
        <?php } ?>

        <?php if(isset($report_data->saved_filter)) { ?>
        $('#leadsource-filter').val('<?php echo $report_data->saved_filter; ?>');
        $('.selectpicker').selectpicker('refresh')
        <?php } ?>

        <?php if($report_data->saved_filter == 'custom') { ?>
        $('input[name="leadsource_date"]').daterangepicker({
            locale: {
                format: 'MM/DD/YYYY'
            },
            clearBtn: true,
            startDate: '<?php echo date("m/d/Y",strtotime($report_data->start_date)); ?>',
            endDate: '<?php echo date("m/d/Y",strtotime($report_data->end_date)); ?>'
        });
        <?php } ?>

        //generate chart and table based on lead source filter change
        $('#leadsource-filter').change(function(){
            var filter_val = $(this).val();

            var configid = $('#reportleadid').val();

            //if filter is predefined
            if(filter_val != 'custom') {
                $.ajax({
                    url: "<?php echo admin_url('reports/filter_leadsource'); ?>",
                    method: "POST",
                    type: "html",
                    data: "saved_filter="+filter_val+'&reportconfigurationid='+configid
                }).done(function(data){
                    //console.log(data);
                    $("#leadsource-div").html(data);
                    $("#leadsource-filter").val(filter_val);
                    $(".selectpicker").selectpicker('refresh');
                });
            } else {
                //if filter is custom, get start date and endate
                $('#leadsource_date').removeClass('hide');
                $('input[name="leadsource_date"]').daterangepicker({
                    locale: {
                        format: 'MM/DD/YYYY'
                    },
                    clearBtn: true
                });

                /**
                 * to clear view leadsource date filter on cancel button
                 */
                $('#leadsource_date').on('cancel.daterangepicker', function(ev, picker) {
                    //do something, like clearing an input
                    $('#leadsource_date').val('');
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_leadsource'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=all&reportconfigurationid='+configid
                    }).done(function(data){
                        //console.log(data);
                        $("#leadsource-div").html(data);
                        $("#leadsource-filter").val('all');
                        $(".selectpicker").selectpicker('refresh');
                    });
                });

                $('.applyBtn').click(function(){
                    //generate chart and table on setting start and end date
                    var configid = $('#leadsource_date').attr('data-reportleadid');
                    var startDate = $('#leadsource_date').data('daterangepicker').startDate._d;
                    var endDate = $('#leadsource_date').data('daterangepicker').endDate._d;
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_leadsource'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=custom&reportconfigurationid='+configid+'&startDate='+startDate+'&endDate='+endDate
                    }).done(function(data){
                        //console.log(data);
                        $("#leadsource-div").html(data);
                        $("#leadsource-filter").val(filter_val);
                        $(".selectpicker").selectpicker('refresh');
                        $('#leadsource_date').removeClass('hide');
                        $('input[name="leadsource_date"]').daterangepicker({
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

        initReportDataTableOffline('.table-leadsource', <?php echo $report_data->default_records; ?>);
    </script>
<?php } else { //detailed report?>
    <div class="panel-body">
        <?php
        $leads = 0;
        foreach ($lead_sources as $sources) {
            $leads += $sources['no_of_leads'];
            $source_name = $sources['name'] . " : ". $sources['no_of_leads'];
            $categories[] = "['" . $sources['name']  . "']";
            $data[] = "['" . $source_name . "', " . $sources['no_of_leads'] . "]";
        }
        ?>
        <!-- Generate Chart -->
        <div id="lead-source-container"></div>

        <!-- Generate Table -->
        <div id="leadsource-table">
            <div>
                <input type="hidden" name="reportleadid" id="reportleadid" value="<?php echo $reportconfigurationid; ?>">
                <div class="subHead pull-left">
                    <b><?php echo $leads . " "; ?></b><span><?php echo (isset($report_data->saved_filter) ? ucwords(str_replace('_', ' ', $report_data->saved_filter)) : 'All'); ?></span>
                </div>
                <div class="pull-right">
                    <div class="disInline repSetting">
                        <a href="javascript:void(0)" data-toggle="modal" id="source_display_report_settings" data-reportleadid="<?php echo $reportconfigurationid; ?>"><i class="fa fa-cog" aria-hidden="true"></i></a>
                    </div>
                    <div class="disInline selectfilter">
                        <select id="leadsource-filter" name="leadsource-filter" class="selectpicker" data-width="100%" data-none-selected-text="Select" data-live-search="true" tabindex="-98">
                            <?php
                            foreach ($filters as $filter) {
                                ?>
                                <option value="<?php echo $filter['filtervalue'];?>" <?php if($report_data->saved_filter == $filter['filtervalue']) { echo 'selected="selected"';} ?>><?php echo $filter['filtername']; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="disInline cusfilter">
                        <?php
                        if($report_data->saved_filter == 'custom') {
                            $class = 'form-control';
                        } else {
                            $class = 'form-control hide';
                        }
                        ?>
                        <input type="text" class="<?php echo $class; ?>" id="leadsource_date" name="leadsource_date" data-reportleadid="<?php echo $reportconfigurationid; ?>" placeholder="Select Dates" />
                    </div>
                </div>
            </div>
            <div  class="table-responsive col-sm-12 ">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th class="text-center"><?php echo strtoupper(_l('leadsource_title')); ?></th>
                        <th class="text-center"><?php echo strtoupper(_l('leads')); ?></th>
                        <th class="text-center"><?php echo strtoupper(_l('leads'))." %"; ?></th>
                        <th class="text-center"><?php echo strtoupper(_l('booked')); ?></th>
                        <th class="text-center"><?php echo strtoupper(_l('booking'))." %"; ?></th>
                        <th class="text-center"><?php echo strtoupper(_l('total_revenue')); ?></th>
                        <th class="text-center"><?php echo strtoupper(_l('avg_revenue')); ?></th>
                        <th class="text-center"><?php echo strtoupper(_l('avg_time_to_booking')); ?></th>
                        <th class="text-center"><?php echo strtoupper(_l('avg_time_to_project')); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(count($lead_sources) > 0) {
                        $tot_leads = $tot_booking = $tot_revenue = $avg_revenue = $avg_time_booking = $avg_time_project = 0;
                        foreach ($lead_sources as $sources) {
                            $tot_leads += $sources['no_of_leads'];
                            $tot_booking += $sources['total_booking'];
                            $tot_revenue += $sources['total_value'];
                            $avg_revenue += $sources['avg_value'];
                            $avg_time_booking += $sources['avg_time_to_booking'];
                            $avg_time_project += $sources['avg_time_to_project'];
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $sources['name']; ?></td>
                                <td class="text-center"><?php echo $sources['no_of_leads']; ?></td>
                                <td class="text-center"><?php echo $sources['lead_percent']; ?></td>
                                <td class="text-center"><?php echo $sources['total_booking']; ?></td>
                                <td class="text-center"><?php echo $sources['booking_percent']."%"; ?></td>
                                <td class="text-center"><?php echo "$".$sources['total_value']; ?></td>
                                <td class="text-center"><?php echo "$".$sources['avg_value']; ?></td>
                                <td class="text-center"><?php echo $sources['avg_time_to_booking']." days"; ?></td>
                                <td class="text-center"><?php echo $sources['avg_time_to_project']." days"; ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr><td colspan="9" class="text-center"><?php echo _l('no_records_found'); ?></td></tr>
                        <?php
                    }
                    ?>
                    </tbody>
                    <?php
                    if(count($lead_sources) > 0) {
                        ?>
                        <tfoot>
                        <tr>
                            <td class="text-center"></td>
                            <td class="text-center"><?php echo $tot_leads; ?></td>
                            <td class="text-center"></td>
                            <td class="text-center"><?php echo $tot_booking; ?></td>
                            <td class="text-center"></td>
                            <td class="text-center"><?php echo "$".$tot_revenue; ?></td>
                            <td class="text-center"><?php echo "$".$avg_revenue; ?></td>
                            <td class="text-center"><?php echo $avg_time_booking." days"; ?></td>
                            <td class="text-center"><?php echo $avg_time_project." days"; ?></td>
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
        if(!empty($categories)) {
        ?>
        $(function() {
            (function (H) {
                H.wrap(H.Renderer.prototype, 'label', function (proceed, str, x, y, shape, anchorX, anchorY, useHTML) {
                    if(/class="fa/.test(str)) useHTML = true;
                    // Run original proceed method
                    return proceed.apply(this, [].slice.call(arguments, 1));
                });
            }(Highcharts));
            // Create the chart
            Highcharts.chart('lead-source-container', {
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
                    name: 'Lead Source',
                    colorByPoint: true,
                    data: [<?php echo join($data, ',') ?>]
                }]
            });
        });
        <?php } ?>

        <?php if($report_data->saved_filter == 'custom') { ?>
        $('input[name="leadsource_date"]').daterangepicker({
            locale: {
                format: 'MM/DD/YYYY'
            },
            clearBtn: true,
            startDate: '<?php echo date("m/d/Y",strtotime($report_data->start_date)); ?>',
            endDate: '<?php echo date("m/d/Y",strtotime($report_data->end_date)); ?>'
        });
        <?php } ?>

        <?php if(isset($report_data->saved_filter)) { ?>
        $('#leadsource-filter').val('<?php echo $report_data->saved_filter; ?>');
        $('.selectpicker').selectpicker('refresh')
        <?php } ?>

        //lead source filter change
        $('#leadsource-filter').change(function(){
            var filter_val  = $(this).val();
            var configid    = $('#reportleadid').val();

            //if filter is predefined
            if(filter_val != 'custom') {
                $('#leadsource_date').addClass('hide');
                $.ajax({
                    url: "<?php echo admin_url('reports/filter_leadsource'); ?>",
                    method: "POST",
                    type: "html",
                    data: 'saved_filter='+filter_val+'&reportconfigurationid='+configid+'&type=detailed'
                }).done(function(data){
                    //console.log(data);
                    $("#leadsource-div").html(data);
                    $("#leadsource-filter").val(filter_val);
                    $(".selectpicker").selectpicker('refresh');
                });
            } else {
                //if filter is custom, get start date and endate
                $('#leadsource_date').removeClass('hide');
                $('input[name="leadsource_date"]').daterangepicker({
                    locale: {
                        format: 'MM/DD/YYYY'
                    },
                    clearBtn: true
                });

                /**
                 * to clear view leadsource date filter on cancel button
                 */
                $('#leadsource_date').on('cancel.daterangepicker', function(ev, picker) {
                    //do something, like clearing an input
                    $('#leadsource_date').val('');
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_leadsource'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=all&reportconfigurationid='+configid+'&type=detailed'
                    }).done(function(data){
                        //console.log(data);
                        $("#leadsource-div").html(data);
                        $("#leadsource-filter").val('all');
                        $(".selectpicker").selectpicker('refresh');
                    });
                });

                $('.applyBtn').click(function(){
                    //generate chart and table on setting start and end date
                    var configid = $('#leadsource_date').attr('data-reportleadid');
                    var startDate = $('#leadsource_date').data('daterangepicker').startDate._d;
                    var endDate = $('#leadsource_date').data('daterangepicker').endDate._d;
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_leadsource'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=custom&reportconfigurationid='+configid+'&startDate='+startDate+'&endDate='+endDate+'&type=detailed'
                    }).done(function(data){
                        //console.log(data);
                        $("#leadsource-div").html(data);
                        $("#leadsource-filter").val(filter_val);
                        $(".selectpicker").selectpicker('refresh');
                        $('#leadsource_date').removeClass('hide');
                        $('input[name="leadsource_date"]').daterangepicker({
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
<div class="modal fade" id="source_display_settings" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('report_settings'); ?>
                </h4>
            </div>
            <?php echo form_open('admin/reports/update_setting',array('novalidate' => true,'id'=>'report-setting')); ?>
            <input type="hidden" name="reportconfigurationid" id="reportconfigurationid" value="<?php echo $reportconfigurationid; ?>">
            <input type="hidden" name="type" id="type" value="leadstatus">
            <div class="modal-body">
                <?php
                $sesssion_data = get_session_data();
                if($sesssion_data['user_type'] == 1) {
                    ?>
                    <div class="row">
                        <div class="form-group">
                            <div class="checkbox checkbox-primary mtop25">
                                <input type="checkbox" name="source_report_permission" id="source_report_permission">
                                <label for="include_share_permission"><?php echo _l('include_share_permission'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div id="source-sharing-permission">
                        <div class="row">
                            <div class="form-group">
                                <select id="sharing_permission" name="sharing_permission[]" class="selectpicker" multiple="multiple">
                                    <option value=""></option>
                                    <?php
                                    $role_arr = explode(",", $report_data->sharing_permission);
                                    foreach ($roles as $role) {
                                        if(in_array(trim($role['roleid']), $role_arr)) {
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
                        <div class="checkbox checkbox-primary mtop25">
                            <input type="checkbox" name="source_no_of_records" id="source_no_of_records">
                            <label for="default_records"><?php echo _l('default_records'); ?></label>
                        </div>
                    </div>
                </div>
                <div id="source-no-of-records">
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
    $(document).ready(function(){
        $('#source_display_report_settings').click(function(){
            $("#reportconfigurationid").val($(this).data('reportleadid'));
            $('#source_display_settings').modal('show');
        });

        $('#source-no-of-records').hide();

        <?php
        $sesssion_data = get_session_data();
        if($sesssion_data['user_type'] == 1) {
        ?>
        $('#source-sharing-permission').hide();

        $('#source_report_permission').click(function(){
            if($('#source_report_permission').is(':checked')) {
                $('#source-sharing-permission').show()
            } else {
                $('#source-sharing-permission').hide();
            }
        });
        <?php } ?>

        $('#source_no_of_records').click(function(){
            if($('#source_no_of_records').is(':checked')) {
                $('#source-no-of-records').show()
            } else {
                $('#source-no-of-records').hide();
            }
        });
    });
</script>