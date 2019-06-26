    <?php init_head(); ?>
<div id="wrapper">
    <div class="content staff-member-page">
        <div class="breadcrumb">
            <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <a href="<?php echo admin_url('setup'); ?>">Settings</a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <a href="<?php echo admin_url('staff'); ?>">Team Members</a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <span><?php echo(isset($member) ? $member->firstname : 'New Team Member'); ?></span>
        </div>
        <h1 class="pageTitleH1"><i class="fa fa-group"></i><?php echo $title; ?></h1>
        <div class="clearfix"></div>
        <div class="row">
            <?php
            /**
             * Added By : Vaidehi
             * Dt : 11/09/2017
             * to check for limit based on package of logged in user
             */
            if ((isset($packagename) && $packagename != "Paid") && (isset($module_active_entries) && ($module_active_entries >= $module_create_restriction)) && !isset($member)) { ?>
                <div class="col-md-<?php if (!isset($member)) {
                    echo '12';
                } else {
                    echo '12';
                } ?>" id="small-table">
                    <div class="panel_s">
                        <div class="panel-body">
                            <div class="warningbox">
                                <h4><?php echo _l('package_limit_restriction_line1', _l('users')); ?></h4>
                                <span><?php echo _l('package_limit_restriction_line2', _l('users')); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn-bottom-toolbar text-right btn-toolbar-container-out">
                    <button class="btn btn-default" type="button"
                            onclick="location.href='<?php echo base_url(); ?>admin/staff'"><?php echo _l('Cancel'); ?></button>
                </div>
            <?php } else { ?>
                <?php if (isset($member)) { ?>
                    <?php
                    // <div class="col-md-12">
                    //    <div class="panel_s">
                    //       <div class="panel-body no-padding-bottom">
                    //          <?php $this->load->view('admin/staff/stats');
                    //       </div>
                    //    </div>
                    // </div>
                    ?>
                    <div class="member">
                        <?php echo form_hidden('isedit'); ?>
                        <?php echo form_hidden('memberid', $member->staffid); ?>
                    </div>
                <?php } ?>
                <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'staff-form', 'autocomplete' => 'off')); ?>
                <div class="col-md-<?php if (!isset($member)) {
                    echo '12';
                } else {
                    echo '12';
                } ?>" id="small-table">
                    <div class="panel_s btmbrd">
                        <div class="panel-body">
                            <!--   <h4 class="no-margin">
                              <?php //echo $title; ?>
                           </h4> -->
                            <h4 class="no-margin">
                                <ul class="nav nav-tabs" id="staff" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#tab_staff_profile" aria-controls="tab_staff_profile" role="tab"
                                           data-toggle="tab">
                                            <?php echo _l('staff_profile_string'); ?>
                                        </a>
                                    </li>
                                    <?php if ($is_sido_admin != true) { ?>
                                        <li role="presentation">
                                            <a href="#tab_staff_permissions" aria-controls="tab_staff_permissions"
                                               role="tab" data-toggle="tab">
                                                <?php echo _l('staff_add_edit_permissions'); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </h4>
                            <div class="tab-content">
                                <div role="tabpanel" class="tab-pane active" id="tab_staff_profile">
                                    <!-- <div class="checkbox checkbox-primary">
                                    <input type="checkbox" value="1" name="two_factor_auth_enabled" id="two_factor_auth_enabled"<?php //if(isset($member) && $member->two_factor_auth_enabled == 1){//echo ' checked';} ?>>
                                    <label for="two_factor_auth_enabled"><i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php //echo _l('two_factor_authentication_info'); ?>"></i>
                                    <?php //echo _l('enable_two_factor_authentication'); ?></label>
                                 </div>
                                 <div class="is-not-staff<?php //if(isset($member) && $member->admin == 1){ echo ' hide'; }?>">
                                    <div class="checkbox checkbox-primary">
                                       <?php
                                    //$checked = '';
                                    //if(isset($member)) {
                                    //if($member->is_not_staff == 1){
                                    //$checked = ' checked';
                                    //}
                                    //}
                                    ?>
                                       <input type="checkbox" value="1" name="is_not_staff" id="is_not_staff" <?php echo $checked; ?>>
                                       <label for="is_not_staff"><?php //echo _l('is_not_staff_member'); ?></label>
                                    </div>
                                    <hr />
                                 </div> -->
                                    <?php /*if ((isset($member) && $member->profile_image == NULL) || !isset($member)) { */ ?><!--
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="profile_image"
                                                           class="profile-image"><?php /*echo _l('staff_edit_profile_image'); */ ?></label>
                                                    <div class="input-group">
                                             <span class="input-group-btn">
                                               <span class="btn btn-primary"
                                                     onclick="$(this).parent().find('input[type=file]').click();">Browse</span>
                                               <input name="profile_image"
                                                      onchange="$(this).parent().parent().find('.form-control').html($(this).val().split(/[\\|/]/).pop());"
                                                      style="display: none;" type="file">
                                             </span>
                                                        <span class="form-control"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    --><?php /*} */ ?>
                                    <?php /*if (isset($member) && $member->profile_image != NULL) { */ ?><!--
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-md-9">
                                                            <?php /*echo staff_profile_image($member->staffid, array('img', 'img-responsive', 'staff-profile-image-thumb'), 'thumb'); */ ?>
                                                        </div>
                                                        <div class="col-md-3 text-right">
                                                            <a href="<?php /*echo admin_url('staff/remove_staff_profile_image/' . $member->staffid); */ ?>"><i
                                                                        class="fa fa-remove"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    --><?php /*} */ ?>
                                    <div class="row mbot20">
                                        <div class="col-sm-3">
                                            <div class="profile-pic">
                                                <?php
                                                $src = "";
                                                if ((isset($member) && $member->profile_image != NULL)) {
                                                    $profileImagePath = FCPATH . 'uploads/staff_profile_images/' . $member->staffid . '/round_' . $member->profile_image;
                                                    if (file_exists($profileImagePath)) {
                                                        $src = base_url() . 'uploads/staff_profile_images/' . $member->staffid . '/round_' . $member->profile_image;
                                                    }

                                                } ?>
                                                <div class="profile_imageview <?php echo empty($src) ? 'hidden' : ''; ?>">
                                                    <img src="<?php echo $src; ?>"/>
                                                    <?php if ($src == "") { ?>
                                                        <!-- <a class="clicktoaddimage" href="javascript:void(0)"
                                                           onclick="croppedDelete('profile');">
                                                            <span><i class="fa fa-trash"></i></span></a>
                                                        <a class="btn btn-info mtop10" href="javascript:void(0)"
                                                           onclick="reCropp('profile');">
                                                            <?php //echo _l('recrop')?></a> -->
                                                        <div class="actionToEdit">
                                                            <a class="clicktoaddimage" href="javascript:void(0)"
                                                               onclick="croppedDelete('profile');">
                                                                <span><i class="fa fa-trash"></i></span>
                                                            </a>
                                                            <a class="recropIcon_blk" href="javascript:void(0)"
                                                               onclick="reCropp('profile');">
                                                                <span><i class="fa fa-crop"
                                                                         aria-hidden="true"></i></span>
                                                            </a>
                                                        </div>
                                                    <?php } else { ?>
                                                        <a class="_delete clicktoaddimage"
                                                           href="<?php echo admin_url('staff/remove_staff_profile_image/' . $member->staffid); ?>">
                                                            <span><i class="fa fa-trash"></i></span>
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                                <div class="clicktoaddimage <?php echo !empty($src) ? 'hidden' : ''; ?>">
                                                    <div class="drag_drop_image">
                                                        <span class="icon"><i class="fa fa-image"></i></span>
                                                        <span><?php echo _l('dd_upload'); ?></span>
                                                    </div>
                                                    <input id="profile_image" type="file" class="" name="profile_image"
                                                           onchange="readFile(this,'profile');"/ >
                                                    <input type="hidden" id="imagebase64" name="imagebase64">
                                                </div>
                                                <div class="cropper" id="profile_croppie">
                                                    <div class="copper_container">
                                                        <div id="profile-cropper"></div>
                                                        <div class="cropper-footer">
                                                            <button type="button" class="btn btn-info p9 actionDone"
                                                                    type="button" id=""
                                                                    onclick="croppedResullt('profile');">
                                                                <?php echo _l('save'); ?>
                                                            </button>
                                                            <button type="button" class="btn btn-default actionCancel"
                                                                    data-dismiss="modal"
                                                                    onclick="croppedCancel('profile');">
                                                                <?php echo _l('cancel'); ?>
                                                            </button>
                                                            <button type="button" class="btn btn-default actionChange"
                                                                    onclick="croppedChange('profile');">
                                                                <?php echo _l('change'); ?>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-9">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <?php $value = (isset($member) ? $member->firstname : ''); ?>
                                                    <div class="form-group">
                                                        <label for="firstname"
                                                               class="control-label"><?php echo _l('staff_add_edit_firstname'); ?>
                                                            <small class="req text-danger">*</small>
                                                        </label>
                                                        <input id="firstname" name="firstname" class="form-control"
                                                               autofocus="1" value="<?php echo $value; ?>" type="text">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <?php $value = (isset($member) ? $member->lastname : ''); ?>
                                                    <div class="form-group">
                                                        <label for="lastname"
                                                               class="control-label"><?php echo _l('staff_add_edit_lastname'); ?>
                                                            <small class="req text-danger">*</small>
                                                        </label>
                                                        <input id="lastname" name="lastname" class="form-control"
                                                               autofocus="1"
                                                               value="<?php echo $value; ?>" type="text">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <?php $value = (isset($member) ? $member->email : ''); ?>
                                                    <?php $readonly = ($value!="" ? "readonly" : ''); ?>
                                                    <div class="form-group">
                                                        <label for="email"
                                                               class="control-label"><?php echo _l('staff_add_edit_email'); ?>
                                                            <small class="req text-danger">*</small>
                                                        </label>
                                                        <input id="email" name="email" class="form-control"
                                                               autocomplete="off"  value="<?php echo $value; ?>"
                                                               type="email" <?php echo $readonly; ?>>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <?php $value = (isset($member) ? $member->phonenumber : ''); ?>
                                                    <?php echo render_input('phonenumber', 'staff_add_edit_phonenumber', $value); ?>
                                                </div>

                                            </div>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="password"
                                                               class="control-label"><?php echo _l('staff_add_edit_password'); ?>
                                                            <small class="req text-danger">*</small>
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control password"
                                                                   name="password"
                                                                   autocomplete="off">
                                                            <span class="input-group-addon">
                                       <a href="#password" class="show_password"
                                          onclick="showPassword('password'); return false;"><i
                                                   class="fa fa-eye"></i></a>
                                       </span>
                                                            <span class="input-group-addon">
                                       <a href="#" class="generate_password"
                                          onclick="generatePassword(this);return false;"><i
                                                   class="fa fa-refresh"></i></a>
                                       </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="password" class="control-label">
                                                            <?php echo _l('staff_status'); ?>
                                                        </label><br/>
                                                        <div class="radio radio-primary radio-inline">
                                                            <input id="active" name="active" value="1" checked="true"
                                                                   type="radio" <?php echo (isset($member) && $member->active == 1) ? "checked" : ""; ?>>
                                                            <label for="<?php echo _l('active'); ?>"><?php echo _l('active'); ?></label>
                                                        </div>
                                                        <div class="radio radio-primary radio-inline">
                                                            <input id="inactive" name="active" value="0"
                                                                   type="radio" <?php echo (isset($member) && $member->active == 0) ? "checked" : ""; ?>>
                                                            <label for="<?php echo _l('inactive'); ?>"><?php echo _l('inactive'); ?></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- <div class="form-group">
                                    <label for="hourly_rate"><?php //echo _l('staff_hourly_rate'); ?></label>
                                    <div class="input-group">
                                       <input type="number" name="hourly_rate" value="<?php //if(isset($member)){echo $member->hourly_rate;} else {echo 0;} ?>" id="hourly_rate" class="form-control">
                                       <span class="input-group-addon">
                                       <?php //echo $base_currency->symbol; ?>
                                       </span>
                                    </div>
                                 </div> -->

                                    <!-- <div class="form-group">
                                    <label for="facebook" class="control-label"><i class="fa fa-facebook"></i> <?php //echo _l('staff_add_edit_facebook'); ?></label>
                                    <input type="text" class="form-control" name="facebook" value="<?php //if(isset($member)){echo $member->facebook;} ?>">
                                 </div>
                                 <div class="form-group">
                                    <label for="google" class="control-label"><i class="fa fa-google"></i> <?php //echo _l('staff_add_edit_linkedin'); ?></label>
                                    <input type="text" class="form-control" name="linkedin" value="<?php //if(isset($member)){echo $member->google;} ?>">
                                 </div>
                                 <div class="form-group">
                                    <label for="skype" class="control-label"><i class="fa fa-skype"></i> <?php //echo _l('staff_add_edit_skype'); ?></label>
                                    <input type="text" class="form-control" name="skype" value="<?php //if(isset($member)){echo $member->skype;} ?>">
                                 </div> -->
                                    <?php if (get_option('disable_language') == 0) { ?>
                                        <div class="form-group">
                                            <label for="default_language"
                                                   class="control-label"><?php echo _l('localization_default_language'); ?></label>
                                            <select name="default_language" data-live-search="true"
                                                    id="default_language" class="form-control selectpicker"
                                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value=""><?php echo _l('system_default_string'); ?></option>
                                                <?php
                                                foreach ($this->perfex_base->get_available_languages() as $language) {
                                                    $selected = '';
                                                    if (isset($member)) {
                                                        if ($member->default_language == $language) {
                                                            $selected = 'selected';
                                                        }
                                                    }
                                                    ?>
                                                    <option value="<?php echo $language; ?>" <?php echo $selected; ?>><?php echo ucfirst($language); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <?php } ?>
                                    <!-- <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php //echo _l('staff_email_signature_help'); ?>"></i>
                                 <?php //$value = (isset($member) ? $member->email_signature : ''); ?>
                                 <?php //echo render_textarea('email_signature','settings_email_signature',$value); ?> -->
                                    <!-- <div class="form-group">
                                    <label for="direction"><?php //echo _l('document_direction'); ?></label>
                                    <select class="selectpicker" data-none-selected-text="<?php //echo _l('system_default_string'); ?>" data-width="100%" name="direction" id="direction">
                                       <option value="" <?php //if(isset($member) && empty($member->direction)){echo 'selected';} ?>></option>
                                       <option value="ltr" <?php //if(isset($member) && $member->direction == 'ltr'){//echo 'selected';} ?>>LTR</option>
                                       <option value="rtl" <?php //if(isset($member) && $member->direction == 'rtl'){//echo 'selected';} ?>>RTL</option>
                                    </select>
                                 </div> -->
                                    <!-- <div class="form-group">
                                    <?php //if(count($departments) > 0){ ?>
                                    <label for="departments"><?php //echo _l('staff_add_edit_departments'); ?></label>
                                    <?php //} ?>
                                    <?php //foreach($departments as $department){ ?>
                                    <div class="checkbox checkbox-primary">
                                       <?php
                                    //$checked = '';
                                    //if(isset($member)) {
                                    //foreach ($staff_departments as $staff_department) {
                                    //if($staff_department['departmentid'] == $department['departmentid']) {
                                    //$checked = ' checked';
                                    //}
                                    //}
                                    //}
                                    ?>
                                       <input type="checkbox" id="dep_<?php //echo $department['departmentid']; ?>" name="departments[]" value="<?php //echo $department['departmentid']; ?>"<?php //echo $checked; ?>>
                                       <label for="dep_<?php //echo $department['departmentid']; ?>"><?php //echo $department['name']; ?></label>
                                    </div>
                                    <?php //} ?>
                                 </div> -->
                                    <?php $rel_id = (isset($member) ? $member->staffid : false); ?>
                                    <?php echo render_custom_fields('staff', $rel_id); ?>
                                    <!-- <?php //if (is_admin()){ ?>
                                 <div class="row">
                                    <div class="col-md-12">
                                       <hr />
                                       <div class="checkbox checkbox-primary">
                                          <?php
                                    //$isadmin = '';
                                    //if(isset($member)) {
                                    //if($member->staffid == get_staff_user_id() || is_admin($member->staffid)) {
                                    //$isadmin = ' checked';
                                    //}
                                    //}
                                    ?>
                                             <input type="checkbox" name="administrator" id="administrator" <?php //echo $isadmin; ?>>
                                             <label for="administrator"><?php //echo _l('staff_add_edit_administrator'); ?></label>
                                          </div>
                                          <?php //if(!isset($member)) { ?>
                                             <?php //if(total_rows('tblemailtemplates',array('slug'=>'new-staff-created','active'=>0)) == 0) { ?>
                                                <div class="checkbox checkbox-primary">
                                                   <input type="checkbox" name="send_welcome_email" id="send_welcome_email" checked>
                                                   <label for="send_welcome_email"><?php //echo _l('staff_send_welcome_email'); ?></label>
                                                </div>
                                             <?php //} ?>
                                       <?php //} ?>
                                    </div>
                                 </div>
                                 <?php //} ?> -->
                                    <!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
                                    <!-- <input  type="text" class="fake-autofill-field" name="fakeusernameremembered" value='' tabindex="-1"/>
                                    <input  type="password" class="fake-autofill-field" name="fakepasswordremembered" value='' tabindex="-1"/> -->
                                    <!-- <div class="clearfix form-group"></div> -->


                                    <?php
                                    /*foreach ($widget_data as $data) {*/
                                    $widget_data = (array)$widget_data;
                                    $all_data = isset($widget_data['widget_type']) ? $widget_data['widget_type'] : "";
                                    $quick_link_all_data = isset($widget_data['quick_link_type']) ? $widget_data['quick_link_type'] : "";
                                    /*}*/
                                    $rel_id = (isset($all_data) ? $all_data : "");
                                    $exp_val = explode(',', $rel_id);

                                    $link_data = (isset($quick_link_all_data) ? $quick_link_all_data : "");
                                    $quick_link_val = explode(',', $link_data);
                                    if(isset($dashboard_widget) && !empty($dashboard_widget)){
                                        if(!empty($dashboard_widget->widget_type)){
                                            $exp_val = explode(',',$dashboard_widget->widget_type);
                                        }
                                        if(!empty($dashboard_widget->quick_link_type)){
                                            $quick_link_val = explode(',',$dashboard_widget->quick_link_type);
                                        }
                                    }
                                    ?>

                                    <div class="form-group">
                                        <label for="password" class="control-label">Dashboard Widget
                                            Configuration</label>
                                        <div class="checkbox">
                                            <?php if (isset($member)) { ?>
                                                <input type="checkbox" id="getting_started"
                                                       name="widget_type[]" <?php if (in_array('getting_started', $exp_val)) {
                                                    echo 'checked';
                                                }; ?> value="getting_started">
                                                <label for="Getting Started">Getting Started</label>
                                            <?php } else { ?>
                                                <input type="checkbox" id="getting_started" name="widget_type[]"
                                                       value="getting_started" checked="checked">
                                                <label for="Getting Started">Getting Started</label>
                                            <?php } ?>
                                        </div>
                                        <div class="checkbox">
                                            <?php if (isset($member)) { ?>
                                                <input type="checkbox" id="lead_pipeline"
                                                       name="widget_type[]" <?php if (in_array('lead_pipeline', $exp_val)) {
                                                    echo 'checked';
                                                }; ?> value="lead_pipeline">
                                                <label for="Lead Pipeline">Lead Pipeline</label>
                                            <?php } else { ?>
                                                <input type="checkbox" id="lead_pipeline" name="widget_type[]"
                                                       checked="checked" value="lead_pipeline">
                                                <label for="Lead Pipeline">Lead Pipeline</label>
                                            <?php } ?>
                                        </div>
                                        <div class="checkbox">
                                            <?php if (isset($member)) { ?>
                                                <input type="checkbox" id="upcoming_event"
                                                       name="widget_type[]" <?php if (in_array('upcoming_project', $exp_val)) {
                                                    echo 'checked';
                                                }; ?> value="upcoming_project">
                                                <label for="Upcoming Project">Upcoming items</label>
                                            <?php } else { ?>
                                                <input type="checkbox" id="upcoming_event" name="widget_type[]"
                                                       checked="checked" value="upcoming_project">
                                                <label for="Upcoming Project">Upcoming items</label>
                                            <?php } ?>
                                        </div>
                                        <div class="checkbox">
                                            <?php if (isset($member)) { ?>
                                                <input type="checkbox" id="quick_link"
                                                       name="widget_type[]" <?php if (in_array('quick_link', $exp_val)) {
                                                    echo 'checked';
                                                }; ?> value="quick_link">
                                                <label for="Quick Links">Quick Links</label>
                                            <?php } else { ?>
                                                <input type="checkbox" id="quick_link" name="widget_type[]"
                                                       checked="checked" value="quick_link">
                                                <label for="Quick Links">Quick Links</label>
                                            <?php } ?>
                                            <div class="form-group">
                                                <div class="checkbox">
                                                    <?php if (isset($member)) { ?>
                                                        <input type="checkbox" id="leads" class="checkBoxClass"
                                                               name="quick_link_type[]" <?php if (in_array('lead', $quick_link_val)) {
                                                            echo 'checked';
                                                        } ?> value="lead">
                                                        <label for="Leads">Leads</label>
                                                    <?php } else { ?>
                                                        <input type="checkbox" id="leads" class="checkBoxClass"
                                                               name="quick_link_type[]" checked="checked" value="lead">
                                                        <label for="Leads">Leads</label>
                                                    <?php } ?>
                                                </div>
                                                <div class="checkbox">
                                                    <?php if (isset($member)) { ?>
                                                        <input type="checkbox" id="projects" class="checkBoxClass"
                                                               name="quick_link_type[]" <?php if (in_array('project', $quick_link_val)) {
                                                            echo 'checked';
                                                        } ?> value="project">
                                                        <label for="Projects">Projects</label>
                                                    <?php } else { ?>
                                                        <input type="checkbox" id="projects" class="checkBoxClass"
                                                               name="quick_link_type[]" checked="checked"
                                                               value="project">
                                                        <label for="Projects">Projects</label>
                                                    <?php } ?>
                                                </div>
                                                <div class="checkbox">
                                                    <?php if (isset($member)) { ?>
                                                        <input type="checkbox" id="messages" class="checkBoxClass"
                                                               name="quick_link_type[]" <?php if (in_array('message', $quick_link_val)) {
                                                            echo 'checked';
                                                        } ?> value="message">
                                                        <label for="Messages">Messages</label>
                                                    <?php } else { ?>
                                                        <input type="checkbox" id="messages" class="checkBoxClass"
                                                               name="quick_link_type[]" checked="checked"
                                                               value="message">
                                                        <label for="Messages">Messages</label>
                                                    <?php } ?>
                                                </div>
                                                <div class="checkbox">
                                                    <?php if (isset($member)) { ?>
                                                        <input type="checkbox" id="tasksdue" class="checkBoxClass"
                                                               name="quick_link_type[]" <?php if (in_array('task_due', $quick_link_val)) {
                                                            echo 'checked';
                                                        } ?> value="task_due">
                                                        <label for="Task Due">Task Due</label>
                                                    <?php } else { ?>
                                                        <input type="checkbox" id="tasksdue" class="checkBoxClass"
                                                               name="quick_link_type[]" checked="checked"
                                                               value="task_due">
                                                        <label for="Task Due">Task Due</label>
                                                    <?php } ?>
                                                </div>
                                                <div class="checkbox">
                                                    <?php if (isset($member)) { ?>
                                                        <input type="checkbox" id="meetings" class="checkBoxClass"
                                                               name="quick_link_type[]" <?php if (in_array('meeting', $quick_link_val)) {
                                                            echo 'checked';
                                                        } ?> value="meeting">
                                                        <label for="Meetings">Meetings</label>
                                                    <?php } else { ?>
                                                        <input type="checkbox" id="meetings" class="checkBoxClass"
                                                               name="quick_link_type[]" checked="checked"
                                                               value="meeting">
                                                        <label for="Meetings">Meetings</label>
                                                    <?php } ?>
                                                </div>
                                                <div class="checkbox">
                                                    <?php if (isset($member)) { ?>
                                                        <input type="checkbox" id="amountreceivable"
                                                               class="checkBoxClass"
                                                               name="quick_link_type[]" <?php if (in_array('amount_receivable', $quick_link_val)) {
                                                            echo 'checked';
                                                        } ?> value="amount_receivable">
                                                        <label for="Amount Receivable">Amount Receivable</label>
                                                    <?php } else { ?>
                                                        <input type="checkbox" id="amountreceivable"
                                                               class="checkBoxClass" name="quick_link_type[]"
                                                               checked="checked" value="amount_receivable">
                                                        <label for="Amount Receivable">Amount Receivable</label>
                                                    <?php } ?>
                                                </div>
                                                <div class="checkbox">
                                                    <?php if (isset($member)) { ?>
                                                        <input type="checkbox" id="amountreceived" class="checkBoxClass"
                                                               name="quick_link_type[]" <?php if (in_array('amount_received', $quick_link_val)) {
                                                            echo 'checked';
                                                        } ?> value="amount_received">
                                                        <label for="Amount Received">Amount Received</label>
                                                    <?php } else { ?>
                                                        <input type="checkbox" id="amountreceived" class="checkBoxClass"
                                                               name="quick_link_type[]" checked="checked"
                                                               value="amount_received">
                                                        <label for="Amount Received">Amount Received</label>
                                                    <?php } ?>
                                                </div>
                                                <div class="checkbox">
                                                    <?php if (isset($member)) { ?>
                                                        <input type="checkbox" id="invites" class="checkBoxClass"
                                                               name="quick_link_type[]" <?php if (in_array('invite', $quick_link_val)) {
                                                            echo 'checked';
                                                        } ?> value="invite">
                                                        <label for="Invites">Invites</label>
                                                    <?php } else { ?>
                                                        <input type="checkbox" id="invites" class="checkBoxClass"
                                                               name="quick_link_type[]" checked="checked"
                                                               value="invite">
                                                        <label for="Invites">Invites</label>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="checkbox">
                                            <?php if (isset($member)) { ?>
                                                <input type="checkbox" id="pinned_item"
                                                       name="widget_type[]" <?php if (in_array('pinned_item', $exp_val)) {
                                                    echo 'checked';
                                                }; ?> value="pinned_item">
                                                <label for="Pinned Items">Pinned Items</label>
                                            <?php } else { ?>
                                                <input type="checkbox" id="pinned_item" name="widget_type[]"
                                                       checked="checked" value="pinned_item">
                                                <label for="Pinned Items">Pinned Items</label>
                                            <?php } ?>
                                        </div>
                                        <div class="checkbox">
                                            <?php if (isset($member)) { ?>
                                                <input type="checkbox" id="task_list"
                                                       name="widget_type[]" <?php if (in_array('task_list', $exp_val)) {
                                                    echo 'checked';
                                                }; ?> value="task_list">
                                                <label for="Recent activities">Recent activities</label>
                                            <?php } else { ?>
                                                <input type="checkbox" id="task_list" name="widget_type[]"
                                                       checked="checked" value="task_list">
                                                <label for="Recent activities">Recent activities</label>
                                            <?php } ?>
                                        </div>
                                        <div class="checkbox">
                                            <?php if (isset($member)) { ?>
                                                <input type="checkbox" id="favourite_contact"
                                                       name="widget_type[]" <?php if (in_array('contacts', $exp_val)) {
                                                    echo 'checked';
                                                } ?> value="contacts">
                                                <label for="Favorite">Pinned contacts</label>
                                            <?php } else { ?>
                                                <input type="checkbox" id="favourite_contact" name="widget_type[]"
                                                       checked="checked" value="favourite">
                                                <label for="Favorite">Pinned contacts</label>
                                            <?php } ?>
                                        </div>
                                        <div class="checkbox">
                                            <?php if (isset($member)) { ?>
                                                <input type="checkbox" id="calender"
                                                       name="widget_type[]" <?php if (in_array('calendar', $exp_val)) {
                                                    echo 'checked';
                                                }; ?> value="calendar">
                                                <label for="Calendar">Calendar</label>
                                            <?php } else { ?>
                                                <input type="checkbox" id="calender" name="widget_type[]"
                                                       checked="checked" value="calendar">
                                                <label for="Calendar">Calendar</label>
                                            <?php } ?>
                                        </div>
                                        <div class="checkbox">
                                            <?php if (isset($member)) { ?>
                                                <input type="checkbox" id="calender"
                                                       name="widget_type[]" <?php if (in_array('messages', $exp_val)) {
                                                    echo 'checked';
                                                }; ?> value="messages">
                                                <label for="New messages">New messages</label>
                                            <?php } else { ?>
                                                <input type="checkbox" id="message" name="widget_type[]"
                                                       checked="checked value=" messages">
                                                <label for="New messages">New messages</label>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php if (isset($member)) { ?>
                                        <p class="text-muted"><?php echo _l('staff_add_edit_password_note'); ?></p>
                                        <?php if ($member->last_password_change != NULL) { ?>
                                            <?php echo _l('staff_add_edit_password_last_changed'); ?>: <?php echo time_ago($member->last_password_change); ?>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="tab_staff_permissions">
                                    <div id="field">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label class="control-label"
                                                       for="user_type"><?php echo _l('staff_add_edit_user_type'); ?>
                                                    <small class="req text-danger">*</small>
                                                </label>
                                                <select id="user_type" name="user_type" class="selectpicker"
                                                        data-width="100%" data-none-selected-text="Select"
                                                        data-live-search="true">
                                                    <option value=""></option>
                                                    <?php
                                                    foreach ($usertypes as $usertype) {
                                                        if(strtolower($usertype['type'])=="admin"){
                                                            $usertype['type']="Brand admin";
                                                        }
                                                        $selected = '';
                                                        if (isset($member)) {
                                                            if ($member->user_type == $usertype['id']) {
                                                                $selected = 'selected';
                                                            }
                                                        }
                                                        ?>
                                                        <option value="<?php echo $usertype['id']; ?>" <?php echo $selected; ?>><?php echo ucfirst($usertype['type']); ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <?php //echo render_select('user_type',$usertypes,array('id','type'),'staff_add_edit_user_type',$uselected); ?>
                                        </div>
                                        <div class="usertype-permission">
                                            <?php if (isset($member)) {
                                                $pi = 0; ?>
                                                <?php if (!empty($member->permission)) { ?>
                                                    <?php foreach ($member->permission as $pk => $pv) { ?>
                                                        <div id="field-<?php echo $pi; ?>">
                                                            <div class="col-md-6">
                                                                <label class="control-label"
                                                                       for="permission[<?php echo $pi; ?>][team]">Team</label>
                                                                <select id="permission[<?php echo $pi; ?>][team]"
                                                                        name="permission[<?php echo $pi; ?>][team]"
                                                                        class="selectpicker" data-width="100%"
                                                                        data-none-selected-text="Select"
                                                                        data-live-search="true">
                                                                    <option value=""></option>

                                                                    <?php
                                                                    foreach ($teams as $team) {
                                                                        $selected = '';
                                                                        if ($pv->team_id == $team['teamid']) {
                                                                            $selected = "selected='selected'";
                                                                        }
                                                                        echo '<option value="' . $team['teamid'] . '" ' . $selected . '>' . $team['name'] . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="control-label"
                                                                       for="permission[<?php echo $pi; ?>][role]">Role</label>
                                                                <select id="permission[<?php echo $pi; ?>][role]"
                                                                        name="permission[<?php echo $pi; ?>][role]"
                                                                        class="selectpicker" data-width="100%"
                                                                        data-none-selected-text="Select"
                                                                        data-live-search="true">
                                                                    <option value=""></option>
                                                                    <?php
                                                                    foreach ($roles as $role) {
                                                                        $rselected = '';
                                                                        if ($pv->role_id == $role['roleid']) {
                                                                            $rselected = "selected='selected'";
                                                                        }
                                                                        echo '<option value="' . $role['roleid'] . '" ' . $rselected . '>' . $role['name'] . '</option>';
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <!--<div class="col-md-12 text-right" style="margin-top:10px">
                                                            <button id="remove<?php /*echo $pi; */?>"
                                                                    class="btn btn-danger remove-me">Remove
                                                            </button>
                                                        </div>-->
                                                        <?php $pi++;
                                                    } ?>
                                                <?php } else { ?>
                                                    <div id="field-0">
                                                        <div class="col-md-6">
                                                            <label class="control-label"
                                                                   for="permission[0][team]">Team</label>
                                                            <select id="permission[0][team]" name="permission[0][team]"
                                                                    class="selectpicker" data-width="100%"
                                                                    data-none-selected-text="Select"
                                                                    data-live-search="true">
                                                                <option value=""></option>

                                                                <?php
                                                                foreach ($teams as $team) {
                                                                    echo '<option value="' . $team['teamid'] . '">' . $team['name'] . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="control-label"
                                                                   for="permission[0][role]">Role</label>
                                                            <select id="permission[0][role]" name="permission[0][role]"
                                                                    class="selectpicker" data-width="100%"
                                                                    data-none-selected-text="Select"
                                                                    data-live-search="true">
                                                                <option value=""></option>
                                                                <?php
                                                                foreach ($roles as $role) {
                                                                    echo '<option value="' . $role['roleid'] . '">' . $role['name'] . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <div id="field-0">
                                                    <div class="col-md-6">
                                                        <label class="control-label"
                                                               for="permission[0][team]">Team</label>
                                                        <select id="permission[0][team]" name="permission[0][team]"
                                                                class="selectpicker" data-width="100%"
                                                                data-none-selected-text="Select"
                                                                data-live-search="true">
                                                            <option value=""></option>

                                                            <?php
                                                            foreach ($teams as $team) {
                                                                echo '<option value="' . $team['teamid'] . '">' . $team['name'] . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="control-label"
                                                               for="permission[0][role]">Role</label>
                                                        <select id="permission[0][role]" name="permission[0][role]"
                                                                class="selectpicker" data-width="100%"
                                                                data-none-selected-text="Select"
                                                                data-live-search="true">
                                                            <option value=""></option>
                                                            <?php
                                                            foreach ($roles as $role) {
                                                                echo '<option value="' . $role['roleid'] . '">' . $role['name'] . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <!--<div class="col-md-12 text-right" style="margin-top:10px">
                                                <button id="add-more" name="add-more" class="btn btn-primary">Add More
                                                </button>
                                            </div>-->
                                        </div>
                                    </div>

                                    <!--  <hr /> -->
                                    <!-- <h4 class="font-medium mbot15 bold"><?php //echo _l('staff_add_edit_permissions'); ?></h4>
                                 <div class="table-responsive">
                                    <table class="table table-bordered roles no-margin">
                                       <thead>
                                          <tr>
                                             <th class="bold"><?php //echo _l('permission'); ?></th>
                                             <th class="text-center bold"><?php //echo _l('permission_view'); ?> (<?php //echo _l('permission_global'); ?>)</th>
                                             <th class="text-center bold"><?php //echo _l('permission_view_own'); ?></th>
                                             <th class="text-center bold"><?php //echo _l('permission_create'); ?></th>
                                             <th class="text-center bold"><?php //echo _l('permission_edit'); ?></th>
                                             <th class="text-center text-danger bold"><?php //echo _l('permission_delete'); ?></th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          <?php
                                    //if(isset($member)){
                                    //$is_admin = is_admin($member->staffid);
                                    //}
                                    //$conditions = get_permission_conditions();
                                    //foreach($permissions as $permission){
                                    //$permission_condition = $conditions[$permission['shortname']];
                                    ?>
                                          <tr data-id="<?php //echo $permission['permissionid']; ?>">
                                             <td>
                                                <?php //echo $permission['name']; ?>
                                             </td>
                                             <td class="text-center">
                                                <?php //if($permission_condition['view'] == true) {
                                    //$statement = '';
                                    //if(isset($is_admin) && $is_admin || isset($member) && has_permission($permission['shortname'],$member->staffid,'view_own')) {
                                    //$statement = 'disabled';
                                    //} else if(isset($member) && has_permission($permission['shortname'],$member->staffid,'view')) {
                                    //$statement = 'checked';
                                    //}
                                    ?>
                                                <?php
                                    //if(isset($permission_condition['help'])){
                                    //echo '<i class="fa fa-question-circle text-danger" data-toggle="tooltip" data-title="'.$permission_condition['help'].'"></i>';
                                    //}
                                    ?>
                                                <div class="checkbox">
                                                   <input type="checkbox" data-can-view <?php //echo $statement; ?> name="view[]" value="<?php //echo $permission['permissionid']; ?>">
                                                   <label></label>
                                                </div>
                                                <?php //} ?>
                                             </td>
                                             <td class="text-center">
                                                <?php //if($permission_condition['view_own'] == true){
                                    //$statement = '';
                                    //if(isset($is_admin) && $is_admin || isset($member) && has_permission($permission['shortname'],$member->staffid,'view')){
                                    //$statement = 'disabled';
                                    //} else if(isset($member) && has_permission($permission['shortname'],$member->staffid,'view_own')){
                                    //$statement = 'checked';
                                    //}
                                    ?>
                                                <div class="checkbox">
                                                   <input type="checkbox" <?php //echo $statement; ?> data-shortname="<?php //echo $permission['shortname']; ?>" data-can-view-own name="view_own[]" value="<?php //echo $permission['permissionid']; ?>">
                                                   <label></label>
                                                </div>
                                                <?php //} else if($permission['shortname'] == 'customers'){
                                    //echo '<i class="fa fa-question-circle mtop15" data-toggle="tooltip" data-title="'._l('permission_customers_based_on_admins').'"></i>';
                                    //} else if($permission['shortname'] == 'projects'){
                                    //echo '<i class="fa fa-question-circle mtop25" data-toggle="tooltip" data-title="'._l('permission_projects_based_on_assignee').'"></i>';
                                    //} else if($permission['shortname'] == 'tasks'){
                                    //echo '<i class="fa fa-question-circle mtop25" data-toggle="tooltip" data-title="'._l('permission_tasks_based_on_assignee').'"></i>';
                                    //} else if($permission['shortname'] == 'payments'){
                                    //echo '<i class="fa fa-question-circle mtop15" data-toggle="tooltip" data-title="'._l('permission_payments_based_on_invoices').'"></i>';
                                    //} ?>
                                             </td>
                                             <td  class="text-center">
                                                <?php //if($permission_condition['create'] == true){
                                    //$statement = '';
                                    //if(isset($is_admin) && $is_admin){
                                    //$statement = 'disabled';
                                    //} else if(isset($member) && has_permission($permission['shortname'],$member->staffid,'create')){
                                    //$statement = 'checked';
                                    //}
                                    ?>
                                                <div class="checkbox">
                                                   <input type="checkbox" data-shortname="<?php //echo $permission['shortname']; ?>" data-can-create <?php //echo $statement; ?> name="create[]" value="<?php //echo $permission['permissionid']; ?>">
                                                   <label></label>
                                                </div>
                                                <?php //} ?>
                                             </td>
                                             <td  class="text-center">
                                                <?php //if($permission_condition['edit'] == true){
                                    //$statement = '';
                                    //if(isset($is_admin) && $is_admin){
                                    //$statement = 'disabled';
                                    //} else if(isset($member) && has_permission($permission['shortname'],$member->staffid,'edit')){
                                    //$statement = 'checked';
                                    //}
                                    ?>
                                                <div class="checkbox">
                                                   <input type="checkbox" data-shortname="<?php //echo $permission['shortname']; ?>" data-can-edit <?php //echo $statement; ?> name="edit[]" value="<?php //echo $permission['permissionid']; ?>">
                                                   <label></label>
                                                </div>
                                                <?php //} ?>
                                             </td>
                                             <td  class="text-center">
                                                <?php //if($permission_condition['delete'] == true){
                                    //$statement = '';
                                    //if(isset($is_admin) && $is_admin){
                                    //$statement = 'disabled';
                                    //} else if(isset($member) && has_permission($permission['shortname'],$member->staffid,'delete')){
                                    //$statement = 'checked';
                                    //}
                                    ?>
                                                <div class="checkbox checkbox-danger">
                                                   <input type="checkbox" data-shortname="<?php //echo $permission['shortname']; ?>" data-can-delete <?php //echo $statement; ?> name="delete[]" value="<?php //echo $permission['permissionid']; ?>">
                                                   <label></label>
                                                </div>
                                                <?php //} ?>
                                             </td>
                                          </tr>
                                          <?php //} ?>
                                       </tbody>
                                    </table>
                                 </div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn-bottom-toolbar text-right btn-toolbar-container-out">
                    <input type="hidden" name="is_sido_admin" value="<?php echo $is_sido_admin; ?>">
                    <button class="btn btn-default" type="button"
                            onclick="location.href='<?php echo base_url(); ?>admin/staff'"><?php echo _l('Cancel'); ?></button>
                    <button type="submit" id="btnsave" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
                <?php echo form_close(); ?>
                <?php if (isset($member)) { ?>
                    <div class="col-md-12 small-table-right-col">
                        <div class="panel_s btmbrd">
                            <div class="panel-body">
                                <h4 class="no-margin">
                                    <?php echo _l('staff_add_edit_notes'); ?>
                                    <span class="pull-right">
                                 <a href="#" class="btn btn-info"
                                    onclick="slideToggle('.usernote'); return false;"><?php echo _l('new_note'); ?></a>
                              </span>
                                </h4>
                                <!-- <hr class="hr-panel-heading" /> -->
                                <div class="clearfix"></div>
                                <!--<hr class="hr-panel-heading" id="staff-note" />-->
                                <div class="mbot15 usernote hide inline-block full-width text-right">
                                    <?php echo form_open(admin_url('misc/add_note/' . $member->staffid . '/staff')); ?>
                                    <textarea id="description" name="description" class="form-control"
                                              rows="5"></textarea><br/>
                                    <?php //echo render_textarea('description','staff_add_edit_note_description','',array('rows'=>5)); ?>
                                    <button class="btn btn-default" type="button"
                                            onclick="slideToggle('.usernote'); return false;"><?php echo _l('Cancel'); ?></button>&nbsp;
                                    <button class="btn btn-info pull-right mbot15"><?php echo _l('submit'); ?></button>
                                    <?php echo form_close(); ?>
                                </div>
                                <div class="clearfix"></div>
                                <div class="mtop15">
                                    <table class="table dt-table scroll-responsive" data-order-col="1"
                                           data-order-type="desc">
                                        <thead>
                                        <tr>
                                            <th><?php echo _l('staff_notes_table_description_heading'); ?></th>
                                            <!--<th><?php //echo _l('staff_notes_table_addedfrom_heading'); ?></th>-->
                                            <th><?php echo _l('staff_notes_table_dateadded_heading'); ?></th>
                                            <th><?php echo _l(''); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($user_notes as $note) { ?>
                                            <tr>
                                                <td>
                                                    <div data-note-description="<?php echo $note['id']; ?>">
                                                        <?php echo((strlen($note['description']) > 40) ? substr($note['description'], 0, 40) . '...' : $note['description']); ?>
                                                    </div>
                                                    <div data-note-edit-textarea="<?php echo $note['id']; ?>"
                                                         class="hide inline-block full-width">
                                                        <textarea name="description" class="form-control"
                                                                  rows="4"><?php echo clear_textarea_breaks($note['description']); ?></textarea>
                                                        <div class="text-right mtop15">
                                                            <button type="button" class="btn btn-default"
                                                                    onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                                                            <button type="button" class="btn btn-info"
                                                                    onclick="edit_note(<?php echo $note['id']; ?>);"><?php echo _l('update_note'); ?></button>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td data-order="<?php echo $note['dateadded']; ?>"><?php echo _dt($note['dateadded']); ?></td>
                                                <!--<td><?php //echo $note['firstname'] . ' ' . $note['lastname']; ?></td>-->

                                                <td>
                                                    <?php if ($note['addedfrom'] == get_staff_user_id() || has_permission('account_setup', '', 'delete')) { ?>
                                                        <div class="show-options">
                                                            <a class="show_act" href="javascript:void(0)">
                                                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                                            </a>
                                                        </div>
                                                        <div class="table_actions">
                                                            <ul>
                                                                <li><a href="#" class=""
                                                                       onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><i
                                                                                class="fa fa-pencil-square-o"></i>Edit</a>
                                                                </li>
                                                                <li>
                                                                    <a href="<?php echo admin_url('misc/delete_note/' . $note['id']); ?>"
                                                                       class=""><i class="fa fa-remove"></i>Delete</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php /*
                     <div class="panel_s">
                        <div class="panel-body">
                           <h4 class="no-margin">
                              <?php echo _l('task_timesheets'); ?> & <?php echo _l('als_reports'); ?>
                           </h4>
                           <hr class="hr-panel-heading" />
                           <?php echo form_open($this->uri->uri_string(),array('method'=>'GET')); ?>
                           <?php echo form_hidden('filter','true'); ?>
                           <div class="row">
                              <div class="col-md-6">
                                 <select name="range" id="range" class="selectpicker" data-width="100%">
                                    <option value="this_month" <?php if(!$this->input->get('range') || $this->input->get('range') == 'this_month'){echo 'selected';} ?>><?php echo _l('staff_stats_this_month_total_logged_time'); ?></option>
                                    <option value="last_month" <?php if($this->input->get('range') == 'last_month'){echo 'selected';} ?>><?php echo _l('staff_stats_last_month_total_logged_time'); ?></option>
                                    <option value="this_week" <?php if($this->input->get('range') == 'this_week'){echo 'selected';} ?>><?php echo _l('staff_stats_this_week_total_logged_time'); ?></option>
                                    <option value="last_week" <?php if($this->input->get('range') == 'last_week'){echo 'selected';} ?>><?php echo _l('staff_stats_last_week_total_logged_time'); ?></option>
                                    <option value="period" <?php if($this->input->get('range') == 'period'){echo 'selected';} ?>><?php echo _l('period_datepicker'); ?></option>
                                 </select>
                                 <div class="row mtop15">
                                    <div class="col-md-12 period <?php if($this->input->get('range') != 'period'){echo 'hide';} ?>">
                                       <?php echo render_date_input('period-from','',$this->input->get('period-from')); ?>
                                    </div>
                                    <div class="col-md-12 period <?php if($this->input->get('range') != 'period'){echo 'hide';} ?>">
                                       <?php echo render_date_input('period-to','',$this->input->get('period-to')); ?>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-md-2 text-right">
                                 <button type="submit" class="btn btn-success apply-timesheets-filters"><?php echo _l('apply'); ?></button>
                              </div>
                           </div>
                           <?php echo form_close(); ?>
                           <hr class="hr-panel-heading" />
                              <table class="table dt-table scroll-responsive">
                                 <thead>
                                    <th><?php echo _l('task'); ?></th>
                                    <th><?php echo _l('timesheet_start_time'); ?></th>
                                    <th><?php echo _l('timesheet_end_time'); ?></th>
                                    <th><?php echo _l('task_relation'); ?></th>
                                    <th><?php echo _l('staff_hourly_rate'); ?> (<?php echo _l('als_staff'); ?>)</th>
                                    <th><?php echo _l('time_h'); ?></th>
                                    <th><?php echo _l('time_decimal'); ?></th>
                                 </thead>
                                 <tbody>
                                    <?php
                                       $total_logged_time = 0;
                                       foreach($timesheets as $t){ ?>
                                    <tr>
                                       <td><a href="#" onclick="init_task_modal(<?php echo $t['task_id']; ?>); return false;"><?php echo $t['name']; ?></a></td>
                                       <td data-order="<?php echo $t['start_time']; ?>"><?php echo _dt($t['start_time'],true); ?></td>
                                       <td data-order="<?php echo $t['end_time']; ?>"><?php echo _dt($t['end_time'],true); ?></td>
                                       <td>
                                          <?php
                                             $rel_data   = get_relation_data($t['rel_type'], $t['rel_id']);
                                             $rel_values = get_relation_values($rel_data, $t['rel_type']);
                                             echo '<a href="' . $rel_values['link'] . '">' . $rel_values['name'].'</a>';
                                             ?>
                                       </td>
                                       <td><?php echo format_money($t['hourly_rate'],$base_currency->symbol); ?></td>
                                       <td>
                                          <?php echo '<b>'.seconds_to_time_format($t['end_time'] - $t['start_time']).'</b>'; ?>
                                       </td>
                                       <td data-order="<?php echo sec2qty($t['total']); ?>">
                                          <?php
                                             $total_logged_time += $t['total'];
                                             echo '<b>'.sec2qty($t['total']).'</b>';
                                             ?>
                                       </td>
                                    </tr>
                                    <?php } ?>
                                 </tbody>
                                 <tfoot>
                                    <tr>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td></td>
                                       <td align="right"><?php echo '<b>' . _l('total_by_hourly_rate') .':</b> '. format_money((sec2qty($total_logged_time) * $member->hourly_rate),$base_currency->symbol); ?></td>
                                       <td align="right">
                                          <?php echo '<b>'._l('total_logged_hours_by_staff') . ':</b> ' . seconds_to_time_format($total_logged_time); ?>
                                       </td>
                                       <td align="right">
                                          <?php echo '<b>'._l('total_logged_hours_by_staff') . ':</b> ' . sec2qty($total_logged_time); ?>
                                       </td>
                                    </tr>
                                 </tfoot>
                              </table>
                        </div>
                     </div>
                     <div class="panel_s">
                        <div class="panel-body">
                           <h4 class="no-margin">
                              <?php echo _l('projects'); ?>
                           </h4>
                           <hr class="hr-panel-heading" />
                           <div class="_filters _hidden_inputs hidden staff_projects_filter">
                              <?php echo form_hidden('staff_id',$member->staffid); ?>
                           </div>
                           <?php render_datatable(array(
                              _l('project_name'),
                              _l('project_start_date'),
                              _l('project_deadline'),
                              _l('project_status'),
                              ),'staff-projects'); ?>
                        </div>
                     </div> */ ?>
                    </div>
                <?php } ?>
                <!--  <?php //if(isset($member)){ ?>
                  <div class="col-md-12">
                     <div class="panel_s">
                        <div class="panel-body">
                           <h4 class="no-margin"><?php //echo $member->firstname . ' ' . $member->lastname; ?>
                              <?php //if($member->last_activity && $member->staffid != get_staff_user_id()){ ?>
                              <small> - <?php //echo _l('last_active'); ?>: <?php //echo time_ago($member->last_activity); ?></small>
                              <?php //} ?>
                              <!--<a href="#" onclick="small_table_full_view(); return false;" data-placement="left" data-toggle="tooltip" data-title="<?php //echo _l('toggle_full_view'); ?>" class="toggle_view pull-right">
                              <i class="fa fa-expand"></i></a>-->
                <!-- </h4>
             </div>
          </div>
       </div> -->
                <?php //} ?>
            <?php } ?>
        </div>
        <div class="btn-bottom-pusher"></div>
    </div>
</div>

<?php $next_p = 0; ?>
<?php init_tail(); ?>

<script>
    $(function () {
        $("#quick_link").click(function () {
            $(".checkBoxClass").prop('checked', $(this).prop('checked'));
        });

        if ($('.checkBoxClass:checked').length == $('.checkBoxClass').length) {
            $("#quick_link").prop('checked', true);
        }
        $(".usertype-permission").toggle();

        $("#phonenumber").mask("(999) 999-9999", {placeholder: "(___) ___-____"});

        //$('select[name="role"]').on('change', function() {
        //var roleid = $(this).val();
        //init_roles_permissions(roleid, true);
        //});

        $('#user_type').change(function () {
            var selected = $(this).find("option:selected").val();
            if (selected == 2) {
                $(".usertype-permission").toggle();
            } else {
                $(".usertype-permission").hide();
            }
        });

        if ($('#user_type').val() == 2) {
            $(".usertype-permission").toggle();
        }

        $('input[name="administrator"]').on('change', function () {
            var checked = $(this).prop('checked');
            var isNotStaffMember = $('.is-not-staff');

            if (checked == true) {
                isNotStaffMember.addClass('hide');
                $('.roles').find('input').prop('disabled', true).prop('checked', false);
            } else {
                isNotStaffMember.removeClass('hide');
                isNotStaffMember.find('input').prop('checked', false);
                $('.roles').find('input').prop('disabled', false);
            }
        });

        //init_roles_permissions();
        //$(".staff-form").validate({ ignore: "" });
        var validator = $('.staff-form').submit(function () {
        }).validate({
            ignore: "",
            rules: {
                firstname: 'required',
                lastname: 'required',
                username: 'required',
                password: {
                    required: {
                        depends: function (element) {
                            return ($('input[name="isedit"]').length == 0) ? true : false
                        }
                    }
                },
                email: {
                    required: true,
                    email: true,
                    remote: {
                        url: site_url + "admin/misc/staff_email_exists",
                        type: 'post',
                        data: {
                            email: function () {
                                return $('input[name="email"]').val();
                            },
                            memberid: function () {
                                return $('input[name="memberid"]').val();
                            }
                        }
                    }
                },
                user_type: {
                    required: {
                        depends: function (element) {
                            return ($('input[name="is_sido_admin"]').val() == 1) ? false : true
                        }
                    }
                }
            }
        });
        // _validate_form($('.staff-form'),{
        //    firstname:'required',
        //    lastname:'required',
        //    username:'required',
        //    password: {
        //       required: {
        //          depends: function(element){
        //             return ($('input[name="isedit"]').length == 0) ? true : false
        //          }
        //       }
        //    },
        //    email: {
        //       required:true,
        //       email:true,
        //       remote:{
        //          url: site_url + "admin/misc/staff_email_exists",
        //          type:'post',
        //          data: {
        //             email:function(){
        //                return $('input[name="email"]').val();
        //             },
        //             memberid:function(){
        //                return $('input[name="memberid"]').val();
        //             }
        //          }
        //       }
        //    },
        //    user_type: {
        //       required: {
        //          depends: function(element){
        //             return ($('input[name="is_sido_admin"]').val() == 1) ? false : true
        //          }
        //       }
        //    }
        // });

        var my_fields = $("div[id^='field-']");
        var highest = -Infinity;
        $.each(my_fields, function (mindex, mvalue) {
            var fieldNum = mvalue.id.split("-");
            highest = Math.max(highest, parseFloat(fieldNum[1]));
        });

        var next = highest;
        var roles = <?php echo json_encode($roles); ?>;
        var teams = <?php echo json_encode($teams); ?>;

        $("#add-more").click(function (e) {
            e.preventDefault();
            var addto = "#field-" + next;
            var addRemove = "#field-" + (next);

            next = next + 1;
            var newIn = "";
            newIn += ' <div id="field-' + next + '" name="field' + next + '"><div class="col-md-6"><label class="control-label" for="permission[' + next + '][team]">Team</label><select id="permission[' + next + '][team]" name="permission[' + next + '][team]" class="selectpicker" data-width="100%" data-none-selected-text="Select" data-live-search="true"><option value=""></option>';
            $.each(teams, function (tindex, tvalue) {
                newIn += '<option value="' + tvalue.teamid + '">' + tvalue.name + '</option>';
            });

            newIn += '</select></div>';
            newIn += '<div class="col-md-6"><label class="control-label" for="permission[' + next + '][role]">Role</label><select id="permission[' + next + '][role]" name="permission[' + next + '][role]" class="selectpicker" data-width="100%" data-none-selected-text="Select" data-live-search="true"><!--<option value=""></option>-->';
            $.each(roles, function (rindex, rvalue) {
                newIn += '<option value="' + rvalue.roleid + '">' + rvalue.name + '</option>';
            });

            newIn += '</select></div></div>';
            var newInput = $(newIn);
            var removeBtn = '<div class="col-md-12 text-right" style="margin-top:10px"><button id="remove' + (next - 1) + '" class="btn btn-danger remove-me" >Remove</button></div>';
            var removeButton = $(removeBtn);
            $(addto).after(newInput);
            $(addRemove).after(removeButton);
            $("#field-" + next).attr('data-source', $(addto).attr('data-source'));
            $("#count").val(next);

            $('.remove-me').click(function (e) {
                e.preventDefault();
                var fieldNum = this.id.charAt(this.id.length - 1);
                var fieldID = "#field-" + fieldNum;
                $(this).remove();
                $(fieldID).remove();
            });

            $('.selectpicker').selectpicker('render');
        });

        $('.remove-me').click(function (e) {
            e.preventDefault();
            var fieldNum = this.id.charAt(this.id.length - 1);
            var fieldID = "#field-" + fieldNum;
            $(this).remove();
            $(fieldID).remove();
        });
    });
</script>
</body>
</html>