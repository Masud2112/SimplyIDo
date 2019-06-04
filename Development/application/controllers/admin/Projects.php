<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Projects extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('projects_model');
        $this->load->model('leads_model');
        $this->load->model('currencies_model');
        $this->load->helper('date');
        $this->load->model('venues_model');
        $this->load->model('invoices_model');
        $this->load->model('addressbooks_model');
    }

    //public function index($clientid = '')
    public function index($id = '')
    {
        $pg = $this->input->get('pg');

        if (!has_permission('projects', '', 'view')) {
            access_denied('Projects');
        }
        if ($this->input->is_ajax_request()) {
            if ($this->input->get('kanban')) {
                $data['statuses'] = $this->projects_model->get_project_status();
                echo $this->load->view('admin/projects/kan-ban', $data, true);
                die();
            } elseif ($this->input->get('table_projects')) {
                $this->perfex_base->get_table_data('projects');
            }
        }

        $data['switch_projects_kanban'] = true;

        if ($this->session->has_userdata('projects_kanban_view') && $this->session->userdata('projects_kanban_view') == 'true') {
            $data['switch_projects_kanban'] = false;
            $data['bodyclass'] = 'kan-ban-body';
        }

        /*if(is_mobile()){

            $this->session->set_userdata(array('projects_kanban_view' => 0));
        }*/

        $data['staff'] = $this->staff_model->get('', 1);

        $data['statuses'] = $this->projects_model->get_project_status();
        $data['sources'] = $this->leads_model->get_source();
        $data['eventtypes'] = $this->projects_model->get_event_type();
        $data['pg'] = $pg;
        $data['title'] = _l('projects');
        // in case accesed the url leads/index/ directly with id - used in search
        $data['projectid'] = $id;
        $this->load->view('admin/projects/manage', $data);

        // if ($this->input->is_ajax_request()) {
        //     $this->perfex_base->get_table_data('projects', array(
        //         'clientid' => $clientid
        //     ));
        // }
        // $data['statuses'] = $this->projects_model->get_project_statuses();
        // $data['title']    = _l('projects');
        // $this->load->view('admin/projects/manage', $data);
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/19/2017
     * for project kanban view */
    public function projects_kanban_load_more()
    {

        if (!is_staff_member()) {
            header("HTTP/1.0 404 Not Found");
            echo _l('access_denied');
            die;
        }

        $status = $this->input->get('status');
        $page = $this->input->get('page');

        $this->db->where('id', $status);
        $status = $this->db->get('tblprojectstatus')->row_array();

        $projects = $this->projects_model->do_project_kanban_query($status['id'], $this->input->get('search'), $page, array(
            'sort_by' => $this->input->get('sort_by'),
            'sort' => $this->input->get('sort')
        ));

        foreach ($projects as $project) {
            $this->load->view('admin/projects/_kan_ban_card', array(
                'project' => $project,
                'status' => $status
            ));
        }
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/18/2017
     * View project statuses */
    public function statuses()
    {
        /*if (!is_admin()) {
            access_denied('Leads Statuses');
        }*/
        if (!has_permission('lists', '', 'view', true)) {
            access_denied('lists');
        }
        $data['statuses'] = $this->projects_model->get_project_status();
        $data['title'] = 'Project Status';
        $this->load->view('admin/projects/manage_statuses', $data);
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/18/2017
     * check project status name exists or not
     */
    public function projectstatus_name_exists()
    {

        if ($this->input->post()) {
            $id = $this->input->post('id');

            $where = "";
            $where .= 'deleted = 0 and brandid = ' . get_user_session();

            if ($id != '') {
                $where .= ' and id=' . $id;
                $this->db->where($where);

                $_current_source = $this->db->get('tblprojectstatus')->row();
                if ($_current_source->name == $this->input->post('name')) {
                    echo json_encode(true);
                    die();
                }
            }
            $name = $this->input->post('name');
            $where .= ' and name = "' . $name . '"';

            $this->db->where($where);

            $total_rows = $this->db->count_all_results('tblprojectstatus');
            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            exit;
        }
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/18/2017
     * Add or update project status
     */
    public function status()
    {
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
                $success = $this->projects_model->add_status($data);
                $message = '';
                if ($success == true) {
                    $message = _l('added_successfully', _l('project_status'));
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
                $success = $this->projects_model->update_status($data, $id);
                $message = '';

                if (is_array($success)) {
                    $success = false;
                    $message = _l('Unable to save project statuses');
                } elseif ($success == true) {
                    $message = _l('updated_successfully', _l('project_status'));
                }

                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            }
        }
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/18/2017
     * Delete project status from databae
     */
    public function delete_status($id)
    {
        if (!has_permission('lists', '', 'delete', true)) {
            access_denied('Delete Project Status');
        }
        if (!$id) {
            redirect(admin_url('projects/statuses'));
        }
        $response = $this->projects_model->delete_status($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('project_status_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('project_status')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('project_status_lowercase')));
        }
        //redirect(admin_url('leads/statuses'));
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/22/2017
     * Used in canban when dragging
     */
    public function update_kan_ban_project_status()
    {
        if ($this->input->post()) {
            if ($this->input->is_ajax_request()) {
                $this->projects_model->update_project_status($this->input->post());
            }
        }
    }

    public function staff_projects()
    {
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('staff_projects');
        }
    }

    public function expenses($id)
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('expenses_model');
            $this->perfex_base->get_table_data('project_expenses', array(
                'project_id' => $id
            ));
        }
    }

    public function add_expense()
    {
        if ($this->input->post()) {
            $this->load->model('expenses_model');
            $id = $this->expenses_model->add($this->input->post());
            if ($id) {
                set_alert('success', _l('added_successfully', _l('expense')));
                echo json_encode(array(
                    'url' => admin_url('projects/view/' . $this->input->post('project_id') . '/?group=project_expenses'),
                    'expenseid' => $id
                ));
                die;
            }
            echo json_encode(array(
                'url' => admin_url('projects/view/' . $this->input->post('project_id') . '/?group=project_expenses')
            ));
            die;
        }
    }

    // public function project($id = '')
    // {
    //     if (!has_permission('projects', '', 'edit') && !has_permission('projects', '', 'create')) {
    //         access_denied('Projects');
    //     }
    //     if ($this->input->post()) {
    //         $data                = $this->input->post();
    //         $data['description'] = $this->input->post('description', false);
    //         if ($id == '') {
    //             if (!has_permission('projects', '', 'create')) {
    //                 acccess_danied('Projects');
    //             }
    //             $id = $this->projects_model->add($data);
    //             if ($id) {
    //                 set_alert('success', _l('added_successfully', _l('project')));
    //                 redirect(admin_url('projects/view/' . $id));
    //             }
    //         } else {
    //             if (!has_permission('projects', '', 'edit')) {
    //                 acccess_danied('Projects');
    //             }
    //             $success = $this->projects_model->update($data, $id);
    //             if ($success) {
    //                 set_alert('success', _l('updated_successfully', _l('project')));
    //             }
    //             redirect(admin_url('projects/view/' . $id));
    //         }
    //     }
    //     if ($id == '') {
    //         $title                            = _l('add_new', _l('project_lowercase'));
    //         $data['auto_select_billing_type'] = $this->projects_model->get_most_used_billing_type();
    //     } else {
    //         $data['project']         = $this->projects_model->get($id);
    //         $data['project_members'] = $this->projects_model->get_project_members($id);
    //         $title                   = _l('edit', _l('project_lowercase'));
    //     }

    //     if ($this->input->get('customer_id')) {
    //         $data['customer_id']        = $this->input->get('customer_id');
    //     }

    //     /**
    //     * Added By : Vaidehi
    //     * Dt : 11/09/2016
    //     * to get number of brands created and can be created based on package of logged in user
    //     */
    //     $response = $this->get_module_creation_access('projects');

    //     $data['module_create_restriction']  = $response['module_create_restriction'];
    //     $data['module_active_entries']      = $response['module_active_entries'];
    //     $data['packagename']                = $response['packagename'];

    //     $data['last_project_settings'] = $this->projects_model->get_last_project_settings();
    //     $data['settings']              = $this->projects_model->get_settings();
    //     $data['statuses']              = $this->projects_model->get_project_statuses();
    //     $data['staff']                 = $this->staff_model->get('', 1);

    //     $data['title'] = $title;
    //     $this->load->view('admin/projects/project', $data);
    // }

    /**
     * Added By : Vaidehi
     * Dt : 12/18/2017
     * to display add project form
     */
    public function project($id = '')
    {
        $pg = $this->input->get('pg');

        if ($id != '') {
            $data['project'] = $this->projects_model->get($id);
            $data['title'] = _l('edit', _l('project')) . ' ' . $data['project']->name;
            $data['title'] = _l('edit', _l('project'));

        } else {
            $data['title'] = _l('add_new', _l('project'));
        }

        $session_data = get_session_data();

        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
        $package_type_id = $session_data['package_type_id'];

        $data['profile_allow'] = 0;

        if ($is_sido_admin == 1 || $is_admin == 1) {
            $data['profile_allow'] = 1;
        } elseif ($package_type_id == 2) {
            $data['profile_allow'] = 0;
        } elseif ($package_type_id == 3) {
            $data['profile_allow'] = 1;
        }

        $data['global_search_allow'] = 0;

        if ($is_sido_admin == 1 || $is_admin == 1) {
            $data['global_search_allow'] = 1;
        } elseif ($package_type_id == 1) {
            $data['global_search_allow'] = 0;
        } elseif ($package_type_id == 3 || $package_type_id == 2) {
            $data['global_search_allow'] = 1;
        }

        $data['members'] = $this->staff_model->get('', 1, array(
            'is_not_staff' => 0
        ));

        $data['socialsettings'] = $this->addressbooks_model->get_socialsettings();
        $data['email_phone_type'] = get_email_phone_type();
        $data['address_type'] = get_address_type();
        $data['clients'] = $this->addressbooks_model->get_my_existing_contacts();
        $data['eventtypes'] = $this->projects_model->get_event_type();
        $data['sources'] = $this->leads_model->get_source();
        $data['statuses'] = $this->projects_model->get_project_status();
        $data['tags'] = $this->tags_model->get();
        /**
         * Added By : Vaidehi
         * Dt : 12/28/2017
         * to get parent project, if sub project added and/or edited
         */
        if (!empty($this->input->get('parent_project'))) {
            $data['parent_project'] = $this->input->get('parent_project');
            $parent_project = $this->projects_model->get($data['parent_project']);
            $data['parent_project_name'] = $parent_project->name;
        } else {
            if ($id != '' && $data['project']->parent > 0) {
                $data['parent_project'] = $data['project']->parent;
                $parent_project = $this->projects_model->get($data['parent_project']);
                $data['parent_project_name'] = $parent_project->name;
            }
        }

        if ($id == '') {
            if (!has_permission('projects', '', 'create', true)) {
                access_denied('project');
            }

            if ($this->input->post()) {
                $data = $this->input->post();
                if (isset($data['imagebase64'])) {
                    unset($data['imagebase64']);
                }
                if (isset($data['bannerbase64'])) {
                    unset($data['bannerbase64']);
                }
                $pg = $this->input->post('pg');
                $new_parent_project = $this->input->post('parent');

                $id = $this->projects_model->add($data);

                if ($id) {
                    handle_project_cover_image_upload($id);
                    handle_project_profile_image_upload($id);
                    set_alert('success', _l('added_successfully', _l('project')));

                    if (isset($new_parent_project) && $new_parent_project > 0) {
                        redirect(admin_url('projects/dashboard/' . $new_parent_project));
                    } elseif (isset($pg) && $pg == "calendar") {
                        redirect(admin_url('calendar'));
                    } elseif (isset($pg) && $pg == "home") {
                        redirect(admin_url());
                    } else {
                        //redirect(admin_url('projects'));
                        redirect(admin_url('projects/dashboard/' . $id));
                    }
                } else {
                    set_alert('danger', _l('problem_lead_adding', _l('project')));
                    redirect(admin_url('projects/project/' . $id));
                }
            }
        } else {
            if (!has_permission('projects', '', 'edit', true)) {
                access_denied('projects');
                redirect(admin_url('projects'));
            }

            if ($this->input->post()) {
                $pg = $this->input->post('pg');
                $new_parent_project = $this->input->post('parent');
                $data = $this->input->post();
                if (isset($data['imagebase64'])) {
                    unset($data['imagebase64']);
                }
                if (isset($data['bannerbase64'])) {
                    unset($data['bannerbase64']);
                }
                $success = $this->projects_model->update($data, $id);
                if ($success) {
                    handle_project_cover_image_upload($id);
                    handle_project_profile_image_upload($id);
                    set_alert('success', _l('updated_successfully', _l('project')));

                    if (isset($new_parent_project) && $new_parent_project > 0) {
                        redirect(admin_url('projects/dashboard/' . $new_parent_project));
                    } elseif (isset($pg) && $pg != "") {
                        redirect(admin_url('calendar'));
                    } else {
                        //redirect(admin_url('projects'));
                        redirect(admin_url('projects/dashboard/' . $id));
                    }
                } else {
                    set_alert('danger', _l('problem_lead_updating', _l('project')));
                    redirect(admin_url('projects/project/' . $id));
                }
            }
        }

        /**
         * Added By : Vaidehi
         * Dt : 12/22/2017
         * to get number of brands created and can be created based on package of logged in user
         */
        $response = $this->get_module_creation_access('projects');

        $data['module_create_restriction'] = $response['module_create_restriction'];
        $data['module_active_entries'] = $response['module_active_entries'];
        $data['packagename'] = $response['packagename'];

        /**
         * Added By : Vaidehi
         * Dt : 02/21/2018
         * to get approved venues
         */
        $this->load->model('venues_model');
        $data['venues'] = $this->venues_model->get_approved_venues();

        $data['pg'] = $this->input->get('pg');
        $data['index'] = 0;
        $this->load->view('admin/projects/project', $data);
    }

    /**
     * Added By : vaidehi
     * Dt : 12/20/2017
     * Remove project profile image / ajax
     */
    public function remove_project_profile_image($id = '')
    {
        if (is_numeric($id) && (has_permission('projects', '', 'create', true) || has_permission('projects', '', 'edit', true))) {
            $project_id = $id;
        } else {
            $project_id = "";
        }

        if (file_exists(get_upload_path_by_type('project_profile_image') . $project_id)) {
            delete_dir(get_upload_path_by_type('project_profile_image') . $project_id);
        }

        $this->db->where('id', $project_id);
        $this->db->update('tblprojects', array(
            'project_profile_image' => null
        ));
        if ($this->input->is_ajax_request()) {
            return true;
        }
        redirect(admin_url('projects/project/' . $project_id));

    }

    /**
     * Added By : vaidehi
     * Dt : 12/20/2017
     * Remove project profile image / ajax
     */
    public function remove_project_cover_image($id = '')
    {
        if (is_numeric($id) && (has_permission('projects', '', 'create', true) || has_permission('projects', '', 'edit', true))) {
            $project_id = $id;
        } else {
            $project_id = "";
        }
        if (file_exists(get_upload_path_by_type('project_cover_image') . $project_id)) {
            delete_dir(get_upload_path_by_type('project_cover_image') . $project_id);
        }
        $this->db->where('id', $project_id);
        $this->db->update('tblprojects', array(
            'projectcoverimage' => null
        ));
        if ($this->input->is_ajax_request()) {
            return true;
        }
        redirect(admin_url('projects/project/' . $project_id));

    }

    /**
     * Added By : vaidehi
     * Dt : 12/20/2017
     * For Pin/Unpin projects
     */
    public function pinproject()
    {
        $project_id = $this->input->post('project_id');

        $pindata = $this->projects_model->pinproject($project_id);

        echo $pindata;
        exit;
    }

    /**
     * Added By : vaidehi
     * Dt : 12/20/2017
     * For project status change
     */
    public function statuschange()
    {
        $status_id = $this->input->post('status_id');
        $project_id = $this->input->post('project_id');
        $statusdata = $this->projects_model->statuschange($project_id, $status_id);

        echo "success";
        exit;
    }

    /**
     * Added By : vaidehi
     * Dt : 12/20/2017
     * for project overview update
     */
    public function projectoverviewupdate()
    {
        $data = array();
        $data['statuses'] = $this->projects_model->get_project_status();
        $response = $this->load->view('admin/projects/projectoverviewupdate', $data, TRUE);
        echo $response;
        exit;
    }

    /**
     * Added By : vaidehi
     * Dt : 12/20/2017
     * kanban view
     */
    public function switch_projects_kanban($set = 0)
    {
        if ($set == 1) {
            $set = 'true';
        } else {
            $set = 'false';
        }

        $this->session->set_userdata(array(
            'projects_kanban_view' => $set
        ));

        redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * Added By : Masud
     * Dt : 06/21/2017
     * Invites kanban view
     */
    public function switch_invites_kanban($set = 0)
    {
        if ($set == 1) {
            $set = 'true';
        } else {
            $set = 'false';
        }

        $this->session->set_userdata(array(
            'invites_kanban_view' => $set
        ));

        redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * Added By : vaidehi
     * Dt : 12/21/2017
     * get project sub event
     */
    public function getsubevents()
    {
        $project_id = $this->input->post('projectid');
        $data['events'] = $this->projects_model->get('', array('parent' => $project_id));

        $this->load->view('admin/projects/sub-events', $data);
    }

    public function view($id)
    {
        // fix when loading lightbox in js
        if ($id === 'lightbox.min.map') {
            die;
        }

        if ($this->projects_model->is_member($id) || has_permission('projects', '', 'view', true)) {
            $project = $this->projects_model->get($id);
            if (!$project) {
                blank_page(_l('project_not_found'));
            }
            $data['statuses'] = $this->projects_model->get_project_statuses();

            if (!$this->input->get('group')) {
                $view = 'project_overview';
            } else {
                $view = $this->input->get('group');
            }

            $this->load->model('payment_modes_model');
            $data['payment_modes'] = $this->payment_modes_model->get('', array(), true);

            $data['project'] = $project;
            $data['currency'] = $this->projects_model->get_currency($id);

            $data['project_total_logged_time'] = $this->projects_model->total_logged_time($id);

            $data['staff'] = $this->staff_model->get('', 1);
            $percent = $this->projects_model->calc_progress($id);

            if ($view == 'project_overview') {
                $data['members'] = $this->projects_model->get_project_members($id);
                $i = 0;
                foreach ($data['members'] as $member) {
                    $data['members'][$i]['total_logged_time'] = 0;
                    $member_timesheets = $this->tasks_model->get_unique_member_logged_task_ids($member['staff_id'], ' AND task_id IN (SELECT id FROM tblstafftasks WHERE rel_type="project" AND rel_id="' . $id . '")');

                    foreach ($member_timesheets as $member_task) {
                        $data['members'][$i]['total_logged_time'] += $this->tasks_model->calc_task_total_time($member_task->task_id, ' AND staff_id=' . $member['staff_id']);
                    }

                    $i++;
                }

                $data['project_total_days'] = round((human_to_unix($data['project']->deadline . ' 00:00') - human_to_unix($data['project']->start_date . ' 00:00')) / 3600 / 24);
                $data['project_days_left'] = $data['project_total_days'];
                $data['project_time_left_percent'] = 100;
                if ($data['project']->deadline) {
                    if (human_to_unix($data['project']->start_date . ' 00:00') < time() && human_to_unix($data['project']->deadline . ' 00:00') > time()) {
                        $data['project_days_left'] = round((human_to_unix($data['project']->deadline . ' 00:00') - time()) / 3600 / 24);
                        $data['project_time_left_percent'] = $data['project_days_left'] / $data['project_total_days'] * 100;
                    }
                    if (human_to_unix($data['project']->deadline . ' 00:00') < time()) {
                        $data['project_days_left'] = 0;
                        $data['project_time_left_percent'] = 0;
                    }
                }

                $__total_where_tasks = 'rel_type = "project" AND rel_id=' . $id;
                if (!has_permission('tasks', '', 'view', true)) {
                    $__total_where_tasks .= ' AND tblstafftasks.id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid = ' . get_staff_user_id() . ')';

                    if (get_option('show_all_tasks_for_project_member') == 1) {
                        $__total_where_tasks .= ' AND (rel_type="project" AND rel_id IN (SELECT project_id FROM tblprojectmembers WHERE staff_id=' . get_staff_user_id() . '))';
                    }
                }
                $where = ($__total_where_tasks == '' ? '' : $__total_where_tasks . ' AND ') . 'status != 5';

                $data['tasks_not_completed'] = total_rows('tblstafftasks', $where);
                $total_tasks = total_rows('tblstafftasks', $__total_where_tasks);
                $data['total_tasks'] = $total_tasks;

                $where = ($__total_where_tasks == '' ? '' : $__total_where_tasks . ' AND ') . 'status = 5 AND rel_type="project" AND rel_id="' . $id . '"';

                $data['tasks_completed'] = total_rows('tblstafftasks', $where);

                $data['tasks_not_completed_progress'] = ($total_tasks > 0 ? number_format(($data['tasks_completed'] * 100) / $total_tasks, 2) : 0);

                @$percent_circle = $percent / 100;
                $data['percent_circle'] = $percent_circle;


                $data['project_overview_chart'] = $this->projects_model->get_project_overview_weekly_chart_data($id, ($this->input->get('overview_chart') ? $this->input->get('overview_chart') : 'this_week'));
            } elseif ($view == 'project_invoices') {
                $this->load->model('invoices_model');

                $data['invoiceid'] = '';
                $data['status'] = '';
                $data['custom_view'] = '';

                $data['invoices_years'] = $this->invoices_model->get_invoices_years();
                $data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();
                $data['invoices_statuses'] = $this->invoices_model->get_statuses();
            } elseif ($view == 'project_gantt') {
                $gantt_type = (!$this->input->get('gantt_type') ? 'milestones' : $this->input->get('gantt_type'));
                $taskStatus = (!$this->input->get('gantt_task_status') ? null : $this->input->get('gantt_task_status'));
                $data['gantt_data'] = $this->projects_model->get_gantt_data($id, $gantt_type, $taskStatus);
            } elseif ($view == 'project_milestones') {
                $data['milestones_exclude_completed_tasks'] = (!do_action('default_milestones_exclude_completed_tasks', $this->input->get('exclude_completed')) || ($this->input->get('exclude_completed') && $this->input->get('exclude_completed') == 'yes'));
            } elseif ($view == 'project_files') {
                $data['files'] = $this->projects_model->get_files($id);
            } elseif ($view == 'project_expenses') {
                $this->load->model('taxes_model');
                $this->load->model('expenses_model');
                $data['taxes'] = $this->taxes_model->get();
                $data['expense_categories'] = $this->expenses_model->get_category();
                $data['currencies'] = $this->currencies_model->get();
            } elseif ($view == 'project_activity') {
                $data['activity'] = $this->projects_model->get_activity($id);
            } elseif ($view == 'project_notes') {
                $data['staff_notes'] = $this->projects_model->get_staff_notes($id);
            } elseif ($view == 'project_estimates') {
                $this->load->model('estimates_model');
                $data['estimates_years'] = $this->estimates_model->get_estimates_years();
                $data['estimates_sale_agents'] = $this->estimates_model->get_sale_agents();
                $data['estimate_statuses'] = $this->estimates_model->get_statuses();
                $data['estimateid'] = '';
                $data['switch_pipeline'] = '';
            } elseif ($view == 'project_tickets') {
                $data['chosen_ticket_status'] = '';
                $this->load->model('tickets_model');
                $data['ticket_assignees'] = $this->tickets_model->get_tickets_assignes_disctinct();

                $this->load->model('departments_model');
                $data['staff_deparments_ids'] = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                $data['default_tickets_list_statuses'] = do_action('default_tickets_list_statuses', array(1, 2, 4));
            } elseif ($view == 'project_timesheets') {
                $data['tasks'] = $this->projects_model->get_tasks($id);
                $data['timesheets_staff_ids'] = $this->projects_model->get_distinct_tasks_timesheets_staff($id);
            }

            // Discussions
            if ($this->input->get('discussion_id')) {
                $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
                $data['discussion'] = $this->projects_model->get_discussion($this->input->get('discussion_id'), $id);
                $data['current_user_is_admin'] = is_admin();
            }

            $data['percent'] = $percent;

            $data['projects_assets'] = true;
            $data['circle_progress_asset'] = true;
            $data['accounting_assets'] = true;

            $other_projects = array();
            $other_projects_where = 'id !=' . $id . ' and status = 2';

            if (!has_permission('projects', '', 'view', true)) {
                $other_projects_where .= ' AND tblprojects.id IN (SELECT project_id FROM tblprojectmembers WHERE staff_id=' . get_staff_user_id() . ')';
            }

            $data['other_projects'] = $this->projects_model->get('', '', $other_projects_where);
            //$data['title']       = $data['project']->name;
            $data['title'] = _l('project');
            $data['bodyclass'] = 'project invoices_total_manual estimates_total_manual';
            $data['project_status'] = get_project_status_by_id($project->status);

            $hook_data = do_action('project_group_access_admin', array(
                'id' => $project->id,
                'view' => $view,
                'all_data' => $data
            ));

            $data = $hook_data['all_data'];
            $view = $hook_data['view'];

            // Unable to load the requested file: admin/projects/project_tasks#.php - FIX
            if (strpos($view, '#') !== false) {
                $view = str_replace('#', '', $view);
            }

            $view = trim($view);
            $data['group_view'] = $this->load->view('admin/projects/' . $view, $data, true);

            $this->load->view('admin/projects/view', $data);
        } else {
            access_denied('Project View');
        }
    }

    public function mark_as()
    {
        $success = false;
        $message = '';
        if ($this->input->is_ajax_request()) {
            if (has_permission('projects', '', 'create', true) || has_permission('projects', '', 'edit', true)) {
                $status = get_project_status_by_id($this->input->post('status_id'));

                $message = _l('project_marked_as_failed', $status['name']);
                $success = $this->projects_model->mark_as($this->input->post());

                if ($success) {
                    $message = _l('project_marked_as_success', $status['name']);
                }
            }
        }
        echo json_encode(array(
            'success' => $success,
            'message' => $message
        ));
    }

    public function file($id, $project_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin'] = is_admin();

        $data['file'] = $this->projects_model->get_file($id, $project_id);
        if (!$data['file']) {
            header("HTTP/1.0 404 Not Found");
            die;
        }
        $this->load->view('admin/projects/_file', $data);
    }

    public function update_file_data()
    {
        if ($this->input->post()) {
            $this->projects_model->update_file_data($this->input->post());
        }
    }

    public function add_external_file()
    {
        if ($this->input->post()) {
            $data = array();
            $data['project_id'] = $this->input->post('project_id');
            $data['files'] = $this->input->post('files');
            $data['external'] = $this->input->post('external');
            $data['visible_to_customer'] = ($this->input->post('visible_to_customer') == 'true' ? 1 : 0);
            $data['staffid'] = get_staff_user_id();
            $this->projects_model->add_external_file($data);
        }
    }

    public function export_project_data($id)
    {
        if (has_permission('projects', '', 'create', true)) {
            $project = $this->projects_model->get($id);
            $this->load->library('pdf');
            $members = $this->projects_model->get_project_members($id);
            $project->currency_data = $this->projects_model->get_currency($id);

            // Add <br /> tag and wrap over div element every image to prevent overlaping over text
            $project->description = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<br><br><div>$1</div><br><br>', $project->description);

            $data['project'] = $project;
            $data['milestones'] = $this->projects_model->get_milestones($id);
            $data['timesheets'] = $this->projects_model->get_timesheets($id);

            $data['tasks'] = $this->projects_model->get_tasks($id, array(), false);
            $data['total_logged_time'] = seconds_to_time_format($this->projects_model->total_logged_time($project->id));
            if ($project->deadline) {
                $data['total_days'] = round((human_to_unix($project->deadline . ' 00:00') - human_to_unix($project->start_date . ' 00:00')) / 3600 / 24);
            } else {
                $data['total_days'] = '/';
            }
            $data['total_members'] = count($members);
            $data['total_tickets'] = total_rows('tbltickets', array(
                'project_id' => $id
            ));
            $data['total_invoices'] = total_rows('tblinvoices', array(
                'project_id' => $id
            ));

            $this->load->model('invoices_model');

            $data['invoices_total_data'] = $this->invoices_model->get_invoices_total(array(
                'currency' => $project->currency_data->id,
                'project_id' => $project->id
            ));

            $data['total_milestones'] = count($data['milestones']);
            $data['total_files_attached'] = total_rows('tblprojectfiles', array(
                'project_id' => $project->id
            ));
            $data['total_discussion'] = total_rows('tblprojectdiscussions', array(
                'project_id' => $project->id
            ));
            $data['members'] = $members;
            $this->load->view('admin/projects/export_data_pdf', $data);
        }
    }

    public function update_task_milestone()
    {
        if ($this->input->post()) {
            $this->projects_model->update_task_milestone($this->input->post());
        }
    }

    public function pin_action($project_id)
    {
        $this->projects_model->pin_action($project_id);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function add_edit_members($project_id)
    {
        if (has_permission('projects', '', 'edit', true) || has_permission('projects', '', 'create', true)) {
            $this->projects_model->add_edit_members($this->input->post(), $project_id);
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function discussions($project_id)
    {
        if ($this->projects_model->is_member($project_id) || has_permission('projects', '', 'view', true)) {
            if ($this->input->is_ajax_request()) {
                $this->perfex_base->get_table_data('project_discussions', array(
                    'project_id' => $project_id
                ));
            }
        }
    }

    public function discussion($id = '')
    {
        if ($this->input->post()) {
            $message = '';
            $success = false;
            if (!$this->input->post('id')) {
                $id = $this->projects_model->add_discussion($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('project_discussion'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            } else {
                $data = $this->input->post();
                $id = $data['id'];
                unset($data['id']);
                $success = $this->projects_model->edit_discussion($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('project_discussion'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            }
            die;
        }
    }

    public function get_discussion_comments($id, $type)
    {
        echo json_encode($this->projects_model->get_discussion_comments($id, $type));
    }

    public function add_discussion_comment($discussion_id, $type)
    {
        echo json_encode($this->projects_model->add_discussion_comment($this->input->post(null, false), $discussion_id, $type));
    }

    public function update_discussion_comment()
    {
        echo json_encode($this->projects_model->update_discussion_comment($this->input->post()));
    }

    public function delete_discussion_comment($id)
    {
        echo json_encode($this->projects_model->delete_discussion_comment($id));
    }

    public function delete_discussion($id)
    {
        $success = false;
        if (has_permission('projects', '', 'delete', true)) {
            $success = $this->projects_model->delete_discussion($id);
        }
        $alert_type = 'warning';
        $message = _l('project_discussion_failed_to_delete');
        if ($success) {
            $alert_type = 'success';
            $message = _l('project_discussion_deleted');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));
    }

    public function change_milestone_color()
    {
        if ($this->input->post()) {
            $this->projects_model->update_milestone_color($this->input->post());
        }
    }

    // public function upload_file($project_id)
    // {
    //     handle_project_file_uploads($project_id);
    // }

    public function change_file_visibility($id, $visible)
    {
        if ($this->input->is_ajax_request()) {
            $this->projects_model->change_file_visibility($id, $visible);
        }
    }

    public function change_activity_visibility($id, $visible)
    {
        if (has_permission('projects', '', 'create', true)) {
            if ($this->input->is_ajax_request()) {
                $this->projects_model->change_activity_visibility($id, $visible);
            }
        }
    }

    public function remove_file($project_id, $id)
    {
        $this->projects_model->remove_file($id);
        redirect(admin_url('projects/view/' . $project_id . '?group=project_files'));
    }

    public function milestones($project_id)
    {
        if ($this->projects_model->is_member($project_id) || has_permission('projects', '', 'view', true)) {
            if ($this->input->is_ajax_request()) {
                $this->perfex_base->get_table_data('milestones', array(
                    'project_id' => $project_id
                ));
            }
        }
    }

    public function milestone($id = '')
    {
        if ($this->input->post()) {
            $message = '';
            $success = false;
            if (!$this->input->post('id')) {
                $id = $this->projects_model->add_milestone($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('project_milestone')));
                }
            } else {
                $data = $this->input->post();
                $id = $data['id'];
                unset($data['id']);
                $success = $this->projects_model->update_milestone($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('project_milestone')));
                }
            }
        }

        redirect(admin_url('projects/view/' . $this->input->post('project_id') . '?group=project_milestones'));
    }

    public function delete_milestone($project_id, $id)
    {
        if (has_permission('projects', '', 'delete', true)) {
            if ($this->projects_model->delete_milestone($id)) {
                set_alert('deleted', 'project_milestone');
            }
        }
        redirect(admin_url('projects/view/' . $project_id . '?group=project_milestones'));
    }

    public function bulk_action_files()
    {
        do_action('before_do_bulk_action_for_project_files');
        $total_deleted = 0;
        $hasPermissionDelete = has_permission('projects', '', 'delete', true);
        // bulk action for projects currently only have delete button
        if ($this->input->post()) {
            $fVisibility = $this->input->post('visible_to_customer') == 'true' ? 1 : 0;
            $ids = $this->input->post('ids');
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($hasPermissionDelete && $this->input->post('mass_delete') && $this->projects_model->remove_file($id)) {
                        $total_deleted++;
                    } else {
                        $this->projects_model->change_file_visibility($id, $fVisibility);
                    }
                }
            }
        }
        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_files_deleted', $total_deleted));
        }
    }

    public function timesheets($project_id)
    {
        if ($this->projects_model->is_member($project_id) || has_permission('projects', '', 'view', true)) {
            if ($this->input->is_ajax_request()) {
                $this->perfex_base->get_table_data('timesheets', array(
                    'project_id' => $project_id
                ));
            }
        }
    }

    public function timesheet()
    {
        if ($this->input->post()) {
            $message = '';
            $success = false;
            $success = $this->tasks_model->timesheet($this->input->post());
            if ($success === true) {
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
    }

    public function timesheet_task_assignees($task_id, $project_id, $staff_id = 'undefined')
    {
        $assignees = $this->tasks_model->get_task_assignees($task_id);
        $data = '';
        $has_permission_edit = has_permission('projects', '', 'edit', true);
        $has_permission_create = has_permission('projects', '', 'edit', true);
        // The second condition if staff member edit their own timesheet
        if ($staff_id == 'undefined' || $staff_id != 'undefined' && (!$has_permission_edit || !$has_permission_create)) {
            $staff_id = get_staff_user_id();
            $current_user = true;
        }
        foreach ($assignees as $staff) {
            $selected = '';
            // maybe is admin and not project member
            if ($staff['assigneeid'] == $staff_id && $this->projects_model->is_member($project_id, $staff_id)) {
                $selected = ' selected';
            }
            if ((!$has_permission_edit || !$has_permission_create) && isset($current_user)) {
                if ($staff['assigneeid'] != $staff_id) {
                    continue;
                }
            }
            $data .= '<option value="' . $staff['assigneeid'] . '"' . $selected . '>' . get_staff_full_name($staff['assigneeid']) . '</option>';
        }
        echo $data;
    }

    public function remove_team_member($project_id, $staff_id)
    {
        if (has_permission('projects', '', 'edit', true) || has_permission('projects', '', 'create', true)) {
            if ($this->projects_model->remove_team_member($project_id, $staff_id)) {
                set_alert('success', _l('project_member_removed'));
            }
        }
        redirect(admin_url('projects/view/' . $project_id));
    }

    public function save_note($project_id)
    {
        if ($this->input->post()) {
            $success = $this->projects_model->save_note($this->input->post(null, false), $project_id);
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('project_note')));
            }
            redirect(admin_url('projects/view/' . $project_id . '?group=project_notes'));
        }
    }

    public function delete($project_id)
    {
        if (has_permission('projects', '', 'delete', true)) {
            $project = $this->projects_model->get($project_id);
            $success = $this->projects_model->delete($project_id);
            if ($success) {
                set_alert('success', _l('deleted', _l('project')));
                //redirect(admin_url('projects'));
            } else {
                set_alert('warning', _l('problem_deleting', _l('project_lowercase')));
                //redirect(admin_url('projects/view/' . $project_id));
            }
        }
    }

    public function copy($project_id)
    {
        if (has_permission('projects', '', 'create', true)) {
            $id = $this->projects_model->copy($project_id, $this->input->post());
            if ($id) {
                set_alert('success', _l('project_copied_successfully'));
                redirect(admin_url('projects/view/' . $id));
            } else {
                set_alert('danger', _l('failed_to_copy_project'));
                redirect(admin_url('projects/view/' . $project_id));
            }
        }
    }

    public function mass_stop_timers($project_id, $billable = 'false')
    {
        if (has_permission('invoices', '', 'create', true)) {
            $where = array(
                'billed' => 0,
                'startdate <=' => date('Y-m-d')
            );
            if ($billable == 'true') {
                $where['billable'] = true;
            }
            $tasks = $this->projects_model->get_tasks($project_id, $where);
            $total_timers_stopped = 0;
            foreach ($tasks as $task) {
                $this->db->where('task_id', $task['id']);
                $this->db->where('end_time IS NULL');
                $this->db->update('tbltaskstimers', array(
                    'end_time' => time()
                ));
                $total_timers_stopped += $this->db->affected_rows();
            }
            $message = _l('project_tasks_total_timers_stopped', $total_timers_stopped);
            $type = 'success';
            if ($total_timers_stopped == 0) {
                $type = 'warning';
            }
            echo json_encode(array(
                'type' => $type,
                'message' => $message
            ));
        }
    }

    public function get_pre_invoice_project_info($project_id)
    {
        if (has_permission('invoices', '', 'create', true)) {
            $data['billable_tasks'] = $this->projects_model->get_tasks($project_id, array(
                'billable' => 1,
                'billed' => 0,
                'startdate <=' => date('Y-m-d')
            ));

            $data['not_billable_tasks'] = $this->projects_model->get_tasks($project_id, array(
                'billable' => 1,
                'billed' => 0,
                'startdate >' => date('Y-m-d')
            ));

            $data['project_id'] = $project_id;
            $data['billing_type'] = get_project_billing_type($project_id);

            $this->load->model('expenses_model');
            $this->db->where('invoiceid IS NULL');
            $data['expenses'] = $this->expenses_model->get('', array(
                'project_id' => $project_id,
                'billable' => 1
            ));

            $this->load->view('admin/projects/project_pre_invoice_settings', $data);
        }
    }

    public function get_invoice_project_data()
    {
        if (has_permission('invoices', '', 'create', true)) {
            $type = $this->input->post('type');
            $project_id = $this->input->post('project_id');
            // Check for all cases
            if ($type == '') {
                $type == 'single_line';
            }
            $this->load->model('payment_modes_model');
            $data['payment_modes'] = $this->payment_modes_model->get('', array(
                'expenses_only !=' => 1
            ));
            $this->load->model('taxes_model');
            $data['taxes'] = $this->taxes_model->get();
            $data['currencies'] = $this->currencies_model->get();
            $data['base_currency'] = $this->currencies_model->get_base_currency();
            $this->load->model('invoice_items_model');

            $data['ajaxItems'] = false;
            if (total_rows('tblitems') <= ajax_on_total_items()) {
                $data['items'] = $this->invoice_items_model->get_grouped();
            } else {
                $data['items'] = array();
                $data['ajaxItems'] = true;
            }

            $data['items_groups'] = $this->invoice_items_model->get_groups();
            $data['staff'] = $this->staff_model->get('', 1);
            $project = $this->projects_model->get($project_id);
            $data['project'] = $project;
            $items = array();

            $project = $this->projects_model->get($project_id);
            $item['id'] = 0;

            $default_tax = unserialize(get_option('default_tax'));
            $item['taxname'] = $default_tax;

            $tasks = $this->input->post('tasks');
            if ($tasks) {
                $item['long_description'] = '';
                $item['qty'] = 0;
                $item['task_id'] = array();
                if ($type == 'single_line') {
                    $item['description'] = $project->name;
                    foreach ($tasks as $task_id) {
                        $task = $this->tasks_model->get($task_id);
                        $item['long_description'] .= $task->name . ' - ' . seconds_to_time_format($this->tasks_model->calc_task_total_time($task_id)) . ' ' . _l('hours') . "\r\n";
                        $item['task_id'][] = $task_id;
                        if ($project->billing_type == 2) {
                            $sec = $this->tasks_model->calc_task_total_time($task_id);
                            if ($sec < 60) {
                                $sec = 0;
                            }
                            $item['qty'] += sec2qty($sec);
                        }
                    }
                    if ($project->billing_type == 1) {
                        $item['qty'] = 1;
                        $item['rate'] = $project->project_cost;
                    } elseif ($project->billing_type == 2) {
                        $item['rate'] = $project->project_rate_per_hour;
                    }
                    $item['unit'] = '';
                    $items[] = $item;
                } elseif ($type == 'task_per_item') {
                    foreach ($tasks as $task_id) {
                        $task = $this->tasks_model->get($task_id);
                        $item['description'] = $project->name . ' - ' . $task->name;
                        $item['qty'] = floatVal(sec2qty($this->tasks_model->calc_task_total_time($task_id)));
                        $item['long_description'] = seconds_to_time_format($this->tasks_model->calc_task_total_time($task_id)) . ' ' . _l('hours');
                        if ($project->billing_type == 2) {
                            $item['rate'] = $project->project_rate_per_hour;
                        } elseif ($project->billing_type == 3) {
                            $item['rate'] = $task->hourly_rate;
                        }
                        $item['task_id'] = $task_id;
                        $item['unit'] = '';
                        $items[] = $item;
                    }
                } elseif ($type == 'timesheets_individualy') {
                    $timesheets = $this->projects_model->get_timesheets($project_id, $tasks);
                    $added_task_ids = array();
                    foreach ($timesheets as $timesheet) {
                        if ($timesheet['task_data']->billed == 0 && $timesheet['task_data']->billable == 1) {
                            $item['description'] = $project->name . ' - ' . $timesheet['task_data']->name;
                            if (!in_array($timesheet['task_id'], $added_task_ids)) {
                                $item['task_id'] = $timesheet['task_id'];
                            }

                            array_push($added_task_ids, $timesheet['task_id']);

                            $item['qty'] = floatVal(sec2qty($timesheet['total_spent']));
                            $item['long_description'] = _l('project_invoice_timesheet_start_time', _dt($timesheet['start_time'], true)) . "\r\n" . _l('project_invoice_timesheet_end_time', _dt($timesheet['end_time'], true)) . "\r\n" . _l('project_invoice_timesheet_total_logged_time', seconds_to_time_format($timesheet['total_spent'])) . ' ' . _l('hours');

                            if ($this->input->post('timesheets_include_notes') && $timesheet['note']) {
                                $item['long_description'] .= "\r\n\r\n" . _l('note') . ': ' . $timesheet['note'];
                            }

                            if ($project->billing_type == 2) {
                                $item['rate'] = $project->project_rate_per_hour;
                            } elseif ($project->billing_type == 3) {
                                $item['rate'] = $timesheet['task_data']->hourly_rate;
                            }
                            $item['unit'] = '';
                            $items[] = $item;
                        }
                    }
                }
            }
            if ($project->billing_type != 1) {
                $data['hours_quantity'] = true;
            }
            if ($this->input->post('expenses')) {
                if (isset($data['hours_quantity'])) {
                    unset($data['hours_quantity']);
                }
                if (count($tasks) > 0) {
                    $data['qty_hrs_quantity'] = true;
                }
                $expenses = $this->input->post('expenses');
                $this->load->model('expenses_model');
                foreach ($expenses as $expense_id) {
                    // reset item array
                    $item = array();
                    $item['id'] = 0;
                    $expense = $this->expenses_model->get($expense_id);
                    $item['expense_id'] = $expense->expenseid;
                    $item['description'] = _l('item_as_expense') . ' ' . $expense->name;
                    $item['long_description'] = $expense->description;
                    $item['qty'] = 1;
                    $item['taxname'] = array();
                    if ($expense->tax != 0) {
                        array_push($item['taxname'], $expense->tax_name . '|' . $expense->taxrate);
                    }
                    if ($expense->tax2 != 0) {
                        array_push($item['taxname'], $expense->tax_name2 . '|' . $expense->taxrate2);
                    }
                    $item['rate'] = $expense->amount;
                    $item['order'] = 1;
                    $item['unit'] = '';
                    $items[] = $item;
                }
            }
            $data['customer_id'] = $project->clientid;
            $data['invoice_from_project'] = true;
            $data['add_items'] = $items;
            $this->load->view('admin/projects/invoice_project', $data);
        }
    }

    public function get_rel_project_data($id, $task_id = '')
    {
        if ($this->input->is_ajax_request()) {
            $selected_milestone = '';
            if ($task_id != '' && $task_id != 'undefined') {
                $task = $this->tasks_model->get($task_id);
                $selected_milestone = $task->milestone;
            }

            $allow_to_view_tasks = 0;
            $this->db->where('project_id', $id);
            $this->db->where('name', 'view_tasks');
            $project_settings = $this->db->get('tblprojectsettings')->row();
            if ($project_settings) {
                $allow_to_view_tasks = $project_settings->value;
            }

            echo json_encode(array(
                'allow_to_view_tasks' => $allow_to_view_tasks,
                'billing_type' => get_project_billing_type($id),
                'milestones' => render_select('milestone', $this->projects_model->get_milestones($id), array(
                    'id',
                    'name'
                ), 'task_milestone', $selected_milestone)
            ));
        }
    }

    public function invoice_project($project_id)
    {
        if (has_permission('invoices', '', 'create', true)) {
            $this->load->model('invoices_model');
            $data = $this->input->post(null, false);
            $data['project_id'] = $project_id;
            $invoice_id = $this->invoices_model->add($data);
            if ($invoice_id) {
                $this->projects_model->log_activity($project_id, 'project_activity_invoiced_project', format_invoice_number($invoice_id));
                set_alert('success', _l('project_invoiced_successfully'));
            }
            redirect(admin_url('projects/view/' . $project_id . '?group=project_invoices'));
        }
    }

    public function view_project_as_client($id, $clientid)
    {
        if (is_admin()) {
            $this->clients_model->login_as_client($clientid);
            redirect(site_url('clients/project/' . $id));
        }
    }

    /* Added by Purvi on 12-20-2017 for Project dashboard */
    /**
     * Modified By : Vaidehi
     * Dt : 12/26/2017
     * for project dashboard
     */
    public function dashboard($id = '')
    {
        /*echo "<pre>";
        print_r($_SESSION);
        echo is_sido_admin();
        die('<--here');*/
        $pg = $this->input->get('pg');
        /*if (!is_staff_member()) {
            header("HTTP/1.0 404 Not Found");
            echo _l('access_denied');
            die;
        }*/
        if (!has_permission('projects', '', 'view', true)) {
            access_denied('project');
        }
        $brandid = $this->projects_model->get_project_details($id);
        $staffbrand = $this->projects_model->get_staff_brand(get_staff_user_id());
        if (!is_sido_admin()) {
            if (in_array($brandid, $staffbrand)) {
                $this->session->set_userdata('brand_id', $brandid);
            } else {
                header("HTTP/1.0 404 Not Found");
                echo _l('access_denied');
                die;
            }
        }
        if (is_numeric($id)) {
            $project = $this->projects_model->get($id);
            // if (!$project) {
            //     header("HTTP/1.0 404 Not Found");
            //     echo _l('project_not_found');
            //     die;
            // }
        }
        $project = $this->projects_model->getprojectdashboard($id);
        if ($project->venueid > 0) {
            $venue = $this->venues_model->get($project->venueid);
            $data['venue'] = $venue;
        }
        $invoices = $this->invoices_model->get_by_relid($id);
        $data['project'] = $project;
        $data['invoices'] = $invoices;
        $data['projectid'] = $id;
        $data['statuses'] = $this->projects_model->get_project_status();
        $data['pg'] = $pg;


        $this->load->view('admin/projects/dashboard', $data);
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/26/2017
     * for sub project dashboard
     */
    public function subdashboard()
    {
        $id = $this->input->post("projectid");
        if (!is_staff_member()) {
            header("HTTP/1.0 404 Not Found");
            echo _l('access_denied');
            die;
        }

        if (is_numeric($id)) {
            $project = $this->projects_model->get($id);
            if (!$project) {
                header("HTTP/1.0 404 Not Found");
                echo _l('project_not_found');
                die;
            }
        }

        $project = $this->projects_model->getsubprojectdashboard($id);
        $data['project'] = $project;
        $data['projectid'] = $id;
        $data['statuses'] = $this->projects_model->get_project_status();

        $this->load->view('admin/projects/sub-project-dashboard', $data);
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/01/2018
     * for invite vendor
     */
    public function invite($contacttype = '', $projectid = '')
    {
        if (!has_permission('invites', '', 'create', true)) {
            access_denied('invites');
        }
        $this->load->model('staff_model');
        $this->load->model('addressbooks_model');
        $this->load->model('tags_model');

        $project = $this->projects_model->get($projectid);
        $data['project'] = $project;
        $data['tags'] = $this->tags_model->get();
        $data['contacts'] = $this->addressbooks_model->get_existing_contacts('tblprojectcontact', "projectid", $projectid);
        $data['teammember'] = $this->staff_model->get('', 1);
        $data['clients'] = $this->projects_model->get_project_client($projectid);
        $data['clientConatct'] = $this->projects_model->get_project_client_contact($projectid);
        if ($contacttype == 3) {
            $data['title'] = _l('invite', 'Vendor');
        } else if ($contacttype == 4) {
            $data['title'] = _l('invite', 'Collaborator');
        } else if ($contacttype == 5) {
            $data['title'] = _l('invite', 'Venue');
        }
        $data['contacttype'] = $contacttype;
        if (!empty($project)) {
            if ($project->no_of_events == 0) {
                $p[] = objectToArray($this->projects_model->get($projectid));
                $data['events'] = $p;
            } else {
                $data['events'] = $this->projects_model->get('', array('parent' => $projectid));
            }
        }
        $data['permissions'] = $this->projects_model->get_project_permission($projectid);

        if ($this->input->post()) {
            $data = $this->input->post();
            $id = $this->projects_model->sendinvites($data);
            if ($id) {
                if (($data['contacttype'] == 4 && $data['invite'][0] == "new") || ($data['contacttype'] == 3 && $data['invite'][0] == "new")) {
                    $addressbookdata = array();
                    $addressbookdata['company'] = isset($data['company']) ? $data['company'] : 0;
                    $addressbookdata['ispublic'] = isset($data['ispublic']) ? $data['ispublic'] : 0;
                    $addressbookdata['companyname'] = isset($data['companyname']) ? $data['companyname'] : "";
                    $addressbookdata['companytitle'] = isset($data['companytitle']) ? $data['companytitle'] : "";
                    $addressbookdata['firstname'] = isset($data['firstname']) ? $data['firstname'] : "";
                    $addressbookdata['lastname'] = isset($data['lastname']) ? $data['lastname'] : "";
                    $addressbookdata['tags'] = isset($data['tags']) ? $data['tags'] : "";
                    $addressbookdata['email'] = isset($data['email']) ? array(array('type' => 'primary', 'email' => $data['email'])) : "";
                    $addressbookdata['phone'] = isset($data['phone']) ? array(array('type' => 'primary', 'phone' => $data['phone'], 'ext' => "")) : "";
                    $this->addressbooks_model->add($addressbookdata);
                }
                set_alert('success', _l('added_successfully', _l('invite')));
                redirect(admin_url('projects/dashboard/' . $projectid));
            } else {
                set_alert('danger', _l('problem_adding', _l('invite')));
                redirect(admin_url('projects/invite/' . $contacttype . '/' . $projectid));
            }
        }

        /**
         * Added By : Vaidehi
         * Dt : 02/21/2018
         * to get approved venues
         */
        $this->load->model('venues_model');
        $data['venues'] = $this->venues_model->get_approved_venues();

        $data['sitelocations'] = $this->venues_model->get_sitelocations($project->venueid);
        $data['invitedusers'] = $this->projects_model->get_invitedusers($projectid);
        $this->load->view('admin/projects/invite', $data);
    }

    /**
     * Added By : Vaidehi
     * Dt : 02/22/2018
     * to venue site locations
     */
    public function getsitelocations()
    {
        $venueid = $this->input->post('venuneid');
        $this->load->model('venues_model');
        $sitelocations = $this->venues_model->get_sitelocations($venueid);

        if (count($sitelocations) > 0) {
            echo json_encode($sitelocations);
            die();
        }
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/03/2018
     * get all invitees
     */
    public function invites()
    {
        if (!has_permission('invites', '', 'view', true)) {
            access_denied('invites');
        }

        $pg = $this->input->get('pg');
        $data['title'] = 'Invites';
        $data['pg'] = $pg;
        $data['switch_invites_kanban'] = true;

        if ($this->session->has_userdata('invites_kanban_view') && $this->session->userdata('invites_kanban_view') == 'true') {
            $data['switch_invites_kanban'] = false;
            $data['bodyclass'] = 'kan-ban-body';
        }

        /*if(is_mobile()){
            $this->session->set_userdata(array(
            'invites_kanban_view' => 0
            ));
        }*/
        $data['invitees_pending'] = $this->projects_model->get_invitees("", "", "", "", "", "", "", "pending");
        $data['invitees_approved'] = $this->projects_model->get_invitees("", "", "", "", "", "", "", "approved");
        $data['invitees_declined'] = $this->projects_model->get_invitees("", "", "", "", "", "", "", "declined");
        if ($this->input->is_ajax_request()) {
            if ($this->input->get('kanban')) {
                $data['kanban'] = $this->input->get('kanban');
                if ($this->input->get('limit')) {
                    $data['limit'] = $this->input->get('limit');
                }
                if ($this->input->get('page')) {
                    $data['page'] = $this->input->get('page');
                }
                if ($this->input->get('search')) {
                    $data['search'] = $this->input->get('search');
                }
                if ($this->input->get('status')) {
                    $data['status'] = $this->input->get('status');
                }
                $data['totalinvites'] = $this->projects_model->get_invitees("", "", "", "", "", "", $this->input->get('search'), $this->input->get('status'));
                $data['invitees'] = $this->projects_model->get_invitees("", "", "", $this->input->get('limit'), $this->input->get('page'), $this->input->get('kanban'), $this->input->get('search'), $this->input->get('status'));
                echo $this->load->view('admin/projects/invites/kan-ban', $data, true);
                die();
            }
        } else {
            $data['invitees'] = $this->projects_model->get_invitees();
            $this->load->view('admin/projects/get-invitees', $data);
        }

    }

    /**
     * Added By : Vaidehi
     * Dt : 01/09/2018
     * get detailed invite screen
     */
    public function invitedetails($id = '')
    {
        if (!has_permission('invites', '', 'edit', true)) {
            access_denied('invites');
        }
        $brandid = $this->projects_model->get_invite_details($id);
        $staffbrand = $this->projects_model->get_staff_brand(get_staff_user_id());
        if (in_array($brandid, $staffbrand)) {
            $this->session->set_userdata('brand_id', $brandid);
        } else {
            header("HTTP/1.0 404 Not Found");
            echo _l('access_denied');
            die;
        }
        $invite_details = $this->projects_model->get_invitees($id);
        $data['invite_details'] = $invite_details;
        $data['title'] = 'Invite Detail';
        $project = $this->projects_model->getprojectdashboard($invite_details->projectid);
        if ($project->venueid > 0) {
            $venue = $this->venues_model->get($project->venueid);
            $data['venue'] = $venue;
        }
        $this->load->view('admin/projects/invite_details', $data);
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/09/2018
     * to change invite status
     */
    public function invite_status_change()
    {
        //$data['projectid']      = $this->input->post('projectid');
        $data['inviteid'] = $this->input->post('inviteid');
        $data['status'] = $this->input->post('status');
        //$data['contacttype']    = $this->input->post('contacttype');
        /*if(is_staff_logged_in()){
            $data['userid']    = get_staff_user_id();
        }else{
            $data['userid']=0;
        }*/
        if (!empty($this->input->post('accept'))) {
            $data['accept'] = $this->input->post('accept');
        }

        if (!empty($this->input->post('comments'))) {
            $data['comments'] = $this->input->post('comments');
        }

        $res = $this->projects_model->update_invite_status($data);

        echo json_encode($res);
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/09/2018
     * to send invite email to vendor and/or account owner
     */
    public function send_invite()
    {
        $inviteid = $this->input->post('inviteid');

        $res = $this->projects_model->send_invite($inviteid);

        if ($res == 'Mail sent') {
            //_l('email_sent_successfully', _l('invite'))
            $msg = array('alert_type' => 'success', 'message' => _l('email_sent_successfully', _l('invite')));
        } else {
            //_l('email_sent_successfully', _l('problem_sending_email', _l('invite'))
            $msg = array('alert_type' => 'danger', 'message' => _l('problem_sending_email', _l('invite')));
        }

        echo json_encode($msg);
        exit();
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/09/2018
     * to resend invite email to vendor and/or account owner
     */
    public function resend_invite()
    {
        $inviteid = $this->input->post('inviteid');
        $type = $this->input->post('type');
        $usertype = $this->input->post('usertype');
        $userid = $this->input->post('userid');

        $res = $this->projects_model->resend_invite($inviteid, $type, $usertype, $userid);

        if ($res == 'Mail sent') {
            //_l('email_sent_successfully', _l('invite'))
            $msg = array('alert_type' => 'success', 'message' => _l('email_sent_successfully', _l('invite')));
        } else {
            //_l('email_sent_successfully', _l('problem_sending_email', _l('invite'))
            $msg = array('alert_type' => 'danger', 'message' => _l('problem_sending_email', _l('invite')));
        }

        echo json_encode($msg);
        exit();
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/19/2018
     * view invite permission
     */
    public function viewinvite()
    {
        $project_id = $this->input->post('projectid');
        $isparent = $this->input->post('isparent');
        $staffid = $this->input->post('staffid');
        $contactid = $this->input->post('addressbookid');
        $isvendor = $this->input->post('isvendor');
        $iscollaborator = $this->input->post('iscollaborator');
        $contacttype = $this->input->post('contacttype');
        $venueid = $this->input->post('venueid');

        if ($staffid > 0 || $contactid > 0) {
            if ($isparent == 1) {
                $data['events'] = $this->projects_model->get($project_id, array('parent' => $project_id), $staffid, $contactid, $isvendor, $iscollaborator);
            } else {
                $data['events'] = $this->projects_model->get($project_id, array(), $staffid, $contactid, $isvendor, $iscollaborator);
            }
        } else {
            $data['events'] = $this->projects_model->get($project_id, array(), '', '', '', '', $venueid);
        }
        $data['staffid'] = $staffid;
        $data['contactid'] = $contactid;
        $data['isvendor'] = $isvendor;
        $data['iscollaborator'] = $iscollaborator;
        $data['venueid'] = $venueid;
        $data['contacttype'] = $contacttype;

        $this->load->view('admin/projects/sub-events', $data);
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/18/2018
     * edit invite permission
     */
    public function editinvitepermission($projectid, $staffid, $contactid, $isvendor, $iscollaborator, $venueid)
    {

        $this->load->model('staff_model');
        $this->load->model('addressbooks_model');
        $this->load->model('tags_model');

        //for vendor
        if (isset($isvendor) && $isvendor == 1) {
            $contacttype = 3;
        }

        //for collaborator
        if (isset($iscollaborator) && $iscollaborator == 1) {
            $contacttype = 4;
        }

        //for venueid
        if (isset($venueid) && $venueid > 0) {
            $contacttype = 5;
        }

        $data['contacttype'] = $contacttype;

        if ((isset($isvendor) && $isvendor == 1) || (isset($iscollaborator) && $iscollaborator == 1)) {
            $data['invite_details'] = $this->projects_model->edit_invite_detail($projectid, $staffid, $contactid, $contacttype);
        }

        if (isset($venueid) && $venueid > 0) {
            $data['invite_details'] = $this->projects_model->edit_invite_detail($projectid, 0, 0, $contacttype, $venueid);
        }

        $data['title'] = 'Edit Invite Permission';

        $project = $this->projects_model->get($projectid);
        $data['project'] = $project;

        $data['tags'] = $this->tags_model->get();

        $data['permissions'] = $this->projects_model->get_project_permission($projectid);

        if ($this->input->post()) {
            $success = $this->projects_model->editinvitepermission($this->input->post());

            if ($success) {

                if ($project->parent === 0 || empty($project->parent)) {
                    $pid = $project->id;
                } else {
                    $pid = $project->parent;
                }

                set_alert('success', _l('permission_updated_successfully', _l('invite')));
                redirect(admin_url('projects/dashboard/' . $pid));
            } else {
                set_alert('danger', _l('problem_updating_permission', _l('invite')));
                redirect(admin_url('projects/editinvitepermission/' . $projectid . '/' . $staffid . '/' . $contactid . '/' . $isvendor . '/' . $iscollaborator));
            }
        }
        $this->load->view('admin/projects/edit_invite_permission', $data);
    }

    public function removevendor($projectid, $staffid, $contactid, $isvendor, $iscollaborator, $venueid)
    {
        $data['projectid'] = $projectid;
        $data['staffid'] = $staffid;
        $data['contactid'] = $contactid;
        $data['isvendor'] = $isvendor;
        $data['iscollaborator'] = $iscollaborator;
        $data['venueid'] = $venueid;

        $success = $this->projects_model->remove_vendor($data);
        if ($success) {
            if (isset($venueid) && $venueid > 0) {
                set_alert('success', _l('deleted', _l('Venue')));
            } elseif (isset($isvendor) && $isvendor > 0) {
                set_alert('success', _l('deleted', _l('Vendor')));
            } elseif (isset($iscollaborator) && $iscollaborator > 0) {
                set_alert('success', _l('deleted', _l('Collaborator')));
            }

        }
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/04/2018
     * check for unique email
     */
    public function emailexists()
    {
        $useremail = $this->input->post('useremail');
        $account_data = $this->projects_model->check_account_exists($useremail);

        if ($account_data) {
            echo 1;
            die();
        }

        echo 0;
        die();
    }

    // Added by Purvi on 12/20/2017
    public function upload_file($projectid)
    {
        handle_project_attachments($projectid);
    }

    public function upload_event_file($projectid)
    {
        handle_event_attachments($projectid);
    }

    public function remove_custom_file($projectid, $id)
    {
        $this->projects_model->remove_file($id);
        set_alert('success', _l('deleted', _l('media_file')));
        exit;
    }

    public function upload_exist_file()
    {
        $file_path = $_POST['file_path'];
        $projectid = $_POST['projectid'];
        handle_project_existing_attachments($projectid);
    }

    public function upload_exist_file_from_event()
    {
        $file_path = $_POST['file_path'];
        $projectid = $_POST['projectid'];
        handle_event_existing_attachments($projectid);
    }

    public function notes()
    {
        $eid = $this->input->get('eid');
        $pid = $this->input->get('pid');
        if (!is_staff_member()) {
            header("HTTP/1.0 404 Not Found");
            echo _l('access_denied');
            die;
        }

        if ($this->input->post() && $this->input->post('description') != "") {
            $data = $this->input->post();
            $postpid = $data['hdnpid'];
            $posteid = $data['hdneid'];
            if (isset($data['rel_type']) && $data['rel_type'] != "") {
                $rel_type = $data['rel_type'];
                $rel_id = $data[$data['rel_type']];
            } else {
                $rel_type = "";
                $rel_id = "";
            }
            unset($data['rel_id']);
            unset($data['project']);
            unset($data['event']);
            unset($data['rel_type']);
            unset($data['hdneid']);
            unset($data['hdnpid']);

            $note_id = $this->misc_model->add_note($data, $rel_type, $rel_id);
            if ($note_id) {

                $projectid = $rel_id;
                $message = 'Created note';

                set_alert('success', _l('added_successfully', _l('note')));
                if (isset($postpid) && $postpid != "") {
                    redirect(admin_url('projects/notes/' . "?pid=" . $postpid));
                } else if (isset($posteid) && $posteid != "") {
                    redirect(admin_url('projects/notes/' . "?eid=" . $posteid));
                } else {
                    redirect(admin_url('meetings/'));
                }
            } else {
                set_alert('danger', _l('problem_adding_project_note', _l('note')));
                if (isset($postpid) && $postpid != "") {
                    redirect(admin_url('projects/notes/' . "?pid=" . $postpid));
                } else if (isset($posteid) && $posteid != "") {
                    redirect(admin_url('projects/notes/' . "?eid=" . $posteid));
                } else {
                    redirect(admin_url('meetings/'));
                }
            }
        }

        if ($pid) {
            $project_details = $this->projects_model->get($pid);
            $data['projectid'] = $pid;
            $data['notes'] = $this->misc_model->get_notes($pid, 'project');
        } else {
            $project_details = $this->projects_model->get($eid);
            $data['projectid'] = $eid;
            $data['notes'] = $this->misc_model->get_notes($eid, 'event');
        }

        $data['timezone'] = $project_details->eventtimezone;
        $data['title'] = _l('project_notes');

        $data['projects'] = $this->tasks_model->get_projects();
        $data['events'] = $this->tasks_model->get_events($pid);
        $offsetobj = $this->misc_model->get_timezoneoffset();
        $offset = explode(':', $offsetobj->timezoneoffset);
        $data['timeoffset'] = $offset[0];
        if ($this->input->get('pid')) {
            $projectid = $this->input->get('pid');

            $this->load->model('projects_model');

            $data['pid'] = $projectid;
            $data['lname'] = '';
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        }
        if ($this->input->get('eid')) {
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
        $this->load->view('admin/projects/notes', $data);
    }

    public function delete_note($id, $projectid)
    {
        if (!is_staff_member()) {
            header("HTTP/1.0 404 Not Found");
            echo _l('access_denied');
            die;
        }

        /*echo json_encode(array(
            'success' => $this->misc_model->delete_note($id)
        ));*/
        $response = $this->misc_model->delete_note($id, $projectid);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('note')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('note')));
        } else {
            set_alert('danger', _l('problem_deleting', _l('note')));
        }
    }

    /**
     * Added By : Vaidehi
     * Dt : 03/21/2018
     * for Event Type
     */
    public function event_types()
    {
        if (!has_permission('lists', '', 'view')) {
            access_denied('Lists');
        }
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        //$is_admin = $session_data['is_admin'];
        if ($is_sido_admin == 0) {
            if (!has_permission('projects', '', 'view', true)) {
                access_denied('projects');
            }
        }
        $data['eventtypes'] = $this->projects_model->get_event_type();
        $data['title'] = _l('project_type_s');
        $this->load->view('admin/projects/manage_eventtypes', $data);
    }

    /**
     * Added By : Vaidehi
     * Dt : 03/21/2018
     * to check for event type exists
     */
    public function eventtype_name_exists()
    {
        if ($this->input->post()) {
            $id = $this->input->post('eventtypeid');

            $where = "";
            $where .= ' deleted = 0 AND brandid=' . get_user_session();

            if ($id != '') {
                $where .= ' AND eventtypeid = ' . $id;
                $this->db->where($where);

                $_current_source = $this->db->get('tbleventtype')->row();

                if ($_current_source->name == $this->input->post('eventtypename')) {
                    echo json_encode(true);
                    die();
                }
            }
            $name = $this->input->post('eventtypename');
            $where .= ' AND eventtypename = "' . $name . '"';
            $this->db->where($where);

            $total_rows = $this->db->count_all_results('tbleventtype');

            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            exit;
        }
    }

    /**
     * Added By : Vaidehi
     * Dt : 03/21/2018
     * Add or update event type
     */
    public function eventtype()
    {
        if (!has_permission('projects', '', 'view', true)) {
            access_denied('projects');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data['brandid'] = get_user_session();
            if (!$this->input->post('eventtypeid')) {
                if (!has_permission('projects', '', 'create', true)) {
                    access_denied('projects');
                }
                $success = $this->projects_model->add_eventtype($data);
                $message = '';
                if ($success == true) {
                    $message = _l('added_successfully', _l('eventtype'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            } else {
                if (!has_permission('projects', '', 'edit', true)) {
                    access_denied('projects');
                }
                $data = $this->input->post();
                $id = $data['eventtypeid'];
                unset($data['eventtypeid']);

                $success = $this->projects_model->update_eventtype($data, $id);
                $message = '';
                if (is_array($success)) {
                    $success = false;
                    $message = _l('Unable to save event type');
                } elseif ($success == true) {
                    $message = _l('updated_successfully', _l('eventtype'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            }
        }
    }

    /* Delete event type */
    public function delete_eventtype($id)
    {
        if (!has_permission('projects', '', 'delete', true)) {
            access_denied('Delete Event Type');
        }
        if (!$id) {
            redirect(admin_url('projects/event_types'));
        }
        $response = $this->projects_model->delete_eventtype($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('eventtype_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('eventtype')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('eventtype_lowercase')));
        }
    }

    /**
     * Added By: Vaidehi
     * Dt: 04/19/2018
     * to get project end date as start date plus one hour
     */
    public function getprojectendate()
    {
        $startdate = $this->input->post('startdate');

        $startdate = str_replace('/', '-', $startdate);
        if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $startdate)) {
            $startdate = ((isset($startdate) && !empty($startdate)) ? date("Y-m-d H:i:s", strtotime($startdate)) : "");
        } else {
            if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                $startdate = ((isset($startdate) && !empty($startdate)) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $startdate))) : "");
            } else {
                $startdate = str_replace('-', '/', $startdate);
                $startdate = ((isset($startdate) && !empty($startdate)) ? date("Y-m-d H:i:s", strtotime($startdate)) : "");
            }
        }
        //$convertedTime = date('Y-m-d H:i:s',strtotime('+1 hour',strtotime($startdate)));
        if (get_brand_option('time_format') == '12') {
            $convertedTime = date('m/d/Y g:i A', strtotime('+1 hour', strtotime($startdate)));
        } else {
            $convertedTime = date('m/d/Y G:i', strtotime('+1 hour', strtotime($startdate)));
        }
        echo _dt($convertedTime, true);
        //echo substr(_dt($convertedTime, true), 0, -3);
        die();
    }

    function addnewcontact()
    {
        $lid = $this->input->get('lid');
        $pid = $this->input->get('pid');
        $eid = $this->input->get('eid');

        $venueid = $this->input->get('venue');
        $locid = $this->input->get('locid');
        $vid = $this->input->get('vid');
        $data = array();
        $data['index'] = $_POST['index'];
        $data['selectedclients'] = isset($_POST['selectedclients']) ? $_POST['selectedclients'] : array();
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
        $package_type_id = $session_data['package_type_id'];

        $profile_allow = 0;

        if ($is_sido_admin == 1 || $is_admin == 1) {
            $profile_allow = 1;
        } elseif ($package_type_id == 2) {
            $profile_allow = 0;
        } elseif ($package_type_id == 3) {
            $profile_allow = 1;
        }

        $global_search_allow = 0;

        if ($is_sido_admin == 1 || $is_admin == 1) {
            $global_search_allow = 1;
        } elseif ($package_type_id == 1) {
            $global_search_allow = 0;
        } elseif ($package_type_id == 3 || $package_type_id == 2) {
            $global_search_allow = 1;
        }
        $data['roles'] = $this->roles_model->get();
        $data['tags'] = $this->tags_model->get();
        $data['global_search_allow'] = $global_search_allow;
        $data['profile_allow'] = $profile_allow;
        $data['socialsettings'] = $this->addressbooks_model->get_socialsettings();
        $data['email_phone_type'] = get_email_phone_type();
        $data['address_type'] = get_address_type();
        if ($this->input->get('lid')) {
            $data['lid'] = $this->input->get('lid');
            $this->load->model('leads_model');
            $data['lname'] = '';
            if ($data['lid'] != "") {
                $data['lname'] = $this->leads_model->get($data['lid'])->name;
            }
        } elseif ($this->input->get('pid')) {
            $projectid = $this->input->get('pid');
            if (isset($_GET['pid'])) {
                $data['parent_id'] = $this->projects_model->get($_GET['pid'])->parent;
            } else {
                $data['parent_id'] = 0;
            }
            $this->load->model('projects_model');

            $data['pid'] = $projectid;
            $data['lname'] = '';
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        } elseif (isset($venueid) && $venueid > 0) {
            $data['venueid'] = $venueid;
        } elseif (isset($locid) && $locid > 0) {
            $data['locid'] = $locid;
            $data['vid'] = $vid;
        }
        $data['leads'] = $this->addressbooks_model->get_leads();
        $data['projects'] = $this->addressbooks_model->get_projects();
        $data['events'] = $this->addressbooks_model->get_events($pid);
        $data['clients'] = $this->addressbooks_model->get_my_existing_contacts();
        $this->load->view('admin/projects/newform', $data);
    }

    function project_status_change()
    {
        $result = $this->projects_model->project_status_change($this->input->post());
        echo $result;
        die();
    }

    function reorderprojecttype()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post('eventtypes')) {
                $project_types = $this->input->post('eventtypes');
                foreach ($project_types as $project_type) {
                    $this->projects_model->reorderprojecttype($project_type);
                }

            }

        }
    }
}
