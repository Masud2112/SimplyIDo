<?php
/**
 * Added By: Vaidehi
 * Dt: 10/03/2017
 * for handling client/account registration
 */
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Register_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('phpass');
        $this->load->model('Authentication_model');
        $this->load->model('Addressbooks_model');
        $this->load->model('Packages_model');
        $this->load->model('Venues_model');
    }

    //get all active brandtypes
    function get_brandtypes()
    {
        $this->db->order_by('name');
        $brandTypes = $this->db->get('tblbrandtype')->result_array();
        return $brandTypes;
    }

    //get all active packages
    function get_packages()
    {
        $where = array('deleted = ' => 0, 'status = ' => 1);

        return $this->db->where($where)->get('tblpackages')->result_array();
    }

    //save entry in db
    function saveclient($data, $page = "", $role = "account-owner")
    {
        $paymentmode = isset($data['paymentmode'])?$data['paymentmode']:"";
        unset($data['paymentmode']);
        //make entry in tblstaff table
        $staffdata = array();
        $staffdata['email'] = $data['email'];
        $staffdata['firstname'] = $data['firstname'];
        $staffdata['lastname'] = $data['lastname'];
        $staffdata['facebook'] = $data['facebook'];
        $staffdata['twitter'] = $data['twitter'];
        $staffdata['google'] = $data['google'];

        if (isset($data['password']) || $data['password'] != "") {
            $staffdata['random_pass'] = $data['password'];

            $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
            $staffdata['password'] = $hasher->HashPassword($data['password']);
        }

        $staffdata['admin'] = 0;
        $staffdata['active'] = (isset($data['active']) ? $data['active'] : 1);
        $staffdata['deleted'] = 0;
        $staffdata['user_type'] = 1;
        if (isset($data['user_type'])) {
            $staffdata['user_type'] = $data['user_type'];
        }
        if (isset($data['is_not_staff'])) {
            $staffdata['is_not_staff'] = $data['is_not_staff'];
        }
        $staffdata['is_sido_admin'] = 0;

        /**
         * Added By : Vaidehi
         * Dt : 11/23/2017
         * to add random color for user profile
         */
        $this->db->select('color');
        $this->db->from('tblcolors');
        $this->db->order_by('rand()');
        $this->db->limit(1);
        $staffcolor = $this->db->get()->row();
        $staffdata['profilecolor'] = $staffcolor->color;

        $staffdata['created_by'] = 0;
        $staffdata['datecreated'] = date('Y-m-d H:i:s');
        $this->db->insert('tblstaff', $staffdata);
        $staffid = $this->db->insert_id();

        //make entry in tblclients table
        $clientdata = array();
        $clientdata['company'] = $data['brandname'];
        $clientdata['country'] = 236;
        $clientdata['city'] = (isset($data['city']) ? $data['city'] : '');
        $clientdata['zip'] = (isset($data['zipcode']) ? $data['zipcode'] : '');
        $clientdata['state'] = (isset($data['state']) ? $data['state'] : '');
        $clientdata['address'] = (isset($data['address']) ? $data['address'] : '');
        $clientdata['billing_street'] = (isset($data['address']) ? $data['address'] : '');
        $clientdata['billing_city'] = (isset($data['city']) ? $data['city'] : '');
        $clientdata['billing_state'] = (isset($data['state']) ? $data['state'] : '');
        $clientdata['billing_zip'] = (isset($data['zipcode']) ? $data['zipcode'] : '');
        $clientdata['billing_country'] = 236;
        $clientdata['packageid'] = $data['packagetype'];
        $clientdata['primary_user_id'] = $staffid;
        $clientdata['active'] = 1;
        $clientdata['is_deleted'] = 0;
        $clientdata['datecreated'] = date('Y-m-d H:i:s');

        $this->db->insert('tblclients', $clientdata);
        $clientid = $this->db->insert_id();
        //make entry in tblbrand table
        if (is_serialized($data['brandtype'])) {
            $brandtypes = $data['brandtype'];
            unset($data['brandtype']);
        }
        $branddata = array();
        $branddata['name'] = $data['brandname'];
        $branddata['brandtypeid'] = isset($data['brandtype']) ? $data['brandtype'] : 0;
        $branddata['userid'] = $clientid;
        $branddata['billing_street'] = (isset($data['address']) ? $data['address'] : '');
        $branddata['billing_city'] = (isset($data['city']) ? $data['city'] : '');
        $branddata['billing_state'] = (isset($data['state']) ? $data['state'] : '');
        $branddata['billing_zip'] = (isset($data['zipcode']) ? $data['zipcode'] : '');
        $branddata['billing_country'] = 236;
        $branddata['deleted'] = 0;
        $branddata['created_by'] = $clientid;
        $branddata['datecreated'] = date('Y-m-d H:i:s');

        $this->db->insert('tblbrand', $branddata);
        $brandid = $this->db->insert_id();

        //make entry in tblstaffbrand table
        $staffbranddata = array();
        $staffbranddata['staffid'] = $staffid;
        $staffbranddata['brandid'] = $brandid;
        $staffbranddata['isdefault'] = 1;

        $this->db->insert('tblstaffbrand', $staffbranddata);
        $staffbrandid = $this->db->insert_id();

        //get role id of "Admin" role created by sido admin
        /*$where = array('name = ' => 'Account Owner', 'brandid = ' => 0, 'deleted = ' => 0);

        $roleiddata = $this->db->where($where)->get('tblroles')->row();*/
        $roleiddata = get_role($role);
        //make entry in tblroleuserteam table
        $roledata = array();
        $roledata['role_id'] = $roleiddata;

        $roledata['user_id'] = $staffid;
        /*echo $role;
        echo "<pre>";
        print_r($roledata);
        die('<--here');*/
        $this->db->insert('tblroleuserteam', $roledata);
        $roleuserteamid = $this->db->insert_id();

        //get all taxes created by sido admin
        //$where = array('brandid = ' => 0, 'deleted = ' => 0);

        //$alltaxes = $this->db->where($where)->get('tbltaxes')->result();

        /*foreach ($alltaxes as $taxes) {
            //make entry of all taxes for newly created account in tbltaxes with client id
            $taxdata = array();
            $taxdata['name'] = $taxes->name;
            $taxdata['taxrate'] = $taxes->taxrate;
            $taxdata['brandid'] = $brandid;
            $taxdata['deleted'] = 0;
            $taxdata['created_by'] = $clientid;
            $taxdata['datecreated'] = date('Y-m-d H:i:s');

            $this->db->insert('tbltaxes', $taxdata);
            $taxid = $this->db->insert_id();
        }*/

        //get all tags created by sido admin
        $where = array('brandid = ' => 0, 'deleted = ' => 0);

        $alltags = $this->db->where($where)->get('tbltags')->result();

        foreach ($alltags as $tags) {
            //make entry of all tags for newly created account in tbltags with client id
            $tagdata = array();
            $tagdata['name'] = $tags->name;
            $tagdata['color'] = $tags->color;
            $tagdata['brandid'] = $brandid;
            $tagdata['deleted'] = 0;
            $tagdata['created_by'] = $clientid;
            $tagdata['datecreated'] = date('Y-m-d H:i:s');

            $this->db->insert('tbltags', $tagdata);
            $tagid = $this->db->insert_id();
        }

        //get all lead sources created by sido admin
        $where = array('brandid = ' => 0, 'deleted = ' => 0);

        $allsources = $this->db->where($where)->get('tblleadssources')->result();

        foreach ($allsources as $source) {
            //make entry of all lead sources for newly created account in tbltags with client id
            $sourcedata = array();
            $sourcedata['name'] = $source->name;
            $sourcedata['brandid'] = $brandid;
            $sourcedata['deleted'] = 0;

            $this->db->insert('tblleadssources', $sourcedata);
            $sourceid = $this->db->insert_id();
        }

        //get all lead status created by sido admin
        $where = array('brandid = ' => 0, 'deleted = ' => 0);

        $allstatuses = $this->db->where($where)->get('tblleadsstatus')->result();

        foreach ($allstatuses as $status) {
            //make entry of all lead statuses for newly created account with client id
            $statusdata = array();
            $statusdata['name'] = $status->name;
            $statusdata['statusorder'] = $status->statusorder;
            $statusdata['color'] = $status->color;
            $statusdata['isdefault'] = $status->isdefault;
            $statusdata['isdeleteable'] = $status->isdeleteable;
            $statusdata['brandid'] = $brandid;
            $statusdata['deleted'] = 0;

            $this->db->insert('tblleadsstatus', $statusdata);
            $statusid = $this->db->insert_id();
        }
        /**
         * Added By : Vaidehi
         * Dt : 11/10/2017
         * get all task status created by sido admin
         */
        $where = array('brandid = ' => 0, 'deleted = ' => 0);

        $alltasksstatus = $this->db->where($where)->get('tbltasksstatus')->result();

        foreach ($alltasksstatus as $tasksstatus) {
            //make entry of all task status for newly created account in tbltaskstatus with client id
            $tasksstatusdata = array();
            $tasksstatusdata['name'] = $tasksstatus->name;
            $tasksstatusdata['statusorder'] = $tasksstatus->statusorder;
            $tasksstatusdata['color'] = $tasksstatus->color;
            $tasksstatusdata['isdefault'] = $tasksstatus->isdefault;
            $tasksstatusdata['brandid'] = $brandid;
            $tasksstatusdata['deleted'] = 0;

            $this->db->insert('tbltasksstatus', $tasksstatusdata);
            $tasksstatusid = $this->db->insert_id();
        }
        //get all options created by sido admin
        //$visible_array = array('companyname', 'company_logo', 'favicon', 'main_domain', 'rtl_support_client', 'rtl_support_admin', 'allowed_files', 'invoice_company_name', 'invoice_company_address', 'invoice_company_city', 'company_state', 'invoice_company_country_code', 'invoice_company_postal_code', 'invoice_company_phonenumber', 'company_vat', 'company_info_format', 'dateformat', 'time_format', 'default_timezone', 'active_language', 'disable_language', 'output_client_pdfs_from_admin_area_in_client_language', 'email_protocol', 'smtp_email', 'smtp_password', 'smtp_port', 'smtp_host', 'smtp_email_charset', 'smtp_encryption', 'smtp_username', 'email_signature', 'email_header', 'email_footer', 'decimal_separator', 'thousand_separator', 'number_padding_prefixes', 'show_tax_per_item', 'remove_tax_name_from_item_table', 'remove_decimals_on_zero', 'currency_placement', 'default_tax', 'total_to_words_lowercase', 'total_to_words_enabled', 'invoice_prefix', 'next_invoice_number', 'next_proposal_number', 'invoice_due_after', 'view_invoice_only_logged_in', 'delete_only_on_last_invoice', 'invoice_number_decrement_on_delete', 'exclude_invoice_from_client_area_with_draft_status', 'show_sale_agent_on_invoices', 'invoice_number_format', 'predefined_clientnote_invoice', 'predefined_terms_invoice', 'estimate_prefix', 'next_estimate_number', 'delete_only_on_last_estimate', 'estimate_number_decrement_on_delete', 'estimate_due_after', 'view_estimate_only_logged_in', 'show_sale_agent_on_estimates', 'estimate_auto_convert_to_invoice_on_client_accept', 'exclude_estimate_from_client_area_with_draft_status', 'estimate_number_format', 'estimates_pipeline_limit', 'default_estimates_pipeline_sort', 'predefined_clientnote_estimate', 'predefined_terms_estimate', 'proposal_number_prefix', 'proposal_due_after', 'proposals_pipeline_limit', 'default_proposals_pipeline_sort', 'exclude_proposal_from_client_area_with_draft_status', 'allow_staff_view_proposals_assigned', 'paymentmethod_stripe_active', 'paymentmethod_stripe_label', 'paymentmethod_stripe_description_dashboard', 'paymentmethod_stripe_currencies', 'paymentmethod_stripe_test_mode_enabled', 'paymentmethod_stripe_default_selected', 'paymentmethod_stripe_initialized', 'paymentmethod_paypal_active', 'paymentmethod_paypal_label', 'paymentmethod_paypal_username', 'paymentmethod_paypal_password', 'paymentmethod_paypal_signature', 'paymentmethod_paypal_description_dashboard', 'paymentmethod_paypal_currencies', 'paymentmethod_paypal_test_mode_enabled', 'paymentmethod_paypal_default_selected', 'paymentmethod_paypal_initialized', 'clients_default_theme', 'default_view_calendar', 'calendar_first_day', 'show_invoices_on_calendar', 'show_estimates_on_calendar', 'show_proposals_on_calendar', 'show_contracts_on_calendar', 'show_tasks_on_calendar', 'show_meetings_on_calendar', 'show_lead_on_calendar', 'show_projects_on_calendar', 'show_lead_reminders_on_calendar', 'show_customer_reminders_on_calendar', 'show_estimate_reminders_on_calendar', 'show_invoice_reminders_on_calendar', 'show_proposal_reminders_on_calendar', 'show_expense_reminders_on_calendar', 'calendar_invoice_color', 'calendar_estimate_color', 'calendar_proposal_color', 'calendar_reminder_color', 'calendar_contract_color', 'calendar_project_color', 'invoice_auto_operations_hour', 'cron_send_invoice_overdue_reminder', 'automatically_send_invoice_overdue_reminder_after', 'automatically_resend_invoice_overdue_reminder_after', 'create_invoice_from_recurring_only_on_paid_invoices', 'send_renewed_invoice_from_recurring_to_email', 'estimate_expiry_reminder_enabled', 'send_estimate_expiry_reminder_before', 'proposal_expiry_reminder_enabled', 'send_proposal_expiry_reminder_before', 'expenses_auto_operations_hour', 'contract_expiry_reminder_enabled', 'contract_expiration_before', 'tasks_reminder_notification_before', 'banner', 'theme_style', 'filter_tags','brandtype');
        $visible_array = array('companyname', 'company_logo', 'favicon', 'main_domain', 'rtl_support_client', 'rtl_support_admin', 'allowed_files', 'invoice_company_name', 'invoice_company_address', 'invoice_company_city', 'company_state', 'invoice_company_country_code', 'invoice_company_postal_code', 'invoice_company_phonenumber', 'company_vat', 'company_info_format', 'dateformat', 'time_format', 'default_timezone', 'active_language', 'disable_language', 'output_client_pdfs_from_admin_area_in_client_language', 'email_protocol', 'smtp_email', 'smtp_password', 'smtp_port', 'smtp_host', 'smtp_email_charset', 'smtp_encryption', 'smtp_username', 'email_signature', 'email_header', 'email_footer', 'decimal_separator', 'thousand_separator', 'number_padding_prefixes', 'show_tax_per_item', 'remove_tax_name_from_item_table', 'remove_decimals_on_zero', 'currency_placement', 'default_tax', 'total_to_words_lowercase', 'total_to_words_enabled', 'invoice_prefix', 'next_invoice_number', 'invoice_due_after', 'view_invoice_only_logged_in', 'delete_only_on_last_invoice', 'invoice_number_decrement_on_delete', 'exclude_invoice_from_client_area_with_draft_status', 'show_sale_agent_on_invoices', 'invoice_number_format', 'predefined_clientnote_invoice', 'predefined_terms_invoice', 'estimate_prefix', 'next_estimate_number', 'delete_only_on_last_estimate', 'estimate_number_decrement_on_delete', 'estimate_due_after', 'view_estimate_only_logged_in', 'show_sale_agent_on_estimates', 'estimate_auto_convert_to_invoice_on_client_accept', 'exclude_estimate_from_client_area_with_draft_status', 'estimate_number_format', 'estimates_pipeline_limit', 'default_estimates_pipeline_sort', 'predefined_clientnote_estimate', 'predefined_terms_estimate', 'proposal_number_prefix', 'proposal_due_after', 'proposals_pipeline_limit', 'default_proposals_pipeline_sort', 'exclude_proposal_from_client_area_with_draft_status', 'allow_staff_view_proposals_assigned', 'paymentmethod_stripe_active', 'paymentmethod_stripe_label', 'paymentmethod_stripe_api_secret_key', 'paymentmethod_stripe_api_publishable_key', 'paymentmethod_stripe_description_dashboard', 'paymentmethod_stripe_currencies', 'paymentmethod_stripe_test_mode_enabled', 'paymentmethod_stripe_default_selected', 'paymentmethod_stripe_initialized', 'paymentmethod_paypal_active', 'paymentmethod_paypal_label', 'paymentmethod_paypal_username', 'paymentmethod_paypal_password', 'paymentmethod_paypal_signature', 'paymentmethod_paypal_description_dashboard', 'paymentmethod_paypal_currencies', 'paymentmethod_paypal_test_mode_enabled', 'paymentmethod_paypal_default_selected', 'paymentmethod_paypal_initialized', 'clients_default_theme', 'default_view_calendar', 'calendar_first_day', 'show_invoices_on_calendar', 'show_estimates_on_calendar', 'show_proposals_on_calendar', 'show_contracts_on_calendar', 'show_tasks_on_calendar', 'show_meetings_on_calendar', 'show_lead_on_calendar', 'show_projects_on_calendar', 'show_lead_reminders_on_calendar', 'show_customer_reminders_on_calendar', 'show_estimate_reminders_on_calendar', 'show_invoice_reminders_on_calendar', 'show_proposal_reminders_on_calendar', 'show_expense_reminders_on_calendar', 'calendar_invoice_color', 'calendar_estimate_color', 'calendar_proposal_color', 'calendar_reminder_color', 'calendar_contract_color', 'calendar_project_color', 'invoice_auto_operations_hour', 'cron_send_invoice_overdue_reminder', 'automatically_send_invoice_overdue_reminder_after', 'automatically_resend_invoice_overdue_reminder_after', 'create_invoice_from_recurring_only_on_paid_invoices', 'send_renewed_invoice_from_recurring_to_email', 'estimate_expiry_reminder_enabled', 'send_estimate_expiry_reminder_before', 'proposal_expiry_reminder_enabled', 'send_proposal_expiry_reminder_before', 'expenses_auto_operations_hour', 'contract_expiry_reminder_enabled', 'contract_expiration_before', 'tasks_reminder_notification_before', 'banner', 'theme_style', 'filter_tags', 'brandtype');

        $alloptions = $this->db->get('tbloptions')->result();
        foreach ($alloptions as $option) {
            if (in_array($option->name, $visible_array)) {
                $is_visible = 1;
            } else {
                $is_visible = 0;
            }

            //make entry of all options in tblbrandsettings with client id
            $optiondata = array();

            $optiondata['name'] = $option->name;

            if ($option->name == 'companyname') {
                $optiondata['value'] = $data['brandname'];
            } elseif ($option->name == 'company_logo') {
                $ext = explode(".", $option->value);
                $newlogoname = "logo-" . $brandid . "-" . strtotime(date('Y-m-d H:i:s')) . "." . $ext[1];
                $optiondata['value'] = $newlogoname;
            } elseif ($option->name == 'favicon') {
                $ext = explode(".", $option->value);
                $newfaviconname = "favicon-" . $brandid . "-" . strtotime(date('Y-m-d H:i:s')) . "." . $ext[1];
                $optiondata['value'] = $newfaviconname;
            } elseif ($option->name == 'banner') {
                $ext = explode(".", $option->value);
                $newbannername = "banner-" . $brandid . "-" . strtotime(date('Y-m-d H:i:s')) . "." . $ext[1];
                $optiondata['value'] = $newbannername;
            } elseif ($option->name == 'invoice_company_name') {
                $optiondata['value'] = $data['brandname'];
            } elseif ($option->name == 'invoice_company_address') {
                $optiondata['value'] = (isset($data['address']) ? $data['address'] : '');
            } elseif ($option->name == 'invoice_company_city') {
                $optiondata['value'] = (isset($data['city']) ? $data['city'] : '');
            } elseif ($option->name == 'company_state') {
                $optiondata['value'] = (isset($data['state']) ? $data['state'] : '');
            } elseif ($option->name == 'invoice_company_postal_code') {
                $optiondata['value'] = (isset($data['zipcode']) ? $data['zipcode'] : '');;
            } elseif ($option->name == 'company_info_format') {
                $optiondata['value'] = '{company_name}<br />
	{address} {city} {state}<br />
	{zip_code}';
            } elseif ($option->name == "paymentmethod_stripe_api_publishable_key") {
                $optiondata['value'] = "";
            } elseif ($option->name == "paymentmethod_stripe_api_secret_key") {
                $optiondata['value'] = "";
            } else {
                $optiondata['value'] = $option->value;
            }

            if ($option->name == 'company_logo') {
                if (file_exists(COMPANY_FILES_FOLDER . $option->value)) {
                    copy(COMPANY_FILES_FOLDER . $option->value, BRAND_IMAGES_FOLDER . $newlogoname);
                }
            }

            if ($option->name == 'favicon') {
                if (file_exists(COMPANY_FILES_FOLDER . $option->value)) {
                    copy(COMPANY_FILES_FOLDER . $option->value, BRAND_IMAGES_FOLDER . $newfaviconname);
                }
            }

            if ($option->name == 'banner') {
                if (file_exists(COMPANY_FILES_FOLDER . $option->value)) {
                    copy(COMPANY_FILES_FOLDER . $option->value, BRAND_IMAGES_FOLDER . $newbannername);
                }
            }

            $optiondata['brandid'] = $brandid;
            $optiondata['isvisible'] = $is_visible;
            $optiondata['created_by'] = $clientid;
            $optiondata['datecreated'] = date('Y-m-d H:i:s');

            $this->db->insert('tblbrandsettings', $optiondata);
            $optionid = $this->db->insert_id();
        }
        if (isset($brandtypes) && $brandtypes != "") {
            $optiondata = array();
            $optiondata['name'] = "brandtypes";
            $optiondata['value'] = $brandtypes;
            $optiondata['brandid'] = $brandid;
            $optiondata['isvisible'] = 1;
            $optiondata['created_by'] = $clientid;
            $optiondata['datecreated'] = date('Y-m-d H:i:s');
            $this->db->insert('tblbrandsettings', $optiondata);
        }
        /**
         * Added By : Vaidehi
         * Dt : 12/04/2017
         * get all email templates created by sido admin
         */
        $where = array('brandid = ' => 0, 'language' => 'english');

        $allemailtemplates = $this->db->where($where)->get('tblemailtemplates')->result();

        foreach ($allemailtemplates as $emailtemplates) {
            //make entry of all email templates for newly created account in tblemailtemplates with client id
            $emailtemplatedata = array();
            $emailtemplatedata['type'] = $emailtemplates->type;
            $emailtemplatedata['slug'] = $emailtemplates->slug;
            $emailtemplatedata['language'] = $emailtemplates->language;
            $emailtemplatedata['name'] = $emailtemplates->name;
            $emailtemplatedata['subject'] = $emailtemplates->subject;
            $emailtemplatedata['message'] = $emailtemplates->message;
            $emailtemplatedata['fromname'] = $emailtemplates->fromname;
            $emailtemplatedata['fromemail'] = $emailtemplates->fromemail;
            $emailtemplatedata['plaintext'] = $emailtemplates->plaintext;
            $emailtemplatedata['active'] = $emailtemplates->active;
            $emailtemplatedata['order'] = $emailtemplates->order;
            $emailtemplatedata['brandid'] = $brandid;
            $emailtemplatedata['created_by'] = $staffid;
            $emailtemplatedata['datecreated'] = date('Y-m-d H:i:s');

            $this->db->insert('tblemailtemplates', $emailtemplatedata);
            $emailtemplateid = $this->db->insert_id();
        }

        /**
         * Added By : Vaidehi
         * Dt : 12/06/2017
         * get all agreement templates created by sido admin
         */
        $where = array('brandid = ' => 0, 'deleted' => 0);

        $allagreementtemplates = $this->db->where($where)->get('tblagreementtemplates')->result();

        foreach ($allagreementtemplates as $agreementtemplates) {
            //make entry of all agreement templates for newly created account in tblagreementtemplates with client id
            $agreementtemplatedata = array();
            $agreementtemplatedata['name'] = $agreementtemplates->name;
            $agreementtemplatedata['content'] = $agreementtemplates->content;
            $agreementtemplatedata['brandid'] = $brandid;
            $agreementtemplatedata['created_by'] = $staffid;
            $agreementtemplatedata['datecreated'] = date('Y-m-d H:i:s');

            $this->db->insert('tblagreementtemplates', $emailtemplatedata);
            $agreementtemplateid = $this->db->insert_id();
        }

        /**
         * Added By : Vaidehi
         * Dt : 12/18/2017
         * get all payment schedules templates created by sido admin
         */
        $where = array('brandid = ' => 0, 'deleted' => 0);

        $allpayscheduletemplates = $this->db->where($where)->get('tblpaymenttemplates')->result();

        foreach ($allpayscheduletemplates as $payscheduletemplates) {
            //make entry of all payment schedule templates for newly created account in tblpaymenttemplates with client id
            $payscheduletemplatedata = array();
            $payscheduletemplatedata['name'] = $payscheduletemplates->name;
            $payscheduletemplatedata['brandid'] = $brandid;
            $payscheduletemplatedata['created_by'] = $staffid;
            $payscheduletemplatedata['datecreated'] = date('Y-m-d H:i:s');

            $this->db->insert('tblpaymenttemplates', $payscheduletemplatedata);
            $payscheduletemplateid = $this->db->insert_id();

            $detail_where = array('paymentscheduleid = ' => $payscheduletemplateid);

            $alldetailscheduletemplates = $this->db->where($detail_where)->get('tblpaymenttemplatedetails')->result();

            foreach ($alldetailscheduletemplates as $detailscheduletemplates) {
                $detailscheduletemplatedata = array();
                $detailscheduletemplatedata['paymentscheduleid'] = $payscheduletemplateid;
                $detailscheduletemplatedata['duedate_type'] = $detailscheduletemplates->duedate_type;
                $detailscheduletemplatedata['duedate_number'] = $detailscheduletemplates->duedate_number;
                $detailscheduletemplatedata['custom_range_duration'] = $detailscheduletemplates->custom_range_duration;
                $detailscheduletemplatedata['duedate_criteria'] = $detailscheduletemplates->duedate_criteria;
                $detailscheduletemplatedata['price_type'] = $detailscheduletemplates->price_type;
                $detailscheduletemplatedata['price_amount'] = $detailscheduletemplates->price_amount;
                $detailscheduletemplatedata['price_percentage'] = $detailscheduletemplates->price_percentage;

                $this->db->insert('tblpaymenttemplatedetails', $detailscheduletemplatedata);
                $paydetailscheduletemplateid = $this->db->insert_id();
            }
        }

        /**
         * Added By : Vaidehi
         * Dt : 12/18/2017
         * get all project status created by sido admin
         */
        $where = array('brandid = ' => 0, 'deleted = ' => 0);

        $allstatuses = $this->db->where($where)->get('tblprojectstatus')->result();

        foreach ($allstatuses as $status) {
            //make entry of all project statuses for newly created account with client id
            $statusdata = array();
            $statusdata['name'] = $status->name;
            $statusdata['statusorder'] = $status->statusorder;
            $statusdata['color'] = $status->color;
            $statusdata['isdefault'] = $status->isdefault;
            $statusdata['brandid'] = $brandid;
            $statusdata['deleted'] = 0;

            $this->db->insert('tblprojectstatus', $statusdata);
            $statusid = $this->db->insert_id();
        }

        /**
         * Added By : Vaidehi
         * Dt : 02/08/2017
         * get all income category created by sido admin
         */
        $where = array('brandid = ' => 0, 'deleted = ' => 0);

        $allincome_category = $this->db->where($where)->get('tblincome_category')->result();

        foreach ($allincome_category as $income_category) {
            //make entry of all income category for newly created account in tblincome_category with client id
            $income_categorydata = array();
            $income_categorydata['name'] = $income_category->name;
            $income_categorydata['addedby'] = $staffid;
            $income_categorydata['dateadded'] = date('Y-m-d H:i:s');
            $income_categorydata['brandid'] = $brandid;
            $income_categorydata['deleted'] = 0;

            $this->db->insert('tblincome_category', $income_categorydata);
            $tasksstatusid = $this->db->insert_id();
        }

        /**
         * Added By : Vaidehi
         * Dt : 02/08/2017
         * get all expense category created by sido admin
         */
        $where = array('brandid = ' => 0, 'deleted = ' => 0);

        $allexpense_category = $this->db->where($where)->get('tblexpense_category')->result();

        foreach ($allexpense_category as $expense_category) {
            //make entry of all expense category for newly created account in tblexpense_category with client id
            $expense_categorydata = array();
            $expense_categorydata['name'] = $expense_category->name;
            $expense_categorydata['addedby'] = $staffid;
            $expense_categorydata['dateadded'] = date('Y-m-d H:i:s');
            $expense_categorydata['brandid'] = $brandid;
            $expense_categorydata['deleted'] = 0;

            $this->db->insert('tblexpense_category', $expense_categorydata);
            $tasksstatusid = $this->db->insert_id();
        }

        /**
         * Added By : Vaidehi
         * Dt : 03/05/2018
         * prefill dashboard values
         */
        $dashboard_data = array();
        $dashboard_data['staffid'] = $staffid;
        $dashboard_data['widget_type'] = 'upcoming_project,pinned_item,calendar,weather,favourite,quick_link,lead_pipeline,message,getting_started,task_list,contacts,messages';
        $dashboard_data['quick_link_type'] = 'lead,project,message,task_due,meeting,amount_receivable,amount_received,invite';
        $dashboard_data['order'] = '[{"widget_name":"getting_started","order":0},{"widget_name":"lead_pipeline","order":1},{"widget_name":"calendar","order":2},{"widget_name":"pinned_item","order":3},{"widget_name":"quick_link","order":4},{"widget_name":"upcoming_project","order":5},{"widget_name":"contacts","order":6},{"widget_name":"messages","order":7},{"widget_name":"task_list","order":8},{"widget_name":"mybrands","order":9}]';
        $dashboard_data['is_visible'] = 1;
        $dashboard_data['brandid'] = $brandid;
        $dashboard_data['dateadded'] = date('Y-m-d H:i:s');
        $dashboard_data['addedby'] = $staffid;
        $this->db->insert('  tbldashboard_settings', $dashboard_data);

        /*
        ** Added By vaidehi on 03/21/2018
        ** for report configuration
        */
        $report_data['report_name'] = 'Booking Success';
        $report_data['is_visible'] = 1;
        $report_data['report_order'] = 0;
        $report_data['default_records'] = 5;
        $report_data['saved_filter'] = 'all';
        $report_data['staff_user_id'] = $staffid;
        $report_data['brandid'] = $brandid;
        $report_data['createdby'] = $staffid;
        $report_data['datecreated'] = date('Y-m-d H:i:s');

        $this->db->insert('tblreportconfiguration', $report_data);

        $report_data['report_name'] = 'Lead Source';
        $report_data['is_visible'] = 1;
        $report_data['report_order'] = 1;
        $report_data['default_records'] = 5;
        $report_data['saved_filter'] = 'all';
        $report_data['staff_user_id'] = $staffid;
        $report_data['brandid'] = $brandid;
        $report_data['createdby'] = $staffid;
        $report_data['datecreated'] = date('Y-m-d H:i:s');

        $this->db->insert('tblreportconfiguration', $report_data);

        $report_data['report_name'] = 'Revenue';
        $report_data['is_visible'] = 1;
        $report_data['report_order'] = 2;
        $report_data['default_records'] = 5;
        $report_data['saved_filter'] = 'all';
        $report_data['staff_user_id'] = $staffid;
        $report_data['brandid'] = $brandid;
        $report_data['createdby'] = $staffid;
        $report_data['datecreated'] = date('Y-m-d H:i:s');

        $this->db->insert('tblreportconfiguration', $report_data);

        /**
         * Added By : Vaidehi
         * Dt : 03/21/2017
         * get all event type created by sido admin
         */
        $where = array('brandid = ' => 0, 'deleted = ' => 0);

        $eventtypes = $this->db->where($where)->get('tbleventtype')->result();

        foreach ($eventtypes as $eventtype) {
            //make entry of all event type for newly created account in tbleventtype with client id
            $eventtypedata = array();
            $eventtypedata['eventtypename'] = $eventtype->eventtypename;
            $eventtypedata['createdby'] = $staffid;
            $eventtypedata['datecreated'] = date('Y-m-d H:i:s');
            $eventtypedata['brandid'] = $brandid;
            $eventtypedata['deleted'] = 0;

            $this->db->insert('tbleventtype', $eventtypedata);
            $eventtypeid = $this->db->insert_id();
        }

        /**
         * Added By: Vaidehi
         * Dt: 03/23/2018
         * get all venues
         */
        $this->db->where('deleted', 0);
        $this->db->where('isapproved', 1);
        $this->db->where('active', 1);
        $get_venues = $this->db->get('tblvenue')->result_array();

        $this->db->where('deleted', 0);
        $get_brand = $this->db->get('tblbrand')->result_array();

        foreach ($get_brand as $brand) {
            foreach ($get_venues as $get_venue) {
                $venue_exists = $this->venues_model->check_brand_venue_exists($brand['brandid'], $get_venue['venueid']);

                if (empty($venue_exists->brandvenueid)) {
                    $venuebrand = [];
                    $venuebrand['brandid'] = $brand['brandid'];
                    $venuebrand['venueid'] = $get_venue['venueid'];
                    $venuebrand['deleted'] = 0;

                    $this->db->insert('tblbrandvenue', $venuebrand);
                }
            }
        }

        /**
         * Added By: Vaidehi
         * Dt: 03/23/2018
         * get all addressbook
         */
        /*$this->db->where('deleted', 0);
        $this->db->where('ispublic', 1);
        $get_addressbooks = $this->db->get('tbladdressbook')->result_array();

        $this->db->where('deleted', 0);
        $get_brand = $this->db->get('tblbrand')->result_array();

        foreach ($get_brand as $brand) {
            foreach ($get_addressbooks as $get_addressbook) {
                $addressbook_exists = $this->addressbooks_model->check_brand_addressbook_exists($brand['brandid'], $get_addressbook['addressbookid']);

                if(empty($addressbook_exists->id)) {
                    $addressbookbrand = [];
                    $addressbookbrand['brandid'] 		= $brand['brandid'];
                    $addressbookbrand['addressbookid'] 	= $get_addressbook['addressbookid'];
                    $addressbookbrand['deleted'] 		= 0;

                    $this->db->insert('tbladdressbook_client', $addressbookbrand);
                }
            }
        }*/

        $package = $this->packages_model->get($data['packagetype']);

        $payment_data = array();
        $amount = $package->price;
        $packageid = $data['packagetype'];
        $payment_data = array();
        $payment_data['paymentmode'] = $paymentmode;
        $payment_data['amount'] = $amount;
        $payment_data['packageid'] = $data['packagetype'];
        $payment_data['brand_id'] = $brandid;
        $payment_data['new_user_id'] = $staffid;
        if (!empty($page) && $page == "invite") {
            $payment_data['page'] = $page;
        }
        $this->packages_model->process_payment($payment_data, $data['packagetype']);
        $response = $clientid;
        return $response;
    }

    function check_brand_exists($servicename)
    {
        $where = array('name = ' => $servicename, 'deleted' => 0);

        return $this->db->where($where)->get('tblbrand')->row();
    }

    function check_account_exists($email)
    {
        $where = array('email = ' => $email, 'deleted = ' => 0);

        return $this->db->where($where)->get('tblstaff')->row();
    }

    function do_staff_login($email, $password, $remember, $staff)
    {
        $table = 'tblcontacts';
        $_id = 'id';
        if ($staff == true) {
            $table = 'tblstaff';
            $_id = 'staffid';
        }
        $this->db->where('email', $email);
        $user = $this->db->get($table)->row();

        if ($user->active == 0) {
            logActivity('Inactive User Tried to Login [Email:' . $email . ', Is Staff Member:' . ($staff == true ? 'Yes' : 'No') . ', IP:' . $this->input->ip_address() . ']');

            return array(
                'memberinactive' => true
            );
        }

        $twoFactorAuth = false;
        if ($staff == true) {
            $twoFactorAuth = $user->two_factor_auth_enabled == 0 ? false : true;
            $this->db->where('userid', $user->clientid);
            $client_data = $this->db->get('tblclients')->row();
            if (!$twoFactorAuth) {
                do_action('before_staff_login', array(
                    'email' => $email,
                    'userid' => $user->$_id
                ));
                $user_data = array(
                    'staff_user_id' => $user->$_id,
                    'staff_logged_in' => true,
                    'account_id' => $user->clientid,
                    'package_id' => $client_data->packageid,
                    'is_sido_admin' => $user->is_sido_admin
                );
            } else {
                $user_data = array();
                if ($remember) {
                    $user_data['tfa_remember'] = true;
                }
            }
        } else {
            do_action('before_client_login', array(
                'email' => $email,
                'userid' => $user->userid,
                'contact_user_id' => $user->$_id
            ));

            $user_data = array(
                'client_user_id' => $user->userid,
                'contact_user_id' => $user->$_id,
                'client_logged_in' => true
            );
        }

        $this->session->set_userdata($user_data);

        if (!$twoFactorAuth) {
            if ($remember) {
                $this->create_autologin($user->$_id, $staff);
            }

            $this->update_login_info($user->$_id, $staff);
        } else {
            return array('two_factor_auth' => true, 'user' => $user);
        }

        return true;
    }

    /**
     * Added By : Vaidehi
     * Dt : 11/08/2017
     * get module restriction based on package of logged in user
     */
    function get_module_restriction_by_packageid($search)
    {
        $this->db->select('tblpackagepermissions.restriction');

        $this->db->join('tblpermissions', 'tblpackagepermissions.permissionid = tblpermissions.permissionid');
        $this->db->where('tblpackagepermissions.packageid', $search['packageid']);
        $this->db->where('tblpackagepermissions.can_access', 1);
        $this->db->where('tblpermissions.shortname', $search['modulename']);

        return $this->db->get('tblpackagepermissions')->row();
    }

    /**
     * Added By : Vaidehi
     * Dt : 11/09/2017
     * get count of brands for logged in user
     */
    function count_allbrands_by_userid($useremail)
    {
        $this->db->select('tblstaff.staffid');

        $this->db->where('tblstaff.email', $useremail);
        $this->db->where('tblstaff.active', 1);

        $staff = $this->db->get('tblstaff')->row();

        if (isset($staff->staffid)) {
            $this->db->select('tblstaffbrand.staffbrandid');

            $this->db->where('tblstaffbrand.staffid', $staff->staffid);
            $this->db->where('tblstaffbrand.active', 1);

            return $this->db->count_all_results('tblstaffbrand');
        } else {
            return 0;
        }
    }

    /**
     * Added By : Vaidehi
     * Dt : 11/09/2017
     * get package name
     */
    public function get_package_type($packageid)
    {
        $this->db->select('tblpackagetype.name');

        $this->db->join('tblpackages', 'tblpackages.packagetypeid = tblpackagetype.id');
        $this->db->where('tblpackages.packageid', $packageid);

        return $this->db->get('tblpackagetype')->row();
    }

    /**
     * @param  integer ID
     * @param  boolean Is Client or Staff
     * @return none
     * Update login info on autologin
     */
    private function update_login_info($user_id, $staff)
    {
        $table = 'tblcontacts';
        $_id = 'id';
        if ($staff == true) {
            $table = 'tblstaff';
            $_id = 'staffid';
        }
        $this->db->set('last_ip', $this->input->ip_address());
        $this->db->set('last_login', date('Y-m-d H:i:s'));
        $this->db->where($_id, $user_id);
        $this->db->update($table);
    }

}

?>