<?php
/**/
$items = array_merge($packages, $items);
if (isset($quote) && count($quote) > 0) {
    $gtype = $quote['quote_type'];
    $gname = $quote['quote_name'];
    $qid = $quote['qid'];
    $gid = $quote['gid'];
    $quote_items = json_decode($quote['quote_items'], true);
    $quote_order = $quote['quote_order'];
    if (!empty($quote_items)) {
        $quote_items = array_values($quote_items);
    }
}
if ($gtype == 1) {
    $type = "(Choose <b><i><u>ONE</u></i></b> Only)";
    $msg = _l('choose_one');
} elseif ($gtype == 2) {
    $type = "(Choose <b><i><u>ANY</u></i></b>)";
    $msg = _l('choose_any');
} else {
    $type = "";
    $msg = _l('no_choice');
}
$class = isset($quote_items) && count($quote_items) > 0 ? "" : "hidden";
?>
<table width="100%">
    <tr align="left" style="background-color: #ccc">
        <th align="left" style="height: 50px">
            <span style="line-height: 50px;font-size: 27px;display: block;"><?php echo $gname ?></span>
            <?php if ($type != "") {
                echo "<br/>" . $type;
            } ?>
        </th>
    </tr>
    <tr align="left" style="background-color: #424242; color: #fff; font-style: italic;">
        <td align="left" style="font-size: 16px;padding: 10px; height: 30px;line-height: 30px">
            <?php echo $msg; ?>
        </td>
    </tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr width="100%"
        style="text-align: right;height: 40px;line-height: 40px;vertical-align: middle;color: #7c7c7c;font-weight: 500;background-color: #ebebeb;padding: 10px 0;border-bottom: 1px solid #ccc!important;">
        <th width="1%"></th>
        <th align="left" width="<?php if ($proposal->markups == 0 && $proposal->discounts == 0) {
            echo "55%";
        } else {
            echo "45%";
        } ?> "><?php echo _l('name') ?></th>
        <th width="10%"><?php echo ucfirst(_l('qty')) ?></th>
        <th width="10%"><?php echo _l('price') ?></th>
        <?php if ($proposal->markups == 1 || $proposal->discounts == 1) { ?>
            <th width="10%">
                <?php if ($proposal->markups == 1 && $proposal->discounts == 0) {
                    echo _l('markup');
                } elseif ($proposal->markups == 0 && $proposal->discounts == 1) {
                    echo _l('discount');
                } else {
                    echo _l('mkp_disc');
                } ?>
            </th>
        <?php } ?>
        <th width="10%"><?php echo _l('tax') ?></th>
        <th width="13%"><?php echo _l('subtotal'); ?></th>
        <th width="1%"></th>
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
    <?php
    if (isset($quote_items) && count($quote_items) > 0) {
        foreach ($quote_items as $key => $quote_item) {
            if (strtolower($quote_item['type']) == 'package') {
                $item = $CI->invoice_items_model->get_group($quote_item['id']);
            } else {
                $item = $CI->invoice_items_model->get_item($quote_item['id']);
            }
            if ($gtype == 0) {
                $quote_item['maxqty'] = $quote_item['qty'];
            }
            $item_type = $quote_item['type'];
            $quoteindex = $gid;
            $qitems = $key;
            $qty = $quote_item['qty'];
            $mdiscoun = $quote_item['mdiscoun'];
            $mdiscoun_type = isset($quote_item['mdiscoun_type']) ? $quote_item['mdiscoun_type'] : "discount";
            $mdiscoun_calc = isset($quote_item['mdiscoun_calc']) ? $quote_item['mdiscoun_calc'] : "amount";
            $gtype = $gtype;
            $gname = $gname;
            $maxqty = isset($quote_item['maxqty']) ? $quote_item['maxqty'] : 1;
            $allow_client = isset($quote_item['allow_client']) ? $quote_item['allow_client'] : 0;
            include "quote_item.php";
        }
    }
    ?>
</table>