<?php
/**
 * Added By: Vaidehi
 * Dt: 10/03/2017
 * for handling client/account registration
 */
//$this->load->view('authentication/includes/head.php');
?>
<!--<body>-->
<div id="" class="row wrapper multi-step-signup">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="company-logo">
        <?php get_company_logo(); ?>
    </div>
    <!-- /step-tabs -->
    <!-- Register Form -->
    <div class="col-lg-12 login-center mx-auto">
        <span id="errormsg" class="parsley-required"></span>
        <form id="trial-form" class="multi-step-form" data-parsley-validate novalidate method="post"
              action="<?php echo base_url(); ?>savetrialaccount">
            <!-- 3 Step Navigation -->
            <ul id="progressbar">
                <li class="active" id="signup-step-1"></li>
                <li id="signup-step-2"></li>
                <li id="signup-step-3"></li>
            </ul>
            <input type="hidden" name="page" id="page" value="signup">
            <fieldset id="signup-step-1" class="signup_step  form-material active current animated fadeInRight">
                <h6 class="text-uppercase text-center">Sign up</h6>
                <div class="row">
                    <div class="cell form-group col-xs-6" data-name="firstname">
                        <div class="input-group mdiscoun-input-group">
                            <span class="input-group-addon"><i class="fa fa-user"></i></span>
                            <input class="form-control" type="text" name="firstname" id="firstname"
                                   placeholder="First Name" required
                                   data-parsley-required-message="Please enter valid first name"
                                   aria-describedby="basic-addon1">
                        </div>

                    </div>
                    <div class="cell form-group col-xs-6" data-name="lastname">
                        <input class="form-control" type="text" name="lastname" id="lastname" placeholder="Last Name"
                               required data-parsley-required-message="Please enter valid last name">
                    </div>
                </div>
                <div class="row">
                    <div class="cell form-group col-xs-12 " data-name="useremail">
                        <div class="input-group mdiscoun-input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                            <input class="form-control" type="email" placeholder="Email" name="useremail" id="useremail"
                                data-parsley-type="email" required
                                data-parsley-required-message="Please enter valid email address" autocomplete="off">
                            <span id="emailmsg" class="parsley-required"></span>
                        </div>
                    </div>
                    <div class="cell form-group col-xs-12" data-name="passwd">
                        <div class="input-group mdiscoun-input-group">
                            <span class="input-group-addon"><i class="fa fa-key"></i></span>
                            <input class="form-control" type="password" name="passwd" id="passwd" minlength="6"
                                placeholder="Password" required
                                data-parsley-required-message="Password should be minimum of 6 characters"
                                data-parsley-minlength-message="Password must be at least 6 characters long">
                        </div>
                    </div>
                    <div class="cell form-group col-xs-12" data-name="cpasswd">
                        <div class="input-group mdiscoun-input-group">
                            <span class="input-group-addon"><i class="fa fa-check"></i></span>
                            <input class="form-control" type="password" placeholder="Confirm Password" name="cpasswd"
                                id="cpasswd" required data-parsley-equalto="#passwd" minlength="6"
                                data-parsley-required-message="Confirm password should be minimum of 6 characters"
                                data-parsley-minlength-message="Confirm Password must be at least 6 characters long"
                                data-parsley-equalto-message="Confirm Password should match with Password">
                        </div>
                    </div>
                    <div class="cell form-group col-xs-12 mb-3" data-name="terms">
                        <div class="checkbox checkbox-info">
                            <input type="checkbox" name="terms[]" required data-parsley-mincheck="1"
                                data-parsley-required-message="Please agree to terms and conditions" checked="checked">
                            <label><span class="label-text">By signing up, I agree to the Privacy Policy and the Terms of Services</span></label>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <button class="btn btn-info btn-block ripple next-btn" id="account-next">Next</button>
                </div>
                <hr>
                <p class="text-center">Already have an account ? <a href="#" data-dismiss="modal">Login</a></p>
            </fieldset>
            <fieldset id="signup-step-2" class="signup_step form-material animated fadeInRight">
                <h6 class="text-uppercase text-center">Please select your service type(s)</h6>
                <div class="form-group" data-name="brandname">
                    <input class="form-control" type="text" placeholder="<?php echo _l('register_brand_name'); ?>"
                           name="brandname" id="brandname" required
                           data-parsley-required-message="<?php echo _l('brand_name_required'); ?>">
                    <span id="brandmsg" class="parsley-required"></span>
                </div>
                <div class="form-group brandtypeList_blk" data-name="brandtype">
                    <div class="row">
                    <?php foreach ($brandtypes as $brandtype) { ?>
                        <div class="checkbox col-sm-6 col-md-4 col-xs-12">
                            <input id ="<?php echo $brandtype['brandtypeid']; ?>" name="brandtype[]" type="checkbox" value="<?php echo $brandtype['brandtypeid']; ?>" class="checkbox" required data-parsley-required-message="<?php echo _l('Please select at least one checkbox'); ?>"/>
                            <label for="<?php echo $brandtype['brandtypeid']; ?>"><?php echo $brandtype['name']; ?></label></div>
                    <?php } ?>
                    <div class="checkbox col-sm-6 col-md-4 col-xs-12"><input id ="other" name="brandtype[]" type="checkbox" value="other" class="checkbox"/>
                        <label for="other" class="form-group"><input type="text" name="otherbrandval" placeholder="Other" class="form-control"></label></div>
                    </div>
                </div>
                <input type="hidden" name="packagetype" value="1"/>
                <div class="text-center">
                    <button class="btn btn-info ripple prev-btn hidden" id="account-prev">Previous</button>
                    <button class="btn btn-info ripple next-btn_js" type="submit" id="frm-account-submit">Complete Sign Up</button>
                    <button class="btn btn-info btn-block ripple next-btn hidden" id="thankyou-next">Next</button>
                </div>
            </fieldset>
            <fieldset id="signup-step-3" class="signup_step form-material animated fadeInRight text-center">
                <h2>Congratulations & Welcome!</h2>
                <h4>Your FREE Trial Account is ready.</h4>
                <p>You will recieve an email confirmation that your account has been set up. You may use the button below to access your Simply I Do dashboard. Enjoy! </p>
                <a href="<?php echo admin_url();?>" class="btn btn-info btn-block ripple" id="frm-submit">Let's Get Started </a>
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

                current_fs = nextfieldset.parents(".signup_step");
                next_fs = nextfieldset.parents(".signup_step").next();

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

        $(".next-btn_js").on('click', function (e) {

            var nextfieldset = $(this);

            $('.multi-step-form').parsley().whenValidate({
                group: 'block-' + curIndex()
            }).done(function () {
                navigateTo(curIndex() + 1);
                next_fs = nextfieldset.parents(".signup_step").next();
            });

                //activate next step on progressbar using the index of next_fs
                $("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");
        });
        $(".prev-btn").on('click', function (e) {
            e.preventDefault();
            if (animating) return false;
            animating = true;

            current_fs = $(this).parents(".signup_step");
            previous_fs = $(this).parents(".signup_step").prev();

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
                        'position': 'relative'
                    });
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
        $("#brandname").change(function () {
            var brandname = $(this).val();
            if(brandname !=""){
                $.ajax({
                    url: "<?php echo base_url();?>brandexists",
                    method: "post",
                    data: "brandname=" + brandname,
                    success: function (data) {
                        if (data == 1) {
                            $(':input[type="submit"]').prop('disabled', false);
                            $("#brandmsg").html("");
                        } else {
                            $(':input[type="submit"]').prop('disabled', true);
                            $("#brandmsg").html("Brand name already exists");
                        }
                    }
                });
            }else {
                $("#brandmsg").html("Please enter brand name ");
            }

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

    $('body').on('submit','#trial-form',function (e) {
        var formdata = $(this).serialize();
        e.preventDefault();
        $.ajax({
            url: "<?php echo base_url();?>register/savetrialaccount",
            method: "post",
            data: formdata,
            success: function (data) {
                if (data > 0 ) {
                    $('#thankyou-next').trigger('click');
                } else {

                }
            }
        });

    });

</script>
<!--</body>
</html>-->