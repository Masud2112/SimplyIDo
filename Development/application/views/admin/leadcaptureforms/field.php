<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 20-03-2018
 * Time: 15:58
 */
$buttons = array('heading' => 'Heading', 'columns' => 'Columns', 'text_box' => 'Text Box', 'image' => 'Image', 'short_answer' => 'Short Answer', 'long_answer' => 'Long Answer', 'date' => 'Date Select', 'file' => 'File Uploader', 'yn_question' => 'Yes/No Question', 'select' => 'Dropdown', 'checkbox' => 'Checkboxes', 'radio' => 'Single Choice');
/*$buttons[]= array('boxtype'=>'code_block','name'=>'Code Block','icon'=>'fa-code');
$buttons[]= array('boxtype'=>'group','name'=>'Group','icon'=>'fa-link');
$buttons[]= array('boxtype'=>'introduction_block','name'=>'Introduction Block','icon'=>'fa-users');
$buttons[]= array('boxtype'=>'custom_block','name'=>'Custom Block','icon'=>'fa-cubes');*/
if ($field['required'] == 1) {
    $_required = ' checked';
} else {
    $_required = '';
}
$x = 0;
?>
    <div id="field_<?php echo $qindex; ?>" draggable="true" class="field">
        <div class="form-group">
            <input type="hidden" value="" name="order[]">
            <label for="<?php echo $field['questionid'] ?>" class="control-label question_type display-block">
                <!--<span><?php /*echo isset($buttons[$field['boxtype']]) ? $buttons[$field['boxtype']] : _l('question_string') */ ?></span>-->
                <!--<a href="javascript:void(0)" class="pull-right question_body_toggle"><i class="fa fa-caret-up"></i></a>-->
                <a href="javascript:void(0)"
                   onclick="remove_question_from_database(this,'<?php echo $field['questionid'] ?>'); return false;"
                   class="pull-right"><i class="fa fa-trash-o text-danger"></i></a>
                <!--<a href="javascript:void(0)" onclick="" class="pull-right"><i class="fa fa-pencil"></i></a>-->
                <!--<a href="javascript:void(0)"
               onclick="duplicate_question(<?php /*echo $field['questionid'] */ ?>,<?php /*echo $qindex; */ ?>)"
               class="pull-right"><i class="fa fa-files-o"></i></a>-->
                <!--<a href="javascript:void(0)"
               onclick="update_question(this,'<?php /*echo $field['boxtype'] */ ?>',<?php /*echo $field['questionid'] */ ?>); return false;"
            class="pull-right update-question-button"><i class="fa fa-refresh text-success question_update"></i></a>-->
            </label>
            <div class="question_body">
                <?php if ($field['boxtype'] != 'heading' && $field['boxtype'] != 'image' && $field['boxtype'] != 'text_box') { ?>
                    <div class="checkbox checkbox-primary required">
                        <input type="checkbox" id="req_<?php echo $field['questionid'] ?>"
                               onchange="update_question(this,<?php echo $field['boxtype'] ?>,<?php echo $field['questionid'] ?>);"
                               data-question_required="<?php echo $field['questionid'] ?>"
                               name="required[]" <?php echo $_required ?> />
                        <label for="req_<?php echo $field['questionid'] ?>"><?php echo _l('survey_question_required') ?></label>
                    </div>
                    <input type="text"
                           onblur="update_question(this,'<?php echo $field['boxtype'] ?>',<?php echo $field['questionid'] ?>);"
                           data-questionid="<?php echo $field['questionid'] ?>" class="form-control questionid"
                           value="<?php echo $field['question'] ?>">
                <?php } ?>
                <?php if ($field['boxtype'] == 'image') {
                    $desriptionid = $field['box_descriptions'][0]['questionboxdescriptionid'];
                    $image = $field['box_descriptions'][0]['description'];
                    ?>
                    <div class="image">
                        <input type="hidden" data-questionid="<?php echo $field['questionid'] ?>"
                               class="form-control questionid" value="<?php echo $field['question'] ?>">
                        <div class="imageview <?php echo empty($image) ? 'hidden' : ''; ?>">
                            <img src="<?php echo base_url() ?>uploads/leadcaptureforms/image/<?php echo $field['questionid'] ?>/<?php echo $image; ?>"/>
                            <a class="clicktoaddimage" href="javascript:void(0)"><span><i
                                            class="fa fa-pencil"></i></span></a>
                        </div>
                        <div class="clicktoaddimage <?php echo !empty($image) ? 'hidden' : ''; ?>">
                            <div class="drag_drop_image">
                                <span class="icon"><i class="fa fa-image"></i></span>
                                <span>Drag and Drop or Click here to add image</span>
                            </div>
                            <input type="file" class="" name="question_image"
                                   onchange="upload_image(this,'<?php echo $field['boxtype'] ?>',<?php echo $field['questionid'] ?>,<?php echo $desriptionid ?>,'<?php echo $image ?>');"/>
                        </div>

                    </div>
                <?php } else { ?>
                    <!--<input type="text"
                       onblur="update_question(this,'<?php /*echo $field['boxtype']*/ ?>',<?php /*echo $field['questionid'] */ ?>);"
                       data-questionid="<?php /*echo $field['questionid']  */ ?>" class="form-control questionid"
                       value="<?php /*echo $field['question'] */ ?>">-->
                <?php } ?>
                <?php if ($field['boxtype'] == 'checkbox' || $field['boxtype'] == 'radio' || $field['boxtype'] == "select") { ?>
                    <div class="row">
                        <?php
                        if (isset($field['box_descriptions'])) {
                            foreach ($field['box_descriptions'] as $box_description) {
                                $box_description_icon_class = 'fa-minus text-danger';
                                $box_description_function = 'remove_box_description_from_database(this,' . $box_description['questionboxdescriptionid'] . '); return false;';
                                if ($x == 0) {
                                    $box_description_icon_class = 'fa-plus';
                                    $box_description_function = 'add_box_description_to_database(this,' . $field['questionid'] . ',' . $field['boxid'] . '); return false;';
                                }
                                ?>
                                <div class="box_area">
                                    <div class="col-md-3">
                                        <a href="#" class="add_remove_action survey_add_more_box"
                                           onclick="<?php echo $box_description_function ?>"><i
                                                    class="fa <?php echo $box_description_icon_class ?>"></i></a>
                                        <div class="<?php echo $field['boxtype'] ?> <?php $field['boxtype'] ?>-primary">
                                            <?php if ($field['boxtype'] != "select") { ?>
                                                <input type="<?php echo $field['boxtype'] ?>" disabled="disabled"/>
                                            <?php } ?>
                                            <label>
                                                <input type="text"
                                                       onblur="update_question(this,'<?php echo $field['boxtype'] ?>',<?php echo $field['questionid'] ?>);"
                                                       data-box-descriptionid="<?php echo $box_description['questionboxdescriptionid'] ?>"
                                                       value="<?php echo $box_description['description'] ?>"
                                                       class="input_box_description">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $x++;
                            }
                        } ?>
                    </div>
                <?php } elseif ($field['boxtype'] == 'heading') { ?>
                    <div class="box_area">
                        <div class="mtop10">
                        <textarea id="inlineEditor_<?php echo $field['questionid']; ?>"
                                  onload="initeditor('inlineEditor_<?php echo $field['questionid']; ?>')">
                            <?php echo !empty($field['question']) ? $field['question'] : strtoupper(_l('heading')) ?>
                        </textarea>
                        </div>
                    </div>
                <?php } elseif ($field['boxtype'] == 'text_box') { ?>
                    <div class="box_area">
                        <div class="mtop10">
                        <textarea id="inlineEditor_<?php echo $field['questionid']; ?>">
                            <?php echo !empty($field['question']) ? $field['question'] : strtoupper(_l('sample_text')) ?>
                        </textarea>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php if ($field['boxtype'] == 'heading' || $field['boxtype'] == 'text_box' || $field['boxtype'] == 'text_box') { ?>
    <script src="http://172.16.1.51/sidoleadcaptureform/Development/assets/plugins/ckeditor/ckeditor.js"></script>
    <script type="text/javascript">
        var eid = "inlineEditor_<?php echo $field['questionid']; ?>";
        CKEDITOR.disableAutoInline = true;
        CKEDITOR.inline(eid, {

            toolbar: [
                {name: 'document', groups: ['mode', 'document', 'doctools'], items: ['Source', '-']},
                {name: 'styles', items: ['Font', 'FontSize', 'Format',]},
                {name: 'colors', items: ['TextColor', 'BGColor']},
                {
                    name: 'basicstyles',
                    groups: ['basicstyles', 'cleanup'],
                    items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat']
                },
                {
                    name: 'paragraph',
                    groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'links'],
                    items: ['Outdent', 'Indent', 'Blockquote', 'CreateDiv', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'BidiLtr', 'BidiRtl', 'Language', 'Link', 'Unlink', 'Anchor', 'NumberedList', 'BulletedList', 'Image']
                },
            ],
            removeButtons: 'HorizontalRule,Table,PageBreak,Iframe,Language,BidiRtl,BidiLtr,Outdent,Indent,RemoveFormat,Blockquote,Smiley,Strike,Subscript,Superscript,Anchor,help,about',
            image2_alignClasses: ['image-align-left', 'image-align-center', 'image-align-right'],
            image2_disableResizer: true,
            extraPlugins: 'autogrow',
        });
    </script>
<?php } ?>