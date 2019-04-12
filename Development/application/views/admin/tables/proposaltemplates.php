<?php

defined('BASEPATH') OR exit('No direct script access allowed');
$is_admin = is_admin();
$session_data = get_session_data();

$user_id = $session_data['staff_user_id'];
$user_type = $session_data['user_type'];

$brandid = get_user_session();

$lid = $this->_instance->input->get('lid');
$pid = $this->_instance->input->get('pid');
$status = isset($session_data['proposals_status_view']) ? $session_data['proposals_status_view'] : "active";
$aColumns = array(
    'tblproposaltemplates.isarchieve as isarchieve',
    'tblproposaltemplates.templateid as id',
    'tblproposaltemplates.proposal_version as version',
    'tblproposaltemplates.name as name',
    'tblproposaltemplates.status as status',
    'tblproposaltemplates.number_format as number_format',
    'tblproposaltemplates.declinedby as declinedby',
    'tblproposaltemplates.closedat as closedat',
    'tblproposaltemplates.declinedat as declinedat',
    'tblproposaltemplates.isclosed as isclosed',
    'tblproposaltemplates.closereason as closereason',
    'tblproposaltemplates.resason_comment as resason_comment',
    'tblproposaltemplates.closedby as closedby',
    'tblproposaltemplates.datecreated as datecreated',
    'tblproposaltemplates.dateupdated as dateupdated',
    '(SELECT pinid FROM tblpins WHERE tblpins.userid=' . $user_id . ' and tblpins.pintype = "Proposal" and tblpins.pintypeid = tblproposaltemplates.templateid) as pinned',


);

$sIndexColumn = "templateid";
$sTable = 'tblproposaltemplates';

$where = array();
$join = array(
    'LEFT JOIN tblproposaltemplate_feedback as pf ON pf.proposal_id = tblproposaltemplates.templateid'
);
array_push($where, ' AND tblproposaltemplates.deleted = 0');
if ($brandid > 0) {
    array_push($where, 'AND tblproposaltemplates.brandid =' . $brandid);
}
if ((isset($lid) && $lid > 0) || (isset($pid) && $pid > 0)) {
    //array_push($join, 'LEFT JOIN tblproposalrelation as pr ON pr.proposal_id = tblproposaltemplates.templateid');
    if (isset($lid) && $lid > 0) {
        array_push($where, 'AND tblproposaltemplates.rel_type="lead" AND tblproposaltemplates.rel_id =' . $lid);
    } else {
        array_push($where, 'AND tblproposaltemplates.rel_type="project" AND tblproposaltemplates.rel_id =' . $pid);
    }
} else {
    array_push($where, 'AND tblproposaltemplates.is_template=1');
    $url = "proposaltemplates/proposal/";
}
if ($status == "active") {
    $isarchieve = 0;
} else {
    $isarchieve = 1;
}
array_push($where, 'AND (tblproposaltemplates.isarchieve=' . $isarchieve . ')');
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('templateid'));
$output = $result['output'];
$rResult = $result['rResult'];

