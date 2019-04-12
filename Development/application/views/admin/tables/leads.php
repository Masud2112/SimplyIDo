<?php
defined('BASEPATH') or exit('No direct script access allowed');

$is_admin = is_admin();
$session_data = get_session_data();
$user_id = $session_data['staff_user_id'];
$brandid = get_user_session();
$custom_fields = get_table_custom_fields('leads');

$aColumns     = array(
    '1',
    'tblleads.id as id',
    'tblleads.eventstartdatetime',
    'tblleads.name',
    'tblleadsstatus.name as status_name',
    'CONCAT(firstname, \' \', lastname) as assigned_name',
    'eventinquireon',
    '(SELECT pinid FROM tblpins WHERE tblpins.userid='.$user_id.' and tblpins.pintype = "Lead" and tblpins.pintypeid = tblleads.id) as pinned'//,
    //'dateadded'
);

$sIndexColumn = "id";
$sTable       = 'tblleads';

$join = array(
    'LEFT JOIN tblstaffleadassignee ON tblstaffleadassignee.leadid = tblleads.id',
    'LEFT JOIN tblstaff ON tblstaff.staffid = tblstaffleadassignee.assigned',
    'LEFT JOIN tblleadsstatus ON tblleadsstatus.id = tblleads.status',
    'LEFT JOIN tblleadssources ON tblleadssources.id = tblleads.source',
    'LEFT JOIN tbleventtype ON tbleventtype.eventtypeid = tblleads.eventtypeid',
);


foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_'.$key);
    array_push($customFieldsColumns,$selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_' . $key . ' ON tblleads.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

$where = array();
$filter = false;

//added by vaiehi on 03/08/2018
array_push($where, ' AND tblleads.converted = 0');
array_push($where, ' AND tblleads.deleted = 0');
array_push($where, ' AND tblleads.brandid =' . $brandid);

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

if (!$filter || ($filter && $filter != 'lost' && $filter != 'junk')) {
    array_push($where, 'AND lost = 0 AND junk = 0');
}

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
    array_push($where, 'AND tblleads.eventtypeid =' . $by_assigned);
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

if (!$is_admin) {
    array_push($where, 'AND (tblstaffleadassignee.assigned =' . get_staff_user_id() . ' OR addedfrom = ' . get_staff_user_id() . ' OR is_public = 1) GROUP BY tblleads.id');
}

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->_instance->db->query('SET SQL_BIG_SELECTS=1');
}


$aColumns = do_action('leads_table_sql_columns', $aColumns);

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'junk',
    'lost',
    'color',
    'tblstaffleadassignee.assigned as assigned',
    'tblleads.addedfrom as addedfrom',
    'tblleads.status as statusid',
    'tblleads.profile_image',
    'tbleventtype.eventtypename as eventtypename'
));

$output  = $result['output'];
$rResult = $result['rResult'];

$this->_instance->load->model('leads_model');
$statuses = $this->_instance->leads_model->get_status();

