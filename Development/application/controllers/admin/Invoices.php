<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoices extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        //Added By Sanjay on 03/17/2018 to set session type as blank
        $this->session->set_userdata('type', '');
        $this->load->model('invoices_model');
        $this->load->model('proposaltemplates_model');
    }

    /* Get all invoices in case user go on index page */
    public function index($id = false)
    {
        $this->list_invoices($id);
    }

    /* List all invoices datatables */
    public function list_invoices($id = false, $clientid = false)
    {
        /*if (is_mobile()) {
            $this->session->set_userdata(array(
                'invoices_kanban_view' => 0
            ));
        }*/
        if (!has_permission('invoices', '', 'view', true) && !has_permission('invoices', '', 'view_own', true)) {
            access_denied('invoices');
        }
        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', array(), true);
        if ($this->input->is_ajax_request()) {
            if (!$this->input->get('kanban')) {
                $this->perfex_base->get_table_data('invoices', array(
                    'id' => $id,
                    'clientid' => $clientid,
                    'data' => $data
                ));
            }
        }
        $data['invoiceid'] = '';
        if (is_numeric($id)) {
            $data['invoiceid'] = $id;
        }
        $data['title'] = _l('invoices');
        $data['invoices_years'] = $this->invoices_model->get_invoices_years();
        $data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();
        $data['invoices_statuses'] = $this->invoices_model->get_statuses();
        $data['bodyclass'] = 'invoices_total_manual';

        //Added By Avni on 11/21/2017 Start
        if ($this->input->get('lid')) {
            $leadid = $this->input->get('lid');

            $this->load->model('leads_model');

            $data['lid'] = $leadid;
            $data['lname'] = '';
            if ($leadid != "") {
                $data['lname'] = $this->leads_model->get($leadid)->name;
            }
        }

        if ($this->input->get('pid')) {
            $projectid = $this->input->get('pid');

            $this->load->model('projects_model');

            $data['pid'] = $projectid;
            $data['lname'] = '';
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        }
        if ($this->input->get('eid')) {
            $projectid = $this->input->get('eid');
            $parent_id = $this->projects_model->get($projectid)->parent;
            $this->load->model('projects_model');

            $data['eid'] = $projectid;
            $data['parent_id'] = $parent_id;
            $data['lname'] = '';
            $data['pname'] = '';
            if ($parent_id != "") {
                $data['pname'] = $this->projects_model->get($parent_id)->name;
            }
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        }
        $data['members'] = $this->staff_model->get('', 1, array(
            'is_not_staff' => 0
        ));
        //Added By Avni on 11/21/2017 End

        //Added By Masud on 06/26/2018 start
        $data['switch_invoices_kanban'] = true;
        if ($this->session->has_userdata('invoices_kanban_view') && $this->session->userdata('invoices_kanban_view') == 'true') {
            $data['switch_invoices_kanban'] = false;
            $data['bodyclass'] = 'kan-ban-body';
        }
        $lid = $pid = $eid = "";
        if ($this->input->get('lid')) {
            $lid = $this->input->get('lid');
        }
        if ($this->input->get('pid')) {
            $pid = $this->input->get('pid');
        }
        if ($this->input->get('eid')) {
            $eid = $this->input->get('eid');
        }
        if ($this->input->is_ajax_request()) {
            if ($this->input->get('kanban')) {
                if ($this->input->get('limit')) {
                    $data['limit'] = $this->input->get('limit');
                }
                if ($this->input->get('page')) {
                    $data['page'] = $this->input->get('page');
                }
                $data['totalinvoices'] = $this->invoices_model->get_kanban_invoices($lid, $pid, $eid, "", "", $this->input->get('search'), $this->input->get('kanban'), $id, $clientid, $data);

                $data['invoices'] = $this->invoices_model->get_kanban_invoices($lid, $pid, $eid, $this->input->get('limit'), $this->input->get('page'), $this->input->get('search'), $this->input->get('kanban'), $id, $clientid, $data);
                echo $this->load->view('admin/invoices/kan-ban', $data, true);
                die();
            }
        }
        //Added By Masud on 06/26/2018 end
        if (isset($_GET['eid'])) {
            $data['parent_id'] = $this->projects_model->get($_GET['eid'])->parent;
        } else {
            $data['parent_id'] = 0;
        }

        $this->load->view('admin/invoices/manage', $data);
    }

    public function client_change_data($customer_id, $current_invoice = 'undefined')
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('projects_model');
            $this->load->model('Addressbooks_model');
            $data = array();
            $data['billing_shipping'] = $this->Addressbooks_model->get_customer_billing_and_shipping_details($customer_id);
            $data['client_currency'] = $this->clients_model->get_customer_default_currency($customer_id);

            $data['customer_has_projects'] = customer_has_projects($customer_id);
            $data['billable_tasks'] = $this->tasks_model->get_billable_tasks($customer_id);
            $_data['invoices_to_merge'] = $this->invoices_model->check_for_merge_invoice($customer_id, $current_invoice);
            $data['merge_info'] = $this->load->view('admin/invoices/merge_invoice', $_data, true);

            $this->load->model('currencies_model');
            $__data['expenses_to_bill'] = $this->invoices_model->get_expenses_to_bill($customer_id);
            $data['expenses_bill_info'] = $this->load->view('admin/invoices/bill_expenses', $__data, true);
            echo json_encode($data);
        }
    }

    public function update_number_settings($id)
    {
        $response = array(
            'success' => false,
            'message' => ''
        );
        if ($this->input->post('prefix')) {
            $affected_rows = 0;

            $this->db->where('id', $id);
            $this->db->update('tblinvoices', array(
                'prefix' => $this->input->post('prefix')
            ));
            if ($this->db->affected_rows() > 0) {
                $affected_rows++;
            }

            if ($affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = _l('updated_successfully', _l('invoice'));
            }
        }
        echo json_encode($response);
        die;
    }

    public function validate_invoice_number()
    {
        $isedit = $this->input->post('isedit');
        $number = $this->input->post('number');
        $date = $this->input->post('date');
        $original_number = $this->input->post('original_number');
        $number = trim($number);
        $number = ltrim($number, '0');
        $lid = $this->input->post('lid');
        $pid = $this->input->post('pid');
        $eid = $this->input->post('eid');
        $format = $this->input->post('format');
        if ($isedit == 'true') {
            if ($number == $original_number) {
                echo json_encode(true);
                die;
            }
        }
        if ($format == 3) {
            $date = date_create($date);
            $date = date_format($date, 'Y-m-d');
            if (total_rows('tblinvoices', array(
                    'date' => $date,
                    'number' => $number
                )) > 0) {
                echo 'false';
            } else {
                echo 'true';
            }
        } elseif ($format == 4) {

            if ($lid != "") {
                $lead_event_data = $this->leads_model->get($lid);
                $lead_event_date = date('Y-m-d', strtotime($lead_event_data->eventstartdatetime));

                if (total_rows('tblinvoices', array(
                        'leaddate' => $lead_event_date,
                        'number' => $number
                    )) > 0) {
                    echo 'false';
                } else {
                    echo 'true';
                }
            } elseif ($pid != "") {
                $project_event_data = $this->projects_model->get($pid);
                $project_event_date = date('Y-m-d', strtotime($project_event_data->eventstartdatetime));

                if (total_rows('tblinvoices', array(
                        'leaddate' => $project_event_date,
                        'number' => $number
                    )) > 0) {
                    echo 'false';
                } else {
                    echo 'true';
                }
            } elseif ($eid != "") {
                $project_event_data = $this->projects_model->get($eid);
                $project_event_date = date('Y-m-d', strtotime($project_event_data->eventstartdatetime));

                if (total_rows('tblinvoices', array(
                        'leaddate' => $project_event_date,
                        'number' => $number
                    )) > 0) {
                    echo 'false';
                } else {
                    echo 'true';
                }
            }
        } else {
            if (total_rows('tblinvoices', array(
                    'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),
                    'number' => $number
                )) > 0) {
                echo 'false';
            } else {
                echo 'true';
            }
        }

    }

    public function mark_as_cancelled($id)
    {
        if (!has_permission('invoices', '', 'edit', true) && !has_permission('invoices', '', 'create', true)) {
            access_denied('invoices');
        }
        $success = $this->invoices_model->mark_as_cancelled($id);
        if ($success) {
            set_alert('success', _l('invoice_marked_as_cancelled_successfully'));
        }

        if ($this->input->get('lid')) {
            redirect(admin_url('invoices/list_invoices?lid=' . $this->input->get('lid') . '#' . $id));
        } else {
            redirect(admin_url('invoices/list_invoices#' . $id));
        }
    }

    public function unmark_as_cancelled($id)
    {

        if (!has_permission('invoices', '', 'edit', true) && !has_permission('invoices', '', 'create', true)) {
            access_denied('invoices');
        }
        $success = $this->invoices_model->unmark_as_cancelled($id);
        if ($success) {
            set_alert('success', _l('invoice_unmarked_as_cancelled'));
        }
        if ($this->input->get('lid')) {
            redirect(admin_url('invoices/list_invoices?lid=' . $this->input->get('lid') . '#' . $id));
        } else {
            redirect(admin_url('invoices/list_invoices#' . $id));
        }
    }

    public function copy($id)
    {
        if (!$id) {
            redirect(admin_url('invoices'));
        }
        if (!has_permission('invoices', '', 'create', true)) {
            access_denied('invoices');
        }
        $new_id = $this->invoices_model->copy($id);
        if ($new_id) {
            set_alert('success', _l('invoice_copy_success'));
            redirect(admin_url('invoices/invoice/' . $new_id));
        } else {
            set_alert('success', _l('invoice_copy_fail'));
        }
        redirect(admin_url('invoices/invoice/' . $id));
    }

    public function get_merge_data($id)
    {
        $invoice = $this->invoices_model->get($id);
        $i = 0;
        foreach ($invoice->items as $item) {
            $invoice->items[$i]['taxname'] = get_invoice_item_taxes($item['id']);
            $invoice->items[$i]['long_description'] = clear_textarea_breaks($item['long_description']);
            $this->db->where('item_id', $item['id']);
            $rel = $this->db->get('tblitemsrelated')->result_array();
            $item_related_val = '';
            $rel_type = '';
            foreach ($rel as $item_related) {
                $rel_type = $item_related['rel_type'];
                $item_related_val .= $item_related['rel_id'] . ',';
            }
            if ($item_related_val != '') {
                $item_related_val = substr($item_related_val, 0, -1);
            }
            $invoice->items[$i]['item_related_formatted_for_input'] = $item_related_val;
            $invoice->items[$i]['rel_type'] = $rel_type;
            $i++;
        }
        echo json_encode($invoice);
    }

    public function get_bill_expense_data($id)
    {
        $this->load->model('expenses_model');
        $expense = $this->expenses_model->get($id);

        $expense->qty = 1;
        $expense->long_description = clear_textarea_breaks($expense->description);
        $expense->description = $expense->name;
        $expense->rate = $expense->amount;
        if ($expense->tax != 0) {
            $expense->taxname = array();
            array_push($expense->taxname, $expense->tax_name . '|' . $expense->taxrate);
        }
        if ($expense->tax2 != 0) {
            array_push($expense->taxname, $expense->tax_name2 . '|' . $expense->taxrate2);
        }
        echo json_encode($expense);
    }

    /* Add new invoice or update existing */
    public function invoice($id = '')
    {
        $pg = $this->input->get('pg');

        if (!has_permission('invoices', '', 'view', true) && !has_permission('invoices', '', 'view_own', true)) {
            access_denied('invoices');
        }
        $postlid = $this->input->post('leadid');
        $postpid = $this->input->post('project_id');
        $posteid = $this->input->post('eventid');

        if ($this->input->post()) {
            $pg = $this->input->post('pg');
            $invoice_data = $this->input->post(null, false);
            unset($invoice_data['pg']);
            if ($id == '') {
                if (!has_permission('invoices', '', 'create', true)) {
                    access_denied('invoices');
                }
                $id = $this->invoices_model->add($invoice_data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('invoice')));

                    if (isset($postlid) && $postlid != "") {
                        redirect(admin_url('invoices/list_invoices?lid=' . $postlid . '#' . $id));
                    } else if (isset($postpid) && $postpid != "") {
                        redirect(admin_url('invoices/list_invoices?pid=' . $postpid . '#' . $id));
                    } else if (isset($posteid) && $posteid != "") {
                        redirect(admin_url('invoices/list_invoices?eid=' . $posteid . '#' . $id));
                    } elseif (isset($pg) && $pg == "calendar") {
                        redirect(admin_url('calendar'));
                    } elseif (isset($pg) && $pg == "home") {
                        redirect(admin_url());
                    } else {
                        redirect(admin_url('invoices/list_invoices#' . $id));
                    }
                }
            } else {
                if (!has_permission('invoices', '', 'edit', true)) {
                    access_denied('invoices');
                }
                $success = $this->invoices_model->update($invoice_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('invoice')));
                }

                if (isset($postlid) && $postlid != "") {
                    redirect(admin_url('invoices/list_invoices?lid=' . $postlid . '#' . $id));
                } else if (isset($postpid) && $postpid != "") {
                    redirect(admin_url('invoices/list_invoices?pid=' . $postpid . '#' . $id));
                } else if (isset($posteid) && $posteid != "") {
                    redirect(admin_url('invoices/list_invoices?eid=' . $posteid . '#' . $id));
                } elseif (isset($pg) && $pg != "") {
                    redirect(admin_url('calendar'));
                } else {
                    redirect(admin_url('invoices/list_invoices#' . $id));
                }
            }
        }
        if ($id == '') {
            $title = _l('create_new_invoice');
            $data['billable_tasks'] = array();
            $format = get_brand_option('invoice_number_format');
            $date = date('Y-m-d');
            if ($format == 3) {
                if (total_rows('tblinvoices', array(
                        'date' => $date,
                    )) == 0) {
                    $this->db->where('name', 'next_invoice_number');
                    $this->db->set('value', '1', false);
                    $this->db->update('tblbrandsettings');
                }
            }
        } else {
            $invoice = $this->invoices_model->get($id);
            if (!$invoice || (!has_permission('invoices', '', 'view', true) && $invoice->addedfrom != get_staff_user_id())) {
                blank_page(_l('invoice_not_found'), 'danger');
            }

            $data['invoices_to_merge'] = $this->invoices_model->check_for_merge_invoice($invoice->clientid, $invoice->id);
            $data['expenses_to_bill'] = $this->invoices_model->get_expenses_to_bill($invoice->clientid);

            $data['invoice'] = $invoice;
            $data['edit'] = true;
            $data['billable_tasks'] = $this->tasks_model->get_billable_tasks($invoice->clientid);
            $title = _l('edit', _l('invoice_lowercase')) . format_invoice_number($invoice->id);
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }

        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', array(
            'expenses_only !=' => 1
        ));

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('invoice_items_model');

        $data['ajaxItems'] = false;
        if (total_rows('tblitems') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get_grouped();
            //Added by vaidehi on 03/19/2018 to get packages on invoice page
            $data['groups'] = $this->invoice_items_model->get_package_groups();
        } else {
            $data['items'] = array();
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $data['staff'] = $this->staff_model->get('', 1);
        $data['title'] = $title;
        $data['bodyclass'] = 'invoice';
        $data['accounting_assets'] = true;
        //Added By Avni on 11/22/2017 Start
        if ($this->input->get('lid') || (isset($invoice->leadid) && $invoice->leadid > 0)) {
            if (isset($invoice) && $invoice->project_id > 0) {
                $leadid = $invoice->leadid;
            } else {
                $leadid = $this->input->get('lid');
            }
            $data['contacts'] = $this->invoices_model->get_contacts($leadid);
            $data['lid'] = $leadid;

            $this->load->model('leads_model');
            $data['lname'] = '';
            if ($data['lid'] != "") {
                $data['lname'] = $this->leads_model->get($data['lid'])->name;
            }
        } elseif ($this->input->get('pid') || (isset($invoice->project_id) && $invoice->project_id > 0)) {
            if (isset($invoice) && $invoice->project_id > 0) {
                $projectid = $invoice->project_id;
            } else {
                $projectid = $this->input->get('pid');
            }

            $data['contacts'] = $this->invoices_model->get_contacts("", $this->input->get('pid'));

            $this->load->model('projects_model');

            $data['pid'] = $projectid;
            $data['lname'] = '';
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        } elseif ($this->input->get('eid') || (isset($invoice->eventid) && $invoice->eventid > 0)) {
            if (isset($invoice) && $invoice->project_id > 0) {
                $projectid = $invoice->eventid;
            } else {
                $projectid = $this->input->get('eid');
            }
            $data['contacts'] = $this->invoices_model->get_contacts("", "", $projectid);

            $parent_id = $this->projects_model->get($projectid)->parent;
            $this->load->model('projects_model');

            $data['eid'] = $projectid;
            $data['parent_id'] = $parent_id;
            $data['lname'] = '';
            $data['pname'] = '';
            if ($parent_id != "") {
                $data['pname'] = $this->projects_model->get($parent_id)->name;
            }
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        } else {
            $data['contacts'] = $this->invoices_model->get_contacts();
        }
        $data['pg'] = $this->input->get('pg');
        //Added By Avni on 11/22/2017 End


        if (isset($_GET['pid'])) {
            $data['parent_id'] = $this->projects_model->get($_GET['pid'])->parent;
        } else {
            $data['parent_id'] = 0;
        }
        $this->load->view('admin/invoices/invoice', $data);
    }

    /* Get all invoice data used when user click on invoiec number in a datatable left side*/
    public function get_invoice_data_ajax($id, $lid = "", $pid = "", $eid = "")
    {
        if (!has_permission('invoices', '', 'view', true) && !has_permission('invoices', '', 'view_own', true)) {
            echo _l('access_denied');
            die;
        }
        if (!$id) {
            die('No invoice found');
        }
        //echo '<pre>'; print_r($this->input->get());
        $invoice = $this->invoices_model->get($id);
        $proposalid = get_invoice_proposalid($id);
        if($proposalid > 0 ){
            $proposal = $this->proposaltemplates_model->getproposaltemplates($proposalid);
        }
        if (!$invoice || (!has_permission('invoices', '', 'view', true) && $invoice->addedfrom != get_staff_user_id())) {
            echo _l('invoice_not_found');
            die;
        }
        $invoice->date = _d($invoice->date);
        $invoice->duedate = _d($invoice->duedate);
        $template_name = 'invoice-send-to-client';
        if ($invoice->sent == 1) {
            $template_name = 'invoice-already-send';
        }

        $template_name = do_action('after_invoice_sent_template_statement', $template_name);

        // $contact = $this->clients_model->get_contact(get_primary_contact_user_id($invoice->clientid));
        /*
        **  Added by Avni on 11/24/2017 Start
        */
        $this->load->model('Addressbooks_model');
        $contact = $this->Addressbooks_model->get_contacts($invoice->clientid);
        if ($contact) {
            //$data->client->company = $contact->firstname . ' ' . $contact->lastname;

            $email = '';
            if ($contact->email) {
                $email = $contact->email->email;
            }
            if ($contact->address) {
                $invoice->billing_street = $contact->address->address . ' ' . $contact->address->address2;
                $invoice->billing_city = $contact->address->city;
                $invoice->billing_state = $contact->address->state;
                $invoice->billing_zip = $contact->address->zip;
            }

            if (isset($contact->phone->phone)) {
                $invoice->phonenumber = $contact->phone->phone;
            } else {
                $invoice->phonenumber = '';
            }
        }

        /*
        **  Added by Avni on 11/24/2017 Start
        */

        $data['template'] = get_email_template_for_sending($template_name, $email);

        $data['invoices_to_merge'] = $this->invoices_model->check_for_merge_invoice($invoice->clientid, $id);
        $data['template_name'] = $template_name;
        $this->db->where('slug', $template_name);
        $this->db->where('language', 'english');

        /**
         * Added By : Vaidehi
         * Dt : 12/05/2017
         * to get brand wise email templates
         */
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];

        if ($is_sido_admin == 0 && $is_admin == 0) {
            $brandid = get_user_session();
            $this->db->where('brandid = ', $brandid);
        }

        $template_result = $this->db->get('tblemailtemplates')->row();

        $data['template_system_name'] = isset($template_result->name) ? $template_result->name : "";
        $data['template_id'] = isset($template_result->emailtemplateid) ? $template_result->emailtemplateid : "";

        $data['template_disabled'] = false;
        if (total_rows('tblemailtemplates', array('slug' => $data['template_name'], 'active' => 0)) > 0) {
            $data['template_disabled'] = true;
        }
        // Check for recorded payments
        $this->load->model('payments_model');
        $this->load->model('Addressbooks_model');
        $data['members'] = $this->staff_model->get('', 1);
        $data['contacts'] = $this->Addressbooks_model->get_contacts($invoice->clientid);
        $data['payments'] = $this->payments_model->get_invoice_payments($id);
        $data['activity'] = $this->invoices_model->get_invoice_activity($id);

        $data['invoice_recurring_invoices'] = $this->invoices_model->get_invoice_recurring_invoices($id);

        $data['invoice'] = $invoice;

        //Added By Avni on 11/28/2017 Start
        //echo '<pre>'; print_r($this->input->get('lid'));
        if ($lid > 0) {
            $data['lid'] = $lid;

            $this->load->model('leads_model');
            $data['lname'] = '';
            if ($data['lid'] != "") {
                $data['lname'] = $this->leads_model->get($data['lid'])->name;
            }
        } elseif ($pid > 0) {
            $projectid = $pid;

            $this->load->model('projects_model');

            $data['pid'] = $projectid;
            $data['lname'] = '';
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        } elseif ($eid > 0) {
            $projectid = $eid;
            $parent_id = $this->projects_model->get($projectid)->parent;
            $this->load->model('projects_model');

            $data['eid'] = $projectid;
            $data['parent_id'] = $parent_id;
            $data['lname'] = '';
            $data['pname'] = '';
            if ($parent_id != "") {
                $data['pname'] = $this->projects_model->get($parent_id)->name;
            }
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        }
        if(isset($proposal) && !empty($proposal)){
            $data['proposal']=$proposal;
        }
        //Added By Avni on 11/28/2017 End
        $this->load->view('admin/invoices/invoice_preview_template', $data);
    }

    public function get_invoices_total()
    {

        if ($this->input->post()) {
            load_invoices_total_template();
        }
    }

    /* Record new inoice payment view */
    public function record_invoice_payment_ajax($id, $lid = "", $type = "")
    {
        $this->load->model('payment_modes_model');
        $this->load->model('payments_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', array(
            'expenses_only !=' => 1
        ));
        $data['lid'] = $lid;
        $data['type'] = $type;

        $data['invoice'] = $invoice = $this->invoices_model->get($id);
        $data['payments'] = $this->payments_model->get_invoice_payments($id);

        $this->load->view('admin/invoices/record_payment_template', $data);
    }

    /* This is where invoice payment record $_POST data is send */
    public function record_payment()
    {
        if (!has_permission('account_setup', '', 'create', true)) {
            access_denied('Record Payment');
        }
        if ($this->input->post()) {

            $type = $this->input->post('type');

            $this->load->model('payments_model');
            $id = $this->payments_model->process_payment($this->input->post(), '');
            if ($id) {
                set_alert('success', _l('invoice_payment_recorded'));

                if ($type == 'lid') {
                    redirect(admin_url('invoices/list_invoices?lid=' . $this->input->post('lid') . '#' . $this->input->post('invoiceid')));
                } else if ($type == 'pid') {
                    redirect(admin_url('invoices/list_invoices?pid=' . $this->input->post('lid') . '#' . $this->input->post('invoiceid')));
                } else if ($type == 'eid') {
                    redirect(admin_url('invoices/list_invoices?eid=' . $this->input->post('lid') . '#' . $this->input->post('invoiceid')));
                }

                redirect(admin_url('payments/payment/' . $this->input->post('invoiceid')));
            } else {
                set_alert('danger', _l('invoice_payment_record_failed'));
            }

            redirect(admin_url('invoices/list_invoices?lid=' . $this->input->get('lid') . '#' . $id));
        }
    }

    /* Send invoiece to email */
    public function send_to_email($id)
    {
        if (!has_permission('invoices', '', 'view', true) && !has_permission('invoices', '', 'view_own', true)) {
            access_denied('invoices');
        }
        $success = $this->invoices_model->send_invoice_to_client($id, '', $this->input->post('attach_pdf'), $this->input->post('cc'));
        // In case client use another language
        load_admin_language();
        if ($success) {
            set_alert('success', _l('invoice_sent_to_client_success'));
        } else {
            set_alert('danger', _l('invoice_sent_to_client_fail'));
        }
        redirect(admin_url('invoices/list_invoices#' . $id));
    }

    /* Delete invoice payment*/
    public function delete_payment($id, $invoiceid)
    {
        if (!has_permission('account_setup', '', 'delete', true)) {
            access_denied('payments');
        }
        $this->load->model('payments_model');
        if (!$id) {
            redirect(admin_url('payments'));
        }
        $response = $this->payments_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('payment')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('payment_lowercase')));
        }
        redirect(admin_url('invoices/list_invoices#' . $invoiceid));
    }

    /* Delete invoice */
    public function delete($id)
    {
        if (!has_permission('invoices', '', 'delete', true)) {
            access_denied('invoices');
        }
        if (!$id) {
            redirect(admin_url('invoices/list_invoices'));
        }
        $success = $this->invoices_model->delete($id);

        if ($success) {
            set_alert('success', _l('deleted', _l('invoice')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('invoice_lowercase')));
        }
        // if (strpos($_SERVER['HTTP_REFERER'], 'list_invoices') !== false) {
        //     if($this->input->get('lid')) {
        //         redirect(admin_url('invoices/list_invoices?lid=' . $this->input->get('lid')));
        //     } else {
        //         redirect(admin_url('invoices/list_invoices'));
        //     }
        // } else {
        //     redirect($_SERVER['HTTP_REFERER']);
        // }
    }

    public function delete_attachment($id)
    {
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo $this->invoices_model->delete_attachment($id);
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /* Will send overdue notice to client */
    public function send_overdue_notice($id)
    {
        if (!has_permission('invoices', '', 'view', true)) {
            access_denied('invoices');
        }
        $send = $this->invoices_model->send_invoice_overdue_notice($id);
        if ($send) {
            set_alert('success', _l('invoice_overdue_reminder_sent'));
        } else {
            set_alert('warning', _l('invoice_reminder_send_problem'));
        }
        if ($this->input->get('lid')) {
            redirect(admin_url('invoices/list_invoices?lid=' . $this->input->get('lid') . '#' . $id));
        } else {
            redirect(admin_url('invoices/list_invoices#' . $id));
        }
    }

    /* Generates invoice PDF and senting to email of $send_to_email = true is passed */
    public function pdf($id)
    {
        if (!has_permission('invoices', '', 'view', true) && !has_permission('invoices', '', 'view_own', true)) {
            access_denied('invoices');
        }
        if (!$id) {
            if ($this->input->get('lid')) {
                redirect(admin_url('invoices/list_invoices?lid=' . $this->input->get('lid')));
            } else {
                redirect(admin_url('invoices/list_invoices'));
            }
        }
        $invoice = $this->invoices_model->get($id);
        $invoice_number = format_invoice_number($invoice->id);

        /*
        **  Added by Avni on 11/28/2017 Start
        */
        $this->load->model('Addressbooks_model');
        $contact = $this->Addressbooks_model->get_contacts($invoice->clientid);

        if ($contact) {

            $email = '';
            if ($contact->email) {
                $email = $contact->email->email;
            }
            if ($contact->address) {
                $invoice->billing_street = $contact->address->address . ' ' . $contact->address->address2;
                $invoice->billing_city = $contact->address->city;
                $invoice->billing_state = $contact->address->state;
                $invoice->billing_zip = $contact->address->zip;
            }

            if (isset($contact->phone->phone)) {
                $invoice->phonenumber = $contact->phone->phone;
            } else {
                $invoice->phonenumber = '';
            }
        }

        /*
        **  Added by Avni on 11/28/2017 End
        */

        try {
            $pdf = invoice_pdf($invoice);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== FALSE) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';
        if ($this->input->get('print')) {
            $type = 'I';
        }
        ob_end_clean();
        $pdf->Output(mb_strtoupper(slug_it($invoice_number)) . '.pdf', $type);
    }

    public function mark_as_sent($id)
    {
        if (!$id) {
            redirect(admin_url('invoices/list_invoices'));
        }
        $success = $this->invoices_model->set_invoice_sent($id, true);
        if ($success) {
            set_alert('success', _l('invoice_marked_as_sent'));
        } else {
            set_alert('warning', _l('invoice_marked_as_sent_failed'));
        }
        if ($this->input->get('lid')) {
            redirect(admin_url('invoices/list_invoices?lid=' . $this->input->get('lid') . '#' . $id));
        } else {
            redirect(admin_url('invoices/list_invoices#' . $id));
        }
    }

    public function get_due_date()
    {
        if ($this->input->post()) {
            $date = $this->input->post('date');
            $duedate = '';
            if (get_option('invoice_due_after') != 0) {
                $date = to_sql_date($date);
                $d = date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime($date)));
                $duedate = _d($d);
                echo $duedate;
            }
        }
    }

    function rec_invoices()
    {
        $invoices = $this->invoices_model->rec_invoices();
        foreach ($invoices as $invoice) {
            $invoiceId = $invoice->id;
            $recurring_type = $invoice->recurring_type;
            $recurring_evry = $invoice->recurring;
            $recurring_end_date = $invoice->recurring_ends_on;
            $recurring_start_date = $invoice->date;
        }
    }

    /**
     * Added By : Masud
     * Dt : 06/26/2018
     * kanban view for meeting
     */
    public function switch_invoices_kanban($set = 0)
    {
        {
            if ($set == 1) {
                $set = 'true';
            } else {
                $set = 'false';
            }

            $this->session->set_userdata(array(
                'invoices_kanban_view' => $set
            ));

            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * Added By: Masud
     * Dt: 06/27/2018
     * for pinned Invoice
     */
    public function pininvoice()
    {
        $invoice_id = $this->input->post('invoice_id');

        $pindata = $this->invoices_model->pininvoice($invoice_id);

        echo $pindata;
        exit;
    }
}
