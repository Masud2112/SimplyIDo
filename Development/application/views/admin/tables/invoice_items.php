<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$brandid = get_user_session();

$session_data = get_session_data();
$user_id = $session_data['staff_user_id'];

$aColumns = array(
    'profile_image',
    'description',
    'cost_price',
    '(SELECT CONCAT(`tbllineitem_category`.`name`," >> ",`tbllineitem_subcategory`.`name`)) as category',
    'rate',
    'is_taxable',
    'profit',
);
$sIndexColumn = "id";
$sTable = 'tblitems';

$join = array(
    'LEFT JOIN tbllineitem_subcategory ON tbllineitem_subcategory.id = tblitems.line_item_sub_category AND tbllineitem_subcategory.deleted = 0 AND tbllineitem_subcategory.brandid =' . $brandid,
    'LEFT JOIN  tbllineitem_category ON  tbllineitem_category.id = tbllineitem_subcategory.parent_id AND  tbllineitem_category.deleted = 0 AND  tbllineitem_category.brandid =' . $brandid,
    'LEFT JOIN  tbltaxes ON  tbltaxes.id = tblitems.tax AND  tbltaxes.deleted = 0 AND tbltaxes.brandid =' . $brandid,
);
$additionalSelect = array(
    'tblitems.id',
    'sku',
    'long_description',
    '(SELECT GROUP_CONCAT(tblitems_options.option_name ORDER BY `order` ASC) FROM `tblitems_options` WHERE deleted = 0 AND itemid = tblitems.id GROUP BY itemid) as product_option_name',
    '(SELECT GROUP_CONCAT(tblitems_options.option_type ORDER BY `order` ASC) FROM `tblitems_options` WHERE deleted = 0 AND itemid = tblitems.id GROUP BY itemid) as product_option_type',
    'group_id',
    'is_custom',
    '(SELECT tbltaxes.taxrate FROM tbltaxes where tbltaxes.id=tblitems.tax AND deleted = 0 AND brandid = ' . $brandid . ') as tax',
    /*'tax'*/
);

$where = array();
array_push($where, ' AND tblitems.deleted = 0 AND tblitems.is_template = 1 AND tblitems.brandid =' . $brandid);


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output = $result['output'];
$rResult = $result['rResult'];

$rResult = array_reverse($rResult);

