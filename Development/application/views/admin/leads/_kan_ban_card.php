<?php
$lead_already_client_tooltip = '';
if (total_rows('tblclients', array(
    'leadid' => $lead['id']
))) {
    $lead_already_client_tooltip = ' data-toggle="tooltip" title="' . _l('lead_have_client_profile') . '"';
}
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
$CI->db->where('pintype', 'Lead');
$CI->db->where('pintypeid', $lead['id']);
$result = $CI->db->get()->row();
$leadAssignees = get_lead_assignee($lead['id']);
$assignedOutput = '';
if (count($leadAssignees) > 0) {
    $count = 1;
    $assignee = 1;
    $moreAssigned = "<div class='moreassignee hide'>";
    foreach ($leadAssignees as $leadAssignee) {
        if (count($leadAssignees) > 2 && $count > 2) {
            $full_name = $leadAssignee->firstname . " " . $leadAssignee->lastname;
            $moreAssigned .= '<a data-toggle="tooltip" title="' . $full_name . '" href="javascript:void(0)">' . staff_profile_image($leadAssignee->staffid, array(
                    'staff-profile-image-small'
                )) . '<span class="">' . $full_name . '</span></a>';
        }
        $count++;
    }
    $moreAssigned .= "</div>";
    foreach ($leadAssignees as $leadAssignee) {
        $full_name = $leadAssignee->firstname . " " . $leadAssignee->lastname;
        $assignedOutput .= '<a data-toggle="tooltip" title="' . $full_name . '" href="javascript:void(0)">' . staff_profile_image($leadAssignee->staffid, array(
                'staff-profile-image-small'
            )) . '</a>';
        // For exporting
        $assignedOutput .= '<span class="hide">' . $full_name . '</span>';
        if ($assignee == 2 && count($leadAssignees) > 2) {
            $assignedOutput .= '<a href="javascript:void(0)" class="assigneemore">';
            $assignedOutput .= '<span class="no-img staff-profile-image-small" style="background-color:#ccc">+' . (count($leadAssignees) - 2) . '</span>';
            $assignedOutput .= '</a>';
            $assignedOutput .= $moreAssigned;
            break;
        }
        $assignee++;
    }
}
if ($lead['status'] == $status['id']) { ?>
    <li data-lead-id="<?php echo $lead['id']; ?>"<?php echo $lead_already_client_tooltip; ?>
        class="col-sm-6 col-lg-4 kanban-card-block kanban-card <?php echo $class ?> <?php if (total_rows('tblclients', array('leadid' => $lead['id'])) > 0 && get_option('lead_lock_after_convert_to_customer') == 1 && !$is_admin) {
            echo 'not-sortable';
        } ?>">
        <div class="panel-body card-body">
            <div class="row">
                <div class="col-xs-11 card-name">
                    <div class="carddate-block">
                        <div class="card_date">
                            <div class="card_month">
                                <small><?php echo date('M', strtotime($lead['eventstartdatetime'])) ?></small>
                            </div>
                            <div class="card_d">
                                <strong><?php echo date('d', strtotime($lead['eventstartdatetime'])) ?></strong>
                            </div>
                            <div class="card_day">
                                <small><?php echo date('D', strtotime($lead['eventstartdatetime'])) ?></small>
                            </div>
                        </div>

                        <?php if (date('Y', strtotime($lead['eventstartdatetime'])) > date('Y')) { ?>
                            <div class="card_year">
                                <small><?php echo date('Y', strtotime($lead['eventstartdatetime'])) ?></small>
                            </div>
                        <?php } ?>
                    </div>
                    <?php echo lead_profile_image($lead['id'], array('lead-profile-image-xs')); ?>
                    <span class="leadNameTitle">
                        <a href="<?php echo admin_url('leads/dashboard/' . $lead['id']); ?>">
                            <?php echo $lead['lead_name']; ?>
                        </a>
                    </span>
                    <span class="lead-bold"><i class="fa fa-tty"></i> <?php echo $lead['eventtypename']; ?></span>
                    <span class="lead-date"><?php echo _time($lead['eventstartdatetime']) ?> - <?php echo _time($lead['eventenddatetime']) ?></span>
                </div>
                <div class="col-xs-1 text-muted">


                    <!--<small class="text-dark"><?php /*echo _l('task_assigned'); */ ?>: <span
                                class="lead-bold"><?php /*echo get_staff_full_name($lead['assigned']); */ ?></span></small>-->
                    <div class="show-act-block"><?php
                        $options = "<div><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";

                        //$options .= icon_btn('leads/dashboard/' . $aRow['id'], 'eye', 'btn-success', array('title'=>'View Dashboard'));
                        $options .= '<li><a href=' . admin_url() . 'leads/dashboard/' . $lead['id'] . ' class="" title="View Dashboard"><i class="fa fa-eye"></i><span>View</span></a></li>';
                        if (has_permission('leads', '', 'edit')) {
                            //$options .= icon_btn('leads/lead/' . $aRow['id'], 'pencil-square-o');
                            $options .= '<li><a href=' . admin_url() . 'leads/lead/' . $lead['id'] . ' class="" title="Edit"><i class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>';
                        } else {
                            $options .= "";
                        }

                        if (has_permission('leads', '', 'delete')) {
                            //$options .= icon_btn('leads/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
                            $options .= '<li><a href=' . admin_url() . 'leads/delete/' . $lead['id'] . ' class="_delete" title="Delete"><i class="fa fa-remove"></i><span>Delete</span></a></li>';
                        }
                        $options .= "</ul></div>";
                        echo $options;
                        ?></div>
                    <div class="lead-pin-block">
                        <i class="fa fa-fw fa-thumb-tack lead-pin <?php echo isset($result->pinned) ? "pinned" : ""; ?>"
                           title="<?php echo isset($result->pinned) ? "Unpin from Home" : "Pin to Home"; ?>"
                           id="<?php echo $lead['id'] ?>"
                           lead_id="<?php echo $lead['id'] ?>"></i>
                    </div>
                    <div class="checkbox"><input type="checkbox" value="<?php echo $lead['id'] ?>"><label></label></div>
                </div>
                <div class="col-md-1 text-right text-muted">
                    <!--<small class="text-dark"><?php /*echo _l('lead_event_date'); */ ?>: <span
                                class="lead-bold"><?php /*echo _dt($lead['eventstartdatetime']); */ ?></span></small>
                    <br/>-->
                    <?php //if(is_date($lead['lastcontact']) && $lead['lastcontact'] != '0000-00-00 00:00:00'){ ?>
                    <!-- <small class="text-dark"><?php //echo _l('leads_dt_last_contact'); ?>: <span class="lead-bold"><?php //echo time_ago($lead['lastcontact']); ?></span></small><br /> -->
                    <?php //} ?>
                    <?php /*$total_notes = total_rows('tblnotes', array(
                        'rel_id' => $lead['id'],
                        'rel_type' => 'lead',
                    )); */ ?><!--
                    <span class="mright5 mtop5 inline-block text-muted" data-toggle="tooltip" data-placement="left"
                          data-title="<?php /*echo _l('leads_canban_notes', $total_notes); */ ?>">
            <i class="fa fa-sticky-note-o"></i> <?php /*echo $total_notes; */ ?>
            </span>
                    <?php /*$total_attachments = total_rows('tblfiles', array(
                        'rel_id' => $lead['id'],
                        'rel_type' => 'lead',
                    )); */ ?>
                    <span class="mtop5 inline-block text-muted" data-placement="left" data-toggle="tooltip"
                          data-title="<?php /*echo _l('lead_kan_ban_attachments', $total_attachments); */ ?>">
            <i class="fa fa-paperclip"></i>
                        <?php /*echo $total_attachments; */ ?>
            </span>-->
                </div>
                <?php $tags = get_tags_in($lead['id'], 'lead');
                if (count($tags) > 0) { ?>
                    <div class="col-md-12">
                        <div class="mtop5 kanban-tags">
                            <?php echo render_tags($tags); ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="card-footer">
                <div class="lp_inquired_on pull-left text-left">
                    <i class="fa fa-inbox" aria-hidden="true"></i>
                    <?php echo time_ago($lead['eventinquireon']); ?>
                </div>
                <div class="lp_assigned_users pull-right text-right"><?php echo $assignedOutput; ?></div>
            </div>
        </div>
    </li>
<?php }
