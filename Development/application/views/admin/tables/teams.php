<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$brandid = get_user_session();

$aColumns     = array(
    'name'
    );

$sIndexColumn = "teamid";
$sTable       = 'tblteams';
//Added on 10/03 By Purvi
$where   = array();

array_push($where, ' AND deleted = 0');
if($brandid > 0){
    array_push($where, 'AND brandid =' . $brandid);
}  


$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,array(),$where,array('teamid'));
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name') {
            //$team_permissions = $this->_instance->teams_model->get_team_permissions($aRow['teamid']);
            $_data = $_data;
        }
        $row[] = $_data;
    }
    $row[]= $_data = '<span class="display-block">'. total_rows('tblroleuserteam', array(
            'team_id' => $aRow['teamid']
        )) . '</span>';

    $options = "<div class='text-right mright10'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
    if (has_permission('account_setup','','edit')) {
        $options .= icon_url('teams/team/' . $aRow['teamid'], 'pencil-square-o');
    }
    if (has_permission('account_setup','','delete')) {
        $row[]   = $options .= icon_url('teams/delete/' . $aRow['teamid'], 'remove', '_delete');
    }else{
        $row[]   = $options .= "";
    }
    $options.="</ul></div>";
    $output['aaData'][] = $row;
}
