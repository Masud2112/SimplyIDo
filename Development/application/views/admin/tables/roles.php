<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$brandid = get_user_session();
$aColumns     = array(
    'name'
    );

$sIndexColumn = "roleid";
$sTable       = 'tblroles';

//Added on 10/03 By Purvi
$where   = array();

array_push($where, ' AND deleted = 0');

if(!is_admin()) {
    array_push($where, ' AND visible = 1');
}

if($brandid > 0){
    array_push($where, 'AND brandid =' . $brandid);
}


$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), $where, array(
    'roleid',
    'isupgrade',
    'isdowngrade'
    ));
//$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,array(),array(),array('roleid'));
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name') {
            $role_permissions = $this->_instance->roles_model->get_role_permissions($aRow['roleid']);
            $_data= $_data;
        }
        $row[] = $_data;
    }
    $row[]=$_data = '<span class="mtop10 display-block">'. total_rows('tblroleuserteam', array(
            'role_id' => $aRow['roleid']
        )) . '</span>';

    $options = "<div class='text-right mright10'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
    if (has_permission('account_setup', '', 'edit')) {
        $options .= icon_url('roles/role/' . $aRow['roleid'], 'pencil-square-o');
    } else {
        $options = "";
    }

    if (has_permission('account_setup', '', 'delete')) {
        if($aRow['isupgrade'] == 1 || $aRow['isdowngrade'] == 1) {
            $row[]   = $options .= "";
        } else {
            $row[]   = $options .= icon_url('roles/delete/' . $aRow['roleid'], 'remove', '_delete');
        }
    } else {
        $row[]   = $options .= "";
    }
    $options.="</ul></div>";
    $output['aaData'][] = $row;
}
