<?php
defined('BASEPATH') or exit('No direct script access allowed');

$is_admin       = is_admin();
$session_data   = get_session_data();

$user_id    = $session_data['staff_user_id'];
$user_type  = $session_data['user_type'];

$brandid    = get_user_session();

$custom_fields = get_table_custom_fields('projects');

if($user_type == 1) {
    $aColumns     = array(
    //'1',
    'tblprojects.id as id',
    'tblprojects.eventstartdatetime',
    'tblprojects.name',
    '(SELECT CONCAT(tblvenue.venueaddress, \', \', tblvenue.venuecity, \', \', tblvenue.venuestate) FROM tblvenue WHERE tblvenue.venueid =  tblprojects.venueid) as project_venue',
    'tblprojectstatus.name as status_name',
    '(SELECT COUNT(p1.id) FROM tblprojects p1 WHERE p1.parent = tblprojects.id AND p1.deleted = 0) as no_of_events',
    'CONCAT(firstname, \' \', lastname) as assigned_name',
    '(SELECT pinid FROM tblpins WHERE tblpins.userid='.$user_id.' and tblpins.pintype = "Project" and tblpins.pintypeid = tblprojects.id) as pinned',
    //'tblprojects.datecreated'
    );    
} else {
    $aColumns     = array(
    //'1',
    'tblprojects.id as id',
    'tblprojects.eventstartdatetime',
    'tblprojects.name',
    '(SELECT CONCAT(tblvenue.venueaddress, \', \', tblvenue.venuecity, \', \', tblvenue.venuestate) FROM tblvenue WHERE tblvenue.venueid =  tblprojects.venueid) as project_venue',
    'tblprojectstatus.name as status_name',
    '(SELECT COUNT(p1.id) FROM tblprojects p1 LEFT JOIN tblprojectcontact pc ON p1.id = pc.projectid WHERE p1.parent = tblprojects.id AND p1.deleted = 0 AND (p1.assigned = ' . $user_id . ' OR tblprojectcontact.contactid = ' . $user_id . ')) as no_of_events',
    'CONCAT(firstname, \' \', lastname) as assigned_name',
    '(SELECT pinid FROM tblpins WHERE tblpins.userid='.$user_id.' and tblpins.pintype = "Project" and tblpins.pintypeid = tblprojects.id) as pinned',
    //'tblprojects.datecreated'
    );

}

$sIndexColumn = "id";
$sTable       = 'tblprojects';

