<div class="col-md-12 no-padding animated fadeIn">
    <div class="panel_s">
        <?php echo form_open('admin/subscription/record_payment',array('id'=>'subscription_payment_form')); ?>
        <?php echo form_hidden('packageid',$subscription->packageid); ?>
        <div class="panel-body">
            <h4 class="no-margin"><?php echo _l('record_payment_for_invoice'); ?> "<?php echo $subscription->name; ?>" package</h4>
           <hr class="hr-panel-heading" />
            <div class="row">
                <div class="col-md-6">
                    <?php
                    $amount = $subscription->price;
                    echo render_input('amount','record_payment_amount_received',$amount,'number',array('max'=>$amount)); ?>
                    <?php echo render_date_input('date','record_payment_date',_d(date('Y-m-d'))); ?>
                    <div class="form-group">
                        <label for="paymentmode" class="control-label"><?php echo _l('payment_mode'); ?></label>
                        <select class="selectpicker" name="paymentmode" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <option value=""></option>
                            <?php foreach($payment_modes as $mode){ ?>
                            <option value="<?php echo $mode['id']; ?>" selected><?php echo $mode['name']; ?></option>
                            <!-- <option value="stripe">Stripe</option>
                            <option value="paypal">Paypal</option> -->
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
                
                <a href="#" class="btn btn-default" onclick="init_subscription();"><?php echo _l('cancel'); ?></a>
                <button type="submit" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" data-form="#record_payment_form" class="btn btn-info"><?php echo _l('proceed'); ?></button>
            </div>
            
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script type="text/javascript">
   $(function(){
     init_selectpicker();
     init_datepicker();
     _validate_form($('#subscription_payment_form'),{amount:'required',date:'required',paymentmode:'required'});
 });
</script>
