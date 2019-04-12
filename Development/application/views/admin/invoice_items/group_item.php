<?php

if(isset($product)){
    $item = $product;
}

if(isset($item)){
    $itemid = $item->itemid;
    $taxid = $item->taxid;
    $name = $item->description;
    $image = $item->profile_image;
    $sku = $item->sku;
    $cost = $item->cost_price;
    $description = $item->long_description;
//$taxrate = $item->taxrate;
    $rate = $item->rate;
    if(isset($item->qty) && $item->qty > 0){
        $qty = $item->qty;
    }else{
        $qty = 1;
    }
    if(isset($item->subtotal) && $item->subtotal > 0){
        $subtotal = $item->subtotal;
    }else{
        $subtotal = $rate*$qty;
    }
    $profit = $item->profit*$qty;
    $is_taxable = $item->is_taxable;
    if($is_taxable > 0 ){
        $readonly = "";
        $taxrate = $item->taxrate;
    }else{
        $taxrate = "";
        $readonly = "readonly";
    }
    if(!isset($vcols)){
        $vcols = array('qty','cost','subtotal','price','tax','profit' );
    }
    ?>
    <div id = "<?php echo "id-".$itemid; ?>" class="package_item">
        <div class="row">
            <div class="col-xs-1"><div class="drag-icon"><i class="fa fa-bars"></i></div></div>
            <!-- <div class="col-sm-1"><i class="fa fa-bars"></i></div> -->
            <div class="col-xs-4">
                <div class="row">
                    <div class="col-xs-3">
                        <div class="item_image">
                            <?php echo line_item_image($itemid,array('profile_image','img-responsive','item-profile-image-thumb'),'thumb'); ?>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <h4><?php echo $name?></h4>
                        <h6><?php echo $sku?></h6>
                        <?php if(isset($item->item_options) && count($item->item_options) > 0){ ?>
                            <div class="options">
                                <div class="opt_title"><?php echo _l('options');?></div>
                                <div class="options_list row" style="display: none;">
                                    <?php foreach ($item->item_options as $option){ ?>
                                        <div class="col-xs-12">
                                            <label>
                                                <?php echo _l($option['option_type'])." : ";?>
                                                <?php echo _l($option['option_name']);?>
                                            </label>
                                            <?php if($option['option_type']=='dropdown' || $option['option_type'] == 'single_option' || $option['option_type'] == 'multi_select') { ?>
                                                <select class="form-control">
                                                    <option value="">Choice</option>
                                                    <?php foreach ($option['choices'] as $choice) { ?>
                                                        <option value="<?php echo $choice['id'].'_'.$choice['choice_rate'].'_'.$choice['choice_profit'].'_'.$choice['choice_cost_price']?>"><?php echo $choice['choice_name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if(isset($description) && $description !="") {?>
                            <div class="description">
                                <div class="desc_title" data-pid = "<?php echo "id-".$itemid; ?>"><?php echo _l('Description');?></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-xs-1 quantity qty-col <?php echo in_array('qty', $vcols)?'package_col visibility_visible':'package_col' ?>">
                <input class="qty" data-pid = "<?php echo "id-".$itemid; ?>" type="number" name="item[<?php echo $itemid ; ?>][qty]" value="<?php echo $qty ;?>" style="width: 50px;text-align: center;">
            </div>
            <div class="col-xs-1 cost cost-col <?php echo in_array('cost', $vcols)?'package_col visibility_visible':'package_col' ?>"><?php echo $cost?></div>
            <div class="col-xs-1 price price-col <?php echo in_array('price', $vcols)?'package_col visibility_visible':'package_col' ?> "><?php echo $rate?></div>
            <div class="col-xs-1 subtotal subtotal-col <?php echo in_array('subtotal', $vcols)?'package_col visibility_visible':'package_col' ?>">
                <strong>
                    <input type="text" class = "subtotal" data-pid = "<?php echo "id-".$itemid; ?>" name="item[<?php echo $itemid ; ?>][subtotal]" value="<?php echo $subtotal?>" <?php echo $readonly ?> style="width: 80px;text-align: center;">
                </strong>
            </div>
            <div class="col-xs-1 tax-col <?php echo in_array('tax', $vcols)?'package_col visibility_visible':'package_col' ?>"><?php echo $taxrate?></div>
            <div class="col-xs-1 profit profit-col <?php echo in_array('profit', $vcols)?'package_col visibility_visible':'package_col' ?>"><?php echo $profit?></div>
            <div class="col-xs-1"><button class="btn btn-danger package_item_remove" data-pid = "<?php echo "id-".$itemid; ?>" ><i class="fa fa-close"></i></button></div>
            <div class="clearfix"></div>
            <div class="row"><div class="col-xs-2"></div><div class="col-xs-10 desc_inner" style="display: none"><?php echo strip_tags($description); ?></div></div>
        </div>
        <!-- <div class="col-sm-1 remove "><button data-pid = "<?php echo "id-".$itemid; ?>" class="package_item_remove" >Remove</button></div> -->

    </div>
    <!-- </div> -->
<?php } ?>