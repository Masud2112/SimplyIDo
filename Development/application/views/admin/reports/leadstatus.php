<?php init_head(); ?>
<div id="wrapper">
  <div class="content leadstatus-reports-page">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div class="row">
              <div class="main-banners">
                <div class="pull-left">
                  <button class="btn btn-info" onclick='location.href="<?php echo admin_url('reports');?>"'>BACK</button>
                </div>
                <div class="topButton">
                  <a href="<?php echo admin_url("reports/config"); ?>">
                    <i class="fa fa-cog menu-icon"></i>
                  </a>
                </div>
                <div class="pull-right main_config_icon">
					
					<div class="breadcrumb">
						<a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
						<i class="fa fa-angle-right breadcrumb-arrow"></i>
						<a href="<?php echo admin_url('reports/'); ?>"><?php echo _l('reports'); ?></a>
						<i class="fa fa-angle-right breadcrumb-arrow"></i>
						<span><?php echo _l('bookingsuccess_title'); ?></span>
					</div>                  
                </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <input type="hidden" name="reportid" id="reportid" value="<?php echo $reportconfigurationid; ?>">
                <input type="hidden" name="filter_val" id="filter_val" value="<?php echo $filter_val; ?>">
                <div class="panel_s report-panel" id="leadstatus-div">
                </div>
              </div>
            </div>
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
  $(function(){
    <?php if(isset($report_data['saved_filter'])) { ?>
      var filter_val = $('#filter_val').val();
      var configid = $('#reportid').val();
      
      $.ajax({
        url: "<?php echo admin_url('reports/filter_leadstatus'); ?>",
        method: "POST",
        type: "html",
        data: "saved_filter="+filter_val+'&reportconfigurationid='+configid+'&type=detailed'
      }).done(function(data){
        //console.log(data);
        $("#leadstatus-div").html(data);
        $("#leadstatus-filter").val(filter_val);
        $(".selectpicker").selectpicker('refresh');
      });
    <?php } else { ?>
      //lead status filter
      if($('#leadstatus-filter').val() == undefined) {
        var filter_val = $('#filter_val').val();
        var configid = $('#reportid').val();
        $.ajax({
          url: "<?php echo admin_url('reports/filter_leadstatus'); ?>",
          method: "POST",
          type: "html",
          data: "saved_filter="+filter_val+'&reportconfigurationid='+configid+'&type=detailed'
        }).done(function(data){
          //console.log(data);
          $("#leadstatus-div").html(data);
          $("#leadstatus-filter").val(filter_val);
          $(".selectpicker").selectpicker('refresh');
        });
      }
    <?php } ?>
  });
</script>
</body>
</html>
