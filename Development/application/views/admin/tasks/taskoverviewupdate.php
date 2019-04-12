<?php
      $where_not_admin = '(addedfrom = '.get_staff_user_id().' OR is_public = 1) AND deleted = 0';
      $numStatuses = count($statuses);
      $is_admin = is_admin();
      //echo '<pre>'; print_r($numStatuses);
      $processwidth=100 / $numStatuses;
      $brandid = get_user_session();
      foreach($statuses as $status){ ?>
      <div class="process-step" style="width:<?php echo $processwidth . '%' ; ?>">
      <?php
        $projectid = $this->input->get('pid');
        if($projectid != ""){
          $this->db->select('id');
          $this->db->where('(parent = '.$projectid.' OR id = '.$projectid.')');
          $this->db->where('deleted', 0);
          $related_project_ids = $this->db->get('tblprojects')->result_array();
        }else{
          $related_project_ids = array();
        }
         $this->db->where('status',$status['id']);
         if(!$is_admin){
          $this->db->where($where_not_admin);
          $this->db->where('brandid = '. $brandid);
          if($this->input->get('lid')) {
            $leadid = $this->input->get('lid');
            $this->db->where('rel_type ="lead"');
            $this->db->where('rel_id = '. $leadid);
          }
          if($this->input->get('eid')) {
            $eventid = $this->input->get('eid');
            $this->db->where('rel_type ="event"');
            $this->db->where('rel_id = '. $eventid);
          }
          if($this->input->get('pid')) {
            $related_project_ids = array_column($related_project_ids, 'id');
            if(!empty($related_project_ids)){
                $related_project_ids = implode(",", $related_project_ids);
                $this->db->where('rel_id in(' . $related_project_ids .')');
                $this->db->where('rel_type in("project", "event")');
            }else{
                $this->db->where('rel_id = ' . $projectid);
                $this->db->where('rel_type = "project"');
            }
          }
         }
         $total = $this->db->count_all_results('tblstafftasks');
         ?>
       <a href="javascript:void(0)" onclick="filterstatus('<?php echo $status['id']; ?>'); return false;">
      <h3 class="bold" style="background-color:<?php echo $status['color']; ?>"><?php echo $total; ?></h3>
      <span style="color:<?php echo $status['color']; ?>"><?php echo $status['name']; ?></span>
     </a>
   </div>
   <?php } ?>