<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 02-08-2018
 * Time: 01:06 PM
 */
?><b>SELECTED SERVICES</b><br />
<?php foreach ($selectedItems as $selected_item) {
    $selected_item = (array)$selected_item;
    if (strtolower($selected_item['type']) == 'package') {
        $item = $CI->invoice_items_model->get_group($selected_item['id']);
        $item->description = $item->name;
        $item->long_description = $item->group_description;
    } else {
        $item = $CI->invoice_items_model->get_item($selected_item['id']);
    }
    ?>
    <table width="100%">
        <tr>
            <td><b><?php echo $item->description ?></b></td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td><?php echo html_entity_decode($item->long_description); ?>
                <?php if (strtolower($selected_item['type']) == 'package') {
                    $package_items = json_decode($item->group_items); ?>
                    <ul>
                        <?php foreach ($package_items as $key => $package_item) {
                            $lineintem = $CI->invoice_items_model->get_item($key);
                            if (!empty($lineintem)) { ?>
                                <li><?php echo $lineintem->description; ?></li>
                            <?php }
                        } ?>
                    </ul>
                <?php } ?>
            </td>
        </tr>
    </table>
<?php } ?>