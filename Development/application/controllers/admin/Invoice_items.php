<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Invoice_items extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoice_items_model');
        $this->load->model('taxes_model');
        $this->load->model('staff_model');
    }

    /* List all available items */
    public function index()
    {
        if (!has_permission('items', '', 'view', true)) {
            access_denied('Invoice Items');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('invoice_items');
        }
        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $data['items_groups'] = $this->invoice_items_model->get_groups();
        $data['product_service_groups'] = $this->invoice_items_model->get_product_service_groups();
        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $GLOBALS['brands'] = $brands = $this->staff_model->get_all_brands();;

        $data['column_setting_data'] = $this->invoice_items_model->get_column_setting();

        $data['title'] = _l('invoice_items');
        $this->load->view('admin/invoice_items/manage', $data);
    }

    /*
    ** Added by Avni on 11/29/2017 Start
    ** For Add Edit feature on new page instead of Modal
    */
    public function item($id = '')
    {
        if (!has_permission('items', '', 'view', true)) {
            access_denied('items');
        }

        /*if(isset($_GET['package_id'])){
                $data['pckage_id'] = $_GET['package_id'];
                $item = (array)$this->invoice_items_model->get_group($data['pckage_id']);
                $pitem = ($item['group_items']);
                $pitem = json_decode($pitem, true);
                $pitem[32] = array('qty' => 1,'subtotal'=>'100.00' );
                $item['group_items'] = json_encode($pitem);
                $item['group_price'] = $item['group_price'] + 100.00;
                $item['group_profit'] = $item['group_profit'] + 50.00;
                $item['group_cost'] =   $item['group_cost'] + 50.00;
                echo "<pre>";
                print_r($item);
        }*/

        if ($this->input->post()) {
            $data = $this->input->post();
            unset($data['imagebase64']);
            if (isset($_GET['package_id'])) {
                $data['package_id'] = $_GET['package_id'];
            } elseif (isset($_GET['pid']) && isset($_GET['qid'])) {
                $data['package_id'] = $_GET['pid'];
                $data['quote_id'] = $_GET['qid'];
            }
            if ($id == '') {
                if (!has_permission('items', '', 'create', true)) {
                    access_denied('items');
                }

                $id = $this->invoice_items_model->add($data);
                if ($id) {
                    handle_line_items_image_upload($id);
                    if ($this->input->is_ajax_request()) {
                        echo $id;
                        die;
                    }
                    set_alert('success', _l('added_successfully', _l('invoice_item')));
                    if (isset($data['package_id'])) {
                        if (isset($data['package_id'])) {
                            redirect(admin_url('proposaltemplates/proposal/' . $data['package_id']));
                        } else {
                            redirect(admin_url('invoice_items/package/' . $data['package_id']));
                        }
                    } else {
                        redirect(admin_url('invoice_items/'));
                    }
                } else {
                    set_alert('danger', _l('problem_adding', _l('invoice_item_lowercase')));
                    redirect(admin_url('invoice_items/items/' . $id));
                }
            } else {

                if (!has_permission('items', '', 'edit', true)) {
                    access_denied('items');
                }
                handle_line_items_image_upload($id);
                $success = $this->invoice_items_model->edit($data);
                //var_dump($success);die;
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('invoice_item')));
                    redirect(admin_url('invoice_items'));
                } else {
                    set_alert('danger', _l('problem_updating', _l('invoice_item_lowercase')));
                    redirect(admin_url('invoice_items/items/' . $id));
                }
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('invoice_items'));
        } else {
            $item = $this->invoice_items_model->get($id);
            $data['item'] = $item;
            $title = _l('edit', _l('invoice_items')) . ' ' . $item->description;
        }
        /* 
        ** Added by Avni on 11/29/2017 Start 
        ** Allowing feature to upload image for Paid Members only
        */
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
        $package_type_id = $session_data['package_type_id'];

        $profile_allow = 0;

        if ($is_sido_admin == 1 || $is_admin == 1) {
            $profile_allow = 1;
        } elseif ($package_type_id == 2) {
            $profile_allow = 0;
        } elseif ($package_type_id == 3) {
            $profile_allow = 1;
        }

        $data['title'] = $title;
        $data['profile_allow'] = $profile_allow;

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $data['items_groups'] = $this->invoice_items_model->get_groups();
        //$data['product_service_groups']       = $this->invoice_items_model->get_product_service_groups();
        $data['product_service_groups'] = $this->invoice_items_model->get_line_item_category_lists();
        $data['product_sub_groups'] = $this->invoice_items_model->get_line_item_sub_category_list();
        $data['income_category_list'] = $this->invoice_items_model->get_income_categories();
        $data['expense_category_list'] = $this->invoice_items_model->get_expense_categories();

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        // Added by Avni on 11/29/2017 End     
        $this->load->view('admin/invoice_items/item', $data);
    }

    /* Edit or update items / ajax request /*/
    public function manage()
    {
        if (has_permission('items', '', 'view', true)) {
            if ($this->input->post()) {
                $data = $this->input->post();

                if ($data['itemid'] == '') {
                    if (!has_permission('items', '', 'create', true)) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    $id = $this->invoice_items_model->add($data);
                    handle_line_items_image_upload($id);
                    $success = false;
                    $message = '';
                    if ($id) {
                        $success = true;
                        $message = _l('added_successfully', _l('invoice_item'));
                    }
                    echo json_encode(array(
                        'success' => $success,
                        'message' => $message,
                        'item' => $this->invoice_items_model->get($id)
                    ));
                } else {
                    if (!has_permission('items', '', 'edit', true)) {
                        header('HTTP/1.0 400 Bad error');
                        echo _l('access_denied');
                        die;
                    }
                    handle_line_items_image_upload($data['itemid']);
                    $success = $this->invoice_items_model->edit($data);
                    $message = '';
                    if ($success) {
                        $message = _l('updated_successfully', _l('invoice_item'));
                    }
                    echo json_encode(array(
                        'success' => $success,
                        'message' => $message
                    ));
                }
            }
        }
    }

    /*public function add_group()
    {
        if ($this->input->post() && has_permission('items', '', 'create', true)) {
            $insert_id = $this->invoice_items_model->add_group($this->input->post());
            if($insert_id > 0) {
                set_alert('success', _l('added_successfully', _l('item_group')));    
            } else {
                set_alert('warning',_l('problem_item_group_adding', _l('item_group')));
            }
        }
    }


    public function add_product_service_group()
    {
        if ($this->input->post() && has_permission('items', '', 'create', true)) {
            $insert_id = $this->invoice_items_model->add_product_service($this->input->post());
            if($insert_id > 0) {
                set_alert('success', _l('added_successfully', _l('item_group')));    
            } else {
                set_alert('warning',_l('problem_product_service_group_adding', _l('item_group')));
            }
        }
    }

    public function update_group($id)
    {
        if ($this->input->post() && has_permission('items', '', 'edit', true)) {
            $success = $this->invoice_items_model->edit_group($this->input->post(), $id);
            if($success) {
                set_alert('success', _l('updated_successfully', _l('item_group')));    
            } else {
                set_alert('warning',_l('problem_item_group_editing', _l('item_group')));
            }   
        }
    }

     public function update_product_service_group($id)
    {
        if ($this->input->post() && has_permission('items', '', 'edit', true)) {
            $success = $this->invoice_items_model->edit_product_service_group($this->input->post(), $id);
            if($success) {
                set_alert('success', _l('updated_successfully', _l('item_group')));    
            } else {
                set_alert('warning',_l('problem_item_group_editing', _l('item_group')));
            }   
        }
    }

    public function delete_group($id)
    {
        if (has_permission('items', '', 'delete', true)) {            

            $response = $this->invoice_items_model->delete_group($id);
            if (is_array($response) && isset($response['referenced'])) {
                set_alert('warning', _l('is_referenced', _l('item_group')));
            } elseif ($response == true) {
                set_alert('success', _l('deleted', _l('item_group')));
            } else {
                set_alert('warning', _l('problem_deleting', _l('item_group')));
            }
        }
        //redirect(admin_url('invoice_items?groups_modal=true'));
    }

    public function delete_service_group($id)
    {
        if (has_permission('items', '', 'delete', true)) {            

            $response = $this->invoice_items_model->delete_pro_service_group($id);
            if (is_array($response) && isset($response['referenced'])) {
                set_alert('warning', _l('is_referenced', _l('item_group')));
            } elseif ($response == true) {
                set_alert('success', _l('deleted', _l('item_group')));
            } else {
                set_alert('warning', _l('problem_deleting', _l('item_group')));
            }
        }
        //redirect(admin_url('invoice_items?groups_modal=true'));
    }
    */

    /* Delete item*/
    public function delete($id)
    {
        if (!has_permission('items', '', 'delete', true)) {
            access_denied('Invoice Items');
        }

        if (!$id) {
            redirect(admin_url('invoice_items'));
        }

        $response = $this->invoice_items_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('invoice_item_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('invoice_item')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('invoice_item_lowercase')));
        }
        //redirect(admin_url('invoice_items'));
    }

    public function search()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            echo json_encode($this->invoice_items_model->search($this->input->post('q')));
        }
    }

    /* Get item by id / ajax */
    public function get_item_by_id($id)
    {
        if ($this->input->is_ajax_request()) {
            $item = $this->invoice_items_model->get($id);
            $item->long_description = nl2br($item->long_description);

            /**
             * Added by Avni on 11/29/2017 Start
             * Allowing feature to upload image for Paid Members only
             */
            $session_data = get_session_data();
            $is_sido_admin = $session_data['is_sido_admin'];
            $is_admin = $session_data['is_admin'];
            $package_type_id = $session_data['package_type_id'];

            $profile_allow = 0;

            if ($is_sido_admin == 1 || $is_admin == 1) {
                $profile_allow = 1;
            } elseif ($package_type_id == 2) {
                $profile_allow = 0;
            } elseif ($package_type_id == 3) {
                $profile_allow = 1;
            }

            $item->profile_allow = $profile_allow;
            $profile_image = FCPATH . "uploads/line_item_images/" . $item->itemid . "/small_" . $item->profile_image;
            if (!file_exists($profile_image)) {
                $item->profile_image = "";
            }
            // Added by Avni on 11/29/2017 End
            echo json_encode($item);
        }
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/01/2017
     * send all items for given group
     * Get item by id / ajax
     */
    public function get_item_group_by_id($id)
    {
        if ($this->input->is_ajax_request()) {
            $resArr = [];
            $item = $this->invoice_items_model->get('', $id);

            foreach ($item as $lineitem) {
                $res = new stdClass();
                $res->itemid = $lineitem['itemid'];
                $res->rate = $lineitem['rate'];
                $res->taxrate = $lineitem['taxrate'];
                $res->taxid = $lineitem['taxid'];
                $res->taxname = $lineitem['taxname'];
                $res->taxrate_2 = $lineitem['taxrate_2'];
                $res->taxid_2 = $lineitem['taxid_2'];
                $res->taxname_2 = $lineitem['taxname_2'];
                $res->description = $lineitem['description'];
                $res->long_description = nl2br($lineitem['long_description']);
                $res->profile_image = $lineitem['profile_image'];
                //$res->item_main_id     = $lineitem['itemid'];

                /* 
                ** Added by Avni on 11/29/2017 Start 
                ** Allowing feature to upload image for Paid Members only
                */
                $session_data = get_session_data();
                $is_sido_admin = $session_data['is_sido_admin'];
                $is_admin = $session_data['is_admin'];
                $package_type_id = $session_data['package_type_id'];

                $profile_allow = 0;

                if ($is_sido_admin == 1 || $is_admin == 1) {
                    $profile_allow = 1;
                } elseif ($package_type_id == 2) {
                    $profile_allow = 0;
                } elseif ($package_type_id == 3) {
                    $profile_allow = 1;
                }
                // Added by Avni on 11/29/2017 End 

                $res->group_id = $lineitem['group_id'];
                $res->group_name = $lineitem['group_name'];
                $res->unit = $lineitem['unit'];
                $res->profile_allow = $profile_allow;
                $profile_image = site_url() . "uploads/line_item_images/" . $res->itemid . "/" . $res->profile_image;
                if (!file_exists($profile_image)) {
                    $res->profile_image = "";
                }
                array_push($resArr, $res);
            }

            echo json_encode($resArr);
        }
    }

    public function category_name_exists()
    {
        if ($this->input->post()) {
            $id = $this->input->post('id');
            if ($id != '') {
                $this->db->where('id', $tag_id);
                $this->db->where('brandid', get_user_session());
                $this->db->where('deleted', '0');

                $_current_tag = $this->db->get('tbllineitem_category')->row();
                if ($_current_tag->name == $this->input->post('name')) {
                    echo json_encode(true);
                    die();
                }
            }
            $this->db->where('name', $this->input->post('name'));
            $this->db->where('brandid', get_user_session());
            $this->db->where('deleted', '0');
            $total_rows = $this->db->count_all_results('tbllineitem_category');
            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            die();
        }
    }

    public function subcategory_name_exists()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $id = $this->input->post('id');
            if ($id != '') {
                $this->db->where('id', $tag_id);
                $this->db->where('brandid', get_user_session());
                $this->db->where('deleted', '0');

                $_current_tag = $this->db->get('tbllineitem_category')->row();
                if ($_current_tag->name == $this->input->post('name')) {
                    echo json_encode(true);
                    die();
                }
            }
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

            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            die();
        }
    }

    /*
    ** Added By Avni on 11/24/2017 
    */
    public function invoice_name_exists()
    {
        if ($this->input->post()) {
            $id = $this->input->post('itemid');
            if ($id != '') {
                $this->db->where('id', $id);
                $this->db->where('brandid', get_user_session());
                $this->db->where('deleted', '0');

                $_current_item = $this->db->get('tblitems')->row();
                if ($_current_item->description == $this->input->post('description')) {
                    echo json_encode(true);
                    die();
                }
            }
            $this->db->where('description', $this->input->post('description'));
            $this->db->where('brandid', get_user_session());
            $this->db->where('deleted', '0');
            $total_rows = $this->db->count_all_results('tblitems');
            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            die();
        }
    }

    /*
    ** Added By Avni on 11/29/2017 
    ** Remove line items image / ajax 
    */
    public function remove_line_item_image($id = '')
    {
        if (is_numeric($id) && (has_permission('items', '', 'create', true) || has_permission('items', '', 'edit', true))) {
            $itemid = $id;
        } else {
            $itemid = "";
        }

        $item = $this->invoice_items_model->get($itemid);
        if (file_exists(get_upload_path_by_type('line_items_image') . $itemid)) {
            delete_dir(get_upload_path_by_type('line_items_image') . $itemid);
        }
        $this->db->where('id', $itemid);
        $this->db->update('tblitems', array(
            'profile_image' => null
        ));
        if ($this->input->is_ajax_request()) {
            return true;
        }
        if (!is_numeric($id)) {
            redirect(admin_url('invoice_items/item/' . $id));
        } else {
            redirect(admin_url('invoice_items/item/' . $id));
        }
    }

    /**
     * Added By : Vaidehi
     * Dt : 12/07/2017
     * get all line items for propsoal
     */
    public function get_all_lineitems()
    {
        $lineitems = $this->invoice_items_model->get();
        echo json_encode($lineitems);
        die();
    }

    public function product_item()
    {
        if (!has_permission('items', '', 'view', true)) {
            access_denied('Invoice Items');
        }

        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('invoice_items');
        }

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $data['items_groups'] = $this->invoice_items_model->get_groups();
        $data['product_service_groups'] = $this->invoice_items_model->get_line_item_category();
        $data['line_item_sub_cat'] = $this->invoice_items_model->get_line_item_sub_category();

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $data['title'] = _l('new_productcategory_main_title');
        $this->load->view('admin/invoice_items/product_item', $data);
    }

    /*
    ** Added By Sanjay on 02/05/2018 
    ** Add line item
    */
    public function add_line_item_category($id = '')
    {
        if ($this->input->post() && has_permission('items', '', 'create', true)) {

            $insert_id = $this->invoice_items_model->add_line_item_category($this->input->post());

            if ($insert_id) {
                if ($this->input->is_ajax_request()) {
                    echo $insert_id;
                    die();
                }
                set_alert('success', _l('added_successfully', _l('category')));
            } else {
                set_alert('danger', _l('problem_adding', _l('category_lowercase')));
            }
        }

        redirect(admin_url('invoice_items/view_line_item_category'));
    }

    /*
    ** Added By Sanjay on 02/05/2018 
    ** Update line item
    */
    public function update_line_item_category($id)
    {
        if ($this->input->post() && has_permission('items', '', 'edit', true)) {
            $success = $this->invoice_items_model->edit_line_item_category($this->input->post(), $id);
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('category')));
            } else {
                set_alert('danger', _l('problem_updating', _l('category_lowercase')));
            }
        }
    }

    /*
    ** Added By Sanjay on 02/05/2018 
    ** Delete line item
    */
    public function delete_line_item_category($id)
    {
        if (has_permission('items', '', 'delete', true)) {
            $response = $this->invoice_items_model->delete_line_item_category($id);
            if (is_array($response) && isset($response['referenced'])) {
                set_alert('warning', _l('is_referenced', _l('category')));
            } elseif ($response == true) {
                set_alert('success', _l('deleted', _l('category')));
            } else {
                set_alert('warning', _l('problem_deleting', _l('category')));
            }
        }
    }

    /*
    ** Added By Sanjay on 02/05/2018 
    ** Add line item sub category
    */
    public function add_line_item_sub_category()
    {
        if ($this->input->post() && has_permission('items', '', 'create', true)) {
            $insert_id = $this->invoice_items_model->add_line_item_sub_category($this->input->post());
            if ($insert_id) {
                set_alert('success', _l('added_successfully', _l('subcategory')));
            } else {
                set_alert('warning', _l('problem_adding', _l('subcategory')));
            }
        }
    }

    /*
   ** Added By Sanjay on 02/05/2018
   ** update line item sub category
   */
    public function update_line_item_sub_category()
    {
        if ($this->input->post() && has_permission('items', '', 'edit', true)) {
            if ($this->input->post('id') != "") {
                $success = $this->invoice_items_model->edit_line_item_sub_category($this->input->post());
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('subcategory')));
                } else {
                    set_alert('danger', _l('problem_updating', _l('subcategory_lowercase')));
                }
            } else {
                $success = $this->invoice_items_model->add_line_item_sub_category($this->input->post());

                if ($success) {
                    if ($this->input->is_ajax_request()) {
                        echo $success;
                        die();
                    }
                    set_alert('success', _l('added_successfully', _l('subcategory')));
                } else {
                    set_alert('danger', _l('problem_adding', _l('subcategory_lowercase')));
                }
            }
        }
        redirect(admin_url('invoice_items/view_line_item_category', 'refresh'));
    }

    /*
    ** Added By Sanjay on 02/05/2018 
    ** delete line item sub category
    */
    public function delete_line_item_sub_category($id)
    {
        if (has_permission('items', '', 'delete', true)) {
            $response = $this->invoice_items_model->delete_line_item_sub_category($id);
            if (is_array($response) && isset($response['referenced'])) {
                set_alert('warning', _l('is_referenced', _l('subcategory')));
            } elseif ($response == true) {
                set_alert('success', _l('deleted', _l('subcategory')));
            } else {
                set_alert('warning', _l('problem_deleting', _l('subcategory')));
            }
        }
    }

    /*
    ** Added By Sanjay on 02/05/2018 
    ** Get all line item sub category
    */
    public function getsubcategory($option_id)
    {
        $this->invoice_items_model->option_id = $option_id;
        $suboptions = $this->invoice_items_model->getSubcatOptions();

        header('Content-Type: application/x-json; charset=utf-8');
        echo json_encode($suboptions);
    }


    /*
    ** Added By Sanjay on 02/05/2018 
    ** Get all line item category
    */
    public function add_line_item_master_category()
    {
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('line_items');
        }

        $success = $this->invoice_items_model->add_category_data();

        if ($success) {
            set_alert('success', _l('added_successfully', _l('category')));
        } else {
            set_alert('warning', _l('problem_product_service_group_adding', _l('category')));
        }
        $this->load->view('admin/invoice_items/manage_line_item');
    }

    /*
    ** Added By Sanjay on 02/05/2018 
    ** View line item category listing
    */
    public function view_line_item_category()
    {

        if (!has_permission('items', '', 'view', true)) {
            access_denied('Invoice Items');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('line_items');
        }
        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $data['items_groups'] = $this->invoice_items_model->get_groups();
        $data['all_category_data'] = $this->invoice_items_model->get_line_item_category_list();
        $data['product_service_groups'] = $this->invoice_items_model->get_line_item_category();
        $data['line_item_sub_cat'] = $this->invoice_items_model->get_line_item_sub_category();

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $data['title'] = _l('new_line_items_list_title');
        /*echo "<pre>";
        print_r($data);
        die();*/
        $this->load->view('admin/invoice_items/manage_line_item', $data);
    }


    /*
    ** Added By Sanjay on 02/05/2018 
    ** Delete category status
    */
    public function delete_category_status($id)
    {
        $response = $this->invoice_items_model->delete_category_combination($id);

        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('category_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('category')));
        } else {
            set_alert('danger', _l('problem_deleting', _l('package_lowercase')));
        }

        //redirect(admin_url('invoice_items/manage_line_item'),'refresh');   
    }

    /*
    ** Added By Sanjay on 02/05/2018 
    ** Check for line item name exist
    */
    public function line_item_name_exists()
    {
        if ($this->input->post()) {
            $tag_id = $this->input->post('tagid');
            if ($tag_id != '') {
                $this->db->where('id', $tag_id);
                $this->db->where('brandid', get_user_session());
                $this->db->where('deleted', '0');

                $_current_tag = $this->db->get('tblitems')->row();
                if ($_current_tag->description == $this->input->post('description')) {
                    echo json_encode(true);
                    die();
                }
            }
            $this->db->where('description', $this->input->post('description'));
            $this->db->where('brandid', get_user_session());
            $this->db->where('deleted', '0');
            $this->db->where('is_template', '1');
            $total_rows = $this->db->count_all_results('tblitems');
            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            die();
        }
    }

    /*
    ** Added By Masud on 02/15/2018 
    ** Check for Package name exist
    */
    public function package_name_exists()
    {
        if ($this->input->post()) {
            $tag_id = $this->input->post('tagid');
            if ($tag_id != '') {

                $this->db->where('name', $this->input->post('name'));
                $this->db->where('brandid', get_user_session());
                $this->db->where('deleted', '0');
                $total_rows = $this->db->get('tblitems_groups')->result_array();
                if (count($total_rows) > 0) {
                    if ($total_rows[0]['id'] == $tag_id) {
                        echo json_encode(true);
                    } else {
                        echo json_encode(false);
                    }
                }

                /*$this->db->where('id', $tag_id);         
                $this->db->where('brandid', get_user_session());
                $this->db->where('deleted', '0'); 
                                      
                $_current_tag = $this->db->get('tblitems_groups')->row();
                if ($_current_tag->description == $this->input->post('name')) {
                    echo json_encode(true);
                    die();
                }*/
            } else {
                $this->db->where('name', $this->input->post('name'));
                $this->db->where('brandid', get_user_session());
                $this->db->where('deleted', '0');
                $total_rows = $this->db->count_all_results('tblitems_groups');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    echo json_encode(true);
                }
            }

            die();
        }
    }

    /*
    ** Added By Sanjay on 02/05/2018 
    ** Check for line item sku name exist
    */
    public function sku_name_exists()
    {
        if ($this->input->post()) {
            $tag_id = $this->input->post('tagid');
            if ($tag_id != '') {
                $this->db->where('id', $tag_id);
                $this->db->where('brandid', get_user_session());
                $this->db->where('deleted', '0');

                $_current_tag = $this->db->get('tblitems')->row();
                if ($_current_tag->sku == $this->input->post('sku')) {
                    echo json_encode(true);
                    die();
                }
            }
            $this->db->where('sku', $this->input->post('sku'));
            $this->db->where('brandid', get_user_session());
            $this->db->where('deleted', '0');
            $total_rows = $this->db->count_all_results('tblitems');
            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            die();
        }
    }

    /*
    ** Added By Masud on 02/12/2018 
    ** Check for Package sku name exist
    */
    public function group_sku_name_exists($id = "")
    {

        if ($this->input->post()) {
            $data = $this->input->post();
            $sku = $data ['group_sku'];
            if (isset($sku) && $sku != "") {
                $this->db->where('sku', $sku);
                $this->db->where('brandid', get_user_session());
                $this->db->where('deleted', '0');
                $total_rows = $this->db->count_all_results('tblitems');
                if ($total_rows > 0) {
                    echo json_encode(false);
                } else {
                    $this->db->where('group_sku', $sku);
                    $this->db->where('brandid', get_user_session());
                    $this->db->where('deleted', '0');
                    $total_rows = $this->db->get('tblitems_groups')->row();
                    if (count($total_rows) > 0) {
                        if ($id == $total_rows->id) {
                            echo json_encode(true);
                        } else {
                            echo json_encode(false);
                        }
                    } else {
                        echo json_encode(true);
                    }
                }
            }
            die();
        }
    }


    // Delete option from line item edit
    public function delete_options($id)
    {
        $success = $this->invoice_items_model->delete_options($id);

        if ($success) {
            set_alert('success', _l('category_deleted_successfully', _l('item_group')));
        } else {
            set_alert('warning', _l('problem_product_service_group_adding', _l('item_group')));
        }
        redirect(admin_url('invoice_items/manage_line_item'), 'refresh');
    }

    public function duplicate_pro_service()
    {
        $data = $this->input->post();
        $success = $this->invoice_items_model->make_duplicate_pro_service($data);

        /**
         * Modified By: Vaidehi
         * Dt: 02/05/2018
         * to display success and error messages correctly
         */
        if ($success) {
            $mydir = get_upload_path_by_type('line_items_image') . "/" . $success . "/";
            if (!is_dir($mydir)) {
                mkdir($mydir);
            }
            $path = get_upload_path_by_type('line_items_image') . $data['duplicate_record_id'] . '/*.*';
            $files = glob($path);
            foreach ($files as $file) {
                $file_to_go = str_replace("/" . $data['duplicate_record_id'] . "/", "/" . $success . "/", $file);
                copy($file, $file_to_go);
            }

            set_alert('success', _l('added_successfully', _l('invoice_item')));
        } else {
            set_alert('warning', _l('problem_invoice_item_adding', _l('invoice_item')));
        }

        redirect(admin_url('invoice_items'), 'refresh');
    }

    /*
        added by Masud 29-01-2018
    */
    public function ajax_option_order_update()
    {
        $data = $_POST;
        $options = json_decode($data['options']);
        $data = [];
        foreach ($options as $option) {
            $data['option_id'] = $option->id;
            $data['order'] = $option->order;
            $success = $this->invoice_items_model->update_options_order($data);
        }
        die('<--here');
    }

    public function ajax_choice_order_update()
    {
        $data = $_POST;
        $options = json_decode($data['options']);
        $data = "";
        foreach ($options as $option) {
            $data['id'] = $option->id;
            $data['order'] = $option->order;
            $success = $this->invoice_items_model->update_choices_order($data);
        }
        die('<--here');
    }

    /*
        added by Masud 30-01-2018
    */
    function duplicate_option_choice_ajax()
    {

        $post = $this->input->post();
        $post = json_decode($post['data']);
        $id = $post->id;
        $type = $post->type;
        if ($type == 'option') {

            $option_data = $this->invoice_items_model->get_item_option($id);
            $duplicate_option_id = $this->invoice_items_model->add_item_option($option_data);
            $choices_data = $this->invoice_items_model->get_item_option_choices($id);
            foreach ($choices_data as $choice_data) {
                $this->invoice_items_model->add_item_option_choice($choice_data, $duplicate_option_id);
            }
        } else {

            $choice_data = $this->invoice_items_model->get_item_option_choice($id);
            $option_id = $choice_data['option_id'];
            $option_data = $this->invoice_items_model->get_item_option($option_id);
            $this->invoice_items_model->add_item_option_choice($choice_data, $option_id, $type);

        }
        $item_id = $option_data['itemid'];
        $item = $this->invoice_items_model->get($item_id);
        $data['item'] = $item;
        $title = _l('edit', _l('invoice_items')) . ' ' . $item->description;
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
        $package_type_id = $session_data['package_type_id'];

        $profile_allow = 0;

        if ($is_sido_admin == 1 || $is_admin == 1) {
            $profile_allow = 1;
        } elseif ($package_type_id == 2) {
            $profile_allow = 0;
        } elseif ($package_type_id == 3) {
            $profile_allow = 1;
        }

        $data['title'] = $title;
        $data['profile_allow'] = $profile_allow;

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $data['items_groups'] = $this->invoice_items_model->get_groups();
        $data['product_service_groups'] = $this->invoice_items_model->get_line_item_category_list();
        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $result = $this->load->view('admin/invoice_items/item-option', $data);
    }

    function delete_option_choice_ajax()
    {
        $data = $this->input->post();
        $data = json_decode($data['data']);
        $id = $data->id;
        if ($data->type == 'option' && $id > 0) {
            return $this->invoice_items_model->delete_option($id);
        } else {
            if ($data->type == 'choice' && $id > 0) {
                return $this->invoice_items_model->delete_choice($id);
            }
        }
    }


    /*
        added by Masud 29-01-2018
    */
    public function save_display_settings()
    {
        $success = $this->invoice_items_model->save_display_columns();
        if ($success) {
            set_alert('success', _l('column_setting_added_successfully'));
        } else {
            set_alert('warning', _l('problem_product_service_group_adding', _l('category')));
        }
        redirect(admin_url('invoice_items/'), 'refresh');

    }

    /*
    ** Added By Sanjay on 02/05/2018 
    ** Get all taxes for taxes dropdown in add product & services form
    */
    function get_all_taxes()
    {
        $this->db->select('taxrate, id');
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        $this->db->order_by('id', 'desc');
        echo json_encode($this->db->get('tbltaxes')->result_array());
        die();
    }

    /*Product & Services Packges*/

    public function groups()
    {
        if (has_permission('items', '', 'view', true)) {

            if ($this->input->is_ajax_request()) {
                $this->perfex_base->get_table_data('groups');
            }
            //$data['items_groups'] = $this->invoice_items_model->get_groups();
            $cols = $this->invoice_items_model->get_group_col('', 'package_list');
            $data['vcols'] = $cols;
            $data['title'] = "Packages";
            $this->load->view('admin/invoice_items/groups', $data);
        }
    }

    public function add_group($id = '')
    {
        if (!has_permission('items', '', 'view', true)) {
            access_denied('items');
        }
        if ($this->input->post()) {

            $data = $this->input->post();
            $data['group_items'] = json_encode($data['item']);
            $data['group_price'] = $data['package_total'];
            $data['group_cost'] = $data['package_cost_total'];
            $data['group_profit'] = $data['package_profit'];
            unset($data['package_total']);
            unset($data['package_cost_total']);
            unset($data['package_profit']);
            unset($data['item']);
            unset($data['add_item_to_package']);
            if (!isset($data['manual_entry'])) {
                $data['manual_entry'] = 0;
            }

            if ($id == '') {
                //die('<--here new');
                if (!has_permission('items', '', 'create', true)) {
                    access_denied('items');
                }

                $id = $this->invoice_items_model->add_group($data);
                if ($id) {
                    handle_group_image_upload($id);
                    set_alert('success', _l('added_successfully', _l('item_group')));
                    redirect(admin_url('invoice_items/packages'));
                } else {
                    set_alert('danger', _l('problem_invoice_item_adding', _l('invoice_item_lowercase')));
                    redirect(admin_url('invoice_items/package/' . $id));
                }
            } else {

                if (!has_permission('items', '', 'edit', true)) {
                    access_denied('items');
                }
                handle_group_image_upload($id);

                unset($data['itemid']);
                $success = $this->invoice_items_model->edit_group($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('item_group')));
                    redirect(admin_url('invoice_items/packages'));
                } else {
                    set_alert('danger', _l('problem_invoice_item_updating', _l('invoice_item_lowercase')));
                    redirect(admin_url('invoice_items/package/' . $id));
                }
            }
        }

        if ($id == '') {
            $title = _l('new_package');
        } else {
            $item = $this->invoice_items_model->get_group($id);
            $cols = $this->invoice_items_model->get_group_col($id, 'package');
            $data['item'] = $item;
            $data['vcols'] = $cols;
            $title = _l('edit_package');
        }
        $data['product_service_groups'] = $this->invoice_items_model->get_line_item_category_list();
        //$data['line_item_sub_cat']       = $this->invoice_items_model->get_line_item_sub_category();
        $data['title'] = $title;
        /*echo "<pre>";
        print_r($data);
        die('<--here');*/
        $this->load->view('admin/invoice_items/group', $data);
    }

    public function update_group($id)
    {
        if ($this->input->post() && has_permission('items', '', 'edit', true)) {
            $success = $this->invoice_items_model->edit_group($this->input->post(), $id);
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('item_group')));
            } else {
                set_alert('warning', _l('problem_item_group_editing', _l('item_group')));
            }
        }
    }

    public function delete_group($id)
    {
        if (has_permission('items', '', 'delete', true)) {

            $response = $this->invoice_items_model->delete_group($id);
            if (is_array($response) && isset($response['referenced'])) {
                set_alert('warning', _l('is_referenced', _l('item_group')));
            } elseif ($response == true) {
                set_alert('success', _l('deleted', _l('item_group')));
            } else {
                set_alert('warning', _l('problem_deleting', _l('item_group')));
            }
        }
        //redirect(admin_url('invoice_items/packages'));
    }


    /*
    ** Added by Sanjay 05-02-2018
    ** For adding income category
    */
    public function add_income_category()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if (@$data['id'] == '') {

                $success = $this->invoice_items_model->save_income_category($this->input->post());
                if ($success) {
                    $message = _l('added_successfully', _l('income_category'));
                } else {
                    $message = _l('problem_adding', _l('income_category'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            } else {

                $success = $this->invoice_items_model->edit_income_category($data);

                if ($success) {
                    $message = _l('updated_successfully', _l('income_category'));
                } else {
                    $message = _l('problem_updating', _l('income_category'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            }
        }
    }


    /*
    ** Added By Sanjay on 02/05/2018 
    ** View line item category listing
    */
    public function view_income_category()
    {
        if (!has_permission('lists', '', 'view', true)) {
            access_denied('Invoice Items');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('income_category');
        }

        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['title'] = _l('income_category_title');
        $this->load->view('admin/invoice_items/manage_income_category', $data);
    }


    /* 
    ** Added By Sanjay on 02/05/2018 
    ** Delete income category from database 
    */
    public function income_dategory_delete($id)
    {
        if (has_permission('lists', '', 'delete', true)) {
            $response = $this->invoice_items_model->delete_income_category($id);
            if (is_array($response) && isset($response['referenced'])) {
                set_alert('warning', _l('is_income_category_referenced', _l('income_category')));
            } elseif ($response == true) {
                set_alert('success', _l('deleted', _l('income_category')));
            } else {
                set_alert('warning', _l('problem_deleting', _l('income_category')));
            }
        }
    }

    /* 
    ** Added By Sanjay on 02/05/2018  
    ** Retrieve list of income category from database 
    */
    function get_all_income_category_list()
    {
        $this->db->select('name, id');
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        //$this->db->order_by('id', 'desc');
        echo json_encode($this->db->get('tblincome_category')->result_array());
        die();
    }


    /*
    ** Added By Sanjay on 02/06/2018 
    ** For adding expense category
    */
    public function add_expense_category()
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            if (@$data['id'] == '') {

                $success = $this->invoice_items_model->save_expense_category($data);

                if ($success) {
                    $message = _l('added_successfully', _l('expense_category'));
                } else {
                    $message = _l('problem_adding', _l('expense_category'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            } else {

                $success = $this->invoice_items_model->edit_expense_category($data);

                if ($success) {
                    $message = _l('updated_successfully', _l('expense_category'));
                } else {
                    $message = _l('problem_updating', _l('expense_category'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message
                ));
            }
        }
    }


    /*
    ** Added By Sanjay on 02/06/2018 
    ** View line item category listing
    */
    public function view_expense_category()
    {
        if (!has_permission('lists', '', 'view', true)) {
            access_denied('Invoice Items');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('expense_category');
        }

        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['title'] = _l('expense_category_title');
        $this->load->view('admin/invoice_items/manage_expense_category', $data);
    }


    /* 
    ** Added By Sanjay on 02/06/2018 
    ** Delete expense category from database 
    */
    public function expense_dategory_delete($id)
    {
        if (has_permission('lists', '', 'delete', true)) {
            $response = $this->invoice_items_model->delete_expense_category($id);
            if (is_array($response) && isset($response['referenced'])) {
                set_alert('warning', _l('is_expense_category_referenced', _l('expense_category')));
            } elseif ($response == true) {
                set_alert('success', _l('deleted', _l('expense_category')));
            } else {
                set_alert('warning', _l('problem_deleting', _l('expense_category')));
            }
        }
    }

    /* 
    ** Added By Sanjay on 02/06/2018 
    ** Retrieve list of expense category from database 
    */
    function get_all_expense_category_list()
    {
        $this->db->select('name, id');
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', '0');
        //$this->db->order_by('id', 'desc');
        echo json_encode($this->db->get('tblexpense_category')->result_array());
        die();
    }


    /* Added by Masud */

    public function get_item_by_itemid($id)
    {
        /*if ($this->input->is_ajax_request()) {*/
        $item = $this->invoice_items_model->get($id);
        $item->long_description = nl2br($item->long_description);
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
        $package_type_id = $session_data['package_type_id'];
        $profile_allow = 0;
        if ($is_sido_admin == 1 || $is_admin == 1) {
            $profile_allow = 1;
        } elseif ($package_type_id == 2) {
            $profile_allow = 0;
        } elseif ($package_type_id == 3) {
            $profile_allow = 1;
        }
        $item->profile_allow = $profile_allow;
        $data['item'] = $item;
        return $this->load->view('admin/invoice_items/group_item', $data);
        /*}*/
    }

    /**
     * Added By: Vaidehi
     * Dt: 02/07/2018
     * to check income category name exists or not
     */
    public function incomecategory_name_exists()
    {
        if ($this->input->post()) {
            $id = $this->input->post('id');
            if ($id != '') {
                $this->db->where('id', $id);
                $this->db->where('brandid', get_user_session());
                $this->db->where('deleted', 0);
                $_current_income = $this->db->get('tblincome_category')->row();
                if ($_current_income->name == $this->input->post('name')) {
                    echo json_encode(true);
                    die();
                }
            }
            $this->db->where('name', $this->input->post('name'));
            $this->db->where('brandid', get_user_session());
            $this->db->where('deleted', 0);
            $total_rows = $this->db->count_all_results('tblincome_category');
            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            die();
        }
    }

    /**
     * Added By: Vaidehi
     * Dt: 02/07/2018
     * to check expense category name exists or not
     */
    public function expensecategory_name_exists()
    {
        if ($this->input->post()) {
            $id = $this->input->post('id');
            if ($id != '') {
                $this->db->where('id', $id);
                $this->db->where('brandid', get_user_session());
                $this->db->where('deleted', 0);
                $_current_income = $this->db->get('tblexpense_category')->row();
                if ($_current_income->name == $this->input->post('name')) {
                    echo json_encode(true);
                    die();
                }
            }
            $this->db->where('name', $this->input->post('name'));
            $this->db->where('brandid', get_user_session());
            $this->db->where('deleted', 0);
            $total_rows = $this->db->count_all_results('tblexpense_category');
            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            die();
        }
    }

    function save_package_display_settings()
    {

        if ($this->input->post()) {
            $data = $this->input->post();
            $success = $this->invoice_items_model->save_package_display_columns($data);
            if ($success != "") {
                echo $success;
            }
        }
    }

    /* Remove addressbook profile image / ajax */
    public function remove_group_image($id = '')
    {
        if (is_numeric($id) && (has_permission('items', '', 'create', true) || has_permission('items', '', 'edit', true))) {
            $addressbook_id = $id;
        } else {
            $addressbook_id = "";
        }
        $this->db->where('id', $addressbook_id);
        $member = $this->invoice_items_model->get_group($addressbook_id);
        if (file_exists(get_upload_path_by_type('product_services_package_image') . $addressbook_id)) {

            delete_dir(get_upload_path_by_type('product_services_package_image') . $addressbook_id);
        }
        $this->db->where('id', $addressbook_id);
        $this->db->update('tblitems_groups', array(
            'group_image' => null
        ));
        if (isset($lid)) {
            redirect(admin_url('invoice_items/package/' . $addressbook_id . '?lid=' . $lid));
        } else {
            redirect(admin_url('invoice_items/package/' . $addressbook_id));
        }

    }

    public function duplicate_group()
    {
        $data = $this->input->post();
        $success = $this->invoice_items_model->duplicate_group($data);
        if ($success) {
            $mydir = get_upload_path_by_type('product_services_package_image') . "/" . $success . "/";
            if (!is_dir($mydir)) {
                mkdir($mydir);
            }
            $path = get_upload_path_by_type('product_services_package_image') . $data['duplicate_record_id'] . '/*.*';
            $files = glob($path);
            foreach ($files as $file) {
                $file_to_go = str_replace("/" . $data['duplicate_record_id'] . "/", "/" . $success . "/", $file);
                copy($file, $file_to_go);
            }

            set_alert('success', _l('added_successfully', _l('item_group')));
        } else {
            set_alert('warning', _l('problem_invoice_item_adding', _l('item_group')));
        }

        redirect(admin_url('invoice_items/packages'), 'refresh');
    }
}

