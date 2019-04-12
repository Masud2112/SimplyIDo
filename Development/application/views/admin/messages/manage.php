<?php init_head(); ?>
<div id="wrapper">
    <div class="content messages-list">
        <div class="row">
            <div class="col-md-12">


                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php if (isset($lid)) { ?>
                        <a href="<?php echo admin_url('leads/'); ?>">Leads</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('leads/dashboard/' . $lid); ?>"><?php echo ($lname); ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } elseif (isset($pid)) { ?>
                        <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('projects/dashboard/' . $pid); ?>"><?php echo ($lname); ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php }else{ ?>
                    <?php } ?>
                    <span>Messages</span>
                </div>
                <h1 class="pageTitleH1"><i class="fa fa-envelope-o"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="col-sm-12">
                            <div class="modal fade bulk_actions" id="leads_bulk_actions" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                                        </div>
                                        <div class="modal-body">
                                            <div class="radio">
                                                <input id="selected" type="radio" name="mark_read" value="selected" class="mark_read">
                                                <label for="selected">Mark selected all read</label>
                                            </div>
                                            <div class="radio">
                                                <input id="all" type="radio" name="mark_read" value="all" class="mark_read">
                                                <label for="all">Mark all read</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                                            <a href="#" class="btn btn-info" onclick="message_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                                        </div>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                        </div>

                        <div class="mail-inbox">
                            <div class="pull-left">
                                <a href="#" data-toggle="modal" data-table=".table-messages" data-target="#leads_bulk_actions" class="bulk-actions-btn bulk_act_btn btn btn-info"><?php echo _l('bulk_actions'); ?></a>
                                <?php if (has_permission('messages','','create')) { ?>
                                    <?php if (isset($lid)) { ?>
                                        <a href="<?php echo admin_url('messages/message?lid=' . $lid); ?>"
                                           class="btn btn-info"><?php echo _l('new_message'); ?></a>
                                    <?php } else if (isset($pid)) { ?>
                                        <a href="<?php echo admin_url('messages/message?pid=' . $pid); ?>"
                                           class="btn btn-info "><?php echo _l('new_message'); ?></a>
                                        <div class="clearfix"></div>
                                    <?php } else if (isset($eid)) { ?>
                                        <a href="<?php echo admin_url('messages/message?eid=' . $eid); ?>"
                                           class="btn btn-info"><?php echo _l('new_message'); ?></a>
                                    <?php } else { ?>
                                        <a href="<?php echo admin_url('messages/message'); ?>"
                                           class="btn btn-info "><?php echo _l('new_message'); ?></a>
                                    <?php } ?>

                                <?php } ?>
                            </div>
                            <div class="pull-right">
                                <?php
                                $list=$card="";
                                if(isset($switch_messages_kanban) && $switch_messages_kanban==1){
                                    $list="selected disabled";
                                }else{
                                    $card="selected disabled";
                                }?>
								<?php if(is_mobile()) { echo '<a class="btn btn-primary  filter_btn_search"><i class="glyphicon glyphicon-search"></i></a>'; } ?>
                                <!--<a class="btn btn-primary filter_btn"><i class="fa fa-filter"></i></a>-->
                                <a href="<?php echo admin_url('messages/switch_messages_kanban/'); ?>"
                                   class="btn btn-primary viewchangeBtn hidden-xs <?php echo $list ?>">
                                    <?php echo _l('switch_to_list_view'); ?>
                                </a>
                                <a href="<?php echo admin_url('messages/switch_messages_kanban/1'); ?>"
                                   class="btn btn-primary viewchangeBtn hidden-xs <?php echo $card ?>">
                                    <?php echo _l('projects_switch_to_kanban'); ?>
                                </a>
                            </div>
                            <?php if($switch_messages_kanban != 1){?>
                                <div class="col-sm-4 col-xs-6  pull-right leads-search">
                                    <div class="message_search text-right" data-toggle="tooltip" data-placement="bottom" data-title="Use # + tagname to search by tags">
                                        <span class="input-group-addon lead_serach_ico inline-block"><span class="glyphicon glyphicon-search"></span></span>
                                        <div class="lead_search_inner form-group inline-block no-margin"><input type="search" id="search" name="search" class="form-control" data-name="search" onkeyup="messages_kanban();" placeholder="Search..." value=""></div>
                                    </div>
                                    <input type="hidden" name="sort_type" value="">
                                    <input type="hidden" name="sort" value="">
                                </div>
                            <?php } ?>
                            <div class="clearfix"></div>
                            <?php if ($this->session->has_userdata('messages_kanban_view') && $this->session->userdata('messages_kanban_view') == 'true') { ?>
                                <div class="active kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                                    <div class="row">
                                        <div class="projects-kan-ban">
                                            <div id="kan-ban"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <table class="mail-list table-messages table sdtheme dataTable no-footer dtr-inline collapsed"
                                       data-order-col="3" data-order-type="desc">
                                    <thead class="">
                                    <tr>
                                        <th class="sorting_disabled not-export" rowspan="1" colspan="1" aria-label=" - ">
                                            <span class="hide"> - </span>
                                            <div class="checkbox mass_select_all_wrap">
                                                <input type="checkbox"
                                                       id="mass_select_all"
                                                       data-to-table="messages"><label></label>
                                            </div>
                                        </th>
                                        <th></th>
                                        <th><?php echo _l('message_from') ?></th>
                                        <th><?php echo _l('message_subject') ?></th>
                                        <th><?php echo _l('message_tags') ?></th>
                                        <th class="hide"><?php echo _l('message_date') ?></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php
                                    foreach ($messages as $message) {
                                        $readclass = "";
                                        if ($message['isread'] == 0) {
                                            $readclass = "unread";
                                        } else {
                                            $readclass = "";
                                        }
                                        ?>
                                        <tr class="<?php echo $readclass; ?>">
                                            <td>
                                                <div class="checkbox">
                                                    <input type="checkbox" value="<?php echo $message['id']; ?>">
                                                    <label></label>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($message['pinned'] > 0) { ?>
                                                    <i class="fa fa-fw fa-thumb-tack message-pin pinned"
                                                       title="Unpin from dashboard" id="<?php echo $message['id']; ?>"
                                                       message_id="<?php echo $message['id']; ?>"></i>
                                                <?php } else { ?>
                                                    <i class="fa fa-fw fa-thumb-tack message-pin" title="Pin to dashboard"
                                                       id="<?php echo $message['id']; ?>"
                                                       message_id="<?php echo $message['id']; ?>"></i>
                                                <?php } ?>
                                            </td>
                                            <td width="20%">
                                                <?php
                                                if($message['created_by_type']=="teammember"){
                                                    echo staff_profile_image($message['created_by'], array(
                                                        'staff-profile-image-small',
                                                        'mright10'
                                                    ));
                                                    echo get_staff_full_name($message['created_by']);
                                                }else{
                                                    echo addressbook_profile_image($message['created_by'], array(
                                                        'staff-profile-image-small',
                                                        'mright10'
                                                    ));
                                                    echo get_addressbook_full_name($message['created_by']);
                                                }

                                                ?>
                                            </td>
                                            <td class="mail-list-message">
                                                <?php if (isset($lid)) { ?>
                                                    <a href="<?php echo admin_url('messages/view/' . $message['id'] . '?lid=' . $lid); ?>"><b><?php echo ($message['created_by_check_type'] == "child") ? "RE: " . $message['subject'] : $message['subject']; ?></b></a>
                                                <?php } else if (isset($pid)) { ?>
                                                    <a href="<?php echo admin_url('messages/view/' . $message['id'] . '?pid=' . $pid); ?>"><b><?php echo ($message['created_by_check_type'] == "child") ? "RE: " . $message['subject'] : $message['subject']; ?></b></a>
                                                <?php } else if (isset($eid)) { ?>
                                                    <a href="<?php echo admin_url('messages/view/' . $message['id'] . '?eid=' . $eid); ?>"><b><?php echo ($message['created_by_check_type'] == "child") ? "RE: " . $message['subject'] : $message['subject']; ?></b></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo admin_url('messages/view/' . $message['id']); ?>"><b><?php echo ($message['created_by_check_type'] == "child") ? "RE: " . $message['subject'] : $message['subject']; ?></b></a>
                                                <?php } ?>
                                            </td>
                                            <td class="text-left">
                                                <?php echo render_tags($message['tags']) ?>
                                            </td>
                                            <td class="mail-list-time hide"><?php echo($message['created_date']); ?></td>
                                            <td class="mail-list-time"><?php echo time_ago($message['created_date']); ?></td>
                                            <td>

                                                <?php if ($message['isread'] == 0) { ?>
                                                    <a class="isread" href="javascript:void(0)"
                                                       messageid="<?php echo $message['id']; ?>"><i
                                                                class="fa fa-check pull-right" data-toggle="tooltip"
                                                                data-title="Mark as read"></i></a>
                                                <?php } ?>
                                                <?php if ($message['chilemessages'] > 0) { ?>
                                                    <span data-toggle="tooltip"
                                                          data-title="<?php echo $message['chilemessages']; ?> comment(s)"
                                                          class="badge badge-success pull-right mright5 mtop2"><?php echo $message['chilemessages']; ?></span>
                                                <?php } ?>
                                                <?php if ($message['attachments'] > 0) { ?>
                                                    <i class="fa fa-paperclip pull-left" data-toggle="tooltip"
                                                       data-title="<?php echo $message['attachments']; ?> Attachment(s)"></i>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
    $(function () {		
        messages_kanban();
    });
</script>

</body>
</html>