foreach ($rResult as $aRow) {

    $leadAssignees = get_lead_assignee($aRow['id']);
    $row = array();

    $row[] = '<div class="checkbox"><input type="checkbox" value="'.$aRow['id'].'"><label></label></div>';
    if($aRow['pinned'] > 0){
        $row[] =  '<i class="fa fa-fw fa-thumb-tack lead-pin pinned" title="Unpin from dashboard" id="'.$aRow['id'].'" lead_id="'.$aRow['id'].'"></i>';
    }else{
        $row[] =  '<i class="fa fa-fw fa-thumb-tack lead-pin" title="Pin to dashboard" id="'.$aRow['id'].'" lead_id="'.$aRow['id'].'"></i>';
    }

    $eventmonth = date("M",strtotime($aRow['tblleads.eventstartdatetime']));
    $eventday = date("j",strtotime($aRow['tblleads.eventstartdatetime']));
    $eventweekday = date("D",strtotime($aRow['tblleads.eventstartdatetime']));
    $eventyear = date("Y",strtotime($aRow['tblleads.eventstartdatetime']));

    $eventdate = "";
    $eventdate .= "<div class='text-center'><div><small>".strtoupper($eventmonth)."</small></div>";
    $eventdate .= "<div><h4 style='margin:0px'>".$eventday."</h4></div>";
    $eventdate .= "<div><small>".strtoupper($eventweekday)." | ".$eventyear."</small></div></div>";
    /*$eventdate ='<div class="carddate-block">
					<div class="card_date" title="'.$eventyear.'">
						<div class="card_month">
							<small>'.$eventmonth.'</small>
						</div>
						<div class="card_d">
							<strong>'.$eventday.'</strong>
						</div>
						<div class="card_day">
							<small>'.$eventweekday.'</small>
						</div>
					</div>';

                    if ($eventyear > date('Y')) {
                        $eventdate .='<div class="card_year"><small>'.$eventyear.'</small></div>';
                    }
    $eventdate .='</div>';*/
    $row[] = $eventdate;

    // if ($aRow['status_name'] == null) {
    //     if ($aRow['lost'] == 1) {
    //         $statusOutput = '<span class="label label-danger inline-block">' . _l('lead_lost') . '</span>';
    //     } elseif ($aRow['junk'] == 1) {
    //         $statusOutput = '<span class="label label-warning inline-block">' . _l('lead_junk') . '</span>';
    //     }
    // } else {
    //     $statusOutput = '<span class="inline-block label'.(!$this->_instance->input->post('status') ? ' pointer lead-status' : '').' label-' . (empty($aRow['color']) ? 'default': '') . '" style="color:' . $aRow['color'] . ';border:1px solid ' . $aRow['color'] . '">' . $aRow['status_name'] . '</span>';
    // }

    $name = stripcslashes($aRow['tblleads.name']); //str_replace("'", "",$aRow['name']);

    $leadname = "";

    $leadname .= '<div class="eventDet_blk"><div class="lead-pimg">'.lead_profile_image($aRow['id'], array("lead-profile-image-small")).'</div><div class="lead-det"><div class="eventname"><a href="'.admin_url('leads/dashboard/'.$aRow['id']).'">'.$name.'</a></div></div>';
    $leadname .= "<div class='eventtypename'><i class='fa fa-tty'></i> ".$aRow['eventtypename']."</div></div>";
    $row[] =  $leadname;

    $statuscontent = '<div class="divInline_blk"> <span class="leadstatuscolor" style="background-color:' . $aRow['color'] . ';"></span>';
    $statuscontent .= '<select lead_id="'.$aRow['id'].'" class="selectpicker leadstatus">';
    foreach ($statuses as $svalue) {
        $selected = "";
        if($aRow['statusid'] == $svalue['id']){
            $selected = "selected='selected'";
        }else{
            $selected = "";
        }
        $statuscontent .= '<option  value="'.$svalue['id'].'" '.$selected.'>'.$svalue['name'].'</option>';
    }
    $statuscontent .= '</select> </div>';
    $row[] = $statuscontent;

    //$row[] =  $aRow['assigned_name'];
    $assignedOutput = '';

    if (count($leadAssignees) > 0) {
        $count = 1;
        $assignee=1;
        $moreAssigned="<div class='moreassignee hide'>";
        foreach ($leadAssignees as $leadAssignee){
            if(count($leadAssignees) > 2 && $count > 2){
                $full_name = $leadAssignee->firstname." ".$leadAssignee->lastname;
                $moreAssigned .= '<a data-toggle="tooltip" title="'.$full_name.'" href="javascript:void(0)">'.staff_profile_image($leadAssignee->staffid, array(
                        'staff-profile-image-small'
                    )) . '<span class="">'.$full_name.'</span></a>';
            }
            $count++;
        }
        $moreAssigned.="</div>";
        foreach ($leadAssignees as $leadAssignee){
            $full_name = $leadAssignee->firstname." ".$leadAssignee->lastname;
            $assignedOutput .= '<a data-toggle="tooltip" title="'.$full_name.'" href="javascript:void(0)">'.staff_profile_image($leadAssignee->staffid, array(
                    'staff-profile-image-small'
                )) . '</a>';
            // For exporting
            $assignedOutput .= '<span class="hide">'.$full_name.'</span>';
            if($assignee ==2 && count($leadAssignees) > 2){
                $assignedOutput .= '<a href="javascript:void(0)" class="assigneemore">';
                $assignedOutput .='<span class="no-img staff-profile-image-small" style="background-color:#ccc">+'.(count($leadAssignees)-2).'</span>';
                $assignedOutput .= '</a>';
                $assignedOutput .=$moreAssigned;
                break;
            }
            $assignee++;
        }
    }

    $row[] = $assignedOutput;

    // $row[] = $aRow['eventtypename'];

    $row[] = ($aRow['eventinquireon'] == '0000-00-00 00:00:00' || !is_date($aRow['eventinquireon']) ? '' : '<span>'._dt($aRow['eventinquireon']).'</span>');


    //$row[] = $aRow['source_name'];

    //$row[] = '<span data-toggle="tooltip" data-title="'._dt($aRow['dateadded']).'">'.time_ago($aRow['dateadded']).'</span>';

    // Custom fields add values
    foreach($customFieldsColumns as $customFieldColumn){
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $hook_data = do_action('leads_table_row_data', array(
        'output' => $row,
        'row' => $aRow
    ));

    $row = $hook_data['output'];
    $options = "<div><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";

    //$options .= icon_btn('leads/dashboard/' . $aRow['id'], 'eye', 'btn-success', array('title'=>'View Dashboard'));
    $options.='<li><a href='.admin_url().'leads/dashboard/'.$aRow['id'].' class="" title="View Dashboard"><i class="fa fa-eye"></i><span>View</span></a></li>';
    if (has_permission('leads', '', 'edit')){
        //$options .= icon_btn('leads/lead/' . $aRow['id'], 'pencil-square-o');
        $options.='<li><a href='.admin_url().'leads/lead/'.$aRow['id'].' class="" title="Edit"><i class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>';
    } else {
        $options .= "";
    }

    if (has_permission('leads', '', 'delete')) {
        //$options .= icon_btn('leads/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
        $options.='<li><a href='.admin_url().'leads/delete/'.$aRow['id'].' class="_delete" title="Delete"><i class="fa fa-remove"></i><span>Delete</span></a></li>';
    }
    $options.="</ul></div>";
    $row[] = $options;
    // $row['DT_RowId'] = 'lead_'.$aRow['id'];

    // if ($aRow['assigned'] == get_staff_user_id()) {
    //     $row['DT_RowClass'] = 'alert-info';
    // }

    $output['aaData'][] = $row;
}
