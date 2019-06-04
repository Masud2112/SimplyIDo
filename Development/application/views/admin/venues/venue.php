<?php init_head(); ?>
<div id="wrapper">
    <div class="content venue-page">
        <div class="row">
            <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'venue-form', 'autocomplete' => 'off')); ?>
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
                        <a href="<?php echo admin_url('venues') . '?lid=' . $lid; ?>">Venues</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } elseif (isset($pid)) { ?>
                        <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('leads/dashboard/' . $pid); ?>"><?php echo($lname); ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('venues') . '?pid=' . $pid; ?>">Venues</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } else { ?>
                        <a href="<?php echo admin_url('venues'); ?>">Venues</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } ?>
                    <?php if (isset($venue->venueid)) { ?>
                        <a href="<?php echo admin_url('venues/view/') . $venue->venueid; ?>"><?php echo $venue->venuename; ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } else { ?>
                        <span><?php echo isset($venue) ? $venue->venuename : "New Venue" ?></span>
                    <?php } ?>

                    <?php if (isset($venue->venueid)) {
                        echo ' <span>Edit</span>';
                    } ?>

                </div>
                <h1 class="pageTitleH1"><i class="fa fa-address-book-o"></i><?php echo $title; ?></h1>
                <?php if (isset($venue)) { ?>
                    <?php echo form_hidden('venueid', $venue->venueid); ?>
                <?php } ?>
                <div class="clearfix"></div>
                <h4 class="sub-title">
                    <strong><?php echo _l('cover_photo'); ?></strong>
                </h4>
                <?php if ((isset($venue) && $venue->venuecoverimage != NULL)) { ?>
                    <a class="editicon"
                       href="<?php echo admin_url('venues/remove_venue_cover_image/' . $venue->venueid); ?>"><i
                                class="fa fa-remove"></i><span class="mleft5">Remove Photo</span></a>
                <?php } ?>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="banner-pic">
                        <?php
                        $src = "";
                        /*if ((isset($venue) && $venue->venuecoverimage != NULL)) {
                            $src = base_url() . 'uploads/venue_cover_images/' . $venue->venueid . '/croppie_' . $venue->venuecoverimage;
                        }*/

                        if ((isset($venue) && $venue->venuecoverimage != NULL)) {
                            $venueid = $venue->venueid;
                            $path = get_upload_path_by_type('venue_coverimage') . $venueid . '/' . $venue->venuecoverimage;
                            if (file_exists($path)) {
                                $path = get_upload_path_by_type('venue_coverimage') . $venueid . '/croppie_' . $venue->venuecoverimage;
                                $src = base_url() . 'uploads/venue_cover_images/' . $venueid . '/' . $venue->venuecoverimage;
                                if (file_exists($path)) {
                                    $src = base_url() . 'uploads/venue_cover_images/' . $venueid . '/croppie_' . $venue->venuecoverimage;
                                }
                            }
                        }
                        ?>
                        <div class="banner_imageview <?php echo empty($src) ? 'hidden' : ''; ?>">
                            <img src="<?php echo $src; ?>"/>
                            <?php if ($src == "") { ?>
                                <!-- <a class="clicktoaddimage" href="javascript:void(0)"
                                   onclick="croppedDelete('banner');">
                                    <span><i class="fa fa-trash"></i></span></a>
                                <a class="btn btn-info mtop10" href="javascript:void(0)"
                                   onclick="reCropp('banner');">
                                    <?php //echo _l('recrop')?></a> -->

                                <div class="actionToEdit">
                                    <a class="clicktoaddimage" href="javascript:void(0)"
                                       onclick="croppedDelete('banner');">
                                        <span><i class="fa fa-trash"></i></span>
                                    </a>
                                    <a class="recropIcon_blk" href="javascript:void(0)" onclick="reCropp('banner');">
                                        <span><i class="fa fa-crop" aria-hidden="true"></i></span>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="clicktoaddimage <?php echo !empty($src) ? 'hidden' : ''; ?>">
                            <div class="drag_drop_image">
                                <span class="icon"><i class="fa fa-image"></i></span>
                                <span><?php echo _l('dd_upload'); ?></span>
                            </div>
                            <input type="file" class="" name="venuecoverimage" onchange="readFile(this,'banner');"/>
                            <input type="hidden" id="bannerbase64" name="bannerbase64">
                        </div>
                        <div class="cropper" id="banner_croppie">
                            <div class="copper_container">
                                <div id="banner-cropper"></div>
                                <div class="cropper-footer">
                                    <button type="button" class="btn btn-info p9 actionDone" type="button" id=""
                                            onclick="croppedResullt('banner');">
                                        <?php echo _l('save'); ?>
                                    </button>
                                    <button type="button" class="btn btn-default actionCancel" data-dismiss="modal"
                                            onclick="croppedCancel('banner');">
                                        <?php echo _l('cancel'); ?>
                                    </button>
                                    <button type="button" class="btn btn-default actionChange"
                                            onclick="croppedChange('banner');">
                                        <?php echo _l('change'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <h4 class="sub-title">
                            <strong><?php echo _l('Profile'); ?></strong>
                        </h4>
                        <?php if (isset($venue)) { ?>
                            <a class="viewicon "
                               href="<?php echo admin_url('venues/view/' . $venue->venueid) ?>"><strong><i
                                            class="fa fa-eye"></i><span class="mleft5">View Venue</span></strong></a>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <div class="panel_s btmbrd">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <!--<div class="profile-pic">
                                            <?php /*if ((isset($venue) && $venue->venuelogo == NULL) || !isset($venue)) { */ ?>
                                                <div class="form-group uploadProfilepic">
                                                    <label for="venuelogo"
                                                           class="venue-logoimage"><?php /*echo _l('venue_logo_image'); */ ?></label>
                                                    <div class="input-group">
														<span class="input-group-btn">
														<span class="btn btn-primary"
                                                              onclick="$(this).parent().find('input[type=file]').click();">Browse</span>
															<input name="venuelogo"
                                                                   onchange="$(this).parent().parent().find('.form-control').html($(this).val().split(/[\\|/]/).pop());"
                                                                   style="display: none;" type="file">
														</span>
                                                        <span class="form-control"></span>
                                                    </div>
                                                </div>
                                            <?php /*} */ ?>
                                            <?php /*if (isset($venue) && $venue->venuelogo != NULL) { */ ?>
                                                <div class="profileImg_blk">
                                                    <?php /*echo venue_logo_image($venue->venueid, array('venuelogo', 'img-responsive', 'venue-logo-image-thumb'), 'thumb'); */ ?>
                                                </div>
                                                <div class="text-center">
                                                    <a href="<?php /*echo admin_url('venues/remove_venue_logo_image/' . $venue->venueid); */ ?>"><i
                                                                class="fa fa-remove"></i><span
                                                                class="mleft5">Remove</span></a>
                                                </div>

                                            <?php /*} */ ?>
                                        </div>-->
                                        <div class="profile-pic">
                                            <?php
                                            $src = "";
                                            if ((isset($venue) && $venue->venuelogo != NULL)) {
                                                $OrigImagePath = 'uploads/venue_logo_images/' . $venue->venueid . '/' . $venue->venuelogo;
                                                $profileImagePath = FCPATH . 'uploads/venue_logo_images/' . $venue->venueid . '/round_' . $venue->venuelogo;
                                                if (file_exists($profileImagePath)) {
                                                    $src = base_url() . 'uploads/venue_logo_images/' . $venue->venueid . '/round_' . $venue->venuelogo;
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
                                                            <span><i class="fa fa-crop" aria-hidden="true"></i></span>
                                                        </a>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="actionToEdit">
                                                        <a class="_delete clicktoaddimage"
                                                           href="<?php echo admin_url('venues/remove_venue_logo_image/' . $venue->venueid); ?>">
                                                            <span><i class="fa fa-trash"></i></span>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                                <!--<a class="actionRecrop" href="javascript:void(0)" data-src="<?php /*echo $OrigImagePath; */ ?>">
                                                    <span><i class="fa fa-crop"></i></span>
                                                </a>-->
                                            </div>
                                            <div class="clicktoaddimage <?php echo !empty($src) ? 'hidden' : ''; ?>">
                                                <div class="drag_drop_image">
                                                    <span class="icon"><i class="fa fa-image"></i></span>
                                                    <span><?php echo _l('dd_upload'); ?></span>
                                                </div>
                                                <input id="profile_image" type="file" class="" name="venuelogo"
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
                                            <div class="col-sm-12">
                                                <?php echo render_input('venuename', 'venue_add_edit_venuename', (isset($venue) ? $venue->venuename : ''), ''); ?>
                                            </div>
                                            <div class="col-sm-12">
                                                <?php echo render_input('venueslogan', 'venue_add_edit_slogan', (isset($venue) ? $venue->venueslogan : ''), ''); ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <?php
                                                $venuetags = (isset($venue->venuetype) ? $venue->venuetype : '');
                                                $venuetags = explode(',', $venuetags);
                                                $venueTypeLists = array('Aquarium', 'Ballroom', 'Banquet Hall', 'Barn/Farm/Ranch', 'Beach/Lake/Waterfront', 'Bed & Breakfast/Inn', 'Boat/Cruise Ship/Yacht', 'Boutique Hotel
', 'Brewery/Distillery', 'Cafe / Coffee House', 'Camping / Glamping', 'Castle / Chateau / Manor', 'Church / Chapel / Temple', 'City Hall', 'College Campus / School', 'Community Center', 'Event Center', 'Field / Forest / Orchard', 'Garden
', 'Golf Course / Country Club', 'Historic / Landmark Venue', 'Hotel / Resort', 'Island', 'Library', 'Loft', 'Mansion / Villa', 'Marina', 'Mountain / Lodge', 'Museum / Gallery', 'Park', 'Plantation', 'Private Club', 'Private Estate / Residence', 'Restaurant', 'Rooftop / Penthouse', 'Theater', 'Train', 'Warehouse', 'Winery / Vinyard', 'Yacht Club', 'Zoo');
                                                ?>
                                                <div class="form-group">
                                                    <label for="venuecountry"
                                                           class="control-label"><?php echo _l('venue_add_edit_type'); ?></label>
                                                    <select name="venuetype" id="venuetypes"
                                                            class="form-control selectpicker"
                                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                        <?php foreach ($venueTypeLists as $venueTypeList) { ?>
                                                            <option value="<?php echo $venueTypeList; ?>" <?php echo in_array($venueTypeList, $venuetags) ? "selected" : "" ?> >
                                                                <?php echo $venueTypeList; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <?php
                                                $venuetags = (isset($venue->venuetags) ? $venue->venuetags : '');
                                                $venuetags = explode(',', $venuetags);
                                                $venuetagsLists = $tags;
                                                ?>
                                                <div class="form-group">
                                                    <label for="venuecountry"
                                                           class="control-label"><?php echo _l('venue_add_edit_tags'); ?>
                                                        <!--<small class="req text-danger">*</small>-->
                                                    </label>
                                                    <select name="venuetags[]" id="venuetags"
                                                            class="form-control selectpicker"
                                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                                            multiple="multiple">
                                                        <?php foreach ($venuetagsLists as $venuetagsList) { ?>
                                                            <option value="<?php echo $venuetagsList['id']; ?>" <?php echo in_array($venuetagsList['id'], $venuetags) ? "selected" : "" ?> >
                                                                <?php echo $venuetagsList['name']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-lg-8">
                                                <div class="row">
                                                    <?php if ($global_search_allow == 1) { ?>
                                                        <div class="col-sm-6 col-lg-7">
                                                            <label class="control-label hidden-xs">&nbsp;</label>
                                                            <div class="form-group">
                                                                <div class="checkbox checkbox-primary"
                                                                     title="Allow Global Search?">
                                                                    <input value="1" type="checkbox" name="ispublic"
                                                                           id="ispublic" <?php if (isset($venue)) {
                                                                        if ($venue->ispublic == 1) {
                                                                            echo 'checked';
                                                                        }
                                                                    }; ?>>
                                                                    <label for="ispublic"><?php echo _l('shared') ?></label>
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
                                                    <div class="col-sm-6 col-lg-5">
                                                        <label class="control-label hidden-xs">&nbsp;</label>
                                                        <div class="form-group">
                                                            <input type="checkbox" name="favourite"
                                                                   class="hidden favourite"
                                                                   id="favourite" <?php echo $selected; ?>/>
                                                            <label for="favourite" class="favourite_label">
                                                                <i class="fa <?php echo $icon; ?>"></i><span
                                                                        class="mleft5"><?php echo _l('mark_as_favourite') ?></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <!-- --><?php /*echo render_input('venueemail', 'venue_add_edit_venueemail', (isset($venue) ? $venue->venueemail : ''), ''); */ ?>
                                            </div>
                                            <!--<div class="col-sm-6 multiphone">
                                                <?php /*echo render_input('venuephone', 'venue_add_edit_venuephone', (isset($venue) ? $venue->venuephone : ''), ''); */ ?>
                                            </div>-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <h4><strong>Email</strong></h4>
                        <div class="clearfix"></div>
                        <div class="panel_s btmbrd">
                            <div class="panel-body">
                                <?php if (isset($venue)) {
                                    $pe = 0; ?>
                                    <?php if (!empty($venue->venueemail)) {
                                        if (is_serialized($venue->venueemail)) {
                                            $venue->venueemail = unserialize($venue->venueemail);
                                        } else {
                                            $venuephone = array();
                                            $venuephone[0]['type'] = "primary";
                                            $venuephone[0]['phone'] = $venue->venueemail;
                                            $venue->venueemail = $venuephone;
                                        }
                                        ?>
                                        <?php foreach ($venue->venueemail as $pk => $pv) { ?>
                                            <div class="row" id="email-<?php echo $pe; ?>">
                                                <div class="col-sm-3">
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
                                                <div class="col-sm-8  col-xs-10 multiemail">
                                                    <?php $email = (isset($pv['email']) ? $pv['email'] : ''); ?>
                                                    <?php echo render_input('email[' . $pe . '][email]', '<small class="req text-danger">* </small>Email', $email, 'email', array('autocomplete' => 'off')); ?>
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
                                                <label for="email[0][type]" class="control-label">Type</label>
                                                <select name="email[0][type]" id="email[0][type]"
                                                        class="form-control selectpicker"
                                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                    <?php
                                                    echo '<option value="primary">Primary</option>';
                                                    ?>
                                                </select>
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
                                            <label for="email[0][type]" class="control-label">Type</label>
                                            <select name="email[0][type]" id="email[0][type]"
                                                    class="form-control selectpicker"
                                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <?php
                                                echo '<option value="primary">Primary</option>';
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-6 multiemail">
                                            <?php $email = (isset($venue) ? $venue->email : ''); ?>
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
                        <h4><strong>Online</strong></h4>
                        <div class="clearfix"></div>
                        <div class="panel_s btmbrd">
                            <div class="panel-body">
                                <?php if (isset($venue)) {
                                    $pw = 0; ?>
                                    <?php if (!empty($venue->venuelinks)) {
                                        foreach ($venue->venuelinks as $pk => $pv) { ?>
                                            <div class="row" id="website-<?php echo $pw; ?>">
                                                <div class="col-sm-3">
                                                    <label for="website[<?php echo $pw; ?>][type]"
                                                           class="control-label">Type</label>
                                                    <select name="website[<?php echo $pw; ?>][type]"
                                                            id="website[<?php echo $pw; ?>][type]"
                                                            class="form-control social_web selectpicker"
                                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                        <?php foreach ($socialsettings as $social) {
                                                            $sselected = '';
                                                            if ($social['socialid'] == $pv['venuelinktype']) {
                                                                $sselected = "selected='selected'";
                                                            }
                                                            echo '<option value="' . $social['socialid'] . '" ' . $sselected . '>' . $social['name'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-8  col-xs-10">
                                                    <?php $website = (isset($pv['venuelink']) ? $pv['venuelink'] : ''); ?>
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
                                            <div class="col-sm-6">
                                                <?php $website = ''; ?>
                                                <?php echo render_input('website[0][url]', 'Address', $website); ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <div class="row" id="website-0">
                                        <div class="col-sm-3">
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
                                        <div class="col-sm-6">
                                            <?php $website = (isset($venue) ? $venue->website : ''); ?>
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
                        <h4><strong>Phone</strong></h4>
                        <div class="clearfix"></div>
                        <div class="panel_s btmbrd">
                            <div class="panel-body">
                                <?php if (isset($venue)) {
                                    $pp = 0; ?>
                                    <?php if (!empty($venue->venuephone)) {
                                        if (is_serialized($venue->venuephone)) {
                                            $venue->venuephone = unserialize($venue->venuephone);
                                        } else {
                                            $venuephone = array();
                                            $venuephone[0]['type'] = "primary";
                                            $venuephone[0]['phone'] = $venue->venuephone;
                                            $venue->venuephone = $venuephone;
                                        }
                                        ?>
                                        <?php foreach ($venue->venuephone as $pk => $pv) { ?>
                                            <div class="row" id="phone-<?php echo $pp; ?>">
                                                <div class="col-sm-3">
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
                                                <div class="col-sm-6 col-xs-10 multiphone">
                                                    <?php $phone = (isset($pv['phone']) ? $pv['phone'] : ''); ?>
                                                    <?php echo render_input('phone[' . $pp . '][phone]', 'client_phonenumber', $phone, 'tel', array('autocomplete' => 'off')); ?>
                                                </div>
                                                <div class="col-sm-2 col-xs-10 multiext">
                                                    <?php $phone = (isset($pv['ext']) ? $pv['ext'] : ''); ?>
                                                    <?php echo render_input('phone[' . $pp . '][ext]', 'Ext', $phone, 'tel', array('autocomplete' => 'off', 'maxlength' => 5)); ?>
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
                                            <label for="phone[0][type]" class="control-label">Type</label>
                                            <select name="phone[0][type]" id="phone[0][type]"
                                                    class="form-control selectpicker"
                                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <?php
                                                echo '<option value="primary" selected="selected">Primary</option>';
                                                ?>
                                            </select>
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
                        <h4><strong>Address</strong></h4>
                        <div class="clearfix"></div>
                        <div class="panel_s btmbrd">
                            <div class="panel-body">
                                <div id="venueaddress">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <label for="venueaddress[type]" class="control-label">Type</label>
                                            <select name="venueaddress[type]" id="venueaddress[type]"
                                                    class="form-control selectpicker"
                                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <?php
                                                echo '<option value="primary" selected="selected">Primary</option>';
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-sm-8">
                                            <div id="locationField" class="form-group">
                                                <label class="control-label" for="address">Address</label>
                                                <input id="venueautocomplete" class="form-control searchmap"
                                                       data-addmap="0"
                                                       placeholder="Search Google Maps..." onFocus="geolocate()"
                                                       type="text">
                                            </div>

                                            <div class="customadd-btn">
                                                <button type="button"
                                                        class="btn btn-info custom_address customadd"
                                                        style="display:block" data-addressid="0">Custom
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                    if (isset($venue)) {
                                        if (!empty($venue->venueaddress) || !empty($venue->venueaddress2) || !empty($venue->venuecity) || !empty($venue->venuestate) || !empty($venue->venuezip)) {
                                            $style = 'style="display:block"';
                                        } else {
                                            $style = 'style="display:none"';
                                        }
                                    } else {
                                        $style = 'style="display:none"';
                                    }
                                    ?>
                                    <div id="customaddress" class="addressdetails customaddress" <?php echo $style; ?> >

                                        <div class="row">
                                            <div class="col-xs-11">
                                                <?php $street_number = (isset($venue) ? $venue->venueaddress : ''); ?>
                                                <?php echo render_input('venueaddress[street_number]', 'Address1', $street_number); ?>
                                            </div>
                                            <div class="col-xs-1">
                                                <div data-id="#customaddress"
                                                     class="exp_clps_address">
                                                    <a href="javascript:void(0)"><i class="fa fa-caret-up"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="address_extra">
                                            <div class="row">
                                                <div class="col-sm-11">
                                                    <?php $address2 = (isset($venue) ? $venue->venueaddress2 : ''); ?>
                                                    <?php echo render_input('venueaddress[route]', 'Address2', $address2); ?>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="col-sm-5">
                                                    <?php $city = (isset($venue) ? $venue->venuecity : ''); ?>
                                                    <?php echo render_input('venueaddress[locality]', 'client_city', $city); ?>
                                                </div>
                                                <div class="col-sm-6">
                                                    <?php $state = (isset($venue) ? $venue->venuestate : ''); ?>
                                                    <?php echo render_input('venueaddress[administrative_area_level_1]', 'client_state', $state); ?>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-5">
                                                    <?php $zip = (isset($venue) ? $venue->venuezip : ''); ?>
                                                    <?php echo render_input('venueaddress[postal_code]', 'client_postal_code', $zip); ?>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="form-group">
                                                        <label for="venueaddress[country]"
                                                               class="control-label">Country</label>
                                                        <select name="venueaddress[country]" id="venueaddress[country]"
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
                        </div>
                    </div>
                </div>
                <div class="topButton">
                    <button class="btn btn-default" type="button"
                            onclick="fncancel();"><?php echo _l('Cancel'); ?></button>
                    <?php if ($is_sido_admin == 1) { ?>
                        <button type="submit" class="btn btn-info"><?php echo _l('submit_approve'); ?></button>
                    <?php } else { ?>
                        <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                    <?php } ?>
                </div>
                <!--</div>
            </div>-->
            </div>
            <input type="hidden" name="hdnlid" value="<?php echo isset($lid) ? $lid : ''; ?>">
            <input type="hidden" name="hdnpid" value="<?php echo isset($pid) ? $pid : ''; ?>">
            <input type="hidden" name="hdneid" value="<?php echo isset($eid) ? $eid : ''; ?>">
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<!--<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB-0SSogvGqWSro2pyjAlek2DP_lwfQMvE&libraries=places&callback=initAutocomplete"></script>-->
<script type="text/javascript">
    //add more venue link
    $("body").on("click", "#add-more-venue", function (e) {
        e.preventDefault();
        var my_fields = $("div[id^='field-']");
        var highest = -Infinity;
        $.each(my_fields, function (mindex, mvalue) {
            var fieldNum = mvalue.id.split("-");
            highest = Math.max(highest, parseFloat(fieldNum[1]));
        });

        var next = highest;
        var arr = [];

        var addto = "#field-" + next;
        var addRemove = "#field-" + (next);

        next = next + 1;
        var newIn = "";
        newIn += '<div class="row" id="field-' + next + '">';
        newIn += ' <div name="field' + next + '" class="col-sm-6 form-group"><label class="control-label" for="venuelink[' + next + ']">Venue Link</label><input type="text" id="venuelink' + next + '" name="venuelink[' + next + ']" class="form-control venuelink"></div>';

        newIn += '<div class="col-sm-2"><button id="removevenue' + (next) + '" class="btn btn-danger remove-me-venue" >Remove</button></div></div>';
        newIn += '</div>';
        var newInput = $(newIn);
        $(addto).after(newInput);
        //$(addRemove).after(removeButton);

        $("#field-" + next).attr('data-source', $(addto).attr('data-source'));
        $("#count").val(next);
    });

    //remove venue link
    $('body').on("click", ".remove-me-venue", function (e) {
        e.preventDefault();
        var fieldNum = this.id.charAt(this.id.length - 1);

        var removedEventNum = $("#venuelinks" + fieldNum).val();
        var fieldID = "#field-" + fieldNum;
        $(this).remove();
        $(fieldID).remove();
        $(".selectpicker").selectpicker('refresh');
    });

    //add more site location
    $("body").on("click", "#add-more-sitelocation", function (e) {
        e.preventDefault();
        var my_fields = $("div[id^='fieldsitelocation-']");
        var highest = -Infinity;
        $.each(my_fields, function (mindex, mvalue) {
            var fieldNum = mvalue.id.split("-");
            highest = Math.max(highest, parseFloat(fieldNum[1]));
        });

        var next = highest;
        var arr = [];

        var addto = "#fieldsitelocation-" + next;
        var addRemove = "#fieldsitelocation-" + (next);

        next = next + 1;
        var newIn = "";
        newIn += ' <div id="fieldsitelocation-' + next + '" name="fieldsitelocation' + next + '" class="row mbot20">';
        newIn += ' <div class="row form-group">';
        newIn += ' <div class="col-sm-4">';
        newIn += ' <label class="control-label" for="sitelocation_name[' + next + ']">Site Location Name</label><input type="text" id="sitelocation_name' + next + '" name="sitelocation_name[' + next + ']" class="form-control">';
        newIn += ' </div>';
        newIn += ' <div class="col-sm-8">';
        newIn += ' <label class="control-label" for="sitelocation_link[' + next + ']">Site Location Link</label>';
        newIn += ' <input type="text" id="sitelocation_link' + next + '" name="sitelocation_link[' + next + ']" class="form-control">';
        newIn += ' </div>';
        newIn += ' </div>';
        newIn += ' <div class="row form-group">';
        newIn += ' <div class="col-sm-4">';
        newIn += ' <label for="sitelocation_contactname[' + next + ']" class="control-label">Site Location Contact Name</label>';
        newIn += ' <input type="text" id="sitelocation_contactname' + next + '" name="sitelocation_contactname[' + next + ']" class="form-control">';
        newIn += ' </div>';
        newIn += ' <div class="col-sm-4 multiphone">';
        newIn += ' <label for="sitelocation_contactphone[' + next + ']" class="control-label">Site Location Contact Phone</label><input type="text" id="sitelocation_contactphone' + next + '" name="sitelocation_contactphone[' + next + ']" class="form-control">';
        newIn += ' </div>';
        newIn += ' <div class="col-sm-4">';
        newIn += ' <label for="sitelocation_contactemail' + next + '" class="control-label">Site Location Contact Email</label><input type="text" id="sitelocation_contactemail' + next + '" name="sitelocation_contactemail[' + next + ']" class="form-control contactemail">';
        newIn += ' </div>';
        newIn += ' </div>';
        newIn += ' <div class="row">';
        newIn += ' <div class="col-sm-4">';
        newIn += ' <div class="profile-pic">';
        newIn += ' <div class="form-group uploadProfilepic">';
        newIn += ' <label for="sitelocationimages' + next + '" class="sitelocation-images">Site Location Images</label>';
        newIn += ' <div class="input-group">';
        newIn += ' <span class="input-group-btn">';
        newIn += ' <span class="btn btn-primary" onclick="$(this).parent().find("input[type=file]").click();">Browse</span>';
        newIn += ' <input type="file" name="sitelocationimages' + next + '[]" onchange="$(this).parent().parent().find(".form-control").html($(this).val().split(/[\\|/]/).pop());" style="display: none;" id="sitelocationimages' + next + '" multiple="multiple">';
        newIn += ' </span>';
        newIn += ' <span class="form-control"></span>';
        newIn += ' </div>';
        newIn += ' </div>';
        newIn += ' </div>';
        newIn += ' </div>';
        newIn += ' <div class="col-sm-4">';
        newIn += ' <div class="profile-pic">';
        newIn += ' <div class="form-group uploadProfilepic">';
        newIn += ' <label for="sitelocationfiles' + next + '" class="sitelocation-files">Site Location Files</label>';
        newIn += ' <div class="input-group">';
        newIn += ' <span class="input-group-btn">';
        newIn += ' <span class="btn btn-primary" onclick="$(this).parent().find("input[type=file]").click();">Browse</span>';
        newIn += ' <input type="file" name="sitelocationfiles' + next + '[]" onchange="$(this).parent().parent().find(".form-control").html($(this).val().split(/[\\|/]/).pop());" style="display: none;"  id="sitelocationfiles' + next + '" multiple="multiple">';
        newIn += ' </span>';
        newIn += ' <span class="form-control"></span>';
        newIn += ' </div>';
        newIn += ' </div>';
        newIn += ' </div>';
        newIn += ' </div>';
        newIn += ' </div>';

        newIn += ' <div class="text-left"><button id="removesitelocation' + (next) + '" class="btn btn-danger remove-me-sitelocation" >Remove</button></div>';
        newIn += ' </div>';
        var newInput = $(newIn);
        $(addto).after(newInput);
        //$(addRemove).after(removeButton);

        $("#fieldsitelocation-" + next).attr('data-source', $(addto).attr('data-source'));
        $("#count").val(next);

        createPhoneValidation();
        createExtValidation();
    });

    //add more site location
    $("body").on("click", "#add-more-venuecontact", function (e) {
        e.preventDefault();
        var my_fields = $("div[id^='venuecontact-']");
        var highest = -Infinity;
        $.each(my_fields, function (mindex, mvalue) {
            var fieldNum = mvalue.id.split("-");
            highest = Math.max(highest, parseFloat(fieldNum[1]));
        });

        var next = highest;
        var arr = [];

        var addto = "#venuecontact-" + next;
        var addRemove = "#venuecontact-" + (next);

        next = next + 1;
        var newIn = "";
        newIn += ' <div id="venuecontact-' + next + '" name="venuecontact' + next + '" class="row mbot20">';
        newIn += ' <div class="row form-group">';
        newIn += ' <div class="col-sm-4">';
        newIn += ' <label for="venue_contactname[' + next + ']" class="control-label">Venue Contact Name</label>';
        newIn += ' <input type="text" id="venuecontactname' + next + '" name="venuecontactname[' + next + ']" class="form-control">';
        newIn += ' </div>';
        newIn += ' <div class="col-sm-4 multiphone">';
        newIn += ' <label for="venue_contactphone[' + next + ']" class="control-label">Venue Contact Phone</label><input type="text" id="venuecontactphone' + next + '" name="venuecontactphone[' + next + ']" class="form-control">';
        newIn += ' </div>';
        newIn += ' <div class="col-sm-4">';
        newIn += ' <label for="venue_contactemail' + next + '" class="control-label">Venue Contact Email</label><input type="text" id="venuecontactemail' + next + '" name="venuecontactemail[' + next + ']" class="form-control contactemail">';
        newIn += ' </div>';
        newIn += ' </div>';

        newIn += ' <div class="text-left"><button id="removevenuecontact' + (next) + '" class="btn btn-danger remove-me-venuecontact" >Remove</button></div>';
        newIn += ' </div>';
        var newInput = $(newIn);
        $(addto).after(newInput);
        //$(addRemove).after(removeButton);

        $("#venuecontact-" + next).attr('data-source', $(addto).attr('data-source'));
        $("#count").val(next);

        createPhoneValidation();
        createExtValidation();
    });

    //remove site location
    $('body').on("click", ".remove-me-sitelocation", function (e) {
        var fieldNum = this.id.charAt(this.id.length - 1);

        var removedEventNum = $("#sitelocation_name" + fieldNum).val();
        var fieldID = "#fieldsitelocation-" + fieldNum;
        $(this).remove();
        $(fieldID).remove();
        $(".selectpicker").selectpicker('refresh');
    });

    //remove site location
    $('body').on("click", ".remove-me-venuecontact", function (e) {
        var fieldNum = this.id.charAt(this.id.length - 1);

        var removedEventNum = $("#venuecontactname" + fieldNum).val();
        var fieldID = "#venuecontact-" + fieldNum;
        $(this).remove();
        $(fieldID).remove();
        $(".selectpicker").selectpicker('refresh');
    });
    _validate_form($('.venue-form'), {
        venuename: 'required',
        venueaddress: 'required',
        /*venuetags: 'required',*/
        venueemail: 'email'
    });

    $('.contactemail').each(function () {
        $(this).rules("add", {
            email: true
        });
    });

    jQuery.validator.addMethod("phoneUS", function (phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, "");
        return this.optional(element) || phone_number.length > 9 && phone_number.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
    }, "Please specify a valid phone number.(Ex: xxxxxxxxxx)");

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
    createPhoneValidation();
    createExtValidation();
</script>
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
        if (id > '0') {
            location.href = '<?php echo base_url(); ?>admin/venues/view/<?php echo isset($venue->venueid) ? $venue->venueid : ""; ?>?lid=' + id;
        } else if (pid > '0') {
            location.href = '<?php echo base_url(); ?>admin/venues/view/<?php echo isset($venue->venueid) ? $venue->venueid : ""; ?>?pid=' + pid;
        } else if (eid > '0') {
            location.href = '<?php echo base_url(); ?>admin/venues/view/<?php echo isset($venue->venueid) ? $venue->venueid : ""; ?>?eid=' + eid;
        } else {
            <?php if( isset($venue->venueid) && $venue->venueid > 0 ){ ?>
            location.href = '<?php echo base_url(); ?>admin/venues/view/<?php echo isset($venue->venueid) ? $venue->venueid : ""; ?>';
            <?php }else{ ?>
            location.href = '<?php echo base_url(); ?>admin/venues/';

            <?php } ?>

        }
    }

    // Code for multiple email validation
    var createEmailValidation = function () {

        $(".multiemail .form-control").each(function (index, value) {
            $(this).rules('remove');
            $(this).rules('add', {
                email: true,
                required: true,
                remote: {
                    url: site_url + "admin/misc/venue_email_exists",
                    type: 'post',
                    data: {
                        email: function () {
                            return $(value).val();
                        },
                        venueid: function () {
                            return $('input[name="venueid"]').val();
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
    }

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
    $('.custom_address').on('click', function () {
        $(".customaddress").show();
    });
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
        newemailIn += ' <div class="row" id="email-' + emailnext + '" name="email' + emailnext + '"><div class="col-sm-3"><label class="control-label" for="email[' + emailnext + '][type]">Type</label><select id="email[' + emailnext + '][type]" name="email[' + emailnext + '][type]" class="selectpicker" data-width="100%" data-none-selected-text="Select">';
        $.each(email_phone_type, function (etindex, etvalue) {
            newemailIn += '<option value="' + etindex + '">' + etvalue + '</option>';
        });

        newemailIn += '</select></div>';
        newemailIn += '<div class="col-sm-8 multiemail"><div class="form-group"><label class="control-label" for="email[' + emailnext + '][email]"><small class="req text-danger">* </small>Email</label><input id="email[' + emailnext + '][email]" class="form-control" name="email[' + emailnext + '][email]" autocomplete="off" value="" type="email"></div>';
        newemailIn += '</div>';
        newemailIn += '<div class="col-sm-1"><button id="emailremove-' + (emailnext) + '" class="email-remove-me" ><i class="fa fa-trash-o"></i></button></div></div>';
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
        newphoneIn += ' <div class="row" id="phone-' + phonenext + '" name="phone' + phonenext + '"><div class="col-sm-3"><label class="control-label" for="phone[' + phonenext + '][type]">Type</label><select id="phone[' + phonenext + '][type]" name="phone[' + phonenext + '][type]" class="selectpicker" data-width="100%" data-none-selected-text="Select">';
        $.each(email_phone_type, function (epindex, epvalue) {
            newphoneIn += '<option value="' + epindex + '">' + epvalue + '</option>';
        });

        newphoneIn += '</select></div>';
        newphoneIn += '<div class="col-sm-6 col-xs-10 multiphone"><div class="form-group"><label class="control-label" for="phone[' + phonenext + '][phone]">Phone</label><input id="phone[' + phonenext + '][phone]" class="form-control" name="phone[' + phonenext + '][phone]" autocomplete="off" value="" type="tel"></div>';
        newphoneIn += '</div>';
        newphoneIn += '<div class="col-sm-2 col-xs-10 multiext"><div class="form-group"><label class="control-label" for="phone[' + phonenext + '][ext]">Ext</label><input id="phone[' + phonenext + '][ext]" class="form-control" name="phone[' + phonenext + '][ext]" autocomplete="off" maxlength=5 value="" type="tel"></div>';
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
        newwebsiteIn += ' <div class="row" id="website-' + websitenext + '" name="website' + websitenext + '"><div class="col-sm-3"><label class="control-label" for="website[' + websitenext + '][type]">Type</label><select id="website[' + websitenext + '][type]" name="website[' + websitenext + '][type]" class="selectpicker" data-width="100%" data-none-selected-text="Select">';
        $.each(website_type, function (windex, wvalue) {
            newwebsiteIn += '<option value="' + wvalue['socialid'] + '">' + wvalue['name'] + '</option>';
        });

        newwebsiteIn += '</select></div>';
        newwebsiteIn += '<div class="col-sm-8  col-xs-10"><div class="form-group"><label class="control-label" for="website[' + websitenext + '][url]">Address</label><input id="website[' + websitenext + '][url]" class="form-control" name="website[' + websitenext + '][url]" autocomplete="off" value="" type="text"></div>';
        newwebsiteIn += '</div>';
        newwebsiteIn += '<div class="col-sm-1  col-xs-2"><label class="control-label" for="">&nbsp;</label><button id="websiteremove-' + (websitenext) + '" class="website-remove-me" ><i class="fa fa-trash-o"></i></button></div></div>';
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

    $(".searchmap").on("keyup, change, keypress, keydown, click", function () {
        initAutocomplete();
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

    function initAutocomplete(addid = "") {
        addid = addid;
        autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('venueautocomplete')),
            {types: ['geocode'], componentRestrictions: {country: 'us'}});
        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', function () {
            //google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();

            for (var component in componentForm) {
                document.getElementById("venueaddress[" + component + "]").value = '';
                document.getElementById("venueaddress[" + component + "]").disabled = false;
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
                    document.getElementById("venueaddress[" + addressType + "]").value = val;
                }
            }
            $(".customaddress").show();
            $(".customadd").hide();
            $(".removeadd").show();
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