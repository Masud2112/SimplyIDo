<?php init_head(); 
  $session_data = get_session_data(); 
  $package_type_id = $session_data['package_type_id'];
?>
<div id="wrapper">
  <div class="content manage-subscription-page">
    <div class="row">
      <div class="col-md-12">                    
              <div class="breadcrumb">
                  <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                  <i class="fa fa-angle-right breadcrumb-arrow"></i>
                  <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                  <i class="fa fa-angle-right breadcrumb-arrow"></i>
                  <span>Manage Subscription</span>
              </div>
          
		  <h1 class="pageTitleH1"><i class="fa fa-retweet"></i><?php echo $title; ?></h1>
          <div class="clearfix"></div>
        <div class="panel_s pricing_table_container show btmbrd">
          <div class="panel-body">
            <div class="row subscription-overview">                   
              <?php echo form_open('admin/subscription/record_payment',array('id'=>'subscription_payment_form')); ?>
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="packagelist" class="control-label">Package List<small class="req text-danger">* </small></label>
                      <select class="selectpicker" name="packageid" id="packagelist" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value=""></option>
                        <?php 
                          foreach($subscription as $subscription_list) { 
                            if($subscription_list['packagetypeid'] >= $package_type_id) {
                        ?>
                          <option value="<?php echo $subscription_list['packageid']; ?>" data-price="<?php echo $subscription_list['price']; ?>"><?php echo $subscription_list['name']; ?></option>
                        <?php } }?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="amount" class="control-label"><?php echo _l('record_payment_amount_received'); ?><small class="req text-danger">* </small></label>
                      <input type="number" name="amount" id="amount" readonly="readonly" class="form-control" value="">
                    </div>
                    <?php echo render_date_input('date','record_payment_date',_d(date('Y-m-d'))); ?>
                    <div class="form-group">
                      <label for="paymentmode" class="control-label"><?php echo _l('payment_mode'); ?><small class="req text-danger">* </small></label>
                      <select class="selectpicker" name="paymentmode" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value=""></option>
                        <?php foreach($payment_modes as $mode){ ?>
                          <option value="<?php echo $mode['id']; ?>" selected><?php echo $mode['name']; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label for="note" class="control-label"><?php echo _l('record_payment_leave_note'); ?></label>
                    <textarea name="note" class="form-control" rows="8" placeholder="<?php echo _l('invoice_record_payment_note_placeholder'); ?>" id="note"></textarea>
                  </div>
                </div>
                <div class="pull-right mtop15">
                  <a href="<?php echo admin_url('subscription'); ?>" class="btn btn-default"><?php echo _l('cancel'); ?></a>
                  <button type="submit" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" data-form="#record_payment_form" class="btn btn-info"><?php echo _l('proceed'); ?></button>
                </div>
              <?php echo form_close(); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
  $(function(){
    init_selectpicker();
    init_datepicker();
    _validate_form($('#subscription_payment_form'),{amount:'required',date:'required',paymentmode:'required', packagelist: 'required'});

    $("#packagelist").change(function(){
      $('#amount').val($('#packagelist option:selected').attr('data-price'));
    });
  });
</script>
</body>
</html>