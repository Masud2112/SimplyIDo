<?php
/**
* Added By: Vaidehi
* Dt: 10/14/2017
* Brands Module
*/
defined('BASEPATH') or exit('No direct script access allowed');
class Brands extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('brands_model');
        $this->load->library('form_validation');
    }

    /* List all brands */
    public function index()
    {
        if (!has_permission('brands', '', 'view', true)) {
            if (!have_assigned_customers() && !has_permission('brands', '', 'create', true)) {
                access_denied('brands');
            }
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('brands');
        }
        
        $data['title']          = _l('brands');
        
        $this->load->view('admin/brands/manage', $data);
    }

    /* Edit brand or add new brands*/
    public function brand($id = '')
    {
        if (!has_permission('brands', '', 'view', true)) {
            if ($id != '') {
                access_denied('brands');
            }
        }

        $title = _l('add_new', _l('brand'));

        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('brands', '', 'create', true)) {
                    access_denied('brands');
                }

                $this->form_validation->set_rules('brandname', _l('brand_name_required'), 'trim|required');
                $this->form_validation->set_rules('brandtype[]', _l('brand_type_required'), 'required');
                $this->form_validation->set_rules('address1', _l('address_required'), 'trim|required');
                $this->form_validation->set_rules('city', _l('city_required'), 'trim|required');
                $this->form_validation->set_rules('state', _l('state_required'), 'trim|required');
                $this->form_validation->set_rules('zipcode', _l('zipcode_required'), 'trim|required');
                $this->form_validation->set_rules('country', _l('country_required'), 'trim|required');

                if ($this->input->post()) {
                    if ($this->form_validation->run() !== false) {
                        $isdefault = 0;
                        if($this->input->post('isdefault')){
                            $isdefault=$this->input->post('isdefault');
                        }
                        $data1['brandname']      = $this->input->post('brandname');
                        $data1['brandtype']      = $this->input->post('brandtype');
                        $data1['address']        = $this->input->post('address1') . " " . $this->input->post('address2');
                        $data1['city']           = $this->input->post('city');
                        $data1['state']          = $this->input->post('state');
                        $data1['zipcode']        = $this->input->post('zipcode');
                        $data1['phone']        = $this->input->post('phone');
                        $data1['email']        = $this->input->post('email');
                        $data1['isdefault']      = $isdefault;

                        $id = $this->brands_model->add($data1);
                        
                        if ($id) {
                            set_alert('success', _l('added_successfully', _l('brand')));
                            $this->session->set_userdata('brand_id', $id);
                            redirect(admin_url('brand_settings'));
                        }
                    }
                } else {
                    set_alert('danger',_l('valid_form'));
                }
            }
        }

        /**
        * Added By : Vaidehi
        * Dt : 11/09/2017
        * to get number of brands created and can be created based on package of logged in user
        */
        $response = $this->get_module_creation_access('brands');
        
        if(!empty($response)) {
            $data['module_create_restriction']  = $response['module_create_restriction'];
            $data['module_active_entries']      = $response['module_active_entries'];
            $data['packagename']                = $response['packagename'];    
        }
        
        $data['brandtypes']     = $this->brands_model->get_brandtypes();
        $data['title']          = $title;

        $this->load->view('admin/brands/brand', $data);
    }

    /* Delete brands */
    public function delete($id)
    {
        if (!has_permission('brands', '', 'delete', true)) {
            access_denied('brands');
        }
        if (!$id) {
            redirect(admin_url('brands'));
        }
        $response = $this->clients_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('brand_delete_invoices_warning'));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('brand')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('brand_lowercase')));
        }
        redirect(admin_url('brands'));
    }

    //check for unique brand name
    public function brandexists()
    {
        $brandname = $this->input->post('brandname');
        $brands = $this->brands_model->check_brand_exists($brandname);
        
        if(empty($brands) || count($brands) == 0) {
            echo "success";
            die();
        }

        echo "failure";
        die();
    }
}
