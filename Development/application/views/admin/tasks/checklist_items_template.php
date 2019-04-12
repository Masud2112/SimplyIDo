<?php
$total_checklist_items = total_rows('tbltaskchecklists', array('taskid' => $task_id));
$CI =& get_instance();
$CI->load->model('tasks_model');
$checklist_items = $CI->tasks_model->get_checklist_items($task_id, 1);
$checklist_items = count($checklist_items);
//$total_checklist_items = total_rows('tbltaskchecklists',array('taskid'=>$task['id']));
$completed = 0;
if ($total_checklist_items > 0) {
    $completed = ($checklist_items * 100) / $total_checklist_items;
    $completed = ceil($completed);
}
?>
<div class="clearfix"></div>
<?php //if(count($checklists) > 0){ ?>

<?php //} ?>
<div class="">
    <div class="progress mtop15">
        <div class="progress-bar not-dynamic progress-bar-default task-progress-bar" role="progressbar"
             aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"
             style="width:<?php echo $completed > 0 ? $completed : 0; ?>%">
        </div>
    </div>
    <?php
    foreach ($checklists as $list) { ?>
        <div class="checklist" data-checklist-id="<?php echo $list['id']; ?>">
            <div class="checkbox checkbox-success checklist-checkbox" data-toggle="tooltip" title="">
                <input type="checkbox" <?php if ($list['finished'] == 1 && $list['finished_from'] != get_staff_user_id() && !is_admin()) {
                    echo 'disabled';
                } ?> name="checklist-box" <?php if ($list['finished'] == 1) {
                    echo 'checked';
                }; ?>>
                <label for=""><span class="hide"><?php echo $list['description']; ?></span></label>
                <textarea data-taskid="<?php echo $task_id; ?>" name="checklist-description"
                          rows="1"><?php echo clear_textarea_breaks($list['description']); ?></textarea>
                <?php if (has_permission('tasks', '', 'delete') || $list['addedfrom'] == get_staff_user_id()) { ?>
                    <a href="#" class="pull-right text-muted remove-checklist"
                       onclick="delete_checklist_item(<?php echo $list['id']; ?>,this); return false;"><i
                                class="fa fa-remove"></i>
                    </a>
                <?php } ?>
                <?php if (has_permission('checklist_templates', '', 'create')) { ?>
                    <a href="#"
                       class="pull-right text-muted mright5 save-checklist-template<?php if ($list['description'] == '' || total_rows('tblcheckliststemplates', array('description' => $list['description'])) > 0) {
                           echo ' hide';
                       } ?>" data-toggle="tooltip" data-title="<?php echo _l('save_as_template'); ?>"
                       onclick="save_checklist_item_template(<?php echo $list['id']; ?>,this); return false;">
                        <i class="fa fa-level-up" aria-hidden="true"></i>
                    </a>
                <?php } ?>
            </div>
        </div>
        <?php if ($list['finished'] == 1) {  //&& $list['finished_from'] != get_staff_user_id() ?>
            <p class="small"><?php echo _l('task_checklist_item_completed_by', get_staff_full_name($list['finished_from'])); ?></p>
        <?php } ?>
    <?php } ?>
</div>