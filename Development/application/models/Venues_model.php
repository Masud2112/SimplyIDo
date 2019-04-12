<?php

/**
 * Added By: Vaidehi
 * Dt: 02/13/2018
 * Venue Module
 */
defined('BASEPATH') or exit('No direct script access allowed');

class Venues_model extends CRM_Model
{
    /**
     * Get venue
     * @param  mixed $id Optional - venue id
     * @param  integer $active Optional get all active or inactive
     * @return mixed if id is passed return object else array
     */
    public function get($id = '', $where = array())
    {
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];

        if ($is_sido_admin == 0 && $is_admin == 0) {
            $brandid = get_user_session();

            $this->db->join('tblbrandvenue', 'tblbrandvenue.venueid = tblvenue.venueid', 'left');
            $this->db->where('tblbrandvenue.deleted ', 0);
            $this->db->where('tblbrandvenue.brandid', $brandid);
        }

        $this->db->where('tblvenue.deleted', 0);
        if (is_numeric($id)) {
            $this->db->where('tblvenue.venueid', $id);
            $venue = $this->db->get('tblvenue')->row();

            //get venue images
            $venue->venueimages = $this->get_venue_attachments($id, 'venueimages');

            //get venue sitelocation images
            $venue->sitelocationimages = $this->get_venue_attachments($id, 'sitelocationimages');

            //get venue files
            $venue->venuefiles = $this->get_venue_attachments($id, 'venuefiles');
            $venue->venueattachments = $this->get_venue_attachments($id, 'venueattachment');
            //get venue sitelocation files
            $venue->sitelocationfiles = $this->get_venue_attachments($id, 'sitelocationfiles');

            //get site location for venue
            $this->db->select('tblvenue_sitecontact.*');
            $this->db->where('tblvenue_sitecontact.deleted = 0');
            $this->db->where('tblvenue_sitecontact.venueid', $id);
            $venue->sitelocations = $this->db->get('tblvenue_sitecontact')->result_array();

            //get venue links
            $this->db->select('tblvenuelink.*,tblsocials.name');
            $this->db->where('tblvenuelink.deleted = 0');
            $this->db->join('tblsocials', 'tblsocials.socialid = tblvenuelink.venuelinktype');
            $this->db->where('tblvenuelink.venueid', $id);
            $venue->venuelinks = $this->db->get('tblvenuelink')->result_array();
            //get venue contacts
            $this->db->select('tblvenuecontact.*');
            $this->db->where('tblvenuecontact.deleted = 0');
            $this->db->where('tblvenuecontact.rel_id', $id);
            $this->db->where('tblvenuecontact.rel_type', 'venue');
            $venue->venuecontacts = $this->db->get('tblvenuecontact')->result_array();

            return $venue;
        }

        $this->db->order_by('venuename', 'desc');

