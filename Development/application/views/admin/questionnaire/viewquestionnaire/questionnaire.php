<?php init_head(); ?>
<div id="wrapper" class="questionnaire-page">
   <div class="content">
      <div class="row">
         <?php echo form_open($this->uri->uri_string(), array('id'=>'questionnaire')); ?>
           <div class="col-md-12">
               <div class="panel_s">
                  <div class="panel-body">
                     <h4 class="no-margin">
                        <?php echo $title; ?>
                     </h4>
                     <hr class="hr-panel-heading" />

                     <?php $value = (isset($questionnaire) ? $questionnaire->name : ''); ?>
                     <div class="form-group">
                      <label class="control-label" for="name"><small class="req text-danger">* </small>Name</label>
                      <input id="name" class="form-control" name="name" autofocus="1" value="<?php echo $value; ?>" type="text">
                     </div>
                     <div class="form-group">
                                <?php if(isset($questionnaire)){ ?>
                                    <?php $this->load->view('admin/questionnaire/questionnaire_buttons'); ?>
                                    <div class="_buttons">
                                        <!-- Single button -->
                                        <!-- <a href="<?php //echo site_url('questionnaire/'.$questionnaire->id . '/' . $survey->hash); ?>" target="_blank" class="btn btn-success pull-right mleft10 btn-with-tooltip" data-toggle="tooltip" data-placement="bottom" data-title="<?php //echo _l('survey_list_view_tooltip'); ?>"><i class="fa fa-eye"></i></a> -->
                                        <?php if(has_permission('questionnaire','','edit')){ ?>
                                        <div class="btn-group pull-left">
                                            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <?php echo _l('questionnaire_insert_field'); ?> <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="#" onclick="add_question('checkbox',<?php echo (isset($questionnaire) ? $questionnaire->id : '0'); ?>);return false;">
                                                        <?php echo _l('survey_field_checkbox'); ?></a>
                                                    </li>
                                                    <li>
                                                        <a href="#" onclick="add_question('radio',<?php echo (isset($questionnaire) ? $questionnaire->id : '0'); ?>);return false;">
                                                            <?php echo _l('survey_field_radio'); ?></a>
                                                        </li>
                                                        <li>
                                                            <a href="#" onclick="add_question('input',<?php echo (isset($questionnaire) ? $questionnaire->id : '0'); ?>);return false;">
                                                                <?php echo _l('survey_field_input'); ?></a>
                                                            </li>
                                                            <li>
                                                                <a href="#" onclick="add_question('textarea',<?php echo (isset($questionnaire) ? $questionnaire->id : '0'); ?>);return false;">
                                                                    <?php echo _l('survey_field_textarea'); ?></a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <?php } ?>
                                                    <div class="clearfix"></div>
                                                    <hr />
                         <ul class="list-unstyled survey_question_callback" id="questionnaire_questions">
                                                    <?php
                                                    $question_area = '<ul class="list-unstyled survey_question_callback" id="questionnaire_questions">';
                                                    if(isset($questionnaire->questions)) {
                                                    if(count($questionnaire->questions) > 0){
                                                       foreach($questionnaire->questions as $question){
                                                          $que_data['question'] =$question;
                                                          $this->load->view('admin/questionnaire/question',$que_data);
                                                          $question_area .= '<li>';
                                                          $question_area .= '<div class="form-group question">';
                                                          $question_area .= '<div class="checkbox checkbox-primary required">';
                                                          if($question['required'] == 1){
                                                             $_required = ' checked';
                                                         } else {
                                                             $_required = '';
                                                         }
                                                         $question_area .= '<input type="checkbox" id="req_'.$question['questionid'].'" onchange="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].');" data-question_required="'.$question['questionid'].'" name="required[]" '.$_required.'>';
                                                         $question_area .= '<label for="req_'.$question['questionid'].'">'._l('survey_question_required').'</label>';
                                                         $question_area .= '</div>';
                                                         $question_area .= '<input type="hidden" value="" name="order[]">';
                                                         // used only to identify input key no saved in database
                                                         $question_area .='<label for="'.$question['questionid'].'" class="control-label display-block">'._l('question_string').'
                                                         <a href="#" onclick="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].'); return false;" class="pull-right update-question-button"><i class="fa fa-refresh text-success question_update"></i></a>
                                                         <a href="#" onclick="remove_question_from_database(this,'.$question['questionid'].'); return false;" class="pull-right"><i class="fa fa-remove text-danger"></i></a>
                                                     </label>';
                                                     $question_area .= '<input type="text" onblur="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].');" data-questionid="'.$question['questionid'].'" class="form-control questionid" value="'.$question['question'].'">';
                                                     if($question['boxtype'] == 'textarea'){
                                                      // $question_area .= '<textarea class="form-control mtop20" disabled="disabled" rows="6">'._l('survey_question_only_for_preview').'</textarea>';
                                                  } else if($question['boxtype'] == 'checkbox' || $question['boxtype'] == 'radio'){
                                                      $question_area .= '<div class="row">';
                                                      $x = 0;
                                                      foreach($question['box_descriptions'] as $box_description){
                                                         $box_description_icon_class = 'fa-minus text-danger';
                                                         $box_description_function = 'remove_box_description_from_database(this,'.$box_description['questionboxdescriptionid'].'); return false;';
                                                         if($x == 0){
                                                            $box_description_icon_class = 'fa-plus';
                                                            $box_description_function = 'add_box_description_to_database(this,'.$question['questionid'].','.$question['boxid'].'); return false;';
                                                        }
                                                        $question_area .= '<div class="box_area">';

                                                        $question_area .= '<div class="col-md-12">';
                                                        $question_area .= '<a href="#" class="add_remove_action survey_add_more_box" onclick="'.$box_description_function.'"><i class="fa '.$box_description_icon_class.'"></i></a>';
                                                        $question_area .= '<div class="'.$question['boxtype'].' '.$question['boxtype'].'-primary">';
                                                        $question_area .= '<input type="'.$question['boxtype'].'" disabled="disabled"/>';
                                                        $question_area .= '
                                                        <label>
                                                            <input type="text" onblur="update_question(this,\''.$question['boxtype'].'\','.$question['questionid'].');" data-box-descriptionid="'.$box_description['questionboxdescriptionid'].'" value="'.$box_description['description'].'" class="input_box_description">
                                                        </label>';
                                                        $question_area .= '</div>';
                                                        $question_area .= '</div>';
                                                        $question_area .= '</div>';
                                                        $x++;
                                                    }
                                            // end box row
                                                    $question_area .= '</div>';
                                                } else {
                                                  // $question_area .= '<input type="text" class="form-control mtop20" disabled="disabled" value="'._l('survey_question_only_for_preview').'">';
                                              }
                                              $question_area .= '</div>';
                                              $question_area .= '</li>';
                                          }
                                      }
                                    }
                                      $question_area .= '</ul>';
                                      //echo $question_area;
                                      ?></ul>
                                      <?php } //else { ?>
                                      <!-- <p class="no-margin"><?php //echo _l('survey_create_first'); ?></p> -->
                                      <?php //} ?>
                                  </div>
                    <!--  <?php //$content = (isset($agreement) ? $agreement->content : ''); ?>
                     <div class="form-group">
                        <label for="content" class="control-label"> <small class="req text-danger">* </small>Content</label>
                        <div id="agreement_wrapper">
                           <textarea id="content" name="content"><?php //echo $content; ?></textarea>
                        </div>
                     </div> -->

                     <div class="text-right btn-toolbar-container-out">
                        <button class="btn btn-default" type="button" onclick="location.href='<?php echo base_url(); ?>admin/questionnaire'"><?php echo _l( 'Cancel'); ?></button>
                        <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                     </div>
                  </div>
               </div>
            </div>
            <?php echo form_close(); ?>
      </div>
   </div>
</div>
<?php init_tail(); ?>
<?php echo app_script('assets/js','questionnaire.js'); ?>
<script>
   //
   $(document).ready(function(){
    _validate_form($('#questionnaire'),{name:'required'});
   });

</script>
</body>
</html>