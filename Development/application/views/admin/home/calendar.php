<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 11-04-2018
 * Time: 04:21 PM
 */
?>

<div class="">
    <div class="panel-body" id="unique_calendar_widget">
        <div class="row">
            <div class="col-md-12 mbot10  posrel">
                <div class="handle"><img src="<?php echo site_url()?>assets\images\dragger.png" alt="dragger"></div>
                <h4 class="no-margin pull-left mbot10">Calendar</h4>
                <a href="#" data-toggle="modal" data-target="#calendar_setting"
                   class="toggle_set_button"><i class="fa fa-cog menu-icon"></i></a>
                <a href="javascript:void(0)" class="toggle_control_cutton" id="calendar_collapse" data-pid="#unique_calendar_widget"><i class="fa fa-caret-up"></i></a>
                <?php
                $userdata = $this->session->userdata();
                if($userdata['package_type_id']!=2){ ?>
                    <a href="javascript:void(0)" data-toggle="modal" data-target="#dashboard_create_list" class="dashboard_action_button" id="dashboard_action_button"><i class="fa fa-plus menu-icon"></i></a>
                <?php } ?>

            </div>
        </div>

       
        <div class="panel_s widget-body clearfix" id="calendar_data">
            <div id="dashboard_calendar"></div>
            <div id="event_list"></div>
            <div class="col-md-12"></div>
        </div>
    </div>
</div>

<div class="modal fade" id="calendar_setting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span>Calendar Setting</span>
                </h4>
            </div>
            <div class="modal-body">
                <?php if (has_permission('items', '', 'create')) { ?>
                    <div class="row">
                        <form action="<?php echo admin_url()?>home/dashboard_widget_setting" novalidate="1" id="calendar_setting_form" method="post"
                              accept-charset="utf-8">
                            <div id="additional"></div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="checkbox radio-primary radio-inline">
                                        <input type="checkbox" id="dashboard_calendar" name="widget_visibility" class="checkbox task" value="1">
                                        <label for="dashboard_calendar">Hide</label>
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
                <input type="hidden" name="widget" value="calendar">
                <input type="hidden" name="user" value="<?php echo get_staff_user_id();?>">
                <button type="submit" class="btn btn-info" id="save_setting"><?php echo _l('Save'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>