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
            <?php echo form_open($this->uri->uri_string(),array('class'=>'package-form')); ?>
            <div class="col-md-12">                                
                    <div class="breadcrumb">
                        <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('packages'); ?>">Packages</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <span><?php echo isset($package)?$package->name:"New Package"; ?></span>
                    </div>                
				<h1 class="pageTitleH1"><i class="fa fa-usd"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                     <?php if(isset($package)){ ?>
                      <?php if (has_permission('packages','','create')) { ?>
                     <a href="<?php echo admin_url('packages/package'); ?>" class="btn btn-info pull-left mbot20 display-block"><?php echo _l('new_package'); ?></a>
                     <div class="clearfix"></div>
                     <?php } ?>
                     <?php } ?>
                     <?php if(isset($package)){ ?>
                     <?php $attrs = (isset($package) ? array() : array('autofocus'=>true)); ?>
                     <?php if(total_rows('tblpackagepermissions',array('packageid'=>$package->packageid)) > 0){ ?>
                     <div class="alert alert-warning bold">
                        <?php echo _l('change_package_permission_warning'); ?>
                        <div class="checkbox" style="display: none;">
                            <input type="checkbox" name="update_customer_permissions" id="update_customer_permissions" checked="checked">
                            <label for="update_customer_permissions"><?php echo _l('package_update_customer_permissions'); ?></label>
                        </div>
                    </div>
                    <?php } ?>
                    <?php } ?>
                    <div class="form-group">
                      <label for="packagetypeid" class="control-label"><small class="req text-danger">* </small><?php echo _l('packages_add_edit_package_type'); ?>
                      </label>
                      <select name="packagetypeid" id="packagetypeid" class="form-control selectpicker" data-none-selected-text="<?php echo _l('packages_add_edit_package_type'); ?>" autofocus="1" required>
                        <option value="">Select Package Type</option>
                        <?php foreach($packagetypes as $packagetype){
                           $selected = '';
                           if(isset($package)){
                              if($package->packagetypeid == $packagetype['id']){
                                 $selected = 'selected';
                             }
                           }
                           ?>
                        <option value="<?php echo $packagetype['id']; ?>" <?php echo $selected; ?>><?php echo ucfirst($packagetype['name']); ?></option>
                        <?php } ?>
                      </select>
                    </div>
                   
                    <?php $value = (isset($package) ? $package->name : ''); ?>
                    <?php echo render_input('name','packages_add_edit_name',$value,'text'); ?>
                    
                    <div id="trialdiv">
                      <?php $value = (isset($package) ? $package->trial_period : ''); ?>
                      <?php echo render_input('trial_period','packages_add_edit_package_trial_period',$value,'text'); ?>
                    </div>  
                    <div id="pricediv">
                      <?php $value = (isset($package) ? $package->price : ''); ?>
                      <?php echo render_input('price','packages_add_edit_package_price',$value,'text'); ?>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="bold"><?php echo _l('package_permission'); ?></th>
                                    <th class="text-center bold">
                                      <div class="checkbox">
                                        <input type="checkbox" id="ckbCheckAll">
                                        <label id="pkglabel"><?php echo _l('package_access'); ?>
                                      </label>
                                      </div>
                                    </th>
                                    <th class="bold restriction"><?php echo _l('package_restriction'); ?></th>
                                    <!--
                                     <th class="text-center bold"><?php //echo _l('permission_view'); ?></th>
                                    <th class="text-center bold"><?php //echo _l('permission_view_own'); ?></th>
                                    <th class="text-center bold"><?php //echo _l('permission_create'); ?></th>
                                    <th class="text-center bold"><?php //echo _l('permission_edit'); ?></th>
                                    <th class="text-center text-danger bold"><?php //echo _l('permission_delete'); ?></th>  
                                    -->
                                </tr>
                            </thead>
                            <tbody>
                              <?php
                                //$conditions = get_permission_conditions();
                                $conditions = get_package_permission_conditions();
                                  foreach($permissions as $i => $permission) { 
                                    $class = (($permission['name'] == 'Brands' || $permission['name'] == 'Projects' || $permission['name'] == 'Team Members') ? 'show' : 'hide');
                                    
                                    if(isset($package_permissions)) {
                                      $search = ['name' => $permission['name']];
                                      $keys = array_keys(
                                        array_filter(
                                            $package_permissions,
                                            function ($v) use ($search) {
                                                return $v['name'] ==   $search['name']; 
                                            }
                                        )
                                      );
                                      
                                      $package_restriction_key = (!empty($keys[0]) ? $keys[0] : '');
                                    }

                                    $permission_condition = $conditions[$permission['shortname']];
                                    ?>
                                    <tr>
                                        <td>
                                         <?php echo $permission['name']; ?></td>
                                         <td class="text-center">
                                            <?php 
                                              if($permission_condition['access'] == true) {
                                                $statement = '';

                                                if(isset($package)) {
                                                  if(total_rows('tblpackagepermissions',array('packageid'=>$package->packageid,'permissionid'=>$permission['permissionid'],'can_access'=>1)) > 0) {
                                                    $statement = 'checked';
                                                  }
                                                }
                                            ?>
                                                <div class="checkbox">
                                                  <input type="checkbox" class="checkBoxClass" data-can-access <?php echo $statement; ?> name="access[]" value="<?php echo $permission['permissionid']; ?>">
                                                  <label></label>
                                                </div>
                                            <?php } ?>
                                        </td>
                                        <td class="text-center restriction">
                                          <input type="number" class="form-control <?php echo $class; ?>" name="restriction[<?php echo $permission['permissionid']; ?>]" value="<?php echo ( (isset($package_restriction_key) && $package_restriction_key != '') ? $package_permissions[$package_restriction_key]['restriction'] : 1); ?>">
                                        </td>
                                    </tr>
                              <?php } ?>
                              </tbody>
                            </table>
                          </div>
                            </div>
                        </div>
                    </div>
            <div class="btn-bottom-toolbar text-right btn-toolbar-container-out">
                <button class="btn btn-default" type="button" onclick="location.href='<?php echo base_url(); ?>admin/packages'"><?php echo _l( 'Cancel'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
             <?php echo form_close(); ?>
             </div>
        </div>
    </div>
    <?php init_tail(); ?>
    <script>
      $("#trialdiv").toggle();
      $("#pricediv").toggle();
      _validate_form($('.package-form'),{name:'required', packagetypeid: 'packagetypeid'});
      $("#packagetypeid").change(function(){
        var packagetext = $("#packagetypeid option:selected").text();
        if(packagetext == 'Paid') {
          $("#pricediv").toggle();
          $("#trialdiv").hide();
          $(".restriction").hide();
        } else if(packagetext == 'Trial') {
          $("#trialdiv").toggle();
          $("#pricediv").hide();
          $(".restriction").show();
        } else {
          $("#trialdiv").hide();
          $("#pricediv").hide();
          $(".restriction").show();
        }
      });

      var packagetext = $("#packagetypeid option:selected").text();
      if(packagetext == 'Trial') {
        $("#trialdiv").toggle();
      }
      if(packagetext == 'Paid') {
        $("#pricediv").toggle();
        $(".restriction").hide();
      }

      $("#ckbCheckAll").click(function () {
        $(".checkBoxClass").prop('checked', $(this).prop('checked'));
      });
      
      if ($('.checkBoxClass:checked').length == $('.checkBoxClass').length) {
        $("#ckbCheckAll").prop('checked',true);
      }
    </script>
  </body>
</html>