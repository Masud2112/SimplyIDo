<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Proposaltemplates_model extends CRM_Model
{
    private $perm_statements = array('view', 'view_own', 'edit', 'create', 'delete');

    public function __construct()
    {
        parent::__construct();
        $this->load->model('emails_model');
    }

    /**
     * Add new employee proposaltemplate
     * @param mixed $data
     */
    public function addproposaltemplate($data)
    {
        /*echo "<pre>";
        print_r($data);
        die();*/
        unset($data['pg']);
        unset($data['relation_type']);
        unset($data['relation_id']);

        $save_and_send = "";
        if (isset($data['group'])) {
            $quote_groups = $data['group'];
        }
        if (isset($data['save_and_send'])) {
            $save_and_send = "save_send";
        }
        $data['is_template'] = 1;
        if (isset($data['rel_id']) && $data['rel_id'] > 0) {
            $data['is_template'] = 0;
            /*if (isset($data['save_as_template'])) {
                $data['is_template'] = 1;
            }*/
        }
        /*if (isset($data['save_as_template'])) {
            $save_as_template = $data['save_as_template'];
        }*/
        $removed_sec = isset($data['remove_sec']) ? json_encode($data['remove_sec'], true) : "";
        $rec_payment = isset($data['rec_payment']) ? $data['rec_payment'] : "";
        $data['removed_sections'] = $removed_sec;
        if (isset($data['signatures']) && !empty(isset($data['signatures']))) {

            $data['signatures'] = json_encode($data['signatures'], true);
        }
        //$quote_gallery_image = $data['image'];
        if (isset($data['pschedulename']) && !empty(isset($data['pschedulename']))) {
            $payment_schedule['name'] = $data['pschedulename'];
            $payment_schedule['payment_schedule'] = $data['payment_schedule'];
            $payment_schedule['is_template'] = 0;
            if (isset($data['is_ps_template'])) {
                $payment_schedule['is_template'] = 1;
            }
        }

        $proposaltemplates = array();
        /*if (!isset($data['save_as_template'])) {
            $proposaltemplates = $this->proposaltemplates_model->check_proposal_name_exists($data['name'], '');
        }*/
        if (empty($proposaltemplates) || $proposaltemplates->templateid <= 0) {
            $data['content'] = $data['introduction'];
            $data['brandid'] = get_user_session();
            $data['created_by'] = $this->session->userdata['staff_user_id'];
            $data['datecreated'] = date('Y-m-d H:i:s');
            //unset($data['content']);
            $items = array();
            if (isset($data['newitems'])) {
                $items = $data['newitems'];
                unset($data['newitems']);
            }

            $unsetters = array(
                'item_group_select', 'item_select', 'item_id', 'quantity', 'rate', 'subtotal', 'discount_percent', 'discount_total', 'adjustment', 'total', 'proposaltemplateid', 'description', 'introduction', 'group', 'product_package', 'group_name', 'group_type', 'pimage_title', 'pimage_caption', 'ps_pkg_search', 'image', 'group_id', 'pvideo', 'gid', 'signer', 'pschedulename', 'is_ps_template', 'payment_schedule', 'remove_sec', 'save_and_send', 'rec_payment', 'save_as_template', 'page_name', 'save_and_preview');

            foreach ($unsetters as $unseter) {
                if (isset($data[$unseter])) {
                    unset($data[$unseter]);
                }
            }
            unset($data['item_id']);
            unset($data['subtotal']);
            $data['sections'] = json_encode($data['sections']);
            $data['number_format'] = get_brand_option('invoice_number_format');
            $data['status'] = "draft";
            if (!isset($data['proposal_version'])) {
                $data['proposal_version'] = get_brand_option('next_proposal_number');
            }

            $this->db->insert('tblproposaltemplates', $data);
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                $this->db->where('name', 'next_proposal_number');
                $this->db->where('brandid', get_user_session());
                $this->db->set('value', 'value+1', false);
                $this->db->update('tblbrandsettings');
                /*if (isset($data['rel_id']) && $data['rel_id'] > 0) {
                    $proposalrelation = array();
                    $proposalrelation['proposal_id'] = $insert_id;
                    $proposalrelation['rel_type'] = $data['rel_type'];
                    $proposalrelation['rel_id'] = $data['rel_id'];
                    $proposalrelation['status'] = "draft";
                    $proposalrelation['comment'] = "";
                    $this->db->insert('tblproposalrelation', $proposalrelation);
                }*/

                if (isset($payment_schedule) && !empty($payment_schedule)) {
                    $this->load->model('paymentschedules_model');
                    /*$payment_schedule['name'] = $this->check_payment_schedule_name($payment_schedule['name']);*/
                    $ps_template_id = $this->paymentschedules_model->addpaymentschedule($payment_schedule);
                    if ($ps_template_id > 0) {
                        $temp['ps_template'] = $ps_template_id;
                        $this->db->where('templateid', $insert_id);
                        $this->db->update('tblproposaltemplates', $temp);
                    }
                }
                if (isset($rec_payment) && !empty($rec_payment)) {
                    $rec_payment['rel_id'] = $insert_id;
                    $rec_payment['rec_start_date'] = date('Y-m-d', strtotime($rec_payment['rec_start_date']));
                    $rec_payment['rec_end_date'] = date('Y-m-d', strtotime($rec_payment['rec_end_date']));
                    $this->add_rec_payment($rec_payment);
                }
                if (isset($quote_groups) && !empty($quote_groups)) {
                    foreach ($quote_groups as $group) {
                        $group_data['quote_name'] = $group['gname'];
                        $group_data['quote_type'] = $group['gtype'];
                        $group_data['quote_items'] = json_encode($group['item']);
                        $group_data['proposal_id'] = $insert_id;
                        $this->db->insert('tblproposal_quotes', $group_data);
                        $quote_ids[] = $this->db->insert_id();
                    }
                }
                if (count($items) > 0) {
                    foreach ($items as $key => $item) {
                        $this->db->insert('tblitems_in', array(
                            'description' => $item['description'],
                            'long_description' => isset($item['long_description']) ? nl2br($item['long_description']) : "",
                            'qty' => $item['qty'],
                            'rate' => number_format($item['rate'], get_decimal_places(), '.', ''),
                            'rel_id' => $insert_id,
                            'rel_type' => 'proposaltemplate',
                            'item_order' => $item['order'],
                        ));

                        $itemid = $this->db->insert_id();

                        if ($itemid) {

                            if (isset($item['taxname']) && is_array($item['taxname'])) {
                                foreach ($item['taxname'] as $taxname) {
                                    if ($taxname != '') {
                                        $tax_array = explode('|', $taxname);

                                        if (isset($tax_array[0]) && isset($tax_array[1])) {
                                            $tax_name = trim($tax_array[0]);
                                            $tax_rate = trim($tax_array[1]);
                                            if (total_rows('tblitemstax', array(
                                                    'itemid' => $itemid,
                                                    'taxrate' => $tax_rate,
                                                    'taxname' => $tax_name,
                                                    'rel_id' => $insert_id,
                                                    'rel_type' => 'proposaltemplate')) == 0) {
                                                $this->db->insert('tblitemstax', array(
                                                    'itemid' => $itemid,
                                                    'taxrate' => $tax_rate,
                                                    'taxname' => $tax_name,
                                                    'rel_id' => $insert_id,
                                                    'rel_type' => 'proposaltemplate'
                                                ));
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                //$this->update_total_tax($insert_id);
                logActivity('New Proposal Template Added [ID: ' . $insert_id . '.' . $data['name'] . ']');

                return $insert_id;
            }
        }
        return false;
    }


    /**
     * Update employee proposaltemplate
     * @param  array $data proposaltemplate data
     * @param  mixed $id proposaltemplate id
     * @return boolean
     */
    public function updateproposaltemplate($data, $id)
    {
        /*echo "<pre>";
        print_r($data);
        die('<--here');*/
        if (!isset($data['gratuity'])) {
            $data['gratuity'] = 0;
        }
        $data['sections'] = json_encode($data['sections']);
        unset($data['pg']);
        unset($data['relation_type']);
        unset($data['relation_id']);

        $removed_sec = isset($data['remove_sec']) ? json_encode($data['remove_sec'], true) : "";
        $rec_payment = isset($data['rec_payment']) ? $data['rec_payment'] : "";
        /*$save_and_send = "";
        if (isset($data['save_and_send'])) {
            $save_and_send = "save_send";
        }
        if (isset($data['send'])) {
            $save_and_send = "send";
        }*/
        if (isset($data['group']) && !empty(isset($data['group']))) {

            $quote_groups = $data['group'];
        }
        if (isset($data['signatures']) && !empty(isset($data['signatures']))) {

            $data['signatures'] = json_encode($data['signatures'], true);
        }
        if (isset($data['pschedulename']) && !empty(isset($data['pschedulename']))) {

            $this->load->model('paymentschedules_model');
            $payment_schedule['name'] = $data['pschedulename'];
            $payment_schedule['payment_schedule'] = $data['payment_schedule'];
            $payment_schedule['is_template'] = 1;
            if (isset($data['is_ps_template'])) {
                $payment_schedule['is_template'] = 1;
            } else {
                $payment_schedule['is_template'] = 0;
            }

            if (isset($data['ps_template']) && !empty(isset($data['ps_template']))) {
                $this->paymentschedules_model->updatepaymentschedule($payment_schedule, $data['ps_template']);
            } else {
                //$payment_schedule['name'] = $this->check_payment_schedule_name($payment_schedule['name']);
                $data['ps_template'] = $this->paymentschedules_model->addpaymentschedule($payment_schedule);
            }
        }
        if (isset($data['rec_payment'])) {
            $rec_payment['rel_id'] = $id;
            $rec_payment['rec_start_date'] = date('Y-m-d', strtotime($rec_payment['rec_start_date']));
            $rec_payment['rec_end_date'] = date('Y-m-d', strtotime($rec_payment['rec_end_date']));
            if (isset($rec_payment['rec_id']) && $rec_payment['rec_id'] > 0) {
                $rec_id = $rec_payment['rec_id'];
                unset($rec_payment['rec_id']);
                $this->update_rec_payment($rec_payment, $rec_id);
            } else {
                $this->add_rec_payment($rec_payment);
            }
        }
        $proposaltemplates = array();
        //$proposaltemplates = $this->proposaltemplates_model->check_proposal_name_exists($data['name'], $id);
        if (count($proposaltemplates) <= 0 || empty($proposaltemplates)) {
            $affectedRows = 0;
            unset($data['proposaltemplateid']);
            $items = array();
            if (isset($data['items'])) {
                $items = $data['items'];
                unset($data['items']);
            }
            $newitems = array();
            if (isset($data['newitems'])) {
                $newitems = $data['newitems'];
                unset($data['newitems']);
            }

            $data['updated_by'] = $this->session->userdata['staff_user_id'];
            $data['dateupdated'] = date('Y-m-d H:i:s');
            $data['content'] = $data['introduction'];
            if (isset($data['save_as_template'])) {
                $save_as_template = $data['save_as_template'];
            }
            $unsetters = array('currency_symbol', 'price', 'taxname', 'taxid', 'isedit', 'unit', 'description', 'long_description', 'tax', 'rate', 'quantity', 'item_select', 'item_group_select', 'task_select', 'task_id', 'expense_id', 'repeat_every_custom', 'repeat_type_custom', 'merge_current_invoice', 'group', 'introduction', 'product_package', 'group_id', 'group_type', 'group_name', 'pimage_title', 'pimage_caption', 'image', 'pvideo', 'ps_pkg_search', 'gid', 'signer', 'pschedulename', 'payment_schedule', 'remove_sec', 'is_ps_template', 'save_and_send', 'send', 'rec_payment', 'save_as_template', 'page_name', 'save_and_preview');
            foreach ($unsetters as $u) {
                if (isset($u)) {
                    unset($data[$u]);
                }
            }
            $action_data = array(
                'data' => $data,
                'newitems' => $newitems,
                'items' => $items,
                'id' => $id,
                'removed_items' => array()
            );
            $_data = $action_data;
            $data['removed_items'] = $_data['removed_items'];
            //$items                 = $_data['items'];
            $newitems = $_data['newitems'];
            $data = $_data['data'];
            unset($data['items']);
            unset($data['item_id']);
            unset($data['subtotal']);
            unset($data['discount_percent']);
            unset($data['discount_total']);
            unset($data['adjustment']);
            unset($data['total']);
            unset($data['adjustment']);
            unset($data['newitems']);
            if (isset($quote_groups)) {
                foreach ($quote_groups as $group) {
                    $group_data['quote_name'] = $group['gname'];
                    $group_data['quote_type'] = $group['gtype'];
                    $group_data['quote_order'] = $group['quote_order'];
                    $group_data['quote_items'] = json_encode($group['item'], true);
                    $group_data['proposal_id'] = $id;
                    $quote_ids[] = $group['gid'];
                    if (isset($group['gid']) && $group['gid'] != "") {
                        $this->db->where('qid', $group['gid']);
                        $this->db->update('tblproposal_quotes', $group_data);
                    } else {
                        $this->db->insert('tblproposal_quotes', $group_data);
                        $quote_ids[] = $this->db->insert_id();
                    }
                }
            }
            $data['removed_sections'] = $removed_sec;
            $data['is_template'] = 1;
            if (isset($data['rel_id']) && $data['rel_id'] > 0) {
                $data['is_template'] = 0;
                /*if (isset($save_as_template)) {
                    $data['is_template'] = 1;
                }*/
            }
            $this->db->where('templateid', $id);
            $this->db->update('tblproposaltemplates', $data);
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }
            if ($affectedRows > 0) {


                $this->load->model('taxes_model');
                $this->db->where('rel_id', $id);
                $this->db->delete('tblitems_in');
                $this->db->where('rel_id', $id);
                $this->db->delete('tblitemstax');
                if (count($newitems) > 0) {
                    foreach ($newitems as $key => $item) {
                        $this->db->insert('tblitems_in', array(
                            'description' => $item['description'],
                            'long_description' => isset($item['long_description']) ? nl2br($item['long_description']) : '',
                            'qty' => $item['qty'],
                            'rate' => number_format($item['rate'], get_decimal_places(), '.', ''),
                            'rel_id' => $id,
                            'rel_type' => 'proposaltemplate',
                            'item_order' => $item['order'],
                            'unit' => isset($item['unit']) ? $item['unit'] : ''
                        ));
                        $new_item_added = $this->db->insert_id();
                        if ($new_item_added) {

                            if (isset($item['taxname']) && is_array($item['taxname'])) {
                                foreach ($item['taxname'] as $taxname) {
                                    if ($taxname != '') {
                                        $tax_array = explode('|', $taxname);
                                        if (isset($tax_array[0]) && isset($tax_array[1])) {
                                            $tax_name = trim($tax_array[0]);
                                            $tax_rate = trim($tax_array[1]);
                                            if (total_rows('tblitemstax', array(
                                                    'taxrate' => $tax_rate,
                                                    'taxname' => $tax_name,
                                                    'itemid' => $new_item_added,
                                                    'rel_id' => $id,
                                                    'rel_type' => 'proposaltemplate')) == 0) {
                                                $this->db->insert('tblitemstax', array(
                                                    'taxrate' => $tax_rate,
                                                    'taxname' => $tax_name,
                                                    'itemid' => $new_item_added,
                                                    'rel_id' => $id,
                                                    'rel_type' => 'proposaltemplate'
                                                ));
                                                if ($this->db->affected_rows() > 0) {
                                                    $affectedRows++;
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            $affectedRows++;
                        }
                    }
                }
                logActivity('Proposal Template Updated [ID: ' . $id . '.' . $data['name'] . ']');

                return true;
            }
        }
        return false;
    }

    /**
     * Get employee proposaltemplate by id
     * @param  mixed $id Optional proposaltemplate id
     * @return mixed     array if not id passed else object
     */
    public function getproposaltemplates($id = '')
    {
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', 0);
        if (is_numeric($id)) {
            $this->db->where('templateid', $id);
            $proposal = $this->db->get('tblproposaltemplates')->row();
            $proposal->items = $this->get_invoice_items($id);
            return $proposal;
        }
        $this->db->where('is_template', 1);
        $this->db->order_by('datecreated', 'desc');
        $proposal = $this->db->get('tblproposaltemplates')->result_array();
        return $proposal;
    }

    public function get_invoice_items($id)
    {
        $this->db->select();
        $this->db->from('tblitems_in');
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'proposaltemplate');
        $this->db->order_by('item_order', 'asc');
        $items = $this->db->get()->result_array();

        return $items;
    }

    public function get_invoice_item($id)
    {
        $this->db->where('id', $id);

        return $this->db->get('tblitems_in')->row();
    }

    /**
     * Delete employee proposaltemplate
     * @param  mixed $id proposaltemplate id
     * @return mixed
     */
    public function deleteproposaltemplate($id)
    {
        //$current = $this->getproposaltemplates($id);
        // Check first if proposaltemplate is used in table
        // if (is_reference_in_table('proposaltemplate_id', 'tblroleuserproposaltemplate', $id)) {
        //     return array(
        //         'referenced' => true
        //     );
        // }
        $affectedRows = 0;
        // $this->db->where('templateid', $id);
        // $this->db->delete('tblproposaltemplates');
        $data['deleted'] = 1;
        $data['updated_by'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $this->db->where('templateid', $id);
        $this->db->update('tblproposaltemplates', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        // $this->db->where('templateid', $id);
        // $this->db->delete('tblproposaltemplatepermissions');
        // if ($this->db->affected_rows() > 0) {
        //     $affectedRows++;
        // }
        $this->db->select('name');
        $this->db->where('templateid', $id);
        $name = $this->db->get('tblproposaltemplates')->row()->name;
        if ($affectedRows > 0) {
            logActivity('Proposal Deleted :[ID:' . $id . ', Name:' . $name . ']');

            return true;
        }

        return false;
    }

    /**
     * Get proposaltemplate id
     * @param  mixed $id permission id
     * @return mixed if id passed return object else array
     */
    public function check_proposal_name_exists($name, $id)
    {
        $brandid = get_user_session();
        if ($id > 0) {
            $where = array('templateid !=' => $id, 'name =' => $name, 'deleted =' => 0, 'brandid =' => $brandid);
        } else {
            $where = array('name =' => $name, 'deleted =' => 0, 'brandid =' => $brandid);
        }
        return $this->db->where($where)->get('tblproposaltemplates')->row();
    }

    /*
     * Added by Masud for paymnet schedule on 06-04-2018
     */
    /*public function check_payment_schedule_name($name)
    {
        $brandid = get_user_session();
        $name = explode("_", $name);
        $name = $name[0];
        $where = array('name LIKE' => $name . "%", 'deleted =' => 0, 'brandid =' => $brandid);
        $payments = $this->db->where($where)->get('tblpaymenttemplates')->result();
        return $name . "_" . count($payments);
    }*/

    /*
        added by Masud for quote on 20/02/2018
    */
    function getproposal_quotes($id)
    {

        $this->db->where('proposal_id', $id);
        $this->db->where('deleted', 0);
        $this->db->order_by('quote_order', 'asc');
        $quotes = $this->db->get('tblproposal_quotes')->result_array();

        return $quotes;
    }

    function getproposal_quote_by_id($id)
    {

        $this->db->where('qid', $id);
        $this->db->order_by('quote_order', 'asc');
        $quotes = $this->db->get('tblproposal_quotes')->row();

        return $quotes;
    }

    function getproposal_gallery($id)
    {

        $this->db->where('proposal_id', $id);
        $this->db->where('type', 'gallery');
        $this->db->where('deleted', 0);
        //$this->db->order_by('quote_order', 'asc');
        $quotes = $this->db->get('tblproposal_gallery_files')->result_array();

        return $quotes;
    }

    function getproposal_files($id)
    {

        $this->db->where('proposal_id', $id);
        $this->db->where('type', 'file');
        $this->db->where('deleted', 0);
        //$this->db->order_by('quote_order', 'asc');
        $quotes = $this->db->get('tblproposal_gallery_files')->result_array();

        return $quotes;
    }

    function getproposal_medias($id, $type = "")
    {

        $this->db->where('proposal_id', $id);
        if (isset($type) && $type != "") {
            $this->db->where('type', $type);
        }
        $this->db->where('deleted', 0);
        //$this->db->order_by('quote_order', 'asc');
        $quotes = $this->db->get('tblproposal_gallery_files')->result_array();

        return $quotes;
    }

    function get_quote_group($data)
    {
        $name = $data['gname'];
        $this->db->where('quote_name', $name);
        $quotes = $this->db->get('tblproposal_quotes')->result_array();
        foreach ($quotes as $quote) {
            if ($quote['qid'] == $data['qid']) {
                return 0;
            }
        }
        return count($quotes);
    }

    function addproposalgalvideo($data)
    {
        $this->db->insert('tblproposal_gallery_files', $data);
    }

    function delete_file_image($id)
    {
        $data['deleted'] = 1;
        $this->db->where('id', $id);
        $this->db->update('tblproposal_gallery_files', $data);
    }

    function delete_quote_group($id)
    {

        $data['deleted'] = 1;
        $this->db->where('qid', $id);
        $this->db->update('tblproposal_quotes', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function unique_quote_group_name($name, $pid)
    {
        $name = $old_desc = explode("_", $name);
        $name = $name[0];
        $query = 'SELECT `quote_name` FROM `tblproposal_quotes` WHERE `quote_name` LIKE "' . $name . '%" AND `proposal_id` = "' . $pid . '" AND `deleted` = 0';
        $result = $this->db->query($query);
        $rows = $result->result();
        $name_exist = array();
        foreach ($rows as $row) {
            if (!empty($row->quote_name)) {
                $old_desc = explode("_", $row->quote_name);
                $exist = $old_desc[0];
                if ($name == $exist) {
                    $name_exist[] = $row->quote_name;
                }
            }
        }
        $name = $name . "_" . count($name_exist);
        return $name;
    }

    function duplicate_proposal($data)
    {
        $proposal_id = $data['duplicate_record_id'];
        $quotes = $this->getproposal_quotes($proposal_id);
        $medias = $this->getproposal_medias($proposal_id);
        $brandid = $data['brandid'];
        if ($data['duplicate_by_brand'] == "current_brand") {
            $brandid = get_user_session();
        }
        $proposal = (array)$this->getproposaltemplates($proposal_id);
        $proposal['rel_type'] = $data['rel_type'];
        $proposal['rel_id'] = $data['rel_id'];
        unset($proposal['templateid']);
        unset($proposal['updated_by']);
        unset($proposal['dateupdated']);
        unset($proposal['items']);
        $proposal['created_by'] = $this->session->userdata['staff_user_id'];
        $proposal['datecreated'] = date('Y-m-d H:i:s');
        $proposal['status'] = 'draft';
        $curr_pro = explode("_", $proposal['name']);
        $temp = $curr_pro[0];
        $this->db->select('name');
        $this->db->where('brandid', $brandid);
        $this->db->where('deleted', 0);
        $this->db->like('name', $proposal['name']);
        //$this->db->where('is_template', 1);
        /*if ($proposal['rel_type'] && $proposal['rel_id'] > 0) {
            $this->db->where('rel_type', $proposal['rel_type']);
            $this->db->where('rel_id', $proposal['rel_id']);
        }*/
        $this->db->order_by('templateid', 'ASC');
        $rows = $this->db->get('tblproposaltemplates')->result();
        $proposal_name = explode("_", $proposal['name']);

        if (!empty($rows)) {
            $exist_record = array();
            foreach ($rows as $row) {
                $old_desc = explode("_", $row->name);
                //$proposal_name = explode("_", $row->name);

                if ($proposal_name[0] == $old_desc[0]) {
                    $exist_record[] = $old_desc[0];
                }
            }
            $curr_pointer = count($exist_record);
            if ($curr_pointer > 0) {
                $proposal['name'] = $proposal_name[0] . '_' . $curr_pointer;
            }
        }
        $proposal['brandid'] = $brandid;
        $proposal['signatures'] = "";
        if (isset($proposal['ps_template']) && $proposal['ps_template'] > 0) {
            $paymentschedule = (array)$this->paymentschedules_model->getpaymentschedules($proposal['ps_template']);
            unset($paymentschedule['templateid']);
            $paymentschedule['brandid'] = $brandid;
            $paymentschedule['payment_schedule'] = $paymentschedule['schedules'];
            unset($paymentschedule['schedules']);
            unset($paymentschedule['updated_by']);
            unset($paymentschedule['dateupdated']);
            $curr_pro = explode("_", $paymentschedule['name']);
            $temp = $curr_pro[0];
            $query = 'SELECT `name` FROM `tblpaymenttemplates` WHERE `name` LIKE "' . $temp . '%" AND `brandid` = "' . $brandid . '" AND `deleted` = 0 ORDER BY `templateid` DESC LIMIT 0,1';
            $result = $this->db->query($query);
            $rows = $result->row();
            if (!empty($rows->name)) {
                $old_desc = explode("_", $rows->name);
                $curr_pointer = @$old_desc[1] + 1;
                if (isset($old_desc[1]) && $old_desc[1] > 0) {
                    $paymentschedule['name'] = $old_desc[0] . '_' . $curr_pointer;
                } else {
                    $paymentschedule['name'] = $old_desc[0] . '_1';
                }
            }
            $ps_template_id = $this->paymentschedules_model->addpaymentschedule($paymentschedule);
            $proposal['ps_template'] = $ps_template_id;
        }
        $proposalversion = str_pad(get_brand_option('next_proposal_number'), 2, '0', STR_PAD_LEFT);
        $proposal['proposal_version'] = $proposalversion;
        $this->db->insert('tblproposaltemplates', $proposal);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {

            /*if (isset($proposal['rel_id']) && $proposal['rel_id'] > 0) {
                $proposalrelation = array();
                $proposalrelation['proposal_id'] = $insert_id;
                $proposalrelation['rel_type'] = $proposal['rel_type'];
                $proposalrelation['rel_id'] = $proposal['rel_id'];
                $proposalrelation['status'] = "draft";
                $proposalrelation['comment'] = "";
                $this->db->insert('tblproposalrelation', $proposalrelation);
            }*/
            if ($data['duplicate_by_brand'] == "current_brand") {
                if (isset($quotes) && count($quotes) > 0) {
                    foreach ($quotes as $quote) {
                        unset($quote['qid']);
                        $quote['proposal_id'] = $insert_id;
                        $this->db->insert('tblproposal_quotes', $quote);
                    }
                }
                if (isset($medias) && count($medias) > 0) {
                    foreach ($medias as $media) {
                        unset($media['id']);
                        $media['proposal_id'] = $insert_id;
                        $this->db->insert('tblproposal_gallery_files', $media);
                    }
                }
            } else {
                if (isset($quotes) && count($quotes) > 0) {
                    foreach ($quotes as $quote) {
                        $quote_items = json_decode($quote['quote_items'], true);
                        unset($quote['qid']);
                        $new_quote_items = array();
                        foreach ($quote_items as $quote_item) {
                            $item_data['duplicate_record_id'] = $quote_item['id'];
                            $item_data['duplicate_by_brand'] = 'existing_brand';
                            $item_data['brandid'] = $brandid;
                            if ($quote_item['type'] == 'package') {
                                $id = $this->invoice_items_model->duplicate_group($item_data);
                                if ($id) {
                                    $mydir = get_upload_path_by_type('product_services_package_image') . "/" . $id . "/";
                                    if (!is_dir($mydir)) {
                                        mkdir($mydir);
                                    }
                                    $path = get_upload_path_by_type('product_services_package_image') . $item_data['duplicate_record_id'] . '/*.*';
                                    $files = glob($path);
                                    foreach ($files as $file) {
                                        $file_to_go = str_replace("/" . $item_data['duplicate_record_id'] . "/", "/" . $id . "/", $file);
                                        copy($file, $file_to_go);
                                    }

                                }
                                $quote_item['id'] = $id;
                            }
                            if ($quote_item['type'] == 'product') {
                                $id = $this->invoice_items_model->make_duplicate_pro_service($item_data);
                                if ($id) {
                                    $mydir = get_upload_path_by_type('line_items_image') . "/" . $id . "/";
                                    if (!is_dir($mydir)) {
                                        mkdir($mydir);
                                    }
                                    $path = get_upload_path_by_type('line_items_image') . $item_data['duplicate_record_id'] . '/*.*';
                                    $files = glob($path);
                                    foreach ($files as $file) {
                                        $file_to_go = str_replace("/" . $item_data['duplicate_record_id'] . "/", "/" . $id . "/", $file);
                                        copy($file, $file_to_go);
                                    }

                                }
                                $quote_item['id'] = $id;
                            }
                            $new_quote_items[] = $quote_item;
                        }
                        $quote['quote_items'] = json_encode($new_quote_items, true);
                        $quote['proposal_id'] = $insert_id;
                        $this->db->insert('tblproposal_quotes', $quote);
                    }
                    if (isset($medias) && count($medias) > 0) {
                        foreach ($medias as $media) {
                            unset($media['id']);
                            $media['proposal_id'] = $insert_id;
                            $this->db->insert('tblproposal_gallery_files', $media);
                        }
                    }
                }
            }
            $this->db->where('name', 'next_proposal_number');
            $this->db->where('brandid', get_user_session());
            $this->db->set('value', 'value+1', false);
            $this->db->update('tblbrandsettings');
        }
        return $insert_id;
    }

    function get_proposal_feedback($id)
    {
        $this->db->where('proposal_id', $id);
        $feedback = $this->db->get('tblproposaltemplate_feedback')->row();
        return $feedback;
    }

    function add_proposal_feedback($data)
    {

        $this->db->select('*');
        $this->db->where('proposal_id', $data['proposal_id']);
        $feedback = $this->db->get('tblproposaltemplate_feedback')->row();
        $signatures = json_encode($data['signatures'], true);
        $pdata['signatures'] = $signatures;
        if (isset($data['ps_template']) && $data['ps_template'] > 0) {
            $payment_schedule['payment_schedule'] = $data['payment_schedule'];
            $this->paymentschedules_model->updateschedule($payment_schedule, $data['ps_template']);
        }
        unset($data['signatures']);
        unset($data['payment_schedule']);
        /*if ($data['is_final'] == 1) {
            $final['is_final'] = 0;
            $this->db->where('rel_id', $data['rel_id']);
            $this->db->update('tblproposaltemplate_feedback', $final);
        }*/

        if (count($feedback) > 0) {
            $accepted = json_decode($feedback->accepted, true);
            if (!in_array($data['accepted'], $accepted)) {
                array_push($accepted, $data['accepted']);
            }
            $data['accepted'] = json_encode($accepted);
            // unset($data['selected_items']);
            $this->db->where('proposal_id', $data['proposal_id']);
            $this->db->update('tblproposaltemplate_feedback', $data);

            $this->db->where('templateid', $data['proposal_id']);
            $this->db->update('tblproposaltemplates', $pdata);
            return $feedback->id;
        } else {
            $accepted = array();
            array_push($accepted, $data['accepted']);
            $data['accepted'] = json_encode($accepted);
            $this->db->insert('tblproposaltemplate_feedback', $data);
            $insert_id = $this->db->insert_id();
            $this->db->where('templateid', $data['proposal_id']);
            $this->db->update('tblproposaltemplates', $pdata);
            return $insert_id;
        }
    }

    function add_rec_payment($data)
    {
        $this->db->insert('tblrecurring_payments', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return true;
        }
        return false;
    }

    function update_rec_payment($data, $id)
    {

        $this->db->where('rec_id', $id);
        $this->db->update('tblrecurring_payments', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function get_rec_payment($id)
    {
        $this->db->where('rel_id', $id);
        $result = (array)$this->db->get('tblrecurring_payments')->row();
        return $result;
    }

    /**
     * Added By: Masud
     * Dt: 06/27/2018
     * For Kanban view Contact
     */

    public function get_kanban_proposals($rel_id = "", $rel_type = "", $data, $status = "active")
    {

        $limit = $start = "";
        if (isset($data['limit']) && $data['limit'] > 0) {
            $limit = $data['limit'];
        }
        if (isset($data['page']) && $data['page'] > 0) {
            $page = $data['page'];
        }
        if (isset($limit) && isset($page)) {
            $start = ($page - 1) * $limit;
        }
        $brandid = get_user_session();
        $aColumns = array('*');

        $sIndexColumn = "templateid";
        $sTable = 'tblproposaltemplates';

        $where = array();
        if ($brandid > 0) {
            array_push($where, 'AND brandid =' . $brandid);
        }

        if ($status == "active") {
            //array_push($where, ' AND deleted = 0 AND (status="draft" OR status="sent" OR status="accepted")');
            $isarchieve = 0;
        } else {
            //array_push($where, ' AND deleted = 0 AND (status="decline" OR status="archive" OR status="closed")');
            $isarchieve = 1;
        }
        array_push($where, ' AND deleted = 0 AND (isarchieve=' . $isarchieve . ')');
        if ($rel_type != "") {
            array_push($where, 'AND rel_type="' . $rel_type . '" AND rel_id =' . $rel_id);
        } else {
            array_push($where, 'AND is_template=1');
            $url = "proposaltemplates/proposal/";
        }
        $result = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), $where, array(), '', $limit, $start);

        return $result['rResult'];
    }

    /**
     * Added By: Masud
     * Dt: 07/12/2018
     * For Pin/Unpin Proposal
     */
    public function pinproposal($proposal_id)
    {
        $session_data = get_session_data();
        $user_id = $session_data['staff_user_id'];

        $pinexist = $this->db->select('pinid')->from('tblpins')->where('pintype = "Proposal" AND pintypeid = ' . $proposal_id . ' AND userid = ' . $user_id)->get()->row();
        if (!empty($pinexist)) {
            $this->db->where('userid', $user_id);
            $this->db->where('pintypeid', $proposal_id);
            $this->db->where('pintype', "Proposal");
            $this->db->delete('tblpins');

            return "deleted";
        } else {
            $this->db->insert('tblpins', array(
                'pintype' => "Proposal",
                'pintypeid' => $proposal_id,
                'userid' => $user_id
            ));

            return "added";
        }
    }

    /**
     * Added By: Masud
     * Dt: 07/30/2018
     * For Decline Proposal
     */
    public function updatestatus($id, $status)
    {
        $data = array();
        if ($status == "archive") {
            $data['isarchieve'] = 1;
        } elseif ($status == "active") {
            $data['isarchieve'] = 0;
        } else {
            $data['status'] = $status;
        }
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $this->db->where('templateid', $id);
        $this->db->update('tblproposaltemplates', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Added By: Masud
     * Dt: 08/07/2018
     * For Decline Proposal
     */

    public function getclientproposal($id = '')
    {
        $this->db->where('deleted', 0);
        $this->db->where('templateid', $id);
        $proposal = $this->db->get('tblproposaltemplates')->row();
        $proposal->items = $this->get_invoice_items($id);
        return $proposal;
    }

    /**
     * Added By: Masud
     * Dt: 08/08/2018
     * For Decline Proposal
     */

    public function getproposalbytoken($token)
    {
        $this->db->where('token', $token);
        $proposal = $this->db->get('tblproposaltoken')->row();
        return $proposal;
    }

    function addproposalinvoice($data)
    {
        $this->db->insert('tblproposalinvoice', $data);
    }

    function get_proposal_invoices($id, $status = 0)
    {
        $this->db->select('tblproposalinvoice.invoice_id,tblinvoices.*', $id);
        $this->db->join('tblinvoices', 'tblinvoices.id = tblproposalinvoice.invoice_id');
        /*$this->db->join('tblinvoicepaymentrecords', 'tblinvoices.id = tblinvoicepaymentrecords.invoiceid', 'left');*/
        $this->db->where('proposal_id', $id);
        if ($status > 0) {
            if ($status == 1) {
                $this->db->where('(tblinvoices.status=1 OR tblinvoices.status=3 OR tblinvoices.status=4)');
                $this->db->limit(1);
            } else {
                $this->db->where('tblinvoices.status', $status);
            }
        }
        $this->db->order_by('tblinvoices.duedate', 'asc');
        $invoices = $this->db->get('tblproposalinvoice')->result();
        foreach ($invoices as $key => $invoice) {
            $this->db->select('*');
            $this->db->where('invoiceid', $invoice->invoice_id);
            $this->db->order_by('date', 'asc');
            $records = $this->db->get('tblinvoicepaymentrecords')->result();
            $invoices[$key]->paymentrecords = $records;

        }
        return $invoices;
    }

    /**
     * Added By: Masud
     * Dt: 07/30/2018
     * For Decline Proposal
     */
    public function addreason($id, $status, $reson)
    {
        $data = array();
        $data['status'] = $status;
        $data['resason_comment'] = $reson;
        if ($status == "decline" || $status == "closed") {
            $data['isarchieve'] = 1;
        }
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $this->db->where('templateid', $id);
        $this->db->update('tblproposaltemplates', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function sentproposal($id, $data)
    {
        $emailbody = $data['emailbody'];
        $emailbody = str_replace('#emailbody', $emailbody, $data['emailview']->output->final_output);
        $subject = $data['emailsubject'];
        $ccemails = $data['emailcc'];
        $toemails = $data['emailto'];
        if ($ccemails != "") {
            $toemails .= $ccemails;
        }
        $toemails = explode('; ', $toemails);
        /*foreach ($toemails as $toemail) {
            $this->db->where('email', $toemail);
            $staff = $this->db->select('*')->from('tblstaff')->get()->row();
            if(empty($staff)){
                $this->db->where('email', $toemail);
                $addressbook = $this->db->select('addressbookid')->from('tbladdressbookemail')->get()->row();
                if(!empty($addressbook)) {
                    $addressbookid = $addressbook->addressbookid;
                    $this->db->where('addressbookid', $addressbookid);
                    $staff = $this->db->select('*')->from('tbladdressbook')->get()->row();
                }
            }
        }*/
        $data = array();
        $data['status'] = "sent";
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $this->db->where('templateid', $id);
        $this->db->update('tblproposaltemplates', $data);
        $proposal = $this->proposaltemplates_model->getproposaltemplates($id);
        $rel_type = $proposal->rel_type;
        $rel_id = $proposal->rel_id;
        if ($rel_type == "lead") {
            $event = $this->leads_model->get($rel_id);
        } else {
            $event = $this->projects_model->get($rel_id);
        }
        $this->db->where('eventtypeid', $event->eventtypeid);
        $event_type = $this->db->get('tbleventtype')->row()->eventtypename;
        $signers = json_decode($proposal->signatures, true);
        $signeremail = array();
        foreach ($signers as $signer) {
            if ($signer['signer_type'] == "client") {
                $query = "SELECT email FROM tbladdressbookemail WHERE type='primary' AND addressbookid=" . $signer['signer_id'];
                $result = $this->db->query($query);
                $email = "";
                if (!empty($result->first_row())) {
                    $email = $result->first_row()->email;
                    $signer['email'] = $email;
                }
            } else {
                $signer['email'] = get_staff_email($signer['signer_id']);
            }
            $data = array();
            $data['proposal_id'] = $id;
            $data['client_id'] = $signer['signer_id'];
            $data['email'] = isset($signer['email']) ? $signer['email'] : "";
            // Check if the key exists
            $this->db->where('client_id', $data['client_id']);
            $this->db->where('email', $data['email']);
            $this->db->where('proposal_id', $id);
            $exists = $this->db->get('tblproposaltoken')->row();
            if ($exists) {
                $data['token'] = $exists->token;
            } else {
                $data['token'] = md5(rand() . microtime());
                if ($exists) {
                    $data['token'] = md5(rand() . microtime());
                }
                $data['usertype'] = $signer['signer_type'];
                $this->db->insert('tblproposaltoken', $data);
            }
            $proposallink = site_url('proposal/proposal/' . $data['token']);
            $emailmessage = $emailbody;
            $emailmessage = str_replace('#proposal_link', $proposallink, $emailmessage);
            //$assigned_mail = get_staff_email($event->assigned);
            /*$merge_fields = array();
            $merge_fields = array_merge($merge_fields, get_proposal_merge_fields($id));
            $merge_fields['{proposal_link}'] = site_url('proposal/view/' . $data['token']);
            $merge_fields['{assigned_mail}'] = "mailto:" . $assigned_mail;
            $merge_fields['{proposal_event_name}'] = $event->name;
            $merge_fields['{events_detail}'] = "<br /><br /><b>Event Name: " . $event->name . "</b><br /><b>Event Type: " . $event_type . "</b><br /><b>Event Date & Time: " . $event->eventstartdatetime . "</b><br /><br />";
            $merge_fields['{proposal_proposal_to}'] = $signer['name'];*/
            $this->emails_model->send_simple_email($data['email'], $subject, $emailmessage);
            $signeremail[] = $signer['email'];
        }
        $nonsigneremail = array_diff($toemails, $signeremail);

        foreach ($nonsigneremail as $signer) {
            if (!empty($signer) || $signer !== "") {
                $emailmessage = $emailbody;
                $data = array();
                $data['proposal_id'] = $id;
                $data['client_id'] = 0;
                $data['email'] = $signer;
                // Check if the key exists
                $this->db->where('email', $signer);
                $this->db->where('proposal_id', $id);
                $exists = $this->db->get('tblproposaltoken')->row();
                if ($exists) {
                    $data['token'] = $exists->token;
                } else {
                    $data['token'] = md5(rand() . microtime());
                    if ($exists) {
                        $data['token'] = md5(rand() . microtime());
                    }
                    $data['usertype'] = 'notclient';
                    $this->db->insert('tblproposaltoken', $data);
                }
                $proposallink = site_url('proposal/proposal/' . $data['token']);
                $emailmessage = str_replace('#proposal_link', $proposallink, $emailmessage);
                $this->emails_model->send_simple_email($data['email'], $subject, $emailmessage);
            }
        }
    }

    function sentdeclinemail($data)
    {
        $id = $data['id'];
        $proposal = $this->proposaltemplates_model->getproposaltemplates($id);
        $rel_type = $proposal->rel_type;
        $rel_id = $proposal->rel_id;
        if ($rel_type == "lead") {
            $event = $this->leads_model->get($rel_id);
        } else {
            $event = $this->projects_model->get($rel_id);
        }
        $this->db->where('eventtypeid', $event->eventtypeid);
        $event_type = $this->db->get('tbleventtype')->row()->eventtypename;
        $signers = json_decode($proposal->signatures, true);
        if ($data['usertype'] == "client") {
            $decline_by = get_addressbook_full_name($data['userid']);
        } else {
            $decline_by = get_staff_full_name($data['userid']);
        }
        foreach ($signers as $signer) {
            if ($signer['signer_id'] != $data['userid']) {
                if ($signer['signer_type'] == "client") {
                    $query = "SELECT email FROM tbladdressbookemail WHERE type='primary' AND addressbookid=" . $signer['signer_id'];
                    $result = $this->db->query($query);
                    $email = "";
                    if (!empty($result->first_row())) {
                        $email = $result->first_row()->email;
                        $signer['email'] = $email;
                    }
                } else {
                    $signer['email'] = get_staff_email($signer['signer_id']);
                }
                $assigned_mail = get_staff_email($event->assigned);
                $merge_fields = array();
                $merge_fields = array_merge($merge_fields, get_proposal_merge_fields($id));
                $merge_fields['{proposal_link}'] = site_url('proposal/view/' . $data['token']);
                $merge_fields['{assigned_mail}'] = "mailto:" . $assigned_mail;
                $merge_fields['{proposal_event_name}'] = $event->name;
                $merge_fields['{events_detail}'] = "<br /><br /><b>Event Name: " . $event->name . "</b><br /><b>Event Type: " . $event_type . "</b><br /><b>Event Date & Time: " . $event->eventstartdatetime . "</b><br /><br />";
                $merge_fields['{proposal_proposal_to}'] = $signer['name'];
                $message = "Hi ,<br /><br />";
                $message .= "Prposal for " . $event->name . " has been declined by " . $decline_by . "<br /><br />";
                $message .= "<b>Reason : </b> " . $data['reason'];
                $this->emails_model->send_simple_email($signer['email'], 'Proposal Declined', $message);

            }
        }
    }

    function get_proposal_status($id)
    {
        $this->db->where('templateid', $id);
        $status = $this->db->get('tblproposaltemplates')->row()->status;
        return $status;
    }

    /**
     * Added By: Masud
     * Dt: 09/13/2018
     * for Member sign
     */
    function addmembersign($id, $data)
    {
        $proposal = $this->getproposaltemplates($id);
        $signatures = json_decode($proposal->signatures, true);
        foreach ($signatures as $key => $signature) {
            if ($signature['signer_id'] == $data['signer'] && $signature['signer_type'] == "member") {
                $signatures[$key]['image'] = $data['image'];
                $signatures[$key]['sign_date'] = date('F d, Y');
            }
        }
        $signatures = json_encode($signatures);
        $this->db->where('templateid', $id);
        $this->db->update('tblproposaltemplates', array('signatures' => $signatures));
        if ($this->db->affected_rows() > 0) {
            return 1;
        }
        return 0;
    }

    function sentPaymentemail($data)
    {
        $invoiceid = $data['invoiceid'];
        $id = get_invoice_proposalid($invoiceid);
        $proposal = $this->proposaltemplates_model->getproposaltemplates($id);
        $rel_type = $proposal->rel_type;
        $rel_id = $proposal->rel_id;
        if ($rel_type == "lead") {
            $event = $this->leads_model->get($rel_id);
        } else {
            $event = $this->projects_model->get($rel_id);
        }
        $this->db->where('eventtypeid', $event->eventtypeid);
        $event_type = $this->db->get('tbleventtype')->row()->eventtypename;
        if (!empty($event->assigned) && count($event->assigned) > 0) {
            foreach ($event->assigned as $assigned) {
                $assigned_mail = get_staff_email($assigned);
                $message = "<h1>PAYMENT NOTIFICATION</h1><br /><br />";
                $message .= "<b>Project : </b> " . $event->name . "<br /><br />";
                $message .= "<b>Project Date : </b> " . date('m/d/Y H:i', strtotime($event->eventstartdatetime)) . "<br /><br />";
                $message .= "<b>Paymnet Type : </b> " . $data['paymentmode'] . "<br /><br />";
                $message .= "<b>Paymnet Total : </b>" . format_money($data['total']) . "<br /><br />";
                $message .= "<b>Estimated Paymnet Date : </b> " . $data['estimated'] . "<br /><br /><br />";
                $message .= $data['message'] . "<br /><br />";
                $this->emails_model->send_simple_email(get_brand_option('smtp_email'), 'PAYMENT NOTIFICATION', $message);
            }
        }
    }

    function close($data)
    {
        $proposalid = $data['proposalid'];
        unset($data['proposalid']);
        $data['isclosed'] = 1;
        $data['isarchieve'] = 1;
        $this->db->where('templateid', $proposalid);
        $this->db->update('tblproposaltemplates', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function reopen($data)
    {
        $proposalid = $data['proposalid'];
        unset($data['proposalid']);
        $data['isclosed'] = 0;
        $data['isarchieve'] = 0;
        $this->db->where('templateid', $proposalid);
        $this->db->update('tblproposaltemplates', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function decline($id, $data)
    {
        $proposal = $this->getproposaltemplates($id);
        $declinedby = array();
        array_push($declinedby, $data['declinedby']);
        $data['declinedby'] = json_encode($declinedby, true);
        unset($data['usertoken']);
        /*$data['isarchieve']=1;*/
        $data['status'] = 'decline';
        /*echo "<pre>";
        print_r($data);
        die();*/
        $this->db->where('templateid', $id);
        $this->db->update('tblproposaltemplates', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }

    function get_proposal_invoices_id($proposalid)
    {
        $this->db->select('invoice_id');
        $this->db->where('proposal_id', $proposalid);
        $invoices = $this->db->get('tblproposalinvoice')->row();
        $invoiceid = $invoices->invoice_id;
        return $invoiceid;
    }

    function sentproposalupdatemail($data)
    {
        $id = $data['proposal_id'];
        $proposal = $this->proposaltemplates_model->getproposaltemplates($id);
        $rel_type = $proposal->rel_type;
        $rel_id = $proposal->rel_id;
        if ($rel_type == "lead") {
            $event = $this->leads_model->get($rel_id);
        } else {
            $event = $this->projects_model->get($rel_id);
        }
        $this->db->where('eventtypeid', $event->eventtypeid);
        $event_type = $this->db->get('tbleventtype')->row()->eventtypename;
        $signers = json_decode($proposal->signatures, true);
        $changed_by = get_addressbook_full_name($data['accepted']);
        foreach ($signers as $signer) {
            /*if ($signer['signer_id'] != $data['userid']) {*/
            if ($signer['signer_type'] == "client") {
                $query = "SELECT email FROM tbladdressbookemail WHERE type='primary' AND addressbookid=" . $signer['signer_id'];
                $result = $this->db->query($query);
                $email = "";
                if (!empty($result->first_row())) {
                    $email = $result->first_row()->email;
                    $signer['email'] = $email;
                }
            } else {
                $signer['email'] = get_staff_email($signer['signer_id']);
            }
            $assigned_mail = get_staff_email($event->assigned);
            $merge_fields = array();
            $merge_fields = array_merge($merge_fields, get_proposal_merge_fields($id));
            $merge_fields['{proposal_link}'] = site_url('proposal/view/' . $data['token']);
            $merge_fields['{assigned_mail}'] = "mailto:" . $assigned_mail;
            $merge_fields['{proposal_event_name}'] = $event->name;
            $merge_fields['{events_detail}'] = "<br /><br /><b>Event Name: " . $event->name . "</b><br /><b>Event Type: " . $event_type . "</b><br /><b>Event Date & Time: " . $event->eventstartdatetime . "</b><br /><br />";
            $merge_fields['{proposal_proposal_to}'] = $signer['name'];
            $message = "Hi ,<br /><br />";
            $message .= "Prposal for " . $event->name . " has been Modified by " . $changed_by . "<br /><br />";
            $message .= "<a class = 'btn btn-default' href='" . site_url('proposal/view/' . $data['token']) . "'>" . $proposal->name . "</a> ";
            $this->emails_model->send_simple_email($signer['email'], 'Proposal qoute changed', $message);

            /*}*/
        }
    }

    function get_clients($type, $id)
    {
        $brandid = get_user_session();
        if ($type == "lead") {
            $data['rel_content'] = $this->leads_model->get($id);
            $this->db->select('contactid');
            $this->db->distinct();
            $this->db->where('leadid', $id);
            $this->db->where('brandid', $brandid);
            $contacts = $this->db->get('tblleadcontact')->result();
        } else {
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
            $data['rel_content'] = $this->projects_model->get($id);
        }
        /*echo "<pre>";
        print_r($contacts);
        die();*/
        foreach ($contacts as $key => $contact) {
            $contactid = $contact->contactid;
            /*if ($type != "lead") {
                $email = get_staff_email($contactid);
                $this->db->where('email', $email);
                $contactid = $this->db->get('tbladdressbookemail')->row();
                echo "<pre>";
                print_r($contactid);
                die();
            }*/
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
    }
}