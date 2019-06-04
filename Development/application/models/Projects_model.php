<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Projects_model extends CRM_Model
{
    private $project_settings;

    public function __construct()
    {
        parent::__construct();
        //static array for Invite Status
        $this->invite_vendor_status = array("Pending Approval from Account Owner", "Approved by Account Owner", "Sent to Vendor", "Approved by Vendor", "Declined by Vendor", "Declined by Account Owner");

        $this->invite_collaborator_status = array("Pending Approval from Account Owner", "Approved by Account Owner", "Sent to Collaborator", "Approved by Collaborator", "Declined by Collaborator", "Declined by Account Owner");

        $this->invite_venue_status = array("Pending Approval from Venue Owner", "Approved by Account Owner", "Sent to Venue Owner", "Approved by Venue Owner", "Declined by Venue Owner", "Declined by Account Owner");

        $project_settings = array(
            'view_tasks',
            'create_tasks',
            'edit_tasks',
            'comment_on_tasks',
            'view_task_comments',
            'view_task_attachments',
            'view_task_checklist_items',
            'upload_on_tasks',
            'view_task_total_logged_time',
            'view_finance_overview',
            'upload_files',
            'open_discussions',
            'view_milestones',
            'view_gantt',
            'view_timesheets',
            'view_activity_log',
            'view_team_members'
        );
        $this->project_settings = do_action('project_settings', $project_settings);
        $this->is_admin = is_admin();

        $this->load->model('staff_model');
        $this->load->model('misc_model');
        $this->load->model('emails_model');
    }

    public function get_project_statuses()
    {
        $statuses = do_action('before_get_project_statuses', array(
            array(
                'id' => 1,
                'color' => '#989898',
                'name' => _l('project_status_1'),
                'order' => 1,
                'filter_default' => true,
            ),
            array(
                'id' => 2,
                'color' => '#03a9f4',
                'name' => _l('project_status_2'),
                'order' => 2,
                'filter_default' => true,
            ),
            array(
                'id' => 3,
                'color' => '#ff6f00',
                'name' => _l('project_status_3'),
                'order' => 3,
                'filter_default' => true,
            ),
            array(
                'id' => 4,
                'color' => '#84c529',
                'name' => _l('project_status_4'),
                'order' => 100,
                'filter_default' => false,
            ),
            array(
                'id' => 5,
                'color' => '#989898',
                'name' => _l('project_status_5'),
                'order' => 4,
                'filter_default' => false,
            ),
        ));

        usort($statuses, function ($a, $b) {
            return $a['order'] - $b['order'];
        });

        return $statuses;
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/18/2017
     * Get project statuses
     * @param mixed $id status id
     * @return mixed      object if id passed else array
     */
    public function get_project_status($id = '', $where = array())
    {
        $brandid = get_user_session();
        $session_data = get_session_data();

        $is_admin = $session_data['is_admin'];

        $where = "";
        $where .= 'deleted=0';
        if ($is_admin == false) {
            $where .= ' and brandid =' . $brandid;
        }

        if (is_numeric($id)) {
            $where .= ' and id=' . $id;
            $this->db->where($where);
            $this->db->order_by("statusorder", "asc");

            return $this->db->get('tblprojectstatus')->row();
        }

        $this->db->where($where);
        $this->db->order_by("statusorder", "asc");
        return $this->db->get('tblprojectstatus')->result_array();
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/18/2017
     * Add new project status
     * @param array $data lead status data
     */
    public function add_status($data)
    {
        $this->db->insert('tblprojectstatus', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Project Status Added [StatusID: ' . $insert_id . ', Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/18/2017
     * update project status
     */
    public function update_status($data, $id)
    {
        $data['updated_by'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update('tblprojectstatus', $data);

        if ($this->db->affected_rows() > 0) {
            logActivity('Project Status Updated [StatusID: ' . $id . ', Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/18/2017
     * Delete project status from database
     * @param mixed $id status id
     * @return boolean
     */
    public function delete_status($id)
    {
        $current = $this->get_project_status($id);
        // Check if is already using in table
        if (is_reference_in_table('status', 'tblprojects', $id)) {
            return array(
                'referenced' => true
            );
        }

        $data['deleted'] = 1;
        $data['updated_by'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update('tblprojectstatus', $data);

        if ($this->db->affected_rows() > 0) {
            $session_data = get_session_data();
            $is_admin = $session_data['is_admin'];

            if ($is_admin == false) {
                if (get_brand_option('projects_default_status') == $id) {
                    update_brand_option('projects_default_status', '');
                }
            } else {
                if (get_option('projects_default_status') == $id) {
                    update_option('projects_default_status', '');
                }
            }

            logActivity('Project Status Deleted [StatusID: ' . $id . ']');

            return true;
        }

        return false;
    }

    public function get_distinct_tasks_timesheets_staff($project_id)
    {
        return $this->db->query('SELECT DISTINCT staff_id FROM tbltaskstimers LEFT JOIN tblstafftasks ON tblstafftasks.id = tbltaskstimers.task_id WHERE rel_type="project" AND rel_id=' . $project_id)->result_array();
    }

    public function get_most_used_billing_type()
    {
        return $this->db->query("SELECT billing_type, COUNT(*) AS total_usage
                FROM tblprojects
                GROUP BY billing_type
                ORDER BY total_usage DESC
                LIMIT 1")->row();
    }

    public function timers_started_for_project($project_id, $where = array(), $task_timers_where = array())
    {
        $tasks = $this->get_tasks($project_id, $where);
        $timers_found = false;
        $_task_timers_where = array();
        foreach ($task_timers_where as $key => $val) {
            $_task_timers_where[$key] = $val;
        }
        foreach ($tasks as $task) {
            $_task_timers_where['task_id'] = $task['id'];
            if (total_rows('tbltaskstimers', $_task_timers_where) > 0) {
                $timers_found = true;
                break;
            }
        }

        return $timers_found;
    }

    public function pin_action($id)
    {
        if (total_rows('tblpinnedprojects', array(
                'staff_id' => get_staff_user_id(),
                'project_id' => $id
            )) == 0) {
            $this->db->insert('tblpinnedprojects', array(
                'staff_id' => get_staff_user_id(),
                'project_id' => $id
            ));

            return true;
        } else {
            $this->db->where('project_id', $id);
            $this->db->where('staff_id', get_staff_user_id());
            $this->db->delete('tblpinnedprojects');

            return true;
        }
    }

    public function get_currency($id)
    {
        $project = $this->get($id);
        $this->load->model('currencies_model');
        // $customer_currency = $this->clients_model->get_customer_default_currency($project->clientid);
        // if ($customer_currency != 0) {
        //     $currency = $this->currencies_model->get($customer_currency);
        // } else {
        //     $currency = $this->currencies_model->get_base_currency();
        // }
        $currency = $this->currencies_model->get_base_currency();

        return $currency;
    }

    public function calc_progress($id)
    {
        $this->db->select('progress_from_tasks,progress,status');
        $this->db->where('id', $id);
        $project = $this->db->get('tblprojects')->row();

        if ($project->status == 4) {
            return 100;
        }

        if ($project->progress_from_tasks == 1) {
            return $this->calc_progress_by_tasks($id);
        } else {
            return $project->progress;
        }
    }

    public function calc_progress_by_tasks($id)
    {
        $total_project_tasks = total_rows('tblstafftasks', array(
            'rel_type' => 'project',
            'rel_id' => $id
        ));
        $total_finished_tasks = total_rows('tblstafftasks', array(
            'rel_type' => 'project',
            'rel_id' => $id,
            'status' => 5
        ));
        $percent = 0;
        if ($total_finished_tasks >= floatval($total_project_tasks)) {
            $percent = 100;
        } else {
            if ($total_project_tasks !== 0) {
                $percent = number_format(($total_finished_tasks * 100) / $total_project_tasks, 2);
            }
        }

        return $percent;
    }

    public function get_last_project_settings()
    {
        $this->db->select('id');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $last_project = $this->db->get('tblprojects')->row();
        if ($last_project) {
            return $this->get_project_settings($last_project->id);
        }

        return array();
    }

    public function get_settings()
    {
        return $this->project_settings;
    }

    public function get($id = '', $where = array(), $staffid = '', $contactid = '', $isvendor = '', $iscollaborator = '', $venueid = '')
    {
        $userid = get_staff_user_id();
        $staff = $this->staff_model->get($userid);

        $user_type = $staff->user_type;
        // get all projects which are assigned to team member, if team member logged in
        if ($user_type == 2) {
            $this->db->select('*,maintbl.name, maintbl.id,tblprojectstatus.name as status_name, concat(tblstaff.firstname,  \'  \', tblstaff.lastname) as assigned_name,tblstaff.email as assigned_email, tblstaff.phonenumber as assigned_phone, maintbl.project_profile_image, DATE_FORMAT( maintbl.eventstartdatetime, "%Y-%m-%d %H:%i") as eventstartdate, (SELECT COUNT(id) FROM tblprojects p1 WHERE p1.parent = maintbl.id AND p1.deleted = 0 ) AS no_of_events, tblleadssources.name as source_name, tbleventtype.eventtypename');

            //$this->db->select('*,maintbl.name, maintbl.id,tblprojectstatus.name as status_name, concat(tblstaff.firstname,  \'  \', tblstaff.lastname) as assigned_name,tblstaff.email as assigned_email,tblstaff.phonenumber as assigned_phone,maintbl.project_profile_image, DATE_FORMAT( maintbl.eventstartdatetime, "%Y-%m-%d %H:%i") as eventstartdate, tblleadssources.name as source_name, tbleventtype.eventtypename');

        } else {
            //get all projects for account owner
            $this->db->select('DISTINCT(`maintbl`.`id`), maintbl.*, `tblprojectstatus`.`name` as `status_name`, concat(tblstaff.firstname, " ", tblstaff.lastname) as assigned_name,tblstaff.email as assigned_email, tblstaff.phonenumber as assigned_phone, `maintbl`.`project_profile_image`, DATE_FORMAT( maintbl.eventstartdatetime, "%Y-%m-%d %H:%i") as eventstartdate, (SELECT COUNT(id) FROM tblprojects p1 WHERE p1.parent = maintbl.id AND p1.deleted = 0) AS no_of_events, tblleadssources.name as source_name,tbleventtype.eventtypename');
        }

        if ($staffid != "" || $contactid != "") {
            $q1 = $this->db->query('SELECT GROUP_CONCAT(`id`) AS pid FROM `tblprojects` WHERE `id` = ' . $id . ' OR `parent` = ' . $id);
            $all_project = $q1->row();

            //for vendor
            if ($isvendor == 1) {
                $q1 = $this->db->query('SELECT GROUP_CONCAT(`tblinvitestatus`.`projectid`) AS pid FROM `tblinvitestatus` JOIN `tblinvite` ON `tblinvitestatus`.`inviteid` = `tblinvite`.`inviteid` WHERE `tblinvite`.`staffid` = ' . $staffid . ' AND `tblinvitestatus`.`projectid` IN (' . $all_project->pid . ') AND `tblinvitestatus`.`status` = "' . $this->invite_vendor_status[3] . '"');
                $projects = $q1->row();
            }

            //for collaborator
            if ($iscollaborator == 1) {
                $q1 = $this->db->query('SELECT GROUP_CONCAT(`tblinvitestatus`.`projectid`) AS pid FROM `tblinvitestatus` JOIN `tblinvite` ON `tblinvitestatus`.`inviteid` = `tblinvite`.`inviteid` WHERE `tblinvite`.`staffid` = ' . $staffid . ' AND `tblinvitestatus`.`projectid` IN (' . $all_project->pid . ') AND `tblinvitestatus`.`status` = "' . $this->invite_collaborator_status[3] . '"');
                $projects = $q1->row();
            }
        }

        //for venue
        if ($venueid > 0) {
            $q1 = $this->db->query('SELECT GROUP_CONCAT(`id`) AS pid FROM `tblprojects` WHERE `id` = ' . $id . ' OR `parent` = ' . $id);
            $all_project = $q1->row();

            $q1 = $this->db->query('SELECT GROUP_CONCAT(`tblinvitestatus`.`projectid`) AS pid FROM `tblinvitestatus` JOIN `tblinvite` ON `tblinvitestatus`.`inviteid` = `tblinvite`.`inviteid` WHERE `tblinvite`.`venueid` = ' . $venueid . ' AND `tblinvitestatus`.`projectid` IN (' . $all_project->pid . ') AND `tblinvitestatus`.`status` = "' . $this->invite_venue_status[3] . '"');
            $projects = $q1->row();
        }

        $this->db->join('tblprojectstatus', 'tblprojectstatus.id = maintbl.status', 'left');
        $this->db->join('tblstaffprojectassignee', 'tblstaffprojectassignee.projectid = maintbl.id', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblstaffprojectassignee.assigned', 'left');
        $this->db->join('tblleadssources', 'tblleadssources.id=maintbl.source', 'left');
        $this->db->join('tbleventtype', 'tbleventtype.eventtypeid=maintbl.eventtypeid', 'left');

        if (isset($staffid) && $staffid > 0) {
            $this->db->join('tblprojectcontact', 'tblprojectcontact.projectid = maintbl.id or tblprojectcontact.eventid = maintbl.id', 'left');
            $this->db->where('tblprojectcontact.contactid', $staffid);
        } else if (isset($contactid) && $contactid > 0) {
            $this->db->join('tblprojectcontact', 'tblprojectcontact.projectid = maintbl.id or tblprojectcontact.eventid = maintbl.id', 'left');
            $this->db->where('tblprojectcontact.contactid', $contactid);
        } else if (isset($venueid) && $venueid > 0) {
            $this->db->join('tblprojectvenue', 'tblprojectvenue.projectid = maintbl.id or tblprojectvenue.eventid = maintbl.id', 'left');
            $this->db->where('tblprojectvenue.venueid', $venueid);
        } else {
            $this->db->join('tblprojectcontact', 'tblprojectcontact.projectid = maintbl.id', 'left');
        }

        if ($user_type == 2) {
            //$this->db->where('maintbl.assigned = ' . $userid . ' OR tblprojectcontact.contactid = ' . $userid);
            $this->db->where('(tblstaffprojectassignee.assigned =' . get_staff_user_id() . ' OR addedfrom = ' . get_staff_user_id() . ' OR tblprojectcontact.contactid = ' . get_staff_user_id() . ')');
        }

        if ($staffid > 0) {
            $this->db->where('maintbl.id IN (' . $id . ')');
            // $this->db->where('tblprojects.id IN (' . $projects->pid . ')');
            //$this->db->where('tblprojects.id IN (' .  strstr($projects->pid , ',', true) . ')');
        }

        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];

        if ($is_sido_admin == 0 && $is_admin == 0) {
            /*if (!is_brand_private()) {*/
            $this->db->where('maintbl.brandid', get_user_session());
            /*}*/
        }

        if (is_numeric($id)) {

            /*echo "<pre>";
            print_r($staffid);
            die();*/
            if ($staffid > 0 || $contactid > 0) {
                if ($isvendor == 1) {
                    $this->db->where('tblprojectcontact.isvendor', 1);
                    $this->db->group_by('tblprojectcontact.contactid, tblprojectcontact.isvendor');
                }

                if ($iscollaborator == 1) {
                    $this->db->where('tblprojectcontact.iscollaborator', 1);
                    $this->db->group_by('tblprojectcontact.contactid, tblprojectcontact.iscollaborator');
                }

                return $this->db->get('tblprojects as maintbl')->result_array();

                //return $this->db->get('tblprojects as maintbl')->result_array();
            } else if ($venueid > 0) {
                $this->db->where('tblprojectvenue.venueid', $venueid);
                $this->db->group_by('maintbl.id');
                return $this->db->get('tblprojects as maintbl')->result_array();
            } else {
                $this->db->where('maintbl.id', $id);
                $project = $this->db->get('tblprojects as maintbl')->row();
                if ($project) {
                    $project->attachments = $this->get_files($id);
                    $project->assigned = $this->get_project_assignee($id);
                    $project->pcontact = $this->get_project_contact($id);
                    /*echo "<pre>";
                    print_r($project->pcontact);
                    die('<--here');*/

                }
                return $project;
            }
        }
        $this->db->where($where);
        $this->db->order_by("eventstartdatetime", "asc");
        return $this->db->get('tblprojects  as maintbl')->result_array();
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/19/2017
     * for project kanban view
     */
    public function do_project_kanban_query($status, $search = '', $page = 1, $sort = array(), $count = false)
    {
        $session_data = get_session_data();

        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
        $user_id = $session_data['staff_user_id'];
        $user_type = $session_data['user_type'];

        $brandid = get_user_session();
        // if($is_sido_admin == 0 && $is_admin == 0) {
        //     $limit                          = get_brand_option('projects_kanban_limit');
        //     $default_leads_kanban_sort      = get_brand_option('default_projects_kanban_sort');
        //     $default_leads_kanban_sort_type = get_brand_option('default_projects_kanban_sort_type');    
        // } else {
        $limit = get_option('projects_kanban_limit');
        $default_projects_kanban_sort = get_option('default_projects_kanban_sort');
        $default_projects_kanban_sort_type = get_option('default_projects_kanban_sort_type');
        //}       

        $this->db->select('tblprojects.name as project_name, tblprojects.id as id, tblprojects.assigned,  tblprojects.eventstartdatetime, tblprojects.eventenddatetime, tblprojects.status, tbleventtype.eventtypename  as eventtypename, tblprojects.venueid');
        /*$this->db->select('*');*/
        $this->db->from('tblprojects');
        $this->db->join('tblstaffprojectassignee', 'tblstaffprojectassignee.projectid = tblprojects.id', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblstaffprojectassignee.assigned', 'left');
        $this->db->join('tbleventtype', 'tbleventtype.eventtypeid = tblprojects.eventtypeid', 'left');
        $this->db->join('tblprojectcontact', 'tblprojectcontact.projectid = tblprojects.id AND tblprojectcontact.active=1', 'left');
        //$this->db->join('tblleadssources', 'tblleadssources.brandid = tblprojects.id', 'left');
        $this->db->where('status', $status);
        $this->db->where('tblprojects.deleted = ', 0);
        $this->db->where('tblprojects.parent = ', 0);
        $this->db->group_by('tblprojects.id');

        if (!$this->is_admin) {
            /*if (!is_brand_private()) {*/
                $this->db->where('tblprojects.brandid =' . $brandid);
            /*} else {*/
                $this->db->where('(tblstaffprojectassignee.assigned = ' . get_staff_user_id() . ' OR addedfrom =' . get_staff_user_id() . ' OR tblprojectcontact.contactid = ' . get_staff_user_id() . ')');
            /*}*/
        }
        if ($search != '') {
            if (!_startsWith($search, '#')) {
                $this->db->where('(tblprojects.name LIKE "%' . $search . '%" OR tblleadssources.name LIKE "%' . $search . '%" OR tblstaff.email LIKE "%' . $search . '%" OR tblstaff.phonenumber LIKE "%' . $search . '%" OR CONCAT(tblstaff.firstname, \' \', tblstaff.lastname) LIKE "%' . $search . '%")');
            } else {
                $this->db->where('tblprojects.id IN
                (SELECT rel_id FROM tbltags_in WHERE tag_id IN
                (SELECT id FROM tbltags WHERE name="' . strafter($search, '#') . '")
                AND tbltags_in.rel_type=\'lead\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
                ');
            }
        }

        if (isset($sort['sort_by']) && $sort['sort_by'] && isset($sort['sort']) && $sort['sort']) {
            $this->db->order_by($sort['sort_by'], $sort['sort']);
        } else {
            $this->db->order_by($default_projects_kanban_sort, $default_projects_kanban_sort_type);
        }

        if ($count == false) {
            if ($page > 1) {
                $page--;
                $position = ($page * $limit);
                $this->db->limit($limit, $position);
            } else {
                $this->db->limit($limit);
            }
        }

        if ($count == false) {
            return $this->db->get()->result_array();
        } else {
            return $this->db->count_all_results();
        }
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/26/2017
     * get project detail for Project dashboard
     */
    public function getprojectdashboard($id = '', $where = array())
    {
        $userid = get_staff_user_id();
        $staff = $this->staff_model->get($userid);

        $user_type = $staff->user_type;

        if (is_numeric($id)) {
            $this->db->where($where);

            $this->db->select('tblprojects.*,tbleventtype.eventtypename  as eventtypename,tblprojectstatus.name as status_name,tblleadssources.name as source_name, (SELECT p1.name FROM tblprojects p1 WHERE p1.id = tblprojects.parent) AS parent_name, CONCAT(firstname, \' \', lastname) as assigned_name, (SELECT COUNT(p.id) FROM tblprojects p WHERE p.parent = ' . $id . ' AND p.deleted = 0) AS no_of_events, (SELECT TO_DAYS(CURRENT_DATE()) - TO_DAYS(DATE(eventstartdatetime)) FROM tblprojects p1 WHERE p1.parent = ' . $id . ' AND p1.deleted = 0 AND DATE(p1.`eventstartdatetime`) > CURRENT_DATE() ORDER BY p1.eventstartdatetime ASC LIMIT 0, 1) AS days_left');
            $this->db->join('tblprojectstatus', 'tblprojectstatus.id=tblprojects.status', 'left');
            $this->db->join('tbleventtype', 'tbleventtype.eventtypeid=tblprojects.eventtypeid', 'left');
            $this->db->join('tblstaff', 'tblstaff.staffid=tblprojects.assigned', 'left');
            $this->db->join('tblprojectcontact', 'tblprojectcontact.projectid=tblprojects.id', 'left');
            $this->db->join('tblleadssources', 'tblleadssources.id=tblprojects.source', 'left');

            $this->db->where('tblprojects.id', $id);
            $project = $this->db->get('tblprojects')->row();

            $this->db->select('tblpins.pinid');
            $this->db->where('tblpins.userid', $userid);
            $this->db->where('tblpins.pintype', 'Project');
            $this->db->where('tblpins.pintypeid', $id);
            $tblpins = $this->db->get('tblpins')->row();

            $project->project_parent = $project->parent;

            //get parent id for the project
            if ($project->parent > 0) {
                $parent = $project->parent;
            } else {
                $parent = $id;
            }

            $project->projectid = $parent;

            // get all sub projects which are assigned to team member, if team member logged in
            if ($user_type == 2) {
                $subproject_count_query = $this->db->query('SELECT COUNT(p.id) AS no_of_events, (SELECT TO_DAYS(CURRENT_DATE()) - TO_DAYS(DATE(eventstartdatetime)) FROM tblprojects p1 WHERE p1.parent = ' . $parent . ' AND p1.deleted = 0 AND DATE(p1.`eventstartdatetime`) > CURRENT_DATE() ORDER BY p1.eventstartdatetime ASC LIMIT 0, 1) AS days_left FROM tblprojects p LEFT JOIN tblprojectcontact pc ON p.id = pc.eventid WHERE p.parent = ' . $parent . ' AND p.deleted = 0 AND (p.assigned = ' . $userid . ' OR pc.contactid = ' . $userid . ')');
            } else {
                //get all sub projects for account owner
                $subproject_count_query = $this->db->query('SELECT COUNT(p.id) AS no_of_events, (SELECT TO_DAYS(CURRENT_DATE()) - TO_DAYS(DATE(eventstartdatetime)) FROM tblprojects p1 WHERE p1.parent = ' . $parent . ' AND p1.deleted = 0 AND DATE(p1.`eventstartdatetime`) > CURRENT_DATE() ORDER BY p1.eventstartdatetime ASC LIMIT 0, 1) AS days_left FROM tblprojects p WHERE p.parent = ' . $parent . ' AND p.deleted = 0 ');
            }

            $subproject_count = $subproject_count_query->row();
            $project->no_of_events = $subproject_count->no_of_events;
            $project->days_left = $subproject_count->days_left;

            // get all sub projects for today which are assigned to team member, if team member logged in
            if ($user_type == 2) {
                $today_subproject_query = $this->db->query('SELECT `tblprojects`.*, CONCAT(firstname, \' \', lastname) as assigned_name, CURRENT_DATE() as currentdatetime
    FROM `tblprojects`
    LEFT JOIN `tblstaffprojectassignee` ON `tblstaffprojectassignee`.`projectid`=`tblprojects`.`id`
    LEFT JOIN `tblstaff` ON `tblstaff`.`staffid`=`tblstaffprojectassignee`.`assigned`
    LEFT JOIN `tblprojectcontact` ON `tblprojects`.`id` = `tblprojectcontact`.`eventid` 
    WHERE `tblprojects`.`parent` = ' . $parent . ' AND `tblprojects`.`deleted` = 0 AND DATE(`tblprojects`.`eventstartdatetime`) = CURRENT_DATE() AND (`tblstaffprojectassignee`.`assigned` = ' . $userid . ' OR `tblprojectcontact`.`contactid` = ' . $userid . ')
');
            } else {
                //get all sub projects for today for account owner
                $today_subproject_query = $this->db->query('SELECT `tblprojects`.*, CONCAT(firstname, \' \', lastname) as assigned_name, CURRENT_DATE() as currentdatetime
    FROM `tblprojects`
    LEFT JOIN `tblstaff` ON `tblstaff`.`staffid`=`tblprojects`.`assigned`
    WHERE `tblprojects`.`parent` = ' . $parent . ' AND `tblprojects`.`deleted` = 0 AND DATE(`tblprojects`.`eventstartdatetime`) = CURRENT_DATE() 
');
            }
            $today_subproject = $today_subproject_query->result_array();

            // get all sub projects for future dates which are assigned to team member, if team member logged in
            if ($user_type == 2) {
                $future_subproject_query = $this->db->query('SELECT `tblprojects`.*, CONCAT(firstname, \' \', lastname) as assigned_name, CURRENT_DATE() as currentdatetime
    FROM `tblprojects`
    LEFT JOIN `tblstaffprojectassignee` ON `tblstaffprojectassignee`.`projectid`=`tblprojects`.`id`
    LEFT JOIN `tblstaff` ON `tblstaff`.`staffid`=`tblstaffprojectassignee`.`assigned`
    LEFT JOIN `tblprojectcontact` ON `tblprojects`.`id` = `tblprojectcontact`.`eventid` 
    WHERE `tblprojects`.`parent` = ' . $parent . ' AND `tblprojects`.`deleted` = 0 AND DATE(`tblprojects`.`eventstartdatetime`) > CURRENT_DATE() AND (`tblstaffprojectassignee`.`assigned` = ' . $userid . ' OR `tblprojectcontact`.`contactid` = ' . $userid . ')
    ORDER BY `tblprojects`.`eventstartdatetime` ASC');
            } else {
                //get all sub projects for future date for account owner
                $future_subproject_query = $this->db->query('SELECT `tblprojects`.*, CONCAT(firstname, \' \', lastname) as assigned_name, CURRENT_DATE() as currentdatetime
    FROM `tblprojects`
    LEFT JOIN `tblstaff` ON `tblstaff`.`staffid`=`tblprojects`.`assigned`
    WHERE `tblprojects`.`parent` = ' . $parent . ' AND `tblprojects`.`deleted` = 0 AND DATE(`tblprojects`.`eventstartdatetime`) > CURRENT_DATE()  
    ORDER BY `tblprojects`.`eventstartdatetime` ASC');
            }
            $future_subproject = $future_subproject_query->result_array();

            // get all sub projects for past dates which are assigned to team member, if team member logged in
            if ($user_type == 2) {
                $past_subproject_query = $this->db->query('SELECT `tblprojects`.*, CONCAT(firstname, \' \', lastname) as assigned_name, CURRENT_DATE() as currentdatetime
    FROM `tblprojects`
    LEFT JOIN `tblstaffprojectassignee` ON `tblstaffprojectassignee`.`projectid`=`tblprojects`.`id`
    LEFT JOIN `tblstaff` ON `tblstaff`.`staffid`=`tblstaffprojectassignee`.`assigned`
    LEFT JOIN `tblprojectcontact` ON `tblprojects`.`id` = `tblprojectcontact`.`eventid` 
    WHERE `tblprojects`.`parent` = ' . $parent . ' AND `tblprojects`.`deleted` = 0 AND DATE(`tblprojects`.`eventstartdatetime`) < CURRENT_DATE() AND (`tblstaffprojectassignee`.`assigned` = ' . $userid . ' OR `tblprojectcontact`.`contactid` = ' . $userid . ')
    ORDER BY `tblprojects`.`eventstartdatetime` DESC');
            } else {
                //get all sub projects for past date for account owner
                $past_subproject_query = $this->db->query('SELECT `tblprojects`.*, CONCAT(firstname, \' \', lastname) as assigned_name, CURRENT_DATE() as currentdatetime
    FROM `tblprojects`
    LEFT JOIN `tblstaff` ON `tblstaff`.`staffid`=`tblprojects`.`assigned`
    WHERE `tblprojects`.`parent` = ' . $parent . ' AND `tblprojects`.`deleted` = 0 AND DATE(`tblprojects`.`eventstartdatetime`) < CURRENT_DATE() 
    ORDER BY `tblprojects`.`eventstartdatetime` DESC');
            }
            $past_subproject = $past_subproject_query->result_array();

            //merging all sub projects in one array
            $project->sub_projects = array_merge($today_subproject, $future_subproject, $past_subproject);
            $sub_projects_id = array();
            array_push($sub_projects_id, $id);

            foreach ($project->sub_projects as $sub_projects) {
                array_push($sub_projects_id, $sub_projects['id']);
            }

            //get last and next interaction for projects and/or sub project

            $this->db->select('id');
            $this->db->where('rel_type IN ("project","event")');
            $this->db->where_in('rel_id', $sub_projects_id);
            $this->db->where('deleted', 0);
            $this->db->where('brandid', get_user_session());
            $task_ids = $this->db->get('tblstafftasks')->result_array();
            $task_id_final = array();
            foreach ($task_ids as $ti) {
                array_push($task_id_final, $ti['id']);
            }

            $this->db->select('meetingid');
            $this->db->where('rel_type IN ("project","event")');
            $this->db->where_in('rel_id', $sub_projects_id);
            $this->db->where('deleted', 0);
            $this->db->where('brandid', get_user_session());
            $meeting_ids = $this->db->get('tblmeetings')->result_array();
            $meeting_id_final = array();

            foreach ($meeting_ids as $mi) {
                array_push($meeting_id_final, $mi['meetingid']);
            }
            $lasttask = array();
            $nexttask = array();
            $lastmeeting = array();
            $nextmeeting = array();
            $lastactiondates = array();
            $nextactiondates = array();

            if (!empty($task_id_final)) {
                $this->db->select('datefinished as lastaction,tblstafftasks.name as lastaction_name');
                $this->db->where('DATE(datefinished) <= CURRENT_DATE() AND DATE(datefinished)!="0000-00-00 00:00:00"');
                $this->db->where_in('id', $task_id_final);
                $this->db->order_by("lastaction", "desc");
                $this->db->limit(1);
                $lasttask = $this->db->get('tblstafftasks')->row();
                if (!empty($lasttask) && $lasttask->lastaction == "0000-00-00 00:00:00") {
                    $lasttask = array();
                }
                //$lastactiondates = $this->db->get('tblstafftasks')->row();

                $this->db->select('duedate as nextaction,tblstafftasks.name as nextaction_name');
                $this->db->where('DATE(duedate) > CURRENT_DATE()');
                $this->db->where_in('id', $task_id_final);
                $this->db->order_by("nextaction", "asc");
                $this->db->limit(1);
                $nexttask = $this->db->get('tblstafftasks')->row();
                //$nextactiondates = $this->db->get('tblstafftasks')->row();
            }
            if (!empty($meeting_id_final)) {
                // Get last meeting date
                $this->db->select('start_date as lastaction,tblmeetings.name as lastaction_name');
                $this->db->where('DATE(start_date) <= CURRENT_DATE()');
                $this->db->where_in('meetingid', $meeting_id_final);
                $this->db->order_by("lastaction", "desc");
                $this->db->limit(1);
                $lastmeeting = $this->db->get('tblmeetings')->row();
                //$lastactiondates = $this->db->get('tblmeetings')->row();

                // Get next meeting date
                $this->db->select('start_date as nextaction,tblmeetings.name as nextaction_name');
                $this->db->where('DATE(start_date) > CURRENT_DATE()');
                $this->db->where_in('meetingid', $meeting_id_final);
                $this->db->order_by("nextaction", "asc");
                $this->db->limit(1);
                $nextmeeting = $this->db->get('tblmeetings')->row();
                //$nextactiondates = $this->db->get('tblmeetings')->row();

            }

            if (!empty($meeting_id_final) || !empty($task_id_final)) {

                if (!empty($nexttask) && !empty($nextmeeting)) {
                    if (strtotime($nexttask->nextaction) > strtotime($nextmeeting->nextaction)) {
                        $nextactiondates = $nextmeeting;
                    } else {
                        $nextactiondates = $nexttask;
                    }
                } elseif (!empty($nexttask) && empty($nextmeeting)) {
                    $nextactiondates = $nexttask;
                } elseif (empty($nexttask) && !empty($nextmeeting)) {
                    $nextactiondates = $nextmeeting;
                }

                if (!empty($lasttask) && !empty($lastmeeting)) {
                    if (strtotime($lasttask->nextaction) > strtotime($lastmeeting->nextaction)) {
                        $lastactiondates = $lasttask;
                    } else {
                        $lastactiondates = $lastmeeting;
                    }
                } elseif (!empty($lasttask) && empty($lastmeeting)) {
                    $lastactiondates = $nexttask;
                } elseif (empty($lasttask) && !empty($lastmeeting)) {
                    $lastactiondates = $lastmeeting;
                }

                $project->lastaction = isset($lastactiondates->lastaction) ? $lastactiondates->lastaction : "";
                $project->nextaction = isset($nextactiondates->nextaction) ? $nextactiondates->nextaction : "";
                $project->last_meeting_name = isset($lastactiondates->lastaction_name) ? $lastactiondates->lastaction_name : "Project Booked";
                $project->next_meeting_name = isset($nextactiondates->nextaction_name) ? $nextactiondates->nextaction_name : "";

            } else {
                $project->lastaction = "";
                $project->nextaction = "";
                $project->last_meeting_name = "Project Booked";
                $project->next_meeting_name = "";
            }

            if ($project) {
                if ($project->from_form_id != 0) {
                    $project->form_data = $this->get_form(array(
                        'id' => $project->from_form_id
                    ));
                }
                $project->attachments = $this->get_files($id);
                $project->pinid = isset($tblpins->pinid) ? $tblpins->pinid : 0;
            }

            $assignedOutput = '';
            if (!empty($project->assigned_name)) {

                $full_name = $project->assigned_name;

                $assignedOutput = '<a data-toggle="tooltip" data-title="' . $full_name . '"  href="javascript:void(0)">' . staff_profile_image($project->assigned, array(
                        'staff-profile-image-small'
                    )) . '</a>';
            }

            $project->assigned_name = $assignedOutput;

            $project->is_client = $this->is_client();

            $project->permission = $this->get_project_tool_permission($id);

            $project->vendors = $this->get_project_invites($id, 3);

            $project->collaborators = $this->get_project_invites($id, 4);
            $project->clients = $this->get_project_clients($id);
            $project->venues = $this->get_project_invites($id, 5);
            /*echo "<pre>";
            print_r($project->collaborators);
            die('<--here');*/
            return $project;
        }

        return $this->db->get('tblprojects')->result_array();
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/27/2017
     * get sub project detail for Project dashboard
     */
    public function getsubprojectdashboard($id = '', $where = array())
    {
        if (is_numeric($id)) {
            $this->db->where($where);
            $userid = get_staff_user_id();
            //get sub project details
            $this->db->select('tblprojects.*, (SELECT p1.name FROM tblprojects p1 WHERE p1.id = tblprojects.parent) AS parent_name, tbleventtype.eventtypename  as eventtypename,tblprojectstatus.name as status_name, CONCAT(firstname, \' \', lastname) as assigned_name');
            $this->db->join('tblprojectstatus', 'tblprojectstatus.id=tblprojects.status', 'left');
            $this->db->join('tbleventtype', 'tbleventtype.eventtypeid=tblprojects.eventtypeid', 'left');
            $this->db->join('tblstaff', 'tblstaff.staffid=tblprojects.assigned', 'left');

            $this->db->where('tblprojects.id', $id);
            $project = $this->db->get('tblprojects')->row();

            $this->db->select('tblprojects.*, CONCAT(firstname, \' \', lastname) as assigned_name');
            $this->db->join('tblstaff', 'tblstaff.staffid=tblprojects.assigned', 'left');
            $this->db->where('tblprojects.parent', $id);
            $this->db->where('tblprojects.deleted', 0);
            $project->sub_projects = $this->db->get('tblprojects')->result_array();

            $this->db->select('meetingid');
            $this->db->where('rel_type', "event");
            $this->db->where('rel_id', $id);
            $meeting_ids = $this->db->get('tblmeetings')->result_array();

            $meeting_id_final = array();
            foreach ($meeting_ids as $mi) {
                array_push($meeting_id_final, $mi['meetingid']);
            }

            if (!empty($meeting_id_final)) {
                // Get last meeting date
                $this->db->select('DATE(tblmeetings.start_date) as lastaction,tblmeetings.name as last_meeting_name');
                $this->db->where('DATE(start_date) <= CURRENT_DATE()');
                $this->db->where_in('meetingid', $meeting_id_final);
                $this->db->order_by("lastaction", "desc");
                $this->db->limit(1);
                $lastactiondates = $this->db->get('tblmeetings')->row();

                // Get next meeting date
                $this->db->select('DATE(tblmeetings.start_date) as nextaction,tblmeetings.name as next_meeting_name');
                $this->db->where('DATE(start_date) > CURRENT_DATE()');
                $this->db->where_in('meetingid', $meeting_id_final);
                $this->db->order_by("nextaction", "asc");
                $this->db->limit(1);
                $nextactiondates = $this->db->get('tblmeetings')->row();

                $project->lastaction = isset($lastactiondates->lastaction) ? $lastactiondates->lastaction : "";
                $project->nextaction = isset($nextactiondates->nextaction) ? $nextactiondates->nextaction : "";
                $project->last_meeting_name = isset($lastactiondates->last_meeting_name) ? $lastactiondates->last_meeting_name : "Project Booked";
                $project->next_meeting_name = isset($nextactiondates->next_meeting_name) ? $nextactiondates->next_meeting_name : "";

            } else {
                $project->lastaction = "";
                $project->nextaction = "";
                $project->last_meeting_name = "Project Booked";
                $project->next_meeting_name = "";
            }

            if ($project) {
                if ($project->from_form_id != 0) {
                    $project->form_data = $this->get_form(array(
                        'id' => $project->from_form_id
                    ));
                }

                $project->attachments = $this->get_files($id);
                $project->pinid = isset($tblpins->pinid) ? $tblpins->pinid : 0;
            }

            $assignedOutput = '';

            if (!empty($project->assigned_name)) {

                $full_name = $project->assigned_name;

                $assignedOutput = '<a data-toggle="tooltip" data-title="' . $full_name . '"  href="javascript:void(0)">' . staff_profile_image($project->assigned, array(
                        'staff-profile-image-small'
                    )) . '</a>';
            }

            $project->assigned_name = $assignedOutput;

            $project->is_client = $this->is_client();

            $project->permission = $this->get_project_tool_permission($id);

            $project->vendors = $this->get_project_invites($id, 3);

            $project->collaborators = $this->get_project_invites($id, 4);

            $project->venues = $this->get_project_invites($id, 5);

            return $project;
        }

        return $this->db->get('tblprojects')->result_array();
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/02/2018
     * to get project permission array based on logged in user and project id
     */
    public function get_project_permission($projectid = '')
    {
        $this->load->model('staff_model');
        $this->load->model('roles_model');

        $session_data = get_session_data();
        $staff_user_id = $session_data['staff_user_id'];

        $staff = $this->staff_model->get($staff_user_id);

        $staff_permissions = $staff->permission;
        $permission_ids = array();

        //get all permissions which are assigned to logged in user id
        foreach ($staff_permissions as $staff_permission) {
            array_push($permission_ids, $staff_permission->role_id);
        }

        foreach ($permission_ids as $permission_id) {
            //get all permissions which are visible on project page
            $roles_data[] = $this->roles_model->get_project_role_permissions($permission_id);
        }

        $roles_final_data = array();
        $roles_data = objectToArray($roles_data);
        foreach ($roles_data as $rvalue) {
            foreach ($rvalue as $rpvalue) {
                //prepare final permission array with permissions assigned to logged in user and visible on project page
                if (in_array($rpvalue['permissionid'], array_column($roles_final_data, 'permissionid'))) {
                } else {
                    $roles_final_data[] = $rpvalue;
                }
            }
        }

        return $roles_final_data;
    }

    public function calculate_total_by_project_hourly_rate($seconds, $hourly_rate)
    {
        $hours = seconds_to_time_format($seconds);
        $decimal = sec2qty($seconds);
        $total_money = 0;
        $total_money += ($decimal * $hourly_rate);

        return array(
            'hours' => $hours,
            'total_money' => $total_money
        );
    }

    public function calculate_total_by_task_hourly_rate($tasks)
    {
        $total_money = 0;
        $_total_seconds = 0;

        foreach ($tasks as $task) {
            $seconds = $task['total_logged_time'];
            $_total_seconds += $seconds;
            $total_money += sec2qty($seconds) * $task['hourly_rate'];
        }

        return array(
            'total_money' => $total_money,
            'total_seconds' => $_total_seconds
        );
    }

    public function get_tasks($id, $where = array(), $apply_restrictions = false)
    {
        $has_permission = has_permission('tasks', '', 'view');
        $show_all_tasks_for_project_member = get_option('show_all_tasks_for_project_member');

        if (is_client_logged_in()) {
            $this->db->where('visible_to_client', 1);
        }

        $this->db->select(implode(', ', prefixed_table_fields_array('tblstafftasks')) . ',tblmilestones.name as milestone_name,
        (SELECT SUM(CASE
            WHEN end_time is NULL THEN ' . time() . '-start_time
            ELSE end_time-start_time
            END) FROM tbltaskstimers WHERE task_id=tblstafftasks.id) as total_logged_time
        ');
        $this->db->join('tblmilestones', 'tblmilestones.id = tblstafftasks.milestone', 'left');
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'project');
        if ($apply_restrictions == true) {
            if (!is_client_logged_in() && !$has_permission && $show_all_tasks_for_project_member == 0) {
                $this->db->where('(
                    tblstafftasks.id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid=' . get_staff_user_id() . ')
                    OR tblstafftasks.id IN(SELECT taskid FROM tblstafftasksfollowers WHERE staffid=' . get_staff_user_id() . ')
                    OR is_public = 1
                    OR (addedfrom =' . get_staff_user_id() . ' AND is_added_from_contact = 0)
                    )');
            }
        }
        $this->db->order_by('milestone_order', 'asc');
        $this->db->where($where);
        $tasks = $this->db->get('tblstafftasks')->result_array();

        return $tasks;
    }

    // public function get_files($project_id)
    // {
    //     if (is_client_logged_in()) {
    //         $this->db->where('visible_to_customer', 1);
    //     }
    //     $this->db->where('project_id', $project_id);

    //     return $this->db->get('tblprojectfiles')->result_array();
    // }

    function get_files($projectid)
    {
        $this->db->select('id');
        $this->db->where('(parent = ' . $projectid . ' OR id = ' . $projectid . ')');
        $this->db->where('deleted', 0);
        $related_project_ids = $this->db->get('tblprojects')->result_array();

        $related_project_ids = array_column($related_project_ids, 'id');

        if (!empty($related_project_ids)) {
            $related_project_ids = implode(",", $related_project_ids);
            $this->db->where('rel_id in(' . $related_project_ids . ')');
            $this->db->where('rel_type in("project", "event")');
        } else {
            $this->db->where('rel_id = ' . $projectid);
            $this->db->where('rel_type = "project"');
        }
        $_attachments = $this->db->get('tblfiles')->result_array();
        return $_attachments;
    }

    function get_event_files($projectid)
    {
        $this->db->where('rel_id', $projectid);
        $this->db->where('rel_type', 'event');
        $_attachments = $this->db->get('tblfiles')->result_array();
        return $_attachments;
    }

    public function get_file($id, $project_id = false)
    {
        if (is_client_logged_in()) {
            $this->db->where('visible_to_customer', 1);
        }
        $this->db->where('id', $id);
        $file = $this->db->get('tblprojectfiles')->row();

        if ($file && $project_id) {
            if ($file->project_id != $project_id) {
                return false;
            }
        }

        return $file;
    }

    public function update_file_data($data)
    {
        $this->db->where('id', $data['id']);
        unset($data['id']);
        $this->db->update('tblprojectfiles', $data);
    }

    public function change_file_visibility($id, $visible)
    {
        $this->db->where('id', $id);
        $this->db->update('tblprojectfiles', array(
            'visible_to_customer' => $visible
        ));
    }

    public function change_activity_visibility($id, $visible)
    {
        $this->db->where('id', $id);
        $this->db->update('tblprojectactivity', array(
            'visible_to_customer' => $visible
        ));
    }

    // public function remove_file($id)
    // {

    //     $id = do_action('before_remove_project_file', $id);

    //     $this->db->where('id', $id);
    //     $file = $this->db->get('tblprojectfiles')->row();
    //     if ($file) {
    //         if (empty($file->external)) {
    //             $path = get_upload_path_by_type('project') . $file->project_id . '/';
    //             $fullPath =$path.$file->file_name;
    //             if (file_exists($fullPath)) {

    //                 unlink($fullPath);
    //                 $fname = pathinfo($fullPath, PATHINFO_FILENAME);
    //                 $fext = pathinfo($fullPath, PATHINFO_EXTENSION);
    //                 $thumbPath = $path.$fname.'_thumb.'.$fext;

    //                 if(file_exists($thumbPath)) {
    //                     unlink($thumbPath);
    //                 }
    //             }
    //         }

    //         $this->db->where('id', $id);
    //         $this->db->delete('tblprojectfiles');
    //         $this->log_activity($file->project_id, 'project_activity_project_file_removed', $file->file_name, $file->visible_to_customer);
    //         // Delete discussion comments
    //         $this->_delete_discussion_comments($id, 'file');

    //         if (is_dir(get_upload_path_by_type('project') . $file->project_id)) {
    //             // Check if no attachments left, so we can delete the folder also
    //             $other_attachments = list_files(get_upload_path_by_type('project') . $file->project_id);
    //             if (count($other_attachments) == 0) {
    //                 delete_dir(get_upload_path_by_type('project') . $file->project_id);
    //             }
    //         }

    //         return true;
    //     }

    //     return false;
    // }
    public function remove_file($id)
    {

        $this->db->where('id', $id);
        $file = $this->db->get('tblfiles')->row();
        if ($file) {
            $path = get_upload_path_by_type('project') . $file->rel_id . '/';
            $fullPath = $path . $file->file_name;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            $this->db->where('id', $id);
            $this->db->delete('tblfiles');
            if ($file->rel_type == "project") {
                $this->projects_model->log_project_activity($file->rel_id, 'not_project_activity_attachment_deleted');
            }
            return true;
        }

        return false;
    }

    public function get_project_overview_weekly_chart_data($id, $type = 'this_week')
    {
        $project = $this->get($id);
        $chart = array();

        $has_permission_create = has_permission('projects', '', 'create');
        // If don't have permission for projects create show only bileld time
        if (!$has_permission_create) {
            $timesheets_type = 'total_logged_time_only';
        } else {
            if ($project->billing_type == 2 || $project->billing_type == 3) {
                $timesheets_type = 'billable_unbilled';
            } else {
                $timesheets_type = 'total_logged_time_only';
            }
        }

        $chart['data'] = array();
        $chart['data']['labels'] = array();
        $chart['data']['datasets'] = array();

        $chart['data']['datasets'][] = array(
            'label' => ($timesheets_type == 'billable_unbilled' ? str_replace(':', '', _l('project_overview_billable_hours')) : str_replace(':', '', _l('project_overview_logged_hours'))),
            'data' => array(),
            'backgroundColor' => array(),
            'borderColor' => array(),
            'borderWidth' => 1
        );

        if ($timesheets_type == 'billable_unbilled') {
            $chart['data']['datasets'][] = array(
                'label' => str_replace(':', '', _l('project_overview_unbilled_hours')),
                'data' => array(),
                'backgroundColor' => array(),
                'borderColor' => array(),
                'borderWidth' => 1
            );
        }

        $temp_weekdays_data = array();
        $weeks = array();
        $where_time = '';

        if ($type == 'this_month') {
            $beginThisMonth = date('Y-m-01');
            $endThisMonth = date('Y-m-t 23:59:59');

            $weeks_split_start = date('Y-m-d', strtotime($beginThisMonth));
            $weeks_split_end = date('Y-m-d', strtotime($endThisMonth));

            $where_time = 'start_time BETWEEN ' . strtotime($beginThisMonth) . ' AND ' . strtotime($endThisMonth);
        } elseif ($type == 'last_month') {
            $beginLastMonth = date('Y-m-01', strtotime('-1 MONTH'));
            $endLastMonth = date('Y-m-t 23:59:59', strtotime('-1 MONTH'));

            $weeks_split_start = date('Y-m-d', strtotime($beginLastMonth));
            $weeks_split_end = date('Y-m-d', strtotime($endLastMonth));

            $where_time = 'start_time BETWEEN ' . strtotime($beginLastMonth) . ' AND ' . strtotime($endLastMonth);
        } elseif ($type == 'last_week') {
            $beginLastWeek = date('Y-m-d', strtotime('monday last week'));
            $endLastWeek = date('Y-m-d 23:59:59', strtotime('sunday last week'));
            $where_time = 'start_time BETWEEN ' . strtotime($beginLastWeek) . ' AND ' . strtotime($endLastWeek);
        } else {
            $beginThisWeek = date('Y-m-d', strtotime('monday this week'));
            $endThisWeek = date('Y-m-d 23:59:59', strtotime('sunday this week'));
            $where_time = 'start_time BETWEEN ' . strtotime($beginThisWeek) . ' AND ' . strtotime($endThisWeek);
        }

        if ($type == 'this_week' || $type == 'last_week') {
            foreach (get_weekdays() as $day) {
                array_push($chart['data']['labels'], $day);
            }
            $weekDay = date('w', strtotime(date('Y-m-d H:i:s')));
            $i = 0;
            foreach (get_weekdays_original() as $day) {
                if ($weekDay != "0") {
                    $chart['data']['labels'][$i] = date('d', strtotime($day . ' ' . str_replace('_', ' ', $type))) . ' - ' . $chart['data']['labels'][$i];
                } else {
                    if ($type == 'this_week') {
                        $strtotime = 'last ' . $day;
                        if ($day == 'Sunday') {
                            $strtotime = 'sunday this week';
                        }
                        $chart['data']['labels'][$i] = date('d', strtotime($strtotime)) . ' - ' . $chart['data']['labels'][$i];
                    } else {
                        $strtotime = $day . ' last week';
                        $chart['data']['labels'][$i] = date('d', strtotime($strtotime)) . ' - ' . $chart['data']['labels'][$i];
                    }
                }
                $i++;
            }
        } elseif ($type == 'this_month' || $type == 'last_month') {
            $weeks_split_start = new DateTime($weeks_split_start);
            $weeks_split_end = new DateTime($weeks_split_end);
            $weeks = get_weekdays_between_dates($weeks_split_start, $weeks_split_end);
            $total_weeks = count($weeks);
            for ($i = 1; $i <= $total_weeks; $i++) {
                array_push($chart['data']['labels'], split_weeks_chart_label($weeks, $i));
            }
        }

        $loop_break = ($timesheets_type == 'billable_unbilled') ? 2 : 1;

        for ($i = 0; $i < $loop_break; $i++) {
            $temp_weekdays_data = array();
            // Store the weeks in new variable for each loop to prevent duplicating
            $tmp_weeks = $weeks;


            $color = '3, 169, 244';

            $where = 'task_id IN (SELECT id FROM tblstafftasks WHERE rel_type = "project" AND rel_id = "' . $id . '"';

            if ($timesheets_type != 'total_logged_time_only') {
                $where .= ' AND billable=1';
                if ($i == 1) {
                    $color = '252, 45, 66';
                    $where .= ' AND billed = 0';
                }
            }

            $where .= ')';
            $this->db->where($where_time);
            $this->db->where($where);
            if (!$has_permission_create) {
                $this->db->where('staff_id', get_staff_user_id());
            }
            $timesheets = $this->db->get('tbltaskstimers')->result_array();

            foreach ($timesheets as $t) {
                $total_logged_time = 0;
                if ($t['end_time'] == null) {
                    $total_logged_time = time() - $t['start_time'];
                } else {
                    $total_logged_time = $t['end_time'] - $t['start_time'];
                }

                if ($type == 'this_week' || $type == 'last_week') {
                    $weekday = date('N', $t['start_time']);
                    if (!isset($temp_weekdays_data[$weekday])) {
                        $temp_weekdays_data[$weekday] = 0;
                    }
                    $temp_weekdays_data[$weekday] += $total_logged_time;
                } else {
                    // months - this and last
                    $w = 1;
                    foreach ($tmp_weeks as $week) {
                        $start_time_date = strftime('%Y-%m-%d', $t['start_time']);
                        if (!isset($tmp_weeks[$w]['total'])) {
                            $tmp_weeks[$w]['total'] = 0;
                        }
                        if (in_array($start_time_date, $week)) {
                            $tmp_weeks[$w]['total'] += $total_logged_time;
                        }
                        $w++;
                    }
                }
            }

            if ($type == 'this_week' || $type == 'last_week') {
                ksort($temp_weekdays_data);
                for ($w = 1; $w <= 7; $w++) {
                    $total_logged_time = 0;
                    if (isset($temp_weekdays_data[$w])) {
                        $total_logged_time = $temp_weekdays_data[$w];
                    }
                    array_push($chart['data']['datasets'][$i]['data'], sec2qty($total_logged_time));
                    array_push($chart['data']['datasets'][$i]['backgroundColor'], 'rgba(' . $color . ',0.8)');
                    array_push($chart['data']['datasets'][$i]['borderColor'], 'rgba(' . $color . ',1)');
                }
            } else {
                // loop over $tmp_weeks because the unbilled is shown twice because we auto increment twice
                // months - this and last
                foreach ($tmp_weeks as $week) {
                    $total = 0;
                    if (isset($week['total'])) {
                        $total = $week['total'];
                    }
                    $total_logged_time = $total;
                    array_push($chart['data']['datasets'][$i]['data'], sec2qty($total_logged_time));
                    array_push($chart['data']['datasets'][$i]['backgroundColor'], 'rgba(' . $color . ',0.8)');
                    array_push($chart['data']['datasets'][$i]['borderColor'], 'rgba(' . $color . ',1)');
                }
            }
        }

        return $chart;
    }

    public function get_gantt_data($project_id, $type = 'milestones', $taskStatus = null)
    {
        $type_data = array();
        if ($type == 'milestones') {
            $type_data[] = array(
                'name' => _l('milestones_uncategorized'),
                'id' => 0
            );
            $_milestones = $this->get_milestones($project_id);
            foreach ($_milestones as $m) {
                $type_data[] = $m;
            }
        } elseif ($type == 'members') {
            $type_data[] = array(
                'name' => _l('task_list_not_assigned'),
                'staff_id' => 0
            );
            $_members = $this->get_project_members($project_id);
            foreach ($_members as $m) {
                $type_data[] = $m;
            }
        } else {
            if (!$taskStatus) {
                $statuses = $this->tasks_model->get_statuses();
                foreach ($statuses as $status) {
                    $type_data[] = $status['id'];
                }
            } else {
                $type_data[] = $taskStatus;
            }
        }

        $gantt_data = array();
        $has_permission = has_permission('tasks', '', 'view');
        foreach ($type_data as $data) {
            if ($type == 'milestones') {
                $tasks = $this->get_tasks($project_id, 'milestone=' . $data['id'] . ($taskStatus ? ' AND tblstafftasks.status=' . $taskStatus : ''), true);
                $name = $data['name'];
            } elseif ($type == 'members') {
                if ($data['staff_id'] != 0) {
                    $tasks = $this->get_tasks($project_id, 'tblstafftasks.id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid=' . $data['staff_id'] . ')' . ($taskStatus ? ' AND tblstafftasks.status=' . $taskStatus : ''), true);
                    $name = get_staff_full_name($data['staff_id']);
                } else {
                    $tasks = $this->get_tasks($project_id, 'tblstafftasks.id NOT IN (SELECT taskid FROM tblstafftaskassignees)' . ($taskStatus ? ' AND tblstafftasks.status=' . $taskStatus : ''), true);
                    $name = $data['name'];
                }
            } else {
                $tasks = $this->get_tasks($project_id, array(
                    'status' => $data
                ), true);

                $name = format_task_status($data, false, true);
            }

            if (count($tasks) > 0) {
                $data = array();
                $data['values'] = array();
                $values = array();
                $data['desc'] = $tasks[0]['name'];
                $data['name'] = $name;
                $class = '';
                if ($tasks[0]['status'] == 5) {
                    $class = 'line-throught';
                }

                $values['from'] = strftime('%Y/%m/%d', strtotime($tasks[0]['startdate']));
                $values['to'] = strftime('%Y/%m/%d', strtotime($tasks[0]['duedate']));
                $values['desc'] = $tasks[0]['name'] . ' - ' . _l('task_total_logged_time') . ' ' . seconds_to_time_format($tasks[0]['total_logged_time']);
                $values['label'] = $tasks[0]['name'];
                if ($tasks[0]['duedate'] && date('Y-m-d') > $tasks[0]['duedate'] && $tasks[0]['status'] != 5) {
                    $values['customClass'] = 'ganttRed';
                } elseif ($tasks[0]['status'] == 5) {
                    $values['label'] = ' <i class="fa fa-check"></i> ' . $values['label'];
                    $values['customClass'] = 'ganttGreen';
                }
                $values['dataObj'] = array(
                    'task_id' => $tasks[0]['id']
                );
                $data['values'][] = $values;
                $gantt_data[] = $data;
                unset($tasks[0]);
                foreach ($tasks as $task) {
                    $data = array();
                    $data['values'] = array();
                    $values = array();
                    $class = '';
                    if ($task['status'] == 5) {
                        $class = 'line-throught';
                    }
                    $data['desc'] = $task['name'];
                    $data['name'] = '';

                    $values['from'] = strftime('%Y/%m/%d', strtotime($task['startdate']));
                    $values['to'] = strftime('%Y/%m/%d', strtotime($task['duedate']));
                    $values['desc'] = $task['name'] . ' - ' . _l('task_total_logged_time') . ' ' . seconds_to_time_format($task['total_logged_time']);
                    $values['label'] = $task['name'];
                    if ($task['duedate'] && date('Y-m-d') > $task['duedate'] && $task['status'] != 5) {
                        $values['customClass'] = 'ganttRed';
                    } elseif ($task['status'] == 5) {
                        $values['label'] = ' <i class="fa fa-check"></i> ' . $values['label'];
                        $values['customClass'] = 'ganttGreen';
                    }

                    $values['dataObj'] = array(
                        'task_id' => $task['id']
                    );
                    $data['values'][] = $values;
                    $gantt_data[] = $data;
                }
            }
        }

        return $gantt_data;
    }

    public function calc_milestone_logged_time($project_id, $id)
    {
        $total = array();
        $tasks = $this->get_tasks($project_id, array(
            'milestone' => $id
        ));

        foreach ($tasks as $task) {
            $total[] = $task['total_logged_time'];
        }

        return array_sum($total);
    }

    public function total_logged_time($id)
    {
        $q = $this->db->query('
            SELECT SUM(CASE
                WHEN end_time is NULL THEN ' . time() . '-start_time
                ELSE end_time-start_time
                END) as total_logged_time
            FROM tbltaskstimers
            WHERE task_id IN (SELECT id FROM tblstafftasks WHERE rel_type="project" AND rel_id=' . $id . ')')
            ->row();

        return $q->total_logged_time;
    }

    public function get_milestones($project_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->order_by('milestone_order', 'ASC');
        $milestones = $this->db->get('tblmilestones')->result_array();
        $i = 0;
        foreach ($milestones as $milestone) {
            $milestones[$i]['total_logged_time'] = $this->calc_milestone_logged_time($project_id, $milestone['id']);
            $i++;
        }

        return $milestones;
    }

    public function add_milestone($data)
    {
        $data['due_date'] = to_sql_date($data['due_date']);
        $data['datecreated'] = date('Y-m-d');
        $data['description'] = nl2br($data['description']);

        if (isset($data['description_visible_to_customer'])) {
            $data['description_visible_to_customer'] = 1;
        } else {
            $data['description_visible_to_customer'] = 0;
        }
        $this->db->insert('tblmilestones', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $this->db->where('id', $insert_id);
            $milestone = $this->db->get('tblmilestones')->row();
            $project = $this->get($milestone->project_id);
            if ($project->settings->view_milestones == 1) {
                $show_to_customer = 1;
            } else {
                $show_to_customer = 0;
            }
            $this->log_activity($milestone->project_id, 'project_activity_created_milestone', $milestone->name, $show_to_customer);
            logActivity('Project Milestone Created [ID:' . $insert_id . ']');

            return $insert_id;
        }

        return false;
    }

    public function update_milestone($data, $id)
    {
        $this->db->where('id', $id);
        $milestone = $this->db->get('tblmilestones')->row();
        $data['due_date'] = to_sql_date($data['due_date']);
        $data['description'] = nl2br($data['description']);

        if (isset($data['description_visible_to_customer'])) {
            $data['description_visible_to_customer'] = 1;
        } else {
            $data['description_visible_to_customer'] = 0;
        }

        $this->db->where('id', $id);
        $this->db->update('tblmilestones', $data);
        if ($this->db->affected_rows() > 0) {
            $project = $this->get($milestone->project_id);
            if ($project->settings->view_milestones == 1) {
                $show_to_customer = 1;
            } else {
                $show_to_customer = 0;
            }
            $this->log_activity($milestone->project_id, 'project_activity_updated_milestone', $milestone->name, $show_to_customer);
            logActivity('Project Milestone Updated [ID:' . $id . ']');

            return true;
        }

        return false;
    }

    public function update_task_milestone($data)
    {
        $this->db->where('id', $data['task_id']);
        $this->db->update('tblstafftasks', array(
            'milestone' => $data['milestone_id']
        ));

        foreach ($data['order'] as $order) {
            $this->db->where('id', $order[0]);
            $this->db->update('tblstafftasks', array(
                'milestone_order' => $order[1]
            ));
        }
    }

    public function update_milestone_color($data)
    {
        $this->db->where('id', $data['milestone_id']);
        $this->db->update('tblmilestones', array(
            'color' => $data['color']
        ));
    }

    public function delete_milestone($id)
    {
        $this->db->where('id', $id);
        $milestone = $this->db->get('tblmilestones')->row();
        $this->db->where('id', $id);
        $this->db->delete('tblmilestones');
        if ($this->db->affected_rows() > 0) {
            $project = $this->get($milestone->project_id);
            if ($project->settings->view_milestones == 1) {
                $show_to_customer = 1;
            } else {
                $show_to_customer = 0;
            }
            $this->log_activity($milestone->project_id, 'project_activity_deleted_milestone', $milestone->name, $show_to_customer);
            $this->db->where('milestone', $id);
            $this->db->update('tblstafftasks', array(
                'milestone' => 0
            ));
            logActivity('Project Milestone Deleted [' . $id . ']');

            return true;
        }

        return false;
    }


    /**
     * Added By : Vaidehi
     * Dt : 12/19/2017
     * Add new project to database
     * @param mixed $data project data
     * @return mixed false || projectid
     */
    public function add($data)
    {
        /*echo "<pre>";
        print_r($data);
        die('<----here');*/

        unset($data['pg']);
        unset($data['imagebase64']);
        $contacts = array();
        if (isset($data['assigned'])) {
            $assigned = $data['assigned'];
        }
        unset($data['assigned']);
        /*if ($data['projectcontact'][0] == 'new') {*/
        if (isset($data['contact'])) {
            $contacts = $data['contact'];
        }

        /*echo "<pre>";
        print_r($contacts);
        die('<--here');*/
        $this->load->model('addressbooks_model');
        if (!empty($contacts)) {
            $files = array();
            $newcontactids = array();
            $newcontacts = array();
            $existingclients = array();
            if (isset($_FILES['contact'])) {
                foreach ($_FILES['contact'] as $i => $valarray) {
                    foreach ($valarray as $j => $val) {
                        $files[$j]['profile_image'][$i] = $val['profile_image'];
                    }
                }
            }
            foreach ($contacts as $key => $contactdata) {
                if ($contactdata['contacttype'] == "new") {
                    $newcontacts[] = $contactdata;
                    $imagebase64 = $contactdata['imagebase64'];
                    unset($contactdata['imagebase64']);
                    unset($contactdata['contacttype']);
                    unset($contactdata['id']);
                    if (!empty($contactdata['firstname']) && !empty($contactdata['lastname']) && !empty($contactdata['email'][0]['email'])) {
                        $newcontactid = $this->addressbooks_model->add($contactdata);
                        if (isset($files[$key])) {
                            $file = $files[$key];
                            $file['imagebase64'] = $imagebase64;
                            handle_multiple_addressbook_profile_image_upload($newcontactid, $file);
                        }
                        $newcontactids[] = $newcontactid;
                    }
                } elseif ($contactdata['contacttype'] == "existing") {
                    $existingclients[] = $contactdata;
                }
            }
        }
        /*echo "<pre>";
        print_r($existingclients);
        die();*/
        /*}*/
        /*if ($data['projectcontact'][0] == 'existing') {
            $existingclientid = $data['clients'];
        }*/
        unset($data['clients']);
        unset($data['contact']);
        if (isset($data['custom_contact_date']) || isset($data['custom_contact_date'])) {
            if (isset($data['contacted_today'])) {
                $data['lastcontact'] = date('Y-m-d H:i:s');
                unset($data['contacted_today']);
            } else {
                $data['lastcontact'] = to_sql_date($data['custom_contact_date'], true);
            }
        }

        if (isset($data['is_public']) && ($data['is_public'] == 1 || $data['is_public'] === 'on')) {
            $data['is_public'] = 1;
        } else {
            $data['is_public'] = 0;
        }

        if (!isset($data['country']) || isset($data['country']) && $data['country'] == '') {
            $data['country'] = 0;
        }

        if (isset($data['custom_contact_date'])) {
            unset($data['custom_contact_date']);
        }

        $brandid = get_user_session();

        $data['brandid'] = $brandid;

        /*if (isset($existingclientid) && $existingclientid != "") {
            $data['project_contactid'] = $existingclientid;
        } else {
            $data['project_contactid'] = $newcontactid;
        }*/

        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();
        $data['createdby'] = get_staff_user_id();

        $data = do_action('before_add_project', $data);

        $tags = '';
        if (isset($data['tags'])) {
            $tags = $data['tags'];
            unset($data['tags']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if ($data['eventstartdatetime'] != '') {

            $eventstartdatetime = str_replace('/', '-', $data['eventstartdatetime']);
            if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $eventstartdatetime)) {
                $data['eventstartdatetime'] = ((isset($data['eventstartdatetime']) && !empty($data['eventstartdatetime'])) ? date("Y-m-d H:i:s", strtotime($data['eventstartdatetime'])) : "");
            } else {
                if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                    $data['eventstartdatetime'] = ((isset($data['eventstartdatetime']) && !empty($data['eventstartdatetime'])) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $data['eventstartdatetime']))) : "");
                } else {
                    $data['eventstartdatetime'] = ((isset($data['eventstartdatetime']) && !empty($data['eventstartdatetime'])) ? date("Y-m-d H:i:s", strtotime($data['eventstartdatetime'])) : "");
                }
            }
        }

        if ($data['eventenddatetime'] != '') {
            $eventenddatetime = str_replace('/', '-', $data['eventenddatetime']);
            if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $eventenddatetime)) {
                $data['eventstartdatetime'] = ((isset($data['eventenddatetime']) && !empty($data['eventenddatetime'])) ? date("Y-m-d H:i:s", strtotime($data['eventenddatetime'])) : "");
            } else {
                if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                    $data['eventenddatetime'] = ((isset($data['eventenddatetime']) && !empty($data['eventenddatetime'])) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $data['eventenddatetime']))) : "");
                } else {
                    $data['eventenddatetime'] = ((isset($data['eventenddatetime']) && !empty($data['eventenddatetime'])) ? date("Y-m-d H:i:s", strtotime($data['eventenddatetime'])) : "");
                }
            }
        }
        $data['project_profile_image'] = $_FILES['project_profile_image']['name'];
        unset($data['projectcontact']);
        unset($data['project_profile_image']);
        unset($data['country']);
        //save new project
        $this->db->insert('tblprojects', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            $this->log_activity($insert_id, 'project_activity_created');
            //create sub project for main project
            if ($data['parent'] <= 0) {
                $subproject_sql = 'INSERT INTO `tblprojects`(`parent`, `name`, `project_profile_image`, `title`, `assigned`, `dateadded`, `from_form_id`, `status`, `source`, `project_contactid`, `lastcontact`, `dateassigned`, `last_status_change`, `addedfrom`, `leadorder`, `date_converted`, `eventtypeid`, `eventstartdatetime`, `eventenddatetime`, `eventtimezone`, `budget`, `sourcedetails`, `comments`, `brandid`, createdby) SELECT ' . $insert_id . ', `name`, `project_profile_image`, `title`, `assigned`, `dateadded`, `from_form_id`, `status`, `source`, `project_contactid`, `lastcontact`, `dateassigned`, `last_status_change`, `addedfrom`, `leadorder`, `date_converted`, `eventtypeid`, `eventstartdatetime`, `eventenddatetime`, `eventtimezone`, `budget`, `sourcedetails`, `comments`, `brandid`, createdby FROM tblprojects WHERE `id` = ' . $insert_id;
                $this->db->query($subproject_sql);
                $subproject_id = $this->db->insert_id();

                if ($subproject_id > 0) {
                    $this->log_activity($insert_id, 'project_activity_created');
                }
            }

            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }

            //create project contact for main project and/or sub-project

            if (isset($existingclients) && !empty($existingclients)) {
                $pdet = $this->get($insert_id);
                $session_data = get_session_data();
                if (isset($session_data['is_sido_admin'])) {
                    $is_sido_admin = $session_data['is_sido_admin'];
                    $is_admin = $session_data['is_admin'];
                } else {
                    $is_sido_admin = 1;
                    $is_admin = 1;
                }
                if ($is_sido_admin == 0 && $is_admin == 0) {
                    $email_signature = get_brand_option('email_signature');
                } else {
                    $email_signature = get_option('email_signature');
                }
                $vendor_name = get_staff_full_name();

                foreach ($existingclients as $existingclient) {
                    $existingclientid = $existingclient['id'];
                    $contactdata = $this->addressbooks_model->get($existingclientid);
                    $satff_email = $contactdata->email[0]['email'];
                    $where = array('email' => $satff_email, 'deleted' => 0);
                    $staff_exist = $this->db->where($where)->get('tblstaff')->row();
                    $firstname = $contactdata->firstname;
                    $password = $this->randomPassword();
                    if (isset($existingclient['isclient']) && $existingclient['isclient'] == 1) {

                        if (empty($staff_exist)) {
                            $query = $this->db->query('SELECT packageid FROM tblpackages WHERE name = "Free Package"');
                            $package = $query->row();
                            $staffdata = [];
                            $staffdata['firstname'] = $contactdata->firstname;
                            $staffdata['lastname'] = $contactdata->lastname;
                            $staffdata['email'] = $satff_email;
                            $staffdata['password'] = $password;
                            $staffdata['random_pass'] = $password;
                            $staffdata['created_by'] = $this->session->userdata['staff_user_id'];
                            $staffdata['datecreated'] = date('Y-m-d H:i:s');
                            $staffdata['active'] = 0;
                            $staffdata['facebook'] = null;
                            $staffdata['twitter'] = null;
                            $staffdata['google'] = null;
                            $staffdata['profile_image'] = $contactdata->profile_image;
                            $staffdata['brandname'] = $firstname;
                            $staffdata['brandtype'] = 1;
                            $staffdata['is_not_staff'] = 1;
                            $staffdata['user_type'] = 2;
                            /*$staffdata['permission'] = array(array('role' => 4));*/
                            $staffdata['packagetype'] = (isset($package->packageid) ? $package->packageid : 2);
                            /*$this->load->model('staff_model');
                            $this->staff_model->add($staffdata);*/
                            $this->load->model('register_model');
                            $this->register_model->saveclient($staffdata, 'invite', 'client');
                            logActivity('New Client Created [Email Address:' . $satff_email . ' for Project: ' . $insert_id . 'staffdata IP:' . $this->input->ip_address() . ']');
                            $where = array('email' => $satff_email, 'deleted' => 0);
                            $staff_det = $this->db->where($where)->get('tblstaff')->row();
                        } else {
                            $staff_det = $staff_exist;
                            /*$roleiddata = get_role('client');
                            $roledata = array();
                            $roledata['role_id'] = $roleiddata;
                            $roledata['user_id'] = $staff_det->staffid;
                            $this->db->insert('tblroleuserteam', $roledata);*/
                        }
                        $project_contact = array();
                        if ($pdet->parent == 0) {
                            $project_contact['projectid'] = $insert_id;
                        } else {
                            $project_contact['projectid'] = 0;
                            $project_contact['eventid'] = $insert_id;
                        }
                        $project_contact['projectid'] = $insert_id;
                        $project_contact['contactid'] = $staff_det->staffid;
                        $project_contact['brandid'] = get_user_session();
                        //for client
                        $project_contact['isvendor'] = 0;
                        $project_contact['iscollaborator'] = 0;
                        $project_contact['isclient'] = 1;

                        $this->db->insert('tblprojectcontact', $project_contact);

                        $staff_brand = [];
                        $staff_brand['active'] = 1;
                        $staff_brand['staffid'] = $staff_det->staffid;
                        $staff_brand['brandid'] = get_user_session();
                        $this->db->insert('tblstaffbrand', $staff_brand);
                        $this->db->where('staffid', $staff_det->staffid);
                        $this->db->update('tblstaff', array('active' => 1));

                        /**
                         * Added By : Masud
                         * Dt : 03/23/2018
                         * prefill dashboard values
                         */
                        $dashboard_data = array();
                        $dashboard_data['staffid'] = $staff_det->staffid;

                        $dashboard_data['order'] = '[{"widget_name":"getting_started","order":0},{"widget_name":"lead_pipeline","order":1},{"widget_name":"calendar","order":2},{"widget_name":"pinned_item","order":3},{"widget_name":"quick_link","order":4},{"widget_name":"upcoming_project","order":5},{"widget_name":"contacts","order":6},{"widget_name":"messages","order":7},{"widget_name":"task_list","order":8}]';
                        $dashboard_data['is_visible'] = 1;
                        $dashboard_data['brandid'] = get_user_session();
                        $dashboard_data['dateadded'] = date('Y-m-d H:i:s');
                        $dashboard_data['addedby'] = $staff_det->staffid;
                        $this->db->insert('  tbldashboard_settings', $dashboard_data);

                        /*$event = '<h3>Event details below:</h3><br/><br/>';*/
                        $event = 'Event: ' . $pdet->name . '<br/><br/>';
                        $event .= 'Type: ' . $pdet->eventtypename . '<br/><br/>';
                        $event .= 'From: ' . _dt($pdet->eventstartdatetime) . '<br/><br/>';
                        $event .= 'To: ' . _dt($pdet->eventenddatetime) . '<br/><br/>';

                        $message = '<h2><span style="font-size: 14pt;">Hi ' . $firstname . '</span>,</h2> Event details below:<br/><br/>' . $event . '<br/><br/>Please click on the <a href="' . admin_url("projects/dashboard/" . $insert_id) . '">View Event</a> to view the details. You can login with following credentials: <br/><br/>Email: ' . $satff_email . '<br/><br/>Password: ' . $password . '<br/><br/> If you have any questions or concerns, please do not hesitate to contact at:<br/><br/>Name: ' . $pdet->assigned_name . '<br/><br/>Phone: ' . $pdet->assigned_phone . '<br/><br/>Email: ' . $pdet->assigned_email . '<br/><br/>Sincerely,<br/><br/>' . $vendor_name . ' ' . $email_signature;
                        $this->emails_model->send_simple_email($satff_email, "Your Project Created", $message);
                    } /*else {*/
                    $project_contact = array();
                    if ($pdet->parent == 0) {
                        $project_contact['projectid'] = $insert_id;
                    } else {
                        $project_contact['projectid'] = 0;
                        $project_contact['eventid'] = $insert_id;
                    }
                    $project_contact['projectid'] = $insert_id;
                    $project_contact['contactid'] = $existingclientid;
                    $project_contact['brandid'] = get_user_session();
                    //for client
                    $project_contact['isvendor'] = 0;
                    $project_contact['iscollaborator'] = 0;
                    $project_contact['isclient'] = 0;

                    $this->db->insert('tblprojectcontact', $project_contact);
                    /*}*/
                }
            }
            if (count($newcontactids) > 0) {
                foreach ($newcontactids as $newcontactid) {
                    $projectcontact = array();
                    if ($data['parent'] > 0) {
                        $projectcontact['eventid'] = $insert_id;
                    } else {
                        $projectcontact['projectid'] = $insert_id;
                    }
                    $projectcontact['contactid'] = $newcontactid;
                    $brandid = get_user_session();

                    $projectcontact['brandid'] = $brandid;

                    $this->db->where('projectid', $insert_id);
                    $this->db->where('contactid', $projectcontact['contactid']);
                    $this->db->where('brandid', $brandid);
                    $projectcontacts = $this->db->get('tblprojectcontact')->row();

                    if (count($projectcontacts) <= 0) {
                        $this->db->insert('tblprojectcontact', $projectcontact);
                    }
                }
            }

            //save project contact
            if (isset($subproject_id)) {
                $sub_contacts_sql = 'INSERT INTO `tblprojectcontact`(`eventid`, `contactid`, `brandid`) SELECT ' . $subproject_id . ', `contactid`, `brandid` FROM tblprojectcontact WHERE `projectid` = ' . $insert_id;
                $this->db->query($sub_contacts_sql);
            }

            if (isset($assigned) && !empty($assigned)) {
                foreach ($assigned as $assignee) {
                    $assignData = array();
                    $assignData['projectid'] = $insert_id;
                    $assignData['assigned'] = $assignee;
                    $this->db->insert('tblstaffprojectassignee', $assignData);
                    $this->project_assigned_member_notification($insert_id, $assignee);
                    $this->project_new_created_notification($insert_id, $assignee);
                }
            }
            /*$this->project_new_created_notification($insert_id, $data['assigned']);
            $this->project_assigned_member_notification($insert_id, $data['assigned']);*/
            do_action('project_created', $insert_id);

            /**
             * Added By : Masud
             * Dt : 10/26/2018
             * Create Client Login
             * @param mixed $data project data
             * @return mixed false || projectid
             */
            if (isset($newcontacts) && !empty($newcontacts)) {

                $pdet = $this->get($insert_id);
                $session_data = get_session_data();
                if (isset($session_data['is_sido_admin'])) {
                    $is_sido_admin = $session_data['is_sido_admin'];
                    $is_admin = $session_data['is_admin'];
                } else {
                    $is_sido_admin = 1;
                    $is_admin = 1;
                }
                if ($is_sido_admin == 0 && $is_admin == 0) {
                    $email_signature = get_brand_option('email_signature');
                } else {
                    $email_signature = get_option('email_signature');
                }
                $vendor_name = get_staff_full_name();
                foreach ($newcontacts as $key => $contactdata) {
                    if (isset($contactdata['isclient']) && $contactdata['isclient'] == 1) {
                        $client_email = $contactdata['email'][0]['email'];

                        $where = array('email' => $client_email, 'deleted' => 0);
                        $staff_exist = $this->db->where($where)->get('tblstaff')->row();
                        if (empty($staff_exist)) {
                            $firstname = $contactdata['firstname'];
                            $query = $this->db->query('SELECT packageid FROM tblpackages WHERE name = "Free Package"');
                            $package = $query->row();

                            //generate random password
                            $password = $this->randomPassword();

                            $staffdata = [];
                            $staffdata['firstname'] = $contactdata['firstname'];
                            $staffdata['lastname'] = $contactdata['lastname'];
                            $staffdata['email'] = $client_email;
                            $staffdata['password'] = $password;
                            $staffdata['random_pass'] = $password;
                            $staffdata['created_by'] = $this->session->userdata['staff_user_id'];
                            $staffdata['datecreated'] = date('Y-m-d H:i:s');
                            $staffdata['active'] = 0;
                            $staffdata['email_signature'] = "";
                            $staffdata['facebook'] = null;
                            $staffdata['twitter'] = null;
                            $staffdata['google'] = null;
                            $staffdata['brandname'] = $firstname;
                            $staffdata['brandtype'] = 1;
                            $staffdata['is_not_staff'] = 1;
                            $staffdata['user_type'] = 2;
                            /*$staffdata['permission'] = array(array('role' => 4));*/
                            $staffdata['packagetype'] = (isset($package->packageid) ? $package->packageid : 2);
                            /*$this->load->model('staff_model');
                            $this->staff_model->add($staffdata);*/
                            $this->load->model('register_model');
                            $this->register_model->saveclient($staffdata, 'invite', 'client');
                            logActivity('New Client Created [Email Address:' . $client_email . ' for project: ' . $insert_id . 'staffdata IP:' . $this->input->ip_address() . ']');
                            $where = array('email' => $client_email, 'deleted' => 0);
                            $staff_det = $this->db->where($where)->get('tblstaff')->row();
                        } else {
                            $staff_det = $staff_exist;
                            /*$roleiddata = get_role('client');
                            $roledata = array();
                            $roledata['role_id'] = $roleiddata;
                            $roledata['user_id'] = $staff_det->staffid;
                            $this->db->insert('tblroleuserteam', $roledata);*/
                        }
                        $project_contact = [];
                        if ($pdet->parent == 0) {
                            $project_contact['projectid'] = $insert_id;
                        } else {
                            $project_contact['projectid'] = 0;
                            $project_contact['eventid'] = $insert_id;
                        }
                        $project_contact['projectid'] = $insert_id;
                        $project_contact['contactid'] = $staff_det->staffid;
                        $project_contact['brandid'] = get_user_session();
                        //for client
                        $project_contact['isvendor'] = 0;
                        $project_contact['iscollaborator'] = 0;
                        $project_contact['isclient'] = 1;
                        $this->db->insert('tblprojectcontact', $project_contact);

                        $staff_brand = [];
                        $staff_brand['active'] = 1;
                        $staff_brand['staffid'] = $staff_det->staffid;
                        $staff_brand['brandid'] = get_user_session();
                        $this->db->insert('tblstaffbrand', $staff_brand);
                        $this->db->where('staffid', $staff_det->staffid);
                        $this->db->update('tblstaff', array('active' => 1));

                        /**
                         * Added By : Masud
                         * Dt : 03/23/2018
                         * prefill dashboard values
                         */
                        $dashboard_data = array();
                        $dashboard_data['staffid'] = $staff_det->staffid;
                        $dashboard_data['widget_type'] = 'upcoming_project,pinned_item,calendar,weather,favourite,quick_link,message,getting_started,task_list,contacts,messages';
                        $dashboard_data['quick_link_type'] = 'project,message,task_due,meeting,invite';
                        $dashboard_data['order'] = '[{"widget_name":"getting_started","order":0},{"widget_name":"lead_pipeline","order":1},{"widget_name":"calendar","order":2},{"widget_name":"pinned_item","order":3},{"widget_name":"quick_link","order":4},{"widget_name":"upcoming_project","order":5},{"widget_name":"contacts","order":6},{"widget_name":"messages","order":7},{"widget_name":"task_list","order":8}]';
                        $dashboard_data['is_visible'] = 1;
                        $dashboard_data['brandid'] = get_user_session();
                        $dashboard_data['dateadded'] = date('Y-m-d H:i:s');
                        $dashboard_data['addedby'] = $staff_det->staffid;
                        $this->db->insert('  tbldashboard_settings', $dashboard_data);

                        /*$event = '<h3>Event details below:</h3><br/><br/>';*/
                        $event = 'Event: ' . $pdet->name . '<br/><br/>';
                        $event .= 'Type: ' . $pdet->eventtypename . '<br/><br/>';
                        $event .= 'From: ' . _dt($pdet->eventstartdatetime) . '<br/><br/>';
                        $event .= 'To: ' . _dt($pdet->eventenddatetime) . '<br/><br/>';

                        $message = '<h2><span style="font-size: 14pt;">Hi ' . $firstname . '</span>,</h2> Event details below:<br/><br/>' . $event . '<br/><br/>Please click on the <a href="' . admin_url("projects/dashboard/" . $insert_id) . '">View Event</a> to view the details. You can login with following credentials: <br/><br/>Email: ' . $client_email . '<br/><br/>Password: ' . $password . '<br/><br/> If you have any questions or concerns, please do not hesitate to contact at:<br/><br/>Name: ' . $pdet->assigned_name . '<br/><br/>Phone: ' . $pdet->assigned_phone . '<br/><br/>Email: ' . $pdet->assigned_email . '<br/><br/>Sincerely,<br/><br/>' . $vendor_name . ' ' . $email_signature;
                        $this->emails_model->send_simple_email($client_email, "Your Project Created", $message);

                    }/*else{

                        $newcontactid = $this->addressbooks_model->add($contactdata);
                        if(isset($files) && !empty($files)){
                            $file = $files[$key];
                            handle_multiple_addressbook_profile_image_upload($newcontactid, $file);
                        }
                    }*/
                }
            }

            return $insert_id;
        }

        return false;
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/20/2017
     * Update project
     * @param array $data lead data
     * @param mixed $id leadid
     * @return boolean
     */
    public function update($data, $id)
    {
        unset($data['pg']);
        unset($data['imagebase64']);
        if (isset($data['assigned'])) {
            $assigned = $data['assigned'];
        }
        unset($data['assigned']);
        $current_project_data = $this->get($id);
        $current_status = $this->get_project_status($current_project_data->status);

        if ($current_status) {
            $current_status_id = $current_status->id;
            $current_status = $current_status->name;
        } else {
            if ($current_project_data->junk == 1) {
                $current_status = _l('project_junk');
            } elseif ($current_project_data->lost == 1) {
                $current_status = _l('project_junk');
            } else {
                $current_status = '';
            }
            $current_status_id = 0;
        }

        $affectedRows = 0;

        if (isset($data['lastcontact']) && $data['lastcontact'] == '' || isset($data['lastcontact']) && $data['lastcontact'] == null) {
            $data['lastcontact'] = null;
        } elseif (isset($data['lastcontact'])) {
            $data['lastcontact'] = to_sql_date($data['lastcontact'], true);
        }

        if (isset($data['tags'])) {
            if (handle_tags_save($data['tags'], $id, 'lead')) {
                $affectedRows++;
            }
            unset($data['tags']);
        }

        /**
         * Added By : Vaidehi
         * Dt : 10/30/2017
         * to save extra form fields in db
         */
        $brandid = get_user_session();

        $data['brandid'] = $brandid;
        //$data['description'] = nl2br($data['description']);
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $data['updatedby'] = get_staff_user_id();

        if ($data['eventstartdatetime'] != '') {
            $eventstartdatetime = str_replace('/', '-', $data['eventstartdatetime']);
            if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $eventstartdatetime)) {
                $data['eventstartdatetime'] = ((isset($data['eventstartdatetime']) && !empty($data['eventstartdatetime'])) ? date("Y-m-d H:i", strtotime($data['eventstartdatetime'])) : "");
            } else {
                if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                    $data['eventstartdatetime'] = ((isset($data['eventstartdatetime']) && !empty($data['eventstartdatetime'])) ? date("Y-m-d H:i", strtotime(str_replace('/', '-', $data['eventstartdatetime']))) : "");
                } else {
                    $data['eventstartdatetime'] = ((isset($data['eventstartdatetime']) && !empty($data['eventstartdatetime'])) ? date("Y-m-d H:i", strtotime($data['eventstartdatetime'])) : "");
                }
            }

        }

        if ($data['eventenddatetime'] != '') {
            $eventenddatetime = str_replace('/', '-', $data['eventenddatetime']);
            if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $eventenddatetime)) {
                $data['eventenddatetime'] = ((isset($data['eventenddatetime']) && !empty($data['eventenddatetime'])) ? date("Y-m-d H:i", strtotime($data['eventstartdatetime'])) : "");
            } else {
                if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                    $data['eventenddatetime'] = ((isset($data['eventenddatetime']) && !empty($data['eventenddatetime'])) ? date("Y-m-d H:i", strtotime(str_replace('/', '-', $data['eventenddatetime']))) : "");
                } else {
                    $data['eventenddatetime'] = ((isset($data['eventenddatetime']) && !empty($data['eventenddatetime'])) ? date("Y-m-d H:i", strtotime($data['eventenddatetime'])) : "");
                }
            }
            // $eventenddatetime = date('Y-m-d H:i',strtotime($data['eventenddatetime']));
            // $data['eventenddatetime']  = $eventenddatetime;
        }

        $data['project_profile_image'] = (isset($_FILES['project_profile_image']['name']) ? $_FILES['project_profile_image']['name'] : $current_project_data->project_profile_image);

        unset($data['contact']);
        unset($data['project_profile_image']);

        if (isset($data['address'])) {
            $address = array_filter($data['address'], function ($var) {
                return ($var['locality'] != '' || $var['administrative_area_level_1'] != '' || $var['street_number'] != '' || $var['route'] != '' || $var['postal_code'] != '');
            });
            unset($data['address']);
        }

        if (isset($address)) {
            foreach ($address as $a) {
                $data['address'] = $a['street_number'];
                $data['address2'] = $a['route'];
                $data['city'] = $a['locality'];
                $data['state'] = $a['administrative_area_level_1'];
                $data['zip'] = $a['postal_code'];
                $data['country'] = $a['country'];
            }
        }

        $this->db->where('id', $id);
        $this->db->update('tblprojects', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if (isset($data['status']) && $current_status_id != $data['status']) {

                $this->db->where('id', $id);
                $this->db->update('tblprojects', array(
                    'last_status_change' => date('Y-m-d H:i:s')
                ));

                $new_status_name = $this->get_project_status($data['status'])->name;

                $this->project_status_changed_notification($id, $new_status_name);

                $additional_data = serialize(array(
                    $current_status,
                    $new_status_name,
                    get_staff_full_name()
                ));

                $this->log_activity($id, 'not_project_status_updated', $additional_data);

                do_action('project_status_changed', array('project_id' => $id, 'old_status' => $current_status_id, 'new_status' => $data['status']));

                do_action('after_update_project', $id);
            }

            if (isset($assigned) && !empty($assigned)) {
                $assignedusers = $this->get_project_assignee($id);
                foreach ($assigned as $assignee) {
                    if (!in_array($assignee, $assignedusers)) {
                        $assignData = array();
                        $assignData['projectid'] = $id;
                        $assignData['assigned'] = $assignee;
                        $this->db->insert('tblstaffprojectassignee', $assignData);
                        $this->project_assigned_member_notification($id, $assignee);
                    }
                }
                foreach ($assignedusers as $assignee) {
                    if (!in_array($assignee, $assigned)) {
                        $this->db->where('projectid', $id);
                        $this->db->where('assigned', $assignee);
                        $this->db->delete('tblstaffprojectassignee');
                    }
                }
            }

            return true;
        }

        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    function get_project_assignee($id)
    {
        $this->db->select('assigned');
        $this->db->where('projectid', $id);
        $assignee = $this->db->get('tblstaffprojectassignee')->result_array();
        $assignee = array_map('current', $assignee);
        return $assignee;
    }

    function get_project_contact($id)
    {
        $this->db->select('contactid');
        $this->db->where('projectid', $id);
        $this->db->where('(isvendor=1 OR iscollaborator=1 OR isclient=1)');
        $assignee = $this->db->get('tblprojectcontact')->result_array();
        $assignee = array_map('current', $assignee);
        return $assignee;
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/22/2017
     * Update canban lead status when drag and drop
     * @param array $data lead data
     * @return boolean
     */
    public function update_project_status($data)
    {
        $this->db->select('status');
        $this->db->where('id', $data['projectid']);
        $_old = $this->db->get('tblprojects')->row();

        $old_status = '';

        if ($_old) {
            $old_status = $this->get_project_status($_old->status);
            if ($old_status) {
                $old_status = $old_status->name;
            }
        }

        $affectedRows = 0;
        $current_status = $this->get_project_status($data['status'])->name;

        $this->db->where('id', $data['projectid']);
        $this->db->update('tblprojects', array(
            'status' => $data['status']
        ));

        $_log_message = '';

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if ($current_status != $old_status && $old_status != '') {
                $_log_message = 'not_project_activity_status_updated';
                $additional_data = serialize(array(
                    $old_status,
                    $current_status,
                    get_staff_full_name()
                ));

                do_action('after_update_project', $data['projectid']);
            }

            $this->db->where('id', $data['projectid']);
            $this->db->update('tblprojects', array(
                'last_status_change' => date('Y-m-d H:i:s')
            ));
        }
        if (isset($data['order'])) {
            foreach ($data['order'] as $order_data) {
                $this->db->where('id', $order_data[0]);
                $this->db->update('tblprojects', array(
                    'leadorder' => $order_data[1]
                ));
            }
        }
        if ($affectedRows > 0) {
            if ($_log_message == '') {
                return true;
            }
            $this->project_status_changed_notification($data['projectid'], $current_status);
            $this->log_activity($data['projectid'], 'not_project_status_updated', $additional_data);

            return true;
        }

        return false;
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/20/2017
     * For Pin/Unpin Projects
     */
    public function pinproject($project_id)
    {
        $session_data = get_session_data();
        $user_id = $session_data['staff_user_id'];

        $pinexist = $this->db->select('pinid')->from('tblpins')->where('pintype = "Project" AND pintypeid=' . $project_id . ' AND userid=' . $user_id)->get()->row();

        if (!empty($pinexist)) {
            $this->db->where('userid', $user_id);
            $this->db->where('pintypeid', $project_id);
            $this->db->where('pintype', "Project");
            $this->db->delete('tblpins');
            return 0;
        } else {
            $this->db->insert('tblpins', array(
                'pintype' => "Project",
                'pintypeid' => $project_id,
                'userid' => $user_id
            ));
            return 1;
        }
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/20/2017
     * For Project Status change
     */
    public function statuschange($project_id, $status_id)
    {
        $this->db->select('status');
        $this->db->where('id', $project_id);
        $_old = $this->db->get('tblprojects')->row();

        $old_status = '';

        if ($_old) {
            $old_status = $this->get_project_status($_old->status);
            if ($old_status) {
                $old_status = $old_status->name;
            }
        }

        $affectedRows = 0;
        $current_status = $this->get_project_status($status_id)->name;

        $this->db->where('id', $project_id);
        $this->db->update('tblprojects', array(
            'status' => $status_id
        ));

        $_log_message = '';

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if ($current_status != $old_status && $old_status != '') {
                $_log_message = 'not_project_status_updated';
                $additional_data = serialize(array(
                    $old_status,
                    $current_status,
                    get_staff_full_name()
                ));

                $this->project_status_changed_notification($project_id, $current_status);
                do_action('project_status_changed', array('project_id' => $project_id, 'old_status' => $old_status, 'new_status' => $current_status));
            }
            $this->db->where('id', $project_id);
            $this->db->update('tblprojects', array(
                'last_status_change' => date('Y-m-d H:i:s')
            ));
        }
        if ($affectedRows > 0) {
            if ($_log_message == '') {
                return true;
            }
            $this->log_activity($project_id, 'not_project_status_updated', $additional_data);
        }
    }

    public function project_assigned_member_notification($project_id, $assigned, $integration = false)
    {
        if ((!empty($assigned) && $assigned != 0)) {
            if ($integration == false) {
                if ($assigned == get_staff_user_id()) {
                    return false;
                }
            }

            $name = $this->db->select('name')->from('tblprojects')->where('id', $project_id)->get()->row()->name;

            $notification_data = array(
                'description' => ($integration == false) ? 'not_assigned_project_to_you' : 'not_project_assigned_from_form',
                'brandid' => get_user_session(),
                'touserid' => $assigned,
                'eid' => $project_id,
                'not_type' => 'projects',
                'link' => 'projects/dashboard/' . $project_id,
                'additional_data' => ($integration == false ? serialize(array(
                    $name
                )) : serialize(array()))
            );

            if (add_notification($notification_data)) {
                pusher_trigger_notification(array($assigned));
            }

            $this->db->where('staffid', $assigned);
            $email = $this->db->get('tblstaff')->row()->email;

            $this->load->model('emails_model');
            $merge_fields = array();
            $merge_fields = array_merge($merge_fields, get_lead_merge_fields($project_id));
            $this->emails_model->send_email_template('new-project-assigned', $email, $merge_fields);

            $this->db->where('id', $project_id);
            $this->db->update('tblprojects', array(
                'dateassigned' => date('Y-m-d')
            ));

            $not_additional_data = array(
                // '<a href="' . admin_url('profile/' . $assigned) . '" target="_blank">' . get_staff_full_name($assigned) . '</a>'
                '<a href="' . admin_url('staff/member/' . $assigned) . '" target="_blank">' . get_staff_full_name($assigned) . '</a>',
                get_staff_full_name()
            );

            if ($integration == true) {
                unset($not_additional_data[0]);
                array_values(($not_additional_data));
            }

            $not_additional_data = serialize($not_additional_data);

            $not_desc = ($integration == false ? 'not_project_activity_assigned_to' : 'not_project_activity_assigned_from_form');

            $this->log_activity($project_id, $not_desc, $not_additional_data);
        }
    }

    /**
     * Added By : Masud
     * Dt : 04/01/2018
     * Get project statuses
     * @param mixed $id status id
     * @return mixed      object if id passed else array
     */

    public function project_new_created_notification($project_id, $assigned, $integration = false)
    {
        $name = $this->db->select('name')->from('tblprojects')->where('id', $project_id)->get()->row()->name;
        if ($assigned == "") {
            $assigned = 0;
        }

        $notification_data = array(
            'description' => ($integration == false) ? 'not_new_project_is_created' : 'not_new_project_is_created_from_form',
            'brandid' => get_user_session(),
            'touserid' => '',
            'eid' => $project_id,
            'not_type' => "projects",
            'link' => 'projects/dashboard/' . $project_id,
            'additional_data' => ($integration == false ? serialize(array(
                $name
            )) : serialize(array()))
        );

        if (add_notification($notification_data)) {
            pusher_trigger_notification(array($assigned));
        }

        /*$this->db->where('staffid', $assigned);
        $email = $this->db->get('tblstaff')->row()->email;

        $this->load->model('emails_model');
        $merge_fields = array();
        $merge_fields = array_merge($merge_fields, get_lead_merge_fields($project_id));
        $this->emails_model->send_email_template('new-project-assigned', $email, $merge_fields);

        $this->db->where('id', $project_id);
        $this->db->update('tblprojects', array(
            'dateassigned' => date('Y-m-d')
        ));

        $not_additional_data = array(
            get_staff_full_name(),
            // '<a href="' . admin_url('profile/' . $assigned) . '" target="_blank">' . get_staff_full_name($assigned) . '</a>'
              '<a href="javascript:void(0)" >' . get_staff_full_name($assigned) . '</a>'
        );

        if ($integration == true) {
            unset($not_additional_data[0]);
            array_values(($not_additional_data));
        }

        $not_additional_data = serialize($not_additional_data);

        $not_desc = ($integration == false ? 'not_project_activity_assigned_to' : 'not_project_activity_assigned_from_form');
        $this->log_activity($project_id, $not_desc, $not_additional_data);*/
    }

    /**
     * Added By : Masud
     * Dt : 04/01/2018
     * Get project statuses
     * @param mixed $id status id
     * @return mixed      object if id passed else array
     */

    public function project_status_changed_notification($project_id, $status, $integration = false)
    {
        /*echo $project_id;
        echo $status;
        die('<--here');*/
        $project = $this->db->select('name,assigned')->from('tblprojects')->where('id', $project_id)->get()->row();
        $name = $project->name;
        $assigned = $project->assigned;
        $notification_data = array(
            'description' => ($integration == false) ? 'not_project_status_is_changed' : 'not_new_project_is_created_from_form',
            'brandid' => get_user_session(),
            'touserid' => $assigned,
            'eid' => $project_id,
            'not_type' => "projects",
            'link' => 'projects/dashboard/' . $project_id,
            'additional_data' => ($integration == false ? serialize(array(
                $name, $status
            )) : serialize(array()))
        );

        if (add_notification($notification_data)) {
            pusher_trigger_notification(array($assigned));
        }

        /*$this->db->where('staffid', $assigned);
        $email = $this->db->get('tblstaff')->row()->email;

        $this->load->model('emails_model');
        $merge_fields = array();
        $merge_fields = array_merge($merge_fields, get_lead_merge_fields($project_id));
        $this->emails_model->send_email_template('new-project-assigned', $email, $merge_fields);

        $this->db->where('id', $project_id);
        $this->db->update('tblprojects', array(
            'dateassigned' => date('Y-m-d')
        ));

        $not_additional_data = array(
            get_staff_full_name(),
            // '<a href="' . admin_url('profile/' . $assigned) . '" target="_blank">' . get_staff_full_name($assigned) . '</a>'
              '<a href="javascript:void(0)" >' . get_staff_full_name($assigned) . '</a>'
        );

        if ($integration == true) {
            unset($not_additional_data[0]);
            array_values(($not_additional_data));
        }

        $not_additional_data = serialize($not_additional_data);

        $not_desc = ($integration == false ? 'not_project_activity_assigned_to' : 'not_project_activity_assigned_from_form');
        $this->log_activity($project_id, $not_desc, $not_additional_data);*/
    }

    /**
     * Simplified function to send non complicated email templates for project contacts
     * @param mixed $id project id
     * @return boolean
     */
    public function send_project_customer_email($id, $template)
    {
        $this->db->select('clientid');
        $this->db->where('id', $id);
        $clientid = $this->db->get('tblprojects')->row()->clientid;

        $sent = false;
        $contacts = $this->clients_model->get_contacts($clientid);
        $this->load->model('emails_model');
        foreach ($contacts as $contact) {
            if (has_contact_permission('projects', $contact['id'])) {
                $merge_fields = array();
                $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($clientid, $contact['id']));
                $merge_fields = array_merge($merge_fields, get_project_merge_fields($id, array(
                    'customer_template' => true
                )));
                if ($this->emails_model->send_email_template($template, $contact['email'], $merge_fields)) {
                    $send = true;
                }
            }
        }

        return $sent;
    }

    public function mark_as($data)
    {
        $this->db->select('status');
        $this->db->where('id', $data['project_id']);
        $old_status = $this->db->get('tblprojects')->row()->status;

        $this->db->where('id', $data['project_id']);
        $this->db->update('tblprojects', array(
            'status' => $data['status_id']
        ));
        if ($this->db->affected_rows() > 0) {

            do_action('project_status_changed', array(
                'status' => $data['status_id'],
                'project_id' => $data['project_id']
            ));

            if ($data['status_id'] == 4) {
                $this->log_activity($data['project_id'], 'project_marked_as_finished');
                $this->db->where('id', $data['project_id']);
                $this->db->update('tblprojects', array('date_finished' => date('Y-m-d H:i:s')));
            } else {
                $this->log_activity($data['project_id'], 'not_project_status_updated', '<b><lang>project_status_' . $data['status_id'] . '</lang></b>');
            }

            if ($data['notify_project_members_status_change'] == 1) {
                $this->_notify_project_members_status_change($data['project_id'], $old_status, $data['status_id']);
            }
            if ($data['mark_all_tasks_as_completed'] == 1) {
                $this->_mark_all_project_tasks_as_completed($data['project_id']);
            }

            if (isset($data['send_project_marked_as_finished_email_to_contacts']) && $data['send_project_marked_as_finished_email_to_contacts'] == 1) {
                $this->send_project_customer_email($data['project_id'], 'project-finished-to-customer');
            }

            return true;
        }


        return false;
    }

    private function _notify_project_members_status_change($id, $old_status, $new_status)
    {
        $members = $this->get_project_members($id);
        $notifiedUsers = array();
        foreach ($members as $member) {
            if ($member['staff_id'] != get_staff_user_id()) {
                $notified = add_notification(array(
                    'fromuserid' => get_staff_user_id(),
                    'description' => 'not_project_status_updated',
                    'link' => 'projects/view/' . $id,
                    'touserid' => $member['staff_id'],
                    'additional_data' => serialize(array(
                        '<lang>project_status_' . $old_status . '</lang>',
                        '<lang>project_status_' . $new_status . '</lang>'
                    ))
                ));
                if ($notified) {
                    array_push($notifiedUsers, $member['staff_id']);
                }
            }
        }
        pusher_trigger_notification($notifiedUsers);
    }

    private function _mark_all_project_tasks_as_completed($id)
    {
        $this->db->where('rel_type', 'project');
        $this->db->where('rel_id', $id);
        $this->db->update('tblstafftasks', array(
            'status' => 5,
            'datefinished' => date('Y-m-d H:i:s')
        ));
        $tasks = $this->get_tasks($id);
        foreach ($tasks as $task) {
            $this->db->where('task_id', $task['id']);
            $this->db->where('end_time IS NULL');
            $this->db->update('tbltaskstimers', array(
                'end_time' => time()
            ));
        }
        $this->log_activity($id, 'project_activity_marked_all_tasks_as_complete');
    }

    public function add_edit_members($data, $id)
    {
        $affectedRows = 0;
        if (isset($data['project_members'])) {
            $project_members = $data['project_members'];
        }

        $new_project_members_to_receive_email = array();
        $this->db->select('name,clientid');
        $this->db->where('id', $id);
        $project = $this->db->get('tblprojects')->row();
        $project_name = $project->name;
        $client_id = $project->clientid;

        $project_members_in = $this->get_project_members($id);
        if (sizeof($project_members_in) > 0) {
            foreach ($project_members_in as $project_member) {
                if (isset($project_members)) {
                    if (!in_array($project_member['staff_id'], $project_members)) {
                        $this->db->where('project_id', $id);
                        $this->db->where('staff_id', $project_member['staff_id']);
                        $this->db->delete('tblprojectmembers');
                        if ($this->db->affected_rows() > 0) {
                            $this->db->where('staff_id', $project_member['staff_id']);
                            $this->db->where('project_id', $id);
                            $this->db->delete('tblpinnedprojects');

                            $this->log_activity($id, 'project_activity_removed_team_member', get_staff_full_name($project_member['staff_id']));
                            $affectedRows++;
                        }
                    }
                } else {
                    $this->db->where('project_id', $id);
                    $this->db->delete('tblprojectmembers');
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            if (isset($project_members)) {
                $notifiedUsers = array();
                foreach ($project_members as $staff_id) {
                    $this->db->where('project_id', $id);
                    $this->db->where('staff_id', $staff_id);
                    $_exists = $this->db->get('tblprojectmembers')->row();
                    if (!$_exists) {
                        if (empty($staff_id)) {
                            continue;
                        }
                        $this->db->insert('tblprojectmembers', array(
                            'project_id' => $id,
                            'staff_id' => $staff_id
                        ));
                        if ($this->db->affected_rows() > 0) {
                            if ($staff_id != get_staff_user_id()) {
                                $notified = add_notification(array(
                                    'fromuserid' => get_staff_user_id(),
                                    'description' => 'not_staff_added_as_project_member',
                                    'link' => 'projects/view/' . $id,
                                    'touserid' => $staff_id,
                                    'additional_data' => serialize(array(
                                        $project_name
                                    ))
                                ));
                                array_push($new_project_members_to_receive_email, $staff_id);
                                if ($notified) {
                                    array_push($notifiedUsers, $staff_id);
                                }
                            }


                            $this->log_activity($id, 'project_activity_added_team_member', get_staff_full_name($staff_id));
                            $affectedRows++;
                        }
                    }
                }
                pusher_trigger_notification($notifiedUsers);
            }
        } else {
            if (isset($project_members)) {
                $notifiedUsers = array();
                foreach ($project_members as $staff_id) {
                    if (empty($staff_id)) {
                        continue;
                    }
                    $this->db->insert('tblprojectmembers', array(
                        'project_id' => $id,
                        'staff_id' => $staff_id
                    ));
                    if ($this->db->affected_rows() > 0) {
                        if ($staff_id != get_staff_user_id()) {
                            $notified = add_notification(array(
                                'fromuserid' => get_staff_user_id(),
                                'description' => 'not_staff_added_as_project_member',
                                'link' => 'projects/view/' . $id,
                                'touserid' => $staff_id,
                                'additional_data' => serialize(array(
                                    $project_name
                                ))
                            ));
                            array_push($new_project_members_to_receive_email, $staff_id);
                            if ($notifiedUsers) {
                                array_push($notifiedUsers, $staff_id);
                            }
                        }
                        $this->log_activity($id, 'project_activity_added_team_member', get_staff_full_name($staff_id));
                        $affectedRows++;
                    }
                }
                pusher_trigger_notification($notifiedUsers);
            }
        }

        if (count($new_project_members_to_receive_email) > 0) {
            $this->load->model('emails_model');
            $all_members = $this->get_project_members($id);
            foreach ($all_members as $data) {
                if (in_array($data['staff_id'], $new_project_members_to_receive_email)) {
                    $merge_fields = array();
                    $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($client_id));
                    $merge_fields = array_merge($merge_fields, get_staff_merge_fields($data['staff_id']));
                    $merge_fields = array_merge($merge_fields, get_project_merge_fields($id));
                    $this->emails_model->send_email_template('staff-added-as-project-member', $data['email'], $merge_fields);
                }
            }
        }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    public function is_member($project_id, $staff_id = '')
    {
        if (!is_numeric($staff_id)) {
            $staff_id = get_staff_user_id();
        }
        $member = total_rows('tblprojectmembers', array(
            'staff_id' => $staff_id,
            'project_id' => $project_id
        ));
        if ($member > 0) {
            return true;
        }

        return false;
    }

    public function get_projects_for_ticket($client_id)
    {
        return $this->get('', array(
            'clientid' => $client_id
        ));
    }

    public function get_project_settings($project_id)
    {
        $this->db->where('project_id', $project_id);

        return $this->db->get('tblprojectsettings')->result_array();
    }

    public function get_project_members($id)
    {
        $this->db->select('email,project_id,staff_id');
        $this->db->join('tblstaff', 'tblstaff.staffid=tblprojectmembers.staff_id');
        $this->db->where('project_id', $id);

        return $this->db->get('tblprojectmembers')->result_array();
    }

    public function remove_team_member($project_id, $staff_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->where('staff_id', $staff_id);
        $this->db->delete('tblprojectmembers');
        if ($this->db->affected_rows() > 0) {

            // Remove member from tasks where is assigned
            $this->db->where('staffid', $staff_id);
            $this->db->where('taskid IN (SELECT id FROM tblstafftasks WHERE rel_type="project" AND rel_id="' . $project_id . '")');
            $this->db->delete('tblstafftaskassignees');

            $this->log_activity($project_id, 'project_activity_removed_team_member', get_staff_full_name($staff_id));

            return true;
        }

        return false;
    }

    public function get_timesheets($project_id, $tasks_ids = array())
    {
        if (count($tasks_ids) == 0) {
            $tasks = $this->get_tasks($project_id);
            $tasks_ids = array();
            foreach ($tasks as $task) {
                array_push($tasks_ids, $task['id']);
            }
        }
        if (count($tasks_ids) > 0) {
            $this->db->where('task_id IN(' . implode(', ', $tasks_ids) . ')');
            $timesheets = $this->db->get('tbltaskstimers')->result_array();
            $i = 0;
            foreach ($timesheets as $t) {
                $task = $this->tasks_model->get($t['task_id']);
                $timesheets[$i]['task_data'] = $task;
                $timesheets[$i]['staff_name'] = get_staff_full_name($t['staff_id']);
                if (!is_null($t['end_time'])) {
                    $timesheets[$i]['total_spent'] = $t['end_time'] - $t['start_time'];
                } else {
                    $timesheets[$i]['total_spent'] = time() - $t['start_time'];
                }
                $i++;
            }

            return $timesheets;
        } else {
            return array();
        }
    }

    public function get_discussion($id, $project_id = '')
    {
        if ($project_id != '') {
            $this->db->where('project_id', $project_id);
        }
        $this->db->where('id', $id);
        if (is_client_logged_in()) {
            $this->db->where('show_to_customer', 1);
            $this->db->where('project_id IN (SELECT id FROM tblprojects WHERE clientid=' . get_client_user_id() . ')');
        }
        $discussion = $this->db->get('tblprojectdiscussions')->row();
        if ($discussion) {
            return $discussion;
        }

        return false;
    }

    public function get_discussion_comment($id)
    {
        $this->db->where('id', $id);
        $comment = $this->db->get('tblprojectdiscussioncomments')->row();
        if ($comment->contact_id != 0) {
            if (is_client_logged_in()) {
                if ($comment->contact_id == get_contact_user_id()) {
                    $comment->created_by_current_user = true;
                } else {
                    $comment->created_by_current_user = false;
                }
            } else {
                $comment->created_by_current_user = false;
            }
            $comment->profile_picture_url = contact_profile_image_url($comment->contact_id);
        } else {
            if (is_client_logged_in()) {
                $comment->created_by_current_user = false;
            } else {
                if (is_staff_logged_in()) {
                    if ($comment->staff_id == get_staff_user_id()) {
                        $comment->created_by_current_user = true;
                    } else {
                        $comment->created_by_current_user = false;
                    }
                } else {
                    $comment->created_by_current_user = false;
                }
            }
            if (is_admin($comment->staff_id)) {
                $comment->created_by_admin = true;
            } else {
                $comment->created_by_admin = false;
            }
            $comment->profile_picture_url = staff_profile_image_url($comment->staff_id);
        }
        $comment->created = (strtotime($comment->created) * 1000);
        if (!empty($comment->modified)) {
            $comment->modified = (strtotime($comment->modified) * 1000);
        }
        if (!is_null($comment->file_name)) {
            $comment->file_url = site_url('uploads/discussions/' . $comment->discussion_id . '/' . $comment->file_name);
        }

        return $comment;
    }

    public function get_discussion_comments($id, $type)
    {
        $this->db->where('discussion_id', $id);
        $this->db->where('discussion_type', $type);
        $comments = $this->db->get('tblprojectdiscussioncomments')->result_array();
        $i = 0;
        foreach ($comments as $comment) {
            if ($comment['contact_id'] != 0) {
                if (is_client_logged_in()) {
                    if ($comment['contact_id'] == get_contact_user_id()) {
                        $comments[$i]['created_by_current_user'] = true;
                    } else {
                        $comments[$i]['created_by_current_user'] = false;
                    }
                } else {
                    $comments[$i]['created_by_current_user'] = false;
                }
                $comments[$i]['profile_picture_url'] = contact_profile_image_url($comment['contact_id']);
            } else {
                if (is_client_logged_in()) {
                    $comments[$i]['created_by_current_user'] = false;
                } else {
                    if (is_staff_logged_in()) {
                        if ($comment['staff_id'] == get_staff_user_id()) {
                            $comments[$i]['created_by_current_user'] = true;
                        } else {
                            $comments[$i]['created_by_current_user'] = false;
                        }
                    } else {
                        $comments[$i]['created_by_current_user'] = false;
                    }
                }
                if (is_admin($comment['staff_id'])) {
                    $comments[$i]['created_by_admin'] = true;
                } else {
                    $comments[$i]['created_by_admin'] = false;
                }
                $comments[$i]['profile_picture_url'] = staff_profile_image_url($comment['staff_id']);
            }
            if (!is_null($comment['file_name'])) {
                $comments[$i]['file_url'] = site_url('uploads/discussions/' . $id . '/' . $comment['file_name']);
            }
            $comments[$i]['created'] = (strtotime($comment['created']) * 1000);
            if (!empty($comment['modified'])) {
                $comments[$i]['modified'] = (strtotime($comment['modified']) * 1000);
            }
            $i++;
        }

        return $comments;
    }

    public function get_discussions($project_id)
    {
        $this->db->where('project_id', $project_id);
        if (is_client_logged_in()) {
            $this->db->where('show_to_customer', 1);
        }
        $discussions = $this->db->get('tblprojectdiscussions')->result_array();
        $i = 0;
        foreach ($discussions as $discussion) {
            $discussions[$i]['total_comments'] = total_rows('tblprojectdiscussioncomments', array(
                'discussion_id' => $discussion['id']
            ));
            $i++;
        }

        return $discussions;
    }

    public function add_discussion_comment($data, $discussion_id, $type)
    {
        $discussion = $this->get_discussion($discussion_id);
        $_data['discussion_id'] = $discussion_id;
        $_data['discussion_type'] = $type;
        if (isset($data['content'])) {
            $_data['content'] = $data['content'];
        }
        if (isset($data['parent']) && $data['parent'] != null) {
            $_data['parent'] = $data['parent'];
        }
        if (is_client_logged_in()) {
            $_data['contact_id'] = get_contact_user_id();
            $_data['fullname'] = get_contact_full_name($_data['contact_id']);
            $_data['staff_id'] = 0;
        } else {
            $_data['contact_id'] = 0;
            $_data['staff_id'] = get_staff_user_id();
            $_data['fullname'] = get_staff_full_name($_data['staff_id']);
        }
        $_data = handle_project_discussion_comment_attachments($discussion_id, $data, $_data);
        $_data['created'] = date('Y-m-d H:i:s');
        $this->db->insert('tblprojectdiscussioncomments', $_data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if ($type == 'regular') {
                $discussion = $this->get_discussion($discussion_id);
                $not_link = 'projects/view/' . $discussion->project_id . '?group=project_discussions&discussion_id=' . $discussion_id;
            } else {
                $discussion = $this->get_file($discussion_id);
                $not_link = 'projects/view/' . $discussion->project_id . '?group=project_files&file_id=' . $discussion_id;
                $discussion->show_to_customer = $discussion->visible_to_customer;
            }

            $this->send_project_email_template($discussion->project_id, 'new-project-discussion-comment-to-staff', 'new-project-discussion-comment-to-customer', $discussion->show_to_customer, array(
                'staff' => array(
                    'discussion_id' => $discussion_id,
                    'discussion_comment_id' => $insert_id,
                    'discussion_type' => $type
                ),
                'customers' => array(
                    'customer_template' => true,
                    'discussion_id' => $discussion_id,
                    'discussion_comment_id' => $insert_id,
                    'discussion_type' => $type
                )
            ));


            $this->log_activity($discussion->project_id, 'project_activity_commented_on_discussion', $discussion->subject, $discussion->show_to_customer);

            $notification_data = array(
                'description' => 'not_commented_on_project_discussion',
                'link' => $not_link
            );

            if (is_client_logged_in()) {
                $notification_data['fromclientid'] = get_contact_user_id();
            } else {
                $notification_data['fromuserid'] = get_staff_user_id();
            }

            $members = $this->get_project_members($discussion->project_id);
            $notifiedUsers = array();
            foreach ($members as $member) {
                if ($member['staff_id'] == get_staff_user_id() && !is_client_logged_in()) {
                    continue;
                }
                $notification_data['touserid'] = $member['staff_id'];
                if (add_notification($notification_data)) {
                    array_push($notifiedUsers, $member['staff_id']);
                }
            }
            pusher_trigger_notification($notifiedUsers);

            $this->_update_discussion_last_activity($discussion_id, $type);

            return $this->get_discussion_comment($insert_id);
        }

        return false;
    }

    public function update_discussion_comment($data)
    {
        $comment = $this->get_discussion_comment($data['id']);
        $this->db->where('id', $data['id']);
        $this->db->update('tblprojectdiscussioncomments', array(
            'modified' => date('Y-m-d H:i:s'),
            'content' => $data['content']
        ));
        if ($this->db->affected_rows() > 0) {
            $this->_update_discussion_last_activity($comment->discussion_id, $comment->discussion_type);
        }

        return $this->get_discussion_comment($data['id']);
    }

    public function delete_discussion_comment($id)
    {
        $comment = $this->get_discussion_comment($id);
        $this->db->where('id', $id);
        $this->db->delete('tblprojectdiscussioncomments');
        if ($this->db->affected_rows() > 0) {
            $this->delete_discussion_comment_attachment($comment->file_name, $comment->discussion_id);

            $additional_data = '';
            if ($comment->discussion_type == 'regular') {
                $discussion = $this->get_discussion($comment->discussion_id);
                $not = 'project_activity_deleted_discussion_comment';
                $additional_data .= $discussion->subject . '<br />' . $comment->content;
            } else {
                $discussion = $this->get_file($comment->discussion_id);
                $not = 'project_activity_deleted_file_discussion_comment';
                $additional_data .= $discussion->subject . '<br />' . $comment->content;
            }

            if (!is_null($comment->file_name)) {
                $additional_data .= $comment->file_name;
            }
            $this->log_activity($discussion->project_id, $not, $additional_data);
        }
        $this->db->where('parent', $id);
        $this->db->update('tblprojectdiscussioncomments', array(
            'parent' => null
        ));
        if ($this->db->affected_rows() > 0) {
            $this->_update_discussion_last_activity($comment->discussion_id, $comment->discussion_type);
        }

        return true;
    }

    public function delete_discussion_comment_attachment($file_name, $discussion_id)
    {
        $path = PROJECT_DISCUSSION_ATTACHMENT_FOLDER . $discussion_id;
        if (!is_null($file_name)) {
            if (file_exists($path . '/' . $file_name)) {
                unlink($path . '/' . $file_name);
            }
        }
        if (is_dir($path)) {
            // Check if no attachments left, so we can delete the folder also
            $other_attachments = list_files($path);
            if (count($other_attachments) == 0) {
                delete_dir($path);
            }
        }
    }

    public function add_discussion($data)
    {
        if (is_client_logged_in()) {
            $data['contact_id'] = get_contact_user_id();
            $data['staff_id'] = 0;
            $data['show_to_customer'] = 1;
        } else {
            $data['staff_id'] = get_staff_user_id();
            $data['contact_id'] = 0;
            if (isset($data['show_to_customer'])) {
                $data['show_to_customer'] = 1;
            } else {
                $data['show_to_customer'] = 0;
            }
        }
        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);
        $this->db->insert('tblprojectdiscussions', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $members = $this->get_project_members($data['project_id']);
            $notification_data = array(
                'description' => 'not_created_new_project_discussion',
                'link' => 'projects/view/' . $data['project_id'] . '?group=project_discussions&discussion_id=' . $insert_id
            );

            if (is_client_logged_in()) {
                $notification_data['fromclientid'] = get_contact_user_id();
            } else {
                $notification_data['fromuserid'] = get_staff_user_id();
            }

            $notifiedUsers = array();
            foreach ($members as $member) {
                if ($member['staff_id'] == get_staff_user_id() && !is_client_logged_in()) {
                    continue;
                }
                $notification_data['touserid'] = $member['staff_id'];
                if (add_notification($notification_data)) {
                    array_push($notifiedUsers, $member['staff_id']);
                }
            }
            pusher_trigger_notification($notifiedUsers);
            $this->send_project_email_template($data['project_id'], 'new-project-discussion-created-to-staff', 'new-project-discussion-created-to-customer', $data['show_to_customer'], array(
                'staff' => array(
                    'discussion_id' => $insert_id,
                    'discussion_type' => 'regular'
                ),
                'customers' => array(
                    'customer_template' => true,
                    'discussion_id' => $insert_id,
                    'discussion_type' => 'regular'
                )
            ));
            $this->log_activity($data['project_id'], 'project_activity_created_discussion', $data['subject'], $data['show_to_customer']);

            return $insert_id;
        }

        return false;
    }

    public function edit_discussion($data, $id)
    {
        $this->db->where('id', $id);
        if (isset($data['show_to_customer'])) {
            $data['show_to_customer'] = 1;
        } else {
            $data['show_to_customer'] = 0;
        }
        $data['description'] = nl2br($data['description']);
        $this->db->update('tblprojectdiscussions', $data);
        if ($this->db->affected_rows() > 0) {
            $this->log_activity($data['project_id'], 'project_activity_updated_discussion', $data['subject'], $data['show_to_customer']);

            return true;
        }

        return false;
    }

    public function delete_discussion($id)
    {
        $discussion = $this->get_discussion($id);
        $this->db->where('id', $id);
        $this->db->delete('tblprojectdiscussions');
        if ($this->db->affected_rows() > 0) {
            $this->log_activity($discussion->project_id, 'project_activity_deleted_discussion', $discussion->subject, $discussion->show_to_customer);
            $this->_delete_discussion_comments($id, 'regular');

            return true;
        }

        return false;
    }

    public function copy($project_id, $data)
    {
        $project = $this->get($project_id);
        $settings = $this->get_project_settings($project_id);
        $_new_data = array();
        $fields = $this->db->list_fields('tblprojects');
        foreach ($fields as $field) {
            if (isset($project->$field)) {
                $_new_data[$field] = $project->$field;
            }
        }
        unset($_new_data['id']);

        $_new_data['start_date'] = to_sql_date($data['start_date']);

        if ($_new_data['start_date'] > date('Y-m-d')) {
            $_new_data['status'] = 1;
        } else {
            $_new_data['status'] = 2;
        }
        if ($data['deadline']) {
            $_new_data['deadline'] = to_sql_date($data['deadline']);
        } else {
            $_new_data['deadline'] = null;
        }

        $_new_data['project_created'] = date('Y-m-d H:i:s');
        $_new_data['addedfrom'] = get_staff_user_id();

        $_new_data['date_finished'] = null;

        $this->db->insert('tblprojects', $_new_data);
        $id = $this->db->insert_id();
        if ($id) {
            $tags = get_tags_in($project_id, 'project');
            handle_tags_save($tags, $id, 'project');

            foreach ($settings as $setting) {
                $this->db->insert('tblprojectsettings', array(
                    'project_id' => $id,
                    'name' => $setting['name'],
                    'value' => $setting['value']
                ));
            }
            $added_tasks = array();
            $tasks = $this->get_tasks($project_id);
            if (isset($data['tasks'])) {
                foreach ($tasks as $task) {
                    if (isset($data['task_include_followers'])) {
                        $copy_task_data['copy_task_followers'] = 'true';
                    }
                    if (isset($data['task_include_assignees'])) {
                        $copy_task_data['copy_task_assignees'] = 'true';
                    }
                    if (isset($data['tasks_include_checklist_items'])) {
                        $copy_task_data['copy_task_checklist_items'] = 'true';
                    }
                    $copy_task_data['copy_from'] = $task['id'];
                    $task_id = $this->tasks_model->copy($copy_task_data, array(
                        'rel_id' => $id,
                        'rel_type' => 'project',
                        'last_recurring_date' => null,
                        'status' => $data['copy_project_task_status']
                    ));
                    if ($task_id) {
                        array_push($added_tasks, $task_id);
                    }
                }
            }
            if (isset($data['milestones'])) {
                $milestones = $this->get_milestones($project_id);
                $_added_milestones = array();
                foreach ($milestones as $milestone) {
                    $dCreated = new DateTime($milestone['datecreated']);
                    $dDuedate = new DateTime($milestone['due_date']);
                    $dDiff = $dCreated->diff($dDuedate);
                    $due_date = date('Y-m-d', strtotime(date('Y-m-d', strtotime('+' . $dDiff->days . 'DAY'))));

                    $this->db->insert('tblmilestones', array(
                        'name' => $milestone['name'],
                        'project_id' => $id,
                        'milestone_order' => $milestone['milestone_order'],
                        'description_visible_to_customer' => $milestone['description_visible_to_customer'],
                        'description' => $milestone['description'],
                        'due_date' => $due_date,
                        'datecreated' => date('Y-m-d'),
                        'color' => $milestone['color']
                    ));

                    $milestone_id = $this->db->insert_id();
                    if ($milestone_id) {
                        $_added_milestone_data = array();
                        $_added_milestone_data['id'] = $milestone_id;
                        $_added_milestone_data['name'] = $milestone['name'];
                        $_added_milestones[] = $_added_milestone_data;
                    }
                }
                if (isset($data['tasks'])) {
                    if (count($added_tasks) > 0) {
                        // Original project tasks
                        foreach ($tasks as $task) {
                            if ($task['milestone'] != 0) {
                                $this->db->where('id', $task['milestone']);
                                $milestone = $this->db->get('tblmilestones')->row();
                                if ($milestone) {
                                    $name = $milestone->name;
                                    foreach ($_added_milestones as $added_milestone) {
                                        if ($name == $added_milestone['name']) {
                                            $this->db->where('id IN (' . implode(', ', $added_tasks) . ')');
                                            $this->db->where('milestone', $task['milestone']);
                                            $this->db->update('tblstafftasks', array(
                                                'milestone' => $added_milestone['id']
                                            ));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                // milestones not set
                if (count($added_tasks)) {
                    foreach ($added_task as $task) {
                        $this->db->where('id', $task['id']);
                        $this->db->update('tblstafftasks', array(
                            'milestone' => 0
                        ));
                    }
                }
            }
            if (isset($data['members'])) {
                $members = $this->get_project_members($project_id);
                $_members = array();
                foreach ($members as $member) {
                    array_push($_members, $member['staff_id']);
                }
                $this->add_edit_members(array(
                    'project_members' => $_members
                ), $id);
            }

            $this->log_activity($id, 'project_activity_created');
            logActivity('Project Copied [ID: ' . $project_id . ', NewID: ' . $id . ']');

            return $id;
        }

        return false;
    }

    public function get_staff_notes($project_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->where('staff_id', get_staff_user_id());
        $notes = $this->db->get('tblprojectnotes')->row();
        if ($notes) {
            return $notes->content;
        }

        return '';
    }

    public function save_note($data, $project_id)
    {
        // Check if the note exists for this project;
        $this->db->where('project_id', $project_id);
        $this->db->where('staff_id', get_staff_user_id());
        $notes = $this->db->get('tblprojectnotes')->row();
        if ($notes) {
            $this->db->where('id', $notes->id);
            $this->db->update('tblprojectnotes', array(
                'content' => $data['content']
            ));
            if ($this->db->affected_rows() > 0) {
                return true;
            }

            return false;
        } else {
            $this->db->insert('tblprojectnotes', array(
                'staff_id' => get_staff_user_id(),
                'content' => $data['content'],
                'project_id' => $project_id
            ));
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                return true;
            }

            return false;
        }

        return false;
    }

    public function delete($project_id)
    {
        $project = $this->get($project_id);

        $data['deleted'] = 1;
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $data['updatedby'] = get_staff_user_id();

        $this->db->where('id', $project_id);
        $this->db->update('tblprojects', $data);

        if ($this->db->affected_rows() > 0) {
            $additional_data = serialize(array(
                $project->name,
                get_staff_full_name()
            ));

            $this->log_activity($project_id, 'not_project_deleted', $additional_data);

            $this->db->where('project_id', $project_id);
            $this->db->delete('tblprojectmembers');
            $this->db->where('project_id', $project_id);
            $this->db->delete('tblprojectnotes');

            $this->db->where('project_id', $project_id);
            $this->db->delete('tblmilestones');

            // Delete the custom field values
            $this->db->where('relid', $project_id);
            $this->db->where('fieldto', 'projects');
            $this->db->delete('tblcustomfieldsvalues');

            $this->db->where('rel_id', $project_id);
            $this->db->where('rel_type', 'project');
            $this->db->delete('tbltags_in');

            $this->db->where('project_id', $project_id);
            $discussions = $this->db->get('tblprojectdiscussions')->result_array();
            foreach ($discussions as $discussion) {
                $discussion_comments = $this->get_discussion_comments($discussion['id'], 'regular');
                foreach ($discussion_comments as $comment) {
                    $this->delete_discussion_comment_attachment($comment['file_name'], $discussion['id']);
                }
                $this->db->where('discussion_id', $discussion['id']);
                $this->db->delete('tblprojectdiscussioncomments');
            }
            $this->db->where('project_id', $project_id);
            $this->db->delete('tblprojectdiscussions');
            $files = $this->get_files($project_id);
            foreach ($files as $file) {
                $this->remove_file($file['id']);
            }
            $tasks = $this->get_tasks($project_id);
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id'], false);
            }

            $this->db->where('project_id', $project_id);
            $this->db->delete('tblprojectactivity');

            $this->db->where('project_id', $project_id);
            $this->db->update('tblexpenses', array(
                'project_id' => 0
            ));

            $this->db->where('project_id', $project_id);
            $this->db->update('tblinvoices', array(
                'project_id' => 0
            ));

            $this->db->where('project_id', $project_id);
            $this->db->update('tblestimates', array(
                'project_id' => 0
            ));

            $this->db->where('project_id', $project_id);
            $this->db->update('tbltickets', array(
                'project_id' => 0
            ));

            $this->db->where('project_id', $project_id);
            $this->db->delete('tblpinnedprojects');

            logActivity('Project Deleted [ID: ' . $project_id . ', Name: ' . $project->name . ']');

            return true;
        }

        return false;
    }

    public function get_activity($id = '', $limit = '', $only_project_members_activity = false)
    {
        if (!is_client_logged_in()) {
            $has_permission = has_permission('projects', '', 'view');
            if (!$has_permission) {
                $this->db->where('project_id IN (SELECT project_id FROM tblprojectmembers WHERE staff_id=' . get_staff_user_id() . ')');
            }
        }

        if (is_client_logged_in()) {
            $this->db->where('visible_to_customer', 1);
        }
        if (is_numeric($id)) {
            $this->db->where('project_id', $id);
        }

        if (is_numeric($limit)) {
            $this->db->limit($limit);
        }

        $this->db->where('brandid', get_user_session());
        $this->db->order_by('dateadded', 'desc');
        $activities = $this->db->get('tblprojectactivity')->result_array();
        $i = 0;

        if (count($activities) > 0) {
            foreach ($activities as $activity) {
                $seconds = get_string_between($activity['additional_data'], '<seconds>', '</seconds>');
                $other_lang_keys = get_string_between($activity['additional_data'], '<lang>', '</lang>');
                $_additional_data = $activity['additional_data'];
                if ($seconds != '') {
                    $_additional_data = str_replace('<seconds>' . $seconds . '</seconds>', seconds_to_time_format($seconds), $_additional_data);
                }
                if ($other_lang_keys != '') {
                    $_additional_data = str_replace('<lang>' . $other_lang_keys . '</lang>', _l($other_lang_keys), $_additional_data);
                }
                if (strpos($_additional_data, 'project_status_') !== false) {
                    $_additional_data = project_status_by_id(strafter($_additional_data, 'project_status_'));
                }
                $activities[$i]['description'] = _l($activities[$i]['description_key']);
                $activities[$i]['additional_data'] = $_additional_data;
                $this->db->select('name');
                $this->db->where('id', $activity['project_id']);
                $project_name = isset($this->db->get('tblprojects')->row()->name) ? $this->db->get('tblprojects')->row()->name : '';
                $activities[$i]['project_name'] = $project_name;
                unset($activities[$i]['description_key']);
                $i++;
            }
        }

        return $activities;
    }

    public function log_activity($project_id, $description_key, $additional_data = '', $visible_to_customer = 1)
    {
        if (!DEFINED('CRON')) {
            if (is_client_logged_in()) {
                $data['contact_id'] = get_contact_user_id();
                $data['staff_id'] = 0;
                $data['fullname'] = get_contact_full_name(get_contact_user_id());
            } elseif (is_staff_logged_in()) {
                $data['contact_id'] = 0;
                $data['staff_id'] = get_staff_user_id();
                $data['fullname'] = get_staff_full_name(get_staff_user_id());
            }
        } else {
            $data['contact_id'] = 0;
            $data['staff_id'] = 0;
            $data['fullname'] = '[CRON]';
        }
        $data['description_key'] = $description_key;
        $data['brandid'] = get_user_session();
        $data['additional_data'] = $additional_data;
        $data['visible_to_customer'] = $visible_to_customer;
        $data['project_id'] = $project_id;
        $data['dateadded'] = date('Y-m-d H:i:s');

        $data = do_action('before_log_project_activity', $data);

        $this->db->insert('tblprojectactivity', $data);
    }

    public function new_project_file_notification($file_id, $project_id)
    {
        $file = $this->get_file($file_id);

        $additional_data = $file->file_name;
        $this->log_activity($project_id, 'project_activity_uploaded_file', $additional_data, $file->visible_to_customer);

        $members = $this->get_project_members($project_id);
        $notification_data = array(
            'description' => 'not_project_file_uploaded',
            'link' => 'projects/view/' . $project_id . '?group=project_files&file_id=' . $file_id,
        );

        if (is_client_logged_in()) {
            $notification_data['fromclientid'] = get_contact_user_id();
        } else {
            $notification_data['fromuserid'] = get_staff_user_id();
        }

        $notifiedUsers = array();
        foreach ($members as $member) {
            if ($member['staff_id'] == get_staff_user_id() && !is_client_logged_in()) {
                continue;
            }
            $notification_data['touserid'] = $member['staff_id'];
            if (add_notification($notification_data)) {
                array_push($notifiedUsers, $member['staff_id']);
            }
        }
        pusher_trigger_notification($notifiedUsers);

        $this->send_project_email_template(
            $project_id,
            'new-project-file-uploaded-to-staff',
            'new-project-file-uploaded-to-customer',
            $file->visible_to_customer,
            array(
                'staff' => array('discussion_id' => $file_id, 'discussion_type' => 'file'),
                'customers' => array('customer_template' => true, 'discussion_id' => $file_id, 'discussion_type' => 'file'),
            )
        );
    }

    public function add_external_file($data)
    {
        $insert['dateadded'] = date('Y-m-d H:i:s');
        $insert['project_id'] = $data['project_id'];
        $insert['external'] = $data['external'];
        $insert['visible_to_customer'] = $data['visible_to_customer'];
        $insert['file_name'] = $data['files'][0]['name'];
        $insert['subject'] = $data['files'][0]['name'];
        $insert['external_link'] = $data['files'][0]['link'];

        $path_parts = pathinfo($data['files'][0]['name']);
        $insert['filetype'] = get_mime_by_extension('.' . $path_parts['extension']);

        if (isset($data['files'][0]['thumbnailLink'])) {
            $insert['thumbnail_link'] = $data['files'][0]['thumbnailLink'];
        }

        if (isset($data['staffid'])) {
            $insert['staffid'] = $data['staffid'];
        } elseif (isset($data['contact_id'])) {
            $insert['contact_id'] = $data['contact_id'];
        }

        $this->db->insert('tblprojectfiles', $insert);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $this->new_project_file_notification($insert_id, $data['project_id']);

            return $insert_id;
        }

        return false;
    }

    public function send_project_email_template($project_id, $staff_template, $customer_template, $action_visible_to_customer, $additional_data = array())
    {
        if (count($additional_data) == 0) {
            $additional_data['customers'] = array();
            $additional_data['staff'] = array();
        } elseif (count($additional_data) == 1) {
            if (!isset($additional_data['staff'])) {
                $additional_data['staff'] = array();
            } else {
                $additional_data['customers'] = array();
            }
        }

        $project = $this->get($project_id);
        $members = $this->get_project_members($project_id);

        $this->load->model('emails_model');
        foreach ($members as $member) {
            if (is_staff_logged_in()) {
                if ($member['staff_id'] == get_staff_user_id()) {
                    continue;
                }
            }
            $merge_fields = array();
            $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($project->clientid));
            $merge_fields = array_merge($merge_fields, get_staff_merge_fields($member['staff_id']));
            $merge_fields = array_merge($merge_fields, get_project_merge_fields($project->id, $additional_data['staff']));
            $this->emails_model->send_email_template($staff_template, $member['email'], $merge_fields);
        }
        if ($action_visible_to_customer == 1) {
            $contacts = $this->clients_model->get_contacts($project->clientid);
            foreach ($contacts as $contact) {
                if (is_client_logged_in()) {
                    if ($contact['id'] == get_contact_user_id()) {
                        continue;
                    }
                }
                if (has_contact_permission('projects', $contact['id'])) {
                    $merge_fields = array();
                    $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($project->clientid, $contact['id']));
                    $merge_fields = array_merge($merge_fields, get_project_merge_fields($project->id, $additional_data['customers']));
                    $this->emails_model->send_email_template($customer_template, $contact['email'], $merge_fields);
                }
            }
        }
    }

    private function _get_project_billing_data($id)
    {
        $this->db->select('billing_type,project_rate_per_hour');
        $this->db->where('id', $id);

        return $this->db->get('tblprojects')->row();
    }

    public function total_logged_time_by_billing_type($id, $conditions = array())
    {
        $project_data = $this->_get_project_billing_data($id);
        $data = array();
        if ($project_data->billing_type == 2) {
            $seconds = $this->total_logged_time($id);
            $data = $this->projects_model->calculate_total_by_project_hourly_rate($seconds, $project_data->project_rate_per_hour);
            $data['logged_time'] = $data['hours'];
        } elseif ($project_data->billing_type == 3) {
            $data = $this->_get_data_total_logged_time($id);
        }

        return $data;
    }

    public function data_billable_time($id)
    {
        return $this->_get_data_total_logged_time($id, array(
            'billable' => 1
        ));
    }

    public function data_billed_time($id)
    {
        return $this->_get_data_total_logged_time($id, array(
            'billable' => 1,
            'billed' => 1
        ));
    }

    public function data_unbilled_time($id)
    {
        return $this->_get_data_total_logged_time($id, array(
            'billable' => 1,
            'billed' => 0
        ));
    }

    private function _delete_discussion_comments($id, $type)
    {
        $this->db->where('discussion_id', $id);
        $this->db->where('discussion_type', $type);
        $comments = $this->db->get('tblprojectdiscussioncomments')->result_array();
        foreach ($comments as $comment) {
            $this->delete_discussion_comment_attachment($comment['file_name'], $id);
        }
        $this->db->where('discussion_id', $id);
        $this->db->where('discussion_type', $type);
        $this->db->delete('tblprojectdiscussioncomments');
    }

    private function _get_data_total_logged_time($id, $conditions = array())
    {
        $project_data = $this->_get_project_billing_data($id);
        $tasks = $this->get_tasks($id, $conditions);

        if ($project_data->billing_type == 3) {
            $data = $this->calculate_total_by_task_hourly_rate($tasks);
            $data['logged_time'] = seconds_to_time_format($data['total_seconds']);
        } elseif ($project_data->billing_type == 2) {
            $seconds = 0;
            foreach ($tasks as $task) {
                $seconds += $task['total_logged_time'];
            }
            $data = $this->calculate_total_by_project_hourly_rate($seconds, $project_data->project_rate_per_hour);
            $data['logged_time'] = $data['hours'];
        }

        return $data;
    }

    private function _update_discussion_last_activity($id, $type)
    {
        if ($type == 'file') {
            $table = 'tblprojectfiles';
        } elseif ($type == 'regular') {
            $table = 'tblprojectdiscussions';
        }
        $this->db->where('id', $id);
        $this->db->update($table, array(
            'last_activity' => date('Y-m-d H:i:s')
        ));
    }

    public function add_attachment_to_database($project_id, $attachment, $external = false, $form_activity = false, $type = "")
    {
        if ($type == "") {
            $t = "project";
        } else {
            $t = "event";
        }
        $this->misc_model->add_attachment_to_database($project_id, $t, $attachment, $external);

        if ($form_activity == false) {
            $this->projects_model->log_project_activity($project_id, 'not_project_activity_added_attachment');
        } else {
            $this->projects_model->log_project_activity($project_id, 'not_project_activity_log_attachment', true, serialize(array(
                $form_activity
            )));
        }

        // No notification when attachment is imported from web to project form
        if ($form_activity == false) {
            $project = $this->get($project_id);
            $not_user_ids = array();
            if ($project->addedfrom != get_staff_user_id()) {
                array_push($not_user_ids, $project->addedfrom);
            }
            if ($project->assigned != get_staff_user_id() && $project->assigned != 0) {
                array_merge($not_user_ids, $project->assigned);
            }
            $notifiedUsers = array();
            foreach ($not_user_ids as $uid) {
                $notified = add_notification(array(
                    'description' => 'not_project_added_attachment',
                    'touserid' => $uid,
                    'brandid' => get_user_session(),
                    'eid' => $project_id,
                    'not_type' => 'projects',
                    'link' => '#projectid=' . $project_id,
                    'additional_data' => serialize(array(
                        $project->name
                    ))
                ));
                if ($notified) {
                    array_push($notifiedUsers, $uid);
                }
            }
            pusher_trigger_notification($notifiedUsers);
        }
    }

    public function log_project_activity($id, $description, $integration = false, $additional_data = '')
    {
        $log = array(
            'dateadded' => date('Y-m-d H:i:s'),
            'description_key' => $description,
            'project_id' => $id,
            'staff_id' => get_staff_user_id(),
            'additional_data' => $additional_data,
            'fullname' => get_staff_full_name(get_staff_user_id())
        );
        if ($integration == true) {
            $log['staffid'] = 0;
            $log['full_name'] = '[CRON]';
        }

        $this->db->insert('tblprojectactivity', $log);

        return $this->db->insert_id();
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/03/2017
     * save invite
     */
    public function sendinvite($data)
    {
        $userid = get_staff_user_id();
        $staff = $this->staff_model->get($userid);

        $user_type = $staff->user_type;

        if ($data['contacttype'] == 3 || $data['contacttype'] == 4) {
            $contact_sel = $data['vendor'];

            //check if staff is invited or contact is invited
            if (strpos($data['vendor'], 'staff-') !== false) {
                $data['staffid'] = str_replace('staff-', '', $data['vendor']);
            } else {
                $data['contactid'] = str_replace('contact-', '', $data['vendor']);
            }
        }

        $events = $data['events'];
        $permissions = $data['permissionid'];
        $parent = $data['parent'];

        $data['tags'] = (isset($data['tags']) ? implode(",", $data['tags']) : '');
        $data['projectid'] = $data['project'];

        $where = array('id' => $data['projectid'], 'deleted' => 0);
        $project = $this->db->where($where)->get('tblprojects')->row();
        if ($project->parent == 0) {
            $projectid = $data['projectid'];
        } else {
            $projectid = $project->parent;
        }


        $this->db->where('isclient', 1);
        $this->db->where('projectid', $projectid);
        $clients = $this->db->get('tblprojectcontact')->result();
        unset($data['project']);
        unset($data['invite']);
        unset($data['events']);
        unset($data['permissionid']);
        unset($data['parent']);
        unset($data['vendor']);
        unset($data['company']);

        //for vendor
        if ($data['contacttype'] == 3) {
            $data['status'] = ($user_type == 2 ? $this->invite_vendor_status[0] : $this->invite_vendor_status[2]);
        }

        //for collaborator
        if ($data['contacttype'] == 4) {
            $data['status'] = ($user_type == 2 ? $this->invite_collaborator_status[0] : $this->invite_collaborator_status[2]);
        }

        //for venue
        if ($data['contacttype'] == 5) {
            $data['status'] = ($user_type == 2 ? $this->invite_venue_status[0] : $this->invite_venue_status[2]);
            //$data['status']         = $this->invite_venue_status[3];
        }
        $data['status'] = "pending";
        $data['invitedby'] = $this->session->userdata['staff_user_id'];
        $data['created_by'] = $this->session->userdata['staff_user_id'];
        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['brandid'] = get_user_session();

        //create entry in invite table
        $this->db->insert('tblinvite', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id > 0) {
            if ($data['contacttype'] == 3 || $data['contacttype'] == 4) {
                if (isset($data['staffid'])) {
                    $contact = $data['staffid'];
                } elseif (isset($data['contactid'])) {
                    $contact = $data['contactid'];
                } else {
                    $contact = $data['email'];
                }
            } else {
                $contact = $data['venueid'];
            }

            logActivity('New invitation has been created by [Created By:' . $data['created_by'] . ' for Project ID:' . $data['projectid'] . ' to Contact ID: ' . $contact . ' IP:' . $this->input->ip_address() . ']');
        }

        //insert all permissions assigned for each selected project
        foreach ($events as $key => $event) {
            $given_permissions = '';
            foreach ($permissions[$key] as $permission) {
                $permission_data = [];
                $given_permissions .= $permission . ",";

                $permission_data['projectid'] = $event;
                $permission_data['permissionid'] = $permission;
                $permission_data['inviteid'] = $insert_id;

                $this->db->insert('tbleventpermission', $permission_data);
            }

            $status_data = [];
            $status_data['inviteid'] = $insert_id;
            $status_data['projectid'] = $event;

            //for vendor
            if ($data['contacttype'] == 3) {
                $status_data['status'] = ($user_type == 2 ? $this->invite_vendor_status[0] : $this->invite_vendor_status[2]);

                if ($insert_id > 0 && $user_type == 2 && isset($data['staffid'])) {
                    $status_data['status'] = $this->invite_vendor_status[2];
                }
            }

            //for collaborator
            if ($data['contacttype'] == 4) {
                $status_data['status'] = ($user_type == 2 ? $this->invite_collaborator_status[0] : $this->invite_collaborator_status[2]);

                if ($insert_id > 0 && $user_type == 2 && isset($data['staffid'])) {
                    $status_data['status'] = $this->invite_collaborator_status[2];
                }
            }

            //for venue
            if ($data['contacttype'] == 5) {
                $status_data['status'] = ($user_type == 2 ? $this->invite_venue_status[0] : $this->invite_venue_status[2]);
                //$status_data['status']      = $this->invite_venue_status[3];
                if ($insert_id > 0 && $user_type == 2 && isset($data['staffid'])) {
                    //$status_data['status']      = $this->invite_venue_status[2];
                    $status_data['status'] = $this->invite_venue_status[3];
                }
            }

            $status_data['created_by'] = $this->session->userdata['staff_user_id'];
            $status_data['datecreated'] = date('Y-m-d H:i:s');

            $this->db->insert('tblinvitestatus', $status_data);

            logActivity('Invitation has been created by with following permissions [Project ID:' . $data['projectid'] . ' Permissions: ' . $given_permissions . ' IP:' . $this->input->ip_address() . ']');
        }

        //send email
        $this->load->model('emails_model');
        if ($insert_id > 0 && $user_type == 2 && !isset($data['staffid'])) {
            if ($data['contacttype'] == 3 || $data['contacttype'] == 4) {
                //if team member has send invite, send email to account owner for invite creation
                $merge_fields = array();
                $merge_fields = array_merge($merge_fields, get_invite_merge_field($insert_id, $data['contacttype']));

                $account_user_query = $this->db->query('SELECT tblstaff.* FROM tblstaff WHERE staffid IN ( SELECT staffid FROM tblstaffbrand WHERE brandid = (SELECT brandid FROM tblstaffbrand WHERE staffid = ' . $this->session->userdata['staff_user_id'] . ')) AND user_type = 1 AND email != ""');
                $results = $account_user_query->result_array();
                foreach ($results as $result) {
                    $staffdetails['{name}'] = $result['firstname'];
                    $merge_fields = array_merge($merge_fields, $staffdetails);

                    //for vendor
                    if ($data['contacttype'] == 3) {
                        $send = $this->emails_model->send_email_template('invite-new', $result['email'], $merge_fields);
                    }

                    //for collaborator
                    if ($data['contacttype'] == 4) {
                        $send = $this->emails_model->send_email_template('invite-new-collaborator', $result['email'], $merge_fields);
                    }

                    if (!$send) {
                        logActivity('Invitation email could not be sent to [Email Address:' . $result['email'] . ' for invitation: ' . $insert_id . ' IP:' . $this->input->ip_address() . ']');
                    }
                }
            } else {
                //if team member has send invite, send email to account owner for invite creation
                $merge_fields = array();
                $merge_fields = array_merge($merge_fields, get_invite_merge_field($insert_id, $data['contacttype']));

                $account_user_query = $this->db->query('SELECT tblstaff.* FROM tblstaff WHERE staffid IN ( SELECT staffid FROM tblstaffbrand WHERE brandid = (SELECT brandid FROM tblstaffbrand WHERE staffid = ' . $this->session->userdata['staff_user_id'] . ')) AND user_type = 1 AND email != ""');
                $results = $account_user_query->result_array();
                foreach ($results as $result) {
                    $staffdetails['{name}'] = $result['firstname'];
                    $merge_fields = array_merge($merge_fields, $staffdetails);

                    //for venue
                    $send = $this->emails_model->send_email_template('invite-new-venue', $result['email'], $merge_fields);

                    if (!$send) {
                        logActivity('Invitation email could not be sent to [Email Address:' . $result['email'] . ' for invitation: ' . $insert_id . ' IP:' . $this->input->ip_address() . ']');
                    }
                }
            }
        } else {
            if ($data['contacttype'] == 3 || $data['contacttype'] == 4) {
                //if account owner has send invite, send email to invited vendor
                $merge_fields = array();
                $merge_fields = array_merge($merge_fields, get_invite_merge_field($insert_id, $data['contacttype'], 'sent-to-vendor'));

                //if existing staff or contact id, assign project directly
                if (isset($data['staffid']) && $data['staffid'] != '') {
                    $account_user_query = $this->db->query('SELECT tblstaff.* FROM tblstaff WHERE staffid = ' . $data['staffid']);
                    $results = $account_user_query->row();
                    $vendor_email = $results->email;
                    $firstname = $results->firstname;

                    foreach ($events as $key => $event) {
                        $allow_insert = true;

                        //for vendor
                        if ($data['contacttype'] == 3) {
                            $vcnt = $this->db->query('SELECT `projectcontactid` FROM `tblprojectcontact` WHERE (`projectid` = ' . $event . ' OR `eventid` = ' . $event . ') AND `contactid` = ' . $data['staffid'] . ' AND `isvendor` = 1');
                            if ($vcnt->num_rows() > 0) {
                                $allow_insert = false;
                            }
                        }

                        //for collaborator
                        if ($data['contacttype'] == 4) {
                            $vcnt = $this->db->query('SELECT `projectcontactid` FROM `tblprojectcontact` WHERE (`projectid` = ' . $event . ' OR `eventid` = ' . $event . ') AND `contactid` = ' . $data['staffid'] . ' AND `iscollaborator` = 1');
                            if ($vcnt->num_rows() > 0) {
                                $allow_insert = false;
                            }
                        }

                        //if not inserted, then insert
                        if ($allow_insert) {
                            $project_contact = [];

                            $pdet = $this->get($event);
                            if ($pdet->parent == 0) {
                                $project_contact['projectid'] = $event;
                            } else {
                                $project_contact['projectid'] = 0;
                                $project_contact['eventid'] = $event;
                            }

                            $project_contact['contactid'] = $data['staffid'];
                            $project_contact['brandid'] = get_user_session();

                            //for vendor
                            if ($data['contacttype'] == 3) {
                                $project_contact['isvendor'] = 1;
                                $project_contact['iscollaborator'] = 0;
                            }

                            //for collaborator
                            if ($data['contacttype'] == 4) {
                                $project_contact['isvendor'] = 0;
                                $project_contact['iscollaborator'] = 1;
                            }

                            $this->db->insert('tblprojectcontact', $project_contact);
                        }
                    }

                    //for vendor
                    if ($data['contacttype'] == 3) {
                        $send = $this->emails_model->send_email_template('invite-vendor', $vendor_email, $merge_fields);
                    }

                    //for collaborator
                    if ($data['contacttype'] == 4) {
                        $send = $this->emails_model->send_email_template('invite-collaborator', $vendor_email, $merge_fields);
                    }
                } elseif (isset($data['contactid']) && $data['contactid'] != '') {
                    $account_user_query = $this->db->query('SELECT `email`, `firstname` FROM `tbladdressbookemail` JOIN `tbladdressbook` ON `tbladdressbook`.`addressbookid` = `tbladdressbookemail`.`addressbookid` WHERE `type` = "primary" AND `tbladdressbook`.`addressbookid` = ' . $data['contactid']);
                    $results = $account_user_query->row();
                    $vendor_email = $results->email;
                    $firstname = $results->firstname;
                    $staffdetails['{name}'] = $firstname;
                    $merge_fields = array_merge($merge_fields, $staffdetails);

                    foreach ($events as $key => $event) {
                        $allow_insert = true;

                        //for vendor
                        if ($data['contacttype'] == 3) {
                            $vcnt = $this->db->query('SELECT `projectcontactid` FROM `tblprojectcontact` WHERE (`projectid` = ' . $event . ' OR `eventid` = ' . $event . ') AND `contactid` = ' . $data['contactid'] . ' AND `isvendor` = 1');
                            if ($vcnt->num_rows() > 0) {
                                $allow_insert = false;
                            }
                        }

                        //for collaborator
                        if ($data['contacttype'] == 4) {
                            $vcnt = $this->db->query('SELECT `projectcontactid` FROM `tblprojectcontact` WHERE (`projectid` = ' . $event . ' OR `eventid` = ' . $event . ') AND `contactid` = ' . $data['contactid'] . ' AND `iscollaborator` = 1');
                            if ($vcnt->num_rows() > 0) {
                                $allow_insert = false;
                            }
                        }

                        //if not inserted, insert
                        if ($allow_insert) {
                            $project_contact = [];

                            $pdet = $this->get($event);
                            if ($pdet->parent == 0) {
                                $project_contact['projectid'] = $event;
                            } else {
                                $project_contact['projectid'] = 0;
                                $project_contact['eventid'] = $event;
                            }
                            $project_contact['contactid'] = $data['contactid'];
                            $project_contact['brandid'] = get_user_session();

                            //for vendor
                            if ($data['contacttype'] == 3) {
                                $project_contact['isvendor'] = 1;
                                $project_contact['iscollaborator'] = 0;
                            }

                            //for collaborator
                            if ($data['contacttype'] == 4) {
                                $project_contact['isvendor'] = 0;
                                $project_contact['iscollaborator'] = 1;
                            }

                            $this->db->insert('tblprojectcontact', $project_contact);
                        }
                    }

                    //for vendor
                    if ($data['contacttype'] == 3) {
                        $send = $this->emails_model->send_email_template('invite-vendor', $vendor_email, $merge_fields);
                    }

                    //for collaborator
                    if ($data['contacttype'] == 4) {
                        $send = $this->emails_model->send_email_template('invite-collaborator', $vendor_email, $merge_fields);
                    }
                } else {
                    //if new vendor, create account
                    $where = array('email = ' => $data['email'], 'deleted = ' => 0);

                    $res = $this->db->where($where)->get('tblstaff')->row();
                    //check if account already exists or not
                    if (count($res) > 0) {
                        //if user is not active, send credentials in mail
                        if ($res->last_login == NULL || $res->last_login == null) {
                            $staffdetails['{name}'] = $data['firstname'];
                            $staffdetails['{vendor_password'] = $res->random_pass;
                            $merge_fields = array_merge($merge_fields, $staffdetails);

                            //for vendor
                            if ($data['contacttype'] == 3) {
                                $send = $this->emails_model->send_email_template('invite-new-vendor', $data['email'], $merge_fields);
                            }

                            //for collaborator
                            if ($data['contacttype'] == 4) {
                                $send = $this->emails_model->send_email_template('invite-new-collaborator', $data['email'], $merge_fields);
                            }
                        } else {
                            //if user is active, do not send credentials in mail

                            //for vendor
                            if ($data['contacttype'] == 3) {
                                $send = $this->emails_model->send_email_template('invite-vendor', $data['email'], $merge_fields);
                            }

                            //for collaborator
                            if ($data['contacttype'] == 4) {
                                $send = $this->emails_model->send_email_template('invite-collaborator', $data['email'], $merge_fields);
                            }
                        }
                    } else {
                        //if user does not found, create new user
                        $vendor_email = $data['email'];
                        $firstname = $data['firstname'];

                        $merge_fields = array_merge($merge_fields, get_invite_merge_field($insert_id, $data['contacttype'], 'sent-to-vendor'));

                        $query = $this->db->query('SELECT packageid FROM tblpackages WHERE name = "Free Package"');
                        $package = $query->row();

                        //generate random password
                        $password = $this->randomPassword();

                        $staffdata = [];
                        $staffdata['firstname'] = $data['firstname'];
                        $staffdata['lastname'] = $data['lastname'];
                        $staffdata['email'] = $vendor_email;
                        $staffdata['password'] = $password;
                        $staffdata['random_pass'] = $password;
                        $staffdata['created_by'] = $this->session->userdata['staff_user_id'];
                        $staffdata['datecreated'] = date('Y-m-d H:i:s');
                        $staffdata['active'] = 0;
                        $staffdata['facebook'] = null;
                        $staffdata['twitter'] = null;
                        $staffdata['google'] = null;
                        $staffdata['brandname'] = $firstname;
                        $staffdata['brandtype'] = 1;
                        $staffdata['is_not_staff'] = 1;
                        $staffdata['user_type'] = $data['contacttype'];
                        $staffdata['packagetype'] = (isset($package->packageid) ? $package->packageid : 2);
                        $this->load->model('register_model');

                        $this->register_model->saveclient($staffdata, 'invite');

                        logActivity('New User Created [Email Address:' . $vendor_email . ' for invitation: ' . $insert_id . 'staffdata IP:' . $this->input->ip_address() . ']');

                        $where = array('email' => $vendor_email, 'deleted' => 0);
                        $staff_det = $this->db->where($where)->get('tblstaff')->row();

                        //assign project to new invited vendor
                        foreach ($events as $key => $event) {
                            $project_contact = [];

                            $pdet = $this->get($event);
                            if ($pdet->parent == 0) {
                                $project_contact['projectid'] = $event;
                            } else {
                                $project_contact['projectid'] = 0;
                                $project_contact['eventid'] = $event;
                            }

                            $project_contact['projectid'] = $projectid;
                            $project_contact['contactid'] = $staff_det->staffid;
                            $project_contact['brandid'] = get_user_session();

                            //for vendor
                            if ($data['contacttype'] == 3) {
                                $project_contact['isvendor'] = 1;
                                $project_contact['iscollaborator'] = 0;
                            }

                            //for collaborator
                            if ($data['contacttype'] == 4) {
                                $project_contact['isvendor'] = 0;
                                $project_contact['iscollaborator'] = 1;
                            }

                            $this->db->insert('tblprojectcontact', $project_contact);
                        }

                        //update staff id in invite table
                        $invite_data = [];
                        $invite_data['staffid'] = $staff_det->staffid;
                        $invite_data['updated_by'] = (isset($this->session->userdata['staff_user_id']) ? $this->session->userdata['staff_user_id'] : 0);
                        $invite_data['dateupdated'] = date('Y-m-d H:i:s');
                        $this->db->where('inviteid', $insert_id);
                        $this->db->update('tblinvite', $invite_data);

                        $staff_brand = [];
                        $staff_brand['active'] = 1;
                        $staff_brand['staffid'] = $staff_det->staffid;
                        $staff_brand['brandid'] = get_user_session();
                        $this->db->insert('tblstaffbrand', $staff_brand);
                        /**
                         * Added By : Masud
                         * Dt : 03/23/2018
                         * prefill dashboard values
                         */
                        $dashboard_data = array();
                        $dashboard_data['staffid'] = $staff_det->staffid;
                        $dashboard_data['widget_type'] = 'upcoming_project,pinned_item,calendar,weather,favourite,quick_link,lead_pipeline,message,getting_started,task_list,contacts,messages';
                        $dashboard_data['quick_link_type'] = 'lead,project,message,task_due,meeting,amount_receivable,amount_received,invite';
                        $dashboard_data['order'] = '[{"widget_name":"getting_started","order":0},{"widget_name":"lead_pipeline","order":1},{"widget_name":"calendar","order":2},{"widget_name":"pinned_item","order":3},{"widget_name":"quick_link","order":4},{"widget_name":"upcoming_project","order":5},{"widget_name":"contacts","order":6},{"widget_name":"messages","order":7},{"widget_name":"task_list","order":8}]';
                        $dashboard_data['is_visible'] = 1;
                        $dashboard_data['brandid'] = get_user_session();
                        $dashboard_data['dateadded'] = date('Y-m-d H:i:s');
                        $dashboard_data['addedby'] = $staff_det->staffid;
                        $this->db->insert('  tbldashboard_settings', $dashboard_data);

                        $staffdetails['{name}'] = $firstname;
                        $staffdetails['{vendor_password}'] = $password;
                        $merge_fields = array_merge($merge_fields, $staffdetails);

                        //for vendor
                        if ($data['contacttype'] == 3) {
                            $send = $this->emails_model->send_email_template('invite-new-vendor', $vendor_email, $merge_fields);
                        }

                        //for collaborator
                        if ($data['contacttype'] == 4) {
                            $send = $this->emails_model->send_email_template('invite-new-project-collaborator', $vendor_email, $merge_fields);
                        }
                    }
                }
            }

            if ($data['contacttype'] == 5) {
                foreach ($events as $key => $event) {
                    $project_venue = [];

                    $pdet = $this->get($event);
                    if ($pdet->parent == 0) {
                        $project_venue['projectid'] = $event;
                    } else {
                        $project_venue['projectid'] = 0;
                        $project_venue['eventid'] = $event;
                    }
                    $project_venue['venueid'] = $data['venueid'];
                    $project_venue['brandid'] = get_user_session();

                    $this->db->insert('tblprojectvenue', $project_venue);
                }
            }

            if (!$send) {
                logActivity('Invitation email could not be sent to [Email Address:' . $vendor_email . ' for invitation: ' . $insert_id . ' IP:' . $this->input->ip_address() . ']');
            }
        }

        return $insert_id;
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/03/2017
     * get invitees
     */
    public function get_invitees($id = '', $viewtype = '', $where = array(''), $limit = "", $page = "", $is_kanban = false, $search = "", $status = "")
    {
        if ($viewtype != 'vendor-view') {
            $userid = get_staff_user_id();
            $staff = $this->staff_model->get($userid);
            $user_type = $staff->user_type;
        }

        //get all invitees details
        $this->db->select('tblinvite.*, `tblprojects`.`id` AS pid, (SELECT CONCAT(`tblstaff`.`firstname`,  \'  \', `tblstaff`.`lastname`) FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) as invited_name, (SELECT `tblstaff`.`email` FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) as invited_email, `tblprojects`.`name` as project_name, (SELECT `tbleventtype`.`eventtypename` FROM `tbleventtype` WHERE `tbleventtype`.`eventtypeid` = `tblprojects`.`eventtypeid`) as project_type, `tblprojects`.`eventstartdatetime` as eventstartdatetime, `tblprojects`.`eventenddatetime` as eventenddatetime, IF(`tblinvite`.`firstname` =  \' \', IF(`tblinvite`.`staffid` > 0,(SELECT CONCAT(`tblstaff`.`firstname`,  \'  \', `tblstaff`.`lastname`) FROM `tblstaff` WHERE `staffid` = `tblinvite`.`staffid`), CONCAT(`tbladdressbook`.`firstname`,  \'  \', `tbladdressbook`.`lastname`)), CONCAT(`tblinvite`.`firstname`,  \'  \', `tblinvite`.`lastname`)) as assigned_name , IF(`tblinvite`.`phone` =  \' \', IF(`tblinvite`.`staffid` > 0,(SELECT `tblstaff`.`phonenumber` FROM `tblstaff` WHERE `staffid` = `tblinvite`.`staffid`), (SELECT `phone` FROM `tbladdressbookphone` WHERE `addressbookid` = `tbladdressbook`.`addressbookid` AND `type` = "primary")), `tblinvite`.`phone`) as assigned_phone, IF(`tblinvite`.`email` =  \' \', IF(`tblinvite`.`staffid` > 0, (SELECT `tblstaff`.`email` FROM `tblstaff` WHERE `staffid` = `tblinvite`.`staffid`), (SELECT `email` FROM `tbladdressbookemail` WHERE `addressbookid` = `tbladdressbook`.`addressbookid` AND `type` = "primary")), `tblinvite`.`email`) as assigned_email, IF(`tblinvite`.`venueid` !=  \' \', (SELECT `venuename` FROM `tblvenue` WHERE `venueid` = `tblinvite`.`venueid`), CONCAT(`tblinvite`.`firstname`,  \'  \', `tblinvite`.`lastname`)) as venue_name, IF(`tblinvite`.`venueid` !=  \' \', (SELECT `venueemail` FROM `tblvenue` WHERE `venueid` = `tblinvite`.`venueid`), `tblinvite`.`email`) as venue_email, tblvenue.venuename ');
        $this->db->join('tblprojects', 'tblprojects.id = tblinvite.projectid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblinvite.staffid', 'left');
        $this->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblinvite.contactid', 'left');
        $this->db->join('tblvenue', 'tblvenue.venueid = tblinvite.venueid', 'left');
        $this->db->join('tblinvitestatus', 'tblinvitestatus.inviteid = tblinvite.inviteid', 'inner'); //tblinvitestatus.status as invite_state2
        //$this->db->where('tblinvite.inviteid=tblinvitestatus.inviteid');
        /*if ($viewtype != 'vendor-view') {
            $this->db->where('tblinvitestatus.userid', get_staff_user_id());
        }*/
        //$this->db->where('tblinvitestatus.deleted=0');
        //add assigned clause for logged in user
        /*if ($viewtype != 'vendor-view') {
            if ($user_type == 2) {
                $this->db->where('tblprojects.assigned', $userid);
            }
        }*/
        //add brand id clause for logged in user
        if ($viewtype != 'vendor-view') {
            /*if (!is_brand_private()) {*/
            $this->db->where('tblinvite.brandid', get_user_session());
            /*}*/
        }
        $this->db->where('tblinvite.deleted', 0);
        if (isset($where['projectid'])) {
            $this->db->where('tblprojects.id', $where['projectid']);
        }
        if (isset($where['contacttype'])) {
            $this->db->where('contacttype', $where['contacttype']);
        }
        //id exists, get specific id details
        if ($id != '') {
            $this->db->where('tblinvite.inviteid', $id);
            $invite = $this->db->get('tblinvite')->row();
            //get all projects and/or sub project details for given invite id
            $query = $this->db->query('SELECT `inviteid`, `projectid`, (SELECT `name` FROM `tblprojects` WHERE `id` = `tblinvitestatus`.`projectid`) AS project_name, (SELECT `tbleventtype`.`eventtypename` FROM `tbleventtype` WHERE `tbleventtype`.`eventtypeid` = (SELECT `eventtypeid` FROM `tblprojects` WHERE `id` = `tblinvitestatus`.`projectid`)) as project_type, (SELECT `eventstartdatetime` FROM `tblprojects` WHERE `id` = `tblinvitestatus`.`projectid`) AS eventstartdatetime, (SELECT `eventenddatetime` FROM `tblprojects` WHERE `id` = `tblinvitestatus`.`projectid`) AS eventenddatetime, (SELECT t1.`status` FROM `tblinvitestatus` t1 WHERE t1.`inviteid` = `tblinvitestatus`.`inviteid` AND t1.`projectid` = `tblinvitestatus`.`projectid` ORDER BY t1.`datecreated` DESC LIMIT 0,1) AS status, (SELECT t1.`comments` FROM `tblinvitestatus` t1 WHERE t1.`inviteid` = `tblinvitestatus`.`inviteid` AND t1.`projectid` = `tblinvitestatus`.`projectid` ORDER BY t1.`datecreated` DESC LIMIT 0,1) AS comments, (SELECT GROUP_CONCAT(`permissionid`) FROM `tbleventpermission` WHERE `deleted` = 0 AND `inviteid` = `tblinvitestatus`.`inviteid` AND `projectid` = `tblinvitestatus`.`projectid`) AS permission_id,(SELECT GROUP_CONCAT(`name`) FROM `tbleventpermission` JOIN `tblpermissions` ON `tblpermissions`.`permissionid` = `tbleventpermission`.`permissionid` WHERE `deleted` = 0 AND `inviteid` = `tblinvitestatus`.`inviteid` AND `projectid` = `tblinvitestatus`.`projectid`) AS permission_name FROM `tblinvitestatus` WHERE `inviteid` = ' . $id . ' GROUP BY `tblinvitestatus`.`inviteid`, `tblinvitestatus`.`projectid`');
            $invite->events = $query->result_array();
            $query = $this->db->query('SELECT * FROM `tblinvitestatus` WHERE `inviteid` = ' . $id);
            $invite->invitestatuses = $query->result_array();
            $this->db->select('tblstaff.staffid');
            $this->db->join('tblstaff', 'tblstaff.staffid = tblprojectcontact.contactid');
            $this->db->where('tblprojectcontact.isclient', 1);
            $this->db->where('tblprojectcontact.projectid', $invite->projectid);
            $clients = $this->db->get('tblprojectcontact')->result_array();
            $clients = array_map('current', $clients);
            $invite->clients = $clients;
            $query = $this->db->query('SELECT * FROM `tblinvitestatus` WHERE `inviteid` = ' . $id . ' AND `usertype`="invitee"');
            $invite->invitee = $query->row();

            $query = $this->db->query('SELECT * FROM `tblinvitestatus` WHERE `inviteid` = ' . $id . ' AND `usertype`="venue"');
            $invite->venueinvitee = $query->row();
            $query = $this->db->query('SELECT `name` FROM `tbleventpermission` JOIN `tblpermissions` ON `tblpermissions`.`permissionid` = `tbleventpermission`.`permissionid` WHERE `tbleventpermission`.`deleted` = 0 AND `tbleventpermission`.`inviteid` =' . $id . ' AND `tbleventpermission`.`projectid` = ' . $invite->projectid);
            $permission = $query->result_array();
            $invite->permissions = $permission;
            return $invite;
        }

        //get all project permission assigned to staff
        if (isset($where['staffid'])) {
            $this->db->select('GROUP_CONCAT(`tblinvite`.`inviteid`) AS invite');
            $this->db->where('tblinvite.staffid', $where['staffid']);
            $invitedetails = $this->db->get('tblinvite')->row();

            //get tags
            $query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS tag_name FROM `tbltags` WHERE `deleted` = 0 AND `id` IN (' . $invitedetails->tags . ')');
            $tags = $query->row();
            $invitedetails->assigned_tags = $tags->tag_name;

            //get permissions
            $query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS permission_name FROM `tbleventpermission` JOIN `tblpermissions` ON `tblpermissions`.`permissionid` = `tbleventpermission`.`permissionid` JOIN `tblinvitestatus` ON `tblinvitestatus`.`inviteid` = `tbleventpermission`.`inviteid` WHERE `tbleventpermission`.`deleted` = 0 AND `tbleventpermission`.`inviteid` IN (' . $invitedetails->invite . ') AND `tbleventpermission`.`projectid` = ' . $invitedetails->pid . ' AND `tblinvitestatus`.`status` = "' . $this->invite_vendor_status[3] . '"');
            $permission = $query->row();
            $invitedetails->permission_name = $permission->permission_name;

            return $invitedetails;
        }

        //get all project permission assigned to contact
        if (isset($where['contactid'])) {
            $this->db->select('GROUP_CONCAT(`tblinvite`.`inviteid`) AS invite');
            $this->db->where('tblinvite.contactid', $where['contactid']);
            $invitedetails = $this->db->get('tblinvite')->row();

            //get tags
            $query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS tag_name FROM `tbltags` WHERE `deleted` = 0 AND `id` IN (' . $invitedetails->tags . ')');
            $tags = $query->row();
            $invitedetails->assigned_tags = $tags->tag_name;

            //get permissions
            $query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS permission_name FROM `tbleventpermission` JOIN `tblpermissions` ON `tblpermissions`.`permissionid` = `tbleventpermission`.`permissionid` JOIN `tblinvitestatus` ON `tblinvitestatus`.`inviteid` = `tbleventpermission`.`inviteid` WHERE `tbleventpermission`.`deleted` = 0 AND `tbleventpermission`.`inviteid` IN (' . $invitedetails->invite . ') AND `tbleventpermission`.`projectid` = ' . $invitedetails->pid . ' AND `tblinvitestatus`.`status` = "' . $this->invite_vendor_status[3] . '"');
            $permission = $query->row();
            $invitedetails->permission_name = $permission->permission_name;

            return $invitedetails;
        }

        //get all project permission assigned to venue
        if (isset($where['contactid'])) {
            $this->db->select('GROUP_CONCAT(`tblinvite`.`inviteid`) AS invite');
            $this->db->where('tblinvite.contactid', $where['contactid']);
            $invitedetails = $this->db->get('tblinvite')->row();

            //get tags
            $query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS tag_name FROM `tbltags` WHERE `deleted` = 0 AND `id` IN (' . $invitedetails->tags . ')');
            $tags = $query->row();
            $invitedetails->assigned_tags = $tags->tag_name;

            //get permissions
            $query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS permission_name FROM `tbleventpermission` JOIN `tblpermissions` ON `tblpermissions`.`permissionid` = `tbleventpermission`.`permissionid` JOIN `tblinvitestatus` ON `tblinvitestatus`.`inviteid` = `tbleventpermission`.`inviteid` WHERE `tbleventpermission`.`deleted` = 0 AND `tbleventpermission`.`inviteid` IN (' . $invitedetails->invite . ') AND `tbleventpermission`.`projectid` = ' . $invitedetails->pid . ' AND `tblinvitestatus`.`status` = "' . $this->invite_vendor_status[3] . '"');
            $permission = $query->row();
            $invitedetails->permission_name = $permission->permission_name;

            return $invitedetails;
        }

        /*if (!empty($status)) {
            $this->db->like('tblinvite.status', $status);
            if ($status == "Pending") {
                $this->db->or_like('tblinvite.status', "Sent");
            }elseif ($status == "Accepted") {
                $this->db->or_like('tblinvite.status', "Approved");
            }
        }*/
        if (!empty($status)) {
            if (strtolower($status) == "pending") {
                /*$this->db->like('tblinvite.status', $status);
                $this->db->or_like('tblinvite.status', "Sent");*/
                //$this->db->where('(tblinvite.status LIKE "%' . $status . '%" ESCAPE \'!\' OR tblinvite.status LIKE "%Sent%" ESCAPE \'!\')');
                $this->db->where('(tblinvite.status = "' . $status . '")');
            } elseif (strtolower($status) == "accepted") {
                /*$this->db->like('tblinvite.status', $status);
                $this->db->or_like('tblinvite.status', "Approved");*/
                //$this->db->where('(tblinvite.status LIKE "%' . $status . '%" ESCAPE \'!\' OR tblinvite.status LIKE "%Approved%" ESCAPE \'!\')');
                $this->db->where('(tblinvite.status = "' . $status . '")');
            } else {
                $this->db->where('tblinvite.status', $status);
            }
        }
        if (!empty($search)) {
            $this->db->like('tblinvite.firstname', $search);
            $this->db->or_like('tblinvite.lastname', $search);
        }
        $this->db->order_by('tblinvite.inviteid', 'desc');
        $this->db->group_by('tblinvite.inviteid');
        if ($is_kanban == true && $limit > 0) {
            $start = ($page - 1) * $limit;
            $this->db->limit($limit, $start);
        }
        //$this->db->join('tblinvitestatus', 'tblinvitestatus.inviteid = tblinvite.inviteid','inner'); //tblinvitestatus.status as invite_state2
        //$this->db->where('tblinvite.inviteid=tblinvitestatus.inviteid');
        //$this->db->where('tblinvitestatus.staffid',get_staff_user_id());
        //$this->db->where('tblinvitestatus.deleted=0');
        $FinalResujlt = $this->db->get('tblinvite')->result_array();
        //echo $this->db->last_query();
        //die('<--here');
        /*echo "<pre>";
        print_r($FinalResujlt);
        die();*/
        $arry = array();
        foreach ($FinalResujlt as $key => $fn) {
            //$FinalResujlt[$key]['status'] = $this->get_statusname_for_invitee($fn['inviteid']->status);
            $stts_invitee = $this->get_statusname_for_invitee($fn['inviteid']);
            $FinalResujlt[$key]['invitestatus'] = !empty($stts_invitee) ? $stts_invitee->status : "";
            //echo '<pre>1-->'; print_r($key); echo '</pre>';
            //echo '<pre>1-->'; print_r($fn['status']); echo '</pre>';
            //echo '<pre>2-->'; print_r($this->get_statusname_for_invitee($fn['inviteid'])); echo '</pre>';
        }
        //echo '<pre>2-->'; print_r($FinalResujlt); echo '</pre>';			 die;
        return $FinalResujlt;
    }


    function get_statusname_for_invitee($id)
    {

        $query = "SELECT status FROM tblinvitestatus WHERE inviteid=" . $id;
        return $this->db->query($query)->row();
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/04/2017
     * check for unique email
     */
    function check_account_exists($email)
    {
        $where = array('email = ' => $email, 'deleted = ' => 0);

        $res = $this->db->where($where)->get('tblstaff')->row();
        $res1 = ((count($res) > 0) ? count($res) : 0);
        //check email id exists in staff or address book table when chosing existing contact
        $query = $this->db->query('SELECT `tbladdressbook`.* FROM `tbladdressbook` LEFT JOIN `tbladdressbookemail` ON `tbladdressbookemail`.`addressbookid` = `tbladdressbook`.`addressbookid` WHERE `tbladdressbook`.`ispublic` = 1 AND `tbladdressbook`.`deleted` = 0 AND `tbladdressbookemail`.`email` = "' . $email . '" UNION ALL SELECT `tbladdressbook`.* FROM `tbladdressbook` LEFT JOIN `tbladdressbookemail` ON `tbladdressbookemail`.`addressbookid` = `tbladdressbook`.`addressbookid` LEFT JOIN `tbladdressbook_client` ON `tbladdressbook_client`.`addressbookid` = `tbladdressbook`.`addressbookid` WHERE `tbladdressbook`.`ispublic` = 0 AND `tbladdressbook`.`deleted` = 0 AND `tbladdressbook_client`.`deleted` = 0 AND`tbladdressbookemail`.`email` = "' . $email . '"  AND `tbladdressbook_client`.`brandid` = ' . get_user_session());
        $contact_res = $query->row();
        $data = array('addressbookid' => "", 'addressbookemailid' => "", 'email' => $email);
        $res2 = $this->misc_model->addressbook_email_exists($data);
        //$res2 = ((count($contact_res) > 0) ? count($contact_res) : 0);
        if ($res1 == 0 && $res2 == 0) {
            return true;
        }

        return false;
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/09/2018
     * to update invite status
     */
    function update_invite_status($data)
    {
        unset($data['projectid']);
        unset($data['contacttype']);
        unset($data['vendor_accept']);
        $userid = get_staff_user_id();
        $inviteid = $data['inviteid'];
        $newstatus = $data['status'];
        $usertype = "";

        $this->db->select('userid,usertype');
        $this->db->where('inviteid', $data['inviteid']);
        $inviteusers = $this->db->get('tblinvitestatus')->result_array();

        $this->db->where('inviteid', $data['inviteid']);
        $invitedetails = $this->db->get('tblinvite')->row();
        if (!is_staff_logged_in() || $invitedetails->staffid == $userid) {
            $usertype = "invitee";
            $invitedetails = $this->get_invitees($data['inviteid'], 'vendor-view');
        } else {
            $invitedetails = $this->get_invitees($data['inviteid']);
        }
        if ($invitedetails->contacttype == 3) {
            $inv_type = "Vendor";
        } elseif ($invitedetails->contacttype == 4) {
            $inv_type = "Collaborator";
        } else {
            $inv_type = "Venue";
        }

        $clients = $invitedetails->clients;
        $invited_by_type = in_array($invitedetails->invitedby, $clients) ? "client" : "member";
        $this->db->where('inviteid', $data['inviteid']);
        if (is_staff_logged_in()) {
            $this->db->where('userid', $userid);
        } else {
            $this->db->where('(usertype="invitee" OR usertype="venue")');
        }
        $curentUserInvitesStatus = $this->db->get('tblinvitestatus')->row();

        $this->db->where('inviteid', $data['inviteid']);
        $this->db->where('(usertype="invitee" OR usertype="venue")');
        $InviteesStatus = $this->db->get('tblinvitestatus')->row();

        $oldstatus = $curentUserInvitesStatus->status;
        $usertype = $curentUserInvitesStatus->usertype;
        $userid = $curentUserInvitesStatus->userid;
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $data['updated_by'] = $userid;
        $this->db->where('inviteid', $data['inviteid']);
        $this->db->where('userid', $userid);
        $this->db->update('tblinvitestatus', $data);

        if (is_staff_logged_in()) {
            $currentuser = get_staff_user_id();
        } else {
            $currentuser = $invitedetails->staffid;
            if($invitedetails->contacttype == 5){
                $currentuser = $invitedetails->venueid;
            }
        }
        if ($newstatus == "declined") {
            $this->db->where('inviteid', $data['inviteid']);
            $this->db->update('tblinvite', array('status' => 'declined'));

            $this->db->where('contactid', $invitedetails->staffid);
            $this->db->where('projectid', $invitedetails->projectid);
            $this->db->update('tblprojectcontact', array('active' => 0));
            $diclined_by = get_staff_full_name($currentuser);

            foreach ($inviteusers as $inviteuser) {
                if ($inviteuser['userid'] > 0) {
                    $inviteuserid = $inviteuser['userid'];
                } else {
                    $inviteuserid = $invitedetails->staffid;
                }
                $firstname = get_staff_first_name($inviteuserid);
                $client_email = get_staff_email($inviteuserid);
                $eventname = $invitedetails->project_name;
                $message = '<h2><span style="font-size: 14pt;">Hi ' . $firstname . '</span>,</h2><br/><br/>';
                $message .= $inv_type . ' invitation for the event ' . $eventname . ' is declined by ' . $diclined_by . '<br/><br/>';
                if ($inviteuserid != $currentuser) {
                    if ($inviteuser['userid'] == 0) {
                        if ($InviteesStatus->status == "approved" || $invited_by_type == "client") {
                            $message = '<h2><span style="font-size: 14pt;">Hi ' . $firstname . '</span>,</h2><br/><br/>';
                            $message .= 'Your ' . $inv_type . ' invitation for the event <b>' . $eventname . '</b> is declined by ' . $diclined_by . '<br/><br/>';
                            $this->emails_model->send_simple_email($client_email, $inv_type . " invitation declined", $message);
                        }
                    } else {
                        $this->emails_model->send_simple_email($client_email, $inv_type . " invitation declined", $message);
                    }
                }
            }

            /*$merge_fields = array();
            $merge_fields = array_merge($merge_fields, get_decline_merge_field($data['inviteid'], $invitedetails->contacttype, 'sent-by-vendor'));


            if (is_staff_logged_in()) {
                $client = $userid;
            } else {
                $client = $invitedetails->staffid;
            }
            $staffdetails['{name}'] = get_staff_first_name($client);
            $merge_fields = array_merge($merge_fields, $staffdetails);
            $this->load->model('emails_model');
            array_push($clients, $invitedetails->invitedby);
            foreach ($clients as $client) {
                $clientEmail = get_staff_email($client);
                if ($invitedetails->contacttype == 3) {
                    $send = $this->emails_model->send_email_template('decline-vendor', $clientEmail, $merge_fields);
                }
                //for collaborator
                if ($invitedetails->contacttype == 4) {
                    $send = $this->emails_model->send_email_template('decline-collaborator', $clientEmail, $merge_fields);
                }
                //for venue
                if ($invitedetails->contacttype == 5) {
                    $send = $this->emails_model->send_email_template('decline-venue', $clientEmail, $merge_fields);
                }
            }
            if (!$send) {
                logActivity('Decline email could not be sent to [Email Address:' . $invitedetails->invited_email . ' for invitation: ' . $data['inviteid'] . ' IP:' . $this->input->ip_address() . ']');
                return 'Mail not sent';
            }*/

        } elseif ($newstatus == "approved") {

            /*if ($currentuser == $invitedetails->staffid) {
                $this->db->update('tblstaff',array('active'=>1,));
            }*/
            $approved_by = get_staff_full_name($currentuser);
            if($invitedetails->contacttype == 5){
                $approved_by = get_venue_name($currentuser);
            }
            foreach ($inviteusers as $inviteuser) {
                if ($inviteuser['userid'] > 0) {
                    $inviteuserid = $inviteuser['userid'];
                } else {
                    $inviteuserid = $invitedetails->staffid;
                }
                $firstname = get_staff_first_name($inviteuserid);
                $client_email = get_staff_email($inviteuserid);
                $eventname = $invitedetails->project_name;
                $message = '<h2><span style="font-size: 14pt;">Hi ' . $firstname . '</span>,</h2><br/><br/>';
                $message .= $inv_type . ' invitation for the event ' . $eventname . ' is approved by ' . $approved_by . '<br/><br/>';
                if ($inviteuserid != $currentuser && $InviteesStatus->status != "declined") {
                    if ($inviteuser['userid'] == 0) {

                        if ($invited_by_type == "client") {
                            $message = '<h2><span style="font-size: 14pt;">Hi ' . $firstname . '</span>,</h2><br/><br/>';
                            $message .= 'Your ' . $inv_type . ' invitation for the event <b>' . $eventname . '</b> is approved by ' . $approved_by . '<br/><br/>';
                            $this->emails_model->send_simple_email($client_email, $inv_type . " invitation approved", $message);
                        } else {
                            if (($invitedetails->status == "pending" || $InviteesStatus->status == "pending") && $invitedetails->invitetype == "new") {

                                $merge_fields = array();
                                $merge_fields = array_merge($merge_fields, get_invite_merge_field($inviteid, $invitedetails->contacttype, 'sent-to-vendor'));
                                $staffdetails['{name}'] = get_staff_first_name($invitedetails->staffid);
                                $staffdetails['{vendor_password'] = get_staff_randum_password($invitedetails->staffid);
                                $merge_fields = array_merge($merge_fields, $staffdetails);

                                //for vendor
                                if ($invitedetails->contacttype == 3) {
                                    $send = $this->emails_model->send_email_template('invite-new-vendor', $client_email, $merge_fields);
                                }

                                //for collaborator
                                if ($invitedetails->contacttype == 4) {
                                    $send = $this->emails_model->send_email_template('invite-new-project-collaborator', $client_email, $merge_fields);
                                }
                            } elseif (($invitedetails->status == "pending" || $InviteesStatus->status == "pending") && $invitedetails->invitetype == "existing") {
                                $merge_fields = array();
                                $merge_fields = array_merge($merge_fields, get_invite_merge_field($invitedetails->inviteid, $invitedetails->contacttype, 'sent-to-vendor'));
                                $staffdetails['{name}'] = get_staff_first_name($invitedetails->staffid);
                                $vendor_email = get_staff_email($invitedetails->staffid);
                                //for vendor
                                if ($invitedetails->contacttype == 3) {
                                    $send = $this->emails_model->send_email_template('invite-vendor', $vendor_email, $merge_fields);
                                }

                                //for collaborator
                                if ($invitedetails->contacttype == 4) {
                                    $send = $this->emails_model->send_email_template('invite-collaborator', $vendor_email, $merge_fields);
                                }
                            } else {

                                $message = '<h2><span style="font-size: 14pt;">Hi ' . $firstname . '</span>,</h2><br/><br/>';
                                $message .= 'Your ' . $inv_type . ' invitation for the event <b>' . $eventname . '</b> is approved by ' . $approved_by . '<br/><br/>';
                                $this->emails_model->send_simple_email($client_email, $inv_type . " invitation approved", $message);
                            }
                        }
                    } else {
                        $this->emails_model->send_simple_email($client_email, $inv_type . " invitation approved", $message);
                    }
                }
            }

            $this->db->where('inviteid', $inviteid);
            $this->db->update('tblinvite', array('status' => 'approved'));

            if ($invitedetails->invitetype == "new" && $invitedetails->staffid == $currentuser) {
                $this->db->where('staffid', $invitedetails->staffid);
                $this->db->update('tblstaff', array('active' => 1));
            }
            $this->db->where('contactid', $invitedetails->staffid);
            $this->db->where('projectid', $invitedetails->projectid);
            $this->db->update('tblprojectcontact', array('active' => 1));

        } /*elseif ($newstatus == "approved" && $invitedetails->status == "pending" && $userid > 0) {
            $this->db->where('inviteid', $data['inviteid']);
            $this->db->update('tblinvite', array('status' => 'approved'));

            $merge_fields = array();
            $merge_fields = array_merge($merge_fields, get_invite_merge_field($data['inviteid'], $data['contacttype'], 'sent-to-vendor'));

            $vendor_email = get_staff_email($invitedetails->staffid);
            //for vendor
            if ($invitedetails->contacttype == 3) {
                $send = $this->emails_model->send_email_template('invite-vendor', $vendor_email, $merge_fields);
            }

            //for collaborator
            if ($invitedetails->contacttype == 4) {
                $send = $this->emails_model->send_email_template('invite-collaborator', $vendor_email, $merge_fields);
            }
        }*/
        return true;

        /*if ($this->db->affected_rows() > 0) {
            if ($data['status'] == $this->invite_vendor_status[4] || $data['status'] == $this->invite_collaborator_status[4] || $data['status'] == $this->invite_venue_status[4]) {
                $invitedetails = $this->get_invitees($data['inviteid'], 'vendor-view');
            } else {
                $invitedetails = $this->get_invitees($data['inviteid'], 'vendor-view');
            }

            //if account owner declines invite, send email to team member who has send invite
            if ((isset($invitedetails->email) || $invitedetails->email != '') && ($data['status'] == 'Accepted by Vendor' || $data['status'] == 'Accepted by Collaborator')) {
                $staff_data = [];
                $staff_data['active'] = 1;
                $staff_data['updated_date'] = date('Y-m-d H:i:s');
                $this->db->where('email', $invitedetails->email);
                $this->db->update('tblstaff', $staff_data);

                logActivity('User status changed [Email Id: ' . $invitedetails->email . ', Status: Active]');
            }

            //if invited vendor declines invite, send email to team member/account owner has send invite
            if (isset($data['status']) && ($data['status'] == $this->invite_vendor_status[4]) || $data['status'] == $this->invite_collaborator_status[4] || $data['status'] == $this->invite_venue_status[4]) {

                $merge_fields = array();
                $merge_fields = array_merge($merge_fields, get_decline_merge_field($data['inviteid'], $invitedetails->contacttype, 'sent-by-vendor'));
                $this->load->model('emails_model');

                //for vendor
                if ($invitedetails->contacttype == 3) {
                    $send = $this->emails_model->send_email_template('decline-vendor', $invitedetails->invited_email, $merge_fields);
                }

                //for collaborator
                if ($invitedetails->contacttype == 4) {
                    $send = $this->emails_model->send_email_template('decline-collaborator', $invitedetails->invited_email, $merge_fields);
                }

                //for venue
                if ($invitedetails->contacttype == 5) {
                    $send = $this->emails_model->send_email_template('decline-venue', $invitedetails->invited_email, $merge_fields);
                }

                if (!$send) {
                    logActivity('Decline email could not be sent to [Email Address:' . $invitedetails->invited_email . ' for invitation: ' . $data['inviteid'] . ' IP:' . $this->input->ip_address() . ']');
                    return 'Mail not sent';
                }
            }

            return true;
        }

        return false;*/
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/10/2018
     * to send invite via email
     */
    function send_invite($inviteid)
    {
        $this->load->model('emails_model');
        $invitedetails = $this->get_invitees($inviteid);

        $merge_fields = array();
        $merge_fields = array_merge($merge_fields, get_invite_merge_field($inviteid, $invitedetails->contacttype, 'sent-to-vendor'));

        $declinecnt = 0;
        $approvecnt = 0;

        foreach ($invitedetails->events as $event) {
            //if account owner approves invite and sent invite to vendor

            //for vendor
            if ($invitedetails->contacttype == 3) {
                $status = $this->invite_vendor_status[2];
            }

            //for collaborator
            if ($invitedetails->contacttype == 4) {
                $status = $this->invite_collaborator_status[2];
            }

            //for venue
            if ($invitedetails->contacttype == 5) {
                $status = $this->invite_venue_status[2];
            }

            if ($event['status'] == $this->invite_vendor_status[1] || $event['status'] == $status) {
                //for vendor
                if ($invitedetails->contacttype == 3) {
                    $data['status'] = $this->invite_vendor_status[2];
                }

                //for collaborator
                if ($invitedetails->contacttype == 4) {
                    $data['status'] = $this->invite_collaborator_status[2];
                }

                //for venue
                if ($invitedetails->contacttype == 5) {
                    $data['status'] = $this->invite_venue_status[2];
                }

                $data['projectid'] = $event['projectid'];
                $data['inviteid'] = $inviteid;
                $data['created_by'] = $this->session->userdata['staff_user_id'];
                $data['datecreated'] = date('Y-m-d H:i:s');

                $invite = [];
                $invite['deleted'] = 1;
                $invite['dateupdated'] = date('Y-m-d H:i:s');
                $this->db->where('inviteid', $inviteid);
                $this->db->update('tblinvitestatus', $invite);

                $this->db->insert('tblinvitestatus', $data);
                $invite = [];
                $invite['status'] = $data['status'];
                $invite['dateupdated'] = date('Y-m-d H:i:s');
                $this->db->where('inviteid', $data['inviteid']);
                $this->db->update('tblinvite', $invite);
                $approvecnt++;
            } else if ($event['status'] == 'Declined by Account Owner') {
                $declinecnt++;
            }
        }

        if ($approvecnt > 0) {
            if ($invitedetails->contacttype == 3 || $invitedetails->contacttype == 4) {
                //send email to existing staff or contact, with all relevant projects and/or sub projects details under single invite
                if (isset($invitedetails->staffid)) {
                    $account_user_query = $this->db->query('SELECT tblstaff.* FROM tblstaff WHERE staffid = ' . $invitedetails->staffid);
                    $results = $account_user_query->row();
                    $vendor_email = $results->email;
                    $firstname = $results->firstname;

                    $staffdetails['{name}'] = $firstname;
                    $merge_fields = array_merge($merge_fields, $staffdetails);

                    //for vendor
                    if ($invitedetails->contacttype == 3) {
                        $send = $this->emails_model->send_email_template('invite-vendor', $vendor_email, $merge_fields);
                    }

                    //for collaborator
                    if ($invitedetails->contacttype == 4) {
                        $send = $this->emails_model->send_email_template('invite-collaborator', $vendor_email, $merge_fields);
                    }
                } elseif (isset($invitedetails->contactid) && $invitedetails->contactid > 0) {
                    $account_user_query = $this->db->query('SELECT `email`, `firstname` FROM `tbladdressbookemail` JOIN `tbladdressbook` ON `tbladdressbook`.`addressbookid` = `tbladdressbookemail`.`addressbookid` WHERE `type` = "primary" AND `tbladdressbook`.`addressbookid` = ' . $invitedetails->contactid);
                    $results = $account_user_query->row();
                    $vendor_email = $results->email;
                    $firstname = $results->firstname;

                    $staffdetails['{name}'] = $firstname;
                    $merge_fields = array_merge($merge_fields, $staffdetails);

                    //for vendor
                    if ($invitedetails->contacttype == 3) {
                        $send = $this->emails_model->send_email_template('invite-vendor', $vendor_email, $merge_fields);
                    }

                    //for collaborator
                    if ($invitedetails->contacttype == 4) {
                        $send = $this->emails_model->send_email_template('invite-collaborator', $vendor_email, $merge_fields);
                    }
                } else {
                    //create new account, for new invited vendor
                    $vendor_email = $invitedetails->email;
                    $firstname = $invitedetails->firstname;

                    $where = array('email = ' => $vendor_email, 'deleted = ' => 0);

                    //check if account already exists
                    $res = $this->db->where($where)->get('tblstaff')->row();
                    if (count($res) > 0) {
                        //if exists, do not send credentials in email
                        if ($res->last_login == NULL || $res->last_login == null) {
                            $staffdetails['{name}'] = $firstname;
                            $staffdetails['{vendor_password'] = $res->random_pass;
                            $merge_fields = array_merge($merge_fields, $staffdetails);

                            //for vendor
                            if ($invitedetails->contacttype == 3) {
                                $send = $this->emails_model->send_email_template('invite-new-vendor', $vendor_email, $merge_fields);
                            }

                            //for collaborator
                            if ($invitedetails->contacttype == 4) {
                                $send = $this->emails_model->send_email_template('invite-new-project-collaborator', $vendor_email, $merge_fields);
                            }
                        } else {
                            //for vendor
                            if ($invitedetails->contacttype == 3) {
                                //else send credentials in email
                                $send = $this->emails_model->send_email_template('invite-vendor', $vendor_email, $merge_fields);
                            }

                            //for collaborator
                            if ($invitedetails->contacttype == 4) {
                                //else send credentials in email
                                $send = $this->emails_model->send_email_template('invite-collaborator', $vendor_email, $merge_fields);
                            }
                        }
                    } else {
                        //create new account and send email with all details for invite with account credentials
                        $query = $this->db->query('SELECT packageid FROM tblpackages WHERE name = "Free Package"');
                        $package = $query->row();
                        $password = $this->randomPassword();

                        $staffdata = [];
                        $staffdata['firstname'] = $firstname;
                        $staffdata['lastname'] = $invitedetails->lastname;
                        $staffdata['email'] = $vendor_email;
                        $staffdata['password'] = $password;
                        $staffdata['random_pass'] = $password;
                        $staffdata['created_by'] = $this->session->userdata['staff_user_id'];
                        $staffdata['datecreated'] = date('Y-m-d H:i:s');
                        $staffdata['active'] = 0;
                        $staffdata['facebook'] = null;
                        $staffdata['twitter'] = null;
                        $staffdata['google'] = null;
                        $staffdata['brandname'] = $firstname;
                        $staffdata['brandtype'] = 1;
                        $staffdata['packagetype'] = (isset($package->packageid) ? $package->packageid : 2);

                        $this->load->model('register_model');
                        $this->register_model->saveclient($staffdata);

                        logActivity('New User Created [Email Address:' . $vendor_email . ' for invitation: ' . $inviteid . ' IP:' . $this->input->ip_address() . ']');

                        $where = array('email' => $vendor_email, 'deleted' => 0);
                        $staff_det = $this->db->where($where)->get('tblstaff')->row();

                        $where = array('id' => $invitedetails->projectid, 'deleted' => 0);
                        $project = $this->db->where($where)->get('tblproject')->row();
                        if ($project->parent == 0) {
                            $projectid = $data['projectid'];
                        } else {
                            $projectid = $project->parent;
                        }

                        $project_contact['projectid'] = $projectid;
                        $project_contact['contactid'] = $staff_det->staffid;
                        $project_contact['brandid'] = get_user_session();

                        //for vendor
                        if ($invitedetails->contacttype == 3) {
                            $project_contact['isvendor'] = 1;
                            $project_contact['iscollaborator'] = 0;
                        }

                        //for collaborator
                        if ($invitedetails->contacttype == 4) {
                            $project_contact['isvendor'] = 0;
                            $project_contact['iscollaborator'] = 1;
                        }

                        $this->db->insert('tblprojectcontact', $project_contact);

                        //update staff id in invite table
                        $invite_data = [];
                        $invite_data['staffid'] = $staff_det->staffid;
                        $invite_data['updated_by'] = (isset($this->session->userdata['staff_user_id']) ? $this->session->userdata['staff_user_id'] : 0);
                        $invite_data['dateupdated'] = date('Y-m-d H:i:s');
                        $this->db->where('inviteid', $inviteid);
                        $this->db->update('tblinvite', $invite_data);

                        $staff_brand = [];
                        $staff_brand['active'] = 1;
                        $staff_brand['staffid'] = $staff_det->staffid;
                        $staff_brand['brandid'] = get_user_session();
                        $this->db->insert('tblstaffbrand', $staff_brand);
                        /**
                         * Added By : Masud
                         * Dt : 03/23/2018
                         * prefill dashboard values
                         */
                        $dashboard_data = array();
                        $dashboard_data['staffid'] = $staff_det->staffid;
                        $dashboard_data['widget_type'] = 'upcoming_project,pinned_item,calendar,weather,favourite,quick_link,lead_pipeline,message,getting_started,task_list,contacts,messages';
                        $dashboard_data['quick_link_type'] = 'lead,project,message,task_due,meeting,amount_receivable,amount_received,invite';
                        $dashboard_data['order'] = '[{"widget_name":"getting_started","order":0},{"widget_name":"lead_pipeline","order":1},{"widget_name":"calendar","order":2},{"widget_name":"pinned_item","order":3},{"widget_name":"quick_link","order":4},{"widget_name":"upcoming_project","order":5},{"widget_name":"contacts","order":6},{"widget_name":"messages","order":7},{"widget_name":"task_list","order":8}]';
                        $dashboard_data['is_visible'] = 1;
                        $dashboard_data['brandid'] = get_user_session();
                        $dashboard_data['dateadded'] = date('Y-m-d H:i:s');
                        $dashboard_data['addedby'] = $staff_det->staffid;
                        $this->db->insert('  tbldashboard_settings', $dashboard_data);
                        $staffdetails['{name}'] = $firstname;
                        $staffdetails['{vendor_password'] = $password;
                        $merge_fields = array_merge($merge_fields, $staffdetails);

                        //for vendor
                        if ($invitedetails->contacttype == 3) {
                            $send = $this->emails_model->send_email_template('invite-new-vendor', $vendor_email, $merge_fields);
                        }

                        //for collaborator
                        if ($invitedetails->contacttype == 4) {
                            $send = $this->emails_model->send_email_template('invite-new-project-collaborator', $vendor_email, $merge_fields);
                        }
                    }
                }
            } else if ($invitedetails->contacttype == 5) {
                $account_user_query = $this->db->query('SELECT tblvenue.* FROM tblvenue WHERE venueid = ' . $invitedetails->venueid);
                $results = $account_user_query->row();
                $venueemail = $results->venueemail;
                $venuename = $results->venuename;

                $staffdetails['{name}'] = $venuename;
                $merge_fields = array_merge($merge_fields, $staffdetails);

                $send = $this->emails_model->send_email_template('invite-venue', $venueemail, $merge_fields);
            }

            if (!$send) {
                logActivity('Invitation email could not be sent to [Email Address:' . $vendor_email . ' for invitation: ' . $inviteid . ' IP:' . $this->input->ip_address() . ']');
                return 'Mail not sent';
            }

        }

        if ($declinecnt > 0) {
            $merge_fields = array();
            $merge_fields = array_merge($merge_fields, get_decline_merge_field($inviteid));

            $send = $this->emails_model->send_email_template('decline-invite', $invitedetails->invited_email, $merge_fields);

            if (!$send) {
                logActivity('Decline email could not be sent to [Email Address:' . $invitedetails->invited_email . ' for invitation: ' . $inviteid . ' IP:' . $this->input->ip_address() . ']');
                return 'Mail not sent';
            }
        }

        return 'Mail sent';
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/10/2018
     * to send invite via email
     */
    function resend_invite($inviteid, $type = '', $usertype = "", $userid = "")
    {
        $this->load->model('emails_model');
        $invitedetails = $this->get_invitees($inviteid);
        if ($type == 'account-owner') {
            foreach ($invitedetails->events as $event) {
                $data['status'] = $this->invite_vendor_status[0];
                $data['projectid'] = $event['projectid'];
                $data['inviteid'] = $inviteid;
                $data['created_by'] = $this->session->userdata['staff_user_id'];
                $data['datecreated'] = date('Y-m-d H:i:s');

                //$this->db->insert('tblinvitestatus', $data);
            }
        }

        if ($type == 'vendor') {
            //for vendor
            if ($invitedetails->contacttype == 3) {
                foreach ($invitedetails->events as $event) {
                    $data['status'] = $this->invite_vendor_status[2];
                    $data['projectid'] = $event['projectid'];
                    $data['inviteid'] = $inviteid;
                    $data['created_by'] = $this->session->userdata['staff_user_id'];
                    $data['datecreated'] = date('Y-m-d H:i:s');

                    //$this->db->insert('tblinvitestatus', $data);
                }
            }

            //for collaborator
            if ($invitedetails->contacttype == 4) {
                foreach ($invitedetails->events as $event) {
                    $data['status'] = $this->invite_collaborator_status[2];
                    $data['projectid'] = $event['projectid'];
                    $data['inviteid'] = $inviteid;
                    $data['created_by'] = $this->session->userdata['staff_user_id'];
                    $data['datecreated'] = date('Y-m-d H:i:s');

                    //$this->db->insert('tblinvitestatus', $data);
                }
            }

            //for venue
            if ($invitedetails->contacttype == 5) {
                foreach ($invitedetails->events as $event) {
                    $data['status'] = $this->invite_venue_status[2];
                    $data['projectid'] = $event['projectid'];
                    $data['inviteid'] = $inviteid;
                    $data['created_by'] = $this->session->userdata['staff_user_id'];
                    $data['datecreated'] = date('Y-m-d H:i:s');

                    //$this->db->insert('tblinvitestatus', $data);
                }
            }
        }
        if (get_staff_user_id() == $userid && $usertype == "client") {

            $merge_fields = array();
            $merge_fields = array_merge($merge_fields, get_invite_merge_field($inviteid, $invitedetails->contacttype, 'sent-to-vendor'));
            if ($invitedetails->contacttype == 3 || $invitedetails->contacttype == 4) {
                if (isset($invitedetails->staffid)) {
                    $account_user_query = $this->db->query('SELECT tblstaff.* FROM tblstaff WHERE staffid = ' . $invitedetails->staffid);
                    $results = $account_user_query->row();
                    $vendor_email = $results->email;
                    $firstname = $results->firstname;

                    $staffdetails['{name}'] = $firstname;
                    $merge_fields = array_merge($merge_fields, $staffdetails);

                    //for vendor
                    if ($invitedetails->contacttype == 3) {
                        $send = $this->emails_model->send_email_template('invite-vendor', $vendor_email, $merge_fields);
                    }

                    //for collaborator
                    if ($invitedetails->contacttype == 4) {
                        $send = $this->emails_model->send_email_template('invite-collaborator', $vendor_email, $merge_fields);
                    }
                } elseif (isset($invitedetails->contactid) && $invitedetails->contactid > 0) {
                    $account_user_query = $this->db->query('SELECT `email`, `firstname` FROM `tbladdressbookemail` JOIN `tbladdressbook` ON `tbladdressbook`.`addressbookid` = `tbladdressbookemail`.`addressbookid` WHERE `type` = "primary" AND `tbladdressbook`.`addressbookid` = ' . $invitedetails->contactid);
                    $results = $account_user_query->row();

                    $vendor_email = $results->email;
                    $firstname = $results->firstname;

                    $staffdetails['{name}'] = $firstname;
                    $merge_fields = array_merge($merge_fields, $staffdetails);

                    //for vendor
                    if ($invitedetails->contacttype == 3) {
                        $send = $this->emails_model->send_email_template('invite-vendor', $vendor_email, $merge_fields);
                    }

                    //for collaborator
                    if ($invitedetails->contacttype == 4) {
                        $send = $this->emails_model->send_email_template('invite-collaborator', $vendor_email, $merge_fields);
                    }
                } else {
                    $vendor_email = $invitedetails->email;
                    $firstname = $invitedetails->firstname;

                    $staffdetails['{name}'] = $firstname;

                    $merge_fields = array_merge($merge_fields, $staffdetails);

                    //for vendor
                    if ($invitedetails->contacttype == 3) {
                        $send = $this->emails_model->send_email_template('invite-vendor', $vendor_email, $merge_fields);
                    }

                    //for collaborator
                    if ($invitedetails->contacttype == 4) {
                        $send = $this->emails_model->send_email_template('invite-collaborator', $vendor_email, $merge_fields);
                    }
                }
            } else if ($invitedetails->contacttype == 5) {
                $account_user_query = $this->db->query('SELECT tblvenue.* FROM tblvenue WHERE venueid = ' . $invitedetails->venueid);
                $results = $account_user_query->row();
                $venue_email = $results->venueemail;
                $venuename = $results->venuename;

                $staffdetails['{name}'] = $venuename;
                $merge_fields = array_merge($merge_fields, $staffdetails);

                $send = $this->emails_model->send_email_template('invite-venue', $venue_email, $merge_fields);
            }
        } else {
            $merge_fields = array();
            $merge_fields = array_merge($merge_fields, get_invite_merge_field($inviteid, $invitedetails->contacttype));
            $clients = $invitedetails->clients;
            if (count($clients) > 0) {
                foreach ($clients as $client) {
                    $clientEmail = get_staff_email($client);
                    $staffdetails['{name}'] = get_staff_first_name($client);
                    $merge_fields = array_merge($merge_fields, $staffdetails);
                    if ($invitedetails->contacttype == 3) {
                        $send = $this->emails_model->send_email_template('invite-new', $clientEmail, $merge_fields);
                    } //for collaborator
                    elseif ($invitedetails->contacttype == 4) {
                        $send = $this->emails_model->send_email_template('invite-new-collaborator', $clientEmail, $merge_fields);
                    } else {
                        $send = $this->emails_model->send_email_template('invite-new-venue', $clientEmail, $merge_fields);
                    }
                }
            }
        }
        if (!$send) {
            logActivity('Invitation email could not be sent to [Email Address:' . $venue_email . ' for invitation: ' . $inviteid . ' IP:' . $this->input->ip_address() . ']');
            return 'Mail not sent';
        }

        return 'Mail sent';
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/18/2018
     * for edit invite permission
     */
    function editinvitepermission($data)
    {
        $invite = explode(",", $data['inviteid']);
        foreach ($invite as $inviteid) {
            //delete all event permission for specific invite
            $permission_data = [];
            $permission_data['deleted'] = 1;
            $this->db->where('inviteid', $inviteid);
            $this->db->where('projectid', $data['projectid']);
            $this->db->update('tbleventpermission', $permission_data);
        }

        $new_permission = $data['permissions'];

        foreach ($invite as $inviteid) {
            foreach ($new_permission as $permission) {
                //add new event permission for specific invite
                $permission_data = [];
                $permission_data['projectid'] = $data['projectid'];
                $permission_data['permissionid'] = $permission;
                $permission_data['inviteid'] = $inviteid;
                $permission_data['deleted'] = 0;

                $this->db->insert('tbleventpermission', $permission_data);
            }
        }

        logActivity('Invite Permission Updated [Invite Id: ' . $inviteid . ']');

        return true;
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/18/2018
     * for remvoing vendor
     */
    function remove_vendor($data)
    {
        if (isset($data['iscollaborator']) && $data['iscollaborator'] == 1) {
            $contacttype = 4;
        }

        if (isset($data['isvendor']) && $data['isvendor'] == 1) {
            $contacttype = 3;
        }

        //remove venue
        if (isset($data['venueid']) && $data['venueid'] > 0) {
            $contacttype = 5;
        }

        $invitedetails = $this->edit_invite_detail($data['projectid'], $data['staffid'], $data['contactid'], $contacttype, $data['venueid']);
        $invites = explode(",", $invitedetails->invite);
        /*echo "<pre>";
        print_r($invitedetails);
        die('<--here');*/
        if (isset($data['staffid']) && $data['staffid'] > 0) {
            $this->db->where('(projectid=' . $data['projectid'] . " OR eventid=" . $data['projectid'] . ")");
            //$this->db->or_where('eventid', $data['projectid']);
            $this->db->where('contactid', $data['staffid']);

            //for vendor
            if (isset($data['isvendor']) && $data['isvendor'] == 1) {
                $this->db->where('isvendor', 1);
            }

            //for collaborator
            if (isset($data['iscollaborator']) && $data['iscollaborator'] == 1) {
                $this->db->where('iscollaborator', 1);
            }

            $this->db->delete('tblprojectcontact');
        }

        if (isset($data['contactid']) && $data['contactid'] > 0) {
            $this->db->where('(projectid=' . $data['projectid'] . " OR eventid=" . $data['projectid'] . ")");
            //$this->db->or_where('eventid', $data['projectid']);
            $this->db->where('contactid', $data['contactid']);

            //for vendor
            if (isset($data['isvendor']) && $data['isvendor'] == 1) {
                $this->db->where('isvendor', 1);
            }

            //for collaborator
            if (isset($data['iscollaborator']) && $data['iscollaborator'] == 1) {
                $this->db->where('iscollaborator', 1);
            }

            $this->db->delete('tblprojectcontact');
        }

        //remvoe venue
        if (isset($data['venueid']) && $data['venueid'] > 0) {
            $this->db->where('(projectid=' . $data['projectid'] . " OR eventid=" . $data['projectid'] . ")");
            //$this->db->or_where('eventid', $data['projectid']);
            $this->db->where('venueid', $data['venueid']);

            $this->db->delete('tblprojectvenue');
        }

        foreach ($invites as $inviteid) {
            $permission_data = [];
            $permission_data['deleted'] = 1;
            $this->db->where('inviteid', $inviteid);
            $this->db->where('projectid', $invitedetails->pid);
            $this->db->update('tbleventpermission', $permission_data);

            $this->db->where('inviteid', $inviteid);
            $this->db->update('tblinvite', array('deleted' => 1));
        }

        /*foreach ($invites as $inviteid) {
            $permission_data = [];
            $permission_data['deleted'] = 1;
            $permission_data['inviteid'] = $inviteid;
            $permission_data['projectid'] = $invitedetails->pid;
            $permission_data['status'] = 'deleted';
            $permission_data['comments'] = 'Deleted by Account Owner';
            $permission_data['created_by'] = $this->session->userdata['staff_user_id'];
            $permission_data['datecreated'] = date('Y-m-d H:i:s');
            $this->db->insert('tblinvitestatus', $permission_data);
        }*/

        if ($this->db->affected_rows() > 0) {
            logActivity('Remove Invite for [Invite Id: ' . $inviteid . ', Project Id: ' . $data['projectid'] . ']');
        }

        return true;
    }

    function randomPassword()
    {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 6; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/23/2018
     * check logged in user is client or not
     */
    function is_client()
    {
        //get all project tool permission
        $userid = get_staff_user_id();
        $staff = $this->staff_model->get($userid);
        $query = $this->db->query('SELECT `isclient` FROM `tblinvite` WHERE `deleted` = 0 AND `email` = "' . $staff->email . '" OR `staffid` = ' . $userid);
        $is_client = $query->row();
        if (isset($is_client)) {
            $is_client = $is_client->isclient;
        } else {
            $is_client = 0;
        }

        return $is_client;
    }

    /**
     * Added By : Masud
     * Dt : 10/29/2018
     * Get Project client
     */

    function get_project_clients($id)
    {
        $this->db->select('tblstaff.*,contactid');
        $this->db->where('projectid', $id);
        $this->db->where('tblprojectcontact.isclient', 1);
        $this->db->join('tblstaff', 'tblstaff.staffid = tblprojectcontact.contactid', 'left');
        return $this->db->get('tblprojectcontact')->result_array();
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/23/2018
     * check project tool permissions for team member
     */
    function get_project_tool_permission($projectid)
    {
        //get all project tool permission
        $userid = get_staff_user_id();
        $staff = $this->staff_model->get($userid);

        $query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS permisssion_name FROM `tblpermissions` WHERE `permissionid` IN ( SELECT `permissionid` FROM `tbleventpermission` WHERE `deleted` = 0  AND `projectid` = ' . $projectid . ' AND `inviteid` IN (SELECT `inviteid` FROM `tblinvite` WHERE `deleted` = 0 AND (`email` = "' . $staff->email . '" OR `staffid` = ' . $userid . ') GROUP BY `staffid`,`email`))');
        $permission = $query->row();

        $permission_name = $permission->permisssion_name;

        return $permission_name;
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/23/2018
     * get vendor for project and/or subproject who have accepted invite
     */
    function get_project_invites($projectid, $contacttype)
    {
        $vendors = [];
        $q1 = $this->db->query('SELECT GROUP_CONCAT(`id`) AS pid FROM `tblprojects` WHERE `id` = ' . $projectid . ' OR `parent` = ' . $projectid);
        $all_project = $q1->row();

        $pinviteid = '';

        //for vendor
        if ($contacttype == 3) {
            $pids = explode(",", $all_project->pid);

            foreach ($pids as $projectid) {
                $in_query = $this->db->query('SELECT GROUP_CONCAT(`tblinvitestatus`.`inviteid`) AS invite_id FROM `tblinvitestatus` JOIN `tblinvite` ON `tblinvitestatus`.`inviteid` = `tblinvite`.`inviteid` WHERE `tblinvitestatus`.`deleted` = 0 AND `tblinvite`.`deleted` = 0 AND `tblinvitestatus`.`projectid` = ' . $projectid . ' AND contacttype = ' . $contacttype);
                if ($in_query->num_rows() > 0) {
                    $pinvite = $in_query->row();
                    if (!empty($pinvite->invite_id)) {
                        $inviteds = array_unique(explode(",", $pinvite->invite_id));

                        foreach ($inviteds as $inviteid) {
                            $cnt_query = $this->db->query('SELECT `inviteid`, `status` FROM `tblinvitestatus` WHERE `projectid` = ' . $projectid . ' AND `inviteid` = ' . $inviteid . ' ORDER BY `tblinvitestatus`.`invitestatusid` DESC LIMIT 0,1');
                            if ($cnt_query->num_rows() > 0) {
                                $cnt = $cnt_query->row();

                                /*if ($cnt->status == $this->invite_vendor_status[3]) {
                                    $pinviteid .= $cnt->inviteid . ',';
                                }*/
                                if ($cnt->status != 'declined') {
                                    $pinviteid .= $cnt->inviteid . ',';
                                }
                            }
                        }
                    }
                }
            }
        }

        //for collaborator
        if ($contacttype == 4) {
            $pids = explode(",", $all_project->pid);

            foreach ($pids as $projectid) {
                $in_query = $this->db->query('SELECT GROUP_CONCAT(`tblinvitestatus`.`inviteid`) AS invite_id FROM `tblinvitestatus` JOIN `tblinvite` ON `tblinvitestatus`.`inviteid` = `tblinvite`.`inviteid` WHERE `tblinvitestatus`.`deleted` = 0 AND `tblinvite`.`deleted` = 0 AND `tblinvitestatus`.`projectid` = ' . $projectid . ' AND contacttype = ' . $contacttype);
                if ($in_query->num_rows() > 0) {
                    $pinvite = $in_query->row();
                    if (!empty($pinvite->invite_id)) {
                        $inviteds = array_unique(explode(",", $pinvite->invite_id));
                        foreach ($inviteds as $inviteid) {
                            $cnt_query = $this->db->query('SELECT `inviteid`, `status` FROM `tblinvitestatus` WHERE `projectid` = ' . $projectid . ' AND `usertype`="invitee" AND `inviteid` = ' . $inviteid);
                            if ($cnt_query->num_rows() > 0) {
                                $cnt = $cnt_query->row();
                                /*if ($cnt->status == $this->invite_collaborator_status[3]) {
                                    $pinviteid .= $cnt->inviteid . ',';
                                }*/
                                if ($cnt->status != 'declined') {
                                    $pinviteid .= $cnt->inviteid . ',';
                                }
                            }
                        }
                    }
                }
            }
        }

        //for venues
        if ($contacttype == 5) {
            $pids = explode(",", $all_project->pid);

            foreach ($pids as $projectid) {
                $in_query = $this->db->query('SELECT GROUP_CONCAT(`inviteid`) AS invite_id FROM `tblinvitestatus` WHERE `projectid` = ' . $projectid);
                if ($in_query->num_rows() > 0) {
                    $pinvite = $in_query->row();
                    if (!empty($pinvite->invite_id)) {
                        $inviteds = array_unique(explode(",", $pinvite->invite_id));

                        foreach ($inviteds as $inviteid) {
                            $cnt_query = $this->db->query('SELECT `inviteid`, `status` FROM `tblinvitestatus` WHERE `projectid` = ' . $projectid . ' AND `inviteid` = ' . $inviteid . ' ORDER BY `tblinvitestatus`.`invitestatusid` DESC LIMIT 0,1');
                            if ($cnt_query->num_rows() > 0) {
                                $cnt = $cnt_query->row();
                                /*if ($cnt->status == $this->invite_venue_status[3]) {
                                    $pinviteid .= $cnt->inviteid . ',';
                                }*/
                                if ($cnt->status != 'declined') {
                                    $pinviteid .= $cnt->inviteid . ',';
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($pinviteid != NULL && $pinviteid != '') {
            if ($contacttype != 5) {
                $query = $this->db->query('SELECT `inviteid`, `companyname`, `email`, `firstname`, CONCAT(`firstname`, " ", `lastname`) AS vendor_name, `email`, `staffid`, `contactid`, `tags` FROM `tblinvite` WHERE `deleted` = 0 AND `inviteid` IN (' . rtrim($pinviteid, ',') . ') AND `contacttype` = ' . $contacttype);
                $invite_vendor = $query->result_array();
                $vendorlists = [];
                if (count($invite_vendor) > 0) {
                    foreach ($invite_vendor as $invite) {

                        $this->db->select('status');
                        $this->db->where('inviteid', $invite['inviteid']);
                        $this->db->where('usertype="invitee" OR usertype="venue"');
                        $inviteestatus = $this->db->get('tblinvitestatus')->row();
                        $details['status'] = $inviteestatus->status;
                        $details['inviteid'] = $invite['inviteid'];
                        if (isset($invite['staffid']) && $invite['staffid'] > 0) {
                            $staff_query = $this->db->query('SELECT `staffid`, `email`, `firstname`, CONCAT(`firstname`, " ", `lastname`) AS vendor_name, `profile_image` FROM `tblstaff` WHERE `staffid` = ' . $invite['staffid']);
                            $staff_details = $staff_query->row();

                            $details['staffid'] = $invite['staffid'];
                            $details['addressbookid'] = 0;
                            $details['name'] = $staff_details->vendor_name;
                            $details['firstname'] = $staff_details->firstname;
                            $details['email'] = $staff_details->email;
                            $details['companyname'] = '';
                            $details['image'] = staff_profile_image($staff_details->staffid, array('staff-profile-image-small'));
                            $details['tags'] = '';

                            array_push($vendorlists, $details);
                        } else if (isset($invite['contactid']) && $invite['contactid'] > 0) {
                            $contact_query = $this->db->query('SELECT `tbladdressbook`.`addressbookid`, `email`, `companyname`, `firstname`, CONCAT(`firstname`, " ", `lastname`) AS vendor_name, `profile_image` FROM `tbladdressbook` LEFT JOIN `tbladdressbookemail` ON (`tbladdressbook`.`addressbookid` = `tbladdressbookemail`.`addressbookid` AND `tbladdressbookemail`.`type` = "primary") WHERE `tbladdressbook`.`addressbookid` = ' . $invite['contactid']);
                            $contact_details = $contact_query->row();
                            $details['staffid'] = 0;
                            $details['addressbookid'] = $invite['contactid'];
                            $details['name'] = $contact_details->vendor_name;
                            $details['firstname'] = $contact_details->firstname;
                            $details['email'] = $contact_details->email;
                            $details['companyname'] = $contact_details->companyname;
                            $details['image'] = addressbook_profile_image($contact_details->addressbookid, array("addressbook-profile-image-small"));

                            $tags_query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS vendor_tags FROM `tbltags` JOIN `tbladdressbooktags` ON `tbladdressbooktags`.`tagid` = `tbltags`.`id` WHERE `deleted` = 0 AND `addressbookid` = ' . $invite['contactid']);
                            $tags_details = $tags_query->row();
                            $details['tags'] = $tags_details->vendor_tags;
                            array_push($vendorlists, $details);
                        } else {
                            $staff_query = $this->db->query('SELECT `staffid`, `email`, `firstname`, CONCAT(`firstname`, " ", `lastname`) AS vendor_name, `profile_image` FROM `tblstaff` WHERE `email` = "' . $invite['email'] . '"');
                            $staff_details = $staff_query->row();

                            $details['staffid'] = $invite['staffid'];
                            $details['name'] = $staff_details->vendor_name;
                            $details['firstname'] = $staff_details->firstname;
                            $details['email'] = $staff_details->email;
                            $details['companyname'] = $invite['companyname'];
                            $details['image'] = staff_profile_image($staff_details->staffid, array('staff-profile-image-small'));
                            $tags_query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS vendor_tags FROM `tbltags` WHERE `deleted` = 0 AND `id` IN (' . $invite['tags'] . ')');
                            $tags_details = $tags_query->row();
                            $details['tags'] = $tags_details->vendor_tags;

                            array_push($vendorlists, $details);
                        }
                    }
                    $vendors = array_map("unserialize", array_unique(array_map("serialize", $vendorlists)));
                }
            } else {
                $query = $this->db->query('SELECT `inviteid`, `tblinvite`.`venueid`, `sitelocationid`, `tblvenue`.*  FROM `tblinvite` JOIN `tblvenue` ON `tblvenue`.`venueid` = `tblinvite`.`venueid` JOIN `tblbrandvenue` ON `tblvenue`.`venueid` = `tblbrandvenue`.`venueid` WHERE `tblvenue`.`deleted` = 0 AND `tblvenue`.`isapproved` = 1 AND `tblbrandvenue`.`deleted` = 0 AND `tblinvite`.`deleted` = 0 AND `tblinvite`.`inviteid` IN (' . rtrim($pinviteid, ',') . ') AND `tblinvite`.`contacttype` = ' . $contacttype);
                $invite_vendor = $query->result_array();
                $vendorlists = [];

                if (count($invite_vendor) > 0) {
                    foreach ($invite_vendor as $invite) {
                        $this->db->select('status');
                        $this->db->where('inviteid', $invite['inviteid']);
                        $this->db->where('usertype="invitee" OR usertype="venue"');
                        $inviteestatus = $this->db->get('tblinvitestatus')->row();
                        if (!empty($inviteestatus)) {
                            $details['status'] = $inviteestatus->status;
                            $details['inviteid'] = $invite['inviteid'];
                            $details['venueid'] = $invite['venueid'];
                            $details['venuename'] = $invite['venuename'];
                            $details['venuecontactname'] = $invite['venuecontactname'];
                            $details['venueemail'] = $invite['venueemail'];
                            $details['venuelogo'] = venue_logo_image($invite['venueid'], array('venue-logo-image-small'));

                            array_push($vendorlists, $details);
                        }
                    }

                    $vendors = array_map("unserialize", array_unique(array_map("serialize", $vendorlists)));
                }
            }
        }
        return $vendors;
    }

    function edit_invite_detail($projectid, $staffid, $contactid, $contacttype, $venueid = '')
    {
        //get all project permission assigned to staff
        if (isset($staffid) && $staffid > 0) {
            $this->db->select('GROUP_CONCAT(`tblinvite`.`inviteid`) AS invite');
            $this->db->where('tblinvite.staffid', $staffid);
            $this->db->where('tblinvite.contacttype', $contacttype);
            $this->db->where('tblinvite.projectid', $projectid);
            $this->db->where('tblinvite.deleted', 0);
            $invitedetails = $this->db->get('tblinvite')->row();
            $inviteids = explode(",", $invitedetails->invite);

            $tags = '';
            foreach ($inviteids as $inviteid) {
                $invite = $this->get_invitees($inviteid);
                if (!empty($invite->tags)) {
                    $tags .= $invite->tags . ",";
                }
            }

            if ($contacttype == 3 || $contacttype == 4) {
                //get tags
                if (isset($tags) && $tags != '') {
                    $query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS tag_name FROM `tbltags` WHERE `deleted` = 0 AND `id` IN (' . rtrim($tags, ",") . ')');
                    $tags = $query->row();
                    $invitedetails->assigned_tags = $tags->tag_name;
                }
            }

            //get permissions
            //for vendor
            if ($contacttype == 3) {
                $pinviteid = '';

                foreach ($inviteids as $inviteid) {
                    $cnt_query = $this->db->query('SELECT `inviteid`, `status` FROM `tblinvitestatus` WHERE `projectid` = ' . $projectid . ' AND `inviteid` = ' . $inviteid . ' ORDER BY `tblinvitestatus`.`invitestatusid` DESC LIMIT 0,1');
                    if ($cnt_query->num_rows() > 0) {
                        $cnt = $cnt_query->row();

                        /*if ($cnt->status == $this->invite_vendor_status[3]) {
                            $pinviteid .= $cnt->inviteid . ',';
                        }*/
                        if ($cnt->status != 'declined') {
                            $pinviteid .= $cnt->inviteid . ',';
                        }
                    }
                }

                $query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS permission_name FROM `tbleventpermission` JOIN `tblpermissions` ON `tblpermissions`.`permissionid` = `tbleventpermission`.`permissionid` WHERE `tbleventpermission`.`deleted` = 0 AND `tbleventpermission`.`inviteid` IN (' . rtrim($pinviteid, ",") . ') AND `tbleventpermission`.`projectid` = ' . $projectid);
                $permission = $query->row();

                $invitedetails->permission_name = $permission->permission_name;
            }

            //for collaborator
            if ($contacttype == 4) {
                $pinviteid = '';

                foreach ($inviteids as $inviteid) {
                    $cnt_query = $this->db->query('SELECT `inviteid`, `status` FROM `tblinvitestatus` WHERE `projectid` = ' . $projectid . ' ORDER BY `tblinvitestatus`.`invitestatusid` DESC LIMIT 0,1');
                    if ($cnt_query->num_rows() > 0) {
                        $cnt = $cnt_query->row();

                        /*if ($cnt->status == $this->invite_collaborator_status[3]) {
                            $pinviteid .= $cnt->inviteid . ',';
                        }*/
                        if ($cnt->status != 'declined') {
                            $pinviteid .= $cnt->inviteid . ',';
                        }
                    }
                }
                $query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS permission_name FROM `tbleventpermission` JOIN `tblpermissions` ON `tblpermissions`.`permissionid` = `tbleventpermission`.`permissionid` WHERE `tbleventpermission`.`deleted` = 0 AND `tbleventpermission`.`inviteid` IN (' . rtrim($pinviteid, ",") . ') AND `tbleventpermission`.`projectid` = ' . $projectid);
                $permission = $query->row();

                $invitedetails->permission_name = $permission->permission_name;
            }

            $invitedetails->pid = $projectid;

            if ($contacttype == 3 || $contacttype == 4) {
                $staff_query = $this->db->query('SELECT `staffid`, CONCAT(`firstname`, " ", `lastname`) AS vendor_name, `profile_image`, `email`, `phonenumber` FROM `tblstaff` WHERE `staffid` = ' . $staffid);
                $staff_details = $staff_query->row();
                $invitedetails->assigned_name = $staff_details->vendor_name;
                $invitedetails->assigned_email = $staff_details->email;
                $invitedetails->assigned_phone = $staff_details->phonenumber;
            }

            if ($contacttype == 5) {
                $staff_query = $this->db->query('SELECT * FROM `tblvenue` WHERE `venueid` = ' . $venueid);
                $staff_details = $staff_query->row();
                $invitedetails->assigned_name = $staff_details->venuename;
                $invitedetails->assigned_email = $staff_details->venueemail;
                $invitedetails->assigned_phone = $staff_details->venuephone;
            }

            return $invitedetails;
        }

        //get all project permission assigned to contact
        if (isset($contactid) && $contactid > 0) {
            $this->db->select('GROUP_CONCAT(`tblinvite`.`inviteid`) AS invite');
            $this->db->where('tblinvite.contactid', $contactid);
            $this->db->where('tblinvite.contacttype', $contacttype);
            $invitedetails = $this->db->get('tblinvite')->row();
            $inviteids = explode(",", $invitedetails->invite);

            $tags = '';
            foreach ($inviteids as $inviteid) {
                $invite = $this->get_invitees($inviteid);
                if (!empty($invite->tags)) {
                    $tags .= $invite->tags . ",";
                }
            }

            //get tags
            if (isset($tags) && $tags != '') {
                $query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS tag_name FROM `tbltags` WHERE `deleted` = 0 AND `id` IN (' . rtrim($tags, ",") . ')');
                $tags = $query->row();
                $invitedetails->assigned_tags = $tags->tag_name;
            }

            //get permissions
            //for vendor
            if ($contacttype == 3) {
                $query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS permission_name FROM `tbleventpermission` JOIN `tblpermissions` ON `tblpermissions`.`permissionid` = `tbleventpermission`.`permissionid` JOIN `tblinvitestatus` ON `tblinvitestatus`.`inviteid` = `tbleventpermission`.`inviteid` WHERE `tbleventpermission`.`deleted` = 0 AND `tbleventpermission`.`inviteid` IN (' . $invitedetails->invite . ') AND `tbleventpermission`.`projectid` = ' . $projectid . ' AND `tblinvitestatus`.`status` = "' . $this->invite_vendor_status[3] . '" ORDER BY `tblinvitestatus`.`invitestatusid` DESC LIMIT 0,1');
                $permission = $query->row();

                $invitedetails->permission_name = $permission->permission_name;
            }

            //for collaborator
            if ($contacttype == 4) {
                $query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS permission_name FROM `tbleventpermission` JOIN `tblpermissions` ON `tblpermissions`.`permissionid` = `tbleventpermission`.`permissionid` JOIN `tblinvitestatus` ON `tblinvitestatus`.`inviteid` = `tbleventpermission`.`inviteid` WHERE `tbleventpermission`.`deleted` = 0 AND `tbleventpermission`.`inviteid` IN (' . $invitedetails->invite . ') AND `tbleventpermission`.`projectid` = ' . $projectid . ' AND `tblinvitestatus`.`status` = "' . $this->invite_collaborator_status[3] . '" ORDER BY `tblinvitestatus`.`invitestatusid` DESC LIMIT 0,1');
                $permission = $query->row();

                $invitedetails->permission_name = $permission->permission_name;
            }

            $invitedetails->pid = $projectid;

            $contact_query = $this->db->query('SELECT `addressbookid`, `companyname`, CONCAT(`firstname`, " ", `lastname`) AS vendor_name, `profile_image`, (SELECT `email` FROM `tbladdressbookemail` WHERE `addressbookid` = ' . $contactid . ' AND type = "primary") AS email, (SELECT `phone` FROM `tbladdressbookphone` WHERE `addressbookid` = ' . $contactid . ' AND type = "primary") AS phonenumber FROM `tbladdressbook` WHERE `addressbookid` = ' . $contactid);
            $contact_details = $contact_query->row();
            $invitedetails->assigned_name = $contact_details->vendor_name;
            $invitedetails->assigned_email = $contact_details->email;
            $invitedetails->assigned_phone = $contact_details->phonenumber;

            return $invitedetails;
        }

        //get all project permission assigned to venue
        if (isset($venueid) && $venueid > 0) {
            $this->db->select('GROUP_CONCAT(`tblinvite`.`inviteid`) AS invite');
            $this->db->where('tblinvite.venueid', $venueid);
            $this->db->where('tblinvite.contacttype', $contacttype);
            $invitedetails = $this->db->get('tblinvite')->row();
            $inviteids = explode(",", $invitedetails->invite);

            $tags = '';
            foreach ($inviteids as $inviteid) {
                $invite = $this->get_invitees($inviteid);
                if (!empty($invite->tags)) {
                    $tags .= $invite->tags . ",";
                }
            }

            //get permissions
            //for venue
            $query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS permission_name FROM `tbleventpermission` JOIN `tblpermissions` ON `tblpermissions`.`permissionid` = `tbleventpermission`.`permissionid` JOIN `tblinvitestatus` ON `tblinvitestatus`.`inviteid` = `tbleventpermission`.`inviteid` WHERE `tbleventpermission`.`deleted` = 0 AND `tbleventpermission`.`inviteid` IN (' . $invitedetails->invite . ') AND `tbleventpermission`.`projectid` = ' . $projectid . ' AND `tblinvitestatus`.`status` = "' . $this->invite_venue_status[3] . '" ORDER BY `tblinvitestatus`.`invitestatusid` DESC LIMIT 0,1');
            $permission = $query->row();

            $invitedetails->permission_name = $permission->permission_name;

            $invitedetails->pid = $projectid;

            $venue_query = $this->db->query('SELECT * FROM `tblvenue` WHERE `venueid` = ' . $venueid);
            $venue_details = $venue_query->row();
            $invitedetails->assigned_name = $venue_details->venuename;
            $invitedetails->assigned_email = $venue_details->venueemail;
            $invitedetails->assigned_phone = $venue_details->venuephone;

            return $invitedetails;
        }
    }

    /**
     * Added By: Vaidehi
     * Dt: 03/05/2018
     * get cron events
     */
    public function get_cronevents()
    {
        $query = $this->db->query('SELECT `tblprojects`.*, DATE_FORMAT(`tblprojects`.`eventstartdatetime`, "%m/%d/%Y %H:%i") AS startdatetime, DATE_FORMAT(`tblprojects`.`eventenddatetime`, "%m/%d/%Y %H:%i") AS enddatetime, `tblstaff`.`firstname`, `tblstaff`.`lastname`, `tblstaff`.`email`, `tbladdressbook`.`firstname` AS contactfirstname, `tbladdressbook`.`lastname` AS contactlastname, `tbladdressbookemail`.`email` AS contactemail, s1.`firstname` AS assigned_firstname, s1.`lastname` AS assigned_lastname, s1.`email` AS assigned_email, `tblvenue`.`venuename`, CONCAT(`tblvenue`.`venueaddress`, IFNULL(CONCAT(", ",`tblvenue`.`venueaddress2`), ""), IFNULL(CONCAT(", ",`tblvenue`.`venuecity`), ""), IFNULL(CONCAT(", ",`tblvenue`.`venuestate`), ""), ", US", " - ", `tblvenue`.`venuezip`) AS location FROM `tblprojects` LEFT JOIN `tblprojectcontact` ON(`tblprojectcontact`.`eventid` = `tblprojects`.`id` AND `tblprojectcontact`.`isvendor` = 0 AND `tblprojectcontact`.`iscollaborator` = 0) LEFT JOIN `tblstaff` ON `tblprojects`.`assigned` = `tblstaff`.`staffid` LEFT JOIN `tblstaff` s1 ON (`tblprojects`.`createdby` = s1.`staffid` OR `tblprojects`.`updatedby` = s1.`staffid`) LEFT JOIN `tbladdressbook` ON `tbladdressbook`.`addressbookid` = `tblprojectcontact`.`contactid` LEFT JOIN `tbladdressbookemail` ON `tbladdressbookemail`.`addressbookid` = `tbladdressbook`.`addressbookid` AND `tbladdressbookemail`.`type` = "primary" LEFT JOIN `tbladdressbook_client` ON (`tbladdressbook_client`.`addressbookid` = `tbladdressbook`.`addressbookid` AND `tbladdressbook_client`.`deleted` = 0) LEFT JOIN `tblvenue` ON `tblprojects`.`venueid` = `tblvenue`.`venueid` LEFT JOIN `tblbrandvenue` ON (`tblbrandvenue`.`venueid` = `tblvenue`.`venueid` AND `tblbrandvenue`.`deleted` = 0) WHERE DATE(`tblprojects`.`eventstartdatetime`) = DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY) AND `tblprojects`.`deleted` = 0 AND `tblprojects`.`parent` > 0');

        $response = $query->result_array();

        return $response;
    }

    /**
     * Added By : Vaidehi
     * Dt : 03/21/2018
     * for Event Type
     */

    /**
     * Get Event Types
     * @param mixed $id Optional - Event Type ID
     * @return mixed object if id passed else array
     */
    public function get_event_type($id = false)
    {
        $brandid = get_user_session();
        $session_data = get_session_data();

        $is_admin = $session_data['is_admin'];

        $where = "";
        $where .= 'deleted = 0';
        if ($is_admin == false) {
            $where .= ' AND brandid =' . $brandid;
        }

        if (is_numeric($id)) {
            $where .= ' AND id=' . $id;
            $this->db->where($where);

            return $this->db->get('tbleventtype')->row();
        }

        $this->db->where($where);
        $this->db->order_by('order', 'asc');
        return $this->db->get('tbleventtype')->result_array();
    }

    /**
     * Added By : Vaidehi
     * Dt : 03/21/2018
     * for Event Type name
     */
    public function eventtype_name_exists()
    {
        if ($this->input->post()) {
            $id = $this->input->post('eventtypeid');

            $where = "";
            $where .= ' deleted = 0 AND brandid=' . get_user_session();

            if ($id != '') {
                $where .= ' AND eventtypeid=' . $id;
                $this->db->where($where);

                $_current_source = $this->db->get('tbleventtype')->row();
                if ($_current_source->name == $this->input->post('eventtypename')) {
                    echo json_encode(true);
                    die();
                }
            }
            $name = $this->input->post('eventtypename');
            $where .= ' AND eventtypename="' . $name . '"';
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
     * Add new event type
     * @param mixed $data eventtype data
     */
    public function add_eventtype($data)
    {
        $data['createdby'] = $this->session->userdata['staff_user_id'];
        $data['datecreated'] = date('Y-m-d H:i:s');
        $this->db->insert('tbleventtype', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Event Type Added [EventTypeID: ' . $insert_id . ', Name: ' . $data['eventtypename'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Update event type
     * @param mixed $data eventtype data
     * @param mixed $id eventtype id
     * @return boolean
     */
    public function update_eventtype($data, $id)
    {
        $data['updatedby'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $this->db->where('eventtypeid', $id);
        $this->db->update('tbleventtype', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Event Type Updated [EventTypeID: ' . $id . ', Name: ' . $data['eventtypename'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete eventtype from database
     * @param mixed $id eventtype id
     * @return mixed
     */
    public function delete_eventtype($id)
    {
        //$current = $this->get_eventtype($id);
        // Check if is already using in table
        if (is_reference_in_table('eventtypeid', 'tblleads', $id) || is_reference_in_table('eventtypeid', 'tblprojects', $id)) {
            return array(
                'referenced' => true
            );
        }
        $data['deleted'] = 1;
        $data['updatedby'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $this->db->where('eventtypeid', $id);
        $this->db->update('tbleventtype', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Event Type Deleted [EventTypeID: ' . $id . ']');

            return true;
        }
        return false;
    }

    public function getproject($id = '', $where = array(), $staffid = '', $contactid = '', $isvendor = '', $iscollaborator = '', $venueid = '')
    {
        if (is_numeric($id)) {
            $this->db->where('maintbl.id', $id);
            $project = $this->db->get('tblprojects as maintbl')->row();
            if ($project) {
                $project->attachments = $this->get_files($id);
            }

            return $project;
        }
    }

    /**
     * Added By : Vaidehi
     * Dt : 01/03/2017
     * save invite
     */
    public function sendinvites($data)
    {
        if ($data['contacttype'] == 3) {
            $role = "vendor";
            $widget_type = 'upcoming_project,pinned_item,calendar,quick_link,message,getting_started,contacts,messages';
            $quick_link_type = 'project,message,task_due,meeting';
        } elseif ($data['contacttype'] == 4) {
            $role = "collaborator";
            $widget_type = 'upcoming_project,pinned_item,calendar,quick_link,message,getting_started,task_list,contacts,messages';
            $quick_link_type = 'project,message,task_due,meeting,invite';
        } else {
            $role = "venue";
        }
        $userid = get_staff_user_id();
        $inviteType = isset($data['invite'][0]) ? $data['invite'][0] : "";
        if ($inviteType == "existing" && ($data['contacttype'] == 3 || $data['contacttype'] == 4)) {
            $contact_sel = $data['vendor'];
            //check if staff is invited or contact is invited
            if (strpos($data['vendor'], 'staff-') !== false) {
                $data['staffid'] = str_replace('staff-', '', $data['vendor']);
            } else {
                $data['contactid'] = str_replace('contact-', '', $data['vendor']);
            }
        }
        $events = $data['events'];
        $permissions = $data['permissionid'];
        $parent = $data['parent'];

        $data['tags'] = (isset($data['tags']) ? implode(",", $data['tags']) : '');
        $data['projectid'] = $data['project'];
        $project = $this->get($data['projectid']);
        if ($project->parent == 0) {
            $projectid = $data['projectid'];
        } else {
            $projectid = $project->parent;
        }
        $clients = $this->get_project_client($projectid);
        unset($data['project']);
        unset($data['invite']);
        unset($data['events']);
        unset($data['permissionid']);
        unset($data['parent']);
        unset($data['vendor']);
        unset($data['company']);
        $data['status'] = "pending";
        $data['invitedby'] = $this->session->userdata['staff_user_id'];
        $data['created_by'] = $this->session->userdata['staff_user_id'];
        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['brandid'] = get_user_session();
        $data['invitetype'] = $inviteType;
        //create entry in invite table
        $this->db->insert('tblinvite', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id > 0) {
            if (isset($data['staffid'])) {
                $contact = $data['staffid'];
            } elseif (isset($data['contactid'])) {
                $contact = $data['contactid'];
            } else {
                $contact = $data['email'];
            }

            logActivity('New invitation has been created by [Created By:' . $data['created_by'] . ' for Project ID:' . $data['projectid'] . ' to Contact ID: ' . $contact . ' IP:' . $this->input->ip_address() . ']');

            //insert all permissions assigned for each selected project
            foreach ($events as $key => $event) {
                $given_permissions = '';
                foreach ($permissions[$key] as $permission) {
                    $permission_data = [];
                    $given_permissions .= $permission . ",";

                    $permission_data['projectid'] = $event;
                    $permission_data['permissionid'] = $permission;
                    $permission_data['inviteid'] = $insert_id;

                    $this->db->insert('tbleventpermission', $permission_data);
                }
                logActivity('Invitation has been created by with following permissions [Project ID:' . $data['projectid'] . ' Permissions: ' . $given_permissions . ' IP:' . $this->input->ip_address() . ']');
            }

            if ($data['contacttype'] == 3 || $data['contacttype'] == 4) {
                //if account owner has send invite, send email to invited vendor
                $merge_fields = array();
                if (in_array($userid, $clients)) {
                    $merge_fields = array_merge($merge_fields, get_invite_merge_field($insert_id, $data['contacttype']));
                } else {
                    $merge_fields = array_merge($merge_fields, get_invite_merge_field($insert_id, $data['contacttype']));
                }
                //if existing staff or contact id, assign project directly
                if (isset($data['staffid']) && $data['staffid'] != '') {
                    $account_user_query = $this->db->query('SELECT tblstaff.* FROM tblstaff WHERE staffid = ' . $data['staffid']);
                    $results = $account_user_query->row();
                    $vendor_email = $results->email;
                    $firstname = $results->firstname;
                    foreach ($events as $key => $event) {
                        $allow_insert = true;
                        //for vendor
                        if ($data['contacttype'] == 3) {
                            $vcnt = $this->db->query('SELECT `projectcontactid` FROM `tblprojectcontact` WHERE (`projectid` = ' . $event . ' OR `eventid` = ' . $event . ') AND `contactid` = ' . $data['staffid'] . ' AND `isvendor` = 1');
                            if ($vcnt->num_rows() > 0) {
                                $allow_insert = false;
                            }
                        }

                        //for collaborator
                        if ($data['contacttype'] == 4) {
                            $vcnt = $this->db->query('SELECT `projectcontactid` FROM `tblprojectcontact` WHERE (`projectid` = ' . $event . ' OR `eventid` = ' . $event . ') AND `contactid` = ' . $data['staffid'] . ' AND `iscollaborator` = 1');
                            if ($vcnt->num_rows() > 0) {
                                $allow_insert = false;
                            }
                        }

                        //if not inserted, then insert
                        if ($allow_insert) {
                            $project_contact = [];

                            $pdet = $this->get($event);
                            if ($pdet->parent == 0) {
                                $project_contact['projectid'] = $event;
                            } else {
                                $project_contact['projectid'] = 0;
                                $project_contact['eventid'] = $event;
                            }

                            $project_contact['contactid'] = $data['staffid'];
                            $project_contact['brandid'] = get_user_session();

                            //for vendor
                            if ($data['contacttype'] == 3) {
                                $project_contact['isvendor'] = 1;
                                $project_contact['iscollaborator'] = 0;
                            }

                            //for collaborator
                            if ($data['contacttype'] == 4) {
                                $project_contact['isvendor'] = 0;
                                $project_contact['iscollaborator'] = 1;
                            }
                            $project_contact['active'] = 0;
                            $this->db->insert('tblprojectcontact', $project_contact);
                        }
                    }
                    $status_data = [];
                    $status_data['status'] = "pending";
                    $status_data['inviteid'] = $insert_id;
                    $status_data['projectid'] = $event;
                    $staff_brand['userid'] = $data['staffid'];
                    $status_data['usertype'] = "invitee";
                    $status_data['created_by'] = $this->session->userdata['staff_user_id'];
                    $status_data['datecreated'] = date('Y-m-d H:i:s');
                    $this->db->insert('tblinvitestatus', $status_data);

                    $this->invite_new_created_notification($insert_id, $data['staffid']);

                    if (in_array($userid, $clients)) {
                        //for vendor
                        if ($data['contacttype'] == 3) {
                            $send = $this->emails_model->send_email_template('invite-vendor', $vendor_email, $merge_fields);
                        }

                        //for collaborator
                        if ($data['contacttype'] == 4) {
                            $send = $this->emails_model->send_email_template('invite-collaborator', $vendor_email, $merge_fields);
                        }
                    } else {

                        if (count($clients) > 0) {
                            foreach ($clients as $client) {
                                $clientEmail = get_staff_email($client);
                                $staffdetails['{name}'] = get_staff_first_name($client);
                                $merge_fields = array_merge($merge_fields, $staffdetails);
                                if ($data['contacttype'] == 3) {
                                    $send = $this->emails_model->send_email_template('invite-new', $clientEmail, $merge_fields);
                                }
                                //for collaborator
                                if ($data['contacttype'] == 4) {
                                    $send = $this->emails_model->send_email_template('invite-new-collaborator', $clientEmail, $merge_fields);
                                }
                            }
                        }

                    }

                } elseif (isset($data['contactid']) && $data['contactid'] != '') {
                    $account_user_query = $this->db->query('SELECT `email`, `firstname` FROM `tbladdressbookemail` JOIN `tbladdressbook` ON `tbladdressbook`.`addressbookid` = `tbladdressbookemail`.`addressbookid` WHERE `type` = "primary" AND `tbladdressbook`.`addressbookid` = ' . $data['contactid']);
                    $results = $account_user_query->row();
                    $vendor_email = $results->email;
                    $firstname = $results->firstname;
                    $staffdetails['{name}'] = $firstname;
                    $merge_fields = array_merge($merge_fields, $staffdetails);

                    foreach ($events as $key => $event) {
                        $allow_insert = true;

                        //for vendor
                        if ($data['contacttype'] == 3) {
                            $vcnt = $this->db->query('SELECT `projectcontactid` FROM `tblprojectcontact` WHERE (`projectid` = ' . $event . ' OR `eventid` = ' . $event . ') AND `contactid` = ' . $data['contactid'] . ' AND  `isvendor` = 1');
                            if ($vcnt->num_rows() > 0) {
                                $allow_insert = false;
                            }
                        }

                        //for collaborator
                        if ($data['contacttype'] == 4) {
                            $vcnt = $this->db->query('SELECT `projectcontactid` FROM `tblprojectcontact` WHERE (`projectid` = ' . $event . ' OR `eventid` = ' . $event . ') AND `contactid` = ' . $data['contactid'] . ' AND `iscollaborator` = 1');
                            if ($vcnt->num_rows() > 0) {
                                $allow_insert = false;
                            }
                        }
                        //if not inserted, insert
                        if ($allow_insert) {
                            $project_contact = [];

                            $pdet = $this->get($event);
                            if ($pdet->parent == 0) {
                                $project_contact['projectid'] = $event;
                            } else {
                                $project_contact['projectid'] = 0;
                                $project_contact['eventid'] = $event;
                            }
                            $project_contact['contactid'] = $data['contactid'];
                            $project_contact['brandid'] = get_user_session();
                            //for vendor
                            if ($data['contacttype'] == 3) {
                                $project_contact['isvendor'] = 1;
                                $project_contact['iscollaborator'] = 0;
                            }
                            //for collaborator
                            if ($data['contacttype'] == 4) {
                                $project_contact['isvendor'] = 0;
                                $project_contact['iscollaborator'] = 1;
                            }
                            $project_contact['active'] = 0;
                            $this->db->insert('tblprojectcontact', $project_contact);

                        }
                    }
                    $status_data = [];
                    $status_data['status'] = "pending";
                    $status_data['inviteid'] = $insert_id;
                    $status_data['projectid'] = $event;
                    $staff_brand['userid'] = 0;
                    $status_data['usertype'] = "invitee";
                    $status_data['created_by'] = $this->session->userdata['staff_user_id'];
                    $status_data['datecreated'] = date('Y-m-d H:i:s');
                    $this->db->insert('tblinvitestatus', $status_data);
                    $this->invite_new_created_notification($insert_id, $data['contactid']);
                    if (in_array($userid, $clients)) {
                        //for vendor
                        if ($data['contacttype'] == 3) {
                            $send = $this->emails_model->send_email_template('invite-vendor', $vendor_email, $merge_fields);
                        }
                        //for collaborator
                        if ($data['contacttype'] == 4) {
                            $send = $this->emails_model->send_email_template('invite-collaborator', $vendor_email, $merge_fields);
                        }
                    } else {
                        if (count($clients) > 0) {
                            foreach ($clients as $client) {
                                $clientEmail = get_staff_email($client);
                                $staffdetails['{name}'] = get_staff_first_name($client);
                                $merge_fields = array_merge($merge_fields, $staffdetails);
                                if ($data['contacttype'] == 3) {
                                    $send = $this->emails_model->send_email_template('invite-new', $clientEmail, $merge_fields,'sent-to-vendor');
                                }
                                //for collaborator
                                if ($data['contacttype'] == 4) {
                                    $send = $this->emails_model->send_email_template('invite-new-collaborator', $clientEmail, $merge_fields,'sent-to-vendor');
                                }
                            }
                        }
                    }
                } else {
                    //if new vendor, create account
                    $where = array('email = ' => $data['email'], 'deleted = ' => 0);
                    $res = $this->db->where($where)->get('tblstaff')->row();
                    //check if account already exists or not
                    if (count($res) > 0) {
                        //if user is not active, send credentials in mail
                        if ($res->last_login == NULL || $res->last_login == null) {
                            $staffdetails['{name}'] = $data['firstname'];
                            $staffdetails['{vendor_password'] = $res->random_pass;
                            $merge_fields = array_merge($merge_fields, $staffdetails);
                            //for vendor
                            if ($data['contacttype'] == 3) {
                                $send = $this->emails_model->send_email_template('invite-new-vendor', $data['email'], $merge_fields);
                            }
                            //for collaborator
                            if ($data['contacttype'] == 4) {
                                $send = $this->emails_model->send_email_template('invite-new-collaborator', $data['email'], $merge_fields);
                            }
                        } else {
                            //if user is active, do not send credentials in mail
                            //for vendor
                            if ($data['contacttype'] == 3) {
                                $send = $this->emails_model->send_email_template('invite-vendor', $data['email'], $merge_fields);
                            }
                            //for collaborator
                            if ($data['contacttype'] == 4) {
                                $send = $this->emails_model->send_email_template('invite-collaborator', $data['email'], $merge_fields);
                            }
                        }
                    } else {
                        //if user does not found, create new user
                        $vendor_email = $data['email'];
                        $firstname = $data['firstname'];

                        $merge_fields = array_merge($merge_fields, get_invite_merge_field($insert_id, $data['contacttype'], 'sent-to-vendor'));

                        $query = $this->db->query('SELECT packageid FROM tblpackages WHERE name = "Free Package"');
                        $package = $query->row();

                        //generate random password
                        $password = $this->randomPassword();

                        $staffdata = [];
                        $staffdata['firstname'] = $data['firstname'];
                        $staffdata['lastname'] = $data['lastname'];
                        $staffdata['email'] = $vendor_email;
                        $staffdata['password'] = $password;
                        $staffdata['random_pass'] = $password;
                        $staffdata['created_by'] = $this->session->userdata['staff_user_id'];
                        $staffdata['datecreated'] = date('Y-m-d H:i:s');
                        $staffdata['active'] = 0;
                        $staffdata['facebook'] = null;
                        $staffdata['twitter'] = null;
                        $staffdata['google'] = null;
                        $staffdata['brandname'] = $firstname;
                        $staffdata['brandtype'] = 1;
                        $staffdata['is_not_staff'] = 1;
                        $staffdata['user_type'] = $data['contacttype'];
                        $staffdata['packagetype'] = (isset($package->packageid) ? $package->packageid : 2);
                        $this->load->model('register_model');

                        $this->register_model->saveclient($staffdata, 'invite', $role);

                        logActivity('New User Created [Email Address:' . $vendor_email . ' for invitation: ' . $insert_id . 'staffdata IP:' . $this->input->ip_address() . ']');

                        $where = array('email' => $vendor_email, 'deleted' => 0);
                        $staff_det = $this->db->where($where)->get('tblstaff')->row();

                        //assign project to new invited vendor
                        foreach ($events as $key => $event) {
                            $project_contact = [];

                            $pdet = $this->get($event);
                            if ($pdet->parent == 0) {
                                $project_contact['projectid'] = $event;
                            } else {
                                $project_contact['projectid'] = 0;
                                $project_contact['eventid'] = $event;
                            }

                            $project_contact['projectid'] = $projectid;
                            $project_contact['contactid'] = $staff_det->staffid;
                            $project_contact['brandid'] = get_user_session();

                            //for vendor
                            if ($data['contacttype'] == 3) {
                                $project_contact['isvendor'] = 1;
                                $project_contact['iscollaborator'] = 0;
                            }

                            //for collaborator
                            if ($data['contacttype'] == 4) {
                                $project_contact['isvendor'] = 0;
                                $project_contact['iscollaborator'] = 1;
                            }

                            $this->db->insert('tblprojectcontact', $project_contact);
                        }

                        //update staff id in invite table
                        $invite_data = [];
                        $invite_data['staffid'] = $staff_det->staffid;
                        $invite_data['updated_by'] = (isset($this->session->userdata['staff_user_id']) ? $this->session->userdata['staff_user_id'] : 0);
                        $invite_data['dateupdated'] = date('Y-m-d H:i:s');
                        $this->db->where('inviteid', $insert_id);
                        $this->db->update('tblinvite', $invite_data);

                        $staff_brand = [];
                        $staff_brand['active'] = 1;
                        $staff_brand['staffid'] = $staff_det->staffid;
                        $staff_brand['brandid'] = get_user_session();
                        $this->db->insert('tblstaffbrand', $staff_brand);

                        /**
                         * Added By : Masud
                         * Dt : 03/23/2018
                         * prefill dashboard values
                         */
                        $dashboard_data = array();
                        $dashboard_data['staffid'] = $staff_det->staffid;
                        $dashboard_data['widget_type'] = $widget_type;
                        $dashboard_data['quick_link_type'] = $quick_link_type;
                        $dashboard_data['order'] = '[{"widget_name":"getting_started","order":0},{"widget_name":"lead_pipeline","order":1},{"widget_name":"calendar","order":2},{"widget_name":"pinned_item","order":3},{"widget_name":"quick_link","order":4},{"widget_name":"upcoming_project","order":5},{"widget_name":"contacts","order":6},{"widget_name":"messages","order":7},{"widget_name":"task_list","order":8}]';
                        $dashboard_data['is_visible'] = 1;
                        $dashboard_data['brandid'] = get_user_session();
                        $dashboard_data['dateadded'] = date('Y-m-d H:i:s');
                        $dashboard_data['addedby'] = $staff_det->staffid;
                        $this->db->insert('  tbldashboard_settings', $dashboard_data);

                        /* Add invitee status */
                        $status_data = [];
                        $status_data['status'] = "pending";
                        $status_data['inviteid'] = $insert_id;
                        $status_data['projectid'] = $event;
                        $staff_brand['userid'] = $staff_det->staffid;
                        $status_data['usertype'] = "invitee";
                        $status_data['created_by'] = $this->session->userdata['staff_user_id'];
                        $status_data['datecreated'] = date('Y-m-d H:i:s');
                        $this->db->insert('tblinvitestatus', $status_data);

                        $staffdetails['{name}'] = $firstname;
                        $staffdetails['{vendor_password}'] = $password;
                        $merge_fields = array_merge($merge_fields, $staffdetails);
                        $this->invite_new_created_notification($insert_id, $staff_det->staffid);
                        if (in_array($userid, $clients)) {
                            //for vendor
                            if ($data['contacttype'] == 3) {
                                $send = $this->emails_model->send_email_template('invite-new-vendor', $vendor_email, $merge_fields);
                            }

                            //for collaborator
                            if ($data['contacttype'] == 4) {
                                $send = $this->emails_model->send_email_template('invite-new-project-collaborator', $vendor_email, $merge_fields);
                            }
                        } else {
                            if (count($clients) > 0) {
                                foreach ($clients as $client) {
                                    $clientEmail = get_staff_email($client);
                                    $staffdetails['{name}'] = get_staff_first_name($client);
                                    $merge_fields = array_merge($merge_fields, $staffdetails);
                                    if ($data['contacttype'] == 3) {
                                        $send = $this->emails_model->send_email_template('invite-new', $clientEmail, $merge_fields,'sent-to-vendor');
                                    }
                                    //for collaborator
                                    if ($data['contacttype'] == 4) {
                                        $send = $this->emails_model->send_email_template('invite-new-collaborator', $clientEmail, $merge_fields,'sent-to-vendor');
                                    }
                                }
                            }
                        }
                    }
                }
            } elseif ($data['contacttype'] == 5) {
                foreach ($events as $key => $event) {
                    $project_venue = [];

                    $pdet = $this->get($event);
                    if ($pdet->parent == 0) {
                        $project_venue['projectid'] = $event;
                    } else {
                        $project_venue['projectid'] = 0;
                        $project_venue['eventid'] = $event;
                    }
                    $project_venue['venueid'] = $data['venueid'];
                    $project_venue['brandid'] = get_user_session();

                    $this->db->insert('tblprojectvenue', $project_venue);
                }
                $status_data = [];
                $status_data['status'] = "pending";
                $status_data['inviteid'] = $insert_id;
                $status_data['projectid'] = $event;
                $staff_brand['userid'] = $data['venueid'];
                $status_data['usertype'] = "venue";
                $status_data['created_by'] = $this->session->userdata['staff_user_id'];
                $status_data['datecreated'] = date('Y-m-d H:i:s');
                $this->db->insert('tblinvitestatus', $status_data);
            }
            /* Add invitee status for clients and inviter */
            if (!in_array($userid, $clients)) {
                $status_data = [];
                if ($userid == get_staff_user_id()) {
                    $status_data['status'] = "approved";
                } else {
                    $status_data['status'] = "pending";
                }
                $status_data['inviteid'] = $insert_id;
                $status_data['projectid'] = $event;
                $status_data['userid'] = $userid;
                $status_data['usertype'] = "member";
                $status_data['created_by'] = $this->session->userdata['staff_user_id'];
                $status_data['datecreated'] = date('Y-m-d H:i:s');
                $this->db->insert('tblinvitestatus', $status_data);
            }
            if (count($clients) > 0) {
                foreach ($clients as $client) {
                    $status_data = [];
                    if ($client == get_staff_user_id()) {
                        $status_data['status'] = "approved";
                    } else {
                        $status_data['status'] = "pending";
                    }
                    $status_data['inviteid'] = $insert_id;
                    $status_data['projectid'] = $event;
                    $status_data['userid'] = $client;
                    $status_data['usertype'] = "client";
                    $status_data['created_by'] = $this->session->userdata['staff_user_id'];
                    $status_data['datecreated'] = date('Y-m-d H:i:s');
                    $this->db->insert('tblinvitestatus', $status_data);
                }
            }
            $status_data = [];
            $status_data['userid'] = get_invite_staffid($insert_id);
            $this->db->where('inviteid', $insert_id);
            $this->db->where('usertype', 'invitee');
            $this->db->update('tblinvitestatus', $status_data);
        }

        return $insert_id;
    }

    function get_project_client($projectid)
    {
        $this->db->select('tblstaff.staffid');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblprojectcontact.contactid');
        $this->db->where('tblprojectcontact.isclient', 1);
        $this->db->where('tblprojectcontact.projectid', $projectid);
        $clients = $this->db->get('tblprojectcontact')->result_array();
        $clients = array_map('current', $clients);
        return $clients;
    }

    function get_project_client_contact($projectid)
    {
        $this->db->select('tblstaff.email');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblprojectcontact.contactid');
        $this->db->where('tblprojectcontact.isclient', 1);
        $this->db->where('tblprojectcontact.projectid', $projectid);
        $clients = $this->db->get('tblprojectcontact')->result_array();
        $clients = array_map('current', $clients);
        $this->db->select('addressbookid');
        $this->db->where_in('email', $clients);
        $this->db->where('type', 'primary');
        $clients = $this->db->get('tbladdressbookemail')->result_array();
        $clients = array_map('current', $clients);
        return $clients;
    }

    function get_project_details($projectid)
    {
        $this->db->select('brandid');
        $this->db->where('id', $projectid);
        $this->db->where('deleted', 0);
        $project = $this->db->get('tblprojects')->row();
        return $project->brandid;
    }

    function get_invite_details($projectid)
    {
        $this->db->select('brandid');
        $this->db->where('inviteid', $projectid);
        $this->db->where('deleted', 0);
        $project = $this->db->get('tblinvite')->row();
        return $project->brandid;
    }

    function get_staff_brand($staffid)
    {
        $this->db->select('brandid');
        $this->db->where('staffid', $staffid);
        $this->db->where('active', 1);
        $brands = $this->db->get('tblstaffbrand')->result_array();
        $brands = array_map('current', $brands);
        return $brands;
    }

    function project_status_change($data)
    {
        $this->db->where('id', $data['projectid']);
        $this->db->update('tblprojects', array('status' => $data['status']));
        if ($this->db->affected_rows() > 0) {
            return 1;
        }
        return 0;
    }

    function reorderprojecttype($data)
    {
        $this->db->where('eventtypeid', $data['eventtypeid']);
        $this->db->update('tbleventtype', array('order' => $data['order']));
        if ($this->db->affected_rows() > 0) {
            return 1;
        }
        return 0;
    }

    function get_invitedusers($projectid)
    {
        $this->db->select('staffid,contactid,venueid');
        $this->db->where('projectid', $projectid);
        $invitedusers = $this->db->get('tblinvite')->result_array();
        //$invitedusers = array_map('current',$invitedusers);
        $invitedsatff = array();
        $invitedcontact = array();
        $invitedvenue = array();
        foreach ($invitedusers as $key => $inviteduser) {
            if ($inviteduser['staffid'] > 0) {
                array_push($invitedsatff, $inviteduser['staffid']);
            }
            if ($inviteduser['contactid'] > 0) {
                array_push($invitedcontact, $inviteduser['contactid']);
            }
            if ($inviteduser['venueid'] > 0) {
                array_push($invitedvenue, $inviteduser['venueid']);
            }
        }
        $invitedusers = array();
        $invitedusers['staff'] = $invitedsatff;
        $invitedusers['contact'] = $invitedcontact;
        $invitedusers['venue'] = $invitedvenue;
        return $invitedusers;
    }

    /**
     * Added By : Masud
     * Dt : 27/05/2018
     * to save extra form fields in db
     */
    public function invite_new_created_notification($invite_id, $assigned, $integration = false)
    {
        $name = $this->db->select('CONCAT(firstname," ",lastname) as name')->from('tblinvite')->where('inviteid', $invite_id)->get()->row()->name;
        if ($assigned == "") {
            $assigned = 0;
        }

        $notification_data = array(
            'description' => ($integration == false) ? 'not_new_invite_created' : 'not_new_invite_created',
            'touserid' => $assigned,
            'eid' => $invite_id,
            'brandid' => get_user_session(),
            'not_type' => 'invites',
            'link' => 'invites/invitedetails/' . $invite_id,
            'additional_data' => ($integration == false ? serialize(array(
                $name
            )) : serialize(array()))
        );

        if ($integration != false) {
            $notification_data['fromcompany'] = 1;
        }

        if (add_notification($notification_data)) {
            pusher_trigger_notification(array($assigned));
        }
    }
}

















