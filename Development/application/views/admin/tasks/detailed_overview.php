<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            
            <?php if(!$this->input->get('project_id')){ ?>
              <?php if (isset($lid)) { ?>      
                <a href="<?php echo admin_url('tasks/?lid='.$lid); ?>" class="btn btn-default pull-left"><?php echo _l('back_to_tasks_list'); ?></a>
              <?php }else{ ?>
                <a href="<?php echo admin_url('tasks'); ?>" class="btn btn-default pull-left"><?php echo _l('back_to_tasks_list'); ?></a>
              <?php } ?>
            <?php } else { ?>
                <?php if (isset($lid)) { ?>      
                    <a href="<?php echo admin_url('projects/view/'.$this->input->get('project_id').'?group=project_tasks&lid='.$lid); ?>" class="mtop5 pull-left btn btn-default"><?php echo _l('back_to_project'); ?></a>
                <?php }else{ ?>
                    <a href="<?php echo admin_url('projects/view/'.$this->input->get('project_id').'?group=project_tasks'); ?>" class="mtop5 pull-left btn btn-default"><?php echo _l('back_to_project'); ?></a>
                <?php } ?>
            <?php } ?>
            <div class="clearfix"></div>
            <hr />
            <?php echo form_open($this->uri->uri_string() . ($this->input->get('project_id') ? '?project_id='.$this->input->get('project_id') : ''). (isset($lid) && $lid != "" ? '?lid='.$lid : '')); ?>
            <div class="row">
              <?php echo form_hidden('project_id',$this->input->get('project_id')); ?>
              <?php if(has_permission('tasks','','create')){ ?>
              <div class="col-md-2 border-right">
                <?php
                echo render_select('member',$members,array('staffid',array('firstname','lastname')),'',$staff_id,array('data-none-selected-text'=>_l('all_staff_members')),array(),'no-margin'); ?>
              </div>
              <?php } ?>
              <div class="col-md-2 border-right">
                <?php
                $months = array();

                for ($m = 1; $m <= 12; $m++) {
                  $data = array();
                  $data['month'] = $m;
                  $data['name'] = _l(date('F', mktime(0, 0, 0, $m, 1)));
                  $months[] = $data;
                }
                $selected = ($this->input->post('month') ? $this->input->post('month') : date('m'));
                if($this->input->post() && $this->input->post('month') == ''){
                  $selected = '';
                }
                echo render_select('month',$months,array('month',array('name')),'',$selected,array('data-none-selected-text'=>_l('task_filter_detailed_all_months')),array(),'no-margin');
                ?>
              </div>
              <div class="col-md-2 text-center border-right">
                <div class="form-group no-margin">
                  <select name="status" id="status" class="selectpicker no-margin" data-width="100%" data-title="<?php echo _l('task_status'); ?>">
                    <option value="" selected><?php echo _l('task_list_all'); ?></option>
                    <?php foreach($task_statuses as $status){ ?>
                    <option value="<?php echo $status['id']; ?>" <?php if($this->input->post('status') == $status['id']){echo 'selected'; } ?>><?php echo $status['name']; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-md-2 border-right">
               <select name="year" id="year" class="selectpicker no-margin" data-width="100%">
                <?php foreach($years as $data){ ?>
                <option value="<?php echo $data['year']; ?>" <?php if($this->input->post('year') == $data['year'] || date('Y') == $data['year']){echo 'selected'; } ?>><?php echo $data['year']; ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn btn-info btn-block" style="margin-top:3px;"><?php echo _l('filter'); ?></button>
            </div>
          </div>
          <?php echo form_close(); ?>
        </div>
      </div>
      <div class="panel_s">
        <div class="panel-body">
          <?php foreach($overview as $month =>$data){ if(count($data) == 0){continue;} ?>
          <h4 class="no-margin bold text-success"><?php echo  _l(date('F', mktime(0, 0, 0, $month, 1))); ?>
            <?php if($this->input->get('project_id')){ echo ' - ' . get_project_name_by_id($this->input->get('project_id'));} ?>
          </h4>
          <div class="table-responsive">
            <table class="table tasks-overview">
              <thead>
                <tr>
                  <th><?php echo _l('tasks_dt_name'); ?></th>
                  <th><?php echo _l('task_status'); ?></th>
                  <th><?php echo _l('task_assigned'); ?></th>
                  <th><?php echo _l('tasks_dt_datestart'); ?></th>
                  <th><?php echo _l('task_duedate'); ?></th>
                </tr>
              </thead>
              <tbody>
               <?php
               foreach($data as $task){
                $where_total_time = '';
                if(is_numeric($staff_id)){
                  $where_total_time = ' AND staff_id=' . $staff_id;
                }

                $task_total_logged_time_by_user = $this->tasks_model->calc_task_total_time($task['id'], $where_total_time);
                $row_class = ($task['status'] == 5 ? 'task-finished-table-green' : 'task-unfinished-table text-danger');
                ?>
                <tr class="<?php echo $row_class; ?>">
                  <td class="stripped-table-data"><a href="<?php echo admin_url('tasks/dashboard/' . $task['id']. (isset($lid) && $lid != "" ? '?lid='.$lid : '')); ?>"><?php echo $task['name']; ?></a>
                    <?php
                    // if (!empty($task['rel_id'])) {
                    //   $rel_data   = get_relation_data($task['rel_type'], $task['rel_id']);
                    //   $rel_values = get_relation_values($rel_data, $task['rel_type']);
                    //   echo '<br />'. _l('task_related_to').': <a class="text-muted" href="' . $rel_values['link'] . '">' . $rel_values['name'] . '</a>';
                    // }
                    ?>
                    
           </td>
           <td><?php echo format_task_status($task['status']); ?></td>
         <td>
          <?php
          $assignees = $this->tasks_model->get_task_assignees($task['id']);
          $_assignees = '';
          foreach ($assignees as $assigned) {
            $_assignees .= staff_profile_image($assigned['assigneeid'], array(
              'staff-profile-image-small mright5'
              ), 'small', array(
              'data-toggle' => 'tooltip',
              'data-title' => get_staff_full_name($assigned['assigneeid'])
              ));

          }
          echo $_assignees;
          ?>
        </td>
        <td><?php echo _d($task['startdate']); ?></td>
        <td><?php echo _d($task['duedate']); ?></td>
      </tr>
      <?php } ?>
    </tbody>
   </table>
 </div>
 <hr />
 <?php } ?>
</div>
</div>
</div>
</div>
</div>
</div>
<?php init_tail(); ?>
</body>
</html>
