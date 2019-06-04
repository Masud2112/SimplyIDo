<?php init_head(); ?>
<div id="wrapper">
    <div class="content addressbook-page">
        <div class="row">
            <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'addressbook-form', 'autocomplete' => 'off')); ?>
            <div class="col-sm-12">
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <?php if (isset($venueid) || isset($vid)) {
                        if (isset($vid)) {
                            $vnuid = $vid;
                        } else {
                            $vnuid = $venueid;
                        }
                        ?>

                        <?php if (isset($lid)) { ?>
                            <a href="<?php echo admin_url('leads/'); ?>">Leads</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('leads/dashboard/' . $lid); ?>"><?php echo($lname); ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('venues') . '?lid=' . $lid; ?>">Venues</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('venues/view/') . $venueid; ?>"><?php echo get_vanue_data($venueid)->venuename; ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } elseif (isset($pid)) { ?>
                            <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('leads/dashboard/' . $pid); ?>"><?php echo($lname); ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('venues') . '?pid=' . $pid; ?>">Venues</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('venues/view/') . $venueid; ?>"><?php echo get_vanue_data($venueid)->venuename; ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } else { ?>
                            <a href="<?php echo admin_url('venues'); ?>">Venues</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('venues/view/') . $vnuid; ?>"><?php echo get_vanue_data($vnuid)->venuename; ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <?php if (isset($locid)) { ?>
                                <a href="<?php echo admin_url('venues/onsitelocview/' . $locid . '?venue=' . $vnuid); ?>">
                                    <?php echo get_venueloc_data($locid)->locname; ?>
                                </a>
                                <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <?php } ?>
                            <span>New Contact</span>
                        <?php } ?>
                    <?php } else { ?>
                        <?php if (isset($lid)) { ?>
                            <a href="<?php echo admin_url('leads/'); ?>">Leads</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('leads/dashboard/' . $lid); ?>"><?php echo($lname); ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('addressbooks') . '?lid=' . $lid; ?>">Contacts</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } elseif (isset($pid)) {
                            ?>
                            <a href="<?php echo admin_url('projects/'); ?>">Projects</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('projects/dashboard/' . $pid); ?>"><?php echo($lname); ?></a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                            <a href="<?php echo admin_url('addressbooks') . '?pid=' . $pid; ?>">Contacts</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } else { ?>
                            <a href="<?php echo admin_url('addressbooks'); ?>">Contacts</a>
                            <i class="fa fa-angle-right breadcrumb-arrow"></i>
                        <?php } ?>
                        <span><?php echo _l('new_contact'); ?></span>
                    <?php } ?>

                </div>
                <h1 class="pageTitleH1"><i class="fa fa-address-book-o"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="multiplecontacts">
                    <?php $this->load->view('admin/addressbooks/newform'); ?>
                </div>
                <!--<a id="addnewcontact" href="javascript:void(0)" class="btn btn-default"><i class="fa fa-plus"></i>Add
                    New Contact</a>-->
                <div class="topButton">
                    <button class="btn btn-default" type="button"
                            onclick="fncancel();"><?php echo _l('Cancel'); ?></button>
                    <button type="submit" class="btn btn-info save_contact"><?php echo _l('submit'); ?></button>
                </div>
            </div>
            <?php if (isset($lid)) { ?>
                <input type="hidden" name="hdnlid" value="<?php echo $lid; ?>">
            <?php } elseif (isset($pid)) { ?>
                <input type="hidden" name="hdnpid" value="<?php echo $pid; ?>">
            <?php } elseif (isset($eid)) { ?>
                <input type="hidden" name="hdneid" value="<?php echo $eid; ?>">
            <?php } elseif (isset($venueid)) { ?>
                <input type="hidden" name="hdnvenueid" value="<?php echo $venueid; ?>">
            <?php } elseif (isset($locid)) { ?>
                <input type="hidden" name="hdnlocid" value="<?php echo isset($locid) ? $locid : ''; ?>">
                <input type="hidden" name="hdnvid" value="<?php echo isset($vid) ? $vid : ''; ?>">
            <?php } ?>
            <?php echo form_close(); ?>

        </div>
    </div>
