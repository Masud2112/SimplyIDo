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

Espo.define('crm:views/knowledge-base-article/record/detail', 'views/record/detail', function (Dep) {

    return Dep.extend({

        setup: function () {
            Dep.prototype.setup.call(this);

            if (this.getAcl().checkScope('Email', 'create')) {
                this.dropdownItemList.push({
                    'label': 'Send in Email',
                    'name': 'sendInEmail'
                });
            }
        },

        actionSendInEmail: function () {
            Espo.Ui.notify(this.translate('pleaseWait', 'messages'));
            Espo.require('crm:knowledge-base-helper', function (Helper) {
                var helper = new Helper(this.getLanguage());

                helper.getAttributesForEmail(this.model, {}, function (attributes) {
                    var viewName = this.getMetadata().get('clientDefs.Email.modalViews.compose') || 'views/modals/compose-email';
                    this.createView('composeEmail', viewName, {
                        attributes: attributes,
                        selectTemplateDisabled: true,
                        signatureDisabled: true
                    }, function (view) {
                        Espo.Ui.notify(false);
                        view.render();
                    }, this);
                }.bind(this));
            }, this);
        },

    });
});

