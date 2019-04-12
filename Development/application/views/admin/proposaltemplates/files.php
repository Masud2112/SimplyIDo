<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 26-02-2018
 * Time: 12:07
 */


$removed_sections = array();
if(isset($proposal)){
    $sections=json_decode($proposal->sections,true);
    $section=$sections['files'];
    $pid = $proposal->templateid;
    $signatures = json_decode($proposal->signatures,true);
    $removed_sections = json_decode($proposal->removed_sections,true);
}
$class = "";
$checked = "";
if(isset($removed_sections)){
    $class = in_array('files',$removed_sections)?"removed_section":"";
    $checked = in_array('files',$removed_sections)?"checked":"";
}
?>
<div id="files" class="<?php echo $class?>">
    <div class="row ">
        <div class="files_header col-sm-12">
            <div class="row ">
                <div class="col-sm-6">
                    <h4 id="files_page_name"><i class="fa fa-folder-open-o mright10"></i><b><span><?php echo isset($section)?$section['name']:"FILES"; ?></span></b></h4>
                    <input type="hidden" name="sections[files][name]" class="files_page_name" value="<?php echo isset($section)?$section['name']:"FILES"; ?>">
                </div>
                <div class="col-sm-6  col-right">
                    <?php
                    if(!isset($_GET['preview'])){
                    ?>
                        <a href="#" class="btn btn-info inline-block" id="add_file" data-toggle="modal" data-target="#add_media_popup"><i class="fa fa-plus-square"></i> ADD MEDIA</a>
                        <div class="show-options">
                            <a class='show_act' href='javascript:void(0)'>
                                <i class='fa fa-ellipsis-v' aria-hidden='true'></i>
                            </a>
                        </div>
                        <div class='table_actions'>
                            <ul>
                                <li>
                                    <a href='javascript:void(0)' class="" id="edit_page" data-toggle="modal"
                                       data-target="#edit_files_popup">
                                        <i class="fa fa-pencil-square-o"></i><span>Edit</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
						<div class="checkbox">
                            <input type="checkbox" class="checkbox remove_proposal_section" name="remove_sec[]" id="remove_files" data-pid="#files" value="files" <?php echo $checked?> />
                            <label for="remove_files"><?php echo "Remove"; ?></label></div>
                    <?php } ?>
                </div>
            </div>
            <div class="section_body">
            <div class="file_form">
                <div class="tab-content">
                    <div id="file" class=" col-sm-6">
                        <h3>Add File</h3>
                        <div class="form-group">
                            <div class="input-group">
						          <span class="input-group-btn">
						            <span class="btn btn-primary" onclick="$(this).parent().find('input[type=file]').click();">Browse</span>
						            <input name="pfile" onchange="$(this).parent().parent().find('.form-control').html($(this).val().split(/[\\|/]/).pop());" style="display: none;" type="file">
						          </span>
                                <span class="form-control"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (isset($files) && count($files) > 0 && !empty($files)){
                //$path = get_upload_path_by_type('proposalgallery') . $pid . '/';
                $path = 'uploads/proposals_images/files/' . $pid . '/';
                //$path = base_url($path);
                ?>
                <div class="clearfix"></div>
                    <div class="gal_files row">
			<div class="hrow"><span class="hTxt"><?php echo count($files)?> Items</span>
                <a href="javascript:void(0)" class="fa fa-caret-up pull-right"></a>
            </div>
						<div class="row rowWrap">
                        <?php foreach ($files as $image) {
                            $img_id = $image['id'];
                            $title = $image['title'];
                            $caption = $image['caption'];
                            $name = $image['name'];
                            $extension = pathinfo($name,PATHINFO_EXTENSION );
                            ?>
                            <div id = "gal_file_<?php echo $img_id?>" class="gal_file col-sm-3 image_<?php echo $img_id?>">
                                <a href="<?php echo base_url($path.$name)?>" target="_blank">
                                    <?php
                                        if($extension =='zip'||$extension =='rar'){
                                            echo '<i class="mime mime-zip"></i> <span class="fname">'.$name.'</span>';
                                        }elseif ($extension=='pdf'){
                                            echo '<i class="mime mime-pdf"></i> <span class="fname">'.$name.'</span>';
                                        }elseif ($extension=='docx'||$extension =='doc'){
                                            echo '<i class="mime mime-word"></i> <span class="fname">'.$name.'</span>';

                                        }elseif ($extension=='xlsx'||$extension =='csv'){
                                            echo '<i class="mime mime-excel"></i> <span class="fname">'.$name.'</span>';

                                        }else{

                                            echo gallery_proposal_image($img_id,$pid);
                                            echo '<span class="fname">'.$name.'</span>';
                                        }
                                    ?>
                                </a>
                                <?php
                                if(!isset($_GET['preview'])){
                                ?>
                                <div class='deleteItem'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div>
								<div class='table_actions'>
								<ul><li><a href="javascript:void(0)" class="proosal_file_remove" data-pid="#gal_file_<?php echo $img_id?>" data-file=<?php echo $img_id?>><i class="fa fa-close"></i>Delete</a></li></ul></div><?php } ?>
                            </div>
                        <?php } ?>
                    </div>
            <?php } ?>
                    </div>
            </div>
    </div>
</div></div>

<div class="modal fade" id="edit_files_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                                <input type="text" name="page_name[quote]" class="form-control page_name" value="<?php echo isset($section)?strtoupper($section['name']):"FILES"; ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" class="btn btn-info page_save" data-pid="files_page_name" data-id="#edit_files_popup"><?php echo _l('submit'); ?></a>
            </div>
        </div>
    </div>
</div>