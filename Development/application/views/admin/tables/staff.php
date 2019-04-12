<?php
defined('BASEPATH') or exit('No direct script access allowed');
$brandid = get_user_session();
$session_data   = get_session_data();
$is_sido_admin  = $session_data['is_sido_admin'];
$is_admin       = $session_data['is_admin'];

$custom_fields = get_custom_fields('staff', array(
    'show_on_table' => 1
));
$aColumns      = array(
    'firstname',
    'email',
    'last_login',
    'tblstaff.active'
);
$sIndexColumn  = "staffid";
$sTable        = 'tblstaff';
$join          = array();
$i             = 0;
foreach ($custom_fields as $field) {
    $select_as = 'cvalue_'.$i;
    if ($field['type'] == 'date_picker' || $field['type'] == 'date_picker_time') {
        $select_as = 'date_picker_cvalue_'.$i;
    }
    array_push($aColumns, 'ctable_'.$i.'.value as '.$select_as);
    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_' . $i . ' ON tblstaff.staffid = ctable_' . $i . '.relid AND ctable_' . $i . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $i . '.fieldid=' . $field['id']);
    $i++;
}
// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->_instance->db->query('SET SQL_BIG_SELECTS=1');
}
$where = do_action('staff_table_sql_where', array());
if($is_admin == 0){
    if($is_sido_admin == 0){
        array_push($join, 'JOIN tblstaffbrand as sb ON tblstaff.staffid = sb.staffid AND brandid=' . $brandid);
        array_push($join, 'JOIN tblroleuserteam as rt ON tblstaff.staffid = rt.user_id');
        array_push($join, 'JOIN tblroles as rl ON rt.role_id = rl.roleid');
        array_push($where, 'AND (tblstaff.is_not_staff = 0)');
        /*array_push($where, 'AND (tblstaff.user_type = 1 OR tblstaff.user_type = 2)');*/
        /*$this->db->join('tblroleuserteam', 'tblroleuserteam.user_id = tblstaff.staffid');
        $this->db->join('tblroles', 'tblroles.roleid = tblroleuserteam.role_id');*/
    }
}


//Added on 10/03 By Purvi
array_push($where, ' AND tblstaff.deleted = 0');
if($is_sido_admin == 1){
    array_push($where, 'AND (is_sido_admin = 1)');
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'profile_image',
    'lastname',
    'tblstaff.staffid',
    'admin'
),"GROUP BY tblstaff.staffid");

$output  = $result['output'];
$rResult = $result['rResult'];
/*echo "<pre>";
print_r($rResult);
die('<--here');*/
foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {

        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if ($aColumns[$i] == 'last_login') {
            if ($_data != null) {
                $_data = time_ago($_data);
            } else {
                $_data = 'Never';
            }
        } elseif ($aColumns[$i] == 'tblstaff.active') {
            $checked = '';
            if ($aRow['tblstaff.active'] == 1) {
                $checked = 'checked';
            }

            $_data = '<div class="onoffswitch">
                <input type="checkbox" data-switch-url="'.admin_url().'staff/change_staff_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_'.$aRow['staffid'].'" data-id="'.$aRow['staffid'].'" ' . $checked . '>
                <label class="onoffswitch-label" for="c_'.$aRow['staffid'].'"></label>
            </div>';

            // For exporting
            $_data .= '<span class="hide">' . ($checked == 'checked' ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
        } elseif ($aColumns[$i] == 'firstname') {
            $_data = staff_profile_image($aRow['staffid'], array(
                'staff-profile-image-small'
            ));
            $_data .= $aRow['firstname'] . ' ' . $aRow['lastname'];
        } elseif ($aColumns[$i] == 'email') {
            $_data = '<a href="mailto:' . $_data . '">' . $_data . '</a>';
        } else {
            if (strpos($aColumns[$i], 'date_picker_') !== false) {
                $_data = (strpos($_data, ' ') !== false ? _dt($_data) : _d($_data));
            }
        }
        $row[] = $_data;
    }
    $options = "<div class='text-right mright10'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
    if (has_permission('account_setup', '', 'edit')){
        $options .= icon_url('staff/member/' . $aRow['staffid'], 'pencil-square-o');
    }else{
        $options = "";
    }
    if (has_permission('account_setup', '', 'delete') && $output['iTotalRecords'] > 1 && $aRow['staffid'] != get_staff_user_id()) {
        $options .= icon_url('#', 'remove', '', array(
            'onclick'=>'delete_staff_member('.$aRow['staffid'].'); return false;',
        ));
    }
    $options.="</ul></div>";
    $row[]              = $options;
    $output['aaData'][] = $row;
}
