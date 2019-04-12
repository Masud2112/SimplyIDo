<?php init_head(); ?>
<div id="wrapper">
    <div class="content paymentschedules-manage-page">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span>Payment Schedules</span>
                </div>

                <h1 class="pageTitleH1"><i class="fa fa-calendar"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="">
                            <?php if (has_permission('paymentschedules','','create')) { ?>
                                <a href="<?php echo admin_url('paymentschedules/paymentschedule'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_paymentschedule'); ?></a>
                            <?php } ?>
                        </div>
                        <?php if(is_mobile()) { echo '<a class="btn btn-primary  filter_btn_search"><i class="glyphicon glyphicon-search"></i></a>'; } ?>
                        <div class="clearfix"></div>
                        <?php render_datatable(array(
                            _l('paymentschedules_dt_name'),
                            _l('paymentschedules_created'),
                            _l('')
                        ),'paymentschedules'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    var notSortable = $('.table-paymentschedules').find('th').length - 1;
    initDataTable('.table-paymentschedules', window.location.href, [2], notSortable, 'undefined',[1,'DESC']);

</script>
</body>
</html>
