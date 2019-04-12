<?php init_head(); ?>

<?php
if(isset($vcols) && $vcols != ""){
    $vcols = explode(',', $vcols);
}else{$vcols= array('qty','tax','subtotal','price','profit','cost' );}
?>
<div id="wrapper">
    <div class="content package-group-page">
        <div class="row">
            <?php echo form_open_multipart($this->uri->uri_string(),array('class'=>'group-form','id'=>'manage-group-form','autocomplete'=>'off')); ?>
            <div class="col-md-12">
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('setup'); ?>"><?php echo _l('breadcrum_setting_label'); ?></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('invoice_items/packages'); ?>">Packages</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php if(isset($item)) { ?>
                        <span><?php echo $item->name; ?></span>
                    <?php } else { ?>
                        <span>New Package</span>
                    <?php } ?>
                </div>

                <h1 class="pageTitleH1"><i class="fa fa-dollar "></i><?php echo $title; ?><?php if(isset($item)){ ?>
                        <?php echo form_hidden('itemid',$item->id); ?>
                    <?php } ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <h4 class="hide">
                            <?php echo $title; ?>

                        </h4>


                        <div class="row">
                            <?php $name=( isset($item) ? $item->name : ''); ?>
                            <?php $sku=( isset($item) ? $item->group_sku : ''); ?>
                            <div class = "col-sm-6">
                                <?php echo render_input('name','invoice_item_add_edit_description',$name,'text'); ?>
                                <?php echo render_input('group_sku','line_item_sku_label_title',$sku,'text'); ?>
                            </div>
                            <div class = "col-sm-6">
                                <div class = "row">
                                    <?php if(isset($item) && $item->group_image != NULL){ ?>
                                        <div class="col-md-4">
                                            <div class="package_image">
                                                <?php echo group_image($item->id,array('group_image','img-responsive','item-profile-image-thumb'),'thumb'); ?>
                                            </div>
                                            <!-- <div class="col-md-2 text-right">
                      <a href="<?php echo admin_url('invoice_items/remove_group_image/'.$item->id); ?>"><i class="fa fa-remove"></i></a>
                      </div> -->
                                        </div>
                                    <?php } ?>
                                    <?php //if((isset($item) && $item->group_image == NULL) || !isset($item)){ ?>
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label for="profile_image" class="profile-image"><?php echo _l('group_image'); ?></label>
                                            <!-- <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('profile_dimension'); ?>"></i> -->
                                            <div class="input-group">
                                                <span class="form-control"></span>
                                                <span class="input-group-btn">
                            <span class="btn btn-primary" onclick="$(this).parent().find('input[type=file]').click();">Browse</span>
                            <input name="group_image" onchange="$(this).parent().parent().find('.form-control').html($(this).val().split(/[\\|/]/).pop());" style="display: none;" type="file">
                                                    <?php if(isset($item) && $item->group_image != NULL){ ?>
                                                        <a href="<?php echo admin_url('invoice_items/remove_group_image/'.$item->id); ?>" class="btn btn-primary"><i class="fa fa-remove"></i>REMOVE</a>
                                                    <?php } ?>
                          </span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php // } ?>
                                </div>
                            </div>
                        </div>
                        <?php $description=( isset($item) ? $item->group_description : ''); ?>
                        <div class="form-group">
                            <label for="long_description" class="control-label"> <?php echo _l('invoice_item_long_description'); ?> </label>
                            <textarea id="long_description" name="group_description" class="form-control long_description" rows="4" aria-hidden="true"><?php echo $description ?></textarea>
                        </div>
                        <div class="clearfix mbot15"></div>
                        <div class="package_items_section">
                            <div class="row">
                                <div class="col-sm-8"><h4><i class="fa fa-list-ul"></i>Package Items</h4></div>
                                <div class="col-sm-4 text-right">
                                    <div class="additem-dropdown">
                                        <select name="add_item_to_package" class="selectpicker no-margin ajax-search" data-width="100%" id="add_item_to_package" data-none-selected-text="<?php echo _l('add_item'); ?>" data-live-search="true">
                                            <option value="">Add Product / Service</option>
                                            <?php if(isset($item)){ ?>
                                                <option value="newitem-<?php echo $item->id ?>"><i class="fa fa-plus"></i>New Product / Service</option>
                                            <?php } ?>
                                            <?php foreach ($product_service_groups as $group): ?>
                                                <optgroup label="<?php echo $group['parent_category']." >> ".$group['name']; ?>">
                                                    <?php
                                                    $product_services = get_product_services_by_cat_id($group['id']);
                                                    foreach ($product_services as $ps) { ?>
                                                        <option value="<?php echo $ps->id ; ?>"><?php echo $ps->description ; ?></option>
                                                    <?php } ?>
                                                </optgroup>
                                            <?php endforeach ?>
                                        </select></div>
                                    <div class="setting-btn"><a href="#" class="btn-info" data-toggle="modal" data-target="#display_column" id="display_column_popup" ><i class="fa fa-cog" aria-hidden="true"></i></a></div>
                                </div>
                            </div>
                            <div id ="package_items" class="package_items">
                                <div class="table-responsive">
                                    <div class="table-wrap">
                                        <div class="package_items_header">
                                            <div class="row header">
                                                <div class="col-xs-1"></div>
                                                <div class="col-xs-4">Name</div>
                                                <div class="col-xs-1 qty-col <?php echo in_array('qty', $vcols)?'package_col visibility_visible':'package_col' ?>">Qty.</div>
                                                <div class="col-xs-1 cost-col <?php echo in_array('cost', $vcols)?'package_col visibility_visible':'package_col' ?>">Cost</div>
                                                <div class="col-xs-1 price-col <?php echo in_array('price', $vcols)?'package_col visibility_visible':'package_col' ?>">Price</div>
                                                <div class="col-xs-1 subtotal-col <?php echo in_array('subtotal', $vcols)?'package_col visibility_visible':'package_col' ?>">Subtotal</div>
                                                <div class="col-xs-1 tax-col <?php echo in_array('tax', $vcols)?'package_col visibility_visible':'package_col' ?>">Tax*</div>
                                                <div class="col-xs-1 profit-col <?php echo in_array('profit', $vcols)?'package_col visibility_visible':'package_col' ?>">Profit</div>
                                                <div class="col-xs-1">Action</div>
                                            </div>
                                        </div>
                                        <div id ="package_item_list" class="sortable">
                                            <?php if(isset($item->group_items) && $item->group_items != ""){
                                                $pitems = json_decode($item->group_items);
                                                if(count($pitems) > 0){
                                                    foreach ($pitems as $pitemid => $pitem) {
                                                        $CI =& get_instance();
                                                        $product= $CI->invoice_items_model->get($pitemid);
                                                        $product->qty = $pitem->qty;
                                                        $product->subtotal = $pitem->subtotal;
                                                        $data['product'] = $product;
                                                        $data['vcols'] = $vcols;
                                                        $this->load->view('admin/invoice_items/group_item',$data);
                                                    }}else{
                                                    echo "<p class='add_product_msg text-center mtop30 mbot30'>"._l('no_items_in_package')."<p>";
                                                }
                                                ?>
                                            <?php }else{
                                                echo "<p class='add_product_msg text-center mtop30 mbot30'>"._l('no_items_in_package')."<p>";
                                            } ?>
                                        </div>
                                        <div class="package_items_footer package_items_header" style="min-height: 40px;">
                                            <div class="row header">
                                                <div class="col-xs-1"></div>
                                                <div class="col-xs-4"></div>
                                                <div class="col-xs-1"></div>
                                                <?php $dnone=""; if(isset($item->manual_entry) && $item->manual_entry==1){ $dnone = "ptdnone"; } ?>
                                                <div class="col-xs-1 <?php echo $dnone?>">
                                                    <span>$</span><span class="gcost"><?php if(isset($item->group_cost)){ echo $item->group_cost; } ?></span>

                                                </div>
                                                <div class="col-xs-1"></div>
                                                <div class="col-xs-1 <?php echo $dnone?>">
                                                    <span>$</span><span class="gsubtotal"><?php if(isset($item->group_price)){echo $item->group_price; }?></span>
                                                </div>
                                                <div class="col-xs-1"></div>
                                                <div class="col-xs-1 <?php echo $dnone?>">
                                                    <span>$</span><span class="gprofit"><?php if(isset($item->group_profit)){ echo $item->group_profit; } ?></span>
                                                </div>
                                                <div class="col-xs-1"></div>
                                            </div>
                                        </div>
                                        <?php //if($item->group_cost){} ?>

                                    </div>
                                </div>
                                <div class="package_items_total pull-right" style="">
                                    <input type="checkbox" name="manual_entry" id = "manual_entry" value="1" <?php if(isset($item->manual_entry) && $item->manual_entry==1){ echo "checked"; } ?>>
                                    <label for="manual_entry"><?php echo _l('manual_entry'); ?></label>
                                    <div class="package_total_header">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <h4><strong><?php echo _l('package_price');?></strong></h4>
                                            </div>
                                            <div class="col-sm-6 package_total_val">
                                                
                                                <div class="input-group">
                                                    <span class="input-group-addon" ><i class="fa fa-usd" aria-hidden="true"></i></span>
                                                <input type="text" class="form-control package_total" name="package_total" value="<?php if(isset($item->group_price)){echo $item->group_price; }?>" <?php if(isset($item->manual_entry) && $item->manual_entry==0 || !isset($item) ){ echo "readonly"; } ?>>

                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="package_total_inner">
                                        <div class="row">
                                            <div class="col-sm-6"><h5><strong><?php echo _l('package_cost');?></strong></h5></div>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <span class="input-group-addon" ><i class="fa fa-usd" aria-hidden="true"></i></span>
                                                    <input type="text" class="form-control package_cost_total" name="package_cost_total" value="<?php if(isset($item->group_cost)){ echo $item->group_cost; } ?> " readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6"><h5><strong><?php echo _l('package_profit');?></strong></h5></div>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                        <span class="input-group-addon" ><i class="fa fa-usd" aria-hidden="true"></i></span>
                                                    <input type="text" class ="form-control package_profit" name="package_profit" value="<?php if(isset($item->group_profit)){ echo $item->group_profit; } ?>" readonly >
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="topButton">
                            <button class="btn btn-default" type="button" onclick="fncancel();"><?php echo _l( 'Cancel'); ?></button>
                            <button type="submit" class="btn btn-info package_save"><?php echo _l('submit'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php
if(isset($item)) {
    $options = array('page_id' => $item->id, 'page_type' => 'package');
    $data['options'] = $options;
    $data['vcols'] = $vcols;
    $this->load->view('admin/invoice_items/group_options', $data);
}
?>
<?php init_tail(); ?>
<script>
    init_editor('.long_description');
    function fncancel(){
        location.href='<?php echo base_url(); ?>admin/invoice_items/packages';
    }
    $(function(){
        _validate_form($('form.group-form'),{
            name:{required:true},
        });

    });


    $( ".sortable" ).sortable();
    $('#manual_entry').change(function(){
        if($(this).prop('checked')==true){
            $('.package_total').attr('readonly',false);
            $('.package_items_footer.package_items_header span').fadeOut(500);
        }else{
            package_calculation();
            $('.package_total').attr('readonly',true);
            $('.package_items_footer.package_items_header span').fadeIn(500);

        }
    });
    $('.package_save').click(function(e){
        var package_total = $('.package_total').val();
        var psubtotal=0;
        $('.package_item').each(function(){
            psubtotal= parseFloat(psubtotal) + parseFloat($('.subtotal .subtotal',this).val());
        });
        if( package_total < psubtotal){
            e.preventDefault();
            alert('Sum of all the displayed Sub-Total values is greater than the Package Price.')
        }
    });

    var validator = $("#manage-group-form").validate({
        rules: {
            name:{
                required:true,
                remote: {
                    url: admin_url + "invoice_items/package_name_exists",
                    type: 'post',
                    data: {
                        tagid:function(){
                            return <?php echo  isset($item)? $item->id:''?>;
                        }
                    }
                }
            },
            group_sku:{
                remote: {
                    url: admin_url + "invoice_items/group_sku_name_exists/<?php echo  isset($item)? $item->id:''?>",
                    type: 'post',
                    data: {
                        tagid:function(){
                            return <?php echo  isset($item)? $item->id:''?>;
                        }
                    }
                }},
        },
        messages: {
            sku: "The sku already exist",

        }
    });
</script>
