
Espo.define('crm:views/tax/detail', 'views/detail', function (Dep) {
    
        return Dep.extend({
    
            setup: function () {
                Dep.prototype.setup.call(this);                
            },
    
            actionSetCompleted: function (data) {
                var id = data.id;
    
                this.model.save({                   
                    patch: true,
                    success: function () {
                        Espo.Ui.success(this.translate('Saved'));
                        this.$el.find('[data-action="setCompleted"]').remove();
                    }.bind(this),
                });
    
            },
    
        });
    
    });
    