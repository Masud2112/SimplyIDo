<?php
/**
* Added By : Vaidehi
* Dt : 10/12/2017
* For Brand Settings Module
*/
defined('BASEPATH') or exit('No direct script access allowed');
class Brand_settings_model extends CRM_Model
{
    private $encrypted_fields = array('smtp_password');

    public function __construct()
    {
        $this->load->model('Brands_model');

        parent::__construct();
        $payment_gateways = $this->payment_modes_model->get_online_payment_modes(true);
        foreach ($payment_gateways as $gateway) {
            $class_name = $gateway['id'] . '_gateway';
            $settings   = $this->$class_name->get_settings();
            foreach ($settings as $option) {
                if (isset($option['encrypted']) && $option['encrypted'] == true) {
                    array_push($this->encrypted_fields, $option['name']);
                }
            }
        }
    }

    /**
     * Update all settings
     * @param  array $data all settings
     * @return integer
     */
    public function update($data)
    {
        $response = [];

        $original_encrypted_fields = array();
        foreach ($this->encrypted_fields as $ef) {
            $original_encrypted_fields[$ef] = get_option($ef);
        }
        $affectedRows = 0;
        $data         = do_action('before_settings_updated', $data);

        //get current brand id of logged in user
        $brandid = get_user_session();

        $all_settings_looped = array();
        if(isset($data['settings'])){
            foreach ($data['settings'] as $name => $val) {
                $flag = 1;
                if($name == 'brandtypes') {
                    $val=serialize($val);
                }
                if($name == 'companyname') {
                    $brandrow = $this->brands_model->get_brand_by_id();

                    if($brandrow->name != $val) {
                        $brands = $this->brands_model->check_brand_exists($val);

                        if(empty($brands) || count($brands) == 0) {
                            $flag = 1;

                            $branddata = array();
                            $branddata['name']        = $val;
                            $branddata['updated_by']  = $this->session->userdata['staff_user_id'];
                            $branddata['dateupdated'] = date('Y-m-d H:i:s');

                            $this->db->where('brandid', $brandid);
                            $this->db->update('tblbrand', $branddata);
                            $response['brandname'] = 'Brand updated successfully';
                        } else {
                            $flag = 0;
                            $response['brandname'] = 'Brand name exists';
                        }
                    }
                } else {
                    $flag = 1;
                }

                if($flag == 1) {
                    if (is_string($val)) {
                        $val = trim($val);
                    }

                    array_push($all_settings_looped, $name);

                    $hook_data['name']  = $name;
                    $hook_data['value'] = $val;
                    $hook_data          = do_action('before_single_setting_updated_in_loop', $hook_data);
                    $name               = $hook_data['name'];
                    $val                = $hook_data['value'];

                    // Check if the option exists
                    //$this->db->where('name', $name);
                    //$exists = $this->db->count_all_results('tblbrandsettings');
                    // if ($exists == 0) {
                    //     continue;
                    // }

                    if ($name == 'default_contact_permissions') {
                        $val = serialize($val);
                    } elseif ($name == 'email_signature') {
                        $val = nl2br_save_html($val);
                    } elseif ($name == 'default_tax') {
                        $val = array_filter($val, function ($value) {
                            return $value !== '';
                        });
                        $val = serialize($val);
                    } elseif($name == 'company_info_format' || $name == 'customer_info_format'){

                        $val = strip_tags($val);
                        $val = nl2br($val);

                    } elseif (in_array($name, $this->encrypted_fields)) {
                        // Check if not empty $val password
                        // Get original
                        // Decrypt original
                        // Compare with $val password
                        // If equal unset
                        // If not encrypt and save
                        if (!empty($val)) {
                            $or_decrypted = $this->encryption->decrypt($original_encrypted_fields[$name]);
                            if ($or_decrypted == $val) {
                                continue;
                            } else {
                                $val = $this->encryption->encrypt($val);
                            }
                        }
                    }

                    /* Added by Purvi on 11-27-2017 for add new settings and manage insert/update*/
                    $exists_key = $this->check_exist_key($name,$brandid);
                    if($name == "filter_tags"){
                        $val = implode(',',$val);
                    }

                    if($exists_key > 0){
                        $this->db->where('name', $name);
                        $this->db->where('brandid', $brandid);
                        $this->db->update('tblbrandsettings', array(
                            'value' => $val
                        ));
                    } else{
                        $optiondata = array();
                        $optiondata['name']         = $name;
                        $optiondata['value']        = $val;
                        $optiondata['brandid']      = $brandid;
                        $optiondata['isvisible']    = 1;
                        $optiondata['created_by']   = $this->session->userdata['staff_user_id'];
                        $optiondata['datecreated']  = date('Y-m-d H:i:s');
                        $this->db->insert('tblbrandsettings', $optiondata);
                    }

                    foreach(array_keys($data['settings']) as $key){
                        $filter_name = $key;
                    }

                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        }

        // Contact permission default none
        if (!in_array('default_contact_permissions', $all_settings_looped) && in_array('customer_settings', $all_settings_looped)) {
            $this->db->where('name', 'default_contact_permissions');
            $this->db->update('tblbrandsettings', array(
            'value' => serialize(array())
        ));
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }
        }

        if (isset($data['custom_fields'])) {
            if (handle_custom_fields_post(0, $data['custom_fields'])) {
                $affectedRows++;
            }
        }
        
        $response['affectedRows'] = $affectedRows;

        return $response;
    }

    public function add_new_company_pdf_field($data)
    {
        $field = 'custom_company_field_' . trim($data['field']);
        $field = preg_replace('/\s+/', '_', $field);
        if (add_option($field, $data['value'])) {
            return true;
        }

        return false;
    }

    public function check_exist_key($name, $brandid)
    {
        $this->db->where('brandid', $brandid);
        $this->db->where('name', $name);
        $exist_data = $this->db->get('tblbrandsettings')->result_array();
        $exist_data_count = count($exist_data);
        return $exist_data_count;
    }
}