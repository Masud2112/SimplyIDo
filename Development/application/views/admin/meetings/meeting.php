<?php init_head();
if (isset($meeting) && isset($meeting->location)) {
    $location = $meeting->location;
}
?>
<div id="wrapper">
    <div class="content meeting-page">
        <div class="row">
            <?php echo form_open($this->uri->uri_string(), array('class' => 'meeting-form')); ?>
            <div class="col-sm-12">
                <div class="breadcrumb">
                    <?php /*if (isset($pg) && $pg == 'home') { */ ?>
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php /*} */ ?>
                    <?php if (isset($lid)) { ?>
                        <a href="<?php echo admin_url('leads/'); ?>">Leads</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('leads/dashboard/' . $lid); ?>"><?php echo($lname); ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('meetings') . '?lid=' . $lid; ?>">Meetings</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } elseif (isset($pid)) { ?>
                        <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('projects/dashboard/' . $pid); ?>"><?php echo($lname); ?></a>
                        <?php if ($parent_id > 0) { ?>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('projects/dashboard/') . $parent_id; ?>"><?php echo get_project_name_by_id($parent_id); ?></a>                                            <?php } ?>

                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('meetings') . '?pid=' . $pid; ?>">Meetings</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php }elseif (isset($eid)) { ?>
                        <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                        <?php if ($parent_id > 0) { ?>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('projects/dashboard/') . $parent_id; ?>"><?php echo get_project_name_by_id($parent_id); ?></a>
                        <?php } ?>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('projects/dashboard/' . $eid); ?>"><?php echo($lname); ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('meetings') . '?eid=' . $eid; ?>">Meetings</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } else { ?>
                        <a href="<?php echo admin_url('meetings'); ?>">Meetings</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } ?>
                    <span><?php echo isset($meeting) ? $meeting->name : "New Meeting" ?></span>
                </div>
                <h1 class="pageTitleH1"><i class="fa fa-handshake-o"></i><?php echo $title; ?></h1>

                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <?php $attrs = (isset($meeting) ? array() : array('autofocus' => true)); ?>
                        <?php $value = (isset($meeting) ? $meeting->name : ''); ?>
                        <div class="row">
                            <div class="col-sm-6">
                                <?php echo render_input('name', 'meetings_dt_name', $value, 'text', $attrs); ?>
                            </div>
                            <div class="col-sm-6">
                                <?php $selected = (isset($meeting) ? $meeting->status : ''); ?>
                                <?php echo render_select('status', $meeting_status, array('statusid', 'name'), 'Status', $selected); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group" id="meetingstartdate">
                                    <?php
                                    if (isset($_GET['from_dt'])) {
                                        $from_dt = date_create($_GET['from_dt']);
                                        $from_dt = date_format($from_dt, 'm/d/Y H:i');
                                        $start_date = _dt($from_dt, true);
                                    } else {
                                        $start_date = isset($meeting->start_date) ? _dt($meeting->start_date, true) : "";
                                    }
                                    ?>
                                    <?php echo render_datetime_input('start_date', 'From', $start_date); ?>
                                    <!-- <label class="form-control-label">Meeting Date</label>
                    <div class="input-datetimerange input-group ">
                    <input type="text" class="form-control datetimepicker" data-date-min-date= "<?php //echo date('m-d-Y H:i');?>" name="start_date" value="<?php //echo $start_date; ?>" id="start_date"> <span class="input-group-addon bg-info text-inverse meeting-date-mid">to</span>
                    <input data-date-min-date= "<?php //echo date('m-d-Y H:i');?>" data-date-end-date= "" type="text" class="form-control datetimepicker" name="end_date" value="<?php //echo $end_date; ?>">
                    </div> -->
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group" id="meetingenddate">
                                    <?php $end_date = isset($meeting->end_date) ? _dt($meeting->end_date, true) : ""; ?>
                                    <?php echo render_datetime_input('end_date', 'To', $end_date); ?>
                                    <!-- <label class="form-control-label">Meeting Date</label>
                  <div class="input-datetimerange input-group ">
                  <input type="text" class="form-control datetimepicker" data-date-min-date= "<?php //echo date('m-d-Y H:i');?>" name="start_date" value="<?php //echo $start_date; ?>" id="start_date"> <span class="input-group-addon bg-info text-inverse meeting-date-mid">to</span>
                  <input data-date-min-date= "<?php //echo date('m-d-Y H:i');?>" data-date-end-date= "" type="text" class="form-control datetimepicker" name="end_date" value="<?php //echo $end_date; ?>">
                  </div> -->
                                </div>
                            </div>
                            <!-- <div class="col-sm-6">
                <label for="duration" class="control-label">Duration</label>
                <select id="duration" class="selectpicker" name="duration" data-width="100%" data-none-selected-text="Select" data-live-search="true">
                <option value=""></option>
                <?php //foreach($durations as $k => $v){
                            //$selected1 = "";
                            //if(isset($meeting)){
                            //if($meeting->duration == $k){
                            //$selected1 = "selected='selected'";
                            //}
                            //} else {
                            //$selected1= "";
                            //}
                            //echo '<option value = "'.$k.'" '.$selected1.'>'.$v.'</option>';
                            //}
                            ?>
                </select>
                </div>
                -->
                        </div>
                        <div class="row">
                            <?php
                            $rel_type = '';
                            $rel_id = '';
                            if (isset($meeting) || ($this->input->get('rel_id') && $this->input->get('rel_type'))) {
                                if ($this->input->get('rel_id')) {
                                    $rel_id = $this->input->get('rel_id');
                                    $rel_type = $this->input->get('rel_type');
                                } else {
                                    $rel_id = $meeting->rel_id;
                                    $rel_type = $meeting->rel_type;
                                }
                            } elseif (isset($lid)) {
                                $rel_id = $lid;
                                $rel_type = 'lead';
                            } elseif (isset($pid)) {
                                $rel_id = $pid;
                                $rel_type = 'project';
                            } elseif (isset($eid)) {
                                $rel_id = $eid;
                                $rel_type = 'event';
                            } ?>
                            <div class="clearfix"></div>

                            <div class="col-sm-6 meeting_location">
                                <?php /*$value = (isset($meeting) ? $meeting->location : ''); */ ?><!--
                                --><?php /*echo render_input('location', 'meeting_add_edit_location', $value); */ ?>
                                <div class="form-group">
                                    <label class="control-label" for="location">
                                        <?php echo _l('meeting_add_edit_location'); ?>
                                    </label>
                                    <div class="location_action pull-right inline-block">
                                        <a href="#" id="location_edit"
                                           class="<?php echo isset($location) ? "" : 'hide'; ?>"><i
                                                    class="fa fa-pencil"></i></a>
                                        <a href="#" id="location_new" class="mleft4"><i class="fa fa-plus"></i></a>
                                    </div>
                                    <?php
                                    $location_name = (isset($location) ? $location->location_name : '');

                                    ?>
                                    <?php $locationid = (isset($location) ? $location->locationid : ''); ?>
                                    <select id="location" name="location" class="form-control selectpicker"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                            data-live-search="true">
                                        <option value=0></option>
                                        <?php foreach ($locations as $loc) { ?>
                                            <option value="<?php echo $loc->locationid ?>" <?php echo $loc->locationid == $locationid ? "selected" : "" ?> >
                                                <?php echo $loc->location_name ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <!--<input id="location" type="text" name="location"
                                           class="form-control" value="<?php /*echo $location_name */ ?>"/>-->

                                <div class="location_fields existing">
                                    <input id="locationid" class="form-control" type="hidden"
                                           name="location[locationid]"
                                           value="<?php echo $locationid ?>">
                                    <div class="form-group">
                                        <label class="control-label"
                                               for="location_name"><?php echo _l('location_name'); ?></label>
                                        <?php $location_name = (isset($location) ? $location->location_name : ''); ?>
                                        <input id="existing_location_name" type="text" name="location_name"
                                               class="form-control" value="<?php echo $location_name ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="loc_autocomplete_existing"><?php echo _l('address_search'); ?></label>
                                        <input id="loc_autocomplete_existing" class="form-control searchmap"
                                               data-addmap="0"
                                               placeholder="Search Google Maps..." onFocus="geolocate()"
                                               type="text">
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?php $address = (isset($location) ? $location->address : ''); ?>
                                            <div class="form-group">
                                                <label for="location_street_number"
                                                       class="control-label">Address</label>
                                                <input type="text" id="location_street_number"
                                                       name="location[street_number]"
                                                       class="form-control" value="<?php echo $address ?>">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?php $address2 = (isset($location) ? $location->address2 : ''); ?>
                                            <?php //echo render_input('location[route]', 'Address2',$address2); ?>
                                            <div class="form-group">
                                                <label for="location_route"
                                                       class="control-label">Address2</label>
                                                <input type="text" id="location_route" name="location[route]"
                                                       class="form-control" value="<?php echo $address2 ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <?php $city = (isset($location) ? $location->city : ''); ?>
                                            <?php //echo render_input('location[locality]', 'client_city', $city); ?>
                                            <div class="form-group">
                                                <label for="location_locality" class="control-label">City</label>
                                                <input type="text" id="location_locality" name="location[locality]"
                                                       class="form-control" value="<?php echo $city ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php $state = (isset($location) ? $location->state : ''); ?>
                                            <?php //echo render_input('location[administrative_area_level_1]', 'client_state', $state); ?>
                                            <div class="form-group">
                                                <label for="location_administrative_area_level_1"
                                                       class="control-label">State</label>
                                                <input type="text" id="location_administrative_area_level_1"
                                                       name="location[administrative_area_level_1]"
                                                       class="form-control" value="<?php echo $state ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <?php $zip = (isset($location) ? $location->zip : ''); ?>
                                            <?php //echo render_input('location[postal_code]', 'client_postal_code', $zip); ?>
                                            <div class="form-group">
                                                <label for="location_postal_code" class="control-label">Zip
                                                    code</label>
                                                <input type="text" id="location_postal_code"
                                                       name="location[postal_code]"
                                                       class="form-control" value="<?php echo $zip ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="location_country"
                                                       class="control-label">Country</label>
                                                <select name="location[country]" id="location_country"
                                                        class="form-control selectpicker"
                                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <option value="US" selected>United States</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button class="btn btn-danger delete_loc">DELETE</button>
                                        <button class="btn btn-default cancel_loc">CANCEL</button>
                                        <button class="btn btn-info edit_loc">SAVE</button>
                                    </div>
                                </div>
                                <div class="location_fields new">
                                    <div class="form-group">
                                        <label class="control-label"
                                               for="location_name"><?php echo _l('location_name'); ?></label>
                                        <input id="new_location_name" type="text" name="location_name"
                                               class="form-control ">
                                    </div>
                                    <div class="form-group">
                                        <label for="loc_autocomplete"><?php echo _l('address_search'); ?></label>
                                        <input id="loc_autocomplete_new" class="form-control searchmap"
                                               data-addmap="0"
                                               placeholder="Search Google Maps..." onFocus="geolocate()"
                                               type="text">
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?php //$address = (isset($location) ? $location->address : ''); ?>
                                            <div class="form-group">
                                                <label for="newlocation_street_number"
                                                       class="control-label">Address</label>
                                                <input type="text" id="newlocation_street_number"
                                                       name="newlocation[street_number]"
                                                       class="form-control new_location_address" value="<?php //echo $address ?>">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <?php //$address2 = (isset($location) ? $location->address2 : ''); ?>
                                            <?php //echo render_input('location[route]', 'Address2',$address2); ?>
                                            <div class="form-group">
                                                <label for="newlocation_route"
                                                       class="control-label">Address2</label>
                                                <input type="text" id="newlocation_route" name="newlocation[route]"
                                                       class="form-control new_location_address2" value="<?php //echo $address2 ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <?php //$city = (isset($location) ? $location->city : ''); ?>
                                            <?php //echo render_input('location[locality]', 'client_city', $city); ?>
                                            <div class="form-group">
                                                <label for="newlocation_locality" class="control-label">City</label>
                                                <input type="text" id="newlocation_locality" name="newlocation[locality]"
                                                       class="form-control new_location_city" value="<?php //echo $city ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <?php //$state = (isset($location) ? $location->state : ''); ?>
                                            <?php //echo render_input('location[administrative_area_level_1]', 'client_state', $state); ?>
                                            <div class="form-group">
                                                <label for="newlocation_administrative_area_level_1"
                                                       class="control-label">State</label>
                                                <input type="text" id="newlocation_administrative_area_level_1"
                                                       name="newlocation[administrative_area_level_1]"
                                                       class="form-control new_location_state" value="<?php //echo $state ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <?php //$zip = (isset($location) ? $location->zip : ''); ?>
                                            <?php //echo render_input('location[postal_code]', 'client_postal_code', $zip); ?>
                                            <div class="form-group">
                                                <label for="newlocation_postal_code" class="control-label">Zip
                                                    code</label>
                                                <input type="text" id="newlocation_postal_code"
                                                       name="newlocation[postal_code]"
                                                       class="form-control new_location_zip" value="<?php //echo $zip ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="newlocation_country"
                                                       class="control-label">Country</label>
                                                <select name="newlocation[country]" id="newlocation_country"
                                                        class="form-control selectpicker"
                                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <option value="US" selected>United States</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <button class="btn btn-default cancel_loc">CANCEL</button>
                                        <button class="btn btn-info save_loc">SAVE</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="default_timezone"
                                           class="control-label"><?php echo _l('settings_localization_default_timezone'); ?></label>
                                    <select name="default_timezone" id="default_timezone"
                                            class="form-control selectpicker"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                            data-live-search="true">
                                        <?php //foreach(get_timezones_list() as $key => $timezones) { ?>
                                        <!--<optgroup label="<?php //echo $key; ?>">-->
                                        <?php foreach (get_timezones_list() as $key => $timezone) {
                                            $selectedt = "";
                                            if (isset($meeting)) {
                                                if ($meeting->default_timezone == "") {
                                                    if (get_option('default_timezone') == $key) {
                                                        $selectedt = "selected='selected'";
                                                    }
                                                } elseif ($meeting->default_timezone == $key) {
                                                    $selectedt = "selected='selected'";
                                                }
                                            } else {
                                                if (get_option('default_timezone') == $key) {
                                                    $selectedt = "selected='selected'";
                                                }
                                            }
                                            $timezone_name = str_replace("America/", "", $timezone);
                                            $timezone_name = str_replace("_", " ", $timezone_name);
                                            ?>
                                            <option value="<?php echo $key; ?>" <?php echo $selectedt; ?>><?php echo $timezone_name; ?></option>
                                        <?php } ?>
                                        <!--</optgroup>-->
                                        <?php //} ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <?php $selectedstaff = array();
                                if (isset($meeting->users)) {
                                    foreach ($meeting->users as $user) {
                                        array_push($selectedstaff, $user);
                                    }
                                }
                                echo render_select('users[]', $users, array('staffid', array('firstname', 'lastname')), 'Team Members', $selectedstaff, array('multiple' => true), array(), '', '', false);
                                ?>
                            </div>
                            <div class="col-sm-6">
                                <?php $selectedcontacts = array();
                                if (isset($meeting->contacts)) {
                                    foreach ($meeting->contacts as $contact) {
                                        array_push($selectedcontacts, $contact);
                                    }
                                }
                                echo render_select('contacts[]', $contacts, array('addressbookid', array('firstname', 'lastname')), 'Contacts', $selectedcontacts, array('multiple' => true), array(), '', '', false);
                                ?>
                            </div>
                        </div>
                        <!-- <div class="row">
                <div class="col-sm-6">
                  <?php //$selectedleads = array();
                        //   if(isset($meeting->leads)) {
                        //     foreach($meeting->leads as $lead) {
                        //       array_push($selectedleads,$lead);
                        //     }
                        //   }
                        //   else if(isset($lid)) {
                        //     foreach($leads as $lead) {
                        //       if($lead['id'] == $lid) {
                        //          array_push($selectedleads,$lead['id']);
                        //       }
                        //     }
                        //   }
                        // echo render_select('leads[]',$leads,array('id','name'),'Leads',$selectedleads,array('multiple'=>true),array(),'','',false);
                        ?>
                </div>
              </div> -->
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="rel_type"
                                           class="control-label"><?php echo _l('task_related_to'); ?></label>
                                    <select name="rel_type" class="selectpicker" id="rel_type" data-width="100%"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value=""></option>
                                        <!--
                          <option value="invoice" <?php //if(isset($meeting) || $this->input->get('rel_type')){if($rel_type == 'invoice'){echo 'selected';}} ?>>
                             <?php //echo _l('invoice'); ?>
                          </option>
                          <option value="customer"
                             <?php //if(isset($meeting) || $this->input->get('rel_type')){if($rel_type == 'customer'){echo 'selected';}} ?>>
                             <?php //echo _l('client'); ?>
                          </option>
                          <option value="estimate" <?php //if(isset($meeting) || $this->input->get('rel_type')){if($rel_type == 'estimate'){echo 'selected';}} ?>>
                             <?php //echo _l('estimate'); ?>
                          </option>
                          <option value="contract" <?php //if(isset($meeting) || $this->input->get('rel_type')){if($rel_type == 'contract'){echo 'selected';}} ?>>
                             <?php //echo _l('contract'); ?>
                          </option>
                          <option value="ticket" <?php //if(isset($meeting) || $this->input->get('rel_type')){if($rel_type == 'ticket'){echo 'selected';}} ?>>
                             <?php //echo _l('ticket'); ?>
                          </option>
                          <option value="expense" <?php //if(isset($meeting) || $this->input->get('rel_type')){if($rel_type == 'expense'){echo 'selected';}} ?>>
                             <?php //echo _l('expense'); ?>
                          </option> -->
                                        <?php if (isset($lid) || (!isset($eid) && !isset($pid))) { ?>
                                            <option value="lead" <?php if (isset($meeting) || isset($lid) || $this->input->get('rel_type')) {
                                                if ($rel_type == 'lead') {
                                                    echo 'selected';
                                                }
                                            } ?>>
                                                <?php echo _l('lead'); ?>
                                            </option>
                                        <?php } ?>
                                        <?php if (isset($pid) || (!isset($eid) && !isset($lid))) { ?>
                                            <option value="project" <?php if (isset($meeting) || isset($pid) || $this->input->get('rel_type')) {
                                                if ($rel_type == 'project') {
                                                    echo 'selected';
                                                }
                                            } ?>>
                                                <?php echo _l('project'); ?>
                                            </option>
                                        <?php } ?>
                                        <?php if ((isset($pid) || isset($eid)) || !isset($lid)) { ?>
                                            <option value="event" <?php if (isset($meeting) || isset($eid) || $this->input->get('rel_type')) {
                                                if ($rel_type == 'event') {
                                                    echo 'selected';
                                                }
                                            } ?>>
                                                Sub-Projects
                                            </option>
                                        <?php } ?>
                                        <!-- <option value="proposal" <?php //if(isset($meeting) || $this->input->get('rel_type')){if($rel_type == 'proposal'){echo 'selected';}} ?>>
                             <?php //echo _l('proposal'); ?>
                          </option> -->
                                    </select>
                                </div>
                            </div>
                            <?php if (isset($lid) || (!isset($eid) && !isset($pid))) { ?>
                                <div class="col-sm-6 lead-search <?php echo $rel_type == "lead" ? "" : "hide"; ?>">
                                    <?php $selectedleads = array();
                                    $selectedleads = $rel_id != "" ? $rel_id : "";
                                    echo render_select('lead', $leads, array('id', 'name'), 'Leads', $selectedleads, array(), array(), '', '', false);
                                    ?>
                                </div>
                            <?php } ?>
                            <?php if (isset($pid) || (!isset($eid) && !isset($lid))) { ?>
                                <div class="col-sm-6 project-search <?php echo $rel_type == "project" ? "" : "hide"; ?>">
                                    <?php $selectedprojects = array();
                                    $selectedprojects = $rel_id != "" ? $rel_id : "";
                                    echo render_select('project', $projects, array('id', 'name'), 'Projects', $selectedprojects, array(), array(), '', '', false);
                                    ?>
                                </div>
                            <?php } ?>
                            <?php if ((isset($pid) || isset($eid)) || !isset($lid)) { ?>
                                <div class="col-sm-6 event-search <?php echo $rel_type == "event" ? "" : "hide"; ?>">
                                    <?php $selectedevents = array();
                                    $selectedevents = $rel_id != "" ? $rel_id : "";
                                    echo render_select('event', $events, array('id', 'name'), 'Sub-Projects', $selectedevents, array(), array(), '', '', false);
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label for="color"
                                       class="control-label"><?php echo _l('meeting_add_edit_color'); ?></label>
                                <?php
                                $event_colors = '';
                                $favourite_colors = get_system_favourite_colors();
                                $i = 0;
                                $meeting_color = "";
                                if (isset($meeting)) {
                                    if (in_array($meeting->color, $favourite_colors)) {
                                        $meeting_color = $meeting->color;
                                    } else {
                                        $meeting_color = $favourite_colors[0];
                                    }
                                } else {
                                    $meeting_color = $favourite_colors[0];
                                }

                                foreach ($favourite_colors as $color) {
                                    $color_selected_class = 'cpicker-small';
                                    if ($meeting_color == $color) {
                                        $color_selected_class = 'cpicker-big';
                                    }
                                    $event_colors .= "<div class='calendar-cpicker cpicker " . $color_selected_class . "' data-color='" . $color . "' style='background:" . $color . ";border:1px solid " . $color . "'></div>";
                                    $i++;
                                }

                                echo '<div class="cpicker-wrapper">';
                                echo $event_colors;
                                echo '</div>';
                                echo form_hidden('color', $meeting_color);
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <?php $desc = isset($meeting->description) ? $meeting->description : ""; ?>
                                <?php echo render_textarea('description', 'Description', $desc, array('rows' => 5)); ?>
                            </div>
                        </div>
                        <div class="" id="field">
                            <h4><?php echo _l('meeting_add_edit_reminder'); ?></h4>
                            <hr class="hr-panel-heading"/>
                            <?php
                            if (isset($meeting->reminders) && count($meeting->reminders) > 0) {
                                $i = 0;
                                foreach ($meeting->reminders as $meetingreminder) {
                                    ?>
                                <div class="row" id="field-<?php echo $i; ?>">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="duration"
                                                   class="control-label"><?php echo _l('meeting_add_edit_duration'); ?></label>
                                            <input type="number" id="reminder[<?php echo $i; ?>][duration]"
                                                   name="reminder[<?php echo $i; ?>][duration]" class="form-control"
                                                   value="<?php echo $meetingreminder['duration']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label"
                                                   for="reminder[<?php echo $i; ?>][meetinginterval]"><?php echo _l('meeting_add_edit_interval'); ?></label>
                                            <select id="reminder[<?php echo $i; ?>][meetinginterval]"
                                                    name="reminder[<?php echo $i; ?>][meetinginterval]"
                                                    class="selectpicker" data-width="100%"
                                                    data-none-selected-text="Select">
                                                <?php
                                                foreach ($reminders as $kr => $vr) {
                                                    if ($kr == $meetingreminder['meetinginterval']) {
                                                        $selected = "selected='selected'";
                                                    } else {
                                                        $selected = "";
                                                    }
                                                    echo '<option value = "' . $kr . '" ' . $selected . '>' . $vr . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <?php if ($i != 0) { ?>
                                        <div class="col-sm-2" id="divremove<?php echo $i; ?>">
                                            <button id="remove<?php echo $i; ?>" class="btn btn-danger remove-me">
                                                Remove
                                            </button>
                                        </div>

                                        <?php
                                    }
                                    $i++; ?></div><?php
                                }
                            } else { ?>
                                <div id="field-0" class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="duration"
                                                   class="control-label"><?php echo _l('meeting_add_edit_duration'); ?></label>
                                            <input type="number" id="reminder[0][duration]" name="reminder[0][duration]"
                                                   class="form-control" value="">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="control-label"
                                                   for="reminder[0][meetinginterval]"><?php echo _l('meeting_add_edit_interval'); ?></label>
                                            <select id="reminder[0][meetinginterval]"
                                                    name="reminder[0][meetinginterval]" class="selectpicker"
                                                    data-width="100%" data-none-selected-text="Select">
                                                <?php foreach ($reminders as $kr => $vr) {
                                                    $selected2 = "";
                                                    if (isset($meeting)) {
                                                        if ($meeting->reminder == $kr) {
                                                            $selected2 = "selected='selected'";
                                                        }
                                                    } else {
                                                        $selected2 = "";
                                                    }
                                                    echo '<option value = "' . $kr . '" ' . $selected2 . '>' . $vr . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <div class="col-sm-9 text-right">
                                <button id="add-more" name="add-more" class="btn btn-primary">Add More</button>
                            </div>
                        </div>
                        <?php if (!isset($meeting)) { ?>
                            <h4 class="no-margin"><?php echo _l('meeting_add_edit_notes'); ?></h4>
                            <hr class="hr-panel-heading"/>
                            <?php echo render_textarea('note_description'); ?>
                        <?php } ?>
                        <div class="topButton">
                            <button class="btn btn-default" type="button"
                                    onclick="fncancel();"><?php echo _l('Cancel'); ?></button>
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="hdnlid" value="<?php echo isset($lid) ? $lid : ''; ?>">
            <input type="hidden" name="hdnpid" value="<?php echo isset($pid) ? $pid : ''; ?>">
            <input type="hidden" name="hdneid" value="<?php echo isset($eid) ? $eid : ''; ?>">
            <input type="hidden" name="pg" value="<?php echo isset($pg) ? $pg : ''; ?>">
            <?php echo form_close(); ?>
            <?php if (isset($meeting)) { ?>
                <div class="col-sm-12">
                    <div class="panel_s">
                        <div class="panel-body">
                            <h4 class="no-margin"><?php echo _l('meeting_add_edit_notes'); ?></h4>
                            <hr class="hr-panel-heading"/>
                            <?php echo form_open(admin_url('meetings/add_note/' . $meeting->meetingid), array('id' => 'meeting-notes')); ?>
                            <?php echo render_textarea('description'); ?>
                            <input type="hidden" name="hdnlid" value="<?php echo isset($lid) ? $lid : ''; ?>">
                            <input type="hidden" name="hdnpid" value="<?php echo isset($pid) ? $pid : ''; ?>">
                            <input type="hidden" name="hdneid" value="<?php echo isset($eid) ? $eid : ''; ?>">
                            <input type="hidden" name="pg" value="<?php echo isset($pg) ? $pg : ''; ?>">
                            <button type="submit" class="btn btn-info pull-right"><?php echo _l('save'); ?></button>
                            <div class="clearfix"></div>
                            <?php echo form_close(); ?>
                            <hr/>
                            <div class="panel_s mtop20">
                                <?php
                                $len = count($notes);
                                $i = 0;
                                foreach ($notes as $note) { ?>
                                    <div class="media meeting-note">
                                        <!--<a href="<?php //echo admin_url('profile/'.$note["addedfrom"]); ?>" target="_blank">-->
                                        <?php echo staff_profile_image($note['addedfrom'], array('staff-profile-image-small', 'pull-left mright10')); ?>
                                        <!--</a>-->
                                        <div class="media-body">
                                            <?php if ($note['addedfrom'] == get_staff_user_id() || is_admin()) { ?>
                                                <div class="pull-right text-right">
                                                    <div><a class='show_act' href='javascript:void(0)'><i
                                                                    class='fa fa-ellipsis-v' aria-hidden='true'></i></a>
                                                    </div>
                                                    <div class='table_actions'>
                                                        <ul>
                                                            <li>
                                                                <a href="#" class="pull-right"
                                                                   onclick="delete_meeting_note(this,<?php echo $note['id']; ?>);return false;">
                                                                    <i class="fa fa fa-times"></i>DELETE</a>
                                                            </li>
                                                            <li>
                                                                <a href="#" class="pull-right"
                                                                   onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;">
                                                                    <i class="fa fa-pencil-square-o"></i>EDIT</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php if (!empty($note['date_contacted'])) { ?>
                                                <span data-toggle="tooltip"
                                                      data-title="<?php echo _dt($note['date_contacted']); ?>">
                            <i class="fa fa-phone-square text-success font-medium valign" aria-hidden="true"></i>
                          </span>
                                            <?php } ?>
                                            <small>
                                                <?php
                                                /**
                                                 * Added By : Vaidehi
                                                 * Dt : 11/14/2017
                                                 * to display datetime in give timezone format
                                                 */

                                                $date = new DateTime($note['dateadded'] . ' ' . $timeoffset);
                                                $date->setTimezone(new DateTimeZone($meeting->default_timezone));

                                                $dt = $date->format('Y-m-d H:i:s');

                                                echo _l('lead_note_date_added', _dt($dt, true));
                                                ?>
                                            </small>
                                            <!--<a href="<?php //echo admin_url('profile/'.$note["addedfrom"]); ?>" target="_blank">-->
                                            <h5 class="media-heading bold"><?php echo get_staff_full_name($note['addedfrom']); ?></h5>
                                            <!--</a>-->
                                            <div data-note-description="<?php echo $note['id']; ?>" class="text-muted">
                                                <?php echo $note['description']; ?>
                                            </div>
                                            <div data-note-edit-textarea="<?php echo $note['id']; ?>"
                                                 class="hide mtop15">
                                                <?php echo render_textarea('note', '', $note['description']); ?>
                                                <div class="text-right ">
                                                    <button type="button" class="btn btn-default"
                                                            onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                                                    <button type="button" class="btn btn-info"
                                                            onclick="edit_note(<?php echo $note['id']; ?>);"><?php echo _l('update_note'); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($i >= 0 && $i != $len - 1) {
                                            echo '<hr />';
                                        }
                                        ?>
                                    </div>
                                    <?php $i++;
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php app_external_form_footer('meeting'); ?>
<?php init_tail(); ?>
<script>
    $(function () {
        // $('select[name="role"]').on('change', function() {
        //   var roleid = $(this).val();
        //   init_roles_permissions(roleid, true);
        // });
        //init_roles_permissions();
    });
</script>
<script>
    jQuery.validator.addMethod("greaterThan",
        function (value, element, params) {
            if (!/Invalid|NaN/.test(new Date(value))) {
                return new Date(value) > new Date($(params).val());
            }

            return isNaN(value) && isNaN($(params).val())
                || (Number(value) > Number($(params).val()));
        }, 'Must be greater than Start Date.');

    _validate_form($('.meeting-form'), {
        name: 'required',
        status: 'required',
        //  duration:'required',
        start_date: {
            required: true
        },
        //location: 'required',
        users: 'required',
        contacts: 'required',
        // leads:'required',
        end_date: {required: true, greaterThan: start_date},
        default_timezone: 'required',
        location_name: 'required',
    });

    //Added By Avni on 10/18/2017
    $('#meetingstartdate #start_date').change(function (e) {
        var selected = e.target.value;
        if (selected != '') {
            $.ajax({
                url: "<?php echo admin_url('meetings/getmeetingendate')?>",
                data: "startdate=" + selected,
                method: "post",
                success: function (result) {
                    $('#meetingenddate #end_date').val(result);
                }
            });
        }
    });

    var reminders = <?php echo json_encode($reminders); ?>;

    $("#add-more").click(function (e) {
        e.preventDefault();
        var my_fields = $("div[id^='field-']");
        var highest = -Infinity;
        $.each(my_fields, function (mindex, mvalue) {
            var fieldNum = mvalue.id.split("-");
            highest = Math.max(highest, parseFloat(fieldNum[1]));
        });

        var next = highest;
        var addto = "#field-" + next;
        var addRemove = "#field-" + (next);

        next = next + 1;
        var newIn = "";
        newIn += ' <div class="row" id="field-' + next + '"><div class="col-sm-3"><div class="form-group"><label class="control-label" for="reminder[' + next + '][duration]">Duration</label><input type="number" name="reminder[' + next + '][duration]" id="reminder[' + next + '][duration]" class="form-control"/></div>';

        newIn += '</select></div>';
        newIn += '<div class="col-sm-3"><div class="form-group"><label class="control-label" for="reminder[' + next + '][meetinginterval]">Interval</label><select id="reminder[' + next + '][meetinginterval]" name="reminder[' + next + '][meetinginterval]" class="selectpicker" data-width="100%" data-none-selected-text="Select">';
        $.each(reminders, function (rindex, rvalue) {
            newIn += '<option value="' + rindex + '">' + rvalue + '</option>';
        });

        newIn += '</select></div></div>';
        newIn += '<div class="col-sm-2" id="divremove' + (next) + '"><button id="remove' + (next) + '" class="btn btn-danger remove-me" >Remove</button></div></div>';

        var newInput = $(newIn);

        //var removeButton = $(removeBtn);
        $(addto).after(newInput);
        //$(addRemove).after(removeButton);
        $("#field-" + next).attr('data-source', $(addto).attr('data-source'));
        $("#count").val(next);

        $('.remove-me').click(function (e) {
            e.preventDefault();
            var fieldNum = this.id.charAt(this.id.length - 1);
            var fieldID = "#field-" + fieldNum;
            // $(this).remove();
            $(fieldID).remove();
            // $('#divremove'+fieldNum).remove();
        });

        $('.selectpicker').selectpicker('render');
    });

    $('.remove-me').click(function (e) {
        e.preventDefault();
        var fieldNum = this.id.charAt(this.id.length - 1);
        var fieldID = "#field-" + fieldNum;
        //$(this).remove();
        $(fieldID).remove();
        //$('#divremove'+fieldNum).remove();
    });

    function fncancel() {
        var id =<?php if (isset($lid)) {
            echo $lid;
        } else {
            echo '0';
        }  ?>;
        var pid =<?php if (isset($pid)) {
            echo $pid;
        } else {
            echo '0';
        }  ?>;
        var eid =<?php if (isset($eid)) {
            echo $eid;
        } else {
            echo '0';
        }  ?>;
        if (id > '0') {
            location.href = '<?php echo base_url(); ?>admin/meetings?lid=' + id;
        } else if (pid > '0') {
            location.href = '<?php echo base_url(); ?>admin/meetings?pid=' + pid;
        } else if (eid > '0') {
            location.href = '<?php echo base_url(); ?>admin/meetings?eid=' + eid;
        } else {
            window.history.go(-1);
        }
    }
</script>
<script>
    var _rel_id = $('#rel_id'),
        //_rel_type = $('#rel_type'),
        _rel_id_wrapper = $('#rel_id_wrapper'),
        data = {};

    $(function () {

        // var inner_popover_template = '<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>';

        //  $('.trigger').popover({
        //  html: true,
        //  placement: "bottom",
        //  trigger: 'click',
        //  title:"<?php //echo _l('actions'); ?>",
        //  content: function() {
        //   return $('body').find('.content-menu').html();
        //  },
        //    template: inner_popover_template
        // });

        // custom_fields_hyperlink();

        // jQuery.validator.addMethod("greaterThan",
        //  function(value, element, params) {

        //      if (!/Invalid|NaN/.test(new Date(value))) {
        //          return new Date(value) >= new Date($(params).val());
        //      }

        //      return isNaN(value) && isNaN($(params).val())
        //          || (Number(value) > Number($(params).val()));
        //  },'Must be greater than Start Date.');

        _validate_form($('.meeting-form'), {
            name: 'required',
            //startdate: 'required',
            duedate: 'required', //, greaterThan: "#startdate"
            //'assigned[]': 'required',
            status: 'required'
        });

        _validate_form($('#meeting-notes'), {
            description: 'required'
        });

        //$('.rel_id_label').html(_rel_type.find('option:selected').text());
        $("#rel_type").on('change', function () {
            //  var clonedSelect = _rel_id.html('').clone();
            //  _rel_id.selectpicker('destroy').remove();
            //  _rel_id = clonedSelect;
            //  $('#rel_id_select').append(clonedSelect);
            //  $('.rel_id_label').html(_rel_type.find('option:selected').text());

            //  meeting_rel_select();
            //  if($(this).val() != ''){
            //   _rel_id_wrapper.removeClass('hide');
            // } else {
            //   _rel_id_wrapper.addClass('hide');
            // }
            // init_project_details(_rel_type.val());
            var selected = $(this).val();
            if (selected == "lead") {
                $(".lead-search").removeClass("hide");
                $(".project-search").addClass("hide");
                $(".event-search").addClass("hide");
            } else if (selected == "project") {
                $(".project-search").removeClass("hide");
                $(".lead-search").addClass("hide");
                $(".event-search").addClass("hide");
            } else if (selected == "event") {
                $(".event-search").removeClass("hide");
                $(".lead-search").addClass("hide");
                $(".project-search").addClass("hide");
            }
        });

        init_datepicker();
        init_color_pickers();
        init_selectpicker();
        // meeting_rel_select();
        //  $('body').on('change','#rel_id',function(){
        //   if($(this).val() != ''){
        //     if(_rel_type.val() == 'project'){
        //       $.get(admin_url + 'projects/get_rel_project_data/'+$(this).val()+'/'+meetingid,function(project){
        //         $("select[name='milestone']").html(project.milestones);
        //         if(typeof(_milestone_selected_data) != 'undefined'){
        //          $("select[name='milestone']").val(_milestone_selected_data.id);
        //          $('input[name="duedate"]').val(_milestone_selected_data.due_date)
        //        }
        //        $("select[name='milestone']").selectpicker('refresh');
        //        if(project.billing_type == 3){
        //         $('.meeting-hours').addClass('project-meeting-hours');
        //       } else {
        //         $('.meeting-hours').removeClass('project-meeting-hours');
        //       }
        //       init_project_details(_rel_type.val(),project.allow_to_view_meetings);
        //     },'json');
        //     }
        //   }
        // });

        <?php if(!isset($meeting) && $rel_id != ''){ ?>
        _rel_id.change();
        <?php } ?>

    });

    // End code of Add more / Remove address
    //Added By Purvi on 11/10/2017
    // $('#startdate').change(function(e){
    //   var selected = e.target.value;
    //   $('#duedate').val(selected);

    // });

    /*
    ** Added By Sanjay on 02/08/2018
    ** For start-date and end-date
    */


    $(function () {
        $("#end_date").rules('add', {greaterThan: "#start_date"});

        $(".input-group-addon").css({"padding": "0px"});
        $(".fa.fa-calendar.calendar-icon").css({"padding": "6px 12px"});

        $('.input-group-addon').find('.fa-calendar').on('click', function () {
            $(this).parent().siblings('#start_date').trigger('focus');
        });
        $('.input-group-addon').find('.fa-calendar').on('click', function () {
            $(this).parent().siblings('#end_date').trigger('focus');
        });

        url = window.location.href;
        //var date = url.split('?')[1].split('=')[1];
        // if(date)
        // {
        //   var spl_txt = date.split('-');
        //   var time = new Date();
        //   date = spl_txt[1]+"/"+spl_txt[2]+"/"+spl_txt[0]+" "+time.getHours() + ":" + time.getMinutes();
        //   $('#start_date').val(date);
        // }
    });
    $(".searchmap").on("keyup, change, keypress, keydown, click", function () {
        var id = $(this).attr('id')
        initAutocomplete(id);
    });

    var componentForm = {
        street_number: 'short_name',
        /*route: 'long_name',*/
        locality: 'long_name',
        administrative_area_level_1: 'long_name',
        country: 'short_name',
        postal_code: 'short_name'
    };

    function initAutocomplete(id) {
        // Create the autocomplete object, restricting the search to geographical
        // location types.
        var iId = "location";
        if (id == 'loc_autocomplete_new') {
            var iId = "newlocation";
        }
        autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById(id)),
            {types: ['geocode'], componentRestrictions: {country: 'us'}});

        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', function () {
            //google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
            for (var component in componentForm) {
                document.getElementById(iId + "_" + component + "").value = '';
                document.getElementById(iId + "_" + component + "").disabled = false;
            }

            // Get each component of the address from the place details
            // and fill the corresponding field on the form.
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    if (addressType == "street_number") {
                        var val = place.address_components[i][componentForm['street_number']] + " " + place.address_components[1]['long_name'];
                    }
                    document.getElementById(iId + "_" + addressType + "").value = val;
                }
            }
        });

    }

    function geolocate() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var geolocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                var circle = new google.maps.Circle({
                    center: geolocation,
                    radius: position.coords.accuracy
                });
                autocomplete.setBounds(circle.getBounds());
            });
        }
    }

</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB-0SSogvGqWSro2pyjAlek2DP_lwfQMvE&libraries=places"></script>
</body>
</html>