<?php

/**
 * Created by PhpStorm.
 * User: masud
 * Date: 20-09-2018
 * Time: 04:44 PM
 */


$fivep = round((($proposal->feedback->proposal_total * 5) / 100), 2);
$tenp = round((($proposal->feedback->proposal_total * 10) / 100), 2);;
$fiftnep = round((($proposal->feedback->proposal_total * 15) / 100), 2);
$twentp = round((($proposal->feedback->proposal_total * 20) / 100), 2);
?>
<div id="check" class="tab-pane fade in col-sm-12">
    <div class="cell example example2">
        <?php echo form_open(site_url('proposal/payment'), array('id' => 'record_payment_form', 'class' => 'record_checkpayment_form')); ?>
        <div class="paymentPay_blk">
            <ul class="paymentPay_list_blk">
                <li class="cardDet_blk">
                    <div class="paybleto">
                        <div><strong>Payble to:</strong></div>
                        <div><strong>
                                <?php echo get_brand_option('invoice_company_name'); ?>
                            </strong></div>
                        <p><?php echo get_brand_option('invoice_company_address'); ?>
                            <?php echo get_brand_option('invoice_company_city'); ?>
                            <?php echo get_brand_option('company_state'); ?>
                            <?php echo get_brand_option('invoice_company_postal_code'); ?></p>
                    </div>
                    <div class="form-group">
                        <label for="checkfrom">From</label>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">
                                <i class="fa fa-user" aria-hidden="true"></i>
                            </span>
                            <input id="checkfrom" class="form-control" type="text" required=""
                                   name="from" aria-describedby="basic-addon1"
                                   value="<?php echo get_addressbook_full_name($invoice->clientid) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="checkestimated">Estimated Arrival</label>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon2">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                            </span>
                            <input id="checkestimated" class="datepicker form-control" type="text" required=""
                                   name="estimated" aria-describedby="basic-addon2" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="checkmessage">Message</label>
                        <div class="input-group">
                            <textarea id="checkmessage" class="form-control" name="message"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary pull-right " type="submit" <?php echo $authtype=="member"?"disabled":"" ?> >
                            <i class="fa fa-paper-plane" aria-hidden="true"></i>
                            <?php echo _l('send_message'); ?>
                        </button>
                    </div>
                </li>
                <?php
                if ($proposal->gratuity == 1) { ?>
                    <li class="grechuty_blk">
                        <label for="">Gratuity</label>
                        <div class="grechutyDist_blk">
                            <div class="full_gdb">
                                <a href="javascript:void(0)" class="active grtcal" data-val="0"
                                   data-amount="0">
                                    <span>No Thanks</span>
                                </a>
                            </div>
                            <div class="half_gdb">
                                <a href="javascript:void(0)" class="grtcal" data-val="5"
                                   data-amount="<?php echo $fivep ?>">
                                    <span>5%</span>
                                    <span>($<?php echo $fivep ?>)</span>
                                </a>
                            </div>
                            <div class="half_gdb">
                                <a href="javascript:void(0)" class="grtcal" data-val="10"
                                   data-amount="<?php echo $tenp ?>">
                                    <span>10%</span>
                                    <span>($<?php echo $tenp ?>)</span>
                                </a>
                            </div>
                            <div class="half_gdb">
                                <a href="javascript:void(0)" class="grtcal" data-val="15"
                                   data-amount="<?php echo $fiftnep ?>">
                                    <span>15%</span>
                                    <span>($<?php echo $fiftnep ?>)</span>
                                </a>
                            </div>
                            <div class="half_gdb">
                                <a href="javascript:void(0)" class="grtcal" data-val="20"
                                   data-amount="<?php echo $twentp ?>">
                                    <span>20%</span>
                                    <span>($<?php echo $twentp ?>)</span>
                                </a>
                            </div>
                            <div class="full_gdb custom">
                                <a href="javascript:void(0)" class="grtcal" data-val="custom">
                                    <span>Custom</span>
                                </a>
                            </div>
                        </div>
                        <div class="customGbg customgratuity hide">
                            <div class="input-group">
                                <span class="input-group-addon" id="basic-addon1">$</span>
                                <input id="customgratuity" class="form-control" type="text" value=""
                                       data-total="<?php echo $proposal->feedback->proposal_total ?>">
                            </div>
                        </div>
                    </li>
                    <?php
                } ?>
                <li class="totalPay_blk">

                    <label for="">Payment Option</label>
                    <?php
                    $maxamount = get_invoice_total_left_to_pay($invoice->id, $invoice->total);
                    if ($maxamount == $amount) {
                        $amount = get_invoice_total_left_to_pay($invoice->id, $invoice->total);
                    }
                    $a2 = $amount;
                    $remaining_amount = ($maxamount - $amount);
                    $amount = number_format((float)$amount, 2, '.', ',');
                    $maxamount = number_format((float)$maxamount, 2, '.', ',');
                    $remaining_amount = number_format((float)$remaining_amount, 2, '.', ',');
                    ?>
                    <div class="totalPay_inner_blk">
                        <div class="totalPay_link">

                            <ul class="payments">
                                <li class="payment active"
                                    data-total= <?php echo $amount ?> data-remaining= <?php echo $remaining_amount ?>>
                                    <a href="javascript:void(0)">This Payment</a>
                                </li>
                                <li class="fullpayment" data-total= <?php echo $maxamount ?>><a
                                            href="javascript:void(0)">Pay In Full</a></li>
                            </ul>
                        </div>
                        <div class="totalPay_Det">
                            <div class="totalPay_input_blk">
                                <ul class="totalPay_Input">
                                    <li>
                                        <label for="total" class="control-label">Progress Payment
                                            <small>PMT <?php echo(count($paidinvoices) + 1); ?>
                                                of <?php echo count($invoices); ?> |
                                                DUE <?php echo date('F,d', strtotime($nextinvoice->duedate)) ?></small>
                                        </label>
                                    </li>
                                    <li>
                                        <span class="customInput"> <!--<span class="currancy_symbol">$</span>--><input
                                                    id="total" type="text" class="paymenttotal form-control"
                                                    value="$<?php echo $amount ?>" min="<?php echo $amount; ?>"
                                                    max="<?php echo $maxamount ?>" data-grtuet="0"/>                                  </span>
                                    </li>
                                </ul>
                                <?php if ($proposal->gratuity == 1) { ?>

                                    <ul class="totalPay_Input">
                                        <li>
                                            <label class="inline-block">Gratuity
                                                <small class="grtpercent">(0%)</small>
                                            </label>
                                        </li>
                                        <li>
                                            <div class="gratuity_amount inline-block">$0</div>
                                        </li>
                                    </ul>
                                    <?php
                                } ?>

                            </div>
                            <div class="payNow_blk">
                                <div class="form-group">
                                    <ul class="totalPay_Input">
                                        <li>
                                            <input type="hidden" name="total" class="paymentamount form-control"
                                                   value="<?php echo $a2 ?>">
                                            <label for="total" class="control-label">Payment Total</label>
                                        </li>
                                        <li>
                                            <a class="processpay" href="#"><strong><?php echo "$" . $amount ?></strong></a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="form-group">

                                    <ul class="totalPay_Input">
                                        <li>
                                            <label for="total" class="control-label"> Remaining balance<br/> <span>(AFTER Payment)</span>
                                            </label>
                                        </li>
                                        <li>
                                            <a href="#" class="remaining_amount">
                                                <strong>$<?php echo $remaining_amount; ?> </strong> </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input name="invoiceid" type="hidden" id="invoice_id"
                           value="<?php echo $invoice->id; ?>">
                    <input name="gratuity_percent" type="hidden" id="gratuity_percent"
                           value="0" class="gratuity_percent">
                    <input name="gratuity_val" type="hidden" id="gratuity_val" class="gratuity_val"
                           value="0">
                    <?php echo form_hidden('custid', getcustomerid($invoice->clientid)); ?>
                    <?php echo form_hidden('paymentmode', 'check'); ?>
                    <div class="pull-right mtop15">
                        <input type="hidden" name="lid" value="<?php echo $lid; ?>">
                        <input type="hidden" name="type" value="<?php echo $type; ?>">
                        <input type="hidden" name="proposalid" value="<?php echo $proposal->templateid; ?>">
                        <?php if (isset($token)) { ?>
                            <input type="hidden" name="token" value="<?php echo $token; ?>">
                            <?php
                        } ?>
                    </div>
                </li>
            </ul>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
