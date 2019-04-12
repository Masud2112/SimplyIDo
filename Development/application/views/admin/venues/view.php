<?php
/**
 * Added By : Vaidehi
 * Dt : 02/21/2018
 * Venue details
 */
init_head();
?>
<div id="wrapper" class="venuedashboard">
    <div class="content">
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
            <span><?php echo isset($venue) ? $venue->venuename : "New Venue" ?></span>
        </div>
        <h1 class="pageTitleH1"><i class="fa fa-address-book-o"></i><?php echo "Venue Dashboard"; ?></h1>
        <div class="row">
            <div class="col-md-12 widget-holder venueDashboard_blk">
                <div class="coverImg">
                    <?php
                    $cover_path = base_url() . 'assets/images/default_banner.png';
                    if (!empty($venue->venuecoverimage)) {
                        $path = get_upload_path_by_type('venue_coverimage') . $venueid . '/' . $venue->venuecoverimage;
                        if (file_exists($path)) {
                            $path = get_upload_path_by_type('venue_coverimage') . $venueid . '/croppie_' . $venue->venuecoverimage;
                            $cover_path = base_url() . 'uploads/venue_cover_images/' . $venueid . '/' . $venue->venuecoverimage;
                            if (file_exists($path)) {
                                $cover_path = base_url() . 'uploads/venue_cover_images/' . $venueid . '/croppie_' . $venue->venuecoverimage;
                            }
                        }
                    } ?>
                    <img src="<?php echo $cover_path; ?>">
                </div>
                <div class="row h-100">
                    <div class="col-sm-6 ">
                        <div class="card-user-profile-widget">
                            <div class="row">
                                <div class="col-sm-12 widget-holder">
                                    <div class="titleRow">
                                        <h4>Profile</h4>
                                        <a class="editlink onHoverShow"
                                           href="<?php echo admin_url('venues/venue/' . $venueid) ?>"><strong><i
                                                        class="fa fa-pencil"></i><span class="mleft5">Edit Venue</span></strong></a>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="card-user-info-widget panel_s btmbrd">
                                        <div class="row">
                                            <div class="col-sm-12 card-user-info">
                                                <figure class="text-center thumb-lg">
                                                    <div class="thumb-img-wrap">
                                                        <?php echo venue_logo_image($venueid, array('profile_image', '', 'profile-image-thumb')); ?>
                                                    </div>
                                                    <?php if (!empty($venue->venuelogo)) { ?>
                                                        <a href="<?php echo admin_url('venues/remove_venue_logo_image/' . $venueid . "?screen=view") ?>"
                                                           class="removelink"><i class="fa fa-remove"></i><span
                                                                    class="mleft5">Remove</span></a>
                                                    <?php } ?>
                                                </figure>
                                                <div class="profile-container">

                                                    <h4><?php echo $venue->venuename; ?></h4>
                                                    <h6><?php
                                                        if (!empty($venue->venueslogan)) {
                                                            echo $venue->venueslogan;
                                                        } ?>
                                                    </h6>
                                                    <h6>
                                                        <?php
                                                        $address = '';
                                                        if (!empty($venue->venuecity)) {
                                                            $address .= $venue->venuecity;
                                                        }
                                                        if (!empty($venue->venuestate)) {
                                                            $address .= ', ' . $venue->venuestate;
                                                        }
                                                        ?>
                                                        <?php echo $address; ?>
                                                    </h6>


                                                    <div class="venuetags">
                                                        <?php if (!empty($venue->venuetags)) {
                                                            $venuetags = explode(',', $venue->venuetags);
                                                            $venuetags = get_tags_in_addressbook($venuetags); ?>
                                                            <i class="fa fa-tags"></i>
                                                            <?php foreach ($venuetags as $venuetag) { ?>
                                                                <span class="tag"><?php echo $venuetag; ?></span>
                                                            <?php }
                                                        } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if ($venue->ispublic == 1) {
                                            $shared = "fa-unlock";
                                        } else {
                                            $shared = "fa-lock";
                                        }
                                        ?>
                                        <span class="shared pull-right"><i
                                                    class="fa <?php echo $shared ?> "></i><span
                                                    class="mleft5">Shared</span></span>

                                    </div>
                                </div>
                                <div class="col-sm-12 widget-holder address-block">
                                    <div class="titleRow">
                                        <h4>Address</h4>
                                        <a class="editicon onHoverShow"
                                           href="<?php echo admin_url('venues/venue/' . $venueid) ?>"><strong><i
                                                        class="fa fa-pencil"></i></strong></a>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="panel_s btmbrd">
                                        <div class="panel-body">
                                            <div class="col-md-12">
                                                <?php
                                                $address1 = '';
                                                $address2 = '';
                                                if (!empty($venue->venueaddress)) {
                                                    $address1 .= $venue->venueaddress;
                                                }
                                                if (!empty($venue->venueaddress2)) {
                                                    $address1 .= ', ' . $venue->venueaddress2;
                                                }
                                                if (!empty($venue->venuecity)) {
                                                    $address2 .= $venue->venuecity;
                                                }
                                                if (!empty($venue->venuestate)) {
                                                    $address2 .= ', ' . $venue->venuestate;
                                                }
                                                if (!empty($venue->venuezip)) {
                                                    $address2 .= ' - ' . $venue->venuezip;
                                                }
                                                if (!empty($venue->venuecountry) && $venue->venuecountry == 236) {
                                                    $address2 .= ', USA';
                                                }
                                                $latlang = getLatLong($address1 . " " . $address2);
                                                $maplink = "javascript:void(0)";
                                                if (is_array($latlang)) {
                                                    $lat = $latlang['latitude'];
                                                    $lang = $latlang['longitude'];
                                                    $maplink = "http://maps.google.co.uk/maps?q=" . $lat . "," . $lang;
                                                }
                                                if (!empty($address1)) {
                                                    ?>
                                                    <p><strong>Primary</strong></p>
                                                    <p><?php echo $address1; ?></p>
                                                    <p><?php echo $address2; ?></p>
                                                    <div class="address_link">
                                                        <a href="<?php echo $maplink; ?>" target="_blank">
                                                            <i class="fa fa-car"></i>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 widget-holder venueConnect_blk">
                                    <div class="titleRow">
                                        <h4>Connect</h4>
                                        <a class="editicon onHoverShow"
                                           href="<?php echo admin_url('venues/venue/' . $venueid) ?>">
                                            <strong><i class="fa fa-pencil"></i></strong>
                                        </a>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="panel_s btmbrd">
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <?php
                                                    if (!empty($venue->venueemail)) {
                                                        if (is_serialized($venue->venueemail)) {
                                                            $venue->venueemail = unserialize($venue->venueemail);
                                                        } else {
                                                            $venueemail = array();
                                                            $venueemail[0]['type'] = "primary";
                                                            $venueemail[0]['email'] = $venue->venueemail;
                                                            $venue->venueemail = $venueemail;
                                                        }
                                                        ?>
                                                        <div class="row mbot10">
                                                            <div class="col-md-2">Email</div>
                                                            <div class="col-md-10">
                                                                <?php foreach ($venue->venueemail as $email) { ?>
                                                                    <div class="row mbot5">
                                                                        <div class="col-md-2">
                                                                            <b><?php echo ucfirst($email['type']) ?>
                                                                                :</b>
                                                                        </div>
                                                                        <div class="col-md-8"><a
                                                                                    href="mailto:<?php echo $email['email'] ?>"><?php echo $email['email'] ?></a>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if (!empty($venue->venuephone)) {
                                                        if (is_serialized($venue->venuephone)) {
                                                            $venue->venuephone = unserialize($venue->venuephone);
                                                        } else {
                                                            $venuephone = array();
                                                            $venuephone[0]['type'] = "primary";
                                                            $venuephone[0]['phone'] = $venue->venuephone;
                                                            $venue->venuephone = $venuephone;
                                                        } ?>
                                                        <div class="row mbot10">
                                                            <div class="col-md-2">Phone</div>
                                                            <div class="col-md-10">
                                                                <?php foreach ($venue->venuephone as $phone) { ?>
                                                                    <div class="row mbot5">
                                                                        <div class="col-md-2">
                                                                            <b><?php echo ucfirst($phone['type']) ?>
                                                                                :</b>
                                                                        </div>
                                                                        <div class="col-md-8">
                                                                            <?php
                                                                            echo $phone['phone'];
                                                                            if (!empty($phone['ext'])) {
                                                                                echo " X " . $phone['ext'];
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php
                                                    if (!empty($venue->venuelinks)) { ?>
                                                        <div class="row mbot10">
                                                            <div class="col-md-2">Online</div>
                                                            <div class="col-md-10 weblink">
                                                                <?php foreach ($venue->venuelinks as $website) { ?>
                                                                    <div class="row mbot5">
                                                                        <div class="col-md-2">
                                                                            <b><?php echo ucfirst($website['name']) ?>
                                                                                :</b>
                                                                        </div>
                                                                        <div class="col-md-8"><a
                                                                                    href="http://<?php echo $website['venuelink'] ?>"
                                                                                    target="_blank"><?php echo $website['venuelink'] ?></a>
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 widget-holder">
                                    <div class="titleRow">
                                        <h4>Contacts</h4>
                                        <a class="editicon onHoverShow"
                                           href="<?php echo admin_url('addressbooks/addressbook?venue=' . $venueid) ?>">
                                            <i class="fa fa-plus"></i>
                                        </a>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="panel_s btmbrd">
                                        <div class="panel-body">
                                            <?php
                                            $current_venue = get_vanue_data($venueid);
                                            $class = "";
                                            if (is_sido_admin() || $current_venue->created_by == get_staff_user_id()) {
                                                $class = "sortable";
                                            }
                                            ?>
                                            <div class="row row-flex <?php echo $class; ?>">
                                                <?php
                                                foreach ($venuecontacts as $vc) {
                                                    $cid = $vc['addressbookid'];
                                                    $name = get_addressbook_full_name($cid);
                                                    $email = get_addressbook_email($cid);
                                                    $phone = get_addressbook_phone($cid);
                                                    $title = get_addressbook_title($cid);

                                                    ?>
                                                    <div class="col-sm-6">
                                                        <div class="contact_inner">
                                                            <div class="pull-right text-right">
                                                                <div>
                                                                    <a class='show_act'
                                                                       href='javascript:void(0)'><i
                                                                                class='fa fa-ellipsis-v'
                                                                                aria-hidden='true'></i>
                                                                    </a>
                                                                </div>
                                                                <div class='table_actions'>
                                                                    <ul>
                                                                        <li>
                                                                            <a href="<?php echo admin_url('addressbooks/view/' . $cid . '?venue=' . $venueid) ?>"
                                                                               class="">
                                                                                <i class="fa fa-eye"></i>View
                                                                            </a>
                                                                        </li>
                                                                        <?php if (is_sido_admin() || $venue->created_by == get_staff_user_id()) { ?>
                                                                            <li>
                                                                                <a href="<?php echo admin_url('addressbooks/addressbook/' . $cid . '?venue=' . $venueid) ?>"
                                                                                   class="">
                                                                                    <i class="fa fa-pencil-square-o"></i>Edit
                                                                                </a>
                                                                            </li>
                                                                            <?php
                                                                            if (is_sido_admin() || $venue->created_by == get_staff_user_id()) { ?>
                                                                                <li>
                                                                                    <a href="<?php echo admin_url('venues/deletecontact/' . $vc['venuecontactid']) ?>"
                                                                                       class="_delete">
                                                                                        <i class="fa fa fa-times"></i>Delete
                                                                                    </a>
                                                                                </li>
                                                                            <?php }
                                                                        } ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="text-center"><?php echo addressbook_profile_image($cid, array('profile-image'), 'thumb'); ?></div>
                                                            <h3 class="cntct_name"><?php echo $name ?></h3>
                                                            <p><?php echo $title ?></p>
                                                            <h3 class="call"><?php echo $phone ?></h3>
                                                            <p><?php echo $email ?></p>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 widget-holder">
                                    <div class="titleRow">
                                        <h4>Notes</h4>
                                        <?php
                                        $len = count($notes);
                                        $i = 0;
                                        if ($len > 0) { ?>
                                            <a href="javascript:void(0)" class="new_contact_note onHoverShow"><i
                                                        class="fa fa-plus"></i></a>
                                        <?php } ?>
                                        <div class="clearfix"></div>
                                    </div>

                                    <div class="panel_s btmbrd">
                                        <div class="panel-body">

                                            <div class="col-sm-12">
                                                <?php
                                                $noteFormclass = "";
                                                if ($len > 0) {
                                                    $noteFormclass = "hideForm";
                                                } ?>
                                                <?php echo form_open(admin_url('venues/add_note/' . $venueid), array('id' => 'venue-note', 'class' => $noteFormclass)); ?>
                                                <?php echo render_textarea('description'); ?>
                                                <input type="hidden" name="hdnlid"
                                                       value="<?php echo isset($lid) ? $lid : ''; ?>">
                                                <input type="hidden" name="hdnpid"
                                                       value="<?php echo isset($pid) ? $pid : ''; ?>">
                                                <input type="hidden" name="hdneid"
                                                       value="<?php echo isset($eid) ? $eid : ''; ?>">
                                                <input type="hidden" name="pg"
                                                       value="<?php echo isset($pg) ? $pg : ''; ?>">
                                                <button type="submit"
                                                        class="btn btn-info pull-right"><?php echo _l('save note'); ?></button>
                                                <button class="btn btn-default pull-right contact_note_cancel mright5"><?php echo _l('cancel'); ?></button>
                                                <div class="clearfix"></div>
                                                <?php echo form_close(); ?>
                                                <div class="panel_s ">
                                                    <?php
                                                    if ($len > 0) {
                                                        foreach ($notes as $note) { ?>
                                                            <div class="media meeting-note">
                                                                <!--<a href="<?php //echo admin_url('profile/'.$note["addedfrom"]); ?>" target="_blank">-->
                                                                <?php echo staff_profile_image($note['addedfrom'], array('staff-profile-image-small', 'pull-left mright10')); ?>
                                                                <!--</a>-->
                                                                <div class="media-body">
                                                                    <?php if ($note['addedfrom'] == get_staff_user_id() || is_admin()) { ?>
                                                                        <div class="pull-right text-right">
                                                                            <div><a class='show_act'
                                                                                    href='javascript:void(0)'><i
                                                                                            class='fa fa-ellipsis-v'
                                                                                            aria-hidden='true'></i></a>
                                                                            </div>
                                                                            <div class='table_actions'>
                                                                                <ul>
                                                                                    <li>
                                                                                        <a href="#"
                                                                                           class=""
                                                                                           onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;">
                                                                                            <i class="fa fa-pencil-square-o"></i>Edit</a>
                                                                                    </li>
                                                                                    <li>
                                                                                        <a href="<?php echo admin_url('venues/delete_note/' . $note['id']) ?>"
                                                                                           class="_delete">
                                                                                            <i class="fa fa fa-times"></i>Delete</a>
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
                                                                        $date = new DateTime($note['dateadded']);
                                                                        $dt = $date->format('Y-m-d H:i:s');
                                                                        echo _l('lead_note_date_added', _dt($dt, true));
                                                                        ?>
                                                                    </small>
                                                                    <!--<a href="<?php //echo admin_url('profile/'.$note["addedfrom"]); ?>" target="_blank">-->
                                                                    <h5 class="media-heading bold"><?php echo get_staff_full_name($note['addedfrom']); ?></h5>
                                                                    <!--</a>-->
                                                                    <div data-note-description="<?php echo $note['id']; ?>"
                                                                         class="text-muted">
                                                                        <?php echo $note['description']; ?>
                                                                    </div>
                                                                    <div data-note-edit-textarea="<?php echo $note['id']; ?>"
                                                                         class="hide mtop15">
                                                                        <?php echo render_textarea('note', '', $note['description']); ?>
                                                                        <div class="text-right ">
                                                                            <button type="button"
                                                                                    class="btn btn-default"
                                                                                    onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                                                                            <button type="button"
                                                                                    class="btn btn-info"
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
                                                        }
                                                    } ?>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-user-info-widget -->
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-12 onsite-locations">
                                <div class="titleRow">
                                    <h4>On-Site Locations</h4>
                                    <?php
                                    $class = "";
                                    if (is_sido_admin() || $venue->created_by == get_staff_user_id()) {
                                        $class = "sortable";
                                    }
                                    ?>
                                    <a href="<?php echo admin_url('venues/onsitelocation?venue=' . $venueid) ?>"
                                       class="new_onsite_location editicon onHoverShow"><i class="fa fa-plus"></i></a>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel_s btmbrd">
                                    <div class="panel-body">
                                        <ul class="nav nav-tabs">
                                            <li class="active"><a data-toggle="tab" href="#all">All</a></li>
                                            <li><a data-toggle="tab" href="#indoor">Indoor</a></li>
                                            <li><a data-toggle="tab" href="#outdoor">Outdoor</a></li>
                                        </ul>
                                        <div class="tab-content">
                                            <div id="all" class="tab-pane fade in active">
                                                <div class="row <?php echo $class; ?>">
                                                    <?php
                                                    foreach ($venuelocations as $venuelocation) {
                                                        ?>
                                                        <div class="col-sm-6">
                                                            <div class="location_cover_image">
                                                                <?php
                                                                if (!empty($venuelocation->loccoverimage)) {

                                                                    $src = base_url() . 'uploads/venue_loc_images/' . $venuelocation->locid . '/' . $venuelocation->loccoverimage;
                                                                    if (!empty($venuelocation->loccoverimage)) {
                                                                        $path = get_upload_path_by_type('venue_locimage') . $venuelocation->locid . '/' . $venuelocation->loccoverimage;
                                                                        if (file_exists($path)) {
                                                                            $path = get_upload_path_by_type('venue_locimage') . $venuelocation->locid . '/croppie_' . $venuelocation->loccoverimage;
                                                                            $src = base_url() . 'uploads/venue_loc_images/' . $venuelocation->locid . '/' . $venuelocation->loccoverimage;
                                                                            if (file_exists($path)) {
                                                                                $src = base_url() . 'uploads/venue_loc_images/' . $venuelocation->locid . '/croppie_' . $venuelocation->loccoverimage;
                                                                            }
                                                                        }
                                                                    }
                                                                    //$path = base_url('uploads/venue_loc_images/') . $venuelocation->locid . '/' . $venuelocation->loccoverimage;
                                                                } else {
                                                                    $path = base_url('assets/images/no-package.png');
                                                                }
                                                                ?>
                                                                <a href="<?php echo admin_url('venues/onsitelocview/' . $venuelocation->locid . '?venue=' . $venueid) ?>">
                                                                    <img src="<?php echo $src; ?>"/>
                                                                </a>
                                                            </div>
                                                            <div class="locatio_footer">
                                                                <span><?php echo $venuelocation->locname ?></span>
                                                                <div class="pull-right text-right">
                                                                    <div><a class='show_act'
                                                                            href='javascript:void(0)'><i
                                                                                    class='fa fa-ellipsis-v'
                                                                                    aria-hidden='true'></i></a>
                                                                    </div>
                                                                    <div class='table_actions'>
                                                                        <ul>
                                                                            <li>
                                                                                <a href="<?php echo admin_url('venues/onsitelocview/' . $venuelocation->locid . '?venue=' . $venueid) ?>">
                                                                                    <i class="fa fa-eye"></i>View
                                                                                </a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="<?php echo admin_url('venues/onsitelocation/' . $venuelocation->locid . '?venue=' . $venueid) ?>">
                                                                                    <i class="fa fa-pencil"></i>Edit
                                                                                </a>
                                                                            </li>
                                                                            <li>
                                                                                <a class="_delete"
                                                                                   href="<?php echo admin_url('venues/deletelocation/' . $venuelocation->locid) ?>">
                                                                                    <i class="fa fa fa-times"></i>Delete
                                                                                </a>
                                                                            </li>

                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div id="indoor" class="tab-pane fade">
                                                <div class="row <?php echo $class; ?>">
                                                    <?php
                                                    foreach ($venuelocations as $venuelocation) {
                                                        if ($venuelocation->type == "indoor") {
                                                            ?>
                                                            <div class="col-sm-6">
                                                                <div class="location_cover_image">
                                                                    <?php
                                                                    if (!empty($venuelocation->loccoverimage)) {
                                                                        $path = base_url('uploads/venue_loc_images/') . $venuelocation->locid . '/' . $venuelocation->loccoverimage;
                                                                    } else {
                                                                        $path = base_url('assets/images/no-package.png');
                                                                    }
                                                                    ?>
                                                                    <img src="<?php echo $path; ?>"/>
                                                                </div>
                                                                <div class="locatio_footer">
                                                                    <span><?php echo $venuelocation->locname ?></span>
                                                                    <div class="pull-right text-right">
                                                                        <div><a class='show_act'
                                                                                href='javascript:void(0)'><i
                                                                                        class='fa fa-ellipsis-v'
                                                                                        aria-hidden='true'></i></a>
                                                                        </div>
                                                                        <div class='table_actions'>
                                                                            <ul>
                                                                                <li>
                                                                                    <a href="<?php echo admin_url('venues/view/' . '?venue=' . $venueid) ?>"
                                                                                       class="">
                                                                                        <i class="fa fa-eye"></i>View
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="<?php echo admin_url('venues/onsitelocation/' . $venuelocation->locid . '?venue=' . $venueid) ?>">
                                                                                        <i class="fa fa-pencil"></i>Edit
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="_delete"
                                                                                       href="<?php echo admin_url('venues/deletelocation/' . $venuelocation->locid) ?>">
                                                                                        <i class="fa fa fa-times"></i>Delete
                                                                                    </a>
                                                                                </li>

                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php }
                                                    } ?>
                                                </div>
                                            </div>
                                            <div id="outdoor" class="tab-pane fade">
                                                <div class="row row-flex <?php echo $class; ?>">
                                                    <?php
                                                    foreach ($venuelocations as $venuelocation) {
                                                        if ($venuelocation->type == "outdoor") {
                                                            ?>
                                                            <div class="col-sm-6">
                                                                <div class="location_cover_image">
                                                                    <?php
                                                                    if (!empty($venuelocation->loccoverimage)) {
                                                                        $path = base_url('uploads/venue_loc_images/') . $venuelocation->locid . '/' . $venuelocation->loccoverimage;
                                                                    } else {
                                                                        $path = base_url('assets/images/no-package.png');
                                                                    }
                                                                    ?>
                                                                    <img src="<?php echo $path; ?>"/>
                                                                </div>
                                                                <div class="locatio_footer">
                                                                    <span><?php echo $venuelocation->locname ?></span>
                                                                    <div class="pull-right text-right">
                                                                        <div>
                                                                            <a class='show_act'
                                                                               href='javascript:void(0)'>
                                                                                <i class='fa fa-ellipsis-v'
                                                                                   aria-hidden='true'>

                                                                                </i>
                                                                            </a>
                                                                        </div>
                                                                        <div class='table_actions'>
                                                                            <ul>
                                                                                <li>
                                                                                    <a href="<?php echo admin_url('venues/view/' . '?venue=' . $venueid) ?>"
                                                                                       class="">
                                                                                        <i class="fa fa-eye"></i>View
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="<?php echo admin_url('venues/onsitelocation/' . $venuelocation->locid . '?venue=' . $venueid) ?>">
                                                                                        <i class="fa fa-pencil"></i>Edit
                                                                                    </a>
                                                                                </li>
                                                                                <li>
                                                                                    <a class="_delete"
                                                                                       href="<?php echo admin_url('venues/deletelocation/' . $venuelocation->locid) ?>">
                                                                                        <i class="fa fa fa-times"></i>Delete
                                                                                    </a>
                                                                                </li>

                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php }
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 attachments-block">
                                <h4><strong>Attachments</strong>
                                    <div class="clearfix"></div>
                                </h4>
                                <div class="panel_s btmbrd">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-sm-4 mbot10">
                                                <?php echo form_open_multipart('admin/venues/upload_file', array('id' => 'venue_attachment', 'class' => 'dropzone')); ?> <?php echo form_close(); ?>
                                                <?php if (get_option('dropbox_app_key') != '') { ?>
                                                    <div class="text-center mtop10">
                                                        <div id="dropbox-chooser-venue"></div>
                                                    </div>

                                                <?php } ?>
                                            </div>
                                            <?php if (count($venue->venueattachments) > 0) { ?>
                                                <?php
                                                $i = 1;
                                                // Store all url related data here
                                                $attachments_data = array();
                                                $show_more_link_venue_attachments = do_action('show_more_link_venue_attachments', 6);
                                                foreach ($venue->venueattachments as $attachment) { ?>
                                                    <div id="vattch-<?php echo $attachment['id']; ?>"
                                                         data-venue-attachment-id="<?php echo $attachment['id']; ?>"
                                                         class="venue-attachment-col col-sm-4<?php if ($i > $show_more_link_venue_attachments) {
                                                             echo ' hide venue-attachment-col-more';
                                                         } ?>">
                                                        <ul class="list-unstyled venue-attachment-wrapper">
                                                            <li class="mbot10 venue-attachment<?php if (strtotime($attachment['dateadded']) >= strtotime('-16 hours')) {
                                                                echo ' ';
                                                            } ?>">

                                                                <div class="venue-attachment-user">
                                                                    <?php if ($attachment['staffid'] == get_staff_user_id() || is_admin()) { ?>

                                                                    <?php }
                                                                    $externalPreview = false;
                                                                    $is_image = false;
                                                                    $path = get_upload_path_by_type('venue_attachments') . $venueid . '/' . $attachment['file_name'];
                                                                    $href_url = site_url('download/file/venueattachment/' . $attachment['id']);
                                                                    $isHtml5Video = is_html5_video($path);
                                                                    if (empty($attachment['external'])) {
                                                                        $is_image = is_image($path);
                                                                        $img_url = site_url('download/preview_image?path=' . protected_file_url_by_path($path, true) . '&type=' . $attachment['filetype']);
                                                                        $img_url = site_url(protected_file_url_by_path($path, true));
                                                                    } else if ((!empty($attachment['thumbnail_link']) || !empty($attachment['external']))
                                                                        && !empty($attachment['thumbnail_link'])) {
                                                                        $is_image = true;
                                                                        $img_url = optimize_dropbox_thumbnail($attachment['thumbnail_link']);
                                                                        $externalPreview = $img_url;
                                                                        $href_url = $attachment['external_link'];
                                                                    } else if (!empty($attachment['external']) && empty($attachment['thumbnail_link'])) {
                                                                        $href_url = $attachment['external_link'];
                                                                    }
                                                                    if (!empty($attachment['external']) && $attachment['external'] == 'dropbox' && $is_image) { ?>
                                                                        <a href="<?php echo $href_url; ?>"
                                                                           target="_blank"
                                                                           class=""
                                                                           data-toggle="tooltip"
                                                                           data-title="<?php echo _l('open_in_dropbox'); ?>"><i
                                                                                    class="fa fa-dropbox"
                                                                                    aria-hidden="true"></i></a>
                                                                    <?php }
                                                                    /*if ($attachment['staffid'] != 0) {
                                                                        echo '<a href="javascript:void(0)">' . get_staff_full_name($attachment['staffid']) . '</a> - ';
                                                                    } else if ($attachment['contact_id'] != 0) {
                                                                        echo '<a href="javascript:void(0)">' . get_contact_full_name($attachment['contact_id']) . '</a> - ';
                                                                    }*/
                                                                    //echo time_ago($attachment['dateadded']);
                                                                    ?>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                                <?php ob_start(); ?>
                                                                <div class="<?php if ($is_image) {
                                                                    echo 'preview-image';
                                                                } else if (!$isHtml5Video) {
                                                                    echo 'venue-attachment-no-preview';
                                                                } ?>">
                                                                    <div class="attachment-options"><a class='show_act'
                                                                                                       href='javascript:void(0)'><i
                                                                                    class='fa fa-ellipsis-v'
                                                                                    aria-hidden='true'></i></a></div>
                                                                    <div class='table_actions'>
                                                                        <ul>
                                                                            <li>
                                                                                <a href="<?php echo $href_url; ?>"
                                                                                   class="">
                                                                                    <i class="fa fa-download"></i>Download
                                                                                </a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#"
                                                                                   class="attachmnt_edit"
                                                                                   data-attachmentid="<?php echo $attachment['id'] ?>"
                                                                                   data-attachmentname="<?php echo $attachment['file_name'] ?>"
                                                                                   data-attachmenttitle="<?php echo !empty($attachment['title']) ? $attachment['title'] : $attachment['file_name'] ?>">
                                                                                    <i class="fa fa-pencil"></i>Edit
                                                                                </a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#"
                                                                                   onclick="remove_venue_attachment(this,<?php echo $attachment['id']; ?>); return false;"><i
                                                                                            class="fa fa fa-times"></i>Delete</a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                    <?php
                                                                    // Not link on video previews because on click on the video is opening new tab
                                                                    if (!$isHtml5Video){ ?>
                                                                    <a href="<?php echo(!$externalPreview ? $href_url : $externalPreview); ?>"
                                                                       target="_blank"<?php if ($is_image) { ?> data-lightbox="venue-attachment"<?php } ?>
                                                                       class="<?php if ($isHtml5Video) {
                                                                           echo 'video-preview';
                                                                       } ?>">
                                                                        <?php } ?>
                                                                        <?php if ($is_image) { ?>
                                                                            <img src="<?php echo $img_url; ?>"
                                                                                 class="img img-responsive">
                                                                        <?php } else if ($isHtml5Video) { ?>
                                                                            <video width="100%"
                                                                                   height="100%"
                                                                                   src="<?php echo site_url('download/preview_video?path=' . protected_file_url_by_path($path) . '&type=' . $attachment['filetype']); ?>"
                                                                                   controls> Your
                                                                                browser does not support
                                                                                the video tag.
                                                                            </video>
                                                                        <?php } else { ?>
                                                                            <i class="fa-4x <?php echo get_file_class($attachment['filetype']); ?>"></i>
                                                                            <!--<span><?php /*echo $attachment['file_name']; */ ?></span>-->
                                                                        <?php } ?>
                                                                        <?php if (!$isHtml5Video){ ?>
                                                                    </a>
                                                                <?php } ?>
                                                                </div>
                                                                <?php
                                                                $attachments_data[$attachment['id']] = ob_get_contents();
                                                                ob_end_clean();
                                                                echo $attachments_data[$attachment['id']];
                                                                ?>
                                                                <div class="clearfix"></div>
                                                                <div class="attachment_footer">
                                                                    <span><?php echo !empty($attachment['title']) ? $attachment['title'] : $attachment['file_name']; ?></span>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <?php
                                                    $i++;
                                                } ?>

                                                <div class="clearfix"></div>
                                                <?php if (($i - 1) > $show_more_link_venue_attachments) { ?>
                                                    <div class="col-md-12"
                                                         id="show-more-less-venue-attachments-col"><a href="#"
                                                                                                      class="venue-attachments-more"
                                                                                                      onclick="slideToggle('.venue-attachment-col-more',venue_attachments_toggle); return false;"><?php echo _l('show_more'); ?></a>
                                                        <a href="#" class="venue-attachments-less hide"
                                                           onclick="slideToggle('.venue-attachment-col-more',venue_attachments_toggle); return false;"><?php echo _l('show_less'); ?></a>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>

                                        </div>
                                    </div>
                                    <div id="attacemnt-title-modal" class="modal fade attacemnt-title-modal"
                                         tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" aria-label="Close"
                                                            data-dismiss="modal">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                    <h4 class="modal-title">Add title for attachment </h4>
                                                </div>
                                                <form name="attachment_title"
                                                      action="<?php echo admin_url('venues/update_attachment/' . $venueid) ?>"
                                                      method="post">
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label>Attachment title</label>
                                                                    <input id="attachmenttitle" class="form-control"
                                                                           type="text" name="title"
                                                                           value=""/>
                                                                </div>
                                                                <input id="attachmentid" type="hidden"
                                                                       name="attachmentid" value=""/>
                                                                <div id="attachment_name">
                                                                    <span class="file_name">

                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit" class="btn btn-default">
                                                            <?php echo _l('Save'); ?>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.widget-body -->
            </div>
            <!-- /.widget-bg -->
        </div>
    </div>
</div>


<?php init_tail(); ?>

<script>
    $(function () {
        _validate_form($('#venue-note'), {
            description: 'required'
        });

    });

    if (typeof (venueAttachmentDropzone) != 'undefined') {
        venueAttachmentDropzone.destroy();
    }
    venueAttachmentDropzone = new Dropzone("#venue_attachment", {
        autoProcessQueue: true,
        createImageThumbnails: false,

        dictDefaultMessage: appLang.drop_files_here_to_upload,
        dictFallbackMessage: appLang.browser_not_support_drag_and_drop,
        dictFileTooBig: appLang.file_exceeds_maxfile_size_in_form,
        dictCancelUpload: appLang.cancel_upload,
        dictMaxFilesExceeded: appLang.you_can_not_upload_any_more_files,
        maxFilesize: (max_php_ini_upload_size_bytes / (1024 * 1024)).toFixed(0),
        maxFiles: 1,
        acceptedFiles: app_allowed_files,
        error: function (file, response) {
            alert_float('danger', response);
        },
        sending: function (file, xhr, formData) {
            formData.append("venueid", '<?php echo $venueid; ?>');
        },
        success: function (files, response) {
            response = response.split(':');
            $('#attachmentid').val(response[1]);
            $('#attachment_name > span').text(response[0]);
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                //init_venue_modal('<?php echo $venueid; ?>');
                alert_float('success', "Attachment uploaded successfully.");
                $('#attacemnt-title-modal').modal('show');

            }
        }
    });

    $('a.attachmnt_edit').click(function (e) {
        e.preventDefault();
        var attachmentId = $(this).data('attachmentid');
        var attachmentName = $(this).data('attachmentname');
        var attachmentTitle = $(this).data('attachmenttitle');
        $('#attachmentid').val(attachmentId);
        $('#attachmenttitle').val(attachmentTitle);

        $('#attachment_name > span').text(attachmentName);
        $('#attacemnt-title-modal').modal('show');


    });
</script>

</body>
</html>