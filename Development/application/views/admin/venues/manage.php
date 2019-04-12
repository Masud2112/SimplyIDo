<?php
/**
 * Added By: Vaidehi
 * Dt: 02/13/2018
 * Venue Module
 */
init_head();
?>
<div id="wrapper">
    <div class="content venues">
        <div class="row">
            <div class="col-md-12">


                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php if (isset($lid)) { ?>
                        <a href="<?php echo admin_url('leads/'); ?>">Leads</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('leads/dashboard/' . $lid); ?>"><?php echo($lname); ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('addressbooks/' . $lid); ?>"><?php echo "Contacts"; ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } elseif (isset($pid)) { ?>
                        <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('projects/dashboard/' . $pid); ?>"><?php echo($lname); ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('addressbooks/' . $pid); ?>"><?php echo "Contacts"; ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } else { ?>
                       
                    <?php } ?>
                    <span>Venues</span>
                </div>

                <h1 class="pageTitleH1"><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="conven-tabs">
                            <?php if (has_permission('addressbook', '', 'create')) {
                                $rel_link = "";
                                if (isset($lid)) {
                                    $rel_link = '?lid=' . $lid;
                                } else if (isset($pid)) {
                                    $rel_link = '?pid=' . $pid;
                                } else if (isset($eid)) {
                                    $rel_link = '?eid=' . $eid;
                                } ?>
                                <a class="contact-btn" href="<?php echo admin_url('addressbooks'.$rel_link); ?>"><?php echo _l('Contacts'); ?></a>
                                <a href="javascript:void(0)"
                                   class="venue-btn active"><?php echo _l('venues'); ?></a>
                                <a href="<?php echo admin_url('venues/venue' . $rel_link); ?>"
                                   class="btn btn-primary btn-new"><?php echo _l('new_venue'); ?></a>
                                <!--<a href="javascript:void(0)" class="btn btn-info" data-toggle="modal" data-target="#existing_modal"><?php /*echo _l('choose_existing_venue'); */?></a>-->
                                <div class="clearfix"></div>
                            <?php } ?>
                        </div>
                        <div class="pull-right">
                            <?php
                            $list = $card = "";
                            if (isset($switch_venues_kanban) && $switch_venues_kanban == 1) {
                                $list = "selected disabled";
                            } else {
                                $card = "selected disabled";
                            } ?>
                            <!--<a class="btn btn-primary filter_btn"><i class="fa fa-filter"></i></a>-->
							
							  <?php if(is_mobile()) { echo '<a class="btn btn-primary  filter_btn_search"><i class="glyphicon glyphicon-search"></i></a>'; } ?>
                            <a href="<?php echo admin_url('venues/switch_venues_kanban/'); ?>"
                               class="btn btn-primary viewchangeBtn hidden-xs <?php echo $list ?>">
                                <?php echo _l('switch_to_list_view'); ?>
                            </a>
                            <a href="<?php echo admin_url('venues/switch_venues_kanban/1'); ?>"
                               class="btn btn-primary viewchangeBtn hidden-xs <?php echo $card ?>">
                                <?php echo _l('projects_switch_to_kanban'); ?>
                            </a>
                        </div>
                        <?php if ($switch_venues_kanban != 1) { ?>
                            <div class="col-sm-4 pull-right leads-search">
                                <div class="message_search text-right" data-toggle="tooltip" data-placement="bottom"
                                     data-title="Use # + tagname to search by tags">
                                    <span class="input-group-addon lead_serach_ico inline-block"><span
                                                class="glyphicon glyphicon-search"></span></span>
                                    <div class="lead_search_inner form-group inline-block no-margin">
                                        <input type="search" id="search" name="search" class="form-control"
                                               data-name="search" onkeyup="venues_kanban();" placeholder="Search..."
                                               value="">
                                    </div>
                                </div>
                                <input type="hidden" name="sort_type" value="">
                                <input type="hidden" name="sort" value="">
                            </div>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <?php if ($this->session->has_userdata('venues_kanban_view') && $this->session->userdata('venues_kanban_view') == 'true') { ?>
                            <div class="active kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                                <div class="row">
                                    <div class="contacts-kan-ban">
                                        <div id="kan-ban"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else {
                            render_datatable(array(
                                _l(''),
                                _l(''),
                                _l('name'),
                                _l('phone'),
                                _l('email'),
                                _l('tags'),
                                _l('')
                            ), 'venue');
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="existing_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('choose_existing_venue'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/venues/choose_existing_venue', array('id' => 'existing_venue_form')); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        echo render_select('venues', $venues, array('venueid', 'venuename'), 'venue_add_edit');
                        ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="hdnlid" value="<?php echo isset($lid) ? $lid : ''; ?>">
                <input type="hidden" name="hdnpid" value="<?php echo isset($pid) ? $pid : ''; ?>">
                <input type="hidden" name="hdneid" value="<?php echo isset($eid) ? $eid : ''; ?>">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    var notSortable = $('.table-venue').find('th').length - 1;
    initDataTable('.table-venue', window.location.href, [0, 1, notSortable], [notSortable, 0, 1], '', [2, "asc"]);
    $(function () {
        _validate_form($('#existing_venue_form'), {venues: {required: true}});

        venues_kanban();
    });
</script>
</body>
</html>