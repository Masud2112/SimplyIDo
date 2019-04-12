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
          <?php echo form_open('admin/subscription/get_project_list',array('id'=>'brand_list_form')); ?>
          <div class="panel-body">
              <div>                        
                <h5>Team Members</h5>
                  <div class="form-group">
                      <?php 
                        if(count($staff_members) > 0){
                        foreach ($staff_members as $member_list) { ?>
							         <div class="team-member">
                        <label>Brand name:  <?php echo $member_list['brandname']->name;?></label>
                        <input type="hidden" name="brand_id[]" value="<?php echo $member_list['brandname']->brandid;?>">
                        <?php  
                        if(count($member_list['group_memeber_list']) > 0){
                        foreach ($member_list['group_memeber_list'] as $single_list) { ?>
                        <div class="checkbox mleft15">
                          <?php ?>
                              <input type="checkbox" class="team_member_list_<?php echo $member_list['brandname']->brandid;?>" data-class="team_member_list_<?php echo $member_list['brandname']->brandid;?>" name="member_list[]" <?php if($single_list['staffid']==$this->session->userdata['staff_user_id']){ echo "disabled"; } ?> value="<?php echo $single_list['staffid']; ?>">
                              <label for=""><?php echo $single_list['firstname']." ".$single_list['lastname']; ?>  
                                [<?php echo $single_list['email']; ?>] 
                                <span style="color: red;"><?php if($single_list['staffid']==$this->session->userdata['staff_user_id']){ echo "(Account owner)";} ?></span>
                                </label>
                        </div>
                            <?php }} else { ?>
                            <span><?php echo _l('noteam_members_found'); ?></span>
                          <?php } ?>
                  </div>
							<?php } }?>
                        </div>
              </div>
              <div class="pull-right mtop15">
                  <a href="javascript:;" class="btn btn-default" onclick="init_subscription();"><?php echo _l('previous'); ?></a>
                  <button type="submit" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" data-form="#brand_list_form" class="btn btn-info"><?php echo _l('next'); ?></button>
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
  <?php if($team_member_restriction) { ?>
    $(function(){    
      $('input[type=checkbox]').on('change', function (e) {
      var curr_brand = "."+$(this).attr("data-class");
      var cnt = 0;
      ttl_chk_bx = $(curr_brand).length;
      mx_unchecked_limit = <?php echo $new_package_team_member_restriction ?>;
      if(ttl_chk_bx > mx_unchecked_limit){
        $(curr_brand).each(function(){
          if($(this).prop('checked')==false)
          {
            cnt++;
          }
        });
      }
      if(cnt > 0 && cnt < <?php echo $new_package_team_member_restriction ?>) {
        $(this).prop('checked', false);
        alert("only <?php echo $new_package_team_member_restriction ?> team member allowed as per your subscription, please delete remaining team members ");
      }
    });
    });
  <?php } ?>
</script>
</body>
</html>