<?php init_head(); ?>
<div id="wrapper" class="questionnaire-page">
    <div class="content">
        <div class="row">
            <?php echo form_open($this->uri->uri_string(), array('id' => 'questionnaire')); ?>
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin pull-left">
                            <?php echo $title; ?>
                        </h4>
                        <div class=" pull-right topButton text-right btn-toolbar-container-out">
                            <?php if(isset($questionnaire)){ ?>
                                <a class="btn btn-default" type="button" href='<?php echo base_url(); ?>admin/questionnaire/viewquestionnaire/<?php echo $questionnaire->id?>?preview=true' target="_blank" title="Preview"><i class="fa fa-eye"></i></a>
                            <?php } ?>
                            <button class="btn btn-default" type="button"
                                    onclick="location.href='<?php echo base_url(); ?>admin/questionnaire'"><?php echo _l('Cancel'); ?></button>
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                        </div>
                        <hr class="hr-panel-heading"/>

                        <?php $value = (isset($questionnaire) ? $questionnaire->name : ''); ?>
                        <div class="form-group">
                            <label class="control-label" for="name">
                                <small class="req text-danger">*</small>
                                Name</label>
                            <input id="name" class="form-control" name="name" autofocus="1"
                                   value="<?php echo $value; ?>" type="text">
                        </div>
                        <div class="form-group">
                            <?php if (isset($questionnaire)) { ?>
                                <?php if (has_permission('questionnaire', '', 'edit')) { ?>
                                    <?php $this->load->view('admin/questionnaire/questionnaire_buttons'); ?>
                                <?php } ?>
                                <div class="clearfix"></div>
                                <hr/>
                                <ul class="list-unstyled survey_question_callback" id="questionnaire_questions">
                                    <?php
                                    $question_area = '';
                                    if (isset($questionnaire->questions)) {
                                        if (count($questionnaire->questions) > 0) {
                                            foreach ($questionnaire->questions as $index=>$question) {
                                                $que_data['question'] = $question;
                                                $que_data['qindex'] = $index;
                                                $this->load->view('admin/questionnaire/question', $que_data);
                                            }
                                        }
                                    }
                                    ?>
                                </ul>
                            <?php } ?>
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