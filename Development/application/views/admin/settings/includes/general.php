<div class="row">
	<div class="col-md-12">
		<?php $company_logo = get_option('company_logo'); ?>
		<?php if($company_logo != ''){ ?>
			<div class="form-group">
				<div class="row">
					<div class="col-md-9">
						<img src="<?php echo base_url('uploads/company/'.$company_logo); ?>" class="img img-responsive" alt="<?php get_option('companyname'); ?>">
					</div>
					<?php if(has_permission('settings','','delete')){ ?>
					<div class="col-md-3 text-right">
						<a href="<?php echo admin_url('settings/remove_company_logo'); ?>" data-toggle="tooltip" title="<?php echo _l('settings_general_company_remove_logo_tooltip'); ?>" class="_delete text-danger"><i class="fa fa-remove"></i></a>
					</div>
					<?php } ?>
				</div>
			</div>
			<div class="clearfix"></div>
			<?php } else { ?>
				<div class="form-group">
					<label for="company_logo" class="control-label"><?php echo _l('settings_general_company_logo'); ?></label>
					<!--<input type="file" name="company_logo" class="form-control" value="" data-toggle="tooltip" title="<?php //echo _l('settings_general_company_logo_tooltip'); ?>">-->
					 <div class="input-group">
                          <span class="input-group-btn">
                            <span class="btn btn-primary" onclick="$(this).parent().find('input[type=file]').click();">Browse</span>
                            <input name="company_logo" onchange="$(this).parent().parent().find('.form-control').html($(this).val().split(/[\\|/]/).pop());" style="display: none;" type="file" title="<?php echo _l('settings_general_company_logo_tooltip'); ?>">
                          </span>
                          <span class="form-control"></span>
                      </div>
				</div>
				<?php } ?>
				<hr />
				<?php $favicon = get_option('favicon'); ?>
				<?php if($favicon != ''){ ?>
					<div class="form-group favicon">
						<div class="row">
							<div class="col-md-9">
								<img src="<?php echo base_url('uploads/company/'.$favicon); ?>" class="img img-responsive">
							</div>
							<?php if(has_permission('settings','','delete')){ ?>
							<div class="col-md-3 text-right">
								<a href="<?php echo admin_url('settings/remove_favicon'); ?>" class="_delete text-danger"><i class="fa fa-remove"></i></a>
							</div>
							<?php } ?>
						</div>
						<div class="clearfix"></div>
					</div>
					<?php } else { ?>
						<div class="form-group favicon_upload">
							<label for="favicon" class="control-label"><?php echo _l('settings_general_favicon'); ?></label>
							<!--<input type="file" name="favicon" class="form-control">-->
							<div class="input-group">
	                              <span class="input-group-btn">
	                                <span class="btn btn-primary" onclick="$(this).parent().find('input[type=file]').click();">Browse</span>
	                                <input name="favicon" onchange="$(this).parent().parent().find('.form-control').html($(this).val().split(/[\\|/]/).pop());" style="display: none;" type="file">
	                              </span>
	                              <span class="form-control"></span>
	                        </div>
						</div>
						<?php } ?>
						<hr />
						<?php $attrs = (get_option('companyname') != '' ? array() : array('autofocus'=>true)); ?>
						<?php echo render_input('settings[companyname]','settings_general_company_name',get_option('companyname'),'text',$attrs); ?>
						<hr />
						<?php if(is_admin()) { ?>
						<?php echo render_input('settings[main_domain]','settings_general_company_main_domain',get_option('main_domain')); ?>
						<hr />
						<?php render_yes_no_option('rtl_support_admin','settings_rtl_support_admin'); ?>
						<hr />
						<?php render_yes_no_option('rtl_support_client','settings_rtl_support_client'); ?>
						<hr />
						<?php echo render_input('settings[allowed_files]','settings_allowed_upload_file_types',get_option('allowed_files')); ?>
						<?php } ?>
					</div>
				</div>
