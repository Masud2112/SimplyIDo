<!--
-- Added By: Vaidehi
-- Dt: 09/19/2017
-- sign up page
-->
<div id="wrapper" class="row wrapper multi-step-signup">
    <!-- 3 Step Navigation -->
    <div class="steps-tab clearfix" data-target="#multi-step-signup">
        <ul class="list-unstyled list-inline text-center mt-4">
            <li class="list-inline-item active" id="step-1"><a href="#"><span class="step">1</span> Account Setup </a>
            </li>
            <li class="list-inline-item" id="step-2"><a href="#"><span class="step">2</span> Create Brand </a>
            </li>
            <!--<li class="list-inline-item" id="step-3"><a href="#"><span class="step">3</span> Payment Details </a>
            </li>-->
        </ul>
    </div>
    <!-- /step-tabs -->
    <!-- Register Form -->
    <div class="col-lg-12 login-center mx-auto">
        <form id="signup-form" class="multi-step-form form-material" onsubmit="return false;" data-parsley-validate novalidate method="post">
            <fieldset id="signup-step-1" class="form-material active animated fadeInRight">
                <h6 class="text-uppercase">Register Now</h6>
                <p class="text-muted">Create your account for free and enjoy.</p>
                <div class="cell form-group" data-name="firstname">
                    <input class="form-control" type="text" name="firstname" id="firstname" placeholder="First Name" required data-parsley-required-message="Please enter valid first name">
                    <label>First Name</label>
                </div>
                <div class="cell form-group" data-name="lastname">
                    <input class="form-control" type="text" name="lastname" id="lastname" placeholder="Last Name" required data-parsley-required-message="Please enter valid last name">
                    <label>Last Name</label>
                </div>
                <div class="cell form-group" data-name="useremail">
                    <input class="form-control" type="email" placeholder="Email" name="useremail" id="useremail"data-parsley-type="email" required data-parsley-required-message="Please enter valid email address">
                    <label>Email</label>
                </div>
                <div class="cell form-group" data-name="passwd">
                    <input class="form-control" type="password" name="passwd" id="passwd" minlength="6" placeholder="Password" required data-parsley-required-message="Password should be minimum of 6 characters" data-parsley-minlength-message="Password must be at least 6 characters long">
                    <label>Password</label>
                </div>
                <div class="cell form-group" data-name="cpasswd">
                    <input class="form-control" type="password" placeholder="Confirm Password" name="cpasswd" id="cpasswd" required data-parsley-equalto="#passwd" minlength="6" data-parsley-required-message="Confirm password should be minimum of 6 characters" data-parsley-minlength-message="Confirm Password must be at least 6 characters long" data-parsley-equalto-message="Confirm Password should match with Password">
                    <label>Confirm Password</label>
                </div>
                <div class="cell form-group mb-3" data-name="terms">
                    <div class="checkbox checkbox-info">
                        <label>
                            <input type="checkbox" name="terms[]" required data-parsley-mincheck="1" data-parsley-required-message="Please agree to terms and conditions"> <span class="label-text">I agree to all <a href="#">Terms &amp; Conditions</a></span>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <button class="btn btn-info btn-block ripple next-btn" id="account-next">Next</button>
                </div>
            </fieldset>
            <fieldset id="signup-step-2" class="form-material animated fadeInRight">
                <h6 class="text-uppercase">Create Brand</h6>
                <p class="text-muted">Input Brand specific settings below.</p>
                <div class="cell form-group" data-name="brandname">
                    <input class="form-control" type="text" placeholder="Brand Name" name="brandname" id="brandname" required data-parsley-required-message="Please enter valid brand name">
                    <label>Brand Name</label>
                </div>
                <div class="cell form-group" data-name="servicetype">                  
                   <select class="form-control" data-placeholder="Choose" name="servicetype" id="servicetype" required data-parsley-required-message="Please select valid service">
                    </select>
                    <label>Service</label>
                </div>
                <div class="cell form-group" data-name="packagetype">                  
                   <select class="form-control" data-placeholder="Choose" name="packagetype" id="packagetype" required data-parsley-required-message="Please select valid package">
                    </select>
                    <label>Package</label>
                </div>
                <div class="cell form-group" data-name="address1" data-parsley-required-message="Please enter valid address">
                    <input class="form-control" type="text" placeholder="Address Line 1" name="address1" id="address1" required data-parsley-required-message="Please enter valid address">
                    <label>Address Line 1</label>
                </div>
                <div class="cell form-group" data-name="address2">
                    <input class="form-control" type="text" placeholder="Address Line 2" name="address2" id="address2">
                    <label>Address Line 2</label>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="cell form-group" data-name="city" data-parsley-required-message="Please enter valid city">
                            <input class="form-control" type="text" placeholder="City" name="city" id="city" required data-parsley-required-message="Please enter valid city">
                            <label>City</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="cell form-group" data-name="state" data-parsley-required-message="Please enter valid state">
                            <input class="form-control" type="text" placeholder="State/Province" name="state" id="state" required data-parsley-required-message="Please enter valid state">
                            <label>State/Province</label>
                        </div>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-6">
                        <div class="cell form-group" data-name="zipcode" data-parsley-required-message="Please enter valid zipcode">
                            <input class="form-control" type="text" placeholder="Zip Code" name="zipcode" id="zipcode" required data-parsley-required-message="Please enter valid zipcode">
                            <label>Zip Code</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="cell form-group" data-name="country">
                            <input class="form-control" type="text" name="country" id="country" value="United States" readonly required data-parsley-required-message="Please enter valid country">
                            <label></br></br>Country</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row text-center">
                    <div class="col-6">
                        <button class="btn btn-primary btn-block ripple prev-btn">Previous</button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-info btn-block ripple next-btn" type="submit" id="brand-next">Sign Up</button>
                    </div>
                </div>
                <!--<div class="form-group row text-center">
                    <div class="col-6">
                        <button class="btn btn-primary btn-block ripple prev-btn">Previous</button>
                    </div>
                    <div class="col-6">
                        <button class="btn btn-info btn-block ripple next-btn" id="brand-next">Next</button>x
                    </div>
                </div>-->
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
<script type="text/javascript">
    function formatRepoSelection(serie) {
        if (serie.name === undefined) {
            return serie.name;
        } else {
            return serie.name;
        }
    }

    function formatRepo (serie) {
        if (serie.loading) return serie.name;
        var markup = '<div class="clearfix">' +
          '<div clas="col-sm-10">' +
          '<div class="clearfix">' +
          '<div class="col-xs-12">' + serie.name + '</div>' +
          '</div>';
          markup += '</div></div>';
      return markup;
    }
    
    $(function () {
        $(".modal").removeAttr('tabindex');       

        $("select[name='servicetype']").select2({
            ajax: {
                url: "api/v1/ServiceType",
                delay: 1000,
                dataType: 'json',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader ("Authorization", "Basic " + btoa('avni@intellimedianetworks.com' + ":" + 'avni@123'));
                },
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                  return {
                        results: $.map(data.list, function (item) {
                            return {
                                text: item.name,
                                name: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 3,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

        $("select[name='packagetype']").select2({
            ajax: {
                url: "api/v1/Package",
                delay: 1000,
                dataType: 'json',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader ("Authorization", "Basic " + btoa('avni@intellimedianetworks.com' + ":" + 'avni@123'));
                },
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, page) {
                  return {
                        results: $.map(data.list, function (item) {
                            return {
                                text: item.name,
                                name: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 3,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

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

        // on form submit
        $("#signup-form").on('submit', function(event) {
            // validate form with parsley.
            $(this).parsley().validate();

            // if this form is valid
            if ($(this).parsley().isValid()) {
                // show alert message
                if($(":button" ).hasClass( "btn-disabled" )) {
                    $(":button" ).removeClass( "btn-disabled" );
                    $(":button" ).addClass( "btn-color-scheme" );
                    $(".btn-color-scheme").trigger("click");
                    $("#signup-form").submit();
                    $(".dialog").modal("show");
                    return true;
                }
            }

            // prevent default so the form doesn't submit. We can return true and
            // the form will be submited or proceed with a ajax request.
            event.preventDefault();
        });
    });
</script>