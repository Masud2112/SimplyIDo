{{#if actionList.length}}
{{#each actionList}}
	{{#if link}}
		<a href="{{link}}" class="btn btn-primary btn-circle btn-sm action {{action}}" {{#each data}} data-{{@key}}="{{./this}}"{{/each}} title="{{#if html}}{{{html}}}{{else}}{{translate label scope=../../scope}}{{/if}}"></a>
	{{else}}
    	<a href="javascript:" class="btn btn-primary btn-circle btn-sm action {{action}}" {{#each data}} data-{{@key}}="{{./this}}"{{/each}} {{#if action}} data-action={{action}}{{/if}} title="{{#if html}}{{{html}}}{{else}}{{translate label scope=../../scope}}{{/if}}"></a>
    {{/if}}
{{/each}}
<!-- <div class="list-row-buttons btn-group pull-right">
    <button type="button" class="btn btn-link btn-sm dropdown-toggle" data-toggle="dropdown">
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu pull-right">
    {{#each actionList}}
        <li><a {{#if link}}href="{{link}}"{{else}}href="javascript:"{{/if}} class="action" {{#if action}} data-action={{action}}{{/if}}{{#each data}} data-{{@key}}="{{./this}}"{{/each}}>{{#if html}}{{{html}}}{{else}}{{translate label scope=../../scope}}{{/if}}</a></li>
    {{/each}}
    </ul>
</div> -->
{{/if}}



