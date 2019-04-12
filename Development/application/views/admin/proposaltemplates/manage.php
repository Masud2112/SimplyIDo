<?php init_head();
$plid = '';
$pllink = '';
$plttl = '';
if (isset($_GET['lid']) && $_GET['lid'] > 0) {
    $url = admin_url('proposaltemplates/proposal?lid=' . $_GET['lid']);
    $plid = $_GET['lid'];
    $pllink = 'leads';
    $plttl = 'Leads';
    $rel_link = '?lid=' . $_GET['lid'];
} elseif (isset($_GET['pid']) && $_GET['pid'] > 0) {

    $url = admin_url('proposaltemplates/proposal?pid=' . $_GET['pid']);
    $plid = $_GET['pid'];
    $pllink = 'projects';
    $plttl = 'Projects';
    $rel_link = '?pid=' . $_GET['pid'];
} else {
    $url = admin_url('proposaltemplates/proposal');
    $rel_link = '';
}
$session_data = get_session_data();
?>
<div id="wrapper">
    <div class="content proposaltemplates-manage">
        <div class="row">
            <div class="col-md-12">


                <div class="breadcrumb ">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php if (isset($plid) && $plid != "") { ?>
                        <a href="<?php echo admin_url($pllink); ?>"><?php echo $plttl; ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url($pllink . '/dashboard/' . $plid); ?>"><?php echo(isset($lname) ? $lname : ""); ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php if (isset($parent_id)&& $parent_id > 0) { ?>
                            <a href="<?php echo admin_url('projects/dashboard/') . $parent_id; ?>"><?php echo get_project_name_by_id($parent_id); ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } ?>


                    <?php } else { ?>
                        <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } ?>
                    <span class="breadcrumb-item active"><?php echo _l('proposals'); ?></span>
                </div>

                <h1 class="pageTitleH1"><i class="fa fa-file-text-o "></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
					<div class="filterBtnRow">
                        <div class="conven-tabs">
                            <?php //if ($switch_proposals_kanban != 1) { ?>
                                <a href="javascript:void(0)" class="contact-btn proposal_tab <?php echo $switch_proposals_kanban == 1?"list":"card"?> <?php echo isset($session_data['proposals_status_view']) && $session_data['proposals_status_view']=='active'?'active':''; ?>
