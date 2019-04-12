<?php
if (isset($_GET['lid'])) {
    $rel_id = $_GET['lid'];
    $rel_type = 'lead';
    $rel_link = '?lid=' . $rel_id;
    $preview = '?lid=' . $rel_id;
    $plttl = "Leads";
    $pllink = 'leads';

} elseif (isset($_GET['pid'])) {
    $rel_id = $_GET['pid'];
    $rel_type = 'project';
    $rel_link = '?pid=' . $rel_id;
    $preview = '?pid=' . $rel_id;
    $plttl = "Projects";
    $pllink = 'projects';
} else {
    $rel_id = "";
    $rel_type = '';
    $rel_link = "";
    $preview = "";
    $plttl = "";
}

if (isset($proposal['rel_type']) && $proposal['rel_id'] > 0) {
    $event = get_event_name($proposal['rel_type'], $proposal['rel_id']);
    $proposal['eventtypename'] = isset($event->name) ? $event->name : "";
}
/*if ($proposal['status'] == $status['statusid']) {*/
/*echo "<pre>";
print_r($proposal);*/

$session_data = get_session_data();
$user_id = $session_data['staff_user_id'];

$class = "";
if ($count <= 3) {
    $class = "first_row";
}
$session_data = get_session_data();
$user_id = $session_data['staff_user_id'];
$CI =& get_instance();
$CI->db->select('pinid as pinned');
$CI->db->from('tblpins');
$CI->db->where('userid', $user_id);
$CI->db->where('pintype', 'Proposal');
$CI->db->where('pintypeid', $proposal['templateid']);
$result = $CI->db->get()->row();
$banner_path = base_url() . "assets/images/default_banner.jpg";

?>
<?php
if (isset($proposal)) {
    $nextproposalnumber = $proposal['proposal_version'];
    $format = $proposal['number_format'];
} else {
    $nextproposalnumber = get_brand_option('next_proposal_number');
    $format = get_brand_option('invoice_number_format');
}
/*if(!isset($rel_id)){
    $rel_id=$proposal->rel_id;
}*/
$pad_length = 2;
if ($format == 1) {
    // Number based
    $prefix = "";
    $pad_length = 6;
} else if ($format == 2) {
    if (isset($proposal) && !empty($proposal['datecreated'])) {
        $prefix = date('Y', strtotime($proposal['datecreated']));
    } else {
        $prefix = date('Y');
    }

} else if ($format == 3) {
    if (isset($proposal) && !empty($proposal['datecreated'])) {
        $prefix = date('Ymd', strtotime($proposal['datecreated']));
    } else {
        $prefix = date('Ymd');
    }
} else if ($format == 4) {
    if (isset($rel_content) && !empty($rel_content)) {
        $event_date = date('Ymd', strtotime($rel_content->eventstartdatetime));
        $prefix = $event_date . "/" . str_pad($rel_content->id, $pad_length, '0', STR_PAD_LEFT) . "/";
    } else {
        if (isset($proposal) && !empty($proposal->datecreated)) {
            $event_date = date('Ymd', strtotime($proposal->datecreated));
            $prefix = $event_date . "/" . str_pad($proposal->templateid, $pad_length, '0', STR_PAD_LEFT) . "/";
        }else{
            $event_date = date('Ymd');
            $prefix = $event_date . "/01/";
        }
    }
} else {
    if (isset($proposal) && !empty($proposal['datecreated'])) {
        $prefix = date('Ymd', strtotime($proposal['datecreated']));
    } else {
        $prefix = date('Ymd');
    }
}

