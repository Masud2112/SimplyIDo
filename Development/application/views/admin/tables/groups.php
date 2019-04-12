<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$brandid = get_user_session();

$session_data   = get_session_data();
$user_id    = $session_data['staff_user_id'];

$aColumns     = array(
    'name',
    'group_sku',
    'group_cost',
    'group_price',
    'group_profit',
    
    /*'(SELECT CONCAT(`tbllineitem_category`.`name`," >> ",`tbllineitem_subcategory`.`name`)) as category',
    'rate',
    'is_taxable',
    'profit',*/
    );
$sIndexColumn = "id";
$sTable       = 'tblitems_groups';

$join             = array(
    /*'LEFT JOIN tbllineitem_subcategory ON tbllineitem_subcategory.id = tblitems.line_item_sub_category AND tbllineitem_subcategory.deleted = 0 AND tbllineitem_subcategory.brandid =' . $brandid,
    'LEFT JOIN  tbllineitem_category ON  tbllineitem_category.id = tbllineitem_subcategory.parent_id AND  tbllineitem_category.deleted = 0 AND  tbllineitem_category.brandid =' . $brandid,
    'LEFT JOIN  tbltaxes ON  tbltaxes.id = tblitems.tax AND  tbltaxes.deleted = 0 AND tbltaxes.brandid =' . $brandid,*/
);
$additionalSelect = array(
    'id',
    'group_image',
    'group_description',
    /*'tblitems.id',
    'sku',
    'long_description',
    '(SELECT GROUP_CONCAT(tblitems_options.option_name ORDER BY `order` ASC) FROM `tblitems_options` WHERE deleted = 0 AND itemid = tblitems.id GROUP BY itemid) as product_option_name',
    '(SELECT GROUP_CONCAT(tblitems_options.option_type ORDER BY `order` ASC) FROM `tblitems_options` WHERE deleted = 0 AND itemid = tblitems.id GROUP BY itemid) as product_option_type',
    'group_id',
    'is_custom',
    '(SELECT tbltaxes.taxrate FROM tbltaxes where tbltaxes.id=tblitems.tax AND deleted = 0 AND brandid = '.$brandid.') as tax',*/
    /*'tax'*/
    );

$where = array();
array_push($where, ' AND tblitems_groups.deleted = 0 AND tblitems_groups.brandid =' . $brandid);


$result           = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, $additionalSelect);
$output           = $result['output'];
$rResult          = $result['rResult'];

//$rResult = array_reverse($rResult);

foreach ($rResult as $aRow) {    
    $row = array();


    $package_image_section = "";
    $package_image_section .= '<div class="product-pimg">'.group_image($aRow['id'], array('item-profile-image-product_services_package_image')).'</div>';

    $package_image_section .= '<div class="product-det mleft10"><div class="proName"><a href="'.admin_url('invoice_items/package/'.$aRow['id']).'">'.$aRow['name'].'</a></div>';
    
    $package_image_section .= '<div class="skutxt">SKU: '.$aRow['group_sku'].'</div>';

    if(!empty($aRow['group_description'])) {
        if(strlen($aRow['group_description']) > 90) {
            $description = substr($aRow['group_description'], 0, 90);
            $description .= $description . ' ...';
        } else {
            $description = $aRow['group_description'];
        }

        $package_image_section .= '<div class="toggle-bar"><a class="unique_desc" data-toggle="collapse" href="#show_desc_'.$aRow['id'].'" id="desc-'.$aRow['id'].'" onclick="fnCollapseDesc('.$aRow['id'].')" role="button" aria-expanded="false" aria-controls="collapseExample"><i id="icon-'.$aRow['id'].'" class="fa fa-sort-desc mright5" aria-hidden="true"></i>Description</a><div class="collapse" id="show_desc_'.$aRow['id'].'">'. strip_tags($description) . '</div></div></div>';
    }


    $package_image_section .= '</div></div>';

    

    $row[] = $package_image_section;
    //$row[] = $aRow['name'];
    $row[] = $aRow['group_cost'];
    $row[] = $aRow['group_price'];
    $row[] = $aRow['group_profit'];


    
    //$product_options = explode(",", $aRow['product_option_name']);
    //$product_options_type = explode(",", $aRow['product_option_type']);

    /*$package_image_section = "";
    $package_image_section .= '<div class="product-pimg">'.line_item_image($aRow['id'], array('item-profile-image-small')).'</div>';

    $package_image_section .= '<div class="product-det mleft10"><div class="proName"><a href="'.admin_url('invoice_items/item/'.$aRow['id']).'">'.$aRow['description'].'</a></div>';
    
    $package_image_section .= '<div class="skutxt">SKU: '.$aRow['sku'].'</div>';*/

    
    
    /**
    * Added By: Vaidehi
    * Dt: 02/04/2018
    * added to show description, if description is not empty
    */
    /*if(!empty($aRow['group_description'])) {
        if(strlen($aRow['group_description']) > 90) {
            $description = substr($aRow['group_description'], 0, 90);
            $description .= $description . ' ...';
        } else {
            $description = $aRow['group_description'];
        }

        $line_item_image_section .= '<div class="toggle-bar"><a class="unique_desc" data-toggle="collapse" href="#show_desc_'.$aRow['id'].'" id="desc-'.$aRow['id'].'" onclick="fnCollapseDesc('.$aRow['id'].')" role="button" aria-expanded="false" aria-controls="collapseExample"><i id="icon-'.$aRow['id'].'" class="fa fa-sort-desc mright5" aria-hidden="true"></i>Description</a><div class="collapse" id="show_desc_'.$aRow['id'].'">'. strip_tags($description) . '</div></div></div>';
    }

    $row[] = $line_item_image_section;*/

    /*$row[] = $aRow['category'];
    $row[] = $aRow['cost_price'];
    $row[] = $aRow['rate'];*/
    
    /*$is_taxable ="";
    //var_dump($aRow['tax']);
    if($aRow['is_taxable']=="1" && $aRow['is_custom']=="1")
    {
            $is_taxable .= $aRow['tax'];  
    }
    else if($aRow['is_taxable']=="1")
    {
        
        $is_taxable .= "<i class='fa fa-check'></i>";  
    }
    else
    {
        $is_taxable .= "---";
    }
    
    $row[] = $is_taxable;

    $row[] = $aRow['profit'];*/

    $options = "<div class='text-right mright10'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
    if(has_permission('items','','edit')){
        $options .= icon_url('invoice_items/package/' . $aRow['id'], 'pencil-square-o', '');
        $options .= icon_url('#', 'clone', '',array('data-toggle' => 'modal', 'data-target' => '#duplicate_group', 'id' => 'duplicate_action_button', 'data-id' => $aRow['id'], 'onclick' => 'duplicate_status(this)'));
         
    }
    if(has_permission('items','','delete')){
       $options .= icon_url('invoice_items/delete_group/' . $aRow['id'], 'remove', '_delete');
   }
    $options.="</ul></div>";

   $row[] = $options;

   $output['aaData'][] = $row;
}