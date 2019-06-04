<?php
/**
 * Created by PhpStorm.
 * User: masud
 * Date: 26-02-2018
 * Time: 12:07
 */

$removed_sections = array();
if (isset($proposal)) {
    $pid = $proposal->templateid;
    $signatures = json_decode($proposal->signatures, true);
    $removed_sections = json_decode($proposal->removed_sections, true);
}
$class = "";
$checked = "";
if (isset($removed_sections)) {
    $class = in_array('files', $removed_sections) ? "removed_section" : "";
    $checked = in_array('files', $removed_sections) ? "checked" : "";
}
?>
<?php if (isset($files) && count($files) > 0 && !empty($files)){ ?>
<div id="files" class="<?php echo $class ?>">
    <div class="row ">
        <div class="files_header col-sm-12">
            <div class="row mbot10">
                <div class="col-sm-6"><h4><i class="fa fa-folder-open-o mright10"></i><b>FILES</b></h4></div>
                <div class="col-sm-6  col-right">
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
						            <span class="btn btn-primary"
                                          onclick="$(this).parent().find('input[type=file]').click();">Browse</span>
						            <input name="pfile"
                                           onchange="$(this).parent().parent().find('.form-control').html($(this).val().split(/[\\|/]/).pop());"
                                           style="display: none;" type="file">
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
                    <div class="hrow"><span class="hTxt"><?php echo count($files) ?> Items</span> <i
                                class="fa fa-caret-up"></i></div>
                    <div class="row rowWrap">
                        <?php foreach ($files as $image) {
                            $img_id = $image['id'];
                            $title = $image['title'];
                            $caption = $image['caption'];
                            $name = $image['name'];
                            $extension = pathinfo($name, PATHINFO_EXTENSION);
                            ?>
                            <div id="gal_file_<?php echo $img_id ?>"
                                 class="gal_file col-sm-3 image_<?php echo $img_id ?>">
                                <a href="<?php echo base_url($path . $name) ?>" target="_blank">
                                    <?php
                                    if ($extension == 'zip' || $extension == 'rar') {
                                        echo '<i class="mime mime-zip"></i> <span class="fname">' . $name . '</span>';
                                    } elseif ($extension == 'pdf') {
                                        echo '<i class="mime mime-pdf"></i> <span class="fname">' . $name . '</span>';
                                    } elseif ($extension == 'docx' || $extension == 'doc') {
                                        echo '<i class="mime mime-word"></i> <span class="fname">' . $name . '</span>';

                                    } elseif ($extension == 'xlsx' || $extension == 'csv' || $extension == 'xls') {
                                        echo '<i class="mime mime-excel"></i> <span class="fname">' . $name . '</span>';

                                    } else {

                                        echo '<div class="gallery_proposal_image_blk">' . gallery_proposal_image($img_id, $pid) . '</div>';
                                        echo '<span class="fname">' . $name . '</span>';
                                    }
                                    ?>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div></div>
    <?php } ?>
