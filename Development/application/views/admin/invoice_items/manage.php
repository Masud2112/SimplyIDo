<?php init_head(); ?>
<div id="wrapper">
  <div class="content manage-products-page">
    <div class="row">
      <div class="col-md-12">         
		  <div class="breadcrumb">
			  <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
			  <i class="fa fa-angle-right breadcrumb-arrow"></i>
			  <a href="<?php echo admin_url('setup'); ?>"><?php echo _l('breadcrum_setting_label'); ?></a>
			  <i class="fa fa-angle-right breadcrumb-arrow"></i>
			  <span><?php echo _l('breadcrum_product_service_label'); ?></span>
		  </div>
		  <h1 class="pageTitleH1"><i class="fa fa-money"></i><?php echo $title; ?></h1>
          <div class="clearfix"></div>
        <div class="panel_s btmbrd">
          <div class="panel-body">
            <?php if(has_permission('items','','create')){ ?>
              <div class="_buttons">
                <a href="<?php echo admin_url('invoice_items/item'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_invoice_item'); ?></a>  
                <!-- <a href="#" class="btn btn-info pull-left mleft5" data-toggle="modal" data-target="#groups"><?php //echo _l('item_groups'); ?></a> -->
                <a href="<?php echo admin_url('invoice_items/view_line_item_category'); ?>" class="btn btn-info pull-left display-block mleft5"><?php echo _l('product_services_category_button_text'); ?></a>
              </div>
              <div class="clearfix"></div>
            <?php }			  
              if(isset($column_setting_data)){
                $a = explode(',',$column_setting_data->column_name); ?>
                <?php if(!in_array('category', $a)){ ?> 
                  <style type="text/css"> 
                    tr > th:nth-child(2), tr > td:nth-child(2) {display: none;}</style>
                <?php } ?>
                <?php if(!in_array('cost', $a)){ ?> 
                  <style type="text/css"> tr > th:nth-child(3), tr > td:nth-child(3) {display: none;}</style>
                <?php } ?>
                <?php if(!in_array('profit', $a)){ ?> 
                  <style type="text/css"> tr > th:nth-child(6), tr > td:nth-child(6) {display: none;}</style>
            <?php } } ?>
			  
			   <?php if(is_mobile()) { echo '<a class="btn btn-primary  filter_btn_search"><i class="glyphicon glyphicon-search"></i></a>'; } ?>
         <div class="ProductsnServices_blk  ">
            <?php render_datatable(array(
              _l('invoice_items_list_description'),
              _l('Category'),
              _l('Cost'),
              _l('Price (USD)'),
              _l('Tax'),
              _l('Profit ($)'),
              _l(''),
              ),'invoice-items'); ?>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!--
  * Added by: Sanjay
  * Date: 02-05-2018
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
      <?php echo form_open('admin/invoice_items/save_display_settings',array('novalidate'=>true,'id'=>'pro_service_duplicate')); ?>
        <div class="modal-body">
          <div class="row">
            <div id="additionalnew"></div>
            <div class="form-group">
              <?php 
               if(isset($column_setting_data)){
                $all_setting = $column_setting_data->column_name;
                $raw_setting = explode(",", $all_setting );                 
              } ?>
              <input type="hidden" name="brand_id" value="<?php echo get_user_session(); ?>">
              <input type="hidden" name="staff_id" value="<?php echo $this->session->userdata['staff_user_id']; ?>">
              <div class="col-md-12">
                <div class="col-md-12">
                  <div class="checkbox">
                    <?php $checked = (isset($all_setting) ? ((in_array('category', $raw_setting)) ? 'checked="checked"' : '') : 'checked="checked"');?>
                    <input type="checkbox" class="display_group" name="display_option[]" value="category" <?php echo $checked; ?>>
                    <label for="leads"><?php echo _l('column_display_option_category'); ?></label>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="checkbox">
                    <?php $checked = (isset($all_setting) ? ((in_array('cost', $raw_setting)) ? 'checked="checked"' : '') : 'checked="checked"');?>
                    <input type="checkbox" class="display_group" name="display_option[]" value="cost" <?php echo $checked; ?>>
                    <label for="leads"><?php echo _l('column_display_option_cost'); ?></label>
                  </div>
                </div>
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
          <input type="hidden" name="page_type" value="product_service_list">
          <button type="submit" class="btn btn-info" id="add_subcategory"><?php echo _l('submit'); ?></button>
        </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>

