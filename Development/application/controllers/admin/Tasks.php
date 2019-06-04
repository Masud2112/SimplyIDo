<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Tasks extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('projects_model');
    }

    /* Open also all taks if user access this /tasks url */
    public function index($id = '')
    {
        $this->list_tasks($id);
    }

    /* List all tasks */
    public function list_tasks($id = '')
    {
        $pg = $this->input->get('pg');
        // if passed from url
        $_custom_view = '';
        if ($this->input->get('custom_view')) {
            $_custom_view = $this->input->get('custom_view');
        }

        if ($this->input->is_ajax_request()) {
            if ($this->input->get('kanban')) {
                $data = array();
                echo $this->load->view('admin/tasks/kan_ban', $data, true);
                die();
            } else {
                $this->perfex_base->get_table_data('tasks');
            }
        }
        $data['taskid'] = '';
        if (is_numeric($id)) {
            $data['taskid'] = $id;
        }

        if ($this->input->get('kanban')) {
            $this->switch_kanban(0, true);
        }

        $data['switch_kanban'] = false;
        $data['bodyclass'] = 'tasks_page';
        if ($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') {
            $data['switch_kanban'] = true;
            $data['bodyclass'] = 'tasks_page kan-ban-body';
        }

        /*if(is_mobile()){
            $this->session->set_userdata(array(
            'tasks_kanban_view' => 0
            ));
        }*/
        $data['custom_view'] = $_custom_view;
        $data['title'] = _l('tasks');
        $data['statuses'] = $this->tasks_model->get_status();
        $data['members'] = $this->staff_model->get('', 1, array(
            'is_not_staff' => 0
        ));
        if ($this->input->get('lid')) {
            $leadid = $this->input->get('lid');

            $this->load->model('leads_model');

            $data['lid'] = $leadid;
            $data['lname'] = '';
            if ($leadid != "") {
                $data['lname'] = $this->leads_model->get($leadid)->name;
            }
        } elseif ($this->input->get('pid')) {
            $projectid = $this->input->get('pid');

            $this->load->model('projects_model');

            $data['pid'] = $projectid;
            $data['lname'] = '';
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        } elseif ($this->input->get('eid')) {
            $projectid = $this->input->get('eid');
            $parent_id = $this->projects_model->get($projectid)->parent;
            $this->load->model('projects_model');

            $data['eid'] = $projectid;
            $data['parent_id'] = $parent_id;
            $data['lname'] = '';
            $data['pname'] = '';
            if ($parent_id != "") {
                $data['pname'] = $this->projects_model->get($parent_id)->name;
            }
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        }
        $data['pg'] = $pg;
        $this->load->view('admin/tasks/manage', $data);
    }

    public function tasks_kanban_load_more()
    {
        $status = $this->input->get('status');
        $page = $this->input->get('page');
        $where = array();
        if ($this->input->get('project_id')) {
            $where['rel_id'] = $this->input->get('project_id');
            $where['rel_type'] = 'project';
        }


        $tasks = $this->tasks_model->do_kanban_query($status, $this->input->get('search'), $page, false, $where);

        foreach ($tasks as $task) {
            $this->load->view('admin/tasks/_kan_ban_card', array(
                'task' => $task,
                'status' => $status
            ));
        }
    }

    public function update_order()
    {
        $this->tasks_model->update_order($this->input->post());
    }

    public function switch_kanban($set = 0, $manual = false)
    {
        if ($set == 1) {
            $set = 'false';
        } else {
            $set = 'true';
        }

        $this->session->set_userdata(array(
            'tasks_kanban_view' => $set
        ));
        if ($manual == false) {
            // clicked on VIEW KANBAN from projects area and will redirect again to the same view
            if (strpos($_SERVER['HTTP_REFERER'], 'project_id') !== false) {
                redirect(admin_url('tasks'));
            } else {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function update_task_description($id)
    {
        if (has_permission('tasks', '', 'edit', true)) {
            $this->db->where('id', $id);
            $this->db->update('tblstafftasks', array(
                'description' => $this->input->post('description', false)
            ));
        }
    }

    public function detailed_overview()
    {
        $overview = array();
        $brandid = get_user_session();
        $has_permission_create = has_permission('tasks', '', 'create', true);
        $has_permission_view = has_permission('tasks', '', 'view', true);
        $lid = $this->input->get('lid');
        $pid = $this->input->get('pid');
        $eid = $this->input->get('eid');

        if (!$has_permission_create) {
            $staff_id = get_staff_user_id();
        } elseif ($this->input->post('member')) {
            $staff_id = $this->input->post('member');
        } else {
            $staff_id = '';
        }

        $month = ($this->input->post('month') ? $this->input->post('month') : date('m'));
        if ($this->input->post() && $this->input->post('month') == '') {
            $month = '';
        }

        $status = $this->input->post('status');

        $fetch_month_from = 'startdate';

        $year = ($this->input->post('year') ? $this->input->post('year') : date('Y'));
        $project_id = $this->input->get('project_id');

        for ($m = 1; $m <= 12; $m++) {
            if ($month != '' && $month != $m) {
                continue;
            }
            $this->db->where('MONTH(' . $fetch_month_from . ')', $m);
            $this->db->where('YEAR(' . $fetch_month_from . ')', $year);

            if ($project_id && $project_id != '') {
                $this->db->where('rel_id', $project_id);
                $this->db->where('rel_type', 'project');
            }

            if (isset($lid) && $lid != "") {
                $this->db->where('rel_id', $lid);
                $this->db->where('rel_type', 'lead');
            }

            if (isset($pid) && $pid != "") {
                $this->db->where('rel_id', $pid);
                $this->db->where('rel_type', 'project');
            }

            if (isset($eid) && $eid != "") {
                $this->db->where('rel_id', $eid);
                $this->db->where('rel_type', 'event');
            }

            $this->db->where('deleted = 0');
            if ($brandid > 0) {
                $this->db->where('brandid = ' . $brandid);
            }

            if (is_numeric($staff_id)) {
                $this->db->where('(id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid=' . $staff_id . '))');
            }

            // User dont have permission for view but have for create
            // Only show tasks createad by this user.
            if (!$has_permission_view && $has_permission_create) {
                $this->db->where('addedfrom', get_staff_user_id());
            }

            if ($status) {
                $this->db->where('status', $status);
            }


            $this->db->order_by($fetch_month_from, 'ASC');
            array_push($overview, $m);
            $overview[$m] = $this->db->get('tblstafftasks')->result_array();
            //echo $this->db->last_query();exit;
        }

        unset($overview[0]);

        $overview = array(
            'staff_id' => $staff_id,
            'detailed' => $overview
        );

        $data['lid'] = $lid;
        $data['pid'] = $pid;
        $data['eid'] = $eid;
        $data['members'] = $this->staff_model->get();
        $data['overview'] = $overview['detailed'];
        $data['years'] = $this->tasks_model->get_distinct_tasks_years(($this->input->post('month_from') ? $this->input->post('month_from') : 'startdate'));
        $data['staff_id'] = $overview['staff_id'];
        $data['title'] = _l('detailed_overview');
        $this->load->view('admin/tasks/detailed_overview', $data);
    }

    public function init_relation_tasks($rel_id, $rel_type)
    {
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('tasks_relations', array(
                'rel_id' => $rel_id,
                'rel_type' => $rel_type
            ));
        }
    }

    /* Add new task or update existing */
    public function task($id = '')
    {

        $lid = $this->input->get('lid');
        $pid = $this->input->get('pid');
        $eid = $this->input->get('eid');
        $pg = $this->input->get('pg');

        if (!has_permission('tasks', '', 'edit', true) && !has_permission('tasks', '', 'create', true)) {
            access_denied('Tasks');
        }

        $data = array();
        // FOr new task add directly from the projects milestones
        if ($this->input->get('milestone_id')) {
            $this->db->where('id', $this->input->get('milestone_id'));
            $milestone = $this->db->get('tblmilestones')->row();
            if ($milestone) {
                $data['_milestone_selected_data'] = array(
                    'id' => $milestone->id,
                    'due_date' => _d($milestone->due_date)
                );
            }
        }
        if ($this->input->get('start_date')) {
            $data['start_date'] = $this->input->get('start_date');
        }
        if ($this->input->post()) {

            $postlid = $this->input->post('hdnlid');
            $postpid = $this->input->post('hdnpid');
            $posteid = $this->input->post('hdneid');
            $pg = $this->input->post('pg');

            $data = $this->input->post();
            unset($data['pg']);
            $data['description'] = $this->input->post('description', false);
            if ($id == '') {
                if (!has_permission('tasks', '', 'create', true)) {
                    access_denied('Tasks');
                }
                $id = $this->tasks_model->add($data);
                if ($id) {
                    $uploadedFiles = handle_task_attachments_array($id);
                    if ($uploadedFiles && is_array($uploadedFiles)) {
                        foreach ($uploadedFiles as $file) {
                            $this->misc_model->add_attachment_to_database($id, 'task', array($file));
                        }
                    }

                    set_alert('success', _l('added_successfully', _l('task')));

                    if (isset($postlid) && $postlid != "") {
                        redirect(admin_url('tasks/' . "?lid=" . $postlid));
                    } elseif (isset($postpid) && $postpid != "") {
                        redirect(admin_url('tasks/' . "?pid=" . $postpid));
                    } elseif (isset($posteid) && $posteid != "") {
                        redirect(admin_url('tasks/' . "?eid=" . $posteid));
                    } elseif (isset($pg) && $pg == "calendar") {
                        redirect(admin_url('calendar'));
                    } elseif (isset($pg) && $pg == "home") {
                        redirect(admin_url());
                    } else {
                        redirect(admin_url('tasks'));
                    }
                }

            } else {
                if (!has_permission('tasks', '', 'edit', true)) {
                    access_denied('Tasks');
                }
                $success = $this->tasks_model->update($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('task'));
                }
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('task')));
                    if (isset($postlid) && $postlid != "") {
                        redirect(admin_url('tasks/' . "?lid=" . $postlid));
                    } elseif (isset($postpid) && $postpid != "") {
                        redirect(admin_url('tasks/' . "?pid=" . $postpid));
                    } elseif (isset($posteid) && $posteid != "") {
                        redirect(admin_url('tasks/' . "?eid=" . $posteid));
                    } elseif (isset($pg) && $pg != "") {
                        redirect(admin_url('calendar'));
                    } else {
                        redirect(admin_url('tasks/'));
                    }
                }
                if (isset($postlid) && $postlid != "") {
                    redirect(admin_url('tasks/task/' . $id . "?lid=" . $postlid));
                } elseif (isset($postpid) && $postpid != "") {
                    redirect(admin_url('tasks/task/' . $id . "?pid=" . $postpid));
                } elseif (isset($posteid) && $posteid != "") {
                    redirect(admin_url('tasks/task/' . $id . "?eid=" . $posteid));
                } elseif (isset($pg) && $pg != "") {
                    redirect(admin_url('calendar'));
                } else {
                    redirect(admin_url('tasks/task/' . $id));
                }
            }
            die;
        }

        $data['milestones'] = array();
        $data['checklistTemplates'] = $this->tasks_model->get_checklist_templates();
        if ($id == '') {
            $title = _l('add_new', _l('task'));
        } else {
            $data['task'] = $this->tasks_model->get($id);
            if ($data['task']->rel_type == 'project') {
                $data['milestones'] = $this->projects_model->get_milestones($data['task']->rel_id);
            }
            //Added By Avni on 12/04/2017
            $data['task']->reminders = $this->tasks_model->get_task_usersreminder($id);
            $title = _l('edit', _l('task')) . ' ' . $data['task']->name;
        }
        $data['project_end_date_attrs'] = array();
        if ($this->input->get('rel_type') == 'project' && $this->input->get('rel_id')) {
            $project = $this->projects_model->get($this->input->get('rel_id'));
            if ($project->deadline) {
                $data['project_end_date_attrs'] = array(
                    'data-date-end-date' => $project->deadline
                );
            }
        }
        $pid = $this->input->get('pid');
        $eid = $this->input->get('eid');
        $data['tags'] = $this->tags_model->get();
        $data['id'] = $id;
        $data['title'] = $title;
        $data['lid'] = $this->input->get('lid');
        $data['pid'] = $this->input->get('pid');
        $data['eid'] = $this->input->get('eid');
        $data['pg'] = $this->input->get('pg');
        $data['statuses'] = $this->tasks_model->get_status();
        $data['leads'] = $this->tasks_model->get_leads();
        $data['projects'] = $this->tasks_model->get_projects();
        $data['events'] = $this->tasks_model->get_events($pid);
        $data['members'] = $this->staff_model->get('', 1, array(
            'is_not_staff' => 0
        ));
        if ($data['lid']) {
            $this->load->model('leads_model');
            $data['lname'] = '';
            if ($data['lid'] != "") {
                $data['lname'] = $this->leads_model->get($data['lid'])->name;
            }
        } elseif ($data['pid']) {
            //echo '<pre>'; print_r($this->projects_model->get($this->input->get('pid'))); die;
            $this->load->model('leads_model');
            $data['lname'] = '';
            $data['parent_id'] = $this->projects_model->get($this->input->get('pid'))->parent;
            if ($data['pid'] != "") {
                $data['lname'] = $this->projects_model->get($data['pid'])->name;
            }
        } elseif ($data['eid']) {
            $projectid = $this->input->get('eid');
            $parent_id = $this->projects_model->get($projectid)->parent;
            $this->load->model('projects_model');

            $data['eid'] = $projectid;
            $data['parent_id'] = $parent_id;
            $data['lname'] = '';
            $data['pname'] = '';
            if ($parent_id != "") {
                $data['pname'] = $this->projects_model->get($parent_id)->name;
            }
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        }
        //echo '<pre>==>'; print_r($data); die;
        //Added By Avni on 12/04/2017
        $data['reminders'] = get_meeting_reminders();
        $this->load->view('admin/tasks/task', $data);
    }

    public function copy()
    {
        if (has_permission('tasks', '', 'create', true)) {
            $new_task_id = $this->tasks_model->copy($this->input->post());
            $response = array(
                'new_task_id' => '',
                'alert_type' => 'warning',
                'message' => _l('failed_to_copy_task'),
                'success' => false
            );
            if ($new_task_id) {
                $response['message'] = _l('task_copied_successfully');
                $response['new_task_id'] = $new_task_id;
                $response['success'] = true;
                $response['alert_type'] = 'success';
            }
            echo json_encode($response);
        }
    }

    public function get_billable_task_data($task_id)
    {
        $task = $this->tasks_model->get_billable_task_data($task_id);
        $task->description = seconds_to_time_format($task->total_seconds) . ' ' . _l('hours');
        echo json_encode($task);
    }

    /* Get task data in a right pane */
    public function get_task_data()
    {
        $taskid = $this->input->post('taskid');

        $tasks_where = array();

        if (!has_permission('tasks', '', 'view', true)) {
            $tasks_where = get_tasks_where_string(false);
        }

        $task = $this->tasks_model->get($taskid, $tasks_where);

        if (!$task) {
            header("HTTP/1.0 404 Not Found");
            echo 'Task not found';
            die();
        }

        $data['checklistTemplates'] = $this->tasks_model->get_checklist_templates();
        $data['task'] = $task;
        $data['id'] = $task->id;
        $data['staff'] = $this->staff_model->get('', 1);
        $data['task_is_billed'] = $this->tasks_model->is_task_billed($taskid);

        $this->load->view('admin/tasks/view_task_template', $data);
    }

    public function get_staff_started_timers()
    {
        $data['startedTimers'] = $this->misc_model->get_staff_started_timers();
        $_data['html'] = $this->load->view('admin/tasks/started_timers', $data, true);
        if (count($data['startedTimers']) > 0) {
            $_data['timers_found'] = true;
        }
        echo json_encode($_data);
    }

    public function save_checklist_item_template()
    {
        if (has_permission('checklist_templates', '', 'create', true)) {
            $id = $this->tasks_model->add_checklist_template($this->input->post('description'));
            echo json_encode(array('id' => $id));
        }
    }

    public function remove_checklist_item_template($id)
    {
        if (has_permission('checklist_templates', '', 'delete', true)) {
            $success = $this->tasks_model->remove_checklist_item_template($id);
            echo json_encode(array('success' => $success));
        }
    }

    public function init_checklist_items()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $post_data = $this->input->post();
                $data['task_id'] = $post_data['taskid'];
                $data['checklists'] = $this->tasks_model->get_checklist_items($post_data['taskid']);
                $this->load->view('admin/tasks/checklist_items_template', $data);
            }
        }
    }

    public function task_tracking_stats($task_id)
    {
        $data['stats'] = json_encode($this->tasks_model->task_tracking_stats($task_id));
        $this->load->view('admin/tasks/tracking_stats', $data);
    }

    public function checkbox_action($listid, $value)
    {
        $this->db->where('id', $listid);
        $this->db->update('tbltaskchecklists', array(
            'finished' => $value
        ));

        if ($this->db->affected_rows() > 0) {
            if ($value == 1) {
                $this->db->where('id', $listid);
                $this->db->update('tbltaskchecklists', array(
                    'finished_from' => get_staff_user_id()
                ));
                do_action('task_checklist_item_finished', $listid);
            }
        }
    }

    public function add_checklist_item()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                echo json_encode(array(
                    'success' => $this->tasks_model->add_checklist_item($this->input->post())
                ));
            }
        }
    }

    public function update_checklist_order()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $this->tasks_model->update_checklist_order($this->input->post());
            }
        }
    }

    public function delete_checklist_item($id)
    {
        $list = $this->tasks_model->get_checklist_item($id);
        if (has_permission('tasks', '', 'delete', true) || $list->addedfrom == get_staff_user_id()) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(array(
                    'success' => $this->tasks_model->delete_checklist_item($id)
                ));
            }
        }
    }

    public function update_checklist_item()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post()) {
                $desc = $this->input->post('description');
                $desc = trim($desc);
                $this->tasks_model->update_checklist_item($this->input->post('listid'), $desc);
                echo json_encode(array('can_be_template' => (total_rows('tblcheckliststemplates', array('description' => $desc)) == 0)));
            }
        }
    }

    public function make_public($task_id)
    {
        if (!has_permission('tasks', '', 'edit', true)) {
            json_encode(array(
                'success' => false
            ));
            die;
        }
        echo json_encode(array(
            'success' => $this->tasks_model->make_public($task_id)
        ));
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->tasks_model->add_attachment_to_database($this->input->post('task_id'), $this->input->post('files'), $this->input->post('external'));
        }
    }

    /* Add new task comment / ajax */
    public function add_task_comment()
    {
        echo json_encode(array(
            'success' => $this->tasks_model->add_task_comment($this->input->post(null, false))
        ));
    }

    /* Add new task follower / ajax */
    public function add_task_followers()
    {
        if (has_permission('tasks', '', 'edit', true) || has_permission('tasks', '', 'create', true)) {
            echo json_encode(array(
                'success' => $this->tasks_model->add_task_followers($this->input->post())
            ));
        }
    }

    /* Add task assignees / ajax */
    public function add_task_assignees()
    {
        if (has_permission('tasks', '', 'edit', true) || has_permission('tasks', '', 'create', true)) {
            echo json_encode(array(
                'success' => $this->tasks_model->add_task_assignees($this->input->post())
            ));
        }
    }

    public function edit_comment()
    {
        if ($this->input->post()) {
            $success = $this->tasks_model->edit_comment($this->input->post(null, false));
            $message = '';
            if ($success) {
                $message = _l('task_comment_updated');
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
        }
    }

    /* Remove task comment / ajax */
    public function remove_comment($id)
    {
        echo json_encode(array(
            'success' => $this->tasks_model->remove_comment($id)
        ));
    }

    /* Remove assignee / ajax */
    public function remove_assignee($id, $taskid)
    {
        if (has_permission('tasks', '', 'edit', true) && has_permission('tasks', '', 'create', true)) {
            $success = $this->tasks_model->remove_assignee($id, $taskid);
            $message = '';
            if ($success) {
                $message = _l('task_assignee_removed');
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
        }
    }

    /* Remove task follower / ajax */
    public function remove_follower($id, $taskid)
    {
        if (has_permission('tasks', '', 'edit', true) && has_permission('tasks', '', 'create', true)) {
            $success = $this->tasks_model->remove_follower($id, $taskid);
            $message = '';
            if ($success) {
                $message = _l('task_follower_removed');
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
        }
    }

    /* Mark task as complete / ajax*/
    public function mark_complete($id)
    {
        $task = $this->tasks_model->get($id);
        $success = $this->tasks_model->mark_complete($id);
        if ($task->current_user_is_assigned || $task->current_user_is_creator || is_admin()) {
            $message = '';
            if ($success) {
                $message = _l('task_marked_as_complete');
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => ''
            ));
        }
    }

    public function mark_as($status, $id)
    {
        $task = $this->tasks_model->get($id);

        if ($task->current_user_is_assigned || $task->current_user_is_creator || is_admin()) {
            $success = $this->tasks_model->mark_as($status, $id);
            $message = '';

            if ($success) {
                $message = _l('task_marked_as_success', format_task_status($status, true, true));
            }

            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));

        } else {
            echo json_encode(array(
                'success' => false,
                'message' => ''
            ));
        }
    }

    /* Unmark task as complete / ajax*/
    public function unmark_complete($id)
    {
        $task = $this->tasks_model->get($id);

        if ($task->current_user_is_assigned || $task->current_user_is_creator || is_admin()) {
            $success = $this->tasks_model->unmark_complete($id);
            $message = '';
            if ($success) {
                $message = _l('task_unmarked_as_complete');
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
        } else {
            echo json_encode(array(
                'success' => false,
                'message' => ''
            ));
        }
    }

    /* Delete task from database */
    public function delete_task($id)
    {
        if (!has_permission('tasks', '', 'delete', true)) {
            access_denied('tasks');
        }
        $success = $this->tasks_model->delete_task($id);
        $message = _l('problem_deleting', _l('task_lowercase'));
        if ($success) {
            $message = _l('deleted', _l('task'));
            set_alert('success', $message);
        } else {
            set_alert('warning', $message);
        }

        // if (strpos($_SERVER['HTTP_REFERER'], 'tasks/index') !== false || strpos($_SERVER['HTTP_REFERER'], 'tasks/view') !== false) {
        //     redirect(admin_url('tasks'));
        // } elseif (preg_match("/projects\/view\/[1-9]+/", $_SERVER['HTTP_REFERER'])) {
        //     $project_url = explode('?', $_SERVER['HTTP_REFERER']);
        //     redirect($project_url[0].'?group=project_tasks');
        // } else {
        //     redirect($_SERVER['HTTP_REFERER']);
        // }
    }

    /**
     * Remove task attachment
     * @since  Version 1.0.1
     * @param  mixed $id attachment it
     * @return json
     */
    public function remove_task_attachment($id)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode(array(
                'success' => $this->tasks_model->remove_task_attachment($id)
            ));
        }
    }

    /**
     * Upload task attachment
     * @since  Version 1.0.1
     */
    public function upload_file()
    {
        if ($this->input->post()) {
            $taskid = $this->input->post('taskid');
            $file = handle_tasks_attachments($taskid);
            if ($file) {
                $files = array();
                $files[] = $file;
                $success = $this->tasks_model->add_attachment_to_database($taskid, $file);
                set_alert('success', _l('updated_successfully', _l('task')));
                redirect(admin_url('tasks/dashboard/' . $taskid));
            }
        }
    }

    public function timer_tracking()
    {
        echo json_encode(array(
            'success' => $this->tasks_model->timer_tracking($this->input->post('task_id'), $this->input->post('timer_id'), nl2br($this->input->post('note')))
        ));
    }

    public function delete_timesheet($id)
    {
        if (has_permission('tasks', '', 'delete', true) || has_permission('projects', '', 'delete', true)) {
            $alert_type = 'warning';
            $success = $this->tasks_model->delete_timesheet($id);
            if ($success) {
                $message = _l('deleted', _l('project_timesheet'));
                set_alert('success', $message);
            }
            if (!$this->input->is_ajax_request()) {
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function log_time()
    {
        $success = $this->tasks_model->timesheet($this->input->post());
        if ($success === true) {
            $this->session->set_flashdata('task_single_timesheets_open', true);
            $message = _l('added_successfully', _l('project_timesheet'));
        } elseif (is_array($success) && isset($success['end_time_smaller'])) {
            $message = _l('failed_to_add_project_timesheet_end_time_smaller');
        } else {
            $message = _l('project_timesheet_not_updated');
        }

        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
        die;
    }

    public function update_tags()
    {
        if (has_permission('tasks', '', 'create', true) || has_permission('tasks', '', 'edit', true)) {
            handle_tags_save($this->input->post('tags'), $this->input->post('task_id'), 'task');
        }
    }

    public function bulk_action()
    {
        do_action('before_do_bulk_action_for_tasks');
        $total_deleted = 0;
        if ($this->input->post()) {

            $status = $this->input->post('status');
            $ids = $this->input->post('ids');
            $tags = $this->input->post('tags');
            $assignees = $this->input->post('assignees');
            $milestone = $this->input->post('milestone');
            $priority = $this->input->post('priority');

            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if (has_permission('tasks', '', 'delete', true)) {
                            if ($this->tasks_model->delete_task($id)) {
                                $total_deleted++;
                            }
                        }
                    } else {
                        if ($status) {
                            $this->tasks_model->mark_as($status, $id);
                        }
                        if ($priority || $milestone) {
                            $update = array();
                            if ($priority) {
                                $update['priority'] = $priority;
                            }
                            if ($milestone) {
                                $update['milestone'] = $milestone;
                            }
                            $this->db->where('id', $id);
                            $this->db->update('tblstafftasks', $update);
                        }
                        if ($tags) {
                            handle_tags_save($tags, $id, 'task');
                        }
                        if ($assignees) {
                            $notifiedUsers = array();
                            foreach ($assignees as $user_id) {
                                if (!$this->tasks_model->is_task_assignee($user_id, $id)) {
                                    $this->db->select('rel_type,rel_id');
                                    $this->db->where('id', $id);
                                    $task = $this->db->get('tblstafftasks')->row();
                                    if ($task->rel_type == 'project') {
                                        // User is we are trying to assign the task is not project member
                                        if (total_rows('tblprojectmembers', array('project_id' => $task->rel_id, 'staff_id' => $user_id)) == 0) {
                                            $this->db->insert('tblprojectmembers', array('project_id' => $task->rel_id, 'staff_id' => $user_id));
                                        }
                                    }
                                    $this->db->insert('tblstafftaskassignees', array(
                                        'staffid' => $user_id,
                                        'taskid' => $id,
                                        'assigned_from' => get_staff_user_id()
                                    ));
                                    if ($user_id != get_staff_user_id()) {

                                        $notification_data = array(
                                            'description' => 'not_task_assigned_to_you',
                                            'touserid' => $user_id,
                                            'link' => admin_url('tasks/dashboard/' . $id)
                                        );

                                        $this->db->select('name');
                                        $this->db->where('id', $id);
                                        $task_name = $this->db->get('tblstafftasks')->row()->name;
                                        $notification_data['additional_data'] = serialize(array(
                                            $task_name
                                        ));
                                        if (add_notification($notification_data)) {
                                            array_push($notifiedUsers, $user_id);
                                        }
                                    }
                                }
                            }
                            pusher_trigger_notification($notifiedUsers);
                        }
                    }
                }
            }
            if ($this->input->post('mass_delete')) {
                set_alert('success', _l('total_tasks_deleted', $total_deleted));
            }
        }
    }

    //Added By Avni on 11/10/2017
    public function statuschange()
    {
        $status_id = $_POST['status_id'];
        $task_id = $_POST['task_id'];
        $statusdata = $this->tasks_model->statuschange($task_id, $status_id);
        echo "success";
        exit;
    }

    public function taskoverviewupdate()
    {
        $data = array();
        $data['statuses'] = $this->tasks_model->get_status();
        $response = $this->load->view('admin/tasks/taskoverviewupdate', $data, TRUE);
        echo $response;
        exit;
    }

    public function dashboard($id = '')
    {
        $pg = $this->input->get('pg');
        $taskid = $id;

        $tasks_where = array();

        if (!has_permission('tasks', '', 'view', true)) {
            $tasks_where = get_tasks_where_string(false);
        }

        $task = $this->tasks_model->get($taskid, $tasks_where);
        $data['checklistTemplates'] = $this->tasks_model->get_checklist_templates();
        $data['task'] = $task;
        $data['id'] = $task->id;
        $data['staff'] = $this->staff_model->get('', 1);
        $data['task_is_billed'] = $this->tasks_model->is_task_billed($taskid);
        $data['lid'] = $this->input->get('lid');
        $data['pid'] = $this->input->get('pid');
        $data['eid'] = $this->input->get('eid');
        $data['pg'] = $pg;
        if ($data['lid']) {
            $this->load->model('leads_model');
            $data['lname'] = '';
            if ($data['lid'] != "") {
                $data['lname'] = $this->leads_model->get($data['lid'])->name;
            }
        } elseif ($data['pid']) {
            $this->load->model('leads_model');
            $data['lname'] = '';
            if ($data['pid'] != "") {
                $data['lname'] = $this->projects_model->get($data['pid'])->name;
            }
        } elseif ($data['eid']) {
            $projectid = $this->input->get('eid');
            $parent_id = $this->projects_model->get($projectid)->parent;
            $this->load->model('projects_model');

            $data['eid'] = $projectid;
            $data['parent_id'] = $parent_id;
            $data['lname'] = '';
            $data['pname'] = '';
            if ($parent_id != "") {
                $data['pname'] = $this->projects_model->get($parent_id)->name;
            }
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        }

        $this->load->view('admin/tasks/dashboard', $data);
    }

    /* View leads statuses */
    public function statuses()
    {
        if (!has_permission('lists', '', 'view', true)) {
            access_denied('lists');
        }
        $data['statuses'] = $this->tasks_model->get_status();
        $data['title'] = 'Task Status';
        $this->load->view('admin/tasks/manage_statuses', $data);
    }

    /* Add or update tasks status */
    public function status()
    {
        /*if (!is_admin()) {
            access_denied('Tasks Statuses');
        }*/
        if (!has_permission('lists', '', 'view', true)) {
            access_denied('lists');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['brandid'] = get_user_session();
            if (!$this->input->post('id')) {
                if (!has_permission('lists', '', 'create', true)) {
                    access_denied('lists');
                }
                $success = $this->tasks_model->add_status($data);
                $message = '';
                if ($success == true) {
                    $message = _l('added_successfully', _l('task_status'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            } else {
                if (!has_permission('lists', '', 'edit', true)) {
                    access_denied('lists');
                }
                $data = $this->input->post();
                $id = $data['id'];
                unset($data['id']);
                $success = $this->tasks_model->update_status($data, $id);
                $message = '';

                if (is_array($success)) {
                    $success = false;
                    $message = _l('Unable to save task statuses');
                } elseif ($success == true) {
                    $message = _l('updated_successfully', _l('task_status'));
                }

                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            }
        }
    }

    /* Delete tasks status from databae */
    public function delete_status($id)
    {
        /*if (!is_admin()) {
            access_denied('Tasks Statuses');
        }*/

        if (!has_permission('lists', '', 'delete')) {
            access_denied('Delete Task Status');
        }
        if (!$id) {
            redirect(admin_url('tasks/statuses'));
        }
        $response = $this->tasks_model->delete_status($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('task_status_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('task_status')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('task_status_lowercase')));
        }
        //redirect(admin_url('tasks/statuses'));
    }

    //Added on 11/06/2017 By Purvi
    public function taskstatus_name_exists()
    {

        if ($this->input->post()) {
            $id = $this->input->post('id');

            $where = "";
            $where .= 'deleted=0 and brandid=' . get_user_session();

            if ($id != '') {
                $where .= ' and id=' . $id;
                $this->db->where($where);

                $_current_source = $this->db->get('tbltasksstatus')->row();
                if ($_current_source->name == $this->input->post('name')) {
                    echo json_encode(true);
                    die();
                }
            }
            $name = $this->input->post('name');
            $where .= ' and name="' . $name . '"';

            $this->db->where($where);

            $total_rows = $this->db->count_all_results('tbltasksstatus');
            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            exit;
        }
    }

    /**
     * Added By Purvi on 11-10-2017 For Pin/Unpin Task
     */
    public function pintask()
    {
        $task_id = $_POST['task_id'];

        $pindata = $this->tasks_model->pintask($task_id);

        echo $pindata;
        exit;
    }

    function progressmanaual($taskid)
    {
        $manaual = $this->tasks_model->progressmanaual($taskid, $this->input->post());
        echo $manaual;
        die;
    }
    function task_name_exists(){
        $name = $this->input->post('name');
        $result = $this->tasks_model->task_name_exists($name);
        echo $result;
        die();
    }
}