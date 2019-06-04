<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 06-03-2018
 * Time: 18:03
 */
/**/
if (isset($pv)) { ?>
    <div class='payment-wrapper psMobUI' id='paymentwrapper-<?php echo $pe; ?>'>
        <div class='payment-wrapper-ul row'>
            <div class="col-sm-1 psMobUI-icon activity_icon">
                <div class="icon_section">
                    <?php echo $pk + 1 ?>
                </div>
            </div>
            <div class="col-sm-3 psMobUI-col">
                <span class="payment_title">
                    <?php echo "Payment "; ?>
                    <?php echo $pk + 1; ?>
                </span>
            </div>
            <div class="col-sm-2 psMobUI-col">
                <span class="mobLabel">Due Date</span>
                <a href='javascript:void(0)' class='payment-way'><span>TBD</span>
                    <i class="fa fa-caret-down"></i>
                </a>
            </div>
            <div class="col-sm-2 psMobUI-col">
                <span class="mobLabel">Status</span>
                <span class='pull-left'>UNPAID</span>
            </div>
            <div class="col-sm-2 psMobUI-col">
                <span class="mobLabel">Pmt.Method</span>
                <a href='#' class='payment-method'>
                    <span><?php echo strtoupper($pv['payment_method']); ?></span>
                    <i class='fa fa-caret-down'></i>
                </a>
            </div>
            <div class="col-sm-2 psMobUI-col">
                <span class="mobLabel">Amount</span>
                <a href='javascript:void(0)' class='payment-wrapper-price'>
                <span><?php
                    if ($pv['price_type'] == "divide_equally") {
                        echo "Divide Equally";
                    } elseif ($pv['price_type'] == "fixed_amount") {
                        echo !empty($pv['price_amount']) ? format_money($pv['price_amount']) : "$0.00";
                    } else {
                        echo !empty($pv['price_percentage']) ? $pv['price_percentage'] . "%" : "0%";
                    }
                    ?></span>
                    <i class="fa fa-caret-down"></i>
                </a>
            </div>
            <div class="col-sm-1 psMobUI-removeCol removeCol">
                <div class='text-right mright10'>
                    <a class='show_act' href='javascript:void(0)'>
                        <i class='fa fa-ellipsis-v' aria-hidden='true'></i>
                    </a>
                </div>
                <div class='table_actions'>
                    <ul>
                        <li>
                            <a href='javascript:void(0)' class='btn-xs remove-payment'>
                                <i class='fa fa-times'></i>Delete
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class='sub-payment-wrapper' style="display: none;">
            <div class='col-sm-3 psMobUI-col col-wrap'>
                <select class='selectpicker payment-data' name='payment_schedule[<?php echo $pe; ?>][duedate_type]'
                        data-width='100%' data-live-search='false'>
                    <?php foreach ($duedate_types as $dtkey => $dtvalue) { ?>
                        <option value='<?php echo($dtkey); ?>' <?php echo ($dtkey == $pv['duedate_type']) ? "selected" : "" ?>>
                            <?php echo($dtvalue); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <?php
            if ($pv['duedate_type'] == "custom") {
                $payment_class = "";
            } else {
                $payment_class = "hide";
            }
            if ($pv['duedate_type'] == "fixed_date") {
                $payment_class2 = "";
            } else {
                $payment_class2 = "hide";
            }
            ?>
            <input type='hidden' class='form-control order'
                   value='<?php echo isset($pv[' order']) && $pv[' order'] > 0 ? $pv['order'] : $pk + 1; ?>'
                   name='payment_schedule[<?php echo $pe; ?>][order]'>
            <div class='col-sm-8 psMobUI-col custom-payment-wrapper <?php echo($payment_class) ?>'>
                <div class='col-sm-2 psMobUI-col'>
                    <input type='number' class='form-control' min='1' value='<?php echo $pv[' duedate_number'] ?>'
                           name='payment_schedule[<?php echo $pe; ?>][duedate_number]'>
                </div>
                <div class='col-sm-2 psMobUI-col'>
                    <select class='selectpicker custom-range-duration'
                            name='payment_schedule[<?php echo $pe; ?>][custom_range_duration]' data-width='100%'
                            data-live-search='false'>
                        <?php foreach ($duedate_duration as $ddkey => $ddvalue) { ?>
                            <option value='<?php echo($ddkey); ?>' <?php echo ($ddkey == $pv['custom_range_duration']) ? "selected" : "" ?>>
                                <?php echo($ddvalue); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class='col-sm-2 psMobUI-col'>
                    <select class='selectpicker custom-range-at'
                            name='payment_schedule[<?php echo $pe; ?>][duedate_criteria]' data-width='100%'
                            data-live-search='false'>
                        <?php foreach ($duedate_criteria as $dckey => $dcvalue) { ?>
                            <option value='<?php echo($dckey); ?>' <?php echo ($dckey == $pv['duedate_criteria']) ? "selected" : "" ?>>
                                <?php echo($dcvalue); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class='col-md-8 duedate_date <?php echo($payment_class2) ?>'>
                <input type='text' class='form-control datepicker'
                       value='<?php echo date(' m/d/Y', strtotime($pv['duedate_date'])); ?>'
                       name='payment_schedule[<?php echo $pe ?>][duedate_date]' autocomplete="off">
            </div>
        </div>
        <div class='sub-payment-price-wrapper' style='display:none'>
            <div class='col-wrap'>
                <div class='col-sm-6 psMobUI-col'>
                    <select class='selectpicker amount-data' name='payment_schedule[<?php echo $pe; ?>][price_type]'
                            data-width='100%' data-live-search='false'>
                        <?php foreach ($amount_types as $atkey => $atvalue) { ?>
                            <option value='<?php echo($atkey); ?>' <?php echo ($atkey == $pv['price_type']) ? "selected" : "" ?>>
                                <?php echo($atvalue); ?>
                            </option>
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
                <div class='col-sm-6 psMobUI-col custom-payment-price-wrapper <?php echo($amttype_class) ?>'>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-usd" aria-hidden="true"></i></span>
                        <input type='number' class='form-control price_amount' min='0'
                               name='payment_schedule[<?php echo $pe; ?>][price_amount]'
                               value="<?php echo($pv['price_amount']) ?>">
                    </div>
                </div>
                <?php
                if ($pv['price_type'] == "percentage") {
                    $ptttype_class = "";
                } else {
                    $ptttype_class = "hide";
                }
                ?>
                <div class='col-sm-6 psMobUI-col custom-payment-percentage-wrapper <?php echo($ptttype_class); ?>'>
                    <div class="input-group">
                        <input type='number' class='form-control price_percentage' min='0'
                               name='payment_schedule[<?php echo $pe; ?>][price_percentage]'
                               value="<?php echo $pv['price_percentage']; ?>">
                        <span class="input-group-addon"><i class="fa fa-percent" aria-hidden="true"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class='sub-payment-method-wrapper' style='display:none'>
            <div class='col-wrap'>
                <select class='selectpicker payment_method_val'
                        name='payment_schedule[<?php echo $pe ?>][payment_method]' data-width='100%'
                        data-live-search='false' data-pid='paymentwrapper-<?php echo $pe; ?>'>
                    <option <?php echo $pv['payment_method'] == 'any' ? 'selected' : '' ?>
                            value='any'>
                        <?php echo "ANY" ?>
                    </option>
                    <option <?php echo $pv['payment_method'] == 'cash' ? 'selected' : '' ?>
                            value='cash'>
                        <?php echo "CASH" ?>
                    </option>
                    <option <?php echo $pv['payment_method'] == 'check' ? 'selected' : '' ?>
                            value='check'>
                        <?php echo "CHECK" ?>
                    </option>
                    <option <?php echo $pv['payment_method'] == 'online' ? 'selected' : '' ?>
                            value='online'>
                        <?php echo "ONLINE" ?>
                    </option>
                </select>
            </div>
        </div>
    </div>
<?php } else { ?>
    <?php $payment_index = isset($payment_index) ? $payment_index : 0 ?>
    <div class="payment-wrapper psMobUI" id="paymentwrapper-<?php echo $payment_index ?>">
        <div class='payment-wrapper-ul row'>
            <div class="col-sm-1 psMobUI-col-icon activity_icon">
                <div class="icon_section">
                    <?php echo $payment_index + 1 ?>
                </div>
            </div>
            <div class="col-sm-3 psMobUI-col">
            <span class="payment_title">
                    <?php echo "Payment " ?>
                    <?php echo $payment_index + 1 ?>
                </span>
            </div>
            <div class="col-sm-2 psMobUI-col">
                <span class="mobLabel">Due Date</span>
                <a href='javascript:void(0)' class='payment-way'><span>TBD</span><i
                            class="fa fa-caret-down"></i></a></div>
            <div class="col-sm-2 psMobUI-col">
                <span class="mobLabel">Status</span>UNPAID
            </div>
            <div class="col-sm-2 psMobUI-col">
                <span class="mobLabel">Pmt.Method</span>
                <a href='javascript:void(0)' class='payment-method'><span>CASH</span><i
                            class='fa fa-caret-down'></i></a></div>
            <div class="col-sm-2 psMobUI-col">
                <span class="mobLabel">Amount</span>
                <a href='javascript:void(0)' class='payment-wrapper-price'><span>Divide Equally</span> <i
                            class='fa fa-caret-down'></i></a></div>
            <div class="col-sm-1 psMobUI-removeCol removeCol">
                <div class='text-right mright10'><a class='show_act' href='javascript:void(0)'><i
                                class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div>
                <div class='table_actions'>
                    <ul>
                        <li>
                            <a href='javascript:void(0)' class='btn-xs remove-payment'>
                                <i class='fa fa-times'></i>Delete
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>
        <input type='hidden' class='form-control order' value='<?php echo $payment_index + 1 ?>'
               name='payment_schedule[<?php echo $payment_index; ?>][order]'>
        <div class='sub-payment-wrapper' style='display:none'>
            <div class='col-md-2'>
                <select class='selectpicker payment-data'
                        name='payment_schedule[<?php echo $payment_index ?>][duedate_type]' data-width='100%'
                        data-live-search='false'>
                    <?php foreach ($duedate_types as $dtindex => $dtvalue) {
                        $dtselected = "";
                        if ($dtindex == 'midway') {
                            $dtselected = "selected";
                        } ?>
                        <option value='<?php echo $dtindex ?>' <?php echo $dtselected ?>>
                            <?php echo $dtvalue ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class='col-md-8 custom-payment-wrapper hide'>
                <div class='col-md-2'>
                    <input type='number' class='form-control' min='1' value='1'
                           name='payment_schedule[<?php echo $payment_index ?>][duedate_number]'>
                </div>
                <div class='col-md-3'>
                    <select class='selectpicker custom-range-duration'
                            name='payment_schedule[<?php echo $payment_index ?>][custom_range_duration]'
                            data-width='100%' data-live-search='false'>
                        <?php foreach ($duedate_duration as $ddindex => $ddvalue) { ?>
                            <option value='<?php echo $ddindex ?>'>
                                <?php echo $ddvalue ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class='col-md-3'>
                    <select class='selectpicker custom-range-at'
                            name='payment_schedule[<?php echo $payment_index ?>][duedate_criteria]' data-width='100%'
                            data-live-search='false'>
                        <?php foreach ($duedate_criteria as $dcindex => $dcvalue) { ?>
                            <option value='<?php echo $dcindex ?>'>
                                <?php echo $dcvalue ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class='col-md-8 duedate_date hide'>
                <input type='text' class='form-control datepicker'
                       name='payment_schedule[<?php echo $payment_index ?>][duedate_date]' autocomplete="off">
            </div>
        </div>
        <div class='sub-payment-price-wrapper' style='display:none'>
            <div class='col-wrap'>
                <div class='col-md-6'>
                    <select class='selectpicker amount-data'
                            name='payment_schedule[<?php echo $payment_index ?>][price_type]' data-width='100%'
                            data-live-search='false'>
                        <?php foreach ($amount_types as $atindex => $atvalue) { ?>
                            <option value='<?php echo $atindex ?>'>
                                <?php echo $atvalue ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class='col-md-6 custom-payment-price-wrapper hide'>
                    <div class="input-group">
                        <span class="input-group-addon"><i class="fa fa-usd" aria-hidden="true"></i></span>
                        <input type='number' class='form-control price_amount' min='0'
                               name='payment_schedule[<?php echo $payment_index ?>][price_amount]'>
                    </div>
                </div>
                <div class='col-md-6 custom-payment-percentage-wrapper hide'>
                    <div class="input-group">
                        <input type='number' class='form-control price_percent' min='0'
                               name='payment_schedule[<?php echo $payment_index ?>][price_percentage]'>
                        <span class="input-group-addon input-group-percent"><i class="fa fa-percent" aria-hidden="true"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class='sub-payment-method-wrapper' style='display:none'>
            <div class='col-md-2'>
                <select class='selectpicker payment_method_val'
                        name='payment_schedule[<?php echo $payment_index ?>][payment_method]' data-width='100%'
                        data-live-search='false' data-pid='paymentwrapper-<?php echo $payment_index; ?>'>
                    <option value='any'>
                        <?php echo "ANY" ?>
                    </option>
                    <option value='cash' selected>
                        <?php echo "CASH" ?>
                    </option>
                    <option value='check'>
                        <?php echo "CHECK" ?>
                    </option>
                    <option value='online'>
                        <?php echo "ONLINE" ?>
                    </option>
                </select>
            </div>
        </div>
    </div>
<?php } ?>