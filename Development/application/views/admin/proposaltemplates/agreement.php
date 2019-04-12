<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 28-02-2018
 * Time: 16:38
 */

$session_data = $_SESSION;
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
$removed_sections = array();
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
    $sections = json_decode($proposal->sections, true);
    $section = $sections['agreemnet'];
    $signatures = json_decode($proposal->signatures, true);
    $removed_sections = json_decode($proposal->removed_sections, true);
    $token_proposal = array();
    $token_proposal['proposalversion'] = $proposal->proposal_version;
    $token_proposal['proposalname'] = $proposal->name;
    $token_proposal['proposalopentill'] = $proposal->valid_date;
    $token_proposal['proposallink'] = "<a draggable='false' class = 'btn btn-info' href='#proposal_link'><i class='fa fa-file-text-o mright5'></i>" . $proposal->name . "</a>";
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
        $lead_token['leadassigned'] = !empty($lead_contents['assigned'])?get_staff_full_name($lead_contents['assigned'][0]):"";
        $lead_token['leadstartdate'] = _dt($lead_contents['eventstartdatetime']);
        $lead_token['leadenddate'] = _dt($lead_contents['eventenddatetime']);
        $lead_token['leadstarttime'] = date('h:i A', strtotime($lead_contents['eventstartdatetime']));
        $lead_token['leadendtime'] = date('h:i A', strtotime($lead_contents['eventenddatetime']));
        $lead_token['leadtotaltime'] = $totalhours . "hour(s)";
        $lead_token['leadtype'] = $lead_contents['eventtypename'];
        $lead_token['leaddashboardbutton'] = "<a  draggable='false' class = 'btn btn-info' href='" . admin_url('leads/dashboard/' . $lead_contents['id']) . "'>Lead Dashboard</a>";
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
        $project_token['projectassigned'] = !empty($project_contents['assigned'])?get_staff_full_name($project_contents['assigned'][0]):"";
        $project_token['projectstartdate'] = _dt($project_contents['eventstartdatetime']);
        $project_token['projectenddate'] = _dt($project_contents['eventenddatetime']);
        $project_token['projectstarttime'] = date('h:i A', strtotime($project_contents['eventstartdatetime']));
        $project_token['projectendtime'] = date('h:i A', strtotime($project_contents['eventenddatetime']));
        $project_token['projecttotaltime'] = $totalhours . "hour(s)";
        $project_token['projectbudget'] = format_money($project_contents['budget']);
        $project_token['projectsource'] = $project_contents['source_name'];
        $project_token['projecttype'] = $project_contents['eventtypename'];
        $project_token['projectdashboardbutton'] = "<a draggable='false' class='btn btn-info' href='" . admin_url('projects/dashboard/' . $project_contents['id']) . "'>Project Dashboard</a>";
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
$class = "";
$checked = "";
if (isset($removed_sections)) {
    $class = in_array('agreement', $removed_sections) ? "removed_section" : "";
    $checked = in_array('agreement', $removed_sections) ? "checked" : "";
}
?>
<div id="agreement" class="<?php echo $class ?>">
    <div class="row">
        <div class="files_header col-sm-12">
            <div class="row">
                <div class="col-sm-6">
                    <h4 id="agreement_page_name"><i
                                class="fa fa-file-text-o mright10"></i><b><span><?php echo isset($section) ? $section['name'] : "Agreement"; ?></span></b>
                    </h4>
                    <input type="hidden" name="sections[agreemnet][name]" class="agreement_page_name"
                           value="<?php echo isset($section) ? $section['name'] : "Agreement"; ?>">
                </div>
                <div class="col-sm-6 col-right">

                    <?php /*if (!isset($_GET['preview'])) {*/ ?>
                        <a href="javascript:void(0)" class="btn btn-info token_btn"
                           data-pid="#agreement_tokens">Tokens</a>
                        <?php if (isset($agreements) && count($agreements) > 0) { ?>
                            <select id="agreement_picker" class="agreement_load selectpicker">
                                <option value=""><i class="fa fa-plus-square"></i> USE TEMPLATE</option>
                                <?php foreach ($agreements as $agreement) { ?>
                                    <option value="<?php echo $agreement['templateid'] ?>"><?php echo ucfirst($agreement['name']) ?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>

                        <div class="show-options">
                            <a class='show_act' href='javascript:void(0)'>
                                <i class='fa fa-ellipsis-v' aria-hidden='true'></i>
                            </a>
                        </div>
                        <div class='table_actions'>
                            <ul>
                                <li>
                                    <a href='javascript:void(0)' class="" id="edit_page" data-toggle="modal"
                                       data-target="#edit_agreement_popup">
                                        <i class="fa fa-pencil-square-o"></i><span>Edit</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="checkbox">
                            <input type="checkbox" class="checkbox remove_proposal_section" name="remove_sec[]"
                                   id="remove_agreement"
                                   data-pid="#agreement" value="agreement" <?php echo $checked ?>/>
                            <label for="remove_agreement"><?php echo "Remove"; ?></label></div>
                    <?php /*} */ ?>
                </div>
            </div>
            <div class="section_body agreement-page">
                <div id="agreement_wrapper" class="agreement_form">
                    <?php $available_merge_fields = get_agreement_merge_fields(); ?>
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
                                    <a href="javascript:void(0)" class="btn btn-info  <?php echo $fin_token_value; ?>"
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
                                                    <?php echo $client[strtolower($new_value['name'])] ? "<b>" . $new_value['name'] . "</b> : ".$client[strtolower($new_value['name'])] : "<b>" . $new_value['name'] . "</b>"; ?>
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
                                                    <?php echo $member[strtolower($new_value['name'])] ? "<b>" . $new_value['name'] . "</b> : ".$member[strtolower($new_value['name'])] : "<b>" . $new_value['name'] . "</b>"; ?>
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
                                                    <?php echo $task[$new_value['name']] ? "<b>" . $new_value['name'] . "</b> : ".$task[$new_value['name']] : "<b>" . $new_value['name'] . "</b>"; ?>
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
                                                    <?php echo $token_proposal[$valid] ? "<b>" . $new_value['name'] . "</b> : ".$token_proposal[$valid] : "<b>" . $new_value['name'] . "</b>"; ?>
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
                                                    <?php echo $lead_token[strtolower($new_value['name'])] ? "<b>" . $new_value['name'] . "</b> : ".$lead_token[strtolower($new_value['name'])] : "<b>" . $new_value['name'] . "</b>"; ?>
                                                </span>
                                            </span>
                                        </div>
                                    <?php } elseif (isset($project_token) && !empty($project_token) && $fin_token_value == "projects") {

                                        $new_value['name'] = str_replace(" ", "", $new_value['name']);
                                        //$lead_token[strtolower($new_value['name'])];
                                        ?>
                                        <div class="tags <?php echo $fin_token_value; ?>">
                                            <span>
                                                <span  draggable="true" class="add_merge_field"
                                                   data-val="<?php echo $project_token[strtolower($new_value['name'])]; ?>">
                                                    <?php echo $project_token[strtolower($new_value['name'])] ? "<b>" . $new_value['name'] . "</b> : ".$project_token[strtolower($new_value['name'])] : "<b>" . $new_value['name'] . "</b>"; ?>
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
                                                    <?php echo $meeting[$valid] ? "<b>" . $new_value['name'] . "</b> : ".$meeting[$valid] : "<b>" . $new_value['name'] . "</b>"; ?>
                                                </span>
                                            </span>
                                                </div>
                                            <?php }
                                        } ?>
                                    <?php } elseif (isset($other_token) && !empty($other_token) && $fin_token_value == "other") {

                                        $new_value['name'] = str_replace(" ", "", $new_value['name']);
                                        if($other_token[strtolower($new_value['name'])]==""){
                                            $other_token[strtolower($new_value['name'])]=$new_value['name'];
                                        }
                                        ?>
                                        <div class="tags <?php echo $fin_token_value; ?>">
                                            <span>
                                                <span draggable="true" class="add_merge_field"
                                                   data-val='<?php echo $other_token[strtolower($new_value['name'])]; ?>'>
                                                    <?php echo $other_token[strtolower($new_value['name'])] ? "<b>" . $new_value['name'] . "</b> : ".$other_token[strtolower($new_value['name'])] : "<b>" . $new_value['name'] . "</b>"; ?>
                                                </span>
                                            </span>
                                        </div>
                                    <?php } else { ?>
                                        <div class="tags <?php echo $fin_token_value; ?>">
                                            <span>
                                                <span  draggable="true" class="add_merge_field"
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

                    <?php if (isset($_GET['preview']) && $_GET['preview'] == true) { ?>
                        <div class="agreement_txt">
                            <?php echo isset($proposal) ? $proposal->agreement : "" ?>
                        </div>
                    <?php } else { ?>
                        <?php //echo render_textarea('agreement', '', isset($proposal) ? $proposal->agreement : '', array(), array(), '', 'tinymce'); ?>
                        <textarea id="pagreement" class="pagreement"
                                  name="agreement"><?php echo isset($proposal) ? $proposal->agreement : "" ?></textarea>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="edit_agreement_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('edit page'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="group_popup">
                        <div class="group_name">
                            <div class="form-group">
                                <label class="control-label">Page Name
                                    <small class="req text-danger">*</small>
                                </label>
                                <input type="text" name="page_name[quote]" class="form-control page_name"
                                       value="<?php echo isset($section) ? strtoupper($section['name']) : "AGREEMENT"; ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" class="btn btn-info page_save" data-pid="agreement_page_name"
                   data-id="#edit_agreement_popup"><?php echo _l('submit'); ?></a>
            </div>
        </div>
    </div>
</div>
