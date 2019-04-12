<?php
/**/
/**/
?>
<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="payment-grid">
                    <div class="hrow">
                        <span class="hTxt"><?php echo isset($paymentschedule) ? count($paymentschedule->schedules) : 1 ?>
                            Payments</span>
                        <i class="fa fa-caret-up"></i>
                    </div>

                    <div class="row rowWrap">
                        <?php $value = (isset($paymentschedule) ? $paymentschedule->name : ''); ?>
                        <div class="payment-header">
                            <div class="payment-head row">
                                <div class="col-xs-1">PMT</div>
                                <div class="col-xs-3">Description</div>
                                <div class="col-xs-2">Due Date</div>
                                <div class="col-xs-1">Status</div>
                                <div class="col-xs-2">Pmt.Method</div>
                                <div class="col-xs-2">Amount</div>
                            </div>
                        </div>
                        <div class="payment-body">
                            <?php if (!empty($paymentschedule->schedules)) {
                                $schedules = $paymentschedule->schedules;
                                //$schedules = array_reverse($schedules);
                                /*foreach ($schedules as $key => $schedule) {
                                    if ($schedule['duedate_type'] == "upon_signing") {
                                        $schedules['temp'] = $schedules[0];
                                        $schedules[0] = $schedules[$key];
                                        $schedules[$key] = $schedules['temp'];
                                        unset($schedules['temp']);
                                    }

                                }*/
                                $pe = 0; ?>

                                <?php foreach ($schedules as $pk => $pv) {

                                    $payment_data = array('pe' => $pe, 'pk' => $pk, 'pv' => $pv);
                                    ?>
                                    <?php $this->load->view('admin/proposaltemplates/viewproposal/payment', $payment_data); ?>

                                    <?php $pe++;
                                } ?>
                            <?php } else { ?>
                                <?php $this->load->view('admin/proposaltemplates/viewproposal/payment'); ?>
                            <?php } ?>
                        </div>
                        <div class="payment-footer">
                            <?php
                            $checked = '';
                            if ($paymentschedule->is_template == 0) {
                                $checked = 'checked';
                            }
                            ?>
                            <?php if ((isset($proposal->ps_template) && $proposal->ps_template == 0) || !isset($proposal->ps_template)) { ?>
                                <!--<div class="col-md-12 ">
                                    <div class="add-payment-btn pull-right">
                                        <a href="javascript:void(0)" class="btn btn-primary" id="add_more_payment">Add Payment</a>
                                    </div>
                                </div>-->
                            <?php } ?>
                            <!-- <div class="col-md-7 text-right">
                                  <div class="total-wrapper">
                                      <span class="mright20">Total</span>
                                      $<span class="total">0</span>
                                  </div>

                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if (isset($proposal) && $proposal->ps_template > 0) { ?>
            <input type="hidden" name="ps_template" value="<?php echo $proposal->ps_template ?>">
        <?php } ?>
        <?php //echo form_close(); ?>
    </div>
</div>
<script>
    duedate_types = <?php echo json_encode($duedate_types); ?>;
    duedate_criteria = <?php echo json_encode($duedate_criteria); ?>;
    amount_types = <?php echo json_encode($amount_types); ?>;
    duedate_duration = <?php echo json_encode($duedate_duration); ?>;
</script>