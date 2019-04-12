<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Staff extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /* List all staff members */
    public function index()
    {
        if (!has_permission('account_setup', '', 'view', true)) {
            access_denied('account_setup');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('staff');
        }
        $data['staff_members'] = $this->staff_model->get('');
        $data['title'] = _l('staff_members');
        $this->load->view('admin/staff/manage', $data);
    }

    /* Add new staff member or edit existing */
    public function member($id = '')
    {
        do_action('staff_member_edit_view_profile', $id);

        $this->load->model('departments_model');
        $this->load->model('home_model');
        if ($this->input->post()) {
            $data = $this->input->post();
            // Don't do XSS clean here.
            $data['email_signature'] = $this->input->post('email_signature', false);

            if ($id == '') {
                if (!has_permission('account_setup', '', 'create', true)) {
                    access_denied('account_setup');
                }
                $id = $this->staff_model->add($data);
                if ($id) {
                    if (isset($_POST['imagebase64'])) {
                        $data = $_POST['imagebase64'];

                        list($type, $data) = explode(';', $data);
                        list(, $data) = explode(',', $data);
                        $data = base64_decode($data);
                        $path = get_upload_path_by_type('staff') . $id . '/';
                        _maybe_create_upload_path($path);
                        $filename = unique_filename($path, $_FILES["profile_image"]["name"]);
                        $path .= '/round_' . $filename;
                        file_put_contents($path, $data);
                    }

                    handle_staff_profile_image_upload($id);
                    set_alert('success', _l('added_successfully', _l('staff_member')));
                    //redirect(admin_url('staff/member/' . $id));
                    redirect(admin_url('staff/'));
                }
            } else {
                if (!has_permission('account_setup', '', 'edit', true)) {
                    access_denied('account_setup');
                }
                $response = $this->staff_model->update($data, $id);
                if (isset($_POST['imagebase64'])) {
                    $data = $_POST['imagebase64'];

                    list($type, $data) = explode(';', $data);
                    list(, $data) = explode(',', $data);
                    $data = base64_decode($data);
                    $path = get_upload_path_by_type('staff') . $id . '/';
                    _maybe_create_upload_path($path);
                    $filename = unique_filename($path, $_FILES["profile_image"]["name"]);
                    $path .= '/round_' . $filename;
                    file_put_contents($path, $data);
                }

                handle_staff_profile_image_upload($id);

                if (is_array($response)) {
                    if (isset($response['cant_remove_main_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_main_admin'));
                    } elseif (isset($response['cant_remove_yourself_from_admin'])) {
                        set_alert('warning', _l('staff_cant_remove_yourself_from_admin'));
                    }
                    redirect(admin_url('staff/member/' . $id));
                } elseif ($response == true) {
                    set_alert('success', _l('updated_successfully', _l('staff_member')));
                    redirect(admin_url('staff/'));
                }
            }
        }

        if ($id == '') {
            $title = _l('add_new', _l('staff_member'));
        } else {
            $member = $this->staff_model->get($id);
            if (!$member) {
                blank_page('Staff Member Not Found', 'danger');
            }
            $data['member'] = $member;
            $title = $member->firstname . ' ' . $member->lastname;
            $data['staff_permissions'] = $this->roles_model->get_staff_permissions($id);
            $data['staff_departments'] = $this->departments_model->get_staff_departments($member->staffid);

            $ts_filter_data = array();
            if ($this->input->get('filter')) {
                if ($this->input->get('range') != 'period') {
                    $ts_filter_data[$this->input->get('range')] = true;
                } else {
                    $ts_filter_data['period-from'] = $this->input->get('period-from');
                    $ts_filter_data['period-to'] = $this->input->get('period-to');
                }
            } else {
                $ts_filter_data['this_month'] = true;
            }

            $data['logged_time'] = $this->staff_model->get_logged_time_data($id, $ts_filter_data);
            $data['timesheets'] = $data['logged_time']['timesheets'];
        }

        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $this->load->model('currencies_model');

        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['roles'] = $this->roles_model->get();
        $data['teams'] = $this->teams_model->get();
        $data['usertypes'] = $this->staff_model->getUserType();
        $data['permissions'] = $this->roles_model->get_permissions();
        $data['user_notes'] = $this->misc_model->get_notes($id, 'staff');
        $data['departments'] = $this->departments_model->get();
        $data['is_sido_admin'] = $is_sido_admin;
        $data['title'] = $title;
        $data['widget_data'] = $this->home_model->get_dashboard_data();

        /**
         * Added By : Vaidehi
         * Dt : 11/09/2017
         * to get number of brands created and can be created based on package of logged in user
         */
        $response = $this->get_module_creation_access('staff');

        $data['module_create_restriction'] = $response['module_create_restriction'];
        $data['module_active_entries'] = $response['module_active_entries'];
        $data['packagename'] = $response['packagename'];

        $this->load->view('admin/staff/member', $data);
    }

    public function change_language($lang = '')
    {
        $lang = do_action('before_staff_change_language', $lang);
        $this->db->where('staffid', get_staff_user_id());
        $this->db->update('tblstaff', array('default_language' => $lang));
        if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url());
        }
    }

    public function timesheets()
    {
        $data['view_all'] = false;
        if (is_admin() && $this->input->get('view') == 'all') {
            $data['staff_members_with_timesheets'] = $this->db->query('SELECT DISTINCT staff_id FROM tbltaskstimers WHERE staff_id !=' . get_staff_user_id())->result_array();
            $data['view_all'] = true;
        }

        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('staff_timesheets', array('view_all' => $data['view_all']));
        }

        if ($data['view_all'] == false) {
            unset($data['view_all']);
        }
        $data['logged_time'] = $this->staff_model->get_logged_time_data(get_staff_user_id());
        $data['title'] = '';
        $this->load->view('admin/staff/timesheets', $data);
    }

    public function delete()
    {
        if (has_permission('account_setup', '', 'delete', true)) {
            $success = $this->staff_model->delete($this->input->post('id'), $this->input->post('transfer_data_to'));
            if ($success) {
                set_alert('success', _l('deleted', _l('staff_member')));
            }
        }
        redirect(admin_url('staff'));
    }

    /* When staff edit his profile */
    public function edit_profile()
    {
        if ($this->input->post()) {
            $staff_id = get_staff_user_id();
            if (isset($_POST['imagebase64'])) {
                $data = $_POST['imagebase64'];

                list($type, $data) = explode(';', $data);
                list(, $data) = explode(',', $data);
                $data = base64_decode($data);
                $path = get_upload_path_by_type('staff') . $staff_id . '/';
                _maybe_create_upload_path($path);
                $filename = unique_filename($path, $_FILES["profile_image"]["name"]);
                $path .= '/round_' . $filename;
                file_put_contents($path, $data);
            }
            handle_staff_profile_image_upload();
            $data = $this->input->post();
            unset($data['imagebase64']);
            // Don't do XSS clean here.
            $data['email_signature'] = $this->input->post('email_signature', false);

            $success = $this->staff_model->update_profile($data, get_staff_user_id());
            if ($success) {
                set_alert('success', _l('staff_profile_updated'));
            }
            redirect(admin_url('staff/edit_profile/' . get_staff_user_id()));
        }
        $member = $this->staff_model->get(get_staff_user_id());
        $this->load->model('departments_model');
        $data['member'] = $member;
        $data['departments'] = $this->departments_model->get();
        $data['staff_departments'] = $this->departments_model->get_staff_departments($member->staffid);
        $data['title'] = $member->firstname . ' ' . $member->lastname;
        $this->load->view('admin/staff/profile', $data);
    }

    /* Remove staff profile image / ajax */
    public function remove_staff_profile_image($id = '')
    {
        $staff_id = get_staff_user_id();
        if (is_numeric($id) && (has_permission('account_setup', '', 'create', true) || has_permission('account_setup', '', 'edit', true))) {
            $staff_id = $id;
        }
        do_action('before_remove_staff_profile_image');
        $member = $this->staff_model->get($staff_id);
        if (file_exists(get_upload_path_by_type('staff') . $staff_id)) {
            delete_dir(get_upload_path_by_type('staff') . $staff_id);
        }
        $this->db->where('staffid', $staff_id);
        $this->db->update('tblstaff', array(
            'profile_image' => null
        ));
        if ($this->input->is_ajax_request()) {
            return true;
        }
        if (!is_numeric($id)) {
            redirect(admin_url('staff/edit_profile/' . $staff_id));
        } else {
            redirect(admin_url('staff/member/' . $staff_id));
        }
    }

    /* When staff change his password */
    public function change_password_profile()
    {
        if ($this->input->post()) {
            $response = $this->staff_model->change_password($this->input->post(), get_staff_user_id());
            if (is_array($response) && isset($response[0]['passwordnotmatch'])) {
                set_alert('danger', _l('staff_old_password_incorrect'));
            } else {
                if ($response == true) {
                    set_alert('success', _l('staff_password_changed'));
                } else {
                    set_alert('warning', _l('staff_problem_changing_password'));
                }
            }
            redirect(admin_url('staff/edit_profile'));
        }
    }

    /* View public profile. If id passed view profile by staff id else current user*/
    public function profile($id = '')
    {
        if ($id == '') {
            $id = get_staff_user_id();
        }

        do_action('staff_profile_access', $id);

        $data['logged_time'] = $this->staff_model->get_logged_time_data($id);
        $data['staff_p'] = $this->staff_model->get($id);

        if (!$data['staff_p']) {
            blank_page('Staff Member Not Found', 'danger');
        }

        $this->load->model('departments_model');
        $data['staff_departments'] = $this->departments_model->get_staff_departments($data['staff_p']->staffid);
        $data['departments'] = $this->departments_model->get();
        $data['title'] = _l('staff_profile_string') . ' - ' . $data['staff_p']->firstname . ' ' . $data['staff_p']->lastname;
        // notifications
        $this->db->select('user_type');
        $this->db->where('staffid', get_staff_user_id());
        $user_type = $this->db->get('tblstaff')->row();
        if ($user_type->user_type > 1) {
            $where = array('touserid' => get_staff_user_id());
        } else {
            $where = array('1' => 1);
        }
        $total_notifications = total_rows('tblnotifications', $where);
        $data['total_pages'] = ceil($total_notifications / $this->misc_model->get_notifications_limit());
        $this->load->view('admin/staff/myprofile', $data);
    }

    /* Change status to staff active or inactive / ajax */
    public function change_staff_status($id, $status)
    {
        if (has_permission('account_setup', '', 'edit', true)) {
            if ($this->input->is_ajax_request()) {
                $this->staff_model->change_staff_status($id, $status);
            }
        }
    }

    /* Logged in staff notifications*/
    public function notifications()
    {
        $this->load->model('misc_model');
        if ($this->input->post()) {
            $this->db->select('user_type');
            $this->db->where('staffid', get_staff_user_id());
            $user_type = $this->db->get('tblstaff')->row();

            $page = $this->input->post('page');
            $offset = ($page * $this->misc_model->get_notifications_limit());
            $this->db->limit($this->misc_model->get_notifications_limit(), $offset);
            if ($user_type->user_type > 1) {
                $this->db->like('touserid', get_staff_user_id());
                //$this->db->or_where('fromuserid', get_staff_user_id());
            }
            //$this->db->where('touserid', get_staff_user_id());
            $this->db->or_where('fromuserid', get_staff_user_id());
            $this->db->where('brandid', get_user_session());
            $this->db->order_by('date', 'desc');
            $notifications = $this->db->get('tblnotifications')->result_array();
            $i = 0;
            foreach ($notifications as $notification) {
                if (($notification['fromcompany'] == null && $notification['fromuserid'] != 0) || ($notification['fromcompany'] == null && $notification['fromclientid'] != 0)) {
                    if ($notification['fromuserid'] != 0) {
                        $notifications[$i]['profile_image'] = '<a href="' . admin_url('staff/member/' . $notification['fromuserid']) . '">' . staff_profile_image($notification['fromuserid'], array(
                                'staff-profile-image-small',
                                'img-circle',
                                'pull-left'
                            )) . '</a>';
                    } else {
                        $notifications[$i]['profile_image'] = '<a href="' . admin_url('clients/client/' . $notification['fromclientid']) . '">
                    <img class="client-profile-image-small img-circle pull-left" src="' . contact_profile_image_url($notification['fromclientid']) . '"></a>';
                    }
                } else {
                    $notifications[$i]['profile_image'] = '';
                    $notifications[$i]['full_name'] = '';
                }
                $additional_data = '';
                if (!empty($notification['additional_data'])) {
                    $additional_data = unserialize($notification['additional_data']);
                    $x = 0;
                    foreach ($additional_data as $data) {
                        if (strpos($data, '<lang>') !== false) {
                            $lang = get_string_between($data, '<lang>', '</lang>');
                            $temp = _l($lang);
                            if (strpos($temp, 'project_status_') !== false) {
                                $status = get_project_status_by_id(strafter($temp, 'project_status_'));
                                $temp = $status['name'];
                            }
                            $additional_data[$x] = $temp;
                        }
                        $x++;
                    }
                }
                $touserids = explode(',', $notification['touserid']);
                if (count($touserids) > 1) {
                    foreach ($touserids as $touserid) {
                        if ($touserid == get_staff_user_id()) {
                            $additional_data[] = "you";
                        } else {
                            $name = $this->misc_model->get_username_by_id($touserid);
                            $additional_data[] = $name;
                        }
                    }
                } else {
                    if ($notification['touserid'] == get_staff_user_id()) {
                        $additional_data[] = "you";
                    } else {
                        $name = $this->misc_model->get_username_by_id($notification['touserid']);
                        $additional_data[] = $name;
                    }
                }
                $notifications[$i]['description'] = _l($notification['description'], $additional_data);
                $notifications[$i]['date'] = time_ago($notification['date']);
                $i++;
            } //$notifications as $notification
            echo json_encode($notifications);
            die;
        }
    }
}
