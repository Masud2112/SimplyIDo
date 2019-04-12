<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 26-03-2018
 * Time: 18:44
 */
?>
<div class="modal fade" id="recurring_payment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    Recurring Payment
                </h4>
            </div>
            <div class="modal-body">
                <?php if(has_permission('items','','create')){ ?>
                    <div class="row">
                        <div class="recurring_payment_form">
                            <div class="recurring_payment_form_inner">
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label class="control-label">Name:<small class="req text-danger">*</small></label>
                                        <input type="text" class="recurring_name form-control" required="required" value="<?php echo isset($rec_name)?$rec_name: ''?>"/>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="control-label">Billed:<small class="req text-danger">*</small></label>
                                        <select class="form-control selectpicker recurring_bill_type" required >
                                            <option value="">Select Option</option>
                                            <option value="weekly" <?php echo isset($rec_bill_type)&& $rec_bill_type=="weekly"?"selected":""?>>Weekly</option>
                                            <option value="monthly" <?php echo isset($rec_bill_type)&& $rec_bill_type=="monthly"?"selected":""?>>Monthly</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group col-sm-6">
                                        <label class="control-label">Number Of Week/Month:<small class="req text-danger">*</small></label>
                                        <input type="number" min="1" class="no_of_week_mnth form-control" required value="<?php echo isset($rec_no_of_week_mnth)?$rec_no_of_week_mnth: ''?>"/>
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="control-label">Start Date:<small class="req text-danger">*</small></label>
                                        <input type="text" min="1" class="recurring_start_date datepicker form-control" required value="<?php echo isset($rec_start_date)?date('m/d/Y',strtotime($rec_start_date)): ''?>"/>
                                    </div>
                                </div>
                                <div class="row stdate_no_rec">
                                    <div class="form-group col-sm-6">
                                        <label class="control-label">End Date:</label>
                                        <input type="text" min="1" class="recurring_end_date datepicker form-control"  value="<?php echo isset($rec_end_date)?date('m/d/Y',strtotime($rec_end_date)): ''?>"/> OR
                                    </div>
                                    <div class="form-group col-sm-6">
                                        <label class="control-label">Number Of Recurrences:</label>
                                        <input type="number" min="1" class="no_of_recurrence form-control" value="<?php echo isset($rec_no)?$rec_no: ''?>"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info" id="add_recurrence"><?php echo _l('submit'); ?></button>
            </div>
        </div>
    </div>
</div>