<?php echo !isset($session_data['proposals_status_view'])?'active':''; ?>"
                                   data-status="active">
                                    <?php echo _l('Active'); ?>
                                    <span class="count_by_status"><?php echo count($proposals_active); ?></span>
                                </a>
                                <a href="javascript:void(0)" class="custom-btn proposal_tab <?php echo $switch_proposals_kanban == 1?"list":"card"?> <?php echo isset($session_data['proposals_status_view']) && $session_data['proposals_status_view']=='archived'?'active':''; ?>" data-status="archived" >
                                    <?php echo _l('Archived'); ?>
                                    <span class="count_by_status"><?php echo count($proposals_archived); ?></span>
                                </a>
                            <?php //} ?>
                            <input type="hidden" id="invite_status" value="<?php echo isset($session_data['proposals_status_view'])?$session_data['proposals_status_view']:'active'; ?>"/>
                            <?php if (has_permission('proposals', '', 'create')) { ?>
                                <a href="<?php echo $url; ?>" class="btn btn-primary  btn-new">
                                    <i class="fa fa-plus"></i><span><?php echo _l('new_proposaltemplate'); ?></span>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="pull-right">
                            <?php
                            $list = $card = "";
                            if (isset($switch_proposals_kanban) && $switch_proposals_kanban == 1) {
                                $list = "selected disabled";
                            } else {
                                $card = "selected disabled";
                            } ?>
                            <?php if (is_mobile()) {
                                echo '<a class="btn btn-primary  filter_btn_search"><i class="glyphicon glyphicon-search"></i></a>';
                            } ?>
                            <!--<a class="btn btn-primary filter_btn"><i class="fa fa-filter"></i></a>-->
                            <a href="<?php echo admin_url('proposaltemplates/switch_proposals_kanban/'); ?>"
                               class="btn btn-primary viewchangeBtn hidden-xs <?php echo $list ?>">
                                <?php echo _l('switch_to_list_view'); ?>
                            </a>
                            <a href="<?php echo admin_url('proposaltemplates/switch_proposals_kanban/1'); ?>"
                               class="btn btn-primary viewchangeBtn hidden-xs <?php echo $card ?>">
                                <?php echo _l('projects_switch_to_kanban'); ?>
                            </a>
                        </div>
                        <?php if ($switch_proposals_kanban != 1) { ?>
                            <div class="col-sm-4 col-xs-6  pull-right leads-search">
                                <div class="message_search text-right" data-toggle="tooltip" data-placement="bottom"
                                     data-title="Use # + tagname to search by tags">
                                    <span class="input-group-addon lead_serach_ico inline-block"><span
                                                class="glyphicon glyphicon-search"></span></span>
                                    <div class="lead_search_inner form-group inline-block no-margin"><input
                                                type="search" id="search" name="search" class="form-control"
                                                data-name="search" onkeyup="proposals_kanban();" placeholder="Search..."
                                                value=""></div>
                                </div>
                                <input type="hidden" name="sort_type" value="">
                                <input type="hidden" name="sort" value="">
                            </div>
                        <?php } ?>
						</div>
                        <div class="clearfix"></div>
                        <?php if ($this->session->has_userdata('proposals_kanban_view') && $this->session->userdata('proposals_kanban_view') == 'true') { ?>

                            <div class="active kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                                <div class="row">
                                    <div class="projects-kan-ban">
                                        <div id="kan-ban"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <?php
                            $column = array(
                                _l(''),
                                _l(''),
                                _l('proposaltemplates_dt_name'),
                                _l('name'),
                                _l('project'),
                                _l('status'),
                                _l('date'),
                                _l('')
                            );
                            if ($plid=="" ) {
                                $column[3]="";
                            }
                            render_datatable($column, 'proposaltemplates');
                        } ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->load->view('admin/proposaltemplates/proposal_duplicate');

?>
<!---- Close Proposal start --->

<div class="modal fade" id="close_proposal_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="close_form" action="<?php echo admin_url('proposaltemplates/close'.$rel_link) ?>"
                  method="post">
                <div class="modal-body">
                    <h2 class="text-center" id="myModalLabel">
                        <?php echo _l('close_proposal'); ?>
                    </h2>
                    <div class="confirm_comment mtop35">
                        <label>Please Let us know the reason to close proposal</label>
                        <textarea name="closereason" id="reason" rows="10" style="width: 100%"></textarea>
                    </div>
                    <input name="closedby" type="hidden" id="userid" value="<?php echo get_staff_user_id(); ?>">
                    <input name="proposalid" type="hidden" id="close_proposal_id" value="">
                    <input name="closedat" type="hidden" id="closedat" value="<?php echo date('Y-m-d H:i:s'); ?>">
                </div>
                <div class="modal-footer">
                    <!--<input type="submit" class="btn btn-info" value="Yes"/>-->
                    <button type="submit" class="btn btn-info">Yes</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">No</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="reopen_proposal_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="close_form" action="<?php echo admin_url('proposaltemplates/reopen'.$rel_link) ?>"
                  method="post">
                <div class="modal-body text-center">
                    <h2 class="text-center" id="myModalLabel">
                        <?php echo _l('reopen_proposal'); ?>
                    </h2>

                    <!--<input name="closedby" type="hidden" id="userid" value="<?php /*echo get_staff_user_id(); */?>">-->
                    <input name="proposalid" type="hidden" id="reopen_proposal_id" value="">
                    <button type="submit" class="btn btn-info">Yes</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">No</span>
                    </button>
                </div>
                <!--<div class="modal-footer">
                    <button type="submit" class="btn btn-info">Yes</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">No</span>
                    </button>
                </div>-->
            </form>
        </div>
    </div>
</div>
<!---- Close Proposal end --->
<?php init_tail(); ?>
<script>
    var notSortable = $('.table-proposaltemplates').find('th').length - 1;
    var url = window.location.href;
    var status = $('.proposal_tab.list.active').attr('data-status');
    /*if (status === undefined){
        status="active";
    }*/
    url = updateQueryStringParameter(url,'status',status);
    initDataTable('.table-proposaltemplates', url, [2], [1, notSortable],"undefined", [2, 'DESC']);
</script>

<script>

    function duplicate_proposal(invoker) {
        var id = $(invoker).data('id');
        $('#duplicate_record_id').val(id);
        $('#additional').append(hidden_input('duplicate_record_id', id));
    }

    $(function () {
        /*var validator = $("#close_form").validate({
            rules: {reason: {required: true}},
        });*/

        //initDataTable('.table-groups', window.location.href, [], [4], 'undefined', [0, 'ASC']);

        //$('div.dataTables_filter .input-group').append( '<div class="pull-right"><a href="#" class="btn-info display-block mleft5 padding-5" data-toggle="modal" data-target="#display_column" id="display_column_popup" style="padding: 5px 12px;border-radius: 3px;"><i class="fa fa-cog" aria-hidden="true"></i></a></div>' );
        $('div.dataTables_filter input').css('border-radius', '1');

        $('.brand_list_section').hide();
        $('.duplicate_by_brand').on('click', function () {
            if ($("#duplicate_by_existing_brand").prop('checked') == true) {
                $('.brand_list_section').show();
            } else {
                $('.brand_list_section').hide();
            }
        });
        proposals_kanban();
    });
</script>

</body>
</html>
