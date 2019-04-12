<?php

$table_data = array(
    _l(''),
    array(
     'name'=>_l('tasks_dt_name'),
     'th_attrs'=>array('class'=>'task-name')
     ),
    //_l('tasks_dt_datestart'),
    array(
     'name'=>_l('task_duedate'),
     'th_attrs'=>array('class'=>'task-due')
     ),
    //_l('tags'),
    array(
     'name'=>_l('Progress'),
     'th_attrs'=>array('class'=>'task-progress')
     ),
    array(
     'name'=>_l('task_status'),
     'th_attrs'=>array('class'=>'task-st')
     ),
    array(
     'name'=>_l('task_assigned'),
     'th_attrs'=>array('class'=>'task-assigned')
     ),
    array(
     'name'=>_l('tasks_list_priority'),
     'th_attrs'=>array('class'=>'task-priority')
     )
    );

if(isset($bulk_actions)){
    array_unshift($table_data,'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="tasks"><label></label></div>');
}

$custom_fields = get_custom_fields('tasks', array(
    'show_on_table' => 1
    ));

foreach ($custom_fields as $field) {
    array_push($table_data, $field['name']);
}

$table_data = do_action('tasks_table_columns',$table_data);

array_push($table_data, _l(''));

render_datatable($table_data, 'tasks');
?>
</div>