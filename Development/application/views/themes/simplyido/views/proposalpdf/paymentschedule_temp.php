<?php $value = (isset($paymentschedule) ? $paymentschedule->name : ''); ?>
<table width="100%" class="content" style="border: 1px solid #ccc">
    <tr>
        <td><b><?php echo isset($paymentschedule) ? count($paymentschedule->schedules) : 1 ?>Payments</b></td>
    </tr>
    <tr class="row">
        <td class="col-md-12">
            <table align="center" width="100%">
                <tr height="30" bgcolor="<?php echo get_option('pdf_table_heading_color'); ?>"
                    style="color:<?php echo get_option('pdf_table_heading_text_color') ?>;">
                    <th>PMT</th>
                    <th>Description</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Pmt.Method</th>
                    <th>Amount</th>
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
            </table>
        </td>
        <?php
        if (isset($proposal) && $proposal->ps_template > 0) { ?>
            <!--<input type="hidden" name="ps_template" value="<?php /*echo $proposal->ps_template */ ?>">-->
        <?php } ?>
        <?php //echo form_close(); ?>
    </tr>
</table>