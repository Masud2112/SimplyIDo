<?php
/*echo "<pre>";
print_r($invite['contacttype']);
die();*/

/*echo "<pre>";
print_r($invite);
die();*/
?>
    <li data-invite-id="<?php echo $invite['inviteid']; ?>"
        class="col-sm-6 col-lg-4 kanban-card-block kanban-card">
        <div class="panel-body card-body">
            <div class="row">
                <div class="col-xs-12 card-name">
                    <div class="card_invitee">
                        <div class="invited_user">
                            <div class="inviteFrom">
                                <?php echo addressbook_profile_image($invite['contactid'], array(
                                    'staff-profile-image-small',
                                    'media-object img-circle pull-left mright10'
                                )); ?>
                                <div class="inviteHead">
                                <span class="inviteFromName">
									<?php if ($invite['contacttype'] == 5) {
                                        echo $invite['venue_name'];
                                    } else {
                                        echo $invite['assigned_name'];
                                    } ?>
								</span>
                                    <span class="inviteFromEmail">
                                <?php echo $invite['assigned_email']; ?>
								</span>
                                    <span class="inviteType">
                                <?php
                                if ($invite['contacttype'] == 3) {
                                    echo "Vendor";
                                } elseif ($invite['contacttype'] == 4) {
                                    echo "Collaborator";
                                } else {
                                    echo "Venue";
                                }
                                ?>
								</span>
                                    <?php
                                    //if (strpos($invite['status'], 'Sent') !== false) {
                                    if (strtolower($invite['status'])== "pending") {
                                        $status = 'Pending';
                                        $icon = "clock-o";
                                    } //elseif (strpos($invite['status'], 'Accept') !== false) {
                                        elseif (strtolower($invite['status'])== "approved") {
                                        $status = 'Approved';
                                        $icon = "check";
                                    } else {
                                        $status = "Declined";
                                        $icon = "ban";
                                    }
                                    ?>
                                    <span class="inviteStatus <?php echo strtolower($status); ?>"><i
                                                class="fa fa-<?php echo $icon ?>"></i> <?php echo $status ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card_event">
                        <div class="carddate-block">
                            <div class="card_date">
                                <div class="card_month">
                                    <small><?php echo date('M', strtotime($invite['eventstartdatetime'])) ?></small>
                                </div>
                                <div class="card_d">
                                    <strong><?php echo date('d', strtotime($invite['eventstartdatetime'])) ?></strong>
                                </div>
                                <div class="card_day">
                                    <small><?php echo date('D', strtotime($invite['eventstartdatetime'])) ?></small>
                                </div>
                            </div>

                            <?php if (date('Y', strtotime($invite['eventstartdatetime'])) > date('Y')) { ?>
                                <div class="card_year">
                                    <small><?php echo date('Y', strtotime($invite['eventstartdatetime'])) ?></small>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="invite-body">
                        <span class="inviteProject display-block">
                            <?php echo isset($invite['project_name']) ? $invite['project_name'] : ""; ?>
                        </span>
                            <span class="inviteProjectType">
                            <i class="fa fa-book"></i>
                                <?php echo isset($invite['project_type']) ? $invite['project_type'] : ""; ?>
                        </span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="col-xs-1 text-muted">
                    <div class="show-act-block"><?php
                        $options = "<div><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
                        if (has_permission('projects', '', 'edit')) {
                            $options .= '<li><a href=' . admin_url() . 'invites/invitedetails/' . $invite['inviteid'] . ' class="" title="Edit"><i class="fa fa-pencil-square-o"></i><span>View</span></a></li>';
                        } else {
                            $options .= "";
                        }

                        /*if (has_permission('leads', '', 'delete')) {
                            $options .= '<li><a href=' . admin_url() . 'invites/delete/' . $invite['inviteid'] . ' class="_delete" title="Delete"><i class="fa fa-remove"></i><span>Delete</span></a></li>';
                        }*/
                        $options .= "</ul></div>";
                        echo $options;
                        ?></div>
                </div>
                <div class="clearfix"></div>
                <div class="card-footer">
                    <div class="invite_by text-center">
                        <span class="inviteFrom display-block">
                        <?php echo "by " . staff_profile_image($invite['invitedby'], array(
                                'staff-profile-image-small',
                                'media-object img-circle m0'
                            )); ?>
                            <?php echo get_staff_full_name($invite['invitedby']) ?>
                            on <?php echo isset($invite) ? date('n/j/Y', strtotime($invite['datecreated'])) : ""; ?>
                    </span>
                    </div>
                </div>
            </div>
    </li>
<?php //} ?>