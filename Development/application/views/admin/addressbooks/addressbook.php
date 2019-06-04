<?php init_head(); ?>
<div id="wrapper">
    <div class="content addressbook-page">
        <div class="row">
            <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'addressbook-form', 'autocomplete' => 'off')); ?>
            <div class="col-sm-12">
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php if (isset($venueid) || isset($vid)) {
                        if (isset($vid)) {
                            $vnuid = $vid;
                        } else {
                            $vnuid = $venueid;
                        }
                        ?>

                        <?php if (isset($lid)) { ?>
                            <a href="<?php echo admin_url('leads/'); ?>">Leads</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('leads/dashboard/' . $lid); ?>"><?php echo($lname); ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('venues') . '?lid=' . $lid; ?>">Venues</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('venues/view/') . $venueid; ?>"><?php echo get_vanue_data($venueid)->venuename; ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } elseif (isset($pid)) { ?>
                            <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('leads/dashboard/' . $pid); ?>"><?php echo($lname); ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('venues') . '?pid=' . $pid; ?>">Venues</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('venues/view/') . $venueid; ?>"><?php echo get_vanue_data($venueid)->venuename; ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } else { ?>
                            <a href="<?php echo admin_url('venues'); ?>">Venues</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('venues/view/') . $vnuid; ?>"><?php echo get_vanue_data($vnuid)->venuename; ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <?php if (isset($addressbook)) { ?>
                                <?php if (isset($locid)) { ?>
                                    <a href="<?php echo admin_url('venues/onsitelocview/' . $locid . '?venue=' . $vnuid); ?>">
                                        <?php echo get_venueloc_data($locid)->locname; ?>
                                    </a>
                                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                                    <a href="<?php echo admin_url('addressbooks/view/') . $addressbook->addressbookid; ?>">
                                        <?php echo ucfirst($addressbook->firstname) . " " . ucfirst($addressbook->lastname); ?>
                                    </a>
                                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                                <?php } ?>
                                <span>Edit Contact</span>
                            <?php } else { ?>
                                <?php if (isset($locid)) { ?>
                                    <a href="<?php echo admin_url('venues/onsitelocview/' . $locid . '?venue=' . $vnuid); ?>">
                                        <?php echo get_venueloc_data($locid)->locname; ?>
                                    </a>
                                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                                <?php } ?>
                                <span>New Contact</span>
                            <?php } ?>
                        <?php } ?>


                    <?php } else { ?>

                        <?php if (isset($lid)) { ?>
                            <a href="<?php echo admin_url('leads/'); ?>">Leads</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('leads/dashboard/' . $lid); ?>"><?php echo($lname); ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('addressbooks') . '?lid=' . $lid; ?>">Contacts</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } elseif (isset($pid)) {
                            ?>
                            <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('projects/dashboard/' . $pid); ?>"><?php echo($lname); ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('addressbooks') . '?pid=' . $pid; ?>">Contacts</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } else { ?>
                            <a href="<?php echo admin_url('addressbooks'); ?>">Contacts</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } ?>


                        <?php if (isset($addressbook->addressbookid)) { ?>
                            <a href="<?php echo admin_url('addressbooks/view/' . $addressbook->addressbookid); ?>"><?php echo ucfirst($addressbook->firstname) . " " . ucfirst($addressbook->lastname); ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <span>Edit</span>
                        <?php } else { ?>
                            <span><?php echo isset($addressbook) ? ucfirst($addressbook->firstname) . " " . ucfirst($addressbook->lastname) : "New Conatct" ?></span>
                        <?php } ?>
                    <?php } ?>

                </div>
                <h1 class="pageTitleH1"><i class="fa fa-address-book-o"></i><?php echo $title; ?></h1>
                <?php if (isset($addressbook)) { ?>
                    <?php echo form_hidden('addressbookid', $addressbook->addressbookid); ?>
                <?php } ?> </h1>
                <div class="clearfix"></div>
                <h5 class="pull-left"><strong>Profile</strong></h5>
                <?php if (isset($addressbook)) { ?>
                    <span class="display-block pull-right"><a class=""
                                                              href="<?php echo admin_url('addressbooks/view/' . $addressbook->addressbookid) ?>"><i
                                    class="fa fa-eye"></i><span class="mleft5">View Contact</span></a></span>
                <?php } ?>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <?php /*if ($profile_allow == 1) { */?>
                                    <div class="profile-pic">
                                        <?php /*if ((isset($addressbook) && $addressbook->profile_image == NULL) || !isset($addressbook)) { */ ?><!--
                                            <div class="form-group uploadProfilepic">
                                                <label for="profile_image"
                                                       class="profile-image"><?php /*echo _l('staff_edit_profile_image'); */ ?></label>
                                                <i class="fa fa-question-circle" data-toggle="tooltip"
                                                   data-title="<?php /*echo _l('profile_dimension'); */ ?>"></i>
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
                                        <?php /*} */ ?>
                                        <?php /*if (isset($addressbook) && $addressbook->profile_image != NULL) { */ ?>
                                            <div class="form-group">
                                                <div class="addressbook-profile_blk">
                                                    <?php /*echo addressbook_profile_image($addressbook->addressbookid, array('profile_image', 'img-responsive', 'addressbook-profile-image-thumb'), 'thumb'); */ ?>
                                                </div>
                                                <a href="<?php /*echo admin_url('addressbooks/remove_addressbook_profile_image/' . $addressbook->addressbookid); */ ?>"><i
                                                            class="fa fa-remove"></i><span
                                                            class="mleft5 mtop8"><?php /*echo _l('remove') */ ?></span></a>
                                            </div>
                                        --><?php /*} */ ?>
                                        <?php
                                        $src = "";
                                        if ((isset($addressbook) && $addressbook->profile_image != NULL)) {
                                            $profileImagePath = FCPATH . 'uploads/addressbook_profile_images/' . $addressbook->addressbookid . '/round_' . $addressbook->profile_image;
                                            if (file_exists($profileImagePath)) {
                                                $src = base_url() . 'uploads/addressbook_profile_images/' . $addressbook->addressbookid . '/round_' . $addressbook->profile_image;
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
                                                    <a class="clicktoaddimage" href="javascript:void(0)" onclick="croppedDelete('profile');">
                                                        <span><i class="fa fa-trash"></i></span>
                                                    </a>
                                                    <a class="recropIcon_blk" href="javascript:void(0)" onclick="reCropp('profile');">
                                                        <span><i class="fa fa-crop" aria-hidden="true"></i></span>
                                                    </a>
                                                </div>
                                            <?php } else { ?>
                                                <div class="actionToEdit">
                                                    <a class="_delete clicktoaddimage"
                                                    href="<?php echo admin_url('addressbooks/remove_addressbook_profile_image/' . $addressbook->addressbookid); ?>">
                                                        <span><i class="fa fa-trash"></i></span></a>
                                                </div>
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
                                                            type="button" id="" onclick="croppedResullt('profile');">
                                                        <?php echo _l('save'); ?>
                                                    </button>
                                                    <button type="button" class="btn btn-default actionCancel"
                                                            data-dismiss="modal" onclick="croppedCancel('profile');">
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
                                <?php /*} else { */?><!--
                                    <input type="hidden" name="profile_image" value="">
                                --><?php /*} */?>
                            </div>
                            <div class="col-sm-9">
                                <?php if (isset($lid) || isset($eid) || isset($pid)) { ?>
                                    <?php
                                    $rel_type = '';
                                    $rel_id = '';
                                    if (isset($addressbook) || ($this->input->get('rel_id') && $this->input->get('rel_type'))) {
                                        if ($this->input->get('rel_id')) {
                                            $rel_id = $this->input->get('rel_id');
                                            $rel_type = $this->input->get('rel_type');
                                        } else {
                                            $rel_id = $addressbook->rel_id;
                                            $rel_type = $addressbook->rel_type;
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
                                    }
                                    ?>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="rel_type"
                                                       class="control-label"><?php echo _l('task_related_to'); ?></label>
                                                <select name="rel_type" class="selectpicker" id="rel_type"
                                                        data-width="100%"
                                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <option value=""></option>
                                                    <?php if (isset($lid) || (!isset($eid) && !isset($pid))) { ?>
                                                        <option value="lead" <?php if (isset($addressbook) || isset($lid) || $this->input->get('rel_type')) {
                                                            if ($rel_type == 'lead') {
                                                                echo 'selected';
                                                            }
                                                        } ?>>
                                                            <?php echo _l('lead'); ?>
                                                        </option>
                                                    <?php } ?>
                                                    <?php if (isset($pid) || (!isset($eid) && !isset($lid))) { ?>
                                                        <option value="project" <?php if (isset($addressbook) || isset($pid) || $this->input->get('rel_type')) {
                                                            if ($rel_type == 'project') {
                                                                echo 'selected';
                                                            }
                                                        } ?>>
                                                            <?php echo _l('project'); ?>
                                                        </option>
                                                    <?php } ?>
                                                    <?php if ((isset($pid) || isset($eid)) || !isset($lid)) { ?>
                                                        <option value="event" <?php if (isset($addressbook) || isset($eid) || $this->input->get('rel_type')) {
                                                            if ($rel_type == 'event') {
                                                                echo 'selected';
                                                            }
                                                        } ?>>
                                                            Sub-Projects
                                                        </option>
                                                    <?php } ?>
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
                                <?php } ?>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <div class="checkbox checkbox-primary mtop0" title="Company">
                                                <input value="1" type="checkbox" name="company"
                                                       id="company" <?php if (isset($addressbook)) {
                                                    if ($addressbook->company == 1) {
                                                        echo 'checked';
                                                    }
                                                }; ?>>
                                                <label for="company">Company</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row companydetails">
                                    <div class="col-sm-6">
                                        <?php $companyname = (isset($addressbook) ? $addressbook->companyname : ''); ?>
                                        <?php echo render_input('companyname', 'Company Name', $companyname, 'text'); ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?php $companytitle = (isset($addressbook) ? $addressbook->companytitle : ''); ?>
                                        <?php echo render_input('companytitle', 'Title', $companytitle, 'text'); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <?php $attrs = (isset($addressbook) ? array() : array('autofocus' => true)); ?>
                                        <?php $firstname = (isset($addressbook) ? $addressbook->firstname : ''); ?>
                                        <?php echo render_input('firstname', 'First Name', $firstname, 'text', $attrs); ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <?php $lastname = (isset($addressbook) ? $addressbook->lastname : ''); ?>
                                        <?php echo render_input('lastname', 'Last Name', $lastname, 'text'); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="gender" class="control-label">Gender</label>
                                            <select id="gender" class="selectpicker" name="gender" data-width="100%"
                                                    data-none-selected-text="Select" data-live-search="false">
                                                <option value=""></option>
                                                <option value="male" <?php echo isset($addressbook) && $addressbook->gender == "male" ? "selected='selected'" : ""; ?>>
                                                    Male
                                                </option>
                                                <option value="female" <?php echo (isset($addressbook) && $addressbook->gender == "female") ? "selected='selected'" : ""; ?>>
                                                    Female
                                                </option>
                                                <option value="others" <?php echo isset($addressbook) && $addressbook->gender == "others" ? "selected='selected'" : ""; ?>>
                                                    Other
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="tags" class="control-label">Tags
                                                <!--<small class="req text-danger">*</small>-->
                                            </label>
                                            <select name="tags[]" id="tags[]" class="form-control selectpicker"
                                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                                    data-live-search="true" multiple>
                                                <?php
                                                foreach ($tags as $tag) {
                                                    $tselected = '';
                                                    if (in_array($tag['id'], $addressbook->tags_id)) {
                                                        $tselected = "selected='selected'";
                                                    }
                                                    echo '<option value="' . $tag['id'] . '" ' . $tselected . '>' . $tag['name'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <?php

                                            $mode_of_communication = isset($addressbook) ? explode(',', $addressbook->mode_of_communication) : array();
                                            ?>
                                            <label for="mode_of_communication" class="control-label">Preferred Mode of
                                                Communication</label>
                                            <select name="mode_of_communication[]" id="mode_of_communication[]"
                                                    class="form-control selectpicker" data-none-selected-text="Select"
                                                    data-width="100%" data-live-search="true" multiple>
                                                <option value="email" <?php echo in_array('email', $mode_of_communication) ? "selected" : "" ?> >
                                                    Email
                                                </option>
                                                <option value="text" <?php echo in_array('text', $mode_of_communication) ? "selected" : "" ?>>
                                                    Text
                                                </option>
                                                <option value="phone" <?php echo in_array('phone', $mode_of_communication) ? "selected" : "" ?>>
                                                    Phone
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="row">
                                            <?php if ($global_search_allow == 1) { ?>
                                                <div class="col-lg-7 col-md-6 col-sm-12">
                                                    <label class="control-label hidden-xs">&nbsp;</label>
                                                    <div class="form-group">
                                                        <div class="checkbox checkbox-primary"
                                                             title="Allow Global Search?">
                                                            <input value="1" type="checkbox" name="ispublic"
                                                                   class="ispublic"
                                                                   id="contact_0_ispublic" <?php if (isset($addressbook)) {
                                                                if ($addressbook->ispublic == 1) {
                                                                    echo 'checked';
                                                                }
                                                            }; ?>>
                                                            <label for="contact_0_ispublic"><?php echo _l('shared') ?></label>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } else { ?>
                                                <input type="hidden" name="ispublic" value="0">
                                            <?php } ?>
                                            <?php
                                            if (!empty($favorite) && isset($favorite->favoriteid)) {
                                                $icon = "fa-star";
                                                $selected = "checked";
                                            } else {
                                                $icon = "fa-star-o";
                                                $selected = "";
                                            }
                                            ?>
                                            <div class="col-lg-5 col-md-6 col-sm-12">
                                                <label class="control-label hidden-xs">&nbsp;</label>
                                                <div class="form-group">
                                                    <input type="checkbox" name="favourite" class="hidden favourite"
                                                           id="favourite" <?php echo $selected; ?>/>
                                                    <label for="favourite" class="favourite_label">
                                                        <i class="fa <?php echo $icon; ?>"></i><span
                                                                class="mleft5"><?php echo _l('mark_as_favourite') ?></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="row row-flex">
                    <div class="col-sm-6">
                        <h5><strong>Email</strong></h5>
                        <div class="panel_s btmbrd">
                            <div class="panel-body">
                                <?php if (isset($addressbook)) {
                                    $pe = 0; ?>
                                    <?php if (!empty($addressbook->email)) { ?>
                                        <?php foreach ($addressbook->email as $pk => $pv) { ?>
                                            <div class="row" id="email-<?php echo $pe; ?>">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="email[<?php echo $pe; ?>][type]"
                                                               class="control-label">Type</label>
                                                        <select name="email[<?php echo $pe; ?>][type]"
                                                                id="email[<?php echo $pe; ?>][type]"
                                                                class="form-control selectpicker"
                                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                            <?php if ($pe == 0) {
                                                                echo '<option value="primary" selected="selected">Primary</option>';
                                                            } else {
                                                                foreach ($email_phone_type as $eptk => $eptv) {
                                                                    $tselected = '';
                                                                    if ($eptk == $pv['type']) {
                                                                        $tselected = "selected='selected'";
                                                                    }
                                                                    echo '<option value="' . $eptk . '" ' . $tselected . '>' . $eptv . '</option>';
                                                                }
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-8 col-xs-1 0 multiemail">
                                                    <?php $email = (isset($pv['email']) ? $pv['email'] : ''); ?>
                                                    <?php echo render_input('email[' . $pe . '][email]', '<small class="req text-danger">* </small>Email', $email, 'email', array('autocomplete' => 'off', 'data-addressbookemailid' => $pv['addressbookemailid'])); ?>
                                                </div>
                                                <?php if ($pe != 0) { ?>
                                                    <div class="col-sm-1 col-xs-2">
                                                        <button class="email-remove-me"
                                                                id="emailremove-<?php echo $pe; ?>">
                                                            <i class="fa fa-trash-o"></i>
                                                        </button>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <?php $pe++;
                                        } ?>
                                    <?php } else { ?>
                                        <div class="row" id="email-0">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="email[0][type]" class="control-label">Type</label>
                                                    <select name="email[0][type]" id="email[0][type]"
                                                            class="form-control selectpicker"
                                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                        <?php
                                                        echo '<option value="primary">Primary</option>';
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-7 multiemail">
                                                <?php $email = ''; ?>
                                                <?php echo render_input('email[0][email]', '<small class="req text-danger">* </small>Email', $email, 'email', array('autocomplete' => 'off')); ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <div class="row" id="email-0">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="email[0][type]" class="control-label">Type</label>
                                                <select name="email[0][type]" id="email[0][type]"
                                                        class="form-control selectpicker"
                                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <?php
                                                    echo '<option value="primary">Primary</option>';
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-8 multiemail">
                                            <?php $email = (isset($addressbook) ? $addressbook->email : ''); ?>
                                            <?php echo render_input('email[0][email]', '<small class="req text-danger">* </small>Email', $email, 'email', array('autocomplete' => 'off')); ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="text-left">
                                    <button id="email-add-more" name="email-add-more" class="btn btn-primary">
                                        <i class="fa fa-plus"></i><span class="mleft5">Add email</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h5><strong>Online</strong></h5>
                        <div class="panel_s btmbrd">
                            <div class="panel-body">
                                <?php if (isset($addressbook)) {
                                    $pw = 0; ?>
                                    <?php if (!empty($addressbook->website)) { ?>
                                        <?php foreach ($addressbook->website as $pk => $pv) { ?>
                                            <div class="row" id="website-<?php echo $pw; ?>">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="website[<?php echo $pw; ?>][type]"
                                                               class="control-label">Type</label>
                                                        <select name="website[<?php echo $pw; ?>][type]"
                                                                id="website[<?php echo $pw; ?>][type]"
                                                                class="form-control social_web selectpicker"
                                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                            <?php foreach ($socialsettings as $social) {
                                                                $sselected = '';
                                                                if ($social['socialid'] == $pv['type']) {
                                                                    $sselected = "selected='selected'";
                                                                }
                                                                echo '<option value="' . $social['socialid'] . '" ' . $sselected . '>' . $social['name'] . '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-8 col-xs-10">
                                                    <?php $website = (isset($pv['url']) ? $pv['url'] : ''); ?>
                                                    <?php echo render_input('website[' . $pw . '][url]', 'Address', $website); ?>
                                                </div>
                                                <?php if ($pw != 0) { ?>
                                                    <div class="col-sm-1 col-xs-2">
                                                        <label class="control-label" for="">&nbsp;</label>
                                                        <button class="website-remove-me"
                                                                id="websiteremove-<?php echo $pw; ?>"><i
                                                                    class="fa fa-trash-o"></i>
                                                        </button>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <?php $pw++;
                                        } ?>
                                    <?php } else { ?>
                                        <div class="row" id="website-0">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="website[0][type]" class="control-label">Type</label>
                                                    <select name="website[0][type]" id="website[0][type]"
                                                            class="form-control selectpicker"
                                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                        <?php
                                                        foreach ($socialsettings as $social) {
                                                            echo '<option value="' . $social['socialid'] . '" >' . $social['name'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-8">
                                                <?php $website = ''; ?>
                                                <?php echo render_input('website[0][url]', 'Address', $website); ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <div class="row" id="website-0">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="website[0][type]" class="control-label">Type</label>
                                                <select name="website[0][type]" id="website[0][type]"
                                                        class="form-control selectpicker"
                                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <?php
                                                    foreach ($socialsettings as $social) {
                                                        echo '<option value="' . $social['socialid'] . '" >' . $social['name'] . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-8">
                                            <?php $website = (isset($addressbook) ? $addressbook->website : ''); ?>
                                            <?php echo render_input('website[0][url]', 'Address', $website); ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="text-left">
                                    <button id="website-add-more" name="website-add-more" class="btn btn-primary">
                                        <i class="fa fa-plus"></i><span class="mleft5">Add Link</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <h5><strong>Phone</strong></h5>
                        <div class="panel_s btmbrd">
                            <div class="panel-body">
                                <?php if (isset($addressbook)) {
                                    $pp = 0; ?>
                                    <?php if (!empty($addressbook->phone)) { ?>
                                        <?php foreach ($addressbook->phone as $pk => $pv) { ?>
                                            <div class="row" id="phone-<?php echo $pp; ?>">
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="phone[<?php echo $pp; ?>][type]"
                                                               class="control-label">Type</label>
                                                        <select name="phone[<?php echo $pp; ?>][type]"
                                                                id="phone[<?php echo $pp; ?>][type]"
                                                                class="form-control selectpicker"
                                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                            <?php if ($pp == 0) {
                                                                echo '<option value="primary" selected="selected">Primary</option>';
                                                            } else {
                                                                foreach ($email_phone_type as $eptk => $eptv) {
                                                                    $tselected = '';
                                                                    if ($eptk == $pv['type']) {
                                                                        $tselected = "selected='selected'";
                                                                    }
                                                                    echo '<option value="' . $eptk . '" ' . $tselected . '>' . $eptv . '</option>';
                                                                }
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-xs-6 multiphone">
                                                    <div class="form-group">
                                                        <?php $phone = (isset($pv['phone']) ? $pv['phone'] : ''); ?>
                                                        <?php echo render_input('phone[' . $pp . '][phone]', 'client_phonenumber', $phone, 'phone', array('autocomplete' => 'off')); ?>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2 col-xs-6 multiext">
                                                    <div class="form-group">
                                                        <?php $phone = (isset($pv['ext']) ? $pv['ext'] : ''); ?>
                                                        <?php echo render_input('phone[' . $pp . '][ext]', 'Ext', $phone, 'tel', array('autocomplete' => 'off', 'maxlength' => 5,)); ?>
                                                    </div>
                                                </div>
                                                <?php if ($pp != 0) { ?>
                                                    <div class="col-sm-1 col-xs-2">
                                                        <button class="phone-remove-me"
                                                                id="phoneremove-<?php echo $pp; ?>"><i
                                                                    class="fa fa-trash-o"></i>
                                                        </button>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <?php $pp++;
                                        } ?>
                                    <?php } else { ?>
                                        <div class="row" id="phone-0">
                                            <div class="col-sm-3">
                                                <label for="phone[0][type]" class="control-label">Type</label>
                                                <select name="phone[0][type]" id="phone[0][type]"
                                                        class="form-control selectpicker"
                                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <?php
                                                    echo '<option value="primary" selected="selected">Primary</option>';
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-sm-6  multiphone">
                                                <?php $phonenumber = ''; ?>
                                                <?php echo render_input('phone[0][phone]', 'client_phonenumber', $phonenumber); ?>
                                            </div>
                                            <div class="col-sm-2 col-xs-10 multiext">
                                                <?php $phone = ''; ?>
                                                <?php echo render_input('phone[0][ext]', 'Ext', $phone, 'tel', array('autocomplete' => 'off', 'maxlength' => 5)); ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <div class="row" id="phone-0">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="phone[0][type]" class="control-label">Type</label>
                                                <select name="phone[0][type]" id="phone[0][type]"
                                                        class="form-control selectpicker"
                                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <?php
                                                    echo '<option value="primary" selected="selected">Primary</option>';
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 multiphone">
                                            <?php $phonenumber = (isset($addressbook) ? $addressbook->phonenumber : ''); ?>
                                            <?php echo render_input('phone[0][phone]', 'client_phonenumber', $phonenumber); ?>
                                        </div>
                                        <div class="col-sm-2 col-xs-10 multiext">
                                            <?php $phone = ''; ?>
                                            <?php echo render_input('phone[0][ext]', 'Ext', $phone, 'tel', array('autocomplete' => 'off', 'maxlength' => 5)); ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="text-left">
                                    <button id="phone-add-more" name="phone-add-more" class="btn btn-primary">
                                        <i class="fa fa-plus"></i><span class="mleft5">Add Phone</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h5><strong>Address</strong></h5>
                        <div class="panel_s btmbrd">
                            <div class="panel-body">
                                <?php if (isset($addressbook)) {
                                    $pa = 0; ?>
                                    <?php if (!empty($addressbook->address)) { ?>
                                        <?php foreach ($addressbook->address as $pk => $pv) { ?>
                                            <div id="address-<?php echo $pa; ?>" class="col-sm-12">
                                                <?php
                                                if (!empty($pv['address']) || !empty($pv['address2']) || !empty($pv['city']) || !empty($pv['state']) || !empty($pv['zip'])) {
                                                    $style = 'style="display:block"';
                                                    $style1 = 'style="display:none"';
                                                } else {
                                                    $style = 'style="display:none"';
                                                    $style1 = 'style="display:block"';
                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <label for="address[<?php echo $pa; ?>][type]"
                                                               class="control-label">Type</label>
                                                        <select name="address[<?php echo $pa; ?>][type]"
                                                                id="address[<?php echo $pa; ?>][type]"
                                                                class="form-control selectpicker"
                                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                            <?php
                                                            if ($pa == 0) {
                                                                echo '<option value="primary" selected="selected">Primary</option>';
                                                            } else {
                                                                foreach ($address_type as $eptk => $eptv) {
                                                                    $aselected = '';
                                                                    if ($pv['type'] == $eptk) {
                                                                        $aselected = "selected='selected'";
                                                                    }
                                                                    echo '<option value="' . $eptk . '" ' . $aselected . '>' . $eptv . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-sm-8 col-xs-11">
                                                        <div class="row">
                                                            <div class="col-sm-8">
                                                                <div id="locationField" class="form-group">
                                                                    <label class="control-label" for="address">Address</label>
                                                                    <input id="autocomplete<?php echo $pa; ?>"
                                                                        class="form-control searchmap"
                                                                        data-addmap="<?php echo $pa; ?>"
                                                                        placeholder="Search Google Maps..."
                                                                        onFocus="geolocate()"
                                                                        type="text">
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-4">
                                                                <div class="customadd-btn">
                                                                    <?php /*if (empty($pv['address']) && empty($pv['address2']) && empty($pv['city']) && empty($pv['state']) && empty($pv['zip'])) { */ ?>
                                                                    <button type="button"
                                                                            class="btn btn-info custom_address customadd-<?php echo $pa; ?>"
                                                                            data-addressid="<?php echo $pa; ?>">Custom
                                                                    </button>
                                                                    <?php /*} */ ?>
                                                                    <?php if (!empty($pv['address']) || !empty($pv['address2']) || !empty($pv['city']) || !empty($pv['state']) || !empty($pv['zip'])) { ?>
                                                                        <!--<button type="button"
                                                                                class="btn btn-default remove_address removeadd-<?php /*echo $pa; */ ?>"
                                                                                data-addressid="<?php /*echo $pa; */ ?>">Remove &
                                                                            Search
                                                                            Again
                                                                        </button>-->
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        
                                                    </div>
                                                    <div class="col-sm-1 col-xs-1">
                                                        <div class="form-group">

                                                            <?php if ($pa != 0) { ?>
                                                                <button class="address-remove-me"
                                                                        id="addressremove-<?php echo $pa; ?>"><i
                                                                            class="fa fa-trash-o"></i>
                                                                </button>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="customaddress-<?php echo $pa; ?>"
                                                     class="addressdetails customaddress-<?php echo $pa; ?>" <?php echo $style; ?> >

                                                    <div class="row">
                                                        <div class="col-xs-11">
                                                            <?php $address = (isset($pv['address']) ? $pv['address'] : ''); ?>
                                                            <?php echo render_input('address[' . $pa . '][street_number]', 'Address1', $address); ?>
                                                        </div>

                                                        <div class="col-xs-1">
                                                            <div data-id="#customaddress-<?php echo $pa; ?>"
                                                                 class="exp_clps_address">
                                                                <a href="javascript:void(0)"><i
                                                                            class="fa fa-caret-up"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="address_extra">
                                                        <div class="row">
                                                            <div class="col-xs-11">
                                                                <?php $address2 = (isset($pv['address2']) ? $pv['address2'] : ''); ?>
                                                                <?php echo render_input('address[' . $pa . '][route]', 'Address2', $address2); ?>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                            <div class="col-sm-5">
                                                                <?php $city = (isset($pv['city']) ? $pv['city'] : ''); ?>
                                                                <?php echo render_input('address[' . $pa . '][locality]', 'client_city', $city); ?>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <?php $state = (isset($pv['state']) ? $pv['state'] : ''); ?>
                                                                <?php echo render_input('address[' . $pa . '][administrative_area_level_1]', 'client_state', $state); ?>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-sm-5">
                                                                <?php $zip = (isset($pv['zip']) ? $pv['zip'] : ''); ?>
                                                                <?php echo render_input('address[' . $pa . '][postal_code]', 'client_postal_code', $zip); ?>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="address[<?php echo $pa; ?>][country]"
                                                                           class="control-label">Country</label>
                                                                    <select name="address[<?php echo $pa; ?>][country]"
                                                                            id="address[<?php echo $pa; ?>][country]"
                                                                            class="form-control selectpicker"
                                                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                                        <option value="US" selected>United States
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php $pa++;
                                        } ?>
                                    <?php } else { ?>

                                        <div id="address-0">
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <label for="address[0][type]" class="control-label">Type</label>
                                                    <select name="address[0][type]" id="address[0][type]"
                                                            class="form-control selectpicker"
                                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                        <?php
                                                        echo '<option value="primary" selected="selected">Primary</option>';
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="row">
                                                        <div class="col-sm-8">
                                                            <div id="locationField" class="form-group">
                                                                <label class="control-label" for="address">Address</label>
                                                                <input id="autocomplete0" class="form-control searchmap"
                                                                    data-addmap="0"
                                                                    placeholder="Search Google Maps..." onFocus="geolocate()"
                                                                    type="text">
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="customadd-btn">
                                                                <div class="form-group">
                                                                    <!--<label class="control-label" for="search">&nbsp</label>-->
                                                                    <button type="button"
                                                                            class="btn btn-info custom_address customadd-0"
                                                                            style="display:block" data-addressid="0">Custom
                                                                    </button>
                                                                    <!--<button type="button"
                                                                            class="btn btn-default remove_address removeadd-0"
                                                                            style="display:none" data-addressid="0">Remove & Search
                                                                        Again
                                                                    </button>-->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    

                                                </div>
                                            </div>
                                            <?php
                                            if (isset($addressbook)) {
                                                if (!empty($addressbook->address) || !empty($addressbook->address2) || !empty($addressbook->city) || !empty($addressbook->state) || !empty($addressbook->zip)) {
                                                    $style = 'style="display:block"';
                                                } else {
                                                    $style = 'style="display:none"';
                                                }
                                            } else {
                                                $style = 'style="display:none"';
                                            }
                                            ?>
                                            <div class="addressdetails customaddress-0" <?php echo $style; ?> >
                                                <div class="row">
                                                    <div class="col-sm-11">
                                                        <?php $address = ''; ?>
                                                        <?php echo render_input('address[0][street_number]', 'Address2', $address); ?>
                                                    </div>

                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-11">
                                                        <?php $address2 = ''; ?>
                                                        <?php echo render_input('address[0][route]', 'Address1', $address2); ?>
                                                    </div>
                                                    <div class="col-xs-1">
                                                        <div data-id="#customaddress-<?php echo $pa; ?>"
                                                             class="exp_clps_address">
                                                            <a href="javascript:void(0)"><i class="fa fa-caret-up"></i></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <?php $city = ''; ?>
                                                        <?php echo render_input('address[0][locality]', 'client_city', $city); ?>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <?php $state = ''; ?>
                                                        <?php echo render_input('address[0][administrative_area_level_1]', 'client_state', $state); ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <?php $zip = ''; ?>
                                                        <?php echo render_input('address[0][postal_code]', 'client_postal_code', $zip); ?>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <div class="form-group">
                                                            <label for="address[0][country]"
                                                                   class="control-label">Country</label>
                                                            <select name="address[0][country]" id="address[0][country]"
                                                                    class="form-control selectpicker"
                                                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                                <option value="US" selected>United States</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <div id="address-0">
                                        <div class="row">
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="address[0][type]" class="control-label">Type</label>
                                                    <select name="address[0][type]" id="address[0][type]"
                                                            class="form-control selectpicker"
                                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                        <?php
                                                        echo '<option value="primary" selected="selected">Primary</option>';
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-8 col-xs-11">
                                                <div class="row">
                                                    <div class="col-sm-8">
                                                        <div id="locationField" class="form-group">
                                                            <label class="control-label" for="address">Address</label>
                                                            <input id="autocomplete0" class="form-control searchmap"
                                                                data-addmap="0"
                                                                placeholder="Search Google Maps..." onFocus="geolocate()"
                                                                type="text">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="customadd-btn">
                                                            <div class="form-group">
                                                                <button type="button"
                                                                        class="btn btn-info custom_address customadd-0"
                                                                        style="display:block" data-addressid="0">Custom
                                                                </button>
                                                                <!--<button type="button"
                                                                        class="btn btn-default remove_address removeadd-0"
                                                                        style="display:none" data-addressid="0">Remove & Search
                                                                    Again
                                                                </button>-->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <?php
                                        if (isset($addressbook)) {
                                            if (!empty($addressbook->address) || !empty($addressbook->address2) || !empty($addressbook->city) || !empty($addressbook->state) || !empty($addressbook->zip)) {
                                                $style = 'style="display:block"';
                                            } else {
                                                $style = 'style="display:none"';
                                            }
                                        } else {
                                            $style = 'style="display:none"';
                                        }
                                        ?>
                                        <div id="customaddress-0"
                                             class="addressdetails customaddress-0" <?php echo $style; ?> >
                                            <div class="row">
                                                <div class="col-sm-11 col-xs-11">
                                                    <?php $address = (isset($addressbook) ? $addressbook->address : ''); ?>
                                                    <?php echo render_input('address[0][street_number]', 'Address1', $address); ?>
                                                </div>
                                                <div class="col-xs-1">
                                                    <div data-id="#customaddress-0"
                                                         class="exp_clps_address">
                                                        <a href="javascript:void(0)"><i class="fa fa-caret-up"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="address_extra">
                                                <div class="row">
                                                    <div class="col-sm-11">
                                                        <?php $address2 = (isset($addressbook) ? $addressbook->address2 : ''); ?>
                                                        <?php echo render_input('address[0][route]', 'Address2', $address2); ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <?php $city = (isset($addressbook) ? $addressbook->city : ''); ?>
                                                        <?php echo render_input('address[0][locality]', 'client_city', $city); ?>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <?php $state = (isset($addressbook) ? $addressbook->state : ''); ?>
                                                        <?php echo render_input('address[0][administrative_area_level_1]', 'client_state', $state); ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <?php $zip = (isset($addressbook) ? $addressbook->zip : ''); ?>
                                                        <?php echo render_input('address[0][postal_code]', 'client_postal_code', $zip); ?>
                                                    </div>
                                                    <div class="col-sm-5">
                                                        <div class="form-group">
                                                            <label for="address[0][country]"
                                                                   class="control-label">Country</label>
                                                            <select name="address[0][country]" id="address[0][country]"
                                                                    class="form-control selectpicker"
                                                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                                <option value="US" selected>United States</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="text-left col-sm-12">
                                    <button id="address-add-more" class="btn btn-primary">
                                        <i class="fa fa-plus"></i><span class="mleft5">Address</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="topButton">
                    <button class="btn btn-default" type="button"
                            onclick="fncancel();"><?php echo _l('Cancel'); ?></button>
                    <button type="submit" class="btn btn-info save_contact"><?php echo _l('submit'); ?></button>
                </div>
            </div>
            <?php if (isset($lid)) { ?>
                <input type="hidden" name="hdnlid" value="<?php echo $lid; ?>">
            <?php } elseif (isset($pid)) { ?>
                <input type="hidden" name="hdnpid" value="<?php echo $pid; ?>">
            <?php } elseif (isset($eid)) { ?>
                <input type="hidden" name="hdneid" value="<?php echo $eid; ?>">
            <?php } elseif (isset($venueid)) { ?>
                <input type="hidden" name="hdnvenueid" value="<?php echo $venueid; ?>">
            <?php } elseif (isset($locid)) { ?>
                <input type="hidden" name="hdnlocid" value="<?php echo isset($locid) ? $locid : ''; ?>">
                <input type="hidden" name="hdnvid" value="<?php echo isset($vid) ? $vid : ''; ?>">
            <?php } ?>

            <?php echo form_close(); ?>

        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
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
        var venueid =<?php if (isset($venueid)) {
            echo $venueid;
        } else {
            echo '0';
        }  ?>;
        var locid =<?php if (isset($locid)) {
            echo $locid;
        } else {
            echo '0';
        }  ?>;
        if (id > '0') {
            location.href = '<?php echo base_url(); ?>admin/addressbooks?lid=' + id;
        } else if (pid > '0') {
            location.href = '<?php echo base_url(); ?>admin/addressbooks?pid=' + pid;
        } else if (eid > '0') {
            location.href = '<?php echo base_url(); ?>admin/addressbooks?eid=' + eid;
        } else if (locid > '0') {
            var vid =<?php if (isset($vid)) {
                echo $vid;
            } else {
                echo '0';
            }  ?>;
            location.href = '<?php echo base_url(); ?>admin/venues/onsitelocview/' + locid + '?venue=' + vid;
        } else if (venueid > '0') {
            location.href = '<?php echo base_url(); ?>admin/venues/view/' + venueid;
        } else {
            location.href = '<?php echo base_url(); ?>admin/addressbooks';
        }
    }
</script>
<script>
    _validate_form($('.addressbook-form'), {
        firstname: 'required',
        lastname: 'required',
        // email: {
        //   required:true,
        //   email:true,
        //   remote:{
        //      url: site_url + "admin/misc/addressbook_email_exists",
        //      type:'post',
        //      data: {
        //       email:function(){
        //        return $('input[name="email"]').val();
        //      },
        //       addressbookid:function(){
        //        return $('input[name="addressbookid"]').val();
        //       }
        //    }}
        // },
        companyname: {
            required: {
                depends: function (element) {
                    return ($('input[name="company"]').val() == '1') ? true : false
                }
            }
        },
        companytitle: {
            required: {
                depends: function (element) {
                    return ($('input[name="company"]').val() == '1') ? true : false
                }
            }
        },
        /*'tags[]': 'required'*/
    });


    // Code for multiple email validation
    /*var createEmailValidation = function () {

        $(".multiemail .form-control").each(function (index, value) {
            $(this).rules('remove');
            $(this).rules('add', {
                email: true,
                required: true,
                remote: {
                    url: site_url + "admin/misc/addressbook_email_exists",
                    type: 'post',
                    data: {
                        email: function () {
                            return $(value).val();
                        },
                        addressbookid: function () {
                            return $('input[name="addressbookid"]').val();
                        },
                        addressbookemailid:function(){
                            return $(value).data('addressbookemailid');
                        }
                    }
                },
                messages: {
                    email: "Please enter valid email.",
                    required: "Please enter an email adress.",
                    remote: "Email already exist."
                }
            });
        });
    }*/

    // Code for multiple phone validation
    var createPhoneValidation = function () {
        $(".multiphone .form-control").each(function () {
            $(this).mask("(999) 999-9999", {placeholder: "(___) ___-____"});
        });
    }
    var createExtValidation = function () {
        $(".multiext .form-control").each(function () {
            $(this).mask("99999", {placeholder: "12345"});
        });
    }
    showcompany();
    $('#company').on('click', function () {
        showcompany();
    });

    //$(".addressdetails").hide()
    //$(".removeadd-0").hide();
    $('.custom_address').on('click', function () {
        var addressid = $(this).data('addressid');
        $(".customaddress-" + addressid).show();
    });
    $('.remove_address').on('click', function () {
        var addressid = $(this).data('addressid');
        $("#autocomplete" + addressid).val('');
        $("#address[" + addressid + "][street_number]").val('');
        $("#address[" + addressid + "][route]").val('');
        $("#address[" + addressid + "][locality]").val('');
        $("#address[" + addressid + "][administrative_area_level_1]").val('');
        $("#address[" + addressid + "][postal_code]").val('');
        $(".customaddress-" + addressid).hide();
        $(this).hide();
        $(".customadd-" + addressid).show();
    });

    $("#rel_type").on('change', function () {
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

    function showcompany() {
        if ($('#company').is(":checked"))
            $(".companydetails").show();
        else
            $(".companydetails").hide();
    }


</script>
<script>
    // This example displays an address form, using the autocomplete feature
    // of the Google Places API to help users fill in the information.

    // This example requires the Places library. Include the libraries=places
    // parameter when you first load the API. For example:
    // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">


    // Start code of Add more / Remove email

    var email_phone_type = <?php echo json_encode($email_phone_type); ?>;
    $("#email-add-more").click(function (e) {
        e.preventDefault();
        var my_email_fields = $("div[id^='email-']");
        var highestemail = -Infinity;
        $.each(my_email_fields, function (mindex, mvalue) {
            var fieldEmailNum = mvalue.id.split("-");
            highestemail = Math.max(highestemail, parseFloat(fieldEmailNum[1]));
        });
        var emailnext = highestemail;
        var addtoEmail = "#email-" + emailnext;
        var addRemoveEmail = "#email-" + (emailnext);

        emailnext = emailnext + 1;

        var newemailIn = "";
        newemailIn += ' <div class="row" id="email-' + emailnext + '" name="email' + emailnext + '"><div class="col-sm-3"><div class="form-group"><label class="control-label" for="email[' + emailnext + '][type]">Type</label><select id="email[' + emailnext + '][type]" name="email[' + emailnext + '][type]" class="selectpicker" data-width="100%" data-none-selected-text="Select">';
        $.each(email_phone_type, function (etindex, etvalue) {
            newemailIn += '<option value="' + etindex + '">' + etvalue + '</option>';
        });

        newemailIn += '</select></div></div>';
        newemailIn += '<div class="col-sm-8 col-xs-10 multiemail"><div class="form-group"><label class="control-label" for="email[' + emailnext + '][email]"><small class="req text-danger">* </small>Email</label><input id="email[' + emailnext + '][email]" class="form-control" name="email[' + emailnext + '][email]" autocomplete="off" value="" type="email"></div>';
        newemailIn += '</div>';
        newemailIn += '<div class="col-sm-1 col-xs-2"><button id="emailremove-' + (emailnext) + '" class="email-remove-me" ><i class="fa fa-trash-o"></i></button></div></div>';
        var newemailInput = $(newemailIn);

        //var removeEmailButton = $(removeEmailBtn);
        $(addtoEmail).after(newemailInput);
        // $(addRemoveEmail).after(removeEmailButton);
        $("#email-" + emailnext).attr('data-source', $(addtoEmail).attr('data-source'));
        $("#count").val(emailnext);

        $('.email-remove-me').click(function (e) {
            e.preventDefault();
            var fieldEmailNum = this.id.split("-");
            var fieldEmailID = "#email-" + fieldEmailNum[1];
            $(fieldEmailID).remove();
        });
        $('.selectpicker').selectpicker('render');
        createEmailValidation();
    });
    createEmailValidation();
    $('.email-remove-me').click(function (e) {
        e.preventDefault();
        var fieldEmailNum = this.id.split("-");
        var fieldEmailID = "#email-" + fieldEmailNum[1];
        $(fieldEmailID).remove();
    });
    // End code of Add more / Remove email

    // Start code of Add more / Remove phone

    var email_phone_type = <?php echo json_encode($email_phone_type); ?>;
    $("#phone-add-more").click(function (e) {

        e.preventDefault();
        var my_phone_fields = $("div[id^='phone-']");
        var highestphone = -Infinity;
        $.each(my_phone_fields, function (mindex, mvalue) {
            var fieldphoneNum = mvalue.id.split("-");
            highestphone = Math.max(highestphone, parseFloat(fieldphoneNum[1]));
        });
        var phonenext = highestphone;
        var addtophone = "#phone-" + phonenext;
        var addRemovephone = "#phone-" + (phonenext);

        phonenext = phonenext + 1;
        var newphoneIn = "";
        newphoneIn += ' <div class="row" id="phone-' + phonenext + '" name="phone' + phonenext + '"><div class="col-sm-3"><div class="form-group"><label class="control-label" for="phone[' + phonenext + '][type]">Type</label><select id="phone[' + phonenext + '][type]" name="phone[' + phonenext + '][type]" class="selectpicker" data-width="100%" data-none-selected-text="Select">';
        $.each(email_phone_type, function (epindex, epvalue) {
            newphoneIn += '<option value="' + epindex + '">' + epvalue + '</option>';
        });

        newphoneIn += '</select></div></div>';
        newphoneIn += '<div class="col-sm-6 col-xs-8 multiphone"><div class="form-group"><label class="control-label" for="phone[' + phonenext + '][phone]">Phone</label><input id="phone[' + phonenext + '][phone]" class="form-control" name="phone[' + phonenext + '][phone]" autocomplete="off" value="" type="text"></div>';
        newphoneIn += '</div>';
        newphoneIn += '<div class="col-sm-2 col-xs-4 multiext"><div class="form-group"><label class="control-label" for="phone[' + phonenext + '][ext]">Ext</label><input id="phone[' + phonenext + '][ext]" class="form-control" name="phone[' + phonenext + '][ext]" autocomplete="off" maxlength=5 value="" type="tel"></div>';
        newphoneIn += '</div>';
        newphoneIn += '<div class="col-sm-1 col-xs-2"><button id="phoneremove-' + (phonenext) + '" class=" phone-remove-me" ><i class="fa fa-trash-o"></i></button></div></div>';
        var newphoneInput = $(newphoneIn);

        //var removephoneButton = $(removephoneBtn);
        $(addtophone).after(newphoneInput);
        // $(addRemovephone).after(removephoneButton);
        $("#phone-" + phonenext).attr('data-source', $(addtophone).attr('data-source'));
        $("#count").val(phonenext);

        $('.phone-remove-me').click(function (e) {
            e.preventDefault();
            var fieldPhoneNum = this.id.split("-");
            var fieldphoneID = "#phone-" + fieldPhoneNum[1];
            //$(this).parent('div').remove();
            $(fieldphoneID).remove();
        });
        createPhoneValidation();
        createExtValidation();
        $('.selectpicker').selectpicker('render');
    });
    createPhoneValidation();
    createExtValidation();
    $('.phone-remove-me').click(function (e) {
        e.preventDefault();
        var fieldPhoneNum = this.id.split("-");
        var fieldphoneID = "#phone-" + fieldPhoneNum[1];
        //$(this).parent('div').remove();
        $(fieldphoneID).remove();
    });
    // End code of Add more / Remove phone

    // Start code of Add more / Remove website

    var website_type = <?php echo json_encode($socialsettings); ?>;
    $("#website-add-more").click(function (e) {

        e.preventDefault();
        var my_website_fields = $("div[id^='website-']");
        var highestwebsite = -Infinity;
        $.each(my_website_fields, function (mindex, mvalue) {
            var fieldwebsiteNum = mvalue.id.split("-");
            highestwebsite = Math.max(highestwebsite, parseFloat(fieldwebsiteNum[1]));
        });
        var websitenext = highestwebsite;
        var addtowebsite = "#website-" + websitenext;
        var addRemovewebsite = "#website-" + (websitenext);
        websitenext = websitenext + 1;

        var newwebsiteIn = "";
        newwebsiteIn += ' <div class="row" id="website-' + websitenext + '" name="website' + websitenext + '"><div class="col-sm-3"><div class="form-group"><label class="control-label" for="website[' + websitenext + '][type]">Type</label><select id="website[' + websitenext + '][type]" name="website[' + websitenext + '][type]" class="selectpicker" data-width="100%" data-none-selected-text="Select">';
        $.each(website_type, function (windex, wvalue) {
            newwebsiteIn += '<option value="' + wvalue['socialid'] + '">' + wvalue['name'] + '</option>';
        });

        newwebsiteIn += '</select></div></div>';
        newwebsiteIn += '<div class="col-sm-8  col-xs-10"><div class="form-group"><label class="control-label" for="website[' + websitenext + '][url]">Address</label><input id="website[' + websitenext + '][url]" class="form-control" name="website[' + websitenext + '][url]" autocomplete="off" value="" type="text"></div>';
        newwebsiteIn += '</div>';
        newwebsiteIn += '<div class="col-sm-1  col-xs-2"><button id="websiteremove-' + (websitenext) + '" class="website-remove-me" ><i class="fa fa-trash-o"></i></button></div></div>';
        var newwebsiteInput = $(newwebsiteIn);
        $(addtowebsite).after(newwebsiteInput);
        $("#website-" + websitenext).attr('data-source', $(addtowebsite).attr('data-source'));
        $("#count").val(websitenext);

        $('.website-remove-me').click(function (e) {
            e.preventDefault();
            var fieldwebsiteNum = this.id.split("-");
            var fieldwebsiteID = "#website-" + fieldwebsiteNum[1];
            $(fieldwebsiteID).remove();
        });
        $('.selectpicker').selectpicker('render');
    });
    $('.website-remove-me').click(function (e) {
        e.preventDefault();
        var fieldwebsiteNum = this.id.split("-");
        var fieldwebsiteID = "#website-" + fieldwebsiteNum[1];
        $(fieldwebsiteID).remove();
    });
    // End code of Add more / Remove website

    // Start code of Add more / Remove address

    var address_type = <?php echo json_encode($address_type); ?>;
    $("#address-add-more").click(function (e) {

        e.preventDefault();
        var my_address_fields = $("div[id^='address-']");
        var highestaddress = -Infinity;
        $.each(my_address_fields, function (mindex, mvalue) {
            var fieldaddressNum = mvalue.id.split("-");
            highestaddress = Math.max(highestaddress, parseFloat(fieldaddressNum[1]));
        });
        var addressnext = highestaddress;
        var addtoaddress = "#address-" + addressnext;
        var addRemoveaddress = "#address-" + (addressnext);

        addressnext = addressnext + 1;
        var newaddressIn = "";
        newaddressIn += ' <div id="address-' + addressnext + '" class="col-sm-12"><div class="row"><div class="col-sm-3"><div class="form-group"><label for="address[' + addressnext + '][type]" class="control-label">Type</label><select name="address[' + addressnext + '][type]" id="address[' + addressnext + '][type]" class="form-control selectpicker" data-none-selected-text="Select">';
        $.each(address_type, function (aindex, avalue) {
            newaddressIn += '<option value="' + aindex + '">' + avalue + '</option>';
        });

        newaddressIn += '</select></div></div><div class="col-sm-8 col-xs-11"><div id="locationField" class="form-group"><label class="control-label" for="address">Address</label><input id="autocomplete' + addressnext + '" class="form-control searchmap" data-addmap="' + addressnext + '" placeholder="Search Google Maps..." onfocus="geolocate()" type="text"></div><div class="customadd-btn"><div class="form-group"><button type="button" class="btn btn-info custom_address customadd-' + addressnext + '" data-addressid="' + addressnext + '">Custom</button></div></div></div><div class="col-sm-1 col-xs-1"><button id="addressremove-' + (addressnext) + '" class=" address-remove-me"><i class="fa fa-trash-o"></i></button></div></div>';
        newaddressIn += ' <div id="customaddress-' + addressnext + '" class="addressdetails customaddress-' + addressnext + '" style="display:none"><div class="row"><div class="col-sm-11"><div class="form-group"><label for="address[' + addressnext + '][street_number]" class="control-label">Address1</label><input id="address[' + addressnext + '][street_number]" name="address[' + addressnext + '][street_number]" class="form-control" value="" type="text"></div></div><div class="col-xs-1"><div data-id="#customaddress-' + addressnext + '" class="exp_clps_address"><a href="javascript:void(0)"><i class="fa fa-caret-up"></i></a></div></div></div><div class="address_extra"><div class="row"><div class="col-sm-11"><div class="form-group"><label for="address[' + addressnext + '][route]" class="control-label">Address2</label><input id="address[' + addressnext + '][route]" name="address[' + addressnext + '][route]" class="form-control" value="" type="text"></div></div><div class="col-sm-6"><div class="form-group"><label for="address[' + addressnext + '][locality]" class="control-label">City</label><input id="address[' + addressnext + '][locality]" name="address[' + addressnext + '][locality]" class="form-control" value="" type="text"></div></div><div class="col-sm-5"><div class="form-group"><label for="address[' + addressnext + '][administrative_area_level_1]" class="control-label">State</label><input id="address[' + addressnext + '][administrative_area_level_1]" name="address[' + addressnext + '][administrative_area_level_1]" class="form-control" value="" type="text"></div></div></div><div class="row"><div class="col-sm-6"><div class="form-group"><label for="address[' + addressnext + '][postal_code]" class="control-label">Zip Code</label><input id="address[' + addressnext + '][postal_code]" name="address[' + addressnext + '][postal_code]" class="form-control" value="" type="text"></div></div><div class="col-sm-5"><div class="form-group"><label for="address[' + addressnext + '][country]" class="control-label">Country</label><select name="address[' + addressnext + '][country]" id="address[' + addressnext + '][country]" class="form-control selectpicker" data-none-selected-text="Select" ><option value="US" selected="">United States</option></select></div></div></div></div></div>';

        newaddressIn += '</div></div>';
        var newaddressInput = $(newaddressIn);

        // var removeaddressButton = $(removeaddressBtn);
        $(addtoaddress).after(newaddressInput);

        //$(addRemoveaddress).after(removeaddressButton);
        $("#address-" + addressnext).attr('data-source', $(addtoaddress).attr('data-source'));
        $("#count").val(addressnext);
        $(".removeadd-" + addressnext).hide();
        $('.custom_address').on('click', function () {
            var addressid = $(this).data('addressid');
            $(".customaddress-" + addressid).show();
        });
        $('.remove_address').on('click', function () {
            var addressid = $(this).data('addressid');
            $("#autocomplete" + addressid).val('');
            $("#address[" + addressid + "][street_number]").val('');
            $("#address[" + addressid + "][route]").val('');
            $("#address[" + addressid + "][locality]").val('');
            $("#address[" + addressid + "][administrative_area_level_1]").val('');
            $("#address[" + addressid + "][postal_code]").val('');
            $(".customaddress-" + addressid).hide();
            $(this).hide();
            $(".customadd-" + addressid).show();
        });

        $('.address-remove-me').click(function (e) {
            e.preventDefault();
            var fieldaddressNum = this.id.split("-");
            var fieldaddressID = "#address-" + fieldaddressNum[1];
            $(fieldaddressID).remove();
        });
        $('.selectpicker').selectpicker('render');
        $(".searchmap").on("keyup, change, keypress, keydown, click", function () {
            // alert("here");
            var searchmapid = $(this).data('addmap');
            initAutocomplete(searchmapid);
        });
    });
    $('.address-remove-me').click(function (e) {
        e.preventDefault();
        var fieldaddressNum = this.id.split("-");
        var fieldaddressID = "#address-" + fieldaddressNum[1];
        $(fieldaddressID).remove();
    });

    $(".searchmap").on("keyup, change, keypress, keydown, click", function () {
        // alert("here1");
        var searchmapid = $(this).data('addmap');
        initAutocomplete(searchmapid);
    });

    var placeSearch, autocomplete;
    var componentForm = {
        street_number: 'short_name',
        /*route: 'long_name',*/
        locality: 'long_name',
        administrative_area_level_1: 'long_name',
        country: 'short_name',
        postal_code: 'short_name'
    };

    // function initAutocomplete() {
    //     // Create the autocomplete object, restricting the search to geographical
    //     // location types.
    //    autocomplete = new google.maps.places.Autocomplete(
    //         /** @type {!HTMLInputElement} */(document.getElementById('autocomplete0')),
    //         {types: ['geocode'],  componentRestrictions: {country: 'us'}});

    //     // When the user selects an address from the dropdown, populate the address
    //     // fields in the form.
    //     autocomplete.addListener('place_changed', fillInAddress);
    // }

    function initAutocomplete(addid) {
        // Create the autocomplete object, restricting the search to geographical
        // location types.
        // alert(addid);
        addid = addid;

        //alert(addid);

        autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('autocomplete' + addid)),
            {types: ['geocode'], componentRestrictions: {country: 'us'}});

        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', function () {
            //google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();

            for (var component in componentForm) {
                document.getElementById("address[" + addid + "][" + component + "]").value = '';
                document.getElementById("address[" + addid + "][" + component + "]").disabled = false;
            }

            // Get each component of the address from the place details
            // and fill the corresponding field on the form.
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    if (addressType == "street_number") {
                        val = place.address_components[i][componentForm['street_number']] + " " + place.address_components[1]['long_name'];
                    }
                    document.getElementById("address[" + addid + "][" + addressType + "]").value = val;
                }
            }
            $(".customaddress-" + addid).show();
            $(".customadd-" + addid).hide();
            $(".removeadd-" + addid).show();
        });

    }

    // function fillInAddress() {
    //   // Get the place details from the autocomplete object.
    //   var place = autocomplete.getPlace();

    //   for (var component in componentForm) {
    //     document.getElementById("address[0]["+component+"]").value = '';
    //     document.getElementById("address[0]["+component+"]").disabled = false;
    //   }

    //   // Get each component of the address from the place details
    //   // and fill the corresponding field on the form.
    //   for (var i = 0; i < place.address_components.length; i++) {
    //     var addressType = place.address_components[i].types[0];
    //     if (componentForm[addressType]) {
    //       var val = place.address_components[i][componentForm[addressType]];
    //       document.getElementById("address[0]["+addressType+"]").value = val;
    //     }
    //   }
    //   $(".customaddress-0").show();
    //   $(".custom_address").hide();
    //   $(".remove_address").show();
    // }

    // Bias the autocomplete object to the user's geographical location,
    // as supplied by the browser's 'navigator.geolocation' object.
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

    // End code of Add more / Remove address
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB-0SSogvGqWSro2pyjAlek2DP_lwfQMvE&libraries=places"></script>

</body>
</html>