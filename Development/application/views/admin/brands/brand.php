<?php
  /**
  * Added By: Vaidehi
  * Dt: 10/14/2017
  * Brand Module
  */ 
  init_head(); 
?>
  <div id="wrapper">
    <div class="content">
      <div class="row">
        <?php 
          /**
          * Added By : Vaidehi
          * Dt : 11/09/2017
          * to check for limit based on package of logged in user
          */
          if((isset($packagename) && $packagename != "Paid") && (isset($module_active_entries) && ($module_active_entries >= $module_create_restriction))) { ?>
          <div class="col-md-8 col-md-offset-2">
              <div class="panel_s">
                <div class="panel-body">
                  <div class="warningbox">
                    <h4><?php echo _l('package_limit_restriction_line1', _l('brands_lowercase')); ?></h4>
                    <span><?php echo _l('package_limit_restriction_line2', _l('brands_lowercase')); ?></span>
                  </div>
              </div>
            </div>
          </div>
        <?php } else {?>
            <?php echo form_open($this->uri->uri_string(),array('class'=>'brand-form')); ?>
              <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                  <div class="panel-body">
                    <h4 class="no-margin">
                     <?php echo $title; ?>                         
                    </h4>
                    <hr class="hr-panel-heading" />                  
                    <div class="form-group" data-name="brandname">
                      <label for="brand-name" class="control-label"><small class="req text-danger">* </small><?php echo _l('register_brand_name'); ?></label>
                      <input class="form-control" type="text" placeholder="<?php echo _l('register_brand_name'); ?>" name="brandname" id="brandname">
                      <span id="brandmsg" class="parsley-required"></span>
                    </div>
                    <div class="form-group" data-name="brandtype">
                      <label for="brandtype" class="control-label"><small class="req text-danger">* </small><?php echo _l('register_brandtype'); ?></label>                       
                      <select class="form-control selectpicker" data-placeholder="<?php echo _l('register_brandtype'); ?>" name="brandtype" id="brandtype" data-none-selected-text="<?php echo _l('dropdown_non_selected_service'); ?>" data-live-search="true">
                          <option value=""></option>
                          <?php 
                              foreach ($brandtypes as $brandtype) {
                          ?>
                                <option value="<?php echo $brandtype['brandtypeid']; ?>"><?php echo $brandtype['name']; ?></option>
                          <?php
                              }
                          ?>
                      </select>
                    </div>
                    <div class="form-group" data-name="address1">
                      <label for="address1" class="control-label"><small class="req text-danger">* </small><?php echo _l('register_address1'); ?></label>
                      <input class="form-control" type="text" placeholder="<?php echo _l('register_address1'); ?>" name="address1" id="address1">
                    </div>
                    <div class="form-group" data-name="address2">
                      <label for="address2" class="control-label"><?php echo _l('register_address2'); ?></label>
                      <input class="form-control" type="text" placeholder="<?php echo _l('register_address2'); ?>" name="address2" id="address2">
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group" data-name="city">
                          <label for="city" class="control-label"><small class="req text-danger">* </small><?php echo _l('register_city'); ?></label>
                          <input class="form-control" type="text" placeholder="<?php echo _l('register_city'); ?>" name="city" id="city">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group" data-name="state">
                          <label for="state" class="control-label"><small class="req text-danger">* </small><?php echo _l('register_state'); ?></label>
                          <input class="form-control" type="text" placeholder="<?php echo _l('register_state'); ?>" name="state" id="state">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group" data-name="zipcode">
                          <label for="zip-code" class="control-label"><small class="req text-danger">* </small><?php echo _l('register_zipcode'); ?></label>
                          <input class="form-control" type="text" placeholder="<?php echo _l('register_zipcode'); ?>" name="zipcode" id="zipcode">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group" data-name="country">
                          <label for="country" class="control-label"><small class="req text-danger">* </small><?php echo _l('register_country'); ?></label>
                          <input class="form-control" type="text" name="country" id="country" value="United States" readonly>
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <button class="btn btn-default" type="button" onclick="location.href='<?php echo base_url(); ?>admin/'"><?php echo _l( 'Cancel'); ?></button>
                      <button class="btn btn-info type="submit" id="frm-submit"><?php echo _l('create_brand'); ?></button>
                    </div>
                  </div>
                </div>
              </div>
            <?php echo form_close(); ?>
        <?php } ?>
      </div>
    </div>
  </div>
  <?php init_tail(); ?>
  <script>
    $(function() {
      /**
      * Added By : Vaidehi
      * Dt : 11/09/2017
      * added to condition to run form validation script and check brand name exists script, if form is visible
      */
      if($(".row form").is(":visible")) {
        //ajax call to check whether brand name exists or not
        $("#brandname").blur(function() {
          var brandname = $(this).val();
          $.ajax({
            url: "<?php echo base_url();?>brandexists",
            method: "post",
            data: "brandname="+brandname,
            success: function(data){
              if(data == 1) {
                $('#frm-submit').prop('disabled', false);
                  $("#brandmsg").html("");
              } else {
                $('#frm-submit').prop('disabled', true);
                $("#brandmsg").html("Brand name already exists");
              }
            }
          });

          /**
          * Added By : Vaidehi
          * Dt : 11/20/2017
          * to disable submit if error exists on form
          */
          if($("#brandmsg").is(":visible")){
            $('#frm-submit').prop('disabled', true);
          }
        });
        
        _validate_form($('.brand-form'),{brandname:'required', brandtype:'required', address1:'required', city:'required', state:'required', zipcode:'required', country:'required'});
      }
    });
  </script>
</body>
</html>