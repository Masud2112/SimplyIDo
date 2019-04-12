<?php
if (isset($message['rel_type']) && $message['rel_id'] > 0) {
    $event = get_event_name($message['rel_type'], $message['rel_id']);
    $message['eventtypename'] = isset($event->name) ? $event->name : "";
}
/*if ($message['status'] == $status['statusid']) {*/
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
$CI->db->where('pintype', 'Message');
$CI->db->where('pintypeid', $message['id']);
$result = $CI->db->get()->row();

$CI->db->select('GROUP_CONCAT(id) as childids');
$CI->db->from('tblmessages');
$CI->db->where('parent', $message['id']);
$childids = $CI->db->get()->row();
if ($childids->childids == "") {
    $childids->childids = $message['id'];
}
$CI->db->select('count(*) as attachments');
$CI->db->from('tblmessagesattachment');
$CI->db->where('messageid=' . $message['id'] . ' OR messageid IN (' . $childids->childids . ')');
$attachments = $CI->db->get()->row();
$attachments = isset($attachments->attachments) ? $attachments->attachments : 0;


//echo '<pre>-->'; print_r($message); die;

?>
    <li data-message-id="<?php echo $message['id']; ?>"
        class="col-sm-6 col-lg-4 kanban-card-block kanban-card <?php echo $class ?>">
        <div class="panel-body card-body">
            <div class="row">

                <div class="col-xs-11 card-name">
                    <span class="messageFrom display-block">
                        <?php /*echo staff_profile_image($message['created_by'], array(
                            'staff-profile-image-small',
                            'media-object img-circle pull-left mright10'
                        )); */?>
                        <?php
                        if ($message['created_by_type'] == "teammember") {
                            echo staff_profile_image($message['created_by'], array(
                                'staff-profile-image-small',
                                'media-object img-circle pull-left mright10'
                            ));
                            $name = get_staff_full_name($message['created_by']);
                        } else {
                            echo addressbook_profile_image($message['created_by'], array(
                                'staff-profile-image-small',
                                'mright10'
                            ));
                            $name =  get_addressbook_full_name($message['created_by']);
                        } ?>

                        <div class="mfBlk">
                            <span clasName"><?php echo $name; ?></span>
                    <span class="mdate"><?php echo isset($message) ? date('D, M d, Y g:i A', strtotime($message['created_date'])) : ""; ?></span>
                </div>
                </span>

                <div class="message-body ">
                    <span class="messageNameTitle display-block">
                        <?php if (isset($lid)) { ?>
                            <a href="<?php echo admin_url('messages/view/' . $message['id'] . '?lid=' . $lid); ?>"><b><?php echo ($message['created_by_check_type'] == "child") ? "re: " . $message['subject'] : $message['subject']; ?></b></a>
                        <?php } else if (isset($pid)) { ?>
                            <a href="<?php echo admin_url('messages/view/' . $message['id'] . '?pid=' . $pid); ?>"><b><?php echo ($message['created_by_check_type'] == "child") ? "re: " . $message['subject'] : $message['subject']; ?></b></a>
                        <?php } else if (isset($eid)) { ?>
                            <a href="<?php echo admin_url('messages/view/' . $message['id'] . '?eid=' . $eid); ?>"><b><?php echo ($message['created_by_check_type'] == "child") ? "re: " . $message['subject'] : $message['subject']; ?></b></a>
                        <?php } else { ?>
                            <a href="<?php echo admin_url('messages/view/' . $message['id']); ?>"><b><?php echo ($message['created_by_check_type'] == "child") ? "re: " . $message['subject'] : $message['subject']; ?></b></a>
                        <?php } ?>
                    </span>
                    <span class="meetingRelType display-block">
                        <?php if ($message['rel_type'] == "project" || $message['rel_type'] == "event") { ?>
                            <i class="fa fa-book"></i>
                        <?php } elseif ($message['rel_type'] == "lead") { ?>
                            <i class="fa fa-tty"></i>
                        <?php } ?>

                        <?php echo isset($message['eventtypename']) ? $message['eventtypename'] : ""; ?>
                    </span>
                </div>
                <div class="clearfix"></div>


            </div>
            <div class="col-xs-1 text-muted">
                <div class="show-act-block"><?php
                    $options = "<div><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
                    /*$options .= '<li><a href=' . admin_url() . 'messages/message/' . $message['id'] . ' class="" title="View Dashboard"><i class="fa fa-eye"></i><span>View</span></a></li>';*/
                    if (has_permission('leads', '', 'edit')) {
                        $options .= '<li><a href=' . admin_url() . 'messages/view/' . $message['id'] . ' class="" title="Edit"><i class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>';
                    } else {
                        $options .= "";
                    }

                    if (has_permission('leads', '', 'delete')) {
                        $options .= '<li><a href=' . admin_url() . 'messages/delete/' . $message['id'] . ' class="_delete" title="Delete"><i class="fa fa-remove"></i><span>Delete</span></a></li>';
                    }
                    $options .= "</ul></div>";
                    echo $options;
                    ?></div>
                <div class="checkbox"><input type="checkbox" value="<?php echo $message['id'] ?>"><label></label>
                </div>
                <div class="pin-block">
                    <i class="fa fa-fw fa-thumb-tack message-pin <?php echo isset($result->pinned) ? "pinned" : ""; ?>"
                       title="<?php echo isset($result->pinned) ? "Unpin from Home" : "Pin to Home"; ?>"
                       id="<?php echo $message['id'] ?>" message_id="<?php echo $message['id'] ?>">

                    </i>
                </div>

            </div>
            <div class="clearfix"></div>
            <div class="card-footer">
                <div class="message_users">
                    <?php
                    $users = explode(',', $message['messageusers']);
                    $totalusers = count($users);
                    $counter = 1;
                    if ($message['messageusers'] != "") {
                    foreach ($users as $user) {
                        if ($counter <= 2) {
                            $user = explode('-', $user);
                            if ($user[0] == "teammember") {
                                echo staff_profile_image($user[1], array(
                                    'staff-profile-image-small',
                                    'media-object img-circle pull-left mright10'
                                ));
                            } else {
                                echo addressbook_profile_image($user[1], array(
                                    'staff-profile-image-small',
                                    'media-object img-circle pull-left mright10'
                                ));
                            }
                            $counter++;
                        }
                    }
                    if (count($users) > 2) { ?>
                    <div class="more_users">
                                    <span class="no-img staff-profile-image-small media-object img-circle pull-left mright10"
                                          style="background-color:#ccc">+<?php echo $totalusers - 2 ?></span>
                        <div class="more_users_view">

                            <?php echo '<ul class="name-tooltip">';
                            foreach ($users as $user) {
                                $user = explode('-', $user);
                                echo '<li>' . get_staff_full_name($user[1]) . '</li>';
                            }
                            echo '</ul>'; ?>
                        </div>
                        <?php }
                        }
                        ?>
                    </div>
                    <?php if ($message['chilemessages'] > 0) { ?>
                        <span class="replies inline-block pull-right">
                        <i class="fa fa-reply pull-left" data-toggle="tooltip"
                           data-title="<?php echo $message['chilemessages']; ?> Attachment(s)"></i>
                        <span class="replies_count"><?php echo $message['chilemessages']; ?></span></span>
                    <?php } ?>
                    <?php if ($attachments > 0) { ?>
                        <span class="attachments inline-block pull-right">
                                <i class="fa fa-paperclip pull-left"
                                   data-toggle="tooltip"
                                   data-title="<?php echo $attachments; ?> Attachment(s)"></i>
                        <span class="attachment_count"><?php echo $attachments; ?></span></span>
                    <?php } ?>

                </div>
            </div>
        </div>
    </li>
<?php //} ?>