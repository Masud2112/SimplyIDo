<nav class="navbar">
    <!-- Logo Area -->
    <div class="navbar-header">
        <a href="{{basePath}}"><img class="logo-expand" src="{{logoSrc}}" class="logo"></a>
        <a href="{{basePath}}"><img class="logo-collapse" src="{{basePath}}client/img/logo-collapse.png" class="logo"></a>
            <!-- <p>OSCAR</p> -->
    </div>
    <!-- /.navbar-header -->
    <!-- Left Menu & Sidebar Toggle -->
    <ul class="nav navbar-nav">
        <li class="sidebar-toggle"><a href="javascript:void(0)" class="ripple"><i class="material-icons list-icon">menu</i></a>
        </li>
    </ul>
    <!-- /.navbar-left -->
    <!-- Search Form -->
    <form class="navbar-search d-none d-sm-block" role="search"><i class="material-icons list-icon">search</i> 
        <input type="text" class="search-query" placeholder="Search anything..."> <a href="javascript:void(0);" class="remove-focus"><i class="material-icons">clear</i></a>
    </form>
    <!-- /.navbar-search -->
    <div class="spacer"></div>
    {{#ifEqual userType 'accountowner'}}
    <div class="page-title-right d-inline-flex invite-events">
        <div class="d-none d-sm-inline-flex justify-center align-items-center"><a href="#" class="btn btn-outline-primary mr-l-20 btn-sm btn-rounded hidden-xs hidden-sm ripple" target="_blank">Invited Events</a>
        </div>
    </div>
    <div class="btn-list dropdown d-none d-md-flex"><a href="#" class="btn btn-primary dropdown-toggle ripple" data-toggle="dropdown"><i class="material-icons list-icon fs-24">playlist_add</i> Create New</a>
        <div class="dropdown-menu dropdown-left animated flipInY"><span class="dropdown-header">Create new ...</span>
            {{#each quickCreateList}}
                <a href="#{{./this}}/create" data-name="{{./this}}" data-action="quick-create" class="dropdown-item">{{translate this category='scopeNames'}}</a>
            {{/each}}
        </div>
    </div>
    <ul class="nav navbar-nav d-none d-lg-block">
        <li class="dropdown"><a href="#" class="dropdown-toggle ripple" data-toggle="dropdown"><i class="material-icons list-icon">mail_outline</i> <span class="badge badge-border bg-primary">2</span></a>
            <div class="dropdown-menu dropdown-left dropdown-card dropdown-card-dark animated flipInY">
                <div class="card">
                    <header class="card-header">New messages <span class="mr-l-10 badge badge-border badge-border-inverted bg-primary">2</span>
                    </header>
                    <ul class="list-unstyled dropdown-list-group ps ps--theme_default" data-ps-id="73ef0630-8423-169b-3a34-2a3fd15ac997">
                        <li><a href="#" class="media"><span class="d-flex user--online thumb-xs"><img src="{{basePath}}client/img/user3.jpg" class="rounded-circle" alt=""> </span><span class="media-body"><span class="media-heading">Steve Smith</span> <small>09.04.2017 @ 2:30pm</small> <span class="media-content">Thank you for sending the quotation. We have some ...</span></span></a>
                        </li>
                        <li><a href="#" class="media"><span class="d-flex user--offline thumb-xs"><img src="{{basePath}}client/img/user6.jpg" class="rounded-circle" alt=""> </span><span class="media-body"><span class="media-heading">Emily Lee</span> <small>09.04.2017 @ 2:15pm</small> <span class="media-content">Hi Scott!</span></span></a>
                        </li>
                        <li><a href="#" class="media"><span class="d-flex user--online thumb-xs"><img src="{{basePath}}client/img/user2.jpg" class="rounded-circle" alt=""> </span><span class="media-body"><span class="media-heading">Christopher Palmer</span> <small>09.44.2017 @ 2:45pm</small> <span class="media-content">Like the card design and theme. The content also looks great ...</span></span></a>
                        </li>
                    <div class="ps__scrollbar-x-rail" style="left: 0px; bottom: 0px;"><div class="ps__scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__scrollbar-y-rail" style="top: 0px; right: 0px;"><div class="ps__scrollbar-y" tabindex="0" style="top: 0px; height: 0px;"></div></div></ul>
                    <!-- /.dropdown-list-group -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.dropdown-menu -->
        </li>
        <!-- /.dropdown -->
        <li class="dropdown">{{{notificationsBadge}}}
            <!-- <div class="dropdown-menu dropdown-left dropdown-card dropdown-card-dark animated flipInY">
                <div class="card">
                    <header class="card-header">New notifications <span class="mr-l-10 badge badge-border badge-border-inverted bg-primary">3</span>
                    </header>
                    <ul class="list-unstyled dropdown-list-group ps ps--theme_default" data-ps-id="8d3e9051-37c4-ff5e-ecd2-e15f4086da68">
                        <li><a href="#" class="media"><span class="d-flex"><i class="material-icons list-icon">check</i> </span><span class="media-body"><span class="media-heading">To Do</span> <small>09.04.2017 @ 3:00pm</small> <span class="media-content">Contract sent to Kimberley for review</span></span></a>
                        </li>
                        <li><a href="#" class="media"><span class="d-flex"><i class="material-icons list-icon">event_available</i> </span><span class="media-body"><span class="media-heading">Invitation Accepted</span> <small>09.04.2017 @ 3:30pm</small> <span class="media-content">Meeting with Nathan McCullum on Friday 8 AM ...</span></span></a>
                        </li>
                        <li><a href="#" class="media"><span class="d-flex"><i class="material-icons list-icon">check</i> </span><span class="media-body"><span class="media-heading">To Do</span> <small>09.04.2017 @ 3:35pm</small> <span class="media-content">Payment received from Tim &amp; Sarah wedding</span></span></a>
                        </li>
                    <div class="ps__scrollbar-x-rail" style="left: 0px; bottom: 0px;"><div class="ps__scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__scrollbar-y-rail" style="top: 0px; right: 0px;"><div class="ps__scrollbar-y" tabindex="0" style="top: 0px; height: 0px;"></div></div></ul>
                    
                </div>
               
            </div> -->
            <!-- /.dropdown-menu -->
        </li>
    </ul>
    {{/ifEqual}}
    <ul class="nav navbar-nav">
        <li class="dropdown"><a href="#" class="dropdown-toggle ripple" data-toggle="dropdown" aria-expanded="false"><span class="avatar thumb-sm"><img src="{{userLogoSrc}}" class="rounded-circle" alt=""> <i class="material-icons list-icon">expand_more</i></span></a>
        <div class="dropdown-menu dropdown-left dropdown-card dropdown-card-wide dropdown-card-dark text-inverse">
            <div class="card">
                <header class="card-heading-extra">
                    <div class="row">
                        <div class="col-8">
                            <h5 class="mr-b-10 sub-heading-font-family fw-300">{{userName}}</h5>
                        </div>
                        <div class="col-4 d-flex justify-content-end"><a href="#logout" class="mr-t-10"><i class="material-icons list-icon">power_settings_new</i> Logout</a>
                        </div>
                        <!-- /.col-4 -->
                    </div>
                    <!-- /.row -->
                </header>
                <ul class="list-unstyled dropdown-list-group ps ps--theme_default ps--active-y" data-ps-id="70bacf70-f6a2-3ef5-4261-594f7ad0f38f">
                    <li><a href="{{basePath}}#User/edit/{{userId}}" class="nav-link">My Profile</a></li>
                    {{#each menu}}
                        {{#unless divider}}
                            <li><a href="{{#if link}}{{link}}{{else}}javascript:{{/if}}" class="nav-link{{#if action}} action{{/if}}"{{#if action}} data-action="{{action}}"{{/if}}>{{label}}</a></li>
                        {{/unless}}
                    {{/each}}
                </ul>
            </div>
        </div>
    </li>
    </ul>
</nav>
<div class="content-wrapper">
<aside class="site-sidebar scrollbar-enabled clearfix">
    <div class=" navbar-collapse navbar-body">
    <nav class="sidebar-nav">
        <ul class="nav in side-menu">
            {{#ifEqual userType 'admin'}}

                {{#each tabDefsList}}
                {{#unless isInMore}}
                <li data-name="{{name}}" class="not-in-more"><a href="{{link}}" class="nav-link"><span class="full-label"><i class="list-icon material-icons">{{icon}}</i><span class="hide-menu">{{label}}</span></span><span class="short-label" title="{{label}}">{{shortLabel}}</span></a></li>
                {{/unless}}
                {{/each}}
            {{/ifEqual}}
            {{#ifEqual userType 'sidoadmin'}}
                <li data-name="Account" class="not-in-more"><a href="{{basePath}}#Account" class="nav-link"><span class="full-label"><i class="list-icon material-icons">account_box</i><span class="hide-menu">Accounts </span></span></a></li>
                <li data-name="Package" class="not-in-more"><a href="{{basePath}}#Package" class="nav-link"><span class="full-label"><i class="list-icon material-icons">attach_money</i><span class="hide-menu">Package</span></span></a></li>
                <li data-name="Template" class="not-in-more"><a href="javascript:void(0);" class="ripple" aria-expanded="true"><span class="full-label"><i class="list-icon material-icons">featured_play_list</i><span class="hide-menu">Template</span></span></a>
                    <ul class="list-unstyled sub-menu collapse in" aria-expanded="true" style="">
                        <li><a href="{{basePath}}#EmailTemplate">Email</a>
                        </li>
                    </ul>
                </li>
               
                <li data-name="Tag" class="not-in-more"><a href="{{basePath}}#Tag" class="nav-link"><span class="full-label"><i class="list-icon material-icons">label</i><span class="hide-menu">Tag</span></span></a></li>
                <li data-name="Tax" class="not-in-more"><a href="{{basePath}}#Tax" class="nav-link"><span class="full-label"><i class="list-icon material-icons">account_balance</i><span class="hide-menu">Tax</span></span></a></li>
            {{/ifEqual}}
            {{#ifEqual userType 'accountowner'}}
                <li data-name="Admin" class="not-in-more"><a href="{{basePath}}#Admin" class="nav-link"><span class="full-label"><i class="list-icon material-icons">account_circle</i><span class="hide-menu">Account Setting</span></span></a></li>
                <li data-name="Email" class="not-in-more"><a href="{{basePath}}#Email" class="nav-link"><span class="full-label"><i class="list-icon material-icons">mail_outline</i><span class="hide-menu">Emails</span></span></a></li>
            {{/ifEqual}}
            <!-- <li class="dropdown more">
                <a id="nav-more-tabs-dropdown" class="dropdown-toggle" data-toggle="dropdown" href="#"><span class="more-label">{{translate 'More'}} <b class="caret"></b></span><span class="glyphicon glyphicon glyphicon-option-horizontal more-icon"></span></a>
                <ul class="dropdown-menu" role="menu" aria-labelledby="nav-more-tabs-dropdown">
                {{#each tabDefsList}}
                {{#if isInMore}}
                    <li data-name="{{name}}" class="in-more"><a href="{{link}}" class="nav-link"><span class="full-label">{{label}}</span></a></li>
                {{/if}}
                {{/each}}
                </ul>
            </li> -->
        </ul>
    </nav>
    </div>
</aside>