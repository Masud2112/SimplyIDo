Espo.define('crm:views/tag/record/list-expanded', ['views/record/list-expanded', 'crm:views/tag/record/list'], function (Dep, List) {
    
        return Dep.extend({
    
            rowActionsView: 'crm:views/tag/record/row-actions/default',
    
            actionSetCompleted: function (data) {
                console.log(1);
                List.prototype.actionSetCompleted.call(this, data);
            },
    
        });
    
    });