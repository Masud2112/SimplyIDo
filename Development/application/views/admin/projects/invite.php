<?php
/**
 * Added By : Vaidehi
 * Dt : 01/01/2018
 * Add Invite Form
 */
init_head();
?>
<div id="wrapper">
    <div class="content invite-page">


        <div class="breadcrumb">
            <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <a href="<?php echo admin_url('projects/'); ?>"><?php echo _l('projects'); ?></a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <a href="<?php echo admin_url('projects/dashboard/' . $project->id); ?>"><?php echo($project->name); ?></a>
            <i class="fa fa-angle-right breadcrumb-arrow"></i>
            <?php if ($contacttype == 3) { ?>
                <span class="breadcrumb-item active">Invite Vendor</span>
            <?php } ?>
            <?php if ($contacttype == 4) { ?>
                <span class="breadcrumb-item active">Invite Collaborator</span>
            <?php } ?>
            <?php if ($contacttype == 5) { ?>
                <span class="breadcrumb-item active">Invite Venue</span>
            <?php } ?>
        </div>
        <h1 class="pageTitleH1"> <?php echo $title; ?> </h1>
        <div class="clearfix"></div>
        <div class="row">
            <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'invite-form', 'autocomplete' => 'off')); ?>
            <input type="hidden" name="contacttype" id="contacttype" value="<?php echo $contacttype; ?>">
            <input type="hidden" name="parent" id="parent" value="<?php echo $project->parent; ?>">
            <input type="hidden" name="project" id="project" value="<?php echo $project->id; ?>">
            <div class="col-md-12">
                <div class="panel_s btmbrd">
                    <div class="panel-body ">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel_s">
                                    <div class="panel-body formdata">
                                        <h5 class="sub-title">
                                            <?php if ($contacttype == 5) {
                                                echo _l('choose_venue');
                                            } else {
                                                echo _l('choose_contact');
                                            } ?>
                                        </h5>

                                        <?php if ($contacttype == 3 || $contacttype == 4) { ?>
                                            <div class="form-group invite-options">
                                                <div class="radio radio-primary radio-inline">
                                                    <input id="invite_new" name="invite[]" value="new" checked="true"
                                                           type="radio">
                                                    <label for="invite_new"><?php echo _l('new_invite'); ?></label>
                                                </div>
                                                <div class="radio radio-primary radio-inline">
                                                    <input id="invite_existing" name="invite[]" value="existing"
                                                           type="radio">
                                                    <label for="invite_existing"><?php echo _l('choose_existing_invite'); ?></label>
                                                </div>
                                            </div>
                                            <div id="new-invite">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="checkbox checkbox-primary" title="Company">
                                                                <input value="1" type="checkbox" name="company"
                                                                       id="company">
                                                                <label for="company">Company</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row companydetails">
                                                    <div class="col-md-6">
                                                        <?php echo render_input('companyname', 'Company Name', '', 'text'); ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php echo render_input('companytitle', 'Title', '', 'text'); ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <?php echo render_input('firstname', 'staff_add_edit_firstname', ''); ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php echo render_input('lastname', 'staff_add_edit_lastname', ''); ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group" id="email-error">
                                                            <label for="email" class="control-label">Email
                                                                <small class="req text-danger">*</small>
                                                            </label>
                                                            <input type="email" id="email" name="email"
                                                                   class="form-control" autocomplete="off" value="">
                                                            <span id="emailmsg"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6 multiphone">
                                                        <?php echo render_input('phone', 'staff_add_edit_phonenumber', ''); ?>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="tags" class="control-label">Tags
                                                                <!--<small class="req text-danger">*</small>-->
                                                            </label>
                                                            <select name="tags[]" id="tags[]"
                                                                    class="form-control selectpicker"
                                                                    data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"
                                                                    data-live-search="true" multiple>
                                                                <?php
                                                                foreach ($tags as $tag) {
                                                                    $tselected = '';
                                                                    if (in_array($tag['id'], $addressbook->tags_id)) {
                                                                        $tselected = "selected='selected'";
                                                                    }
                                                                    echo '<option value="' . $tag['id'] . '" ' . $tselected . '>' . $tag['name'] . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="existing-invite">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="events"
                                                                   class="control-label"><?php echo _l('contacts'); ?>
                                                                <small class="req text-danger">*</small>
                                                            </label>
                                                            <select name="vendor" id="vendor"
                                                                    class="form-control selectpicker"
                                                                    data-live-search="true">
                                                                <option value=""></option>
                                                                <optgroup label="Team Members">
                                                                    <?php
                                                                    foreach ($teammember as $member) {
                                                                        if ($member['staffid'] != get_staff_user_id() && !in_array($member['staffid'], $clients)) {
                                                                            if (!in_array($member['staffid'], $invitedusers['staff'])) {
                                                                                ?>
                                                                                <option value="staff-<?php echo $member['staffid']; ?>"
                                                                                        data-subtext="<?php echo $member['email']; ?>"><?php echo $member['firstname'] . " " . $member['lastname']; ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </optgroup>
                                                                <optgroup label="Contacts">
                                                                    <?php
                                                                    foreach ($contacts as $contact) {
                                                                        if (!in_array($contact['addressbookid'], $clientConatct)) {
                                                                            if (!in_array($contact['addressbookid'], $invitedusers['contact'])) {
                                                                                ?>
                                                                                <option value="contact-<?php echo $contact['addressbookid']; ?>"
                                                                                        data-subtext="<?php echo $contact['email']; ?>"><?php echo $contact['firstname'] . " " . $contact['lastname']; ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                    }
                                                                    ?>
                                                                </optgroup>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php }
                                        if ($contacttype == 5) { ?>
                                            <div class="form-group">
                                                <label for="venueid" class="control-label">
                                                    <small class="req text-danger">*
                                                    </small><?php echo _l('project_add_edit_venue'); ?></label>
                                                <select name="venueid" id="venueid" class="selectpicker"
                                                        data-width="100%" data-none-selected-text="Select"
                                                        data-live-search="true">
                                                    <option value=""></option>
                                                    <?php
                                                    foreach ($venues as $venue) {
                                                        if (!in_array($venue['venueid'], $invitedusers['venue'])) {
                                                            ?>
                                                            <option value="<?php echo $venue['venueid']; ?>"
                                                                    data-venueemail='<?php echo $venue['venueemail']; ?>' <?php if ($project->venueid == $venue['venueid']) {
                                                                echo 'selected="selected"';
                                                            } ?>><?php echo $venue['venuename']; ?>
                                                            </option>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <?php echo render_select('sitelocationid', $sitelocations, array('venue_sitecontactid', 'sitelocation_name'), 'project_add_edit_sitelocation'); ?>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel_s">
                                    <div class="panel-body formdata">
                                        <h5 class="sub-title">
                                            <?php echo _l('assign_permission'); ?>
                                        </h5>

                                        <div class="row">
                                            <div id="field-0" class="row mbot20">
                                                <div class="col-md-4">
                                                    <label for="events"
                                                           class="control-label"><?php echo _l('projects'); ?>
                                                        <small class="req text-danger">*</small>
                                                    </label>
                                                    <select name="events[0]" id="events0"
                                                            class="form-control selectpicker event-selectpicker"
                                                            data-none-selected-text="<?php echo _l('projects'); ?>"
                                                            data-live-search="true">
                                                        <option value=""></option>
                                                        <optgroup label="Projects">
                                                            <option value="<?php echo $project->id; ?>"><?php echo $project->name; ?></option>
                                                        </optgroup>
                                                        <?php
                                                        if ($project->no_of_events > 0) {
                                                            ?>
                                                            <optgroup label="Sub Projects">
                                                                <?php
                                                                foreach ($events as $event) {
                                                                    echo '<option value="' . $event['id'] . '">' . $event['name'] . '</option>';
                                                                }
                                                                ?>
                                                            </optgroup>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label for="permission"
                                                           class="control-label"><?php echo _l('permissions'); ?>
                                                        <small class="req text-danger">*</small>
                                                    </label>
                                                    <select name="permissionid[0][]" id="permissionid[0]"
                                                            class="form-control selectpicker permission-selectpicker"
                                                            data-none-selected-text="<?php echo _l('permission'); ?>"
                                                            data-live-search="true" multiple>
                                                        <?php
                                                        foreach ($permissions as $permission) {
                                                            echo '<option value="' . $permission['permissionid'] . '">' . $permission['name'] . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php
                                            if ($project->no_of_events > 0) {
                                                ?>
                                                <div class="col-md-12 text-right" style="margin-top:10px">
                                                    <button id="add-more" name="add-more" class="btn btn-primary">Add
                                                        More
                                                    </button>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="topButton">
                            <button class="btn btn-default" type="button"
                                    onclick="location.href='<?php echo admin_url('projects/dashboard/' . $project->id); ?>'"><?php echo _l('Cancel'); ?></button>
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script type="text/javascript">
    /**
     * Added By: Vaidehi
     * Dt: 02/22/2018
     * to get sitelocation on venue change
     */
    $('#venueid').change(function () {
        var vemail = $(this).find(':selected').attr('data-venueemail');
        if (vemail.length > 0) {
            $(".btn-info").removeClass('disabled');
            $('form').bind('submit');
        } else {
            alert_float('warning', 'Please add venue email address');
            $(".btn-info").addClass('disabled');
            $('form').unbind('submit');
        }

        var venue_id = $('#venueid').val();
        $.ajax({
            url: "<?php echo admin_url('projects/getsitelocations');?>",
            method: "post",
            data: "venueid=" + venue_id,
            success: function (data) {
                if (data.length > 0) {
                    var sielocation = JSON.parse(data);
                    var $el = $("#sitelocationid");
                    $el.empty(); // remove old options
                    $el.append($("<option></option>")
                        .attr("value", "").text(""));
                    $.each(sielocation, function (key, value) {
                        $el.append($("<option></option>")
                            .attr("value", value.venue_sitecontactid).text(value.sitelocation_name));
                    });
                    $('.selectpicker').selectpicker('refresh');
                } else {
                    $("#sitelocationid").empty();
                    $('.selectpicker').selectpicker('refresh');
                }
            }
        });
    });

    showcompany();

    function showcompany() {
        if ($('#company').is(":checked"))
            $(".companydetails").show();
        else
            $(".companydetails").hide();
    }

    $('#company').on('click', function () {
        showcompany();
    });

    $('#existing-invite').toggle();

    $('input:radio').change(function () {
        if ($(this).val() == 'new') {
            $('#new-invite').toggle();
            $('#existing-invite').hide();
        }

        if ($(this).val() == 'existing') {
            $('#new-invite').hide();
            $('#existing-invite').toggle();
        }
    });

    //ajax call to check whether email exists or not
    $("#email").blur(function () {
        var useremail = $(this).val();
        $.ajax({
            url: "<?php echo admin_url('projects/emailexists');?>",
            method: "post",
            data: "useremail=" + useremail,
            success: function (data) {
                if (data == 1) {
                    $("#email-error").removeClass("has-error");
                    $(':button[type="submit"]').prop('disabled', false);
                    $("#emailmsg").html("");
                    $("#emailmsg").removeClass("text-danger");
                } else {
                    $("#email-error").addClass("has-error");
                    $(':button[type="submit"]').prop('disabled', true);
                    $("#emailmsg").html("Email Address already exists");
                    $("#emailmsg").addClass("text-danger");
                }
            }
        });
    });

    _validate_form($('.invite-form'),
        {
            email: 'required',
            'events[0]': 'required',
            'permissionid[0][]': 'required',
            firstname: 'required',
            lastname: 'required',
            vendor: 'required',
            companyname: {
                required: {
                    depends: function (element) {
                        return ($('input[name="company"]').val() == '1') ? true : false
                    }
                }
            },
            companytitle: {
                required: {
                    depends: function (element) {
                        return ($('input[name="company"]').val() == '1') ? true : false
                    }
                }
            },
            venueid: {
                required: {
                    depends: function (element) {
                        return ($('input[name="contacttype"]').val() == '5') ? true : false
                    }
                }
            }
        });

    var events = <?php echo json_encode($events); ?>;
    var permissions = <?php echo json_encode($permissions); ?>;

    $("body").on("click", "#add-more", function (e) {
        e.preventDefault();
        var my_fields = $("div[id^='field-']");
        var highest = -Infinity;
        $.each(my_fields, function (mindex, mvalue) {
            var fieldNum = mvalue.id.split("-");
            highest = Math.max(highest, parseFloat(fieldNum[1]));
        });

        var next = highest;
        var arr = [];

        $('.event-selectpicker').each(function (index, data) {
            var eventid = $(this).attr('id');
            var eventselected = $("#" + eventid + " option:selected");
            arr.push(eventselected);
        });

        var addto = "#field-" + next;
        var addRemove = "#field-" + (next);

        next = next + 1;
        var newIn = "";
        newIn += ' <div id="field-' + next + '" name="field' + next + '" class="row mbot20"><div class="col-md-4"><label class="control-label" for="event[' + next + ']">Projects <small class="req text-danger">* </small></label><select id="events' + next + '" name="events[' + next + ']" class="form-control selectpicker event-selectpicker" data-width="100%" data-none-selected-text="Select" data-live-search="true">';

        newIn += '<optgroup label="Projects">';
        newIn += '<option value="<?php echo $project->id;?>"><?php echo addslashes($project->name); ?></option>';
        newIn += '</optgroup>';

        newIn += '<optgroup label="Sub Projects">';
        $.each(events, function (tindex, tvalue) {
            var projectname = tvalue.name;
            newIn += '<option value="' + tvalue.id + '">' + projectname.replace(/'/g, "\'");
            +'</option>';
        });
        newIn += '</optgroup>';

        newIn += '</select></div>';
        newIn += '<div class="col-md-4"><label class="control-label" for="permission[' + next + ']">Permissions <small class="req text-danger">* </small></label><select id="permissionid[' + next + ']" name="permissionid[' + next + '][]" class="form-control selectpicker permission-selectpicker" data-width="100%" data-none-selected-text="Select" data-live-search="true" multiple>';
        $.each(permissions, function (rindex, rvalue) {
            newIn += '<option value="' + rvalue.permissionid + '">' + rvalue.name + '</option>';
        });

        newIn += '</select></div>';

        newIn += '<div class="col-md-2 mtop25"><button id="remove' + (next) + '" class="btn btn-danger remove-me" >Remove</button></div></div>';
        var newInput = $(newIn);
        $(addto).after(newInput);
        //$(addRemove).after(removeButton);

        $("#field-" + next).attr('data-source', $(addto).attr('data-source'));
        $("#count").val(next);

        $('.selectpicker').selectpicker('render');
        $("#events" + next + " option").show();

        $(arr).each(function () {
            var arrval = $(this).val();
            if (arrval != "") {
                $('.event-selectpicker').each(function (index, data) {
                    if ($("#events" + index).val() == arrval) {
                        $("#events" + index + " option[value^=" + arrval + "]").show();
                    } else {
                        $("#events" + index + " option[value^=" + arrval + "]").hide();
                    }
                });
                $("#events" + next + " option[value^=" + arrval + "]").hide();
                $(".selectpicker").selectpicker('refresh');
            }
        });

        $('.event-selectpicker').each(function () {
            $(this).rules("add", {
                required: true
            });
        });

        $('.permission-selectpicker').each(function () {
            $(this).rules("add", {
                required: true
            });
        });

    });

    jQuery.validator.addMethod("phoneUS", function (phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, "");
        return this.optional(element) || phone_number.length > 9 && phone_number.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
    }, "Please specify a valid phone number.(Ex: xxxxxxxxxx)");

    var createPhoneValidation = function () {
        $(".multiphone .form-control").each(function () {
            $(this).mask("(999) 999-9999", {placeholder: "(___) ___-____"});
        });
    }
    createPhoneValidation();

    $('body').on("click", ".remove-me", function (e) {
        e.preventDefault();
        var fieldNum = this.id.charAt(this.id.length - 1);

        var removedEventNum = $("#events" + fieldNum).val();

        $('.event-selectpicker').each(function (index, data) {
            if (removedEventNum != "") {
                $("#events" + index + " option[value^=" + removedEventNum + "]").show();
            }
        });
        var fieldID = "#field-" + fieldNum;
        $(this).remove();
        $(fieldID).remove();
        $(".selectpicker").selectpicker('refresh');
    });

    $('body').on('changed.bs.select', '.event-selectpicker', function () {
        // check the old value
        var oldSelected = $(this).attr('data-selected');
        var newSelected = $(this).val();
        var eventid = $(this).attr('id');
        if (oldSelected != "" || oldSelected != 'undefined') {
            $('.event-selectpicker').each(function (index, data) {
                $("#events" + index + " option[value^=" + oldSelected + "]").show();
                if ($("#events" + index).val() == newSelected) {
                    $("#events" + index + " option[value^=" + newSelected + "]").show();
                } else {
                    $("#events" + index + " option[value^=" + newSelected + "]").hide();
                }
            });
        }
        // Save the new value
        $(this).attr('data-selected', $(this).val());
        // Refresh the selectpicker to reflect updates
        $('.selectpicker').selectpicker('refresh');
    });
</script>
</body>
</html>