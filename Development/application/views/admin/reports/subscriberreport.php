<?php 
  //show report on main report screen
  if(!$detailed_report) { 
?>
  <h4 class="sub-title"><?php echo _l('subscriber_title'); ?></h4>	
  <div class="pull-right">
    <input type="hidden" name="reportsubscriberid" id="reportsubscriberid" value="<?php echo $reportconfigurationid; ?>">
    <div class="disInline repSetting">
      <a href="javascript:void(0)" data-toggle="modal" id="subscriber_display_report_settings" data-reportsubscriberid="<?php echo $reportconfigurationid; ?>"><i class="fa fa-cog" aria-hidden="true"></i></a>
    </div>
    <div class="disInline selectfilter">
      <?php echo render_select('subscriber-filter', $filters, array('filtervalue','filtername'), '', (isset($report_data->saved_filter) ? $report_data->saved_filter : '')); ?>
    </div>
    <div class="disInline cusfilter">                                   
      <input type="text" class="form-control hide" id="subscriber_date" name="subscriber_date" data-reportsubscriberid="<?php echo $reportconfigurationid; ?>" placeholder="Select Dates" />     
    </div>
  </div>
  <div class="clearfix"></div>  
  <div class="panel-body">
    <?php
    $subscriber_text_title = "Subcribers";
      //if report is shown in configuration
    if($has_permission_subscriber == 1) {
      if($report_data->is_visible == 1) { 
        $is_subscriber = 1; 
        if(count($subscribers) > 0) {
          $tot_subscriber = 0;
          foreach ($subscribers as $subscriber) {
            $tot_subscriber += $subscriber['subscribers'];
            $subscriber_percent = !empty($subscriber['subscriber_percent']) ? $subscriber['subscriber_percent'] : 0;
            $subscriber_name = $subscriber['name'] . " : ". $subscriber['subscribers'] . "<br/> ( " . $subscriber_percent . "% ) ";
            $subscriber_data[] = "['" . $subscriber_name . "', " . $subscriber_percent . "]";
          }
          $subscriber_text_title = $tot_subscriber . "<br/> <small>Subcribers</small>";
            $subscriber_text_title = $tot_subscriber . " Subcribers";
        }
    ?> 
        <!--Report in Chart-->
        <div id="subscriber-container" class="chart"></div>
        <div class="widget-heading">
          <div class="more-setting">
            <a href="javascript:void(0)" id="expand-subscriber" data-target="#subscriber-table" id="subscriber-collapse"><span id="subscriber-span">HIDE DETAIL </span><i class="fa fa-caret-up"></i></a>
          </div>
        </div>
        <!--Report in Table Form-->
          <h3 class="text-center"><?php echo isset($subscriber_text_title)?$subscriber_text_title:"Subcribers"; ?></h3>
        <div id="subscriber-table">
          <table class="table table-striped table-subscriber">
            <thead>
              <tr>
                <th class="text-center"><?php echo strtoupper(_l('vendor_type')); ?></th>
                <th class="text-center">#</th>
                <th class="text-center">%</th>
                <th class="text-center"><?php echo strtoupper(_l('avg_time_to_paid')); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php 
                if(count($subscribers) > 0) {
                  foreach ($subscribers as $subscriber) {
              ?>
                    <tr>
                      <td class="text-center"><?php echo $subscriber['name']; ?></td>
                      <td class="text-center"><?php echo $subscriber['subscribers']; ?></td>
                      <td class="text-center"><?php echo $subscriber['subscriber_percent']; ?></td>
                      <td class="text-center"><?php echo $subscriber['avg_time_to_paid'] ." days"; ?></td>
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
          <div>
            <?php 
              if(count($subscribers) > 0) {
            ?>
                <!-- <div class="loadmore_section">
                  <a href="#" id="leadstatus_loadMore" class="btn btn-info pull-left active display-block mright10 loadMore">(<?php //echo count($subscribers); ?>)<span class="all_pin_data_only_count">Load More</a>
                </div> -->
            <?php } ?>
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
    //collapse-expand report
    if(isset($is_subscriber)) {
  ?>
    //for subscriber
    $('#expand-subscriber').click(function () {
      var target = $(this).attr('data-target');
      $(target).slideToggle()
      $('#subscriber-span').text($('#subscriber-span').text() == 'HIDE DETAIL ' ? 'SHOW DETAIL ' : 'HIDE DETAIL ');
      $(this).find('i').toggleClass('fa-caret-up fa-caret-down');
    });
  <?php } ?>

  <?php
    //generate dynamic chart
    if(isset($is_subscriber) && !empty($subscriber_data)) { ?>
      $(function() {
        (function (H) {
          H.wrap(H.Renderer.prototype, 'label', function (proceed, str, x, y, shape, anchorX, anchorY, useHTML) {
            if(/class="fa/.test(str)) useHTML = true;
            // Run original proceed method
            return proceed.apply(this, [].slice.call(arguments, 1));
          });
        }(Highcharts));
        //for subscriber
        var chart = new Highcharts.chart('subscriber-container', {
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
                    this.series.chart.innerText.attr({text: '<?php //echo $subscriber_text_title; ?>'});
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
            name: 'subscriber',
            data: [<?php echo join($subscriber_data, ',') ?>]
          }]
        },
        function(chart) { // on complete
          var xpos = '55%';
          var ypos = '58%';
          var circleradius = 200;
      
          // Render the text 
          chart.innerText = chart.renderer.text('<?php //echo $subscriber_text_title; ?>', 230, 220).css({
            width: circleradius*2,
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
    $('#subscriber-container').html('<span class="chart-norecord">No Records Found</span>');
  <?php } ?>

  <?php if(isset($report_data->saved_filter)) { ?>
    $('#subscriber-filter').val('<?php echo $report_data->saved_filter; ?>');
    $('.selectpicker').selectpicker('refresh')
  <?php } ?>

  <?php if($report_data->saved_filter == 'custom') { ?>
    $('input[name="subscriber_date"]').daterangepicker({
      locale: {
        format: 'MM/DD/YYYY'
      },
      clearBtn: true,
      startDate: '<?php echo date("m/d/Y",strtotime($report_data->start_date)); ?>',
      endDate: '<?php echo date("m/d/Y",strtotime($report_data->end_date)); ?>'
    });
  <?php } ?>

  //generate chart and table based on subscriber filter change 
  $('#subscriber-filter').change(function(){
    var filter_val = $(this).val();
    var configid = $('#reportsubscriberid').val();

    //if filter is predefined
    if(filter_val != 'custom') {
      $.ajax({
        url: "<?php echo admin_url('reports/filter_subscriber'); ?>",
        method: "POST",
        type: "html",
        data: "saved_filter="+filter_val+'&reportconfigurationid='+configid
      }).done(function(data){
        //console.log(data);
        $("#subscriber-div").html(data);
        $("#subscriber-filter").val(filter_val);
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
      * to clear view subscriber date filter on cancel button
      */
      $('#subscriber_date').on('cancel.daterangepicker', function(ev, picker) {
        //do something, like clearing an input
        $('#subscriber_date').val('');
        $.ajax({
          url: "<?php echo admin_url('reports/filter_subscriber'); ?>",
          method: "POST",
          type: "html",
          data: 'saved_filter=all&reportconfigurationid='+configid
        }).done(function(data){
          //console.log(data);
          $("#subscriber-div").html(data);
          $("#subscriber-filter").val('all');
          $(".selectpicker").selectpicker('refresh');
        });
      });

      $('.applyBtn').click(function(){
        //generate chart and table on setting start and end date
        var configid = $('#subscriber_date').attr('data-reportsubscriberid');
        var startDate = $('#subscriber_date').data('daterangepicker').startDate._d;
        var endDate = $('#subscriber_date').data('daterangepicker').endDate._d;
        $.ajax({
          url: "<?php echo admin_url('reports/filter_subscriber'); ?>",
          method: "POST",
          type: "html",
          data: 'saved_filter=custom&reportconfigurationid='+configid+'&startDate='+startDate+'&endDate='+endDate
        }).done(function(data){
          //console.log(data);
          $("#subscriber-div").html(data);
          $("#subscriber-filter").val(filter_val);
          $(".selectpicker").selectpicker('refresh');
          $('#subscriber_date').removeClass('hide');
          $('input[name="subscriber_date"]').daterangepicker({
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

  initReportDataTableOffline('.table-subscriber', <?php echo $report_data->default_records; ?>);
</script>
<?php } ?>

<!--
  * Added by: Vaidehi
  * Date: 03/17/2018
  * Popup to display setting option
  -->
<div class="modal fade" id="subscrber_display_settings" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
        <input type="hidden" name="type" id="type" value="subscriber">
        <div class="modal-body">
          <?php 
            $get_session_data = get_session_data();
            if($get_session_data['user_type'] == 1) {
          ?>
            <div class="row">
              <div class="form-group">
                <div class="checkbox checkbox-primary mtop25">
                  <input type="checkbox" name="subscriber_report_permission" id="subscriber_report_permission">
                  <label for="include_share_permission"><?php echo _l('include_share_permission'); ?></label>
                </div>
              </div>
            </div>
            <div id="subscriber-sharing-permission">
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
                <input type="checkbox" name="subscriber_no_of_records" id="subscriber_no_of_records">
                <label for="default_records"><?php echo _l('default_records'); ?></label>
              </div>
            </div>
          </div>
          <div id="subscriber-no-of-records">
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
$('#subscriber_display_report_settings').click(function(){
  $("#reportconfigurationid").val($(this).data('reportsubscriberid'));
  $('#subscrber_display_settings').modal('show');
});

$('#subscriber-no-of-records').hide();

<?php 
  $get_session_data = get_session_data();
  if($get_session_data['user_type'] == 1) {
?>
  $('#subscriber-sharing-permission').hide();

  $('#subscriber_report_permission').click(function(){
    if($('#subscriber_report_permission').is(':checked')) {
      $('#subscriber-sharing-permission').show()
    } else {
      $('#subscriber-sharing-permission').hide();
    }
  });
<?php } ?>

$('#subscriber_no_of_records').click(function(){
  if($('#subscriber_no_of_records').is(':checked')) {
    $('#subscriber-no-of-records').show()
  } else {
    $('#subscriber-no-of-records').hide();
  }
});
</script>