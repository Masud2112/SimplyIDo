
Espo.define('Crm:Views.Tax.List', 'Views.List', function (Dep) {
    
        return Dep.extend({
        
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
    