<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 02-08-2018
 * Time: 01:06 PM
 */
?>
<h5><b>SELECTED SERVICES</b></h5>
<div class="quote_items">
    <?php
    foreach ($selected_items as $selected_item) {
        if (strtolower($selected_item['type']) == 'package') {
            $item = $this->invoice_items_model->get_group($selected_item['id']);
            $item->description = $item->name;
            $item->long_description = $item->group_description;
        } else {
            $item = $this->invoice_items_model->get($selected_item['id']);
        }


        ?>
        <div class="item">
            <h5><strong><?php echo $item->description ?></strong></h5>
            <?php echo html_entity_decode($item->long_description); ?>
            <?php if (strtolower($selected_item['type']) == 'package') {
                $package_items = json_decode($item->group_items); ?>
                <ul>
                    <?php foreach ($package_items as $key => $package_item) {
                        $lineintem = $this->invoice_items_model->get($key);
                        if(!empty($lineintem)){ ?>
                        <li><?php echo $lineintem->description; ?></li>
                    <?php } }?>
                </ul>
            <?php } ?>
        </div>
    <?php } ?>
</div>
