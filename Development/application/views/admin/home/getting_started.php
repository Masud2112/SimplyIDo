<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 11-04-2018
 * Time: 04:08 PM
 */
?>
<div class=" to_do_list">
    <div class="panel-body" id="unique_getting_started_widget">
        <div class="row">
            <div class="mbot10 posrel">
                <h4 class="no-margin pull-left">Getting Started</h4>
                <a href="#" data-toggle="modal" data-target="#getting_start_setting"
                   class="toggle_set_button"><i class="fa fa-cog menu-icon"></i></a>
                <a href="javascript: void(0)" class="toggle_control_cutton"
                   id="getting_started_collapse" data-pid="#unique_getting_started_widget">
                    <i class="fa fa-caret-up"></i></a>
            </div>
            <div class="panel_s col-md-12 widget-body clearfix" id="getting_started_data">
                <div class="col-md-12">
                    <?php if (has_permission('proposals', '', 'create')) { ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" id="calender_list"
                                       name="to_do_list" <?php if ($proposal_status > 0) {
                                    echo "checked";
                                } ?>>
                                <label for="tasks"><a
                                            href="<?php echo admin_url('proposaltemplates/proposal'); ?>">Create
                                        your first proposal.</a></label>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if (has_permission('addressbook', '', 'create')) { ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" id="calender_list"
                                       name="to_do_list" <?php if ($contact_status > 0) {
                                    echo "checked";
                                } ?> >
                                <label for="tasks"><a
                                            href="<?php echo admin_url('addressbooks/addressbook'); ?>">Create
                                        your first contact.</a></label>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if (has_permission('leads', '', 'create', true)) { ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" id="calender_list"
                                       name="to_do_list" <?php if ($lead_status > 0) {
                                    echo "checked";
                                } ?>>
                                <label for="tasks"><a
                                            href="<?php echo admin_url('leads/lead'); ?>">Add
                                        your first lead.</a></label>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="col-md-12">
                    <?php if (has_permission('invoices', '', 'create', true)) { ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" id="calender_list"
                                       name="to_do_list" <?php if ($invoice_status > 0) {
                                    echo "checked";
                                } ?>>
                                <label for="tasks"><a
                                            href="<?php echo admin_url('invoices/invoice'); ?>">Creat your first
                                        Invoice.</a></label>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($this->session->userdata['user_type'] == 1) { ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" id="calender_list"
                                       name="to_do_list" <?php if (isset($package_type->name)) {
                                    echo "checked";
                                } ?>>
                                <label for="tasks"><a
                                            href="<?php echo admin_url('brand_settings'); ?>">Add
                                        your dashboard image or graphic.</a></label>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="checkbox">
                                <input type="checkbox" id="calender_list"
                                       name="to_do_list" <?php if ($theme_status > 0) {
                                    echo "checked";
                                } ?>>
                                <label for="tasks"><a
                                            href="<?php echo admin_url('brand_settings?group=clients'); ?>">Choose
                                        your dashboard theme.</a></label>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <div class="col-md-12">
                    <?php if ($this->session->userdata['user_type'] == 1) { ?>
                        <div class="col-md-4 col-sm-6">
                            <?php
                            foreach ($banking_status as $option) {
                                if ($option['name'] == 'invoice_prefix') {
                                    $bank_data['invoice_prefix'] = $option['value'];
                                } elseif ($option['name'] == 'invoice_number_format') {
                                    $bank_data['invoice_number_format'] = $option['value'];
                                } elseif ($option['name'] == 'predefined_clientnote_invoice') {
                                    $bank_data['predefined_clientnote_invoice'] = $option['value'];
                                } elseif ($option['name'] == 'predefined_terms_invoice') {
                                    $bank_data['predefined_terms_invoice'] = $option['value'];
                                }
                            }
                            ?>
                            <div class="checkbox">
                                <input type="checkbox" id="calender_list"
                                       name="to_do_list" <?php if (!empty($bank_data['invoice_prefix']) && !empty($bank_data['invoice_number_format']) && !empty($bank_data['predefined_clientnote_invoice']) && !empty($bank_data['predefined_terms_invoice'])) {
                                    echo "checked";
                                } ?> >
                                <label for="tasks"><a
                                            href="<?php echo admin_url('brand_settings?group=sales'); ?>">Setup
                                        your banking information.</a></label>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <?php
                            foreach ($company_info_status as $company_option) {
                                if ($company_option['name'] == 'companyname') {
                                    $company_data['companyname'] = $company_option['value'];
                                } elseif ($company_option['name'] == 'invoice_company_address') {
                                    $company_data['invoice_company_address'] = $company_option['value'];
                                } elseif ($company_option['name'] == 'invoice_company_city') {
                                    $company_data['invoice_company_city'] = $company_option['value'];
                                } elseif ($company_option['name'] == 'company_state') {
                                    $company_data['company_state'] = $company_option['value'];
                                } elseif ($company_option['name'] == 'invoice_company_postal_code') {
                                    $company_data['invoice_company_postal_code'] = $company_option['value'];
                                } elseif ($company_option['name'] == 'customer_info_format') {
                                    $company_data['customer_info_format'] = $company_option['value'];
                                }
                            }
                            ?>
                            <div class="checkbox">
                                <input type="checkbox" id="calender_list"
                                       name="to_do_list" <?php if (!empty($company_data['companyname']) && isset($company_data['invoice_company_address']) && !empty($company_data['invoice_company_city']) && !empty($company_data['company_state']) && !empty($company_data['invoice_company_postal_code']) && !empty($company_data['customer_info_format'])) {
                                    echo "checked";
                                } ?>>
                                <label for="tasks"><a
                                            href="<?php echo admin_url('brand_settings?group=company'); ?>">Update
                                        your company information.</a></label>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="checkbox">
                                 <input type="checkbox" id="calender_list" name="to_do_list">
                                <label for="tasks"><a
                                            href="<?php echo admin_url('brand_settings?group=general'); ?>">Review
                                        all of your account settings.</a></label>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="getting_start_setting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span>Getting Started Setting</span>
                </h4>
            </div>
            <div class="modal-body">
                <?php if (has_permission('items', '', 'create')) { ?>
                    <div class="row">
                        <form action="<?php echo admin_url()?>home/dashboard_widget_setting" novalidate="1" id="getting_start_setting_form" method="post"
                              accept-charset="utf-8">
                            <div id="additional"></div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="checkbox radio-primary radio-inline">
                                        <input type="checkbox" id="dashboard_getting_start" name="widget_visibility" class="checkbox task" value="1">
                                        <label for="dashboard_getting_start">Hide</label>
                                    </div>
                                </div>
                            </div>
                    </div>

                <?php } ?>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="widget" value="getting_started">
                <input type="hidden" name="user" value="<?php echo get_staff_user_id();?>">
                <button type="submit" class="btn btn-info" id="save_setting"><?php echo _l('Save'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>