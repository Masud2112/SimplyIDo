<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$brandid = get_user_session();
$session_data = get_session_data();
$is_sido_admin = $session_data['is_sido_admin'];
$is_admin = $session_data['is_admin'];

$aColumns     = array(
    'name',
    'color'
    );
$sIndexColumn = "id";
$sTable       = 'tbltags';

//Added on 10/03 By Avni
$where   = array();
array_push($where, ' AND deleted = 0');
if($brandid > 0){
    array_push($where, ' AND brandid =' . $brandid);
}  
else if($is_sido_admin > 0){
    array_push($where, ' AND brandid =0');
} 


$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), $where, array(
    'id','brandid'
    ));
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        $is_referenced = '';//(total_rows('tblexpenses',array('tax'=>$aRow['id'])) > 0 || total_rows('tblexpenses',array('tax2'=>$aRow['id'])) > 0 ? 1 : 0);

        if($aColumns[$i] == 'name'){
            if($aRow['brandid'] > 0 || $is_sido_admin == 1 || $is_admin == 1){
                $_data = '<a href="#" data-toggle="modal" data-is-referenced="'.$is_referenced.'" data-target="#tag_modal" data-id="'.$aRow['id'].'">'.$_data.'</a>';
            }else {
                $_data = $_data;
            }
        }
        if($aColumns[$i] == 'color'){
             $_data = '<span style="padding:5px;background-color:'.$_data.';display: inline-block; width: 10px; height: 10px;"></span> &nbsp;' .$_data;
        }
        
        $row[] = $_data;
    }

    //Added on 10/03 By Avni
   
   if($aRow['brandid'] > 0 || $is_sido_admin == 1 || $is_admin == 1){
       $options = "<div class='text-right mright10'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
        if (has_permission('lists','','edit')) {
            $options .= icon_url('#' . $aRow['id'], 'pencil-square-o', '', array(
                'data-toggle' => 'modal',
                'data-target' => '#tag_modal',
                'data-id' => $aRow['id'],
                'data-is-referenced'=>$is_referenced
                ));
        }else{
            $options = "";
        }
        if (has_permission('lists','','delete')) {
            $row[]   = $options .= icon_url('tags/delete/' . $aRow['id'], 'remove', 'modal-alert-warning _delete');
        }else{
            $row[]   = $options .= "";
        }
       $options.="</ul></div>";
    }else{
        $row[]   = $options = "";
    }

    $output['aaData'][] = $row;
}
