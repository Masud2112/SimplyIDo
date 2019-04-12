<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('home_model');
        $this->load->model('projects_model');
    }

    /* This is admin home view */
    public function index($id = '')
    {
        // When user comes to dashboard in case the setup menu is opened close it
        // It's obvious that the user wont use the setup menu anymore if located in the dashboard.
        $this->session->set_userdata(array(
            'setup-menu-open' => ''
        ));

        $this->load->model('departments_model');
        $this->load->model('todo_model');
        $this->load->model('staff_model');
        $this->load->model('leads_model');
        $data['departments']               = $this->departments_model->get();

        $data['todos']                     = $this->todo_model->get_todo_items(0);
        // Only show last 5 finished todo items
        $this->todo_model->setTodosLimit(5);
        $data['todos_finished']            = $this->todo_model->get_todo_items(1);
        $data['upcoming_events_next_week'] = $this->home_model->get_upcoming_events_next_week();
        $data['upcoming_events']           = $this->home_model->get_upcoming_events();
        $data['title']                     = _l('dashboard_string');
        $this->load->model('currencies_model');
        $data['currencies']                           = $this->currencies_model->get();
        $data['base_currency']                        = $this->currencies_model->get_base_currency();
        $data['activity_log']                         = $this->misc_model->get_activity_log();
        // Tickets charts
        $tickets_awaiting_reply_by_status = $this->home_model->tickets_awaiting_reply_by_status();
        $tickets_awaiting_reply_by_department = $this->home_model->tickets_awaiting_reply_by_department();

        $data['tickets_reply_by_status']              = json_encode($tickets_awaiting_reply_by_status);
        $data['tickets_awaiting_reply_by_department'] = json_encode($tickets_awaiting_reply_by_department);

        $data['tickets_reply_by_status_no_json']              = $tickets_awaiting_reply_by_status;
        $data['tickets_awaiting_reply_by_department_no_json'] = $tickets_awaiting_reply_by_department;

        $data['projects_status_stats']                = json_encode($this->home_model->projects_status_stats());
        $data['leads_status_stats']                   = json_encode($this->home_model->leads_status_stats());
        $data['google_ids_calendars']                 = $this->misc_model->get_google_calendar_ids();
        $data['bodyclass']                            = 'home invoices_total_manual';
        $this->load->model('announcements_model');
        $data['staff_announcements'] = $this->announcements_model->get();
        $data['total_undismissed_announcements'] = $this->announcements_model->get_total_undismissed_announcements();

        $this->load->model('projects_model');
        $data['projects_activity'] = $this->projects_model->get_activity('', do_action('projects_activity_dashboard_limit',20));
        // To load js files
        $data['calendar_assets']   = true;
        $this->load->model('utilities_model');
        $this->load->model('estimates_model');
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();

        $this->load->model('proposals_model');
        $data['proposal_statuses'] = $this->proposals_model->get_statuses();

        $wps_currency = 'undefined';
        if (is_using_multiple_currencies()) {
            $wps_currency = $data['base_currency']->id;
        }
        $data['weekly_payment_stats'] = json_encode($this->home_model->get_weekly_payments_statistics($wps_currency));

        $data['widget_data']     = $this->home_model->get_dashboard_data();
        $data['package_type']    = $this->home_model->get_package_type();
        $data['banner']          = $this->home_model->get_banner_by_brand();
        $data['package_days']    = $this->home_model->get_package_days();
        $data['proposal_status'] = $this->home_model->get_proposal_status();
        $data['contact_status']  = $this->home_model->get_contact_status();
        $data['lead_status']     = $this->home_model->get_lead_status();
        $data['invoice_status']  = $this->home_model->get_invoice_status();
        $data['theme_status']    = $this->home_model->get_theme_status();
        $data['banking_status']  = $this->home_model->get_banking_status();
        $data['company_info_status']  = $this->home_model->get_company_info_status();

        $data['statuses']        = $this->leads_model->get_status();
        $data['sources']         = $this->leads_model->get_source();
        $data['eventtypes']      = $this->leads_model->get_event_type();
        $data['leadid']          = $id;

        $data['project_data']    = $this->home_model->get_all_project_data();
        $data['lead_data']       = $this->home_model->get_all_lead_data();
        $data['tasks_data']      = $this->home_model->get_all_tasks_data();
        $data['meeting_data']    = $this->home_model->get_all_meeting_data();

        $data['my_project_data']    = $this->home_model->get_my_all_project_data();
        $data['my_lead_data']       = $this->home_model->get_my_all_lead_data();
        $data['my_tasks_data']      = $this->home_model->get_my_all_tasks_data();
        $data['my_meeting_data']    = $this->home_model->get_my_all_meeting_data();
        
        $data['project_pinned_data']    = $this->home_model->get_all_project_pinned_data();
        $data['lead_pinned_data']       = $this->home_model->get_all_lead_pinned_data();
        $data['task_pinned_data']       = $this->home_model->get_all_task_pinned_data();
        $data['message_pinned_data']    = $this->home_model->get_all_message_pinned_data();

        $data['ql_total_lead_count']    = $this->home_model->get_quick_link_all_count();
        $data['pinned_contact_data']    = $this->home_model->get_all_pinned_contacts();
        $data['pinned_venues_data']     = $this->home_model->get_all_pinned_venues();
        
        $data['message_data']               = $this->home_model->get_all_message_data();
        $data['unread_message_data']        = $this->home_model->get_all_unread_message_data();
        $data['lead_unread_message_data']   = $this->home_model->get_all_lead_unread_message_data();
        
        $data['activity_log_data']      = $this->home_model->get_all_activity_log_data();
        $data['lead_activity_log_data'] = $this->home_model->get_all_lead_activity_log_data();
        $data['all_activity_log_data']  = $this->home_model->get_all_stuff_activity_log_data();
  
        $data['is_home']             = true;
        $this->load->view('admin/home', $data);
    }

    /* Chart weekly payments statistics on home page / ajax */
    public function weekly_payments_statistics($currency)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->home_model->get_weekly_payments_statistics($currency));
            die();
        }
    }

    /**
    * Added By : Vaidehi
    * Dt: 10/12/2017
    * to update brand id in session
    */
    public function updatebrand() {
        $brandid = $this->input->post('brandid');

        if($brandid) {
            /**
            * Added By : Vaidehi
            * Dt : 11/20/2017
            * to restrict user to list view for leads and tasks module on brand switch
            */
            if($this->session->userdata('leads_kanban_view') == true){
                $this->session->unset_userdata('leads_kanban_view');    
            }

            if($this->session->userdata('tasks_kanban_view') == true){
                $this->session->unset_userdata('tasks_kanban_view');    
            }

            if($this->session->userdata('projects_kanban_view') == true){
                $this->session->unset_userdata('projects_kanban_view');    
            }
            
            $this->session->set_userdata('brand_id', $brandid);
            echo "success";die();
            //redirect(admin_url());
        }
    }

    
    /**
    * Added By : Sanjay
    * Dt: 01/01/2018
    * to search keyword for global search
    */
    public function search()
    {  
        $searchword = $this->input->get('search_input');
        
        $check_filter_tags = $this->home_model->search_filter_tags();
        $all_fil_tags = $check_filter_tags[0]['value'];
        $single_filter_tag = explode(',', $all_fil_tags);
        
        /*for retieve lead search result*/
        if(in_array('leads', $single_filter_tag) && has_permission('leads','','view')) {
            $data['lead_search_result'] = $this->home_model->get_lead_search($searchword);
        }

        /*for retieve project search result*/
        if(in_array('projects', $single_filter_tag) && has_permission('projects','','view')) {
            $data['project_search_result'] = $this->home_model->get_project_search($searchword);
        }

        /*for retieve tasks search result*/
        if(in_array('tasks', $single_filter_tag) && has_permission('tasks','','view')) {
            $data['tasks_search_result'] = $this->home_model->get_tasks_search($searchword);
        }

        /*for retieve files search result*/
        if(in_array('files', $single_filter_tag) && has_permission('files','','view')) {
            $data['files_search_result'] = $this->home_model->get_files_search($searchword);
        }

        /*for retieve meetings search result*/
        if(in_array('meetings', $single_filter_tag) && has_permission('meetings','','view')) {            
            $data['meetings_search_result'] = $this->home_model->get_meetings_search($searchword);
        }

        /*for retieve addressbook search result*/
        if(in_array('addressbook', $single_filter_tag) && has_permission('addressbook','','view'))
        {
            $data['addressbook_search_result'] = $this->home_model->get_addressbook_search($searchword);
        }

        /*for retieve messages search result*/
        if(in_array('messages', $single_filter_tag) && has_permission('messages','','view')) {
            $data['messages_search_result'] = $this->home_model->get_messages_search($searchword);
        }

        /*for retieve proposals search result*/
        if(in_array('proposals', $single_filter_tag) && has_permission('proposals','','view')) { 
            $data['proposals_search_result'] = $this->home_model->get_proposals_search($searchword);
        }

         /*for retieve agreements search result*/
        if(in_array('agreements', $single_filter_tag) && has_permission('agreements','','view')) {
            $data['agreements_search_result'] = $this->home_model->get_agreements_search($searchword);
        }

        /*for retieve payment schedules search result*/
        if(in_array('paymentschedules', $single_filter_tag) && has_permission('paymentschedules','','view')) {
            $data['paymentschedules_search_result'] = $this->home_model->get_paymentschedules_search($searchword);
        }
        
        $data['searchword'] = $this->input->get('search_input');
        
        if($this->input->get('search_input') == null) {   
            set_alert('danger', _l('search_form_blank_message', _l('search_form_blank_message')));
            redirect('admin', 'refresh');
        } else {
            $this->load->view('admin/home_search',$data);    
        }        
    }
    

    /*
    ** Added By Sanjay on 02/26/2018 
    ** view dashboard config setting
    */
    public function config()
    {   
        $data['widget_data']     = $this->home_model->get_dashboard_data();
        $this->load->view('admin/configuration',$data);    
    }

    /*
    ** Added By Sanjay on 02/26/2018 
    ** Save all dashboard config setting
    */
    public function save_config()
    {   
        $data = $this->input->post();
        $id = $this->session->userdata['staff_user_id'];

        $response = $this->home_model->save_config_setting($data, $id);
        set_alert('success', _l('updated_successfully', _l('config_save')));
        redirect(admin_url('home/config'));
    }


    public function check_dashboard_setting_ajax()
    {
        if ($this->input->post()) {
            $tag_id = $this->input->post('tagid');
            $current_click = $this->input->post('currentval');
           /* var_dump($tagid);
            var_dump($current_click);die;*/
            if ($tag_id != '') {
                $this->db->where('staffid', $tag_id);         
                $this->db->where('brandid', get_user_session());
                $this->db->where('deleted', '0'); 
                $_current_tag = $this->db->get('tbldashboard_settings')->row();
                $check_in_val = $_current_tag->widget_type;
                $exp_val = explode(',', $check_in_val);
                if(in_array($current_click, $exp_val))
                {
                    $pos = array_search($current_click, $exp_val);
                    unset($exp_val[$pos]);
                    $remaining_values = $exp_val;
                    $this->db->where('staffid', $tag_id);         
                    $this->db->where('brandid', get_user_session());
                    $this->db->where('deleted', '0'); 
                    $up_data['widget_type']   = implode(",", $remaining_values);
                    $this->db->update('tbldashboard_settings', $up_data);
                }   
                else
                {
                    $parts = explode(',', $check_in_val);
                    array_push($parts, $current_click);
                    $all_added_value = implode(',', $parts);
                    $this->db->where('staffid', $tag_id);         
                    $this->db->where('brandid', get_user_session());
                    $this->db->where('deleted', '0'); 
                    $up_data['widget_type']   = $all_added_value;
                    $this->db->update('tbldashboard_settings', $up_data);
                }
            }
        }
    }


            
    public function ajax_widget_order_update(){
        $data = $_POST;
        $options = json_decode($data['options']);
        $success = $this->home_model->update_widget_order($data['options']);
        die('<--here');
    }


    /**
    * Added By: Vaidehi
    * Dt: 02/27/2018
    * for pinned venue
    */
    public function pinvenue(){
        $venue_id = $this->input->post('venue_id');
        
        $pindata = $this->home_model->pinvenue($venue_id);

        return $pindata;
        //exit;
    }
    public function dashboard_widget_setting(){
        if ($this->input->post()) {
            $data = $this->input->post();
            //$widget[$data['widget']]['items']=$data['items'];
            $widget_setting = array('items'=>$data['items'],'time_frame'=>$data['time_frame']);
            $tag_id = $data['user'];
            $current_click = $data['widget'];
            if ($tag_id != '') {
                $this->db->where('staffid', $tag_id);
                $this->db->where('brandid', get_user_session());
                $this->db->where('deleted', '0');
                $_current_tag = $this->db->get('tbldashboard_settings')->row();
                $widgets=json_decode($_current_tag->widget_settings,true);
                $widgets[$data['widget']]=$widget_setting;
                $widget_type = $check_in_val = $_current_tag->widget_type;
                $exp_val = explode(',', $check_in_val);
                if(isset($data['widget_visibility'])&& $data['widget_visibility']==1){
                    if(in_array($current_click, $exp_val))
                    {
                        $pos = array_search($current_click, $exp_val);
                        unset($exp_val[$pos]);
                        $widget_type = implode(",", $exp_val);
                    }
                }
                else
                {
                    if(!in_array($current_click, $exp_val))
                    {
                        $parts = explode(',', $check_in_val);
                        array_push($parts, $current_click);
                        $widget_type = implode(',', $parts);
                    }
                }
                $this->db->where('staffid', $tag_id);
                $this->db->where('brandid', get_user_session());
                $this->db->where('deleted', '0');
                $up_data['widget_type']   = $widget_type;
                $up_data['widget_settings']   = json_encode($widgets);
                $this->db->update('tbldashboard_settings', $up_data);
            }
            redirect(admin_url());
        }
    }

}