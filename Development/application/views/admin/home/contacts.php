<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 11-04-2018
 * Time: 04:27 PM
 */
/**/
$widget_setting = json_decode($widget_data->widget_settings, true);
if (isset($widget_setting['contacts'])) {
    $widget_setting = $widget_setting['contacts'];
}
$items = isset($widget_setting['items']) ? $widget_setting['items'] : 5;
?>
<div class="">
    <div class="panel-body" id="unique_pinned_contact_widget">
        <div class="row">
            <div class="col-md-12 mbot10 posrel">
                <div class="handle"><img src="<?php echo site_url()?>assets\images\dragger.png" alt="dragger"></div>
                <h4 class="no-margin pull-left">Pinned contacts</h4>
                <a href="#" data-toggle="modal" data-target="#contacts_setting"
                   class="toggle_set_button"><i class="fa fa-cog menu-icon"></i></a>
                <a href="javascript:void(0)" class="toggle_control_cutton" id="pinned_contacts_collapse"
                   data-pid="#unique_pinned_contact_widget">
                    <i class="fa fa-caret-up"></i>
                </a>
            </div>
        </div>

        <div class="panel_s widget-body clearfix" id="pinned_contacts_data">
            <div class="navbar navbar-light bg-faded no_bot_margin">
                <ul class="nav nav-tabs contact_list">
                    <li class="active"><a class="nav-item nav-link active"
                                          data-toggle="tab" href="#all">All
                            (<span class="all_pin_cont_count"><?php echo count($pinned_contact_data) + count($pinned_venues_data) ?></span>)</a>
                    </li>
                    <li><a class="nav-item nav-link" data-toggle="tab"
                           href="#contacts">Contacts (<span
                                    class="contact_count"><?php echo count($pinned_contact_data) ?></span>)</a>
                    </li>
                    <li><a class="nav-item nav-link" data-toggle="tab"
                           href="#venues">Venues (<span
                                    class="venue_count"><?php echo count($pinned_venues_data) ?></span>)</a>
                    </li>
                </ul>
            </div>

            <div class="tab-content contact_list_content">
                <div class="tab-pane active" id="all" data-item="<?php echo $items; ?>">
                    <div class="pin_contact_data_container">
                        <?php
                        if (!empty($pinned_contact_data) || !empty($pinned_venues_data)) {
                            foreach ($pinned_contact_data as $all_pin_con_key => $contact_data) {
                                $phone = str_replace('(', "", $contact_data['primary_phone']);
                                $phone = str_replace(')', "", $phone);
                                $phone = str_replace(' ', "", $phone);
                                $phone = str_replace('-', "", $phone);
                                ?>
                                <div class="row lazy_content contacts pinned_all_contact_list_content contact_<?php echo $contact_data['addressbookid']; ?>"
                                     id="allcontact_<?php echo $all_pin_con_key; ?>">
                                    <div class="col-sm-1 col-md-1 col-lg-1 col-xs-2">
                                        <i class="fa fa-fw fa-thumb-tack contact-pin pinned list_pin_icon"
                                           title="Unpin from dashboard"
                                           id="<?php echo $contact_data['addressbookid'] ?>"
                                           contact_id="<?php echo $contact_data['addressbookid'] ?>"></i>
                                    </div>
                                    <div class="col-sm-1 col-md-2 col-lg-1 col-xs-2">
                                        <div class="project-pimg">
                                            <?php echo addressbook_profile_image($contact_data['pintypeid'], array('addressbook-profile-image-small')); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-10 col-md-9 col-lg-10 col-xs-8">
                                        <div class="task-det">
                                            <div class="item-name"><?php echo $contact_data['firstname'] . " " . $contact_data['lastname']; ?></div>
                                            <div class="project_list_time"><?php echo $contact_data['primary_email']; ?></div>
                                            <div class="contact-options pull-right">
                                                <a href="javascript:void(0)" class="popooveContact"
                                                   data-popover-content="#contact_myPopover_<?php echo $contact_data['addressbookid']; ?>">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </a>
                                                <div id="contact_myPopover_<?php echo $contact_data['addressbookid']; ?>"
                                                     class="contactPopover hide">
                                                    <a href="tel:<?php echo $phone ?>"
                                                       class="btn btn-info inline-block"><i class="fa fa-phone "></i>
                                                        CALL</a>
                                                    <a href="<?php echo admin_url('messages/message') ?>?to=<?php echo $contact_data['addressbookid'] ?>"
                                                       class="btn btn-info inline-block"><i
                                                                class="fa fa-envelope-o"></i> MESSAGE</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            foreach ($pinned_venues_data as $all_pin_ven_key => $venue_data) {
                                ?>
                                <div class="row lazy_content contacts pinned_all_contact_list_content only_ven_sec venue_<?php echo $venue_data['venueid']; ?>"
                                     id="allvenue_<?php echo $all_pin_ven_key; ?>">
                                    <div class="col-sm-1 col-md-1 col-lg-1 col-xs-2">
                                        <i class="fa fa-fw fa-thumb-tack venue-pin pinned list_pin_icon"
                                           title="Unpin from dashboard"
                                           id="<?php echo $venue_data['venueid']; ?>"
                                           venue_id="<?php echo $venue_data['venueid']; ?>"></i>
                                    </div>
                                    <div class="col-sm-1 col-md-2 col-lg-1 col-xs-2">
                                        <div class="project-pimg">
                                            <?php echo venue_logo_image($venue_data['pintypeid'], array('venue-image-small profImgDiv')); ?>
                                        </div>
                                    </div>
                                    <div class="col-sm-10 col-md-9 col-lg-10 col-xs-8">
                                        <div class="task-det">
                                            <div class="item-name"><?php echo $venue_data['venuename']; ?></div>
                                            <div class="project_list_time">
                                                <?php
                                                if (!empty($venue_data['venueemail'])) {
                                                    if (is_serialized($venue_data['venueemail'])) {
                                                        $venue_data['venueemail'] = unserialize($venue_data['venueemail']);
                                                        $venue_data['venueemail'] = $venue_data['venueemail'][0]['email'];
                                                    }
                                                }
                                                echo $venue_data['venueemail']; ?></div>
                                            <!--<div class="contact-options pull-right">
                                                <a href="javascript:void(0)" class="popooveContact" data-popover-content="#contact_myPopover_<?php /*echo $contact_data['addressbookid']; */ ?>">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </a>
                                                <div id="contact_myPopover_<?php /*echo $contact_data['addressbookid']; */ ?>" class="contactPopover hide">
                                                    <a href="tel:<?php /*echo $venue_data['primary_phone']*/ ?>" class="btn btn-info inline-block"><i class="fa fa-phone "></i> CALL</a>
                                                    <a href="<?php /*echo admin_url('messages/message')*/ ?>?to=<?php /*echo $venue_data['addressbookid']*/ ?>" class="btn btn-info inline-block"><i class="fa fa-envelope-o"></i> MESSAGE</a>
                                                </div>
                                            </div>-->
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } else { ?>
                            <div class="row ">
                                No pinned contacts/venues found!
                            </div>
                        <?php } ?>

                        <div class="pinned_item_button_section">
                            <?php $totaL_pin_contact = count($pinned_contact_data) + count($pinned_venues_data);
                            if ($totaL_pin_contact > $items) { ?>
                                <!-- <div class="pinned_item_button_section"> -->
                                <!-- <a href="#" id="all_pinned_contact_loadMore" class="btn btn-info loadMore">Load More</a> -->
                                <a href="javascript:;" id="all_pinned_contact_loadMore" class="btn btn-info loadMore"
                                   data-widget="contacts" data-pid="#all" data-item="<?php echo $items; ?>"><i
                                            class="fa far fa-eye mright5"></i>(<span
                                            class="all_master_list_count"><?php echo count($pinned_contact_data) + count($pinned_venues_data) ?></span>)
                                    Pinned</a>
                                <a href="javascript:;" id="all_pinned_contact_loadless" data-widget="contacts"
                                   data-pid="#all" data-item="<?php echo $items; ?>" class="btn btn-info loadless"><i
                                            class="fa far fa-eye mright5"></i>Show Less</a>
                                <!-- </div> -->
                            <?php } ?>

                            <a href="<?php echo admin_url('addressbooks?pg=home'); ?>"
                               class="btn btn-info"><i
                                        class="fa fas fa-user mright5"></i>Contacts</a>
                        </div>

                    </div>
                </div>
                <div class="tab-pane" id="contacts" data-item="<?php echo $items; ?>">
                    <div class="pin_contact_data_container">
                        <?php if (!empty($pinned_contact_data)) { ?>
                            <?php
                            foreach ($pinned_contact_data as $c_key => $contact_data) {
                                $phone = str_replace('(', "", $contact_data['primary_phone']);
                                $phone = str_replace(')', "", $phone);
                                $phone = str_replace(' ', "", $phone);
                                ?>
                                <div class="row  lazy_content contacts pinned_contact_list_content contact_<?php echo $contact_data['addressbookid']; ?>"
                                     id="contact_<?php echo $c_key; ?>">
                                    <div class="col-md-1 col-xs-2">
                                        <i class="fa fa-fw fa-thumb-tack contact-pin pinned list_pin_icon"
                                           title="Unpin from dashboard"
                                           id="<?php echo $contact_data['addressbookid'] ?>"
                                           contact_id="<?php echo $contact_data['addressbookid'] ?>"></i>
                                    </div>
                                    <div class="col-md-2 col-xs-2">
                                        <div class="project-pimg">
                                            <?php echo addressbook_profile_image($contact_data['pintypeid'], array('addressbook-profile-image-small')); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-9 col-xs-8">
                                        <div class="task-det">
                                            <div class="item-name"><?php echo $contact_data['firstname'] . " " . $contact_data['lastname']; ?></div>
                                            <div class="project_list_time"><?php echo $contact_data['primary_email']; ?></div>
                                            <div class="contact-options pull-right">
                                                <a href="javascript:void(0)" class="popooveContact"
                                                   data-popover-content="#contact_myPopover_<?php echo $contact_data['addressbookid']; ?>">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </a>
                                                <div id="contact_myPopover_<?php echo $contact_data['addressbookid']; ?>"
                                                     class="contactPopover hide">
                                                    <a href="tel:<?php echo $phone ?>"
                                                       class="btn btn-info inline-block"><i class="fa fa-phone "></i>
                                                        CALL</a>
                                                    <a href="<?php echo admin_url('messages/message') ?>?to=<?php echo $contact_data['addressbookid'] ?>"
                                                       class="btn btn-info inline-block"><i
                                                                class="fa fa-envelope-o"></i> MESSAGE</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } else { ?>
                            <div class="row mbot15">
                                No pinned contacts found!
                            </div>
                        <?php } ?>


                        <div class="pinned_item_button_section">
                            <?php $totaL_pin_contact = count($pinned_contact_data) + count($pinned_venues_data);
                            if ($totaL_pin_contact > $items) { ?>
                                <!-- <div class="pinned_item_button_section"> -->
                                <!-- <a href="#" id="all_pinned_contact_loadMore" class="btn btn-info loadMore">Load More</a> -->
                                <a href="javascript:;" id="all_pinned_contact_only_loadMore"
                                   class="btn btn-info loadMore" data-pid="#contacts" data-item="<?php echo $items; ?>"
                                   data-widget="contacts"><i class="fa far fa-eye mright5"></i>(<span
                                            class="contact_count"><?php echo count($pinned_contact_data); ?></span>)
                                    Pinned</a>
                                <a href="javascript:;" id="all_pinned_contact_only_loadless" data-pid="#contacts"
                                   data-item="<?php echo $items; ?>" data-widget="contacts"
                                   class="btn btn-info loadless"><i class="fa far fa-eye mright5"></i>Show Less</a>
                                <!-- </div> -->
                            <?php } ?>
                            <a href="<?php echo admin_url('addressbooks'); ?>"
                               class="btn btn-info"><i
                                        class="fa fas fa-user mright5"></i>Contacts</a>
                        </div>


                    </div>
                </div>
                <div class="tab-pane" id="venues" data-item="<?php echo $items; ?>">
                    <div class="pin_contact_data_container">
                        <?php if (!empty($pinned_venues_data)) { ?>
                            <?php
                            foreach ($pinned_venues_data as $v_key => $venue_data) {
                                if (!empty($venue_data['venueemail'])) {
                                    if (is_serialized($venue_data['venueemail'])) {
                                        $venue_data['venueemail'] = unserialize($venue_data['venueemail']);
                                        $venue_data['venueemail'] = $venue_data['venueemail'][0]['email'];
                                    }
                                }
                                ?>
                                <div class="row lazy_content contacts pinned_venues_list_content venue_<?php echo $venue_data['venueid']; ?>"
                                     id="venue_<?php echo $v_key; ?>">
                                    <div class="col-md-1 col-xs-2">
                                        <i class="fa fa-fw fa-thumb-tack venue-pin pinned list_pin_icon"
                                           title="Unpin from dashboard"
                                           id="<?php echo $venue_data['venueid']; ?>"
                                           venue_id="<?php echo $venue_data['venueid']; ?>"></i>
                                    </div>
                                    <div class="col-md-2 col-xs-2">
                                        <div class="project-pimg">
                                            <?php echo venue_logo_image($venue_data['pintypeid'], array('venue-image-small profImgDiv')); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-9 col-xs-8">
                                        <div class="task-det">
                                            <div class="item-name"><?php echo $venue_data['venuename']; ?></div>
                                            <div class="project_list_time"><?php echo $venue_data['venueemail']; ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } else { ?>
                            <div class="mbot15">
                                No pinned venues found!
                            </div>
                        <?php } ?>

                        <div class="pinned_item_button_section">
                            <?php $totaL_pin_contact = count($pinned_venues_data);
                            if ($totaL_pin_contact > $items) { ?>
                                <a href="javascript:;" id="all_pinned_venues_only_loadMore"
                                   class="btn btn-info loadMore" data-pid="#venues" data-item="<?php echo $items; ?>"
                                   data-widget="contacts"><i class="fa far fa-eye mright5"></i>(<span
                                            class="venue_count"><?php echo count($pinned_venues_data); ?></span>) Pinned</a>
                                <a href="javascript:;" id="all_pinned_venues_only_loadless" data-pid="#venues"
                                   data-item="<?php echo $items; ?>" data-widget="contacts"
                                   class="btn btn-info loadless"><i class="fa far fa-eye mright5"></i>Show Less</a>
                            <?php } ?>
                            <a href="<?php echo admin_url('venues'); ?>" class="btn btn-info"><i
                                        class="fa fas fa-user mright5"></i>Venues</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="contacts_setting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span>Contacts Setting</span>
                </h4>
            </div>
            <div class="modal-body">
                <?php if (has_permission('items', '', 'create')) { ?>
                    <div class="row">
                        <form action="<?php echo admin_url() ?>home/dashboard_widget_setting" novalidate="1"
                              id="contacts_setting_form" method="post"
                              accept-charset="utf-8">
                            <div id="additional"></div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="checkbox radio-primary radio-inline">
                                        <input type="checkbox" id="dashboard_contacts" name="widget_visibility"
                                               class="checkbox task" value="1">
                                        <label for="dashboard_contacts">Hide</label>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Number of messages to display </label>
                                        <input type="number" name="items" class="form-control" min="5"
                                               value="<?php echo $items; ?>">
                                    </div>
                                </div>
                            </div>
                    </div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="widget" value="contacts">
                <input type="hidden" name="user" value="<?php echo get_staff_user_id(); ?>">
                <button type="submit" class="btn btn-info" id="save_setting"><?php echo _l('Save'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>