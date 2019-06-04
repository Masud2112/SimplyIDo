<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 20-03-2018
 * Time: 15:58
 */
$question_type = $question['boxtype'];
$class = "";
if ($question['required'] == 1) {
    $_required = '<span class="req text-danger">*</span>';
    $required = "required";
} else {
    $_required = '';
    $required = "";
}
if ($question_type == 'date') {
    $class = "datepicker";
}
?>
<div class="single_question">
    <?php if ($question_type != 'heading' && $question_type != 'image') { ?>
        <input type="hidden" name="answers[<?php echo $queindex ?>][questionid]" value="<?php echo $question['questionid'] ?>">
    <?php } ?>
    <div class="form-group viewquestion">

        <?php if ($question_type == 'heading'){
        $tag = $question['box_descriptions'][0]['description'];
        ?>
        <<?php echo $tag ?>><?php echo $question['question'] ?></<?php echo $tag ?>>

<?php } elseif ($question_type == 'image') {
    $image = $question['box_descriptions'][0]['description'];
    ?>
    <div class="que_image_sec">
        <img src="<?php echo base_url() ?>uploads/questionnaire/image/<?php echo $question['questionid'] ?>/<?php echo $image; ?>"/>
    </div>
<?php } else { ?>
    <label><?php echo $question['question'] ?><?php echo $_required ?></label>
    <?php if ($question_type == 'checkbox' || $question_type == 'radio' || $question_type == "select") { ?>
        <div class="row">
            <div class="box_area">
                <div class="mtop5">
                    <?php if ($question_type != "select") { ?>
                        <?php foreach ($question['box_descriptions'] as $box_description) { ?>
                            <div class="<?php echo $question_type ?> <?php $question_type ?>-primary inline-block">

                                <input id="<?php echo "op_" . $box_description['questionboxdescriptionid'] ?>"
                                       type="<?php echo $question['boxtype'] ?>"
                                       name="<?php echo $question_type == 'checkbox'?'answers['.$queindex.'][answer][]':'answers['.$queindex.'][answer]'?>" <?php echo $required ?> value="<?php echo $box_description['description']?>"/>
                                <label for="<?php echo "op_" . $box_description['questionboxdescriptionid'] ?>"><?php echo $box_description['description'] ?></label>


                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <select name="answers[<?php echo $queindex ?>][answer]"
                                class="selectpicker <?php echo $question_type ?> <?php $question_type ?>-primary" <?php echo $required ?>>
                            <option value=""></option>
                            <?php foreach ($question['box_descriptions'] as $box_description) { ?>
                                <option value="<?php echo $box_description['description'] ?>"><?php echo $box_description['description'] ?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>
                </div>
            </div>
        </div>

    <?php } elseif ($question_type == 'yn_question') { ?>
        <div class="radio <?php $question_type ?>-primary inline-block">

            <input type="radio" name="answers[<?php echo $queindex ?>][answer]"
                   id="yes_<?php echo $question['questionid'] ?>" <?php echo $required ?> value="yes" />
            <label for="" yes_".<?php echo $question['questionid'] ?>">YES</label>
        </div>
        <div class="radio <?php $question_type ?>-primary inline-block">
            <input type="radio" name="answers[<?php echo $queindex ?>][answer]"
                   id="no_<?php echo $question['questionid'] ?>" <?php echo $required ?> value="no"/>
            <label for="no_" .<?php echo $question['questionid'] ?>">NO</label>
        </div>
    <?php } elseif ($question_type == 'file') { ?>
        <div class="que_file_upload">
            <div class="drag_drop_file drag_drop_image text-center">
                <span class="icon"><i class="fa fa-cloud-upload"></i></span>
                <span class="file_name">Drag and drop or click to upload your file</span>
            </div>
            <input type="file" name="answers[<?php echo $queindex ?>][answer]" data-questionid="<?php echo $question['questionid'] ?>"
                   class="form-control questionid <?php echo $class; ?>" value="" <?php echo $required ?> >
        </div>
    <?php } elseif ($question_type == 'short_answer') { ?>
        <textarea type="text" name="answers[<?php echo $queindex ?>][answer]" data-questionid="<?php echo $question['questionid'] ?>"
                  class="form-control questionid <?php echo $class; ?>" rows="5" <?php echo $required ?>></textarea>
    <?php } elseif ($question_type == 'long_answer') { ?>
        <!--<input type="text" data-questionid="<?php /*echo $question['questionid'] */ ?>"
                       class="form-control questionid <?php /*echo $class; */ ?>" value="">-->
        <?php echo render_textarea('answers['.$queindex.'][answer]', '', '', array(), array(), '', 'tinymce'); ?>
    <?php } else { ?>
        <input type="text" data-questionid="<?php echo $question['questionid'] ?>" name="answers[<?php echo $queindex ?>][answer]"
               class="form-control questionid <?php echo $class; ?>" value="" <?php echo $required ?> />
    <?php }
} ?>
</div>
</div>
