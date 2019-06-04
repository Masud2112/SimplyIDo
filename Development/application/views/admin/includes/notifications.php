<a href="#" class="dropdown-toggle notifications-icon" data-toggle="dropdown" aria-expanded="false">
    <i class="fa fa-bell-o"></i> <span class="notiText"><?php if (is_mobile()) {
            echo 'Notification';
        } ?></span>
    <?php
    if ($unread_notifications > 0) { ?>
        <span class="label label-warning icon-total-indicator icon-notifications"><?php echo $unread_notifications; ?></span>
    <?php } ?>
</a>
<ul class="dropdown-menu notifications animated fadeIn width400"
    data-total-unread="<?php echo $unread_notifications; ?>">
    <li class="not_mark_all_as_read">
        <a href="#"
           onclick="mark_all_notifications_as_read_inline(); return false;"><?php echo _l('mark_all_as_read'); ?></a>
    </li>
    <?php
    $_notifications = $this->misc_model->get_user_notifications(false);
    foreach ($_notifications as $notification) {
        if (has_permission(strtolower($notification['not_type']), '','view')) {
            if ($notification['not_type'] == "leads") {
                if (is_lead_converted($notification['eid'])) {
                    $notification['link'] = get_lead_to_project_link($notification['eid']);
                }
            }
            ?>
            <li class="relative notification-wrapper" data-notification-id="<?php echo $notification['id']; ?>">
                <?php if (!empty($notification['link'])){ ?>
                <a href="<?php echo admin_url($notification['link']); ?>" class="notification-top notification-link">
                    <?php } ?>
                    <div class="notification-box<?php if ($notification['isread_inline'] == 0) {
                        echo ' unread';
                    } ?>">
                        <?php
                        if (($notification['fromcompany'] == NULL && $notification['fromuserid'] != 0) || ($notification['fromcompany'] == NULL && $notification['fromclientid'] != 0)) {
                            if ($notification['fromuserid'] != 0) {
                                echo staff_profile_image($notification['fromuserid'], array('staff-profile-image-small', 'img-circle notification-image', 'pull-left'));
                            } else {
                                echo '<img src="' . contact_profile_image_url($notification['fromclientid']) . '" class="client-profile-image-small img-circle pull-left notification-image">';
                            }
                        }
                        ?>
                        <div class="media-body">
                            <?php
                            $additional_data = array();
                            if (!empty($notification['additional_data'])) {
                                $additional_data = unserialize($notification['additional_data']);

                                $i = 0;
                                foreach ($additional_data as $data) {
                                    if (strpos($data, '<lang>') !== false) {
                                        $lang = get_string_between($data, '<lang>', '</lang>');
                                        $temp = _l($lang);
                                        if (strpos($temp, 'project_status_') !== FALSE) {
                                            $status = get_project_status_by_id(strafter($temp, 'project_status_'));
                                            $temp = $status['name'];
                                        }
                                        $additional_data[$i] = $temp;
                                    }
                                    $i++;
                                }
                            }
                            $touserids = explode(',', $notification['touserid']);
                            if (count($touserids) > 1) {
                                foreach ($touserids as $touserid) {
                                    if ($touserid == get_staff_user_id()) {
                                        //$additional_data[]="you";
                                        array_push($additional_data, 'you');
                                    } else {
                                        $name = $this->misc_model->get_username_by_id($touserid);
                                        array_push($additional_data, $name);
                                        //$additional_data[] = $name;
                                    }
                                }
                            } else {
                                if ($notification['touserid'] == get_staff_user_id()) {
                                    //$additional_data[]="you";
                                    array_push($additional_data, 'you');
                                } else {
                                    $name = $this->misc_model->get_username_by_id($notification['touserid']);
                                    //$additional_data[] = $name;
                                    array_push($additional_data, $name);
                                }
                            }
                            if (count($additional_data) > 1) {
                                $description = _l($notification['description'], $additional_data);
                            } else {
                                $description = _l($notification['description'], $additional_data);
                            }
                            if (($notification['fromcompany'] == NULL && $notification['fromuserid'] != 0) || ($notification['fromcompany'] == NULL && $notification['fromclientid'] != 0)) {
                                if ($notification['fromuserid'] != 0) {
                                    $description = $notification['from_fullname'] . ' - ' . $description;
                                } else {
                                    $description = $notification['from_fullname'] . ' - ' . $description . '<br /><span class="label inline-block mtop5 label-info">' . _l('is_customer_indicator') . '</span>';
                                }
                            }
                            echo '<span class="notification-title">' . $description . '</span>'; ?>
                            <small class="text-muted"><?php echo time_ago($notification['date']); ?></small>

                        </div>
                    </div>
                    <?php if (!empty($notification['link'])){ ?>
                </a>
            <?php } ?>
                <?php if ($notification['isread_inline'] == 0) { ?>
                    <!-- <a href="#" class="text-muted pull-right not-mark-as-read-inline" onclick="set_notification_read_inline(<?php echo $notification['id']; ?>);" data-placement="left" data-toggle="tooltip" data-title="<?php echo _l('mark_as_read'); ?>"><small><i class="fa fa-circle-thin" aria-hidden="true"></i></small></a> -->
                <?php } ?>
            </li>
        <?php }
    } ?>
    <?php if (count($_notifications) != 0) { ?>
        <li class="divider no-mbot"></li>
    <?php } ?>
    <li class="text-center">
        <?php if (count($_notifications) > 0) { ?>
            <a href="<?php echo admin_url('profile?notifications=true'); ?>"><?php echo _l('nav_view_all_notifications'); ?></a>
        <?php } else { ?>
            <?php echo _l('nav_no_notifications'); ?>
        <?php } ?>
    </li>
</ul>
