<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin_controller extends CRM_Controller
{
    private $_current_version;

    public function __construct()
    {
        parent::__construct();

        $this->_current_version = $this->misc_model->get_current_db_version();

        if ($this->misc_model->is_db_upgrade_required($this->_current_version)) {
            if ($this->input->post('upgrade_database')) {
                $this->misc_model->upgrade_database();
            }
            include_once(APPPATH . 'views/admin/includes/db_update_required.php');
            die;
        }

        if (CI_VERSION != '3.1.5') {
            echo '<h2>Additionally you will need to replace the <b>system</b> folder. We updated Codeigniter to 3.1.5.</h2>';
            echo '<p>From the newest downloaded files upload the <b>system</b> folder to your Perfex CRM installation directory.';
            die;
        }

        if(!extension_loaded('mbstring') && (!function_exists('mb_strtoupper') || !function_exists('mb_strtolower'))){
            die('<h1>"mbstring" PHP extension is not loaded. Enable this extension from cPanel or consult with your hosting provider to assist you enabling "mbstring" extension.</h4>');
        }

        $language = load_admin_language();
        $this->load->model('authentication_model');
        $this->authentication_model->autologin();

        if (!is_staff_logged_in()) {
            if (strpos(current_full_url(), 'authentication/admin') === false) {
                $this->session->set_userdata(array(
                    'red_url' => current_full_url()
                    ));
            }
            redirect(site_url('authentication/admin'));
        }

        // In case staff have setup logged in as client - This is important don't change it
        $this->session->unset_userdata('client_user_id');
        $this->session->unset_userdata('contact_user_id');
        $this->session->unset_userdata('client_logged_in');
        $this->session->unset_userdata('logged_in_as_client');

        // Update staff last activity
        $this->db->where('staffid',get_staff_user_id());
        $this->db->update('tblstaff',array('last_activity'=>date('Y-m-d H:i:s')));

        /**
        * Added By : Vaidehi
        * Dt: 10/13/2017
        * get all brands for account owner and team member
        */
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];

        if($is_sido_admin == 0 && $is_admin == 0) {
            $brands = $this->staff_model->get_all_brands();
        } else {
            $brands = "";
        }

        // Do not check on ajax requests
        if (!$this->input->is_ajax_request()) {
            // Check for just updates message
            add_action('before_start_render_content', 'show_just_updated_message');

            if (ENVIRONMENT == 'production' && is_admin()) {
                if ($this->config->item('encryption_key') === '') {
                    die('<h1>Encryption key not sent in application/config/config.php</h1>For more info visit <a href="http://www.perfexcrm.com/knowledgebase/encryption-key/">Encryption key explained</a> FAQ3');
                } elseif (strlen($this->config->item('encryption_key')) != 32) {
                    die('<h1>Encryption key length should be 32 charachters</h1>For more info visit <a href="https://help.perfexcrm.com/encryption-key-explained/">Encryption key explained</a>');
                }
            }

            add_action('before_start_render_content', 'show_development_mode_message');
            // Check if cron is required to be setup for some features
            add_action('before_start_render_content', 'is_cron_setup_required');
            // Check if timezone is set
            add_action('before_start_render_content', '_maybe_timezone_not_set');
            // Notice for cloudflare rocket loader
            add_action('before_start_render_content', '_maybe_using_cloudflare_rocket_loader');
            // Notice for iconv extension
            add_action('before_start_render_content', '_maybe_iconv_needs_to_be_enabled');

            $this->init_quick_actions_links();
        }

        if (is_mobile()) {
            $this->session->set_userdata(array(
                'is_mobile' => true
            ));
        } else {
            $this->session->unset_userdata('is_mobile');
        }

        $auto_loaded_vars = array(
            'current_user'              => $this->staff_model->get(get_staff_user_id()),
            'app_language'              =>$language,
            'locale'                    => get_locale_key($language),
            'unread_notifications'      => total_rows('tblnotifications',array('touserid'=>get_staff_user_id(),'isread'=>0)),
            'google_api_key'            => get_option('google_api_key'),
            'current_version'           => $this->_current_version,
            'tasks_filter_assignees'    => $this->get_tasks_distinct_assignees(),
            'task_statuses'             => $this->tasks_model->get_status(),
            'brands'                    => $brands
        );

        $GLOBALS['current_user'] = $auto_loaded_vars['current_user'];
        
        /**
        * Added By : Vaidehi
        * Dt: 10/13/2017
        * get all brands for account owner and team member
        */
        $GLOBALS['brands']       = $auto_loaded_vars['brands'];

        $auto_loaded_vars = do_action('before_set_auto_loaded_vars_admin_area', $auto_loaded_vars);
        $this->load->vars($auto_loaded_vars);
    }

    public function get_tasks_distinct_assignees()
    {
        return $this->misc_model->get_tasks_distinct_assignees();
    }

    private function init_quick_actions_links()
    {
       /* $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('invoice'),
            'permission' => 'invoices',
            'url' => 'invoices/invoice'
            ));

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('estimate'),
            'permission' => 'estimates',
            'url' => 'estimates/estimate'
            ));

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('proposal'),
            'permission' => 'proposals',
            'url' => 'proposals/proposal'
            ));


        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('client'),
            'permission' => 'customers',
            'url' => 'clients/client'
            ));
        

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('project'),
            'url' => 'projects/project',
            'permission' => 'projects'
            ));


        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('task'),
            'url' => '#',
            'custom_url' => true,
            'href_attributes' => array(
                'onclick' => 'new_task();return false;'
                ),
            'permission' => 'tasks'
            ));
        */
          $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('lead'),
            'url' => 'leads/lead',
            //'custom_url' = true,
            'permission' => 'leads',
            //'href_attributes' => array(
            //    'onclick' => 'init_lead(); return false;'
             //   )
            ));
            $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('project'),
            'url' => 'projects/project',
            'permission' => 'projects',
            ));
           $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('task'),
            'url' => 'tasks/task',
            'permission' => 'tasks',
            ));

            $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('contact'),
            'permission' => 'addressbook',
            'url' => 'addressbooks/addressbook'
            ));

            $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('meeting'),
            'permission' => 'meetings',
            'url' => 'meetings/meeting'
            ));

            /* Added by Purvi on 11-27-2017 for add quick link on header */
            $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('message'),
            'permission' => 'messages',
            'url' => 'messages/message'
            ));
          /* $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('expense'),
            'permission' => 'expenses',
            'url' => 'expenses/expense'
            ));


        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('contract'),
            'permission' => 'contracts',
            'url' => 'contracts/contract'
            ));


        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('goal'),
            'url' => 'goals/goal',
            'permission' => 'goals'
            ));

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('kb_article'),
            'permission' => 'knowledge_base',
            'url' => 'knowledge_base/article'
            ));

        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('survey'),
            'permission' => 'surveys',
            'url' => 'surveys/survey'
            ));

        $tickets = array(
            'name' => _l('ticket'),
            'url' => 'tickets/add'
            );
        if (get_option('access_tickets_to_none_staff_members') == 0 && !is_staff_member()) {
            $tickets['permission'] = 'is_staff_member';
        }
        $this->perfex_base->add_quick_actions_link($tickets);
        */
        $this->perfex_base->add_quick_actions_link(array(
            'name' => _l('staff_member'),
            'url' => 'staff/member',
            'permission' => 'staff'
            ));

        /*$this->perfex_base->add_quick_actions_link(array(
            'name' => _l('calendar_event'),
            'url' => 'utilities/calendar?new_event=true&date='._d(date('Y-m-d')),
            'permission' => ''
            ));*/
    }

    /**
    * Added By : Vaidehi
    * Dt : 11/09/2017
    * to get check whether new entry is allowed on module or not
    */
    public function get_module_creation_access($modulename)
    {
        $this->load->model('brands_model');
        $this->load->model('staff_model');
        $this->load->model('projects_model');

        $session_data       = get_session_data();
        $is_sido_admin      = $session_data['is_sido_admin'];
        $is_admin           = $session_data['is_admin'];
        $package_id         = $session_data['package_id'];
        $package_type_id    = $session_data['package_type_id'];

        if($is_sido_admin == 0 && $is_admin == 0) {
            $search = array('packageid' => $package_id, 'modulename' => $modulename);

            $brand_limit                    = $this->brands_model->get_module_restriction_by_packageid($search);
            
            $module_create_restriction      = (isset($brand_limit->restriction) ? $brand_limit->restriction :  1);

            if($modulename == 'brands') {
                $module_active_entries          = $this->brands_model->count_allbrands_by_userid();    
            } else if($modulename == 'staff') {
                $module_active_entries          = count($this->staff_model->get('', 1));
            } else if($modulename == 'projects') {
                $module_active_entries          = count($this->projects_model->get('', array('parent' => 0)));
            }
            
            $packagename        = $this->brands_model->get_package_type();
        
            $response = array('module_create_restriction' => $module_create_restriction, 'module_active_entries' => $module_active_entries, 'packagename' => $packagename->name);
            /*echo "<pre>";
            print_r($this->projects_model->get('', array('parent' => 0)));
            die();*/
            return $response;
        }
    }
}
