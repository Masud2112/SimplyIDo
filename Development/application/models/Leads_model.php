<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Leads_model extends CRM_Model
{
    private $is_admin;

    public function __construct()
    {
        parent::__construct();
        $this->is_admin = is_admin();
    }

    /**
     * Get lead
     * @param  string $id Optional - leadid
     * @return mixed
     */
    public function get($id = '', $where = array())
    {
        $this->db->select('*,tblleads.name, tblleads.id,tblleadsstatus.name as status_name,tblleadssources.name as source_name,tbleventtype.eventtypename');
        $this->db->join('tblleadsstatus', 'tblleadsstatus.id=tblleads.status', 'left');
        $this->db->join('tblleadssources', 'tblleadssources.id=tblleads.source', 'left');
        $this->db->join('tbleventtype', 'tbleventtype.eventtypeid=tblleads.eventtypeid', 'left');

        //added by vaidehi on 03/08/2018 
        $this->db->where('converted', 0);

        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where('tblleads.id', $id);
            $lead = $this->db->get('tblleads')->row();
            if ($lead) {
                if ($lead->from_form_id != 0) {
                    $lead->form_data = $this->get_form(array(
                        'id' => $lead->from_form_id
                    ));
                }
                $lead->attachments = $this->get_lead_attachments($id);
                $lead->assigned = $this->get_lead_assignee($id);
            }

            return $lead;
        }

        return $this->db->get('tblleads')->result_array();
    }

    public function do_kanban_query($status, $search = '', $page = 1, $sort = array(), $count = false)
    {
        $limit = get_option('leads_kanban_limit');
        $default_leads_kanban_sort = get_option('default_leads_kanban_sort');
        $default_leads_kanban_sort_type = get_option('default_leads_kanban_sort_type');

        $this->db->select('tblleads.name as lead_name,tblleads.profile_image as lead_image,tblleadssources.name as source_name,tblleads.id as id,tblleads.assigned,tblleads.email,tblleads.phonenumber,tblleads.company,tblleads.eventstartdatetime, tblleads.eventenddatetime ,tblleads.status,tblleads.lastcontact, tbleventtype.eventtypename  as eventtypename, tblleads.eventinquireon');
        $this->db->from('tblleads');
        $this->db->join('tblleadssources', 'tblleadssources.id=tblleads.source', 'left');
        $this->db->join('tblstaff', 'tblstaff.staffid=tblleads.assigned', 'left');
        $this->db->join('tbleventtype', 'tbleventtype.eventtypeid=tblleads.eventtypeid', 'left');
        $this->db->where('status', $status);
        $this->db->where('tblleads.deleted = ', 0);
        $this->db->where('tblleads.converted = ', 0);
        $this->db->where('tblleads.brandid = ', get_user_session());

        if (!$this->is_admin) {
            $this->db->where('(assigned = ' . get_staff_user_id() . ' OR addedfrom=' . get_staff_user_id() . ' OR is_public=1)');
        }
        if ($search != '') {
            if (!_startsWith($search, '#')) {
                $this->db->where('(tblleads.name LIKE "%' . $search . '%" OR tblleadssources.name LIKE "%' . $search . '%" OR tblleads.email LIKE "%' . $search . '%" OR tblleads.phonenumber LIKE "%' . $search . '%" OR tblleads.company LIKE "%' . $search . '%" OR CONCAT(tblstaff.firstname, \' \', tblstaff.lastname) LIKE "%' . $search . '%")');
            } else {
                $this->db->where('tblleads.id IN
                (SELECT rel_id FROM tbltags_in WHERE tag_id IN
                (SELECT id FROM tbltags WHERE name="' . strafter($search, '#') . '")
                AND tbltags_in.rel_type=\'lead\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
                ');
            }
        }

        if (isset($sort['sort_by']) && $sort['sort_by'] && isset($sort['sort']) && $sort['sort']) {
            $this->db->order_by($sort['sort_by'], $sort['sort']);
        } else {
            $this->db->order_by($default_leads_kanban_sort, $default_leads_kanban_sort_type);
        }

        if ($count == false) {
            if ($page > 1) {
                $page--;
                $position = ($page * $limit);
                $this->db->limit($limit, $position);
            } else {
                $this->db->limit($limit);
            }
        }

        if ($count == false) {
            return $this->db->get()->result_array();
        } else {
            return $this->db->count_all_results();
        }
    }

    /**
     * Add new lead to database
     * @param mixed $data lead data
     * @return mixed false || leadid
     */
    public function add($data)
    {

        unset($data['pg']);
        unset($data['imagebase64']);
        /**
         * Added By : Vaidehi
         * Dt : 10/17/2017
         * to remove extra fields from input array
         */
        $contacts = array();
        if (isset($data['contact'])) {
            $contacts = $data['contact'];
        }
        unset($data['contact']);
        if (isset($data['assigned'])) {
            $assigned = $data['assigned'];
        }
        unset($data['assigned']);

        /*if ($data['leadcontact'][0] == 'new') {*/
        $this->load->model('addressbooks_model');
        if (!empty($contacts)) {
            $files = array();
            $newcontactids = array();
            $existingclients = array();
            if (isset($_FILES['contact'])) {
                foreach ($_FILES['contact'] as $i => $valarray) {
                    foreach ($valarray as $j => $val) {
                        $files[$j]['profile_image'][$i] = $val['profile_image'];
                    }
                }
            }

            foreach ($contacts as $key => $contactdata) {
                if ($contactdata['contacttype'] == "new") {
                    $imagebase64 = $contactdata['imagebase64'];
                    unset($contactdata['imagebase64']);
                    unset($contactdata['contacttype']);
                    unset($contactdata['id']);
                    $newcontactid = $this->addressbooks_model->add($contactdata);
                    if (isset($files)) {
                        $file = $files[$key];
                        $file['imagebase64'] = $imagebase64;
                        handle_multiple_addressbook_profile_image_upload($newcontactid, $file);
                    }
                    $newcontactids[] = $newcontactid;
                } elseif ($contactdata['contacttype'] == "existing") {
                    $existingclients[] = $contactdata['id'];
                }
            }
        }
        /*}*/
//        if ($data['leadcontact'][0] == 'existing') {
//            $existingclientid = $data['clients'];
//        }
        unset($data['clients']);
        if (isset($data['custom_contact_date']) || isset($data['custom_contact_date'])) {
            if (isset($data['contacted_today'])) {
                $data['lastcontact'] = date('Y-m-d H:i:s');
                unset($data['contacted_today']);
            } else {
                $data['lastcontact'] = to_sql_date($data['custom_contact_date'], true);
            }
        }

        if (isset($data['is_public']) && ($data['is_public'] == 1 || $data['is_public'] === 'on')) {
            $data['is_public'] = 1;
        } else {
            $data['is_public'] = 0;
        }

        if (!isset($data['country']) || isset($data['country']) && $data['country'] == '') {
            $data['country'] = 0;
        }

        if (isset($data['custom_contact_date'])) {
            unset($data['custom_contact_date']);
        }

        $brandid = get_user_session();

        $data['brandid'] = $brandid;
        //$data['description'] = nl2br($data['description']);
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $data = do_action('before_lead_added', $data);

        $tags = '';
        if (isset($data['tags'])) {
            $tags = $data['tags'];
            unset($data['tags']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if ($data['eventstartdatetime'] != '') {
            //$eventstartdatetime = date('Y-m-d H:i',strtotime($data['eventstartdatetime']));
            //$data['eventstartdatetime']  = $eventstartdatetime;
            $eventstartdatetime = str_replace('/', '-', $data['eventstartdatetime']);
            if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $eventstartdatetime)) {
                $data['eventstartdatetime'] = ((isset($data['eventstartdatetime']) && !empty($data['eventstartdatetime'])) ? date("Y-m-d H:i:s", strtotime($data['eventstartdatetime'])) : "");
            } else {
                if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                    $data['eventstartdatetime'] = ((isset($data['eventstartdatetime']) && !empty($data['eventstartdatetime'])) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $data['eventstartdatetime']))) : "");
                } else {
                    $data['eventstartdatetime'] = ((isset($data['eventstartdatetime']) && !empty($data['eventstartdatetime'])) ? date("Y-m-d H:i:s", strtotime($data['eventstartdatetime'])) : "");
                }
            }
        }

        if ($data['eventenddatetime'] != '') {
            // $eventenddatetime = date('Y-m-d H:i',strtotime($data['eventenddatetime']));
            // $data['eventenddatetime']  = $eventenddatetime;
            $eventenddatetime = str_replace('/', '-', $data['eventenddatetime']);
            if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $eventenddatetime)) {
                $data['eventstartdatetime'] = ((isset($data['eventenddatetime']) && !empty($data['eventenddatetime'])) ? date("Y-m-d H:i:s", strtotime($data['eventenddatetime'])) : "");
            } else {
                if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                    $data['eventenddatetime'] = ((isset($data['eventenddatetime']) && !empty($data['eventenddatetime'])) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $data['eventenddatetime']))) : "");
                } else {
                    $data['eventenddatetime'] = ((isset($data['eventenddatetime']) && !empty($data['eventenddatetime'])) ? date("Y-m-d H:i:s", strtotime($data['eventenddatetime'])) : "");
                }
            }
        }

        if ($data['eventinquireon'] != '') {
            // $eventinquireon = date('Y-m-d',strtotime($data['eventinquireon']));
            // $data['eventinquireon']  = $eventinquireon;
            $eventinquireon = str_replace('/', '-', $data['eventinquireon']);
            if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $eventinquireon)) {
                $data['eventinquireon'] = ((isset($data['eventinquireon']) && !empty($data['eventinquireon'])) ? date("Y-m-d", strtotime($data['eventinquireon'])) : "");
            } else {
                if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                    $data['eventinquireon'] = ((isset($data['eventinquireon']) && !empty($data['eventinquireon'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $data['eventinquireon']))) : "");
                } else {
                    $data['eventinquireon'] = ((isset($data['eventinquireon']) && !empty($data['eventinquireon'])) ? date("Y-m-d", strtotime($data['eventinquireon'])) : "");
                }
            }
        }

        if ($data['eventdecideby'] != '') {
            // $eventdecideby = date('Y-m-d',strtotime($data['eventdecideby']));
            // $data['eventdecideby']  = $eventdecideby;
            $eventdecideby = str_replace('/', '-', $data['eventdecideby']);
            if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $eventdecideby)) {
                $data['eventdecideby'] = ((isset($data['eventdecideby']) && !empty($data['eventdecideby'])) ? date("Y-m-d", strtotime($data['eventdecideby'])) : "");
            } else {
                if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                    $data['eventdecideby'] = ((isset($data['eventdecideby']) && !empty($data['eventdecideby'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $data['eventdecideby']))) : "");
                } else {
                    $data['eventdecideby'] = ((isset($data['eventdecideby']) && !empty($data['eventdecideby'])) ? date("Y-m-d", strtotime($data['eventdecideby'])) : "");
                }
            }
        }
        //$_FILES['profile_image'] = $_FILES['lead_profile_image'];
        $data['profile_image'] = $_FILES['lead_profile_image']['name'];
        unset($data['leadcontact']);
        unset($data['lead_profile_image']);
        $this->db->insert('tblleads', $data);

        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            logActivity('New Lead Added [ID:' . $insert_id . ' , Name: ' . $data['name'] . ']');
            $this->log_lead_activity($insert_id, 'not_lead_activity_created');

            handle_tags_save($tags, $insert_id, 'lead');

            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }
            if (isset($assigned) && !empty($assigned)) {
                foreach ($assigned as $assignee) {
                    $assignData = array();
                    $assignData['leadid'] = $insert_id;
                    $assignData['assigned'] = $assignee;
                    $this->db->insert('tblstaffleadassignee', $assignData);
                    $this->lead_assigned_member_notification($insert_id, $assignee);
                    $this->lead_new_created_notification($insert_id, $assignee);
                }
            }
            //$this->lead_assigned_member_notification($insert_id, $data['assigned']);
            do_action('lead_created', $insert_id);

            if (isset($existingclients) && count($existingclients) > 0) {
                foreach ($existingclients as $existingclientid) {
                    $leadcontact = array();
                    $leadcontact['leadid'] = $insert_id;
                    $brandid = get_user_session();
                    $leadcontact['brandid'] = $brandid;
                    $leadcontact['contactid'] = $existingclientid;
                    $this->db->where('leadid', $insert_id);
                    $this->db->where('contactid', $leadcontact['contactid']);
                    $this->db->where('brandid', $brandid);
                    $leadcontacts = $this->db->get('tblleadcontact')->row();

                    if (count($leadcontacts) <= 0) {
                        $this->db->insert('tblleadcontact', $leadcontact);
                    }
                }

            }
            if (count($newcontactids) > 0) {
                foreach ($newcontactids as $newcontactid) {
                    $leadcontact = array();
                    $leadcontact['leadid'] = $insert_id;
                    $brandid = get_user_session();
                    $leadcontact['brandid'] = $brandid;
                    $leadcontact['contactid'] = $newcontactid;
                    $this->db->where('leadid', $insert_id);
                    $this->db->where('contactid', $leadcontact['contactid']);
                    $this->db->where('brandid', $brandid);
                    $leadcontacts = $this->db->get('tblleadcontact')->row();

                    if (count($leadcontacts) <= 0) {
                        $this->db->insert('tblleadcontact', $leadcontact);
                    }
                }

            }

            /**
             * Added By: Vaidehi
             * Dt: 03/20/2018
             * for maintaining all statuses
             */
            $status_data = [];
            $status_data['old_statusid'] = 0;
            $status_data['new_statusid'] = $data['status'];
            $status_data['leadid'] = $insert_id;
            $this->db->insert('tblleadstatushistory', $status_data);
            return $insert_id;
        }

        return false;
    }

    public function lead_assigned_member_notification($lead_id, $assigned, $integration = false)
    {
        if ((!empty($assigned) && $assigned != 0)) {
            if ($integration == false) {
                if ($assigned == get_staff_user_id()) {
                    return false;
                }
            }

            $name = $this->db->select('name')->from('tblleads')->where('id', $lead_id)->get()->row()->name;

            $notification_data = array(
                'description' => ($integration == false) ? 'not_assigned_lead_to_you' : 'not_lead_assigned_from_form',
                'touserid' => $assigned,
                'eid' => $lead_id,
                'not_type' => 'lead',
                'brandid' => get_user_session(),
                'link' => 'leads/dashboard/' . $lead_id,
                'additional_data' => ($integration == false ? serialize(array(
                    $name
                )) : serialize(array()))
            );

            if ($integration != false) {
                $notification_data['fromcompany'] = 1;
            }

            if (add_notification($notification_data)) {
                pusher_trigger_notification(array($assigned));
            }

            $this->db->where('staffid', $assigned);
            $email = $this->db->get('tblstaff')->row()->email;

            $this->load->model('emails_model');
            $merge_fields = array();
            $merge_fields = array_merge($merge_fields, get_lead_merge_fields($lead_id));
            $this->emails_model->send_email_template('new-lead-assigned', $email, $merge_fields);

            $this->db->where('id', $lead_id);
            $this->db->update('tblleads', array(
                'dateassigned' => date('Y-m-d')
            ));

            $not_additional_data = array(
                get_staff_full_name(),
                // '<a href="' . admin_url('profile/' . $assigned) . '" target="_blank">' . get_staff_full_name($assigned) . '</a>'
                '<a href="javascript:void(0)" >' . get_staff_full_name($assigned) . '</a>'
            );

            if ($integration == true) {
                unset($not_additional_data[0]);
                array_values(($not_additional_data));
            }

            $not_additional_data = serialize($not_additional_data);

            $not_desc = ($integration == false ? 'not_lead_activity_assigned_to' : 'not_lead_activity_assigned_from_form');
            $this->log_lead_activity($lead_id, $not_desc, $integration, $not_additional_data);
        }
    }

    /**
     * Added By : Masud
     * Dt : 01/04/2018
     * to save extra form fields in db
     */

    public function lead_new_created_notification($lead_id, $assigned, $integration = false)
    {
        //die('<--here lead_new_created_notification');
        $name = $this->db->select('name')->from('tblleads')->where('id', $lead_id)->get()->row()->name;
        if ($assigned == "") {
            $assigned = 0;
        }

        $notification_data = array(
            'description' => ($integration == false) ? 'not_new_lead_created' : 'not_lead_assigned_from_form',
            'touserid' => $assigned,
            'eid' => $lead_id,
            'brandid' => get_user_session(),
            'not_type' => 'lead',
            'link' => 'leads/dashboard/' . $lead_id,
            'additional_data' => ($integration == false ? serialize(array(
                $name
            )) : serialize(array()))
        );

        if ($integration != false) {
            $notification_data['fromcompany'] = 1;
        }

        if (add_notification($notification_data)) {
            pusher_trigger_notification(array($assigned));
        }

        /*$this->db->where('staffid', $assigned);
        $email = $this->db->get('tblstaff')->row()->email;

        $this->load->model('emails_model');
        $merge_fields = array();
        $merge_fields = array_merge($merge_fields, get_lead_merge_fields($lead_id));
        $this->emails_model->send_email_template('new-lead-assigned', $email, $merge_fields);

        $this->db->where('id', $lead_id);
        $this->db->update('tblleads', array(
            'dateassigned' => date('Y-m-d')
        ));

        $not_additional_data = array(
            get_staff_full_name(),
            // '<a href="' . admin_url('profile/' . $assigned) . '" target="_blank">' . get_staff_full_name($assigned) . '</a>'
              '<a href="javascript:void(0)" >' . get_staff_full_name($assigned) . '</a>'
        );

        if ($integration == true) {
            unset($not_additional_data[0]);
            array_values(($not_additional_data));
        }

        $not_additional_data = serialize($not_additional_data);

        $not_desc = ($integration == false ? 'not_lead_activity_assigned_to' : 'not_lead_activity_assigned_from_form');
        $this->log_lead_activity($lead_id, $not_desc, $integration, $not_additional_data);*/
    }

    /**
     * Added By : Masud
     * Dt : 01/04/2018
     * to save extra form fields in db
     */
    public function lead_status_changed_notification($lead_id, $status, $assigned = "", $integration = false)
    {
        //die('<--here lead_new_created_notification');
        $lead = $this->db->select('name,assigned')->from('tblleads')->where('id', $lead_id)->get()->row();
        $name = $lead->name;
        $assigned = $lead->assigned;

        if ($assigned == "") {
            $assigned = 0;
        }
        $notification_data = array(
            'description' => ($integration == false) ? 'not_lead_status_changed' : 'not_lead_assigned_from_form',
            'touserid' => $assigned,
            'eid' => $lead_id,
            'brandid' => get_user_session(),
            'not_type' => 'lead',
            'link' => 'leads/dashboard/' . $lead_id,
            'additional_data' => ($integration == false ? serialize(array(
                $name, $status
            )) : serialize(array()))
        );

        if ($integration != false) {
            $notification_data['fromcompany'] = 1;
        }

        if (add_notification($notification_data)) {
            pusher_trigger_notification(array($assigned));
        }

        /*$this->db->where('staffid', $assigned);
        $email = $this->db->get('tblstaff')->row()->email;

        $this->load->model('emails_model');
        $merge_fields = array();
        $merge_fields = array_merge($merge_fields, get_lead_merge_fields($lead_id));
        $this->emails_model->send_email_template('new-lead-assigned', $email, $merge_fields);

        $this->db->where('id', $lead_id);
        $this->db->update('tblleads', array(
            'dateassigned' => date('Y-m-d')
        ));

        $not_additional_data = array(
            get_staff_full_name(),
            // '<a href="' . admin_url('profile/' . $assigned) . '" target="_blank">' . get_staff_full_name($assigned) . '</a>'
              '<a href="javascript:void(0)" >' . get_staff_full_name($assigned) . '</a>'
        );

        if ($integration == true) {
            unset($not_additional_data[0]);
            array_values(($not_additional_data));
        }

        $not_additional_data = serialize($not_additional_data);

        $not_desc = ($integration == false ? 'not_lead_activity_assigned_to' : 'not_lead_activity_assigned_from_form');
        $this->log_lead_activity($lead_id, $not_desc, $integration, $not_additional_data);*/
    }

    /**
     * Update lead
     * @param  array $data lead data
     * @param  mixed $id leadid
     * @return boolean
     */
    public function update($data, $id)
    {
        unset($data['pg']);
        unset($data['imagebase64']);
        if (isset($data['assigned'])) {
            $assigned = $data['assigned'];
        }
        unset($data['assigned']);
        $current_lead_data = $this->get($id);
        $current_status = $this->get_status($current_lead_data->status);
        if ($current_status) {
            $current_status_id = $current_status->id;
            $current_status = $current_status->name;
        } else {
            if ($current_lead_data->junk == 1) {
                $current_status = _l('lead_junk');
            } elseif ($current_lead_data->lost == 1) {
                $current_status = _l('lead_lost');
            } else {
                $current_status = '';
            }
            $current_status_id = 0;
        }

        $affectedRows = 0;
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }
        if (isset($data['lastcontact']) && $data['lastcontact'] == '' || isset($data['lastcontact']) && $data['lastcontact'] == null) {
            $data['lastcontact'] = null;
        } elseif (isset($data['lastcontact'])) {
            $data['lastcontact'] = to_sql_date($data['lastcontact'], true);
        }

        if (isset($data['tags'])) {
            if (handle_tags_save($data['tags'], $id, 'lead')) {
                $affectedRows++;
            }
            unset($data['tags']);
        }

        /**
         * Added By : Vaidehi
         * Dt : 10/30/2017
         * to save extra form fields in db
         */
        $brandid = get_user_session();

        $data['brandid'] = $brandid;
        //$data['description'] = nl2br($data['description']);
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $data['updatedby'] = get_staff_user_id();

        if ($data['eventstartdatetime'] != '') {
            $eventstartdatetime = str_replace('/', '-', $data['eventstartdatetime']);
            if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $eventstartdatetime)) {
                $data['eventstartdatetime'] = ((isset($data['eventstartdatetime']) && !empty($data['eventstartdatetime'])) ? date("Y-m-d H:i", strtotime($data['eventstartdatetime'])) : "");
            } else {
                if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                    $data['eventstartdatetime'] = ((isset($data['eventstartdatetime']) && !empty($data['eventstartdatetime'])) ? date("Y-m-d H:i", strtotime(str_replace('/', '-', $data['eventstartdatetime']))) : "");
                } else {
                    $data['eventstartdatetime'] = ((isset($data['eventstartdatetime']) && !empty($data['eventstartdatetime'])) ? date("Y-m-d H:i", strtotime($data['eventstartdatetime'])) : "");
                }
            }
        }

        if ($data['eventenddatetime'] != '') {
            $eventenddatetime = str_replace('/', '-', $data['eventenddatetime']);
            if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $eventenddatetime)) {
                $data['eventenddatetime'] = ((isset($data['eventenddatetime']) && !empty($data['eventenddatetime'])) ? date("Y-m-d H:i", strtotime($data['eventstartdatetime'])) : "");
            } else {
                if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                    $data['eventenddatetime'] = ((isset($data['eventenddatetime']) && !empty($data['eventenddatetime'])) ? date("Y-m-d H:i", strtotime(str_replace('/', '-', $data['eventenddatetime']))) : "");
                } else {
                    $data['eventenddatetime'] = ((isset($data['eventenddatetime']) && !empty($data['eventenddatetime'])) ? date("Y-m-d H:i", strtotime($data['eventenddatetime'])) : "");
                }
            }
        }

        if ($data['eventinquireon'] != '') {
            $eventinquireon = str_replace('/', '-', $data['eventinquireon']);
            if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $eventinquireon)) {
                $data['eventinquireon'] = ((isset($data['eventinquireon']) && !empty($data['eventinquireon'])) ? date("Y-m-d", strtotime($data['eventinquireon'])) : "");
            } else {
                if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                    $data['eventinquireon'] = ((isset($data['eventinquireon']) && !empty($data['eventinquireon'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $data['eventinquireon']))) : "");
                } else {
                    $data['eventinquireon'] = ((isset($data['eventinquireon']) && !empty($data['eventinquireon'])) ? date("Y-m-d", strtotime($data['eventinquireon'])) : "");
                }
            }
        }

        if ($data['eventdecideby'] != '') {
            $eventdecideby = str_replace('/', '-', $data['eventdecideby']);
            if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $eventdecideby)) {
                $data['eventdecideby'] = ((isset($data['eventdecideby']) && !empty($data['eventdecideby'])) ? date("Y-m-d", strtotime($data['eventdecideby'])) : "");
            } else {
                if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                    $data['eventdecideby'] = ((isset($data['eventdecideby']) && !empty($data['eventdecideby'])) ? date("Y-m-d", strtotime(str_replace('/', '-', $data['eventdecideby']))) : "");
                } else {
                    $data['eventdecideby'] = ((isset($data['eventdecideby']) && !empty($data['eventdecideby'])) ? date("Y-m-d", strtotime($data['eventdecideby'])) : "");
                }
            }
        }

        $data['profile_image'] = (isset($data['lead_profile_image']['name']) ? $data['lead_profile_image']['name'] : $current_lead_data->profile_image);

        unset($data['contact']);
        unset($data['lead_profile_image']);
        $this->db->where('id', $id);
        $this->db->update('tblleads', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if (isset($data['status']) && $current_status_id != $data['status']) {
                $this->db->where('id', $id);
                $this->db->update('tblleads', array(
                    'last_status_change' => date('Y-m-d H:i:s')
                ));
                $new_status_name = $this->get_status($data['status'])->name;
                $this->lead_status_changed_notification($id, $new_status_name);
                $this->log_lead_activity($id, 'not_lead_activity_status_updated', false, serialize(array(
                    get_staff_full_name(),
                    $current_status,
                    $new_status_name
                )));

                do_action('lead_status_changed', array('lead_id' => $id, 'old_status' => $current_status_id, 'new_status' => $data['status']));

                /**
                 * Added By: Vaidehi
                 * Dt: 03/20/2018
                 * for maintaining all statuses
                 */
                $status_data = [];
                $status_data['old_statusid'] = $current_status_id;
                $status_data['new_statusid'] = $data['status'];
                $status_data['leadid'] = $id;
                $this->db->insert('tblleadstatushistory', $status_data);
            }

            if (($current_lead_data->junk == 1 || $current_lead_data->lost == 1) && $data['status'] != 0) {
                $this->db->where('id', $id);
                $this->db->update('tblleads', array(
                    'junk' => 0,
                    'lost' => 0
                ));
            }

            /*if (isset($data['assigned'])) {
                if ($current_lead_data->assigned != $data['assigned'] && (!empty($data['assigned']) && $data['assigned'] != 0)) {
                    $this->lead_assigned_member_notification($id, $data['assigned']);
                }
            }*/
            if (isset($assigned) && !empty($assigned)) {
                $assignedusers = $this->get_lead_assignee($id);
                foreach ($assigned as $assignee) {
                    if (!in_array($assignee, $assignedusers)) {
                        $assignData = array();
                        $assignData['leadid'] = $id;
                        $assignData['assigned'] = $assignee;
                        $this->db->insert('tblstaffleadassignee', $assignData);
                        $this->lead_assigned_member_notification($id, $assignee);
                    }
                }
                foreach ($assignedusers as $assignee) {
                    if (!in_array($assignee, $assigned)) {
                        $this->db->where('leadid', $id);
                        $this->db->where('assigned', $assignee);
                        $this->db->delete('tblstaffleadassignee');
                    }
                }
            }
            logActivity('Lead Updated [Name: ' . $data['name'] . ']');

            return true;
        }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    function get_lead_assignee($id)
    {
        $this->db->select('assigned');
        $this->db->where('leadid', $id);
        $assignee = $this->db->get('tblstaffleadassignee')->result_array();
        $assignee = array_map('current', $assignee);
        return $assignee;
    }

    /**
     * Delete lead from database and all connections
     * @param  mixed $id leadid
     * @return boolean
     */
    public function delete($id, $type = "")
    {
        $affectedRows = 0;

        do_action('before_lead_deleted', $id);

        $leadname = $this->get($id)->name;

        $data['deleted'] = 1;
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $data['updatedby'] = get_staff_user_id();

        /*For soft delete lead while brand delete*/
        if ($type == "subscription") {
            $this->db->where('brandid', $id);
        } else {
            $this->db->where('id', $id);
        }

        $this->db->update('tblleads', $data);
        //echo $this->db->last_query();die;
        if ($this->db->affected_rows() > 0) {
            $this->log_lead_activity($id, 'not_lead_deleted', false, serialize(array(
                $leadname,
                get_staff_full_name()
            )));

            $attachments = $this->get_lead_attachments($id);
            foreach ($attachments as $attachment) {
                $this->delete_lead_attachment($attachment['id']);
            }

            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'leads');
            $this->db->delete('tblcustomfieldsvalues');

            $this->db->where('leadid', $id);
            $this->db->delete('tblleadactivitylog');

            $this->db->where('leadid', $id);
            $this->db->delete('tblleadsemailintegrationemails');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'lead');
            $this->db->delete('tblnotes');

            $this->db->where('rel_type', 'lead');
            $this->db->where('rel_id', $id);
            $this->db->delete('tblreminders');

            $this->db->where('rel_type', 'lead');
            $this->db->where('rel_id', $id);
            $this->db->delete('tbltags_in');

            // $this->load->model('proposals_model');
            // $this->db->where('rel_id', $id);
            // $this->db->where('rel_type', 'lead');
            // $proposals = $this->db->get('tblproposals')->result_array();

            // foreach ($proposals as $proposal) {
            //     $this->proposals_model->delete($proposal['id']);
            // }

            // Get related tasks
            $this->db->where('rel_type', 'lead');
            $this->db->where('rel_id', $id);
            $tasks = $this->db->get('tblstafftasks')->result_array();
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id']);
            }

            $affectedRows++;
        }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    /**
     * Mark lead as lost
     * @param  mixed $id lead id
     * @return boolean
     */
    public function mark_as_lost($id)
    {
        $this->db->select('status');
        $this->db->from('tblleads');
        $this->db->where('id', $id);
        $last_lead_status = $this->db->get()->row()->status;

        $this->db->where('id', $id);
        $this->db->update('tblleads', array(
            'lost' => 1,
            'status' => 0,
            'last_status_change' => date('Y-m-d H:i:s'),
            'last_lead_status' => $last_lead_status
        ));
        if ($this->db->affected_rows() > 0) {
            $this->log_lead_activity($id, 'not_lead_activity_marked_lost');
            logActivity('Lead Marked as Lost [LeadID: ' . $id . ']');
            do_action('lead_marked_as_lost', $id);

            return true;
        }

        return false;
    }

    /**
     * Unmark lead as lost
     * @param  mixed $id leadid
     * @return boolean
     */
    public function unmark_as_lost($id)
    {
        $this->db->select('last_lead_status');
        $this->db->from('tblleads');
        $this->db->where('id', $id);
        $last_lead_status = $this->db->get()->row()->last_lead_status;

        $this->db->where('id', $id);
        $this->db->update('tblleads', array(
            'lost' => 0,
            'status' => $last_lead_status
        ));
        if ($this->db->affected_rows() > 0) {
            $this->log_lead_activity($id, 'not_lead_activity_unmarked_lost');
            logActivity('Lead Unmarked as Lost [LeadID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Mark lead as junk
     * @param  mixed $id lead id
     * @return boolean
     */
    public function mark_as_junk($id)
    {
        $this->db->select('status');
        $this->db->from('tblleads');
        $this->db->where('id', $id);
        $last_lead_status = $this->db->get()->row()->status;

        $this->db->where('id', $id);
        $this->db->update('tblleads', array(
            'junk' => 1,
            'status' => 0,
            'last_status_change' => date('Y-m-d H:i:s'),
            'last_lead_status' => $last_lead_status
        ));
        if ($this->db->affected_rows() > 0) {
            $this->log_lead_activity($id, 'not_lead_activity_marked_junk');
            logActivity('Lead Marked as Junk [LeadID: ' . $id . ']');
            do_action('lead_marked_as_junk', $id);

            return true;
        }

        return false;
    }

    /**
     * Unmark lead as junk
     * @param  mixed $id leadid
     * @return boolean
     */
    public function unmark_as_junk($id)
    {
        $this->db->select('last_lead_status');
        $this->db->from('tblleads');
        $this->db->where('id', $id);
        $last_lead_status = $this->db->get()->row()->last_lead_status;

        $this->db->where('id', $id);
        $this->db->update('tblleads', array(
            'junk' => 0,
            'status' => $last_lead_status
        ));
        if ($this->db->affected_rows() > 0) {
            $this->log_lead_activity($id, 'not_lead_activity_unmarked_junk');
            logActivity('Lead Unmarked as Junk [LeadID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Get lead attachments
     * @since Version 1.0.4
     * @param  mixed $id lead id
     * @return array
     */
    public function get_lead_attachments($id = '', $attachment_id = '')
    {
        if (is_numeric($attachment_id)) {
            $this->db->where('id', $attachment_id);

            return $this->db->get('tblfiles')->row();
        }
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'lead');
        $this->db->order_by('dateadded', 'DESC');

        return $this->db->get('tblfiles')->result_array();
    }

    public function add_attachment_to_database($lead_id, $attachment, $external = false, $form_activity = false)
    {
        $this->misc_model->add_attachment_to_database($lead_id, 'lead', $attachment, $external);

        if ($form_activity == false) {
            $this->leads_model->log_lead_activity($lead_id, 'not_lead_activity_added_attachment');
        } else {
            $this->leads_model->log_lead_activity($lead_id, 'not_lead_activity_log_attachment', true, serialize(array(
                $form_activity
            )));
        }

        // No notification when attachment is imported from web to lead form
        if ($form_activity == false) {
            $lead = $this->get($lead_id);
            $not_user_ids = array();
            if ($lead->addedfrom != get_staff_user_id()) {
                array_push($not_user_ids, $lead->addedfrom);
            }
            /*if ($lead->assigned != get_staff_user_id() && $lead->assigned != 0) {
                array_push($not_user_ids, $lead->assigned);
            }*/
            if (!empty($lead->assigned) && count($lead->assigned) > 0) {
                array_merge($not_user_ids, $lead->assigned);
            }
            $notifiedUsers = array();
            foreach ($not_user_ids as $uid) {
                $notified = add_notification(array(
                    'description' => 'not_lead_added_attachment',
                    'touserid' => $uid,
                    'brandid' => get_user_session(),
                    'eid' => $lead_id,
                    'not_type' => 'lead',
                    'link' => '#leadid=' . $lead_id,
                    'additional_data' => serialize(array(
                        $lead->name
                    ))
                ));
                if ($notified) {
                    array_push($notifiedUsers, $uid);
                }
            }
            pusher_trigger_notification($notifiedUsers);
        }
    }

    /**
     * Delete lead attachment
     * @param  mixed $id attachment id
     * @return boolean
     */
    public function delete_lead_attachment($id)
    {
        $attachment = $this->get_lead_attachments('', $id);
        $deleted = false;

        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(get_upload_path_by_type('lead') . $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete('tblfiles');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                logActivity('Lead Attachment Deleted [LeadID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_by_type('lead') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('lead') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(get_upload_path_by_type('lead') . $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    // Sources

    /**
     * Get leads sources
     * @param  mixed $id Optional - Source ID
     * @return mixed object if id passed else array
     */
    public function get_source($id = false)
    {
        $brandid = get_user_session();
        $session_data = get_session_data();
        //$is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];

        $where = "";
        $where .= 'deleted=0';
        if ($is_admin == false) {
            $where .= ' and brandid =' . $brandid;
        }

        if (is_numeric($id)) {
            $where .= ' and id=' . $id;
            $this->db->where($where);

            return $this->db->get('tblleadssources')->row();
        }

        $this->db->where($where);
        return $this->db->get('tblleadssources')->result_array();
    }

    /**
     * Added By : Vaidehi
     * Dt : 10/16/2017
     * to get all event type
     */
    /**
     * Get event type
     * @param  mixed $id Optional - Source ID
     * @return mixed object if id passed else array
     */
    public function get_event_type($id = false)
    {
        if (is_numeric($id)) {
            $where .= ' eventtypeid = ' . $id;
            $this->db->where($where);

            return $this->db->get('tbleventtype')->row();
        }

        return $this->db->get('tbleventtype')->result_array();
    }

    /**
     * Add new lead source
     * @param mixed $data source data
     */
    public function add_source($data)
    {
        $this->db->insert('tblleadssources', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Leads Source Added [SourceID: ' . $insert_id . ', Name: ' . $data['name'] . ']');
            return true;
        }

        return false;
    }

    /**
     * Update lead source
     * @param  mixed $data source data
     * @param  mixed $id source id
     * @return boolean
     */
    public function update_source($data, $id)
    {
        $data['updated_by'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update('tblleadssources', $data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Leads Source Updated [SourceID: ' . $id . ', Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete lead source from database
     * @param  mixed $id source id
     * @return mixed
     */
    public function delete_source($id)
    {
        $current = $this->get_source($id);
        // Check if is already using in table
        if (is_reference_in_table('source', 'tblleads', $id) || is_reference_in_table('lead_source', 'tblleadsintegration', $id)) {
            return array(
                'referenced' => true
            );
        }

        $data['deleted'] = 1;
        $data['updated_by'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update('tblleadssources', $data);
        if ($this->db->affected_rows() > 0) {
            if (get_option('leads_default_source') == $id) {
                update_option('leads_default_source', '');
            }
            logActivity('Leads Source Deleted [LeadID: ' . $id . ']');

            return true;
        }

        return false;
    }

    // Statuses

    /**
     * Get lead statuses
     * @param  mixed $id status id
     * @return mixed      object if id passed else array
     */
    public function get_status($id = '', $where = array())
    {
        // Added by Avni on 10/05
        $brandid = get_user_session();
        $session_data = get_session_data();
        //$is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];


        $where = "";
        $where .= 'deleted=0';
        if ($is_admin == false) {
            $where .= ' and brandid =' . $brandid;
        }

        if (is_numeric($id)) {
            $where .= ' and id=' . $id;
            $this->db->where($where);
            $this->db->order_by("statusorder", "asc");

            return $this->db->get('tblleadsstatus')->row();
        }

        $this->db->where($where);
        $this->db->order_by("statusorder", "asc");
        return $this->db->get('tblleadsstatus')->result_array();
    }

    /**
     * Add new lead status
     * @param array $data lead status data
     */
    public function add_status($data)
    {
        $this->db->insert('tblleadsstatus', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Leads Status Added [StatusID: ' . $insert_id . ', Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    public function update_status($data, $id)
    {
        $data['updated_by'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update('tblleadsstatus', $data);

        if ($this->db->affected_rows() > 0) {
            logActivity('Leads Status Updated [StatusID: ' . $id . ', Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete lead status from database
     * @param  mixed $id status id
     * @return boolean
     */
    public function delete_status($id)
    {
        $current = $this->get_status($id);
        // Check if is already using in table
        if (is_reference_in_table('status', 'tblleads', $id) || is_reference_in_table('lead_status', 'tblleadsintegration', $id)) {
            return array(
                'referenced' => true
            );
        }

        $data['deleted'] = 1;
        $data['updated_by'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update('tblleadsstatus', $data);

        if ($this->db->affected_rows() > 0) {
            $session_data = get_session_data();
            $is_admin = $session_data['is_admin'];

            if ($is_admin == false) {
                if (get_brand_option('leads_default_status') == $id) {
                    update_brand_option('leads_default_status', '');
                }
            } else {
                if (get_option('leads_default_status') == $id) {
                    update_option('leads_default_status', '');
                }
            }

            logActivity('Leads Status Deleted [StatusID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Update canban lead status when drag and drop
     * @param  array $data lead data
     * @return boolean
     */
    public function update_lead_status($data)
    {
        $this->db->select('status');
        $this->db->where('id', $data['leadid']);
        $_old = $this->db->get('tblleads')->row();

        $old_status = '';

        if ($_old) {
            $old_status = $this->get_status($_old->status);
            if ($old_status) {
                $old_status = $old_status->name;
            }
        }

        $affectedRows = 0;
        $current_status = $this->get_status($data['status'])->name;

        $this->db->where('id', $data['leadid']);
        $this->db->update('tblleads', array(
            'status' => $data['status']
        ));

        $_log_message = '';

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if ($current_status != $old_status && $old_status != '') {
                $_log_message = 'not_lead_activity_status_updated';
                $additional_data = serialize(array(
                    get_staff_full_name(),
                    $old_status,
                    $current_status
                ));
                $this->lead_status_changed_notification($data['leadid'], $current_status);
                do_action('lead_status_changed', array('lead_id' => $data['leadid'], 'old_status' => $old_status, 'new_status' => $current_status));
            }
            $this->db->where('id', $data['leadid']);
            $this->db->update('tblleads', array(
                'last_status_change' => date('Y-m-d H:i:s')
            ));
        }
        if (isset($data['order'])) {
            foreach ($data['order'] as $order_data) {
                $this->db->where('id', $order_data[0]);
                $this->db->update('tblleads', array(
                    'leadorder' => $order_data[1]
                ));
            }
        }
        if ($affectedRows > 0) {
            if ($_log_message == '') {
                return true;
            }
            $this->log_lead_activity($data['leadid'], $_log_message, false, $additional_data);

            return true;
        }

        return false;
    }

    /* Ajax */

    /**
     * All lead activity by staff
     * @param  mixed $id lead id
     * @return array
     */
    public function get_lead_activity_log($id)
    {
        $sorting = do_action('lead_activity_log_default_sort', 'DESC');

        $this->db->where('leadid', $id);
        $this->db->order_by('date', $sorting);

        return $this->db->get('tblleadactivitylog')->result_array();
    }

    /**
     * Add lead activity from staff
     * @param  mixed $id lead id
     * @param  string $description activity description
     */
    public function log_lead_activity($id, $description, $integration = false, $additional_data = '')
    {
        $log = array(
            'date' => date('Y-m-d H:i:s'),
            'description' => $description,
            'leadid' => $id,
            'staffid' => get_staff_user_id(),
            'brandid' => get_user_session(),
            'additional_data' => $additional_data,
            'full_name' => get_staff_full_name(get_staff_user_id())
        );
        if ($integration == true) {
            $log['staffid'] = 0;
            $log['full_name'] = '[CRON]';
        }

        $this->db->insert('tblleadactivitylog', $log);

        return $this->db->insert_id();
    }

    /**
     * Get email integration config
     * @return object
     */
    public function get_email_integration()
    {
        $this->db->where('id', 1);

        return $this->db->get('tblleadsintegration')->row();
    }

    /**
     * Get lead imported email activity
     * @param  mixed $id leadid
     * @return array
     */
    public function get_mail_activity($id)
    {
        $this->db->where('leadid', $id);
        $this->db->order_by('dateadded', 'asc');

        return $this->db->get('tblleadsemailintegrationemails')->result_array();
    }

    /**
     * Update email integration config
     * @param  mixed $data All $_POST data
     * @return boolean
     */
    public function update_email_integration($data)
    {
        $this->db->where('id', 1);
        $original_settings = $this->db->get('tblleadsintegration')->row();

        $this->db->where('id', 1);
        if (isset($data['active'])) {
            $data['active'] = 1;
        } else {
            $data['active'] = 0;
        }

        if ($data['responsible'] == '') {
            $data['responsible'] = 0;
        }

        if (isset($data['delete_after_import'])) {
            $data['delete_after_import'] = 1;
        } else {
            $data['delete_after_import'] = 0;
        }

        if (isset($data['mark_public'])) {
            $data['mark_public'] = 1;
        } else {
            $data['mark_public'] = 0;
        }

        if (isset($data['notify_lead_imported'])) {
            $data['notify_lead_imported'] = 1;
        } else {
            $data['notify_lead_imported'] = 0;
        }
        if (isset($data['notify_lead_contact_more_times'])) {
            $data['notify_lead_contact_more_times'] = 1;
        } else {
            $data['notify_lead_contact_more_times'] = 0;
        }
        if ($data['notify_lead_contact_more_times'] != 0 || $data['notify_lead_imported'] != 0) {
            if ($data['notify_type'] == 'specific_staff') {
                if (isset($data['notify_ids_staff'])) {
                    $data['notify_ids'] = serialize($data['notify_ids_staff']);
                    unset($data['notify_ids_staff']);
                } else {
                    $data['notify_ids'] = serialize(array());
                    unset($data['notify_ids_staff']);
                }
                if (isset($data['notify_ids_roles'])) {
                    unset($data['notify_ids_roles']);
                }
            } else {
                if (isset($data['notify_ids_roles'])) {
                    $data['notify_ids'] = serialize($data['notify_ids_roles']);
                    unset($data['notify_ids_roles']);
                } else {
                    $data['notify_ids'] = serialize(array());
                    unset($data['notify_ids_roles']);
                }
                if (isset($data['notify_ids_staff'])) {
                    unset($data['notify_ids_staff']);
                }
            }
        } else {
            $data['notify_ids'] = serialize(array());
            $data['notify_type'] = null;
            if (isset($data['notify_ids_staff'])) {
                unset($data['notify_ids_staff']);
            }
            if (isset($data['notify_ids_roles'])) {
                unset($data['notify_ids_roles']);
            }
        }
        if (isset($data['only_loop_on_unseen_emails'])) {
            $data['only_loop_on_unseen_emails'] = 1;
        } else {
            $data['only_loop_on_unseen_emails'] = 0;
        }
        // Check if not empty $data['password']
        // Get original
        // Decrypt original
        // Compare with $data['password']
        // If equal unset
        // If not encrypt and save
        if (!empty($data['password'])) {
            $or_decrypted = $this->encryption->decrypt($original_settings->password);
            if ($or_decrypted == $data['password']) {
                unset($data['password']);
            } else {
                $data['password'] = $this->encryption->encrypt($data['password']);
            }
        }

        $this->db->update('tblleadsintegration', $data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function change_status_color($data)
    {
        $this->db->where('id', $data['status_id']);
        $this->db->update('tblleadsstatus', array(
            'color' => $data['color']
        ));
    }

    public function update_status_order()
    {
        $data = $this->input->post();
        foreach ($data['order'] as $status) {
            $this->db->where('id', $status[0]);
            $this->db->update('tblleadsstatus', array(
                'statusorder' => $status[1]
            ));
        }
    }

    public function get_form($where)
    {
        $this->db->where($where);

        return $this->db->get('tblwebtolead')->row();
    }

    public function add_form($data)
    {
        $data = $this->_do_lead_web_to_form_responsibles($data);
        $data['success_submit_msg'] = nl2br($data['success_submit_msg']);
        $data['form_key'] = md5(rand() . microtime());
        // Check if the key exists
        $this->db->where('form_key', $data['form_key']);
        $exists = $this->db->get('tblwebtolead')->row();
        if ($exists) {
            $data['form_key'] = md5(rand() . microtime());
        }

        if (isset($data['create_task_on_duplicate'])) {
            $data['create_task_on_duplicate'] = 1;
        } else {
            $data['create_task_on_duplicate'] = 0;
        }

        if (isset($data['mark_public'])) {
            $data['mark_public'] = 1;
        } else {
            $data['mark_public'] = 0;
        }

        if (isset($data['allow_duplicate'])) {
            $data['allow_duplicate'] = 1;
            $data['track_duplicate_field'] = '';
            $data['track_duplicate_field_and'] = '';
            $data['create_task_on_duplicate'] = 0;
        } else {
            $data['allow_duplicate'] = 0;
        }

        $data['dateadded'] = date('Y-m-d H:i:s');

        $this->db->insert('tblwebtolead', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            logActivity('New Web to Lead Form Added [' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    public function update_form($id, $data)
    {
        $data = $this->_do_lead_web_to_form_responsibles($data);
        $data['success_submit_msg'] = nl2br($data['success_submit_msg']);

        if (isset($data['create_task_on_duplicate'])) {
            $data['create_task_on_duplicate'] = 1;
        } else {
            $data['create_task_on_duplicate'] = 0;
        }

        if (isset($data['allow_duplicate'])) {
            $data['allow_duplicate'] = 1;
            $data['track_duplicate_field'] = '';
            $data['track_duplicate_field_and'] = '';
            $data['create_task_on_duplicate'] = 0;
        } else {
            $data['allow_duplicate'] = 0;
        }

        if (isset($data['mark_public'])) {
            $data['mark_public'] = 1;
        } else {
            $data['mark_public'] = 0;
        }

        $this->db->where('id', $id);
        $this->db->update('tblwebtolead', $data);

        return ($this->db->affected_rows() > 0 ? true : false);
    }

    public function delete_form($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblwebtolead');

        $this->db->where('from_form_id', $id);
        $this->db->update('tblleads', array(
            'from_form_id' => 0
        ));

        if ($this->db->affected_rows() > 0) {
            logActivity('Lead Form Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

    private function _do_lead_web_to_form_responsibles($data)
    {
        if (isset($data['notify_lead_imported'])) {
            $data['notify_lead_imported'] = 1;
        } else {
            $data['notify_lead_imported'] = 0;
        }

        if ($data['responsible'] == '') {
            $data['responsible'] = 0;
        }
        if ($data['notify_lead_imported'] != 0) {
            if ($data['notify_type'] == 'specific_staff') {
                if (isset($data['notify_ids_staff'])) {
                    $data['notify_ids'] = serialize($data['notify_ids_staff']);
                    unset($data['notify_ids_staff']);
                } else {
                    $data['notify_ids'] = serialize(array());
                    unset($data['notify_ids_staff']);
                }
                if (isset($data['notify_ids_roles'])) {
                    unset($data['notify_ids_roles']);
                }
            } else {
                if (isset($data['notify_ids_roles'])) {
                    $data['notify_ids'] = serialize($data['notify_ids_roles']);
                    unset($data['notify_ids_roles']);
                } else {
                    $data['notify_ids'] = serialize(array());
                    unset($data['notify_ids_roles']);
                }
                if (isset($data['notify_ids_staff'])) {
                    unset($data['notify_ids_staff']);
                }
            }
        } else {
            $data['notify_ids'] = serialize(array());
            $data['notify_type'] = null;
            if (isset($data['notify_ids_staff'])) {
                unset($data['notify_ids_staff']);
            }
            if (isset($data['notify_ids_roles'])) {
                unset($data['notify_ids_roles']);
            }
        }

        return $data;
    }

    /**
     * Added By Purvi on 10-18-2017 For Pin/Unpin Leads
     */
    public function pinlead($lead_id)
    {
        $session_data = get_session_data();
        $user_id = $session_data['staff_user_id'];

        $pinexist = $this->db->select('pinid')->from('tblpins')->where('pintype = "Lead" AND pintypeid=' . $lead_id . ' AND userid=' . $user_id)->get()->row();
        if (!empty($pinexist)) {
            $this->db->where('userid', $user_id);
            $this->db->where('pintypeid', $lead_id);
            $this->db->where('pintype', "Lead");
            $this->db->delete('tblpins');
            return 0;
        } else {
            $this->db->insert('tblpins', array(
                'pintype' => "Lead",
                'pintypeid' => $lead_id,
                'userid' => $user_id
            ));
            return 1;
        }

    }

    public function statuschange($lead_id, $status_id)
    {
        $this->db->select('status');
        $this->db->where('id', $lead_id);
        $_old = $this->db->get('tblleads')->row();

        $old_status = '';

        if ($_old) {
            $old_status = $this->get_status($_old->status);
            if ($old_status) {
                $old_statusid = $old_status->id;
                $old_status = $old_status->name;
            }
        }

        $affectedRows = 0;
        $current_status = $this->get_status($status_id)->name;

        $this->db->where('id', $lead_id);
        $this->db->update('tblleads', array(
            'status' => $status_id
        ));

        $_log_message = '';

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if ($current_status != $old_status && $old_status != '') {
                $_log_message = 'not_lead_activity_status_updated';
                $additional_data = serialize(array(
                    get_staff_full_name(),
                    $old_status,
                    $current_status
                ));
                $this->lead_status_changed_notification($lead_id, $current_status);
                do_action('lead_status_changed', array('lead_id' => $lead_id, 'old_status' => $old_status, 'new_status' => $current_status));
            }
            $this->db->where('id', $lead_id);
            $this->db->update('tblleads', array(
                'last_status_change' => date('Y-m-d H:i:s')
            ));

            /**
             * Added By: Vaidehi
             * Dt: 03/20/2018
             * for maintaining all statuses
             */

            $status_data = [];
            $status_data['old_statusid'] = $old_statusid;
            $status_data['new_statusid'] = $status_id;
            $status_data['leadid'] = $lead_id;
            $this->db->insert('tblleadstatushistory', $status_data);
        }
        if ($affectedRows > 0) {
            if ($_log_message == '') {
                return true;
            }
            $this->log_lead_activity($lead_id, $_log_message, false, $additional_data);
        }
    }

    /**
     * Get lead Detail for Lead dashboard
     * Added by Purvi on 10-27-2017
     */
    public function getleaddashboard($id = '', $where = array())
    {
        if (is_numeric($id)) {
            $this->db->where($where);
            $userid = get_staff_user_id();
            $this->db->select('tblleads.*,tbleventtype.eventtypename  as eventtypename,tblleadsstatus.name as status_name,tblleadssources.name as source_name, CONCAT(firstname, \' \', lastname) as assigned_name');
            $this->db->join('tblleadsstatus', 'tblleadsstatus.id=tblleads.status', 'left');
            $this->db->join('tbleventtype', 'tbleventtype.eventtypeid=tblleads.eventtypeid', 'left');
            $this->db->join('tblleadssources', 'tblleadssources.id=tblleads.source', 'left');
            $this->db->join('tblstaff', 'tblstaff.staffid=tblleads.assigned', 'left');

            $this->db->where('tblleads.id', $id);
            $lead = $this->db->get('tblleads')->row();

            $this->db->select('tblpins.pinid');
            $this->db->where('tblpins.userid', $userid);
            $this->db->where('tblpins.pintype', 'Lead');
            $this->db->where('tblpins.pintypeid', $id);
            $tblpins = $this->db->get('tblpins')->row();


            $this->db->select('id');
            $this->db->where('rel_type', "lead");
            $this->db->where_in('rel_id', $id);
            $this->db->where('deleted', 0);
            $this->db->where('brandid', get_user_session());
            $task_ids = $this->db->get('tblstafftasks')->result_array();
            $task_id_final = array();
            foreach ($task_ids as $ti) {
                array_push($task_id_final, $ti['id']);
            }

            $this->db->select('meetingid');
            $this->db->where('rel_type', "lead");
            $this->db->where('rel_id', $id);
            $this->db->where('deleted', 0);
            $this->db->where('brandid', get_user_session());
            $meeting_ids = $this->db->get('tblmeetings')->result_array();
            $meeting_id_final = array();
            foreach ($meeting_ids as $mi) {
                array_push($meeting_id_final, $mi['meetingid']);
            }
            $lasttask = array();
            $nexttask = array();
            $lastmeeting = array();
            $nextmeeting = array();
            $lastactiondates = array();
            $nextactiondates = array();

            if (!empty($task_id_final)) {
                $this->db->select('datefinished as lastaction,tblstafftasks.name as lastaction_name');
                $this->db->where('DATE(datefinished) <= CURRENT_DATE() AND DATE(datefinished)!="0000-00-00 00:00:00"');
                $this->db->where_in('id', $task_id_final);
                $this->db->order_by("lastaction", "desc");
                $this->db->limit(1);
                $lasttask = $this->db->get('tblstafftasks')->row();
                if (!empty($lasttask) && $lasttask->lastaction == "0000-00-00 00:00:00") {
                    $lasttask = array();
                }
                //$lastactiondates = $this->db->get('tblstafftasks')->row();

                $this->db->select('duedate as nextaction,tblstafftasks.name as nextaction_name');
                $this->db->where('DATE(duedate) > CURRENT_DATE()');
                $this->db->where_in('id', $task_id_final);
                $this->db->order_by("nextaction", "asc");
                $this->db->limit(1);
                $nexttask = $this->db->get('tblstafftasks')->row();
                //$nextactiondates = $this->db->get('tblstafftasks')->row();
            }

            if (!empty($meeting_id_final)) {
                // Get last meeting date
                $this->db->select('DATE(tblmeetings.start_date) as lastaction,tblmeetings.name as lastaction_name');
                $this->db->where('DATE(start_date) <= CURRENT_DATE()');
                $this->db->where_in('meetingid', $meeting_id_final);
                $this->db->order_by("lastaction", "desc");
                $this->db->limit(1);
                $lastmeeting = $this->db->get('tblmeetings')->row();

                // Get next meeting date
                $this->db->select('DATE(tblmeetings.start_date) as nextaction,tblmeetings.name as nextaction_name');
                $this->db->where('DATE(start_date) > CURRENT_DATE()');
                $this->db->where_in('meetingid', $meeting_id_final);
                $this->db->order_by("nextaction", "asc");
                $this->db->limit(1);
                $nextmeeting = $this->db->get('tblmeetings')->row();

                /*$lead->lastaction = isset($lastactiondates->lastaction) ? $lastactiondates->lastaction : "";
                $lead->nextaction = isset($nextactiondates->nextaction) ? $nextactiondates->nextaction : "";
                $lead->last_meeting_name = isset($lastactiondates->last_meeting_name) ? $lastactiondates->last_meeting_name : "Initial Inquiry";
                $lead->next_meeting_name = isset($nextactiondates->next_meeting_name) ? $nextactiondates->next_meeting_name : "";*/

            }
            if (!empty($meeting_id_final) || !empty($task_id_final)) {

                if (!empty($nexttask) && !empty($nextmeeting)) {
                    if (strtotime($nexttask->nextaction) > strtotime($nextmeeting->nextaction)) {
                        $nextactiondates = $nextmeeting;
                    } else {
                        $nextactiondates = $nexttask;
                    }
                } elseif (!empty($nexttask) && empty($nextmeeting)) {
                    $nextactiondates = $nexttask;
                } elseif (empty($nexttask) && !empty($nextmeeting)) {
                    $nextactiondates = $nextmeeting;
                }

                if (!empty($lasttask) && !empty($lastmeeting)) {
                    if (strtotime($lasttask->nextaction) > strtotime($lastmeeting->nextaction)) {
                        $lastactiondates = $lasttask;
                    } else {
                        $lastactiondates = $lastmeeting;
                    }
                } elseif (!empty($lasttask) && empty($lastmeeting)) {
                    $lastactiondates = $nexttask;
                } elseif (empty($lasttask) && !empty($lastmeeting)) {
                    $lastactiondates = $lastmeeting;
                }

                $lead->lastaction = isset($lastactiondates->lastaction) ? $lastactiondates->lastaction : "";
                $lead->nextaction = isset($nextactiondates->nextaction) ? $nextactiondates->nextaction : "";
                $lead->last_meeting_name = isset($lastactiondates->lastaction_name) ? $lastactiondates->lastaction_name : "Initial Inquiry";
                $lead->next_meeting_name = isset($nextactiondates->nextaction_name) ? $nextactiondates->nextaction_name : "";

            } else {
                $lead->lastaction = "";
                $lead->nextaction = "";
                $lead->last_meeting_name = "Initial Inquiry";
                $lead->next_meeting_name = "";
            }
            if ($lead) {
                if ($lead->from_form_id != 0) {
                    $lead->form_data = $this->get_form(array(
                        'id' => $lead->from_form_id
                    ));
                }
                $lead->attachments = $this->get_lead_attachments($id);
                $lead->pinid = isset($tblpins->pinid) ? $tblpins->pinid : 0;
            }

            $assignedOutput = '';
            if (!empty($lead->assigned_name)) {

                $full_name = $lead->assigned_name;

                $assignedOutput = '<a data-toggle="tooltip" data-title="' . $full_name . '"  href="javascript:void(0)">' . staff_profile_image($lead->assigned, array(
                        'staff-profile-image-small'
                    )) . '</a>';
            }
            $lead->assigned_name = $assignedOutput;

            //echo "<pre>";print_r($lead);exit;

            return $lead;
        }

        return $this->db->get('tblleads')->result_array();
    }

    function get_files($lead_id, $limit = "", $page = "", $is_kanban = false)
    {
        $this->db->where('rel_id', $lead_id);
        $this->db->where('rel_type', 'lead');
        if ($is_kanban == true && $limit > 0) {
            $start = ($page - 1) * $limit;
            $this->db->limit($limit, $start);
        }
        $_attachments = $this->db->get('tblfiles')->result_array();
        return $_attachments;
    }

    public function remove_file($id)
    {

        $this->db->where('id', $id);
        $file = $this->db->get('tblfiles')->row();
        if ($file) {
            $path = get_upload_path_by_type('lead') . $file->rel_id . '/';
            $fullPath = $path . $file->file_name;
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            $this->db->where('id', $id);
            $this->db->delete('tblfiles');
            if ($file->rel_type == "lead") {
                $this->leads_model->log_lead_activity($file->rel_id, 'not_lead_activity_attachment_deleted');
            }
            return true;
        }

        return false;
    }

    /*
       Created by Purvi on 12-19-2017 for convert lead to project.
    */
    public function convert_lead($lead_id, $clients = array())
    {

        /*foreach ($clients as $key => $clientid) {
            $contactdata = $this->addressbooks_model->get($clientid);
            $client_email = $contactdata->email[0]['email'];
            $where = array('email' => $client_email, 'deleted' => 0);
            $staff_exist = $this->db->where($where)->get('tblstaff')->row();
            echo "<pre>";
            print_r($staff_exist);
            if(empty($staff_exist)){
                echo "hii";
            }else{
                echo "hello";
            }
        }
        die('<--here');*/
        $dateadded = date('Y-m-d H:i:s');
        $brandid = get_user_session();
        $this->db->select('id');
        $this->db->where('brandid', $brandid);
        $this->db->where('isdefault', 1);
        $this->db->where('deleted', 0);
        $projectstatus = $this->db->get('tblprojectstatus')->row();
        $projectstatus = $projectstatus->id;


        /*$this->db->select('name,profile_image,assigned,dateadded,'.$projectstatus.',source,lastcontact,dateassigned,addedfrom,date_converted,eventtypeid,venueid,eventstartdatetime,eventenddatetime,eventtimezone,budget,sourcedetails,comments,brandid,"Lead",'.$lead_id);
        $this->db->where('id', $lead_id);
        $lead = $this->db->get('tblleads')->row();
        echo "<pre>";
        print_r($lead);
        die('<--here');*/
        //Insert Project from lead convert
        $project_sql = 'INSERT INTO `tblprojects`(`name`, `project_profile_image`, `assigned`, `dateadded`, `status`, `source`, `lastcontact`, `dateassigned`, `addedfrom`, `date_converted`, `eventtypeid`, `venueid`, `eventstartdatetime`, `eventenddatetime`, `eventtimezone`, `budget`, `sourcedetails`, `comments`, `brandid`, `created_type`, `convert_type_id`) SELECT  `name`, `profile_image`, `assigned`,`dateadded`, ' . $projectstatus . ', `source`, `lastcontact`, `dateassigned`, `addedfrom`, `date_converted`, `eventtypeid`, `venueid`, `eventstartdatetime`, `eventenddatetime`, `eventtimezone`, `budget`, `sourcedetails`, `comments`, `brandid`, "lead", ' . $lead_id . ' FROM tblleads WHERE `id` = ' . $lead_id;
        $this->db->query($project_sql);
        $project_id = $this->db->insert_id();

        $mydir = get_upload_path_by_type('project_profile_image') . "/" . $project_id . "/";
        if (!is_dir($mydir)) {
            mkdir($mydir);
        }
        $path = get_upload_path_by_type('lead_profile_image') . $lead_id . '/*.*';
        $files = glob($path);
        foreach ($files as $file) {
            $file_to_go = str_replace("lead_profile_images/" . $lead_id . "/", "project_profile_images/" . $project_id . "/", $file);
            copy($file, $file_to_go);
        }
        logActivity('New Project Created [ID: ' . $project_id . ']');

        $subproject_sql = 'INSERT INTO `tblprojects`(`name`, `project_profile_image`, `assigned`, `dateadded`, `status`, `source`, `lastcontact`, `dateassigned`, `addedfrom`, `date_converted`, `eventtypeid`,  `venueid`, `eventstartdatetime`, `eventenddatetime`, `eventtimezone`, `budget`, `sourcedetails`, `comments`, `brandid`, `parent`, `created_type`, `convert_type_id`) SELECT  `name`, `profile_image`, `assigned`,`dateadded`, ' . $projectstatus . ', `source`, `lastcontact`, `dateassigned`, `addedfrom`, `date_converted`, `eventtypeid`, `venueid`, `eventstartdatetime`, `eventenddatetime`, `eventtimezone`, `budget`, `sourcedetails`, `comments`, `brandid`, ' . $project_id . ', "lead", ' . $lead_id . ' FROM tblleads WHERE `id` = ' . $lead_id;
        $this->db->query($subproject_sql);
        $subproject_id = $this->db->insert_id();

        $mydir = get_upload_path_by_type('project_profile_image') . "/" . $subproject_id . "/";
        if (!is_dir($mydir)) {
            mkdir($mydir);
        }
        $path = get_upload_path_by_type('lead_profile_image') . $lead_id . '/*.*';
        $files = glob($path);
        foreach ($files as $file) {
            $file_to_go = str_replace("lead_profile_images/" . $lead_id . "/", "project_profile_images/" . $subproject_id . "/", $file);
            copy($file, $file_to_go);
        }

        logActivity('New Sub-project Created [ID: ' . $subproject_id . ']');

        /**
         * Modified By: Vaidehi
         * Dt : 03/08/2018
         * to mainitain lead converted
         */
        //update Lead
        $this->db->where('id', $lead_id);
        $this->db->update('tblleads', array(
            'converted' => 1,
            'updatedby' => get_staff_user_id(),
            'dateupdated' => date('Y-m-d H:i:s')
        ));

        // insert lead pin for project pin
        $pinproject_sql = 'INSERT INTO `tblpins`(`pintype`, `pintypeid`, `userid`) SELECT  "Project",' . $project_id . ', `userid` FROM tblpins WHERE `pintype` = "Lead" AND `pintypeid` = ' . $lead_id;
        $this->db->query($pinproject_sql);

        /*
            Get all messages from lead and convert it to project messages with all details
        */
        $this->db->select('id');
        $this->db->where('rel_id', $lead_id);
        $this->db->where('rel_type', 'lead');
        $this->db->where('deleted', 0);
        $lead_messages = $this->db->get('tblmessages')->result_array();
        $lead_messages = array_column($lead_messages, 'id');
        foreach ($lead_messages as $lmvalue) {
            // insert message for project
            $message_sql = 'INSERT INTO `tblmessages`(`subject`, `content`, `brandid`, `parent`, `rel_id`, `rel_type`, `deleted`, `created_by`, `created_by_type`, `created_date`) SELECT  `subject`, `content`, `brandid`, `parent`, ' . $project_id . ', "project", `deleted`, `created_by`, `created_by_type`, `created_date` FROM tblmessages WHERE `id` = ' . $lmvalue;
            $this->db->query($message_sql);
            $message_id = $this->db->insert_id();
            logActivity('New message Added [ID: ' . $message_id . ']');

            // insert message users for project messages
            $messagesusers_sql = 'INSERT INTO `tblmessagesusers`(`messageid`, `userid`, `usertype`) SELECT  ' . $message_id . ', `userid`, `usertype` FROM tblmessagesusers WHERE `messageid` = ' . $lmvalue;
            $this->db->query($messagesusers_sql);

            // insert message tags for project messages
            $messagetags_sql = 'INSERT INTO `tblmessagetags`(`messageid`, `tagid`) SELECT  ' . $message_id . ', `tagid` FROM tblmessagetags WHERE `messageid` = ' . $lmvalue;
            $this->db->query($messagetags_sql);

            // insert message notify for project messages
            $messagesnotify_sql = 'INSERT INTO `tblmessagesnotify`(`messageid`, `userid`, `usertype`) SELECT  ' . $message_id . ', `userid`, `usertype` FROM tblmessagesnotify WHERE `messageid` = ' . $lmvalue;
            $this->db->query($messagesnotify_sql);

            // insert message all users for project messages
            $messagesallusers_sql = 'INSERT INTO `tblmessagesallusers`(`messageid`, `userid`, `usertype`,`isread`) SELECT  ' . $message_id . ', `userid`, `usertype`, `isread` FROM tblmessagesallusers WHERE `messageid` = ' . $lmvalue;
            $this->db->query($messagesallusers_sql);

            // insert message attachment for project messages
            $messagesattachment_sql = 'INSERT INTO `tblmessagesattachment`(`messageid`, `name`) SELECT  ' . $message_id . ', `name` FROM tblmessagesattachment WHERE `messageid` = ' . $lmvalue;
            $this->db->query($messagesattachment_sql);

            // Move all lead message attachment to project message attachment and remove lead message attachment
            $messageattachment_old_path = get_upload_path_by_type('message') . $lmvalue . '/';
            $messageattachment_new_path = get_upload_path_by_type('message') . $message_id . '/';
            _maybe_create_upload_path($messageattachment_new_path);
            $this->copy_directory($messageattachment_old_path, $messageattachment_new_path);
            $this->delete_directory($messageattachment_old_path);
        }

        //Delete Lead Messages
        $this->db->where('rel_type', "lead");
        $this->db->where('id', $lead_id);
        $this->db->update('tblmessages', array(
            'deleted' => 1
        ));

        /*
            Get all files from lead and convert it to project files
        */
        $files_sql = 'INSERT INTO `tblfiles`(`rel_id`, `rel_type`, `file_name`, `filetype`, `visible_to_customer`, `attachment_key`, `external`, `external_link`, `thumbnail_link`, `staffid`, `contact_id`, `dateadded`, `brandid`) SELECT ' . $project_id . ', "project", `file_name`, `filetype`, `visible_to_customer`, `attachment_key`, `external`, `external_link`, `thumbnail_link`, `staffid`, `contact_id`, `dateadded`, `brandid` FROM tblfiles WHERE `rel_id` = ' . $lead_id . ' and `rel_type` = "lead"';
        $this->db->query($files_sql);
        $leadattachment_old_path = get_upload_path_by_type('lead') . $lead_id . '/';
        $leadattachment_new_path = get_upload_path_by_type('project') . $project_id . '/';
        _maybe_create_upload_path($leadattachment_new_path);
        $this->copy_directory($leadattachment_old_path, $leadattachment_new_path);
        $this->delete_directory($leadattachment_old_path);
        //Delete Lead Files
        $this->db->where('rel_id', $lead_id);
        $this->db->where('rel_type', "lead");
        $this->db->delete('tblfiles');

        /*
            Get all notes from lead and convert it to project notes
        */
        $notes_sql = 'INSERT INTO `tblnotes`(`rel_id`, `rel_type`, `description`, `date_contacted`, `addedfrom`, `dateadded`) SELECT ' . $project_id . ', "project", `description`, `date_contacted`, `addedfrom`, `dateadded` FROM tblnotes WHERE `rel_id` = ' . $lead_id . ' and `rel_type` = "lead"';
        $this->db->query($notes_sql);
        //Delete Lead Notes
        $this->db->where('rel_id', $lead_id);
        $this->db->where('rel_type', "lead");
        $this->db->delete('tblnotes');

        /*
            Get all meetings from lead and convert it to project meetings with all details
        */
        $this->db->select('meetingid');
        $this->db->where('rel_id', $lead_id);
        $this->db->where('rel_type', 'lead');
        $this->db->where('deleted', 0);
        $lead_meetings = $this->db->get('tblmeetings')->result_array();
        $lead_meetings = array_column($lead_meetings, 'meetingid');
        foreach ($lead_meetings as $lmevalue) {
            // insert message for project
            $meetings_sql = 'INSERT INTO `tblmeetings`( `name`, `status`, `location`, `start_date`, `end_date`, `duration`, `color`, `default_timezone`, `reminder`, `description`, `rel_id`, `rel_type`, `brandid`, `created_by`, `created_date`) SELECT   `name`, `status`, `location`, `start_date`, `end_date`, `duration`, `color`, `default_timezone`, `reminder`, `description`, ' . $project_id . ', "project", `brandid`, `created_by`, `created_date` FROM tblmeetings WHERE `meetingid` = ' . $lmevalue;
            $this->db->query($meetings_sql);
            $meeting_id = $this->db->insert_id();
            logActivity('New meeting Added [ID: ' . $meeting_id . ']');
            //Delete Lead meetings
            $this->db->where('rel_id', $lead_id);
            $this->db->where('rel_type', 'lead');
            $this->db->update('tblmeetings', array(
                'deleted' => 1
            ));

            // insert meeting reminders for project meeting
            $meetingreminders_sql = 'INSERT INTO `tblmeetingreminders`(`meetingid`, `duration`, `meetinginterval`) SELECT  ' . $meeting_id . ', `duration`, `meetinginterval` FROM tblmeetingreminders WHERE `meetingid` = ' . $lmevalue;
            $this->db->query($meetingreminders_sql);

            // insert meeting users for project meeting
            $meetingusers_sql = 'INSERT INTO `tblmeetingusers`(`meeting_id`, `user_id`, `contact_id`) SELECT  ' . $meeting_id . ', `user_id`, `contact_id` FROM tblmeetingusers WHERE `meeting_id` = ' . $lmevalue;
            $this->db->query($meetingusers_sql);

        }

        /*
            Get all contacts from lead and convert it to project contects
        */
        $contacts_sql = 'INSERT INTO `tblprojectcontact`(`projectid`, `contactid`, `brandid`) SELECT ' . $project_id . ', `contactid`, `brandid` FROM tblleadcontact WHERE `leadid` = ' . $lead_id;
        $this->db->query($contacts_sql);

        $sub_contacts_sql = 'INSERT INTO `tblprojectcontact`(`eventid`, `contactid`, `brandid`) SELECT ' . $subproject_id . ', `contactid`, `brandid` FROM tblleadcontact WHERE `leadid` = ' . $lead_id;
        $this->db->query($sub_contacts_sql);

        /*
            Get all tasks from lead and convert it to project tasks with all details
        */
        $this->db->select('id');
        $this->db->where('rel_id', $lead_id);
        $this->db->where('rel_type', 'lead');
        $this->db->where('deleted', 0);
        $lead_tasks = $this->db->get('tblstafftasks')->result_array();
        $lead_tasks = array_column($lead_tasks, 'id');
        foreach ($lead_tasks as $ltvalue) {
            // insert message for project
            $task_sql = 'INSERT INTO `tblstafftasks`(`name`, `description`, `priority`, `dateadded`, `startdate`, `duedate`, `datefinished`, `addedfrom`, `is_added_from_contact`, `status`, `rel_id`, `rel_type`, `is_public`,`kanban_order`, `brandid`, `created_by`) SELECT  `name`, `description`, `priority`, `dateadded`, `startdate`, `duedate`, `datefinished`, `addedfrom`, `is_added_from_contact`, `status`, ' . $project_id . ', "project", `is_public`,`kanban_order`, `brandid`, `created_by` FROM tblstafftasks WHERE `id` = ' . $ltvalue;
            $this->db->query($task_sql);
            $task_id = $this->db->insert_id();
            logActivity('New task Added [ID: ' . $task_id . ']');
            //Delete Lead meetings
            $this->db->where('rel_id', $lead_id);
            $this->db->where('rel_type', 'lead');
            $this->db->update('tblstafftasks', array(
                'deleted' => 1
            ));

            // insert task users for project task
            $taskassignees_sql = 'INSERT INTO `tblstafftaskassignees`(`staffid`, `taskid`, `assigned_from`,`is_assigned_from_contact`) SELECT  `staffid`,' . $task_id . ', `assigned_from`, `is_assigned_from_contact` FROM tblstafftaskassignees WHERE `taskid` = ' . $ltvalue;
            $this->db->query($taskassignees_sql);

            // insert task comments for project task
            $taskcomments_sql = 'INSERT INTO `tblstafftaskcomments`(`content`, `taskid`,`staffid`,`contact_id`,`file_id`,`dateadded`) SELECT  `content`, ' . $task_id . ',`staffid`,`contact_id`,`file_id`,"' . $dateadded . '" FROM tblstafftaskcomments WHERE `taskid` = ' . $ltvalue;
            $this->db->query($taskcomments_sql);

            // insert task checklist for project task
            $taskchecklists_sql = 'INSERT INTO `tbltaskchecklists`(`taskid`, `description`, `finished`, `dateadded`, `addedfrom`, `finished_from`, `list_order`) SELECT  ' . $task_id . ', `description`, `finished`, "' . $dateadded . '", `addedfrom`, `finished_from`, `list_order` FROM tbltaskchecklists WHERE `taskid` = ' . $ltvalue;
            $this->db->query($taskchecklists_sql);

            // insert task reminders for project task
            $taskreminders_sql = 'INSERT INTO `tbltaskreminders`(`taskid`, `duration`, `interval`) SELECT  ' . $task_id . ', `duration`, `interval` FROM tbltaskreminders WHERE `taskid` = ' . $ltvalue;
            $this->db->query($taskreminders_sql);

            // insert task tags for project task
            $taskstags_sql = 'INSERT INTO `tbltaskstags`(`taskid`, `tagid`) SELECT  ' . $task_id . ', `tagid` FROM tbltaskstags WHERE `taskid` = ' . $ltvalue;
            $this->db->query($taskstags_sql);

            // insert task tags for project task
            $taskstags_sql = 'INSERT INTO `tbltaskstags`(`taskid`, `tagid`) SELECT  ' . $task_id . ', `tagid` FROM tbltaskstags WHERE `taskid` = ' . $ltvalue;
            $this->db->query($taskstags_sql);

            // insert task pin for project task
            $pintask_sql = 'INSERT INTO `tblpins`(`pintype`, `pintypeid`, `userid`) SELECT  "Task",' . $task_id . ', `userid` FROM tblpins WHERE `pintype` = "Task" AND `pintypeid` = ' . $ltvalue;
            $this->db->query($pintask_sql);

            // insert task attachments for project task
            $tasksfiles_sql = 'INSERT INTO `tblfiles`(`rel_id`, `rel_type`, `file_name`, `filetype`, `visible_to_customer`, `attachment_key`, `external`, `external_link`, `thumbnail_link`, `staffid`, `contact_id`, `dateadded`, `brandid`) SELECT ' . $task_id . ', "task", `file_name`, `filetype`, `visible_to_customer`, `attachment_key`, `external`, `external_link`, `thumbnail_link`, `staffid`, `contact_id`, "' . $dateadded . '", `brandid` FROM tblfiles WHERE `rel_id` = ' . $ltvalue . ' and `rel_type` = "task"';
            $this->db->query($tasksfiles_sql);

            // Move all lead message attachment to project message attachment and remove lead message attachment
            $taskattachment_old_path = get_upload_path_by_type('task') . $ltvalue . '/';
            $taskattachment_new_path = get_upload_path_by_type('task') . $task_id . '/';
            _maybe_create_upload_path($taskattachment_new_path);
            $this->copy_directory($taskattachment_old_path, $taskattachment_new_path);
            $this->delete_directory($taskattachment_old_path);
        }

        /*
            Get all invoices from lead and convert it to project invoices with all details
        */
        $leadinvoices = $this->db->select('leaddate')->from('tblinvoices')->where('leadid', $lead_id)->get()->result_array();

        foreach ($leadinvoices as $leadinvoice) {
            $this->db->where('leadid', $lead_id);
            $this->db->update('tblinvoices', array(
                'project_id' => $project_id,
                'projectdate' => $leadinvoice['leaddate']
            ));
        }

        /*
            Get all Proposals from lead and convert it to project Proposals with all details
        */
        $this->db->select('*');
        $this->db->from('tblproposaltemplates');
        $this->db->where('rel_id', $lead_id);
        $this->db->where('rel_type', 'lead');
        $proposals = $this->db->get()->result_array();
        if (count($proposals) > 0) {
            foreach ($proposals as $proposal) {
                $this->db->where('rel_id', $lead_id);
                $this->db->where('rel_type', 'lead');
                $this->db->update('tblproposaltemplates', array(
                    'rel_id' => $project_id,
                    'rel_type' => "project"
                ));
            }
        }

        /*
           Get all assignee from lead and convert it to project assignee with all details
       */
        $this->db->select('assigned');
        $this->db->from('tblstaffleadassignee');
        $this->db->where('leadid', $lead_id);
        $assignees = $this->db->get()->result_array();
        if (count($assignees) > 0) {
            foreach ($assignees as $assignee) {
                $project_assignee = array('projectid' => $project_id, "assigned" => $assignee['assigned']);
                $this->db->insert('tblstaffprojectassignee', $project_assignee);
            }
        }

        if (!empty($clients)) {
            $this->load->model('projects_model');
            $this->load->model('register_model');
            $insert_id = $project_id;
            $pdet = $this->projects_model->get($insert_id);
            $session_data = get_session_data();
            if (isset($session_data['is_sido_admin'])) {
                $is_sido_admin = $session_data['is_sido_admin'];
                $is_admin = $session_data['is_admin'];
            } else {
                $is_sido_admin = 1;
                $is_admin = 1;
            }
            if ($is_sido_admin == 0 && $is_admin == 0) {
                $email_signature = get_brand_option('email_signature');
            } else {
                $email_signature = get_option('email_signature');
            }
            $vendor_name = get_staff_full_name();
            foreach ($clients as $key => $clientid) {
                $contactdata = $this->addressbooks_model->get($clientid);

                $client_email = $contactdata->email[0]['email'];

                $where = array('email' => $client_email, 'deleted' => 0);
                $staff_exist = $this->db->where($where)->get('tblstaff')->row();
                if (empty($staff_exist)) {

                    $firstname = $contactdata->firstname;
                    $query = $this->db->query('SELECT packageid FROM tblpackages WHERE name = "Free Package"');
                    $package = $query->row();

                    //generate random password
                    $password = $this->projects_model->randomPassword();

                    $staffdata = [];
                    $staffdata['firstname'] = $contactdata->firstname;
                    $staffdata['lastname'] = $contactdata->lastname;
                    $staffdata['email'] = $client_email;
                    $staffdata['password'] = $password;
                    $staffdata['random_pass'] = $password;
                    $staffdata['created_by'] = $this->session->userdata['staff_user_id'];
                    $staffdata['datecreated'] = date('Y-m-d H:i:s');
                    $staffdata['active'] = 0;
                    $staffdata['facebook'] = null;
                    $staffdata['twitter'] = null;
                    $staffdata['google'] = null;
                    $staffdata['brandname'] = $firstname;
                    $staffdata['brandtype'] = 1;
                    $staffdata['is_not_staff'] = 1;
                    $staffdata['user_type'] = 2;
                    $staffdata['packagetype'] = (isset($package->packageid) ? $package->packageid : 2);
                    $this->register_model->saveclient($staffdata, 'invite');
                    logActivity('New User Created [Email Address:' . $client_email . ' for invitation: ' . $insert_id . 'staffdata IP:' . $this->input->ip_address() . ']');
                    $where = array('email' => $client_email, 'deleted' => 0);
                    $staff_det = $this->db->where($where)->get('tblstaff')->row();
                } else {
                    $staff_det = $staff_exist;
                    $password = $staff_det->random_pass;
                }
                $project_contact = [];
                if ($pdet->parent == 0) {
                    $project_contact['projectid'] = $insert_id;
                } else {
                    $project_contact['projectid'] = 0;
                    $project_contact['eventid'] = $insert_id;
                }
                $project_contact['projectid'] = $insert_id;
                $project_contact['contactid'] = $staff_det->staffid;
                $project_contact['brandid'] = get_user_session();
                //for client
                $project_contact['isvendor'] = 0;
                $project_contact['iscollaborator'] = 0;
                $project_contact['isclient'] = 1;
                $this->db->insert('tblprojectcontact', $project_contact);

                $staff_brand = [];
                $staff_brand['active'] = 1;
                $staff_brand['staffid'] = $staff_det->staffid;
                $staff_brand['brandid'] = get_user_session();
                $this->db->insert('tblstaffbrand', $staff_brand);
                $this->db->where('staffid', $staff_det->staffid);
                $this->db->update('tblstaff', array('active' => 1));

                $event = 'Event: ' . $pdet->name . '<br/><br/>';
                $event .= 'Type: ' . $pdet->eventtypename . '<br/><br/>';
                $event .= 'From: ' . _dt($pdet->eventstartdatetime) . '<br/><br/>';
                $event .= 'To: ' . _dt($pdet->eventenddatetime) . '<br/><br/>';

                $message = '<h2><span style="font-size: 14pt;">Hi ' . $firstname . '</span>,</h2>Event details below:<br/><br/>' . $event . '<br/><br/>Please click on the <a href="' . admin_url("projects/dashboard/" . $insert_id) . '">View Event</a> to view the details. You can login with following credentials: <br/><br/>Email: ' . $client_email . '<br/><br/>Password: ' . $password . '<br/><br/> If you have any questions or concerns, please do not hesitate to contact at:<br/><br/>Name: ' . $pdet->assigned_name . '<br/><br/>Phone: ' . $pdet->assigned_phone . '<br/><br/>Email: ' . $pdet->assigned_email . '<br/><br/>Sincerely,<br/><br/>' . $vendor_name . ' ' . $email_signature;
                $this->emails_model->send_simple_email($client_email, "Your lead converted to project", $message);

            }
        }

    }

    /*
       Created by Purvi on 12-19-2017 for Copy files from one folder to another folder.
    */
    function copy_directory($src, $dst)
    {
        if (is_dir($src)) {
            $dir = opendir($src);
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($src . '/' . $file)) {
                        recurse_copy($src . '/' . $file, $dst . '/' . $file);
                    } else {
                        copy($src . '/' . $file, $dst . '/' . $file);
                    }
                }
            }
            closedir($dir);
        }

    }

    /*
       Created by Purvi on 12-19-2017 for delete files and folder.
    */
    function delete_directory($dirPath)
    {
        if (is_dir($dirPath)) {
            if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
                $dirPath .= '/';
            }
            $files = glob($dirPath . '*', GLOB_MARK);
            foreach ($files as $file) {
                if (is_dir($file)) {
                    self::deleteDir($file);
                } else {
                    unlink($file);
                }
            }
            rmdir($dirPath);
        }
    }

    public function getlead($id = '', $where = array())
    {
        $this->db->select('*,tblleads.name, tblleads.id,tblleadsstatus.name as status_name,tblleadssources.name as source_name');
        $this->db->join('tblleadsstatus', 'tblleadsstatus.id=tblleads.status', 'left');
        $this->db->join('tblleadssources', 'tblleadssources.id=tblleads.source', 'left');

        //added by vaidehi on 03/08/2018
        $this->db->where('converted', 0);

        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where('tblleads.id', $id);
            $lead = $this->db->get('tblleads')->row();
            if ($lead) {
                if ($lead->from_form_id != 0) {
                    $lead->form_data = $this->get_form(array(
                        'id' => $lead->from_form_id
                    ));
                }
                $lead->attachments = $this->get_lead_attachments($id);
            }

            return $lead;
        }

        return $this->db->get('tblleads')->result_array();
    }
}