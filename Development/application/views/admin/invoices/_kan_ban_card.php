<?php
/*if (isset($invoice['rel_type']) && $invoice['rel_id'] > 0) {
    $event = get_event_name($invoice['rel_type'], $invoice['rel_id']);
    $invoice['eventtypename'] = isset($event->name) ? $event->name : "";
}*/
/*if ($invoice['status'] == $status['statusid']) {*/
/*echo "<pre>";
print_r($invoice);*/

$session_data = get_session_data();
$user_id = $session_data['staff_user_id'];

$class = "";
if ($count <= 3) {
    $class = "first_row";
}
$session_data = get_session_data();
$user_id = $session_data['staff_user_id'];
$CI =& get_instance();
$CI->db->select('pinid as pinned');
$CI->db->from('tblpins');
$CI->db->where('userid', $user_id);
$CI->db->where('pintype', 'Invoice');
$CI->db->where('pintypeid', $invoice['id']);
$result = $CI->db->get()->row();

$favorit = $CI->db->select('favoriteid')->from('tblfavorites')->where('favtype = "invoice" AND typeid=' . $invoice['id'] . ' AND userid=' . $user_id)->get()->row();

$invoiceaddress = "";
if (!empty($invoice['invoicecity'])) {
    $invoiceaddress .= $invoice['invoicecity'];
}
if (!empty($invoice['invoicestate'])) {
    $invoiceaddress .= ", ";
    $invoiceaddress .= $invoice['invoicestate'];
}
?>
    <li data-invoice-id="<?php echo $invoice['id']; ?>"
        class="col-sm-6 col-lg-4 kanban-card-block kanban-card <?php echo $class ?>">
        <div class="invoice_card_image">
            <img src="<?php echo base_url() ?>assets/images/default_banner.jpg"/>
        </div>
        <div class="panel-body card-body">
            <div class="row">
                <div class="pin-block">
                    <i class="fa fa-fw fa-thumb-tack invoice-pin <?php echo isset($result->pinned) ? "pinned" : ""; ?>"
                       title="<?php echo isset($result->pinned) ? "Unpin from Home" : "Pin to Home"; ?>"
                       id="<?php echo $invoice['id']; ?>"
                       invoice_id="<?php echo $invoice['id']; ?>">
                    </i>
                </div>
                <div class="col-xs-12 card-name">
                    <div class="invoice-body text-center ">
                        <div class="invoice_icon"><i class="fa fa-file-text-o"></i></div>
                        <div class="invoice_number"><span><?php echo format_invoice_number($invoice['id']) ?></span>
                        </div>
                        <div class="invoice_issued">
                            <span class="date_issued">Issued: </span>
                            <b><?php echo date('M d Y',strtotime($invoice['date']))?></b>
                        </div>
                        <div class="invoice_due">
                            <span class="date_due">Due: </span>
                            <b><?php echo date('M d Y',strtotime($invoice['duedate']))?></b>
                            <span class="days_remain"><?php
                                if(strtotime($invoice['duedate']) > strtotime("now")){
                                    echo "(".after_time($invoice['duedate']).")";
                                } else{
                                    echo "(".time_ago($invoice['duedate']).")";
                                }?>
                            </span>
                        </div>
                    </div>
                    <?php if(isset($invoice['project_id']) || isset($invoice['leadid']) || isset($invoice['eventid'])){ ?>
                    <div class="invoice_event">
                        <?php
                        if(isset($invoice['project_id'])&& $invoice['project_id'] > 0){
                            $eventimage = project_profile_image($invoice['project_id']);
                            $eventname=get_project_col_by_id($invoice['project_id'],"name");
                            $eventdate=get_project_col_by_id($invoice['project_id'],"eventstartdatetime");
                        }elseif (isset($invoice['leadid'])&& $invoice['leadid'] > 0){
                            $eventimage = lead_profile_image($invoice['leadid']);
                            $eventname=get_lead_col_by_id($invoice['leadid'],'name');
                            $eventdate=get_lead_col_by_id($invoice['leadid'],'eventstartdatetime');
                        }elseif (isset($invoice['eventid'])&& $invoice['eventid'] > 0){
                            $eventimage = project_profile_image($invoice['eventid']);
                            $eventname=get_project_col_by_id($invoice['eventid'],"name");
                            $eventdate=get_project_col_by_id($invoice['eventid'],'eventstartdatetime');
                        }

                        ?>
                        <div class="event_iamge inline-block">
                            <?php echo $eventimage ; ?>
                        </div>
                        <div class="event_details inline-block">

                            <span class="event_name">
                                <?php echo $eventname; ?>
                            </span>
                            <span class="event_date">
                                <?php echo date('l, F d, Y',strtotime($eventdate)); ?>
                            </span>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <div class="right-links">
                    <div class="show-act-block"><?php
                        $options = "<div><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
                        $options .= '<li><a href=' . admin_url() . 'invoices/list_invoices#' . $invoice['id'] . ' class=""><i class="fa fa-eye"></i><span>View</span></a></li>';
                        /*if (has_permission('invoice', '', 'edit')) {*/
                        $options .= '<li><a href=' . admin_url() . 'invoices/invoice/' . $invoice['id'] . ' class=""><i class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>';
                        /*} else {
                            $options .= "";
                        }*/
                        $options .= "</ul></div>";
                        echo $options;
                        ?>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="card-footer">
                    <div class="invoice_status">
                        <?php echo format_invoice_status($invoice['tblinvoices.status']); ?>
                    </div>
                    <div class="invoice_total">
                        <?php echo format_money($invoice['total'], $invoice['symbol']); ?>
                    </div>
                </div>
            </div>
    </li>
<?php //} ?>