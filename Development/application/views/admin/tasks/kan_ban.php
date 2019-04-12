<?php

$where = array();
if($this->input->get('project_id')){
    $where['rel_id'] = $this->input->get('project_id');
    $where['rel_type'] = 'project';
}
else if($this->input->get('lid')){
    $where['rel_id'] = $this->input->get('lid');
    $where['rel_type'] = 'lead';
}
foreach ($task_statuses as $status) {
    $tasks = $this->tasks_model->do_kanban_query($status['id'],$this->input->get('search'),1,false,$where);
    /*echo "<pre>";
    print_r($tasks);
    die();*/
    $total_tasks = count($tasks);
    $total_pages = ceil($this->tasks_model->do_kanban_query($status['id'],$this->input->get('search'),1,true,$where)/get_option('tasks_kanban_limit'));
    ?>
    <ul class="kan-ban-col tasks-kanban" data-col-status-id="<?php echo $status['id']; ?>" data-total-pages="<?php echo $total_pages; ?>">
        <li class="kan-ban-col-wrapper">
            <div class="border-right panel_s">
                <div class="panel-heading-bg" style="border-left:4px solid <?php echo $status['color']; ?>;?>" data-status-id="<?php echo $status['id']; ?>">
                    <div class="kan-ban-step-indicator-full"></div>
                    <span class="heading"><?php echo format_task_status($status['id'],false,true); ?>
          </span>
                    <a href="#" onclick="return false;" class="pull-right color-white">
                    </a>
                    <?php if(count($tasks) > 3 ){ ?>
                        <span class="pull-right">
                        <a href="javascript:void(0)" class="kan-ban-exp-clps" data-pid="#status_<?php echo $status['id']; ?>">
                            <i class="fa fa-caret-down"></i>
                        </a>
                    </span>
                    <?php } ?>
                </div>
                <div class="kan-ban-content-wrapper" id="status_<?php echo $status['id']; ?>">
                    <div class="kan-ban-content">
                        <ul class="status tasks-status sortable relative" data-task-status-id="<?php echo $status['id']; ?>">
                            <?php
                            foreach ($tasks as $task) {
                                if ($task['status'] == $status['id']) {
                                    $this->load->view('admin/tasks/_kan_ban_card',array('task'=>$task,'status'=>$status['id']));
                                } } ?>
                            <li class="text-center not-sortable mtop30 kanban-empty<?php if($total_tasks > 0){echo ' hide';} ?>">
                                <h4 class="text-muted">
                                    <i class="fa fa-circle-o-notch" aria-hidden="true"></i><br /><br />
                                    <?php echo _l('no_tasks_found'); ?></h4>
                            </li>
                        </ul>
                    </div>
                </div>
        </li>
    </ul>
<?php } ?>
