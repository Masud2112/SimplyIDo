<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 16-08-2018
 * Time: 12:22 PM
 */
$item=(array)$item;
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
     class="col-sm-4 ps_pkg_item <?php echo $data_class . ' disabled ' . $disabled ?> <?php echo $item_type != "" ? strtolower($item_type) : 'product' ?>"
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