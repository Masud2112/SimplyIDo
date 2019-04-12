<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 14-03-2018
 * Time: 16:15
 */

$removed_sections = array();
if(isset($proposal)){
    $sections=json_decode($proposal->sections,true);
    $section=$sections['intro'];
    $pid = $proposal->templateid;
    $signatures = json_decode($proposal->signatures,true);
    $removed_sections = json_decode($proposal->removed_sections,true);
}
$class = "";
$checked = "";
if(isset($removed_sections)){
    $class = in_array('introduction',$removed_sections)?"removed_section":"";
    $checked = in_array('introduction',$removed_sections)?"checked":"";
}
$contents = '';
if(isset($proposal)){$contents = $proposal->content;}
?>

<div id="introduction_sec" class=" ptop10 <?php echo $class?>">
    <div class="row">
        <div class="col-sm-6">
            <h4 id="intro_page_name"><i class="fa fa-file-text-o mright10"></i><b><span><?php echo isset($section)?$section['name']:"Introduction"; ?></span></b></h4>
            <input type="hidden" name="sections[intro][name]" class="intro_page_name" value="<?php echo isset($section)?$section['name']:"Introduction"; ?>">
        </div>
        <div class="col-sm-6 col-right">
            <?php
            if(!isset($_GET['preview'])){
                ?>
                <a href="#" class="btn btn-info inline-block hide" id="add_media" data-toggle="modal" data-target="#add_media_popup"><i class="fa fa-plus-square"></i> ADD MEDIA</a>
                <div class="show-options">
                    <a class='show_act' href='javascript:void(0)'>
                        <i class='fa fa-ellipsis-v' aria-hidden='true'></i>
                    </a>
                </div>
				 <div class='table_actions'>
                    <ul>
                        <li>
                            <a href='#' class="" id="edit_page" data-toggle="modal"
                               data-target="#edit_intro_popup">
                                <i class="fa fa-pencil-square-o"></i><span>Edit</span>
                            </a>
                        </li>
                    </ul>
                </div>
				<div class="checkbox">
                    <input type="checkbox" class="checkbox remove_proposal_section" name="remove_sec[]" id="remove_introduction" data-pid="#introduction_sec" value="introduction" <?php echo $checked?> />
                    <label for="remove_introduction"><?php echo "Remove"; ?></label></div>
               
            <?php } ?>
        </div>
    </div>
    <div class="section_body">
        <?php if(isset($_GET['preview']) && $_GET['preview']==true){ ?>
            <div class="introduction_txt">
                <?php echo $contents ?>
            </div>
        <?php } else { ?>
            <?php echo render_textarea('introduction','',$contents,array(),array(),''); ?>
        <?php } ?>
    </div>
</div>
<div class="modal fade" id="edit_intro_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('edit page'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="group_popup">
                        <div class="group_name">
                            <div class="form-group">
                                <label class="control-label">Page Name
                                    <small class="req text-danger">*</small>
                                </label>
                                <input type="text"  class="form-control page_name" value="<?php echo isset($section)?strtoupper($section['name']):"INTRODUCTION"; ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" class="btn btn-info page_save" data-pid="intro_page_name" data-id="#edit_intro_popup"><?php echo _l('submit'); ?></a>
            </div>
        </div>
    </div>
</div>