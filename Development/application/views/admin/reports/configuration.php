<?php init_head(); ?>

<div id="wrapper">
  <div class="content reports-config">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <h4 class="no-margin"><?php echo $title; ?></h4>
          <div class="topButton">
            <button class="btn btn-default" type="button" onclick='location.href="<?php echo admin_url('reports'); ?>"'><?php echo _l( 'back'); ?></button>
          </div>
          <hr class="hr-panel-heading" />
          <input type="hidden" name=" staff_user_id" value="<?php echo get_staff_user_id(); ?>">
          <div class="row report-sortable sortable_config_item">
          <?php 
            foreach ($report_data as $report) {
          ?>
              <div class="col-md-12 panel_s option" data-class = 'option' data-id = "<?php echo $report['reportconfigurationid'] ?>" data-name="<?php echo $report['report_name']; ?>" data-order ="<?php echo $report['report_order'] ?>">
                <div class="panel-body config_block">
                  <h5 class="pull-left"><?php echo _l($report['report_name']); ?></h5>
                  <?php 
                    if($report['is_visible'] == 1) { 
                      $class = "btn btn-danger pull-right update_setting";
                      $name  = "Hide"; 
                    } else {
                      $class = "btn btn-info pull-right update_setting";
                      $name  = "Show";
                    }
                  ?>
                  <a href="#" class="<?php echo $class; ?>" id="<?php echo $report['reportconfigurationid'] ?>" data-id="<?php echo $report['reportconfigurationid'] ?>"><?php echo $name; ?></a>
                </div>
              </div>               
          <?php 
            }
          ?>
          </div>  
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
<script type="text/javascript">
  $( function() {
    //to update show/hide status
    $('.update_setting').click(function(e){
      var id      = $(this).attr("data-id"); 

      $.ajax({
        type: "POST",
        url: admin_url + "reports/update_setting",
        async: true,
        data: { reportconfigurationid:id, type: 'is_visible'}
      })
      .done(function(data){ 
        if(data) {
          if(data == "show") {
            $('#'+id).html('Hide');
            $('#'+id).removeClass('btn-info');
            $('#'+id).addClass('btn-danger');
          } else {
            $('#'+id).html('Show');
            $('#'+id).addClass('btn-info');
            $('#'+id).removeClass('btn-danger');
          }
        }
      }) ; 
    });

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
  });
</script>