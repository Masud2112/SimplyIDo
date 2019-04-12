<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Teams extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        // Model is autoloaded
    }

    /* List all staff teams */
    public function index()
    {
        if (!has_permission('account_setup', '', 'view', true)) {
            access_denied('account_setup');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('teams');
        }
        $data['title'] = _l('all_teams');
        $this->load->view('admin/teams/manage', $data);
    }

    /* Add new team or edit existing one */
    public function team($id = '')
    {
        if (!has_permission('account_setup', '', 'view', true)) {
            access_denied('account_setup');
        }
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('account_setup', '', 'create', true)) {
                    access_denied('account_setup');
                }
                
                $id = $this->teams_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('team')));
                    //redirect(admin_url('teams/team/' . $id));
                    redirect(admin_url('teams/'));
                } else {
                    set_alert('danger', _l('problem_team_adding', _l('team_lowercase')));
                    redirect(admin_url('teams/team/' . $id));
                }

            } else {
                if (!has_permission('account_setup', '', 'edit', true)) {
                    access_denied('account_setup');
                }
                
                $success = $this->teams_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('team')));
                    redirect(admin_url('teams/'));
                } else {
                    set_alert('danger', _l('problem_team_updating', _l('team_lowercase')));
                    redirect(admin_url('teams/team/' . $id));
                }
                redirect(admin_url('teams/team/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('team'));
        } else {
            $data['team_permissions'] = $this->teams_model->get_team_permissions($id);
            $team                     = $this->teams_model->get($id);
            $data['team']             = $team;
            $title                    = _l('edit', _l('team')) . ' ' . $team->name;
        }
        $data['roles']       = $this->roles_model->get();
        $data['permissions'] = $this->teams_model->get_permissions();
        $data['title']       = $title;
        $this->load->view('admin/teams/team', $data);
    }

    /* Delete staff team from database */
    public function delete($id)
    {
        if (!has_permission('account_setup', '', 'delete', true)) {
            access_denied('account_setup');
        }
        if (!$id) {
            redirect(admin_url('teams'));
        }
        $response = $this->teams_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('team_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('team')));
        } else {
            set_alert('danger', _l('problem_deleting', _l('team_lowercase')));
        }
        //redirect(admin_url('teams'));
    }
}