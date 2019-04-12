<?php
/**
 * Added By : Vaidehi
 * Dt : 10/12/2017
 * For Brand Settings Module
 */
?>
<div class="row">
    <div class="col-md-12">
        <?php
        $banner = get_brand_option('banner');
        $src = "";
        ?>
        <?php if ($banner != '') {
            $path = get_upload_path_by_type('brands') . '/' . $banner;
            if (file_exists($path)) {
                $path = get_upload_path_by_type('brands') . '/croppie_' . $banner;
                $src = base_url('uploads/brands/' . $banner);
                if (file_exists($path)) {
                    $src = base_url('uploads/brands/croppie_' . $banner);
                }
            }
            ?>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <div class="bshead">
                            <h4 class="pull-left">DASHBOARD COVER IMAGE</h4>
                            <?php if (isset($packagename) && $packagename == "Paid") { ?>
                                <?php if (has_permission('brands', '', 'delete')) { ?>
                                    <div class="pull-right">
                                        <a href="<?php echo admin_url('brand_settings/remove_banner'); ?>"
                                           data-toggle="tooltip"
                                           title="<?php echo _l('settings_general_banner_remove_tooltip'); ?>"
                                           class="_delete text-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <div class="imageview <?php echo empty($src) ? 'hidden' : ''; ?>">
                            <img src="<?php echo $src; ?>" class="img img-responsive"
                                 alt="<?php get_brand_option('banner'); ?>" style="width: 100%">
                        </div>
                    </div>

                </div>
            </div>
            <div class="clearfix"></div>
        <?php } else { ?>
            <div class="form-group">
                <div class="col-md-12">
                    <div class="bshead">
                        <h4 class="pull-left">DASHBOARD COVER IMAGE</h4>
                    </div>
                    <div class="cover-pic banner-pic">

                        <div class="banner_imageview <?php echo empty($src) ? 'hidden' : ''; ?>">
                            <img src="<?php echo $src; ?>"/>
                            <!-- <a class="clicktoaddimage" href="javascript:void(0)" onclick="croppedDelete('banner');">
                                <span><i class="fa fa-trash"></i></span></a>
                            <a class="recropIcon_blk" href="javascript:void(0)"
                               onclick="reCropp('banner');">
                                <span> <i class="fa fa-crop" aria-hidden="true"></i></span></a> -->
                            <div class="actionToEdit">
                                <a class="clicktoaddimage" href="javascript:void(0)" onclick="croppedDelete('banner');">
                                    <span><i class="fa fa-trash"></i></span>
                                </a>
                                <a class="recropIcon_blk" href="javascript:void(0)" onclick="reCropp('banner');">
                                    <span><i class="fa fa-crop" aria-hidden="true"></i></span>
                                </a>
                            </div>
                        </div>
                        <div class="clicktoaddimage <?php echo !empty($src) ? 'hidden' : ''; ?>">
                            <div class="drag_drop_image">
                                <span class="icon"><i class="fa fa-image"></i></span>
                                <span><?php echo _l('dd_upload'); ?></span>
                            </div>
                            <input type="file" class="" name="banner" onchange="readFile(this,'banner');"/>
                            <input type="hidden" id="bannerbase64" name="bannerbase64">
                        </div>
                        <div class="cropper" id="banner_croppie">
                            <div class="copper_container">
                                <div id="banner-cropper"></div>
                                <div class="cropper-footer">
                                    <button type="button" class="btn btn-info p9 actionDone" type="button" id=""
                                            onclick="croppedResullt('banner');">
                                        <?php echo _l('save'); ?>
                                    </button>
                                    <button type="button" class="btn btn-default actionCancel" data-dismiss="modal"
                                            onclick="croppedCancel('banner');">
                                        <?php echo _l('cancel'); ?>
                                    </button>
                                    <button type="button" class="btn btn-default actionChange"
                                            onclick="croppedChange('banner');">
                                        <?php echo _l('change'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--<div class="bsBody text-center browseImgShow_js">
							<label for="banner" class="control-label"><?php /*echo _l('settings_general_banner'); */ ?></label>
							<i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php /*echo _l('settings_general_banner_tooltip'); */ ?>"></i>
							 	<div class="input-group browseImgBrand_blk">
		                            <span class="browseImgBrand" onclick="$(this).parent().find('input[type=file]').click();">Drag image or click to upload</span>
		                            <input name="banner" onchange="$(this).parents('.browseImgShow').find('.browseImgShow').html($(this).val().split(/[\\|/]/).pop());" style="display: none;" type="file" title="<?php /*echo _l('settings_general_banner_tooltip'); */ ?>">
		                          <span class="form-control"></span>
            </div>
            <div class="browseImgShow">

            </div>
        </div>
        -->
                </div>
            </div>
        <?php } ?>
        <!--<hr />-->
        <div class="clearfix"></div>
        <div class="brandingImg">
            <?php $company_logo = get_brand_option('company_logo');
            $src = "";
            ?>
            <?php if ($company_logo != '') {
                $clogoImagePath = FCPATH . 'uploads/brands/round_' . $company_logo;
                $src = base_url('uploads/brands/' . $company_logo);
                if (file_exists($clogoImagePath)) {
                    $src = base_url('uploads/brands/round_' . $company_logo);
                }
                ?>
                <div class="form-group col-sm-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bshead">
                                <h4 class="pull-left">BRAND IMAGE</h4>
                                <?php if (isset($packagename) && $packagename == "Paid") { ?>
                                    <?php if (has_permission('brands', '', 'delete')) { ?>
                                        <div class="pull-right">
                                            <a href="<?php echo admin_url('brand_settings/remove_company_logo'); ?>"
                                               data-toggle="tooltip"
                                               title="<?php echo _l('settings_general_company_remove_logo_tooltip'); ?>"
                                               class="_delete text-danger"><i class="fa fa-trash-o"></i></a>
                                        </div>
                                    <?php } ?>
                                <?php } ?>

                            </div>

                            <div class="bsBody">
                                <img src="<?php echo $src; ?>" class="img img-responsive"
                                     alt="<?php get_brand_option('companyname'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="form-group  col-sm-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bshead">
                                <h4 class="pull-left">BRAND IMAGE</h4>
                            </div>

                            <div class="company brandimage-pic banner-pic">

                                <div class="brandimage_imageview <?php echo empty($src) ? 'hidden' : ''; ?>">
                                    <img src="<?php echo $src; ?>"/>
                                    
                                    <!-- <a class="clicktoaddimage" href="javascript:void(0)"
                                       onclick="croppedDelete('brandimage');">
                                        <span><i class="fa fa-trash"></i></span></a>
                                    <a class="recropIcon_blk" href="javascript:void(0)"
                                       onclick="reCropp('brandimage');">
                                        <span> <i class="fa fa-crop" aria-hidden="true"></i></span></a> -->
                                        
                                    <div class="actionToEdit">
                                        <a class="clicktoaddimage" href="javascript:void(0)" onclick="croppedDelete('brandimage');">
                                            <span><i class="fa fa-trash"></i></span>
                                        </a>
                                        <a class="recropIcon_blk" href="javascript:void(0)" onclick="reCropp('brandimage');">
                                            <span><i class="fa fa-crop" aria-hidden="true"></i></span>
                                        </a>
                                    </div>
                                </div>
                                <div class="clicktoaddimage <?php echo !empty($src) ? 'hidden' : ''; ?>">
                                    <div class="drag_drop_image">
                                        <span class="icon"><i class="fa fa-image"></i></span>
                                        <span><?php echo _l('dd_upload'); ?></span>
                                    </div>
                                    <input id="brandimage_image" type="file" class="" name="company_logo"
                                           onchange="readFile(this,'brandimage');"/>
                                    <input type="hidden" id="brandimagebase64" name="brandimagebase64">
                                </div>
                                <div class="cropper" id="brandimage_croppie">
                                    <div class="copper_container">
                                        <div id="brandimage-cropper"></div>
                                        <div class="cropper-footer">
                                            <a type="button" class="btn btn-info p9 actionDone"
                                               type="button" id="" onclick="croppedResullt('brandimage');">
                                                <?php echo _l('save'); ?>
                                            </a>
                                            <button type="button" class="btn btn-default actionCancel"
                                                    data-dismiss="modal" onclick="croppedCancel('brandimage');">
                                                <?php echo _l('cancel'); ?>
                                            </button>
                                            <button type="button" class="btn btn-default actionChange"
                                                    onclick="croppedChange('brandimage');">
                                                <?php echo _l('change'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php $company_icon = get_brand_option('company_icon');
            $src = "";
            ?>

            <?php if ($company_icon != '') {
                $clogoImagePath = FCPATH . 'uploads/brands/round_' . $company_icon;
                $src = base_url('uploads/brands/' . $company_icon);
                if (file_exists($clogoImagePath)) {
                    $src = base_url('uploads/brands/round_' . $company_icon);
                }
                ?>
                <div class="form-group col-sm-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bshead">
                                <h4 class="pull-left">BRAND ICON</h4>
                                <?php if (isset($packagename) && $packagename == "Paid") { ?>
                                    <?php if (has_permission('brands', '', 'delete')) { ?>
                                        <div class="pull-right">
                                            <a href="<?php echo admin_url('brand_settings/remove_company_icon'); ?>"
                                               data-toggle="tooltip"
                                               title="<?php echo _l('settings_general_company_remove_logo_tooltip'); ?>"
                                               class="_delete text-danger"><i class="fa fa-trash-o"></i></a>
                                        </div>
                                    <?php } ?>
                                <?php } ?>

                            </div>

                            <div class="bsBody">
                                <img src="<?php echo $src; ?>" class="img img-responsive"
                                     alt="<?php get_brand_option('companyname'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="form-group  col-sm-4">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bshead">
                                <h4 class="pull-left">BRAND ICON</h4>
                            </div>

                            <div class="company profile-pic">

                                <div class="profile_imageview <?php echo empty($src) ? 'hidden' : ''; ?>">
                                    <img src="<?php echo $src; ?>"/>
                                    <!-- <a class="clicktoaddimage" href="javascript:void(0)"
                                       onclick="croppedDelete('profile');">
                                        <span><i class="fa fa-trash"></i></span></a>
                                    <a class="recropIcon_blk" href="javascript:void(0)"
                                       onclick="reCropp('profile');">
                                        <span> <i class="fa fa-crop" aria-hidden="true"></i></span></a> -->
                                        <div class="actionToEdit">
                                            <a class="clicktoaddimage" href="javascript:void(0)" onclick="croppedDelete('profile');">
                                                <span><i class="fa fa-trash"></i></span>
                                            </a>
                                            <a class="recropIcon_blk" href="javascript:void(0)" onclick="reCropp('profile');">
                                                <span><i class="fa fa-crop" aria-hidden="true"></i></span>
                                            </a>
                                        </div>
                                </div>
                                <div class="clicktoaddimage <?php echo !empty($src) ? 'hidden' : ''; ?>">
                                    <div class="drag_drop_image">
                                        <span class="icon"><i class="fa fa-image"></i></span>
                                        <span><?php echo _l('dd_upload'); ?></span>
                                    </div>
                                    <input id="profile_image" type="file" class="" name="company_icon"
                                           onchange="readFile(this,'profile');"/>
                                    <input type="hidden" id="imagebase64" name="imagebase64">
                                </div>
                                <div class="cropper" id="profile_croppie">
                                    <div class="copper_container">
                                        <div id="profile-cropper"></div>
                                        <div class="cropper-footer">
                                            <a type="button" class="btn btn-info p9 actionDone"
                                               type="button" id="" onclick="croppedResullt('profile');">
                                                <?php echo _l('save'); ?>
                                            </a>
                                            <button type="button" class="btn btn-default actionCancel"
                                                    data-dismiss="modal" onclick="croppedCancel('profile');">
                                                <?php echo _l('cancel'); ?>
                                            </button>
                                            <button type="button" class="btn btn-default actionChange"
                                                    onclick="croppedChange('profile');">
                                                <?php echo _l('change'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php $favicon = get_brand_option('favicon');
            $src = "";
            ?>
            <?php if ($favicon != '') {
                $faviconImagePath = FCPATH . 'uploads/brands/round_' . $favicon;
                $src = base_url('uploads/brands/' . $favicon);
                if (file_exists($faviconImagePath)) {
                    $src = base_url('uploads/brands/round_' . $favicon);
                }
                ?>
                <div class="form-group  col-sm-4 favicon">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="bshead">
                                <h4 class="pull-left">FAVICON IMAGE</h4>
                                <?php if (isset($packagename) && $packagename == "Paid") { ?>
                                    <?php if (has_permission('brands', '', 'delete')) { ?>
                                        <div class="pull-right">
                                            <a href="<?php echo admin_url('brand_settings/remove_favicon'); ?>"
                                               class="_delete text-danger"><i class="fa fa-trash-o"></i></a>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="bsBody">
                                <img src="<?php echo $src; ?>"
                                     class="img img-responsive">
                            </div>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="form-group  col-sm-4 favicon_upload">
                    <div class="col-md-12">
                        <div class="bshead">
                            <h4 class="pull-left">FAVICON IMAGE</h4>
                        </div>
                        <!--<div class="bsBody text-center browseImgShow_js">
                            <label for="favicon"
                                   class="control-label"><?php /*echo _l('settings_general_favicon'); */ ?></label>
                            <!--<input type="file" name="favicon" class="form-control">
                            <div class="input-group browseImgBrand_blk">

                                <span class="browseImgBrand"
                                      onclick="$(this).parent().find('input[type=file]').click();">Drag image or click to upload</span>
                                <input name="favicon"
                                       onchange="$(this).parents('.browseImgShow').find('.browseImgShow').html($(this).val().split(/[\\|/]/).pop());"
                                       style="display: none;" type="file">

                                <!--  <span class="form-control"></span>
                            </div>
                            <div class="browseImgShow">

                            </div>
                        </div>-->

                        <!--<div class="cover-pic">

                            <div class="imageview <?php /*echo empty($src) ? 'hidden' : ''; */ ?>">
                                <a class="clicktoaddimage" href="javascript:void(0)"><span><i class="fa fa-pencil"></i></span></a>
                                <img src="<?php /*echo $src; */ ?>"/>
                            </div>
                            <div class="clicktoaddimage <?php /*echo !empty($src) ? 'hidden' : ''; */ ?>">
                                <div class="drag_drop_image">
                                    <span class="icon"><i class="fa fa-image"></i></span>
                                    <span>Drag and Drop or Click here to add image</span>
                                </div>
                                <input type="file" class="" name="favicon" onchange="preview_banner(this);"/>
                            </div>
                        </div>-->
                        <div class="company favicon-pic">

                            <div class="favicon_imageview <?php echo empty($src) ? 'hidden' : ''; ?>">
                                <img src="<?php echo $src; ?>"/>
                                <!-- <a class="clicktoaddimage" href="javascript:void(0)"
                                   onclick="croppedDelete('favicon');">
                                    <span><i class="fa fa-trash"></i></span>
                                </a>
                                <a class="recropIcon_blk" href="javascript:void(0)"
                                   onclick="reCropp('favicon');">
                                    <span> <i class="fa fa-crop" aria-hidden="true"></i></span></a> -->
                                    
                                <div class="actionToEdit">
                                    <a class="clicktoaddimage" href="javascript:void(0)" onclick="croppedDelete('favicon');">
                                        <span><i class="fa fa-trash"></i></span>
                                    </a>
                                    <a class="recropIcon_blk" href="javascript:void(0)" onclick="reCropp('favicon');">
                                        <span><i class="fa fa-crop" aria-hidden="true"></i></span>
                                    </a>
                                </div>
                            </div>
                            <div class="clicktoaddimage <?php echo !empty($src) ? 'hidden' : ''; ?>">
                                <div class="drag_drop_image">
                                    <span class="icon"><i class="fa fa-image"></i></span>
                                    <span><?php echo _l('dd_upload'); ?></span>
                                </div>
                                <input id="favicon_image" type="file" class="" name="favicon"
                                       onchange="readFile(this,'favicon');"/>
                                <input type="hidden" id="favicon64" name="favicon64">
                            </div>
                            <div class="cropper" id="favicon_croppie">
                                <div class="copper_container">
                                    <div id="favicon-cropper"></div>
                                    <div class="cropper-footer">
                                        <a type="button" class="btn btn-info p9 faviconDone"
                                           type="button" id="" onclick="croppedResullt('favicon');">
                                            <?php echo _l('save'); ?>
                                        </a>
                                        <button type="button" class="btn btn-default faviconCancel"
                                                data-dismiss="modal" onclick="croppedCancel('favicon');">
                                            <?php echo _l('close'); ?>
                                        </button>
                                        <button type="button" class="btn btn-default actionChange"
                                                onclick="croppedChange('favicon');">
                                            <?php echo _l('change'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <!--<div class="col-sm-4">
                <div class="bshead">
                    <h4 class="pull-left">COMPANY NAME</h4>
                </div>
                <div class="bsBody">
                    <?php /*$attrs = (get_brand_option('companyname') != '' ? array() : array('autofocus' => true)); */ ?>
                    <?php /*echo render_input('settings[companyname]', 'settings_general_company_name', get_brand_option('companyname'), 'text', $attrs); */ ?>
                    <span id="brandmsg"></span>
                </div>
            </div>-->
        </div>
    </div>
</div>

<!-- 	<script>

			$(".browseImgBrand_blk input[type=file]").change(function(){
			    readURL(this);
			    alert();
			});
		
		
			function readURL(input) {
			    if (input.files && input.files[0]) {
			        var reader = new FileReader();

			        reader.onload = function (e) {
			            $('.browseImgShow img').attr('src', e.target.result);
			        }

			        reader.readAsDataURL(input.files[0]);
			    }
			}
	</script> -->
<script type="text/javascript">

    function PreviewImage() {
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById("uploadImage").files[0]);

        oFReader.onload = function (oFREvent) {
            document.getElementById("uploadPreview").src = oFREvent.target.result;
        };
    };

</script>