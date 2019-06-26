<?php init_head();
if (isset($eid)) {
    //$pid = $eid;
}
?>
<div id="wrapper">
    <div class="content manage-meetings-page">
        <div class="row">
            <div class="col-md-12">
                <div class="pull-right">
                    <div class="breadcrumb">
                        <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php if (isset($lid)) { ?>
                            <a href="<?php echo admin_url('leads/'); ?>">Leads</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('leads/dashboard/' . $lid); ?>"><?php echo($lname); ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } elseif (isset($pid)) { ?>
                            <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <?php if (isset($parent_id) && $parent_id > 0) { ?>
                                <a href="<?php echo admin_url('projects/dashboard/') . $parent_id; ?>"><?php echo get_project_name_by_id($parent_id); ?></a>
                                <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <?php } ?>
                            <a href="<?php echo admin_url('projects/dashboard/' . $pid); ?>"><?php echo($lname); ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } elseif (isset($eid)) { ?>
                            <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <?php if (isset($parent_id) && $parent_id > 0) { ?>
                                <a href="<?php echo admin_url('projects/dashboard/') . $parent_id; ?>"><?php echo get_project_name_by_id($parent_id); ?></a>
                                <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <?php } ?>
                            <a href="<?php echo admin_url('projects/dashboard/' . $eid); ?>"><?php echo($lname); ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } else { ?>
                        <?php } ?>
                        <span>Meetings</span>
                    </div>
                </div>
                <h1 class="pageTitleH1"><i class="fa fa-handshake-o"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>

                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="clearfix filterBtnRow">
                            <!-- <a class="btn bulk_act_btn btn-info" data-toggle="modal" data-target="#meeting_bulk_actions" tabindex="0" aria-controls="DataTables_Table_0" href="javascript:void(0);" ><span>Bulk Actions</span></a> -->
                            <?php if (has_permission('meetings', '', 'create')) { ?>
                                <?php if (isset($lid)) { ?>
                                    <a href="<?php echo admin_url('meetings/meeting?lid=' . $lid); ?>"
                                       class="btn btn-info "><?php echo _l('new_meeting'); ?></a>
                                <?php } elseif (isset($pid)) { ?>
                                    <a href="<?php echo admin_url('meetings/meeting?pid=' . $pid); ?>"
                                       class="btn btn-info "><?php echo _l('new_meeting'); ?></a>
                                <?php } elseif (isset($eid)) { ?>
                                    <a href="<?php echo admin_url('meetings/meeting?eid=' . $eid); ?>"
                                       class="btn btn-info "><?php echo _l('new_meeting'); ?></a>
                                <?php } else { ?>
                                    <a href="<?php echo admin_url('meetings/meeting'); ?>"
                                       class="btn btn-info "><?php echo _l('new_meeting'); ?></a>
                                <?php }
                            } ?>
                            <div class="pull-right">
                                <?php
                                $list = $card = "";
                                if (isset($switch_meetings_kanban) && $switch_meetings_kanban == 1) {
                                    $list = "selected disabled";
                                } else {
                                    $card = "selected disabled";
                                } ?>
                                <?php if (is_mobile()) {
                                    echo '<a class="btn btn-primary  filter_btn_search"><i class="glyphicon glyphicon-search"></i></a>';
                                } ?>
                                <a class="btn btn-primary filter_btn"><i class="fa fa-filter"></i></a>
                                <a href="<?php echo admin_url('meetings/switch_meetings_kanban/'); ?>"
                                   class="btn btn-primary viewchangeBtn hidden-xs <?php echo $list ?>">
                                    <?php echo _l('switch_to_list_view'); ?>
                                </a>
                                <a href="<?php echo admin_url('meetings/switch_meetings_kanban/1'); ?>"
                                   class="btn btn-primary viewchangeBtn hidden-xs <?php echo $card ?>">
                                    <?php echo _l('projects_switch_to_kanban'); ?>
                                </a>
                            </div>
                            <?php if ($switch_meetings_kanban != 1) { ?>
                                <div class="col-sm-4 col-xs-6  pull-right leads-search">
                                    <div class="message_search text-right" data-toggle="tooltip" data-placement="bottom"
                                         data-title="Use # + tagname to search by tags">
                                    <span class="input-group-addon lead_serach_ico inline-block"><span
                                                class="glyphicon glyphicon-search"></span></span>
                                        <div class="lead_search_inner form-group inline-block no-margin"><input
                                                    type="search" id="search" name="search" class="form-control"
                                                    data-name="search" onkeyup="meetings_kanban();"
                                                    placeholder="Search..."
                                                    value=""></div>
                                    </div>
                                    <input type="hidden" name="sort_type" value="">
                                    <input type="hidden" name="sort" value="">
                                </div>
                            <?php } ?>
                        </div>
                        <div class="modal fade bulk_actions" id="meeting_bulk_actions" tabindex="-1"
                             role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close"><span
                                                    aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div id="bulk_change">
                                            <?php //if (is_admin()) { ?>
                                            <div class="checkbox checkbox-danger">
                                                <input type="checkbox" name="meeting_mass_delete"
                                                       id="meeting_mass_delete">
                                                <label for="meeting_mass_delete"><?php echo _l('mass_delete'); ?></label>
                                            </div>
                                            <?php //} ?>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default"
                                                data-dismiss="modal"><?php echo _l('close'); ?></button>
                                        <a href="#" class="btn btn-info"
                                           onclick="meeting_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>
                            <!-- /.modal-dialog -->
                        </div>
                        <div class="clearfix"></div>
                        <?php if ($this->session->has_userdata('meetings_kanban_view') && $this->session->userdata('meetings_kanban_view') == 'true') { ?>

                            <div class="active kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                                <div class="row">
                                    <div class="projects-kan-ban">
                                        <div id="kan-ban"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <?php render_datatable(array(
                                _l(''),
                                _l(''),
                                _l('meeting'),
                                _l('Lead / Project'),
                                '<i class=" fa fa-bell-o"></i>',
                                _l('meetings_dt_status'),
                                _l('Assigned'),
                                _l('')
                            ), 'meetings'); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    var notSortable = $('.table-meetings').find('th').length - 1;
    initDataTable('.table-meetings', window.location.href, [1], notSortable);

    $(function () {
        meetings_kanban();
    });
</script>
</body>
</html>
