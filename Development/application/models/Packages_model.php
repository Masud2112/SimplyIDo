<?php
/**
* Added By: Vaidehi
* Dt: 10/02/2017
* Package Module
*/
defined('BASEPATH') or exit('No direct script access allowed');
class Packages_model extends CRM_Model
{
    //private $perm_statements = array('view', 'view_own', 'edit', 'create', 'delete');

    private $perm_statements = array('access');

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add new package
     * @param mixed $data
     */
    public function add($data)
    {
        $permissions = array();
        /*if (isset($data['view'])) {
            $permissions['view'] = $data['view'];
            unset($data['view']);
        }

        if (isset($data['view_own'])) {
            $permissions['view_own'] = $data['view_own'];
            unset($data['view_own']);
        }
        if (isset($data['edit'])) {
            $permissions['edit'] = $data['edit'];
            unset($data['edit']);
        }
        if (isset($data['create'])) {
            $permissions['create'] = $data['create'];
            unset($data['create']);
        }
        if (isset($data['delete'])) {
            $permissions['delete'] = $data['delete'];
            unset($data['delete']);
        }*/

        if (isset($data['access'])) {
            $permissions['access'] = $data['access'];
            unset($data['access']);
        }

        if (isset($data['restriction'])) {
            $restriction['restriction'] = $data['restriction'];
            unset($data['restriction']);
        }
        
        $package = $this->packages_model->check_package_name_exists($data['name'], '');
        if($package->packageid <= 0 || empty($package)) {
            $data['created_by']     = $this->session->userdata['staff_user_id'];
            $data['datecreated']    = date('Y-m-d H:i:s');
            $data['hash'] = md5(rand() . microtime());
            $this->db->insert('tblpackages', $data);
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                $_all_permissions = $this->packages_model->get_permissions();

                foreach ($_all_permissions as $permission) {
                    $this->db->insert('tblpackagepermissions', array(
                        'permissionid'  => $permission['permissionid'],
                        'packageid'     => $insert_id,
                        'can_access'    => 0,
                        'restriction'   => ($data['packagetypeid'] != 3 ? $restriction['restriction'][$permission['permissionid']] : ''),
                    ));
                }

                foreach ($this->perm_statements as $c) {
                    foreach ($permissions as $key => $p) {
                        if ($key == $c) {
                            foreach ($p as $perm) {
                                $this->db->where('packageid', $insert_id);
                                $this->db->where('permissionid', $perm);
                                $this->db->update('tblpackagepermissions', array(
                                    'can_' . $c => 1,
                                    'restriction'   => ($data['packagetypeid'] != 3 ? $restriction['restriction'][$perm] : '')
                                ));
                            }
                        }
                    }
                }

                logActivity('New Package Added [ID: ' . $insert_id . '.' . $data['name'] . ']');

                return $insert_id;
            }
        }

