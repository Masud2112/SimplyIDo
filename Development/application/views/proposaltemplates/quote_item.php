<?php
/*echo "<pre>";
print_r($proposal_selected_items);
die();*/
$options = array();
$tax_rate = 0.00;
$tax_val = 0.00;
$cextra = 0.00;
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
    $id = $item->id;
    $image = line_item_image($id, array('profile_image', 'img-responsive', 'item-profile-image-thumb'), 'thumb');
    $name = $item->description;
    $sku = $item->sku;
    $price = $item->rate;
    $description = $item->long_description;
}
$originlprice = $price;
$qty = (isset($qty) && $qty > 0 ? $qty : 1);

if ($allow_client == 1 && $qty > $maxqty) {
    $qty = $maxqty;
}
$mdiscoun = (!empty($mdiscoun) ? $mdiscoun : 0);
$omdiscoun = $mdiscoun;
$omdiscoun_val = $mdiscoun;
if (isset($mdiscoun_calc) && $mdiscoun_calc == "percentage") {
    $mdiscoun = ($price * $mdiscoun) / 100;
}
$hiddensubtotal = ($price + $mdiscoun);
$subtotal = ($price + $mdiscoun) * $qty;
$class = "";
$colclass = "";
if ($omdiscoun < 0) {
    $class = "danger";
}
if ($mdiscoun_calc == "percentage") {
    $omdiscoun_prefix = "%";
} else {
    $omdiscoun_prefix = "$";
}
if ($mdiscoun_type == "discount" && $proposal->discounts == 0) {
    $class .= " hide";
    $price = $hiddensubtotal;
} elseif ($mdiscoun_type == "markup" && $proposal->markups == 0) {
    $class .= " hide";
    $price = $hiddensubtotal;
}
if (isset($item->is_taxable) && $item->is_taxable == 1 && $tax_rate > 0) {
    $tax_val = ($subtotal * $tax_rate) / 100;
    $subtotal = $subtotal + $tax_val;
}
$disabled = "";
if ($proposal->status == "accepted" || $proposal->status == "decline" || (isset($proposal->feedback) && $proposal->feedback->total_signed > 0)) {
    //$disabled="disabled";
}

/*if(fmod($subtotal,1)==0){
    $subtotal = $subtotal.".00";
}else{
    $subtotal = round($subtotal,3);
}*/
$subtotal = number_format($subtotal, 2, ".", "");
$price = number_format($price, 2, ".", "");