</div>
<?php
if (isset($lid)) {
    $rellink = "?lid=" . $lid;
} elseif (isset($pid)) {
    $rellink = "?pid=" . $pid;
} elseif (isset($venueid)) {
    $rellink = "?venue=" . $venueid;
} elseif (isset($locid)) {
    $rellink = "?locid=" . $locid . "&vid=" . $vid;
} else {
    $rellink = "";
} ?>
<?php init_tail(); ?>
<script>
    function fncancel() {
        var id =<?php if (isset($lid)) {
            echo $lid;
        } else {
            echo '0';
        }  ?>;
        var pid =<?php if (isset($pid)) {
            echo $pid;
        } else {
            echo '0';
        }  ?>;
        var eid =<?php if (isset($eid)) {
            echo $eid;
        } else {
            echo '0';
        }  ?>;
        var venueid =<?php if (isset($venueid)) {
            echo $venueid;
        } else {
            echo '0';
        }  ?>;
        var locid =<?php if (isset($locid)) {
            echo $locid;
        } else {
            echo '0';
        }  ?>;
        if (id > '0') {
            location.href = '<?php echo base_url(); ?>admin/addressbooks?lid=' + id;
        } else if (pid > '0') {
            location.href = '<?php echo base_url(); ?>admin/addressbooks?pid=' + pid;
        } else if (eid > '0') {
            location.href = '<?php echo base_url(); ?>admin/addressbooks?eid=' + eid;
        } else if (locid > '0') {
            var vid =<?php if (isset($vid)) {
                echo $vid;
            } else {
                echo '0';
            }  ?>;
            location.href = '<?php echo base_url(); ?>admin/venues/onsitelocview/' + locid + '?venue=' + vid;
        } else if (venueid > '0') {
            location.href = '<?php echo base_url(); ?>admin/venues/view/' + venueid;
        } else {
            location.href = '<?php echo base_url(); ?>admin/addressbooks';
        }
    }
