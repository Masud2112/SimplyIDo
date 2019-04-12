<?php
/**
* Added By: Vaidehi
* Dt: 01/29/2017
* Questionnaire Module
*/
defined('BASEPATH') OR exit('No direct script access allowed');
$brandid    = get_user_session();

$aColumns     = array(
    'name',
    'createddate'
);

$sIndexColumn = "id";
$sTable       = 'tblquestionnairetemplate';

$where = array();
array_push($where, ' AND deleted = 0');
array_push($where, ' AND brandid =' . $brandid);
$rel_id = 0;
if(isset($_GET['pid'])){
    $rel_id = $_GET['pid'];
}
array_push($where, ' AND rel_id =' . $rel_id);

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,array(),$where,array('id'));

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
    $row[] = $aRow['name'];
    $row[] = _dt($aRow['createddate']);
    // for ($i = 0; $i < count($aColumns); $i++) {
    //     $_data = $aRow[$aColumns[$i]];
    //     $row[] = $_data;
    // }
    $options = "";
    
    if (has_permission('questionnaire','','edit')) {
        $options = icon_btn('questionnaire/questionnaire/' . $aRow['id'], 'pencil-square-o');
    } else {
        $options = "";
    }
    if (has_permission('questionnaire','','delete')) {
        $row[]   = $options .= icon_btn('questionnaire/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
    } else {
        $row[]   = $options .= "";
    }
    $output['aaData'][] = $row;
}
