<?php init_head(); ?>
<div id="wrapper">
    <div class="content line-item-page">
        <div class="row">
          <?php //var_dump($this->uri->uri_string());die; ?>
            <?php echo form_open_multipart(admin_url('invoice_items/add_line_item_master_category'),array('class'=>'item-form','autocomplete'=>'off')); ?>         
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                         <?php echo $title; ?>                          
                         <?php if(isset($item)){ ?>
                      <?php echo form_hidden('itemid',$item->itemid); ?>
                      <?php } ?>
                     </h4>                     
                      <hr class="hr-panel-heading" />

                       <!--  <div class="col-md-12">
                          <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="line_item_cat_name" id="line_item_cat_name" class="form-control" value="<?php //echo $sku; ?>">
                          </div>
                        </div> -->
                        <div class="col-md-12">                 
                          <div class="form-group">
                               <label for="subtype">Category</label>
                               <?php //$sku=( isset($item) ? $item->sku : ''); ?>
                              <!-- <input type="text" name="subtype" id="subtype" class="form-control" value="<?php //echo $sku; ?>"> -->
                              <div class="form-group">   
                                <select class="form-control" name="main_li_list" id="main_li_list">
                                <?php foreach($product_service_groups as $group){ ?>
                                    <option value="<?php echo $group['id']; ?>"><?php echo $group['name']; ?></option>
                                <?php } ?>
                                </select>
                              </div>
                               
                              <a href="#" class="btn btn-info pull-left mtop5 mbot25" data-toggle="modal" data-target="#line_item_category">Add Category</a>
                            
                          </div>
                        </div>

                        <div class="col-md-12">                 
                          <div class="form-group">
                               <label for="kind">Sub-Category</label>

                              <div class="form-group"> 
                                 <select class="form-control" name="suboptions" id="suboptions">
                                  <option value="#">-- Please select sub-category --</option>
                                </select>
                              </div>

                               <?php //$sku=( isset($item) ? $item->sku : ''); ?>
                              <!-- <input type="text" name="kind" id="kind" class="form-control" value="<?php //echo $sku; ?>"> -->
                               <a href="#" class="btn btn-info pull-left mtop5" data-toggle="modal" data-target="#line_item_sub_category">Add sub category</a>
                          </div>
                        </div>

                       
                <div class="clearfix mbot15"></div>
                <?php //echo render_input('unit','unit'); ?>
                
                    </div>
                </div>
            </div>
            <div class="btn-bottom-toolbar text-right btn-toolbar-container-out">        
                <button class="btn btn-default" type="button" onclick="fncancel();"><?php echo _l( 'Cancel'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
            <?php echo form_close(); ?>    
        </div>
    </div>
</div>


<div class="modal fade" id="line_item_category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          Add line item Category
        </h4>
      </div>
      <div class="modal-body">
        <?php if(has_permission('items','','create')){ ?>
        <div class="row">

          <?php echo form_open('admin/invoice_items/add_line_item_category',array('id'=>'tag_form')); ?>
          <?php echo form_hidden('tagid'); ?>

          <div class="col-md-11">
            <input type="text" name="line_item_category_name" id="line_item_category_name" class="form-control" placeholder="<?php echo _l('item_group_name'); ?>">
          </div>
          <div class="col-md-1">
            <span class="pull-right">
              <button class="btn btn-info p9" type="button" id="new-line-item-category-insert">Add</button>
            </span>
          </div>

          <?php echo form_close(); ?>

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
                      <button class="btn btn-info p7 update-line-item-category" type="button"><?php echo _l('submit'); ?></button>
                    </span>
                  </div>
                </div>
              </td>
              <td align="right">
                <?php if(has_permission('items','','edit')){ ?><button type="button" class="btn btn-orange btn-icon edit-item-group"><i class="fa fa-pencil-square-o"></i></button><?php } ?>
                <?php if(has_permission('items','','delete')){ ?><a href="<?php echo admin_url('invoice_items/delete_line_item_category/'.$group['id']); ?>" class="btn btn-danger btn-icon delete-item-group _delete"><i class="fa fa-remove"></i></a><?php } ?></td>
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



<div class="modal fade" id="line_item_sub_category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          Add line item Sub Category
        </h4>
      </div>
      <div class="modal-body">
        <?php if(has_permission('items','','create')){ ?>
        <div class="row">
            <div class="col-md-12">
              <label for="option_parent_category">Choose Line Item Parent category</label>
            <div class="form-group">   
                <select class="form-control" name="parent_category" id="parent_category">
                  <?php foreach($product_service_groups as $group){ ?>
                    <option value="<?php echo $group['id']; ?>"><?php echo $group['name']; ?></option>
                  <?php } ?>
                </select>
            </div>
            </div>
        </div>

        <div class="row">
          <div class="col-md-11">
            <label for="line_item_subcategory">Line Item Sub category</label>
            <input type="text" name="line_item_sub_category_name" id="line_item_sub_category_name" class="form-control" placeholder="<?php echo _l('item_group_name'); ?>">
          </div>
          <div class="col-md-1">
            <span class="pull-right mtop25">
              <button class="btn btn-info p9" type="button" id="new-line-item-sub-category-insert">Add</button>
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
              <?php foreach($line_item_sub_cat as $group){ ?>
              <tr data-group-row-id="<?php echo $group['id']; ?>">
                <td data-order="<?php echo $group['name']; ?>">
                  <span class="line_item_name_plain_text"><?php echo $group['name']; ?></span>
                  <div class="group_edit hide">
                   <div class="input-group">
                    <input type="text" class="form-control">
                    <span class="input-group-btn">
                      <button class="btn btn-info p7 update-line-item-sub-category" type="button"><?php echo _l('submit'); ?></button>
                    </span>
                  </div>
                </div>
              </td>
              <td align="right">
                <?php if(has_permission('items','','edit')){ ?><button type="button" class="btn btn-orange btn-icon edit-line-item-group"><i class="fa fa-pencil-square-o"></i></button><?php } ?>
                <?php if(has_permission('items','','delete')){ ?><a href="<?php echo admin_url('invoice_items/delete_line_item_sub_category/'.$group['id']); ?>" class="btn btn-danger btn-icon delete-item-group _delete"><i class="fa fa-remove"></i></a><?php } ?></td>
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



<?php init_tail(); ?>


<script>
    init_editor('.long_description');
    function fncancel(){
        location.href='<?php echo base_url(); ?>admin/invoice_items';          
    }
    $(function(){
        _validate_form($('form'), {
            description:{
                required:true,
                remote: {
                    url: admin_url + "invoice_items/invoice_name_exists",
                    type: 'post',
                    data: {
                        itemid:function(){
                            return $('input[name="itemid"]').val();
                        }
                    }
                }
            },            
            rate: {
                required: true,
            },
            group_id : 'required'
        });
    });


         





  //$('#new-address-book').toggle();
  $('#existing-client-book').toggle();

  $('input:radio').change(function() {
      if($(this).val() == 'markup') {
          $('#new-address-book').toggle();
          $('#existing-client-book').hide();
      }

      if($(this).val() == 'discount') {
          $('#new-address-book').hide();
          $('#existing-client-book').toggle();            
      }
  });

  $('#markup_discount_rate').on('input',function(e){
    
    var flat_price = $( "#rate" ).val();
    var markup_rate = $( "#markup_discount_rate" ).val();
    //alert(markup_rate);

    var final_markup_price = (+flat_price) + (+markup_rate);

    $( "#calculated_rate" ).val(final_markup_price);

  });



  /*
    * Added by: Sanjay
    * Date: 01-02-2017
    * For Product & Services search result
    */

    initDataTable('.table-invoice-items', window.location.href, [0,2], [0,2],'undefined',[0,'ASC']);

    if(get_url_param('line_item_category')){
      // Set time out user to see the message
      setTimeout(function(){
       $('#line_item_category').modal('show');
     },1000);
    }
    if(get_url_param('line_item_sub_category')){
      // Set time out user to see the message
      setTimeout(function(){
       $('#line_item_sub_category').modal('show');
     },1000);
    }


function manage_tag(form) {
                var data = $('#tag_form').serialize();     
                var url = form.action;
                $.post(url, data).done(function(response) {
                  response = JSON.parse(response);
                  if (response.success == true) {
                    //$('.table-tags').DataTable().ajax.reload();
                    alert_float('success', response.message);
                  } else {
                    if(response.message != ''){
                      alert_float('warning', response.message);
                    }
                  }
                  $('#line_item_category').modal('hide');
                });
                return false;
              }
              
            _validate_form($('#tag_form'),{
              line_item_category_name:{
                required:true,
                remote: {
                  url: admin_url + "invoice_items/category_name_exists",
                  type: 'post',
                  data: {
                    tagid:function(){
                      return $('input[name="tagid"]').val();
                    }
                  }
                }
              },
              color:{required:true}}, manage_tag);


            
    $('#new-line-item-category-insert').on('click',function(){
          var line_item_category_name = $('#line_item_category_name').val();
          if(line_item_category_name != ''){
            $.post(admin_url+'invoice_items/add_line_item_category',{name:line_item_category_name}).done(function(){
              window.location.href = admin_url+'invoice_items/product_item?line_item_category=true';
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

    $('body').on('click','.update-line-item-category',function(){
      var tr = $(this).parents('tr');
      var product_group_id = tr.attr('data-group-row-id');
      name = tr.find('.group_edit input').val();
      if(name != ''){
        $.post(admin_url+'invoice_items/update_line_item_category/'+product_group_id,{name:name}).done(function(){
          window.location.href = admin_url+'invoice_items/product_item?line_item_category=true';
       });
      }
    });




    $('#new-line-item-sub-category-insert').on('click',function(){
         var line_item_sub_category_name = $('#line_item_sub_category_name').val();
         var line_item_parent_cat_name = $( "#parent_category option:selected" ).val();
         
        if(line_item_sub_category_name != ''){
          var postData = {
            'name' : line_item_sub_category_name,
            'parent_cat_id' : line_item_parent_cat_name
          };
            $.post(admin_url+'invoice_items/add_line_item_sub_category',postData).done(function(){
              window.location.href = admin_url+'invoice_items/product_item?line_item_sub_category=true';
            });  
          }
        });

    $('body').on('click','.edit-line-item-group',function(){
      var tr = $(this).parents('tr'),
      group_id = tr.attr('data-group-row-id');
      tr.find('.line_item_name_plain_text').toggleClass('hide');
      tr.find('.group_edit').toggleClass('hide');
      tr.find('.group_edit input').val(tr.find('.line_item_name_plain_text').text());
    });

    $('body').on('click','.update-line-item-sub-category',function(){
      var tr = $(this).parents('tr');
      var product_group_id = tr.attr('data-group-row-id');
      name = tr.find('.group_edit input').val();
      if(name != ''){
        $.post(admin_url+'invoice_items/update_line_item_sub_category/'+product_group_id,{name:name}).done(function(){
          window.location.href = admin_url+'invoice_items/product_item?line_item_sub_category=true';
       });
      }
    });



    $('#main_li_list').change(function(){ 

            $("#suboptions > option").remove();
            var option = $('#main_li_list').val();
            
            if(option == '#'){
                return false; 
            }

            $.ajax({
                type: "POST",
                url: admin_url+"/invoice_items/getsubcategory/"+option, 
                success: function(suboptions) 
                {console.log(suboptions);
                    $.each(suboptions,function(id,name)
                    {
                        var opt = $('<option />');
                        opt.val(id);
                        opt.text(name);
                        $('#suboptions').append(opt);
                    });
                }
 
            });
 
        });


</script>
