
<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">{{translate 'Scope Level' scope='Role'}}</h4>
    </div>
    <div class="panel-body">
        <div class="no-margin">
            <table class="table table-bordered no-margin package-view">
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
                        <span style="color: {{prop ../colors access}};">{{translateOption access scope='Role' field='accessList'}}</span>
                    </td>

                    {{#ifNotEqual type 'boolean'}}
                        {{#each ../list}}
                            <td>
                                {{#ifNotEqual ../../access 'not-set'}}
                                    <span style="color: {{prop ../../../../colors level}};">{{translateOption level field='levelList' scope='Role'}}</span>
                                {{/ifNotEqual}}
                            </td>
                        {{/each}}
                    {{/ifNotEqual}}
                </tr>
                {{/each}}
            </table>
        </div>
    </div>
</div>