</script>
<script>
    _validate_form($('.addressbook-form'), {
        "contact[0][firstname]": 'required',
        "contact[0][lastname]": 'required',
        "contact[0][companyname]": {
            required: {
                depends: function (element) {
                    return ($('input[name="contact[0][company]"]').val() == '1') ? true : false
                }
            }
        },
        "contact[0][companytitle]": {
            required: {
                depends: function (element) {
                    return ($('input[name="contact[0][company]"]').val() == '1') ? true : false
                }
            }
        },
        /*'contact[0][tags][]': 'required'*/
    });

    var createRequiredValidation = function () {

        $(".required.form-control").each(function (index, value) {
            $(this).rules('remove');
            $(this).rules('add', {
                required: true,
            });
        });
    }
    createRequiredValidation();
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
    $('body').on('click', '.company', function () {
        var index = $(this).data('index');
        showcompany(index);
    });

    //$(".addressdetails").hide()
    //$(".removeadd-0").hide();
    $('.custom_address').on('click', function () {
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

    $("#rel_type").on('change', function () {
        var selected = $(this).val();
        if (selected == "lead") {
            $(".lead-search").removeClass("hide");
            $(".project-search").addClass("hide");
            $(".event-search").addClass("hide");
        } else if (selected == "project") {
            $(".project-search").removeClass("hide");
            $(".lead-search").addClass("hide");
            $(".event-search").addClass("hide");
        } else if (selected == "event") {
            $(".event-search").removeClass("hide");
            $(".lead-search").addClass("hide");
            $(".project-search").addClass("hide");
        }
    });

    function showcompany(index = 0) {
        if ($('#contact_' + index + '_company').is(":checked"))
            $("#companydetails_" + index).show();
        else
            $("#companydetails_" + index).hide();
    }


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
        newemailIn += '<div class="col-sm-8 col-xs-10 multiemail"><div class="form-group"><label class="control-label" for="email[' + emailnext + '][email]"><small class="req text-danger">* </small>Email</label><input id="contact[' + index + '][email][' + emailnext + '][email]" class="form-control" name="contact[' + index + '][email][' + emailnext + '][email]" autocomplete="off" value="" type="email"></div>';
        newemailIn += '</div>';
        newemailIn += '<div class="col-sm-1 col-xs-2"><button id="emailremove-' + (emailnext) + '" class="email-remove-me" data-index=' + index + '><i class="fa fa-trash-o"></i></button></div></div>';
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
        newphoneIn += '<div class="col-sm-6 col-xs-7 multiphone"><div class="form-group"><label class="control-label" for="phone[' + phonenext + '][phone]">Phone</label><input id="contact[' + index + '][phone][' + phonenext + '][phone]" class="form-control" name="contact[' + index + '][phone][' + phonenext + '][phone]" autocomplete="off" value="" type="text"></div>';
        newphoneIn += '</div>';
        newphoneIn += '<div class="col-sm-2 col-xs-4 multiext"><div class="form-group"><label class="control-label" for="phone[' + phonenext + '][ext]">Ext</label><input id="contact[' + index + '][phone][' + phonenext + '][ext]" class="form-control" name="contact[' + index + '][phone][' + phonenext + '][ext]" autocomplete="off" maxlength=5 value="" type="tel"></div>';
        newphoneIn += '</div>';
        newphoneIn += '<div class="col-sm-1 col-xs-1"><button id="phoneremove-' + (phonenext) + '" class=" phone-remove-me" data-index=' + index + '><i class="fa fa-trash-o"></i></button></div></div>';
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
        $('.selectpicker').selectpicker('render');
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
        newwebsiteIn += '<div class="col-sm-8  col-xs-10"><div class="form-group"><label class="control-label" for="website[' + websitenext + '][url]">Address</label><input id="contact[' + index + '][website][' + websitenext + '][url]" class="form-control" name="contact[' + index + '][website][' + websitenext + '][url]" autocomplete="off" value="" type="text"></div>';
        newwebsiteIn += '</div>';
        newwebsiteIn += '<div class="col-sm-1  col-xs-2"><button id="websiteremove-' + (websitenext) + '" class="website-remove-me" data-index=' + index + ' ><i class="fa fa-trash-o"></i></button></div></div>';
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
        newaddressIn += ' <div class="contactaddress" id="address-' + addressnext + '" class="col-sm-12"><div class="row"><div class="col-sm-3"><div class="form-group"><label for="contact[' + index + '][address][' + addressnext + '][type]" class="control-label">Type</label><select name="contact[' + index + '][address][' + addressnext + '][type]" id="contact[' + index + '][address][' + addressnext + '][type]" class="form-control selectpicker" data-none-selected-text="Select">';
        $.each(address_type, function (aindex, avalue) {
            newaddressIn += '<option value="' + aindex + '">' + avalue + '</option>';
        });

        newaddressIn += '</select></div></div><div class="col-sm-8 col-xs-11"><div class="row"><div class="col-sm-8"><div id="locationField" class="form-group"><label class="control-label" for="address">Address</label><input id="contact_' +  index + '_autocomplete' + addressnext + '" class="form-control searchmap" data-addmap="' + addressnext + '" placeholder="Search Google Maps..." onfocus="geolocate()" type="text" data-index="' + index + '"></div></div><div class="col-sm-4"><div class="customadd-btn"><div class="form-group"><button type="button" class="btn btn-info custom_address customadd-' + addressnext + '" data-addressid="' + addressnext + '" data-index="' + index + '">Custom</button></div></div></div></div></div><div class="col-sm-1 col-xs-1"><button id="addressremove-' + (addressnext) + '" class=" address-remove-me"><i class="fa fa-trash-o"></i></button></div></div>';
        newaddressIn += ' <div id="customaddress-' + addressnext + '" class="addressdetails customaddress-' + addressnext + '" style="display:none"><div class="row"><div class="col-sm-11"><div class="form-group"><label for="contact[' + index + '][address][' + addressnext + '][street_number]" class="control-label">Address1</label><input id="contact[' + index + '][address][' + addressnext + '][route]" name="contact[' + index + '][address][' + addressnext + '][route]" class="form-control" value="" type="text"></div></div><div class="col-xs-1"><div data-id="#customaddress-' + addressnext + '" class="exp_clps_address"><a href="javascript:void(0)"><i class="fa fa-caret-up"></i></a></div></div></div><div class="address_extra"><div class="row"><div class="col-sm-11"><div class="form-group"><label for="contact[' + index + '][address][' + addressnext + '][street_number]" class="control-label">Address2</label><input id="contact[' + index + '][address][' + addressnext + '][street_number]" name="contact[' + index + '][address][' + addressnext + '][street_number]" class="form-control" value="" type="text"></div></div><div class="col-sm-6"><div class="form-group"><label for="contact[' + index + '][address][' + addressnext + '][locality]" class="control-label">City</label><input id="contact[' + index + '][address][' + addressnext + '][locality]" name="contact[' + index + '][address][' + addressnext + '][locality]" class="form-control" value="" type="text"></div></div><div class="col-sm-5"><div class="form-group"><label for="contact[' + index + '][address][' + addressnext + '][administrative_area_level_1]" class="control-label">State</label><input id="contact[' + index + '][address][' + addressnext + '][administrative_area_level_1]" name="contact[' + index + '][address][' + addressnext + '][administrative_area_level_1]" class="form-control" value="" type="text"></div></div></div><div class="row"><div class="col-sm-6"><div class="form-group"><label for="contact[' + index + '][address][' + addressnext + '][postal_code]" class="control-label">Zip Code</label><input id="contact[' + index + '][address][' + addressnext + '][postal_code]" name="contact[' + index + '][address][' + addressnext + '][postal_code]" class="form-control" value="" type="text"></div></div><div class="col-sm-5"><div class="form-group"><label for="contact[' + index + '][address][' + addressnext + '][country]" class="control-label">Country</label><select name="contact[' + index + '][address][' + addressnext + '][country]" id="contact[' + index + '][address][' + addressnext + '][country]" class="form-control selectpicker" data-none-selected-text="Select" ><option value="US" selected="">United States</option></select></div></div></div></div></div>';

        newaddressIn += '</div></div>';
        var newaddressInput = $(newaddressIn);

        // var removeaddressButton = $(removeaddressBtn);
        $(addtoaddress).after(newaddressInput);

        //$(addRemoveaddress).after(removeaddressButton);
        $("#address-" + addressnext).attr('data-source', $(addtoaddress).attr('data-source'));
        $("#count").val(addressnext);
        $(".removeadd-" + addressnext).hide();
        $('.custom_address').on('click', function () {
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

    // function initAutocomplete() {
    //     // Create the autocomplete object, restricting the search to geographical
    //     // location types.
    //    autocomplete = new google.maps.places.Autocomplete(
    //         /** @type {!HTMLInputElement} */(document.getElementById('autocomplete0')),
    //         {types: ['geocode'],  componentRestrictions: {country: 'us'}});

    //     // When the user selects an address from the dropdown, populate the address
    //     // fields in the form.
    //     autocomplete.addListener('place_changed', fillInAddress);
    // }

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

    // function fillInAddress() {
    //   // Get the place details from the autocomplete object.
    //   var place = autocomplete.getPlace();

    //   for (var component in componentForm) {
    //     document.getElementById("address[0]["+component+"]").value = '';
    //     document.getElementById("address[0]["+component+"]").disabled = false;
    //   }

    //   // Get each component of the address from the place details
    //   // and fill the corresponding field on the form.
    //   for (var i = 0; i < place.address_components.length; i++) {
    //     var addressType = place.address_components[i].types[0];
    //     if (componentForm[addressType]) {
    //       var val = place.address_components[i][componentForm[addressType]];
    //       document.getElementById("address[0]["+addressType+"]").value = val;
    //     }
    //   }
    //   $(".customaddress-0").show();
    //   $(".custom_address").hide();
    //   $(".remove_address").show();
    // }

    // Bias the autocomplete object to the user's geographical location,
    // as supplied by the browser's 'navigator.geolocation' object.
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

    $('body').on('click', '#addnewcontact', function (e) {
        var index = $('.multiplecontacts .contact').length;
        var name = $('input.contact_' + (index - 1) + '_firstname').val();
        var temp_data = {'index': index};
        $.ajax({
            type: 'POST',
            url: admin_url + 'addressbooks/addnewcontact<?php echo $rellink; ?>',
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
                createRequiredValidation();
            }
        });
    });
    $('body').on('click', '.contactheader.active', function (e) {
        var index = $(this).data('index');
        $('.contactheader').addClass('active');
        $(this).removeClass('active');
        $('.contact #contactinner').slideUp();
        $('#contact_' + index + ' #contactinner').slideDown();
    });
    $('body').on('change', '.contact_firstname', function (e) {
        var index = $(this).data('index');
        $('#contactheader_' + (index) + " span").text($(this).val());

    });
    $(function () {
        $('#profile-cropper0').croppie({
            viewport: {width: 180, height: 180, type: 'circle'},
            boundary: {width: 180, height: 180},
        });
    });
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB-0SSogvGqWSro2pyjAlek2DP_lwfQMvE&libraries=places"></script>

</body>
</html>


<div class="location_fields new">
    <div class="form-group">
        <label class="control-label" for="location_name">
            <small class="req text-danger">*</small>
            Location Name</label>
        <input id="new_location_name" type="text" name="location_name" class="form-control">
    </div>
    <div class="form-group">
        <label for="loc_autocomplete">Address Search</label>
        <input id="loc_autocomplete_new" class="form-control searchmap" data-addmap="0"
               placeholder="Search Google Maps..." onfocus="geolocate()" type="text">
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label for="newlocation[street_number]" class="control-label">Address</label>
                <input type="text" id="newlocation[street_number]" name="newlocation[street_number]"
                       class="form-control" value="">
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group"><label for="newlocation[route]" class="control-label">Address2</label><input
                        type="text" id="newlocation[route]" name="newlocation[route]" class="form-control" value="">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group"><label for="newlocation[locality]" class="control-label">City</label><input
                        type="text" id="newlocation[locality]" name="newlocation[locality]" class="form-control"
                        value=""></div>
        </div>
        <div class="col-sm-6">
            <div class="form-group"><label for="newlocation[administrative_area_level_1]"
                                           class="control-label">State</label><input type="text"
                                                                                     id="newlocation[administrative_area_level_1]"
                                                                                     name="newlocation[administrative_area_level_1]"
                                                                                     class="form-control" value="">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group"><label for="newlocation[postal_code]" class="control-label">Zip Code</label><input
                        type="text" id="newlocation[postal_code]" name="newlocation[postal_code]" class="form-control"
                        value=""></div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label for="newlocation[country]" class="control-label">Country</label>
                <div class="btn-group bootstrap-select disabled form-control">
                    <button type="button" class="btn dropdown-toggle disabled btn-default" data-toggle="dropdown"
                            role="button" data-id="newlocation[country]" tabindex="-1" aria-disabled="true"
                            title="United States"><span class="filter-option pull-left">United States</span>&nbsp;<span
                                class="bs-caret"><span class="caret"></span></span></button>
                    <div class="dropdown-menu open" role="combobox">
                        <ul class="dropdown-menu inner" role="listbox" aria-expanded="false">
                            <li data-original-index="0" class="selected"><a tabindex="0" class="" data-tokens="null"
                                                                            role="option" aria-disabled="false"
                                                                            aria-selected="true"><span class="text">United States</span><span
                                            class="glyphicon glyphicon-ok check-mark"></span></a></li>
                        </ul>
                    </div>
                    <select name="newlocation[country]" id="newlocation[country]" class="form-control selectpicker"
                            data-none-selected-text="Select" tabindex="-98">
                        <option value="US" selected="">United States</option>
                    </select></div>
            </div>
        </div>
    </div>
</div>