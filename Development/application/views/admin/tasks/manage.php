<?php init_head(); 
if (isset($eid)) {
	//$pid = $eid;
}

?>
<div id="wrapper">
    <div class="content manage-task-page">
        <div class="row">
            <div class="col-md-12">

                <?php /*if (isset($pg) && $pg == 'home') { */ ?>

                <div class="breadcrumb">
                    <?php /*if (isset($pg) && $pg == 'home') { */?>
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php /*} */?>
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
					<?php if(isset($parent_id) > 0) {?>							
							 <a href="<?php echo admin_url('projects/dashboard/').$parent_id; ?>"><?php echo get_project_name_by_id($parent_id); ?></a>
							 <i class="fa fa-angle-right breadcrumb-arrow"></i>
							<?php } ?>
                    <?php }elseif (isset($eid)) { ?>
                        <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php if(isset($parent_id) > 0) {?>
                            <a href="<?php echo admin_url('projects/dashboard/').$parent_id; ?>"><?php echo get_project_name_by_id($parent_id); ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } ?>
                        <a href="<?php echo admin_url('projects/dashboard/' . $eid); ?>"><?php echo ($lname); ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php }else{ ?>
                    <?php } ?>
                    <span>Tasks</span>
                </div>
                <h1 class="pageTitleH1"><i class="fa fa-tasks"></i><?php echo $title; ?></h1>
                <?php /*} */ ?>
                <div class="clearfix"></div>
                <div class="_buttons">
                    <?php $this->load->view('admin/tasks/_summary', $statuses); ?>
                </div>
                <div class="titleRow">
                    <h4><?php echo _l('tasks_summary'); ?></h4>
                    <div class="clearfix"></div>
                </div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="clearfix filterBtnRow">
                            <div class="_buttons inline-block datatable">
                                <?php if (!$this->session->has_userdata('tasks_kanban_view') || $this->session->userdata('tasks_kanban_view') == 'false') { ?>
                                    <a href="#" data-toggle="modal" data-target="#tasks_bulk_actions"
                                       class="bulk-actions-btn bulk_act_btn btn btn-info"
                                       data-table=".table-tasks"><?php echo _l('bulk_actions'); ?></a>
                                <?php } ?>
                                <?php if (has_permission('tasks', '', 'create')) { ?>
                                    <?php if (isset($lid) && $lid != "") { ?>
                                        <a href="<?php echo admin_url('tasks/task?lid=' . $lid); ?>"
                                           class="btn btn-info"><?php echo _l('new_task'); ?></a>
                                    <?php } else if (isset($pid) && $pid != "") { ?>
                                        <a href="<?php echo admin_url('tasks/task?pid=' . $pid); ?>"
                                           class="btn btn-info"><?php echo _l('new_task'); ?></a>
                                    <?php } else if (isset($eid) && $eid != "") { ?>
                                        <a href="<?php echo admin_url('tasks/task?eid=' . $eid); ?>"
                                           class="btn btn-info"><?php echo _l('new_task'); ?></a>
                                    <?php } else { ?>
                                        <a href="<?php echo admin_url('tasks/task'); ?>"
                                           class="btn btn-info"><?php echo _l('new_task'); ?></a>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="pull-right">
                                <?php
                                $list = $card = "";
                                if (isset($switch_kanban) && $switch_kanban == 1) {
                                    $card = "selected disabled";
                                } else {
                                    $list = "selected disabled";
                                }
                                ?>
								<?php if(is_mobile()) { echo '<a class="btn btn-primary  filter_btn_search"><i class="glyphicon glyphicon-search"></i></a>'; } ?>	
                                <a class="btn btn-primary filter_btn"><i class="fa fa-filter"></i></a>
                                <a href="<?php if (!$this->input->get('project_id')) {
                                    echo admin_url('tasks/switch_kanban/1');
                                } else {
                                    echo admin_url('projects/view/' . $this->input->get('project_id') . '?group=project_tasks');
                                }; ?>"
                                   class="btn btn-primary viewchangeBtn hidden-xs <?php echo $list ?>"><?php echo _l('switch_to_list_view'); ?>
                                </a>
                                <a href="<?php if (!$this->input->get('project_id')) {
                                    echo admin_url('tasks/switch_kanban/');
                                } else {
                                    echo admin_url('projects/view/' . $this->input->get('project_id') . '?group=project_tasks');
                                }; ?>"
                                   class="btn btn-primary viewchangeBtn hidden-xs <?php echo $card ?>"><?php echo _l('leads_switch_to_kanban'); ?>
                                </a>
                            </div>
                            <?php if ($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
                                <!--<div data-toggle="tooltip" class="col-md-2 col-xs-6 pull-right tasks-search"
                                     data-placement="bottom"
                                     data-title="<?php /*echo _l('search_by_tags'); */?>">
                                    <?php /*echo render_input('search', '', '', 'search', array('data-name' => 'search', 'onkeyup' => 'tasks_kanban();', 'placeholder' => _l('search_tasks')), array(), 'no-margin') */?>
                                </div>-->
                                <div class="lead_search pull-right text-right" data-toggle="tooltip" data-placement="bottom"
                                     data-title="<?php echo _l('search_by_tags'); ?>">
                                        <span class="input-group-addon lead_serach_ico inline-block"><span
                                                    class="glyphicon glyphicon-search"></span></span>
                                    <div class="lead_search_inner form-group inline-block no-margin"><input
                                                type="search" id="search" name="search" class="form-control"
                                                data-name="search" onkeyup="tasks_kanban();" placeholder="Search..."
                                                value=""></div>
                                </div>
                            <?php } else { ?>
                                <?php $this->load->view('admin/tasks/tasks_filter_by', array('view_table_name' => '.table-tasks')); ?>
                            <?php } ?>
                        </div>
                        <?php
                        if ($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
                            <div class="kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                                <div class="row">
                                    <div id="kanban-params">
                                        <?php echo form_hidden('project_id', $this->input->get('project_id')); ?>
                                        <?php echo form_hidden('lid', $this->input->get('lid')); ?>
                                        <?php echo form_hidden('pid', $this->input->get('pid')); ?>
                                        <?php echo form_hidden('eid', $this->input->get('eid')); ?>
                                    </div>
                                    <div class="container-fluid tasks-kan-ban">
                                        <div id="kan-ban"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <?php //$this->load->view('admin/tasks/_summary',array('table'=>'.table-tasks')); ?>
                            <div class="cardViewContainer">
                                <div id="tasks-table">
                                    <div class="tasks-filter-wrapper lead-filterRow">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h5 class="sub-head"><?php echo _l('filter_by'); ?></h5>
                                            </div>
                                            <?php //if(is_admin()){ ?>
                                            <div class="col-md-4">
                                                <?php
                                                echo render_select('view_status', $statuses, array('id', 'name'), '', '', array('data-width' => '100%', 'data-none-selected-text' => 'Status'));
                                                ?>
                                            </div>
                                            <div class="col-md-4">
                                                <!--<select id="view_taskdate" name="view_taskdate" class="selectpicker" data-width="100%" data-none-selected-text="Tasks" data-live-search="true" tabindex="-98">
                                                  <option value=""></option>
                                                  <option value="today">Todays Task</option>
                                                  <option value="duedate">Due Date Tasks</option>
                                                  <option value="upcoming">Upcoming Tasks</option>
                                                </select> -->
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="view_taskdate"
                                                           name="view_taskdate" placeholder="Select Due Date"/>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <select id="view_assigned" name="view_assigned" class="selectpicker"
                                                        data-width="100%" data-live-search="true"
                                                        data-none-selected-text="Assigned To">
                                                    <option value=""></option>
                                                    <?php foreach ($members as $as) { ?>
                                                        <option value="<?php echo $as['staffid']; ?>"><?php echo get_staff_full_name($as['staffid']); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <?php $this->load->view('admin/tasks/_table', array('bulk_actions' => true)); ?>
                                </div>
                            </div>
                            <?php $this->load->view('admin/tasks/_bulk_actions'); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css"/>
<script>
    taskid = '<?php echo $taskid; ?>';
    $(function () {
        tasks_kanban();
        $('input[name="view_taskdate"]').daterangepicker({
            locale: {
                format: 'MM/DD/YYYY'
            },
            clearBtn: true
        });
        /**
         * Added By : Purvi
         * Dt : 11/10/2017
         * to clear view taskdate filter on cancel button
         */
        $('#view_taskdate').on('cancel.daterangepicker', function (ev, picker) {
            //do something, like clearing an input
            $('#view_taskdate').val('');
            $('.table-tasks').DataTable().ajax.reload();
        });
    });
    function filterstatus(id) {
        $('select[name=view_status]').val(id);
        $('.selectpicker').selectpicker('refresh');
        $('.table-tasks').DataTable().ajax.reload();
    }
	
	 
	
	
</script>
</body>
</html>
