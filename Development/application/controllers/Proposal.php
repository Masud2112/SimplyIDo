<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Proposal extends CRM_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('proposaltemplates_model');
        $this->load->model('proposals_model');
        $this->load->model('currencies_model');
        $this->load->model('invoice_items_model');
        $this->load->model('invoices_model');
        $this->load->model('agreements_model');
        $this->load->model('paymentschedules_model');
        $this->load->model('leads_model');
        $this->load->model('projects_model');

        // Model is autoloaded
    }

    /* For finalize Proposal*/
    function view($token, $preview = "")
    {
        if (is_numeric($token)) {
            $id = $token;
            $usertype = "member";
        } else {
            $proposalToken = $this->proposaltemplates_model->getproposalbytoken($token);
            $id = $proposalToken->proposal_id;
            $usertype = $proposalToken->usertype;
            $data['token'] = $token;
        }
        $this->memberlogin($usertype);
        $proposaltemplate = $this->proposaltemplates_model->getclientproposal($id);
        if (is_staff_logged_in() == false) {

            $newdata = array();
            $newdata['brand_id'] = $proposaltemplate->brandid;
            $newdata['authclient'] = $proposalToken->client_id;
            $newdata['authemail'] = $proposalToken->email;
            $newdata['is_sido_admin'] = 0;
            $newdata['is_admin'] = 0;
            $newdata['staff_user_id'] = $proposaltemplate->created_by;
            $newdata['token'] = $token;
            $this->session->set_userdata($newdata);
        } else {
            if (is_staff_logged_in() && $usertype == "client") {
                $newdata = array();
                $newdata['brand_id'] = $proposaltemplate->brandid;
                $this->session->set_userdata($newdata);
            }
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $discount_percent = isset($data['discount_percent']) ? $data['discount_percent'] : 0;
            $discount_amount = isset($data['discount_amount']) ? $data['discount_amount'] : 0;
            unset($data['discount_percent']);
            unset($data['discount_amount']);
            $selected_items = array();
            if (isset($data['group'])) {
                $groups = $data['group'];
                if (!empty($groups)) {
                    foreach ($groups as $group) {
                        if (isset($group['item']) && !empty($group['item'])) {
                            if ($group['gtype'] == 0) {
                                foreach ($group['item'] as $item) {
                                    $selected_items[] = $item;
                                }
                            } elseif ($group['gtype'] == 1) {
                                foreach ($group['item'] as $item) {
                                    if (isset($group['selected_item']) && $group['selected_item'] == $item['type'] . "_" . $item['id']) {
                                        $selected_items[] = $item;
                                    }
                                }
                            } else {
                                foreach ($group['item'] as $item) {
                                    if (!empty($group['selected_item']) && in_array($item['type'] . "_" . $item['id'], $group['selected_item'])) {
                                        $selected_items[] = $item;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (empty($selected_items)) {
                set_alert('warning', 'No item(s) selected in quote group.');
                redirect(site_url('proposal/view/' . $token));
            }
            $data['selected_items'] = json_encode($selected_items);
            $data['total_signer'] = count($data['signatures']);
            $data['is_final'] = 0;
            if (isset($data['rec_payment']) && !empty($data['rec_payment'])) {
                $rec_payment = $data['rec_payment'];
            }
            unset($data['agreement_date']);
            unset($data['client_name']);
            unset($data['proposal_title']);
            unset($data['event_date']);
            unset($data['event_venue']);
            unset($data['group']);
            unset($data['rec_payment']);
            $total_signed = 0;
            $total_client = 0;
            $client_signed = 0;
            foreach ($data['signatures'] as $signature) {
                if (isset($signature['image']) && !empty($signature['image'])) {
                    $total_signed++;
                }
                if (isset($signature['image']) && !empty($signature['image']) && $signature['signer_type'] == 'client') {
                    $client_signed++;
                }
                if ($signature['signer_type'] == 'client') {
                    $total_client++;
                }
            }
            $data['total_signed'] = $total_signed;
            if ($total_client == $client_signed) {
                $data['is_final'] = 1;
            }

            $data['accepted'] = isset($proposalToken) ? $proposalToken->client_id : "";
            $feedback = $this->proposaltemplates_model->add_proposal_feedback($data);

            if ($proposaltemplate->status != "accepted") {
                $this->updatestatus('accepted', $id, true);
            }
            /*if ($feedback) {*/
            $final_feedback = $this->proposaltemplates_model->get_proposal_feedback($id);
            if ($final_feedback->is_final == 1 && $final_feedback->is_invoiced == 0) {
                $brandid = $proposaltemplate->brandid;
                if ($data['rel_type'] == "lead") {
                    $rel_id = $data['rel_id'];
                    $data['rel_content'] = $this->leads_model->get($rel_id);
                    $this->db->select('contactid');
                    $this->db->where('leadid', $rel_id);
                    $this->db->where('brandid', $brandid);
                    $contacts = $this->db->get('tblleadcontact')->row();
                } else {
                    $rel_id = $data['rel_id'];
                    $data['rel_content'] = $this->projects_model->get($rel_id);
                    $this->db->select('contactid');
                    $this->db->where('projectid', $rel_id);
                    $this->db->where('brandid', $brandid);
                    //$this->db->where('isvendor', 0);
                    //$this->db->where('iscollaborator', 0);
                    $contacts = $this->db->get('tblprojectcontact')->row();
                }

                $selected_items = json_decode($data['selected_items'], true);
                $item_ids = array();
                foreach ($selected_items as $key => $selected_item) {
                    if ($selected_item['type'] == 'product') {
                        $product = $this->invoice_items_model->get($selected_item['id']);
                        $item_ids[$key]['description'] = $product->description;
                        $item_ids[$key]['qty'] = $selected_item['qty'];
                        $item_ids[$key]['rate'] = $product->rate;
                        $item_ids[$key]['amount'] = $selected_item['subtotal'];
                        $item_ids[$key]['markupdiscount'] = $selected_item['mkpdisc'];
                        if ($product->is_taxable == 1) {
                            $taxid = $proposaltemplate->proposal_custom_tax;
                            $ptax = get_tax_rate_by_id($taxid);
                            $tax_rate = "";
                            if (!empty($ptax)) {
                                $tax_rate = $ptax->name . "|" . $ptax->taxrate;
                            }
                            $item_ids[$key]['taxname'] = array($tax_rate);
                        }

                    } else {
                        $package = $this->invoice_items_model->get_group($selected_item['id']);
                        $item_ids[$key]['description'] = $package->name;
                        $item_ids[$key]['qty'] = $selected_item['qty'];
                        $item_ids[$key]['rate'] = $package->group_price;
                        $item_ids[$key]['amount'] = $selected_item['subtotal'];
                        $item_ids[$key]['markupdiscount'] = $selected_item['mkpdisc'];
                    }
                }
                if (isset($rec_payment)) {
                    $next_invoice_number = get_option('next_invoice_number');
                    $amount = $data['proposal_total'] / $rec_payment['rec_no'];
                    $transaction_charge = 0;
                    $recurring = $rec_payment['rec_no_of_week_mnth'];
                    $due_date = $rec_payment['rec_end_date'];
                    if ($rec_payment['rec_bill_type'] == "monthly") {
                        $rec_payment['rec_bill_type'] = "month";
                    } else {
                        $rec_payment['rec_bill_type'] = "week";
                    }
                    $invoice_data['clientid'] = $contacts->contactid;
                    $invoice_data['subtotal'] = $amount;
                    $invoice_data['transaction_charge'] = $transaction_charge;
                    $invoice_data['total'] = $amount + $transaction_charge;
                    $invoice_data['number'] = $next_invoice_number;
                    $invoice_data['date'] = date('m/d/Y');
                    $invoice_data['allowed_payment_modes'] = Array(4, 'stripe', 'paypal');
                    $invoice_data['duedate'] = $due_date;
                    $invoice_data['newitems'] = $item_ids;
                    $invoice_data['recurring'] = $recurring;
                    $invoice_data['recurring_type'] = $rec_payment['rec_bill_type'];
                    $invoice_data['custom_recurring'] = 1;
                    $invoice_data['recurring_ends_on'] = $due_date;
                    if ($data['rel_type'] == "lead") {
                        $invoice_data['leadid'] = $data['rel_id'];
                    } else {
                        $invoice_data['project_id'] = $data['rel_id'];
                    }
                    $invoice_data['sale_agent'] = $data['rel_content']->addedfrom;
                    $invoice_data['clientnote'] = "";
                    $invoice_data['terms'] = "";
                    $this->invoices_model->add($invoice_data);
                } else {
                    $invoice_data = array();
                    $invoice_data['clientid'] = $contacts->contactid;
                    $invoice_data['number'] = str_pad(1, 2, '0', STR_PAD_LEFT);

                    $invoice_data['proposal_number'] = str_pad($proposaltemplate->proposal_version, 2, '0', STR_PAD_LEFT);

                    $invoice_data['number_format'] = !empty($proposaltemplate->number_format) ? $proposaltemplate->number_format : get_brand_option('invoice_number_format');

                    $invoice_data['date'] = date('m/d/Y', strtotime($proposaltemplate->datecreated));
                    $invoice_data['allowed_payment_modes'] = Array(4, 'stripe', 'paypal');
                    $invoice_data['duedate'] = date('m/d/Y', strtotime($data['rel_content']->eventstartdatetime));
                    $amount = $data['proposal_total'];
                    $invoice_data['subtotal'] = $data['proposal_subtotal'];
                    $transaction_charge = 0;
                    $invoice_data['transaction_charge'] = $transaction_charge;
                    $invoice_data['total'] = $data['proposal_total'] + $transaction_charge;
                    $invoice_data['newitems'] = $item_ids;
                    if ($data['rel_type'] == "lead") {
                        $invoice_data['leadid'] = $data['rel_id'];
                        $invoice_data['leaddate'] = date('Y-m-d', strtotime($data['rel_content']->eventstartdatetime));
                    } else {
                        $invoice_data['project_id'] = $data['rel_id'];
                        $invoice_data['projectdate'] = date('Y-m-d', strtotime($data['rel_content']->eventstartdatetime));
                    }
                    $invoice_data['sale_agent'] = $data['rel_content']->addedfrom;
                    $invoice_data['clientnote'] = "";
                    $invoice_data['terms'] = "";
                    if ($proposaltemplate->othrdiscval < 0) {
                        $invoice_data['discount_percent'] = $discount_percent;
                        $invoice_data['discount_total'] = $discount_amount;
                        $invoice_data['discount_type'] = $proposaltemplate->othrdisctype;


                    }
                    $invoice_id = $this->invoices_model->add($invoice_data);
                    $proposalInvoice = array();
                    $proposalInvoice['invoice_id'] = $invoice_id;
                    $proposalInvoice['proposal_id'] = $id;
                    $this->proposaltemplates_model->addproposalinvoice($proposalInvoice);
                }
                $this->db->where('proposal_id', $data['proposal_id']);
                $this->db->update('tblproposaltemplate_feedback', array('is_invoiced' => 1));
                if ($this->db->affected_rows() > 0) {
                    $final_feedback->is_invoiced = 1;
                }

                if ($final_feedback->is_invoiced == 1) {
                    set_alert('success', 'THANK YOU! Your invoice is now available.');
                } else {
                    set_alert('success', 'THANK YOU! Your invoice will be available when the last client signs the proposal.');
                }
            } elseif ($final_feedback->is_invoiced == 1) {
                $invoiceid = $this->proposaltemplates_model->get_proposal_invoices_id($data['proposal_id']);
                $invoice_data = array();
                $invoice_data['subtotal'] = $data['proposal_subtotal'];
                $transaction_charge = 0;
                $invoice_data['transaction_charge'] = $transaction_charge;
                $invoice_data['total'] = $data['proposal_total'] + $transaction_charge;
                $invoice_status = get_invoice_status($invoiceid);
                if ($invoice_status == 2) {
                    $invoice_data['status'] = 3;
                }
                $updateinvoice = $this->invoices_model->updateinvoice($invoice_data, $invoiceid);
                /*$this->notify_proposal_change($data, $signature['signer_id']);*/
                if ($updateinvoice == true) {
                    $data['token'] = $token;
                    $this->proposaltemplates_model->sentproposalupdatemail($data);
                    set_alert('success', 'Your invoices is now updated.');
                }
            }
            if (($data['total_signer'] - 1) == $data['total_signed']) {
                foreach ($data['signatures'] as $signature) {
                    if ($signature['counter_signer'] == 1) {
                        $data['name'] = $proposaltemplate->name;
                        $this->notify_to_sign($data, $signature['signer_id']);
                    }
                }
            }
            redirect(site_url('proposal/view/' . $token), 'refresh');
            /*}*/
        }
        $data['proposal'] = $proposaltemplate;
        if (isset($proposaltemplate->rel_type) && $proposaltemplate->rel_type == "lead") {
            $data['rel_content'] = $this->leads_model->getlead($proposaltemplate->rel_id);
        } elseif (isset($proposaltemplate->rel_type) && $proposaltemplate->rel_type == "project") {
            $data['rel_content'] = $this->projects_model->getproject($proposaltemplate->rel_id);
        }

        if (isset($data['rel_content']) && !empty($data['rel_content'])) {
            $vanue_id = $data['rel_content']->venueid;
            if ($vanue_id > 0) {
                $data['venue'] = $this->venues_model->getvenue($vanue_id);
            }

        }

        if ($proposaltemplate->rel_type != "" && $proposaltemplate->rel_id > 0) {
            $clients = $this->get_clients($proposaltemplate->rel_type, $proposaltemplate->rel_id);
            $data['clients'] = $clients;
        }

        if (isset($data['proposal']->ps_template) && $data['proposal']->ps_template > 0) {
            $data['proposal']->pmt_sdl_template = $this->get_payment_schedule_template($data['proposal']->ps_template, $print = false);
        }
        $data['rel_id'] = $proposaltemplate->rel_id;
        $data['quotes'] = $this->proposaltemplates_model->getproposal_quotes($id);
        $data['gallery'] = $this->proposaltemplates_model->getproposal_gallery($id);
        $data['files'] = $this->proposaltemplates_model->getproposal_files($id);
        $title = _l('view_proposal') . " - " . $proposaltemplate->name;
        $created_by = $proposaltemplate->created_by;
        $data['accounting_assets'] = true;

        $this->load->model('taxes_model');
        //$data['taxes'] = $this->taxes_model->get();
        $this->load->model('invoice_items_model');
        $data['ajaxItems'] = false;
        if (total_rows('tblitems') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get();
        } else {
            $data['items'] = array();
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $data['statuses'] = $this->proposals_model->get_statuses();
        $data['staff'] = $this->staff_model->getstaff($created_by, 1);
        $data['currencies'] = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['agreements'] = $this->agreements_model->getagreements();
        $data['title'] = $title;
        $feedback = $this->proposaltemplates_model->get_proposal_feedback($id);
        if (isset($feedback) && !empty($feedback)) {
            $data['proposal']->feedback = $feedback;
            $data['proposal_selected_items'] = json_decode($data['proposal']->feedback->selected_items, true);
        }
        $data['rec_payment'] = $this->proposaltemplates_model->get_rec_payment($id);
        //$data['tasks'] = $this->tasks_model->get_task_by_rel($proposaltemplate->rel_type, $proposaltemplate->rel_id);
        $data['brandid'] = $proposaltemplate->brandid;
        if (isset($proposalToken) || (isset($proposalToken) && is_staff_logged_in())) {
            $data['authclient'] = $proposalToken->client_id;
            $data['authemail'] = $proposalToken->email;
            $data['authtype'] = $proposalToken->usertype;
        } else {
            if (is_staff_logged_in()) {
                $data['authclient'] = get_staff_user_id();
                $data['authemail'] = get_staff_email();
                $data['authtype'] = "member";
            }
        }
        $invoices = $this->proposaltemplates_model->get_proposal_invoices($id);
        $nextinvoices = $this->proposaltemplates_model->get_proposal_invoices($id, 1);
        $paidinvoices = $this->proposaltemplates_model->get_proposal_invoices($id, 2);

        if (count($invoices) > 0) {
            $data['invoices'] = $invoices;
            $data['nextinvoice'] = $invoices[0];
            $data['paidinvoices'] = $paidinvoices;
        }
        if ($preview == "preview") {
            $data['preview'] = $preview;
        }
        if (($this->input->get('pid') && $this->input->get('pid') > 0) || ($this->input->get('lid') && $this->input->get('lid') > 0) || (isset($data['token']) && $data['token'] != "")) {
            $status = $this->proposaltemplates_model->get_proposal_status($id);
            $data['proposal']->status = $status;
        }
        if ($data['proposal']->isclosed == 1) {
            $data['proposal']->status = "closed";
        }
        $data['bullet_url'] = "javascript:void(0)";

        $stripePublishableKey = get_custom_brand_option('paymentmethod_stripe_api_publishable_key', $proposaltemplate->brandid);
        $stripePublishableKey->value;
        $data['stripePublishableKey'] = $stripePublishableKey->value;
        $this->load->view('proposaltemplates/viewproposal', $data);
    }

    /**
     * Added By: Masud
     * Dt: 07/12/2018
     * for pinned Proposal
     */
    public function updatestatus($status, $id, $print = false)
    {

        if (isset($_GET['lid']) && $_GET['lid'] > 0) {
            $rel_id = $_GET['lid'];
            $rel_link = '?lid=' . $rel_id;
        } elseif (isset($_GET['pid']) && $_GET['pid'] > 0) {
            $rel_id = $_GET['pid'];
            $rel_link = '?pid=' . $rel_id;
        } else {
            $rel_id = "";
            $rel_link = "";
        }
        if ($this->input->post('usertoken') && !empty($this->input->post('usertoken'))) {
            $reason = $this->input->post('reason');
            $declined = $this->proposaltemplates_model->addreason($id, $status, $reason);
            if ($declined == true) {
                $data = $this->input->post();
                $data['id'] = $id;
                $this->proposaltemplates_model->sentdeclinemail($data);
                set_alert('success', 'Proposal has been declined');
                redirect(site_url('proposal/view/' . $this->input->post('usertoken')));
            }
            die;
        } else {
            $pindata = $this->proposaltemplates_model->updatestatus($id, $status);
            if ($print == true) {
                return true;
            } else {

                if ($pindata == true) {
                    redirect(admin_url('proposaltemplates' . $rel_link));
                } else {
                    redirect(admin_url('proposaltemplates/view/' . $id . $rel_link));
                }
            }
        }

    }

    function quote_selecteditems()
    {
        $selected_items = $this->input->post();
        echo $this->load->view('proposaltemplates/selected_services', array('selected_items' => $selected_items), true);
        die();
    }

    function get_payment_schedule_template($id = "", $print = 'true')
    {
        if ($id > 0) {
            $paymentschedule = $this->paymentschedules_model->getpaymentschedule($id);
            if (isset($paymentschedule) && !empty($paymentschedule)) {
                if ($this->input->is_ajax_request()) {
                    $paymentschedule->name = "";
                    $paymentschedule->is_template = 0;
                }
                $data['paymentschedule'] = $paymentschedule;
            }
        }
        $data['duedate_types'] = get_duedate_type();
        $data['duedate_criteria'] = get_duedate_criteria();
        $data['duedate_duration'] = get_duedate_duration();
        $data['amount_types'] = get_amount_type();
        if ($print == true) {
            return $this->load->view('proposaltemplates/paymentschedule_temp', $data);
        } else {
            return $data;
        }
    }

    function get_clients($type, $id)
    {
        /*if (isset($_GET['lid']) || isset($_GET['pid'])) {*/
        if ($type == "lead") {
            $data['rel_content'] = $this->leads_model->getlead($id);
            $brandid = get_user_session();
            $this->db->select('contactid');
            $this->db->distinct();
            $this->db->where('leadid', $id);
            $this->db->where('brandid', $brandid);
            $contacts = $this->db->get('tblleadcontact')->result();

        } else {
            $data['rel_content'] = $this->projects_model->getproject($id);
            $brandid = get_user_session();
            $this->db->select('id');
            $this->db->where('(parent = ' . $id . ' OR id = ' . $id . ')');
            $this->db->where('deleted', 0);
            $related_project_ids = $this->db->get('tblprojects')->result_array();
            $related_project_ids = array_column($related_project_ids, 'id');
            if (!empty($related_project_ids)) {
                $related_project_ids = implode(",", $related_project_ids);
                $this->db->select('contactid');
                $this->db->distinct();
                $this->db->where('(projectid IN (' . $related_project_ids . ') OR eventid IN (' . $related_project_ids . '))');
                $this->db->where('isvendor', 0);
                $this->db->where('iscollaborator', 0);
                $this->db->where('brandid', $brandid);
                $contacts = $this->db->get('tblprojectcontact')->result();
            }
        }

        foreach ($contacts as $key => $contact) {
            $contactid = $contact->contactid;
            $query = "SELECT firstname,lastname FROM tbladdressbook WHERE deleted=0 AND addressbookid=" . $contactid;
            $result = $this->db->query($query);
            $name = "";
            if (!empty($result->first_row())) {
                $clients[$key]['id'] = $contactid;
                $name = $result->first_row()->firstname . " " . $result->first_row()->lastname;
                $clients[$key]['name'] = $name;
                $clients[$key]['firstname'] = $result->first_row()->firstname;
                $clients[$key]['lastname'] = $result->first_row()->lastname;

                $query = "SELECT phone FROM tbladdressbookphone WHERE type='primary' AND addressbookid=" . $contactid;
                $result = $this->db->query($query);
                $phone = "";
                if (!empty($result->first_row())) {
                    $phone = $result->first_row()->phone;
                    $clients[$key]['phone'] = $phone;
                }


                $query = "SELECT email FROM tbladdressbookemail WHERE type='primary' AND addressbookid=" . $contactid;
                $result = $this->db->query($query);
                $email = "";
                if (!empty($result->first_row())) {
                    $email = $result->first_row()->email;
                    $clients[$key]['email'] = $email;
                }
            }
        }
        if (!empty($clients)) {
            return $clients;
        }
        /*}*/
    }

    public function memberlogin($usertype = "client")
    {
        if ($usertype == "member") {
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

            if (!extension_loaded('mbstring') && (!function_exists('mb_strtoupper') || !function_exists('mb_strtolower'))) {
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
            $this->db->where('staffid', get_staff_user_id());
            $this->db->update('tblstaff', array('last_activity' => date('Y-m-d H:i:s')));

            /**
             * Added By : Vaidehi
             * Dt: 10/13/2017
             * get all brands for account owner and team member
             */
            $session_data = get_session_data();
            $is_sido_admin = $session_data['is_sido_admin'];
            $is_admin = $session_data['is_admin'];

            if ($is_sido_admin == 0 && $is_admin == 0) {
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

                //$this->init_quick_actions_links();
            }

            if (is_mobile()) {
                $this->session->set_userdata(array(
                    'is_mobile' => true
                ));
            } else {
                $this->session->unset_userdata('is_mobile');
            }

            $auto_loaded_vars = array(
                'current_user' => $this->staff_model->get(get_staff_user_id()),
                'app_language' => $language,
                'locale' => get_locale_key($language),
                'unread_notifications' => total_rows('tblnotifications', array('touserid' => get_staff_user_id(), 'isread' => 0)),
                'google_api_key' => get_option('google_api_key'),
                'current_version' => $this->_current_version,
                //'tasks_filter_assignees'    => $this->get_tasks_distinct_assignees(),
                'task_statuses' => $this->tasks_model->get_status(),
                'brands' => $brands
            );

            $GLOBALS['current_user'] = $auto_loaded_vars['current_user'];

            /**
             * Added By : Vaidehi
             * Dt: 10/13/2017
             * get all brands for account owner and team member
             */
            $GLOBALS['brands'] = $auto_loaded_vars['brands'];

            $auto_loaded_vars = do_action('before_set_auto_loaded_vars_admin_area', $auto_loaded_vars);
            $this->load->vars($auto_loaded_vars);
        }
    }

    function notify_to_sign($data, $id, $integration = false)
    {
        $notification_data = array(
            'description' => ($integration == false) ? 'not_to_signed_proposal' : 'not_to_signed_proposal',
            'touserid' => $id,
            'eid' => $data['proposal_id'],
            'brandid' => get_user_session(),
            'not_type' => 'proposal',
            'link' => 'proposaltemplates/viewproposal/' . $data['proposal_id'],
            'additional_data' => ($integration == false ? serialize(array(
                $data['name']
            )) : serialize(array()))
        );
        if (add_notification($notification_data)) {
            pusher_trigger_notification(array($id));
        }
    }

    /**
     * Added By: Masud
     * Dt: 09/07/2018
     * for Gratuity
     */
    public function addinvoicegratuity($invoice_id)
    {
        $data = $this->input->post();
        $this->invoices_model->addinvoicegratuity($invoice_id, $data);
    }

    /**
     * Added By: Masud
     * Dt: 09/13/2018
     * for Member sign
     */
    public function addmembersign($id)
    {
        $data = $this->input->post();
        echo $this->proposaltemplates_model->addmembersign($id, $data);
    }

    function payment()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if (!isset($data['token'])) {
                if (isset($data['lid']) && isset($data['type'])) {
                    $rellink = $data['proposalid'] . "?" . $data['type'] . "=" . $data['lid'];
                }
            } else {
                $rellink = $data['token'];
            }
            $this->proposaltemplates_model->sentPaymentemail($data);
            set_alert('success', 'Payment notification sent');

            redirect(site_url('proposal/view/' . $rellink));
        }
    }

    function decline($id)
    {
        if (isset($_GET['lid']) && $_GET['lid'] > 0) {
            $rel_link = '?lid=' . $_GET['lid'];
        } elseif (isset($_GET['pid']) && $_GET['pid'] > 0) {
            $rel_link = '?pid=' . $_GET['pid'];
        } else {
            $rel_link = '';
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            $this->proposaltemplates_model->decline($id, $data);
        }
        if ($this->input->post('usertoken')) {
            $rel_link = $data['usertoken'];
        } else {
            $rel_link = $id . $rel_link;
        }
        set_alert('success', _l('declined_successfully', _l('proposaltemplate')));
        redirect(site_url('proposal/view/' . $rel_link));
    }

    function makepayment($token, $preview = "")
    {
        if (is_numeric($token)) {
            $id = $token;
            $usertype = "member";
        } else {
            $proposalToken = $this->proposaltemplates_model->getproposalbytoken($token);
            $id = $proposalToken->proposal_id;
            $usertype = $proposalToken->usertype;
            $data['token'] = $token;
        }
        $this->memberlogin($usertype);
        $proposaltemplate = $this->proposaltemplates_model->getclientproposal($id);
        if (is_staff_logged_in() == false) {

            $newdata = array();
            $newdata['brand_id'] = $proposaltemplate->brandid;
            $newdata['authclient'] = $proposalToken->client_id;
            $newdata['authemail'] = $proposalToken->email;
            $newdata['authemail'] = $proposalToken->email;
            $newdata['is_sido_admin'] = 0;
            $newdata['is_admin'] = 0;
            $newdata['staff_user_id'] = $proposaltemplate->created_by;
            $newdata['token'] = $token;
            $this->session->set_userdata($newdata);
        }
        $data['proposal'] = $proposaltemplate;
        if (isset($proposaltemplate->rel_type) && $proposaltemplate->rel_type == "lead") {
            $data['rel_content'] = $this->leads_model->getlead($proposaltemplate->rel_id);
        } elseif (isset($proposaltemplate->rel_type) && $proposaltemplate->rel_type == "project") {
            $data['rel_content'] = $this->projects_model->getproject($proposaltemplate->rel_id);
        }

        if (isset($data['rel_content']) && !empty($data['rel_content'])) {
            $vanue_id = $data['rel_content']->venueid;
            if ($vanue_id > 0) {
                $data['venue'] = $this->venues_model->getvenue($vanue_id);
            }

        }

        if ($proposaltemplate->rel_type != "" && $proposaltemplate->rel_id > 0) {
            $clients = $this->get_clients($proposaltemplate->rel_type, $proposaltemplate->rel_id);
            $data['clients'] = $clients;
        }

        if (isset($data['proposal']->ps_template) && $data['proposal']->ps_template > 0) {
            $data['proposal']->pmt_sdl_template = $this->get_payment_schedule_template($data['proposal']->ps_template, $print = false);
        }
        $data['rel_id'] = $proposaltemplate->rel_id;
        $data['quotes'] = $this->proposaltemplates_model->getproposal_quotes($id);
        $data['gallery'] = $this->proposaltemplates_model->getproposal_gallery($id);
        $data['files'] = $this->proposaltemplates_model->getproposal_files($id);
        $title = _l('view_proposal') . " - " . $proposaltemplate->name;
        $created_by = $proposaltemplate->created_by;
        $data['accounting_assets'] = true;

        $this->load->model('taxes_model');
        //$data['taxes'] = $this->taxes_model->get();
        $this->load->model('invoice_items_model');
        $data['ajaxItems'] = false;
        if (total_rows('tblitems') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get();
        } else {
            $data['items'] = array();
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $data['statuses'] = $this->proposals_model->get_statuses();
        $data['staff'] = $this->staff_model->getstaff($created_by, 1);
        $data['currencies'] = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['agreements'] = $this->agreements_model->getagreements();
        $data['title'] = $title;
        $feedback = $this->proposaltemplates_model->get_proposal_feedback($id);
        if (isset($feedback) && !empty($feedback)) {
            $data['proposal']->feedback = $feedback;
            $data['proposal_selected_items'] = json_decode($data['proposal']->feedback->selected_items, true);
        }
        $data['rec_payment'] = $this->proposaltemplates_model->get_rec_payment($id);
        //$data['tasks'] = $this->tasks_model->get_task_by_rel($proposaltemplate->rel_type, $proposaltemplate->rel_id);
        $data['brandid'] = $proposaltemplate->brandid;
        if (isset($proposalToken)) {
            $data['authclient'] = $proposalToken->client_id;
            $data['authemail'] = $proposalToken->email;
            $data['authtype'] = $proposalToken->usertype;
        } else {
            if (is_staff_logged_in()) {
                $data['authclient'] = get_staff_user_id();
                $data['authemail'] = get_staff_email();
                $data['authtype'] = "member";
            }
        }
        $invoices = $this->proposaltemplates_model->get_proposal_invoices($id);
        $nextinvoices = $this->proposaltemplates_model->get_proposal_invoices($id, 1);
        $paidinvoices = $this->proposaltemplates_model->get_proposal_invoices($id, 2);
        /*echo "<pre>";
        print_r($invoices);
        print_r($nextinvoices);
        print_r($paidinvoices);
        die();*/
        if (count($invoices) > 0) {
            $data['invoices'] = $invoices;
            $data['nextinvoice'] = $invoices[0];
            $data['paidinvoices'] = $paidinvoices;
            $data['invoice'] = $invoices[0];
            $data['amount'] = get_invoice_total_left_to_pay($data['invoice']->id, $data['invoice']->total);
        }
        if ($preview == "preview") {
            $data['preview'] = $preview;
        }
        if (($this->input->get('pid') && $this->input->get('pid') > 0) || ($this->input->get('lid') && $this->input->get('lid') > 0) || (isset($data['token']) && $data['token'] != "")) {
            $status = $this->proposaltemplates_model->get_proposal_status($id);
            $data['proposal']->status = $status;
        }
        if ($data['proposal']->isclosed == 1) {
            $data['proposal']->status = "closed";
        }
        $data['stripePublishableKey'] = get_brand_option('paymentmethod_stripe_api_publishable_key');
        $data['bullet_url'] = site_url('proposal/view/' . $token);
        $data['page'] = "payment";
        $this->load->view('proposaltemplates/makepayment2', $data);
    }

    function addnewpayment()
    {
        if ($this->input->post()) {
            $postdata = $this->input->post();
            $proposaltemplate = $this->proposaltemplates_model->getclientproposal($postdata['proposal_id']);
            $data['proposal'] = $proposaltemplate;
            if (isset($postdata['rel_type']) && $postdata['rel_type'] == "lead") {
                $data['rel_content'] = $this->leads_model->getlead($proposaltemplate->rel_id);
            } elseif (isset($postdata['rel_type']) && $postdata['rel_type'] == "project") {
                $data['rel_content'] = $this->projects_model->getproject($proposaltemplate->rel_id);
            }
            $pv = array();
            $pv['status'] = 0;
            $pv['duedate_date'] = "0000-00-00";
            $pv['duedate_type'] = "project_date";
            $pv['payment_method'] = "cash";
            $pv['price_amount'] = $postdata['remaining_amount'];
            $pv['status'] = 0;
            $pv['price_type'] = "percentage";
            $pv['price_percentage'] = 100;
            $pv['paymentdetailid'] = 0;
            $pv['duedate_number'] = 1;
            $pv['custom_range_duration'] = "";
            $pv['duedate_criteria'] = "";
            $data['duedate_types'] = get_duedate_type();
            $data['duedate_criteria'] = get_duedate_criteria();
            $data['duedate_duration'] = get_duedate_duration();
            $data['amount_types'] = get_amount_type();
            $data['pv'] = $pv;
            $data['pk'] = $postdata['pk'];
            $data['pe'] = $postdata['pe'];
            /*echo "<pre>";
            print_r($data);
            die();*/
            $this->load->view('proposaltemplates/payment', $data);
        }
    }

    function createemail($proposalid)
    {
        $this->memberlogin('member');
        $proposaltemplate = $this->proposaltemplates_model->getclientproposal($proposalid);
        if (isset($proposaltemplate->rel_type) && $proposaltemplate->rel_type == "lead") {
            $rel_content = $this->leads_model->get($proposaltemplate->rel_id);
        } elseif (isset($proposaltemplate->rel_type) && $proposaltemplate->rel_type == "project") {
            $rel_content = $this->projects_model->get($proposaltemplate->rel_id);
        }
        if ($this->input->post()) {
            $email['proposal'] = $proposaltemplate;
            $email['rel_content'] = $rel_content;
            $email['title'] = "PROPOSAL";
            $email['emailtemp'] = 1;
            $emailview = $this->load->view('proposaltemplates/emailhead', $email);
            $data = $this->input->post();
            $data['emailview'] = $emailview;
            $this->proposaltemplates_model->sentproposal($proposalid, $data);

            set_alert('success', 'Proposal sent successfully');
            if ($this->input->get('pid') && $this->input->get('pid') > 0) {
                redirect(site_url('proposal/view/' . $proposalid . "?pid=" . $this->input->get('pid')));
            } elseif ($this->input->get('lid') && $this->input->get('lid') > 0) {
                redirect(site_url('proposal/view/' . $proposalid . "?lid=" . $this->input->get('lid')));
            } else {
                redirect(site_url('proposal/view/' . $proposalid));
            }
        }
        $data = array();
        $proposaltemplate = $this->proposaltemplates_model->getclientproposal($proposalid);
        $signermails = "";
        foreach (json_decode($proposaltemplate->signatures) as $signer) {
            if ($signer->signer_type == "member") {
                $signermail = get_staff_email($signer->signer_id);
            } else {
                $signermail = get_addressbook_email($signer->signer_id);
            }
            $signermails .= $signermail . "; ";
        }
        $data['signermails'] = $signermails;
        $data['proposal'] = $proposaltemplate;
        $data['rel_content'] = $rel_content;
        if (isset($_GET['lid']) || isset($_GET['pid'])) {
            if (isset($_GET['lid'])) {
                $id = $_GET['lid'];
                $clients = $this->proposaltemplates_model->get_clients('lead', $id);
                $data['rel_content'] = $this->leads_model->get($id);
                $data['addressbooks'] = $this->addressbooks_model->get_existing_contacts('tblleadcontact', "leadid", $this->input->get('lid'));
            } else {
                $id = $_GET['pid'];
                $clients = $this->proposaltemplates_model->get_clients('project', $id);
                $data['rel_content'] = $this->projects_model->get($id);
                $data['addressbooks'] = $this->addressbooks_model->get_existing_contacts('tblprojectcontact', "projectid", $this->input->get('pid'));
            }

        } elseif (isset($proposaltemplate->rel_type) && $proposaltemplate->rel_id > 0) {
            $clients = $this->proposaltemplates_model->get_clients($proposaltemplate->rel_type, $proposaltemplate->rel_id);
            if ($proposaltemplate->rel_type == 'lead') {
                $data['rel_content'] = $this->leads_model->get($proposaltemplate->rel_id);
            } else {
                $data['rel_content'] = $this->projects_model->get($proposaltemplate->rel_id);
            }
        }
        if (isset($data['rel_content']) && !empty($data['rel_content'])) {
            $vanue_id = $data['rel_content']->venueid;
            if ($vanue_id > 0) {
                $data['venue'] = $this->venues_model->get($vanue_id);
            }
            if (!empty($clients) && count($clients) > 0) {
                $data['eclients'] = $clients;
            }
        }
        $data['members'] = $this->staff_model->get('', 1, array('is_not_staff' => 0));

        if (!empty($clients) && count($clients) > 0) {
            $data['clients'] = $clients;
        }

        $this->load->view('proposaltemplates/createemail', $data);
    }

    function proposal($token, $preview = "")
    {
        if (is_numeric($token)) {
            $id = $token;
            $usertype = "member";
        } else {
            $proposalToken = $this->proposaltemplates_model->getproposalbytoken($token);
            $id = $proposalToken->proposal_id;
            $usertype = $proposalToken->usertype;
            $data['token'] = $token;
        }
        $this->memberlogin($usertype);
        $proposaltemplate = $this->proposaltemplates_model->getclientproposal($id);
        if (is_staff_logged_in() == false) {

            $newdata = array();
            $newdata['brand_id'] = $proposaltemplate->brandid;
            $newdata['authclient'] = $proposalToken->client_id;
            $newdata['authemail'] = $proposalToken->email;
            $newdata['authemail'] = $proposalToken->email;
            $newdata['is_sido_admin'] = 0;
            $newdata['is_admin'] = 0;
            $newdata['staff_user_id'] = $proposaltemplate->created_by;
            $newdata['token'] = $token;
            $this->session->set_userdata($newdata);
        }

        if ($this->input->post()) {
            $data = $this->input->post();

            $discount_percent = isset($data['discount_percent']) ? $data['discount_percent'] : 0;
            $discount_amount = isset($data['discount_amount']) ? $data['discount_amount'] : 0;
            unset($data['discount_percent']);
            unset($data['discount_amount']);
            $selected_items = array();
            if (isset($data['group'])) {
                $groups = $data['group'];
                if (!empty($groups)) {
                    foreach ($groups as $group) {
                        if (isset($group['item']) && !empty($group['item'])) {
                            if ($group['gtype'] == 0) {
                                foreach ($group['item'] as $item) {
                                    $selected_items[] = $item;
                                }
                            } elseif ($group['gtype'] == 1) {
                                foreach ($group['item'] as $item) {
                                    if (isset($group['selected_item']) && $group['selected_item'] == $item['id']) {
                                        $selected_items[] = $item;
                                    }
                                }
                            } else {
                                foreach ($group['item'] as $item) {
                                    if (!empty($group['selected_item']) && in_array($item['id'], $group['selected_item'])) {
                                        $selected_items[] = $item;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (empty($selected_items)) {
                set_alert('warning', 'No item(s) selected in quote group.');
                redirect(site_url('proposal/view/' . $token));
            }
            $data['selected_items'] = json_encode($selected_items);
            $data['total_signer'] = count($data['signatures']);
            $data['is_final'] = 0;
            if (isset($data['rec_payment']) && !empty($data['rec_payment'])) {
                $rec_payment = $data['rec_payment'];
            }
            unset($data['agreement_date']);
            unset($data['client_name']);
            unset($data['proposal_title']);
            unset($data['event_date']);
            unset($data['event_venue']);
            unset($data['group']);
            unset($data['rec_payment']);
            $total_signed = 0;
            $total_client = 0;
            $client_signed = 0;
            foreach ($data['signatures'] as $signature) {
                if (isset($signature['image']) && !empty($signature['image'])) {
                    $total_signed++;
                }
                if (isset($signature['image']) && !empty($signature['image']) && $signature['signer_type'] == 'client') {
                    $client_signed++;
                }
                if ($signature['signer_type'] == 'client') {
                    $total_client++;
                }
            }
            $data['total_signed'] = $total_signed;
            if ($total_client == $client_signed) {
                $data['is_final'] = 1;
            }

            $data['accepted'] = isset($proposalToken) ? $proposalToken->client_id : "";
            $feedback = $this->proposaltemplates_model->add_proposal_feedback($data);

            if ($proposaltemplate->status != "accepted") {
                $this->updatestatus('accepted', $id, true);
            }
            /*if ($feedback) {*/
            $final_feedback = $this->proposaltemplates_model->get_proposal_feedback($id);
            if ($final_feedback->is_final == 1 && $final_feedback->is_invoiced == 0) {
                $brandid = $proposaltemplate->brandid;
                if ($data['rel_type'] == "lead") {
                    $rel_id = $data['rel_id'];
                    $data['rel_content'] = $this->leads_model->get($rel_id);
                    $this->db->select('contactid');
                    $this->db->where('leadid', $rel_id);
                    $this->db->where('brandid', $brandid);
                    $contacts = $this->db->get('tblleadcontact')->row();
                } else {
                    $rel_id = $data['rel_id'];
                    $data['rel_content'] = $this->projects_model->get($rel_id);
                    $this->db->select('contactid');
                    $this->db->where('projectid', $rel_id);
                    $this->db->where('brandid', $brandid);
                    //$this->db->where('isvendor', 0);
                    //$this->db->where('iscollaborator', 0);
                    $contacts = $this->db->get('tblprojectcontact')->row();
                }

                $selected_items = json_decode($data['selected_items'], true);
                $item_ids = array();
                foreach ($selected_items as $key => $selected_item) {
                    if ($selected_item['type'] == 'product') {
                        $product = $this->invoice_items_model->get($selected_item['id']);
                        $item_ids[$key]['description'] = $product->description;
                        $item_ids[$key]['qty'] = $selected_item['qty'];
                        $item_ids[$key]['rate'] = $product->rate;
                        $item_ids[$key]['amount'] = $selected_item['subtotal'];
                        $item_ids[$key]['markupdiscount'] = $selected_item['mkpdisc'];
                        if ($product->is_taxable == 1) {
                            $taxid = $proposaltemplate->proposal_custom_tax;
                            $ptax = get_tax_rate_by_id($taxid);
                            $tax_rate = $ptax->name . "|" . $ptax->taxrate;
                            $item_ids[$key]['taxname'] = array($tax_rate);
                        }

                    } else {
                        $package = $this->invoice_items_model->get_group($selected_item['id']);
                        $item_ids[$key]['description'] = $package->name;
                        $item_ids[$key]['qty'] = $selected_item['qty'];
                        $item_ids[$key]['rate'] = $package->group_price;
                        $item_ids[$key]['amount'] = $selected_item['subtotal'];
                        $item_ids[$key]['markupdiscount'] = $selected_item['mkpdisc'];
                    }
                }
                if (isset($rec_payment)) {
                    $next_invoice_number = get_option('next_invoice_number');
                    $amount = $data['proposal_total'] / $rec_payment['rec_no'];
                    $transaction_charge = 0;
                    $recurring = $rec_payment['rec_no_of_week_mnth'];
                    $due_date = $rec_payment['rec_end_date'];
                    if ($rec_payment['rec_bill_type'] == "monthly") {
                        $rec_payment['rec_bill_type'] = "month";
                    } else {
                        $rec_payment['rec_bill_type'] = "week";
                    }
                    $invoice_data['clientid'] = $contacts->contactid;
                    $invoice_data['subtotal'] = $amount;
                    $invoice_data['transaction_charge'] = $transaction_charge;
                    $invoice_data['total'] = $amount + $transaction_charge;
                    $invoice_data['number'] = $next_invoice_number;
                    $invoice_data['date'] = date('m/d/Y');
                    $invoice_data['allowed_payment_modes'] = Array(4, 'stripe', 'paypal');
                    $invoice_data['duedate'] = $due_date;
                    $invoice_data['newitems'] = $item_ids;
                    $invoice_data['recurring'] = $recurring;
                    $invoice_data['recurring_type'] = $rec_payment['rec_bill_type'];
                    $invoice_data['custom_recurring'] = 1;
                    $invoice_data['recurring_ends_on'] = $due_date;
                    if ($data['rel_type'] == "lead") {
                        $invoice_data['leadid'] = $data['rel_id'];
                    } else {
                        $invoice_data['project_id'] = $data['rel_id'];
                    }
                    $invoice_data['sale_agent'] = $data['rel_content']->addedfrom;
                    $invoice_data['clientnote'] = "";
                    $invoice_data['terms'] = "";
                    $this->invoices_model->add($invoice_data);
                } else {
                    $invoice_data = array();
                    $invoice_data['clientid'] = $contacts->contactid;
                    $invoice_data['number'] = str_pad(1, 2, '0', STR_PAD_LEFT);

                    $invoice_data['proposal_number'] = str_pad($proposaltemplate->proposal_version, 2, '0', STR_PAD_LEFT);

                    $invoice_data['number_format'] = !empty($proposaltemplate->number_format) ? $proposaltemplate->number_format : get_brand_option('invoice_number_format');

                    $invoice_data['date'] = date('m/d/Y', strtotime($proposaltemplate->datecreated));
                    $invoice_data['allowed_payment_modes'] = Array(4, 'stripe', 'paypal');
                    $invoice_data['duedate'] = date('m/d/Y', strtotime($data['rel_content']->eventstartdatetime));
                    $amount = $data['proposal_total'];
                    $invoice_data['subtotal'] = $data['proposal_subtotal'];
                    $transaction_charge = 0;
                    $invoice_data['transaction_charge'] = $transaction_charge;
                    $invoice_data['total'] = $data['proposal_total'] + $transaction_charge;
                    $invoice_data['newitems'] = $item_ids;
                    if ($data['rel_type'] == "lead") {
                        $invoice_data['leadid'] = $data['rel_id'];
                        $invoice_data['leaddate'] = date('Y-m-d', strtotime($data['rel_content']->eventstartdatetime));
                    } else {
                        $invoice_data['project_id'] = $data['rel_id'];
                        $invoice_data['projectdate'] = date('Y-m-d', strtotime($data['rel_content']->eventstartdatetime));
                    }
                    $invoice_data['sale_agent'] = $data['rel_content']->addedfrom;
                    $invoice_data['clientnote'] = "";
                    $invoice_data['terms'] = "";
                    if ($proposaltemplate->othrdiscval < 0) {
                        $invoice_data['discount_percent'] = $discount_percent;
                        $invoice_data['discount_total'] = $discount_amount;
                        $invoice_data['discount_type'] = $proposaltemplate->othrdisctype;


                    }
                    $invoice_id = $this->invoices_model->add($invoice_data);
                    $proposalInvoice = array();
                    $proposalInvoice['invoice_id'] = $invoice_id;
                    $proposalInvoice['proposal_id'] = $id;
                    $this->proposaltemplates_model->addproposalinvoice($proposalInvoice);
                }
                $this->db->where('proposal_id', $data['proposal_id']);
                $this->db->update('tblproposaltemplate_feedback', array('is_invoiced' => 1));
                if ($this->db->affected_rows() > 0) {
                    $final_feedback->is_invoiced = 1;
                }

                if ($final_feedback->is_invoiced == 1) {
                    set_alert('success', 'THANK YOU! Your invoice is now available.');
                } else {
                    set_alert('success', 'THANK YOU! Your invoice will be available when the last client signs the proposal.');
                }
            } elseif ($final_feedback->is_invoiced == 1) {
                $invoiceid = $this->proposaltemplates_model->get_proposal_invoices_id($data['proposal_id']);
                $invoice_data = array();
                $invoice_data['subtotal'] = $data['proposal_subtotal'];
                $transaction_charge = 0;
                $invoice_data['transaction_charge'] = $transaction_charge;
                $invoice_data['total'] = $data['proposal_total'] + $transaction_charge;
                $invoice_status = get_invoice_status($invoiceid);
                if ($invoice_status == 2) {
                    $invoice_data['status'] = 3;
                }
                $updateinvoice = $this->invoices_model->updateinvoice($invoice_data, $invoiceid);
                /*$this->notify_proposal_change($data, $signature['signer_id']);*/
                if ($updateinvoice == true) {
                    $data['token'] = $token;
                    $this->proposaltemplates_model->sentproposalupdatemail($data);
                    set_alert('success', 'Your invoices is now updated.');
                }
            }
            if (($data['total_signer'] - 1) == $data['total_signed']) {
                foreach ($data['signatures'] as $signature) {
                    if ($signature['counter_signer'] == 1) {
                        $data['name'] = $proposaltemplate->name;
                        $this->notify_to_sign($data, $signature['signer_id']);
                    }
                }
            }
            redirect(site_url('proposal/view/' . $token), 'refresh');
            /*}*/
        }
        $data['proposal'] = $proposaltemplate;
        if (isset($proposaltemplate->rel_type) && $proposaltemplate->rel_type == "lead") {
            $data['rel_content'] = $this->leads_model->getlead($proposaltemplate->rel_id);
        } elseif (isset($proposaltemplate->rel_type) && $proposaltemplate->rel_type == "project") {
            $data['rel_content'] = $this->projects_model->getproject($proposaltemplate->rel_id);
        }

        if (isset($data['rel_content']) && !empty($data['rel_content'])) {
            $vanue_id = $data['rel_content']->venueid;
            if ($vanue_id > 0) {
                $data['venue'] = $this->venues_model->getvenue($vanue_id);
            }

        }

        if ($proposaltemplate->rel_type != "" && $proposaltemplate->rel_id > 0) {
            $clients = $this->get_clients($proposaltemplate->rel_type, $proposaltemplate->rel_id);
            $data['clients'] = $clients;
        }

        if (isset($data['proposal']->ps_template) && $data['proposal']->ps_template > 0) {
            $data['proposal']->pmt_sdl_template = $this->get_payment_schedule_template($data['proposal']->ps_template, $print = false);
        }
        $data['rel_id'] = $proposaltemplate->rel_id;
        $data['quotes'] = $this->proposaltemplates_model->getproposal_quotes($id);
        $data['gallery'] = $this->proposaltemplates_model->getproposal_gallery($id);
        $data['files'] = $this->proposaltemplates_model->getproposal_files($id);
        $title = _l('view_proposal');
        $created_by = $proposaltemplate->created_by;
        $data['accounting_assets'] = true;

        $this->load->model('taxes_model');
        //$data['taxes'] = $this->taxes_model->get();
        $this->load->model('invoice_items_model');
        $data['ajaxItems'] = false;
        if (total_rows('tblitems') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get();
        } else {
            $data['items'] = array();
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $data['statuses'] = $this->proposals_model->get_statuses();
        $data['staff'] = $this->staff_model->getstaff($created_by, 1);
        $data['currencies'] = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['agreements'] = $this->agreements_model->getagreements();
        $data['title'] = $title;
        $feedback = $this->proposaltemplates_model->get_proposal_feedback($id);
        if (isset($feedback) && !empty($feedback)) {
            $data['proposal']->feedback = $feedback;
            $data['proposal_selected_items'] = json_decode($data['proposal']->feedback->selected_items, true);
        }
        $data['rec_payment'] = $this->proposaltemplates_model->get_rec_payment($id);
        //$data['tasks'] = $this->tasks_model->get_task_by_rel($proposaltemplate->rel_type, $proposaltemplate->rel_id);
        $data['brandid'] = $proposaltemplate->brandid;
        if (isset($proposalToken)) {
            $data['authclient'] = $proposalToken->client_id;
            $data['authemail'] = $proposalToken->email;
            $data['authtype'] = $proposalToken->usertype;
        } else {
            if (is_staff_logged_in()) {
                $data['authclient'] = get_staff_user_id();
                $data['authemail'] = get_staff_email();
                $data['authtype'] = "member";
            }
        }
        $invoices = $this->proposaltemplates_model->get_proposal_invoices($id);
        $nextinvoices = $this->proposaltemplates_model->get_proposal_invoices($id, 1);
        $paidinvoices = $this->proposaltemplates_model->get_proposal_invoices($id, 2);
        /*echo "<pre>";
        print_r($invoices);
        print_r($nextinvoices);
        print_r($paidinvoices);
        die();*/
        if (count($invoices) > 0) {
            $data['invoices'] = $invoices;
            $data['nextinvoice'] = $invoices[0];
            $data['paidinvoices'] = $paidinvoices;
        }
        if ($preview == "preview") {
            $data['preview'] = $preview;
        }
        if (($this->input->get('pid') && $this->input->get('pid') > 0) || ($this->input->get('lid') && $this->input->get('lid') > 0) || (isset($data['token']) && $data['token'] != "")) {
            $status = $this->proposaltemplates_model->get_proposal_status($id);
            $data['proposal']->status = $status;
        }
        if ($data['proposal']->isclosed == 1) {
            $data['proposal']->status = "closed";
        }
        $data['bullet_url'] = "javascript:void(0)";
        $data['stripePublishableKey'] = get_brand_option('paymentmethod_stripe_api_publishable_key');
        $this->load->view('proposaltemplates/proposal', $data);
    }

    function proposalpdf($id)
    {
        /*if (!has_permission('invoices', '', 'view', true) && !has_permission('invoices', '', 'view_own', true)) {
            access_denied('invoices');
        }*/
        /*if (!$id) {
            if ($this->input->get('lid')) {
                redirect(admin_url('invoices/list_invoices?lid=' . $this->input->get('lid')));
            } else {
                redirect(admin_url('invoices/list_invoices'));
            }
        }*/
        $proposal = $this->proposaltemplates_model->getclientproposal($id);
        $proposal->quotes = $this->proposaltemplates_model->getproposal_quotes($id);
        $proposal->gallery = $this->proposaltemplates_model->getproposal_gallery($id);
        $proposal->files = $this->proposaltemplates_model->getproposal_files($id);
        $proposal->invoices = $this->proposaltemplates_model->get_proposal_invoices($id);
        $proposal_number = format_proposal_number($proposal);

        if (isset($proposal->rel_type) && $proposal->rel_type == "lead") {
            $proposal->rel_content = $this->leads_model->getlead($proposal->rel_id);
        } elseif (isset($proposal->rel_type) && $proposal->rel_type == "project") {
            $proposal->rel_content = $this->projects_model->getproject($proposal->rel_id);
        }

        if (isset($proposal->rel_content) && !empty($proposal->rel_content)) {
            $vanue_id = $proposal->rel_content->venueid;
            if ($vanue_id > 0) {
                $proposal->venue = $this->venues_model->getvenue($vanue_id);
            }

        }

        if ($proposal->rel_type != "" && $proposal->rel_id > 0) {
            $clients = $this->get_clients($proposal->rel_type, $proposal->rel_id);
            $proposal->clients = $clients;
        }

        if (isset($proposal->ps_template) && $proposal->ps_template > 0) {
            $proposal->pmt_sdl_template = $this->get_payment_schedule_template($proposal->ps_template, $print = false);
        }
        $this->load->model('invoice_items_model');
        $data['ajaxItems'] = false;
        if (total_rows('tblitems') <= ajax_on_total_items()) {
            $proposal->items = $this->invoice_items_model->get();
        } else {
            $proposal->items = array();
            $data['ajaxItems'] = true;
        }
        $proposal->items_groups = $this->invoice_items_model->get_groups();
        $proposal->feedback = $feedback = $this->proposaltemplates_model->get_proposal_feedback($id);
        try {
            $pdf = proposal_pdf($proposal);
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
        $pdf->Output(mb_strtoupper(slug_it($proposal_number)) . '.pdf', $type);
    }
}