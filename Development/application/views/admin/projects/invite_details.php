<?php
/**
 * Added By : Vaidehi
 * Dt : 01/09/2018
 * Invite Detail Screen
 */

init_head();
$invitee = $invite_details->invitee;
if ($invite_details->contacttype == 5) {
    $invitee = $invite_details->venueinvitee;
}
?>
<div id="wrapper" class="invitedetaildashboard">
    <div class="content invitedetails">
        <div class="breadcrumb">
            <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <a href="<?php echo admin_url('projects/invites'); ?>">Invites</a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <span><?php echo $title; ?></span>
        </div>
        <h1 class="pageTitleH1"><i class="fa fa-envelope-open-o"></i><?php echo _l('invite'); ?></h1>
        <div class="clearfix"></div>
        <div class="col-sm-12">
            <div class="project_cover_image">
            <?php echo project_cover_image($invite_details->pid, array('cover_image'), '');?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="row">
                    <div class="col-sm-4">
                        <?php
                        $src = "<img class='profile_image lead-profile-image-thumb img-responsive img-thumbnail' src='" . base_url('assets/images/preview_no_available.jpg') . "'>";
                        if ($invite_details->pid > 0) {
                            $src = project_profile_image($invite_details->pid, array('profile_image', 'img-responsive img-thumbnail', 'project-profile-image-thumb'), '');
                        }
                        ?>
                        <figure class="text-center profileImg_blk"><?php echo $src; ?></figure>
                    </div>
                    <div class="col-sm-8">
                        <h4><?php echo isset($invite_details->project_name) ? $invite_details->project_name : "--"; ?></h4>
                        <div class="card-user-info-widget panel_s btmbrd">
                            <div class="row">
                                <div class="col-sm-12 card-user-info">
                                    <a data-toggle="tooltip" data-title="Back"
                                       href="<?php echo admin_url('projects/invites'); ?>"
                                       class="btn btn-default pull-right hide"><i class="fa fa-chevron-left"></i></a>

                                    <?php
                                    if (($invite_details->eventstartdatetime != "")) {
                                        $eventstartdatetime = date('l, F d, Y', strtotime($invite_details->eventstartdatetime));
                                    } else {
                                        $eventstartdatetime = "--";
                                    }

                                    if (($invite_details->eventenddatetime != "")) {
                                        $eventenddatetime = date('l, F d, Y', strtotime($invite_details->eventenddatetime));
                                    } else {
                                        $eventenddatetime = "--";
                                    }

                                    if (($invite_details->assigned_name != "")) {
                                        $assigned_name = $invite_details->assigned_name;
                                    } else if (($invite_details->venue_name != "")) {
                                        $assigned_name = $invite_details->venue_name;
                                    } else {
                                        $assigned_name = "--";
                                    }

                                    if (($invite_details->project_type != "")) {
                                        $eventtypename = $invite_details->project_type;
                                    } else {
                                        $eventtypename = "--";
                                    }
                                    ?>
                                    <ul class="list-unstyled mb-0 text-muted email-details-list">
                                        <li class="col-12"><i
                                                    class="list-icon fa fa-book"></i><?php echo $eventtypename; ?></li>

                                        <li class="col-12 mr-t-20"><i
                                                    class="list-icon fa fa-calendar"></i><?php echo $eventstartdatetime; ?>
                                        </li>
                                        <!--<li class="col-12"><i class="list-icon fa fa-calendar-check-o"></i>&nbsp; <?php /*echo $eventenddatetime; */ ?></li>-->
                                        <li class="col-12">
                                            <i class="list-icon fa fa-clock-o"></i>
                                            <?php
                                            if (app_time_format() == 24) {
                                                echo date("G:i ", strtotime($invite_details->eventstartdatetime));
                                            } else {
                                                echo date("g:i A", strtotime($invite_details->eventstartdatetime));
                                            } ?>
                                        </li>
                                        <?php if (isset($venue) && !empty($venue)) {
                                            $vname = $venue->venuename;
                                            //$vaddress = $venue->venueaddress . ", " . $venue->venueaddress2 . ", " . $venue->venuecity . ", " . $venue->venuestate;
                                            $vaddress = "";

                                            //$vaddress = $venue->venueaddress . ",<br /> ";
                                            if ($venue->venueaddress != "") {
                                                $vaddress = $venue->venueaddress . "<br /> ";
                                            }
                                            if ($venue->venueaddress2 != "") {
                                                $vaddress .= $venue->venueaddress2 . "<br />";
                                            }
                                            if ($venue->venuecity != "") {
                                                $vaddress .= $venue->venuecity." ";
                                            }
                                            if ($venue->venuestate != "") {
                                                $vaddress .= $venue->venuestate;
                                            }
                                            ?>
                                            <li class="col-12 venue">
                                                <i class="list-icon fa fa-map-marker"></i>
                                                <span><?php echo $vname; ?></span>
                                                <span class="display-block" style="margin-left: 24px;line-height: 1.5;position: relative;top: -4px;">
                                                <?php echo $vaddress; ?>
                                            </span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <h4>Invite Approvals</h4>
                <div class="widget-holder ">
                    <div class="rows">
                        <div class="widget-bg lead-details panel_s btmbrd">

                            <div class="widget-body clearfix ">
                                <div class="weather-card-default">
                                    <?php
                                    $clientstutus = array();
                                    $clients = $invite_details->clients;
                                    $currentclintstatus = "";
                                    $currentuser = array();
                                    /*echo "<pre>";
                                    print_r($clients);
                                    print_r($invite_details->invitestatuses);
                                    die('<--here');*/
                                    foreach ($invite_details->invitestatuses as $key => $invitestatus) {
                                        if (get_staff_user_id() == $invitestatus['userid']) {
                                            $currentuser = $invite_details->invitestatuses[$key];
                                        }
                                        if ($invitestatus['usertype'] == "client") {
                                            $clientstutus[] = $invite_details->invitestatuses[$key]['status'];
                                            if (get_staff_user_id() == $invitestatus['userid']) {
                                                $currentclintstatus = $invitestatus['status'];
                                                $currentuser = $invite_details->invitestatuses[$key];
                                            }
                                        }
                                        if ($invitestatus['userid'] > 0 && $invitestatus['usertype'] != "venue" && $invitestatus['usertype'] != "invitee") { ?>
                                            <div class="user_approval">
                                                <div class="iaClient">
                                                    <h4><?php echo get_staff_full_name($invitestatus['userid']); ?></h4>
                                                    <a><?php echo get_staff_email($invitestatus['userid']); ?></a>
                                                    <div class="labelStatus">
                                                        <?php
                                                        if (in_array($invitestatus['userid'], $clients)) { ?>
                                                            <span class="isclient label">CLIENT</span>
                                                        <?php } else {

                                                        } ?>
                                                    </div>
                                                </div>
                                                <div class="iaStatus">
                                                    <?php
                                                    if (get_staff_user_id() == $invitestatus['userid']) { ?>
                                                        <select id="update_invite_stautus"
                                                                class="update_invite_stautus selectpicker <?php echo $invitestatus['status'] ?>"
                                                                data-inviteid= <?php echo $invite_details->inviteid; ?>>
                                                            <?php if ($invitestatus['status'] == "pending") { ?>
                                                                <option value="pending" selected>Pending</option>
                                                            <?php } ?>
                                                            <option value="approved" <?php echo $invitestatus['status'] == "approved" ? "selected" : "" ?>>
                                                                Approved
                                                            </option>
                                                            <option value="declined" <?php echo $invitestatus['status'] == "declined" ? "selected" : "" ?>>
                                                                Declined
                                                            </option>
                                                        </select>
                                                    <?php } else { ?>
                                                        <div class="invite_user_status <?php echo strtolower($invitestatus['status']); ?>">

                                                            <?php
                                                            if ($invitestatus['status'] == "approved") {
                                                                echo "<i class='fa fa-check'></i> ";
                                                            } elseif ($invitestatus['status'] == "declined") {
                                                                echo "<i class='fa fa-ban'></i> ";
                                                            }
                                                            echo $invitestatus['status']; ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12 ">
                <h4>Invite Details</h4>
                <div class="widget-holder ">
                    <div class="row">
                        <div class="widget-bg lead-details panel_s btmbrd">
                            <div class="user_approval">
                                <div class="iaClient">
                                    <?php
                                    if ($invite_details->contacttype == 5) { ?>
                                        <h4><?php echo $invite_details->venue_name; ?></h4>
                                        <a><?php
                                            $venueEmail = unserialize($invite_details->venue_email);
                                            echo $venueEmail[0]['email']; ?>
                                        </a>
                                    <?php } else {
                                        if ($invite_details->staffid > 0) { ?>
                                            <h4><?php echo get_staff_full_name($invite_details->staffid); ?></h4>
                                            <a><?php echo get_staff_email($invite_details->staffid); ?></a>
                                        <?php } elseif ($invite_details->contactid > 0) { ?>
                                            <h4><?php echo get_addressbook_full_name($invite_details->contactid); ?></h4>
                                            <a><?php echo get_addressbook_email($invite_details->contactid); ?></a>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <div class="iaStatus">
                                    <?php
                                    if (get_staff_user_id() == $invite_details->staffid) { ?>
                                        <select id="update_invite_stautus"
                                                class="update_invite_stautus selectpicker <?php echo $invitee->status ?>"
                                                data-inviteid= <?php echo $invite_details->inviteid; ?>>
                                            <?php if ($invitee->status == "pending") { ?>
                                                <option value="pending" selected>Pending</option>
                                            <?php } ?>
                                            <option value="approved" <?php echo $invitee->status == "approved" ? "selected" : "" ?>>
                                                Approved
                                            </option>
                                            <option value="declined" <?php echo $invitee->status == "declined" ? "selected" : "" ?>>
                                                Declined
                                            </option>
                                        </select>
                                    <?php } ?>
                                </div>
                                <div class="clearfix"></div>
                                <div class="invite_summary invite_summary_blk">
                                    <div class="row isLi">
                                        <div class="col-xs-4 isHeading"><span><?php echo "STATUS"; ?></span></div>
                                        <div class="col-xs-8 isDetails">
                                            <div class="invite_user_status inviteeStatus">
                                                <div class="label"><?php echo $invitee->status; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row isLi">
                                        <div class="col-xs-4 isHeading"><span><?php echo "ROLE"; ?></span></div>
                                        <div class="col-xs-8 isDetails">
                                            <div class="invite_user_status contacttypeStatus">
                                                <div class="label">
                                                    <?php if ($invite_details->contacttype == 3) {
                                                        echo "Vendor";
                                                    } elseif ($invite_details->contacttype == 4) {
                                                        echo "Collaborator";
                                                    } else {
                                                        echo "Venue";
                                                    } ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row isLi">
                                        <div class="col-xs-4 isHeading"><span><?php echo "PERMISSION"; ?></span></div>
                                        <div class="col-xs-8 isDetails">
                                            <div class="invite_user_status permissions_blk">
                                                <?php foreach ($invite_details->permissions as $permission) {
                                                    echo "<span>" . $permission['name'] . "</span>";
                                                } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row isLi">
                                        <div class="col-xs-4 isHeading"><?php echo "INVITED BY"; ?></div>
                                        <div class="col-xs-8 isDetails">
                                            <div class="invite_user_status iuDetails">
                                                <?php echo staff_profile_image($invite_details->invitedby); ?>
                                                <div class="iuDetailsCont">
                                                    <h4><?php echo get_staff_full_name($invite_details->invitedby); ?></h4>
                                                    <p class="fs80">on <?php echo _dt($invite_details->datecreated); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--<div class="row">
                                        <div class="col-xs-4 isHeading"><?php /*echo "COMMENT"; */ ?></div>
                                        <div class="col-xs-8">
                                            <div class="invite_user_status">
                                                <?php /*echo $invitee->status; */ ?>
                                            </div>
                                        </div>
                                    </div>-->
                                    <?php if ($invite_details->status != "approved") {
                                    }
                                    if ($invite_details->contacttype == 3) {
                                        $type = "vendor";
                                    } elseif ($invite_details->contacttype == 4) {
                                        $type = "collaborator";
                                    } else {
                                        $type = "venue";
                                    }
                                    $usertype = "";
                                    $userid = "";
                                    if (!empty($currentuser)) {
                                        $usertype = $currentuser['usertype'];
                                        $userid = $currentuser['userid'];
                                    }
                                    ?>
                                    <?php if ((get_staff_user_id() == $invite_details->created_by && !in_array('approved', $clientstutus)) || (in_array(get_staff_user_id(), $clients) && $currentclintstatus == "approved")) { ?>
                                        <div class="resend_invite text-center">
                                            <button class="btn btn-info"
                                                    onclick="fnResendInvite(<?php echo $invite_details->inviteid; ?>, '<?php echo $type; ?>','<?php echo $usertype; ?>','<?php echo $userid; ?>');">
                                                <i class="fa fa-send"></i>RE-SEND INVITE
                                            </button>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <!--<div class="widget-body clearfix ">
                                <div class="weather-card-default table-responsive">
                                    <table class="table table-bordered table-condensed">
                                        <thead>
                                        <tr>
                                            <th><?php /*echo _l('project_name'); */ ?></th>
                                            <th><?php /*echo _l('status'); */ ?></th>
                                            <th><?php /*echo _l('permissions'); */ ?></th>
                                            <th><?php /*echo _l('comments'); */ ?></th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <input type="hidden" name="totevents" id="totevents"
                                               value="<?php /*echo count($invite_details->events); */ ?>">
                                        <input type="hidden" name="eventsupdate" id="eventsupdate" value="0">
                                        <input type="hidden" name="contacttype" id="contacttype"
                                               value="<?php /*echo $invite_details->contacttype; */ ?>">
                                        <?php
                            /*                                        $session_data = get_session_data();
                                                                    $user_type = $session_data['user_type'];
                                                                    $status = 0;
                                                                    $account_owner_action = 0;
                                                                    $vendor_action = 0;
                                                                    foreach ($invite_details->events as $event) {
                                                                        if ($event['status'] == 'Pending Approval from Account Owner') {
                                                                            $status++;
                                                                        } elseif ($event['status'] == 'Declined by Account Owner') {
                                                                            $account_owner_action++;
                                                                        } elseif ($event['status'] == 'Declined by Vendor') {
                                                                            $vendor_action++;
                                                                        }
                                                                        */ ?>
                                            <tr>
                                                <td><?php /*echo $event['project_name']; */ ?></td>
                                                <td id="td-<?php /*echo $event['projectid']; */ ?><?php /*echo $event['inviteid']; */ ?>"><?php /*echo $event['status']; */ ?></td>
                                                <td><?php /*echo str_replace(",", "<br/>", $event['permission_name']); */ ?></td>
                                                <td id="comments-<?php /*echo $event['projectid']; */ ?><?php /*echo $event['inviteid']; */ ?>"><?php /*echo(isset($event['comments']) ? $event['comments'] : ''); */ ?></td>
                                                <td class="text-right"
                                                    id="accept-<?php /*echo $event['projectid']; */ ?><?php /*echo $event['inviteid']; */ ?>">
                                                    <?php
                            /*                                                    if ($user_type == 1) {
                                                                                    */ ?>
                                                        <button class="btn btn-info"
                                                                onclick="fnAccept(<?php /*echo $event['projectid']; */ ?>,<?php /*echo $event['inviteid']; */ ?>);">
                                                            Accept
                                                        </button>
                                                        <a onclick="decline_invite(<?php /*echo $event['projectid']; */ ?>,<?php /*echo $event['inviteid']; */ ?>, <?php /*echo $invite_details->contacttype; */ ?>); return false;"
                                                           class="btn btn-default">Decline</a>
                                                    <?php /*} */ ?>
                                                </td>
                                            </tr>
                                            <?php
                            /*                                        }
                                                                    */ ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php /*if ($user_type == 1) {
                                    if (count($invite_details->events) == $status) { */ ?>
                                        <button class="btn btn-info pull-right disabled" id="btnSendEmail">Send Invite
                                        </button>
                                    <?php /*} else {
                                        if ($vendor_action == 0) { */ ?>
                                            <button class="btn btn-info pull-right"
                                                    onclick="fnSend(<?php /*echo $event['inviteid']; */ ?>);">Send Invite
                                            </button>
                                        <?php /*}
                                    }
                                }
                                */ ?>
                                <?php /*if ($user_type == 2) {
                                    if ($account_owner_action > 0 && $vendor_action == 0) {
                                        */ ?>
                                        <button class="btn btn-info pull-right"
                                                onclick="fnResendInvite(<?php /*echo $event['inviteid']; */ ?>, 'account-owner');">
                                            Resend Invite
                                        </button>
                                    <?php /*}
                                } else {
                                    if ($vendor_action > 0) { */ ?>
                                        <button class="btn btn-info pull-right"
                                                onclick="fnResendInvite(<?php /*echo $event['inviteid']; */ ?>, 'vendor');">
                                            Resend
                                            Invite
                                        </button>
                                    <?php /*}
                                }
                                */ ?>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 ">
            </div>
        </div>


    </div>
</div>
<?php include_once(APPPATH . 'views/admin/projects/decline-invite.php'); ?>
<?php init_tail(); ?>
<script type="text/javascript">
    function decline_invite(projectid, inviteid, contacttype) {
        $("#inviteid").val(inviteid);
        $("#projectid").val(projectid);
        $("#contacttype").val(contacttype);
        $('#decline').modal('show');
    }

    function fnAccept(projectid, inviteid) {
        var contacttype = $('#contacttype').val();
        $.ajax({
            url: "<?php echo admin_url('projects/invite_status_change');?>",
            method: "post",
            data: "projectid=" + projectid + "&inviteid=" + inviteid + "&accept=" + 1 + "&contacttype=" + contacttype,
            success: function (data) {
                if (data) {
                    var totupd = parseInt($("#eventsupdate").val()) + parseInt(1);
                    $("#eventsupdate").val(totupd);
                    $('#td-' + projectid + inviteid).html('Approved by Account Owner');
                    //$('#accept-'+projectid+inviteid).html('<i class="fa fa-2x fa-check" aria-hidden="true" style="color: #84c529;"></i>');
                }

                if ($("#eventsupdate").val() == $("#totevents").val()) {
                    $("#btnSendEmail").removeClass("disabled");
                }
            }
        });
    }

    function fnSend(inviteid) {
        $.ajax({
            url: "<?php echo admin_url('projects/send_invite');?>",
            method: "post",
            data: "inviteid=" + inviteid,
            success: function (data) {
                //console.log(data);
                data = $.parseJSON(data);
                alert_float(data.alert_type, data.message);
                //window.location.href = '<?php echo admin_url("projects/invites"); ?>';
            }
        });
    }

    function fnResendInvite(inviteid, type, usertype, userid) {
        $.ajax({
            url: "<?php echo admin_url('projects/resend_invite');?>",
            method: "post",
            data: "inviteid=" + inviteid + "&type=" + type + "&usertype=" + usertype + "&userid=" + userid,
            success: function (data) {
                alert_float(data.alert_type, data.message);
                //window.location.href = '<?php echo admin_url("projects/invites"); ?>';
            }
        });
    }

    $("#btnsubmit").click(function () {
        var projectid = $("#projectid").val();
        var inviteid = $("#inviteid").val();
        var contacttype = $("#contacttype").val();
        var comments = $("#comments").val();
        $.ajax({
            url: "<?php echo admin_url('projects/invite_status_change');?>",
            method: "post",
            data: "projectid=" + projectid + "&inviteid=" + inviteid + "&comments=" + comments + "&accept=" + 0 + "&contacttype=" + contacttype,
            success: function (data) {
                //console.log(data);
                if (data) {
                    var totupd = parseInt($("#eventsupdate").val()) + parseInt(1);
                    $("#eventsupdate").val(totupd);
                    $('#td-' + projectid + inviteid).html('Declined by Account Owner');
                    //$('#accept-'+projectid+inviteid).html('<i class="fa fa-2x fa-times" aria-hidden="true" style="color: #fc2d42;"></i>');
                    $('#comments-' + projectid + inviteid).html(comments);
                    $('#decline').modal('hide');
                }

                if ($("#eventsupdate").val() == $("#totevents").val()) {
                    $("#btnSendEmail").removeClass("disabled");
                }
            }
        });
    });
</script>
</body>
</html>