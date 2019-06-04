<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home_model extends CRM_Model
{
    private $is_admin;

    public function __construct()
    {
        parent::__construct();
        $this->is_admin = is_admin();
    }

    /**
     * @return array
     * Used in home dashboard page
     * Return all upcoming events this week
     */
    public function get_upcoming_events()
    {
        $this->db->where('(start BETWEEN "' . date('Y-m-d', strtotime('monday this week')) . '" AND "' . date('Y-m-d', strtotime('sunday this week')) . '")');
        $this->db->where('(userid = ' . get_staff_user_id() . ' OR public = 1)');
        $this->db->order_by('start', 'desc');
        $this->db->limit(6);

        return $this->db->get('tblevents')->result_array();
    }

    /**
     * @param integer (optional) Limit upcoming events
     * @return integer
     * Used in home dashboard page
     * Return total upcoming events next week
     */
    public function get_upcoming_events_next_week()
    {
        $monday_this_week = date('Y-m-d', strtotime('monday next week'));
        $sunday_this_week = date('Y-m-d', strtotime('sunday next week'));
        $this->db->where('(start BETWEEN "' . $monday_this_week . '" AND "' . $sunday_this_week . '")');
        $this->db->where('(userid = ' . get_staff_user_id() . ' OR public = 1)');

        return $this->db->count_all_results('tblevents');
    }

    /**
     * @param mixed
     * @return array
     * Used in home dashboard page, currency passed from javascript (undefined or integer)
     * Displays weekly payment statistics (chart)
     */
    public function get_weekly_payments_statistics($currency)
    {
        $all_payments = array();
        $has_permission_payments_view = has_permission('account_setup', '', 'view');
        $this->db->select('amount,tblinvoicepaymentrecords.date');
        $this->db->from('tblinvoicepaymentrecords');
        $this->db->join('tblinvoices', 'tblinvoices.id = tblinvoicepaymentrecords.invoiceid');
        $this->db->where('CAST(tblinvoicepaymentrecords.date as DATE) >= "' . date('Y-m-d', strtotime('monday this week')) . '" AND CAST(tblinvoicepaymentrecords.date as DATE) <= "' . date('Y-m-d', strtotime('sunday this week')) . '"');
        $this->db->where('tblinvoices.status !=', 5);
        if ($currency != 'undefined') {
            $this->db->where('currency', $currency);
        }

        if (!$has_permission_payments_view) {
            $this->db->where('invoiceid IN (SELECT id FROM tblinvoices WHERE addedfrom=' . get_staff_user_id() . ')');
        }

        // Current week
        $all_payments[] = $this->db->get()->result_array();
        $this->db->select('amount,tblinvoicepaymentrecords.date');
        $this->db->from('tblinvoicepaymentrecords');
        $this->db->join('tblinvoices', 'tblinvoices.id = tblinvoicepaymentrecords.invoiceid');
        $this->db->where('CAST(tblinvoicepaymentrecords.date as DATE) >= "' . date('Y-m-d', strtotime('monday last week', strtotime('last sunday'))) . '" AND CAST(tblinvoicepaymentrecords.date as DATE) <= "' . date('Y-m-d', strtotime('sunday last week', strtotime('last sunday'))) . '"');

        $this->db->where('tblinvoices.status !=', 5);
        if ($currency != 'undefined') {
            $this->db->where('currency', $currency);
        }
        // Last Week
        $all_payments[] = $this->db->get()->result_array();

        $chart = array(
            'labels' => get_weekdays(),
            'datasets' => array(
                array(
                    'label' => _l('this_week_payments'),
                    'backgroundColor' => 'rgba(37,155,35,0.2)',
                    'borderColor' => "#84c529",
                    'borderWidth' => 1,
                    'tension' => false,
                    'data' => array(
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0
                    )
                ),
                array(
                    'label' => _l('last_week_payments'),
                    'backgroundColor' => 'rgba(197, 61, 169, 0.5)',
                    'borderColor' => "#c53da9",
                    'borderWidth' => 1,
                    'tension' => false,
                    'data' => array(
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0
                    )
                )
            )
        );


        for ($i = 0; $i < count($all_payments); $i++) {
            foreach ($all_payments[$i] as $payment) {
                $payment_day = date('l', strtotime($payment['date']));
                $x = 0;
                foreach (get_weekdays_original() as $day) {
                    if ($payment_day == $day) {
                        $chart['datasets'][$i]['data'][$x] += $payment['amount'];
                    }
                    $x++;
                }
            }
        }

        return $chart;
    }

    public function projects_status_stats()
    {
        $this->load->model('projects_model');
        $statuses = $this->projects_model->get_project_statuses();
        $colors = get_system_favourite_colors();

        $chart = array(
            'labels' => array(),
            'datasets' => array()
        );

        $_data = array();
        $_data['data'] = array();
        $_data['backgroundColor'] = array();
        $_data['hoverBackgroundColor'] = array();

        $i = 0;
        $has_permission = has_permission('projects', '', 'view');
        foreach ($statuses as $status) {
            $this->db->where('status', $status['id']);
            if (!$has_permission) {
                $this->db->where('id IN (SELECT project_id FROM tblprojectmembers WHERE staff_id=' . get_staff_user_id() . ')');
            }

            array_push($chart['labels'], $status['name']);
            array_push($_data['backgroundColor'], $status['color']);
            array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['color'], -20));
            array_push($_data['data'], $this->db->count_all_results('tblprojects'));

            $i++;
        }
        $chart['datasets'][] = $_data;
        $chart['datasets'][0]['label'] = _l('home_stats_by_project_status');

        return $chart;
    }

    public function leads_status_stats()
    {
        $this->load->model('leads_model');
        $statuses = $this->leads_model->get_status();
        $colors = get_system_favourite_colors();
        $chart = array(
            'labels' => array(),
            'datasets' => array()
        );

        $_data = array();
        $_data['data'] = array();
        $_data['backgroundColor'] = array();
        $_data['hoverBackgroundColor'] = array();

        foreach ($statuses as $status) {
            $this->db->where('status', $status['id']);
            if (!$this->is_admin) {
                $this->db->where('(addedfrom = ' . get_staff_user_id() . ' OR is_public = 1 OR assigned = ' . get_staff_user_id() . ')');
            }
            if ($status['color'] == '') {
                $status['color'] = '#737373';
            }
            array_push($chart['labels'], $status['name']);
            array_push($_data['backgroundColor'], $status['color']);
            array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['color'], -20));
            array_push($_data['data'], $this->db->count_all_results('tblleads'));
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }

    /**
     * Display total tickets awaiting reply by department (chart)
     * @return array
     */
    public function tickets_awaiting_reply_by_department()
    {
        $this->load->model('departments_model');
        $departments = $this->departments_model->get();
        $colors = get_system_favourite_colors();
        $chart = array(
            'labels' => array(),
            'datasets' => array()
        );

        $_data = array();
        $_data['data'] = array();
        $_data['backgroundColor'] = array();
        $_data['hoverBackgroundColor'] = array();

        $i = 0;
        foreach ($departments as $department) {
            if (!$this->is_admin) {
                if (get_option('staff_access_only_assigned_departments') == 1) {
                    $staff_deparments_ids = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                    $departments_ids = array();
                    if (count($staff_deparments_ids) == 0) {
                        $departments = $this->departments_model->get();
                        foreach ($departments as $department) {
                            array_push($departments_ids, $department['departmentid']);
                        }
                    } else {
                        $departments_ids = $staff_deparments_ids;
                    }
                    if (count($departments_ids) > 0) {
                        $this->db->where('department IN (SELECT departmentid FROM tblstaffdepartments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="' . get_staff_user_id() . '")');
                    }
                }
            }
            $this->db->where_in('status', array(
                1,
                2,
                4
            ));

            $this->db->where('department', $department['departmentid']);
            $total = $this->db->count_all_results('tbltickets');

            if ($total > 0) {
                $color = '#333';
                if (isset($colors[$i])) {
                    $color = $colors[$i];
                }
                array_push($chart['labels'], $department['name']);
                array_push($_data['backgroundColor'], $color);
                array_push($_data['hoverBackgroundColor'], adjust_color_brightness($color, -20));
                array_push($_data['data'], $total);
            }
            $i++;
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }

    /**
     * Display total tickets awaiting reply by status (chart)
     * @return array
     */
    public function tickets_awaiting_reply_by_status()
    {
        $this->load->model('tickets_model');
        $statuses = $this->tickets_model->get_ticket_status();
        $_statuses_with_reply = array(
            1,
            2,
            4
        );

        $chart = array(
            'labels' => array(),
            'datasets' => array()
        );

        $_data = array();
        $_data['data'] = array();
        $_data['backgroundColor'] = array();
        $_data['hoverBackgroundColor'] = array();

        foreach ($statuses as $status) {
            if (in_array($status['ticketstatusid'], $_statuses_with_reply)) {
                if (!$this->is_admin) {
                    if (get_option('staff_access_only_assigned_departments') == 1) {
                        $staff_deparments_ids = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                        $departments_ids = array();
                        if (count($staff_deparments_ids) == 0) {
                            $departments = $this->departments_model->get();
                            foreach ($departments as $department) {
                                array_push($departments_ids, $department['departmentid']);
                            }
                        } else {
                            $departments_ids = $staff_deparments_ids;
                        }
                        if (count($departments_ids) > 0) {
                            $this->db->where('department IN (SELECT departmentid FROM tblstaffdepartments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="' . get_staff_user_id() . '")');
                        }
                    }
                }

                $this->db->where('status', $status['ticketstatusid']);
                $total = $this->db->count_all_results('tbltickets');
                if ($total > 0) {
                    array_push($chart['labels'], ticket_status_translate($status['ticketstatusid']));
                    array_push($_data['backgroundColor'], $status['statuscolor']);
                    array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['statuscolor'], -20));
                    array_push($_data['data'], $total);
                }
            }
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }


    /*
    * Added by: Sanjay
    * Date: 01-01-2017
    * for global search result
    */
    /*Update filter tags for brand settings*/
    public function search_filter_tags()
    {
        $brandid = get_user_session();
        $name = "filter_tags";
        $brand_tags = get_brand_option('value');
        $this->db->select('value');
        $this->db->where('brandid', $brandid);
        $this->db->where('name', $name);
        $this->db->from('tblbrandsettings');
        $query = $this->db->get()->result_array();
        return $query;
    }

    /*for retrieve lead search result*/
    function get_lead_search($search)
    {

        $brandid = get_user_session();
        $this->db->select('*');
        $this->db->like('name', $search);
        $this->db->where('brandid', $brandid);
        $this->db->where('deleted', '0');
        $this->db->from('tblleads');
        $query = $this->db->get()->result();
        return $query;

    }

    /*for retrieve project search result*/
    public function get_project_search($search)
    {
        $brandid = get_user_session();
        $this->db->select('*');
        $this->db->like('name', $search);
        $this->db->where('brandid', $brandid);
        $this->db->where('deleted', '0');
        $this->db->from('tblprojects');
        $query = $this->db->get()->result();

        //echo $this->db->last_query();die;
        return $query;
    }

    /*for retrieve tasks search result*/
    public function get_tasks_search($search)
    {
        $brandid = get_user_session();
        $this->db->select('*');
        $this->db->like('name', $search);
        $this->db->where('brandid', $brandid);
        $this->db->where('deleted', '0');
        $this->db->from('tblstafftasks');
        $query = $this->db->get()->result();
        return $query;
    }

    /*for retrieve files search result*/
    public function get_files_search($search)
    {
        $brandid = get_user_session();
        $this->db->select('*');
        $this->db->like('file_name', $search);
        $this->db->where('brandid', $brandid);
        $this->db->from('tblfiles');
        $query = $this->db->get()->result();
        //echo $this->db->last_query();die;
        return $query;
    }

    /*for retrieve meetings search result*/
    public function get_meetings_search($search)
    {
        $brandid = get_user_session();
        $this->db->select('*');
        $this->db->like('name', $search);
        $this->db->where('brandid', $brandid);
        $this->db->where('deleted', '0');
        $this->db->from('tblmeetings');
        $query = $this->db->get()->result();
        return $query;
    }

    /*for retrieve addressbook search result*/
    public function get_addressbook_search($search)
    {

        $new_word = explode(" ", $search);
        $brandid = get_user_session();

        $this->db->query('SET sql_mode=""');
        $this->db->select('tbladdressbook.*,tbladdressbook_client.addressbookid,tbladdressbook_client.brandid,(select tbladdressbookemail.email from tbladdressbookemail where tbladdressbookemail.type = "primary" and addressbookid = tbladdressbook.addressbookid) as emailaddress');

        $this->db->group_start();
        $this->db->where('tbladdressbook.deleted', '0');
        $this->db->group_end();

        $this->db->group_start();
        $this->db->where('tbladdressbook_client.brandid', $brandid);
        $this->db->or_where('tbladdressbook.ispublic', '1');
        $this->db->group_end();

        $this->db->group_start();
        $this->db->group_by('tbladdressbook.addressbookid');

        foreach ($new_word as $searchvalue) {
            $this->db->like('tbladdressbook.firstname', $searchvalue);
            $this->db->or_like('tbladdressbookemail.email', $searchvalue);
            $this->db->or_like('tbladdressbookphone.phone', $searchvalue);
            $this->db->or_like('tbladdressbook.lastname', $searchvalue);
            $this->db->or_like('tbladdressbookdetails.address', $searchvalue);
            $this->db->or_like('tbladdressbookdetails.address2', $searchvalue);
            $this->db->or_like('tbladdressbookdetails.city', $searchvalue);
            $this->db->or_like('tbladdressbookdetails.state', $searchvalue);
            $this->db->or_like('tbladdressbookdetails.zip', $searchvalue);
        }

        $this->db->group_end();

        $this->db->from('tbladdressbook');
        $this->db->join('tbladdressbook_client', 'tbladdressbook.addressbookid = tbladdressbook_client.addressbookid', 'left');
        $this->db->join('tbladdressbookemail', 'tbladdressbook.addressbookid = tbladdressbookemail.addressbookid', 'left');
        $this->db->join('tbladdressbookphone', 'tbladdressbook.addressbookid = tbladdressbookphone.addressbookid', 'left');
        $this->db->join('tbladdressbookdetails', 'tbladdressbook.addressbookid = tbladdressbookdetails.addressbookid', 'left');
        $query = $this->db->get()->result();
        //echo $this->db->last_query();exit;
        return $query;
    }

    /*for retrieve messages search result*/
    public function get_messages_search($search)
    {
        $brandid = get_user_session();
        $this->db->select('*');
        $this->db->like('subject', $search);
        $this->db->where('brandid', $brandid);
        $this->db->where('deleted', '0');
        $this->db->from('tblmessages');
        $query = $this->db->get()->result();
        return $query;
    }

    /*for retrieve proposals search result*/
    public function get_proposals_search($search)
    {
        $brandid = get_user_session();
        $this->db->select('*');
        $this->db->like('name', $search);
        $this->db->where('brandid', $brandid);
        $this->db->where('deleted', '0');
        $this->db->from('tblproposaltemplates');
        $query = $this->db->get()->result();
        return $query;
    }

    /*for retrieve agreements search result*/
    public function get_agreements_search($search)
    {
        $brandid = get_user_session();
        $this->db->select('*');
        $this->db->like('name', $search);
        //$this->db->or_like('content', $search);
        $this->db->where('brandid', $brandid);
        $this->db->where('deleted', '0');
        $this->db->from('tblagreementtemplates');
        $query = $this->db->get()->result();
        return $query;
    }

    /*for retrieve payment schedules search result*/
    public function get_paymentschedules_search($search)
    {
        $brandid = get_user_session();
        $this->db->select('*');
        $this->db->like('name', $search);
        $this->db->where('brandid', $brandid);
        $this->db->where('deleted', '0');
        $this->db->from('tblpaymenttemplates');
        $query = $this->db->get()->result();
        return $query;
    }

    /*
    ** Added By Sanjay on 02/13/2018 
    ** Get all dashboard setting data
    */
    public function get_dashboard_data()
    {
        $this->db->where('deleted', 0);
        $this->db->where('staffid', get_staff_user_id());
        $this->db->where('brandid', get_user_session());
        return $this->db->get('tbldashboard_settings')->row();
    }

    /*
    ** Added By Sanjay on 02/13/2018
    ** Get package type by package type id
    */
    public function get_package_type()
    {
        $this->db->select('name');
        $this->db->where('id', $this->session->userdata['package_type_id']);
        return $this->db->get('tblpackagetype')->row();
    }

    /*
   ** Added By Sanjay on 02/13/2018
   ** Get banner by brand id
   */
    public function get_banner_by_brand()
    {
        $this->db->select('value');
        $this->db->where('name', 'banner');
        $this->db->where('brandid', get_user_session());
        $banner = $this->db->get('tblbrandsettings')->row();
        return $banner;
    }

    /*
    ** Added By Sanjay on 02/13/2018
    ** Get package type by package type id
    */
    public function get_package_days()
    {
        $this->db->select('name,trial_period');
        $this->db->where('packageid', $this->session->userdata['package_id']);
        return $this->db->get('tblpackages')->row();
    }

    /*
    ** Added By Sanjay on 02/14/2018
    ** check for proposal of specific logged in user
    */
    public function get_proposal_status()
    {
        $this->db->select('*');
        $this->db->where('created_by', $this->session->userdata['staff_user_id']);
        $total_rows = $this->db->get('tblproposaltemplates')->num_rows();
        return $total_rows;
    }

    /*
    ** Added By Sanjay on 02/14/2018
    ** check for contact of specific logged in user
    */
    public function get_contact_status()
    {
        $this->db->select('*');
        $this->db->where('created_by', $this->session->userdata['staff_user_id']);
        $total_rows = $this->db->get('tbladdressbook')->num_rows();
        return $total_rows;
    }

    /*
    ** Added By Sanjay on 02/14/2018
    ** check for lead creation of specific logged in user
    */
    public function get_lead_status()
    {
        $this->db->select('*');
        $this->db->where('addedfrom', $this->session->userdata['staff_user_id']);
        $total_rows = $this->db->get('tblleads')->num_rows();
        return $total_rows;
    }

    /*
    ** Added By Sanjay on 02/14/2018
    ** check for invoice creation of specific logged in user
    */
    public function get_invoice_status()
    {
        $this->db->select('*');
        $this->db->where('addedfrom', $this->session->userdata['staff_user_id']);
        $total_rows = $this->db->get('tblinvoices')->num_rows();
        return $total_rows;
    }

    /*
    ** Added By Sanjay on 02/14/2018
    ** check for theme status of specific logged in user
    */
    public function get_theme_status()
    {
        $this->db->select('*');
        $this->db->where('name', 'clients_default_theme');
        $this->db->where('created_by', $this->session->userdata['staff_user_id']);
        $total_rows = $this->db->get('tblbrandsettings')->num_rows();
        return $total_rows;
    }


    /*
    ** Added By Sanjay on 02/14/2018
    ** check for bank detail status of specific logged in user
    */
    public function get_banking_status()
    {
        $cid = get_user_session();
        $query = "SELECT name,value FROM tblbrandsettings WHERE name IN ('invoice_prefix','invoice_number_format','predefined_clientnote_invoice','predefined_terms_invoice') AND brandid = $cid";
        $result = $this->db->query($query);
        $rows = $result->result_array();
        return $rows;
    }

    /*
    ** Added By Sanjay on 02/14/2018
    ** check for company detail status of specific logged in user
    */
    public function get_company_info_status()
    {
        $cid = get_user_session();
        $query = "SELECT name,value FROM tblbrandsettings WHERE name IN ('companyname','invoice_company_address','invoice_company_city','company_state','invoice_company_postal_code','customer_info_format') AND brandid = $cid";
        $result = $this->db->query($query);
        $rows = $result->result_array();
        return $rows;
    }


    /*
    ** Added By Sanjay on 02/14/2018
    ** Get all project and sub-project of loggedin user
    */
    public function get_all_project_data($interval = 3)
    {
        //$interval=$interval+1;
        $this->db->select("tblprojects.*,tblprojects.eventstartdatetime as sorting_date,'project' as type,tblstaff.firstname,tblstaff.lastname,(SELECT venuename from tblvenue where venueid=tblprojects.venueid) as venuename");
        $this->db->where('tblprojects.deleted', 0);
        $this->db->where('tblprojects.parent != ', 0);

        //for sido admin
        if (!empty(get_user_session())) {
            $this->db->where('tblprojects.brandid', get_user_session());
        }

        //$this->db->where('tblprojects.eventstartdatetime >=', date('Y-m-d H:i:s'));
        $this->db->where('tblprojects.eventstartdatetime BETWEEN CURRENT_DATE() AND DATE_ADD(CURRENT_DATE(), INTERVAL ' . $interval . ' DAY)');
        $this->db->order_by('tblprojects.eventstartdatetime', "ASC");
        $this->db->join('tblstaff', 'tblprojects.assigned = tblstaff.staffid', 'left');
        $result = $this->db->get('tblprojects')->result_array();
        return $result;
    }


    /*
    ** Added By Sanjay on 02/23/2018
    ** Get all leads of logged-in user
    */
    public function get_all_lead_data($interval = 3)
    {
        $this->db->select("tblleads.*,tblleads.eventstartdatetime as sorting_date,'lead' as type,tblstaff.firstname,tblstaff.lastname,(SELECT venuename from tblvenue where venueid=tblleads.venueid) as venuename");
        $this->db->where('tblleads.deleted', 0);
        $this->db->where('tblleads.converted', '0');
        //for sido admin
        if (!empty(get_user_session())) {
            $this->db->where('tblleads.brandid', get_user_session());
        }

        //$this->db->where('tblleads.eventstartdatetime >=', date('Y-m-d H:i:s'));
        $this->db->where('tblleads.eventstartdatetime BETWEEN CURRENT_DATE() AND DATE_ADD(CURRENT_DATE(), INTERVAL ' . $interval . ' DAY)');
        $this->db->order_by('tblleads.eventstartdatetime', "ASC");
        $this->db->join('tblstaff', 'tblleads.assigned = tblstaff.staffid', 'left');
        return $this->db->get('tblleads')->result_array();
    }


    /*
    ** Added By Sanjay on 02/23/2018
    ** Get all tasks list of logged-in user
    */
    public function get_all_tasks_data($interval = 3)
    {
        $this->db->select("tblstafftasks.*,tblstafftasks.duedate as sorting_date,'task' as type,tblstaff.firstname,tblstaff.lastname");
        $this->db->where('tblstafftasks.deleted', 0);

        //for sido admin
        if (!empty(get_user_session())) {
            $this->db->where('tblstafftasks.brandid', get_user_session());
        }

        //$this->db->where('tblstafftasks.duedate >=', date('Y-m-d H:i:s'));
        $this->db->where('tblstafftasks.duedate BETWEEN CURRENT_DATE() AND DATE_ADD(CURRENT_DATE(), INTERVAL ' . $interval . ' DAY)');
        $this->db->order_by('tblstafftasks.duedate', "ASC");
        $this->db->join('tblstaff', 'tblstafftasks.addedfrom = tblstaff.staffid', 'left');
        $result = $this->db->get('tblstafftasks')->result_array();
        return $result;
    }


    /*
    ** Added By Sanjay on 02/23/2018
    ** Get all meeting list of logged-in user
    */
    public function get_all_meeting_data($interval = 3)
    {
        $this->db->select("tblmeetings.*,tblmeetings.start_date as sorting_date,'meeting' as type,tblstaff.firstname,tblstaff.lastname");
        $this->db->where('tblmeetings.deleted', 0);

        //for sido admin
        if (!empty(get_user_session())) {
            $this->db->where('tblmeetings.brandid', get_user_session());
        }

        //$this->db->where('tblmeetings.start_date >=', date('Y-m-d H:i:s'));
        $this->db->where('tblmeetings.start_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND DATE_ADD(CURRENT_DATE(), INTERVAL ' . $interval . ' DAY)');
        $this->db->order_by('tblmeetings.start_date', "ASC");
        $this->db->join('tblstaff', 'tblmeetings.created_by = tblstaff.staffid', 'left');
        return $this->db->get('tblmeetings')->result_array();
    }

    /*
    ** Added By Sanjay on 03/01/2018
    ** Get all my project and sub-project of loggedin user
    */
    public function get_my_all_project_data($interval = 3)
    {
        $this->db->select("tblprojects.*,tblprojects.eventstartdatetime as sorting_date,'project' as type,tblstaff.firstname,tblstaff.lastname,(SELECT venuename from tblvenue where venueid=tblprojects.venueid) as venuename");
        $this->db->where('tblprojects.deleted', 0);
        $this->db->where('tblprojects.parent != ', 0);

        //for sido admin
        if (!empty(get_user_session())) {
            $this->db->where('tblprojects.brandid', get_user_session());
            $this->db->where('tblprojects.assigned', get_staff_user_id());
        }

        //$this->db->where('tblprojects.eventstartdatetime >=', date('Y-m-d H:i:s'));
        $this->db->where('tblprojects.eventstartdatetime BETWEEN CURRENT_DATE() AND DATE_ADD(CURRENT_DATE(), INTERVAL ' . $interval . ' DAY)');
        $this->db->order_by('eventstartdatetime', "ASC");
        $this->db->join('tblstaff', 'tblprojects.assigned = tblstaff.staffid', 'left');
        return $this->db->get('tblprojects')->result_array();
    }

    /*
    ** Added By Sanjay on 03/01/2018
    ** Get all my leads of logged-in user
    */
    public function get_my_all_lead_data($interval = 3)
    {
        $this->db->select("tblleads.*,tblleads.eventstartdatetime as sorting_date,'lead' as type,tblstaff.firstname,tblstaff.lastname,(SELECT venuename from tblvenue where venueid=tblleads.venueid) as venuename");
        $this->db->where('tblleads.deleted', 0);
        $this->db->where('tblleads.converted', 0);

        //for sido admin
        if (!empty(get_user_session())) {
            $this->db->where('tblleads.brandid', get_user_session());
            $this->db->where('tblleads.assigned', get_staff_user_id());
        }

        //$this->db->where('tblleads.eventstartdatetime >=', date('Y-m-d H:i:s'));
        $this->db->where('tblleads.eventstartdatetime BETWEEN CURRENT_DATE() AND DATE_ADD(CURRENT_DATE(), INTERVAL ' . $interval . ' DAY)');
        $this->db->order_by('tblleads.eventstartdatetime', "ASC");
        $this->db->join('tblstaff', 'tblleads.assigned = tblstaff.staffid', 'left');
        return $this->db->get('tblleads')->result_array();
    }

    /*
    ** Added By Sanjay on 03/01/2018
    ** Get all my tasks list of logged-in user
    */
    public function get_my_all_tasks_data($interval = 3)
    {
        $this->db->select("tblstafftasks.*,tblstafftasks.duedate as sorting_date,'task' as type,tblstaff.firstname,tblstaff.lastname");
        $this->db->join('tblstafftaskassignees', 'tblstafftaskassignees.taskid = tblstafftasks.id', 'left');
        $this->db->where('tblstafftasks.deleted', 0);

        //for sido admin
        if (!empty(get_user_session())) {
            $this->db->where('tblstafftasks.brandid', get_user_session());
            $this->db->where('tblstafftaskassignees.staffid', get_staff_user_id());
        }
        $this->db->where('tblstafftasks.duedate BETWEEN CURRENT_DATE() AND DATE_ADD(CURRENT_DATE(), INTERVAL ' . $interval . ' DAY)');
        $this->db->order_by('tblstafftasks.duedate', "ASC");
        $this->db->join('tblstaff', 'tblstafftaskassignees.staffid = tblstaff.staffid', 'left');
        $result = $this->db->get('tblstafftasks')->result_array();
        return $result;
    }

    /*
    ** Added By Sanjay on 02/23/2018
    ** Get all meeting list of logged-in user
    */
    public function get_my_all_meeting_data($interval = 3)
    {
        $this->db->select("tblmeetings.*,tblmeetings.start_date as sorting_date,'meeting' as type,tblstaff.firstname,tblstaff.lastname");

        $this->db->join('tblmeetingusers', 'tblmeetingusers.meeting_id =  tblmeetings.meetingid', 'left');

        $this->db->where('tblmeetings.deleted', 0);

        //for sido admin
        if (!empty(get_user_session())) {
            $this->db->where('tblmeetingusers.user_id', get_staff_user_id());
            $this->db->where('tblmeetings.brandid', get_user_session());
        }
        $this->db->join('tblstaff', 'tblmeetingusers.user_id = tblstaff.staffid', 'left');
        $this->db->where('tblmeetings.start_date >=', date('Y-m-d'));
        $this->db->where('tblmeetings.start_date <=', date('Y-m-d', strtotime('+' . $interval . ' day')));
        //$this->db->where('tblmeetings.start_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL '.$interval.' DAY)');
        $this->db->order_by('tblmeetings.start_date', "desc");
        $result = $this->db->get('tblmeetings')->result_array();
        /*echo $this->db->last_query();
        echo "<pre>";
        print_r($result);
        die();*/
        return $result;
    }

    /*
    ** Added By Sanjay on 02/23/2018
    ** Get all pinned project data of logged-in user
    */
    public function get_all_project_pinned_data()
    {
        $this->db->query('SET sql_mode=""');
        $this->db->select("tblpins.*,tblprojects.id as pro_id,tblprojects.name as pro_name,tblprojects.eventstartdatetime as sorting_date,tbleventtype.eventtypename as pro_event_type,tblprojects.eventstartdatetime,tblprojects.eventenddatetime, CONCAT(tblvenue.venuename, ' ', tblvenue.venueaddress) AS venuelocation, tblprojectstatus.name as pro_status, tblprojectstatus.color as pro_status_color,tblstaff.staffid, tblstaff.firstname as pro_ass_fname, tblstaff.lastname as pro_ass_lname");
        $this->db->join('tblprojects', 'tblprojects.id = tblpins.pintypeid', 'left');
        $this->db->join('tbleventtype', 'tblprojects.eventtypeid =  tbleventtype.eventtypeid', 'left');
        $this->db->join('tblvenue', 'tblprojects.venueid =  tblvenue.venueid', 'left');
        $this->db->join('tblprojectstatus', 'tblprojectstatus.id = tblprojects.status', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblprojects.assigned', 'left');

        //for sido admin
        if (!empty(get_user_session())) {
            $this->db->where('tblprojects.brandid', get_user_session());
            $this->db->where('tblpins.userid', get_staff_user_id());
        }

        $this->db->where('tblpins.pintype', 'Project');
        $result = $this->db->get('tblpins')->result_array();
        return $result;
    }

    /*
    ** Added By Sanjay on 02/23/2018
    ** Get all pinned lead data of logged-in user
    */
    public function get_all_lead_pinned_data()
    {
        $this->db->select("tblpins.*,tblleads.id as lead_id,tblleads.name as lead_name,tblleads.eventstartdatetime as sorting_date,tblleads.eventstartdatetime,tblleads.eventenddatetime,tbleventtype.eventtypename as lead_event_type,tblstaff.staffid, tblstaff.firstname as lead_ass_fname, tblstaff.lastname as lead_ass_lname,tblleadsstatus.name as lead_status,tblleadsstatus.color as lead_status_color,tblleadsstatus.name as task_status,tblleadsstatus.color");
        $this->db->join('tblleads', 'tblpins.pintypeid = tblleads.id', 'left');
        $this->db->join('tblstafftasks', 'tblpins.pintypeid =  tblstafftasks.id', 'left');
        $this->db->join('tbleventtype', 'tblleads.eventtypeid =  tbleventtype.eventtypeid', 'left');
        $this->db->join('tblprojectstatus', 'tblprojectstatus.id = tblleads.status', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblleads.assigned', 'left');
        $this->db->join('tblleadsstatus', 'tblleadsstatus.id = tblleads.status', 'left');

        //for sido admin
        if (!empty(get_user_session())) {
            $this->db->where('tblpins.userid', get_staff_user_id());
            $this->db->where('tblleads.brandid', get_user_session());
        }

        $this->db->where('tblleads.converted', '0');
        $this->db->where('tblleads.deleted', '0');
        $this->db->where('tblpins.pintype', 'Lead');
        $result = $this->db->get('tblpins')->result_array();
        return $result;
    }


    /*
    ** Added By Sanjay on 02/23/2018
    ** Get all pinned tasks data of logged-in user
    */
    public function get_all_task_pinned_data()
    {
        //$this->db->query('SET sql_mode=""');
        $this->db->select("tblpins.*,tblstafftasks.id as task_id,tblstafftasks.name as task_name,tblstafftasks.duedate as sorting_date,task_tblleads.name as task_event_type,tbltasksstatus.name as task_status,tbltasksstatus.color as task_status_color,tblstaff.staffid, 
            task_ass_name.firstname as task_ass_fname,
            task_ass_name.lastname as task_ass_lname");
        $this->db->join('tblstafftasks', 'tblpins.pintypeid =  tblstafftasks.id', 'left');
        $this->db->join('tblleads as task_tblleads', 'tblstafftasks.rel_id =  task_tblleads.id', 'left');
        $this->db->join('tbltasksstatus', 'tbltasksstatus.id = tblstafftasks.status', 'left');
        $this->db->join('tblleads', 'tblpins.pintypeid = tblleads.id', 'left');
        $this->db->join('tbleventtype', 'tblleads.eventtypeid =  tbleventtype.eventtypeid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblleads.assigned', 'left');
        $this->db->join('tblstafftaskassignees', 'tblstafftaskassignees.taskid = tblstafftasks.id', 'left');
        $this->db->join('tblstaff as task_ass_name', 'tblstafftaskassignees.staffid = task_ass_name.staffid', 'left');

        //for sido admin
        if (!empty(get_user_session())) {
            $this->db->where('tblpins.userid', get_staff_user_id());
            $this->db->where('tblstafftasks.brandid', get_user_session());
        }
        $this->db->group_by('tblstafftaskassignees.taskid');
        $this->db->where('tblstafftasks.deleted', 0);
        $this->db->where('tblpins.pintype', 'Task');
        $result = $this->db->get('tblpins')->result_array();
        return $result;
    }

    /*
   ** Added By Sanjay on 02/23/2018
   ** Get all pinned messages data of logged-in user
   */
    public function get_all_message_pinned_data()
    {
        $this->db->select("tblpins.*,tblmessages.id as msg_id, tblmessages.subject,tblmessages.created_date as sorting_date,tblmessages.id as msg_id,tblstaff.staffid, tblstaff.firstname,tblstaff.lastname");

        $this->db->join('tblmessages', 'tblmessages.id =  tblpins.pintypeid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblmessages.created_by', 'left');

        //for sido admin
        if (!empty(get_user_session())) {
            $this->db->where('tblpins.userid', get_staff_user_id());
            $this->db->where('tblmessages.brandid', get_user_session());
        }

        $this->db->where('tblpins.pintype', 'Message');
        $result = $this->db->get('tblpins')->result_array();
        return $result;
    }

    /*
    ** Added By Sanjay on 02/16/2018
    ** Get total lead count of logged in user
    */
    public function get_quick_link_all_count()
    {
        $userid = $this->session->userdata['staff_user_id'];
        $lead_query = "SELECT * FROM tblleads WHERE deleted = 0 AND brandid = " . get_user_session();
        $lead_result = $this->db->query($lead_query);
        $lead_count = $lead_result->num_rows();

        /*$project_query = "SELECT * FROM tblprojects WHERE deleted = 0 AND parent = 0 AND brandid = " . get_user_session();*/
        $this->db->select('maintbl.id');
        $this->db->join('tblprojectstatus', 'tblprojectstatus.id = maintbl.status', 'left');
        $this->db->join('tblstaffprojectassignee', 'tblstaffprojectassignee.projectid = maintbl.id', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblstaffprojectassignee.assigned', 'left');
        $this->db->join('tblleadssources', 'tblleadssources.id=maintbl.source', 'left');
        $this->db->join('tbleventtype', 'tbleventtype.eventtypeid=maintbl.eventtypeid', 'left');
        $this->db->where('maintbl.parent', 0);
        if ($_SESSION['user_type'] == 2) {
            $this->db->join('tblprojectcontact', 'tblprojectcontact.projectid = maintbl.id or tblprojectcontact.eventid = maintbl.id', 'left');
            $this->db->where('tblprojectcontact.contactid', get_staff_user_id());
        } else {
            $this->db->where('maintbl.brandid', get_user_session());
        }
        $project_result = $this->db->get('tblprojects  as maintbl')->result_array();
        $project_count = count($project_result);


        $message_query = "SELECT * FROM tblmessages JOIN `tblmessagesallusers` ON `tblmessagesallusers`.`messageid` = `tblmessages`.`id` WHERE isread = 0 AND deleted = 0 AND brandid = " . get_user_session() . " AND userid = " . get_staff_user_id();
        $message_result = $this->db->query($message_query);
        $message_count = $message_result->num_rows();

        $meeting_query = "SELECT * FROM tblmeetings WHERE deleted = 0 AND brandid = " . get_user_session();
        $meeting_result = $this->db->query($meeting_query);
        $meeting_count = $meeting_result->num_rows();

        $task_query = "SELECT * FROM tblstafftasks WHERE deleted = 0 AND duedate >= CURRENT_DATE() AND brandid = " . get_user_session();
        $task_result = $this->db->query($task_query);
        $task_count = $task_result->num_rows();

        $invite_query = "SELECT * FROM tblinvite WHERE deleted = 0 AND status = 'Pending Approval from Account Owner' AND brandid = " . get_user_session();
        $invite_result = $this->db->query($invite_query);
        $invite_count = $invite_result->num_rows();

        $count['lead_count'] = $lead_count;
        $count['project_count'] = $project_count;
        $count['message_count'] = $message_count;
        $count['task_count'] = $task_count;
        $count['meeting_count'] = $meeting_count;
        $count['invite_count'] = $invite_count;

        return $count;
    }


    /*
    ** Added By Sanjay on 02/16/2018
    ** Get all pinned contact data of logged in user
    */
    public function get_all_pinned_contacts()
    {
        $this->db->select('tblpins.*,tbladdressbook.addressbookid,tbladdressbook.firstname,tbladdressbook.lastname,tbladdressbook.profile_image,tbladdressbook.created_by,(SELECT tbladdressbookemail.email FROM tbladdressbookemail WHERE addressbookid = tbladdressbook.addressbookid AND type = "primary")as primary_email,(SELECT tbladdressbookphone.phone FROM tbladdressbookphone WHERE addressbookid = tbladdressbook.addressbookid AND type = "primary")as primary_phone');
        $this->db->join('tbladdressbook', 'tblpins.pintypeid = tbladdressbook.addressbookid', 'left');

        //for sido admin
        if (!empty(get_user_session())) {
            $this->db->join('tbladdressbook_client', 'tbladdressbook_client.addressbookid = tbladdressbook.addressbookid', 'left');
            $this->db->where('tblpins.userid', get_staff_user_id());
            $this->db->where('tbladdressbook_client.brandid', get_user_session());
            $this->db->where('tbladdressbook_client.deleted', 0);
        }

        $this->db->where('tblpins.pintype', 'Addressbook');

        return $this->db->get('tblpins')->result_array();
    }


    /*
    ** Added By Sanjay on 02/21/2018
    ** Get all pinned venues data of logged in user
    */
    public function get_all_pinned_venues()
    {
        $this->db->select('tblpins.*,tblvenue.venueid,tblvenue.venuename,tblvenue.venuelogo,tblvenue.venueemail,tblvenue.venuephone');
        $this->db->join('tblvenue', 'tblpins.pintypeid = tblvenue.venueid', 'left');

        //for sido admin
        if (!empty(get_user_session())) {
            $this->db->join('tblbrandvenue', 'tblbrandvenue.venueid = tblvenue.venueid', 'left');
            $this->db->where('tblpins.userid', get_staff_user_id());
            $this->db->where('tblbrandvenue.brandid', get_user_session());
            $this->db->where('tblbrandvenue.deleted', 0);
        }

        $this->db->where('tblpins.pintype', 'Venues');

        return $this->db->get('tblpins')->result_array();
    }

    /*
    ** Added By Sanjay on 02/16/2018
    ** Get all message data of logged in user
    */
    public function get_all_message_data($interval = 3)
    {
        $user_id = get_staff_user_id();
        $this->db->select("tblmessages.*,tblmessagesallusers.isread,tblstaff.firstname,tblstaff.lastname,tblstaff.profilecolor");
        $this->db->join('tblmessagesallusers', 'tblmessages.id = tblmessagesallusers.messageid', 'left');
        $this->db->join('tblstaff', 'tblmessages.created_by = tblstaff.staffid', 'left');
        $this->db->where('tblmessages.deleted', 0);
        $this->db->where('tblmessages.parent', 0);
        $this->db->where('tblmessagesallusers.userid', $user_id);
        $this->db->where('tblmessagesallusers.isread', 0);
        $this->db->where('tblmessages.brandid', get_user_session());
        $this->db->where('tblmessages.created_date BETWEEN DATE_SUB(CURDATE(), INTERVAL ' . $interval . ' DAY) AND DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY)');
        $this->db->group_by('tblmessages.id');
        $this->db->order_by('tblmessages.created_date', 'desc');
        $result = $this->db->get('tblmessages')->result_array();
        //return $result;
    }


    /*
    ** Added By Sanjay on 02/16/2018
    ** Get all unread message data of logged in user
    */
    public function get_all_unread_message_data($interval = 3)
    {
        $user_id = $this->session->userdata['staff_user_id'];
        $brandid = get_user_session();


        $meeting_query = "SET sql_mode = '' ";
        $meeting_result = $this->db->query($meeting_query);

        $this->db->select("tblmessagesallusers.*,tblmessages.*,tblstaff.firstname,tblstaff.lastname,tblstaff.profilecolor,tblprojects.eventstartdatetime,assigned_info.firstname as ass_fname,assigned_info.lastname as ass_lname,tblprojectstatus.name as umsg_pro_status, tblprojectstatus.color as umsg_pro_color,'project' as umsg_lead_type,CONCAT(tblvenue.venuename,' ',tblvenue.venueaddress) as venue, tblprojects.name, tbleventtype.eventtypename");

        $this->db->join('tblmessages', 'tblmessages.id =  tblmessagesallusers.messageid', 'left');
        $this->db->join('tblprojects', 'tblprojects.id =  tblmessages.rel_id', 'left');
        $this->db->join('tblstaff', 'tblmessages.created_by =  tblstaff.staffid', 'left');
        $this->db->join('tblprojectstatus', 'tblprojectstatus.id = tblprojects.status', 'left');
        $this->db->join('tbleventtype', 'tblprojects.eventtypeid =  tbleventtype.eventtypeid', 'left');

        $this->db->join('tblvenue', 'tblvenue.venueid = tblprojects.venueid', 'left');

        $this->db->join('tblstaff as assigned_info', 'tblprojects.assigned =  assigned_info.staffid', 'left');
        //$this->db->where('tblmessages.rel_type','project');
        $this->db->where('tblmessagesallusers.userid =', $user_id);
        $this->db->where('tblmessagesallusers.isread', 0);
        $this->db->where('tblmessages.brandid', get_user_session());
        $this->db->where('tblmessages.parent=0');
        $this->db->where('tblmessages.created_date BETWEEN DATE_SUB(CURDATE(), INTERVAL ' . $interval . ' DAY) AND CURDATE()');
        $this->db->group_by('tblmessages.id');
        $this->db->order_by('tblmessages.created_date', 'desc');
        $result = $this->db->get('tblmessagesallusers')->result_array();
        /*echo $staffid = $this->session->userdata['staff_user_id'];
        die('here');*/
        $staffid = $this->session->userdata['staff_user_id'];
        $interval = 7;
        $this->db->select('(SELECT id FROM tblmessages m JOIN tblmessagesallusers mu ON (mu.messageid = m.id AND mu.isread = 0 AND mu.userid = ' . $staffid . ') WHERE m.id = tblmessages.id OR m.parent = tblmessages.id limit 1) as id,(SELECT parent FROM tblmessages m JOIN tblmessagesallusers mu ON (mu.messageid = m.id AND mu.isread = 0 AND mu.userid = ' . $staffid . ') WHERE m.id = tblmessages.id OR m.parent = tblmessages.id limit 1) as parent');
        $this->db->where('deleted', 0);
        $this->db->where('parent', 0);
        $this->db->where('tblmessages.created_date BETWEEN DATE_SUB(CURDATE(), INTERVAL ' . $interval . ' DAY) AND DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY)');
        $unreadmessages = $this->db->get('tblmessages')->result_array();
        $result = array();
        $this->load->model('messages_model');
        foreach ($unreadmessages as $key => $unreadmessage) {
            if ($unreadmessage['id'] != "") {
                if ($unreadmessage['parent'] > 0) {
                    $result[] = (array)$this->messages_model->getmessagedetails($unreadmessage['parent']);
                } else {
                    $result[] = (array)$this->messages_model->getmessagedetails($unreadmessage['id']);
                }
            }
        }
        /*echo "<pre>";
        print_r($result);
        die();*/
        return $result;

    }


    /*
    ** Added By Sanjay on 02/28/2018
    ** Get all unread lead message data of logged in user
    */
    public function get_all_lead_unread_message_data($interval = 3)
    {
        $user_id = $this->session->userdata['staff_user_id'];
        $brandid = get_user_session();

        $this->db->select("tblmessagesallusers.*,tblmessages.*,tblstaff.firstname,tblstaff.lastname,tblstaff.profilecolor,tblleads.eventstartdatetime,lead_assigned.firstname as ass_fname,lead_assigned.lastname as ass_lname,tblleadsstatus.name as umsg_pro_status, tblleadsstatus.color as umsg_pro_color,'lead' as umsg_lead_type,CONCAT(tblvenue.venuename,' ',tblvenue.venueaddress) as venue, tblleads.name, tbleventtype.eventtypename");
        $this->db->join('tblmessages', 'tblmessages.id =  tblmessagesallusers.messageid', 'left');
        $this->db->join('tblleads', 'tblleads.id =  tblmessages.rel_id', 'left');
        $this->db->join('tblstaff', 'tblmessages.created_by =  tblstaff.staffid', 'left');
        $this->db->join('tblleadsstatus', 'tblleadsstatus.id = tblleads.status', 'left');
        $this->db->join('tblstaff as lead_assigned', 'tblleads.assigned =  lead_assigned.staffid', 'left');
        $this->db->join('tblvenue', 'tblvenue.venueid = tblleads.venueid', 'left');
        $this->db->join('tbleventtype', 'tblleads.eventtypeid =  tbleventtype.eventtypeid', 'left');
        $this->db->where('tblmessages.rel_type', 'lead');
        $this->db->where('tblmessagesallusers.userid =', $user_id);
        $this->db->where('tblmessagesallusers.isread', 0);
        $this->db->where('tblmessages.brandid', get_user_session());
        $this->db->where('tblmessages.parent', 0);
        $this->db->where('tblmessages.created_date BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL ' . $interval . ' DAY) AND DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY)');
        $this->db->group_by('tblmessages.id');
        $this->db->order_by('tblmessages.created_date', 'desc');
        return $this->db->get('tblmessagesallusers')->result_array();
    }

    /*
    ** Added By Sanjay on 02/22/2018
    ** Get all activity data of logged in user
    */
    public function get_all_activity_log_data($interval = 3)
    {
        $this->db->select("tblprojectactivity.*,tblprojectactivity.dateadded as act_sorting_date,tblprojects.*, tbleventtype.eventtypename, CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as updated_by_name,tblstaff.firstname as pro_by_fname,tblstaff.lastname as pro_by_lname,CONCAT(tblvenue.venuename,' ',tblvenue.venueaddress) as venue,tblprojectstatus.name as pro_act_status, tblprojectstatus.color as pro_act_color,assigned_info.firstname as act_ass_fname,assigned_info.lastname as act_ass_lname,'project_info' as activity_type");
        $this->db->join('tblprojects', 'tblprojects.id = tblprojectactivity.project_id', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblprojects.updatedby', 'left');
        $this->db->join('tblvenue', 'tblvenue.venueid = tblprojects.venueid', 'left');
        $this->db->join('tbleventtype', 'tbleventtype.eventtypeid = tblprojects.eventtypeid', 'left');
        $this->db->join('tblprojectstatus', 'tblprojectstatus.id = tblprojects.status', 'left');
        $this->db->join('tblstaff as assigned_info', 'tblprojects.assigned =  assigned_info.staffid', 'left');

        //for sido admin
        if (!empty(get_user_session())) {
            $this->db->where('tblprojectactivity.brandid', get_user_session());
        }

        $this->db->where('tblprojectactivity.dateadded BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL ' . $interval . ' DAY) AND DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY)');
        $this->db->order_by("tblprojectactivity.dateadded", "DESC");
        $result = $this->db->get('tblprojectactivity')->result_array();
        return $result;
    }

    /*
    ** Added By Sanjay on 02/23/2018
    ** Get all lead activity data of logged in user
    */
    public function get_all_lead_activity_log_data($interval = 3)
    {
        $this->db->select("tblleadactivitylog.*,tblleadactivitylog.date as act_sorting_date,tblleads.*, tbleventtype.eventtypename, CONCAT(tblstaff.firstname,' ',tblstaff.lastname) as lead_updated_by_name,tblstaff.firstname as lead_by_fname,tblstaff.lastname as lead_by_lname,CONCAT(tblvenue.venuename,' ',tblvenue.venueaddress) as venue,tblleadsstatus.name as lead_act_status, tblleadsstatus.color as lead_act_color,assigned_info.firstname as lead_act_ass_fname,assigned_info.lastname as lead_act_ass_lname,'lead_info' as activity_type, tblleadactivitylog.description as description_key");
        $this->db->join('tblleads', 'tblleads.id = tblleadactivitylog.leadid', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblleads.updatedby', 'left');
        $this->db->join('tblvenue', 'tblvenue.venueid = tblleads.venueid', 'left');
        $this->db->join('tblleadsstatus', 'tblleadsstatus.id = tblleads.status', 'left');
        $this->db->join('tbleventtype', 'tbleventtype.eventtypeid = tblleads.eventtypeid', 'left');
        $this->db->join('tblstaff as assigned_info', 'tblleads.assigned =  assigned_info.staffid', 'left');

        //for sido admin
        if (!empty(get_user_session())) {
            $this->db->where('tblleadactivitylog.brandid', get_user_session());
        }

        $this->db->where('tblleadactivitylog.date BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL ' . $interval . ' DAY) AND DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY)');
        $this->db->order_by("tblleadactivitylog.date", "DESC");
        $result = $this->db->get('tblleadactivitylog')->result_array();
        return $result;
    }

    /*
    ** Added By Sanjay on 02/28/2018
    ** Get all stuff activity data of logged in user
    */
    public function get_all_stuff_activity_log_data($interval = 3)
    {
        $this->db->select("tblactivitylog.*,tblactivitylog.date as act_sorting_date,'all_info' as activity_type");

        //for sido admin
        if (!empty(get_user_session())) {
            $this->db->where('tblactivitylog.brandid', get_user_session());
        }

        $this->db->where('(description LIKE "%Updated%" OR description LIKE "%updated%" OR description LIKE "%Added%" OR description LIKE "%added%" OR description LIKE "%Deleted%" OR description LIKE "%deleted%")');
        $this->db->where('tblactivitylog.date BETWEEN DATE_SUB(CURRENT_DATE(), INTERVAL ' . $interval . ' DAY) AND DATE_ADD(CURRENT_DATE(), INTERVAL 1 DAY)');
        $this->db->order_by("tblactivitylog.date", "DESC");
        $result = $this->db->get('tblactivitylog')->result_array();
        return $result;
    }

    /*
    ** Added By Sanjay on 02/26/2018 
    ** Save all dashboard config setting
    */
    public function save_config_setting($data, $id)
    {
        /*var_dump($data);
        var_dump($id);die;*/

        $to_do_list = $data['widget_type'];
        unset($data['widget_type']);

        $quick_link_types = $data['quick_link_type'];
        unset($data['quick_link_type']);

        $dashboard_data['widget_type'] = implode(",", $to_do_list);
        $dashboard_data['quick_link_type'] = implode(",", $quick_link_types);
        $dashboard_data['brandid'] = get_user_session();
        $dashboard_data['dateadded'] = date('Y-m-d H:i:s');
        $dashboard_data['addedby'] = $this->session->userdata['staff_user_id'];
        $dashboard_data['dateupdated'] = date('Y-m-d H:i:s');
        $dashboard_data['updatedby'] = $this->session->userdata['staff_user_id'];
        $this->db->where('staffid', $id);
        $this->db->update('tbldashboard_settings', $dashboard_data);
    }


    function update_widget_order($data)
    {
        $bulk_setting['order'] = $data;
        $this->db->where('deleted', '0');
        $this->db->where('staffid', get_staff_user_id());
        $this->db->update('tbldashboard_settings', $bulk_setting);
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function defaultBrand($brandid)
    {
        $this->db->where('staffid', get_staff_user_id());
        $this->db->update('tblstaffbrand', array('isdefault' => 0));

        $this->db->where('brandid', $brandid);
        $this->db->where('staffid', get_staff_user_id());
        $this->db->update('tblstaffbrand', array('isdefault' => 1));
        if ($this->db->affected_rows() > 0) {
            return 1;
        } else {
            return 0;
        }

    }
    function updatenotification($page,$brandid)
    {
        $this->db->where('touserid', get_staff_user_id());
        $this->db->where('not_type', $page);
        $this->db->like('description', 'not_new','after');
        $this->db->update('tblnotifications', array(
            'isread' => 1,
            'isread_inline' => 1
        ));
        if ($this->db->affected_rows() > 0) {
            return 1;
        } else {
            return 0;
        }

    }

}
