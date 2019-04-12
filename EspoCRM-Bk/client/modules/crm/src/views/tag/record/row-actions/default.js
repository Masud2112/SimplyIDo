

Espo.define('Crm:Views.Tag.Record.RowActions.Default', 'Views.Record.RowActions.Default', function (Dep) {
    
        return Dep.extend({
    
            getActionList: function () {
                var actions = Dep.prototype.getActionList.call(this);               
    
                return actions;
            }
        });
    
    });
    