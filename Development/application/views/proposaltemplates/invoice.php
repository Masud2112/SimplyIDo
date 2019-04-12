<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 02-08-2018
 * Time: 07:06 PM
 */
if (isset($_GET['pid']) && $_GET['pid'] > 0) {
    $rel_link = "?pid=" . $_GET['pid'];
} elseif (isset($_GET['lid']) && $_GET['lid'] > 0) {
    $rel_link = "?lid=" . $_GET['lid'];
} else {
    $rel_link = "";
}
?>
<div class="invoice">
    <div id="proposal_invoice">
        <?php
        $this->load->view('proposaltemplates/psl_section_head', array('title' => "invoice"));

        $invoice = $invoices[0];
        $feedback = $proposal->feedback;
        $invoice_items = json_decode($feedback->selected_items, true);
        ?>
        <div id="invoiced_items" class="invoiced_items">
            <div class="section_body">
                <div class="clearfix clear_with_groups"></div>
                <div class="quote_groups invoiced_items_header_blk">
                    <div class="row header">
                        <div class="col-sm-7">
                            <span class="nameTxt"><?php echo _l('name') ?></span>
                        </div>
                        <div class="col-sm-1 qty-col"><?php echo _l('qty') ?></div>
                        <div class="col-sm-1 price-col "><?php echo _l('price') ?></div>
                        <div class="col-sm-1 mark_disc-col"><?php echo _l('discount') ?></div>
                        <div class="col-sm-1 tax-col"><?php echo _l('tax') ?></div>
                        <div class="col-sm-1 subtotal-col "><?php echo _l('amount') ?></div>
                    </div>
                </div>
                <div class="invoiced_items_body_blk">
                    <?php if (isset($invoice_items) && count($invoice_items) > 0) {
                        $totalPrice = 0;
                        $totalTax = 0;
                        $fullsttl = 0;
                        $totalmdisc = 0;
                        foreach ($invoice_items as $invoice_item) {
                            if (strtolower($invoice_item['type']) == 'package') {
                                $item = $this->invoice_items_model->get_group($invoice_item['id']);
                                $id = $item->id;
                                $name = $item->name;
                                $sku = $item->group_sku;
                                $price = $item->group_price;
                                $description = $item->group_description;
                            } else {
                                $item = $this->invoice_items_model->get_item($invoice_item['id']);
                                $id = $item->id;
                                $name = $item->description;
                                $sku = $item->sku;
                                $price = $item->rate;
                                $description = $item->long_description;
                            }
                            $netprice = $price * $invoice_item['qty'];
                            $totalPrice = $totalPrice + $netprice;
                            $data['item_type'] = $invoice_item['type'];
                            $data['item'] = $item;
                            $data['qty'] = $invoice_item['qty'];
                            $tax_rate = 0;
                            $tax_val = 0;
                            if (isset($proposal) && $proposal->proposal_custom_tax > 0) {
                                $ptax = get_tax_rate_by_id($proposal->proposal_custom_tax);
                                $tax_rate = $ptax->taxrate;
                            }
                            $qty = (isset($invoice_item['qty']) && $invoice_item['qty'] > 0 ? $invoice_item['qty'] : 1);
                            $mdiscount = 0;
                            if (isset($invoice_item['mdiscount']) && !empty($invoice_item['mdiscount'])) {
                                if (isset($invoice_item['mdistype']) && $invoice_item['mdistype'] == "percentage") {
                                    $mdiscount = $invoice_item['mdiscount'];
                                    $mdiscount = (($price * $mdiscount) / 100) * $qty;
                                } else {
                                    $mdiscount = $invoice_item['mdiscount'] * $qty;
                                }
                            }
                            $totalmdisc = $totalmdisc + $mdiscount;

                            $subtotal = ($qty * $price) + $mdiscount;
                            $class = "";
                            if ($mdiscoun_type == "discount") {
                                $class = "danger";
                            }
                            if (isset($item->is_taxable) && $item->is_taxable == 1 && $tax_rate > 0) {
                                $tax_val = (($subtotal * $tax_rate) / 100);
                                $subtotal = $subtotal + $tax_val;
                                $totalTax = $totalTax + $tax_val;
                            }
                            $fullsttl = $fullsttl + $subtotal;
                            $disabled = "";
                            ?>
                            <div class='invoiced_item'>
                                <div class="row psMobUI">
                                    <div class="col-sm-7 psMobUI-col">
                                        <span class="mobLabel">Name :</span>
                                        <?php echo $name ?>
                                    </div>
                                    <div class="col-sm-1 psMobUI-col">
                                        <span class="mobLabel">Qty :</span>
                                        <?php echo $qty; ?>
                                    </div>
                                    <div class="col-sm-1 psMobUI-col">
                                        <span class="mobLabel">Price :</span>
                                        <?php echo format_money($price); ?>
                                    </div>
                                    <div class="col-sm-1 psMobUI-col ">
                                        <span class="mobLabel">Discount :</span>
                                        <?php
                                        if ($mdiscount < 0) {
                                            echo str_replace("-", '-$', $mdiscount);
                                        } else {
                                            echo format_money($mdiscount);
                                        }
                                        ?>
                                    </div>
                                    <div class="col-sm-1 psMobUI-col">
                                        <span class="mobLabel">Tax :</span>
                                        <?php
                                        if (isset($item->is_taxable) && $item->is_taxable == 1) {
                                            echo format_money($tax_val);
                                        } else {
                                            echo "---";
                                        }
                                        ?>
                                    </div>

                                    <div class="col-sm-1 psMobUI-col">
                                        <span class="mobLabel">Subtotal :</span>
                                        <?php echo format_money($subtotal) ; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="row">
                                        <div class="col-sm-3"></div>
                                        <div class="col-sm-9 desc_inner"
                                             style="display: none"><?php echo strip_tags($description); ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    }
                    ?>
                </div>
                <div class="clearfix"></div>
                <div class="invoice_footer invoiced_items_footer_blk">
                    <div class="row">
                        <div class="col-sm-7">
                            <div class="invoice_items">SUBTOTAL</div>
                        </div>
                        <div class="col-sm-1 "></div>
                        <div class="col-sm-1 "><span class="invoice_sbttl"><?php echo format_money($totalPrice); ?></span>
                        </div>
                        <div class="col-sm-1 "><?php if ($totalmdisc < 0) {
                                echo str_replace('-', '-$', $totalmdisc);
                            } else {
                                echo format_money($totalmdisc) ;
                            } ?></div>
                        <div class="col-sm-1 "><?php echo $totalTax > 0 ? format_money($totalTax) : "---" ?></div>
                        <div class="col-sm-1 "><?php echo format_money($fullsttl);  ?></div>
                        <div class="col-sm-1 "></div>
                    </div>
                </div>
            </div>
            <div class="row invoice_totalSec_blk">
                <div class="col-md-4 col-xs-12 pull-right">
                    <div class="final_total text-right">
                        <div class="psubtotal">
                            <div class="row">
                                <div class="col-xs-7">
                                    <h5><?php echo _l('subtotal') ?></h5>
                                </div>
                                <div class="col-xs-5">
                                    <?php echo "$" . number_format($feedback->proposal_subtotal, 2) ?>
                                </div>
                            </div>
                        </div>
                        <?php if (isset($proposal) && !empty($proposal->othrdisctype) && $proposal->othrdiscval != 0) { ?>
                            <div class="potherdisc">
                                <div class="row">
                                    <div class="col-xs-7">
                                        <h5><?php echo "Other discount(";
                                            echo "<span class='discpercent'>" . $proposal->othrdiscval . "</span>";
                                            echo "%";
                                            echo ")"; ?></h5>
                                    </div>
                                    <div class="col-xs-5">
                                        <span class="proposal_othedisc"
                                              data-val="<?php echo $proposal->othrdiscval; ?>"
                                              data-valtype="<?php echo $proposal->othrdisctype; ?>">
                                            <?php
                                            echo "-$";
                                            echo "<span class='discamount'>" . $proposal->othrdiscval . "</span>";
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php
                        if (isset($proposal) && !empty($proposal->other) && $proposal->otherval > 0) { ?>
                            <div class="pother">
                                <div class="row">
                                    <div class="col-xs-7">
                                        <h5><?php echo $proposal->other; ?></h5>
                                    </div>
                                    <div class="col-xs-5">
                                        <span class="proposal_otherval psymbol"
                                              data-val="<?php echo $proposal->otherval; ?>"><?php echo number_format($proposal->otherval, 2); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="ptotal 1213">
                            <div class="row">
                                <div class="col-xs-7">
                                    <h5><?php echo _l('total') ?></h5>
                                </div>
                                <div class="col-xs-5">
                                    <h5 class=""><?php echo "$" . number_format($feedback->proposal_total, 2); ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="payments">
                            <?php
                            $currentamount = get_invoice_total_left_to_pay($invoice->id, $invoice->total);
                            $currentpaymentid = 0;
                            if (isset($proposal) && $proposal->ps_template > 0) {
                                $pmt_sdl_template = $proposal->pmt_sdl_template;
                                $schedules = $pmt_sdl_template['paymentschedule']->schedules;
                                $paymentrecords = $nextinvoice->paymentrecords;
                                if (count($schedules) > 0) {
                                    $currentamount = $schedules[0]['price_amount'];
                                    $currentpaymentid = $schedules[0]['paymentdetailid'];
                                    $totalpaidamount=0;
                                    foreach ($schedules as $key => $schedule) {
                                        $extraPaidamount = 0;
                                        if (count($paymentrecords) > 0 && !empty($paymentrecords[$key])) {
                                            $paidamount = $paymentrecords[$key]->amount - $paymentrecords[$key]->gratuity_val;
                                            $paymentdate = $paymentrecords[$key]->date;
                                            $totalpaidamount=$totalpaidamount+$paidamount;
                                            ?>
                                            <div class="row">
                                                <div class="col-xs-7">
                                                    <h5><?php
                                                        if ($key + 1 == count($schedules) || $totalpaidamount==$nextinvoice->total) {
                                                            echo "Final Payment";
                                                        } else {
                                                            echo "Payment" . ($key + 1);
                                                        } ?>
                                                    </h5>
                                                    <?php
                                                    echo "<span class='invoicedtls'>(<span class='paid'>PAID:" . date('M d', strtotime($paymentdate)) . "</span>)</span>"
                                                    ?>
                                                </div>
                                                <div class="col-xs-5 proposal_actions">
                                                <span class="danger">
                                                    <?php echo "-" . format_money($paidamount); ?>
                                                </span>
                                                </div>
                                            </div>
                                        <?php } else {
                                            if (count($paymentrecords) > 0 && !empty($paymentrecords[$key - 1])) {
                                                $paidamount = $paymentrecords[$key - 1]->amount - $paymentrecords[$key - 1]->gratuity_val;
                                                $paymentdate = $paymentrecords[$key - 1]->date;
                                                if ($paidamount > $schedule['price_amount']) {
                                                    $extraPaidamount = $paidamount - $schedule['price_amount'];
                                                }
                                            }
                                            if (get_invoice_total_left_to_pay($nextinvoice->id, $nextinvoice->total) > 0) {
                                                ?>
                                                <div class="row">
                                                    <div class="col-xs-7">
                                                        <h5><?php
                                                            if ($key + 1 == count($schedules)) {
                                                                echo "Final Payment";
                                                            } else {
                                                                echo "Payment" . ($key + 1);
                                                            } ?>
                                                        </h5>
                                                        <?php
                                                        $dudate = date('M d', strtotime($schedule['duedate_date']));
                                                        if (strtotime($schedule['duedate_date']) == strtotime(date('Y-m-d'))) {
                                                            $dudate = "Today";
                                                        }
                                                        echo "<span class='invoicedtls'>(<span class='due'>DUE:" . $dudate . "</span>)</span>" ?>
                                                    </div>
                                                    <div class="col-xs-5 proposal_actions ">
                                                        <?php
                                                        if ($extraPaidamount > 0 && $extraPaidamount < $schedule['price_amount']) {
                                                            $schedule['price_amount'] = $schedule['price_amount'] - $extraPaidamount;
                                                        }
                                                        if ($key + 1 == count($schedules)) {
                                                            $schedule['price_amount'] = get_invoice_total_left_to_pay($nextinvoice->id, $nextinvoice->total);
                                                        }
                                                        ?>
                                                        <a class="btn btn-primary pay_in_full proposal_step slickNext"
                                                           data-tab="payment"
                                                           data-total="<?php echo $schedule['price_amount']; ?>"
                                                           data-paymentid="<?php echo $schedule['paymentdetailid']; ?>"
                                                           href="#">
                                                            <?php echo format_money($schedule['price_amount']); ?>
                                                        </a>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            $currentamount = $schedule['price_amount'];
                                            $currentpaymentid = $schedule['paymentdetailid'];
                                        }
                                        if (count($nextinvoice->paymentrecords) == $key) {
                                            break;
                                        }
                                    } ?>

                                <?php }
                            }
                            /*foreach ($invoices as $key => $invoice) {*/ ?>

                            <?php
                            /*if ($invoice->status != 2) {
                                break;
                            }
                        }*/
                            if ($nextinvoice->status == 3) {
                                $proposal_total = get_invoice_total_left_to_pay($nextinvoice->id, $nextinvoice->total);
                            } else {
                                $proposal_total = $nextinvoice->total;
                            }
                            ?>
                        </div>
                        <?php
                        if ($nextinvoice->status != 2 && isset($proposal_total)) { ?>
                            <div class="fullPayments_blk">
                                <div class="row">
                                    <div class="col-xs-7 text-right">
                                        <h5><?php echo _l('pay_in_full') ?></h5>
                                    </div>
                                    <div class="col-xs-5 proposal_actions text-right">
                                        <a class="btn btn-primary pay_in_full final_step proposal_step slickNext"
                                           data-tab="payment"
                                           data-payment="full"
                                           data-paymentid="0"
                                           data-total="<?php echo $proposal_total; ?>"
                                           href="#">
                                            <?php echo format_money($proposal_total); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-md-8 col-xs-12">
                    <!--<div class="quote_note">
                        <h5><?php /*echo _l('notes') */?></h5>
                        <p><?php /*echo _l('proposal_notes_text') */?></p>
                    </div>
                    <div class="quote_terms_condition">
                        <h5><?php /*echo _l('terms_condition') */?></h5>
                        <p><?php /*echo _l('proposal_terms_condition') */?></p>
                    </div>-->
                    <?php echo $proposal->client_message; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="proposal_actions text-center mtop35">
        <div class="inline-block">
            <a class="btn btn-info"
               href="<?php echo $proposal->status == "draft" ? admin_url('proposaltemplates/proposal/' . $proposal->templateid) . $rel_link : admin_url('proposaltemplates') . $rel_link; ?>"
               onclick="self.close()">
                <i class="fa fa-reply" aria-hidden="true"></i>
                <?php echo _l('exit_proposal'); ?>
            </a>
        </div>
        <div class="inline-block">
            <a class="btn btn-primary proposal_step slickNext " data-tab="agreement"
               href="#">
                <i class="fa fa-angle-left" aria-hidden="true"></i>
                <?php echo _l('agreement'); ?>
            </a>
        </div>
        <?php
        if ($proposal->isclosed == 0 && get_invoice_total_left_to_pay($nextinvoice->id, $nextinvoice->total)!=0) { ?>
            <div class="inline-block">
                <a class="btn btn-primary proposal_step slickNext " data-tab="payment"
                   href="#">
                    <?php echo _l('payment'); ?>
                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                </a>
            </div>
        <?php } ?>
    </div>
</div>
<div id="makepayment">
</div>
</div>
<?php echo form_close(); ?>
<div class="makepayment hide">
    <?php $this->load->view('proposaltemplates/makepayment', array('amount' => $currentamount, 'paymentid' => $currentpaymentid)); ?>
</div>
