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
        <!-- 3 Step Navigation -->
        <div class="steps-tab clearfix" data-target="#multi-step-signup">
            <ul class="list-unstyled list-inline text-center mt-4">
                <li class="list-inline-item active" id="step-2"><a href="#"><span class="step">2</span><?php echo _l('create_brand'); ?> </a>
                </li>
                <!--<li class="list-inline-item" id="step-3"><a href="#"><span class="step">3</span> Payment Details </a>
                </li>-->
            </ul>
        </div>
        <!-- /step-tabs -->
        <!-- Register Form -->
        <div class="col-lg-12 login-center mx-auto">
            <form id="signup-form" class="multi-step-form form-material" data-parsley-validate novalidate method="post" action="<?php echo base_url();?>saveclient">
                <input type="hidden" name="page" id="page" value="social">
            	<input type="hidden" name="useremail" id="useremail" value="<?php echo $socialemail; ?>" />
            	<input type="hidden" name="firstname" id="firstname" value="<?php echo $firstname; ?>"/>
                <input type="hidden" name="facebook" id="facebook" value="<?php echo $facebook; ?>"/>
                <input type="hidden" name="twitter" id="twitter" value="<?php echo $twitter; ?>"/>
                <input type="hidden" name="google" id="google" value="<?php echo $google; ?>"/>
                <fieldset id="signup-step-2" class="form-material animated active fadeInRight">
                    <h6 class="text-uppercase"><?php echo _l('create_brand'); ?></h6>
                    <p class="text-muted"><?php echo _l('brand_heading'); ?></p>
                    <div class="form-group" data-name="brandname">
                        <label for="brand-name" class="control-label"><?php echo _l('register_brand_name'); ?></label>
                        <input class="form-control" type="text" placeholder="<?php echo _l('register_brand_name'); ?>" name="brandname" id="brandname" required data-parsley-required-message="<?php echo _l('brand_name_required'); ?>">
                        <span id="brandmsg" class="parsley-required"></span>
                    </div>
                    <div class="form-group" data-name="brandtype">
                        <label for="brandtype" class="control-label"><?php echo _l('register_brandtype'); ?></label>
                        <select class="form-control" data-placeholder="<?php echo _l('register_brandtype'); ?>" name="brandtype" id="brandtype" required data-parsley-required-message="<?php echo _l('brandtype_required'); ?>">
                            <option value="">Please Select </option>
                            <?php 
                                foreach ($brandtypes as $brandtype) {
                            ?>
                                    <option value="<?php echo $brandtype['brandtypeid']; ?>"><?php echo $brandtype['name']; ?></option>
                            <?php
                                    # code...
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" data-name="packagetype">
                        <label for="package" class="control-label"><?php echo _l('register_package'); ?></label>
                        <select class="form-control" data-placeholder="<?php echo _l('register_package'); ?>" name="packagetype" id="packagetype" required data-parsley-required-message="<?php echo _l('package_required'); ?>">
                            <option value="">Please Select </option>
                            <?php 
                                foreach ($packages as $package) {
                            ?>
                                    <option value="<?php echo $package['packageid']; ?>"><?php echo $package['name']; ?></option>
                            <?php
                                    # code...
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" data-name="address1" data-parsley-required-message="Please enter valid address">
                        <label for="address1" class="control-label"><?php echo _l('register_address1'); ?></label>
                        <input class="form-control" type="text" placeholder="<?php echo _l('register_address1'); ?>" name="address1" id="address1" required data-parsley-required-message="<?php echo _l('address_required'); ?>">
                    </div>
                    <div class="form-group" data-name="address2">
                        <label for="address2" class="control-label"><?php echo _l('register_address2'); ?></label>
                        <input class="form-control" type="text" placeholder="<?php echo _l('register_address2'); ?>" name="address2" id="address2">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" data-name="city" data-parsley-required-message="Please enter valid city">
                                <label for="city" class="control-label"><?php echo _l('register_city'); ?></label>
                                <input class="form-control" type="text" placeholder="<?php echo _l('register_city'); ?>" name="city" id="city" required data-parsley-required-message="<?php echo _l('city_required'); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" data-name="state" data-parsley-required-message="Please enter valid state">
                                <label for="state" class="control-label"><?php echo _l('register_state'); ?></label>
                                <input class="form-control" type="text" placeholder="<?php echo _l('register_state'); ?>" name="state" id="state" required data-parsley-required-message="<?php echo _l('state_required'); ?>">
                            </div>
                        </div>
                    </div>
                     <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" data-name="zipcode" data-parsley-required-message="Please enter valid zipcode">
                                <label for="zip-code" class="control-label"><?php echo _l('register_zipcode'); ?></label>
                                <input class="form-control" type="text" placeholder="<?php echo _l('register_zipcode'); ?>" name="zipcode" id="zipcode" required data-parsley-required-message="<?php echo _l('zipcode_required'); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" data-name="country">
                                <label for="country" class="control-label"><?php echo _l('register_country'); ?></label>
                                <input class="form-control" type="text" name="country" id="country" value="United States" readonly required data-parsley-required-message="<?php echo _l('country_required'); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-info btn-block next-btn" type="submit" id="frm-submit"><?php echo _l('btn_sign_up'); ?></button>
                    </div>
                </fieldset>
                <!--<fieldset id="signup-step-3" class="form-material animated fadeInRight">
                    <h6 class="text-uppercase">Payment Details</h6>
                    <p class="text-muted">Input Payment details below.</p>
                    <div class="form-group">
                        <input class="form-control" type="text" placeholder="Card Number" name="cardnumber" id="cardnumber" required data-parsley-type="number">
                        <label>Card Number</label>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="form-control" data-placeholder="Month" required>
                                    <option value="01">January</option>
                                    <option value="02">February</option>
                                    <option value="03">March</option>
                                    <option value="04">April</option>
                                    <option value="05">May</option>
                                    <option value="06">June</option>
                                    <option value="07">July</option>
                                    <option value="08">August</option>
                                    <option value="09">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                                <label>Expiration</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="form-control" data-placeholder="Year" required>
                                    <option value="2017">2017</option>
                                    <option value="2018">2018</option>
                                    <option value="2019">2019</option>
                                    <option value="2020">A2020</option>
                                    <option value="2021">2021</option>
                                    <option value="2022">2022</option>
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <input class="form-control" type="text" placeholder="CVC" name="cvc" id="cvcr" required data-parsley-type="number">
                                <label>Security Code</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row text-center">
                        <div class="col-6">
                            <button class="btn btn-primary btn-block ripple prev-btn">Previous</button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-info btn-block ripple" type="button" id="frm-submit">Sign Up</button>
                        </div>
                    </div>
                </fieldset>-->   
            </form>
        </div>
        
        <!-- /.login-center -->
    </div>
    <div class="msg-box hidden"></div>
    <!-- /.body-container -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.8.0/parsley.js"></script>
    <script type="text/javascript">
        
        $(function () {
            //ajax call to check whether brand name exists or not
            $("#brandname").blur(function() {
                var brandname = $(this).val();
                $.ajax({
                    url: "<?php echo base_url();?>serviceexists",
                    method: "post",
                    data: "brandname="+brandname,
                    success: function(data){
                        if(data == "success") {
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
            $("#packagetype").change(function() {
                var packagetype = $(this).val();
                var useremail = $('#useremail').val();
                var brandname = $('#brandname').val();

                if(packagetype != '' && useremail != '' && brandname != '') {
                    $.ajax({
                        url: "<?php echo base_url();?>activebrands",
                        method: "post",
                        data: "packagetype="+packagetype+"&useremail="+useremail+'&brandname='+brandname,
                        success: function(data){
                            if(data == "success") {
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

            if($("#packagetype").val() > 0) {
                var packagetype = $('#packagetype').val();
                var useremail = $('#useremail').val();
                var brandname = $('#brandname').val();

                if(packagetype != '' && useremail != '' && brandname != '') {
                    $.ajax({
                        url: "<?php echo base_url();?>activebrands",
                        method: "post",
                        data: "packagetype="+packagetype+"&useremail="+useremail+'&brandname='+brandname,
                        success: function(data){
                            if(data == "success") {
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
                  
            
            $('#signup-form').parsley();

            var $sections = $(".fadeInRight");
                
            $sections.each(function(index, section) {
                $(section).find(':input').attr('data-parsley-group', 'block-' + index);
            });

            var el = $('.multi-step-form');
            el.each(function(){
                var $this = $(this);
                
                $stepsTab = $('.steps-tab[data-target="#' + $this.attr('id') + '"]');

                $this.find('.next-btn').on( "click", function(){
                    if ($('.multi-step-form').parsley().validate({group: 'block-' + curIndex()})) {
                        $this.find('fieldset.active').removeClass('active').addClass('done').next('fieldset').addClass('active');
                        $stepsTab.find('li.active').removeClass('active').addClass('done').next('li').addClass('active');
                    }
                });

                $this.find('.prev-btn').on( "click", function(){
                    $this.find('fieldset.active').removeClass('active').prev('fieldset').addClass('active');
                    $stepsTab.find('li.active').removeClass('active').removeClass('done').prev('li').addClass('active');
                });
            });
            
            var el1 = $('input:not([type=radio]):not([type=checkbox]), textarea');
         
            el1.each(function() {
                var $this = $(this),
                    self = this;

                var hasValueFunction = function() {
                    if( self.value.length > 0 ) {
                        self.parentNode.classList.add('input-has-value');
                        $(self).closest('.form-group').addClass('input-has-value');
                    } else {
                        self.parentNode.classList.remove('input-has-value');
                        $(self).closest('.form-group').removeClass('input-has-value');
                    }
                };

                hasValueFunction(this);
                $this.on('input', hasValueFunction);

                $this.focusin(function() {
                    this.parentNode.classList.add('input-focused');
                    $this.closest('.form-group').addClass('input-focused');
                });
                
                $this.focusout(function() {
                    this.parentNode.classList.remove('input-focused');
                    $this.closest('.form-group').removeClass('input-focused');
                });

                $this.find('.remove-focus').on('click',function() {
                    $this.emit('focusout');
                });
            });

            function curIndex() {
                // Return the current index by looking at which section has the class 'active'
                return $sections.index($sections.filter('.active'));
            }       
        });
    </script>
</body>
</html>