$join = array(
    'LEFT JOIN tblstaff ON tblstaff.staffid = tblprojects.assigned',
    'LEFT JOIN tblprojectstatus ON tblprojectstatus.id = tblprojects.status',
    'LEFT JOIN tblleadssources ON tblleadssources.id = tblprojects.source',
    'LEFT JOIN tbleventtype ON tbleventtype.eventtypeid = tblprojects.eventtypeid',
    'LEFT JOIN tblprojectcontact ON tblprojectcontact.projectid = tblprojects.id',
    'LEFT JOIN tblvenue ON tblvenue.venueid = tblprojects.venueid'
);

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_'.$key);
    array_push($customFieldsColumns,$selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_' . $key . ' ON tblprojects.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

$where = array();
$filter = false;

array_push($where, ' AND tblprojects.deleted = 0');
array_push($where, ' AND tblprojects.parent = 0');
array_push($where, ' AND tblprojects.brandid =' . $brandid);

if ($this->_instance->input->post('project_custom_view')) {
    $filter = $this->_instance->input->post('project_custom_view');
    if ($filter == 'lost') {
        array_push($where, 'AND lost = 1');
    } elseif ($filter == 'junk') {
        array_push($where, 'AND junk = 1');
    } elseif ($filter == 'not_assigned') {
        array_push($where, 'AND assigned = 0');
    } elseif ($filter == 'contacted_today') {
        array_push($where, 'AND lastcontact LIKE "'.date('Y-m-d').'%"');
    } elseif ($filter == 'created_today') {
        array_push($where, 'AND dateadded LIKE "'.date('Y-m-d').'%"');
    } elseif ($filter == 'public') {
        array_push($where, 'AND is_public = 1');
    }
}

// if (!$filter || ($filter && $filter != 'lost' && $filter != 'junk')) {
//     array_push($where, 'AND lost = 0 AND junk = 0');
// }

//if ($is_admin) {
    if ($this->_instance->input->post('assigned')) {
        $by_assigned = $this->_instance->input->post('assigned');
        array_push($where, 'AND assigned =' . $by_assigned);
    }
//}
if ($this->_instance->input->post('status')) {
    $by_assigned = $this->_instance->input->post('status');
    array_push($where, 'AND status =' . $by_assigned);
}

if ($this->_instance->input->post('source')) {
    $by_assigned = $this->_instance->input->post('source');
    array_push($where, 'AND source =' . $by_assigned);
}

if ($this->_instance->input->post('eventtype')) {
    $by_assigned = $this->_instance->input->post('eventtype');
    array_push($where, 'AND tblprojects.eventtypeid =' . $by_assigned);
}

if ($this->_instance->input->post('eventdate')) {
    // $by_assigned = $this->_instance->input->post('eventdate');
    // array_push($where, 'AND month(eventstartdatetime) =' . date($by_assigned)); 
    $eventdate = $this->_instance->input->post('eventdate');
    $eventdate = explode("-", $eventdate);
    $eventstartdate = date("Y-m-d",strtotime($eventdate[0]));
    $eventedenddate = date("Y-m-d",strtotime($eventdate[1]));
    array_push($where, 'AND date(eventstartdatetime) between "' . $eventstartdate .'" AND "'. $eventedenddate.'"'); 
}

if ($this->_instance->input->post('inquireddate')) {
    $inquireddate = $this->_instance->input->post('inquireddate');
    $inquireddate = explode("-", $inquireddate);
    $inquiredstartdate = date("Y-m-d",strtotime($inquireddate[0]));
    $inquiredenddate = date("Y-m-d",strtotime($inquireddate[1]));
    array_push($where, 'AND eventinquireon between "' . $inquiredstartdate .'" AND "'. $inquiredenddate.'"');   
}

if(!$is_admin) {
    array_push($where, 'AND (assigned =' . get_staff_user_id() . ' OR addedfrom = ' . get_staff_user_id() . ' OR tblprojectcontact.contactid = ' . get_staff_user_id() . ')');
}

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->_instance->db->query('SET SQL_BIG_SELECTS=1');
}

$group_by = 'GROUP BY tblprojects.id';

$aColumns = do_action('projects_table_sql_columns', $aColumns);

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'color',
    'assigned',
    'tblprojects.addedfrom as addedfrom',
    'tblprojects.status as statusid',
    'tblprojects.project_profile_image',
    'tbleventtype.eventtypename as eventtypename'
), $group_by);

$output  = $result['output'];
$rResult = $result['rResult'];

$this->_instance->load->model('projects_model');
$statuses = $this->_instance->projects_model->get_project_status();

