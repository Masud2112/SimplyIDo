<?php
/**
 * Added By : Purvi
 * Dt : 10/26/2017
 * Lead Dashboard
 */
init_head();
?>
<div id="wrapper" class="leaddashboard">
    <div class="content">

        <div class="breadcrumb">
            <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <a href="<?php echo admin_url('leads'); ?>">Leads</a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <span><?php echo $lead->name; ?></span>
        </div>
        <h1 class="pageTitleH1"><i class="fa fa-tty"></i><?php echo "Lead"; ?></h1>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-sm-4">
                        <figure class="text-center thumb-lg">
                            <div class="profileImg_blk"><?php echo lead_profile_image($leadid, array('profile_image', 'img-responsive img-thumbnail', 'lead-profile-image-thumb'), 'round'); ?></div>
                        </figure>
                    </div>
                    <div class="col-sm-8">
                        <h4 class="text-uppercase"><?php echo isset($lead->name) ? $lead->name : "--"; ?>
                            <?php if (has_permission('leads', '', 'edit')) { ?>
                                <a data-toggle="tooltip" data-title="Edit Lead"
                                   href="<?php echo admin_url('leads/lead/' . $leadid); ?>"
                                   class="btn btn-icon"><i
                                            class="fa fa-pencil"></i></a>
                            <?php } ?>
                        </h4>
                        <div class="card-user-info-widget panel_s btmbrd">
                            <div class="row">
                                <div class="col-sm-12 card-user-info">

                                    <div class="hide">
                                        <?php if (isset($pg) && $pg != '') { ?>
                                            <a data-toggle="tooltip" data-title="Back"
                                               href="<?php echo admin_url('calendar'); ?>"
                                               class="btn btn-default pull-right"><i class="fa fa-chevron-left"></i></a>
                                        <?php } else { ?>
                                            <a data-toggle="tooltip" data-title="Back"
                                               href="<?php echo admin_url('leads'); ?>"
                                               class="btn btn-default pull-right"><i class="fa fa-chevron-left"></i></a>
                                        <?php } ?>
                                    </div>

                                    <?php
                                    if (($lead->eventstartdatetime != "")) {
                                        $eventstartdatetime = date('l, F d, Y', strtotime($lead->eventstartdatetime));
                                    } else {
                                        $eventstartdatetime = "--";
                                    }

                                    if (($lead->eventenddatetime != "")) {
                                        $eventenddatetime = date('l, F d, Y', strtotime($lead->eventenddatetime));
                                    } else {
                                        $eventenddatetime = "--";
                                    }

                                    if ($lead->pinid > 0) {
                                        $pintitle = 'Unpin from Home';
                                        $pinclass = 'pinned';
                                    } else {
                                        $pintitle = 'Pin to Home';
                                        $pinclass = "";
                                    }

                                    if (($lead->assigned_name != "")) {
                                        $assigned_name = $lead->assigned_name;
                                    } else {
                                        $assigned_name = "--";
                                    }

                                    if (($lead->eventtypename != "")) {
                                        $eventtypename = $lead->eventtypename;
                                    } else {
                                        $eventtypename = "--";
                                    }
                                    $leadAssignees = get_lead_assignee($lead->id);
                                    $assignedOutput = '';
                                    if (count($leadAssignees) > 0) {
                                        $count = 1;
                                        $assignee = 1;
                                        $moreAssigned = "<div class='moreassignee hide'>";
                                        foreach ($leadAssignees as $leadAssignee) {
                                            if (count($leadAssignees) > 2 && $count > 2) {
                                                $full_name = $leadAssignee->firstname . " " . $leadAssignee->lastname;
                                                $moreAssigned .= '<a data-toggle="tooltip" title="' . $full_name . '" href="javascript:void(0)">' . staff_profile_image($leadAssignee->staffid, array(
                                                        'staff-profile-image-small'
                                                    )) . '<span class="">' . $full_name . '</span></a>';
                                            }
                                            $count++;
                                        }
                                        $moreAssigned .= "</div>";
                                        foreach ($leadAssignees as $leadAssignee) {
                                            $full_name = $leadAssignee->firstname . " " . $leadAssignee->lastname;
                                            $assignedOutput .= '<a data-toggle="tooltip" title="' . $full_name . '" href="javascript:void(0)">' . staff_profile_image($leadAssignee->staffid, array(
                                                    'staff-profile-image-small'
                                                )) . '</a>';
                                            // For exporting
                                            $assignedOutput .= '<span class="hide">' . $full_name . '</span>';
                                            if ($assignee == 2 && count($leadAssignees) > 2) {
                                                $assignedOutput .= '<a href="javascript:void(0)" class="assigneemore">';
                                                $assignedOutput .= '<span class="no-img staff-profile-image-small" style="background-color:#ccc">+' . (count($leadAssignees) - 2) . '</span>';
                                                $assignedOutput .= '</a>';
                                                $assignedOutput .= $moreAssigned;
                                                break;
                                            }
                                            $assignee++;
                                        }
                                    }
                                    ?>
                                    <ul class="list-unstyled mb-0 text-muted email-details-list">
                                        <li class="col-12">
                                            <i class="list-icon fa fa-tty"></i><?php echo $eventtypename; ?>
                                        </li>
                                        <li class="col-12 mr-t-20">
                                            <i class="list-icon fa fa-calendar"></i><?php echo $eventstartdatetime; ?>
                                        </li>
                                        <li class="col-12">
                                            <i class="list-icon fa fa-clock-o"></i>
                                            <?php
                                            $eventTime = date("g:i A", strtotime($lead->eventstartdatetime));
                                            if (app_time_format() == 24) {
                                                $eventTime = date("G:i A", strtotime($lead->eventstartdatetime));
                                            }
                                            echo $eventTime;
                                            ?>
                                        </li>
                                        <?php if ($lead->venueid > 0) {
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
                                                $vaddress .= $venue->venuecity . ", ";
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
                                        <li class="col-12 lead-pin-unpin">
                                            <i class="list-icon fa fa-thumb-tack <?php echo $pinclass; ?>"></i>
                                            <span class="lead-pin <?php echo $pinclass; ?>"
                                                  lead_id="<?php echo $leadid; ?>">
                                                <?php echo $pintitle; ?>
                                            </span>
                                        </li>
                                        <li class="col-12">
                                            <i class="list-icon fa fa-user"></i><?php echo _l('assignedto').$assignedOutput; ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <button type="button" data-toggle="modal" data-title="Convert Lead to Project"
                                    href="<?php //echo admin_url('leads/convert_lead/' . $leadid); ?>"
                                    class="btn btn-info btn-icon " data-toggle="modal"
                                    data-target="#leadcontactlist" data-original-title="" title="">
                                <?php echo _l('convert_to_project');?>
                            </button>
                        </div>

                    </div>
                </div>

                <div class="row">


                    <div class="col-md-12">
                        <div class="statistic-squares text-center">
                            <span id="leadCountdown" class="countdown"></span>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if (!empty($lead->lastaction)) {
                            $lastaction = _dt($lead->lastaction);
                        } else {
                            $lastaction = _dt($lead->eventinquireon);//"None";
                        }

                        if (!empty($lead->nextaction)) {
                            $nextaction = _dt($lead->nextaction);
                        } else {
                            $nextaction = "Nothing Scheduled";
                        }
                        /*echo "<pre>";
                        print_r($lead);
                        die();*/
                        ?>
                        <div class="widget-bg meeting-action panel_s btmbrd">
                            <div class="row m-0">
                                <div class="col-xs-5">
                                    <div class="progress-stats-round text-center input-has-value">
                                        <span>LAST ACTION</span>
                                        <h4 class=" mr-tb-10"><i class="fa fa-calendar"></i></h4>
                                        <?php if (!empty($lead->lastaction)) { ?>
                                            <div class="date color-primary"><?php echo $lead->last_meeting_name; ?></div>
                                            <small><?php echo "(" . time_ago($lead->lastaction) . ")"; ?></small>
                                        <?php } else { ?>
                                            <div class="date color-primary"><?php echo $lead->last_meeting_name; ?></div>
                                            <small><?php echo "(" . time_ago($lastaction) . ")"; ?></small>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-xs-2">
                                    <h4 class="action-arrow"><i class="fa fa-angle-double-right"></i></h4>
                                </div>
                                <div class="col-xs-5">
                                    <div class="progress-stats-round text-center input-has-value">
                                        <span>NEXT ACTION</span>
                                        <h4 class="mr-tb-10"><i class="fa fa-calendar"></i></h4>
                                        <?php if (!empty($lead->nextaction)) { ?>
                                            <div class="date color-primary"><?php echo $lead->next_meeting_name ?></div>
                                            <small><?php echo "(" . after_time($lead->nextaction) . ")"; ?></small>
                                        <?php } else { ?>
                                            <div class="date color-primary"><?php echo $nextaction ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- /.card-user-info-widget -->
                    </div>
                    <!-- /.widget-body -->
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="lead_activity">
                            <h4 class="pull-left text-uppercase"><?php echo _l('activity_log'); ?></h4>
                            <a href="javascript:void(0)" data-pid="#lead_activity"
                               class="pull-right expnd_cllps inline-block mtop10 mright10"><i
                                        class="fa fa-caret-down"></i></a>
                            <div class="clearfix"></div>
                            <div class="panel_s btmbrd">
                                <div class="activity-wrapper">
                                    <input type="hidden" name="leadid" id="leadid" value="<?php echo $leadid; ?>">
                                    <div class="activity_section ">

                                        <?php foreach ($activity_log as $log) { ?>
                                            <div class="row  activity_single task_list lazy_content recent_act_master_list_content">
                                                <div class="col-xs-1 text-center activity_icon">
                                                    <div class="icon_section">
                                                        <a href="javascript:void(0)">
                                                            <i class="fa fa-list-ul menu-icon"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <!--<div class="feed-item">-->
                                                <!--<div class="date"><?php /*echo time_ago($log['date']); */ ?></div>-->
                                                <div class="col-xs-11 activity_desc">
                                                    <div class="proImgCol pull-right">
                                                        <?php if ($log['staffid'] != 0) { ?>
                                                            <a href="javascript:void(0)">
                                                                <?php echo staff_profile_image($log['staffid'], array('staff-profile-image-small pull-left mright5'));
                                                                ?>
                                                            </a>
                                                            <?php
                                                        } ?>
                                                    </div>

                                                    <?php
                                                    $additional_data = '';
                                                    if (!empty($log['additional_data'])) {
                                                        $additional_data = unserialize($log['additional_data']);
                                                        for ($i = 0; $i < count($additional_data); $i++) {
                                                            $additional_data[$i] = "<b>" . $additional_data[$i] . "</b>";
                                                        }
                                                        if ($log['staffid'] == get_staff_user_id()) {
                                                            echo ($log['staffid'] == 0) ? _l($log['description'], $additional_data) : "You " . str_replace($log['full_name'], "", _l($log['description'], $additional_data));
                                                        } else {
                                                            echo ($log['staffid'] == 0) ? _l($log['description'], $additional_data) : "You" . ' - ' . _l($log['description'], $additional_data);
                                                        }
                                                    } else {
                                                        if ($log['staffid'] == get_staff_user_id()) {
                                                            echo "You ";
                                                        } else {
                                                            echo $log['full_name'] . ' - ';
                                                        }
                                                        if ($log['custom_activity'] == 0) {
                                                            echo _l($log['description']);
                                                        } else {
                                                            echo _l($log['description'], '', false);
                                                        }
                                                    }
                                                    ?>
                                                    <div class="">
                                                        <span><?php echo date('D, ', strtotime($log['date']))._d($log['date']); ?>
                                                            at <?php echo _time($log['date']); ?>
                                                        </span>
                                                        <span>(<?php echo time_ago($log['date']); ?>);</span>
                                                    </div>


                                                </div>
                                                <!--</div>-->
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <!--  <div>
               <?php //echo render_textarea('lead_activity_textarea','','',array('placeholder'=>_l('enter_activity')),array(),'mtop15'); ?>
               <div class="text-right">
                  <button id="lead_enter_activity" class="btn btn-info"><?php //echo _l('submit'); ?></button>
               </div>
            </div>
            <div class="clearfix"></div> -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.widget-bg -->
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12 widget-holder">
                        <h4 class="text-uppercase">Lead Tools</h4>
                        <div class="lead-tool-block panel_s btmbrd">
                            <div class="widget-body clearfix">
                                <div class="tabs tabs-bordered">
                                    <ul class="nav nav-tabs">
                                        <?php if (has_permission('meetings', '', 'view')) { ?>
                                            <li class="col-lg-3 col-sm-4 col-xs-6 nav-item1">
                                                <a class="nav-link"
                                                   href="<?php echo admin_url('meetings?lid=' . $leadid); ?>">
                                                    <i class="fa fa-handshake-o"></i>
                                                    <p><?php echo _l('meetings'); ?></p>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if (has_permission('tasks', '', 'view')) { ?>
                                            <li class="col-lg-3 col-sm-4 col-xs-6 nav-item2">
                                                <a class="nav-link"
                                                   href="<?php echo admin_url('tasks?lid=' . $leadid); ?>">
                                                    <i class="fa fa-tasks"></i>
                                                    <p><?php echo _l('tasks'); ?></p>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if (has_permission('messages', '', 'view')) { ?>
                                            <li class="col-lg-3 col-sm-4 col-xs-6 nav-item3">
                                                <?php $count = get_count('tblmessages', 'lead', $leadid) ?>
                                                <a class="nav-link"
                                                   href="<?php echo admin_url('messages?lid=' . $leadid); ?>"
                                                   aria-expanded="true">
                                                    <i class="fa fa-envelope"></i>
                                                    <p><?php echo _l('messages'); ?></p>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <li class="col-lg-3 col-sm-4 col-xs-6 nav-item4">
                                            <?php $count = get_count('tblnotes', 'lead', $leadid) ?>
                                            <a class="nav-link"
                                               href="<?php echo admin_url('leads/notes/' . $leadid); ?>">
                                                <i class="fa fa-sticky-note-o"></i>
                                                <p><?php echo _l('notes'); ?></p>
                                            </a>
                                        </li>
                                        <?php if (has_permission('files', '', 'view')) { ?>
                                            <li class="col-lg-3 col-sm-4 col-xs-6 nav-item5">
                                                <a class="nav-link"
                                                   href="<?php echo admin_url('files?lid=' . $leadid); ?>">
                                                    <i class="fa fa-folder-open"></i>
                                                    <p><?php echo _l('files'); ?></p>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <li class="col-lg-3 col-sm-4 col-xs-6 nav-item6">
                                            <?php $count = get_count('tblproposaltemplates', 'lead', $leadid) ?>
                                            <a class="nav-link"
                                               href="<?php echo admin_url('proposaltemplates?lid=' . $leadid); ?>">
                                                <i class="fa fa-file-text-o"></i>
                                                <p><?php echo _l('proposals'); ?></p>
                                            </a>
                                        </li>
                                        <?php if (has_permission('invoices', '', 'view')) { ?>
                                            <li class="col-lg-3 col-sm-4 col-xs-6 nav-item7">
                                                <a class="nav-link"
                                                   href="<?php echo admin_url('invoices?lid=' . $leadid); ?>">
                                                    <i class="fa fa-money"></i>
                                                    <p><?php echo _l('invoices'); ?></p>
                                                </a>
                                            </li>
                                        <?php } ?>
                                        <?php if (has_permission('addressbook', '', 'create')) { ?>
                                            <li class="col-lg-3 col-sm-4 col-xs-6 nav-item8">
                                                <a class="nav-link"
                                                   href="<?php echo admin_url('addressbooks?lid=' . $leadid); ?>"
                                                   aria-expanded="true">
                                                    <i class="fa fa-address-book-o"></i>
                                                    <p><?php echo _l('Contacts'); ?></p>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                    <!-- /.nav-tabs -->
                                    <!-- /.tab-content -->
                                </div>
                                <!-- /.tabs -->
                            </div>
                            <!-- /.widget-body -->
                        </div>

                        <!-- /.widget-bg -->
                    </div>
                </div>
                <div class="row">
                    <div id="lead-details" class="col-md-12 lead-details">

                        <h4 class="pull-left text-uppercase">Lead Details</h4>
                        <a href="javascript:void(0)" data-pid="#lead-details"
                           class="pull-right expnd_cllps inline-block mtop10 mright10"><i class="fa fa-caret-down"></i></a>
                        <div class="clearfix"></div>
                        <div class="panel_s btmbrd">
                            <div class="widget-bg widget-body clearfix">
                                <div class="weather-card-default">
                                    <table class="table table-bordered table-condensed">
                                        <tbody>
                                        <tr>
                                            <td class="col1"><p><?php echo _l('lead_add_edit_event_status'); ?></p></td>
                                            <td>
                                                <?php if (has_permission('leads', '', 'edit')){ ?>
                                                    <select class="form-control selectpicker leadstatus"
                                                            lead_id="<?php echo $leadid; ?>">
                                                        <?php foreach ($statuses as $s) {
                                                            $statusselect = "";
                                                            if ($s['id'] == $lead->status) {
                                                                $statusselect = "selected='selected'";
                                                            }
                                                            ?>
                                                            <option value="<?php echo $s['id'] ?>" <?php echo $statusselect; ?>><?php echo $s['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                <?php } else { ?>
                                                <b><?php echo $lead->status_name;
                                                    } ?></b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p><?php echo _l('status updated'); ?></p></td>
                                            <td>
                                                <b><?php echo ($lead->dateupdated != "") ? date('m/d/Y', strtotime($lead->dateupdated)) . " (" . time_ago($lead->dateupdated) . ")" : ""; ?></b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p><?php echo _l('lead_add_edit_maxbudget'); ?></p></td>
                                            <td>
                                                <b><?php echo ($lead->budget != "") ? "$" . number_format($lead->budget, 0, ",", ",") : "$0"; ?></b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p><?php echo _l('lead_add_edit_confidence'); ?></p></td>
                                            <td>
                                                <div class="confidece">
                                                    <div>
                                                        <b><?php echo ($lead->bookingconfidence != "") ? $lead->bookingconfidence : "--"; ?></b>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p><?php echo _l('lead_add_edit_event_inquireon'); ?></p></td>
                                            <td><b>
                                                    <span><?php echo date('D, F d, Y', strtotime($lead->eventinquireon)) ?></span>
                                                    <span>(<?php echo time_ago($lead->eventinquireon); ?>)</span>
                                                </b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p><?php echo _l('lead_add_edit_decideby'); ?></p></td>
                                            <td>
                                                <b><span><?php
                                                        if($lead->eventdecideby!="" && $lead->eventdecideby!='0000-00-00'){
                                                            echo date('D, F d, Y', strtotime($lead->eventdecideby));
                                                        }else{
                                                            echo "--";
                                                        }
                                                        ?>
                                                    </span>
                                                    <!--<span>(<?php /*echo time_ago($lead->eventdecideby); */?>)</span>-->
                                                </b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p><?php echo _l('lead_add_edit_source'); ?></p></td>
                                            <td>
                                                <b><?php echo ($lead->source_name != "") ? $lead->source_name : "--"; ?></b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p><?php echo _l('lead_add_edit_sourcedetails'); ?></p></td>
                                            <td>
                                                <b><?php echo ($lead->sourcedetails != "") ? $lead->sourcedetails : "--"; ?></b>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><p><?php echo _l('lead_add_edit_comments'); ?></p></td>
                                            <td><b><?php echo ($lead->comments != "") ? $lead->comments : "--"; ?></b>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
        </div>


    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="leadcontactlist" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Contacts</h4>
            </div>
            <!-- <div class="modal-body"> -->
            <h4 class="pleft5">Please select one or more contacts to assign as client.</h4>
            <form action="<?php echo admin_url('leads/convert_lead/' . $leadid); ?>" method="post">
                <table id="leadcontacts" class="table table-striped table-bordered" style="width:100%;margin: 0px;">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Client</th>
                    </tr>
                    </thead>

                    <?php
                    foreach ($leadcontacts as $key => $value) {
                        ?>
                        <tr>
                            <td><?php echo $value['firstname'] . ' ' . $value['lastname']; ?></td>
                            <td><?php echo $value['email']; ?></td>
                            <td>
                                <div class="checkbox">
                                    <input id="user_<?php echo $value['addressbookid']; ?>"type="checkbox" name='selectedcontact[]'
                                           value="<?php echo $value['addressbookid']; ?>" class="leadcontact"/>
                                    <label for="user_<?php echo $value['addressbookid']; ?>">
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
                <!-- </div> -->
                <div class="modal-footer">
                    <button type="submit" class="btn btn-info" id="leadToprojectClient">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<?php echo app_stylesheet('assets/css', 'jquery.countdown.css'); ?>
<?php echo app_script('assets/js', 'jquery.plugin.js'); ?>
<?php echo app_script('assets/js', 'jquery.countdown.js'); ?>
<script type="text/javascript">
    newDate = new Date(<?php echo date("Y, n - 1, d, H, i", strtotime($lead->eventstartdatetime)) ?>);
    $('#leadCountdown').countdown({until: newDate});
</script>
</body>
</html>