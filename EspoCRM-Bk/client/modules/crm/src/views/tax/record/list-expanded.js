Espo.define('crm:views/tax/record/list-expanded', ['views/record/list-expanded', 'crm:views/tag/record/list'], function (Dep, List) {
    
        return Dep.extend({
    
            rowActionsView: 'crm:views/tax/record/row-actions/default',
    
            actionSetCompleted: function (data) {
                console.log(1);
                List.prototype.actionSetCompleted.call(this, data);
            },
    
        });
    
    });