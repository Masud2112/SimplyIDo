<?php
/**
* Added By : Vaidehi
* Dt : 10/12/2017
* For Brand Settings Module
*/
?>
<?php if(isset($packagename) && $packagename != "Paid") { ?>
	<div class="no-access">
			<h2><?php echo _l('brand_no_access'); ?></h2><br/>
			<p><?php echo _l('brand_settings_no_access'); ?></p>
		</div>
		<div class="overlay"></div>
<?php } ?>
<div <?php echo ((isset($packagename) && $packagename != "Paid") ? 'class="settings-noaccess"' : '');?>>
	<?php echo form_hidden('finance_settings'); ?>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active">
			<a href="#invoice" aria-controls="invoice" role="tab" data-toggle="tab"><?php echo _l('settings_sales_heading_invoice'); ?></a>
		</li>
	</ul>
	<div class="tab-content">
	  <div role="tabpanel" class="tab-pane active" id="invoice">
	  	<div class="col-md-2">
			<div class="form-group">
				<label class="control-label" for="invoice_prefix"><?php echo _l('settings_sales_invoice_prefix'); ?></label>
				<input type="text" name="settings[invoice_prefix]" class="form-control" value="<?php echo get_brand_option('invoice_prefix'); ?>">
			</div>
		</div>
		<div class="col-md-9 col-md-offset-1">
		<i class="fa fa-question-circle hide" data-toggle="tooltip" data-title="<?php echo _l('settings_sales_next_invoice_number_tooltip'); ?>"></i>
          
		<!-- settings_sales_next_invoice_number -->
		<!-- <hr /> -->
		
			<label for="invoice_number_format" class="control-label clearfix"><?php echo _l('settings_sales_invoice_number_format'); ?></label>
		<div class="form-group">
			<div class="radio radio-primary radio-inline">
				<input type="radio" id="number_based" name="settings[invoice_number_format]" value="1" <?php if(get_brand_option('invoice_number_format') == '1'){echo 'checked';} ?>>
				<label for="number_based"><?php echo _l('settings_sales_invoice_number_format_number_based'); ?></label>
			</div>
		</div>
		<div class="form-group">
			<div class="radio radio-primary radio-inline">
				<input type="radio" name="settings[invoice_number_format]" value="2" id="year_based" <?php if(get_brand_option('invoice_number_format') == '2'){echo 'checked';} ?>>
				<label for="year_based"><?php echo _l('settings_sales_invoice_number_format_year_based'); ?> (YYYY/01)</label>
			</div>
		</div>
		<div class="form-group">
			<div class="radio radio-primary radio-inline">
				<input type="radio" name="settings[invoice_number_format]" value="3" id="month_based" <?php if(get_brand_option('invoice_number_format') == '3'){echo 'checked';} ?>>
				<label for="month_based"><?php echo _l('settings_sales_invoice_number_format_month_based'); ?> (YYYYMMDD/01)</label>
			</div>
		</div>
		<div class="form-group">
			<div class="radio radio-primary radio-inline">
				<input type="radio" name="settings[invoice_number_format]" value="4" id="event_based" <?php if(get_brand_option('invoice_number_format') == '4'){echo 'checked';} ?>>
				<label for="month_based"><?php echo _l('settings_sales_invoice_number_format_event_based'); ?> (YYYYMMDD/0101)</label>
			</div>
		</div>

			<?php $next_proposal_number=get_brand_option('next_proposal_number'); 	$next_proposal_number=$next_proposal_number>0?$next_proposal_number:1; 
			echo render_input('settings[next_proposal_number]','',$next_proposal_number,'',array(),array(),'','hide'); ?>

		
		</div>

		<?php echo render_input('settings[next_invoice_number]','',get_brand_option('next_invoice_number'),'',array(),array(),'','hide'); ?>
		<?php echo render_textarea('settings[predefined_clientnote_invoice]','settings_predefined_clientnote',get_brand_option('predefined_clientnote_invoice'),array('rows'=>6)); ?>
		<?php echo render_textarea('settings[predefined_terms_invoice]','settings_predefined_predefined_term',get_brand_option('predefined_terms_invoice'),array('rows'=>6)); ?>

	</div>
	</div>
</div>
