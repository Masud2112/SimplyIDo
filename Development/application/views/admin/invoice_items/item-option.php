<h4>Options</h4>
<div class="row">
    <div class="col-md-12">
        <div class="row header">
            <div class="col-md-1"><i class="fa fa-bars"></i></div>
            <div class="col-md-4">Name</div>
            <div class="col-md-1 text-center">Required</div>
            <div class="col-md-2">Type</div>
            <div class="col-md-1 text-center">Choices</div>
            <div class="col-md-2">Prices</div>
            <div class="col-md-1"></div>
        </div>
    </div>
</div>
<div class="row">
    <span id="item_option_id"></span>
    <?php if (isset($item->item_options) && count($item->item_options > 0)) { ?>
        <div class="item_options_list sortable">
            <?php foreach ($item->item_options as $op_key => $options) {
                $option_id = $options['id'];
                $option_order = $options['order'];
                $div_id = "#add_pro_service_section-" . $op_key;
                ?>
                <div class='add_pro_service_section option' id='add_pro_service_section-<?php echo $op_key ?>'
                     data-class='option' data-id='<?php echo $option_id; ?>' data-order='<?php echo $option_order; ?>'>
                    <div class="option_header row">
                        <div class="col-md-1">
                            <i class="fa fa-bars"></i>
                        </div>
                        <div class="col-md-4 opt_name">
                            <?php if ($options['option_type'] == 'dropdown' || $options['option_type'] == 'single_option' || $options['option_type'] == 'multi_select') { ?>
                                <i class="fa fa-caret-down"></i>
                            <?php } ?>
                            <?php echo $options['option_name']; ?></div>
                        <div class="col-md-1 text-center">
                            <?php if ($options['is_required'] == 1) { ?>
                                <span class="required">*</span>
                            <?php } else { ?>
                                <span class="optional">*</span>
                            <?php } ?>
                        </div>
                        <div class="col-md-2"><?php echo _l($options['option_type']); ?></div>
                        <div class="col-md-1 text-center"><?php echo(isset($options['choices']) ? count($options['choices']) : "-"); ?></div>
                        <?php
                        if (isset($options['choices'])) {
                            $rate = array();
                            foreach ($options['choices'] as $index => $choice) {
                                $rate[] = $choice['choice_rate'];
                            }
                            if (count($rate) > 0) {
                                $max_choie = max($rate);
                                $min_choie = min($rate);
                                if ($min_choie == $max_choie) {
                                    $min_choie = 0;
                                }
                                $choice_rate_range = "$" . $min_choie . " - $" . $max_choie;
                            } else {
                                $choice_rate_range = "-";
                            }
                        } else {
                            $choice_rate_range = "-";
                        }
                        ?>
                        <div class="col-md-2"><?php echo $choice_rate_range; ?></div>
                        <div class="col-md-1 text-right">
                            <div><a class='show_act mright20' href='javascript:void(0)'><i class='fa fa-ellipsis-v'
                                                                                  aria-hidden='true'></i></a></div>
                            <div class='table_actions'>
                                <ul>
                                    <li><a href="#"
                                           onclick="edit_status(this,<?php //echo $category_data['id']; ?>);return false;"
                                           data-id="#add_pro_service_section-<?php echo $op_key ?>"
                                           data-name="<?php //echo $category_data['name']; ?>"
                                           data-maincat="<?php //echo $category_data['parent_id']; ?>"
                                           class="opt_edite"><i
                                                    class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>

                                    <li><a href="#" class="" data-toggle="modal"
                                           data-target="#duplicate_line_item" id="duplicate_option_button"
                                           data-id="<?php echo $option_id; ?>"
                                           onclick="duplicate_option_choice('option',<?php echo $option_id; ?>)"><i
                                                    class="fa fa-clone"></i><span>Clone</span></a></li>

                                    <li>
                                        <a href="<?php //echo admin_url('invoice_items/delete_option/'.$option_id); ?>javascript:void(0)"
                                           class=""
                                           onclick="delete_option_choice('option',<?php echo $option_id; ?>,'<?php echo $div_id; ?>')"><i
                                                    class="fa fa-remove"></i><span>Delete</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php if ($options['option_type'] == 'dropdown' || $options['option_type'] == 'single_option' || $options['option_type'] == 'multi_select') { ?>
                        <div class="option_choices text-center" style="display: none;">
                            <label><?php echo $options['option_name']; ?></label>
                            <select class="form-control">
                                <?php foreach ($options['choices'] as $ch_key => $choice) { ?>
                                    <option>
                                        <?php echo $choice['choice_name'] . ' (+$' . $choice['choice_rate'] . ')' ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    <?php } ?>
                    <div class="option_inner" style="display: none;">
                        <?php $item_option_id = (isset($item) ? $options['id'] : ''); ?>
                        <input type="hidden" name="option[<?php echo $op_key ?>][item_option_id]"
                               id="item_option_id_new" value="<?php echo $item_option_id; ?>">
                        <h4><span class='add-title'>Option <?php echo $op_key + 1; ?></span></h4>
                        <div class='form-group options_fields' id='options_fields-<?php echo $op_key ?>'>
                            <div class='row'>
                                <div class='col-md-3'>
                                    <label for='option_name' class='control-label'>Option Name
                                        <small class="req text-danger">*</small>
                                    </label>
                                    <input type='text' id='option_name'
                                           name='option[<?php echo $op_key ?>][option_name]'
                                           class='form-control option_main_name'
                                           value='<?php echo $options['option_name']; ?>'>
                                </div>

                                <div class='col-md-3'>
                                    <label for='name' class='control-label'>Type</label>
                                    <select name='option[<?php echo $op_key ?>][option_type]'
                                            id='Option_type-<?php echo $op_key ?>'
                                            class='form-control list_option_type'>
                                        <!--<option value=''>Select Type</option>-->
                                        <option value='dropdown'
                                                <?php if ($options['option_type'] == "dropdown"){ ?>selected<?php } ?>>
                                            Dropdown
                                        </option>
                                        <option value='text_input'
                                                <?php if ($options['option_type'] == "text_input"){ ?>selected<?php } ?>>
                                            Text Input
                                        </option>
                                        <option value='text_text_field'
                                                <?php if ($options['option_type'] == "text_text_field"){ ?>selected<?php } ?>>
                                            Large Text Field
                                        </option>
                                        <option value='single_option'
                                                <?php if ($options['option_type'] == "single_option"){ ?>selected<?php } ?>>
                                            Single Option
                                        </option>
                                        <option value='multi_select'
                                                <?php if ($options['option_type'] == "multi_select"){ ?>selected<?php } ?>>
                                            Multi-Select
                                        </option>
                                    </select>
                                </div>
                                <div class='col-md-3 mtop25'>
                                    <div class='checkbox row'>
                                        <div class='col-md-6 '><input type='checkbox'
                                                                      name="option[<?php echo $op_key ?>][is_required]"
                                                                      id='option-<?php echo $op_key ?>' value='1'
                                                                      <?php if ($options['is_required'] == "1") { ?>checked<?php } ?>><label
                                                    for="option-<?php echo $op_key ?>">Required</label></div>
                                        <!-- <div class='col-md-6'><input type='radio' name='option[<?php echo $op_key ?>][is_required]' id='option2' value='0' <?php if ($options['is_required'] == "0") { ?>checked <?php } ?>><label>Optional</label></div> -->

                                    </div>
                                </div>
                                <div class='col-md-3 mtop25 text-right'>
                                    <!-- <a href = "javascript:void(0)" class='remove btn btn-primary' id="main_remove_option"onclick="delete_option_choice('option',<?php echo $option_id; ?>,'<?php echo $div_id; ?>')">REMOVE OPTION</a> -->
                                </div>
                            </div>
                        </div>

                        <?php if (isset($options['choices']) && count($options['choices'] > 0)) { ?>
                            <div class="choices_header">
                                <div class="col-md-1"></div>
                                <div class="col-md-7">Choice</div>
                                <div class="col-md-2">Price</div>
                                <div class="col-md-2"></div>
                            </div>
                            <div class="choices sortable">
                                <?php foreach ($options['choices'] as $ch_key => $choice) { ?>
                                    <?php //echo $ch_key = 1;
                                    $choice_id = $choice['id'];
                                    $choice_order = $choice['order'];
                                    $div_id = "#add_pro_service_section-" . $op_key;
                                    $div_id .= " #choice_fields-" . $ch_key;
                                    ?>

                                    <div class=" choice_fields choice" id="choice_fields-<?php echo $ch_key ?>"
                                         data-class='choice' data-id='<?php echo $choice_id; ?>'
                                         data-order='<?php echo $choice_order; ?>'>
                                        <div class="choice_header">
                                            <div class="col-md-1"><i class="fa fa-bars"></i></div>
                                            <div class="col-md-7 choice_name">
                                                <i class="fa fa-caret-down"></i><?php echo $choice['choice_name']; ?>
                                            </div>
                                            <div class="col-md-2 choice_price">
                                                <?php echo $choice['choice_rate']; ?>
                                            </div>
                                            <div class="col-sm-2 text-right">
                                                <div><a class='show_act mright20' href='javascript:void(0)'><i
                                                                class='fa fa-ellipsis-v'
                                                                aria-hidden='true'></i></a></div>
                                                <div class='table_actions'>
                                                    <ul>
                                                        <li><a href="#" class=""
                                                           data-toggle="modal"
                                                           data-target="#duplicate_line_item"
                                                           id="duplicate_action_button"
                                                           data-id="67"
                                                           onclick="duplicate_option_choice('choice',<?php echo $choice_id; ?>)"><i
                                                                        class="fa fa-clone"></i>Clone</a></li>
                                                        <li><a href="javascript:void(0) <?php //echo admin_url('invoice_items/delete_category_status/'); ?>"
                                                           class=""
                                                           onclick="delete_option_choice('choice',<?php echo $choice_id; ?>,'<?php echo $div_id; ?>')"><i
                                                                        class="fa fa-remove"></i>Delete</a></li></ul>
                                                </div>
                                            </div>
                                        </div>
                                        <?php $item_choice_id = (isset($item) ? $choice['id'] : ''); ?>
                                        <div class="choice_inner" style="display: none;">
                                            <input type="hidden"
                                                   name="choice[<?php echo $op_key ?>][<?php echo $ch_key ?>][item_choice_id]"
                                                   id="item_choice_id" value="<?php echo $item_choice_id; ?>"/>
                                            <h4><span class="add-title">Choice <?php echo $ch_key + 1; ?> </span></h4>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="choice-firstrow">
                                                        <label for="option_name" class="control-label">Name</label>
                                                        <div class="checkbox">
                                                            <input type="checkbox" class="default_selection"
                                                                   data-pid='add_pro_service_section-<?php echo $op_key ?>'
                                                                   name="choice[<?php echo $op_key ?>][<?php echo $ch_key ?>][choice_default_selection]"
                                                                   id="default_selection" autocomplete="off" value="1"
                                                                   <?php if ($choice['is_default_select'] == "1"){ ?>checked<?php } ?>>
                                                            <label for="default_selection">Default Selection</label>
                                                        </div>
                                                        <input type="text" id="option_name"
                                                               name="choice[<?php echo $op_key ?>][<?php echo $ch_key ?>][choice_name]"
                                                               class=" opt_choice_name form-control"
                                                               value="<?php echo $choice['choice_name']; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="choice_cost" class="control-label">Cost</label>
                                                        <input type="number"
                                                               id="choice_cost_price_<?php echo $ch_key ?>"
                                                               name="choice[<?php echo $op_key ?>][<?php echo $ch_key ?>][choice_cost_price]"
                                                               class="form-control choice_cost_price_profit"
                                                               data-index="<?php echo $index ?>"
                                                               data-pindex="<?php echo $op_key ?>"
                                                               value="<?php echo $choice['choice_cost_price']; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="choice_price" class="control-label">Price</label>
                                                        <small class="req text-danger"> *</small>
                                                        <input type="number" id="choice_rate_<?php echo $ch_key ?>"
                                                               name="choice[<?php echo $op_key ?>][<?php echo $ch_key ?>][choice_rate]"
                                                               class="choice_cost_price_profit form-control choice_price_new"
                                                               data-index="<?php echo $index ?>"
                                                               data-pindex="<?php echo $op_key ?>"
                                                               value="<?php echo $choice['choice_rate']; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="choice_profit" class="control-label">Profit</label>
                                                        <input type="number" id="choice_profit_<?php echo $ch_key ?>"
                                                               name="choice[<?php echo $op_key ?>][<?php echo $ch_key ?>][choice_profit]"
                                                               class="choice_cost_price_profit form-control"
                                                               data-index="<?php echo $index ?>"
                                                               data-pindex="<?php echo $op_key ?>"
                                                               value="<?php echo $choice['choice_profit']; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="choice_profit_margin" class="control-label">Profit
                                                            Margin(%)</label>
                                                        <input type="number"
                                                               id="choice_profit_margin_<?php echo $ch_key ?>"
                                                               name="choice[<?php echo $op_key ?>][<?php echo $ch_key ?>][choice_profit_margin]"
                                                               class="choice_cost_price_profit form-control"
                                                               data-index="<?php echo $index ?>"
                                                               data-pindex="<?php echo $op_key ?>"
                                                               value="<?php echo $choice['choice_profit_margin']; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <button class="remove btn btn-primary" id="remove_choice" onclick="delete_option_choice('choice',<?php echo $choice_id; ?>,'<?php echo $div_id; ?>')">REMOVE CHOICE</button> -->
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <?php if ($options['option_type'] == 'dropdown' || $options['option_type'] == 'single_option' || $options['option_type'] == 'multi_select') { ?>
                            <div class="text-center mtop10 mbot10" id="add_choice_button"><a
                                        class="btn btn-primary col-md-12">Add choice to this dropdown</a></div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <div class="col-md-12 choice_field_section">
        <div class="mtop25">
            <div class="alert alert-warning hide tax_is_used_in_expenses_warning">
                <?php echo _l('tax_is_used_in_expenses_warning'); ?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <hr class="hr-panel-heading">
    <div class="col-md-12 text-center" id="add_option_button">
        <a class="btn btn-primary"><i class="fa fa-plus-square" aria-hidden="true"></i> Add an option for
            this Product/Service</a>
    </div>
    <!-- <hr class="hr-panel-heading"> -->
</div>