 <?php
	/**
	* Added By : Vaidehi
	* Dt : 05/01/2018
	* For Global Search - Search filter tags settings
	*/
?>
<div class="row">
	<div class="col-md-12 search_filterss">
		<div id="all_tags_section">
      <?php 
        $all_tags =  get_option('filter_tags');
        $exp_val = explode(',', $all_tags);
      ?>
      <div class="col-md-3">
      	<div class="form-group">
      		<div class="checkbox">
          	<input type="checkbox" class="filter_group" name="settings[filter_tags][]" id="addressbook" <?php if(in_array('addressbook', $exp_val)){echo 'checked';}; ?> value="addressbook">
          	<label for="addressbook"><?php echo _l('settings_general_search_filter_tag_addressbook_label'); ?></label>
        	</div>
      	  <div class="checkbox">
          	<input type="checkbox" class="filter_group" name="settings[filter_tags][]" id="proposals" <?php if(in_array('proposals', $exp_val)){echo 'checked';}; ?> value="proposals">
          	<label for="proposals"><?php echo _l('settings_general_search_filter_tag_proposals_label'); ?></label>
        	</div>
        	<div class="checkbox">
          	<input type="checkbox" class="filter_group" name="settings[filter_tags][]" id="agreements" <?php if(in_array('agreements', $exp_val)){echo 'checked';}; ?> value="agreements">
          	<label for="agreements"><?php echo _l('settings_general_search_filter_tag_agreements_label'); ?></label>
        	</div>
        	<div class="checkbox">
          	<input type="checkbox" class="filter_group" name="settings[filter_tags][]" id="paymentschedules" <?php if(in_array('paymentschedules', $exp_val)){echo 'checked';}; ?> value="paymentschedules">
          	<label for="paymentschedules"><?php echo _l('settings_general_search_filter_tag_payment_schedules_label'); ?></label>
        	</div>
        </div>
    	</div>
    </div>
	</div>
</div>	