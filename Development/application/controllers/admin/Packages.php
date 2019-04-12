<?php
/**
* Added By: Vaidehi
* Dt: 10/02/2017
* Package Module
*/
defined('BASEPATH') or exit('No direct script access allowed');
class Packages extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        // Model is autoloaded
    }

    /* List all packages */
    public function index()
    {
        if (!has_permission('packages', '', 'view', true)) {
            access_denied('package');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('packages');
        }

        $data['title'] = _l('all_packages');
       
        $this->load->view('admin/packages/manage', $data);
    }

    /* Add new package or edit existing one */
    public function package($id = '')
    {
        if (!has_permission('packages', '', 'view', true)) {
            access_denied('package');
        }
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('packages', '', 'create', true)) {
                    access_denied('package');
                }
                $id = $this->packages_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('package')));
                    //redirect(admin_url('packages/package/' . $id));
                    redirect(admin_url('packages'));
                } else {
                    set_alert('danger', _l('problem_package_adding', _l('package_lowercase')));
                    redirect(admin_url('packages/package/' . $id));
                }
            } else {
                if (!has_permission('packages', '', 'edit', true)) {
                    access_denied('package');
                }
                $success = $this->packages_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('package')));
                } else {
                    set_alert('danger', _l('problem_package_updating', _l('package_lowercase')));
                    redirect(admin_url('packages/package/' . $id));
                }
                
                redirect(admin_url('packages'));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('package'));
            $packagetype                 = $this->packages_model->get_all_packagetype();
            $data['packagetypes']        = $packagetype;
        } else {
            $data['package_permissions'] = $this->packages_model->get_package_permissions($id);
            $package                     = $this->packages_model->get($id);
            $packagetype                 = $this->packages_model->get_all_packagetype();
            $data['package']             = $package;
            $data['packagetypes']        = $packagetype;
            $title                       = _l('edit', _l('package')) . ' ' . $package->name;
        }
        $data['permissions'] = $this->packages_model->get_permissions();
        $data['title']       = $title;
        
        $this->load->view('admin/packages/package', $data);
    }

    /* Delete package from database */
    public function delete($id)
    {
        if (!has_permission('packages', '', 'delete', true)) {
            access_denied('package');
        }
        if (!$id) {
            redirect(admin_url('packages'));
        }

        $response = $this->packages_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('package_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('package')));
        } else {
            set_alert('danger', _l('problem_deleting', _l('package_lowercase')));
        }

        /**
        * Modified By : Vaidehi
        * Dt : 11/20/2017
        * to redirect from js
        */
        //redirect(admin_url('packages'));
    }
}
