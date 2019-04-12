<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Services_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get brand type by brandtypeid
     * @param  mixed $id brandtypeid
     * @return mixed     if id passed return object else array
     */
    public function get($id = '', $where = array())
    {
        $this->db->where($where);        
        if (is_numeric($id)) {
            $this->db->where('brandtypeid', $id);

            return $this->db->get('tblbrandtype')->row();
        }
        $this->db->order_by('name', 'ASC');

        return $this->db->get('tblbrandtype')->result_array();
    }

    /**
     * Add new service
     * @param array $data service data
     * @return boolean
     */
    public function add($data)
    {
        unset($data['brandtypeid']);
        $data['name']           = trim($data['name']);
        $data['datecreated']    = date('Y-m-d H:i:s');
        $this->db->insert('tblbrandtype', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New service Added [ID: ' . $insert_id . ', ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Edit service
     * @param  array $data service data
     * @return boolean
     */
    public function edit($data)
    {        
        $serviceid        = $data['brandtypeid'];
       
        unset($data['brandtypeid']);
        $data['name'] = trim($data['name']);                
        $this->db->where('brandtypeid', $serviceid);
        $this->db->update('tblbrandtype', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('service Updated [ID: ' . $serviceid . ', ' . $data['name'] . ']');            
            return true;
        }

        return false;
    }

    /**
     * Delete service from database
     * @param  mixed $id brandtypeid
     * @return boolean
     */
    public function delete($id)
    {
        if (
            is_reference_in_table('brandtypeid', 'tblbrand', $id)) {
            return array(
                'referenced' => true
            );
        }
       
        $this->db->where('brandtypeid', $id);
        $this->db->delete('tblbrandtype');        
        if ($this->db->affected_rows() > 0) {
            logActivity('Service deleted successfully');

            return true;
        }

        return false;
    }
}
