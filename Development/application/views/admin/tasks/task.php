<?php
/*
   Added By Purvi on 11-01-2017 for Tasks Add/Edit
*/
?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content task-page">
        <div class="row">
            <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'task-form')); ?>
            <div class="col-sm-12">
                <div class="breadcrumb">
                    <?php /*if (isset($pg) && $pg == 'home') { */ ?>
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php /*} */ ?>
                    <?php if (isset($lid)) { ?>
                        <a href="<?php echo admin_url('leads/'); ?>">Leads</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('leads/dashboard/' . $lid); ?>"><?php echo($lname); ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('tasks') . '?lid=' . $lid; ?>">Tasks</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } elseif (isset($pid)) { ?>
                        <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('projects/dashboard/' . $pid); ?>"><?php echo($lname); ?></a>
                        <?php if ($parent_id > 0) { ?>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('projects/dashboard/') . $parent_id; ?>"><?php echo get_project_name_by_id($parent_id); ?></a>                                            <?php } ?>

                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('tasks') . '?pid=' . $pid; ?>">Tasks</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } elseif (isset($eid)) { ?>
                        <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                        <?php if ($parent_id > 0) { ?>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('projects/dashboard/') . $parent_id; ?>">
                                <?php echo get_project_name_by_id($parent_id); ?></a>
                        <?php } ?>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('projects/dashboard/' . $eid); ?>"><?php echo($lname); ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('tasks') . '?eid=' . $eid; ?>">Tasks</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } else { ?>
                        <a href="<?php echo admin_url('tasks'); ?>">Tasks</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } ?>
                    <span><?php echo isset($task) ? $task->name : "New Task" ?></span>
                </div>

                <h1 class="pageTitleH1"><i class="fa fa-tasks"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <?php if (isset($tasks)) { ?>
                            <?php echo form_hidden('taskid', $tasks->taskid);
                        } ?>
                        <?php
                        $rel_type = '';
                        $rel_id = '';
                        if (isset($task) || ($this->input->get('rel_id') && $this->input->get('rel_type'))) {
                            if ($this->input->get('rel_id')) {
                                $rel_id = $this->input->get('rel_id');
                                $rel_type = $this->input->get('rel_type');
                            } else {
                                $rel_id = $task->rel_id;
                                $rel_type = $task->rel_type;
                            }
                        } elseif (isset($lid)) {
                            $rel_id = $lid;
                            $rel_type = 'lead';
                        } elseif (isset($pid)) {
                            $rel_id = $pid;
                            $rel_type = 'project';
                        } elseif (isset($eid)) {
                            $rel_id = $eid;
                            $rel_type = 'event';
                        } ?>
                        <?php
                        if (isset($task) && $task->billed == 1) {
                            echo '<div class="alert alert-success text-center no-margin">' . _l('task_is_billed', '<a href="' . admin_url('invoices/list_invoices/' . $task->invoice_id) . '" target="_blank">' . format_invoice_number($task->invoice_id)) . '</a></div><br />';
                        }
                        ?>
                        <?php if (isset($task)) { ?>
                            <div class="pull-right mbot10 task-single-menu task-menu-options">
                                <div class="content-menu hide">
                                    <ul>
                                        <?php if (has_permission('tasks', '', 'create')) { ?>
                                            <?php
                                            $copy_template = "";
                                            if (total_rows('tblstafftaskassignees', array('taskid' => $task->id)) > 0) {
                                                $copy_template .= "<div class='checkbox checkbox-primary'><input type='checkbox' name='copy_task_assignees' id='copy_task_assignees' checked><label for='copy_task_assignees'>" . _l('task_single_assignees') . "</label></div>";
                                            }
                                            if (total_rows('tblstafftasksfollowers', array('taskid' => $task->id)) > 0) {
                                                $copy_template .= "<div class='checkbox checkbox-primary'><input type='checkbox' name='copy_task_followers' id='copy_task_followers' checked><label for='copy_task_followers'>" . _l('task_single_followers') . "</label></div>";
                                            }
                                            if (total_rows('tbltaskchecklists', array('taskid' => $task->id)) > 0) {
                                                $copy_template .= "<div class='checkbox checkbox-primary'><input type='checkbox' name='copy_task_checklist_items' id='copy_task_checklist_items' checked><label for='copy_task_checklist_items'>" . _l('task_checklist_items') . "</label></div>";
                                            }
                                            if (total_rows('tblfiles', array('rel_id' => $task->id, 'rel_type' => 'task')) > 0) {
                                                $copy_template .= "<div class='checkbox checkbox-primary'><input type='checkbox' name='copy_task_attachments' id='copy_task_attachments'><label for='copy_task_attachments'>" . _l('task_view_attachments') . "</label></div>";
                                            }

                                            $copy_template .= "<p>" . _l('task_status') . "</p>";
                                            $task_copy_statuses = do_action('task_copy_statuses', $task_statuses);
                                            foreach ($task_copy_statuses as $copy_status) {
                                                $copy_template .= "<div class='radio radio-primary'><input type='radio' value='" . $copy_status['id'] . "' name='copy_task_status' id='copy_task_status_" . $copy_status['id'] . "'" . ($copy_status['id'] == do_action('copy_task_default_status', 1) ? ' checked' : '') . "><label for='copy_task_status_" . $copy_status['id'] . "'>" . $copy_status['name'] . "</label></div>";
                                            }

                                            $copy_template .= "<div class='text-center'>";
                                            $copy_template .= "<button type='button' data-task-copy-from='" . $task->id . "' class='btn btn-success copy_task_action'>" . _l('copy_task_confirm') . "</button>";
                                            $copy_template .= "</div>";
                                            ?>
                                            <li><a href="#" onclick="return false;" data-placement="bottom"
                                                   data-toggle="popover"
                                                   data-content="<?php echo htmlspecialchars($copy_template); ?>"
                                                   data-html="true"><?php echo _l('task_copy'); ?></span></a>
                                            </li>
                                        <?php } ?>
                                        <?php if (has_permission('tasks', '', 'delete')) { ?>
                                            <li>
                                                <a href="<?php echo admin_url('tasks/delete_task/' . $task->id); ?>"
                                                   class="_delete task-delete">
                                                    <?php echo _l('task_single_delete'); ?>
                                                </a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <?php
                                // if(has_permission('tasks','','delete') || has_permission('tasks','','create')){
                                // <a href="#" onclick="return false;" class="trigger manual-popover mright5">
                                // <i class="fa fa-circle-thin" aria-hidden="true"></i>
                                // <i class="fa fa-circle-thin" aria-hidden="true"></i>
                                // <i class="fa fa-circle-thin" aria-hidden="true"></i>
                                // </a>
                                //  } ?>
                            </div>
                        <?php } ?>
                        <div class="task-visible-to-customer checkbox checkbox-inline checkbox-primary<?php if ((isset($task) && $task->rel_type != 'project') || !isset($task) || (isset($task) && $task->rel_type == 'project' && total_rows('tblprojectsettings', array('project_id' => $task->rel_id, 'name' => 'view_tasks', 'value' => 0)) > 0)) {
                            echo ' hide';
                        } ?>">
                            <input type="checkbox" id="task_visible_to_client"
                                   name="visible_to_client" <?php if (isset($task)) {
                                if ($task->visible_to_client == 1) {
                                    echo 'checked';
                                }
                            } ?>>
                            <label for="task_visible_to_client"><?php echo _l('task_visible_to_client'); ?></label>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <?php $value = (isset($task) ? $task->name : ''); ?>
                                <?php echo render_input('name', 'task_add_edit_subject', $value); ?>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <?php
                                if (isset($_GET['due_dt'])) {
                                    $due_dt = date_create($_GET['due_dt']);
                                    $due_dt = date_format($due_dt, 'm/d/Y H:i');
                                    $value = $due_dt;
                                } else {
                                    $value = (isset($task) ? _dt($task->duedate, true) : '');
                                }

                                ?>
                                <?php echo render_datetime_input('duedate', 'task_add_edit_due_date', $value, $project_end_date_attrs); ?>

                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="priority"
                                           class="control-label"><?php echo _l('task_add_edit_priority'); ?></label>
                                    <select name="priority" class="selectpicker" id="priority" data-width="100%"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value="1" <?php if (isset($task) && $task->priority == 1 || !isset($task) && get_option('default_task_priority') == 1) {
                                            echo 'selected';
                                        } ?>><?php echo _l('task_priority_low'); ?></option>
                                        <option value="2" <?php if (isset($task) && $task->priority == 2 || !isset($task) && get_option('default_task_priority') == 2) {
                                            echo 'selected';
                                        } ?>><?php echo _l('task_priority_medium'); ?></option>
                                        <option value="3" <?php if (isset($task) && $task->priority == 3 || !isset($task) && get_option('default_task_priority') == 3) {
                                            echo 'selected';
                                        } ?>><?php echo _l('task_priority_high'); ?></option>
                                        <option value="4" <?php if (isset($task) && $task->priority == 4 || !isset($task) && get_option('default_task_priority') == 4) {
                                            echo 'selected';
                                        } ?>><?php echo _l('task_priority_urgent'); ?></option>
                                        <?php do_action('task_priorities_select', (isset($task) ? $task : 0)); ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!--</div>-->
                        <div class="recurring_custom <?php if ((isset($task) && $task->custom_recurring != 1) || (!isset($task))) {
                            echo 'hide';
                        } ?>">
                            <div class="row">
                                <div class="col-sm-6">
                                    <?php $value = (isset($task) && $task->custom_recurring == 1 ? $task->repeat_every : 1); ?>
                                    <?php echo render_input('repeat_every_custom', '', $value, 'number', array('min' => 1)); ?>
                                </div>
                                <div class="col-sm-6">
                                    <select name="repeat_type_custom" id="repeat_type_custom" class="selectpicker"
                                            data-width="100%"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value="day" <?php if (isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'day') {
                                            echo 'selected';
                                        } ?>><?php echo _l('task_recurring_days'); ?></option>
                                        <option value="week" <?php if (isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'week') {
                                            echo 'selected';
                                        } ?>><?php echo _l('task_recurring_weeks'); ?></option>
                                        <option value="month" <?php if (isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'month') {
                                            echo 'selected';
                                        } ?>><?php echo _l('task_recurring_months'); ?></option>
                                        <option value="year" <?php if (isset($task) && $task->custom_recurring == 1 && $task->recurring_type == 'year') {
                                            echo 'selected';
                                        } ?>><?php echo _l('task_recurring_years'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="recurring_ends_on"
                             class="<?php if (!isset($task) || (isset($task) && $task->recurring == 0)) {
                                 echo 'hide';
                             } ?>">
                            <?php $value = (isset($task) ? _d($task->recurring_ends_on) : ''); ?>
                            <?php echo render_date_input('recurring_ends_on', 'recurring_ends_on', $value); ?>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <?php echo render_select('status', $statuses, array('id', 'name'), 'Status', (isset($task) ? $task->status : '')); ?>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="assigned" class="control-label">Assigned To</label>
                                    <select name="assigned[]" id="assigned[]" class="form-control selectpicker"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                            data-live-search="true" multiple>
                                        <?php
                                        foreach ($members as $assigne) {
                                            $tselected = '';
                                            if (isset($task)) {
                                                if (in_array($assigne['staffid'], $task->assigned)) {
                                                    $tselected = "selected='selected'";
                                                }
                                            }

                                            echo '<option value="' . $assigne['staffid'] . '" ' . $tselected . '>' . $assigne['firstname'] . ' ' . $assigne['lastname'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="rel_type"
                                           class="control-label"><?php echo _l('task_related_to'); ?></label>
                                    <select name="rel_type" class="selectpicker" id="rel_type" data-width="100%"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value=""></option>
                                        <?php if (isset($lid) || (!isset($eid) && !isset($pid))) { ?>
                                            <option value="lead" <?php if (isset($task) || isset($lid) || $this->input->get('rel_type')) {
                                                if ($rel_type == 'lead') {
                                                    echo 'selected';
                                                }
                                            } ?>>
                                                <?php echo _l('lead'); ?>
                                            </option>
                                        <?php } ?>
                                        <?php if (isset($pid) || (!isset($eid) && !isset($lid))) { ?>
                                            <option value="project" <?php if (isset($task) || isset($pid) || $this->input->get('rel_type')) {
                                                if ($rel_type == 'project') {
                                                    echo 'selected';
                                                }
                                            } ?>>
                                                <?php echo _l('project'); ?>
                                            </option>
                                        <?php } ?>
                                        <?php if ((isset($pid) || isset($eid)) || !isset($lid)) { ?>
                                            <option value="event" <?php if (isset($task) || isset($eid) || $this->input->get('rel_type')) {
                                                if ($rel_type == 'event') {
                                                    echo 'selected';
                                                }
                                            } ?>>
                                                Sub-Projects
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php if (isset($lid) || (!isset($eid) && !isset($pid))) { ?>
                                <div class="col-sm-6 lead-search <?php echo $rel_type == "lead" ? "" : "hide"; ?>">
                                    <?php $selectedleads = array();
                                    $selectedleads = $rel_id != "" ? $rel_id : "";
                                    echo render_select('lead', $leads, array('id', 'name'), 'Leads', $selectedleads, array(), array(), '', '', false);
                                    ?>
                                </div>
                            <?php } ?>
                            <?php if (isset($pid) || (!isset($eid) && !isset($lid))) { ?>
                                <div class="col-sm-6 project-search <?php echo $rel_type == "project" ? "" : "hide"; ?>">
                                    <?php $selectedprojects = array();
                                    $selectedprojects = $rel_id != "" ? $rel_id : "";
                                    echo render_select('project', $projects, array('id', 'name'), 'Projects', $selectedprojects, array(), array(), '', '', false);
                                    ?>
                                </div>
                            <?php } ?>
                            <?php if ((isset($pid) || isset($eid)) || !isset($lid)) { ?>
                                <div class="col-sm-6 event-search <?php echo $rel_type == "event" ? "" : "hide"; ?>">
                                    <?php $selectedevents = array();
                                    $selectedevents = $rel_id != "" ? $rel_id : "";
                                    echo render_select('event', $events, array('id', 'name'), 'Sub-Projects', $selectedevents, array(), array(), '', '', false);
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?php if (isset($task) && (has_permission('create') || has_permission('edit'))) {
                            echo render_datetime_input('datefinished', 'task_finished', _dt($task->datefinished));
                        }
                        ?>
                        <div class="form-group checklist-templates-wrapper<?php if (count($checklistTemplates) == 0 || isset($task)) {
                            echo ' hide';
                        } ?>">
                            <label for="checklist_items"><?php echo _l('insert_checklist_templates'); ?></label>
                            <select id="checklist_items" name="checklist_items[]"
                                    class="selectpicker checklist-items-template-select" multiple="1"
                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex') ?>"
                                    data-width="100%" data-live-search="true">
                                <option value=""></option>
                                <?php foreach ($checklistTemplates as $chkTemplate) { ?>
                                    <option value="<?php echo $chkTemplate['id']; ?>">
                                        <?php echo $chkTemplate['description']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <?php $rel_id_custom_field = (isset($task) ? $task->id : false); ?>
                        <?php echo render_custom_fields('tasks', $rel_id_custom_field); ?>
                        <?php echo render_textarea('description', 'Task Description', (isset($task) ? $task->description : ''), array(), array(), '', ''); ?>
                        <?php if (!isset($task)) { ?>
                            <div class="form-group">
                                <label><?php echo _l('attach_files'); ?> <i class="fa fa-question-circle"
                                                                            data-toggle="tooltip"
                                                                            data-title="Allowed extensions - <?php echo str_replace('.', '', get_option('allowed_files')); ?>"></i></label>

                                <div id="new-task-attachments" style="margin-top:20px;">
                                    <div class="row attachments">
                                        <div class="attachment">
                                            <div class="row col-sm-12">
                                                <div class="col-sm-7">
                                                    <div class="form-group">
                                                        <div class="input-group">
                                            <span class="input-group-btn">
                                              <span class="btn btn-primary"
                                                    onclick="$(this).parent().find('input[type=file]').click();">Browse</span>
                                              <input name="attachments[0]"
                                                     onchange="$(this).parent().parent().find('.form-control').html($(this).val().split(/[\\|/]/).pop());"
                                                     style="display: none;"
                                                     filesize="<?php echo file_upload_max_size(); ?>"
                                                     extension="<?php echo str_replace('.', '', get_option('allowed_files')); ?>"
                                                     type="file">
                                            </span>
                                                            <span class="form-control"></span>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="text-right">
                                                    <button class="btn btn-primary add_more_attachments" type="button">
                                                        <i
                                                                class="fa fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="" id="field">
                            <h4><?php echo _l('task_add_edit_reminder'); ?></h4>
                            <hr class="hr-panel-heading"/>
                            <?php
                            if (isset($task->reminders) && count($task->reminders) > 0) {
                                $i = 0;
                                foreach ($task->reminders as $taskreminder) {
                                    ?>
                                <div class="row" id="field-<?php echo $i; ?>">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="duration"
                                                   class="control-label"><?php echo _l('task_add_edit_duration'); ?></label>
                                            <input type="number" id="reminder[<?php echo $i; ?>][duration]"
                                                   name="reminder[<?php echo $i; ?>][duration]" class="form-control"
                                                   value="<?php echo $taskreminder['duration']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label"
                                                   for="reminder[<?php echo $i; ?>][interval]"><?php echo _l('task_add_edit_interval'); ?></label>
                                            <select id="reminder[<?php echo $i; ?>][interval]"
                                                    name="reminder[<?php echo $i; ?>][interval]" class="selectpicker"
                                                    data-width="100%" data-none-selected-text="Select">
                                                <?php
                                                foreach ($reminders as $kr => $vr) {
                                                    if ($kr == $taskreminder['interval']) {
                                                        $selected = "selected='selected'";
                                                    } else {
                                                        $selected = "";
                                                    }
                                                    echo '<option value = "' . $kr . '" ' . $selected . '>' . $vr . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <?php if ($i != 0) { ?>
                                        <div class="col-sm-2" id="divremove<?php echo $i; ?>">
                                            <button id="remove<?php echo $i; ?>" class="btn btn-danger remove-me">Remove
                                            </button>
                                        </div>

                                        <?php
                                    }
                                    $i++; ?></div><?php

                                }

                            } else { ?>
                                <div id="field-0" class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="duration"
                                                   class="control-label"><?php echo _l('task_add_edit_duration'); ?></label>
                                            <input type="number" id="reminder[0][duration]" name="reminder[0][duration]"
                                                   class="form-control" value="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label"
                                                   for="reminder[0][interval]"><?php echo _l('task_add_edit_interval'); ?></label>
                                            <select id="reminder[0][interval]" name="reminder[0][interval]"
                                                    class="selectpicker" data-width="100%"
                                                    data-none-selected-text="Select">
                                                <?php foreach ($reminders as $kr => $vr) {
                                                    $selected2 = "";
                                                    if (isset($task)) {
                                                        if ($task->reminder == $kr) {
                                                            $selected2 = "selected='selected'";
                                                        }
                                                    } else {
                                                        $selected2 = "";
                                                    }
                                                    echo '<option value = "' . $kr . '" ' . $selected2 . '>' . $vr . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="col-sm-9 text-right">
                                <button id="add-more" name="add-more" class="btn btn-primary">Add More</button>
                            </div>
                        </div>
                        <div class="topButton">
                            <button class="btn btn-default" type="button"
                                    onclick="fncancel();"><?php echo _l('Cancel'); ?></button>
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                        </div>

                    </div>
                </div>
            </div>
            <input type="hidden" name="hdnlid" value="<?php echo isset($lid) ? $lid : ''; ?>">
            <input type="hidden" name="hdnpid" value="<?php echo isset($pid) ? $pid : ''; ?>">
            <input type="hidden" name="hdneid" value="<?php echo isset($eid) ? $eid : ''; ?>">
            <input type="hidden" name="pg" value="<?php echo isset($pg) ? $pg : ''; ?>">
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php app_external_form_footer('task'); ?>
<?php init_tail(); ?>
<script>
    function fncancel() {
        var id =<?php if (isset($lid)) {
            echo $lid;
        } else {
            echo '0';
        }  ?>;
        var pid =<?php if (isset($pid)) {
            echo $pid;
        } else {
            echo '0';
        }  ?>;
        var eid =<?php if (isset($eid)) {
            echo $eid;
        } else {
            echo '0';
        }  ?>;
        if (id > '0') {
            location.href = '<?php echo base_url(); ?>admin/tasks?lid=' + id;
        } else if (pid > '0') {
            location.href = '<?php echo base_url(); ?>admin/tasks?pid=' + pid;
        } else if (eid > '0') {
            location.href = '<?php echo base_url(); ?>admin/tasks?eid=' + eid;
        } else {
            window.history.go(-1);
        }
    }

    $(function () {

        _validate_form($('.task-form'), {
            name: 'required',
            //startdate: 'required',
            duedate: 'required', //, greaterThan: "#startdate"
            //'assigned[]': 'required',
            status: 'required'
        });

        $("#rel_type").on('change', function () {
            var selected = $(this).val();
            if (selected == "lead") {
                $(".lead-search").removeClass("hide");
                $(".project-search").addClass("hide");
                $(".event-search").addClass("hide");
            } else if (selected == "project") {
                $(".project-search").removeClass("hide");
                $(".lead-search").addClass("hide");
                $(".event-search").addClass("hide");
            } else if (selected == "event") {
                $(".event-search").removeClass("hide");
                $(".lead-search").addClass("hide");
                $(".project-search").addClass("hide");
            }
        });

        init_datepicker();
        init_color_pickers();
        init_selectpicker();

        // End code of Add more / Remove address
        //Added By Purvi on 11/10/2017
        // $('#startdate').change(function(e){
        //   var selected = e.target.value;
        //   $('#duedate').val(selected);

    });
</script>
<script type="text/javascript">
    $(function () {
        var reminders = <?php echo json_encode($reminders); ?>;

        $("#add-more").click(function (e) {
            e.preventDefault();
            var my_fields = $("div[id^='field-']");
            var highest = -Infinity;
            $.each(my_fields, function (mindex, mvalue) {
                var fieldNum = mvalue.id.split("-");
                highest = Math.max(highest, parseFloat(fieldNum[1]));
            });

            var next = highest;
            var addto = "#field-" + next;
            var addRemove = "#field-" + (next);

            next = next + 1;
            var newIn = "";
            newIn += ' <div class="row" id="field-' + next + '"><div class="col-sm-3"><div class="form-group"><label class="control-label" for="reminder[' + next + '][duration]">Duration</label><input type="number" name="reminder[' + next + '][duration]" id="reminder[' + next + '][duration]" class="form-control"/></div>';

            newIn += '</select></div>';
            newIn += '<div class="col-sm-3"><div class="form-group"><label class="control-label" for="reminder[' + next + '][interval]">Interval</label><select id="reminder[' + next + '][interval]" name="reminder[' + next + '][interval]" class="selectpicker" data-width="100%" data-none-selected-text="Select">';
            $.each(reminders, function (rindex, rvalue) {
                newIn += '<option value="' + rindex + '">' + rvalue + '</option>';
            });

            newIn += '</select></div></div>';
            newIn += '<div class="col-sm-2" id="divremove' + (next) + '"><button id="remove' + (next) + '" class="btn btn-danger remove-me" >Remove</button></div></div>';

            var newInput = $(newIn);

            $(addto).after(newInput);
            $("#field-" + next).attr('data-source', $(addto).attr('data-source'));
            $("#count").val(next);

            $('.remove-me').click(function (e) {
                e.preventDefault();
                var fieldNum = this.id.charAt(this.id.length - 1);
                var fieldID = "#field-" + fieldNum;
                $(fieldID).remove();
            });

            $('.selectpicker').selectpicker('render');
        });

        $('.remove-me').click(function (e) {
            e.preventDefault();
            var fieldNum = this.id.charAt(this.id.length - 1);
            var fieldID = "#field-" + fieldNum;
            $(fieldID).remove();
        });
    });


    /*
    ** Added By Sanjay on 02/08/2018
    ** For start-date and end-date
    */
    $(function () {
        $(".input-group-addon").css({"padding": "0px"});
        $(".fa.fa-calendar.calendar-icon").css({"padding": "6px 12px"});

        $('.input-group-addon').find('.fa-calendar').on('click', function () {
            $(this).parent().siblings('#duedate').trigger('focus');
        });


        url = window.location.href;
        var date = url.split('?')[1].split('=')[1];
        // if(date)
        // {
        //   var spl_txt = date.split('-');
        //   var time = new Date();
        //   date = spl_txt[1]+"/"+spl_txt[2]+"/"+spl_txt[0]+" "+time.getHours() + ":" + time.getMinutes();
        //   $('#duedate').val(date);
        // }

    });

</script>
</body>
</html>