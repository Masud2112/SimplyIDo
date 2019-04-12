<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Staff_model extends CRM_Model
{
    private $perm_statements = array('view', 'view_own', 'edit', 'create', 'delete');

    public function __construct()
    {
        parent::__construct();
    }

    public function delete($id, $transfer_data_to)
    {
        if (!is_numeric($transfer_data_to)) {
            return false;
        }

        if ($id == $transfer_data_to) {
            return false;
        }

        do_action('before_delete_staff_member', array(
            'id' => $id,
            'transfer_data_to' => $transfer_data_to
        ));

        $name = get_staff_full_name($id);
        $transferred_to = get_staff_full_name($transfer_data_to);

        $this->db->where('addedfrom', $id);
        $this->db->update('tblestimates', array(
            'addedfrom' => $transfer_data_to
        ));

        $this->db->where('sale_agent', $id);
        $this->db->update('tblestimates', array(
            'sale_agent' => $transfer_data_to
        ));

        $this->db->where('addedfrom', $id);
        $this->db->update('tblinvoices', array(
            'addedfrom' => $transfer_data_to
        ));

        $this->db->where('sale_agent', $id);
        $this->db->update('tblinvoices', array(
            'sale_agent' => $transfer_data_to
        ));

        $this->db->where('addedfrom', $id);
        $this->db->update('tblexpenses', array(
            'addedfrom' => $transfer_data_to
        ));

        $this->db->where('addedfrom', $id);
        $this->db->update('tblnotes', array(
            'addedfrom' => $transfer_data_to
        ));

        $this->db->where('userid', $id);
        $this->db->update('tblpostcomments', array(
            'userid' => $transfer_data_to
        ));

        $this->db->where('creator', $id);
        $this->db->update('tblposts', array(
            'creator' => $transfer_data_to
        ));

        $this->db->where('staff_id', $id);
        $this->db->update('tblprojectdiscussions', array(
            'staff_id' => $transfer_data_to
        ));

        $this->db->where('addedfrom', $id);
        $this->db->update('tblprojects', array(
            'addedfrom' => $transfer_data_to
        ));

        $this->db->where('staffid', $id);
        $this->db->update('tblprojectfiles', array(
            'staffid' => $transfer_data_to
        ));

        $this->db->where('staffid', $id);
        $this->db->update('tblproposalcomments', array(
            'staffid' => $transfer_data_to
        ));

        $this->db->where('addedfrom', $id);
        $this->db->update('tblproposals', array(
            'addedfrom' => $transfer_data_to
        ));

        $this->db->where('staffid', $id);
        $this->db->update('tblstafftaskcomments', array(
            'staffid' => $transfer_data_to
        ));

        $this->db->where('addedfrom', $id);
        $this->db->where('is_added_from_contact', 0);
        $this->db->update('tblstafftasks', array(
            'addedfrom' => $transfer_data_to
        ));

        $this->db->where('staffid', $id);
        $this->db->update('tblfiles', array(
            'staffid' => $transfer_data_to
        ));

        $this->db->where('renewed_by_staff_id', $id);
        $this->db->update('tblcontractrenewals', array(
            'renewed_by_staff_id' => $transfer_data_to
        ));

        $this->db->where('addedfrom', $id);
        $this->db->update('tbltaskchecklists', array(
            'addedfrom' => $transfer_data_to
        ));

        $this->db->where('finished_from', $id);
        $this->db->update('tbltaskchecklists', array(
            'finished_from' => $transfer_data_to
        ));

        $this->db->where('admin', $id);
        $this->db->update('tblticketreplies', array(
            'admin' => $transfer_data_to
        ));

        $this->db->where('admin', $id);
        $this->db->update('tbltickets', array(
            'admin' => $transfer_data_to
        ));

        $this->db->where('addedfrom', $id);
        $this->db->update('tblleads', array(
            'addedfrom' => $transfer_data_to
        ));

        $this->db->where('assigned', $id);
        $this->db->update('tblleads', array(
            'assigned' => $transfer_data_to
        ));

        $this->db->where('staff_id', $id);
        $this->db->update('tbltaskstimers', array(
            'staff_id' => $transfer_data_to
        ));

        $this->db->where('addedfrom', $id);
        $this->db->update('tblcontracts', array(
            'addedfrom' => $transfer_data_to
        ));

        $this->db->where('assigned_from', $id);
        $this->db->where('is_assigned_from_contact', 0);
        $this->db->update('tblstafftaskassignees', array(
            'assigned_from' => $transfer_data_to
        ));

        $this->db->where('responsible', $id);
        $this->db->update('tblleadsintegration', array(
            'responsible' => $transfer_data_to
        ));

        $this->db->where('responsible', $id);
        $this->db->update('tblwebtolead', array(
            'responsible' => $transfer_data_to
        ));

        $this->db->where('notify_type', 'specific_staff');
        $webtolead = $this->db->get('tblwebtolead')->result_array();

        foreach ($webtolead as $form) {
            if (!empty($form['notify_ids'])) {
                $staff = unserialize($form['notify_ids']);
                if (is_array($staff)) {
                    if (in_array($id, $staff)) {
                        if (($key = array_search($id, $staff)) !== false) {
                            unset($staff[$key]);
                            $staff = serialize(array_values($staff));
                            $this->db->where('id', $form['id']);
                            $this->db->update('tblwebtolead', array(
                                'notify_ids' => $staff
                            ));
                        }
                    }
                }
            }
        }

        $this->db->where('id', 1);
        $leads_email_integration = $this->db->get('tblleadsintegration')->row();

        if ($leads_email_integration->notify_type == 'specific_staff') {
            if (!empty($leads_email_integration->notify_ids)) {
                $staff = unserialize($leads_email_integration->notify_ids);
                if (is_array($staff)) {
                    if (in_array($id, $staff)) {
                        if (($key = array_search($id, $staff)) !== false) {
                            unset($staff[$key]);
                            $staff = serialize(array_values($staff));
                            $this->db->where('id', 1);
                            $this->db->update('tblleadsintegration', array(
                                'notify_ids' => $staff
                            ));
                        }
                    }
                }
            }
        }

        $this->db->where('assigned', $id);
        $this->db->update('tbltickets', array(
            'assigned' => 0
        ));

        // $this->db->where('staff', 1);
        // $this->db->where('userid', $id);
        // $this->db->delete('tbldismissedannouncements');

        // $this->db->where('userid', $id);
        // $this->db->delete('tblcommentlikes');

        // $this->db->where('userid', $id);
        // $this->db->delete('tblpostlikes');

        // $this->db->where('staff_id', $id);
        // $this->db->delete('tblcustomeradmins');

        // $this->db->where('fieldto', 'staff');
        // $this->db->where('relid', $id);
        // $this->db->delete('tblcustomfieldsvalues');

        // $this->db->where('userid', $id);
        // $this->db->delete('tblevents');

        // $this->db->where('touserid', $id);
        // $this->db->delete('tblnotifications');

        // $this->db->where('staff_id', $id);
        // $this->db->delete('tblprojectmembers');

        // $this->db->where('staff_id', $id);
        // $this->db->delete('tblprojectnotes');

        // $this->db->where('creator', $id);
        // $this->db->or_where('staff', $id);
        // $this->db->delete('tblreminders');

        // $this->db->where('staffid', $id);
        // $this->db->delete('tblstaffdepartments');

        // $this->db->where('staffid', $id);
        // $this->db->delete('tbltodoitems');

        // $this->db->where('staff', 1);
        // $this->db->where('user_id', $id);
        // $this->db->delete('tbluserautologin');

        // $this->db->where('staffid', $id);
        // $this->db->delete('tblstaffpermissions');

        // $this->db->where('staffid', $id);
        // $this->db->delete('tblstafftaskassignees');

        // $this->db->where('staffid', $id);
        // $this->db->delete('tblstafftasksfollowers');

        // $this->db->where('staff_id', $id);
        // $this->db->delete('tblpinnedprojects');

        // $this->db->where('staffid', $id);
        // $this->db->delete('tblstaff');
        $data['deleted'] = 1;
        $data['updated_by'] = $this->session->userdata['staff_user_id'];
        $data['updated_date'] = date('Y-m-d H:i:s');
        $this->db->where('staffid', $id);
        $this->db->update('tblstaff', $data);

        logActivity('Staff Member Deleted [Name: ' . $name . ', Data Transferred To: ' . $transferred_to . ']');
        do_action('staff_member_deleted', array(
            'id' => $id,
            'transfer_data_to' => $transfer_data_to
        ));

        return true;
    }

    /**
     * Get staff member/s
     * @param  mixed $id Optional - staff id
     * @param  integer $active Optional get all active or inactive
     * @return mixed if id is passed return object else array
     */
    public function get($id = '', $active = '', $where = array())
    {

        $this->db->where($where);
        if (is_int($active)) {
            $this->db->where('tblstaff.active', $active);
        }

        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];


        if ($is_sido_admin == 0 && $is_admin == 0) {
            $this->db->join('tblstaffbrand', 'tblstaffbrand.staffid = tblstaff.staffid');
            $this->db->where('tblstaffbrand.brandid', get_user_session());
            $this->db->where('tblstaff.deleted', 0);

            $this->db->join('tblroleuserteam', 'tblroleuserteam.user_id = tblstaff.staffid');
            $this->db->join('tblroles', 'tblroles.roleid = tblroleuserteam.role_id');
        }

        if (is_numeric($id)) {
            $this->db->select('tblstaff.*');

            if ($is_sido_admin == 0 && $is_admin == 0) {
                $this->db->select('tblstaff.*,tblroles.name as designation');
            } else {
                $this->db->select('tblstaff.*');
            }

            $this->db->where('tblstaff.staffid', $id);
            $this->db->where('tblstaff.deleted', 0);
            $staff = $this->db->get('tblstaff')->row();

            //$this->db->select('tblstaffpermissions.*,tblpermissions.shortname as permission_name');
            //$this->db->join('tblpermissions','tblpermissions.permissionid = tblstaffpermissions.permissionid');
            //$this->db->where('staffid',$id);
            //$staff->permissions = $this->db->get('tblstaffpermissions')->result();

            if ($is_admin == 0) {
                $staff->permission = '';
                $this->db->where('user_id', $id);
                $staff->permission = $this->db->get('tblroleuserteam')->result();
            }

            return $staff;
        }
        if ($is_sido_admin == 0 && $is_admin == 0) {
            $this->db->select('tblstaff.*,tblroles.name as designation');
        } else {
            $this->db->select('tblstaff.*');
        }
        $this->db->order_by('firstname', 'desc');
        $this->db->group_by("tblstaff.staffid");
        $members = $this->db->get('tblstaff')->result_array();
        /*echo $this->db->last_query();die;
        $members=array_map("unserialize",
            array_unique(array_map("serialize", $members)));
        echo "<pre>";
        print_r($members);
        die();*/
        return $members;
    }

    /**
     * Added By : Vaidehi
     * Dt: 10/12/2017
     * get all brands
     */
    /**
     * Get all brands of staff member/s
     * @param  mixed $id Optional - staff id
     * @param  integer $active Optional get all active or inactive
     * @return mixed if id is passed return object else array
     */
    public function get_all_brands()
    {
        $session_data = get_session_data();

        $this->db->join('tblstaffbrand', 'tblstaffbrand.brandid = tblbrand.brandid');
        $this->db->join('tblstaff', 'tblstaffbrand.staffid = tblstaff.staffid');
        $this->db->where('tblstaffbrand.staffid', $session_data['staff_user_id']);
        $this->db->where('tblstaffbrand.active', '1');
        $this->db->where('tblbrand.deleted', 0);
        $this->db->order_by('tblbrand.brandid', 'asc');

        return $this->db->get('tblbrand')->result_array();
    }

    function array_filter_recursive($array)
    {
        foreach ($array as $key => &$value) {
            if (empty($value)) {
                unset($array[$key]);
            } else {
                if (is_array($value)) {
                    $value = array_filter_recursive($value);
                    if (empty($value)) {
                        unset($array[$key]);
                    }
                }
            }
        }

        return $array;
    }

    /**
     * Add new staff member
     * @param array $data staff $_POST data
     */
    public function add($data)
    {
        unset($data['imagebase64']);
        $data['clientid'] = get_user_session();
        $data['created_by'] = $this->session->userdata['staff_user_id'];
        $data['datecreated'] = date('Y-m-d H:i:s');
        if (isset($data['fakeusernameremembered'])) {
            unset($data['fakeusernameremembered']);
        }
        if (isset($data['fakepasswordremembered'])) {
            unset($data['fakepasswordremembered']);
        }

        $to_do_list = @$data['widget_type'];
        unset($data['widget_type']);

        $quick_link_types = @$data['quick_link_type'];
        unset($data['quick_link_type']);


        // First check for all cases if the email exists.
        $this->db->where('email', $data['email']);
        $email = $this->db->get('tblstaff')->row();
        if ($email) {
            die('Email already exists');
        }

        $data['admin'] = 0;
        if (is_admin()) {
            if (isset($data['administrator'])) {
                $data['admin'] = 1;
                unset($data['administrator']);
            }
        }

        $send_welcome_email = true;
        $original_password = $data['password'];
        if (!isset($data['send_welcome_email'])) {
            $send_welcome_email = false;
        } else {
            unset($data['send_welcome_email']);
        }
        $data['email_signature'] = nl2br_save_html($data['email_signature']);
        $this->load->helper('phpass');
        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        $data['password'] = $hasher->HashPassword($data['password']);
        $data['datecreated'] = date('Y-m-d H:i:s');
        if (isset($data['departments'])) {
            $departments = $data['departments'];
            unset($data['departments']);
        }

        if (isset($data['permission'])) {
            $per = array_filter(array_map('array_filter', $data['permission']));
            unset($data['permission']);
        }

        // $permissions = array();
        // if (isset($data['view'])) {
        //     $permissions['view'] = $data['view'];
        //     unset($data['view']);
        // }
        // if (isset($data['view_own'])) {
        //     $permissions['view_own'] = $data['view_own'];
        //     unset($data['view_own']);
        // }
        // if (isset($data['edit'])) {
        //     $permissions['edit'] = $data['edit'];
        //     unset($data['edit']);
        // }
        // if (isset($data['create'])) {
        //     $permissions['create'] = $data['create'];
        //     unset($data['create']);
        // }
        // if (isset($data['delete'])) {
        //     $permissions['delete'] = $data['delete'];
        //     unset($data['delete']);
        // }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if ($data['admin'] == 1) {
            $data['is_not_staff'] = 0;
        }

        $this->db->select('color');
        $this->db->from('tblcolors');
        $this->db->order_by('rand()');
        $this->db->limit(1);
        $staffcolor = $this->db->get()->row();
        $data['profilecolor'] = $staffcolor->color;

        $this->db->insert('tblstaff', $data);
        $staffid = $this->db->insert_id();

        /*
        ** Added By Sanjay on 02/13/2018 
        ** adding dashboard settings data
        */
        $dashboard_data['staffid'] = $staffid;
        $dashboard_data['widget_type'] = !empty($to_do_list)?implode(",", $to_do_list):"";
        $dashboard_data['quick_link_type'] = !empty($quick_link_types)?implode(",", $quick_link_types):"";
        $dashboard_data['order'] = '[{"widget_name":"getting_started","order":0},{"widget_name":"lead_pipeline","order":1},{"widget_name":"calendar","order":2},{"widget_name":"pinned_item","order":3},{"widget_name":"quick_link","order":4},{"widget_name":"upcoming_project","order":5},{"widget_name":"contacts","order":6},{"widget_name":"messages","order":7},{"widget_name":"task_list","order":8}]';
        $dashboard_data['is_visible'] = 1;
        $dashboard_data['brandid'] = get_user_session();
        $dashboard_data['dateadded'] = date('Y-m-d H:i:s');
        $dashboard_data['addedby'] = $this->session->userdata['staff_user_id'];
        $this->db->insert('tbldashboard_settings', $dashboard_data);

        /*
        ** Added By vaidehi on 03/21/2018 
        ** for report configuration
        */
        if (is_sido_admin()) {
            $this->db->where('staff_user_id', $id);
            $this->db->where('brandid', get_user_session());
            $_exists = $this->db->get('tblreportconfiguration')->row();
            if (!$_exists) {
                $report_data['report_name'] = 'Sign Up';
                $report_data['is_visible'] = 1;
                $report_data['report_order'] = 0;
                $report_data['default_records'] = 5;
                $report_data['saved_filter'] = 'all';
                $report_data['staff_user_id'] = $id;
                $report_data['brandid'] = get_user_session();
                $report_data['createdby'] = $id;
                $report_data['datecreated'] = date('Y-m-d H:i:s');

                $this->db->insert('tblreportconfiguration', $report_data);

                $report_data['report_name'] = 'Subscribers';
                $report_data['is_visible'] = 1;
                $report_data['report_order'] = 1;
                $report_data['default_records'] = 5;
                $report_data['saved_filter'] = 'all';
                $report_data['staff_user_id'] = $id;
                $report_data['brandid'] = get_user_session();
                $report_data['createdby'] = $id;
                $report_data['datecreated'] = date('Y-m-d H:i:s');

                $this->db->insert('tblreportconfiguration', $report_data);

                $report_data['report_name'] = 'Conversion Rate';
                $report_data['is_visible'] = 1;
                $report_data['report_order'] = 2;
                $report_data['default_records'] = 5;
                $report_data['saved_filter'] = 'all';
                $report_data['staff_user_id'] = $id;
                $report_data['brandid'] = get_user_session();
                $report_data['createdby'] = $id;
                $report_data['datecreated'] = date('Y-m-d H:i:s');

                $this->db->insert('tblreportconfiguration', $report_data);

                $report_data['report_name'] = 'Net Revenue';
                $report_data['is_visible'] = 1;
                $report_data['report_order'] = 3;
                $report_data['default_records'] = 12;
                $report_data['saved_filter'] = 'all';
                $report_data['staff_user_id'] = $id;
                $report_data['brandid'] = get_user_session();
                $report_data['createdby'] = $id;
                $report_data['datecreated'] = date('Y-m-d H:i:s');

                $this->db->insert('tblreportconfiguration', $report_data);

                $report_data['report_name'] = 'Churn';
                $report_data['is_visible'] = 1;
                $report_data['report_order'] = 4;
                $report_data['default_records'] = 5;
                $report_data['saved_filter'] = 'all';
                $report_data['staff_user_id'] = $id;
                $report_data['brandid'] = get_user_session();
                $report_data['createdby'] = $id;
                $report_data['datecreated'] = date('Y-m-d H:i:s');

                $this->db->insert('tblreportconfiguration', $report_data);
            }
        } else {
            $this->db->where('staff_user_id', $staffid);
            $this->db->where('brandid', get_user_session());
            $_exists = $this->db->get('tblreportconfiguration')->row();
            if (!$_exists) {
                $report_data['report_name'] = 'Booking Success';
                $report_data['is_visible'] = 1;
                $report_data['report_order'] = 0;
                $report_data['default_records'] = 5;
                $report_data['saved_filter'] = 'all';
                $report_data['staff_user_id'] = $staffid;
                $report_data['brandid'] = get_user_session();
                $report_data['createdby'] = $staffid;
                $report_data['datecreated'] = date('Y-m-d H:i:s');

                $this->db->insert('tblreportconfiguration', $report_data);

                $report_data['report_name'] = 'Lead Source';
                $report_data['is_visible'] = 1;
                $report_data['report_order'] = 1;
                $report_data['default_records'] = 5;
                $report_data['saved_filter'] = 'all';
                $report_data['staff_user_id'] = $staffid;
                $report_data['brandid'] = get_user_session();
                $report_data['createdby'] = $staffid;
                $report_data['datecreated'] = date('Y-m-d H:i:s');

                $this->db->insert('tblreportconfiguration', $report_data);

                $report_data['report_name'] = 'Revenue';
                $report_data['is_visible'] = 1;
                $report_data['report_order'] = 2;
                $report_data['default_records'] = 5;
                $report_data['saved_filter'] = 'all';
                $report_data['staff_user_id'] = $staffid;
                $report_data['brandid'] = get_user_session();
                $report_data['createdby'] = $staffid;
                $report_data['datecreated'] = date('Y-m-d H:i:s');

                $this->db->insert('tblreportconfiguration', $report_data);
            }
        }

        if (isset($data['user_type']) && $data['user_type'] == 1) {
            $this->db->insert('tblroleuserteam', array(
                'role_id' => 1,
                'team_id' => 0,
                'user_id' => $staffid
            ));
        }
        if (isset($data['is_sido_admin']) && $data['is_sido_admin'] == 1) {
            $this->db->insert('tblroleuserteam', array(
                'role_id' => 2,
                'team_id' => 0,
                'user_id' => $staffid
            ));
        }
        foreach ($per as $v) {
            if (isset($v['role'])) {
                $v['role'] = $v['role'];
            } else {
                $v['role'] = get_role('team-member');
            }
            if (isset($v['team'])) {
                $v['team'] = $v['team'];
            } else {
                $v['team'] = "";
            }
            $this->db->insert('tblroleuserteam', array(
                'role_id' => $v['role'],
                'team_id' => $v['team'],
                'user_id' => $staffid
            ));
        }

        if ($staffid) {
            $brandid = get_user_session();

            $branddata = array();
            $branddata['staffid'] = $staffid;
            $branddata['brandid'] = $brandid;
            $branddata['active'] = $data['active'];

            $this->db->insert('tblstaffbrand', $branddata);

            $sl = $data['firstname'] . ' ' . $data['lastname'];
            if ($sl == ' ') {
                $sl = 'unknown-' . $staffid;
            }

            if ($send_welcome_email == true) {
                $this->load->model('emails_model');
                $merge_fields = array();
                $merge_fields = array_merge($merge_fields, get_staff_merge_fields($staffid, $original_password));
                $this->emails_model->send_email_template('new-staff-created', $data['email'], $merge_fields);
            }
            $this->db->where('staffid', $staffid);
            $this->db->update('tblstaff', array(
                'media_path_slug' => slug_it($sl)
            ));

            if (isset($custom_fields)) {
                handle_custom_fields_post($staffid, $custom_fields);
            }
            if (isset($departments)) {
                foreach ($departments as $department) {
                    $this->db->insert('tblstaffdepartments', array(
                        'staffid' => $staffid,
                        'departmentid' => $department
                    ));
                }
            }


            //$staffid;


            // $_all_permissions = $this->roles_model->get_permissions();
            // foreach ($_all_permissions as $permission) {
            //     $this->db->insert('tblstaffpermissions', array(
            //         'permissionid' => $permission['permissionid'],
            //         'staffid' => $staffid,
            //         'can_view' => 0,
            //         'can_view_own' => 0,
            //         'can_edit' => 0,
            //         'can_create' => 0,
            //         'can_delete' => 0
            //     ));
            // }
            // foreach ($this->perm_statements as $c) {
            //     foreach ($permissions as $key => $p) {
            //         if ($key == $c) {
            //             foreach ($p as $perm) {
            //                 $this->db->where('staffid', $staffid);
            //                 $this->db->where('permissionid', $perm);
            //                 $this->db->update('tblstaffpermissions', array(
            //                     'can_' . $c => 1
            //                 ));
            //             }
            //         }
            //     }
            // }

            logActivity('New Staff Member Added [ID: ' . $staffid . ', ' . $data['firstname'] . ' ' . $data['lastname'] . ']');
            // Delete all staff permission if is admin we dont need permissions stored in database (in case admin check some permissions)
            if ($data['admin'] == 1) {
                $this->db->where('staffid', $staffid);
                $this->db->delete('tblstaffpermissions');
            }
            // Get all announcements and set it to read.
            $this->db->select('announcementid');
            $this->db->from('tblannouncements');
            $this->db->where('showtostaff', 1);
            $announcements = $this->db->get()->result_array();
            foreach ($announcements as $announcement) {
                $this->db->insert('tbldismissedannouncements', array(
                    'announcementid' => $announcement['announcementid'],
                    'staff' => 1,
                    'userid' => $staffid
                ));
            }
            do_action('staff_member_created', $staffid);

            return $staffid;
        }

        return false;
    }

    /**
     * Update staff member info
     * @param  array $data staff data
     * @param  mixed $id staff id
     * @return boolean
     */
    public function update($data, $id)
    {
        unset($data['imagebase64']);
        if (isset($data['fakeusernameremembered'])) {
            unset($data['fakeusernameremembered']);
        }
        if (isset($data['fakepasswordremembered'])) {
            unset($data['fakepasswordremembered']);
        }

        $to_do_list = @$data['widget_type'];
        unset($data['widget_type']);

        $quick_link_types = @$data['quick_link_type'];
        unset($data['quick_link_type']);

        $hook_data['data'] = $data;
        $hook_data['userid'] = $id;
        $hook_data = do_action('before_update_staff_member', $hook_data);
        $data = $hook_data['data'];
        $id = $hook_data['userid'];

        if (is_admin()) {
            if (isset($data['administrator'])) {
                $data['admin'] = 1;
                unset($data['administrator']);
            } else {
                if ($id != get_staff_user_id()) {
                    if ($id == 1) {
                        return array(
                            'cant_remove_main_admin' => true
                        );
                    }
                } else {
                    return array(
                        'cant_remove_yourself_from_admin' => true
                    );
                }
                $data['admin'] = 0;
            }
        }

        $affectedRows = 0;
        if (isset($data['departments'])) {
            $departments = $data['departments'];
            unset($data['departments']);
        }
        // $permissions = array();
        // if (isset($data['view'])) {
        //     $permissions['view'] = $data['view'];
        //     unset($data['view']);
        // }

        // if (isset($data['view_own'])) {
        //     $permissions['view_own'] = $data['view_own'];
        //     unset($data['view_own']);
        // }
        // if (isset($data['edit'])) {
        //     $permissions['edit'] = $data['edit'];
        //     unset($data['edit']);
        // }
        // if (isset($data['create'])) {
        //     $permissions['create'] = $data['create'];
        //     unset($data['create']);
        // }
        // if (isset($data['delete'])) {
        //     $permissions['delete'] = $data['delete'];
        //     unset($data['delete']);
        // }
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $this->load->helper('phpass');
            $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
            $data['password'] = $hasher->HashPassword($data['password']);
            $data['last_password_change'] = date('Y-m-d H:i:s');
        }


        if (isset($data['two_factor_auth_enabled'])) {
            $data['two_factor_auth_enabled'] = 1;
        } else {
            $data['two_factor_auth_enabled'] = 0;
        }

        if (isset($data['is_not_staff'])) {
            $data['is_not_staff'] = 1;
        } else {
            $data['is_not_staff'] = 0;
        }

        if (isset($data['admin']) && $data['admin'] == 1) {
            $data['is_not_staff'] = 0;
        }

        $data['email_signature'] = nl2br_save_html($data['email_signature']);

        $this->load->model('departments_model');
        $staff_departments = $this->departments_model->get_staff_departments($id);
        if (sizeof($staff_departments) > 0) {
            if (!isset($data['departments'])) {
                $this->db->where('staffid', $id);
                $this->db->delete('tblstaffdepartments');
            } else {
                foreach ($staff_departments as $staff_department) {
                    if (isset($departments)) {
                        if (!in_array($staff_department['departmentid'], $departments)) {
                            $this->db->where('staffid', $id);
                            $this->db->where('departmentid', $staff_department['departmentid']);
                            $this->db->delete('tblstaffdepartments');
                            if ($this->db->affected_rows() > 0) {
                                $affectedRows++;
                            }
                        }
                    }
                }
            }
            if (isset($departments)) {
                foreach ($departments as $department) {
                    $this->db->where('staffid', $id);
                    $this->db->where('departmentid', $department);
                    $_exists = $this->db->get('tblstaffdepartments')->row();
                    if (!$_exists) {
                        $this->db->insert('tblstaffdepartments', array(
                            'staffid' => $id,
                            'departmentid' => $department
                        ));
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
            }
        } else {
            if (isset($departments)) {
                foreach ($departments as $department) {
                    $this->db->insert('tblstaffdepartments', array(
                        'staffid' => $id,
                        'departmentid' => $department
                    ));
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        }

        if (isset($data['permission']) && $data['user_type'] != 1) {
            $per = array_filter(array_map('array_filter', $data['permission']));
            unset($data['permission']);
        } else {
            $per = array();
            unset($data['permission']);
        }
        $data['updated_by'] = $this->session->userdata['staff_user_id'];
        $data['updated_date'] = date('Y-m-d H:i:s');
        $this->db->where('staffid', $id);
        $this->db->update('tblstaff', $data);


        /*
        ** Added By Sanjay on 02/13/2018 
        ** updating dashboard settings data
        */
        $this->db->where('staffid', $id);
        $this->db->where('brandid', get_user_session());
        $_exists = $this->db->get('tbldashboard_settings')->row();
        if (!$_exists) {
            $dashboard_data['widget_type'] = 'upcoming_project,pinned_item,calendar,weather,contacts,quick_link,lead_pipeline,messages,getting_started,task_list';
            $dashboard_data['quick_link_type'] = 'lead,project,message,task_due,meeting,amount_receivable,amount_received,invite';
            $dashboard_data['order'] = '[{"widget_name":"getting_started","order":0},{"widget_name":"lead_pipeline","order":1},{"widget_name":"calendar","order":2},{"widget_name":"pinned_item","order":3},{"widget_name":"quick_link","order":4},{"widget_name":"upcoming_project","order":5},{"widget_name":"contacts","order":6},{"widget_name":"messages","order":7},{"widget_name":"task_list","order":8}]';
            $dashboard_data['is_visible'] = 1;
            $dashboard_data['brandid'] = get_user_session();
            $dashboard_data['dateadded'] = date('Y-m-d H:i:s');
            $dashboard_data['addedby'] = $this->session->userdata['staff_user_id'];
            $dashboard_data['staffid'] = $id;
            $this->db->insert('tbldashboard_settings', $dashboard_data);
        } else {
            $dashboard_data['widget_type'] = implode(",", $to_do_list);
            $dashboard_data['quick_link_type'] = implode(",", $quick_link_types);
            $dashboard_data['order'] = '[{"widget_name":"getting_started","order":0},{"widget_name":"lead_pipeline","order":1},{"widget_name":"calendar","order":2},{"widget_name":"pinned_item","order":3},{"widget_name":"quick_link","order":4},{"widget_name":"upcoming_project","order":5},{"widget_name":"contacts","order":6},{"widget_name":"messages","order":7},{"widget_name":"task_list","order":8}]';
            $dashboard_data['is_visible'] = 1;
            $dashboard_data['brandid'] = get_user_session();
            $dashboard_data['dateupdated'] = date('Y-m-d H:i:s');
            $dashboard_data['updatedby'] = $this->session->userdata['staff_user_id'];
            $this->db->where('staffid', $id);
            $this->db->update('tbldashboard_settings', $dashboard_data);
        }

        /*
        ** Added By vaidehi on 03/21/2018 
        ** for report configuration
        */
        if (is_sido_admin()) {
            $this->db->where('staff_user_id', $id);
            $this->db->where('brandid', get_user_session());
            $_exists = $this->db->get('tblreportconfiguration')->row();
            if (!$_exists) {
                $report_data['report_name'] = 'Sign Up';
                $report_data['is_visible'] = 1;
                $report_data['report_order'] = 0;
                $report_data['default_records'] = 5;
                $report_data['staff_user_id'] = $id;
                $report_data['brandid'] = get_user_session();
                $report_data['createdby'] = $id;
                $report_data['datecreated'] = date('Y-m-d H:i:s');

                $this->db->insert('tblreportconfiguration', $report_data);

                $report_data['report_name'] = 'Subscribers';
                $report_data['is_visible'] = 1;
                $report_data['report_order'] = 1;
                $report_data['default_records'] = 5;
                $report_data['staff_user_id'] = $id;
                $report_data['brandid'] = get_user_session();
                $report_data['createdby'] = $id;
                $report_data['datecreated'] = date('Y-m-d H:i:s');

                $this->db->insert('tblreportconfiguration', $report_data);

                $report_data['report_name'] = 'Conversion Rate';
                $report_data['is_visible'] = 1;
                $report_data['report_order'] = 2;
                $report_data['default_records'] = 5;
                $report_data['staff_user_id'] = $id;
                $report_data['brandid'] = get_user_session();
                $report_data['createdby'] = $id;
                $report_data['datecreated'] = date('Y-m-d H:i:s');

                $this->db->insert('tblreportconfiguration', $report_data);

                $report_data['report_name'] = 'Net Revenue';
                $report_data['is_visible'] = 1;
                $report_data['report_order'] = 3;
                $report_data['default_records'] = 5;
                $report_data['staff_user_id'] = $id;
                $report_data['brandid'] = get_user_session();
                $report_data['createdby'] = $id;
                $report_data['datecreated'] = date('Y-m-d H:i:s');

                $this->db->insert('tblreportconfiguration', $report_data);

                $report_data['report_name'] = 'Churn';
                $report_data['is_visible'] = 1;
                $report_data['report_order'] = 4;
                $report_data['default_records'] = 5;
                $report_data['staff_user_id'] = $id;
                $report_data['brandid'] = get_user_session();
                $report_data['createdby'] = $id;
                $report_data['datecreated'] = date('Y-m-d H:i:s');

                $this->db->insert('tblreportconfiguration', $report_data);
            }
        } else {
            $this->db->where('staff_user_id', $id);
            $this->db->where('brandid', get_user_session());
            $_exists = $this->db->get('tblreportconfiguration')->row();
            if (!$_exists) {
                $report_data['report_name'] = 'Booking Success';
                $report_data['is_visible'] = 1;
                $report_data['report_order'] = 0;
                $report_data['default_records'] = 5;
                $report_data['staff_user_id'] = $id;
                $report_data['brandid'] = get_user_session();
                $report_data['createdby'] = $id;
                $report_data['datecreated'] = date('Y-m-d H:i:s');

                $this->db->insert('tblreportconfiguration', $report_data);

                $report_data['report_name'] = 'Lead Source';
                $report_data['is_visible'] = 1;
                $report_data['report_order'] = 1;
                $report_data['default_records'] = 5;
                $report_data['staff_user_id'] = $id;
                $report_data['brandid'] = get_user_session();
                $report_data['createdby'] = $id;
                $report_data['datecreated'] = date('Y-m-d H:i:s');

                $this->db->insert('tblreportconfiguration', $report_data);

                $report_data['report_name'] = 'Revenue';
                $report_data['is_visible'] = 1;
                $report_data['report_order'] = 2;
                $report_data['default_records'] = 5;
                $report_data['staff_user_id'] = $id;
                $report_data['brandid'] = get_user_session();
                $report_data['createdby'] = $id;
                $report_data['datecreated'] = date('Y-m-d H:i:s');

                $this->db->insert('tblreportconfiguration', $report_data);
            }
        }

        /**
         * Added By : Vaidehi
         * Dt : 11/10/2017
         * added if condition to not delete entry from account owner
         */
        //if($data['user_type'] != 1 ){
        $this->db->where('user_id', $id);
        $this->db->delete('tblroleuserteam');
        //}
        if (isset($data['user_type']) && $data['user_type'] == 1) {
            $this->db->insert('tblroleuserteam', array(
                'role_id' => 1,
                'team_id' => 0,
                'user_id' => $id
            ));
        }
        if (isset($data['is_sido_admin']) && $data['is_sido_admin'] == 1) {
            $this->db->insert('tblroleuserteam', array(
                'role_id' => 2,
                'team_id' => 0,
                'user_id' => $id
            ));
        }
        foreach ($per as $v) {
            if (isset($v['role'])) {
                $v['role'] = $v['role'];
            } else {
                $v['role'] = "";
            }

            if (isset($v['team'])) {
                $v['team'] = $v['team'];
            } else {
                $v['team'] = "";
            }

            $this->db->insert('tblroleuserteam', array(
                'role_id' => $v['role'],
                'team_id' => $v['team'],
                'user_id' => $id
            ));
        }

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        // if ($this->update_permissions($permissions, $id)) {
        //     $affectedRows++;
        // }

        if (isset($data['admin']) && $data['admin'] == 1) {
            $this->db->where('staffid', $id);
            $this->db->delete('tblstaffpermissions');
        }
        if ($affectedRows > 0) {
            do_action('staff_member_updated', $id);
            logActivity('Staff Member Updated [ID: ' . $id . ', ' . $data['firstname'] . ' ' . $data['lastname'] . ']');

            return true;
        }

        return false;
    }

    public function update_permissions($permissions, $id)
    {
        $all_permissions = $this->roles_model->get_permissions();
        if (total_rows('tblstaffpermissions', array(
                'staffid' => $id
            )) == 0) {
            foreach ($all_permissions as $p) {
                $_ins = array();
                $_ins['staffid'] = $id;
                $_ins['permissionid'] = $p['permissionid'];
                $this->db->insert('tblstaffpermissions', $_ins);
            }
        } elseif (total_rows('tblstaffpermissions', array(
                'staffid' => $id
            )) != count($all_permissions)) {
            foreach ($all_permissions as $p) {
                if (total_rows('tblstaffpermissions', array(
                        'staffid' => $id,
                        'permissionid' => $p['permissionid']
                    )) == 0) {
                    $_ins = array();
                    $_ins['staffid'] = $id;
                    $_ins['permissionid'] = $p['permissionid'];
                    $this->db->insert('tblstaffpermissions', $_ins);
                }
            }
        }
        $_permission_restore_affected_rows = 0;
        foreach ($all_permissions as $permission) {
            foreach ($this->perm_statements as $c) {
                $this->db->where('staffid', $id);
                $this->db->where('permissionid', $permission['permissionid']);
                $this->db->update('tblstaffpermissions', array(
                    'can_' . $c => 0
                ));
                if ($this->db->affected_rows() > 0) {
                    $_permission_restore_affected_rows++;
                }
            }
        }
        $_new_permissions_added_affected_rows = 0;
        foreach ($permissions as $key => $val) {
            foreach ($val as $p) {
                $this->db->where('staffid', $id);
                $this->db->where('permissionid', $p);
                $this->db->update('tblstaffpermissions', array(
                    'can_' . $key => 1
                ));
                if ($this->db->affected_rows() > 0) {
                    $_new_permissions_added_affected_rows++;
                }
            }
        }
        if ($_new_permissions_added_affected_rows != $_permission_restore_affected_rows) {
            return true;
        }
    }

    public function update_profile($data, $id)
    {
        $hook_data['data'] = $data;
        $hook_data['userid'] = $id;
        $hook_data = do_action('before_staff_update_profile', $hook_data);
        $data = $hook_data['data'];
        $id = $hook_data['userid'];

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $this->load->helper('phpass');
            $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
            $data['password'] = $hasher->HashPassword($data['password']);
            $data['last_password_change'] = date('Y-m-d H:i:s');
        }

        if (isset($data['two_factor_auth_enabled'])) {
            $data['two_factor_auth_enabled'] = 1;
        } else {
            $data['two_factor_auth_enabled'] = 0;
        }

        $data['email_signature'] = nl2br_save_html($data['email_signature']);

        $this->db->where('staffid', $id);
        $this->db->update('tblstaff', $data);
        if ($this->db->affected_rows() > 0) {
            do_action('staff_member_profile_updated', $id);
            logActivity('Staff Profile Updated [Staff: ' . get_staff_full_name($id) . ']');

            return true;
        }

        return false;
    }

    /**
     * Change staff passwordn
     * @param  mixed $data password data
     * @param  mixed $userid staff id
     * @return mixed
     */
    public function change_password($data, $userid)
    {
        $hook_data['data'] = $data;
        $hook_data['userid'] = $userid;
        $hook_data = do_action('before_staff_change_password', $hook_data);
        $data = $hook_data['data'];
        $userid = $hook_data['userid'];

        $member = $this->get($userid);
        // CHeck if member is active
        if ($member->active == 0) {
            return array(
                array(
                    'memberinactive' => true
                )
            );
        }
        $this->load->helper('phpass');
        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        // Check new old password
        if (!$hasher->CheckPassword($data['oldpassword'], $member->password)) {
            return array(
                array(
                    'passwordnotmatch' => true
                )
            );
        }
        $data['newpasswordr'] = $hasher->HashPassword($data['newpasswordr']);
        $this->db->where('staffid', $userid);
        $this->db->update('tblstaff', array(
            'password' => $data['newpasswordr'],
            'last_password_change' => date('Y-m-d H:i:s')
        ));
        if ($this->db->affected_rows() > 0) {
            logActivity('Staff Password Changed [' . $userid . ']');

            return true;
        }

        return false;
    }

    /**
     * Change staff status / active / inactive
     * @param  mixed $id staff id
     * @param  mixed $status status(0/1)
     */
    public function change_staff_status($id, $status)
    {
        $hook_data['id'] = $id;
        $hook_data['status'] = $status;
        $hook_data = do_action('before_staff_status_change', $hook_data);
        $status = $hook_data['status'];
        $id = $hook_data['id'];

        $this->db->where('staffid', $id);
        $this->db->update('tblstaff', array(
            'active' => $status
        ));
        logActivity('Staff Status Changed [StaffID: ' . $id . ' - Status(Active/Inactive): ' . $status . ']');
    }

    public function get_logged_time_data($id = '', $filter_data = array())
    {
        if ($id == '') {
            $id = get_staff_user_id();
        }
        $result['timesheets'] = array();
        $result['total'] = array();
        $result['this_month'] = array();

        $first_day_this_month = date('Y-m-01'); // hard-coded '01' for first day
        $last_day_this_month = date('Y-m-t 23:59:59');

        $result['last_month'] = array();
        $first_day_last_month = date('Y-m-01', strtotime('-1 MONTH')); // hard-coded '01' for first day
        $last_day_last_month = date('Y-m-t 23:59:59', strtotime('-1 MONTH'));

        $result['this_week'] = array();
        $first_day_this_week = date('Y-m-d', strtotime('monday this week'));
        $last_day_this_week = date('Y-m-d 23:59:59', strtotime('sunday this week'));

        $result['last_week'] = array();

        $first_day_last_week = date('Y-m-d', strtotime('monday last week'));
        $last_day_last_week = date('Y-m-d 23:59:59', strtotime('sunday last week'));

        $this->db->select('task_id,start_time,end_time,staff_id,tbltaskstimers.hourly_rate,name,tbltaskstimers.id,rel_id,rel_type');
        $this->db->where('staff_id', $id);
        $this->db->join('tblstafftasks', 'tblstafftasks.id = tbltaskstimers.task_id');
        $timers = $this->db->get('tbltaskstimers')->result_array();
        $_end_time_static = time();

        $filter_period = false;
        if (isset($filter_data['period-from']) && $filter_data['period-from'] != '' && isset($filter_data['period-to']) && $filter_data['period-to'] != '') {
            $filter_period = true;
            $from = to_sql_date($filter_data['period-from']);
            $from = date('Y-m-d', strtotime($from));
            $to = to_sql_date($filter_data['period-to']);
            $to = date('Y-m-d', strtotime($to));
        }

        foreach ($timers as $timer) {
            $start_date = strftime('%Y-%m-%d', $timer['start_time']);

            $end_time = $timer['end_time'];

            if ($timer['end_time'] == null) {
                $end_time = $_end_time_static;
            }

            $total = $end_time - $timer['start_time'];

            $result['total'][] = $total;
            $timer['total'] = $total;
            $timer['end_time'] = $end_time;

            if ($start_date >= $first_day_this_month && $start_date <= $last_day_this_month) {
                $result['this_month'][] = $total;
                if (isset($filter_data['this_month']) && $filter_data['this_month'] != '') {
                    $result['timesheets'][$timer['id']] = $timer;
                }
            }
            if ($start_date >= $first_day_last_month && $start_date <= $last_day_last_month) {
                $result['last_month'][] = $total;
                if (isset($filter_data['last_month']) && $filter_data['last_month'] != '') {
                    $result['timesheets'][$timer['id']] = $timer;
                }
            }
            if ($start_date >= $first_day_this_week && $start_date <= $last_day_this_week) {
                $result['this_week'][] = $total;
                if (isset($filter_data['this_week']) && $filter_data['this_week'] != '') {
                    $result['timesheets'][$timer['id']] = $timer;
                }
            }
            if ($start_date >= $first_day_last_week && $start_date <= $last_day_last_week) {
                $result['last_week'][] = $total;
                if (isset($filter_data['last_week']) && $filter_data['last_week'] != '') {
                    $result['timesheets'][$timer['id']] = $timer;
                }
            }

            if ($filter_period == true) {
                if ($start_date >= $from && $start_date <= $to) {
                    $result['timesheets'][$timer['id']] = $timer;
                }
            }
        }
        $result['total'] = array_sum($result['total']);
        $result['this_month'] = array_sum($result['this_month']);
        $result['last_month'] = array_sum($result['last_month']);
        $result['this_week'] = array_sum($result['this_week']);
        $result['last_week'] = array_sum($result['last_week']);

        return $result;
    }

    public function getUserType()
    {
        $this->db->where('deleted', 0);
        //Added By Vaidehi to display only admin and team member role
        $this->db->where('isvisible', 1);

        return $this->db->get('tblusertype')->result_array();
    }

    public function get_dashboard_data($id)
    {
        $this->db->where('deleted', 0);
        $this->db->where('staffid', $id);
        return $this->db->get('tbldashboard_settings')->result_array();
    }

    /**
     * Added By: Vaidehi
     * Dt: 03/05/2018
     * get cron events
     */
    public function get_cronsubscriptions()
    {
        $query = $this->db->query('SELECT `tblstaff`.*, `tblclients`.`company`, `tblclients`.`primary_user_id`, `tblpackages`.`name`, `tblpackages`.`trial_period` FROM `tblstaff` LEFT JOIN `tblclients` ON `tblclients`.`primary_user_id` = `tblstaff`.`staffid` LEFT JOIN `tblpackages` ON `tblclients`.`packageid` = `tblpackages`.`packageid` LEFT JOIN `tblpackagetype` ON `tblpackagetype`.`id` = `tblpackages`.`packagetypeid` WHERE `tblclients`.`active` = 1 AND `tblclients`.`is_deleted` = 0 AND `tblpackagetype`.`name` = "Trial" AND `tblpackages`.`status` = 1 AND `tblpackages`.`deleted` = 0 AND `tblstaff`.`active` = 1 AND `tblstaff`.`deleted` = 0 AND `tblstaff`.`user_type` = 1 AND DATE_ADD(DATE(`tblstaff`.`datecreated`), INTERVAL (`tblpackages`.`trial_period` - 1) DAY) = CURRENT_DATE()');

        $response = $query->result_array();

        return $response;
    }

    /**
     * Added By: Vaidehi
     * Dt: 03/21/2018
     * to get logged in staff role
     */
    public function get_staff_role()
    {
        $roles = $this->db->query('SELECT GROUP_CONCAT(`role_id`) AS roleid FROM `tblroleuserteam` GROUP BY `user_id` HAVING `user_id` = ' . get_staff_user_id())->row();
        return $roles;
    }

    public function get_staff($id)
    {
        $this->db->where('staffid', $id);
        return $this->db->get('tblstaff')->row();
    }

    public function getstaff($id = '', $active = '', $where = array())
    {
        if (is_int($active)) {
            $this->db->where('tblstaff.active', $active);
        }
        if (is_numeric($id)) {
            $this->db->select('tblstaff.*');
            $this->db->where('tblstaff.staffid', $id);
            $this->db->where('tblstaff.deleted', 0);
            $staff = $this->db->get('tblstaff')->row();
            return $staff;
        }
    }
}
