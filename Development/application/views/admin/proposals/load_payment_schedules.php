<div class="payment-body">
  <?php if(!empty($paymentschedule->schedules)){ 
    $pe = 0; ?>
      <?php foreach ($paymentschedule->schedules as $pk => $pv) { ?>
        <div class='payment-wrapper' id='paymentwrapper-<?php echo $pe; ?>'>
          <ul class='payment-wrapper-ul'>
            <li>
              <span class='pull-right'>Unpaid</span></li><li>--</li><li><a href='javascript:void(0)' class='payment-way active'><?php echo $duedate_types[$pv['duedate_type']]; ?></a></li><li><a href='javascript:void(0)' class='payment-price'>$<span><?php echo !empty($pv['price_amount']) ? number_format($pv['price_amount']) : "0"; ?></span></a>
            </li>
          </ul>
          <div class='sub-payment-wrapper'>
            <div class='col-md-3'>
              <select class='selectpicker payment-data' name='payment_schedule[<?php echo $pe; ?>][duedate_type]' data-width='100%'' data-live-search='false'>
                <?php foreach ($duedate_types as $dtkey => $dtvalue) { ?>
                    <option value='<?php echo($dtkey); ?>' <?php echo ($dtkey == $pv['duedate_type']) ? "selected" : "" ?>><?php echo($dtvalue); ?></option>
                <?php } ?>
              </select>
            </div>
              <?php 
                if($pv['duedate_type'] == "custom"){ 
                    $payment_class = "";
                }else{
                    $payment_class = "hide";
                }
              ?>
              <div class='col-md-9 custom-payment-wrapper <?php echo($payment_class) ?>'>
                <div class='col-md-2'>
                  <input type='number' class='form-control' min='1' value='<?php echo $pv['duedate_number'] ?>' name='payment_schedule[<?php echo $pe; ?>][duedate_number]'>
                </div>
                <div class='col-md-3'>
                  <select class='selectpicker custom-range-duration' name='payment_schedule[<?php echo $pe; ?>][custom_range_duration]' data-width='100%'' data-live-search='false'>
                    <?php foreach ($duedate_duration as $ddkey => $ddvalue) { ?>
                      <option value='<?php echo($ddkey); ?>' <?php echo ($ddkey == $pv['custom_range_duration']) ? "selected" : "" ?>><?php echo($ddvalue); ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class='col-md-3'>
                  <select class='selectpicker custom-range-at' name='payment_schedule[<?php echo $pe; ?>][duedate_criteria]' data-width='100%'' data-live-search='false'>
                    <?php foreach ($duedate_criteria as $dckey => $dcvalue) { ?>
                      <option value='<?php echo($dckey); ?>' <?php echo ($dckey == $pv['duedate_criteria']) ? "selected" : "" ?>><?php echo($dcvalue); ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            
          </div>
          <div class='sub-payment-price-wrapper' style='display:none'>
            <div class='col-md-3'>
              <select class='selectpicker amount-data' name='payment_schedule[<?php echo $pe; ?>][price_type]' data-width='100%'' data-live-search='false'>
                <option value="">Select Option</option>
                <?php foreach ($amount_types as $atkey => $atvalue) { ?>
                  <option value='<?php echo($atkey); ?>' <?php echo ($atkey == $pv['price_type']) ? "selected" : "" ?>><?php echo($atvalue); ?></option>
                <?php } ?>
            </select>
            </div>
              <?php 
                if($pv['price_type'] == "fixed_amount"){ 
                    $amttype_class = "";
                }else{
                    $amttype_class = "hide";
                }
              ?>
              <div class='col-md-2 custom-payment-price-wrapper <?php echo($amttype_class) ?>'>
                <span>$</span>
                <input type='number' class='form-control price_amount' min='0' name='payment_schedule[<?php echo $pe; ?>][price_amount]' value="<?php echo($pv['price_amount']) ?>">
              </div>

              <?php 
                if($pv['price_type'] == "percentage"){ 
                    $ptttype_class = "";
                }else{
                    $ptttype_class = "hide";
                }
              ?>
            <div class='col-md-2 custom-payment-percentage-wrapper <?php echo($ptttype_class); ?>'>
                <input type='number' class='form-control' min='0' name='payment_schedule[<?php echo $pe; ?>][price_percentage]' value="<?php echo $pv['price_percentage']; ?>"> 
                <span>%</span>
            </div>
          </div>
      </div>
      <?php $pe++; } ?>
  <?php }else{ ?>
    <div class='payment-wrapper' id='paymentwrapper-0'>
        <ul class='payment-wrapper-ul'>
          <li><a href='javascript:void(0)' class='btn btn-danger remove-payment btn-xs'><i class='fa fa-times'></i></a>
            <span class='pull-right'>Unpaid</span></li><li>--</li><li><a href='javascript:void(0)' class='payment-way'>Midway</a></li><li><a href='javascript:void(0)' class='payment-price'>$<span>0</span></a></li>
        </ul>
        <div class='sub-payment-wrapper' style='display:none'>
          <div class='col-md-3'>
            <select class='selectpicker payment-data' name='payment_schedule[0][duedate_type]' data-width='100%'' data-live-search='false'>
              <?php foreach ($duedate_types as $dtkey => $dtvalue) { ?>
                  <option value='<?php echo($dtkey); ?>' <?php echo ($dtkey == "midway") ? "selected" : "" ?>><?php echo($dtvalue); ?></option>
              <?php } ?>
            </select>
          </div>
          <div class='col-md-9 custom-payment-wrapper hide'>
            <div class='col-md-2'>
              <input type='number' class='form-control' min='1' value='1' name='payment_schedule[0][duedate_number]'>
            </div>
            <div class='col-md-3'>
              <select class='selectpicker custom-range-duration' name='payment_schedule[0][custom_range_duration]' data-width='100%'' data-live-search='false'>
                <?php foreach ($duedate_duration as $ddkey => $ddvalue) { ?>
                  <option value='<?php echo($ddkey); ?>'><?php echo($ddvalue); ?></option>
                <?php } ?>
              </select>
            </div>
            <div class='col-md-3'>
              <select class='selectpicker custom-range-at' name='payment_schedule[0][duedate_criteria]' data-width='100%'' data-live-search='false'>
                <?php foreach ($duedate_criteria as $dckey => $dcvalue) { ?>
                  <option value='<?php echo($dckey); ?>'><?php echo($dcvalue); ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </div>
        <div class='sub-payment-price-wrapper' style='display:none'>
          <div class='col-md-3'>
            <select class='selectpicker amount-data' name='payment_schedule[0][price_type]' data-width='100%'' data-live-search='false'>
              <option value="">Select Option</option>
              <?php foreach ($amount_types as $atkey => $atvalue) { ?>
                <option value='<?php echo($atkey); ?>'><?php echo($atvalue); ?></option>
              <?php } ?>
          </select>
          </div>
          <div class='col-md-2 custom-payment-price-wrapper hide'>
            <span>$</span>
            <input type='number' class='form-control price_amount' min='0' name='payment_schedule[0][price_amount]'>
          </div>
          <div class='col-md-2 custom-payment-percentage-wrapper hide'>
            <input type='number' class='form-control' min='0' name='payment_schedule[0][price_percentage]'> 
            <span>%</span>
          </div>
        </div>
    </div>
  <?php } ?>
</div>