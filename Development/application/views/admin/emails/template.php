<?php init_head(); ?>
<div id="wrapper" class="email-template-page">
  <div class="content">
   <h1 class="pageTitleH1" id="page-title"><i class="fa fa-envelope-o"></i><?php echo $title; ?></h1>
	<div class="clearfix"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s btmbrd">
          <div class="panel-body">          
            <?php echo form_open($this->uri->uri_string(),array('class'=>'email-form')); ?>
              <div class="panel_s">
                <div class="panel-body">
                  <div class="row">
                    <div class="col-md-6">
                      <?php echo render_input('name','template_name',$template->name,'text',array('disabled'=>true)); ?>
                    </div>
                    <div class="col-md-6">
                      <?php echo render_input('subject['.$template->emailtemplateid.']','template_subject',$template->subject); ?>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <?php echo render_input('fromname','template_fromname',$template->fromname); ?>
                    </div>
                    <div class="col-md-6">
                      <?php if($template->slug != 'two-factor-authentication'){ ?>
                        <!--<i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php //echo _l('email_template_only_domain_email'); ?>"></i>-->
                        <?php echo render_input('fromemail','template_fromemail',$template->fromemail,'email'); ?>
                      <?php } ?>
                    </div>
                  </div>
                  <!-- <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="plaintext" id="plaintext" <?php //if($template->plaintext == 1){echo 'checked';} ?>>
                    <label for="plaintext"><?php //echo _l('send_as_plain_text'); ?></label>
                  </div> -->
                  <?php //if($template->slug != 'two-factor-authentication'){ ?>
                    <!-- <div class="checkbox checkbox-primary">
                      <input type="checkbox" name="disabled" id="disabled" <?php //if($template->active == 0){echo 'checked';} ?>>
                      <label data-toggle="tooltip" title="<?php //echo _l('disable_email_from_being_sent'); ?>" for="disabled"><?php //echo _l('email_template_disabled'); ?></label>
                    </div> -->
                  <?php //} ?>
                </div>
              </div>
              <div class="panel_s">
                <div class="">
                  <div class="row">
                    <div class="col-md-6">
                      <?php
                        $editors = array();
                        array_push($editors,'message['.$template->emailtemplateid.']');
                      ?>
                      <!-- <h4 class="bold font-medium">English</h4> -->
                      <p class="bold"><?php echo _l('email_template_email_message'); ?></p>
                      <?php echo render_textarea('message['.$template->emailtemplateid.']','',$template->message,array('data-url-converter-callback'=>'myCustomURLConverter'),array(),'','tinymce tinymce-manual'); ?>
                      <?php 
                        foreach($available_languages as $language) {
                          $lang_template = $this->emails_model->get(array('slug'=>$template->slug,'language'=>$language));
                          if(count($lang_template) > 0) {
                            $lang_used = false;

                            if(get_option('active_language') == $language || total_rows('tblstaff',array('default_language'=>$language)) > 0 || total_rows('tblclients',array('default_language'=>$language)) > 0) {
                              $lang_used = true;
                            }

                            $hide_template_class = '';
                            
                            if($lang_used == false) {
                              $hide_template_class = 'hide';
                            }
                        ?>
                          <hr />
                          <h4 class="font-medium pointer bold" onclick='slideToggle("#temp_<?php echo $language; ?>");'>  <?php echo ucfirst($language); ?></h4>
                          <?php
                            $lang_template = $lang_template[0];
                            array_push($editors,'message['.$lang_template['emailtemplateid'].']');
                            echo '<div id="temp_'.$language.'" class="'.$hide_template_class.'">';
                            echo render_input('subject['.$lang_template['emailtemplateid'].']','template_subject',$lang_template['subject']);
                            echo '<p class="bold">'._l('email_template_email_message').'</p>';
                            echo render_textarea('message['.$lang_template['emailtemplateid'].']','',$lang_template['message'],array('data-url-converter-callback'=>'myCustomURLConverter'),array(),'','tinymce tinymce-manual');
                            echo '</div>';
                          }
                        } 
                      ?>
                    </div>
                     <div class="col-md-6">
                     <div class="tokens">
                        <p class="bold">
                          <?php echo _l('available_merge_fields'); ?>
                        </p>
                        
                        <div class="row">
                          <?php 
                            if($template->type == 'ticket' || $template->type == 'project') { ?>
                              <div class=" col-md-12 tokens-wrap">
                                <?php if($template->type != 'project') { ?>
                                    <div class="alert alert-warning">
                                      <?php 
                                        if($template->type == 'ticket') {
                                          echo _l('email_template_ticket_warning');
                                        } else {
                                          echo _l('email_template_contact_warning');
                                        } 
                                      ?>
                                    </div>
                                <?php } else {
                                  if($template->slug == 'new-project-discussion-comment-to-staff' || $template->slug == 'new-project-discussion-comment-to-customer') { ?>
                                    <div class="alert alert-info">
                                      <?php echo _l('email_template_discussion_info'); ?>
                                    </div>
                                <?php  }
                                  } ?>
                              </div>
                          <?php } ?>
                          <div class="col-md-12 tokens-wrap	">
                            <div class="row available_merge_fields_container">
                              <?php
                                /*foreach($available_merge_fields as $field) {*/
                                  foreach($available_merge_fields as $key => $val) {
                                    echo '<div class="col-md-12 merge_fields_col">';
                                    echo '<h5 class="bold">'.ucfirst($key).'</h5><div class="field-list">';
                                    foreach($val as $_field) {
                                      foreach($_field['available'] as $_available) {
                                        if($_available == $template->type) {
                                          if($template->slug != 'client-statement' && _startsWith($_field['key'],'{statement')) {
                                            continue;
                                          }
                                          echo '<p class="tags">'.$_field['name'].'<span><a href="#" class="add_merge_field">'.$_field['key'].'</a></span></p>';
                                        }
                                      }
                                    }
                                    echo '</div></div>';
                                  }
                                /*}*/
                                if($template->slug == 'new-client-created' || $template->slug == 'new-staff-created') {
                                  echo '<div class="col-md-12 merge_fields_col">';
                                  echo '<h5 class="bold">Password</h5><div class="field-list">';
                                  echo '<p class="tags">Password<span><a href="#" class="add_merge_field">{password}</a></span></p>';
                                  echo '</div></div>';
                                } else if($template->slug == 'two-factor-authentication') {
                                  echo '<div class="col-md-12 merge_fields_col">';
                                  echo '<h5 class="bold">Two Factor Authentication Code</h5><div class="field-list">';
                                  echo '<p class="tags">Authentication Code<span><a href="#" class="add_merge_field">{two_factor_auth_code}</a></span></p>';
                                  echo '</div></div>';
                                }
                              ?>
                            </div>
                          </div>
                        </div>
                     </div>
                    </div>                   
                  </div>
                </div>
              </div>
              <div class="btn-bottom-toolbar text-right">
                <button class="btn btn-default" type="button" onclick="location.href='<?php echo base_url(); ?>admin/emails'"><?php echo _l( 'Cancel'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
              </div>
            <?php echo form_close(); ?>
          </div>
        </div>
      </div>
    </div>
    <div class="btn-bottom-pusher"></div>
  </div>
</div>
<?php init_tail(); ?>
<script>
  $(function(){
    <?php foreach($editors as $id){ ?>
      init_editor('textarea[name="<?php echo $id; ?>"]',{urlconverter_callback:'merge_field_format_url'});
      <?php } ?>
      var merge_fields_col = $('.merge_fields_col');
        // If not fields available
        $.each(merge_fields_col, function() {
          var total_available_fields = $(this).find('p');
          if (total_available_fields.length == 0) {
            $(this).remove();
          }
        });
    // Add merge field to tinymce
    $('.add_merge_field').on('click', function(e) {
     e.preventDefault();
     tinymce.activeEditor.execCommand('mceInsertContent', false, $(this).text());
   });
    _validate_form($('.email-form'), {
      name: 'required',
      fromname: 'required',
    });
  });
</script>
</body>
</html>
