<?php
/*
Added by Purvi on 11/16/2017
*/
defined('BASEPATH') or exit('No direct script access allowed');
class Messages extends Admin_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('messages_model');
        // Model is autoloaded
    }

    /* List all staff messages */
    public function index()
    {
        $pg = $this->input->get('pg');

        if (!has_permission('messages', '', 'view', true)) {
            access_denied('messages');
        }
        $data['title'] = _l('all_messages');
        if($this->input->get('lid')) {
            $leadid = $this->input->get('lid');

            $this->load->model('leads_model');

            $data['lid'] = $leadid;
            $data['lname'] = '';
            if($leadid != "") {
                $data['lname'] = $this->leads_model->get($leadid)->name;
            }
        }
        if($this->input->get('pid')) {
            $projectid = $this->input->get('pid');

            $this->load->model('projects_model');

            $data['pid'] = $projectid;
            $data['lname'] = '';
            if($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        }
        if($this->input->get('eid')) {
            $projectid = $this->input->get('eid');
            $parent_id = $this->projects_model->get($projectid)->parent;
            $this->load->model('projects_model');

            $data['eid'] = $projectid;
            $data['parent_id'] = $parent_id;
            $data['lname'] = '';
            $data['pname'] = '';
            if($parent_id != "") {
                $data['pname'] = $this->projects_model->get($parent_id)->name;
            }
            if($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        }
        $kanban=false;
        $search="";
        if($this->input->get('search')){
            $search=$this->input->get('search');
            $data['search']=$search;
        }
        $where=array();
        if($this->input->get('kanban')){
            $kanban=$this->input->get('kanban');
            $data['totalmessages'] = $this->messages_model->get("","","",$where,"","","",$search);
        }

        if($this->input->get('lid')) {
            $data['messages'] = $this->messages_model->get($this->input->get('lid'),"","",$where,$this->input->get('limit'),$this->input->get('page'),$kanban,$search);
        }else if($this->input->get('pid')) {
            $data['messages'] = $this->messages_model->get("", $this->input->get('pid'),"",$where,$this->input->get('limit'),$this->input->get('page'),$kanban,$search);
        }else if($this->input->get('eid')) {
            $data['messages'] = $this->messages_model->get("", "",$this->input->get('eid'),"",$where,$this->input->get('limit'),$this->input->get('page'),$kanban,$search);
        }else{
            $data['messages'] = $this->messages_model->get("","","",$where,$this->input->get('limit'),$this->input->get('page'),$kanban,$search);
        }

        /*echo "<pre>";
        print_r($this->session->userdata());
        die();*/
        $data['switch_messages_kanban'] = true;
        if ($this->session->has_userdata('messages_kanban_view') && $this->session->userdata('messages_kanban_view') == 'true') {
            $data['switch_messages_kanban'] = false;
            $data['bodyclass']     = 'kan-ban-body';
        }
		
		
		/*if(is_mobile()){
			$this->session->set_userdata(array(
            'messages_kanban_view' => 0
        	));
		}*/

        $data['pg']         = $pg;
        if($this->input->get('limit')) {
            $data['limit'] = $this->input->get('limit');
        }
        if($this->input->get('page')) {
            $data['page'] = $this->input->get('page');
        }
        if ($this->input->is_ajax_request()) {
            if ($this->input->get('kanban')) {
                //$data['messages'] = $this->messages_model->get();
                echo $this->load->view('admin/messages/kan-ban', $data, true);
                die();
            }
        }else{
            $this->load->view('admin/messages/manage', $data);
        }
    }

    /* Add new message or edit existing one */
    public function message($id = '')
    {
        $lid = $this->input->get('lid');
        $pid = $this->input->get('pid');
        $eid = $this->input->get('eid');
        if (!has_permission('messages', '', 'view', true)) {
            access_denied('messages');
        }
        $lid = $this->input->get('lid');
        if ($this->input->post()) {
            $postlid = $this->input->post('hdnlid');
            $postpid = $this->input->post('hdnpid');
            $posteid = $this->input->post('hdneid');
            if ($id == '') {
                if (!has_permission('messages', '', 'create', true)) {
                    access_denied('messages');
                }
                $id = $this->messages_model->add($this->input->post());
                if ($id) {
                    $uploadedFiles = handle_message_attachments_array($id);
                    if($uploadedFiles && is_array($uploadedFiles)){
                        foreach($uploadedFiles as $file){
                            $this->messages_model->add_attachment_to_message($id,array($file));
                        }
                    }
                    $this->sendmail($id);
                    $rel_id = $this->input->post('rel_id');
                    $rel_type = $this->input->post('rel_type');
                    set_alert('success', _l('message_reply_successfully'));
                    if(isset($postlid) && $postlid!="") {
                        redirect(admin_url('messages/' . "?lid=" . $postlid));
                    }else if(isset($postpid) && $postpid!="") {
                        redirect(admin_url('messages/' . "?pid=" . $postpid));
                    }else if(isset($posteid) && $posteid!="") {
                        redirect(admin_url('messages/' . "?eid=" . $posteid));
                    }else {
                        redirect(admin_url('messages/'));
                    }
                }
            } else {
                if (!has_permission('messages', '', 'edit', true)) {
                    access_denied('messages');
                }
                $success = $this->messages_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('message')));
                    //redirect(admin_url('messages/message/' . $id));
                    if(isset($postlid) && $postlid!="") {
                        redirect(admin_url('messages/' . "?lid=" . $postlid));
                    }else if(isset($postpid) && $postpid!="") {
                        redirect(admin_url('messages/' . "?pid=" . $postpid));
                    }else if(isset($posteid) && $posteid!="") {
                        redirect(admin_url('messages/' . "?eid=" . $posteid));
                    }else {
                        redirect(admin_url('messages/'));
                    }
                } else {
                    set_alert('danger', _l('problem_message_editing', _l('message_lowercase')));
                    if(isset($postlid) && $postlid!="") {
                        redirect(admin_url('messages/message' . "?lid=" . $postlid));
                    }else if(isset($postpid) && $postpid!="") {
                        redirect(admin_url('messages/message' . "?pid=" . $postpid));
                    }else if(isset($posteid) && $posteid!="") {
                        redirect(admin_url('messages/message' . "?eid=" . $posteid));
                    }else {
                        redirect(admin_url('messages/message'));
                    }
                }
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('message'));
        } else {
            $message            = $this->messages_model->get($id);
            $data['message']    = $message;
            $title              = _l('edit', _l('message')) . ' ' . $message->name;
        }
        $data['contacts']   = $this->messages_model->getcontacts();
        $data['teammember'] = $this->messages_model->getteammember();
        $data['tags']       = $this->tags_model->get();
        $data['title']      = $title;
        $data['lid']        = $this->input->get('lid');
        if($data['lid'] ) {
            $this->load->model('leads_model');
            $data['lname'] = '';
            if($data['lid'] != "") {
                $data['lname'] = $this->leads_model->get($data['lid'] )->name;
            }
        }
        if($this->input->get('pid')) {
            $projectid = $this->input->get('pid');

            $this->load->model('projects_model');

            $data['pid'] = $projectid;
            $data['lname'] = '';
            if($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        }
        if($this->input->get('eid')) {
            $projectid = $this->input->get('eid');
            $parent_id = $this->projects_model->get($projectid)->parent;
            $this->load->model('projects_model');

            $data['eid'] = $projectid;
            $data['parent_id'] = $parent_id;
            $data['lname'] = '';
            $data['pname'] = '';
            if($parent_id != "") {
                $data['pname'] = $this->projects_model->get($parent_id)->name;
            }
            if($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        }
        $data['leads']          = $this->meetings_model->get_leads();
        $data['projects']       = $this->meetings_model->get_projects();
        $data['events']         = $this->meetings_model->get_events($pid);
        $this->load->view('admin/messages/message', $data);
    }

    /**
        Added By Purvi on 11-17-2017 For read message
    */
    public function readmessage(){
        $message_id = $_POST['message_id'];

        $readdata = $this->messages_model->readmessage($message_id);

        echo $readdata;
        exit;
    }
    /**
    Added By Purvi on 11-17-2017 For read message
     */
    public function readmessages(){
        $message_ids = $_POST['message_ids'];

        $readdata = $this->messages_model->readmessages($message_ids);

        echo $readdata;
        exit;
    }
    /**
        Added By Purvi on 11-17-2017 For read all messages
    */
    public function readallmessages(){

        $readdata = $this->messages_model->readallmessages();

        echo $readdata;
        exit;
    }

    /* List all staff messages */
    public function view($id = '')
    {
        $pg = $this->input->get('pg');

        if (!has_permission('messages', '', 'view', true)) {
            access_denied('messages');
        }
        $data['title'] = _l('view_messages');
        if($this->input->get('lid')) {
            $leadid = $this->input->get('lid');

            $this->load->model('leads_model');

            $data['lid'] = $leadid;
            $data['lname'] = '';
            if($leadid != "") {
                $data['lname'] = $this->leads_model->get($leadid)->name;
            }
        }
        if($this->input->get('pid')) {
            $projectid = $this->input->get('pid');

            $this->load->model('projects_model');

            $data['pid'] = $projectid;
            $data['lname'] = '';
            if($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        }
        if($this->input->get('eid')) {
            $projectid = $this->input->get('eid');
            $parent_id = $this->projects_model->get($projectid)->parent;
            $this->load->model('projects_model');

            $data['eid'] = $projectid;
            $data['parent_id'] = $parent_id;
            $data['lname'] = '';
            $data['pname'] = '';
            if($parent_id != "") {
                $data['pname'] = $this->projects_model->get($parent_id)->name;
            }
            if($projectid != "") {
                $data['lname'] = $this->projects_model->get($projectid)->name;
            }
        }
        $data['messages'] = $this->messages_model->getmessagedetails($id);

        $this->messages_model->readmessagethread($id);

        $data['contacts']   = $this->messages_model->getcontacts($id);
        $data['teammember'] = $this->messages_model->getteammember($id);
        $data['pg']     = $pg;
        $this->load->view('admin/messages/view', $data);
    }

    /* Add new message or edit existing one */
    public function replymessage($id = '')
    {
        if (!has_permission('messages', '', 'create', true)) {
            access_denied('messages');
        }

        $lid = $this->input->get('lid');
        if ($this->input->post()) {
            if ($id != '') {
                if (!has_permission('messages', '', 'create', true)) {
                    access_denied('messages');
                }
                $insert_id = $this->messages_model->replymessage($this->input->post(),$id);
                if ($insert_id) {
                    $uploadedFiles = handle_message_attachments_array($insert_id);
                    if($uploadedFiles && is_array($uploadedFiles)){
                        foreach($uploadedFiles as $file){
                            $this->messages_model->add_attachment_to_message($insert_id,array($file));
                        }
                    }
                    $this->sendmail($id,$insert_id);
                    $rel_id = $this->input->post('rel_id');
                    $rel_type = $this->input->post('rel_type');
                    set_alert('success', _l('message_reply_successfully'));
                    if(isset($rel_id) && isset($rel_type)){
                        if($rel_type == 'lead'){
                            redirect(admin_url('messages/view/'.$id.'/?lid='.$rel_id));
                        }elseif($rel_type == 'project'){
                            redirect(admin_url('messages/view/'.$id.'/?pid='.$rel_id));
                        }elseif($rel_type == 'event'){
                            redirect(admin_url('messages/view/'.$id.'/?eid='.$rel_id));
                        }
                    }else{
                        redirect(admin_url('messages/view/'.$id));
                    }
                }
            }
        }

    }

    public function delete($id)
    {
        if (!has_permission('messages', '', 'delete', true)) {
            access_denied('messages');
        }
        $response = $this->messages_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('messages')));
        } else {
            set_alert('danger', _l('problem_deleting', _l('message')));
        }

    }

     public function sendmail($message_id = "", $child_id = ""){
        $this->load->model('emails_model');
        $merge_fields = array();

        $template     = 'new-message-created';
        $message_thread = $message_details_data = $message_privacy = "";
        //$merge_fields = array_merge($merge_fields, get_meetings_merge_fields($insert_id));
        $message_details = $this->messages_model->getmessagedetailsforemail($message_id);
        //$message_emails = $this->messages_model->getuseremails(10);
        if($child_id != ""){
            $message_emails = $this->messages_model->getuseremails($child_id);
        }else{
            $message_emails = $this->messages_model->getuseremails($message_id);
        }
        //echo "<pre>";print_r($message_emails);exit;
        $privacy = "";
        if(!empty($message_details->privacy)){
            $privacy = implode(", ", $message_details->privacy);
        }
        //echo "<pre>";print_r($message_details);print_r($message_emails);exit;
        if($privacy != ""){
            $message_privacy .= '<table style="max-width:600px; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:18px;">
                  <tr>
                    <td><p style="line-height:10.5pt; padding:4px 5px; font-size:12px; font-family:\'Arial\',sans-serif; color:#333333; background:#FFE6EA"><img border=0 width=16 height=16  src="'.base_url('assets/images/private.png').'" style="vertical-align:middle;" >&nbsp;'.$privacy.'</p></td>
                  </tr>
                  </table>';
        }
        $message_thread .= '<a href="{reply_link}" style="display: inline-block; background: rgb(0, 169, 185) none repeat scroll 0% 0%; color: rgb(255, 255, 255); text-decoration: none; border-radius: 4px; line-height: 20pt; width: 50px; text-align: center;"><img border=0 src="'.base_url('assets/images/reply.png').'" ></a><br><br>';
            if(!empty($message_details->child_message)){
                $message_thread .= '<table style="max-width:600px; font-family:Arial, Helvetica, sans-serif; font-size:14px; line-height:18px;">';

                foreach ($message_details->child_message as $child_message) {

                   // echo "<pre>";print_r($child_message);exit;
                   $message_thread .= '<tr><td><table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td style="padding:5px 0px; font-family:Arial, Helvetica, sans-serif; font-size:10.5pt; line-height:12.0pt;"><b style="font-size:14px; font-weight:bold;">'.$child_message["created_by_name"].'</b> <span style="font-size:13px;  color:#676767;">replied on</span> <span style="font-size:13px;  color:#676767;">'._dt($child_message["created_date"],true).'</span></td>
                        </tr>
                        <tr>
                          <td style="background:#efefef; padding:10px; border-left:5px solid #ccc;">'.$child_message["content"].'</td>
                        </tr>';
                        if(!empty($child_message['attachments'])){
                            $message_thread .= '<tr><td style="background:#efefef; padding:10px; border-left:5px solid #ccc;">Attachments '.count($child_message['attachments']).' File(s)<br/>';
                            foreach ($child_message['attachments'] as $attachment) {
                                        $MessageFilePath = 'uploads/messages/' . $child_message['id'] . '/' . $attachment;
                                    $message_thread .= '<a href="'.base_url($MessageFilePath).'" style="font-size:14px; color:#1093b0;">'.$attachment.'</a><br/>';
                            }
                            $message_thread .= '</td>
                            </tr>';
                        }
          $message_thread .= '</table></td></tr>
                              <tr>
                                <td height="10"></td>
                              </tr>';
            }
           $message_thread .= '</table>';
        }

        $message_details_data .= '<table border="0" cellspacing="0" cellpadding="0" style="max-width:600px;border:1px solid #efefef;">
        <tr>
          <td style="padding:10px 10px; font-family:Arial, Helvetica, sans-serif; background:#efefef; font-weight:bold;">Message Details: <span style="font-size:12px; color:#333; font-weight:normal;">'._dt($message_details->created_date,true).'</span></td>
        </tr>
        <tr>
          <td style="padding:10px;"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          
            <tr>
              <td width="100" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; padding:5px 0px;">Subject:</td>
              <td style="font-family:Arial, Helvetica, sans-serif; font-size:14px;  padding:5px 0px;">'.$message_details->subject.'</td>
            </tr>
            <tr>
              <td width="100" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; padding:5px 0px;">From:</td>
              <td style="font-family:Arial, Helvetica, sans-serif; font-size:14px;  padding:5px 0px;">'.$message_details->created_by_name.'</td>
            </tr>
            <tr>
              <td width="100" style="font-family:Arial, Helvetica, sans-serif; font-size:14px; font-weight:bold; padding:5px 0px;">Message:</td>
             <td style="font-family:Arial, Helvetica, sans-serif; font-size:14px;  padding:5px 0px;">'.$message_details->content.'</td>
            </tr>';
            if(!empty($message_details->attachments)){
                            $message_details_data .= '<tr><td colspan="2" style="font-family:Arial, Helvetica, sans-serif; font-size:14px;  padding:5px 0px;"><b>Attachments:</b> ('.count($message_details->attachments).' File(s))<br/>';
                            foreach ($message_details->attachments as $attachment) {
                                        $MessageFilePath = 'uploads/messages/' . $message_details->id . '/' . $attachment;
                                    $message_details_data .= '<a href="'.base_url($MessageFilePath).'" style="font-size:14px; color:#1093b0;line-height:18px;">'.$attachment.'</a><br/>';
                            }
                            $message_details_data .= '</td>
                            </tr>';
                        }
          $message_details_data .= '</table>';

         $merge_fields['{message_thread}']   = $message_thread;
         $merge_fields['{message_details}']  = $message_details_data;
         $merge_fields['{message_privacy}']  = $message_privacy;
         $merge_fields['{message_subject}']  = $message_details->subject;
         $merge_fields['{message_from}']     = $message_emails->created_by_name;
        // echo $message_privacy;
        // echo $message_thread;
        // echo $message_details_data;
        // exit;

        foreach ($message_emails->usersdetail as $fvalue) {
            $reply_url = "?mid=".$message_id."&cid=".$fvalue['uid']."&brand_id=".get_user_session();
            $reply_url = base64_encode($reply_url);
            if($fvalue['usertype'] == "teammember"){
                $merge_fields['{message_thread}'] = str_replace("{reply_link}",admin_url("messages/view/".$message_id),$message_thread);
            }else{
                $merge_fields['{message_thread}'] = str_replace("{reply_link}",site_url('clients/replymessage?'.$reply_url),$message_thread);
            }
            $send = $this->emails_model->send_email_template("new-message-created", $fvalue['email'], $merge_fields);
        }

    }

    public function edit_message()
    {
        if ($this->input->post()) {
            $success = $this->messages_model->edit_message($this->input->post(null, false));
            $message = '';
            if ($success) {
                $message = _l('message_updated');
            }
            echo json_encode(array(
                'success' => $success,
                'message' => $message
            ));
        }
    }

    /**
    * Added By: Vaidehi
    * Dt: 02/28/2018
    * for pinned message
    */
    public function pinmessage(){
        $message_id = $this->input->post('message_id');

        $pindata = $this->messages_model->pinmessage($message_id);

        echo $pindata;
        exit;
    }

    /**
     * Added By : Masud
     * Dt : 06/11/2018
     * kanban view for meeting
     */
    public function switch_messages_kanban($set = 0)
    {
        if ($set == 1) {
            $set = 'true';
        } else {
            $set = 'false';
        }

        $this->session->set_userdata(array(
            'messages_kanban_view' => $set
        ));

        redirect($_SERVER['HTTP_REFERER']);
    }
}