        return $this->db->get('tblvenue')->result_array();
    }

    //for adding venue
    public function add($data)
    {

        $this->load->model('emails_model');
        unset($data['imagebase64']);
        $userid = get_staff_user_id();
        $staff = $this->staff_model->get($userid);
        $venueaddress = isset($data['venueaddress']) ? $data['venueaddress'] : "";
        $data['venueaddress'] = $venueaddress['street_number'];
        $data['venueaddress2'] = $venueaddress['route'];
        $data['venuecity'] = $venueaddress['locality'];
        $data['venuestate'] = $venueaddress['administrative_area_level_1'];
        $data['venuezip'] = $venueaddress['postal_code'];
        $data['venuecountry'] = $venueaddress['country'];
        $venue = $this->check_venue_exists($data);

        if (empty($venue)) {
            $brandid = !empty(get_user_session()) ? get_user_session() : 0;

            $session_data = get_session_data();
            $is_sido_admin = $session_data['is_sido_admin'];

            if (isset($data['rel_type']) && $data['rel_type'] != "") {
                $rel_type = $data['rel_type'];
                $rel_id = $data[$data['rel_type']];
            } else {
                $rel_type = "";
                $rel_id = "";
            }

            //$venuelink 					= isset($data['venuelink'])?$data['venuelink']:"";
            $sitelocation_name = isset($data['sitelocation_name']) ? $data['sitelocation_name'] : "";
            $sitelocation_link = isset($data['sitelocation_link']) ? $data['sitelocation_link'] : "";
            $sitelocation_contactname = isset($data['sitelocation_contactname']) ? $data['sitelocation_contactname'] : "";
            $sitelocation_contactphone = isset($data['sitelocation_contactphone']) ? $data['sitelocation_contactphone'] : "";
            $sitelocation_contactemail = isset($data['sitelocation_contactemail']) ? $data['sitelocation_contactemail'] : "";
            $venuecontactname = isset($data['venuecontactname']) ? $data['venuecontactname'] : "";
            $venuecontactphone = isset($data['venuecontactphone']) ? $data['venuecontactphone'] : "";
            $venuecontactemail = isset($data['venuecontactemail']) ? $data['venuecontactemail'] : "";

            $venueemails = isset($data['email']) ? $data['email'] : "";
            $venuephones = isset($data['phone']) ? $data['phone'] : "";
            $venuelinks = isset($data['website']) ? $data['website'] : "";
            $data['venueemail'] = isset($data['email']) ? serialize($data['email']) : "";
            $data['venuephone'] = isset($data['phone']) ? serialize($data['phone']) : "";

            unset($data['lead']);
            unset($data['project']);
            unset($data['event']);
            unset($data['hdnlid']);
            unset($data['hdnpid']);
            unset($data['hdneid']);
            unset($data['rel_type']);
            unset($data['rel_id']);
            unset($data['venuelink']);
            unset($data['sitelocation_name']);
            unset($data['sitelocation_link']);
            unset($data['sitelocation_contactname']);
            unset($data['sitelocation_contactphone']);
            unset($data['sitelocation_contactemail']);
            unset($data['venuecontactname']);
            unset($data['venuecontactphone']);
            unset($data['venuecontactemail']);

            unset($data['email']);
            unset($data['website']);
            unset($data['phone']);
            unset($data['favourite']);
            $data['created_by'] = get_staff_user_id();
            $data['datecreated'] = date('Y-m-d H:i:s');

            //if venue created by admin, make it global
            if ($is_sido_admin == 1) {
                $data['isapproved'] = 1;
            } else {
                $data['isapproved'] = 0;
            }
            if (isset($data['venuetags'])) {
                $data['venuetags'] = implode(',', $data['venuetags']);
            }
            $this->db->insert('tblvenue', $data);
            $insert_id = $this->db->insert_id();

            if ($insert_id) {
                if ($is_sido_admin == 1) {
                    $this->add_brand_venue($insert_id);

                    $this->new_venue_notification($insert_id);
                } else {
                    $venuebrand = [];
                    $venuebrand['brandid'] = get_user_session();
                    $venuebrand['venueid'] = $insert_id;
                    $venuebrand['deleted'] = 0;

                    $this->db->insert('tblbrandvenue', $venuebrand);
                }
                //venue links
                foreach ($venuelinks as $link) {
                    if (!empty($link)) {
                        $venuelinkdata = [];

                        $venuelinkdata['venueid'] = $insert_id;
                        $venuelinkdata['venuelinktype'] = $link['type'];
                        $venuelinkdata['venuelink'] = $link['url'];
                        $venuelinkdata['deleted'] = 0;
                        $venuelinkdata['created_by'] = $this->session->userdata['staff_user_id'];
                        $venuelinkdata['datecreated'] = date('Y-m-d H:i:s');

                        $this->db->insert('tblvenuelink', $venuelinkdata);
                    }
                }

                //venue site locations
                /*foreach ($sitelocation_name as $key => $sitelocation) {
                    if(!empty($sitelocation)) {
                        $sitelocationdata = [];

                        $sitelocationdata['venueid']						= $insert_id;
                        $sitelocationdata['sitelocation_name']				= $sitelocation;
                        $sitelocationdata['sitelocation_link']				= $sitelocation_link[$key];
                        $sitelocationdata['sitelocation_contactname']		= $sitelocation_contactname[$key];
                        $sitelocationdata['sitelocation_contactphone']		= $sitelocation_contactphone[$key];
                        $sitelocationdata['sitelocation_contactemail']		= $sitelocation_contactemail[$key];
                        $sitelocationdata['deleted']						= 0;
                        $sitelocationdata['created_by']						= $this->session->userdata['staff_user_id'];
                        $sitelocationdata['datecreated']					= date('Y-m-d H:i:s');

                        $this->db->insert('tblvenue_sitecontact', $sitelocationdata);
                    }
                }*/

                //venue contact
                /*foreach ($venuecontactname as $key => $venuecontact) {
                    if(!empty($venuecontact)) {
                        $venuecontactdata = [];

                        $venuecontactdata['venueid']				= $insert_id;
                        $venuecontactdata['venuecontactname']		= $venuecontact;
                        $venuecontactdata['venuecontactphone']		= $venuecontactphone[$key];
                        $venuecontactdata['venuecontactemail']		= $venuecontactemail[$key];
                        $venuecontactdata['deleted']				= 0;
                        $venuecontactdata['created_by']				= $this->session->userdata['staff_user_id'];
                        $venuecontactdata['datecreated']			= date('Y-m-d H:i:s');

                        $this->db->insert('tblvenuecontact', $venuecontactdata);
                    }
                }*/

                //Lead Venue
                if (isset($rel_type) && $rel_type == 'lead') {
                    $leadcontact = array();
                    $leadcontact['leadid'] = $rel_id;
                    $leadcontact['venueid'] = $insert_id;
                    $leadcontact['brandid'] = $brandid;

                    $this->db->where('leadid', $rel_id);
                    $this->db->where('venueid', $insert_id);
                    $this->db->where('brandid', $brandid);
                    $leadcontacts = $this->db->get('tblleadvenue')->row();

                    if (count($leadcontacts) <= 0) {
                        $this->db->insert('tblleadvenue', $leadcontact);
                    }
                }

                //Project Venue
                if (isset($rel_type) && $rel_type == 'project') {
                    $projectcontact = array();
                    $projectcontact['projectid'] = $rel_id;
                    $projectcontact['venueid'] = $insert_id;
                    $projectcontact['brandid'] = $brandid;

                    $this->db->where('projectid', $rel_id);
                    $this->db->where('venueid', $insert_id);
                    $this->db->where('brandid', $brandid);
                    $projectcontacts = $this->db->get('tblprojectvenue')->row();

                    if (count($projectcontacts) <= 0) {
                        $this->db->insert('tblprojectvenue', $projectcontact);
                    }
                }

                //Sub Project Venue
                if (isset($rel_type) && $rel_type == 'event') {
                    $projectcontact = array();
                    $projectcontact['eventid'] = $rel_id;
                    $projectcontact['venueid'] = $insert_id;
                    $projectcontact['brandid'] = $brandid;

                    $this->db->where('eventid', $rel_id);
                    $this->db->where('venueid', $insert_id);
                    $this->db->where('brandid', $brandid);
                    $projectcontacts = $this->db->get('tblprojectvenue')->row();

                    if (count($projectcontacts) <= 0) {
                        $this->db->insert('tblprojectvenue', $projectcontact);
                    }
                }

                logActivity('Venue added [ID: ' . $insert_id . '.' . $data['venuename'] . ']');

                //if account owner create venue, send email to SiDO admin
                if ($is_sido_admin == 1) {
                    $get_admins = $this->db->query('SELECT `staffid`, `email`, `firstname`, `lastname` FROM `tblstaff` WHERE `is_sido_admin` = 1');
                    $admins = $get_admins->result_array();
                    foreach ($admins as $admin) {
                        $merge_fields = array();

                        $staffdetails['{name}'] = $admin['firstname'];
                        $staffdetails['{staff_name}'] = $staff->firstname;
                        $merge_fields = array_merge($merge_fields, $staffdetails);

                        $merge_fields = array_merge($merge_fields, get_venue_merge_field($insert_id));

                        $send = $this->emails_model->send_email_template('new-venue-added', $admin['email'], $merge_fields);
                    }
                } else {
                    //else send email to Venue Owner
                    if (!empty($data['venueemail'])) {
                        $merge_fields = array();
                        $staffdetails['{staff_name}'] = $staff->firstname;
                        $merge_fields = array_merge($merge_fields, $staffdetails);

                        $merge_fields = array_merge($merge_fields, get_venue_merge_field($insert_id));

                        $send = $this->emails_model->send_email_template('venue-added', $data['venueemail'], $merge_fields);
                    }
                }

                return $insert_id;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //for updating venue
    public function update($data, $id)
    {
        $this->load->model('emails_model');
        unset($data['imagebase64']);
        $userid = get_staff_user_id();
        $staff = $this->staff_model->get($userid);
        $venueaddress = isset($data['venueaddress']) ? $data['venueaddress'] : "";
        $data['venueaddress'] = $venueaddress['street_number'];
        $data['venueaddress2'] = $venueaddress['route'];
        $data['venuecity'] = $venueaddress['locality'];
        $data['venuestate'] = $venueaddress['administrative_area_level_1'];
        $data['venuezip'] = $venueaddress['postal_code'];
        $data['venuecountry'] = $venueaddress['country'];
        $venue = $this->check_venue_exists($data, $id);

        if (empty($venue)) {
            $brandid = !empty(get_user_session()) ? get_user_session() : 0;

            $session_data = get_session_data();
            $is_sido_admin = $session_data['is_sido_admin'];

            if (isset($data['rel_type']) && $data['rel_type'] != "") {
                $rel_type = $data['rel_type'];
                $rel_id = $data[$data['rel_type']];
            } else {
                $rel_type = "";
                $rel_id = "";
            }

            $venuelink = $data['venuelink'];

            if (isset($data['venuelinkid'])) {
                $venuelinkid = $data['venuelinkid'];
            }

            if (isset($data['venue_sitecontactid'])) {
                $venue_sitecontactid = $data['venue_sitecontactid'];
            }

            $venuelink = isset($data['venuelink']) ? $data['venuelink'] : "";
            $sitelocation_name = isset($data['sitelocation_name']) ? $data['sitelocation_name'] : "";
            $sitelocation_link = isset($data['sitelocation_link']) ? $data['sitelocation_link'] : "";
            $sitelocation_contactname = isset($data['sitelocation_contactname']) ? $data['sitelocation_contactname'] : "";
            $sitelocation_contactphone = isset($data['sitelocation_contactphone']) ? $data['sitelocation_contactphone'] : "";
            $sitelocation_contactemail = isset($data['sitelocation_contactemail']) ? $data['sitelocation_contactemail'] : "";
            $venuecontactname = isset($data['venuecontactname']) ? $data['venuecontactname'] : "";
            $venuecontactphone = isset($data['venuecontactphone']) ? $data['venuecontactphone'] : "";
            $venuecontactemail = isset($data['venuecontactemail']) ? $data['venuecontactemail'] : "";
            $venuelinks = isset($data['website']) ? $data['website'] : "";

            $data['venueemail'] = isset($data['email']) ? serialize($data['email']) : "";
            $data['venuephone'] = isset($data['phone']) ? serialize($data['phone']) : "";

            $data['ispublic'] = isset($data['ispublic']) ? $data['ispublic'] : 0;

            unset($data['lead']);
            unset($data['project']);
            unset($data['event']);
            unset($data['hdnlid']);
            unset($data['hdnpid']);
            unset($data['hdneid']);
            unset($data['rel_type']);
            unset($data['rel_id']);
            unset($data['venuelink']);
            unset($data['sitelocation_name']);
            unset($data['sitelocation_link']);
            unset($data['sitelocation_contactname']);
            unset($data['sitelocation_contactphone']);
            unset($data['sitelocation_contactemail']);
            unset($data['venuelinkid']);
            unset($data['venue_sitecontactid']);
            unset($data['venuecontactid']);
            unset($data['venuecontactname']);
            unset($data['venuecontactphone']);
            unset($data['venuecontactemail']);

            unset($data['email']);
            unset($data['website']);
            unset($data['phone']);
            unset($data['favourite']);
            unset($data['venueid']);

            $data['updated_by'] = get_staff_user_id();
            $data['dateupdated'] = date('Y-m-d H:i:s');
            if (isset($data['venuetags'])) {
                $data['venuetags'] = implode(',', $data['venuetags']);
            }
            //if venue created by admin, make it global
            $data['isapproved'] = 1;

            $this->db->where('venueid', $id);
            $this->db->update('tblvenue', $data);

            if ($id) {
                $this->add_brand_venue($id);

                $this->new_venue_notification($id);

                $venuelinkdata = [];
                $venuelinkdata['deleted'] = 1;
                $venuelinkdata['updated_by'] = $this->session->userdata['staff_user_id'];
                $venuelinkdata['dateupdated'] = date('Y-m-d H:i:s');

                $this->db->where('venueid', $id);
                $this->db->update('tblvenuelink', $venuelinkdata);

                //venue links
                foreach ($venuelinks as $key => $link) {
                    if (!empty($link)) {
                        $venuelinkdata = [];

                        $venuelinkdata['venueid'] = $id;
                        $venuelinkdata['venuelinktype'] = $link['type'];
                        $venuelinkdata['venuelink'] = $link['url'];
                        $venuelinkdata['deleted'] = 0;
                        $venuelinkdata['created_by'] = $this->session->userdata['staff_user_id'];
                        $venuelinkdata['datecreated'] = date('Y-m-d H:i:s');

                        $this->db->insert('tblvenuelink', $venuelinkdata);
                    }
                }

                $sitelocationdata = [];
                $sitelocationdata['deleted'] = 1;
                $sitelocationdata['updated_by'] = $this->session->userdata['staff_user_id'];
                $sitelocationdata['dateupdated'] = date('Y-m-d H:i:s');

                $this->db->where('venueid', $id);
                $this->db->update('tblvenue_sitecontact', $sitelocationdata);

                //venue site locations
                /*foreach ($sitelocation_name as $key => $sitelocation) {
                    if(!empty($sitelocation)) {
                        if(isset($venue_sitecontactid[$key])) {
                            $sitelocationdata = [];

                            $sitelocationdata['venueid']						= $id;
                            $sitelocationdata['sitelocation_name']				= $sitelocation;
                            $sitelocationdata['sitelocation_link']				= $sitelocation_link[$key];
                            $sitelocationdata['sitelocation_contactname']		= $sitelocation_contactname[$key];
                            $sitelocationdata['sitelocation_contactphone']		= $sitelocation_contactphone[$key];
                            $sitelocationdata['sitelocation_contactemail']		= $sitelocation_contactemail[$key];
                            $sitelocationdata['deleted']						= 0;
                            $sitelocationdata['updated_by']						= $this->session->userdata['staff_user_id'];
                            $sitelocationdata['dateupdated']					= date('Y-m-d H:i:s');

                            $this->db->where('venue_sitecontactid', $venue_sitecontactid[$key]);
                            $this->db->update('tblvenue_sitecontact', $sitelocationdata);
                        } else {
                            $sitelocationdata = [];

                            $sitelocationdata['venueid']						= $id;
                            $sitelocationdata['sitelocation_name']				= $sitelocation;
                            $sitelocationdata['sitelocation_link']				= $sitelocation_link[$key];
                            $sitelocationdata['sitelocation_contactname']		= $sitelocation_contactname[$key];
                            $sitelocationdata['sitelocation_contactphone']		= $sitelocation_contactphone[$key];
                            $sitelocationdata['sitelocation_contactemail']		= $sitelocation_contactemail[$key];
                            $sitelocationdata['deleted']						= 0;
                            $sitelocationdata['created_by']						= $this->session->userdata['staff_user_id'];
                            $sitelocationdata['datecreated']					= date('Y-m-d H:i:s');

                            $this->db->insert('tblvenue_sitecontact', $sitelocationdata);
                        }
                    }
                }*/

                /*$venuecontactdata = [];
                $venuecontactdata['deleted'] = 1;
                $venuecontactdata['updated_by'] = $this->session->userdata['staff_user_id'];
                $venuecontactdata['dateupdated'] = date('Y-m-d H:i:s');

                $this->db->where('venueid', $id);
                $this->db->update('tblvenuecontact', $venuecontactdata);*/

                //venue contact
                /*foreach ($venuecontactname as $key => $venuecontact) {
                    if(!empty($venuecontact)) {
                        if(isset($venuecontactid[$key])) {
                            $venuecontactdata = [];

                            $venuecontactdata['venueid']				= $id;
                            $venuecontactdata['venuecontactname']		= $venuecontactname[$key];
                            $venuecontactdata['venuecontactphone']		= $venuecontactphone[$key];
                            $venuecontactdata['venuecontactemail']		= $venuecontactemail[$key];
                            $venuecontactdata['deleted']				= 0;
                            $venuecontactdata['updated_by']				= $this->session->userdata['staff_user_id'];
                            $venuecontactdata['dateupdated']			= date('Y-m-d H:i:s');

                            $this->db->where('venuecontactid', $venuecontactid[$key]);
                            $this->db->update('tblvenuecontact', $venuecontactdata);
                        } else {
                            $venuecontactdata = [];

                            $venuecontactdata['venueid']				= $id;
                            $venuecontactdata['venuecontactname']		= $venuecontactname[$key];
                            $venuecontactdata['venuecontactphone']		= $venuecontactphone[$key];
                            $venuecontactdata['venuecontactemail']		= $venuecontactemail[$key];
                            $venuecontactdata['deleted']				= 0;
                            $venuecontactdata['created_by']				= $this->session->userdata['staff_user_id'];
                            $venuecontactdata['datecreated']			= date('Y-m-d H:i:s');

                            $this->db->insert('tblvenuecontact', $venuecontactdata);
                        }
                    }
                }*/

                //Lead Venue
                if (isset($rel_type) && $rel_type == 'lead') {
                    $leadcontact = array();
                    $leadcontact['leadid'] = $rel_id;
                    $leadcontact['venueid'] = $id;
                    $leadcontact['brandid'] = $brandid;

                    $this->db->where('leadid', $rel_id);
                    $this->db->where('venueid', $insert_id);
                    $this->db->where('brandid', $brandid);
                    $leadcontacts = $this->db->get('tblleadvenue')->row();

                    if (count($leadcontacts) <= 0) {
                        $this->db->insert('tblleadvenue', $leadcontact);
                    }
                }

                //Project Venue
                if (isset($rel_type) && $rel_type == 'project') {
                    $projectcontact = array();
                    $projectcontact['projectid'] = $rel_id;
                    $projectcontact['venueid'] = $id;
                    $projectcontact['brandid'] = $brandid;

                    $this->db->where('projectid', $rel_id);
                    $this->db->where('venueid', $insert_id);
                    $this->db->where('brandid', $brandid);
                    $projectcontacts = $this->db->get('tblprojectvenue')->row();

                    if (count($projectcontacts) <= 0) {
                        $this->db->insert('tblprojectvenue', $projectcontact);
                    }
                }

                //Sub Project Venue
                if (isset($rel_type) && $rel_type == 'event') {
                    $projectcontact = array();
                    $projectcontact['eventid'] = $rel_id;
                    $projectcontact['venueid'] = $id;
                    $projectcontact['brandid'] = $brandid;

                    $this->db->where('eventid', $rel_id);
                    $this->db->where('venueid', $insert_id);
                    $this->db->where('brandid', $brandid);
                    $projectcontacts = $this->db->get('tblprojectvenue')->row();

                    if (count($projectcontacts) <= 0) {
                        $this->db->insert('tblprojectvenue', $projectcontact);
                    }
                }

                logActivity('Venue edited [ID: ' . $id . '.' . $data['venuename'] . ']');

                //send email to Venue Owner
                if (!empty($data['venueemail'])) {
                    $merge_fields = array();

                    $staffdetails['{staff_name}'] = $staff->firstname;
                    $merge_fields = array_merge($merge_fields, $staffdetails);

                    $merge_fields = array_merge($merge_fields, get_venue_merge_field($id));

                    $send = $this->emails_model->send_email_template('venue-added', $data['venueemail'], $merge_fields);
                }

                return $id;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //to check venue name, venue email , venue phone and/or venue address exists or not
    function check_venue_exists($data, $id = '')
    {
        $where = ' ';

        if (!empty($data['venueemail'])) {
            $where .= 'AND venueemail = "' . $data['venueemail'] . '"';
        }

        if (!empty($data['venuephone'])) {
            $where .= 'AND venuephone = "' . $data['venuephone'] . '"';
        }

        if (!empty($data['venueaddress'])) {
            $where .= 'AND venueaddress = "' . $data['venueaddress'] . '"';
        }

        if (!empty($data['venueaddress2'])) {
            $where .= 'AND venueaddress2 = "' . $data['venueaddress2'] . '"';
        }

        if (!empty($data['venuecity'])) {
            $where .= 'AND venuecity = "' . $data['venuecity'] . '"';
        }

        if (!empty($data['venuestate'])) {
            $where .= 'AND venuestate = "' . $data['venuestate'] . '"';
        }

        if (!empty($data['venuezip'])) {
            $where .= 'AND venuezip = "' . $data['venuezip'] . '"';
        }

        if (isset($id) && $id > 0) {
            $where .= ' AND venueid != ' . $id;
        }

        $query = 'SELECT * FROM tblvenue WHERE (venuename = "' . $data['venuename'] . '"  ' . $where . ') AND deleted = 0';

        $venue = $this->db->query($query);

        if ($venue->num_rows() > 0) {
            return $venue;
        }
    }

    //for new venue notification
    public function new_venue_notification($venueid, $integration = false, $action = "created")
    {
        $result = $this->db->select("venuename,ispublic")->from('tblvenue')->where('venueid', $venueid)->get()->row();
        $name = $result->venuename;
        $ispublic = $result->ispublic;
        $notification_data = array(
            'description' => ($action == 'created') ? 'not_new_venue_is_created' : 'not_venue_is_updated',
            'brandid' => get_user_session(),
            'touserid' => 0,
            'eid' => $venueid,
            'ispublic' => $ispublic,
            'not_type' => 'Venue',
            'link' => admin_url('venues/venue/' . $venueid),
            'additional_data' => ($integration == false ? serialize(array($name)) : serialize(array()))
        );

        if (add_notification($notification_data)) {
            pusher_trigger_notification(array(get_staff_user_id()));
        }
    }

    //to add venue for each brand
    function add_brand_venue($venueid)
    {
        $this->db->where('deleted', 0);
        $get_brand = $this->db->get('tblbrand')->result_array();

        foreach ($get_brand as $brand) {

            $venue_exists = $this->check_brand_venue_exists($brand['brandid'], $venueid);

            if (empty($venue_exists->brandvenueid)) {
                $venuebrand = [];
                $venuebrand['brandid'] = $brand['brandid'];
                $venuebrand['venueid'] = $venueid;
                $venuebrand['deleted'] = 0;

                $this->db->insert('tblbrandvenue', $venuebrand);
            }
        }
    }

    //to check if venue for brand exists or not
    function check_brand_venue_exists($brandid, $venueid)
    {
        $this->db->where('brandid', $brandid);
        $this->db->where('venueid', $venueid);
        $this->db->where('deleted', 0);
        return $this->db->get('tblbrandvenue')->row();
    }

    public function gettype($id, $lid = "", $pid = "", $eid = "")
    {
        $brandid = get_user_session();
        $data = array();

        if ($lid > 0) {
            $this->db->select('leadid');
            $this->db->where('venueid', $id);
            $this->db->where('brandid', $brandid);
            $leadcontact = $this->db->get('tblleadvenue')->row();
            $data['rel_id'] = $projectcontact->leadid;
            $data['rel_type'] = "lead";
        } elseif ($pid > 0 || $eid > 0) {
            $this->db->select('projectid,eventid');
            $this->db->where('venueid', $id);
            $this->db->where('brandid', $brandid);
            $projectcontact = $this->db->get('tblprojectvenue')->row();

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
     * Get all venue attachments
     * @param  mixed $venueid venueid
     * @return array
     */
    public function get_venue_attachments($venueid, $type)
    {
        $this->db->select(implode(', ', prefixed_table_fields_array('tblfiles')));
        $this->db->where('rel_id', $venueid);
        $this->db->where('rel_type', $type);
        $this->db->order_by('dateadded', 'desc');

        return $this->db->get('tblfiles')->result_array();
    }

    /**
     * Remove venue attachment from server and database
     * @param  mixed $id attachmentid
     * @return boolean
     */
    public function remove_venue_attachment($id, $location = "")
    {
        $deleted = false;
        // Get the attachment
        $this->db->where('id', $id);
        $attachment = $this->db->get('tblfiles')->row();

        if ($attachment) {
            if (empty($attachment->external)) {
                $relPath = get_upload_path_by_type('venue_attachments') . $attachment->rel_id . '/';
                if ($location == 'location') {
                    $relPath = get_upload_path_by_type('venue_attachments') . 'locations/' . $attachment->rel_id . '/';
                }
                $fullPath = $relPath . $attachment->file_name;
                unlink($fullPath);
                $fname = pathinfo($fullPath, PATHINFO_FILENAME);
                $fext = pathinfo($fullPath, PATHINFO_EXTENSION);
                $thumbPath = $relPath . $fname . '_thumb.' . $fext;
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }

            $this->db->where('id', $attachment->id);
            $this->db->delete('tblfiles');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                logActivity('Venue Attachment Deleted [VenueID: ' . $attachment->rel_id . ']');
            }
            $dir = get_upload_path_by_type('venue_attachments') . $attachment->rel_id;
            if ($location == 'location') {
                $dir = get_upload_path_by_type('venue_attachments') . 'locations/' . $attachment->rel_id;
            }
            if (is_dir($dir)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files($dir);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir($dir);
                }
            }
        }

        if ($deleted) {
            $this->db->where('file_id', $id);
            $comment_attachment = $this->db->get('tblstafftaskcomments')->row();

            if ($comment_attachment) {
                $this->remove_comment($comment_attachment->id);
            }
        }

        return $deleted;
    }

    /**
     * to mark venue as favorite
     */
    public function favorite($venue_id)
    {
        $user_id = get_staff_user_id();

        $exist = $this->db->select('favoriteid')->from('tblfavorites')->where('favtype = "Venue" AND typeid=' . $venue_id . ' AND userid=' . $user_id)->get()->row();
        if (!empty($exist)) {
            $this->db->where('userid', $user_id);
            $this->db->where('typeid', $venue_id);
            $this->db->where('favtype', "Venue");
            $this->db->delete('tblfavorites');
            return "deleted";
        } else {
            $this->db->insert('tblfavorites', array(
                'favtype' => "Venue",
                'typeid' => $venue_id,
                'userid' => $user_id
            ));
            return "added";
        }
    }

    /**
     * to approved venues for each brand
     */
    public function get_approved_venues()
    {
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];

        if ($is_sido_admin == 0 && $is_admin == 0) {
            $brandid = get_user_session();
            $this->db->join('tblbrandvenue', 'tblbrandvenue.venueid = tblvenue.venueid', 'left');
            $this->db->where('tblbrandvenue.deleted', 0);
            $this->db->where('tblbrandvenue.brandid', $brandid);
        }

        $this->db->where('tblvenue.deleted', 0);
        $this->db->where('tblvenue.isapproved', 1);

        $this->db->order_by('venuename', 'desc');

        return $this->db->get('tblvenue')->result_array();
    }

    /**
     * to get sitelocation for venue
     */
    public function get_sitelocations($venueid)
    {
        $this->db->select('*');
        $this->db->where('tblvenue_sitecontact.deleted', 0);
        $this->db->where('tblvenue_sitecontact.venueid', $venueid);

        $this->db->order_by('sitelocation_name', 'asc');

        return $this->db->get('tblvenue_sitecontact')->result_array();
    }

    /**
     * to delete vneue
     */
    public function delete($id)
    {
        $brandid = get_user_session();

        if ($brandid > 0) {
            $cond = ' AND brandid = ' . $brandid;
        } else {
            $cond = ' AND 1 = 1';
        }

        $query = $this->db->query('SELECT * FROM `tblprojects` WHERE `deleted` = 0 AND (`eventstartdatetime` >= NOW() OR `eventenddatetime` >= NOw()) AND `venueid` = ' . $id . $cond);
        if ($query->num_rows() > 0) {
            return array(
                'referenced' => true
            );
        }

        $affectedRows = 0;

        if ($brandid > 0) {
            $data['deleted'] = 1;
            $this->db->where('venueid', $id);
            $this->db->where('brandid', $brandid);
            $this->db->update('tblbrandvenue', $data);
        } else {
            $data['deleted'] = 1;
            $data['updated_by'] = get_staff_user_id();
            $data['dateupdated'] = date('Y-m-d H:i:s');
            $this->db->where('venueid', $id);
            $this->db->update('tblvenue', $data);
        }

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            logActivity('Venue deleted successfully ' . $id);

            return true;
        }

        return false;
    }

    /**
     * Added By: Vaidehi
     * Dt: 02/27/2018
     * For Pin/Unpin Venue
     */
    public function pinvenue($venue_id)
    {
        $session_data = get_session_data();
        $user_id = $session_data['staff_user_id'];

        $pinexist = $this->db->select('pinid')->from('tblpins')->where('pintype = "Venues" AND pintypeid = ' . $venue_id . ' AND userid = ' . $user_id)->get()->row();
        if (!empty($pinexist)) {
            $this->db->where('userid', $user_id);
            $this->db->where('pintypeid', $venue_id);
            $this->db->where('pintype', "Venues");
            $this->db->delete('tblpins');

            return 0;
        } else {
            $this->db->insert('tblpins', array(
                'pintype' => "Venues",
                'pintypeid' => $venue_id,
                'userid' => $user_id
            ));

            return 1;
        }
    }

    /**
     * Added By : Vaidehi
     * Dt : 02/28/2018
     * to get global venue
     */
    public function get_global_venues()
    {
        $brandid = get_user_session();
        $query = "SELECT v.* FROM `tblvenue` v WHERE v.venueid NOT IN (SELECT bv.venueid FROM tblbrandvenue as bv WHERE bv.brandid = $brandid AND bv.deleted = 0) AND v.`isapproved` = 1 AND v.`deleted` = 0";
        $result = $this->db->query($query);
        $rows = $result->result_array();
        return $rows;

    }

    /**
     * Added By : Vaidehi
     * Dt : 02/28/2018
     * to add global venue
     */
    public function add_existing_venue($data)
    {
        $brandid = get_user_session();
        $pid = $data['hdnpid'];
        $lid = $data['hdnlid'];
        $eid = $data['hdneid'];

        unset($data['hdnlid']);
        unset($data['hdnpid']);
        unset($data['hdneid']);

        $this->db->insert(' tblbrandvenue', array(
            'venueid' => $data['venues'],
            'brandid' => $brandid
        ));
        $insert_id = $this->db->insert_id();

        if ($pid > 0) {
            $this->db->insert('tblprojectvenue', array(
                'venueid' => $data['venues'],
                'projectid' => $pid,
                'brandid' => $brandid
            ));
        } elseif ($eid > 0) {
            $this->db->insert('tblprojectvenue', array(
                'venueid' => $data['venues'],
                'eventid' => $eid,
                'brandid' => $brandid
            ));
        } elseif ($lid > 0) {
            $this->db->insert('tblleadvenue', array(
                'venueid' => $data['venues'],
                'leadid' => $lid,
                'brandid' => $brandid
            ));
        }
        return $insert_id;
    }

    /**
     * Added By: Masud
     * Dt: 06/26/2018
     * For Kanban view Contact
     */

    public function get_kanban_venues($leadid = "", $projectid = "", $eventid = "", $limit = 9, $page = 1, $search = "", $is_kanban = false)
    {
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];

        if ($is_sido_admin == 0 && $is_admin == 0) {
            $brandid = get_user_session();

            $this->db->join('tblbrandvenue', 'tblbrandvenue.venueid = tblvenue.venueid', 'left');
            $this->db->where('tblbrandvenue.deleted', 0);
            $this->db->where('tblbrandvenue.brandid', $brandid);
        }

        $this->db->where('tblvenue.deleted', 0);
        $this->db->where('(tblvenue.ispublic = 1 OR tblvenue.created_by=' . get_staff_user_id() . ')');

        //$this->db->where('tblvenue.isapproved', 1);
        if ($is_kanban == true && $limit > 0) {
            $start = ($page - 1) * $limit;
            $this->db->limit($limit, $start);
        }
        if (!empty($search)) {
            $this->db->like('tblvenue.venuename', $search);
        }
        $this->db->order_by('venuename', 'asc');

        $result = $this->db->get('tblvenue')->result_array();
        return $result;
    }

    /**
     * Added By: Masud
     * Dt: 07/04/2018
     * For Venue favorite
     */
    function get_favorite($id)
    {
        $session_data = get_session_data();
        $user_id = $session_data['staff_user_id'];
        $favorit = $this->db->select('favoriteid')->from('tblfavorites')->where('favtype = "Venue" AND typeid=' . $id . ' AND userid=' . $user_id)->get()->row();
        return $favorit;
    }

    /**
     * Added By: Masud
     * Dt: 07/09/2018
     * For file title
     */
    function update_attachment($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblfiles', $data);
        return $this->db->affected_rows();
    }

    /**
     * Added By: Masud
     * Dt: 07/09/2018
     * For file title
     */

    function add_venue_contact($data)
    {
        $this->db->insert('tblvenuecontact', $data);
    }

    /**
     * Added By: Masud
     * Dt: 07/09/2018
     * For venue contact
     */
    function get_venue_contact($id, $rel_type = "venue")
    {
        $this->db->select('venuecontactid,addressbookid,created_by');
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', $rel_type);
        $this->db->where('deleted', 0);
        $results = $this->db->get('tblvenuecontact')->result_array();
        return $results;

    }

    /**
     * Added By: Masud
     * Dt: 07/10/2018
     * For venue contact
     */
    function deletecontact($id)
    {
        $data = array('deleted' => 1);
        $this->db->where('venuecontactid', $id);
        $this->db->update('tblvenuecontact', $data);
        return $this->db->affected_rows();
    }

    /**
     * Added By: Masud
     * Dt: 07/10/2018
     * For venue On site location
     */
    function savelocation($data, $id = "")
    {
        if ($id > 0) {
            $data['dateupdated'] = date('Y-m-d H:i:s');
            $this->db->where('locid', $id);
            $this->db->update('tblvenueloc', $data);
            handle_venue_loc_image_upload($id);
            return $this->db->affected_rows();
        } else {
            $data['created_by'] = get_staff_user_id();
            $this->db->insert('tblvenueloc', $data);
            $insertid = $this->db->insert_id();
            handle_venue_loc_image_upload($insertid);
            return $insertid;
        }
    }

    function getlocation($id)
    {
        $this->db->where('locid', $id);
        $this->db->where('deleted', 0);
        $results = $this->db->get('tblvenueloc')->row();
        $results->venueattachments = $this->get_venue_attachments($id, 'venuelocfile');
        $results->venuegallery = $this->get_venue_attachments($id, 'venuelocgallery');
        return $results;
    }

    function getlocations($venueid, $type = "")
    {
        $this->db->where('venueid', $venueid);
        $this->db->where('deleted', 0);
        if ($type != "") {
            $this->db->where('type', $type);
        }
        $results = $this->db->get('tblvenueloc')->result();
        return $results;
    }

    function deletelocation($id)
    {
        $data = array('deleted' => 1);
        $this->db->where('locid', $id);
        $this->db->update('tblvenueloc', $data);
        return $this->db->affected_rows();
    }
    public function getvenue($id = '', $where = array())
    {
        $this->db->where('tblvenue.deleted', 0);
        if (is_numeric($id)) {
            $this->db->where('tblvenue.venueid', $id);
            $venue = $this->db->get('tblvenue')->row();
            return $venue;
        }
    }
}