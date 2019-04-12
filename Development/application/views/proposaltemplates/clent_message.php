<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 28-02-2018
 * Time: 16:38
 */

$removed_sections = array();
if(isset($proposal)){
    $signatures = json_decode($proposal->signatures,true);
    $removed_sections = json_decode($proposal->removed_sections,true);
}
$class = "";
$checked = "";
if(isset($removed_sections)){
    $class = in_array('message',$removed_sections)?"removed_section":"";
    $checked = in_array('message',$removed_sections)?"checked":"";
}
/*if(!empty($proposal->client_message)){
*/?><!--
<div id="message" class="<?php /*echo $class*/?>">
    <div class="row">
        <div class="files_header col-sm-12">
            <h4><i class="fa fa-file-text-o mright10"></i><b>CLIENT MESSAGE</b></h4>
            <div class="section_body">
                <div class="message_form">
                    <div class="message_txt">
                        <?php /*echo isset($proposal) ? $proposal->client_message:"" */?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
--><?php /*} */?>

