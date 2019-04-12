<?php //echo site_url('cron/index'); ?>
<?php init_head();
  $sesssion_data = get_session_data();
?>
<div id="wrapper">
  <div class="content reports-page">
    <div class="row">
      <div class="col-md-12">
        <div class="main-banners">
          <?php if(@$package_type->name=="Paid" && !empty($banner->value)){ ?>
            <img src="<?php echo base_url('uploads/brands/').$banner->value; ?>" style="width: 100%;">
          <?php }else{ ?>
            <img src="<?php echo base_url('uploads/company/banner.jpg'); ?>" style="width: 100%;">
          <?php } ?>
        </div>
		    <h1 class="pageTitleH1"><i class="fa fa-area-chart"></i> Reports</h1>
		    <div class="welcomeUser">
          <a href="<?php echo admin_url("reports/config"); ?>">
				    <i class="fa fa-cog menu-icon"></i>
          </a>
        </div>
        <div class="breadcrumb">
          <a href="#"><i class="fa fa-home"></i></a>
          <i class="fa fa-angle-right breadcrumb-arrow"></i>
          <span>Reports</span>
        </div>
		    <div class="clearfix"></div>
        <div class="row">
          <div id="sortable1" class="col-md-6 sortable_config_item">
            <?php 
              foreach ($report_data as $report) {
                if($report['report_order']%2 == 0){
            ?>
              <?php if($report['report_name'] == 'Revenue' && $sesssion_data['user_type'] == 1) { ?>
                <div class="row col-sm-12 option" data-class='option' data-id = "<?php echo $report['reportconfigurationid'] ?>" data-name="<?php echo $report['report_name']; ?>" data-order ="<?php echo $report['report_order'] ?>">           
                  <?php if(isset($report['saved_filter'])) { ?>
                    <input type="hidden" name="revenue-default" id="revenue-default" value="<?php echo $report['saved_filter'];?>">
                  <?php } ?>
                  <?php if(isset($report['start_date'])) { ?>
                    <input type="hidden" name="revenue-defaultstartdate" id="revenue-defaultstartdate" value="<?php echo $report['start_date'];?>">
                  <?php } ?>
                  <?php if(isset($report['end_date'])) { ?>
                    <input type="hidden" name="revenue-defaultenddate" id="revenue-defaultenddate" value="<?php echo $report['end_date'];?>">
                  <?php } ?>
                  <div class="panel_s report-panel" id="revenue-div">
                  </div>
                </div>
              <?php } else if($report['report_name'] == 'Lead Source') { ?>
                <div class="row col-sm-12 option" data-class='option' data-id = "<?php echo $report['reportconfigurationid'] ?>" data-name="<?php echo $report['report_name']; ?>" data-order ="<?php echo $report['report_order'] ?>">
                  <?php if(isset($report['saved_filter'])) { ?>
                    <input type="hidden" name="leadsource-default" id="leadsource-default" value="<?php echo $report['saved_filter'];?>">
                  <?php } ?>
                  <?php if(isset($report['start_date'])) { ?>
                    <input type="hidden" name="leadsource-defaultstartdate" id="leadsource-defaultstartdate" value="<?php echo $report['start_date'];?>">
                  <?php } ?>
                  <?php if(isset($report['end_date'])) { ?>
                    <input type="hidden" name="leadsource-defaultenddate" id="leadsource-defaultenddate" value="<?php echo $report['end_date'];?>">
                  <?php } ?>
                  <div class="panel_s report-panel" id="leadsource-div">
                  </div>
                </div>
              <?php } elseif($report['report_name'] == 'Booking Success') { ?>
                <div class="row col-sm-12 option" data-class='option' data-id = "<?php echo $report['reportconfigurationid'] ?>" data-name="<?php echo $report['report_name']; ?>" data-order ="<?php echo $report['report_order'] ?>">
                  <?php if(isset($report['saved_filter'])) { ?>
                    <input type="hidden" name="leadstatus-default" id="leadstatus-default" value="<?php echo $report['saved_filter'];?>">
                  <?php } ?>
                  <?php if(isset($report['start_date'])) { ?>
                    <input type="hidden" name="leadstatus-defaultstartdate" id="leadstatus-defaultstartdate" value="<?php echo $report['start_date'];?>">
                  <?php } ?>
                  <?php if(isset($report['end_date'])) { ?>
                    <input type="hidden" name="leadstatus-defaultenddate" id="leadstatus-defaultenddate" value="<?php echo $report['end_date'];?>">
                  <?php } ?>
                  <div class="panel_s report-panel" id="leadstatus-div">
                  </div>
                </div>
              <?php } ?>
            <?php
                } 
              }
            ?>   
          </div>
          <div id="sortable2" class="col-md-6 sortable_config_item">
            <?php 
              foreach ($report_data as $report) {
                if($report['report_order']%2 != 0){
            ?>
              <?php if($report['report_name'] == 'Revenue' && $sesssion_data['user_type'] == 1) { ?>
                <div class="row col-sm-12 option" data-class='option' data-id = "<?php echo $report['reportconfigurationid'] ?>" data-name="<?php echo $report['report_name']; ?>" data-order ="<?php echo $report['report_order'] ?>">           
                  <?php if(isset($report['saved_filter'])) { ?>
                    <input type="hidden" name="revenue-default" id="revenue-default" value="<?php echo $report['saved_filter'];?>">
                  <?php } ?>
                  <?php if(isset($report['start_date'])) { ?>
                    <input type="hidden" name="revenue-defaultstartdate" id="revenue-defaultstartdate" value="<?php echo $report['start_date'];?>">
                  <?php } ?>
                  <?php if(isset($report['end_date'])) { ?>
                    <input type="hidden" name="revenue-defaultenddate" id="revenue-defaultenddate" value="<?php echo $report['end_date'];?>">
                  <?php } ?>
                  <div class="panel_s report-panel" id="revenue-div">
                  </div>
                </div>
              <?php } else if($report['report_name'] == 'Lead Source') { ?>
                <div class="row col-sm-12 option" data-class='option' data-id = "<?php echo $report['reportconfigurationid'] ?>" data-name="<?php echo $report['report_name']; ?>" data-order ="<?php echo $report['report_order'] ?>">
                  <?php if(isset($report['saved_filter'])) { ?>
                    <input type="hidden" name="leadsource-default" id="leadsource-default" value="<?php echo $report['saved_filter'];?>">
                  <?php } ?>
                  <?php if(isset($report['start_date'])) { ?>
                    <input type="hidden" name="leadsource-defaultstartdate" id="leadsource-defaultstartdate" value="<?php echo $report['start_date'];?>">
                  <?php } ?>
                  <?php if(isset($report['end_date'])) { ?>
                    <input type="hidden" name="leadsource-defaultenddate" id="leadsource-defaultenddate" value="<?php echo $report['end_date'];?>">
                  <?php } ?>
                  <div class="panel_s report-panel" id="leadsource-div">
                  </div>
                </div>
              <?php } elseif($report['report_name'] == 'Booking Success') { ?>
                <div class="row col-sm-12 option" data-class='option' data-id = "<?php echo $report['reportconfigurationid'] ?>" data-name="<?php echo $report['report_name']; ?>" data-order ="<?php echo $report['report_order'] ?>">
                  <?php if(isset($report['saved_filter'])) { ?>
                    <input type="hidden" name="leadstatus-default" id="leadstatus-default" value="<?php echo $report['saved_filter'];?>">
                  <?php } ?>
                  <?php if(isset($report['start_date'])) { ?>
                    <input type="hidden" name="leadstatus-defaultstartdate" id="leadstatus-defaultstartdate" value="<?php echo $report['start_date'];?>">
                  <?php } ?>
                  <?php if(isset($report['end_date'])) { ?>
                    <input type="hidden" name="leadstatus-defaultenddate" id="leadstatus-defaultenddate" value="<?php echo $report['end_date'];?>">
                  <?php } ?>
                  <div class="panel_s report-panel" id="leadstatus-div">
                  </div>
                </div>
              <?php } ?>
            <?php
                } 
              }
            ?>  
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />

