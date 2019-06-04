<?php

init_head();
if(isset($_GET['pid'])){
   $url =  admin_url('questionnaire/questionnaire')."?pid=".$_GET['pid'];
}else{
    $url =  admin_url('questionnaire/questionnaire');
}
?>
<div id="wrapper">
	<div class="content questionnaire">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="_buttons">
							<?php if (has_permission('questionnaire','','create')) { ?>
							<a href="<?php echo $url; ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_questionnaire'); ?></a>
							<?php } ?>
						</div>
						<div class="clearfix"></div>
						<hr class="hr-panel-heading" />
						<div class="clearfix"></div>
						<?php render_datatable(array(
							_l('questionnaire_name'),
							_l('questionnaire_datecreated'),
							_l('')
							),'questionnaire'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php init_tail(); ?>
	<script>
		var notSortable = $('.table-questionnaire').find('th').length - 1;
		//initDataTable(selector, url, notsearchable, notsortable, fnserverparams, defaultorder)
		initDataTable('.table-questionnaire', window.location.href, [], notSortable, 'undefined',[0,'DESC']);
	</script>
</body>
</html>
