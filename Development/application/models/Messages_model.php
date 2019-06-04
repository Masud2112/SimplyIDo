<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Messages_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get message by id
     * @param mixed $id message id
     * @return mixed     if id passed return object else array
     */
    public function get($lid = "", $pid = "", $eid = "", $where = array(), $limit = "", $page = "", $is_kanban = false, $search = "")
    {
        $user_id = get_staff_user_id();

        if (isset($eid) && $eid != "") {
            $pid = $eid;
        }

        if ($pid != "") {
            $this->db->select('id');
            $this->db->where('(parent = ' . $pid . ' OR id = ' . $pid . ')');
            $this->db->where('deleted', 0);
            $related_project_ids = $this->db->get('tblprojects')->result_array();
        } else {
            $related_project_ids = array();
        }

        $brandid = get_user_session();
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
        $staffid = $this->session->userdata['staff_user_id'];
        $this->db->select('tblmessages.id,tblmessages.subject,tblmessages.created_date,tblmessages.rel_type,tblmessages.rel_id, IFNULL((SELECT mu.isread FROM tblmessages m JOIN tblmessagesallusers mu ON (mu.messageid = m.id AND mu.isread = 0 AND mu.userid = ' . $this->session->userdata['staff_user_id'] . ') WHERE m.id = tblmessages.id OR m.parent = tblmessages.id limit 1),1) as isread,(select count(*) from tblmessages as tm where tm.parent=tblmessages.id and deleted = 0) as chilemessages, (SELECT GROUP_CONCAT(t.name) FROM tbltags as t INNER JOIN tblmessagetags ON tblmessagetags.tagid = t.id Where tblmessagetags.messageid = tblmessages.id and t.id=tblmessagetags.tagid and t.deleted=0) as tags,(select count(*) from tblmessagesattachment as tma where tma.messageid=tblmessages.id OR tma.messageid IN (select GROUP_CONCAT(tm.id) from tblmessages as tm where tm.parent=tblmessages.id and deleted = 0) ) as attachments,IFNULL((SELECT tm.created_by FROM tblmessages tm where tm.parent = tblmessages.id order by tm.created_date desc limit 1),tblmessages.created_by) as created_by,IFNULL((SELECT "child" FROM tblmessages tm where tm.parent = tblmessages.id order by tm.created_date desc limit 1),"parent") as created_by_check_type, (SELECT pinid FROM tblpins WHERE tblpins.userid = ' . $user_id . ' and tblpins.pintype = "Message" and tblpins.pintypeid = tblmessages.id) as pinned, (select GROUP_CONCAT(tmu.usertype,"-",tmu.userid) from tblmessagesusers as tmu where tmu.messageid=tblmessages.id) as messageusers , IFNULL((SELECT tm.created_by_type FROM tblmessages tm where tm.parent = tblmessages.id order by tm.created_date desc limit 1),tblmessages.created_by_type) as created_by_type');

        if (isset($where) && $where != "") {
            $this->db->where($where);
        }

        if ($brandid > 0) {
            $this->db->where('brandid', $brandid);
        } else if ($is_sido_admin > 0) {
            $this->db->where('brandid', 0);
        }
        //$this->db->where('deleted', 0);

        $this->db->join('tblmessagesusers', 'tblmessagesusers.messageid = tblmessages.id', 'left');
        $this->db->join('tblmessagesnotify', 'tblmessagesnotify.messageid = tblmessages.id', 'left');


        $this->db->where("((tblmessagesusers.usertype = 'teammember' AND  tblmessagesusers.userid = $staffid ) OR (tblmessagesnotify.usertype = 'teammember' AND  tblmessagesnotify.userid = $staffid )) ");
        //$this->db->where('tblmessagesusers.usertype', 'teammember');
        //$this->db->where('tblmessagesusers.userid', $this->session->userdata['staff_user_id']);

        if ($lid != "") {
            $this->db->where('tblmessages.rel_id', $lid);
            $this->db->where('tblmessages.rel_type', "lead");
        }

        if ($pid != "") {
            $related_project_ids = array_column($related_project_ids, 'id');
            if (!empty($related_project_ids)) {
                $related_project_ids = implode(",", $related_project_ids);
                $this->db->where('tblmessages.rel_id in(' . $related_project_ids . ')');
                $this->db->where('tblmessages.rel_type in("project", "event")');
            } else {
                $this->db->where('tblmessages.rel_id = ' . $pid);
                $this->db->where('tblmessages.rel_type = "project"');
            }
        }

        if ($eid != "") {
            $this->db->where('tblmessages.rel_id', $eid);
            $this->db->where('tblmessages.rel_type', "event");
        }
        if (!empty($search)) {
            $this->db->like('tblmessages.subject', $search);
        }
        $this->db->where('tblmessages.deleted', 0);
        $this->db->where('tblmessages.parent', 0);
        $this->db->order_by('created_date', 'DESC');
        $this->db->group_by('tblmessages.id');
        if ($is_kanban == true && $limit > 0) {
            $start = ($page - 1) * $limit;
            $this->db->limit($limit, $start);
        }
        $messages = $this->db->get('tblmessages')->result_array();
        /*echo $this->db->last_query();
        echo "<pre>";print_r($messages);exit;*/
        return $messages;
    }

    public function getmessagedetails($id = "")
    {
        $brandid = get_user_session();
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
        $this->db->select('tblmessages.rel_type,tblmessages.rel_id ,tblmessages.id,tblmessages.subject,tblmessages.content, tblmessages.created_by,tblmessages.created_by_type,IF(created_by_type = "teammember",CONCAT(tblstaff.firstname, " ", tblstaff.lastname), IF(created_by_type = "contact", CONCAT(tbladdressbook.firstname, " ", tbladdressbook.lastname), CONCAT(tblstaff.firstname, " ", tblstaff.lastname))) AS created_by_name,tblmessages.created_date,tblmessages.brandid');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblmessages.created_by', 'left');
        $this->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblmessages.created_by', 'left');
        $this->db->where('tblmessages.id', $id);
        $this->db->where('tblmessages.deleted = 0');
        $message = $this->db->get('tblmessages')->row();

        $this->db->select('name');
        $this->db->where('messageid', $id);
        $attachments = $this->db->get('tblmessagesattachment')->result_array();
        $attachments = array_column($attachments, 'name');

        $message_user = $contact_user = array();
        $this->db->select('CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as message_user');
        $this->db->where('messageid', $id);
        $this->db->where('usertype', 'teammember');
        //$this->db->where('userid != '. $message->created_by);
        $this->db->join('tblstaff', 'tblstaff.staffid = tblmessagesusers.userid', 'left');
        $message_user = $this->db->get('tblmessagesusers')->result_array();
        $message_user = array_column($message_user, 'message_user');

        $this->db->select("IF(usertype = 'teammember',CONCAT('tm_',userid), IF(usertype = 'contact', CONCAT('cn_',userid),userid)) AS nValue");
        $this->db->where('messageid', $id);
        $msg_user = $this->db->get('tblmessagesnotify')->result_array();
        $msg_user = array_column($msg_user, 'nValue');

        $this->db->select('CONCAT(tbladdressbook.firstname, " ", tbladdressbook.lastname) as contact_user');
        $this->db->where('messageid', $id);
        //$this->db->where('userid != '. $message->created_by);
        $this->db->where('usertype', 'contact');
        $this->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblmessagesusers.userid', 'left');
        $contact_user = $this->db->get('tblmessagesusers')->result_array();
        $contact_user = array_column($contact_user, 'contact_user');

        $message_users = array_merge($message_user, $contact_user);

        $message_user_to = $contact_user_to = array();
        $this->db->select('CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as message_user_to');
        $this->db->where('messageid', $id);
        $this->db->where('usertype', 'teammember');
        $this->db->where('userid != ' . $message->created_by);
        $this->db->join('tblstaff', 'tblstaff.staffid = tblmessagesnotify.userid', 'left');
        $message_user_to = $this->db->get('tblmessagesnotify')->result_array();
        $message_user_to = array_column($message_user_to, 'message_user_to');

        $this->db->select('CONCAT(tbladdressbook.firstname, " ", tbladdressbook.lastname) as contact_user_to');
        $this->db->where('messageid', $id);
        $this->db->where('userid != ' . $message->created_by);
        $this->db->where('usertype', 'contact');
        $this->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblmessagesnotify.userid', 'left');
        $contact_user_to = $this->db->get('tblmessagesnotify')->result_array();
        $contact_user_to = array_column($contact_user_to, 'contact_user_to');

        $message_users_to = array_merge($message_user_to, $contact_user_to);

        $message->attachments = $attachments;
        $message->users = $message_users_to;
        $message->prefixuser = $msg_user;
        $message->privacy = $message_users;

        // Get child messages
        $this->db->select('tblmessages.id,tblmessages.subject,tblmessages.content, tblmessages.created_by,tblmessages.created_by_type,IF(created_by_type = "teammember",CONCAT(tblstaff.firstname, " ", tblstaff.lastname), IF(created_by_type = "contact", CONCAT(tbladdressbook.firstname, " ", tbladdressbook.lastname), CONCAT(tblstaff.firstname, " ", tblstaff.lastname))) AS created_by_name, tblmessages.created_date,tblmessages.brandid');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblmessages.created_by', 'left');
        $this->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblmessages.created_by', 'left');
        $this->db->where('parent', $id);
        $this->db->where('tblmessages.deleted = 0');
        $child_messages = $this->db->get('tblmessages')->result_array();
        $child_messages_data = $child_messages_final_data = array();
        if (!empty($child_messages)) {
            foreach ($child_messages as $child_message) {
                $child_messages_data['id'] = $child_message['id'];
                $child_messages_data['content'] = $child_message['content'];
                $child_messages_data['created_by'] = $child_message['created_by'];
                $child_messages_data['created_by_type'] = $child_message['created_by_type'];
                $child_messages_data['created_by_name'] = $child_message['created_by_name'];
                $child_messages_data['created_date'] = $child_message['created_date'];
                $child_messages_data['brandid'] = $child_message['brandid'];

                $this->db->select('name');
                $this->db->where('messageid', $child_message['id']);
                $attachments = $this->db->get('tblmessagesattachment')->result_array();
                $attachments = array_column($attachments, 'name');

                $message_user_to = $contact_user_to = array();
                $this->db->select('CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as message_user_to');
                $this->db->where('messageid', $child_message['id']);
                $this->db->where('usertype', 'teammember');
                //$this->db->where('userid != '. $child_message['created_by']);
                $this->db->join('tblstaff', 'tblstaff.staffid = tblmessagesnotify.userid', 'left');
                $message_user_to = $this->db->get('tblmessagesnotify')->result_array();
                $message_user_to = array_column($message_user_to, 'message_user_to');

                $this->db->select('CONCAT(tbladdressbook.firstname, " ", tbladdressbook.lastname) as contact_user_to');
                $this->db->where('messageid', $child_message['id']);
                //$this->db->where('userid != '.$child_message['created_by']);
                $this->db->where('usertype', 'contact');
                $this->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblmessagesnotify.userid', 'left');
                $contact_user_to = $this->db->get('tblmessagesnotify')->result_array();
                $contact_user_to = array_column($contact_user_to, 'contact_user_to');

                $message_users_to = array_merge($message_user_to, $contact_user_to);
                $child_messages_data['attachments'] = $attachments;
                $child_messages_data['users'] = $message_users_to;
                $child_messages_final_data[] = $child_messages_data;
            }
            $message->child_message = $child_messages_final_data;
        } else {
            $message->child_message = array();
        }
        //echo "<pre>";print_r($message);exit;
        return $message;
    }

    public function getclientmessagedetails($id = "")
    {
        $brandid = get_user_session();
        $session_data = get_session_data();
        $this->db->select('tblmessages.id,tblmessages.subject,tblmessages.content, tblmessages.created_by,CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as created_by_name,tblmessages.created_date,tblmessages.brandid');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblmessages.created_by');
        $this->db->where('id', $id);
        $this->db->where('tblmessages.deleted = 0');
        $message = $this->db->get('tblmessages')->row();

        $this->db->select('name');
        $this->db->where('messageid', $id);
        $attachments = $this->db->get('tblmessagesattachment')->result_array();
        $attachments = array_column($attachments, 'name');

        $message_user = $contact_user = array();
        $this->db->select('CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as message_user');
        $this->db->where('messageid', $id);
        $this->db->where('usertype', 'teammember');
        //$this->db->where('userid != '. $message->created_by);
        $this->db->join('tblstaff', 'tblstaff.staffid = tblmessagesusers.userid', 'left');
        $message_user = $this->db->get('tblmessagesusers')->result_array();
        $message_user = array_column($message_user, 'message_user');

        $this->db->select("IF(usertype = 'teammember',CONCAT('tm_',userid), IF(usertype = 'contact', CONCAT('cn_',userid),userid)) AS nValue");
        $this->db->where('messageid', $id);
        $msg_user = $this->db->get('tblmessagesnotify')->result_array();
        $msg_user = array_column($msg_user, 'nValue');

        $this->db->select('CONCAT(tbladdressbook.firstname, " ", tbladdressbook.lastname) as contact_user');
        $this->db->where('messageid', $id);
        //$this->db->where('userid != '. $message->created_by);
        $this->db->where('usertype', 'contact');
        $this->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblmessagesusers.userid', 'left');
        $contact_user = $this->db->get('tblmessagesusers')->result_array();
        $contact_user = array_column($contact_user, 'contact_user');

        $message_users = array_merge($message_user, $contact_user);

        $message_user_to = $contact_user_to = array();
        $this->db->select('CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as message_user_to');
        $this->db->where('messageid', $id);
        $this->db->where('usertype', 'teammember');
        $this->db->where('userid != ' . $message->created_by);
        $this->db->join('tblstaff', 'tblstaff.staffid = tblmessagesnotify.userid', 'left');
        $message_user_to = $this->db->get('tblmessagesnotify')->result_array();
        $message_user_to = array_column($message_user_to, 'message_user_to');

        $this->db->select('CONCAT(tbladdressbook.firstname, " ", tbladdressbook.lastname) as contact_user_to');
        $this->db->where('messageid', $id);
        $this->db->where('userid != ' . $message->created_by);
        $this->db->where('usertype', 'contact');
        $this->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblmessagesnotify.userid', 'left');
        $contact_user_to = $this->db->get('tblmessagesnotify')->result_array();
        $contact_user_to = array_column($contact_user_to, 'contact_user_to');

        $message_users_to = array_merge($message_user_to, $contact_user_to);

        $message->attachments = $attachments;
        $message->users = $message_users_to;
        $message->prefixuser = $msg_user;
        $message->privacy = $message_users;

        // Get child messages
        $this->db->select('tblmessages.id,tblmessages.subject,tblmessages.content, tblmessages.created_by,CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as created_by_name,tblmessages.created_date,tblmessages.brandid');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblmessages.created_by');
        $this->db->where('parent', $id);
        $this->db->where('tblmessages.deleted = 0');
        $child_messages = $this->db->get('tblmessages')->result_array();
        $child_messages_data = $child_messages_final_data = array();
        if (!empty($child_messages)) {
            foreach ($child_messages as $child_message) {
                $child_messages_data['id'] = $child_message['id'];
                $child_messages_data['content'] = $child_message['content'];
                $child_messages_data['created_by'] = $child_message['created_by'];
                $child_messages_data['created_by_name'] = $child_message['created_by_name'];
                $child_messages_data['created_date'] = $child_message['created_date'];
                $child_messages_data['brandid'] = $child_message['brandid'];

                $this->db->select('name');
                $this->db->where('messageid', $child_message['id']);
                $attachments = $this->db->get('tblmessagesattachment')->result_array();
                $attachments = array_column($attachments, 'name');

                $message_user_to = $contact_user_to = array();
                $this->db->select('CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as message_user_to');
                $this->db->where('messageid', $child_message['id']);
                $this->db->where('usertype', 'teammember');
                $this->db->where('userid != ' . $child_message['created_by']);
                $this->db->join('tblstaff', 'tblstaff.staffid = tblmessagesnotify.userid', 'left');
                $message_user_to = $this->db->get('tblmessagesnotify')->result_array();
                $message_user_to = array_column($message_user_to, 'message_user_to');

                $this->db->select('CONCAT(tbladdressbook.firstname, " ", tbladdressbook.lastname) as contact_user_to');
                $this->db->where('messageid', $child_message['id']);
                $this->db->where('userid != ' . $child_message['created_by']);
                $this->db->where('usertype', 'contact');
                $this->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblmessagesnotify.userid', 'left');
                $contact_user_to = $this->db->get('tblmessagesnotify')->result_array();
                $contact_user_to = array_column($contact_user_to, 'contact_user_to');

                $message_users_to = array_merge($message_user_to, $contact_user_to);
                $child_messages_data['attachments'] = $attachments;
                $child_messages_data['users'] = $message_users_to;
                $child_messages_final_data[] = $child_messages_data;
            }
            $message->child_message = $child_messages_final_data;
        } else {
            $message->child_message = array();
        }
        //echo "<pre>";print_r($message);exit;
        return $message;
    }

    /**
     * Add new message
     * @param array $data message data
     * @return boolean
     */
    public function add($data)
    {
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

        $data['subject'] = trim($data['subject']);
        $data["brandid"] = get_user_session();
        $data['created_by'] = $this->session->userdata['staff_user_id'];
        $data['created_by_type'] = "teammember";
        $data['created_date'] = date('Y-m-d H:i:s');

        $teammember = $contact = $tags = $contactmessageto = $teammembermessageto = array();
        if (isset($data['privacy']) && !empty($data['privacy'])) {
            foreach ($data['privacy'] as $p) {
                $pdata = explode("_", $p);
                if ($pdata[0] == "tm") {
                    $teammember[] = $pdata[1];
                } else {
                    $contact[] = $pdata[1];
                }
            }
            unset($data['privacy']);
        }
        if (isset($data['message_to']) && !empty($data['message_to'])) {
            foreach ($data['message_to'] as $p) {
                $pdata = explode("_", $p);
                if ($pdata[0] == "tm") {
                    $teammembermessageto[] = $pdata[1];
                } else {
                    $contactmessageto[] = $pdata[1];
                }
            }
            unset($data['message_to']);
        }
        $allusers = array_unique(array_merge($teammember, $teammembermessageto));

        if (isset($data['tags'])) {
            $tags = $data['tags'];
            unset($data['tags']);
        }
        unset($data['hdnlid']);
        unset($data['hdnpid']);
        unset($data['hdneid']);

        $this->db->insert('tblmessages', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {

            if (!empty($teammember)) {
                foreach ($teammember as $t) {
                    if ($this->session->userdata['staff_user_id'] == $t) {
                        $this->db->insert('tblmessagesusers', array(
                            'messageid' => $insert_id,
                            'usertype' => 'teammember',
                            'userid' => $t
                        ));
                    } else {
                        $this->db->insert('tblmessagesusers', array(
                            'messageid' => $insert_id,
                            'usertype' => 'teammember',
                            'userid' => $t
                        ));
                    }
                }
                if (!in_array($this->session->userdata['staff_user_id'], $teammember)) {
                    $this->db->insert('tblmessagesusers', array(
                        'messageid' => $insert_id,
                        'usertype' => 'teammember',
                        'userid' => $this->session->userdata['staff_user_id']
                    ));
                }
            }

            if (!empty($contact)) {
                foreach ($contact as $c) {
                    $cemail=get_addressbook_email($c);
                    $staffid = get_staff_details_by_email($cemail,'staffid');
                    if(!empty($staffid) && $staffid > 0){
                        $this->db->insert('tblmessagesusers', array(
                            'messageid' => $insert_id,
                            'usertype' => 'teammember',
                            'userid' => $staffid
                        ));
                        $this->db->insert('tblmessagesallusers', array(
                            'messageid' => $insert_id,
                            'usertype' => 'teammember',
                            'userid' => $staffid
                        ));
                    }
                    $this->db->insert('tblmessagesusers', array(
                        'messageid' => $insert_id,
                        'usertype' => 'contact',
                        'userid' => $c
                    ));
                }
            }

            if (!empty($teammembermessageto)) {
                foreach ($teammembermessageto as $t) {
                    if ($this->session->userdata['staff_user_id'] == $t) {
                        $this->db->insert('tblmessagesnotify', array(
                            'messageid' => $insert_id,
                            'usertype' => 'teammember',
                            'userid' => $t
                        ));
                    } else {
                        $this->db->insert('tblmessagesnotify', array(
                            'messageid' => $insert_id,
                            'usertype' => 'teammember',
                            'userid' => $t
                        ));
                    }
                }
                if (!in_array($this->session->userdata['staff_user_id'], $teammembermessageto)) {
                    $this->db->insert('tblmessagesnotify', array(
                        'messageid' => $insert_id,
                        'usertype' => 'teammember',
                        'userid' => $this->session->userdata['staff_user_id']
                    ));
                }
            } else {
                $this->db->insert('tblmessagesnotify', array(
                    'messageid' => $insert_id,
                    'usertype' => 'teammember',
                    'userid' => $this->session->userdata['staff_user_id'],
                ));
            }


            if (!empty($allusers)) {
                foreach ($allusers as $t) {
                    if ($this->session->userdata['staff_user_id'] == $t) {
                        $this->db->insert('tblmessagesallusers', array(
                            'messageid' => $insert_id,
                            'usertype' => 'teammember',
                            'userid' => $t,
                            'isread' => 1
                        ));
                    } else {
                        $this->db->insert('tblmessagesallusers', array(
                            'messageid' => $insert_id,
                            'usertype' => 'teammember',
                            'userid' => $t
                        ));
                    }

                    $this->message_new_created_notification($insert_id,$t);
                }
                if (!in_array($this->session->userdata['staff_user_id'], $allusers)) {
                    $this->db->insert('tblmessagesallusers', array(
                        'messageid' => $insert_id,
                        'usertype' => 'teammember',
                        'userid' => $this->session->userdata['staff_user_id'],
                        'isread' => 1
                    ));
                }
            }

            if (!empty($contactmessageto)) {
                foreach ($contactmessageto as $cm) {
                    $this->db->insert('tblmessagesnotify', array(
                        'messageid' => $insert_id,
                        'usertype' => 'contact',
                        'userid' => $cm
                    ));

                    $cemail=get_addressbook_email($cm);
                    $staffid = get_staff_details_by_email($cemail,'staffid');
                    if(!empty($staffid) && $staffid > 0){
                        $this->db->insert('tblmessagesnotify', array(
                            'messageid' => $insert_id,
                            'usertype' => 'teammember',
                            'userid' => $staffid
                        ));
                        $this->db->insert('tblmessagesallusers', array(
                            'messageid' => $insert_id,
                            'usertype' => 'teammember',
                            'userid' => $staffid
                        ));
                    }
                }
            }

            if (!empty($tags)) {
                foreach ($tags as $t) {
                    $this->db->insert('tblmessagetags', array(
                        'messageid' => $insert_id,
                        'tagid' => $t
                    ));
                }
            }

            logActivity('New message Added [ID: ' . $insert_id . ', Name:' . $data['subject'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Delete message from database
     * @param mixed $id message id
     * @return boolean
     */
    public function delete($id)
    {
        $affectedRows = 0;
        $brandid = get_user_session();
        $data['deleted'] = 1;
        $data['updated_by'] = $this->session->userdata['staff_user_id'];
        $data['dateupdated'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->where('brandid', $brandid);
        $this->db->update('tblmessages', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            logActivity('Message deleted successfully');

            return true;
        }

        return false;
    }

    /* Added by Purvi on 11-17-2017 for get contacts for send messages */
    public function getcontacts($message_id = "")
    {
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
        $messagesusers = array();
        if ($message_id > 0) {
            $this->db->where('tblmessagesusers.usertype', 'contact');
            $this->db->where('tblmessagesusers.messageid', $message_id);
            $messagesusers = $this->db->get('tblmessagesusers')->result_array();
        }
        $this->db->select('tbladdressbook.addressbookid, CONCAT(tbladdressbook.firstname, " ", tbladdressbook.lastname) as contact_name, tbladdressbookemail.email');
        if ($is_sido_admin == 0 && $is_admin == 0) {
            $brandid = get_user_session();

            $this->db->join('tbladdressbook_client', 'tbladdressbook_client.addressbookid = tbladdressbook.addressbookid');
            $this->db->where('tbladdressbook_client.brandid', get_user_session());
        }

        if (!empty($messagesusers)) {
            $this->db->join('tblmessagesusers', 'tblmessagesusers.userid = tbladdressbook.addressbookid');
            $this->db->where('tblmessagesusers.usertype', 'contact');
            $this->db->where('tblmessagesusers.messageid', $message_id);
        }

        $this->db->join('tbladdressbookemail', 'tbladdressbookemail.addressbookid=tbladdressbook.addressbookid', 'left');
        if ($this->input->get('lid')) {
            $this->db->join('tblleadcontact', 'tblleadcontact.contactid=tbladdressbook.addressbookid');
            $this->db->where('tblleadcontact.leadid', $this->input->get('lid'));
        }
        if ($this->input->get('pid')) {
            $this->db->join('tblprojectcontact', 'tblprojectcontact.contactid=tbladdressbook.addressbookid');
            $this->db->where('tblprojectcontact.projectid', $this->input->get('pid'));
        }
        $this->db->where('tbladdressbook.deleted', 0);
        $this->db->where('tbladdressbook_client.deleted', 0);
        $this->db->where('tbladdressbookemail.type', 'primary');
        $this->db->where('tbladdressbookemail.type != ""');

        $addressbook = $this->db->get('tbladdressbook')->result_array();
        //echo $this->db->last_query();exit;
        return $addressbook;
    }

    /* Added by Purvi on 11-17-2017 for get team members for send messages */
    public function getteammember($message_id = "")
    {
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
        $messagesusers = array();
        if ($message_id > 0) {
            $this->db->where('tblmessagesusers.usertype', 'teammember');
            $this->db->where('tblmessagesusers.messageid', $message_id);
            $messagesusers = $this->db->get('tblmessagesusers')->result_array();
        }
        $this->db->select('tblstaff.staffid, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name, tblstaff.email');
        if ($is_sido_admin == 0 && $is_admin == 0) {
            $this->db->join('tblstaffbrand', 'tblstaffbrand.staffid = tblstaff.staffid');
            $this->db->where('tblstaffbrand.brandid', get_user_session());
        }
        if ($this->input->get('lid') || $this->input->get('pid')) {
            $this->db->where('tblstaff.user_type', 1, 'OR');
        }
        if (!empty($messagesusers)) {
            $this->db->join('tblmessagesusers', 'tblmessagesusers.userid = tblstaff.staffid');
            $this->db->where('tblmessagesusers.usertype', 'teammember');
            $this->db->where('tblmessagesusers.messageid', $message_id);
        }
        $this->db->where('tblstaff.active', 1);
        $this->db->where('tblstaff.deleted', 0);
        $this->db->where('tblstaff.is_not_staff', 0);
        $this->db->order_by('firstname', 'desc');
        $this->db->group_by('tblstaff.staffid');
        $teammember = $this->db->get('tblstaff')->result_array();
        if ($this->input->get('lid')) {
            $this->db->select('tblstaff.staffid, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name, tblstaff.email');
            if ($is_sido_admin == 0 && $is_admin == 0) {
                $this->db->join('tblstaffbrand', 'tblstaffbrand.staffid = tblstaff.staffid');
                $this->db->where('tblstaffbrand.brandid', get_user_session());
            }
            $this->db->join('tblleads', 'tblleads.assigned = tblstaff.staffid', 'left');
            $this->db->where('tblleads.id', $this->input->get('lid'));
            $this->db->where('tblstaff.active', 1);
            $this->db->where('tblstaff.deleted', 0);
            $this->db->order_by('firstname', 'desc');
            $leadteammember = $this->db->get('tblstaff')->result_array();
            $teammember = array_merge($teammember, $leadteammember);
            $teammember = array_map("unserialize", array_unique(array_map("serialize", $teammember)));
        }
        if ($this->input->get('pid')) {
            $this->db->select('tblstaff.staffid, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name, tblstaff.email');
            if ($is_sido_admin == 0 && $is_admin == 0) {
                $this->db->join('tblstaffbrand', 'tblstaffbrand.staffid = tblstaff.staffid');
                $this->db->where('tblstaffbrand.brandid', get_user_session());
            }
            //$this->db->join('tblprojects', 'tblprojects.assigned = tblstaff.staffid','left');
            $this->db->join('tblstaffprojectassignee', 'tblstaffprojectassignee.assigned = tblstaff.staffid', 'left');
            //$this->db->join('tblstaff', 'tblstaff.staffid = tblstaffprojectassignee.assigned', 'left');
            $this->db->where('tblstaffprojectassignee.projectid', $this->input->get('pid'));
            $this->db->where('tblstaff.active', 1);
            $this->db->where('tblstaff.deleted', 0);
            $this->db->order_by('firstname', 'desc');
            $this->db->group_by('tblstaff.staffid');
            $leadteammember = $this->db->get('tblstaff')->result_array();
            $teammember = array_merge($teammember, $leadteammember);
            $teammember = array_map("unserialize", array_unique(array_map("serialize", $teammember)));
        }
        //echo $this->db->last_query();exit;
        //echo "<pre>";print_r($teammember);exit;
        return $teammember;
    }

    /**
     * Added By Purvi on 11-17-2017 For Read Message
     */
    public function readmessage($message_id)
    {
        $data = array();
        $session_data = get_session_data();
        $user_id = $session_data['staff_user_id'];

        $message_thread_data = $this->db->select('id')->from('tblmessages')->where('id=' . $message_id . ' OR parent=' . $message_id)->get()->result_array();
        if (!empty($message_thread_data)) {
            foreach ($message_thread_data as $msg) {
                $data['isread'] = 1;
                $this->db->where('userid', $user_id);
                $this->db->where('usertype', 'teammember');
                $this->db->where('messageid', $msg['id']);
                $this->db->update('tblmessagesallusers', $data);
            }
            return "read";
        } else {
            return false;
        }

    }

    /**
     * Added By Purvi on 11-17-2017 For Read all message
     */
    public function readallmessages()
    {
        $data = array();
        $session_data = get_session_data();
        $user_id = $session_data['staff_user_id'];
        $data['isread'] = 1;
        $this->db->where('userid', $user_id);
        $this->db->where('usertype', 'teammember');
        $this->db->update('tblmessagesallusers', $data);
        return "read";
    }

    /**
     * Added By Purvi on 11-17-2017 For Read all message
     */
    public function readmessages($message_ids)
    {
        $data = array();
        $session_data = get_session_data();
        $user_id = $session_data['staff_user_id'];
        $data['isread'] = 1;
        $this->db->where('userid', $user_id);
        $this->db->where('usertype', 'teammember');
        $this->db->where_in('messageid', $message_ids);
        $this->db->update('tblmessagesallusers', $data);
        return "read";
    }

    /**
     * Added By Purvi on 11-17-2017 For Read all message
     */
    public function readmessagethread($messageid)
    {
        $data = array();
        $session_data = get_session_data();
        $user_id = $session_data['staff_user_id'];

        $this->db->select('tblmessages.id');
        $this->db->where('parent', $messageid);
        $this->db->where('tblmessages.deleted = 0');
        $child_messages = $this->db->get('tblmessages')->result_array();

        $data['isread'] = 1;
        $this->db->where('messageid', $messageid);
        $this->db->where('userid', $user_id);
        $this->db->where('usertype', 'teammember');
        $this->db->update('tblmessagesallusers', $data);

        foreach ($child_messages as $cm) {
            $data['isread'] = 1;
            $this->db->where('messageid', $cm['id']);
            $this->db->where('userid', $user_id);
            $this->db->where('usertype', 'teammember');
            $this->db->update('tblmessagesallusers', $data);
        }
        return "read";
    }


    public function add_attachment_to_message($messageid, $attachment)
    {
        $this->db->insert('tblmessagesattachment', array(
            'messageid' => $messageid,
            'name' => $attachment[0]['file_name']
        ));
        return true;

    }

    /**
     * Add new message
     * @param array $data message data
     * @return boolean
     */
    public function replymessage($data, $id)
    {
        $data["brandid"] = get_user_session();
        $data['created_by'] = $this->session->userdata['staff_user_id'];
        $data['created_by_type'] = "teammember";
        $data['created_date'] = date('Y-m-d H:i:s');
        $teammembermessageto = $contactmessageto = array();
        if (isset($data['message_to']) && !empty($data['message_to'])) {
            foreach ($data['message_to'] as $p) {
                $pdata = explode("_", $p);
                if ($pdata[0] == "tm") {
                    $teammembermessageto[] = $pdata[1];
                } else {
                    $contactmessageto[] = $pdata[1];
                }
            }
            unset($data['message_to']);
        }

        $this->db->select('userid');
        $this->db->where('tblmessagesusers.usertype', 'teammember');
        $this->db->where('tblmessagesusers.messageid', $id);
        $messagesusers = $this->db->get('tblmessagesusers')->result_array();
        $messagesusers = array_column($messagesusers, 'userid');

        $allusers = array_unique(array_merge($teammembermessageto, $messagesusers));

        $data['parent'] = $id;
        $this->db->insert('tblmessages', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {

            if (!empty($teammembermessageto)) {
                foreach ($teammembermessageto as $tm) {
                    $this->db->insert('tblmessagesnotify', array(
                        'messageid' => $insert_id,
                        'usertype' => 'teammember',
                        'userid' => $tm
                    ));
                }
            }

            if (!empty($contactmessageto)) {
                foreach ($contactmessageto as $cm) {
                    $this->db->insert('tblmessagesnotify', array(
                        'messageid' => $insert_id,
                        'usertype' => 'contact',
                        'userid' => $cm
                    ));
                }
            }

            if (!empty($allusers)) {
                foreach ($allusers as $t) {
                    if ($this->session->userdata['staff_user_id'] == $t) {
                        $this->db->insert('tblmessagesallusers', array(
                            'messageid' => $insert_id,
                            'usertype' => 'teammember',
                            'userid' => $t,
                            'isread' => 1
                        ));
                    } else {
                        $this->db->insert('tblmessagesallusers', array(
                            'messageid' => $insert_id,
                            'usertype' => 'teammember',
                            'userid' => $t
                        ));
                    }
                }
                if (!in_array($this->session->userdata['staff_user_id'], $allusers)) {
                    $this->db->insert('tblmessagesallusers', array(
                        'messageid' => $insert_id,
                        'usertype' => 'teammember',
                        'userid' => $this->session->userdata['staff_user_id'],
                        'isread' => 1
                    ));
                }
            }

            logActivity('Message reply Added [ID: ' . $insert_id);

            return $insert_id;
        }

        return false;
    }

    public function getmessagedetailsforemail($id = "")
    {
        $brandid = get_user_session();
        $session_data = get_session_data();
        $this->db->select('tblmessages.id,tblmessages.subject,tblmessages.content, tblmessages.created_by,IF(created_by_type = "teammember",CONCAT(tblstaff.firstname, " ", tblstaff.lastname), IF(created_by_type = "contact", CONCAT(tbladdressbook.firstname, " ", tbladdressbook.lastname), CONCAT(tblstaff.firstname, " ", tblstaff.lastname))) AS created_by_name,tblmessages.created_date,tblmessages.brandid');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblmessages.created_by', 'left');
        $this->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblmessages.created_by', 'left');
        $this->db->where('id', $id);
        $this->db->where('tblmessages.deleted = 0');
        $message = $this->db->get('tblmessages')->row();

        $this->db->select('name');
        $this->db->where('messageid', $id);
        $attachments = $this->db->get('tblmessagesattachment')->result_array();
        $attachments = array_column($attachments, 'name');

        $message_user = $contact_user = array();
        $this->db->select('CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as message_user');
        $this->db->where('messageid', $id);
        $this->db->where('usertype', 'teammember');
        //$this->db->where('userid != '. $message->created_by);
        $this->db->join('tblstaff', 'tblstaff.staffid = tblmessagesusers.userid', 'left');
        $message_user = $this->db->get('tblmessagesusers')->result_array();
        $message_user = array_column($message_user, 'message_user');

        $this->db->select('CONCAT(tbladdressbook.firstname, " ", tbladdressbook.lastname) as contact_user');
        $this->db->where('messageid', $id);
        //$this->db->where('userid != '. $message->created_by);
        $this->db->where('usertype', 'contact');
        $this->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblmessagesusers.userid', 'left');
        $contact_user = $this->db->get('tblmessagesusers')->result_array();
        $contact_user = array_column($contact_user, 'contact_user');

        $message_users = array_merge($message_user, $contact_user);

        $message_user_to = $contact_user_to = array();
        $this->db->select('CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as message_user_to');
        $this->db->where('messageid', $id);
        $this->db->where('usertype', 'teammember');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblmessagesnotify.userid', 'left');
        $message_user_to = $this->db->get('tblmessagesnotify')->result_array();
        $message_user_to = array_column($message_user_to, 'message_user_to');

        $this->db->select('CONCAT(tbladdressbook.firstname, " ", tbladdressbook.lastname) as contact_user_to');
        $this->db->where('messageid', $id);
        $this->db->where('usertype', 'contact');
        $this->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblmessagesnotify.userid', 'left');
        $contact_user_to = $this->db->get('tblmessagesnotify')->result_array();
        $contact_user_to = array_column($contact_user_to, 'contact_user_to');

        $message_users_to = array_merge($message_user_to, $contact_user_to);

        $message->attachments = $attachments;
        $message->users = $message_users_to;
        $message->privacy = $message_users;

        // Get child messages
        $this->db->select('tblmessages.id,tblmessages.subject,tblmessages.content, tblmessages.created_by,IF(created_by_type = "teammember",CONCAT(tblstaff.firstname, " ", tblstaff.lastname), IF(created_by_type = "contact", CONCAT(tbladdressbook.firstname, " ", tbladdressbook.lastname), CONCAT(tblstaff.firstname, " ", tblstaff.lastname))) AS created_by_name,tblmessages.created_date,tblmessages.brandid');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblmessages.created_by', 'left');
        $this->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblmessages.created_by', 'left');

        $this->db->where('parent', $id);
        $this->db->order_by('tblmessages.created_date', 'desc');
        $this->db->where('tblmessages.deleted = 0');
        $child_messages = $this->db->get('tblmessages')->result_array();
        // echo $this->db->last_query();
        // echo "<pre>";
        // print_r($child_messages);exit;
        $child_messages_data = $child_messages_final_data = array();
        if (!empty($child_messages)) {
            foreach ($child_messages as $child_message) {
                $child_messages_data['id'] = $child_message['id'];
                $child_messages_data['content'] = $child_message['content'];
                $child_messages_data['created_by'] = $child_message['created_by'];
                $child_messages_data['created_by_name'] = $child_message['created_by_name'];
                $child_messages_data['created_date'] = $child_message['created_date'];
                $child_messages_data['brandid'] = $child_message['brandid'];

                $this->db->select('name');
                $this->db->where('messageid', $child_message['id']);
                $attachments = $this->db->get('tblmessagesattachment')->result_array();
                $attachments = array_column($attachments, 'name');

                $message_user_to = $contact_user_to = array();
                $this->db->select('CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as message_user_to');
                $this->db->where('messageid', $child_message['id']);
                $this->db->where('usertype', 'teammember');
                $this->db->join('tblstaff', 'tblstaff.staffid = tblmessagesnotify.userid', 'left');
                $message_user_to = $this->db->get('tblmessagesnotify')->result_array();
                $message_user_to = array_column($message_user_to, 'message_user_to');

                $this->db->select('CONCAT(tbladdressbook.firstname, " ", tbladdressbook.lastname) as contact_user_to');
                $this->db->where('messageid', $child_message['id']);
                $this->db->where('usertype', 'contact');
                $this->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblmessagesnotify.userid', 'left');
                $contact_user_to = $this->db->get('tblmessagesnotify')->result_array();
                $contact_user_to = array_column($contact_user_to, 'contact_user_to');

                $message_users_to = array_merge($message_user_to, $contact_user_to);
                $child_messages_data['attachments'] = $attachments;
                $child_messages_data['users'] = $message_users_to;
                $child_messages_final_data[] = $child_messages_data;
            }
            $message->child_message = $child_messages_final_data;
        } else {
            $message->child_message = array();
        }
        //echo "<pre>";print_r($message);exit;
        return $message;
    }

    public function getuseremails($id)
    {
        $brandid = get_user_session();
        $session_data = get_session_data();

        $this->db->select('CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as created_by_name');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblmessages.created_by');
        $this->db->where('id', $id);
        $this->db->where('tblmessages.deleted = 0');
        $message = $this->db->get('tblmessages')->row();

        $message_user_to = $contact_user_to = array();
        $this->db->select('tblstaff.email,CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as message_user_to, usertype,tblmessagesnotify.userid as uid');
        $this->db->where('messageid', $id);
        $this->db->where('tblstaff.deleted = 0');
        $this->db->where('usertype', 'teammember');
        $this->db->join('tblstaff', 'tblstaff.staffid = tblmessagesnotify.userid', 'left');
        $message_user_to = $this->db->get('tblmessagesnotify')->result_array();
        // $message_user_to = array_column($message_user_to, 'email');

        $this->db->select('tbladdressbookemail.email,CONCAT(tbladdressbook.firstname, " ", tbladdressbook.lastname) as contact_user, usertype,tblmessagesnotify.userid as uid');
        $this->db->where('messageid', $id);
        $this->db->where('type', 'primary');
        $this->db->where('tbladdressbook.deleted = 0');
        $this->db->join('tbladdressbookemail', 'tbladdressbookemail.addressbookid = tblmessagesnotify.userid', 'left');
        $this->db->join('tbladdressbook', 'tbladdressbook.addressbookid = tblmessagesnotify.userid', 'left');
        $contact_user_to = $this->db->get('tblmessagesnotify')->result_array();
        //$contact_user_to = array_column($contact_user_to, 'email');

        $message_users_to = array_merge($message_user_to, $contact_user_to);
        $message->usersdetail = $message_users_to;
        //echo "<pre>";print_r($message);exit;
        return $message;
    }

    public function edit_message($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->update('tblmessages', array(
            'content' => $data['content'],
            'updated_by' => $this->session->userdata['staff_user_id'],
            'dateupdated' => date('Y-m-d H:i:s')
        ));
        if ($this->db->affected_rows() > 0) {
            logActivity('Message updated [ID: ' . $data['id'] . ']');
            return true;
        } else {
            return false;
        }
    }

    public function getunreadmessagecount()
    {
        $staffid = $this->session->userdata['staff_user_id'];

        $this->db->select('(SELECT 1 FROM tblmessages m JOIN tblmessagesallusers mu ON (mu.messageid = m.id AND mu.isread = 0 AND mu.userid = ' . $this->session->userdata['staff_user_id'] . ') WHERE m.id = tblmessages.id OR m.parent = tblmessages.id limit 1) as unreadcount');
        $this->db->where('deleted', 0);
        $this->db->where('parent', 0);
        $tblmessagesunread = $this->db->get('tblmessages')->result_array();
        $totcount = 0;
        foreach ($tblmessagesunread as $value) {
            $totcount += $value['unreadcount'];
        }
        return $totcount;
    }

    /**
     * Add new message
     * @param array $data message data
     * @return boolean
     */
    public function replyclientmessage($data)
    {

        $id = $data['message_id'];
        $contact_id = $data['contact_id'];
        unset($data['message_id']);
        unset($data['contact_id']);
        $data['created_by'] = $contact_id;
        $data['created_by_type'] = "contact";
        $data['created_date'] = date('Y-m-d H:i:s');
        $teammembermessageto = $contactmessageto = array();
        if (isset($data['message_to']) && !empty($data['message_to'])) {
            foreach ($data['message_to'] as $p) {
                $pdata = explode("_", $p);
                if ($pdata[0] == "tm") {
                    $teammembermessageto[] = $pdata[1];
                } else {
                    $contactmessageto[] = $pdata[1];
                }
            }
            unset($data['message_to']);
        }

        $this->db->select('userid');
        $this->db->where('tblmessagesusers.usertype', 'teammember');
        $this->db->where('tblmessagesusers.messageid', $id);
        $messagesusers = $this->db->get('tblmessagesusers')->result_array();
        $messagesusers = array_column($messagesusers, 'userid');

        $allusers = array_unique(array_merge($teammembermessageto, $messagesusers));

        unset($data['hdnlid']);
        unset($data['hdnpid']);
        unset($data['hdneid']);

        $data['parent'] = $id;
        $this->db->insert('tblmessages', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if (!empty($teammembermessageto)) {
                foreach ($teammembermessageto as $tm) {
                    $this->db->insert('tblmessagesnotify', array(
                        'messageid' => $insert_id,
                        'usertype' => 'teammember',
                        'userid' => $tm
                    ));
                }
            }

            if (!empty($contactmessageto)) {
                foreach ($contactmessageto as $cm) {
                    $this->db->insert('tblmessagesnotify', array(
                        'messageid' => $insert_id,
                        'usertype' => 'contact',
                        'userid' => $cm
                    ));
                }
            }

            if (!empty($allusers)) {
                foreach ($allusers as $t) {
                    $this->db->insert('tblmessagesallusers', array(
                        'messageid' => $insert_id,
                        'usertype' => 'teammember',
                        'userid' => $t
                    ));
                }
            }

            logActivity('Message reply Added [ID: ' . $insert_id);

            return $insert_id;
        }

        return false;
    }

    /* Added by Purvi on 11-17-2017 for get contacts for send messages */
    public function getclientcontacts($message_id = "", $brandid = "")
    {
        $messagesusers = array();
        if ($message_id > 0) {
            $this->db->where('tblmessagesusers.usertype', 'contact');
            $this->db->where('tblmessagesusers.messageid', $message_id);
            $messagesusers = $this->db->get('tblmessagesusers')->result_array();
        }
        $this->db->select('tbladdressbook.addressbookid, CONCAT(tbladdressbook.firstname, " ", tbladdressbook.lastname) as contact_name, tbladdressbookemail.email');

        $this->db->join('tbladdressbook_client', 'tbladdressbook_client.addressbookid = tbladdressbook.addressbookid');
        $this->db->where('tbladdressbook_client.brandid', $brandid);

        if (!empty($messagesusers)) {
            $this->db->join('tblmessagesusers', 'tblmessagesusers.userid = tbladdressbook.addressbookid');
            $this->db->where('tblmessagesusers.usertype', 'contact');
            $this->db->where('tblmessagesusers.messageid', $message_id);
        }

        $this->db->join('tbladdressbookemail', 'tbladdressbookemail.addressbookid=tbladdressbook.addressbookid', 'left');
        if ($this->input->get('lid')) {
            $this->db->join('tblleadcontact', 'tblleadcontact.contactid=tbladdressbook.addressbookid');
            $this->db->where('tblleadcontact.leadid', $this->input->get('lid'));
        }
        $this->db->where('tbladdressbook.deleted', 0);
        $this->db->where('tbladdressbook_client.deleted', 0);
        $this->db->where('tbladdressbookemail.type', 'primary');
        $this->db->where('tbladdressbookemail.type != ""');

        $addressbook = $this->db->get('tbladdressbook')->result_array();
        //echo $this->db->last_query();exit;
        return $addressbook;
    }

    /* Added by Purvi on 11-17-2017 for get team members for send messages */
    public function getclientteammember($message_id = "", $brandid = "")
    {
        $messagesusers = array();
        if ($message_id > 0) {
            $this->db->where('tblmessagesusers.usertype', 'teammember');
            $this->db->where('tblmessagesusers.messageid', $message_id);
            $messagesusers = $this->db->get('tblmessagesusers')->result_array();
        }

        $this->db->select('tblstaff.staffid, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name, tblstaff.email');
        $this->db->join('tblstaffbrand', 'tblstaffbrand.staffid = tblstaff.staffid');
        $this->db->where('tblstaffbrand.brandid', $brandid);

        if ($this->input->get('lid')) {
            $this->db->where('tblstaff.user_type', 1, 'OR');
        }

        if (!empty($messagesusers)) {
            $this->db->join('tblmessagesusers', 'tblmessagesusers.userid = tblstaff.staffid');
            $this->db->where('tblmessagesusers.usertype', 'teammember');
            $this->db->where('tblmessagesusers.messageid', $message_id);
        }

        $this->db->where('tblstaff.active', 1);
        $this->db->where('tblstaff.deleted', 0);
        $this->db->order_by('firstname', 'desc');
        $teammember = $this->db->get('tblstaff')->result_array();

        if ($this->input->get('lid')) {
            $this->db->select('tblstaff.staffid, CONCAT(tblstaff.firstname, " ", tblstaff.lastname) as staff_name, tblstaff.email');
            $this->db->join('tblstaffbrand', 'tblstaffbrand.staffid = tblstaff.staffid');
            $this->db->where('tblstaffbrand.brandid', $brandid);


            $this->db->join('tblleads', 'tblleads.assigned = tblstaff.staffid', 'left');
            $this->db->where('tblleads.id', $this->input->get('lid'));

            $this->db->where('tblstaff.active', 1);
            $this->db->where('tblstaff.deleted', 0);
            $this->db->order_by('firstname', 'desc');
            $leadteammember = $this->db->get('tblstaff')->result_array();
            $teammember = array_merge($teammember, $leadteammember);
            $teammember = array_map("unserialize", array_unique(array_map("serialize", $teammember)));
        }

        return $teammember;
    }

    /**
     * Added By: Vaidehi
     * Dt: 02/28/2018
     * For Pin/Unpin Message
     */
    public function pinmessage($message_id)
    {
        $session_data = get_session_data();
        $user_id = $session_data['staff_user_id'];

        $pinexist = $this->db->select('pinid')->from('tblpins')->where('pintype = "Message" AND pintypeid = ' . $message_id . ' AND userid = ' . $user_id)->get()->row();
        if (!empty($pinexist)) {
            $this->db->where('userid', $user_id);
            $this->db->where('pintypeid', $message_id);
            $this->db->where('pintype', "Message");
            $this->db->delete('tblpins');

            return 0;
        } else {
            $this->db->insert('tblpins', array(
                'pintype' => "Message",
                'pintypeid' => $message_id,
                'userid' => $user_id
            ));

            return 1;
        }
    }

    function get_message_status()
    {
        return $this->get();
    }

    /**
     * Added By : Masud
     * Dt : 27/05/2018
     * to save extra form fields in db
     */

    public function message_new_created_notification($message_id, $assigned, $integration = false)
    {
        $name = $this->db->select('subject as name')->from('tblmessages')->where('id', $message_id)->get()->row()->name;
        if ($assigned == "") {
            $assigned = 0;
        }

        $notification_data = array(
            'description' => ($integration == false) ? 'not_new_message_created' : 'not_new_message_created',
            'touserid' => $assigned,
            'eid' => $message_id,
            'brandid' => get_user_session(),
            'not_type' => 'messages',
            'link' => 'messages/message/' . $message_id,
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
    }

}