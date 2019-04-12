<?php init_head(); ?>
<div id="wrapper">
  <div class="content manage-team-member-page">
    <div class="row">
      <div class="col-md-12"> 
              <div class="breadcrumb">
                  <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                  <i class="fa fa-angle-right breadcrumb-arrow"></i>
                  <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                  <i class="fa fa-angle-right breadcrumb-arrow"></i>
                  <span>Subscription Overview</span>
              </div>          
		  <h1 class="pageTitleH1"><i class="fa fa-retweet"></i><?php echo $title; ?></h1>
          <div class="clearfix"></div>
        <div class="panel_s btmbrd team_member_section">
          <?php echo form_open('admin/subscription/all_option_delete',array('id'=>'project_list_form')); ?>
          <div class="panel-body">
            <div class="_buttons">                  
            </div>
              <div class="clearfix"></div>
              <hr class="hr-panel-heading" />
              <div class="row">                        
                <h5>Project list</h5>
                  <div class="form-group">
                      <?php foreach ($project_list as $single_project) { ?>
                      <div class="team-member">
                        <label>Brand name:  <?php echo $single_project['brandname']->name;?></label><br/>
                        <input type="hidden" name="brand_id[]" value="<?php echo $single_project['brandname']->brandid;?>">
                        <?php  
                        if(count($single_project['project_list']) > 0) {
                        foreach ($single_project['project_list'] as $single_list) { ?>
                        <div class="checkbox mleft15">
                              <input type="checkbox" class="brand_project_list_<?php echo $single_project['brandname']->brandid;?>" data-class="brand_project_list_<?php echo $single_project['brandname']->brandid;?>" name="project_list[]" value="<?php echo $single_list['id']; ?>">
                              <label for=""><?php echo $single_list['name']; ?></label>
                        </div>
                      <?php } } else {?>
                        <span><?php echo _l('no_projects_found'); ?></span>
                      <?php } ?>
                    </div>
                      <?php } ?>
                  </div> 
              </div>
              <div class="pull-right mtop15">
                <a href="#" class="btn btn-default" onclick="init_subscription();"><?php echo _l('previous'); ?></a>
                <button type="submit" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" data-form="#project_list_form" class="btn btn-info"><?php echo _l('Submit'); ?></button>
              </div>
          </div>
          <?php echo form_close(); ?>
        </div> 
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
  function init_subscription(){ 
             location.href='<?php echo base_url(); ?>admin/subscription/subscription_option';
        }
</script>
<script type="text/javascript">
  <?php if($project_restriction) { ?>
    $(function(){
        $('input[type=checkbox]').on('change', function (e) {
        var curr_brand = "."+$(this).attr("data-class");
        var cnt = 0;
        ttl_chk_bx = $(curr_brand).length;
        mx_unchecked_limit = <?php echo $new_package_project_restriction ?>;
        if(ttl_chk_bx > mx_unchecked_limit){
          $(curr_brand).each(function(){
            if($(this).prop('checked')==false)
            {
              cnt++;
            }
          });
        }
        if(cnt > 0 && cnt < <?php echo $new_package_project_restriction ?>)
            {
              $(this).prop('checked', false);
              alert("only <?php echo $new_package_project_restriction ?> projects allowed as per your subscription, please delete remaining projects");
            }
        });
       });
  <?php } ?>
</script>
</body>
</html>