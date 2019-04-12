<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$brandid = get_user_session();

$aColumns     = array(
    'name','datecreated'
    );

$sIndexColumn = "templateid";
$sTable       = 'tblagreementtemplates';

$where   = array();

array_push($where, ' AND deleted = 0');
if($brandid > 0){
    array_push($where, 'AND brandid =' . $brandid);
}  


$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,array(),$where,array('templateid'));
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
    
    $row[] = $aRow['name'];
    $row[] = _dt($aRow['datecreated'],false);


    $options = "<div class='text-right mright10'><a class='show_act ' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";

    if (has_permission('agreements','','edit')) {
        $options .= icon_url('agreements/agreement/' . $aRow['templateid'], 'pencil-square-o');
    }
    if (has_permission('agreements','','delete')) {
        $row[]   = $options .= icon_url('agreements/deleteagreement/' . $aRow['templateid'], 'remove', '_delete');
    }else{
        $row[]   = $options .= "";
    }
    $options.="</ul></div>";
    $output['aaData'][] = $row;
}
