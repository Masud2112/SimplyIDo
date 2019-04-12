<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 11-04-2018
 * Time: 04:16 PM
 */
?>
<div class="">
    <div class="panel-body" id="unique_quick_links_widget">
        <div class="row">
            <div class="col-md-12 mbot10 posrel">
                <div class="handle"><img src="<?php echo site_url()?>assets\images\dragger.png" alt="dragger"></div>
                <h4 class="no-margin pull-left">Quick links</h4>
                <a href="#" data-toggle="modal" data-target="#quick_links_setting"
                   class="toggle_set_button"><i class="fa fa-cog menu-icon"></i></a>
                <a href="javascript:void(0)" class="toggle_control_cutton" id="quick_link_collapse" data-pid="#unique_quick_links_widget"><i
                            class="fa fa-caret-up"></i></a>
            </div>
        </div>    
            <div class="panel_s widget-body clearfix" id="quick_link_data">
                <div class="form-group quick_link_container">
                    <?php
                    if (in_array('lead', $single_quick_link_list)) {
                        if (has_permission('leads', '', 'create', true)) { ?>
                            <div class="col-lg-3 col-sm-4 col-xs-6">
                                <div class="quickBlock lead_section">
                                    <a href="<?php echo admin_url('leads/?pg=home') ?>">
                                        <span><?php echo $ql_total_lead_count['lead_count']; ?></span>
                                        Active Leads
                                    </a>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>

                    <?php
                    if (in_array('project', $single_quick_link_list)) {
                        if (has_permission('projects', '', 'create', true)) { ?>
                            <div class="col-lg-3 col-sm-4 col-xs-6 ">
                                <div class="quickBlock project_section">
                                    <a href="<?php echo admin_url('projects/?pg=home') ?>">
                                        <span><?php echo $ql_total_lead_count['project_count']; ?></span>
                                        Active Projects</a>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>

                    <?php
                    if (in_array('message', $single_quick_link_list)) {
                        if (has_permission('messages', '', 'create', true)) { ?>
                            <div class="col-lg-3 col-sm-4 col-xs-6 ">
                                <div class="quickBlock message_section">
                                    <a href="<?php echo admin_url('messages/?pg=home') ?>">
                                        <span><?php echo $ql_total_lead_count['message_count'] ?></span>
                                        Unread Messages</a>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>

                    <?php
                    if (in_array('task_due', $single_quick_link_list)) {
                        if (has_permission('tasks', '', 'create', true)) { ?>
                            <div class="col-lg-3 col-sm-4 col-xs-6 ">
                                <div class="quickBlock task_due_section">
                                    <a href="<?php echo admin_url('tasks/?pg=home') ?>">
                                        <span><?php echo $ql_total_lead_count['task_count']; ?></span>
                                        Tasks due</a>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>

                    <?php
                    if (in_array('meeting', $single_quick_link_list)) {
                        if (has_permission('meetings', '', 'create', true)) { ?>
                            <div class="col-lg-3 col-sm-4 col-xs-6 ">
                                <div class="quickBlock meeting_section">
                                    <a href="<?php echo admin_url('meetings/?pg=home') ?>">
                                        <span><?php echo $ql_total_lead_count['meeting_count']; ?></span>
                                        Meetings</a>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>

                    <?php
                    if (in_array('amount_receivable', $single_quick_link_list)) { ?>
                        <div class="col-lg-3 col-sm-4 col-xs-6 ">
                            <div class="quickBlock amt_rcvble_section">
                                <span> $0 </span>
                                 Receivable
                            </div>
                        </div>
                    <?php } ?>

                    <?php
                    if (in_array('amount_received', $single_quick_link_list)) { ?>
                        <div class="col-lg-3 col-sm-4 col-xs-6 ">
                            <div class="quickBlock amt_recv_section">
                                <span> $0 </span>
                                 Received
                            </div>
                        </div>
                    <?php } ?>

                    <?php
                    if (in_array('invite', $single_quick_link_list)) {
                        if (has_permission('projects', '', 'create', true)) { ?>
                            <div class="col-lg-3 col-sm-4 col-xs-6">
                                <div class="quickBlock invites_section">
                                    <a href="<?php echo admin_url('projects/invites/?pg=home') ?>">
                                        <span><?php echo $ql_total_lead_count['invite_count']; ?></span>
                                        Invites</a>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        
    </div>
</div>
<div class="modal fade" id="quick_links_setting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span>Quick Links Setting</span>
                </h4>
            </div>
            <div class="modal-body">
                <?php if (has_permission('items', '', 'create')) { ?>
                    <div class="row">
                        <form action="<?php echo admin_url()?>home/dashboard_widget_setting" novalidate="1" id="quick_links_setting_form" method="post"
                              accept-charset="utf-8">
                            <div id="additional"></div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="checkbox radio-primary radio-inline">
                                        <input type="checkbox" id="dashboard_quick_links" name="widget_visibility" class="checkbox task" value="1">
                                        <label for="dashboard_quick_links">Hide</label>
                                    </div>
                                </div>
                                <!--<div class="col-md-12">
                                    <div class="selectdays">
                                        <select class="selectpicker selectdays">
                                            <option value="3">In Next 5 Days</option>
                                            <option value="4">In Next 10 Days</option>
                                            <option value="5">In Next 15 Days</option>
                                            <option value="6">In Next 20 Days</option>
                                            <option value="7">In Next 25 Days</option>
                                        </select>
                                    </div>
                                </div>-->
                            </div>
                    </div>

                <?php } ?>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="widget" value="quick_link">
                <input type="hidden" name="user" value="<?php echo get_staff_user_id();?>">
                <button type="submit" class="btn btn-info" id="save_setting"><?php echo _l('Save'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>