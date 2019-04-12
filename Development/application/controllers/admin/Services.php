<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Services extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('services_model');       
    }

    /* List all services */
    public function index()
    {                             

        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('brandtype');
        }
        $data['title'] = _l('services');
        
        $this->load->view('admin/brandtype/manage', $data);
    }

    /* Add or edit service / ajax */
    public function manage()
    {          
        if ($this->input->post()) {
            $data = $this->input->post();
            
            if ($data['brandtypeid'] == '') {                
                $success = $this->services_model->add($data);
                $message = '';
                if ($success == true) {
                    $message = _l('added_successfully', _l('service'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            } else {
               
                $success = $this->services_model->edit($data);
                $message = '';
                if (is_array($success)) {
                    $success = false;
                    $message = _l('Unable to save service');
                } elseif ($success == true) {
                    $message = _l('updated_successfully', _l('service'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            }
        }
    }

    /* Delete service from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('services'));
        }
       
        $response = $this->services_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('service_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('services')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('service_lowercase')));
        }               
    }

    public function service_name_exists()
    {
        if ($this->input->post()) {
            $service_id = $this->input->post('brandtypeid');
            if ($service_id != '') {
                $this->db->where('brandtypeid', $service_id);                                         
                                      
                $_current_service = $this->db->get('tblbrandtype')->row();
                if ($_current_service->name == $this->input->post('name')) {
                    echo json_encode(true);
                    die();
                }
            }
            $this->db->where('name', $this->input->post('name'));           
            $total_rows = $this->db->count_all_results('tblbrandtype');
            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            die();
        }
    }
}
