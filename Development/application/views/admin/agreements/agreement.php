<?php init_head();

$session_data = $_SESSION;
if (isset($session_data['is_sido_admin'])) {
    $is_sido_admin = $session_data['is_sido_admin'];
    $is_admin = $session_data['is_admin'];
} else {
    $is_sido_admin = 1;
    $is_admin = 1;
}
$other_token = array();
if (!is_staff_logged_in() || is_client_logged_in()) {
    $other_token['emailsignature'] = get_option('email_signature');
} else {
    $this->db->select('email_signature')->from('tblstaff')->where('staffid', get_staff_user_id());
    $signature = $this->db->get()->row()->email_signature;
    if (empty($signature)) {
        if ($is_sido_admin == 0 && $is_admin == 0) {
            $other_token['emailsignature'] = get_brand_option('email_signature');
        } else {
            $other_token['emailsignature'] = get_option('email_signature');
        }
    } else {
        $other_token['emailsignature'] = $signature;
    }
}

$logo_width = do_action('merge_field_logo_img_width', '');
$logo_width = $logo_width != "" ? $logo_width : "100px";
$image_url = base_url('uploads/company/' . get_brand_option('company_logo'));
$other_token['logoimage'] = '<img draggable="false" src="' . base_url('uploads/brands/' . get_brand_option('company_logo')) . '"' . ($logo_width != '' ? ' width="' . $logo_width . '"' : '') . ' >';

$other_token['portalurl'] = admin_url();
$other_token['crmurl'] = site_url();
$other_token['adminurl'] = admin_url();
$other_token['clienturl'] = site_url();

