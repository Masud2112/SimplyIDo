<!-- Added By Avni on 11/14/2017 -->
<?php init_head();?>
<div id="wrapper">
	<div class="content admin-services-page">
		<div class="row">
			<div class="col-md-12">
                <h1 class="pageTitleH1"><i class="fa fa-cubes"></i><?php echo $title; ?></h1>
                <div class="pull-right">
                    <div class="breadcrumb mb0">
                        <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <span>Services</span>
                    </div>
                </div>
                <div class="clearfix"></div>
				<div class="panel_s btmbrd">
					<div class="panel-body">						
						<div class="_buttons">
							<a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#service_modal"><?php echo _l('new_service'); ?></a>
						</div>
						<div class="clearfix"></div>
						<?php render_datatable(array(
							_l('service_dt_name'),							
							_l('')
							),'services'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="service_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel">
						<span class="edit-title"><?php echo _l('service_edit_title'); ?></span>
						<span class="add-title"><?php echo _l('service_add_title'); ?></span>
					</h4>
				</div>
				<?php echo form_open('admin/services/manage',array('id'=>'service_form')); ?>
				<?php echo form_hidden('brandtypeid'); ?>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">							
							<?php echo render_input('name','service_add_edit_name'); ?>			
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
					<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
					<?php echo form_close(); ?>
				</div>
			</div>
		</div>
	</div>
	<?php init_tail(); ?>
	<script>
		initDataTable('.table-services', window.location.href, [1], [1]);
		_validate_form($('#service_form'),{
			name:{
				required:true,
				remote: {
					url: admin_url + "services/service_name_exists",
					type: 'post',
					data: {
						brandtypeid:function(){
							return $('input[name="brandtypeid"]').val();
						}
					}
				}
			}},manage_service);
		/* SERVICE MANAGE FUNCTIONS */
		function manage_service(form) {
			var data = $(form).serialize();			
			var url = form.action;			
			$.post(url, data).done(function(response) {				
				response = JSON.parse(response);
				if (response.success == true) {
					$('.table-services').DataTable().ajax.reload();
					alert_float('success', response.message);
				} else {
					if(response.message != ''){
						alert_float('warning', response.message);
					}
				}
				$('#service_modal').modal('hide');
			});
			return false;
		}
		$(function(){
				// don't allow | charachter in service name
				// is used for service name separations!
				$('#service_modal input[name="name"]').on('change',function(){
					var val = $(this).val();
					if(val.indexOf('|') > -1){
						val = val.replace('|','');
						// Clean extra spaces in case this char is in the middle with space
						val = val.replace( / +/g, ' ' );
						$(this).val(val);
					}
				});
				$('#service_modal').on('show.bs.modal', function(event) {
					var button = $(event.relatedTarget)
					var id = button.data('id');					
					$('#service_modal input').val('').prop('disabled',false);
					$('#service_modal .add-title').removeClass('hide');
					$('#service_modal .edit-title').addClass('hide');					
					if (typeof(id) !== 'undefined') {
						$('input[name="brandtypeid"]').val(id);
						var name = $(button).parents('tr').find('td').eq(0).text();
						var color = $(button).parents('tr').find('td').eq(1).text();
						
						$('#service_modal .add-title').addClass('hide');
						$('#service_modal .edit-title').removeClass('hide');	
						$('#service_modal input[name="name"]').val(name);												
					}
				});
				$('#service_modal').on('hidden.bs.modal', function() {
					$('.form-group').removeClass('has-error');
			        $('.form-group').find('p.text-danger').remove();
				});
			});

		/* END SERVICE MANAGE FUNCTIONS */
	</script>
</body>
</html>
