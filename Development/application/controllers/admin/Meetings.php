<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Meetings extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        // Model is autoloaded

        $this->load->model('addressbooks_model');
    }

    /* List all staff meetings */
    public function index()
    {
        $pg = $this->input->get('pg');

        if (!has_permission('meetings', '', 'view', true)) {
            access_denied('meetings');
        }

        //Added By Avni on 10/30 for loading lead name by lead id.

        $data['title'] = _l('all_meetings');
        if ($this->input->get('lid')) {
            $leadid = $this->input->get('lid');

            $this->load->model('leads_model');

            $data['lid'] = $leadid;
            $data['lname'] = '';
            if ($leadid != "") {
                $data['lname'] = $this->leads_model->get($leadid)->name;
            }
        }
        if ($this->input->get('pid')) {
            $projectid = $this->input->get('pid');

            $this->load->model('projects_model');

            $data['pid'] = $projectid;
            $data['lname'] = '';
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        }
        if ($this->input->get('eid')) {
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
        $data['pg'] = $pg;
        $data['switch_meetings_kanban'] = true;
        if ($this->session->has_userdata('meetings_kanban_view') && $this->session->userdata('meetings_kanban_view') == 'true') {
            $data['switch_meetings_kanban'] = false;
            $data['bodyclass'] = 'kan-ban-body';
        }


        /*if(is_mobile()){
            $this->session->set_userdata(array(
            'meetings_kanban_view' => 0
            ));
        }*/

        if ($this->input->is_ajax_request()) {
            if ($this->input->get('kanban')) {
                $data['kanban'] = $this->input->get('kanban');
                if ($this->input->get('limit')) {
                    $data['limit'] = $this->input->get('limit');
                }
                if ($this->input->get('page')) {
                    $data['page'] = $this->input->get('page');
                }
                if ($this->input->get('search')) {
                    $data['search'] = $this->input->get('search');
                }
                $data['statuses'] = $this->meetings_model->get_meeting_status();
                echo $this->load->view('admin/meetings/kan-ban', $data, true);
                die();
            } else {
                $this->perfex_base->get_table_data('meetings');
            }
        }
        $this->load->view('admin/meetings/manage', $data);
    }

    /* Add new meeting or edit existing one */
    public function meeting($id = '')
    {
        $lid = $this->input->get('lid');
        $pid = $this->input->get('pid');
        $eid = $this->input->get('eid');
        $pg = $this->input->get('pg');
        if (!has_permission('meetings', '', 'view')) {
            access_denied('meetings');
        }

        if ($this->input->post()) {
            $data = $this->input->post();
            $postlid = $this->input->post('hdnlid');
            $postpid = $this->input->post('hdnpid');
            $posteid = $this->input->post('hdneid');
            $pg = $this->input->post('pg');
            $meeting_note_data = array();
            $meeting_note_data['description'] = isset($data['note_description'])?$data['note_description']:"";
            unset($data['note_description']);
            /*echo "<pre>";
            print_r($data);
            print_r($meeting_note_data);
            die('<----here');*/
            if ($id == '') {
                if (!has_permission('meetings', '', 'create', true)) {
                    access_denied('meetings');
                }

                $id = $this->meetings_model->add($data);
                if ($id) {
                    if ($meeting_note_data['description'] != "") {
                        $note_id = $this->misc_model->add_note($meeting_note_data, 'meeting', $id);
                    }
                    set_alert('success', _l('added_successfully', _l('meeting')));

                    if (isset($postlid) && $postlid != "") {
                        redirect(admin_url('meetings/' . "?lid=" . $postlid));
                    } else if (isset($postpid) && $postpid != "") {
                        redirect(admin_url('meetings/' . "?pid=" . $postpid));
                    } else if (isset($posteid) && $posteid != "") {
                        redirect(admin_url('meetings/' . "?eid=" . $posteid));
                    } elseif (isset($pg) && $pg == "calendar") {
                        redirect(admin_url('calendar'));
                    } elseif (isset($pg) && $pg == "home") {
                        redirect(admin_url());
                    } else {
                        redirect(admin_url('meetings/meeting/' . $id));
                    }
                } else {
                    set_alert('danger', _l('problem_meeting_adding', _l('meeting_lowercase')));
                    if (isset($postlid) && $postlid != "") {
                        redirect(admin_url('meetings/meeting/' . $id . "?lid=" . $postlid));
                    } elseif (isset($postpid) && $postpid != "") {
                        redirect(admin_url('meetings/meeting/' . $id . "?pid=" . $postpid));
                    } elseif (isset($posteid) && $posteid != "") {
                        redirect(admin_url('meetings/meeting/' . $id . "?eid=" . $posteid));
                    } elseif (isset($pg) && $pg == "calendar") {
                        redirect(admin_url('calendar'));
                    } elseif (isset($pg) && $pg == "home") {
                        redirect(admin_url());
                    } else {
                        //redirect(admin_url('meetings/meeting/' . $id));
                        redirect(admin_url('meetings/'));
                    }
                }

            } else {
                if (!has_permission('meetings', '', 'edit', true)) {
                    access_denied('meetings');
                }

                $success = $this->meetings_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('meeting')));
                    if (isset($postlid) && $postlid != "") {
                        redirect(admin_url('meetings/' . "?lid=" . $postlid));
                    } elseif (isset($postpid) && $postpid != "") {
                        redirect(admin_url('meetings/' . "?pid=" . $postpid));
                    } elseif (isset($posteid) && $posteid != "") {
                        redirect(admin_url('meetings/' . "?eid=" . $posteid));
                    } elseif (isset($pg) && $pg != "") {
                        redirect(admin_url('calendar'));
                    } else {
                        redirect(admin_url('meetings/'));
                    }
                } else {
                    set_alert('danger', _l('problem_meeting_updating', _l('meeting_lowercase')));
                    if (isset($postlid) && $postlid != "") {
                        redirect(admin_url('meetings/meeting/' . $id . "?lid=" . $postlid));
                    } elseif (isset($postpid) && $postpid != "") {
                        redirect(admin_url('meetings/meeting/' . $id . "?pid=" . $postpid));
                    } elseif (isset($posteid) && $posteid != "") {
                        redirect(admin_url('meetings/meeting/' . $id . "?eid=" . $posteid));
                    } elseif (isset($pg) && $pg != "") {
                        redirect(admin_url('calendar'));
                    } else {
                        redirect(admin_url('meetings/meeting/' . $id));
                    }
                }
                if (isset($postlid) && $postlid != "") {
                    redirect(admin_url('meetings/meeting/' . $id . "?lid=" . $postlid));
                } elseif (isset($postpid) && $postpid != "") {
                    redirect(admin_url('meetings/meeting/' . $id . "?pid=" . $postpid));
                } elseif (isset($posteid) && $posteid != "") {
                    redirect(admin_url('meetings/meeting/' . $id . "?eid=" . $posteid));
                } elseif (isset($pg) && $pg != "") {
                    redirect(admin_url('calendar'));
                } else {
                    redirect(admin_url('meetings/meeting/' . $id));
                }
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('meeting'));
        } else {
            // $data['meeting']->users = $data['meeting']->contacts = $data['meeting']->leads = [];
            $meeting = $this->meetings_model->get($id);
            $data['meeting'] = $meeting;
            $data['meeting']->users = $this->meetings_model->get_meeting_users($id, 'user_id');
            $data['meeting']->contacts = $this->meetings_model->get_meeting_users($id, 'contact_id');
            //$data['meeting']->leads     = $this->meetings_model->get_meeting_users($id,'lead_id');
            $data['meeting']->reminders = $this->meetings_model->get_meeting_usersreminder($id);
            $data['notes'] = $this->misc_model->get_notes($id, 'meeting');

            /**
             * Added By : Vaidehi
             * Dt : 11/16/2017
             * to get offset for default timezone of application
             */
            $offsetobj = $this->misc_model->get_timezoneoffset();
            $offset = explode(':', $offsetobj->timezoneoffset);
            $data['timeoffset'] = $offset[0];

            $title = _l('edit', _l('meeting')) . ' ' . $meeting->name;
        }
        $data['users'] = $this->meetings_model->get_users();
        if (isset($lid) && $lid != "") {
            $data['contacts'] = $this->meetings_model->get_contacts($lid);
        } elseif (isset($pid) && $pid != "") {
            $data['contacts'] = $this->meetings_model->get_contacts("", $pid);
        } elseif (isset($eid) && $eid != "") {
            $data['contacts'] = $this->meetings_model->get_contacts("", "", $eid);
        } else {
            $data['contacts'] = $this->meetings_model->get_contacts();
        }

        $data['leads'] = $this->meetings_model->get_leads();
        $data['projects'] = $this->meetings_model->get_projects();
        $data['events'] = $this->meetings_model->get_events($pid);
        $data['meeting_status'] = $this->meetings_model->get_meeting_status();
        //$data['durations']      = get_meeting_duration();
        $data['reminders'] = get_meeting_reminders();
        $data['title'] = $title;
        $data['lid'] = $this->input->get('lid');
        $data['pid'] = $this->input->get('pid');
        $data['eid'] = $this->input->get('eid');
        $data['pg'] = $this->input->get('pg');
        if ($data['lid']) {
            $this->load->model('leads_model');
            $data['lname'] = '';
            if ($data['lid'] != "") {
                $data['lname'] = $this->leads_model->get($data['lid'])->name;
            }
        } elseif ($data['pid']) {
            $projectid = $this->input->get('pid');

            $this->load->model('projects_model');
            $data['parent_id'] = $this->projects_model->get($projectid)->parent;
            $data['pid'] = $projectid;
            $data['lname'] = '';
            if ($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        } elseif ($data['eid']) {
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
        $data['locations'] = $this->meetings_model->get_locations();
        $this->load->view('admin/meetings/meeting', $data);
    }

    /* Delete staff meeting from database */
    public function delete($id)
    {
        if (!has_permission('meetings', '', 'delete', true)) {
            access_denied('meetings');
        }
        $lid = $this->input->get('lid');
        if (!$id) {
            redirect(admin_url('meetings'));
        }
        $response = $this->meetings_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('meeting_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('meeting')));
        } else {
            set_alert('danger', _l('problem_deleting', _l('meeting_lowercase')));
        }
        // if(isset($lid)) {
        //     redirect(admin_url('meetings?lid=' . $lid));
        // }
        // else {
        //     redirect(admin_url('meetings'));
        // }
    }

    /* Add new meeting note */
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
            $note_id = $this->misc_model->add_note($data, 'meeting', $rel_id);
            if ($note_id) {
                set_alert('success', "Meeting note added successfully");
                //redirect(admin_url('meetings/meeting/' . $id));
                if ($lid != "") {
                    redirect(admin_url('meetings/meeting/' . $rel_id . '?lid=' . $lid));
                } elseif ($pid != "") {
                    redirect(admin_url('meetings/meeting/' . $rel_id . '?pid=' . $pid));
                } elseif ($eid != "") {
                    redirect(admin_url('meetings/meeting/' . $rel_id . '?eid=' . $eid));
                } else {
                    redirect(admin_url('meetings/meeting/' . $rel_id));
                }
            }
        }
        echo $rel_id;
        exit;
    }

    public function delete_note($id, $projectid)
    {
        if (!is_staff_member()) {
            header("HTTP/1.0 404 Not Found");
            echo _l('access_denied');
            die;
        }
        echo json_encode(array(
            'success' => $this->misc_model->delete_project_note($id, $projectid)
        ));
    }

    /**
     * Added By: Vaidehi
     * Dt: 04/19/2018
     * to get meeting end date as start date plus one hour
     */
    public function getmeetingendate()
    {
        $startdate = $this->input->post('startdate');

        $startdate = str_replace('/', '-', $startdate);
        if (preg_match("/^(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])-[0-9]{4}$/", $startdate)) {
            $startdate = ((isset($startdate) && !empty($startdate)) ? date("Y-m-d H:i:s", strtotime($startdate)) : "");
        } else {
            if (get_brand_option('dateformat') == 'd/m/Y|%d/%m/%Y') {
                $startdate = ((isset($startdate) && !empty($startdate)) ? date("Y-m-d H:i:s", strtotime(str_replace('/', '-', $startdate))) : "");
            } else {
                $startdate = str_replace('-', '/', $startdate);
                $startdate = ((isset($startdate) && !empty($startdate)) ? date("Y-m-d H:i:s", strtotime($startdate)) : "");
            }
        }
        $convertedTime = date('Y-m-d H:i:s', strtotime('+1 hour', strtotime($startdate)));
        if (get_brand_option('time_format') == '12') {
            $convertedTime = date('m/d/Y g:i A',strtotime('+1 hour',strtotime($startdate)));
        }else{
            $convertedTime = date('m/d/Y G:i',strtotime('+1 hour',strtotime($startdate)));
        }
        echo _dt($convertedTime,true);
        //echo substr(_dt($convertedTime, true), 0, -3);
        die();
    }

    /**
     * Added By : Masud
     * Dt : 06/11/2018
     * kanban view for meeting
     */
    public function switch_meetings_kanban($set = 0)
    {
        if ($set == 1) {
            $set = 'true';
        } else {
            $set = 'false';
        }

        $this->session->set_userdata(array(
            'meetings_kanban_view' => $set
        ));

        redirect($_SERVER['HTTP_REFERER']);
    }

    /**
     * Added By: Masud
     * Dt: 06/21/2018
     * for pinned Meeting
     */
    public function pinmeeting()
    {
        $meeting_id = $this->input->post('meeting_id');

        $pindata = $this->meetings_model->pinmeeting($meeting_id);

        echo $pindata;
        exit;
    }

    public function bulk_action()
    {
        $postData = $this->input->post('ids');
        $meetingdata = $this->meetings_model->maskdelete($postData);
        echo $meetingdata;
        exit;
    }

    function get_location($id)
    {
        $location = $this->meetings_model->get_location($id);
        echo json_encode($location);
        die();
    }

    /*
    Start Code
    Added by Munir
    Dt:11/26/2018
    */
    function addloc()
    {
        $locationid = $this->meetings_model->addloc();
        if ($locationid) {
            echo $locationid;
        } else {
            echo 'error';
        }
        die();
    }

    function editloc()
    {
        $location = $this->meetings_model->editloc();
        if ($location > 0) {
            echo 1;
        } else {
            echo 0;
        }
        die();
    }

    /*
    End Code
    Added by Munir
    Dt:11/26/2018
    */
    function deleteloc($id)
    {
        $location = $this->meetings_model->deleteloc($id);
        if ($location > 0) {
            echo 1;
        } else {
            echo 0;
        }
        die();
    }

}