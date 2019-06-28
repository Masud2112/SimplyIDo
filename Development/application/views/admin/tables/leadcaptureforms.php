<?php
/**
 * Added By: Vaidehi
 * Dt: 01/29/2017
 * Questionnaire Module
 */
defined('BASEPATH') OR exit('No direct script access allowed');
$brandid = get_user_session();

$aColumns = array(
    'name',
    'createddate'
);

$sIndexColumn = "id";
$sTable = 'tblleadcaptureforms';

$where = array();
array_push($where, ' AND deleted = 0');
array_push($where, ' AND brandid =' . $brandid);
$rel_id = 0;
if (isset($_GET['pid'])) {
    $rel_id = $_GET['pid'];
}
array_push($where, ' AND rel_id =' . $rel_id);

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), $where, array('id','createdby','updatedby','updateddate'));

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
    $row[] = '<div class="checkbox taskCheckBox"><input type="checkbox" value="'.$aRow['id'].'"><label></label></div>';
    if($aRow['updatedby']=="" && $aRow['updateddate']==""){
        $created=_l("created");
        $user = staff_profile_image($aRow['createdby']);
        $date = strtoupper(date('D, M j, Y',strtotime($aRow['createddate'])));
    }else{
        $created=_l("updated");
        $user = staff_profile_image($aRow['updatedby']);
        $date = strtoupper(date('D, M j, Y',strtotime($aRow['updateddate'])));
    }
    $row[] = '<div class="leadcaptureName">'.$aRow['name'].'</div>';

    $row[] = "<div class='ceratedupdated'><div class='user inline-block mright10'>".$user."</div><div class='inline-block userDet'><b>".strtoupper($created)."</b><br />".$date."</div></div>";

    $row[] = "<div class='fomdisplaymethods'><select id='formmethods' class='methods selectpicker'>
<option value=''>"._l('choosemethods')."</option>
<option value=''>"._l('linktoform')."</option>
<option value=''>"._l('linktodialogwindow')."</option>
<option value=''>"._l('insertonwebpage')."</option>
<option value=''>"._l('facebooklink')."</option>
</select></div>";

    $options = "<div class='pull-right'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";

    $options .= '<li><a href=' . admin_url() . 'leadcaptureforms/form/' . $aRow['id'] . ' class="" title="'._l("edit").'"><i class="fa fa-pencil-square-o"></i><span>'._l("edit").'</span></a></li>';

    $options .= '<li><a href=' . admin_url() . 'leadcaptureforms/delete/' . $aRow['id'] . ' class="" title="'._l("delete").'"><i class="fa fa-pencil-square-o"></i><span>'._l("delete").'</span></a></li>';

    $options .= "</ul></div>";
    $row[] = $options;

    $output['aaData'][] = $row;
}
