<?php
defined('BASEPATH') or exit('No direct script access allowed');

$project_id = $this->_instance->input->post('project_id');
$lid = $this->_instance->input->get('lid');
$pid = $this->_instance->input->get('pid');
$eid = $this->_instance->input->get('eid');
$brandid = get_user_session();

$aColumns = array(
    'number',
    'total',
    'YEAR(date) as year',
    'date',
    //'tbladdressbook.firstname',
    //'tblstaff.firstname',
    //'tblprojects.name as project_name',
    'duedate',
    'tblinvoices.status',
);

$sIndexColumn = "id";
$sTable = 'tblinvoices';

$join = array(
    'LEFT JOIN tbladdressbook ON tbladdressbook.addressbookid = tblinvoices.clientid',
    'LEFT JOIN tblcurrencies ON tblcurrencies.id = tblinvoices.currency',
    //'LEFT JOIN tblprojects ON tblprojects.id = tblinvoices.project_id',
    'LEFT JOIN tblstaff ON tblstaff.staffid = tblinvoices.sale_agent',
);

$custom_fields = get_table_custom_fields('invoice');

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);

    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_' . $key . ' ON tblinvoices.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

$where = array();
array_push($where, ' AND tblinvoices.brandid =' . $brandid);
if (isset($pid)) {
    $this->_instance->db->select('id');
    $this->_instance->db->where('(parent = ' . $pid . ' OR id = ' . $pid . ')');
    $this->_instance->db->where('deleted', 0);
    $related_project_ids = $this->_instance->db->get('tblprojects')->result_array();
    $related_project_ids = array_column($related_project_ids, 'id');
    if (!empty($related_project_ids)) {
        $related_project_ids = implode(",", $related_project_ids);
        array_push($where, ' AND (tblinvoices.project_id in(' . $related_project_ids . ') OR tblinvoices.eventid in(' . $related_project_ids . '))');
    } else {
        array_push($where, ' AND tblinvoices.project_id =' . $pid);
    }
} else if (isset($lid)) {
    array_push($where, ' AND tblinvoices.leadid =' . $lid);
} else if (isset($eid)) {
    array_push($where, ' AND tblinvoices.eventid =' . $eid);
}

$filter = array();

// if ($this->_instance->input->post('not_sent')) {
//     array_push($filter, 'AND sent = 0 AND tblinvoices.status NOT IN(2,5)');
// }
// if ($this->_instance->input->post('not_have_payment')) {
//     array_push($filter, 'AND tblinvoices.id NOT IN(SELECT invoiceid FROM tblinvoicepaymentrecords) AND tblinvoices.status != 5');
// }
// if ($this->_instance->input->post('recurring')) {
//     array_push($filter, 'AND recurring > 0');
// }


//Filter by Top Progress bar
$statuses = $this->_instance->invoices_model->get_statuses();
$statusIds = array();
foreach ($statuses as $status) {
    if ($this->_instance->input->post('invoices_' . $status)) {
        array_push($statusIds, $status);
    }
}

//echo '<pre>'; print_r($this->_instance->input->post('status')); 
// Filter by Dropdown value
if ($this->_instance->input->post('status')) {
    $by_status = $this->_instance->input->post('status');
    array_push($statusIds, $by_status);
}

if (count($statusIds) > 0) {
    array_push($filter, 'AND tblinvoices.status IN (' . implode(', ', $statusIds) . ')');
}

if ($this->_instance->input->post('invoicedate')) {
    $invoicedate = $this->_instance->input->post('invoicedate');
    $invoicedate = explode("-", $invoicedate);
    $invoicestartdate = date("Y-m-d", strtotime($invoicedate[0]));
    $invoiceenddate = date("Y-m-d", strtotime($invoicedate[1]));
    array_push($where, 'AND date(tblinvoices.duedate) between "' . $invoicestartdate . '" AND "' . $invoiceenddate . '"');
}

if ($this->_instance->input->post('assigned')) {
    array_push($where, 'AND tblinvoices.sale_agent = ' . $this->_instance->input->post('assigned'));
}

$agents = $this->_instance->invoices_model->get_sale_agents();
$agentsIds = array();
foreach ($agents as $agent) {
    if ($this->_instance->input->post('sale_agent_' . $agent['sale_agent'])) {
        array_push($agentsIds, $agent['sale_agent']);
    }
}
if (count($agentsIds) > 0) {
    array_push($filter, 'AND sale_agent IN (' . implode(', ', $agentsIds) . ')');
}

