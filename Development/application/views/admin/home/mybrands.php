<?php
if (isset($brands)) { ?>
    <div class="panel-body" id="unique_pinned_contact_widget">
        <div class="row brands">
            <div class="col-md-12 mbot10 posrel">
                <div class="handle"><img src="<?php echo site_url() ?>assets\images\dragger.png" alt="dragger"></div>
                <h4 class="no-margin pull-left">MY BRANDS</h4>
                <!--<a href="#" data-toggle="modal" data-target="#contacts_setting"
                   class="toggle_set_button"><i class="fa fa-cog menu-icon"></i></a>-->
                <a href="javascript:void(0)" class="toggle_control_cutton" id="pinned_contacts_collapse"
                   data-pid="#unique_pinned_contact_widget">
                    <i class="fa fa-caret-up"></i>
                </a>
            </div>
        </div>
        <div class="panel_s widget-body clearfix" id="pinned_contacts_data">
            <div class="row">
                <div class="col-sm-6">
                    <div class="brandinner currentbrand text-center">
                        <div class="brandbody">
                            <div class="brandimage"><?php get_brand_icon_img(get_user_session()); ?></div>
                            <div class="brandname"><?php echo get_brand_details(get_user_session())->name; ?></div>
                        </div>
                        <div class="brandfooter"><?php echo _l('current_brand') ?></div>
                    </div>
                </div>
                <?php foreach ($brands as $brand) {
                    $brandid = $brand['brandid'];
                    $brandname = $brand['name'];
                    $event = get_brand_notification('leads', $brandid);
                    $messages = get_brand_notification('messages', $brandid);
                    $meetings = get_brand_notification('meetings', $brandid);
                    $invites = get_brand_notification('invites', $brandid);
                    $files = get_brand_notification('files', $brandid);
                    $tasks = get_brand_notification('tasks', $brandid);
                    if ($brandid != get_user_session()) {
                        ?>
                        <div class="col-sm-6">
                            <div class="brandinner text-center">
                                <div class="brandbody">
                                    <div class="brandimage"><?php get_brand_icon_img($brandid); ?></div>
                                    <div class="brandname"><?php echo $brandname; ?></div>
                                </div>
                                <div class="brandfooter">
                                    <a data-id="<?php echo $brandid ?>"
                                       data-page="leads"
                                       href="<?php echo admin_url('leads'); ?>" class="summary"
                                       title="Leads">
                                        <i class="fa fa-tty"></i>
                                        <?php if ($event > 0) { ?>
                                            <span class="count"><?php echo $event ?></span>
                                        <?php } ?>
                                    </a>
                                    <a data-id="<?php echo $brandid ?>"
                                       data-page="invites"
                                       href="<?php echo admin_url('invites'); ?>" class="summary"
                                       title="Invites"><i class="fa fa-envelope-open"></i>
                                        <?php if ($invites > 0) { ?>
                                            <span class="count"><?php echo $invites ?></span>
                                        <?php } ?>
                                    </a>
                                    <a data-id="<?php echo $brandid ?>"
                                       data-page="tasks"
                                       href="<?php echo admin_url('tasks'); ?>" class="summary"
                                       title="Tasks"><i class="fa fa-tasks"></i>
                                        <?php if ($tasks > 0) { ?>
                                            <span class="count"><?php echo $tasks ?></span>
                                        <?php } ?>
                                    </a>
                                    <a data-id="<?php echo $brandid ?>"
                                       data-page="files"
                                       href="<?php echo admin_url('files?lp=1'); ?>" class="summary"
                                       title="files"><i class="fa fa-folder-open"></i>
                                        <?php if ($files > 0) { ?>
                                            <span class="count"><?php echo $files ?></span>
                                        <?php } ?>
                                    </a>
                                    <a data-id="<?php echo $brandid ?>"
                                       data-page="meetings"
                                       href="<?php echo admin_url('meetings'); ?>" class="summary"
                                       title="Meetings"><i class="fa fa-comments"></i>
                                        <?php if ($meetings > 0) { ?>
                                            <span class="count"><?php echo $meetings ?></span>
                                        <?php } ?>
                                    </a>
                                    <a data-id="<?php echo $brandid ?>"
                                       data-page="messages"
                                       href="<?php echo admin_url('messages'); ?>" class="summary"
                                       title="Messages"><i class="fa fa-envelope"></i>
                                        <?php if ($messages > 0) { ?>
                                            <span class="count"><?php echo $messages ?></span>
                                        <?php } ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php }
                } ?>
            </div>
        </div>
    </div>
<?php }