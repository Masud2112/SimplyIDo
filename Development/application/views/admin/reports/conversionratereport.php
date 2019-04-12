<?php
//show report on main report screen
if (!$detailed_report) {
    ?>
    <h4 class="sub-title"><?php echo _l('conversionrate_title'); ?></h4>
    <div class="pull-right">
        <input type="hidden" name="reportconversionid" id="reportconversionid"
               value="<?php echo $reportconfigurationid; ?>">
        <div class="disInline repSetting">
            <a href="javascript:void(0)" data-toggle="modal" id="conversion_display_report_settings"
               data-reportconversionid="<?php echo $reportconfigurationid; ?>"><i class="fa fa-cog"
                                                                                  aria-hidden="true"></i></a>
        </div>
        <div class="disInline selectfilter">
            <?php echo render_select('conversionrate-filter', $filters, array('filtervalue', 'filtername'), '', (isset($report_data->saved_filter) ? $report_data->saved_filter : '')); ?>
        </div>
        <div class="disInline cusfilter">
            <input type="text" class="form-control hide" id="conversionrate_date" name="conversionrate_date"
                   data-reportconversionid="<?php echo $reportconfigurationid; ?>" placeholder="Select Dates"/>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        <?php
        $conversionrate_text_title="Sign Ups To Subcribers";
        //if report is shown in configuration
        if ($has_permission_conversion_rate == 1) {
            if ($report_data->is_visible == 1) {
                $is_conversionrate = 1;
                if (count($conversionrates) > 0) {
                    $tot_conversionrate = 0;
                    foreach ($conversionrates as $conversionrate) {
                        $tot_conversionrate += $conversionrate['subscribers'];
                        $conversion = !empty($conversionrate['conversion']) ? $conversionrate['conversion'] : 0;
                        $conversionrate_name = $conversionrate['name'] . " : " . $conversionrate['signups'] . "<br/> ( " . $conversion . "% ) ";
                        $conversionrate_data[] = "['" . $conversionrate_name . "', " . $conversion . "]";
                    }
                    $conversionrate_text_title = $tot_conversionrate . "<br/> <small>Sign Ups To Subcribers</small>";
                    $conversionrate_text_title = $tot_conversionrate . " Sign Ups To Subcribers";
                }
                ?>
                <!--Report in Chart-->
                <div id="conversionrate-container" class="chart"></div>
                <div class="widget-heading">
                    <div class="more-setting">
                        <a href="javascript:void(0)" id="expand-conversionrate" data-target="#conversionrate-table"
                           id="conversionrate-collapse"><span id="conversionrate-span">HIDE DETAIL </span><i
                                    class="fa fa-caret-up"></i></a>
                    </div>
                </div>
                <!--Report in Table Form-->
                <h3 class="text-center"><?php echo $conversionrate_text_title; ?></h3>
                <div id="conversionrate-table">
                    <table class="table table-striped table-conversionrate">
                        <thead>
                        <tr>
                            <th class="text-center"><?php echo strtoupper(_l('vendor_type')); ?></th>
                            <th class="text-center"><?php echo strtoupper(_l('signups')); ?></th>
                            <th class="text-center"><?php echo strtoupper(_l('subscribers')); ?></th>
                            <th class="text-center"><?php echo strtoupper(_l('conversion')); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (count($conversionrates) > 0) {
                            foreach ($conversionrates as $conversionrate) {
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $conversionrate['name']; ?></td>
                                    <td class="text-center"><?php echo $conversionrate['signups']; ?></td>
                                    <td class="text-center"><?php echo $conversionrate['subscribers']; ?></td>
                                    <td class="text-center"><?php echo $conversionrate['conversion'] . "%"; ?></td>
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
                        <?php
                        if (count($conversionrates) > 0) {
                            ?>
                            <!-- <div class="loadmore_section">
                  <a href="#" id="leadstatus_loadMore" class="btn btn-info pull-left active display-block mright10 loadMore">(<?php //echo count($conversionrates); ?>)<span class="all_pin_data_only_count">Load More</a>
                </div> -->
                        <?php } ?>
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
    <script type="text/javascript">
        <?php
        //collapse-expand report
        if(isset($is_conversionrate)) {
        ?>
        //for conversion rate
        $('#expand-conversionrate').click(function () {
            var target = $(this).attr('data-target');
            $(target).slideToggle()
            $('#conversionrate-span').text($('#conversionrate-span').text() == 'HIDE DETAIL ' ? 'SHOW DETAIL ' : 'HIDE DETAIL ');
            $(this).find('i').toggleClass('fa-caret-up fa-caret-down');
        });
        <?php } ?>

        <?php
        //generate dynamic chart
        if(isset($is_conversionrate) && count($conversionrates) > 0) {
        ?>
        $(function () {
            (function (H) {
                H.wrap(H.Renderer.prototype, 'label', function (proceed, str, x, y, shape, anchorX, anchorY, useHTML) {
                    if (/class="fa/.test(str)) useHTML = true;
                    // Run original proceed method
                    return proceed.apply(this, [].slice.call(arguments, 1));
                });
            }(Highcharts));
            //for conversion rate
            var chart = new Highcharts.chart('conversionrate-container', {
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
                                        this.series.chart.innerText.attr({text: '<?php //echo $conversionrate_text_title; ?>'});
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
                        name: 'conversion',
                        data: [<?php echo join($conversionrate_data, ',') ?>]
                    }]
                },
                function (chart) { // on complete
                    var xpos = '55%';
                    var ypos = '58%';
                    var circleradius = 200;

                    // Render the text
                    chart.innerText = chart.renderer.text('<?php //echo $conversionrate_text_title; ?>', 230, 220).css({
                        width: circleradius * 2,
                        color: '#000',
                        fontSize: '16px',
                        fontWeight: 600,
                        textAlign: 'center'
                    }).attr({
                        // why doesn't zIndex get the text in front of the chart?
                        zIndex: 999
                    }).add();
                });
        });
        <?php } else { ?>
        $('#conversionrate-container').html('<span class="chart-norecord">No Records Found</span>');
        <?php } ?>

        <?php if(isset($report_data->saved_filter)) { ?>
        $('#conversionrate-filter').val('<?php echo $report_data->saved_filter; ?>');
        $('.selectpicker').selectpicker('refresh')
        <?php } ?>

        <?php if($report_data->saved_filter == 'custom') { ?>
        $('input[name="conversionrate_date"]').daterangepicker({
            locale: {
                format: 'MM/DD/YYYY'
            },
            clearBtn: true,
            startDate: '<?php echo date("m/d/Y", strtotime($report_data->start_date)); ?>',
            endDate: '<?php echo date("m/d/Y", strtotime($report_data->end_date)); ?>'
        });
        <?php } ?>

        //generate chart and table based on conversionrate filter change
        $('#conversionrate-filter').change(function () {
            var filter_val = $(this).val();
            var configid = $('#reportconversionid').val();

            //if filter is predefined
            if (filter_val != 'custom') {
                $.ajax({
                    url: "<?php echo admin_url('reports/filter_conversionrate'); ?>",
                    method: "POST",
                    type: "html",
                    data: "saved_filter=" + filter_val + '&reportconfigurationid=' + configid
                }).done(function (data) {
                    //console.log(data);
                    $("#conversionrate-div").html(data);
                    $("#conversionrate-filter").val(filter_val);
                    $(".selectpicker").selectpicker('refresh');
                });
            } else {
                //if filter is custom, get start date and endate
                $('#subscriber_date').removeClass('hide');
                $('input[name="subscriber_date"]').daterangepicker({
                    locale: {
                        format: 'MM/DD/YYYY'
                    },
                    clearBtn: true
                });

                /**
                 * to clear view conversion rate date filter on cancel button
                 */
                $('#conversionrate_date').on('cancel.daterangepicker', function (ev, picker) {
                    //do something, like clearing an input
                    $('#conversionrate_date').val('');
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_conversionrate'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=all&reportconfigurationid=' + configid
                    }).done(function (data) {
                        //console.log(data);
                        $("#conversionrate-div").html(data);
                        $("#conversionrate-filter").val('all');
                        $(".selectpicker").selectpicker('refresh');
                    });
                });

                $('.applyBtn').click(function () {
                    //generate chart and table on setting start and end date
                    var configid = $('#conversionrate_date').attr('data-reportconversionid');
                    var startDate = $('#conversionrate_date').data('daterangepicker').startDate._d;
                    var endDate = $('#conversionrate_date').data('daterangepicker').endDate._d;
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_conversionrate'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=custom&reportconfigurationid=' + configid + '&startDate=' + startDate + '&endDate=' + endDate
                    }).done(function (data) {
                        //console.log(data);
                        $("#conversionrate-div").html(data);
                        $("#conversionrate-filter").val(filter_val);
                        $(".selectpicker").selectpicker('refresh');
                        $('#conversionrate_date').removeClass('hide');
                        $('input[name="conversionrate_date"]').daterangepicker({
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

        initReportDataTableOffline('.table-conversionrate', <?php echo $report_data->default_records; ?>);
    </script>
<?php } ?>

<!--
  * Added by: Vaidehi
  * Date: 03/17/2018
  * Popup to display setting option
  -->
<div class="modal fade" id="conversionrate_display_settings" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
            <input type="hidden" name="type" id="type" value="subscriber">
            <div class="modal-body">
                <?php
                $get_session_data = get_session_data();
                if ($get_session_data['user_type'] == 1) {
                    ?>
                    <div class="row">
                        <div class="form-group">
                            <div class="checkbox checkbox-primary mtop25">
                                <input type="checkbox" name="conversionrate_report_permission"
                                       id="conversionrate_report_permission">
                                <label for="include_share_permission"><?php echo _l('include_share_permission'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div id="conversionrate-sharing-permission">
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
                        <div class="checkbox checkbox-primary mtop25">
                            <input type="checkbox" name="conversionrate_no_of_records"
                                   id="conversionrate_no_of_records">
                            <label for="default_records"><?php echo _l('default_records'); ?></label>
                        </div>
                    </div>
                </div>
                <div id="conversionrate-no-of-records">
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
    $('#conversion_display_report_settings').click(function () {
        $("#reportconfigurationid").val($(this).data('reportconversionid'));
        $('#conversionrate_display_settings').modal('show');
    });

    $('#conversionrate-no-of-records').hide();

    <?php
    $get_session_data = get_session_data();
    if($get_session_data['user_type'] == 1) {
    ?>
    $('#conversionrate-sharing-permission').hide();

    $('#conversionrate_report_permission').click(function () {
        if ($('#conversionrate_report_permission').is(':checked')) {
            $('#conversionrate-sharing-permission').show()
        } else {
            $('#conversionrate-sharing-permission').hide();
        }
    });
    <?php } ?>

    $('#conversionrate_no_of_records').click(function () {
        if ($('#conversionrate_no_of_records').is(':checked')) {
            $('#conversionrate-no-of-records').show()
        } else {
            $('#conversionrate-no-of-records').hide();
        }
    });
</script>