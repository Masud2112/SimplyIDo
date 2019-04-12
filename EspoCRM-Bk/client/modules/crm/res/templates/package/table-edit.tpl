
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">{{translate 'Scope Level' scope='Role'}}</h4>
    </div>
    <div class="panel-body">
        <div class="no-margin">
            <table class="table table-bordered no-margin package-edit">
                <tr>
                    <th></th>
                    <th>{{translate 'Access' scope='Role'}}</th>
                    {{#each actionList}}
                        <th>{{translate this scope='Role' category='actions'}}</th>
                    {{/each}}
                </tr>
                {{#each tableDataList}}
                <tr>
                    <td><b>{{translate name category='scopeNamesPlural'}}</b></td>

                    <td>
                        <select name="{{name}}" class="form-control" data-type="access">{{options ../accessList access scope='Role' field='accessList'}}</select>
                    </td>

                    {{#ifNotEqual type 'boolean'}}
                        {{#each ../list}}
                            <td>
                                {{#if levelList}}
                                <select name="{{name}}" class="form-control{{#ifNotEqual ../../../access 'enabled'}} hidden{{/ifNotEqual}}" data-scope="{{../../name}}"{{#ifNotEqual ../../access 'enabled'}} disabled{{/ifNotEqual}}>
                                {{options levelList level field='levelList' scope='Role'}}
                                </select>
                                {{/if}}
                            </td>
                        {{/each}}
                    {{/ifNotEqual}}
                </tr>
                {{/each}}
            </table>
        </div>
    </div>
</div>