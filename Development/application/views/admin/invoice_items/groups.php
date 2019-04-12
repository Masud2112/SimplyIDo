<?php init_head(); ?>
<div id="wrapper">
  <div class="content manage-package-page">
    <div class="row">
      <div class="col-md-12">                    
              <div class="breadcrumb">
                  <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                  <i class="fa fa-angle-right breadcrumb-arrow"></i>
                  <a href="<?php echo admin_url('setup'); ?>"><?php echo _l('breadcrum_setting_label'); ?></a>
                  <i class="fa fa-angle-right breadcrumb-arrow"></i>
                  <span>Packages</span>
              </div>          
		  <h1 class="pageTitleH1"><i class="fa fa-dollar "></i><?php echo $title; ?></h1>
          <div class="clearfix"></div>
          <div class="panel_s btmbrd">
          <div class="panel-body">
           <?php if(has_permission('items','','create')){ ?>
           <div class="_buttons">
            <a href="<?php echo admin_url('invoice_items/package'); ?>" class="btn btn-info mleft5 pull-left display-block"><?php echo _l('new_package'); ?></a>   
          </div>
          <div class="clearfix"></div>
          <?php }  
				 if(is_mobile()) { echo '<a class="btn btn-primary  filter_btn_search"><i class="glyphicon glyphicon-search"></i></a>'; } 
              $column_setting_data = $vcols;
              if(isset($column_setting_data) && $column_setting_data !=""){
                $a = explode(',',$column_setting_data); ?>
                <?php if(!in_array('cost', $a)){ ?> 
                  <style type="text/css"> 
                    tr > th:nth-child(2), tr > td:nth-child(2) {display: none;}</style>
                <?php } ?>
                <?php if(!in_array('price', $a)){ ?> 
                  <style type="text/css"> tr > th:nth-child(3), tr > td:nth-child(3) {display: none;}</style>
                <?php } ?>
                <?php if(!in_array('profit', $a)){ ?> 
                  <style type="text/css"> tr > th:nth-child(4), tr > td:nth-child(4) {display: none;}</style>
            <?php } } ?>

          <?php render_datatable(array(
              _l('item_group_name'),
              _l('Cost ($)'),
              _l('Price ($)'),
              _l('Profit ($)'),
              _l(''),
              ),'groups'); ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<?php
  $options = array(/*'page_id' =>$item->id ,*/'page_type'=>'package_list' );
  $data['options'] = $options;
  $data['vcols'] = explode(',',$column_setting_data);
  $this->load->view('admin/invoice_items/group_options',$data); 

?>
<?php 
  $this->load->view('admin/invoice_items/group_duplicate'); 

?>
<?php init_tail(); ?>
<script>

  function duplicate_status(invoker){
    var id = $(invoker).data('id');
    $('#duplicate_record_id').val(id);
    $('#additional').append(hidden_input('duplicate_record_id',id));
  }
  $(function(){
    initDataTable('.table-groups', window.location.href, [], [4],'undefined',[0,'ASC']);

    $('div.dataTables_filter .input-group').append( '<div class="pull-right"><a href="#" class="btn-info display-block mleft5 padding-5" data-toggle="modal" data-target="#display_column" id="display_column_popup" style="padding: 5px 12px;border-radius: 3px;"><i class="fa fa-cog" aria-hidden="true"></i></a></div>' );
    $('div.dataTables_filter input').css('border-radius','1');
    
    $('.brand_list_section').hide();
    $('.duplicate_by_brand').on('click',function(){
      if($("#duplicate_by_existing_brand").prop('checked') == true){
        $('.brand_list_section').show();
      }else{
        $('.brand_list_section').hide();
      }
    });
	  
	  
		<?php if(is_mobile()){ ?>
			$('#DataTables_Table_0_filter, .leads-search').hide();
			$(".filter_btn_search").click(function(){				
				$('#DataTables_Table_0_filter, .leads-search').toggle();
			});
		<?php } ?>
		
  
  });
	
	
	
</script>

</body>
</html>