<div class="modal fade" id="groups" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <?php echo _l('item_group'); ?>
        </h4>
      </div>
      <div class="modal-body">
        <?php if(has_permission('items','','create')){ ?>
        <div class="row">
          <div class="col-md-10">
            <input type="text" name="item_group_name" id="item_group_name" class="form-control" placeholder="<?php echo _l('item_group_name'); ?>">
          </div>
          <div class="col-md-2">
            <span class="pull-right">
              <button class="btn btn-info p9" type="button" id="new-item-group-insert"><?php echo _l('new_item_group'); ?></button>
            </span>
          </div>
        </div>
        <hr />
        <?php } ?>
        <div class="row">
         <div class="container-fluid">
          <table class="table table-striped dt-table table-items-groups" data-order-col="0" data-order-type="asc">
            <thead>
              <tr>
                <th><?php echo _l('item_group_name'); ?></th>
                <th><?php echo _l(''); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($items_groups as $group){ ?>
              <tr data-group-row-id="<?php echo $group['id']; ?>">
                <td data-order="<?php echo $group['name']; ?>">
                  <span class="group_name_plain_text"><?php echo $group['name']; ?></span>
                  <div class="group_edit hide">
                   <div class="input-group">
                    <input type="text" class="form-control">
                    <span class="input-group-btn">
                      <button class="btn btn-info p7 update-item-group" type="button"><?php echo _l('submit'); ?></button>
                    </span>
                  </div>
                </div>
              </td>
              <td align="right">
                <?php if(has_permission('items','','edit')){ ?><button type="button" class="btn btn-orange btn-icon edit-item-group"><i class="fa fa-pencil-square-o"></i></button><?php } ?>
                <?php if(has_permission('items','','delete')){ ?><a href="<?php echo admin_url('invoice_items/delete_group/'.$group['id']); ?>" class="btn btn-danger btn-icon delete-item-group _delete"><i class="fa fa-remove"></i></a><?php } ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
    </div>
  </div>
</div>
</div>

<div class="modal fade" id="product_services" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <?php echo _l('product_services_groups'); ?>
        </h4>
      </div>
      <div class="modal-body">
        <?php if(has_permission('items','','create')){ ?>
        <div class="row">


          <div class="col-md-10">
            <input type="text" name="product_service_group_name" id="product_service_group_name" class="form-control" placeholder="<?php echo _l('item_group_name'); ?>">
          </div>
          <div class="col-md-2">
            <span class="pull-right">
              <button class="btn btn-info p9" type="button" id="new-product-service-group-insert"><?php echo _l('new_item_group'); ?></button>
            </span>
          </div>


        </div>
        <hr />
        <?php } ?>
        <div class="row">
         <div class="container-fluid">
          <table class="table table-striped dt-table table-items-groups" data-order-col="0" data-order-type="asc">
            <thead>
              <tr>
                <th><?php echo _l('item_group_name'); ?></th>
                <th><?php echo _l(''); ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($product_service_groups as $group){ ?>
              <tr data-group-row-id="<?php echo $group['id']; ?>">
                <td data-order="<?php echo $group['name']; ?>">
                  <span class="group_name_plain_text"><?php echo $group['name']; ?></span>
                  <div class="group_edit hide">
                   <div class="input-group">
                    <input type="text" class="form-control">
                    <span class="input-group-btn">
                      <button class="btn btn-info p7 update-product-service-group" type="button"><?php echo _l('submit'); ?></button>
                    </span>
                  </div>
                </div>
              </td>
              <td align="right">
                <?php if(has_permission('items','','edit')){ ?><button type="button" class="btn btn-orange btn-icon edit-item-group"><i class="fa fa-pencil-square-o"></i></button><?php } ?>
                <?php if(has_permission('items','','delete')){ ?><a href="<?php echo admin_url('invoice_items/delete_service_group/'.$group['id']); ?>" class="btn btn-danger btn-icon delete-item-group _delete"><i class="fa fa-remove"></i></a><?php } ?></td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
    </div>
  </div>
</div>
</div>


<!--
  * Added by: Sanjay
  * Date: 02-05-2018
  * Popup to display option for duplicate product & service in current brand or existing brands.
  -->
<div class="modal fade" id="duplicate_line_item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          Duplicate Product & Service
        </h4>
      </div>
      <div class="modal-body">
        <?php if(has_permission('items','','create')){ ?>
          <div class="row">
            <?php echo form_open('admin/invoice_items/duplicate_pro_service',array('novalidate'=>true,'id'=>'pro_service_duplicate')); ?>
              <div id="additional"></div>
              <div class="form-group">
                <div class="col-md-12 mbot20">
                <div class="radio radio-primary radio-inline">
                  <input type="radio" id="duplicate_by_current_brand" name="duplicate_by_brand" class="duplicate_by_brand" value="current_brand">
                  <label for="number_based">Duplicate for Current brand</label>
                </div>
              </div>

              <div class="col-md-12">
                <div class="radio radio-primary radio-inline">
                  <input type="radio" id="duplicate_by_existing_brand" name="duplicate_by_brand" class="duplicate_by_brand" value="existing_brand">
                  <label for="number_based">Duplicate for Existing brand</label>
                </div>

                <div class="col-md-12 brand_list_section mtop10">
                  <select name="brandid" class="selectpicker col-md-12">
                  <?php foreach ($brands as $brand) {  ?>
                    <option value="<?php echo $brand['brandid']; ?>"><?php echo $brand['name']; ?></option>
                  <?php }  ?>
                 </select>
               </div>
              </div>
              </div>
          </div>
          
        <?php } ?>
       
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-info" id="add_subcategory"><?php echo _l('submit'); ?></button>
      </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>



<?php init_tail(); ?>
<script>

function duplicate_status(invoker){
  var id = $(invoker).data('id');
  $('#duplicate_record_id').val(id);
  $('#additional').append(hidden_input('duplicate_record_id',id));
}
  $(function(){
    var notSortable =  $('.table-invoice-items').find('th').length - 1 ;
    initDataTable('.table-invoice-items', window.location.href, [], [4, notSortable],'',[6, "ASC" ]);
    if(get_url_param('groups_modal')){
      setTimeout(function(){
       $('#groups').modal('show');
     },1000);
    }

$('.brand_list_section').hide();
$('.duplicate_by_brand').on('click',function(){
  if($("#duplicate_by_existing_brand").prop('checked') == true){
    $('.brand_list_section').show();
  }else{
    $('.brand_list_section').hide();
  }
});
    

$('#new-item-group-insert').on('click',function(){
      var group_name = $('#item_group_name').val();
      if(group_name != ''){
        $.post(admin_url+'invoice_items/add_group',{name:group_name}).done(function(){
          window.location.href = admin_url+'invoice_items?groups_modal=true';
        });  
      }
    });

    $('body').on('click','.edit-item-group',function(){
      var tr = $(this).parents('tr'),
      group_id = tr.attr('data-group-row-id');
      tr.find('.group_name_plain_text').toggleClass('hide');
      tr.find('.group_edit').toggleClass('hide');
      tr.find('.group_edit input').val(tr.find('.group_name_plain_text').text());
    });

    $('body').on('click','.update-item-group',function(){
      var tr = $(this).parents('tr');
      var group_id = tr.attr('data-group-row-id');
      name = tr.find('.group_edit input').val();
      if(name != ''){
        $.post(admin_url+'invoice_items/update_group/'+group_id,{name:name}).done(function(){
          window.location.href = admin_url+'invoice_items?groups_modal=true';
       });
      }
    });



    /*
    * Added by: Sanjay
    * Date: 01-02-2017
    * For Product & Services search result
    */

    initDataTable('.table-invoice-items', window.location.href, [0,4], [0,4],'undefined',[0,'ASC']);
    if(get_url_param('product_groups_modal')){
      setTimeout(function(){
       $('#product_services').modal('show');
     },1000);
    }

    $('#new-product-service-group-insert').on('click',function(){
          var product_service_group_name = $('#product_service_group_name').val();
          if(product_service_group_name != ''){
            $.post(admin_url+'invoice_items/add_product_service_group',{name:product_service_group_name}).done(function(){
              window.location.href = admin_url+'invoice_items?product_groups_modal=true';
            });  
          }
        });

    $('body').on('click','.update-product-service-group',function(){
      var tr = $(this).parents('tr');
      var product_group_id = tr.attr('data-group-row-id');
      name = tr.find('.group_edit input').val();
      if(name != ''){
        $.post(admin_url+'invoice_items/update_product_service_group/'+product_group_id,{name:name}).done(function(){
          window.location.href = admin_url+'invoice_items?product_groups_modal=true';
       });
      }
    });

    $('div.dataTables_filter .input-group').append( '<div class="pull-right"><a href="#" class="btn-info display-block mleft5 padding-5" data-toggle="modal" data-target="#display_column" id="display_column_popup" style="padding: 5px 12px;border-radius: 3px;"><i class="fa fa-cog" aria-hidden="true"></i></a></div>' );
    $('div.dataTables_filter input').css('border-radius','1');

  });

  /**
  * Added By: Vaidehi
  * Dt: 02/05/2018
  * to change icon on product description collapse
  */
  function fnCollapseDesc(productid) {
    $('#icon-'+productid).toggleClass('fa-sort-asc fa-sort-desc');
  }

  /**
  * Added By: Vaidehi
  * Dt: 02/05/2018
  * to change icon on options collapse
  */
  function fnCollapseOption(productid) {
    $('#option-icon-'+productid).toggleClass('fa-sort-asc fa-sort-desc');
  } 	
</script>
</body>
</html>
