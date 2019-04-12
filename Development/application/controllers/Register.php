<?php
/**
 * Added By: Vaidehi
 * Dt: 10/03/2017
 * for handling client/account registration
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Register extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Register_model');
        $this->load->library('form_validation');
    }    //load sign up form

    public function signup()
    {
        if (!is_null($this->input->get('package'))) {
            $data['packagetype'] = $this->input->get('package');
            $data['title'] = 'Register';
            $data['brandtypes'] = $this->Register_model->get_brandtypes();
            $data['packages'] = $this->Register_model->get_packages();
            $this->load->view('register', $data);
        } else {
            do_action('after_staff_login');
            redirect(admin_url());
        }
    }    //load social sign up step 2

    public function social()
    {
        $data['firstname'] = $this->input->post('firstname');
        $data['socialemail'] = $this->input->post('socialemail');
        $data['facebook'] = $this->input->post('facebook');
        $data['twitter'] = $this->input->post('twitter');
        $data['google'] = $this->input->post('google');
        $data['brandtypes'] = $this->Register_model->get_brandtypes();
        $data['packages'] = $this->Register_model->get_packages();
        $account_data = $this->Register_model->check_account_exists($data['socialemail']);        //if account is inactive, set alert message
        if (is_array($account_data) && isset($account_data['active']) && $account_data['active'] == 0) {
            set_alert('danger', _l('admin_auth_inactive_account'));
            redirect(site_url('authentication/admin'));
        } elseif (!isset($account_data) || empty($account_data) || count($account_data) <= 0) {
            //if account does not exists, add account
            $data['title'] = 'Register';
            $this->load->view('socialregister', $data);
        } else {
            //if account exists, redirect to dashboard
            $userexists = $this->Register_model->do_staff_login($data['socialemail'], '', false, true);
            if ($userexists) {
                $this->_url_redirect_after_login();
                do_action('after_staff_login');
                redirect(admin_url());
            }
        }
    }    //save client

    public function saveclient()
    {
        if ($this->input->post('page') == 'signup') {
            $this->form_validation->set_rules('firstname', _l('register_name_required'), 'trim|required');
            $this->form_validation->set_rules('lastname', _l('register_last_name_required'), 'trim|required');
            $this->form_validation->set_rules('useremail', _l('register_email_address'), 'trim|required|valid_email');
            $this->form_validation->set_rules('passwd', _l('register_password_required'), 'trim|required|min_length[6]');
            $this->form_validation->set_rules('cpasswd', _l('register_cpassword_required'), 'trim|required|min_length[6]|matches[passwd]');
            $this->form_validation->set_rules('terms[]', _l('register_terms_required'), 'required');
        } else {
            $this->form_validation->set_rules('firstname', _l('register_name_required'), 'trim|required');
            $this->form_validation->set_rules('useremail', _l('register_email_address'), 'trim|required|valid_email');
        }
        $this->form_validation->set_rules('brandname', _l('brand_name_required'), 'trim|required');
        $this->form_validation->set_rules('brandtype[]', _l('brand_type_required'), 'required');
        $this->form_validation->set_rules('packagetype', _l('package_required'), 'required');
        $this->form_validation->set_rules('address1', _l('address_required'), 'trim|required');
        $this->form_validation->set_rules('city', _l('city_required'), 'trim|required');
        $this->form_validation->set_rules('state', _l('state_required'), 'trim|required');
        $this->form_validation->set_rules('zipcode', _l('zipcode_required'), 'trim|required');
        $this->form_validation->set_rules('country', _l('country_required'), 'trim|required');
        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                $account_data = $this->Register_model->check_account_exists($this->input->post('useremail'));                //if account is inactive, set alert message
                if (is_array($account_data) && isset($account_data['active']) && $account_data['active'] == 0) {
                    set_alert('danger', _l('admin_auth_inactive_account'));
                    redirect(site_url('authentication/admin'));
                } else {
                    $name = explode(" ", $this->input->post('firstname'));
                    //if account does not exists, add account
                    $data['email'] = $this->input->post('useremail');
                    if ($this->input->post('page') == 'signup') {
                        $data['firstname'] = $this->input->post('firstname');
                        $data['lastname'] = $this->input->post('lastname');
                        $data['password'] = $this->input->post('passwd');
                        $data['facebook'] = null;
                        $data['twitter'] = null;
                        $data['google'] = null;
                    } else {
                        $data['firstname'] = $name[0];
                        $data['lastname'] = $name[1];
                        $data['facebook'] = $this->input->post('facebook');
                        $data['twitter'] = $this->input->post('twitter');
                        $data['google'] = $this->input->post('google');
                    }
                    $data['brandname']      = $this->input->post('brandname');
                    $data['brandtype']      = $this->input->post('brandtype');
                    $data['packagetype']    = $this->input->post('packagetype');
                    $data['address']        = $this->input->post('address1') . " " . $this->input->post('address2');
                    $data['city']           = $this->input->post('city');
                    $data['state']          = $this->input->post('state');
                    $data['zipcode']        = $this->input->post('zipcode');
                    $data['paymentmode']    = $this->input->post('payment_method')[0];
                    $client = $this->Register_model->saveclient($data);
                    if ($client <= 0) {
                        set_alert('danger', _l('valid_form'));
                        redirect(site_url('authentication/admin'));
                    }
                }
            }
        } else {
            set_alert('danger', _l('valid_form'));
            redirect(site_url('authentication/admin'));
        }
    }

    //check for unique brand name

    public function brandexists()
    {
        $brandname = $this->input->post('brandname');
        $brands = $this->Register_model->check_brand_exists($brandname);
        if (empty($brands) || count($brands) == 0) {
            echo 1;
            die();
        }
        echo 0;
        die();
    }    //check for unique email

    public function emailexists()
    {
        $useremail = $this->input->post('useremail');
        $account_data = $this->Register_model->check_account_exists($useremail);
        if (isset($account_data->active)) {
            echo "0";
            die();
        }
        echo "1";
        die();
    }    //check for number of brands available for package selected

    public function activebrands()
    {
        $useremail = $this->input->post('useremail');
        $packagetype = $this->input->post('packagetype');
        $brandname = $this->input->post('brandname');
        $search = array('packageid' => $packagetype, 'modulename' => 'brands');
        $brand_limit = $this->Register_model->get_module_restriction_by_packageid($search);
        $module_create_restriction = (isset($brand_limit->restriction) ? $brand_limit->restriction : 1);
        $module_active_entries = $this->Register_model->count_allbrands_by_userid($useremail);
        $packagename = $this->Register_model->get_package_type($packagetype);
        if ((isset($packagename->name) && $packagename->name != "Paid") && (isset($module_active_entries) && ($module_active_entries > $module_create_restriction))) {
            echo "failure";
            die();
        }
        echo "success";
        die();
    }

    /**
     * Check if user accessed url while not logged in to redirect after login
     * @return null
     */
    private function _url_redirect_after_login()
    {
        // This is only working for staff members
        if ($this->session->has_userdata('red_url')) {
            $red_url = $this->session->userdata('red_url');
            $this->session->unset_userdata('red_url');
            redirect($red_url);
        }
    }

    public function savetrialaccount()
    {
        if ($this->input->post('page') == 'signup') {
            $this->form_validation->set_rules('firstname', _l('register_name_required'), 'trim|required');
            $this->form_validation->set_rules('lastname', _l('register_last_name_required'), 'trim|required');
            $this->form_validation->set_rules('useremail', _l('register_email_address'), 'trim|required|valid_email');
            $this->form_validation->set_rules('passwd', _l('register_password_required'), 'trim|required|min_length[6]');
            $this->form_validation->set_rules('cpasswd', _l('register_cpassword_required'), 'trim|required|min_length[6]|matches[passwd]');
            $this->form_validation->set_rules('terms[]', _l('register_terms_required'), 'required');
        } else {
            $this->form_validation->set_rules('firstname', _l('register_name_required'), 'trim|required');
            $this->form_validation->set_rules('useremail', _l('register_email_address'), 'trim|required|valid_email');
        }
        $this->form_validation->set_rules('brandname', _l('brand_name_required'), 'trim|required');
        $this->form_validation->set_rules('brandtype[]', _l('brand_type_required'), 'required');
        $this->form_validation->set_rules('packagetype', _l('package_required'), 'required');
        /*$this->form_validation->set_rules('address1', _l('address_required'), 'trim|required');
        $this->form_validation->set_rules('city', _l('city_required'), 'trim|required');
        $this->form_validation->set_rules('state', _l('state_required'), 'trim|required');
        $this->form_validation->set_rules('zipcode', _l('zipcode_required'), 'trim|required');
        $this->form_validation->set_rules('country', _l('country_required'), 'trim|required');*/
        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                $brandtypes = $this->input->post('brandtype');
                if(in_array('other',$brandtypes) && $this->input->post('otherbrandval')!=""){
                        $newbrandtype = add_brand_type($this->input->post('otherbrandval'));
                        array_push($brandtypes,$newbrandtype);
                }
                $brandtypes = array_diff($brandtypes,array('other'));

                $account_data = $this->Register_model->check_account_exists($this->input->post('useremail'));                //if account is inactive, set alert message
                if (is_array($account_data) && isset($account_data['active']) && $account_data['active'] == 0) {
                    set_alert('danger', _l('admin_auth_inactive_account'));
                    redirect(site_url('authentication/admin'));
                } else {
                    $name = explode(" ", $this->input->post('firstname'));
                    //if account does not exists, add account
                    $data['email'] = $this->input->post('useremail');
                    if ($this->input->post('page') == 'signup') {
                        $data['firstname'] = $this->input->post('firstname');
                        $data['lastname'] = $this->input->post('lastname');
                        $data['password'] = $this->input->post('passwd');
                        $data['facebook'] = null;
                        $data['twitter'] = null;
                        $data['google'] = null;
                    } else {
                        $data['firstname'] = $name[0];
                        $data['lastname'] = $name[1];
                        $data['facebook'] = $this->input->post('facebook');
                        $data['twitter'] = $this->input->post('twitter');
                        $data['google'] = $this->input->post('google');
                    }
                    $data['brandname']      = $this->input->post('brandname');
                    $data['brandtype']      = serialize($brandtypes);
                    /*$data['brandtype']      = 1;*/
                    $data['packagetype']    = $this->input->post('packagetype');
                    $data['address']        = $this->input->post('address1') . " " . $this->input->post('address2');
                    $data['city']           = $this->input->post('city');
                    $data['state']          = $this->input->post('state');
                    $data['zipcode']        = $this->input->post('zipcode');
                    $data['paymentmode']    = $this->input->post('payment_method')[0];
                    $client = $this->Register_model->saveclient($data,"invite");

                    if ($client <= 0) {
                        set_alert('danger', _l('valid_form'));
                        redirect(site_url('authentication/admin'));
                    }else{
                        $staffid = get_staffid_by_client($client);
                        $staff = get_staff_details_by_id($staffid);
                        $this->Authentication_model->login($staff->email, $staff->random_pass, '', true);
                        echo $staffid;
                        die();
                    }
                }
            }
        } else {
            set_alert('danger', _l('valid_form'));
            redirect(site_url('authentication/admin'));
        }
    }

}