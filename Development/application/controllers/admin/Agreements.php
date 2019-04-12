<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Agreements extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('emails_model');
        // Model is autoloaded
    }

    /* List all staff agreements */
    public function index()
    {
        if (!has_permission('agreements', '', 'view', true)) {
            access_denied('agreements');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('agreements');
        }
        $data['title'] = _l('agreements');
        $this->load->view('admin/agreements/manage', $data);
    }

    /* Add new agreement or edit existing one */
    public function agreement($id = '')
    {
        if (!has_permission('agreements', '', 'view', true)) {
            access_denied('agreements');
        }
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('agreements', '', 'create', true)) {
                    access_denied('agreements');
                }
                
                $id = $this->agreements_model->addagreement($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('agreement')));
                    //redirect(admin_url('agreements/agreement/' . $id));
                    redirect(admin_url('agreements'));
                } else {
                    set_alert('danger', _l('problem_agreement_adding', _l('agreement_lowercase')));
                    redirect(admin_url('agreements/agreement/' . $id));
                }

            } else {
                if (!has_permission('agreements', '', 'edit', true)) {
                    access_denied('agreements');
                }
                
                $success = $this->agreements_model->updateagreement($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('agreement')));
                    redirect(admin_url('agreements'));
                } else {
                    set_alert('danger', _l('problem_agreement_updating', _l('agreement_lowercase')));
                    redirect(admin_url('agreements/agreement/' . $id));
                }
                redirect(admin_url('agreements/agreement/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('agreement'));
        } else {
            $agreement                     = $this->agreements_model->getagreements($id);
            $data['agreement']             = $agreement;
            $title                    = _l('edit', _l('agreement')) . ' ' . $agreement->name;
        }
        $data['title']       = $title;
        $data['available_merge_fields'] = get_available_merge_fields();
        $this->load->view('admin/agreements/agreement', $data);
    }

    /* Delete staff agreement from database */
    public function deleteagreement($id)
    {
        if (!has_permission('agreements', '', 'delete', true)) {
            access_denied('agreements');
        }
        if (!$id) {
            redirect(admin_url('agreements'));
        }
        $response = $this->agreements_model->deleteagreement($id);
        if ($response == true) {
            set_alert('success', _l('Agreement Template deleted successfully.'));
        } else {
            set_alert('danger', _l('problem_deleting', _l('agreement_lowercase')));
        }
        //redirect(admin_url('agreements'));
    }
}