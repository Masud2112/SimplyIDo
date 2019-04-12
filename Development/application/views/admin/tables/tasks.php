<?php
defined('BASEPATH') or exit('No direct script access allowed');
$brandid = get_user_session();
$session_data   = get_session_data();
$user_id = $session_data['staff_user_id'];
$is_sido_admin  = $session_data['is_sido_admin'];
$lid = $this->_instance->input->get('lid');
$pid = $this->_instance->input->get('pid');
$eid = $this->_instance->input->get('eid');
$hasPermissionEdit = has_permission('tasks', '', 'edit');
$bulkActions = $this->_instance->input->get('bulk_actions');
$aColumns = array(
    '1',
    '(SELECT pinid FROM tblpins WHERE tblpins.userid='.$user_id.' and tblpins.pintype = "Task" and tblpins.pintypeid = tblstafftasks.id) as pinned',
    'tblstafftasks.name',
    'duedate',
    '3',
    '4',
    //'27',
    //'(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbltags_in JOIN tbltags ON tbltags_in.tag_id = tbltags.id WHERE rel_id = tblstafftasks.id and rel_type="task" ORDER by tag_order ASC) as tags',
   '(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM tblstafftaskassignees JOIN tblstaff ON tblstaff.staffid = tblstafftaskassignees.staffid WHERE taskid=tblstafftasks.id ORDER BY tblstafftaskassignees.staffid) as assignees',
    'priority'
   // 'tbltasksstatus.name as status'
);

if ($bulkActions) {
    array_unshift($aColumns, '1');
}

$sIndexColumn = "id";
$sTable       = 'tblstafftasks';

$join          = array();
//Added on 11/10 by Avni
$join = array(
    'LEFT JOIN tbltasksstatus ON tbltasksstatus.id = tblstafftasks.status'
 );

$where = array();
array_push($where, ' AND tblstafftasks.deleted = 0');
if($brandid > 0){
    array_push($where, ' AND tblstafftasks.brandid =' . $brandid);
}  
else if($is_sido_admin > 0){
    array_push($where, ' AND tblstafftasks.brandid =0');
} 

//Added on 11/08 by Purvi 

//include_once(APPPATH . 'views/admin/tables/includes/tasks_filter.php');

$custom_fields = get_table_custom_fields('tasks');

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_'.$key);
    array_push($customFieldsColumns,$selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_' . $key . ' ON tblstafftasks.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

if ($this->_instance->input->post('status')) {
    $by_status = $this->_instance->input->post('status');
    array_push($where, 'AND status =' . $by_status);
}

// if($this->_instance->input->post('taskdate')){
//     $taskdate = $this->_instance->input->post('taskdate');
//     if($taskdate == 'today'){
//         array_push($where, 'AND startdate = "'.date('Y-m-d').'"'); // AND status != 5
//     }elseif($taskdate == 'duedate'){
//         array_push($where,'AND (duedate < "' . date('Y-m-d') . '" AND duedate IS NOT NULL)'); //AND status != 5
//     }elseif($taskdate == 'upcoming'){
//         array_push($where,'AND (startdate > "' . date('Y-m-d') . '")'); // AND status != 5
//     }
// }

if ($this->_instance->input->post('taskdate')) {
    $taskdate = $this->_instance->input->post('taskdate');
    $taskdate = explode("-", $taskdate);
    $taskstartdate = date("Y-m-d",strtotime($taskdate[0]));
    $taskedenddate = date("Y-m-d",strtotime($taskdate[1]));
    array_push($where, 'AND date(duedate) between "' . $taskstartdate .'" AND "'. $taskedenddate.'"'); 
}

if ($this->_instance->input->post('assigned')) {
        array_push($where, 'AND (tblstafftasks.id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid = '.$this->_instance->input->post('assigned').'))');
}
// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->_instance->db->query('SET SQL_BIG_SELECTS=1');
}

