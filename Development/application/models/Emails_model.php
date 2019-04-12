<?php
defined('BASEPATH') or exit('No direct script access allowed');
define('EMAIL_TEMPLATE_SEND', true);

class Emails_model extends CRM_Model
{
    private $attachment = array();
    private $client_email_templates;
    private $staff_email_templates;
    private $rel_id;
    private $rel_type;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('email');
        $this->client_email_templates = get_client_email_templates_slugs();
        $this->staff_email_templates = get_staff_email_templates_slugs();
    }

    /**
     * @param  string
     * @return array
     * Get email template by type
     */
    public function get($where = array())
    {
        /**
         * Added By : Vaidehi
         * Dt : 12/05/2017
         * to get templates brand wise
         */
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];

        if ($is_sido_admin == 0 && $is_admin == 0) {
            $brandid = get_user_session();
            //$this->db->where('brandid', $brandid);
        } else {
            $brandid = 0;
        }

        // $this->db->where('active', 1);

        // $this->db->where($where);

        //return $this->db->get('tblemailtemplates')->result_array();

        $res = $this->db->query('SELECT DISTINCT et.type,t.* FROM `tblemailtemplates` et JOIN (SELECT t1.* FROM `tblemailtemplates` t1 WHERE t1.`active` = 1 AND t1.`language` = "english" AND t1.`brandid` = ' . $brandid . ') t ON t.type = et.type WHERE et.`language` = "english" AND et.`brandid` = ' . $brandid . ' ORDER BY et.`type`');

        return $res->result_array();
    }

    /**
     * @param  integer
     * @return object
     * Get email template by id
     */
    public function get_email_template_by_id($id)
    {
        $this->db->where('emailtemplateid', $id);

        return $this->db->get('tblemailtemplates')->row();
    }

    /**
     * Create new email template
     * @param mixed $data
     */
    public function add_template($data)
    {
        $this->db->insert('tblemailtemplates', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update email template
     */
    public function update($data)
    {
        if (isset($data['plaintext'])) {
            $data['plaintext'] = 1;
        } else {
            $data['plaintext'] = 0;
        }

        if (isset($data['disabled'])) {
            $data['active'] = 0;
            unset($data['disabled']);
        } else {
            $data['active'] = 1;
        }
        $main_id = false;
        $affectedRows = 0;
        $i = 0;
        foreach ($data['subject'] as $id => $val) {
            if ($i == 0) {
                $main_id = $id;
            }

            $_data = array();
            $_data['subject'] = $val;
            $_data['fromname'] = $data['fromname'];
            $_data['fromemail'] = $data['fromemail'];
            $_data['message'] = $data['message'][$id];
            $_data['plaintext'] = $data['plaintext'];
            $_data['active'] = $data['active'];

            $this->db->where('emailtemplateid', $id);
            $this->db->where('brandid', get_user_session());
            $this->db->update('tblemailtemplates', $_data);
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }

            $i++;
        }
        $main_template = $this->get_email_template_by_id($main_id);

        if ($affectedRows > 0 && $main_template) {
            logActivity('Email Template Updated [' . $main_template->name . ']');

            return true;
        }

        return false;
    }

    /**
     * Send email - No templates used only simple string
     * @since Version 1.0.2
     * @param  string $email email
     * @param  string $message message
     * @param  string $subject email subject
     * @return boolean
     */
    public function send_simple_email($email, $subject, $message,$from_name="", $from_email="")
    {
        $cnf = array(
            'from_email' => get_brand_option('smtp_email'),
            'from_name' => get_brand_option('companyname'),
            'email' => $email,
            'subject' => $subject,
            'message' => $message
        );
        if(!empty($from_name)){
            $cnf['from_name'] = $from_name;
        }
        if(!empty($from_email)){
            $cnf['from_email'] = $from_email;
        }

        // Simulate fake template to be parsed
        $template = new StdClass();
        $template->message = get_option('email_header') . $cnf['message'] . get_option('email_footer');
        $template->fromname = $cnf['from_name'];
        $template->subject = $cnf['subject'];

        $template = parse_email_template($template);

        $cnf['message'] = $template->message;
        $cnf['from_name'] = $template->fromname;
        $cnf['subject'] = $template->subject;

        $cnf['message'] = check_for_links($cnf['message']);

        $cnf = do_action('before_send_simple_email', $cnf);

        $this->email->initialize();
        $this->email->set_newline("\r\n");
        $this->email->clear(true);
        $this->email->from($cnf['from_email'], $cnf['from_name']);
        $this->email->to($cnf['email']);

        // Possible action hooks data
        if (isset($cnf['bcc'])) {
            $this->email->bcc($cnf['bcc']);
        }

        if (isset($cnf['cc'])) {
            $this->email->cc($cnf['cc']);
        }

        if (isset($cnf['reply_to'])) {
            $this->email->reply_to($cnf['reply_to']);
        }

        $this->email->subject($cnf['subject']);
        $this->email->message($cnf['message']);
        $this->email->set_alt_message(strip_tags($cnf['message']));

        if (count($this->attachment) > 0) {
            foreach ($this->attachment as $attach) {

                if (!isset($attach['read'])) {
                    $this->email->attach($attach['attachment'], 'attachment', $attach['filename'], $attach['type']);
                } else {
                    if (!isset($attach['filename']) || (isset($attach['filename']) && empty($attach['filename']))) {
                        $attach['filename'] = basename($attach['attachment']);
                    }
                    $this->email->attach($attach['attachment'], '', $attach['filename']);
                }
            }
        }
        $this->clear_attachments();
        /*echo "email: " . $email;
        echo "<pre>";
        print_r($cnf['message']);
        die('<--here');*/
        if ($this->email->send()) {
            logActivity('Email sent to: ' . $cnf['email'] . ' Subject: ' . $cnf['subject']);

            return true;
        }

        return false;
    }

    /**
     * Send email template
     * @param  string $template_slug email template slug
     * @param  string $email email to send
     * @param  array $merge_fields merge field
     * @param  string $ticketid used only when sending email templates linked to ticket / used for piping
     * @param  mixed $cc
     * @return boolean
     */
    public function send_email_template($template_slug, $email, $merge_fields, $ticketid = '', $cc = '')
    {
        if (is_serialized($email)) {
            $email = unserialize($email);
            $email = $email[0]['email'];
        }
        $template = get_email_template_for_sending($template_slug, $email);
        $staff_email_templates_slugs = get_staff_email_templates_slugs();
        $client_email_templates_slugs = get_client_email_templates_slugs();
        $inactive_user_table_check = '';
        // Dont send email templates for non active contacts/staff/ Do checking here
        if (in_array($template_slug, $staff_email_templates_slugs)) {
            $inactive_user_table_check = 'tblstaff';
        } elseif (in_array($template_slug, $client_email_templates_slugs)) {
            $inactive_user_table_check = 'tblcontacts';
        }

        if ($inactive_user_table_check != '') {
            $this->db->select('active')->where('email', $email);
            $user = $this->db->get($inactive_user_table_check)->row();
            if ($user) {
                if ($user->active == 0) {
                    return false;
                }
            }
        }

        if (!$template) {
            logActivity('Failed to send email template [Template not found]');
            return false;
        }

        /*Added By : Vaidehi
        * Dt : 01/08/2018
        * to skip condition for invite flow
        */
        if ($template_slug != 'invite-new' && $template_slug != 'invite-vendor' && $template_slug != 'decline-invite' && $template_slug != 'decline-vendor' && $template_slug != 'invite-new-vendor' && $template_slug != 'invite-new-collaborator' && $template_slug != 'invite-collaborator' && $template_slug != 'invite-new-project-collaborator' && $template_slug != 'new-venue-added' && $template_slug != 'venue-added' && $template_slug != 'task-reminder' && $template_slug != 'meeting-reminder' && $template_slug != 'event-reminder' && $template_slug != 'subscription-reminder' && $template_slug != 'invite-new-venue' && $template_slug != 'decline-venue' && $template_slug != 'invite-venue') {
            if ($template->active == 0 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->clear_attachments();

                $this->db->where('language', 'english');
                $this->db->where('slug', $template->slug);
                if (!empty(get_user_session())) {
                    $this->db->where('brandid', get_user_session());
                }
                $tmpTemplate = $this->db->get('tblemailtemplates')->row();

                if ($tmpTemplate) {
                    logActivity('Failed to send email template [<a href="' . admin_url('emails/email_template/' . $tmpTemplate->emailtemplateid) . '">' . $template->name . '</a>] [Reason: Email template is disabled.]');
                }

                return false;
            }
        }

        $template = parse_email_template($template, $merge_fields);

        /**
         * Added By : Vaidehi
         * Dt : 12/05/2017
         * to get email headers and footers brand wise
         */
        if ($this->session->userdata('is_sido_admin')) {
            $session_data = get_session_data();
            $is_sido_admin = $session_data['is_sido_admin'];
            $is_admin = $session_data['is_admin'];

            if ($is_sido_admin == 0 && $is_admin == 0) {
                $brandid = get_user_session();
                $template->message = get_brand_option('email_header') . $template->message . get_brand_option('email_footer');
            } else {
                $template->message = get_option('email_header') . $template->message . get_option('email_footer');
            }
        } else {
            $template->message = get_option('email_header') . $template->message . get_option('email_footer');
        }

        // Parse merge fields again in case there is merge fields found in email_header and email_footer option.
        // We cant parse this in parse_email_template function because in case the template content is send via $_POST wont work
        $template = _parse_email_template_merge_fields($template, $merge_fields);


        // email config
        if ($template->plaintext == 1) {
            $this->config->set_item('mailtype', 'text');
            $template->message = strip_tags($template->message);
        }

        $fromemail = $template->fromemail;
        $fromname = $template->fromname;

        if ($this->session->userdata('is_sido_admin')) {
            if ($fromemail == '' && $is_sido_admin == 0 && $is_admin == 0) {
                $fromemail = get_brand_option('smtp_email');
            } else {
                $fromemail = get_option('smtp_email');
            }
        } else {
            $fromemail = get_option('smtp_email');
        }

        if ($this->session->userdata('is_sido_admin')) {
            if ($fromname == '' && $is_sido_admin == 0 && $is_admin == 0) {
                $fromname = get_brand_option('companyname');
            } else {
                $fromname = get_option('companyname');
            }
        } else {
            $fromname = get_option('companyname');
        }

        $reply_to = false;

        if (is_numeric($ticketid) && $template->type == 'ticket') {
            $this->load->model('tickets_model');
            $ticket = $this->tickets_model->get_ticket_by_id($ticketid);
            $department_email = get_department_email($ticket->department);
            if (!empty($department_email) && filter_var($department_email, FILTER_VALIDATE_EMAIL)) {
                $reply_to = $department_email;
            }
            // IMPORTANT
            // Dont change/remove this line, this is used for email piping so the software can recognize the ticket id.
            if (substr($template->subject, 0, 10) != "[Ticket ID") {
                $template->subject = '[Ticket ID: ' . $ticketid . '] ' . $template->subject;
            }
        }

        $hook_data['template'] = $template;
        $hook_data['email'] = $email;

        $hook_data['template']->message = check_for_links($hook_data['template']->message);

        $hook_data = do_action('before_email_template_send', $hook_data);

        $template = $hook_data['template'];
        $email = $hook_data['email'];

        if (isset($template->prevent_sending)) {
            return false;
        }

        $this->email->initialize();
        $this->email->set_newline("\r\n");
        $this->email->clear(true);
        $this->email->from($fromemail, $fromname);
        $this->email->subject($template->subject);

        $this->email->message($template->message);
        if (is_array($cc) || !empty($cc)) {
            $this->email->cc($cc);
        }

        // Used for action hooks
        if (isset($template->bcc)) {
            $this->email->bcc($template->bcc);
        }

        if ($reply_to != false) {
            $this->email->reply_to($reply_to);
        } elseif (isset($template->reply_to)) {
            $this->email->reply_to($template->reply_to);
        }

        if ($template->plaintext == 0) {
            $this->email->set_alt_message(strip_tags($template->message));
        }
        /*echo "email: ".$email;
        echo "<pre>";
        print_r($template->message);
        die('<--here');*/
        $this->email->to($email);
        if (count($this->attachment) > 0) {
            foreach ($this->attachment as $attach) {
                if (!isset($attach['read'])) {
                    $this->email->attach($attach['attachment'], 'attachment', $attach['filename'], $attach['type']);
                } else {
                    $this->email->attach($attach['attachment'], '', $attach['filename']);
                }
            }
        }

        $this->clear_attachments();
        if ($this->email->send()) {
            logActivity('Email Send To [Email: ' . $email . ', Template: ' . $template->name . ']');

            return true;
        }

        return false;
    }

    /**
     * @param resource
     * @param string
     * @param string (mime type)
     * @return none
     * Add attachment to property to check before an email is send
     */
    public function add_attachment($attachment)
    {
        $this->attachment[] = $attachment;
    }

    /**
     * @return none
     * Clear all attachment properties
     */
    private function clear_attachments()
    {
        $this->attachment = array();
    }

    public function set_rel_id($rel_id)
    {
        $this->rel_id = $rel_id;
    }

    public function set_rel_type($rel_type)
    {
        $this->rel_type = $rel_type;
    }

    public function get_rel_id()
    {
        return $this->rel_id;
    }

    public function get_rel_type()
    {
        return $this->rel_type;
    }
}
