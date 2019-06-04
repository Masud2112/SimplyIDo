<div class="clearfix"></div>
<div class="col-md-12 hide" id="dvList">
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
            <a href="<?php echo admin_url('projects/dashboard/' . $pid); ?>"><?php echo($lname); ?></a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
        <?php } else { ?>
        <?php } ?>
        <span>Invoices</span>
    </div>

    <h1 class="pageTitleH1"><i class="fa fa-money fa-fw"></i><?php echo $title; ?></h1>
    <div class="clearfix"></div>
    <?php $this->load->view('admin/invoices/invoices_top_stats'); ?>
    <div class="clearfix"></div>
    <div class="panel_s btmbrd">
        <div class="panel-body">
            <div class="clearfix filterBtnRow">
                <div class="">
                    <?php if (has_permission('invoices', '', 'create')) { ?>
                        <?php if (isset($lid)) { ?>
                            <a href="<?php echo admin_url('invoices/invoice?lid=' . $lid); ?>"
                               class="btn btn-info pull-left new new-invoice-list"><?php echo _l('create_new_invoice'); ?></a>
                        <?php } else if (isset($pid)) { ?>
                            <a href="<?php echo admin_url('invoices/invoice?pid=' . $pid); ?>"
                               class="btn btn-info pull-left new new-invoice-list"><?php echo _l('create_new_invoice'); ?></a>
                        <?php } else if (isset($eid)) { ?>
                            <a href="<?php echo admin_url('invoices/invoice?eid=' . $eid); ?>"
                               class="btn btn-info pull-left new new-invoice-list"><?php echo _l('create_new_invoice'); ?></a>
                        <?php } else { ?>
                            <a href="<?php echo admin_url('invoices/invoice'); ?>"
                               class="btn btn-info pull-left new new-invoice-list"><?php echo _l('create_new_invoice'); ?></a>
                        <?php }
                    } ?>
                </div>
                <div class="pull-right">
                    <?php if ($switch_invoices_kanban!= 1) { ?>
                        <!--<div class="leads-search">
                            <div class="message_search text-right" data-toggle="tooltip" data-placement="bottom"
                                 data-title="Use # + tagname to search by tags">
                                    <span class="input-group-addon lead_serach_ico inline-block"><span
                                                class="glyphicon glyphicon-search"></span></span>
                                <div class="lead_search_inner form-group inline-block no-margin">
                                    <input type="search" id="search" name="search" class="form-control"
                                           data-name="search" onkeyup="invoices_kanban();" placeholder="Search..."
                                           value="">
                                </div>
                            </div>
                            <input type="hidden" name="sort_type" value=""/>
                            <input type="hidden" name="sort" value=""/>
                        </div>-->
                    <?php } ?>
                    <a class="btn btn-primary filter_btn"><i class="fa fa-filter"></i></a>
                    <?php
                    $list = $card = "";
                    if (isset($switch_invoices_kanban) && $switch_invoices_kanban== 1) {
                        $list = "selected disabled";
                    } else {
                        $card = "selected disabled";
                    } ?>
                    <!--<a class="btn btn-primary filter_btn"><i class="fa fa-filter"></i></a>-->
                    <a href="<?php echo admin_url('invoices/switch_invoices_kanban/'); ?>"
                       class="btn btn-primary viewchangeBtn hidden-xs <?php echo $list ?>">
                        <?php echo _l('switch_to_list_view'); ?>
                    </a>
                    <a href="<?php echo admin_url('invoices/switch_invoices_kanban/1'); ?>"
                       class="btn btn-primary viewchangeBtn hidden-xs <?php echo $card ?>">
                        <?php echo _l('projects_switch_to_kanban'); ?>
                    </a>
                </div>
            </div>
            <div class="clearfix"></div>
            <div id="invoices-table">
                <div class="lead-filterRow">
                    <div class="row invoice-filter-wrapper">
                        <div class="col-md-12">
                            <p class="bold"><?php echo _l('filter_by'); ?></p>
                        </div>
                        <div class="col-md-3">
                            <select id="view_status" name="view_status" class="selectpicker" data-width="100%"
                                    data-live-search="true" data-none-selected-text="Status">
                                <option value=""></option>
                                <?php foreach ($invoices_statuses as $status) { ?>
                                    <option value="<?php echo $status; ?>"><?php echo format_invoice_status($status, '', false); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="view_invoicedate" name="view_invoicedate"
                                   placeholder="Select Due Date"/>
                        </div>
                        <div class="col-md-3">
                            <select id="view_assigned" name="view_assigned" class="selectpicker" data-width="100%"
                                    data-live-search="true" data-none-selected-text="Assigned To">
                                <option value=""></option>
                                <?php foreach ($members as $as) { ?>
                                    <option value="<?php echo $as['staffid']; ?>"><?php echo get_staff_full_name($as['staffid']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php if ($this->session->has_userdata('invoices_kanban_view') && $this->session->userdata('invoices_kanban_view') == 'true') { ?>
                    <div class="active kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                        <div class="row">
                            <div class="contacts-kan-ban">
                                <div id="kan-ban"></div>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div id="small-table">
                        <!-- if invoiceid found in url -->
                        <?php echo form_hidden('invoiceid', $invoiceid); ?>
                        <?php
                        $table_data = array(
                            _l('invoice_dt_table_heading_number'),
                            _l('invoice_dt_table_heading_amount'),
                            array(
                                'name' => _l('invoice_estimate_year'),
                                'th_attrs' => array('class' => 'not_visible')
                            ),
                            _l('invoice_dt_table_heading_date'),
                            //_l('invoice_dt_table_heading_client'),
                            //_l('invoice_dt_table_heading_team'),
                            //_l('project'),
                            _l('invoice_dt_table_heading_duedate'),
                            _l('invoice_dt_table_heading_status'));
                        $custom_fields = get_custom_fields('invoice', array('show_on_table' => 1));
                        foreach ($custom_fields as $field) {
                            array_push($table_data, $field['name']);
                        }
                        $table_data = do_action('invoices_table_columns', $table_data);
                        render_datatable($table_data, 'invoices');
                        ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php
if($this->uri->segment(3)=="list_invoices"){ ?>
    <div class="col-md-12 small-table-right-col">
        <div id="invoice" class="hide">
        </div>
    </div>
<?php } ?>
<input type="hidden" name="leadid" value="<?php echo isset($lid) ? $lid : ''; ?>">
<input type="hidden" name="project_id" value="<?php echo isset($pid) ? $pid : ''; ?>">