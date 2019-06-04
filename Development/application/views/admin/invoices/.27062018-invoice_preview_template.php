<?php
$leadid = $invoice->leadid;
$project_id = $invoice->project_id;
$eventid = $invoice->eventid;
if (count($invoices_to_merge) > 0) { ?>
    <div class="panel_s no-padding hidden">
        <div class="panel-body">
            <h4 class="no-margin"><?php echo _l('invoices_available_for_merging'); ?></h4>
            <hr/>
            <?php foreach ($invoices_to_merge as $_inv) { ?>
                <p>
                    <a href="<?php echo admin_url('invoices/list_invoices#' . $_inv->id); ?>"
                       target="_blank"><?php echo format_invoice_number($_inv->id); ?></a>
                    - <?php echo format_money($_inv->total, $_inv->symbol); ?>
                    <span class="pull-right">
         <?php echo format_invoice_status($_inv->status); ?>
         </span>
                </p>
            <?php } ?>
        </div>
    </div>
<?php } ?>
<?php echo form_hidden('_attachment_sale_id', $invoice->id); ?>
<?php echo form_hidden('_attachment_sale_type', 'invoice'); ?>
<div class="col-md-12 no-padding">
    <h1 class="pageTitleH1">
        <i class="fa fa-money"></i><?php echo "Invoice Dashboard"; ?>
    </h1>
    <div class="pull-right">
        <div class="breadcrumb">
            <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <?php if (isset($lid)) { ?>
                <a href="<?php echo admin_url('leads/'); ?>">Leads</a>
                <i class="fa fa-angle-right breadcrumb-arrow"></i>
                <a href="<?php echo admin_url('leads/dashboard/' . $lid); ?>"><?php echo($lname); ?></a>
                <i class="fa fa-angle-right breadcrumb-arrow"></i>
                <a href="<?php echo admin_url('invoices?lid=' . $lid); ?>">Invoices</a>
                <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <?php } elseif (isset($pid)) { ?>
                <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                <i class="fa fa-angle-right breadcrumb-arrow"></i>
                <a href="<?php echo admin_url('projects/dashboard/' . $pid); ?>"><?php echo($lname); ?></a>
                <i class="fa fa-angle-right breadcrumb-arrow"></i>
                <a href="<?php echo admin_url('invoices?pid=' . $pid); ?>">Invoices</a>
                <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <?php } else { ?>
                <a href="<?php echo admin_url('invoices'); ?>">Invoices</a>
                <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <?php } ?>
            <span><?php echo isset($invoice) ? $invoice->number : "New Invoice" ?></span>
        </div>
    </div>
    <div class="clearfix"></div>

    <h4> <?php echo format_invoice_number($invoice->id); ?></h4>
    <div class="pull-right">
        <?php
        $_tooltip = _l('invoice_sent_to_email_tooltip');
        $_tooltip_already_send = '';
        if ($invoice->sent == 1) {
            $_tooltip_already_send = _l('invoice_already_send_to_client_tooltip', time_ago($invoice->datesend));
        }
        ?>
        <?php if (has_permission('invoices', '', 'edit')) { ?>
            <?php if (isset($lid) && $lid > 0) { ?>
                <a href="<?php echo admin_url('invoices/invoice/' . $invoice->id . '?lid=' . $lid); ?>"
                   data-toggle="tooltip" title="<?php echo _l('edit_invoice_tooltip'); ?>"
                   class="btn btn-icon btn-with-tooltip" data-placement="bottom"><i
                            class="fa fa-pencil-square-o"></i></a>
            <?php } elseif (isset($pid) && $pid > 0) { ?>
                <a href="<?php echo admin_url('invoices/invoice/' . $invoice->id . '?pid=' . $pid); ?>"
                   data-toggle="tooltip" title="<?php echo _l('edit_invoice_tooltip'); ?>"
                   class="btn btn-icon btn-with-tooltip" data-placement="bottom"><i
                            class="fa fa-pencil-square-o"></i></a>
            <?php } elseif (isset($eid) && $eid > 0) { ?>
                <a href="<?php echo admin_url('invoices/invoice/' . $invoice->id . '?eid=' . $eid); ?>"
                   data-toggle="tooltip" title="<?php echo _l('edit_invoice_tooltip'); ?>"
                   class="btn btn-icon btn-with-tooltip" data-placement="bottom"><i
                            class="fa fa-pencil-square-o"></i></a>
            <?php } else { ?>
                <a href="<?php echo admin_url('invoices/invoice/' . $invoice->id); ?>" data-toggle="tooltip"
                   title="<?php echo _l('edit_invoice_tooltip'); ?>" class="btn btn-icon btn-with-tooltip"
                   data-placement="bottom"><i class="fa fa-pencil-square-o"></i></a>
            <?php } ?>

        <?php } ?>
        <a href="<?php echo admin_url('invoices/pdf/' . $invoice->id . '?print=true'); ?>" target="_blank"
           class="btn btn-icon btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('print'); ?>"
           data-placement="bottom"><i class="fa fa-print"></i></a>
        <a href="<?php echo admin_url('invoices/pdf/' . $invoice->id); ?>" class="btn btn-icon btn-with-tooltip"
           data-toggle="tooltip" title="<?php echo _l('view_pdf'); ?>" data-placement="bottom"><i
                    class="fa fa-file-pdf-o"></i></a>
        <a href="#" class="invoice-send-to-client btn-with-tooltip btn btn-icon" data-toggle="tooltip"
           title="<?php echo $_tooltip; ?>" data-placement="bottom"><span data-toggle="tooltip"
                                                                          data-title="<?php echo $_tooltip_already_send; ?>"><i
                        class="fa fa-envelope"></i></span></a>
        <!-- Single button -->
        <div class="btn-group">
            <button type="button" class="btn btn-info pull-left dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <?php echo _l('more'); ?> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
                <li><a href="<?php echo site_url('viewinvoice/' . $invoice->id . '/' . $invoice->hash) ?>"
                       target="_blank"><?php echo _l('view_invoice_as_customer_tooltip'); ?></a></li>
                <li>
                    <?php if ($invoice->status == 4 || ($invoice->status == 3 && date('Y-m-d') > date('Y-m-d', strtotime(to_sql_date($invoice->duedate))))) { ?>
                        <?php if (isset($invoice->leadid)) { ?>
                            <a href="<?php echo admin_url('invoices/send_overdue_notice/' . $invoice->id . '?lid=' . $invoice->leadid); ?>"><?php echo _l('send_overdue_notice_tooltip'); ?></a>
                        <?php } elseif (isset($invoice->project_id)) { ?>
                            <a href="<?php echo admin_url('invoices/send_overdue_notice/' . $invoice->id . '?pid=' . $invoice->project_id); ?>"><?php echo _l('send_overdue_notice_tooltip'); ?></a>
                        <?php } elseif (isset($invoice->eventid)) { ?>
                            <a href="<?php echo admin_url('invoices/send_overdue_notice/' . $invoice->id . '?eid=' . $invoice->eventid); ?>"><?php echo _l('send_overdue_notice_tooltip'); ?></a>
                        <?php } ?>
                    <?php } ?>
                </li>
                <!-- <li>
                           <a href="#" data-toggle="modal" data-target="#sales_attach_file"><?php //echo _l('invoice_attach_file'); ?></a>
                        </li>
                        <?php //if(has_permission('invoices','','create')){ ?>
                        <li>
                           <a href="<?php //echo admin_url('invoices/copy/'.$invoice->id); ?>"><?php //echo _l('invoice_copy'); ?></a>
                        </li>
                        <?php //} ?> -->
                <?php if ($invoice->sent == 0) { ?>
                    <li>
                        <?php if (isset($lid)) { ?>
                            <a href="<?php echo admin_url('invoices/mark_as_sent/' . $invoice->id . '?lid=' . $lid); ?>"><?php echo _l('invoice_mark_as_sent'); ?></a>
                        <?php } elseif (isset($pid)) { ?>
                            <a href="<?php echo admin_url('invoices/mark_as_sent/' . $invoice->id . '?pid=' . $pid); ?>"><?php echo _l('invoice_mark_as_sent'); ?></a>
                        <?php } elseif (isset($eid)) { ?>
                            <a href="<?php echo admin_url('invoices/mark_as_sent/' . $invoice->id . '?eid=' . $eid); ?>"><?php echo _l('invoice_mark_as_sent'); ?></a>
                        <?php } ?>
                    </li>
                <?php } ?>
                <?php if (has_permission('invoices', '', 'edit') || has_permission('invoices', '', 'create')) { ?>
                    <li>
                        <?php if ($invoice->status != 5 && $invoice->status != 2 && $invoice->status != 3) { ?>
                            <?php if (isset($lid)) { ?>
                                <a href="<?php echo admin_url('invoices/mark_as_cancelled/' . $invoice->id . '?lid=' . $lid); ?>"><?php echo _l('invoice_mark_as', _l('invoice_status_cancelled')); ?></a>
                            <?php } elseif (isset($pid)) { ?>
                                <a href="<?php echo admin_url('invoices/mark_as_cancelled/' . $invoice->id . '?pid=' . $pid); ?>"><?php echo _l('invoice_mark_as', _l('invoice_status_cancelled')); ?></a>
                            <?php } elseif (isset($eid)) { ?>
                                <a href="<?php echo admin_url('invoices/mark_as_cancelled/' . $invoice->id . '?eid=' . $eid); ?>"><?php echo _l('invoice_mark_as', _l('invoice_status_cancelled')); ?></a>
                            <?php } ?>
                        <?php } else if ($invoice->status == 5) { ?>
                            <?php if (isset($lid)) { ?>
                                <a href="<?php echo admin_url('invoices/unmark_as_cancelled/' . $invoice->id . '?lid=' . $lid); ?>"><?php echo _l('invoice_mark_as', _l('invoice_status_cancelled')); ?></a>
                            <?php } elseif (isset($pid)) { ?>
                                <a href="<?php echo admin_url('invoices/unmark_as_cancelled/' . $invoice->id . '?pid=' . $pid); ?>"><?php echo _l('invoice_mark_as', _l('invoice_status_cancelled')); ?></a>
                            <?php } elseif (isset($eid)) { ?>
                                <a href="<?php echo admin_url('invoices/unmark_as_cancelled/' . $invoice->id . '?eid=' . $eid); ?>"><?php echo _l('invoice_mark_as', _l('invoice_status_cancelled')); ?></a>
                            <?php } ?>
                        <?php } ?>
                    </li>
                <?php } ?>
                <?php
                if ((get_option('delete_only_on_last_invoice') == 1 && is_last_invoice($invoice->id)) || (get_option('delete_only_on_last_invoice') == 0)) { ?>
                    <?php if (has_permission('invoices', '', 'delete')) { ?>
                        <li data-toggle="tooltip" data-title="<?php echo _l('delete_invoice_tooltip'); ?>">
                            <a href="<?php echo admin_url('invoices/delete/' . $invoice->id); ?>"
                               class="text-danger delete-text _invoicedelete"><?php echo _l('delete_invoice'); ?></a>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </div>

    </div>


    <div class="clearfix"></div>

    <div class="panel_s btmbrd">
        <div class="">


            <?php if ($invoice->recurring > 0) {
                echo '<div class="ribbon info"><span>' . _l('invoice_recurring_indicator') . '</span></div>';
            } ?>
            <ul class="nav nav-tabs invoice-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#tab_invoice" aria-controls="tab_invoice" role="tab" data-toggle="tab">
                        <?php echo _l('invoice'); ?>
                    </a>
                </li>
                <?php if ($invoice->recurring > 0) { ?>
                    <li role="presentation">
                        <a href="#tab_child_invoices" aria-controls="tab_child_invoices" role="tab" data-toggle="tab">
                            <?php echo _l('child_invoices'); ?>
                        </a>
                    </li>
                <?php } ?>
                <!-- <li role="presentation">
               <a href="#tab_tasks" onclick="init_rel_tasks_table(<?php //echo $invoice->id; ?>,'invoice'); return false;" aria-controls="tab_tasks" role="tab" data-toggle="tab">
               <?php //echo _l('tasks'); ?>
               </a>
            </li> -->
                <li role="presentation">
                    <a href="#tab_activity" aria-controls="tab_activity" role="tab" data-toggle="tab">
                        <?php echo _l('invoice_view_activity_tooltip'); ?>
                    </a>
                </li>
                <li role="presentation">
                    <a href="#tab_reminders"
                       onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?php echo $invoice->id; ?> + '/' + 'invoice', [4], [4],undefined,[1,'ASC']); return false;"
                       aria-controls="tab_reminders" role="tab" data-toggle="tab" class="remindertab">
                        <div class="clearfix"></div>
                        <?php echo _l('estimate_reminders'); ?>
                        <?php
                        $total_reminders = total_rows('tblreminders',
                            array(
                                'isnotified' => 0,
                                'staff' => get_staff_user_id(),
                                'rel_type' => 'invoice',
                                'rel_id' => $invoice->id
                            )
                        );
                        if ($total_reminders > 0) {
                            echo '<span class="badge">' . $total_reminders . '</span>';
                        } else {
                            echo '<span class="badge"></span>';
                        }
                        ?>
                    </a>
                </li>
                <!--  <li role="presentation">
               <a href="#tab_views" aria-controls="tab_views" role="tab" data-toggle="tab">
               <?php //echo _l('view_tracking'); ?>
               </a>
            </li> -->
                <!-- <li role="presentation">
               <a href="#" onclick="small_table_full_view(); return false;" data-placement="left" data-toggle="tooltip" data-title="<?php //echo _l('toggle_full_view'); ?>" class="toggle_view">
               <i class="fa fa-expand"></i></a>
            </li> -->
            </ul>

            <div class="clearfix"></div>
            <div class="tab-content panel-body">
                <div role="tabpanel" class="tab-pane  active" id="tab_invoice">
                    <div class="bannerImg"><img src="<?php echo base_url() ?>assets/images/default_banner.jpg"/></div>
                    <div class="row">
                        <div class="eventInfo col-sm-6">
                            <?php
                            $leadid = $invoice->leadid;
                            $project_id = $invoice->project_id;
                            $eventid = $invoice->eventid;
                            $brandid = get_user_session();
                            $CI =& get_instance();
                            $CI->load->model('leads_model');
                            $CI->load->model('projects_model');
                            if ($leadid > 0) {
                                $event = $CI->leads_model->get($leadid);
                                $image = lead_profile_image($leadid, array('profile_image', 'img-responsive img-thumbnail', 'project-profile-image-thumb'), 'thumb');

                            } elseif ($project_id > 0) {
                                $event = $CI->projects_model->get($project_id);
                                $image = project_profile_image($project_id, array('profile_image', 'img-responsive img-thumbnail', 'project-profile-image-thumb'), 'thumb');
                            } elseif ($eventid > 0) {
                                $event = $CI->projects_model->get($eventid);
                                $image = project_profile_image($eventid, array('profile_image', 'img-responsive img-thumbnail', 'project-profile-image-thumb'), 'thumb');
                            } else {
                                $event = "";
                                echo "";
                            }
                            if (!empty($event)) { ?>
                                <div class="eventImg"><?php echo $image; ?></div>
                                <span class="b1"><?php echo $event->name ?></span></span>
                                <span class="b2">
                                <?php echo date('l, F j, Y', strtotime($event->eventstartdatetime)) ?></span>
                                <span class="b2">
                                <?php echo date('h:i A', strtotime($event->eventstartdatetime)) ?></span></span>
                            <?php } ?>
                        </div>
                        <div class="invoiceDate col-sm-6 text-right">
                            <span class="b1">INVOICE</span>
                            <p class="b2">
                                <?php if (isset($lid) && $lid > 0) { ?>
                                <a href="<?php echo admin_url('invoices/invoice/' . $invoice->id . '?lid=' . $lid); ?>">
                                    <?php }elseif (isset($pid) && $pid > 0) { ?>
                                    <a href="<?php echo admin_url('invoices/invoice/' . $invoice->id . '?pid=' . $pid); ?>">
                                        <?php }elseif (isset($eid) && $eid > 0) { ?>
                                        <a href="<?php echo admin_url('invoices/invoice/' . $invoice->id . '?eid=' . $eid); ?>">
                                            <?php } else { ?>
                                            <a href="<?php echo admin_url('invoices/invoice/' . $invoice->id); ?>">
                                                <?php } ?>
                                                <span id="invoice-number">
                                <?php echo format_invoice_number($invoice->id); ?>
                              </span>
                                            </a>
                            </p>
                            <p class="no-mbot">
                             <span class="bold">
                                <?php echo _l('ISSUED:'); ?>
                             </span>
                                <?php echo $invoice->date; ?>
                            </p>
                            <?php if (!empty($invoice->duedate)) { ?>
                                <p class="no-mbot">
                             <span class="bold">
                              <?php echo _l('DUE:'); ?>
                             </span>
                                    <?php echo $invoice->duedate; ?>
                                </p>
                            <?php } ?>
                            <?php if ($invoice->sale_agent != 0 && get_option('show_sale_agent_on_invoices') == 1) { ?>
                                <p class="no-mbot hide">
                                    <span class="bold"><?php echo _l('sale_agent_string'); ?>: </span>
                                    <?php echo get_staff_full_name($invoice->sale_agent); ?>
                                </p>
                            <?php } ?>
                            <?php $pdf_custom_fields = get_custom_fields('invoice', array('show_on_pdf' => 1));
                            foreach ($pdf_custom_fields as $field) {
                                $value = get_custom_field_value($invoice->id, $field['id'], 'invoice');
                                if ($value == '') {
                                    continue;
                                } ?>
                                <p class="no-mbot">
                                    <span class="bold"><?php echo $field['name']; ?>: </span>
                                    <?php echo $value; ?>
                                </p>
                            <?php } ?>

                        </div>
                    </div>
                    <div class="clearfix brdline"></div>
                    <div id="invoice-preview">
                        <div class="row">
                            <?php
                            if ($invoice->recurring > 0 || $invoice->is_recurring_from != NULL) {
                                $recurring_invoice = $invoice;
                                $next_recurring_date_compare = to_sql_date($recurring_invoice->date);
                                if ($recurring_invoice->last_recurring_date) {
                                    $next_recurring_date_compare = $recurring_invoice->last_recurring_date;
                                }
                                if ($invoice->is_recurring_from != NULL) {
                                    $recurring_invoice = $this->invoices_model->get($invoice->is_recurring_from);
                                    $next_recurring_date_compare = $recurring_invoice->last_recurring_date;
                                }
                                if ($recurring_invoice->custom_recurring == 0) {
                                    $recurring_invoice->recurring_type = 'MONTH';
                                }
                                $next_date = date('Y-m-d', strtotime('+' . $recurring_invoice->recurring . ' ' . strtoupper($recurring_invoice->recurring_type), strtotime($next_recurring_date_compare)));
                                ?>
                                <div class="col-md-12">
                                    <h4 class="font-medium text-success">
                                        <?php if (!$recurring_invoice->recurring_ends_on || ($next_date <= $recurring_invoice->recurring_ends_on)) { ?>
                                            <?php echo '<i class="fa fa-question-circle" data-toggle="tooltip" data-title="' . _l('recurring_recreate_hour_notice', _l('invoice')) . '"></i> ' . _l('next_invoice_date', _d($next_date)); ?>
                                            <br/>
                                            <?php if ($invoice->status == 6) {
                                                echo '<p class="mtop5"><small class="text-warning ">' . _l('recurring_invoice_draft_notice') . '</small></p>';
                                            }
                                            ?>
                                        <?php } ?>
                                        <?php
                                        if ($invoice->is_recurring_from != NULL) { ?>
                                            <?php echo '<small class="text-muted">' . _l('invoice_recurring_from', '<a href="' . admin_url('invoices/list_invoices#' . $invoice->is_recurring_from) . '" onclick="init_invoice(' . $invoice->is_recurring_from . ');return false;">' . format_invoice_number($invoice->is_recurring_from) . '</small></a>'); ?>
                                        <?php } ?>
                                    </h4>
                                </div>
                            <?php } ?>
                            <?php if ($invoice->project_id != 0) { ?>
                                <div class="col-md-12">
                                    <h4 class="font-medium no-mtop mbot20"><?php echo _l('related_to_project', array(
                                            _l('invoice_lowercase'),
                                            _l('project_lowercase'),
                                            '<a href="' . admin_url('projects/dashboard/' . $invoice->project_id) . '" target="_blank">' . $invoice->project_data->name . '</a>',
                                        )); ?></h4>
                                </div>
                            <?php } else if ($invoice->eventid != 0) { ?>
                                <div class="col-md-12">
                                    <h4 class="font-medium no-mtop mbot20"><?php echo _l('related_to_project', array(
                                            _l('invoice_lowercase'),
                                            'event',
                                            '<a href="' . admin_url('projects/dashboard/' . $invoice->eventid) . '" target="_blank">' . $invoice->project_data->name . '</a>',
                                        )); ?></h4>
                                </div>
                            <?php } ?>
                            <div class="col-md-4 invFromTo">
                                <span class="label">From:</span>
                                <address>
                                    <?php echo format_organization_info(); ?>
                                    <?php
                                    echo '<br />'. get_staff_email();
                                    echo '<br />'. get_staff_phone();
                                    ?>
                                </address>
                            </div>
                            <div class="col-sm-4 invFromTo">
                                <span class="label"><?php echo _l('invoice_bill_to'); ?>:</span>
                                <address>
                                    <?php
                                    echo format_customer_info($invoice, 'invoice', 'billing', true);
                                    if(isset($invoice->client->phone->phone)){
                                        echo '<br />'.$invoice->client->phone->phone;
                                    }
                                    if(isset($invoice->client->email->email)){
                                        echo '<br />'.$invoice->client->email->email;
                                    }
                                    ?>
                                </address>
                                <?php if ($invoice->include_shipping == 1 && $invoice->show_shipping_on_invoice == 1) { ?>
                                    <span class="bold"><?php echo _l('ship_to'); ?>:</span>
                                    <address>
                                        <?php echo format_customer_info($invoice, 'invoice', 'shipping'); ?>
                                    </address>
                                <?php } ?>

                            </div>
                            <div class="col-sm-4 invoice-status-txt">

                                <?php echo format_invoice_status($invoice->status, 'mtop5'); ?>

                                <?php
                                if ($invoice->status == 6) { ?>
                                    <div class="alert alert-info">
                                        <?php echo _l('invoice_draft_status_info'); ?>
                                    </div>
                                <?php } ?>
                                <?php if ($invoice->status == 3 || $invoice->status == 4) {
                                    if (date('Y-m-d') > date('Y-m-d', strtotime(to_sql_date($invoice->duedate)))) {
                                        echo '<p class="text-danger no-mbot">' . _l('invoice_is_overdue', floor((abs(time() - strtotime(to_sql_date($invoice->duedate)))) / (60 * 60 * 24))) . '</p>';
                                    }
                                } ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table items invoice-items-preview">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th class="description"
                                                width="50%"><?php echo _l('invoice_table_item_heading'); ?></th>
                                            <?php
                                            $qty_heading = _l('invoice_table_quantity_heading');
                                            if ($invoice->show_quantity_as == 2) {
                                                $qty_heading = _l('invoice_table_hours_heading');
                                            } else if ($invoice->show_quantity_as == 3) {
                                                $qty_heading = _l('invoice_table_quantity_heading') . '/' . _l('invoice_table_hours_heading');
                                            }
                                            ?>
                                            <th><?php echo $qty_heading; ?></th>
                                            <th><?php echo _l('invoice_table_rate_heading'); ?></th>
                                            <?php if (get_option('show_tax_per_item') == 1) { ?>
                                                <th><?php echo _l('invoice_table_tax_heading'); ?></th>
                                            <?php } ?>
                                            <th><?php echo _l('invoice_table_amount_heading'); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $items_data = get_table_items_and_taxes($invoice->items, 'invoice', true);
                                        $taxes = $items_data['taxes'];
                                        echo $items_data['html'];
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-4 col-md-offset-8">
                                <table class="table text-right">
                                    <tbody>
                                    <tr id="subtotal">
                                        <td><span class="bold"><?php echo _l('invoice_subtotal'); ?></span>
                                        </td>
                                        <td class="subtotal">
                                            <?php echo format_money($invoice->subtotal, $invoice->symbol); ?>
                                        </td>
                                    </tr>
                                    <?php if ($invoice->discount_percent != 0) { ?>
                                        <tr>
                                            <td>
                                                <span class="bold"><?php echo _l('invoice_discount'); ?>
                                                    (<?php echo _format_number($invoice->discount_percent, true); ?>
                                                    %)</span>
                                            </td>
                                            <td class="discount">
                                                <?php echo '-' . format_money($invoice->discount_total, $invoice->symbol); ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <?php
                                        if(count($taxes)==0){
                                            echo '<tr class="tax-area"><td class="bold">Tax (0%)</td><td>' . format_money(0, $invoice->symbol) . '</td></tr>';
                                        }
                                    foreach ($taxes as $tax) {
                                        $total = array_sum($tax['total']);
                                        if ($invoice->discount_percent != 0 && $invoice->discount_type == 'before_tax') {
                                            $total_tax_calculated = ($total * $invoice->discount_percent) / 100;
                                            $total = ($total - $total_tax_calculated);
                                        }
                                        $_tax_name = explode('|', $tax['tax_name']);
                                        echo '<tr class="tax-area"><td class="bold">' . $_tax_name[0] . ' (' . _format_number($tax['taxrate']) . '%)</td><td>' . format_money($total, $invoice->symbol) . '</td></tr>';
                                    } ?>
                                    <?php if ((int)$invoice->adjustment != 0) { ?>
                                        <tr>
                                            <td>
                                                <span class="bold"><?php echo _l('invoice_adjustment'); ?></span>
                                            </td>
                                            <td class="adjustment">
                                                <?php echo format_money($invoice->adjustment, $invoice->symbol); ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <!-- <tr id="transaction">
                                 <td><span class="bold"><?php //echo _l('transaction_charge'); ?></span>
                                 </td>
                                 <td class="subtotal">
                                    <?php //echo format_money($invoice->transaction_charge,$invoice->symbol); ?>
                                 </td>
                              </tr> -->
                                    <tr>
                                        <td><span class="bold"><?php echo _l('invoice_total'); ?></span>
                                        </td>
                                        <td class="total">
                                            <?php echo format_money($invoice->total, $invoice->symbol); ?>
                                        </td>
                                    </tr>
                                    <?php if ($invoice->status == 3) { ?>
                                        <tr>
                                            <td><span><?php echo _l('invoice_total_paid'); ?></span></td>
                                            <td>
                                                <?php echo format_money(sum_from_table('tblinvoicepaymentrecords', array('field' => 'amount', 'where' => array('invoiceid' => $invoice->id))), $invoice->symbol); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="text-danger bold"><?php echo _l('invoice_amount_due'); ?></span>
                                            </td>
                                            <td>
                                                <?php echo format_money(get_invoice_total_left_to_pay($invoice->id, $invoice->total), $invoice->symbol); ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <?php if (count($invoice->attachments) > 0) { ?>
                            <div class="clearfix"></div>
                            <hr/>
                            <p class="bold text-muted"><?php echo _l('invoice_files'); ?></p>
                            <?php foreach ($invoice->attachments as $attachment) {
                                $attachment_url = site_url('download/file/sales_attachment/' . $attachment['attachment_key']);
                                if (!empty($attachment['external'])) {
                                    $attachment_url = $attachment['external_link'];
                                }
                                ?>
                                <div class="mbot15 row inline-block full-width"
                                     data-attachment-id="<?php echo $attachment['id']; ?>">
                                    <div class="col-md-8">
                                        <div class="pull-left"><i
                                                    class="<?php echo get_mime_class($attachment['filetype']); ?>"></i>
                                        </div>
                                        <a href="<?php echo $attachment_url; ?>"
                                           target="_blank"><?php echo $attachment['file_name']; ?></a>
                                        <br/>
                                        <small class="text-muted"> <?php echo $attachment['filetype']; ?></small>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <?php if ($attachment['visible_to_customer'] == 0) {
                                            $icon = 'fa-toggle-off';
                                            $tooltip = _l('show_to_customer');
                                        } else {
                                            $icon = 'fa-toggle-on';
                                            $tooltip = _l('hide_from_customer');
                                        }
                                        ?>
                                        <a href="#" data-toggle="tooltip"
                                           onclick="toggle_file_visibility(<?php echo $attachment['id']; ?>,<?php echo $invoice->id; ?>,this); return false;"
                                           data-title="<?php echo $tooltip; ?>"><i class="fa <?php echo $icon; ?>"
                                                                                   aria-hidden="true"></i></a>
                                        <?php if ($attachment['staffid'] == get_staff_user_id() || is_admin()) { ?>
                                            <a href="#" class="text-danger"
                                               onclick="delete_invoice_attachment(<?php echo $attachment['id']; ?>); return false;"><i
                                                        class="fa fa-times"></i></a>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <?php if (has_permission('payments', '', 'create') && $invoice->total > 0 && ($invoice->status != 2 && $invoice->status != 5)) { ?>
                            <?php if (isset($lid) && $lid > 0) { ?>
                                <a href="#"
                                   onclick="record_payment(<?php echo $invoice->id; ?>, <?php echo isset($lid) ? $lid : "" ?>, 'lid'); return false;"
                                   class="mleft10 pull-right btn btn-success"><?php echo _l('invoice_record_payment'); ?></a>
                            <?php } elseif (isset($pid) && $pid > 0) { ?>
                                <a href="#"
                                   onclick="record_payment(<?php echo $invoice->id; ?>, <?php echo isset($pid) ? $pid : "" ?>, 'pid'); return false;"
                                   class="mleft10 pull-right btn btn-success"><?php echo _l('invoice_record_payment'); ?></a>
                            <?php } elseif (isset($eid) && $eid > 0) { ?>
                                <a href="#"
                                   onclick="record_payment(<?php echo $invoice->id; ?>, <?php echo isset($eid) ? $eid : "" ?>, 'eid'); return false;"
                                   class="mleft10 pull-right btn btn-success"><?php echo _l('invoice_record_payment'); ?></a>
                            <?php } ?>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <hr/>
                        <div class="paymentNotes">
                            <?php if ($invoice->clientnote != '') { ?>
                                <div class="col-md-12 row mtop15">
                                    <p class="bold "><?php echo _l('invoice_note'); ?></p>
                                    <p><?php echo $invoice->clientnote; ?></p>
                                </div>
                            <?php } ?>
                            <?php if ($invoice->terms != '') { ?>
                                <div class="col-md-12 row mtop15">
                                    <p class="bold "><?php echo _l('terms_and_conditions'); ?></p>
                                    <p><?php echo $invoice->terms; ?></p>
                                </div>
                            <?php } ?>
                            <div class="col-md-12 no-padding">
                                <?php
                                $total_payments = count($invoice->payments);
                                if ($total_payments > 0) { ?>
                                    <h4 class="bold"><?php echo _l('invoice_received_payments'); ?></h4>
                                    <?php include_once(APPPATH . 'views/admin/invoices/invoice_payments_table.php'); ?>
                                <?php } else { ?>
                                    <h5 class="bold mtop15 pull-left text-center"><?php echo _l('invoice_no_payments_found'); ?></h5>
                                <?php } ?>
                            </div>
                        </div>


                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_tasks">
                    <?php init_relation_tasks_table(array('data-new-rel-id' => $invoice->id, 'data-new-rel-type' => 'invoice')); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab_reminders">
                    <?php if ($invoice->status == 1 || $invoice->status == 3) { ?>
                        <div class="pull-left mright10"><span class="due_lbl"><b>DUE : </b></span><span
                                    class="due_lbl_val"><?php echo date('m/d/Y', strtotime(to_sql_date($invoice->duedate))) ?></span>
                        </div>
                    <?php } ?>
                    <a href="#" class="btn btn-info btn-xs pull-left" data-toggle="modal"
                       data-target=".reminder-modal-invoice-<?php echo $invoice->id; ?>"><i
                                class="fa fa-bell-o"></i> <?php echo _l('invoice_set_reminder_title'); ?></a>
                    <?php echo format_invoice_status($invoice->status, 'pull-right mtop5'); ?>
                    <?php render_datatable(array(_l('reminder_description'), _l('reminder_date'), _l('reminder_staff'), _l('reminder_is_notified'), _l(''),), 'reminders'); ?>
                    <?php $this->load->view('admin/includes/modals/reminder', array('id' => $invoice->id, 'name' => 'invoice', 'members' => $members, 'reminder_title' => _l('invoice_set_reminder_title'))); ?>
                </div>
                <?php if ($invoice->recurring > 0) { ?>
                    <div role="tabpanel" class="tab-pane" id="tab_child_invoices">
                        <?php if (count($invoice_recurring_invoices)) { ?>
                            <p class="mtop30 bold"><?php echo _l('invoice_add_edit_recurring_invoices_from_invoice'); ?></p>
                            <br/>
                            <ul class="list-group">
                                <?php foreach ($invoice_recurring_invoices as $recurring) { ?>
                                    <li class="list-group-item">
                                        <a href="<?php echo admin_url('invoices/list_invoices#' . $recurring->id); ?>"
                                           onclick="init_invoice(<?php echo $recurring->id; ?>); return false;"
                                           target="_blank"><?php echo format_invoice_number($recurring->id); ?>
                                            <span class="pull-right bold"><?php echo format_money($recurring->total, $recurring->symbol); ?></span>
                                        </a>
                                        <br/>
                                        <span class="inline-block mtop10">
                     <?php echo '<span class="bold">' . _d($recurring->date) . '</span>'; ?><br/>
                                            <?php echo format_invoice_status($recurring->status, '', false); ?>
                     </span>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } else { ?>
                            <p class="bold"><?php echo _l('no_child_found', _l('invoices')); ?></p>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div role="tabpanel" class="tab-pane ptop10" id="tab_activity">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="activity-feed">
                                <?php foreach ($activity as $log) { ?>
                                    <div class="feed-item">
                                        <div class="row">
                                            <div class="col-xs-1 activity_icon">
                                                <div class="icon_section text-center mright10">
                                                    <a href="javascript:;">
                                                        <i class="fa fa-money menu-icon"></i>
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-xs-10 activity_desc">
                                                <div class="text">
                                                    <?php
                                                    $additional_data = '';
                                                    if (!empty($log['additional_data'])) {
                                                        $additional_data = unserialize($log['additional_data']);
                                                        if ($log['staffid'] == get_staff_user_id()) {
                                                            echo ($log['staffid'] == 0) ? _l($log['description'], $additional_data) : "You " . str_replace($log['full_name'], "", _l($log['description'], $additional_data));
                                                        } else {
                                                            echo ($log['staffid'] == 0) ? _l($log['description'], $additional_data) : "You" . ' - ' . _l($log['description'], $additional_data);
                                                        }
                                                    } else {
                                                        if ($log['staffid'] == get_staff_user_id()) {
                                                            echo "You ";
                                                        } else {
                                                            echo $log['full_name'] . ' - ';
                                                        }
                                                        echo _l($log['description'], '', false);
                                                    }
                                                    ?>
                                                </div>
                                                <div class="date"><?php echo date('D d/m/Y ', strtotime($log['date'])) ?>
                                                    at <?php echo date('h:i A', strtotime($log['date'])) ?>
                                                    (<?php echo time_ago($log['date']); ?>)
                                                </div>
                                            </div>
                                            <div class="col-xs-1">
                                                <?php if ($log['staffid'] != 0) { ?>
                                                    <a href="javascript:void(0)">
                                                        <?php echo staff_profile_image($log['staffid'], array('staff-profile-image-small pull-left mright5'));
                                                        ?>
                                                    </a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane ptop10" id="tab_views">
                    <?php
                    $views_activity = get_views_tracking('invoice', $invoice->id);
                    foreach ($views_activity as $activity) { ?>
                        <p class="text-success no-margin">
                            <?php echo _l('view_date') . ': ' . _dt($activity['date']); ?>
                        </p>
                        <p class="text-muted">
                            <?php echo _l('view_ip') . ': ' . $activity['view_ip']; ?>
                        </p>
                        <hr/>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/invoices/invoice_send_to_client'); ?>
<script>
    init_items_sortable(true);
    init_btn_with_tooltips();
    init_datepicker();
    init_selectpicker();
    init_form_reminder();
</script>