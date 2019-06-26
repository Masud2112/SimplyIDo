<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$brandid = get_user_session();
$session_data = get_session_data();
$is_sido_admin = $session_data['is_sido_admin'];
$is_admin = is_admin();
$user_id = $session_data['staff_user_id'];
$lid = $this->_instance->input->get('lid');
$pid = $this->_instance->input->get('pid');
$eid = $this->_instance->input->get('eid');
$aColumns = array(
    'name',
    'start_date',
    'end_date',
    /*'location',*/
    '(SELECT location_name FROM tblmeetinglocations WHERE tblmeetinglocations.locationid = tblmeetings.location) as location',
    '(SELECT name FROM tblmeetingstatus WHERE tblmeetingstatus.statusid = tblmeetings.status)',
    '(SELECT pinid FROM tblpins WHERE tblpins.userid=' . $user_id . ' and tblpins.pintype = "Meeting" and tblpins.pintypeid = tblmeetings.meetingid) as pinned',
    '(SELECT name FROM tblmeetingstatus WHERE tblmeetingstatus.statusid=tblmeetings.status) as status',
    '(SELECT COUNT(*) FROM tblmeetingreminders WHERE tblmeetingreminders.meetingid=tblmeetings.meetingid) as reminders'//,
);

$sIndexColumn = "meetingid";
$sTable = 'tblmeetings';
//Added on 10/03 By Purvi
$where = array();
array_push($where, ' AND deleted = 0');
if ($brandid > 0) {
    array_push($where, 'AND brandid =' . $brandid);
}

$join = array();
//Added on 10/30 by Avni 
if (isset($lid)) {
    array_push($where, ' AND rel_id =' . $lid);
    array_push($where, ' AND rel_type = "lead"');
}

//Added on 12/20/2017 by Purvi 
if (isset($pid)) {
    $this->_instance->db->select('id');
    $this->_instance->db->where('(parent = ' . $pid . ' OR id = ' . $pid . ')');
    $this->_instance->db->where('deleted', 0);
    $related_project_ids = $this->_instance->db->get('tblprojects')->result_array();
    $related_project_ids = array_column($related_project_ids, 'id');
    if (!empty($related_project_ids)) {
        $related_project_ids = implode(",", $related_project_ids);
        array_push($where, ' AND rel_id in(' . $related_project_ids . ')');
        array_push($where, ' AND rel_type in("project", "event")');
    } else {
        array_push($where, ' AND rel_id = ' . $pid);
        array_push($where, ' AND rel_type = "project"');
    }

}

if (isset($eid)) {
    array_push($where, ' AND rel_id =' . $eid);
    array_push($where, ' AND rel_type = "event"');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('meetingid', 'rel_id', 'rel_type'));
