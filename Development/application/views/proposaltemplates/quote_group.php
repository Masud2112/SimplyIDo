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
    $msg = _l('choose_one');
} elseif ($gtype == 2) {
    $type = "(Choose <b><i><u>ANY</u></i></b>)";
    $msg = _l('choose_any');
} else {
    $type = "";
    $msg = _l('no_choice');
}
$class = isset($quote_items) && count($quote_items) > 0 ? "" : "hidden";
?>
<div id="group_<?php echo $gid; ?>" class="mbot10 group">
    <div class="group_header">
        <div class="p9 boder1">
            <h3 class="inline-block no-mbot no-mtop group_title"><?php echo $gname ?></h3>
            <span><?php echo $type; ?></span>
            <input type="hidden" name="group[<?php echo $gid; ?>][gid]" value="<?php echo isset($qid) ? $qid : '' ?>"
                   class="quote_id">
            <!--<input type="hidden" name="group[<?php /*echo $gid; */ ?>][gname]" value="<?php /*echo $gname */ ?>"
                   class="quote_name">-->
            <input type="hidden" name="group[<?php echo $gid; ?>][gtype]" value="<?php echo $gtype ?>"
                   class="quote_type">
            <!--<input type="hidden" name="group[<?php /*echo $gid; */ ?>][quote_order]"
                   value="<?php /*echo isset($quote_order) ? $quote_order : 0 */ ?>" class="quote_order">-->
            <a href="javascript:void(0)" data-pid="group_<?php echo $gid; ?>" class="float-right exp_clps"><i
                        class="fa fa-caret-up"></i></a>
            <?php /*if(isset($qid) && $qid > 0){ */ ?>
        </div>
    </div>
    <div class="group_inner">
        <div class="ghead_msg p9">
            <p class="no-mbot"><?php echo $msg; ?></p>
        </div>
        <div class="group_body">
            <div class="quote_items_header <?php echo $class; ?>">
                <div class="row header">
                    <div class="col-xs-12">
                    <!-- <div class="col-md-1"></div> -->
                        <?php if ($proposal->markups == 0 && $proposal->discounts == 0) {
                            $cols_class = "col-lg-8 col-md-7 col-sm-12";
                        } else {
                            $cols_class = "col-lg-7 col-md-6  col-sm-12";
                        } ?><div class="<?php echo $cols_class; ?>">
                            <span class="nameTxt">Name</span></div>
                        <div class="col-lg-1 col-md-1 col-sm-12 qty-col <?php //echo in_array('qty', $vcols)?'package_col visibility_visible':'package_col' ?>">
                            Qty.
                        </div>
                        <!-- <div class="col-md-1 cost-col <?php // echo in_array('cost', $vcols)?'package_col visibility_visible':'package_col' ?>">Cost</div> -->
                        <div class="col-lg-1 col-md-1 col-sm-12 price-col <?php //echo in_array('price', $vcols)?'package_col visibility_visible':'package_col' ?>">
                            Price
                        </div>
                        <?php if ($proposal->markups == 1 || $proposal->discounts == 1) { ?>

                            <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 mark_disc-col <?php //echo in_array('subtotal', $vcols)?'package_col visibility_visible':'package_col' ?>">
                                <?php if ($proposal->markups == 1 && $proposal->discounts == 0) {
                                        echo "Markup";
                                }elseif ($proposal->markups == 0 && $proposal->discounts == 1) {
                                    echo "Discount";
                                }else{
                                    echo "Markup/Disc.";
                                }?>
                            </div>
                        <?php } ?>
                        <div class="col-lg-1 col-md-1 col-sm-12 col-xs-12 tax-col <?php // echo in_array('tax', $vcols)?'package_col visibility_visible':'package_col' ?>">
                            Tax
                        </div>
                        <div class="col-lg-1 col-md-2 col-sm-12 col-xs-12 text-right subtotal-col <?php //echo in_array('profit', $vcols)?'package_col visibility_visible':'package_col' ?>">
                            Subtotal
                        </div>
                        <!--<div class="col-md-1 action-col">Action</div>-->
                    </div>
                    </div>

                </div>
                <div class="quote_items">
                    <?php
                    if (isset($quote_items) && count($quote_items) > 0) {
                        foreach ($quote_items as $key => $quote_item) {
                            if (strtolower($quote_item['type']) == 'package') {
                                $item = $this->invoice_items_model->get_group($quote_item['id']);
                            } else {
                                $item = $this->invoice_items_model->get_item($quote_item['id']);
                            }
                            if($gtype==0){
                                $quote_item['maxqty']=$quote_item['qty'];
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
                            $data['maxqty'] = isset($quote_item['maxqty'])?$quote_item['maxqty']:1;
                            $data['allow_client'] = isset($quote_item['allow_client'])?$quote_item['allow_client']:0;
                            $this->load->view('proposaltemplates/quote_item', $data);
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>