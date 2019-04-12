<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 06-03-2018
 * Time: 16:04
 */

$removed_sections = array();
if (isset($proposal)) {
    $sections=json_decode($proposal->sections,true);
    $section=$sections['payment'];

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
<div id="payments" class="<?php echo $class ?> proposalPaymentSchedula_blk">
    <div class="row">
        <div class="files_header col-sm-12">
            <div class="row">
                <div class="col-sm-6">
                    <h4 id="payment_page_name"><i class="fa fa-calendar-o mright10"></i><b><span><?php echo isset($section)?$section['name']:"PAYMENT SCHEDULE"; ?></span></b></h4>
                    <input type="hidden" name="sections[payment][name]" class="payment_page_name" value="<?php echo isset($section)?$section['name']:"PAYMENT SCHEDULE"; ?>">
                </div>
                <div class="col-sm-6  col-right">
                    <?php
                    if (!isset($_GET['preview'])) {
                        ?>
                    <?php if (!isset($rec_payment) || empty($rec_payment)) { ?>
                        <div class="payment_form_top inline-block"><?php if (isset($paymentschedules) && count($paymentschedules) > 0) { ?>
                                <select id="payment_picker" class="payment_load selectpicker">
                                    <option value="">USE TEMPLATE</option>
                                    <?php foreach ($paymentschedules as $paymentschedule) { ?>
                                        <option value="<?php echo $paymentschedule['templateid'] ?>"><?php echo $paymentschedule['name'] ?></option>
                                    <?php } ?>
                                </select>
                            <?php } ?>
                            <a id="payment-add-more" class="btn btn-info"><i class="fa fa-plus-square"></i> ADD PAYMENT</a>
                        </div>
                        <?php } ?>
						<div class="show-options">
                            <a class='show_act' href='javascript:void(0)'>
                                <i class='fa fa-ellipsis-v' aria-hidden='true'></i>
                            </a>
                        </div>                      
                        <div class='table_actions'>
                            <ul>
                                <li>
                                    <a href='javascript:void(0)' class=""  id="edit_page" data-toggle="modal"
                                       data-target="#edit_payment_popup">
                                        <i class="fa fa-pencil-square-o"></i><span>Edit</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
						<div class="checkbox">
                            <input type="checkbox" class="checkbox remove_proposal_section" name="remove_sec[]" id="remove_payment"
                                   data-pid="#payments" value="payments" <?php echo $checked ?>/>
                            <label for="remove_payment"><?php echo "Remove"; ?></label>
                        </div>
                    <?php } ?>
                </div>
            </div>		 
        </div>
		<div class="clearfix"></div>
        <div class="section_body">
            <?php
            if (!isset($_GET['preview'])) {
            ?>
            <div class="payment_form">
                <?php if ((isset($proposal->ps_template) && $proposal->ps_template == 0) || !isset($proposal->ps_template)) { ?>
                    <?php if (!isset($rec_payment) || empty($rec_payment)) { ?>
                        <div class="payment_options text-center">
                            <p>Select one of the following options to create a payment schedule</p>
                            <?php if (isset($paymentschedules) && count($paymentschedules) > 0) { ?>
                                <select id="payment_picker" class="payment_load selectpicker">
                                    <option value="">USE TEMPLATE</option>
                                    <?php foreach ($paymentschedules as $paymentschedule) { ?>
                                        <option value="<?php echo $paymentschedule['templateid'] ?>"><?php echo $paymentschedule['name'] ?></option>
                                    <?php } ?>
                                </select>
                            <?php } ?>
                            <!--<a class="btn btn-default"><i class="fa fa-plus-square"></i> EQUAL PAYMENTS</a>-->
                            <a class="btn btn-info recurring_payment" data-toggle="modal"
                               data-target="#recurring_payment"><i class="fa fa-plus-square"></i> RECURRING
                                PAYMENTS</a>
                            <a id="payment-add" class="btn btn-info"><i class="fa fa-plus-square"></i> ADD
                                PAYMENT</a>
                        </div>
                    <?php }
                } ?>
                <div class="payment_templates text-center mbot30 mtop30">
                    <?php if (isset($paymentschedules) && count($paymentschedules) > 0) { ?>
                        <select id="payment_picker" class="payment_load selectpicker">
                            <option value="">Select Agreement Template</option>
                            <?php foreach ($paymentschedules as $paymentschedule) { ?>
                                <option value="<?php echo $paymentschedule['templateid'] ?>"><?php echo $paymentschedule['name'] ?></option>
                            <?php } ?>
                        </select>
                    <?php } else { ?>
                        <p>Sorry! No template available</p>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
            <div id="paymentschedule" class="paymentschedule-page">
                <?php if (isset($proposal) && $proposal->ps_template > 0) {
                    $pmt_sdl_template = $proposal->pmt_sdl_template;
                    $this->load->view('admin/proposaltemplates/paymentschedule_temp', $pmt_sdl_template);
                } else {
                    if (isset($rec_payment) && !empty($rec_payment)) {
                        $this->load->view('admin/proposaltemplates/rec_payment_temp', $rec_payment);
                    }
                } ?>
            </div>
        </div>
        <div class="section-footer mobShow">
             <?php
                    if (!isset($_GET['preview'])) {
                        ?>
                    <?php if (!isset($rec_payment) || empty($rec_payment)) { ?>
                        <div class="payment_form_top  inline-block"><?php if (isset($paymentschedules) && count($paymentschedules) > 0) { ?>
                                <select id="payment_picker" class="payment_load selectpicker">
                                    <option value="">USE TEMPLATE</option>
                                    <?php foreach ($paymentschedules as $paymentschedule) { ?>
                                        <option value="<?php echo $paymentschedule['templateid'] ?>"><?php echo $paymentschedule['name'] ?></option>
                                    <?php } ?>
                                </select>
                            <?php } ?>
                            <a id="payment-add-more" class="btn btn-info"><i class="fa fa-plus-square"></i> ADD
                                PAYMENT</a>
                        </div>
                        <?php } ?>
                        <?php } ?>
        </div>
    </div>
</div>
<div class="modal fade" id="edit_payment_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('edit page'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="group_popup">
                        <div class="group_name">
                            <div class="form-group">
                                <label class="control-label">Page Name
                                    <small class="req text-danger">*</small>
                                </label>
                                <input type="text" name="page_name[quote]" class="form-control page_name" value="<?php echo isset($section)?strtoupper($section['name']):"PAYMENT SCHEDULE"; ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" class="btn btn-info page_save" data-pid="payment_page_name" data-id="#edit_payment_popup"><?php echo _l('submit'); ?></a>
            </div>
        </div>
    </div>
</div>