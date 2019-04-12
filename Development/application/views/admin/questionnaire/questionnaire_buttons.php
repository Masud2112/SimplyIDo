<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 20-03-2018
 * Time: 13:31
 */
$buttons =array();
$buttons[]= array('type'=>'heading','name'=>'Heading','icon'=>'fa-header');
/*$buttons[]= array('type'=>'columns','name'=>'Columns','icon'=>'fa-columns');*/
$buttons[]= array('type'=>'text_box','name'=>'Text Box','icon'=>'fa-paragraph');
$buttons[]= array('type'=>'image','name'=>'Image','icon'=>'fa-picture-o');
$buttons[]= array('type'=>'short_answer','name'=>'Short Answer','icon'=>'fa-pencil-square-o');
$buttons[]= array('type'=>'long_answer','name'=>'Long Answer','icon'=>'fa-pencil');
$buttons[]= array('type'=>'date','name'=>'Date Select','icon'=>'fa-calendar-o');
$buttons[]= array('type'=>'file','name'=>'File Uploader','icon'=>'fa-cloud-upload');
$buttons[]= array('type'=>'yn_question','name'=>'Yes/No Question','icon'=>'fa-circle-o');
$buttons[]= array('type'=>'select','name'=>'Dropdown','icon'=>'fa-tasks');
$buttons[]= array('type'=>'checkbox','name'=>'Checkboxes','icon'=>'fa-check-square-o');
$buttons[]= array('type'=>'radio','name'=>'Single Choice','icon'=>'fa-circle');
/*$buttons[]= array('type'=>'code_block','name'=>'Code Block','icon'=>'fa-code');
$buttons[]= array('type'=>'group','name'=>'Group','icon'=>'fa-link');
$buttons[]= array('type'=>'introduction_block','name'=>'Introduction Block','icon'=>'fa-users');
$buttons[]= array('type'=>'custom_block','name'=>'Custom Block','icon'=>'fa-cubes');*/

$btn_width = 100/9;
?>
<div class="questionnaire_buttons">
    <div class="hrow text-center pright15"><i class="fa fa-plus-square"><span style="font-family: sans-serif;font-weight: bold"> ADD QUESTION</span></i><i class="pull-right fa fa-caret-up"></i></div>
    <div class="buttons_container rowWrap" style="display: none">
        <?php foreach ($buttons as $button){ ?>
            <div class="que_button text-center <?php echo $button['type'];?>" style="width:<?php echo $btn_width ?>%" onclick="add_question('<?php echo $button['type'];?>',<?php echo (isset($questionnaire) ? $questionnaire->id : '0'); ?>);return false;" draggable="true">
                <div class="btn_inner">
                    <span class="icon"><i class="fa <?php echo $button['icon'];?>"></i></span>
                    <span class="btn_name"><?php echo $button['name'];?></span>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
