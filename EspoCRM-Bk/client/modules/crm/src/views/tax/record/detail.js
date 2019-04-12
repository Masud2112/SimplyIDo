
Espo.define('crm:views/tax/record/detail', 'views/record/detail', function (Dep) {
    
        return Dep.extend({
    
            duplicateAction: true,
    
            setup: function () {
                Dep.prototype.setup.call(this);                
            },
    
            actionSetCompleted: function (data) {
                var id = data.id;
    
                this.model.save({
                    status: 'Completed'
                }, {
                    patch: true,
                    success: function () {
                        Espo.Ui.success(this.translate('Saved'));
                    }.bind(this),
                });
    
            },
    
    
        });
    });
    
    