<?php init_head(); ?>
<div id="wrapper" class="leadcaptureform-page">
    <div class="content">
        <div class="row">
            <?php echo form_open($this->uri->uri_string(), array('id' => 'leadcaptureform')); ?>
            <div class="col-md-12">
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('setup'); ?>"><?php echo _l('settings') ?></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('leadcaptureforms'); ?>"><?php echo _l('forms') ?></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span><?php echo isset($task) ? $task->name : "New" ?></span>
                </div>

                <h1 class="pageTitleH1"><i class="fa fa-list-ul"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <?php $value = (isset($form) ? $form->name : _l('newleadcaptureform')); ?>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input id="formname" class="form-control" name="name" autofocus="1"
                                           value="<?php echo $value; ?>" type="text"
                                           placeholder="<?php echo _l('newleadcaptureform') ?>"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="pull-right">
                                    <a id="formpreview" class="btn btn-info disabled" type="button"
                                       href='<?php echo base_url(); ?>admin/leadcaptureform/viewquestionnaire?preview=true'
                                       target="_blank" title="Preview">
                                        <i class="fa fa-eye mright5"></i>
                                        <?php echo _l('preview') ?>
                                    </a>
                                    <a class="btn btn-active" type="button"
                                       href='javascript:void(0)'
                                       target="_blank" title="Form Settings">
                                        <i class="fa fa-cogs" aria-hidden="true"></i>
                                    </a>
                                    <?php
                                    if (isset($form)) {
                                        $options = "<div class='inline-block'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>";

                                        $options .= '<li><a href=' . admin_url() . 'leadcaptureforms/clone/' . $form->id . ' class="" title="' . _l("edit") . '"><i class="fa fa-clone"></i><span>' . _l("duplicate") . '</span></a></li>';

                                        $options .= '<li><a href=' . admin_url() . 'leadcaptureforms/delete/' . $form->id . ' class="_delete" title="' . _l("delete") . '"><i class="fa fa-trash"></i><span>' . _l("delete") . '</span></a></li>';

                                        $options .= "</ul></div>";
                                        echo $options;
                                    }

                                    ?>
                                </div>
                            </div>
                        </div>

                        <?php $this->load->view('admin/leadcaptureforms/formtools'); ?>
                        <?php /*if (isset($form)) { */?><!--
                            <?php /*if (has_permission('questionnaire', '', 'edit')) { */?>
                                <?php /*$this->load->view('admin/leadcaptureforms/formtools'); */?>
                            --><?php /*} */?>
                            <div class="clearfix"></div>
                            <hr/>
                            <ul class="list-unstyled survey_question_callback" id="form_field">
                                <?php
                                $question_area = '';
                                if (isset($form->questions)) {
                                    if (count($form->questions) > 0) {
                                        foreach ($form->questions as $index => $question) {
                                            $que_data['question'] = $question;
                                            $que_data['qindex'] = $index;
                                            $this->load->view('admin/leadcaptureforms/field', $que_data);
                                        }
                                    }
                                }
                                ?>
                            </ul>
                        <?php /*}*/ /*else {
                            $this->load->view('admin/leadcaptureforms/defaultformfields');

                        }*/ ?>
                        <div class=" pull-right topButton text-right btn-toolbar-container-out">
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