<?php init_head(); ?>
<div id="wrapper">
    <div class="content line-item-page">
        <div class="row">
            <?php
            $package_id = 0;
            $proposal_id = 0;
            $action = $this->uri->uri_string();
            if (isset($_GET['package_id'])) {
                $action .= "?package_id=" . $_GET['package_id'];
                $package_id = $_GET['package_id'];
            } elseif (isset($_GET['pid']) && isset($_GET['qid'])) {
                $action .= "?pid=" . $_GET['pid'] . "&qid=" . $_GET['qid'];
                $proposal_id = $_GET['pid'];
            }
            echo form_open_multipart($action, array('class' => 'item-form', 'id' => 'manage-item-form', 'autocomplete' => 'off')); ?>
            <?php $item_main_id = (isset($item) ? $item->itemid : ''); ?>
            <input type="hidden" name="tagid" value="<?php echo $item_main_id; ?>">
            <div class="col-md-12">
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('setup'); ?>"><?php echo _l('breadcrum_setting_label'); ?></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('invoice_items'); ?>"><?php echo _l('breadcrum_product_service_label'); ?></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php if (isset($item)) { ?>
                        <span><?php echo $item->description; ?></span>
                    <?php } else { ?>
                        <span><?php echo "New Products & Services"; ?></span>
                    <?php } ?>
                </div>

                <h1 class="pageTitleH1"><i class="fa fa-money"></i><?php echo $title; ?> <?php if (isset($item)) { ?>
                        <?php echo form_hidden('itemid', $item->itemid); ?>
                    <?php } ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <h4 class="hide">
                            <?php echo $title; ?>

                        </h4>
                        <div class="clearfix"></div>

                        <?php if ($profile_allow == 1) { ?>

                            <?php if ((isset($item) && $item->profile_image == NULL) || !isset($item)) { ?>
                                <div class="col-md-6">
                                    <div class="profile-pic">
                                        <?php
                                        $src = "";
                                        $profileImagePath = FCPATH . 'uploads/staff_profile_images/' . $current_user->staffid . '/round_' . $current_user->profile_image;
                                        if (file_exists($profileImagePath)) {
                                            $src = base_url() . 'uploads/staff_profile_images/' . $current_user->staffid . '/round_' . $current_user->profile_image;
                                        } ?>
                                        <div class="profile_imageview <?php echo empty($src) ? 'hidden' : ''; ?>">
                                            <img src="<?php echo $src; ?>"/>
                                            <?php if ($src == "") { ?>
                                                <div class="actionToEdit">
                                                    <a class="clicktoaddimage" href="javascript:void(0)" onclick="croppedDelete('profile');">
                                                        <span><i class="fa fa-trash"></i></span>
                                                    </a>
                                                    <a class="recropIcon_blk" href="javascript:void(0)" onclick="reCropp('profile');">
                                                        <span><i class="fa fa-crop" aria-hidden="true"></i></span>
                                                    </a>
                                                </div>
                                            <?php } else { ?>
                                                <div class="actionToEdit">
                                                    <a class="_delete clicktoaddimage"
                                                    href="<?php echo admin_url('staff/remove_staff_profile_image'); ?>">
                                                        <span><i class="fa fa-trash"></i></span>
                                                    </a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="clicktoaddimage <?php echo !empty($src) ? 'hidden' : ''; ?>">
                                            <div class="drag_drop_image">
                                                <span class="icon"><i class="fa fa-image"></i></span>
                                                <span><?php echo _l('dd_upload'); ?></span>
                                            </div>
                                            <input id="profile_image" type="file" class="" name="profile_image"
                                                   onchange="readFile(this,'profile');"/ >
                                            <input type="hidden" id="imagebase64" name="imagebase64">
                                        </div>
                                        <div class="cropper" id="profile_croppie">
                                            <div class="copper_container">
                                                <div id="profile-cropper"></div>
                                                <div class="cropper-footer">
                                                    <button type="button" class="btn btn-info p9 actionDone"
                                                            type="button" id=""
                                                            onclick="croppedResullt('profile');">
                                                        <?php echo _l('save'); ?>
                                                    </button>
                                                    <button type="button" class="btn btn-default actionCancel"
                                                            data-dismiss="modal"
                                                            onclick="croppedCancel('profile');">
                                                        <?php echo _l('cancel'); ?>
                                                    </button>
                                                    <button type="button" class="btn btn-default actionChange"
                                                            onclick="croppedChange('profile');">
                                                        <?php echo _l('change'); ?>
                                                    </button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if (isset($item) && $item->profile_image != NULL) { ?>
                                <div class="col-md-6">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="profileImg_blk">
                                                <?php echo line_item_image($item->itemid, array('profile_image', 'img-responsive', 'item-profile-image-thumb')); ?>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-2 text-right">
                                        <a class="clicktoaddimage _delete" href="<?php echo admin_url('invoice_items/remove_line_item_image/' . $item->itemid); ?>"> 
                                            <span><i class="fa fa-trash"></i></span>
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <input type="hidden" name="profile_image" value="">
                        <?php } ?>
                        <div class="col-md-6">
                            <?php $name = (isset($item) ? $item->description : ''); ?>
                            <?php echo render_input('description', 'invoice_item_add_edit_description', $name, 'text'); ?>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $group = (isset($item) ? $item->group_id : ''); ?>
                                    <label for="line_item_category"><?php echo _l('product_category'); ?></label>
                                    <a href="#" id="new_line_item_category" class="pull-right mright5"
                                       data-toggle="modal" data-target="#lineitem_category_modal">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                    <select class="selectpicker form-control" name="line_item_category"
                                            id="line_item_category">
                                        <option value="">Select Category</option>
                                        <?php foreach ($product_service_groups as $group) { ?>
                                            <?php $line_item_category_old = (isset($item->line_item_category) ? $item->line_item_category : ''); ?>
                                            <option value=<?php echo $group['id']; ?>
                                                    <?php if ($line_item_category_old == $group['id']) { ?>selected <?php } ?> >
                                                <?php echo $group['name']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <?php $group = (isset($item) ? $item->group_id : ''); ?>
                                    <label for="catgory-subcategory"><?php echo _l('product_sub_category'); ?></label>
                                    <a href="#" id="new_line_item_sub_category" class="pull-right mright5"
                                       data-toggle="modal" data-target="#lineitem_sub_category_modal">
                                        <i class="fa fa-plus"></i>
                                    </a>
                                    <select class="selectpicker form-control" name="line_item_sub_category"
                                            id="line_item_sub_category">
                                        <option value="">Select Sub Category</option>
                                        <?php
                                        foreach ($product_sub_groups as $group) {
                                            $class = "";
                                            if (isset($item->line_item_category) && $item->line_item_category != $group['parent_id']) {
                                                $class = "hide";
                                            }
                                            ?>
                                            <?php $line_item_sub_category_old = (isset($item->line_item_sub_category) ? $item->line_item_sub_category : ''); ?>
                                            <option class="option <?php echo $group['parent_id'] . " " . $class; ?>"
                                                    value=<?php echo $group['id']; ?>
                                                    data-parent="<?php echo $group['parent_id']; ?>"
                                                    <?php if ($line_item_sub_category_old == $group['id']) { ?>selected <?php } ?> ><?php echo $group['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sku">SKU</label>
                                <?php $sku = (isset($item) ? $item->sku : ''); ?>
                                <input type="text" name="sku" id="sku" class="form-control"
                                       value="<?php echo $sku; ?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <?php $description = (isset($item) ? $item->long_description : ''); ?>
                            <div class="form-group">
                                <label for="long_description"
                                       class="control-label"> <?php echo _l('invoice_item_long_description'); ?> </label>
                                <textarea id="long_description" name="long_description"
                                          class="form-control long_description" rows="4"
                                          aria-hidden="true"><?php echo $description ?></textarea>
                            </div>
                        </div>
                        <?php
                        foreach ($currencies as $currency) {
                            if ($currency['isdefault'] == 0 && total_rows('tblclients', array('default_currency' => $currency['id'])) > 0) { ?>
                                <div class="form-group">
                                    <label for="rate_currency_<?php echo $currency['id']; ?>"
                                           class="control-label">
                                        <?php echo _l('invoice_item_add_edit_rate_currency', $currency['name']); ?></label>
                                    <input type="number" id="rate_currency_<?php echo $currency['id']; ?>"
                                           name="rate_currency_<?php echo $currency['id']; ?>"
                                           class="form-control" value="">
                                </div>
                            <?php }
                        }
                        ?>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="markup" class="control-label">Cost</label>
                                    <?php $cost_price_value = (isset($item) ? $item->cost_price : ''); ?>
                                    <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-usd"
                                                                                   aria-hidden="true"></i></span>
                                        <input type="number" id="cost_price" name="cost_price"
                                               class="form-control main"
                                               value="<?php echo $cost_price_value; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="rate" class="control-label">Price</label>
                                    <?php $rate = (isset($item) ? $item->rate : ''); ?>

                                    <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-usd"
                                                                                   aria-hidden="true"></i></span>
                                        <input type="number" id="rate" name="rate" class="form-control main"
                                               value="<?php echo $rate; ?>">
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="profit" class="control-label">Profit</label>
                                    <?php $profit = (isset($item) ? $item->profit : ''); ?>

                                    <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-usd"
                                                                                   aria-hidden="true"></i></span>
                                        <input type="number" id="profit" name="profit" class="form-control main"
                                               value="<?php echo $profit; ?>">
                                    </div>


                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="profit_margin" class="control-label">Profit Margin (%)</label>
                                    <?php $profit_margin = (isset($item) ? $item->profit_margin : ''); ?>

                                    <div class="input-group">
                                        <input type="number" id="profit_margin" name="profit_margin"
                                               class="form-control main" value="<?php echo $profit_margin; ?>">
                                        <span class="input-group-addon othrdisc_suffix"><i class="fa fa-percent"
                                                                                           aria-hidden="true"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sel1">Expense Category</label>
                                    <select class="selectpicker form-control" id="expense_category"
                                            name="expense_category">
                                        <option value="">Select</option>
                                        <?php foreach ($expense_category_list as $expense_category) {
                                            $expense_cat_value = (isset($item->expense_category) ? $item->expense_category : '');
                                            ?>
                                            <option value="<?php echo $expense_category['id']; ?>"
                                                    <?php if ($expense_cat_value == $expense_category['id']) { ?>selected <?php } ?>><?php echo $expense_category['name']; ?></option>
                                        <?php } ?>
                                        <option value="add_new_expense_cat">New Expense Category</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sel1">Income Category</label>
                                    <select class="selectpicker form-control" id="income_category"
                                            name="income_category">
                                        <option value="">Select</option>
                                        <?php foreach ($income_category_list as $income_category) {
                                            $income_cat_value = (isset($item->income_category) ? $item->income_category : '');
                                            ?>
                                            <option value="<?php echo $income_category['id']; ?>"
                                                    <?php if ($income_cat_value == $income_category['id']) { ?>selected <?php } ?>><?php echo $income_category['name']; ?></option>
                                        <?php } ?>
                                        <option value="add_new_income_cat">New Income Category</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-5 col-lg-4 taxable_section">
                                <div class="form-group">
                                    <label for="sel1">&nbsp;</label>
                                    <div class="checkbox">
                                        <?php $tax_status = (isset($item) ? $item->is_taxable : ''); ?>
                                        <input type="checkbox" class="tax_group" name="is_taxable" id="taxable"
                                               value="1" <?php if ($tax_status == "1"){ ?>checked<?php } ?>>
                                        <label for="leads">Taxable
                                            <small>(Taxes are applied when creating a proposal or invoice.)
                                            </small>
                                        </label>
                                    </div>
                                </div>
                            </div>


                            <div class="col-lg-2 custom_section">
                                <div class="form-group">
                                    <label for="sel1">&nbsp;</label>
                                    <div class="checkbox">
                                        <?php $custom_tax_status = (isset($item) ? $item->is_custom : ''); ?>
                                        <input type="checkbox" class="tax_group" name="is_custom" id="custom"
                                               value="1"
                                               <?php if ($custom_tax_status == "1"){ ?>checked<?php } ?>>
                                        <label for="leads">Custom </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-2">
                                <div class="form-group custom_tax">
                                    <label for="sel1">&nbsp;</label>
                                    <select class="form-control selectpicker" name="tax" id="taxes_list">
                                        <option value="">Select tax</option>
                                        <?php foreach ($taxes as $tax_list) { ?>

                                            <?php $tax_type_status = (isset($item->tax) ? $item->tax : ''); ?>
                                            <option value="<?php echo $tax_list['id']; ?>"
                                                    <?php if ($tax_type_status == $tax_list['id']) { ?>selected <?php } ?>><?php echo $tax_list['taxrate']; ?></option>
                                        <?php } ?>
                                        <option value="add_new_tax_modal">Add new tax</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="row">


                            <div class="col-md-12">
                                <div class="row">
                                    <!-- <div class="col-md-12"><label class="alert-info labelTaxes" for="sel1"></label></div> -->

                                </div>
                            </div>
                        </div>
                        <div class="clearfix mbot15"></div>
                        <div class="options-block newProductsnServices_blk">
                            <?php if (isset($item)) { ?>
                                <?php $this->load->view('admin/invoice_items/item-option', $item); ?>
                            <?php } else {
                                $this->load->view('admin/invoice_items/item-option');
                            } ?>
                        </div>
                        <div class="topButton">
                            <button class="btn btn-default" type="button"
                                    onclick="fncancel();"><?php echo _l('Cancel'); ?></button>
                            <button type="submit"
                                    class="btn btn-info product_service_submit"><?php echo _l('submit'); ?></button>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tax_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <span class="add-title"><?php echo _l('tax_add_title'); ?></span>
                    </h4>
                </div>
                <?php echo form_open('admin/taxes/manage', array('id' => 'tax_form')); ?>
                <?php echo form_hidden('taxid'); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="alert alert-warning hide tax_is_used_in_expenses_warning">
                                <?php echo _l('tax_is_used_in_expenses_warning'); ?>
                            </div>
                            <?php echo render_input('name', 'tax_add_edit_name'); ?>
                            <?php echo render_input('taxrate', 'tax_add_edit_rate', '', 'number'); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo _l('cancel'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="expense_category_modal" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <span class="add-title"><?php echo _l('add_expense_category_title'); ?></span>
                    </h4>
                </div>
                <?php echo form_open('admin/invoice_items/add_expense_category', array('id' => 'expense_category_form')); ?>
                <?php echo form_hidden('id'); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo render_input('name', _l('expense_category_title')); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo _l('cancel'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="income_category_modal" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <span class="add-title"><?php echo _l('add_income_category_title'); ?></span>
                    </h4>
                </div>
                <?php echo form_open('admin/invoice_items/add_income_category', array('id' => 'income_category_form')); ?>
                <?php echo form_hidden('id'); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo render_input('name', _l('income_category_title')); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo _l('cancel'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="lineitem_category_modal" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <span class="add-title"><?php echo _l('add_category_button_label'); ?></span>
                    </h4>
                </div>
                <?php echo form_open('', array('id' => 'category_form')); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php echo render_input('name', _l('add_category_button_label'), '', 'text', array(), array(), '', 'line_item_category_name'); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo _l('cancel'); ?></button>
                    <button id="lineitem_cat_save" type="submit"
                            class="btn btn-info "><?php echo _l('submit'); ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
    <div class="modal fade" id="lineitem_sub_category_modal" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <span class="add-title"><?php echo _l('add_sub_category_title'); ?></span>
                    </h4>
                </div>
                <?php echo form_open('admin/invoice_items/add_income_category', array('id' => 'sub_cat_form')); ?>
                <?php echo form_hidden('id'); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label><?php echo _l('line_item_category'); ?></label>
                                <small class="req text-danger">*</small>
                                <select id="parent_id" class="selectpicker form-control" name="parent_id"
                                        id="line_item_category">
                                    <option value="">Select Category</option>
                                    <?php foreach ($product_service_groups as $group) { ?>
                                        <?php $line_item_category_old = (isset($item->line_item_category) ? $item->line_item_category : ''); ?>
                                        <option value=<?php echo $group['id']; ?>
                                                <?php if ($line_item_category_old == $group['id']) { ?>selected <?php } ?> >
                                            <?php echo $group['name']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php echo render_input('name', _l('add_sub_category_title'), '', 'text', array(), array(), '', 'line_item_sub_category_name'); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo _l('cancel'); ?></button>
                    <button id="lineitem_sub_cat_save" type="submit"
                            class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>


</div>
<?php init_tail(); ?>
<script>
    $("#add_option_button").click(function (e) {
        var option_available = $('.add_pro_service_section').length;
        var option_before = "";
        var option_after = "";

        if (option_available <= 0) {
            option_before = "<div class='col-md-12'>";
            option_after = "</div>";

            var addtoEmail = "#item_option_id";
            var emailnext = 0;
        } else {
            e.preventDefault();
            var my_email_fields = $("div[id^='add_pro_service_section-']");
            var highestemail = -Infinity;
            $.each(my_email_fields, function (mindex, mvalue) {
                var fieldEmailNum = mvalue.id.split("-");
                highestemail = Math.max(highestemail, parseFloat(fieldEmailNum[1]));
            });
            var emailnext = highestemail;
            var addtoEmail = "#add_pro_service_section-" + emailnext;
            var addRemoveEmail = "#email-" + (emailnext);

            emailnext = emailnext + 1;
        }
        if ($('.item_options_list').length > 0) {
            option_before = "";
            option_after = "";
        }
        var optionnext = emailnext;
        var choicenext = 0;
        var choice_button_format = "<div class='col-md-12 text-center' id='add_choice_button'><a class='btn btn-primary'>Add choice to this dropdown</a></div>";
        var choice_field_format = '<div class="choices"><div class="form-group choice_fields" id="choice_fields-' + choicenext + '"><div class="panel_s"><div class="panel-body"><div class="choices"><h5 class="sub-title">Choices</h5><div class="choice_inner"><div class="row"><div class="col-md-12"><div class="form-group"><div class="checkbox"><input type="checkbox" name="choice[' + optionnext + '][' + choicenext + '][choice_default_selection]" id="default_selection" autocomplete="off" value="1"><label for="leads">Default Selection</label></div></div></div><div class="col-md-4"><div class="form-group"><label for="option_name" class="control-label">Name <small class="req text-danger">* </small></label><input type="text" id="option_name" name="choice[' + optionnext + '][' + choicenext + '][choice_name] " class="opt_choice_name form-control"></div></div><div class="col-md-2"><div class="form-group"><label for="choice_cost" class="control-label">Cost</label><div class="input-group"><span class="input-group-addon" ><i class="fa fa-usd" aria-hidden="true"></i></span><input type="number" id="choice_cost_price_' + choicenext + '" name="choice[' + optionnext + '][' + choicenext + '][choice_cost_price]" class="choice_cost_price_profit form-control" data-index=' + choicenext + ' data-pindex=' + optionnext + '></div></div></div><div class="col-md-2"><div class="form-group"><label for="choice_price" class="control-label">Price<small class="req text-danger">* </small></label><div class="input-group"><span class="input-group-addon" ><i class="fa fa-usd" aria-hidden="true"></i></span><input type="number" id="choice_rate_' + choicenext + '" name="choice[' + optionnext + '][' + choicenext + '][choice_rate]" class="choice_cost_price_profit form-control choice_price_new" data-index=' + choicenext + ' data-pindex=' + optionnext + '></div></div></div><div class="col-md-2"><div class="form-group"><label for="choice_profit" class="control-label">Profit</label><div class="input-group"><span class="input-group-addon" ><i class="fa fa-usd" aria-hidden="true"></i></span><input type="number" id="choice_profit_' + choicenext + '" name="choice[' + optionnext + '][' + choicenext + '][choice_profit]" class="choice_cost_price_profit form-control" data-index=' + choicenext + ' data-pindex=' + optionnext + '></div></div></div><div class="col-md-2"><div class="form-group"><label for="choice_profit_margin" class="control-label">Profit Margin(%)</label><div class="input-group"><input type="number" id="choice_profit_margin_' + choicenext + '" name="choice[' + optionnext + '][' + choicenext + '][choice_profit_margin]" class="choice_cost_price_profit form-control" data-index=' + choicenext + ' data-pindex=' + optionnext + '><span class="input-group-addon" ><i class="fa fa-percent" aria-hidden="true"></i></span></div></div></div></div></div></div></div></div></div></div>';

        var option_field_format = option_before + "<div class='panel_s add_pro_service_section ' id='add_pro_service_section-" + emailnext + "'><div class='option_inner panel-body'><h5 class='sub-title'><span>New Option</span> <span class='remove'><i class='fa fa-trash-o'></i></span> </h5><div class='form-group options_fields' id='options_fields-" + emailnext + "'><div class='row'><div class='col-md-12'><div class='form-group'><label for='option_name' class='control-label'>Option Name<small class='req text-danger'>* </small></label><input type='text' id='option_name' name='option[" + emailnext + "][option_name]' class='form-control option_main_name' value=''></div></div></div><div class='row'><div class='col-md-9'><label for='name' class='control-label'>Type</label><select name='option[" + emailnext + "][option_type]' id='Option_type-" + emailnext + "' class='form-control list_option_type'><option value='dropdown'>Dropdown</option><option value='text_input'>Text Input</option><option value='text_text_field'>Large Text Field</option><option value='single_option'>Single Option</option><option value='multi_select'>Multi-Select</option></select></div><div class='col-md-3 mtop25'><div class='checkbox'><div class='col-md-4'><input type='checkbox' name='option[" + emailnext + "][is_required]' id='option-" + emailnext + "' value='1'><label for='option-" + emailnext + "'>Required</label></div><div class='col-md-4'></div><div class='col-md-4'></div></div></div></div></div>" + choice_field_format + choice_button_format + "</div></div>" + option_after;

        if ($('.item_options_list').length > 0) {
            $('.item_options_list').append(option_field_format);
        } else {
            $(addtoEmail).after(option_field_format);
        }
        //createOptionValidation();

    });

    var choice_button_format = "<div class='col-md-12 text-center' id='add_choice_button'><a class='btn btn-primary'>Add choice to this dropdown</a></div>";

    $(document.body).delegate('.remove', 'click', function (e) {

        e.preventDefault();
        var parent_id = $(this).parents(".choice_fields").remove();
    });
    $(document.body).delegate('#add_choice_button', 'click', function (e) {
        var parent_id = "";
        if ($(this).parent('.option_inner').length > 0) {
            var parent_id = $(this).parent().parent().attr('id');
        } else {
            parent_id = $(this).parent().attr('id');
        }
        var optionnext = parent_id.split('-');
        optionnext = optionnext[1];
        var option_available = $(this).siblings('.choices').children('.choice_fields').length;
        var option_before = "";
        var option_after = "";
        if (option_available <= 0) {
            choices_before = "<div class='choices'>";
            choices_after = "</div>";

            var addtoEmail = $(this);
            var emailnext = 0;
        } else {
            e.preventDefault();
            var my_email_fields = $("#" + parent_id + " .choices div[id^='choice_fields-']");
            var highestemail = -Infinity;
            $.each(my_email_fields, function (mindex, mvalue) {
                var fieldEmailNum = mvalue.id.split("-");
                highestemail = Math.max(highestemail, parseFloat(fieldEmailNum[1]));
            });
            var emailnext = highestemail;
            var addtoEmail = "#" + parent_id + " .choices #choice_fields-" + emailnext;
            var addRemoveEmail = "#email-" + (emailnext);

            emailnext = emailnext + 1;
            choices_before = "";
            choices_after = "";
        }

        var choice_field_format = choices_before + '<div class="form-group choice_fields" id="choice_fields-' + emailnext + '"><div class="panel_s"><div class="panel-body"><div class="choices"><h5 class="sub-title"><span>Choices</span><span class="remove"><i class="fa fa-trash-o" aria-hidden="true"></i></span> </h5><div class="choice_inner"><div class="row"><div class="col-md-12"><div class="form-group"><div class="checkbox"><input type="checkbox" name="choice[' + optionnext + '][' + emailnext + '][choice_default_selection]" id="default_selection" autocomplete="off" value="1"><label for="leads">Default Selection</label></div></div></div><div class="col-md-4"><div class="form-group"><label for="option_name" class="control-label">Name <small class="req text-danger">* </small></label><input type="text" id="option_name" name="choice[' + optionnext + '][' + emailnext + '][choice_name] " class="opt_choice_name form-control"></div></div><div class="col-md-2"><div class="form-group"><label for="choice_cost" class="control-label">Cost</label><div class="input-group"><span class="input-group-addon" ><i class="fa fa-usd" aria-hidden="true"></i></span><input type="number" id="choice_cost_price_' + emailnext + '" name="choice[' + optionnext + '][' + emailnext + '][choice_cost_price]" class="choice_cost_price_profit form-control" data-index=' + emailnext + ' data-pindex=' + optionnext + '></div></div></div><div class="col-md-2"><div class="form-group"><label for="choice_price" class="control-label">Price<small class="req text-danger">* </small></label><div class="input-group"><span class="input-group-addon" ><i class="fa fa-usd" aria-hidden="true"></i></span><input type="number" id="choice_rate_' + emailnext + '" name="choice[' + optionnext + '][' + emailnext + '][choice_rate]" class="choice_cost_price_profit form-control choice_price_new" data-index=' + emailnext + ' data-pindex=' + optionnext + '></div></div></div><div class="col-md-2"><div class="form-group"><label for="choice_profit" class="control-label">Profit</label><div class="input-group"><span class="input-group-addon" ><i class="fa fa-usd" aria-hidden="true"></i></span><input type="number" id="choice_profit_' + emailnext + '" name="choice[' + optionnext + '][' + emailnext + '][choice_profit]" class="choice_cost_price_profit form-control" data-index=' + emailnext + ' data-pindex=' + optionnext + '></div></div></div><div class="col-md-2"><div class="form-group"><label for="choice_profit_margin" class="control-label">Profit Margin(%)</label><div class="input-group"><input type="number" id="choice_profit_margin_' + emailnext + '" name="choice[' + optionnext + '][' + emailnext + '][choice_profit_margin]" class="choice_cost_price_profit form-control" data-index=' + emailnext + ' data-pindex=' + optionnext + '><span class="input-group-addon" ><i class="fa fa-percent" aria-hidden="true"></i></span></div></div></div></div></div></div></div></div></div>' + choices_after;

        if (option_available <= 0) {
            $(addtoEmail).before(choice_field_format);
        } else {
            $(addtoEmail).after(choice_field_format);
        }
//createChoicepriceValidation();

    });

    $(document.body).delegate('.form-control.choice_price_new', 'change', function () {
        //createChoicepriceValidation();
    });
    $(document.body).delegate('.list_option_type', 'change', function () {
        var opt_type_btn = 0;
        if ($(this).val() == "dropdown" || $(this).val() == "single_option" || $(this).val() == "multi_select") {
            opt_type_btn = 1;
        } else {
            opt_type_btn = 0;
        }

        var id = $(this).parent().parent().parent().attr('id');

        if ($(".option_inner").length > 0) {
            var npid = $(this).parent().parent().parent().parent().parent().attr('id');
        } else {
            var npid = $(this).parent().parent().parent().parent().attr('id');
        }
        if (opt_type_btn == 1) {
            var nid = "#" + npid + " #add_choice_button";

            if ($(nid).length <= 0) {
                $("#" + npid + " .option_inner").append(choice_button_format);
                //$("#"+npid+" .option_inner > .choices").show();
            }
        } else {
            var nid = "#" + npid + " #add_choice_button";
            if ($(nid).length > 0) {
                $(nid).remove();
                $("#" + npid + " .option_inner > .choices").remove();
            }
        }
    });

    if ($("#item_choice_id").val() != 0) {
        $('.choice_field_section').show();
        $('#add_choice_button').show();
    } else {
        $('.choice_field_section').hide();
    }

    /**/
    if ($("input[name=tagid]").val() != 0) {
        $('.custom_section').show();
        $('.custom_tax').show();
    } else {
        $('.custom_section').hide();
        $('.custom_tax').hide();
    }

    $('#taxable').change(function () {
        $('.custom_section').toggle();
        $('.custom_tax').hide();
        $('#custom').prop('checked', false);
    });

    $('#custom').change(function () {
        $('.custom_tax').toggle();
        $('#custom_tax_button').toggle();
    });
    $("#taxes_list").on("change", function () {
        $modal = $('#tax_modal');
        if ($(this).val() === 'add_new_tax_modal') {
            $modal.modal('show');
        }
    });


    $("#income_category").on("change", function () {
        $modal = $('#income_category_modal');
        if ($(this).val() === 'add_new_income_cat') {
            $modal.modal('show');
        }
    });


    $("#expense_category").on("change", function () {
        $modal = $('#expense_category_modal');
        if ($(this).val() === 'add_new_expense_cat') {
            $modal.modal('show');
        }
    });

    if ($("#taxable").prop('checked') == false) {
        $('.custom_section').hide();
    } else {
        $('#custom').show();
    }

    if ($("#custom").prop('checked') == false) {
        $('#taxes_list').hide();
    } else {
        $('#taxes_list').show();
    }

    var validator = $("#manage-item-form").validate({
        rules: {
            description: {
                required: true,
                remote: {
                    url: admin_url + "invoice_items/line_item_name_exists",
                    type: 'post',
                    data: {
                        tagid: function () {
                            return $('input[name="tagid"]').val();
                        }
                    }
                }
            },
            sku: {
                /*required:false,*/
                remote: {
                    url: admin_url + "invoice_items/sku_name_exists",
                    type: 'post',
                    data: {
                        tagid: function () {
                            return $('input[name="tagid"]').val();
                        }
                    }
                }
            },

            rate: {required: true},
            tax: {
                required:
                    function (element) {
                        if ($("#custom").is(':checked')) {
                            var e = document.getElementById("taxes_list");
                            return e.options[e.selectedIndex].value == "";
                        } else {
                            return false;
                        }
                    }
            },
        },
        messages: {
            sku: "The sku already exist",

        }
    });

    // Code for multiple email validation
    var createOptionValidation = function () {
        $(".options_fields .form-control.option_main_name").each(function (index, value) {
            $(this).rules('remove');
            $(this).rules('add', {
                option: true,
                required: true,
                messages: {
                    email: "Please enter an option name.",
                    required: "Please enter an option name."
                }
            });
        });
    }

    var createChoicepriceValidation = function () {
        $(".choice_fields .form-control.choice_price_new").each(function (index, value) {
            $(this).rules('remove');
            $(this).rules('add', {
                option: true,
                required: true,
                messages: {
                    email: "Please enter choice price.",
                    required: "Please enter choice price."
                }
            });
        });
    }
    init_editor('.long_description');
    var lochref = $(location).attr('href');
    var package_id = <?php echo $package_id; ?>;
    var proposal_id = <?php echo $proposal_id; ?>;

    function fncancel() {
        if (lochref.indexOf("pid") >= 0) {
            location.href = '<?php echo base_url(); ?>admin/proposaltemplates/proposal/' + proposal_id;
        } else if (lochref.indexOf("package_id") >= 0) {
            location.href = '<?php echo base_url(); ?>admin/invoice_items/package/' + package_id;
        } else {
            location.href = '<?php echo base_url(); ?>admin/invoice_items';
        }
    }

    $(function () {
        _validate_form($('form'), {
            description: {
                required: true,
                remote: {
                    url: admin_url + "invoice_items/invoice_name_exists",
                    type: 'post',
                    data: {
                        itemid: function () {
                            return $('input[name="itemid"]').val();
                        }
                    }
                }
            },
            rate: {
                required: true,
            },
            group_id: 'required'
        });


    });

    function manage_tax(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
                $('.table-taxes').DataTable().ajax.reload();
                alert_float('success', response.message);
            } else {
                if (response.message != '') {
                    alert_float('warning', response.message);
                }
            }
            $('#tax_modal').modal('hide');

            $.ajax({
                url: admin_url + "invoice_items/get_all_taxes",
                success: function (result) {
                    var $el = $("#taxes_list");
                    $el.empty();
                    $.each(JSON.parse(result), function (key, value) {
                        $el.append($("<option></option>")
                            .attr("value", value.id).text(value.taxrate));
                    });
                    $el.append($("<option></option>")
                        .attr("value", 'add_new_tax_modal').text('Add new tax'));
                    $($el).selectpicker('refresh');
                    $($el).selectpicker('render');
                }
            });

        });
        return false;
    }

    _validate_form($('#tax_form'), {
            name: {
                required: true,
                remote: {
                    url: admin_url + "taxes/tax_name_exists",
                    type: 'post',
                    data: {
                        taxid: function () {
                            return $('input[name="taxid"]').val();
                        }
                    }
                }
            },
            rate: {number: true, required: true}
        },
        manage_tax);


    function manage_expense_category(form) {
        var data = $(form).serialize();
        var url = form.action;

        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
            } else {
                if (response.message != '') {
                    alert_float('warning', response.message);
                }
            }

            $('#expense_category_modal').modal('hide');

            $.ajax({
                url: admin_url + "invoice_items/get_all_expense_category_list",
                success: function (result) {
                    var $el = $("#expense_category");
                    $el.empty();
                    $.each(JSON.parse(result), function (key, value) {
                        $el.append($("<option></option>")
                            .attr("value", value.id).text(value.name));
                    });
                    $el.append($("<option></option>")
                        .attr("value", 'add_new_expense_cat').text('New ExpenseCategory'));
                    $($el).selectpicker('refresh');
                    $($el).selectpicker('render');
                }
            });

        });
        return false;
    }

    _validate_form($('#expense_category_form'), {
            name: {
                required: true,
                remote: {
                    url: admin_url + "invoice_items/expensecategory_name_exists",
                    type: 'post',
                    data: {
                        id: function () {
                            return $('input[name="id"]').val();
                        }
                    }
                }
            }
        },
        manage_expense_category);


    function manage_income_category(form) {
        var data = $(form).serialize();
        var url = form.action;

        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
            } else {
                if (response.message != '') {
                    alert_float('warning', response.message);
                }
            }

            $('#income_category_modal').modal('hide');

            $.ajax({
                url: admin_url + "invoice_items/get_all_income_category_list",
                success: function (result) {
                    var $el = $("#income_category");
                    $el.empty();
                    $.each(JSON.parse(result), function (key, value) {
                        $el.append($("<option></option>")
                            .attr("value", value.id).text(value.name));
                    });
                    $el.append($("<option></option>")
                        .attr("value", 'add_new_income_cat').text('New Income Category'));
                    $($el).selectpicker('refresh');
                    $($el).selectpicker('render');
                }
            });

        });
        return false;
    }

    _validate_form($('#income_category_form'), {
            name: {
                required: true,
                remote: {
                    url: admin_url + "invoice_items/incomecategory_name_exists",
                    type: 'post',
                    data: {
                        id: function () {
                            return $('input[name="id"]').val();
                        }
                    }
                }
            }
        },
        manage_income_category);


    $('#existing-client-book').toggle();


    /*
    * Added by: Sanjay
    * Date: 02-05-2018
    * for calculating price,profit and profit margin
    */
    $("#cost_price, #rate ,#profit_margin,#profit ").change(function () {
        calculate_price(this.name);
    });

    /*$("#cost_price, #profit_margin").change(function()
    {
      calculate_price(this.name);
    });
    $("#cost_price, #profit").change(function()
    {
      calculate_price(this.name);
    });
    $("#rate, #profit").change(function()
    {
      calculate_price(this.name);
    });*/

    function calculate_price(value) {
        if (value == "rate") {//alert('1');
            var total_price = $('#rate').val();
            var cost_price = $('#cost_price').val();
            var change_price = "";
            var profit_rate = "";
            if (cost_price > 0) {
                profit_rate = total_price - cost_price;
                change_price = (profit_rate * 100) / cost_price;
            } else {
                profit_rate = total_price;
                change_price = 100;
            }
            $('#profit').val(profit_rate.toFixed(2));
            $('#profit_margin').val(change_price.toFixed(2));
        } else if (value == "profit_margin") {//alert('2');
            var profit_margin = $('#profit_margin').val();
            var cost_price = $('#cost_price').val();
            var rate = $('#rate').val();
            if (cost_price > 0) {
                var profit_rate = (profit_margin * cost_price) / 100;
                $('#profit').val(Math.round(profit_rate).toFixed(2));
                var net_profit = $('#profit').val();
                var change_price = parseInt(cost_price) + parseInt(net_profit);
                $('#rate').val(change_price.toFixed(2));
            } else {
                var cost_calc = 100 + parseInt(profit_margin);
                var cost_new = (rate * 100) / cost_calc;
                var new_profit = rate - cost_new;
                $('#cost_price').val(cost_new);
                $('#profit').val(new_profit);
            }
        } else if (value == "cost_price") {
            //alert('3');
            var cost_price = $('#cost_price').val();
            var profit = $('#profit').val();
            var price_rate = $('#rate').val();
            var profit_margin = $('#profit_margin').val();
            if (cost_price > 0) {
                if (price_rate > 0) {
                    profit = parseInt(price_rate) - parseInt(cost_price);
                    $('#profit').val(profit.toFixed(2));
                } else {
                    if (profit > 0) {
                        price_rate = parseInt(cost_price) + parseInt(profit);
                        $('#rate').val(Math.round(price_rate).toFixed(2));
                    }
                }
                var total_price = $('#rate').val();
                var net_profit_margin = ((total_price * 100) / cost_price) - 100
                $('#profit_margin').val(net_profit_margin.toFixed(2));
            } else if ($('#profit').val() != "") {
                return false;
            } else {
                var cost_price = $('#cost_price').val();
                var profit = $('#profit').val();
                var price_rate = $('#rate').val();
                $('#profit').val(price_rate);
                var margin = ($('#profit').val() * 100) / $("#rate").val();
                $('#profit_margin').val(margin.toFixed(2));
            }
        } else if (value == "profit") {//alert('4');
            var cost_price = $("#cost_price").val();
            var total_price = $('#rate').val();
            var net_profit = $('#profit').val();
            var calc_net_profit = "";
            if (cost_price > 0) {
                total_price = parseInt(cost_price) + parseInt(net_profit);
                calc_net_profit = ((net_profit * 100) / cost_price);
            } else {
                if (total_price > 0) {
                    cost_price = parseInt(total_price) - parseInt(net_profit);
                    $("#cost_price").val(cost_price.toFixed(2));
                    calc_net_profit = ((total_price * 100) / cost_price) - 100;
                } else {
                    calc_net_profit = "";
                }
            }
            $('#rate').val(total_price.toFixed(2));
            $('#profit_margin').val(calc_net_profit.toFixed(2));
        }
    }

    var id = '';
    var index = '';
    var i = '';
    var pid = "";
    $('body').on('change', '.choice_cost_price_profit, #choice_cost_price, #choice_rate', function () {
        /*if($('.choice_inner').length > 0){
            id = $(this).parent().parent().parent().parent().parent().attr('id');

            pid = $(this).parent().parent().parent().parent().parent().parent().parent().parent().attr('id');
        }else{
            id = $(this).parent().parent().parent().parent().attr('id');

            pid = $(this).parent().parent().parent().parent().parent().parent().attr('id');
        }*/
        index = $(this).data('index');
        pindex = $(this).data('pindex');
        pid = "add_pro_service_section-" + pindex;
        //index = id.split('-');
        //i = index[1];
        choice_calculate_price(this.name, index, pid);
    });


    //To calculate choice price according to cost,price,profit,profit margin
    function choice_calculate_price(value, index, pid) {
        pindex = pid.split('-');
        pindex = pindex[1];
        pid = "#" + pid;

        if (value == "choice[" + pindex + "][" + index + "][choice_rate]") {
            var total_price = $(pid + ' #choice_rate_' + index).val();
            var cost_price = $(pid + ' #choice_cost_price_' + index).val();
            var change_price = "";
            var profit_rate = "";
            if (cost_price > 0) {
                profit_rate = total_price - cost_price;
                change_price = (profit_rate * 100) / cost_price;
            } else {
                profit_rate = total_price;
                change_price = 100;
            }
            $(pid + ' #choice_profit_' + index).val(parseFloat(profit_rate));
            $(pid + ' #choice_profit_margin_' + index).val(change_price.toFixed(2));
        } else if (value == "choice[" + pindex + "][" + index + "][choice_profit_margin]") {
            var profit_margin = $(pid + ' #choice_profit_margin_' + index).val();
            var cost_price = $(pid + ' #choice_cost_price_' + index).val();
            var rate = $(pid + ' #choice_rate_' + index).val();
            if (cost_price > 0) {
                var profit_rate = (profit_margin * cost_price) / 100;
                $(pid + ' #choice_profit_' + index).val(Math.round(profit_rate).toFixed(2));
                var net_profit = $(pid + ' #choice_profit_' + index).val();
                var change_price = parseInt(cost_price) + parseInt(net_profit);
                $(pid + ' #choice_rate_' + index).val(change_price);
            } else {
                var cost_calc = 100 + parseInt(profit_margin);
                var cost_new = (rate * 100) / cost_calc;
                var new_profit = rate - cost_new;
                $(pid + ' #choice_cost_price_' + index).val(cost_new);
                $(pid + ' #choice_profit_' + index).val(new_profit);
            }
        } else if (value == "choice[" + pindex + "][" + index + "][choice_cost_price]") {
            var cost_price = $(pid + ' #choice_cost_price_' + index).val();
            var profit = $(pid + ' #choice_profit_' + index).val();
            var price_rate = $(pid + ' #choice_rate_' + index).val();
            if (cost_price > 0) {
                if (price_rate > 0) {
                    profit = parseInt(price_rate) - parseInt(cost_price)
                } else {
                    price_rate = parseInt(cost_price) + parseInt(profit);
                }

                $(pid + ' #choice_rate_' + index).val(Math.round(price_rate).toFixed(2));
                var net_profit = $(pid + ' #choice_profit_' + index).val();
                var total_price = $(pid + ' #choice_rate_' + index).val();
                var net_profit_margin = ((total_price * 100) / cost_price) - 100
                $(pid + ' #choice_profit_' + index).val(Math.round(profit).toFixed(2));
                $(pid + ' #choice_profit_margin_' + index).val(net_profit_margin.toFixed(2));
            } else {
                var price_rate = $(pid + ' #choice_rate' + index).val();
                $(pid + ' #choice_profit' + index).val(price_rate);
                var margin = ($(pid + ' #choice_profit_' + index).val() * 100) / $(pid + ' #choice_rate_' + index).val();
                $(pid + ' #choice_profit_margin_' + index).val(margin.toFixed(2));
            }
        } else if (value == "choice[" + pindex + "][" + index + "][choice_profit]") {
            var cost_price = $(pid + ' #choice_cost_price_' + index).val();
            var total_price = $(pid + ' #choice_rate_' + index).val();
            var net_profit = $(pid + ' #choice_profit_' + index).val();
            var calc_net_profit = "";
            if (cost_price > 0) {
                total_price = parseInt(cost_price) + parseInt(net_profit);
                calc_net_profit = ((net_profit * 100) / cost_price);
            } else {
                total_price = net_profit;
                net_profit = $(pid + ' #choice_profit_' + index).val();
                calc_net_profit = 100;
            }
            $(pid + ' #choice_rate_' + index).val(total_price);
            $(pid + ' #choice_profit_margin_' + index).val(calc_net_profit.toFixed(2));
        }
    }

    $(function () {
        $(".sortable").sortable({
            stop: function (event, ui) {
                var clas = ui.item.attr("data-class");
                order = 0;
                count = 0;
                var option = [];
                $("." + clas).each(function () {
                    var id = $(this).attr('data-id');
                    order = $(this).attr('data-order');
                    var option_val = {
                        'id': id,
                        'order': count,
                    };
                    $(this).attr('data-order', count);

                    option.push(option_val);

                    count++;
                });
                option = JSON.stringify(option);
                if (clas == "option") {
                    var url = "<?php echo admin_url('invoice_items/ajax_option_order_update'); ?>";
                } else {
                    var url = "<?php echo admin_url('invoice_items/ajax_choice_order_update'); ?>";
                }
                $.ajax({
                    method: "POST",
                    url: url,
                    data: "options=" + option,
                }).done(function () {

                });
            }

        });
    });

    $(document.body).delegate('.opt_name', 'click', function () {
        $('i', this).toggleClass('fa-caret-up fa-caret-down');
        $(this).toggleClass('expanded');
        $(this).parent().siblings('.option_choices').slideToggle();
    });
    $(document.body).delegate('.choice_name', 'click', function () {
        $('i', this).toggleClass('fa-caret-up fa-caret-down');
        $(this).toggleClass('expanded');
        $(this).parent().siblings('.choice_inner').slideToggle();
    });
    $(document.body).delegate('.opt_edite', 'click', function () {
        var id = $(this).attr('data-id');
        selector = id + " .option_inner";
        $(selector).slideToggle();
        $('.table_actions').slideUp().removeClass('active');
    });

    function duplicate_option_choice(type, id) {
        var option_val = {'id': id, 'type': type};
        option_val = JSON.stringify(option_val);
        $.ajax({
            type: 'POST',
            url: "<?php echo admin_url('invoice_items/duplicate_option_choice_ajax'); ?>",
            data: "data=" + option_val,
            success: function (result) {
                $(".options-block").html(result);
            }
        });
    }

    function delete_option_choice(type, id, divid) {

        var alertTitle = 'Are you sure?';
        var option_val = {'id': id, 'type': type};
        option_val = JSON.stringify(option_val);
        swal({
            title: alertTitle,
            text: "You won't be able to revert this!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonClass: 'btn btn-danger',
            confirmButtonText: 'Yes, delete it!',
            showLoaderOnConfirm: true,
            preConfirm: function () {
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        type: 'POST',
                        url: "<?php echo admin_url('invoice_items/delete_option_choice_ajax'); ?>",
                        data: "data=" + option_val,
                    })
                        .done(function (response) {
                            if (response.indexOf(':') > -1) {
                                swal('Oops...', response.substring(response.indexOf(':') + 1), 'warning');
                            } else {
                                swal('Deleted!', response.message, response.status);
                                $(divid).remove();
                            }
                        })
                        .fail(function () {
                            swal('Oops...', 'Something went wrong !', 'error');
                        });
                });
            },
            allowOutsideClick: false
        }).catch(swal.noop);
        return false;
    }

    /*$(document.body).delegate('input[type=checkbox]', 'change', function() {
    //$('.default_selection').click(function(){
      var pid = $(this).attr('name');
      pid = $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent().attr('id');
      if(typeof pid === 'undefined'){
        pid = $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().attr('id');
      }
      alert(pid);
      $('#'+pid+' input[type=checkbox]').prop("checked",false);
      if($(this).prop("checked") == false){
        $(this).prop("checked",true);
      }

    });*/

    $('.product_service_submit').on('click', function (e) {
        //$('.text-danger').remove();
        erroFlag = 0;
        $(".option_main_name").each(function () {
            if ($(this).val() == "") {
                erroFlag++;
                var id = $(this).attr('id');
                var error = '<p id=' + id + '"-error" class="text-danger">Please enter Option name.</p>';
                if ($(this).hasClass('error') == false) {
                    $(this).after(error);
                    $(this).addClass('error')
                }
            }
        });
        $(".opt_choice_name").each(function () {
            if ($(this).val() == "") {
                erroFlag++;
                var id = $(this).attr('id');
                var error = '<p id=' + id + '"-error" class="text-danger">Please enter Choice name.</p>';
                if ($(this).hasClass('error') == false) {
                    $(this).after(error);
                    $(this).addClass('error')
                }

            }
        });
        $(".choice_price_new").each(function () {
            if ($(this).val() == "") {
                erroFlag++;
                var id = $(this).attr('id');
                var error = '<p id=' + id + '"-error" class="text-danger">Please enter choice price.</p>';
                if ($(this).hasClass('error') == false) {
                    $(this).after(error);
                    $(this).addClass('error')
                }
            }
        });
        if (erroFlag > 0) {
            e.preventDefault();
        } else {
            $('#manage-item-form').submit();
        }

    });
</script>
<script type="text/javascript">

    $(document).ready(function () {
        if ($("#taxable").prop('checked') == false) {
            $('.custom_section').hide();
            $('.custom_tax').hide();
        } else {
            $('#custom').show();
            $('.custom_tax').show();
        }

        if ($("#custom").prop('checked') == false) {
            $('.custom_tax').hide();
        } else {
            $('.custom_tax').show();
        }

    });
</script>