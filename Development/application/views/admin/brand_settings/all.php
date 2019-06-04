<?php
/**
 * Added By : Vaidehi
 * Dt : 10/12/2017
 * For Brand Settings Module
 */
init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?php echo form_open_multipart($this->uri->uri_string() . '?group=' . $view_name, array('id' => 'settings-form')); ?>
        <div class="row">
            <?php if ($this->session->flashdata('debug')) { ?>
                <div class="col-lg-12">
                    <div class="alert alert-warning">
                        <?php echo $this->session->flashdata('debug'); ?>
                    </div>
                </div>
            <?php } ?>
            <div class="col-md-12">
                <div class="breadcrumb ">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span>Current Brand</span>
                </div>

                <h1 class="pageTitleH1"><i class="fa fa-cog"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
            </div>
            <div class="col-md-12">
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="optionInnerNavMain_blk">
                            <div class="settingDropDown"><span>General</span> <i class="fa fa-bars"></i></div>
                            <ul class="nav navbar-pills nav-tabs nav-stacked settingTabs">
                                <?php $settings_groups = array(
                                    array(
                                        'name' => 'general',
                                        'lang' => _l('settings_group_general'),
                                        'order' => 1,
                                    ),
                                    array(
                                        'name' => 'company',
                                        'lang' => _l('company_information'),
                                        'order' => 2,
                                    ),
                                    array(
                                        'name' => 'localization',
                                        'lang' => _l('settings_group_localization'),
                                        'order' => 3,
                                    ),
                                    array(
                                        'name' => 'email',
                                        'lang' => _l('settings_group_email'),
                                        'order' => 4,
                                    ),
                                    /*array(
                                     'name'=>'cronjob',
                                     'lang'=>_l('settings_group_cronjob'),
                                     'order'=>5,
                                     ),*/
                                    array(
                                        'name' => 'sales',
                                        'lang' => _l('settings_group_sales'),
                                        'order' => 5,
                                    ),
                                    array(
                                        'name' => 'clients',
                                        'lang' => _l('settings_group_clients'),
                                        'order' => 6,
                                    ),
                                    array(
                                        'name' => 'calendar',
                                        'lang' => _l('settings_calendar'),
                                        'order' => 7,
                                    ),
                                    array(
                                        'name' => 'online_payment_modes',
                                        'lang' => _l('settings_group_online_payment_modes'),
                                        'order' => 8,
                                    ),
                                    array(
                                        'name' => 'search_filter',
                                        'lang' => _l('settings_search_filter_tags'),
                                        'order' => 9,
                                    )
                                );

                                $settings_groups = do_action('settings_groups', $settings_groups);
                                usort($settings_groups, function ($a, $b) {
                                    return $a['order'] - $b['order'];
                                });
                                ?>
                                <?php
                                $i = 0;
                                foreach ($settings_groups as $group) {
                                    if ($group['name'] == 'update' && !is_admin()) {
                                        continue;
                                    }
                                    ?>
                                    <li<?php if ($i == 0) {
                                        echo " class='active'";
                                    } ?>>
                                        <a href="<?php echo(!isset($group['url']) ? admin_url('brand_settings?group=' . $group['name']) : $group['url']) ?>"
                                           data-group="<?php echo $group['name']; ?>">
                                            <?php echo $group['lang']; ?></a>
                                    </li>
                                    <?php $i++;
                                } ?>
                            </ul>
                            <?php if (isset($_GET['pg']) && $_GET['pg'] != "") { ?>
                                <input type="hidden" name="pg" value="<?php echo $_GET['pg']; ?>">
                            <?php } ?>
                            <div class="btn-bottom-toolbar text-right">
                                <button id= "saveSettings" type="button" class="btn btn-info"><?php echo _l('settings_save'); ?></button>
                            </div>
                        </div>
                        <div class="optionInnerPageMain_blk">
                            <div class="optionInnerPage_blk">
                                <?php do_action('before_settings_group_view', $group_view); ?>
                                <?php echo $group_view; ?>
                                <?php do_action('after_settings_group_view', $group_view); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--  <div class="col-md-9">
                 <div class="panel_s btmbrd">
                     <div class="panel-body">
                     </div>
                 </div>
             </div> -->
            <div class="clearfix"></div>
        </div>
        <?php echo form_close(); ?>
        <div class="btn-bottom-pusher"></div>
    </div>
</div>
<div id="new_version"></div>

