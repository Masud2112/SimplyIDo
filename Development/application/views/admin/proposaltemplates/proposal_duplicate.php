<!--
  * Added by: Masud
  * Date: 02-13-2018
  * Popup to display option for duplicate product & service in current brand or existing brands.
  -->
<div class="modal fade" id="duplicate_proposal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    Duplicate Proposal
                </h4>
            </div>
            <div class="modal-body">
                <?php if(has_permission('items','','create')){
                    if(isset($_GET['pid']) && $_GET['pid']>0 ){
                        $rel_type="project";
                        $rel_id=$_GET['pid'];
                    }elseif(isset($_GET['lid']) && $_GET['lid']>0 ){
                        $rel_type="lead";
                        $rel_id=$_GET['lid'];
                    }else{
                        $rel_type="";
                        $rel_id="";
                    }
                    ?>
                    <div class="row">
                        <?php echo form_open('admin/proposaltemplates/duplicate_proposal',array('novalidate'=>true,'id'=>'pro_service_duplicate')); ?>
                        <input type="hidden" name="rel_type" value="<?php echo $rel_type; ?>">
                        <input type="hidden" name="rel_id" value="<?php echo $rel_id; ?>">
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