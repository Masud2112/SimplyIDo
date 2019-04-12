<?php
/**
 * Added By : Vaidehi
 * Dt : 12/19/2017
 * Add New Project Form
 */
init_head();
?>
<div id="wrapper">
    <div class="content getinvitees-page">
        <div class="row">
            <div class="col-md-12">
                <?php /*if (isset($pg) && $pg == 'home') { */ ?>
                <div class="pull-right">
                    <div class="breadcrumb">
                        <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <span><?php echo "Invites"; ?></span>
                    </div>
                </div>
                <?php /*} */ ?>
                <h1 class="pageTitleH1"><i class="fa fa-envelope-open-o"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>


                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="inviteTabsSection">
                            <div class="pull-left">
                                <?php if ($switch_invites_kanban != 1) { ?>
                                    <a href="javascript:void(0)" class="btn btn-info active invite_tab"
                                       data-status="Pending">
                                        <?php echo _l('Pending'); ?>
                                        <span class="count_by_status"><?php echo count($invitees_pending); ?></span>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-info invite_tab" data-status="approved">
                                        <?php echo _l('Approved'); ?>
                                        <span class="count_by_status"><?php echo count($invitees_approved); ?></span>
                                    </a>
                                    <a href="javascript:void(0)" class="btn btn-info invite_tab" data-status="declined">
                                        <?php echo _l('Declined'); ?>
                                        <span class="count_by_status"><?php echo count($invitees_declined); ?></span>
                                    </a>
                                <?php } ?>
                                <input type="hidden" id="invite_status" value="Pending"/>
                            </div>
                            <div class="pull-right">
                                <?php
                                $list = $card = "";
                                if (isset($switch_invites_kanban) && $switch_invites_kanban == 1) {
                                    $list = "selected disabled";
                                } else {
                                    $card = "selected disabled";
                                } ?>
                                <?php if (is_mobile()) {
                                    echo '<a class="btn btn-primary  filter_btn_search"><i class="glyphicon glyphicon-search"></i></a>';
                                } ?>
                                <!--<a class="btn btn-primary filter_btn"><i class="fa fa-filter"></i></a>-->
                                <a href="<?php echo admin_url('projects/switch_invites_kanban/'); ?>"
                                   class="btn btn-primary viewchangeBtn hidden-xs <?php echo $list ?>">
                                    <?php echo _l('switch_to_list_view'); ?>
                                </a>
                                <a href="<?php echo admin_url('projects/switch_invites_kanban/1'); ?>"
                                   class="btn btn-primary viewchangeBtn hidden-xs <?php echo $card ?>">
                                    <?php echo _l('projects_switch_to_kanban'); ?>
                                </a>
                            </div>
                        </div>
                        <?php if ($switch_invites_kanban != 1) { ?>
                            <div class="col-sm-4  pull-right leads-search">
                                <div class="message_search text-right" data-toggle="tooltip" data-placement="bottom"
                                     data-title="Use # + tagname to search by tags">
                                    <span class="input-group-addon lead_serach_ico inline-block"><span
                                                class="glyphicon glyphicon-search"></span></span>
                                    <div class="lead_search_inner form-group inline-block no-margin"><input
                                                type="search" id="search" name="search" class="form-control"
                                                data-name="search" onkeyup="invites_kanban();" placeholder="Search..."
                                                value=""></div>
                                </div>
                                <input type="hidden" name="sort_type" value="">
                                <input type="hidden" name="sort" value="">
                            </div>
                        <?php } ?>
                        <?php if ($this->session->has_userdata('invites_kanban_view') && $this->session->userdata('invites_kanban_view') == 'true') { ?>
                            <div class="clearfix"></div>
                            <div class="active kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                                <div class="row">
                                    <div class="projects-kan-ban">
                                        <div id="kan-ban"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <table class="table sdtheme  dataTable no-footer dt-table collapsed table-invitees"
                                   data-order-col="3" data-order-type="desc">
                                <thead class="">
                                <tr>
                                    <th><?php echo _l('name'); ?></th>
                                    <th><?php echo _l('role'); ?></th>
                                    <th><?php echo _l('project'); ?></th>
                                    <th><?php echo _l('invited_by'); ?></th>
                                    <!--<th><?php /*echo _l('invited_date'); */ ?></th>-->
                                    <th><?php echo _l('status'); ?></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (count($invitees) > 0) {
                                    /*echo "<pre>";
                                    print_r($invitees);
                                    die('<--here');*/
                                    foreach ($invitees as $invitee) {
                                        if (!empty($invitee['venue_email']) && is_serialized($invitee['venue_email'])) {
                                            $invitee['venue_email'] = unserialize($invitee['venue_email']);
                                            $invitee['venue_email'] = $invitee['venue_email'][0]['email'];
                                        }
                                        if ($invitee['contacttype'] == 5) {
                                            $inviteImg = venue_logo_image($invitee['venueid']);
                                        } else {
                                            if (isset($invitee['staffid']) && $invitee['staffid'] > 0) {
                                                $inviteImg = staff_profile_image($invitee['staffid']);
                                            } else {
                                                $inviteImg = addressbook_profile_image($invitee['contactid']);
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td>

                                                <div class="event_Det_blk">
                                                    <div class="lead-pimg profImgDiv">
                                                        <!--<span class="no-img staff-profile-image-small"
                                                              style="background-color:#E91E63">MS</span>
                                                        <div class="event_image project-pimg">
                                                            <img src="http://172.16.1.51/SimplyIDo/Development/uploads/project_profile_images/1/thumb_sq-g-1x1.jpg"
                                                                 class="profile-image-small" alt="Fisrt Sido Lead">
                                                        </div>-->
                                                        <?php echo $inviteImg; ?>
                                                    </div>
                                                    <div class="lead-det">
                                                        <div class="eventname">
                                                            <strong><?php echo !empty($invitee['assigned_name']) ? $invitee['assigned_name'] : $invitee['venue_name']; ?></strong>
                                                            <div class="email"><?php echo !empty($invitee['assigned_email']) ? $invitee['assigned_email'] : $invitee['venue_email']; ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php //echo !empty($invitee['assigned_name']) ? $invitee['assigned_name'] : $invitee['venue_name']; ?>
                                            </td>
                                            <td>
                                                <span class="label label-default">
                                                <?php if ($invitee['contacttype'] == 3) {
                                                    echo "Vendor";
                                                } elseif ($invitee['contacttype'] == 4) {
                                                    echo "Collaborator";
                                                } else {
                                                    echo "Venue";

                                                } ?></span>
                                            </td>
                                            <!--<td>
                                                <?php /*if (!empty($invitee['assigned_email'])) {
                                                    echo $invitee['assigned_email'];
                                                } else {
                                                    if (is_serialized($invitee['venue_email'])) {
                                                        $venueemail = unserialize($invitee['venue_email']);
                                                        $invitee['venue_email'] = $venueemail[0]['email'];
                                                    }
                                                    echo $invitee['venue_email'];

                                                } */ ?>
                                            </td>-->
                                            <td>
                                                <div class="inviteeListDate_blk">
                                                    <div class="ilDate card_date_blk">
                                                        <div class="card_date"
                                                             title="<?php echo date('Y', strtotime($invitee['eventstartdatetime'])); ?>">
                                                            <div class="card_month">
                                                                <small><?php echo strtoupper(date('M', strtotime($invitee['eventstartdatetime']))); ?></small>
                                                            </div>
                                                            <div class="card_d">
                                                                <strong><?php echo date('d', strtotime($invitee['eventstartdatetime'])); ?></strong>
                                                            </div>
                                                            <div class="card_day">
                                                                <small><?php echo strtoupper(date('D', strtotime($invitee['eventstartdatetime']))); ?></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="ild-Deta_blk">
                                                        <strong><a href=""><?php echo $invitee['project_name']; ?></a></strong>
                                                        <span>
                                                        <i class="fa fa-book"></i>
                                                        <?php echo $invitee['project_type']; ?>
                                                    </span>
                                                    </div>
                                                </div>

                                            </td>
                                            <td>

                                                <div class="event_Det_blk">
                                                    <div class="lead-pimg">
                                                        <!--<span class="no-img staff-profile-image-small"
                                                              style="background-color:#E91E63">MS</span>
                                                        <div class="event_image project-pimg">
                                                            <img src="http://172.16.1.51/SimplyIDo/Development/uploads/project_profile_images/1/thumb_sq-g-1x1.jpg"
                                                                 class="profile-image-small" alt="Fisrt Sido Lead">
                                                        </div>-->
                                                        <?php echo staff_profile_image($invitee['created_by']) ?>
                                                    </div>
                                                    <div class="lead-det">
                                                        <div class="eventname">
                                                            <strong><?php echo $invitee['invited_name']; ?></strong>
                                                            <div class="date"><?php echo _dt($invitee['datecreated']); ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <!--<td><?php /*echo _dt($invitee['datecreated']); */ ?></td>-->
                                            <td>
                                                <span class="inviteStatus <?php echo strtolower($invitee['status']); ?>">
                                                    <?php echo strtoupper($invitee['status']); ?>
                                                </span>
                                            </td>
                                            <td class="">
                                                <div class="text-right mright10"><a class='show_act'
                                                                                    href='javascript:void(0)'><i
                                                                class='fa fa-ellipsis-v' aria-hidden='true'></i></a>
                                                </div>
                                                <div class='table_actions'>
                                                    <ul>
                                                        <li>
                                                            <a href="<?php echo admin_url('invites/invitedetails/' . $invitee['inviteid']); ?>"
                                                               class="" title="View Dashboard"><i class="fa fa-eye"></i><span>View</span></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="6" align="center"><?php echo _l('no_invites_found'); ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
    $(function () {
        <?php if(is_mobile()){ ?>
        $('#DataTables_Table_0_filter, .leads-search').hide();
        $(".filter_btn_search").click(function () {
            $('#DataTables_Table_0_filter, .leads-search').toggle();
        });
        <?php } ?>
        invites_kanban();
    });
</script>

</body>
</html>
