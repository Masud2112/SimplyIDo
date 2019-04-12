<?php
/**/

/*echo "<pre>";
print_r($proposal);
die('<--here');*/
/*echo "<pre>";
print_r(json_decode($proposal->feedback->selected_items));
die('<--here');*/
$tiprice = 0.00;
$tax = 0.00;
$totalSI = 0;
$tIsubtotal = 0.00;
$Isubtotal = 0.00;
$tImkpdisc = 0.00;
$Itax_val = 0.00;

$removed_sections = array();
if (isset($proposal)) {
    $signatures = json_decode($proposal->signatures, true);
    $removed_sections = json_decode($proposal->removed_sections, true);
}
$class = "";
$checked = "";
$colclass = "";
if (isset($removed_sections)) {
    $class = in_array('quote', $removed_sections) ? "removed_section" : "";
    $checked = in_array('quote', $removed_sections) ? "checked" : "";
}

$packages = $proposal->items_groups;
$items = $proposal->items;
$selected_items = array();

if (isset($quotes) && count($quotes) > 0) {
    foreach ($quotes as $gid => $quote) {
        $quote_items = json_decode($quote['quote_items'], true);
        if (!empty($quote_items)) {
            foreach ($quote_items as $quote_item) {
                $si[] = strtolower($quote_item['type']) . "_" . $quote_item['id'];
            }
        }
        $quote['gid'] = $gid;
        $selected_items = isset($si) ? $si : array();
        include "quote_group.php";
    }

    $totalSI = count($selected_items);
    /*if (!empty($proposal->proposal_custom_tax) && $proposal->proposal_custom_tax > 0) {
        $ptax = get_tax_rate_by_id($proposal->proposal_custom_tax);
        $tax_rate = $ptax->taxrate;
    }
    foreach ($selected_items as $selected_item) {
        $selected_item = explode('_', $selected_item);
        $itype = $selected_item[0];
        $itId = $selected_item[1];
        if ($itype == "product") {
            $Iitem = $CI->invoice_items_model->get_item($itId);
            $Iprice = $Iitem->rate;
        } else {
            $Iitem = $CI->invoice_items_model->get_group($itId);
            $Iprice = $Iitem->group_price;
        }
        if (isset($Iitem->is_taxable) && $Iitem->is_taxable == 1 && $tax_rate > 0) {
            $Itax_val = ($Iprice * $tax_rate) / 100;
            $Isubtotal = $Iprice + $Itax_val;
        } else {
            $Isubtotal = $Iprice;
        }
        $tax += $Itax_val;
        $tiprice += $Iprice;
        $tIsubtotal += $Isubtotal;
    }*/
    if (isset($proposal->feedback) && !empty($proposal->feedback)) {
        $totalSI = count(json_decode($proposal->feedback->selected_items));
        $tiprice=0.00;
        $selectedItems = json_decode($proposal->feedback->selected_items);
        if (!empty($selectedItems)) {
            foreach ($selectedItems as $selectedItem) {
                $tax = $tax + $selectedItem->tax;
                $tiprice += $selectedItem->subtotal - $selectedItem->tax;
            }
        }
    } ?>
<table class="quote_footer">
    <tr width="100%"
        style="text-align: right;height: 40px;line-height: 40px;vertical-align: middle;color: #7c7c7c;font-weight: 500;background-color: #ebebeb;padding: 10px 0;border-bottom: 1px solid #ccc!important;">
        <th width="1%"></th>
        <!--<th width="5%"></th>-->
        <th align="left" width="<?php if ($proposal->markups == 0 && $proposal->discounts == 0) {
            echo "55%";
        } else {
            echo "45%";
        } ?> "><?php //echo $totalSI . " " . _l('selected') ?></th>
        <th width="10%"><?php //echo _l('qty') ?></th>
        <th width="10%"><?php echo format_money($tiprice); ?></th>
        <?php if ($proposal->markups == 1 || $proposal->discounts == 1) { ?>
            <th width="10%">
                <?php
                if($tImkpdisc < 0){
                    $tImkpdisc=str_replace('-','',$tImkpdisc);
                    echo "-".format_money($tImkpdisc);
                }else{
                    echo format_money($tImkpdisc);
                }

                if ($proposal->markups == 1 && $proposal->discounts == 0) {
                    //echo _l('markup');
                } elseif ($proposal->markups == 0 && $proposal->discounts == 1) {
                    //echo _l('discount');
                } else {
                    //echo _l('mkp_disc');
                } ?>
            </th>
        <?php } ?>
        <th width="10%"><?php echo format_money($tax) ?></th>
        <th width="13%"><?php
            if (isset($proposal->feedback) && $proposal->feedback->proposal_subtotal > 0) {
                echo format_money($proposal->feedback->proposal_subtotal);
            } else {
                echo format_money($tIsubtotal);
            } ?>
        </th>
        <th width="1%"></th>
    </tr>
</table>
<?php } else{ ?>
    <table width="100%" align="center">
        <tr><td><h4><?php echo _l('no_item_selected')?></h4></td></tr>
    </table>
    <?php  } ?>
