<?php
/**
 * Added By : Purvi
 * Dt : 12/20/2017
 * Project Dashboard
 */
init_head();

$projectAssignees = get_project_assignee($project->id);
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
?>

<div id="wrapper" class="projectdashboard">
    <div class="content">

        <div class="breadcrumb">
            <?php /*if (isset($pg) && $pg == 'home') { */ ?>
            <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <?php /*} */ ?>
            <a href="<?php echo admin_url('projects'); ?>">Projects</a>
            <?php if ($project->parent > 0) { ?>
                <i class="fa fa-angle-right breadcrumb-arrow"></i>
                <a href="<?php echo admin_url('projects/dashboard/') . $project->parent; ?>"><?php echo get_project_name_by_id($project->parent); ?></a>
            <?php } ?>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>

            <span><?php echo $project->name; ?></span>
        </div>
        <h1 class="pageTitleH1"><i class="fa fa-book"></i><?php echo _l('project') ?></h1>
        <div class="clearfix"></div>
        <?php if (!empty($project->projectcoverimage)) {

            $path = get_upload_path_by_type('project_cover_image') . $project->id . '/' . $project->projectcoverimage;
            if (file_exists($path)) {
                $path = get_upload_path_by_type('project_cover_image') . $project->id . '/croppie_' . $project->projectcoverimage;
                $cover_path = base_url() . 'uploads/project_cover_images/' . $project->id . '/' . $project->projectcoverimage;
                if (file_exists($path)) {
                    $cover_path = base_url() . 'uploads/project_cover_images/' . $project->id . '/croppie_' . $project->projectcoverimage;
                }
            } else {
                $cover_path = base_url() . 'assets/images/default_banner.jpg';
            }

        } else {
            $cover_path = base_url() . 'assets/images/default_banner.jpg';
        }
        ?>
        <div class="project_cover_image">
            <img src="<?php echo $cover_path ?>" alt="Project Cover Image"/>
        </div>
        <?php /*} */ ?>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-6">

                <div class="row">
                    <div class="col-sm-4">
                        <figure class="text-center thumb-lg">
                            <div class="profileImg_blk"><?php echo project_profile_image($projectid, array('profile_image', 'img-responsive ', 'project-profile-image-thumb')); ?></div>
                        </figure>
                    </div>
                    <div class="col-sm-8">
                        <h4>
                            <?php echo isset($project->name) ? $project->name : "--"; ?>
                            <?php if (has_permission('projects', '', 'edit', true)) { ?>
                                <a data-toggle="tooltip" data-title="Edit project"
                                   href="<?php echo admin_url('projects/project/' . $projectid); ?>"
                                   class="btn btn-icon"><i
                                            class="fa fa-pencil"></i></a>
                            <?php } ?>
                        </h4>
                        <div class="card-user-info-widget panel_s btmbrd">
                            <div class="row">
                                <div class="col-sm-12 card-user-info">

                                    <div class="pull-right hide">
                                        <?php if (isset($pg) && $pg != '') { ?>
                                            <a data-toggle="tooltip" data-title="Back"
                                               href="<?php echo admin_url('calendar'); ?>"
                                               class="btn btn-default pull-right"><i class="fa fa-chevron-left"></i></a>
                                        <?php } else { ?>
                                            <a data-toggle="tooltip" data-title="Back"
                                               href="<?php echo admin_url('projects'); ?>"
                                               class="btn btn-default pull-right"><i class="fa fa-chevron-left"></i></a>
                                        <?php } ?>
                                    </div>

                                    <?php
                                    if (($project->eventstartdatetime != "")) {
                                        $eventstartdatetime = date('l, F d, Y', strtotime($project->eventstartdatetime));
                                    } else {
                                        $eventstartdatetime = "--";
                                    }

                                    if (($project->eventenddatetime != "")) {
                                        $eventenddatetime = date('l, F d, Y', strtotime($project->eventenddatetime));
                                    } else {
                                        $eventenddatetime = "--";
                                    }

                                    if ($project->pinid > 0) {
                                        $pintitle = 'Unpin from Home';
                                        $pinclass = 'pinned';
                                    } else {
                                        $pintitle = 'Pin to Home';
                                        $pinclass = "";
                                    }

                                    if (($project->assigned_name != "")) {
                                        $assigned_name = $project->assigned_name;
                                    } else {
                                        $assigned_name = "--";
                                    }

                                    if (($project->eventtypename != "")) {
                                        $eventtypename = $project->eventtypename;
                                    } else {
                                        $eventtypename = "--";
                                    }

                                    $status_name = $project->status_name;
                                    /*echo "<pre>";
                                    print_r($project);
                                    die();*/
                                    ?>
                                    <ul class="list-unstyled mb-0 text-muted email-details-list">
                                        <li class="col-12"><i
                                                    class="list-icon fa fa-book"></i><?php echo $eventtypename; ?>
                                        </li>
                                        <li class="col-12 mr-t-20"><i
                                                    class="list-icon fa fa-calendar"></i><?php echo $eventstartdatetime; ?>
                                        </li>
                                        <!--<li class="col-12"><i class="list-icon fa fa-calendar-check-o"></i>&nbsp; <?php /*echo $eventenddatetime; */ ?></li>-->
                                        <!--<li class="col-12">
                                            <i class="list-icon fa fa-clock-o"></i>

                                            <?php /*echo date("h:i A", strtotime($project->eventstartdatetime)) //. "-" . date("H:i A", strtotime($project->eventenddatetime)); */ ?>
                                        </li>-->
                                        <li class="col-12">
                                            <i class="list-icon fa fa-clock-o"></i>
                                            <?php
                                            $eventTime = date("g:i A", strtotime($project->eventstartdatetime));
                                            if (app_time_format() == 24) {
                                                $eventTime = date("G:i", strtotime($project->eventstartdatetime));
                                            }
                                            echo $eventTime;
                                            ?>
                                        </li>
                                        <?php if ($project->venueid > 0) {
                                            $vname = $venue->venuename;
                                            $vaddress = "";

                                            //$vaddress = $venue->venueaddress . ",<br /> ";
                                            if ($venue->venueaddress != "") {
                                                $vaddress = $venue->venueaddress . "<br /> ";
                                            }
                                            if ($venue->venueaddress2 != "") {
                                                $vaddress .= $venue->venueaddress2 . "<br />";
                                            }
                                            if ($venue->venuecity != "") {
                                                $vaddress .= $venue->venuecity . " ";
                                            }
                                            if ($venue->venuestate != "") {
                                                $vaddress .= $venue->venuestate;
                                            }
                                            ?>
                                            <li class="col-12 venue">
                                                <i class="list-icon fa fa-map-marker"></i>
                                                <span><?php echo $vname; ?></span>
                                                <span class="display-block" style="text-indent: 29px;line-height: 1;">
                                                <?php echo $vaddress; ?>
                                            </span>
                                            </li>
                                        <?php } ?>
                                        <li class="col-12"><i
                                                    class="fa fa-star-half-o task-info-icon"></i><?php echo $status_name; ?>
                                        </li>
                                        <li class="col-12"><i
                                                    class="list-icon fa fa-thumb-tack <?php echo $pinclass; ?>"></i><span
                                                    class="project-pin <?php echo $pinclass; ?>"
                                                    project_id="<?php echo $projectid; ?>"><?php echo $pintitle; ?></span>
                                        </li>
                                        <li class="col-12"><i
                                                    class="list-icon fa fa-user"></i><?php echo _l('assignedto') . $assignedOutput; ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="statistic-squares text-center"><span id="projectCountdown" class="countdown"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if (!empty($project->lastaction)) {
                            $lastaction = _dt($project->lastaction, true);
                        }

                        if (!empty($project->nextaction)) {
                            $nextaction = _dt($project->nextaction, true);
                        } else {
                            $nextaction = "Nothing Scheduled";
                        }
                        ?>
                        <div class="widget-bg meeting-action panel_s btmbrd">
                            <div class="row m-0">
                                <div class="col-xs-5">
                                    <div class="progress-stats-round text-center input-has-value">
                                        <span>LAST ACTION</span>
                                        <h4 class=" mr-tb-10"><i class="fa fa-calendar"></i></h4>
                                        <?php if (!empty($project->lastaction)) { ?>
                                            <div class="date color-primary"><?php echo $project->last_meeting_name; ?></div>
                                            <small><?php echo "(" . time_ago($project->lastaction) . ")"; ?></small>
                                        <?php } else { ?>
                                            <div class="date color-primary"><?php echo $project->last_meeting_name; ?></div>
                                            <small><?php echo(!empty($lastaction) ? "(" . time_ago($lastaction) . ")" : ""); ?></small>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <h4 class="action-arrow"><i class="fa fa-angle-double-right"></i></h4>
                                </div>
                                <div class="col-xs-5">
                                    <div class="progress-stats-round text-center input-has-value">
                                        <span>NEXT ACTION</span>
                                        <h4 class=" mr-tb-10"><i class="fa fa-calendar"></i></h4>
                                        <?php if (!empty($project->nextaction)) { ?>
                                            <div class="date color-primary"><?php echo $project->next_meeting_name ?></div>
                                            <small><?php echo "(" . after_time($project->nextaction) . ")"; ?></small>
                                        <?php } else { ?>
                                            <div class="date color-primary"><?php echo $nextaction ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-12 widget-holder">
                        <h4>Collaborators
                            <span>
                                (<?php echo(isset($project->collaborators) ? count($project->collaborators) : 0); ?>)
                            </span>
                            <div class="more-setting">
                                <a href="<?php echo admin_url('projects/invite/4/' . $project->id); ?>">
                                    <i class="fa fa-plus"></i>
                                </a>
                                <a href="javascript: void(0);" data-toggle="collapse"
                                   data-target="#collaborator-data" id="collaborator-collapse">
                                    <i class="fa fa-caret-up"></i>
                                </a>
                            </div>
                        </h4>
                        <div class=" panel_s btmbrd collaborators-details">
                            <div class="widget-body widget-bg clearfix collapse" id="collaborator-data">
                                <div class="collaborator-card-default">
                                    <div class="col-sm-12">
                                        <table class="valignCenter">
                                            <tbody>
                                            <?php if ((isset($project->collaborators) && count($project->collaborators) > 0) || (isset($project->clients) && count($project->clients) > 0)) { ?>
                                                <?php if (isset($project->clients) && count($project->clients) > 0) { ?>
                                                    <?php foreach ($project->clients as $client) { ?>
                                                        <tr>
                                                            <td width="40px">
                                                                <div class="lead-pimg">
                                                                    <?php echo staff_profile_image($client['staffid'], array('staff-profile-image-small')); ?>
                                                                </div>
                                                            </td>
                                                            <td width="100%">
                                                                <div class="collaborator-det">
                                                                    <h3><?php echo isset($client['companyname']) ? $client['companyname'] : ''; ?></h3>
                                                                    <div class="invite-tags">
                                                                        <span class="mright5"><?php echo isset($client['firstname']) ? $client['firstname'] : ''; ?></span><span><?php echo isset($client['lastname']) ? $client['lastname'] : ''; ?></span>
                                                                        <!--<span><?php /*echo (isset($client['tags']) && $client['tags'] != '') ? '(' . $client['tags'] . ')' : ''; */ ?></span>-->
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <?php
                                                            $session_data = get_session_data();
                                                            $user_type = $session_data['user_type'];
                                                            /*if ($user_type == 1) {*/
                                                            $staffid = (isset($client['staffid']) ? $client['staffid'] : 0);
                                                            $addressbookid = (isset($client['addressbookid']) ? $client['addressbookid'] : 0);
                                                            ?>
                                                            <td>
                                                                <div>
                                                                    <span class="isclient"><?php echo _l('project_client') ?></span>
                                                                    <!--<a role="menuitem" class="btn btn-success btn-icon"
                                                                   tabindex="-1" href="javascript: void(0);"
                                                                   onclick="fnViewInvite(<?php /*echo $projectid; */ ?>, <?php /*echo $staffid;  */ ?>, <?php /*echo $addressbookid;  */ ?>, 0 , 1);"><i
                                                                            class="fa fa-eye"></i></a>-->
                                                                </div>
                                                            </td>
                                                            <?php /*} */ ?>
                                                        </tr>
                                                    <?php }
                                                } ?>
                                                <?php if (isset($project->collaborators) && count($project->collaborators) > 0) { ?>
                                                    <?php foreach ($project->collaborators as $collaborators) { ?>
                                                        <tr>
                                                            <td width="40px">
                                                                <div class="lead-pimg">
                                                                    <?php echo $collaborators['image']; ?>
                                                                </div>
                                                            </td>
                                                            <td width="100%">
                                                                <div class="collaborator-det">
                                                                    <h3><?php echo isset($collaborators['companyname']) ? $collaborators['companyname'] : ''; ?></h3>
                                                                    <div class="invite-tags">
                                                                        <?php if ($collaborators['status'] == "pending") { ?>
                                                                        <a href="<?php echo admin_url('invites/invitedetails/' . $collaborators['inviteid']) ?>">
                                                                            <?php } ?>
                                                                            <span>
                                                                            <?php echo isset($collaborators['name']) ? $collaborators['name'] : ''; ?>
                                                                        </span>
                                                                            <?php if ($collaborators['status'] == "pending") { ?>
                                                                        </a>
                                                                    <?php } ?>
                                                                        <span><?php echo (isset($collaborators['tags']) && $collaborators['tags'] != '') ? '(' . $collaborators['tags'] . ')' : ''; ?></span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <?php
                                                            $session_data = get_session_data();
                                                            $user_type = $session_data['user_type'];
                                                            /*if ($user_type == 1) {*/
                                                                $staffid = (isset($collaborators['staffid']) ? $collaborators['staffid'] : 0);
                                                                $addressbookid = (isset($collaborators['addressbookid']) ? $collaborators['addressbookid'] : 0);
                                                                //die('<--here');
                                                                ?>
                                                                <td>
                                                                    <div>
                                                                        <?php if ($collaborators['status'] != "pending") { ?>
                                                                            <a role="menuitem"
                                                                               class="btn btn-success btn-icon"
                                                                               tabindex="-1" href="javascript: void(0);"
                                                                               onclick="fnViewInvite(<?php echo $projectid; ?>, <?php echo $staffid; ?>, <?php echo $addressbookid; ?>, 0 , 1);"><i
                                                                                        class="fa fa-eye"></i></a>
                                                                        <?php } else {
                                                                            ?>
                                                                            <div class="isDetails">
                                                                            <span class="invite_user_status inviteeStatus">
                                                                                <span class="label">Pending</span>
                                                                            </span>
                                                                            </div>
                                                                        <?php } ?>

                                                                    </div>
                                                                </td>
                                                            <?php /*} */?>
                                                        </tr>
                                                    <?php } ?>
                                                <?php }
                                            } else { ?>
                                                <tr>
                                                    <td colspan="3">No Collaborators found.</td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 widget-holder">
                        <h4>Vendors
                            <span>(<?php echo(isset($project->vendors) ? count($project->vendors) : 0); ?>)</span>
                            <div class="more-setting">
                                <a href="<?php echo admin_url('projects/invite/3/' . $project->id); ?>"><i
                                            class="fa fa-plus"></i></a>
                                <a href="javascript: void(0);" data-toggle="collapse" data-target="#vendor-data"
                                   id="vendor-collapse"><i class="fa fa-caret-up"></i></a>
                            </div>
                        </h4>

                        <div class="panel_s btmbrd clearfix vendors-details">
                            <div id="vendor-data" class="widget-body widget-bg clearfix collapse in">
                                <div class="vendor-card-default">
                                    <div class="col-sm-12">
                                        <table>
                                            <tbody>
                                            <?php if (isset($project->vendors) && count($project->vendors) > 0) { ?>
                                                <?php foreach ($project->vendors as $vendors) { ?>
                                                    <tr>
                                                        <td width="40px">
                                                            <div class="lead-pimg">
                                                                <?php echo $vendors['image']; ?>
                                                            </div>
                                                        </td>
                                                        <td width="100%">
                                                            <div class="vendor-det">
                                                                <h3><?php echo isset($vendors['companyname']) ? $vendors['companyname'] : ''; ?></h3>
                                                                <div class="invite-tags">
                                                                    <?php if ($vendors['status'] == "pending") { ?>
                                                                    <a href="<?php echo admin_url('invites/invitedetails/' . $vendors['inviteid']) ?>">
                                                                        <?php } ?>
                                                                        <span><?php echo isset($vendors['name']) ? $vendors['name'] : ''; ?></span>
                                                                        <?php if ($vendors['status'] == "pending") { ?>
                                                                    </a>
                                                                <?php } ?>
                                                                    <span><?php echo (isset($vendors['tags']) && $vendors['tags'] != '') ? '(' . $vendors['tags'] . ')' : ''; ?></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <?php
                                                        $session_data = get_session_data();
                                                        $user_type = $session_data['user_type'];
                                                        if ($user_type == 1) {
                                                            $staffid = (isset($vendors['staffid']) ? $vendors['staffid'] : 0);
                                                            $addressbookid = (isset($vendors['addressbookid']) ? $vendors['addressbookid'] : 0);
                                                            ?>
                                                            <td>
                                                                <div>
                                                                    <?php if ($vendors['status'] != "pending") { ?>
                                                                        <a role="menuitem"
                                                                           class="btn btn-success btn-icon"
                                                                           tabindex="-1" href="javascript: void(0);"
                                                                           onclick="fnViewInvite(<?php echo $projectid; ?>, <?php echo $staffid; ?>, <?php echo $addressbookid; ?>, 1, 0);"><i
                                                                                    class="fa fa-eye"></i></a>
                                                                    <?php } else { ?>
                                                                        <div class="isDetails">
                                                                            <span class="invite_user_status inviteeStatus">
                                                                                <span class="label">pending</span>
                                                                            </span>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="3">No vendors found.</td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12 widget-holder">
                        <div class="venues-details">
                            <h4>Venues
                                <span>(<?php echo(isset($project->venues) ? count($project->venues) : 0); ?>)</span>
                                <div class="more-setting">
                                    <a href="<?php echo admin_url('projects/invite/5/' . $project->id); ?>"><i
                                                class="fa fa-plus"></i></a>
                                    <a href="javascript: void(0);" data-toggle="collapse" data-target="#venue-data"
                                       id="venue-collapse"><i class="fa fa-caret-up"></i></a>
                                </div>
                            </h4>
                            <div class="panel_s btmbrd">
                                <div id="venue-data"
                                     class="collapse in venue-card-default widget-body widget-bg clearfix ">
                                    <div class="col-sm-12">
                                        <table>
                                            <tbody>
                                            <?php if (isset($project->venues) && count($project->venues) > 0) { ?>
                                                <?php foreach ($project->venues as $venues) { ?>
                                                    <tr>
                                                        <td width="40px">
                                                            <div class="lead-pimg">
                                                                <?php echo $venues['venuelogo']; ?>
                                                            </div>
                                                        </td>
                                                        <td width="100%">
                                                            <div class="venue-det">
                                                                <?php if ($venues['status'] == "pending") { ?>
                                                                <a href="<?php echo admin_url('invites/invitedetails/' . $venues['inviteid']) ?>">
                                                                    <?php } ?>
                                                                    <h3><?php echo isset($venues['venuename']) ? $venues['venuename'] : ''; ?></h3>
                                                                    <?php if ($venues['status'] == "pending") { ?>
                                                                </a>
                                                            <?php } ?>
                                                                <div class="invite-tags">
                                                                    <span>
                                                                        <?php
                                                                        if (is_serialized($venues['venueemail'])) {
                                                                            $venues['venueemail'] = unserialize($venues['venueemail']);
                                                                            $venues['venueemail'] = $venues['venueemail'][0]['email'];
                                                                        }
                                                                        echo isset($venues['venueemail']) ? $venues['venueemail'] : ''; ?></span>
                                                                    <span><?php echo (isset($venues['venuecontactname']) && $venues['venuecontactname'] != '') ? '(' . $venues['venuecontactname'] . ')' : ''; ?></span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <?php
                                                        $session_data = get_session_data();
                                                        $user_type = $session_data['user_type'];
                                                        if ($user_type == 1) {
                                                            ?>
                                                            <td>
                                                                <div>
                                                                    <?php if ($venues['status'] != "pending") { ?>
                                                                        <a role="menuitem"
                                                                           class="btn btn-success btn-icon"
                                                                           tabindex="-1" href="javascript: void(0);"
                                                                           onclick="fnViewVenueInvite(<?php echo $projectid; ?>, <?php echo $venues['venueid']; ?>);"><i
                                                                                    class="fa fa-eye"></i></a>
                                                                    <?php } else { ?>
                                                                        <div class="isDetails">
                                                                            <span class="invite_user_status inviteeStatus">
                                                                                <span class="label">pending</span>
                                                                            </span>
                                                                        </div>
                                                                    <?php } ?>

                                                                </div>
                                                            </td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tr>
                                                    <td colspan="3">No venues found.</td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">

                <div class="row">
                    <div class="col-md-12 widget-holder">
                        <h4>Project Tools</h4>
                        <div class="project-tool-block panel_s btmbrd">
                            <div class="widget-body clearfix">
                                <div class="tabs tabs-bordered ">
                                    <ul class="nav nav-tabs">
                                        <?php
                                        if (has_permission('meetings', '', 'view')) {
                                            if ($project->is_client == 1) {
                                                $permission_array = explode(",", $project->permission);
                                                $access = (in_array('Meetings', $permission_array) ? true : false);
                                            } else {
                                                $access = true;
                                            }
                                            if ($access) {
                                                ?>
                                                <?php if ($project->project_parent > 0) { ?>
                                                    <li class="col-lg-3 col-sm-4 col-xs-6 nav-item1 ">
                                                        <a class="nav-link"
                                                           href="<?php echo admin_url('meetings?eid=' . $projectid); ?>">
                                                            <i class="fa fa-handshake-o"></i>
                                                            <p><?php echo _l('meetings'); ?></p>
                                                        </a>
                                                    </li>
                                                <?php } else { ?>
                                                    <li class="col-lg-3 col-sm-4 col-xs-6 nav-item1 ">
                                                        <a class="nav-link"
                                                           href="<?php echo admin_url('meetings?pid=' . $projectid); ?>">
                                                            <i class="fa fa-handshake-o"></i>
                                                            <p><?php echo _l('meetings'); ?></p>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            <?php }
                                        }
                                        ?>
                                        <?php if (has_permission('tasks', '', 'view')) {
                                            if ($project->is_client == 1) {
                                                $permission_array = explode(",", $project->permission);
                                                $access = (in_array('Tasks', $permission_array) ? true : false);
                                            } else {
                                                $access = true;
                                            }
                                            if ($access) {
                                                ?>
                                                <?php if ($project->project_parent > 0) { ?>
                                                    <li class="col-lg-3 col-sm-4 col-xs-6 nav-item1 ">
                                                        <a class="nav-link"
                                                           href="<?php echo admin_url('tasks?eid=' . $projectid); ?>">
                                                            <i class="fa fa-tasks"></i>
                                                            <p><?php echo _l('tasks'); ?></p>
                                                        </a>
                                                    </li>
                                                <?php } else { ?>
                                                    <li class="col-lg-3 col-sm-4 col-xs-6 nav-item2">
                                                        <a class="nav-link"
                                                           href="<?php echo admin_url('tasks?pid=' . $projectid); ?>">
                                                            <i class="fa fa-tasks"></i>
                                                            <p><?php echo _l('tasks'); ?></p>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            <?php }
                                        }
                                        ?>
                                        <?php if (has_permission('messages', '', 'view')) { ?>
                                            <?php if ($project->project_parent > 0) { ?>
                                                <li class="col-lg-3 col-sm-4 col-xs-6 nav-item1 ">
                                                    <a class="nav-link"
                                                       href="<?php echo admin_url('messages?eid=' . $projectid); ?>">
                                                        <i class="fa fa-clock-o"></i>
                                                        <p><?php echo _l('messages'); ?></p>
                                                    </a>
                                                </li>
                                            <?php } else { ?>
                                                <li class="col-lg-3 col-sm-4 col-xs-6 nav-item3">
                                                    <a class="nav-link"
                                                       href="<?php echo admin_url('messages?pid=' . $projectid); ?>"
                                                       aria-expanded="true">
                                                        <i class="fa fa-envelope"></i>
                                                        <p><?php echo _l('messages'); ?></p>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php if ($project->project_parent > 0) { ?>
                                            <li class="col-lg-3 col-sm-4 col-xs-6 nav-item4 ">
                                                <a class="nav-link"
                                                   href="<?php echo admin_url('projects/notes?eid=' . $projectid); ?>">
                                                    <i class="fa fa-sticky-note-o"></i>
                                                    <p><?php echo _l('notes'); ?></p>
                                                </a>
                                            </li>
                                        <?php } else { ?>
                                            <li class="col-lg-3 col-sm-4 col-xs-6 nav-item4">
                                                <a class="nav-link"
                                                   href="<?php echo admin_url('projects/notes?pid=' . $projectid); ?>"
                                                   aria-expanded="true">
                                                    <i class="fa fa-sticky-note-o"></i>
                                                    <p><?php echo _l('notes'); ?></p>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if (has_permission('files', '', 'view')) {
                                            if ($project->is_client == 1) {
                                                $permission_array = explode(",", $project->permission);
                                                $access = (in_array('Files', $permission_array) ? true : false);
                                            } else {
                                                $access = true;
                                            }

                                            if ($access) {
                                                ?>
                                                <?php if ($project->project_parent > 0) { ?>
                                                    <li class="col-lg-3 col-sm-4 col-xs-6 nav-item1 ">
                                                        <a class="nav-link"
                                                           href="<?php echo admin_url('files?eid=' . $projectid); ?>">
                                                            <i class="fa fa-folder-open"></i>
                                                            <p><?php echo _l('files'); ?></p>
                                                        </a>
                                                    </li>
                                                <?php } else { ?>
                                                    <li class="col-lg-3 col-sm-4 col-xs-6 nav-item5">
                                                        <a class="nav-link"
                                                           href="<?php echo admin_url('files?pid=' . $projectid); ?>">
                                                            <i class="fa fa-folder-open"></i>
                                                            <p><?php echo _l('files'); ?></p>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            <?php }
                                        }
                                        ?>
                                        <?php if (has_permission('proposals', '', 'view')) {
                                            if ($project->is_client == 1) {

                                                $permission_array = explode(",", $project->permission);
                                                $access = (in_array('Proposals', $permission_array) ? true : false);
                                            } else {
                                                $access = true;
                                            }
                                            if ($access) {
                                                ?>
                                                <li class="col-lg-3 col-sm-4 col-xs-6 nav-item6">
                                                    <?php if ($project->project_parent > 0) { ?>
                                                        <a class="nav-link"
                                                           href="<?php echo admin_url('proposaltemplates?pid=' . $projectid); ?>">
                                                            <i class="fa fa-file-text-o"></i>
                                                            <p><?php echo _l('proposals'); ?></p>
                                                        </a>
                                                    <?php } else { ?>
                                                        <a class="nav-link"
                                                           href="<?php echo admin_url('proposaltemplates?pid=' . $projectid); ?>">
                                                            <i class="fa fa-file-text-o"></i>
                                                            <p><?php echo _l('proposals'); ?></p>
                                                        </a>
                                                    <?php } ?>
                                                </li>
                                            <?php }
                                        }
                                        ?>
                                        <?php /*if (has_permission('questionnaire', '', 'view')) {
                                                if ($project->is_client == 1) {
                                                    $permission_array = explode(",", $project->permission);
                                                    $access = (in_array('Questionnaire', $permission_array) ? true : false);
                                                } else {
                                                    $access = true;
                                                }

                                                if ($access) {
                                                    */ ?><!--
                                                    <li class="col-lg-3 col-sm-4 col-xs-6 nav-item6">
                                                        <?php /*if ($project->project_parent > 0) { */ ?>
                                                            <a class="nav-link"
                                                               href="<?php /*echo admin_url('questionnaire?pid=' . $projectid); */ ?>">
                                                                <i class="fa fa-id-card-o"></i>
                                                                <p><?php /*echo _l('questionnaire'); */ ?></p>
                                                            </a>
                                                        <?php /*} else { */ ?>
                                                            <a class="nav-link"
                                                               href="<?php /*echo admin_url('questionnaire?pid=' . $projectid); */ ?>">
                                                                <i class="fa fa-id-card-o"></i>
                                                                <p><?php /*echo _l('questionnaire'); */ ?></p>
                                                            </a>
                                                        <?php /*} */ ?>
                                                    </li>
                                                --><?php /*}
                                            }
                                            */ ?>
                                        <?php if (has_permission('invoices', '', 'view')) {
                                            if ($project->is_client == 1) {
                                                $permission_array = explode(",", $project->permission);
                                                $access = (in_array('Invoices', $permission_array) ? true : false);
                                            } else {
                                                $access = true;
                                            }

                                            if ($access) {
                                                ?>
                                                <?php if ($project->project_parent > 0) { ?>
                                                    <li class="col-lg-3 col-sm-4 col-xs-6 nav-item7">
                                                        <a class="nav-link"
                                                           href="<?php echo admin_url('invoices?eid=' . $projectid); ?>">
                                                            <i class="fa fa-money"></i>
                                                            <p><?php echo _l('invoices'); ?></p>
                                                        </a>
                                                    </li>
                                                <?php } else { ?>
                                                    <li class="col-lg-3 col-sm-4 col-xs-6 nav-item7">
                                                        <a class="nav-link"
                                                           href="<?php echo admin_url('invoices?pid=' . $projectid); ?>">
                                                            <i class="fa fa-money"></i>
                                                            <p><?php echo _l('invoices'); ?></p>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            <?php }
                                        }
                                        ?>
                                        <?php if (has_permission('addressbook', '', 'create')) { ?>
                                            <?php if ($project->project_parent > 0) { ?>
                                                <li class="col-lg-3 col-sm-4 col-xs-6 nav-item8">
                                                    <a class="nav-link"
                                                       href="<?php echo admin_url('addressbooks?eid=' . $projectid); ?>"
                                                       aria-expanded="true">
                                                        <i class="fa fa-address-book-o"></i>
                                                        <p><?php echo _l('contacts'); ?></p>
                                                    </a>
                                                </li>
                                            <?php } else { ?>
                                                <li class="col-lg-3 col-sm-4 col-xs-6 nav-item8">
                                                    <a class="nav-link"
                                                       href="<?php echo admin_url('addressbooks?pid=' . $projectid); ?>"
                                                       aria-expanded="true">
                                                        <i class="fa fa-address-book-o"></i>
                                                        <p><?php echo _l('contacts'); ?></p>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        <?php } ?>
                                    </ul>
                                    <!-- /.nav-tabs -->
                                    <!-- /.tab-content -->
                                </div>
                                <!-- /.tabs -->
                            </div>
                            <!-- /.widget-body -->
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 project-details">
                        <h4 class="pull-left">Project Details</h4>

                        <a id="project-details-collapse" href="javascript:void(0)" data-pid="#lead-details"
                           class="pull-right inline-block mtop10 mright10" data-toggle="collapse"
                           data-target="#project-data"><i
                                    class="fa fa-caret-up"></i></a>
                        <div class="clearfix"></div>
                        <div class="panel_s btmbrd">
                            <div id="project-data" class="widget-bg widget-body clearfix collapse in">
                                <div class="weather-card-default">
                                    <table class="table table-bordered table-condensed">
                                        <tbody>
                                        <tr>
                                            <td class="col1"><p><?php echo _l('lead_add_edit_event_status'); ?></p>
                                            </td>
                                            <td>
                                                <?php if (has_permission('leads', '', 'edit')){ ?>
                                                    <select id="project_dashboard_sattus"
                                                            class="form-control selectpicker leadstatus"
                                                            lead_id="<?php echo $projectid; ?>">
                                                        <?php foreach ($statuses as $s) {
                                                            $statusselect = "";
                                                            if ($s['id'] == $project->status) {
                                                                $statusselect = "selected='selected'";
                                                            }
                                                            ?>
                                                            <option value="<?php echo $s['id'] ?>" <?php echo $statusselect; ?>><?php echo $s['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } else { ?>
                                                <b><?php echo $project->status_name;
                                                    } ?></b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p><?php echo _l('status_updated'); ?></p></td>
                                            <td>
                                                <b><?php echo ($project->dateupdated != "") ? _dt($project->dateupdated) . " (" . time_ago($project->dateupdated) . ")" : ""; ?></b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p><?php echo _l('lead_add_edit_maxbudget'); ?></p></td>
                                            <td>
                                                <b><?php echo ($project->budget != "") ? "$" . number_format($project->budget, 0, ",", ",") : "$0"; ?></b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p><?php echo _l('lead_add_edit_source'); ?></p></td>
                                            <td>
                                                <b><?php echo ($project->source_name != "") ? $project->source_name : "--"; ?></b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p><?php echo _l('lead_add_edit_sourcedetails'); ?></p></td>
                                            <td>
                                                <b><?php echo ($project->sourcedetails != "") ? $project->sourcedetails : "--"; ?></b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p><?php echo _l('lead_add_edit_comments'); ?></p></td>
                                            <td>
                                                <b><?php echo ($project->comments != "") ? $project->comments : "--"; ?></b>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($project->parent == 0) { ?>
                    <div class="row">
                        <div id="subProjects" class="col-md-12 subProjects-section">
                            <h4>Projects
                                <div class="more-setting">
                                    <a href="<?php echo admin_url('projects/project?parent_project=' . $project->projectid); ?>">
                                        <i class="fa fa-plus"></i> </a>
                                    <a href="javascript:void(0)" data-pid="#subProjects" class="expnd_cllps"
                                       style="color:#1093b0;" type="button"><i class="fa fa-caret-up"></i></a>
                                </div>
                            </h4>
                            <div class="schedule-txt hide">
                                <?php if (ltrim($project->days_left, '-') > 0) { ?>
                                    <span> Upcoming project in <?php echo((substr($project->days_left, 0, 1) === '-') ? 'next' : ''); ?> </span>
                                    <span class="projectdays-count"><?php echo ltrim($project->days_left, '-'); ?></span> days
                                <?php } ?>
                            </div>
                            <div class="clearfix"></div>
                            <div class="sec-wrap panel_s btmbrd">
                                <div id="event-collapse" aria-expanded="false">
                                    <?php if (count($project->sub_projects) <= 0) { ?>
                                        <table class="table">
                                            <tbody>
                                            <tr>
                                                <td colspan="3" class="empty">No sub projects found</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    <?php } else { ?>
                                        <table class="table" id="projects-list">
                                            <tbody>
                                            <?php
                                            foreach ($project->sub_projects as $sub_projects) {
                                                $eventdate = date("Y-m-d", strtotime($sub_projects['eventstartdatetime']));
                                                $eventmonth = date("M", strtotime($sub_projects['eventstartdatetime']));
                                                $eventday = date("j", strtotime($sub_projects['eventstartdatetime']));
                                                $eventweekday = date("D", strtotime($sub_projects['eventstartdatetime']));
                                                $eventyear = date("Y", strtotime($sub_projects['eventstartdatetime']));
                                                $eventtime = date("h:i A", strtotime($sub_projects['eventstartdatetime']));
                                                $eventendmonth = date("M", strtotime($sub_projects['eventenddatetime']));
                                                $eventendday = date("j", strtotime($sub_projects['eventenddatetime']));
                                                $eventendweekday = date("D", strtotime($sub_projects['eventenddatetime']));
                                                $eventendyear = date("Y", strtotime($sub_projects['eventenddatetime']));
                                                $eventendtime = date("h:i A", strtotime($sub_projects['eventenddatetime']));
                                                $current_date = $sub_projects['currentdatetime'];
                                                ?>
                                                <tr id="<?php echo $sub_projects['id']; ?>"
                                                    data-parent-id="<?php echo $project->id; ?>" <?php echo(($eventdate < $current_date) ? 'class="past-event"' : ''); ?> >
                                                    <td class="col1">
                                                        <div class='text-center'>
                                                            <div>
                                                                <small><?php echo strtoupper($eventmonth); ?></small>
                                                            </div>
                                                            <div>
                                                                <h4 style='margin:0px'><?php echo $eventday; ?></h4>
                                                            </div>
                                                            <div>
                                                                <small><?php echo $eventweekday . " | " . $eventyear; ?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="col2">
                                                        <div class="project-pimg"> <?php echo project_profile_image($sub_projects['id'], array("project-profile-image-small")); ?> </div>
                                                    </td>
                                                    <td class="col3">
                                                        <div class="project-link">
                                                            <a href="<?php echo base_url() . 'admin/projects/dashboard/' . $sub_projects['id']; ?>"
                                                               id="1sub-project-<?php echo $sub_projects['id']; ?>"
                                                               data-parent-id="<?php echo $project->id; ?>"
                                                               onclick="1fnGetSubProjectDetails(<?php echo $sub_projects['id']; ?>);"> <?php echo $sub_projects['name']; ?> </a>
                                                        </div>
                                                        <div class="event-date"> <?php echo $eventtime; ?> </div>
                                                        <div class="name-row">
                                                            <span class="assigned-name"><?php echo $sub_projects['assigned_name']; ?></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="col-md-12 widget-holder">
                    <h4>INVOICE SUMMARY

                        <div class="more-setting">
                            <a href="javascript: void(0);" data-toggle="collapse"
                               data-target="#invoices-data" id="invoices-collapse"><i
                                        class="fa fa-caret-up"></i></a>
                        </div>
                    </h4>
                    <div class=" panel_s btmbrd">
                        <div id="invoices-data"
                             class="invoices_container project-tool-block invoices_container collapse in">
                            <?php if (count($invoices) > 0) { ?>
                                <table class="table mtop5">
                                    <tbody>
                                    <?php foreach ($invoices as $invoice) { ?>
                                        <td class="col1">
                                            <div class='text-center'>
                                                <div>
                                                    <small><?php echo strtoupper(date('M', strtotime($invoice->duedate))); ?></small>
                                                </div>
                                                <div>
                                                    <h4 style='margin:0px'><?php echo date('j', strtotime($invoice->duedate)); ?></h4>
                                                </div>
                                                <div>
                                                    <small><?php echo date('D', strtotime($invoice->duedate)) . " | " . date('Y', strtotime($invoice->duedate)); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="col2">
                                            <div class="project-pimg">
                                                <img src="<?php echo base_url() ?>/assets/images/default_banner.jpg"
                                                     class="project-profile-image-small" alt="The Wedding">
                                            </div>
                                        </td>
                                        <td class="col3">
                                            <a class="display-block"
                                               href="<?php echo admin_url('invoices/list_invoices?pid=' . $projectid . '#' . $invoice->id) ?>"><?php echo format_invoice_number($invoice->id); ?></a>
                                            <?php echo format_money($invoice->total, "$"); ?>
                                        </td>
                                        <td class="col1"><?php echo format_invoice_status($invoice->status, 'mtop5'); ?></td>
                                        </tr>
                                    <?php } ?></tbody>
                                </table>

                            <?php } else { ?>
                                <p><?php echo _l('No Invoices found for this project') ?></p>
                            <?php } ?>
                        </div>
                    </div>
                    <div>
                    </div>
                </div>
            </div>
            <?php /* ?>
            <div id="subproject-breadcrumbs" style="display: none;">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo admin_url('projects'); ?>">Projects</a>
                    </li>
                    <?php
                    $dashboardid = (($project->project_parent > 0) ? $project->project_parent : $project->id);
                    $projectname = (($project->project_parent > 0) ? $project->parent_name : $project->name);
                    ?>
                    <li class="breadcrumb-item"><a
                                href="<?php echo admin_url('projects/dashboard/' . $dashboardid); ?>"><?php echo $projectname; ?></a>
                    </li>
                    <li class="breadcrumb-item" id="sub-project"></li>
                </ol>
            </div>
<?php */ ?>
            <div id="project-interaction">
                <input type="hidden" name="dashboard-type" id="dashboard-type"
                       value="<?php echo(($project->project_parent > 0) ? 'event' : 'project'); ?>">
                <input type="hidden" name="projectid" id="projectid" value="<?php echo $projectid; ?>">
                <div class="project-interaction">
                    <div class="row row-flex">
                        <div class="col-md-6">

                            <!-- /.card-user-info-widget -->
                        </div>

                        <!-- /.widget-body -->
                    </div>
                </div>
                <!-- /.row -->
                <div class="row row-flex">


                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="view_invites" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('view_invites'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="ie-dt-fix">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<?php echo app_stylesheet('assets/css', 'jquery.countdown.css'); ?> <?php echo app_script('assets/js', 'jquery.plugin.js'); ?> <?php echo app_script('assets/js', 'jquery.countdown.js'); ?>
<script type="text/javascript">
    newDate = new Date(<?php echo date("Y, n - 1, d, H, i", strtotime($project->eventstartdatetime)) ?>);
    $('#projectCountdown').countdown({until: newDate});

    $('#vendor-data').collapse("show");

    $('#collaborator-data').collapse("show");

    $("#vendor-collapse").click(function () {
        $("#vendor-collapse i").toggleClass('fa-caret-down fa-caret-up');
    });
    $("#project-details-collapse").click(function () {
        $("#project-details-collapse i").toggleClass('fa-caret-down fa-caret-up');
    });
    $("#invoices-collapse").click(function () {
        $("#invoices-collapse i").toggleClass('fa-caret-down fa-caret-up');
    });
    $("#collaborator-collapse").click(function () {
        $("#collaborator-collapse i").toggleClass('fa-caret-down fa-caret-up');

    });

    $("#venue-collapse").click(function () {
        $("#venue-collapse i").toggleClass('fa-caret-down fa-caret-up');
    });

    function fnGetSubProjectDetails(projectid) {
        var parentid = $("#sub-project-" + projectid).attr('data-parent-id');
        $.ajax({
            method: 'post',
            async: false,
            url: '<?php echo admin_url(); ?>projects/subdashboard',
            data: 'projectid=' + projectid,
            dataType: "html",
            success: function (data) {
                $("#event-collapse").attr('aria-expanded', 'false');
                $("#event-collapse").removeClass('in');
                $(".project-info-widget").removeClass("ddshow");
                $("#project-interaction").html(data);
                $(".dd-button > i").addClass("fa-chevron-down");
                $(".dd-button > i").removeClass("fa-chevron-up");

                if ($("#dashboard-type").val() === "event") {
                    $("#subproject-breadcrumbs").css('display', 'block');
                    $("#sub-project").html($(".fw-700").html());
                }
            }
        });
    }

    if ($("#dashboard-type").val() === "event") {
        $("#subproject-breadcrumbs").css('display', 'block');
        $("#sub-project").html($(".fw-700").html());
    }

    $(".dd-button").click(function () {
        if ($(".dd-button > i").hasClass("fa-chevron-down")) {
            $(".dd-button > i").removeClass("fa-chevron-down");
            $(".dd-button > i").addClass("fa-chevron-up");
        } else {
            $(".dd-button > i").addClass("fa-chevron-down");
            $(".dd-button > i").removeClass("fa-chevron-up");
        }

        $("#project-widget").toggleClass('ddshow');
    });

    $('#projects-list t r').click(function () {
        var projectid = $(this).attr("id");
        $.ajax({
            method: 'post',
            async: false,
            url: '<?php echo admin_url(); ?>projects/subdashboard',
            data: 'projectid=' + projectid,
            dataType: "html",
            success: function (data) {
                $("#event-collapse").attr('aria-expanded', 'false');
                $("#event-collapse").removeClass('in');
                $(".project-info-widget").removeClass("ddshow");
                $("#project-interaction").html(data);
                $(".dd-button > i").addClass("fa-chevron-down");
                $(".dd-button > i").removeClass("fa-chevron-up");

                if ($("#dashboard-type").val() === "event") {
                    $("#subproject-breadcrumbs").css('display', 'block');
                    $("#sub-project").html($(".fw-700").html());
                }
            }
        });
    });

    $(document).mouseup(function (e) {
        var container = $(".subProjects-section");
        if (!container.is(e.target) && container.has(e.target).length === 0 && $("#project-widget").hasClass('ddshow')) {
            $('.dd-button').trigger('click');
        }
    });

    function fnViewInvite(projectid, staffid, addressbookid, isvendor, iscollaborator) {
        if (isvendor == 1) {
            var contacttype = 3;
        } else if (iscollaborator == 1) {
            var contacttype = 4;
        } else {
            var contacttype = 5;
        }
        $.ajax({
            method: 'post',
            async: false,
            url: '<?php echo admin_url(); ?>projects/viewinvite',
            data: 'projectid=' + projectid + '&isvendor=' + isvendor + '&iscollaborator=' + iscollaborator + '&isparent=' + 1 + '&staffid=' + staffid + '&addressbookid=' + addressbookid + '&contacttype=' + contacttype,
            dataType: "html",
            success: function (data) {
                $(".ie-dt-fix").html(data);
                $('#view_invites').modal('show');
            }
        });
    }

    function fnViewVenueInvite(projectid, venueid) {
        $.ajax({
            method: 'post',
            async: false,
            url: '<?php echo admin_url(); ?>projects/viewinvite',
            data: 'projectid=' + projectid + '&venueid=' + venueid + '&isparent=' + 1 + '&contacttype=5',
            dataType: "html",
            success: function (data) {
                $(".ie-dt-fix").html(data);
                $('#view_invites').modal('show');
            }
        });
    }
</script>
</body></html>