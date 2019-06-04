<?php 
	/**
	* Added By: Vaidehi
	* Dt: 10/02/2017
	* Package Module
	*/
	init_head(); 
?>
<div id="wrapper">
	<div class="content packages-manage-admin-page">
		<div class="row">
			<div class="col-md-12">
                <h1 class="pageTitleH1"><i class="fa fa-usd"></i><?php echo $title; ?></h1>
                <div class="pull-right">
                    <div class="breadcrumb mb0">
                        <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <span>Packages</span>
                    </div>
                </div>
                <div class="clearfix"></div>
				<div class="panel_s btmbrd">
					<div class="panel-body">
						<div class="_buttons">
							<?php if (has_permission('packages','','create')) { ?>
								<a href="<?php echo admin_url('packages/package'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_package'); ?></a>
							<?php } ?>
						</div>
						<div class="clearfix"></div>
						<?php render_datatable(array(
							_l('packages_dt_name'),
                            _l('packages_total_customers'),
							_l('packages_dt_type'),
							_l('packages_dt_price'),
							_l('packages_dt_trial'),							
							_l('')
							),'packages'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php init_tail(); ?>
	<script>
		var notSortable = $('.table-packages').find('th').length - 1;
		initDataTable('.table-packages', window.location.href, [1], notSortable);
	</script>
</body>
</html>
