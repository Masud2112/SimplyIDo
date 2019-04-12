<?php 
  //show report on main report screen
  if(!$detailed_report) { 
?>
  <h4 class="sub-title"><?php echo _l('netrevenue_title'); ?></h4>	
  <div class="pull-right">
    <input type="hidden" name="reportnetrevenueid" id="reportnetrevenueid" value="<?php echo $reportconfigurationid; ?>">
    <div class="disInline repSetting">
      <a href="javascript:void(0)" data-toggle="modal" id="netrevenue_display_report_settings" data-reportnetrevenueid="<?php echo $reportconfigurationid; ?>"><i class="fa fa-cog" aria-hidden="true"></i></a>
    </div>
    <div class="disInline selectfilter">
      <?php echo render_select('netrevenue-filter', $filters, array('filtervalue','filtername'), '', (isset($report_data->saved_filter) ? $report_data->saved_filter : '')); ?>
    </div>
    <div class="disInline cusfilter">                                   
      <input type="text" class="form-control hide" id="netrevenue_date" name="netrevenue_date" data-reportnetrevenueid="<?php echo $reportconfigurationid; ?>" placeholder="Select Dates" />     
    </div>
  </div>
  <div class="clearfix"></div>  
  <div class="panel-body">
    <?php
      //if report is shown in configuration
    if($has_permission_netrevenue == 1) {
      if($report_data->is_visible == 1) { 
        $is_netrevenue = 1; 
        if(count($netrevenues) > 0) {
          $tot_netrevenue = 0;
          $category_data  = '';
          $revenue_data   = '';
          $churn_data  = '';
          foreach ($netrevenues as $netrevenue) {
            $category_data.= "'".$netrevenue['month']."',";
            $revenue_data.= $netrevenue['revenue'].",";
            $churn_data.= "-".$netrevenue['churn'].",";
          }
        }
    ?> 
        <!--Report in Chart-->
        <div id="netrevenue-container" class="chart"></div>
        <div class="widget-heading">
          <div class="more-setting">
            <a href="javascript:void(0)" id="expand-netrevenue" data-target="#netrevenue-table" id="netrevenue-collapse"><span id="netrevenue-span">HIDE DETAIL </span><i class="fa fa-caret-up"></i></a>
          </div>
        </div>
        <!--Report in Table Form-->
        <div id="netrevenue-table">
          <table class="table table-striped table-netrevenue">
            <thead>
              <tr>
                <th class="text-center"><?php echo strtoupper(_l('month')); ?></th>
                <th class="text-center"><?php echo strtoupper(_l('revenue')); ?></th>
                <th class="text-center"><?php echo strtoupper(_l('churn')); ?></th>
                <th class="text-center"><?php echo '$'.strtoupper(_l('net')); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php 
                if(count($netrevenues) > 0) {
                  foreach ($netrevenues as $netrevenue) {
              ?>
                    <tr>
                      <td class="text-center"><?php echo $netrevenue['month']; ?></td>
                      <td class="text-center"><?php echo ( $netrevenue['revenue'] > 0.00 ? '$'.$netrevenue['revenue'] : '-'); ?></td>
                      <td class="text-center"><?php echo ( $netrevenue['churn'] > 0.00 ? '$'. $netrevenue['churn'] : '-' ); ?></td>
                      <td class="text-center"><?php echo ( $netrevenue['net'] > 0.00 ? '$'.$netrevenue['net'] : '-'); ?></td>
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
              if(count($netrevenues) > 0) {
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
      <div>Report is not visible. Please click on <a href="<?php echo admin_url("reports/config"); ?>">Change Setting</a></div>
    <?php } } else {?>
      <div>You do not have permission to view report.</div>
    <?php } ?>
  </div>
<script type="text/javascript">
  <?php
    //collapse-expand report
    if(isset($is_netrevenue)) {
  ?>
    //for net revenue
    $('#expand-netrevenue').click(function () {
      var target = $(this).attr('data-target');
      $(target).slideToggle()
      $('#netrevenue-span').text($('#netrevenue-span').text() == 'HIDE DETAIL ' ? 'SHOW DETAIL ' : 'HIDE DETAIL ');
      $(this).find('i').toggleClass('fa-caret-up fa-caret-down');
    });
  <?php } ?>

  <?php
    //generate dynamic chart
    if(isset($is_netrevenue) && $category_data!="") {
  ?>
    $(function() {
      (function (H) {
        H.wrap(H.Renderer.prototype, 'label', function (proceed, str, x, y, shape, anchorX, anchorY, useHTML) {
          if(/class="fa/.test(str)) useHTML = true;
          // Run original proceed method
          return proceed.apply(this, [].slice.call(arguments, 1));
        });
      }(Highcharts));
      //for net revenue
      var chart = {
         type: 'column'
      };
      var title = {
         text: ''  
      };   
      var xAxis = {
         categories: [<?php echo $category_data; ?>]
      };
      var credits = {
         enabled: false
      };
      var series = [
         {
            name: 'Revenue',
            data: [<?php echo $revenue_data; ?>],
            color: '#50ac57'
         }, 
         {
            name: 'Churn',
            data: [<?php echo $churn_data; ?>],
            color: '#f2433c'
         }
      ];   

      var exporting = {
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
      };  

      var json = {};   
      json.chart = chart; 
      json.title = title; 
      json.xAxis = xAxis;
      json.credits = credits;
      json.series = series;
      json.exporting = exporting;
      $('#netrevenue-container').highcharts(json);
    });
  <?php } else { ?>
    $('#netrevenue-container').html('<span class="chart-norecord">No Records Found</span>');
  <?php } ?>

  <?php if(isset($report_data->saved_filter)) { ?>
    $('#netrevenue-filter').val('<?php echo $report_data->saved_filter; ?>');
    $('.selectpicker').selectpicker('refresh')
  <?php } ?>

  <?php if($report_data->saved_filter == 'custom') { ?>
    $('input[name="netrevenue_date"]').daterangepicker({
      locale: {
        format: 'MM/DD/YYYY'
      },
      clearBtn: true,
      startDate: '<?php echo date("m/d/Y",strtotime($report_data->start_date)); ?>',
      endDate: '<?php echo date("m/d/Y",strtotime($report_data->end_date)); ?>'
    });
  <?php } ?>

  //generate chart and table based on net revenue filter change 
  $('#netrevenue-filter').change(function(){
    var filter_val = $(this).val();
    var configid = $('#reportnetrevenueid').val();

    //if filter is predefined
    if(filter_val != 'custom') {
      $.ajax({
        url: "<?php echo admin_url('reports/filter_netrevenue'); ?>",
        method: "POST",
        type: "html",
        data: "saved_filter="+filter_val+'&reportconfigurationid='+configid
      }).done(function(data){
        //console.log(data);
        $("#netrevenue-div").html(data);
        $("#netrevenue-filter").val(filter_val);
        $(".selectpicker").selectpicker('refresh');
      });
    } else {
      //if filter is custom, get start date and endate
      $('#netrevenue_date').removeClass('hide');
      $('input[name="netrevenue_date"]').daterangepicker({
        locale: {
          format: 'MM/DD/YYYY'
        },
        clearBtn: true
      });

      /**
      * to clear view net revenue date filter on cancel button
      */
      $('#netrevenue_date').on('cancel.daterangepicker', function(ev, picker) {
        //do something, like clearing an input
        $('#netrevenue_date').val('');
        $.ajax({
          url: "<?php echo admin_url('reports/filter_netrevenue'); ?>",
          method: "POST",
          type: "html",
          data: 'saved_filter=all&reportconfigurationid='+configid
        }).done(function(data){
          //console.log(data);
          $("#netrevenue-div").html(data);
          $("#netrevenue-filter").val('all');
          $(".selectpicker").selectpicker('refresh');
        });
      });

      $('.applyBtn').click(function(){
        //generate chart and table on setting start and end date
        var configid = $('#netrevenue_date').attr('data-reportnetrevenueid');
        var startDate = $('#netrevenue_date').data('daterangepicker').startDate._d;
        var endDate = $('#netrevenue_date').data('daterangepicker').endDate._d;
        $.ajax({
          url: "<?php echo admin_url('reports/filter_netrevenue'); ?>",
          method: "POST",
          type: "html",
          data: 'saved_filter=custom&reportconfigurationid='+configid+'&startDate='+startDate+'&endDate='+endDate
        }).done(function(data){
          //console.log(data);
          $("#netrevenue-div").html(data);
          $("#netrevenue-filter").val(filter_val);
          $(".selectpicker").selectpicker('refresh');
          $('#netrevenue_date').removeClass('hide');
          $('input[name="netrevenue_date"]').daterangepicker({
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

  initReportDataTableOffline('.table-netrevenue', <?php echo $report_data->default_records; ?>);
</script>
<?php } ?>

<!--
  * Added by: Vaidehi
  * Date: 03/17/2018
  * Popup to display setting option
  -->
<div class="modal fade" id="netrevenue_display_settings" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
        <input type="hidden" name="type" id="type" value="netrevenue">
        <div class="modal-body">
          <?php 
            $get_session_data = get_session_data();
            if($get_session_data['user_type'] == 1) {
          ?>
            <div class="row">
              <div class="form-group">
                <div class="checkbox checkbox-primary mtop25">
                  <input type="checkbox" name="netrevenue_report_permission" id="netrevenue_report_permission">
                  <label for="include_share_permission"><?php echo _l('include_share_permission'); ?></label>
                </div>
              </div>
            </div>
            <div id="netrevenue-sharing-permission">
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
                <input type="checkbox" name="netrevenue_no_of_records" id="netrevenue_no_of_records">
                <label for="default_records"><?php echo _l('default_records'); ?></label>
              </div>
            </div>
          </div>
          <div id="netrevenue-no-of-records">
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
$('#netrevenue_display_report_settings').click(function(){
  $("#reportconfigurationid").val($(this).data('reportnetrevenueid'));
  $('#netrevenue_display_settings').modal('show');
});

$('#netrevenue-no-of-records').hide();

<?php 
  $get_session_data = get_session_data();
  if($get_session_data['user_type'] == 1) {
?>
  $('#netrevenue-sharing-permission').hide();

  $('#netrevenue_report_permission').click(function(){
    if($('#netrevenue_report_permission').is(':checked')) {
      $('#netrevenue-sharing-permission').show()
    } else {
      $('#netrevenue-sharing-permission').hide();
    }
  });
<?php } ?>

$('#netrevenue_no_of_records').click(function(){
  if($('#netrevenue_no_of_records').is(':checked')) {
    $('#netrevenue-no-of-records').show()
  } else {
    $('#netrevenue-no-of-records').hide();
  }
});
</script>