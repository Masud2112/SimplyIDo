<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <div class="_buttons">
              <?php if (isset($lid)) { ?> 
                <?php if (has_permission('proposals','','create')) { ?>              
                  <a href="<?php echo admin_url('proposals/proposal?lid=' . $lid); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_proposal'); ?></a>
                <?php }  ?>       
                <div class="pull-right">
                  <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo admin_url('leads/dashboard/' . $lid); ?>"><?php echo ($lname); ?></a></li>
                    <li class="breadcrumb-item active"><?php echo _l('proposals'); ?></li>
                  </ol>                            
                </div>  
                <div class="clearfix"></div>
                <hr class="hr-panel-heading"/>
                <?php } elseif (has_permission('proposals','','create')) { ?>  
                  <a href="<?php echo admin_url('proposals/proposal'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_proposal'); ?></a>
                  <div class="clearfix"></div>
                  <hr class="hr-panel-heading" />
              <?php }  ?>
            </div>
            
            <div class="clearfix"></div>
            <?php render_datatable(array(
              _l('name'),
              _l('proposal_date'),
              _l('proposal_duedate'),
              _l('')
              ),'proposals'); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php init_tail(); ?>
  <script>
    var notSortable = $('.table-proposals').find('th').length - 1;
    initDataTable('.table-proposals', window.location.href, [1], notSortable);
  </script>
</body>
</html>
