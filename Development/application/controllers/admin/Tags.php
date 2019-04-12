<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Tags extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tags_model');       
    }

    /* List all tags */
    public function index()
    {              
       
        if (!has_permission('lists', '', 'view', true)) {
                access_denied('lists');
        }

        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('tags');
        }
        $data['title'] = _l('tags');
        
        $this->load->view('admin/tags/manage', $data);
    }

    /* Add or edit tag / ajax */
    public function manage()
    {
        if (!has_permission('lists', '', 'view', true)) {
            access_denied('lists');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $data["brandid"] = get_user_session();
            if ($data['tagid'] == '') {
                if (!has_permission('lists', '', 'create', true)) {
                    access_denied('lists');
                }
                $success = $this->tags_model->add($data);
                $message = '';
                if ($success == true) {
                    $message = _l('added_successfully', _l('tag'));
                } else {
                    $message = _l('is_referenced', _l('tag'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            } else {
                if (!has_permission('lists', '', 'edit', true)) {
                    access_denied('lists');
                }
                $success = $this->tags_model->edit($data);
                $message = '';
                if (is_array($success)) {
                    $success = false;
                    $message = _l('Unable to save tag');
                } elseif ($success == true) {
                    $message = _l('updated_successfully', _l('tag'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            }
        }
    }

    /* Delete tag from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('tags'));
        }
        if (has_permission('lists', '', 'delete', true)) {
            $response = $this->tags_model->delete($id);
            if (is_array($response) && isset($response['referenced'])) {
                set_alert('warning', _l('is_referenced', _l('tag_lowercase')));
            } elseif ($response == true) {
                set_alert('success', _l('deleted', _l('tag')));
            } else {
                set_alert('warning', _l('problem_deleting', _l('tag_lowercase')));
            }
        }
        //redirect(admin_url('tags'));
        
    }

    public function tag_name_exists()
    {
        if ($this->input->post()) {
            $tag_id = $this->input->post('tagid');
            if ($tag_id != '') {
                $this->db->where('id', $tag_id);         
                $this->db->where('brandid', get_user_session());
                $this->db->where('deleted', '0'); 
                                      
                $_current_tag = $this->db->get('tbltags')->row();
                if ($_current_tag->name == $this->input->post('name')) {
                    echo json_encode(true);
                    die();
                }
            }
            $this->db->where('name', $this->input->post('name'));
            $this->db->where('brandid', get_user_session());
            $this->db->where('deleted', '0'); 
            $total_rows = $this->db->count_all_results('tbltags');
            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            die();
        }
    }
}
