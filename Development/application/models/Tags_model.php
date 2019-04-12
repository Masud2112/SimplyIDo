<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Tags_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get tag by id
     * @param  mixed $id tag id
     * @return mixed     if id passed return object else array
     */
    public function get($id = '', $where = array())
    {
        $brandid = get_user_session();
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
        $this->db->where($where);        
        $this->db->where('deleted', 0);
        if($brandid > 0){
            $this->db->where('brandid', $brandid);
        }  
        else if($is_sido_admin > 0){
            $this->db->where('brandid', 0);
        } 
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get('tbltags')->row();
        }
        $this->db->order_by('name', 'ASC');

        return $this->db->get('tbltags')->result_array();
    }

    /**
     * Add new tag
     * @param array $data tag data
     * @return boolean
     */
    public function add($data)
    {
        unset($data['tagid']);

        /**
        * Added By : Vaidehi
        * Dt : 11/20/2017
        * to check tag name exists in db or not
        */
        $this->db->where('name', $this->input->post('name'));
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0'); 
        $total_rows = $this->db->count_all_results('tbltags');
        
        if ($total_rows <= 0) {
            $data['name']           = trim($data['name']);
            $data['color']          = trim($data['color']);
            $data["brandid"] 	= get_user_session();
            $data['created_by']     = $this->session->userdata['staff_user_id'];
            $data['datecreated']    = date('Y-m-d H:i:s');
            $this->db->insert('tbltags', $data);
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                logActivity('New tag Added [ID: ' . $insert_id . ', ' . $data['name'] . ']');

                return true;
            }
        } 

        return false;
    }

    /**
     * Edit tag
     * @param  array $data tag data
     * @return boolean
     */
    public function edit($data)
    {        
        $tagid        = $data['tagid'];
       
        unset($data['tagid']);
        $data['name'] = trim($data['name']);
        $data['color'] = trim($data['color']);
        $data['updated_by']     = $this->session->userdata['staff_user_id'];
        $data['dateupdated']    = date('Y-m-d H:i:s');
        $this->db->where('id', $tagid);
        $this->db->update('tbltags', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('tag Updated [ID: ' . $tagid . ', ' . $data['name'] . ']');
            // Check if this task is used in settings
            /*$default_tags = unserialize(get_option('default_tag'));
            $i = 0;
            foreach($default_tags as $tag){
                $current_tag = $this->get($tagid);
                $tag_name      = $original_tag->name;
                if (strpos('x'.$tag, $tag_name) !== false) {
                    $default_tags[$i] = str_ireplace($tag_name, $current_tag->name . '|' . $current_tag->color, $default_tags[$i]);
                }
                $i++;
            }
            update_option('default_tag', serialize($default_tags));*/
            return true;
        }

        return false;
    }

    /**
     * Delete tag from database
     * @param  mixed $id tag id
     * @return boolean
     */
    public function delete($id)
    {
        /*if (
            is_reference_in_table('tag', 'tblleads', $id)
            || is_reference_in_table('tag2', 'tbtask', $id)
            || is_reference_in_table('tag', 'tblevents', $id)
            //|| is_reference_in_table('tag2', 'tblexpenses', $id)
            ) {
            return array(
                'referenced' => true
            );
        }*/

        $data['deleted']        = 1;
        $data['updated_by']     = $this->session->userdata['staff_user_id'];
        $data['dateupdated']    = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update('tbltags', $data);        
        if ($this->db->affected_rows() > 0) {
            logActivity('Tag deleted successfully');

            return true;
        }

        return false;
    }
}
