<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 11-04-2018
 * Time: 04:14 PM
 */
?>
<div class="">
    <div class="panel-body" id="unique_lead_pipeline_widget">

        <div class="row leads-overview">
            <div class=" mbot10 posrel">
                <h4 class="no-margin pull-left">Pipeline</h4>

                <a href="#" data-toggle="modal" data-target="#lead_pipeline_setting"
                   class="toggle_set_button"><i class="fa fa-cog menu-icon"></i></a>
                <a href="javascript:void(0)" class="toggle_control_cutton" id="lead_pipeline_collapse" data-pid="#unique_lead_pipeline_widget"><i
                            class="fa fa-caret-up"></i></a>
            </div>

            <div class="panel_s col-md-12 widget-body clearfix" >
                <div class="col-md-12" id="lead_pipeline_data">
                    <?php
                    $where_not_admin = '(addedfrom = ' . get_staff_user_id() . ' OR assigned=' . get_staff_user_id() . ' OR is_public = 1)';
                    $numStatuses = count($statuses);
                    $is_admin = is_admin();
                    $processwidth = 100 / $numStatuses;
                    foreach ($statuses as $status) { ?>
                        <div class="process-step"
                             style="width:<?php echo $processwidth . '%'; ?>">
                            <?php
                            $this->db->where('status', $status['id']);
                            $this->db->where('deleted = ', 0);
                            $this->db->where('converted ', 0);

                            if (!$is_admin) {
                                $this->db->where($where_not_admin);
                            }
                            $total = $this->db->count_all_results('tblleads');
                            ?>
                            <a href="javascript:void(0)"
                               onclick="filterstatus('<?php echo $status['id']; ?>'); return false;">
                                <h3 class="bold"
                                    style="background-color:<?php echo $status['color']; ?>"><?php echo $total; ?></h3>
                                <span style="color:<?php echo $status['color']; ?>"><?php echo $status['name']; ?></span></a>
                        </div>
                    <?php } ?>
                    <?php
                    if (!$is_admin) {
                        $this->db->where($where_not_admin);
                    }
                    $total_leads = $this->db->count_all_results('tblleads');
                    ?>
                    <?php if ($is_admin) { ?>
                        <div class="col-md-2 col-xs-6">
                            <?php
                            $this->db->where('lost', 1);
                            if (!$is_admin) {
                                $this->db->where($where_not_admin);
                            }
                            $total_lost = $this->db->count_all_results('tblleads');
                            $percent_lost = ($total_leads > 0 ? number_format(($total_lost * 100) / $total_leads, 2) : 0);
                            ?>
                            <h3 class="bold"><?php echo $percent_lost; ?>%</h3>
                            <span class="text-danger"><?php echo _l('lost_leads'); ?></span>
                        </div>
                        <div class="col-md-2 col-xs-6">
                            <?php
                            $this->db->where('junk', 1);
                            if (!$is_admin) {
                                $this->db->where($where_not_admin);
                            }
                            $total_junk = $this->db->count_all_results('tblleads');
                            $percent_junk = ($total_leads > 0 ? number_format(($total_junk * 100) / $total_leads, 2) : 0);
                            ?>
                            <h3 class="bold"><?php echo $percent_junk; ?>%</h3>
                            <span class="text-danger"><?php echo _l('junk_leads'); ?></span>
                        </div>
                    <?php } ?>
                </div>
            </div> </div>

    </div>
</div>
<div class="modal fade" id="lead_pipeline_setting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span>Lead Pipeline Setting</span>
                </h4>
            </div>
            <div class="modal-body">
                <?php if (has_permission('items', '', 'create')) { ?>
                    <div class="row">
                        <form action="<?php echo admin_url()?>home/dashboard_widget_setting" novalidate="1" id="lead_pipeline_setting_form" method="post"
                              accept-charset="utf-8">
                            <div id="additional"></div>
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="checkbox radio-primary radio-inline">
                                        <input type="checkbox" id="dashboard_lead_pipeline" name="widget_visibility" class="checkbox task" value="1">
                                        <label for="dashboard_lead_pipeline">Hide</label>
                                    </div>
                                </div>
                            </div>
                    </div>

                <?php } ?>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="widget" value="lead_pipeline">
                <input type="hidden" name="user" value="<?php echo get_staff_user_id();?>">
                <button type="submit" class="btn btn-info" id="save_setting"><?php echo _l('Save'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>