<div class="modal fade" id="new_brand_type" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    Add New Service Type
                </h4>
            </div>
            <?php echo form_open('admin/misc/addservicetype', array('id' => 'service_type_form')); ?>
            <div class="modal-body">
                <div class="form-group">
                    <label for="brand_type_name"><?php echo _l('service_name'); ?></label>
                    <input type="text" name="brand_type" id="brand_type_name" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info p9" type="button" id=""><?php echo _l('save'); ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script type="text/javascript">
    function getURLParameter(name) {
        return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search) || [null, ''])[1].replace(/\+/g, '%20')) || null;
    }

    _validate_form($('#settings-form'), {'settings[companyname]': 'required', 'settings[filter_tags]': 'required'});

    $('.test_email').on('click', function () {
        var email_protocol = $('#email_protocol').val();
        var smtp_host = $('#smtp_host').val();
        var smtp_encryption = $('#smtp_encryption').val();
        var smtp_port = $('#smtp_port').val();
        var fromemail = $('input[name="settings[smtp_email]"]').val();
        var passwd = $('input[name="settings[smtp_password]"]').val();
        var email_signature = $('textarea[name="settings[email_signature]"]').val();
        var email = $('input[name="test_email"]').val();

        if (email != '') {
            $(this).attr('disabled', true);
            $.post(admin_url + 'emails/sent_smtp_test_email_settings', {
                email_protocol: email_protocol,
                smtp_encryption: smtp_encryption,
                smtp_host: smtp_host,
                smtp_port: smtp_port,
                smtp_email: fromemail,
                passwd: passwd,
                email_signature: email_signature,
                test_email: email
            }).done(function (data) {
                //console.log(data);
                var response = jQuery.parseJSON(data);
                if (response.success === 'Email settings are correctly set.') {
                    $(".btn-info").removeClass('disabled');
                    $('#email-setup').val(1);
                    $('form').bind('submit');
                } else {
                    $('#email-setup-error').html(response.failure);
                }
                //window.location.reload();
            });
        }
    });

    /**
     * Added By : Vaidehi
     * Dt : 11/17/2017
     * to provide error if brand name already exists
     */
    $("#saveSettings").click(function () {
        var activetab = $('ul.navbar-pills li.active a').html();
        var curURL = getURLParameter('group');

        if (activetab == 'General') {
            var brandname = $("input[name='settings[companyname]']").val();
            $.ajax({
                url: "<?php echo base_url();?>brandexists",
                method: "post",
                data: "brandname=" + brandname,
                success: function (data) {
                    if (data == "success") {
                        $('#settings-form').submit();
                    } else {
                        $("#brandmsg").html("Brand name already exists");
                    }
                }
            });
        } else {
            if (curURL == 'email' && $('#email-setup').val() != 1) {
                $(".btn-info").addClass('disabled');
                $('form').unbind('submit');
            } else {
                $('#settings-form').submit();
            }
        }
    });

    /**
     * Added By : Vaidehi
     * Dt : 11/28/2017
     * to email configurations based on protocol selected
     */
    $("#email_protocol").change(function () {
        if ($(this).val() == 'smtp') {
            //if smtp, hide gmail authorize button
            $('#div-smtp-encryption').removeClass('hide');
            $('#div-smtp-host').removeClass('hide');
            $('#div-smtp-port').removeClass('hide');
            $('#div-smtp-email').removeClass('hide');
            $('#div-smtp-password').removeClass('hide');
            //$('#authorize-button').removeClass('hide');

            //$('#div-smtp-email-signature').addClass('hide');
        } else {
            //set host based on protocol
            if ($(this).val() == 'yahoo') {
                $('#smtp_host').val('smtp.mail.yahoo.com');
            } else if ($(this).val() == 'aol') {
                $('#smtp_host').val('smtp.aol.com');
            } else if ($(this).val() == 'hotmail') {
                $('#smtp_host').val('smtp.live.com');
            } else if ($(this).val() == 'godaddy') {
                $('#smtp_host').val('smtpout.secureserver.net');
            } else if ($(this).val() == 'gmail') {
                $('smtp_host').val('smtp.gmail.com');
            }

            //if other, hide gmail authorize button, server and port option
            $('#div-smtp-host').addClass('hide');
            $('#div-smtp-port').addClass('hide');

            $('#div-smtp-encryption').removeClass('hide');
            $('#div-smtp-email').removeClass('hide');
            $('#div-smtp-password').removeClass('hide');
        }
    });

    $('#email-settings').addClass('hide');

    // if($('#gmail_email').val() == '') {
    //   $('#account-link').addClass('hide');
    // }

    /**
     * Added By : Vaidehi
     * Dt : 11/28/2017
     * set port and host based on email encryption method
     */
    $('#smtp_encryption').change(function () {
        var protocol = $('#email_protocol').val();

        //set port based on encryption method
        if ($(this).val() == 'tls') {
            if (protocol == 'gmail') {
                $('#smtp_port').val('587');
            } else {
                $('#smtp_port').val('25');
            }
        } else {
            $('#smtp_port').val('465');
        }
    });

    /**
     * Added By : Vaidehi
     * Dt : 11/28/2017
     *& get default selected protocol
     */
    var emailProtocol = $("#email_protocol").find(":selected").val();
    if (emailProtocol == 'smtp') {
        //if smtp, hide gmail authorize button
        $('#div-smtp-host').removeClass('hide');
        $('#div-smtp-port').removeClass('hide');
        $('#div-smtp-email').removeClass('hide');
        $('#div-smtp-password').removeClass('hide');
        $('#div-smtp-encryption').removeClass('hide');
        $('#smtp-button').addClass('hide');
    } else {
        //if other, hide gmail authorize button, server and port option
        $('#div-smtp-host').addClass('hide');
        $('#div-smtp-port').addClass('hide');
        $('#smtp-button').addClass('hide');

        $('#email-settings').removeClass('hide');
    }


    /**
     * Added By : Sanjay
     * Dt : 01/08/2018
     *& validate blank filter tags  on search filter tab
     */

    var curURL = getURLParameter('group');

    if (curURL == 'search_filter') {

        $('input:checkbox').change(function () {
            var numberOfChecked = $('input:checkbox:checked').length;
            //var totalCheckboxes = $('input:checkbox').length;
            var numberNotChecked = $('input:checkbox:not(":checked")').length;

            if (numberOfChecked <= 0) {
                $('.btn-info').attr('disabled', 'disabled');
            } else {
                $('.btn-info').removeAttr('disabled', 'disabled');
            }

        });
    }


    // $('#authorize-button').click(function(){
    //   $('#email_protocol').val('gmail');
    //   $('#email-settings').addClass('hide');
    //   handleClientLoad();
    // });

    // function fnUseSMTP(){
    //   $('#email-settings').removeClass('hide');
    // }