if(isset($lid)) {
    array_push($where, ' AND rel_type = "lead"');
    array_push($where, ' AND rel_id = '. $lid);
}
/* Added by Purvi on 12-20-2017 for Project wise filter */
if(isset($pid)) {
    $this->_instance->db->select('id');
    $this->_instance->db->where('(parent = '.$pid.' OR id = '.$pid.')');
    $this->_instance->db->where('deleted', 0);
    $related_project_ids = $this->_instance->db->get('tblprojects')->result_array();
    $related_project_ids = array_column($related_project_ids, 'id');
    if(!empty($related_project_ids)){
        $related_project_ids = implode(",", $related_project_ids);
        array_push($where, ' AND rel_id in(' . $related_project_ids .')');
        array_push($where, ' AND rel_type in("project", "event")');
    }else{
        array_push($where, ' AND rel_id = ' . $pid);
        array_push($where, ' AND rel_type = "project"');
    }
}
if(isset($eid)) {
    array_push($where, ' AND rel_type = "event"');
    array_push($where, ' AND rel_id = '. $eid);
}

$aColumns = do_action('tasks_table_sql_columns', $aColumns);

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
        'tblstafftasks.id',
        'tblstafftasks.manualprogress',
        'tblstafftasks.progresspercent',
        'rel_type',
        'rel_id',
        tasks_rel_name_select_query() . ' as rel_name',
        //'billed',
        // '(SELECT GROUP_CONCAT(tbltags.name) FROM tbltags INNER JOIN tbltaskstags ON tbltaskstags.tagid = tbltags.id WHERE tbltaskstags.taskid=tblstafftasks.id and tbltags.deleted=0) as tags',
        '(SELECT staffid FROM tblstafftaskassignees WHERE taskid=tblstafftasks.id AND staffid='.get_staff_user_id().') as is_assigned',
        '(SELECT GROUP_CONCAT(staffid SEPARATOR ",") FROM tblstafftaskassignees WHERE taskid=tblstafftasks.id ORDER BY tblstafftaskassignees.staffid) as assignees_ids',
         //'tblstafftasks.name as status',
         'tblstafftasks.status as statusid',
         'color'
    )
);

$output  = $result['output'];
$rResult = $result['rResult'];
$task_statuses = $this->_instance->tasks_model->get_status();

