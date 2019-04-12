<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 17-12-2018
 * Time: 05:20 PM
 */
if (isset($_GET['lid'])) {
    $rel_id = $_GET['lid'];
    $rel_link = '?lid=' . $rel_id;

} elseif (isset($_GET['pid'])) {
    $rel_id = $_GET['pid'];
    $rel_link = '?pid=' . $rel_id;
} else {
    $rel_link = '';
}
$session_data= $_SESSION;
if (isset($session_data['is_sido_admin'])) {
    $is_sido_admin = $session_data['is_sido_admin'];
    $is_admin = $session_data['is_admin'];
} else {
    $is_sido_admin = 1;
    $is_admin = 1;
}
$other_token = array();
if (!is_staff_logged_in() || is_client_logged_in()) {
    $other_token['emailsignature'] = get_option('email_signature');
} else {
    $this->db->select('email_signature')->from('tblstaff')->where('staffid', get_staff_user_id());
    $signature = $this->db->get()->row()->email_signature;
    if (empty($signature)) {
        if ($is_sido_admin == 0 && $is_admin == 0) {
            $other_token['emailsignature'] = get_brand_option('email_signature');
        } else {
            $other_token['emailsignature'] = get_option('email_signature');
        }
    } else {
        $other_token['emailsignature'] = $signature;
    }
}

$logo_width = do_action('merge_field_logo_img_width', '');
$image_url = base_url('uploads/company/' . get_brand_option('company_logo'));
$other_token['logoimage'] = '<img draggable="false" src="' . base_url('uploads/brands/' . get_brand_option('company_logo')) . '"' . ($logo_width != '' ? ' width="' . $logo_width . '"' : '') . ' >';

$other_token['portalurl'] = admin_url();
$other_token['crmurl'] = site_url();
$other_token['adminurl'] = admin_url();
$other_token['clienturl'] = site_url();

if ($is_sido_admin == 0 && $is_admin == 0) {
    $other_token['maindomain'] = get_brand_option('main_domain');
    $other_token['companyname'] = get_brand_option('companyname');
} else {
    $other_token['maindomain'] = get_option('main_domain');
    $other_token['companyname'] = get_option('companyname');
}