$modesIds = array();
foreach ($data['payment_modes'] as $mode) {
    if ($this->_instance->input->post('invoice_payments_by_' . $mode['id'])) {
        array_push($modesIds, $mode['id']);
    }
}
if (count($modesIds) > 0) {
    array_push($where, 'AND tblinvoices.id IN (SELECT invoiceid FROM tblinvoicepaymentrecords WHERE paymentmode IN ("' . implode('", "', $modesIds) . '"))');
}

$years = $this->_instance->invoices_model->get_invoices_years();
$yearArray = array();
foreach ($years as $year) {
    if ($this->_instance->input->post('year_' . $year['year'])) {
        array_push($yearArray, $year['year']);
    }
}
if (count($yearArray) > 0) {
    array_push($where, 'AND YEAR(date) IN (' . implode(', ', $yearArray) . ')');
}

if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

if (is_numeric($clientid)) {
    array_push($where, 'AND tblinvoices.clientid=' . $clientid);
}

// if ($project_id) {
//     array_push($where, 'AND project_id='.$project_id);
// }

// if ($project_id) {
//     array_push($where, 'AND project_id='.$project_id);
// }

if (!has_permission('invoices', '', 'view')) {
    array_push($where, 'AND tblinvoices.addedfrom=' . get_staff_user_id());
}

$aColumns = do_action('invoices_table_sql_columns', $aColumns);

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblinvoices.id',
    'tblinvoices.clientid',
    'symbol',
    'project_id',
    //'tbladdressbook.lastname',
    //'tblstaff.lastname as stafflastname',
    'tblstaff.staffid',
    'tbladdressbook.addressbookid'
));
$output = $result['output'];
$rResult = $result['rResult'];
$CI =& get_instance();
foreach ($rResult as $aRow) {
    $row = array();

    $numberOutput = '';

    // If is from client area table
    if (is_numeric($clientid) || $project_id) {
        $numberOutput = '<a href="' . admin_url('invoices/list_invoices#' . $aRow['id']) . '" target="_blank">' . format_invoice_number($aRow['id']) . '</a>';
    } else {
        if (isset($lid)) {
            $numberOutput = '<a href="' . admin_url('invoices/list_invoices?lid=' . $lid . '#' . $aRow['id']) . '">' . format_invoice_number($aRow['id']) . '</a>';
        } elseif (isset($pid)) {
            $numberOutput = '<a href="' . admin_url('invoices/list_invoices?pid=' . $pid . '#' . $aRow['id']) . '">' . format_invoice_number($aRow['id']) . '</a>';
        } elseif (isset($eid)) {
            $numberOutput = '<a href="' . admin_url('invoices/list_invoices?eid=' . $eid . '#' . $aRow['id']) . '">' . format_invoice_number($aRow['id']) . '</a>';
        } else {
            $numberOutput = '<a href="' . admin_url('invoices/list_invoices#' . $aRow['id']) . '">' . format_invoice_number($aRow['id']) . '</a>';
        }
    }
    /*if(!empty($invoice)){
        $numberOutput = '<a href="javascript:void(0)">' . format_invoice_number($aRow['id']) . '</a>';
    }*/
    $row[] = $numberOutput;


    $row[] = format_money($aRow['total'], $aRow['symbol']);

    //$row[] = format_money($aRow['total_tax'], $aRow['symbol']);

    $row[] = $aRow['year'];

    $row[] = _d($aRow['date']);

    //$row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['customer'] . '</a>';
    //Added By Avni on 11/22/2017

    /*$row[] = '<a href="javascript:void(0);">' .
            addressbook_profile_image( $aRow['addressbookid'], array(
                        'staff-profile-image-small mright5'
                    ), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => $aRow['tbladdressbook.firstname'] . ' ' . $aRow['lastname']
            )) . '</a>';            

    $row[] = '<a href="javascript:void(0);">' .
            staff_profile_image( $aRow['staffid'], array(
                        'staff-profile-image-small mright5'
                    ), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => $aRow['tblstaff.firstname'] . ' ' . $aRow['stafflastname']
            )) . '</a>';*/


    //$row[] = '<a href="'.admin_url('projects/view/'.$aRow['project_id']).'">'.$aRow['project_name'].'</a>';;

    $row[] = _dt($aRow['duedate'], false);

    $row[] = format_invoice_status($aRow['tblinvoices.status']);

    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $hook = do_action('invoices_table_row_data', array(
        'output' => $row,
        'row' => $aRow
    ));

    $row = $hook['output'];

    $output['aaData'][] = $row;
}