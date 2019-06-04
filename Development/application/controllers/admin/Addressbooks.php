<?php

/**
 * Added By: Avni
 * Dt: 10/11/2017
 * Address Book Module
 */


defined('BASEPATH') or exit('No direct script access allowed');

class Addressbooks extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('venues_model');
    }


    /* List all Addressbook */
    public function index()
    {
        $pg = $this->input->get('pg');

        if (!has_permission('addressbook', '', 'view', true)) {
            access_denied('addressbook');
        }
        if ($this->input->is_ajax_request()) {
            if (!$this->input->get('kanban')) {
                $this->perfex_base->get_table_data('addressbook');
            }
        }
        $data['addressbook'] = $this->addressbooks_model->get('', 1);
        $data['title'] = _l('addressbooks');

        //Added By Avni on 10/31 for loading lead name by lead id.
        if ($this->input->get('lid')) {
            $leadid = $this->input->get('lid');

            $this->load->model('leads_model');

            $data['lid'] = $leadid;
            $data['lname'] = '';
            if ($leadid != "") {
                $data['lname'] = $this->leads_model->get($leadid)->name;
            }
            $data['title'] = _l('lead_addressbooks');
            $data['clients'] = $this->addressbooks_model->get_existing_contacts('tblleadcontact', "leadid", $this->input->get('lid'));
        } elseif ($this->input->get('pid')) {
            $projectid = $this->input->get('pid');

            $this->load->model('projects_model');
            if (isset($_GET['eid'])) {
                $data['parent_id'] = $this->projects_model->get($_GET['eid'])->parent;
            } else {
                $data['parent_id'] = 0;
            }
            $data['pid'] = $projectid;
            $data['lname'] = '';
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
            $data['title'] = _l('project_addressbooks');
            $data['clients'] = $this->addressbooks_model->get_existing_contacts('tblprojectcontact', "projectid", $this->input->get('pid'));
        } elseif ($this->input->get('eid')) {
            $projectid = $this->input->get('eid');
            $parent_id = $this->projects_model->get($projectid)->parent;
            $this->load->model('projects_model');

            $data['eid'] = $projectid;
            $data['parent_id'] = $parent_id;
            $data['lname'] = '';
            $data['pname'] = '';
            if ($parent_id != "") {
                $data['pname'] = $this->projects_model->get($parent_id)->name;
            }
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
            $data['title'] = _l('project_addressbooks');
            $data['clients'] = $this->addressbooks_model->get_existing_contacts('tblprojectcontact', "projectid", $this->input->get('eid'));
        } else {
            $data['clients'] = $this->addressbooks_model->get_global_adddress();
        }
        $data['pg'] = $pg;

        $data['switch_contacts_kanban'] = true;
        if ($this->session->has_userdata('contacts_kanban_view') && $this->session->userdata('contacts_kanban_view') == 'true') {
            $data['switch_contacts_kanban'] = false;
            $data['bodyclass'] = 'kan-ban-body';
        }
        if (is_mobile()) {
            $this->session->set_userdata(array(
                'contacts_kanban_view' => 0
            ));
        }
        if ($this->input->is_ajax_request()) {
            if ($this->input->get('kanban')) {
                if ($this->input->get('limit')) {
                    $data['limit'] = $this->input->get('limit');
                }
                if ($this->input->get('page')) {
                    $data['page'] = $this->input->get('page');
                }
                $data['totalcontacts'] = $this->addressbooks_model->get_kanban_contacts("", "", "", "", "", $this->input->get('search'), $this->input->get('kanban'));

                $data['contacts'] = $this->addressbooks_model->get_kanban_contacts("", "", "", $this->input->get('limit'), $this->input->get('page'), $this->input->get('search'), $this->input->get('kanban'));
                echo $this->load->view('admin/addressbooks/kan-ban', $data, true);
                die();
            }
        } else {
            $this->load->view('admin/addressbooks/manage', $data);
        }
    }

    /* Added by Purvi on 10-12-2017*/
    public function addressbook($id = '')
    {
        $lid = $this->input->get('lid');
        $pid = $this->input->get('pid');
        $eid = $this->input->get('eid');

        $venueid = $this->input->get('venue');
        $locid = $this->input->get('locid');
        $vid = $this->input->get('vid');

        if (!has_permission('addressbook', '', 'view', true)) {
            access_denied('addressbooks');
        }
        if ($this->input->post()) {
            if ($this->input->post('hdnlid')) {
                $postlid = $this->input->post('hdnlid');
            } elseif ($this->input->post('hdnpid')) {
                $postpid = $this->input->post('hdnpid');
            } elseif ($this->input->post('hdneid')) {
                $posteid = $this->input->post('hdneid');
            } elseif ($this->input->post('hdnvenueid')) {
                $postvenueid = $this->input->post('hdnvenueid');
            } elseif ($this->input->post('hdnlocid')) {
                $postlocid = $this->input->post('hdnlocid');
                $postvid = $this->input->post('hdnvid');
            }
            $data = $this->input->post();
            $files = array();
            if (isset($_FILES['contact'])) {
                foreach ($_FILES['contact'] as $i => $valarray) {
                    foreach ($valarray as $j => $val) {
                        $files[$j]['profile_image'][$i] = $val['profile_image'];
                    }
                }
            }
            if ($id == '') {
                if (!has_permission('addressbook', '', 'create', true)) {
                    access_denied('addressbooks');
                }
                if (isset($data['contact']) && count($data['contact'] > 0)) {
                    foreach ($data['contact'] as $key => $datacontact) {
                        if (isset($datacontact['favourite'])) {
                            $favourite = $datacontact['favourite'];
                            unset($datacontact['favourite']);
                        }
                        $id = $this->addressbooks_model->add($datacontact);
                        if ($id) {

                            if (isset($favourite)) {
                                $this->favorite($id);
                            }
                            $file = $files[$key];
                            if (isset($_POST['imagebase64'])) {
                                $data = $_POST['imagebase64'];
                                list($type, $data) = explode(';', $data);
                                list(, $data) = explode(',', $data);
                                $data = base64_decode($data);
                                $path = get_upload_path_by_type('addressbook') . $id . '/';
                                _maybe_create_upload_path($path);
                                $filename = unique_filename($path, $_FILES["profile_image"]["name"]);
                                $path .= 'round_' . $filename;
                                file_put_contents($path, $data);
                            }
                            handle_multiple_addressbook_profile_image_upload($id, $file);
                            if ((isset($postvenueid) && $postvenueid != "") || (isset($postlocid) && !empty($postlocid))) {
                                $venuecontactdata = [];
                                if ((isset($postvenueid) && $postvenueid != "")) {
                                    $venuecontactdata['rel_id'] = $postvenueid;
                                    $venuecontactdata['rel_type'] = 'venue';
                                } else {
                                    $venuecontactdata['rel_id'] = $postlocid;
                                    $venuecontactdata['rel_type'] = 'venueloc';
                                }
                                $venuecontactdata['addressbookid'] = $id;
                                $venuecontactdata['deleted'] = 0;
                                $venuecontactdata['created_by'] = $this->session->userdata['staff_user_id'];
                                $venuecontactdata['datecreated'] = date('Y-m-d H:i:s');
                                $this->venues_model->add_venue_contact($venuecontactdata);
                            }
                        }
                    }
                }

                if ($id) {
                    /*if (isset($favourite)) {
                        $this->favorite($id);
                    }
                    handle_addressbook_profile_image_upload($id);*/

                    set_alert('success', _l('added_successfully', _l('contact')));
                    if (isset($postlid) && $postlid != "") {
                        redirect(admin_url('addressbooks/?lid=' . $postlid));
                    } elseif (isset($postpid) && $postpid != "") {
                        redirect(admin_url('addressbooks/?pid=' . $postpid));
                    } elseif (isset($posteid) && $posteid != "") {
                        redirect(admin_url('addressbooks/?eid=' . $posteid));
                    } elseif (isset($postvenueid) && $postvenueid != "") {
                        redirect(admin_url('venues/view/' . $postvenueid));
                    } elseif (isset($postlocid) && $postlocid != "") {
                        redirect(admin_url('venues/onsitelocview/' . $postlocid) . "?venue=" . $postvid);
                    } else {
                        redirect(admin_url('addressbooks/'));
                    }
                } else {
                    set_alert('danger', _l('problem_addressbook_adding', _l('addressbook_lowercase')));
                    if (isset($postlid) && $postlid != "") {
                        redirect(admin_url('addressbooks/addressbook' . $id . "?lid=" . $postlid));
                    } elseif (isset($postpid) && $postpid != "") {
                        redirect(admin_url('addressbooks/addressbook' . $id . "?pid=" . $postpid));
                    } elseif (isset($posteid) && $posteid != "") {
                        redirect(admin_url('addressbooks/addressbook' . $id . "?eid=" . $posteid));
                    } elseif (isset($postvenueid) && $postvenueid != "") {
                        redirect(admin_url('addressbooks/addressbook?venue=' . $postvenueid));
                    } elseif (isset($postlocid) && $postlocid != "") {
                        redirect(admin_url('addressbooks/addressbook?locid=' . $postlocid . '&vid=' . $postvid));
                    } else {
                        redirect(admin_url('addressbooks/addressbook/' . $id));
                    }
                }

            } else {
                if (isset($data['favourite'])) {
                    $favourite = $data['favourite'];
                    unset($data['favourite']);
                }
                if (!has_permission('addressbook', '', 'edit', true)) {
                    access_denied('addressbooks');
                }
                if (isset($_POST['imagebase64'])) {
                    $data = $_POST['imagebase64'];
                    list($type, $data) = explode(';', $data);
                    list(, $data) = explode(',', $data);
                    $data = base64_decode($data);
                    $path = get_upload_path_by_type('addressbook') . $id . '/';
                    _maybe_create_upload_path($path);
                    $filename = unique_filename($path, $_FILES["profile_image"]["name"]);
                    $path .= 'round_' . $filename;
                    file_put_contents($path, $data);
                }
                handle_addressbook_profile_image_upload($id);
                $data = $this->input->post();
                $success = $this->addressbooks_model->update($data, $id);
                if (isset($favourite)) {
                    $this->favorite($id);
                }
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('contact')));
                    if (isset($postlid) && $postlid != "") {
                        redirect(admin_url('addressbooks?lid=' . $postlid));
                    } elseif (isset($postpid) && $postpid != "") {
                        redirect(admin_url('addressbooks?pid=' . $postpid));
                    } elseif (isset($posteid) && $posteid != "") {
                        redirect(admin_url('addressbooks?eid=' . $posteid));
                    } elseif (isset($venueid) && $venueid != "") {
                        redirect(admin_url('addressbooks/view/' . $id . "?venue=" . $venueid));
                    } elseif (isset($postvenueid) && $postvenueid != "") {
                        redirect(admin_url('venues/view/' . $postvenueid));
                    } elseif (isset($postlocid) && $postlocid != "") {
                        redirect(admin_url('venues/onsitelocview/' . $postlocid) . "?venue=" . $postvid);
                    } else {
                        redirect(admin_url('addressbooks/'));
                    }
                } else {
                    set_alert('danger', _l('problem_addressbook_updating', _l('addressbook_lowercase')));
                    if (isset($postlid) && $postlid != "") {
                        redirect(admin_url('addressbooks/addressbook/' . $id . "?lid=" . $postlid));
                    } elseif (isset($postpid) && $postpid != "") {
                        redirect(admin_url('addressbooks/addressbook/' . $id . "?pid=" . $postpid));
                    } elseif (isset($posteid) && $posteid != "") {
                        redirect(admin_url('addressbooks/addressbook/' . $id . "?eid=" . $posteid));
                    } elseif (isset($postvenueid) && $postvenueid != "") {
                        redirect(admin_url('addressbooks/addressbook/' . $id . "?venue=" . $postvenueid));
                    } elseif (isset($postlocid) && $postlocid != "") {
                        redirect(admin_url('addressbooks/addressbook/' . $id . '?locid=' . $postlocid . '&vid=' . $postvid));
                    } else {
                        redirect(admin_url('addressbooks/addressbook/' . $id));
                    }
                }
            }
        }
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
        $package_type_id = $session_data['package_type_id'];

        $profile_allow = 0;

        if ($is_sido_admin == 1 || $is_admin == 1) {
            $profile_allow = 1;
        } elseif ($package_type_id == 2) {
            $profile_allow = 0;
        } elseif ($package_type_id == 3) {
            $profile_allow = 1;
        }

        $global_search_allow = 0;

        if ($is_sido_admin == 1 || $is_admin == 1) {
            $global_search_allow = 1;
        } elseif ($package_type_id == 1) {
            $global_search_allow = 0;
        } elseif ($package_type_id == 3 || $package_type_id == 2) {
            $global_search_allow = 1;
        }

        if ($id == '') {
            $title = _l('add_new', _l('contact'));
        } else {
            $addressbook = $this->addressbooks_model->get($id);
            $gettype = $this->addressbooks_model->gettype($id, $lid, $pid, $eid);
            if ($lid != "" || $pid != "" || $eid != "") {
                $addressbook->rel_type = $gettype['rel_type'];
                $addressbook->rel_id = $gettype['rel_id'];
            }
            $data['addressbook'] = $addressbook;
            $title = _l('edit', _l('contact')) . ' ' . $addressbook->firstname . " " . $addressbook->lastname;
            $data['favorite'] = $this->addressbooks_model->get_favorite($id);
        }
        $data['roles'] = $this->roles_model->get();
        $data['tags'] = $this->tags_model->get();
        $data['global_search_allow'] = $global_search_allow;
        $data['profile_allow'] = $profile_allow;
        $data['socialsettings'] = $this->addressbooks_model->get_socialsettings();
        $data['email_phone_type'] = get_email_phone_type();
        $data['address_type'] = get_address_type();
        $data['title'] = $title;
        $data['lid'] = $this->input->get('lid');
        if ($data['lid']) {
            $this->load->model('leads_model');
            $data['lname'] = '';
            if ($data['lid'] != "") {
                $data['lname'] = $this->leads_model->get($data['lid'])->name;
            }
        } elseif ($this->input->get('pid')) {
            $projectid = $this->input->get('pid');
            if (isset($_GET['pid'])) {
                $data['parent_id'] = $this->projects_model->get($_GET['pid'])->parent;
            } else {
                $data['parent_id'] = 0;
            }
            $this->load->model('projects_model');

            $data['pid'] = $projectid;
            $data['lname'] = '';
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        } elseif ($this->input->get('eid')) {
            $projectid = $this->input->get('eid');
            $parent_id = $this->projects_model->get($projectid)->parent;
            $this->load->model('projects_model');

            $data['eid'] = $projectid;
            $data['parent_id'] = $parent_id;
            $data['lname'] = '';
            $data['pname'] = '';
            if ($parent_id != "") {
                $data['pname'] = $this->projects_model->get($parent_id)->name;
            }
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        } elseif (isset($venueid) && $venueid > 0) {
            $data['venueid'] = $venueid;
        } elseif (isset($locid) && $locid > 0) {
            $data['locid'] = $locid;
            $data['vid'] = $vid;
        }
        $data['leads'] = $this->addressbooks_model->get_leads();
        $data['projects'] = $this->addressbooks_model->get_projects();
        $data['events'] = $this->addressbooks_model->get_events($pid);
        if ($id == '') {
            $data['index'] = 0;
            $this->load->view('admin/addressbooks/new', $data);
        } else {
            $this->load->view('admin/addressbooks/addressbook', $data);
        }
        //$this->load->view('admin/addressbooks/newform', $data);

    }

    public function delete($id)
    {

        if (!has_permission('addressbook', '', 'delete', true)) {
            access_denied('addressbook');
        }
        if (!$id) {
            redirect(admin_url('addressbooks'));
        }
        if ($this->input->get('pid') || $this->input->get('lid')) {
            if ($this->input->get('pid')) {
                $response = $this->addressbooks_model->delete_project_contact($id, $this->input->get('pid'));
            } else {
                $response = $this->addressbooks_model->delete_lead_contact($id, $this->input->get('lid'));
            }
        } else {
            $response = $this->addressbooks_model->delete($id);
        }
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('addressbook_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('addressbook')));
        } else {
            set_alert('danger', _l('problem_deleting', _l('addressbook_lowercase')));
        }
        // if(isset($lid)) {
        //     redirect(admin_url('addressbooks?lid=' . $lid));
        // }
        // else {
        //     redirect(admin_url('addressbooks'));
        // }
    }

    /* Remove addressbook profile image / ajax */
    public function remove_addressbook_profile_image($id = '')
    {
        if (is_numeric($id) && (has_permission('addressbook', '', 'create', true) || has_permission('addressbook', '', 'edit', true))) {
            $addressbook_id = $id;
        } else {
            $addressbook_id = "";
        }
        $member = $this->addressbooks_model->get($addressbook_id);
        if (file_exists(get_upload_path_by_type('addressbook') . $addressbook_id)) {
            delete_dir(get_upload_path_by_type('addressbook') . $addressbook_id);
        }
        $this->db->where('addressbookid', $addressbook_id);
        $this->db->update('tbladdressbook', array(
            'profile_image' => null
        ));
        if ($this->input->is_ajax_request()) {
            die();
        }
        if (isset($lid)) {
            redirect(admin_url('addressbooks/addressbook/' . $addressbook_id . '?lid=' . $lid));
        } else {
            redirect(admin_url('addressbooks/addressbook/' . $addressbook_id));
        }

    }

    /* Added by Avni on 11/13/2017  */
    public function favorite($id = "")
    {
        if (isset($_POST['contact_id'])) {
            $addressbook_id = $_POST['contact_id'];
        } elseif (isset($id)) {
            $addressbook_id = $id;
        }
        $favoritedata = $this->addressbooks_model->favorite($addressbook_id);
        if (isset($id) && $id > 0) {
            return $favoritedata;
        }
        echo $favoritedata;
        die;
    }

    public function choose_existing_contact()
    {
        if (!has_permission('addressbook', '', 'create', true)) {
            access_denied('addressbooks');
        }
        $postlid = $this->input->post('hdnlid');
        $postpid = $this->input->post('hdnpid');
        $posteid = $this->input->post('hdneid');
        $id = $this->addressbooks_model->add_existing_contact($this->input->post());
        if ($id) {
            set_alert('success', _l('added_successfully', _l('contact')));
            //redirect(admin_url('addressbooks/addressbook/' . $id));
            if (isset($postlid) && $postlid != "") {
                redirect(admin_url('addressbooks/?lid=' . $postlid));
            } elseif (isset($postpid) && $postpid != "") {
                redirect(admin_url('addressbooks/?pid=' . $postpid));
            } elseif (isset($posteid) && $posteid != "") {
                redirect(admin_url('addressbooks/?eid=' . $posteid));
            } else {
                redirect(admin_url('addressbooks/'));
            }
        } else {
            set_alert('danger', _l('problem_addressbook_adding', _l('addressbook_lowercase')));
            if (isset($postlid) && $postlid != "") {
                redirect(admin_url('addressbooks/addressbook' . $id . "?lid=" . $postlid));
            } elseif (isset($postpid) && $postpid != "") {
                redirect(admin_url('addressbooks/addressbook' . $id . "?pid=" . $postpid));
            } elseif (isset($posteid) && $posteid != "") {
                redirect(admin_url('addressbooks/addressbook' . $id . "?eid=" . $posteid));
            } else {
                redirect(admin_url('addressbooks/addressbook/' . $id));
            }
        }
    }

    /* Added by Purvi on 01-12-2018 for addressbook details */
    public function view($id = '')
    {
        if (!has_permission('addressbook', '', 'view', true)) {
            access_denied('addressbook');
        }

        $lid = $this->input->get('lid');
        $pid = $this->input->get('pid');
        $eid = $this->input->get('eid');
        $venueid = $this->input->get('venue');
        $locid = $this->input->get('locid');
        $vid = $this->input->get('vid');

        if ($this->input->get('lid')) {
            $leadid = $this->input->get('lid');

            $this->load->model('leads_model');

            $data['lid'] = $leadid;
            $data['lname'] = '';
            if ($leadid != "") {
                $data['lname'] = $this->leads_model->get($leadid)->name;
            }
        } elseif ($this->input->get('pid')) {
            $projectid = $this->input->get('pid');

            $this->load->model('projects_model');

            $data['pid'] = $projectid;
            $data['lname'] = '';
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        } elseif ($this->input->get('eid')) {
            $projectid = $this->input->get('eid');
            $parent_id = $this->projects_model->get($projectid)->parent;
            $this->load->model('projects_model');

            $data['eid'] = $projectid;
            $data['parent_id'] = $parent_id;
            $data['lname'] = '';
            $data['pname'] = '';
            if ($parent_id != "") {
                $data['pname'] = $this->projects_model->get($parent_id)->name;
            }
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        }

        if (is_numeric($id)) {
            $addressbook = $this->addressbooks_model->viewcontact($id);
            if (!$addressbook) {
                access_denied('addressbook');
            }
        }
        $offsetobj = $this->misc_model->get_timezoneoffset();
        $offset = explode(':', $offsetobj->timezoneoffset);
        $data['timeoffset'] = $offset[0];
        $data['addressbook'] = $addressbook;
        $data['addressbookid'] = $id;
        $data['notes'] = $this->misc_model->get_notes($id, 'addressbook');
        $data['favorite'] = $this->addressbooks_model->get_favorite($id);
        if (isset($locid) && $locid > 0) {
            $data['locid'] = $locid;
            $data['vid'] = $vid;
        }
        $this->load->view('admin/addressbooks/view', $data);
    }

    /**
     * Added By: Vaidehi
     * Dt: 02/27/2018
     * for pinned contact
     */
    public function pincontact()
    {
        $contact_id = $this->input->post('contact_id');
        $pindata = $this->addressbooks_model->pincontact($contact_id);
        echo $pindata;
        exit;
    }

    /**
     * Added By : Masud
     * Dt : 06/11/2018
     * kanban view for meeting
     */
    public function switch_contacts_kanban($set = 0)
    {
        {
            if ($set == 1) {
                $set = 'true';
            } else {
                $set = 'false';
            }
            $this->session->set_userdata(array(
                'contacts_kanban_view' => $set
            ));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * Added By : Masud
     * Dt : 07/03/2018
     * Add note for addressbook
     */
    public function add_note($rel_id)
    {
        if (!is_staff_member()) {
            header("HTTP/1.0 404 Not Found");
            echo _l('access_denied');
            die;
        }
        $venueid = $this->input->get('venue');
        $qstring = "";

        if ($this->input->post()) {
            $data = $this->input->post();
            $lid = $data['hdnlid'];
            $pid = $data['hdnpid'];
            $eid = $data['hdneid'];
            unset($data['hdnlid']);
            unset($data['hdnpid']);
            unset($data['hdneid']);
            if ($venueid > 0) {
                if ($lid > 0 || $pid > 0 || $eid > 0) {
                    $qstring = "&venue=" . $venueid;
                } else {
                    $qstring = "?venue=" . $venueid;
                }
            }
            $note_id = $this->misc_model->add_note($data, 'addressbook', $rel_id);
            if ($note_id) {
                set_alert('success', "Contact note added successfully");
                //redirect(admin_url('meetings/meeting/' . $id));
                if ($lid != "") {
                    redirect(admin_url('addressbooks/view/' . $rel_id . '?lid=' . $lid . $qstring));
                } elseif ($pid != "") {
                    redirect(admin_url('addressbooks/view/' . $rel_id . '?pid=' . $pid . $qstring));
                } elseif ($eid != "") {
                    redirect(admin_url('addressbooks/view/' . $rel_id . '?eid=' . $eid . $qstring));
                } else {
                    redirect(admin_url('addressbooks/view/' . $rel_id . $qstring));
                }
            }
        }
        echo $rel_id;
        exit;
    }

    public function add_signer()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            if (isset($data['contactid'])) {
                $rcid = $this->addressbooks_model->addrelcontact($data);
                if ($rcid > 0) {
                    $id = $data['contactid'];
                    if (isset($data['projectid']) && $data['projectid'] > 0) {
                        $data['rel_type'] = "project";
                        $data['project'] = $data['projectid'];
                    } else {
                        $data['rel_type'] = "lead";
                        $data['lead'] = $data['leadid'];
                    }
                }
            } else {
                $data['email'] = array(
                    array('type' => 'primary', 'email' => $data['email'])
                );
                $data['ajax'] = 'ajax';
                $data['ispublic'] = 0;
                $id = $this->addressbooks_model->add($data);
            }
            if ($data['rel_type'] != "") {
                $clients = $this->get_clients($data['rel_type'], $data[$data['rel_type']]);
                $members = $this->staff_model->get('', 1, array('is_not_staff' => 0));
            } ?>
            <select class="selectpicker memberpicker" name="signer">
                <?php if (isset($members) && !empty($members)) { ?>
                    <optgroup label="Members">
                        <?php foreach ($members as $member) { ?>
                            <option value="<?php echo $member['staffid'] ?>"
                                    data-id="member"
                                    data-subtext="<?php echo $member['lastname'] ?>"
                                    data-fname="<?php echo $member['firstname'] ?>"
                                    data-designation="<?php echo isset($member['designation']) ? $member['designation'] : "" ?>"><?php echo $member['firstname'] ?></option>
                        <?php } ?>
                    </optgroup>
                <?php } ?>
                <?php if (isset($clients) && !empty($clients)) { ?>
                    <optgroup label="Clients">
                        <?php foreach ($clients as $client) { ?>
                            <option value="<?php echo $client['id'] ?>"
                                    data-id="client"
                                    data-subtext="<?php echo $client['lastname'] ?>"
                                    data-fname="<?php echo $client['firstname'] ?>"
                                    data-designation="<?php echo isset($client['designation']) ? $member['designation'] : "" ?>" <?php echo $id == $client['id'] ? "selected disabled" : ""; ?>><?php echo $client['firstname'] ?></option>
                        <?php } ?>
                    </optgroup>
                <?php } ?>
                <option value="new">Add new signer</option>
            </select>
            <?php die();
        }
    }

    function get_clients($type, $id)
    {
        /*if (isset($_GET['lid']) || isset($_GET['pid'])) {*/
        $brandid = get_user_session();
        if ($type == "lead") {
            $data['rel_content'] = $this->leads_model->get($id);
            $this->db->select('contactid');
            $this->db->distinct();
            $this->db->where('leadid', $id);
            $this->db->where('brandid', $brandid);
            $contacts = $this->db->get('tblleadcontact')->result();
        } else {
            $this->db->select('id');
            $this->db->where('(parent = ' . $id . ' OR id = ' . $id . ')');
            $this->db->where('deleted', 0);
            $related_project_ids = $this->db->get('tblprojects')->result_array();
            $related_project_ids = array_column($related_project_ids, 'id');
            if (!empty($related_project_ids)) {
                $related_project_ids = implode(",", $related_project_ids);
                $this->db->select('contactid');
                $this->db->distinct();
                $this->db->where('(projectid IN (' . $related_project_ids . ') OR eventid IN (' . $related_project_ids . '))');
                $this->db->where('isvendor', 0);
                $this->db->where('iscollaborator', 0);
                $this->db->where('brandid', $brandid);
                $contacts = $this->db->get('tblprojectcontact')->result();
            }
            $data['rel_content'] = $this->projects_model->get($id);
        }
        foreach ($contacts as $key => $contact) {
            $contactid = $contact->contactid;
            $clients[$key]['id'] = $contactid;
            $query = "SELECT firstname,lastname FROM tbladdressbook WHERE addressbookid=" . $contactid;
            $result = $this->db->query($query);
            $name = "";
            if (!empty($result->first_row())) {
                $name = $result->first_row()->firstname . " " . $result->first_row()->lastname;
                $clients[$key]['name'] = $name;
                $clients[$key]['firstname'] = $result->first_row()->firstname;
                $clients[$key]['lastname'] = $result->first_row()->lastname;
            }


            $query = "SELECT phone FROM tbladdressbookphone WHERE type='primary' AND addressbookid=" . $contactid;
            $result = $this->db->query($query);
            $phone = "";
            if (!empty($result->first_row())) {
                $phone = $result->first_row()->phone;
                $clients[$key]['phone'] = $phone;
            }


            $query = "SELECT email FROM tbladdressbookemail WHERE type='primary' AND addressbookid=" . $contactid;
            $result = $this->db->query($query);
            $email = "";
            if (!empty($result->first_row())) {
                $email = $result->first_row()->email;
                $clients[$key]['email'] = $email;
            }
        }
        if (!empty($clients)) {
            return $clients;
        }
        /*}*/
    }

    function addnewcontact()
    {
        $lid = $this->input->get('lid');
        $pid = $this->input->get('pid');
        $eid = $this->input->get('eid');

        $venueid = $this->input->get('venue');
        $locid = $this->input->get('locid');
        $vid = $this->input->get('vid');
        $data = array();
        $data['index'] = $_POST['index'];
        $session_data = get_session_data();
        $is_sido_admin = $session_data['is_sido_admin'];
        $is_admin = $session_data['is_admin'];
        $package_type_id = $session_data['package_type_id'];

        $profile_allow = 0;

        if ($is_sido_admin == 1 || $is_admin == 1) {
            $profile_allow = 1;
        } elseif ($package_type_id == 2) {
            $profile_allow = 0;
        } elseif ($package_type_id == 3) {
            $profile_allow = 1;
        }

        $global_search_allow = 0;

        if ($is_sido_admin == 1 || $is_admin == 1) {
            $global_search_allow = 1;
        } elseif ($package_type_id == 1) {
            $global_search_allow = 0;
        } elseif ($package_type_id == 3 || $package_type_id == 2) {
            $global_search_allow = 1;
        }
        $data['roles'] = $this->roles_model->get();
        $data['tags'] = $this->tags_model->get();
        $data['global_search_allow'] = $global_search_allow;
        $data['profile_allow'] = $profile_allow;
        $data['socialsettings'] = $this->addressbooks_model->get_socialsettings();
        $data['email_phone_type'] = get_email_phone_type();
        $data['address_type'] = get_address_type();
        if ($this->input->get('lid')) {
            $data['lid'] = $this->input->get('lid');
            $this->load->model('leads_model');
            $data['lname'] = '';
            if ($data['lid'] != "") {
                $data['lname'] = $this->leads_model->get($data['lid'])->name;
            }
        } elseif ($this->input->get('pid')) {
            $projectid = $this->input->get('pid');
            if (isset($_GET['pid'])) {
                $data['parent_id'] = $this->projects_model->get($_GET['pid'])->parent;
            } else {
                $data['parent_id'] = 0;
            }
            $this->load->model('projects_model');

            $data['pid'] = $projectid;
            $data['lname'] = '';
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        } elseif (isset($venueid) && $venueid > 0) {
            $data['venueid'] = $venueid;
        } elseif (isset($locid) && $locid > 0) {
            $data['locid'] = $locid;
            $data['vid'] = $vid;
        }
        $data['leads'] = $this->addressbooks_model->get_leads();
        $data['projects'] = $this->addressbooks_model->get_projects();
        $data['events'] = $this->addressbooks_model->get_events($pid);
        $this->load->view('admin/addressbooks/newform', $data);
    }

    public function delete_note($id)
    {
        if (!is_staff_member()) {
            header("HTTP/1.0 404 Not Found");
            echo _l('access_denied');
            die;
        }
        if ($this->misc_model->delete_note($id)) {
            echo "success";
            die;
        }
    }
}