
Espo.define('crm:views/package/record/detail', 'views/record/detail', function (Dep) {
    return Dep.extend({

        tableView: 'crm:views/package/record/table',

        sideView: false,

        isWide: true,

        editModeDisabled: true,

        setup: function () {
            Dep.prototype.setup.call(this);
            this.createView('extra', this.tableView, {
                el: this.options.el + ' .extra',
                model: this.model
            });
        },
    });
});


