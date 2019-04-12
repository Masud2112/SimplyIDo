<div id="stats-top" class="hide">
  <div id="invoices_total"></div>
  <div class="panel_s mtop20">
    <div class="">
      <?php
      $CI =& get_instance();
      if(isset($pid)){
        $CI->db->select('id');
        $CI->db->where('(parent = '.$pid.' OR id = '.$pid.')');
        $CI->db->where('deleted', 0);
        $related_project_ids = $CI->db->get('tblprojects')->result_array();
        $related_project_ids = array_column($related_project_ids, 'id');
      }else{
        $related_project_ids = array();
      }

      // Added by Avni on 11/23/2017 Start
      if(isset($lid)){
        $CI->db->where('leadid', $lid);
        //$where_all['leadid'] = $lid;
      }elseif(isset($eid)){
        //$where['eventid'] = $eid;
        $CI->db->where('eventid', $eid);
      }elseif(isset($pid)){
          if(!empty($related_project_ids)){
              $related_project_ids = implode(",", $related_project_ids);
              $CI->db->where('(project_id in(' . $related_project_ids .') OR eventid in(' . $related_project_ids .'))');
          }else{
              $CI->db->where('project_id =' . $pid);
          }
      }
      //$where_all['brandid'] = get_user_session();
      $CI->db->where('brandid', get_user_session());
      $CI->db->where('addedfrom', get_staff_user_id());
      $total_invoices = $CI->db->count_all_results('tblinvoices');
    ?>
    <div class="row text-left quick-top-stats">
      <?php foreach($invoices_statuses as $status){ if($status == 5){continue;}
      if(isset($lid)){
        $CI->db->where('leadid', $lid);
      }elseif(isset($eid)){
        $CI->db->where('eventid', $eid);
      }elseif(isset($pid)){
          if(!empty($related_project_ids)){
              $CI->db->where('(project_id in(' . $related_project_ids .') OR eventid in(' . $related_project_ids .'))');
          }else{
              $CI->db->where('project_id =' . $pid);
          }
      }
      $CI->db->where('brandid', get_user_session());
      $CI->db->where('addedfrom', get_staff_user_id());
      $CI->db->where('status', $status);
      $total_by_status = $CI->db->count_all_results('tblinvoices');
      $percent = ($total_invoices > 0 ? number_format(($total_by_status * 100) / $total_invoices,0) : 0);
    ?>
    <div class="col-lg-5ths col-md-5ths">
    <div class="panel-body">
      <div class="row titlerow">
        <div class="col-xs-7">
          <a href="#" data-cview="invoices_<?php echo $status; ?>" onclick="dt_custom_view('invoices_<?php echo $status; ?>','.table-invoices','invoices_<?php echo $status; ?>',true); return false;">
            <h5><?php echo format_invoice_status($status,'',false); ?></h5>
          </a>
        </div>
        <div class="col-xs-5 text-right">
          <?php echo $total_by_status; ?> / <?php echo $total_invoices; ?>
        </div>
		</div>
		 <div class="row">
        <div class="col-md-12">
          <div class="progress no-margin">
            <div class="progress-bar progress-bar-<?php echo get_invoice_status_label($status); ?>" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent; ?>">
            </div>
          </div>
        </div>
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
</div>
</div>
</div>
