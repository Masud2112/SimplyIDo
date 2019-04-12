<?php init_head();?>
<div id="wrapper">
  <div class="content manage-income-cateogry">
    <div class="row">
      <div class="col-md-12">                    
              <div class="breadcrumb">
                  <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                  <i class="fa fa-angle-right breadcrumb-arrow"></i>
                  <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                  <i class="fa fa-angle-right breadcrumb-arrow"></i>
                  <span><?php echo _l('breadcrum_income_category_label'); ?></span>
              </div>          
		  <h1 class="pageTitleH1"><i class="fa fa-list-alt"></i><?php echo $title; ?></h1>
          <div class="clearfix"></div>
          <div class="panel_s btmbrd">
          <div class="panel-body">
            <div class="_buttons">
              <?php if (has_permission('lists','','create')) { ?>
                <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#income_category_modal"><?php echo _l('add_income_category_title'); ?></a>
              <?php } ?>
            </div>
            <div class="clearfix"></div>
            <div class="clearfix"></div>
            <?php render_datatable(array(
              _l('Name'),
              _l('')
              ),'income-category'); ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="modal fade" id="income_category_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">
            <span class="add-title"><?php echo _l('add_income_category_title'); ?></span>
          </h4>
        </div>
        <?php echo form_open('admin/invoice_items/add_income_category',array('id'=>'income_category_form')); ?>
          <?php echo form_hidden('id'); ?>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <?php echo render_input('name',_l('income_category_title')); ?>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>

  <?php init_tail(); ?>
  <script>

    function edit_income_category(invoker,id){
      var id = $(invoker).data('id');
      var name = $(invoker).data('name');
      var maincat = $(invoker).data('maincat');
      $('input[name=id]:hidden').val(id);
      $('#name').val(name);
      $("#income_category_modal #myModalLabel").html('Edit Income Category');
    }
    
    $('input[name=id]:hidden').val("");

    function manage_income_category(form) {
      var data = $(form).serialize();
      var url = form.action;
      $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
          $('.table-income-category').DataTable().ajax.reload();
          alert_float('success', response.message);
        } else {
          if(response.message != ''){
            alert_float('warning', response.message);
          }
        }
        $('#expense_category_modal').modal('hide');
        location.reload();
      });
      return false;
    }

    $(function(){

     initDataTable('.table-income-category', window.location.href, [1], [1],'undefined',[0, "ASC" ]);

      _validate_form($('#income_category_form'),{
        name:{
          required:true,
          remote: {
            url: admin_url + "invoice_items/incomecategory_name_exists",
            type: 'post',
            data: {
              id:function(){
                return $('input[name="id"]').val();
              }
            }
          }
        }},
      manage_income_category);
    });
  </script>
</body>
</html>
