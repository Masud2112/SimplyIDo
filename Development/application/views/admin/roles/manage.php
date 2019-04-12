<?php init_head(); ?>
<div id="wrapper">
    <div class="content role-manage-page">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span>Roles</span>
                </div>
                <h1 class="pageTitleH1"><i class="fa fa-tasks"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <?php if (has_permission('account_setup', '', 'create')){ ?>
                            <div class="">
                                <a href="<?php echo admin_url('roles/role'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_role'); ?></a>
                            </div>
                        <?php } ?>
                        <?php if(is_mobile()) { echo '<a class="btn btn-primary  filter_btn_search"><i class="glyphicon glyphicon-search"></i></a>'; } ?>
                        <div class="clearfix"></div>
                        <?php render_datatable(array(
                            _l('roles_dt_name'),
                            _l('roles_total_users'),
                            _l('')
                        ),'roles'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    var notSortable = $('.table-roles').find('th').length - 1;
    initDataTable('.table-roles', window.location.href, [1], notSortable);
</script>
</body>
</html>
