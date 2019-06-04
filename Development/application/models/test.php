<?php
//get all vendors who have accepted invite
            $q1 = $this->db->query('SELECT GROUP_CONCAT(`id`) AS pid FROM `tblprojects` WHERE `id` = '. $id .' OR `parent` = '.$id);
            $all_project = $q1->row();

            $q1 = $this->db->query('SELECT GROUP_CONCAT(`inviteid`) AS pinviteid FROM `tblinvitestatus` WHERE `projectid` IN ('. $all_project->pid .') AND status = "' . $this->invite_status[3] .'"');
            $all_invite = $q1->row();
            
            if($all_invite->pinviteid != NULL) {
                $query = $this->db->query('SELECT `inviteid`, `companyname`, CONCAT(`firstname`, " ", `lastname`) AS vendor_name, `email`, `staffid`, `contactid`, `tags` FROM `tblinvite` WHERE `deleted` = 0 AND `inviteid` IN ('. $all_invite->pinviteid .')');
                $invite_vendor = $query->result_array();
                $vendorlists = [];
                if(count($invite_vendor) > 0) {
                    foreach ($invite_vendor as $invite) {
                        $status_query = $this->db->query('SELECT `status` FROM `tblinvitestatus` WHERE `inviteid` =' . $invite['inviteid'] . ' ORDER BY `invitestatusid` DESC LIMIT 0,1');
                        $status_details = $status_query->row();

                        //if vendor has accept invite, get that vendor details
                        if($status_details->status == $this->invite_status[3]) {
                            if(isset($invite['staffid'])) {
                                $staff_query = $this->db->query('SELECT `staffid`, CONCAT(`firstname`, " ", `lastname`) AS vendor_name, profile_image FROM `tblstaff` WHERE `staffid` = ' . $invite['staffid']);
                                $staff_details = $staff_query->row();

                                //$details['inviteid']        = $invite['inviteid'];
                                $details['name']            = $staff_details->vendor_name;
                                $details['companyname']     = '';
                                $details['image']           = staff_profile_image($staff_details->staffid, array('staff-profile-image-small'));

                                $details['tags']  = '';

                                array_push($vendorlists, $details);
                            } else if(isset($invite['contactid']) && $invite['contactid'] > 0) {
                                $contact_query = $this->db->query('SELECT `addressbookid`, `companyname`, CONCAT(`firstname`, " ", `lastname`) AS vendor_name, profile_image FROM `tbladdressbook` WHERE `addressbookid` = ' . $invite['contactid']);

                                //$details['inviteid']        = $invite['inviteid'];
                                $details['name']            = $contact_details->vendor_name;
                                $details['companyname']     = $contact_details->companyname;
                                $details['image']           = addressbook_profile_image($contact_details->addressbookid, array("addressbook-profile-image"));

                                $tags_query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS vendor_tags FROM `tbltags` JOIN `tbladdressbooktags` ON `tbladdressbooktags`.`tagid` = `tbltags`.`id` WHERE `deleted` = 0 AND `addressbookid` = ' . $invite['contactid']);
                                $tags_details = $tags_query->row();
                                $details['tags']  = $tags_details->vendor_tags;

                                array_push($vendorlists, $details);
                            } else {
                                $staff_query = $this->db->query('SELECT `staffid`, CONCAT(`firstname`, " ", `lastname`) AS vendor_name, profile_image FROM `tblstaff` WHERE `email` = "' . $invite['email'] . '"');
                                $staff_details = $staff_query->row();
                                
                                //$details['inviteid']        = $invite['inviteid'];
                                $details['name']            = $staff_details->vendor_name;
                                $details['companyname']     = $invite['companyname'];
                                $details['image']           = staff_profile_image($staff_details->staffid, array('staff-profile-image-small'));

                                $tags_query = $this->db->query('SELECT GROUP_CONCAT(`name`) AS vendor_tags FROM `tbltags` WHERE `deleted` = 0 AND `id` IN (' . $invite['tags'] . ')');
                                $tags_details = $tags_query->row();
                                $details['tags']  = $tags_details->vendor_tags;

                                array_push($vendorlists, $details);
                            }
                        }
                    }

                    $project->vendors = array_map("unserialize", array_unique(array_map("serialize", $vendorlists)));
                }
            }

            //get all project tool permission
            $userid  = get_staff_user_id();
            $staff  = $this->staff_model->get($userid);
            $query  = $this->db->query('SELECT `isclient` FROM `tblinvite` WHERE `deleted` = 0 AND `email` = "' . $staff->email . '" OR `staffid` = ' . $userid);
            $is_client = $query->row();
            if(isset($is_client)) {
                $project->is_client = $is_client->isclient;    
            } else {
                $project->is_client = 0;
            }

            $query  = $this->db->query('SELECT GROUP_CONCAT(`name`) AS permisssion_name FROM `tblpermissions` WHERE `permissionid` IN ( SELECT `permissionid` FROM `tbleventpermission` WHERE `deleted` = 0  AND `projectid` = ' . $id . ' AND `inviteid` IN (SELECT `inviteid` FROM `tblinvite` WHERE `deleted` = 0 AND `email` = "' . $staff->email . '" OR `staffid` = ' . $userid . ' GROUP BY `staffid`,`email`))');
            $permission = $query->row();

            $project->permission = $permission->permisssion_name;