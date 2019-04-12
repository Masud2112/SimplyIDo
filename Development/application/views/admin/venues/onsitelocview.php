<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 10-07-2018
 * Time: 03:09 PM
 */
?>

<?php init_head(); ?>
<div id="wrapper" class="venuedashboard venuelocdashboard">
    <div class="content onsite-loc-page">
        <div class="row">
            <div class="col-sm-12">
                <div class="pull-right">
                    <div class="breadcrumb">
                        <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
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
                            <a href="<?php echo admin_url('venues/view/'.$venue->venueid); ?>"><?php echo $venue->venuename; ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } ?>
                        <span><?php echo isset($locaton) ? $locaton->locname : "New Venue" ?></span>
                    </div>
                </div>
                <h1 class="pageTitleH1"><i class="fa fa-address-book-o"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="venue_details">
                    <div class="venue_cover_img">
                        <?php
                        $path = base_url() . 'assets/images/default_banner.png';
                        /*if (!empty($locaton->loccoverimage)) {
                            if (file_exists(get_upload_path_by_type('venue_locimage') . $locaton->locid . "/" . $locaton->loccoverimage)) {
                                $path = base_url() . 'uploads/venue_loc_images/' . $locaton->locid . '/' . $locaton->loccoverimage;
                            }
                        } */?>
                        <img src="<?php echo $path; ?>">

                        <div class="venue_logo_img">
                            <div class="venueProfImg_blk">
                                <div class="venueProfImgInner_blk">
                                <?php echo venue_logo_image($venue->venueid, array('profile_image', 'img-responsive img-thumbnail', 'addressbook-profile-image-thumb'), 'thumb'); ?>
                                </div>
                            </div>
                            <div class="venueProfInfo_blk">
                                
                            <div class="venue_title">
                                <h2> <?php echo $venue->venuename; ?></h2>
                            </div>
                                <?php if (!empty($venue->venueslogan)) { ?>
                                    <p class="col-xs-8 venueProfBotInfo_blk"><?php echo $venue->venueslogan; ?></p>
                                    <p class="col-xs-4 venueProfBotInfo_blk text-right"><?php echo $venue->venuecity . ", " . $venue->venuestate; ?></p>
                                <?php } else { ?>
                                    <p><?php echo $venue->venuecity . ", " . $venue->venuestate; ?></p>
                                <?php } ?>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="loc_details widget-holder">
                            <div class="titleRow">
							<h4>
                                <strong><?php echo _l('onsiteloc'); ?></strong>
                            </h4>
                            <a class="editlink onHoverShow"
                               href="<?php echo admin_url('venues/onsitelocation/' . $locaton->locid . '?venue=' . $venue->venueid) ?>">
                                <i class="fa fa-pencil"></i>
                                <?php echo _l('edit') ?>
                            </a>
							<div class="clearfix"></div>
							</div>							
                            <div class="panel_s btmbrd">
                                <div class="panel-body">
                                    <div class="location_image">
                                        <?php
                                        $src = "";
                                        if ((isset($locaton) && $locaton->loccoverimage != NULL)) {
                                            $src = base_url() . 'uploads/venue_loc_images/' . $locaton->locid . '/' . $locaton->loccoverimage;
                                            $path = get_upload_path_by_type('venue_locimage') . $locaton->locid . '/' . $locaton->loccoverimage;
                                            if (file_exists($path)) {
                                                $path = get_upload_path_by_type('venue_locimage') . $locaton->locid . '/croppie_' . $locaton->loccoverimage;
                                                $src = base_url() . 'uploads/venue_loc_images/' . $locaton->locid . '/' . $locaton->loccoverimage;
                                                if (file_exists($path)) {
                                                    $src = base_url() . 'uploads/venue_loc_images/' . $locaton->locid . '/croppie_' . $locaton->loccoverimage;
                                                }
                                            }
                                        }
                                        ?>
                                        <div class="imageview <?php echo empty($src) ? 'hidden' : ''; ?>">
                                            <img src="<?php echo $src; ?>"/>
                                        </div>
                                    </div>
                                    <div class="locatin_title">
                                        <h3><?php echo $locaton->locname ?></h3>
                                    </div>
                                    <div class="locatin_desc">
                                        <?php echo $locaton->loc_description ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="loc_contacts widget-holder">
							<div class="titleRow">
								<h4><strong><?php echo _l('primary_contatcs'); ?></strong></h4>
								<a class="editicon onHoverShow" href="javascript:void(0)" data-toggle="modal"
								   data-target="#location-contact-modal">
									<i class="fa fa-plus"></i>
								</a>
								<div class="clearfix"></div>
							</div>
                            
                            <div class="panel_s btmbrd">
                                <div class="panel-body">
                                    <?php
                                    $class = "";
                                    if (is_sido_admin() || $venue->created_by == get_staff_user_id()) {
                                        $class = "sortable";
                                    }
                                    ?>
                                    <div class="row row-flex <?php echo $class; ?>">
                                        <?php
                                        if (isset($venueloccontacts) && count($venueloccontacts) > 0) {
                                            foreach ($venueloccontacts as $vc) {
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
                                                                        <a href="<?php echo admin_url('addressbooks/view/' . $cid . '?locid='.$locaton->locid.'&vid=' . $venueid) ?>"
                                                                           class="">
                                                                            <i class="fa fa-eye"></i>View
                                                                        </a>
                                                                    </li>
                                                                    <?php if (is_sido_admin() || $venue->created_by == get_staff_user_id()) { ?>
                                                                        <li>
                                                                            <a href="<?php echo admin_url('addressbooks/addressbook/' . $cid . '?locid='.$locaton->locid.'&vid=' . $venueid) ?>"
                                                                               class="">
                                                                                <i class="fa fa-pencil-square-o"></i>Edit
                                                                            </a>
                                                                        </li>
                                                                        <?php
                                                                        if (is_sido_admin() || $vc['created_by'] == get_staff_user_id()) { ?>
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
                                            <?php }
                                        } ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="loc_image_gal widget-holder">
							<div class="titleRow">
                            <h4>
                                <strong><?php echo _l('loc_image_gallery'); ?></strong>
                            </h4>
                            <a class="editicon onHoverShow  toggle_loc_gallery_form" href="javascript:void(0)"><i
                                        class="fa fa-plus"></i></a>
                            <div class="clearfix"></div>
							</div>
							
                            <div class="panel_s btmbrd">
                                <div class="loc_gallery_form">
                                    <?php echo form_open_multipart('admin/venues/upload_loc_galley', array('id' => 'loc_gallery', 'class' => 'dropzone')); ?> <?php echo form_close(); ?>
                                    <?php if (get_option('dropbox_app_key') != '') { ?>
                                        <div class="text-center mtop10">
                                            <div id="dropbox-chooser-venue"></div>
                                        </div>

                                    <?php } ?>
                                </div>
                                <div class="clearfix"></div>
                                <div class="panel-body">
                                    <?php if (count($locaton->venuegallery) > 0) { ?>
                                        <div id="locgallery" class="row row-flex sortable">
                                            <?php foreach ($locaton->venuegallery as $gallery) {
                                                $path = get_upload_path_by_type('venue_attachments') . 'locations/' . $locaton->locid . '/' . $gallery['file_name'];
                                                $img_url = site_url('uploads/venues/locations/' . $locaton->locid . '/' . $gallery['file_name']);
                                                ?>
                                                <div class="col-sm-6" data-src="<?php echo $img_url ?>">
                                                    <a class="item" href="<?php echo $img_url ?>" data-sub-html=".caption">
                                                        <img src="<?php echo $img_url ?>"
                                                             class="img img-responsive">
                                                    </a>
                                                    <div class="clearfix"></div>
                                                    <div class="attachment_footer">
                                                        <span><?php echo !empty($gallery['title']) ? $gallery['title'] : $gallery['file_name']; ?></span>
                                                        <div class="attachment-options">
                                                            <a class='show_act'
                                                               href='javascript:void(0)'>
                                                                <i class='fa fa-ellipsis-v'
                                                                   aria-hidden='true'></i>
                                                            </a>
                                                        </div>
                                                        <div class='table_actions'>
                                                            <ul>
                                                                <li>
                                                                    <a href="#"
                                                                       class="attachmnt_edit"
                                                                       data-attachmentid="<?php echo $gallery['id'] ?>"
                                                                       data-attachmentname="<?php echo $gallery['file_name'] ?>"
                                                                       data-attachmenttitle="<?php echo !empty($gallery['title']) ? $gallery['title'] : $gallery['file_name']  ?>">
                                                                        <i class="fa fa-pencil"></i>Edit
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="#"
                                                                       onclick="remove_venue_loc_attachment(this,<?php echo $gallery['id'];  ?>); return false;">
                                                                        <i class="fa fa fa-times"></i>Delete</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="no_gallery"><?php echo _l('no_gallery_found'); ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="loc_files widget-holder">
							<div class="titleRow">
                            <h4>
                                <strong><?php echo _l('loc_files'); ?></strong>
                            </h4>
                            <div class="clearfix"></div>
							</div>
							
                            <div class="panel_s btmbrd">
                                <div class="panel-body">
                                    <div class="venue_attachments_wrapper">
                                        <div class="col-md-12" id="attachments">
                                            <div class="row">
                                                <div class="col-sm-4 mbot10">
                                                    <?php echo form_open_multipart('admin/venues/upload_loc_file', array('id' => 'venue_attachment', 'class' => 'dropzone')); ?> <?php echo form_close(); ?>
                                                    <?php if (get_option('dropbox_app_key') != '') { ?>
                                                        <div class="text-center mtop10">
                                                            <div id="dropbox-chooser-venue"></div>
                                                        </div>

                                                    <?php } ?>
                                                </div>
                                                <?php if (count($locaton->venueattachments) > 0) {
                                                $i = 1;
                                                // Store all url related data here
                                                $attachments_data = array();
                                                $show_more_link_venue_attachments = do_action('show_more_link_venue_attachments', 6);
                                                foreach ($locaton->venueattachments as $attachment) { ?>
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
                                                                    $path = get_upload_path_by_type('venue_attachments') . 'locations/' . $locaton->locid . '/' . $attachment['file_name'];
                                                                    $href_url = site_url('download/file/venuelocfile/' . $attachment['id']);
                                                                    $isHtml5Video = is_html5_video($path);
                                                                    if (empty($attachment['external'])) {
                                                                        $is_image = is_image($path);
                                                                        $img_url = site_url('download/preview_image?path=' . protected_file_url_by_path($path, true) . '&type=' . $attachment['filetype']);
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
                                                                    <?php } ?>
                                                                </div>
                                                                <div class="clearfix"></div>
                                                                <?php ob_start(); ?>
                                                                <div class="<?php if ($is_image) {
                                                                    echo 'preview-image';
                                                                } else if (!$isHtml5Video) {
                                                                    echo 'venue-attachment-no-preview';
                                                                } ?>">
                                                                    <div class="attachment-options">
                                                                        <a class='show_act'
                                                                           href='javascript:void(0)'>
                                                                            <i class='fa fa-ellipsis-v'
                                                                               aria-hidden='true'></i>
                                                                        </a>
                                                                    </div>
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
                                                                                   onclick="remove_venue_loc_attachment(this,<?php echo $attachment['id']; ?>); return false;">
                                                                                    <i class="fa fa fa-times"></i>Delete</a>
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
                                            </div>
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
                                </div>
                            </div>
                        </div>
                        <div class="loc_notes widget-holder">
							<div class="titleRow">
                            <h4>
                                <strong><?php echo _l('notes'); ?></strong>
                            </h4>
                            <a href="javascript:void(0)" class="new_contact_note editicon onHoverShow " style=""><i
                                        class="fa fa-plus"></i></a>
                            <div class="clearfix"></div>
							</div>
                            <div class="card-user-info-widget panel_s btmbrd">
                                 
                                        <div class="col-sm-12">
                                            <?php
                                            $len = count($notes);
                                            $i = 0;
                                            $noteFormclass = "";
                                            if ($len > 0) {
                                                $noteFormclass = "hideForm";
                                            } ?>
                                            <?php echo form_open(admin_url('venues/add_loc_note/' . $locaton->locid."?venue=".$venue->venueid), array('id' => 'venue-note', 'class' => $noteFormclass)); ?>
                                            <?php echo render_textarea('description'); ?>
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
                                                                                       onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;">
                                                                                        <i class="fa fa-pencil-square-o"></i>Edit</a>
                                                                                </li>
                                                                                <li>
                                                                                    <a href="#"                                                                                       
                                                                                       onclick="delete_meeting_note(this,<?php echo $note['id']; ?>);return false;">
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
                                                                <h5 class="media-heading bold"><?php echo get_staff_full_name($note['addedfrom']); ?></h5>
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
                                      <div class="clearfix"></div>
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
                                                  action="<?php echo admin_url('venues/update_loc_attachment/' . $locaton->locid . '/' . $venue->venueid) ?>"
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
        </div>
    </div>
    <?php
    $loccontats = array();
    foreach ($venueloccontacts as $venueloccontact) {
        $loccontats[] = $venueloccontact['addressbookid'];
    }
    ?>

    <!--- Location Primary Coantct Modal -->
    <div id="location-contact-modal" class="modal fade location-contact-modal"  
         tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" aria-label="Close"
                            data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add Primary Coantct </h4>
                </div>
                <form name="attachment_title"
                      action="<?php echo admin_url('venues/add_venue_contact/' . $locaton->locid . '/' . $venueid) ?>"
                      method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row row-flex">
                                    <div class="col-sm-3 col-xs-6 onSiteContacts_blk">
                                        <div class="addNewPrimaryContact_blk">
                                            <a href="<?php echo admin_url('addressbooks/addressbook?locid=' . $locaton->locid . '&vid=' . $venueid) ?>">
                                                <i class="fa fa-plus fa-5x"></i>
                                                <span>Add New</span>
                                            </a>
                                        </div>
                                    </div>
                                    <?php
                                    if (isset($venuecontacts) && count($venuecontacts) > 0) {
                                        foreach ($venuecontacts as $vc) {

                                            $cid = $vc['addressbookid'];
                                            $name = get_addressbook_full_name($cid);
                                            $email = get_addressbook_email($cid);
                                            $phone = get_addressbook_phone($cid);
                                            $title = get_addressbook_title($cid);
                                            ?>
                                            <div class="col-sm-3 col-xs-6 onSiteContacts_blk option_Checkbox <?php echo in_array($cid, $loccontats) ? "disabled" : "" ?>">
                                                <input id="contact_<?php echo $cid; ?>" class="hide loc_contact"
                                                       type="checkbox" name="contacts[]" value="<?php echo $cid; ?>">
                                                <label for="contact_<?php echo $cid; ?>" class="display-block">
                                                    <div class="contact_inner">
                                                        <div class="text-center"><?php echo addressbook_profile_image($cid, array('profile-image'), 'thumb'); ?></div>
                                                        <h3 class="cntct_name"><?php echo $name ?></h3>
                                                        <p><?php echo $title ?></p>
                                                        <h3 class="call"><?php echo $phone ?></h3>
                                                        <p><?php echo $email ?></p>
                                                    </div>
                                                </label>
                                            </div>
                                        <?php }
                                    } ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default locConSub" disabled>
                            <?php echo _l('add'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!--- Location Primary Coantct Modal -->
</div>
<?php init_tail(); ?>

<script>
    $(function () {
        _validate_form($('#venue-note'), {
            description: 'required'
        });

    });

    if (typeof(venueAttachmentDropzone) != 'undefined') {
        venueAttachmentDropzone.destroy();
    }
    venueAttachmentDropzone = new Dropzone("#venue_attachment", {
        autoProcessQueue: true,
        createImageThumbnails: false,

        dictDefaultMessage: appLang.drop_file_here_to_upload,
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
            formData.append("locid", '<?php echo $locaton->locid; ?>');
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
    venueAttachmentDropzone = new Dropzone("#loc_gallery", {
        autoProcessQueue: true,
        createImageThumbnails: false,

        dictDefaultMessage: appLang.drop_image_here_to_upload,
        dictFallbackMessage: appLang.browser_not_support_drag_and_drop,
        dictFileTooBig: appLang.file_exceeds_maxfile_size_in_form,
        dictCancelUpload: appLang.cancel_upload,
        dictMaxFilesExceeded: appLang.you_can_not_upload_any_more_files,
        maxFilesize: (max_php_ini_upload_size_bytes / (1024 * 1024)).toFixed(0),
        maxFiles: 1,
        acceptedFiles: app_allowed_gallery,
        error: function (file, response) {
            alert_float('danger', response);
        },
        sending: function (file, xhr, formData) {
            formData.append("venueid", '<?php echo $venueid; ?>');
            formData.append("locid", '<?php echo $locaton->locid; ?>');
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

<script type="text/javascript">
    $('#locgallery').lightGallery({
        subHtmlSelectorRelative: true,
        selector: '.item',
        download: false,
        share: false,
    });
</script>
</body>
</html>
