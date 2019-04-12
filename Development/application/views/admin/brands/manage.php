<?php 
	/**
	* Added By: Vaidehi
	* Dt: 10/02/2017
	* Package Module
	*/
	init_head(); 
?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="_buttons">
							<?php if (has_permission('brands','','create')) { ?>
								<a href="<?php echo admin_url('brands/add'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_brand'); ?></a>
							<?php } ?>
						</div>
						<div class="clearfix"></div>
						<hr class="hr-panel-heading" />
						<div class="clearfix"></div>
						<?php render_datatable(array(
							_l('_name'),
							//_l('options')
							_l('')
							),'brands'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php init_tail(); ?>
	<script>
		var notSortable = $('.table-brands').find('th').length - 1;
		initDataTable('.table-brands', window.location.href, [1], notSortable);
	</script>
</body>
</html>