$output = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {

    if (isset($lid)) {
        $meetinglink = admin_url('meetings/meeting/' . $aRow['meetingid'] . "?lid=" . $lid);
    } elseif (isset($pid)) {
        $meetinglink= admin_url('meetings/meeting/' . $aRow['meetingid'] . "?pid=" . $pid);
    } elseif (isset($eid)) {
        $meetinglink= admin_url('meetings/meeting/' . $aRow['meetingid'] . "?eid=" . $eid);
    } else {
        $meetinglink= admin_url('meetings/meeting/' . $aRow['meetingid']);
    }

    $this->_instance->db->select('*');
    $this->_instance->db->where('meeting_id', $aRow['meetingid']);
    $meeting_users = $this->_instance->db->get('tblmeetingusers')->result_array();
    $assignes="";
    if(count($meeting_users) > 0){
        $count = 1;
        $assignes="<div class='assined_users'>";
        foreach ($meeting_users as $meeting_user){
            if($count==3){
                $assignes.="<div class='profImgDiv more_users'>+".(count($meeting_users)-2)."</div>";
                break;
            }
            if($meeting_user['user_id'] > 0){
                $assignes.="<div class='profImgDiv'>".staff_profile_image($meeting_user['user_id'])."</div>";
            }else{
                $assignes.="<div class='profImgDiv'>".addressbook_profile_image($meeting_user['contact_id'])."</div>";
            }
            $count++;
        }
        $assignes.="<div class='hover_uers'>";
        foreach ($meeting_users as $meeting_user){
            if($meeting_user['user_id'] > 0){
                $assignes.="<div><div class='profImgDiv'>".staff_profile_image($meeting_user['user_id'])."</div>";
                $assignes.=get_staff_full_name($meeting_user['user_id'])."</div>";
            }else{
                $assignes.="<div><div class='profImgDiv'>".addressbook_profile_image($meeting_user['contact_id'])."</div>";
                $assignes.=get_addressbook_full_name($meeting_user['contact_id'])."</div>";
            }
        }
        $assignes.="</div></div>";
    }
    $row = array();
    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['meetingid'] . '"><label></label></div>';
    if ($aRow['pinned'] > 0) {
        $row[] = '<i class="fa fa-fw fa-thumb-tack meeting-pin pinned" title="Unpin from dashboard" id="' . $aRow['meetingid'] . '" meeting_id="' . $aRow['meetingid'] . '"></i>';
    } else {
        $row[] = '<i class="fa fa-fw fa-thumb-tack meeting-pin" title="Pin to dashboard" id="' . $aRow['meetingid'] . '" meeting_id="' . $aRow['meetingid'] . '"></i>';
    }
    $meeting_details = "<strong class='meetingName'><a href='".$meetinglink."'>" . $aRow['name'] . "</strong></a><b>" . _time($aRow['start_date']) . " - " . _time($aRow['end_date']) . "</b><br />" . $aRow['location'];
    $meeting = '<div class="inviteeListDate_blk"><div class="ilDate card_date_blk">';
    $meeting .= '<div class="card_date" title=' . date('Y', strtotime($aRow['start_date'])) . '><div class="card_month">';
    $meeting .= '<small>' . strtoupper(date("M", strtotime($aRow['start_date']))) . '</small>';
    $meeting .= '</div><div class="card_d"><strong>' . date('d', strtotime($aRow['start_date'])) . '</strong></div>';
    $meeting .= '<div class="card_day"><small>' . strtoupper(date('D', strtotime($aRow['start_date']))) . '</small></div>';
    $meeting .= '</div></div><div class="ild-Deta_blk">' . $meeting_details . '</div></div>';

    $row[] = $meeting;
    if ($aRow['rel_id'] > 0) {
        if ($aRow['rel_type'] == "lead") {
            $event = get_lead_col_by_id($aRow['rel_id'], 'name');
            $event_date = get_lead_col_by_id($aRow['rel_id'], 'eventstartdatetime');
            $row[] = "<div class='leadMeetingTime'><i class='fa fa-tty'></i> <span class='leadtime'> <strong> ".$event."</strong> <br />".strtoupper(date('D, M d, Y',strtotime($event_date))) . "</span></div>";
        } else {
            $event = get_project_col_by_id($aRow['rel_id'], 'name');
            $event_date = get_project_col_by_id($aRow['rel_id'], 'eventstartdatetime');
            $row[] = "<div class='leadMeetingTime'><i class='fa fa-book'></i> <span class='leadtime'> <strong> ".$event."</strong> <br />".strtoupper(date('',strtotime($event_date))) . "</span></div>";
        }
    } else {
        $row[] = "";
    }
    //$row[] = "<b>" . _time($aRow['start_date']) . " - " . _time($aRow['end_date']) . "</b>";
    $row[] = '<div class="reminders"><i class=" fa fa-bell-o"></i><span>'.$aRow['reminders'].'</span></div>';
    $row[] = '<strong class="meetingStatus">'.$aRow['status'] . '</strong>';
    $row[] = $assignes;

    /*for ($i = 4; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        $row[] = $_data;
    }*/

    $options = "<div class='text-right mright10'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";

    if (has_permission('meetings', '', 'edit')) {
        if (isset($lid)) {
            $options .= icon_url('meetings/meeting/' . $aRow['meetingid'] . "?lid=" . $lid, 'pencil-square-o');
        } elseif (isset($pid)) {
            $options .= icon_url('meetings/meeting/' . $aRow['meetingid'] . "?pid=" . $pid, 'pencil-square-o');
        } elseif (isset($eid)) {
            $options .= icon_url('meetings/meeting/' . $aRow['meetingid'] . "?eid=" . $eid, 'pencil-square-o');
        } else {
            $options .= icon_url('meetings/meeting/' . $aRow['meetingid'], 'pencil-square-o');
        }

    }

    if (has_permission('meetings', '', 'delete')) {
        $row[] = $options .= icon_url('meetings/delete/' . $aRow['meetingid'], 'remove', '_delete');
    } else {
        $row[] = $options .= "";
    }
    $row[] = $options .= "</ul></div>";
    $output['aaData'][] = $row;
}