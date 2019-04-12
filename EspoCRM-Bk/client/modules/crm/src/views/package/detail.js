Espo.define('crm:views/package/detail', 'views/detail', function (Dep) {
    
    return Dep.extend({

       // tableView: 'crm:views/package/record/table',

       // sideView: false,

        //isWide: true,

        setup: function () {
            Dep.prototype.setup.call(this);    
            // this.createView('extra', this.tableView, {
            //     el: this.options.el + ' .extra',
            //     model: this.model
            // });            
        },

        //editModeDisabled: true,

        // setup: function () {
        //     Dep.prototype.setup.call(this);
        //     this.createView('extra', this.tableView, {
        //         el: this.options.el + ' .extra',
        //         model: this.model
        //     });
        // },
    });
});