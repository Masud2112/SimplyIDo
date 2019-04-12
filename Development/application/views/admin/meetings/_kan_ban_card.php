<?php

if (isset($meeting['rel_type']) && $meeting['rel_id'] > 0) {
    $event = get_event_name($meeting['rel_type'], $meeting['rel_id']);
    $meeting['eventtypename'] = isset($event->name) ? $event->name : "";
}
$reminders = get_all_meeting_reminders($meeting['meetingid']);
$attendees = get_meeting_attendees($meeting['meetingid']);
/*if ($meeting['status'] == $status['statusid']) {*/
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
    $CI->db->where('pintype', 'Meeting');
    $CI->db->where('pintypeid', $meeting['meetingid']);
    $result = $CI->db->get()->row();
    ?>
    <li data-meeting-id="<?php echo $meeting['meetingid']; ?>"
        class="col-sm-6 col-lg-4 kanban-card-block kanban-card <?php echo $class ?>">
        <div class="panel-body card-body">
            <div class="row">

                <div class="col-xs-11 card-name">
                    <div class="carddate-block">
                        <div class="card_date" title="<?php echo date('Y', strtotime($meeting['start_date'])) ?>">
                            <div class="card_month">
                                <small><?php echo date('M', strtotime($meeting['start_date'])) ?></small>
                            </div>
                            <div class="card_d">
                                <strong><?php echo date('d', strtotime($meeting['start_date'])) ?></strong>
                            </div>
                            <div class="card_day">
                                <small><?php echo date('D', strtotime($meeting['start_date'])) ?></small>
                            </div>
                        </div>

                        <?php if (date('Y', strtotime($meeting['start_date'])) > date('Y')) { ?>
                            <div class="card_year">
                                <small><?php echo date('Y', strtotime($meeting['start_date'])) ?></small>
                            </div>
                        <?php } ?>
                    </div>
                    <?php //echo meeting_profile_image($meeting['id'], array('meeting-profile-image-xs')); ?>
                    <span class="meetingNameTitle display-block"><a
                                href="<?php echo admin_url('meetings/meeting/' . $meeting['meetingid']); ?>"><?php echo $meeting['name']; ?></a></span>
                    <span class="mTime"><?php echo date('g:i A', strtotime($meeting['start_date'])) ?>
                        - <?php echo date('g:i A', strtotime($meeting['end_date'])) ?></span>
                    <span class="mTime meetingLocation display-block"><?php echo $meeting['location']; ?></span>
                    <div class="clearfix"></div>
                    <span class="meeingRelType display-block">
                        <?php if ($meeting['rel_type'] == "project" || $meeting['rel_type'] == "event") { ?>
                            <i class="fa fa-book"></i>
                        <?php } elseif ($meeting['rel_type'] == "lead") { ?>
                            <i class="fa fa-tty"></i>
                        <?php } ?>

                        <?php echo isset($meeting['eventtypename']) ? $meeting['eventtypename'] : ""; ?>
                    </span>
                    <span class="mdate"><?php echo isset($event) ? date('D M d, Y', strtotime($event->eventstartdatetime)) : ""; ?></span>
                </div>
                <div class="col-xs-1 text-muted">
                    <div class="show-act-block"><?php
                        $options = "<div><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
                        /*$options .= '<li><a href=' . admin_url() . 'meetings/meeting/' . $meeting['meetingid'] . ' class="" title="View Dashboard"><i class="fa fa-eye"></i><span>View</span></a></li>';*/
                        if (has_permission('leads', '', 'edit')) {
                            $options .= '<li><a href=' . admin_url() . 'meetings/meeting/' . $meeting['meetingid'] . ' class="" title="Edit"><i class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>';
                        } else {
                            $options .= "";
                        }

                        if (has_permission('leads', '', 'delete')) {
                            $options .= '<li><a href=' . admin_url() . 'meetings/delete/' . $meeting['meetingid'] . ' class="_delete" title="Delete"><i class="fa fa-remove"></i><span>Delete</span></a></li>';
                        }
                        $options .= "</ul></div>";
                        echo $options;
                        ?></div>
                    <div class="pin-block">
                        <i class="fa fa-fw fa-thumb-tack meeting-pin <?php echo isset($result->pinned) ? "pinned" : ""; ?>"
                           title="<?php echo isset($result->pinned) ? "Unpin from Home" : "Pin to Home"; ?>"
                           id="<?php echo $meeting['meetingid'] ?>"
                           meeting_id="<?php echo $meeting['meetingid'] ?>"></i>
                    </div>
					<div class="checkbox"><input type="checkbox" value="<?php echo $meeting['meetingid'] ?>"><label></label></div>
                </div>
                <div class="clearfix"></div>
                <div class="card-footer">
                    <?php if ($reminders >0 ) { ?>
                        <span class="meeting_reminder">
                            <i class="fa fa-bell-o"></i>

                            <span class="reminder_count"><?php echo $reminders ?></span>
                        </span>
                    <?php } ?>
                    <span class="meeting_status <?php echo strtolower(str_replace(' ','_',$meeting['status_name'])); ?>"><?php echo $meeting['status_name']; ?></span>
                    <span class="meeting_attendess pull-right">
                        <?php
                        $acounter = 1;
                        foreach ($attendees as $attendee) {
                            if ($acounter <= 2) {
                                {
                                    if ($attendee['type'] == "member") {
                                        echo staff_profile_image($attendee['id'], array(
                                            'staff-profile-image-small',
                                            'media-object img-circle pull-left mright10'
                                        ));
                                    } else {
                                        echo addressbook_profile_image($attendee['id'], array(
                                            'staff-profile-image-small',
                                            'media-object img-circle pull-left mright10'
                                        ));

                                    }
                                }
                                $acounter++;
                                ?>
                            <?php }
                        }
						
                        if (count($attendees) > 2) {
                            ?>
                            <div class="more_users inline-block">
                                    <span class="no-img staff-profile-image-small media-object img-circle pull-left mright10"
                                          style="background-color:#ccc">+<?php echo count($attendees) - 2 ?></span>
                            <div class="more_users_view text-right">
                                <?php
								echo '<ul class="name-tooltip">';
                                foreach ($attendees as $attendee) {
									    echo '<li>'.$attendee['name'].'</li>';
                                } 
							echo '</ul>'; ?>
                            </div>
                            </div>
                        <?php } ?>
                    </span>
                </div>
            </div>
        </div>
    </li>
<?php // } ?>