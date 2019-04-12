<?php
/**
 * Added By : Purvi
 * Dt : 01/12/2018
 * Addressbook details
 */
init_head();

$brandid = get_user_session();
$session_data = get_session_data();
$user_id = $session_data['staff_user_id'];
?>
<div id="wrapper" class="addressbookdashboard">
    <div class="content">
        <div class="breadcrumb">
            <?php /*if (isset($pg) && $pg == 'home') { */ ?>
            <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <?php /*} */
            $venueid = $this->input->get('venue');
            $vid = $this->input->get('vid');
            ?>

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
                    <a href="<?php echo admin_url('venues/view/') . $vnuid; ?>">
                        <?php echo get_vanue_data($vnuid)->venuename; ?>
                    </a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                <?php } elseif (isset($pid)) { ?>
                    <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('leads/dashboard/' . $pid); ?>"><?php echo($lname); ?></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('venues') . '?pid=' . $pid; ?>">Venues</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('venues/view/') . $vnuid; ?>"><?php echo get_vanue_data($vnuid)->venuename; ?></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                <?php } else { ?>
                    <a href="<?php echo admin_url('venues'); ?>">Venues</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('venues/view/') . $vnuid; ?>"><?php echo get_vanue_data($vnuid)->venuename; ?></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php if (isset($locid)) { ?>
                        <a href="<?php echo admin_url('venues/onsitelocview/' . $locid . '?venue=' . $vnuid); ?>">
                            <?php echo get_venueloc_data($locid)->locname; ?>
                        </a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } ?>
                    <span><?php echo ucfirst($addressbook->firstname) . " " . ucfirst($addressbook->lastname); ?></span>
                <?php } ?>
            <?php } else { ?>
                <?php if (isset($lid)) { ?>
                    <a href="<?php echo admin_url('leads/'); ?>">Leads</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('leads/dashboard/' . $lid); ?>"><?php echo($lname); ?></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('addressbooks') . '?lid=' . $lid; ?>">Contacts</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                <?php } elseif (isset($pid)) { ?>
                    <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('leads/dashboard/' . $pid); ?>"><?php echo($lname); ?></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('addressbooks') . '?pid=' . $pid; ?>">Contacts</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                <?php } else { ?>
                    <a href="<?php echo admin_url('addressbooks'); ?>">Contacts</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                <?php } ?>
                <span><?php echo isset($addressbook) ? ucfirst($addressbook->firstname) . " " . ucfirst($addressbook->lastname) : "New Conatct" ?></span>
            <?php } ?>
        </div>
        <h1 class="pageTitleH1"><i class="fa fa-address-book-o"></i><?php echo "View Contact"; ?></h1>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-md-12 widget-holder">
                <div class="row h-100 row-flex">
                    <div class="col-md-6 widget-holder">
                        <div class="titleRow">
                            <?php
                            /*echo "<pre>";
                            print_r($addressbook);
                            die();*/
                            $qstring = "";
                            if (isset($venueid)) {
                                $qstring = "?venue=" . $venueid;
                            }
                            ?>
                            <h4>Profile</h4>
                            <?php
                            if ((has_permission('addressbook', '', 'edit') && $addressbook->created_by == $user_id) || is_sido_admin()) { ?>
                                <a class="editlink onHoverShow"
                                   href="<?php echo admin_url('addressbooks/addressbook/' . $addressbook->addressbookid . $qstring) ?>"><i
                                            class="fa fa-pencil"></i><span class="mleft5">Edit Contact</span></span></a>
                            <?php } ?>
                            <div class="clearfix"></div>
                        </div>
                        <div class="card-user-info-widget panel_s btmbrd">
                            <div class="row">
                                <div class="favorite">
                                    <a href="javascript:void(0)"
                                       class="contact-fav <?php echo isset($favorite->favoriteid) ? "favorite" : "" ?>"
                                       title="<?php echo isset($favorite->favoriteid) ? "UnMark Favorite" : "Mark Favorite" ?>"
                                       contact_id="<?php echo $addressbookid ?>"><i
                                                class="fa fa-star<?php echo isset($favorite->favoriteid) ? "" : "-o" ?>"></i></a>
                                </div>
                                <div class="col-sm-12 card-user-info">
                                    <figure class="text-center thumb-lg">
                                        <div class="addressbook-profile_blk">
                                            <?php echo addressbook_profile_image($addressbookid, array('profile_image', 'img-responsive', 'addressbook-profile-image-thumb')); ?>
                                        </div>
                                        <a class="removelink"
                                           href="<?php echo admin_url('addressbooks/remove_addressbook_profile_image/' . $addressbook->addressbookid); ?>"
                                           class="display-block"><i
                                                    class="fa fa-remove"></i><span
                                                    class=""><?php echo _l('remove') ?></span></a>
                                    </figure>

                                    <div class="pull-right hide">
                                        <?php if (isset($lid)) { ?>
                                            <a data-toggle="tooltip" data-title="Back"
                                               href="<?php echo admin_url('addressbooks?lid=' . $lid); ?>"
                                               class="btn btn-default pull-right">
                                                <i class="fa fa-chevron-left"></i>
                                            </a>
                                        <?php } else if (isset($pid)) { ?>
                                            <a data-toggle="tooltip" data-title="Back"
                                               href="<?php echo admin_url('addressbooks?pid=' . $pid); ?>"
                                               class="btn btn-default pull-right">
                                                <i class="fa fa-chevron-left"></i>
                                            </a>
                                        <?php } else if (isset($eid)) { ?>
                                            <a data-toggle="tooltip" data-title="Back"
                                               href="<?php echo admin_url('addressbooks?eid=' . $eid); ?>"
                                               class="btn btn-default pull-right">
                                                <i class="fa fa-chevron-left"></i>
                                            </a>
                                        <?php } else { ?>
                                            <a data-toggle="tooltip" data-title="Back"
                                               href="<?php echo admin_url('addressbooks'); ?>"
                                               class="btn btn-default pull-right">
                                                <i class="fa fa-chevron-left"></i>
                                            </a>
                                        <?php } ?>
                                    </div>
                                    <div class="profile-container">
                                        <div class="profile-wrap">
                                            <div class="img-caption">
                                                <h4><?php echo $addressbook->firstname . " " . $addressbook->lastname; ?></h4>
                                                <h6><?php echo $addressbook->companytitle; ?></h6>
                                            </div>
                                            <div class="company-caption">
                                                <?php
                                                $tags = get_tags_in_addressbook($addressbook->tags_id);
                                                if (!empty($tags)) {
                                                    $tags_final = implode(", ", $tags);
                                                }
                                                if (($addressbook->company > 0)) { ?>
                                                    <h4><?php echo $addressbook->companyname; ?></h4>
                                                    <h6 class="hide"><?php echo $addressbook->companytitle; ?></h6>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="contacttags">
                                        <?php if (isset($tags_final)) { ?>
                                            <i class="list-icon fa fa-tags"></i>
                                            <?php $tags = explode(',', $tags_final);
                                            foreach ($tags as $tag) { ?>
                                                <span class="tags"><?php echo $tag; ?></span>
                                            <?php }
                                        } ?>
                                    </div>

                                </div>
                            </div>
                            <?php
                            if ($addressbook->ispublic == 1) {
                                $shared = "fa-unlock";
                            } else {
                                $shared = "fa-lock";
                            }
                            ?>
                            <span class="shared"><i class="fa <?php echo $shared ?> "></i><span
                                        class="mleft5">Shared</span></span>
                        </div>
                        <!-- /.card-user-info-widget -->
                    </div>
                    <div class="col-md-6 widget-holder">
                        <div class="titleRow">
                            <h4 class="">Connect</h4>
                            <?php
                            if ((has_permission('addressbook', '', 'edit') && $addressbook->created_by == $user_id) || is_sido_admin()) { ?>
                                <a class="editicon onHoverShow"
                                   href="<?php echo admin_url('addressbooks/addressbook/' . $addressbook->addressbookid) ?>"><i
                                            class="fa fa-pencil"></i></a>
                            <?php } ?>
                            <div class="clearfix"></div>
                        </div>
                        <div class="card-user-info-widget panel_s btmbrd connect-block">
                            <div class="row">
                                <div class="col-sm-12">
                                    <?php if (!empty($addressbook->email)) { ?>
                                        <div class="row mbot10">
                                            <div class="col-md-2 title">Email</div>
                                            <div class="col-md-10">
                                                <?php foreach ($addressbook->email as $email) { ?>
                                                    <div class="row mbot5">
                                                        <div class="col-md-2">
                                                            <b><?php echo ucfirst($email['type']) ?></b></div>
                                                        <div class="col-md-8"><a
                                                                    href="mailto:<?php echo $email['email'] ?>"><?php echo $email['email'] ?></a>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($addressbook->phone)) { ?>
                                        <div class="row mbot10">
                                            <div class="col-md-2 title">Phone</div>
                                            <div class="col-md-10">
                                                <?php foreach ($addressbook->phone as $phone) { ?>
                                                    <div class="row mbot5">
                                                        <div class="col-md-2">
                                                            <b><?php echo ucfirst($phone['type']) ?></b></div>
                                                        <div class="col-md-8">
                                                            <?php echo $phone['phone'];
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
                                    <?php if (!empty($addressbook->website)) { ?>
                                        <div class="row mbot10">
                                            <div class="col-md-2 title">Online</div>
                                            <div class="col-md-10">
                                                <?php foreach ($addressbook->website as $website) { ?>
                                                    <div class="row mbot5">
                                                        <div class="col-md-2">
                                                            <b><?php echo ucfirst($website['name']) ?></b></div>
                                                        <div class="col-md-8"><a
                                                                    href="<?php echo strpos('http', $website['url']) > 0 ? $website['url'] : 'http://' . $website['url'] ?>"
                                                                    target="_blank"><?php echo $website['url'] ?></a>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="preferred-block">
                                    <b>Preferred Mode of Communication:</b>
                                    <?php
                                    if (!empty($addressbook->mode_of_communication)) {
                                        $modes_of_communication = explode(',', $addressbook->mode_of_communication);
                                        foreach ($modes_of_communication as $mode_of_communication) {
                                            if ($mode_of_communication == "text") {
                                                $com_icon = "fa-comment-o";
                                            } elseif ($mode_of_communication == "email") {
                                                $com_icon = "fa-envelope-o";
                                            } elseif ($mode_of_communication == "phone") {
                                                $com_icon = "fa-phone";
                                            }
                                            ?>
                                            <div class="moc"><i
                                                        class="fa <?php echo $com_icon ?>"></i><span><?php echo ucfirst($mode_of_communication); ?></span>
                                            </div>
                                        <?php }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-user-info-widget -->
                    </div>
                </div>
                <div class="row row-flex">
                    <?php if (!empty($addressbook->address)) { ?>

                        <div class="col-md-6 widget-holder address-block">
                            <div class="titleRow">
                                <h4>Address</h4>
                                <?php
                                if ((has_permission('addressbook', '', 'edit') && $addressbook->created_by == $user_id) || is_sido_admin()) { ?>
                                    <a class="editicon onHoverShow"
                                       href="<?php echo admin_url('addressbooks/addressbook/' . $addressbook->addressbookid) ?>"><i
                                                class="fa fa-pencil"></i></a>
                                <?php } ?>
                                <div class="clearfix"></div>
                            </div>
                            <div class="card-user-info-widget panel_s btmbrd">


                                <?php foreach ($addressbook->address as $address) {
                                    $final_array = array();
                                    $final_array['address'] = $address['address'];
                                    $final_array['address2'] = $address['address2'];
                                    $final_array['city'] = $address['city'];
                                    $final_array['state'] = $address['state'];
                                    $final_array['zip'] = $address['zip'];
                                    $final_array['country'] = "US.";
                                    $final_array = array_filter($final_array);
                                    $final_address = implode(", ", $final_array);
                                    $latlang = getLatLong($final_address);
                                    $lat = $latlang['latitude'];
                                    $lang = $latlang['longitude'];
                                    $maplink = "http://maps.google.co.uk/maps?q=" . $lat . "," . $lang;
                                    ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <b><?php echo ucfirst($address['type']) ?></b>
                                            <p><?php echo $final_address; ?></p>
                                            <div class="address_link">
                                                <a href="<?php echo $maplink; ?>" target="_blank">
                                                    <i class="fa fa-car" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                    <?php }
                    /**
                     * Added By : Masud
                     * Dt : 07/03/2017
                     * For Note functionality in contact dashboard
                     */
                    $len = count($notes);
                    $i = 0;
                    ?>
                    <div class="col-md-6 widget-holder notes-block ">
                        <div class="titleRow">
                            <h4>Notes</h4>

                            <?php if ($len > 0) { ?>
                                <a href="javascript:void(0)" class="new_contact_note onHoverShow"><i
                                            class="fa fa-plus"></i></a>
                            <?php } ?>
                            <div class="clearfix"></div>
                        </div>
                        <div class="card-user-info-widget panel_s btmbrd">
                            <div class="col-sm-12">
                                <?php
                                $noteFormclass = "";
                                if ($len > 0) {
                                    $noteFormclass = "hideForm";
                                } ?>
                                <?php echo form_open(admin_url('addressbooks/add_note/' . $addressbook->addressbookid . $qstring), array('id' => 'addressbook-notes', 'class' => $noteFormclass)); ?>
                                <?php echo render_textarea('description'); ?>
                                <input type="hidden" name="hdnlid"
                                       value="<?php echo isset($lid) ? $lid : ''; ?>">
                                <input type="hidden" name="hdnpid"
                                       value="<?php echo isset($pid) ? $pid : ''; ?>">
                                <input type="hidden" name="hdneid"
                                       value="<?php echo isset($eid) ? $eid : ''; ?>">
                                <input type="hidden" name="pg" value="<?php echo isset($pg) ? $pg : ''; ?>">
                                <button type="submit"
                                        class="btn btn-info pull-right"><?php echo _l('save note'); ?></button>
                                <button class="btn btn-default pull-right contact_note_cancel mright5"><?php echo _l('cancel'); ?></button>
                                <div class="clearfix"></div>
                                <?php echo form_close(); ?>
                                <div class="panel_s mtop5">
                                    <?php
                                    if ($len > 0) {
                                        foreach ($notes as $note) { ?>
                                            <div class="media meeting-note">
                                                <!--<a href="<?php //echo admin_url('profile/'.$note["addedfrom"]); ?>" target="_blank">-->
                                                <?php echo staff_profile_image($note['addedfrom'], array('staff-profile-image-small', 'pull-left mright10')); ?>
                                                <!--</a>-->
                                                <div class="user-notes">
                                                    <?php if ($note['addedfrom'] == get_staff_user_id() || is_admin()) { ?>
                                                        <div class="pull-right text-right">
                                                            <div><a class='show_act' href='javascript:void(0)'><i
                                                                            class='fa fa-ellipsis-v'
                                                                            aria-hidden='true'></i></a></div>
                                                            <div class='table_actions'>
                                                                <ul>
                                                                    <li>
                                                                        <a href="<?php echo admin_url('addressbooks/delete_note/' . $note['id']); ?>" class="_delete">
                                                                            <i class="fa fa fa-times"></i>Delete</a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="#"
                                                                           onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;">
                                                                            <i class="fa fa-pencil-square-o"></i>Edit</a>
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
                                        }
                                    } ?>
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
        _validate_form($('#addressbook-notes'), {
            description: 'required'
        });

    });
</script>
</body>
</html>