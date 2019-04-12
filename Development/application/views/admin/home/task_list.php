<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 11-04-2018
 * Time: 04:30 PM
 */

$items = 5;
$interval = 3;
$widget_setting = json_decode($widget_data->widget_settings, true);
if (isset($widget_setting['task_list'])) {
    $widget_setting = $widget_setting['task_list'];
    $items = isset($widget_setting['items']) ? $widget_setting['items'] : 5;
    $interval = isset($widget_setting['time_frame']) ? $widget_setting['time_frame'] : 3;
}
?>
<div class="">
    <div class="panel-body" id="unique_tasks_list_widget">
        <div class="row">
            <div class="col-md-12 mbot10 posrel">
                <div class="handle"><img src="<?php echo site_url() ?>assets\images\dragger.png" alt="dragger"></div>
                <h4 class="no-margin pull-left">Recent activities</h4>
                <a href="#" data-toggle="modal" data-target="#recent_activities_setting"
                   class="toggle_set_button"><i class="fa fa-cog menu-icon"></i></a>
                <a href="javascript:void(0)" class="toggle_control_cutton" id="task_list_collapse"
                   data-pid="#unique_tasks_list_widget">
                    <i class="fa fa-caret-up"></i></a>
            </div>
        </div>


        <div class="panel_s widget-body clearfix" id="task_list_data" data-item="<?php echo $items; ?>">
            <div class="col-md-12 activity_section">
                <?php

                $this->load->model('home_model');
                $activity_log_data = $this->home_model->get_all_activity_log_data($interval);
                $lead_activity_log_data = $this->home_model->get_all_lead_activity_log_data($interval);
                $all_activity_log_data = $this->home_model->get_all_stuff_activity_log_data($interval);
                $recent_act_result = array_merge($activity_log_data, $lead_activity_log_data, $all_activity_log_data);
                /********* modify by dipak  shows twice in recent activity,*************************************/
                $UniqItem = array();
                foreach ($recent_act_result as $result) {
                    if (!array_key_exists('name', $result)) {
                        $NameFilter = explode(':', $result['description']);
                        $result['name'] = str_replace("]", "", trim(end($NameFilter)));
                        $UniqItem[] = $result;
                    } else {
                        $UniqItem[] = $result;
                    }
                }
                $recent_act_result = $UniqItem;
                function act_sortByOrder($a, $b)
                {
                    return strtotime($b['act_sorting_date']) - strtotime($a['act_sorting_date']);
                }

                usort($recent_act_result, 'act_sortByOrder');

                $finalItemArray = [];
                $finalItem = [];

                foreach ($recent_act_result as $uniqItem) {
                    if ($uniqItem['name'] != $finalItem) {
                        $finalItemArray[] = $uniqItem;
                    }
                    $finalItem = $uniqItem['name'];
                }
                $recent_act_result = $finalItemArray;

                /*echo "<pre>";
                echo get_staff_user_id();
                print_r($recent_act_result);
                die('<--here');*/
                /**********************************************/
                foreach ($recent_act_result as $act_key => $ordered_activity_data) { ?>
                    <div class="row  activity_single task_list lazy_content recent_act_master_list_content">
                        <?php
                        if ($ordered_activity_data['activity_type'] == "project_info") {
                            $icon = "fa-book";
                            $title = "<i class='fa fa-book'></i>PROJECT";
                        } elseif ($ordered_activity_data['activity_type'] == "lead_info") {
                            $icon = "fa-tty";
                            $title = "<i class='fa fa-tty'></i>LEAD";
                        } else {
                            $task_stmt = explode("[", $ordered_activity_data['description']);
                            if (strpos($task_stmt[0], 'Proposal') !== false) {
                                $icon = "fa-file-text-o";
                                $title = "PROPOSAL";
                            } elseif (strpos($task_stmt[0], 'Payment Schedule') !== false) {
                                $icon = "fa-calendar";
                                $title = "PAYMENT SCHEDULE";
                            } elseif (strpos($task_stmt[0], 'Meeting') !== false) {
                                $icon = "fa-handshake-o";
                                $title = "Meeting";
                            } else {
                                $icon = "fa-tasks";
                                $title = "TASKS";
                            }
                        }
                        ?>
                        <div class="col-xs-2 activity_icon">
                            <div class="icon_section mright10">
                                <?php if ($ordered_activity_data['activity_type'] == "project_info") { ?>
                                    <a href="javascript:;" title="<?php echo $title; ?>" rel="popover"
                                       data-popover-content="#pro_activity_myPopover_<?php echo $act_key; ?>">
                                        <i class="fa <?php echo $icon; ?> menu-icon"></i>
                                    </a>
                                <?php } elseif ($ordered_activity_data['activity_type'] == "lead_info") { ?>
                                    <a href="javascript:;" title="<?php echo $title; ?>" rel="popover"
                                       data-popover-content="#lead_activity_myPopover_<?php echo $act_key; ?>">
                                        <i class="fa <?php echo $icon; ?> menu-icon"></i>
                                    </a>
                                <?php } else { ?>
                                    <a href="javascript:;" title="<?php echo $title; ?>">
                                        <i class="fa <?php echo $icon; ?> menu-icon"></i>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-xs-9 activity_desc">
                            <?php
                            if ($ordered_activity_data['activity_type'] == "project_info") { ?>
                                <?php $additional_data = '';
                                if (!empty($ordered_activity_data['additional_data'])) {
                                    $additional_data = $ordered_activity_data['additional_data'];
                                    if (is_serialized($additional_data)) {
                                        $additional_data = unserialize($additional_data);
                                    }
                                    //$additional_data = $ordered_activity_data['additional_data'];
                                    /*if ($ordered_activity_data['staff_id'] == get_staff_user_id()) {
                                        echo _l($ordered_activity_data['description_key'], $additional_data) . "";
                                    } else {
                                        echo ($ordered_activity_data['staff_id'] == 0) ? _l($ordered_activity_data['description_key'], $additional_data) : $ordered_activity_data['fullname'] . " " . _l($ordered_activity_data['description_key'], $additional_data);
                                    }*/
                                    echo _l($ordered_activity_data['description_key'], $additional_data) . "";
                                    ?>
                                <?php } else {
                                    $additional_data = array($ordered_activity_data['name']);
                                    echo _l($ordered_activity_data['description_key'], $additional_data) . "";
                                    ?>
                                    <?php /*if ($ordered_activity_data['staff_id'] == get_staff_user_id()) {
                                            echo "New Project Created: ";
                                        } else {
                                            echo $ordered_activity_data['fullname'] . ' - ';
                                        }
                                        //echo _l($ordered_activity_data['description_key']);
                                        */ ?>
                                <?php } ?><br/>
                            <?php } elseif ($ordered_activity_data['activity_type'] == "lead_info") {
                                if ($ordered_activity_data['description_key'] == "Created note") {
                                    $ordered_activity_data['description_key'] = "lead_activity_created";
                                }
                                ?>
                                <?php
                                $lead_additional_data = '';
                                if (!empty($ordered_activity_data['additional_data'])) {
                                    $lead_additional_data = unserialize($ordered_activity_data['additional_data']);
                                    if ($ordered_activity_data['description_key'] == "not_lead_activity_status_updated" || $ordered_activity_data['description_key'] == "not_lead_activity_assigned_to") {
                                        unset($lead_additional_data['0']);
                                    }

                                    /*if ($ordered_activity_data['staffid'] != get_staff_user_id()) {
                                        echo "<strong>";
                                        echo ($ordered_activity_data['staffid'] == 0) ? _l($ordered_activity_data['description_key'], $lead_additional_data) : "New Lead Created" . str_replace($ordered_activity_data['full_name'], "", _l($ordered_activity_data['description'], $lead_additional_data));
                                        echo "</strong>";
                                    } else {
                                        echo ($ordered_activity_data['staffid'] == 0) ? _l($ordered_activity_data['description_key'], $lead_additional_data) : "You created new lead" . ' - ' . _l($ordered_activity_data['description_key'], $lead_additional_data);
                                    }*/
                                    echo _l($ordered_activity_data['description_key'], $lead_additional_data) . "";
                                    ?>
                                <?php } else {
                                    $additional_data = array($ordered_activity_data['name']);
                                    echo _l($ordered_activity_data['description_key'], $additional_data) . "";
                                    ?>
                                    <?php /*if ($ordered_activity_data['staffid'] == get_staff_user_id()) {
                                            echo "New Lead Created:";
                                        } else {
                                            echo $ordered_activity_data['full_name'] . ' - ';
                                        }
                                        if ($ordered_activity_data['custom_activity'] == 0) {
                                            echo _l($ordered_activity_data['description_key']);
                                        } else {
                                            echo _l($ordered_activity_data['description_key'], '', false);
                                        } */ ?>
                                <?php } ?>
                                <br/>
                            <?php } else { ?>
                                <strong>
                                    <?php
                                    $task_stmt = explode("[", $ordered_activity_data['description']);
                                    if (count($task_stmt) > 1) {

                                        echo str_replace('Template', "", $task_stmt[0]) . ":";
                                    } else {
                                        echo $ordered_activity_data['description'];
                                    } ?></strong>
                                <span class="rec_act_ttl">
                                    <?php
                                    if (count($task_stmt) > 1) {

                                        $ttl = str_replace(']', "", $task_stmt[1]);
                                        $ttl = str_replace('ID:', "", $ttl);
                                        $ttl = str_replace('Invoice', "", $ttl);
                                        $ttl = str_replace('Description:', ".", $ttl);
                                        $ttl = str_replace(', Name:', ".", $ttl);
                                        $ttl = str_replace('Name:', ".", $ttl);
                                        if ($task_stmt[0] == 'Email Send To ') {
                                            $ttl = str_replace('Email:', "", $ttl);
                                            $ttl = str_replace(', Template', "", $ttl);
                                            $ttl = explode(":", $ttl);
                                            echo $ttl[0] . " for " . $ttl[1];
                                        } elseif ($task_stmt[0] == "Contact added ") {
                                            $ttl = str_replace('.', "", $ttl);
                                            echo get_addressbook_full_name($ttl);
                                        } else {
                                            $ttl = explode(".", $ttl);
                                            if (count($ttl) > 1) {
                                                echo $ttl[1];
                                            }
                                        }

                                    }

                                    ?></span>
                                <br/>
                            <?php } ?>
                            <?php
                            if (isset($ordered_activity_data['dateadded'])) {
                                $ordered_activity_data['date'] = $ordered_activity_data['dateadded'];
                            }
                            $eventday = strtoupper(date("D", strtotime($ordered_activity_data['date'])));
                            //$full_date = date("d/m/y", strtotime($ordered_activity_data['date']));
                            $full_date = _dt($ordered_activity_data['date']);
                            $full_time = _time($ordered_activity_data['date']);
                            $activity_am_pm = date("A", strtotime($ordered_activity_data['date']));
                            if (isset($ordered_activity_data['staff_id'])) {
                                $ordered_activity_data['staffid'] = $ordered_activity_data['staff_id'];
                            }
                            if ($ordered_activity_data['staffid'] == get_staff_user_id()) {
                                $ordered_activity_data['full_name'] = "You";
                            } else {
                                $ordered_activity_data['full_name'] = get_staff_full_name($ordered_activity_data['staffid']);
                            }
                            if (isset($ordered_activity_data['updatedby']) && is_numeric($ordered_activity_data['updatedby']) && $ordered_activity_data['updatedby'] > 0 && $ordered_activity_data['updatedby'] == get_staff_user_id()) {
                                $ordered_activity_data['updated_by_name'] = "You";
                            }
                            if ($ordered_activity_data['activity_type'] == "project_info") { ?>
                                <?php
                                if (isset($ordered_activity_data['updated_by_name']) && !empty($ordered_activity_data['updated_by_name'])) {
                                    echo $eventday . " On " . $full_date . " at " . $full_time . " by " . $ordered_activity_data['updated_by_name'];
                                } else {
                                    echo $eventday . ", " . $full_date . " at " . $full_time . " by " . $ordered_activity_data['full_name'];
                                }
                                ?>
                            <?php } elseif ($ordered_activity_data['activity_type'] == "lead_info") {
                                /*if (is_numeric($ordered_activity_data['staffid'])) {
                                    $ordered_activity_data['full_name'] = get_staff_full_name($ordered_activity_data['full_name']);
                                }*/
                                if (isset($ordered_activity_data['lead_updated_by_name']) && !empty($ordered_activity_data['lead_updated_by_name'])) {
                                    echo $eventday . " On " . $full_date . " at " . $full_time . " by " . $ordered_activity_data['lead_updated_by_name'];
                                } else {
                                    echo $eventday . ", " . $full_date . " at " . $full_time . " by " . $ordered_activity_data['full_name'];
                                } ?>
                            <?php } else {
                                if (is_numeric($ordered_activity_data['staffid'])) {
                                    //$ordered_activity_data['full_name'] = get_staff_full_name($ordered_activity_data['staffid']);
                                }
                                if (isset($ordered_activity_data['staffid']) && $ordered_activity_data['staffid'] != "") {
                                    echo $eventday . ", " . $full_date . " at " . $full_time . " by " . $ordered_activity_data['full_name'];
                                } else {
                                    echo $eventday . ", " . $full_date . " at " . $full_time . " by " . $ordered_activity_data['full_name'];
                                } ?>
                            <?php } ?>
                            <?php if ($ordered_activity_data['activity_type'] == "project_info") { ?>
                                <?php
                                $proj_act_eventweekday = strtoupper(date("D", strtotime($ordered_activity_data['eventstartdatetime'])));
                                $proj_act_eventyear = date("Y", strtotime($ordered_activity_data['eventstartdatetime']));
                                //$proj_act_full_date = date("m/d/Y", strtotime($ordered_activity_data['eventstartdatetime']));
                                $proj_act_full_date = _dt($ordered_activity_data['eventstartdatetime']);
                                //$proj_act_full_time = date("h:i", strtotime($ordered_activity_data['eventstartdatetime']));
                                $proj_act_full_time = _time($ordered_activity_data['eventstartdatetime']);
                                ?>
                            <?php } elseif ($ordered_activity_data['activity_type'] == "lead_info") {
                                $proj_act_eventweekday = date("D", strtotime($ordered_activity_data['eventstartdatetime']));
                                $proj_act_eventyear = date("Y", strtotime($ordered_activity_data['eventstartdatetime']));
                                //$proj_act_full_date = date("m/d/Y", strtotime($ordered_activity_data['eventstartdatetime']));
                                $proj_act_full_date = _dt($ordered_activity_data['eventstartdatetime']);
                                $proj_act_full_time = _time($ordered_activity_data['eventstartdatetime']);
                            } ?>
                        </div>

                        <?php if ($ordered_activity_data['activity_type'] == "project_info") { ?>
                            <div class="col-xs-1">
                                <div id="pro_activity_myPopover_<?php echo $act_key; ?>"
                                     class="pinePopUp hide">
                                    <div class="col-sm-2 popupover_date">
                                        <div class="carddate-block">
                                            <div class="card_date"
                                                 title="<?php echo date('Y', strtotime($proj_act_full_date)) ?>">
                                                <div class="card_month">
                                                    <small><?php echo date('M', strtotime($proj_act_full_date)) ?></small>
                                                </div>
                                                <div class="card_d">
                                                    <strong><?php echo date('d', strtotime($proj_act_full_date)) ?></strong>
                                                </div>
                                                <div class="card_day">
                                                    <small><?php echo date('D', strtotime($proj_act_full_date)) ?></small>
                                                </div>
                                            </div>
                                            <div class="card_year">
                                                <small><?php echo date('Y', strtotime($proj_act_full_date)) ?></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-10 popupover_content">
                                        <h4 class="mtop0 mbot5">
                                            <strong>
                                                <?php echo(isset($ordered_activity_data['name']) ? $ordered_activity_data['name'] : 'N/A'); ?>
                                            </strong>
                                        </h4>
                                        <div class="mbot5">
                                            <i class="fa fa-map-marker"></i>
                                            <?php echo(isset($activity_data['venue']) ? $activity_data['venue'] : 'N/A'); ?>
                                        </div>
                                        <div class="mbot5"><i
                                                    class="fa fa-clock-o"></i> <?php echo $proj_act_full_time; ?></div>
                                        <div class="mbot5"><i
                                                    class="fa fa-calendar"></i> <?php echo $proj_act_eventweekday . ", " . $proj_act_full_date; ?>
                                        </div>
                                        <div class="mbot5"><span class="tooltip_status">
                                                        <?php echo $ordered_activity_data['pro_act_status']; ?>
                                                    </span></div>
                                        <div class="mbot5">
                                            <?php echo $ordered_activity_data['act_ass_fname'] . " " . $ordered_activity_data['act_ass_lname']; ?>
                                        </div>
                                    </div>

                                </div>
                                <span class="no-img staff-profile-image-small">
                                        <?php
                                        if (isset($ordered_activity_data['staffid'])) {
                                            echo staff_profile_image($ordered_activity_data['staffid'], array(
                                                'staff-profile-image-small'
                                            ));
                                        } else {
                                            echo staff_profile_image($ordered_activity_data['staff_id'], array(
                                                'staff-profile-image-small'
                                            ));
                                        }
                                        ?>
                                </span>
                            </div>
                        <?php } ?>

                        <?php if ($ordered_activity_data['activity_type'] == "lead_info") {
                            /*echo "<pre>";
                            print_r($ordered_activity_data);*/
                            ?>
                            <div class="col-xs-1">
                                <div id="lead_activity_myPopover_<?php echo $act_key; ?>"
                                     class="pinePopUp hide">
                                    <div class="col-sm-2 popupover_date">
                                        <div class="carddate-block">
                                            <div class="card_date"
                                                 title="<?php echo date('Y', strtotime($proj_act_full_date)) ?>">
                                                <div class="card_month">
                                                    <small><?php echo date('M', strtotime($proj_act_full_date)) ?></small>
                                                </div>
                                                <div class="card_d">
                                                    <strong><?php echo date('d', strtotime($proj_act_full_date)) ?></strong>
                                                </div>
                                                <div class="card_day">
                                                    <small><?php echo date('D', strtotime($proj_act_full_date)) ?></small>
                                                </div>
                                            </div>
                                            <div class="card_year">
                                                <small><?php echo date('Y', strtotime($proj_act_full_date)) ?></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-10 popupover_content">
                                        <h4 class="mtop0 mbot5">
                                            <strong>
                                                <?php echo(isset($ordered_activity_data['name']) ? $ordered_activity_data['name'] : 'N/A'); ?>
                                            </strong>
                                        </h4>
                                        <div class="mbot5">
                                            <i class="fa fa-map-marker"></i>
                                            <?php echo(isset($activity_data['venue']) ? $activity_data['venue'] : 'N/A'); ?>
                                        </div>
                                        <div class="mbot5"><i
                                                    class="fa fa-clock-o"></i> <?php echo isset($proj_act_full_time) ? $proj_act_full_time : ""; ?>
                                        </div>
                                        <div class="mbot5"><i
                                                    class="fa fa-calendar"></i> <?php echo $proj_act_eventweekday . ", " . $proj_act_full_date; ?>
                                        </div>
                                        <div class="mbot5"><span class="tooltip_status">
                                                        <?php echo $ordered_activity_data['lead_act_status']; ?>
                                                    </span></div>
                                        <div class="mbot5">
                                            <?php echo $ordered_activity_data['lead_act_ass_fname'] . " " . $ordered_activity_data['lead_act_ass_lname']; ?>
                                        </div>
                                    </div>

                                </div>
                                <span class="no-img staff-profile-image-small">
                                        <?php
                                        if (isset($ordered_activity_data['staffid'])) {
                                            echo staff_profile_image($ordered_activity_data['staffid'], array(
                                                'staff-profile-image-small'
                                            ));
                                        } else {
                                            echo staff_profile_image($ordered_activity_data['staff_id'], array(
                                                'staff-profile-image-small'
                                            ));
                                        }
                                        ?>
                                </span>
                            </div>
                        <?php } ?>

                        <?php if ($ordered_activity_data['activity_type'] == "all_info") { ?>
                            <!--  <div class="col-xs-1 mright20">
                              <a href="javascript:;"><i
                                            class="fa fa-tasks menu-icon list_pin_icon"></i></a>
                            </div>-->
                            <div class="col-xs-1">
                                      <span class="no-img staff-profile-image-small">
                                        <?php
                                        $ordered_activity_data['staffid'];
                                        if (isset($ordered_activity_data['staffid']) && is_numeric($ordered_activity_data['staffid'])) {
                                            echo staff_profile_image($ordered_activity_data['staffid'], array(
                                                'staff-profile-image-small'
                                            ));
                                        } else { ?>
                                            <span class="no-img staff-profile-image-small"
                                                  style="background-color:#22bca7">
                                        <?php
                                        if (isset($ordered_activity_data['staffid'])) {
                                            $ordered_activity_data['staffid'] = explode(" ", $ordered_activity_data['staffid']);
                                            echo substr($ordered_activity_data['staffid'][0], 0, 1) . substr($ordered_activity_data['staffid'][1], 0, 1);
                                        } else {
                                            echo "N/A";
                                        } ?>
                                      </span>
                                        <?php }
                                        ?>
                                      </span>
                            </div>
                        <?php } ?>

                    </div>
                <?php } ?>
                <?php if (count($recent_act_result) > 5) { ?>
                    <div class="pinned_item_button_section">
                        <a href="#" id="all_recent_act_loadMore"
                           class="btn loadMore btn-info" data-widget="task_list"
                           data-pid="#task_list_data" data-item="<?php echo $items; ?>"><i
                                    class="fa far fa-spinner mright5"></i>Load More</a>
                        <a href="javascript:;" id="all_recent_act_loadless" data-widget="task_list"
                           data-pid="#task_list_data" data-item="<?php echo $items; ?>"
                           class="loadless btn btn-info"><i
                                    class="fa far fa-eye mright5"></i>Show Less</a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="recent_activities_setting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span>Recent Activities Setting</span>
                </h4>
            </div>
            <div class="modal-body">
                <?php if (has_permission('items', '', 'create')) { ?>
                    <div class="row">
                        <form action="<?php echo admin_url() ?>home/dashboard_widget_setting" novalidate="1"
                              id="recent_activities_setting_form" method="post"
                              accept-charset="utf-8">
                            <div id="additional"></div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="checkbox radio-primary radio-inline">
                                        <input type="checkbox" id="dashboard_task_list" name="widget_visibility"
                                               class="checkbox task" value="1">
                                        <label for="dashboard_task_list">Hide</label>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Number of messages to display </label>
                                        <input type="number" name="items" class="form-control" min="5"
                                               value="<?php echo $items; ?>">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="selectdays">
                                        <select name="time_frame" class="selectpicker selectdays">
                                            <option value="3" <?php echo $interval == 3 ? "selected" : "" ?> >Last 3
                                                Days
                                            </option>
                                            <option value="7" <?php echo $interval == 7 ? "selected" : "" ?>>Last 7
                                                Days
                                            </option>
                                            <option value="30" <?php echo $interval == 30 ? "selected" : "" ?>>Last 30
                                                Days
                                            </option>
                                            <!--<option value="6">Last 20 Days</option>
                                            <option value="7">Last 25 Days</option>-->
                                        </select>
                                    </div>
                                </div>
                            </div>
                    </div>

                <?php } ?>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="widget" value="task_list">
                <input type="hidden" name="user" value="<?php echo get_staff_user_id(); ?>">
                <button type="submit" class="btn btn-info" id="save_setting"><?php echo _l('Save'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>