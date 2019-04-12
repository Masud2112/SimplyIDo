<?php init_head();
  $sesssion_data = get_session_data();
?>
<div id="wrapper">
  <div class="content reports-page">
    <div class="row">
      <div class="col-md-12">
        <div class="main-banners">
          <img src="<?php echo base_url('uploads/company/banner.jpg'); ?>" style="width: 100%;">
        </div>
        <div class="breadcrumb">
          <a href="#"><i class="fa fa-home"></i></a>
          <i class="fa fa-angle-right breadcrumb-arrow"></i>
          <span>Reports</span>
        </div>
        <h1 class="pageTitleH1"><i class="fa fa-area-chart"></i> Reports</h1>
        <div class="welcomeUser">
          <a href="<?php echo admin_url("reports/config"); ?>">
            <i class="fa fa-cog menu-icon"></i>
          </a>
        </div>
        <div class="clearfix"></div>
        <div class="row">
          <div id="sortable1" class="col-md-6 sortable_config_item">
            <?php
              foreach ($report_data as $report) {
                if($report['report_order']%2 == 0) {
            ?>
                <?php if($report['report_name'] == 'Sign Up') { ?>
                  <div class="row col-sm-12 option" data-class='option' data-id = "<?php echo $report['reportconfigurationid'] ?>" data-name="<?php echo $report['report_name']; ?>" data-order ="<?php echo $report['report_order'] ?>">
                    <?php if(isset($report['saved_filter'])) { ?>
                      <input type="hidden" name="signup-default" id="signup-default" value="<?php echo $report['saved_filter'];?>">
                    <?php } ?>
                    <?php if(isset($report['start_date'])) { ?>
                      <input type="hidden" name="signup-defaultstartdate" id="signup-defaultstartdate" value="<?php echo $report['start_date'];?>">
                    <?php } ?>
                    <?php if(isset($report['end_date'])) { ?>
                      <input type="hidden" name="signup-defaultenddate" id="signup-defaultenddate" value="<?php echo $report['end_date'];?>">
                    <?php } ?>
                    <div class="panel_s report-panel" id="signup-div">
                    </div>
                  </div>
                <?php } else if($report['report_name'] == 'Subscribers') { ?>
                  <div class="row col-sm-12 option" data-class = 'option' data-id = "<?php echo $report['reportconfigurationid'] ?>" data-name="<?php echo $report['report_name']; ?>" data-order ="<?php echo $report['report_order'] ?>">
                    <?php if(isset($report['saved_filter'])) { ?>
                      <input type="hidden" name="subscriber-default" id="subscriber-default" value="<?php echo $report['saved_filter'];?>">
                    <?php } ?>
                    <?php if(isset($report['start_date'])) { ?>
                      <input type="hidden" name="subscriber-defaultstartdate" id="subscriber-defaultstartdate" value="<?php echo $report['start_date'];?>">
                    <?php } ?>
                    <?php if(isset($report['end_date'])) { ?>
                      <input type="hidden" name="subscriber-defaultenddate" id="subscriber-defaultenddate" value="<?php echo $report['end_date'];?>">
                    <?php } ?>
                    <div class="panel_s report-panel" id="subscriber-div">
                    </div>
                  </div>
                <?php } else if($report['report_name'] == 'Conversion Rate') { ?>
                  <div class="row col-sm-12 option" data-class = 'option' data-id = "<?php echo $report['reportconfigurationid'] ?>" data-name="<?php echo $report['report_name']; ?>" data-order ="<?php echo $report['report_order'] ?>">
                    <?php if(isset($report['saved_filter'])) { ?>
                      <input type="hidden" name="conversionrate-default" id="conversionrate-default" value="<?php echo $report['saved_filter'];?>">
                    <?php } ?>
                    <?php if(isset($report['start_date'])) { ?>
                      <input type="hidden" name="conversionrate-defaultstartdate" id="conversionrate-defaultstartdate" value="<?php echo $report['start_date'];?>">
                    <?php } ?>
                    <?php if(isset($report['end_date'])) { ?>
                      <input type="hidden" name="conversionrate-defaultenddate" id="conversionrate-defaultenddate" value="<?php echo $report['end_date'];?>">
                    <?php } ?>
                    <div class="panel_s report-panel" id="conversionrate-div">
                    </div>
                  </div>
                <?php } else if($report['report_name'] == 'Churn') { ?>
                  <div class="row col-sm-12 option" data-class = 'option' data-id = "<?php echo $report['reportconfigurationid'] ?>" data-name="<?php echo $report['report_name']; ?>" data-order ="<?php echo $report['report_order'] ?>">
                    <?php if(isset($report['saved_filter'])) { ?>
                      <input type="hidden" name="churn-default" id="churn-default" value="<?php echo $report['saved_filter'];?>">
                    <?php } ?>
                    <?php if(isset($report['start_date'])) { ?>
                      <input type="hidden" name="churn-defaultstartdate" id="churn-defaultstartdate" value="<?php echo $report['start_date'];?>">
                    <?php } ?>
                    <?php if(isset($report['end_date'])) { ?>
                      <input type="hidden" name="churn-defaultenddate" id="churn-defaultenddate" value="<?php echo $report['end_date'];?>">
                    <?php } ?>
                    <div class="panel_s report-panel" id="churn-div">
                    </div>
                  </div>
                <?php } else if($report['report_name'] == 'Net Revenue') { ?>
                  <div class="row col-sm-12 option" data-class = 'option' data-id = "<?php echo $report['reportconfigurationid'] ?>" data-name="<?php echo $report['report_name']; ?>" data-order ="<?php echo $report['report_order'] ?>">
                    <?php if(isset($report['saved_filter'])) { ?>
                      <input type="hidden" name="netrevenue-default" id="netrevenue-default" value="<?php echo $report['saved_filter'];?>">
                    <?php } ?>
                    <?php if(isset($report['start_date'])) { ?>
                      <input type="hidden" name="netrevenue-defaultstartdate" id="netrevenue-defaultstartdate" value="<?php echo $report['start_date'];?>">
                    <?php } ?>
                    <?php if(isset($report['end_date'])) { ?>
                      <input type="hidden" name="netrevenue-defaultenddate" id="netrevenue-defaultenddate" value="<?php echo $report['end_date'];?>">
                    <?php } ?>
                    <div class="panel_s report-panel" id="netrevenue-div">
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
                if($report['report_order']%2 != 0) {
            ?>
                <?php if($report['report_name'] == 'Sign Up') { ?>
                  <div class="row col-sm-12 option" data-class='option' data-id = "<?php echo $report['reportconfigurationid'] ?>" data-name="<?php echo $report['report_name']; ?>" data-order ="<?php echo $report['report_order'] ?>">
                    <?php if(isset($report['saved_filter'])) { ?>
                      <input type="hidden" name="signup-default" id="signup-default" value="<?php echo $report['saved_filter'];?>">
                    <?php } ?>
                    <?php if(isset($report['start_date'])) { ?>
                      <input type="hidden" name="signup-defaultstartdate" id="signup-defaultstartdate" value="<?php echo $report['start_date'];?>">
                    <?php } ?>
                    <?php if(isset($report['end_date'])) { ?>
                      <input type="hidden" name="signup-defaultenddate" id="signup-defaultenddate" value="<?php echo $report['end_date'];?>">
                    <?php } ?>
                    <div class="panel_s report-panel" id="signup-div">
                    </div>
                  </div>
                <?php } else if($report['report_name'] == 'Subscribers') { ?>
                  <div class="row col-sm-12 option" data-class = 'option' data-id = "<?php echo $report['reportconfigurationid'] ?>" data-name="<?php echo $report['report_name']; ?>" data-order ="<?php echo $report['report_order'] ?>">
                    <?php if(isset($report['saved_filter'])) { ?>
                      <input type="hidden" name="subscriber-default" id="subscriber-default" value="<?php echo $report['saved_filter'];?>">
                    <?php } ?>
                    <?php if(isset($report['start_date'])) { ?>
                      <input type="hidden" name="subscriber-defaultstartdate" id="subscriber-defaultstartdate" value="<?php echo $report['start_date'];?>">
                    <?php } ?>
                    <?php if(isset($report['end_date'])) { ?>
                      <input type="hidden" name="subscriber-defaultenddate" id="subscriber-defaultenddate" value="<?php echo $report['end_date'];?>">
                    <?php } ?>
                    <div class="panel_s report-panel" id="subscriber-div">
                    </div>
                  </div>
                <?php } else if($report['report_name'] == 'Conversion Rate') { ?>
                  <div class="row col-sm-12 option" data-class = 'option' data-id = "<?php echo $report['reportconfigurationid'] ?>" data-name="<?php echo $report['report_name']; ?>" data-order ="<?php echo $report['report_order'] ?>">
                    <?php if(isset($report['saved_filter'])) { ?>
                      <input type="hidden" name="conversionrate-default" id="conversionrate-default" value="<?php echo $report['saved_filter'];?>">
                    <?php } ?>
                    <?php if(isset($report['start_date'])) { ?>
                      <input type="hidden" name="conversionrate-defaultstartdate" id="conversionrate-defaultstartdate" value="<?php echo $report['start_date'];?>">
                    <?php } ?>
                    <?php if(isset($report['end_date'])) { ?>
                      <input type="hidden" name="conversionrate-defaultenddate" id="conversionrate-defaultenddate" value="<?php echo $report['end_date'];?>">
                    <?php } ?>
                    <div class="panel_s report-panel" id="conversionrate-div">
                    </div>
                  </div>
                <?php } else if($report['report_name'] == 'Churn') { ?>
                  <div class="row col-sm-12 option" data-class = 'option' data-id = "<?php echo $report['reportconfigurationid'] ?>" data-name="<?php echo $report['report_name']; ?>" data-order ="<?php echo $report['report_order'] ?>">
                    <?php if(isset($report['saved_filter'])) { ?>
                      <input type="hidden" name="churn-default" id="churn-default" value="<?php echo $report['saved_filter'];?>">
                    <?php } ?>
                    <?php if(isset($report['start_date'])) { ?>
                      <input type="hidden" name="churn-defaultstartdate" id="churn-defaultstartdate" value="<?php echo $report['start_date'];?>">
                    <?php } ?>
                    <?php if(isset($report['end_date'])) { ?>
                      <input type="hidden" name="churn-defaultenddate" id="churn-defaultenddate" value="<?php echo $report['end_date'];?>">
                    <?php } ?>
                    <div class="panel_s report-panel" id="churn-div">
                    </div>
                  </div>
                <?php } else if($report['report_name'] == 'Net Revenue') { ?>
                  <div class="row col-sm-12 option" data-class = 'option' data-id = "<?php echo $report['reportconfigurationid'] ?>" data-name="<?php echo $report['report_name']; ?>" data-order ="<?php echo $report['report_order'] ?>">
                    <?php if(isset($report['saved_filter'])) { ?>
                      <input type="hidden" name="netrevenue-default" id="netrevenue-default" value="<?php echo $report['saved_filter'];?>">
                    <?php } ?>
                    <?php if(isset($report['start_date'])) { ?>
                      <input type="hidden" name="netrevenue-defaultstartdate" id="netrevenue-defaultstartdate" value="<?php echo $report['start_date'];?>">
                    <?php } ?>
                    <?php if(isset($report['end_date'])) { ?>
                      <input type="hidden" name="netrevenue-defaultenddate" id="netrevenue-defaultenddate" value="<?php echo $report['end_date'];?>">
                    <?php } ?>
                    <div class="panel_s report-panel" id="netrevenue-div">
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
  $( ".report-sortable" ).sortable({
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

  //Sign Up filter
  if($('#signup-default').length > 0) {
    var filter_value = $('#signup-default').val();

    if($('#signup-defaultstartdate').length > 0) {
      var filer_start_date = $('#signup-defaultstartdate').val();
    }

    if($('#signup-defaultenddate').length > 0) {
      var filer_end_date = $('#signup-defaultenddate').val();
    }

    var id = '';
    $(".option").each(function(){
      var name    = $(this).attr('data-name');
      if(name == "Sign Up") {
        id = $(this).attr('data-id');
      }
    });

    $.ajax({
      url: "<?php echo admin_url('reports/filter_signup'); ?>",
      method: "POST",
      type: "html",
      data: "saved_filter="+filter_value+"&reportconfigurationid="+id
    }).done(function(data){
      $("#signup-div").html(data);
      // $("#signup-filter").val('all');
      // $(".selectpicker").selectpicker('refresh');
    });
  }

  //Subscriber filter
 if($('#subscriber-default').length > 0) {
    var filter_value = $('#subscriber-default').val();

    if($('#subscriber-defaultstartdate').length > 0) {
      var filer_start_date = $('#subscriber-defaultstartdate').val();
    }

    if($('#subscriber-defaultenddate').length > 0) {
      var filer_end_date = $('#subscriber-defaultenddate').val();
    }

    var id = '';
    $(".option").each(function(){
      var name    = $(this).attr('data-name');
      if(name == "Subscribers") {
        id = $(this).attr('data-id');
      }
    });

    $.ajax({
      url: "<?php echo admin_url('reports/filter_subscriber'); ?>",
      method: "POST",
      type: "html",
      data: "saved_filter="+filter_value+"&reportconfigurationid="+id
    }).done(function(data){
      $("#subscriber-div").html(data);
      // $("#subscriber-filter").val('all');
      // $(".selectpicker").selectpicker('refresh');
    });
  }

  //Conversion Rate filter
  if($('#conversionrate-default').length > 0) {
    var filter_value = $('#conversionrate-default').val();

    if($('#conversionrate-defaultstartdate').length > 0) {
      var filer_start_date = $('#conversionrate-defaultstartdate').val();
    }

    if($('#conversionrate-defaultenddate').length > 0) {
      var filer_end_date = $('#conversionrate-defaultenddate').val();
    }

    var id = '';
    $(".option").each(function(){
      var name    = $(this).attr('data-name');
      if(name == "Conversion Rate") {
        id = $(this).attr('data-id');
      }
    });

    $.ajax({
      url: "<?php echo admin_url('reports/filter_conversionrate'); ?>",
      method: "POST",
      type: "html",
      data: "saved_filter="+filter_value+"&reportconfigurationid="+id
    }).done(function(data){
      $("#conversionrate-div").html(data);
      // $("#conversionrate-filter").val('all');
      // $(".selectpicker").selectpicker('refresh');
    });
  }

  //Churn filter
  if($('#churn-default').length > 0) {

    var filter_value = $('#churn-default').val();

    if($('#churn-defaultstartdate').length > 0) {
      var filer_start_date = $('#churn-defaultstartdate').val();
    }

    if($('#churn-defaultenddate').length > 0) {
      var filer_end_date = $('#churn-defaultenddate').val();
    }

    var id = '';
    $(".option").each(function(){
      var name    = $(this).attr('data-name');
      if(name == "Churn") {
        id = $(this).attr('data-id');
      }
    });

    $.ajax({
      url: "<?php echo admin_url('reports/filter_churn'); ?>",
      method: "POST",
      type: "html",
      data: "saved_filter="+filter_value+"&reportconfigurationid="+id
    }).done(function(data){
      $("#churn-div").html(data);
      // $("#churn-filter").val('all');
      // $(".selectpicker").selectpicker('refresh');
    });
  }

  //Net Revenue filter
  if($('#netrevenue-default').length > 0) {

    var filter_value = $('#netrevenue-default').val();

    if($('#netrevenue-defaultstartdate').length > 0) {
      var filer_start_date = $('#netrevenue-defaultstartdate').val();
    }

    if($('#netrevenue-defaultenddate').length > 0) {
      var filer_end_date = $('#netrevenue-defaultenddate').val();
    }

    var id = '';
    $(".option").each(function(){
      var name    = $(this).attr('data-name');
      if(name == "Churn") {
        id = $(this).attr('data-id');
      }
    });

    $.ajax({
      url: "<?php echo admin_url('reports/filter_netrevenue'); ?>",
      method: "POST",
      type: "html",
      data: "saved_filter="+filter_value+"&reportconfigurationid="+id
    }).done(function(data){
      $("#netrevenue-div").html(data);
      // $("#netrevenue-filter").val('all');
      // $(".selectpicker").selectpicker('refresh');
    });
  }
});
</script>
</body>
</html>
