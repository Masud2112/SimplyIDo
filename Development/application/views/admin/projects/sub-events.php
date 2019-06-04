<table class="table table-striped table-sub-events">
  <thead>
    <tr>
      <th><?php echo _l('events_dt_event_date_time'); ?></th>
      <th><?php echo _l('events_dt_event_name'); ?></th>
      <?php if(isset($staffid) || isset($contactid) || isset($venueid)) { ?>
        <th></th>
      <?php } else { ?>
        <th><?php echo _l('events_dt_event_venue'); ?></th>
        <th><?php echo _l('events_dt_event_assigned_to'); ?></th>
        <th></th>
      <?php } ?>
    </tr>
  </thead>
  <tbody>
    <?php if(isset($staffid)) { ?>
      <input type="hidden" name="staffid" id="staffid" value="<?php echo $staffid; ?>">
    <?php }?>
    <?php if(isset($contactid)) { ?>
      <input type="hidden" name="contactid" id="contactid" value="<?php echo $contactid; ?>">
    <?php }?>
    <?php if(isset($venueid)) { ?>
      <input type="hidden" name="venueid" id="venueid" value="<?php echo $venueid; ?>">
    <?php }?>
    <?php 
      if(count($events) > 0) {
         if(!is_array($events) && count($events)==1){
             $events = (array)$events;
             $e[0]=$events;
             $events=$e;
         }
        foreach ($events as $event) {

    ?>
          <tr>
            <td>
              <?php 
                $session_data = get_session_data();
                $is_sido_admin = isset($session_data['is_sido_admin']) ? $session_data['is_sido_admin'] : 2;
                $is_admin = isset($session_data['is_admin']) ? $session_data['is_admin'] : 2;

                if($is_sido_admin == 0 && $is_admin == 0) {
                  $hour12 = (get_brand_option('time_format') == 24 ? false : true);
                } else {
                  $hour12 = (get_option('time_format') == 24 ? false : true);
                }

                echo ($hour12 == false ? substr(_dt($event['eventstartdatetime'], true),0,-3) : _dt($event['eventstartdatetime'], true));
              ?>
              </td>
            <td><?php echo $event['name']; ?></td>
            <?php if(isset($staffid) || isset($contactid) || isset($venueid)) { 
                $staffid        = isset($staffid) ? $staffid : 0;
                $contactid      = isset($contactid) ? $contactid : 0;
                $isvendor       = isset($isvendor) ? $isvendor : 0;
                $iscollaborator = isset($iscollaborator) ? $iscollaborator : 0;
                $venueid        = isset($venueid) ? $venueid : 0;
              ?>
              <td>
                  <div class='text-right mright10'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>
                      <li><a href="<?php echo admin_url('projects/editinvitepermission')."/".$event['id']."/".$staffid."/".$contactid . "/". $isvendor . "/" . $iscollaborator."/".$venueid; ?>" class="btn-icon" title="Edit Invite Permission"><i class="fa fa-pencil-square-o"></i>Edit</a></li>
                      <li><a href="<?php echo admin_url('projects/removevendor')."/".$event['id']."/".$staffid."/".$contactid . "/". $isvendor . "/" . $iscollaborator."/".$venueid; ?>" class="_delete" title="Remove Vendor"><i class="fa fa-remove"></i>Delete</a></li>
                      </ul></div>
              </td>
            <?php } else { ?>
              <td><?php echo (!empty($event['address']) ? $event['address'] : '') . (!empty($event['city']) ? (" , " . $event['city'] ) : '' ). (!empty( $event['state']) ? " , " . $event['state'] : ''); ?></td>
              <td><?php echo $event['assigned_name']; ?></td>
              <td><a href="<?php echo admin_url("projects/dashboard/" . $event['id']); ?>" class="btn btn-success btn-icon" title="View Dashboard"><i class="fa fa-eye"></i></a></td>
            <?php } ?>
          </tr>
    <?php
        }
      } else {
    ?>
          <tr>
            <?php if(isset($staffid) || isset($contactid) || isset($venueid)) { ?>
              <?php if($contacttype == 3) { ?>
                <td colspan="3" align="center">No Vendors Invited</td>
              <?php } if($contacttype == 4) { ?>
                <td colspan="3" align="center">No Collaborators Invited</td>
              <?php } if($contacttype == 5) { ?>
                <td colspan="3" align="center">No Venues Invited</td>
              <?php } ?>
            <?php } else { ?>
              <td colspan="5" align="center"><?php echo _l('no_events_for_projects'); ?></td>
            <?php } ?>
          </tr>
    <?php
      }
    ?>
  </tbody>
</table>