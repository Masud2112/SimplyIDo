<?php init_head();
$brandid = get_user_session();
?>
<div id="wrapper">
    <div class="content messages-view">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb">
                    <?php /*if (isset($pg) && $pg == 'home') { */?>
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php /*} */?>
                    <?php if (isset($lid)) { ?>
                        <a href="<?php echo admin_url('leads/'); ?>">Leads</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('leads/dashboard/' . $lid); ?>"><?php echo ($lname); ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('Messages?lid='.$lid); ?>">Messages</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } elseif (isset($pid)) { ?>
                        <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('projects/dashboard/' . $pid); ?>"><?php echo ($lname); ?></a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <a href="<?php echo admin_url('Messages?pid='.$pid); ?>">Messages</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>

                    <?php }else{ ?>
                        <a href="<?php echo admin_url('Messages/'); ?>">Messages</a>
                        <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php } ?>
                    <span><?php echo !empty($messages->subject) ? $messages->subject:"New Message" ?> </span>
                </div>
                <h1 class="pageTitleH1"><i class="fa fa-envelope-o"></i>Message</h1>

                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="">
                            <div class="msgTitle_blk">
                                <h4><?php echo !empty($messages->subject) ? $messages->subject : "--"; ?></h4>

                                <?php
                                if(!empty($messages->privacy)){
                                    $privacy = implode(", ", $messages->privacy)
                                    ?>
                                    <div class="privacy-wrapper"><i class="fa fa-lock mright5"></i> Only visible to <?php echo $privacy ?></div>
                                <?php } ?>
                            </div>
                            <div class="mail-single-content">
                                <header class="medias">
                                    <div class="medias-body">
                                        <div class="message-from col-md-9 col-xs-7">
                                            <a href="#" class="thumb-xs">
                                                <?php if($messages->created_by_type == "teammember"){ ?>
                                                    <?php echo staff_profile_image($messages->created_by, array(
                                                        'rounded-circle-img'
                                                    ));
                                                    ?>
                                                <?php }else{ ?>
                                                    <?php echo addressbook_profile_image($messages->created_by, array(
                                                        'rounded-circle-img'
                                                    ));
                                                    ?>
                                                <?php } ?>
                                            </a>
                                            <a href="javascript:void(0)"><?php echo $messages->created_by_name; ?></a>
                                            <?php if(!empty($messages->users)){ ?> <div class="message-to">to <strong><?php echo implode(", ", $messages->users); ?></strong></div><?php } ?>
                                        </div>
                                        <div class="text-right col-md-3 col-xs-5">
                                            <b><?php echo time_ago($messages->created_date); ?></b>
                                            <?php if($messages->created_by == get_staff_user_id() && has_permission('messages','','edit') && $messages->brandid == $brandid){ ?>
                                            <div class='text-right inline-block mleft10 mright10'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>
                                                    <?php if($messages->created_by == get_staff_user_id() && has_permission('messages','','edit') && $messages->brandid == $brandid){ ?>
                                                        <li><a data-toggle="tooltip" title="Edit" href="javascript:void(0)" onclick="edit_message(<?php echo $messages->id; ?>); return false;" class="btn-xs btn-icon mright5 mleft5"><i class="fa fa-pencil-square-o"></i>Edit</a></li>
                                                    <?php } ?>
                                                    <?php if(empty($messages->child_message)){ ?>
                                                        <?php if($messages->created_by == get_staff_user_id() && has_permission('messages','','delete') && $messages->brandid == $brandid){ ?>
                                                            <li><a data-toggle="tooltip" title="Delete" href="<?php echo admin_url('messages/delete/'.$messages->id); ?>" class="btn-icon parent_message_delete btn-xs btn-icon "><i class="fa fa-remove"></i>Delete</a></li>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <!-- /.float-right -->
                                    </div>
                                    <!-- /.media-body -->
                                </header>
                                <!-- /.media -->
                                <div class="mail-single-message">
                                    <div class="message-content msg-cnt-<?php echo $messages->id; ?>">
                                        <?php echo $messages->content; ?>
                                    </div>
                                    <?php echo '<div data-edit-message="'.$messages->id.'" class="hide edit-message mright10"><textarea rows="3" class="form-control" id="message_'.$messages->id.'">'.$messages->content.'</textarea>
                                            <div class="clearfix mtop20"></div>
                                            <button type="button" class="btn btn-info pull-right" onclick="save_edited_message('.$messages->id.');return false;" data-loading-text="'. _l('wait_text').'">'._l('submit').'</button>
                                            <button type="button" class="btn btn-default pull-right mright5" onclick="cancel_edit_message('.$messages->id.')">'._l('cancel').'</button><div class="clearfix "></div>
                                         </div>';
                                    ?>

                                    <?php if(!empty($messages->attachments)){ ?>
                                        <div class="mail-attachment mtop20">
                                            <?php $attach_count = count($messages->attachments); ?>
                                            <h5 class="mail-attachment-heading"><i class="fa fa-paperclip mright10" aria-hidden="true"></i>Attachments <span class="text-muted">(<?php echo $attach_count; ?> Files)</span></h5>
                                            <div class="list-unstyled file-list">
                                                <?php foreach ($messages->attachments as $attachment) { ?>
                                                    <?php
                                                    $MessageFilePath = 'uploads/messages/' . $messages->id . '/' . $attachment;
                                                    ?>
                                                    <div class="file-list-item">
                                                        <a href="<?php echo base_url($MessageFilePath); ?>" target="_blank" class="fw-500 color-content mright10" ><?php echo $attachment;; ?></a>  <span class="text-muted">(<?php echo format_size(filesize(MESSAGE_ATTACHMENTS_FOLDER .$messages->id.'/'.$attachment)); ?>)</span>
                                                        <div class="spacer"></div>
                                                        <div class="list-unstyled list-inline"><a href="<?php echo base_url($MessageFilePath); ?>" target="_blank" class="list-inline-item mright10  btn btn-xs btn-success " >Download</a>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <!-- /.file-item -->
                                            </div>
                                            <!-- /.list-unstyled -->
                                        </div>
                                    <?php } ?>
                                    <!-- /.mail-attachment -->
                                </div>
                                <!-- /.mail-single-message -->
                            </div>
                            <hr>
                            <?php if(!empty($messages->child_message)){ ?>
                                <?php foreach ($messages->child_message as $childmsg) { ?>
                                    <div class="mail-single-content">
                                        <header class="medias">
                                            <div class="medias-body">
                                                <div class="message-from col-md-9">
                                                    <a href="#" class="thumb-xs">
                                                        <?php if($childmsg['created_by_type'] == "teammember"){ ?>
                                                            <?php echo staff_profile_image($childmsg['created_by'], array(
                                                                'rounded-circle'
                                                            ));
                                                            ?>
                                                        <?php }else{ ?>
                                                            <?php echo addressbook_profile_image($childmsg['created_by'], array(
                                                                'rounded-circle'
                                                            ));
                                                            ?>
                                                        <?php } ?>

                                                    </a>
                                                    <a href="javascript:void(0)"><?php echo $childmsg['created_by_name']; ?></a>
                                                    <?php if(!empty($childmsg['users'])){ ?><div class="message-to"> to <strong><?php echo implode(", ", $childmsg['users']); ?></strong></div><?php } ?>
                                                </div>
                                                <div class="message-date col-md-3">
                                                    <b><?php echo time_ago($childmsg['created_date']); ?></b>
                                    <?php if($childmsg['created_by'] == get_staff_user_id() && has_permission('messages','','edit') && $childmsg['brandid'] == $brandid){ ?>
                                                    <div class='text-right inline-block mleft10 mright10'><a class='show_act' href='javascript:void(0)'><i class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div><div class='table_actions'><ul>
                                                            <?php if($childmsg['created_by'] == get_staff_user_id() && has_permission('messages','','edit') && $childmsg['brandid'] == $brandid){ ?>
                                                                <li><a data-toggle="tooltip" title="Edit" href="javascript:void(0)" onclick="edit_message(<?php echo $childmsg['id']; ?>); return false;" class="btn-xs btn-icon mleft10 mright5"><i class="fa fa-pencil-square-o"></i>Edit</a></li>
                                                            <?php } ?>
                                                            <?php if($childmsg['created_by'] == get_staff_user_id() && has_permission('messages','','delete') && $childmsg['brandid'] == $brandid){ ?>
                                                                <li><a data-toggle="tooltip" title="Delete" href="<?php echo admin_url('messages/delete/'.$childmsg['id']); ?>" class="btn-xs btn-icon _delete "><i class="fa fa-remove"></i>Delete</a></li>
                                                            <?php } ?>
                                                        </ul></div>
                                    <?php } ?>
                                                </div>
                                                <!-- /.float-right -->
                                            </div>
                                            <!-- /.media-body -->
                                        </header>
                                        <!-- /.media -->
                                        <div class="mail-single-message">
                                            <div class="message-content msg-cnt-<?php echo $childmsg['id']; ?>">
                                                <?php echo $childmsg['content']; ?>
                                            </div>
                                            <?php echo '<div data-edit-message="'.$childmsg['id'].'" class="hide edit-message mright10"><textarea rows="3" class="form-control" id="message_'.$childmsg['id'].'">'.$childmsg['content'].'</textarea>
                                                <div class="clearfix mtop20"></div>
                                                <button type="button" class="btn btn-info pull-right" onclick="save_edited_message('.$childmsg['id'].');return false;" data-loading-text="'. _l('wait_text').'">'._l('submit').'</button>
                                                <button type="button" class="btn btn-default pull-right mright5" onclick="cancel_edit_message('.$childmsg['id'].')">'._l('cancel').'</button>
                                                <div class="clearfix "></div>
                                             </div>';
                                            ?>

                                            <?php if(!empty($childmsg['attachments'])){ ?>
                                                <div class="mail-attachment mtop20">
                                                    <?php $attach_count = count($childmsg['attachments']); ?>
                                                    <h5 class="mail-attachment-heading"><i class="fa fa-paperclip mright10" aria-hidden="true"></i>Attachments <span class="text-muted">(<?php echo $attach_count; ?> Files)</span></h5>
                                                    <div class="list-unstyled file-list">
                                                        <?php foreach ($childmsg['attachments'] as $attachment) { ?>
                                                            <?php
                                                            $MessageFilePath = 'uploads/messages/' . $childmsg['id'] . '/' . $attachment;
                                                            ?>
                                                            <div class="file-list-item">
                                                                <a href="<?php echo base_url($MessageFilePath); ?>" target="_blank" class="fw-500 color-content mright10" ><?php echo $attachment;; ?></a>  <span class="text-muted">(<?php echo format_size(filesize(MESSAGE_ATTACHMENTS_FOLDER .$childmsg['id'].'/'.$attachment)); ?>)</span>
                                                                <div class="spacer"></div>
                                                                <div class="list-unstyled list-inline"><a target="_blank" href="<?php echo base_url($MessageFilePath); ?>" class="list-inline-item mright10  btn btn-success btn-xs" >Download</a>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                        <!-- /.file-item -->
                                                    </div>
                                                    <!-- /.list-unstyled -->
                                                </div>
                                            <?php } ?>
                                            <!-- /.mail-attachment -->
                                        </div>
                                        <!-- /.mail-single-message -->
                                    </div>
                                    <hr class="line-d">
                                <?php } ?>
                            <?php } ?>
                            <div class="mail-single-reply mr-4 ml-4 clearfix">
                                <div class="triangle-top"></div><span class="float-left">Click here to <a href="javascript:void(0)" class="replylink">Reply</a></span>
                            </div>
                            <div class="reply-wrapper" style="display:none">
                                <?php echo form_open_multipart('admin/messages/replymessage/'.$messages->id,array('id'=>'replymessage')); ?>
                                <h6>Reply Message</h6>
                                <div class="row mtop20">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="content" class="control-label">Content <small class="req text-danger">* </small></label>
                                            <textarea id="content" name="content" class="form-control message" rows="4" aria-hidden="true"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="message_to" class="control-label">Message To <small class="req text-danger">* </small></label>
                                            <select id="message_to" class="selectpicker" name="message_to[]" data-width="100%" data-none-selected-text="Select Users" multiple data-live-search="true" autofocus="">
                                                <optgroup label="Team Member">
                                                    <?php foreach ($teammember as $t) {
                                                        $tselected = "";
                                                        if(in_array("tm_".$t['staffid'], $messages->prefixuser)){
                                                            $tselected = "selected='selected'";
                                                        }
                                                        ?>
                                                        <option value="tm_<?php echo $t['staffid'] ?>" <?php echo $tselected; ?>><?php echo $t['staff_name'] ?></option>
                                                    <?php } ?>
                                                </optgroup>
                                                <optgroup label="Contacts">
                                                    <?php foreach ($contacts as $c) {
                                                        $cselected = "";
                                                        if(in_array("cn_".$c['addressbookid'], $messages->prefixuser)){
                                                            $cselected = "selected='selected'";
                                                        }
                                                        ?>
                                                        <option value="cn_<?php echo $c['addressbookid'] ?>" <?php echo $cselected; ?>><?php echo $c['contact_name'] ?></option>
                                                    <?php } ?>
                                                </optgroup>
                                            </select>

                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <label><?php echo _l('attach_files'); ?> <i class="fa fa-question-circle" data-toggle="tooltip" data-title="Allowed extensions - <?php echo str_replace('.','',get_option('allowed_files')); ?>"></i></label>

                                        <div id="new-message-attachments">
                                            <div class="attachments">
                                                <div class="attachment row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="input-group" id="attachments[0]">
                                                            <span class="input-group-btn">
                                                              <span class="btn btn-primary" onclick="$(this).parent().find('input[type=file]').click();">Browse</span>
                                                              <input name="attachments[0]" onchange="$(this).parent().parent().find('.form-control').html($(this).val().split(/[\\|/]/).pop());" style="display: none;" filesize="<?php echo file_upload_max_size(); ?>" extension="<?php echo str_replace('.','',get_option('allowed_files')); ?>"  type="file">
                                                            </span>
                                                                <span class="form-control"></span>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="text-right">
                                                            <button class="btn btn-primary add_more_attachments" type="button"><i class="fa fa-plus"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pull-right mtop20">
                                    <?php if(isset($lid)){ ?>
                                        <input type="hidden" name="rel_type" value="lead">
                                        <input type="hidden" name="rel_id" value="<?php echo $lid;?>">
                                    <?php }else if(isset($pid)){ ?>
                                        <input type="hidden" name="rel_type" value="project">
                                        <input type="hidden" name="rel_id" value="<?php echo $pid;?>">
                                    <?php }else if(isset($eid)){ ?>
                                        <input type="hidden" name="rel_type" value="event">
                                        <input type="hidden" name="rel_id" value="<?php echo $eid;?>">
                                    <?php } ?>
                                    <button class="btn btn-default cancelreply" type="button" onclick="javascript:void(0)"><?php echo _l( 'Cancel'); ?></button>
                                    <button type="submit" class="btn btn-info">Send</button>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>


    $(function() {
        $(".replylink").on("click",function(){
            $(".mail-single-reply").hide();
            $(".reply-wrapper").show();
        });
        $(".cancelreply").on("click",function(){
            $(".mail-single-reply").show();
            $(".reply-wrapper").hide();
        });

        init_editor('.message');
        var validator = $('#replymessage').submit(function() {
            // update underlying textarea before submit validation
            var content = tinyMCE.activeEditor.getContent();
            $("#content").val(content);
            tinyMCE.triggerSave();
            if($("#content").val() == ""){
                $(".mce-tinymce").css({'border-color': '#fc2d42'});
            } else {
                $(".mce-tinymce").css({'border-color': ''});
            }
        }).validate({
            ignore: "",
            rules: {
                content: "required",
                'message_to[]':'required'
            }
        });
    });
    //_validate_form($('form'),{subject:'required',content:'required', 'message_to[]':'required'});
</script>
</body>
</html>