if (isset($proposal_selected_items) && !empty($proposal_selected_items)) {
    foreach ($proposal_selected_items as $p_s_item) {
        if ($id == $p_s_item['id'] && strtolower($item_type) == $p_s_item['type']) {
            $selected = "selected";
            $qty = $p_s_item['qty'];
            $options = isset($p_s_item['options']) ? $p_s_item['options'] : array();
            $subtotal = $p_s_item['subtotal'];
        }
    }
}
$checked = "";
if (isset($selected)) {
    $checked = "checked";
}
?>
<div id="<?php echo "quote_item_" . $qitems; ?>" class='quote_item
<?php if ($gtype == 0) {
    echo "selected ";
} else {
    if (isset($selected)) {
        echo $selected;
    } else {
        echo "";
    }
}; ?>' data-id="<?php echo $id; ?>" data-type="<?php echo $item_type; ?>">
    <div class="row">
        <?php if ($proposal->markups == 0 && $proposal->discounts == 0) {
            $cols_class = "col-lg-8 col-md-7 col-sm-12";
        } else {
            $cols_class = "col-lg-7 col-md-6  col-sm-12";
        } ?>
        <div class="<?php echo $cols_class; ?>">
            <div class="row">
                <!-- <div class="col-sm-1"></div> -->
                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1">
                    <?php
                    $opname = "group[" . $quoteindex . "][selected_item]";
                    $data_pid = "#quote_item_" . $qitems;
                    if ($gtype == 1) {
                        echo '<div class="radio"><input id="' . $id . '" class = "radio select_item" type="radio" name="' . $opname . '" data-gid="#group_' . $quoteindex . '" data-gtype="' . $gtype . '" data-pid="' . $data_pid . '" value="' . strtolower($item_type) . "_" . $id . '" ' . $checked . ' ' . $disabled . '><label for="' . $id . '"></label></div>';
                    } elseif ($gtype == 2) {
                        echo '<div class="checkbox">
                                    <input type="checkbox" class = "select_item" name="' . $opname . '[]" id="quote_item_check' . $qitems . '" data-gid="#group_' . $quoteindex . '" data-gtype="' . $gtype . '" data-pid="' . $data_pid . '" value="' . strtolower($item_type) . "_" . $id . '" ' . $checked . ' ' . $disabled . '>
                                    <label for="quote_item_check' . $qitems . '"></labelfor>
                                  </div>';
                    } else {
                        echo '<div class="checkbox">
                                    <input type="checkbox" class = "select_item" checked disabled="disabled">
                                    <label></labelfor>
                                  </div>';
                    }
                    ?>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3">
                    <div class="item_image">
                        <?php echo $image; ?>
                        <?php if (isset($lable) && $lable != "") { ?><span><?php echo $lable; ?></span> <?php } ?>
                    </div>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9 col-xs-8">
                    <h4><?php echo $name; ?></h4>
                    <h6><?php echo $sku ?></h6>
                    <?php if (isset($item->item_options) && count($item->item_options) > 0) { ?>
                        <div class="options">
                            <div class="opt_title"><?php echo _l('options'); ?><i class="fa fa-caret-down"></i>
                            </div>
                            <div class="options_list options_list_blk row" style="display: none;">
                                <?php
                                foreach ($item->item_options as $option) {
                                    $choiceid = isset($options[$option['id']]) ? $options[$option['id']] : "";
                                    if (is_array($choiceid)) {
                                        foreach ($choiceid as $chid) {
                                            if (is_numeric($chid) && $chid > 0) {
                                                $choicerate = get_choice_rate_by_id($chid);
                                                $cextra = $choicerate + $cextra;
                                            }
                                        }
                                    } else {
                                        if (is_numeric($choiceid) && $choiceid > 0) {
                                            $choicerate = get_choice_rate_by_id($choiceid);
                                            $cextra = $choicerate + $cextra;
                                        }
                                    }
                                    ?>
                                    <div class="col-sm-12">
                                        <div>
                                            <strong><?php echo ucfirst($option['option_name']); ?></strong>
                                        </div>
                                        <?php if ($option['option_type'] == 'dropdown') { ?>
                                            <select class="selectpicker quote_item_option form-control" <?php echo $option['option_type'] == 'multi_select' ? "multiple" : ""; ?>
                                                    name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][options][<?php echo $option['id']; ?>]"
                                                    data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>">
                                                <option value="" data-price="0">Select Choice</option>
                                                <?php foreach ($option['choices'] as $choice) { ?>
                                                    <option value="<?php echo $choice['id'] ?>"
                                                            data-price="<?php echo $choice["choice_rate"]; ?>" <?php echo $choiceid == $choice['id'] ? "selected" : "" ?>>
                                                        <?php echo $choice['choice_name'] . ' (+$' . $choice["choice_rate"] . ')'; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        <?php } elseif ($option['option_type'] == 'single_option') { ?>
                                            <div class="singlechoice">
                                                <?php foreach ($option['choices'] as $choice) { ?>
                                                    <div class="radio inline-block">
                                                        <input id="<?php echo $choice['id'] ?>" type="radio"
                                                               class="radio quote_item_option"
                                                               name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][options][<?php echo $option['id']; ?>]"
                                                               data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>"
                                                               value="<?php echo $choice['id'] ?>"
                                                               data-price="<?php echo $choice["choice_rate"]; ?>" <?php echo $choiceid == $choice['id'] ? "checked " : " " ?> />
                                                        <label for="<?php echo $choice['id'] ?>"><?php echo $choice['choice_name'] . ' (+$' . $choice["choice_rate"] . ')'; ?></label>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } elseif ($option['option_type'] == 'multi_select') { ?>
                                            <div class="singlechoice">
                                                <?php foreach ($option['choices'] as $choice) { ?>
                                                    <div class="checkbox inline-block">
                                                        <input id="<?php echo $choice['id'] ?>" type="checkbox"
                                                               class="checkbox quote_item_option"
                                                               name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][options][<?php echo $option['id']; ?>][]"
                                                               data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>"
                                                               value="<?php echo $choice['id'] ?>"
                                                               data-price="<?php echo $choice["choice_rate"]; ?>" <?php echo is_array($choiceid) && in_array($choice['id'], $choiceid) ? "checked " : " " ?> />
                                                        <label for="<?php echo $choice['id'] ?>"><?php echo $choice['choice_name'] . ' (+$' . $choice["choice_rate"] . ')'; ?></label>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } elseif ($option['option_type'] == 'text_text_field') {
                                            ?>
                                            <div class="largetextfield">
                                                <div class="form-group">
                                                        <textarea type="text"
                                                                  name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][options][<?php echo $option['id']; ?>]"
                                                                  class="form-control"><?php echo $choiceid; ?></textarea>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="textfield">
                                                <div class="form-group">
                                                    <input type="text"
                                                           name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][options][<?php echo $option['id']; ?>]"
                                                           class="form-control" value="<?php echo $choiceid; ?>">
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if (strtolower($item_type) == "package") { ?>
                        <?php if (isset($description) && $description != "") { ?>
                            <div class="description">
                                <a href="javascript:void(0)" class="desc_title"
                                   data-pid="<?php echo "group_" . $quoteindex . " #quote_item_" . $qitems; ?>"><?php echo _l('Preview'); ?>
                                    <i
                                            class="fa fa-caret-down"></i></a>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <?php if (isset($description) && $description != "") { ?>
                            <div class="description">
                                <div class="desc_title"
                                     data-pid="<?php echo "group_" . $quoteindex . " #quote_item_" . $qitems; ?>"><?php echo _l('Description'); ?>
                                    <i
                                            class="fa fa-caret-down"></i></div>
                            </div>
                        <?php } ?>
                    <?php } ?>

                </div>
            </div>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 quantity qty-col <?php //echo in_array('qty', $vcols)?'package_col visibility_visible':'package_col' ?>">
            <div class="labelMob"><strong>Qty</strong></div>
            <input type="hidden" name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][id]"
                   value= <?php echo $id; ?>>
            <input type="hidden" name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][type]"
                   value= <?php echo $item_type; ?>>
            <input type="hidden" name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][mdiscount]"
                   value= <?php echo $omdiscoun; ?>>
            <input type="hidden" name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][mdistype]"
                   value= <?php echo $mdiscoun_calc; ?>>
            <input id="imdisval" type="hidden"
                   name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][mdisval]"
                   value= <?php echo $mdiscoun; ?>>
            <?php
            $readonly = "readonly";
            if ($gtype == 1 || $gtype == 2) {
                $readonly = strtolower($item_type) == 'package' ? 'readonly' : '';
                if (strtolower($item_type) == 'product' && $allow_client == 0) {
                    $readonly = "readonly";
                }
            } ?>
            <input class="spqqty viewqty<?php echo $readonly ?>" min="1"
                   max="<?php echo $maxqty ?>" <?php echo $readonly ?>
                   data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>" type="number"
                   data-gtype="<?php echo $gtype; ?>" data-group="<?php echo "#group_" . $quoteindex; ?>"
                   name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][qty]"
                   value="<?php echo $qty != "" ? $qty : 1; ?>" style="width: 50px;text-align: center;"/>
            <?php if ($allow_client == 1) { ?>
                <div class="clearfix"></div>
                <span><?php echo "Max." . $maxqty ?></span>
            <?php } ?>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 price  price-col"
             data-originlprice="<?php echo $originlprice ?>"
             data-price="<?php echo $price ?>" data-cextra=<?php echo isset($cextra) ? $cextra : 0.00 ?>>
            <div class="labelMob"><strong>Price</strong></div>
            <span class="psymbol"><?php echo number_format(($price + $cextra), 2, ".", ""); ?></span>
        </div>
        <?php if ($proposal->markups == 0 && $proposal->discounts == 0) {
            $colclass = "hide";
        } ?>
        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 <?php echo $colclass; ?>">
            <div class="labelMob"><strong>Markup/Disc.</strong></div>
            <div class="markup_disc price <?php echo $class ?>" data-mdiscount="<?php echo $omdiscoun; ?>"
                 data-mdistype="<?php echo $mdiscoun_calc; ?>" data-type="<?php echo $mdiscoun_type; ?>">
                <?php
                if ($mdiscoun_calc == "amount") {
                    if ($omdiscoun < 0) {
                        $omdiscoun = str_replace('-', '', $omdiscoun);
                        echo "-".format_money($omdiscoun);
                    }else{
                        echo format_money($omdiscoun);
                    }
                } else {
                    echo $omdiscoun . $omdiscoun_prefix;
                } ?>
            </div>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 tax-col <?php echo isset($item->is_taxable) && $item->is_taxable == 1 ? "taxable" : "" ?>"
             data-taxrate="<?php echo $tax_rate; ?>"
             data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>">
            <div class="labelMob"><strong>Tax</strong></div>
            <?php
            if (isset($item->is_taxable) && $item->is_taxable == 1) {
                if ($tax_val > 0) {
                    echo $tax_rate . "%";
                } else {
                    echo "<i class='fa fa-check'></i>";
                }
            } else {
                echo "---";
            }
            ?>
        </div>

        <div class="col-lg-1 col-md-2 col-sm-12 col-xs-12 qsubtotal qsubtotal-col text-right">
            <div class="labelMob"><strong>Subtotal</strong></div>
            <span class="psymbol"><?php echo $subtotal; ?></span>

        </div>
        <input class="isubtotal" type="hidden"
               name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][subtotal]"
               value= <?php echo $subtotal; ?>>
        <input class="itax" type="hidden"
               name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][tax]"
               value= <?php echo $tax_val; ?>>
        <input class="imkpdisc" type="hidden"
               name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][mkpdisc]"
               value= <?php echo $mdiscoun; ?>>

        <!--<div class="col-sm-1 action-btns"></div>-->
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-sm-3"></div>
            <div class="col-sm-9 desc_inner" style="display: none"><?php echo strip_tags($description); ?></div>
        </div>
    </div>
</div>