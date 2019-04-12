<?php
//show report on main report screen
if (!$detailed_report) {
    ?>
    <h4 class="sub-title"><?php echo _l('signup_title'); ?></h4>
    <div class="pull-right">
        <input type="hidden" name="reportsignupid" id="reportsignupid" value="<?php echo $reportconfigurationid; ?>">
        <div class="disInline repSetting">
            <a href="javascript:void(0)" data-toggle="modal" id="signup_display_report_settings"
               data-reportsignupid="<?php echo $reportconfigurationid; ?>"><i class="fa fa-cog" aria-hidden="true"></i></a>
        </div>
        <div class="disInline selectfilter">
            <?php echo render_select('signup-filter', $filters, array('filtervalue', 'filtername'), '', (isset($report_data->saved_filter) ? $report_data->saved_filter : '')); ?>
        </div>
        <div class="disInline cusfilter">
            <input type="text" class="form-control hide" id="signup_date" name="signup_date"
                   data-reportsignupid="<?php echo $reportconfigurationid; ?>" placeholder="Select Dates"/>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="panel-body">
        <?php
        $signup_text_title = "Sign Up";
        //if report is shown in configuration
        if ($has_permission_signup == 1) {
            if ($report_data->is_visible == 1) {
                $is_signup = 1;
                if (count($signups) > 0) {
                    $tot_signup = 0;
                    foreach ($signups as $signup) {
                        $tot_signup += $signup['signups'];
                        $signup_percent = !empty($signup['signup_percent']) ? $signup['signup_percent'] : 0;
                        $signup_name = $signup['name'] . " : " . $signup['signups'] . "<br/> ( " . $signup_percent . "% ) ";
                        $signup_data[] = "['" . $signup_name . "', " . $signup_percent . "]";
                    }
                    $signup_text_title = $tot_signup . "<br/> <small>Sign Up</small>";
                    $signup_text_title = $tot_signup . " Sign Up";
                }
                ?>
                <!--Report in Chart-->
                <div id="signup-container" class="chart"></div>
                <div class="widget-heading">
                    <div class="more-setting">
                        <a href="javascript:void(0)" id="expand-signup" data-target="#signup-table"
                           id="signup-collapse"><span id="signup-span">HIDE DETAIL </span></span><i
                                    class="fa fa-caret-up"></i></a>
                    </div>
                </div>
                <!--Report in Table Form-->
                <h3 class="text-center"><?php echo isset($signup_text_title)?$signup_text_title:"Sign Up"; ?></h3>
                <div id="signup-table">
                    <table class="table table-striped table-signup">
                        <thead>
                        <tr>
                            <th class="text-center"><?php echo strtoupper(_l('vendor_type')); ?></th>
                            <th class="text-center">#</th>
                            <th class="text-center">%</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (count($signups) > 0) {
                            foreach ($signups as $signup) {
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $signup['name']; ?></td>
                                    <td class="text-center"><?php echo $signup['signups']; ?></td>
                                    <td class="text-center"><?php echo $signup['signup_percent']; ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="3" class="text-center"><?php echo _l('no_records_found'); ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    <div>
                        <?php
                        if (count($signups) > 0) {
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
        if(isset($is_signup)) {
        ?>
        //for signup
        $('#expand-signup').click(function () {
            var target = $(this).attr('data-target');
            $(target).slideToggle()
            $('#signup-span').text($('#signup-span').text() == 'HIDE DETAIL ' ? 'SHOW DETAIL ' : 'HIDE DETAIL ');
            $(this).find('i').toggleClass('fa-caret-up fa-caret-down');
        });
        <?php } ?>

        <?php
        //generate dynamic chart
        if(isset($is_signup) && !empty($signup_data)) {
        ?>
        $(function () {
            (function (H) {
                H.wrap(H.Renderer.prototype, 'label', function (proceed, str, x, y, shape, anchorX, anchorY, useHTML) {
                    if (/class="fa/.test(str)) useHTML = true;
                    // Run original proceed method
                    return proceed.apply(this, [].slice.call(arguments, 1));
                });
            }(Highcharts));
            //for signup
            var chart = new Highcharts.chart('signup-container', {
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
                                        this.series.chart.innerText.attr({text: '<?php //echo $signup_text_title; ?>'});
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
                        name: 'SignUp',
                        data: [<?php echo join($signup_data, ',') ?>]
                    }]
                },
                function (chart) { // on complete
                    var xpos = '55%';
                    var ypos = '58%';
                    var circleradius = 200;

                    // Render the text
                    chart.innerText = chart.renderer.text('<?php //echo $signup_text_title; ?>', 230, 220).css({
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
        $('#signup-container').html('<span class="chart-norecord">No Records Found</span>');
        <?php } ?>

        <?php if(isset($report_data->saved_filter)) { ?>
        $('#signup-filter').val('<?php echo $report_data->saved_filter; ?>');
        $('.selectpicker').selectpicker('refresh')
        <?php } ?>

        <?php if($report_data->saved_filter == 'custom') { ?>
        $('input[name="signup_date"]').daterangepicker({
            locale: {
                format: 'MM/DD/YYYY'
            },
            clearBtn: true,
            startDate: '<?php echo date("m/d/Y", strtotime($report_data->start_date)); ?>',
            endDate: '<?php echo date("m/d/Y", strtotime($report_data->end_date)); ?>'
        });
        <?php } ?>

        //generate chart and table based on signup filter change
        $('#signup-filter').change(function () {
            var filter_val = $(this).val();
            var configid = $('#reportsignupid').val();

            //if filter is predefined
            if (filter_val != 'custom') {
                $.ajax({
                    url: "<?php echo admin_url('reports/filter_signup'); ?>",
                    method: "POST",
                    type: "html",
                    data: "saved_filter=" + filter_val + '&reportconfigurationid=' + configid
                }).done(function (data) {
                    //console.log(data);
                    $("#signup-div").html(data);
                    $("#signup-filter").val(filter_val);
                    $(".selectpicker").selectpicker('refresh');
                });
            } else {
                //if filter is custom, get start date and endate
                $('#signup_date').removeClass('hide');
                $('input[name="signup_date"]').daterangepicker({
                    locale: {
                        format: 'MM/DD/YYYY'
                    },
                    clearBtn: true
                });

                /**
                 * to clear view signup date filter on cancel button
                 */
                $('#signup_date').on('cancel.daterangepicker', function (ev, picker) {
                    //do something, like clearing an input
                    $('#signup_date').val('');
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_signup'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=all&reportconfigurationid=' + configid
                    }).done(function (data) {
                        //console.log(data);
                        $("#signup-div").html(data);
                        $("#signup-filter").val('all');
                        $(".selectpicker").selectpicker('refresh');
                    });
                });

                $('.applyBtn').click(function () {
                    //generate chart and table on setting start and end date
                    var configid = $('#signup_date').attr('data-reportsignupid');
                    var startDate = $('#signup_date').data('daterangepicker').startDate._d;
                    var endDate = $('#signup_date').data('daterangepicker').endDate._d;
                    $.ajax({
                        url: "<?php echo admin_url('reports/filter_signup'); ?>",
                        method: "POST",
                        type: "html",
                        data: 'saved_filter=custom&reportconfigurationid=' + configid + '&startDate=' + startDate + '&endDate=' + endDate
                    }).done(function (data) {
                        //console.log(data);
                        $("#signup-div").html(data);
                        $("#signup-filter").val(filter_val);
                        $(".selectpicker").selectpicker('refresh');
                        $('#signup_date').removeClass('hide');
                        $('input[name="signup_date"]').daterangepicker({
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

        initReportDataTableOffline('.table-signup', <?php echo $report_data->default_records; ?>);
    </script>
<?php } ?>

<!--
  * Added by: Vaidehi
  * Date: 03/17/2018
  * Popup to display setting option
  -->
<div class="modal fade" id="signup_display_settings" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
            <input type="hidden" name="type" id="type" value="signup">
            <div class="modal-body">
                <?php
                $get_session_data = get_session_data();
                if ($get_session_data['user_type'] == 1) {
                    ?>
                    <div class="row">
                        <div class="form-group">
                            <div class="checkbox checkbox-primary mtop25">
                                <input type="checkbox" name="signup_report_permission" id="signup_report_permission">
                                <label for="include_share_permission"><?php echo _l('include_share_permission'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div id="signup-sharing-permission">
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
                            <input type="checkbox" name="signup_no_of_records" id="signup_no_of_records">
                            <label for="default_records"><?php echo _l('default_records'); ?></label>
                        </div>
                    </div>
                </div>
                <div id="signup-no-of-records">
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
    $('#signup_display_report_settings').click(function () {
        $("#reportconfigurationid").val($(this).data('reportsignupid'));
        $('#signup_display_settings').modal('show');
    });

    $('#signup-no-of-records').hide();

    <?php
    $get_session_data = get_session_data();
    if($get_session_data['user_type'] == 1) {
    ?>
    $('#signup-sharing-permission').hide();

    $('#signup_report_permission').click(function () {
        if ($('#signup_report_permission').is(':checked')) {
            $('#signup-sharing-permission').show()
        } else {
            $('#signup-sharing-permission').hide();
        }
    });
    <?php } ?>

    $('#signup_no_of_records').click(function () {
        if ($('#signup_no_of_records').is(':checked')) {
            $('#signup-no-of-records').show()
        } else {
            $('#signup-no-of-records').hide();
        }
    });
</script>