<script type="text/javascript">
$( function() {
  //sort each widget
  $("#sortable1, #sortable2").sortable({
    connectWith: ".sortable_config_item",
    stop: function(event, ui){
      var clas = ui.item.attr("data-class");
     
      order = 0;
      count = 0;
      var option = [];
      $("."+clas).each(function(){
        var id    = $(this).attr('data-id');
        
        order     = $(this).attr('data-order');
        var option_val = {
          'reportconfigurationid': id,
          'report_order':count,
        };

        $(this).attr('data-order',count);
        option.push(option_val);
        count++;
      });
      option = JSON.stringify(option);
  
      var url = "<?php echo admin_url('reports/ajax_order_update'); ?>";

      $.ajax({
        method: "POST",
        url: url,
        data:"options="+option,
      }).done(function() {
      });
    }
  });

  //revenue filter
  if($('#revenue-default').length > 0) {
    var filter_value = $('#revenue-default').val();

    if($('#revenue-defaultstartdate').length > 0) {
      var filer_start_date = $('#revenue-defaultstartdate').val();
    }

    if($('#revenue-defaultenddate').length > 0) {
      var filer_end_date = $('#revenue-defaultenddate').val();
    }

    var id = '';
    $(".option").each(function(){
      var name    = $(this).attr('data-name');
      if(name == "Revenue") {
        id = $(this).attr('data-id');
      }
    });
    
    $.ajax({
      url: "<?php echo admin_url('reports/filter_revenue'); ?>",
      method: "POST",
      type: "html",
      data: "saved_filter="+filter_value+"&reportconfigurationid="+id
    }).done(function(data){
      $("#revenue-div").html(data);
      //$("#revenue-filter").val(filter_value);
      //$(".selectpicker").selectpicker('refresh');
    });
  }

  //lead status filter
  if($('#leadstatus-default').length > 0) {
    var filter_value = $('#leadstatus-default').val();
    
    if($('#leadstatus-defaultstartdate').length > 0) {
      var filer_start_date = $('#leadstatus-defaultstartdate').val();
    }

    if($('#leadstatus-defaultenddate').length > 0) {
      var filer_end_date = $('#leadstatus-defaultenddate').val();
    }

    var id = '';
    $(".option").each(function(){
      var name    = $(this).attr('data-name');
      if(name == "Booking Success") {
        id = $(this).attr('data-id');
      }
    });
    
    $.ajax({
      url: "<?php echo admin_url('reports/filter_leadstatus'); ?>",
      method: "POST",
      type: "html",
      data: "saved_filter="+filter_value+"&reportconfigurationid="+id
    }).done(function(data){
      $("#leadstatus-div").html(data);
      //$("#leadstatus-filter").val(filter_value);
      //$(".selectpicker").selectpicker('refresh');
    });
  }

  //lead source filter
  if($('#leadsource-default').length > 0) {
    var filter_value = $('#leadsource-default').val();

    if($('#leadsource-defaultstartdate').length > 0) {
      var filer_start_date = $('#leadsource-defaultstartdate').val();
    }

    if($('#leadsource-defaultenddate').length > 0) {
      var filer_end_date = $('#leadsource-defaultenddate').val();
    }

    var id = '';
    $(".option").each(function(){
      var name    = $(this).attr('data-name');
      if(name == "Lead Source") {
        id = $(this).attr('data-id');
      }
    });
    
    $.ajax({
      url: "<?php echo admin_url('reports/filter_leadsource'); ?>",
      method: "POST",
      type: "html",
      data: "saved_filter="+filter_value+"&reportconfigurationid="+id
    }).done(function(data){
      $("#leadsource-div").html(data);
      //$("#leadsource-filter").val(filter_value);
      //$(".selectpicker").selectpicker('refresh');
    });
  }
});
</script>
</body>
</html>
