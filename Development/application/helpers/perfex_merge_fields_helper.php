<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * General merge fields not linked to any features
 * @return array
 */
function get_other_merge_fields()
{
    $CI =& get_instance();
    $fields = array();
    $fields['{logo_url}'] = base_url('uploads/company/' . get_brand_option('company_logo'));

    $logo_width = do_action('merge_field_logo_img_width', '');
    $fields['{logo_image_with_url}'] = '<a href="' . site_url() . '" target="_blank"><img src="' . base_url('uploads/company/' . get_brand_option('company_logo')) . '"' . ($logo_width != '' ? ' width="' . $logo_width . '"' : '') . '></a>';

    $fields['{crm_url}'] = site_url();
    $fields['{admin_url}'] = admin_url();

    /**
     * Added By : Vaidehi
     * Dt : 12/05/2017
     * to get brand wise details with new variables
     */
    $fields['{client_url}'] = site_url();
    $fields['{portal_url}'] = admin_url();

    $session_data = get_session_data();
    /**
     * Added By : Vaidehi
     * Dt : 01/08/2018
     * to check if session exists or not
     */
    if (isset($session_data['is_sido_admin'])) {
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
    } else {
        $is_sido_admin = 1;
        $is_admin = 1;
    }

    if ($is_sido_admin == 0 && $is_admin == 0) {
        $fields['{main_domain}'] = get_brand_option('main_domain');
        $fields['{companyname}'] = get_brand_option('companyname');
    } else {
        $fields['{main_domain}'] = get_option('main_domain');
        $fields['{companyname}'] = get_option('companyname');
    }

    if (!is_staff_logged_in() || is_client_logged_in()) {
        $fields['{email_signature}'] = get_option('email_signature');
    } else {
        $CI->db->select('email_signature')->from('tblstaff')->where('staffid', get_staff_user_id());
        $signature = $CI->db->get()->row()->email_signature;
        if (empty($signature)) {
            if ($is_sido_admin == 0 && $is_admin == 0) {
                $fields['{email_signature}'] = get_brand_option('email_signature');
            } else {
                $fields['{email_signature}'] = get_option('email_signature');
            }
        } else {
            $fields['{email_signature}'] = $signature;
        }
    }

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'other';
    $hook_data['id'] = '';

    $hook_data = do_action('other_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * Lead merge fields
 * @param  mixed $id lead id
 * @return array
 */
function get_lead_merge_fields($id)
{
    $CI =& get_instance();
    $fields = array();
    $fields['{lead_name}'] = 'TBD';
    $fields['{lead_email}'] = 'TBD';
    $fields['{lead_position}'] = 'TBD';
    $fields['{lead_company}'] = 'TBD';
    $fields['{lead_country}'] = 'TBD';
    $fields['{lead_zip}'] = 'TBD';
    $fields['{lead_city}'] = 'TBD';
    $fields['{lead_state}'] = 'TBD';
    $fields['{lead_address}'] = 'TBD';
    $fields['{lead_assigned}'] = 'TBD';
    $fields['{lead_status}'] = 'TBD';
    $fields['{lead_source}'] = 'TBD';
    $fields['{lead_phonenumber}'] = 'TBD';
    $fields['{lead_link}'] = 'TBD';
    $fields['{lead_website}'] = 'TBD';
    $fields['{lead_description}'] = 'TBD';

    $CI->db->where('id', $id);
    $lead = $CI->db->get('tblleads')->row();

    if (!$lead) {
        return $fields;
    }

    $fields['{lead_link}'] = admin_url('leads/lead/' . $lead->id);
    $fields['{lead_name}'] = $lead->name;
    $fields['{lead_email}'] = $lead->email;
    $fields['{lead_position}'] = $lead->title;
    $fields['{lead_phonenumber}'] = $lead->phonenumber;
    $fields['{lead_company}'] = $lead->company;
    $fields['{lead_zip}'] = $lead->zip;
    $fields['{lead_city}'] = $lead->city;
    $fields['{lead_state}'] = $lead->state;
    $fields['{lead_address}'] = $lead->address;
    $fields['{lead_website}'] = $lead->website;
    $fields['{lead_description}'] = $lead->description;

    if ($lead->assigned != 0) {
        $fields['{lead_assigned}'] = get_staff_full_name($lead->assigned);
    }
    if ($lead->country != 0) {
        $country = get_country($lead->country);
        $fields['{lead_country}'] = $country->short_name;
    }
    if ($lead->source > 0) {
        $CI->db->select('name');
        $CI->db->from('tblleadssources');
        $CI->db->where('id', $lead->source);
        $source = $CI->db->get()->row();
        if ($source) {
            $fields['{lead_source}'] = $source->name;
        }
    }
    if ($lead->junk == 1) {
        $fields['{lead_status}'] = _l('lead_junk');
    } elseif ($lead->lost == 1) {
        $fields['{lead_status}'] = _l('lead_lost');
    } else {
        $CI->db->select('name');
        $CI->db->from('tblleadsstatus');
        $CI->db->where('id', $lead->status);
        $status = $CI->db->get()->row();
        if ($status) {
            $fields['{lead_status}'] = $status->name;
        }
    }

    $custom_fields = get_custom_fields('leads');
    foreach ($custom_fields as $field) {
        $fields['{' . $field['slug'] . '}'] = get_custom_field_value($id, $field['id'], 'leads');
    }

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'lead';
    $hook_data['id'] = $id;

    $hook_data = do_action('lead_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * Project merge fields
 * @param  mixed $project_id project id
 * @param  array $additional_data option to pass additional data for the templates eq is staff template or customer template
 * This field is also used for the project discussion files and regular discussions
 * @return array
 */
function get_project_merge_fields($project_id, $additional_data = array())
{
    $fields = array();

    $fields['{project_name}'] = 'TBD';
    $fields['{project_deadline}'] = 'TBD';
    $fields['{project_start_date}'] = 'TBD';
    $fields['{project_description}'] = 'TBD';
    $fields['{project_link}'] = 'TBD';
    $fields['{discussion_link}'] = 'TBD';
    $fields['{discussion_creator}'] = 'TBD';
    $fields['{comment_creator}'] = 'TBD';
    $fields['{file_creator}'] = 'TBD';
    $fields['{discussion_subject}'] = 'TBD';
    $fields['{discussion_description}'] = 'TBD';
    $fields['{discussion_comment}'] = 'TBD';

    $CI =& get_instance();

    $CI->db->where('id', $project_id);
    $project = $CI->db->get('tblprojects')->row();

    $fields['{project_name}'] = isset($project->name) ? $project->name : "TBD";
    $fields['{project_deadline}'] = isset($project->deadline) ? _d($project->deadline) : "TBD";
    $fields['{project_start_date}'] = isset($project->start_date) ? _d($project->start_date) : "TBD";
    $fields['{project_description}'] = isset($project->description) ? $project->description : "TBD";

    $custom_fields = get_custom_fields('projects');
    foreach ($custom_fields as $field) {
        $fields['{' . $field['slug'] . '}'] = get_custom_field_value($project_id, $field['id'], 'projects');
    }

    if (is_client_logged_in()) {
        $cf = get_contact_full_name(get_contact_user_id());
    } else {
        $cf = get_staff_full_name(get_staff_user_id());
    }

    $fields['{file_creator}'] = $cf;
    $fields['{discussion_creator}'] = $cf;
    $fields['{comment_creator}'] = $cf;

    if (isset($additional_data['discussion_id'])) {
        $CI->db->where('id', $additional_data['discussion_id']);

        if (isset($additional_data['discussion_type']) && $additional_data['discussion_type'] == 'regular') {
            $table = 'tblprojectdiscussions';
        } else {
            // is file
            $table = 'tblprojectfiles';
        }

        $discussion = $CI->db->get($table)->row();

        $fields['{discussion_subject}'] = $discussion->subject;
        $fields['{discussion_description}'] = $discussion->description;

        if (isset($additional_data['discussion_comment_id'])) {
            $CI->db->where('id', $additional_data['discussion_comment_id']);
            $discussion_comment = $CI->db->get('tblprojectdiscussioncomments')->row();
            //$fields['{discussion_comment}'] = $discussion_comment->content;
        }
    }
    if (isset($additional_data['customer_template'])) {
        $fields['{project_link}'] = site_url('clients/project/' . $project_id);

        if (isset($additional_data['discussion_id']) && isset($additional_data['discussion_type']) && $additional_data['discussion_type'] == 'regular') {
            //$fields['{discussion_link}'] = site_url('clients/project/' . $project_id . '?group=project_discussions&discussion_id=' . $additional_data['discussion_id']);
        } elseif (isset($additional_data['discussion_id']) && isset($additional_data['discussion_type']) && $additional_data['discussion_type'] == 'file') {
            // is file
            //$fields['{discussion_link}'] = site_url('clients/project/' . $project_id . '?group=project_files&file_id=' . $additional_data['discussion_id']);
        }
    } else {
        $fields['{project_link}'] = admin_url('projects/view/' . $project_id);
        if (isset($additional_data['discussion_type']) && $additional_data['discussion_type'] == 'regular' && isset($additional_data['discussion_id'])) {
            //$fields['{discussion_link}'] = admin_url('projects/view/' . $project_id . '?group=project_discussions&discussion_id=' . $additional_data['discussion_id']);
        } else {
            if (isset($additional_data['discussion_id'])) {
                // is file
                // $fields['{discussion_link}'] = admin_url('projects/view/' . $project_id . '?group=project_files&file_id=' . $additional_data['discussion_id']);
            }
        }
    }

    $custom_fields = get_custom_fields('projects');
    foreach ($custom_fields as $field) {
        $fields['{' . $field['slug'] . '}'] = get_custom_field_value($project_id, $field['id'], 'projects');
    }

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'project';
    $hook_data['id'] = $project_id;
    $hook_data['additional_data'] = $additional_data;

    $hook_data = do_action('project_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * Password merge fields
 * @param  array $data
 * @param  boolean $staff is field for staff or contact
 * @param  string $type template type
 * @return array
 */
function get_password_merge_field($data, $staff, $type)
{
    $fields['{reset_password_url}'] = 'TBD';
    $fields['{set_password_url}'] = 'TBD';

    if ($staff == true) {
        if ($type == 'forgot') {
            $fields['{reset_password_url}'] = site_url('authentication/reset_password/' . floatval($staff) . '/' . $data['userid'] . '/' . $data['new_pass_key']);
        }
    } else {
        if ($type == 'forgot') {
            $fields['{reset_password_url}'] = site_url('clients/reset_password/' . floatval($staff) . '/' . $data['userid'] . '/' . $data['new_pass_key']);
        } elseif ($type == 'set') {
            $fields['{set_password_url}'] = site_url('authentication/set_password/' . $staff . '/' . $data['userid'] . '/' . $data['new_pass_key']);
        }
    }

    return $fields;
}

/**
 * Merge fields for Contacts and Customers
 * @param  mixed $client_id
 * @param  string $contact_id
 * @param  string $password password is used when sending welcome email, only 1 time
 * @return array
 */
function get_client_contact_merge_fields($client_id, $contact_id = '', $password = '')
{

    $fields = array();

    if ($contact_id == '') {
        $contact_id = get_primary_contact_user_id($client_id);
    }
    $fields['{contact_firstname}'] = 'TBD';
    $fields['{contact_lastname}'] = 'TBD';
    $fields['{contact_fullname}'] = 'TBD';
    $fields['{contact_email}'] = 'TBD';
    // $fields['{client_company}']     = '';
    // $fields['{client_phonenumber}'] = '';
    // $fields['{client_country}']     = '';
    // $fields['{client_city}']        = '';
    // $fields['{client_zip}']         = '';
    // $fields['{client_state}']       = '';
    // $fields['{client_address}']     = '';
    //$fields['{password}'] = '';
    //$fields['{client_vat_number}']  = '';

    $CI =& get_instance();

    /*$client = $CI->clients_model->get($client_id, array());
    if (!$client) {
        return $fields;
    }*/

    $CI->db->where('addressbookid', $client_id);
    //$CI->db->where('id', $contact_id);
    //$contact = $CI->db->get('tblcontacts')->row();
    $contact = $CI->db->get('tbladdressbook')->row();

    if (isset($client_id) && $client_id > 0) {
        $CI->db->select('email');
        $CI->db->where('addressbookid', $client_id);
        $CI->db->where('type', 'primary');
        $email_result = $CI->db->get('tbladdressbookemail')->row();
        if (!empty($email_result)) {
            $contact->email = $CI->db->get('tbladdressbookemail')->row()->email;
        }
    }
    if ($contact) {
        $fields['{contact_firstname}'] = $contact->firstname;
        $fields['{contact_lastname}'] = $contact->lastname;
        $fields['{contact_fullname}'] = $contact->firstname . " " . $contact->lastname;
        $fields['{contact_email}'] = isset($contact->email) ? $contact->email : "";
    }
    // if (!empty($client->vat)) {
    //     $fields['{client_vat_number}'] = $client->vat;
    // }

    // $fields['{client_company}']     = $client->company;
    // $fields['{client_phonenumber}'] = $client->phonenumber;
    // $fields['{client_country}']     = get_country_short_name($client->country);
    // $fields['{client_city}']        = $client->city;
    // $fields['{client_zip}']         = $client->zip;
    // $fields['{client_state}']       = $client->state;
    // $fields['{client_address}']     = $client->address;
    // $fields['{client_id}']     = $client_id;

    if ($password != '') {
        $fields['{password}'] = $password;
    }

    $custom_fields = get_custom_fields('customers');
    foreach ($custom_fields as $field) {
        $fields['{' . $field['slug'] . '}'] = get_custom_field_value($client_id, $field['id'], 'customers');
    }

    $custom_fields = get_custom_fields('contacts');
    foreach ($custom_fields as $field) {
        $fields['{' . $field['slug'] . '}'] = get_custom_field_value($contact_id, $field['id'], 'contacts');
    }

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'client_contact';
    $hook_data['id'] = $client_id;
    $hook_data['contact_id'] = $contact_id;

    $hook_data = do_action('client_contact_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

function get_statement_merge_fields($statement)
{
    $fields = array();

    $fields['{statement_from}'] = _d($statement['from']);
    $fields['{statement_to}'] = _d($statement['to']);
    $fields['{statement_balance_due}'] = format_money($statement['balance_due'], $statement['currency']->symbol);
    $fields['{statement_amount_paid}'] = format_money($statement['amount_paid'], $statement['currency']->symbol);
    $fields['{statement_invoiced_amount}'] = format_money($statement['invoiced_amount'], $statement['currency']->symbol);
    $fields['{statement_beginning_balance}'] = format_money($statement['beginning_balance'], $statement['currency']->symbol);

    $hook_data['fields_to'] = 'statement';
    $hook_data['merge_fields'] = $fields;
    $hook_data['statement'] = $statement;

    $hook_data = do_action('client_statement_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * Merge fields for estimates
 * @param  mixed $estimate_id estimate id
 * @return array
 */
function get_estimate_merge_fields($estimate_id)
{
    $fields = array();
    $CI =& get_instance();
    $CI->db->where('id', $estimate_id);
    $estimate = $CI->db->get('tblestimates')->row();
    if (!$estimate) {
        return $fields;
    }

    $CI->db->where('id', $estimate->currency);
    $symbol = $CI->db->get('tblcurrencies')->row()->symbol;

    $fields['{estimate_sale_agent}'] = get_staff_full_name($estimate->sale_agent);
    $fields['{estimate_total}'] = format_money($estimate->total, $symbol);
    $fields['{estimate_subtotal}'] = format_money($estimate->subtotal, $symbol);
    $fields['{estimate_link}'] = site_url('viewestimate/' . $estimate_id . '/' . $estimate->hash);
    $fields['{estimate_number}'] = format_estimate_number($estimate_id);
    $fields['{estimate_reference_no}'] = $estimate->reference_no;
    $fields['{estimate_expirydate}'] = _d($estimate->expirydate);
    $fields['{estimate_date}'] = _d($estimate->date);
    $fields['{estimate_status}'] = format_estimate_status($estimate->status, '', false);

    $custom_fields = get_custom_fields('estimate');
    foreach ($custom_fields as $field) {
        $fields['{' . $field['slug'] . '}'] = get_custom_field_value($estimate_id, $field['id'], 'estimate');
    }

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'estimate';
    $hook_data['id'] = $estimate_id;

    $hook_data = do_action('estimate_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * Added by Purvi on 10-11-2017
 * Merge fields for meetings
 * @param  mixed $meeting_id meeting id
 * @return array
 */
function get_meetings_merge_fields($meeting_id)
{
    $fields = array();
    $fields['{meeting_name}'] = "TBD";
    $fields['{meeting_description}'] = "TBD";
    $fields['{meeting_timezone}'] = "TBD";
    $fields['{meeting_zone}'] = "TBD";
    $fields['{meeting_start_date}'] = "TBD";
    $fields['{meeting_end_date}'] = "TBD";
    $fields['{meeting_start}'] = "TBD";
    $fields['{meeting_end}'] = "TBD";
    $CI =& get_instance();
    $CI->db->where('meetingid', $meeting_id);
    $meeting = $CI->db->get('tblmeetings')->row();
    if (!$meeting) {
        return $fields;
    }

    $fields['{meeting_name}'] = $meeting->name;
    $fields['{meeting_description}'] = $meeting->description;
    $fields['{meeting_timezone}'] = $meeting->default_timezone;
    $fields['{meeting_zone}'] = $meeting->default_timezone;
    $fields['{meeting_start_date}'] = $meeting->start_date;
    $fields['{meeting_end_date}'] = $meeting->end_date;
    $fields['{meeting_start}'] = _dt($meeting->start_date, true);
    $fields['{meeting_end}'] = _dt($meeting->end_date, true);
    $fields['{logo_url}'] = base_url('uploads/company/' . get_option('company_logo'));

    $logo_width = do_action('merge_field_logo_img_width', '');
    $fields['{logo_image_with_url}'] = '<a href="' . site_url() . '" target="_blank"><img src="' . base_url('uploads/company/' . get_option('company_logo')) . '"' . ($logo_width != '' ? ' width="' . $logo_width . '"' : '') . '></a>';

    $fields['{crm_url}'] = site_url();
    $fields['{admin_url}'] = admin_url();

    /**
     * Added By : Vaidehi
     * Dt : 12/05/2017
     * to get brand wise details with new variables
     */
    $fields['{client_url}'] = site_url();
    $fields['{portal_url}'] = admin_url();
    $fields['{logo}'] = '<a href="' . site_url() . '" target="_blank"><img src="' . base_url('uploads/company/' . get_option('company_logo')) . '"' . ($logo_width != '' ? ' width="' . $logo_width . '"' : '') . '></a>';

    $session_data = get_session_data();
    $is_sido_admin = $session_data['is_sido_admin'];
    $is_admin = $session_data['is_admin'];

    if ($is_sido_admin == 0 && $is_admin == 0) {
        $fields['{main_domain}'] = get_brand_option('main_domain');
        $fields['{companyname}'] = get_brand_option('companyname');
    } else {
        $fields['{main_domain}'] = get_option('main_domain');
        $fields['{companyname}'] = get_option('companyname');
    }

    if (!is_staff_logged_in() || is_client_logged_in()) {
        $fields['{email_signature}'] = get_option('email_signature');
    } else {
        $CI->db->select('email_signature')->from('tblstaff')->where('staffid', get_staff_user_id());
        $signature = $CI->db->get()->row()->email_signature;
        if (empty($signature)) {
            if ($is_sido_admin == 0 && $is_admin == 0) {
                $fields['{email_signature}'] = get_brand_option('email_signature');
            } else {
                $fields['{email_signature}'] = get_option('email_signature');
            }
        } else {
            $fields['{email_signature}'] = $signature;
        }
    }
    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'meetings';
    $hook_data['id'] = $meeting_id;

    $hook_data = do_action('estimate_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * Merge fields for invoices
 * @param  mixed $invoice_id invoice id
 * @param  mixed $payment_id invoice id
 * @return array
 */
function get_invoice_merge_fields($invoice_id, $payment_id = false)
{
    $fields = array();
    $CI =& get_instance();
    $CI->db->where('id', $invoice_id);
    $invoice = $CI->db->get('tblinvoices')->row();

    if (!$invoice) {
        return $fields;
    }

    $CI->db->where('id', $invoice->currency);
    $symbol = $CI->db->get('tblcurrencies')->row()->symbol;

    $fields['{payment_total}'] = '';
    $fields['{payment_date}'] = '';

    if ($payment_id) {
        $CI->db->where('id', $payment_id);
        $payment = $CI->db->get('tblinvoicepaymentrecords')->row();

        $fields['{payment_total}'] = format_money($payment->amount, $symbol);
        $fields['{payment_date}'] = _d($payment->date);
    }

    $fields['{team_member}'] = get_staff_full_name($invoice->sale_agent);
    $fields['{invoice_total}'] = format_money($invoice->total, $symbol);
    $fields['{invoice_subtotal}'] = format_money($invoice->subtotal, $symbol);

    $fields['{invoice_link}'] = site_url('viewinvoice/' . $invoice_id . '/' . $invoice->hash);
    $fields['{invoice_number}'] = format_invoice_number($invoice_id);
    $fields['{invoice_due_date}'] = _d($invoice->duedate);
    $fields['{invoice_date}'] = _d($invoice->date);
    $fields['{invoice_status}'] = format_invoice_status($invoice->status, '', false);

    $custom_fields = get_custom_fields('invoice');
    foreach ($custom_fields as $field) {
        $fields['{' . $field['slug'] . '}'] = get_custom_field_value($invoice_id, $field['id'], 'invoice');
    }

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'invoice';
    $hook_data['id'] = $invoice_id;

    $hook_data = do_action('invoice_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * Merge fields for proposals
 * @param  mixed $proposal_id proposal id
 * @return array
 */
function get_proposal_merge_fields($proposal_id)
{
    $fields = array();
    $fields['{proposal_id}'] = "TBD";
    $fields['{proposal_link}'] = "TBD";;
    $fields['{proposal_subject}'] = "TBD";;
    $fields['{proposal_open_till}'] = "TBD";;
    $fields['{proposal_assigned}'] = "TBD";;
    $CI =& get_instance();
    $CI->db->where('templateid', $proposal_id);
    //$CI->db->join('tblcountries', 'tblcountries.country_id=tblproposals.country', 'left');
    $proposal = $CI->db->get('tblproposaltemplates')->row();

    if (!$proposal) {
        return $fields;
    }
    $fields['{proposal_id}'] = $proposal_id;
    $fields['{proposal_link}'] = admin_url('proposaltemplates/viewproposal/' . $proposal_id);
    $fields['{proposal_subject}'] = $proposal->name;
    $fields['{proposal_open_till}'] = _d($proposal->valid_date);
    $fields['{proposal_assigned}'] = get_staff_full_name($proposal->created_by);
    /*$CI->load->model('currencies_model');
    if ($proposal->currency != 0) {
        $currency = $CI->currencies_model->get($proposal->currency);
    } else {
        $currency = $CI->currencies_model->get_base_currency();
    }*/
    //$fields['{proposal_number}']      = format_proposal_number($proposal_id);
    /*$fields['{proposal_total}']       = format_money($proposal->proposal_total, $currency->symbol);
    $fields['{proposal_subtotal}']    = format_money($proposal->proposal_subtotal, $currency->symbol);*/
    //$fields['{proposal_proposal_to}'] = $proposal->proposal_to;
    //$fields['{proposal_email}']       = $proposal->email;
    //$fields['{proposal_phone}']       = $proposal->phone;

    /*$custom_fields = get_custom_fields('proposal');
    foreach ($custom_fields as $field) {
        $fields['{' . $field['slug'] . '}'] = get_custom_field_value($proposal_id, $field['id'], 'proposal');
    }*/

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'proposal';
    $hook_data['id'] = $proposal_id;

    $hook_data = do_action('proposal_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];
    return $fields;
}

/**
 * Merge field for contacts
 * @param  mixed $contract_id contract id
 * @return array
 */
function get_contract_merge_fields($contract_id)
{
    $fields = array();
    $CI =& get_instance();
    $CI->db->where('id', $contract_id);
    $contract = $CI->db->get('tblcontracts')->row();

    if (!$contract) {
        return $fields;
    }

    $CI->load->model('currencies_model');
    $currency = $CI->currencies_model->get_base_currency();

    $fields['{contract_id}'] = $contract->id;
    $fields['{contract_subject}'] = $contract->subject;
    $fields['{contract_description}'] = $contract->description;
    $fields['{contract_datestart}'] = _d($contract->datestart);
    $fields['{contract_dateend}'] = _d($contract->dateend);
    $fields['{contract_contract_value}'] = format_money($contract->contract_value, $currency->symbol);

    $custom_fields = get_custom_fields('contracts');
    foreach ($custom_fields as $field) {
        $fields['{' . $field['slug'] . '}'] = get_custom_field_value($contract_id, $field['id'], 'contracts');
    }

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'contract';
    $hook_data['id'] = $contract_id;

    $hook_data = do_action('contract_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * Merge fields for tasks
 * @param  mixed $task_id task id
 * @param  boolean $client_template is client template or staff template
 * @return array
 */
function get_task_merge_fields($task_id, $client_template = false)
{
    $fields = array();
    $fields['{task_name}'] = "TBD";
    $fields['{task_description}'] = "TBD";
    $fields['{task_status}'] = "TBD";
    $fields['{task_priority}'] = "TBD";
    $fields['{task_link}'] = "TBD";
    $fields['{task_due_date}'] = "TBD";
    $fields['{task_duedate}'] = "TBD";
    $fields['{comment_link}'] = "TBD";
    $fields['{task_comment}'] = "TBD";
    $fields['{task_related_to}'] = "TBD";

    $CI =& get_instance();
    $CI->db->where('id', $task_id);
    $task = $CI->db->get('tblstafftasks')->row();

    if (!$task) {
        return $fields;
    }

    // Client templateonly passed when sending to tasks related to project and sending email template to contacts
    // Passed from tasks_model  _send_task_responsible_users_notification function
    if ($client_template == false) {
        $fields['{task_link}'] = admin_url('tasks/dashboard/' . $task_id);
    } else {
        $fields['{task_link}'] = site_url('clients/project/' . $task->rel_id . '?group=project_tasks&taskid=' . $task_id);
    }

    if (is_client_logged_in()) {
        $fields['{task_user_take_action}'] = get_contact_full_name(get_contact_user_id());
    } else {
        $fields['{task_user_take_action}'] = get_staff_full_name(get_staff_user_id());
    }

    /*$fields['{task_comment}'] = '';
    $fields['{task_related_to}'] = '';
    $fields['{project_name}'] = '';*/

    if ($task->rel_type == 'project') {
        $CI->db->select('name');
        $CI->db->from('tblprojects');
        $CI->db->where('id', $task->rel_id);
        $project = $CI->db->get()->row();
        if ($project) {
            $fields['{project_name}'] = $project->name;
        }
    }

    if (!empty($task->rel_id)) {
        $rel_data = get_relation_data($task->rel_type, $task->rel_id);
        $rel_values = get_relation_values($rel_data, $task->rel_type);
        $fields['{task_related_to}'] = $rel_values['name'];
    }

    $fields['{task_name}'] = $task->name;
    $fields['{task_description}'] = $task->description;
    $fields['{task_status}'] = format_task_status($task->status, false, true);
    $fields['{task_priority}'] = task_priority($task->priority);
    //$fields['{task_start_date}'] = _d($task->startdate);
    $fields['{task_due_date}'] = _d($task->duedate);
    $fields['{task_duedate}'] = _dt($task->duedate, true);

    $fields['{comment_link}'] = '';

    $CI->db->where('taskid', $task_id);
    $CI->db->limit(1);
    $CI->db->order_by('dateadded', 'desc');
    $comment = $CI->db->get('tblstafftaskcomments')->row();

    if ($comment) {
        $fields['{task_comment}'] = $comment->content;
        $fields['{comment_link}'] = $fields['{task_link}'] . '#comment_' . $comment->id;
    }

    $custom_fields = get_custom_fields('tasks');
    foreach ($custom_fields as $field) {
        $fields['{' . $field['slug'] . '}'] = get_custom_field_value($task_id, $field['id'], 'tasks');
    }

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'task';
    $hook_data['id'] = $task_id;
    $hook_data['client_template'] = $client_template;

    $hook_data = do_action('task_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * Merge field for staff members
 * @param  mixed $staff_id staff id
 * @param  string $password password is used only when sending welcome email, 1 time
 * @return array
 */
function get_staff_merge_fields($staff_id, $password = '')
{
    $fields = array();

    $CI =& get_instance();
    $CI->db->where('staffid', $staff_id);
    $staff = $CI->db->get('tblstaff')->row();

    /*$fields['{password}'] = '';
    $fields['{teammember_firstname}'] = '';
    $fields['{teammember_lastname}'] = '';
    $fields['{teammember_fullname}'] = '';
    $fields['{teammember_email}'] = '';
    $fields['{teammember_date_created}'] = '';*/

    if (!$staff) {
        return $fields;
    }

    if ($password != '') {
        $fields['{password}'] = $password;
    }

    if ($staff->two_factor_auth_code) {
        $fields['{two_factor_auth_code}'] = $staff->two_factor_auth_code;
    }

    $fields['{teammember_firstname}'] = $staff->firstname;
    $fields['{teammember_lastname}'] = $staff->lastname;
    $fields['{teammember_fullname}'] = $staff->firstname . " " . $staff->lastname;
    $fields['{teammember_email}'] = $staff->email;
    $fields['{teammember_date_created}'] = $staff->datecreated;


    $custom_fields = get_custom_fields('staff');
    foreach ($custom_fields as $field) {
        $fields['{' . $field['slug'] . '}'] = get_custom_field_value($staff_id, $field['id'], 'staff');
    }

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'staff';
    $hook_data['id'] = $staff_id;

    $hook_data = do_action('staff_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * Merge fields for tickets
 * @param  string $template template name, used to identify url
 * @param  mixed $ticket_id ticket id
 * @param  mixed $reply_id reply id
 * @return array
 */
function get_ticket_merge_fields($template, $ticket_id, $reply_id = '')
{
    $fields = array();

    $CI =& get_instance();
    $CI->db->where('ticketid', $ticket_id);
    $ticket = $CI->db->get('tbltickets')->row();

    if (!$ticket) {
        return $fields;
    }

    // Replace contact firstname with the ticket name in case the ticket is not linked to any contact.
    // eq email or form imported.
    if ($ticket->name != NULL && $ticket->name != "") {
        $fields['{contact_firstname}'] = $ticket->name;
    }

    $fields['{ticket_priority}'] = '';
    $fields['{ticket_service}'] = '';


    $CI->db->where('departmentid', $ticket->department);
    $department = $CI->db->get('tbldepartments')->row();
    if ($department) {
        $fields['{ticket_department}'] = $department->name;
    }

    $fields['{ticket_status}'] = ticket_status_translate($ticket->status);
    $CI->db->where('serviceid', $ticket->service);
    $service = $CI->db->get('tblservices')->row();
    if ($service) {
        $fields['{ticket_service}'] = $service->name;
    }

    $fields['{ticket_id}'] = $ticket_id;
    $fields['{ticket_priority}'] = ticket_priority_translate($ticket->priority);

    $customerTemplates = array(
        'new-ticket-opened-admin',
        'ticket-reply',
        'ticket-autoresponse',
        'auto-close-ticket',
    );

    if (in_array($template, $customerTemplates)) {
        $fields['{ticket_url}'] = site_url('clients/ticket/' . $ticket_id);
    } else {
        $fields['{ticket_url}'] = admin_url('tickets/ticket/' . $ticket_id);
    }

    if ($template == 'ticket-reply-to-admin' || $template == 'ticket-reply') {
        $CI->db->where('ticketid', $ticket_id);
        $CI->db->limit(1);
        $CI->db->order_by('date', 'desc');
        $reply = $CI->db->get('tblticketreplies')->row();
        $fields['{ticket_message}'] = $reply->message;
    } else {
        $fields['{ticket_message}'] = $ticket->message;
    }

    $fields['{ticket_date}'] = _dt($ticket->date);
    $fields['{ticket_subject}'] = $ticket->subject;

    $custom_fields = get_custom_fields('tickets');
    foreach ($custom_fields as $field) {
        $fields['{' . $field['slug'] . '}'] = get_custom_field_value($ticket_id, $field['id'], 'tickets');
    }

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'ticket';
    $hook_data['id'] = $ticket_id;
    $hook_data['reply_id'] = $reply_id;
    $hook_data['template'] = $template;

    $hook_data = do_action('ticket_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * @return array
 * All available merge fields for templates are defined here
 */
function get_available_merge_fields()
{
    $available_merge_fields = array(
        'Team Member' => array(
            array(
                'name' => 'Firstname',
                'key' => '{teamember_firstname}',
                'available' => array(
                    'staff',
                    'tasks',
                    'project'
                )
            ),
            array(
                'name' => 'Lastname',
                'key' => '{teamember_lastname}',
                'available' => array(
                    'staff',
                    'tasks',
                    'project'
                )
            ),
            array(
                'name' => 'Email',
                'key' => '{teamember_email}',
                'available' => array(
                    'staff',
                    'project'
                )
            ),
            array(
                'name' => 'Date Created',
                'key' => '{teamember_date_created}',
                'available' => array(
                    'staff'
                )
            ),
            array(
                'name' => 'Reset Password Url',
                'key' => '{reset_password_url}',
                'available' => array(
                    'staff'
                )
            )
        ),
        'clients' => array(
            array(
                'name' => 'Firstname',
                'key' => '{contact_firstname}',
                'available' => array(
                    'client',
                    'ticket',
                    'invoice',
                    'estimate',
                    'contract',
                    'project',
                    'tasks'
                )
            ),
            array(
                'name' => 'Lastname',
                'key' => '{contact_lastname}',
                'available' => array(
                    'client',
                    'ticket',
                    'invoice',
                    'estimate',
                    'contract',
                    'project',
                    'tasks'
                )
            ),
            array(
                'name' => 'Set New Password Url',
                'key' => '{set_password_url}',
                'available' => array(
                    'client'
                )
            ),
            array(
                'name' => 'Reset Password Url',
                'key' => '{reset_password_url}',
                'available' => array(
                    'client'
                )
            ),
            array(
                'name' => 'Email',
                'key' => '{contact_email}',
                'available' => array(
                    'client',
                    'invoice',
                    'estimate',
                    'ticket',
                    'contract',
                    'project'
                )
            ),
            // array(
            //     'name' => 'Client Company',
            //     'key' => '{client_company}',
            //     'available' => array(
            //         'client',
            //         'invoice',
            //         'estimate',
            //         'ticket',
            //         'contract',
            //         'project'
            //     )
            // ),
            // array(
            //     'name' => 'Client Phone Number',
            //     'key' => '{client_phonenumber}',
            //     'available' => array(
            //         'client',
            //         'invoice',
            //         'estimate',
            //         'ticket',
            //         'contract',
            //         'project'
            //     )
            // ),
            // array(
            //     'name' => 'Client Country',
            //     'key' => '{client_country}',
            //     'available' => array(
            //         'client',
            //         'invoice',
            //         'estimate',
            //         'ticket',
            //         'contract',
            //         'project'
            //     )
            // ),
            // array(
            //     'name' => 'Client City',
            //     'key' => '{client_city}',
            //     'available' => array(
            //         'client',
            //         'invoice',
            //         'estimate',
            //         'ticket',
            //         'contract',
            //         'project'
            //     )
            // ),
            // array(
            //     'name' => 'Client Zip',
            //     'key' => '{client_zip}',
            //     'available' => array(
            //         'client',
            //         'invoice',
            //         'estimate',
            //         'ticket',
            //         'contract',
            //         'project'
            //     )
            // ),
            // array(
            //     'name' => 'Client State',
            //     'key' => '{client_state}',
            //     'available' => array(
            //         'client',
            //         'invoice',
            //         'estimate',
            //         'ticket',
            //         'contract',
            //         'project'
            //     )
            // ),
            // array(
            //     'name' => 'Client Address',
            //     'key' => '{client_address}',
            //     'available' => array(
            //         'client',
            //         'invoice',
            //         'estimate',
            //         'ticket',
            //         'contract',
            //         'project'
            //     )
            // ),
            // array(
            //     'name' => 'Client Vat Number',
            //     'key' => '{client_vat_number}',
            //     'available' => array(
            //         'client',
            //         'invoice',
            //         'estimate',
            //         'ticket',
            //         'contract',
            //         'project'
            //     )
            // ),
            // array(
            //     'name' => 'Client ID',
            //     'key' => '{client_id}',
            //     'available' => array(
            //         'client',
            //         'invoice',
            //         'estimate',
            //         'ticket',
            //         'contract',
            //         'project'
            //     )
            // ),
            array(
                'name' => 'Statement From',
                'key' => '{statement_from}',
                'available' => array(
                    'client',
                )
            ),
            array(
                'name' => 'Statement To',
                'key' => '{statement_to}',
                'available' => array(
                    'client',
                )
            ),
            array(
                'name' => 'Statement Balance Due',
                'key' => '{statement_balance_due}',
                'available' => array(
                    'client',
                )
            ),
            array(
                'name' => 'Statement Amount Paid',
                'key' => '{statement_amount_paid}',
                'available' => array(
                    'client',
                )
            ),
            array(
                'name' => 'Statement Invoiced Amount',
                'key' => '{statement_invoiced_amount}',
                'available' => array(
                    'client',
                )
            ),
            array(
                'name' => 'Statement Beginning Balance',
                'key' => '{statement_beginning_balance}',
                'available' => array(
                    'client',
                )
            ),
        ),
        'ticket' => array(
            array(
                'name' => 'Ticket ID',
                'key' => '{ticket_id}',
                'available' => array(
                    'ticket'
                )
            ),
            array(
                'name' => 'Ticket URL',
                'key' => '{ticket_url}',
                'available' => array(
                    'ticket'
                )
            ),
            array(
                'name' => 'Department',
                'key' => '{ticket_department}',
                'available' => array(
                    'ticket'
                )
            ),
            array(
                'name' => 'Date Opened',
                'key' => '{ticket_date}',
                'available' => array(
                    'ticket'
                )
            ),
            array(
                'name' => 'Ticket Subject',
                'key' => '{ticket_subject}',
                'available' => array(
                    'ticket'
                )
            ),
            array(
                'name' => 'Ticket Message',
                'key' => '{ticket_message}',
                'available' => array(
                    'ticket'
                )
            ),
            array(
                'name' => 'Ticket Status',
                'key' => '{ticket_status}',
                'available' => array(
                    'ticket'
                )
            ),
            array(
                'name' => 'Ticket Priority',
                'key' => '{ticket_priority}',
                'available' => array(
                    'ticket'
                )
            ),
            array(
                'name' => 'Ticket Service',
                'key' => '{ticket_service}',
                'available' => array(
                    'ticket'
                )
            )
        ),
        'contract' => array(
            array(
                'name' => 'ID',
                'key' => '{contract_id}',
                'available' => array(
                    'contract'
                )
            ),
            // array(
            //     'name' => 'Contract Subject',
            //     'key' => '{contract_subject}',
            //     'available' => array(
            //         'contract'
            //     )
            // ),
            // array(
            //     'name' => 'Contract Description',
            //     'key' => '{contract_description}',
            //     'available' => array(
            //         'contract'
            //     )
            // ),
            array(
                'name' => 'Start Date',
                'key' => '{contract_datestart}',
                'available' => array(
                    'contract'
                )
            ),
            array(
                'name' => 'End Date',
                'key' => '{contract_dateend}',
                'available' => array(
                    'contract'
                )
            ),
            array(
                'name' => 'Value',
                'key' => '{contract_contract_value}',
                'available' => array(
                    'contract'
                )
            )
        ),
        'invoice' => array(
            array(
                'name' => 'Link',
                'key' => '{invoice_link}',
                'available' => array(
                    'invoice'
                )
            ),
            array(
                'name' => 'Number',
                'key' => '{invoice_number}',
                'available' => array(
                    'invoice'
                )
            ),
            array(
                'name' => 'Duedate',
                'key' => '{invoice_due_date}',
                'available' => array(
                    'invoice'
                )
            ),
            // array(
            //     'name' => 'Invoice Date',
            //     'key' => '{invoice_date}',
            //     'available' => array(
            //         'invoice'
            //     )
            // ),
            array(
                'name' => 'Status',
                'key' => '{invoice_status}',
                'available' => array(
                    'invoice'
                )
            ),
            array(
                'name' => 'Team Member',
                'key' => '{team_member}',
                'available' => array(
                    'invoice'
                )
            ),
            array(
                'name' => 'Total',
                'key' => '{invoice_total}',
                'available' => array(
                    'invoice'
                )
            ),
            array(
                'name' => 'Subtotal',
                'key' => '{invoice_subtotal}',
                'available' => array(
                    'invoice'
                )
            ),
            array(
                'name' => 'Payment Recorded Total',
                'key' => '{payment_total}',
                'available' => array(
                    'invoice'
                )
            ),
            array(
                'name' => 'Payment Recorded Date',
                'key' => '{payment_date}',
                'available' => array(
                    'invoice'
                )
            )
        ),
        'estimate' => array(
            array(
                'name' => 'Estimate Link',
                'key' => '{estimate_link}',
                'available' => array(
                    'estimate'
                )
            ),
            array(
                'name' => 'Estimate Number',
                'key' => '{estimate_number}',
                'available' => array(
                    'estimate'
                )
            ),
            array(
                'name' => 'Reference no.',
                'key' => '{estimate_reference_no}',
                'available' => array(
                    'estimate'
                )
            ),
            array(
                'name' => 'Estimate Expiry Date',
                'key' => '{estimate_expirydate}',
                'available' => array(
                    'estimate'
                )
            ),
            array(
                'name' => 'Estimate Date',
                'key' => '{estimate_date}',
                'available' => array(
                    'estimate'
                )
            ),
            array(
                'name' => 'Estimate Status',
                'key' => '{estimate_status}',
                'available' => array(
                    'estimate'
                )
            ),
            array(
                'name' => 'Estimate Sale Agent',
                'key' => '{estimate_sale_agent}',
                'available' => array(
                    'estimate'
                )
            ),
            array(
                'name' => 'Estimate Total',
                'key' => '{estimate_total}',
                'available' => array(
                    'estimate'
                )
            ),
            array(
                'name' => 'Estimate Subtotal',
                'key' => '{estimate_subtotal}',
                'available' => array(
                    'estimate'
                )
            )
        ),
        'tasks' => array(
            // array(
            //     'name' => 'Staff/Contact who take action on task',
            //     'key' => '{task_user_take_action}',
            //     'available' => array(
            //         'tasks'
            //     )
            // ),
            array(
                'name' => 'Task Link',
                'key' => '{task_link}',
                'available' => array(
                    'tasks'
                )
            ),
            array(
                'name' => 'Comment Link',
                'key' => '{comment_link}',
                'available' => array(
                    'tasks'
                )
            ),
            array(
                'name' => 'Task Name',
                'key' => '{task_name}',
                'available' => array(
                    'tasks'
                )
            ),
            array(
                'name' => 'Task Description',
                'key' => '{task_description}',
                'available' => array(
                    'tasks'
                )
            ),
            array(
                'name' => 'Task Status',
                'key' => '{task_status}',
                'available' => array(
                    'tasks'
                )
            ),
            array(
                'name' => 'Task Comment',
                'key' => '{task_comment}',
                'available' => array(
                    'tasks'
                )
            ),
            array(
                'name' => 'Task Priority',
                'key' => '{task_priority}',
                'available' => array(
                    'tasks'
                )
            ),
            /*array(
                'name' => 'Task Start Date',
                'key' => '{task_star_tdate}',
                'available' => array(
                    'tasks'
                )
            ),*/
            array(
                'name' => 'Task Due Date',
                'key' => '{task_due_date}',
                'available' => array(
                    'tasks'
                )
            ),
            array(
                'name' => 'Task Related To',
                'key' => '{task_related}',
                'available' => array(
                    'tasks'
                )
            )
        ),
        'proposals' => array(
            array(
                'name' => 'Proposal ID',
                'key' => '{proposal_id}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'Proposal Number',
                'key' => '{proposal_number}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'Proposal Name',
                'key' => '{proposal_name}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'Proposal Total',
                'key' => '{proposal_total}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'Proposal Subtotal',
                'key' => '{proposal_subtotal}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'Open Till',
                'key' => '{proposal_open_till}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'Proposal Assigned',
                'key' => '{proposal_assigned}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'Company Name',
                'key' => '{proposal_proposal_to}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'Address',
                'key' => '{proposal_address}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'City',
                'key' => '{proposal_city}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'State',
                'key' => '{proposal_state}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'Zip Code',
                'key' => '{proposal_zip}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'Country',
                'key' => '{proposal_country}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'Email',
                'key' => '{proposal_email}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'Phone',
                'key' => '{proposal_phone}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'Proposal Link',
                'key' => '{proposal_link}',
                'available' => array(
                    'proposals'
                )
            )
        ),
        'leads' => array(
            array(
                'name' => 'Name',
                'key' => '{lead_name}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Email',
                'key' => '{lead_email}',
                'available' => array(
                    'leads'
                )
            ),
            // array(
            //     'name' => 'Position',
            //     'key' => '{lead_position}',
            //     'available' => array(
            //         'leads'
            //     )
            // ),
            array(
                'name' => 'Website',
                'key' => '{lead_website}',
                'available' => array(
                    'leads'
                )
            ),
            // array(
            //     'name' => 'Description',
            //     'key' => '{lead_description}',
            //     'available' => array(
            //         'leads'
            //     )
            // ),
            array(
                'name' => 'Phone Number',
                'key' => '{lead_phonenumber}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Company',
                'key' => '{lead_company}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Country',
                'key' => '{lead_country}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Zip',
                'key' => '{lead_zip}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'City',
                'key' => '{lead_city}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'State',
                'key' => '{lead_state}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Address',
                'key' => '{lead_address}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Assigned',
                'key' => '{lead_assigned}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Status',
                'key' => '{lead_status}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Source',
                'key' => '{lead_source}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Link',
                'key' => '{lead_link}',
                'available' => array(
                    'leads'
                )
            )
        ),
        'projects' => array(
            array(
                'name' => 'Project Name',
                'key' => '{project_name}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Project Description',
                'key' => '{project_description}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Project Start Date',
                'key' => '{project_start_date}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Project Deadline',
                'key' => '{project_deadline}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Project Link',
                'key' => '{project_link}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Discussion Link',
                'key' => '{discussion_link}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'File Creator',
                'key' => '{file_creator}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Discussion Creator',
                'key' => '{discussion_creator}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Comment Creator',
                'key' => '{comment_creator}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Discussion Subject',
                'key' => '{discussion_subject}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Discussion Description',
                'key' => '{discussion_description}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Discussion Comment',
                'key' => '{discussion_comment}',
                'available' => array(
                    'project'
                )
            )
        ),
        'other' => array(
            // array(
            //     'name' => 'Logo URL',
            //     'key' => '{logo_url}',
            //     'fromoptions' => true,
            //     'available' => array(
            //         'ticket',
            //         'client',
            //         'staff',
            //         'invoice',
            //         'estimate',
            //         'contract',
            //         'tasks',
            //         'proposals',
            //         'project',
            //         'leads',
            //         'meetings'
            //     )
            // ),
            array(
                // 'name' => 'Logo image with URL',
                // 'key' => '{logo_image_with_url}',
                'name' => 'Logo',
                'key' => '{logo}',
                'fromoptions' => true,
                'available' => array(
                    'ticket',
                    'client',
                    'staff',
                    'invoice',
                    'estimate',
                    'contract',
                    'tasks',
                    'proposals',
                    'project',
                    'leads',
                    'meetings'
                )
            ),
            array(
                //'name' => 'CRM URL',
                //'key' => '{crm_url}',
                'name' => 'Client URL',
                'key' => '{client_url}',
                'fromoptions' => true,
                'available' => array(
                    'ticket',
                    'client',
                    'staff',
                    'invoice',
                    'estimate',
                    'contract',
                    'tasks',
                    'proposals',
                    'project',
                    'leads',
                    'meetings'
                )
            ),
            array(
                //'name' => 'Admin URL',
                //'key' => '{admin_url}',
                'name' => 'Portal URL',
                'key' => '{portal_url}',
                'fromoptions' => true,
                'available' => array(
                    'ticket',
                    'client',
                    'staff',
                    'invoice',
                    'estimate',
                    'contract',
                    'tasks',
                    'proposals',
                    'project',
                    'leads',
                    'meetings'
                )
            ),
            // array(
            //     'name' => 'Main Domain',
            //     'key' => '{main_domain}',
            //     'fromoptions' => true,
            //     'available' => array(
            //         'ticket',
            //         'client',
            //         'staff',
            //         'invoice',
            //         'estimate',
            //         'contract',
            //         'tasks',
            //         'proposals',
            //         'project',
            //         'leads',
            //         'meetings'
            //     )
            // ),
            array(
                'name' => 'Company Name',
                'key' => '{companyname}',
                'fromoptions' => true,
                'available' => array(
                    'ticket',
                    'client',
                    'staff',
                    'invoice',
                    'estimate',
                    'contract',
                    'tasks',
                    'proposals',
                    'project',
                    'leads',
                    'meetings'
                )
            ),
            array(
                'name' => 'Email Signature',
                'key' => '{email_signature}',
                'fromoptions' => true,
                'available' => array(
                    'ticket',
                    'client',
                    'staff',
                    'invoice',
                    'estimate',
                    'contract',
                    'tasks',
                    'proposals',
                    'project',
                    'leads',
                    'meetings'
                )
            )
        ),
        'meetings' => array(
            array(
                'name' => 'Name',
                'key' => '{meeting_name}',
                'available' => array(
                    'meetings'
                )
            ),
            array(
                'name' => 'Attendees',
                'key' => '{meeting_attendees}',
                'available' => array(
                    'meetings'
                )
            ),

            array(
                'name' => 'Description',
                'key' => '{meeting_description}',
                'available' => array(
                    'meetings'
                )
            ),
            array(
                'name' => 'Timezone',
                'key' => '{meeting_timezone}',
                'available' => array(
                    'meetings'
                )
            ),
            array(
                'name' => 'Start Date',
                'key' => '{meeting_start_date}',
                'available' => array(
                    'meetings'
                )
            ),
            array(
                'name' => 'End Date',
                'key' => '{meeting_end_date}',
                'available' => array(
                    'meetings'
                )
            )
        ),
        'messages' => array(
            array(
                'name' => 'Message Subject',
                'key' => '{message_subject}',
                'available' => array(
                    'messages'
                )
            ),
            array(
                'name' => 'Message From',
                'key' => '{message_from}',
                'available' => array(
                    'messages'
                )
            ),

            array(
                'name' => 'Message Thread',
                'key' => '{message_thread}',
                'available' => array(
                    'messages'
                )
            ),
            array(
                'name' => 'Message Details',
                'key' => '{message_details}',
                'available' => array(
                    'messages'
                )
            ),
            array(
                'name' => 'Message Privacy',
                'key' => '{message_privacy}',
                'available' => array(
                    'messages'
                )
            )
        )
    );
    $i = 0;
    foreach ($available_merge_fields as $fields) {
        $f = 0;
        // Fix for merge fields as custom fields not matching the names
        foreach ($fields as $key => $_fields) {
            switch ($key) {
                case 'clients':
                    $_key = 'customers';
                    break;
                case 'proposals':
                    $_key = 'proposal';
                    break;
                case 'contract':
                    $_key = 'contracts';
                    break;
                case 'ticket':
                    $_key = 'tickets';
                    break;
                default:
                    $_key = $key;
                    break;
            }

            $custom_fields = get_custom_fields($_key, array(), true);
            foreach ($custom_fields as $field) {
                array_push($available_merge_fields[$i][$key], array(
                    'name' => $field['name'],
                    'key' => '{' . $field['slug'] . '}',
                    'available' => $available_merge_fields[$i][$key][$f]['available']
                ));
            }

            $f++;
        }
        $i++;
    }

    return do_action('available_merge_fields', $available_merge_fields);
}


/**
 * Added By : Vaidehi
 * Dt : 01/08/2017
 * Invite merge fields
 * @param  mixed $id invite id
 * @return array
 */
function get_invite_merge_field($id, $contacttype = '', $type = '')
{

    $CI =& get_instance();
    $fields = array();
    $fields['{vendor_name}'] = '';
    $fields['{vendor_phone}'] = '';
    $fields['{vendor_email}'] = '';
    $fields['{vendor_tag}'] = '';
    $fields['{invited_name}'] = '';
    $fields['{invited_email}'] = '';
    $fields['{project_name}'] = '';
    $fields['{project_type}'] = '';
    $fields['{project_venue}'] = '';
    $fields['{project_start_date_time}'] = '';
    $fields['{project_end_date_time}'] = '';
    $fields['{venue_name}'] = '';
    $fields['{venue_phone}'] = '';
    $fields['{venue_email}'] = '';

    if ($contacttype == 3 || $contacttype == 4) {
        $CI->db->select('tblinvite.*, (SELECT CONCAT(`tblstaff`.`firstname`,  \'  \', `tblstaff`.`lastname`) FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) AS invited_name, (SELECT `tblstaff`.`email` FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) AS invited_email, (SELECT `tblstaff`.`phonenumber` FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) AS invited_phone, IF(`tblinvite`.`firstname` =  \' \', IF(`tblinvite`.`staffid` > 0,(SELECT CONCAT(`tblstaff`.`firstname`,  \'  \', `tblstaff`.`lastname`) FROM `tblstaff` WHERE `staffid` = `tblinvite`.`staffid`), CONCAT(`tbladdressbook`.`firstname`,  \'  \', `tbladdressbook`.`lastname`)), CONCAT(`tblinvite`.`firstname`,  \'  \', `tblinvite`.`lastname`)) AS assigned_name , IF(`tblinvite`.`phone` =  \' \', IF(`tblinvite`.`staffid` > 0,(SELECT `tblstaff`.`phonenumber` FROM `tblstaff` WHERE `staffid` = `tblinvite`.`staffid`), (SELECT `phone` FROM `tbladdressbookphone` WHERE `addressbookid` = `tbladdressbook`.`addressbookid` AND `type` = "primary")), `tblinvite`.`phone`) AS assigned_phone, IF(`tblinvite`.`email` =  \' \', IF(`tblinvite`.`staffid` > 0, (SELECT `tblstaff`.`email` FROM `tblstaff` WHERE `staffid` = `tblinvite`.`staffid`), (SELECT `email` FROM `tbladdressbookemail` WHERE `addressbookid` = `tbladdressbook`.`addressbookid` AND `type` = "primary")), `tblinvite`.`email`) AS assigned_email, IF(`tblinvite`.`tags` =  \' \', IF(`tblinvite`.`staffid` > 0, "staff", (SELECT GROUP_CONCAT(`name`) FROM `tbltags` WHERE `id` IN (SELECT `tagid` FROM `tbladdressbooktags` WHERE `addressbookid` = `tblinvite`.`contactid`))),(SELECT GROUP_CONCAT(`name`) FROM `tbltags` WHERE `id` IN (`tblinvite`.`tags`))) AS assigned_tag, (SELECT `tbleventtype`.`eventtypename` FROM `tbleventtype` WHERE `tbleventtype`.`eventtypeid` = `tblprojects`.`eventtypeid`) AS project_type, DATE_FORMAT(`tblprojects`.`eventstartdatetime`, "%m/%d/%Y") AS project_date, `tblprojects`.`name` AS project_name');
        $CI->db->join('tblstaff', 'tblstaff.staffid = tblinvite.staffid', 'left');
        $CI->db->join('tblprojects', 'tblprojects.id = tblinvite.projectid', 'left');
        $CI->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblinvite.contactid', 'left');
        $CI->db->where('tblinvite.brandid', get_user_session());
        $CI->db->where('tblinvite.inviteid', $id);
        $invite = $CI->db->get('tblinvite')->row();
        if (!$invite) {
            return $fields;
        }
    } else {
        $CI->db->select('tblinvite.*, (SELECT CONCAT(`tblstaff`.`firstname`,  \'  \', `tblstaff`.`lastname`) FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) AS invited_name, (SELECT `tblstaff`.`email` FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) AS invited_email, (SELECT `tblstaff`.`phonenumber` FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) AS invited_phone, (SELECT `tbleventtype`.`eventtypename` FROM `tbleventtype` WHERE `tbleventtype`.`eventtypeid` = `tblprojects`.`eventtypeid`) AS project_type, DATE_FORMAT(`tblprojects`.`eventstartdatetime`, "%m/%d/%Y") AS project_date, `tblprojects`.`name` AS project_name, `tblvenue`.`venuename`, `tblvenue`.`venueemail`, `tblvenue`.`venuephone`');
        $CI->db->join('tblstaff', 'tblstaff.staffid = tblinvite.staffid', 'left');
        $CI->db->join('tblprojects', 'tblprojects.id = tblinvite.projectid', 'left');
        $CI->db->join('tblvenue', 'tblvenue.venueid = tblinvite.venueid', 'left');
        $CI->db->where('tblinvite.brandid', get_user_session());
        $CI->db->where('tblinvite.inviteid', $id);
        $invite = $CI->db->get('tblinvite')->row();
        if (!$invite) {
            return $fields;
        }
    }

    //for vendor
    if ($contacttype == 3) {
        if ($type == 'sent-to-vendor') {
            $fields['{invite_link}'] = base_url('clients/viewinvite/' . $invite->inviteid);
        } else {
            $fields['{invite_link}'] = admin_url('projects/invitedetails/' . $invite->inviteid);
        }
    }

    //for collaborator
    if ($contacttype == 4) {
        if ($type == 'sent-to-vendor') {
            $fields['{invite_link}'] = base_url('clients/viewinvite/' . $invite->inviteid);
        } else {
            $fields['{invite_link}'] = admin_url('projects/invitedetails/' . $invite->inviteid);
        }
    }

    //for venue
    if ($contacttype == 5) {
        if ($type == 'sent-to-vendor') {
            $fields['{invite_link}'] = base_url('clients/viewinvite/' . $invite->inviteid);
        } else {
            $fields['{invite_link}'] = admin_url('projects/invitedetails/' . $invite->inviteid);
        }
    }

    if (isset($invite->assigned_name)) {
        $fields['{vendor_name}'] = $invite->assigned_name;
    }

    if (isset($invite->assigned_phone)) {
        $fields['{vendor_phone}'] = $invite->assigned_phone;
    }

    if (isset($invite->assigned_name)) {
        $fields['{vendor_email}'] = $invite->assigned_email;
    }

    if (isset($invite->venuename)) {
        $fields['{venue_name}'] = $invite->venuename;
    }

    if (isset($invite->venuephone)) {
        if (is_serialized($invite->venuephone)) {
            $venuephone = unserialize($invite->venuephone);
            $fields['{venue_phone}'] = $venuephone[0]['phone'];
        } else {
            $fields['{venue_phone}'] = $invite->venuephone;
        }

    }

    if (isset($invite->venueemail)) {
        if (is_serialized($invite->venueemail)) {
            $venueemail = unserialize($invite->venueemail);
            $fields['{venue_email}'] = $venueemail[0]['email'];
        } else {
            $fields['{venue_email}'] = $invite->venueemail;
        }

    }

    /*if (isset($invite->venuephone)) {
        $fields['{venue_phone}'] = $invite->venuephone;
    }

    if (isset($invite->venuename)) {
        $fields['{venue_email}'] = $invite->venueemail;
    }*/

    //for vendor
    if ($contacttype == 3) {
        if ($type == 'sent-to-vendor') {
            $where = ' AND (`tblinvitestatus`.`status` = "Approved by Account Owner" OR `tblinvitestatus`.`status` = "Sent to Vendor")';
        } else {
            $where = ' AND 1 = 1';
        }
    }

    //for collaborator
    if ($contacttype == 4) {
        if ($type == 'sent-to-vendor') {
            $where = ' AND (`tblinvitestatus`.`status` = "Approved by Account Owner" OR `tblinvitestatus`.`status` = "Sent to Collaborator")';
        } else {
            $where = ' AND 1 = 1';
        }
    }

    //for venue
    if ($contacttype == 5) {
        if ($type == 'sent-to-vendor') {
            $where = ' AND (`tblinvitestatus`.`status` = "Approved by Account Owner" OR `tblinvitestatus`.`status` = "Sent to Venue Owner")';
        } else {
            $where = ' AND 1 = 1';
        }
    }

    //get assoicated tags for vendor and/or collaborator
    if ($contacttype == 3 || $contacttype == 4) {
        if (isset($invite->staffid)) {
            $fields['{vendor_tag}'] = '';
        } else if (isset($invite->contactid)) {
            $tags_query = $CI->db->query('SELECT GROUP_CONCAT(`name`) AS vendor_tags FROM `tbltags` JOIN `tbladdressbooktags` ON `tbladdressbooktags`.`tagid` = `tbltags`.`id` WHERE `deleted` = 0 AND `addressbookid` = ' . $invite->contactid);
            $tags_details = $tags_query->row();
            $fields['{vendor_tag}'] = $tags_details->vendor_tags;
        } else {
            if (!empty($invite->tags)) {
                $tags_query = $CI->db->query('SELECT GROUP_CONCAT(`name`) AS vendor_tags FROM `tbltags` WHERE `deleted` = 0 AND `id` IN (' . $invite->tags . ')');
                $tags_details = $tags_query->row();
                $fields['{vendor_tag}'] = $tags_details->vendor_tags;
            }
        }
    }

    $fields['{invited_name}'] = $invite->invited_name;
    $fields['{invited_email}'] = $invite->invited_email;
    $fields['{invited_phone}'] = $invite->invited_phone;
    $fields['{project_type}'] = $invite->project_type;
    $fields['{project_date}'] = $invite->project_date;
    $fields['{project_name}'] = $invite->project_name;


    $query = $CI->db->query('SELECT `inviteid`, `projectid`, (SELECT `name` FROM `tblprojects` WHERE `id` = `tblinvitestatus`.`projectid`) AS project_name, (SELECT `tbleventtype`.`eventtypename` FROM `tbleventtype` WHERE `tbleventtype`.`eventtypeid` = (SELECT `eventtypeid` FROM `tblprojects` WHERE `id` = `tblinvitestatus`.`projectid`)) as project_type, (SELECT DATE_FORMAT(`eventstartdatetime`, "%m/%d/%Y %H:%i") FROM `tblprojects` WHERE `id` = `tblinvitestatus`.`projectid`) AS eventstartdatetime, (SELECT DATE_FORMAT(`eventenddatetime`, "%m/%d/%Y %H:%i") FROM `tblprojects` WHERE `id` = `tblinvitestatus`.`projectid`) AS eventenddatetime, (SELECT t1.`status` FROM `tblinvitestatus` t1 WHERE t1.`inviteid` = `tblinvitestatus`.`inviteid` AND t1.`projectid` = `tblinvitestatus`.`projectid` ORDER BY t1.`datecreated` DESC LIMIT 0,1) AS status,(SELECT GROUP_CONCAT(`permissionid`) FROM `tbleventpermission` WHERE `inviteid` = `tblinvitestatus`.`inviteid` AND `projectid` = `tblinvitestatus`.`projectid`) AS permission_id,(SELECT GROUP_CONCAT(`name`) FROM `tbleventpermission` JOIN `tblpermissions` ON `tblpermissions`.`permissionid` = `tbleventpermission`.`permissionid` WHERE `inviteid` = `tblinvitestatus`.`inviteid` AND `projectid` = `tblinvitestatus`.`projectid`) AS permission_name FROM `tblinvitestatus` WHERE `inviteid` = ' . $id . $where . ' GROUP BY `tblinvitestatus`.`inviteid`, `tblinvitestatus`.`projectid`');
    $projects = $query->result_array();
    if (!$projects) {
        $fields['{project_details}'] = '';
    } else {
        $event = '<h3>Event details below:</h3><br/><br/>';

        foreach ($projects as $project) {
            $event .= 'Event: ' . $project['project_name'] . '<br/><br/>';
            $event .= 'Type: ' . $project['project_type'] . '<br/><br/>';
            //$event .= 'Venue: ' . $project['project_venue'] . '<br/><br/>';
            $event .= 'From: ' . $project['eventstartdatetime'] . '<br/><br/>';
            $event .= 'To: ' . $project['eventenddatetime'] . '<br/><br/>';
        }

        $fields['{project_details}'] = $event;
    }

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'invite';
    $hook_data['id'] = $id;

    $hook_data = do_action('invite_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * Added By : Vaidehi
 * Dt : 01/10/2017
 * Decline merge fields
 * @param  mixed $id invite id
 * @return array
 */
function get_decline_merge_field($id, $contacttype = '', $type = '')
{
    $CI =& get_instance();
    $fields = array();
    $fields['{vendor_name}'] = '';
    $fields['{venue_name}'] = '';
    $fields['{account_name}'] = '';
    $fields['{name}'] = '';

    if ($contacttype == 3 || $contacttype == 4) {
        $CI->db->select('(SELECT CONCAT(`tblstaff`.`firstname`,  \'  \', `tblstaff`.`lastname`) FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) AS invited_name, (SELECT `tblstaff`.`email` FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) AS invited_email, IF(`tblinvite`.`firstname` =  \' \', IF(`tblinvite`.`staffid` > 0,(SELECT CONCAT(`tblstaff`.`firstname`,  \'  \', `tblstaff`.`lastname`) FROM `tblstaff` WHERE `staffid` = `tblinvite`.`staffid`), CONCAT(`tbladdressbook`.`firstname`,  \'  \', `tbladdressbook`.`lastname`)), CONCAT(`tblinvite`.`firstname`,  \'  \', `tblinvite`.`lastname`)) AS assigned_name');
        $CI->db->join('tblstaff', 'tblstaff.staffid = tblinvite.staffid', 'left');
        $CI->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblinvite.contactid', 'left');
        $CI->db->where('tblinvite.inviteid', $id);
        $invite = $CI->db->get('tblinvite')->row();
        if (!$invite) {
            return $fields;
        }
    }

    if ($contacttype == 5) {
        $CI->db->select('(SELECT CONCAT(`tblstaff`.`firstname`,  \'  \', `tblstaff`.`lastname`) FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) AS invited_name, (SELECT `tblstaff`.`email` FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) AS invited_email, `tblvenue`.`venuename`');
        $CI->db->join('tblstaff', 'tblstaff.staffid = tblinvite.staffid', 'left');
        $CI->db->join('tblvenue', 'tblvenue.venueid = tblinvite.venueid', 'left');
        $CI->db->where('tblinvite.inviteid', $id);
        $invite = $CI->db->get('tblinvite')->row();
        if (!$invite) {
            return $fields;
        }
    }

    if (isset($invite->assigned_name)) {
        $fields['{vendor_name}'] = $invite->assigned_name;
    }

    if (isset($invite->venuename)) {
        $fields['{venue_name}'] = $invite->venuename;
    }

    $fields['{name}'] = $invite->invited_name;
    $fields['{account_name}'] = $invite->invited_email;

    //for vendor
    if ($contacttype == 3) {
        if ($type == 'sent-by-vendor') {
            $where = ' AND (`tblinvitestatus`.`status` = "declined")';
        } else {
            $where = ' AND (`tblinvitestatus`.`status` = "declined")';
        }
    }

    //for collaborator
    if ($contacttype == 4) {
        if ($type == 'sent-by-vendor') {
            $where = ' AND (`tblinvitestatus`.`status` = "declined")';
        } else {
            $where = ' AND (`tblinvitestatus`.`status` = "declined")';
        }
    }

    //for venue
    if ($contacttype == 5) {
        if ($type == 'sent-by-vendor') {
            $where = ' AND (`tblinvitestatus`.`status` = "declined")';
        } else {
            $where = ' AND (`tblinvitestatus`.`status` = "declined")';
        }
    }

    $query = $CI->db->query('SELECT `inviteid`, `projectid`, (SELECT `name` FROM `tblprojects` WHERE `id` = `tblinvitestatus`.`projectid`) AS project_name, `comments` FROM `tblinvitestatus` WHERE `inviteid` = ' . $id . $where . ' GROUP BY `tblinvitestatus`.`inviteid`, `tblinvitestatus`.`projectid`');
    $projects = $query->result_array();
    if (!$projects) {
        $fields['{project_details}'] = '';
    } else {
        $event = '<h3>Event details below:</h3><br/><br/>';

        foreach ($projects as $project) {
            $event .= 'Event: ' . $project['project_name'] . '<br/><br/>';
            $event .= 'Comments: ' . $project['comments'] . '<br/><br/>';
        }

        $fields['{project_details}'] = $event;
    }

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'invite';
    $hook_data['id'] = $id;

    $hook_data = do_action('invite_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * Added By : Vaidehi
 * Dt : 02/20/2017
 * Add venue merge fields
 * @param  mixed $id venue id
 * @return array
 */
function get_venue_merge_field($id)
{
    $CI =& get_instance();
    $fields = array();
    $fields['{venue_details}'] = '';

    $CI->db->select('tblvenue.*, tblstaff.firstname');
    $CI->db->join('tblstaff', 'tblstaff.staffid = tblvenue.created_by', 'left');
    $CI->db->where('tblvenue.venueid', $id);
    $venue = $CI->db->get('tblvenue')->row();
    if (!$venue) {
        return $fields;
    }

    $venue_details = '<b>Name: </b>' . $venue->venuename . '<br/>';

    $fields['{venue_name}'] = $venue->venuename;

    if (!empty($venue->venueemail)) {
        if(is_serialized($venue->venueemail)){
            $venueemail=unserialize($venue->venueemail);
            $venue->venueemail =$venueemail[0]['email'];
        }
        $venue_details .= '<b> Email Address: </b>' . $venue->venueemail . '<br/>';
    }

    if (!empty($venue->venuephone)) {
        if (is_serialized($venue->venuephone)) {
            $venuephone = unserialize($venue->venuephone);
            $venue->venuephone= $venuephone[0]['phone'];
            if(!empty($venuephone['ext']) && $venuephone[0]['ext']!=""){
                $venue->venuephone.= "  x".$venuephone[0]['ext'];
            }
        }
        $venue_details .= '<b> Phone Number: </b>' . $venue->venuephone . '<br/>';
    }

    if (!empty($venue->venueaddress)) {
        $venue_details .= '<b> Address: </b>' . $venue->venueaddress . '<br/>';
    }

    if (!empty($venue->venueaddress2)) {
        $venue_details .= '<b> Address: </b>' . $venue->venueaddress2 . '<br/>';
    }

    if (!empty($venue->venuecity)) {
        $venue_details .= '<b> City: </b>' . $venue->venuecity . '<br/>';
    }

    if (!empty($venue->venuestate)) {
        $venue_details .= '<b> State: </b>' . $venue->venuestate . '<br/>';
    }

    if (!empty($venue->venuecountry) && $venue->venuecountry == 236) {
        $venue_details .= '<b> Country: </b> United States <br/>';
    }

    if (!empty($venue->venuezip)) {
        $venue_details .= '<b> Zip/Postal Code: </b>' . $venue->venuezip . '<br/>';
    }

    $fields['{venue_details}'] = $venue_details;

    $fields['{venue_link}'] = admin_url('venues/venue/' . $venue->venueid);

    $get_admins = $CI->db->query('SELECT `staffid`, `email`, `firstname`, `lastname`, `phonenumber` FROM `tblstaff` WHERE `is_sido_admin` = 1');
    $admins = $get_admins->result_array();
    if (!$admins) {
        $fields['{contact_details}'] = '';
    } else {
        $contact_details = '<h3>Contact details below:</h3><br/><br/>';

        foreach ($admins as $admin) {
            $contact_details .= 'Name: ' . $admin['firstname'] . $admin['lastname'] . '<br/><br/>';
            $contact_details .= 'Email: ' . $admin['email'] . '<br/><br/>';
            $contact_details .= 'Phone: ' . $admin['phonenumber'] . '<br/><br/>';
        }

        $fields['{contact_details}'] = $contact_details;
    }

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'venue';
    $hook_data['id'] = $id;

    $hook_data = do_action('venue_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * Added By : Vaidehi
 * Dt : 03/04/2017
 * Add task reminder merge fields
 * @param  mixed $task task
 * @return array
 */
function get_task_reminder_merge_field($task)
{
    if ($task['rel_type'] == 'lead') {
        $CI =& get_instance();
        $CI->db->select('tblleads.*');
        $CI->db->where('tblleads.id', $task['rel_id']);
        $CI->db->where('tblleads.deleted', 0);
        $lead = $CI->db->get('tblleads')->row();
    }

    if ($task['rel_type'] == 'project' || $task['rel_type'] == 'event') {
        $CI =& get_instance();
        $CI->db->select('tblprojects.*');
        $CI->db->where('tblprojects.id', $task['rel_id']);
        $CI->db->where('tblprojects.deleted', 0);
        $project = $CI->db->get('tblprojects')->row();
    }

    $fields = array();

    $fields['{task_name}'] = $task['name'];
    $fields['{name}'] = $task['firstname'];
    $fields['{assigned_name}'] = $task['assigned_firstname'] . ' ' . $task['assigned_lastname'];

    $fields['{task_details}'] = '';


    $task_details = '<b>Name: </b>' . $task['name'] . '<br/>';

    $task_details .= '<b>Due Date: </b>' . $task['due_date'] . '<br/>';
    $task_details .= '<b>Assigned To: </b>' . $task['firstname'] . ' ' . $task['lastname'] . '<br/>';
    $task_details .= '<b>Assigned By: </b>' . $task['assigned_firstname'] . ' ' . $task['assigned_lastname'] . '<br/>';

    if ($task['priority'] == 1) {
        $priority = 'Low';
    } else if ($task['priority'] == 1) {
        $priority = 'Medium';
    } else if ($task['priority'] == 1) {
        $priority = 'High';
    } else {
        $priority = 'Urgent';
    }

    $task_details .= '<b>Priority: </b>' . $priority . '<br/>';

    if ($task['rel_type'] == 'lead') {
        $task_details .= '<h4>Lead Details</h4>';

        $task_details .= '<b>Lead Name: </b>' . $lead->name . '<br/>';
    }

    if ($task['rel_type'] == 'project') {
        $task_details .= '<h4>Project Details</h4>';

        $task_details .= '<b>Project Name: </b>' . $project->name . '<br/>';
    }

    if ($task['rel_type'] == 'event') {
        $task_details .= '<h4>Event Details</h4>';

        $task_details .= '<b>Event Name: </b>' . $project->name . '<br/>';
    }

    $fields['{task_details}'] = $task_details;

    $fields['{task_link}'] = admin_url('tasks/dashboard/' . $task['id']);

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'task-reminder';
    $hook_data['id'] = $task['id'];

    $hook_data = do_action('task_reminder_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * Added By : Vaidehi
 * Dt : 03/04/2017
 * Add meeting reminder merge fields
 * @param  mixed $meeting meeting
 * @return array
 */
function get_meeting_reminder_merge_field($meeting)
{
    if ($meeting['rel_type'] == 'lead') {
        $CI =& get_instance();
        $CI->db->select('tblleads.*');
        $CI->db->where('tblleads.id', $meeting['rel_id']);
        $CI->db->where('tblleads.deleted', 0);
        $lead = $CI->db->get('tblleads')->row();
    }

    if ($meeting['rel_type'] == 'project' || $meeting['rel_type'] == 'event') {
        $CI =& get_instance();
        $CI->db->select('tblprojects.*');
        $CI->db->where('tblprojects.id', $meeting['rel_id']);
        $CI->db->where('tblprojects.deleted', 0);
        $project = $CI->db->get('tblprojects')->row();
    }

    $fields = array();

    $fields['{meeting_name}'] = $meeting['name'];
    if (!empty($meeting['contact_id'])) {
        $fields['{name}'] = $meeting['contactfirstname'];
    } else {
        $fields['{name}'] = $meeting['firstname'];
    }
    $fields['{assigned_name}'] = $meeting['assigned_firstname'] . ' ' . $meeting['assigned_lastname'];

    $fields['{meeting_details}'] = '';


    $meeting_details = '<b>Name: </b>' . $meeting['name'] . '<br/>';

    $meeting_details .= '<b>From: </b>' . $meeting['startdate'] . '<br/>';
    $meeting_details .= '<b>To: </b>' . $meeting['enddate'] . '<br/>';
    $meeting_details .= '<b>Location: </b>' . $meeting['location'] . '<br/>';
    $meeting_details .= '<b>Assigned By: </b>' . $meeting['assigned_firstname'] . ' ' . $meeting['assigned_lastname'] . '<br/>';

    if ($meeting['status'] == 1) {
        $status = 'Confirmed';
    } else if ($meeting['status'] == 2) {
        $status = 'Tentative';
    } else {
        $status = 'Not Held';
    }

    $meeting_details .= '<b>Staus: </b>' . $status . '<br/>';

    if ($meeting['rel_type'] == 'lead') {
        $meeting_details .= '<h4>Lead Details</h4>';

        $meeting_details .= '<b>Lead Name: </b>' . $lead->name . '<br/>';
    }

    if ($meeting['rel_type'] == 'project') {
        $meeting_details .= '<h4>Project Details</h4>';

        $meeting_details .= '<b>Project Name: </b>' . $project->name . '<br/>';
    }

    if ($meeting['rel_type'] == 'event') {
        $meeting_details .= '<h4>Event Details</h4>';

        $meeting_details .= '<b>Event Name: </b>' . $project->name . '<br/>';
    }

    $fields['{meeting_details}'] = $meeting_details;

    $fields['{meeting_link}'] = admin_url('meetings/meeting/' . $meeting['meetingid']);

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'meeting-reminder';
    $hook_data['id'] = $meeting['meetingid'];

    $hook_data = do_action('meeting_reminder_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * Added By : Vaidehi
 * Dt : 03/05/2017
 * Add event reminder merge fields
 * @param  mixed $event event
 * @return array
 */
function get_event_reminder_merge_field($event, $remindertype = '', $invite = array())
{
    $fields = array();

    $fields['{event_name}'] = $event['name'];

    if ($remindertype == 'staff') {
        $fields['{name}'] = $event['firstname'];
    } else if ($remindertype == 'vendors' || $remindertype == 'collaborators') {
        $fields['{name}'] = $invite['firstname'];
    } else if ($remindertype == 'venues') {
        $fields['{name}'] = $invite['venuename'];
    } else if ($remindertype == 'contact') {
        $fields['{name}'] = $event['contactfirstname'];
    }

    $fields['{assigned_name}'] = $event['assigned_firstname'] . ' ' . $event['assigned_lastname'];

    $fields['{event_details}'] = '';


    $event_details = '<b>Name: </b>' . $event['name'] . '<br/>';

    $event_details .= '<b>From: </b>' . $event['startdatetime'] . '<br/>';
    $event_details .= '<b>To: </b>' . $event['enddatetime'] . '<br/>';
    $event_details .= '<b>Location Name: </b>' . $event['venuename'] . '<br/>';
    $event_details .= '<b>Location: </b>' . $event['location'] . '<br/>';
    $event_details .= '<b>Assigned By: </b>' . $event['assigned_firstname'] . ' ' . $event['assigned_lastname'] . '<br/>';

    $fields['{event_details}'] = $event_details;

    $fields['{event_link}'] = admin_url('projects/dashboard/' . $event['id']);

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'event-reminder';
    $hook_data['id'] = $event['id'];

    $hook_data = do_action('event_reminder_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * Added By : Vaidehi
 * Dt : 03/05/2017
 * Add subscription reminder merge fields
 * @param  mixed $subscription subscription
 * @return array
 */
function get_subscription_reminder_merge_field($subscription)
{
    $fields = array();

    $fields['{name}'] = $subscription['firstname'];

    $fields['{subscription_details}'] = '';

    $subscription_details = '<b>Package Name: </b>' . $subscription['name'] . '<br/>';

    $subscription_details .= '<b>Trial Periods (in days): </b>' . $subscription['trial_period'] . '<br/>';

    $fields['{subscription_details}'] = $subscription_details;

    //$fields['{meeting_link}']                = admin_url('meetings/meeting/' . $meeting['meetingid']);
    $fields['{subscription_link}'] = '';

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'subscription-reminder';
    $hook_data['id'] = $subscription['staffid'];

    $hook_data = do_action('meeting_reminder_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

function get_agreement_merge_fields()
{
    $available_merge_fields = array(
        'Team Member' => array(
            array(
                'name' => 'Firstname',
                'key' => '{teamember_firstname}',
                'available' => array(
                    'staff',
                    'tasks',
                    'project'
                )
            ),
            array(
                'name' => 'Lastname',
                'key' => '{teamember_lastname}',
                'available' => array(
                    'staff',
                    'tasks',
                    'project'
                )
            ),
            array(
                'name' => 'Fullname',
                'key' => '{teamember_fullname}',
                'available' => array(
                    'staff',
                    'tasks',
                    'project'
                )
            ),
            array(
                'name' => 'Email',
                'key' => '{teamember_email}',
                'available' => array(
                    'staff',
                    'project'
                )
            ),
            /*array(
                'name' => 'Date Created',
                'key' => '{teamember_date_created}',
                'available' => array(
                    'staff'
                )
            ),*/
        ),
        'clients' => array(
            array(
                'name' => 'Firstname',
                'key' => '{contact_firstname}',
                'available' => array(
                    'client',
                    'ticket',
                    'invoice',
                    'estimate',
                    'contract',
                    'project',
                    'tasks'
                )
            ),
            array(
                'name' => 'Lastname',
                'key' => '{contact_lastname}',
                'available' => array(
                    'client',
                    'ticket',
                    'invoice',
                    'estimate',
                    'contract',
                    'project',
                    'tasks'
                )
            ),
            array(
                'name' => 'Fullname',
                'key' => '{contact_fullname}',
                'available' => array(
                    'client',
                    'ticket',
                    'invoice',
                    'estimate',
                    'contract',
                    'project',
                    'tasks'
                )
            ),
            array(
                'name' => 'Email',
                'key' => '{contact_email}',
                'available' => array(
                    'client',
                    'invoice',
                    'estimate',
                    'ticket',
                    'contract',
                    'project'
                )
            ),
        ),
        'tasks' => array(
            array(
                'name' => 'Task Link',
                'key' => '{task_link}',
                'available' => array(
                    'tasks'
                )
            ),
            array(
                'name' => 'Comment Link',
                'key' => '{comment_link}',
                'available' => array(
                    'tasks'
                )
            ),
            array(
                'name' => 'Task Name',
                'key' => '{task_name}',
                'available' => array(
                    'tasks'
                )
            ),
            array(
                'name' => 'Task Description',
                'key' => '{task_description}',
                'available' => array(
                    'tasks'
                )
            ),
            array(
                'name' => 'Task Status',
                'key' => '{task_status}',
                'available' => array(
                    'tasks'
                )
            ),
            array(
                'name' => 'Task Comment',
                'key' => '{task_comment}',
                'available' => array(
                    'tasks'
                )
            ),
            array(
                'name' => 'Task Priority',
                'key' => '{task_priority}',
                'available' => array(
                    'tasks'
                )
            ),
            /*array(
                'name' => 'Task Start Date',
                'key' => '{task_star_tdate}',
                'available' => array(
                    'tasks'
                )
            ),*/
            array(
                'name' => 'Task Due Date',
                'key' => '{task_due_date}',
                'available' => array(
                    'tasks'
                )
            ),
            array(
                'name' => 'Task Related To',
                'key' => '{task_related}',
                'available' => array(
                    'tasks'
                )
            )
        ),
        'proposals' => array(
            array(
                'name' => 'Proposal Version',
                'key' => '{proposal_version}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'Proposal Name',
                'key' => '{proposal_name}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'Proposal Open Till',
                'key' => '{proposal_open_till}',
                'available' => array(
                    'proposals'
                )
            ),
            array(
                'name' => 'Proposal Link',
                'key' => '{proposal_link}',
                'available' => array(
                    'proposals'
                )
            ),

            /*array(
                'name' => 'Proposal Assigned',
                'key' => '{proposal_assigned}',
                'available' => array(
                    'proposals'
                )
            ),*/
            /*array(
                'name' => 'Proposal Link',
                'key' => '{proposal_link}',
                'available' => array(
                    'proposals'
                )
            )*/
        ),
        'leads' => array(
            array(
                'name' => 'Lead Type',
                'key' => '{lead_type}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead Name',
                'key' => '{lead_name}',
                'available' => array(
                    'leads'
                )
            ),
            /*array(
                'name' => 'Lead Email',
                'key' => '{lead_email}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead Website',
                'key' => '{lead_website}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead Phone Number',
                'key' => '{lead_phone_number}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead Company',
                'key' => '{lead_company}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead Country',
                'key' => '{lead_country}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead Zip',
                'key' => '{lead_zip}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead City',
                'key' => '{lead_city}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead State',
                'key' => '{lead_state}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead Address',
                'key' => '{lead_address}',
                'available' => array(
                    'leads'
                )
            ),*/
            array(
                'name' => 'Lead Assigned',
                'key' => '{lead_assigned}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead Start Date',
                'key' => '{lead_startdate}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead End Date',
                'key' => '{lead_enddate}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead Start Time',
                'key' => '{lead_starttime}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead End Time',
                'key' => '{lead_endtime}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead Total Time',
                'key' => '{lead_totaltime}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead Status',
                'key' => '{lead_status}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead Source',
                'key' => '{lead_source}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead Link',
                'key' => '{lead_link}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Lead Dashboard Button',
                'key' => '{lead_dashboardbutton}',
                'available' => array(
                    'leads'
                )
            )
        ),
        'projects' => array(
            array(
                'name' => 'Project Type',
                'key' => '{project_type}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Project Name',
                'key' => '{project_name}',
                'available' => array(
                    'project'
                )
            ),
            /*array(
                'name' => 'Project Description',
                'key' => '{project_description}',
                'available' => array(
                    'project'
                )
            ),*/
            array(
                'name' => 'Project Assigned',
                'key' => '{project_assigned}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Project Start Date',
                'key' => '{project_start_date}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Project Deadline',
                'key' => '{project_deadline}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Project Start Time',
                'key' => '{project_starttime}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Project End Time',
                'key' => '{project_endtime}',
                'available' => array(
                    'leads'
                )
            ),
            array(
                'name' => 'Project Total Time',
                'key' => '{project_totaltime}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Project Budget',
                'key' => '{project_budget}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Project Link',
                'key' => '{project_link}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Project Source',
                'key' => '{project_source}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Project Dashboard Button',
                'key' => '{project_dashboardbutton}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Project Image',
                'key' => '{project_image}',
                'available' => array(
                    'project'
                )
            )
            /*array(
                'name' => 'Discussion Link',
                'key' => '{discussion_link}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'File Creator',
                'key' => '{file_creator}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Discussion Creator',
                'key' => '{discussion_creator}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Comment Creator',
                'key' => '{comment_creator}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Discussion Subject',
                'key' => '{discussion_subject}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Discussion Description',
                'key' => '{discussion_description}',
                'available' => array(
                    'project'
                )
            ),
            array(
                'name' => 'Discussion Comment',
                'key' => '{discussion_comment}',
                'available' => array(
                    'project'
                )
            )*/
        ),
        'meetings' => array(
            array(
                'name' => 'Name',
                'key' => '{meeting_name}',
                'available' => array(
                    'meetings'
                )
            ),
            array(
                'name' => 'Attendees',
                'key' => '{meeting_attendees}',
                'available' => array(
                    'meetings'
                )
            ),

            array(
                'name' => 'Description',
                'key' => '{meeting_description}',
                'available' => array(
                    'meetings'
                )
            ),
            array(
                'name' => 'Timezone',
                'key' => '{meeting_timezone}',
                'available' => array(
                    'meetings'
                )
            ),
            array(
                'name' => 'Start Date',
                'key' => '{meeting_start_date}',
                'available' => array(
                    'meetings'
                )
            ),
            array(
                'name' => 'End Date',
                'key' => '{meeting_end_date}',
                'available' => array(
                    'meetings'
                )
            )
        ),
        'other' => array(
            array(
                'name' => 'Logo Image',
                'key' => '{logo_image_with_url}',
                'fromoptions' => true,
                'available' => array(
                    'ticket',
                    'client',
                    'staff',
                    'invoice',
                    'estimate',
                    'contract',
                    'tasks',
                    'proposals',
                    'project',
                    'leads',
                    'meetings'
                )
            ),
            array(
                'name' => 'Client URL',
                'key' => '{client_url}',
                'fromoptions' => true,
                'available' => array(
                    'ticket',
                    'client',
                    'staff',
                    'invoice',
                    'estimate',
                    'contract',
                    'tasks',
                    'proposals',
                    'project',
                    'leads',
                    'meetings'
                )
            ),
            array(
                'name' => 'Portal URL',
                'key' => '{portal_url}',
                'fromoptions' => true,
                'available' => array(
                    'ticket',
                    'client',
                    'staff',
                    'invoice',
                    'estimate',
                    'contract',
                    'tasks',
                    'proposals',
                    'project',
                    'leads',
                    'meetings'
                )
            ),
            array(
                'name' => 'Company Name',
                'key' => '{companyname}',
                'fromoptions' => true,
                'available' => array(
                    'ticket',
                    'client',
                    'staff',
                    'invoice',
                    'estimate',
                    'contract',
                    'tasks',
                    'proposals',
                    'project',
                    'leads',
                    'meetings'
                )
            ),
            array(
                'name' => 'Email Signature',
                'key' => '{email_signature}',
                'fromoptions' => true,
                'available' => array(
                    'ticket',
                    'client',
                    'staff',
                    'invoice',
                    'estimate',
                    'contract',
                    'tasks',
                    'proposals',
                    'project',
                    'leads',
                    'meetings'
                )
            )
        )
    );
    $i = 0;
    foreach ($available_merge_fields as $fields) {
        $f = 0;
        // Fix for merge fields as custom fields not matching the names
        foreach ($fields as $key => $_fields) {
            switch ($key) {
                case 'clients':
                    $_key = 'customers';
                    break;
                case 'proposals':
                    $_key = 'proposal';
                    break;
                default:
                    $_key = $key;
                    break;
            }

            $custom_fields = get_custom_fields($_key, array(), true);
            foreach ($custom_fields as $field) {
                array_push($available_merge_fields[$i][$key], array(
                    'name' => $field['name'],
                    'key' => '{' . $field['slug'] . '}',
                    'available' => $available_merge_fields[$i][$key][$f]['available']
                ));
            }

            $f++;
        }
        $i++;
    }

    return do_action('available_merge_fields', $available_merge_fields);
}

function get_agreement_other_merge_fields()
{
    $CI =& get_instance();
    $fields = array();
    $fields['{logo_url}'] = base_url('uploads/company/' . get_brand_option('company_logo'));

    $logo_width = do_action('merge_field_logo_img_width', '');
    $fields['{logo_image}'] = '<a href="' . site_url() . '" target="_blank"><img src="' . base_url('uploads/company/' . get_brand_option('company_logo')) . '"' . ($logo_width != '' ? ' width="' . $logo_width . '"' : '') . '></a>';

    $fields['{crm_url}'] = site_url();
    $fields['{admin_url}'] = admin_url();

    /**
     * Added By : Vaidehi
     * Dt : 12/05/2017
     * to get brand wise details with new variables
     */
    $fields['{client_url}'] = site_url();
    $fields['{portal_url}'] = admin_url();

    $session_data = get_session_data();
    /**
     * Added By : Vaidehi
     * Dt : 01/08/2018
     * to check if session exists or not
     */
    if (isset($session_data['is_sido_admin'])) {
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
    } else {
        $is_sido_admin = 1;
        $is_admin = 1;
    }

    if ($is_sido_admin == 0 && $is_admin == 0) {
        $fields['{main_domain}'] = get_brand_option('main_domain');
        $fields['{companyname}'] = get_brand_option('companyname');
    } else {
        $fields['{main_domain}'] = get_option('main_domain');
        $fields['{companyname}'] = get_option('companyname');
    }

    if (!is_staff_logged_in() || is_client_logged_in()) {
        $fields['{email_signature}'] = get_option('email_signature');
    } else {
        $CI->db->select('email_signature')->from('tblstaff')->where('staffid', get_staff_user_id());
        $signature = $CI->db->get()->row()->email_signature;
        if (empty($signature)) {
            if ($is_sido_admin == 0 && $is_admin == 0) {
                $fields['{email_signature}'] = get_brand_option('email_signature');
            } else {
                $fields['{email_signature}'] = get_option('email_signature');
            }
        } else {
            $fields['{email_signature}'] = $signature;
        }
    }

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'other';
    $hook_data['id'] = '';

    $hook_data = do_action('other_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

function get_agreement_meetings_merge_fields($rel_type, $rel_id)
{
    $fields = array();
    $fields = array();
    $fields['{meeting_name}'] = "TBD";
    $fields['{meeting_description}'] = "TBD";
    $fields['{meeting_timezone}'] = "TBD";
    $fields['{meeting_zone}'] = "TBD";
    $fields['{meeting_start_date}'] = "TBD";
    $fields['{meeting_end_date}'] = "TBD";
    $fields['{meeting_start}'] = "TBD";
    $fields['{meeting_end}'] = "TBD";
    $fields['{meeting_attendees}'] = "TBD";
    $CI =& get_instance();
    $CI->db->where('rel_type', $rel_type);
    $CI->db->where('rel_id', $rel_id);
    $meeting = $CI->db->get('tblmeetings')->row();
    if (!$meeting) {
        return $fields;
    }
    $assignees = get_meeting_assignee($meeting->meetingid);
    $fields['{meeting_attendees}'] = "TBD";
    if (!empty($assignees)) {
        $members = implode(', ', $assignees['member']);
        $clients = implode(', ', $assignees['client']);
        $fields['{meeting_attendees}'] = "";
        if (!empty($members)) {
            $fields['{meeting_attendees}'] .= "Members: " . $members;
        }
        if (!empty($clients)) {
            $fields['{meeting_attendees}'] .= " Clients:" . $clients;
        }
    }

    $fields['{meeting_name}'] = $meeting->name;
    $fields['{meeting_description}'] = $meeting->description;
    $fields['{meeting_timezone}'] = $meeting->default_timezone;
    $fields['{meeting_zone}'] = $meeting->default_timezone;
    $fields['{meeting_start_date}'] = $meeting->start_date;
    $fields['{meeting_end_date}'] = $meeting->end_date;
    $fields['{meeting_start}'] = _dt($meeting->start_date, true);
    $fields['{meeting_end}'] = _dt($meeting->end_date, true);
    $fields['{logo_url}'] = base_url('uploads/company/' . get_option('company_logo'));

    $logo_width = do_action('merge_field_logo_img_width', '');
    //$fields['{logo_image_with_url}'] = '<a href="' . site_url() . '" target="_blank"><img src="' . base_url('uploads/company/' . get_option('company_logo')) . '"' . ($logo_width != '' ? ' width="' . $logo_width . '"' : '') . '></a>';

    $fields['{crm_url}'] = site_url();
    $fields['{admin_url}'] = admin_url();

    /**
     * Added By : Vaidehi
     * Dt : 12/05/2017
     * to get brand wise details with new variables
     */
    $fields['{client_url}'] = site_url();
    $fields['{portal_url}'] = admin_url();
    //$fields['{logo}'] = '<a href="' . site_url() . '" target="_blank"><img src="' . base_url('uploads/company/' . get_option('company_logo')) . '"' . ($logo_width != '' ? ' width="' . $logo_width . '"' : '') . '></a>';

    $session_data = get_session_data();
    $is_sido_admin = $session_data['is_sido_admin'];
    $is_admin = $session_data['is_admin'];

    if ($is_sido_admin == 0 && $is_admin == 0) {
        $fields['{main_domain}'] = get_brand_option('main_domain');
        $fields['{companyname}'] = get_brand_option('companyname');
    } else {
        $fields['{main_domain}'] = get_option('main_domain');
        $fields['{companyname}'] = get_option('companyname');
    }

    if (!is_staff_logged_in() || is_client_logged_in()) {
        $fields['{email_signature}'] = get_option('email_signature');
    } else {
        $CI->db->select('email_signature')->from('tblstaff')->where('staffid', get_staff_user_id());
        $signature = $CI->db->get()->row()->email_signature;
        if (empty($signature)) {
            if ($is_sido_admin == 0 && $is_admin == 0) {
                $fields['{email_signature}'] = get_brand_option('email_signature');
            } else {
                $fields['{email_signature}'] = get_option('email_signature');
            }
        } else {
            $fields['{email_signature}'] = $signature;
        }
    }
    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'meetings';
    $hook_data['id'] = $meeting->meetingid;

    $hook_data = do_action('estimate_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}

/**
 * Added By : Vaidehi
 * Dt : 01/08/2017
 * Invite merge fields
 * @param  mixed $id invite id
 * @return array
 */
function get_client_merge_field($id, $contacttype = '', $type = '')
{
    $CI =& get_instance();
    $fields = array();
    $fields['{vendor_name}'] = '';
    $fields['{vendor_phone}'] = '';
    $fields['{vendor_email}'] = '';
    $fields['{vendor_tag}'] = '';
    $fields['{invited_name}'] = '';
    $fields['{invited_email}'] = '';
    $fields['{project_name}'] = '';
    $fields['{project_type}'] = '';
    $fields['{project_venue}'] = '';
    $fields['{project_start_date_time}'] = '';
    $fields['{project_end_date_time}'] = '';
    $fields['{venue_name}'] = '';
    $fields['{venue_phone}'] = '';
    $fields['{venue_email}'] = '';

    if ($contacttype == 3 || $contacttype == 4) {
        $CI->db->select('tblinvite.*, (SELECT CONCAT(`tblstaff`.`firstname`,  \'  \', `tblstaff`.`lastname`) FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) AS invited_name, (SELECT `tblstaff`.`email` FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) AS invited_email, (SELECT `tblstaff`.`phonenumber` FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) AS invited_phone, IF(`tblinvite`.`firstname` =  \' \', IF(`tblinvite`.`staffid` > 0,(SELECT CONCAT(`tblstaff`.`firstname`,  \'  \', `tblstaff`.`lastname`) FROM `tblstaff` WHERE `staffid` = `tblinvite`.`staffid`), CONCAT(`tbladdressbook`.`firstname`,  \'  \', `tbladdressbook`.`lastname`)), CONCAT(`tblinvite`.`firstname`,  \'  \', `tblinvite`.`lastname`)) AS assigned_name , IF(`tblinvite`.`phone` =  \' \', IF(`tblinvite`.`staffid` > 0,(SELECT `tblstaff`.`phonenumber` FROM `tblstaff` WHERE `staffid` = `tblinvite`.`staffid`), (SELECT `phone` FROM `tbladdressbookphone` WHERE `addressbookid` = `tbladdressbook`.`addressbookid` AND `type` = "primary")), `tblinvite`.`phone`) AS assigned_phone, IF(`tblinvite`.`email` =  \' \', IF(`tblinvite`.`staffid` > 0, (SELECT `tblstaff`.`email` FROM `tblstaff` WHERE `staffid` = `tblinvite`.`staffid`), (SELECT `email` FROM `tbladdressbookemail` WHERE `addressbookid` = `tbladdressbook`.`addressbookid` AND `type` = "primary")), `tblinvite`.`email`) AS assigned_email, IF(`tblinvite`.`tags` =  \' \', IF(`tblinvite`.`staffid` > 0, "staff", (SELECT GROUP_CONCAT(`name`) FROM `tbltags` WHERE `id` IN (SELECT `tagid` FROM `tbladdressbooktags` WHERE `addressbookid` = `tblinvite`.`contactid`))),(SELECT GROUP_CONCAT(`name`) FROM `tbltags` WHERE `id` IN (`tblinvite`.`tags`))) AS assigned_tag, (SELECT `tbleventtype`.`eventtypename` FROM `tbleventtype` WHERE `tbleventtype`.`eventtypeid` = `tblprojects`.`eventtypeid`) AS project_type, DATE_FORMAT(`tblprojects`.`eventstartdatetime`, "%m/%d/%Y") AS project_date, `tblprojects`.`name` AS project_name');
        $CI->db->join('tblstaff', 'tblstaff.staffid = tblinvite.staffid', 'left');
        $CI->db->join('tblprojects', 'tblprojects.id = tblinvite.projectid', 'left');
        $CI->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblinvite.contactid', 'left');
        $CI->db->where('tblinvite.brandid', get_user_session());
        $CI->db->where('tblinvite.inviteid', $id);
        $invite = $CI->db->get('tblinvite')->row();
        if (!$invite) {
            return $fields;
        }
    } else {
        $CI->db->select('tblinvite.*, (SELECT CONCAT(`tblstaff`.`firstname`,  \'  \', `tblstaff`.`lastname`) FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) AS invited_name, (SELECT `tblstaff`.`email` FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) AS invited_email, (SELECT `tblstaff`.`phonenumber` FROM `tblstaff` WHERE `staffid` = `tblinvite`.`invitedby`) AS invited_phone, (SELECT `tbleventtype`.`eventtypename` FROM `tbleventtype` WHERE `tbleventtype`.`eventtypeid` = `tblprojects`.`eventtypeid`) AS project_type, DATE_FORMAT(`tblprojects`.`eventstartdatetime`, "%m/%d/%Y") AS project_date, `tblprojects`.`name` AS project_name, `tblvenue`.`venuename`, `tblvenue`.`venueemail`, `tblvenue`.`venuephone`');
        $CI->db->join('tblstaff', 'tblstaff.staffid = tblinvite.staffid', 'left');
        $CI->db->join('tblprojects', 'tblprojects.id = tblinvite.projectid', 'left');
        $CI->db->join('tblvenue', 'tblvenue.venueid = tblinvite.venueid', 'left');
        $CI->db->where('tblinvite.brandid', get_user_session());
        $CI->db->where('tblinvite.inviteid', $id);
        $invite = $CI->db->get('tblinvite')->row();
        if (!$invite) {
            return $fields;
        }
    }

    //for vendor
    if ($contacttype == 3) {
        if ($type == 'sent-to-vendor') {
            $fields['{invite_link}'] = base_url('clients/viewinvite/' . $invite->inviteid);
        } else {
            $fields['{invite_link}'] = admin_url('projects/invitedetails/' . $invite->inviteid);
        }
    }

    //for collaborator
    if ($contacttype == 4) {
        if ($type == 'sent-to-vendor') {
            $fields['{invite_link}'] = base_url('clients/viewinvite/' . $invite->inviteid);
        } else {
            $fields['{invite_link}'] = admin_url('projects/invitedetails/' . $invite->inviteid);
        }
    }

    //for venue
    if ($contacttype == 5) {
        if ($type == 'sent-to-vendor') {
            $fields['{invite_link}'] = base_url('clients/viewinvite/' . $invite->inviteid);
        } else {
            $fields['{invite_link}'] = admin_url('projects/invitedetails/' . $invite->inviteid);
        }
    }

    if (isset($invite->assigned_name)) {
        $fields['{vendor_name}'] = $invite->assigned_name;
    }

    if (isset($invite->assigned_phone)) {
        $fields['{vendor_phone}'] = $invite->assigned_phone;
    }

    if (isset($invite->assigned_name)) {
        $fields['{vendor_email}'] = $invite->assigned_email;
    }

    if (isset($invite->venuename)) {
        $fields['{venue_name}'] = $invite->venuename;
    }

    if (isset($invite->venuephone)) {
        $fields['{venue_phone}'] = $invite->venuephone;
    }

    if (isset($invite->venuename)) {
        $fields['{venue_email}'] = $invite->venueemail;
    }

    //for vendor
    if ($contacttype == 3) {
        if ($type == 'sent-to-vendor') {
            $where = ' AND (`tblinvitestatus`.`status` = "Approved by Account Owner" OR `tblinvitestatus`.`status` = "Sent to Vendor")';
        } else {
            $where = ' AND 1 = 1';
        }
    }

    //for collaborator
    if ($contacttype == 4) {
        if ($type == 'sent-to-vendor') {
            $where = ' AND (`tblinvitestatus`.`status` = "Approved by Account Owner" OR `tblinvitestatus`.`status` = "Sent to Collaborator")';
        } else {
            $where = ' AND 1 = 1';
        }
    }

    //for venue
    if ($contacttype == 5) {
        if ($type == 'sent-to-vendor') {
            $where = ' AND (`tblinvitestatus`.`status` = "Approved by Account Owner" OR `tblinvitestatus`.`status` = "Sent to Venue Owner")';
        } else {
            $where = ' AND 1 = 1';
        }
    }

    //get assoicated tags for vendor and/or collaborator
    if ($contacttype == 3 || $contacttype == 4) {
        if (isset($invite->staffid)) {
            $fields['{vendor_tag}'] = '';
        } else if (isset($invite->contactid)) {
            $tags_query = $CI->db->query('SELECT GROUP_CONCAT(`name`) AS vendor_tags FROM `tbltags` JOIN `tbladdressbooktags` ON `tbladdressbooktags`.`tagid` = `tbltags`.`id` WHERE `deleted` = 0 AND `addressbookid` = ' . $invite->contactid);
            $tags_details = $tags_query->row();
            $fields['{vendor_tag}'] = $tags_details->vendor_tags;
        } else {
            $tags_query = $CI->db->query('SELECT GROUP_CONCAT(`name`) AS vendor_tags FROM `tbltags` WHERE `deleted` = 0 AND `id` IN (' . $invite->tags . ')');
            $tags_details = $tags_query->row();
            $fields['{vendor_tag}'] = $tags_details->vendor_tags;
        }
    }

    $fields['{invited_name}'] = $invite->invited_name;
    $fields['{invited_email}'] = $invite->invited_email;
    $fields['{invited_phone}'] = $invite->invited_phone;
    $fields['{project_type}'] = $invite->project_type;
    $fields['{project_date}'] = $invite->project_date;
    $fields['{project_name}'] = $invite->project_name;


    $query = $CI->db->query('SELECT `inviteid`, `projectid`, (SELECT `name` FROM `tblprojects` WHERE `id` = `tblinvitestatus`.`projectid`) AS project_name, (SELECT `tbleventtype`.`eventtypename` FROM `tbleventtype` WHERE `tbleventtype`.`eventtypeid` = (SELECT `eventtypeid` FROM `tblprojects` WHERE `id` = `tblinvitestatus`.`projectid`)) as project_type, (SELECT DATE_FORMAT(`eventstartdatetime`, "%m/%d/%Y %H:%i") FROM `tblprojects` WHERE `id` = `tblinvitestatus`.`projectid`) AS eventstartdatetime, (SELECT DATE_FORMAT(`eventenddatetime`, "%m/%d/%Y %H:%i") FROM `tblprojects` WHERE `id` = `tblinvitestatus`.`projectid`) AS eventenddatetime, (SELECT t1.`status` FROM `tblinvitestatus` t1 WHERE t1.`inviteid` = `tblinvitestatus`.`inviteid` AND t1.`projectid` = `tblinvitestatus`.`projectid` ORDER BY t1.`datecreated` DESC LIMIT 0,1) AS status,(SELECT GROUP_CONCAT(`permissionid`) FROM `tbleventpermission` WHERE `inviteid` = `tblinvitestatus`.`inviteid` AND `projectid` = `tblinvitestatus`.`projectid`) AS permission_id,(SELECT GROUP_CONCAT(`name`) FROM `tbleventpermission` JOIN `tblpermissions` ON `tblpermissions`.`permissionid` = `tbleventpermission`.`permissionid` WHERE `inviteid` = `tblinvitestatus`.`inviteid` AND `projectid` = `tblinvitestatus`.`projectid`) AS permission_name FROM `tblinvitestatus` WHERE `inviteid` = ' . $id . $where . ' GROUP BY `tblinvitestatus`.`inviteid`, `tblinvitestatus`.`projectid`');
    $projects = $query->result_array();
    if (!$projects) {
        $fields['{project_details}'] = '';
    } else {
        $event = '<h3>Event details below:</h3><br/><br/>';

        foreach ($projects as $project) {
            $event .= 'Event: ' . $project['project_name'] . '<br/><br/>';
            $event .= 'Type: ' . $project['project_type'] . '<br/><br/>';
            //$event .= 'Venue: ' . $project['project_venue'] . '<br/><br/>';
            $event .= 'From: ' . $project['eventstartdatetime'] . '<br/><br/>';
            $event .= 'To: ' . $project['eventenddatetime'] . '<br/><br/>';
        }

        $fields['{project_details}'] = $event;
    }

    $hook_data['merge_fields'] = $fields;
    $hook_data['fields_to'] = 'invite';
    $hook_data['id'] = $id;

    $hook_data = do_action('invite_merge_fields', $hook_data);
    $fields = $hook_data['merge_fields'];

    return $fields;
}