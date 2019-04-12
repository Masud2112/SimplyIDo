<?php
/**
 * Added By : Vaidehi
 * Dt : 10/14/2017
 * Add New Lead Form
 */
init_head();
?>
<div id="wrapper">
    <div class="content leads-page">
        <div class="row">
            <div class="col-sm-12">

                <div class="breadcrumb">
                    <?php /*if (isset($pg) && $pg == 'home') { */ ?>
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php /*} */ ?>
                    <a href="<?php echo admin_url('leads'); ?>">Leads</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span><?php echo isset($lead) ? $lead->name : "New Lead"; ?></span>
                </div>

                <h1 class="pageTitleH1"><i class="fa fa-tty"></i><?php echo $title; ?></h1>
                <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'lead-form', 'autocomplete' => 'off')); ?>
                <input type="hidden" name="pg" value="<?php echo isset($pg) ? $pg : ''; ?>">

                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body formdata">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h5 class="sub-title">
                                            <?php echo _l('lead_profile'); ?>
                                        </h5>
                                        <?php /*if ((isset($lead) && $lead->profile_image == NULL) || !isset($lead)) { */?><!--
                                            <div class="form-group">
                                                <label for="lead_profile_image"
                                                       class="profile-image"><?php /*echo _l('staff_edit_profile_image'); */?></label>
                                                <i class="fa fa-question-circle" data-toggle="tooltip"
                                                   data-title="<?php /*echo _l("lead_profile_dimension") */?>"></i>
                                                <div class="input-group">
                                      <span class="input-group-btn">
                                        <span class="btn btn-primary"
                                              onclick="$(this).parent().find('input[type=file]').click();">Browse</span>
                                        <input class="image_upload" name="lead_profile_image"
                                               onchange="$(this).parent().parent().find('.form-control').html($(this).val().split(/[\\|/]/).pop());"
                                               style="display: none;" type="file" accept="image/*">
                                      </span>
                                                    <span class="form-control"></span>
                                                </div>
                                            </div>
                                        --><?php /*} */?>
                                        <?php /*if (isset($lead) && $lead->profile_image != NULL) { */?><!--
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-xs-11">
                                                        <div class="profileImg_blk">
                                                            <?php /*echo lead_profile_image($lead->id, array('img', 'img-responsive', 'lead-profile-image-thumb'), 'thumb'); */?>
                                                        </div>
                                                    </div>
                                                    <div class="col-xs-1 text-right deleteImgProf_blk">
                                                        <a href="<?php /*echo admin_url('leads/remove_lead_profile_image/' . $lead->id); */?>"><i
                                                                    class="fa fa-remove"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="lead_profile_image" id="lead_profile_image"
                                                   value="<?php /*echo $lead->profile_image; */?>">
                                        --><?php /*} */?>

                                        <div class="profile-pic">
                                            <?php
                                            $src = "";
                                            if ((isset($lead) && $lead->profile_image != NULL)) {
                                                $profileImagePath = FCPATH.'uploads/lead_profile_images/' . $lead->id . '/round_' . $lead->profile_image;
                                                if (file_exists($profileImagePath)) {
                                                    $src = base_url() . 'uploads/lead_profile_images/' . $lead->id . '/round_' . $lead->profile_image;
                                                }

                                            } ?>
                                            <div class="profile_imageview <?php echo empty($src) ? 'hidden' : ''; ?>">
                                                <img src="<?php echo $src; ?>"/>
                                                <?php if ($src == "") { ?>
                                                    <!-- <a class="clicktoaddimage" href="javascript:void(0)"
                                                       onclick="croppedDelete('profile');">
                                                        <span><i class="fa fa-trash"></i></span>
                                                    </a>
                                                    <a class="btn btn-info mtop10" href="javascript:void(0)"
                                                       onclick="reCropp('profile');">
                                                        <?php //echo _l('recrop')?></a> -->
                                                        
                                                    <div class="actionToEdit">
                                                        <a class="clicktoaddimage" href="javascript:void(0)" onclick="croppedDelete('profile');">
                                                            <span><i class="fa fa-trash"></i></span>
                                                        </a>
                                                        <a class="recropIcon_blk" href="javascript:void(0)" onclick="reCropp('profile');">
                                                            <span><i class="fa fa-crop" aria-hidden="true"></i></span>
                                                        </a>
                                                    </div>
                                                <?php } else { ?>
                                                    <div class="actionToEdit">
                                                        <a class="_delete clicktoaddimage"
                                                        href="<?php echo admin_url('leads/remove_lead_profile_image/'.$lead->id); ?>">
                                                            <span><i class="fa fa-trash"></i></span>
                                                        </a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="clicktoaddimage <?php echo !empty($src) ? 'hidden' : ''; ?>">
                                                <div class="drag_drop_image">
                                                    <span class="icon"><i class="fa fa-image"></i></span>
                                                    <span><?php echo _l('dd_upload');?></span>
                                                </div>
                                                <input id="profile_image" type="file" class="" name="lead_profile_image" onchange="readFile(this,'profile');"/ >
                                                <input type="hidden" id="imagebase64" name="imagebase64">
                                            </div>
                                            <div class="cropper" id="profile_croppie">
                                                <div class="copper_container">
                                                    <div id="profile-cropper"></div>
                                                    <div class="cropper-footer">
                                                        <button type="button" class="btn btn-info p9 actionDone" type="button" id="" onclick="croppedResullt('profile');">
                                                            <?php echo _l('save'); ?>
                                                        </button>
                                                        <button type="button" class="btn btn-default actionCancel" data-dismiss="modal" onclick="croppedCancel('profile');">
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

                                        <?php $attrs = array('autofocus' => true); ?>
                                        <?php echo render_input('name', 'lead_add_edit_event_name', (isset($lead) ? $lead->name : ''), '', $attrs); ?>
                                        <?php echo render_select('eventtypeid', $eventtypes, array('eventtypeid', 'eventtypename'), 'lead_add_edit_event_type', (isset($lead) ? $lead->eventtypeid : '')); ?>
                                        <?php echo render_select('venueid', $venues, array('venueid', 'venuename'), 'lead_add_edit_venue', (isset($project) ? $project->venueid : '')); ?>
                                        <div class="col-sm-6">
                                            <div class="form-group" id="eventstartdate">
                                                <?php
                                                if (isset($_GET['start_dt'])) {
                                                    $from_dt = date_create($_GET['start_dt']);
                                                    $from_dt = date_format($from_dt, 'm/d/Y H:i');
                                                    $start_date = _dt($from_dt, true);
                                                } else {
                                                    $start_date = isset($lead) ? _dt($lead->eventstartdatetime, true) : '';
                                                } ?>
                                                <?php echo render_datetime_input('eventstartdatetime', 'lead_add_edit_event_start_datetime', ($start_date), array('data-date-min-date' => date('Y-m-d H:i'))); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group" id="eventenddate">
                                                <?php echo render_datetime_input('eventenddatetime', 'lead_add_edit_event_end_datetime', (isset($lead) ? _dt($lead->eventenddatetime, true) : ''), array('data-date-min-date' => date('Y-m-d H:i'))); ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="<?php echo _l('lead_add_edit_event_end_timezone'); ?>"
                                                       class="control-label"><?php echo _l('lead_add_edit_event_end_timezone'); ?></label>
                                                <select name="eventtimezone" id="eventtimezone"
                                                        class="form-control selectpicker"
                                                        data-none-selected-text="<?php echo _l('lead_add_edit_event_end_timezone'); ?>"
                                                        data-live-search="true">
                                                    <?php
                                                    //foreach(get_timezones_list() as $timezones) { ?>
                                                    <!--<optgroup label="<?php //echo $key; ?>">-->
                                                    <?php
                                                    foreach (get_timezones_list() as $key => $timezone) {
                                                        $timezone_name = str_replace("America/", "", $timezone);
                                                        $timezone_name = str_replace("_", " ", $timezone_name);
                                                        ?>
                                                        <option value="<?php echo $key; ?>" <?php echo(!isset($lead) ? (get_brand_option('default_timezone') == $key ? 'selected="selected"' : '') : ($lead->eventtimezone == $key ? 'selected="selected"' : '')); ?>>
                                                            <?php echo $timezone_name; ?>
                                                        </option>
                                                    <?php } ?>
                                                    <!--</optgroup>-->
                                                    <?php //} ?>
                                                </select>
                                            </div>
                                        </div>


                                        <?php if (!isset($lead)) { ?>
                                            <div class="form-group">
                                                <div class="lead-select-date-contacted hide">
                                                    <?php echo render_datetime_input('custom_contact_date', 'lead_add_edit_datecontacted', '', array('data-date-end-date' => date('Y-m-d'))); ?>
                                                </div>
                                                <div class="checkbox checkbox-primary mtop25 hide">
                                                    <input type="checkbox" name="contacted_today" id="contacted_today"
                                                           checked>
                                                    <label for="contacted_today"><?php echo _l('lead_add_edit_contacted_today'); ?></label>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <?php echo render_datetime_input('lastcontact', 'leads_dt_last_contact', (isset($lead) ? _dt($lead->lastcontact) : ''), array('data-date-end-date' => date('Y-m-d'))); ?>
                                        <?php } ?>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel_s">
                                    <div class="panel-body">
                                        <h5 class="sub-title">
                                            <?php echo _l('lead_details'); ?>
                                        </h5>

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <?php echo render_select('status', $statuses, array('id', 'name'), 'lead_add_edit_status', (isset($lead) ? $lead->status : '')); ?>
                                            </div>
                                            <div class="col-sm-6">
                                                <?php echo render_select('assigned[]', $members, array('staffid', 'firstname', 'lastname'), 'lead_add_edit_assigned', (isset($lead) ? $lead->assigned : ''), array('multiple' => true), array(), '', '', false); ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <?php //echo render_input('budget', 'lead_add_edit_budget', (isset($lead) ? $lead->budget : ''), 'number'); ?>

                                                <div class="form-group budgetLead_blk">
                                                    <label for="budget" class="control-label">Budget</label>
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="fa fa-usd"
                                                                                           aria-hidden="true"></i></span>
                                                        <input type="number" id="budget" name="budget"
                                                               class="form-control"
                                                               value="<?php echo isset($lead) ? $lead->budget : '' ?>">
                                                    </div>


                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="<?php echo _l('lead_add_edit_booking_confidence'); ?>"
                                                           class="control-label"><?php echo _l('lead_add_edit_booking_confidence'); ?></label>
                                                    <select name="bookingconfidence" id="bookingconfidence"
                                                            class="form-control selectpicker"
                                                            data-none-selected-text="<?php echo _l('lead_add_edit_booking_confidence'); ?>"
                                                            data-live-search="true">
                                                        <option value=""></option>
                                                        <option value="Low" <?php echo(isset($lead->bookingconfidence) && $lead->bookingconfidence == 'Low' ? 'selected="selected"' : ''); ?>>
                                                            Low
                                                        </option>
                                                        <option value="Medium" <?php echo(isset($lead->bookingconfidence) && $lead->bookingconfidence == 'Medium' ? 'selected="selected"' : ''); ?>>
                                                            Medium
                                                        </option>
                                                        <option value="High" <?php echo(isset($lead->bookingconfidence) && $lead->bookingconfidence == 'High' ? 'selected="selected"' : ''); ?>>
                                                            High
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <?php echo render_date_input('eventinquireon', 'lead_add_edit_event_inquireon', (isset($lead) ? _dt($lead->eventinquireon) : date('m/d/Y'))); ?>
                                            </div>
                                            <div class="col-sm-6">
                                                <?php echo render_date_input('eventdecideby', 'lead_add_edit_event_decideby', (isset($lead) ? _dt($lead->eventdecideby) : ''), array('data-date-min-date' => date('Y-m-d'))); ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <?php echo render_select('source', $sources, array('id', 'name'), 'lead_add_edit_source', (isset($lead) ? $lead->source : '')); ?>
                                            </div>
                                            <div class="col-sm-6">
                                                <?php echo render_input('sourcedetails', 'lead_add_edit_sourcedetails', (isset($lead) ? $lead->sourcedetails : '')); ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <?php echo render_textarea('comments', 'lead_add_edit_comments', (isset($lead) ? $lead->comments : '')); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (!isset($lead)) { ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="panel_s">
                                        <div class="panel-body">
                                            <h6 class="sub-sub-title">
                                                <?php echo _l('lead_contact'); ?>
                                            </h6>
                                            <!--<div class="form-group contact-options">
                                            <div class="radio radio-primary radio-inline">
                                                <input id="contact_new" name="leadcontact[]" value="new" checked="true"
                                                       type="radio">
                                                <label for="<?php /*echo _l('new_contact'); */ ?>"><?php /*echo _l('new_contact'); */ ?></label>
                                            </div>
                                            <div class="radio radio-primary radio-inline">
                                                <input id="contact_existing" name="leadcontact[]" value="existing" type="radio">
                                                <label for="<?php /*echo _l('choose_existing_client'); */ ?>"><?php /*echo _l('choose_existing_client'); */ ?></label>
                                            </div>
                                        </div>-->
                                            <div class="multiplecontacts">
                                                <?php $this->load->view('admin/leads/newform', array($clients)); ?>
                                            </div>
                                            <a id="addnewcontact" href="javascript:void(0)"
                                               class="btn btn-default mtop15">
                                                <i class="fa fa-plus"></i>
                                                Add New Contact
                                            </a>
                                        </div>
                                    </div>
                                    <!--<div id="new-address-book">-->

                                    <!--</div>-->
                                    <!--<div id="existing-client-book">
                                        <div class="panel-body">
                                            <?php
                                    /*                                            if (isset($clients)) {
                                                                                    echo render_select('clients', $clients, array('addressbookid', 'name', 'email'), 'lead_add_edit_client');
                                                                                }
                                                                                */ ?>
                                        </div>
                                    </div>-->
                                </div>
                            </div>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <div class="topButton">
                            <button class="btn btn-default" type="button"
                                    onclick="window.history.go(-1);"><?php echo _l('Cancel'); ?></button>
                            <button type="submit" class="btn btn-info lead_save "
                                    data-form="lead-form"><?php echo _l('submit'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php app_external_form_footer('lead'); ?>
<?php init_tail(); ?>
<script type="text/javascript">
    //$('#new-address-book').toggle();
    //$('#existing-client-book').toggle();

    $('body').on('change', 'input:radio', function () {
        var index = $(this).data('index');
        if ($(this).val() == 'new') {
            $('#new-address-book-' + index).toggle();
            $('#existing-client-book-' + index).hide();
            $('#existing-client-book-' + index + ' :input').prop("disabled", true);
            $('#new-address-book-' + index + ' :input').prop("disabled", false);
        }

        if ($(this).val() == 'existing') {
            $('#new-address-book-' + index).hide();
            $('#existing-client-book-' + index).toggle();
            $('#existing-client-book-' + index + ' :input').prop("disabled", false);
            $('#new-address-book-' + index + ' :input').prop("disabled", true);
        }
    });

    jQuery.validator.addMethod("phoneUS", function (phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, "");
        return this.optional(element) || phone_number.length > 9 && phone_number.match(/^(\+?1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
    }, "Please specify a valid phone number.(Ex: xxxxxxxxxx)");

    jQuery.validator.addMethod("greaterThan",
        function (value, element, params) {

            if (!/Invalid|NaN/.test(new Date(value))) {
                return new Date(value) > new Date($(params).val());
            }

            return isNaN(value) && isNaN($(params).val())
                || (Number(value) > Number($(params).val()));
        }, 'Must be greater than Start Date.');

    _validate_form($('.lead-form'), {
        name: 'required',
        eventtypeid: 'required',
        eventstartdatetime: 'required',
        eventenddatetime: {required: true, greaterThan: "#eventstartdatetime"},
        status: 'required',
        eventinquireon: 'required',
        companyname: 'required',
        companytitle: 'required',
        firstname: 'required',
        clients: 'required',
    });

    // Code for multiple email validation
    var createEmailValidation = function () {
        $(".multiemail .form-control").each(function (index, value) {
            $(this).rules('remove');
            $(this).rules('add', {
                email: true,
                required: true,
                remote: {
                    url: site_url + "admin/misc/addressbook_email_exists",
                    type: 'post',
                    data: {
                        email: function () {
                            return $(value).val();
                        },
                        addressbookid: function () {
                            return $('input[name="addressbookid"]').val();
                        }
                    }
                },
                messages: {
                    email: "Please enter valid email.",
                    required: "Please enter an email adress.",
                    remote: "Email already exist."
                }
            });
        });
    }
    /*var createRequiredValidation = function () {

        $(".required.form-control").each(function (index, value) {
            $(this).rules('remove');
            $(this).rules('add', {
                required: true,
            });
        });
    }
    createRequiredValidation();*/
    // Code for multiple phone validation
    var createPhoneValidation = function () {
        $(".multiphone .form-control").each(function () {
            $(this).mask("(999) 999-9999", {placeholder: "(___) ___-____"});
        });
    }
    var createExtValidation = function () {
        $(".multiext .form-control").each(function () {
            $(this).mask("99999", {placeholder: "12345"});
        });
    }

    showcompany();

    function showcompany(index = 0) {
        if ($('#contact_' + index + '_company').is(":checked"))
            $("#companydetails_" + index).show();
        else
            $("#companydetails_" + index).hide();
    }

    $('body').on('click', '.company', function () {
        var index = $(this).data('index');
        showcompany(index);
    });

    //Added By Avni on 10/18/2017
    $('#eventstartdate #eventstartdatetime').change(function (e) {
        var selected = e.target.value;
        if (selected != '') {
            $.ajax({
                url: "<?php echo admin_url('leads/getleadendate')?>",
                data: "startdate=" + selected,
                method: "post",
                success: function (result) {
                    $('#eventenddate #eventenddatetime').val(result);
                }
            });
        }
    });

    $('body').on('click', '.custom_address', function () {
        var addressid = $(this).data('addressid');
        $(".customaddress-" + addressid).show();
    });

    $('.remove_address').on('click', function () {
        var addressid = $(this).data('addressid');
        $("#autocomplete" + addressid).val('');
        $("#address[" + addressid + "][street_number]").val('');
        $("#address[" + addressid + "][route]").val('');
        $("#address[" + addressid + "][locality]").val('');
        $("#address[" + addressid + "][administrative_area_level_1]").val('');
        $("#address[" + addressid + "][postal_code]").val('');
        $(".customaddress-" + addressid).hide();
        $(this).hide();
        $(".customadd-" + addressid).show();
    });
</script>
<script>
    // This example displays an address form, using the autocomplete feature
    // of the Google Places API to help users fill in the information.

    // This example requires the Places library. Include the libraries=places
    // parameter when you first load the API. For example:
    // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">


    // Start code of Add more / Remove email
    var email_phone_type = <?php echo json_encode($email_phone_type); ?>;
    $('body').on('click', '.email-add-more', function (e) {
        e.preventDefault();
        var index = $(this).data('index');
        var my_email_fields = $("#contactemails-" + index + " .contactemail");
        var highestemail = -Infinity;
        $.each(my_email_fields, function (mindex, mvalue) {
            var fieldEmailNum = mvalue.id.split("-");
            highestemail = Math.max(highestemail, parseFloat(fieldEmailNum[1]));
        });
        var emailnext = highestemail;
        var addtoEmail = "#contactemails-" + index + " #email-" + emailnext;
        var addRemoveEmail = "#contactemails-" + index + " #email-" + (emailnext);
        emailnext = emailnext + 1;

        var newemailIn = "";
        newemailIn += ' <div class="row contactemail" id="email-' + emailnext + '" name="email' + emailnext + '"><div class="col-sm-3"><div class="form-group"><label class="control-label" for="email[' + emailnext + '][type]">Type</label><select id="contact[' + index + '][email][' + emailnext + '][type]" name="contact[' + index + '][email][' + emailnext + '][type]" class="selectpicker" data-width="100%" data-none-selected-text="Select">';
        $.each(email_phone_type, function (etindex, etvalue) {
            newemailIn += '<option value="' + etindex + '">' + etvalue + '</option>';
        });

        newemailIn += '</select></div></div>';
        newemailIn += '<div class="col-sm-6 col-xs-10 multiemail"><div class="form-group"><label class="control-label" for="email[' + emailnext + '][email]"><small class="req text-danger">* </small>Email</label><input id="contact[' + index + '][email][' + emailnext + '][email]" class="form-control required" name="contact[' + index + '][email][' + emailnext + '][email]" autocomplete="off" value="" type="email"></div>';
        newemailIn += '</div>';
        newemailIn += '<div class="col-sm-1 col-xs-2"><button id="emailremove-' + (emailnext) + '" class="btn btn-danger email-remove-me" data-index=' + index + '><i class="fa fa-trash-o"></i></button></div></div>';
        var newemailInput = $(newemailIn);

        //var removeEmailButton = $(removeEmailBtn);
        $(addtoEmail).after(newemailInput);
        // $(addRemoveEmail).after(removeEmailButton);
        $("#contactemails-" + index + " #email-" + emailnext).attr('data-source', $(addtoEmail).attr('data-source'));
        $("#count").val(emailnext);

        $('.email-remove-me').click(function (e) {
            e.preventDefault();
            var index = $(this).data('index');
            var fieldEmailNum = this.id.split("-");
            var fieldEmailID = "#contactemails-" + index + " #email-" + fieldEmailNum[1];
            $(fieldEmailID).remove();
        });
        $('.selectpicker').selectpicker('render');
        createEmailValidation();
    });
    createEmailValidation();
    $('.email-remove-me').click(function (e) {
        e.preventDefault();
        var index = $(this).data('index');
        var fieldEmailNum = this.id.split("-");
        var fieldEmailID = "#contactemails-" + index + " #email-" + fieldEmailNum[1];
        $(fieldEmailID).remove();
    });
    // End code of Add more / Remove email

    // Start code of Add more / Remove phone
    var email_phone_type = <?php echo json_encode($email_phone_type); ?>;
    $('body').on('click', '.phone-add-more', function (e) {
        e.preventDefault();
        var index = $(this).data('index');
        var my_phone_fields = $("#contactphones-" + index + " .contactphone");
        var highestphone = -Infinity;
        $.each(my_phone_fields, function (mindex, mvalue) {
            var fieldphoneNum = mvalue.id.split("-");
            highestphone = Math.max(highestphone, parseFloat(fieldphoneNum[1]));
        });
        var phonenext = highestphone;
        var addtophone = "#contactphones-" + index + " #phone-" + phonenext;
        var addRemovephone = "#contactphones-" + index + " #phone-" + (phonenext);

        phonenext = phonenext + 1;
        var newphoneIn = "";
        newphoneIn += ' <div class="row contactphone" id="phone-' + phonenext + '" name="phone' + phonenext + '"><div class="col-sm-3"><div class="form-group"><label class="control-label" for="contact[' + index + '][phone][' + phonenext + '][type]">Type</label><select id="contact[' + index + '][phone][' + phonenext + '][type]" name="contact[' + index + '][phone][' + phonenext + '][type]" class="selectpicker" data-width="100%" data-none-selected-text="Select">';
        $.each(email_phone_type, function (epindex, epvalue) {
            newphoneIn += '<option value="' + epindex + '">' + epvalue + '</option>';
        });

        newphoneIn += '</select></div></div>';
        newphoneIn += '<div class="col-sm-4 col-xs-10 multiphone"><div class="form-group"><label class="control-label" for="phone[' + phonenext + '][phone]">Phone</label><input id="contact[' + index + '][phone][' + phonenext + '][phone]" class="form-control" name="contact[' + index + '][phone][' + phonenext + '][phone]" autocomplete="off" value="" type="text"></div>';
        newphoneIn += '</div>';
        newphoneIn += '<div class="col-sm-1 col-xs-10 multiext"><div class="form-group"><label class="control-label" for="phone[' + phonenext + '][ext]">Ext</label><input id="contact[' + index + '][phone][' + phonenext + '][ext]" class="form-control" name="contact[' + index + '][phone][' + phonenext + '][ext]" autocomplete="off" maxlength=5 value="" type="tel"></div>';
        newphoneIn += '</div>';
        newphoneIn += '<div class="col-sm-1 col-xs-2"><button id="phoneremove-' + (phonenext) + '" class="btn btn-danger phone-remove-me" data-index=' + index + '><i class="fa fa-trash-o"></i></button></div></div>';
        var newphoneInput = $(newphoneIn);

        //var removephoneButton = $(removephoneBtn);
        $(addtophone).after(newphoneInput);
        // $(addRemovephone).after(removephoneButton);
        $("#contactphones-" + index + " #phone-" + phonenext).attr('data-source', $(addtophone).attr('data-source'));
        $("#count").val(phonenext);

        $('.phone-remove-me').click(function (e) {
            e.preventDefault();
            var index = $(this).data('index');
            var fieldPhoneNum = this.id.split("-");
            var fieldphoneID = "#contactphones-" + index + " #phone-" + fieldPhoneNum[1];
            //$(this).parent('div').remove();
            $(fieldphoneID).remove();
        });
        createPhoneValidation();
        createExtValidation();
        $('.selectpicker').selectpicker('refresh');
    });
    createPhoneValidation();
    createExtValidation();
    $('.phone-remove-me').click(function (e) {
        e.preventDefault();
        var index = $(this).data('index');
        var fieldPhoneNum = this.id.split("-");
        var fieldphoneID = "#contactphones-" + index + " #phone-" + fieldPhoneNum[1];
        //$(this).parent('div').remove();
        $(fieldphoneID).remove();
    });
    // End code of Add more / Remove phone

    // Start code of Add more / Remove website
    var website_type = <?php echo json_encode($socialsettings); ?>;
    $('body').on('click', '.website-add-more', function (e) {
        e.preventDefault();
        var index = $(this).data('index');
        var my_website_fields = $("#contactwebsites-" + index + " .contactwebsite");
        var highestwebsite = -Infinity;
        $.each(my_website_fields, function (mindex, mvalue) {
            var fieldwebsiteNum = mvalue.id.split("-");
            highestwebsite = Math.max(highestwebsite, parseFloat(fieldwebsiteNum[1]));
        });
        var websitenext = highestwebsite;
        var addtowebsite = "#contactwebsites-" + index + " #website-" + websitenext;
        var addRemovewebsite = "#contactwebsites-" + index + " #website-" + (websitenext);
        websitenext = websitenext + 1;

        var newwebsiteIn = "";
        newwebsiteIn += ' <div class="row contactwebsite" id="website-' + websitenext + '" name="website' + websitenext + '"><div class="col-sm-3"><div class="form-group"><label class="control-label" for="contact[' + index + '][website][' + websitenext + '][type]">Type</label><select id="contact[' + index + '][website][' + websitenext + '][type]" name="contact[' + index + '][website][' + websitenext + '][type]" class="selectpicker" data-width="100%" data-none-selected-text="Select">';
        $.each(website_type, function (windex, wvalue) {
            newwebsiteIn += '<option value="' + wvalue['socialid'] + '">' + wvalue['name'] + '</option>';
        });

        newwebsiteIn += '</select></div></div>';
        newwebsiteIn += '<div class="col-sm-6  col-xs-10"><div class="form-group"><label class="control-label" for="website[' + websitenext + '][url]">Address</label><input id="contact[' + index + '][website][' + websitenext + '][url]" class="form-control" name="contact[' + index + '][website][' + websitenext + '][url]" autocomplete="off" value="" type="text"></div>';
        newwebsiteIn += '</div>';
        newwebsiteIn += '<div class="col-sm-1  col-xs-2"><button id="websiteremove-' + (websitenext) + '" class="btn btn-danger website-remove-me" data-index=' + index + ' ><i class="fa fa-trash-o"></i></button></div></div>';
        var newwebsiteInput = $(newwebsiteIn);
        $(addtowebsite).after(newwebsiteInput);
        $("#contactwebsites-" + index + " #website-" + websitenext).attr('data-source', $(addtowebsite).attr('data-source'));
        $("#count").val(websitenext);

        $('.website-remove-me').click(function (e) {
            e.preventDefault();
            var index = $(this).data('index');
            var fieldwebsiteNum = this.id.split("-");
            var fieldwebsiteID = "#contactwebsites-" + index + " #website-" + fieldwebsiteNum[1];
            $(fieldwebsiteID).remove();
        });
        $('.selectpicker').selectpicker('render');
    });
    $('.website-remove-me').click(function (e) {
        e.preventDefault();
        var index = $(this).data('index');
        var fieldwebsiteNum = this.id.split("-");
        var fieldwebsiteID = "#contactwebsites-" + index + " #website-" + fieldwebsiteNum[1];
        $(fieldwebsiteID).remove();
    });
    // End code of Add more / Remove website
 
    // Start code of Add more / Remove address
    var address_type = <?php echo json_encode($address_type); ?>;
    $('body').on('click', '.address-add-more', function (e) {
        e.preventDefault();
        var index = $(this).data('index');
        var my_address_fields = $("#contactaddresses-" + index + " .contactaddress");
        var highestaddress = -Infinity;
        $.each(my_address_fields, function (mindex, mvalue) {
            var fieldaddressNum = mvalue.id.split("-");
            highestaddress = Math.max(highestaddress, parseFloat(fieldaddressNum[1]));
        });
        var addressnext = highestaddress;
        var addtoaddress = "#contactaddresses-" + index + " #address-" + addressnext;
        var addRemoveaddress = "#contactaddresses-" + index + " #address-" + (addressnext);

        addressnext = addressnext + 1;
        var newaddressIn = "";
        newaddressIn += ' <div class="contactaddress" id="address-' + addressnext + '"><div class="row"><div class="col-lg-3 col-sm-3"><div class="form-group"><label for="contact[' + index + '][address][' + addressnext + '][type]" class="control-label">Type</label><select name="contact[' + index + '][address][' + addressnext + '][type]" id="contact[' + index + '][address][' + addressnext + '][type]" class="form-control selectpicker" data-none-selected-text="Select">';
        $.each(address_type, function (aindex, avalue) {
            newaddressIn += '<option value="' + aindex + '">' + avalue + '</option>';
        });

      

        newaddressIn += '</select></div></div><div class="col-sm-6 col-xs-10"><div class="row"><div class="col-md-9 col-lg-10"><div id="locationField" class="form-group locationField"><label class="control-label" for="address">Address</label><input id="contact_' + index + '_autocomplete' + addressnext + '" class="form-control searchmap" data-addmap="' + addressnext + '" placeholder="Search Google Maps..." onfocus="geolocate()" type="text" data-index="' + index + '"></div></div><div class="col-md-3 col-lg-2"><div class="customadd-btn"><div class="form-group"><button type="button" class="btn btn-info custom_address customadd-' + addressnext + '" data-addressid="' + addressnext + '" data-index="' + index + '">Custom</button></div></div></div></div></div><div class="col-sm-1 col-xs-2"><button id="addressremove-' + (addressnext) + '" class=" address-remove-me btn btn-danger"><i class="fa fa-trash-o"></i></button></div></div>';
        newaddressIn += ' <div id="customaddress-' + addressnext + '" class="addressdetails customaddress-' + addressnext + '" style="display:none"><div class="row"><div class="col-sm-5 col-md-3"><div class="form-group"><label for="contact[' + index + '][address][' + addressnext + '][street_number]" class="control-label">Address1</label><input id="contact[' + index + '][address][' + addressnext + '][route]" name="contact[' + index + '][address][' + addressnext + '][route]" class="form-control" value="" type="text"></div></div><div class="col-sm-4 col-md-5"><div class="form-group"><label for="contact[' + index + '][address][' + addressnext + '][street_number]" class="control-label">Address2</label><input id="contact[' + index + '][address][' + addressnext + '][street_number]" name="contact[' + index + '][address][' + addressnext + '][street_number]" class="form-control" value="" type="text"></div></div></div><div class="address_extra"><div class="row"><div class="col-sm-5 col-md-3"><div class="form-group"><label for="contact[' + index + '][address][' + addressnext + '][locality]" class="control-label">City</label><input id="contact[' + index + '][address][' + addressnext + '][locality]" name="contact[' + index + '][address][' + addressnext + '][locality]" class="form-control" value="" type="text"></div></div><div class="col-sm-4 col-md-5"><div class="form-group"><label for="contact[' + index + '][address][' + addressnext + '][administrative_area_level_1]" class="control-label">State</label><input id="contact[' + index + '][address][' + addressnext + '][administrative_area_level_1]" name="contact[' + index + '][address][' + addressnext + '][administrative_area_level_1]" class="form-control" value="" type="text"></div></div></div><div class="row"><div class="col-sm-5 col-md-3"><div class="form-group"><label for="contact[' + index + '][address][' + addressnext + '][postal_code]" class="control-label">Zip Code</label><input id="contact[' + index + '][address][' + addressnext + '][postal_code]" name="contact[' + index + '][address][' + addressnext + '][postal_code]" class="form-control" value="" type="text"></div></div><div class="col-sm-4 col-md-5"><div class="form-group"><label for="contact[' + index + '][address][' + addressnext + '][country]" class="control-label">Country</label><select name="contact[' + index + '][address][' + addressnext + '][country]" id="contact[' + index + '][address][' + addressnext + '][country]" class="form-control selectpicker" data-none-selected-text="Select" ><option value="US" selected="">United States</option></select></div></div></div></div></div>';


        

        newaddressIn += '</div></div>';
        var newaddressInput = $(newaddressIn);

        // var removeaddressButton = $(removeaddressBtn);
        $(addtoaddress).after(newaddressInput);

        //$(addRemoveaddress).after(removeaddressButton);
        $("#address-" + addressnext).attr('data-source', $(addtoaddress).attr('data-source'));
        $("#count").val(addressnext);
        $(".removeadd-" + addressnext).hide();
        $('body').on('click', '.custom_address', function () {
            var addressid = $(this).data('addressid');
            var index = $(this).data('index');
            $("#contact_" + index + " .customaddress-" + addressid).show();
        });
        $('.remove_address').on('click', function () {
            var addressid = $(this).data('addressid');
            $("#autocomplete" + addressid).val('');
            $("#address[" + addressid + "][street_number]").val('');
            $("#address[" + addressid + "][route]").val('');
            $("#address[" + addressid + "][locality]").val('');
            $("#address[" + addressid + "][administrative_area_level_1]").val('');
            $("#address[" + addressid + "][postal_code]").val('');
            $(".customaddress-" + addressid).hide();
            $(this).hide();
            $(".customadd-" + addressid).show();
        });

        $('.address-remove-me').click(function (e) {
            e.preventDefault();
            var fieldaddressNum = this.id.split("-");
            var fieldaddressID = "#address-" + fieldaddressNum[1];
            $(fieldaddressID).remove();
        });
        $('.selectpicker').selectpicker('render');
        /*$(".searchmap").on("keyup, change, keypress, keydown, click", function () {
            var searchmapid = $(this).data('addmap');
            var index = $(this).data('index');
            initAutocomplete(searchmapid,index);
        });*/
    });
    $('.address-remove-me').click(function (e) {
        e.preventDefault();
        var fieldaddressNum = this.id.split("-");
        var fieldaddressID = "#address-" + fieldaddressNum[1];
        $(fieldaddressID).remove();
    });

    $("body").on("keyup, change, keypress, keydown, click", ".searchmap", function () {
        var searchmapid = $(this).data('addmap');
        var index = $(this).data('index');
        initAutocomplete(searchmapid, index);
    });

    var placeSearch, autocomplete;
    var componentForm = {
        street_number: 'short_name',
        /*route: 'long_name',*/
        locality: 'long_name',
        administrative_area_level_1: 'long_name',
        country: 'short_name',
        postal_code: 'short_name'
    };

    function initAutocomplete(addid, index = 0) {
        // Create the autocomplete object, restricting the search to geographical
        // location types.
        // alert(addid);
        addid = addid;
        autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('contact_' + index + '_autocomplete' + addid)),
            {types: ['geocode'], componentRestrictions: {country: 'us'}});

        // When the user selects an address from the dropdown, populate the address
        // fields in the form.
        autocomplete.addListener('place_changed', function () {
            //google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
            for (var component in componentForm) {
                document.getElementById("contact[" + index + "][address][" + addid + "][" + component + "]").value = '';
                document.getElementById("contact[" + index + "][address][" + addid + "][" + component + "]").disabled = false;
            }

            // Get each component of the address from the place details
            // and fill the corresponding field on the form.
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    if (addressType == "street_number") {
                        var val = place.address_components[i][componentForm['street_number']] + " " + place.address_components[1]['long_name'];
                    }
                    document.getElementById("contact[" + index + "][address][" + addid + "][" + addressType + "]").value = val;
                }
            }
            $("#contact_" + index + " .customaddress-" + addid).show();
            $("#contact_" + index + " .customadd-" + addid).hide();
            $("#contact_" + index + " .removeadd-" + addid).show();
        });

    }

    function geolocate() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var geolocation = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                var circle = new google.maps.Circle({
                    center: geolocation,
                    radius: position.coords.accuracy
                });

                autocomplete.setBounds(circle.getBounds());
            });
        }
    }

    // End code of Add more / Remove address
    //Added By Avni on 10/18/2017
    /*$('#eventstartdate #eventstartdatetime').change(function (e) {
        var selected = e.target.value;
        $('#eventenddate #eventenddatetime').val(selected);
    });*/


    /*
    ** Added By Sanjay on 02/08/2018
    ** For start-date and end-date
    */
    $(".input-group-addon").css({"padding": "0px"});
    $(".fa.fa-calendar.calendar-icon").css({"padding": "6px 12px"});
    $(function () {
        $('.input-group-addon').find('.fa-calendar').on('click', function () {
            $(this).parent().siblings('#eventstartdatetime').trigger('focus');
            $(this).parent().siblings('#eventenddatetime').trigger('focus');
            $(this).parent().siblings('#eventinquireon').trigger('focus');
            $(this).parent().siblings('#eventdecideby').trigger('focus');
        });

        /*url = window.location.href;
        var date = url.split('?')[1].split('=')[1];*/
        // if(date)
        // {
        //   var spl_txt = date.split('-');
        //   var time = new Date();
        //   date = spl_txt[1]+"/"+spl_txt[2]+"/"+spl_txt[0]+" "+time.getHours() + ":" + time.getMinutes();
        //   $('#eventstartdatetime').val(date);
        // }

        $('#profile-cropper0').croppie({
            viewport: {width: 180, height: 180, type: 'circle'},
            boundary: {width: 180, height:180},
        });
    });


    $('body').on('click', '#addnewcontact', function (e) {
        var empty = 0;
        $('.multiplecontacts .required').each(function () {
            if ($(this).is(":hidden") == false && $(this).val() == "" && $(this).attr("name")) {
                empty++;
            }
        });
        var clients = $('.multiplecontacts select.clientselect').length;
        var selectedclients = [];
        if (clients > 0) {
            $('.multiplecontacts select.clientselect').each(function () {
                if ($(this).val() > 0) {
                    selectedclients.push($(this).val())
                }
            });
        }
        if (empty > 0) {
            $('.lead_save').trigger('click');
            return false;
        }
        //alert(selectedclients);
        var index = $('.multiplecontacts .contact').length;
        var name = $('input.contact_' + (index - 1) + '_firstname').val();
        var temp_data = {'index': index, 'selectedclients': selectedclients};
        $.ajax({
            type: 'POST',
            url: admin_url + 'leads/addnewcontact',
            data: temp_data,
            success: function (result) {
                $('.multiplecontacts').append(result);
                $('.selectpicker').selectpicker('refresh');
                $('#contactheader_' + (index - 1)).addClass('active');
                $('.multiplecontacts .contact:not(.contact_' + index + ') #contactinner').slideUp(result);
                showcompany(index);
                createPhoneValidation();
                createExtValidation();
                createEmailValidation();
                $('#profile-cropper'+index).croppie({
                    viewport: {width: 180, height: 180, type: 'circle'},
                    boundary: {width: 180, height:180},
                    showZoomer: true,
                    //enableResize: true,
                });
                /*createRequiredValidation();*/
            }
        });
    });
    $('body').on('click', '.contactheader.active', function (e) {

        var empty = 0;
        $('.multiplecontacts .required').each(function () {
            if ($(this).is(":hidden") == false && $(this).val() == "" && $(this).attr("name")) {
                empty++;
            }
        });
        if (empty > 0) {
            $('.lead_save').trigger('click');
            return false;
        }


        var index = $(this).data('index');
        $('.contactheader').addClass('active');
        $(this).removeClass('active');
        $('.contact #contactinner').slideUp();
        $('#contact_' + index + ' #contactinner').slideDown();
    });
    $('body').on('change', '.contact_firstname, .contact_lastname', function (e) {
        var index = $(this).data('index');
        var firstname = $('.contact_' + index + '_firstname').val();
        var lastname = $('.contact_' + index + '_lastname').val();
        var name = firstname + " " + lastname;
        $('#contactheader_' + (index) + " span").text(name);

    });
    /*var validator = $(".lead-form").validate({
        rules: {
            lead_profile_image: {
                required: true,
                remote: {
                    url: admin_url + "invoice_items/line_item_name_exists",
                    type: 'post',
                    data: {
                        tagid: function () {
                            return $('input[name="tagid"]').val();
                        }
                    }
                }
            }
        },
    });*/
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB-0SSogvGqWSro2pyjAlek2DP_lwfQMvE&libraries=places"></script>
</body>
</html>