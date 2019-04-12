<?php
/**
 * Added By : Vaidehi
 * Dt : 11/13/2017
 * Notes
 */
init_head();
?>
<div id="wrapper">
    <div class="content  leads-page">
        <div class="row">
            <div class="col-md-12">


                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('leads/'); ?>">Leads</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('leads/dashboard/' . $leadid); ?>">
                        <?php echo($lname); ?>
                    </a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span>Notes</span>
                </div>
                <h1 class="pageTitleH1"><i class="fa fa-newspaper-o"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (count($notes) > 0) { ?>
                                <a href="javascript:void(0);" class="btn btn-info pull-left add-notes">
                                    <?php echo _l('lead_add_edit_add_note'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading"/>
                        <?php if (count($notes) > 0) {
                            echo form_open(admin_url('leads/notes/' . $leadid), array('id' => 'lead-notes', 'style' => 'display : none;'));
                        } else {
                            echo form_open(admin_url('leads/notes/' . $leadid), array('id' => 'lead-notes'));
                        } ?>
                        <?php echo render_textarea('description', '', '', array('autofocus' => true)); ?>
                        <button type="submit"
                                class="pull-right btn btn-info"><?php echo _l('save'); ?></button>
                        <?php if (count($notes) > 0) { ?>
                            <button class="pull-right btn btn-default close-notes"
                                    type="button"><?php echo _l('Cancel'); ?></button>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading"/>
                        <?php echo form_close(); ?>
                        <div class="panel_s mtop20">
                            <?php
                            $len = count($notes);
                            $i = 0;
                            if ($len > 0) {
                            foreach ($notes as $note) {
                                ?>
                                <div class="media lead-note row">
                                    <!-- <div class="date">
                              <?php echo time_ago($note['dateadded']); ?>
                           </div> -->
                                    <div class="col-md-9 col-xs-9 noteLising">
                                        <a href="<?php echo admin_url('profile/' . $note["addedfrom"]); ?>"
                                           target="_blank">
                                            <?php echo staff_profile_image($note['addedfrom'], array('staff-profile-image-small', 'pull-left mright10')); ?>
                                        </a>
                                        <div class="media-body">

                                            <div class="text">
                                                <a href="javascript:void(0);">
                                                    <?php echo get_staff_full_name($note['addedfrom']); ?>
                                                </a>
                                            </div>
                                            <div data-note-description="<?php echo $note['id']; ?>"
                                                 class="text-muted">
                                                <?php echo $note['description']; ?>
                                            </div>
                                            <div data-note-edit-textarea="<?php echo $note['id']; ?>"
                                                 class="hide mtop15">
                                                <?php echo render_textarea('note', '', $note['description']); ?>
                                                <div class="text-right ">
                                                    <button type="button" class="btn btn-default"
                                                            onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                                                    <button type="button" class="btn btn-info"
                                                            onclick="edit_note(<?php echo $note['id']; ?>, <?php echo $leadid; ?>, 'lead');"><?php echo _l('update_note'); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 col-xs-3 noteTime">
                                        <?php if ($note['addedfrom'] == get_staff_user_id() || is_admin()) { ?>
                                            <div style="float: right;">

                                                <b><?php echo time_ago($note['dateadded']); ?></b>

                                                <div class='text-right mright10'>
                                                    <a class='show_act' href='javascript:void(0)'>
                                                        <i class='fa fa-ellipsis-v' aria-hidden='true'></i>
                                                    </a>
                                                </div>
                                                <div class='table_actions'>
                                                    <ul>
                                                        <li><a href="#"
                                                               class=""
                                                               onclick="toggle_edit_note(<?php echo $note['id']; ?>); return false;">
                                                                <i class="fa fa-pencil-square-o"></i>Edit
                                                            </a>
                                                        </li>
                                                        <li><a href="#"
                                                               class=""
                                                               onclick="delete_lead_note(this,<?php echo $note['id']; ?>, <?php echo $leadid; ?>);return false;">
                                                                <i class="fa fa fa-times"></i>Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php
                                if ($i >= 0 && $i != $len - 1) {
                                    echo '<hr />';
                                }
                            }
                            ?>
                        </div>
                    <?php $i++;
                    } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
    $(".add-notes").on("click", function () {
        $(this).hide();
        $("#lead-notes").slideDown("slow");
    });

    $(".close-notes").on("click", function () {
        $(".add-notes").show();
        $("#lead-notes").slideUp("slow");
    });
    _validate_form($('#lead-notes'), {description: 'required'});
</script>
</body>
</html>