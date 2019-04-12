<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Subscription_model extends CRM_Model
{
    private $project_settings;
    public function __construct()
    {
        parent::__construct();
    }

    /*
    ** Added By Sanjay on 03/14/2018 
    ** Get current subscription plan information of logged in user
    */
    public function get_new_package()
    {
        $this->db->where('staffid', $this->session->userdata['staff_user_id']);
        $result =  $this->db->get('tblsubscriptionpaymentrecords')->row();
        return $result;
    }

    /*
    ** Added By Sanjay on 03/14/2018 
    ** Get list of team members
    */
    public function get_members($id)
    {

        $this->db->select("tblstaffbrand.*,tblstaff.firstname,tblstaff.lastname,tblstaff.email");
        $this->db->join('tblstaff', 'tblstaff.staffid = tblstaffbrand.staffid');
        $this->db->join('tblbrand', 'tblbrand.brandid = tblstaffbrand.brandid');
        $this->db->where('tblstaff.deleted', 0);
        $this->db->where('tblbrand.brandid',$id);
         $this->db->where('tblbrand.deleted', 0);
         $this->db->where('tblstaff.deleted', 0);
        $this->db->order_by('tblstaff.firstname', 'asc'); 
        $result =  $this->db->get('tblstaffbrand')->result_array();
        return $result;
    }

    /*
    ** Added By Sanjay on 03/14/2018 
    ** Get brand by brand id
    */
    public function get_brand_by_id($id)
    {
        $this->db->select("tblbrand.name,tblbrand.brandid");
        $this->db->where('brandid',$id);
        $this->db->where('deleted','0');
        $result =  $this->db->get('tblbrand')->row();
        return $result;
    }

    /*
    ** Added By Sanjay on 03/14/2018 
    ** Get list of project by project id id
    */
    public function get_project_by_id($id)
    {
        $this->db->where('parent', 0);
        $this->db->where('deleted', 0);
        $this->db->where('brandid',$id);
        $result =  $this->db->get('tblprojects')->result_array();
        return $result;
    }

    /*
    ** Added By Sanjay on 03/25/2018 
    ** Delete brand by brnad id
    */
    public function delete_brand_by_id($id)
    {
        $data['active'] ='0';
        $this->db->where('brandid', $id);
        $this->db->update('tblstaffbrand', $data);

        $brand_data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tblbrand', $brand_data);

        return true;
    }

    /*
    ** Added By Sanjay on 03/25/2018 
    ** Delete member by member id
    */
    public function delete_member_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('staffid', $id);
        $this->db->update('tblstaff', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/25/2018 
    ** Delete project by project id
    */
    public function delete_project_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('id', $id);
        $this->db->update('tblprojects', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/25/2018 
    ** Delete contact by contact id
    */
    public function delete_addressbook_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tbladdressbook_client', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/25/2018 
    ** Delete brand venue by id
    */
    public function delete_brand_venue_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tblbrandvenue', $data);
        return true;
    }


    /**
     * Get package by id
     * @param  mixed $id Optional package id
     * @return mixed     array if not id passed else object
     */
    public function get_all_pkg()
    {
        $this->db->where('deleted', 0);
        return $this->db->get('tblpackages')->result_array();
    }


    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete lead status by brand id
    */
    public function delete_lead_status_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tblleadsstatus', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete lead source by brand id
    */
    public function delete_lead_source_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tblleadssources', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete lead contact by brand id
    */
    public function delete_lead_contact_by_id($id)
    {
        $this->db->where('brandid', $id);
        $this->db->delete('tblleadcontact');
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete project status by brand id
    */
    public function delete_project_status_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tblprojectstatus', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete project status by brand id
    */
    public function delete_project_contact_by_id($id)
    {
        $this->db->where('brandid', $id);
        $this->db->delete('tblprojectcontact');
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete invites by brand id
    */
    public function delete_invites_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tblinvite', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete tasks by brand id
    */
    public function delete_tasks_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tblstafftasks', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete files by brand id
    */
    public function delete_files_by_id($id)
    {
        $this->db->where('brandid', $id);
        $this->db->delete('tblfiles');
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete messages by brand id
    */
    public function delete_messages_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tblmessages', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete team by brand id
    */
    public function delete_team_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tblteams', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete team by brand id
    */
    public function delete_roles_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tblroles', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete proposal by brand id
    */
    public function delete_proposal_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tblproposals', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete agreements by brand id
    */
    public function delete_agreements_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tblagreementtemplates', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete email by brand id
    */
    public function delete_email_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tblemailtemplates', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete product and services by brand id
    */
    public function delete_product_service_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tblitems', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete income category by brand id
    */
    public function delete_income_category_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tblincome_category', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete expense category by brand id
    */
    public function delete_expense_category_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tblexpense_category', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete tags category by brand id
    */
    public function delete_tags_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tbltags', $data);
        return true;
    }

    /*
    ** Added By Sanjay on 03/19/2018 
    ** Delete taxes category by brand id
    */
    public function delete_task_status_by_id($id)
    {
        $data['deleted'] ='1';
        $this->db->where('brandid', $id);
        $this->db->update('tbltasksstatus', $data);
        return true;
    }   
    
    /**
    * Added By: Vaidehi
    * Dt: 03/26/2018
    * for cancelling subcsription
    */
    public function cancel_subscription($packageid) {
        $this->session->set_userdata('type', 'subscription');
        $this->load->model('packages_model');
        $package = $this->packages_model->get($packageid);

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

            //set type as trial to update package for trial users as well 
            if($package->packagetypeid == 3) {
                //get last payment mode
                $custid_row = $this->db->query('SELECT `id`, `packageid`, `amount`, `paymentmode`, `paymentmethod`, `staffid`, `userid`, `custid`, `subscriptionid`, `iscancel` FROM `tblsubscriptionpaymentrecords` WHERE `userid` = ' . $userid . ' ORDER BY `id` DESC LIMIT 0,1');
                if($custid_row->num_rows()) {
                    $cust           = $custid_row->row();

                    $paymentid      = $cust->id;
                    $paymentmode    = $cust->paymentmode;
                    if($cust->iscancel != 1) {
                        $paymentid          = $cust->id;
                        $packageid          = $cust->packageid;
                        $amount             = $cust->amount;
                        $paymentmode        = $cust->paymentmode;
                        $paymentmethod      = $cust->paymentmethod;
                        $staffid            = $cust->staffid;
                        $userid             = $cust->userid;
                        $custid             = $cust->custid;
                        $subscriptionid     = $cust->subscriptionid;
                        
                        $cf = $paymentmode . '_gateway';

                        //call method to cancel subscription
                        $data['custid']         = $cust->custid;
                        $data['subscriptionid'] = $cust->subscriptionid;
                        $cancel_response        = $this->$cf->cancel_subscription($data);
                        $status                 = $cancel_response['status'];

                        $this->db->query('INSERT INTO `tblsubscriptionpaymentrecords`( `packageid`, `amount`, `paymentmode`, `paymentmethod`, `date`, `daterecorded`, `staffid`, `userid`, `custid`, `subscriptionid`, `iscancel`, `canceldatetime`) VALUES (' . $packageid . ', ' . $amount . ', "' . $paymentmode. '", "' . $paymentmethod. '", "' . date('Y-m-d') . '", "' . date('Y-m-d H:i:s') . '", ' . $staffid. ', ' . $userid . ', "' . $custid . '", "' . $subscriptionid . '", 1, "' . date('Y-m-d H:i:s') . '")');
                    } else {
                        $status = 'canceled';
                    }
                } else {
                    $status  = 'canceled';
                }
            } else {
                $status     = 'canceled';
            }
            
            if($status == 'canceled') {
                //set roles as collaborator
                $roles = $this->db->query('SELECT `roleid` FROM `tblroles` WHERE `isdowngrade` = 1 AND `deleted` = 0 AND `brandid` = 0')->row();
                $roleid = $roles->roleid;

                if(!empty($roleid)) {  
                    //get all account owners for client 
                    $account_owners = $this->db->query('SELECT `tblstaffbrand`.`staffid` FROM `tblstaffbrand` JOIN `tblstaff` ON `tblstaffbrand`.`staffid` = `tblstaff`.`staffid` WHERE `tblstaff`.`deleted` = 0 AND `tblstaff`.`user_type` = 1 AND `tblstaffbrand`.`active` = 1 AND `tblstaffbrand`.`brandid` IN (' . $brandid . ') GROUP BY `staffid`')->result_array();

                    $accountownerid = '';
                    foreach ($account_owners as $account_owner) {
                        $accountownerid .= $account_owner['staffid']. ",";
                    }

                    $accountownerid = rtrim($accountownerid, ",");

                    $this->db->query('UPDATE `tblstaff` SET `role` = ' . $roleid . ', `updated_by` = ' . get_staff_user_id() . ', `updated_date` = "' . date('Y-m-d H:i:s') . '" WHERE `staffid` IN ( ' . $accountownerid . ')');

                    $this->db->query('UPDATE `tblroleuserteam` SET `role_id` = ' . $roleid . ' WHERE `user_id` IN ( ' . $accountownerid . ')');
                }
                
                $this->db->query('UPDATE `tblclients` SET `packageid` = 0, `updated_by` = ' . get_staff_user_id() . ', `dateupdated` = "' . date('Y-m-d H:i:s') . '" WHERE `userid` IN ( ' . $userid . ')');

                if($this->db->affected_rows() > 0 ) {
                    logActivity('Package Update [ID: 0, Package name: Cancelled Subscription, User ID: ' . $userid);
                }
               
                $old_package_id = $this->session->userdata('package_id');
                $old_package_type_id = $this->session->userdata('package_type_id');

                $this->session->set_userdata('old_package_id', $old_package_id);
                $this->session->set_userdata('old_package_type_id', $old_package_type_id);
                $this->session->set_userdata('package_id', 0);
                $this->session->set_userdata('package_type_id', 0);
                
                return true;
            } else {
                return false;
            }
        }
    }
}