if (isset($rel_content)) {
    $rel_content = (array)$rel_content;
}
if (isset($_GET['lid']) || isset($_GET['pid'])) {
    if (isset($_GET['lid'])) {
        $rel_type = "lead";
        $rel_id = $_GET['lid'];
    } else {
        $rel_type = "project";
        $rel_id = $_GET['pid'];
    }
}
if (isset($proposal)) {
    /*echo "<pre>";
    print_r($proposal);
    die('<--here');*/
    $sections = json_decode($proposal->sections, true);
    $section = $sections['agreemnet'];
    $signatures = json_decode($proposal->signatures, true);
    $removed_sections = json_decode($proposal->removed_sections, true);
    $token_proposal = array();
    $token_proposal['proposalversion'] = $proposal->proposal_version;
    $token_proposal['proposalname'] = $proposal->name;
    $token_proposal['proposalopentill'] = $proposal->valid_date;
    $token_proposal['proposallink'] = "<a draggable='false' class = 'btn btn-info' href='#proposal_link' style='border-radius: 4px; padding: 8px 12px; text-transform: uppercase; font-size: 14px; outline-offset: 0; transition: all .15s ease-in-out; -o-transition: all .15s ease-in-out; -moz-transition: all .15s ease-in-out; -webkit-transition: all .15s ease-in-out; font-weight: 700; border: 1px solid; min-height: 38px; color: #fff; background-color: #5bc0de; border-color: #46b8da; text-decoration: none; outline: none;'><i class='fa fa-file-text-o mright5'></i>" . $proposal->name . "</a>";
    $rel_type = isset($proposal->rel_type) ? $proposal->rel_type : "";
    $rel_id = isset($proposal->rel_id) ? $proposal->rel_id : "";
}
if ((isset($rel_type) && $rel_type == "lead") || isset($_GET['lid'])) {
    $rel_type = "lead";
    $lead_contents = isset($rel_content) ? $rel_content : "";
    if (!empty($lead_contents)) {
        $d1 = new DateTime($lead_contents['eventstartdatetime']);
        $d2 = new DateTime($lead_contents['eventenddatetime']);
        $interval = $d1->diff($d2);
        $totalhours = ($interval->days * 24) + $interval->h;
        //$hourprefix = $totalhours > 1 ?"(s)":"";
        $lead_token = array();
        $lead_token['leadname'] = $lead_contents['name'];
        $lead_token['leadstatus'] = $lead_contents['status_name'];
        $lead_token['leadsource'] = $lead_contents['source_name'];
        $lead_token['leadlink'] = admin_url('leads/dashboard/' . $lead_contents['id']);
        //$lead_token['leadassigned'] = get_staff_full_name($lead_contents['assigned'][0]);
        $lead_token['leadassigned'] = !empty($lead_contents['assigned']) ? get_staff_full_name($lead_contents['assigned'][0]) : "";
        $lead_token['leadstartdate'] = _dt($lead_contents['eventstartdatetime']);
        $lead_token['leadenddate'] = _dt($lead_contents['eventenddatetime']);
        $lead_token['leadstarttime'] = date('h:i A', strtotime($lead_contents['eventstartdatetime']));
        $lead_token['leadendtime'] = date('h:i A', strtotime($lead_contents['eventenddatetime']));
        $lead_token['leadtotaltime'] = $totalhours . "hour(s)";
        $lead_token['leadtype'] = $lead_contents['eventtypename'];
        $lead_token['leaddashboardbutton'] = "<a  draggable='false' style='border-radius: 4px; padding: 8px 12px; text-transform: uppercase; font-size: 14px; outline-offset: 0; transition: all .15s ease-in-out; -o-transition: all .15s ease-in-out; -moz-transition: all .15s ease-in-out; -webkit-transition: all .15s ease-in-out; font-weight: 700; border: 1px solid; min-height: 38px; color: #fff; background-color: #5bc0de; border-color: #46b8da; text-decoration: none; outline: none;' href='" . admin_url('leads/dashboard/' . $lead_contents['id']) . "'>Lead Dashboard</a>";
    }

}
if ((isset($rel_type) && $rel_type == "project") || isset($_GET['pid'])) {
    $rel_type = "project";
    $project_contents = isset($rel_content) ? $rel_content : "";
    /*echo "<pre>";
    print_r($project_contents);
    die();*/
    if (!empty($project_contents)) {
        $d1 = new DateTime($project_contents['eventstartdatetime']);
        $d2 = new DateTime($project_contents['eventenddatetime']);
        $interval = $d1->diff($d2);
        $totalhours = ($interval->days * 24) + $interval->h;
        //$hourprefix = $totalhours > 1 ?"(s)":"";
        $project_token = array();
        $project_token['projectname'] = $project_contents['name'];
        $project_token['projectdescription'] = $project_contents['status_name'];
        $project_token['projectdeadline'] = _dt($project_contents['eventenddatetime']);
        $project_token['projectlink'] = admin_url('projects/dashboard/' . $project_contents['id']);
        $project_token['projectassigned'] = !empty($project_contents['assigned']) ? get_staff_full_name($project_contents['assigned'][0]) : "";
        $project_token['projectstartdate'] = _dt($project_contents['eventstartdatetime']);
        $project_token['projectenddate'] = _dt($project_contents['eventenddatetime']);
        $project_token['projectstarttime'] = date('h:i A', strtotime($project_contents['eventstartdatetime']));
        $project_token['projectendtime'] = date('h:i A', strtotime($project_contents['eventenddatetime']));
        $project_token['projecttotaltime'] = $totalhours . "hour(s)";
        $project_token['projectbudget'] = format_money($project_contents['budget']);
        $project_token['projectsource'] = $project_contents['source_name'];
        $project_token['projecttype'] = $project_contents['eventtypename'];
        $project_token['projectdashboardbutton'] = "<a draggable='false' style='border-radius: 4px; padding: 8px 12px; text-transform: uppercase; font-size: 14px; outline-offset: 0; transition: all .15s ease-in-out; -o-transition: all .15s ease-in-out; -moz-transition: all .15s ease-in-out; -webkit-transition: all .15s ease-in-out; font-weight: 700; border: 1px solid; min-height: 38px; color: #fff; background-color: #5bc0de; border-color: #46b8da; text-decoration: none; outline: none;' href='" . admin_url('projects/dashboard/' . $project_contents['id']) . "'>Project Dashboard</a>";
        /*$project_token['projectimage'] = '<img src="' . base_url('uploads/project_profile_images/' . $project_contents['id'].'/thumb_'.$project_contents['project_profile_image']) .'">';*/

        $project_token['projectimage'] = "";

    }
}
if (isset($rel_type) && $rel_id > 0) {
    $this->db->select('meetingid, name,start_date as startdate, end_date as enddate, description as description,default_timezone as timezone');
    $this->db->where('rel_type', $rel_type);
    $this->db->where('rel_id', $rel_id);
    $meetings = $this->db->get('tblmeetings')->result();
    if (count($meetings) > 0 && !empty($meetings)) {
        foreach ($meetings as $meeting) {
            $assignees = get_meeting_assignee($meeting->meetingid);
            if (!empty($assignees)) {
                $meeting_members = implode(', ', $assignees['member']);
                $meeting_clients = implode(', ', $assignees['client']);
                $meeting->attendees = " ";
                if (!empty($meeting_members)) {
                    $meeting->attendees .= "Members: " . $meeting_members;
                }
                if (!empty($meeting_clients)) {
                    $meeting->attendees .= " Clients:" . $meeting_clients;
                }
            }
        }
        $meetings = (array)$meetings;
    }

}
$merge_fields = array();
$template = get_email_template_for_sending("proposal-send-to-customer", get_staff_email(get_staff_user_id()));
$merge_fields = array_merge($merge_fields, get_proposal_merge_fields($proposal->templateid));
$merge_fields['{proposal_link}'] = "#proposal_link";
/*$merge_fields['{assigned_mail}'] = "mailto:" . $assigned_mail;*/
$merge_fields['{proposal_event_name}'] = $rel_content['name'];
$merge_fields['{events_detail}'] = "<br /><br /><b>Event Name: " . $rel_content['name'] . "</b><br /><b>Event Type: " . $rel_content['eventtypename'] . "</b><br /><b>Event Date & Time: " . $rel_content['eventstartdatetime'] . "</b><br /><br />";
$merge_fields['{proposal_proposal_to}'] = "";
$template = parse_email_template($template, $merge_fields);
$message = $template->message;
/*echo "<pre>";
print_r($template->message);
die('<--here');*/
$this->load->view('proposaltemplates/includes/head'); ?>
<?php $available_merge_fields = get_agreement_merge_fields(); ?>
    <div class="wrapper">
        <div class="content">
            <div class="emailform">
                <form id="createemail" method="post"
                      action="<?php echo site_url('proposal/createemail/') . $proposal->templateid . $rel_link; ?>">
                    <div class="emailinputs">
                        <?php $this->load->view('proposaltemplates/psl_section_head', array('title' => "send proposal")); ?>
                        <div class="cmail form-group">
                            <div class="row">
                                <div class="col-sm-1 text-right"><label for="emailto"><?php echo _l('to') ?></label>
                                </div>
                                <div class="col-sm-11"><input name="emailto" type="text" class="form-control"
                                                              id="emailto" value="<?php echo $signermails ?>" required/>
                                </div>
                            </div>
                        </div>
                        <div class="cmail form-group">
                            <div class="row">
                                <div class="col-sm-1 text-right"><label for="emailcc"><?php echo _l('cc') ?></label>
                                </div>
                                <div class="col-sm-11"><input name="emailcc" type="text" class="form-control"
                                                              id="emailcc"/></div>
                            </div>
                        </div>
                        <div class="cmail form-group">
                            <div class="row">
                                <div class="col-sm-1 text-right"><label
                                            for="emailsubject"><?php echo _l('subject') ?></label></div>
                                <div class="col-sm-11"><input name="emailsubject" type="text" class="form-control"
                                                              id="emailsubject" required/></div>
                            </div>


                        </div>
                        <div class="form-group agreement-page">
                            <div class="mbot10 pull-right">
                                <a href="javascript:void(0)" class="btn btn-info token_btn"
                                   data-pid="#agreement_tokens">Tokens</a>
                            </div>
                            <div id="agreement_tokens" class="available_merge_fields_container">
                                <div class="clearfix"></div>
                                <?php ?>
                                <div class="token_groups-main panel_s m0">
                                    <div class="token_groups">
                                        <?php
                                        foreach ($available_merge_fields as $key => $value) {
                                            $org_token_group = strtolower($key);
                                            $fin_token_value = str_replace(" ", "", $org_token_group);
                                            ?>
                                            <a href="javascript:void(0)"
                                               class="btn btn-info  <?php echo $fin_token_value; ?>"
                                               data-pid="<?php echo $fin_token_value; ?>"><?php echo ucfirst($key); ?></a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php
                                foreach ($available_merge_fields as $key => $value) {
                                    $org_token_group = strtolower($key);
                                    $fin_token_value = str_replace(" ", "", $org_token_group);
                                    $parent = "";
                                    if ($fin_token_value == "teammember") {
                                        $parent = "Member";
                                    } elseif ($fin_token_value == "clients") {
                                        $parent = "Client";
                                    } elseif ($fin_token_value == "meetings") {
                                        $parent = "Meeting";
                                    } else {
                                        $parent = "";
                                    }
                                    ?>
                                    <div class="tag-group-container tags_<?php echo $fin_token_value; ?>">
                                        <?php foreach ($value as $key1 => $new_value) {
                                            if (isset($_GET['lid']) || isset($_GET['pid'])) {
                                                $merge_key = strtolower($new_value['name']);
                                                $merge_key = str_replace(" ", "", $merge_key);
                                                $val = isset($merge_fields[$merge_key]) ? $merge_fields[$merge_key] : $new_value['name'];
                                            } else {
                                                $val = $new_value['name'];
                                            }
                                            $val = str_replace(" ", "", $val);
                                            if (isset($clients) && !empty($clients) && $fin_token_value == "clients") {
                                                if ($new_value['name'] == 'Fullname') {
                                                    $new_value['name'] = 'name';
                                                }
                                                if (count($clients) > 1) {
                                                    ?>
                                                    <select class="tokenDrpdwn selectpicker mbot10">
                                                        <option value=""><?php echo $new_value['name']; ?></option>
                                                        <?php foreach ($clients as $client) {
                                                            if (isset($client[strtolower($new_value['name'])]) && $client[strtolower($new_value['name'])] != "") { ?>
                                                                <option value="<?php echo $client[strtolower($new_value['name'])] ?>"><?php echo $client[strtolower($new_value['name'])] ?></option>
                                                            <?php }
                                                        } ?>
                                                    </select>
                                                <?php } else {
                                                    $client = $clients[0]; ?>
                                                    <div class="tags <?php echo $fin_token_value; ?>">
                                            <span>
                                                <span draggable="true" class="add_merge_field"
                                                      data-val="<?php echo $client[strtolower($new_value['name'])]; ?>">
                                                    <?php echo $client[strtolower($new_value['name'])] ? "<b>" . $new_value['name'] . "</b> : " . $client[strtolower($new_value['name'])] : "<b>" . $new_value['name'] . "</b>"; ?>
                                                </span>
                                            </span>
                                                    </div>
                                                <?php } ?>
                                            <?php } elseif (isset($rel_content['assigned']) && count($rel_content['assigned']) > 0 && $fin_token_value == "teammember") {
                                                if (count($rel_content['assigned']) == 1) {
                                                    $this->db->where('staffid', $rel_content['assigned'][0]);
                                                    $member = $this->db->get('tblstaff')->row();
                                                    if (!empty($member)) {
                                                        $member = (array)$member;
                                                        $member['fullname'] = $member['firstname'] . " " . $member['lastname'];
                                                    } ?>
                                                    <div class="tags <?php echo $fin_token_value; ?>">
                                            <span>
                                                <span draggable="true" class="add_merge_field"
                                                      data-val="<?php echo $member[strtolower($new_value['name'])]; ?>">
                                                    <?php echo $member[strtolower($new_value['name'])] ? "<b>" . $new_value['name'] . "</b> : " . $member[strtolower($new_value['name'])] : "<b>" . $new_value['name'] . "</b>"; ?>
                                                </span>
                                            </span>
                                                    </div>
                                                <?php } else { ?>
                                                    <select class="tokenDrpdwn selectpicker mbot10">
                                                        <option value=""><?php echo $new_value['name']; ?></option>
                                                        <?php foreach ($rel_content['assigned'] as $assigned) {
                                                            $this->db->where('staffid', $assigned);
                                                            $member = $this->db->get('tblstaff')->row();
                                                            if (!empty($member)) {
                                                                $member = (array)$member;
                                                                $member['fullname'] = $member['firstname'] . " " . $member['lastname'];
                                                            }

                                                            if (isset($member[strtolower($new_value['name'])]) && $member[strtolower($new_value['name'])] != "") { ?>
                                                                <option value="<?php echo $member[strtolower($new_value['name'])] ?>"><?php echo $member[strtolower($new_value['name'])] ?></option>
                                                            <?php }
                                                        } ?>
                                                    </select>
                                                <?php } ?>
                                            <?php } elseif (isset($tasks) && !empty($tasks) && $fin_token_value == "tasks") {
                                                $new_value['name'] = str_replace("Task ", "", $new_value['name']);
                                                $new_value['name'] = str_replace(" ", "", $new_value['name']);
                                                $new_value['name'] = strtolower($new_value['name']);
                                                if (count($tasks) > 1) {
                                                    ?>
                                                    <select class="tokenDrpdwn selectpicker mbot10">
                                                        <option value=""><?php echo $new_value['name']; ?></option>
                                                        <?php
                                                        foreach ($tasks as $task) {
                                                            if (!empty($task['rel_id'])) {
                                                                $rel_data = get_relation_data($task['rel_type'], $task['rel_id']);
                                                                $rel_values = get_relation_values($rel_data, $task['rel_type']);
                                                                $task['relatedto'] = $rel_values['name'];
                                                            }
                                                            $this->db->where('taskid', $task['id']);
                                                            $this->db->limit(1);
                                                            $this->db->order_by('dateadded', 'desc');
                                                            $comment = $this->db->get('tblstafftaskcomments')->row();
                                                            $task['link'] = admin_url('tasks/dashboard/' . $task['id']);
                                                            $task['commentlink'] = $task['link'] . '#comment_' . $comment->id;
                                                            $task['comment'] = $comment->content;
                                                            $task['priority'] = task_priority($task['priority']);
                                                            $status = get_task_status_by_id($task['status'])['name'];
                                                            $task['status'] = $status;
                                                            if (isset($task[$new_value['name']]) && $task[$new_value['name']] != "") {
                                                                ?>
                                                                <option value="<?php echo $task[$new_value['name']] ?>"><?php echo $task[$new_value['name']] ?></option>
                                                            <?php }
                                                        } ?>
                                                    </select>
                                                <?php } else {
                                                    $task = $tasks[0];
                                                    if (!empty($task['rel_id'])) {
                                                        $rel_data = get_relation_data($task['rel_type'], $task['rel_id']);
                                                        $rel_values = get_relation_values($rel_data, $task['rel_type']);
                                                        $task['relatedto'] = $rel_values['name'];
                                                    }
                                                    $this->db->where('taskid', $task['id']);
                                                    $this->db->limit(1);
                                                    $this->db->order_by('dateadded', 'desc');
                                                    $comment = $this->db->get('tblstafftaskcomments')->row();
                                                    $task['link'] = admin_url('tasks/dashboard/' . $task['id']);
                                                    $task['commentlink'] = isset($comment->id) ? $task['link'] . '#comment_' . $comment->id : "";
                                                    $task['comment'] = isset($comment->content) ? $comment->content : "";
                                                    $task['priority'] = task_priority($task['priority']);
                                                    $status = get_task_status_by_id($task['status'])['name'];
                                                    $task['status'] = $status;
                                                    if ($task[$new_value['name']] != "") {
                                                        ?>

                                                        <div class="tags <?php echo $fin_token_value; ?>">
                                            <span>
                                                <span draggable="true" class="add_merge_field"
                                                      data-val="<?php echo $task[$new_value['name']]; ?>">
                                                    <?php echo $task[$new_value['name']] ? "<b>" . $new_value['name'] . "</b> : " . $task[$new_value['name']] : "<b>" . $new_value['name'] . "</b>"; ?>
                                                </span>
                                            </span>
                                                        </div>

                                                    <?php }
                                                } ?>
                                            <?php } elseif (isset($token_proposal) && !empty($token_proposal) && $fin_token_value == "proposals") {
                                                $valid = strtolower(str_replace(" ", "", $new_value['name']))
                                                ?>
                                                <div class="tags <?php echo $fin_token_value; ?>">
                                            <span>
                                                <span draggable="true" class="add_merge_field"
                                                      data-val="<?php echo $parent . $token_proposal[$valid]; ?>">
                                                    <?php echo $token_proposal[$valid] ? "<b>" . $new_value['name'] . "</b> : " . $token_proposal[$valid] : "<b>" . $new_value['name'] . "</b>"; ?>
                                                </span>
                                            </span>
                                                </div>
                                                <!--<select class="tokenDrpdwn selectpicker mbot10">
                                    <option value=""><?php /*echo $new_value['name']; */
                                                ?></option>
                                    <option value="<?php /*echo $token_proposal[$valid] */
                                                ?>"><?php /*echo $token_proposal[$valid] */
                                                ?></option>
                                </select>-->
                                            <?php } elseif (isset($lead_token) && !empty($lead_token) && $fin_token_value == "leads") {

                                                $new_value['name'] = str_replace(" ", "", $new_value['name']);
                                                //$lead_token[strtolower($new_value['name'])];
                                                ?>
                                                <div class="tags <?php echo $fin_token_value; ?>">
                                            <span>
                                                <span draggable="true" class="add_merge_field"
                                                      data-val="<?php echo $lead_token[strtolower($new_value['name'])]; ?>">
                                                    <?php echo $lead_token[strtolower($new_value['name'])] ? "<b>" . $new_value['name'] . "</b> : " . $lead_token[strtolower($new_value['name'])] : "<b>" . $new_value['name'] . "</b>"; ?>
                                                </span>
                                            </span>
                                                </div>
                                            <?php } elseif (isset($project_token) && !empty($project_token) && $fin_token_value == "projects") {

                                                $new_value['name'] = str_replace(" ", "", $new_value['name']);
                                                //$lead_token[strtolower($new_value['name'])];
                                                ?>
                                                <div class="tags <?php echo $fin_token_value; ?>">
                                            <span>
                                                <span draggable="true" class="add_merge_field"
                                                      data-val="<?php echo $project_token[strtolower($new_value['name'])]; ?>">
                                                    <?php echo $project_token[strtolower($new_value['name'])] ? "<b>" . $new_value['name'] . "</b> : " . $project_token[strtolower($new_value['name'])] : "<b>" . $new_value['name'] . "</b>"; ?>
                                                </span>
                                            </span>
                                                </div>
                                            <?php } elseif (isset($meetings) && !empty($meetings) && $fin_token_value == "meetings") {
                                                $meetings = (array)$meetings;
                                                $valid = str_replace(" ", "", $new_value['name']);
                                                $valid = strtolower($valid);

                                                if (count($meetings) > 1) {
                                                    ?>
                                                    <select class="tokenDrpdwn selectpicker mbot10">
                                                        <option value=""><?php echo $new_value['name']; ?></option>
                                                        <?php foreach ($meetings as $meeting) {
                                                            $meeting = (array)$meeting;
                                                            if (isset($meeting[$valid]) && !empty($meeting[$valid])) {
                                                                ?>
                                                                <option value="<?php echo $meeting[$valid]; ?>"><?php echo $meeting[$valid]; ?></option>
                                                            <?php }
                                                        } ?>
                                                    </select>
                                                <?php } else {
                                                    $meeting = $meetings[0];
                                                    $meeting = (array)$meeting;
                                                    if ($meeting[$valid] != "") {
                                                        ?>
                                                        <div class="tags <?php echo $fin_token_value; ?>">
                                            <span>
                                                <span draggable="true" class="add_merge_field"
                                                      data-val='<?php echo $meeting[$valid]; ?>'>
                                                    <?php echo $meeting[$valid] ? "<b>" . $new_value['name'] . "</b> : " . $meeting[$valid] : "<b>" . $new_value['name'] . "</b>"; ?>
                                                </span>
                                            </span>
                                                        </div>
                                                    <?php }
                                                } ?>
                                            <?php } elseif (isset($other_token) && !empty($other_token) && $fin_token_value == "other") {

                                                $new_value['name'] = str_replace(" ", "", $new_value['name']);
                                                /*if($other_token[strtolower($new_value['name'])]==""){
                                                    $other_token[strtolower($new_value['name'])]=$new_value['name'];
                                                }*/
                                                //$lead_token[strtolower($new_value['name'])];
                                                ?>
                                                <div class="tags <?php echo $fin_token_value; ?>">
                                            <span>
                                                <span draggable="true" class="add_merge_field"
                                                      data-val='<?php echo $other_token[strtolower($new_value['name'])] == "" ? $new_value['name'] : $other_token[strtolower($new_value['name'])]; ?>'>
                                                    <?php echo $other_token[strtolower($new_value['name'])] ? "<b>" . $new_value['name'] . "</b> : " . $other_token[strtolower($new_value['name'])] : "<b>" . $new_value['name'] . "</b>"; ?>
                                                </span>
                                            </span>
                                                </div>
                                            <?php } else { ?>
                                                <div class="tags <?php echo $fin_token_value; ?>">
                                            <span>
                                                <span draggable="true" class="add_merge_field"
                                                      data-val='<?php echo $parent . $val; ?>'>
                                                    <?php echo $new_value['name']; ?>
                                                </span>
                                            </span>
                                                </div>
                                            <?php }
                                        } ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <textarea name="emailbody" type="text" class="form-control" id="emailbody" required>
                                <?php echo $message; ?>
                            </textarea>
                        </div>
                    </div>
                    <div class="buttongroup">
                        <a href="<?php echo admin_url('proposaltemplates' . $rel_link) ?>" class="btn btn-info">
                            <i class="fa fa-close"></i>
                            <?php echo _l('cancel') ?>
                        </a>
                        <a id="emailpreview" type="button" name="preview" class="btn btn-info"
                           value="preview">
                            <i class="fa fa-eye"></i>
                            <?php echo _l('preview') ?>
                        </a>
                        <!--<button type="submit" name="cancle" class="btn btn-info"
                                value="canel">
                            <i class="fa fa-save"></i><?php /*echo _l('save_draft') */ ?>
                        </button>-->
                        <!--<button type="submit" name="cancle" class="btn btn-info"
                                value="canel"><i class="fa fa-clock-o"></i>
                            <?php /*echo _l('send_later') */ ?></button>-->
                        <button type="submit" name="sendnow" class="btn btn-info btn-sendNow"
                                value="sendnow">
                            <i class="fa fa-send"></i><?php echo _l('send_now') ?>
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="createmailpreview" class="createmailpreview">
        <a id="closepreview" href="#" class="pull-right">
            <i class="fa fa-close"></i><?php echo _l('close_preview') ?>
        </a>
        <?php $this->load->view('proposaltemplates/emailhead', array('title' => "proposal")); ?>
    </div>
<?php $this->load->view('proposaltemplates/includes/scripts');