<p></p>
<hr><p></p>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td width="60%" align="left"><b>Notes</b><br/><?php echo _l('proposal_notes_text') ?>
            <br/><br/><b><?php echo _l('terms_condition') ?></b><br/><?php echo _l('proposal_terms_condition') ?></td><td width="40%"><table width="100%">
                <tr><th width="49.5%" style="font-weight: bold; text-transform: capitalize"><?php echo _l('subtotal') ?></th>
                    <td width="49.5%" align="right">
                        <?php if (isset($proposal->feedback->proposal_subtotal)) {
                            echo format_money($proposal->feedback->proposal_subtotal);
                        } else {
                            echo format_money($tIsubtotal);
                        } ?>
                    </td>
                    <td width="1%"></td>
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
                        <th style="font-size:12px;font-weight: bold; text-transform: capitalize"><?php echo "Other discount (";
                            echo "<span class='discpercent'>" . $othrdiscPercent . "</span>";
                            echo "%";
                            echo ")"; ?></th>
                        <td align="right" style="color: red"><?php
                            $othrdiscval = str_replace('-', "", $proposal->othrdiscval);
                            if ($proposal->othrdisctype == "percentage") {
                                $othrdiscval = ($proposal->othrdiscval * $tiprice) / 100;
                            }
                            $othrdiscval = str_replace('-', "", $othrdiscval);
                            echo "-" . format_money($othrdiscval);
                            $tIsubtotal = $tIsubtotal - $othrdiscval;
                            ?></td>
                        <td width="1%"></td>
                    </tr>
                    <hr>
                <?php } ?>
                <?php
                if (isset($proposal) && !empty($proposal->other) && $proposal->otherval > 0) {
                    $tIsubtotal = $tIsubtotal + $proposal->otherval;
                    ?>
                    <tr style="height: 50px; line-height: 50px">
                        <th style="font-weight: bold; text-transform: capitalize"><?php echo ucfirst($proposal->other); ?></th>
                        <td align="right"><?php echo format_money($proposal->otherval); ?></td>
                        <td width="1%"></td>
                    </tr>
                    <hr>
                <?php } ?>
                <tr style="height: 50px; line-height: 50px">
                    <th style="font-weight: bold; text-transform: capitalize"><?php echo _l('total') ?></th>
                    <td align="right"><?php if (isset($proposal->feedback->proposal_total)) {
                            echo format_money($proposal->feedback->proposal_total);
                        } else {
                            echo format_money($tIsubtotal);
                        } ?>
                    </td>
                    <td width="1%"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php
$removed_sections = array();
if (isset($proposal)) {
    $signatures = json_decode($proposal->signatures, true);
    $removed_sections = json_decode($proposal->removed_sections, true);
}
$class = "";
$checked = "";
if (isset($removed_sections)) {
    $class = in_array('payments', $removed_sections) ? "removed_section" : "";
    $checked = in_array('payments', $removed_sections) ? "checked" : "";
}
?>
<br/><br/>
<table>
    <tr>
        <th width="49.5%" align="left"><?php echo strtoupper(_l('payment_schedule')); ?></th>
        <th width="49.5%" align="right"><?php echo isset($paymentschedule) ? count($paymentschedule->schedules) : 1 ?> Payments</th>
        <th width="1%"></th>
    </tr>
    <tr>
        <td></td>
    </tr>
</table>

<table align="center" width="100%" id="payments" class="<?php echo $class ?>">
    <?php if (isset($proposal) && $proposal->ps_template > 0) {
        $pmt_sdl_template = $proposal->pmt_sdl_template; ?>
        <?php $value = (isset($paymentschedule) ? $paymentschedule->name : ''); ?>
        <tr align="right" height="30" bgcolor="<?php echo get_option('pdf_table_heading_color'); ?>"
            style="height:30px; line-height: 30px ;color:<?php echo get_option('pdf_table_heading_text_color') ?>;">
            <th align="center" width="16.5%">PMT</th>
            <th width="16.5%">Description</th>
            <th width="16.5%">Due Date</th>
            <th width="16.5%">Status</th>
            <th width="16.5%">Pmt. Method</th>
            <th width="16.5%">Amount</th>
            <th width="1%"></th>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td width="1%"></td>
        </tr>
        <?php if (!empty($paymentschedule->schedules)) {
            $schedules = $paymentschedule->schedules;
            $pe = 0; ?>
            <?php foreach ($schedules as $pk => $pv) {
                $payment_data = array('pe' => $pe, 'pk' => $pk, 'pv' => $pv);
                ?>
                <?php include "payment.php"; ?>

                <?php $pe++;
            } ?>
        <?php } else { ?>
            <?php include "payment.php"; ?>
        <?php } ?>
        <tr>
            <td></td>
        </tr>
        <?php
        if (isset($proposal) && $proposal->ps_template > 0) { ?>
        <?php } ?>
    <?php } else {
        if (isset($rec_payment) && !empty($rec_payment)) {
            include "rec_payment_temp.php";
        }
    } ?>
</table><br pagebreak="true"/>