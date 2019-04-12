<?php init_head(); ?>
<div id="wrapper">
  <div class="content manage-subscription-page">
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
        <div class="panel_s btmbrd subscription_option brand_section">
            <?php echo form_open('admin/subscription/get_team_member_list',array('id'=>'brand_list_form')); ?>
              <div class="panel-body">
                  <div class="row">                        
                    <h5>Brand List : </h5>
                        <div class="form-group">
                                <?php foreach ($brands as $brand_list) { ?>
                                   <div class="col-sm-3"><div class="checkbox">
                                      <input type="checkbox" class="brand_group" name="brand_list[]" 
                                      <?php if($brand_list['brandid'] == get_user_session()){echo "disabled"; } ?> value="<?php echo $brand_list['brandid']; ?>">
                                      <label for="<?php echo $brand_list['name']; ?>"><?php echo $brand_list['name']; ?>
                                          <span style="color: red;">
                                            <?php //if($brand_list['brandid']==$this->session->userdata['brand_id']){ echo "(Account owner)";} ?></span>
                                      </label>
                                  </div></div>
                                <?php } ?>
                            </div>
                  </div>
              <div class="pull-right mtop15">
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
   $(function(){
    <?php if($brand_restriction) { ?>
    $('input[type=checkbox]').on('change', function (e) {
        if ($('input:checkbox:not(":checked")').length < <?php echo $new_package_brand_restriction ?>) {
            $(this).prop('checked', false);
            alert("only <?php echo $new_package_brand_restriction ?> brands allowed as per your subscription, please delete remaining brands");
        }
    });

    /* for enable/disable NEXT button while brand select */
    $('.brand_group').change(function() {
          var numberOfChecked = $('input:checkbox:checked').length;
          var numberNotChecked = $('input:checkbox:not(":checked")').length;
          if(numberOfChecked <= 0)
          {
            $('.btn-info').attr('disabled','disabled');
          }
          else
          {
            $('.btn-info').removeAttr('disabled','disabled');
          }

          });

   });
   <?php } ?>
</script>
<script type="text/javascript">
  <?php if($brand_restriction) { ?>
    var numberOfChecked = $('input:checkbox:checked').length;
    if(numberOfChecked <= 0) {
      $('.btn-info').attr('disabled','disabled');
    } else {
      $('.btn-info').removeAttr('disabled','disabled');
    }
  <?php } ?>
</script>
</body>
</html>