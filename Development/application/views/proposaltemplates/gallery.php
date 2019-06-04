<?php
/**/

$removed_sections = array();
if (isset($proposal)) {
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
<?php if (isset($gallery) && count($gallery) > 0 && !empty($gallery)) { ?>
    <div id="gallery" class="<?php echo $class ?>">
        <div class="row ">
            <div class="gallery_header col-sm-12">
                <div class="row mbot10">
                    <div class="col-sm-6"><h4><i class="fa fa-image mright10"></i><b>GALLERY</b></h4></div>
                    <div class="col-sm-6 col-right">
                    </div>
                </div>
                <div class="section_body">
                    <?php if (isset($gallery) && count($gallery) > 0 && !empty($gallery)) {
                        $path = get_upload_path_by_type('proposalgallery') . $pid . '/';
                        $path = base_url($path);
                        ?>
                        <div class="clearfix"></div>
                        <div class="gal_images row">
                            <div class="hrow"><span class="hTxt"><?php echo count($gallery) ?> Items</span> <i
                                        class="fa fa-caret-up"></i></div>
                            <div class="row rowWrap">
                                <?php foreach ($gallery as $image) {
                                    $img_id = $image['id'];
                                    $title = $image['title'];
                                    $caption = $image['caption'];
                                    $name = $image['name'];
                                    $url = $image['name'];
                                    $path = 'uploads/proposals_images/gallery/' . $pid . '/';

                                    ?>
                                    <div id="gal_image<?php echo $img_id ?>"
                                         class="gal_image  image_<?php echo $img_id ?>">

                                        <?php
                                        if (strpos($name, 'youtu.be') > 0 || strpos($name, 'youtube') > 0) {
                                            preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $name, $matches);
                                            $name = $matches[0];
                                            if ($content = file_get_contents("http://youtube.com/get_video_info?video_id=" . $name)) {
                                                parse_str($content, $ytarr);
                                                $title = $ytarr['title'];
                                            }
                                            ?>
                                            <a data-lightbox="proposal-gallery" href="<?php echo $url ?>"
                                               target="_blank">
                                                <img src='<?php echo "https://i1.ytimg.com/vi/" . $name . "/mqdefault.jpg"; ?>'/>
                                                <span class="play_icon"><i class="fa fa-play-circle-o"
                                                                           aria-hidden="true"></i></span>
                                            </a>
                                        <?php } elseif (strpos($name, 'vimeo') > 0) {
                                            $vid = substr(parse_url($name, PHP_URL_PATH), 1);
                                            $apiData = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$vid.php"));
                                            $videoInfo = $apiData[0];
                                            $name = $videoInfo['thumbnail_medium'];
                                            $title = $videoInfo['title'];
                                            ?>
                                            <a data-lightbox="proposal-gallery" href="<?php echo $url ?>"
                                               target="_blank" class="gallery_proposal_image_blk">
                                                <img src='<?php echo $name; ?>' class="gallery-image"/>
                                                <span class="play_icon"><i class="fa fa-play-circle-o"
                                                                           aria-hidden="true"></i></span>
                                            </a>
                                        <?php } else {
                                            echo '<a data-lightbox="proposal-gallery" href="' . base_url($path . $name) . '" class="gallery_proposal_image_blk">';
                                            echo gallery_proposal_image($img_id, $pid);
                                            echo '</a>';
                                        }
                                        ?>
                                        <span class="imgTitle"><?php echo $title; ?></span>
                                        <span class="imgCaption"><?php echo $caption; ?></span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>