        return false;
    }

    /**
     * Update package
     * @param  array $data role data
     * @param  mixed $id   role id
     * @return boolean
     */
    public function update($data, $id)
    {
        $affectedRows = 0;
        $permissions  = array();
        /*if (isset($data['view'])) {
            $permissions['view'] = $data['view'];
            unset($data['view']);
        }

        if (isset($data['view_own'])) {
            $permissions['view_own'] = $data['view_own'];
            unset($data['view_own']);
        }
        if (isset($data['edit'])) {
            $permissions['edit'] = $data['edit'];
            unset($data['edit']);
        }
        if (isset($data['create'])) {
            $permissions['create'] = $data['create'];
            unset($data['create']);
        }
        if (isset($data['delete'])) {
            $permissions['delete'] = $data['delete'];
            unset($data['delete']);
        }*/

        if (isset($data['access'])) {
            $permissions['access'] = $data['access'];
            unset($data['access']);
        }

        if (isset($data['restriction'])) {
            $restriction['restriction'] = $data['restriction'];
            unset($data['restriction']);
        }

        $update_customer_permissions = false;
        if (isset($data['update_customer_permissions'])) {
            $update_customer_permissions = true;
            unset($data['update_customer_permissions']);
        }
        
        $package = $this->packages_model->check_package_name_exists($data['name'], $id);
        if(!isset($package)) {

            $data['updated_by']     = $this->session->userdata['staff_user_id'];
            $data['dateupdated']    = date('Y-m-d H:i:s');
            
            $this->db->where('packageid', $id);
            $this->db->update('tblpackages', $data);
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }
            
            $all_permissions = $this->packages_model->get_permissions();
           
            if ($update_customer_permissions == false) {
                $data1['status']         = 0;
                $data1['updated_by']     = $this->session->userdata['staff_user_id'];
                $data1['dateupdated']    = date('Y-m-d H:i:s');
            
                $this->db->where('packageid', $id);
                $this->db->update('tblpackages', $data1);
                if ($this->db->affected_rows() > 0) {
                    $affectedRows++;
                }

                $permissions1 = array();
                /*if (isset($data['view'])) {
                    $permissions1['view'] = $data['view'];
                    unset($data['view']);
                }

                if (isset($data['view_own'])) {
                    $permissions1['view_own'] = $data['view_own'];
                    unset($data['view_own']);
                }
                if (isset($data['edit'])) {
                    $permissions1['edit'] = $data['edit'];
                    unset($data['edit']);
                }
                if (isset($data['create'])) {
                    $permissions1['create'] = $data['create'];
                    unset($data['create']);
                }
                if (isset($data['delete'])) {
                    $permissions1['delete'] = $data['delete'];
                    unset($data['delete']);
                }*/

                if (isset($data['access'])) {
                    $permissions1['access'] = $data['access'];
                    unset($data['access']);
                }
                
                $data['created_by']     = $this->session->userdata['staff_user_id'];
                $data['datecreated']    = date('Y-m-d H:i:s');
                
                $this->db->insert('tblpackages', $data);
                $insert_id = $this->db->insert_id();
                if ($insert_id) {
                    $_all_permissions = $this->packages_model->get_permissions();

                    foreach ($_all_permissions as $permission) {
                        $this->db->insert('tblpackagepermissions', array(
                            'permissionid'  => $permission['permissionid'],
                            'restriction'   => ($data['packagetypeid'] != 3 ? $restriction['restriction'][$permission['permissionid']] : ''),
                            'packageid'     => $insert_id,
                            'can_access'    => 0
                        ));
                    }
                    
                    foreach ($permissions as $key => $val) {
                        foreach ($val as $p) {
                            $this->db->where('packageid', $insert_id);
                            $this->db->where('permissionid', $p);
                            $this->db->update('tblpackagepermissions', array(
                                'can_' . $key   => 1,
                                'restriction'   => ($data['packagetypeid'] != 3 ? $restriction['restriction'][$p] : '')
                            ));
                            if ($this->db->affected_rows() > 0) {
                                $_new_permissions_added_affected_rows++;
                            }
                        }
                    }

                    if ($_new_permissions_added_affected_rows != $_permission_restore_affected_rows) {
                        $affectedRows++;
                    }

                    logActivity('New Package Added [ID: ' . $insert_id . '.' . $data['name'] . ']');
                }
            } else {
                foreach ($all_permissions as $permission) {            
                    foreach ($this->perm_statements as $c) {
                        $package_restriction = $restriction['restriction'][$permission['permissionid']];
                        $access = (in_array($permission['permissionid'], $permissions['access']) ? 1 : 0);

                        $this->db->query("INSERT INTO `tblpackagepermissions` ( `permissionid`, `packageid`, `can_access`, `restriction`) values ('" . $permission['permissionid'] . "', " . $id . ", " . $access . ", " . $package_restriction . ") ON DUPLICATE KEY UPDATE `can_access` = " . $access . ", `restriction` = " . $package_restriction);

                        if ($this->db->affected_rows() > 0) {
                            $_permission_restore_affected_rows++;
                        }
                    }
                }
                
                /*if (total_rows('tblpackagepermissions', array(
                    'packageid' => $id
                )) == 0) {
                    foreach ($all_permissions as $p) {
                        $_ins                 = array();
                        $_ins['packageid']      = $id;
                        $_ins['permissionid']   = $p['permissionid'];
                        $_ins['restriction']    = ($data['packagetypeid'] != 3 ? $restriction['restriction'][$p['permissionid']] : '');
                        $this->db->insert('tblpackagepermissions', $_ins);
                    }
                } elseif (total_rows('tblpackagepermissions', array(
                        'packageid' => $id
                    )) != count($all_permissions)) {

                    foreach ($all_permissions as $p) {
                        if (total_rows('tblpackagepermissions', array(
                            'packageid' => $id,
                            'permissionid' => $p['permissionid']
                        )) == 0) {
                            $_ins                 = array();
                            $_ins['packageid']      = $id;
                            $_ins['permissionid']   = $p['permissionid'];
                            $_ins['restriction']    = ($data['packagetypeid'] != 3 ? $restriction['restriction'][$p['permissionid']] : '');
                            $this->db->insert('tblpackagepermissions', $_ins);
                        }
                    }
                }
               
                $_permission_restore_affected_rows = 0;
                
                foreach ($all_permissions as $permission) {            
                    foreach ($this->perm_statements as $c) {
                        $this->db->where('packageid', $id);
                        $this->db->where('permissionid', $permission['permissionid']);
                        $this->db->update('tblpackagepermissions', array(
                            'can_' . $c     => 0,
                            'restriction'   => ($data['packagetypeid'] != 3 ? $restriction['restriction'][$permission['permissionid']]: '')
                        ));
                        
                        if ($this->db->affected_rows() > 0) {
                            $_permission_restore_affected_rows++;
                        }
                    }
                }

                $_new_permissions_added_affected_rows = 0;
                foreach ($permissions as $key => $val) {
                    foreach ($val as $p) {
                        $this->db->where('packageid', $id);
                        $this->db->where('permissionid', $p);
                        $this->db->update('tblpackagepermissions', array(
                            'can_' . $key   => 1,
                            'restriction'   => ($data['packagetypeid'] != 3 ? $restriction['restriction'][$p] : '')
                        ));
                        
                        if ($this->db->affected_rows() > 0) {
                            $_new_permissions_added_affected_rows++;
                        }
                    }
                }*/

                if ($_new_permissions_added_affected_rows != $_permission_restore_affected_rows) {
                    $affectedRows++;
                }
            }

            if ($affectedRows > 0) {
                logActivity('Package Updated [ID: ' . $id . '.' . $data['name'] . ']');

                return true;
            }
        }

        return false;
    }

    /**
     * Get package by id
     * @param  mixed $id Optional package id
     * @return mixed     array if not id passed else object
     */
    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('packageid', $id);
            $this->db->where('deleted', 0);
            return $this->db->get('tblpackages')->row();
        }
        $this->db->where('deleted', 0);
        return $this->db->get('tblpackages')->result_array();
    }

    /**
     * Delete package
     * @param  mixed $id package id
     * @return mixed
     */
    public function delete($id)
    {
        $current = $this->get($id);
        
        // Check first if package is used in table
        if (is_reference_in_table('packageid', 'tblclients', $id)) {
            return array(
                'referenced' => true
            );
        }
        
        $affectedRows = 0;

        $data['deleted']        = 1;
        $data['updated_by']     = $this->session->userdata['staff_user_id'];
        $data['dateupdated']    = date('Y-m-d H:i:s');
        
        $this->db->where('packageid', $id);
        $this->db->update('tblpackages', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        
        if ($affectedRows > 0) {
            logActivity('Package Deleted [ID: ' . $id);

            return true;
        }

        return false;
    }

    /**
     * Get specific package permissions
     * @param  mixed $id role id
     * @return array
     */
    public function get_package_permissions($id, $type = '')
    {
        $this->db->where('tblpackages.packageid', $id);
        $this->db->join('tblpackages', 'tblpackages.packageid = tblpackagepermissions.packageid', 'left');
        $this->db->join('tblpermissions', 'tblpermissions.permissionid = tblpackagepermissions.permissionid', 'left');

        if($type = 'subscription') {
            $this->db->where('tblpackagepermissions.can_access', 1);
        }

        return $this->db->get('tblpackagepermissions')->result_array();
    }

    /**
     * Get employee role permissions
     * @param  mixed $id permission id
     * @return mixed if id passed return object else array
     */
    public function get_permissions($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('permissionid', $id);

            return $this->db->get('tblpermissions')->row();
        }
        
        $brandid = get_user_session();
        
        $get_session_data = get_session_data();
        $user_package = $get_session_data['package_id'];
        $package = array();
        
        if($brandid > 0){
            $this->db->where('packageid', $user_package);
            $this->db->where('can_access', 1);
            $package = $this->db->get('tblpackagepermissions')->result_array();
            $package = array_column($package, 'permissionid');
        }
        
        if(!empty($package)){
            $this->db->where_in('permissionid', $package); 
        }
        
        $this->db->where('visible_on_package_page', 1);

        $this->db->order_by('name', 'asc');
        
        return $this->db->get('tblpermissions')->result_array();
    }

    public function update_packagepermissions($permissions, $id)
    {
        $all_permissions = $this->packages_model->get_package_permissions();
        if (total_rows('tblpackagepermissions', array(
            'packageid' => $id
        )) == 0) {
            foreach ($all_permissions as $p) {
                $_ins                 = array();
                $_ins['packageid']      = $id;
                $_ins['permissionid'] = $p['permissionid'];
                $this->db->insert('tblpackagepermissions', $_ins);
            }
        } elseif (total_rows('tblpackagepermissions', array(
                'packageid' => $id
            )) != count($all_permissions)) {
            foreach ($all_permissions as $p) {
                if (total_rows('tblpackagepermissions', array(
                    'packageid' => $id,
                    'permissionid' => $p['permissionid']
                )) == 0) {
                    $_ins                 = array();
                    $_ins['packageid']      = $id;
                    $_ins['permissionid'] = $p['permissionid'];
                    $this->db->insert('tblpackagepermissions', $_ins);
                }
            }
        }
        $_permission_restore_affected_rows = 0;
        foreach ($all_permissions as $permission) {
            foreach ($this->perm_statements as $c) {
                $this->db->where('packageid', $id);
                $this->db->where('permissionid', $permission['permissionid']);
                $this->db->update('tblpackagepermissions', array(
                    'can_' . $c => 0
                ));
                if ($this->db->affected_rows() > 0) {
                    $_permission_restore_affected_rows++;
                }
            }
        }
        $_new_permissions_added_affected_rows = 0;
        foreach ($permissions as $key => $val) {
            foreach ($val as $p) {
                $this->db->where('packageid', $id);
                $this->db->where('permissionid', $p);
                $this->db->update('tblpackagepermissions', array(
                    'can_' . $key => 1
                ));
                if ($this->db->affected_rows() > 0) {
                    $_new_permissions_added_affected_rows++;
                }
            }
        }
        if ($_new_permissions_added_affected_rows != $_permission_restore_affected_rows) {
            return true;
        }
    }

    public function get_all_packagetype()
    {
        return $this->db->get('tblpackagetype')->result_array();
    }

    public function get_packagetype_byid($id)
    {
        $this->db->where('id', $id);

        return $this->db->get('tblpackagetype')->result_array();
    }

    /**
     * Get package id
     * @param  mixed $id permission id
     * @return mixed if id passed return object else array
     */
    public function check_package_name_exists($name, $id)
    {
        if($id > 0) {
            $where = array('packageid !=' => $id, 'name =' => $name, 'deleted =' => 0);
        } else {
            $where = array('name =' => $name, 'deleted =' => 0);
        }
        return $this->db->where($where)->get('tblpackages')->row();
    }


    /**
     * Process invoice payment offline or online
     * @since  Version 1.0.1
     * @param  array $data $_POST data
     * @return boolean
     */
    public function process_payment($data, $packageid="")
    {
        // Offline payment mode from the admin side
        // if (is_numeric($data['paymentmode'])) {
        //     if (is_staff_logged_in()) {
        //         $id = $this->subscription_add($data,$packageid);

        //         return $id;
        //     } else {
        //         return false;
        //     }
        //     // Is online payment mode request by client or staff
        // } else

        if (!is_numeric($data['paymentmode']) && !empty($data['paymentmode'])) {
            // This request will come from admin area only
            // If admin clicked the button that dont want to pay the invoice from the getaways only want
            
            if (is_staff_logged_in() && has_permission('account_setup', '', 'create')) {
                /*if (isset($data['do_not_redirect'])) {*/
                    $id = $this->subscription_add($data);
                    //return $id;
                /*}*/
            }

            if (!is_numeric($packageid)) {
                if (!isset($data['packageid'])) {
                    die('No subscription specified');
                } else {
                    $packageid = $data['packageid'];
                }
            }

            if (isset($data['do_not_send_email_template'])) {
                unset($data['do_not_send_email_template']);
                $this->session->set_userdata(array(
                    'do_not_send_email_template' => true
                ));
            }

            $invoice = $this->get($packageid);
            // Check if request coming from admin area and the user added note so we can insert the note also when the payment is recorded
            if (isset($data['note']) && $data['note'] != '') {
                $this->session->set_userdata(array(
                    'payment_admin_note' => $data['note']
                ));
            }

            /*if (get_option('allow_payment_amount_to_be_modified') == 0) {
                $data['amount'] = get_invoice_total_left_to_pay($packageid, $invoice->total);
            }*/

            $data['invoiceid']      = $packageid;
            $data['subscription']   = $invoice;
            $data                   = do_action('before_process_gateway_func', $data);

            $cf = $data['paymentmode'] . '_gateway';

            if($data['amount'] > 0) {
                if($data['paymentmode']=="stripe") {
                    if(!get_staff_user_id()) {
                        $data['custid'] = 0;
                    } else {
                        $brands = $this->db->query('SELECT `brandid` FROM `tblstaffbrand` WHERE `active` = 1 AND `staffid` = '.get_staff_user_id())->result_array();

                        if(count($brands) > 0) {
                            //get all packages of logged in user
                            $brandid = '';
                            foreach ($brands as $brand) {
                                $brandid .= $brand['brandid']. ",";
                            }

                            //get all clients of logged in user
                            $brandid = rtrim($brandid, ",");
                            $users = $this->db->query('SELECT `userid` FROM `tblbrand` WHERE `brandid` IN (' . $brandid . ') GROUP BY `userid`')->result_array();

                            $userid = '';
                            foreach ($users as $user) {
                                $userid .= $user['userid']. ",";
                            }

                            $userid = rtrim($userid, ",");
                            
                            $custid_row = $this->db->query('SELECT IFNULL(`custid`,0) AS custid FROM `tblsubscriptionpaymentrecords` WHERE `userid` = ' . $userid . ' ORDER BY `id` DESC LIMIT 0,1')->row();
                            $data['custid']         = $custid_row->custid;
                        } else {
                            $data['custid']         = 0;
                        }
                    }
                    
                    $this->$cf->subscription_process_payment($data);
                } else {
                    $this->$cf->subscription_process_payment($data);
                }
            } else {
                //Added by Vaidehi on 03/29/2018 for trial and free package subscription
                $this->load->model('payments_model');
                $insertid = $this->payments_model->add($data, 'subscription');
                if ($insertid > 0) {
                    //set_alert('success', _l('online_payment_recorded_success'));
                } else {
                    set_alert('danger', _l('online_payment_recorded_success_fail_database'));
                }
                if($data['page']=="invite"){
                    return $insertid;
                }
                redirect(admin_url('subscription/subscription_option'));
            }
        }    else if($data['amount'] == 0) {
            //Added by Vaidehi on 03/29/2018 for trial and free package subscription
            $this->load->model('payments_model');
            $insertid = $this->payments_model->add($data, 'subscription');
            if ($insertid > 0) {
                //set_alert('success', _l('online_payment_recorded_success'));
            } else {
                set_alert('danger', _l('online_payment_recorded_success_fail_database'));
            }
            if($data['page']=="invite"){
                return $insertid;
            }
            redirect(admin_url('subscription/subscription_option'));
        }

        return false;
    }

    /**
     * Record new payment
     * @param array $data payment data
     * @return boolean
     */
    function subscription_add($data, $packagetype="")
    {   
        //var_dump($data);die;
        // Check if field do not redirect to payment processor is set so we can unset from the database
        if (isset($data['do_not_redirect'])) {
            unset($data['do_not_redirect']);
        }

        if (isset($data['do_not_send_email_template'])) {
            unset($data['do_not_send_email_template']);
            $do_not_send_email_template = true;
        } elseif ($this->session->has_userdata('do_not_send_email_template')) {
            $do_not_send_email_template = true;
            $this->session->unset_userdata('do_not_send_email_template');
        }

        if (is_staff_logged_in()) {
            if (isset($data['date'])) {
                $data['date'] = to_sql_date($data['date']);
            } else {
                $data['date'] = date('Y-m-d H:i:s');
            }
            if (isset($data['note'])) {
                $data['note'] = nl2br($data['note']);
            } elseif ($this->session->has_userdata('payment_admin_note')) {
                $data['note'] = nl2br($this->session->userdata('payment_admin_note'));
                $this->session->unset_userdata('payment_admin_note');
            }
        } else {
            $data['date'] = date('Y-m-d H:i:s');
        }
        unset($data['lid']);
        $data['daterecorded']   = date('Y-m-d H:i:s');
        $data['packageid']      = @$data['packageid'];
        $data                   = do_action('before_payment_recorded', $data);
        
        if($packagetype == "subscription")
        {   
            $data['packageid']      = $data['packageid'];
            $data['amount']         = $data['amount'];
            $data['paymentmode']    = $data['paymentmode'];
            $data['note']           = $data['note'];
            $data['staffid']        = $this->session->userdata['staff_user_id'];
            $data['custid']         = $data['custid'];
            $this->db->insert('tblsubscriptionpaymentrecords', $data);
            $insert_id = $this->db->insert_id();

            $this->db->where('id',$insert_id);
            $pack_data = $this->db->get('tblsubscriptionpaymentrecords')->row();
            $final_pack_data = $this->packages_model->get($pack_data->packageid);

            $brands = $this->db->query('SELECT `brandid` FROM `tblstaffbrand` WHERE `active` = 1 AND `staffid` = '.get_staff_user_id())->result_array();

            if(count($brands) > 0) {
                //get all packages of logged in user
                $brandid = '';
                foreach ($brands as $brand) {
                    $brandid .= $brand['brandid']. ",";
                }

                //get all clients of logged in user
                $brandid = rtrim($brandid, ",");
                
                $users = $this->db->query('SELECT `userid` FROM `tblbrand` WHERE `brandid` IN (' . $brandid . ') GROUP BY `userid`')->result_array();

                $userid = '';
                foreach ($users as $user) {
                    $userid .= $user['userid']. ",";
                }

                $userid = rtrim($userid, ",");
                
                $this->db->query('UPDATE `tblclients` SET `packageid` = ' . $final_pack_data->packageid . ' WHERE `userid` IN ( ' . $userid . ')');

                if($this->db->affected_rows() > 0 ) {
                    logActivity('Package Update [ID:' . $final_pack_data->packageid . ', Package name: ' . $final_pack_data->name . ', User ID: ' . $userid);
                }
                
                $old_package_id = $this->session->userdata('package_id');
                $old_package_type_id = $this->session->userdata('package_type_id');

                $this->session->set_userdata('old_package_id', $old_package_id);
                $this->session->set_userdata('old_package_type_id', $old_package_type_id);
                $this->session->set_userdata('package_id', $final_pack_data->packageid);
                $this->session->set_userdata('package_type_id', $final_pack_data->packagetypeid);
            }

            logActivity('Payment Recorded [ID:' . $insert_id . ', Package name: ' . $final_pack_data->name . ', Total: ' . $final_pack_data->price);
            return $insert_id;
        }
        return false;
    }
}
