<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Proposaltemplates extends Admin_controller
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
        $this->load->model('emails_model');
        $this->load->model('addressbooks_model');

        // Model is autoloaded
    }

    /* List all staff proposaltemplates */
    public function index()
    {
        /*if (is_mobile()) {
            $this->session->set_userdata(array(
                'proposals_kanban_view' => 0
            ));
        }*/

        $rel_id = "";
        $rel_type = "";
        if (!has_permission('proposals', '', 'view', true)) {
            access_denied('proposals');
        }
        $data['title'] = _l('proposals');
        if (isset($_GET['lid'])) {
            $data['lname'] = $this->leads_model->get($_GET['lid'])->name;
            $rel_id = $_GET['lid'];
            $rel_type = "lead";
        } elseif (isset($_GET['pid'])) {
            $data['lname'] = $this->projects_model->get($_GET['pid'])->name;
            $data['parent_id'] = $this->projects_model->get($_GET['pid'])->parent;
            $rel_id = $_GET['pid'];
            $rel_type = "project";
        }
        $data['switch_proposals_kanban'] = true;
        if ($this->session->has_userdata('proposals_kanban_view') && $this->session->userdata('proposals_kanban_view') == 'true') {
            $data['switch_proposals_kanban'] = false;
            $data['bodyclass'] = 'kan-ban-body';
        }
        $status = "active";
        /*$this->session->set_userdata(array(
            'proposals_status_view' => 'active'
        ));*/

        if ($this->input->is_ajax_request()) {
            $this->session->set_userdata(array(
                'proposals_status_view' => $this->input->get('status')
            ));
            if ($this->input->get('kanban')) {
                $data['kanban'] = $this->input->get('kanban');
                if ($this->input->get('limit')) {
                    $data['limit'] = $this->input->get('limit');
                }
                if ($this->input->get('page')) {
                    $data['page'] = $this->input->get('page');
                }
                if ($this->input->get('search')) {
                    $data['search'] = $this->input->get('search');
                }
                if ($this->input->get('status')) {
                    $status = $this->input->get('status');
                    $data['status'] = $this->input->get('status');
                }
                $data['totalproposals'] = $this->proposaltemplates_model->get_kanban_proposals($rel_id, $rel_type, $data, $status);
                $data['proposals'] = $this->proposaltemplates_model->get_kanban_proposals($rel_id, $rel_type, $data, $status);
                echo $this->load->view('admin/proposaltemplates/kan-ban', $data, true);
                die();
            } else {
                $this->perfex_base->get_table_data('proposaltemplates');
            }
        }
        $data['proposals_active'] = $this->proposaltemplates_model->get_kanban_proposals($rel_id, $rel_type, $data, 'active');
        $data['proposals_archived'] = $this->proposaltemplates_model->get_kanban_proposals($rel_id, $rel_type, $data, 'archieved');
        $this->load->view('admin/proposaltemplates/manage', $data);
    }

    /* Add new proposaltemplate or edit existing one */
    public function proposal($id = '')
    {

        if (!has_permission('proposals', '', 'view', true)) {
            access_denied('proposals');
        }
        if (isset($_GET['lid'])) {
            $rid = "?lid=" . $_GET['lid'];
        } elseif (isset($_GET['pid'])) {
            $rid = "?pid=" . $_GET['pid'];
        } else {
            $rid = "";
        }

        if ($this->input->post()) {

            $pg = $this->input->post('pg');
            $post_data = $this->input->post();
            /*echo "<pre>";
            print_r($post_data);
            die('<--here');*/
            if (!isset($post_data['markups'])) {
                $post_data['markups'] = 0;
            }
            if (!isset($post_data['discounts'])) {
                $post_data['discounts'] = 0;
            }
            $post_data['signatures'] = array_values($post_data['signatures']);

            if (!empty($this->input->post('relation_type'))) {
                $post_data['rel_type'] = $this->input->post('relation_type');
            }

            if (!empty($this->input->post('relation_id'))) {
                $post_data['rel_id'] = $this->input->post('relation_id');
            }

            if (isset($post_data['issued_date'])) {
                $post_data['issued_date'] = date_create($post_data['issued_date']);
                $post_data['issued_date'] = date_format($post_data['issued_date'], "Y-m-d");
            }
            if (isset($post_data['valid_date'])) {
                $post_data['valid_date'] = date_create($post_data['valid_date']);
                $post_data['valid_date'] = date_format($post_data['valid_date'], "Y-m-d");
            }
            if(isset($post_data['imagebase64'])){
                unset($post_data['imagebase64']);
            }
            if(isset($post_data['bannerbase64'])){
                unset($post_data['bannerbase64']);
            }
            if ($id == '') {
                if (!has_permission('proposals', '', 'create', true)) {
                    access_denied('proposals');
                }
                $id = $this->proposaltemplates_model->addproposaltemplate($post_data);
                if ($id) {
                    handle_proposaltemplate_banner_upload($id);
                    $this->upload_pmedia($this->input->post(), $id);
                    if (isset($post_data['save_as_template'])) {
                        $template = $post_data;
                        $template['is_template'] = 1;
                        $template['rel_type'] = "";
                        $template['rel_id'] = "";
                        $proposalversion = str_pad(get_brand_option('next_proposal_number'), 2, '0', STR_PAD_LEFT);
                        $template['proposal_version'] = $proposalversion;
                        $template['parent_template'] = $id;
                        $templateid = $this->proposaltemplates_model->addproposaltemplate($template);
                        handle_proposaltemplate_banner_upload($templateid);
                        $this->upload_pmedia($this->input->post(), $templateid);
                    }
                    set_alert('success', _l('added_successfully', _l('proposaltemplate')));
                    //redirect(admin_url('proposaltemplates/proposal/' . $id));
                    //Added By Vaidehi on 04/18/2018 for calendar redirection
                    if (isset($pg) && $pg != '') {
                        redirect(admin_url('calendar'));
                    } else {
                        //redirect(admin_url('/proposaltemplates/proposal/' . $id) . $rid);
                        if (isset($post_data['save_and_preview'])) {
                            redirect(site_url('proposal/view/' . $id) . $rid);
                        } else {
                            redirect(admin_url('proposaltemplates/') . $rid);
                            //redirect(admin_url('proposaltemplates') . $rid);
                        }
                    }
                } else {
                    set_alert('danger', _l('problem_proposaltemplate_adding', _l('proposaltemplate_lowercase')));
                    redirect(admin_url('proposaltemplates/proposal/' . $id . $rid));
                }

            } else {
                if (!has_permission('proposals', '', 'edit', true)) {
                    access_denied('proposals');
                }
                $this->upload_pmedia($this->input->post(), $id);
                $success = $this->proposaltemplates_model->updateproposaltemplate($post_data, $id);
                handle_proposaltemplate_banner_upload($id);
                if ($success) {

                    if (isset($post_data['save_as_template'])) {
                        $template = $post_data;
                        $template['is_template'] = 1;
                        $template['rel_type'] = "";
                        $template['rel_id'] = "";
                        $proposalversion = str_pad(get_brand_option('next_proposal_number'), 2, '0', STR_PAD_LEFT);
                        $template['proposal_version'] = $proposalversion;
                        $template['parent_template'] = $id;
                        $template['ps_template'] = 0;
                        $template['signatures'] = array();
                        $template['payment_schedule'] = array();
                        $templateid = $this->proposaltemplates_model->addproposaltemplate($template);
                        handle_proposaltemplate_banner_upload($templateid);
                        $this->upload_pmedia($template, $templateid);
                    }

                    if (isset($gresult) && $gresult === "ext") {
                        set_alert('danger', 'Image extension not allowed.');
                        //redirect(admin_url('proposaltemplates/proposal/' . $id));
                    } elseif (isset($gresult) && $gresult === "size") {
                        set_alert('danger', 'Image size exceded to the upload limit.');
                        redirect(admin_url('proposaltemplates/proposal/' . $id));
                    } else {
                        set_alert('success', _l('updated_successfully', _l('proposaltemplate')));

                        //Added By Vaidehi on 04/18/2018 for calendar redirection
                        if (isset($pg) && $pg != '') {
                            redirect(admin_url('calendar'));
                        } else {
                            if (isset($post_data['save_and_preview'])) {

                                redirect(site_url('proposal/view/' . $id) . $rid);
                            } else {
                                //redirect(admin_url('proposaltemplates') . $rid);
                                redirect(admin_url('proposaltemplates/') . $rid);
                            }
                        }
                    }
                } else {
                    set_alert('danger', _l('problem_proposaltemplate_updating', _l('proposaltemplate_lowercase')));
                    redirect(admin_url('proposaltemplates/proposal/' . $id . $rid));
                }
                redirect(admin_url('proposaltemplates/proposal/' . $id . $rid));
            }
        }
        $created_by = get_staff_user_id();
        if ($id == '') {
            if (!has_permission('proposals', '', 'create', true)) {
                access_denied('proposals');
            }

            $title = _l('new_proposaltemplate');
        } else {
            if (!has_permission('proposals', '', 'edit', true)) {
                access_denied('proposals');
            }

            $proposaltemplate = $this->proposaltemplates_model->getproposaltemplates($id);
            $data['proposal'] = $proposaltemplate;
            if (isset($data['proposal']->ps_template) && $data['proposal']->ps_template > 0) {
                $data['proposal']->pmt_sdl_template = $this->get_payment_schedule_template($data['proposal']->ps_template, $print = false);
            }
            $data['quotes'] = $this->proposaltemplates_model->getproposal_quotes($id);
            $data['gallery'] = $this->proposaltemplates_model->getproposal_gallery($id);
            $data['files'] = $this->proposaltemplates_model->getproposal_files($id);
            $title = _l('edit_proposaltemplate');
            $created_by = $proposaltemplate->created_by;
            $data['rec_payment'] = $this->proposaltemplates_model->get_rec_payment($id);
        }
        if (isset($_GET['preview']) && $_GET['preview'] == true) {
            $title = _l('preview_proposaltemplate');
        }
        $data['accounting_assets'] = true;

        $this->load->model('taxes_model');
        $this->load->model('invoice_items_model');
        $data['taxes'] = $this->taxes_model->get();
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
        $data['staff'] = $this->staff_model->get($created_by, 1);
        $data['currencies'] = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['agreements'] = $this->agreements_model->getagreements();
        $data['paymentschedules'] = $this->paymentschedules_model->getpaymentschedules('', 1);
        $data['title'] = $title;
        if (isset($_GET['pid'])) {
            $data['parent_id'] = $this->projects_model->get($_GET['pid'])->parent;
        }
        $data['addressbooks'] = $this->addressbooks_model->get_global_adddress();
        if (isset($_GET['lid']) || isset($_GET['pid'])) {
            if (isset($_GET['lid'])) {
                $id = $_GET['lid'];
                $clients = $this->get_clients('lead', $id);
                $data['rel_content'] = $this->leads_model->get($id);
                $data['addressbooks'] = $this->addressbooks_model->get_existing_contacts('tblleadcontact', "leadid", $this->input->get('lid'));
            } else {
                $id = $_GET['pid'];
                $clients = $this->get_clients('project', $id);
                $data['rel_content'] = $this->projects_model->get($id);
                $data['addressbooks'] = $this->addressbooks_model->get_existing_contacts('tblprojectcontact', "projectid", $this->input->get('pid'));
            }

        } elseif (isset($proposaltemplate->rel_type) && $proposaltemplate->rel_id > 0) {
            $clients = $this->get_clients($proposaltemplate->rel_type, $proposaltemplate->rel_id);
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
        $data['proposal_templates'] = $this->proposaltemplates_model->getproposaltemplates();
        $pg = $this->input->get('pg');
        $data['pg'] = $pg;
        if (isset($_GET['lid']) || isset($_GET['pid'])) {
            if (isset($_GET['lid'])) {
                $rel_type = "lead";
                $rel_id = $_GET['lid'];
            } else {
                $rel_type = "project";
                $rel_id = $_GET['pid'];
            }
        }
        if (isset($proposaltemplate->rel_type) && $proposaltemplate->rel_id > 0) {
            $rel_type = $proposaltemplate->rel_type;
            $rel_id = $proposaltemplate->rel_id;
        }
        if (isset($rel_type) && $rel_id > 0) {
            $data['tasks'] = $this->tasks_model->get_task_by_rel($rel_type, $rel_id);
        }
        $this->load->model('taxes_model');
        if (isset($rel_type) && !empty($rel_type)) {
            $data['rel_type'] = $rel_type;
            $data['rel_id'] = $rel_id;
        }
        $data['taxes'] = $this->taxes_model->get();
        $data['tags'] = $this->tags_model->get();
        $data['product_service_groups'] = $this->invoice_items_model->get_line_item_category_list();
        $data['income_category_list'] = $this->invoice_items_model->get_income_categories();
        $data['expense_category_list'] = $this->invoice_items_model->get_expense_categories();
        $this->load->view('admin/proposaltemplates/proposal', $data);
    }

    /* Delete staff proposaltemplate from database */
    public function deleteproposaltemplates($id)
    {
        if (!has_permission('proposals', '', 'delete', true)) {
            access_denied('proposals');
        }
        if (!$id) {
            redirect(admin_url('proposaltemplates'));
        }
        $response = $this->proposaltemplates_model->deleteproposaltemplate($id);
        if ($response == true) {
            set_alert('success', _l('Proposal Template deleted successfully.'));
        } else {
            set_alert('danger', _l('problem_deleting', _l('proposaltemplate_lowercase')));
        }
        //redirect(admin_url('proposaltemplates'));
    }

    /* Remove addressbook profile image / ajax */
    public function remove_proposaltemplate_banner($id = '')
    {
        if (is_numeric($id) && (has_permission('proposals', '', 'create', true) || has_permission('proposals', '', 'edit', true))) {
            $templateid = $id;
        } else {
            $templateid = "";
        }
        if (file_exists(get_upload_path_by_type('proposaltemplate') . $templateid)) {
            delete_dir(get_upload_path_by_type('proposaltemplate') . $templateid);
        }
        $this->db->where('templateid', $templateid);
        $this->db->update('tblproposaltemplates', array(
            'banner' => null
        ));
        if ($this->input->is_ajax_request()) {
            return true;
        }
        redirect(admin_url('proposaltemplates/proposal/' . $templateid));

    }

    function quote_group()
    {

        $data = $this->input->post();
        $data['packages'] = $this->invoice_items_model->get_groups();
        $data['items'] = $this->invoice_items_model->get();
        return $this->load->view('admin/proposaltemplates/quote_group', $data);
    }

    function upload_pmedia($post_data, $id)
    {
        $quote_gallery_image = $post_data['image'];
        if (isset($_FILES['pimage']['name']) && $_FILES['pimage']['name'] != '') {

            $file_name = explode('.', $_FILES['pimage']['name']);
            $proposal_gallery['title'] = $quote_gallery_image['pimage_title'] != "" ? $quote_gallery_image['pimage_title'] : $file_name[0];
            $proposal_gallery['caption'] = $quote_gallery_image['pimage_caption'] != "" ? $quote_gallery_image['pimage_caption'] : $file_name[0];
            $proposal_gallery['type'] = $quote_gallery_image['pimage_type'];
            $proposal_gallery['proposal_id'] = $id;
            $gresult = handle_proposaltemplate_gallery_upload($id, $proposal_gallery);
            if(isset($_POST['imagebase64'])){
                $data = $_POST['imagebase64'];
                list($type, $data) = explode(';', $data);
                list(, $data)      = explode(',', $data);
                $data = base64_decode($data);
                $path = get_upload_path_by_type('proposalgallery') . $id . '/';
                _maybe_create_upload_path($path);
                $filename =$_FILES['pimage']['name'];
                $filename = unique_filename($path, $filename);
                $path .= 'round_'.$filename;
                file_put_contents($path, $data);
            }

        }
        if (isset($_FILES['pfile']['name']) && $_FILES['pfile']['name'] != '') {
            $file_name = explode('.', $_FILES['pfile']['name']);
            $proposal_gallery['title'] = $file_name[0];
            $proposal_gallery['caption'] = $file_name[0];
            $proposal_gallery['type'] = 'file';
            $proposal_gallery['proposal_id'] = $id;
            $gresult = handle_proposaltemplate_gallery_upload($id, $proposal_gallery);
        }
        if (isset($post_data['pvideo']['youtube']) && $post_data['pvideo']['youtube'] != "") {

            $proposal_gallery['title'] = "";
            $proposal_gallery['caption'] = "";
            $proposal_gallery['type'] = 'gallery';
            $proposal_gallery['proposal_id'] = $id;
            $proposal_gallery['name'] = $post_data['pvideo']['youtube'];
            $this->proposaltemplates_model->addproposalgalvideo($proposal_gallery);
        }
        if (isset($post_data['pvideo']['vimeo']) && $post_data['pvideo']['vimeo'] != "") {
            $proposal_gallery['title'] = "";
            $proposal_gallery['caption'] = "";
            $proposal_gallery['type'] = 'gallery';
            $proposal_gallery['proposal_id'] = $id;
            $proposal_gallery['name'] = $post_data['pvideo']['vimeo'];
            $this->proposaltemplates_model->addproposalgalvideo($proposal_gallery);
        }
    }

    function get_item_for_quote()
    {
        $data = $this->input->post();

        $id = $data['itemid'];
        $type = $data['item_type'];
        if (strtolower($type) == 'package') {
            $item = $this->invoice_items_model->get_group($id);
        } else {
            $item = $this->invoice_items_model->get($id);
        }

        $data['item'] = $item;
        return $this->load->view('admin/proposaltemplates/quote_item', $data);
    }

    function quote_group_name_exist()
    {
        $data = $this->input->post();
        $response = $this->proposaltemplates_model->get_quote_group($data);
        echo $response;
    }

    function get_agreement_for_prposal()
    {

        $data = $this->input->post();
        $id = $data['tempid'];
        $agreement = $this->agreements_model->getagreements($id);
        $content = $agreement->content;
        echo $content;
        die;
    }

    function delete_file_image($id)
    {
        $response = $this->proposaltemplates_model->delete_file_image($id);
        echo $response;
    }

    function delete_quote_group($id)
    {
        $response = $this->proposaltemplates_model->delete_quote_group($id);
        if ($response) {
            return true;
        }
        return false;
    }

    function add_signer()
    {
        $data = $this->input->post();
        return $this->load->view('admin/proposaltemplates/single_signature', $data);
    }

    function get_payment_schedule_template($id = "", $print = 'true')
    {
        if ($id > 0) {
            $paymentschedule = $this->paymentschedules_model->getpaymentschedules($id);
            /*echo "<pre>";
            print_r($paymentschedule);
            die();*/
            if (isset($paymentschedule) && !empty($paymentschedule)) {
                if ($this->input->is_ajax_request()) {
                    //$paymentschedule->name = "";
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
            return $this->load->view('admin/proposaltemplates/paymentschedule_temp', $data);
        } else {
            return $data;
        }
    }

    function copy_quote_group($id)
    {
        $qgroup = (array)$this->proposaltemplates_model->getproposal_quote_by_id($id);
        $name = $this->proposaltemplates_model->unique_quote_group_name($qgroup['quote_name'], $qgroup['proposal_id']);
        unset($qgroup['qid']);
        $qgroup['quote_name'] = $name;
        $quotes = $this->proposaltemplates_model->getproposal_quotes($qgroup['proposal_id']);
        $items_groups = $this->invoice_items_model->get_groups();
        $items = $this->invoice_items_model->get();
        if (isset($quotes) && count($quotes) > 0) {
            foreach ($quotes as $gid => $quote) {
                $quote_items = json_decode($quote['quote_items'], true);
                foreach ($quote_items as $quote_item) {
                    $si[] = strtolower($quote_item['type']) . "_" . $quote_item['id'];
                }
            }
        }
        $this->db->insert('tblproposal_quotes', $qgroup);
        $quote_id = $this->db->insert_id();
        $qgroup['qid'] = $quote_id;
        $qgroup['gid'] = count($quotes);
        $data['packages'] = $items_groups;
        $data['items'] = $items;
        $data['selected_items'] = $si;
        $data['quote'] = $qgroup;
        return $this->load->view('admin/proposaltemplates/quote_group', $data);
    }

    function add_payment()
    {
        $data = $this->input->post();
        return $this->load->view('admin/proposaltemplates/payment', $data);
    }

    /* Prpoposal duplicate*/
    function duplicate_proposal()
    {
        $data = $this->input->post();
        $success = $this->proposaltemplates_model->duplicate_proposal($data);
        if ($success) {
            $media_types = array('gallery', 'files', 'banner');
            foreach ($media_types as $media_type) {
                if ($media_type == 'gallery') {
                    $type = "proposalgallery";
                } elseif ($media_type == 'files') {
                    $type = "proposalfiles";
                } else {
                    $type = "proposaltemplate";
                }
                $mydir = get_upload_path_by_type($type) . "/" . $success . "/";
                if (!is_dir($mydir)) {
                    mkdir($mydir);
                }
                $path = get_upload_path_by_type($type) . $data['duplicate_record_id'] . '/*.*';
                $files = glob($path);
                foreach ($files as $file) {
                    $file_to_go = str_replace("/" . $data['duplicate_record_id'] . "/", "/" . $success . "/", $file);
                    copy($file, $file_to_go);
                }
            }

            set_alert('success', _l('added_successfully', _l('proposaltemplate')));
            redirect(admin_url('proposaltemplates'));
        } else {
            set_alert('danger', _l('problem_proposaltemplate_adding', _l('proposaltemplate_lowercase')));
        }

        redirect(admin_url('proposaltemplates'), 'refresh');
    }

    function preview()
    {
        $data = $this->input->post();
        $quotes = array();
        foreach ($data['group'] as $quote) {
            $quote['qid'] = $quote['gid'];
            unset($quote['gid']);
            $quote['quote_name'] = $quote['gname'];
            unset($quote['gname']);
            $quote['quote_type'] = $quote['gtype'];
            unset($quote['gtype']);
            $quote['quote_items'] = json_encode($quote['item']);
            unset($quote['item']);
            $quote['proposal_id'] = "";
            $quotes[] = $quote;
        }
        $data['quotes'] = $quotes;
        unset($data['group']);
        $data['proposal'] = (object)array('templateid' => $data['proposaltemplateid'], 'name' => $data['name'], 'banner' => $data['banner'], 'content' => $data['content'], 'proposal_subtotal' => $data['proposal_subtotal'], 'proposal_total' => $data['proposal_total'], 'removed_sections' => json_encode($data['remove_sec']), 'ps_template' => $data['ps_template'], 'agreement' => $data['agreement'], 'client_message' => $data['client_message'], 'content' => $data['content'], 'signatures' => json_encode($data['signatures']), 'content' => $data['content']);
        unset($data['remove_sec']);
        $data['accounting_assets'] = true;

        $this->load->model('taxes_model');
        $this->load->model('invoice_items_model');
        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('invoice_items_model');
        $data['ajaxItems'] = false;
        if (total_rows('tblitems') <= ajax_on_total_items()) {
            $data['items'] = $this->invoice_items_model->get();
        } else {
            $data['items'] = array();
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();
        $created_by = get_staff_user_id();
        $data['statuses'] = $this->proposals_model->get_statuses();
        $data['staff'] = $this->staff_model->get($created_by, 1);
        $data['currencies'] = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['agreements'] = $this->agreements_model->getagreements();
        $data['paymentschedules'] = $this->paymentschedules_model->getpaymentschedules();
        return $this->load->view('admin/proposaltemplates/proposal_preview', $data);
    }

    /* For finalize Proposal*/


    function viewproposal($id)
    {
        if (!has_permission('proposals', '', 'view', true)) {
            access_denied('proposals');
        }
        $selected_items = array();
        $proposaltemplate = $this->proposaltemplates_model->getproposaltemplates($id);

        if ($this->input->post()) {

            $data = $this->input->post();

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
            foreach ($data['signatures'] as $signature) {
                if (isset($signature['image']) && !empty($signature['image'])) {
                    $total_signed++;
                }
            }
            $data['total_signed'] = $total_signed;
            if ($data['total_signer'] == $data['total_signed']) {
                $data['is_final'] = 1;
            }
            $feedback = $this->proposaltemplates_model->add_proposal_feedback($data);

            if ($proposaltemplate->status != "accepted") {
                $this->updatestatus('accepted', $id, true);
            }
            if ($feedback) {
                $final_feedback = $this->proposaltemplates_model->get_proposal_feedback($id);

                if ($final_feedback->is_invoiced == 0) {

                    $brandid = get_user_session();
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
                        $this->db->where('isvendor', 0);
                        $this->db->where('iscollaborator', 0);
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
                        } else {
                            $package = $this->invoice_items_model->get_group($selected_item['id']);
                            $item_ids[$key]['description'] = $package->name;
                            $item_ids[$key]['qty'] = $selected_item['qty'];
                            $item_ids[$key]['rate'] = $package->group_price;
                        }
                    }

                    if ((isset($data['ps_template']) && $data['ps_template'] > 0) || isset($rec_payment)) {
                        if (isset($data['ps_template']) && isset($data['payment_schedule']) && count($data['payment_schedule']) > 0) {
                            $totalpayments = count($data['payment_schedule']);
                            $remaining_amount = $data['proposal_total'];
                            foreach ($data['payment_schedule'] as $payment_schedule) {

                                $invoice_data = array();
                                $invoice_data['clientid'] = $contacts->contactid;
                                $invoice_data['number'] = get_option('next_invoice_number');
                                $invoice_data['date'] = date('m/d/Y');
                                $invoice_data['allowed_payment_modes'] = Array(4, 'stripe', 'paypal');
                                $invoice_data['duedate'] = $payment_schedule['duedate_date'];
                                if (isset($payment_schedule['price_type']) && $payment_schedule['price_type'] == 'fixed_amount') {
                                    $amount = $payment_schedule['price_amount'];
                                    $remaining_amount = $remaining_amount - $amount;
                                } elseif (isset($payment_schedule['price_type']) && $payment_schedule['price_type'] == 'percentage') {
                                    $amount = ($payment_schedule['price_percentage'] * $remaining_amount) / 100;
                                    $remaining_amount = $remaining_amount - $amount;
                                } else {
                                    $amount = $remaining_amount / $totalpayments;
                                    $remaining_amount = $remaining_amount - $amount;
                                }
                                $invoice_data['subtotal'] = $amount;
                                //$transaction_charge = ($amount * 3) / 100;
                                $transaction_charge = 0;
                                $invoice_data['transaction_charge'] = $transaction_charge;
                                $invoice_data['total'] = $amount + $transaction_charge;
                                $invoice_data['newitems'] = $item_ids;
                                if ($data['rel_type'] == "lead") {
                                    $invoice_data['leadid'] = $data['rel_id'];
                                } else {
                                    $invoice_data['project_id'] = $data['rel_id'];
                                }
                                $invoice_data['sale_agent'] = $data['rel_content']->addedfrom;
                                $invoice_data['clientnote'] = "";
                                $invoice_data['terms'] = "";
                                $this->invoices_model->add($invoice_data);
                                $totalpayments--;
                            }
                        } else {
                            $next_invoice_number = get_option('next_invoice_number');
                            $amount = $data['proposal_total'] / $rec_payment['rec_no'];
                            //$transaction_charge = ($amount * 3) / 100;
                            //$amount = $data['proposal_total'];
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
                        }
                        $CI = get_instance();
                        $CI->db->where('proposal_id', $data['proposal_id']);
                        $CI->db->update('tblproposaltemplate_feedback', array('is_invoiced' => 1));
                    }
                }
                /*if (($data['total_signer'] - 1) == $data['total_signed']) {
                    foreach ($data['signatures'] as $signature) {
                        if ($signature['counter_signer'] == 1) {
                            $data['name'] = $proposaltemplate->name;
                            $this->notify_to_sign($data, $signature['signer_id']);
                        }
                    }
                }*/
                /*if (!empty($file_ary)) {
                    foreach ($file_ary as $signature) {
                        upload_signature($signature, $id);
                    }
                }*/
                redirect(admin_url('proposaltemplates/view/' . $id), 'refresh');
            }
        }

        //$proposaltemplate                     = $this->proposaltemplates_model->getproposaltemplates($id);
        $data['proposal'] = $proposaltemplate;
        if (isset($proposaltemplate->rel_type) && $proposaltemplate->rel_type == "lead") {
            $data['rel_content'] = $this->leads_model->get($proposaltemplate->rel_id);
        } elseif (isset($proposaltemplate->rel_type) && $proposaltemplate->rel_type == "project") {
            $data['rel_content'] = $this->projects_model->get($proposaltemplate->rel_id);
        }
        if (isset($data['rel_content']) && !empty($data['rel_content'])) {
            $vanue_id = $data['rel_content']->venueid;
            if ($vanue_id > 0) {
                $data['venue'] = $this->venues_model->get($vanue_id);
            }

        }
        if ($proposaltemplate->rel_type != "" && $proposaltemplate->rel_id > 0) {
            $clients = $this->get_clients($proposaltemplate->rel_type, $proposaltemplate->rel_id);
            $data['clients'] = $clients;
        }
        if (isset($data['proposal']->ps_template) && $data['proposal']->ps_template > 0) {
            $data['proposal']->pmt_sdl_template = $this->get_payment_schedule_template($data['proposal']->ps_template, $print = false);
        }
        $data['quotes'] = $this->proposaltemplates_model->getproposal_quotes($id);
        $data['gallery'] = $this->proposaltemplates_model->getproposal_gallery($id);
        $data['files'] = $this->proposaltemplates_model->getproposal_files($id);
        $title = _l('view_proposal') . " - " . $proposaltemplate->name;
        $created_by = $proposaltemplate->created_by;
        $data['accounting_assets'] = true;

        $this->load->model('taxes_model');
        $this->load->model('invoice_items_model');
        $data['taxes'] = $this->taxes_model->get();
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
        $data['staff'] = $this->staff_model->get($created_by, 1);
        $data['currencies'] = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['agreements'] = $this->agreements_model->getagreements();
        $data['paymentschedules'] = $this->paymentschedules_model->getpaymentschedules();
        $data['title'] = $title;
        $feedback = $this->proposaltemplates_model->get_proposal_feedback($id);
        if (isset($feedback) && !empty($feedback)) {
            $data['proposal']->feedback = $feedback;
            $data['proposal_selected_items'] = json_decode($data['proposal']->feedback->selected_items, true);
        }
        $data['rec_payment'] = $this->proposaltemplates_model->get_rec_payment($id);
        $data['tasks'] = $this->tasks_model->get_task_by_rel($proposaltemplate->rel_type, $proposaltemplate->rel_id);
        $this->load->view('admin/proposaltemplates/viewproposal/viewproposal', $data);
    }

    function use_template()
    {
        $data = $this->input->post();
        $rel_type = "";
        $rel_id = "";
        $is_template = 1;
        if ($this->input->get('pid')) {
            $rel_type = "project";
            $rel_id = $this->input->get('pid');
            $is_template = 0;
        } elseif ($this->input->get('pid')) {
            $rel_type = "lead";
            $rel_id = $this->input->get('lid');
            $is_template = 0;
        }
        if ($data['current_template'] > 0) {
        } else {
            $pdata = array();
            $pdata['duplicate_record_id'] = $data['template_id'];
            $pdata['duplicate_by_brand'] = "current_brand";
            $pdata['brandid'] = get_user_session();
            $pdata['rel_type'] = $rel_type;
            $pdata['rel_id'] = $rel_id;
            $pdata['is_template'] = $is_template;
            $success = $this->proposaltemplates_model->duplicate_proposal($pdata);
            if ($success) {
                $CI =& get_instance();
                $final['parent_template'] = $data['template_id'];
                $final['is_template'] = 0;
                $CI->db->where('templateid', $success);
                $CI->db->update('tblproposaltemplates', $final);
                $media_types = array('gallery', 'files', 'banner');
                foreach ($media_types as $media_type) {
                    if ($media_type == 'gallery') {
                        $type = "proposalgallery";
                    } elseif ($media_type == 'files') {
                        $type = "proposalfiles";
                    } else {
                        $type = "proposaltemplate";
                    }
                    $mydir = get_upload_path_by_type($type) . "/" . $success . "/";
                    if (!is_dir($mydir)) {
                        mkdir($mydir);
                    }
                    $path = get_upload_path_by_type($type) . $pdata['duplicate_record_id'] . '/*.*';
                    $files = glob($path);
                    foreach ($files as $file) {
                        $file_to_go = str_replace("/" . $pdata['duplicate_record_id'] . "/", "/" . $success . "/", $file);
                        copy($file, $file_to_go);
                    }
                }
                set_alert('success', _l('added_successfully', _l('proposaltemplate')));
            } else {
                set_alert('danger', _l('problem_proposaltemplate_adding', _l('proposaltemplate_lowercase')));
            }

            echo $success;
            die();
        }
    }

    function rec_payment()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if ($data['rec_end_date'] == "") {
                if ($data['rec_bill_type'] == "weekly") {
                    $total_days = ($data['rec_no'] * $data['rec_no_of_week_mnth'] * 7) - 1;
                    $enddate = date('m/d/Y', strtotime("+" . $total_days . " day", strtotime($data['rec_start_date'])));
                } else {
                    $months = $data['rec_no'] * $data['rec_no_of_week_mnth'];
                    $enddate = days_in_months($data['rec_start_date'], $months);
                    //$total_days = ($data['rec_no']*$data['rec_no_of_week_mnth']*30)-1;
                }

                $data['rec_end_date'] = $enddate;
            } else {
                $startDate = new DateTime($data['rec_start_date']);
                $endDate = new DateTime($data['rec_end_date']);
                $interval = $startDate->diff($endDate);
                if ($data['rec_bill_type'] == "weekly") {
                    $diff = (($interval->days + 1) / 7);
                    $diff = ceil($diff);
                    $rec_no = $diff / $data['rec_no_of_week_mnth'];
                    $rec_no = ceil($rec_no);
                } else {
                    $months = (($interval->days + 1) / 30);
                    $months = ceil($months);
                    $rec_no = $months / $data['rec_no_of_week_mnth'];
                    $rec_no = ceil($rec_no);
                }
                $data['rec_no'] = $rec_no;
            }
            return $this->load->view('admin/proposaltemplates/rec_payment_temp', $data);
            die();
        }
    }

    function notify_to_sign($data, $id, $integration = false)
    {
        $notification_data = array(
            'description' => ($integration == false) ? 'not_to_signed_proposal' : 'not_to_signed_proposal',
            'touserid' => $id,
            'eid' => $data['proposal_id'],
            'brandid' => get_user_session(),
            'not_type' => 'proposals',
            'link' => 'proposaltemplates/viewproposal/' . $data['proposal_id'],
            'additional_data' => ($integration == false ? serialize(array(
                $data['name']
            )) : serialize(array()))
        );
        if (add_notification($notification_data)) {
            pusher_trigger_notification(array($id));
        }
    }

    function get_clients($type, $id)
    {

        return $this->proposaltemplates_model->get_clients($type, $id);

    }

    /**
     * Added By : Masud
     * Dt : 06/11/2018
     * kanban view for meeting
     */
    public function switch_proposals_kanban($set = 0)
    {
        if ($set == 1) {
            $set = 'true';
        } else {
            $set = 'false';
        }

        $this->session->set_userdata(array(
            'proposals_kanban_view' => $set
        ));

        redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * Added By: Masud
     * Dt: 07/12/2018
     * for pinned Proposal
     */
    public
    function pinproposal()
    {
        $proposal_id = $this->input->post('proposal_id');

        $pindata = $this->proposaltemplates_model->pinproposal($proposal_id);

        echo $pindata;
        exit;
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


        if ($this->input->is_ajax_request()) {
            $reason = $this->input->post('reason');
            $this->proposaltemplates_model->addreason($id, $status, $reason);
            die;
        } else {
            if ($this->input->post('reason')) {
                $reason = $this->input->post('reason');
                $this->proposaltemplates_model->addreason($id, $status, $reason);
            } else {
                $pindata = $this->proposaltemplates_model->updatestatus($id, $status);
            }
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
        return false;
    }

    function quote_selecteditems()
    {
        $selected_items = $this->input->post();
        echo $this->load->view('admin/proposaltemplates/viewproposal/selected_services', array('selected_items' => $selected_items), true);
        die();
    }

    function item()
    {
        $data['item'] = $this->invoice_items_model->get($this->input->post('itemid'));
        $data['key'] = $this->input->post('key');
        echo $this->load->view('admin/proposaltemplates/lineitem', $data, true);
        die();
    }

    function sentproposal($id)
    {
        $this->proposaltemplates_model->sentproposal($id);
        set_alert('success', 'Proposal sent successfully');
        if ($this->input->get('pid') && $this->input->get('pid') > 0) {
            redirect(site_url('proposal/view/' . $id . "?pid=" . $this->input->get('pid')));
        } elseif ($this->input->get('lid') && $this->input->get('lid') > 0) {
            redirect(site_url('proposal/view/' . $id . "?lid=" . $this->input->get('lid')));
        } else {
            redirect(site_url('proposal/view/' . $id));
        }
    }

    function proposalnameexist()
    {
        $data = $this->input->post();
        if ($data['id'] == "") {
            $data['id'] = 0;
        }
        if ($data['id'] == 0) {
            $response = $this->proposaltemplates_model->check_proposal_name_exists($data['name'], $data['id']);
        } else {
            $response = array();
        }
        /*if (count($response) > 0) {
            echo 1;
        } else {
            echo 0;
        }*/
        echo 0;
        die();
    }

    function close()
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
            $this->proposaltemplates_model->close($data);
        }
        set_alert('success', _l('closed_successfully', _l('proposaltemplate')));
        redirect(admin_url('proposaltemplates' . $rel_link));
    }

    function reopen()
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
            $this->proposaltemplates_model->reopen($data);
        }
        set_alert('success', _l('reopened_successfully', _l('proposaltemplate')));
        redirect(admin_url('proposaltemplates' . $rel_link));
    }
}


