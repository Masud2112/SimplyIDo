<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 14-03-2018
 * Time: 16:15
 */
$removed_sections = array();
if(isset($proposal)){
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

<div id="intro" class=" ptop10 <?php echo $class?>">
    <!--<div class="row mbot10">
        <div class="col-sm-6"><h4><i class="fa fa-file-text-o mright10"></i><b>Introduction</b></h4></div>
        <div class="col-sm-6 col-right text-right">
        </div>
    </div>-->
    <div class="section_body">
        <div class="introduction_txt">
            <?php echo $contents ?>
        </div>
    </div>
</div>
