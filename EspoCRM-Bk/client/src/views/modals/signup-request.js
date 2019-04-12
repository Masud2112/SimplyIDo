/************************************************************************
 * This file is part of Simply I Do.
 *
 * Simply I Do - Open Source CRM application.
 * Copyright (C) 2014-2017 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://simplyido.com
 *
 * Simply I Do is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Simply I Do is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Simply I Do. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Simply I Do" word.
 ************************************************************************/
/**
* Added By: Vaidehi
* Dt: 09/19/2017
* js file to handle all request related to sign up page
*/
Espo.define('views/modals/signup-request', 'views/modal' , function (Dep) {

    return Dep.extend({
		
		cssName: 'signup-request',

       	template: 'modals/signup-request',

       	setup: function () {
            this.buttonList = [
            	{
                    name: 'submit',
                    label: 'Sign Up',
                    style: 'disabled signupbtn'
                },
                {
                    name: 'cancel',
                    label: 'Close'
                }
            ];

            this.header = this.translate('Sign Up Request', 'labels', 'User');
        },

        actionSubmit: function () {
            var $firstName      = this.$el.find('input[name="firstname"]');
            var $lastName       = this.$el.find('input[name="lastname"]');
            var $emailAddress   = this.$el.find('input[name="useremail"]');
            var $userPass       = this.$el.find('input[name="passwd"]');
            var $userCPass      = this.$el.find('input[name="cpasswd"]');
            var $userTerms       = this.$el.find('input[name="terms[]"]');
            var $brandName      = this.$el.find('input[name="brandname"]');
            var $serviceType    = this.$el.find('select[name="servicetype"]');
            var $packageType    = this.$el.find('select[name="packagetype"]');
            var $address1       = this.$el.find('input[name="address1"]');
            var $address2       = this.$el.find('input[name="address2"]');
            var $city           = this.$el.find('input[name="city"]');
            var $state          = this.$el.find('input[name="state"]');
            var $zipcode        = this.$el.find('input[name="zipcode"]');
            var $country        = this.$el.find('input[name="country"]');

            $firstName.popover('destroy');

            var firstName = $firstName.val();

            var isValid = true;
            if (firstName == '') {
                 isValid = false;

                 var message = this.getLanguage().translate('firstNameCantBeEmpty', 'messages', 'User');

                 $firstName.popover({
                     placement: 'bottom',
                     content: message,
                     trigger: 'manual',
                 }).popover('show');

                 var $cellfirstName = $firstName.closest('.form-group');
                 $cellfirstName.addClass('has-error');

                 $firstName.one('mousedown click', function () {
                     $cellfirstName.removeClass('has-error');
                     $firstName.popover('destroy');
                 });
            }

            var isValid = true;
            
            if(!validName(firstName)){
                isValid = false;

                var message = this.getLanguage().translate('validFirstName', 'messages', 'User');

                $firstName.popover({
                    placement: 'bottom',
                    content: message,
                    trigger: 'manual',
                }).popover('show');

                var $cellfirstName = $firstName.closest('.form-group');
                $cellfirstName.addClass('has-error');

                $firstName.one('mousedown click', function () {
                    $cellfirstName.removeClass('has-error');
                    $firstName.popover('destroy');
                });
            }

            $lastName.popover('destroy');

            var lastName = $lastName.val();

            var isValid = true;
            if (lastName == '') {
                 isValid = false;

                 var message = this.getLanguage().translate('lastNameCantBeEmpty', 'messages', 'User');

                 $lastName.popover({
                     placement: 'bottom',
                     content: message,
                     trigger: 'manual',
                 }).popover('show');

                 var $celllastName = $lastName.closest('.form-group');
                 $celllastName.addClass('has-error');

                 $lastName.one('mousedown click', function () {
                     $celllastName.removeClass('has-error');
                     $lastName.popover('destroy');
                 });
            }

            var isValid = true;
            
            if(!validName(lastName)){
                isValid = false;

                var message = this.getLanguage().translate('validFirstName', 'messages', 'User');

                $lastName.popover({
                    placement: 'bottom',
                    content: message,
                    trigger: 'manual',
                }).popover('show');

                var $celllastName = $lastName.closest('.form-group');
                $celllastName.addClass('has-error');

                $lastName.one('mousedown click', function () {
                    $celllastName.removeClass('has-error');
                    $lastName.popover('destroy');
                });
            }

            $emailAddress.popover('destroy');

            var emailAddress = $emailAddress.val();
            
            var isValid = true;
            if (emailAddress == '') {
                 isValid = false;

                 var message = this.getLanguage().translate('emailAddressCantBeEmpty', 'messages', 'User');

                 $emailAddress.popover({
                     placement: 'bottom',
                     content: message,
                     trigger: 'manual',
                 }).popover('show');

                 var $cellEmailAddress = $emailAddress.closest('.form-group');
                 $cellEmailAddress.addClass('has-error');

                 $emailAddress.one('mousedown click', function () {
                     $cellEmailAddress.removeClass('has-error');
                     $emailAddress.popover('destroy');
                 });
            }

            var isValid = true;
            
            if(!isEmail(emailAddress)){
                isValid = false;

                var message = "Please enter valid username(email).";

                $emailAddress.popover({
                    placement: 'bottom',
                    content: message,
                    trigger: 'manual',
                }).popover('show');

                var $cellEmailAddress = $emailAddress.closest('.form-group');
                $cellEmailAddress.addClass('has-error');

                $emailAddress.one('mousedown click', function () {
                    $cellEmailAddress.removeClass('has-error');
                    $emailAddress.popover('destroy');
                });
            }

            $userPass.popover('destroy');

            var userPass = $userPass.val();

            var isValid = true;
            if (userPass == '') {
                 isValid = false;

                 var message = this.getLanguage().translate('passsordCantBeEmpty', 'messages', 'User');

                 $userPass.popover({
                     placement: 'bottom',
                     content: message,
                     trigger: 'manual',
                 }).popover('show');

                 var $celluserPass = $userPass.closest('.form-group');
                 $celluserPass.addClass('has-error');

                 $userPass.one('mousedown click', function () {
                     $celluserPass.removeClass('has-error');
                     $userPass.popover('destroy');
                 });
            }

            $userCPass.popover('destroy');

            var userCPass = $userCPass.val();

            var isValid = true;
            if (userCPass == '') {
                 isValid = false;

                 var message = this.getLanguage().translate('passsordCantBeEmpty', 'messages', 'User');

                 $userCPass.popover({
                     placement: 'bottom',
                     content: message,
                     trigger: 'manual',
                 }).popover('show');

                 var $celluserCPass = $userCPass.closest('.form-group');
                 $celluserCPass.addClass('has-error');

                 $userCPass.one('mousedown click', function () {
                     $celluserCPass.removeClass('has-error');
                     $userCPass.popover('destroy');
                 });
            }

            var isValid = true;
            if (userCPass != userPass) {
                 isValid = false;

                 var message = this.getLanguage().translate('passwordEqualConfirmPasssordCantBeEmpty', 'messages', 'User');

                 $userPass.popover({
                     placement: 'bottom',
                     content: message,
                     trigger: 'manual',
                 }).popover('show');

                 var $celluserPass = $userPass.closest('.form-group');
                 $celluserPass.addClass('has-error');

                 $userPass.one('mousedown click', function () {
                     $celluserPass.removeClass('has-error');
                     $userPass.popover('destroy');
                 });
            }

            $userTerms.popover('destroy');

            var userTerms = $userTerms.val();

            var isValid = true;
            if (userTerms != 'on') {
                 isValid = false;

                 var message = this.getLanguage().translate('termsCantBeEmpty', 'messages', 'User');

                 $userTerms.popover({
                     placement: 'bottom',
                     content: message,
                     trigger: 'manual',
                 }).popover('show');

                 var $celluserTerms = $userTerms.closest('.form-group');
                 $celluserTerms.addClass('has-error');

                 $userTerms.one('mousedown click', function () {
                     $celluserTerms.removeClass('has-error');
                     $userTerms.popover('destroy');
                 });
            }

            $brandName.popover('destroy');

            var brandName = $brandName.val();

            var isValid = true;
            if (brandName == '') {
                 isValid = false;

                 var message = this.getLanguage().translate('brandNameCantBeEmpty', 'messages', 'User');

                 $brandName.popover({
                     placement: 'bottom',
                     content: message,
                     trigger: 'manual',
                 }).popover('show');

                 var $cellbrandName = $brandName.closest('.form-group');
                 $cellbrandName.addClass('has-error');

                 $brandName.one('mousedown click', function () {
                     $cellbrandName.removeClass('has-error');
                     $brandName.popover('destroy');
                 });
            }

            $serviceType.popover('destroy');

            var serviceType = $serviceType.val();

            var isValid = true;
            if (serviceType == '') {
                 isValid = false;

                 var message = this.getLanguage().translate('serviceTypeCantBeEmpty', 'messages', 'User');

                 $serviceType.popover({
                     placement: 'bottom',
                     content: message,
                     trigger: 'manual',
                 }).popover('show');

                 var $cellserviceType = $serviceType.closest('.form-group');
                 $cellserviceType.addClass('has-error');

                 $serviceType.one('mousedown click', function () {
                     $cellserviceType.removeClass('has-error');
                     $serviceType.popover('destroy');
                 });
            }

            $packageType.popover('destroy');

            var packageType = $packageType.val();

            var isValid = true;
            if (packageType == '') {
                 isValid = false;

                 var message = this.getLanguage().translate('packageTypeCantBeEmpty', 'messages', 'User');

                 $packageType.popover({
                     placement: 'bottom',
                     content: message,
                     trigger: 'manual',
                 }).popover('show');

                 var $cellpackageType = $packageType.closest('.form-group');
                 $cellpackageType.addClass('has-error');

                 $packageType.one('mousedown click', function () {
                     $cellpackageType.removeClass('has-error');
                     $packageType.popover('destroy');
                 });
            }

            $address1.popover('destroy');

            var address1 = $address1.val();

            var isValid = true;
            if (address1 == '') {
                 isValid = false;

                 var message = this.getLanguage().translate('paymentAddressCantBeEmpty', 'messages', 'User');

                 $address1.popover({
                     placement: 'bottom',
                     content: message,
                     trigger: 'manual',
                 }).popover('show');

                 var $celladdress1 = $address1.closest('.form-group');
                 $celladdress1.addClass('has-error');

                 $address1.one('mousedown click', function () {
                     $celladdress1.removeClass('has-error');
                     $address1.popover('destroy');
                 });
            }

            $city.popover('destroy');

            var city = $city.val();

            var isValid = true;
            if (city == '') {
                 isValid = false;

                 var message = this.getLanguage().translate('cityCantBeEmpty', 'messages', 'User');

                 $city.popover({
                     placement: 'bottom',
                     content: message,
                     trigger: 'manual',
                 }).popover('show');

                 var $cellcity = $city.closest('.form-group');
                 $cellcity.addClass('has-error');

                 $city.one('mousedown click', function () {
                     $cellcity.removeClass('has-error');
                     $city.popover('destroy');
                 });
            }

            $state.popover('destroy');

            var state = $state.val();

            var isValid = true;
            if (state == '') {
                 isValid = false;

                 var message = this.getLanguage().translate('stateCantBeEmpty', 'messages', 'User');

                 $state.popover({
                     placement: 'bottom',
                     content: message,
                     trigger: 'manual',
                 }).popover('show');

                 var $cellstate = $state.closest('.form-group');
                 $cellstate.addClass('has-error');

                 $state.one('mousedown click', function () {
                     $cellstate.removeClass('has-error');
                     $state.popover('destroy');
                 });
            }

            $zipcode.popover('destroy');

            var zipcode = $zipcode.val();

            var isValid = true;
            if (zipcode == '') {
                 isValid = false;

                 var message = this.getLanguage().translate('zipcodeCantBeEmpty', 'messages', 'User');

                 $zipcode.popover({
                     placement: 'bottom',
                     content: message,
                     trigger: 'manual',
                 }).popover('show');

                 var $cellzipcode = $zipcode.closest('.form-group');
                 $cellzipcode.addClass('has-error');

                 $zipcode.one('mousedown click', function () {
                     $cellzipcode.removeClass('has-error');
                     $zipcode.popover('destroy');
                 });
            }

            var address2 = $address2.val();

            var country = $country.val();

            if (!isValid) return;

            $submit = this.$el.find('button[data-name="submit"]');
            $submit.addClass('disabled');
            this.notify('Please wait...');

            $.ajax({
                url: 'api/v1/Account',
                type: 'POST',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader ("Authorization", "Basic " + btoa('avni@intellimedianetworks.com' + ":" + 'avni@123'));
                },
                data: JSON.stringify({
                    contactIsInactive: false,
                    assignedUserId: null,
                    assignedUserName: null,
                    name: firstName + " " + lastName,
                    emailAddress: emailAddress,
                    billingAddressPostalCode: zipcode,
                    billingAddressStreet: address1 + " " + address2,
                    billingAddressState: state,
                    billingAddressCity: city,
                    billingAddressCountry: country,
                    packageId: packageType
                }),
                error: function (xhr) {
                    this.notify(false);
                    if (xhr.status == 500) {
                        var message = this.translate('sendValidData', 'messages', 'User');

                        var errhtml = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                        errhtml += '<strong>'+message+'</strong>';
                        
                        $("#msg").html(errhtml);
                        $("#msg").addClass('alert alert-danger alert-dismissable');
                        
                        xhr.errorIsHandled = true;
                    } else if(xhr.status == 409) {
                        var message = this.translate('sendValidData', 'messages', 'User');

                        var errhtml = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                        errhtml += '<strong>'+message+'</strong>';
                        
                        $("#msg").html(errhtml);
                        $("#msg").addClass('alert alert-danger alert-dismissable');
                        
                        xhr.errorIsHandled = true;
                    } else {
                        var message = this.translate('badRequest', 'messages', 'User');

                        var errhtml = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                        errhtml += '<strong>'+message+'</strong>';
                        
                        $("#msg").html(errhtml);
                        $("#msg").addClass('alert alert-danger alert-dismissable');
                        
                        xhr.errorIsHandled = true;
                    }
                    $submit.removeClass('disabled');
                }.bind(this)
            }).done(function (data) {
                this.notify(false);
                if(data.id) {
                    var newAccountId = data.id;

                    $.ajax({
                        url: 'api/v1/User',
                        type: 'POST',
                        beforeSend: function (xhr) {
                            xhr.setRequestHeader ("Authorization", "Basic " + btoa('avni@intellimedianetworks.com' + ":" + 'avni@123'));
                        },
                        data: JSON.stringify({
                            firstName: firstName,
                            lastName: lastName,
                            isActive: true,
                            isSuperAdmin: false,
                            gender: "",
                            userName: emailAddress,
                            password: userPass,
                            passwordPreview: "",
                            passwordConfirm: userCPass,
                            phoneno: "",
                            isAdmin: true,
                            userTypeId: 1,
                            accountUserId: newAccountId
                        }),
                        error: function (xhr) {
                            if(xhr.status == 409) {
                                var errhtml = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                                errhtml += '<strong>Email Address already exists</strong>';
                                
                                $("#msg").html(errhtml);
                                $("#msg").addClass('alert alert-danger alert-dismissable');
                                
                                xhr.errorIsHandled = true;
                            } else if (xhr.status == 500) {
                                var errhtml = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                                errhtml += '<strong>Internal Server Error</strong>';
                                
                                $("#msg").html(errhtml);
                                $("#msg").addClass('alert alert-danger alert-dismissable');
                                
                                xhr.errorIsHandled = true;
                            } else {
                                var errhtml = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                                errhtml += '<strong>Bad Request</strong>';
                                
                                $("#msg").html(errhtml);
                                $("#msg").addClass('alert alert-danger alert-dismissable');
                                
                                xhr.errorIsHandled = true;
                            } 
                        }
                    }).done(function (data) {
                        if(data.id) {
                            $.ajax({
                                url: 'api/v1/Service',
                                type: 'POST',
                                beforeSend: function (xhr) {
                                    xhr.setRequestHeader ("Authorization", "Basic " + btoa('avni@intellimedianetworks.com' + ":" + 'avni@123'));
                                },
                                data: JSON.stringify({
                                    name: brandName,
                                    billingaddress_postal_code: zipcode,                           
                                    billingaddress_state: state,
                                    billingaddress_city: city,
                                    billingaddress_country: country,
                                    serviceTypeId: serviceType,
                                    accountId: newAccountId
                                }),
                                error: function (xhr) {
                                    if(xhr.status == 409) {
                                        var errhtml = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                                        errhtml += '<strong>Email Address already exists</strong>';
                                        
                                        $("#msg").html(errhtml);
                                        $("#msg").addClass('alert alert-danger alert-dismissable');
                                        
                                        xhr.errorIsHandled = true;
                                    } else if (xhr.status == 500) {
                                        var errhtml = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                                        errhtml += '<strong>Internal Server Error</strong>';
                                        
                                        $("#msg").html(errhtml);
                                        $("#msg").addClass('alert alert-danger alert-dismissable');
                                        
                                        xhr.errorIsHandled = true;
                                    } else {
                                        var errhtml = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                                        errhtml += '<strong>Bad Request</strong>';
                                        
                                        $("#msg").html(errhtml);
                                        $("#msg").addClass('alert alert-danger alert-dismissable');
                                        
                                        xhr.errorIsHandled = true;
                                    } 
                                }
                            }).done(function (data) {
                                if(data.id) {
                                    var errhtml = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                                    errhtml += '<strong>Account has been created successfully</strong>';
                                    
                                    $("#msg").html(errhtml);
                                    $("#msg").addClass('alert alert-success alert-dismissable');
                                    return false;
                                } else {
                                    var errhtml = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                                    errhtml += '<strong>Email Address already exists</strong>';
                                    
                                    $("#msg").html(errhtml);
                                    $("#msg").addClass('alert alert-danger alert-dismissable');
                                }
                            });
                        } else {
                            var errhtml = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                            errhtml += '<strong>Email Address already exists</strong>';
                            
                            $("#msg").html(errhtml);
                            $("#msg").addClass('alert alert-danger alert-dismissable');
                        }
                    });
                } else {
                    var message = this.translate('sendValidData', 'messages', 'User');

                    var errhtml = '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                    errhtml += '<strong>'+message+'</strong>';
                    
                    $("#msg").html(errhtml);
                    $("#msg").addClass('alert alert-danger alert-dismissable');
                }
            }.bind(this));
        }
    });

    function isEmail(email) {
      var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      return regex.test(email);
    }

    function validName(fname) {
      var regex = /^([a-zA-Z])+$/;
      return regex.test(fname);
    }
});

