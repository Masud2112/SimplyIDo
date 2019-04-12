<div class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="panel_s">
                <div class="payment-grid">
                    <div class="hrow">
                        <span class="hTxt">
                            <?php echo isset($paymentschedule) ? count($paymentschedule->schedules) : 1 ?>
                            <?php echo _l('Payments'); ?>
                        </span>
                        <i class="fa fa-caret-up"></i>
                    </div>
                    <div class="row rowWrap">
                        <div class="payment-header">
                            <div class="payment-head row">
                                <div class="col-xs-1 numIcon">PMT</div>
                                <div class="col-xs-3">Name</div>
                                <div class="col-xs-2">Due Date</div>
                                <div class="col-xs-2">Status</div>
                                <div class="col-xs-2">Pmt.Method</div>
                                <div class="col-xs-1">Amount</div>
                                <div class="col-xs-1 removeCol">Remove</div>
                            </div>
                        </div>
                        <div class="payment-body sortable">
                            <?php if (!empty($paymentschedule->schedules)) {
                                $schedules = $paymentschedule->schedules;
                                foreach ($schedules as $key => $schedule) {
                                    if ($schedule['duedate_type'] == "upon_signing") {
                                        $schedules['temp'] = $schedules[0];
                                        $schedules[0] = $schedules[$key];
                                        $schedules[$key] = $schedules['temp'];
                                        unset($schedules['temp']);
                                    }

                                }
                                $pe = 0; ?>

                                <?php foreach ($paymentschedule->schedules as $pk => $pv) {

                                    $payment_data = array('pe' => $pe, 'pk' => $pk, 'pv' => $pv);
                                    ?>
                                    <?php $this->load->view('admin/proposaltemplates/payment', $payment_data); ?>

                                    <?php $pe++;
                                } ?>
                            <?php } else { ?>
                                <?php $this->load->view('admin/proposaltemplates/payment'); ?>
                            <?php } ?>
                        </div>
                        <div class="payment-footer">
                            <?php

                            if (isset($paymentschedule) && $paymentschedule->is_template == 1) {
                                $is_ps_template = $paymentschedule->is_template;
                                $checked = 'checked';
                            } else {
                                $checked = '';
                                $is_ps_template = 0;
                            }
                            ?>
                            <div class="mright20 mtop8 is_ps_template checkbox pull-left ">
                                <input id="is_ps_template" class="checkbox" name="is_ps_template"
                                       value="<?php echo $is_ps_template; ?>" type="checkbox" <?php echo $checked; ?> >
                                <label class="control-label" for="is_ps_template">Save as template</label>
                            </div>
                            <?php $pname = (isset($paymentschedule) ? $paymentschedule->name : ''); ?>
                            <div class="form-group pmt-sdl-name pull-left hide ">
                                <label class="control-label" for="pmt_sdl_name">
                                    <small class="req text-danger">*</small>
                                    <?php echo _l('Name'); ?>
                                </label>
                                <input type="text" name="pschedulename" id="pmt_sdl_name" class="" value="<?php echo $pname!=""?$pname:"Paymentschdule" ; ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if (isset($proposal) && $proposal->ps_template > 0) { ?>
            <input id="ps_template" type="hidden" name="ps_template" value="<?php echo $proposal->ps_template ?>">
        <?php } ?>
    </div>
</div>
<script>
    duedate_types = <?php echo json_encode($duedate_types); ?>;
    duedate_criteria = <?php echo json_encode($duedate_criteria); ?>;
    amount_types = <?php echo json_encode($amount_types); ?>;
    duedate_duration = <?php echo json_encode($duedate_duration); ?>;
</script>