</script>

<script type="text/javascript">
    // Client ID and API key from the Developer Console
    // var CLIENT_ID = '602076240364-cgk60ro2odina5v921u5ok5mfes3hbbs.apps.googleusercontent.com';

    // Array of API discovery doc URLs for APIs used by the quickstart
    // var DISCOVERY_DOCS = ["https://www.googleapis.com/discovery/v1/apis/gmail/v1/rest"];

    // Authorization scopes required by the API; multiple scopes can be
    // included, separated by spaces.
    // var SCOPES = 'https://www.googleapis.com/auth/gmail.send';

    // var authorizeButton = document.getElementById('authorize-button');

    /**
     *  On load, called to load the auth2 library and API client library.
     */
    // function handleClientLoad() {
    //   gapi.load('client:auth2', initClient);
    // }

    /**
     *  Initializes the API client library and sets up sign-in state
     *  listeners.
     */
    // function initClient() {
    //   gapi.client.init({
    //     discoveryDocs: DISCOVERY_DOCS,
    //     clientId: CLIENT_ID,
    //     scope: SCOPES
    //   }).then(function () {
    //     // Listen for sign-in state changes.
    //     gapi.auth2.getAuthInstance().isSignedIn.listen(updateSigninStatus);

    //     // Handle the initial sign-in state.
    //     updateSigninStatus(gapi.auth2.getAuthInstance().isSignedIn.get());
    //     authorizeButton.onclick = handleAuthClick;
    //   });
    // }

    /**
     *  Called when the signed in status changes, to update the UI
     *  appropriately. After a sign-in, the API is called.
     */
    // function updateSigninStatus(isSignedIn) {
    //   if (isSignedIn) {
    //     getUserInformation();
    //     $('#authorize-button').addClass('hide');
    //   } else {
    //     $('#authorize-button').removeClass('hide');
    //   }
    // }

    /**
     *  Sign in the user upon button click.
     */
    // function handleAuthClick(event) {
    //   gapi.auth2.getAuthInstance().signIn();
    // }

    /**
     *  Sign out the user upon button click.
     */
    // function handleSignoutClick() {
    //   gapi.auth2.getAuthInstance().disconnect();
    // }

    /**
     * get gmail logged in user information
     */
    // function getUserInformation(event) {
    //   gapi.client.load('plus','v1', function(){
    //     var request = gapi.client.plus.people.get( {'userId' : 'me'} );
    //     request.execute( function(profile) {

    //       var email = profile['emails'].filter(function(v) {
    //         return v.type === 'account'; // Filter out the primary email
    //       })[0].value;

    //       var userId = profile.id;
    //       $('#gmail_email').val(email);
    //       $('#gmail_id').val(userId);

    //       $('#account-link').removeClass('hide');
    //       $('#authorize-button').removeClass('hide');

    //       $('#account').html($('#gmail_email').val());
    //       $('#authorize-button').addClass('hide');
    //     });
    //   });
    // }

    // $('#btn-remove').click(function(){
    //   $('#gmail_email').val('');
    //   $('#gmail_id').val('');
    //   $('#account-link').html('Account removed successfully');
    //   $('#authorize-button').removeClass('hide');
    //   handleSignoutClick();
    // });
    $(function () {
        $('.companyphone').mask("(999) 999-9999", {placeholder: "(___) ___-____"});
    });

    $(function () {
        $('.companyphoneext').mask("99999", {placeholder: "12345"});
    });

    $(function(){
        _validate_form($('#service_type_form'), {
            brand_type:{
                required:true,
                remote: {
                    url: admin_url + "misc/service_type_exists",
                    type: 'post',
                    data: {
                        itemid:function(){
                            return $('input[name="itemid"]').val();
                        }
                    }
                }
            },
        });
    });
</script>
<!--<script async defer src="https://apis.google.com/js/api.js"></script>-->
</body>
</html>