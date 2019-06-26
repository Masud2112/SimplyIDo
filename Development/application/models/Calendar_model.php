<?php

defined('BASEPATH') or exit('No direct script access allowed');
class Calendar_model extends CRM_Model
{
    public function __construct()
    {
        parent::__construct();

    }

	public function get_calendar_data( $start, $end, $client_id = '', $contact_id = '', $filters = false )
    {
        $session_data = get_session_data();

        $brandid = get_user_session();

        $is_admin                     = is_admin();
        $has_permission_invoices      = has_permission('invoices', '', 'view');
        $has_permission_invoices_own  = has_permission('invoices', '', 'view_own');
        $has_permission_estimates     = has_permission('estimates', '', 'view');
        $has_permission_estimates_own = has_permission('estimates', '', 'view_own');
        $has_permission_contracts     = has_permission('contracts', '', 'view');
        $has_permission_contracts_own = has_permission('contracts', '', 'view_own');
        $has_permission_proposals     = has_permission('proposals', '', 'view');
        $has_permission_proposals_own = has_permission('proposals', '', 'view_own');
        $data                         = array();

        $client_data = false;
        if (is_numeric($client_id) && is_numeric($contact_id)) {
            $client_data                      = true;
            $has_contact_permission_invoices  = has_contact_permission('invoices', $contact_id);
            $has_contact_permission_estimates = has_contact_permission('estimates', $contact_id);
            $has_contact_permission_proposals = has_contact_permission('proposals', $contact_id);
            $has_contact_permission_contracts = has_contact_permission('contracts', $contact_id);
            $has_contact_permission_projects  = has_contact_permission('projects', $contact_id);
        }

        $hook_data = array(
            'data' => $data,
            'client_data' => $client_data
        );

        if ($client_data == true) {
            $hook_data['client_id']  = $client_id;
            $hook_data['contact_id'] = $contact_id;
        }

        $hook_data = do_action('before_fetch_events', $hook_data);
        $data      = $hook_data['data'];

        // excluded calendar_filters from post
        $ff = (count($filters) > 1 && isset($filters['calendar_filters']) ? true : false);

        if (get_brand_option('show_invoices_on_calendar') == 1 && !$ff || $ff && array_key_exists('invoices', $filters)) {
            $this->db->select('duedate as date,number,id,clientid,hash,leadid');
            $this->db->from('tblinvoices');
            $this->db->where_not_in('status', array(
                2,
                5
            ));

            $this->db->where('(duedate BETWEEN "' . $start . '" AND "' . $end . '")');
            
            //for sido admin
            if(!empty(get_user_session())) {
                $this->db->where('brandid',get_user_session());
            }

            if ($client_data) {
                $this->db->where('clientid', $client_id);

                if (get_brand_option('exclude_invoice_from_client_area_with_draft_status') == 1) {
                    $this->db->where('status !=', 6);
                }
            } else {
                if (!$has_permission_invoices) {
                    if (is_sido_admin() == 0 && $session_data['user_type'] != 1) {
                        $this->db->where('addedfrom', get_staff_user_id());
                    }
                }
            }
            $invoices = $this->db->get()->result_array();

            foreach ($invoices as $invoice) {
                if (!$has_permission_invoices && !$has_permission_invoices_own && !$client_data) {
                    continue;
                } elseif ($client_data && !$has_contact_permission_invoices) {
                    continue;
                }

                $rel_showcase = '';

                /**
                 * Show company name on calendar tooltip for admins
                 */
                if (!$client_data) {
                    $this->load->model('Addressbooks_model');   
                    $contact = $this->Addressbooks_model->get_contacts($invoice['clientid']);
                    if ($contact) {   
                        $rel_showcase = ' Contact: ' . $contact->firstname . ' ' . $contact->lastname . ' Due Date: ' . _dt($invoice['date'],false);                                        
                    }
                }                    

                $number              = format_invoice_number($invoice['id']);

                $invoice['_tooltip'] = _l('calendar_invoice') . ' - ' . $number . $rel_showcase;
                $invoice['title']    = $number;
                $invoice['type']    = "invoice";
                $invoice['color']    = get_brand_option('calendar_invoice_color');

                if (!$client_data) {
                    if($invoice['leadid'] > 0){
                        $invoice['url'] = admin_url('invoices/list_invoices?lid='. $invoice['leadid'] . '#' . $invoice['id']);
                    } else {
                        $invoice['url'] = admin_url('invoices/list_invoices#' . $invoice['id']);
                    }
                } else {
                    $invoice['url'] = site_url('viewinvoice/' . $invoice['id'] . '/' . $invoice['hash']);
                }

                array_push($data, $invoice);
            }
        }
        if (get_brand_option('show_estimates_on_calendar') == 1  && !$ff || $ff && array_key_exists('estimates', $filters)) {


            $this->db->select('number,id,clientid,hash,CASE WHEN expirydate IS NULL THEN date ELSE expirydate END as date', false);
            $this->db->from('tblestimates');

            $this->db->where('status !=', 3, false);
            $this->db->where('status !=', 4, false);
           // $this->db->where('expirydate IS NOT NULL');

            $this->db->where("CASE WHEN expirydate IS NULL THEN (date BETWEEN '$start' AND '$end') ELSE (expirydate BETWEEN '$start' AND '$end') END",null,false);

            if ($client_data) {
                $this->db->where('clientid', $client_id, false);

                if (get_brand_option('exclude_estimate_from_client_area_with_draft_status') == 1) {
                    $this->db->where('status !=', 1, false);
                }
            } else {
                if (!$has_permission_estimates) {
                    $this->db->where('addedfrom', get_staff_user_id(), false);
                }
            }

            $estimates = $this->db->get()->result_array();

            foreach ($estimates as $estimate) {
                if (!$has_permission_estimates && !$has_permission_estimates_own && !$client_data) {
                    continue;
                } elseif ($client_data && !$has_contact_permission_estimates) {
                    continue;
                }

                $rel_showcase = '';
                if (!$client_data) {
                    $rel_showcase = ' (' . get_company_name($estimate['clientid']) . ')';
                }

                $number               = format_estimate_number($estimate['id']);
                $estimate['_tooltip'] = _l('calendar_estimate') . ' - ' . $number . $rel_showcase;
                $estimate['title']    = $number;
                $estimate['color']    = get_brand_option('calendar_estimate_color');
                if (!$client_data) {
                    $estimate['url'] = admin_url('estimates/list_estimates/' . $estimate['id']);
                } else {
                    $estimate['url'] = site_url('viewestimate/' . $estimate['id'] . '/' . $estimate['hash']);
                }
                array_push($data, $estimate);
            }
        }
        if (get_brand_option('show_proposals_on_calendar') == 1 && !$ff || $ff && array_key_exists('proposals', $filters)) {
            $this->db->select('name,templateid AS id,CASE WHEN valid_date IS NULL THEN issued_date ELSE valid_date END as date',false);
            $this->db->from('tblproposaltemplates');
            // $this->db->where('status !=', 2,false);
            // $this->db->where('status !=', 3,false);
            $this->db->where('deleted', 0);
            $this->db->where('rel_id > 0');
            //for sido admin
            if(!empty(get_user_session())) {
                $this->db->where('brandid', get_user_session());
            }
            $this->db->where("CASE WHEN issued_date IS NULL THEN (valid_date BETWEEN '$start' AND '$end') ELSE (valid_date BETWEEN '$start' AND '$end') END",null,false);

            if ($client_data) {
                $this->db->where('rel_type', 'customer');
                $this->db->where('rel_id', $client_id,false);

                // if(get_brand_option('exclude_proposal_from_client_area_with_draft_status')){
                //     $this->db->where('status !=',6,false);
                // }

            } else {
                if (!$has_permission_proposals) {
                    if (is_sido_admin() == 0 && $session_data['user_type'] != 1) {
                        $this->db->where('created_by', get_staff_user_id(),false);
                    }
                }
            }

            $proposals = $this->db->get()->result_array();
           
            foreach ($proposals as $proposal) {
                if (!$has_permission_proposals && !$has_permission_proposals_own && !$client_data) {
                    continue;
                } elseif ($client_data && !$has_contact_permission_proposals) {
                    continue;
                }

                $proposal['_tooltip'] = _l('proposal');
                $proposal['type'] ="proposal";
                $proposal['title']    = $proposal['name'];
                $proposal['color']    = get_brand_option('calendar_proposal_color');
                if (!$client_data) {
                    $proposal['url'] = admin_url('proposaltemplates/proposal/' . $proposal['id']. '?pg=calendar');
                } else {
                    $proposal['url'] = site_url('proposaltemplates/proposal/' . $proposal['id'] . '?pg=calendar');
                }
                array_push($data, $proposal);
            }
        }

        if (get_brand_option('show_tasks_on_calendar') == 1 && !$ff || $ff && array_key_exists('tasks', $filters)) {

            $this->db->select('name as title,id,rel_id,rel_type,status,CASE WHEN duedate IS NULL THEN startdate ELSE duedate END as date',false);
            $this->db->from('tblstafftasks');
            //$this->db->where('status !=', 5);

            //for sido admin
            if(!empty(get_user_session())) {
                $this->db->where('brandid', get_user_session());
            }

            $this->db->where('deleted', 0);

            //$this->db->where("CASE WHEN duedate IS NULL THEN (startdate BETWEEN '$start' AND '$end') ELSE (duedate BETWEEN '$start' AND '$end') END",null,false);

            $this->db->where("CASE WHEN duedate IS NULL THEN (startdate BETWEEN '$start' AND '$end') ELSE (duedate BETWEEN '$start' AND '$end') END",null,false);            

            if ($client_data) {
                $this->db->where('rel_type', 'project');
                //$this->db->where('rel_id IN (SELECT id FROM tblprojects WHERE clientid='.$client_id.')');
                $this->db->where('rel_id IN (SELECT project_id FROM tblprojectsettings WHERE name="view_tasks" AND value=1)');
                $this->db->where('visible_to_client', 1);
            }

            if (is_sido_admin() == 0 && $session_data['user_type'] != 1) {
                $this->db->where('(id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid = ' . get_staff_user_id() . '))');
            }
            $tasks = $this->db->get()->result_array();

            foreach ($tasks as $task) {
                $rel_showcase = '';                
                if (!empty($task['rel_id']) && !$client_data) {
                    $task_rel_data  = get_relation_data($task['rel_type'], $task['rel_id']);
                    if(!empty($task_rel_data)){
                        $task_rel_value = get_relation_values($task_rel_data, $task['rel_type']);
                        $rel_showcase   = ' Lead: ' . $task_rel_value['name'];
                    }
                }
                $rel_showcase = ' Due Date: ' . _dt($task['date'],false);

                //$rel_showcase .= stripslashes(str_replace('\r\n','<br/>',$rel_showcase));

                $task['date'] = $task['date'];
                $task['type'] = "task";
                $name             = mb_substr($task['title'], 0, 60) . '...';
                $task['_tooltip'] = _l('calendar_task') . ' - ' . $name . $rel_showcase;

                $task['title']    = $name;
                $status = get_task_status_by_id($task['status']);
                $task['color']    = isset($status['color'])?$status['color']:"#000000";

                if (!$client_data) {
                    $task['url']     = admin_url('tasks/dashboard/' . $task['id'].'?pg=calendar');
                } else {
                    $task['url'] = site_url('clients/project/' . $task['rel_id'] . '?pg=calendar&taskid=' . $task['id']);
                }
                array_push($data, $task);
            }
        }

        if (get_brand_option('show_lead_on_calendar') == 1 && !$ff || $ff && array_key_exists('leads', $filters)) {

            $this->db->select('tblleads.name as title,tblleads.id,status,CASE WHEN DATE(tblleads.eventstartdatetime) IS NULL THEN DATE(tblleads.eventenddatetime) ELSE DATE(tblleads.eventstartdatetime) END as date',false);
            $this->db->from('tblleads');
            //$this->db->where('status !=', 5);
            
            //for sido admin
            if(!empty(get_user_session())) {
                $this->db->where('tblleads.brandid', get_user_session());
            }

            $this->db->where('tblleads.deleted', 0);
            
            $this->db->where("CASE WHEN DATE(eventstartdatetime) IS NULL THEN (DATE(eventenddatetime) BETWEEN '$start' AND '$end') ELSE (DATE(eventstartdatetime) BETWEEN '$start' AND '$end') END",null,false);

            if (is_sido_admin() == 0 && $session_data['user_type'] != 1) {
                $this->db->join('tblstaffleadassignee', 'tblstaffleadassignee.leadid = tblleads.id', 'left');
                $this->db->join('tblstaff', 'tblstaff.staffid = tblstaffleadassignee.assigned', 'left');
                $this->db->where('(tblstaffleadassignee.assigned = ' . get_staff_user_id() . ' OR addedfrom=' . get_staff_user_id() . ' OR is_public=1)');
            }
            $this->db->group_by('tblleads.id');
            $leads = $this->db->get()->result_array();
            
            foreach ($leads as $lead) {
                $rel_showcase = ' Due Date: ' . _dt($lead['date'],false);
                
                $lead['date'] = $lead['date'];

                $name             = mb_substr($lead['title'], 0, 60) . '...';
                $lead['_tooltip'] = _l('calendar_lead') . ' - ' . $name . $rel_showcase;
                $lead['title']    = $name;
                $status = get_lead_status_by_id($lead['status']);
                $lead['color']    = $status['color'];
                $lead['id']    = $lead['id'];
                $lead['type']    = 'lead';
                $lead['url']     = admin_url('leads/dashboard/' . $lead['id'].'?pg=calendar');
                
                array_push($data, $lead);
            }
        }
        
        if (get_brand_option('show_meetings_on_calendar') == 1 && !$ff || $ff && array_key_exists('meeting', $filters)) {

            $this->db->select('name as title,meetingid as id ,status,color,CASE WHEN start_date IS NULL THEN end_date ELSE start_date END as date',false);
            $this->db->from('tblmeetings');
            //$this->db->where('status !=', 5);

            //for sido admin
            if(!empty(get_user_session())) {
                $this->db->where('brandid', get_user_session());
            }

            $this->db->where('deleted', 0);
            $this->db->where("CASE WHEN start_date IS NULL THEN (end_date BETWEEN '$start' AND '$end') ELSE (start_date BETWEEN '$start' AND '$end') END",null,false);

            if (is_sido_admin() == 0 && $session_data['user_type'] != 1) {
                $this->db->where('(meetingid IN (SELECT meeting_id FROM tblmeetingusers WHERE user_id = ' . get_staff_user_id() . ') OR created_by='.get_staff_user_id().')');
            }

            $meetings = $this->db->get()->result_array();
            //echo "<pre>";print_r($meetings);
            foreach ($meetings as $meeting) {
                $rel_showcase = ' Due Date: ' . _dt($meeting['date'],false);

                $meeting['date'] = $meeting['date'];

                $name             = mb_substr($meeting['title'], 0, 60) . '...';
                //$meeting['_tooltip'] = _l('calendar_meeting') . ' - ' . $name . $rel_showcase;
                $meeting['_tooltip'] = _l('calendar_meeting') . ' - ' . $name;
                $meeting['title']    = $name;
                //$status = get_meeting_status_by_id($meeting['status']);
                $meeting['color']    = $meeting['color'];

                $meeting['url']     = admin_url('meetings/meeting/' . $meeting['id'].'?pg=calendar');
                $meeting['type']     = "meeting";
                
                array_push($data, $meeting);
            }
        }

        // if (!$client_data) {
        //     $available_reminders = $this->perfex_base->get_available_reminders_keys();
        //     $hideNotifiedReminders = get_brand_option('hide_notified_reminders_from_calendar');
        //     foreach ($available_reminders as $key) {
        //         if (get_brand_option('show_' . $key . '_reminders_on_calendar') == 1  && !$ff || $ff && array_key_exists($key.'_reminders', $filters)) {
        //             $this->db->select('date,description,firstname,lastname,creator,staff,rel_id')
        //             ->from('tblreminders')
        //             ->where('(date BETWEEN "' . $start . '" AND "' . $end . '")')
        //             ->where('rel_type', $key)
        //             ->join('tblstaff', 'tblstaff.staffid = tblreminders.staff');
        //             if($hideNotifiedReminders == '1'){
        //                 $this->db->where('isnotified', 0);
        //             }
        //             $reminders = $this->db->get()->result_array();
        //             foreach ($reminders as $reminder) {
        //                 if ((get_staff_user_id() == $reminder['creator'] || get_staff_user_id() == $reminder['staff']) || $is_admin) {
        //                     $_reminder['title'] = '';

        //                     if (get_staff_user_id() != $reminder['staff']) {
        //                         $_reminder['title'] .= '(' . $reminder['firstname'] . ' ' . $reminder['lastname'] . ') ';
        //                     }

        //                     $name                  = mb_substr($reminder['description'], 0, 60) . '...';

        //                     $_reminder['_tooltip'] = _l('calendar_' . $key . '_reminder') . ' - ' . $name;
        //                     $_reminder['title'] .= $name;
        //                     $_reminder['date']  = $reminder['date'];
        //                     $_reminder['color'] = get_brand_option('calendar_reminder_color');
        //                     if ($key == 'customer') {
        //                         $url = admin_url('clients/client/' . $reminder['rel_id']);
        //                     } elseif ($key == 'invoice') {
        //                         $url = admin_url('invoices/list_invoices#' . $reminder['rel_id']);
        //                     } elseif ($key == 'estimate') {
        //                         $url = admin_url('estimates/list_estimates/' . $reminder['rel_id']);
        //                     } elseif ($key == 'lead') {
        //                         $url = '#';
        //                         $_reminder['onclick'] = 'init_lead('.$reminder['rel_id'].'); return false;';
        //                     } elseif ($key == 'proposal') {
        //                         $url = admin_url('proposals/list_proposals/' . $reminder['rel_id']);
        //                     } elseif ($key == 'expense') {
        //                         $url = 'expenses/list_expenses/' . $reminder['rel_id'];
        //                     }

        //                     $_reminder['url'] = $url;
        //                     array_push($data, $_reminder);
        //                 }
        //             }
        //         }
        //     }
        // }

        if (get_brand_option('show_contracts_on_calendar') == 1 && !$ff || $ff && array_key_exists('contracts', $filters)) {
            $this->db->select('subject as title,dateend,datestart,id,client,content');
            $this->db->from('tblcontracts');
            $this->db->where('trash', 0);
            if ($client_data) {
                $this->db->where('client', $client_id);
                $this->db->where('not_visible_to_client', 0);
            } else {
                if (!$has_permission_contracts) {
                    $this->db->where('addedfrom', get_staff_user_id());
                }
            }

            $this->db->where('(dateend > "' . date('Y-m-d') . '" AND dateend IS NOT NULL AND dateend BETWEEN "' . $start . '" AND "' . $end . '")');
            $this->db->or_where('datestart >"' . date('Y-m-d') . '"');

            $contracts = $this->db->get()->result_array();

            foreach ($contracts as $contract) {
                if (!$has_permission_contracts && !$has_permission_contracts_own && !$client_data) {
                    continue;
                } elseif ($client_data && !$has_contact_permission_contracts) {
                    continue;
                }

                $rel_showcase = '';
                if (!$client_data) {
                    $rel_showcase = ' (' . get_company_name($contract['client']) . ')';
                }

                $name                  = $contract['title'];
                $_contract['title']    = $name;
                $_contract['color']    = get_brand_option('calendar_contract_color');
                $_contract['_tooltip'] = _l('calendar_contract') . ' - ' . $name . $rel_showcase;
                if (!$client_data) {
                    $_contract['url'] = admin_url('contracts/contract/' . $contract['id']);
                } else {
                    if (empty($contract['content'])) {
                        // No url for contracts
                        $_contract['url'] = '#';
                    } else {
                        $_contract['url'] = site_url('clients/contract_pdf/' . $contract['id']);
                    }
                }
                if (!empty($contract['dateend'])) {
                    $_contract['date'] = $contract['dateend'];
                } else {
                    $_contract['date'] = $contract['datestart'];
                }
                array_push($data, $_contract);
            }
        }
        //calendar_project
        if (get_brand_option('show_projects_on_calendar') == 1 && !$ff || $ff && array_key_exists('projects', $filters)) {
            $this->load->model('projects_model');
            //$this->db->select('name as title,id,clientid, CASE WHEN deadline IS NULL THEN start_date ELSE deadline END as date',false);
            $this->db->select('tblprojects.name as title,tblprojects.id,DATE(tblprojects.eventstartdatetime) as date,DATE(tblprojects.eventenddatetime) as end_date',false);
            $this->db->from('tblprojects');

            // Exclude cancelled and finished
            // $this->db->where('status !=',4);
            // $this->db->where('status !=',5);

            //$this->db->where("CASE WHEN deadline IS NULL THEN (start_date BETWEEN '$start' AND '$end') ELSE (deadline BETWEEN '$start' AND '$end') END",null,false);

            $this->db->where("DATE(tblprojects.eventstartdatetime) BETWEEN '$start' AND '$end'",null,false);


            /*if ($client_data) {
                $this->db->where('clientid', $client_id);
            }*/

            if (is_sido_admin() == 0 && $session_data['user_type'] != 1) {
                $this->db->join('tblstaffprojectassignee', 'tblstaffprojectassignee.projectid = tblprojects.id', 'left');
                $this->db->join('tblstaff', 'tblstaff.staffid = tblstaffprojectassignee.assigned', 'left');
                $this->db->join('tblprojectcontact', 'tblprojectcontact.projectid = tblprojects.id AND tblprojectcontact.active=1', 'left');
                $this->db->where('(tblstaffprojectassignee.assigned = ' . get_staff_user_id() . ' OR addedfrom =' . get_staff_user_id() . ' OR tblprojectcontact.contactid = ' . get_staff_user_id() . ')');
                //$this->db->where('assigned', get_staff_user_id());
            }

            //for sido admin
            if(!empty(get_user_session())) {
                $this->db->where('tblprojects.brandid', get_user_session());
            }
            $this->db->group_by('tblprojects.id');
            $projects = $this->db->get()->result_array();
            
            foreach ($projects as $project) {
                $rel_showcase = '';

                /*if (!$client_data) {
                    if (!$this->projects_model->is_member($project['id']) && !$is_admin) {
                        continue;
                    }

                    //$rel_showcase = ' (' . get_company_name($project['clientid']) . ')';

                } else {
                    if (!$has_contact_permission_projects) {
                        continue;
                    }
                }*/
                $rel_showcase .= ' Due Date: ' . _dt($project['end_date'],false);

                $_project['id']    = $project['id'];
                $_project['type']    = 'project';
                $name                 = $project['title'];
                $_project['title']    = $name;
                $_project['color']    = get_brand_option('calendar_project_color');
                $_project['_tooltip'] = _l('calendar_project') . ' - ' . $name . $rel_showcase;
                if (!$client_data) {
                    $_project['url'] = admin_url('projects/dashboard/' . $project['id'].'?pg=calendar');
                } else {
                    $_project['url'] = site_url('clients/project/' . $project['id'].'?pg=calendar');
                }

                 $_project['date'] = $project['date'];
                 //var_dump($_project);
                array_push($data, $_project);
            }//die;
        }
        // if (!$client_data && !$ff || (!$client_data && $ff && array_key_exists('events', $filters))) {
        //     $events = $this->get_all_events($start,$end);
        //     foreach ($events as $event) {
        //         if ($event['userid'] != get_staff_user_id() && !$is_admin) {
        //             $event['is_not_creator'] = true;
        //             $event['onclick']        = true;
        //         }
        //         $event['_tooltip'] = _l('calendar_event') . ' - ' . $event['title'];
        //         $event['color']    = $event['color'];
        //         array_push($data, $event);
        //     }
        // }
        //echo "<pre>";print_r($data);exit;
        return $data;
    }

