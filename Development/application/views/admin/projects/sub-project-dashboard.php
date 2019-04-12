<div class="project-interaction">
  <input type="hidden" name="dashboard-type" id="dashboard-type" value="event">
  <input type="hidden" name="projectid" id="projectid" value="<?php echo $projectid; ?>">
  <div class="row row-flex">
    <div class="col-md-6">
      <div class="card-user-info-widget">
        <div class="row">
          <div class="col-sm-12 card-user-info">
            <figure class="text-center thumb-lg"> <?php echo project_profile_image($projectid,array('profile_image','img-responsive img-thumbnail','project-profile-image-thumb'),'thumb'); ?> </figure>
            <div class="pull-right">
              <?php if (has_permission('projects', '', 'edit', true)){ ?>
                <a data-toggle="tooltip" data-title="Edit project" href="<?php echo admin_url('projects/project/'.$projectid); ?>" class="btn btn-orange btn-icon pull-right"><i class="fa fa-pencil-square-o"></i></a>
              <?php } ?>
              <a data-toggle="tooltip" data-title="Back" href="<?php echo admin_url('projects'); ?>" class="btn btn-default pull-right"><i class="fa fa-chevron-left"></i></a>
            </div>
            <div class="img-caption">
              <h4 class="fw-700"><?php echo isset($project->name) ? $project->name : "--"; ?></h4>
            </div>
            <?php 
              if(($project->eventstartdatetime != "")) {
                $eventstartdatetime = date('l, F d, Y',strtotime($project->eventstartdatetime));
              } else {
                $eventstartdatetime = "--";
              }

              if(($project->eventenddatetime != "")) {
                $eventenddatetime = date('l, F d, Y',strtotime($project->eventenddatetime));
              } else {
                $eventenddatetime = "--";
              }

              if($project->pinid > 0) { 
                $pintitle = 'Unpin from Dashboard';
                $pinclass = 'pinned';
              } else {
                $pintitle = 'Pin to Dashboard';
                $pinclass = "";
              }

              if(($project->assigned_name != "")) {
                $assigned_name = $project->assigned_name;
              } else {
                $assigned_name = "--";
              }

              if(($project->eventtypename != "")) {
                $eventtypename = $project->eventtypename;
              } else {
                $eventtypename = "--";
              }
              $status_name = $project->status_name;
            ?>
            <ul class="list-unstyled mb-0 text-muted email-details-list">
              <li class="col-12 mr-t-20"><i class="list-icon fa fa-calendar-plus-o"></i><?php echo $eventstartdatetime; ?></li>
              <li class="col-12"><i class="list-icon fa fa-calendar-check-o"></i><?php echo $eventenddatetime; ?></li>
              <li class="col-12"><i class="list-icon fa fa-map-marker"></i><?php echo $eventtypename; ?></li>
              <li class="col-12"><i class="fa fa-star-half-o task-info-icon"></i><?php echo $status_name; ?></li>
              <li class="col-12"><i class="list-icon fa fa-thumb-tack"></i><span class="project-pin <?php echo $pinclass; ?>"  project_id="<?php echo $projectid; ?>">&nbsp; <?php echo $pintitle; ?></span></li>
              <li class="col-12"><i class="list-icon fa fa-user"></i><?php echo $assigned_name; ?></li>
            </ul>
          </div>
        </div>
      </div>
      <!-- /.card-user-info-widget --> 
    </div>
    <div class="col-md-6">
      <div class="statistic-squares text-center"> <span id="projectCountdown" class="countdown"></span> </div>
      <?php
        if(!empty($project->lastaction)) {
           $lastaction = _dt($project->lastaction);
        }

        if(!empty($project->nextaction)) {
           $nextaction = _dt($project->nextaction);
        } else {
           $nextaction = "Nothing Scheduled";
        }
      ?>
      <div class="widget-bg meeting-action">
        <div class="row m-0">
          <div class="col-md-5">
            <div class="progress-stats-round text-center input-has-value"> <span>LAST INTERACTION</span>
              <h4 class="color-primary mr-tb-10"><i class="fa fa-calendar"></i></h4>
              <?php if(!empty($project->lastaction)) { ?>
              <div class="date"><?php echo $project->last_meeting_name; ?></div>
              <small><?php echo "(".time_ago($project->lastaction).")"; ?></small>
              <?php } else { ?>
              <div class="date"><?php echo $project->last_meeting_name; ?></div>
              <small><?php echo (!empty($lastaction) ? "(".time_ago($lastaction).")" : ""); ?></small>
              <?php } ?>
            </div>
          </div>
          <div class="col-md-2">
            <h4 class="action-arrow"><i class="fa fa-angle-double-right"></i></h4>
          </div>
          <div class="col-md-5">
            <div class="progress-stats-round text-center input-has-value"> <span>NEXT INTERACTION</span>
              <h4 class="color-primary mr-tb-10"><i class="fa fa-calendar"></i></h4>
              <?php if(!empty($project->nextaction)){ ?>
              <div class="date"><?php echo $project->next_meeting_name ?></div>
              <small><?php echo "(".after_time($project->nextaction).")"; ?></small>
              <?php } else { ?>
              <div class="date"><?php echo $nextaction ?></div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.widget-body --> 
  </div>
</div>
<!-- /.row -->
<div class="row row-flex">
  <div class="col-md-6 widget-holder">
    <div class="widget-bg project-details">
      <div class="widget-heading">
        <h5>Project Details</h5>
      </div>
      <div class="widget-body clearfix">
        <div class="weather-card-default"> No Data Available </div>
      </div>
    </div>
    <div class="widget-bg collaborators-details">
      <div class="widget-heading">
        <h5>Collaborators <span>(<?php echo (isset($project->collaborators) ? count($project->collaborators) : 0); ?>)</span></span>
          <div class="more-setting">
            <a href="<?php echo admin_url('projects/invite/4/'.$project->id); ?>"><i class="fa fa-plus"></i></a>
            <a href="javascript: void(0);" data-toggle="collapse" data-target="#collaborator-data" id="collaborator-collapse"><i class="fa fa-caret-down"></i></a>
          </div>
        </h5>
      </div>
      <div class="widget-body clearfix collapse" id="collaborator-data">
        <div class="collaborator-card-default">
          <div class="col-sm-12">
            <table>
              <tbody>       
              <?php if(isset($project->collaborators) && count($project->collaborators) > 0) { ?>
                <?php foreach($project->collaborators as $collaborators) { ?>
                  <tr>
                    <td width="40px">
                      <div class="lead-pimg">
                        <?php echo $collaborators['image']; ?>
                      </div>
                    </td>
                    <td width="100%">
                      <div class="collaborator-det">
                        <h3><?php echo isset($collaborators['companyname']) ? $collaborators['companyname'] : ''; ?></h3>
                        <div class="invite-tags">
                          <span><?php echo isset($collaborators['name']) ? $collaborators['name'] : ''; ?></span>
                          <span><?php echo (isset($collaborators['tags']) && $collaborators['tags'] != '') ? '(' . $collaborators['tags'] . ')' : ''; ?></span>
                        </div>
                      </div>
                    </td>
                    <?php
                      $session_data   = get_session_data(); 
                      $user_type  = $session_data['user_type'];
                      if($user_type == 1) {
                        $staffid = (isset($collaborators['staffid']) ? $collaborators['staffid'] : 0);
                        $addressbookid = (isset($collaborators['addressbookid']) ? $collaborators['addressbookid'] : 0);
                    ?>
                        <td>
                          <div>
                            <a role="menuitem" class="btn btn-success btn-icon" tabindex="-1" href="javascript: void(0);" onclick="fnViewInvite(<?php echo $projectid; ?>, <?php echo $staffid; ?>, <?php echo $addressbookid; ?>, 0 , 1);"><i class="fa fa-eye"></i></a>
                          </div>
                        </td>
                    <?php } ?>  
                  </tr>
                <?php } ?>
              <?php } else { ?>
                <tr><td colspan="3">No Collaborators found.</td></tr>
              <?php }?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="widget-bg vendors-details">
      <div class="widget-heading">
        <h5>Vendors <span>(<?php echo (isset($project->vendors) ? count($project->vendors) : 0); ?>)</span>
          <div class="more-setting">
            <a href="<?php echo admin_url('projects/invite/3/'.$project->id); ?>"><i class="fa fa-plus"></i></a>
            <a href="javascript: void(0);" data-toggle="collapse" data-target="#vendor-data" id="vendor-collapse"><i class="fa fa-caret-down"></i></a>
          </div>            
        </h5>
      </div>
      <div class="widget-body clearfix" id="vendor-data">
        <div class="vendor-card-default">
          <div class="col-sm-12">
            <table>
              <tbody>       
              <?php if(isset($project->vendors) && count($project->vendors) > 0) { ?>
                <?php foreach($project->vendors as $vendors) { ?>
                  <tr>
                    <td width="40px">
                      <div class="lead-pimg">
                        <?php echo $vendors['image']; ?>
                      </div>
                    </td>
                    <td width="100%">
                      <div class="vendor-det">
                        <h3><?php echo isset($vendors['companyname']) ? $vendors['companyname'] : ''; ?></h3>
                        <div class="invite-tags">
                          <span><?php echo isset($vendors['name']) ? $vendors['name'] : ''; ?></span>
                          <span><?php echo (isset($vendors['tags']) && $vendors['tags'] != '') ? '(' . $vendors['tags'] . ')' : ''; ?></span>
                        </div>
                      </div>
                    </td>
                    <?php
                      $session_data   = get_session_data(); 
                      $user_type  = $session_data['user_type'];
                      if($user_type == 1) { 
                        $staffid = (isset($vendors['staffid']) ? $vendors['staffid'] : 0);
                        $addressbookid = (isset($vendors['addressbookid']) ? $vendors['addressbookid'] : 0);
                    ?>
                        <td>
                          <div>
                            <a role="menuitem" class="btn btn-success btn-icon" tabindex="-1" href="javascript: void(0);"onclick="fnViewInvite(<?php echo $projectid; ?>, <?php echo $staffid; ?>, <?php echo $addressbookid; ?>, 1, 0);"><i class="fa fa-eye"></i></a>
                          </div>
                        </td>
                    <?php } ?>  
                  </tr>
                <?php } ?>
              <?php } else { ?>
                <tr><td colspan="3">No vendors found.</td></tr>
              <?php }?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-6 widget-holder">
    <div class="project-tool-block">
      <h5 class="box-title">Project Tools</h5>
      <div class="back-icon">
        <div class="widget-body clearfix">
          <div class="tabs tabs-bordered ">
            <ul class="nav nav-tabs">
              <?php if (has_permission('meetings','','view')) { 
                if($project->is_client == 1) {
                  $permission_array = explode(",",$project->permission);
                  $access = (in_array('Meetings', $permission_array) ? true : false);
                } else {
                  $access = true;
                }

                if($access) {
              ?>
                <li class="nav-item1 ">
                  <a class="nav-link" href="<?php echo admin_url('meetings?eid=' .$projectid); ?>">
                    <i class="fa fa-clock-o"></i>
                    <p><?php echo _l('meetings'); ?></p>
                  </a>
                </li>
              <?php } 
                }
              ?>
              <?php if (has_permission('tasks','','view')) {
                if($project->is_client == 1) {
                  $permission_array = explode(",",$project->permission);
                  $access = (in_array('Tasks', $permission_array) ? true : false);
                } else {
                  $access = true;
                }

                if($access) { 
                ?>
                  <li class="nav-item2">
                    <a class="nav-link" href="<?php echo admin_url('tasks?eid=' .$projectid); ?>">
                      <i class="fa fa-check-square-o"></i>
                      <p><?php echo _l('tasks'); ?></p>
                    </a> 
                  </li>
                <?php } 
                  }
                ?>
                <?php if (has_permission('messages','','view')) { ?>
                  <li class="nav-item3">
                    <a class="nav-link" href="<?php echo admin_url('messages?eid=' .$projectid); ?>" aria-expanded="true">
                      <i class="fa fa-envelope"></i>
                      <p><?php echo _l('messages'); ?></p>
                    </a>
                  </li>
                <?php } ?>
                <li class="nav-item4">
                  <a class="nav-link" href="<?php echo admin_url('projects/notes/?eid=' .$projectid); ?>">
                    <i class="fa fa-sticky-note-o"></i>
                    <p><?php echo _l('notes'); ?></p>
                  </a>
                </li>
                <?php if (has_permission('files','','view')) { 
                  if($project->is_client == 1) {
                    $permission_array = explode(",",$project->permission);
                    $access = (in_array('Files', $permission_array) ? true : false);
                  } else {
                    $access = true;
                  }

                  if($access) {
                ?>
                    <li class="nav-item5">
                      <a class="nav-link" href="<?php echo admin_url('files?eid=' .$projectid); ?>">  <i class="fa fa-files-o"></i>
                        <p><?php echo _l('files'); ?></p>
                      </a>
                    </li>
                <?php } 
                  }
                ?>
                <?php if (has_permission('proposals','','view')) {
                  if($project->is_client == 1) {
                    $permission_array = explode(",",$project->permission);
                    $access = (in_array('Proposals', $permission_array) ? true : false);
                  } else {
                    $access = true;
                  }

                  if($access) {
                ?>
                  <li class="nav-item6">
                    <a class="nav-link" href="<?php echo admin_url('proposals?eid=' .$projectid); ?>">
                      <i class="fa fa-id-card-o"></i>
                      <p><?php echo _l('proposals'); ?></p>
                    </a>
                  </li>
                <?php } 
                  }
                ?>
                <?php if (has_permission('invoices','','view')) { 
                  if($project->is_client == 1) {
                    $permission_array = explode(",",$project->permission);
                    $access = (in_array('Invoices', $permission_array) ? true : false);
                  } else {
                    $access = true;
                  }

                  if($access) {
                ?>
                  <li class="nav-item7">
                    <a class="nav-link" href="<?php echo admin_url('invoices?eid=' .$projectid); ?>">
                      <i class="fa fa-money"></i>
                      <p><?php echo _l('invoices'); ?></p>
                    </a>
                  </li>
               <?php } 
                  }
               ?>
               <?php if (has_permission('addressbook','','create')) { ?>
               <li class="nav-item8"> <a class="nav-link" href="<?php echo admin_url('addressbooks?eid=' .$projectid); ?>" aria-expanded="true"> <i class="fa fa-volume-control-phone"></i>
                 <p><?php echo _l('contacts'); ?></p>
                 </a> </li>
               <?php } ?>
             </ul>
             <!-- /.nav-tabs --> 
             <!-- /.tab-content --> 
           </div>
           <!-- /.tabs --> 
         </div>
         <!-- /.widget-body --> 
        </div>
      </div>
      <div class="widget-bg collaborators-details">
        <div class="widget-heading">
          <h5>Venues <span>(<?php echo (isset($project->venues) ? count($project->venues) : 0); ?>)</span>
            <div class="more-setting">
              <a href="<?php echo admin_url('projects/invite/5/'.$project->id); ?>"><i class="fa fa-plus"></i></a>
              <a href="javascript: void(0);" data-toggle="collapse" data-target="#venue-data" id="venue-collapse"><i class="fa fa-caret-down"></i></a>
            </div>
          </h5>
        </div>
        <div class="widget-body clearfix" id="venue-data">
          <div class="venue-card-default">
            <div class="col-sm-12">
              <table>
                <tbody>       
                <?php if(isset($project->venues) && count($project->venues) > 0) { ?>
                  <?php foreach($project->venues as $venues) { ?>
                    <tr>
                      <td width="40px">
                        <div class="lead-pimg">
                          <?php echo $venues['venuelogo']; ?>
                        </div>
                      </td>
                      <td width="100%">
                        <div class="venue-det">
                          <h3><?php echo isset($venues['venuename']) ? $venues['venuename'] : ''; ?></h3>
                          <div class="invite-tags">
                            <span><?php echo isset($venues['venueemail']) ? $venues['venueemail'] : ''; ?></span>
                            <span><?php echo (isset($venues['venuecontactname']) && $venues['venuecontactname'] != '') ? '(' . $venues['venuecontactname'] . ')' : ''; ?></span>
                          </div>
                        </div>
                      </td>
                      <?php
                        $session_data   = get_session_data(); 
                        $user_type  = $session_data['user_type'];
                        if($user_type == 1) { 
                      ?>
                          <td>
                            <div>
                              <a role="menuitem" class="btn btn-success btn-icon" tabindex="-1" href="javascript: void(0);"onclick="fnViewVenueInvite(<?php echo $projectid; ?>, <?php echo $venues['venueid']; ?>);"><i class="fa fa-eye"></i></a>
                            </div>
                          </td>
                      <?php } ?>  
                    </tr>
                  <?php } ?>
                <?php } else { ?>
                  <tr><td colspan="3">No venues found.</td></tr>
                <?php }?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!-- /.widget-bg --> 
    </div>
  </div>
<script type="text/javascript">
  newDate = new Date(<?php echo date("Y, n - 1, d, H, i",strtotime($project->eventstartdatetime)) ?>); 
  $('#projectCountdown').countdown({until: newDate}); 

  $('#vendor-data').collapse("show"); 

  $('#collaborator-data').collapse("show"); 

  //for vendor
  if($('#vendor-data').hasClass('in')) {
    $("#vendor-collapse i").removeClass('fa-caret-up');
    $("#vendor-collapse i").addClass('fa-caret-down');
  } else {
    $("#vendor-collapse i").removeClass('fa-caret-up');
    $("#vendor-collapse i").addClass('fa-caret-down');
  }

  $("#vendor-collapse").click(function(){
    if($('#vendor-data').hasClass('in')) {
      $("#vendor-collapse i").removeClass('fa-caret-down');
      $("#vendor-collapse i").addClass('fa-caret-up');
    } else {
      $("#vendor-collapse i").removeClass('fa-caret-up');
      $("#vendor-collapse i").addClass('fa-caret-down');
    }
  });

  //for collaborator
  if($('#collaborator-data').hasClass('in')) {
    $("#collaborator-collapse i").removeClass('fa-caret-up');
    $("#collaborator-collapse i").addClass('fa-caret-down');
  } else {
    $("#collaborator-collapse i").removeClass('fa-caret-up');
    $("#collaborator-collapse i").addClass('fa-caret-down');
  }

  $("#collaborator-collapse").click(function(){
    if($('#collaborator-data').hasClass('in')) {
      $("#collaborator-collapse i").removeClass('fa-caret-down');
      $("#collaborator-collapse i").addClass('fa-caret-up');
    } else {
      $("#collaborator-collapse i").removeClass('fa-caret-up');
      $("#collaborator-collapse i").addClass('fa-caret-down');
    }
  });

  //for venue
  if($('#venue-data').hasClass('in')) {
    $("#venue-collapse i").removeClass('fa-caret-up');
    $("#venue-collapse i").addClass('fa-caret-down');
  } else {
    $("#venue-collapse i").removeClass('fa-caret-up');
    $("#venue-collapse i").addClass('fa-caret-down');
  }

  $("#venue-collapse").click(function(){
    if($('#venue-data').hasClass('in')) {
      $("#venue-collapse i").removeClass('fa-caret-down');
      $("#venue-collapse i").addClass('fa-caret-up');
    } else {
      $("#venue-collapse i").removeClass('fa-caret-up');
      $("#venue-collapse i").addClass('fa-caret-down');
    }
  });

  function fnViewInvite(projectid, staffid, addressbookid, isvendor, iscollaborator) {
    $.ajax({
      method: 'post',
      async: false,
      url: '<?php echo admin_url(); ?>projects/viewinvite',
      data: 'projectid='+projectid+'&isvendor='+isvendor+'&iscollaborator='+iscollaborator+'&isparent='+0+'&staffid='+staffid+'&addressbookid='+addressbookid,
      dataType: "html",
      success: function(data) {
        $(".ie-dt-fix").html(data);        
        $('#view_invites').modal('show');
      }
    });
  }

  function fnViewVenueInvite(projectid, venueid) {
    $.ajax({
      method: 'post',
      async: false,
      url: '<?php echo admin_url(); ?>projects/viewinvite',
      data: 'projectid='+projectid+'&venueid='+venueid+'&isparent='1,
      dataType: "html",
      success: function(data) {
        $(".ie-dt-fix").html(data);        
        $('#view_invites').modal('show');
      }
    });
  }
</script>