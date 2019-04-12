<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Agreements_model extends CRM_Model
{
    private $perm_statements = array('view', 'view_own', 'edit', 'create', 'delete');

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add new employee agreement
     * @param mixed $data
     */
    public function addagreement($data)
    {
        $agreements = $this->agreements_model->check_agreementagreement_name_exists($data['name'], '');
        if($agreements->templateid <= 0 || empty($agreements)) {

            $data['brandid'] = get_user_session();
            $data['created_by']  = $this->session->userdata['staff_user_id'];
            $data['datecreated'] = date('Y-m-d H:i:s');
            unset($data['agreementid']);
            $this->db->insert('tblagreementtemplates', $data);
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                
                logActivity('New Agreement Template Added [ID: ' . $insert_id . '.' . $data['name'] . ']');

                return $insert_id;
            }
        }
        return false;
    }

    /**
     * Update employee agreement
     * @param  array $data agreement data
     * @param  mixed $id   agreement id
     * @return boolean
     */
    public function updateagreement($data, $id)
    {
        $agreements = $this->agreements_model->check_agreementagreement_name_exists($data['name'], $id);
        if($agreements->templateid <= 0 || empty($agreements)) {
            $affectedRows = 0;
            unset($data['agreementid']);
            $data['updated_by']     = $this->session->userdata['staff_user_id'];
            $data['dateupdated']    = date('Y-m-d H:i:s');
            $this->db->where('templateid', $id);
            $this->db->update('tblagreementtemplates', $data);
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }

            if ($affectedRows > 0) {
                logActivity('Agreement Template Updated [ID: ' . $id . '.' . $data['name'] . ']');

                return true;
            }
        }
        return false;
    }

    /**
     * Get employee agreement by id
     * @param  mixed $id Optional agreement id
     * @return mixed     array if not id passed else object
     */
    public function getagreements($id = '')
    {
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', 0);
        if (is_numeric($id)) {
            $this->db->where('templateid', $id);

            return $this->db->get('tblagreementtemplates')->row();
        }
        $this->db->order_by('datecreated', 'desc');
        return $this->db->get('tblagreementtemplates')->result_array();
    }

    /**
     * Delete employee agreement
     * @param  mixed $id agreement id
     * @return mixed
     */
    public function deleteagreement($id)
    {
        //$current = $this->getagreements($id);
        // Check first if agreement is used in table
        // if (is_reference_in_table('agreement_id', 'tblroleuseragreement', $id)) {
        //     return array(
        //         'referenced' => true
        //     );
        // }
        $affectedRows = 0;
        // $this->db->where('templateid', $id);
        // $this->db->delete('tblagreementtemplates');
        $data['deleted']        = 1;
        $data['updated_by']     = $this->session->userdata['staff_user_id'];
        $data['dateupdated']    = date('Y-m-d H:i:s');
        $this->db->where('templateid', $id);
        $this->db->update('tblagreementtemplates', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        // $this->db->where('templateid', $id);
        // $this->db->delete('tblagreementpermissions');
        // if ($this->db->affected_rows() > 0) {
        //     $affectedRows++;
        // }
        if ($affectedRows > 0) {
            logActivity('Agreement Template Deleted [ID: ' . $id);

            return true;
        }

        return false;
    }

    /**
     * Get agreement id
     * @param  mixed $id permission id
     * @return mixed if id passed return object else array
     */
    public function check_agreementagreement_name_exists($name, $id)
    {
        $brandid=get_user_session();
        if($id > 0) {
            $where = array('templateid !=' => $id, 'name =' => $name, 'deleted =' => 0, 'brandid =' => $brandid);
        } else {
            $where = array('name =' => $name, 'deleted =' => 0, 'brandid =' => $brandid);
        }
        return $this->db->where($where)->get('tblagreementtemplates')->row();
    }
}