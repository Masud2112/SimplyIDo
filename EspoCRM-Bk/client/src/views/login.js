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

Espo.define('views/login', 'view', function (Dep) {

    return Dep.extend({

        template: 'login',

        views: {
            footer: {
                el: 'body > footer',
                view: 'views/site/footer'
            },
        },

        events: {
            'submit #login-form': function (e) {
                this.login();
                return false;
            },
            'click a[data-action="passwordChangeRequest"]': function (e) {
                this.showPasswordChangeRequest();
            },
            /**
            * Added By: Vaidehi
            * Dt: 09/19/2017
            * to trigger click sign up request
            */
            'click a[data-action="registerRequest"]': function (e) {
                this.showRegisterRequest();
            },
            /**
            * Added By: Vaidehi
            * Dt: 09/28/2017
            * to trigger click socail sign up request
            */
            'click a[data-action="socialSignUpRequest"]': function (e, data) {
                this.showSocialSignUpRequest(data);
            }
        },

        data: function () {
            return {
                logoSrc: this.getLogoSrc()
            };
        },

        getLogoSrc: function () {
            var companyLogoId = this.getConfig().get('companyLogoId');
            if (!companyLogoId) {
                return this.getBasePath() + (this.getThemeManager().getParam('logo') || 'client/img/logo.png');
            }
            return this.getBasePath() + '?entryPoint=LogoImage&id='+companyLogoId+'&t=' + companyLogoId;
        },

        login: function () {
            var userName = $("#field-userName").val();
            var password = $("#field-password").val();

            var $submit = this.$el.find('#btn-login');

            if (userName == '') {
                var $el = $("#field-userName");

                var message = this.getLanguage().translate('userCantBeEmpty', 'messages', 'User');
                $el.popover({
                    placement: 'bottom',
                    content: message,
                    trigger: 'manual',
                }).popover('show');

                var $cell = $el.closest('.form-group');
                $cell.addClass('has-error');
                this.$el.one('mousedown click', function () {
                    $cell.removeClass('has-error');
                    $el.popover('destroy');
                });
                return;
            }

            $submit.addClass('disabled');

            this.notify('Please wait...');

            $.ajax({
                url: 'App/user',
                headers: {
                    'Authorization': 'Basic ' + Base64.encode(userName  + ':' + password),
                    'Espo-Authorization': Base64.encode(userName + ':' + password)
                },
                success: function (data) {
                    this.notify(false);
                    this.trigger('login', {
                        auth: {
                            userName: userName,
                            token: data.token
                        },
                        user: data.user,
                        preferences: data.preferences,
                        acl: data.acl,
                        settings: data.settings
                    });
                }.bind(this),
                error: function (xhr) {
                    $submit.removeClass('disabled');
                    if (xhr.status == 401) {
                        this.onWrong();
                    }
                }.bind(this),
                login: true,
            });
        },

        onWrong: function () {
            var cell = $('#login .form-group');
            cell.addClass('has-error');
            this.$el.one('mousedown click', function () {
                cell.removeClass('has-error');
            });
            Espo.Ui.error(this.translate('wrongUsernamePasword', 'messages', 'User'));
        },

        showPasswordChangeRequest: function () {
            this.notify('Please wait...');
            this.createView('passwordChangeRequest', 'views/modals/password-change-request', {
                url: window.location.href
            }, function (view) {
                view.render();
                view.notify(false);
            });
        },

        /**
        * Added By: Vaidehi
        * Dt: 09/19/2017
        * to create view for sign up page
        */
        showRegisterRequest: function () {
            console.log(window.location.href);
            this.notify('Please wait...');
            this.createView('signUpRequest', 'views/modals/signup-request', {
                url: window.location.href
            }, function (view) {
                view.render();
                view.notify(false);
            });
        },

        /**
        * Added By: Vaidehi
        * Dt: 09/28/2017
        * to create view for social sign up page
        */
        showSocialSignUpRequest: function (data) {
            this.notify('Please wait...');
            this.createView('socialsignUpRequest', 'views/modals/social-signup-request', {
                url: window.location.href,
                data: data
            }, function (view) {
                view.render();
                view.notify(false);
            });
        }
    });

});