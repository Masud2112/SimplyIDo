<?php
$tax_rate = 0;
$tax_val = 0;
if (isset($proposal) && $proposal->proposal_custom_tax > 0) {
    $ptax = get_tax_rate_by_id($proposal->proposal_custom_tax);
    $tax_rate = $ptax->taxrate;
}

$lable = "";
if (strtolower($item_type) == "package") {
    $id = $item->id;
    $image = group_image($id, array('item-profile-image-product_services_package_image'), 'thumb');
    $name = $item->name;
    $sku = $item->group_sku;
    $price = $item->group_price;
    $description = $item->group_description;
    $lable = "Package";
} else {
    $id = $item->itemid;
    $image = line_item_image($id, array('profile_image', 'img-responsive', 'item-profile-image-thumb'), 'thumb');
    $name = $item->description;
    $sku = $item->sku;
    $price = $item->rate;
    $description = $item->long_description;
}
if (isset($proposal_selected_items) && !empty($proposal_selected_items)) {
    foreach ($proposal_selected_items as $p_s_item) {
        if ($id == $p_s_item['id']) {
            $selected = "selected";
            $qty = $p_s_item['qty'];
        }
    }
}

$qty = (isset($qty) && $qty > 0 ? $qty : 1);
$mdiscoun = (!empty($mdiscoun) ? $mdiscoun : 0);
$omdiscoun = $mdiscoun;
$omdiscoun_val = $mdiscoun;
if (isset($mdiscoun_calc) && $mdiscoun_calc == "percentage") {
    $mdiscoun = ($price * $mdiscoun) / 100;
}
$subtotal = ($qty * $price) + $mdiscoun;
$checked = "";
if (isset($selected)) {
    $checked = "checked";
}

