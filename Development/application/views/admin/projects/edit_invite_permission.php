<?php
/**
 * Added By : Vaidehi
 * Dt : 01/09/2018
 * Invite Detail Screen
 */
init_head();
?>
<div id="wrapper">
    <div class="content edit-invite-page">
        <div class="row">
            <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'invite-form', 'autocomplete' => 'off')); ?>
            <input type="hidden" name="contacttype" id="contacttype" value="<?php echo $contacttype; ?>">
            <input type="hidden" name="inviteid" id="inviteid" value="<?php echo $invite_details->invite; ?>">
            <input type="hidden" name="projectid" id="projectid" value="<?php echo $invite_details->pid; ?>">
            <div class="col-md-12">

                <div class="breadcrumb">
                    <?php /*if (isset($pg) && $pg == 'home') { */ ?>
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php /*} */ ?>
                    <a href="<?php echo admin_url('projects'); ?>">Projects</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('projects/dashboard/' . $invite_details->pid); ?>"><?php echo($project->name); ?></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php if ($contacttype == 3) { ?>
                        <span>Invite Vendor</span>
                    <?php } ?>
                    <?php if ($contacttype == 4) { ?>
                        <span>Invite Collaborator</span>
                    <?php } ?>
                    <?php if ($contacttype == 5) { ?>
                        <span>Invite Venue</span>
                    <?php } ?>
                </div>
                <h1 class="pageTitleH1"><i class="fa fa-book"></i><?php echo "EDIT INVITE PERMISSION"; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="topButton">
                            <?php if ($project->parent === 0 || empty($project->parent)) { ?>
                                <button class="btn btn-default" type="button"
                                        onclick="location.href='<?php echo admin_url('projects/dashboard/' . $project->id); ?>'"><?php echo _l('Cancel'); ?></button>
                                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                            <?php } else { ?>
                                <button class="btn btn-default" type="button"
                                        onclick="location.href='<?php echo admin_url('projects/dashboard/' . $project->parent); ?>'"><?php echo _l('Cancel'); ?></button>
                                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                            <?php } ?>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h5 class="sub-title">
                                            <?php if ($contacttype != 5) { ?>
                                                <?php echo _l('contact'); ?>
                                            <?php } else { ?>
                                                <?php echo _l('venue'); ?>
                                            <?php } ?>
                                        </h5>
                                        <hr class="hr-panel-heading"/>
                                        <?php $name = explode(" ", $invite_details->assigned_name); ?>
                                        <?php if ($contacttype != 5) { ?>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="firstname" class="control-label">First Name</label>
                                                        <input type="firstname" id="firstname" name="firstname"
                                                               class="form-control" readonly="true"
                                                               value="<?php echo $name[0]; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="lastname" class="control-label">Last Name</label>
                                                    <input type="firstname" id="lastname" name="lastname"
                                                           class="form-control" readonly="true"
                                                           value="<?php echo $name[1]; ?>">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group" id="email-error">
                                                        <label for="email" class="control-label">Email</label>
                                                        <input type="email" id="email" name="email" class="form-control"
                                                               autocomplete="off" readonly="true"
                                                               value="<?php echo $invite_details->assigned_email; ?>">
                                                        <span id="emailmsg"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 multiphone">
                                                    <label for="phone" class="control-label">Phone</label>
                                                    <input type="number" id="emphoneail" name="phone"
                                                           class="form-control" readonly="true"
                                                           value="<?php echo $invite_details->assigned_phone; ?>">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="tags" class="control-label">Tags
                                                            <small class="req text-danger">*</small>
                                                        </label>
                                                        <select name="tags[]" id="tags[]"
                                                                class="form-control selectpicker"
                                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                                                data-live-search="true" multiple disabled="true">
                                                            <?php
                                                            if (count($invite_details->assigned_tags) > 0) {
                                                                $assigned_tags = explode(",", $invite_details->assigned_tags);
                                                                foreach ($tags as $tag) {
                                                                    $tselected = '';
                                                                    if (in_array($tag['name'], $assigned_tags)) {
                                                                        $tselected = "selected='selected'";
                                                                    }
                                                                    echo '<option value="' . $tag['id'] . '" ' . $tselected . '>' . $tag['name'] . '</option>';
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="venuename" class="control-label">Venue Name</label>
                                                        <input type="venuename" id="venuename" name="venuename"
                                                               class="form-control" readonly="true"
                                                               value="<?php echo $invite_details->assigned_name; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="venueemail" class="control-label">Venue
                                                            Email</label>
                                                        <input type="venueemail" id="venueemail" name="venueemail"
                                                               class="form-control" readonly="true"
                                                               value="<?php echo(isset($invite_details->assigned_email) ? $invite_details->assigned_email : ''); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="venuephone" class="control-label">Venue
                                                            Phone</label>
                                                        <input type="venuephone" id="venuephone" name="venuephone"
                                                               class="form-control" readonly="true"
                                                               value="<?php echo(isset($invite_details->assigned_phone) ? $invite_details->assigned_phone : ''); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h5 class="sub-title">
                                            <?php echo _l('assign_permission'); ?>
                                        </h5>
                                        <hr class="hr-panel-heading"/>
                                        <div class="row">
                                            <?php
                                            $remove[] = '"';
                                            $remove[] = " "; // just as another example
                                            $permission_array = explode(",", $invite_details->permission_name);
                                            ?>
                                            <div id="field-0" class="row mbot20">
                                                <div class="col-md-4">
                                                    <label for="events"
                                                           class="control-label"><?php echo _l('projects'); ?></label>
                                                    <div><?php echo $project->name; ?></div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div>
                                                        <label for="permission"
                                                               class="control-label"><?php echo _l('permissions'); ?></label>
                                                        <?php
                                                        foreach ($permissions as $permission) {
                                                            if (in_array(str_replace($remove, "", $permission['name']), $permission_array)) {
                                                                $tselected = "checked='checked'";
                                                            } else {
                                                                $tselected = "";
                                                            }
                                                            ?>
                                                            <div class="editinvite-check"><input type="checkbox"
                                                                                                 name="permissions[]" <?php echo $tselected; ?>
                                                                                                 value="<?php echo $permission['permissionid']; ?>"/> <?php echo str_replace($remove, "", $permission['name']); ?>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
    $('#view_invites').modal('hide');
</script>
</body>
</html>