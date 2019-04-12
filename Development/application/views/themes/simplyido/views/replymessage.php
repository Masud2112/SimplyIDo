<div class="col-md-12 page-pdf-html-logo">
    <?php get_client_brand_logo('admin', '', $messages->brandid); ?>
    <a href="<?php echo admin_url(); ?>" class="btn btn-info pull-right"><?php echo _l('login_register'); ?></a>
    
</div>
<div class="clearfix"></div>
<div class="panel_s mtop20">
    <div class="panel-body">
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <h4>
            <?php echo $title." - ". $messages->subject; ?>
         </h4>
         <hr class="hr-panel-heading" />
         <?php echo form_open_multipart('clients/replyclientmessage',array('id'=>'replymessage')); ?> 

         <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                  <label for="content" class="control-label">Content <small class="req text-danger">* </small></label>
                  <textarea id="content" name="content" class="form-control message" rows="4" aria-hidden="true"></textarea>
              </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                  <label for="message_to" class="control-label">Message To <small class="req text-danger">* </small></label>
                    <select id="message_to" class="selectpicker" name="message_to[]" data-width="100%" data-none-selected-text="Select Users" multiple data-live-search="true" autofocus="">
                      <optgroup label="Team Member">
                        <?php foreach ($teammember as $t) {
                            $tselected = "";
                            if(in_array("tm_".$t['staffid'], $messages->prefixuser)){
                                $tselected = "selected='selected'";
                            }
                         ?>
                            <option value="tm_<?php echo $t['staffid'] ?>" <?php echo $tselected; ?>><?php echo $t['staff_name'] ?></option>
                        <?php } ?>
                      </optgroup>
                      <optgroup label="Contacts">
                        <?php foreach ($contacts as $c) { 
                            $cselected = "";
                            if(in_array("cn_".$c['addressbookid'], $messages->prefixuser)){
                                $cselected = "selected='selected'";
                            }
                        ?>
                            <option value="cn_<?php echo $c['addressbookid'] ?>" <?php echo $cselected; ?>><?php echo $c['contact_name'] ?></option>
                        <?php } ?>
                      </optgroup>
                   </select>
                    
                </div>
            </div>
            <div class="col-md-6">
              <label><?php echo _l('attach_files'); ?> <i class="fa fa-question-circle" data-toggle="tooltip" data-title="Allowed extensions - <?php echo str_replace('.','',get_option('allowed_files')); ?>"></i></label>
           
              <div id="new-message-attachments">
                 <div class="attachments">
                    <div class="row attachment browseAtt_blk">
                         <div class="col-md-10 browseAtt_input_blk">
                              <div class="form-group">
                                  <div class="input-group" id="attachments[0]">
                                      <span class="input-group-btn">
                                        <span class="btn btn-primary" onclick="$(this).parent().find('input[type=file]').click();">Browse</span>
                                        <input name="attachments[0]" onchange="$(this).parent().parent().find('.form-control').html($(this).val().split(/[\\|/]/).pop());" style="display: none;" filesize="<?php echo file_upload_max_size(); ?>" extension="<?php echo str_replace('.','',get_option('allowed_files')); ?>"  type="file">
                                      </span>
                                      <span class="form-control"></span>
                                </div>
                            </div>
                             
                            </div>
                            <div class="col-md-2">
                            <div class="text-right">
                              <button class="btn btn-primary add_more_attachments" type="button"><i class="fa fa-plus"></i></button>
                            </div>
                            </div>
                    </div>
                 </div>
              </div>
            </div>

            <div class="col-md-12 mtop20 text-right btn-toolbar-container-out">
                  <input type="hidden" name="message_id" value="<?php echo $messages->id; ?>">
                  <input type="hidden" name="brandid" value="<?php echo $messages->brandid; ?>">
                  <input type="hidden" name="contact_id" value="<?php echo $contact_id; ?>">
                  <button class="btn btn-default" type="button" onclick="location.href='<?php echo base_url(); ?>'"><?php echo _l( 'Cancel'); ?></button>
                  <button type="submit" class="btn btn-info">Send</button>
            </div>
         </div>
          <?php echo form_close(); ?>
            </div>

        </div>
    </div>
</div>
</div>
<script>
  $(function() {
   init_editor('.message');
   var validator = $('#replymessage').submit(function() { 
        // update underlying textarea before submit validation
        var content = tinyMCE.activeEditor.getContent();
        $("#content").val(content);        
        tinyMCE.triggerSave();
        if($("#content").val() == ""){
          $(".mce-tinymce").css({'border-color': '#fc2d42'});
        } else {
           $(".mce-tinymce").css({'border-color': ''});
        }
      }).validate({
        ignore: "",
        rules: {
          content: "required",
          'message_to[]':'required'
        }      
      });   
    });
</script>
<style>
.browseAtt_blk .btn-primary{
  background-color:#00a9b9;
}
.browseAtt_blk .btn{
  padding: 8px 12px;
}
.browseAtt_blk .btn-primary.btn-danger, .browseAtt_blk .btn-danger {
    background-color: #fc2d42;
}
</style>