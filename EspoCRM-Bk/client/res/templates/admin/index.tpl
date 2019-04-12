<div class="page-header"><h3>{{translate 'Administration' scope='Admin'}}</h3></div>
{{#ifEqual userType 'admin'}}
<div class="admin-content">
	<div class="row">
		<div class="col-md-7">
			{{#each links}}
			<h4>{{translate label scope='Admin'}}</h4>
			<table class="table table-bordered">
			    {{#each items}}
			    <tr>
			        <td width="200">
			            <a href="{{url}}">{{translate label scope='Admin' category='labels'}}</a>
			        </td>
			        <td>{{translate description scope='Admin' category='descriptions'}}</td>
			    </tr>
			    {{/each}}
			</table>
			{{/each}}
		</div>
		<!-- <div class="col-md-5">
			<iframe src="{{iframeUrl}}" style="width: 100%; height: 874px;" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
		</div> -->
	</div>
{{/ifEqual}}
{{#ifEqual userType 'accountowner'}}
  <div class="row">
    <div class="col-md-4">

      <div class="box" id="Overview">
        <h2>
          <i class="fa fa-cogs fa-fw"></i>
          Account Setup
        </h2>

        <ul class="list-unstyled withIcons">
          <li><i class="list-icon material-icons fa-fw">settings</i><a href="{{basePath}}#Preferences">Main Settings</a></li>
          <li><i class="list-icon material-icons fa-fw">account_balance</i><a href="{{basePath}}#Tax">Tax</a></li>
          <li><i class="list-icon material-icons fa-fw">label</i><a href="{{basePath}}#Tag">Tag</a></li>
          <li><i class="list-icon material-icons fa-fw">people</i><a href="{{basePath}}#User">Users</a></li>
          <li><i class="list-icon material-icons fa-fw">local_play</i><a href="{{basePath}}#Role">Roles</a></li>
          <li><i class="list-icon material-icons fa-fw">group_add</i><a href="{{basePath}}#Team">Team</a></li>
        </ul>
      </div>
    </div>
    <div class="col-md-4">


      <div class="box" id="Templates">
        <h2>
          <i class="fa fa-files-o fa-fw"></i>
          Templates
        </h2>

        <ul class="list-unstyled">
          <li><i class="list-icon material-icons fa-fw">mail_outline</i><a href="{{basePath}}#EmailTemplate">Email</a></li>
        </ul>
      </div>
    </div>
  </div>
{{/ifEqual}}
</div>