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
if (isset($rel_content)) {
    $date1 = date_create($rel_content->eventstartdatetime);
    $date2 = date_create();
    $diff_days = date_diff($date1, $date2);
    $diff_days = $diff_days->format("%a") / 2;
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
    <tr align="right" style="border: 1px solid #ccc; height: 30px; line-height: 30px" >
        <td align="center" width="16.5%"><?php echo $pk + 1 ?></td>
        <td width="16.5%"><?php echo _l('payment', $pk + 1); ?></td>
        <td width="16.5%">
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
                    <?php echo "TBD" ?>
                <?php } else { ?>
                    <?php echo _d($duedate_date) ?>
                <?php } ?>
            <?php } else { ?>
                <?php echo isset($pv['duedate_type'])?strtoupper($duedate_types[$pv['duedate_type']]):""; ?>
            <?php } ?>
        </td>
        <td width="16.5%">
            <?php echo $pv['status'] == 0 ? _l('unpaid') : _l('paid'); ?>
        </td>
        <td width="16.5%"><?php echo strtoupper($pv['payment_method']); ?></td>
        <td width="16.5%">
            <?php echo !empty($pv['price_amount']) ? format_money($pv['price_amount']) : format_money(0); ?>
        </td>
        <td width="1%"></td>
    </tr>
<?php } ?>