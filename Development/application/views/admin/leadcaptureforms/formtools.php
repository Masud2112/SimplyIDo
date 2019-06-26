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
<div class="formtools">
    <div class="expand text-center pright15"><i class="fa fa-plus"></i></div>
    <div class="buttons_container rowWrap" style="display: none">
        <div class="text-center mbot10 mtop20 bold text-uppercase"><?php echo _l('choose_desired_element')?></div>
        <?php foreach ($buttons as $button){ ?>
            <div id = "<?php echo $button['type'];?>" class="que_button text-center" style="width:<?php echo $btn_width ?>%" onclick="add_field('<?php echo $button['type'];?>',<?php echo (isset($questionnaire) ? $questionnaire->id : '0'); ?>);return false;" draggable="true">
                <div class="btn_inner">
                    <span class="icon"><i class="fa <?php echo $button['icon'];?>"></i></span>
                    <span class="btn_name"><?php echo $button['name'];?></span>
                </div>
            </div>
            <!--<div class="modal fade" id="<?php /*echo $button['type'];*/?>" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Field Settings</h4>
                        </div>
                        <div class="modal-body">
                            <p>Some text in the modal.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                </div>
            </div>-->
        <?php } ?>
    </div>
</div>
