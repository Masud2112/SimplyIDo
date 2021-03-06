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

Espo.define('crm:views/dashlets/calendar', 'views/dashlets/abstract/base', function (Dep) {

    return Dep.extend({

        name: 'Calendar',

        noPadding: true,

        _template: '<div class="calendar-container">{{{calendar}}} </div>',

        init: function () {
            Dep.prototype.init.call(this);
        },

        afterRender: function () {
            var mode = this.getOption('mode');
            if (mode === 'timeline') {
                var userList = [];
                var userIdList = this.getOption('usersIds') || [];
                var userNames = this.getOption('usersNames') || {};
                userIdList.forEach(function (id) {
                    userList.push({
                        id: id,
                        name: userNames[id] || id
                    });
                }, this);

                this.createView('calendar', 'crm:views/calendar/timeline', {
                    el: this.options.el + ' > .calendar-container',
                    header: false,
                    calendarType: 'shared',
                    userList: userList,
                    enabledScopeList: this.getOption('enabledScopeList')
                }, function (view) {
                    view.render();
                }, this);
            } else {
                this.createView('calendar', 'crm:views/calendar/calendar', {
                    mode: mode,
                    el: this.options.el + ' > .calendar-container',
                    header: false,
                    enabledScopeList: this.getOption('enabledScopeList'),
                    containerSelector: this.options.el
                }, function (view) {
                    this.listenTo(view, 'view', function () {
                        if (this.getOption('mode') === 'month') {
                            var title = this.getOption('title');
                            var $headerSpan = this.$el.closest('.panel').find('.panel-heading > .panel-title > span');
                            title += ' &raquo; ' + view.getTitle();
                            $headerSpan.html(title);
                        }
                    }, this);
                    view.render();

                    this.on('resize', function () {
                        setTimeout(function() {
                            view.adjustSize();
                        }, 50);
                    });
                }, this);
            }
        },

        setupActionList: function () {
            this.actionList.unshift({
                name: 'viewCalendar',
                html: this.translate('View Calendar', 'labels', 'Calendar'),
                url: '#Calendar'
            });
        },

        setupButtonList: function () {
            if (this.getOption('mode') !== 'timeline') {
                this.buttonList.push({
                    name: 'previous',
                    html: '<span class="glyphicon glyphicon-chevron-left"></span>',
                });
                this.buttonList.push({
                    name: 'next',
                    html: '<span class="glyphicon glyphicon-chevron-right"></span>',
                });
            }
        },

        actionRefresh: function () {
            var view = this.getView('calendar');
            if (!view) return;
            view.actionRefresh();
        },

        actionNext: function () {
            var view = this.getView('calendar');
            if (!view) return;
            view.actionNext();
        },

        actionPrevious: function () {
            var view = this.getView('calendar');
            if (!view) return;
            view.actionPrevious();
        },

        actionViewCalendar: function () {
            this.getRouter().navigate('#Calendar', {trigger: true});
        }
    });
});