foreach ($rResult as $aRow) {
    $row = array();
    $product_options = get_product_options($aRow['id']);
    /*$product_options = explode(",", $aRow['product_option_name']);
    $product_options_type = explode(",", $aRow['product_option_type']);*/

    $line_item_image_section = "";
    $line_item_image_section .= '<div class="product-pimg">' . line_item_image($aRow['id'], array('item-profile-image-small')) . '</div>';

    $line_item_image_section .= '<div class="product-det mleft10"><div class="proName"><a href="' . admin_url('invoice_items/item/' . $aRow['id']) . '">' . $aRow['description'] . '</a></div>';

    $line_item_image_section .= '<div class="skutxt">SKU: ' . $aRow['sku'] . '</div>';

    /**
     * Added By: Vaidehi
     * Dt: 02/04/2018
     * added to show options, if options exists
     */
    if (count($product_options) > 0) {
        $line_item_image_section .= '<div class="toggle-bar"><a class="unique_opt" data-toggle="collapse" href="#show_opt_' . $aRow['id'] . '" id="option-' . $aRow['id'] . '" onclick="fnCollapseOption(' . $aRow['id'] . ')" role="button" aria-expanded="false" aria-controls="collapseExample"><i id="option-icon-' . $aRow['id'] . '" class="fa fa-sort-desc mright5" aria-hidden="true"></i>Options (' . count($product_options) . ')</a><div class="collapse" id="show_opt_' . $aRow['id'] . '">';

        foreach ($product_options as $key => $option) {
            $line_item_image_section .= '<div class="col-sm-12"><label><strong>' . _l($option->option_type) . ' : </strong>' . $option->option_name . '</label>';
            $choice_list = get_choice_list($option->id);
            /*$choice_rate_list = get_choice_rate_by_name($options, $aRow['id']);*/

            if (count($choice_list) > 0) {
                /*$group = (isset($choice_list->choice_name) ? $choice_list->choice_name : '');
                $rate_group = (isset($choice_rate_list->choice_rate) ? $choice_rate_list->choice_rate : '');
                $single_choices = explode(",", $group);
                $single_rate = explode(",", $rate_group);*/
                $line_item_image_section .= '<div class="choice_list">';
                if ($option->option_type == "dropdown") {
                    $line_item_image_section .= '<select class="form-control selectpicker" >';

                    foreach ($choice_list as $key => $choice) {
                        $line_item_image_section .= '<option>' . $choice->choice_name . ' (+$' . $choice->choice_rate . ')</option>';
                    }

                    $line_item_image_section .= '</select>';
                } elseif ($option->option_type == "single_option") {
                    foreach ($choice_list as $key => $choice) {
                        $line_item_image_section .= '<div class="radio"><input type="radio" class="radio"><label>' . $choice->choice_name . ' (+$' . $choice->choice_rate . ')</label></div>';
                    }
                } else {
                    foreach ($choice_list as $key => $choice) {
                        $line_item_image_section .= '<div class="checkbox"><input type="checkbox" class="checkbox"><label>' . $choice->choice_name . ' (+$' . $choice->choice_rate . ')</label></div>';
                    }
                }
                $line_item_image_section .= '</div>';
            }

            $line_item_image_section .= '</div>';
        }

        $line_item_image_section .= '</div></div>';
    }

    /**
     * Added By: Vaidehi
     * Dt: 02/04/2018
     * added to show description, if description is not empty
     */
    if (!empty($aRow['long_description'])) {
        if (strlen($aRow['long_description']) > 90) {
            $description = substr($aRow['long_description'], 0, 90);
            $description .= $description . ' ...';
        } else {
            $description = $aRow['long_description'];
        }

        $line_item_image_section .= '<div class="toggle-bar"><a class="unique_desc" data-toggle="collapse" href="#show_desc_' . $aRow['id'] . '" id="desc-' . $aRow['id'] . '" onclick="fnCollapseDesc(' . $aRow['id'] . ')" role="button" aria-expanded="false" aria-controls="collapseExample"><i id="icon-' . $aRow['id'] . '" class="fa fa-sort-desc mright5" aria-hidden="true"></i>Description</a><div class="collapse" id="show_desc_' . $aRow['id'] . '">' . strip_tags($description) . '</div></div></div>';
    }

    $row[] = $line_item_image_section;

    $row[] = $aRow['category'];
    $row[] = format_money($aRow['cost_price']);
    $row[] = format_money($aRow['rate']);

    $is_taxable = "";
    //var_dump($aRow['tax']);
    if ($aRow['is_taxable'] == "1" && $aRow['is_custom'] == "1") {
        $is_taxable .= $aRow['tax'];
    } else if ($aRow['is_taxable'] == "1") {

        $is_taxable .= "<i class='fa fa-check'></i>";
    } else {
        $is_taxable .= "---";
    }

    $row[] = $is_taxable;

    $row[] = format_money($aRow['profit']);

    $options = "<div class='text-right mright10'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";

    if (has_permission('items', '', 'edit')) {
        $options .= icon_url('invoice_items/item/' . $aRow['id'], 'pencil-square-o', '');
        $options .= icon_url('#', 'clone', '', array('data-toggle' => 'modal', 'data-target' => '#duplicate_line_item', 'id' => 'duplicate_action_button', 'data-id' => $aRow['id'], 'onclick' => 'duplicate_status(this)'));

    }
    if (has_permission('items', '', 'delete')) {
        $options .= icon_url('invoice_items/delete/' . $aRow['id'], 'remove', '_delete');
    }
    $options .= '</div>';
    $options .= "</ul></div>";
    $row[] = $options;

    $output['aaData'][] = $row;
}