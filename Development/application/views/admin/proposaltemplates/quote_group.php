<?php
/**/
$items = array_merge($packages, $items);
if (isset($quote) && count($quote) > 0) {
    $gtype = $quote['quote_type'];
    $gname = $quote['quote_name'];
    $qid = $quote['qid'];
    $gid = $quote['gid'];
    $quote_items = json_decode($quote['quote_items'], true);
    $quote_order = $quote['quote_order'];
    if (!empty($quote_items)) {
        $quote_items = array_values($quote_items);
    }
}
if ($gtype == 1) {
    $type = "(Choose <b><i><u>ONE</u></i></b> Only)";
    $msg = _l('choose_one_client');
} elseif ($gtype == 2) {
    $type = "(Choose <b><i><u>ANY</u></i></b>)";
    $msg = _l('choose_any_client');
} else {
    $type = "";
    $msg = _l('no_choice_client');
}
$class = isset($quote_items) && count($quote_items) > 0 ? "" : "hidden";
?>
<div id="group_<?php echo $gid; ?>" class="mbot10 group">
    <div class="group_header">
        <div class="p9 boder1 movable-block">
            <h3 class="inline-block no-mbot no-mtop group_title"><?php echo $gname ?></h3>
            <span><?php echo $type; ?></span>
            <input type="hidden" name="group[<?php echo $gid; ?>][gid]" value="<?php echo isset($qid) ? $qid : '' ?>"
                   class="quote_id">
            <input type="hidden" name="group[<?php echo $gid; ?>][gname]" value="<?php echo $gname ?>"
                   class="quote_name">
            <input type="hidden" name="group[<?php echo $gid; ?>][gtype]" value="<?php echo $gtype ?>"
                   class="quote_type">
            <input type="hidden" name="group[<?php echo $gid; ?>][quote_order]"
                   value="<?php echo isset($quote_order) ? $quote_order : 0 ?>" class="quote_order">
            <a href="javascript:void(0)" data-pid="group_<?php echo $gid; ?>" class="pull-right exp_clps"><i
                        class="fa fa-caret-up"></i></a>
            <?php /*if(isset($qid) && $qid > 0){ */ ?>
            <?php
            if (!isset($_GET['preview'])) {
                ?>
                <div class="pull-right">
                    <div><a class='show_act mright10' href='javascript:void(0)'><i class='fa fa-ellipsis-v'
                                                                                   aria-hidden='true'></i></a></div>
                    <div class='table_actions'>
                        <ul>
                            <li><a href="javascript:void(0)" class="quote_group_edit"
                                   data-pid="#group_<?php echo $gid; ?>"
                                   data-qid= <?php echo isset($qid) ? $qid : ""; ?> id="edit_group" data-toggle="modal"
                                   data-target="#edit_group_popup_<?php echo $gid; ?>"><i
                                            class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>
                            <?php if (isset($qid) && $qid > 0) { ?>
                                <li><a href="javascript:void(0)"
                                       class="quote_group_copy"
                                       data-pid="#group_<?php echo $gid; ?>"
                                       data-qid= <?php echo isset($qid) ? $qid : ""; ?>>
                                    <i class="fa fa-clone"></i><span>Clone</span></a></li><?php } ?>
                            <li><a href="javascript:void(0)" class="quote_group_delete"
                                   data-pid="#group_<?php echo $gid; ?>"
                                   data-qid= <?php echo isset($qid) ? $qid : ""; ?>><i class="fa fa-remove"></i><span>Delete</span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        </div>
        <!--<div class="setting-btn"><a href="#" class="btn-info" data-toggle="modal"
                                    data-target="#display_column" id="display_column_popup"><i
                        class="fa fa-cog" aria-hidden="true"></i></a></div>-->
    </div>
    <div class="group_inner">
        <div class="ghead_msg p9">
            <p class="no-mbot"><?php echo $msg; ?></p>
        </div>
        <div class="group_body">
            <div class="quote_items_header <?php echo $class; ?>">
                <div class="row header">
                    <!-- <div class="col-md-1"></div> -->
                    <div class="col-md-6"><span class="nameTxt">Name</span></div>
                    <div class="col-md-1 qty-col <?php //echo in_array('qty', $vcols)?'package_col visibility_visible':'package_col' ?>">
                        Qty.
                    </div>
                    <!-- <div class="col-md-1 cost-col <?php // echo in_array('cost', $vcols)?'package_col visibility_visible':'package_col' ?>">Cost</div> -->
                    <div class="col-md-1 price-col <?php //echo in_array('price', $vcols)?'package_col visibility_visible':'package_col' ?>">
                        Price
                    </div>
                    <div class="col-md-1 mark_disc-col <?php //echo in_array('subtotal', $vcols)?'package_col visibility_visible':'package_col' ?>">
                        Markup/Disc.
                    </div>
                    <div class="col-md-1 tax-col <?php // echo in_array('tax', $vcols)?'package_col visibility_visible':'package_col' ?>">
                        Tax
                    </div>
                    <div class="col-md-1 subtotal-col <?php //echo in_array('profit', $vcols)?'package_col visibility_visible':'package_col' ?>">
                        Subtotal
                    </div>
                    <div class="col-md-1 action-col"></div>
                </div>
            </div>
            <div class="quote_items <?php
            if (!isset($_GET['preview'])) {
                echo 'sortable ui-sortable';
            }
            ?>">
                <?php
                if (isset($quote_items) && count($quote_items) > 0) {
                    foreach ($quote_items as $key => $quote_item) {
                        if (strtolower($quote_item['type']) == 'package') {
                            $item = $this->invoice_items_model->get_group($quote_item['id']);
                        } else {
                            $item = $this->invoice_items_model->get($quote_item['id']);
                        }
                        $data['item_type'] = $quote_item['type'];
                        $data['item'] = $item;
                        $data['quoteindex'] = $gid;
                        $data['qitems'] = $key;
                        $data['qty'] = $quote_item['qty'];
                        $data['mdiscoun'] = $quote_item['mdiscoun'];
                        $data['mdiscoun_type'] = isset($quote_item['mdiscoun_type']) ? $quote_item['mdiscoun_type'] : "discount";
                        $data['mdiscoun_calc'] = isset($quote_item['mdiscoun_calc']) ? $quote_item['mdiscoun_calc'] : "amount";
                        $data['gtype'] = $gtype;
                        $data['gname'] = $gname;
                        $data['maxqty'] = isset($quote_item['maxqty']) ? $quote_item['maxqty'] : 1;
                        $data['allow_client'] = isset($quote_item['allow_client']) ? $quote_item['allow_client'] : 0;
                        $this->load->view('admin/proposaltemplates/quote_item', $data);
                    }
                } ?>
            </div>
        </div>
        <?php
        if (!isset($_GET['preview'])) {
            ?>
            <div class="group_footer">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="additemgroupTxt"><?php echo _l('add_item_to_pgroup'); ?></div>
                    </div>
                    <div class="col-sm-6 text-right ">

                        <a href="javascript:void(0)" class="mtop10 mbot10 btn btn-info add_item_group"
                           data-pid="group_<?php echo $gid; ?>">
                            <i class="fa fa-plus-square"></i> TEMPLATE ITEM
                        </a>
                        <a href="#" class="mtop10 mbot10 btn btn-info add_manual_item_group"
                           data-pid="#group_<?php echo $gid; ?>">
                            <i class="fa fa-plus-square"></i> MANUAL ITEM
                        </a>
                        <?php /*if (isset($qid) && $qid > 0) {
                            $url = "invoice_items/item?pid=" . $quote['proposal_id'] . "&qid=" . $qid;
                            */ ?><!--
                            <a href="<?php /*echo admin_url($url); */ ?>"
                               class="mtop10 mbot10 btn btn-info add_manual_item_group"
                               data-pid="group_<?php /*echo $gid; */ ?>">
                                <i class="fa fa-plus-square"></i> ADD MANUAL ITEM
                            </a>
                        <?php /*}else{ */ ?>
                            <button type="submit" name="add_manual_item" class="mtop10 mbot10 btn btn-info add_manual_item_group" value="group_<?php /*echo $gid; */ ?>">
                                <i class="fa fa-plus-square"></i> ADD MANUAL ITEM
                            </button>
                        --><?php /*} */ ?>

                    </div>
                </div>
                <div class="row">
                    <div class="ps_pkg_container">
                        <div class="ps_pkg_inner">
                            <div class=" ps_kg_header">
                                <div class="col-sm-4">
                                    <h6 class="titleH6">Add Template Items <i>(click to select)</i></h6>
                                </div>
                                <div class="col-sm-8 text-right">
                                    <label>Search</label>
                                    <input type="text" name="ps_pkg_search" class="ps_pkg_search"
                                           data-pid="group_<?php echo $gid; ?>">

                                    <div class="ps_pkg_filter">
                                        <a href="javascript:void(0)" data-pid="group_<?php echo $gid; ?>"
                                           data-filter="">All</a>
                                        <a href="javascript:void(0)" data-pid="group_<?php echo $gid; ?>"
                                           data-filter="product">Packages</a>
                                        <a href="javascript:void(0)" data-pid="group_<?php echo $gid; ?>"
                                           data-filter="package">Items</a>
                                    </div>
                                </div>
                            </div>
                            <div class="row pt15 ps_pkg_items">
                                <?php
                                foreach ($items as $key => $item) {

                                    if (isset($item['group_sku'])) {
                                        $id = $item['id'];
                                        $sku = $item['group_sku'];
                                        $image = group_image($id, array('item-profile-image-product_services_package_image'), 'thumb');
                                        $name = $item['name'];
                                        $price = $item['group_price'];
                                        $description = $item['group_description'];
                                        $item_type = "Package";
                                    } else {
                                        $id = $item['itemid'];
                                        $sku = $item['sku'];
                                        $image = line_item_image($id, array('item-profile-image-product_services_package_image'), 'thumb');
                                        $name = $item['description'];
                                        $price = $item['rate'];
                                        $description = $item['long_description'];
                                        $item_type = "";
                                    }
                                    $data_class = $item_type != "" ? strtolower($item_type) . "_" . $id : 'product' . "_" . $id;
                                    $disabled = "";
                                    if (isset($selected_items) && in_array($data_class, $selected_items)) {
                                        $disabled = "disabled";
                                    }
                                    ?>
                                    <div id="item_<?php echo $key; ?>"
                                         class="col-sm-4 ps_pkg_item <?php echo $data_class . ' ' . $disabled ?> <?php echo $item_type != "" ? strtolower($item_type) : 'product' ?>"
                                         data-type="<?php echo $item_type != "" ? strtolower($item_type) : 'product' ?>"
                                         data-id="<?php echo $id; ?>" data-class="<?php echo $data_class ?>"
                                         data-title="<?php echo $name; ?>">
                                        <div class="pakagesItems">
                                            <div class="col-xs-3">
                                                <div class="item_image"><?php echo $image; ?></div>
                                            </div>
                                            <div class="col-xs-9">
                                                <h3 class="mtop0 item_title"><?php echo $name; ?></h3>
                                                <h5 class="item_sku"><?php echo !empty($sku) ? $sku : ""; ?>
                                                    <?php if (strtolower($item_type) == 'package') { ?>
                                                        <span class="item_type"><?php echo $item_type; ?></span>
                                                    <?php } ?>
                                                </h5>
                                                <h5 class="item_price"><?php echo "$" . $price; ?></h5>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="addBtn">
                            <a href="javascript:void(0)" class="btn btn-info add_item_topr"
                               data-pid="#group_<?php echo $gid; ?>">ADD</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="modal fade" id="edit_group_popup_<?php echo $gid; ?>" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('add_group'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="group_popup">
                        <div class="group_name">
                            <div class="form-group">
                                <label>Group Name
                                    <small class="req text-danger">*</small>
                                </label>
                                <input type="text" name="group_name" class="form-control gname"
                                       value="<?php echo $gname; ?>"/>
                                <input type="hidden" name="gid" value="<?php echo isset($qid) ? $qid : '' ?>"
                                       class="quote_id">
                            </div>
                        </div>
                        <div class="group_type">
                            <div class="form-group">
                                <label>Group Type
                                    <small class="req text-danger">*</small>
                                </label>
                                <select class="form-control gtype selectpicker" name="group_type">
                                    <option value="">Select group type</option>
                                    <option <?php echo $gtype == 0 ? "selected" : ""; ?> value="0">Pre-Selected</option>
                                    <option <?php echo $gtype == 1 ? "selected" : ""; ?> value="1">Select One</option>
                                    <option <?php echo $gtype == 2 ? "selected" : ""; ?> value="2">Select Any</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" class="btn btn-info group_save" id="edit_group_save"
                   data-gid="<?php echo $gid; ?>">
                    <?php echo _l('submit'); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
//$this->load->view('admin/proposaltemplates/quote_group_options');
?>
