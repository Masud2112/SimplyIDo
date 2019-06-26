<?php

init_head();
if (isset($_GET['pid'])) {
    $url = admin_url('leadcaptureforms/form') . "?pid=" . $_GET['pid'];
} else {
    $url = admin_url('leadcaptureforms/form');
}
?>
<div id="wrapper">
    <div class="content questionnaire">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons inline-block datatable">
                            <a href="#" data-toggle="modal" data-target="#tasks_bulk_actions"
                               class="bulk-actions-btn bulk_act_btn btn btn-info"
                               data-table=".table-tasks"><?php echo _l('bulk_actions'); ?></a>
                            <a href="<?php echo $url; ?>"
                               class="btn btn-info"><?php echo _l('new_form'); ?></a>
                        </div>
                        <div class="pull-right">
                            <?php
                            $list = $card = "";
                            if (isset($switch_forms_kanban) && $switch_forms_kanban == 1) {
                                $list = "selected disabled";
                            } else {
                                $card = "selected disabled";
                            } ?>

                            <?php if (is_mobile()) {
                                echo '<a class="btn btn-primary  filter_btn_search"><i class="glyphicon glyphicon-search"></i></a>';
                            } ?>
                            <!--<a class="btn btn-primary filter_btn"><i class="fa fa-filter"></i></a>-->
                            <a href="<?php echo admin_url('leadcaptureforms/switch_forms_kanban/'); ?>"
                               class="btn btn-primary viewchangeBtn hidden-xs <?php echo $list ?>">
                                <?php echo _l('switch_to_list_view'); ?>
                            </a>
                            <a href="<?php echo admin_url('leadcaptureforms/switch_forms_kanban/1'); ?>"
                               class="btn btn-primary viewchangeBtn hidden-xs <?php echo $card ?>">
                                <?php echo _l('projects_switch_to_kanban'); ?>
                            </a>
                        </div>
                        <div class="clearfix"></div>
                        <?php if ($this->session->has_userdata('forms_kanban_view') && $this->session->userdata('forms_kanban_view') == 'true') { ?>
                            <div class="active kan-ban-tab" id="kan-ban-tab" >
                                <div class="row">
                                    <div class="container-fluid projects-kan-ban">
                                        <div id="kan-ban"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } else { ?>
                            <?php render_datatable(array(
                                '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="leadcaptureforms"><label></label></div>',
                                _l('form_name'),
                                _l('form_lastactionby'),
                                _l('form_displaymethod'),
                                _l('')
                            ), 'leadcaptureforms'); ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>

    leadcaptureforms_kanban();

    var notSortable = $('.table-leadcaptureforms').find('th').length - 1;
    //initDataTable(selector, url, notsearchable, notsortable, fnserverparams, defaultorder)
    initDataTable('.table-leadcaptureforms', window.location.href, [], [notSortable, 0, 3], 'undefined', [0, 'DESC']);
</script>
</body>
</html>
