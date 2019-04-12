<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 11-04-2018
 * Time: 04:23 PM
 */

$widget_setting = json_decode($widget_data->widget_settings, true);
if (isset($widget_setting['pinned_item'])) {
    $widget_setting = $widget_setting['pinned_item'];
}
$items = isset($widget_setting['items']) ? $widget_setting['items'] : 5;
$pinned_item_all = count($project_pinned_data) + count($lead_pinned_data) + count($task_pinned_data) + count($message_pinned_data);
?>

<div class="">
    <div class="panel-body" id="unique_pinned_item_widget">
        <div class="row">
            <div class="col-md-12 mbot10 posrel">
                <div class="handle"><img src="<?php echo site_url() ?>assets\images\dragger.png" alt="dragger"></div>
                <h4 class="no-margin pull-left">Pinned items</h4>
                <a href="#" data-toggle="modal" data-target="#pinned_items_setting"
                   class="toggle_set_button"><i class="fa fa-cog menu-icon"></i></a>
                <a href="javascript: void(0)" class="toggle_control_cutton" id="pinned_item_collapse"
                   aria-expanded="true" data-pid="#unique_pinned_item_widget"><i
                            class="fa fa-caret-up"></i></a>
            </div>
        </div>

        <div class="panel_s widget-body clearfix" id="pinned_item_data">
            <div class="navbar navbar-light bg-faded no_bot_margin">
                <ul class="nav nav-tabs">
                    <li class="active"><a class="nav-item nav-link"
                                          data-toggle="tab" href="#pinned_item_all">All (<span
                                    class="master_pin_items_cnt"><?php echo count($project_pinned_data) + count($lead_pinned_data) + count($task_pinned_data) + count($message_pinned_data) ?></span>)</a>
                    </li>
                    <li><a class="nav-item nav-link" data-toggle="tab"
                           href="#pinned_item_projects">Projects (<span
                                    class="all_proj_only_count"><?php echo count($project_pinned_data); ?></span>)</a>
                    </li>
                    <li><a class="nav-item nav-link" data-toggle="tab"
                           href="#pinned_item_leads">Leads (<span
                                    class="all_lead_only_count"><?php echo count($lead_pinned_data); ?></span>)</a>
                    </li>
                    <li><a class="nav-item nav-link" data-toggle="tab"
                           href="#pinned_item_tasks">Tasks (<span
                                    class="all_task_only_count"><?php echo count($task_pinned_data); ?></span>)</a>
                    </li>
                    <li><a class="nav-item nav-link" data-toggle="tab"
                           href="#pinned_item_messages">Messages (<span
                                    class="all_msg_only_count"><?php echo count($message_pinned_data); ?></span>)</a>
                    </li>
                </ul>
            </div>


            <div class="tab-content pin_items_container">
                <div class="tab-pane active" id="pinned_item_all" data-item="<?php echo $items; ?>">
                    <?php
                    $pinned_result_set = array_merge($project_pinned_data, $lead_pinned_data, $task_pinned_data, $message_pinned_data);
                    function sortBypinOrder($m, $n)
                    {
                        return strtotime($m['sorting_date']) - strtotime($n['sorting_date']);
                    }

                    usort($pinned_result_set, 'sortBypinOrder');
                    ?>

                    <?php
                    if (!empty($pinned_result_set)) {
                    foreach ($pinned_result_set as $pin_set_key => $pinned_result) {
                    /*echo "<pre>";
                    print_r($pinned_result);*/
                    $pin_eventmonth = date("M", strtotime($pinned_result['sorting_date']));
                    $pin_eventday = date("j", strtotime($pinned_result['sorting_date']));
                    $pin_eventweekday = strtoupper(date("D", strtotime($pinned_result['sorting_date'])));
                    $pin_eventyear = date("Y", strtotime($pinned_result['sorting_date']));
                    $full_date = _d($pinned_result['sorting_date']);
                    $full_time = _time($pinned_result['sorting_date']);
                    $pstatus = "";
                    if (isset($pinned_result['eventenddatetime'])) {
                        $eventEndweekday = date("D", strtotime($pinned_result['eventenddatetime']));
                        $fullEnddate = _d($pinned_result['eventenddatetime']);
                        $fullEndtime = _time($pinned_result['eventenddatetime']);
                    }
                    if ($pinned_result['pintype'] == "Project") {
                        $pinned_title = $pinned_result['pro_name'];
                        $ptype = $pinned_result['pro_event_type'];
                        $pstatus = $pinned_result['pro_status'];
                        $ass_fname = $pinned_result['pro_ass_fname'];
                        $ass_lname = $pinned_result['pro_ass_lname'];
                        $pid = $pinned_result['pro_id'];
                        $icon = "fa-book";
                        $pstatus_color = $pinned_result['pro_status_color'];
                    } elseif ($pinned_result['pintype'] == "Lead") {
                        $pinned_title = $pinned_result['lead_name'];
                        $ptype = $pinned_result['lead_event_type'];
                        $pstatus = $pinned_result['lead_status'];
                        $ass_fname = $pinned_result['lead_ass_fname'];
                        $ass_lname = $pinned_result['lead_ass_lname'];
                        $pid = $pinned_result['lead_id'];
                        $icon = "fa-tty";
                        $pstatus_color = $pinned_result['lead_status_color'];
                    } elseif ($pinned_result['pintype'] == "Task") {
                        $pinned_title = $pinned_result['task_name'];
                        $ptype = $pinned_result['task_event_type'];
                        $pstatus = $pinned_result['task_status'];
                        $ass_fname = $pinned_result['task_ass_fname'];
                        $ass_lname = $pinned_result['task_ass_lname'];
                        $pid = $pinned_result['task_id'];
                        $icon = "fa-tasks";
                        $pstatus_color = $pinned_result['task_status_color'];
                    } elseif ($pinned_result['pintype'] == "Message") {
                        $pinned_title = $pinned_result['subject'];
                        $ass_fname = $pinned_result['firstname'];
                        $ass_lname = $pinned_result['lastname'];
                        $pid = $pinned_result['msg_id'];
                        $icon = "fa-envelope-o";
                    }
                    ?>
                    <div class="row lazy_content pinned_item pinned_item_master_list_content"
                         id="pinned_item_project_<?php echo $pin_set_key; ?>">
                        <div class="col-sm-1 col-xs-1">

                            <?php if ($pinned_result['pintype'] == "Project") { ?>
                                <i class="fa fa-fw fa-thumb-tack pinned list_pin_icon project-pin"
                                   title="Unpin from dashboard"
                                   id="<?php echo $pinned_result['pro_id']; ?>"
                                   project_id="<?php echo $pinned_result['pro_id']; ?>"></i>
                            <?php } elseif ($pinned_result['pintype'] == "Lead") { ?>
                                <i class="fa fa-fw fa-thumb-tack pinned list_pin_icon lead-pin"
                                   title="Unpin from dashboard"
                                   id="<?php echo $pinned_result['lead_id']; ?>"
                                   lead_id="<?php echo $pinned_result['lead_id']; ?>"></i>
                            <?php } elseif ($pinned_result['pintype'] == "Task") { ?>
                                <i class="fa fa-fw fa-thumb-tack pinned list_pin_icon task-pin"
                                   title="Unpin from dashboard"
                                   id="<?php echo $pinned_result['task_id']; ?>"
                                   task_id="<?php echo $pinned_result['task_id']; ?>"></i>
                            <?php } elseif ($pinned_result['pintype'] == "Message") { ?>
                                <i class="fa fa-fw fa-thumb-tack pinned list_pin_icon message-pin"
                                   title="Unpin from dashboard"
                                   id="<?php echo $pinned_result['msg_id'] ?>"
                                   message_id="<?php echo $pinned_result['msg_id'] ?>"></i>
                            <?php } else { ?>
                                <i class="fa fa-fw fa-thumb-tack pinned list_pin_icon"></i>
                            <?php } ?>

                        </div>
                        <div class="col-sm-8 col-xs-6">
                            <div class="task-det">
                                <div>
                                    <?php if ($pinned_result['pintype'] == "Project") { ?>
                                        <a href="<?php echo admin_url('projects/dashboard/' . $pinned_result['pro_id'] . '?pg=home'); ?>"><?php echo $pinned_result['pro_name'] ?></a>
                                    <?php } elseif ($pinned_result['pintype'] == "Lead") { ?>
                                        <a href="<?php echo admin_url('leads/dashboard/' . $pinned_result['lead_id'] . '?pg=home'); ?>"><?php echo $pinned_result['lead_name'] ?></a>
                                    <?php } elseif ($pinned_result['pintype'] == "Task") { ?>
                                        <a href="<?php echo admin_url('tasks/dashboard/' . $pinned_result['task_id'] . '?pg=home'); ?>"><?php echo $pinned_result['task_name'] ?></a>
                                    <?php } elseif ($pinned_result['pintype'] == "Message") { ?>
                                        <a href="<?php echo admin_url('messages?pg=home'); ?>"><?php echo $pinned_result['subject'] ?></a>
                                    <?php } ?>
                                </div>
                                <div class="project_list_time"><?php echo $pin_eventweekday; ?>
                                    , <?php echo $full_date; ?></div>
                            </div>
                        </div>
                        <div class="col-sm-1 col-xs-2">
                            <div>
                                <a href="javascript:;"
                                   title="<?php echo "<i class='fa " . $icon . "'></i> " . $pinned_result['pintype']; ?>"
                                   rel="popover"
                                   data-popover-content="#pine_set_myPopover_<?php echo $pin_set_key; ?>">
                                    <?php if ($pinned_result['pintype'] == "Project") { ?>
                                        <i class="fa fa-handshake-o menu-icon list_info_icon"></i>
                                    <?php } elseif ($pinned_result['pintype'] == "Lead") { ?>
                                        <i class="fa fa-tty menu-icon list_info_icon"></i>
                                    <?php } elseif ($pinned_result['pintype'] == "Task") { ?>
                                        <i class="fa fa-tasks menu-icon list_info_icon"></i>
                                    <?php } elseif ($pinned_result['pintype'] == "Message") { ?>
                                        <i class="fa fa-envelope-o menu-icon list_info_icon"></i>
                                    <?php } ?>
                                </a>
                                <div id="pine_set_myPopover_<?php echo $pin_set_key; ?>"
                                     class="pinePopUp hide">
                                    <div class="col-sm-2 popupover_date">
                                        <div class="carddate-block">
                                            <div class="card_date"
                                                 title="<?php echo date('Y', strtotime($full_date)) ?>">
                                                <div class="card_month">
                                                    <small><?php echo date('M', strtotime($full_date)) ?></small>
                                                </div>
                                                <div class="card_d">
                                                    <strong><?php echo date('d', strtotime($full_date)) ?></strong>
                                                </div>
                                                <div class="card_day">
                                                    <small><?php echo date('D', strtotime($full_date)) ?></small>
                                                </div>
                                            </div>
                                            <div class="card_year">
                                                <small><?php echo date('Y', strtotime($full_date)) ?></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-10 popupover_content">
                                        <h4 class="mtop0 mbot5">
                                            <strong><?php echo $pinned_title ?></strong>
                                        </h4>
                                        <?php if ($pinned_result['pintype'] == "Project" || $pinned_result['pintype'] == "Lead") {
                                            ?>
                                            <div class="mbot5">
                                                <i class="fa fa-map-marker"></i>
                                                <?php echo isset($pinned_result['venuelocation']) ? $pinned_result['venuelocation'] : "  N/A"; ?>
                                            </div>
                                        <?php } ?>
                                        <div class="mbot5"><i
                                                    class="fa fa-clock-o"></i> <?php echo $full_time; ?></div>
                                        <div class="mbot5"><i
                                                    class="fa fa-calendar-o"></i> <?php echo $pin_eventweekday . ", " . $full_date . " at " . $full_time; ?>
                                        </div>
                                        <?php if ($pinned_result['pintype'] == "Project" || $pinned_result['pintype'] == "Lead") { ?>
                                            <div class="mbot5 mleft30"><strong>--to--</strong></div>
                                            <div class="mbot5">
                                                <i class="fa fa-calendar-o"></i>
                                                <?php echo isset($pinned_result['eventenddatetime']) ? $eventEndweekday . ", " . $fullEnddate . " at " . $fullEndtime : "N/A"; ?>
                                            </div>
                                        <?php } ?>
                                        <?php if (!empty($pstatus)){ ?>
                                        <div class="mbot5">
                                                    <span class="tooltip_status"
                                                          style="background-color: <?php echo isset($pstatus_color) ? $pstatus_color : "" ?>">
                                                        <?php echo $pstatus; ?>
                                                    </span>
                                        </div>
                                        <?php } ?>
                                        <div class="mbot5">
                                                    <span class="no-img staff-profile-image-small">
                                                      <?php echo staff_profile_image($pinned_result['staffid'], array('mg-responsive img-thumbnail staff-profile-image-small')); ?>
                                                    </span>
                                            <?php echo $ass_fname . " " . $ass_lname; ?></div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-sm-2 col-xs-3">
                            <a href="javascript:;" class="123">
                                <?php if ($pinned_result['pintype'] == "Project") { ?>
                                    <span class="no-img staff-profile-image-small">
                                                <?php echo staff_profile_image($pinned_result['staffid'], array('img-responsive img-thumbnail staff-profile-image-small')); ?>
                                              </span>
                                <?php } elseif ($pinned_result['pintype'] == "Lead") { ?>
                                    <span class="no-img staff-profile-image-small"
                                          style="background-color:<?php echo $pinned_result['lead_status_color']; ?>">
                                                <?php echo staff_profile_image($pinned_result['staffid'], array('img-responsive img-thumbnail staff-profile-image-small')); ?>
                                              </span>
                                <?php } elseif ($pinned_result['pintype'] == "Task") { ?>
                                    <span class="no-img staff-profile-image-small"
                                          style="background-color:<?php echo $pinned_result['task_status_color']; ?>">
                                                <?php echo staff_profile_image($pinned_result['staffid'], array('img-responsive img-thumbnail staff-profile-image-small')); ?>
                                              </span>
                                <?php } elseif ($pinned_result['pintype'] == "Message") { ?>
                                    <span class="no-img staff-profile-image-small">
                                                  <?php echo staff_profile_image($pinned_result['staffid'], array('img-responsive img-thumbnail staff-profile-image-small')); ?>
                                                </span>
                                <?php } ?>
                            </a>
                        </div>
                    </div>


                    <?php }
                    } else { ?>
                    <div class="mbot15">
                        No pinned items found!
                    </div>
                    <?php } ?>
                    <?php if ($pinned_item_all > $items) { ?>
                    <div class="pinned_item_button_section">
                        <a href="javascript:void(0)" id="all_pinned_loadMore"
                           class="loadMore btn btn-info" data-pid="#pinned_item_all"
                           data-item="<?php echo $items; ?>" data-widget="pinned_item">(<span
                                    class=" all_pin_data_only_count"><?php echo count($pinned_result_set) ?></span>)
                            Pinned</a>
                        <a href="javascript:void(0)" id="all_pinned_venues_only_loadless"
                           data-pid="#pinned_item_all" data-item="<?php echo $items; ?>" data-widget="pinned_item"
                           class="loadless btn btn-info "><i class="fa far fa-eye mright5"></i>Show Less</a>
                    </div>
                    <?php } ?>

                </div>


                <div class="tab-pane" id="pinned_item_projects" data-item="<?php echo $items; ?>">
                    <?php if (!empty($project_pinned_data)) { ?>
                        <?php
                        foreach ($project_pinned_data as $key => $single_project_pin) {
                            $pin_eventmonth = date("M", strtotime($single_project_pin['sorting_date']));
                            $pin_eventday = date("j", strtotime($single_project_pin['sorting_date']));
                            $pin_eventweekday = strtoupper(date("D", strtotime($single_project_pin['sorting_date'])));
                            $pin_eventyear = date("Y", strtotime($single_project_pin['sorting_date']));
                            $full_date = _d($single_project_pin['sorting_date']);
                            $full_time = _time($single_project_pin['sorting_date']);
                            ?>
                            <div class="row lazy_content pinned_item pinned_item_project_list_content"
                                 id="pinned_item_project_<?php echo $key; ?>">
                                <div class="col-sm-1 col-xs-1">
                                    <i class="fa fa-fw fa-thumb-tack pinned list_pin_icon project-pin"
                                       title="Unpin from dashboard"
                                       id="<?php echo $single_project_pin['pro_id']; ?>"
                                       project_id="<?php echo $single_project_pin['pro_id']; ?>"></i>
                                </div>
                                <div class="col-sm-8 col-xs-6">
                                    <div class="task-det">
                                        <div>
                                            <a href="<?php echo admin_url('projects/dashboard/' . $single_project_pin['pro_id'] . '?pg=home'); ?>"><?php echo $single_project_pin['pro_name'] ?></a>
                                        </div>
                                        <div class="project_list_time"><?php echo $pin_eventweekday; ?>
                                            , <?php echo $full_date; ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-1 col-xs-2">
                                    <div>
                                        <a href="javascript:;"
                                           title="<?php echo "<i class='fa fa-book'></i> " . $single_project_pin['pintype']; ?>"
                                           rel="popover"
                                           data-popover-content="#myPopover_<?php echo $key; ?>">
                                            <i class="fa fa-handshake-o menu-icon list_info_icon"></i>
                                        </a>
                                        <div id="myPopover_<?php echo $key; ?>"
                                             class="pinePopUp hide">
                                            <div class="col-sm-2 popupover_date">
                                                <div class="carddate-block">
                                                    <div class="card_date"
                                                         title="<?php echo date('Y', strtotime($full_date)) ?>">
                                                        <div class="card_month">
                                                            <small><?php echo date('M', strtotime($full_date)) ?></small>
                                                        </div>
                                                        <div class="card_d">
                                                            <strong><?php echo date('d', strtotime($full_date)) ?></strong>
                                                        </div>
                                                        <div class="card_day">
                                                            <small><?php echo date('D', strtotime($full_date)) ?></small>
                                                        </div>
                                                    </div>
                                                    <div class="card_year">
                                                        <small><?php echo date('Y', strtotime($full_date)) ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-10 popupover_content">
                                                <h4 class="mtop0 mbot5">
                                                    <strong><?php echo $single_project_pin['pro_name'] ?></strong>
                                                </h4>
                                                <!--<div class="mbot5"><?php /*echo $single_project_pin['pro_event_type']; */ ?></div>-->
                                                <div class="mbot5">
                                                    <i class="fa fa-map-marker"></i>
                                                    <?php
                                                    echo isset($single_project_pin['venuelocation']) ? $single_project_pin['venuelocation'] : "  N/A"; ?>
                                                </div>
                                                <div class="mbot5"><i
                                                            class="fa fa-clock-o"></i> <?php echo $full_time; ?></div>
                                                <i class="fa fa-calendar-o"></i> <?php echo $pin_eventweekday . ", " . $full_date; ?>
                                                <div class="mbot5">
                                                    <span class="tooltip_status">
                                                        <?php echo $single_project_pin['pro_status']; ?>
                                                    </span>
                                                </div>
                                                <div class="mbot5"><span class="no-img staff-profile-image-small">
                                                      <?php echo staff_profile_image($single_project_pin['staffid'], array('mg-responsive img-thumbnail staff-profile-image-small')); ?>
                                                    </span>
                                                    <?php echo $single_project_pin['pro_ass_fname'] . " " . $single_project_pin['pro_ass_lname']; ?>
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
                                <div class="col-sm-2 col-xs-3">
                                    <a href="javascript:;"
                                       class="btn btn-info btn-icon"><?php echo $single_project_pin['pintype'] ?></a>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <div class="mbot15">
                            No pinned project items found!
                        </div>
                    <?php } ?>
                    <?php if (count($project_pinned_data) > $items) { ?>
                        <div class="pinned_item_button_section">
                            <a href="javascript:void(0)" id="all_project_pinned_loadMore"
                               class="loadMore btn btn-info" data-pid="#pinned_item_projects"
                               data-item="<?php echo $items; ?>" data-widget="pinned_item">(<span
                                        class="pin_proj_count"><?php echo count($project_pinned_data); ?></span>)
                                Pinned</a>
                            <a href="javascript:void(0)" id="all_pinned_venues_only_loadless"
                               data-pid="#pinned_item_projects" data-item="<?php echo $items; ?>"
                               data-widget="pinned_item" class="loadless btn btn-info"><i
                                        class="fa far fa-eye mright5"></i>Show Less</a>
                        </div>
                    <?php } ?>
                </div>

                <div class="tab-pane" id="pinned_item_leads" data-item="<?php echo $items; ?>">
                    <?php if (!empty($lead_pinned_data)) { ?>
                        <?php
                        foreach ($lead_pinned_data as $lead_key => $single_lead_pin) {
                            $pin_eventmonth = date("M", strtotime($single_lead_pin['sorting_date']));
                            $pin_eventday = date("j", strtotime($single_lead_pin['sorting_date']));
                            $pin_eventweekday = strtoupper(date("D", strtotime($single_lead_pin['sorting_date'])));
                            $pin_eventyear = date("Y", strtotime($single_lead_pin['sorting_date']));
                            $full_date = _d($single_lead_pin['sorting_date']);
                            $full_time = _time($single_lead_pin['sorting_date']);
                            ?>
                            <div class="row lazy_content pinned_item pinned_item_lead_list_content"
                                 id="pinned_item_lead_list_<?php echo $lead_key; ?>">
                                <div class="col-sm-1 col-xs-1">
                                    <i class="fa fa-fw fa-thumb-tack pinned list_pin_icon lead-pin"
                                       title="Unpin from dashboard"
                                       id="<?php echo $single_lead_pin['lead_id']; ?>"
                                       lead_id="<?php echo $single_lead_pin['lead_id']; ?>"></i>
                                </div>
                                <div class="col-sm-8 col-xs-6">
                                    <div class="task-det">
                                        <div>
                                            <a href="<?php echo admin_url('leads/dashboard/' . $single_lead_pin['lead_id'] . '?pg=home'); ?>"><?php echo $single_lead_pin['lead_name'] ?></a>
                                        </div>
                                        <div class="project_list_time"><?php echo $pin_eventweekday; ?>
                                            , <?php echo $full_date; ?>
                                            at <?php echo $full_time; ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-1 col-xs-2">
                                    <div>
                                        <a href="javascript:;"
                                           title="<?php echo "<i class='fa fa-tty'></i> " . $single_lead_pin['pintype']; ?>"
                                           rel="popover"
                                           data-popover-content="#lead_myPopover_<?php echo $lead_key; ?>">
                                            <i class="fa fa-tty menu-icon list_info_icon"></i>
                                        </a>
                                        <div id="lead_myPopover_<?php echo $lead_key; ?>"
                                             class="pinePopUp hide">
                                            <div class="col-sm-2 popupover_date">
                                                <div class="carddate-block">
                                                    <div class="card_date"
                                                         title="<?php echo date('Y', strtotime($full_date)) ?>">
                                                        <div class="card_month">
                                                            <small><?php echo date('M', strtotime($full_date)) ?></small>
                                                        </div>
                                                        <div class="card_d">
                                                            <strong><?php echo date('d', strtotime($full_date)) ?></strong>
                                                        </div>
                                                        <div class="card_day">
                                                            <small><?php echo date('D', strtotime($full_date)) ?></small>
                                                        </div>
                                                    </div>
                                                    <div class="card_year">
                                                        <small><?php echo date('Y', strtotime($full_date)) ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-10 popupover_content">
                                                <h4 class="mtop0 mbot5">
                                                    <strong><?php echo $single_lead_pin['lead_name'] ?></strong>
                                                </h4>
                                                <!--<div class="mbot5"><?php /*echo $single_project_pin['pro_event_type']; */ ?></div>-->
                                                <div class="mbot5">
                                                    <i class="fa fa-map-marker"></i>
                                                    <?php echo isset($single_lead_pin['venuelocation']) ? $single_lead_pin['venuelocation'] : "  N/A"; ?>
                                                </div>
                                                <div class="mbot5"><i
                                                            class="fa fa-clock-o"></i> <?php echo $full_time; ?></div>
                                                <i class="fa fa-calendar-o"></i> <?php echo $pin_eventweekday . ", " . $full_date; ?>
                                                <div class="mbot5"><span class="tooltip_status">
                                                        <?php echo $single_lead_pin['lead_status']; ?>
                                                    </span></div>
                                                <div class="mbot5"><span class="no-img staff-profile-image-small">
                                                      <?php echo staff_profile_image($single_lead_pin['staffid'], array('mg-responsive img-thumbnail staff-profile-image-small')); ?>
                                                    </span>
                                                    <?php echo $single_lead_pin['lead_ass_fname'] . " " . $single_lead_pin['lead_ass_lname']; ?>
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
                                <div class="col-sm-2 col-xs-3">
                                    <a href="javascript:;"
                                       class="btn btn-info btn-icon"><?php echo $single_lead_pin['pintype'] ?></a>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <div class="mbot15">No pinned lead items found!</div>
                    <?php } ?>
                    <?php if (count($lead_pinned_data) > $items) { ?>
                        <div class="pinned_item_button_section">
                            <a href="javascript:void(0)" id="all_lead_pinned_loadMore"
                               class="loadMore btn btn-info mright10" data-pid="#pinned_item_leads"
                               data-item="<?php echo $items; ?>" data-widget="pinned_item">(<span
                                        class="pin_lead_count"><?php echo count($lead_pinned_data) ?></span>)
                                Pinned</a>
                            <a href="javascript:void(0)" id="all_pinned_venues_only_loadless"
                               data-pid="#pinned_item_leads" data-item="<?php echo $items; ?>" data-widget="pinned_item"
                               class="loadless btn btn-info"><i class="fa far fa-eye mright5"></i>Show Less</a>
                        </div>
                    <?php } ?>
                </div>

                <div class="tab-pane" id="pinned_item_tasks" data-item="<?php echo $items; ?>">
                    <?php if (!empty($task_pinned_data)) { ?>
                        <?php
                        foreach ($task_pinned_data as $task_key => $single_task_pin) {
                            $pin_eventmonth = date("M", strtotime($single_task_pin['sorting_date']));
                            $pin_eventday = date("j", strtotime($single_task_pin['sorting_date']));
                            $pin_eventweekday = strtoupper(date("D", strtotime($single_task_pin['sorting_date'])));
                            $pin_eventyear = date("Y", strtotime($single_task_pin['sorting_date']));
                            $full_date = _d($single_task_pin['sorting_date']);
                            $full_time = _time($single_task_pin['sorting_date']);
                            ?>
                            <div class="row lazy_content pinned_item pinned_item_task_list_content"
                                 id="tasks_only_<?php echo $task_key; ?>">
                                <div class="col-sm-1 col-xs-1">
                                    <i class="fa fa-fw fa-thumb-tack task-pin pinned list_pin_icon task-pin"
                                       title="Unpin from dashboard"
                                       id="<?php echo $single_task_pin['task_id']; ?>"
                                       task_id="<?php echo $single_task_pin['task_id']; ?>"></i>
                                </div>
                                <div class="col-sm-8 col-xs-6">
                                    <div class="task-det">
                                        <div>
                                            <a href="<?php echo admin_url('tasks/dashboard/' . $single_task_pin['task_id'] . '?pg=home'); ?>"><?php echo $single_task_pin['task_name'] ?></a>
                                        </div>
                                        <div class="project_list_time"><?php echo $pin_eventweekday; ?>
                                            , <?php echo $full_date; ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-1 col-xs-2">
                                    <div>
                                        <a href="javascript:;"
                                           title="<?php echo "<i class='fa fa-tasks'></i> " . $single_task_pin['pintype']; ?>"
                                           rel="popover"
                                           data-popover-content="#task_myPopover_<?php echo $task_key; ?>">
                                            <i class="fa fa-tasks menu-icon list_info_icon"></i>
                                        </a>
                                        <div id="task_myPopover_<?php echo $task_key; ?>"
                                             class="pinePopUp hide">
                                            <div class="col-sm-2 popupover_date">
                                                <div class="carddate-block">
                                                    <div class="card_date"
                                                         title="<?php echo date('Y', strtotime($full_date)) ?>">
                                                        <div class="card_month">
                                                            <small><?php echo date('M', strtotime($full_date)) ?></small>
                                                        </div>
                                                        <div class="card_d">
                                                            <strong><?php echo date('d', strtotime($full_date)) ?></strong>
                                                        </div>
                                                        <div class="card_day">
                                                            <small><?php echo date('D', strtotime($full_date)) ?></small>
                                                        </div>
                                                    </div>
                                                    <div class="card_year">
                                                        <small><?php echo date('Y', strtotime($full_date)) ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-10 popupover_content">
                                                <h4 class="mtop0 mbot5">
                                                    <strong><?php echo $single_task_pin['task_name'] ?></strong>
                                                </h4>
                                                <!--<div class="mbot5"><?php /*echo $single_project_pin['pro_event_type']; */ ?></div>-->
                                                <!--<div class="mbot5"><i class="fa fa-map-marker"> </i><?php /*echo $single_task_pin['venuelocation']; */ ?></div>-->
                                                <div class="mbot5"><i
                                                            class="fa fa-clock-o"></i> <?php echo $full_time; ?></div>
                                                <i class="fa fa-calendar-o"></i> <?php echo $pin_eventweekday . ", " . $full_date; ?>
                                                <div class="mbot5"><span class="tooltip_status">
                                                        <?php echo $single_task_pin['task_status']; ?>
                                                    </span></div>
                                                <div class="mbot5"><span class="no-img staff-profile-image-small">
                                                      <?php echo staff_profile_image($single_task_pin['staffid'], array('mg-responsive img-thumbnail staff-profile-image-small')); ?>
                                                    </span>
                                                    <?php echo $single_task_pin['task_ass_fname'] . " " . $single_task_pin['task_ass_lname']; ?>
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
                                <div class="col-sm-2 col-xs-3">
                                    <a href="javascript:;"
                                       class="btn btn-info btn-icon"><?php echo $single_task_pin['pintype'] ?></a>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <div class="mbot15">
                            No pinned task items found!
                        </div>
                    <?php } ?>
                    <?php if (count($task_pinned_data) > $items) { ?>
                        <div class="pinned_item_button_section">
                            <a href="vascript:void(0)" id="all_task_pinned_loadMore"
                               class="loadMore btn btn-info " data-pid="#pinned_item_tasks"
                               data-item="<?php echo $items; ?>" data-widget="pinned_item">(<span
                                        class="pin_task_count"><?php echo count($task_pinned_data) ?></span>)
                                Pinned</a>
                            <a href="javascript:void(0)" id="all_pinned_venues_only_loadless"
                               data-pid="#pinned_item_tasks" data-item="<?php echo $items; ?>" data-widget="pinned_item"
                               class="loadless btn btn-info"><i class="fa far fa-eye mright5"></i>Show Less</a>
                        </div>
                    <?php } ?>
                </div>

                <div class="tab-pane" id="pinned_item_messages" data-item="<?php echo $items; ?>">
                    <?php if (!empty($message_pinned_data)) { ?>
                        <?php
                        foreach ($message_pinned_data as $msg_key => $single_msg_pin) {
                            $pin_eventmonth = date("M", strtotime($single_msg_pin['sorting_date']));
                            $pin_eventday = date("j", strtotime($single_msg_pin['sorting_date']));
                            $pin_eventweekday = strtoupper(date("D", strtotime($single_msg_pin['sorting_date'])));
                            $pin_eventyear = date("Y", strtotime($single_msg_pin['sorting_date']));
                            $full_date = _d($single_msg_pin['sorting_date']);
                            $full_time = _time($single_msg_pin['sorting_date']);
                            ?>
                            <div class="row lazy_content pinned_item pinned_item_msg_list_content"
                                 id="pinned_item_msg_<?php echo $msg_key; ?>">
                                <div class="col-sm-1 col-xs-1">
                                    <i class="fa fa-fw fa-thumb-tack pinned list_pin_icon message-pin"
                                       title="Unpin from dashboard"
                                       id="<?php echo $single_msg_pin['msg_id'] ?>"
                                       message_id="<?php echo $single_msg_pin['msg_id'] ?>"></i>
                                </div>
                                <div class="col-sm-8 col-xs-6">
                                    <div class="task-det">
                                        <div>
                                            <a href="<?php echo admin_url('messages?pg=home'); ?>"><?php echo $single_msg_pin['subject'] ?></a>
                                        </div>
                                        <div class="project_list_time"><?php echo $pin_eventweekday . ", " . $full_date . " at " . $full_time; ?></div>
                                    </div>
                                </div>
                                <div class="col-sm-1 col-xs-2">
                                    <div>
                                        <a href="javascript:;"
                                           title="<?php echo "<i class='fa fa-envelope-o'></i> " . $single_msg_pin['pintype']; ?>"
                                           rel="popover"
                                           data-popover-content="#msg_myPopover_<?php echo $msg_key; ?>">
                                            <i class="fa fa-envelope-o menu-icon list_info_icon"></i>
                                        </a>
                                        <div id="msg_myPopover_<?php echo $msg_key; ?>"
                                             class="pinePopUp hide">
                                            <div class="col-sm-2 popupover_date">
                                                <div class="carddate-block">
                                                    <div class="card_date"
                                                         title="<?php echo date('Y', strtotime($full_date)) ?>">
                                                        <div class="card_month">
                                                            <small><?php echo date('M', strtotime($full_date)) ?></small>
                                                        </div>
                                                        <div class="card_d">
                                                            <strong><?php echo date('d', strtotime($full_date)) ?></strong>
                                                        </div>
                                                        <div class="card_day">
                                                            <small><?php echo date('D', strtotime($full_date)) ?></small>
                                                        </div>
                                                    </div>
                                                    <div class="card_year">
                                                        <small><?php echo date('Y', strtotime($full_date)) ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-10 popupover_content">
                                                <h4 class="mtop0 mbot5">
                                                    <strong><?php echo $single_msg_pin['subject'] ?></strong>
                                                </h4>
                                                <div class="mbot5"><i
                                                            class="fa fa-clock-o"></i> <?php echo $full_time; ?></div>
                                                <i class="fa fa-calendar-o"></i> <?php echo $pin_eventweekday . ", " . $full_date; ?>
                                                <!--<div class="mbot5"><span class="tooltip_status">
                                                        <?php /*echo $single_msg_pin['msg_status']; */ ?>
                                                    </span></div>-->
                                                <div class="mbot5"><span class="no-img staff-profile-image-small">
                                                      <?php echo staff_profile_image($single_msg_pin['staffid'], array('mg-responsive img-thumbnail staff-profile-image-small')); ?>
                                                    </span>
                                                    <?php echo $single_msg_pin['firstname'] . " " . $single_msg_pin['lastname']; ?>
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
                                <div class="col-sm-2 col-xs-3">
                                    <a href="javascript:;"
                                       class="btn btn-info btn-icon"><?php echo $single_msg_pin['pintype'] ?></a>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <div class="mbot15">
                            No pinned messages found!
                        </div>
                    <?php } ?>
                    <?php if (count($message_pinned_data) > $items) { ?>
                        <div class="pinned_item_button_section">
                            <a href="avascript:;" id="all_msg_pinned_loadMore"
                               class="loadMore btn btn-info" data-pid="#pinned_item_messages"
                               data-item="<?php echo $items; ?>" data-widget="pinned_item">(<span
                                        class="pin_msg_count"><?php echo count($message_pinned_data) ?></span>)
                                Pinned</a>
                            <a href="javascript:;" id="all_pinned_venues_only_loadless" data-pid="#pinned_item_messages"
                               data-item="<?php echo $items; ?>" data-widget="pinned_item"
                               class="loadless btn btn-info"><i class="fa far fa-eye mright5"></i>Show Less</a>
                        </div>
                    <?php } ?>
                </div>
            </div>


        </div>
    </div>
</div>

<div class="modal fade" id="pinned_items_setting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span>Pinned Items Setting</span>
                </h4>
            </div>
            <div class="modal-body">
                <?php if (has_permission('items', '', 'create')) { ?>
                    <div class="row">
                        <form action="<?php echo admin_url() ?>home/dashboard_widget_setting" novalidate="1"
                              id="pinned_items_setting_form" method="post"
                              accept-charset="utf-8">
                            <div id="additional"></div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="checkbox radio-primary radio-inline">
                                        <input type="checkbox" id="dashboard_pinned_items" name="widget_visibility"
                                               class="checkbox task" value="1">
                                        <label for="dashboard_pinned_items">Hide</label>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Number of items to display </label>
                                        <input type="number" name="items" class="form-control" min="5"
                                               value="<?php echo $items; ?>">
                                    </div>
                                </div>
                            </div>
                    </div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="widget" value="pinned_item">
                <input type="hidden" name="user" value="<?php echo get_staff_user_id(); ?>">
                <button type="submit" class="btn btn-info" id="save_setting"><?php echo _l('Save'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

    /*$(function () {*/
    //var items = <?php //echo $items; ?>;
    /*var selector = "#all_pinned_loadMore .all_pin_data_only_count";
    update_count(items,selector);

    $(".pinned_item_master_list_content").slice(0, items).show();
    $("#all_pinned_loadMore").on('click', function (e) {
        e.preventDefault();
        $(".pinned_item_master_list_content:hidden").slice(0, items).slideDown();
        if ($(".pinned_item_master_list_content:hidden").length == 0) {
            $("#load").fadeOut('slow');
        }
        selector = "#all_pinned_loadMore .all_pin_data_only_count";
        update_count(items,selector);
    });

    selector = "#all_project_pinned_loadMore .pin_proj_count";
    update_count(items,selector);
    $(".pinned_item_project_list_content").slice(0, items).show();
    $("#all_project_pinned_loadMore").on('click', function (e) {
        e.preventDefault();
        $(".pinned_item_project_list_content:hidden").slice(0, items).slideDown();
        if ($(".pinned_item_project_list_content:hidden").length == 0) {
            $("#load").fadeOut('slow');
        }
        selector = "#all_project_pinned_loadMore .pin_proj_count";
        update_count(items,selector);
    });
    selector = "#all_lead_pinned_loadMore .pin_lead_count";
    update_count(items,selector);
    $(".pinned_item_lead_list_content").slice(0, items).show();
    $("#all_lead_pinned_loadMore").on('click', function (e) {
        e.preventDefault();
        $(".pinned_item_lead_list_content:hidden").slice(0, items).slideDown();
        if ($(".pinned_item_lead_list_content:hidden").length == 0) {
            $("#load").fadeOut('slow');
        }
        selector = "#all_lead_pinned_loadMore .pin_lead_count";
        update_count(items,selector);
    });

    selector = "#all_task_pinned_loadMore .pin_task_count";
    update_count(items,selector);
    $(".pinned_item_task_list_content").slice(0, items).show();
    $("#all_task_pinned_loadMore").on('click', function (e) {
        e.preventDefault();
        $(".pinned_item_task_list_content:hidden").slice(0, items).slideDown();
        if ($(".pinned_item_task_list_content:hidden").length == 0) {
            $("#load").fadeOut('slow');
        }
        selector = "#all_task_pinned_loadMore .pin_task_count";
        update_count(items,selector);
    });

    selector = "#all_msg_pinned_loadMore .pin_msg_count";
    update_count(items,selector);
    $(".pinned_item_msg_list_content").slice(0, items).show();
    $("#all_msg_pinned_loadMore").on('click', function (e) {
        e.preventDefault();
        $(".pinned_item_msg_list_content:hidden").slice(0, items).slideDown();
        if ($(".pinned_item_msg_list_content:hidden").length == 0) {
            $("#load").fadeOut('slow');
        }
        selector = "#all_msg_pinned_loadMore .pin_msg_count";
        update_count(items,selector);
    });

});*/

    /*function update_count(items,selector) {
        var current_count = $(selector).text();
        var newcount =current_count-items;
        if(newcount<0){
            newcount=0;
        }
        $(selector).text(newcount);
    }*/
</script>