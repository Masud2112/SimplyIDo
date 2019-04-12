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

<div id="stripe" class="tab-pane fade in active col-sm-12">
    <div class="cell example example2">
        <?php echo form_open('gateways/stripe/complete_purchase', array('id' => 'record_payment_form', 'class' => 'record_payment_form')); ?>
        <div class="paymentPay_blk">
            <ul class="paymentPay_list_blk">
                <li class="cardDet_blk">
                    <div data-locale-reversible>
                        <div class="form-group">
                            <label for="example2-name">Name On Card</label>
                            <div class="input-group">
                                                <span class="input-group-addon" id="basic-addon1"><i class="fa fa-user"
                                                                                                     aria-hidden="true"></i></span>
                                <input id="example2-name" class="form-control" type="text"
                                       required=""
                                       placeholder="Full name" aria-describedby="basic-addon1">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="field">
                            <label for="example2-card-number">Card number</label>

                            <div class="input-group">
                                                <span class="input-group-addon" id="basic-addon2"><i
                                                            class="fa fa-credit-card" aria-hidden="true"></i></span>
                                <div id="example2-card-number"
                                     class="input empty form-control"></div>
                            </div>
                        </div>
                    </div>
                    <div class="full-width">
                        <div class="field half-width">
                            <div class="form-group">
                                <label for="example2-card-expiry">Expiration</label>

                                <div class="input-group">
                                                    <span class="input-group-addon" id="basic-addon2"><i
                                                                class="fa fa-calendar-o" aria-hidden="true"></i></span>
                                    <div id="example2-card-expiry" class="empty form-control"></div>
                                </div>
                            </div>
                        </div>
                        <div class="field half-width">
                            <div class="form-group">
                                <label for="example2-card-cvc">CVC</label>
                                <div class="input-group">
                                                    <span class="input-group-addon" id="basic-addon2"><i
                                                                class="fa fa-check-circle-o"
                                                                aria-hidden="true"></i></span>
                                    <div id="example2-card-cvc" class="empty form-control"></div>
                                </div>
                            </div>
                        </div>
                        <div class="error" role="alert">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
                                 viewBox="0 0 17 17">
                                <path class="base" fill="#000"
                                      d="M8.5,17 C3.80557963,17 0,13.1944204 0,8.5 C0,3.80557963 3.80557963,0 8.5,0 C13.1944204,0 17,3.80557963 17,8.5 C17,13.1944204 13.1944204,17 8.5,17 Z"></path>
                                <path class="glyph" fill="#FFF"
                                      d="M8.5,7.29791847 L6.12604076,4.92395924 C5.79409512,4.59201359 5.25590488,4.59201359 4.92395924,4.92395924 C4.59201359,5.25590488 4.59201359,5.79409512 4.92395924,6.12604076 L7.29791847,8.5 L4.92395924,10.8739592 C4.59201359,11.2059049 4.59201359,11.7440951 4.92395924,12.0760408 C5.25590488,12.4079864 5.79409512,12.4079864 6.12604076,12.0760408 L8.5,9.70208153 L10.8739592,12.0760408 C11.2059049,12.4079864 11.7440951,12.4079864 12.0760408,12.0760408 C12.4079864,11.7440951 12.4079864,11.2059049 12.0760408,10.8739592 L9.70208153,8.5 L12.0760408,6.12604076 C12.4079864,5.79409512 12.4079864,5.25590488 12.0760408,4.92395924 C11.7440951,4.59201359 11.2059049,4.59201359 10.8739592,4.92395924 L8.5,7.29791847 L8.5,7.29791847 Z"></path>
                            </svg>
                            <span class="errormessage"></span></div>
                    </div>
                </li>
                <?php if ($proposal->gratuity == 1) { ?>
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
                                <li class="payment active" data-total= <?php echo $amount ?> data-remaining= <?php echo $remaining_amount ?>>
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
                                                <small>PMT <?php echo (count($paidinvoices) + 1); ?>
                                                    of <?php echo count($invoices); ?> |
                                                    DUE: <?php echo strtoupper(date('M,d', strtotime($nextinvoice->duedate))) ?></small>
                                            </label>
                                        </li>
                                        <li>
                                            <span class="customInput"> <!--<span class="currancy_symbol">$</span>-->
                                                <input id="total" type="text" class="paymenttotal form-control"
                                                    value="$<?php echo $amount ?>" min="<?php echo $amount; ?>"
                                                    max="<?php echo $maxamount ?>" data-grtuet="0"/>
                                            </span>
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
                                <?php } ?>
                            </div>
                            <div class="payNow_blk">
                                <div class="form-group">
                                    <div class="form-group">
                                        <ul class="totalPay_Input">
                                            <li>
                                                <input id="amount" type="hidden" name="total" class="paymentamount form-control"
                                                    value="<?php echo $a2 ?>">
                                                <input id="paymentid" type="hidden" name="paymentid"
                                                    class="paymentid form-control"
                                                    value="<?php echo $paymentid ?>">
                                                <input id="pschedule" type="hidden" name="pschedule"
                                                    class="pschedule form-control"
                                                    value="<?php echo $proposal->ps_template ?>">
                                                <label for="total" class="control-label"><i class="fa fa-lock"></i>Pay
                                                    Now</label>
                                            </li>
                                            <li>
                                                <button id="processpay" type="submit"
                                                        class="btn btn-primary" <?php echo $authtype=="member"?"disabled":"" ?>>$<?php echo $amount ?></button>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="form-group">
                                        <ul class="totalPay_Input">
                                            <li>
                                                <label for="total" class="control-label">
                                                    Remaining balance<br/>
                                                    <span>(AFTER Payment)</span>
                                                </label>
                                            </li>
                                            <li>
                                                <a href="#" class="remaining_amount">
                                                    <strong>$<?php echo $remaining_amount; ?>
                                                    </strong>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <input name="invoiceid" type="hidden" id="invoice_id"
                           value="<?php echo $invoice->id; ?>">
                    <input name="gratuity_percent" type="hidden" class="gratuity_percent"
                           value="0">
                    <input name="gratuity_val" type="hidden" class="gratuity_val"
                           value="0">
                    <?php //echo form_hidden('invoiceid', $invoice->id); ?>
                    <?php echo form_hidden('custid', getcustomerid($invoice->clientid)); ?>
                    <?php echo form_hidden('paymentmode', 'stripe'); ?>
                    <?php if (total_rows('tblemailtemplates', array('slug' => 'invoice-payment-recorded', 'active' => 0)) == 0) { ?>
                    <?php 
                } ?>
                    <div class="pull-right mtop15">
                        <input type="hidden" name="lid" value="<?php echo $lid; ?>">
                        <input type="hidden" name="type" value="<?php echo $type; ?>">
                    </div>
                </li>
            </ul>
        </div>

        <?php echo form_close(); ?>


    </div>
</div>
