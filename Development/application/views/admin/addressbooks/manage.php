<?php
/**
 * Added By: Avni
 * Dt: 10/11/2017
 * Address Book Module
 */
init_head();
if (isset($eid)) {
    $pid = $eid;
}
?>
<div id="wrapper">
    <div class="content manage-addressbook-page">
        <div class="row">
            <div class="col-md-12">

                <?php /*if(isset($pg) && $pg == 'home') { */ ?>
                <div class="breadcrumb">
                    <?php /*if (isset($pg) && $pg == 'home') { */ ?>
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php /*} */ ?>
                    <?php if (isset($lid)) { ?>
                        <a href="<?php echo admin_url('leads/'); ?>">Leads</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('leads/dashboard/' . $lid); ?>"><?php echo($lname); ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <span>Lead Contacts</span>
                    <?php } elseif (isset($pid)) { ?>
                        <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('projects/dashboard/' . $pid); ?>"><?php echo($lname); ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>

                        <?php if (isset($parent_id) && $parent_id > 0) { ?>
                            <a href="<?php echo admin_url('projects/dashboard/') . $parent_id; ?>"><?php echo get_project_name_by_id($parent_id); ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } ?>
                        <span>Project Contacts</span>
                    <?php } else { ?>
                        <span>Contacts</span>
                    <?php } ?>
                </div>
                <h1 class="pageTitleH1"><i class="fa fa-address-book-o"></i><?php echo $title; ?></h1>

                <?php /*} */ ?>
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
                                <a href="javascript:void(0)"
                                   class="contact-btn active"><?php echo _l('contacts'); ?></a>
                                <a href="<?php echo admin_url('venues' . $rel_link); ?>"
                                   class="venue-btn"><?php echo _l('venues'); ?></a>
                                <a href="<?php echo admin_url('addressbooks/addressbook' . $rel_link); ?>"
                                   class="btn btn-primary  btn-new"><?php echo _l('new_contact'); ?></a>
                                <!--<a href="javascript:void(0)" class="btn btn-info"
                                   data-toggle="modal"
                                   data-target="#existing_modal"><?php /*echo _l('choose_existing_client'); */ ?></a>-->
                                <?php
                                if ((isset($lid) || isset($pid)) && count($clients) > 0) { ?>
                                    <select class="selectpicker existingcontact" data-live-search="true">
                                        <option value=""><?php echo _l('choose_existing_client'); ?></option>
                                        <?php foreach ($clients as $client) { ?>
                                            <option value="<?php echo $client['addressbookid'] ?>"
                                                    data-subtext="<?php echo $client['email'] ?>"
                                                    data-name="<?php echo $client['firstname'] . " " . $client['lastname'] ?>">
                                                <?php echo $client['firstname'] . " " . $client['lastname'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>
                            <?php } ?>
                        </div>

                        <div class="pull-right">
                            <?php
                            $list = $card = "";
                            if (isset($switch_contacts_kanban) && $switch_contacts_kanban == 1) {
                                $list = "selected disabled";
                            } else {
                                $card = "selected disabled";
                            } ?>
                            <?php if (is_mobile()) {
                                echo '<a class="btn btn-primary  filter_btn_search"><i class="glyphicon glyphicon-search"></i></a>';
                            } ?>
                            <!--<a class="btn btn-primary filter_btn"><i class="fa fa-filter"></i></a>-->
                            <a href="<?php echo admin_url('addressbooks/switch_contacts_kanban/'); ?>"
                               class="btn btn-primary viewchangeBtn hidden-xs <?php echo $list ?>">
                                <?php echo _l('switch_to_list_view'); ?>
                            </a>
                            <a href="<?php echo admin_url('addressbooks/switch_contacts_kanban/1'); ?>"
                               class="btn btn-primary viewchangeBtn hidden-xs <?php echo $card ?>">
                                <?php echo _l('projects_switch_to_kanban'); ?>
                            </a>
                        </div>
                        <?php if ($switch_contacts_kanban != 1) { ?>
                            <div class="col-sm-4 col-xs-6 pull-right leads-search">
                                <div class="message_search text-right" data-toggle="tooltip" data-placement="bottom"
                                     data-title="Use # + tagname to search by tags">
                                    <span class="input-group-addon lead_serach_ico inline-block"><span
                                                class="glyphicon glyphicon-search"></span></span>
                                    <div class="lead_search_inner form-group inline-block no-margin"><input
                                                type="search" id="search" name="search" class="form-control"
                                                data-name="search" onkeyup="contacts_kanban();" placeholder="Search..."
                                                value=""></div>
                                </div>
                                <input type="hidden" name="sort_type" value="">
                                <input type="hidden" name="sort" value="">
                            </div>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <?php if ($this->session->has_userdata('contacts_kanban_view') && $this->session->userdata('contacts_kanban_view') == 'true') { ?>
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
                                _l('addressbook_dt_name'),
                                _l('addressbook_dt_phone'),
                                _l('addressbook_dt_email'),
                                _l('addressbook_dt_tags'),
                                _l('')
                            ), 'addressbook');
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade existingLeadContact_modal" id="existing_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <!--<div class="modal-header">

            </div>-->

            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h2 class="text-center p20" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('confirm'); ?></span>
                </h2>
                <h3 class="text-center m" id="myModalLabel">
                    Are you sure to add <strong><span id="existingcontactname" class=""></span></strong> as
                    your <?php echo isset($lid) ? "Lead" : 'Project'; ?> contact
                </h3>
                <?php echo form_open('admin/addressbooks/choose_existing_contact', array('id' => 'existing_contact_form')); ?>
                <div class="row">
                    <div class="col-md-12">
                        <input id="existcontactid" type="hidden" name="clients" value=0/>
                    </div>
                </div>
                <input type="hidden" name="hdnlid" value="<?php echo isset($lid) ? $lid : ''; ?>">
                <input type="hidden" name="hdnpid" value="<?php echo isset($pid) ? $pid : ''; ?>">
                <input type="hidden" name="hdneid" value="<?php echo isset($eid) ? $eid : ''; ?>">
                <div class="text-center">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo _l('cancel'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('Yes'); ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    var notSortable = $('.table-addressbook').find('th').length - 1;
    initDataTable('.table-addressbook', window.location.href, [0, 1], [notSortable, 0, 1], '', [2, "asc"]);
    $(function () {

        <?php if(is_mobile()){ ?>
        $('#DataTables_Table_0_filter, .leads-search').hide();
        $(".filter_btn_search").click(function () {
            $('#DataTables_Table_0_filter, .leads-search').toggle();
        });
        <?php } ?>

        _validate_form($('#existing_contact_form'), {clients: {required: true}});

        contacts_kanban();
    });
</script>
</body>
</html>