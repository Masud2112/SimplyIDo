<?php
/**
 * Added By : Vaidehi
 * Dt : 01/09/2018
 * View Invite Screen
 */
?>
<div id="wrapper" class="invitedetaildashboard">
    <div class="content invitedetails">
        <div class="row">
            <div class="col-md-12 widget-holder">
                <div class="widget-bg lead-details">
                    <div class="widget-heading">
                        <h3>Invite Details</h3>
                    </div>
                    <div class="widget-body clearfix">
                        <div class="weather-card-default">
                            <table class="table table-bordered table-condensed">
                                <thead>
                                <tr>
                                    <th>No.</th>
                                    <th><?php echo _l('event_name'); ?></th>
                                    <th><?php echo _l('event_type'); ?></th>
                                    <th><?php echo _l('start_date_time'); ?></th>
                                    <th><?php echo _l('end_date_time'); ?></th>
                                    <th><?php echo _l('event_venue'); ?></th>
                                    <th><?php echo _l('invited_by'); ?></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i = 0;
                                foreach ($invite_details->events as $event) {

                                    $i++;
                                    ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $event['project_name']; ?></td>
                                        <td><?php echo $event['project_type']; ?></td>
                                        <td><?php echo date("m/d/Y H:i", strtotime($event['eventstartdatetime'])); ?></td>
                                        <td><?php echo date("m/d/Y H:i", strtotime($event['eventenddatetime'])); ?></td>
                                        <td><?php echo(isset($invite_details->venue_name) ? $invite_details->venue_name : ""); ?></td>
                                        <td><?php echo $invite_details->invited_name; ?></td>
                                        <td id="accept-<?php echo $event['projectid']; ?><?php echo $event['inviteid']; ?>">
                                            <?php if ($invite_details->contacttype == 3) { ?>
                                                <?php if ($invite_details->invitee->status != 'approved' && $invite_details->status != 'declined') { ?>
                                                    <button class="btn btn-info"
                                                            onclick="fnAccept(<?php echo $event['projectid']; ?>,<?php echo $event['inviteid']; ?>,<?php echo $invite_details->contacttype; ?>);">
                                                        Accept
                                                    </button>
                                                    <a onclick="decline_invite(<?php echo $event['projectid']; ?>,<?php echo $event['inviteid']; ?>,<?php echo $invite_details->contacttype; ?>); return false;"
                                                       class="btn btn-default">Decline</a>
                                                <?php } ?>
                                                <?php if ($invite_details->invitee->status == 'approved') { ?>
                                                    <i class="fa fa-2x fa-check" aria-hidden="true"
                                                       style="color: #84c529;"></i>
                                                <?php } ?>
                                                <?php if ($invite_details->invitee->status == 'declined') { ?>
                                                    <i class="fa fa-2x fa-times" aria-hidden="true"
                                                       style="color: #fc2d42;"></i>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if ($invite_details->contacttype == 4) { ?>
                                                <?php if ($invite_details->invitee->status != 'approved' && $invite_details->status != 'declined') { ?>
                                                    <button class="btn btn-info"
                                                            onclick="fnAccept(<?php echo $event['projectid']; ?>,<?php echo $event['inviteid']; ?>,<?php echo $invite_details->contacttype; ?>);">
                                                        Accept
                                                    </button>
                                                    <a onclick="decline_invite(<?php echo $event['projectid']; ?>,<?php echo $event['inviteid']; ?>,<?php echo $invite_details->contacttype; ?>); return false;"
                                                       class="btn btn-default">Decline</a>
                                                <?php } ?>
                                                <?php if ($invite_details->invitee->status == 'approved') { ?>
                                                    <i class="fa fa-2x fa-check" aria-hidden="true"
                                                       style="color: #84c529;"></i>
                                                <?php } ?>
                                                <?php if ($invite_details->invitee->status == 'declined') { ?>
                                                    <i class="fa fa-2x fa-times" aria-hidden="true"
                                                       style="color: #fc2d42;"></i>
                                                <?php } ?>
                                            <?php } ?>
                                            <?php if ($invite_details->contacttype == 5) { ?>
                                                <?php if ($invite_details->venueinvitee->status == 'pending') {
                                                    ?>
                                                    <button class="btn btn-info"
                                                            onclick="fnAccept(<?php echo $event['projectid']; ?>,<?php echo $event['inviteid']; ?>,<?php echo $invite_details->contacttype; ?>);">
                                                        Accept
                                                    </button>
                                                    <a onclick="decline_invite(<?php echo $event['projectid']; ?>,<?php echo $event['inviteid']; ?>,<?php echo $invite_details->contacttype; ?>); return false;"
                                                       class="btn btn-default">Decline</a>
                                                <?php } ?>
                                                <?php if ($invite_details->venueinvitee->status == 'approved') { ?>
                                                    <i class="fa fa-2x fa-check" aria-hidden="true"
                                                       style="color: #84c529;"></i>
                                                <?php } ?>
                                                <?php if ($invite_details->venueinvitee->status == 'declined') { ?>
                                                    <i class="fa fa-2x fa-times" aria-hidden="true"
                                                       style="color: #fc2d42;"></i>
                                                <?php } ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once(APPPATH . 'views/admin/projects/decline-invite.php'); ?>
<script type="text/javascript">
    function decline_invite(projectid, inviteid, contacttype) {
        $("#inviteid").val(inviteid);
        $("#projectid").val(projectid);
        $("#contacttype").val(contacttype);
        $('#decline').modal('show');
    }

    function fnAccept(projectid, inviteid, contacttype) {
        $.ajax({
            url: "<?php echo base_url('clients/invite_status_change');?>",
            method: "post",
            data: "projectid=" + projectid + "&inviteid=" + inviteid + "&vendor_accept=" + 1 + '&contacttype=' + contacttype,
            success: function (data) {
                //console.log(data);
                if (data) {
                    $('#accept-' + projectid + inviteid).html('<i class="fa fa-2x fa-check" aria-hidden="true" style="color: #84c529;"></i>');
                }
            }
        });
    }

    $("#btnsubmit").click(function () {
        var projectid = $("#projectid").val();
        var inviteid = $("#inviteid").val();
        var contacttype = $("#contacttype").val();
        var comments = $("#comments").val();
        $.ajax({
            url: "<?php echo base_url('clients/invite_status_change');?>",
            method: "post",
            data: "projectid=" + projectid + "&inviteid=" + inviteid + "&comments=" + comments + "&vendor_accept=" + 0 + "&contacttype=" + contacttype,
            success: function (data) {
                if (data) {
                    $('#accept-' + projectid + inviteid).html('<i class="fa fa-2x fa-times" aria-hidden="true" style="color: #fc2d42;"></i>');
                    $('#decline').modal('hide');
                }
            }
        });
    });
</script>
</body>
</html>