<!--
  * Added by: Masud
  * Date: 02-08-2018
  * Popup to display column setting option
  -->
<div class="modal fade" id="display_column" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <?php echo _l('column_display_option'); ?>
        </h4>
      </div>
      <?php echo form_open('admin/invoice_items/save_package_display_settings',array('novalidate'=>true,'id'=>'package_display_settings')); ?>
        <div class="modal-body">
          <div class="row">
            <div id="additionalnew"></div>
            <div class="form-group">
              <?php 
               if(isset($vcols)){
                $all_setting = $vcols;
                $raw_setting = $vcols  ;              
              } ?>
              <input type="hidden" name="brand_id" value="<?php echo get_user_session(); ?>">
              <input type="hidden" name="staff_id" value="<?php echo $this->session->userdata['staff_user_id']; ?>">
              <input type="hidden" name="page_id" value="<?php echo isset($options['page_id'])?$options['page_id']:""; ?>">
              <input id= "page_type" type="hidden" name="page_type" value="<?php echo $options['page_type'];?>">
              <div class="col-md-12">
                <?php if($options['page_type']=='package') { ?>
                <div class="col-md-12">
                  <div class="checkbox">
                    <?php $checked = (isset($all_setting) ? ((in_array('qty', $raw_setting)) ? 'checked="checked"' : '') : 'checked="checked"');?>
                    <input type="checkbox" class="display_group" name="display_option[]" value="qty" <?php echo $checked; ?>>
                    <label for="leads"><?php echo _l('column_display_option_qty'); ?></label>
                  </div>
                </div>
                <?php } ?>
                <div class="col-md-12">
                  <div class="checkbox">
                    <?php $checked = (isset($all_setting) ? ((in_array('cost', $raw_setting)) ? 'checked="checked"' : '') : 'checked="checked"');?>
                    <input type="checkbox" class="display_group" name="display_option[]" value="cost" <?php echo $checked; ?>>
                    <label for="leads"><?php echo _l('column_display_option_cost'); ?></label>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="checkbox">
                    <?php $checked = (isset($all_setting) ? ((in_array('price', $raw_setting)) ? 'checked="checked"' : '') : 'checked="checked"');?>
                    <input type="checkbox" class="display_group" name="display_option[]" value="price" <?php echo $checked; ?>>
                    <label for="leads"><?php echo _l('column_display_option_price'); ?></label>
                  </div>
                </div>
                <?php if($options['page_type']=='package') { ?>
                <div class="col-md-12">
                  <div class="checkbox">
                    <?php $checked = (isset($all_setting) ? ((in_array('subtotal', $raw_setting)) ? 'checked="checked"' : '') : 'checked="checked"');?>
                    <input type="checkbox" class="display_group" name="display_option[]" value="subtotal" <?php echo $checked; ?>>
                    <label for="leads"><?php echo _l('column_display_option_subtotal'); ?></label>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="checkbox">
                    <?php $checked = (isset($all_setting) ? ((in_array('tax', $raw_setting)) ? 'checked="checked"' : '') : 'checked="checked"');?>
                    <input type="checkbox" class="display_group" name="display_option[]" value="tax" <?php echo $checked; ?>>
                    <label for="leads"><?php echo _l('column_display_option_tax'); ?></label>
                  </div>
                </div>
                <?php } ?>
                <div class="col-md-12">
                  <div class="checkbox">
                    <?php $checked = (isset($all_setting) ? ((in_array('profit', $raw_setting)) ? 'checked="checked"' : '') : 'checked="checked"');?>
                    <input type="checkbox" class="display_group" name="display_option[]" value="profit" <?php echo $checked; ?>>
                    <label for="leads"><?php echo _l('column_display_option_profit'); ?></label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-info" id="save_package_settings"><?php echo _l('submit'); ?></button>
        </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>