$CI =& get_instance();
foreach ($rResult as $aRow) {
    if (isset($lid) && $lid > 0) {
        $url = "proposaltemplates/proposal/" . $aRow['templateid'] . "?lid=" . $lid;
        $viewurl = site_url() . "proposal/view/" . $aRow['templateid'] . "?lid=" . $lid;
        $eventimage = lead_profile_image($lid, array('profile-image-small'));
        $eventname = get_lead_col_by_id($lid, 'name');
        $eventdate = get_lead_col_by_id($lid, 'eventstartdatetime');
        $eventdate = date('M j, Y', strtotime($eventdate));

        $activeurl = "proposaltemplates/updatestatus/active/" . $aRow['templateid'] . "?lid=" . $lid;
        $archiveurl = "proposaltemplates/updatestatus/archive/" . $aRow['templateid'] . "?lid=" . $lid;
        $rel_id = $lid;
    } elseif (isset($pid) && $pid > 0) {
        $url = "proposaltemplates/proposal/" . $aRow['templateid'] . "?pid=" . $pid;
        $viewurl = site_url() . "proposal/view/" . $aRow['templateid'] . "?pid=" . $pid;
        $eventimage = project_profile_image($pid, array('profile-image-small'));
        $eventname = get_project_col_by_id($pid, "name");
        $eventdate = get_project_col_by_id($pid, "eventstartdatetime");
        $eventdate = date('M j, Y', strtotime($eventdate));
        $activeurl = "proposaltemplates/updatestatus/active/" . $aRow['templateid'] . "?pid=" . $pid;
        $archiveurl = "proposaltemplates/updatestatus/archive/" . $aRow['templateid'] . "?pid=" . $pid;
        $rel_id = $pid;
    } else {
        $url = "proposaltemplates/proposal/" . $aRow['templateid'];
        $viewurl = site_url() . "proposal/view/" . $aRow['templateid'];
        $activeurl = "proposaltemplates/updatestatus/active/" . $aRow['templateid'];
        $archiveurl = "proposaltemplates/updatestatus/archive/" . $aRow['templateid'];
        $eventimage = "";
        $eventname = "";
        $eventdate = "";
        $rel_id = 01;
    }

    if (isset($aRow)) {
        $nextproposalnumber = $aRow['version'];
        $format = $aRow['number_format'];
    } else {
        $nextproposalnumber = get_brand_option('next_proposal_number');
        $format = get_brand_option('invoice_number_format');
    }
    $pad_length = 2;
    if ($format == 1) {
        // Number based
        $prefix = "";
        $pad_length = 6;
    } else if ($format == 2) {
        $prefix = date('Y', strtotime($aRow['datecreated']));

    } else if ($format == 3) {
        $prefix = date('Ymd', strtotime($aRow['datecreated']));
    } else if ($format == 4) {
        if (isset($eventdate) && !empty($eventdate)) {
            $event_date = date('Ymd', strtotime($eventdate));
            $prefix = $event_date . "/" . str_pad($rel_id, $pad_length, '0', STR_PAD_LEFT) . "/";
        } else {
            $event_date = date('Ymd', strtotime($aRow['datecreated']));
            $prefix = $event_date . "/" . str_pad($aRow['id'], $pad_length, '0', STR_PAD_LEFT) . "/";
        }
    } else {
        $prefix = date('Ymd', strtotime($aRow['datecreated']));
    }

    $proposalversion = $prefix . str_pad($nextproposalnumber, $pad_length, '0', STR_PAD_LEFT);

    if ($aRow['isclosed'] == 1) {
        $aRow['status'] = "closed";
    }
    $CI->db->where('proposal_id', $aRow['templateid']);
    $feedback = $CI->db->get('tblproposaltemplate_feedback')->row();
    $signed = 0;
    if (!empty($feedback)) {
        $signed = $feedback->total_signed;
    }
    $row = array();
    $row[] = "";
    if ($aRow['pinned'] != "" & $aRow['pinned'] > 0) {
        $row[] = '<i class="fa fa-fw fa-thumb-tack pinned proposal-pin" title="Unpin from home" id="' . $aRow['id'] . '" proposal_id="' . $aRow['id'] . '"></i>';
    } else {
        $row[] = '<i class="fa fa-fw fa-thumb-tack proposal-pin" title="Pin to home" id="' . $aRow['id'] . '" proposal_id="' . $aRow['id'] . '"></i>';
    }
    $row[] = "<i class='fa fa-file-powerpoint-o'></i><a href='javascript:void(0)'>P-" . $proposalversion . "</a>";
    $row[] = "<strong>" . $aRow['name'] . "</strong>";
    /*$proposal_image_section = proposaltemplate_banner($aRow['templateid'], array('banner', 'img-responsive', 'proposaltemplate-profile-image-thumb'), 'thumb', array('width' => 100));
    $row[] = $proposal_image_section;*/
    if ($eventname != "") {
        $row[] = "<div class='event_details'><div class='event_image project-pimg'>" . $eventimage . "</div><div class='event_name_date'><span class='event_name'>" . $eventname . "</span><span class='event_date'>" . $eventdate . "</span></div></div>";
    } else {
        $row[] = "";
    }
    $status = '<div class="proposal_status ' . $aRow['status'] . '"><span class="label label-warning s-status ' . strtolower($aRow['status']) . '">' . $aRow['status'] . '</span>';

    if ($aRow['isclosed'] == 1 || $aRow['status'] == "decline") {
        if (($aRow['isclosed'] == 1 && $aRow['status'] == "decline") || $aRow['isclosed'] == 1 && $aRow['status'] != "decline") {
            $aRow['status'] = "closed";
            $reason = $aRow['closereason'];
            $user = get_staff_full_name($aRow['closedby']);
            $date = date('M j, Y', strtotime($aRow['closedat']));
        } elseif ($aRow['status'] == "decline") {
            $reason = $aRow['resason_comment'];
            $declinedusers = json_decode($aRow['declinedby'], true);
            $user = "";
            if (!empty($declinedusers)) {
                $user = array();
                foreach ($declinedusers as $declineduser) {
                    if ($declineduser['usertype'] == "client") {
                        $user[] = get_addressbook_full_name($declineduser['userid']);
                    } else {
                        $user[] = get_staff_full_name($declineduser['userid']);
                    }
                    $user = implode(', ', $user);
                }
            }

            $date = date('M j, Y', strtotime($aRow['declinedat']));
        }
        $status .= '<div class="status_popover">
            <div class="' . strtolower($aRow['status']) . '"><strong>' . _l("reason") . '</strong><br />
            <p>' . $reason . '</p>
            <p class="dcby">' . ucfirst($aRow['status']) . ' by ' . $user . '</p>
            <p class="dcat">' . ucfirst($aRow['status']) . ' at ' . $date . '</p>
            </div>
            </div>';
    } else {
        $status .= '</div>';
    }
    $row[] = $status;
    if (!is_null($aRow['dateupdated'])) {
        $row[] = '<span class="display-block onlyDate">' . date('M j, Y', strtotime($aRow['dateupdated'])) . '</span>';
    } else {
        $row[] = '<span class="createdTxt">Created</span><span class="display-block">' . date('M j,Y', strtotime($aRow['datecreated'])) . '</span>';
    }

    $options = "
<div class='text-right mright10'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v'
                                                                                  aria-hidden='true'></i></a></div>
<div class='table_actions'>
    <ul>";

    if (has_permission('proposals', '', 'view')) {
        $options .= icon_url($viewurl, 'eye');
    }
    if (has_permission('proposals', '', 'edit')) {
        if ($aRow['isarchieve'] == 0 && $aRow['status'] == "draft") {
            $options .= icon_url($url, 'pencil-square-o');
        }
        if ($aRow['isarchieve'] == 0) {
            $options .= icon_url($archiveurl, 'archive');
        } else {
            if ($aRow['isclosed'] == 0) {
                $options .= icon_url($activeurl, 'refresh');
            }
        }

        if (($aRow['status'] != "complete") || $aRow['isclosed'] == 1) {
            if ($aRow['isclosed'] == 1) {
                $class = "_reopen";
                $type = 'recycle';
            } else {
                $class = "_close";
                $type = 'close';
            }
            $attributes = array('id' => $aRow['templateid']);
            $options .= icon_url("#", $type, $class, $attributes);
        }
    }
    if (has_permission('proposals', '', 'create')) {
        if ($aRow['isarchieve'] == 0) {
            $options .= icon_url('#', 'clone', '', array('data-toggle' => 'modal', 'data-target' => '#duplicate_proposal',
                'id' => 'duplicate_action_button', 'data-id' => $aRow['templateid'], 'onclick' => 'duplicate_proposal(this)'));
        }
    }
    if (has_permission('proposals', '', 'delete')) {
        if ($aRow['isarchieve'] == 0 && $aRow['status'] == "draft") {
            $options .= icon_url('proposaltemplates/deleteproposaltemplates/' . $aRow['templateid'], 'remove',
                '_delete');
        } else {
            $options .= "";
        }
    }

    $options .= "</ul></div>";
    $row[]=$options;
    $output['aaData'][] = $row;
}