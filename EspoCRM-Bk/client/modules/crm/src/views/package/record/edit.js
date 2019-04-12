Espo.define('crm:views/package/record/edit', 'views/record/edit', function (Dep) {
    return Dep.extend({

        tableView: 'crm:views/package/record/table',

        sideView: false,

        isWide: true,

        events: _.extend({

        }, Dep.prototype.events),

        fetch: function () {
            var data = Dep.prototype.fetch.call(this);

            data['data'] = {};

            var scopeList = this.getView('extra').scopeList;
            var actionList = this.getView('extra').actionList;
            
            for (var i in scopeList) {
                var scope = scopeList[i];
                
                if (this.$el.find('select[name="' + scope + '"]').val() == 'not-set') {
                    continue;
                }
                if (this.$el.find('select[name="' + scope + '"]').val() == 'disabled') {
                    data['data'][scope] = false;
                } else {
                    var o = true;
                    o = {};
                    for (var j in actionList) {
                        var action = actionList[j];
                        if(action == "create"){
                            o[action] = "yes";
                        }else{
                            o[action] = "all";
                        }  
                        
                    }
                   
                    data['data'][scope] = o;
                    //console.log(data['data']);
                }
            }
            data['data'] = this.getView('extra').fetchScopeData();
            
            return data;
        },

        getDetailLayout: function (callback) {
            var simpleLayout = [
                {
                    label: '',
                    cells: [
                        {
                            name: 'name',
                            type: 'varchar',
                        },
                    ]
                }
            ];
            callback({
                type: 'record',
                layout: this._convertSimplifiedLayout(simpleLayout)
            });
        },

        setup: function () {

            Dep.prototype.setup.call(this);

            this.createView('extra', this.tableView, {
                mode: 'edit',
                el: this.options.el + ' .extra',
                model: this.model
            }, function (view) {
                this.listenTo(view, 'change', function () {
                    var data = this.fetch();
                    this.model.set(data);
                }, this);
            }, this);
        }

    });
});