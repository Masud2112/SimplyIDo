<?php
/**
 * Added By : Vaidehi
 * Dt : 11/13/2017
 * Notes
 */
init_head();
if (isset($eid)) {
	$pid = $eid;
}
?>

<div id="wrapper">
    <div class="content  projects-page">
        <div class="row">
            <div class="col-md-12">
                <h1 class="pageTitleH1"><i class="fa fa-sticky-note-o"></i><?php echo $title; ?></h1>
                <div class="pull-right">
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
                        <?php } elseif (isset($pid)) { ?>
                            <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('projects/dashboard/' . $pid); ?>"><?php echo ($lname); ?></a>
							 <?php if(isset($parent_id) &&  $parent_id > 0) {?>							
							<i class="fa fa-angle-right breadcrumb-arrow"></i>
							 <a href="<?php echo admin_url('projects/dashboard/').$parent_id; ?>"><?php echo get_project_name_by_id($parent_id); ?></a>							 				<?php } ?>
						
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
						
                        <?php }else{ ?>
                        <?php } ?>
                        <span>Notes</span>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (count($notes) > 0) { ?>
                            <a href="javascript:void(0);" class="btn btn-info pull-left add-notes">
                                <?php echo _l('project_add_edit_add_note'); ?>
                            </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <hr class="hr-panel-heading"/>
                        <?php if (count($notes) > 0) {
                            echo form_open(admin_url('projects/notes/' . $projectid), array('id' => 'project-notes', 'style' => 'display : none;'));
                        } else {
                            echo form_open(admin_url('projects/notes/' . $projectid), array('id' => 'project-notes'));
                        } ?>
                        <?php echo render_textarea('description', 'notes', '', array('autofocus' => true)); ?>
                        <?php
                        $rel_type = '';
                        $rel_id = '';
                        if (isset($pid)) {
                            $rel_id = $pid;
                            $rel_type = 'project';
                        } elseif (isset($eid)) {
                            $rel_id = $eid;
                            $rel_type = 'event';
                        }
                        ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="rel_type"
                                           class="control-label"><?php echo _l('task_related_to'); ?></label>
                                    <select name="rel_type" class="selectpicker" id="rel_type" data-width="100%"
                                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value=""></option>
                                        <?php if (isset($pid) || (!isset($eid))) { ?>
                                            <option value="project" <?php if (isset($meeting) || isset($pid) || $this->input->get('rel_type')) {
                                                if ($rel_type == 'project') {
                                                    echo 'selected';
                                                }
                                            } ?>>
                                                <?php echo _l('project'); ?>
                                            </option>
                                        <?php } ?>
                                        <?php if ((isset($pid) || isset($eid))) { ?>
                                            <option value="event" <?php if (isset($meeting) || isset($eid) || $this->input->get('rel_type')) {
                                                if ($rel_type == 'event') {
                                                    echo 'selected';
                                                }
                                            } ?>>
                                                Sub-Projects
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php if (isset($pid) || (!isset($eid))) { ?>
                                <div class="col-md-6 project-search <?php echo $rel_type == "project" ? "" : "hide"; ?>">
                                    <?php $selectedprojects = array();
                                    $selectedprojects = $rel_id != "" ? $rel_id : "";
                                    echo render_select('project', $projects, array('id', 'name'), 'Projects', $selectedprojects, array(), array(), '', '', false);
                                    ?>
                                </div>
                            <?php } ?>
                            <?php if ((isset($pid) || isset($eid))) { ?>
                                <div class="col-md-6 event-search <?php echo $rel_type == "event" ? "" : "hide"; ?>">
                                    <?php $selectedevents = array();
                                    $selectedevents = $rel_id != "" ? $rel_id : "";
                                    echo render_select('event', $events, array('id', 'name'), 'Sub-Projects', $selectedevents, array(), array(), '', '', false);
                                    ?>
                                </div>
                            <?php } ?>
                        </div>
                        <input type="hidden" name="hdnpid" value="<?php echo isset($pid) ? $pid : ''; ?>">
                        <input type="hidden" name="hdneid" value="<?php echo isset($eid) ? $eid : ''; ?>">
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
                                <div class="project-note media lead-note row">
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
                                            <div data-note-description="<?php echo $note['id']; ?>" class="text-muted">
                                                <?php echo $note['description']; ?>
                                            </div>
                                            <div data-note-edit-textarea="<?php echo $note['id']; ?>"
                                                 class="hide mtop15">
                                                <?php echo render_textarea('note', '', $note['description']); ?>
                                                <div class="text-right ">
                                                    <button type="button" class="btn btn-default"
                                                            onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                                                    <button type="button" class="btn btn-info"
                                                            onclick="edit_note(<?php echo $note['id']; ?>, <?php echo $projectid; ?>, 'project');"><?php echo _l('update_note'); ?></button>
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
                                                        <li><a href="#" class=""
                                                               onclick="delete_project_note(this,<?php echo $note['id']; ?>, <?php echo $projectid; ?>);return false;">
                                                                <i class="fa fa fa-times"></i>Delete
                                                            </a>
                                                        </li>
                                                        <li><a href="#" class=""
                                                               onclick="toggle_edit_note(<?php echo $note['id']; ?>); return false;">
                                                                <i class="fa fa-pencil-square-o"></i>Edit
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
        $("#project-notes").slideDown("slow");
    });

    $(".close-notes").on("click", function () {
        $(".add-notes").show();
        $("#project-notes").slideUp("slow");
    });
    $(function () {
        _validate_form($('#project-notes'), {description: 'required'});
        $("#rel_type").on('change', function () {
            var selected = $(this).val();
            if (selected == "project") {
                $(".project-search").removeClass("hide");
                $(".event-search").addClass("hide");
            } else if (selected == "event") {
                $(".event-search").removeClass("hide");
                $(".project-search").addClass("hide");
            }
        });
    });


</script>
</body>
</html>