<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Teams_model extends CRM_Model
{
    private $perm_statements = array('view', 'view_own', 'edit', 'create', 'delete');

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add new employee team
     * @param mixed $data
     */
    public function add($data)
    {
        $teams = $this->teams_model->check_team_name_exists($data['name'], '');
        if($teams->teamid <= 0 || empty($teams)) {
            $data['brandid'] = get_user_session();
            $data['created_by']  = $this->session->userdata['staff_user_id'];
            $data['created_date'] = date('Y-m-d H:i:s');
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

            $this->db->insert('tblteams', $data);
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                // $_all_permissions = $this->teams_model->get_permissions();
                // foreach ($_all_permissions as $permission) {
                //     $this->db->insert('tblteampermissions', array(
                //         'permissionid' => $permission['permissionid'],
                //         'teamid' => $insert_id,
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
                //                 $this->db->where('teamid', $insert_id);
                //                 $this->db->where('permissionid', $perm);
                //                 $this->db->update('tblteampermissions', array(
                //                     'can_' . $c => 1
                //                 ));
                //             }
                //         }
                //     }
                // }

                logActivity('New Team Added [ID: ' . $insert_id . '.' . $data['name'] . ']');

                return $insert_id;
            }
        }
        return false;
    }

    /**
     * Update employee team
     * @param  array $data team data
     * @param  mixed $id   team id
     * @return boolean
     */
    public function update($data, $id)
    {
        $teams = $this->teams_model->check_team_name_exists($data['name'], $id);
        if($teams->teamid <= 0 || empty($teams)) {
            $affectedRows = 0;
            // $permissions  = array();
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
            // $update_staff_permissions = false;
            // if (isset($data['update_staff_permissions'])) {
            //     $update_staff_permissions = true;
            //     unset($data['update_staff_permissions']);
            // }
            $data['updated_by']     = $this->session->userdata['staff_user_id'];
            $data['updated_date']    = date('Y-m-d H:i:s');
            $this->db->where('teamid', $id);
            $this->db->update('tblteams', $data);
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }


            // $all_permissions = $this->teams_model->get_permissions();
            // if (total_rows('tblteampermissions', array(
            //     'teamid' => $id
            // )) == 0) {
            //     foreach ($all_permissions as $p) {
            //         $_ins                 = array();
            //         $_ins['teamid']       = $id;
            //         $_ins['permissionid'] = $p['permissionid'];
            //         $this->db->insert('tblteampermissions', $_ins);
            //     }
            // } elseif (total_rows('tblteampermissions', array(
            //         'teamid' => $id
            //     )) != count($all_permissions)) {
            //     foreach ($all_permissions as $p) {
            //         if (total_rows('tblteampermissions', array(
            //             'teamid' => $id,
            //             'permissionid' => $p['permissionid']
            //         )) == 0) {
            //             $_ins                 = array();
            //             $_ins['teamid']       = $id;
            //             $_ins['permissionid'] = $p['permissionid'];
            //             $this->db->insert('tblteampermissions', $_ins);
            //         }
            //     }
            // }

            // $_permission_restore_affected_rows = 0;
            // foreach ($all_permissions as $permission) {
            //     foreach ($this->perm_statements as $c) {
            //         $this->db->where('teamid', $id);
            //         $this->db->where('permissionid', $permission['permissionid']);
            //         $this->db->update('tblteampermissions', array(
            //             'can_' . $c => 0
            //         ));
            //         if ($this->db->affected_rows() > 0) {
            //             $_permission_restore_affected_rows++;
            //         }
            //     }
            // }

            // $_new_permissions_added_affected_rows = 0;
            // foreach ($permissions as $key => $val) {
            //     foreach ($val as $p) {
            //         $this->db->where('teamid', $id);
            //         $this->db->where('permissionid', $p);
            //         $this->db->update('tblteampermissions', array(
            //             'can_' . $key => 1
            //         ));
            //         if ($this->db->affected_rows() > 0) {
            //             $_new_permissions_added_affected_rows++;
            //         }
            //     }
            // }
            // if ($_new_permissions_added_affected_rows != $_permission_restore_affected_rows) {
            //     $affectedRows++;
            // }

            // if ($update_staff_permissions == true) {
            //     $this->load->model('staff_model');
            //     $staff = $this->staff_model->get('', '', array(
            //         'team' => $id
            //     ));
            //     foreach ($staff as $m) {
            //         if ($this->staff_model->update_permissions($permissions, $m['staffid'])) {
            //             $affectedRows++;
            //         }
            //     }
            // }

            if ($affectedRows > 0) {
                logActivity('Team Updated [ID: ' . $id . '.' . $data['name'] . ']');

                return true;
            }
        }
        return false;
    }

    /**
     * Get employee team by id
     * @param  mixed $id Optional team id
     * @return mixed     array if not id passed else object
     */
    public function get($id = '')
    {
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', 0);
        if (is_numeric($id)) {
            $this->db->where('teamid', $id);

            return $this->db->get('tblteams')->row();
        }
        $this->db->order_by('name', 'asc');
        return $this->db->get('tblteams')->result_array();
    }

    /**
     * Delete employee team
     * @param  mixed $id team id
     * @return mixed
     */
    public function delete($id)
    {
        $current = $this->get($id);
        // Check first if team is used in table
        if (is_reference_in_table('team_id', 'tblroleuserteam', $id)) {
            return array(
                'referenced' => true
            );
        }
        $affectedRows = 0;
        // $this->db->where('teamid', $id);
        // $this->db->delete('tblteams');
        $data['deleted']        = 1;
        $data['updated_by']     = $this->session->userdata['staff_user_id'];
        $data['updated_date']    = date('Y-m-d H:i:s');
        $this->db->where('teamid', $id);
        $this->db->update('tblteams', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        // $this->db->where('teamid', $id);
        // $this->db->delete('tblteampermissions');
        // if ($this->db->affected_rows() > 0) {
        //     $affectedRows++;
        // }
        if ($affectedRows > 0) {
            logActivity('Team Deleted [ID: ' . $id);

            return true;
        }

        return false;
    }

    /**
     * Get employee team permissions
     * @param  mixed $id permission id
     * @return mixed if id passed return object else array
     */
    public function get_permissions($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('permissionid', $id);

            return $this->db->get('tblpermissions')->row();
        }
        $brandid = get_user_session();
        $get_session_data = get_session_data();
        $user_package = $get_session_data['package_id'];
        $package = array();
        if($brandid > 0){
            $this->db->where('packageid', $user_package);
            $this->db->where('can_access', 1);
            $package = $this->db->get('tblpackagepermissions')->result_array();
            $package = array_column($package, 'permissionid');
        }
        if(!empty($package)){
            $this->db->where_in('permissionid', $package); 
        }
        $this->db->order_by('name', 'asc');

        return $this->db->get('tblpermissions')->result_array();
    }

    /**
     * Get specific team permissions
     * @param  mixed $id team id
     * @return array
     */
    public function get_team_permissions($id)
    {
        $this->db->where('teamid', $id);
        $this->db->join('tblpermissions', 'tblpermissions.permissionid = tblteampermissions.permissionid', 'left');

        return $this->db->get('tblteampermissions')->result_array();
    }

    /**
     * Get staff permission / Staff can have other permissions too different from the team which is assigned
     * @param  mixed $id Optional - staff id
     * @return array
     */
    public function get_staff_permissions($id = '')
    {
        // If not id is passed get from current user
        if ($id == false) {
            $id = get_staff_user_id();
        }
        $this->db->where('staffid', $id);

        return $this->db->get('tblstaffpermissions')->result_array();
    }

    public function get_contact_permissions($id)
    {
        $this->db->where('userid', $id);

        return $this->db->get('tblcontactpermissions')->result_array();
    }

    /**
     * Get team id
     * @param  mixed $id permission id
     * @return mixed if id passed return object else array
     */
    public function check_team_name_exists($name, $id)
    {
        $brandid=get_user_session();
        if($id > 0) {
            $where = array('teamid !=' => $id, 'name =' => $name, 'deleted =' => 0, 'brandid =' => $brandid);
        } else {
            $where = array('name =' => $name, 'deleted =' => 0, 'brandid =' => $brandid);
        }
        return $this->db->where($where)->get('tblteams')->row();
    }
}