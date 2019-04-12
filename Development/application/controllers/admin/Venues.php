<?php

/**
 * Added By: Vaidehi
 * Dt: 02/13/2018
 * Venue Module
 */


defined('BASEPATH') or exit('No direct script access allowed');

class Venues extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /* List all Venue */
    public function index()
    {
        if (!has_permission('addressbook', '', 'view', true)) {
            access_denied('addressbook');
        }

        if ($this->input->is_ajax_request()) {
            if (!$this->input->get('kanban')) {
                $this->perfex_base->get_table_data('venue');
            }
        }

        $data['venue'] = $this->venues_model->get('', 1);
        $data['title'] = _l('Venues');

        //for loading lead name by lead id.        
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

        $data['venues'] = $this->venues_model->get_global_venues();

        $data['switch_venues_kanban'] = true;
        if ($this->session->has_userdata('venues_kanban_view') && $this->session->userdata('venues_kanban_view') == 'true') {
            $data['switch_venues_kanban'] = false;
            $data['bodyclass'] = 'kan-ban-body';
        }

        if (is_mobile()) {
            $this->session->set_userdata(array(
                'venues_kanban_view' => 0
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
                $data['totalvenues'] = $this->venues_model->get_kanban_venues("", "", "", "", "", $this->input->get('search'), $this->input->get('kanban'));

                $data['venues'] = $this->venues_model->get_kanban_venues("", "", "", $this->input->get('limit'), $this->input->get('page'), $this->input->get('search'), $this->input->get('kanban'));
                echo $this->load->view('admin/venues/kan-ban', $data, true);
                die();
            }
        }

        $this->load->view('admin/venues/manage', $data);
    }

    //add venue
    public function venue($id = '')
    {

        $this->load->model('misc_model');

        $lid = $this->input->get('lid');
        $pid = $this->input->get('pid');
        $eid = $this->input->get('eid');

        if (!has_permission('addressbook', '', 'view', true)) {
            access_denied('addressbooks');
        }

        if ($this->input->post()) {
            $postlid = $this->input->post('hdnlid');
            $postpid = $this->input->post('hdnpid');
            $posteid = $this->input->post('hdneid');
            $data = $this->input->post();
            if(isset($data['imagebase64'])){
                unset($data['imagebase64']);
            }
            if(isset($data['bannerbase64'])){
                unset($data['bannerbase64']);
            }
            if (isset($data['favourite'])) {
                $favourite = $data['favourite'];
                unset($data['favourite']);
            }
            if ($id == '') {
                if (!has_permission('addressbook', '', 'create', true)) {
                    access_denied('addressbooks');
                }

                $id = $this->venues_model->add($data);
                if ($id) {
                    if (isset($favourite)) {
                        $this->favorite($id);
                    }
                    handle_venue_image_upload($id);
                    handle_venue_cover_image_upload($id);

                    //for venue images
                    $uploadedFiles = handle_task_attachments_array($id, 'venueimages', 'venueimages');
                    if ($uploadedFiles && is_array($uploadedFiles)) {
                        foreach ($uploadedFiles as $file) {
                            $this->misc_model->add_attachment_to_database($id, 'venueimages', array($file));
                        }
                    }

                    //for venue files
                    $uploadedFiles = handle_task_attachments_array($id, 'venuefiles', 'venueimages');
                    if ($uploadedFiles && is_array($uploadedFiles)) {
                        foreach ($uploadedFiles as $file) {
                            $this->misc_model->add_attachment_to_database($id, 'venuefiles', array($file));
                        }
                    }

                    //for site location images and files
                    foreach ($_FILES as $key => $value) {
                        //site location images
                        if (strpos($key, 'sitelocationimages') !== false) {
                            $uploadedFiles = handle_task_attachments_array($id, $key, 'venueimages');
                            if ($uploadedFiles && is_array($uploadedFiles)) {
                                foreach ($uploadedFiles as $file) {
                                    $this->misc_model->add_attachment_to_database($id, 'sitelocationimages', array($file));
                                }
                            }
                        }

                        //site location files
                        if (strpos($key, 'sitelocationfiles') !== false) {
                            $uploadedFiles = handle_task_attachments_array($id, $key, 'venueimages');
                            if ($uploadedFiles && is_array($uploadedFiles)) {
                                foreach ($uploadedFiles as $file) {
                                    $this->misc_model->add_attachment_to_database($id, 'sitelocationfiles', array($file));
                                }
                            }
                        }
                    }

                    set_alert('success', _l('added_successfully', _l('venue')));

                    if (isset($postlid) && $postlid != "") {
                        redirect(admin_url('venues/?lid=' . $postlid));
                    } elseif (isset($postpid) && $postpid != "") {
                        redirect(admin_url('venues/?pid=' . $postpid));
                    } elseif (isset($posteid) && $posteid != "") {
                        redirect(admin_url('venues/?eid=' . $posteid));
                    } else {
                        redirect(admin_url('venues/'));
                    }
                } else {
                    set_alert('danger', _l('problem_venue_adding', _l('venue_lowercase')));

                    if (isset($postlid) && $postlid != "") {
                        redirect(admin_url('venues/venue' . $id . "?lid=" . $postlid));
                    } elseif (isset($postpid) && $postpid != "") {
                        redirect(admin_url('venues/venue' . $id . "?pid=" . $postpid));
                    } elseif (isset($posteid) && $posteid != "") {
                        redirect(admin_url('venues/venue' . $id . "?eid=" . $posteid));
                    } else {
                        redirect(admin_url('venues/venue/' . $id));
                    }
                }
            } else {
                if (!has_permission('addressbook', '', 'edit', true)) {
                    access_denied('addressbooks');
                }
                handle_venue_image_upload($id);
                handle_venue_cover_image_upload($id);

                //for venue images
                $uploadedFiles = handle_task_attachments_array($id, 'venueimages', 'venueimages');
                if ($uploadedFiles && is_array($uploadedFiles)) {
                    foreach ($uploadedFiles as $file) {
                        $this->misc_model->add_attachment_to_database($id, 'venueimages', array($file));
                    }
                }

                //for venue files
                $uploadedFiles = handle_task_attachments_array($id, 'venuefiles', 'venueimages');
                if ($uploadedFiles && is_array($uploadedFiles)) {
                    foreach ($uploadedFiles as $file) {
                        $this->misc_model->add_attachment_to_database($id, 'venuefiles', array($file));
                    }
                }

                //for site location images and files
                foreach ($_FILES as $key => $value) {
                    //site location images
                    if (strpos($key, 'sitelocationimages') !== false) {
                        $uploadedFiles = handle_task_attachments_array($id, $key, 'venueimages');
                        if ($uploadedFiles && is_array($uploadedFiles)) {
                            foreach ($uploadedFiles as $file) {
                                $this->misc_model->add_attachment_to_database($id, 'sitelocationimages', array($file));
                            }
                        }
                    }

                    //site location files
                    if (strpos($key, 'sitelocationfiles') !== false) {
                        $uploadedFiles = handle_task_attachments_array($id, $key, 'venueimages');
                        if ($uploadedFiles && is_array($uploadedFiles)) {
                            foreach ($uploadedFiles as $file) {
                                $this->misc_model->add_attachment_to_database($id, 'sitelocationfiles', array($file));
                            }
                        }
                    }
                }

                $success = $this->venues_model->update($data, $id);
                if (isset($favourite)) {
                    $this->favorite($id);
                }
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('venue')));
                    if (isset($postlid) && $postlid != "") {
                        redirect(admin_url('venues?lid=' . $postlid));
                    } elseif (isset($postpid) && $postpid != "") {
                        redirect(admin_url('venues?pid=' . $postpid));
                    } elseif (isset($posteid) && $posteid != "") {
                        redirect(admin_url('venues?eid=' . $posteid));
                    } else {
                        redirect(admin_url('venues/'));
                    }
                } else {
                    set_alert('danger', _l('problem_venue_updating', _l('venue_lowercase')));

                    if (isset($postlid) && $postlid != "") {
                        redirect(admin_url('venues/venue' . $id . "?lid=" . $postlid));
                    } elseif (isset($postpid) && $postpid != "") {
                        redirect(admin_url('venues/venue' . $id . "?pid=" . $postpid));
                    } elseif (isset($posteid) && $posteid != "") {
                        redirect(admin_url('venues/venue' . $id . "?eid=" . $posteid));
                    } else {
                        redirect(admin_url('venues/venue/' . $id));
                    }
                }

                if (isset($postlid) && $postlid != "") {
                    redirect(admin_url('venues/venue' . $id . "?lid=" . $postlid));
                } elseif (isset($postpid) && $postpid != "") {
                    redirect(admin_url('venues/venue' . $id . "?pid=" . $postpid));
                } elseif (isset($posteid) && $posteid != "") {
                    redirect(admin_url('venues/venue' . $id . "?eid=" . $posteid));
                } else {
                    redirect(admin_url('venues/venue/' . $id));
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
            $title = _l('add_new', _l('venue'));
        } else {
            $venue = $this->venues_model->get($id);
            $gettype = $this->venues_model->gettype($id, $lid, $pid, $eid);

            if ($lid != "" || $pid != "" || $eid != "") {
                $venue->rel_type = $gettype['rel_type'];
                $venue->rel_id = $gettype['rel_id'];
            }
            $data['venue'] = $venue;
            $title = _l('edit', _l('venue')) . ' ' . $venue->venuename;
            $data['favorite'] = $this->venues_model->get_favorite($id);
        }

        $data['is_sido_admin'] = $is_sido_admin;
        $data['roles'] = $this->roles_model->get();

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
        $data['tags'] = $this->tags_model->get();
        $data['leads'] = $this->addressbooks_model->get_leads();
        $data['projects'] = $this->addressbooks_model->get_projects();
        $data['events'] = $this->addressbooks_model->get_events($pid);
        $data['global_search_allow'] = $global_search_allow;
        $data['profile_allow'] = $profile_allow;
        $data['socialsettings'] = $this->addressbooks_model->get_socialsettings();
        $data['email_phone_type'] = get_email_phone_type();
        $data['address_type'] = get_address_type();
        $this->load->view('admin/venues/venue', $data);
    }

    /* Remove venue logo image / ajax */
    public function remove_venue_logo_image($id = '')
    {
        if (is_numeric($id) && (has_permission('addressbook', '', 'create', true) || has_permission('addressbook', '', 'edit', true))) {
            $venueid = $id;
        } else {
            $venueid = "";
        }

        $member = $this->venues_model->get($venueid);
        if (file_exists(get_upload_path_by_type('venue_logo') . $venueid)) {
            delete_dir(get_upload_path_by_type('venue_logo') . $venueid);
        }

        $this->db->where('venueid', $venueid);
        $this->db->update('tblvenue', array(
            'venuelogo' => null
        ));
        $screen = $this->input->get('screen');
        if ($this->input->is_ajax_request()) {
            return true;
        }
        if ($screen == "view") {
            redirect(admin_url('venues/view/' . $venueid));
        }
        if (isset($lid)) {
            redirect(admin_url('venues/venue/' . $venueid . '?lid=' . $lid));
        } else {
            redirect(admin_url('venues/venue/' . $venueid));
        }

    }

    /* Remove venue cover image / ajax */
    public function remove_venue_cover_image($id = '')
    {
        if (is_numeric($id) && (has_permission('addressbook', '', 'create', true) || has_permission('addressbook', '', 'edit', true))) {
            $venueid = $id;
        } else {
            $venueid = "";
        }

        $member = $this->venues_model->get($venueid);
        if (file_exists(get_upload_path_by_type('venue_coverimage') . $venueid)) {
            delete_dir(get_upload_path_by_type('venue_coverimage') . $venueid);
        }

        $this->db->where('venueid', $venueid);
        $this->db->update('tblvenue', array(
            'venuecoverimage' => null
        ));
        if ($this->input->is_ajax_request()) {
            return true;
        }
        if (isset($lid)) {
            redirect(admin_url('venues/venue/' . $venueid . '?lid=' . $lid));
        } else {
            redirect(admin_url('venues/venue/' . $venueid));
        }

    }

    /* for venue details */
    public function view($id = '')
    {
        if (!has_permission('addressbook', '', 'view', true)) {
            access_denied('addressbook');
        }

        $lid = $this->input->get('lid');
        $pid = $this->input->get('pid');
        $eid = $this->input->get('eid');

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
            $venue = $this->venues_model->get($id);
            if (!$venue) {
                access_denied('addressbook');
            }
        }
        $data['socialsettings'] = $this->addressbooks_model->get_socialsettings();
        $data['notes'] = $this->misc_model->get_notes($id, 'venue');
        $data['tags'] = $this->tags_model->get();
        $data['venue'] = $venue;
        $data['venuecontacts'] = $this->venues_model->get_venue_contact($id);
        $data['venuelocations'] = $this->venues_model->getlocations($id);
        /*$data['venuelocindoor'] = $this->venues_model->getlocations($id,'indoor');
        $data['venuelocoutdoor'] = $this->venues_model->getlocations($id,'outdoor ');*/
        $data['venueid'] = $id;

        $this->load->view('admin/venues/view', $data);
    }

    /**
     * Remove venue attachment
     * @since  Version 1.0.1
     * @param  mixed $id attachment it
     * @return json
     */
    public function remove_venue_attachment($id)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode(array(
                'success' => $this->venues_model->remove_venue_attachment($id)
            ));
        }
    }

    public function remove_venue_loc_attachment($id)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode(array(
                'success' => $this->venues_model->remove_venue_attachment($id, 'location')
            ));
        }
    }

    /**
     * to mark venue as favorite
     */
    public function favorite($id = "")
    {
        $venue_id = $_POST['venue_id'];
        if (isset($_POST['venue_id'])) {
            $venue_id = $_POST['venue_id'];
        } elseif (isset($id)) {
            $venue_id = $id;
        }
        $favoritedata = $this->venues_model->favorite($venue_id);
        if (isset($id)) {
            return $favoritedata;
        }
        echo $favoritedata;
        exit;
    }

    /**
     * to delete venue
     */
    public function delete($id)
    {
        if (!has_permission('addressbook', '', 'delete', true)) {
            access_denied('addressbook');
        }
        if (!$id) {
            redirect(admin_url('addressbooks'));
        }
        $response = $this->venues_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('venue_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('venue')));
        } else {
            set_alert('danger', _l('problem_deleting', _l('venue_lowercase')));
        }
    }

    /**
     * Added By: Vaidehi
     * Dt: 02/27/2018
     * for pinned venue
     */
    public function pinvenue()
    {
        $venue_id = $this->input->post('venue_id');

        $pindata = $this->venues_model->pinvenue($venue_id);

        echo $pindata;
        exit;
    }

    /**
     * Added By: Vaidehi
     * Dt: 02/28/2018
     * for adding existing venue
     */
    public function choose_existing_venue()
    {
        if (!has_permission('addressbook', '', 'create', true)) {
            access_denied('addressbooks');
        }

        $postlid = $this->input->post('hdnlid');
        $postpid = $this->input->post('hdnpid');
        $posteid = $this->input->post('hdneid');
        $id = $this->venues_model->add_existing_venue($this->input->post());
        if ($id) {
            set_alert('success', _l('added_successfully', _l('venue')));

            if (isset($postlid) && $postlid != "") {
                redirect(admin_url('venues/?lid=' . $postlid));
            } elseif (isset($postpid) && $postpid != "") {
                redirect(admin_url('venues/?pid=' . $postpid));
            } elseif (isset($posteid) && $posteid != "") {
                redirect(admin_url('venues/?eid=' . $posteid));
            } else {
                redirect(admin_url('venues/'));
            }
        } else {
            set_alert('danger', _l('problem_venue_adding', _l('venue_lowercase')));

            if (isset($postlid) && $postlid != "") {
                redirect(admin_url('venues/venue/' . $id . "?lid=" . $postlid));
            } elseif (isset($postpid) && $postpid != "") {
                redirect(admin_url('venues/venue/' . $id . "?pid=" . $postpid));
            } elseif (isset($posteid) && $posteid != "") {
                redirect(admin_url('venues/venue/' . $id . "?eid=" . $posteid));
            } else {
                redirect(admin_url('venues/venue/' . $id));
            }
        }
    }

    /**
     * Added By : Masud
     * Dt : 06/26/2018
     * kanban view for meeting
     */
    public function switch_venues_kanban($set = 0)
    {
        {
            if ($set == 1) {
                $set = 'true';
            } else {
                $set = 'false';
            }

            $this->session->set_userdata(array(
                'venues_kanban_view' => $set
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
        if ($this->input->post()) {
            $data = $this->input->post();
            $lid = $data['hdnlid'];
            $pid = $data['hdnpid'];
            $eid = $data['hdneid'];
            unset($data['hdnlid']);
            unset($data['hdnpid']);
            unset($data['hdneid']);
            $note_id = $this->misc_model->add_note($data, 'venue', $rel_id);
            if ($note_id) {
                set_alert('success', "Venue note added successfully");
                //redirect(admin_url('meetings/meeting/' . $id));
                if ($lid != "") {
                    redirect(admin_url('venues/view/' . $rel_id . '?lid=' . $lid));
                } elseif ($pid != "") {
                    redirect(admin_url('venues/view/' . $rel_id . '?pid=' . $pid));
                } elseif ($eid != "") {
                    redirect(admin_url('venues/view/' . $rel_id . '?eid=' . $eid));
                } else {
                    redirect(admin_url('venues/view/' . $rel_id));
                }
            }
        }
        echo $rel_id;
        exit;
    }

    public function upload_file()
    {
        if ($this->input->post()) {
            $venueid = $this->input->post('venueid');
            $file = handle_venue_attachments($venueid, 'file');
            if ($file) {
                $files = array();
                $files[] = $file;
                //$success = $this->tasks_model->add_attachment_to_database($venueid, $file);
                $success = $this->misc_model->add_attachment_to_database($venueid, 'venueattachment', $file);
                echo $file[0]['file_name'] . ":" . $success;
                die();
            }
        }
    }

    function update_attachment($venueid)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $attachmentid = $data['attachmentid'];
            unset($data['attachmentid']);
            $this->venues_model->update_attachment($data, $attachmentid);
        }
        set_alert('success', _l('updated_successfully', _l('attachemnt')));
        redirect(admin_url('venues/view/' . $venueid));
    }

    function update_loc_attachment($locid, $venueid)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $attachmentid = $data['attachmentid'];
            unset($data['attachmentid']);
            $this->venues_model->update_attachment($data, $attachmentid);
        }
        set_alert('success', _l('updated_successfully', _l('attachemnt')));
        redirect(admin_url('venues/onsitelocview/' . $locid . "?venue=" . $venueid));
    }

    function deletecontact($cid)
    {
        $this->venues_model->deletecontact($cid);
    }

    /*
     * Added By Masud Shaikh
     * On 07/10/2018
     * for On site location
     *
    */
    function onsitelocation($id = "")
    {
        $data = array();
        if ($id == '') {
            $title = _l('add_new', _l('onsiteloc'));
        } else {
            $title = _l('edit', _l('onsiteloc'));
            $data['locaton'] = $this->venues_model->getlocation($id);
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            unset($data['imagebase64']);
            if (isset($_FILES['loccoverimage']) && !empty($_FILES['loccoverimage']['name'])) {
                $data['loccoverimage'] = $_FILES['loccoverimage']['name'];
                $data['loccoverimage'] = str_replace(" ","-",$data['loccoverimage']);
                $data['loccoverimage'] = str_replace("(","",$data['loccoverimage']);
                $data['loccoverimage'] = str_replace(")","",$data['loccoverimage']);
            }
            $success = $this->venues_model->savelocation($data, $id);
            set_alert('success', _l('added_successfully', _l('On-Site Location')));
            redirect(admin_url('venues/view/' . $data['venueid']));
        }
        $venueid = $this->input->get('venue');
        $postvenueid = $this->input->post('venueid');
        $data['venueid'] = $venueid;
        $data['title'] = $title;
        $this->load->view('admin/venues/onsiteloc', $data);
    }

    /*
     * Added By Masud Shaikh
     * On 07/11/2018
     * for On site location
     *
    */

    function deletelocation($id)
    {
        $this->venues_model->deletelocation($id);
    }

    function onsitelocview($id)
    {
        $data = array();
        $title = _l('onsiteloc');
        $data['locaton'] = $this->venues_model->getlocation($id);
        $venueid = $this->input->get('venue');
        $data['venueid'] = $venueid;
        $data['venue'] = $this->venues_model->get($data['locaton']->venueid);
        $data['title'] = $title;
        $data['notes'] = $this->misc_model->get_notes($id, 'location');
        $data['venuecontacts'] = $this->venues_model->get_venue_contact($data['locaton']->venueid);
        $data['venueloccontacts'] = $this->venues_model->get_venue_contact($id, 'venueloc');
        $this->load->view('admin/venues/onsitelocview', $data);
    }

    public function add_loc_note($rel_id)
    {
        if (!is_staff_member()) {
            header("HTTP/1.0 404 Not Found");
            echo _l('access_denied');
            die;
        }
        if ($this->input->post()) {
            $data = $this->input->post();
            $venueid = $this->input->get('venue');

            $note_id = $this->misc_model->add_note($data, 'location', $rel_id);
            if ($note_id) {
                set_alert('success', "On-Site location note added successfully");
                redirect(admin_url('venues/onsitelocview/' . $rel_id."?venue=".$venueid));
            }
        }
        echo $rel_id;
        exit;
    }

    public function upload_loc_file()
    {
        if ($this->input->post()) {
            $venueid = $this->input->post('venueid');
            $locid = $this->input->post('locid');
            $file = handle_venue_loc_attachments($venueid, $locid, 'file');
            if ($file) {
                $files = array();
                $files[] = $file;
                //$success = $this->tasks_model->add_attachment_to_database($venueid, $file);
                $success = $this->misc_model->add_attachment_to_database($locid, 'venuelocfile', $file);
                echo $file[0]['file_name'] . ":" . $success;
                die();
            }
        }
    }

    /* Remove venue cover image / ajax */
    public function remove_loc_cover_image($id = '')
    {
        if (is_numeric($id) && (has_permission('addressbook', '', 'create', true) || has_permission('addressbook', '', 'edit', true))) {
            $locid = $id;
        } else {
            $locid = "";
        }

        $location = $this->venues_model->getlocation($locid);
        if (file_exists(get_upload_path_by_type('venue_locimage') . $locid)) {
            delete_dir(get_upload_path_by_type('venue_locimage') . $locid);
        }

        $this->db->where('locid', $locid);
        $this->db->update('tblvenueloc', array(
            'loccoverimage' => null
        ));
        if ($this->input->is_ajax_request()) {
            return true;
        }
        redirect(admin_url('venues/onsitelocation/' . $locid . '?venue=' . $location->venueid));

    }

    public function delete_note($id)
    {
        if (!is_staff_member()) {
            header("HTTP/1.0 404 Not Found");
            echo _l('access_denied');
            die;
        }
        if ($this->misc_model->delete_note($id)) {
            echo "deleted";
        }
    }

    function add_venue_contact($id,$venueid)
    {
        if ($this->input->post()) {
            $contacts=$this->input->post('contacts');
            foreach ($contacts as $contact){
                $venuecontactdata = [];
                $venuecontactdata['rel_id'] = $id;
                $venuecontactdata['rel_type'] = 'venueloc';

                $venuecontactdata['addressbookid'] = $contact;
                $venuecontactdata['deleted'] = 0;
                $venuecontactdata['created_by'] = $this->session->userdata['staff_user_id'];
                $venuecontactdata['datecreated'] = date('Y-m-d H:i:s');
                $this->venues_model->add_venue_contact($venuecontactdata);
            }
            redirect(admin_url('venues/onsitelocview/' . $id)."?venue=".$venueid);

        }
    }
    function upload_loc_galley(){
        if ($this->input->post()) {
            $venueid = $this->input->post('venueid');
            $locid = $this->input->post('locid');
            $file = handle_venue_loc_attachments($venueid, $locid, 'file');
            if ($file) {
                $files = array();
                $files[] = $file;
                //$success = $this->tasks_model->add_attachment_to_database($venueid, $file);
                $success = $this->misc_model->add_attachment_to_database($locid, 'venuelocgallery', $file);
                echo $file[0]['file_name'] . ":" . $success;
                die();
            }
        }
    }
}