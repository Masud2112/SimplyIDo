<?php init_head(); ?>
<div id="wrapper">
    <div class="content manage-projects-page">
        <div class="row">
            <div class="col-md-12">
                
                <?php /*if (isset($pg) && $pg == 'home') { */?>
               
				<div class="breadcrumb">
					<a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
					<i class="fa fa-angle-right breadcrumb-arrow"></i>
					<span><?php echo $title; ?></span>
				</div>
                
				<h1 class="pageTitleH1"><i class="fa fa-book"></i><?php echo $title; ?></h1>
                <?php /*} */?>
                <div class="clearfix"></div>
                <div class="_buttons">
                     
                    <!--<hr class="hr-panel-heading"/>-->
                    <div class="row hide projects-overview">
                        <?php
                        $where_not_admin = '(addedfrom = ' . get_staff_user_id() . ' OR assigned=' . get_staff_user_id() . ')';
                        $numStatuses = count($statuses);
                        $is_admin = is_admin();
                        if ($numStatuses > 0) {
                            $processwidth = 100 / $numStatuses;
                            foreach ($statuses as $status) {
                                ?>
                                <div class="process-step" style="width:<?php echo $processwidth . '%'; ?>">
                                    <?php
                                    /*$this->db->where('status', $status['id']);
                                    $this->db->where('deleted = ', 0);
                                    $this->db->where('parent = ', 0);
                                    if (!$is_admin) {
                                        $this->db->where($where_not_admin);
                                    }
                                    $total = $this->db->count_all_results('tblprojects');*/
                                    $total = get_projects_by_status($status['id']);
                                    ?>
                                    <a href="javascript:void(0)"
                                       onclick="filterstatus('<?php echo $status['id']; ?>'); return false;">
                                        <h3 class="bold brdcolor"
                                            style="border-color:<?php echo $status['color']; ?>"><?php echo $total; ?></h3>
                                        <span style="color:<?php echo $status['color']; ?>"><?php echo $status['name']; ?></span>
                                    </a>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        <?php
                        if (!$is_admin) {
                            $this->db->where($where_not_admin);
                        }
                        $total_projects = $this->db->count_all_results('tblprojects');
                        ?>
                        <?php if ($is_admin) { ?>
                            <div class="col-md-2 col-xs-6">
                                <?php
                                $this->db->where('lost', 1);
                                if (!$is_admin) {
                                    $this->db->where($where_not_admin);
                                }
                                $total_lost = $this->db->count_all_results('tblprojects');
                                $percent_lost = ($total_projects > 0 ? number_format(($total_lost * 100) / $total_projects, 2) : 0);
                                ?>
                                <h3 class="bold"><?php echo $percent_lost; ?>%</h3>
                                <span class="text-danger"><?php echo _l('lost_projects'); ?></span>
                            </div>
                            <div class="col-md-2 col-xs-6">
                                <?php
                                $this->db->where('junk', 1);
                                if (!$is_admin) {
                                    $this->db->where($where_not_admin);
                                }
                                $total_junk = $this->db->count_all_results('tblprojects');
                                $percent_junk = ($total_projects > 0 ? number_format(($total_junk * 100) / $total_projects, 2) : 0);
                                ?>
                                <h3 class="bold"><?php echo $percent_junk; ?>%</h3>
                                <span class="text-danger"><?php echo _l('junk_projects'); ?></span>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="titleRow">
                    <h4><?php echo _l('projects_summary'); ?></h4>

                    <div class="clearfix"></div>
                </div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">


                        <div class="clearfix filterBtnRow">
						<?php if (has_permission('projects', '', 'create')) { ?>
                            <a href="<?php echo admin_url('projects/project'); ?>"
                               class="btn btn-info ">
                                <?php echo _l('new_project'); ?>
                            </a>
                        <?php } ?>
                        
                        <?php if (is_admin()) { ?>
                            <a href="<?php echo admin_url('projects/import'); ?>"
                               class="btn btn-info display-block hidden-xs">
                                <?php echo _l('import_projects'); ?>
                            </a>
                        <?php } ?>
                           <div class="pull-right">
                                <?php
                                $list=$card="";
                                if(isset($switch_projects_kanban)&&$switch_projects_kanban==1){
                                    $list="selected disabled";
                                }else{
                                    $card="selected disabled";
                                }?>
							   
							   <?php if(is_mobile()) { echo '<a class="btn btn-primary  filter_btn_search"><i class="glyphicon glyphicon-search"></i></a>'; } ?>
                                <a class="btn btn-primary filter_btn"><i class="fa fa-filter"></i></a>
                                <a href="<?php echo admin_url('projects/switch_projects_kanban/'); ?>"
                                   class="btn btn-primary viewchangeBtn hidden-xs <?php echo $list ?>">
                                    <?php echo _l('switch_to_list_view'); ?>
                                </a>
                                <a href="<?php echo admin_url('projects/switch_projects_kanban/1'); ?>"
                                   class="btn btn-primary viewchangeBtn hidden-xs <?php echo $card ?>">
                                    <?php echo _l('projects_switch_to_kanban'); ?>
                                </a>
                            </div>
                            <div class="col-sm-4 col-xs-12 pull-right projects-search">
                                <?php if ($this->session->userdata('projects_kanban_view') == 'true') { ?>
                                    <!--<div data-toggle="tooltip" data-placement="bottom"
                                         data-title="<?php /*echo _l('search_by_tags'); */?>">
                                        <?php /*echo render_input('search', '', '', 'search', array('data-name' => 'search', 'onkeyup' => 'projects_kanban();', 'placeholder' => _l('projects_search')), array(), 'no-margin') */?>
                                    </div>-->
                                    <div class="lead_search text-right" data-toggle="tooltip" data-placement="bottom"
                                         data-title="<?php echo _l('search_by_tags'); ?>">
                                        <span class="input-group-addon lead_serach_ico inline-block"><span class="glyphicon glyphicon-search"></span></span>
                                        <div class="lead_search_inner form-group inline-block no-margin"><input type="search" id="search" name="search" class="form-control" data-name="search" onkeyup="projects_kanban();" placeholder="Search..." value=""></div>
                                    </div>
                                <?php } ?>
                                <?php echo form_hidden('sort_type'); ?>
                                <?php echo form_hidden('sort'); ?>
                            </div>
                        </div>
						<div class="clearfix"></div>
                        <div>
                            <?php if ($this->session->has_userdata('projects_kanban_view') && $this->session->userdata('projects_kanban_view') == 'true') { ?>
                                <div class="active kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                                    <div class="row">
                                        <div class="container-fluid projects-kan-ban">
                                            <div id="kan-ban"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row" id="projects-table">
                                    <div class="col-md-12 lead-filterRow">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h5 class="sub-head"><?php echo _l('filter_by'); ?></h5>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <?php
                                                    echo render_select('view_project_status', $statuses, array('id', 'name'), '', '', array('data-width' => '100%', 'data-none-selected-text' => _l('projects_dt_status')));
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="view_projects_eventdate"
                                                           name="view_projects_eventdate"
                                                           placeholder="Select Event Dates"/>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <?php
                                                    echo render_select('view_project_eventtype', $eventtypes, array('eventtypeid', 'eventtypename'), '', '', array('data-width' => '100%', 'data-none-selected-text' => _l('projects_dt_type')));
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <?php echo render_select('view_project_assigned', $staff, array('staffid', array('firstname', 'lastname')), '', '', array('data-width' => '100%', 'data-none-selected-text' => _l('projects_dt_assigned'))); ?>
                                                </div>
                                            </div>
                                            <?php if (is_admin()) { ?>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select name="project_custom_view"
                                                                title="<?php echo _l('additional_filters'); ?>"
                                                                id="project_custom_view" class="selectpicker"
                                                                data-width="100%">
                                                            <option value=""></option>
                                                            <option value="lost"><?php echo _l('lead_lost'); ?></option>
                                                            <option value="junk"><?php echo _l('lead_junk'); ?></option>
                                                            <!--<option value="public"><?php //echo _l('lead_public'); ?></option>-->
                                                            <option value="contacted_today"><?php echo _l('lead_add_edit_contacted_today'); ?></option>
                                                            <option value="created_today"><?php echo _l('created_today'); ?></option>
                                                            <?php if (is_admin()) { ?>
                                                                <option value="not_assigned"><?php echo _l('leads_not_assigned'); ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <!--<hr class="hr-panel-heading"/>-->
                                    <div class="col-md-12">
                                        <!-- <a href="#" data-toggle="modal" data-table=".table-projects" data-target="#projects_bulk_actions" class="hide bulk-actions-btn"><?php //echo _l('bulk_actions'); ?></a> -->
                                        <div class="modal fade bulk_actions" id="projects_bulk_actions" tabindex="-1"
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
                                                        <?php if (is_admin()) { ?>
                                                            <div class="checkbox checkbox-danger">
                                                                <input type="checkbox" name="mass_delete"
                                                                       id="mass_delete">
                                                                <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                                                            </div>
                                                            <hr class="mass_delete_separator"/>
                                                        <?php } ?>
                                                        <div id="bulk_change">
                                                            <?php echo render_select('move_to_status_projects_bulk', $statuses, array('id', 'name'), 'ticket_single_change_status'); ?>
                                                            <?php echo render_select('move_to_source_projects_bulk', $sources, array('id', 'name'), 'lead_source'); ?>
                                                            <?php
                                                            echo render_datetime_input('projects_bulk_last_contact', 'projects_dt_last_contact');
                                                            ?>
                                                            <?php if (is_admin()) {
                                                                echo render_select('assign_to_projects_bulk', $staff, array('staffid', array('firstname', 'lastname')), 'projects_dt_assigned');
                                                            } ?>
                                                            <?php if (is_admin()) { ?>
                                                                <hr/>
                                                                <div class="form-group no-mbot">
                                                                    <div class="radio radio-primary radio-inline">
                                                                        <input type="radio"
                                                                               name="projects_bulk_visibility"
                                                                               id="projects_bulk_public" value="public">
                                                                        <label for="projects_bulk_public">
                                                                            <?php echo _l('project_public'); ?>
                                                                        </label>
                                                                    </div>
                                                                    <div class="radio radio-primary radio-inline">
                                                                        <input type="radio"
                                                                               name="projects_bulk_visibility"
                                                                               id="projects_bulk_private"
                                                                               value="private">
                                                                        <label for="projects_bulk_private">
                                                                            <?php echo _l('private'); ?>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default"
                                                                data-dismiss="modal"><?php echo _l('close'); ?></button>
                                                        <a href="#" class="btn btn-info"
                                                           onclick="leads_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                                                    </div>
                                                </div><!-- /.modal-content -->
                                            </div><!-- /.modal-dialog -->
                                        </div><!-- /.modal -->
                                        <?php
                                        $table_data = array();
                                        $_table_data = array(
                                            //'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="projects"><label></label></div>',
                                            _l(''),
                                            array(
                                                'name' => _l('projects_dt_event_date'),
                                                'th_attrs' => array('class' => 'date-created')
                                            ),
                                            array(
                                                'name' => _l('projects_dt_name'),
                                                'th_attrs' => array('class' => 'project-nm')
                                            ),
                                            _l('projects_dt_venue'),
                                            array(
                                                'name' => _l('projects_dt_status'),
                                                'th_attrs' => array('class' => 'project-status')
                                            ),
                                            _l('projects'),
                                            _l('projects_dt_assigned')//,
                                            // array(
                                            //   'name'=>_l('projects_dt_inquiry_on'),
                                            //   'th_attrs'=>array('class'=>'date-created')
                                            // )
                                        );

                                        foreach ($_table_data as $_t) {
                                            array_push($table_data, $_t);
                                        }
                                        $custom_fields = get_custom_fields('projects', array('show_on_table' => 1));
                                        foreach ($custom_fields as $field) {
                                            array_push($table_data, $field['name']);
                                        }
                                        $_op = _l('');
                                        array_push($table_data, $_op);
                                        render_datatable($table_data, 'projects');
                                        ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="project_sub_events" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        <span class="edit-title"><?php echo _l('event_list'); ?></span>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="ie-dt-fix">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal"><?php echo _l('cancel'); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once(APPPATH . 'views/admin/projects/status.php'); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css"/>

<script>
    var c_projectid = '<?php echo $projectid; ?>';
</script>
<script>
    $(function () {
        slideToggle('.projects-overview');
        projects_kanban();
		
        $('input[name="view_projects_eventdate"]').daterangepicker({
            locale: {
                format: 'MM/DD/YYYY'
            }
        });

        /**
         * Added By : Vaidehi
         * Dt : 11/10/2017
         * to clear view event date filter on cancel button
         */
        $('#view_projects_eventdate').on('cancel.daterangepicker', function (ev, picker) {
            //do something, like clearing an input
            $('#view_projects_eventdate').val('');

            $('.table-projects').DataTable().ajax.reload();
        });
    });

    function filterstatus(id) {
        $('select[name=view_project_status]').val(id);
        $('.selectpicker').selectpicker('refresh');
        $('.table-projects').DataTable().ajax.reload();
    }

    function getSubEvents(projectid) {
        $.ajax({
            method: 'post',
            async: false,
            url: '<?php echo admin_url(); ?>projects/getsubevents',
            data: 'projectid=' + projectid,
            dataType: "html",
            success: function (data) {
                $(".ie-dt-fix").html(data);
                $('#project_sub_events').modal('show');
            }
        });
    }
</script>
</body>
</html>