<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Subscription extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        /*$this->load->model('home_model');*/
        $this->load->model('subscription_model');
        $this->load->model('packages_model');
    }

    /* This is subscription view */
    public function index()
    {
        $data['permissions']        = $this->packages_model->get_permissions();
        //get all packages
        $package_array = [];
        $packages      = $this->packages_model->get();
        foreach ($packages as $package) {
            $temp = [];
            $temp['packageid']      = $package['packageid'];
            $temp['name']           = $package['name'];
            $temp['price']          = $package['price'];
            $trial_period['trial_period']   = $package['trial_period'];
           
            //get all permissions of package
            $temp['package_permission'] = $this->packages_model->get_package_permissions($package['packageid'], 'subscription');
            array_push($package_array, $temp);
        }

        $data['packages']  = $package_array;
        $data['title']     = _l('subscription_title');
        $this->load->view('admin/subscription/manage', $data);
    }

    public function subscription_payment()
    {
        $data['title']                      = _l('subscription_payment_title');
        $this->load->view('admin/subscription/subscription_payment', $data);
    }

    /* Record new subscription payment view */
    public function record_subscription_payment_ajax($id)
    {   
        $this->session->set_userdata('type', 'subscription');
        $this->load->model('payment_modes_model');
        $data['payment_modes']      = $this->payment_modes_model->get('', array(), true);
        $data['subscription']       = $packages = $this->packages_model->get($id);
        $this->load->view('admin/subscription/subscription_payment', $data);
    }

    /* This is where subscription payment record $_POST data is send */
    public function record_payment()
    {
        // if (!has_permission('payments', '', 'create')) {
        //     access_denied('Record Payment');
        // }
        if ($this->input->post()) {
            $id = $this->packages_model->process_payment($this->input->post(), 'subscription');
            if ($id) {
                set_alert('success', _l('subscription_payment_recorded'));
                redirect(admin_url('subscription/subscription_option'));
            } else {
                set_alert('danger', _l('subscription_payment_record_failed'));
            }

            //redirect(admin_url('subscription'));
            $this->subscription_option();
        }
    }

    public function subscription_option()
    {
        $data['brand_restriction'] = true;

        $new_pkg_id = $this->subscription_model->get_new_package();
        $data['new_pkg_data'] = $this->packages_model->get_package_permissions($new_pkg_id->packageid);

        $new_package_detail = $this->packages_model->get($this->session->userdata['package_id']);
       
        //get new brand restriction
        $new_brand_key = array_search('Brands', array_column($data['new_pkg_data'], 'name'));
        $data['new_package_brand_restriction'] = $data['new_pkg_data'][$new_brand_key]['restriction'];

        if($this->session->userdata('old_package_id') > 0) {
            $data['old_pkg_data'] = $this->packages_model->get_package_permissions($this->session->userdata('old_package_id'));

            //get old brand restriction
            $brand_key = array_search('Brands', array_column($data['old_pkg_data'], 'name'));
            $data['old_package_brand_restriction'] = $data['old_pkg_data'][$brand_key]['restriction'];
        } else {
            $data['old_package_brand_restriction'] = $data['new_package_brand_restriction'];
        }

        if($new_package_detail->packagetypeid == 3) {
            $data['brand_restriction'] = false;
        } else if($data['old_package_brand_restriction'] <= $data['new_package_brand_restriction']) {
            $data['brand_restriction'] = false;
        } else { 
            $data['brand_restriction'] = true;
        }

        $data['brands']       = $brands = $this->staff_model->get_all_brands();
        $data['title']          = _l('subscription_option_title');
        
        $this->load->view('admin/subscription/subscription_option', $data);
    }

    public function get_team_member_list()
    {   
        $group_of_brands = $this->input->post('brand_list');
        
        $brand_set       = $brands = $this->staff_model->get_all_brands();
        $all_brands = [];

        //get all brands for logged in account
        foreach ($brand_set as $brand) {
            array_push($all_brands, $brand['brandid']);
        }
        if(count($group_of_brands) > 0) {
            //remove brands which are mareked for deletion
            foreach ($group_of_brands as $brand) {
                foreach (array_keys($all_brands, $brand) as $key) {
                    unset($all_brands[$key]);
                }
            }
        }

        $new_pkg_id = $this->subscription_model->get_new_package();
        $data['new_pkg_data'] = $this->packages_model->get_package_permissions($new_pkg_id->packageid);

        $new_package_detail = $this->packages_model->get($this->session->userdata['package_id']);

        $new_team_member_key = array_search('Team Members', array_column($data['new_pkg_data'], 'name'));
        $data['new_package_team_member_restriction'] = $data['new_pkg_data'][$new_team_member_key]['restriction'];

        if($this->session->userdata('old_package_id') > 0) {
            $data['old_pkg_data'] = $this->packages_model->get_package_permissions($this->session->userdata('old_package_id'));
            $team_member_key = array_search('Team Members', array_column($data['old_pkg_data'], 'name'));
            $data['old_package_team_member_restriction'] = $data['old_pkg_data'][$team_member_key]['restriction'];
        } else {
            $data['old_package_team_member_restriction'] = $data['new_package_team_member_restriction'];
        }

        if($new_package_detail->packagetypeid == 3) {
            $data['team_member_restriction'] = false;
        } else if($data['old_package_team_member_restriction'] <= $data['new_package_team_member_restriction']) {
            $data['team_member_restriction'] = false;
        } else { 
            $data['team_member_restriction'] = true;
        }

        $brand_array = [];
        foreach ($all_brands as $brands) {
            $group_memeber_list = $this->subscription_model->get_members($brands);
            if(count($group_memeber_list) > 0) {
                $temp = [];
                $temp['group_memeber_list'] = $group_memeber_list;
                $temp['brandname'] = $this->subscription_model->get_brand_by_id($brands);
                array_push($brand_array, $temp);
            }
        }
        $data['staff_members'] = $brand_array;
        $data['title']          = _l('subscription_team_member_title');

        $this->load->view('admin/subscription/subscription_team_member', $data);
        
    }

    public function get_project_list()
    {
        $group_of_brands = $this->input->post('brand_id');
        $group_of_member = $this->input->post('member_list');

        $new_package_detail = $this->packages_model->get($this->session->userdata['package_id']);

        $new_pkg_id = $this->subscription_model->get_new_package();
        $data['new_pkg_data'] = $this->packages_model->get_package_permissions($new_pkg_id->packageid);
        //get new project restriction
        $new_projects_key = array_search('Projects', array_column($data['new_pkg_data'], 'name'));
        $data['new_package_project_restriction'] = $data['new_pkg_data'][$new_projects_key]['restriction'];

        if($this->session->userdata('old_package_id') > 0) {
            $data['old_pkg_data'] = $this->packages_model->get_package_permissions($this->session->userdata('old_package_id'));
            //get old brand restriction
            $projects_key = array_search('Projects', array_column($data['old_pkg_data'], 'name'));
            $data['old_package_project_restriction'] = $data['old_pkg_data'][$projects_key]['restriction'];
        } else {
            $data['old_package_project_restriction'] = $data['new_package_project_restriction'];
        }

        if($new_package_detail->packagetypeid == 3) {
            $data['project_restriction'] = false;
        } else if($data['old_package_project_restriction'] <= $data['new_package_project_restriction']) {
            $data['project_restriction'] = false;
        } else { 
            $data['project_restriction'] = true;
        }
        
        $project_array = [];

        if(count($group_of_brands) > 0){
            foreach ($group_of_brands as $brands) {
                $temp = [];
                $temp['project_list'] = $this->subscription_model->get_project_by_id($brands);
                $temp['brandname'] = $this->subscription_model->get_brand_by_id($brands);
                array_push($project_array, $temp);
            }
        }

        if(count($group_of_member) > 0){
            foreach ($group_of_member as $member) {
                $del_member_status = $this->subscription_model->delete_member_by_id($member);
            }
        } else {
            $del_member_status = 1;
        }

        //if ($del_member_status) {
            $data['project_list'] = $project_array;
            $data['title']          = _l('subscription_project_list_title');
            //set_alert('success', _l('brand_deleted_successfully'));
            $this->load->view('admin/subscription/subscription_project_list', $data);
        //}
    }

    public function all_option_delete()
    {
        $group_of_brands = $this->input->post('brand_id');
        $group_of_project = $this->input->post('project_list');
      
        $brand_set       = $brands = $this->staff_model->get_all_brands();
        $all_brands = [];
        //get all brands for logged in account
        foreach ($brand_set as $brand) {
            array_push($all_brands, $brand['brandid']);
        }
        //remove brands which are mareked for deletion
        foreach ($group_of_brands as $brand) {
            foreach (array_keys($all_brands, $brand) as $key) {
                unset($all_brands[$key]);
            }
        }
        
        //var_dump($all_brands);die;

        //to remove brands,leads,meetings,contacts,brandvenues for selected brands
        if($this->input->post()) {
            if(count($all_brands) > 0) {
                //delete everything for each selected brand 
                foreach ($all_brands as $brands) {
                    $del_brand_status       = $this->subscription_model->delete_brand_by_id($brands);
                    $del_lead_response      = $this->leads_model->delete($brands,'subscription');
                    $del_meeting_response   = $this->meetings_model->delete($brands,'subscription');
                    $del_adclient_response  = $this->subscription_model->delete_addressbook_by_id($brands);
                    $del_brvenue_response   = $this->subscription_model->delete_brand_venue_by_id($brands);
                    $del_lead_status_response   = $this->subscription_model->delete_lead_status_by_id($brands);
                    $del_lead_source_response   = $this->subscription_model->delete_lead_source_by_id($brands);
                    $del_lead_contact_response   = $this->subscription_model->delete_lead_contact_by_id($brands);
                    $del_project_status_response   = $this->subscription_model->delete_project_status_by_id($brands);
                    $del_project_contact_response   = $this->subscription_model->delete_project_contact_by_id($brands);
                    $del_invites_response   = $this->subscription_model->delete_invites_by_id($brands);
                    $del_tasks_response   = $this->subscription_model->delete_tasks_by_id($brands);
                    $del_files_response   = $this->subscription_model->delete_files_by_id($brands);
                    $del_message_response   = $this->subscription_model->delete_messages_by_id($brands);
                    $del_team_response   = $this->subscription_model->delete_team_by_id($brands);
                    $del_roles_response   = $this->subscription_model->delete_roles_by_id($brands);
                    $del_proposal_response   = $this->subscription_model->delete_proposal_by_id($brands);
                    $del_agreements_response   = $this->subscription_model->delete_agreements_by_id($brands);
                    $del_email_response   = $this->subscription_model->delete_email_by_id($brands);
                    $del_product_service_response   = $this->subscription_model->delete_product_service_by_id($brands);
                    $del_inc_category_response   = $this->subscription_model->delete_income_category_by_id($brands);
                    $del_exp_category_response   = $this->subscription_model->delete_expense_category_by_id($brands);
                    $del_tags_response   = $this->subscription_model->delete_tags_by_id($brands);
                    $del_taxes_response   = $this->subscription_model->delete_taxes_by_id($brands);
                    $del_task_status_response   = $this->subscription_model->delete_task_status_by_id($brands);
                }
            }

            foreach ($group_of_project as $project) {
                $del_member_status = $this->subscription_model->delete_project_by_id($project);
            }

            if($del_member_status) {
                set_alert('success', _l('options_deleted_successfully'));
            }
        }
        redirect(admin_url('subscription'));
    }

    public function manage_subscription()
    {
        $this->session->set_userdata('type', 'subscription');
        $this->load->model('payment_modes_model');
        $data['payment_modes']      = $this->payment_modes_model->get('', array(), true);
        $data['subscription']       = $packages = $this->subscription_model->get_all_pkg();
        $data['title']              = _l('manage_subscription_title');
        $this->load->view('admin/subscription/manage_subscription', $data);
    }

    /**
    * Added By: Vaidehi
    * Dt: 03/26/2018
    * for cancelling subscription
    */
    public function cancel_subscription($packageid) {
        //$packageid = $this->input->post('packageid');

        $success = $this->subscription_model->cancel_subscription($packageid);

        $message = '';
        if ($success) {
            //$message = _l('cancel_successfully', _l('subscription'));
            set_alert('success', _l('cancel_successfully', _l('subscription')));
        } else {
            set_alert('warning', _l('problem_subscription_cancel', _l('subscription_lowercase')));
            // $success = false;
            // $message = _l('problem_subscription_cancel', _l('subscription_lowercase'));
        }
        
        // echo json_encode(array(
        //     'success' => $success,
        //     'message' => $message
        // )); 
        // die();
    }
}