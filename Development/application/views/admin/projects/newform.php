<?php $nextIndex = $index + 1; ?>
<div id="contact_<?php echo $index; ?>" class="contact contact_<?php echo $index; ?>">
    <div id="contactheader_<?php echo $index; ?>" class="contactheader" data-index="<?php echo $index; ?>">
        <i class="fa fa-caret-right"></i><span class="mleft4"></span>
    </div>
    <div id="contactinner">
        <div class="form-group contact-options">
            <div class="radio radio-primary radio-inline">
                <input id="contact_new_<?php echo $index; ?>" name="projectcontact[<?php echo $index; ?>]" value="new"
                       checked="true" type="radio" data-index="<?php echo $index; ?>">
                <label for="<?php echo _l('new_contact'); ?>"><?php echo _l('new_contact'); ?></label>
            </div>
            <div class="radio radio-primary radio-inline">
                <input id="contact_existing_<?php echo $index; ?>" name="projectcontact[<?php echo $index; ?>]"
                       value="existing"
                       type="radio" data-index="<?php echo $index; ?>">
                <label for="<?php echo _l('choose_existing_client'); ?>"><?php echo _l('choose_existing_client'); ?></label>
            </div>
        </div>
        <div id="new-address-book-<?php echo $index; ?>" class="new-address-book">
            <input type="hidden" name="contact[<?php echo $index; ?>][contacttype]" value="new">
            <div class="panel-body">
                <h6 class="sub-sub-title">
                    New Contact
                </h6>
                <?php if (isset($addressbook)) { ?>
                    <?php echo form_hidden('addressbookid', $addressbook->addressbookid); ?>
                    <?php if (has_permission('addressbooks', '', 'create')) { ?>
                        <a href="<?php echo admin_url('addressbooks/addressbook'); ?>"
                           class="btn btn-info pull-right mbot20 display-block"
                           style="margin-bottom:0px">New Contact</a>
                    <?php } ?>
                <?php } ?>
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
                            <img width="100" src=""/>
                            <!-- <a class="clicktoaddimage" href="javascript:void(0)" onclick="croppedDelete('profile',<?php //echo $index; ?>);">
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
                <div class="row">
                    <div class="col-sm-12">

                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary mtop0" title="Company">
                                        <input value="1" type="checkbox" name="contact[<?php echo $index; ?>][company]"
                                               id="contact_<?php echo $index; ?>_company" class="company"
                                               data-index="<?php echo $index; ?>">
                                        <label for="contact_<?php echo $index; ?>_company">Company</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <div class="checkbox checkbox-primary <?php echo $index == 0 ? "first" : ""; ?>"
                                    >
                                        <input value="1" type="checkbox"
                                               name="contact[<?php echo $index; ?>][isclient]"
                                               id="contact_<?php echo $index; ?>_isclient" <?php echo $index == 0 ? "checked" : ""; ?> >
                                        <label for="contact_<?php echo $index; ?>_isclient"><?php echo "Client ?" ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div id="companydetails_<?php echo $index; ?>" class="row companydetails">
                    <div class="col-sm-6">
                        <?php echo render_input('contact[' . $index . '][companyname]', 'Company Name', '', 'text', array(), array(), '', 'required'); ?>
                    </div>
                    <div class="col-sm-6">
                        <?php echo render_input('contact[' . $index . '][companytitle]', 'Title', '', 'text', array(), array(), '', 'required'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?php $attrs = array('autofocus' => true, 'data-index' => $index); ?>
                        <?php echo render_input('contact[' . $index . '][firstname]', 'First Name', '', 'text', $attrs, array(), "", 'required contact_firstname contact_' . $index . '_firstname'); ?>
                    </div>
                    <div class="col-sm-6">
                        <?php $attrs = array('data-index' => $index); ?>
                        <?php echo render_input('contact[' . $index . '][lastname]', 'Last Name', '', 'text', $attrs, array(), '', 'required contact_lastname contact_' . $index . '_lastname'); ?>
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
                            <label for="mode_of_communication" class="control-label">
                                Preferred Mode of Communication
                            </label>
                            <select name="contact[<?php echo $index; ?>][mode_of_communication][]"
                                    id="mode_of_communication[]"
                                    class="form-control selectpicker"
                                    data-none-selected-text="Select"
                                    data-width="100%" data-live-search="true" multiple>
                                <option value="email">Email</option>
                                <option value="text">Text</option>
                                <option value="phone">Phone</option>
                            </select>
                        </div>
                    </div>
                    <?php if ($global_search_allow == 1) { ?>
                        <div class="col-sm-6">
                            <label class="control-label hidden-xs">&nbsp;</label>

                            <div class="form-group">
                                <div class="checkbox checkbox-primary"
                                     title="Allow Global Search?">
                                    <input value="1" type="checkbox"
                                           name="contact[<?php echo $index; ?>][ispublic]"
                                           id="contact_<?php echo $index; ?>_ispublic">
                                    <label for="contact_<?php echo $index; ?>_ispublic"><?php echo _l('shared') ?></label>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <input type="hidden" name="contact[<?php echo $index; ?>][ispublic]" value="0">
                    <?php } ?>

                </div>
                <h5>Email</h5>
                <div id="contactemails-<?php echo $index; ?>" class="contactemails">
                    <div class="row contactemail" id="email-0">
                        <div class="col-sm-2">
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
                        <div class="col-sm-6 multiemail">
                            <div class="form-group">
                                <?php echo render_input('contact[' . $index . '][email][0][email]', '<small class="req text-danger">* </small>Email', '', 'email', array('autocomplete' => 'off'), array(), '', 'required'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button id="email-add-more" name="email-add-more" class="email-add-more btn btn-primary"
                            data-index=<?php echo $index; ?>>
                        <i class="fa fa-plus"></i><span class="mleft5">Add email</span>
                    </button>
                </div>
                <h5>Phone</h5>
                <div id="contactphones-<?php echo $index; ?>" class="contactphones">
                    <div class="row contactphone" id="phone-0">
                        <div class="col-sm-2 col-xs-12">
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
                        <div class="col-md-5 col-sm-4 col-xs-12 multiphone">
                            <div class="form-group">
                                <?php echo render_input('contact[' . $index . '][phone][0][phone]', 'client_phonenumber', ''); ?>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-12 multiext">
                            <?php $phone = ''; ?>
                            <?php echo render_input('contact[' . $index . '][phone][0][ext]', 'Ext', $phone, 'tel', array('autocomplete' => 'off', 'maxlength' => 5)); ?>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button id="phone-add-more" name="phone-add-more" class="phone-add-more btn btn-primary"
                            data-index=<?php echo $index; ?>>
                        <i class="fa fa-plus"></i><span class="mleft5">Add Phone</span>
                    </button>
                </div>
                <h5>Social</h5>
                <div id="contactwebsites-<?php echo $index; ?>" class="contactwebsites">
                    <div class="row contactwebsite" id="website-0">
                        <div class="col-sm-2">
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
                        <div class="col-sm-6">
                            <div class="form-group">
                                <?php echo render_input('contact[' . $index . '][website][0][url]', 'Address', ''); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <button id="website-add-more" name="website-add-more"
                            class="website-add-more btn btn-primary"
                            data-index=<?php echo $index; ?>>
                        <i class="fa fa-plus"></i><span class="mleft5">Add Link</span>
                    </button>
                </div>
                <h5>Address</h5>
                <div class="contactaddresses" id="contactaddresses-<?php echo $index ?>">
                    <div class="contactaddress" id="address-0">
                        <div class="row">
                            <div class="col-sm-2">
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
                            <div class="col-sm-6 col-xs-12">
                                <div class="col-sm-8 col-md-9 col-lg-9">

                                    <div id="locationField" class="form-group">
                                        <label class="control-label" for="address">Address</label>
                                        <input id="contact_<?php echo $index; ?>_autocomplete0"
                                            class="form-control searchmap"
                                            data-addmap="0"
                                            data-index="<?php echo $index; ?>"
                                            placeholder="Search Google Maps..." onFocus="geolocate()"
                                            type="text">
                                    </div>

                                </div>
                                <div class="col-sm-4 col-md-3 col-lg-3">
                                    <div class="form-group">
                                        <button type="button"
                                                class="btn btn-info custom_address customadd-0"
                                                data-addressid="0">Custom
                                        </button>
                                        <button type="button"
                                                class="btn btn-default remove_address removeadd-0"
                                                style="display:none" data-addressid="0">Remove &
                                            Search Again
                                        </button>
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
                                <div class="col-sm-3">
                                    <?php echo render_input('contact[' . $index . '][address][0][street_number]', 'Address1', ''); ?>
                                </div>
                                <div class="col-sm-5">
                                    <?php echo render_input('contact[' . $index . '][address][0][route]', 'Address2', ''); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <?php echo render_input('contact[' . $index . '][address][0][locality]', 'client_city', ''); ?>
                                </div>
                                <div class="col-sm-5">
                                    <?php echo render_input('contact[' . $index . '][address][0][administrative_area_level_1]', 'client_state', ''); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <?php echo render_input('contact[' . $index . '][address][0][postal_code]', 'client_postal_code', ""); ?>
                                    </div>
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
                <div class="text-right">
                    <button id="address-add-more"
                            class=" address-add-more btn btn-primary"
                            data-index=<?php echo $index; ?>>
                        <i class="fa fa-plus"></i><span class="mleft5">Address</span>
                    </button>
                </div>
            </div>
        </div>
        <div id="existing-client-book-<?php echo $index; ?>" class="existing-client-book">
            <div class="panel-body">
                <input type="hidden" name="contact[<?php echo $index; ?>][contacttype]" value="existing" disabled>
                <?php
                /*                if (isset($clients)) {
                                    echo render_select('contact[' . $index . '][id]', $clients, array('addressbookid', 'name', 'email'), 'lead_add_edit_client', '', array('data-index'=>$index), array(), '', 'required clientselect clientselect_'.$index);
                                }
                                */ ?>
                <?php
                if (isset($clients)) {
                    $sclients = array();
                    if (isset($selectedclients) && !empty($selectedclients)) {
                        $sclients = $selectedclients;
                    }
                    //echo render_select('contact[' . $index . '][id]', $clients, array('addressbookid', 'name', 'email'), 'lead_add_edit_client', '', array('data-index'=>$index), array(), '', 'required clientselect clientselect_'.$index); ?>
                    <label for="contact[<?php echo $index ?>][id]"><?php echo _l('lead_add_edit_client') ?></label>
                    <select name='contact[<?php echo $index ?>][id]'
                            class='selectpicker required clientselect clientselect_<?php echo $index ?>'
                            data-index="<?php echo $index ?>" data-width="100%" data-none-selected-text="Select"
                            data-live-search="true" aria-required="true" tabindex="-98">
                        <option value=""></option>
                        <?php foreach ($clients as $client) { ?>
                            <option value="<?php echo $client['addressbookid'] ?>"
                                    data-subtext="<?php echo $client['email'] ?>" <?php echo in_array($client['addressbookid'], $sclients) ? "disabled" : "" ?>>
                                <?php echo $client['name'] ?>
                            </option>
                        <?php } ?>
                    </select>
                <?php } ?>

                <div class="form-group">
                    <div class="checkbox clientcheckbox checkbox-primary <?php echo $index == 0 ? "first" : ""; ?>"
                    >
                        <input value="1" type="checkbox"
                               name="contact[<?php echo $index; ?>][isclient]"
                               id="clients_<?php echo $index; ?>_isclient" <?php echo $index == 0 ? "checked" : ""; ?>
                               disabled>
                        <label for="clients_<?php echo $index; ?>_isclient"><?php echo "Client ?" ?></label>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($index > 0) { ?>
            <a href="javascript:void(0)" class="btn btn-danger pull-right mtop15 removeContact"
               data-index=<?php echo $index; ?>>Remove Contact</a>
        <?php } ?>
    </div>
</div>