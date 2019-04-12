<?php
$session_data = get_session_data();
$user_id = $session_data['staff_user_id'];
$CI =& get_instance();
$CI->db->select('pinid as pinned');
$CI->db->from('tblpins');
$CI->db->where('userid', $user_id);
$CI->db->where('pintype', 'Task');
$CI->db->where('pintypeid', $task['id']);
$result = $CI->db->get()->row();

$CI->load->model('tasks_model');
$checklist_items = $CI->tasks_model->get_checklist_items($task['id'],1);
$checklist_items = count($checklist_items);
$total_checklist_items = total_rows('tbltaskchecklists',array('taskid'=>$task['id']));
$completed = 0;
if($total_checklist_items > 0 ){
    $completed = ($checklist_items*100)/$total_checklist_items;
    $completed = ceil($completed);
}
/**/
?>
<li class="col-sm-6 col-lg-4 kanban-card-block kanban-card" data-task-id="<?php echo $task['id']; ?>">
    <div class="panel-body card-body">
        <div class="row">
            <div class="col-xs-11 task-name">
                <div class="carddate-block">
                    <div class="card_date">
                        <div class="card_month">
                            <small><?php echo date('M', strtotime($task['duedate'])) ?></small>
                        </div>
                        <div class="card_d"><strong><?php echo date('d', strtotime($task['duedate'])) ?></strong>
                        </div>
                        <div class="card_day">
                            <small><?php echo date('D', strtotime($task['duedate'])) ?></small>
                        </div>
                    </div>

                    <?php if (date('Y', strtotime($task['duedate'])) > date('Y')) { ?>
                        <div class="card_year">
                            <small><?php echo date('Y', strtotime($task['duedate'])) ?></small>
                        </div>
                    <?php } ?>
                </div>
                <span class="leadNameTitle">
                    <?php if ($this->input->get('lid')) {
                        $url = admin_url('tasks/dashboard/' . $task['id'] . '?lid=' . $this->input->get('lid'));
                    } else {
                        $url = admin_url('tasks/dashboard/' . $task['id']);
                    } ?>
                    <a href="<?php echo $url ?>">
                        <span class="inline-block full-width mtop10 mbot10"><?php echo $task['name']; ?></span>
                    </a>
                </span>
                <span class="lbltxt display-block"><i class="fa fa-<?php if ($task['status'] == 5) {
                        echo 'star';
                    } else if ($task['status'] == 1) {
                        echo 'star-o';
                    } else {
                        echo 'star-half-o';
                    } ?> pull-left task-info-icon"></i>
                    <?php echo format_task_status($task['status'], true); ?></span>
                <span class="display-block" >Progress: <?php echo $completed."%"?></span>
                <div class="staffTask_blk">
                    <ul>
                    <?php
                    $assignees = $this->tasks_model->get_task_assignees($task['id']);
                    foreach ($assignees as $assignee) {
                        echo '<li ><a href="javascript:void(0)">' . staff_profile_image($assignee['assigneeid'], array(
                                'staff-profile-image-xs mright5'
                            ), 'small', array(
                                'data-toggle' => 'tooltip',
                                'data-title' => $assignee['firstname'] . ' ' . $assignee['lastname']
                            )) . '</a></li>';
                    } ?>
                    </ul>
                </div>
                <!--<div class="col-md-6 text-left">

                    <?php /*if (total_rows('tbltaskchecklists') > 0) { */?>
                        <span class="mright5 inline-block" data-toggle="tooltip"
                              data-title="<?php /*echo _l('task_checklist_items'); */?>">
            <i class="fa fa-check-square-o" aria-hidden="true"></i> <?php /*echo total_rows('tbltaskchecklists', array(
                                'taskid' => $task['id'],
                                'finished' => 1,
                            ));*/?>
                            /
                            <?php /*echo total_rows('tbltaskchecklists', array(
                                'taskid' => $task['id'],
                            ));; */?>
              </span>
                    <?php /*} */?>
                    <span class="mright5 inline-block" data-toggle="tooltip"
                          data-title="<?php /*echo _l('task_comments'); */?>">
                <i class="fa fa-comments"></i> <?php /*echo total_rows('tblstafftaskcomments', array(
                            'taskid' => $task['id'],
                        ));; */?>
                </span>
                    <?php /*$total_attachments = total_rows('tblfiles', array(
                        'rel_id' => $task['id'],
                        'rel_type' => 'task',
                    )); */?>
                    <span class="inline-block" data-toggle="tooltip"
                          data-title="<?php /*echo _l('task_view_attachments'); */?>">
                   <i class="fa fa-paperclip"></i>
                        <?php /*echo $total_attachments; */?>
                 </span>
                </div>-->
            </div>
            <div class="col-xs-1 text-muted">
                <!--<small class="text-dark"><?php /*echo _l('task_assigned'); */ ?>: <span
                                class="lead-bold"><?php /*echo get_staff_full_name($lead['assigned']); */ ?></span></small>-->
                <div class="show-act-block"><?php
                    $options = "<div><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";

                    //$options .= icon_btn('leads/dashboard/' . $aRow['id'], 'eye', 'btn-success', array('title'=>'View Dashboard'));
                    $options.='<li><a href='.admin_url().'tasks/dashboard/'.$task['id'].' class="" title="View Dashboard"><i class="fa fa-eye"></i><span>View</span></a></li>';
                    if (has_permission('leads', '', 'edit')){
                        //$options .= icon_btn('leads/lead/' . $aRow['id'], 'pencil-square-o');
                        $options.='<li><a href='.admin_url().'tasks/task/'.$task['id'].' class="" title="Edit"><i class="fa fa-pencil-square-o"></i><span>Edit</span></a></li>';
                    } else {
                        $options .= "";
                    }

                    if (has_permission('leads', '', 'delete')) {
                        //$options .= icon_btn('leads/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
                        $options.='<li><a href='.admin_url().'tasks/delete_task/'.$task['id'].' class="_delete" title="Delete"><i class="fa fa-remove"></i><span>Delete</span></a></li>';
                    }
                    $options.="</ul></div>";
                    echo $options;
                    ?></div>
                <div class="task-pin-block">
                    <i class="fa fa-fw fa-thumb-tack task-pin <?php echo isset($result->pinned) ? "pinned" : ""; ?>"
                       title="<?php echo isset($result->pinned) ? "Unpin from Home" : "Pin to Home"; ?>" id="<?php echo $task['id'] ?>"
                       task_id="<?php echo $task['id'] ?>"></i>
                </div>
            </div>
        </div>
        <!--<div class="row">
            <div class="col-md-6">
                <?php
        /*                $assignees = $this->tasks_model->get_task_assignees($task['id']);
                        foreach ($assignees as $assignee) {
                            echo '<a href="javascript:void(0)">' . staff_profile_image($assignee['assigneeid'], array(
                                    'staff-profile-image-xs mright5'
                                ), 'small', array(
                                    'data-toggle' => 'tooltip',
                                    'data-title' => $assignee['firstname'] . ' ' . $assignee['lastname']
                                )) . '</a>';
                        } */?>
            </div>
        </div>-->
    </div>
</li>
