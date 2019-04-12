<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 06-03-2018
 * Time: 18:03
 */
/**/
/*echo "<pre>";
print_r($pv);
die();*/
if(isset($rel_content)){
    $date1 = date_create($rel_content->eventstartdatetime);
    $date2 = date_create();
    $diff_days=date_diff($date1,$date2);
    $diff_days = $diff_days->format("%a")/2;
    $diff_days = round($diff_days);
}

if (isset($pv)) {
    if ($pv['duedate_date'] == '0000-00-00' || $pv['duedate_date'] == '') {
        //$pv['duedate_date']=date('m/d/Y');
        $pv['duedate_date'] = "TBD";
    } else {
        $pv['duedate_date'] = date_create($pv['duedate_date']);
        $pv['duedate_date'] = $duedate_date = date_format($pv['duedate_date'], 'm/d/Y');
    }
    if ($pv['duedate_type'] == "today") {
        $pv['duedate_type'] = "upon_signing";
    }
    ?>
    <div class='payment-wrapper <?php echo $pv['status']==0?"unpaid":"paid";?>' id='paymentwrapper-<?php echo $pe; ?>'
         data-pid="<?php echo $pv['paymentdetailid']; ?>" >
        <div class='payment-wrapper-ul row psMobUI'>
            <div class="col-xs-1 activity_icon psMobUI-icon">
                <div class="icon_section"> <?php echo $pk + 1 ?></div>
            </div>
            <div class="col-xs-3 psMobUI-col">
                <span href='javascript:void(0)' class='payment-way'>
                <span class="payment_title">
                    <?php echo _l('payment', $pk + 1); ?>
                </span><!--<i class="fa fa-caret-down"></i>-->
                </span>
            </div>
            <div class="col-xs-2 psMobUI-col kanban-card-block proposal_payments"> 
                
             <span class="mobLabel">Due Date</span>
                <?php
                if ($pv['duedate_type'] == "fixed_date" || ($pv['duedate_type'] == "project_date" && isset($rel_content)) || $pv['duedate_type'] == "custom") {
                    if ($pv['duedate_type'] == "project_date") {
                        $duedate_date = $rel_content->eventstartdatetime;
                    } elseif ($pv['duedate_type'] == "custom" && ($pv['duedate_criteria'] == 'beforeproject' || $pv['duedate_criteria'] == 'afterproject')) {
                        $duedate_date = $rel_content->eventstartdatetime;
                        $duedate_date = str_replace('-', '/', $duedate_date);
                        if ($pv['duedate_criteria'] == 'beforeproject') {
                            $duedate_date = date('m/d/Y', strtotime('-' . $pv['duedate_number'] . ' ' . $pv['custom_range_duration'], strtotime($duedate_date)));
                        } else {
                            $duedate_date = date('m/d/Y', strtotime('+' . $pv['duedate_number'] . ' ' . $pv['custom_range_duration'], strtotime($duedate_date)));
                        }

                    }
                    if ($pv['duedate_type'] == "custom" && ($pv['duedate_criteria'] == 'afterinvoice')) { ?>
                        <div class="carddate-block">
                            <div class="card_date">
                                <div class="card_month">
                                    <small><?php echo _l('due') ?></small>
                                </div>
                                <div class="card_d">
                                    <strong><?php echo "TBD" ?></strong>
                                </div>
                                <div class="card_day">
                                    <small></small>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="carddate-block">
                            <div class="card_date" title="<?php echo date('Y', strtotime($duedate_date)) ?>">
                                <div class="card_month">
                                    <small><?php echo date('M', strtotime($duedate_date)) ?></small>
                                </div>
                                <div class="card_d">
                                    <strong><?php echo date('d', strtotime($duedate_date)) ?></strong>
                                </div>
                                <div class="card_day">
                                    <small><?php echo date('D', strtotime($duedate_date)) ?></small>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="carddate-block">
                        <div class="card_date" title="<?php echo $duedate_types[$pv['duedate_type']]; ?>">
                            <div class="card_month">
                                <small><?php echo _l('due') ?></small>
                            </div>
                            <div class="card_d">
                                <strong></strong>
                            </div>
                            <div class="card_day dueDayCust_blk">
                                <span><?php echo $duedate_types[$pv['duedate_type']]; ?></span>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="col-xs-1 psMobUI-col">
            <span class="mobLabel">Status</span>
                <span class='pull-left'><?php echo $pv['status']==0?_l('unpaid'):_l('paid');?></span>
            </div>
            <div class="col-xs-2 psMobUI-col">
                <span class="mobLabel">Pmt.Method</span>
                <span href='javascript:void(0)'
                      class='payment-method'><span><?php echo strtoupper($pv['payment_method']); ?></span>
                    <!--<i class='fa fa-caret-down'></i>--></span>
            </div>
            <div class="col-xs-2 psMobUI-col">
                <span class="mobLabel">Amount</span>
                <span href='javascript:void(0)'
                      class='payment-price'>$<span><?php echo !empty($pv['price_amount']) ? number_format($pv['price_amount']) : "0"; ?></span>
                    <!--<i class="fa fa-caret-down"></i>-->
                    <input type='hidden' class='form-control'
                           name='payment_schedule[<?php echo $pe; ?>][paymentdetailid]'
                           value="<?php echo $pv['paymentdetailid']; ?>" />

                    <input type='hidden' class='form-control price_amount' min='0'
                           name='payment_schedule[<?php echo $pe; ?>][price_amount]'
                           value="<?php echo($pv['price_amount']) ?>">
                </span>
            </div>
        </div>
        <div class='sub-payment-wrapper' style="display: none;">
            <input type='hidden' class='form-control order' value='<?php echo $pe + 1 ?>'
                   name='payment_schedule[<?php echo $pe; ?>][order]'>
            <div class='col-xs-2'>
                <select class='selectpicker payment-data' name='payment_schedule[<?php echo $pe; ?>][duedate_type]'
                        data-width='100%' data-live-search='false'>
                    <?php foreach ($duedate_types as $dtkey => $dtvalue) { ?>
                        <option value='<?php echo($dtkey); ?>' <?php echo ($dtkey == $pv['duedate_type']) ? "selected" : "" ?>><?php echo($dtvalue); ?></option>
                    <?php } ?>
                </select>
            </div>
            <?php
            if ($pv['duedate_type'] == "custom") {
                $payment_class = "";
            } else {
                $payment_class = "hide";
            }
            ?>
            <div class='col-xs-9 custom-payment-wrapper <?php echo($payment_class) ?>'>
                <div class='col-xs-2'>
                    <input type='number' class='form-control' min='1' value='<?php echo $pv['duedate_number'] ?>'
                           name='payment_schedule[<?php echo $pe; ?>][duedate_number]'>
                </div>
                <div class='col-xs-1'>
                    <select class='selectpicker custom-range-duration'
                            name='payment_schedule[<?php echo $pe; ?>][custom_range_duration]' data-width='100%'
                            data-live-search='false'>
                        <?php foreach ($duedate_duration as $ddkey => $ddvalue) { ?>
                            <option value='<?php echo($ddkey); ?>' <?php echo ($ddkey == $pv['custom_range_duration']) ? "selected" : "" ?>><?php echo($ddvalue); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class='col-xs-2'>
                    <select class='selectpicker custom-range-at'
                            name='payment_schedule[<?php echo $pe; ?>][duedate_criteria]' data-width='100%'
                            data-live-search='false'>
                        <?php foreach ($duedate_criteria as $dckey => $dcvalue) { ?>
                            <option value='<?php echo($dckey); ?>' <?php echo ($dckey == $pv['duedate_criteria']) ? "selected" : "" ?>><?php echo($dcvalue); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class='col-md-8 duedate_date'>
                <?php  if ($pv['duedate_type'] == "upon_signing") {
                    $duedate_date = date('m/d/Y');
                }elseif ($pv['duedate_type'] == "midway") {
                    $duedate_date = date('m/d/Y', strtotime('+' . $diff_days." days", strtotime(date('m/d/Y'))));
                }else{
                    $duedate_date = date('m/d/Y', strtotime($duedate_date));
                }?>
                <input type='text' class='form-control datepicker'
                       value='<?php echo $duedate_date; ?>'
                       name='payment_schedule[<?php echo $pe ?>][duedate_date]' autocomplete="off">
            </div>
        </div>
        <div class='sub-payment-price-wrapper' style='display:none'>
            <div class='col-xs-2'>
                <select class='selectpicker amount-data' name='payment_schedule[<?php echo $pe; ?>][price_type]'
                        data-width='100%' data-live-search='false'>
                    <?php foreach ($amount_types as $atkey => $atvalue) { ?>
                        <option value='<?php echo($atkey); ?>' <?php echo ($atkey == $pv['price_type']) ? "selected" : "" ?>><?php echo($atvalue); ?></option>
                    <?php } ?>
                </select>
            </div>
            <?php
            if ($pv['price_type'] == "fixed_amount") {
                $amttype_class = "";
            } else {
                $amttype_class = "hide";
            }
            ?>
            <div class='col-xs-2 custom-payment-price-wrapper <?php echo($amttype_class) ?>'>
                <span>$</span>
                <!--<input type='number' class='form-control price_amount' min='0'
                       name='payment_schedule[<?php /*echo $pe; */ ?>][price_amount]'
                       value="<?php /*echo($pv['price_amount']) */ ?>">-->
            </div>

            <?php
            if ($pv['price_type'] == "percentage") {
                $ptttype_class = "";
            } else {
                $ptttype_class = "hide";
            }
            ?>
            <div class='col-xs-2 custom-payment-percentage-wrapper <?php echo($ptttype_class); ?>'>
                <input type='number' class='form-control price_percentage' min='0'
                       name='payment_schedule[<?php echo $pe; ?>][price_percentage]'
                       value="<?php echo $pv['price_percentage']; ?>">
                <span>%</span>
            </div>
        </div>
        <div class='sub-payment-method-wrapper' style='display:none'>
            <div class='col-xs-3'>
                <select class='selectpicker payment_method_val'
                        name='payment_schedule[<?php echo $pe ?>][payment_method]' data-width='100%'
                        data-live-search='false' data-pid='paymentwrapper-<?php echo $pe; ?>'>
                    <option <?php echo $pv['payment_method'] == 'cash' ? 'selected' : '' ?>
                            value='cash'><?php echo "CASH" ?></option>
                    <option <?php echo $pv['payment_method'] == 'check' ? 'selected' : '' ?>
                            value='check'><?php echo "CHECK" ?></option>
                    <option <?php echo $pv['payment_method'] == 'bank transfer' ? 'selected' : '' ?>
                            value='bank transfer'><?php echo "BANK TRANSFER" ?></option>
                    <option <?php echo $pv['payment_method'] == 'online' ? 'selected' : '' ?>
                            value='online'><?php echo "ONLINE" ?></option>
                    <option <?php echo $pv['payment_method'] == 'any' ? 'selected' : '' ?>
                            value='any'><?php echo "ANY" ?></option>
                </select>
            </div>
        </div>
    </div>
<?php } ?>