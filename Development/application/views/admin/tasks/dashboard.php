<?php
/**
 * Added By : Purvi
 * Dt : 11/06/2017
 * Task Dashboard
 */
init_head();
?>
<div id="wrapper" class="taskdashboard">
    <div class="content">


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
                <i class="fa fa-angle-right breadcrumb-arrow"></i>
                <a href="<?php echo admin_url('tasks') . '?pid=' . $pid; ?>">Tasks</a>
                <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <?php } else { ?>
                <a href="<?php echo admin_url('tasks'); ?>">Tasks</a>
                <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <?php } ?>
            <span><?php echo isset($task) ? $task->name : "New Task" ?></span>
        </div>
        <h1 class="pageTitleH1"><i class="fa fa-tasks"></i><?php echo "TASKS"; ?></h1>
        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-6">

                <div class="row">
                    <div class="col-sm-4">
                        <figure class="text-center thumb-lg">
                            <?php
                            $src = "<img class='profile_image lead-profile-image-thumb img-responsive img-thumbnail' src='" . base_url('assets/images/user-placeholder.jpg') . "'>";
                            if ($task->rel_id > 0) {
                                if ($task->rel_type == "lead") {
                                    $src = lead_profile_image($task->rel_id, array('profile_image', 'img-responsive img-thumbnail', 'lead-profile-image-thumb'), 'thumb');
                                } else {
                                    $src = project_profile_image($task->rel_id, array('profile_image', 'img-responsive img-thumbnail', 'project-profile-image-thumb'), 'thumb');
                                }
                            }
                            ?>
                            <!--<img src="http://localhost/SimplyIDo/Development/uploads/project_profile_images/5/thumb_11.jpg" class="profile_image img-responsive img-thumbnail project-profile-image-thumb" alt="lead 31">-->
                            <div class="profileImg_blk">
                                <?php echo $src ?>
                            </div>
                        </figure>
                    </div>
                    <div class="col-sm-8">
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
                            $copy_template .= "<div class='radio radio-primary'><input type='radio' value='" . $copy_status['id'] . "' name='copy_task_status' id='copy_task_status_" . $copy_status['id'] . "'" . ($copy_status['isdefault'] == 1 ? ' checked' : '') . "><label for='copy_task_status_" . $copy_status['id'] . "'>" . $copy_status['name'] . "</label></div>";
                        }
                        $copy_template .= "<div class='text-center'>";
                        $copy_template .= "<button type='button' data-task-copy-from='" . $task->id . "' class='btn btn-success copy_task_action' data-loading-text='" . _l('wait_text') . "''>" . _l('copy_task_confirm') . "</button>";
                        $copy_template .= "</div>";
                        ?>
                        <h4><?php echo isset($task->name) ? $task->name : "--"; ?>
                            <a data-title="Copy" onclick="return false;" data-placement="bottom" data-toggle="popover"
                               data-content="<?php echo htmlspecialchars($copy_template); ?>" data-html="true" href="#"
                               class="btn btn-icon btn-xs pull-right mright10"><i class="fa fa-copy"></i></a>
                            <?php if (has_permission('leads', '', 'edit')) {
                                if (isset($lid) && $lid != "") { ?>
                                    <a data-toggle="tooltip" data-title="Edit Task"
                                       href="<?php echo admin_url('tasks/task/' . $task->id . '?lid=' . $lid); ?>"
                                       class="btn btn-icon btn-xs pull-right mright10"><i class="fa fa-pencil"></i></a>
                                <?php } elseif (isset($pid) && $pid != "") { ?>
                                    <a data-toggle="tooltip" data-title="Edit Task"
                                       href="<?php echo admin_url('tasks/task/' . $task->id . '?pid=' . $pid); ?>"
                                       class="btn btn-icon btn-xs pull-right mright10"><i class="fa fa-pencil"></i></a>
                                <?php } elseif (isset($eid) && $eid != "") { ?>
                                    <a data-toggle="tooltip" data-title="Edit Task"
                                       href="<?php echo admin_url('tasks/task/' . $task->id . '?eid=' . $eid); ?>"
                                       class="btn btn-icon btn-xs pull-right mright10"><i class="fa fa-pencil"></i></a>
                                <?php } else { ?>
                                    <a data-toggle="tooltip" data-title="Edit Task"
                                       href="<?php echo admin_url('tasks/task/' . $task->id); ?>"
                                       class="btn btn-icon btn-xs pull-right mright10"><i class="fa fa-pencil"></i></a>
                                <?php } ?>
                            <?php } ?>
                            <?php /*if (isset($lid) && $lid != "") { */ ?><!--
                                <a data-toggle="tooltip" data-title="Back"
                                   href="<?php /*echo admin_url('tasks?lid=' . $lid); */ ?>"
                                   class="btn btn-icon btn-xs pull-right mright10"><i
                                            class="fa fa-chevron-left"></i></a>
                            <?php /*} elseif (isset($pid) && $pid != "") { */ ?>
                                <a data-toggle="tooltip" data-title="Back"
                                   href="<?php /*echo admin_url('tasks?pid=' . $pid); */ ?>"
                                   class="btn btn-icon btn-xs pull-right mright10"><i
                                            class="fa fa-chevron-left"></i></a>
                            <?php /*} elseif (isset($eid) && $eid != "") { */ ?>
                                <a data-toggle="tooltip" data-title="Back"
                                   href="<?php /*echo admin_url('tasks?eid=' . $eid); */ ?>"
                                   class="btn btn-icon btn-xs pull-right mright10"><i
                                            class="fa fa-chevron-left"></i></a>
                            <?php /*} elseif (isset($pg) && $pg != '') { */ ?>
                                <a data-toggle="tooltip" data-title="Back" href="<?php /*echo admin_url('calendar'); */ ?>"
                                   class="btn btn-icon pull-right"><i class="fa fa-chevron-left"></i></a>
                            <?php /*} else { */ ?>
                                <a data-toggle="tooltip" data-title="Back" href="<?php /*echo admin_url('tasks'); */ ?>"
                                   class="btn btn-icon btn-xs pull-right mright10"><i
                                            class="fa fa-chevron-left"></i></a>
                            --><?php /*} */ ?>
                        </h4>

                        <div class="card-user-info-widget panel_s btmbrd">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="task-info task-status">
                                        <h5><span class="lbltxt"><i class="fa fa-<?php if ($task->status == 5) {
                                                    echo 'star';
                                                } else if ($task->status == 1) {
                                                    echo 'star-o';
                                                } else {
                                                    echo 'star-half-o';
                                                } ?> pull-left task-info-icon"></i><?php echo _l('task_status'); ?></span> <?php echo format_task_status($task->status, true); ?>
                                        </h5>
                                    </div>
                                    <div class="task-info <?php if (!$task->status != 5) {
                                        echo ' text-danger';
                                    } else {
                                        echo 'text-info';
                                    } ?><?php if (!$task->duedate) {
                                        echo ' hide';
                                    } ?>">
                                        <h5><span class="lbltxt"><i
                                                        class="fa task-info-icon fa-calendar-check-o pull-left"></i> <?php echo _l('task_single_due_date'); ?> </span><?php echo _d($task->duedate); ?>
                                        </h5>
                                    </div>
                                    <div class="text-<?php echo get_task_priority_class($task->priority); ?> task-info">
                                        <h5><span class="lbltxt"><i
                                                        class="fa task-info-icon pull-left fa-bolt"></i> <?php echo _l('task_single_priority'); ?> </span><?php echo task_priority($task->priority); ?>
                                        </h5>
                                    </div>
                                    <?php if ($task->rel_type == 'project' && $task->milestone != 0) { ?>
                                        <div class="task-info">
                                            <h5><span class="lbltxt"><i
                                                            class="fa fa-rocket task-info-icon pull-left"></i> <?php echo _l('task_milestone'); ?> </span><?php echo $task->milestone_name; ?>
                                            </h5>
                                        </div>
                                    <?php } ?>
                                    <div class="task_users_wrapper  task-info">
                                        <h5><span class="lbltxt"><i class="fa task-info-icon pull-left fa-user"
                                                                    style="margin:0 8px 0 0 !important"></i> Assigned To</span>
                                            <?php
                                            $_assignees = '';
                                            foreach ($task->assignees as $assignee) {
                                                $_remove_assigne = '';
                                                if (has_permission('tasks', '', 'edit') || has_permission('tasks', '', 'create')) {
                                                    $_remove_assigne = ' <a href="#" class="remove-task-user text-danger" onclick="remove_assignee(' . $assignee['id'] . ',' . $task->id . '); return false;"><i class="fa fa-remove"></i></a>';
                                                }
                                                $_assignees .= '
									   <div class="task-user"  data-toggle="tooltip" data-title="' . get_staff_full_name($assignee['assigneeid']) . '">
										  <a href="javascript:void(0)">' . staff_profile_image($assignee['assigneeid'], array(
                                                        'staff-profile-image-small'
                                                    )) . '</a> ' . $_remove_assigne . '</span>
									   </div>';
                                            }
                                            if ($_assignees == '') {
                                                $_assignees = '--';
                                                //$_assignees = '<div class="text-danger display-block">'._l('task_no_assignees').'</div>';
                                            }
                                            echo $_assignees;
                                            ?></h5>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="description-block">
                                        <h4 class="th font-medium mbot15 pull-left"><?php echo _l('task_view_description'); ?></h4>
                                        <div class="clearfix"></div>
                                        <?php if (!empty($task->description)) {
                                            echo '<div class="tc-content"><div id="task_view_description">' . check_for_links($task->description) . '</div></div>';
                                        } else {
                                            echo '<div class="no-margin tc-content task-no-description" id="task_view_description"><span class="text-muted">' . _l('task_no_description') . '</span></div>';
                                        } ?>
                                        <div class="clearfix"></div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group checklist-templates-wrapper simple-bootstrap-select task-single-checklist-templates<?php if (count($checklistTemplates) == 0) {
                    echo ' hide';
                } ?>">
                    <select id="checklist_items_templates" class="selectpicker checklist-items-template-select"
                            data-none-selected-text="<?php echo _l('insert_checklist_templates') ?>"
                            data-width="100%" data-live-search="true">
                        <option value=""></option>
                        <?php foreach ($checklistTemplates as $chkTemplate) { ?>
                            <option value="<?php echo $chkTemplate['id']; ?>"> <?php echo $chkTemplate['description']; ?> </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="clearfix"></div>
                <h4 class="pull-left"><?php echo _l('progress'); ?></h4>
                <label class="switch pull-right">
                    <input id="manualprogress" type="checkbox" name="manualprogress" value=1
                           data-taskid= <?php echo $task->id ?> <?php echo $task->manualprogress == 1 ? "checked" : "" ?>>
                    <span class="slider round"></span>
                </label>
                <label class="pull-right mright10"><?php echo _l('manual'); ?>:</label>
                <div class="manualprogress <?php echo $task->manualprogress == 1 ? "" : "hide" ?>">
                    <div class="task-block checklist-block panel_s btmbrd">
                        <h4><?php echo _l('manual'); ?></h4>
                        <div class="progress mtop15">
                            <div class="progress-bar not-dynamic progress-bar-default task-manual-progress-bar"
                                 role="progressbar"
                                 data-percent="<?php echo isset($task->progresspercent) ? $task->progresspercent : 0 ?>"
                                 aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"
                                 style="width:0%">0%
                            </div>
                        </div>
                        <div class="slidecontainer">
                            <input name="progresspercent" type="range" min="0" max="100" value=<?php echo isset($task->progresspercent) ? $task->progresspercent : 0 ?> class="seek" id="myRange" data-taskid= <?php echo $task->id ?>>
                        </div>
                    </div>
                </div>
                <div class="checklistsprogress <?php echo $task->manualprogress == 1 ? "hide" : "" ?>">
                    <div class="task-block checklist-block panel_s btmbrd">
                        <h4 class="pull-left"><?php echo _l('task_checklist_items'); ?></h4>
                        <a href="#" onclick="add_task_checklist_item('<?php echo $task->id; ?>'); return false"
                           class="btn btn-icon add_task_checklist_item"> <span class="new-checklist-item"><i
                                        class="fa fa-plus"></i></span></a>
                        <div class="row checklist-items-wrapper">
                            <div class="col-md-12 ">
                                <div id="checklist-items">
                                    <?php $this->load->view('admin/tasks/checklist_items_template',
                                        array(
                                            'task_id' => $task->id,
                                            'checklists' => $task->checklist_items)); ?>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <p class="hide text-muted no-margin"
                   id="task-no-checklist-items"><?php echo _l('task_no_checklist_items_found'); ?></p>
            </div>
            <div class="task-block hide">
                <div class="mbot10 task-single-menu task-menu-options">
                    <div class="content-menu hide">
                        <ul>
                            <?php if (has_permission('tasks', '', 'edit')) { ?>
                                <li>
                                    <a href="<?php echo admin_url('tasks/task/' . $task->id); ?>"> <?php echo _l('task_single_edit'); ?> </a>
                                </li>
                            <?php } ?>
                            <?php if (has_permission('tasks', '', 'create')) { ?>

                                <li><a href="#" onclick="return false;" data-placement="bottom" data-toggle="popover"
                                       data-content="<?php echo htmlspecialchars($copy_template); ?>"
                                       data-html="true"><?php echo _l('task_copy'); ?></span></a></li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php if (has_permission('tasks', '', 'delete') || has_permission('tasks', '', 'edit') || has_permission('tasks', '', 'create')) { ?>
                        <a href="#" onclick="return false;" class="trigger manual-popover mright5"> <i
                                    class="fa fa-circle-thin" aria-hidden="true"></i> <i class="fa fa-circle-thin"
                                                                                         aria-hidden="true"></i> <i
                                    class="fa fa-circle-thin" aria-hidden="true"></i> </a>
                    <?php } ?>
                </div>


                <?php $custom_fields = get_custom_fields('tasks');
                foreach ($custom_fields as $field) { ?>
                    <?php $value = get_custom_field_value($task->id, $field['id'], 'tasks');
                    if ($value == '') {
                        continue;
                    } ?>
                    <div class="task-info text-muted">
                        <h5 class="task-info-custom-field"> <?php echo $field['name']; ?>: <?php echo $value; ?> </h5>
                    </div>
                <?php } ?>

                <?php
                if ($task->recurring == 1) {
                    echo '<span class="label label-info inline-block mbot5 mtop5">' . _l('recurring_task') . '</span>';
                }
                ?>
                <div class="clearfix"></div>
                <?php if ($task->current_user_is_assigned) {
                    foreach ($task->assignees as $assignee) {
                        if ($assignee['assigneeid'] == get_staff_user_id() && get_staff_user_id() != $assignee['assigned_from'] && $assignee['assigned_from'] != 0 || $assignee['is_assigned_from_contact'] == 1) {
                            if ($assignee['is_assigned_from_contact'] == 0) {
                                echo '<p class="text-muted mtop10 task-assigned-from">' . _l('task_assigned_from', '<a href="' . admin_url('profile/' . $assignee['assigned_from']) . '" target="_blank">' . get_staff_full_name($assignee['assigned_from'])) . '</a></p>';
                            } else {
                                echo '<p class="text-muted mtop10 task-assigned-from task-assigned-from-contact">' . _l('task_assigned_from', get_contact_full_name($assignee['assigned_from'])) . '<br /><span class="label inline-block mtop5 label-info">' . _l('is_customer_indicator') . '</span></p>';
                            }
                            break;
                        }
                    }
                } ?>
                <h4 class="task-info-heading mbot15"><i class="fa fa-users"
                                                        aria-hidden="true"></i> <?php echo _l('task_single_assignees'); ?>
                </h4>
                <?php if (has_permission('tasks', '', 'edit') || has_permission('tasks', '', 'create')) { ?>
                    <select data-width="100%" <?php if ($task->rel_type == 'project') { ?> data-live-search-placeholder="<?php echo _l('search_project_members'); ?>" <?php } ?>
                            data-task-id="<?php echo $task->id; ?>" id="add_task_assignees"
                            class="text-muted mbot10 task-action-select selectpicker<?php if (total_rows('tblstafftaskassignees', array('taskid' => $task->id)) == 0) {
                                echo ' task-assignees-dropdown-indicator';
                            } ?>" name="select-assignees" data-live-search="true"
                            title='<?php echo _l('task_single_assignees_select_title'); ?>'
                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <?php
                        $options = '';
                        foreach ($staff as $assignee) {
                            if (total_rows('tblstafftaskassignees', array(
                                    'staffid' => $assignee['staffid'],
                                    'taskid' => $task->id
                                )) == 0) {
                                if ($task->rel_type == 'project') {
                                    if (total_rows('tblprojectmembers', array(
                                            'project_id' => $task->rel_id,
                                            'staff_id' => $assignee['staffid']
                                        )) == 0) {
                                        continue;
                                    }
                                }
                                $options .= '<option value="' . $assignee['staffid'] . '">' . get_staff_full_name($assignee['staffid']) . '</option>';
                            }
                        }
                        echo $options;
                        ?>
                    </select>
                <?php } ?>

            </div>
        </div>
        <div class="row">

            <div class="col-md-12 task-single-col-right">
                <h4><?php echo _l('task_view_attachments'); ?></h4>

                <div class="task-block panel_s btmbrd">
                    <div class="row">
                        <div class="col-sm-2">
                            <?php echo form_open_multipart('admin/tasks/upload_file', array('id' => 'task-attachment', 'class' => 'dropzone')); ?> <?php echo form_close(); ?>
                            <?php if (get_option('dropbox_app_key') != '') { ?>
                                <div class="text-center mtop10">
                                    <div id="dropbox-chooser-task"></div>
                                </div>

                            <?php } ?>
                        </div>
                        <div class="col-sm-10">
                            <?php if (count($task->attachments) > 0) { ?>
                                <div class="row task_attachments_wrapper">
                                    <div class="col-md-12" id="attachments">

                                        <div class="row">
                                            <?php
                                            $i = 1;
                                            // Store all url related data here
                                            $attachments_data = array();
                                            $show_more_link_task_attachments = do_action('show_more_link_task_attachments', 6);
                                            foreach ($task->attachments as $attachment) { ?>
                                                <div data-commentid="<?php echo $attachment['comment_file_id']; ?>"
                                                     data-task-attachment-id="<?php echo $attachment['id']; ?>"
                                                     class="task-attachment-col col-md-2<?php if ($i > $show_more_link_task_attachments) {
                                                         echo ' hide task-attachment-col-more';
                                                     } ?>">
                                                    <ul class="list-unstyled task-attachment-wrapper">
                                                        <li class="mbot10 task-attachment<?php if (strtotime($attachment['dateadded']) >= strtotime('-16 hours')) {
                                                            echo ' highlight-bg';
                                                        } ?>">
                                                            <div class="mbot10 pull-right task-attachment-user">
                                                                <?php if ($attachment['staffid'] == get_staff_user_id() || is_admin()) { ?>
                                                                    <a href="#" class="pull-right"
                                                                       onclick="remove_task_attachment(this,<?php echo $attachment['id']; ?>); return false;">
                                                                        <i class="fa fa fa-times"></i> </a>
                                                                <?php }
                                                                $externalPreview = false;
                                                                $is_image = false;
                                                                $path = get_upload_path_by_type('task') . $task->id . '/' . $attachment['file_name'];
                                                                $href_url = site_url('download/file/taskattachment/' . $attachment['id']);
                                                                $isHtml5Video = is_html5_video($path);
                                                                if (empty($attachment['external'])) {
                                                                    $is_image = is_image($path);
                                                                    $img_url = site_url('download/preview_image?path=' . protected_file_url_by_path($path, true) . '&type=' . $attachment['filetype']);
                                                                    $img_url = site_url('download/file/taskattachment/' . $attachment['id']);
                                                                } else if ((!empty($attachment['thumbnail_link']) || !empty($attachment['external']))
                                                                    && !empty($attachment['thumbnail_link'])) {
                                                                    $is_image = true;
                                                                    $img_url = optimize_dropbox_thumbnail($attachment['thumbnail_link']);
                                                                    $externalPreview = $img_url;
                                                                    $href_url = $attachment['external_link'];
                                                                } else if (!empty($attachment['external']) && empty($attachment['thumbnail_link'])) {
                                                                    $href_url = $attachment['external_link'];
                                                                }
                                                                if (!empty($attachment['external']) && $attachment['external'] == 'dropbox' && $is_image) { ?>
                                                                    <a href="<?php echo $href_url; ?>" target="_blank"
                                                                       class="" data-toggle="tooltip"
                                                                       data-title="<?php echo _l('open_in_dropbox'); ?>"><i
                                                                                class="fa fa-dropbox"
                                                                                aria-hidden="true"></i></a>
                                                                <?php }
                                                                if ($attachment['staffid'] != 0) {
                                                                    echo '<a href="javascript:void(0)">' . get_staff_full_name($attachment['staffid']) . '</a> - ';
                                                                } else if ($attachment['contact_id'] != 0) {
                                                                    echo '<a href="javascript:void(0)">' . get_contact_full_name($attachment['contact_id']) . '</a> - ';
                                                                }
                                                                echo time_ago($attachment['dateadded']);
                                                                ?>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                            <?php ob_start(); ?>
                                                            <div class="<?php if ($is_image) {
                                                                echo 'preview-image';
                                                            } else if (!$isHtml5Video) {
                                                                echo 'task-attachment-no-preview';
                                                            } ?>">
                                                                <?php
                                                                // Not link on video previews because on click on the video is opening new tab
                                                                if (!$isHtml5Video){ ?>
                                                                <a href="<?php echo(!$externalPreview ? $href_url : $externalPreview); ?>"
                                                                   target="_blank"<?php if ($is_image) { ?> data-lightbox="task-attachment"<?php } ?>
                                                                   class="<?php if ($isHtml5Video) {
                                                                       echo 'video-preview';
                                                                   } ?>">
                                                                    <?php } ?>
                                                                    <?php if ($is_image) { ?>
                                                                        <img src="<?php echo $img_url; ?>"
                                                                             class="img img-responsive">
                                                                    <?php } else if ($isHtml5Video) { ?>
                                                                        <video width="100%" height="100%"
                                                                               src="<?php echo site_url('download/preview_video?path=' . protected_file_url_by_path($path) . '&type=' . $attachment['filetype']); ?>"
                                                                               controls> Your browser does not support
                                                                            the video tag.
                                                                        </video>
                                                                    <?php } else { ?>
                                                                        <div class="file-icon"><i
                                                                                    class="<?php echo get_file_class($attachment['filetype']); ?>"></i>
                                                                        </div>
                                                                        <?php echo pathinfo($attachment['file_name'])['filename'];//echo $attachment['file_name']; ?>
                                                                    <?php } ?>
                                                                    <?php if (!$isHtml5Video){ ?>
                                                                </a>
                                                            <?php } ?>
                                                            </div>
                                                            <?php
                                                            $attachments_data[$attachment['id']] = ob_get_contents();
                                                            ob_end_clean();
                                                            echo $attachments_data[$attachment['id']];
                                                            ?>
                                                            <div class="clearfix"></div>
                                                        </li>
                                                    </ul>
                                                </div>
                                                <?php
                                                $i++;
                                            } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <?php if (($i - 1) > $show_more_link_task_attachments) { ?>
                                    <div class="col-md-12" id="show-more-less-task-attachments-col">
                                        <a href="#" class="task-attachments-more"
                                           onclick="slideToggle('.task-attachment-col-more',task_attachments_toggle); return false;"><?php echo _l('show_more'); ?></a>
                                        <a href="#" class="task-attachments-less hide"
                                           onclick="slideToggle('.task-attachment-col-more',task_attachments_toggle); return false;"><?php echo _l('show_less'); ?></a>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <h4 class="pull-left"><?php echo _l('task_comments'); ?></h4>
                <a href="#" class="pull-right hide" id="taskCommentSlide"
                   onclick="slideToggle('.tasks-comments'); return false;">
                    <i class="fa fa-caret-down"></i>
                </a>
                <div class="clearfix"></div>
                <div class="task-block panel_s btmbrd">
                    <div class="tasks-comments inline-block full-width" <?php //if(count($task->comments) == 0){echo 'style="display:none"';} ?>>
                        <textarea name="comment" placeholder="<?php echo _l('task_single_add_new_comment'); ?>"
                                  id="task_comment" rows="3" class="form-control"></textarea>
                        <button type="button" class="btn btn-info mtop20 pull-right" autocomplete="off"
                                onclick="add_task_comment('<?php echo $task->id; ?>');"> <?php echo _l('task_single_add_new_comment'); ?> </button>
                        <div class="clearfix"></div>
                        <hr/>
                        <div id="task-comments" class="mtop10">
                            <?php
                            $comments = '';
                            $len = count($task->comments);
                            $i = 0;
                            foreach ($task->comments as $comment) {
                                $comments .= '<div id="comment_' . $comment['id'] . '" data-commentid="' . $comment['id'] . '" data-task-attachment-id="' . $comment['file_id'] . '" class="tc-content task-comment' . (strtotime($comment['dateadded']) >= strtotime('-16 hours') ? ' highlight-bg' : '') . '">';
                                $comments .= '<small class="mtop5 text-muted"><a data-task-comment-href-id="' . $comment['id'] . '" href="' . admin_url('tasks/view/' . $task->id) . '#comment_' . $comment['id'] . '" class="task-date-as-comment-id">' . time_ago($comment['dateadded']) . '</a></small>';
                                if ($comment['staffid'] != 0) {
                                    $comments .= '<a href="' . admin_url('profile/' . $comment['staffid']) . '" target="_blank">' . staff_profile_image($comment['staffid'], array(
                                            'staff-profile-image-small',
                                            'media-object img-circle pull-left mright10'
                                        )) . '</a>';
                                } elseif ($comment['contact_id'] != 0) {
                                    $comments .= '<img src="' . contact_profile_image_url($comment['contact_id']) . '" class="client-profile-image-small media-object img-circle pull-left mright10">';
                                }
                                if ($comment['staffid'] == get_staff_user_id() || is_admin()) {
                                    $comment_added = strtotime($comment['dateadded']);
                                    $minus_1_hour = strtotime('-1 hours');
                                    if (get_option('client_staff_add_edit_delete_task_comments_first_hour') == 0 || (get_option('client_staff_add_edit_delete_task_comments_first_hour') == 1 && $comment_added >= $minus_1_hour) || is_admin()) {
                                        $comments .= '<div class="text-right mright10"><a class="show_act" href="javascript:void(0)"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></a></div><div class="table_actions"><ul><li><span class=""><a href="#" onclick="edit_task_comment(' . $comment['id'] . '); return false;" class=""><i class="fa fa-pencil-square-o"></i>Edit</a></span></li>';
                                        $comments .= '<li><span class=""><a href="#" onclick="remove_task_comment(' . $comment['id'] . '); return false;" class=""><i class="fa fa-remove"></i>Delete</a></span></li></ul></div>';
                                    }
                                }
                                $comments .= '<div class="media-body">';
                                if ($comment['staffid'] != 0) {
                                    $comments .= get_staff_full_name($comment['staffid']) . '<br />';
                                } elseif ($comment['contact_id'] != 0) {
                                    $comments .= '<span class="label label-info mtop5 inline-block">' . _l('is_customer_indicator') . '</span><br />' . get_contact_full_name($comment['contact_id']);
                                }
                                $comments .= '<div data-edit-comment="' . $comment['id'] . '" class="hide edit-task-comment mright10"><textarea rows="3" class="form-control" id="task_comment_' . $comment['id'] . '">' . $comment['content'] . '</textarea>
            <div class="clearfix mtop20"></div>
            <button type="button" class="btn btn-info pull-right" onclick="save_edited_comment(' . $comment['id'] . ',' . $task->id . ')">' . _l('submit') . '</button>
            <button type="button" class="btn btn-default pull-right mright5" onclick="cancel_edit_comment(' . $comment['id'] . ')">' . _l('cancel') . '</button>
         </div>';
                                if ($comment['file_id'] != 0) {
                                    $comment['content'] = str_replace('[task_attachment]', $attachments_data[$comment['file_id']], $comment['content']);
                                    // Replace lightbox to prevent loading the image twice
                                    $comment['content'] = str_replace('data-lightbox="task-attachment"', 'data-lightbox="task-attachment-comment"', $comment['content']);
                                }
                                $comments .= '<div class="comment-content mtop10">' . app_happy_text(check_for_links($comment['content'])) . '</div>';
                                $comments .= '</div>';
                                $comments .= '</div>';
                                $i++;
                            }
                            echo $comments;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">

    $('#task_comment').keyup(function () {
        if ($('#task_comment').hasClass('task-error')) {
            $('#task_comment').removeClass('task-error');
            $('#comment-error').remove();
        }
    });
    var inner_popover_template = '<div class="popover" style="width:300px !important"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content" </div></div></div>';

    $('.task-menu-options .trigger').popover({
        html: true,
        placement: "bottom",
        trigger: 'click',
        title: "<?php echo _l('actions'); ?>",
        content: function () {
            return $('body').find('.task-menu-options .content-menu').html();
        },
        template: inner_popover_template
    });

    $('.task-menu-status .trigger').popover({
        html: true,
        placement: "bottom",
        trigger: 'click',
        title: "<?php echo _l('task_status'); ?>",
        content: function () {
            return $('body').find('.task-menu-status .content-menu').html();
        },
        template: inner_popover_template
    });

    tinyMCE.remove('#task_view_description');

    if (typeof (Dropbox) != 'undefined') {
        document.getElementById("dropbox-chooser-task").appendChild(Dropbox.createChooseButton({
            success: function (files) {
                $.post(admin_url + 'tasks/add_external_attachment', {
                    files: files,
                    task_id: '<?php echo $task->id; ?>',
                    external: 'dropbox'
                }).done(function () {
                    init_task_modal('<?php echo $task->id; ?>');
                });
            },
            linkType: "preview",
            extensions: app_allowed_files.split(','),
        }));
    }

    init_selectpicker();
    init_datepicker();
    init_lightbox({positionFromTop: 120});

    if (typeof (taskAttachmentDropzone) != 'undefined') {
        taskAttachmentDropzone.destroy();
    }

    taskAttachmentDropzone = new Dropzone("#task-attachment", {
        autoProcessQueue: true,
        createImageThumbnails: false,

        dictDefaultMessage: appLang.drop_files_here_to_upload,
        dictFallbackMessage: appLang.browser_not_support_drag_and_drop,
        dictFileTooBig: appLang.file_exceeds_maxfile_size_in_form,
        dictCancelUpload: appLang.cancel_upload,
        dictMaxFilesExceeded: appLang.you_can_not_upload_any_more_files,
        maxFilesize: (max_php_ini_upload_size_bytes / (1024 * 1024)).toFixed(0),
        maxFiles: 10,
        acceptedFiles: app_allowed_files,
        error: function (file, response) {
            alert_float('danger', response);
        },
        sending: function (file, xhr, formData) {
            formData.append("taskid", '<?php echo $task->id; ?>');
        },
        success: function (files, response) {
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                //init_task_modal('<?php echo $task->id; ?>');
                alert_float('success', "Attachment uploaded successfully.");
                window.location.reload();
            }
        }
    });
    $(function () {
        $("#checklist-items").sortable({
            helper: 'clone',
            items: 'div.checklist',
            update: function (event, ui) {
                update_checklist_order();
            }
        });

        setTimeout(function () {
            do_task_checklist_items_height();
        }, 200);

        /**
         * Added By : Vaidehi
         * Dt : 11/17/2017
         * to make visible 0% on task dashboard
         */
        if ($(".task-progress-bar, .task-manual-progress-bar").html() == '0%') {
            $(".task-progress-bar, .task-manual-progress-bar").addClass('zero-progress');
        } else {
            $(".task-progress-bar, .task-manual-progress-bar").removeClass('zero-progress');
        }
    });
</script>
</body>
</html>
