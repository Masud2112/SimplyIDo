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
$selectedItems = json_decode($proposal->feedback->selected_items);
$tiprice = 0.00;
$tax = 0.00;
foreach ($selectedItems as $selectedItem) {
    $tax = $tax + $selectedItem->tax;
    $tiprice += $selectedItem->subtotal - $selectedItem->tax;
}
$nextinvoice = $invoice = $proposal->invoices[0];
$feedback = $proposal->feedback;
$invoice_items = json_decode($feedback->selected_items, true);
?>
    <table class="section_body">
    <tr align="right" bgcolor="<?php echo get_option('pdf_table_heading_color'); ?>"
        style="height:30px; line-height: 30px ;color:<?php echo get_option('pdf_table_heading_text_color') ?>;">
        <th width="1%"></th>
        <th width="23%" align="left"><?php echo _l('name') ?></th>
        <th width="15%"><?php echo ucfirst(_l('qty')) ?></th>
        <th width="15%"><?php echo ucfirst(_l('price')) ?></th>
        <th width="15%"><?php echo ucfirst( _l('discount')) ?></th>
        <th width="15%"><?php echo ucfirst(_l('tax')) ?></th>
        <th width="15%"><?php echo ucfirst(_l('amount')) ?></th>
        <th width="1%"></th>
    </tr>
    <?php if (isset($invoice_items) && count($invoice_items) > 0) {
        $totalPrice = 0;
        $totalTax = 0;
        $fullsttl = 0;
        $totalmdisc = 0;
        foreach ($invoice_items as $invoice_item) {
            if (strtolower($invoice_item['type']) == 'package') {
                $item = $CI->invoice_items_model->get_group($invoice_item['id']);
                $id = $item->id;
                $name = $item->name;
                $sku = $item->group_sku;
                $price = $item->group_price;
                $description = $item->group_description;
            } else {
                $item = $CI->invoice_items_model->get_item($invoice_item['id']);
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
            <tr style="height: 40px; line-height: 40px" align="right">
                <td width="1%"></td>
            <td width="23%" align="left"><?php echo $name ?></td>
            <td width="15%">
                <?php echo $qty; ?>
            </td>
            <td width="15%">
                <?php echo format_money($price); ?>
            </td>
            <td width="15%">
                <?php
                if ($mdiscount < 0) {
                    echo str_replace("-", '-$', $mdiscount);
                } else {
                    echo format_money($mdiscount);
                }
                ?>
            </td>
            <td width="15%">
                <?php
                if (isset($item->is_taxable) && $item->is_taxable == 1) {
                    echo format_money($tax_val);
                } else {
                    echo "---";
                }
                ?>
            </td>
            <td width="15%">
                <?php echo format_money($subtotal); ?>
            </td>
                <td width="1%"></td>
            </tr>
        <?php }
    }
    ?>
    <tr align="right" bgcolor="<?php echo get_option('pdf_table_heading_color'); ?>"
        style="height:30px; line-height: 30px ;color:<?php echo get_option('pdf_table_heading_text_color') ?>;">
        <td width="1%"></td>
        <td width="23%" align="left"><?php _l('subtotal')?></td>
        <td width="15%"></td>
        <td width="15%"><?php echo format_money($totalPrice); ?></td>
        <td width="15%"><?php if ($totalmdisc < 0) {
                echo str_replace('-', '-$', $totalmdisc);
            } else {
                echo format_money($totalmdisc);
            } ?></td>
        <td width="15%"><?php echo $totalTax > 0 ? format_money($totalTax) : "---" ?></td>
        <td width="15%"><?php echo format_money($fullsttl); ?></td>
        <td width="1%"></td>
    </tr>
    </table>
    <table class="row invoice_totalSec_blk">
        <tr>
            <td></td>
        </tr>
        <tr>
            <td width="55%"><?php echo $proposal->client_message; ?></td>
            <td width="45%"><table><tr><th style="font-weight: bold; text-transform: capitalize"><?php echo _l('subtotal') ?></th>
                        <td align="right"><?php echo isset($proposal->feedback->proposal_subtotal) ? format_money($proposal->feedback->proposal_subtotal) : '' ?></td>
                    </tr>
                    <hr>
                    <?php if (isset($proposal) && !empty($proposal->othrdisctype) && $proposal->othrdiscval != 0) {
                        if ($proposal->othrdisctype == "percentage") {
                            $othrdiscPercent = $proposal->othrdiscval;
                        } else {
                            $othrdiscPercent = ($proposal->othrdiscval * 100) / $tiprice;
                            $othrdiscPercent = round($othrdiscPercent, 2);
                        }
                        ?>
                        <tr style="height: 50px; line-height: 50px">
                            <th style="font-size:12px; font-weight: bold; text-transform: capitalize"><?php echo "Other discount(";
                                echo "<span class='discpercent'>" . $othrdiscPercent . "</span>";
                                echo "%";
                                echo ")"; ?></th>
                            <td align="right" style="color: red"><?php
                                $proposal->othrdiscval = str_replace('-', "", $proposal->othrdiscval);
                                if ($proposal->othrdisctype == "percentage") {
                                    $proposal->othrdiscval = ($proposal->othrdiscval * $tiprice) / 100;
                                }
                                echo "-" . format_money($proposal->othrdiscval);
                                ?>
                            </td>
                        </tr>
                        <hr><?php } ?>
                    <?php if (isset($proposal) && !empty($proposal->other) && $proposal->otherval > 0) { ?>
                        <tr style="height: 50px; line-height: 50px">
                            <th style="font-weight: bold; text-transform: capitalize"><?php echo ucfirst($proposal->other); ?></th>
                            <td align="right"><?php echo format_money($proposal->otherval); ?></td>
                        </tr>
                        <hr>
                    <?php } ?>
                    <tr style="height: 50px; line-height: 50px">
                        <th style="font-weight: bold; text-transform: capitalize"><?php echo _l('total') ?></th>
                        <td align="right"><?php echo isset($proposal->feedback->proposal_total) ? format_money($proposal->feedback->proposal_total) : '' ?></td>
                    </tr><?php
                    $currentamount = get_invoice_total_left_to_pay($invoice->id, $invoice->total);
                    $currentpaymentid = 0;
                    if (isset($proposal) && $proposal->ps_template > 0) {
                        $pmt_sdl_template = $proposal->pmt_sdl_template;
                        $schedules = $pmt_sdl_template['paymentschedule']->schedules;
                        $paymentrecords = $nextinvoice->paymentrecords;
                        if (count($schedules) > 0) {
                            $currentamount = $schedules[0]['price_amount'];
                            $currentpaymentid = $schedules[0]['paymentdetailid'];
                            $totalpaidamount = 0;
                            foreach ($schedules as $key => $schedule) {
                                $extraPaidamount = 0;
                                if (count($paymentrecords) > 0 && !empty($paymentrecords[$key])) {
                                    $paidamount = $paymentrecords[$key]->amount - $paymentrecords[$key]->gratuity_val;
                                    $paymentdate = $paymentrecords[$key]->date;
                                    $totalpaidamount = $totalpaidamount + $paidamount;
                                    ?>
                                    <tr style="height: 50px; line-height: 50px">
                                    <td align="left" style="font-size:12px;"><b><?php
                                        if ($key + 1 == count($schedules) || $totalpaidamount == $nextinvoice->total) {
                                            echo "Final Payment";
                                        } else {
                                            echo "Payment" . ($key + 1);
                                        } ?>
                                        <?php
                                        echo "(<b>PAID:" . date('M d', strtotime($paymentdate)) . "</b>)"
                                            ?></b>
                                    </td>
                                    <td align="right"><?php echo "-" . format_money($paidamount); ?></td>
                                    </tr><?php } else {
                                    if (count($paymentrecords) > 0 && !empty($paymentrecords[$key - 1])) {
                                        $paidamount = $paymentrecords[$key - 1]->amount - $paymentrecords[$key - 1]->gratuity_val;
                                        $paymentdate = $paymentrecords[$key - 1]->date;
                                        if ($paidamount > $schedule['price_amount']) {
                                            $extraPaidamount = $paidamount - $schedule['price_amount'];
                                        }
                                    }
                                    if (get_invoice_total_left_to_pay($nextinvoice->id, $nextinvoice->total) > 0) { ?>
                                        <hr>
                                        <tr style="height: 50px; line-height: 50px">
                                            <td align="left" style="font-size:12px;"><b><?php
                                                if ($key + 1 == count($schedules)) {
                                                    echo "Final Payment";
                                                } else {
                                                    echo "Payment" . ($key + 1);
                                                }
                                                $dudate = date('M d', strtotime($schedule['duedate_date']));
                                                if (strtotime($schedule['duedate_date']) == strtotime(date('Y-m-d'))) {
                                                    $dudate = "Today";
                                                }echo "(DUE:" . $dudate . ")" ?></b>
                                            </td>
                                            <td align="right"><?php
                                                if ($extraPaidamount > 0 && $extraPaidamount < $schedule['price_amount']) {
                                                    $schedule['price_amount'] = $schedule['price_amount'] - $extraPaidamount;
                                                }
                                                if ($key + 1 == count($schedules)) {
                                                    $schedule['price_amount'] = get_invoice_total_left_to_pay($nextinvoice->id, $nextinvoice->total);
                                                }
                                                ?>
                                                <?php echo format_money($schedule['price_amount']); ?>
                                            </td>
                                        </tr>
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
                    if ($nextinvoice->status == 3) {
                        $proposal_total = get_invoice_total_left_to_pay($nextinvoice->id, $nextinvoice->total);
                    } else {
                        $proposal_total = $nextinvoice->total;
                    }
                    ?>
                </table>
            </td>
        </tr>
    </table>


<?php
/*if ($nextinvoice->status != 2 && isset($proposal_total)) { */ ?><!--
    <div class="fullPayments_blk">
        <div class="row">
            <div class="col-xs-7 text-right">
                <h5><?php /*echo _l('pay_in_full') */ ?></h5>
            </div>
            <div class="col-xs-5 proposal_actions text-right">
                <a class="btn btn-primary pay_in_full final_step proposal_step slickNext"
                   data-tab="payment"
                   data-payment="full"
                   data-paymentid="0"
                   data-total="<?php /*echo $proposal_total; */ ?>"
                   href="#">
                    <?php /*echo format_money($proposal_total); */ ?>
                </a>
            </div>
        </div>
    </div>
--><?php /*} */ ?>