foreach ($rResult as $aRow) {
    $row = array();
    $finished = total_rows('tbltaskchecklists', array(
              'taskid' => $aRow['id'],
              'finished' => 1,
              ));

    $total_tasks = total_rows('tbltaskchecklists', array(
                'taskid' => $aRow['id'],
                ));
    $complete_task_per = $task_per = 0;
    if($total_tasks != "" && $finished != ""){
        $task_per = 100/$total_tasks;
        $complete_task_per = $task_per * $finished;
        if(is_float($complete_task_per)){
            $complete_task_per = round($complete_task_per);
        }else{
            $complete_task_per = $complete_task_per;
        }
        
    }
   // if ($bulkActions) {
        $row[] = '<div class="checkbox taskCheckBox"><input type="checkbox" value="'.$aRow['id'].'"><label></label></div>';
   // }

    if($aRow['pinned'] > 0){
        $row[] =  '<i class="fa fa-fw fa-thumb-tack task-pin pinned" title="Unpin from dashboard" id="'.$aRow['id'].'" task_id="'.$aRow['id'].'"></i>';
    }else{
        $row[] =  '<i class="fa fa-fw fa-thumb-tack task-pin" title="Pin to dashboard" id="'.$aRow['id'].'" task_id="'.$aRow['id'].'"></i>';
    }
    if(isset($lid)){
        $outputName = '<a href="'.admin_url('tasks/dashboard/'.$aRow['id']. '?lid='.$lid).'" class="display-block main-tasks-table-href-name'.(!empty($aRow['rel_id']) ? ' mbot5' : '').'">' . $aRow['tblstafftasks.name'] . '</a>';
    }elseif(isset($pid)){
        $outputName = '<a href="'.admin_url('tasks/dashboard/'.$aRow['id']. '?pid='.$pid).'" class="display-block main-tasks-table-href-name'.(!empty($aRow['rel_id']) ? ' mbot5' : '').'">' . $aRow['tblstafftasks.name'] . '</a>';
    }elseif(isset($eid)){
        $outputName = '<a href="'.admin_url('tasks/dashboard/'.$aRow['id']. '?eid='.$eid).'" class="display-block main-tasks-table-href-name'.(!empty($aRow['rel_id']) ? ' mbot5' : '').'">' . $aRow['tblstafftasks.name'] . '</a>';
    }else{
        $outputName = '<a href="'.admin_url('tasks/dashboard/'.$aRow['id']).'" class="display-block main-tasks-table-href-name'.(!empty($aRow['rel_id']) ? ' mbot5' : '').'">' . $aRow['tblstafftasks.name'] . '</a>';
    }
    
    if ($aRow['rel_name']) {

         $relName = task_rel_name($aRow['rel_name'], $aRow['rel_id'], $aRow['rel_type']);

         $link = task_rel_link($aRow['rel_id'], $aRow['rel_type']);

         $outputName .= '<span class="hide"> - </span><a class="text-muted" data-toggle="tooltip" title="' . _l('task_related_to') . '" href="' . $link . '">' . $relName . '</a>';

    }

    $row[] = $outputName;

    //$row[] = _dt($aRow['startdate']);

    $row[] = _dt($aRow['duedate']);
    if($aRow['manualprogress']==1){
        $complete_task_per = $aRow['progresspercent'];
    }
    if($complete_task_per > 0){
        $row[] = '<div class="progress progress-md"><div class="btn-primary bg-success progress-bar-striped progress-bar-animated" style="width: '.$complete_task_per.'%" role="progressbar">'.$complete_task_per.'%</div></div>';
    }else{
        $row[] = '<div class="progress progress-md"><div class="btn-primary bg-success progress-bar-striped progress-bar-animated zero-progress" style="width: '.$complete_task_per.'%" role="progressbar">'.$complete_task_per.'%</div></div>';
    }
    
    //$row[] = render_tags($aRow['tags']);
    
    //$row[] = render_tags($aRow['tags']);

    $statuscontent = '<div class="divInline_blk"> <span class="taskstatuscolor" style="background-color:' . $aRow['color'] . ';"></span>';
    $statuscontent .= '<select task_id="'.$aRow['id'].'" class="selectpicker taskstatus">';
    foreach ($task_statuses as $svalue) {
        //echo '<pre>'; print_r($svalue['id']);
        $selected = "";
        if($aRow['statusid'] == $svalue['id']){
            $selected = "selected='selected'";
        }else{
            $selected = "";
        }
        $statuscontent .= '<option  value="'.$svalue['id'].'" '.$selected.'>'.$svalue['name'].'</option>';
    }
    $statuscontent .= '</select></div>';
    $row[] = $statuscontent;


    $outputAssignees = '<div class="divInline_blk"> ';

    $assignees        = explode(',', $aRow['assignees']);
    $assigneeIds        = explode(',', $aRow['assignees_ids']);
    $export_assignees = '';
    foreach ($assignees as $key => $assigned) {
        $assignee_id = $assigneeIds[$key];
        if ($assigned != '') {
            $outputAssignees .= '<a href="javascript:void(0);">' .
            staff_profile_image($assignee_id, array(
                        'staff-profile-image-small mright5'
                    ), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => $assigned
            )) . '</a>';
            // For exporting
            $export_assignees .= $assigned . ', ';
        }
    }
    if ($export_assignees != '') {
        $outputAssignees .= '<span class="hide">' . mb_substr($export_assignees, 0, -2) . '</span> </div>';
    }

   $row[] = $outputAssignees;

    $row[] = '<span class="inline-block">' . task_priority($aRow['priority']) . '</span>';


    // $status = get_task_status_by_id($aRow['status']);
    // $outputStatus = '<span class="inline-block label" style="color:'.$status['color'].';border:1px solid '.$status['color'].'" task-status-table="'.$aRow['status'].'">' . $status['name'];

    // if ($aRow['status'] == 5) {
    //     $outputStatus .= '<a href="#" onclick="unmark_complete(' . $aRow['id'] . '); return false;"><i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip" title="' . _l('task_unmark_as_complete') . '"></i></a>';
    // } else {
    //     $outputStatus .= '<a href="#" onclick="mark_complete(' . $aRow['id'] . '); return false;"><i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" title="' . _l('task_single_mark_as_complete') . '"></i></a>';
    // }

    // $outputStatus .= '</span>';

    // $row[] = $outputStatus;

    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $hook_data = do_action('tasks_table_row_data', array(
        'output' => $row,
        'row' => $aRow
    ));

    $row = $hook_data['output'];

    $options = "<div class='text-right mright10'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";
    if(isset($lid)){
        $options .= icon_url('tasks/dashboard/' . $aRow['id']. (isset($lid) && $lid != "" ? '?lid='.$lid : ''), 'eye', '', array('title'=>'View Dashboard'));

        //$options.='<li><a href='.admin_url().'tasks/dashboard/'.$aRow['id'].(isset($lid) && $lid != "" ? '?lid='.$lid : '').' class="" title="View Dashboard"><i class="fa fa-eye"></i><span>View</span></a></li>';

        if ($hasPermissionEdit) {
            $options .= icon_url('tasks/task/' . $aRow['id']. (isset($lid) && $lid != "" ? '?lid='.$lid : ''), 'pencil-square-o');
            //$options.='<li><a href='.admin_url().'tasks/tasks/'.$aRow['id'].(isset($lid) && $lid != "" ? '?lid='.$lid : '').' class="" title="Edit"><i class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>';
        }   

        if (has_permission('tasks','','delete')) {
            $options .= icon_url('tasks/delete_task/' . $aRow['id']. (isset($lid) && $lid != "" ? '?lid='.$lid : ''), 'remove', '_delete');
           // $options.='<li><a href='.admin_url().'tasks/delete_task/'.$aRow['id'].(isset($lid) && $lid != "" ? '?lid='.$lid : '').' class="_delete" title="Delete"><i class="fa fa-remove"></i><span>Delete</span></a></li>';
        }
    }elseif(isset($pid)){
        $options .= icon_url('tasks/dashboard/' . $aRow['id']. (isset($pid) && $pid != "" ? '?pid='.$pid : ''), 'eye', '', array('title'=>'View Dashboard'));
        //$options.='<li><a href='.admin_url().'tasks/dashboard/'.$aRow['id'].(isset($pid) && $pid != "" ? '?pid='.$pid : '').' class="" title="View Dashboard"><i class="fa fa-eye"></i><span>View</span></a></li>';

        if ($hasPermissionEdit) {
            $options .= icon_url('tasks/task/' . $aRow['id']. (isset($pid) && $pid != "" ? '?pid='.$pid : ''), 'pencil-square-o');
            //$options.='<li><a href='.admin_url().'tasks/tasks/'.$aRow['id'].(isset($pid) && $pid != "" ? '?pid='.$pid : '').' class="" title="Edit"><i class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>';
        }   

        if (has_permission('tasks','','delete')) {
            $options .= icon_url('tasks/delete_task/' . $aRow['id']. (isset($pid) && $pid != "" ? '?pid='.$pid : ''), 'remove', ' _delete');
           // $options.='<li><a href='.admin_url().'tasks/delete_task/'.$aRow['id'].(isset($pid) && $pid != "" ? '?lid='.$pid : '').' class="_delete" title="Delete"><i class="fa fa-remove"></i><span>Delete</span></a></li>';
        }
    }elseif(isset($eid)){
        $options .= icon_url('tasks/dashboard/' . $aRow['id']. (isset($eid) && $eid != "" ? '?eid='.$eid : ''), 'eye', '', array('title'=>'View Dashboard'));
        //$options.='<li><a href='.admin_url().'tasks/dashboard/'.$aRow['id'].(isset($eid) && $eid != "" ? '?eid='.$eid : '').' class="" title="View Dashboard"><i class="fa fa-eye"></i><span>View</span></a></li>';

        if ($hasPermissionEdit) {
            $options .= icon_url('tasks/task/' . $aRow['id']. (isset($eid) && $eid != "" ? '?eid='.$eid : ''), 'pencil-square-o');
            //$options.='<li><a href='.admin_url().'tasks/tasks/'.$aRow['id'].(isset($eid) && $eid != "" ? '?eid='.$eid : '').' class="" title="Edit"><i class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>';
        }   

        if (has_permission('tasks','','delete')) {
            $options .= icon_url('tasks/delete_task/' . $aRow['id']. (isset($eid) && $eid != "" ? '?eid='.$eid : ''), 'remove', ' _delete');
            //$options.='<li><a href='.admin_url().'tasks/delete_task/'.$aRow['id'].(isset($eid) && $eid != "" ? '?eid='.$eid : '').' class="_delete" title="Delete"><i class="fa fa-remove"></i><span>Delete</span></a></li>';
        }
    }else{
        $options .= icon_url('tasks/dashboard/' . $aRow['id'], 'eye', '', array('title'=>'View Dashboard'));
        //$options.='<li><a href='.admin_url().'tasks/dashboard/'.$aRow['id'].' class="" title="View Dashboard"><i class="fa fa-eye"></i><span>View</span></a></li>';
        if ($hasPermissionEdit) {
            $options .= icon_url('tasks/task/' . $aRow['id'], 'pencil-square-o');
            //$options.='<li><a href='.admin_url().'tasks/tasks/'.$aRow['id'].' class="" title="Edit"><i class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>';
        }   

        if (has_permission('tasks','','delete')) {
            $options .= icon_url('tasks/delete_task/' . $aRow['id'], 'remove', '_delete');
            //$options.='<li><a href='.admin_url().'tasks/delete_task/'.$aRow['id'].' class="_delete" title="Delete"><i class="fa fa-remove"></i><span>Delete</span></a></li>';
        }
    }

    $options.="</ul></div>";
    $row[]   = $options;
    $class = 'btn-success no-margin';

    // $tooltip        = '';
    // if ($aRow['billed'] == 1 || $aRow['status'] == 5) {
    //     $class = 'btn-default disabled';
    //     if ($aRow['status'] == 5) {
    //         $tooltip = ' data-toggle="tooltip" data-title="' . format_task_status($aRow['status'], false, true) . '"';
    //     } elseif ($aRow['billed'] == 1) {
    //         $tooltip = ' data-toggle="tooltip" data-title="' . _l('task_billed_cant_start_timer') . '"';
    //     } elseif (!$aRow['is_assigned']) {
    //         $tooltip = ' data-toggle="tooltip" data-title="' . _l('task_start_timer_only_assignee') . '"';
    //     }
    // }

    $atts  = array(
        'onclick' => 'timer_action(this,' . $aRow['id'] . '); return false'
    );

    // if ($timer = $this->_instance->tasks_model->is_timer_started($aRow['id'])) {
    //     $options .= icon_btn('#', 'clock-o', 'btn-danger pull-right no-margin', array(
    //         'onclick' => 'timer_action(this,' . $aRow['id'] . ',' . $timer->id . '); return false'
    //     ));
    // } else {
    //     $options .= '<span' . $tooltip . ' class="pull-right">' . icon_btn('#', 'clock-o', $class . ' no-margin', $atts) . '</span>';
    // }

    $row[]              = $options;

    $rowClass = '';
    if ((!empty($aRow['duedate']) && $aRow['duedate'] < date('Y-m-d'))) // && $aRow['status'] != 5
    {
        $rowClass = 'text-danger bold ';
    }

    $row['DT_RowClass'] = $rowClass;

    $output['aaData'][] = $row;
}
