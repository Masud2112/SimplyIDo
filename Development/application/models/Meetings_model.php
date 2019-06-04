<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Meetings_model extends CRM_Model
{
    private $perm_statements = array('view', 'view_own', 'edit', 'create', 'delete');

    public function __construct()
    {
        parent::__construct();
        $this->load->model('venues_model');
    }

    /**
     * Add new employee meeting
     * @param mixed $data
     */
    public function add($data)
    {
        if (isset($data['newlocation'])) {
            $location = array();
            $location['location_name'] = $data['location_name'];
            $location['address'] = $data['newlocation']['street_number'];
            $location['address2'] = $data['newlocation']['route'];
            $location['city'] = $data['newlocation']['locality'];
            $location['state'] = $data['newlocation']['administrative_area_level_1'];
            $location['zip'] = $data['newlocation']['postal_code'];
            $location['brandid'] = get_user_session();
            if($location['location_name']!="" && $location['address']!="" && $location['address2']!="" && $location['city']!="" && $location['state']!="" && $location['zip']!=""){
                $this->db->insert('tblmeetinglocations', $location);
                $data['location'] = $this->db->insert_id();
            }
        } elseif (isset($data['location']) && is_array($data['location'])) {
            $location = array();
            $location['location_name'] = $data['location_name'];
            $location['address'] = $data['location']['street_number'];
            $location['address2'] = $data['location']['route'];
            $location['city'] = $data['location']['locality'];
            $location['state'] = $data['location']['administrative_area_level_1'];
            $location['zip'] = $data['location']['postal_code'];
            $location['brandid'] = get_user_session();
            $this->db->where('locationid', $data['location']['locationid']);
            $this->db->update('tblmeetinglocations', $location);
            $data['location'] = $data['location']['locationid'];
        }
        unset($data['pg']);
        unset($data['location_name']);
        unset($data['newlocation']);

        if (isset($data['rel_type']) && $data['rel_type'] != "") {
            $data['rel_type'] = $data['rel_type'];
            $data['rel_id'] = $data[$data['rel_type']];
        } else {
            $data['rel_type'] = "";
            $data['rel_id'] = "";
        }
        unset($data['lead']);
        unset($data['project']);
        unset($data['event']);
        //echo "<pre>";print_r($data);exit;
        // $meetings = $this->meetings_model->check_meeting_name_exists($data['name'], '');
        // if($meetings->meetingid <= 0 || empty($meetings)) {

        /**
         * Added By : Vaidehi
         * Dt : 10/25/2017
         * for mulitple meeting reminders
         */
        $newmeetingreminders = array_filter($data['reminder'], function ($var) {
            return ($var['duration'] != '');
        });

        //$data['start_date'] = date("Y-m-d H:i:s", strtotime($data['start_date']));
        //$data['end_date'] = date("Y-m-d H:i:s", strtotime($data['end_date']));

        $start_date = str_replace('/', '-', $data['start_date']);
        if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $start_date)) {
            $data['start_date'] = ((isset($data['start_date']) && !empty($data['start_date'])) ? date("Y-m-d H:i:s", strtotime($data['start_date'])) : "");
        } else {
            if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                $data['start_date'] = ((isset($data['start_date']) && !empty($data['start_date'])) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $data['start_date']))) : "");
            } else {
                $data['start_date'] = ((isset($data['start_date']) && !empty($data['start_date'])) ? date("Y-m-d H:i:s", strtotime($data['start_date'])) : "");
            }
        }

        $end_date = str_replace('/', '-', $data['end_date']);
        if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $end_date)) {
            $data['end_date'] = ((isset($data['end_date']) && !empty($data['end_date'])) ? date("Y-m-d H:i:s", strtotime($data['end_date'])) : "");
        } else {
            if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                $data['end_date'] = ((isset($data['end_date']) && !empty($data['end_date'])) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $data['end_date']))) : "");
            } else {
                $data['end_date'] = ((isset($data['end_date']) && !empty($data['end_date'])) ? date("Y-m-d H:i:s", strtotime($data['end_date'])) : "");
            }
        }

        $newmeetingreminders = (count($newmeetingreminders) > 0 ? $newmeetingreminders : '');
        unset($data['reminder']);
        unset($data['hdnlid']);
        unset($data['hdnpid']);
        unset($data['hdneid']);

        $data['created_by'] = $this->session->userdata['staff_user_id'];
        $data['created_date'] = date('Y-m-d H:i:s');
        $data['brandid'] = get_user_session();

        $users = $contacts = $leads = array();
        if (isset($data['users'])) {
            $users = $data['users'];
            unset($data['users']);
        }
        if (isset($data['contacts'])) {
            $contacts = $data['contacts'];
            unset($data['contacts']);
        }
        // if (isset($data['leads'])) {
        //     $leads = $data['leads'];
        //     unset($data['leads']);
        // }
        $data['location'] = (int)$data['location'];
        $this->db->insert('tblmeetings', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            /**
             * Added By : Vaidehi
             * Dt : 10/25/2017
             * for mulitple meeting reminders
             */
            $this->new_meeting_notification($insert_id, $users, $contacts, $data['rel_type'], $data['rel_id']);

            if (isset($newmeetingreminders) && !empty($newmeetingreminders)) {
                foreach ($newmeetingreminders as $newmeetingreminder) {
                    $newmeetingreminder['meetingid'] = $insert_id;
                    $this->db->insert('tblmeetingreminders', $newmeetingreminder);
                }
            }

            if (!empty($users)) {
                foreach ($users as $u) {
                    $this->db->insert('tblmeetingusers', array(
                        'meeting_id' => $insert_id,
                        'user_id' => $u
                    ));
                }
            }
            if (!empty($contacts)) {
                foreach ($contacts as $c) {
                    $this->db->insert('tblmeetingusers', array(
                        'meeting_id' => $insert_id,
                        'contact_id' => $c
                    ));
                }
            }
            // if(!empty($leads)){
            //     foreach ($leads as $l) {

            //         $this->db->insert('tblmeetingusers', array(
            //             'meeting_id' => $insert_id,
            //             'lead_id' => $l
            //         ));

            //         *
            //         * Added By : Vaidehi
            //         * Dt : 11/16/2017
            //         * to make log entry in lead activity log table

            //         $meeting_user_id = $this->db->insert_id();

            //         if(!empty($l) && !empty($meeting_user_id)) {
            //             $this->load->model('leads_model');
            //             $this->leads_model->log_lead_activity($l, 'Meeting Added');
            //         }
            //     }
            // }

            $final_user_contact_array = array();
            foreach ($users as $u) {
                $this->db->select('CONCAT(tblstaff.firstname, " ", tblstaff.lastname) AS meeting_user_name,email');
                $this->db->where('staffid', $u);
                $user_array = $this->db->get('tblstaff')->row();
                $final_user_contact_array[] = $user_array;
            }

            foreach ($contacts as $c) {
                $this->db->select('CONCAT(tbladdressbook.firstname, " ", tbladdressbook.lastname) as meeting_user_name, tbladdressbookemail.email');
                $this->db->join('tbladdressbookemail', 'tbladdressbookemail.addressbookid=tbladdressbook.addressbookid', 'left');
                $this->db->where('tbladdressbook.deleted', 0);
                $this->db->where('tbladdressbookemail.type', 'primary');
                $this->db->where('tbladdressbookemail.type != ""');
                $this->db->where('tbladdressbook.addressbookid', $c);
                $contact_array = $this->db->get('tbladdressbook')->row();
                $final_user_contact_array[] = $contact_array;
            }
            $this->load->model('emails_model');
            $merge_fields = array();

            $template = 'meetings-send-notification';
            $merge_fields = array_merge($merge_fields, get_meetings_merge_fields($insert_id));

            foreach ($final_user_contact_array as $fvalue) {
                // $merge_fields['{meeting_user_name}'] = $fvalue->meeting_user_name;
                $merge_fields['{meeting_attendees}'] = $fvalue->meeting_user_name;
                $send = $this->emails_model->send_email_template($template, $fvalue->email, $merge_fields);
            }

            logActivity('New Meeting Added [ID: ' . $insert_id . '.' . $data['name'] . ']');

            return $insert_id;
        }
        // }
        return false;
    }

    public function new_meeting_notification($meeting_id, $users, $contacts, $rel_type, $rel_id, $integration = false)
    {
        $name = "Global";
        $description = "not_new_meeting_created";
        if ($rel_id > 0) {
            ($rel_type == 'lead') ? $table = 'tblleads' : $table = 'tblprojects';
            $name = $this->db->select('name')->from($table)->where('id', $rel_id)->get()->row()->name;
            ($rel_type == 'lead') ? $description = 'not_new_lead_meeting_created' : $description = 'not_new_project_meeting_created';
        }

        $tousers = implode(',', $users);
        $tocontacts = implode(',', $contacts);
        if ($rel_id == "") {
            $rel_id = 0;
        }
        $notification_data = array(
            //'description' => ($integration == false) ? 'not_new_meeting_created' : 'not_new_meeting_created',
            'description' => $description,
            'touserid' => $tousers,
            'tocontactid' => $tocontacts,
            'eid' => $meeting_id,
            'brandid' => get_user_session(),
            'not_type' => 'meetings',
            'link' => 'meetings/meeting/' . $meeting_id,
            'additional_data' => ($integration == false ? serialize(array(
                $name
            )) : serialize(array()))
        );

        if ($integration != false) {
            $notification_data['fromcompany'] = 1;
        }

        if (isset($users)) {
            if (add_notification($notification_data)) {
                pusher_trigger_notification($users);
            }
        }
    }

    public function meeting_status_changed_notification($meeting_id, $users, $contacts, $rel_type, $rel_id, $staus, $integration = false)
    {
        $name = "Global";
        if ($rel_id > 0) {
            ($rel_type == 'lead') ? $table = 'tblleads' : $table = 'tblprojects';
            $name = $this->db->select('name')->from($table)->where('id', $rel_id)->get()->row()->name;
        }

        $tousers = implode(',', $users);
        $tocontacts = implode(',', $contacts);
        if ($rel_id == "") {
            $rel_id = 0;
        }
        $notification_data = array(
            'description' => ($integration == false) ? 'not_meeting_status_changed' : 'not_meeting_status_changed',
            'touserid' => $tousers,
            'tocontactid' => $tocontacts,
            'eid' => $meeting_id,
            'isread' => 0,
            'isread_inline' => 0,
            'brandid' => get_user_session(),
            'not_type' => 'meetings',
            'link' => 'meetings/meeting/' . $meeting_id,
            'additional_data' => ($integration == false ? serialize(array(
                $name, $staus
            )) : serialize(array()))
        );

        if ($integration != false) {
            $notification_data['fromcompany'] = 1;
        }

        if (add_notification($notification_data)) {
            pusher_trigger_notification($users);
        }
    }

    /**
     * Update employee meeting
     * @param  array $data meeting data
     * @param  mixed $id meeting id
     * @return boolean
     */
    public function update($data, $id)
    {

        if (isset($data['newlocation'])) {
            $location = array();
            $location['location_name'] = $data['location_name'];
            $location['address'] = $data['newlocation']['street_number'];
            $location['address2'] = $data['newlocation']['route'];
            $location['city'] = $data['newlocation']['locality'];
            $location['state'] = $data['newlocation']['administrative_area_level_1'];
            $location['zip'] = $data['newlocation']['postal_code'];
            $location['brandid'] = get_user_session();

            if($location['location_name']!="" && $location['address']!="" && $location['address2']!="" && $location['city']!="" && $location['state']!="" && $location['zip']!=""){
                $this->db->insert('tblmeetinglocations', $location);
                $data['location'] = $this->db->insert_id();
            }
            //$data['location'] = $this->db->insert_id();
            unset($data['newlocation']);
        } elseif (isset($data['location']) && is_array($data['location'])) {
            $location = array();
            $location['location_name'] = $data['location_name'];
            $location['address'] = $data['location']['street_number'];
            $location['address2'] = $data['location']['route'];
            $location['city'] = $data['location']['locality'];
            $location['state'] = $data['location']['administrative_area_level_1'];
            $location['zip'] = $data['location']['postal_code'];
            $location['brandid'] = get_user_session();
            $this->db->where('locationid', $data['location']['locationid']);
            $this->db->update('tblmeetinglocations', $location);
            $data['location'] = $data['location']['locationid'];
        }
        unset($data['pg']);
        unset($data['location_name']);

        if (isset($data['rel_type']) && $data['rel_type'] != "") {
            $data['rel_type'] = $data['rel_type'];
            $data['rel_id'] = $data[$data['rel_type']];
        } else {
            $data['rel_type'] = "";
            $data['rel_id'] = "";
        }
        unset($data['lead']);
        unset($data['project']);
        unset($data['event']);
        // $meetings = $this->meetings_model->check_meeting_name_exists($data['name'], $id);
        // if($meetings->meetingid <= 0 || empty($meetings)) {
        /**
         * Added By : Vaidehi
         * Dt : 10/25/2017
         * for mulitple meeting reminders
         */
        $newmeetingreminders = array_filter($data['reminder'], function ($var) {
            return ($var['duration'] != '');
        });

        $newmeetingreminders = (count($newmeetingreminders) > 0 ? $newmeetingreminders : '');
        unset($data['reminder']);
        unset($data['hdnlid']);
        unset($data['hdnpid']);
        unset($data['hdneid']);
        unset($data['note']);

        //$data['start_date'] = date("Y-m-d H:i:s", strtotime($data['start_date']));

        $start_date = str_replace('/', '-', $data['start_date']);
        if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $start_date)) {
            $data['start_date'] = ((isset($data['start_date']) && !empty($data['start_date'])) ? date("Y-m-d H:i:s", strtotime($data['start_date'])) : "");
        } else {
            if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                $data['start_date'] = ((isset($data['start_date']) && !empty($data['start_date'])) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $data['start_date']))) : "");
            } else {
                $data['start_date'] = ((isset($data['start_date']) && !empty($data['start_date'])) ? date("Y-m-d H:i:s", strtotime($data['start_date'])) : "");
            }
        }

        //$data['end_date'] = date("Y-m-d H:i:s", strtotime($data['end_date']));

        $end_date = str_replace('/', '-', $data['end_date']);
        if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $end_date)) {
            $data['end_date'] = ((isset($data['end_date']) && !empty($data['end_date'])) ? date("Y-m-d H:i:s", strtotime($data['end_date'])) : "");
        } else {
            if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                $data['end_date'] = ((isset($data['end_date']) && !empty($data['end_date'])) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $data['end_date']))) : "");
            } else {
                $data['end_date'] = ((isset($data['end_date']) && !empty($data['end_date'])) ? date("Y-m-d H:i:s", strtotime($data['end_date'])) : "");
            }
        }

        $affectedRows = 0;

        $users = $contacts = $leads = array();

        if (isset($data['users'])) {
            $users = $data['users'];
            unset($data['users']);
        }
        if (isset($data['contacts'])) {
            $contacts = $data['contacts'];
            unset($data['contacts']);
        }

        // if (isset($data['leads'])) {
        //     $leads = $data['leads'];
        //     unset($data['leads']);
        // }

        $this->db->where('meeting_id', $id);
        $this->db->delete('tblmeetingusers');

        $data['updated_by'] = $this->session->userdata['staff_user_id'];
        $data['updated_date'] = date('Y-m-d H:i:s');
        $data['location'] = (int)$data['location'];
        /*echo "<pre>";
        print_r($data);
        die('<--here');*/
        $this->db->where('meetingid', $id);
        $this->db->update('tblmeetings', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        /**
         * Added By : Vaidehi
         * Dt : 10/25/2017
         * for mulitple meeting reminders
         */
        if (count($newmeetingreminders) > 0) {
            $this->db->where('meetingid', $id);
            $this->db->delete('tblmeetingreminders');

            foreach ($newmeetingreminders as $newmeetingreminder) {
                $newmeetingreminder['meetingid'] = $id;
                $this->db->insert('tblmeetingreminders', $newmeetingreminder);
            }
        }

        if (!empty($users)) {
            foreach ($users as $u) {
                $this->db->insert('tblmeetingusers', array(
                    'meeting_id' => $id,
                    'user_id' => $u
                ));

                /**
                 * Added By : Vaidehi
                 * Dt : 11/16/2017
                 * to make log entry in lead activity log table
                 */
                $meeting_user_id = $this->db->insert_id();

                if (!empty($l) && !empty($meeting_user_id)) {
                    $this->load->model('leads_model');
                    $this->leads_model->log_lead_activity($l, 'Meeting Updated');
                }
            }
        }
        if (!empty($contacts)) {
            foreach ($contacts as $c) {
                $this->db->insert('tblmeetingusers', array(
                    'meeting_id' => $id,
                    'contact_id' => $c
                ));
            }
        }
        // if(!empty($leads)){
        //     foreach ($leads as $l) {
        //         $this->db->insert('tblmeetingusers', array(
        //             'meeting_id' => $id,
        //             'lead_id' => $l
        //         ));
        //     }
        // }

        if ($affectedRows > 0) {
            logActivity('Meeting Updated [ID: ' . $id . '.' . $data['name'] . ']');

            return true;
        }
        // }
        return false;
    }

    /**
     * Get employee meeting by id
     * @param  mixed $id Optional meeting id
     * @return mixed     array if not id passed else object
     */
    public function get($id = '', $where = array())
    {
        $brandid = get_user_session();

        $this->db->where('tblmeetings.deleted', 0);
        if ($brandid > 0) {
            array_push($where, ' AND brandid =' . $brandid);
        }
        if (is_numeric($id)) {
            $this->db->where('tblmeetings.meetingid', $id);
            $meeting = $this->db->get('tblmeetings')->row();
            if ($meeting->location_type == 'venue') {
                $location = $this->venues_model->get($meeting->location);
            } else {
                $this->db->where('locationid', $meeting->location);
                $this->db->where('deleted', 0);
                $location = $this->db->get('tblmeetinglocations')->row();
            }
            $meeting->location = $location;
            return $meeting;
        }
        $this->db->order_by('name', 'asc');
        return $this->db->get('tblmeetings')->result_array();
    }

    public function get_meeting_users($id = '', $type = "")
    {
        $this->db->select($type);
        $this->db->where('meeting_id', $id);
        $this->db->where($type . ' !=', 'NULL');
        $result_array = $this->db->get('tblmeetingusers')->result_array();
        $final_array = array_column($result_array, $type);
        return $final_array;
    }

    /**
     * Added By : Vaidehi
     * Dt : 10/25/2017
     * to get meeting reminders
     */
    public function get_meeting_usersreminder($id = '')
    {
        $this->db->where('meetingid', $id);
        $result_array = $this->db->get('tblmeetingreminders')->result_array();
        return $result_array;
    }

    /**
     * Delete employee meeting
     * @param  mixed $id meeting id
     * @return mixed
     */
    public function delete($id, $type = "")
    {
        $current = $this->get($id);
        // // Check first if meeting is used in table
        // if (is_reference_in_table('meeting', 'tblstaff', $id)) {
        //     return array(
        //         'referenced' => true
        //     );
        // }
        $affectedRows = 0;
        // $this->db->where('meetingid', $id);
        // $this->db->delete('tblmeetings');
        $data['deleted'] = 1;
        $data['updated_by'] = $this->session->userdata['staff_user_id'];
        $data['updated_date'] = date('Y-m-d H:i:s');

        /*For soft delete meeting while brand delete*/
        if ($type == "subscription") {
            $this->db->where('brandid', $id);
        } else {
            $this->db->where('meetingid', $id);
        }

        $this->db->update('tblmeetings', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        // $this->db->where('meetingid', $id);
        // $this->db->delete('tblmeetingpermissions');
        // if ($this->db->affected_rows() > 0) {
        //     $affectedRows++;
        // }
        if ($affectedRows > 0) {
            logActivity('Meeting Deleted [ID: ' . $id);

            return true;
        }

        return false;
    }


    public function get_meeting_status()
    {
        $this->db->where('deleted', 0);

        return $this->db->get('tblmeetingstatus')->result_array();
    }

    public function get_users()
    {
        $brandid = get_user_session();

        $this->db->where('tblstaff.deleted', 0);
        $this->db->where('tblstaff.is_not_staff', 0);
        $this->db->where('tblstaff.active', 1);
        if ($brandid > 0) {
            $this->db->join('tblstaffbrand', 'tblstaffbrand.staffid = tblstaff.staffid');
            $this->db->where('tblstaffbrand.brandid', $brandid);
        }

        return $this->db->get('tblstaff')->result_array();
    }

    /* Added by Purvi on 01/16/2018 for Lead and Project condition*/
    public function get_contacts($leadid = "", $projectid = "", $eventid = "")
    {
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

            if ($leadid != "") {
                $this->db->where('tblleadcontact.leadid', $leadid);
            }
        } elseif ($brandid > 0 && $projectid != "") {
            $this->db->join('tblprojectcontact', 'tblprojectcontact.contactid = tbladdressbook.addressbookid');
            if (!empty($related_project_ids)) {
                $this->db->where('(tblprojectcontact.projectid in (' . $related_project_ids . ') OR tblprojectcontact.eventid in (' . $related_project_ids . '))');
            } else {
                $this->db->where('tblprojectcontact.projectid', $projectid);
            }
        } elseif ($brandid > 0 && $eventid != "") {
            $this->db->join('tblprojectcontact', 'tblprojectcontact.contactid = tbladdressbook.addressbookid');
            if ($eventid != "") {
                $this->db->where('tblprojectcontact.eventid', $eventid);
            }
        }
        if ($brandid > 0) {
            $this->db->join('tbladdressbook_client', 'tbladdressbook_client.addressbookid = tbladdressbook.addressbookid');
            $this->db->where('tbladdressbook_client.brandid', $brandid);
            $this->db->where('tbladdressbook_client.deleted', 0);
            $this->db->where('(tbladdressbook.ispublic=1 OR tbladdressbook.brandid=' . get_user_session() . ')');
        }
        $this->db->group_by('tbladdressbook.addressbookid');
        $this->db->where('tbladdressbook.deleted', 0);
        return $this->db->get('tbladdressbook')->result_array();
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
        /*
        $this->db->select('id,name');
        $this->db->where('brandid',  get_user_session());
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

    /**
     * Get meeting id
     * @param  mixed $id permission id
     * @return mixed if id passed return object else array
     */
    public function check_meeting_name_exists($name, $id)
    {
        if ($id > 0) {
            $where = array('meetingid !=' => $id, 'name =' => $name, 'deleted =' => 0);
        } else {
            $where = array('name =' => $name, 'deleted =' => 0);
        }
        return $this->db->where($where)->get('tblmeetings')->row();
    }

    /**
     * Added By: Vaidehi
     * Dt: 03/04/2018
     * get cron meetings
     */
    public function get_cronmeetings()
    {
        $query = $this->db->query('SELECT `tblmeetings`.*, DATE_FORMAT(`tblmeetings`.`start_date`, "%m/%d/%Y %H:%i") AS startdate, DATE_FORMAT(`tblmeetings`.`end_date`, "%m/%d/%Y %H:%i") AS enddate, `tblmeetingusers`.`user_id`, `tblmeetingusers`.`contact_id`, `tblstaff`.`firstname`, `tblstaff`.`lastname`, `tblstaff`.`email`, `tbladdressbook`.`firstname` AS contactfirstname, `tbladdressbook`.`lastname` AS contactlastname, `tbladdressbookemail`.`email` AS contactemail, s1.`firstname` AS assigned_firstname, s1.`lastname` AS assigned_lastname, s1.`email` AS assigned_email FROM `tblmeetings` LEFT JOIN `tblmeetingusers` ON `tblmeetings`.`meetingid` = `tblmeetingusers`.`meeting_id` LEFT JOIN `tblstaff` ON `tblmeetingusers`.`user_id` = `tblstaff`.`staffid` LEFT JOIN `tblstaff` s1 ON (`tblmeetings`.`created_by` = s1.`staffid` OR `tblmeetings`.`updated_by` = s1.`staffid`) LEFT JOIN `tbladdressbook` ON `tbladdressbook`.`addressbookid` = `tblmeetingusers`.`contact_id` LEFT JOIN `tbladdressbookemail` ON `tbladdressbookemail`.`addressbookid` = `tbladdressbook`.`addressbookid` AND `tbladdressbookemail`.`type` = "primary" LEFT JOIN `tbladdressbook_client` ON (`tbladdressbook_client`.`addressbookid` = `tbladdressbook`.`addressbookid` AND `tbladdressbook_client`.`deleted` = 0) WHERE `tblmeetings`.`deleted` = 0');

        $response = $query->result_array();
        return $response;
    }

    /**
     * Added By : Masud
     * Dt : 06/11/2018
     * for Meeeting kanban view
     */
    public function do_meeting_kanban_query($status, $sort = array(), $count = false, $limit = "", $page = 1, $is_kanban = false, $search = '')
    {
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
        //$limit                              = get_option('projects_kanban_limit');
        $default_projects_kanban_sort = get_option('default_projects_kanban_sort');
        $default_projects_kanban_sort_type = get_option('default_projects_kanban_sort_type');

        $brandid = get_user_session();
        $this->db->select('tblmeetings.*,tblmeetingstatus.name as status_name, (SELECT location_name FROM tblmeetinglocations WHERE tblmeetinglocations.locationid = tblmeetings.location) as location');
        $this->db->join('tblmeetingstatus', 'tblmeetingstatus.statusid=tblmeetings.status', 'left');
        $this->db->where('tblmeetings.deleted', 0);
        $this->db->where('tblmeetings.brandid', $brandid);
        if ($this->input->get('lid')) {
            $this->db->where('tblmeetings.rel_type', 'lead');
            $this->db->where('tblmeetings.rel_id', $this->input->get('lid'));
        } elseif ($this->input->get('pid')) {
            $this->db->where('tblmeetings.rel_type', 'project');
            $this->db->where('tblmeetings.rel_id', $this->input->get('pid'));
        } elseif ($this->input->get('eid')) {
            $this->db->where('tblmeetings.rel_type', 'project');
            $this->db->where('tblmeetings.rel_id', $this->input->get('pid'));
        }
        $this->db->order_by('tblmeetings.start_date', 'DESC');
        if ($is_kanban == true && $limit > 0) {
            $start = ($page - 1) * $limit;
            $this->db->limit($limit, $start);
        }
        if (!empty($search)) {
            $this->db->like('tblmeetings.name', $search);
        }
        $meetings = $this->db->get('tblmeetings')->result_array();
        return $meetings;
    }

    /**
     * Added By: Masud
     * Dt: 06/21/2018
     * For Pin/Unpin Meeting
     */
    public function pinmeeting($meeting_id)
    {
        $session_data = get_session_data();
        $user_id = $session_data['staff_user_id'];

        $pinexist = $this->db->select('pinid')->from('tblpins')->where('pintype = "Meeting" AND pintypeid = ' . $meeting_id . ' AND userid = ' . $user_id)->get()->row();
        if (!empty($pinexist)) {
            $this->db->where('userid', $user_id);
            $this->db->where('pintypeid', $meeting_id);
            $this->db->where('pintype', "Meeting");
            $this->db->delete('tblpins');

            return 0;
        } else {
            $this->db->insert('tblpins', array(
                'pintype' => "Meeting",
                'pintypeid' => $meeting_id,
                'userid' => $user_id
            ));

            return 1;
        }
    }


    /**
     * Added By: Dipak
     * Dt: 25/06/2018
     * For Mask delete meeting
     */
    public function maskdelete($id_Array)
    {
        $affectedRows = 0;
        $data['deleted'] = 1;
        $data['updated_by'] = $this->session->userdata['staff_user_id'];
        $data['updated_date'] = date('Y-m-d H:i:s');


        $this->db->where_in('meetingid', $id_Array);
        $this->db->update('tblmeetings', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }
        if ($affectedRows > 0) {
            logActivity('Meeting Deleted [ID: ' . print_r($id_Array));
            echo 'Meeting has been deleted [ID: ' . print_r($id_Array);
            return true;
        }

        return false;
    }

    function get_locations()
    {
        $this->db->where('brandid', get_user_session());
        $this->db->where('deleted', 0);
        return $this->db->get('tblmeetinglocations')->result();
    }
    function get_location($id)
    {
        $this->db->where('locationid', $id);
        return $this->db->get('tblmeetinglocations')->row();
    }
    
/*
Start Code
Added by Munir
Dt:11/26/2018
*/
function addloc()
{

    $data=array(
        'location_name'=>$_POST['location_name'],
        'address'=>$_POST['address'],
        'address2'=>$_POST['address2'],
        'city'=>$_POST['city'],
        'state'=>$_POST['state'],
        'zip'=>$_POST['zip'],
        'brandid'=>get_user_session()
    );
    $this->db->insert('tblmeetinglocations', $data);
    return $this->db->insert_id();
}

function editloc()
{
    $data=array(
        'location_name'=>$_POST['location_name'],
        'address'=>$_POST['address'],
        'address2'=>$_POST['address2'],
        'city'=>$_POST['city'],
        'state'=>$_POST['state'],
        'zip'=>$_POST['zip']
    );
    $this->db->where('locationid', $_POST['locationid']); // here is the id
    $this->db->update('tblmeetinglocations', $data);
    return $this->db->affected_rows();
}
    /*
    End Code
    Added by Munir
    Dt:11/26/2018
    */

    function deleteloc($id)
    {
        $this->db->where('locationid', $id);
        $this->db->update('tblmeetinglocations',array('deleted'=>1));
        return $this->db->affected_rows();
    }
}