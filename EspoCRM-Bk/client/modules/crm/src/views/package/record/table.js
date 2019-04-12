Espo.define('crm:views/package/record/table', 'view', function (Dep) {
 return Dep.extend({

        template: 'crm:package/table',

        scopeList: null,

        actionList: ['create', 'read', 'edit', 'delete', 'stream'],

        accessList: ['enabled', 'disabled'],

        // levelListMap: {
        //     'recordAllTeamOwnNo': ['all', 'team', 'own', 'no'],
        //     'recordAllTeamNo': ['all', 'team', 'no'],
        //     'recordAllOwnNo': ['all', 'own', 'no'],
        //     'recordAllNo': ['all', 'no'],
        //     'record': ['all', 'team', 'own', 'no']
        // },

        type: 'acl',

        //levelList: ['yes', 'all', 'team', 'own', 'no'],

        booleanLevelList: ['yes', 'no'],

        //booleanActionList: ['create'],

        colors: {
            yes: '#6BC924',
            all: '#6BC924',
            account: '#999900',
            contact: '#999900',
            team: '#999900',
            own: '#CC9900',
            no: '#F23333',
            enabled: '#6BC924',
            disabled: '#F23333',
            'not-set': '#A8A8A8'
        },

        mode: 'detail',

        tableData: null,

        data: function () {
            var data = {};
            data.editMode = this.mode === 'edit';
           // data.actionList = this.actionList;
            data.accessList = this.accessList;
            data.colors = this.colors;

            data.tableDataList = this.getTableDataList();
            
            return data;
        },

        getTableDataList: function () {
            var aclData = this.acl.data;
            var aclDataList = [];

            this.scopeList.forEach(function (scope) {
                var o = {};

                var access = 'not-set';

                if (this.final) {
                    access = 'enabled';
                }

                if (scope in aclData) {
                    if (aclData[scope] === false) {
                        access = 'disabled';
                    } else {
                        access = 'enabled';
                    }
                }
                var list = [];
                var type = this.aclTypeMap[scope];
                var isVisible = this.getLanguage().translate(scope, 'scopeIsVisible');

                // if (this.aclTypeMap[scope] != 'boolean') {
                //     this.actionList.forEach(function (action, j) {
                //         var allowedActionList = this.getMetadata().get(['scopes', scope, this.type + 'ActionList']);

                //         if (allowedActionList) {
                //             if (!~allowedActionList.indexOf(action)) {
                //                 list.push({
                //                     action: action,
                //                     levelList: false,
                //                     level: null
                //                 });
                //                 return;
                //             }
                //         }

                //         if (action === 'stream') {
                //             if (!this.getMetadata().get('scopes.' + scope + '.stream')) {
                //                 list.push({
                //                     action: 'stream',
                //                     levelList: false,
                //                     level: null
                //                 });
                //                 return;
                //             }
                //         }

                //         var level = 'all';
                //         if (~this.booleanActionList.indexOf(action)) {
                //             level = 'yes';
                //         }
                //         if (scope in aclData) {
                //             if (access == 'enabled') {
                //                 if (aclData[scope] !== true) {
                //                     if (action in aclData[scope]) {
                //                         level = aclData[scope][action];
                //                     } else {
                //                         // TODO remove it
                //                         if (~this.booleanActionList.indexOf(action)) {
                //                             level = 'yes';
                //                         } else {
                //                             if (j > 0) {
                //                                 level = (list[j - 1] || {}).level;
                //                             }
                //                         }
                //                     }
                //                 }
                //             } else {
                //                 level = 'no';
                //             }
                //         }
                //         var levelList = this.getMetadata().get(['scopes', scope, this.type + 'LevelList']) || this.levelListMap[type] || [];
                //         if (~this.booleanActionList.indexOf(action)) {
                //             levelList = this.booleanLevelList;
                //         }
                //         list.push({
                //             level: level,
                //             name: scope + '-' + action,
                //             action: action,
                //             levelList: levelList
                //         });
                //     }, this);
                // }

                if(isVisible == 'Yes'){
                    aclDataList.push({
                        list: list,
                        access: access,
                        name: scope,
                        type: type,
                        isVisible: isVisible
                    });
                }
            }, this);

            return aclDataList;
        },

        setup: function () {
            this.mode = this.options.mode || 'detail';

            this.final = this.options.final || false;

            this.setupData();

            this.listenTo(this.model, 'sync', function () {
                this.setupData();
                if (this.isRendered()) {
                    this.reRender();
                }
            }, this);

            this.template = 'crm:package/table';
            if (this.mode == 'edit') {
                this.template = 'crm:package/table-edit';
            }
        },

        setupData: function () {
            this.acl = {};
            if (this.options.acl) {
                this.acl.data = this.options.acl.data;
            } else {
                this.acl.data = Espo.Utils.cloneDeep(this.model.get('data') || {});
            }
            
            this.setupScopeList();
        },

        setupScopeList: function () {
            this.aclTypeMap = {};
            this.scopeList = [];

            var scopeListAll = Object.keys(this.getMetadata().get('scopes')).sort(function (v1, v2) {
                 return this.translate(v1, 'scopeNamesPlural').localeCompare(this.translate(v2, 'scopeNamesPlural'));
            }.bind(this));

            scopeListAll.forEach(function (scope) {
                if (this.getMetadata().get('scopes.' + scope + '.disabled')) return;
                var acl = this.getMetadata().get('scopes.' + scope + '.acl');
                if (acl) {
                    this.scopeList.push(scope);
                    this.aclTypeMap[scope] = acl;
                    if (acl === true) {
                        this.aclTypeMap[scope] = 'record';
                    }
                }
            }, this);
        },

        fetchScopeData: function () {
            var data = {};

            var scopeList = this.scopeList;
            var actionList = this.actionList;
            var aclTypeMap = this.aclTypeMap;

            for (var i in scopeList) {
                var scope = scopeList[i];
                if (this.$el.find('select[name="' + scope + '"]').val() == 'not-set') {
                    continue;
                }
                if (this.$el.find('select[name="' + scope + '"]').val() == 'disabled') {
                    data[scope] = false;
                } else {
                    var o = true;
                    if (aclTypeMap[scope] != 'boolean') {
                        o = {};
                        for (var j in actionList) {
                            var action = actionList[j];
                            if(action == "create"){
                                o[action] = "yes";
                            }else{
                                o[action] = "all";
                            } 
                        }
                    }
                    data[scope] = o;
                }
            }
            return data;
        },

        afterRender: function () {
            if (this.mode == 'edit') {
                this.scopeList.forEach(function (scope) {
                    var $read = this.$el.find('select[name="'+scope+'-read"]');
                    $read.on('change', function () {
                        var value = $read.val();
                        this.controlEditSelect(scope, value);
                        this.controlDeleteSelect(scope, value);
                        this.controlStreamSelect(scope, value);
                    }.bind(this));

                    var $edit = this.$el.find('select[name="'+scope+'-edit"]');
                    $edit.on('change', function () {
                        var value = $edit.val();
                        this.controlDeleteSelect(scope, value);
                    }.bind(this));

                    this.controlEditSelect(scope, $read.val(), true);
                    this.controlStreamSelect(scope, $read.val(), true);
                    this.controlDeleteSelect(scope, $edit.val(), true);
                }, this);
            }
        },

        
        controlEditSelect: function (scope, value, dontChange) {
            var $edit = this.$el.find('select[name="'+scope+'-edit"]');

            if (!dontChange) {
                if (this.levelList.indexOf($edit.val()) < this.levelList.indexOf(value)) {
                    $edit.val(value);
                }
            }

            $edit.find('option').each(function (i, o) {
                var $o = $(o);
                if (this.levelList.indexOf($o.val()) < this.levelList.indexOf(value)) {
                    $o.attr('disabled', 'disabled');
                } else {
                    $o.removeAttr('disabled');
                }
            }.bind(this));
        },

        controlStreamSelect: function (scope, value, dontChange) {
            var $stream = this.$el.find('select[name="'+scope+'-stream"]');

            if (!dontChange) {
                if (this.levelList.indexOf($stream.val()) < this.levelList.indexOf(value)) {
                    $stream.val(value);
                }
            }

            $stream.find('option').each(function (i, o) {
                var $o = $(o);
                if (this.levelList.indexOf($o.val()) < this.levelList.indexOf(value)) {
                    $o.attr('disabled', 'disabled');
                } else {
                    $o.removeAttr('disabled');
                }
            }.bind(this));
        },

        controlDeleteSelect: function (scope, value, dontChange) {
            var $delete = this.$el.find('select[name="'+scope+'-delete"]');

            if (!dontChange) {
                if (this.levelList.indexOf($delete.val()) < this.levelList.indexOf(value)) {
                    $delete.val(value);
                }
            }

            $delete.find('option').each(function (i, o) {
                var $o = $(o);
                if (this.levelList.indexOf($o.val()) < this.levelList.indexOf(value)) {
                    $o.attr('disabled', 'disabled');
                } else {
                    $o.removeAttr('disabled');
                }
            }.bind(this));
        }
    });
});