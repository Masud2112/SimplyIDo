<?php
/**/

$removed_sections = array();
if (isset($proposal)) {
    $sections = json_decode($proposal->sections, true);
    $section = $sections['gallery'];
    $pid = $proposal->templateid;
    $signatures = json_decode($proposal->signatures, true);
    $removed_sections = json_decode($proposal->removed_sections, true);
}
$class = "";
$checked = "";
if (isset($removed_sections)) {
    $class = in_array('gallery', $removed_sections) ? "removed_section" : "";
    $checked = in_array('gallery', $removed_sections) ? "checked" : "";
}
/**/
?>
<div id="gallery" class="<?php echo $class ?>">
    <div class="row ">
        <div class="gallery_header col-sm-12">
            <div class="row ">
                <div class="col-sm-6">
                    <h4 id="gallery_page_name"><i
                                class="fa fa-image mright10"></i><b><span><?php echo isset($section) ? $section['name'] : "GALLERY"; ?></span></b>
                    </h4>
                    <input type="hidden" name="sections[gallery][name]" class="gallery_page_name"
                           value="<?php echo isset($section) ? $section['name'] : "GALLERY"; ?>">
                </div>
                <div class="col-sm-6 col-right">
                    <?php
                    if (!isset($_GET['preview'])) {
                        ?>
                        <a href="#" class="btn btn-info inline-block" id="add_media" data-toggle="modal"
                           data-target="#add_media_popup"><i class="fa fa-plus-square"></i> ADD MEDIA</a>

                        <div class="show-options">
                            <a class='show_act' href='javascript:void(0)'>
                                <i class='fa fa-ellipsis-v' aria-hidden='true'></i>
                            </a>
                        </div>
                        <div class='table_actions'>
                            <ul>
                                <li>
                                    <a href='javascript:void(0)' class="" id="edit_page" data-toggle="modal"
                                       data-target="#edit_gallery_popup">
                                        <i class="fa fa-pencil-square-o"></i><span>Edit</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="checkbox">
                            <input type="checkbox" class="checkbox remove_proposal_section" name="remove_sec[]"
                                   id="remove_gallery"
                                   data-pid="#gallery" value="gallery" <?php echo $checked ?> />
                            <label for="remove_gallery"><?php echo "Remove"; ?></label></div>
                    <?php } ?>
                </div>
            </div>
            <div class="section_body">
                <div class="gallery_form">
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#image">ADD IMAGE</a></li>
                        <li><a data-toggle="tab" href="#youtube">ADD YOUTUBE</a></li>
                        <li><a data-toggle="tab" href="#vimeo">ADD VIMEO </a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="image" class="tab-pane fade in active">
                            <div class="row">
                            <div class="col-sm-6 proposal-pic">
                                <div class="proposal_imageview hidden">
                                    <img src=""/>
                                    <!-- <a class="clicktoaddimage" href="javascript:void(0)" onclick="croppedDelete('proposal');"><span><i
                                                    class="fa fa-trash"></i></span></a>
                                    <a class="btn btn-info mtop10" href="javascript:void(0)"
                                       onclick="reCropp('proposal');">
                                        <?php //echo _l('recrop')?></a> -->
                                        
                                    <div class="actionToEdit">
                                        <a class="clicktoaddimage" href="javascript:void(0)" onclick="croppedDelete('proposal');">
                                            <span><i class="fa fa-trash"></i></span>
                                        </a>
                                        <a class="recropIcon_blk" href="javascript:void(0)" onclick="reCropp('proposal');">
                                            <span><i class="fa fa-crop" aria-hidden="true"></i></span>
                                        </a>
                                    </div>
                                </div>
                                <div class="clicktoaddimage">
                                    <div class="drag_drop_image">
                                        <span class="icon"><i class="fa fa-image"></i></span>
                                        <span><?php echo _l('dd_upload'); ?></span>
                                    </div>
                                    <input id="proposal_image" type="file" class="" name="pimage" onchange="readFile(this,'proposal');"/ >
                                    <input type="hidden" id="imagebase64" name="imagebase64">
                                </div>
                                <div class="cropper" id="proposal_croppie">
                                    <div class="pcopper_container">
                                        <div id="proposal-cropper"></div>
                                        <div class="cropper-footer">
                                            <button type="button" class="btn btn-info p9 proposalDone" type="button"
                                                    id="" onclick="croppedResullt('proposal');">
                                                <?php echo _l('save'); ?>
                                            </button>
                                            <button type="button" class="btn btn-default proposalCancel"
                                                    data-dismiss="modal" onclick="croppedCancel('proposal');">
                                                <?php echo _l('cancel'); ?>
                                            </button>
                                            <button type="button" class="btn btn-default actionChange"
                                                    onclick="croppedChange('proposal');">
                                                <?php echo _l('change'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 ">
                                <div class="form-group">
                                    <label>Title: </label>
                                    <input type="text" name="image[pimage_title]" class="form-control pimage"/>
                                    <input type="hidden" name="image[pimage_type]" value="gallery"/>
                                </div>
                                <div class="form-group">
                                    <label>Caption: </label>
                                    <input type="text" name="image[pimage_caption]" class="form-control pimagecap"/>
                                </div>
                                <!--<div class="form-group">
                                    <label for="banner" class="profile-image">Image</label>
                                    <div class="input-group">
						          <span class="input-group-btn">
						            <span class="btn btn-primary"
                                          onclick="$(this).parent().find('input[type=file]').click();">Browse</span>
						            <input name="pimage"
                                           onchange="$(this).parent().parent().find('.form-control').html($(this).val().split(/[\\|/]/).pop());"
                                           style="display: none;" type="file">
						          </span>
                                        <span class="form-control"></span>
                                    </div>
                                </div>-->
                            </div>
                            </div>
                        </div>
                        <div id="youtube" class="col-sm-6 tab-pane fade">

                            <div class="form-group">
                                <label for="banner" class="profile-image">Add YouTube Video URL:</label>
                                <input type="text" name="pvideo[youtube]" class="form-control"/>
                            </div>
                        </div>
                        <div id="vimeo" class="col-sm-6 tab-pane fade">

                            <div class="form-group">
                                <label for="banner" class="profile-image">Add Vimeo Video URL:</label>
                                <input type="text" name="pvideo[vimeo]" class="form-control"/>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (isset($gallery) && count($gallery) > 0 && !empty($gallery)) {
                    $path = get_upload_path_by_type('proposalgallery') . $pid . '/';
                    $path = base_url($path);
                    ?>
                    <div class="clearfix"></div>
                    <div class="gal_images row">
                        <div class="hrow"><span class="hTxt"><?php echo count($gallery) ?> Items</span>
                            <a class="fa fa-caret-up pull-right" href="javascript:void(0)"></a>
                        </div>
                        <div class="row rowWrap">
                            <?php foreach ($gallery as $image) {
                                $img_id = $image['id'];
                                $title = $image['title'];
                                $caption = $image['caption'];
                                $name = $image['name'];
                                $url = $image['name'];
                                $path = 'uploads/proposals_images/gallery/' . $pid . '/';

                                ?>
                                <div id="gal_image<?php echo $img_id ?>" class="gal_image  image_<?php echo $img_id ?>">

                                    <?php
                                    if (strpos($name, 'youtu.be') > 0 || strpos($name, 'youtube') > 0) {
                                        preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $name, $matches);
                                        $name = $matches[0];

                                        if ($content = file_get_contents("http://youtube.com/get_video_info?video_id=" . $name)) {
                                            parse_str($content, $ytarr);
                                            $title = $ytarr['title'];
                                        }
                                        ?>
                                        <a href="<?php echo $url ?>" target="_blank" class="video_btn" toggle="modal"
                                           data-target="#proposal_videos">
                                            <img src='<?php echo "https://i1.ytimg.com/vi/" . $name . "/mqdefault.jpg"; ?>'/>
                                            <i class="fa fa-play-circle-o paly_icon" aria-hidden="true"></i>
                                        </a>
                                    <?php } elseif (strpos($name, 'vimeo') > 0) {
                                        $vid = substr(parse_url($name, PHP_URL_PATH), 1);
                                        $apiData = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$vid.php"));
                                        $videoInfo = $apiData[0];
                                        $title = $videoInfo['title'];
                                        $name = $videoInfo['thumbnail_medium'];
                                        ?>
                                        <a href="<?php echo $url ?>" target="_blank">
                                            <img src='<?php echo $name; ?>'/>
                                            <i class="fa fa-play-circle-o paly_icon" aria-hidden="true"></i>
                                        </a>
                                    <?php } else {
                                        echo '<a data-lightbox="proposal-gallery" href="' . base_url($path . $name) . '">';
                                        echo gallery_proposal_image($img_id, $pid);
                                        echo '</a>';
                                    }
                                    ?>
                                    <span class="imgTitle"><?php echo substr($title, 0, 20); ?></span>
                                    <span class="imgCaption"><?php echo substr($caption, 0, 20); ?></span>
                                    <?php
                                    if (!isset($_GET['preview'])) {
                                        ?>
                                        <div class='deleteItem'><a class='show_act' href='javascript:void(0)'><i
                                                        class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div>
                                        <div class='table_actions'>
                                            <ul>
                                                <li><a href="<?php echo admin_url('proposaltemplates/delete_file_image/'.$img_id)?>" class="_delete"
                                                       data-pid="#gal_image<?php echo $img_id ?>"
                                                       data-file=<?php echo $img_id ?>><i class="fa fa-close"></i>Delete
                                                    </a></li>
                                            </ul>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="edit_gallery_popup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
                                <input type="text" class="form-control page_name"
                                       value="<?php echo isset($section) ? strtoupper($section['name']) : "GALLERY"; ?>"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:void(0)" class="btn btn-info page_save" data-pid="gallery_page_name"
                   data-id="#edit_gallery_popup"><?php echo _l('submit'); ?></a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="proposal_videos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <!-- 16:9 aspect ratio -->
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" src="" id="video" allowscriptaccess="always">></iframe>
                </div>


            </div>

        </div>
    </div>
</div>