if ($is_sido_admin == 0 && $is_admin == 0) {
    $other_token['maindomain'] = get_brand_option('main_domain');
    $other_token['companyname'] = get_brand_option('companyname');
} else {
    $other_token['maindomain'] = get_option('main_domain');
    $other_token['companyname'] = get_option('companyname');
}
?>
<div id="wrapper" class="agreement-page">
    <div class="content">


        <div class="breadcrumb">
            <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <a href="<?php echo admin_url('setup'); ?>">Settings</a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <a href="<?php echo admin_url('agreements'); ?>">Agreements</a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <?php if (isset($agreement)) { ?>
                <span><?php echo $agreement->name; ?></span>
            <?php } else { ?>
                <span>New Agreement</span>
            <?php } ?>
        </div>

        <h1 class="pageTitleH1"><i class="fa fa-files-o"></i><?php echo $title; ?></h1>
        <div class="clearfix"></div>
        <div class="row">
            <?php echo form_open($this->uri->uri_string(), array('id' => 'agreement_template')); ?>
            <div class="col-md-9 temp_both_comm template_section temp_common">
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <?php $available_merge_fields = get_agreement_merge_fields(); ?>
                        <?php $value = (isset($agreement) ? $agreement->name : ''); ?>
                        <div class="nameTxt form-group">
                            <label class="control-label" for="name">
                                <small class="req text-danger">*</small>
                                Name</label>
                            <input id="name" class="form-control" name="name" autofocus="1"
                                   value="<?php echo $value; ?>" type="text">
                        </div>
                        <?php $content = (isset($agreement) ? $agreement->content : ''); ?>
                        <div class="agreementContent form-group">
                            <label for="content" class="control-label">
                                <small class="req text-danger">*</small>
                                Content</label>
                            <a class=" token_btn btn btn-info pull-right mbot10" href="javascript:void(0)" role="button"
                               data-pid="#agreement_tokens">
                                <span class=""><?php echo _l('agreement_template_add_token_section_title'); ?></span>
                                <!--<i class="fa fa-caret-down pull-right"></i>-->
                            </a>
                            <div id="agreement_tokens" class="available_merge_fields_container">
                                <div class="clearfix"></div>
                                <?php ?>
                                <div class="token_groups-main panel_s m0">
                                    <div class="token_groups">
                                        <?php
                                        foreach ($available_merge_fields as $key => $value) {
                                            $org_token_group = strtolower($key);
                                            $fin_token_value = str_replace(" ", "", $org_token_group);
                                            ?>
                                            <a href="javascript:void(0)"
                                               class="btn btn-info  <?php echo $fin_token_value; ?>"
                                               data-pid="<?php echo $fin_token_value; ?>"><?php echo ucfirst($key); ?></a>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php
                                foreach ($available_merge_fields as $key => $value) {
                                    $org_token_group = strtolower($key);
                                    $fin_token_value = str_replace(" ", "", $org_token_group);
                                    $parent = "";
                                    if ($fin_token_value == "teammember") {
                                        $parent = "Member";
                                    } elseif ($fin_token_value == "clients") {
                                        $parent = "Client";
                                    } elseif ($fin_token_value == "leads") {
                                        $parent = "Lead";
                                    }elseif ($fin_token_value == "meetings") {
                                        $parent = "Meetings";
                                    } else {
                                        $parent = "";
                                    }
                                    ?>
                                    <div class="tag-group-container tags_<?php echo $fin_token_value; ?>">
                                        <?php foreach ($value as $key1 => $new_value) {
                                            if (isset($other_token) && !empty($other_token) && $fin_token_value == "other") {

                                                $new_value['name'] = str_replace(" ", "", $new_value['name']);
                                                if ($other_token[strtolower($new_value['name'])] == "") {
                                                    $other_token[strtolower($new_value['name'])] = $new_value['name'];
                                                }
                                                ?>
                                                <div class="tags <?php echo $fin_token_value; ?>">
                                            <span>
                                                <span draggable="true" class="add_merge_field"
                                                      data-val='<?php echo $other_token[strtolower($new_value['name'])]; ?>'>
                                                    <?php echo $other_token[strtolower($new_value['name'])] ? "<b>" . $new_value['name'] . "</b> : " . $other_token[strtolower($new_value['name'])] : "<b>" . $new_value['name'] . "</b>"; ?>
                                                </span>
                                            </span>
                                                </div>
                                            <?php } else { ?>
                                                <div class="tags <?php echo $fin_token_value; ?>">
                                            <span>
                                                <?php
                                                if (isset($_GET['lid']) || isset($_GET['pid'])) {
                                                    $merge_key = strtolower($new_value['name']);
                                                    $merge_key = str_replace(" ", "", $merge_key);
                                                    $val = isset($merge_fields[$merge_key]) ? $merge_fields[$merge_key] : $new_value['name'];
                                                } else {
                                                    $val = $new_value['name'];
                                                }
                                                $val = str_replace(" ", "", $val);
                                                ?>
                                                <a href="#" class="add_merge_field"
                                                   data-val="<?php echo $parent . $val; ?>">
                                                    <?php echo $new_value['name']; ?>
                                                </a>
                                            </span>
                                                </div>
                                            <?php }
                                        } ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div id="agreement_wrapper">
                                <textarea id="content" name="content"><?php echo $content; ?></textarea>
                            </div>
                        </div>

                        <div class="text-right btn-toolbar-container-out">
                            <input type="hidden" name="agreementid"
                                   value="<?php echo isset($agreement) ? $agreement->templateid : ""; ?>">
                            <button class="btn btn-default" type="button"
                                    onclick="location.href='<?php echo base_url(); ?>admin/agreements'"><?php echo _l('Cancel'); ?></button>
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
</div>
<?php init_tail(); ?>
<script>
    //_validate_form($('form'),{name:'required',content:'required'});
    $(document).ready(function () {
        CKEDITOR.config.height = 580;
        CKEDITOR.replace('content', {
            toolbar: [
                {name: 'document', groups: ['mode', 'document', 'doctools'], items: ['Source', '-']},
                /*{ name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },*/
                /*{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker' ], items: [ 'Find', 'Replace', '-', 'SelectAll', '-', 'Scayt' ] },*/
                /*{ name: 'forms', items: [ 'Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField' ] },*/
                /*'/',*/
                /*{ name: 'forms', items: [ 'Button' ] },*/
                {name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize']},
                {name: 'colors', items: ['TextColor', 'BGColor']},
                {
                    name: 'basicstyles',
                    groups: ['basicstyles', 'cleanup'],
                    items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'RemoveFormat']
                },
                {
                    name: 'paragraph',
                    groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'links'],
                    items: ['Outdent', 'Indent', 'Blockquote', 'CreateDiv', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'BidiLtr', 'BidiRtl', 'Language', 'Link', 'Unlink', 'Anchor', 'NumberedList', 'BulletedList', 'Image']
                },
                /*{name: 'links', items: ['Link', 'Unlink', 'Anchor']},*/
                /*{
                    name: 'insert',
                    items: ['Image', 'Table', 'HorizontalRule', 'Smiley', 'SpecialChar', 'PageBreak', 'Iframe']
                },*/

                /*'/',*/
                /*{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] },*/
                /*{ name: 'others', items: [ '-' ] },
                { name: 'about', items: [ 'About' ] }*/
            ],
            removeButtons: 'HorizontalRule,Table,PageBreak,Iframe,Language,BidiRtl,BidiLtr,Outdent,Indent,RemoveFormat,Blockquote,Smiley,Strike,Subscript,Superscript,Anchor,help,about',
            image2_alignClasses: ['image-align-left', 'image-align-center', 'image-align-right'],
            image2_disableResizer: true,
            extraPlugins: 'autogrow',
        });
        CKEDITOR.config.autoParagraph = false;
        CKEDITOR.config.enterMode = CKEDITOR.ENTER_DIV;
        CKEDITOR.editorConfig = function (config) {
            // Define changes to default configuration here. For example:
            config.language = 'fr';
            config.uiColor = '#000';
            config.title = false;
            config.fillEmptyBlocks = false;
            config.autoParagraph = false;
            config.allowedContent = true;
            config.enterMode = CKEDITOR.ENTER_DIV;
        };
        $("#agreement_template").validate(
            {
                ignore: [],
                rules: {
                    name: {
                        required: true,
                        remote: {
                            url: site_url + "admin/misc/agreement_title_exists",
                            type: 'post',
                            data: {
                                name: function () {
                                    return $('input[name="name"]').val();
                                },
                                agreementid: function () {
                                    return $('input[name="agreementid"]').val();
                                }
                            }
                        }
                    },
                    content: {
                        required: function () {
                            CKEDITOR.instances.content.updateElement();
                        },
                        minlength: 1
                    }
                }
            });

        $('.add_merge_field').on('click', function (e) {
            e.preventDefault();
            /*CKEDITOR.instances.content.insertHtml("&nbsp;<div style='display: inline;font-weight: bold;text-decoration: underline dashed'>" + $(this).attr('data-val') + "</div>&nbsp;")*/
            e.preventDefault();
            CKEDITOR.instances.content.insertHtml("&nbsp;<div class='token'>" + $(this).attr('data-val') + "</div>&nbsp;");
        });
        $('.add_merge_html_field').on('click', function (e) {
            e.preventDefault();
            console.log($(this));
            CKEDITOR.instances.content.insertHtml($(this).find("span").html());
        });

    });

    /*$("#token_parent").append($("#token_parent option").remove().sort(function(a, b) {
         var at = $(a).text(), bt = $(b).text();
         return (at > bt)?1:((at < bt)?-1:0);
     }));*/

    $(".show_hide").click(function () {

        $(".template_section, .token_section").toggleClass("temp_common");

    });
    /*$('body').on('click','#agreement_tokens .token_btn',function () {
        var pid = $(this).attr('data-pid');
        $('i',this).toggleClass('fa-caret-up fa-caret-down');
        $(pid+' .panel_s > div ').slideToggle();
        $(".token_groups a").removeClass('active');
        $(".tag-group-container").slideUp();

    });
    $(".token_groups a").click(function () {
        var curr_val = ".tags_"+$(this).attr('data-pid');
        if($(this).hasClass('active')){
            $(this).removeClass('active');
            $(".tag-group-container").slideUp();
        }else {
            $(".token_groups a").removeClass('active');
            $(this).addClass('active');
            $(".tag-group-container").hide();
            $(".tag-group-container"+curr_val).slideDown();
        }

    });*/

</script>
</body>
</html>