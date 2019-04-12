<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoice_items_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get invoice item by ID
     * @param  mixed $id
     * @return mixed - array if not passed id, object if id passed
     */
    public function get($id = '', $groupid = '')
    {
        /**
         * Modified By : Vaidehi
         * Dt : 11/30/2017
         * to get all items for given group in add/edit invoice page
         */
        if ($groupid == '') {
            $columns = $this->db->list_fields('tblitems');
            $rateCurrencyColumns = '';
            foreach ($columns as $column) {
                if (strpos($column, 'rate_currency_') !== FALSE) {
                    $rateCurrencyColumns .= $column . ',';
                }
            }

            $this->db->select('tblitems.id as itemid,rate,
                t1.taxrate as taxrate,t1.id as taxid,t1.name as taxname,
                t2.taxrate as taxrate_2,t2.id as taxid_2,t2.name as taxname_2,
                description,long_description,group_id,tblitems_groups.name as group_name,unit,profile_image,sku,line_item_category,line_item_sub_category,income_category,expense_category,markup_rate,markup_type,total_price,cost_price,net_price,is_taxable,is_custom,tax,profit,profit_margin');
            $this->db->from('tblitems');
            $this->db->join('tbltaxes t1', 't1.id = tblitems.tax AND t1.deleted = 0 AND t1.brandid =' . get_user_session(), 'left');
            $this->db->join('tbltaxes t2', 't2.id = tblitems.tax2 AND t2.deleted = 0 AND t2.brandid =' . get_user_session(), 'left');
            $this->db->join('tblitems_groups', 'tblitems_groups.id = tblitems.group_id AND tblitems_groups.deleted = 0 AND tblitems_groups.brandid =' . get_user_session(), 'left');
            $this->db->where('tblitems.deleted', '0');
            if($id==""){
                $this->db->where('tblitems.is_template', '1');
            }
            $this->db->where('tblitems.brandid', get_user_session());
            if (is_numeric($id)) {
                $this->db->where('tblitems.id', $id);

                $items = $this->db->get()->row();
                $q1 = $this->db->query('SELECT id,itemid,option_name,option_type,is_required,`order` FROM tblitems_options WHERE itemid = ' . $id . ' AND deleted=0 ORDER BY `order` ASC');
                $options = $q1->result_array();
                $options_arr = [];
                foreach ($options as $option) {
                    if ($option['option_type'] == 'single_option' || $option['option_type'] == 'dropdown' || $option['option_type'] == 'multi_select') {
                        $q1 = $this->db->query('SELECT id,option_id,choice_name,choice_cost_price,choice_rate,choice_profit,choice_profit_margin,is_default_select,`order` FROM tblitems_choices WHERE option_id = ' . $option['id'] . ' AND deleted=0 ORDER BY `order` ASC');
                        $option_choices = $q1->result_array();

                        $option['choices'] = $option_choices;
                    }
                    array_push($options_arr, $option);
                }

                if (count($options_arr) > 0) {
                    $items->item_options = $options_arr;
                }

                return $items;
            }

            return $this->db->get()->result_array();
        } else {
            /**
             * Modified By: Vaidehi
             * Dt: 03/19/2018
             * to get groups on invoice page
             */
            $this->db->where('deleted', 0);
            $this->db->where('brandid', get_user_session());
            $this->db->where('id', $groupid);

            $group = $this->db->get('tblitems_groups')->row();

            $items = [];

            if (isset($group->group_items) && $group->group_items != "") {
                $pitems = json_decode($group->group_items);
                if (count($pitems) > 0) {
                    foreach ($pitems as $pitemid => $pitem) {
                        $product = (array)$this->get($pitemid);
                        array_push($items, $product);
                    }
                }
            }

            return $items;
        }
    }

    public function get_grouped()
    {
        $items = array();
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        $this->db->where('manual_entry', '0');
        $this->db->order_by('name', 'asc');
        $groups = $this->db->get('tblitems_groups')->result_array();

        array_unshift($groups, array(
            'id' => 0,
            'name' => ''
        ));

        foreach ($groups as $group) {
            $this->db->select('*,tblitems.id as id');
            $this->db->where('group_id', $group['id']);
            $this->db->where('tblitems.brandid', get_user_session());
            $this->db->where('tblitems.deleted', '0');
            $this->db->join('tblitems_groups', 'tblitems_groups.id = tblitems.group_id and tblitems_groups.deleted=0 and tblitems_groups.brandid=' . get_user_session(), 'left');
            $this->db->order_by('description', 'asc');
            $_items = $this->db->get('tblitems')->result_array();

            if (count($_items) > 0
            ) {
                $items[$group['id']] = array();
                foreach ($_items as $i) {
                    array_push($items[$group['id']], $i);
                }
            }
        }

        return $items;
    }

    /**
     * Added By: Vaidehi
     * Dt" 03/19/2018
     * for groups
     */
    public function get_package_groups()
    {
        $items = array();
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        $this->db->where('manual_entry', '0');
        $this->db->order_by('name', 'asc');
        return $this->db->get('tblitems_groups')->result_array();
    }

    /**
     * Add new invoice item
     * @param array $data Invoice item data
     * @return boolean
     */
    public function add($data, $copy = '')
    {

        unset($data['itemid']);

        if (isset($data['package_id']) && $data['package_id'] != '') {
            $package_id = $data['package_id'];
            unset($data['package_id']);
        }
        if (isset($data['quote_id']) && $data['quote_id'] != '') {
            $quote_id = $data['quote_id'];
            unset($data['quote_id']);
        }
        if (isset($data['option']) && $data['option'] != '') {
            $options = $data['option'];
            unset($data['option']);
        }
        if (isset($data['choice']) && $data['choice'] != '') {
            $choices = $data['choice'];
            unset($data['choice']);
        }
        if (isset($data['tax2']) && $data['tax2'] == '') {
            unset($data['tax2']);
        }

        if (isset($data['group_id']) && $data['group_id'] == '') {
            $data['group_id'] = 0;
        }

        if (isset($data['is_taxable']) && $data['is_taxable'] == '') {
            $data['is_taxable'] = NULL;
        }
        if (isset($data['is_custom']) && $data['is_custom'] == '') {
            $data['is_custom'] = NULL;
        }

        $columns = $this->db->list_fields('tblitems');
        $this->load->dbforge();

        foreach ($data as $column => $itemData) {
            if (!in_array($column, $columns) && strpos($column, 'rate_currency_') !== FALSE) {
                $field = array(
                    $column => array(
                        'type' => 'decimal(11,' . get_decimal_places() . ')',
                        'null' => true,
                    )
                );
                $this->dbforge->add_column('tblitems', $field);
            }
        }

        /**
         * Added By : Sanjay
         * Dt : 01/02/2017
         */
        unset($data['tagid']);
        unset($data['option_name']);
        unset($data['option_type']);
        unset($data['is_required']);
        //unset($data['new_brand_id']);

        $data['sku'] = isset($data['sku'])?$data['sku']:'';
        $data['line_item_sub_category'] = isset($data['line_item_sub_category'])?$data['line_item_sub_category']:'';
        $data['income_category'] = isset($data['income_category'])?$data['income_category']:'';
        $data['expense_category'] = isset($data['expense_category'])?$data['expense_category']:'';
        $data['cost_price'] = isset($data['cost_price'])?$data['cost_price']:'';
        $data['rate'] = isset($data['rate'])?$data['rate']:'';
        $data['profit'] = isset($data['profit'])?$data['profit']:'';
        $data['profit_margin'] = isset($data['profit_margin'])?$data['profit_margin']:'';
        $data['is_taxable'] = isset($data['is_taxable'])?$data['is_taxable']:0;
        $data['is_custom'] = isset($data['is_custom'])?$data['is_custom']:0;
        $data['tax'] = isset($data['tax'])?$data['tax']:'';

        /**
         * Added By : Avni
         * Dt : 11/20/2017
         */

        $data['addedby'] = $this->session->userdata['staff_user_id'];
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['brandid'] = (isset($data['brandid']) ? $data['brandid'] : get_user_session());
        $this->db->insert('tblitems', $data);
        $insert_id = $this->db->insert_id();
        /**
         * Added By : Masud
         * Dt : 02/09/2017
         */
        if (isset($package_id) && $package_id > 0) {
            if (isset($quote_id) && $quote_id > 0) {
                $this->load->model('proposaltemplates_model');
                $item = (array)$this->proposaltemplates_model->getproposal_quote_by_id($quote_id);
                $pitem = $item['quote_items'];
                $pitem = json_decode($pitem, true);
                $pitem[] = array('id' => $insert_id, 'type' => 'product', 'qty' => 1, 'mdiscoun' => '');
                $pitem = json_encode($pitem);
                $item['quote_items'] = $pitem;
                $this->db->where('qid', $quote_id);
                $this->db->update('tblproposal_quotes', $item);
            } else {

                //$data['pckage_id'] = $_GET['package_id'];
                $item = (array)$this->invoice_items_model->get_group($package_id);
                $pitem = $item['group_items'];
                $pitem = json_decode($pitem, true);
                $pitem[$insert_id] = array('qty' => 1, 'subtotal' => $data['rate']);
                $pitem = json_encode($pitem);
                $item['group_items'] = $pitem;
                $item['group_price'] = $item['group_price'] + $data['rate'];
                $item['group_profit'] = $item['group_profit'] + $data['profit'];
                $item['group_cost'] = $item['group_cost'] + $data['cost_price'];

                /*echo "<pre>";
                print_r($item);*/
                $this->db->where('id', $package_id);
                $this->db->update('tblitems_groups', $item);
            }
        }
        //die('<--here');
        if (isset($options)) {
            foreach ($options as $opt => $option) {
                $op = array();
                $op['option_name'] = $option['option_name'];
                $op['option_type'] = $option['option_type'];
                $op['is_required'] = (isset($option['is_required']) ? $option['is_required'] : 0);
                $op['itemid'] = $insert_id;
                $op['brandid'] = get_user_session();
                $op['dateadded'] = date('Y-m-d H:i:s');
                $op['addedby'] = $this->session->userdata['staff_user_id'];
                $this->db->insert('tblitems_options', $op);

                $option_insert_id = $this->db->insert_id();

                if (isset($choices[$opt])) {
                    if (count($choices[$opt]) > 0) {
                        foreach ($choices[$opt] as $chi => $chv) {
                            $ch = array();
                            $ch['choice_name'] = $chv['choice_name'];
                            $ch['choice_cost_price'] = $chv['choice_cost_price'];
                            $ch['choice_rate'] = $chv['choice_rate'];
                            $ch['choice_profit'] = $chv['choice_profit'];
                            $ch['choice_profit_margin'] = $chv['choice_profit_margin'];
                            $ch['is_default_select'] = isset($chv['choice_default_selection']) ? $chv['choice_default_selection'] : "";
                            $ch['option_id'] = $option_insert_id;
                            $ch['brandid'] = get_user_session();
                            $ch['dateadded'] = date('Y-m-d H:i:s');
                            $ch['addedby'] = $this->session->userdata['staff_user_id'];
                            $this->db->insert('tblitems_choices', $ch);
                        }
                    }
                }
            }
        }
        if ($insert_id) {
            logActivity('New Invoice Item Added [ID:' . $insert_id . ', ' . $data['description'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Update invoice item
     * @param  array $data Invoice data to update
     * @return boolean
     */
    public function edit($data)
    {

        $itemid = $data['itemid'];
        unset($data['itemid']);

        $options = $data['option'];
        unset($data['option']);

        $choices = @$data['choice'];
        unset($data['choice']);

        if (isset($data['group_id']) && $data['group_id'] == '') {
            $data['group_id'] = 0;
        }

        if (isset($data['tax']) && $data['tax'] == '') {
            $data['tax'] = NULL;
        }

        if (isset($data['tax2']) && $data['tax2'] == '') {
            $data['tax2'] = NULL;
        }

        if (isset($data['is_taxable']) && $data['is_taxable'] == '') {
            $data['is_taxable'] = NULL;
        }
        if (isset($data['is_custom']) && $data['is_custom'] == '') {
            $data['is_custom'] = NULL;
        }

        $columns = $this->db->list_fields('tblitems');
        $this->load->dbforge();

        foreach ($data as $column => $itemData) {
            if (!in_array($column, $columns) && strpos($column, 'rate_currency_') !== FALSE) {
                $field = array(
                    $column => array(
                        'type' => 'decimal(11,' . get_decimal_places() . ')',
                        'null' => true,
                    )
                );
                $this->dbforge->add_column('tblitems', $field);
            }
        }

        /**
         * Added By : Sanjay
         * Dt : 01/02/2017
         */
        unset($data['tagid']);
        $data['sku'] = $data['sku'];
        $data['line_item_sub_category'] = $data['line_item_sub_category'];
        $data['income_category'] = $data['income_category'];
        $data['expense_category'] = $data['expense_category'];
        $data['cost_price'] = $data['cost_price'];
        $data['rate'] = $data['rate'];
        $data['profit'] = $data['profit'];
        $data['profit_margin'] = $data['profit_margin'];
        $data['is_taxable'] = isset($data['is_taxable']) ? $data['is_taxable'] : "";
        $data['is_custom'] = isset($data['is_custom']) ? $data['is_custom'] : "";
        $data['tax'] = $data['tax'];
        $data['updatedby'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');

        $this->db->where('id', $itemid);
        $this->db->update('tblitems', $data);

        foreach ($options as $opt => $option) {
            $op = array();
            $op['option_name'] = $option['option_name'];
            $op['option_type'] = $option['option_type'];
            $op['is_required'] = (isset($option['is_required']) ? $option['is_required'] : 0);
            $op['brandid'] = get_user_session();
            $op['dateadded'] = date('Y-m-d H:i:s');
            $op['addedby'] = $this->session->userdata['staff_user_id'];
            $op['updatedby'] = $this->session->userdata['staff_user_id'];
            $op['dateupdated'] = date('Y-m-d H:i:s');

            $item_option_id = $option['item_option_id'];

            if (isset($item_option_id) && $item_option_id > 0) {
                $this->db->where('id', $item_option_id);
                $this->db->update('tblitems_options', $op);
            } else {
                $op['itemid'] = $itemid;
                $this->db->insert('tblitems_options', $op);
                $item_option_id = $this->db->insert_id();
            }

            if (count($choices[$opt]) > 0) {
                foreach ($choices[$opt] as $chi => $chv) {
                    $ch = array();
                    $ch['choice_name'] = $chv['choice_name'];
                    $ch['choice_cost_price'] = $chv['choice_cost_price'];
                    $ch['choice_rate'] = $chv['choice_rate'];
                    $ch['choice_profit'] = $chv['choice_profit'];
                    $ch['choice_profit_margin'] = $chv['choice_profit_margin'];
                    $ch['is_default_select'] = isset($chv['choice_default_selection']) ? $chv['choice_default_selection'] : "";
                    $ch['brandid'] = get_user_session();
                    $ch['dateadded'] = date('Y-m-d H:i:s');
                    $ch['addedby'] = $this->session->userdata['staff_user_id'];
                    $ch['updatedby'] = $this->session->userdata['staff_user_id'];
                    $ch['dateupdated'] = date('Y-m-d H:i:s');

                    if (isset($chv['item_choice_id']) && $chv['item_choice_id'] > 0) {
                        $this->db->where('id', $chv['item_choice_id']);
                        $this->db->update('tblitems_choices', $ch);
                    } else {
                        $ch['option_id'] = $item_option_id;
                        $this->db->insert('tblitems_choices', $ch);
                    }
                }
            }
        }

        if ($this->db->affected_rows() > 0) {
            logActivity('Invoice Item Updated [ID: ' . $itemid . ', ' . $data['description'] . ']');
            return true;
        }

        return false;
    }

    public function search($q)
    {
        $this->db->select('rate, id, description as name, long_description as subtext');
        $this->db->like('description', $q);
        $this->db->or_like('long_description', $q);

        $items = $this->db->get('tblitems')->result_array();

        foreach ($items as $key => $item) {
            $items[$key]['subtext'] = strip_tags(mb_substr($item['subtext'], 0, 200)) . '...';
            $items[$key]['name'] = '(' . _format_number($item['rate']) . ') ' . $item['name'];
        }

        return $items;
    }

    /**
     * Delete invoice item
     * @param  mixed $id
     * @return boolean
     */
    public function delete($id)
    {
        /**
         * Added By : Masud
         * Dt : 02/19/2018
         * For validation : not to delete if available in package.
         */
        $in_package = 0;

        $query = 'SELECT `group_items` FROM `tblitems_groups` WHERE `group_items` !="" AND `group_items` !="null" AND `brandid` = "' . get_user_session() . '" AND `deleted` = 0';
        $results = $this->db->query($query);
        $results = $results->result();
        foreach ($results as $result) {
            $result = json_decode($result->group_items, true);
            if (isset($result[$id])) {
                $in_package += 1;
            }
        }

        $pquery = 'SELECT `quote_items` FROM `tblproposal_quotes` WHERE `quote_items` !="" AND `quote_items` !="null" AND `deleted` = 0';
        $presults = $this->db->query($pquery);
        $presults = $presults->result();
        foreach ($presults as $p_result) {
            $pr_result = json_decode($p_result->quote_items, true);
            foreach ($pr_result as $result) {
                if ($result['id'] == $id) {
                    $in_package += 1;
                }
            }
        }

        if ($in_package > 0) {
            return array(
                'referenced' => true
            );
        }

        /**
         * Added By : Avni
         * Dt : 11/20/2017
         * For soft deleting so that reference in existing invoice is displayed
         */
        $current = $this->get($id);

        $data['deleted'] = 1;
        $data['updatedby'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update('tblitems', $data);
        //$this->db->delete('tblitems');
        if ($this->db->affected_rows() > 0) {
            logActivity('Invoice Item Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    public function get_groups()
    {
        $this->db->order_by('name', 'asc');
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        return $this->db->get('tblitems_groups')->result_array();
    }

    public function get_group($id)
    {
        $this->db->where('deleted', '0');
        $this->db->where('id', $id);
        return $this->db->get('tblitems_groups')->row();

    }

    public function get_group_col($id = "", $page_type)
    {
        //$this->db->order_by('name', 'asc');
        $this->db->where('brandid', get_user_session());
        $this->db->where('addedby', $this->session->userdata['staff_user_id']);
        $this->db->where('deleted', '0');
        $this->db->where('page_type', $page_type);
        if (isset($id) && $id > 0) {
            $this->db->where('page_id', $id);
        }
        return $this->db->get('tblcolumn_settings')->row('column_name');

    }

    public function add_group($data)
    {
        /**
         * Added By : Vaidehi
         * Dt : 12/01/2017
         * to check line group name exists in db or not
         */
        $this->db->where('name', $this->input->post('name'));
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        $total_rows = $this->db->count_all_results('tblitems_groups');

        if ($total_rows <= 0) {
            $data['addedby'] = $this->session->userdata['staff_user_id'];
            $data['dateadded'] = date('Y-m-d H:i:s');
            $data['brandid'] = get_user_session();

            $this->db->insert('tblitems_groups', $data);
            $insert_id = $this->db->insert_id();
            logActivity('Items Group Created [Name: ' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    public function edit_group($data, $id)
    {
        $this->db->where('name', $this->input->post('name'));
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        $this->db->where('id != ', $id);
        $total_rows = $this->db->count_all_results('tblitems_groups');

        if ($total_rows <= 0) {
            /*echo "<pre>";
            print_r($data);
            die('<--here if');*/

            $data['updatedby'] = $this->session->userdata['staff_user_id'];
            $data['dateupdated'] = date('Y-m-d H:i:s');

            $this->db->where('id', $id);
            $this->db->update('tblitems_groups', $data);
            if ($this->db->affected_rows() > 0) {
                logActivity('Items Group Updated [Name: ' . $data['name'] . ']');

                return true;
            }
        }
        /*echo "<pre>";
        print_r($data);
        die('<--here after');*/
        return false;
    }

    public function delete_group($id)
    {
        $in_package = 0;

        $pquery = 'SELECT `quote_items` FROM `tblproposal_quotes` WHERE `quote_items` !="" AND `quote_items` !="null" AND `deleted` = 0';
        $presults = $this->db->query($pquery);
        $presults = $presults->result();
        foreach ($presults as $p_result) {
            $pr_result = json_decode($p_result->quote_items, true);
            foreach ($pr_result as $result) {
                if ($result['id'] == $id && $result['type'] == 'product') {
                    $in_product += 1;
                }
                if ($result['id'] == $id && $result['type'] == 'package') {
                    $in_package += 1;
                }
            }
        }

        if ($in_package > 0) {
            return array(
                'referenced' => true
            );
        }

        /**
         * Added By : Avni
         * Dt : 11/29/2017
         * For soft deleting so that reference in existing invoice is displayed
         */
        $current = $this->get('', $id);

        if ($current) {
            $data['deleted'] = 1;
            $data['updatedby'] = $this->session->userdata['staff_user_id'];
            $data['dateupdated'] = date('Y-m-d H:i:s');

            $this->db->where('id', $id);
            $this->db->update('tblitems_groups', $data);

            logActivity('Item Group Deleted');

            return true;
        }

        return false;
    }

    public function add_product_service($data)
    {
        /**
         * Added By : Sanjay
         * Dt : 01/01/2018
         * to check line group name exists in db or not
         */
        $this->db->where('name', $this->input->post('name'));
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        $total_rows = $this->db->count_all_results('tblitems_groups');

        if ($total_rows <= 0) {
            $data['addedby'] = $this->session->userdata['staff_user_id'];
            $data['dateadded'] = date('Y-m-d H:i:s');
            $data['brandid'] = get_user_session();

            $this->db->insert('tblproductcategory', $data);
            logActivity('Product & Services Group Created [Name: ' . $data['name'] . ']');

            return $this->db->insert_id();
        }

        return false;
    }

    public function get_product_service_groups()
    {
        $this->db->order_by('name', 'asc');
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        return $this->db->get('tblproductcategory')->result_array();
    }

    public function edit_product_service_group($data, $id)
    {
        $this->db->where('name', $this->input->post('name'));
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        $this->db->where('id != ', $id);
        $total_rows = $this->db->count_all_results('tblproductcategory');

        if ($total_rows <= 0) {
            $data['updatedby'] = $this->session->userdata['staff_user_id'];
            $data['dateupdated'] = date('Y-m-d H:i:s');

            $this->db->where('id', $id);
            $this->db->update('tblproductcategory', $data);
            if ($this->db->affected_rows() > 0) {
                logActivity('Product & Services Group Updated [Name: ' . $data['name'] . ']');

                return true;
            }
        }

        return false;
    }

    public function delete_pro_service_group($id)
    {
        $this->db->where('id', $id);
        $group = $this->db->get('tblitems')->row();

        if ($group) {
            $this->db->where('group_id', $id);
            $this->db->update('tblitems', array(
                'group_id' => 0
            ));

            /**
             * Added By : Avni
             * Dt : 11/29/2017
             * For soft deleting so that reference in existing invoice is displayed
             */
            $current = $this->get($id);

            $data['deleted'] = 1;
            $data['updatedby'] = $this->session->userdata['staff_user_id'];
            $data['dateupdated'] = date('Y-m-d H:i:s');

            $this->db->where('id', $id);
            $this->db->update('tblproductcategory', $data);

            //$this->db->delete('tblitems_groups');

            logActivity('Product & Services Group Deleted ' . $id);

            return true;
        }

        return false;
    }

    /*
    ** Added by Sanjay on 02/25/2018 Start
    ** For getting all product & service category
    */
    public function get_line_item_category()
    {
        $this->db->order_by('dateadded', 'desc');
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        return $this->db->get('tbllineitem_category')->result_array();
    }

    /*
    ** Added by Sanjay on 02/25/2018 Start
    ** For adding product & service category
    */
    public function add_line_item_category($data)
    {
        unset($data['id']);
        if (isset($data['brandid'])) {
            $data['brandid'] = $data['brandid'];
        } else {
            $data['brandid'] = get_user_session();
        }

        $this->db->where('name', $this->input->post('name'));
        $this->db->where('brandid', $data['brandid']);
        $this->db->where('deleted', '0');
        $total_rows = $this->db->count_all_results('tbllineitem_category');

        if ($total_rows <= 0) {
            $data['addedby'] = $this->session->userdata['staff_user_id'];
            $data['dateadded'] = date('Y-m-d H:i:s');

            $this->db->insert('tbllineitem_category', $data);

            $insert_id = $this->db->insert_id();

            logActivity('Product & Services Category Added [Name: ' . $data['name'] . ', ID: ' . $insert_id . ']');

            return $insert_id;
        }

        return false;
    }


    /*
    ** Added by Sanjay on 02/25/2018 Start
    ** For update product & service category
    */
    public function edit_line_item_category($data, $id)
    {
        $this->db->where('name', $data['name']);
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        $this->db->where('id != ', $id);
        $total_rows = $this->db->count_all_results('tbllineitem_category');

        if ($total_rows <= 0) {
            $data['name'] = $this->input->post('name');
            $data['updatedby'] = $this->session->userdata['staff_user_id'];
            $data['dateupdated'] = date('Y-m-d H:i:s');

            $this->db->where('id', $id);
            $this->db->update('tbllineitem_category', $data);
            if ($this->db->affected_rows() > 0) {
                logActivity('Product & Services Category Updated [Name: ' . $data['name'] . ' ID: ' . $data['id'] . ']');

                return $id;
            }
        }

        return false;
    }


    /*
    ** Added by Sanjay on 02/25/2018 Start
    ** For delete product & service category
    */
    public function delete_line_item_category($id)
    {
        /**
         * Modified By: Vaidehi
         * Dt: 02/05/2018
         * to check whether subcategory exists or not
         */
        $this->db->where('parent_id', $id);
        $this->db->where('deleted', 0);
        $category = $this->db->get('tbllineitem_subcategory')->row();
        if (!empty($category)) {
            return array(
                'referenced' => true
            );
        }

        $affectedRows = 0;
        $data['deleted'] = 1;
        $data['updatedby'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update('tbllineitem_category', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            logActivity('Product & Services Category Deleted');

            return true;
        }
        return false;
    }

    /*
    ** Added by Sanjay on 02/25/2018 Start
    ** For getting all product & service sub category
    */
    public function get_line_item_sub_category()
    {
        $this->db->order_by('dateadded', 'desc');
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        return $this->db->get('tbllineitem_subcategory')->result_array();
    }

    /*
    ** Added by Sanjay on 02/25/2018 Start
    ** For adding product & service sub category
    */
    public function add_line_item_sub_category($data)
    {
        if (isset($data['brandid'])) {
            $data['brandid'] = $data['brandid'];
        } else {
            $data['brandid'] = get_user_session();
        }

        $this->db->where('parent_id', $data['parent_id']);
        $this->db->where('name', $data['name']);
        $this->db->where('brandid', $data['brandid']);
        $this->db->where('deleted', '0');
        $total_rows = $this->db->count_all_results('tbllineitem_subcategory');

        if ($total_rows <= 0) {
            $data['addedby'] = $this->session->userdata['staff_user_id'];
            $data['dateadded'] = date('Y-m-d H:i:s');

            $this->db->insert('tbllineitem_subcategory', $data);

            $insert_id = $this->db->insert_id();

            logActivity('Product & Services Sub Category Added [Name: ' . $data['name'] . ', ID: ' . $insert_id . ']');

            return $insert_id;
        }

        return false;
    }

    /*
    ** Added by Sanjay on 02/25/2018 Start
    ** For update product & service sub category
    */
    public function edit_line_item_sub_category($data)
    {
        $this->db->where('name', $data['name']);
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        $this->db->where('id != ', $data['id']);
        $total_rows = $this->db->count_all_results('tbllineitem_subcategory');

        if ($total_rows <= 0) {
            $data['updatedby'] = $this->session->userdata['staff_user_id'];
            $data['dateupdated'] = date('Y-m-d H:i:s');

            $this->db->where('id', $data['id']);
            $this->db->update('tbllineitem_subcategory', $data);
            if ($this->db->affected_rows() > 0) {
                logActivity('Product & Services Sub Category Created [Name: ' . $data['name'] . ']');
            }

            return $data['id'];
        }

        return false;
    }


    /*
    ** Added by Sanjay on 02/25/2018 Start
    ** For delete product & service sub category
    */
    public function delete_line_item_sub_category($id)
    {
        $this->db->where('id', $id);
        $group = $this->db->get('tblitems')->row();

        if ($group) {

            $this->db->where('group_id', $id);
            $this->db->update('tblitems', array(
                'group_id' => 0
            ));

            $current = $this->get($id);

            $data['deleted'] = 1;
            $data['updatedby'] = $this->session->userdata['staff_user_id'];
            $data['dateupdated'] = date('Y-m-d H:i:s');

            $this->db->where('id', $id);
            $this->db->update('tbllineitem_subcategory', $data);

            logActivity('Product & Services Sub Category Deleted ' . $id);

            return true;
        }

        return false;
    }


    // For getting sub category option list
    public function getSubcatOptions()
    {
        if (!is_null($this->option_id)) {
            $this->db->select('id, name');
            $this->db->where('parent_id', $this->option_id);
            $suboptions = $this->db->get('tbllineitem_subcategory');
            if ($suboptions->num_rows() > 0) {
                $suboptions_arr;
                foreach ($suboptions->result() as $suboption) {
                    $suboptions_arr[$suboption->id] = $suboption->name;
                }

                return $suboptions_arr;
            }
        }

        return;
    }

    /*
    ** Added by Sanjay on 02/25/2018 Start
    ** For adding category data to product category table
    */
    public function add_category_data()
    {
        $data['name'] = $this->input->post('line_item_cat_name');
        $data['li_category'] = $this->input->post('main_li_list');
        $data['li_sub_category'] = $this->input->post('suboptions');
        $data['brandid'] = get_user_session();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedby'] = $this->session->userdata['staff_user_id'];
        $this->db->insert('tblproductcategory', $data);

        return $this->db->insert_id();
    }

    /*
    ** Added by Sanjay on 02/25/2018 Start
    ** For retrive category data from line item sub category table
    */
    function get_line_item_category_list($id = '')
    {
        $this->db->order_by('parent_category', 'asc');
        $this->db->select('tbllineitem_subcategory.*, (SELECT name FROM tbllineitem_category WHERE id = `tbllineitem_subcategory`.`parent_id` ORDER BY `name`) as parent_category');
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');

        if ($id != '') {
            $this->db->where('deleted', $id);
            return $this->db->get('tbllineitem_subcategory')->row();
        }
        return $this->db->get('tbllineitem_subcategory')->result_array();
    }

    /*
    ** Added by Masud on 03/01/2019 Start
    ** For retrive category data from line item sub category table
    */
    function get_line_item_sub_category_list($id = '')
    {
        $this->db->order_by('parent_category', 'asc');
        $this->db->select('tbllineitem_subcategory.*, (SELECT name FROM tbllineitem_category WHERE id = `tbllineitem_subcategory`.`parent_id` ORDER BY `name`) as parent_category');
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');

        if ($id != '') {
            $this->db->where('deleted', $id);
            return $this->db->get('tbllineitem_subcategory')->row();
        }
        return $this->db->get('tbllineitem_subcategory')->result_array();
    }
    /*
    ** Added by Masud on 03/01/2019 Start
    ** For retrive category data from line item sub category table
    */
    function get_line_item_category_lists()
    {
        //$this->db->order_by('parent_category', 'asc');
        $this->db->select('*');
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        return $this->db->get('tbllineitem_category')->result_array();
    }

    /*
    ** Added by Sanjay on 02/25/2018 Start
    ** For Deleting category-subcategory combination from category-subcategory listing
    */
    function delete_category_combination($id)
    {
        // Check first if subcategory is used in table
        if (is_reference_in_table('line_item_sub_category', 'tblitems', $id)) {
            return array(
                'referenced' => true
            );
        }

        $affectedRows = 0;
        $data['deleted'] = 1;
        $data['updatedby'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update('tbllineitem_subcategory', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            logActivity('Product & Services Sub Category Deleted');

            return true;
        }
        return false;

    }

    function add_product_services()
    {
        $data['itemid'] = $this->input->post('tagid');
        $data['option_name'] = $this->input->post('option_name');
        $data['option_type'] = $this->input->post('option_type');


        $data['brandid'] = get_user_session();
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedby'] = $this->session->userdata['staff_user_id'];
        $this->db->insert('tblitems_options', $data);
    }

    //For deleting option from selected product & services
    function delete_options($id)
    {
        $data['deleted'] = 1;
        $data['updatedby'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $this->db->where('id', $this->input->post('name'));
        $this->db->update('tblitems_options', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            logActivity('Produce & Services options Deleted');

            return true;
        }
        return false;

    }

    /**
     * Added By: Sanjay
     * Dt:
     * to duplicate product in same or other brand
     */
    function make_duplicate_pro_service($data)
    {
        /**
         * Modified By: Vaidehi
         * Dt: 02/04/2018
         * to get correct name and sku while duplicating product and pass return value to controller
         */

        $brandid = $data['brandid'];

        $duplicate_record_id = $data['duplicate_record_id'];

        $res = $this->get($duplicate_record_id);

        $data = (array)$res;

        unset($data['id']);
        unset($data['dateadded']);
        unset($data['addedby']);
        unset($data['taxrate']);
        unset($data['taxid']);
        unset($data['taxname']);
        unset($data['taxrate_2']);
        unset($data['taxid_2']);
        unset($data['taxname_2']);
        unset($data['group_name']);
        unset($data['item_options']);
        unset($data['taxrate']);

        //get name of selected product
        $curr_pro = explode("_", $res->description);
        $temp = $curr_pro[0];

        //get new name for same brand or existing brand
        $query = 'SELECT `description` FROM `tblitems` WHERE `description` LIKE "' . $temp . '%" AND `brandid` = "' . $brandid . '" AND `deleted` = 0 ORDER BY `id` DESC LIMIT 0,1';
        $result = $this->db->query($query);
        $rows = $result->row();
        if (!empty($rows->description)) {
            $old_desc = explode("_", $rows->description);
            $curr_pointer = @$old_desc[1] + 1;
            if (isset($old_desc[1]) && $old_desc[1] > 0) {
                $data['description'] = $old_desc[0] . '_' . $curr_pointer;
            } else {
                $data['description'] = $old_desc[0] . '_1';
            }
        } else {
            $data['description'] = $res->description;
        }

        //get sku of selected product
        $curr_sku_new = explode("_", $res->sku);
        $temp_sku = $curr_sku_new[0];

        //get new name for same brand or existing brand
        $query = 'SELECT `sku` FROM `tblitems` WHERE `sku` LIKE "' . $temp_sku . '%" AND `brandid` = "' . $brandid . '" AND `deleted` = 0 ORDER BY `id` DESC LIMIT 0,1';
        $result = $this->db->query($query);
        $rows_sku = $result->row();

        if (!empty($rows_sku->sku)) {
            $old_sku = explode("_", $rows_sku->sku);
            $curr_sku = @$old_sku[1] + 1;
            if (isset($old_sku[1]) && $old_sku[1] > 0) {
                $data['sku'] = $old_sku[0] . '_' . $curr_sku;
            } else {
                $data['sku'] = $old_sku[0] . '_1';
            }
        } else {
            $data['sku'] = $res->sku;
        }

        /**
         * Added By: Vaidehi
         * Dt: 02/07/2018
         * to copy income categoy, expense category, category, sub category and tax
         */
        //check if income category exists or not
        if ($res->income_category != '' || $res->income_category != NULL) {
            $income_query = $this->db->query('SELECT `id`, `name` FROM `tblincome_category` WHERE `id` = ' . $res->income_category . ' AND `deleted` = 0 AND brandid = ' . get_user_session());
            $response = $income_query->row();
            $categoryexist = $this->check_category_exists($response->name, '', $brandid, 'tblincome_category');
            if (empty($categoryexist)) {
                $income_data['name'] = $response->name;
                $income_data['brandid'] = $brandid;

                $income_category = $this->save_income_category($income_data);

                $data['income_category'] = $income_category;
            } else {
                $data['income_category'] = $categoryexist->id;
            }
        }

        //check if expense category exists or not
        if ($res->expense_category != '' || $res->expense_category != NULL) {
            $expense_query = $this->db->query('SELECT `id`, `name` FROM `tblexpense_category` WHERE `id` = ' . $res->expense_category . ' AND `deleted` = 0 AND brandid = ' . get_user_session());
            $response = $expense_query->row();
            $categoryexist = $this->check_category_exists($response->name, '', $brandid, 'tblexpense_category');
            if (empty($categoryexist)) {
                $expense_data['name'] = $response->name;
                $expense_data['brandid'] = $brandid;

                $expense_category = $this->save_expense_category($expense_data);
                $data['expense_category'] = $expense_category;
            } else {
                $data['expense_category'] = $categoryexist->id;
            }
        }

        //check if sub category exists or not
        if (!empty($res->line_item_sub_category)) {
            $subcategory_query = $this->db->query('SELECT `id`, `name`, `parent_id` FROM `tbllineitem_subcategory` WHERE `id` = ' . $res->line_item_sub_category . ' AND `deleted` = 0 AND brandid = ' . get_user_session());
            $response = $subcategory_query->row();
            $subcategoryexist = $this->check_category_exists($response->name, '', $brandid, 'tbllineitem_subcategory');
            if (empty($subcategoryexist)) {
                //check if category exists or not
                $category_query = $this->db->query('SELECT `id`, `name` FROM `tbllineitem_category` WHERE `id` = ' . $response->parent_id . ' AND `deleted` = 0 AND brandid = ' . get_user_session());
                $cat_response = $category_query->row();
                $categoryexist = $this->check_category_exists($cat_response->name, '', $brandid, 'tbllineitem_category');
                if (empty($categoryexist)) {
                    $category_data['name'] = $cat_response->name;
                    $category_data['brandid'] = $brandid;

                    $category = $this->add_line_item_category($category_data);

                } else {
                    $category = $categoryexist->id;
                }

                $subcategory_data['name'] = $response->name;
                $subcategory_data['parent_id'] = $category;
                $subcategory_data['brandid'] = $brandid;

                $sub_category = $this->add_line_item_sub_category($subcategory_data);

                $data['line_item_sub_category'] = $sub_category;
            } else {
                $data['line_item_sub_category'] = $subcategoryexist->id;
            }
        }

        //check if tax exists or not
        if ($res->tax != 0) {
            $tax_query = $this->db->query('SELECT `id`, `name`, `taxrate` FROM `tbltaxes` WHERE `id` = ' . $res->tax . ' AND `deleted` = 0 AND brandid = ' . get_user_session());
            $response = $tax_query->row();
            $taxres = $this->db->query('SELECT `id`, `name`, `taxrate` FROM `tbltaxes` WHERE `name` = "' . $response->name . '" AND `deleted` = 0 AND brandid = ' . $brandid);
            $taxexist = $taxres->row();
            if (empty($taxexist)) {
                $taxes_data['name'] = $response->name;
                $taxes_data['brandid'] = $brandid;
                $taxes_data['created_by'] = $this->session->userdata['staff_user_id'];
                $taxes_data['datecreated'] = date('Y-m-d H:i:s');
                $insert_id = $this->db->insert('tbltaxes', $taxes_data);
                $data['tax'] = $insert_id;
            } else {
                $data['tax'] = $taxexist->id;
            }
        }

        //copy all options
        $q1 = $this->db->query('SELECT id,option_name,option_type,is_required FROM tblitems_options WHERE itemid = ' . $duplicate_record_id . ' AND deleted=0');
        $data['option'] = $q1->result_array();
        $options_arr = [];
        foreach ($data['option'] as $option) {
            if ($option['option_type'] == 'single_option' || $option['option_type'] == 'dropdown' || $option['option_type'] == 'multi_select') {

                //copy all choices
                $q1 = $this->db->query('SELECT choice_name,choice_cost_price,choice_rate,choice_profit,choice_profit_margin,is_default_select FROM tblitems_choices WHERE option_id = ' . $option['id']);
                $option_choices = $q1->result_array();
                $option['choices'] = $option_choices;
            }

            if (!empty($option['choices'])) {
                array_push($options_arr, $option['choices']);
            }
        }

        $data['choice'] = $options_arr;

        $data['brandid'] = $brandid;
        $insert_id = $this->add($data, $duplicate_record_id);

        if ($insert_id) {
            return $insert_id;
        }

        return false;
    }

    function update_options_order($data)
    {
        $id = $data['option_id'];
        unset($data['option_id']);
        $this->db->where('id', $id);
        $this->db->update('tblitems_options', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function update_choices_order($data)
    {
        $id = $data['id'];
        unset($data['id']);
        $this->db->where('id', $id);
        $this->db->update('tblitems_choices', $data);

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    function delete_option($id)
    {
        $data['deleted'] = 1;
        $this->db->where('id', $id);
        $this->db->update('tblitems_options', $data);
        if ($this->db->affected_rows() > 0) {
            $this->db->where('option_id', $id);
            $this->db->update('tblitems_choices', $data);

            return $this->db->affected_rows();
        }
    }

    function delete_choice($id)
    {
        $data['deleted'] = 1;
        $this->db->where('id', $id);
        $this->db->update('tblitems_choices', $data);
        return $this->db->affected_rows();
    }


    /*
    ** Added by Sanjay on 02-05-2018 Start
    ** 
    * to save display column setting for product & services listing page
    */
    function save_display_columns()
    {
        $col_set = $this->input->post('display_option');
        $all_column = implode(",", $col_set);

        $this->db->where('page_type', $this->input->post('page_type'));
        $this->db->where('brandid', get_user_session());
        $this->db->where('updatedby', $this->session->userdata['staff_user_id']);
        $total_rows = $this->db->count_all_results('tblcolumn_settings');

        if ($total_rows > 0) {
            $data['column_name'] = $all_column;
            $data['page_type'] = $this->input->post('page_type');
            $data['dateupdated'] = date('Y-m-d H:i:s');
            $data['updatedby'] = $this->session->userdata['staff_user_id'];
            $this->db->where('brandid', $this->input->post('brand_id'));
            $this->db->where('updatedby', $this->input->post('staff_id'));
            $this->db->update('tblcolumn_settings', $data);
        } else {
            $data['column_name'] = $all_column;
            $data['brandid'] = get_user_session();
            $data['page_type'] = $this->input->post('page_type');
            $data['dateadded'] = date('Y-m-d H:i:s');
            $data['addedby'] = $this->session->userdata['staff_user_id'];
            $data['dateupdated'] = date('Y-m-d H:i:s');
            $data['updatedby'] = $this->session->userdata['staff_user_id'];
            $this->db->insert('tblcolumn_settings', $data);
        }
        return $total_rows;

    }

    /*
    ** Added by Sanjay on 02-05-2018 Start
    ** 
    * to retrieve column setting data for product & services listing page
    */
    function get_column_setting()
    {
        $this->db->select('column_name');
        $this->db->where('brandid', get_user_session());
        $this->db->where('updatedby', $this->session->userdata['staff_user_id']);
        $this->db->where('deleted', '0');
        return $this->db->get('tblcolumn_settings')->row();
    }

    /*
        added by Masud 30-01-2018 Start
    */

    function get_item_option($option_id)
    {
        $this->db->select('*');
        $this->db->where('id', $option_id);
        return $this->db->get('tblitems_options')->row_array();
    }

    function get_item_option_choices($option_id)
    {

        $this->db->select('*');
        $this->db->where('option_id', $option_id);
        return $this->db->get('tblitems_choices')->result('array');
    }

    function get_item_option_choice($id)
    {

        $this->db->select('*');
        $this->db->where('id', $id);
        return $this->db->get('tblitems_choices')->row_array();
    }

    function add_item_option($data)
    {
        unset($data['id']);
        $data['addedby'] = $this->session->userdata['staff_user_id'];
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['updatedby'] = 0;
        $data['dateupdated'] = "";
        $data['option_name'] = $data['option_name'] . "-copy";

        $this->db->insert('tblitems_options', $data);
        $option_insert_id = $this->db->insert_id();

        return $option_insert_id;
    }

    function add_item_option_choice($data, $option_id, $type = "")
    {

        unset($data['id']);
        $data['addedby'] = $this->session->userdata['staff_user_id'];
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['updatedby'] = 0;
        $data['dateupdated'] = "";
        if ($type == 'choice') {
            $data['choice_name'] = $data['choice_name'] . "-copy";
        }
        $data['option_id'] = $option_id;
        $this->db->insert('tblitems_choices', $data);
    }
    /*
        added by Masud 30-01-2018 End
    */

    /*
    ** Added by Sanjay on 02-05-2018 Start
    ** to save income category
    */
    function save_income_category($data)
    {
        if (isset($data['brandid'])) {
            $data['brandid'] = $data['brandid'];
        } else {
            $data['brandid'] = get_user_session();
        }

        $categoryexist = $this->check_category_exists($data['name'], 0, $data['brandid'], 'tblincome_category');

        if (empty($categoryexist)) {
            unset($data['id']);

            $data['dateadded'] = date('Y-m-d H:i:s');
            $data['addedby'] = $this->session->userdata['staff_user_id'];
            $this->db->insert('tblincome_category', $data);

            $insert_id = $this->db->insert_id();

            logActivity('Income category Added [Name: ' . $data['name'] . ', ID: ' . $insert_id . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Added by Sanjay on 02-05-2018
     * Edit income category from database
     */
    function edit_income_category($data)
    {
        $categoryexist = $this->check_category_exists($data['name'], $data['id'], get_user_session(), 'tblincome_category');

        if (empty($categoryexist)) {
            $data['brandid'] = get_user_session();
            $data['updatedby'] = $this->session->userdata['staff_user_id'];
            $data['dateupdated'] = date('Y-m-d H:i:s');
            $this->db->where('id', $data['id']);
            $this->db->update('tblincome_category', $data);

            logActivity('Income category Updated [Name: ' . $data['name'] . ', ID: ' . $data['id'] . ']');

            return $data['id'];
        }

        return false;
    }

    /*
    ** Added by Sanjay on 02-05-2018 
    ** Delete income category from database
    */
    function delete_income_category($id)
    {
        if (is_reference_in_table('income_category', 'tblitems', $id)) {
            return array(
                'referenced' => true
            );
        }
        $data['deleted'] = 1;
        $data['updatedby'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update('tblincome_category', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Income category Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /*
    ** Added by Sanjay on 02-06-2018 
    ** Retrieve all income category
    */
    function get_income_categories()
    {
        $this->db->order_by('name', 'asc');
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        return $this->db->get('tblincome_category')->result_array();
    }

    /*
    ** Added by Sanjay on 02-06-2018 Start
    ** To save expense category
    */
    function save_expense_category($data)
    {
        if (isset($data['brandid'])) {
            $data['brandid'] = $data['brandid'];
        } else {
            $data['brandid'] = get_user_session();
        }

        $categoryexist = $this->check_category_exists($data['name'], 0, $data['brandid'], 'tblexpense_category');

        if (empty($categoryexist)) {
            $data['dateadded'] = date('Y-m-d H:i:s');
            $data['addedby'] = $this->session->userdata['staff_user_id'];
            $this->db->insert('tblexpense_category', $data);

            $insert_id = $this->db->insert_id();

            logActivity('Expense category Added [Name: ' . $data['name'] . ', ID: ' . $insert_id . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Added by Sanjay on 02-06-2018
     * Edit expense category from database
     */
    function edit_expense_category($data)
    {
        $categoryexist = $this->check_category_exists($data['name'], $data['id'], get_user_session(), 'tblexpense_category');

        if (empty($categoryexist)) {
            $data['brandid'] = get_user_session();
            $data['updatedby'] = $this->session->userdata['staff_user_id'];
            $data['dateupdated'] = date('Y-m-d H:i:s');
            $this->db->where('id', $data['id']);
            $this->db->update('tblexpense_category', $data);

            logActivity('Expense category Updated [Name: ' . $data['name'] . ', ID: ' . $data['id'] . ']');

            return $data['id'];
        }

        return false;
    }

    /*
    ** Added by Sanjay on 02-06-2018 
    ** Delete expense category from database
    */
    function delete_expense_category($id)
    {
        if (is_reference_in_table('expense_category', 'tblitems', $id)) {
            return array(
                'referenced' => true
            );
        }
        $data['deleted'] = 1;
        $data['updatedby'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update('tblexpense_category', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Expense category Deleted [ID: ' . $id . ']');
            return true;
        }
        return false;
    }

    /*
    ** Added by Sanjay on 02-06-2018 
    ** Retrieve all expense category
    */
    function get_expense_categories()
    {
        $this->db->order_by('name', 'asc');
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        return $this->db->get('tblexpense_category')->result_array();
    }

    /**
     * Added By: Vaidehi
     * Dt: 02/07/2018
     * to chekc if category / sub-category / income-category / expense-category name exists or not
     */
    function check_category_exists($name, $id = '', $brandid, $table)
    {
        $where = array('name =' => $name, 'deleted =' => 0);

        if (isset($id) && $id > 0) {
            $where ['id != '] = $id;
        }

        if (isset($brandid) && $brandid > 0) {
            $where['brandid = '] = $brandid;
        }

        return $this->db->where($where)->get($table)->row();
    }

    /*
    ** Added by Masud on 02-05-2018 Start
    ** 
    * to save display column setting for package
    */
    function save_package_display_columns($data)
    {
        $col_set = $data['display_option'];
        $all_column = implode(",", $col_set);
        unset($data['brand_id']);
        unset($data['staff_id']);
        unset($data['display_option']);


        $this->db->where('brandid', get_user_session());
        $this->db->where('addedby', $this->session->userdata['staff_user_id']);
        if (isset($data['page_id']) && $data['page_id'] > 0) {
            $this->db->where('page_id', $data['page_id']);
        }
        if (isset($data['page_type']) && $data['page_type'] != "") {
            $this->db->where('page_type', $data['page_type']);
        }
        $total_rows = $this->db->count_all_results('tblcolumn_settings');
        /*echo $total_rows;
        echo $this->db->last_query();
        die('<--here');*/
        if ($total_rows > 0) {
            $data['column_name'] = $all_column;
            $data['dateupdated'] = date('Y-m-d H:i:s');
            $data['updatedby'] = $this->session->userdata['staff_user_id'];
            $this->db->where('brandid', $this->input->post('brand_id'));
            $this->db->where('updatedby', $this->input->post('staff_id'));
            if (isset($data['page_id']) && $data['page_id'] > 0) {
                $this->db->where('page_id', $data['page_id']);
            }
            if (isset($data['page_type']) && $data['page_type'] != "") {
                $this->db->where('page_type', $data['page_type']);
            }
            $this->db->update('tblcolumn_settings', $data);
            return $all_column;
        } else {
            $data['column_name'] = $all_column;
            $data['brandid'] = get_user_session();
            $data['dateadded'] = date('Y-m-d H:i:s');
            $data['addedby'] = $this->session->userdata['staff_user_id'];
            $data['dateupdated'] = date('Y-m-d H:i:s');
            $data['updatedby'] = $this->session->userdata['staff_user_id'];
            $this->db->insert('tblcolumn_settings', $data);
            return $all_column;
        }

    }

    /**
     * Added By: Masud
     * Dt: 02-13-2018
     * to duplicate package in same or other brand
     */
    function duplicate_group($data)
    {
        $brandid = $data['brandid'];

        $duplicate_record_id = $data['duplicate_record_id'];
        $duplicate_by_brand = $data['duplicate_by_brand'];

        $res = $this->get_group($duplicate_record_id);
        $data = (array)$res;
        unset($data['id']);
        unset($data['taxrate']);
        unset($data['taxid']);
        unset($data['taxname']);
        unset($data['taxrate_2']);
        unset($data['taxid_2']);
        unset($data['taxname_2']);
        unset($data['group_name']);
        unset($data['item_options']);
        unset($data['taxrate']);
        unset($data['dateupdated']);
        unset($data['updatedby']);
        $data['addedby'] = $this->session->userdata['staff_user_id'];
        $data['dateadded'] = date('Y-m-d H:i:s');
        //get name of selected product
        $curr_pro = explode("_", $res->name);
        $temp = $curr_pro[0];
        //get sku of selected product
        $curr_sku_new = explode("_", $res->sku);
        $temp_sku = $curr_sku_new[0];

        //get new name for same brand or existing brand
        $query = 'SELECT `name`, `group_sku` FROM `tblitems_groups` WHERE `name` LIKE "' . $temp . '%" AND `brandid` = "' . $brandid . '" AND `deleted` = 0 ORDER BY `id` DESC LIMIT 0,1';
        $result = $this->db->query($query);
        $rows = $result->row();

        if (!empty($rows->name)) {
            $old_desc = explode("_", $rows->name);
            $curr_pointer = @$old_desc[1] + 1;
            if (isset($old_desc[1]) && $old_desc[1] > 0) {
                $data['name'] = $old_desc[0] . '_' . $curr_pointer;
            } else {
                $data['name'] = $old_desc[0] . '_1';
            }
        } else {
            $data['name'] = $res->name;
        }

        if (!empty($rows->group_sku)) {
            $old_sku = explode("_", $rows->group_sku);
            $curr_sku = @$old_sku[1] + 1;
            if (isset($old_sku[1]) && $old_sku[1] > 0) {
                $data['group_sku'] = $old_sku[0] . '_' . $curr_sku;
            } else {
                $data['group_sku'] = $old_sku[0] . '_1';
            }
        } else {
            $data['group_sku'] = $res->group_sku;
        }
        if ($duplicate_by_brand == "current_brand") {
            $this->db->insert('tblitems_groups', $data);
            $insert_id = $this->db->insert_id();
            logActivity('Package duplicated successfully [Name: ' . $data['name'] . ']');
            return $insert_id;
        } else {
            $data['brandid'] = $brandid;
            $group_items = json_decode($data['group_items'], 'true');
            $new_group_items = [];
            if (count($group_items) > 0) {
                foreach ($group_items as $itemid => $group_item) {
                    $res = $this->get($itemid);
                    $item = (array)$res;
                    unset($item['id']);
                    unset($item['dateadded']);
                    unset($item['addedby']);
                    unset($item['taxrate']);
                    unset($item['taxid']);
                    unset($item['taxname']);
                    unset($item['taxrate_2']);
                    unset($item['taxid_2']);
                    unset($item['taxname_2']);
                    unset($item['group_name']);
                    unset($item['item_options']);
                    unset($item['taxrate']);

                    //get name of selected product
                    $curr_pro = explode("_", $res->description);
                    $temp = $curr_pro[0];
                    //get sku of selected product
                    $curr_sku_new = explode("_", $res->sku);
                    $temp_sku = $curr_sku_new[0];

                    //get new name for same brand or existing brand
                    $query = 'SELECT `description`,`sku` FROM `tblitems` WHERE `description` LIKE "' . $temp . '%" AND `brandid` = "' . $brandid . '" AND `deleted` = 0 ORDER BY `id` DESC LIMIT 0,1';
                    $result = $this->db->query($query);
                    $rows = $result->row();

                    if (!empty($rows->description)) {
                        $old_desc = explode("_", $rows->description);
                        $curr_pointer = @$old_desc[1] + 1;
                        if (isset($old_desc[1]) && $old_desc[1] > 0) {
                            $item['description'] = $old_desc[0] . '_' . $curr_pointer;
                        } else {
                            $item['description'] = $old_desc[0] . '_1';
                        }
                    } else {
                        $item['description'] = $res->description;
                    }

                    if (!empty($rows->sku)) {
                        $old_sku = explode("_", $rows->sku);
                        $curr_sku = @$old_sku[1] + 1;
                        if (isset($old_sku[1]) && $old_sku[1] > 0) {
                            $item['sku'] = $old_sku[0] . '_' . $curr_sku;
                        } else {
                            $item['sku'] = $old_sku[0] . '_1';
                        }
                    } else {
                        $item['sku'] = $res->sku;
                    }
                    if ($res->income_category != '' || $res->income_category != NULL) {
                        $income_query = $this->db->query('SELECT `id`, `name` FROM `tblincome_category` WHERE `id` = ' . $res->income_category . ' AND `deleted` = 0 AND brandid = ' . get_user_session());
                        $response = $income_query->row();
                        $categoryexist = $this->check_category_exists($response->name, '', $brandid, 'tblincome_category');
                        if (empty($categoryexist)) {
                            $income_data['name'] = $response->name;
                            $income_data['brandid'] = $brandid;

                            $income_category = $this->save_income_category($income_data);

                            $item['income_category'] = $income_category;
                        } else {
                            $item['income_category'] = $categoryexist->id;
                        }
                    }

                    //check if expense category exists or not
                    if ($res->expense_category != '' || $res->expense_category != NULL) {
                        $expense_query = $this->db->query('SELECT `id`, `name` FROM `tblexpense_category` WHERE `id` = ' . $res->expense_category . ' AND `deleted` = 0 AND brandid = ' . get_user_session());
                        $response = $expense_query->row();
                        $categoryexist = $this->check_category_exists($response->name, '', $brandid, 'tblexpense_category');
                        if (empty($categoryexist)) {
                            $expense_data['name'] = $response->name;
                            $expense_data['brandid'] = $brandid;

                            $expense_category = $this->save_expense_category($expense_data);
                            $item['expense_category'] = $expense_category;
                        } else {
                            $item['expense_category'] = $categoryexist->id;
                        }
                    }
                    //check if sub category exists or not
                    if (!empty($res->line_item_sub_category)) {
                        $subcategory_query = $this->db->query('SELECT `id`, `name`, `parent_id` FROM `tbllineitem_subcategory` WHERE `id` = ' . $res->line_item_sub_category . ' AND `deleted` = 0 AND brandid = ' . get_user_session());
                        $response = $subcategory_query->row();
                        $subcategoryexist = $this->check_category_exists($response->name, '', $brandid, 'tbllineitem_subcategory');
                        if (empty($subcategoryexist)) {
                            //check if category exists or not
                            $category_query = $this->db->query('SELECT `id`, `name` FROM `tbllineitem_category` WHERE `id` = ' . $response->parent_id . ' AND `deleted` = 0 AND brandid = ' . get_user_session());
                            $cat_response = $category_query->row();
                            $categoryexist = $this->check_category_exists($cat_response->name, '', $brandid, 'tbllineitem_category');
                            if (empty($categoryexist)) {
                                $category_data['name'] = $cat_response->name;
                                $category_data['brandid'] = $brandid;

                                $category = $this->add_line_item_category($category_data);

                            } else {
                                $category = $categoryexist->id;
                            }

                            $subcategory_data['name'] = $response->name;
                            $subcategory_data['parent_id'] = $category;
                            $subcategory_data['brandid'] = $brandid;

                            $sub_category = $this->add_line_item_sub_category($subcategory_data);

                            $item['line_item_sub_category'] = $sub_category;
                        } else {
                            $item['line_item_sub_category'] = $subcategoryexist->id;
                        }
                    }
                    //check if tax exists or not
                    if ($res->tax != 0) {
                        $tax_query = $this->db->query('SELECT `id`, `name`, `taxrate` FROM `tbltaxes` WHERE `id` = ' . $res->tax . ' AND `deleted` = 0 AND brandid = ' . get_user_session());
                        $response = $tax_query->row();
                        $taxres = $this->db->query('SELECT `id`, `name`, `taxrate` FROM `tbltaxes` WHERE `name` = "' . $response->name . '" AND `deleted` = 0 AND brandid = ' . $brandid);
                        $taxexist = $taxres->row();
                        if (empty($taxexist)) {
                            $taxes_data['name'] = $response->name;
                            $taxes_data['brandid'] = $brandid;
                            $taxes_data['created_by'] = $this->session->userdata['staff_user_id'];
                            $taxes_data['datecreated'] = date('Y-m-d H:i:s');
                            $insert_id = $this->db->insert('tbltaxes', $taxes_data);
                            $item['tax'] = $insert_id;
                        } else {
                            $item['tax'] = $taxexist->id;
                        }
                    }

                    //copy all options
                    $q1 = $this->db->query('SELECT id,option_name,option_type,is_required,`order` FROM tblitems_options WHERE itemid = ' . $itemid . ' AND deleted=0');
                    $item['option'] = $q1->result_array();
                    $options_arr = [];
                    foreach ($item['option'] as $option) {
                        if ($option['option_type'] == 'single_option' || $option['option_type'] == 'dropdown' || $option['option_type'] == 'multi_select') {

                            //copy all choices
                            $q1 = $this->db->query('SELECT choice_name,choice_cost_price,choice_rate,choice_profit,choice_profit_margin,is_default_select,`order` FROM tblitems_choices WHERE option_id = ' . $option['id']);
                            $option_choices = $q1->result_array();
                            $option['choices'] = $option_choices;
                        }

                        if (!empty($option['choices'])) {
                            array_push($options_arr, $option['choices']);
                        }
                    }

                    $item['choice'] = $options_arr;
                    $item['brandid'] = $brandid;
                    /*echo "<pre>";
                    print_r($item);
                    die('<----here');*/
                    $insert_id = $this->add($item, $itemid);
                    $new_group_items[$insert_id] = array('qty' => $group_item['qty'], 'subtotal' => $group_item['subtotal']);
                }
            }
            $data['group_items'] = json_encode($new_group_items);

            $this->db->insert('tblitems_groups', $data);
            $insert_id = $this->db->insert_id();
            logActivity('Package duplicated successfully [Name: ' . $data['name'] . ']');
            return $insert_id;
        }
        return false;
    }

    /**
     * Modified By : masud
     * Dt : 07/08/2018
     * to get all items for given group in add/edit invoice page
     */

    public function get_item($id)
    {

        $columns = $this->db->list_fields('tblitems');
        $rateCurrencyColumns = '';
        foreach ($columns as $column) {
            if (strpos($column, 'rate_currency_') !== FALSE) {
                $rateCurrencyColumns .= $column . ',';
            }
        }

        $this->db->select('*');
        $this->db->from('tblitems');
        $this->db->where('tblitems.deleted', '0');
        $this->db->where('tblitems.id', $id);

        $items = $this->db->get()->row();
        $q1 = $this->db->query('SELECT id,itemid,option_name,option_type,is_required,`order` FROM tblitems_options WHERE itemid = ' . $id . ' AND deleted=0 ORDER BY `order` ASC');
        $options = $q1->result_array();
        $options_arr = [];
        foreach ($options as $option) {
            if ($option['option_type'] == 'single_option' || $option['option_type'] == 'dropdown' || $option['option_type'] == 'multi_select') {
                $q1 = $this->db->query('SELECT id,option_id,choice_name,choice_cost_price,choice_rate,choice_profit,choice_profit_margin,is_default_select,`order` FROM tblitems_choices WHERE option_id = ' . $option['id'] . ' AND deleted=0 ORDER BY `order` ASC');
                $option_choices = $q1->result_array();

                $option['choices'] = $option_choices;
            }
            array_push($options_arr, $option);
        }

        if (count($options_arr) > 0) {
            $items->item_options = $options_arr;
        }
        return $items;
    }
}