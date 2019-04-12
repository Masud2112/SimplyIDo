Espo.define('crm:views/package/record/detail-side', 'views/record/detail-side', function (Dep) {

    return Dep.extend({

        panelList: [
            {
                name: 'default',
                label: false,
                view: 'views/package/record/panels/side'
            }
        ],

    });
});


