<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$brandid = get_user_session();
$session_data = get_session_data();
$is_sido_admin = $session_data['is_sido_admin'];
$is_admin = $session_data['is_admin'];

$aColumns     = array(
    'name',
    );
$sIndexColumn = "id";
$sTable       = 'tblincome_category';

//Added on 10/03 By Avni
$where   = array();

array_push($where, ' AND deleted = 0');
if($brandid > 0){
    array_push($where, 'AND brandid =' . $brandid);
}  
else if($is_sido_admin > 0){
    array_push($where, 'AND brandid =0');
} 


$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), $where, array(
    'id','brandid'
    ));
$output  = $result['output'];
$rResult = $result['rResult'];


foreach ($rResult as $aRow) {
    $row = array();
    
    $row[]   = $aRow['name'];

    //Added on 10/03 By Avni
    if($aRow['brandid'] > 0 || $is_sido_admin == 1 || $is_admin == 1){
        $options = "<div class='text-right mright10'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
        if (has_permission('items','','edit')) {
            $options .= icon_url('#' . $aRow['id'], 'pencil-square-o', '', array(
                'data-toggle' => 'modal',
                'data-target' => '#income_category_modal',
                'data-id' => $aRow['id'],
                'data-name' => $aRow['name'],
                'onclick' => 'edit_income_category(this)'
                ));

        }else{
            $options = "";
        }
        if (has_permission('items','','delete')) {
            if($aRow['name']=="Uncategorized" || $aRow['name']=="uncategorized")
            {
                 $row[]   = $options .= "";
            }
            else
            {
                $row[]   = $options .= icon_url('invoice_items/income_dategory_delete/' . $aRow['id'], 'remove', '_delete');
            }
        }else{
            $row[]   = $options .= "";
        }
        $options.="</ul></div>";
    }else{
        $row[]   = $options = "";
    }
    $output['aaData'][] = $row;
}
