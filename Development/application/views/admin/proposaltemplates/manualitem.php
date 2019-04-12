<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 14-08-2018
 * Time: 11:59 AM
 */
?>

<div class="line-item-page">
    <div class="row">
        <?php
        $action = $this->uri->uri_string();
        echo form_open_multipart($action, array('class' => 'item-form', 'id' => 'manual-item-form', 'autocomplete' => 'off')); ?>

        <div class="col-md-12">
            <div class="clearfix"></div>
            <div class="panel_s">
                <div class="panel-body">
                    <div class="clearfix"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="profile_image"
                                       class="profile-image"><?php echo _l('line_item_image'); ?></label>
                                <i class="fa fa-question-circle" data-toggle="tooltip"
                                   data-title="<?php echo _l('profile_dimension'); ?>"></i>
                                <div class="input-group">
                          <span class="input-group-btn">
                              <span class="btn btn-primary"
                                    onclick="$(this).parent().find('input[type=file]').click();">Browse</span>
                              <input name="profile_image"
                                     onchange="$(this).parent().parent().find('.form-control').html($(this).val().split(/[\\|/]/).pop());"
                                     style="display: none;" type="file">
                            </span>
                                    <span class="form-control profile_image_name"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php echo render_input('description', 'invoice_item_add_edit_description', '', 'text'); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?php $group = (isset($item) ? $item->group_id : ''); ?>
                            <label for="catgory-subcategory"><?php echo _l('product_service_group'); ?></label>
                            <select class="selectpicker form-control" name="line_item_sub_category"
                                    id="line_item_sub_category">
                                <option value="">Select Category</option>
                                <?php foreach ($product_service_groups as $group) { ?>
                                    <?php $line_item_sub_category_old = (isset($item->line_item_sub_category) ? $item->line_item_sub_category : ''); ?>
                                    <option value="<?php echo $group['id']; ?>"
                                            <?php if ($line_item_sub_category_old == $group['id']) { ?>selected <?php } ?> ><?php echo $group['parent_category'] . " >> " . $group['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sku">SKU</label>
                                <input type="text" name="sku" id="sku" class="form-control" value="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="long_description"
                               class="control-label"> <?php echo _l('invoice_item_long_description'); ?> </label>
                        <textarea id="long_description" name="long_description"
                                  class="form-control long_description" rows="4"
                                  aria-hidden="true"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <label for="markup" class="control-label">Cost</label>
                            <input type="number" id="cost_price" name="cost_price" class="form-control main"
                                   value="">
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="rate" class="control-label">Price</label>
                                <input type="number" id="rate" name="rate" class="form-control main"
                                       value="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="profit" class="control-label">Profit</label>
                                <input type="number" id="profit" name="profit" class="form-control main"
                                       value="">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="profit_margin" class="control-label">Profit Margin (%)</label>
                                <input type="number" id="profit_margin" name="profit_margin"
                                       class="form-control main" value="">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="sel1">Expense Category</label>
                                <select class="selectpicker form-control" id="expense_category"
                                        name="expense_category">
                                    <option value="">Select</option>
                                    <?php foreach ($expense_category_list as $expense_category) {
                                        $expense_cat_value = (isset($item->expense_category) ? $item->expense_category : ''); ?>
                                        <option value="<?php echo $expense_category['id']; ?>"><?php echo $expense_category['name']; ?></option>
                                    <?php } ?>
                                    <!--<option value="add_new_expense_cat">New Expense Category</option>-->
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="sel1">Income Category</label>
                                <select class="selectpicker form-control" id="income_category"
                                        name="income_category">
                                    <option value="">Select</option>
                                    <?php foreach ($income_category_list as $income_category) {
                                        $income_cat_value = (isset($item->income_category) ? $item->income_category : ''); ?>
                                        <option value="<?php echo $income_category['id']; ?>"><?php echo $income_category['name']; ?></option>
                                    <?php } ?>
                                    <!--<option value="add_new_income_cat">New Income Category</option>-->
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12"><label class="alert-info labelTaxes" for="sel1">Taxes are
                                        applied when creating a proposal or invoice.</label></div>
                                <div class="col-md-4 taxable_section">
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <input type="checkbox" class="tax_group" name="is_taxable" id="taxable"
                                                   value="1">
                                            <label for="taxable">Taxable</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 custom_section">
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <input type="checkbox" class="tax_group" name="is_custom" id="custom"
                                                   value="1">
                                            <label for="leads">Custom </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group custom_tax">
                                        <select class="form-control selectpicker" name="tax" id="taxes_list">
                                            <option value="">Select tax</option>
                                            <?php foreach ($taxes as $tax_list) { ?>
                                                <?php $tax_type_status = (isset($item->tax) ? $item->tax : ''); ?>
                                                <option value="<?php echo $tax_list['id']; ?>"
                                                        <?php if ($tax_type_status == $tax_list['id']) { ?>selected <?php } ?>><?php echo $tax_list['taxrate']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix mbot15"></div>
                    <div class="options-block manualOptionAdd_blk">
                        <?php $this->load->view('admin/proposaltemplates/item-option'); ?>
                    </div>
                    <div class="topButton">
                        <input type="hidden" value="" class="gpid">
                        <input name="is_template" type="hidden" value="0" class="is_template" >
                        <input class="btn btn-default cnclLineitem" type="button" onclick="cancelreset()"
                               value="<?php echo _l('Cancel'); ?>">
                        <button type="submit" value="template"
                                class="btn btn-info save_as_template"><?php echo _l('save_as_template'); ?></button>
                        <button type="submit"
                                class="btn btn-info product_service_submit"><?php echo _l('submit'); ?></button>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
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
        }
        else {
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
        var option_field_format = option_before + "<div class='add_pro_service_section ' id='add_pro_service_section-" + emailnext + "'><div class='option_inner'><h4><span class='add-title'>New Option</span></h4><div class='form-group options_fields' id='options_fields-" + emailnext + "'><div class='row'><div class='col-md-12'><div class='form-group'><label for='option_name' class='control-label'>Option Name<small class='req text-danger'>* </small></label><input type='text' id='option_name' name='option[" + emailnext + "][option_name]' class='form-control option_main_name' value=''></div></div></div><div class='row'><div class='col-md-9'><label for='name' class='control-label'>Type</label><select name='option[" + emailnext + "][option_type]' id='Option_type-" + emailnext + "' class='form-control list_option_type'><option value=''>Select Type</option><option value='dropdown'>Dropdown</option><option value='text_input'>Text Input</option><option value='text_text_field'>Large Text Field</option><option value='single_option'>Single Option</option><option value='multi_select'>Multi-Select</option></select></div><div class='col-md-3 mtop25'><div class='checkbox'><div class='col-md-4'><input type='checkbox' name='option[" + emailnext + "][is_required]' id='option-" + emailnext + "' value='1'><label for = 'option-" + emailnext + "'>Required</label></div><div class='col-md-4'></div><div class='col-md-4'></div></div></div></div></div><button class='btn btn-default remove' id='main_remove_option'>REMOVE OPTION</button></div></div>" + option_after;

        if ($('.item_options_list').length > 0) {
            $('.item_options_list').append(option_field_format);
        } else {
            $(addtoEmail).after(option_field_format);
        }
        //createOptionValidation();

    });

    var choice_button_format = "<div class='col-md-12 text-center' id='add_choice_button'><a class='btn btn-primary col-md-12'>Add choice to this dropdown</a></div>";

    $(document.body).delegate('.remove', 'click', function (e) {

        e.preventDefault();
        var parent_id = $(this).parent().parent().remove();
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
        }
        else {
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

        var choice_field_format = choices_before + '<div class="form-group choice_fields" id="choice_fields-' + emailnext + '"><div class="choice_inner"><h4><span class="add-title">Choices</span></h4><div class="row"><div class="col-md-12"><div class="form-group"><div class="checkbox"><input type="checkbox" name="choice[' + optionnext + '][' + emailnext + '][choice_default_selection]" id="default_selection" autocomplete="off" value="1" ><label for="leads">Default Selection</label></div></div><div class="form-group"><label for="option_name" class="control-label">Name <small class="req text-danger">* </small></label><input type="text" id="option_name" name="choice[' + optionnext + '][' + emailnext + '][choice_name] " class="opt_choice_name form-control" ></div></div></div><div class="row"><div class="col-md-3"><div class="form-group"><label for="choice_cost" class="control-label">Cost</label><input type="number" id="choice_cost_price_' + emailnext + '" name="choice[' + optionnext + '][' + emailnext + '][choice_cost_price]" class="form-control" ></div></div><div class="col-md-3"><div class="form-group"><label for="choice_price" class="control-label">Price<small class="req text-danger">* </small></label><input type="number" id="choice_rate_' + emailnext + '" name="choice[' + optionnext + '][' + emailnext + '][choice_rate]" class="form-control choice_price_new" ></div></div><div class="col-md-3"><div class="form-group"><label for="choice_profit" class="control-label">Profit</label><input type="number" id="choice_profit_' + emailnext + '" name="choice[' + optionnext + '][' + emailnext + '][choice_profit]" class="form-control" ></div></div><div class="col-md-3"><div class="form-group"><label for="choice_profit_margin" class="control-label">Profit Margin(%)</label><input type="number" id="choice_profit_margin_' + emailnext + '" name="choice[' + optionnext + '][' + emailnext + '][choice_profit_margin]" class="form-control"></div></div></div><button class="remove">REMOVE CHOICE</button></div></div>' + choices_after;

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
                $("#" + id).after(choice_button_format);
            }
        } else {
            var nid = "#" + npid + " #add_choice_button";
            if ($(nid).length > 0) {
                $(nid).remove();
            }
        }
    });

    if ($("#item_choice_id").val() != 0) {
        $('.choice_field_section').show();
        $('#add_choice_button').show();
    }
    else {
        $('.choice_field_section').hide();
    }

    /**/
    if ($("input[name=tagid]").val() != 0) {
        $('.custom_section').show();
        $('.custom_tax').show();
    }
    else {
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
    }
    else {
        $('#custom').show();
    }

    if ($("#custom").prop('checked') == false) {
        $('#taxes_list').hide();
    }
    else {
        $('#taxes_list').show();
    }

    var validator = $("#manage-item-form, #manual-item-form").validate({
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
                        }
                        else {
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
        }
        else if (value == "profit_margin") {//alert('2');
            var profit_margin = $('#profit_margin').val();
            var cost_price = $('#cost_price').val();
            var rate = $('#rate').val();
            if (cost_price > 0) {
                var profit_rate = (profit_margin * cost_price) / 100;
                $('#profit').val(Math.round(profit_rate).toFixed(2));
                var net_profit = $('#profit').val();
                var change_price = parseInt(cost_price) + parseInt(net_profit);
                $('#rate').val(change_price.toFixed(2));
            }
            else {
                var cost_calc = 100 + parseInt(profit_margin);
                var cost_new = (rate * 100) / cost_calc;
                var new_profit = rate - cost_new;
                $('#cost_price').val(cost_new);
                $('#profit').val(new_profit);
            }
        }
        else if (value == "cost_price") {
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
            }
            else if ($('#profit').val() != "") {
                return false;
            }
            else {
                var cost_price = $('#cost_price').val();
                var profit = $('#profit').val();
                var price_rate = $('#rate').val();
                $('#profit').val(price_rate);
                var margin = ($('#profit').val() * 100) / $("#rate").val();
                $('#profit_margin').val(margin.toFixed(2));
            }
        }
        else if (value == "profit") {//alert('4');
            var cost_price = $("#cost_price").val();
            var total_price = $('#rate').val();
            var net_profit = $('#profit').val();
            var calc_net_profit = "";
            if (cost_price > 0) {
                total_price = parseInt(cost_price) + parseInt(net_profit);
                calc_net_profit = ((net_profit * 100) / cost_price);
            }
            else {
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
    $(document.body).delegate('.form-control:not(.list_option_type),#choice_cost_price, #choice_rate', 'change', function () {
        if ($('.choice_inner').length > 0) {
            id = $(this).parent().parent().parent().parent().parent().attr('id');

            pid = $(this).parent().parent().parent().parent().parent().parent().parent().parent().attr('id');
        } else {
            id = $(this).parent().parent().parent().parent().attr('id');

            pid = $(this).parent().parent().parent().parent().parent().parent().attr('id');
        }
        index = id.split('-');
        i = index[1];

        choice_calculate_price(this.name, i, pid);
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
        }
        else if (value == "choice[" + pindex + "][" + index + "][choice_profit_margin]") {
            var profit_margin = $(pid + ' #choice_profit_margin_' + index).val();
            var cost_price = $(pid + ' #choice_cost_price_' + index).val();
            var rate = $(pid + ' #choice_rate_' + index).val();
            if (cost_price > 0) {
                var profit_rate = (profit_margin * cost_price) / 100;
                $(pid + ' #choice_profit_' + index).val(Math.round(profit_rate).toFixed(2));
                var net_profit = $(pid + ' #choice_profit_' + index).val();
                var change_price = parseInt(cost_price) + parseInt(net_profit);
                $(pid + ' #choice_rate_' + index).val(change_price);
            }
            else {
                var cost_calc = 100 + parseInt(profit_margin);
                var cost_new = (rate * 100) / cost_calc;
                var new_profit = rate - cost_new;
                $(pid + ' #choice_cost_price_' + index).val(cost_new);
                $(pid + ' #choice_profit_' + index).val(new_profit);
            }
        }
        else if (value == "choice[" + pindex + "][" + index + "][choice_cost_price]") {
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
        }
        else if (value == "choice[" + pindex + "][" + index + "][choice_profit]") {
            var cost_price = $(pid + ' #choice_cost_price_' + index).val();
            var total_price = $(pid + ' #choice_rate_' + index).val();
            var net_profit = $(pid + ' #choice_profit_' + index).val();
            var calc_net_profit = "";
            if (cost_price > 0) {
                total_price = parseInt(cost_price) + parseInt(net_profit);
                calc_net_profit = ((net_profit * 100) / cost_price);
            }
            else {
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
                            }
                            else {
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
        }
        else {
            $('#custom').show();
            $('.custom_tax').show();
        }
        if ($("#custom").prop('checked') == false) {
            $('.custom_tax').hide();
        }
        else {
            $('.custom_tax').show();
        }
    });
    $('body').on('click', '.add_manual_item_group', function (e) {
        e.preventDefault();
        var gpid = $(this).attr('data-pid');
        $('.gpid').val(gpid);
        $('#add_manual_item_popup').modal('show');
    });
    $('#manual-item-form').submit(function (e) {
        e.preventDefault();
        var data = $(this).serialize();
        var form = $('#manual-item-form')[0];
        var form_data = new FormData(form);
        if ($('#add_manual_item_popup .form-group').hasClass('has-error')) {
            $('.is_template').val(0);
            return false;
        }
        document.getElementById("manual-item-form").reset();
        $.ajax({
            type: 'POST',
            url: admin_url + 'invoice_items/item',
            data: form_data,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            success: function (result) {
                var item_type = "product";
                var gpid = $('.gpid').val();
                var qitems = $(gpid + " .quote_item").length;
                if (qitems > 0) {
                    qitems = qitems;
                } else {
                    qitems = 0;
                }
                item_id = parseInt(result);
                quote_item = gpid + " .quote_item";
                var key = $(gpid + ' .ps_pkg_item').length;
                quoteindex = gpid.split('_');
                quoteindex = quoteindex[1];
                quote_type = $(gpid + " .quote_type").val();
                var qi_data = {
                    'itemid': item_id,
                    'item_type': item_type,
                    'qitems': qitems,
                    'quoteindex': quoteindex,
                    'gtype': quote_type
                };
                $.ajax({
                    type: 'POST',
                    url: admin_url + 'proposaltemplates/get_item_for_quote',
                    data: qi_data,
                    success: function (result) {
                        $(gpid + " .quote_items").append(result);
                        $(gpid + ' .quote_items_header').removeClass('hidden');

                        $.ajax({
                            type: 'POST',
                            url: admin_url + 'proposaltemplates/item',
                            data: {'itemid': item_id, 'key': key},
                            success: function (result) {
                                $('.ps_pkg_items').append(result);
                            }
                        });
                        proposal_price_calculation();
                        final_proposal_price_calculation();
                        $('.selectpicker').selectpicker('refresh');
                        /*createPercentValidation();
                        createAmountValidation();*/
                    }
                });
                $('#add_manual_item_popup').modal('hide');
                $('.profile_image_name').text('');
            }
        });
    });
    $('.save_as_template').click(function () {
        $('.is_template').val(1);
    });
    function cancelreset(){
        document.getElementById("manual-item-form").reset();
        $('#add_manual_item_popup').modal('hide');
        $('.profile_image_name').text('');
    }
</script>
