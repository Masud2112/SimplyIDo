<?php
/**
 * Added By: Vaidehi
 * Dt: 10/03/2017
 * for handling client/account registration
 */
$this->load->view('authentication/includes/head.php');
?>
<body>
<div id="wrapper" class="row wrapper multi-step-signup">
    <div class="company-logo">
        <?php get_company_logo(); ?>
    </div>
    <!-- /step-tabs -->
    <!-- Register Form -->
    <div class="col-lg-12 login-center mx-auto">
        <span id="errormsg" class="parsley-required"></span>
        <form id="signup-form" class="multi-step-form" data-parsley-validate novalidate method="post"
              action="<?php echo base_url(); ?>saveclient">
            <!-- 3 Step Navigation -->
            <ul id="progressbar">
                <li class="active" id="signup-step-1">Account Setup</li>
                <li id="signup-step-2">Create Brand</li>
                <li id="signup-step-3">Payment Details</li>
            </ul>
            <input type="hidden" name="page" id="page" value="signup">
            <fieldset id="signup-step-1" class="form-material active current animated fadeInRight">
                <h6 class="text-uppercase">Register Now</h6>
                <p class="text-muted">Create your account for free and enjoy.</p>
                <div class="cell form-group" data-name="firstname">
                    <label>First Name</label>
                    <input class="form-control" type="text" name="firstname" id="firstname" placeholder="First Name"
                           required data-parsley-required-message="Please enter valid first name">
                </div>
                <div class="cell form-group" data-name="lastname">
                    <label>Last Name</label>
                    <input class="form-control" type="text" name="lastname" id="lastname" placeholder="Last Name"
                           required data-parsley-required-message="Please enter valid last name">
                </div>
                <div class="cell form-group" data-name="useremail">
                    <label>Email</label>
                    <input class="form-control" type="email" placeholder="Email" name="useremail" id="useremail"
                           data-parsley-type="email" required
                           data-parsley-required-message="Please enter valid email address">
                    <span id="emailmsg" class="parsley-required"></span>
                </div>
                <div class="cell form-group" data-name="passwd">
                    <label>Password</label>
                    <input class="form-control" type="password" name="passwd" id="passwd" minlength="6"
                           placeholder="Password" required
                           data-parsley-required-message="Password should be minimum of 6 characters"
                           data-parsley-minlength-message="Password must be at least 6 characters long">
                </div>
                <div class="cell form-group" data-name="cpasswd">
                    <label>Confirm Password</label>
                    <input class="form-control" type="password" placeholder="Confirm Password" name="cpasswd"
                           id="cpasswd" required data-parsley-equalto="#passwd" minlength="6"
                           data-parsley-required-message="Confirm password should be minimum of 6 characters"
                           data-parsley-minlength-message="Confirm Password must be at least 6 characters long"
                           data-parsley-equalto-message="Confirm Password should match with Password">
                </div>
                <div class="cell form-group mb-3" data-name="terms">
                    <div class="checkbox checkbox-info">
                        <input type="checkbox" name="terms[]" required data-parsley-mincheck="1"
                               data-parsley-required-message="Please agree to terms and conditions" checked="checked">
                        <label><span class="label-text">I agree to all </label><a href="#">Terms &amp;
                            Conditions</a></span>
                    </div>
                </div>
                <button class="btn btn-info btn-block ripple next-btn" id="account-next">Next</button>
            </fieldset>
            <fieldset id="signup-step-2" class="form-material animated fadeInRight">
                <h6 class="text-uppercase"><?php echo _l('create_brand'); ?></h6>
                <p class="text-muted"><?php echo _l('brand_heading'); ?></p>
                <div class="form-group" data-name="brandname">
                    <label for="brand-name" class="control-label"><?php echo _l('register_brand_name'); ?></label>
                    <input class="form-control" type="text" placeholder="<?php echo _l('register_brand_name'); ?>"
                           name="brandname" id="brandname" required
                           data-parsley-required-message="<?php echo _l('brand_name_required'); ?>">
                    <span id="brandmsg" class="parsley-required"></span>
                </div>
                <div class="form-group" data-name="brandtype">
                    <label for="brandtype" class="control-label"><?php echo _l('register_brandtype'); ?></label>
                    <select class="form-control" data-placeholder="<?php echo _l('register_brandtype'); ?>"
                            name="brandtype" id="brandtype" required
                            data-parsley-required-message="<?php echo _l('brandtype_required'); ?>">
                        <option value="">Please Select</option>
                        <?php
                        foreach ($brandtypes as $brandtype) {
                            ?>
                            <option value="<?php echo $brandtype['brandtypeid']; ?>"><?php echo $brandtype['name']; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group" data-name="packagetype">
                    <label for="package" class="control-label"><?php echo _l('register_package'); ?></label>
                    <select class="form-control packagetype_js" data-placeholder="<?php echo _l('register_package'); ?>"
                            name="packagetype" id="packagetype" required
                            data-parsley-required-message="<?php echo _l('package_required'); ?>">
                        <option value="">Please Select</option>
                        <?php
                        foreach ($packages as $package) {
                            ?>
                            <option value="<?php echo $package['packageid']; ?>" <?php echo($package['packageid'] == $packagetype ? "selected" : ""); ?>
                                    data-price="<?php echo $package['price']; ?>"><?php echo $package['name']; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group" data-name="address1" data-parsley-required-message="Please enter valid address">
                    <label for="address1" class="control-label"><?php echo _l('register_address1'); ?></label>
                    <input class="form-control" type="text" placeholder="<?php echo _l('register_address1'); ?>"
                           name="address1" id="address1" required
                           data-parsley-required-message="<?php echo _l('address_required'); ?>">
                </div>
                <div class="form-group" data-name="address2">
                    <label for="address2" class="control-label"><?php echo _l('register_address2'); ?></label>
                    <input class="form-control" type="text" placeholder="<?php echo _l('register_address2'); ?>"
                           name="address2" id="address2">
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" data-name="city"
                             data-parsley-required-message="Please enter valid city">
                            <label for="city" class="control-label"><?php echo _l('register_city'); ?></label>
                            <input class="form-control" type="text" placeholder="<?php echo _l('register_city'); ?>"
                                   name="city" id="city" required
                                   data-parsley-required-message="<?php echo _l('city_required'); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" data-name="state"
                             data-parsley-required-message="Please enter valid state">
                            <label for="state" class="control-label"><?php echo _l('register_state'); ?></label>
                            <input class="form-control" type="text" placeholder="<?php echo _l('register_state'); ?>"
                                   name="state" id="state" required
                                   data-parsley-required-message="<?php echo _l('state_required'); ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group" data-name="zipcode"
                             data-parsley-required-message="Please enter valid zipcode">
                            <label for="zip-code" class="control-label"><?php echo _l('register_zipcode'); ?></label>
                            <input class="form-control" type="text" placeholder="<?php echo _l('register_zipcode'); ?>"
                                   name="zipcode" id="zipcode" required
                                   data-parsley-required-message="<?php echo _l('zipcode_required'); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" data-name="country">
                            <label for="country" class="control-label"><?php echo _l('register_country'); ?></label>
                            <input class="form-control" type="text" name="country" id="country" value="United States"
                                   readonly required
                                   data-parsley-required-message="<?php echo _l('country_required'); ?>">
                        </div>
                    </div>
                </div>
                <button class="btn btn-info btn-block ripple prev-btn" id="account-prev">Previous</button>
                <button class="btn btn-info btn-block ripple next-btn" id="payment-next">Next</button>
                 <button class="btn btn-info btn-block ripple hide" type="submit" id="frm-account-submit">Sign Up</button>
            </fieldset>
            <fieldset id="signup-step-3" class="form-material animated fadeInRight">
                <h6 class="text-uppercase"><?php echo _l('make_payment'); ?></h6>
                <!--<p class="text-muted"><?php /*echo _l('choose_your_payment_mode'); */?></p>-->
                <div class="row">
                    <div class="col-sm-12 text-center paymentMethodStripe">
                         <label for="payment_method_stripe"><input type="radio" name="payment_method[]" value="stripe" id="payment_method_stripe" checked required data-parsley-required-message="<?php echo _l('payment_method_required'); ?>" class="hide">
                       <i class="fa fa-cc-stripe fa-5x" aria-hidden="true"></i></label>
                    </div>
                    <!-- <div class="col-sm-6 text-center">
                        <label for="paypal"><i class="fa fa-cc-paypal fa-5x" aria-hidden="true"></i></label>
                        <input type="radio" name="payment_method[]" value="paypal" id="payment_method_paypal" required
                               data-parsley-required-message="<?php echo _l('payment_method_required'); ?>">
                    </div> -->
                </div>
                <button class="btn btn-primary btn-block ripple prev-btn">Previous</button>
                <button class="btn btn-info btn-block ripple" type="submit" id="frm-submit">Sign Up</button>
            </fieldset>
        </form>
    </div>
    <!-- /.login-center -->
</div>
<div class="msg-box hidden"></div>
<!-- /.body-container -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.8.0/parsley.js"></script>
<script src='http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/jquery-ui.min.js'></script>
<script type="text/javascript">
    $(function () {
        //ajax call to check whether email exists or not
        // $('.packagetype_js').on('change', function(){
        //     alert( $(this).attr("data-id"));
        // });


        $("#useremail").change(function () {
            var useremail = $(this).val();
            $.ajax({
                url: "<?php echo base_url();?>emailexists",
                method: "post",
                data: "useremail=" + useremail,
                success: function (data) {
                    if (data == 1) {
                        $(':input[type="submit"]').prop('disabled', false);
                        $('#account-next').prop('disabled', false);
                        $("#emailmsg").html("");
                    } else {
                        $(':input[type="submit"]').prop('disabled', true);
                        $('#account-next').prop('disabled', true);
                        $("#emailmsg").html("Email already exists");
                    }
                }
            });
        });

        var $sections = $('.form-material');

        function navigateTo(index) {
            // Mark the current section with the class 'current'
            $sections
                .removeClass('current')
                .eq(index)
                .addClass('current');
        }

        function curIndex() {
            // Return the current index by looking at which section has the class 'current'
            return $sections.index($sections.filter('.current'));
        }

        // Prepare sections by setting the `data-parsley-group` attribute to 'block-0', 'block-1', etc.
        $sections.each(function (index, section) {
            $(section).find(':input').attr('data-parsley-group', 'block-' + index);
        });

        //jQuery time
        var current_fs, next_fs, previous_fs; //fieldsets
        var left, opacity, scale; //fieldset properties which we will animate
        var animating; //flag to prevent quick multi-click glitches

        $(".next-btn").on('click', function (e) {
            e.preventDefault();
            var nextfieldset = $(this);
            $('.multi-step-form').parsley().whenValidate({
                group: 'block-' + curIndex()
            }).done(function () {
                navigateTo(curIndex() + 1);
                var package_amount = $('#packagetype option:selected').attr('data-price');

                if (animating) return false;
                animating = true;

                current_fs = nextfieldset.parent();
                next_fs = nextfieldset.parent().next();

                //activate next step on progressbar using the index of next_fs
                $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

                //show the next fieldset
                next_fs.show();
                //hide the current fieldset with style
                current_fs.animate({opacity: 0}, {
                    step: function (now, mx) {
                        //as the opacity of current_fs reduces to 0 - stored in "now"
                        //1. scale current_fs down to 80%
                        scale = 1 - (1 - now) * 0.2;
                        //2. bring next_fs from the right(50%)
                        left = (now * 50) + "%";
                        //3. increase opacity of next_fs to 1 as it moves in
                        opacity = 1 - now;
                        current_fs.css({
                           // 'transform': 'scale(' + scale + ')',
                            'position': 'absolute',
                            'width': '100%',

                        });
                        next_fs.css({'left': left, 'opacity': opacity});
                    },
                    duration: 800,
                    complete: function () {
                        current_fs.hide();
                        animating = false;
                    },
                    //this comes from the custom easing plugin
                    easing: 'easeInOutBack'
                });

                // if (package_amount <= 0) {
                //     $('#payment-next').html('Sign Up');
                //     $('#payment-next').attr('type', 'submit');
                // }
            });
        });

        $(".prev-btn").on('click', function (e) {
            e.preventDefault();
            if (animating) return false;
            animating = true;

            current_fs = $(this).parent();
            previous_fs = $(this).parent().prev();

            //de-activate current step on progressbar
            $("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

            //show the previous fieldset
            previous_fs.show();
            //hide the current fieldset with style
            current_fs.animate({opacity: 0}, {
                step: function (now, mx) {
                    //as the opacity of current_fs reduces to 0 - stored in "now"
                    //1. scale previous_fs from 80% to 100%
                    scale = 0.8 + (1 - now) * 0.2;
                    //2. take current_fs to the right(50%) - from 0%
                    left = ((1 - now) * 50) + "%";
                    //3. increase opacity of previous_fs to 1 as it moves in
                    opacity = 1 - now;
                    current_fs.css({'left': left});
                    previous_fs.css({
                        //'transform': 'scale(' + scale + ')', 
                        'opacity': opacity, 
                        'position': 'relative'});
                },
                duration: 800,
                complete: function () {
                    current_fs.hide();
                    animating = false;
                },
                //this comes from the custom easing plugin
                easing: 'easeInOutBack'
            });
            navigateTo(curIndex() - 1);
        });

        //ajax call to check whether brand name exists or not
        $("#brandname").blur(function () {
            var brandname = $(this).val();
            $.ajax({
                url: "<?php echo base_url();?>brandexists",
                method: "post",
                data: "brandname=" + brandname,
                success: function (data) {
                    if (data == 1) {
                        $(':input[type="submit"]').prop('disabled', false);
                    } else {
                        $(':input[type="submit"]').prop('disabled', true);
                        $("#brandmsg").html("Brand name already exists");
                    }
                }
            });
        });

        /**
         * Added By : Vaidehi
         * Dt : 11/09/2017
         * ajax call to get number of brands created by user
         */
        $("#packagetype").change(function () {
            var package_amount = $('#packagetype option:selected').attr('data-price');
            if (package_amount > 0) {
                // $('#payment-next').show();
                // $('#frm-account-submit').removeAttr('type','submit');
                // $('#frm-account-submit').addClass('hide');
                // $('#frm-account-submit').hide();

                /*$('#payment-next').html('Next');
                $('#payment-next').removeAttr('type', 'submit');
                $('input[name="payment_method[]"]')
                    .attr('data-parsley-required', 'true')
                    .parsley();*/
                $('#payment-next').removeClass('hide');
                $('#frm-account-submit').addClass('hide');
            } else {
                /*$('#frm-account-submit').attr('type', 'submit');

                $('input[name="payment_method[]"]')
                    .removeAttr('data-parsley-required')
                    .parsley().destroy();
                $('#payment-next').html('Sign Up');
                $('#payment-next').attr('type', 'submit');*/
                $('#payment-next').addClass('hide');
                $('#frm-account-submit').removeClass('hide');
            }

            var packagetype = $(this).val();
            var useremail = $('#useremail').val();
            var brandname = $('#brandname').val();

            if (packagetype != '' && useremail != '' && brandname != '') {
                $.ajax({
                    url: "<?php echo base_url();?>activebrands",
                    method: "post",
                    data: "packagetype=" + packagetype + "&useremail=" + useremail + '&brandname=' + brandname,
                    success: function (data) {
                        if (data == "success") {
                            $(':input[type="submit"]').prop('disabled', false);
                        } else {
                            $(':input[type="submit"]').prop('disabled', true);
                            $("#errormsg").html("No more brands can be created in your current plan. To create a new brand, you must either upgrade your service plan or deactivate an existing brand.");
                        }
                    }
                });
            } else {
                alert('Please select valid package and enter valid email address');
                return false;
            }
        });

        if ($("#packagetype").val() > 0) {
            var packagetype = $('#packagetype').val();
            var useremail = $('#useremail').val();
            var brandname = $('#brandname').val();

            if (packagetype != '' && useremail != '' && brandname != '') {
                $.ajax({
                    url: "<?php echo base_url();?>activebrands",
                    method: "post",
                    data: "packagetype=" + packagetype + "&useremail=" + useremail + '&brandname=' + brandname,
                    success: function (data) {
                        if (data == "success") {
                            $(':input[type="submit"]').prop('disabled', false);
                        } else {
                            $(':input[type="submit"]').prop('disabled', true);
                            $("#errormsg").html("No more brands can be created in your current plan. To create a new brand, you must either upgrade your service plan or deactivate an existing brand.");
                        }
                    }
                });
            } else {
                //alert('Please select valid package and enter valid email address');
                return false;
            }
        }
    });
</script>
</body>
</html>