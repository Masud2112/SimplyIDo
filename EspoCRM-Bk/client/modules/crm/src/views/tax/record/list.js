
Espo.define('crm:views/tax/record/list', 'views/record/list', function (Dep) {
    
        return Dep.extend({
    
            rowActionsView: 'crm:views/tax/record/row-actions/default',
    
            actionSetCompleted: function (data) {
                var id = data.id;
                if (!id) {
                    return;
                }
                var model = this.collection.get(id);
                if (!model) {
                    return;
                }
                    
    
                this.listenToOnce(model, 'sync', function () {
                    this.notify(false);
                    this.collection.fetch();
                }, this);
    
                this.notify('Saving...');
                model.save();
    
            },
    
        });
    
    });
    