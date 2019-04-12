<?php
$options = array();
$tax_rate = 0.00;
$tax_val = 0.00;
$cextra = 0.00;
if (isset($proposal) && $proposal->proposal_custom_tax > 0) {
    $ptax = get_tax_rate_by_id($proposal->proposal_custom_tax);
    $tax_rate = $ptax->taxrate;
}

$lable = "";
if (strtolower($item_type) == "package") {
    $id = $item->id;
    $image = group_image($id, array('item-profile-image-product_services_package_image'), 'thumb', array('width' => '60px'));
    $name = $item->name;
    $sku = $item->group_sku;
    $price = $item->group_price;
    $description = $item->group_description;
    $lable = "Package";
} else {
    $id = $item->id;
    $image = line_item_image($id, array('profile_image', 'img-responsive', 'item-profile-image-thumb'), 'thumb', array('width' => '60px'));
    $name = $item->description;
    $sku = $item->sku;
    $price = $item->rate;
    $description = $item->long_description;
}
$originlprice = $price;
$qty = (isset($qty) && $qty > 0 ? $qty : 1);

if ($allow_client == 1 && $qty > $maxqty) {
    $qty = $maxqty;
}
$mdiscoun = (!empty($mdiscoun) ? $mdiscoun : 0);
$omdiscoun = $mdiscoun;
$omdiscoun_val = $mdiscoun;
if (isset($mdiscoun_calc) && $mdiscoun_calc == "percentage") {
    $mdiscoun = ($price * $mdiscoun) / 100;
}


$hiddensubtotal = ($price + $mdiscoun);
$subtotal = ($price + $mdiscoun) * $qty;
$class = "";
$colclass = "";
if ($omdiscoun < 0) {
    $class = "danger";
}
if ($mdiscoun_calc == "percentage") {
    $omdiscoun_prefix = "%";
} else {
    $omdiscoun_prefix = "$";
}
if ($mdiscoun_type == "discount" && $proposal->discounts == 0) {
    $class .= " hide";
    $price = $hiddensubtotal;
} elseif ($mdiscoun_type == "markup" && $proposal->markups == 0) {
    $class .= " hide";
    $price = $hiddensubtotal;
}
if (isset($item->is_taxable) && $item->is_taxable == 1 && $tax_rate > 0) {
    $tax_val = ($subtotal * $tax_rate) / 100;
    $subtotal = $subtotal + $tax_val;
}
$disabled = "";
if ($proposal->status == "accepted" || $proposal->status == "decline" || (isset($proposal->feedback) && $proposal->feedback->total_signed > 0)) {
    //$disabled="disabled";
}

$subtotal = number_format($subtotal, 2, ".", "");
$price = number_format($price, 2, ".", "");

if (isset($proposal_selected_items) && !empty($proposal_selected_items)) {
    foreach ($proposal_selected_items as $p_s_item) {
        if ($id == $p_s_item['id'] && strtolower($item_type) == $p_s_item['type']) {
            $selected = "selected";
            $qty = $p_s_item['qty'];
            $options = isset($p_s_item['options']) ? $p_s_item['options'] : array();
            $subtotal = $p_s_item['subtotal'];
        }
    }
}
$checked = "";
if (isset($selected)) {
    $checked = "checked";
}

if(!isset($proposal->feedback) && $gtype == 0){
    $tImkpdisc+=($mdiscoun * $qty);
    $tax += $tax_val;
    $tiprice += ($price * $qty);
    $tIsubtotal += $subtotal;
}

?>
<tr style="text-align: right">
    <td width="1%"></td>
    <!--<td width="5%">
    </td>-->
    <td width="<?php if ($proposal->markups == 0 && $proposal->discounts == 0) {
        echo "55%";
    } else {
        echo "45%";
    } ?> ">
        <table width="100%" align="left" style="text-align: left" cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td width="80px"
                    style="text-align: left"><?php echo $image; ?><?php if (isset($lable) && $lable != "") { ?>
                        <span><?php echo $lable; ?></span> <?php } ?></td>
                <td style="text-align: left"><b
                            style="font-size: 14px;color: #00a9b9; text-transform: uppercase;"><?php echo $name; ?></b><br/><?php echo $sku ?>
                </td>
            </tr>
        </table>
    </td>
    <td width="10%"><?php echo $qty != "" ? $qty : 1; ?></td>
    <td width="10%"><?php echo format_money(($price + $cextra)); ?></td>
    <?php if ($proposal->markups == 1 || $proposal->discounts == 1) { ?>
        <td width="10%">
            <?php
            if ($mdiscoun_calc == "amount") {
                if ($omdiscoun < 0) {
                    $omdiscoun = str_replace('-', '', $omdiscoun);
                    echo "-".format_money($omdiscoun);
                }else{
                    echo format_money($omdiscoun);
                }
            } else {
                echo $omdiscoun . $omdiscoun_prefix;
            } ?>
        </td>
    <?php } ?>
    <td width="10%">
        <?php
        if (isset($item->is_taxable) && $item->is_taxable == 1) {
            echo $tax_rate . "%";
        } else {
            echo "---";
        }
        ?>
    </td>
    <td width="13%"><?php echo format_money($subtotal); ?></td>
    <td width="1%"></td>
</tr>
<tr>
    <td width="1%"></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td width="1%"></td>
</tr>