<?php echo "<pre>";
print_r($item); ?>
<?php foreach ($options['choices'] as $ch_key => $choice) { ?>
    <?php //echo $ch_key = 1;
    $choice_id = $choice['id'];
    $choice_order = $choice['order'];
    $div_id = "choice_fields-" . $ch_key;
    ?>

    <div class=" choice_fields choice" id="choice_fields-<?php echo $ch_key ?>" data-class='choice'
         data-id='<?php echo $choice_id; ?>' data-order='<?php echo $choice_order; ?>'>
        <div class="choice_header">
            <div class="col-md-1"><i class="fa fa-bars"></i></div>
            <div class="col-md-7 choice_name">
                <i class="fa fa-caret-down"></i><?php echo $choice['choice_name']; ?>
            </div>
            <div class="col-md-2 choice_price">
                <?php echo $choice['choice_rate']; ?>
            </div>
            <div class="col-sm-2 text-right">
                <div><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v'
                                                                      aria-hidden='true'></i></a></div>
                <div class='table_actions'>
                    <ul>
                        <a href="#" class="btn btn-orange btn-xs btn-icon" data-toggle="modal"
                           data-target="#duplicate_line_item" id="duplicate_action_button" data-id="67"
                           onclick="duplicate_option_choice('choice',<?php echo $choice_id; ?>)"><i
                                    class="fa fa-clone"></i></a>
                        <a href="javascript:void(0) <?php //echo admin_url('invoice_items/delete_category_status/'); ?>"
                           class="btn btn-danger btn-xs btn-icon _delete"
                           onclick="delete_option_choice('choice',<?php echo $choice_id; ?>,'<?php echo $div_id; ?>')"><i
                                    class="fa fa-remove"></i></a>
                    </ul>
                </div>
            </div>
        </div>
        <?php $item_choice_id = (isset($item) ? $choice['id'] : ''); ?>
        <div class="choice_inner" style="display: none;">
            <input type="hidden" name="choice[<?php echo $op_key ?>][<?php echo $ch_key ?>][item_choice_id]"
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
                               class="form-control" value="<?php echo $choice['choice_name']; ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="choice_cost" class="control-label">Cost</label>
                        <input type="number" id="choice_cost_price_<?php echo $ch_key ?>"
                               name="choice[<?php echo $op_key ?>][<?php echo $ch_key ?>][choice_cost_price]"
                               class="form-control" value="<?php echo $choice['choice_cost_price']; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="choice_price" class="control-label">Price</label>
                        <small class="req text-danger"> *</small>
                        <input type="number" id="choice_rate_<?php echo $ch_key ?>"
                               name="choice[<?php echo $op_key ?>][<?php echo $ch_key ?>][choice_rate]"
                               class="form-control choice_price_new" value="<?php echo $choice['choice_rate']; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="choice_profit" class="control-label">Profit</label>
                        <input type="number" id="choice_profit_<?php echo $ch_key ?>"
                               name="choice[<?php echo $op_key ?>][<?php echo $ch_key ?>][choice_profit]"
                               class="form-control" value="<?php echo $choice['choice_profit']; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="choice_profit_margin" class="control-label">Profit Margin(%)</label>
                        <input type="number" id="choice_profit_margin_<?php echo $ch_key ?>"
                               name="choice[<?php echo $op_key ?>][<?php echo $ch_key ?>][choice_profit_margin]"
                               class="form-control" value="<?php echo $choice['choice_profit_margin']; ?>">
                    </div>
                </div>
            </div>
            <button class="remove btn btn-primary" id="remove_choice"
                    onclick="delete_option_choice('choice',<?php echo $choice_id; ?>,'<?php echo $div_id; ?>')">REMOVE
                CHOICE
            </button>
        </div>
    </div>
<?php } ?>