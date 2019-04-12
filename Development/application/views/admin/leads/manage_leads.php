<?php init_head(); ?>
<div id="wrapper">
    <div class="content manage-leads-page">
        <div class="row">
            <div class="col-md-12">
                <?php /*if(isset($pg) && $pg == 'home') { */ ?>

                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span><?php echo $title; ?></span>
                </div>

                <?php /*} */ ?>
                <h1 class="pageTitleH1"><i class="fa fa-tty"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="_buttons">
                    <div class="row hide leads-overview">
                        <?php
                        $where_not_admin = '(addedfrom = ' . get_staff_user_id() . ' OR assigned=' . get_staff_user_id() . ' OR is_public = 1)';
                        $numStatuses = count($statuses);
                        $is_admin = is_admin();
                        //echo '<pre>'; print_r($numStatuses);
                        $processwidth = 100 / $numStatuses;
                        foreach ($statuses as $status) { ?>
                            <div class="process-step" style="width:<?php echo $processwidth . '%'; ?>;">
                                <?php
                                /*                                $this->db->where('status', $status['id']);
                                                                $this->db->where('deleted = ', 0);
                                                                $this->db->where('converted = ', 0);
                                                                $this->db->where('brandid = ', get_user_session());
                                                                if (!$is_admin) {
                                                                    $this->db->where($where_not_admin);
                                                                }
                                                                $total = $this->db->count_all_results('tblleads');
                                                                */
                                $total = get_leads_by_status($status['id']);
                                ?>
                                <a href="javascript:void(0)"
                                   onclick="filterstatus('<?php echo $status['id']; ?>'); return false;"
                                   id="single_filter_<?php echo $status['id']; ?>">
                                    <h3 class="bold brdcolor"
                                        style="border-color:<?php echo $status['color']; ?>"><?php echo $total; ?></h3>
                                    <span style="color:<?php echo $status['color']; ?>"><?php echo $status['name']; ?></span></a>
                            </div>
                        <?php } ?>
                        <?php
                        if (!$is_admin) {
                            $this->db->where($where_not_admin);
                        }
                        $total_leads = $this->db->count_all_results('tblleads');
                        ?>
                        <?php if ($is_admin) { ?>
                            <div class="col-md-2 col-xs-6">
                                <?php
                                $this->db->where('lost', 1);
                                if (!$is_admin) {
                                    $this->db->where($where_not_admin);
                                }
                                $total_lost = $this->db->count_all_results('tblleads');
                                $percent_lost = ($total_leads > 0 ? number_format(($total_lost * 100) / $total_leads, 2) : 0);
                                ?>
                                <h3 class="bold"><?php echo $percent_lost; ?>%</h3>
                                <span class="text-danger"><?php echo _l('lost_leads'); ?></span>
                            </div>
                            <div class="col-md-2 col-xs-6">
                                <?php
                                $this->db->where('junk', 1);
                                if (!$is_admin) {
                                    $this->db->where($where_not_admin);
                                }
                                $total_junk = $this->db->count_all_results('tblleads');
                                $percent_junk = ($total_leads > 0 ? number_format(($total_junk * 100) / $total_leads, 2) : 0);
                                ?>
                                <h3 class="bold"><?php echo $percent_junk; ?>%</h3>
                                <span class="text-danger"><?php echo _l('junk_leads'); ?></span>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="titleRow">
                    <h4><?php echo _l('leads_summary'); ?></h4>
                    <!--<a href="#" onclick="init_lead(); return false;" class="btn mright5 btn-info pull-left display-block">
					  <?php //echo _l('new_lead'); ?>
                      </a>-->
                    <div class="clearfix"></div>
                </div>

                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="clearfix filterBtnRow">
                            <div class="inline-block datatable">
                                <a class="btn bulk_act_btn btn-info" data-toggle="modal"
                                   data-target="#leads_bulk_actions" tabindex="0" aria-controls="DataTables_Table_0"
                                   href="#"><span>Bulk Actions</span></a>
                                <?php if (has_permission('leads', '', 'create')) { ?>
                                    <a href="<?php echo admin_url('leads/lead'); ?>"
                                       class="btn btn-info">
                                        <?php echo _l('new_lead'); ?>
                                    </a>
                                <?php } ?>
                            </div>
                            <div class="modal fade bulk_actions" id="leads_bulk_actions" tabindex="-1"
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
                                                <?php echo render_select('move_to_status_leads_bulk', $statuses, array('id', 'name'), 'ticket_single_change_status'); ?>
                                                <?php echo render_select('move_to_source_leads_bulk', $sources, array('id', 'name'), 'lead_source'); ?>
                                                <?php
                                                echo render_datetime_input('leads_bulk_last_contact', 'leads_dt_last_contact');
                                                ?>

                                                <?php if (is_admin()) {
                                                    echo render_select('assign_to_leads_bulk', $staff, array('staffid', array('firstname', 'lastname')), 'leads_dt_assigned');
                                                }
                                                ?>
                                                <?php /* <div class="form-group">
                                          <?php echo '<p><b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b></p>'; ?>
                                          <input type="text" class="tagsinput" id="tags_bulk" name="tags_bulk" value="" data-role="tagsinput">
                                       </div> */ ?>
                                                <?php if (is_admin()) { ?>
                                                    <hr/>
                                                    <div class="form-group no-mbot">
                                                        <div class="radio radio-primary radio-inline">
                                                            <input type="radio" name="leads_bulk_visibility"
                                                                   id="leads_bulk_public" value="public">
                                                            <label for="leads_bulk_public">
                                                                <?php echo _l('lead_public'); ?>
                                                            </label>
                                                        </div>
                                                        <div class="radio radio-primary radio-inline">
                                                            <input type="radio" name="leads_bulk_visibility"
                                                                   id="leads_bulk_private" value="private">
                                                            <label for="leads_bulk_private">
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
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                            <!-- /.modal -->
                            <div class="pull-right">
                                <?php if (is_admin()) { ?>
                                    <a href="<?php echo admin_url('leads/import'); ?>"
                                       class="btn btn-info pull-left display-block hidden-xs">
                                        <?php echo _l('import_leads'); ?>
                                    </a>
                                <?php } ?>
                                <!--<a href="#" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" data-title="<?php echo _l('leads_summary'); ?>" data-placement="bottom" onclick="slideToggle('.leads-overview'); return false;"><i class="fa fa-bar-chart"></i></a> -->
                                <?php //if(is_admin()){
                                $list = $card = "";
                                if (isset($switch_kanban) && $switch_kanban == 1) {
                                    $list = "selected disabled";
                                } else {
                                    $card = "selected disabled";
                                }
                                ?>
                                <?php if (is_mobile()) {
                                    echo '<a class="btn btn-primary  filter_btn_search"><i class="glyphicon glyphicon-search"></i></a>';
                                } ?>
                                <a class="btn btn-primary filter_btn"><i class="fa fa-filter"></i></a>
                                <a href="<?php echo admin_url('leads/switch_kanban/'); ?>"
                                   class="btn btn-primary viewchangeBtn hidden-xs <?php echo $list ?>"><?php echo _l('switch_to_list_view'); ?>
                                </a>
                                <a href="<?php echo admin_url('leads/switch_kanban/1'); ?>"
                                   class="btn btn-primary viewchangeBtn hidden-xs <?php echo $card ?>">
                                    <?php echo _l('leads_switch_to_kanban'); ?>
                                </a>
                                <?php //} ?>
                            </div>
                            <div class="col-sm-4 col-xs-12  pull-right leads-search">
                                <?php if ($this->session->userdata('leads_kanban_view') == 'true') { ?>
                                    <div class="lead_search text-right" data-toggle="tooltip" data-placement="bottom"
                                         data-title="<?php echo _l('search_by_tags'); ?>">
                                        <span class="input-group-addon lead_serach_ico inline-block"><span
                                                    class="glyphicon glyphicon-search"></span></span>
                                        <div class="lead_search_inner form-group inline-block no-margin"><input
                                                    type="search" id="search" name="search" class="form-control"
                                                    data-name="search" onkeyup="leads_kanban();" placeholder="Search..."
                                                    value=""></div>
                                    </div>
                                    <!--<label><div class="input-group"><span class="input-group-addon"><span class="glyphicon glyphicon-search"></span></span><input type="search" class="form-control input-sm" placeholder="Search..." aria-controls="DataTables_Table_0"></div></label>-->
                                <?php } ?>
                                <?php echo form_hidden('sort_type'); ?>
                                <?php echo form_hidden('sort'); ?>
                            </div>
                        </div>
                        <div class="cardViewContainer">
                            <?php
                            if ($this->session->has_userdata('leads_kanban_view') && $this->session->userdata('leads_kanban_view') == 'true') { ?>
                                <div class="active kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                                    <!-- <div class="kanban-leads-sort">
                           <span class="bold"><?php //echo _l('leads_sort_by'); ?>: </span>
                           <a href="#" onclick="leads_kanban_sort('dateadded'); return false"><?php //echo _l('leads_sort_by_datecreated'); ?></a>
                           |
                           <a href="#" onclick="leads_kanban_sort('leadorder');return false;"><?php //echo _l('leads_sort_by_kanban_order'); ?></a>
                           |
                           <a href="#" onclick="leads_kanban_sort('lastcontact');return false;"><?php //echo _l('leads_sort_by_lastcontact'); ?></a>
                        </div> -->
                                    <div class="row">
                                        <div class="container-fluid leads-kan-ban">
                                            <div id="kan-ban"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="row" id="leads-table">
                                    <div class="lead-filterRow">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h5 class="sub-head"><?php echo _l('filter_by'); ?></h5>
                                            </div>
                                            <?php //if(is_admin()){ ?>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="view_inquireddate"
                                                           name="view_inquireddate"
                                                           placeholder="Select Inquired Dates"/>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <?php
                                                    echo render_select('view_status', $statuses, array('id', 'name'), '', '', array('data-width' => '100%', 'data-none-selected-text' => _l('leads_dt_status')));
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="view_eventdate"
                                                           name="view_eventdate" placeholder="Select Event Dates"/>
                                                </div>
                                                <!--
                                                  <select id="view_eventdate" name="view_eventdate" class="selectpicker" data-width="100%" data-none-selected-text="Event Date" data-live-search="true" tabindex="-98">
                                                    <option value=""></option>
                                                    <option value="m">This Month</option>
                                                    <option value="m+1">Next Month</option>
                                                  </select>

                                                      //echo render_select('view_eventdate',$eventdate,array('id','name'),'','',array('data-width'=>'100%','data-none-selected-text'=>_l('leads_dt_name')));
                                                      ?> -->
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <?php
                                                    echo render_select('view_eventtype', $eventtypes, array('eventtypeid', 'eventtypename'), '', '', array('data-width' => '100%', 'data-none-selected-text' => _l('leads_dt_type')));
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <?php echo render_select('view_assigned', $staff, array('staffid', array('firstname', 'lastname')), '', '', array('data-width' => '100%', 'data-none-selected-text' => _l('leads_dt_assigned'))); ?>
                                                </div>
                                            </div>
                                            <?php //} ?>


                                            <?php if (is_admin()) { ?>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <select name="custom_view"
                                                                title="<?php echo _l('additional_filters'); ?>"
                                                                id="custom_view" class="selectpicker" data-width="100%">
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
                                    <div class="col-md-12">
                                        <?php
                                        $table_data = array();
                                        $_table_data = array(
                                            '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="leads"><label></label></div>',
                                            // '#',
                                            _l(''),
                                            _l('projects_dt_event_date'),
                                            array(
                                                'name' => _l('projects_dt_name'),
                                                'th_attrs' => array('class' => 'lead-nm')
                                            ),
                                            array(
                                                'name' => _l('leads_dt_status'),
                                                'th_attrs' => array('class' => 'lead-status')
                                            ),
                                            _l('leads_dt_assigned'),
                                            // _l('leads_dt_type'),
                                            // _l('leads_dt_phonenumber'),
                                            // _l('tags'),
                                            // _l('leads_source'),

                                            // array(
                                            //  'name'=>_l('leads_dt_datecreated'),
                                            //  'th_attrs'=>array('class'=>'date-created')
                                            //  )
                                            array(
                                                'name' => _l('leads_dt_inquiry_on'),
                                                'th_attrs' => array('class' => 'date-created')
                                            ));

                                        foreach ($_table_data as $_t) {
                                            array_push($table_data, $_t);
                                        }
                                        $custom_fields = get_custom_fields('leads', array('show_on_table' => 1));
                                        foreach ($custom_fields as $field) {
                                            array_push($table_data, $field['name']);
                                        }
                                        //$table_data = do_action('leads_table_columns',$table_data);
                                        $_op = _l('');
                                        array_push($table_data, $_op);
                                        render_datatable($table_data, 'leads'); ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once(APPPATH . 'views/admin/leads/status.php'); ?>
<?php init_tail(); ?>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css"/>

<script>
    var c_leadid = '<?php echo $leadid; ?>';
</script>
<script>
    $(function () {

        slideToggle('.leads-overview');
        leads_kanban();
        $('input[name="view_inquireddate"]').daterangepicker({
            locale: {
                format: 'MM/DD/YYYY'
            },
            clearBtn: true
        });

        $('input[name="view_eventdate"]').daterangepicker({
            locale: {
                format: 'MM/DD/YYYY'
            }
        });

        /**
         * Added By : Vaidehi
         * Dt : 11/10/2017
         * to clear view inquireddate filter on cancel button
         */
        $('#view_inquireddate').on('cancel.daterangepicker', function (ev, picker) {
            //do something, like clearing an input
            $('#view_inquireddate').val('');

            $('.table-leads').DataTable().ajax.reload();
        });

        /**
         * Added By : Vaidehi
         * Dt : 11/10/2017
         * to clear view inquireddate filter on cancel button
         */
        $('#view_eventdate').on('cancel.daterangepicker', function (ev, picker) {
            //do something, like clearing an input
            $('#view_eventdate').val('');

            $('.table-leads').DataTable().ajax.reload();
        });
    });


    function filterstatus(id) {
        $('select[name=view_status]').val(id);
        $('.selectpicker').selectpicker('refresh');
        $('.table-leads').DataTable().ajax.reload();
    }

    /*
    ** Added By Sanjay on 02/14/2018
    ** Rendering filtered data from dashboard lead pipeline
    */
    /*$(function () {
        url = window.location.href;
        var did = url.split('?')[1].split('=')[1];
        if (did) {
            setTimeout(function () {
                $("#single_filter_" + did).trigger("click");
            }, 1000);
        }


    });*/

</script>
</body>
</html>