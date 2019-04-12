<?php init_head();
$venue = get_vanue_data($_GET['venue']);
?>
<div id="wrapper">
    <div class="content onsite-loc-page">
        <div class="row">
            <?php echo form_open_multipart($this->uri->uri_string(), array('id' => 'onsiteloc', 'class' => 'venue-form', 'autocomplete' => 'off')); ?>
            <div class="col-sm-12">
                <div class="pull-right">
                    <div class="breadcrumb">
                        <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('venues'); ?>">Venues</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('venues/view/' . $venue->venueid); ?>"><?php echo $venue->venuename; ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php if (isset($locaton)) { ?>
                            <a href="<?php echo admin_url('venues/onsitelocview/' . $locaton->locid . "?venue=" . $venue->venueid); ?>"><?php echo $locaton->locname; ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } ?>
                        <span><?php echo isset($locaton) ? "Edit" : "New On-site location" ?></span>
                    </div>
                </div>
                <?php
                if (isset($locaton)) { ?>
                    <?php echo form_hidden('venueid', $locaton->venueid); ?>
                <?php } ?>
                <h1 class="pageTitleH1"><i class="fa fa-address-book-o"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-sm-6">
                        <h5 class="sub-title">
                            <strong><?php echo _l('photo'); ?></strong>
                        </h5>
                        <div class="panel_s btmbrd">
                            <div class="panel-body">
                                <div class="oloc-pic">
                                    <?php
                                    $src = "";
                                    if ((isset($locaton) && $locaton->loccoverimage != NULL)) {
                                        $src = base_url() . 'uploads/venue_loc_images/' . $locaton->locid . '/' . $locaton->loccoverimage;
                                        $path = get_upload_path_by_type('venue_locimage') . $locaton->locid . '/' . $locaton->loccoverimage;
                                        if (file_exists($path)) {
                                            $path = get_upload_path_by_type('venue_locimage') . $locaton->locid . '/croppie_' . $locaton->loccoverimage;
                                            $src = base_url() . 'uploads/venue_loc_images/' . $locaton->locid . '/' . $locaton->loccoverimage;
                                            if (file_exists($path)) {
                                                $src = base_url() . 'uploads/venue_loc_images/' . $locaton->locid . '/croppie_' . $locaton->loccoverimage;
                                            }
                                        }
                                    }
                                    ?>
                                    <div class="oloc_imageview <?php echo empty($src) ? 'hidden' : ''; ?>">
                                        <?php if ($src == "") { ?>
                                            <!-- <a class="clicktoaddimage" href="javascript:void(0)"
                                               onclick="croppedDelete('oloc');">
                                                <span><i class="fa fa-trash"></i></span></a>
                                            <a class="btn btn-info mtop10" href="javascript:void(0)"
                                               onclick="reCropp('oloc');">
                                                <?php //echo _l('recrop')?></a> -->
                                            <div class="actionToEdit">
                                                <a class="clicktoaddimage" href="javascript:void(0)" onclick="croppedDelete('oloc');">
                                                    <span><i class="fa fa-trash"></i></span>
                                                </a>
                                                <a class="recropIcon_blk" href="javascript:void(0)" onclick="reCropp('oloc');">
                                                    <span><i class="fa fa-crop" aria-hidden="true"></i></span>
                                                </a>
                                            </div>
                                        <?php } ?>
                                        <img src="<?php echo $src; ?>"/>
                                    </div>
                                    <div class="clicktoaddimage <?php echo !empty($src) ? 'hidden' : ''; ?>">
                                        <div class="drag_drop_image">
                                            <span class="icon"><i class="fa fa-image"></i></span>
                                            <span><?php echo _l('dd_upload'); ?></span>
                                        </div>
                                        <input type="file" class="" name="loccoverimage"
                                               onchange="readFile(this,'oloc');"/>
                                        <input type="hidden" id="imagebase64" name="imagebase64">
                                    </div>
                                    <div class="cropper" id="oloc_croppie">
                                        <div class="cropper_container">
                                            <div id="oloc-cropper"></div>
                                            <div class="cropper-footer">
                                                <button type="button" class="btn btn-info p9 actionDone" type="button"
                                                        id=""
                                                        onclick="croppedResullt('oloc');">
                                                    <?php echo _l('save'); ?>
                                                </button>
                                                <button type="button" class="btn btn-default actionCancel"
                                                        data-dismiss="modal"
                                                        onclick="croppedCancel('oloc');">
                                                    <?php echo _l('cancel'); ?>
                                                </button>
                                                <button type="button" class="btn btn-default actionChange"
                                                        onclick="croppedChange('oloc');">
                                                    <?php echo _l('change'); ?>
                                                </button>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center removelink pd-t10">
                                        <?php if ((isset($locaton) && $locaton->loccoverimage != NULL)) { ?>
                                            <a class="_delete"
                                               href="<?php echo admin_url('venues/remove_loc_cover_image/' . $locaton->locid); ?>">
                                                <i class="fa fa-remove"></i>
                                                <span class="mleft5"><?php echo _l('remove') ?></span>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h5 class="sub-title">
                            <strong><?php echo _l('Details'); ?></strong>
                        </h5>
                        <div class="panel_s btmbrd">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <?php echo render_input('locname', 'venue_add_edit_locname', (isset($locaton) ? $locaton->locname : ''), ''); ?>
                                    </div>
                                    <div class="col-sm-12">
                                        <?php echo _l('loc_type') ?>
                                        <div class="radio inline-block">
                                            <input type="radio" id="indoor" name="type"
                                                   value="indoor" <?php echo isset($locaton) && $locaton->type == "indoor" ? "checked" : "checked" ?> >
                                            <label for="indoor"><?php echo _l('indoor') ?></label>
                                        </div>
                                        <div class="radio inline-block">
                                            <input type="radio" id="outdoor" name="type"
                                                   value="outdoor" <?php echo isset($locaton) && $locaton->type == "outdoor" ? "checked" : "" ?>>
                                            <label for="outdoor"><?php echo _l('outdoor') ?></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <label for="loc_description"
                                               class="control-label"><?php echo _l('venue_add_edit_locdescription') ?></label>
                                        <textarea class="loc_description" id="loc_description" name="loc_description">
                                            <?php echo(isset($locaton) ? $locaton->loc_description : ''); ?>
                                        </textarea>
                                    </div>
                                    <input type="hidden" name="venueid" value="<?php echo $venueid; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="topButton">
                    <?php if (isset($locaton)) { ?>
                        <a href="<?php echo base_url(); ?>admin/venues/onsitelocview/<?php echo isset($locaton->locid) ? $locaton->locid : ""; ?>?venue=<?php echo $venueid; ?>"
                           class="btn btn-default" type="button"
                           onclick="fncancel();"><?php echo _l('Cancel'); ?></a>
                    <?php } else { ?>
                        <a href="<?php echo base_url(); ?>admin/venues/view/<?php echo $venueid; ?>"
                           class="btn btn-default" type="button"
                           onclick="fncancel();"><?php echo _l('Cancel'); ?></a>
                    <?php } ?>

                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
                <!--</div>
            </div>-->
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
    function fncancel() {
        location.href = '<?php echo base_url(); ?>admin/venues/onsitelocview/<?php echo isset($locaton->locid) ? $locaton->locid : ""; ?>?venue=<?php echo $venueid;?>';
    }

    $(function () {
        init_editor('.loc_description');
        var validator = $('#onsiteloc').submit(function () {
            // update underlying textarea before submit validation
            var content = tinyMCE.activeEditor.getContent();
            $("#loc_description").val(content);
            tinyMCE.triggerSave();
            /*if($("#loc_description").val() == ""){
                $(".mce-tinymce").css({'border-color': '#fc2d42'});
            } else {
                $(".mce-tinymce").css({'border-color': ''});
            }*/
        }).validate({
            ignore: "",
            rules: {
                locname: "required",
                type: "required",
                //loc_description: "required",
            }
        });

    });
    //_validate_form($('form'),{subject:'required',content:'required', 'message_to[]':'required'});
</script>

</body>
</html>