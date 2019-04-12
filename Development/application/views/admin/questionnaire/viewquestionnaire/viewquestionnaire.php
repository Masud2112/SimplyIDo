<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 20-03-2018
 * Time: 17:13
 */
init_head(); ?>
<div id="wrapper" class="questionnaire-page">
    <div class="content">
        <div class="row">
            <?php echo form_open_multipart($this->uri->uri_string(), array('id' => 'questionnaire')); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading"/>
                        <div class="form-group">
                            <?php if (isset($questionnaire)) { ?>
                                <div class="list-unstyled survey_question_callback" id="viewquestionnaire_questions">
                                    <?php
                                    if (isset($questionnaire->questions)) {
                                        if (count($questionnaire->questions) > 0) {

                                            foreach ($questionnaire->questions as $key=>$question) {
                                                $que_data['question'] = $question;
                                                $que_data['queindex'] = $key;
                                                $this->load->view('admin/questionnaire/viewquestionnaire/question', $que_data);
                                            }
                                        }
                                    }
                                    ?></div>
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
                            <button class="btn btn-default" type="button"
                                    onclick="location.href='<?php echo base_url(); ?>admin/questionnaire'"><?php echo _l('Cancel'); ?></button>
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
<?php echo app_script('assets/js', 'questionnaire.js'); ?>
<script>
    //
    $(document).ready(function () {
        _validate_form($('#questionnaire'), {name: 'required'});
    });

</script>
</body>
</html>
