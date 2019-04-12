<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
$brandid = get_user_session();

$aColumns     = array(
    'tblproductcategory.id',
    'tblproductcategory.name',
    '(SELECT name FROM tbllineitem_category WHERE id = `tblproductcategory`.`li_category`) as parent_category',
    '(SELECT name FROM tbllineitem_subcategory WHERE id = `tblproductcategory`.`li_sub_category`) as child_category',
   );


$sIndexColumn = "id";
$sTable       = 'tblproductcategory';

$where = array();
array_push($where, ' AND tblproductcategory.deleted = 0 AND tblproductcategory.brandid =' . $brandid);

$result           = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), $where, array());

$output           = $result['output'];
$rResult          = $result['rResult'];

foreach ($rResult as $aRow) {    
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {

        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }

        $row[] = $_data;
    }
    $options = '';
    if(has_permission('items','','edit')){
        $options .= icon_btn('invoice_items/product_item/' . $aRow['tblproductcategory.id'], 'pencil-square-o', 'btn-orange');
        
    }
    if(has_permission('items','','delete')){
       $options .= icon_btn('invoice_items/product_item/' . $aRow['tblproductcategory.id'], 'remove', 'btn-danger _delete');
   }
   $row[] = $options;

   $output['aaData'][] = $row;
}