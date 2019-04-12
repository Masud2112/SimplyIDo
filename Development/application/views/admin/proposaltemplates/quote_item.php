<?php
$tax_rate = 0;
$tax_val = 0;
if (isset($proposal) && $proposal->proposal_custom_tax > 0) {
    $ptax = get_tax_rate_by_id($proposal->proposal_custom_tax);
    $tax_rate = $ptax->taxrate;
} elseif (isset($proposal_custom_tax)) {
    $ptax = get_tax_rate_by_id($proposal_custom_tax);
    $tax_rate = !empty($ptax) ? $ptax->taxrate : 0;
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

$qty = (isset($qty) && $qty > 0 ? $qty : 1);
$mdiscoun = (!empty($mdiscoun) ? $mdiscoun : 0);
$omdiscoun = $mdiscoun;
$omdiscoun_val = $mdiscoun;
if (empty($mdiscoun)) {
    $mdiscoun = 0;
}
if (isset($mdiscoun_calc) && $mdiscoun_calc == "percentage") {
    $mdiscoun = ($price * $mdiscoun) / 100;
}

$subtotal = ($price + $mdiscoun) * $qty;

if (isset($item->is_taxable) && $item->is_taxable == 1 && $tax_rate > 0) {
    $subtotal = $subtotal + (($subtotal * $tax_rate) / 100);
    $tax_val = ($subtotal * $tax_rate) / 100;
}
$subtotal = number_format($subtotal, 2);
if (!isset($mdiscoun_type)) {
    $mdiscoun_type = "discount";
}
if (!isset($mdiscoun_calc)) {
    $mdiscoun_calc = "percentage";
}
$class = "";
if ($mdiscoun_type == "discount") {
    $class = "danger";
    if ($mdiscoun_calc == "amount") {
        $omdiscoun = $omdiscoun . "$";
    } else {
        $omdiscoun = $omdiscoun . "%";
    }
} else {
    if (isset($mdiscoun_calc) && $mdiscoun_calc == "amount") {
        $omdiscoun = $omdiscoun . "$";
    } else {
        $omdiscoun = $omdiscoun . "%";
    }

}
?>
<?php if ($qitems == 0) { ?>


<?php } ?>
<div id="<?php echo "quote_item_" . $qitems; ?>" class="quote_item ui-sortable-handle">
    <div class="row qiMobUI">
        <div class="col-sm-6 qiMobUI-col1">
            <div class="row ">
                <div class="col-sm-1 col-xs-1 drag_mob">
                    <?php
                    if (!isset($_GET['preview'])) { ?>
                        <div class="drag-icon"><i class="fa fa-bars"></i></div><?php } ?></div>
                <div class="col-sm-1 hide_mob">
                    <?php
                    /*$opname = "";
                    if ($gtype == 1) {
                        if (isset($_GET['preview']) && $_GET['preview'] == true) {
                            $opname = "selected_one";
                        }
                        echo '<input type="radio" name="' . $opname . '">';
                    } elseif ($gtype == 2) {
                        if (isset($_GET['preview']) && $_GET['preview'] == true) {
                            $opname = "selected_any";
                        }
                        echo '<div class="checkbox">
                                    <input type="checkbox" name="' . $opname . '[]" id="quote_item_check' . $qitems . '">
                                    <label for="quote_item_check' . $qitems . '"></labelfor>
                                  </div>';
                    } else {
                        echo "";
                    }*/
                    ?>
                </div>
                <div class="col-sm-3 col-xs-3 itmImg_mob">
                    <div class="item_image">
                        <?php echo $image; ?>
                        <?php if (isset($lable) && $lable != "") { ?><span><?php echo $lable; ?></span> <?php } ?>
                    </div>
                </div>
                <div class="col-sm-7 col-xs-8 itemDet_mob">
                    <h4><?php echo $name ?></h4>
                    <h6><?php echo $sku ?></h6>
                    <?php if (isset($item->item_options) && count($item->item_options) > 0) { ?>
                        <div class="options">
                            <div class="opt_title"><?php echo _l('options'); ?><i class="fa fa-caret-down"></i></div>
                            <div class="options_list row" style="display: none;">
                                <div class="dropdowns_main">
                                    <?php foreach ($item->item_options as $option) { ?>
                                        <?php if ($option['option_type'] == 'dropdown') { ?>
                                            <div class="dropdown">
                                                <div>
                                                    <strong><?php echo ucfirst($option['option_name']); ?></strong>
                                                </div>
                                                <select class="selectpicker quote_item_option form-control" <?php echo $option['option_type'] == 'multi_select' ? "multiple" : ""; ?>>
                                                    <option value="" data-price="0">Select Choice</option>
                                                    <?php foreach ($option['choices'] as $choice) { ?>
                                                        <option value="<?php echo $choice['id'] ?>"
                                                                data-price="<?php echo $choice["choice_rate"]; ?>" <?php //echo $choiceid == $choice['id'] ? "selected" : "" ?>>
                                                            <?php echo $choice['choice_name'] . ' (+$' . $choice["choice_rate"] . ')'; ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        <?php }
                                    } ?>
                                </div>
                                <div class="text_fields_main">
                                    <?php foreach ($item->item_options as $option) { ?>
                                        <?php if ($option['option_type'] == 'text_field') { ?>
                                            <div class="text_field">
                                                <div>
                                                    <strong><?php echo ucfirst($option['option_name']); ?></strong>
                                                </div>
                                                <div class="text_field">
                                                    <div class="form-group">
                                                        <input type="text"
                                                               name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][options][<?php echo $option['id']; ?>]"
                                                               class="form-control" value="">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }
                                    } ?>
                                </div>
                                <div class="text_text_fields_main">
                                    <?php foreach ($item->item_options as $option) { ?>
                                        <?php if ($option['option_type'] == 'text_text_field') { ?>
                                            <div class="text_text_field">
                                                <div>
                                                    <strong><?php echo ucfirst($option['option_name']); ?></strong>
                                                </div>
                                                <div class="largetextfield">
                                                    <div class="form-group-textarea">
                                                        <textarea type="text" name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][options][<?php echo $option['id']; ?>]" class="form-control"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }
                                    } ?>
                                </div>
                                <div class="single_options_main">
                                    <?php foreach ($item->item_options as $option) {
                                        if ($option['option_type'] == 'single_option') { ?>
                                            <div class="single_option">
                                                <div>
                                                    <strong><?php echo ucfirst($option['option_name']); ?></strong>
                                                </div>
                                                <div class="singlechoice">
                                                    <?php foreach ($option['choices'] as $choice) { ?>
                                                        <div class="radio inline-block">
                                                            <input id="<?php echo $choice['id'] ?>" type="radio"
                                                                   class="radio quote_item_option"
                                                                   name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][options][<?php echo $option['id']; ?>]"
                                                                   data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>"
                                                                   value="<?php echo $choice['id'] ?>"
                                                                   data-price="<?php echo $choice["choice_rate"]; ?>" <?php //echo $choiceid == $choice['id'] ? "checked " : " " ?> />
                                                            <label for="<?php echo $choice['id'] ?>"><?php echo $choice['choice_name'] . ' (+$' . $choice["choice_rate"] . ')'; ?></label>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php }
                                    } ?>
                                </div>
                                <div class="multi_selects_main">
                                    <?php foreach ($item->item_options as $option) {
                                        if ($option['option_type'] == 'multi_select') { ?>
                                            <div class="multi_select">
                                                <div>
                                                    <strong><?php echo ucfirst($option['option_name']); ?></strong>
                                                </div>
                                                <div class="singlechoice">
                                                    <?php foreach ($option['choices'] as $choice) { ?>
                                                        <div class="checkbox inline-block">
                                                            <input id="<?php echo $choice['id'] ?>" type="checkbox"
                                                                   class="checkbox quote_item_option"
                                                                   name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][options][<?php echo $option['id']; ?>][]"
                                                                   data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>"
                                                                   value="<?php echo $choice['id'] ?>"
                                                                   data-price="<?php echo $choice["choice_rate"]; ?>" <?php //echo is_array($choiceid)&&in_array($choice['id'],$choiceid)  ? "checked " : " " ?> />
                                                            <label for="<?php echo $choice['id'] ?>"><?php echo $choice['choice_name'] . ' (+$' . $choice["choice_rate"] . ')'; ?></label>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?php }
                                    } ?>
                                </div>
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
                                    <i class="fa fa-caret-down"></i></div>
                            </div>
                        <?php } ?>
                    <?php } ?>

                </div>
            </div>
        </div>
        <div class="col-sm-1 qiMobUI-col2 quantity qty_mob qty-col <?php //echo in_array('qty', $vcols)?'package_col visibility_visible':'package_col' ?>">
            <div class="mobLabel">Qty.:</div>
            <input type="hidden" name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][id]"
                   value= <?php echo $id; ?>>
            <input type="hidden" name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][type]"
                   value= <?php echo $item_type; ?>>
            <?php
            $readonly = "readonly";
            /*if ($gtype == 1 || $gtype == 2) {*/
            $readonly = strtolower($item_type) == 'package' ? 'readonly' : '';
            /*}*/ ?>
            <input class="pqqty num_js <?php echo $readonly ?>" min="1" <?php echo $readonly ?>
                   data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>" type="text"
                   name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][qty]"
                   value="<?php echo $qty != "" ? $qty : 1; ?>" style="width: 50px;text-align: center;">
            <?php if (($gtype == 1 || $gtype == 2) && strtolower($item_type) == 'product') { ?>
                <i class="fa fa-caret-down"
                   data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>"></i>
            <?php } ?>
        </div>
        <div class="col-sm-1 qiMobUI-col3 price_mob  price-col <?php //echo in_array('price', $vcols)?'package_col visibility_visible':'package_col' ?> ">
            <div class="mobLabel">Price:</div>
            <div class="psymbol price"><?php echo $price ?></div>
        </div>
        <div class="col-sm-1 qiMobUI-col4 disc_mob">
            <div class="mobLabel">Markup/Disc.:</div>
            <?php
            if (isset($_GET['preview']) && $_GET['preview'] == true) {
                ?>
                <div class="price <?php echo $class; ?>"><?php echo $omdiscoun ? $omdiscoun : "0"; ?></div>
            <?php } else { ?>
                <div class="markup_disc price <?php echo $class; ?>"
                     data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>">
                    <span><?php echo $omdiscoun ? $omdiscoun : "0"; ?></span>
                    <i class="fa fa-caret-down"></i>
                </div>
            <?php } ?>

        </div>
        <div class="col-sm-1 tax_mob qiMobUI-col5 tax-col <?php echo isset($item->is_taxable) && $item->is_taxable == 1 ? "taxable" : "" ?>"
             data-taxrate="<?php echo $tax_rate; ?>"
             data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>">
            <div class="mobLabel">Tax:</div>
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

        <div class="col-sm-1 qiMobUI-col6 subTot_mob   qsubtotal-col <?php //echo in_array('profit', $vcols)?'package_col visibility_visible':'package_col' ?>">
            <div class="mobLabel">Subtotal</div>
            <div class="psymbol qsubtotal"><?php echo $subtotal; ?></div>
        </div>
        <div class="col-sm-1 qiMobUI-col7 action_mob action-btns">
            <div class="mobLabel"></div>
            <?php
            if (!isset($_GET['preview'])) {
                ?>
                <div class='text-right mright10'><a class='show_act' href='javascript:void(0)'><i
                                class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div>
                <div class='table_actions'>
                    <ul>
                        <li><a href="javascript:void(0)" class="quote_item_remove"
                               data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>"
                               data-itemid="<?php echo $item_type . "_" . $id; ?>">
                                <i class="fa fa-close"></i>Delete
                            </a></li>
                    </ul>
                </div>
            <?php } ?>
        </div>
        <div class="clearfix"></div>

        <div class="proposalQuoteDesc_blk">
            <div class="col-sm-12 desc_inner" style="display: none"><?php echo strip_tags($description); ?></div>
        </div>

        <div class="mkpdiosc_container">
            <div class="discount_blk">
                <div class="discountTitle_blk">
                    <h5><span><?php echo _l('markup_discount_setting') ?></span></h5>
                    <a href="javascript:void(0)"
                       data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>"
                       class="markup_disc_remove"><i class="fa fa-remove"></i></a>
                </div>
                <div class="discount_list_blk">
                    <div class="form-group discount_list_inner_blk">
                        <label class="pull-left">Type</label>
                        <select class="form-control mdiscount_type selectpicker"
                                data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>"
                                name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][mdiscoun_type]"
                                value='<?php echo $omdiscoun ? $omdiscoun : ""; ?>'
                                style="width: 100%;text-align: center;">
                            <option value="discount" <?php echo isset($mdiscoun_type) && $mdiscoun_type == "discount" ? "selected" : "" ?> >
                                Discount
                            </option>
                            <option value="markup" <?php echo isset($mdiscoun_type) && $mdiscoun_type == "markup" ? "selected" : "" ?> >
                                Markup
                            </option>
                        </select>
                    </div>
                    <div class="form-group discount_list_inner_blk">
                        <label class="pull-left">Calculation</label>
                        <select class="form-control mdiscount_calc selectpicker"
                                data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>"
                                name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][mdiscoun_calc]"
                                value='<?php echo $omdiscoun ? $omdiscoun : ""; ?>'
                                style="width: 100%;text-align: center;">
                            <option value="percentage" <?php echo isset($mdiscoun_calc) && $mdiscoun_calc == "percentage" ? "selected" : "" ?> >
                                Percentage
                            </option>
                            <option value="amount" <?php echo isset($mdiscoun_calc) && $mdiscoun_calc == "amount" ? "selected" : "" ?> >
                                Fixed Amount
                            </option>
                        </select>
                    </div>
                    <div class="form-group discount_list_inner_blk mdiscoun_field">
                        <label class="pull-left"><?php echo isset($mdiscoun_type) && $mdiscoun_type == "markup" ? "Markup" : "Discount" ?></label>
                        <!-- <input type="text"
                               class="mdiscoun <?php echo isset($mdiscoun_type) && $mdiscoun_type == "markup" ? "markup" : "discount" ?> form-control text-center <?php echo isset($mdiscoun_calc) && $mdiscoun_calc == "amount" ? "amount" : "percentage" ?>"
                               data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>"
                               name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][mdiscoun]"
                               value='<?php echo $omdiscoun_val ? $omdiscoun_val : ""; ?>' style="width: 100%;">
                        <span class="mdiscoun_suffix "><?php echo isset($mdiscoun_calc) && $mdiscoun_calc == "amount" ? "$" : "%" ?></span> -->
                        <div class="clearfix"></div>
                        <div class="input-group mdiscoun-input-group">
                            <span class="input-group-addon mdiscoun_prefix" style="display: none">$</span>
                            <input type="text"
                                   class="mdiscoun <?php echo isset($mdiscoun_type) && $mdiscoun_type == "markup" ? "markup" : "discount" ?> form-control text-center <?php echo isset($mdiscoun_calc) && $mdiscoun_calc == "amount" ? "amount" : "percentage" ?>"
                                   data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>"
                                   name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][mdiscoun]"
                                   value='<?php echo $omdiscoun_val ? $omdiscoun_val : ""; ?>' style="width: 100%;">
                            <span class="input-group-addon mdiscoun_suffix">%</span>
                        </div>
                    </div>
                    <div class="form-group discount_list_inner_blk">
                        <label class="pull-left">Apply to</label>
                        <select class="form-control mdiscount_apply selectpicker"
                                data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>"
                                data-gid="<?php echo "#group_" . $quoteindex; ?>"
                                value='<?php echo $omdiscoun ? $omdiscoun : ""; ?>'
                                style="width: 100%;text-align: center;">
                            <option value="this"> This item only</option>
                            <option value="all_g"> All item in group</option>
                            <option value="all_q"> All item in quote</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <?php if (/*($gtype == 1 || $gtype == 2) && */
            strtolower($item_type) == 'product') { ?>
            <div class="maxQtyContainer">
                <div class="discount_blk">
                    <div class="discountTitle_blk">
                        <h5><span><?php echo _l('qty_setting') ?></span></h5>
                        <a href="javascript:void(0)"
                           data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>"
                           class="quantity_remove"><i class="fa fa-remove"></i></a>
                    </div>
                    <div class="discount_list_blk">
                        <div class="form-group discount_list_inner_blk maxqty_field">
                            <input type="number" class="display-block text-center maxqty"
                                   data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>"
                                   name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][maxqty]"
                                   value='<?php echo isset($maxqty) && $maxqty > 0 ? $maxqty : 1; ?>' min="1">
                            <label class="display-block maxqty_label"><?php echo "Max.Qty." ?></label>
                        </div>
                        <div class="checkbox">
                            <input id="allowClient" type="checkbox" class="checkbox allow_client form-control"
                                   data-pid="<?php echo "#group_" . $quoteindex . " #quote_item_" . $qitems; ?>"
                                   name="group[<?php echo $quoteindex; ?>][item][<?php echo $qitems; ?>][allow_client]"
                                   value=1 <?php echo isset($allow_client) && $allow_client == 1 ? "checked" : "" ?> >
                            <label for="allowClient">
                                <?php echo "Allow client to modify quantity." ?>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
