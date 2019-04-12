<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 11-04-2018
 * Time: 04:24 PM
 */
$this->load->model('tasks_model');
$this->load->model('meetings_model');
$items = 3;
$interval = 3;
$widget_setting = json_decode($widget_data->widget_settings, true);
if (isset($widget_setting['upcoming_project'])) {
    $widget_setting = $widget_setting['upcoming_project'];
    $items = isset($widget_setting['items']) ? $widget_setting['items'] : 3;
    $interval = isset($widget_setting['time_frame']) ? $widget_setting['time_frame'] : 3;
}
?>

<div>
    <div class="panel-body upcoming-project-page" id="unique_upcoming_projects_widget">
        <div class="row">
            <div class="col-md-12 mbot10 posrel">
                <div class="handle"><img src="<?php echo site_url()?>assets\images\dragger.png" alt="dragger"></div>
                <h4 class="no-margin pull-left mbot10">Upcoming items</h4>
                <a href="#" data-toggle="modal" data-target="#upcoming_items_setting"
                   class="toggle_set_button"><i class="fa fa-cog menu-icon"></i></a>
                <a href="javascript:void(0)" class="toggle_control_cutton"
                   id="upcoming_project_collapse" data-pid="#unique_upcoming_projects_widget"><i
                            class="fa fa-caret-up"></i></a>
                <?php
                    $userdata = $this->session->userdata();
                    if($userdata['package_type_id']!=2){ ?>
                        <a href="javascript:void(0)" data-toggle="modal" data-target="#dashboard_create_list"
                           id="dashboard_action_button" class="dashboard_action_button"><i class="fa fa-plus menu-icon"></i></a>
                    <?php } ?>
            </div>
        </div>

        <div class="panel_s widget-body clearfix" id="upcoming_project_data">
            <div class="navbar navbar-light bg-faded no_bot_margin ">
                <ul class="nav nav-tabs all_items">
                    <li class="active"><a class="nav-item nav-link active" data-toggle="tab" href="#myitems">My
                            Items</a></li>
                    <li><a class="nav-item nav-link" data-toggle="tab" href="#allitems">All Items</a></li>
                </ul>
            </div>
            <div class="tab-content all_items_content">
                <div class="tab-pane active" id="myitems" data-item="<?php echo $items; ?>">
                    <div class="project_data_container">
                        <?php
                        $this->load->model('home_model');
                        $my_project_data = $this->home_model->get_my_all_project_data($interval);
                        $my_lead_data = $this->home_model->get_my_all_lead_data($interval);
                        $my_tasks_data = $this->home_model->get_my_all_tasks_data($interval);
                        $my_meeting_data = $this->home_model->get_my_all_meeting_data($interval);
                        $my_result = array_merge($my_project_data, $my_lead_data, $my_tasks_data, $my_meeting_data);
                        $arr = array();
                        foreach ($my_result as $key => $item) {
                            $startdate = _d($item['sorting_date']);
                            $arr[$startdate][$key] = $item;
                        }
                        ksort($arr);
                        $my_result = $arr;
                        if (!empty($my_result)) {
                            foreach ($my_result as $date => $pro_datas) {
                                if (strtotime($date) == strtotime(date('Y-m-d'))) {
                                    $daylable = "( TODAY )";
                                } elseif (strtotime($date) == strtotime(date("Y-m-d", strtotime("+1 day")))) {
                                    $daylable = "( TOMORROW )";
                                } else {
                                    $daylable = "";
                                }
                                ?>
                                <div class="lazy_content upcoming_project">
                                    <div class="row upcoming_dates">

                                        <div class="col-xs-12">
                                            <div class="edate">
                                                <div class="emonth"><?php echo date('M', strtotime($date)) ?></div>
                                                <div class="date"><?php echo date('d', strtotime($date)) ?></div>
                                            </div>
                                            <div class="day"><?php echo date('l', strtotime($date)) . " " . $daylable ?></div>
                                            <?php
                                            $userdata = $this->session->userdata();
                                            if($userdata['package_type_id']!=2){ ?>
                                                <a href="javascript:void(0)" data-toggle="modal"
                                                   data-target="#dashboard_create_list" id="duplicate_action_button"
                                                   class="duplicate_action_button"
                                                   data-date="<?php echo date('m/d/Y', strtotime($date)); ?>"><i
                                                            class="fa fa-plus menu-icon "></i></a>
                                            <?php } ?>

                                        </div>
                                    </div>
                                    <?php
                                    foreach ($pro_datas as $pro_data) {
                                        if (!isset($pro_data['id'])) {
                                            $pro_data['id'] = $pro_data['meetingid'];
                                        }
                                        $eventmonth = date("M", strtotime($pro_data['sorting_date']));
                                        $eventday = date("j", strtotime($pro_data['sorting_date']));
                                        $eventweekday = strtoupper(date("D", strtotime($pro_data['sorting_date'])));
                                        $eventyear = date("Y", strtotime($pro_data['sorting_date']));
                                        $full_date = _d($pro_data['sorting_date']);
                                        $full_time = _time($pro_data['sorting_date']);
                                        if (isset($pro_data['eventenddatetime'])) {
                                            $eventEndweekday = strtoupper(date("D", strtotime($pro_data['eventenddatetime'])));
                                            $fullEnddate = _d($pro_data['eventenddatetime']);
                                            $fullEndtime = _time($pro_data['eventenddatetime']);
                                        }
                                        ?>
                                        <div class="row upcoming_item_master_list_content">
                                            <div class="col-xs-10">
                                                <div class="upcoming_item_icon">
                                                    <div class="icon_section">
                                                        <?php if ($pro_data['type'] == "project") {
                                                            $icon = "fa-book";
                                                            $title = "<i class='fa fa-book'></i> PROJECT";
                                                        } elseif ($pro_data['type'] == "lead") {
                                                            $icon = "fa-tty";
                                                            $title = "<i class='fa fa-tty'></i> LEAD";
                                                        } elseif ($pro_data['type'] == "task") {
                                                            $icon = "fa-tasks";
                                                            $title = "<i class='fa fa-tasks'></i> TASKS";
                                                        } else {
                                                            $icon = "fa-meetup";
                                                            $title = "<i class='fa fa-handshake-o'></i> MEETING";
                                                        } ?>
                                                        <a href="javascript:void(0)"
                                                           rel="popover" title="<?php echo $title; ?>"
                                                           data-popover-content="#upcoming_myPopover_<?php echo $pro_data['id']; ?>">
                                                            <i class="fa <?php echo $icon; ?> menu-icon"></i>
                                                        </a>
                                                        <div id="upcoming_myPopover_<?php echo $pro_data['id'] ?>"
                                                             class="pinePopUp hide">
                                                            <div class="col-sm-2 popupover_date">
                                                                <div class="carddate-block">
                                                                    <div class="card_date"
                                                                         title="<?php echo $eventyear; ?>">
                                                                        <div class="card_month">
                                                                            <small><?php echo $eventmonth; ?></small>
                                                                        </div>
                                                                        <div class="card_d">
                                                                            <strong><?php echo date('d', strtotime($pro_data['sorting_date'])) ?></strong>
                                                                        </div>
                                                                        <div class="card_day">
                                                                            <small><?php echo $eventweekday ?></small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card_year">
                                                                        <small><?php echo $eventyear ?></small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-10 popupover_content">
                                                                <h4 class="mtop0 mbot5">
                                                                    <strong><?php echo $pro_data['name'] ?></strong>
                                                                </h4>
                                                                <?php if ($pro_data['type'] == "project" || $pro_data['type'] == "lead") { ?>
                                                                    <div class="mbot5">
                                                                        <i class="fa fa-map-marker"></i>
                                                                        <?php echo isset($pro_data['venuename']) ? $pro_data['venuename'] : "N/A"; ?>
                                                                    </div>
                                                                <?php } ?>
                                                                <div class="mbot5"><i
                                                                            class="fa fa-clock-o"></i> <?php echo $full_time; ?>
                                                                </div>
                                                                <div class="mbot5"><i
                                                                            class="fa fa-calendar-o"></i> <?php echo $eventweekday . ", " . $full_date . " at " . $full_time; ?>
                                                                </div>
                                                                <?php if ($pro_data['type'] == "project" || $pro_data['type'] == "lead") { ?>
                                                                    <div class="mbot5 mleft30"><strong>--to--</strong>
                                                                    </div>
                                                                    <div class="mbot5">
                                                                        <i class="fa fa-calendar-o"></i>
                                                                        <?php echo isset($pro_data['eventenddatetime']) ? $eventEndweekday . ", " . $fullEnddate . " at " . $fullEndtime : "N/A"; ?>
                                                                    </div>
                                                                <?php } ?>
                                                                <div class="mbot5"><span
                                                                            class="no-img staff-profile-image-small">
                                                      <?php echo isset($pro_data['assigned']) ? staff_profile_image($pro_data['assigned'], array('staff-profile-image-small')) : staff_profile_image(0, array('staff-profile-image-small')); ?>
                                                    </span>
                                                                    <?php echo $pro_data['firstname'] . " " . $pro_data['lastname']; ?>
                                                                </div>
                                                                <!--<div class="btn-row">
                                                    <a href="<?php /*echo admin_url('projects/dashboard/' . $pid . '?pg=home'); */ ?>"
                                                       class="btn btn-info display-block">
                                                        <i class="fa fa-link menu-icon mright5"></i>View</a>
                                                </div>-->
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="project-det-list">
                                                    <div class="upcoming_eve_ttl">
                                                        <strong>
                                                            <?php if ($pro_data['type'] == "project") { ?>
                                                                <a href="<?php echo admin_url('projects/dashboard/' . $pro_data['id'] . '?pg=home'); ?>"><?php echo $pro_data['name']; ?></a>
                                                            <?php } elseif ($pro_data['type'] == "lead") { ?>
                                                                <a href="<?php echo admin_url('leads/dashboard/' . $pro_data['id'] . '?pg=home'); ?>"><?php echo $pro_data['name']; ?></a>
                                                            <?php } elseif ($pro_data['type'] == "task") { ?>
                                                                <a href="<?php echo admin_url('tasks/dashboard/' . $pro_data['id'] . '?pg=home'); ?>"><?php echo $pro_data['name']; ?></a>
                                                            <?php } else { ?>
                                                                <a href="<?php echo admin_url('meetings/meeting/' . $pro_data['id'] . '?pg=home'); ?>"><?php echo $pro_data['name']; ?></a>
                                                            <?php } ?>
                                                        </strong>
                                                    </div>
                                                    <div class="project_list_time"><?php echo _time($pro_data['sorting_date']); ?></div>
                                                    <div class="project_list_assigned_name"><?php echo $pro_data['firstname'] . " " . $pro_data['lastname'] ?></div>
                                                </div>
                                            </div>
                                            <div class="col-xs-2">
                                                <div class="project-pimg">
                                                    <?php if ($pro_data['type'] == "project") {
                                                        echo project_profile_image($pro_data['id'], array('profile_image', 'lead-profile-image-small'), 'small');
                                                    } elseif ($pro_data['type'] == "lead") {
                                                        echo lead_profile_image($pro_data['id'], array('profile_image', 'lead-profile-image-small'), 'small');
                                                    } else {
                                                        if(isset($pro_data['rel_type']) && $pro_data['rel_id'] > 0 ){
                                                            if($pro_data['rel_type']=="project"){
                                                                echo project_profile_image($pro_data['rel_id'], array('profile_image', 'lead-profile-image-small'), 'small');
                                                            }else{
                                                                echo lead_profile_image($pro_data['rel_id'], array('profile_image', 'lead-profile-image-small'), 'small');
                                                            }
                                                        }else{?>
                                                            <img src="<?php echo base_url('assets/images/user-placeholder.jpg'); ?>" class="project-profile-image-small" alt="new project">
                                                        <?php } } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    } ?>
                                </div>
                                <?php
                            }
                        } else { ?>
                            <div class="mbot15">
                                No upcoming items found!
                            </div>
                        <?php } ?>
                        <?php if (count($my_result) > $items) { ?>
                            <div class="pinned_item_button_section">
                                <a href="javascript:void(0)" id="all_my_upcoming_loadMore"
                                   class="btn btn-info loadMore"
                                   data-widget="upcoming_project" data-pid="#myitems" data-item="<?php echo $items; ?>">Load
                                    More</a>
                                <a href="javascript:;" id="all_pinned_contact_loadless" data-widget="upcoming_project"
                                   data-pid="#myitems" data-item="<?php echo $items; ?>"
                                   class="loadless btn btn-info mright10"><i
                                            class="fa far fa-eye mright5"></i>Show Less</a>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="tab-pane" id="allitems" data-item="<?php echo $items; ?>">
                    <div class="project_data_container">
                        <?php
                        $this->load->model('home_model');
                        $project_data = $this->home_model->get_all_project_data($interval);
                        $lead_data = $this->home_model->get_all_lead_data($interval);
                        $tasks_data = $this->home_model->get_all_tasks_data($interval);
                        $meeting_data = $this->home_model->get_all_meeting_data($interval);
                        $result = array_merge($project_data, $lead_data, $tasks_data, $meeting_data);
                        /*function sortByOrder($a, $b)
                        {
                            return strtotime($a['sorting_date']) - strtotime($b['sorting_date']);
                        }

                        usort($result, 'sortByOrder');*/
                        $arr = array();
                        foreach ($result as $key => $item) {
                            /*if(isset($item['eventstartdatetime'])){
                                $startdate = date('Y-m-d', strtotime($item['eventstartdatetime']));
                            }else{

                                $startdate = date('Y-m-d', strtotime($item['duedate']));
                            }*/
                            $startdate = _d($item['sorting_date']);
                            $arr[$startdate][$key] = $item;
                        }
                        ksort($arr);
                        $result = $arr;
                        /*echo "<pre>";
                        print_r($result);
                        die();*/
                        if (!empty($result)) {
                            foreach ($result as $date => $pro_datas) {
                                if (strtotime($date) == strtotime(date('Y-m-d'))) {
                                    $daylable = "( TODAY )";
                                } elseif (strtotime($date) == strtotime(date("Y-m-d", strtotime("+1 day")))) {
                                    $daylable = "( TOMORROW )";
                                } else {
                                    $daylable = "";
                                }
                                ?>
                                <div class="lazy_content upcoming_project">
                                    <div class="row upcoming_dates">
                                        <div class="col-sm-12">
                                            <div class="edate">
                                                <div class="emonth"><?php echo date('M', strtotime($date)) ?></div>
                                                <div class="date"><?php echo date('d', strtotime($date)) ?></div>
                                            </div>
                                            <div class="day"><?php echo date('l', strtotime($date)) . " " . $daylable; ?></div>
                                            <?php
                                            $userdata = $this->session->userdata();
                                            if($userdata['package_type_id']!=2){ ?>
                                                <a href="javascript:void(0)" data-toggle="modal"
                                                   data-target="#dashboard_create_list" id="duplicate_action_button"
                                                   class="duplicate_action_button"
                                                   data-date="<?php echo date('m/d/Y', strtotime($date)); ?>">
                                                    <i class="fa fa-plus menu-icon "></i>
                                                </a>
                                            <?php } ?>

                                        </div>
                                    </div>
                                    <?php foreach ($pro_datas as $pro_data) {
                                        if (!isset($pro_data['id'])) {
                                            $pro_data['id'] = $pro_data['meetingid'];
                                        }
                                        $eventmonth = date("M", strtotime($pro_data['sorting_date']));
                                        $eventday = date("j", strtotime($pro_data['sorting_date']));
                                        $eventweekday = strtoupper(date("D", strtotime($pro_data['sorting_date'])));
                                        $eventyear = date("Y", strtotime($pro_data['sorting_date']));
                                        $fulldate = _d($pro_data['sorting_date']);
                                        $full_date = _d($pro_data['sorting_date']);
                                        $fulltime = _time($pro_data['sorting_date']);
                                        $full_time = _time($pro_data['sorting_date']);

                                        if (isset($pro_data['eventenddatetime'])) {
                                            $eventEndweekday = strtoupper(date("D", strtotime($pro_data['eventenddatetime'])));
                                            $fullEnddate = _d($pro_data['eventenddatetime']);
                                            $fullEndtime = _time($pro_data['eventenddatetime']);
                                        }
                                        ?>
                                        <div class="row upcoming_item_all_master_list_content upcoming_item_master_list_content">
                                            <div class="col-xs-10">
                                                <div class="upcoming_item_icon">
                                                    <div class="icon_section">
                                                        <?php if ($pro_data['type'] == "project") {
                                                            $icon = "fa-book";
                                                            $title = "<i class='fa fa-book'></i> PROJECT";
                                                        } elseif ($pro_data['type'] == "lead") {
                                                            $icon = "fa-tty";
                                                            $title = "<i class='fa fa-tty'></i> LEAD";
                                                        } elseif ($pro_data['type'] == "task") {
                                                            $icon = "fa-tasks";
                                                            $title = "<i class='fa fa-tasks'></i> TASKS";
                                                        } else {
                                                            $icon = "fa-meetup";
                                                            $title = "<i class='fa fa-handshake-o'></i> MEETING";
                                                        } ?>
                                                        <a href="javascript:void(0)"
                                                           rel="popover" title="<?php echo $title; ?>"
                                                           data-popover-content="#upcoming_myPopover_<?php echo $pro_data['id']; ?>">
                                                            <i class="fa <?php echo $icon; ?> menu-icon"></i>
                                                        </a>
                                                        <div id="upcoming_myPopover_<?php echo $pro_data['id'] ?>"
                                                             class="pinePopUp hide">
                                                            <div class="col-sm-2 popupover_date">
                                                                <div class="carddate-block">
                                                                    <div class="card_date"
                                                                         title="<?php echo $eventyear; ?>">
                                                                        <div class="card_month">
                                                                            <small><?php echo $eventmonth; ?></small>
                                                                        </div>
                                                                        <div class="card_d">
                                                                            <strong><?php echo date('d', strtotime($pro_data['sorting_date'])) ?></strong>
                                                                        </div>
                                                                        <div class="card_day">
                                                                            <small><?php echo $eventweekday ?></small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card_year">
                                                                        <small><?php echo $eventyear ?></small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-10 popupover_content">
                                                                <h4 class="mtop0 mbot5">
                                                                    <strong><?php echo $pro_data['name'] ?></strong>
                                                                </h4>
                                                                <div class="mbot5"><i
                                                                            class="fa fa-map-marker"></i> <?php echo isset($pro_data['venuename']) ? $pro_data['venuename'] : "N/A"; ?>
                                                                </div>
                                                                <div class="mbot5"><i
                                                                            class="fa fa-clock-o"></i> <?php echo $full_time; ?>
                                                                </div>
                                                                <div class="mbot5"><i
                                                                            class="fa fa-calendar-o"></i> <?php echo $eventweekday . ", " . $full_date . " at " . $full_time; ?>
                                                                </div>
                                                                <?php if ($pro_data['type'] == "project" || $pro_data['type'] == "lead") { ?>
                                                                    <div class="mbot5 mleft30"><strong>--to--</strong>
                                                                    </div>
                                                                    <div class="mbot5">
                                                                        <i class="fa fa-calendar-o"></i>
                                                                        <?php echo isset($pro_data['eventenddatetime']) ? $eventEndweekday . ", " . $fullEnddate . " at " . $fullEndtime : "N/A"; ?>
                                                                    </div>
                                                                <?php } ?>
                                                                <!--<div class="mbot5"><span class="tooltip_status">
                                                        <?php /*echo $single_msg_pin['msg_status']; */ ?>
                                                    </span></div>-->
                                                                <div class="mbot5">
                                                                    <span class="no-img staff-profile-image-small">
                                                      <?php echo isset($pro_data['assigned']) ? staff_profile_image($pro_data['assigned'], array('staff-profile-image-small')) : staff_profile_image(0, array('staff-profile-image-small')); ?>
                                                    </span>
                                                                    <?php echo $pro_data['firstname'] . " " . $pro_data['lastname']; ?>
                                                                </div>
                                                                <!--<div class="btn-row">
                                                    <a href="<?php /*echo admin_url('projects/dashboard/' . $pid . '?pg=home'); */ ?>"
                                                       class="btn btn-info display-block">
                                                        <i class="fa fa-link menu-icon mright5"></i>View</a>
                                                </div>-->
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="project-det-list">
                                                    <div class="upcoming_eve_ttl">
                                                        <strong>
                                                            <?php if ($pro_data['type'] == "project") { ?>
                                                                <a href="<?php echo admin_url('projects/dashboard/' . $pro_data['id'] . '?pg=home'); ?>"><?php echo $pro_data['name']; ?></a>
                                                            <?php } elseif ($pro_data['type'] == "lead") { ?>
                                                                <a href="<?php echo admin_url('leads/dashboard/' . $pro_data['id'] . '?pg=home'); ?>"><?php echo $pro_data['name']; ?></a>
                                                            <?php } elseif ($pro_data['type'] == "task") { ?>
                                                                <a href="<?php echo admin_url('tasks/dashboard/' . $pro_data['id'] . '?pg=home'); ?>"><?php echo $pro_data['name']; ?></a>
                                                            <?php } else { ?>
                                                                <a href="<?php echo admin_url('meetings/meeting/' . $pro_data['id'] . '?pg=home'); ?>"><?php echo $pro_data['name']; ?></a>
                                                            <?php } ?>
                                                        </strong>
                                                    </div>
                                                    <div class="project_list_time"><?php echo _time($pro_data['sorting_date']); ?></div>
                                                    <?php
                                                    if ($pro_data['type'] == "task") {
                                                        $id = $pro_data['id'];
                                                        $assignees = $this->tasks_model->get_task_assignees($id);
                                                        $counter=1;
                                                        foreach ($assignees as $assignee){
                                                            echo ucfirst($assignee['firstname']) ." ".ucfirst($assignee['lastname']);
                                                            if($counter != count($assignees)){
                                                                echo ", ";
                                                            }
                                                            $counter++;
                                                        }
                                                        ?>
                                                    <?php }elseif ($pro_data['type'] == "meeting"){
                                                        $id = $pro_data['id'];
                                                        $assignees = $this->meetings_model->get_meeting_users($id,'user_id');
                                                        $counter=1;
                                                        foreach ($assignees as $assignee){
                                                            echo get_staff_full_name($assignee);
                                                            if($counter != count($assignees)){
                                                                echo ", ";
                                                            }
                                                            $counter++;
                                                        }
                                                    ?>
                                                    <?php } else { ?>
                                                        <div class="project_list_assigned_name">
                                                            <?php echo $pro_data['firstname'] . " " . $pro_data['lastname'] ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="col-xs-2">
                                                <div class="project-pimg">
                                                    <?php if ($pro_data['type'] == "project") {
                                                        echo project_profile_image($pro_data['id'], array('profile_image', 'lead-profile-image-small'), 'small');
                                                    } elseif ($pro_data['type'] == "lead") {
                                                        echo lead_profile_image($pro_data['id'], array('profile_image', 'lead-profile-image-small'), 'small');
                                                    } else {
                                                        if(isset($pro_data['rel_type']) && $pro_data['rel_id'] > 0 ){
                                                            if($pro_data['rel_type']=="project"){
                                                                echo project_profile_image($pro_data['rel_id'], array('profile_image', 'lead-profile-image-small'), 'small');
                                                            }else{
                                                                echo lead_profile_image($pro_data['rel_id'], array('profile_image', 'lead-profile-image-small'), 'small');
                                                            }
                                                        }else{?>
                                                        <img src="<?php echo base_url('assets/images/user-placeholder.jpg'); ?>" class="project-profile-image-small" alt="new project">
                                                    <?php } } ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php }
                        } else { ?>
                            <div class="row mbot15">
                                No my upcoming items found!
                            </div>
                        <?php } ?>
                        <?php if (count($result) > $items) { ?>
                            <div class="pinned_item_button_section">
                                <a href="javascript:void(0)" id="all_master_upcoming_loadMore" class="loadMore"
                                   data-widget="upcoming_project" data-pid="#allitems"
                                   data-item="<?php echo $items; ?>">Load More</a>
                                <a href="javascript:;" id="all_pinned_contact_loadless" data-widget="upcoming_project"
                                   data-pid="#allitems" data-item="<?php echo $items; ?>"
                                   class="loadless btn btn-info mright10"><i
                                            class="fa far fa-eye mright5"></i>Show Less</a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="upcoming_items_setting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span>Upcoming Items Setting</span>
                </h4>
            </div>
            <div class="modal-body">
                <?php if (has_permission('items', '', 'create')) { ?>
                    <div class="row">
                        <form action="<?php echo admin_url() ?>home/dashboard_widget_setting" novalidate="1"
                              id="upcoming_items_setting_form" method="post"
                              accept-charset="utf-8">
                            <div id="additional"></div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="checkbox radio-primary radio-inline">
                                        <input type="checkbox" id="upcoming_project" name="widget_visibility"
                                               class="checkbox task" value="1">
                                        <label for="upcoming_project">Hide</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="selectdays">
                                        <select name="time_frame" class="selectpicker selectdays">
                                            <option value="3" <?php echo $interval == 3 ? "selected" : "" ?>>In Next 3
                                                Days
                                            </option>
                                            <option value="4" <?php echo $interval == 4 ? "selected" : "" ?>>In Next 4
                                                Days
                                            </option>
                                            <option value="5" <?php echo $interval == 5 ? "selected" : "" ?>>In Next 5
                                                Days
                                            </option>
                                            <option value="6" <?php echo $interval == 6 ? "selected" : "" ?>>In Next 6
                                                Days
                                            </option>
                                            <option value="7" <?php echo $interval == 7 ? "selected" : "" ?>>In Next 7
                                                Days
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                    </div>

                <?php } ?>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="widget" value="upcoming_project">
                <input type="hidden" name="user" value="<?php echo get_staff_user_id(); ?>">
                <button type="submit" class="btn btn-info widget_setting"
                        id="save_setting"><?php echo _l('Save'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>