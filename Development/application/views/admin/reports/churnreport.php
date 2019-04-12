<?php
//show report on main report screen
if (!$detailed_report) {
    ?>
    <h4 class="sub-title"><?php echo _l('churn_title'); ?></h4>
    <div class="pull-right">
        <input type="hidden" name="reportchurnid" id="reportchurnid" value="<?php echo $reportconfigurationid; ?>">
        <div class="disInline repSetting">
            <a href="javascript:void(0)" data-toggle="modal" id="churn_display_report_settings"
               data-reportchurnid="<?php echo $reportconfigurationid; ?>"><i class="fa fa-cog"
                                                                             aria-hidden="true"></i></a>
        </div>
        <div class="disInline selectfilter">
            <?php echo render_select('churn-filter', $filters, array('filtervalue', 'filtername'), '', (isset($report_data->saved_filter) ? $report_data->saved_filter : '')); ?>
        </div>
        <div class="disInline cusfilter">
            <input type="text" class="form-control hide" id="churn_date" name="churn_date"
                   data-reportchurnid="<?php echo $reportconfigurationid; ?>" placeholder="Select Dates"/>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        <?php
        $churn_text_title="Churn";
        //if report is shown in configuration
        if ($has_permission_churn == 1) {
            if ($report_data->is_visible == 1) {
                $is_churn = 1;
                if (count($churns) > 0) {
                    $tot_churn = 0;
                    foreach ($churns as $churn) {
                        $tot_churn += $churn['churns'];
                        $churn_percent = !empty($churn['churn_percent']) ? $churn['churn_percent'] : 0;
                        $churn_name = $churn['name'] . " : " . $churn['churns'] . "<br/> ( " . $churn_percent . "% ) ";
                        $churn_data[] = "['" . $churn_name . "', " . $churn_percent . "]";
                    }
                    $churn_text_title = $tot_churn . "<br/> <small>Churn</small>";
                    $churn_text_title = $tot_churn . " Churn";
                }
                ?>
                <!--Report in Chart-->
                <div id="churn-container" class="chart"></div>
                <div class="widget-heading">
                    <div class="more-setting">
                        <a href="javascript:void(0)" id="expand-churn" data-target="#churn-table"
                           id="churn-collapse"><span id="churn-span">HIDE DETAIL</span><i class="fa fa-caret-up"></i></a>
                    </div>
                </div>
                <!--Report in Table Form-->
                <h3 class="text-center"><?php echo $churn_text_title; ?></h3>
                <div id="churn-table">
                    <table class="table table-striped table-churn">
                        <thead>
                        <tr>
                            <th class="text-center"><?php echo strtoupper(_l('vendor_type')); ?></th>
                            <th class="text-center">#</th>
                            <th class="text-center">%</th>
                            <th class="text-center"><?php echo strtoupper(_l('avg_time_to_cancel')); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (count($churns) > 0) {
                            foreach ($churns as $churn) {
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $churn['name']; ?></td>
                                    <td class="text-center"><?php echo $churn['churns']; ?></td>
                                    <td class="text-center"><?php echo $churn['churn_percent']; ?></td>
                                    <td class="text-center"><?php echo $churn['avg_time_to_cancel']; ?></td>
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
                        if (count($churns) > 0) {
                            ?>
                            <!-- <div class="loadmore_section">
                  <a href="#" id="leadstatus_loadMore" class="btn btn-info pull-left active display-block mright10 loadMore">(<?php //echo count($signups); ?>)<span class="all_pin_data_only_count">Load More</a>
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
        if(isset($is_churn)) {
        ?>
        //for churn
        $('#expand-churn').click(function () {
            var target = $(this).attr('data-target');
            $(target).slideToggle()
            $('#churn-span').text($('#churn-span').text() == 'HIDE DETAIL' ? 'SHOW DETAIL' : 'HIDE DETAIL');
            $(this).find('i').toggleClass('fa-caret-up fa-caret-down');
        });
        <?php } ?>

        <?php
        //generate dynamic chart
        if(isset($is_churn) && !empty($churn_data)) {
        ?>
        $(function () {
            (function (H) {
                H.wrap(H.Renderer.prototype, 'label', function (proceed, str, x, y, shape, anchorX, anchorY, useHTML) {
                    if (/class="fa/.test(str)) useHTML = true;
                    // Run original proceed method
                    return proceed.apply(this, [].slice.call(arguments, 1));
                });
            }(Highcharts));
            //for churn
            var chart = new Highcharts.chart('churn-container', {
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
                                        this.series.chart.innerText.attr({text: '<?php //echo $churn_text_title; ?>'});
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
                        name: 'Churn',
                        data: [<?php echo join($churn_data, ',') ?>]
                    }]
                },
                function (chart) { // on complete
                    var xpos = '55%';
                    var ypos = '58%';
                    var circleradius = 200;

                    // Render the text
                    chart.innerText = chart.renderer.text('<?php //echo $churn_text_title; ?>', 230, 220).css({
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
        $('#churn-container').html('<span class="chart-norecord">No Records Found</span>');
        <?php } ?>

        <?php if(isset($report_data->saved_filter)) { ?>
        $('#churn-filter').val('<?php echo $report_data->saved_filter; ?>');
        $('.selectpicker').selectpicker('refresh')
        <?php } ?>

        <?php if($report_data->saved_filter == 'custom') { ?>
        $('input[name="churn_date"]').daterangepicker({
            locale: {
                format: 'MM/DD/YYYY'
            },
            clearBtn: true,
            startDate: '<?php echo date("m/d/Y", strtotime($report_data->start_date)); ?>',
            endDate: '<?php echo date("m/d/Y", strtotime($report_data->end_date)); ?>'
        });
        <?php } ?>

        //generate chart and table based on churn filter change
        $('#churn-filter').change(function () {
            var filter_val = $(this).val();
            var configid = $('#reportchurnid').val();

            //if filter is predefined
            if (filter_val != 'custom') {
                $.ajax({
                    url: "<?php echo admin_url('reports/filter_churn'); ?>",
                    method: "POST",
                    type: "html",
                    data: "saved_filter=" + filter_val + '&reportconfigurationid=' + configid
                }).done(function (data) {
                    //console.log(data);
                    $("#churn-div").html(data);
                    $("#churn-filter").val(filter_val);
                    $(".selectpicker").selectpicker('refresh');
                });
            } else {
                //if filter is custom, get start date and endate
                $('#churn_date').removeClass('hide');
                $('input[name="churn_date"]').daterangepicker({
                    locale: {
                        format: 'MM/DD/YYYY'
                    },
                    clearBtn: true
                });

                /**
                 * to clear view churn date filter on cancel button
                 */
                $('#churn_date').on('cancel.daterangepicker', function (ev, picker) {
                    //do something, like clearing an input
                    $('#churn_date').val('');
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_churn'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=all&reportconfigurationid=' + configid
                    }).done(function (data) {
                        //console.log(data);
                        $("#churn-div").html(data);
                        $("#churn-filter").val('all');
                        $(".selectpicker").selectpicker('refresh');
                    });
                });

                $('.applyBtn').click(function () {
                    //generate chart and table on setting start and end date
                    var configid = $('#churn_date').attr('data-reportchurnid');
                    var startDate = $('#churn_date').data('daterangepicker').startDate._d;
                    var endDate = $('#churn_date').data('daterangepicker').endDate._d;
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_churn'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=custom&reportconfigurationid=' + configid + '&startDate=' + startDate + '&endDate=' + endDate
                    }).done(function (data) {
                        //console.log(data);
                        $("#churn-div").html(data);
                        $("#churn-filter").val(filter_val);
                        $(".selectpicker").selectpicker('refresh');
                        $('#signup_date').removeClass('hide');
                        $('input[name="churn_date"]').daterangepicker({
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

        initReportDataTableOffline('.table-churn', <?php echo $report_data->default_records; ?>);
    </script>
<?php } ?>

<!--
  * Added by: Vaidehi
  * Date: 03/17/2018
  * Popup to display setting option
  -->
<div class="modal fade" id="churn_display_settings" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
            <input type="hidden" name="type" id="type" value="churn">
            <div class="modal-body">
                <?php
                $get_session_data = get_session_data();
                if ($get_session_data['user_type'] == 1) {
                    ?>
                    <div class="row">
                        <div class="form-group">
                            <div class="checkbox checkbox-primary mtop25">
                                <input type="checkbox" name="churn_report_permission" id="churn_report_permission">
                                <label for="include_share_permission"><?php echo _l('include_share_permission'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div id="churn-sharing-permission">
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
                            <input type="checkbox" name="churn_no_of_records" id="churn_no_of_records">
                            <label for="default_records"><?php echo _l('default_records'); ?></label>
                        </div>
                    </div>
                </div>
                <div id="churn-no-of-records">
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
    $('#churn_display_report_settings').click(function () {
        $("#reportconfigurationid").val($(this).data('reportchurnid'));
        $('#churn_display_settings').modal('show');
    });

    $('#churn-no-of-records').hide();

    <?php
    $get_session_data = get_session_data();
    if($get_session_data['user_type'] == 1) {
    ?>
    $('#churn-sharing-permission').hide();

    $('#churn_report_permission').click(function () {
        if ($('#churn_report_permission').is(':checked')) {
            $('#churn-sharing-permission').show()
        } else {
            $('#churn-sharing-permission').hide();
        }
    });
    <?php } ?>

    $('#churn_no_of_records').click(function () {
        if ($('#churn_no_of_records').is(':checked')) {
            $('#churn-no-of-records').show()
        } else {
            $('#churn-no-of-records').hide();
        }
    });
</script>