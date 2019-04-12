<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Payments_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('invoices_model');
    }

    /**
     * Get payment by ID
     * @param  mixed $id payment id
     * @return object
     */
    public function get($id)
    {
        $this->db->select('*,tblinvoicepaymentrecords.id as paymentid');
        $this->db->join('tblinvoicepaymentsmodes', 'tblinvoicepaymentsmodes.id = tblinvoicepaymentrecords.paymentmode', 'left');
        $this->db->order_by('tblinvoicepaymentrecords.id', 'asc');
        $this->db->where('tblinvoicepaymentrecords.id', $id);
        $payment = $this->db->get('tblinvoicepaymentrecords')->row();
        if (!$payment) {
            return false;
        }
        // Since version 1.0.1
        $this->load->model('payment_modes_model');
        $online_modes = $this->payment_modes_model->get_online_payment_modes(true);
        if (is_null($payment->id)) {
            foreach ($online_modes as $online_mode) {
                if ($payment->paymentmode == $online_mode['id']) {
                    $payment->name = $online_mode['name'];
                }
            }
        }

        return $payment;
    }

    /**
     * Get all invoice payments
     * @param  mixed $invoiceid invoiceid
     * @return array
     */
    public function get_invoice_payments($invoiceid)
    {
        $this->db->select('*,tblinvoicepaymentrecords.id as paymentid');
        $this->db->join('tblinvoicepaymentsmodes', 'tblinvoicepaymentsmodes.id = tblinvoicepaymentrecords.paymentmode', 'left');
        $this->db->order_by('tblinvoicepaymentrecords.id', 'asc');
        $this->db->where('invoiceid', $invoiceid);
        $payments = $this->db->get('tblinvoicepaymentrecords')->result_array();
        // Since version 1.0.1
        $this->load->model('payment_modes_model');
        $online_modes = $this->payment_modes_model->get_online_payment_modes(true);
        $i            = 0;
        foreach ($payments as $payment) {
            if (is_null($payment['id'])) {
                foreach ($online_modes as $online_mode) {
                    if ($payment['paymentmode'] == $online_mode['id']) {
                        $payments[$i]['id']   = $online_mode['id'];
                        $payments[$i]['name'] = $online_mode['name'];
                    }
                }
            }
            $i++;
        }

        return $payments;
    }

    /**
     * Process invoice payment offline or online
     * @since  Version 1.0.1
     * @param  array $data $_POST data
     * @return boolean
     */
    public function process_payment($data, $invoiceid = '')
    {

        unset($data['type']);
        // Offline payment mode from the admin side
        if (is_numeric($data['paymentmode'])) {
            if (is_staff_logged_in()) {
                $id = $this->add($data);

                return $id;
            } else {
                return false;
            }
            // Is online payment mode request by client or staff
        } elseif (!is_numeric($data['paymentmode']) && !empty($data['paymentmode'])) {
            // This request will come from admin area only
            // If admin clicked the button that dont want to pay the invoice from the getaways only want
            if (is_staff_logged_in() && has_permission('account_setup', '', 'create')) {
                if (isset($data['do_not_redirect'])) {
                    $id = $this->add($data);

                    return $id;
                }
            }
            if (!is_numeric($invoiceid)) {
                if (!isset($data['invoiceid'])) {
                    die('No invoice specified');
                } else {
                    $invoiceid = $data['invoiceid'];
                }
            }

            if (isset($data['do_not_send_email_template'])) {
                unset($data['do_not_send_email_template']);
                $this->session->set_userdata(array(
                    'do_not_send_email_template' => true
                ));
            }

            $invoice = $this->invoices_model->get($invoiceid);
            // Check if request coming from admin area and the user added note so we can insert the note also when the payment is recorded
            if (isset($data['note']) && $data['note'] != '') {
                $this->session->set_userdata(array(
                    'payment_admin_note' => $data['note']
                ));
            }

            if (get_option('allow_payment_amount_to_be_modified') == 0) {
                $data['amount'] = get_invoice_total_left_to_pay($invoiceid, $invoice->total);
            }

            $data['invoiceid'] = $invoiceid;
            $data['invoice']   = $invoice;
            $data              = do_action('before_process_gateway_func', $data);

            //Added by Vaidehi on 03/28/2018 to create customer
            $clientid          = $data['invoice']->clientid;
            
            $custid_row = $this->db->query('SELECT IFNULL(`custid`,0) AS custid FROM `tblinvoices` LEFT JOIN `tblinvoicepaymentrecords` ON `tblinvoices`.`id` =  `tblinvoicepaymentrecords`.`invoiceid` WHERE `tblinvoices`.`clientid` = ' . $clientid . ' ORDER BY `tblinvoicepaymentrecords`.`invoiceid` DESC LIMIT 0,1')->row();
            $data['custid']         = $custid_row->custid;
           
            $cf = $data['paymentmode'] . '_gateway';
            $this->$cf->process_payment($data);
        }
        return false;
    }

    /**
     * Record new payment
     * @param array $data payment data
     * @return boolean
     */
    public function add($data, $packagetype = '')
    {
        /**
         * Added By: Masud Shaikh
         * Dt: 05/24/2018
         * To get the page from is Invite page or not
         */
        if (isset($data['page'])) {
            $page=$data['page'];
            unset($data['page']);
        }

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
        $data['daterecorded'] = date('Y-m-d H:i:s');
        $data                 = do_action('before_payment_recorded', $data);

        if(!empty(get_user_session())) {
            $brandid = get_user_session();
        } else {
            $brandid = $data['brand_id'];
        }
        /**
         * Added By: Masud Shaikh
         * Dt: 05/24/2018
         * To get the page from is Invite page or not
         */
        if(isset($page) && $page=="invite"){
            $brandid = $data['brand_id'];
        }
        /* Check whether the payment for subscription or invoice */
        if($packagetype == "subscription") {   
            $client_userid = $this->db->query('SELECT `userid` FROM `tblbrand` WHERE `deleted` = 0 AND `brandid` = '.$brandid)->row();
            $data['packageid'] = $data['packageid'];
            
            if(!empty(get_staff_user_id())) {
                $user_id =  get_staff_user_id();
            } else {
                $user_id = $data['new_user_id'];
            }

            /**
             * Added By: Masud Shaikh
             * Dt: 05/24/2018
             * To get the page from is Invite page or not
             */
            if(isset($page) && $page=="invite"){
                $user_id = $data['new_user_id'];
            }
            $data['staffid']   = $user_id;
            $data['userid']    = $client_userid->userid;

            //Added by Vaidehi on 03/29/2018 for trial and free package subscription
            if(isset($data['invoiceid'])) {
                unset($data['invoiceid']);
            }

            if(isset($data['subscription'])) {
                unset($data['subscription']);
            }

            if(isset($data['brand_id'])) {
                unset($data['brand_id']);
            }

            if(isset($data['new_user_id'])) {
                $user_id = $data['new_user_id'];
                $newuser_id = $data['new_user_id'];
                unset($data['new_user_id']);
            }

            $this->db->insert('tblsubscriptionpaymentrecords', $data);
            $insert_id = $this->db->insert_id();

            $pack_data = $this->db->get('tblsubscriptionpaymentrecords')->row();
            $final_pack_data = $this->packages_model->get($pack_data->packageid);

            logActivity('Payment Recorded [ID:' . $insert_id . ', Package name: ' . $final_pack_data->name . ', Total: ' . $final_pack_data->price);
        } else {

            $this->db->insert('tblinvoicepaymentrecords', $data);
            $insert_id = $this->db->insert_id();
        }

        if ($insert_id) {
            /* Check whether the payment for subscription or invoice */
            if($packagetype == 'subscription') {
                $this->db->where('id',$insert_id);
                $pack_data = $this->db->get('tblsubscriptionpaymentrecords')->row();
                $final_pack_data = $this->packages_model->get($pack_data->packageid);

                if(!empty(get_staff_user_id())) {
                    $user_id =  get_staff_user_id();
                } else {
                    $user_id = $user_id;
                }

                /**
                 * Added By: Masud Shaikh
                 * Dt: 05/24/2018
                 * To get the page from is Invite page or not
                 */
                if(isset($page) && $page=="invite"){
                    $user_id = $newuser_id;
                }
                //for registration subscription
                if(empty(get_user_session()) || (isset($page) && $page=="invite")) {
                    logActivity('Payment Recorded [ID:' . $insert_id . ', Package name: ' . $final_pack_data->name . ', Total: ' . $final_pack_data->price);

                    $this->load->model('Staff_model');
                    $this->load->model('Authentication_model');

                    //if payment success, set staff as non deleted
                    $this->db->query('UPDATE `tblstaff` SET `deleted` = 0, `updated_by` = ' . $user_id. ', `updated_date` = "' . date('Y-m-d H:i:s') . '" WHERE `staffid` = '.$user_id);

                    $this->db->query('UPDATE `tblclients` SET `is_deleted` = 0, `updated_by` = ' . $user_id. ', `dateupdated` = "' . date('Y-m-d H:i:s') . '" WHERE `primary_user_id` = '.$user_id);

                    $staff = $this->Staff_model->get_staff($user_id);
                    $staff_data = $this->Authentication_model->login($staff->email, $staff->random_pass, '', true);

                    if(isset($page) && $page=="invite") {
                        return $insert_id;
                    }
                    if (is_staff_logged_in()) {
                        redirect(admin_url());
                    } else {
                        set_alert('danger', _l('valid_form'));
                        redirect(site_url('authentication/admin'));
                    }
                } else {

                    $brands = $this->db->query('SELECT `brandid` FROM `tblstaffbrand` WHERE `active` = 1 AND `staffid` = '.$user_id)->result_array();

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

                        //Added by Vaidehi on 03/29/2018 to set role as admin
                        //set roles as admin
                        $roles = $this->db->query('SELECT `roleid` FROM `tblroles` WHERE `isupgrade` = 1 AND `deleted` = 0 AND `brandid` = 0')->row();
                        $roleid = $roles->roleid;

                        //get all account owners for client 
                        $account_owners = $this->db->query('SELECT `tblstaffbrand`.`staffid` FROM `tblstaffbrand` JOIN `tblstaff` ON `tblstaffbrand`.`staffid` = `tblstaff`.`staffid` WHERE `tblstaff`.`deleted` = 0 AND `tblstaff`.`user_type` = 1 AND `tblstaffbrand`.`active` = 1 AND `tblstaffbrand`.`brandid` IN (' . $brandid . ') GROUP BY `staffid`')->result_array();

                        $accountownerid = '';
                        foreach ($account_owners as $account_owner) {
                            $accountownerid .= $account_owner['staffid']. ",";
                        }

                        $accountownerid = rtrim($accountownerid, ",");

                        $this->db->query('UPDATE `tblstaff` SET `role` = ' . $roleid . ', `updated_by` = ' . get_staff_user_id() . ', `updated_date` = "' . date('Y-m-d H:i:s') . '" WHERE `staffid` IN ( ' . $accountownerid . ')');

                        $this->db->query('UPDATE `tblroleuserteam` SET `role_id` = ' . $roleid . ' WHERE `user_id` IN ( ' . $accountownerid . ')');
                        
                        $old_package_id         = $this->session->userdata('package_id');
                        $old_package_type_id    = $this->session->userdata('package_type_id');

                        $this->session->set_userdata('old_package_id', $old_package_id);
                        $this->session->set_userdata('old_package_type_id', $old_package_type_id);
                        $this->session->set_userdata('package_id', $final_pack_data->packageid);
                        $this->session->set_userdata('package_type_id', $final_pack_data->packagetypeid);
                    }

                    logActivity('Payment Recorded [ID:' . $insert_id . ', Package name: ' . $final_pack_data->name . ', Total: ' . $final_pack_data->price);
                    return $insert_id;
                }
            } else { 
                $invoice = $this->invoices_model->get($data['invoiceid']);
                $force_update = false;
                if ($invoice->status == 6) {
                    $force_update = true;
                }
                update_invoice_status($data['invoiceid'], $force_update);
                if (is_staff_logged_in()) {
                    $this->invoices_model->log_invoice_activity($data['invoiceid'], 'invoice_activity_payment_made_by_staff', false, serialize(array(
                        format_money($data['amount'], $invoice->symbol),
                        '<a href="' . admin_url('payments/payment/' . $insert_id) . '" target="_blank">#' . $insert_id . '</a>'
                    )));
                } else {
                    $this->invoices_model->log_invoice_activity($data['invoiceid'], 'invoice_activity_payment_made_by_client', true, serialize(array(
                        format_money($data['amount'], $invoice->symbol),
                        '<a href="' . admin_url('payments/payment/' . $insert_id) . '" target="_blank">#' . $insert_id . '</a>'
                    )));
                }

                logActivity('Payment Recorded [ID:' . $insert_id . ', Invoice Number: ' . format_invoice_number($invoice->id) . ', Total: ' . format_money($data['amount'], $invoice->symbol) . ']');
                // Send email to the client that the payment is recorded
                $payment               = $this->get($insert_id);
                $payment->invoice_data = $this->invoices_model->get($payment->invoiceid);
                $paymentpdf            = payment_pdf($payment);
                $attach                = $paymentpdf->Output(_l('payment') . '-' . $payment->paymentid . '.pdf', 'S');

                $this->load->model('emails_model');
                if (!isset($do_not_send_email_template)) {
                    $contacts = $this->clients_model->get_contacts($invoice->clientid);
                    foreach ($contacts as $contact) {
                        if (has_contact_permission('invoices', $contact['id'])) {
                            $this->emails_model->add_attachment(array(
                                'attachment' => $attach,
                                'filename' => _l('payment') . '-' . $payment->paymentid . '.pdf',
                                'type' => 'application/pdf'
                            ));
                            $merge_fields = array();
                            $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($invoice->clientid, $contact['id']));
                            $merge_fields = array_merge($merge_fields, get_invoice_merge_fields($invoice->id, $insert_id));
                            $this->emails_model->send_email_template('invoice-payment-recorded', $contact['email'], $merge_fields);
                        }
                    }
                }

                $this->db->where('staffid', $invoice->addedfrom);
                $this->db->or_where('staffid', $invoice->sale_agent);
                $staff_invoice = $this->db->get('tblstaff')->result_array();

                $merge_fields = array();
                if (!is_client_logged_in()) {
                    $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($invoice->clientid));
                } else {
                    $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($invoice->clientid, get_contact_user_id()));
                }
                $merge_fields = array_merge($merge_fields, get_invoice_merge_fields($invoice->id));

                $notifiedUsers = array();
                foreach ($staff_invoice as $member) {
                    if (get_option('notification_when_customer_pay_invoice') == 1) {
                        if (is_staff_logged_in() && $member['staffid'] == get_staff_user_id()) {
                            continue;
                        }

                        $this->emails_model->add_attachment(array(
                            'attachment' => $attach,
                            'filename' => _l('payment') . '-' . $payment->paymentid . '.pdf',
                            'type' => 'application/pdf'
                        ));

                        $notified = add_notification(array(
                            'fromcompany' => true,
                            'touserid' => $member['staffid'],
                            'description' => 'not_invoice_payment_recorded',
                            'link' => 'invoices/list_invoices#' . $invoice->id,
                            'additional_data' => serialize(array(
                                format_invoice_number($invoice->id)
                            ))
                        ));
                        if($notified){
                            array_push($notifiedUsers,$member['staffid']);
                        }
                    }
                    $this->emails_model->send_email_template('invoice-payment-recorded-to-staff', $member['email'], $merge_fields);
                }

                pusher_trigger_notification($notifiedUsers);

                do_action('after_payment_added', $insert_id);

                return $insert_id;
            }
        }

        return false;
    }

    /**
     * Update payment
     * @param  array $data payment data
     * @param  mixed $id   paymentid
     * @return boolean
     */
    public function update($data, $id)
    {
        $payment = $this->get($id);

        $data['date'] = to_sql_date($data['date']);
        $data['note'] = nl2br($data['note']);
        $_data        = do_action('before_payment_updated', array(
            'data' => $data,
            'id' => $id
        ));
        $data         = $_data['data'];
        $this->db->where('id', $id);
        $this->db->update('tblinvoicepaymentrecords', $data);
        if ($this->db->affected_rows() > 0) {
            if ($data['amount'] != $payment->amount) {
                update_invoice_status($payment->invoiceid);
            }
            logActivity('Payment Updated [Number:' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete payment from database
     * @param  mixed $id paymentid
     * @return boolean
     */
    public function delete($id)
    {
        $current         = $this->get($id);
        $current_invoice = $this->invoices_model->get($current->invoiceid);
        $invoiceid       = $current->invoiceid;
        do_action('before_payment_deleted', array(
            'paymentid' => $id,
            'invoiceid' => $invoiceid
        ));
        $this->db->where('id', $id);
        $this->db->delete('tblinvoicepaymentrecords');
        if ($this->db->affected_rows() > 0) {
            update_invoice_status($invoiceid);
            $this->invoices_model->log_invoice_activity($invoiceid, 'invoice_activity_payment_deleted', false, serialize(array(
                $current->paymentid,
                format_money($current->amount, $current_invoice->symbol)
            )));
            logActivity('Payment Deleted [ID:' . $id . ', Invoice Number: ' . format_invoice_number($current->id) . ']');

            return true;
        }

        return false;
    }
}
