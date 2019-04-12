<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Paymentschedules extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        // Model is autoloaded
    }

    /* List all staff paymentschedules */
    public function index()
    {
        if (!has_permission('paymentschedules', '', 'view', true)) {
            access_denied('paymentschedules');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('paymentschedules');
        }
        $data['title'] = _l('all_paymentschedules');
        $this->load->view('admin/paymentschedules/manage', $data);
    }

    /* Add new paymentschedule or edit existing one */
    public function paymentschedule($id = '')
    {
        if (!has_permission('paymentschedules', '', 'view', true)) {
            access_denied('paymentschedules');
        }
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('paymentschedules', '', 'create', true)) {
                    access_denied('paymentschedules');
                }
                
                $id = $this->paymentschedules_model->addpaymentschedule($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('paymentschedule')));
                    //redirect(admin_url('paymentschedules/paymentschedule/' . $id));
                    redirect(admin_url('paymentschedules'));
                } else {
                    set_alert('danger', _l('problem_paymentschedule_adding', _l('paymentschedule_lowercase')));
                    redirect(admin_url('paymentschedules/paymentschedule/' . $id));
                }

            } else {
                if (!has_permission('paymentschedules', '', 'edit', true)) {
                    access_denied('paymentschedules');
                }
                
                $success = $this->paymentschedules_model->updatepaymentschedule($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('paymentschedule')));
                    redirect(admin_url('paymentschedules'));
                } else {
                    set_alert('danger', _l('problem_paymentschedule_updating', _l('paymentschedule_lowercase')));
                    redirect(admin_url('paymentschedules/paymentschedule/' . $id));
                }
                redirect(admin_url('paymentschedules/paymentschedule/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('paymentschedule'));
        } else {
            $paymentschedule                     = $this->paymentschedules_model->getpaymentschedules($id);
            $data['paymentschedule']             = $paymentschedule;
            $title                    = _l('edit', _l('paymentschedule')) . ' ' . $paymentschedule->name;
        }
        $data['duedate_types'] = get_duedate_type();
        $data['duedate_criteria'] = get_duedate_criteria();
        $data['duedate_duration'] = get_duedate_duration();
        $data['amount_types'] = get_amount_type();
        $data['title']       = $title;
        $this->load->view('admin/paymentschedules/paymentschedule', $data);
    }

    /* Delete staff paymentschedule from database */
    public function deletepaymentschedule($id)
    {
        if (!has_permission('paymentschedules', '', 'delete', true)) {
            access_denied('paymentschedules');
        }
        if (!$id) {
            redirect(admin_url('paymentschedules'));
        }
        $response = $this->paymentschedules_model->deletepaymentschedule($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('paymentschedule')));
        } else {
            set_alert('danger', _l('problem_deleting', _l('paymentschedule_lowercase')));
        }
        //redirect(admin_url('paymentschedules'));
    }
    public function check_paymentschedule_name_exists()
    {
        $result = $this->paymentschedules_model->check_paymentschedule_name_exists($this->input->post('pmt_sdl_name'));

        if(count($result)> 0 && $result->templateid !=$this->input->post('tagid')){
            echo json_encode(false);
        }else{
            echo json_encode(true);
        }
        die();
    }
}