foreach ($rResult as $aRow) {
    $row = array();

    //$row[] = '<div class="checkbox"><input type="checkbox" value="'.$aRow['id'].'"><label></label></div>';
    if($aRow['pinned'] > 0){
        $row[] =  '<i class="fa fa-fw fa-thumb-tack pinned project-pin" title="Unpin from dashboard" id="'.$aRow['id'].'" project_id="'.$aRow['id'].'"></i>';
    }else{
        $row[] =  '<i class="fa fa-fw fa-thumb-tack project-pin" title="Pin to dashboard" id="'.$aRow['id'].'" project_id="'.$aRow['id'].'"></i>';
    }
    
    $eventmonth = date("M",strtotime($aRow['tblprojects.eventstartdatetime']));
    $eventday = date("j",strtotime($aRow['tblprojects.eventstartdatetime']));
    $eventweekday = date("D",strtotime($aRow['tblprojects.eventstartdatetime']));
    $eventyear = date("Y",strtotime($aRow['tblprojects.eventstartdatetime']));

    $eventdate = "";
    $eventdate .= "<div class='text-center'><div><small>".strtoupper($eventmonth)."</small></div>";
    $eventdate .= "<div><h4 style='margin:0px'>".$eventday."</h4></div>";
    $eventdate .= "<div><small>".strtoupper($eventweekday)." | ".$eventyear."</small></div></div>";
    $eventdate .= "<div class='text-center'><small>". date('h:i A', strtotime($aRow['tblprojects.eventstartdatetime']))."</small></div>";
    $row[] = $eventdate;
    
    $name = stripcslashes($aRow['tblprojects.name']);
    
    $project_name = "";
    
    $project_name .= '<div class="project-pimg">'.project_profile_image($aRow['id'], array("project-profile-image-small")).'</div><div class="project-det"><div><a href="'.admin_url('projects/dashboard/'.$aRow['id']).'">'.$name.'</a></div>';
    $project_name .= "<div><i class='fa fa-book'></i>".$aRow['eventtypename']."</div></div>";
    $row[] =  $project_name;

    //$row[] = (!empty($aRow['project_venue']) ? ltrim($aRow['project_venue'], ',') : '');
    $row[] = '';

    $statuscontent = '<span class="projectstatuscolor" style="background-color:' . $aRow['color'] . ';"></span>';
    $statuscontent .= '<select project_id="'.$aRow['id'].'" class="selectpicker projectstatus">';
    foreach ($statuses as $svalue) {
        $selected = "";
        if($aRow['statusid'] == $svalue['id']){
            $selected = "selected='selected'";
        }else{
            $selected = "";
        }
        $statuscontent .= '<option  value="'.$svalue['id'].'" '.$selected.'>'.$svalue['name'].'</option>';
    }
    $statuscontent .= '</select>';
    $row[] = $statuscontent;

    $row[]  = '<a href="javascript: void(0);" onclick="getSubEvents(' . $aRow['id'] . ')" data-id="' . $aRow['id'] . '">' . $aRow['no_of_events'] . '</a>';

    $assignedOutput = '';
    if ($aRow['assigned'] != 0) {

        $full_name = $aRow['assigned_name'];

        $assignedOutput = '<a data-toggle="tooltip" data-title="'.$full_name.'" href="javascript:void(0)">'.staff_profile_image($aRow['assigned'], array(
            'staff-profile-image-small'
            )) . '</a>';

        // For exporting
        $assignedOutput .= '<span class="hide">'.$full_name.'</span>';
    }

    $row[] = $assignedOutput;

    //$row[] = ($aRow['tblprojects.datecreated'] == '0000-00-00 00:00:00' || !is_date($aRow['tblprojects.datecreated']) ? '' : '<span>'._dt($aRow['tblprojects.datecreated']).'</span>');
    
    // Custom fields add values
    foreach($customFieldsColumns as $customFieldColumn){
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $hook_data = do_action('projects_table_row_data', array(
        'output' => $row,
        'row' => $aRow
    ));

    $row = $hook_data['output'];
    $options = "<div><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";

    //$options .= icon_btn('projects/dashboard/' . $aRow['id'], 'eye', 'btn-success', array('title'=>'View Dashboard'));
    $options.='<li><a href='.admin_url().'projects/dashboard/'.$aRow['id'].' class="" title="View Dashboard"><i class="fa fa-eye"></i><span>View</span></a></li>';
    if (has_permission('projects', '', 'edit')){
        //$options .= icon_btn('projects/project/' . $aRow['id'], 'pencil-square-o');
        $options.='<li><a href='.admin_url().'projects/project/'.$aRow['id'].' class="" title="Edit"><i class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>';

    } else {
        $options .= "";
    }
    
    // if (has_permission('projects', '', 'delete')) {    
    //     $options .= icon_btn('projects/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
    // }
    $options.="</ul></div>";
    $row[] = $options;
    
    $output['aaData'][] = $row;
}
