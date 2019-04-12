<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
* Added By : Vaidehi
* Dt : 12/14/2018
* to get current brand id
*/
$brandid        = get_user_session();
$session_data   = get_session_data();
$is_sido_admin  = $session_data['is_sido_admin'];
$lid            = $this->_instance->input->get('lid');

$baseCurrencySymbol = $this->_instance->currencies_model->get_base_currency()->symbol;

$aColumns     = array(
    'tblproposals.id',
    'name',
    'proposal_date',
    'proposal_duedate',
    // 'date',
    // 'open_till',
    // '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tblproposals.id and rel_type="proposal" ORDER by tag_order ASC) as tags',
    // 'datecreated',
    // 'status',
);

$sIndexColumn = "id";
$sTable       = 'tblproposals';

$where   = array();

array_push($where, ' AND deleted = 0');
if($brandid > 0){
    array_push($where, 'AND brandid =' . $brandid);
}

$filter = array();

$join = array();
if(isset($lid)) {
    array_push($join, 'INNER JOIN tblproposalusers ON tblproposals.id = tblproposalusers.proposal_id AND lead_id =' . $lid);
}

$aColumns = do_action('proposals_table_sql_columns', $aColumns);

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'proposal_id'
));

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();

    $row[]  = $aRow['name'];

    $row[]  = _d($aRow['proposal_date']);

    $row[]  = _d($aRow['proposal_duedate']);

    $options = '';
    
    if (has_permission('proposals','','edit')) {
        $options = icon_btn('proposals/proposal/' . $aRow['id'], 'pencil-square-o');
        if(isset($lid)) {
            $options = icon_btn('proposals/proposal/' . $aRow['id'] . "?lid=" . $lid, 'pencil-square-o');
        }
        
    }
    
    if (has_permission('proposals','','delete')) {
        $row[]   = $options .= icon_btn('proposals/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
    } else {
        $row[]   = $options .= "";
    }

    $hook_data = do_action('proposals_table_row_data', array(
        'output'    => $row,
        'row'       => $aRow
    ));

    $row = $hook_data['output'];

    $output['aaData'][] = $row;
}
