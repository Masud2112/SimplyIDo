<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 20-03-2018
 * Time: 15:58
 */
$buttons = array('heading' => 'Heading', 'columns' => 'Columns', 'text_box' => 'Text Box', 'image' => 'Image', 'short_answer' => 'Short Answer', 'long_answer' => 'Long Answer', 'date' => 'Date Select', 'file' => 'File Uploader', 'yn_question' => 'Yes/No Question', 'select' => 'Dropdown', 'checkbox' => 'Checkboxes', 'radio' => 'Single Choice');
/*$buttons[]= array('type'=>'code_block','name'=>'Code Block','icon'=>'fa-code');
$buttons[]= array('type'=>'group','name'=>'Group','icon'=>'fa-link');
$buttons[]= array('type'=>'introduction_block','name'=>'Introduction Block','icon'=>'fa-users');
$buttons[]= array('type'=>'custom_block','name'=>'Custom Block','icon'=>'fa-cubes');*/
if ($question['required'] == 1) {
    $_required = ' checked';
} else {
    $_required = '';
}
$x = 0;
?>
<li draggable="true">
    <div class="form-group question">
        <input type="hidden" value="" name="order[]">
        <label for="<?php echo $question['questionid'] ?>" class="control-label display-block">
            <?php echo isset($buttons[$question['boxtype']]) ? $buttons[$question['boxtype']] : _l('question_string') ?>
            <a href="javascript:void(0)" onclick="" class="pull-right question_body_toggle"><i class="fa fa-caret-up"></i></a>
            <a href="javascript:void(0)"
               onclick="update_question(this,'<?php echo $question['boxtype'] ?>',<?php echo $question['questionid'] ?>); return false;"
            class="pull-right update-question-button"><i class="fa fa-refresh text-success question_update"></i></a>
            <a href="javascript:void(0)"
               onclick="remove_question_from_database(this,'<?php echo $question['questionid'] ?>'); return false;"
            class="pull-right"><i class="fa fa-remove text-danger"></i></a>
        </label>
        <div class="question_body">
        <?php if ($question['boxtype'] != 'heading' && $question['boxtype'] != 'image') { ?>
            <div class="checkbox checkbox-primary required">
                <input type="checkbox" id="req_<?php echo $question['questionid'] ?>"
                       onchange="update_question(this,<?php echo $question['boxtype'] ?>,<?php echo $question['questionid'] ?>);"
                       data-question_required="<?php echo $question['questionid'] ?>"
                       name="required[]" <?php echo $_required ?> />
                <label for="req_<?php echo $question['questionid'] ?>"><?php echo _l('survey_question_required') ?></label>
            </div>
        <?php } ?>
        <?php if($question['boxtype'] == 'image'){
            $desriptionid= $question['box_descriptions'][0]['questionboxdescriptionid'];
            $image= $question['box_descriptions'][0]['description'];
            ?>
            <div class="image">
                <input type="hidden" data-questionid="<?php echo $question['questionid'] ?>" class="form-control questionid" value="<?php echo $question['question'] ?>">
                <div class="imageview <?php echo empty($image)?'hidden':'';?>">
                    <img src="<?php echo base_url()?>uploads/questionnaire/image/<?php echo $question['questionid'] ?>/<?php echo $image; ?>" />
                    <a class="clicktoaddimage" href="javascript:void(0)"><span><i class="fa fa-pencil"></i></span></a>
                </div>
                <div class="clicktoaddimage <?php echo !empty($image)?'hidden':'';?>">
                    <div class="drag_drop_image">
                        <span class="icon"><i class="fa fa-image"></i></span>
                        <span>Drag and Drop or Click here to add image</span>
                    </div>
                    <input type="file" class="" name="question_image" onchange="upload_image(this,'<?php echo $question['boxtype'] ?>',<?php echo $question['questionid'] ?>,<?php echo $desriptionid ?>,'<?php echo $image ?>');"/>
                </div>

            </div>
        <?php }else{ ?>
            <input type="text"
                   onblur="update_question(this,'<?php echo $question['boxtype'] ?>',<?php echo $question['questionid'] ?>);"
                   data-questionid="<?php echo $question['questionid'] ?>" class="form-control questionid"
                   value="<?php echo $question['question'] ?>">
        <?php } ?>
        <?php if ($question['boxtype'] == 'checkbox' || $question['boxtype'] == 'radio' || $question['boxtype'] == "select") { ?>
            <div class="row">
                <?php
                if (isset($question['box_descriptions'])) {
                    foreach ($question['box_descriptions'] as $box_description) {
                        $box_description_icon_class = 'fa-minus text-danger';
                        $box_description_function = 'remove_box_description_from_database(this,' . $box_description['questionboxdescriptionid'] . '); return false;';
                        if ($x == 0) {
                            $box_description_icon_class = 'fa-plus';
                            $box_description_function = 'add_box_description_to_database(this,' . $question['questionid'] . ',' . $question['boxid'] . '); return false;';
                        }
                        ?>
                        <div class="box_area">
                            <div class="col-md-3">
                                <a href="#" class="add_remove_action survey_add_more_box"
                                   onclick="<?php echo $box_description_function ?>"><i
                                            class="fa <?php echo $box_description_icon_class ?>"></i></a>
                                <div class="<?php echo $question['boxtype'] ?> <?php $question['boxtype'] ?>-primary">
                                    <?php if ($question['boxtype'] != "select") { ?>
                                        <input type="<?php echo $question['boxtype'] ?>" disabled="disabled"/>
                                    <?php } ?>
                                    <label>
                                        <input type="text"
                                               onblur="update_question(this,'<?php echo $question['boxtype'] ?>',<?php echo $question['questionid'] ?>);"
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
        <?php } ?>
        <?php if ($question['boxtype'] == 'heading') {
            $tag = $question['box_descriptions'][0]['description'];
            $desriptionid= $question['box_descriptions'][0]['questionboxdescriptionid'];
            ?>
            <div class="box_area">
                <div class="mtop10">
                    <select name="heading" class="selectpicker input_box_description"
                            onchange="update_question(this,'<?php echo $question['boxtype'] ?>',<?php echo $question['questionid'] ?>);" data-box-descriptionid="<?php echo $desriptionid; ?>">
                        <option value="">Select heading tag</option>
                        <option value="h1" <?php echo $tag == 'h1' ? 'selected' : '' ?>>H1</option>
                        <option value="h2" <?php echo $tag == 'h2' ? 'selected' : '' ?>>H2</option>
                        <option value="h3" <?php echo $tag == 'h3' ? 'selected' : '' ?>>H3</option>
                        <option value="h4" <?php echo $tag == 'h4' ? 'selected' : '' ?>>H4</option>
                        <option value="h5" <?php echo $tag == 'h5' ? 'selected' : '' ?>>H5</option>
                        <option value="h6" <?php echo $tag == 'h6' ? 'selected' : '' ?>>H6</option>
                    </select>
                </div>
            </div>
        <?php } ?>
        </div>
    </div>
</li>