$proposalversion = $prefix . str_pad($nextproposalnumber, $pad_length, '0', STR_PAD_LEFT);
if ($proposal['isclosed'] == 1) {
    $proposal['status'] = "closed";
}
?>
    <li data-proposal-id="<?php echo $proposal['templateid']; ?>"
        class="col-sm-6 col-lg-4 kanban-card-block kanban-card <?php echo $class ?>">
        <div class="proposal_card_image">
            <?php if (!empty($proposal['banner'])) {
                $banner_path = get_upload_path_by_type('proposaltemplate') . $proposal['templateid'] . "/" . $proposal['banner'];
                if (file_exists($banner_path)) {
                    $banner_path = base_url() . "uploads/proposals_images/banner/" . $proposal['templateid'] . "/" . $proposal['banner'];
                }
            }
            ?>
            <img src="<?php echo $banner_path ?> "/>
        </div>
        <div class="panel-body card-body">
            <div class="row">
                <div class="pin-block">
                    <i class="fa fa-fw fa-thumb-tack proposal-pin <?php echo isset($result->pinned) ? "pinned" : ""; ?>"
                       title="<?php echo isset($result->pinned) ? "Unpin from Home" : "Pin to Home"; ?>"
                       id="<?php echo $proposal['templateid']; ?>"
                       proposal_id="<?php echo $proposal['templateid']; ?>">
                    </i>
                </div>
                <div class="col-xs-12 card-name">
                    <div class="proposal-body text-center">
                        <div class="proposal_icon"><i class="fa fa-file-text-o"></i></div>
                        <div class="proposal_number">
                            <span><?php echo "P-" . $proposalversion; ?></span>
                        </div>
                        <div class="proposal_title text-center">
                            <strong><?php echo $proposal['name']; ?></strong>
                        </div>
                    </div>

                    <?php if ((isset($_GET['lid']) && $_GET['lid'] > 0) || (isset($_GET['pid']) && $_GET['pid'] > 0)) { ?>
                        <div class="proposal_event">
                            <?php
                            if ($proposal['rel_type'] == "project") {
                                $eventimage = project_profile_image($proposal['rel_id'], array('profile-image-small'));
                                $eventname = get_project_col_by_id($proposal['rel_id'], "name");
                                $eventdate = get_project_col_by_id($proposal['rel_id'], "eventstartdatetime");
                            } else {
                                $eventimage = lead_profile_image($proposal['rel_id'], array('profile-image-small'));
                                $eventname = get_lead_col_by_id($proposal['rel_id'], 'name');
                                $eventdate = get_lead_col_by_id($proposal['rel_id'], 'eventstartdatetime');
                            }

                            ?>
                            <div class="event_iamge inline-block">
                                <?php echo $eventimage; ?>
                            </div>
                            <div class="event_details inline-block">

                            <span class="event_name display-block">
                                <strong><?php echo $eventname; ?></strong>
                            </span>
                                <span class="event_date display-block">
                                <?php echo date('l, F d, Y', strtotime($eventdate)); ?>
                            </span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="right-links">
                    <div class="show-act-block"><?php
                        $options = "<div><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
                        $options .= '<li><a href=' . site_url() . 'proposal/view/' . $proposal['templateid'] . $rel_link . ' class=""><i class="fa fa-eye"></i><span>View</span></a></li>';
                        /*if (has_permission('proposal', '', 'edit')) {*/
                        if ($proposal['status'] == "draft" && $proposal['isarchieve'] == 0) {
                            $options .= '<li><a href=' . admin_url() . 'proposaltemplates/proposal/' . $proposal['templateid'] . $rel_link . ' class=""><i class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>';
                        }
                        if ($proposal['isarchieve'] == 0) {
                            $options .= icon_url('#', 'clone', '', array('data-toggle' => 'modal', 'data-target' => '#duplicate_proposal', 'id' => 'duplicate_action_button', 'data-id' => $proposal['templateid'], 'onclick' => 'duplicate_proposal(this)'));
                        }
                        /*if($proposal['status']=="draft" || $proposal['status']=="sent" || $proposal['status']=="accepted"){
                            $options .= '<li><a href=' . admin_url() . 'proposaltemplates/updatestatus/archive/' . $proposal['templateid'].$rel_link. ' class=""><i class="fa fa-archive"></i><span>Archive</span></a></li>';
                        }*/
                        if ($proposal['isarchieve'] == 0) {
                            //$options .= icon_url($archiveurl, 'archive');
                            $options .= '<li><a href=' . admin_url() . 'proposaltemplates/updatestatus/archive/' . $proposal['templateid'] . $rel_link . ' class=""><i class="fa fa-archive"></i><span>Archive</span></a></li>';
                        } else {
                            if ($proposal['isclosed'] == 0) {
                                $options .= '<li><a href=' . admin_url() . 'proposaltemplates/updatestatus/active/' . $proposal['templateid'] . $rel_link . ' class=""><i class="fa fa-refresh"></i><span>Active</span></a></li>';
                                //$options .= icon_url($activeurl, 'refresh');
                            }
                        }
                        if ((/*$aRow['isarchieve'] == 0 && */
                                $proposal['status'] != "complete") || $proposal['isclosed'] == 1) {
                            if ($proposal['isclosed'] == 1) {
                                $class = "_reopen";
                                $type = 'recycle';
                            } else {
                                $class = "_close";
                                $type = 'close';
                            }
                            $attributes = array('id' => $proposal['templateid']);
                            $options .= icon_url("#", $type, $class, $attributes);
                        }
                        if ($proposal['status'] == "draft" && $proposal['isarchieve'] == 0) {
                            $options .= icon_url('proposaltemplates/deleteproposaltemplates/' . $proposal['templateid'], 'remove', '_delete');
                        }
                        /*} else {
                            $options .= "";
                        }*/
                        $options .= "</ul></div>";
                        echo $options;
                        ?>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="card-footer">
                    <div class="proposal_status">
                        <span class="label label-warning s-status <?php echo strtolower($proposal['status']); ?>"><?php echo $proposal['status']; ?></span>
                        <?php
                        if ($proposal['isclosed'] == 1 || $proposal['status'] == "decline") {
                            if (($proposal['isclosed'] == 1 && $proposal['status'] == "decline") || $proposal['isclosed'] == 1 && $proposal['status'] != "decline") {
                                $proposal['status'] = "closed";
                                $reason = $proposal['closereason'];
                                $user = get_staff_full_name($proposal['closedby']);
                                $date = date('M j, Y', strtotime($proposal['closedat']));
                            } else/*if ($proposal['status'] == "decline")*/ {
                                $reason = $proposal['resason_comment'];
                                $declinedusers = json_decode($proposal['declinedby'], true);
                                $user = array();
                                if(!empty($declinedusers)){
                                    foreach ($declinedusers as $declineduser){
                                        if($declineduser['usertype']=="client"){
                                            $user[] = get_addressbook_full_name($declineduser['userid']);
                                        }else{
                                            $user[] = get_staff_full_name($declineduser['userid']);
                                        }
                                        $user=implode(', ',$user);
                                    }
                                }
                                $date = date('M j, Y', strtotime($proposal['declinedat']));
                            }
                            echo $status = '<div class="status_popover">
            <div class="' . strtolower($proposal['status']) . '"><strong>' . _l("reason") . '</strong><br />
            <p>' . $reason . '</p>
            <p class="dcby">' . ucfirst($proposal['status']) . ' by ' . $user . '</p>
            <p class="dcat">' . ucfirst($proposal['status']) . ' at ' . $date . '</p>
            </div>
            </div>';
                        }
                        ?>
                    </div>
                    <div class="proposal_date">
                        <?php
                        if (!is_null($proposal['dateupdated'])) { ?>
                            <span class="display-block onlyDate">
                            <?php echo date('M j, Y', strtotime($proposal['dateupdated'])); ?>
                        </span>
                        <?php } else { ?>
                            <span class="createdTxt">Created</span>
                            <span class="display-block"><?php echo date('M j,Y', strtotime($proposal['datecreated'])); ?></span>
                        <?php } ?>
                    </div>
                </div>
            </div>
    </li>
<?php //} ?>