$class = "";
if ($mdiscoun_type=="discount") {
    $class = "danger";
} else {
}
if ($mdiscoun_calc == "percentage") {
    $omdiscoun_prefix = "%";
} else {
    $omdiscoun_prefix = "$";
}
if($mdiscoun_type=="discount" && $proposal->discounts==0){
    $class.= " hide";
} elseif($mdiscoun_type=="markup" && $proposal->markups==0){
    $class.= " hide";
}
if (isset($item->is_taxable) && $item->is_taxable == 1 && $tax_rate > 0) {
    $subtotal = $subtotal + (($subtotal * $tax_rate) / 100);
    $tax_val = ($subtotal * $tax_rate) / 100;
}
?>
<div id="<?php echo "quote_item_" . $qitems; ?>" class='quote_item
<?php if ($gtype == 0) {
    echo "selected";
} else {
    if (isset($selected)) {
        echo $selected;
    } else {
        echo "";
    }
} ?>' data-id="<?php echo $id; ?>" data-type="<?php echo $item_type; ?>">
    <div class="row">
        <?php if($proposal->markups==0 && $proposal->discounts==0){ ?>
        <div class="col-sm-7">
            <?php }else{?>
            <div class="col-sm-6">
            <?php } ?>
            <div class="row">
                <div class="col-sm-1"></div>
                <div class="col-sm-1">
                    <?php
                    $opname = "group[" . $quoteindex . "][selected_item]";
                    $data_pid = "#quote_item_" . $qitems;
                    if ($gtype == 1) {
                        //echo '<input type="radio" name="'.strtolower(str_replace(' ', "_", $gname)).'" value ="'.$item_type.'_'.$id.'" >';
                        echo '<input readonly class = "select_item" type="radio" name="' . $opname . '" data-gid="#group_' . $quoteindex . '" data-gtype="' . $gtype . '" data-pid="' . $data_pid . '" value="' . $id . '" ' . $checked . '>';
                    } elseif ($gtype == 2) {
                        //echo '<input type="checkbox" name="'.strtolower(str_replace(' ', "_", $gname)).'[]" value ="'.$item_type.'_'.$id.'" >';
                        echo '<div class="checkbox">
                                    <input readonly type="checkbox" class = "select_item" name="' . $opname . '[]" id="quote_item_check' . $qitems . '" data-gid="#group_' . $quoteindex . '" data-gtype="' . $gtype . '" data-pid="' . $data_pid . '" value="' . $id . '" ' . $checked . '>
                                    <label for="quote_item_check' . $qitems . '"></labelfor>
                                  </div>';
                    } else {
                        echo "";
                    }
                    ?>
                </div>
                <div class="col-sm-3">
                    <div class="item_image">
                        <?php echo $image; ?>
                        <?php if (isset($lable) && $lable != "") { ?><span><?php echo $lable; ?></span> <?php } ?>
                    </div>
                </div>
                <div class="col-sm-7">
                    <h4><?php echo $name ?></h4>
                    <h6><?php echo $sku ?></h6>
                    <?php if (isset($item->item_options) && count($item->item_options) > 0) { ?>
                        <div class="options">
                            <div class="opt_title"><?php echo _l('options'); ?><i class="fa fa-caret-down"></i></div>
                            <div class="options_list row" style="display: none;">
                                <?php foreach ($item->item_options as $option) { ?>
                                    <div class="col-sm-12">
                                        <label>
                                            <?php echo _l($option['option_type']) . " : "; ?>
                                            <?php echo _l($option['option_name']); ?>
                                        </label>
                                        <?php if ($option['option_type'] == 'dropdown' || $option['option_type'] == 'single_option' || $option['option_type'] == 'multi_select') { ?>
                                            <select class="form-control">
                                                <option value="">Choice</option>
                                                <?php foreach ($option['choices'] as $choice) { ?>
                                                    <option value="<?php echo $choice['id'] . '_' . $choice['choice_rate'] . '_' . $choice['choice_profit'] . '_' . $choice['choice_cost_price'] ?>"><?php echo $choice['choice_name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if (strtolower($item_type) == "package") { ?>
                        <?php if (isset($description) && $description != "") { ?>
                            <div class="description">
                                <div class="desc_title"
                                     data-pid="<?php echo "group_" . $quoteindex . " #quote_item_" . $qitems; ?>"><?php echo _l('Preview'); ?><i class="fa fa-caret-down"></i></div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <?php if (isset($description) && $description != "") { ?>
                            <div class="description">
                                <div class="desc_title"
                                     data-pid="<?php echo "group_" . $quoteindex . " #quote_item_" . $qitems; ?>"><?php echo _l('Description'); ?><i class="fa fa-caret-down"></i></div>
                            </div>
                        <?php } ?>
                    <?php } ?>

                </div>
            </div>
        </div>
        <div class="col-sm-1 quantity qty-col <?php //echo in_array('qty', $vcols)?'package_col visibility_visible':'package_col' ?>">
            <input type="hidden" name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][id]"
                   value= <?php echo $id; ?>>
            <input type="hidden" name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][type]"
                   value= <?php echo $item_type; ?>>
            <?php
            $readonly = "readonly";
            if ($gtype == 1 || $gtype == 2) {
                $readonly = strtolower($item_type) == 'package' ? 'readonly' : '';
                if(strtolower($item_type) == 'product' && $allow_client==0){
                    $readonly="readonly";
                }
            } ?>
            <input class="spqqty" min="1" max="<?php echo $maxqty ?>" <?php echo $readonly ?>
                   data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>" type="number"
                   name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][qty]"
                   value="<?php echo $qty != "" ? $qty : 1; ?>" style="width: 50px;text-align: center;"/>
        </div>
        <div class="col-sm-1 price psymbol price-col" data-price="<?php echo $price ?>"><?php echo $price ?></div>
        <?php /*if($proposal->markups==1 || $proposal->discounts==1){ */?>
        <div class="col-sm-1 <?php echo $class; ?>">
            <div class="markup_disc price <?php echo $class; ?>" data-mdiscount="<?php echo $omdiscoun ; ?>" data-mdistype="<?php echo $mdiscoun_calc ; ?>">
                <?php echo $omdiscoun.$omdiscoun_prefix ; ?></div>
        </div>
        <?php /*} */?>
            <div class="col-sm-1 tax-col <?php echo isset($item->is_taxable) && $item->is_taxable == 1 ? "taxable" : "" ?>" data-taxrate="<?php echo $tax_rate; ?>" data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>" >
                <?php
                if (isset($item->is_taxable) && $item->is_taxable == 1) {
                    if ($tax_val > 0) {
                        echo $tax_rate."%";
                    } else {
                        echo "<i class='fa fa-check'></i>";
                    }
                } else {
                    echo "---";
                }
                ?>
            </div>

        <div class="col-sm-1 qsubtotal psymbol qsubtotal-col <?php //echo in_array('profit', $vcols)?'package_col visibility_visible':'package_col' ?>">
            <?php echo $subtotal; ?>
        </div>
        <div class="col-sm-1 action-btns"></div>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-sm-3"></div>
            <div class="col-sm-9 desc_inner" style="display: none"><?php echo strip_tags($description); ?></div>
        </div>
    </div>
</div>