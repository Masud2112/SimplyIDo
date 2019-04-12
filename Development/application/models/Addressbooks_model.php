
<?php

/**
 * Added By: Avni
 * Dt: 10/11/2017
 * Address Book Module
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Addressbooks_model extends CRM_Model
{


    private $perm_statements = array('view', 'view_own', 'edit', 'create', 'delete');

    public function __construct()
    {
        parent::__construct();
    }

    public function delete($id)
    {
        $affectedRows = 0;
        $brandid = get_user_session();
        $created_by = $this->db->select('created_by')->from('tbladdressbook')->where('addressbookid', $id)->get()->row()->created_by;

        $name = $this->db->select('CONCAT(firstname," ",lastname) as name')->from('tbladdressbook')->where('addressbookid', $id)->get()->row()->name;
        //Updated deleted flag if added contact is not public
        $data['deleted'] = 1;
        // $data['updated_by']     = $this->session->userdata['staff_user_id'];
        // $data['dateupdated']    = date('Y-m-d H:i:s');
        //$this->db->where('ispublic', 0);
        if ($created_by == $this->session->userdata['staff_user_id']) {
            $this->db->where('addressbookid', $id);
            $this->db->update('tbladdressbook', $data);
        } else {
            $this->db->where('addressbookid', $id);
            $this->db->where('brandid', $brandid);
            $this->db->update('tbladdressbook_client', $data);
        }

        if ($this->db->affected_rows() > 0) {
            $this->db->where('addressbookid', $id);
            $this->db->update('tblvenuecontact', $data);

            $affectedRows++;
        }

        if ($affectedRows > 0) {
            logActivity('Contact deleted [ID:' . $id . ',Name:' . $name . ']');

            return true;
        }

        return false;
    }

    /**
     * Get address book
     * @param  mixed $id Optional - addressbook id
     * @param  integer $active Optional get all active or inactive
     * @return mixed if id is passed return object else array
     */
    public function get($id = '', $where = array())
    {
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
        $addressbooktags = $addressbookweb = $addressbookphone = $addressbookemail = $addressbookdetails = array();
        if ($is_sido_admin == 0 && $is_admin == 0) {
            $brandid = get_user_session();

            $this->db->join('tbladdressbook_client', 'tbladdressbook_client.addressbookid = tbladdressbook.addressbookid');

            if ($brandid > 0) {
                $this->db->where('tbladdressbook_client.brandid', get_user_session());
            }

            $this->db->where('tbladdressbook_client.deleted', 0);
        }

        $this->db->where('tbladdressbook.deleted', 0);

        if (is_numeric($id)) {
            $this->db->where('tbladdressbook.addressbookid', $id);
            $addressbook = $this->db->get('tbladdressbook')->row();
            $this->db->select('tbladdressbooktags.tagid');

            if ($is_sido_admin == 0 && $is_admin == 0) {
                $this->db->where('brandid', $brandid);
            }

            $this->db->where('addressbookid', $id);
            $addressbooktags = $this->db->get('tbladdressbooktags')->result_array();
            $addressbooktags = array_column($addressbooktags, 'tagid');

            $this->db->where('addressbookid', $id);
            $this->db->join('tblsocials', 'tblsocials.socialid = tbladdressbookweb.type');
            $addressbookweb = $this->db->get('tbladdressbookweb')->result_array();

            $this->db->where('addressbookid', $id);
            $addressbookphone = $this->db->get('tbladdressbookphone')->result_array();

            $this->db->where('addressbookid', $id);
            $addressbookemail = $this->db->get('tbladdressbookemail')->result_array();

            $this->db->where('addressbookid', $id);
            $addressbookdetails = $this->db->get('tbladdressbookdetails')->result_array();

            $addressbook->tags_id = $addressbooktags;
            $addressbook->website = $addressbookweb;
            $addressbook->phone = $addressbookphone;
            $addressbook->email = $addressbookemail;
            $addressbook->address = $addressbookdetails;
            // $this->db->join('tbladdressbook_client', 'tbladdressbook_client.addressbookid = tbladdressbook.addressbookid');
            // if($is_sido_admin == 0 && $is_admin == 0){
            //     $this->db->where('tbladdressbook_client.brandid', get_user_session());
            // }

            // $this->db->where('user_id',$id);
            // $addressbook->permission = $this->db->get('tblroleuserteam')->result();

            return $addressbook;
        }
        $this->db->order_by('firstname', 'desc');

        return $this->db->get('tbladdressbook')->result_array();
    }

    public function viewcontact($id = '', $where = array())
    {

        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
        $addressbooktags = $addressbookweb = $addressbookphone = $addressbookemail = $addressbookdetails = array();

        $this->db->where('tbladdressbook.deleted', 0);
        if (is_numeric($id)) {
            $this->db->where('tbladdressbook.addressbookid', $id);
            $addressbook = $this->db->get('tbladdressbook')->row();
            $this->db->select('tbladdressbooktags.tagid');

            $this->db->where('addressbookid', $id);
            $addressbooktags = $this->db->get('tbladdressbooktags')->result_array();
            $addressbooktags = array_column($addressbooktags, 'tagid');

            $this->db->where('addressbookid', $id);
            $this->db->join('tblsocials', 'tblsocials.socialid = tbladdressbookweb.type');
            $addressbookweb = $this->db->get('tbladdressbookweb')->result_array();

            $this->db->where('addressbookid', $id);
            $addressbookphone = $this->db->get('tbladdressbookphone')->result_array();

            $this->db->where('addressbookid', $id);
            $addressbookemail = $this->db->get('tbladdressbookemail')->result_array();

            $this->db->where('addressbookid', $id);
            $addressbookdetails = $this->db->get('tbladdressbookdetails')->result_array();

            $addressbook->tags_id = $addressbooktags;
            $addressbook->website = $addressbookweb;
            $addressbook->phone = $addressbookphone;
            $addressbook->email = $addressbookemail;
            $addressbook->address = $addressbookdetails;
            // $this->db->join('tbladdressbook_client', 'tbladdressbook_client.addressbookid = tbladdressbook.addressbookid');
            // if($is_sido_admin == 0 && $is_admin == 0){
            //     $this->db->where('tbladdressbook_client.brandid', get_user_session());
            // }

            // $this->db->where('user_id',$id);
            // $addressbook->permission = $this->db->get('tblroleuserteam')->result();
            return $addressbook;
        }
        $this->db->order_by('firstname', 'desc');

        return $this->db->get('tbladdressbook')->result_array();
    }

    public function get_socialsettings()
    {
        $this->db->where('deleted', 0);
        return $this->db->get('tblsocials')->result_array();
    }

    public function add($data)
    {

        if(isset($data['isclient'])){
            $isclient=1;
        }else{
            $isclient=0;
        }
        if (isset($data['rel_type']) && $data['rel_type'] != "") {
            $rel_type = $data['rel_type'];
            $rel_id = $data[$data['rel_type']];
        } else {
            $rel_type = "";
            $rel_id = "";
        }
        $ajax = "";
        if (isset($data['ajax'])) {
            $ajax = $data['ajax'];
        }
        unset($data['lead']);
        unset($data['project']);
        unset($data['event']);
        unset($data['hdnlid']);
        unset($data['hdnpid']);
        unset($data['hdneid']);
        unset($data['hdnlocid']);
        unset($data['hdnvid']);
        unset($data['hdnvenueid']);
        unset($data['rel_type']);
        unset($data['rel_id']);
        unset($data['ajax']);
        unset($data['isclient']);
        unset($data['imagebase64']);

        $data['created_by'] = $this->session->userdata['staff_user_id'];
        $data['datecreated'] = date('Y-m-d H:i:s');
        $brandid = get_user_session();
        $socialsettings = $tags = array();

        if (isset($data['email'])) {
            // $email = array_filter(array_map('array_filter', $data['email']),'email');
            $email = array_filter($data['email'], function ($var) {
                return ($var['email'] != '');
            });
            unset($data['email']);
        }

        if (isset($data['phone'])) {
            $phone = array_filter($data['phone'], function ($var) {
                return ($var['phone'] != '');
            });
            unset($data['phone']);
        }

        if (isset($data['website'])) {
            $website = array_filter($data['website'], function ($var) {
                return ($var['url'] != '');
            });
            unset($data['website']);
        }

        if (isset($data['tags'])) {
            $tags = $data['tags'];
            unset($data['tags']);
        }

        if (isset($data['mode_of_communication'])) {
            $mode_of_communication = implode(',', $data['mode_of_communication']);
            unset($data['mode_of_communication']);
            $data['mode_of_communication'] = $mode_of_communication;
        }

        if (isset($data['address'])) {
            $address = array_filter($data['address'], function ($var) {
                return ($var['locality'] != '' || $var['administrative_area_level_1'] != '' || $var['street_number'] != '' || $var['route'] != '' || $var['postal_code'] != '');
            });
            unset($data['address']);
        }

        $this->db->select('color');
        $this->db->from('tblcolors');
        $this->db->order_by('rand()');
        $this->db->limit(1);
        $staffcolor = $this->db->get()->row();
        $data['profilecolor'] = $staffcolor->color;
        $data['brandid']=get_user_session();
        $this->db->insert('tbladdressbook', $data);
        //$this->db->last_query();
        $insert_id = $this->db->insert_id();
        if ($insert_id) {


            /**
             * Added By: Vaidehi
             * Dt: 03/23/2018
             * to add addressbook for all existing brands
             */
            if (isset($data['ispublic'])&& $data['ispublic'] == 1) {
                /**
                 * Added By: Masud
                 * Dt: 07/06/2018
                 * to add addressbook for all existing brands of current login user
                 */

                $session_data = get_session_data();
                $staffid = $session_data['staff_user_id'];
                $this->db->select('userid');
                $this->db->where('primary_user_id', $staffid);
                $clientdata = $this->db->get('tblclients')->row();
                $clientid = $clientdata->userid;
                $this->db->where('userid', $clientid);
            }else{
                $this->db->where('brandid', get_user_session());
            }
            $this->db->where('deleted', 0);
            $get_brand = $this->db->get('tblbrand')->result_array();
            foreach ($get_brand as $brand) {

                $addressbook_exists = $this->check_brand_addressbook_exists($brand['brandid'], $insert_id);

                if (empty($addressbook_exists->addressbookid)) {
                    $addressbookbrand = [];
                    $addressbookbrand['brandid'] = $brand['brandid'];
                    $addressbookbrand['addressbookid'] = $insert_id;
                    $addressbookbrand['deleted'] = 0;

                    $this->db->insert('tbladdressbook_client', $addressbookbrand);
                }
            }

            if ($ajax == "") {
                $this->new_contact_notification($insert_id);
            }
            /*$this->db->insert(' tbladdressbook_client', array(
                'addressbookid' => $insert_id,
                'brandid' => $brandid
            ));*/

            if (!empty($email)) {
                foreach ($email as $e) {
                    $this->db->insert('tbladdressbookemail', array(
                        'addressbookid' => $insert_id,
                        'type' => $e['type'],
                        'email' => $e['email']
                    ));
                }
            }

            if (!empty($phone)) {
                foreach ($phone as $p) {
                    $this->db->insert('tbladdressbookphone', array(
                        'addressbookid' => $insert_id,
                        'type' => $p['type'],
                        'phone' => $p['phone'],
                        'ext' => $p['ext']
                    ));
                }
            }

            if (!empty($website)) {
                foreach ($website as $w) {
                    $this->db->insert('tbladdressbookweb', array(
                        'addressbookid' => $insert_id,
                        'type' => $w['type'],
                        'url' => $w['url']
                    ));
                }
            }

            if (!empty($address)) {
                foreach ($address as $a) {
                    $this->db->insert('tbladdressbookdetails', array(
                        'addressbookid' => $insert_id,
                        'type' => $a['type'],
                        'address' => $a['street_number'],
                        'address2' => $a['route'],
                        'city' => $a['locality'],
                        'state' => $a['administrative_area_level_1'],
                        'zip' => $a['postal_code'],
                        'country' => $a['country']
                    ));
                }
            }

            if (!empty($tags)) {
                foreach ($tags as $t) {
                    $this->db->insert('tbladdressbooktags', array(
                        'addressbookid' => $insert_id,
                        'tagid' => $t,
                        'brandid' => $brandid
                    ));
                }
            }

            //Added By Avni on 10/31/2017 for Lead Contact
            if (isset($rel_type) && $rel_type == 'lead') {
                $leadcontact = array();
                $leadcontact['leadid'] = $rel_id;
                $leadcontact['contactid'] = $insert_id;
                $brandid = get_user_session();
                $leadcontact['brandid'] = $brandid;

                $this->db->where('leadid', $rel_id);
                $this->db->where('contactid', $insert_id);
                $this->db->where('brandid', $brandid);
                $leadcontacts = $this->db->get('tblleadcontact')->row();

                if (count($leadcontacts) <= 0) {
                    $this->db->insert('tblleadcontact', $leadcontact);
                }
            }

            if (isset($rel_type) && $rel_type == 'project') {
                $projectcontact = array();
                $projectcontact['projectid'] = $rel_id;
                $projectcontact['contactid'] = $insert_id;
                $brandid = get_user_session();
                $projectcontact['brandid'] = $brandid;

                $this->db->where('projectid', $rel_id);
                $this->db->where('contactid', $insert_id);
                $this->db->where('brandid', $brandid);
                $projectcontacts = $this->db->get('tblprojectcontact')->row();

                if (count($projectcontacts) <= 0) {
                    $this->db->insert('tblprojectcontact', $projectcontact);
                }
            }

            /*if (isset($rel_type) && $rel_type == 'event') {
                $projectcontact = array();
                $projectcontact['eventid'] = $rel_id;
                $projectcontact['contactid'] = $insert_id;
                $brandid = get_user_session();
                $projectcontact['brandid'] = $brandid;

                $this->db->where('eventid', $rel_id);
                $this->db->where('contactid', $insert_id);
                $this->db->where('brandid', $brandid);
                $projectcontacts = $this->db->get('tblprojectcontact')->row();

                if (count($projectcontacts) <= 0) {
                    $this->db->insert('tblprojectcontact', $projectcontact);
                }
            }*/
            if ($ajax == "") {
                logActivity('Contact added [ID: ' . $insert_id . ', Name:' . $data['firstname'] . ']');
            }

            return $insert_id;
        }
        return false;
    }

    /**
     * Added By: Vaidehi
     * Dt: 03/23/2018
     * to check if addressbook for brand exists or not
     */
    function check_brand_addressbook_exists($brandid, $addressbookid)
    {
        $this->db->where('brandid', $brandid);
        $this->db->where('addressbookid', $addressbookid);
        $this->db->where('deleted', 0);
        return $this->db->get('tbladdressbook_client')->row();
    }

    public function new_contact_notification($contact_id, $integration = false, $action = 'created')
    {
        $result = $this->db->select("CONCAT(firstname, lastname) name,ispublic")->from('tbladdressbook')->where('addressbookid', $contact_id)->get()->row();
        $name = $result->name;
        $ispublic = $result->ispublic;
        $notification_data = array(
            'description' => ($action == 'created') ? 'not_new_contact_is_created' : 'not_contact_is_updated',
            'brandid' => get_user_session(),
            'touserid' => 0,
            'eid' => $contact_id,
            'not_type' => 'Contact',
            'ispublic' => $ispublic,
            'link' => 'addressbooks/addressbook/' . $contact_id,
            'additional_data' => ($integration == false ? serialize(array(
                $name
            )) : serialize(array()))
        );
        $assigned = get_staff_user_id();
        if (add_notification($notification_data)) {
            pusher_trigger_notification(array($assigned));
        }
    }

    public function update($data, $id)
    {
        if (isset($data['rel_type']) && $data['rel_type'] != "") {
            $rel_type = $data['rel_type'];
            $rel_id = $data[$data['rel_type']];
        } else {
            $rel_type = "";
            $rel_id = "";
        }
        unset($data['lead']);
        unset($data['project']);
        unset($data['event']);
        unset($data['hdnlid']);
        unset($data['hdnpid']);
        unset($data['hdneid']);
        unset($data['hdnvenueid']);
        unset($data['rel_type']);
        unset($data['rel_id']);
        unset($data['hdnlocid']);
        unset($data['hdnvid']);
        unset($data['imagebase64']);

        $affectedRows = 0;
        $socialsettings = $tags = array();

        $brandid = get_user_session();

        if (isset($data['mode_of_communication'])) {
            $mode_of_communication = implode(',', $data['mode_of_communication']);
            unset($data['mode_of_communication']);

            $data['mode_of_communication'] = $mode_of_communication;
        }

        if (isset($data['email'])) {
            $email = array_filter($data['email'], function ($var) {
                return ($var['email'] != '');
            });

            unset($data['email']);
        }

        if (isset($data['phone'])) {
            $phone = array_filter($data['phone'], function ($var) {
                return ($var['phone'] != '');
            });

            unset($data['phone']);
        }

        if (isset($data['website'])) {
            $website = array_filter($data['website'], function ($var) {
                return ($var['url'] != '');
            });

            unset($data['website']);
        }

        if (isset($data['tags'])) {
            $tags = $data['tags'];

            unset($data['tags']);
        }

        if (isset($data['address'])) {
            $address = array_filter($data['address'], function ($var) {
                return ($var['locality'] != '' || $var['administrative_area_level_1'] != '' || $var['street_number'] != '' || $var['route'] != '' || $var['postal_code'] != '');
            });

            unset($data['address']);
        }

        if (isset($data['company'])) {
            $data['company'] = $data['company'];
        } else {
            $data['company'] = 0;
        }

        if (isset($data['ispublic'])) {
            $data['ispublic'] = $data['ispublic'];
        } else {
            $data['ispublic'] = 0;
        }

        $this->db->where('addressbookid', $id);
        $this->db->where('brandid', $brandid);
        $this->db->delete('tbladdressbooktags');

        $this->db->where('addressbookid', $id);
        $this->db->delete('tbladdressbookweb');

        $this->db->where('addressbookid', $id);
        $this->db->delete('tbladdressbookphone');

        $this->db->where('addressbookid', $id);
        $this->db->delete('tbladdressbookemail');

        $this->db->where('addressbookid', $id);
        $this->db->delete('tbladdressbookdetails');

        $data['updated_by'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $this->db->where('addressbookid', $id);
        $this->db->update('tbladdressbook', $data);
        if ($this->db->affected_rows() > 0) {
            $this->new_contact_notification($id, 'update');
            $affectedRows++;
        }

        /**
         * Added By: Vaidehi
         * Dt: 03/23/2018
         * to add addressbook for all existing brands
         */
        if ($data['ispublic'] == 1) {
            /**
             * Added By: Masud
             * Dt: 20/10/2018
             * to add addressbook for all existing brands of current login user
             */

            $session_data = get_session_data();
            $staffid = $session_data['staff_user_id'];
            $this->db->select('userid');
            $this->db->where('primary_user_id', $staffid);
            $clientdata = $this->db->get('tblclients')->row();
            $clientid = $clientdata->userid;
            $this->db->where('userid', $clientid);
            $this->db->where('deleted', 0);
            $get_brand = $this->db->get('tblbrand')->result_array();
            foreach ($get_brand as $brand) {
                $addressbook_exists = $this->check_brand_addressbook_exists($brand['brandid'], $id);
                if (empty($addressbook_exists->addressbookid)) {
                    $addressbookbrand = [];
                    $addressbookbrand['brandid'] = $brand['brandid'];
                    $addressbookbrand['addressbookid'] = $id;
                    $addressbookbrand['deleted'] = 0;
                    $this->db->insert('tbladdressbook_client', $addressbookbrand);
                }
            }
        }
        if (!empty($email)) {
            foreach ($email as $e) {
                $this->db->insert('tbladdressbookemail', array(
                    'addressbookid' => $id,
                    'type' => $e['type'],
                    'email' => $e['email']
                ));
            }
        }

        if (!empty($phone)) {
            foreach ($phone as $p) {
                $this->db->insert('tbladdressbookphone', array(
                    'addressbookid' => $id,
                    'type' => $p['type'],
                    'phone' => $p['phone'],
                    'ext' => $p['ext']
                ));
            }
        }

        if (!empty($website)) {
            foreach ($website as $w) {
                $this->db->insert('tbladdressbookweb', array(
                    'addressbookid' => $id,
                    'type' => $w['type'],
                    'url' => $w['url']
                ));
            }
        }

        if (!empty($address)) {
            foreach ($address as $a) {
                $this->db->insert('tbladdressbookdetails', array(
                    'addressbookid' => $id,
                    'type' => $a['type'],
                    'address' => $a['street_number'],
                    'address2' => $a['route'],
                    'city' => $a['locality'],
                    'state' => $a['administrative_area_level_1'],
                    'zip' => $a['postal_code'],
                    'country' => $a['country']
                ));
            }
        }

        if (!empty($tags)) {
            foreach ($tags as $t) {
                $this->db->insert('tbladdressbooktags', array(
                    'addressbookid' => $id,
                    'tagid' => $t,
                    'brandid' => $brandid
                ));
            }
        }

        if (isset($rel_type) && $rel_type == 'lead') {
            $leadcontact = array();
            $leadcontact['leadid'] = $rel_id;
            $leadcontact['contactid'] = $id;
            $brandid = get_user_session();
            $leadcontact['brandid'] = $brandid;

            $this->db->where('leadid', $rel_id);
            $this->db->where('contactid', $id);
            $this->db->where('brandid', $brandid);
            $this->db->delete('tblleadcontact');

            $this->db->insert('tblleadcontact', $leadcontact);
        }

        if (isset($rel_type) && $rel_type == 'project') {
            $projectcontact = array();
            $projectcontact['projectid'] = $rel_id;
            $projectcontact['contactid'] = $id;
            $brandid = get_user_session();
            $projectcontact['brandid'] = $brandid;

            $this->db->where('contactid', $id);
            $this->db->where('brandid', $brandid);
            $this->db->delete('tblprojectcontact');

            $this->db->insert('tblprojectcontact', $projectcontact);
        }

        if (isset($rel_type) && $rel_type == 'event') {
            $projectcontact = array();
            $projectcontact['eventid'] = $rel_id;
            $projectcontact['contactid'] = $id;
            $brandid = get_user_session();
            $projectcontact['brandid'] = $brandid;

            $this->db->where('contactid', $id);
            $this->db->where('brandid', $brandid);
            $this->db->delete('tblprojectcontact');

            $this->db->insert('tblprojectcontact', $projectcontact);
        }

        if ($affectedRows > 0) {
            logActivity('Contact updated [ID: ' . $id . '.' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Get addressbookid id
     * @param  mixed $id permission id
     * @return mixed if id passed return object else array
     */
    public function check_addressbook_name_exists($email, $id)
    {
        if ($id > 0) {
            $where = array('addressbookid !=' => $id, 'email =' => $email, 'deleted =' => 0);
        } else {
            $where = array('email =' => $email, 'deleted =' => 0);
        }
        return $this->db->where($where)->get('tbladdressbook')->row();
    }

    /*Added by Avni on 11/13/2017*/
    public function favorite($addressbook_id)
    {
        $session_data = get_session_data();
        $user_id = $session_data['staff_user_id'];

        $exist = $this->db->select('favoriteid')->from('tblfavorites')->where('favtype = "Addressbook" AND typeid=' . $addressbook_id . ' AND userid=' . $user_id)->get()->row();
        if (!empty($exist)) {
            $this->db->where('userid', $user_id);
            $this->db->where('typeid', $addressbook_id);
            $this->db->where('favtype', "Addressbook");
            $this->db->delete('tblfavorites');
            return "deleted";
        } else {
            $this->db->insert('tblfavorites', array(
                'favtype' => "Addressbook",
                'typeid' => $addressbook_id,
                'userid' => $user_id
            ));
            return "added";
        }

    }

    // Added by Avni on 11/23/2017 Start

    /**
     *  Get customer billing details
     * @param   mixed $id customer id
     * @return  array
     */
    public function get_customer_billing_and_shipping_details($id)
    {
        $this->db->select('address,address2,city,state,zip,country');
        $this->db->from('tbladdressbookdetails');
        $this->db->where('addressbookid', $id);
        $this->db->where('type', 'primary');

        return $this->db->get()->result_array();
    }

    /**
     * Get customers contacts
     * @param  mixed $customer_id
     * @param  array $where perform where in query
     * @return array
     */
    public function get_contacts($id = '', $where = array())
    {
        $this->db->where($where);
        if ($id != '') {
            $this->db->where('addressbookid', $id);

            $addressbook = $this->db->get('tbladdressbook')->row();
            $this->db->select('tbladdressbooktags.tagid');
            $this->db->where('addressbookid', $id);
            $addressbooktags = $this->db->get('tbladdressbooktags')->result_array();
            $addressbooktags = array_column($addressbooktags, 'tagid');

            $this->db->where('addressbookid', $id);
            $addressbookweb = $this->db->get('tbladdressbookweb')->result_array();

            $this->db->where('addressbookid', $id);
            $this->db->where('type', 'primary');
            $addressbookphone = $this->db->get('tbladdressbookphone')->row();

            $this->db->where('addressbookid', $id);
            $this->db->where('type', 'primary');
            $addressbookemail = $this->db->get('tbladdressbookemail')->row();

            $this->db->where('addressbookid', $id);
            $this->db->where('type', 'primary');
            $addressbookdetails = $this->db->get('tbladdressbookdetails')->row();
            if(!empty($addressbook)){
                if(isset($addressbook->tags_id)){
                    $addressbook->tags_id = !empty($addressbooktags) ? $addressbooktags : "";
                }
                if(isset($addressbook->website)){
                    $addressbook->website = !empty($addressbookweb) ? $addressbookweb : "";
                }
                if(isset($addressbook->phone)){
                    $addressbook->phone = !empty($addressbookphone) ? $addressbookphone : "";
                }
                if(isset($addressbook->email)){
                    $addressbook->email = !empty($addressbookemail) ? $addressbookemail : "";
                }
                if(isset($addressbook->address)){
                    $addressbook->address = !empty($addressbookdetails) ? $addressbookdetails : "";
                }
            }

            return $addressbook;
        }
        // $this->db->order_by('is_primary', 'DESC');

        return $this->db->get('tbladdressbookdetails')->result_array();
    }
    // Added by Avni on 11/23/2017 End

    /**
     * Added By : Sanjay
     * Dt : 12/27/2017
     * Get contact with global search
     */
    public function get_lead_adddress()
    {
        $brandid = get_user_session();
        $query = "SELECT a.* FROM `tbladdressbook` a JOIN `tbladdressbook_client` ac ON a.addressbookid = ac.addressbookid AND ac.deleted = 0 WHERE ac.`brandid` = $brandid AND ac.`deleted` = 0 UNION DISTINCT SELECT a1.* FROM `tbladdressbook` a1 WHERE a1.`ispublic` = 1 AND a1.`deleted` = 0";
        $result = $this->db->query($query);
        $rows = $result->result_array();
        return $rows;

    }

    public function get_global_adddress()
    {
        $brandid = get_user_session();
        $query = "SELECT a.* FROM `tbladdressbook` a WHERE a.addressbookid NOT IN(SELECT ac.addressbookid from tbladdressbook_client as ac WHERE ac.brandid = $brandid and ac.deleted = 0) AND a.`ispublic` = 1 AND a.`deleted` = 0";
        $query = "SELECT a.* FROM `tbladdressbook` a WHERE a.addressbookid NOT IN(SELECT ac.addressbookid from tbladdressbook_client as ac WHERE ac.brandid = $brandid and ac.deleted = 0) AND a.`ispublic` = 1 AND a.`deleted` = 0";
        $result = $this->db->query($query);
        $rows = $result->result_array();
        return $rows;

    }

    public function get_leads()
    {
        $this->db->select('id,name');
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', 0);

        //$this->db->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        return $this->db->get('tblleads')->result_array();
    }

    public function get_projects()
    {
        /*$this->db->select('id,name');
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', 0);
        $this->db->where('parent', 0);
        $this->db->where('addedfrom', get_staff_user_id());
        //$this->db->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        return $this->db->get('tblprojects')->result_array();*/
        $getProject = 'SELECT DISTINCT tblprojects.id as id, tblprojects.name FROM tblprojects LEFT JOIN tblprojectcontact ON tblprojectcontact.projectid = tblprojects.id WHERE tblprojects.deleted = 0 AND tblprojects.parent = 0 AND tblprojects.brandid = ' . get_user_session() . ' AND ( assigned = ' . get_staff_user_id() . ' OR addedfrom = ' . get_staff_user_id() . ' OR tblprojectcontact.contactid = ' . get_staff_user_id() . ')';
        return $this->db->query($getProject)->result_array();
    }

    public function get_events($pid)
    {
        $this->db->select('id,name');
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', 0);
        $this->db->where('addedfrom', get_staff_user_id());
        if ($pid != "") {
            $this->db->where('parent', $pid);
        } else {
            $this->db->where('parent != ""');
        }
        //$this->db->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");
        $res = $this->db->get('tblprojects')->result_array();
        return $res;
    }

    public function gettype($id, $lid = "", $pid = "", $eid = "")
    {
        $brandid = get_user_session();
        $data = array();
        if ($lid > 0) {
            $this->db->select('leadid');
            $this->db->where('contactid', $id);
            $this->db->where('brandid', $brandid);
            $leadcontact = $this->db->get('tblleadcontact')->row();
            $data['rel_id'] = $leadcontact->leadid;
            $data['rel_type'] = "lead";
        } elseif ($pid > 0 || $eid > 0) {
            $this->db->select('projectid,eventid');
            $this->db->where('contactid', $id);
            $this->db->where('brandid', $brandid);
            $projectcontact = $this->db->get('tblprojectcontact')->row();
            if ($projectcontact->projectid > 0) {
                $data['rel_id'] = $projectcontact->projectid;
                $data['rel_type'] = "project";
            } else {
                $data['rel_id'] = $projectcontact->eventid;
                $data['rel_type'] = "event";
            }
        }
        return $data;
    }

    /**
     * Added By : Purvi
     * Dt : 01/11/2017
     * For add global contact to addressbook
     */
    public function add_existing_contact($data)
    {
        $brandid = get_user_session();
        $pid = $data['hdnpid'];
        $lid = $data['hdnlid'];
        $eid = $data['hdneid'];
        unset($data['hdnlid']);
        unset($data['hdnpid']);
        unset($data['hdneid']);
        $this->db->insert(' tbladdressbook_client', array(
            'addressbookid' => $data['clients'],
            'brandid' => $brandid
        ));
        $insert_id = $this->db->insert_id();
        if ($pid > 0) {
            $this->db->insert('tblprojectcontact', array(
                'contactid' => $data['clients'],
                'projectid' => $pid,
                'brandid' => $brandid
            ));
        } elseif ($eid > 0) {
            $this->db->insert('tblprojectcontact', array(
                'contactid' => $data['clients'],
                'eventid' => $eid,
                'brandid' => $brandid
            ));
        } elseif ($lid > 0) {
            $this->db->insert('tblleadcontact', array(
                'contactid' => $data['clients'],
                'leadid' => $lid,
                'brandid' => $brandid
            ));
        }
        return $insert_id;
    }

    /**
     * Added By: Vaidehi
     * Dt: 02/27/2018
     * For Pin/Unpin Contact
     */
    public function pincontact($contact_id)
    {
        $session_data = get_session_data();
        $user_id = $session_data['staff_user_id'];

        $pinexist = $this->db->select('pinid')->from('tblpins')->where('pintype = "Addressbook" AND pintypeid = ' . $contact_id . ' AND userid = ' . $user_id)->get()->row();
        if (!empty($pinexist)) {
            $this->db->where('userid', $user_id);
            $this->db->where('pintypeid', $contact_id);
            $this->db->where('pintype', "Addressbook");
            $this->db->delete('tblpins');

            return 0;
        } else {
            $this->db->insert('tblpins', array(
                'pintype' => "Addressbook",
                'pintypeid' => $contact_id,
                'userid' => $user_id
            ));

            return 1;
        }
    }

    /**
     * Added By: Masud
     * Dt: 06/25/2018
     * For Kanban view Contact
     */

    public function get_kanban_contacts($leadid = "", $projectid = "", $eventid = "", $limit = 9, $page = 1, $search = "", $is_kanban = false)
    {
        if ($this->input->get('pid')) {
            $eventid = $projectid = $this->input->get('pid');
        }
        if ($this->input->get('lid')) {
            $leadid = $this->input->get('lid');
        }
        $this->db->query('SET sql_mode = ""');
        $brandid = get_user_session();
        if (isset($projectid) && $projectid > 0) {
            $this->db->select('id');
            $this->db->where('(parent = ' . $projectid . ' OR id = ' . $projectid . ')');
            $this->db->where('deleted', 0);
            $related_project_ids = $this->db->get('tblprojects')->result_array();
            $related_project_ids = array_column($related_project_ids, 'id');
            $related_project_ids = implode(",", $related_project_ids);
        } else {
            $related_project_ids = "";
        }
        if ($brandid > 0 && $leadid != "") {
            $this->db->join('tblleadcontact', 'tblleadcontact.contactid = tbladdressbook.addressbookid');
            /*$this->db->join('tbladdressbook_client', 'tbladdressbook_client.addressbookid = tbladdressbook.addressbookid');
            $this->db->where('tbladdressbook_client.brandid', $brandid);
            $this->db->where('tbladdressbook_client.deleted', 0);*/
            if ($leadid != "") {
                $this->db->where('tblleadcontact.leadid', $leadid);
            }
        } elseif ($brandid > 0 && $projectid != "") {
            $this->db->join('tblprojectcontact', 'tblprojectcontact.contactid = tbladdressbook.addressbookid');
            /*$this->db->join('tbladdressbook_client', 'tbladdressbook_client.addressbookid = tbladdressbook.addressbookid');
            $this->db->where('tbladdressbook_client.brandid', $brandid);
            $this->db->where('tbladdressbook_client.deleted', 0);*/
            if (!empty($related_project_ids)) {
                $this->db->where('(tblprojectcontact.projectid in (' . $related_project_ids . ') OR tblprojectcontact.eventid in (' . $related_project_ids . '))');
            } else {
                $this->db->where('tblprojectcontact.projectid', $projectid);
            }
        } elseif ($brandid > 0 && $eventid != "") {
            $this->db->join('tblprojectcontact', 'tblprojectcontact.contactid = tbladdressbook.addressbookid');
            /*$this->db->join('tbladdressbook_client', 'tbladdressbook_client.addressbookid = tbladdressbook.addressbookid');
            $this->db->where('tbladdressbook_client.brandid', $brandid);
            $this->db->where('tbladdressbook_client.deleted', 0);*/
            if ($eventid != "") {
                $this->db->where('tblprojectcontact.eventid', $eventid);
            }
        } else {
            /*if ($brandid > 0) {
                $this->db->join('tbladdressbook_client', 'tbladdressbook_client.addressbookid = tbladdressbook.addressbookid');
                $this->db->where('tbladdressbook_client.brandid', $brandid);
                $this->db->where('tbladdressbook_client.deleted', 0);
            }*/
        }
        if ($brandid > 0) {
            $this->db->join('tbladdressbook_client', 'tbladdressbook_client.addressbookid = tbladdressbook.addressbookid');
            $this->db->where('tbladdressbook_client.brandid', $brandid);
            $this->db->where('tbladdressbook_client.deleted', 0);
            $this->db->where('(tbladdressbook.ispublic=1 OR tbladdressbook.brandid=' . get_user_session() . ')');
        }
        if ($is_kanban == true && $limit > 0) {
            $start = ($page - 1) * $limit;
            $this->db->limit($limit, $start);
        }
        if (!empty($search)) {
            $this->db->where('(tbladdressbook.firstname LIKE "%' . $search . '%" OR tbladdressbook.lastname LIKE "%' . $search . '%")');
            //$this->db->or_like('tbladdressbook.lastname', $search);
        }
        $user_id = get_staff_user_id();
        $this->db->group_by('tbladdressbook.addressbookid');
        $this->db->where('tbladdressbook.deleted', 0);
        $this->db->where('(tbladdressbook.ispublic=1 OR tbladdressbook.created_by=' . $user_id . ')');
        $this->db->order_by("tbladdressbook.firstname", "ASC");
        $result = $this->db->get('tbladdressbook')->result_array();
        return $result;
    }

    /**
     * Added By: Munir
     * Dt: 11/27/2018
     * For all lead Contact
     */

    public function get_lead_contacts($leadid)
    {
        $brandid = get_user_session();
        $this->db->join('tblleadcontact', 'tblleadcontact.contactid = tbladdressbook.addressbookid');
        $this->db->join('tbladdressbookemail', 'tbladdressbookemail.addressbookid = tbladdressbook.addressbookid');
        $this->db->join('tbladdressbook_client', 'tbladdressbook_client.addressbookid = tbladdressbook.addressbookid');
        $this->db->where('tblleadcontact.leadid', $leadid);
        $this->db->where('tbladdressbook_client.brandid', $brandid);
        $this->db->where('tbladdressbook_client.deleted', 0);
        $this->db->where('(tbladdressbook.ispublic=1 OR tbladdressbook.brandid=' . get_user_session() . ')');
        $user_id = get_staff_user_id();
        $this->db->group_by('tbladdressbook.addressbookid');
        $this->db->where('tbladdressbook.deleted', 0);
        $this->db->where('(tbladdressbook.ispublic=1 OR tbladdressbook.created_by=' . $user_id . ')');
        $this->db->order_by("tbladdressbook.firstname", "ASC");
        $result = $this->db->get('tbladdressbook')->result_array();
        return $result;
    }

    function get_favorite($id)
    {
        $session_data = get_session_data();
        $user_id = $session_data['staff_user_id'];
        $favorit = $this->db->select('favoriteid')->from('tblfavorites')->where('favtype = "Addressbook" AND typeid=' . $id . ' AND userid=' . $user_id)->get()->row();
        return $favorit;
    }

    /**
     * Added By: Masud
     * Dt: 10/04/2018
     * For add existing Contact
     * For add existing Contact
     */

    public function get_existing_contacts($reltbl = "", $reltype = "", $relid = 0)
    {
        $brandid = get_user_session();
        $current_user = get_staff_user_id();
        $query = "SELECT  CONCAT(a.firstname,' ',a.lastname) as name, a.* ae.email FROM `tbladdressbook` a LEFT JOIN tbladdressbookemail ae ON a.addressbookid= ae.addressbookid WHERE a.addressbookid NOT IN(SELECT ac.addressbookid from tbladdressbook_client as ac WHERE ac.brandid = $brandid and ac.deleted = 0) AND a.`ispublic` = 1 AND a.`deleted` = 0";
        if (!empty($reltype) && $relid > 0) {
            $query = "SELECT CONCAT(a.firstname,' ',a.lastname) as name, a.*, ae.email FROM `tbladdressbook` a LEFT JOIN tbladdressbookemail ae ON a.addressbookid= ae.addressbookid WHERE a.addressbookid IN(SELECT ac.addressbookid from tbladdressbook_client as ac WHERE ac.brandid = $brandid and ac.deleted = 0) AND a.addressbookid NOT IN(SELECT rc.contactid from $reltbl as rc WHERE rc.brandid = $brandid and rc.$reltype = $relid) AND (a.`ispublic` = 1 OR a.created_by=$current_user) AND a.`deleted` = 0 AND ae.`type`='primary' ";
        } else {
            $query = "SELECT CONCAT(a.firstname,' ',a.lastname) as name , a.*, ae.email FROM `tbladdressbook` a LEFT JOIN tbladdressbookemail ae ON a.addressbookid= ae.addressbookid WHERE a.addressbookid IN(SELECT ac.addressbookid from tbladdressbook_client as ac WHERE ac.brandid = $brandid and ac.deleted = 0) AND (a.`ispublic` = 1 OR a.created_by=$current_user) AND a.`deleted` = 0 AND ae.`type`='primary'";
        }
        $result = $this->db->query($query);
        $rows = $result->result_array();
        return $rows;

    }

    public function get_my_existing_contacts()
    {
        $brandid = get_user_session();
        $current_user = get_staff_user_id();
        $query = "SELECT a.* FROM `tbladdressbook` a WHERE a.addressbookid NOT IN(SELECT ac.addressbookid from tbladdressbook_client as ac WHERE ac.brandid = $brandid and ac.deleted = 0) AND a.`ispublic` = 1 AND a.`deleted` = 0";

        $query = "SELECT CONCAT(a.firstname,' ',a.lastname) as name, a.*, ae.email FROM `tbladdressbook` a LEFT JOIN tbladdressbookemail ae ON a.addressbookid= ae.addressbookid WHERE a.addressbookid IN(SELECT ac.addressbookid from tbladdressbook_client as ac WHERE ac.brandid = $brandid and ac.deleted = 0) AND (a.`ispublic` = 1 OR a.brandid=$brandid) AND a.`deleted` = 0 AND ae.`type`='primary'";
        $result = $this->db->query($query);
        $rows = $result->result_array();
        return $rows;

    }

    function delete_project_contact($id, $projectid)
    {
        $this->db->where('contactid', $id);
        $this->db->where('projectid', $projectid);
        return $this->db->delete('tblprojectcontact');

    }

    function delete_lead_contact($id, $leadid)
    {
        $this->db->where('contactid', $id);
        $this->db->where('leadid', $leadid);
        return $this->db->delete('tblleadcontact');
    }

    function addrelcontact($data)
    {
        if (isset($data['projectid']) && $data['projectid'] > 0) {
            $this->db->insert('tblprojectcontact', $data);
            $insert_id = $this->db->insert_id();
            return $insert_id;
        } else {
            $this->db->insert('tblleadcontact', $data);
            $insert_id = $this->db->insert_id();
            return $insert_id;
        }
        return false;
    }
}