    function get_calendar_event_data($data){
        $type=$data['type'];
        $id=$data['id'];
        $where = "id";
        if($type=="proposal"){
            $tbl="tblproposaltemplates";
            $where = "templateid";
        }elseif ($type=="project"){
            $tbl="tblprojects";
        }elseif ($type=="lead"){
            $tbl="tblleads";
        }elseif ($type=="meeting"){
            $tbl="tblmeetings";
            $where = "meetingid";
        }elseif ($type=="invoice"){
            $tbl="tblinvoices";
        }elseif ($type=="task"){
            $tbl="tblstafftasks";
        }
        /*if($type=="project" || $type=="lead"){
            $this->db->select('*,(SELECT venuename from tblvenue where venueid='.$tbl.'.venueid) as venuename")');
        }else{
            $this->db->select('*');
        }*/
        $this->db->select('*');
        $this->db->from($tbl);
        $this->db->where($where, $id);
        $result = $this->db->get()->row();
        $venuename="";
        if(isset($result->venueid) && $result->venueid > 0 ){
            $this->db->from('tblvenue');
            $this->db->where('venueid', $result->venueid);
            $venuename = $this->db->get()->row()->venuename;
            $result->venuename=$venuename;
        }
        if(isset($result->assigned) && $result->assigned > 0 ){
            $this->db->from('tblstaff');
            $this->db->where('staffid', $result->assigned);
            $staff = $this->db->get()->row();
            $firstname = $staff->firstname;
            $lastname = $staff->lastname;
            $result->firstname=$firstname;
            $result->lastname=$lastname;
        }
        $result->type=$type;
        return $result;
    }

}