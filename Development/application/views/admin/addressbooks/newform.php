<?php $nextIndex = $index + 1; ?>
<div id="contact_<?php echo $index; ?>" class="contact contact_<?php echo $index; ?>">
    <div id="contactheader_<?php echo $index; ?>" class="contactheader" data-index="<?php echo $index; ?>">
        <i class="fa fa-caret-right"></i><span></span>
    </div>
    <div id="contactinner">
        <h5 class="pull-left"><strong>Profile</strong></h5>
        <div class="clearfix"></div>
        <div class="panel_s btmbrd">
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-3">
                        <?php if ($profile_allow == 1) { ?>
                            <div class="mprofile-pic profile-pic<?php echo $index; ?>">

                                <!--<div class="form-group uploadProfilepic">
                            <label for="profile_image"
                                   class="profile-image"><?php /*echo _l('staff_edit_profile_image'); */ ?></label>
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="<?php /*echo _l('profile_dimension'); */ ?>"></i>
                            <div class="input-group">
													<span class="input-group-btn">
												  	<span class="btn btn-primary"
                                                          onclick="$(this).parent().find('input[type=file]').click();">Browse</span>
												  		<input name="contact[<?php /*echo $index; */ ?>][profile_image]"
                                                               onchange="$(this).parent().parent().find('.form-control').html($(this).val().split(/[\\|/]/).pop());"
                                                               style="display: none;" type="file">
													</span>
                                <span class="form-control"></span>
                            </div>
                        </div>-->
                                <div class="profile_imageview<?php echo $index; ?> hidden">
                                    <img src=""/>
                                    <!-- <a class="clicktoaddimage" href="javascript:void(0)" onclick="croppedDelete('profile',<?php //echo $index; ?>);" >
                                <span>
                                    <i class="fa fa-trash"></i>
                                </span>
                            </a>
                            <a class="btn btn-info mtop10" href="javascript:void(0)"
                               onclick="reCropp('profile',<?php //echo $index; ?>);">
                                <?php //echo _l('recrop')?></a> -->

                                    <div class="actionToEdit">
                                        <a class="clicktoaddimage" href="javascript:void(0)" onclick="croppedDelete('profile',<?php echo $index; ?>);">
                                            <span><i class="fa fa-trash"></i></span>
                                        </a>
                                        <a class="recropIcon_blk" href="javascript:void(0)" onclick="reCropp('profile',<?php echo $index; ?>);">
                                            <span><i class="fa fa-crop" aria-hidden="true"></i></span>
                                        </a>
                                    </div>
                                </div>
                                <div id="clicktoaddimage<?php echo $index; ?>" class="clicktoaddimage">
                                    <div class="drag_drop_image">
                                        <span class="icon"><i class="fa fa-image"></i></span>
                                        <span><?php echo _l('dd_upload'); ?></span>
                                    </div>
                                    <input id="profile_image<?php echo $index; ?>"
                                           name="contact[<?php echo $index; ?>][profile_image]"
                                           onchange="readFile(this,'profile',<?php echo $index; ?>);"
                                           type="file">
                                    <input type="hidden" id="imagebase64<?php echo $index; ?>"
                                           name="contact[<?php echo $index; ?>][imagebase64]">
                                </div>
                                <div class="cropper" id="profile_croppie<?php echo $index; ?>">
                                    <div class="copper_container">
                                        <div id="profile-cropper<?php echo $index; ?>"></div>
                                        <div class="cropper-footer">
                                            <button type="button" class="btn btn-info p9 actionDone" type="button" onclick="croppedResullt('profile',<?php echo $index; ?>);">
                                                <?php echo _l('save'); ?>
                                            </button>
                                            <button type="button" class="btn btn-default actionCancel"
                                                    data-dismiss="modal"
                                                    onclick="croppedCancel('profile',<?php echo $index; ?>);">
                                                <?php echo _l('cancel'); ?>
                                            </button>
                                            <button type="button" class="btn btn-default actionChange"
                                                    onclick="croppedChange('profile',<?php echo $index; ?>);">
                                                <?php echo _l('change'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <input type="hidden" name="profile_image" value="">
                        <?php } ?>
                    </div>
                    <div class="col-sm-9">
                        <?php if (isset($lid) || isset($eid) || isset($pid)) { ?>
                            <?php
                            $rel_type = '';
                            $rel_id = '';
                            if (isset($lid)) {
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
                                        <select name="contact[<?php echo $index; ?>][rel_type]" class="selectpicker"
                                                id="rel_type"
                                                data-width="100%"
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <option value=""></option>
                                            <?php if (isset($lid) || (!isset($eid) && !isset($pid))) { ?>
                                                <option value="lead" <?php if (isset($lid) || $this->input->get('rel_type')) {
                                                    if ($rel_type == 'lead') {
                                                        echo 'selected';
                                                    }
                                                } ?>>
                                                    <?php echo _l('lead'); ?>
                                                </option>
                                            <?php } ?>
                                            <?php if (isset($pid) || (!isset($eid) && !isset($lid))) { ?>
                                                <option value="project" <?php if (isset($pid) || $this->input->get('rel_type')) {
                                                    if ($rel_type == 'project') {
                                                        echo 'selected';
                                                    }
                                                } ?>>
                                                    <?php echo _l('project'); ?>
                                                </option>
                                            <?php } ?>
                                            <?php if ((isset($pid) || isset($eid)) || !isset($lid)) { ?>
                                                <option value="event" <?php if (isset($eid) || $this->input->get('rel_type')) {
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
                                        echo render_select('contact[' . $index . '][lead]', $leads, array('id', 'name'), 'Leads', $selectedleads, array(), array(), '', '', false);
                                        ?>
                                    </div>
                                <?php } ?>
                                <?php if (isset($pid) || (!isset($eid) && !isset($lid))) { ?>
                                    <div class="col-sm-6 project-search <?php echo $rel_type == "project" ? "" : "hide"; ?>">
                                        <?php $selectedprojects = array();
                                        $selectedprojects = $rel_id != "" ? $rel_id : "";
                                        echo render_select('contact[' . $index . '][project]', $projects, array('id', 'name'), 'Projects', $selectedprojects, array(), array(), '', '', false);
                                        ?>
                                    </div>
                                <?php } ?>
                                <?php if ((isset($pid) || isset($eid)) || !isset($lid)) { ?>
                                    <div class="col-sm-6 event-search <?php echo $rel_type == "event" ? "" : "hide"; ?>">
                                        <?php $selectedevents = array();
                                        $selectedevents = $rel_id != "" ? $rel_id : "";
                                        echo render_select('contact[' . $index . '][event]', $events, array('id', 'name'), 'Sub-Projects', $selectedevents, array(), array(), '', '', false);
                                        ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary mtop0" title="Company">
                                        <input value="1" type="checkbox" name="contact[<?php echo $index; ?>][company]"
                                               id="contact_<?php echo $index; ?>_company" class="company" data-index="<?php echo $index; ?>">
                                        <label for="contact_<?php echo $index; ?>_company">Company</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="companydetails_<?php echo $index; ?>" class="row companydetails">
                            <div class="col-sm-6">
                                <?php echo render_input('contact[' . $index . '][companyname]', 'Company Name', '', 'text',array(),array(),'','required'); ?>
                            </div>
                            <div class="col-sm-6">
                                <?php echo render_input('contact[' . $index . '][companytitle]', 'Title', '', 'text',array(),array(),'','required'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <?php $attrs = array('autofocus' => true,'data-index'=>$index); ?>
                                <?php echo render_input('contact[' . $index . '][firstname]', 'First Name', '', 'text', $attrs,array(),"",'required contact_firstname contact_' . $index . '_firstname'); ?>
                            </div>
                            <div class="col-sm-6">
                                <?php echo render_input('contact[' . $index . '][lastname]', 'Last Name', '', 'text',array(),array(),'','required'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="gender" class="control-label">Gender</label>
                                    <select id="gender" class="selectpicker"
                                            name="contact[<?php echo $index; ?>][gender]"
                                            data-width="100%"
                                            data-none-selected-text="Select" data-live-search="false">
                                        <option value=""></option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="others">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="tags" class="control-label">Tags
                                        <!--<small class="req text-danger">*</small>-->
                                    </label>
                                    <select name="contact[<?php echo $index; ?>][tags][]" id="tags"
                                            class="form-control selectpicker"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                            data-live-search="true" multiple>
                                        <?php
                                        foreach ($tags as $tag) {
                                            echo '<option value="' . $tag['id'] . '">' . $tag['name'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="mode_of_communication" class="control-label">Preferred Mode
                                        of
                                        Communication</label>
                                    <select name="contact[<?php echo $index; ?>][mode_of_communication][]"
                                            id="mode_of_communication[]"
                                            class="form-control selectpicker"
                                            data-none-selected-text="Select"
                                            data-width="100%" data-live-search="true" multiple>
                                        <option value="email">
                                            Email
                                        </option>
                                        <option value="text">
                                            Text
                                        </option>
                                        <option value="phone">
                                            Phone
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="row">
                                    <?php if ($global_search_allow == 1) { ?>
                                        <div class="col-lg-7 col-md-6 col-sm-12">
                                            <label class="control-label hidden-sm">&nbsp;</label>
                                            <div class="form-group">
                                                <div class="checkbox checkbox-primary"
                                                     title="Allow Global Search?">
                                                    <input value="1" type="checkbox" class="ispublic"
                                                           name="contact[<?php echo $index; ?>][ispublic]"
                                                           id="contact_<?php echo $index; ?>_ispublic">
                                                    <label for="contact_<?php echo $index; ?>_ispublic"><?php echo _l('shared') ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <input type="hidden" name="contact[<?php echo $index; ?>][ispublic]" value="0">
                                    <?php } ?>
                                    <?php
                                    $icon = "fa-star-o";
                                    $selected = "";
                                    ?>
                                    <div class="col-lg-5 col-md-6 col-sm-12">
                                        <label class="control-label hidden-sm">&nbsp;</label>
                                        <div class="form-group">
                                            <input type="checkbox" name="contact[<?php echo $index; ?>][favourite]"
                                                   class="hidden favourite"
                                                   id="contact_<?php echo $index; ?>_favourite" <?php echo $selected; ?>/>
                                            <label for="contact_<?php echo $index; ?>_favourite" class="favourite_label">
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
                        <div id="contactemails-<?php echo $index; ?>" class="contactemails">
                            <div class="row contactemail" id="email-0">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="contact[<?php echo $index; ?>][email][0][type]"
                                               class="control-label">Type</label>
                                        <select name="contact[<?php echo $index; ?>][email][0][type]"
                                                id="contact[<?php echo $index; ?>][email][0][type]"
                                                class="form-control selectpicker"
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <?php
                                            echo '<option value="primary">Primary</option>';
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-8 multiemail">
                                    <?php echo render_input('contact[' . $index . '][email][0][email]', '<small class="req text-danger">* </small>Email', '', 'email', array('autocomplete' => 'off',"data-index"=>$index)); ?>
                                </div>
                            </div>
                        </div>
                        <div class="text-left">
                            <button id="email-add-more" name="email-add-more" class="email-add-more btn btn-primary"
                                    data-index=<?php echo $index; ?>>
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
                        <div id="contactwebsites-<?php echo $index; ?>" class="contactwebsites">
                            <div class="row contactwebsite" id="website-0">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="contact[<?php echo $index; ?>][website][0][type]"
                                               class="control-label">Type</label>
                                        <select name="contact[<?php echo $index; ?>][website][0][type]"
                                                id="contact[<?php echo $index; ?>][website][0][type]"
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
                                    <?php echo render_input('contact[' . $index . '][website][0][url]', 'Address', ''); ?>
                                </div>
                            </div>
                        </div>
                        <div class="text-left">
                            <button id="website-add-more" name="website-add-more"
                                    class="website-add-more btn btn-primary"
                                    data-index=<?php echo $index; ?>>
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
                        <div id="contactphones-<?php echo $index; ?>" class="contactphones">
                            <div class="row contactphone" id="phone-0">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="contact[<?php echo $index; ?>][phone][0][type]"
                                               class="control-label">Type</label>
                                        <select name="contact[<?php echo $index; ?>][phone][0][type]"
                                                id="contact[<?php echo $index; ?>][phone][0][type]"
                                                class="form-control selectpicker"
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                            <?php
                                            echo '<option value="primary" selected="selected">Primary</option>';
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-xs-7 multiphone">
                                    <?php echo render_input('contact[' . $index . '][phone][0][phone]', 'client_phonenumber', ''); ?>
                                </div>
                                <div class="col-sm-2 col-xs-4 multiext">
                                    <?php $phone = ''; ?>
                                    <?php echo render_input('contact[' . $index . '][phone][0][ext]', 'Ext', $phone, 'tel', array('autocomplete' => 'off', 'maxlength' => 5)); ?>
                                </div>
                            </div>
                        </div>
                        <div class="text-left">
                            <button id="phone-add-more" name="phone-add-more" class="phone-add-more btn btn-primary"
                                    data-index=<?php echo $index; ?>>
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
                        <div class="contactaddresses" id="contactaddresses-<?php echo $index ?>">
                            <div class="contactaddress" id="address-0">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="contact[<?php echo $index; ?>][address][0][type]"
                                                   class="control-label">Type</label>
                                            <select name="contact[<?php echo $index; ?>][address][0][type]"
                                                    id="address[0][type]"
                                                    class="form-control selectpicker"
                                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <?php
                                                echo '<option value="primary" selected="selected">Primary</option>';
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-8 col-xs-11">
                                        <div id="locationField" class="form-group">
                                            <label class="control-label" for="address">Address</label>
                                            <input id="contact_<?php echo $index;?>_autocomplete0" class="form-control searchmap"
                                                   data-addmap="0"
                                                   data-index="<?php echo $index;?>"
                                                   placeholder="Search Google Maps..." onFocus="geolocate()"
                                                   type="text">
                                        </div>
                                        <div class="customadd-btn">
                                            <div class="form-group">
                                                <button type="button"
                                                        class="btn btn-info custom_address customadd-0"
                                                        style="display:block" data-addressid="0" data-index=<?php echo $index; ?>>Custom
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
                                <?php
                                $style = 'style="display:none"';
                                ?>
                                <div id="customaddress-0"
                                     class="addressdetails customaddress-0" <?php echo $style; ?> >
                                    <div class="row">
                                        <div class="col-sm-11 col-xs-11">
                                            <?php echo render_input('contact[' . $index . '][address][0][street_number]', 'Address1', ''); ?>
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
                                                <?php echo render_input('contact[' . $index . '][address][0][route]', 'Address2', ''); ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <?php echo render_input('contact[' . $index . '][address][0][locality]', 'client_city', ''); ?>
                                            </div>
                                            <div class="col-sm-5">
                                                <?php echo render_input('contact[' . $index . '][address][0][administrative_area_level_1]', 'client_state', ''); ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <?php echo render_input('contact[' . $index . '][address][0][postal_code]', 'client_postal_code', ""); ?>
                                            </div>
                                            <div class="col-sm-5">
                                                <div class="form-group">
                                                    <label for="address[0][country]"
                                                           class="control-label">Country</label>
                                                    <select name="contact[<?php echo $index; ?>][address][0][country]"
                                                            id="contact[<?php echo $index; ?>][address][0][country]"
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
                        </div>
                        <div class="text-left col-sm-12">
                            <button id="address-add-more" name="address-add-more"
                                    class=" address-add-more btn btn-primary"
                                    data-index=<?php echo $index; ?>>
                                <i class="fa fa-plus"></i><span class="mleft5">Address</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

