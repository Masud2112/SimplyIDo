<?php init_head(); ?>
<div id="wrapper" class="agreement-manage-page">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span>Agreements</span>
                </div>
                <h1 class="pageTitleH1"><i class="fa fa-files-o"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div>
                            <?php if (has_permission('agreements','','create')) { ?>
                                <a href="<?php echo admin_url('agreements/agreement'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_agreement'); ?></a>
                            <?php } ?>
                        </div>
                        <?php if(is_mobile()) { echo '<a class="btn btn-primary  filter_btn_search"><i class="glyphicon glyphicon-search"></i></a>'; } ?>
                        <div class="clearfix"></div>
                        <?php render_datatable(array(
                            _l('agreements_dt_name'),
                            _l('agreements_created'),
                            _l('')
                        ),'agreements'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    var notSortable = $('.table-agreements').find('th').length - 1;
    initDataTable('.table-agreements', window.location.href, [2], notSortable, 'undefined',[1,'DESC']);
</script>
</body>
</html>
