<?php init_head(); ?>
<?php
$brandid = get_user_session();
?>
    <div id="wrapper">
        <div class="content files-page">
            <div class="breadcrumb">
                <?php /*if (isset($pg) && $pg == 'home') { */ ?>
                <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                <i class="fa fa-angle-right breadcrumb-arrow"></i>
                <?php /*} */ ?>
                <?php if (isset($lid)) { ?>
                    <a href="<?php echo admin_url('leads/'); ?>">Leads</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('leads/dashboard/' . $lid); ?>"><?php echo($lname); ?></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                <?php } elseif (isset($pid)) { ?>
                    <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('projects/dashboard/' . $pid); ?>"><?php echo($lname); ?></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                <?php } else { ?>
                <?php } ?>
                <span>Files</span>
            </div>
            <h1 class="pageTitleH1"><i class="fa fa-newspaper-o"></i><?php echo $title; ?></h1>
            <div class="clearfix"></div>
            <div class="panel_s btmbrd">
                <div class="row">
                    <?php if (isset($lid) && $lid != ""){ ?>
                    <div class="col-md-12">
                        <div class="panel_s btmbrd">
                            <div class="panel-body">
                                <div class="_buttons">
                                    <?php if (has_permission('files', '', 'create')) { ?>
                                        <a href="javascript:void(0);"
                                           class="btn btn-info pull-left add-files display-block">Add Files</a>
                                    <?php } ?>
                                </div>
                                <div class="clearfix"></div>
                                <?php if (has_permission('files', '', 'create')) { ?>
                                    <div class="file-upload-wrapper" style="display:none">
                                        <div class="close-btn btn btn-primary">
                                            <a href="javascript:void(0)" class="close-files"><i class="fa fa-close"></i></a>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5 upload-file">
                                                <?php echo form_open_multipart(admin_url('leads/upload_file/' . $lid), array('class' => 'dropzone', 'id' => 'lead-files-upload')); ?>
                                                <input type="file" name="file" multiple/>
                                                <?php echo form_close(); ?>
                                            </div>
                                            <div class="col-md-2"><span class="orTxt">OR</span></div>
                                            <div class="col-md-5">
                                                <button type="button" class="btn btn-info btn-lg open-exist"
                                                        data-toggle="modal" data-target="#">Choose from existing
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                <?php } ?>

                                <div class="col-md-12">
                                    <table class="table dt-table scroll-responsive table-leads-files table-striped"
                                           data-order-col="3" data-order-type="desc">
                                        <thead>
                                        <tr>
                                            <th><?php echo _l('lead_file_filename'); ?></th>
                                            <th><?php echo _l('lead_file_size'); ?></th>
                                            <th><?php echo _l('lead_file_uploaded_by'); ?></th>
                                            <th><?php echo _l('lead_file_dateadded'); ?></th>
                                            <th><?php echo _l(''); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($files as $file) {
                                            $LeadFilePath = 'uploads/leads/' . $lid . '/' . $file['file_name'];
                                            $extension = get_file_extension($file['file_name']);
                                            ?>
                                            <tr>
                                                <td data-order="<?php echo $file['file_name']; ?>">
                                                    <a href="<?php echo base_url($LeadFilePath); ?>" download>
                                                        <?php if (is_image(LEAD_ATTACHMENTS_FOLDER . $lid . '/' . $file['file_name'])) { ?>
                                                            <img src="<?php echo base_url($LeadFilePath); ?>"
                                                                 class="img img-responsive mright20 pull-left"
                                                                 alt="<?php echo $file['file_name']; ?>" width="50">
                                                            <span><?php echo $file['file_name']; ?></span>
                                                        <?php } elseif (in_array($extension, array('doc', 'docx'))) { ?>
                                                            <img src="<?php echo base_url("assets/images/docx.png"); ?>"
                                                                 class="img img-responsive mright20 pull-left"
                                                                 alt="<?php echo $file['file_name']; ?>" width="50">
                                                            <span><?php echo $file['file_name']; ?></span>
                                                        <?php } elseif (in_array($extension, array('pdf'))) { ?>
                                                            <img src="<?php echo base_url("assets/images/pdf.png"); ?>"
                                                                 class="img img-responsive mright20 pull-left"
                                                                 alt="<?php echo $file['file_name']; ?>" width="50">
                                                            <span><?php echo $file['file_name']; ?></span>
                                                        <?php } elseif (in_array($extension, array('xls', 'xlsx'))) { ?>
                                                            <img src="<?php echo base_url("assets/images/xlsx.png"); ?>"
                                                                 class="img img-responsive mright20 pull-left"
                                                                 alt="<?php echo $file['file_name']; ?>" width="50">
                                                            <span><?php echo $file['file_name']; ?></span>
                                                        <?php } ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <span><?php echo format_size(filesize(LEAD_ATTACHMENTS_FOLDER . $lid . '/' . $file['file_name'])); ?></span>
                                                </td>
                                                <td>
                                                    <?php if ($file['staffid'] != 0) {
                                                        $_data = '<a href="' . admin_url('staff/profile/' . $file['staffid']) . '">' . staff_profile_image($file['staffid'], array(
                                                                'staff-profile-image-small'
                                                            )) . '</a>';
                                                        $_data .= get_staff_full_name($file['staffid']);
                                                        echo $_data;
                                                    }
                                                    ?>
                                                </td>
                                                <td data-order="<?php echo $file['dateadded']; ?>"><?php echo _dt($file['dateadded']); ?></td>
                                                <td>
                                                    <?php if ($file['staffid'] == get_staff_user_id() || has_permission('files', '', 'delete') || $file['brandid'] == $brandid){ ?>
                                                    <div class='text-right mright10'>
                                                        <a class='show_act' href='javascript:void(0)'>
                                                            <i class='fa fa-ellipsis-v' aria-hidden='true'></i>
                                                        </a>
                                                    </div>
                                                    <div class='table_actions'>
                                                        <ul>
                                                            <li><a data-toggle="tooltip" title="Delete"
                                                                   href="<?php echo admin_url('leads/remove_file/' . $lid . '/' . $file['id']); ?>"
                                                                   class=" _delete"><i
                                                                            class="fa fa-remove"></i>Delete</a></li>
                                                            <?php } ?>
                                                            <li><a data-toggle="tooltip" title="Download"
                                                                   href="<?php echo base_url($LeadFilePath); ?>"
                                                                   download class=""><i class="fa fa-download"></i>Download</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div id="exist-files-wrapper" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Choose File</h4>
                </div>
                <div class="modal-body">
                    <div class="dt-loader" style="display:none"></div>
                    <div id="elfinder_existing"></div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="file-path"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-orange add-from-exist"
                            data-loading-text="<?php echo _l('wait_text'); ?>">Add
                    </button
                </div>
            </div>

        </div>
    </div>
    </div>
    <?php init_tail(); ?>
    <script type="text/javascript" charset="utf-8">

        if ($('#lead-files-upload').length > 0) {
            if (typeof(leadFilesUpload) != 'undefined') {
                leadFilesUpload.destroy();
            }
            leadFilesUpload = new Dropzone('#lead-files-upload', {
                paramName: "file",
                dictFileTooBig: appLang.file_exceeds_maxfile_size_in_form,
                dictDefaultMessage: appLang.drop_files_here_to_upload,
                dictFallbackMessage: appLang.browser_not_support_drag_and_drop,
                dictRemoveFile: appLang.remove_file,
                dictCancelUpload: appLang.cancel_upload,
                acceptedFiles: app_allowed_files,
                maxFilesize: (max_php_ini_upload_size_bytes / (1024 * 1024)).toFixed(0),
                accept: function (file, done) {
                    done();
                },
                success: function (file, response) {
                    if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                        alert_float('success', "File(s) uploaded successfully");
                        window.location.reload();
                    }
                },
                error: function (file, response) {
                    alert_float('danger', response);
                },
                sending: function (file, xhr, formData) {
                    console.log(formData);

                }
            });
        }
        $(function () {
            $(".add-from-exist").on("click", function () {
                $(".dt-loader").show();
                $("#elfinder_existing").addClass("uploader-disabled");
                var file_path = $("#file-path").val();
                $.post(admin_url + 'leads/upload_exist_file', {
                    file_path: file_path,
                    leadid: "<?php echo $lid; ?>"
                }).done(function (data) {
                    if (data == "success") {
                        $(".dt-loader").hide();
                        $("#elfinder_existing").removeClass("uploader-disabled");
                        $('.modal-footer .btn-default').click();
                        window.location.reload();
                        alert_float('success', "File(s) added successfully.");
                    } else {
                        $(".dt-loader").hide();
                        $("#elfinder_existing").removeClass("uploader-disabled");
                        $('.modal-footer .btn-default').click();
                        window.location.reload();
                        alert_float('error', data);
                    }

                });
            });

            $(".open-exist").click(function () {
                $('#elfinder_existing').elfinder({
                    url: admin_url + 'files/elfinder_init',
                    lang: '<?php echo get_media_locale($locale); ?>',
                    height: 400,
                    uiOptions: {
                        toolbar: []
                    },
                    handlers: {
                        select: function (event, elfinderInstance) {
                            //console.log(event.data);
                            var selected = event.data.selected;
                            if (selected.length > 0) {
                                var filepath = elfinderInstance.path(selected[0]);
                                $("#file-path").val(filepath);
                            }

                        }
                    }
                });
                $('#exist-files-wrapper').modal('show');
                $('.ui-widget-header').hide();
            });
            $(".add-files").on("click", function () {
                $(".file-upload-wrapper").slideDown("slow");
            });

            $(".close-files").on("click", function () {
                $(".file-upload-wrapper").slideUp("slow");
            });

        });
    </script>
    </body>
    </html>

<?php } elseif (isset($pid) && $pid != "") { ?>
    <div class="col-md-12">
        <div class="panel_s btmbrd">
            <div class="panel-body">
                <div class="_buttons">
                    <?php if (has_permission('files', '', 'create')) { ?>
                        <a href="javascript:void(0);" class="btn btn-info pull-left add-files display-block">Add
                            Files</a>
                    <?php } ?>
                </div>
                <div class="clearfix"></div>
                <?php if (has_permission('files', '', 'create')) { ?>
                    <div class="file-upload-wrapper" style="display:none">
                        <div class="close-btn btn btn-primary">
                            <a href="javascript:void(0)" class="close-files"><i class="fa fa-close"></i></a>
                        </div>
                        <div class="row">
                            <div class="col-md-5 upload-file">
                                <?php echo form_open_multipart(admin_url('projects/upload_file/' . $pid), array('class' => 'dropzone', 'id' => 'project-files-upload')); ?>
                                <input type="file" name="file" multiple/>
                                <?php echo form_close(); ?>
                            </div>
                            <div class="col-md-2"><span class="orTxt">OR</span></div>
                            <div class="col-md-5">
                                <button type="button" class="btn btn-info btn-lg open-exist" data-toggle="modal"
                                        data-target="#">Choose from existing
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                <?php } ?>

                <div class="col-md-12">
                    <table class="table dt-table scroll-responsive table-leads-files table-striped" data-order-col="3"
                           data-order-type="desc">
                        <thead>
                        <tr>
                            <th><?php echo _l('lead_file_filename'); ?></th>
                            <th><?php echo _l('lead_file_size'); ?></th>
                            <th><?php echo _l('lead_file_uploaded_by'); ?></th>
                            <th><?php echo _l('lead_file_dateadded'); ?></th>
                            <th><?php echo _l(''); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($files as $file) {
                            $ProjectFilePath = 'uploads/projects/' . $file['rel_id'] . '/' . $file['file_name'];
                            $extension = get_file_extension($file['file_name']);
                            ?>
                            <tr>
                                <td data-order="<?php echo $file['file_name']; ?>">
                                    <a href="<?php echo base_url($ProjectFilePath); ?>" download>
                                        <?php if (is_image(PROJECT_ATTACHMENTS_FOLDER . $file['rel_id'] . '/' . $file['file_name'])) { ?>
                                            <img src="<?php echo base_url($ProjectFilePath); ?>"
                                                 class="img img-responsive mright20 pull-left"
                                                 alt="<?php echo $file['file_name']; ?>" width="50">
                                            <span><?php echo $file['file_name']; ?></span>
                                        <?php } elseif (in_array($extension, array('doc', 'docx'))) { ?>
                                            <img src="<?php echo base_url("assets/images/docx.png"); ?>"
                                                 class="img img-responsive mright20 pull-left"
                                                 alt="<?php echo $file['file_name']; ?>" width="50">
                                            <span><?php echo $file['file_name']; ?></span>
                                        <?php } elseif (in_array($extension, array('pdf'))) { ?>
                                            <img src="<?php echo base_url("assets/images/pdf.png"); ?>"
                                                 class="img img-responsive mright20 pull-left"
                                                 alt="<?php echo $file['file_name']; ?>" width="50">
                                            <span><?php echo $file['file_name']; ?></span>
                                        <?php } elseif (in_array($extension, array('xls', 'xlsx'))) { ?>
                                            <img src="<?php echo base_url("assets/images/xlsx.png"); ?>"
                                                 class="img img-responsive mright20 pull-left"
                                                 alt="<?php echo $file['file_name']; ?>" width="50">
                                            <span><?php echo $file['file_name']; ?></span>
                                        <?php } ?>
                                    </a>
                                </td>
                                <td>
                                    <span><?php echo format_size(filesize(PROJECT_ATTACHMENTS_FOLDER . $file['rel_id'] . '/' . $file['file_name'])); ?></span>
                                </td>
                                <td>
                                    <?php if ($file['staffid'] != 0) {
                                        $_data = '<a href="' . admin_url('staff/profile/' . $file['staffid']) . '">' . staff_profile_image($file['staffid'], array(
                                                'staff-profile-image-small'
                                            )) . '</a>';
                                        $_data .= get_staff_full_name($file['staffid']);
                                        echo $_data;
                                    }
                                    ?>
                                </td>
                                <td data-order="<?php echo $file['dateadded']; ?>"><?php echo _dt($file['dateadded']); ?></td>
                                <td>
                                    <?php if ($file['staffid'] == get_staff_user_id() || has_permission('files', '', 'delete') || $file['brandid'] == $brandid) { ?>
                                        <a data-toggle="tooltip" title="Delete"
                                           href="<?php echo admin_url('projects/remove_custom_file/' . $pid . '/' . $file['id']); ?>"
                                           class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                                    <?php } ?>
                                    <a data-toggle="tooltip" title="Download"
                                       href="<?php echo base_url($ProjectFilePath); ?>" download
                                       class="btn btn-orange btn-icon"><i class="fa fa-download"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    <div id="exist-files-wrapper" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Choose File</h4>
                </div>
                <div class="modal-body">
                    <div class="dt-loader" style="display:none"></div>
                    <div id="elfinder_existing"></div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="file-path"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-orange add-from-exist"
                            data-loading-text="<?php echo _l('wait_text'); ?>">Add
                    </button
                </div>
            </div>

        </div>
    </div>
    </div>
    <?php init_tail(); ?>
    <script type="text/javascript" charset="utf-8">

        if ($('#project-files-upload').length > 0) {
            if (typeof(leadFilesUpload) != 'undefined') {
                leadFilesUpload.destroy();
            }
            leadFilesUpload = new Dropzone('#project-files-upload', {
                paramName: "file",
                dictFileTooBig: appLang.file_exceeds_maxfile_size_in_form,
                dictDefaultMessage: appLang.drop_files_here_to_upload,
                dictFallbackMessage: appLang.browser_not_support_drag_and_drop,
                dictRemoveFile: appLang.remove_file,
                dictCancelUpload: appLang.cancel_upload,
                acceptedFiles: app_allowed_files,
                maxFilesize: (max_php_ini_upload_size_bytes / (1024 * 1024)).toFixed(0),
                accept: function (file, done) {
                    done();
                },
                success: function (file, response) {
                    if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                        alert_float('success', "File(s) uploaded successfully");
                        window.location.reload();
                    }
                },
                error: function (file, response) {
                    alert_float('danger', response);
                },
                sending: function (file, xhr, formData) {
                    console.log(formData);

                }
            });
        }
        $(function () {
            $(".add-from-exist").on("click", function () {
                $(".dt-loader").show();
                $("#elfinder_existing").addClass("uploader-disabled");
                var file_path = $("#file-path").val();
                $.post(admin_url + 'projects/upload_exist_file', {
                    file_path: file_path,
                    projectid: "<?php echo $pid; ?>"
                }).done(function (data) {
                    if (data == "success") {
                        $(".dt-loader").hide();
                        $("#elfinder_existing").removeClass("uploader-disabled");
                        $('.modal-footer .btn-default').click();
                        window.location.reload();
                        alert_float('success', "File(s) added successfully.");
                    } else {
                        $(".dt-loader").hide();
                        $("#elfinder_existing").removeClass("uploader-disabled");
                        $('.modal-footer .btn-default').click();
                        window.location.reload();
                        alert_float('error', data);
                    }

                });
            });

            $(".open-exist").click(function () {
                $('#elfinder_existing').elfinder({
                    url: admin_url + 'files/elfinder_init',
                    lang: '<?php echo get_media_locale($locale); ?>',
                    height: 400,
                    uiOptions: {
                        toolbar: []
                    },
                    handlers: {
                        select: function (event, elfinderInstance) {
                            //console.log(event.data);
                            var selected = event.data.selected;
                            if (selected.length > 0) {
                                var filepath = elfinderInstance.path(selected[0]);
                                $("#file-path").val(filepath);
                            }

                        }
                    }
                });
                $('#exist-files-wrapper').modal('show');
                $('.ui-widget-header').hide();
            });
            $(".add-files").on("click", function () {
                $(".file-upload-wrapper").slideDown("slow");
            });

            $(".close-files").on("click", function () {
                $(".file-upload-wrapper").slideUp("slow");
            });

        });
    </script>
    </body>
    </html>

<?php } elseif (isset($eid) && $eid != "") { ?>
    <div class="col-md-12">
        <div class="panel_s btmbrd">
            <div class="panel-body">
                <div class="_buttons">
                    <?php if (has_permission('files', '', 'create')) { ?>
                        <a href="javascript:void(0);" class="btn btn-info pull-left add-files display-block">Add
                            Files</a>
                    <?php } ?>
                </div>
                <div class="clearfix"></div>
                <?php if (has_permission('files', '', 'create')) { ?>
                    <div class="file-upload-wrapper" style="display:none">
                        <div class="close-btn btn btn-primary">
                            <a href="javascript:void(0)" class="close-files"><i class="fa fa-close"></i></a>
                        </div>
                        <div class="row">
                            <div class="col-md-5 upload-file">
                                <?php echo form_open_multipart(admin_url('projects/upload_event_file/' . $eid), array('class' => 'dropzone', 'id' => 'project-files-upload')); ?>
                                <input type="file" name="file" multiple/>
                                <?php echo form_close(); ?>
                            </div>
                            <div class="col-md-2"><span class="orTxt">OR</span></div>
                            <div class="col-md-5">
                                <button type="button" class="btn btn-info btn-lg open-exist" data-toggle="modal"
                                        data-target="#">Choose from existing
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                <?php } ?>

                <div class="col-md-12">
                    <table class="table dt-table scroll-responsive table-leads-files table-striped" data-order-col="3"
                           data-order-type="desc">
                        <thead>
                        <tr>
                            <th><?php echo _l('lead_file_filename'); ?></th>
                            <th><?php echo _l('lead_file_size'); ?></th>
                            <th><?php echo _l('lead_file_uploaded_by'); ?></th>
                            <th><?php echo _l('lead_file_dateadded'); ?></th>
                            <th><?php echo _l(''); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($files as $file) {
                            $ProjectFilePath = 'uploads/projects/' . $eid . '/' . $file['file_name'];
                            $extension = get_file_extension($file['file_name']);
                            ?>
                            <tr>
                                <td data-order="<?php echo $file['file_name']; ?>">
                                    <a href="<?php echo base_url($ProjectFilePath); ?>" download>
                                        <?php if (is_image(PROJECT_ATTACHMENTS_FOLDER . $eid . '/' . $file['file_name'])) { ?>
                                            <img src="<?php echo base_url($ProjectFilePath); ?>"
                                                 class="img img-responsive mright20 pull-left"
                                                 alt="<?php echo $file['file_name']; ?>" width="50">
                                            <span><?php echo $file['file_name']; ?></span>
                                        <?php } elseif (in_array($extension, array('doc', 'docx'))) { ?>
                                            <img src="<?php echo base_url("assets/images/docx.png"); ?>"
                                                 class="img img-responsive mright20 pull-left"
                                                 alt="<?php echo $file['file_name']; ?>" width="50">
                                            <span><?php echo $file['file_name']; ?></span>
                                        <?php } elseif (in_array($extension, array('pdf'))) { ?>
                                            <img src="<?php echo base_url("assets/images/pdf.png"); ?>"
                                                 class="img img-responsive mright20 pull-left"
                                                 alt="<?php echo $file['file_name']; ?>" width="50">
                                            <span><?php echo $file['file_name']; ?></span>
                                        <?php } elseif (in_array($extension, array('xls', 'xlsx'))) { ?>
                                            <img src="<?php echo base_url("assets/images/xlsx.png"); ?>"
                                                 class="img img-responsive mright20 pull-left"
                                                 alt="<?php echo $file['file_name']; ?>" width="50">
                                            <span><?php echo $file['file_name']; ?></span>
                                        <?php } ?>
                                    </a>
                                </td>
                                <td>
                                    <span><?php echo format_size(filesize(PROJECT_ATTACHMENTS_FOLDER . $eid . '/' . $file['file_name'])); ?></span>
                                </td>
                                <td>
                                    <?php if ($file['staffid'] != 0) {
                                        $_data = '<a href="' . admin_url('staff/profile/' . $file['staffid']) . '">' . staff_profile_image($file['staffid'], array(
                                                'staff-profile-image-small'
                                            )) . '</a>';
                                        $_data .= get_staff_full_name($file['staffid']);
                                        echo $_data;
                                    }
                                    ?>
                                </td>
                                <td data-order="<?php echo $file['dateadded']; ?>"><?php echo _dt($file['dateadded']); ?></td>
                                <td>
                                    <?php if ($file['staffid'] == get_staff_user_id() || has_permission('files', '', 'delete') || $file['brandid'] == $brandid) { ?>
                                        <a data-toggle="tooltip" title="Delete"
                                           href="<?php echo admin_url('projects/remove_custom_file/' . $eid . '/' . $file['id']); ?>"
                                           class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                                    <?php } ?>
                                    <a data-toggle="tooltip" title="Download"
                                       href="<?php echo base_url($ProjectFilePath); ?>" download
                                       class="btn btn-orange btn-icon"><i class="fa fa-download"></i></a>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    <div id="exist-files-wrapper" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Choose File</h4>
                </div>
                <div class="modal-body">
                    <div class="dt-loader" style="display:none"></div>
                    <div id="elfinder_existing"></div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="file-path"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-orange add-from-exist"
                            data-loading-text="<?php echo _l('wait_text'); ?>">Add
                    </button
                </div>
            </div>

        </div>
    </div>
    </div>
    <?php init_tail(); ?>
    <script type="text/javascript" charset="utf-8">

        if ($('#project-files-upload').length > 0) {
            if (typeof(leadFilesUpload) != 'undefined') {
                leadFilesUpload.destroy();
            }
            leadFilesUpload = new Dropzone('#project-files-upload', {
                paramName: "file",
                dictFileTooBig: appLang.file_exceeds_maxfile_size_in_form,
                dictDefaultMessage: appLang.drop_files_here_to_upload,
                dictFallbackMessage: appLang.browser_not_support_drag_and_drop,
                dictRemoveFile: appLang.remove_file,
                dictCancelUpload: appLang.cancel_upload,
                acceptedFiles: app_allowed_files,
                maxFilesize: (max_php_ini_upload_size_bytes / (1024 * 1024)).toFixed(0),
                accept: function (file, done) {
                    done();
                },
                success: function (file, response) {
                    if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                        alert_float('success', "File(s) uploaded successfully");
                        window.location.reload();
                    }
                },
                error: function (file, response) {
                    alert_float('danger', response);
                },
                sending: function (file, xhr, formData) {
                    console.log(formData);

                }
            });
        }
        $(function () {
            $(".add-from-exist").on("click", function () {
                $(".dt-loader").show();
                $("#elfinder_existing").addClass("uploader-disabled");
                var file_path = $("#file-path").val();
                $.post(admin_url + 'projects/upload_exist_file_from_event', {
                    file_path: file_path,
                    projectid: "<?php echo $eid; ?>"
                }).done(function (data) {
                    if (data == "success") {
                        $(".dt-loader").hide();
                        $("#elfinder_existing").removeClass("uploader-disabled");
                        $('.modal-footer .btn-default').click();
                        window.location.reload();
                        alert_float('success', "File(s) added successfully.");
                    } else {
                        $(".dt-loader").hide();
                        $("#elfinder_existing").removeClass("uploader-disabled");
                        $('.modal-footer .btn-default').click();
                        window.location.reload();
                        alert_float('error', data);
                    }

                });
            });

            $(".open-exist").click(function () {
                $('#elfinder_existing').elfinder({
                    url: admin_url + 'files/elfinder_init',
                    lang: '<?php echo get_media_locale($locale); ?>',
                    height: 400,
                    uiOptions: {
                        toolbar: []
                    },
                    handlers: {
                        select: function (event, elfinderInstance) {
                            //console.log(event.data);
                            var selected = event.data.selected;
                            if (selected.length > 0) {
                                var filepath = elfinderInstance.path(selected[0]);
                                $("#file-path").val(filepath);
                            }

                        }
                    }
                });
                $('#exist-files-wrapper').modal('show');
                $('.ui-widget-header').hide();
            });
            $(".add-files").on("click", function () {
                $(".file-upload-wrapper").slideDown("slow");
            });

            $(".close-files").on("click", function () {
                $(".file-upload-wrapper").slideUp("slow");
            });

        });
    </script>
    </body>
    </html>

<?php } else { ?>
    <div class="col-md-12">
        <div class="panel_s btmbrd">
            <div class="panel-body">
                <h3 class="page-title">Files</h3>
                <div id="elfinder"></div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    </div>
    <?php init_tail(); ?>
    <script type="text/javascript" charset="utf-8">
        $(function () {
            $('#elfinder').elfinder({
                url: admin_url + 'files/elfinder_init',
                /**
                 * Modified By : Vaidehi
                 * Dt : 11/17/2017
                 * for showing limited options on folder and files
                 */
                commands: [
                    'custom', 'open', 'reload', 'home', 'up', 'back', 'forward', 'getfile', 'quicklook',
                    'download', 'rm', 'duplicate', 'rename', 'mkdir', 'mkfile', 'upload', 'copy',
                    'cut', 'paste', 'edit', 'extract', 'archive', 'search', 'info', 'view', 'help', 'resize', 'sort', 'netmount'
                ],
                contextmenu: {
                    // navbarfolder menu
                    navbar: ['open', 'download', '|', 'upload', 'mkdir', '|', 'rm', '|', 'rename'],
                    // current directory menu
                    cwd: ['reload', 'back', '|', 'upload', 'mkdir', '|', 'view', 'sort'],
                    // current directory file menu
                    files: ['open', 'download', '|', 'mkdir', '|', 'rm', '|', 'edit', 'rename']
                },
                lang: '<?php echo get_media_locale($locale); ?>',
                height: 700,
                uiOptions: {
                    // toolbar configuration
                    toolbar: [
                        ['back', 'forward'],
                        ['mkdir', 'upload'],
                        ['download',],
                        ['rm'],
                        ['rename'],
                        ['search'],
                        ['view']
                    ]
                }
            });
        });
    </script>
    </body>
    </html>
<?php } ?>