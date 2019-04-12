<?php
if ($project['status'] == $status['id']) {
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
    $CI->db->where('pintype', 'Project');
    $CI->db->where('pintypeid', $project['id']);
    $result = $CI->db->get()->row();
    $projectAssignees = get_project_assignee($project['id']);
    $assignedOutput = '';

    if (count($projectAssignees) > 0) {
        $count = 1;
        $assignee = 1;
        $moreAssigned = "<div class='moreassignee hide'>";
        foreach ($projectAssignees as $projectAssignee) {
            if (count($projectAssignees) > 2 && $count > 2) {
                $full_name = $projectAssignee->firstname . " " . $projectAssignee->lastname;
                $moreAssigned .= '<a data-toggle="tooltip" title="' . $full_name . '" href="javascript:void(0)">' . staff_profile_image($projectAssignee->staffid, array(
                        'staff-profile-image-small'
                    )) . '<span class="">' . $full_name . '</span></a>';
            }
            $count++;
        }
        $moreAssigned .= "</div>";
        foreach ($projectAssignees as $projectAssignee) {
            $full_name = $projectAssignee->firstname . " " . $projectAssignee->lastname;
            $assignedOutput .= '<a data-toggle="tooltip" title="' . $full_name . '" href="javascript:void(0)">' . staff_profile_image($projectAssignee->staffid, array(
                    'staff-profile-image-small'
                )) . '</a>';
            // For exporting
            $assignedOutput .= '<span class="hide">' . $full_name . '</span>';
            if ($assignee == 2 && count($projectAssignees) > 2) {
                $assignedOutput .= '<a href="javascript:void(0)" class="assigneemore">';
                $assignedOutput .= '<span class="no-img staff-profile-image-small" style="background-color:#ccc">+' . (count($projectAssignees) - 2) . '</span>';
                $assignedOutput .= '</a>';
                $assignedOutput .= $moreAssigned;
                break;
            }
            $assignee++;
        }
    }
    $venue_loc = "";
    if ($project['venueid'] > 0) {
        $venue = get_vanue_data($project['venueid']);
        $venue_loc = $venue->venuename . " (" . $venue->venuecity . ")";
    }
    ?>
    <li data-project-id="<?php echo $project['id']; ?>"
        class="col-sm-6 col-lg-4 kanban-card-block kanban-card <?php echo $class ?>">
        <div class="panel-body card-body">
            <div class="row">

                <div class="col-xs-11 card-name">
                    <div class="carddate-block">
                        <div class="card_date"
                             title="<?php echo date('Y', strtotime($project['eventstartdatetime'])) ?>">
                            <div class="card_month">
                                <small><?php echo date('M', strtotime($project['eventstartdatetime'])) ?></small>
                            </div>
                            <div class="card_d">
                                <strong><?php echo date('d', strtotime($project['eventstartdatetime'])) ?></strong>
                            </div>
                            <div class="card_day">
                                <small><?php echo date('D', strtotime($project['eventstartdatetime'])) ?></small>
                            </div>
                        </div>

                        <?php if (date('Y', strtotime($project['eventstartdatetime'])) > date('Y')) { ?>
                            <div class="card_year">
                                <small><?php echo date('Y', strtotime($project['eventstartdatetime'])) ?></small>
                            </div>
                        <?php } ?>
                    </div>
                    <?php echo project_profile_image($project['id'], array(
                        'project-profile-image-xs'
                    )); ?>
                    <span class="leadNameTitle"><a
                                href="<?php echo admin_url('projects/dashboard/' . $project['id']); ?>"><?php echo $project['project_name']; ?></a></span>
                    <span class="lead-bold"><i class="fa fa-book"></i> <?php echo $project['eventtypename']; ?></span>
                    <span class="lead-date"><?php echo _time($project['eventstartdatetime']); ?> - <?php echo _time($project['eventenddatetime']); ?></span>
                </div>
                <div class="col-xs-1 text-muted">
                    <div class="show-act-block"><?php
                        $options = "<div><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
                        $options .= '<li><a href=' . admin_url() . 'projects/dashboard/' . $project['id'] . ' class="" title="View Dashboard"><i class="fa fa-eye"></i><span>View</span></a></li>';
                        if (has_permission('leads', '', 'edit')) {
                            $options .= '<li><a href=' . admin_url() . 'projects/project/' . $project['id'] . ' class="" title="Edit"><i class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>';
                        } else {
                            $options .= "";
                        }

                        /*if (has_permission('leads', '', 'delete')) {
                            $options .= '<li><a href=' . admin_url() . 'projects/delete/' . $project['id'] . ' class="_delete" title="Delete"><i class="fa fa-remove"></i><span>Delete</span></a></li>';
                        }*/
                        $options .= "</ul></div>";
                        echo $options;
                        ?></div>
                    <div class="lead-pin-block">
                        <i class="fa fa-fw fa-thumb-tack project-pin <?php echo isset($result->pinned) ? "pinned" : ""; ?>"
                           title="<?php echo isset($result->pinned) ? "Unpin from Home" : "Pin to Home"; ?>"
                           id="<?php echo $project['id'] ?>"
                           project_id="<?php echo $project['id'] ?>"></i>
                    </div>
                </div>
                <!--<div class="col-md-6 text-right text-muted">
                    <small class="text-dark">
                        <?php /*echo _l('projects_event_date'); */ ?> :
                        <span class="project-bold"><?php /*echo _dt($project['eventstartdatetime']); */ ?></span>
                    </small>
                    <br/>
                    <?php
                /*                    $total_notes = total_rows('tblnotes', array(
                                        'rel_id' => $project['id'],
                                        'rel_type' => 'project'
                                    ));
                                    */ ?>
                    <span class="mright5 mtop5 inline-block text-muted" data-toggle="tooltip" data-placement="left"
                          data-title="<?php /*echo _l('projects_canban_notes', $total_notes); */ ?>">
               <i class="fa fa-sticky-note-o"></i> <?php /*echo $total_notes; */ ?>
            </span>
                    <?php
                /*                    $total_attachments = total_rows('tblfiles', array(
                                        'rel_id' => $project['id'],
                                        'rel_type' => 'project'
                                    ));
                                    */ ?>
                    <span class="mtop5 inline-block text-muted" data-placement="left" data-toggle="tooltip"
                          data-title="<?php /*echo _l('project_kan_ban_attachments', $total_attachments); */ ?>">
               <i class="fa fa-paperclip"></i>
                        <?php /*echo $total_attachments; */ ?>
            </span>
                </div>-->
                <?php
                $tags = get_tags_in($project['id'], 'project');
                if (count($tags) > 0) {
                    ?>
                    <div class="col-md-12">
                        <div class="mtop5 kanban-tags">
                            <?php echo render_tags($tags); ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="card-footer">
                <div class="lp_venue pull-left text-left">
                    <?php if ($venue_loc != "") { ?>
                        <i class="fa fa-map-marker" aria-hidden="true"></i>
                        <?php echo $venue_loc; ?>
                    <?php } ?>
                </div>
                <div class="lp_assigned_users pull-right text-right"><?php echo $assignedOutput; ?></div>
            </div>
        </div>
    </li>
<?php } ?>