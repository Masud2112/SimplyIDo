<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 11-04-2018
 * Time: 04:29 PM
 */

$items = 5;
$interval =3;
$widget_setting = json_decode($widget_data->widget_settings, true);
if (isset($widget_setting['messages'])) {
    $widget_setting = $widget_setting['messages'];
    $items = isset($widget_setting['items']) ? $widget_setting['items'] : 5;
    $interval = isset($widget_setting['time_frame']) ? $widget_setting['time_frame'] : 3;
}
$message_data= $this->home_model->get_all_message_data($interval);
$unread_message_data= $this->home_model->get_all_unread_message_data($interval);
$lead_unread_message_data= $this->home_model->get_all_lead_unread_message_data($interval);
/*echo "<pre>";
print_r($unread_message_data);
die('<--here');*/
?>
<div class="">
    <div class="panel-body" id="unique_messages_widget">
        <div class="row">
            <div class="col-md-12 mbot10 posrel">
                <div class="handle"><img src="<?php echo site_url()?>assets\images\dragger.png" alt="dragger"></div>
                <h4 class="no-margin pull-left">New messages</h4>
                <a href="#" data-toggle="modal" data-target="#message_setting"
                   class="toggle_set_button"><i class="fa fa-cog menu-icon"></i></a>
                <a href="javascript:void(0)" class="toggle_control_cutton" id="messages_collapse" data-pid="#unique_messages_widget">
                    <i class="fa fa-caret-up"></i></a>
                <a class="duplicate_action_button" href="<?php echo admin_url('messages/message'); ?>" class=""><i
                        class="fa fa-plus menu-icon"></i></a>

            </div>
        </div>
        
        <div class="panel_s widget-body clearfix" id="messages_data" data-item="<?php echo $items; ?>">
            <div class="new_message_data_container ">
                <?php
                if (!empty($unread_message_data)) {
                    foreach ($unread_message_data as $umsg_key => $unread_messages) {
                        if(isset($unread_messages['rel_type']) && isset($unread_messages['rel_id']) && $unread_messages['rel_id'] > 0 ){
                            $CI =& get_instance();
                            $CI->load->model('leads_model');
                            $CI->load->model('projects_model');

                            if($unread_messages['rel_type']=='lead'){
                                $event =  (array)$CI->leads_model->get($unread_messages['rel_id']);
                            }else{
                                $event =  (array)$CI->projects_model->get($unread_messages['rel_id']);
                            }
                        }
                        if(isset($unread_messages['child_message']) && !empty($unread_messages['child_message'])){
                            $unread_messages['created_by_name'] = $unread_messages['child_message'][count($unread_messages['child_message'])-1]['created_by_name'];
                        }
                        /*echo "<pre>";
                        print_r($unread_messages['child_message']);
                        die();*/
                        if(isset($event) && !empty($event)){
                            $umsg_eventmonth = date("M", strtotime($event['eventstartdatetime']));
                            $umsg_eventday = date("j", strtotime($event['eventstartdatetime']));
                            $umsg_eventweekday = strtoupper(date("D", strtotime($event['eventstartdatetime'])));
                            $umsg_eventyear = date("Y", strtotime($event['eventstartdatetime']));
                            $umsg_full_date = _d($event['eventstartdatetime']);
                            $umsg_full_time = _time($event['eventstartdatetime']);
                        }
                        ?>
                        <div class="row mbot10 lazy_content messages all_msg_master_list_content">
                            <div class="col-md-2">
                                <span class="no-img staff-profile-image-small mright10"><?php echo staff_profile_image($unread_messages['created_by'], array('staff-profile-image-small')); ?></span>
                            </div>
                            <div class="col-md-8">
                                <a href="<?php echo admin_url("messages/view/") . $unread_messages['id']; ?>"><b><?php echo $unread_messages['subject'] ?></b></a>
                                <div>
                                    from <?php echo $unread_messages['created_by_name']; ?></div>
                            </div>
                            <div class="col-md-2">
                                <a href="javascript:;"
                                   title="<?php //echo $unread_messages['umsg_lead_type']; ?>"
                                   rel="popover"
                                   data-popover-content="#umsg_myPopover_<?php echo $umsg_key; ?>">
                                    <i class="fa fa-handshake-o menu-icon list_pin_icon"></i>
                                </a>
                                <div id="umsg_myPopover_<?php echo $umsg_key; ?>"
                                     class="pinePopUp hide">
                                    <ul>
                                        <li><b>Title: </b>
                                            <?php echo(isset($unread_messages['subject']) ? $unread_messages['subject'] : 'N/A'); ?>
                                        </li>
                                        <li><b>Type: </b>
                                            <?php echo(isset($event['name']) ? $event['name'] : 'N/A'); ?>
                                        </li>
                                        <li><b>Location: </b>
                                            <?php echo(isset($event['venue']) ? $event['venue'] : 'N/A'); ?>
                                        </li>
                                        <li>
                                            <b>Date:</b> <?php echo isset($umsg_eventweekday)?$umsg_eventweekday . ", " . $umsg_full_date:""; ?>
                                        </li>
                                        <li>
                                            <b>Time:</b> <?php echo isset($umsg_full_time)?$umsg_full_time:""; ?>
                                        </li>
                                        <li><b>Status:</b> <span
                                                class="tooltip_status"
                                                style="font-weight:500;color:;background: ">
                                                <?php echo isset($event['status_name'])?$event['status_name']:""; ?></span>
                                        </li>
                                        <li>
                                            <b>Assigned:</b> <?php echo $unread_messages['created_by_name']; ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
                <?php
                /*if (!empty($lead_unread_message_data)) {
                    foreach ($lead_unread_message_data as $lmsg_key => $lead_unread_messages) {
                        $umsg_eventmonth = date("M", strtotime($lead_unread_messages['eventstartdatetime']));
                        $umsg_eventday = date("j", strtotime($lead_unread_messages['eventstartdatetime']));
                        $umsg_eventweekday = date("D", strtotime($lead_unread_messages['eventstartdatetime']));
                        $umsg_eventyear = date("Y", strtotime($lead_unread_messages['eventstartdatetime']));
                        $umsg_full_date = date("m/d/Y", strtotime($lead_unread_messages['eventstartdatetime']));
                        $lead_umsg_full_time = date("h:i", strtotime($lead_unread_messages['eventstartdatetime']));
                        */?><!--
                        <div class="row mbot10 lazy_content messages all_msg_master_list_content">
                            <div class="col-md-2">
                                <span class="no-img staff-profile-image-small mright10"><?php /*echo staff_profile_image($lead_unread_messages['created_by'], array('staff-profile-image-small')); */?></span>
                            </div>
                            <div class="col-md-8">
                                <a href="<?php /*echo admin_url("messages/view/?pg=home") . $lead_unread_messages['id']; */?>"><b><?php /*echo $lead_unread_messages['subject'] */?></b></a>
                                <div>
                                    from <?php /*echo $lead_unread_messages['firstname'] . " " . $lead_unread_messages['lastname']; */?></div>
                            </div>
                            <div class="col-md-2">
                                <a href="javascript:;"
                                   title="<?php /*echo $lead_unread_messages['umsg_lead_type']; */?>"
                                   rel="popover"
                                   data-popover-content="#lmsg_myPopover_<?php /*echo $lmsg_key; */?>">
                                    <i class="fa fa-tty menu-icon list_pin_icon"></i>
                                </a>
                                <div id="lmsg_myPopover_<?php /*echo $lmsg_key; */?>"
                                     class="pinePopUp hide">
                                    <ul>
                                        <li><b>Title:</b>
                                            <?php /*echo(isset($lead_unread_messages['name']) ? $lead_unread_messages['name'] : 'N/A'); */?>
                                        </li>
                                        <li><b>Type:</b>
                                            <?php /*echo(isset($lead_unread_messages['eventtypename']) ? $lead_unread_messages['eventtypename'] : 'N/A'); */?>
                                        </li>
                                        <li><b>Location:</b>
                                            <?php /*echo(isset($lead_unread_messages['venue']) ? $lead_unread_messages['venue'] : 'N/A'); */?>
                                        </li>
                                        <li>
                                            <b>Date:</b> <?php /*echo $umsg_eventweekday . ", " . $umsg_full_date; */?>
                                        </li>
                                        <li>
                                            <b>Time:</b> <?php /*echo $umsg_full_time; */?>
                                        </li>
                                        <li><b>Status:</b> <span
                                                class="tooltip_status"
                                                style="font-weight:500;color: #ffffff;background: <?php /*echo $lead_unread_messages['umsg_pro_color']; */?>"><?php /*echo $lead_unread_messages['umsg_pro_status']; */?></span>
                                        </li>
                                        <li>
                                            <b>Assigned:</b> <?php /*echo $lead_unread_messages['ass_fname'] . " " . $lead_unread_messages['ass_lname']; */?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        --><?php
/*                    }
                }*/
                ?>

                <?php
                if (!empty($message_data)) {
                    foreach ($message_data as $lmsg_key => $messages) {
                        ?>
                        <div class="row mbot10 lazy_content messages all_msg_master_list_content">
                            <div class="col-md-2">
                                <span class="no-img staff-profile-image-small mright10"><?php echo staff_profile_image($messages['created_by'], array('staff-profile-image-small')); ?></span>
                            </div>
                            <div class="col-md-8">
                                <a href="<?php echo admin_url("messages/view/") . $messages['id']; ?>"><b><?php echo $messages['subject'] ?></b></a>
                                <div>
                                    from <?php echo $messages['firstname'] . " " . $messages['lastname']; ?></div>
                            </div>
                            <div class="col-md-2">
                                <a href="javascript:;"
                                   title="<?php echo(isset($lead_unread_messages['umsg_lead_type']) ? $lead_unread_messages['umsg_lead_type'] : 'N/A'); ?>"
                                   rel="popover"
                                   data-popover-content="#lmsg_myPopover_<?php echo $lmsg_key; ?>">
                                    <i class="fa fa-tty menu-icon list_pin_icon"></i>
                                </a>
                                <div id="lmsg_myPopover_<?php echo $lmsg_key; ?>"
                                     class="pinePopUp hide">
                                    <ul>
                                        <li><b>Title:</b>
                                            <?php echo(isset($lead_unread_messages['name']) ? $lead_unread_messages['name'] : 'N/A'); ?>
                                        </li>
                                        <li><b>Type: </b>
                                            <?php echo(isset($lead_unread_messages['eventtypename']) ? $lead_unread_messages['eventtypename'] : 'N/A'); ?>
                                        </li>
                                        <li><b>Location: </b>
                                            <?php echo(isset($lead_unread_messages['venue']) ? $lead_unread_messages['venue'] : 'N/A'); ?>
                                        </li>
                                        <li>
                                            <b>Date:</b> <?php echo (isset($umsg_eventweekday) ? $umsg_eventweekday : 'N/A') . ", " . (isset($umsg_full_date) ? $umsg_full_date : 'N/A'); ?>
                                        </li>
                                        <li>
                                            <b>Time:</b> <?php echo(isset($lead_umsg_full_time) ? $lead_umsg_full_time : 'N/A'); ?>
                                        </li>
                                        <li><b>Status:</b> <span
                                                class="tooltip_status"
                                                style="font-weight:500;background:
                                                <?php echo(isset($lead_unread_messages['umsg_pro_color']) ? $lead_unread_messages['umsg_pro_color'] : 'N/A'); ?>
                                                    "><?php

                                                //echo $lead_unread_messages['umsg_pro_status'];
                                                echo(isset($lead_unread_messages['umsg_pro_status']) ? $lead_unread_messages['umsg_pro_status'] : 'N/A');

                                                ?>

                                              </span></li>
                                        <li>
                                            <b>Assigned:</b> <?php echo (isset($lead_unread_messages['ass_fname']) ? $lead_unread_messages['ass_fname'] : 'N/A') . " " . (isset($lead_unread_messages['ass_lname']) ? $lead_unread_messages['ass_lname'] : ''); ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
                <?php if (empty($lead_unread_message_data) && empty($unread_message_data)) { ?>
                    <div class="mbot15">

                    </div>
                <?php } ?>


                <div class="message_button_section text-center">
                    <?php
                    $total_unread_msgs = count($unread_message_data) + count($lead_unread_message_data);
                    //echo $total_unread_msgs;
                    if (count($message_data) > 5) { ?>
                        <!-- <a href="#" id="all_unread_msg_loadMore" class="loadMore">Load More</a> -->
                        <a href="javascript:;" id="all_unread_msg_loadMore"
                           class="btn btn-info mright10 loadMore " data-widget="messages" data-pid="#messages_data" data-item="<?php echo $items; ?>"><i class="fa far fa-eye mright5"></i>(<?php echo $total_unread_msgs ?>) New</a>
                        <a href="javascript:;" id="all_unread_msg_loadless" data-widget="messages" data-pid="#messages_data" data-item="<?php echo $items; ?>" class="loadless btn btn-info mright10"><i class="fa far fa-eye mright5"></i>Show Less</a>
                    <?php } ?>
                    <!-- <a href="javascript:;" class="btn btn-info pull-left active display-block mright10"><i class="fa far fa-eye mright5"></i>(<?php //echo count($unread_message_data) ?>) New</a> -->
                    <a href="<?php echo admin_url('messages?pg=home'); ?>" class="btn btn-info"><i class="fa fa-envelope-o menu-icon mright5"></i>Messages</a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="message_setting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span>Message Setting</span>
                </h4>
            </div>
            <div class="modal-body">
                <?php if (has_permission('items', '', 'create')) { ?>
                    <div class="row">
                        <form action="<?php echo admin_url()?>home/dashboard_widget_setting" novalidate="1" id="message_setting_form" method="post"
                              accept-charset="utf-8">
                            <div id="additional"></div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="checkbox radio-primary radio-inline">
                                        <input type="checkbox" id="dashboard_messages" name="widget_visibility" class="checkbox task" value="1">
                                        <label for="dashboard_messages">Hide</label>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Number of messages to display </label>
                                        <input type="number" name="items" class="form-control" min="5" value="5">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="selectdays">
                                        <label>Designated time frame</label>
                                        <select name="time_frame" class="selectpicker selectdays">
                                            <option value="3" <?php echo $interval==3?"selected":"" ?>>In last 3 days</option>
                                            <option value="7" <?php echo $interval==7?"selected":"" ?>>In last 7 days</option>
                                            <option value="15" <?php echo $interval==15?"selected":"" ?>>In last 15 days</option>
                                            <option value="30" <?php echo $interval==30?"selected":"" ?>>In last 30 days</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                    </div>

                <?php } ?>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="widget" value="messages">
                <input type="hidden" name="user" value="<?php echo get_staff_user_id();?>">
                <button type="submit" class="btn btn-info" id="save_setting"><?php echo _l('Save'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>