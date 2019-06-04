<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 06-03-2018
 * Time: 18:03
 */
/**/
$rec_start_date = date_create($rec_start_date);
$rec_start_date = date_format($rec_start_date, 'm/d/Y');

$rec_end_date = date_create($rec_end_date);
$rec_end_date = date_format($rec_end_date, 'm/d/Y');
?>
<div class="payment-grid">
    <div class="hrow" style="overflow: hidden">
        <span class="hTxt">Recurring Payment</span>
        <i class="fa fa-caret-up"></i>
        <!--<a class="btn btn-info recurring_payment pull-right" data-toggle="modal" data-target="#recurring_payment"><span>Edit</span></a>-->

    </div>
    <div class="row rowWrap">
        <div class="clearfix"></div>
        <div class="payment-header">
            <div class="payment-head row">
                <div class="col-xs-1">PMT</div>
                <div class="col-xs-3">Description</div>
                <div class="col-xs-2">Start Date</div>
                <div class="col-xs-2">End Date</div>
                <div class="col-xs-1">Bill Type</div>
                <div class="col-xs-2">Every Week/Month</div>
                <div class="col-xs-1">Recurrences</div>
            </div>
        </div>
        <div class='payment-body'>
            <div class='payment-wrapper' id='paymentwrapper'>
                <input type="hidden" class="rec_id" name="rec_payment[rec_id]" value="<?php echo isset($rec_id)?$rec_id:"" ?>"/>
                <input type="hidden" name="rec_payment[rec_name]" value="<?php echo $rec_name ?>" />
                <input type="hidden" name="rec_payment[rec_bill_type]" value="<?php echo $rec_bill_type ?>"/>
                <input type="hidden" name="rec_payment[rec_no_of_week_mnth]"
                       value="<?php echo $rec_no_of_week_mnth ?>"/>
                <input type="hidden" name="rec_payment[rec_start_date]" value="<?php echo $rec_start_date ?>"/>
                <input type="hidden" name="rec_payment[rec_end_date]" value="<?php echo $rec_end_date ?>"/>
                <input type="hidden" name="rec_payment[rec_no]" value="<?php echo $rec_no ?>"/>
                <div class='payment-wrapper-ul row'>
                    <div class="col-xs-1"><?php echo 1 ?></div>
                    <div class="col-xs-3"><?php echo $rec_name ?></div>
                    <div class="col-xs-2"><?php echo date('m/d/Y',strtotime($rec_start_date)) ?></div>
                    <div class="col-xs-2"><?php echo date('m/d/Y',strtotime($rec_end_date))?></div>
                    <div class="col-xs-1"><?php echo $rec_bill_type ?></div>
                    <div class="col-xs-2"><?php echo $rec_bill_type == "weekly" ? "Every " . $rec_no_of_week_mnth . " Week" : "Every " . $rec_no_of_week_mnth . " Month"; ?></div>
                    <div class="col-xs-1"><?php echo $rec_no; ?></div>
                </div>
                <p class="padding-10 text-center"><strong>Payment will start from <?php echo date('d M Y',strtotime($rec_start_date))?> For <?php echo $rec_bill_type == "weekly" ? "Every " . $rec_no_of_week_mnth . " Week" : "Every " . $rec_no_of_week_mnth . " Month"; ?><?php echo $rec_no_of_week_mnth > 1?'s':''?>.</strong></p>
            </div>
        </div>
    </div>
</div>
