<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Defined styling areas for the theme style feature
 * Those string are not translated to keep the language file neat
 * @param  string $type
 * @return array
 */
function get_styling_areas($type = 'admin')
{
    /**
     * Added By : Vaidehi
     * Dt : 10/23/2017
     * to display custom theme style for all users except sido admin and super admin
     */
    $session_data = get_session_data();
    $is_sido_admin = isset($session_data['is_sido_admin']) ? $session_data['is_sido_admin'] : 2;
    $is_admin = isset($session_data['is_admin']) ? $session_data['is_admin'] : 2;

    if($is_sido_admin == 0 && $is_admin == 0) {
        $areas = array(
            'general' => array(
                array(
                    'name' => 'Top Header Background Color',
                    'id' => 'top-header',
                    'target' => '.admin #header, 
                    #side-menutop, 
                    #side-menutop li
                    #side-menutop li:hover, 
                    #side-menutop li a,
                    #side-menutop li > a:focus,
                    .admin #side-menutop li.active > a',
                    'css' => 'background-color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Top Header Links Color',
                    'id' => 'top-header-links',
                    'target' => '.admin .navbar-nav > li > a, 
                    ul.mobile-icon-menu>li>a, 
                    .mobile-menu-toggle, 
                    .open-customizer-mobile',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Table Header Text Color',
                    'id' => 'table-headings',
                    'target' => 'table.dataTable thead .sorting, 
                    table.dataTable thead .sorting_asc, 
                    table.dataTable thead .sorting_desc, 
                    table.dataTable thead .sorting_asc_disabled, 
                    table.dataTable thead .sorting_desc_disabled,
                    .sdtheme.table.dataTable thead .sorting, 
                    .sdtheme.table.dataTable thead .sorting_asc, 
                    .sdtheme.table.dataTable thead .sorting_desc, 
                    .sdtheme.table.dataTable thead .sorting_asc_disabled, 
                    .sdtheme.table.dataTable thead .sorting_desc_disabled',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<table class="table dataTable"><thead><tr><th style="border-bottom: 1px solid #f0f0f0" class="sorting">Example Heading 1</th><th style="border-bottom: 1px solid #f0f0f0" class="sorting">Example Heading 2</th></tr></thead></table>'
                ),
                array(
                    'name' => 'Table Header Background Color',
                    'id' => 'table-items-heading',
                    //'target' => '.table > thead > tr > th',
                    'target' => '.table > thead > tr > th, 
                    table.dataTable thead .sorting, 
                    table.dataTable thead .sorting_asc, 
                    table.dataTable thead .sorting_desc, 
                    table.dataTable thead .sorting_asc_disabled, 
                    table.dataTable thead .sorting_desc_disabled,
                    .sdtheme.table > thead > tr > th, 
                    .sdtheme.table.dataTable thead .sorting, 
                    .sdtheme.table.dataTable thead .sorting_asc, 
                    .sdtheme.table.dataTable thead .sorting_desc, 
                    .sdtheme.table.dataTable thead .sorting_asc_disabled, 
                    .sdtheme.table.dataTable thead .sorting_desc_disabled',
                    'css' => 'background',
                    'additional_selectors' => '',
                    'example' => '<table class="table dataTable"><thead><tr><th style="border-bottom: 1px solid #f0f0f0" class="sorting">Example Heading 1</th><th style="border-bottom: 1px solid #f0f0f0" class="sorting">Example Heading 2</th></tr></thead></table>'
                    // 'example' => '<table class="table"><thead><tr><th>Example Heading 1</th><th>Example Heading 2</th></tr></thead></table>'
                ),
                array(
                    'name' => 'Table Odd Row Color',
                    'id' => 'table-items-odd',
                    //'target' => '.table > thead > tr > th',
                    'target' => '.table>tbody>tr:nth-of-type(odd),
                    .sdtheme.table-striped>tbody>tr:nth-of-type(odd)',
                    'css' => 'background',
                    'additional_selectors' => '',
                    'example' => '<table class="table dataTable"><tbody><tr><td>Data 1</td><td >Data 2</td></tr><tr><td>Data 1</td><td >Data 2</td></tr></tbody></table>'
                ),
                array(
                    'name' => 'Table Even Row Color',
                    'id' => 'table-items-even',
                    //'target' => '.table > thead > tr > th',
                    'target' => '.table>tbody>tr:nth-of-type(even),
                    .sdtheme.table>tbody>tr:nth-of-type(even)',
                    'css' => 'background',
                    'additional_selectors' => '',
                    'example' => '<table class="table dataTable"><tbody><tr><td>Data 1</td><td >Data 2</td></tr><tr><td>Data 1</td><td >Data 2</td></tr></tbody></table>'
                ),
                array(
                    'name' => 'Table Row Hover Color',
                    'id' => 'table-items-row-hover',
                    //'target' => '.table > thead > tr > th',
                    'target' => '.table>tbody>tr:hover,
                    .sdtheme.table>tbody>tr:hover',
                    'css' => 'background',
                    'additional_selectors' => '',
                    'example' => '<table class="table dataTable"><tbody><tr><td>Data 1</td><td >Data 2</td></tr><tr><td>Data 1</td><td >Data 2</td></tr></tbody></table>'
                )/*,
                array(
                    'name' => 'Admin Login Background',
                    'id' => 'admin-login-background',
                    'target' => 'body.login_admin',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Text Muted',
                    'id' => 'text-muted',
                    'target' => '.text-muted',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<p>Example <span class="bold text-muted">text muted</span></p>'
                ),
                array(
                    'name' => 'Text Danger',
                    'id' => 'text-danger',
                    'target' => '.text-danger',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<p>Example <span class="bold text-danger">text danger</span></p>'
                ),
                array(
                    'name' => 'Text Warning',
                    'id' => 'text-warning',
                    'target' => '.text-warning',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<p>Example <span class="bold text-warning">text warning</span></p>'
                ),
                array(
                    'name' => 'Text Info',
                    'id' => 'text-info',
                    'target' => '.text-info',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<p>Example <span class="bold text-info">text info</span></p>'
                ),
                array(
                    'name' => 'Text Success',
                    'id' => 'text-success',
                    'target' => '.text-success',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<p>Example <span class="bold text-success">text success</span></p>'
                )*/
            ),
            'admin' => array(
                /*array(
                    'name' => 'Menu Links Color',
                    'id' => 'links-color',
                    'target' => 'a',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Menu Links Hover Color',
                    'id' => 'side-menu',
                    'target' => 'a:hover,a:focus',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),*/
                array(
                    'name' => 'Menu Background Color',
                    'id' => 'admin-menu',
                    'target' => '.admin #side-menu, 
                    .admin #side-menutop,
                    .admin #setup-menu',
                    'css' => 'background',
                    'additional_selectors' => 'body|background+#setup-menu-wrapper|background'
                ),
                array(
                    'name' => 'Text Color',
                    'id' => 'admin-menu-links',
                    'target' => '.admin #side-menu li a,
                    .admin #side-menutop li a,
                    #side-menutop li:focus,
                    .admin #setup-menu li a',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Submenu Background Color',
                    'id' => 'admin-menu-submenu-open',
                    'target' => '.admin #side-menu li .nav-second-level li,
                    .admin #setup-menu li .nav-second-level li',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Submenu Text Color',
                    'id' => 'admin-menu-submenu-links',
                    'target' => '.admin #side-menu li .nav-second-level li a,
                    .admin #setup-menu li .nav-second-level li a',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Selected Background Color',
                    'id' => 'admin-menu-active-item',
                    'target' => '
                    .admin #setup-menu li.active > a,
                    #side-menu li > a:hover,
                    #side-menu li > a:focus,
                    #setup-menu > li > a:hover,
                    #setup-menu > li > a:focus,
                    .bootstrap-select .dropdown-menu .selected a',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Selected Menu Text Color',
                    'id' => 'admin-menu-active-item-color',
                    'target' => '
                    .admin #side-menu li.active > a,
                    .admin #setup-menu li.active > a,
                    .admin #side-menutop li.active > a,
                    #side-menu.nav > li > a:hover,
                    #side-menu.nav > li > a:focus,
                    #side-menutop li a:hover',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Selected Submenu Background Color',
                    'id' => 'admin-menu-active-subitem',
                    'target' => '.admin #side-menu li .nav-second-level li.active a,.admin #setup-menu li .nav-second-level li.active a, .admin #side-menutop li .nav-second-level li.active a',
                    'css' => 'background',
                    'additional_selectors' => ''
                )//,
                // array(
                //     'name' => 'Top Header Links Border Color (border right)',
                //     'id' => 'top-header-border',
                //     'target' => '.admin #header li.icon',
                //     'css' => 'border-right-color',
                //     'additional_selectors' => '#top_search_button button|border-right-color+.hide-menu|border-right-color'
                // )

            ),
            'customers' => array(
                array(
                    'name' => 'Navigation Background Color',
                    'id' => 'customers-navigation',
                    'target' => '.customers .navbar-default',
                    'css' => 'background-color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Navigation Links Color',
                    'id' => 'customers-navigation-links',
                    'target' => '.customers .navbar-default .navbar-nav>li>a',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Invoice/Estimate HTML View Top Header Background Color',
                    'id' => 'html-view-top-header',
                    'target' => '.viewinvoice .page-pdf-html-logo,.viewestimate .page-pdf-html-logo',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Proposal view (right side background color)',
                    'id' => 'proposal-view',
                    'target' => '.proposal-view .proposal-right',
                    'css' => 'background',
                    'additional_selectors' => ''
                )
            ),

            'tabs' => array(
                array(
                    'name' => 'Tabs Background Color',
                    'id' => 'tabs-bg',
                    'target' => '.nav-tabs',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Tabs Links Color',
                    'id' => 'tabs-links',
                    'target' => '.nav-tabs>li>a',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Tabs Link Active/Hover Color',
                    'id' => 'tabs-links-active-hover',
                    'target' => '.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover, .nav-tabs>li>a:focus, .nav-tabs>li>a:hover',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Tabs Border Color',
                    'id' => 'tabs-border',
                    'target' => '.nav-tabs',
                    'css' => 'border-color',
                    'additional_selectors' => '.navbar-pills.nav-tabs>li>a:focus,.navbar-pills.nav-tabs>li>a:hover|border-color'
                ),
                array(
                    'name' => 'Tabs Active Border Color',
                    'id' => 'tabs-active-border',
                    'target' => '.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover, .nav-tabs>li>a:focus, .nav-tabs>li>a:hover',
                    'css' => 'border-bottom-color',
                    'additional_selectors' => ''
                )
            ),
            'modals' => array(
                array(
                    'name' => 'Heading Background',
                    'id' => 'modal-heading',
                    'target' => '.modal-header',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Heading Color',
                    'id' => 'modal-heading-color',
                    'target' => '.modal-header .modal-title',
                    'css' => 'color',
                    'additional_selectors' => ''
                )
            ),
            'buttons' => array(
                array(
                    'name' => 'Save Button Text Color',
                    'id' => 'btn-info',
                    'target' => '.btn-info, 
                    .btn-primary, 
                    .navbar-nav>li>a.profile i,
                    .pagination>.active>a,
                    .pagination>.active>a|border-color, 
                    .pagination>.active>a:focus, 
                    .pagination>.active>a:hover, 
                    .pagination>.active>span, 
                    .pagination>.active>span:focus, 
                    .pagination>.active>span:hover',
                    'css' => 'color',
                    'additional_selectors' => '.btn-info|color',
                    'example' => '<button type="button" class="btn btn-info">Save</button>'
                ),
                array(
                    'name' => 'Save Button Background Color',
                    'id' => 'btn-info-background',
                    'target' => '.btn-info, 
                    .btn-primary,
                    .open>.dropdown-toggle.btn-info',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-info|background-color',
                    'example' => '<button type="button" class="btn btn-info">Save</button>'
                ),
                array(
                    'name' => 'Save Button Hover Text Color',
                    'id' => 'btn-info-hover',
                    'target' => '.btn-info:hover, 
                    .btn-primary:hover,
                    .open>.dropdown-toggle.btn-info:hover,
                    .open>.dropdown-toggle.btn-info.focus,
                    .open>.dropdown-toggle.btn-info:focus,
                    .btn-info.focus,
                    .btn-info:focus,
                    .open>.dropdown-toggle.btn-info,
                    .btn-info.disabled, 
                    .btn-info.disabled.active, 
                    .btn-info.disabled.focus, 
                    .btn-info.disabled:active,
                    .btn-info.disabled:focus, 
                    .btn-info.disabled:hover, 
                    .btn-info[disabled], 
                    .btn-info[disabled].active, 
                    .btn-info[disabled].focus, 
                    .btn-info[disabled]:active, 
                    .btn-info[disabled]:focus, 
                    .btn-info[disabled]:hover, 
                    fieldset[disabled] .btn-info, 
                    fieldset[disabled] .btn-info.active, 
                    fieldset[disabled] .btn-info.focus, 
                    fieldset[disabled] .btn-info:active, 
                    fieldset[disabled] .btn-info:focus, 
                    fieldset[disabled] .btn-info:hover',
                    'css' => 'color',
                    'additional_selectors' => '.btn-info:hover|color',
                    'example' => '<button type="button" class="btn btn-info">Save</button>'
                ),
                array(
                    'name' => 'Save Button Hover Background Color',
                    'id' => 'btn-info-hover-background',
                    'target' => '.btn-info:hover, 
                    .btn-primary:hover,
                    .open>.dropdown-toggle.btn-info:hover,
                    .open>.dropdown-toggle.btn-info.focus,
                    .open>.dropdown-toggle.btn-info:focus,
                    .btn-info.focus,
                    .btn-info:focus,
                    .open>.dropdown-toggle.btn-info,
                    .btn-info.disabled, 
                    .btn-info.disabled.active, 
                    .btn-info.disabled.focus, 
                    .btn-info.disabled:active,
                    .btn-info.disabled:focus, 
                    .btn-info.disabled:hover, 
                    .btn-info[disabled], 
                    .btn-info[disabled].active, 
                    .btn-info[disabled].focus, 
                    .btn-info[disabled]:active, 
                    .btn-info[disabled]:focus, 
                    .btn-info[disabled]:hover, 
                    fieldset[disabled] .btn-info, 
                    fieldset[disabled] .btn-info.active, 
                    fieldset[disabled] .btn-info.focus, 
                    fieldset[disabled] .btn-info:active, 
                    fieldset[disabled] .btn-info:focus, 
                    fieldset[disabled] .btn-info:hover',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-info:hover|background-color',
                    'example' => '<button type="button" class="btn btn-info">Save</button>'
                ),
                array(
                    'name' => 'Edit',
                    'id' => 'btn-orange',
                    'target' => '.btn-orange, 
                    .btn-orange.focus, .btn-orange:focus,
                    .btn-orange:hover,
                    .btn-orange.active, 
                    .btn-orange:active, 
                    .open>.dropdown-toggle.btn-orange,
                    .btn-orange.active.focus, 
                    .btn-orange.active:focus, 
                    .btn-orange.active:hover, 
                    .btn-orange:active.focus, 
                    .btn-orange:active:focus, 
                    .btn-orange:active:hover, 
                    .open>.dropdown-toggle.btn-orange.focus, 
                    .open>.dropdown-toggle.btn-orange:focus, 
                    .open>.dropdown-toggle.btn-orange:hover,
                    .btn-orange.disabled, 
                    .btn-orange.disabled.active, 
                    .btn-orange.disabled.focus, 
                    .btn-orange.disabled:active, 
                    .btn-orange.disabled:focus, 
                    .btn-orange.disabled:hover, 
                    .btn-orange[disabled], 
                    .btn-orange[disabled].active, 
                    .btn-orange[disabled].focus, 
                    .btn-orange[disabled]:active, 
                    .btn-orange[disabled]:focus, 
                    .btn-orange[disabled]:hover, 
                    fieldset[disabled] .btn-orange, 
                    fieldset[disabled] .btn-orange.active, 
                    fieldset[disabled] .btn-orange.focus, 
                    fieldset[disabled] .btn-orange:active, 
                    fieldset[disabled] .btn-orange:focus, 
                    fieldset[disabled] .btn-orange:hover',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-orange|border-color',
                    'example' => '<a href="#" class="btn btn-orange btn-icon"><i class="fa fa-pencil-square-o"></i></a>'
                ),
                array(
                    'name' => 'Cancel Button Text Color',
                    'id' => 'btn-default',
                    'target' => '.btn-default',
                    'css' => 'color',
                    'additional_selectors' => '.btn-default|color',
                    'example' => '<button type="button" class="btn btn-default">Cancel</button>'
                ),
                array(
                    'name' => 'Cancel Button Background Color',
                    'id' => 'btn-default-background',
                    'target' => '.btn-default',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-default|background-color',
                    'example' => '<button type="button" class="btn btn-default">Cancel</button>'
                ),
                array(
                    'name' => 'Cancel Button Hover Text Color',
                    'id' => 'btn-default-hover',
                    'target' => '.btn-default:hover',
                    'css' => 'color',
                    'additional_selectors' => '.btn-default:hover|color',
                    'example' => '<button type="button" class="btn btn-default">Cancel</button>'
                ),
                array(
                    'name' => 'Cancel Button Hover Background Color',
                    'id' => 'btn-default-hover-background',
                    'target' => '.btn-default:hover',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-default:hover|background-color',
                    'example' => '<button type="button" class="btn btn-default">Cancel</button>'
                ),
                array(
                    'name' => 'Delete Button Text Color',
                    'id' => 'btn-danger',
                    'target' => '.btn-danger,
                    .open>.dropdown-toggle.btn-danger',
                    'css' => 'color',
                    'additional_selectors' => '.btn-danger|color',
                    'example' => '<a href="#" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>'
                ),
                array(
                    'name' => 'Delete Button Background Color',
                    'id' => 'btn-danger-background',
                    'target' => '.btn-danger,
                    .open>.dropdown-toggle.btn-danger',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-danger|background-color',
                    'example' => '<a href="#" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>'
                ),
                array(
                    'name' => 'Delete Button Hover Text Color',
                    'id' => 'btn-danger-hover',
                    'target' => '.btn-danger:hover,
                    .btn-danger.active:focus,
                    .btn-danger.active.focus,
                    .open>.dropdown-toggle.btn-danger.focus,
                    .open>.dropdown-toggle.btn-danger:focus,
                    .open>.dropdown-toggle.btn-danger:hover,
                    .btn-danger.focus, 
                    .btn-danger:focus,
                    .btn-danger.active, 
                    .btn-danger:active, 
                    .open>.dropdown-toggle.btn-danger,
                    .btn-danger.active:hover, 
                    .btn-danger:active.focus, 
                    .btn-danger:active:focus, 
                    .btn-danger:active:hover,
                    .btn-danger.disabled, 
                    .btn-danger.disabled.active, 
                    .btn-danger.disabled.focus, 
                    .btn-danger.disabled:active, 
                    .btn-danger.disabled:focus, 
                    .btn-danger.disabled:hover, 
                    .btn-danger[disabled], 
                    .btn-danger[disabled].active, 
                    .btn-danger[disabled].focus, 
                    .btn-danger[disabled]:active, 
                    .btn-danger[disabled]:focus, 
                    .btn-danger[disabled]:hover, 
                    fieldset[disabled] .btn-danger, 
                    fieldset[disabled] .btn-danger.active, 
                    fieldset[disabled] .btn-danger.focus, 
                    fieldset[disabled] .btn-danger:active, 
                    fieldset[disabled] .btn-danger:focus, 
                    fieldset[disabled] .btn-danger:hover',
                    'css' => 'color',
                    'additional_selectors' => '.btn-danger:hover|color',
                    'example' => '<a href="#" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>'
                ),
                array(
                    'name' => 'Delete Button Hover Background Color',
                    'id' => 'btn-danger-hover-background',
                    'target' => '.btn-danger:hover,
                    .btn-danger.active:focus,
                    .btn-danger.active.focus,
                    .open>.dropdown-toggle.btn-danger.focus,
                    .open>.dropdown-toggle.btn-danger:focus,
                    .open>.dropdown-toggle.btn-danger:hover,
                    .btn-danger.focus, 
                    .btn-danger:focus,
                    .btn-danger.active, 
                    .btn-danger:active, 
                    .open>.dropdown-toggle.btn-danger,
                    .btn-danger.active:hover, 
                    .btn-danger:active.focus, 
                    .btn-danger:active:focus, 
                    .btn-danger:active:hover,
                    fieldset[disabled] .btn-danger, 
                    fieldset[disabled] .btn-danger.active, 
                    fieldset[disabled] .btn-danger.focus, 
                    fieldset[disabled] .btn-danger:active, 
                    fieldset[disabled] .btn-danger:focus, 
                    fieldset[disabled] .btn-danger:hover',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-danger:hover|background-color',
                    'example' => '<a href="#" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>'
                ),
                array(
                    'name' => 'Create New on Header',
                    'id' => 'quick-links',
                    'target' => '.quick-links #dropdownQuickLinks',
                    'css' => 'background-color',
                    'additional_selectors' => '',//'.btn-info|border-color',
                    'example' => '<ul class="nav navbar-nav"><li class="quick-links"><a href="javascript:void(0);" class="dropdown-toggle" id="dropdownQuickLinks"><i class="fa fa-plus-square" aria-hidden="true"></i> &nbsp; Create New</a></li></ul>'
                ),
                array(
                    'name' => 'Icons',
                    'id' => 'fa',
                    'target' => '.lead-tool-block .tabs-bordered .nav-tabs a i, .countdown-amount,.pinned,.favorite',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<div class="leaddashboard lead-tool-block"><div class="tabs tabs-bordered "><ul class="nav nav-tabs" style="margin-bottom:-25px;"> <li class="nav-item1"><a class="nav-link" href="#" style="height:60px; margin-top: -20px;"> <i class="fa fa-clock-o" style="display:unset"></i></a></li></ul></div></div>'
                )
            )
        );
    } else if($is_sido_admin == 1) {
        $areas = array(
            'general' => array(
                array(
                    'name' => 'Top Header Background Color',
                    'id' => 'top-header',
                    'target' => '.admin #header, 
                    #side-menutop, 
                    #side-menutop li
                    #side-menutop li:hover, 
                    #side-menutop li a,
                    #side-menutop li > a:focus,
                    .admin #side-menutop li.active > a',
                    'css' => 'background-color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Top Header Links Color',
                    'id' => 'top-header-links',
                    'target' => '.admin .navbar-nav > li > a, 
                    ul.mobile-icon-menu>li>a, 
                    .mobile-menu-toggle, 
                    .open-customizer-mobile',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Table Header Text Color',
                    'id' => 'table-headings',
                    'target' => 'table.dataTable thead .sorting, 
                    table.dataTable thead .sorting_asc, 
                    table.dataTable thead .sorting_desc, 
                    table.dataTable thead .sorting_asc_disabled, 
                    table.dataTable thead .sorting_desc_disabled,
                    .sdtheme.table.dataTable thead .sorting, 
                    .sdtheme.table.dataTable thead .sorting_asc, 
                    .sdtheme.table.dataTable thead .sorting_desc, 
                    .sdtheme.table.dataTable thead .sorting_asc_disabled, 
                    .sdtheme.table.dataTable thead .sorting_desc_disabled',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<table class="table dataTable"><thead><tr><th style="border-bottom: 1px solid #f0f0f0" class="sorting">Example Heading 1</th><th style="border-bottom: 1px solid #f0f0f0" class="sorting">Example Heading 2</th></tr></thead></table>'
                ),
                array(
                    'name' => 'Table Header Background Color',
                    'id' => 'table-items-heading',
                    //'target' => '.table > thead > tr > th',
                    'target' => '.table > thead > tr > th, 
                    table.dataTable thead .sorting, 
                    table.dataTable thead .sorting_asc, 
                    table.dataTable thead .sorting_desc, 
                    table.dataTable thead .sorting_asc_disabled, 
                    table.dataTable thead .sorting_desc_disabled,
                    .sdtheme.table > thead > tr > th, 
                    .sdtheme.table.dataTable thead .sorting, 
                    .sdtheme.table.dataTable thead .sorting_asc, 
                    .sdtheme.table.dataTable thead .sorting_desc, 
                    .sdtheme.table.dataTable thead .sorting_asc_disabled, 
                    .sdtheme.table.dataTable thead .sorting_desc_disabled',
                    'css' => 'background',
                    'additional_selectors' => '',
                    'example' => '<table class="table dataTable"><thead><tr><th style="border-bottom: 1px solid #f0f0f0" class="sorting">Example Heading 1</th><th style="border-bottom: 1px solid #f0f0f0" class="sorting">Example Heading 2</th></tr></thead></table>'
                    // 'example' => '<table class="table"><thead><tr><th>Example Heading 1</th><th>Example Heading 2</th></tr></thead></table>'
                ),
                array(
                    'name' => 'Table Odd Row Color',
                    'id' => 'table-items-odd',
                    //'target' => '.table > thead > tr > th',
                    'target' => '.table>tbody>tr:nth-of-type(odd),
                    .sdtheme.table-striped>tbody>tr:nth-of-type(odd)',
                    'css' => 'background',
                    'additional_selectors' => '',
                    'example' => '<table class="table dataTable"><tbody><tr><td>Data 1</td><td >Data 2</td></tr><tr><td>Data 1</td><td >Data 2</td></tr></tbody></table>'
                ),
                array(
                    'name' => 'Table Even Row Color',
                    'id' => 'table-items-even',
                    //'target' => '.table > thead > tr > th',
                    'target' => '.table>tbody>tr:nth-of-type(even),
                    .sdtheme.table>tbody>tr:nth-of-type(even)',
                    'css' => 'background',
                    'additional_selectors' => '',
                    'example' => '<table class="table dataTable"><tbody><tr><td>Data 1</td><td >Data 2</td></tr><tr><td>Data 1</td><td >Data 2</td></tr></tbody></table>'
                ),
                array(
                    'name' => 'Table Row Hover Color',
                    'id' => 'table-items-row-hover',
                    //'target' => '.table > thead > tr > th',
                    'target' => '.table>tbody>tr:hover,
                    .sdtheme.table>tbody>tr:hover',
                    'css' => 'background',
                    'additional_selectors' => '',
                    'example' => '<table class="table dataTable"><tbody><tr><td>Data 1</td><td >Data 2</td></tr><tr><td>Data 1</td><td >Data 2</td></tr></tbody></table>'
                )/*,
                array(
                    'name' => 'Admin Login Background',
                    'id' => 'admin-login-background',
                    'target' => 'body.login_admin',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Text Muted',
                    'id' => 'text-muted',
                    'target' => '.text-muted',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<p>Example <span class="bold text-muted">text muted</span></p>'
                ),
                array(
                    'name' => 'Text Danger',
                    'id' => 'text-danger',
                    'target' => '.text-danger',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<p>Example <span class="bold text-danger">text danger</span></p>'
                ),
                array(
                    'name' => 'Text Warning',
                    'id' => 'text-warning',
                    'target' => '.text-warning',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<p>Example <span class="bold text-warning">text warning</span></p>'
                ),
                array(
                    'name' => 'Text Info',
                    'id' => 'text-info',
                    'target' => '.text-info',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<p>Example <span class="bold text-info">text info</span></p>'
                ),
                array(
                    'name' => 'Text Success',
                    'id' => 'text-success',
                    'target' => '.text-success',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<p>Example <span class="bold text-success">text success</span></p>'
                )*/
            ),
            'admin' => array(
                /*array(
                    'name' => 'Menu Links Color',
                    'id' => 'links-color',
                    'target' => 'a',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Menu Links Hover Color',
                    'id' => 'side-menu',
                    'target' => 'a:hover,a:focus',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),*/
                array(
                    'name' => 'Menu Background Color',
                    'id' => 'admin-menu',
                    'target' => '.admin #side-menu, 
                    .admin #side-menutop,
                    .admin #setup-menu',
                    'css' => 'background',
                    'additional_selectors' => 'body|background+#setup-menu-wrapper|background'
                ),
                array(
                    'name' => 'Text Color',
                    'id' => 'admin-menu-links',
                    'target' => '.admin #side-menu li a,
                    .admin #side-menutop li a,
                    #side-menutop li:focus,
                    .admin #setup-menu li a',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Submenu Background Color',
                    'id' => 'admin-menu-submenu-open',
                    'target' => '.admin #side-menu li .nav-second-level li,
                    .admin #setup-menu li .nav-second-level li',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Submenu Text Color',
                    'id' => 'admin-menu-submenu-links',
                    'target' => '.admin #side-menu li .nav-second-level li a,
                    .admin #setup-menu li .nav-second-level li a',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Selected Background Color',
                    'id' => 'admin-menu-active-item',
                    'target' => '
                    .admin #setup-menu li.active > a,
                    #side-menu li > a:hover,
                    #side-menu li > a:focus,
                    #setup-menu > li > a:hover,
                    #setup-menu > li > a:focus,
                    .bootstrap-select .dropdown-menu .selected a',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Selected Menu Text Color',
                    'id' => 'admin-menu-active-item-color',
                    'target' => '
                    .admin #side-menu li.active > a,
                    .admin #setup-menu li.active > a,
                    .admin #side-menutop li.active > a,
                    #side-menu.nav > li > a:hover,
                    #side-menu.nav > li > a:focus,
                    #side-menutop li a:hover',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Selected Submenu Background Color',
                    'id' => 'admin-menu-active-subitem',
                    'target' => '.admin #side-menu li .nav-second-level li.active a,.admin #setup-menu li .nav-second-level li.active a, .admin #side-menutop li .nav-second-level li.active a',
                    'css' => 'background',
                    'additional_selectors' => ''
                )//,
                // array(
                //     'name' => 'Top Header Links Border Color (border right)',
                //     'id' => 'top-header-border',
                //     'target' => '.admin #header li.icon',
                //     'css' => 'border-right-color',
                //     'additional_selectors' => '#top_search_button button|border-right-color+.hide-menu|border-right-color'
                // )

            ),
            'customers' => array(
                array(
                    'name' => 'Navigation Background Color',
                    'id' => 'customers-navigation',
                    'target' => '.customers .navbar-default',
                    'css' => 'background-color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Navigation Links Color',
                    'id' => 'customers-navigation-links',
                    'target' => '.customers .navbar-default .navbar-nav>li>a',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Invoice/Estimate HTML View Top Header Background Color',
                    'id' => 'html-view-top-header',
                    'target' => '.viewinvoice .page-pdf-html-logo,.viewestimate .page-pdf-html-logo',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Proposal view (right side background color)',
                    'id' => 'proposal-view',
                    'target' => '.proposal-view .proposal-right',
                    'css' => 'background',
                    'additional_selectors' => ''
                )
            ),

            'tabs' => array(
                array(
                    'name' => 'Tabs Background Color',
                    'id' => 'tabs-bg',
                    'target' => '.nav-tabs',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Tabs Links Color',
                    'id' => 'tabs-links',
                    'target' => '.nav-tabs>li>a',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Tabs Link Active/Hover Color',
                    'id' => 'tabs-links-active-hover',
                    'target' => '.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover, .nav-tabs>li>a:focus, .nav-tabs>li>a:hover',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Tabs Border Color',
                    'id' => 'tabs-border',
                    'target' => '.nav-tabs',
                    'css' => 'border-color',
                    'additional_selectors' => '.navbar-pills.nav-tabs>li>a:focus,.navbar-pills.nav-tabs>li>a:hover|border-color'
                ),
                array(
                    'name' => 'Tabs Active Border Color',
                    'id' => 'tabs-active-border',
                    'target' => '.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover, .nav-tabs>li>a:focus, .nav-tabs>li>a:hover',
                    'css' => 'border-bottom-color',
                    'additional_selectors' => ''
                )
            ),
            'modals' => array(
                array(
                    'name' => 'Heading Background',
                    'id' => 'modal-heading',
                    'target' => '.modal-header',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Heading Color',
                    'id' => 'modal-heading-color',
                    'target' => '.modal-header .modal-title',
                    'css' => 'color',
                    'additional_selectors' => ''
                )
            ),
            'buttons' => array(
                array(
                    'name' => 'Save Button Text Color',
                    'id' => 'btn-info',
                    'target' => '.btn-info, 
                    .btn-primary, 
                    .navbar-nav>li>a.profile i,
                    .pagination>.active>a,
                    .pagination>.active>a|border-color, 
                    .pagination>.active>a:focus, 
                    .pagination>.active>a:hover, 
                    .pagination>.active>span, 
                    .pagination>.active>span:focus, 
                    .pagination>.active>span:hover',
                    'css' => 'color',
                    'additional_selectors' => '.btn-info|color',
                    'example' => '<button type="button" class="btn btn-info">Save</button>'
                ),
                array(
                    'name' => 'Save Button Background Color',
                    'id' => 'btn-info-background',
                    'target' => '.btn-info, 
                    .btn-primary,
                    .open>.dropdown-toggle.btn-info',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-info|background-color',
                    'example' => '<button type="button" class="btn btn-info">Save</button>'
                ),
                array(
                    'name' => 'Save Button Hover Text Color',
                    'id' => 'btn-info-hover',
                    'target' => '.btn-info:hover, 
                    .btn-primary:hover,
                    .open>.dropdown-toggle.btn-info:hover,
                    .open>.dropdown-toggle.btn-info.focus,
                    .open>.dropdown-toggle.btn-info:focus,
                    .btn-info.focus,
                    .btn-info:focus,
                    .open>.dropdown-toggle.btn-info,
                    .btn-info.disabled, 
                    .btn-info.disabled.active, 
                    .btn-info.disabled.focus, 
                    .btn-info.disabled:active,
                    .btn-info.disabled:focus, 
                    .btn-info.disabled:hover, 
                    .btn-info[disabled], 
                    .btn-info[disabled].active, 
                    .btn-info[disabled].focus, 
                    .btn-info[disabled]:active, 
                    .btn-info[disabled]:focus, 
                    .btn-info[disabled]:hover, 
                    fieldset[disabled] .btn-info, 
                    fieldset[disabled] .btn-info.active, 
                    fieldset[disabled] .btn-info.focus, 
                    fieldset[disabled] .btn-info:active, 
                    fieldset[disabled] .btn-info:focus, 
                    fieldset[disabled] .btn-info:hover',
                    'css' => 'color',
                    'additional_selectors' => '.btn-info:hover|color',
                    'example' => '<button type="button" class="btn btn-info">Save</button>'
                ),
                array(
                    'name' => 'Save Button Hover Background Color',
                    'id' => 'btn-info-hover-background',
                    'target' => '.btn-info:hover, 
                    .btn-primary:hover,
                    .open>.dropdown-toggle.btn-info:hover,
                    .open>.dropdown-toggle.btn-info.focus,
                    .open>.dropdown-toggle.btn-info:focus,
                    .btn-info.focus,
                    .btn-info:focus,
                    .open>.dropdown-toggle.btn-info,
                    .btn-info.disabled, 
                    .btn-info.disabled.active, 
                    .btn-info.disabled.focus, 
                    .btn-info.disabled:active,
                    .btn-info.disabled:focus, 
                    .btn-info.disabled:hover, 
                    .btn-info[disabled], 
                    .btn-info[disabled].active, 
                    .btn-info[disabled].focus, 
                    .btn-info[disabled]:active, 
                    .btn-info[disabled]:focus, 
                    .btn-info[disabled]:hover, 
                    fieldset[disabled] .btn-info, 
                    fieldset[disabled] .btn-info.active, 
                    fieldset[disabled] .btn-info.focus, 
                    fieldset[disabled] .btn-info:active, 
                    fieldset[disabled] .btn-info:focus, 
                    fieldset[disabled] .btn-info:hover',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-info:hover|background-color',
                    'example' => '<button type="button" class="btn btn-info">Save</button>'
                ),
                array(
                    'name' => 'Edit',
                    'id' => 'btn-orange',
                    'target' => '.btn-orange, 
                    .btn-orange.focus, .btn-orange:focus,
                    .btn-orange:hover,
                    .btn-orange.active, 
                    .btn-orange:active, 
                    .open>.dropdown-toggle.btn-orange,
                    .btn-orange.active.focus, 
                    .btn-orange.active:focus, 
                    .btn-orange.active:hover, 
                    .btn-orange:active.focus, 
                    .btn-orange:active:focus, 
                    .btn-orange:active:hover, 
                    .open>.dropdown-toggle.btn-orange.focus, 
                    .open>.dropdown-toggle.btn-orange:focus, 
                    .open>.dropdown-toggle.btn-orange:hover,
                    .btn-orange.disabled, 
                    .btn-orange.disabled.active, 
                    .btn-orange.disabled.focus, 
                    .btn-orange.disabled:active, 
                    .btn-orange.disabled:focus, 
                    .btn-orange.disabled:hover, 
                    .btn-orange[disabled], 
                    .btn-orange[disabled].active, 
                    .btn-orange[disabled].focus, 
                    .btn-orange[disabled]:active, 
                    .btn-orange[disabled]:focus, 
                    .btn-orange[disabled]:hover, 
                    fieldset[disabled] .btn-orange, 
                    fieldset[disabled] .btn-orange.active, 
                    fieldset[disabled] .btn-orange.focus, 
                    fieldset[disabled] .btn-orange:active, 
                    fieldset[disabled] .btn-orange:focus, 
                    fieldset[disabled] .btn-orange:hover',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-orange|border-color',
                    'example' => '<a href="#" class="btn btn-orange btn-icon"><i class="fa fa-pencil-square-o"></i></a>'
                ),
                array(
                    'name' => 'Cancel Button Text Color',
                    'id' => 'btn-default',
                    'target' => '.btn-default',
                    'css' => 'color',
                    'additional_selectors' => '.btn-default|color',
                    'example' => '<button type="button" class="btn btn-default">Cancel</button>'
                ),
                array(
                    'name' => 'Cancel Button Background Color',
                    'id' => 'btn-default-background',
                    'target' => '.btn-default',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-default|background-color',
                    'example' => '<button type="button" class="btn btn-default">Cancel</button>'
                ),
                array(
                    'name' => 'Cancel Button Hover Text Color',
                    'id' => 'btn-default-hover',
                    'target' => '.btn-default:hover',
                    'css' => 'color',
                    'additional_selectors' => '.btn-default:hover|color',
                    'example' => '<button type="button" class="btn btn-default">Cancel</button>'
                ),
                array(
                    'name' => 'Cancel Button Hover Background Color',
                    'id' => 'btn-default-hover-background',
                    'target' => '.btn-default:hover',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-default:hover|background-color',
                    'example' => '<button type="button" class="btn btn-default">Cancel</button>'
                ),
                array(
                    'name' => 'Delete Button Text Color',
                    'id' => 'btn-danger',
                    'target' => '.btn-danger,
                    .open>.dropdown-toggle.btn-danger',
                    'css' => 'color',
                    'additional_selectors' => '.btn-danger|color',
                    'example' => '<a href="#" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>'
                ),
                array(
                    'name' => 'Delete Button Background Color',
                    'id' => 'btn-danger-background',
                    'target' => '.btn-danger,
                    .open>.dropdown-toggle.btn-danger',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-danger|background-color',
                    'example' => '<a href="#" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>'
                ),
                array(
                    'name' => 'Delete Button Hover Text Color',
                    'id' => 'btn-danger-hover',
                    'target' => '.btn-danger:hover,
                    .btn-danger.active:focus,
                    .btn-danger.active.focus,
                    .open>.dropdown-toggle.btn-danger.focus,
                    .open>.dropdown-toggle.btn-danger:focus,
                    .open>.dropdown-toggle.btn-danger:hover,
                    .btn-danger.focus, 
                    .btn-danger:focus,
                    .btn-danger.active, 
                    .btn-danger:active, 
                    .open>.dropdown-toggle.btn-danger,
                    .btn-danger.active:hover, 
                    .btn-danger:active.focus, 
                    .btn-danger:active:focus, 
                    .btn-danger:active:hover,
                    .btn-danger.disabled, 
                    .btn-danger.disabled.active, 
                    .btn-danger.disabled.focus, 
                    .btn-danger.disabled:active, 
                    .btn-danger.disabled:focus, 
                    .btn-danger.disabled:hover, 
                    .btn-danger[disabled], 
                    .btn-danger[disabled].active, 
                    .btn-danger[disabled].focus, 
                    .btn-danger[disabled]:active, 
                    .btn-danger[disabled]:focus, 
                    .btn-danger[disabled]:hover, 
                    fieldset[disabled] .btn-danger, 
                    fieldset[disabled] .btn-danger.active, 
                    fieldset[disabled] .btn-danger.focus, 
                    fieldset[disabled] .btn-danger:active, 
                    fieldset[disabled] .btn-danger:focus, 
                    fieldset[disabled] .btn-danger:hover',
                    'css' => 'color',
                    'additional_selectors' => '.btn-danger:hover|color',
                    'example' => '<a href="#" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>'
                ),
                array(
                    'name' => 'Delete Button Hover Background Color',
                    'id' => 'btn-danger-hover-background',
                    'target' => '.btn-danger:hover,
                    .btn-danger.active:focus,
                    .btn-danger.active.focus,
                    .open>.dropdown-toggle.btn-danger.focus,
                    .open>.dropdown-toggle.btn-danger:focus,
                    .open>.dropdown-toggle.btn-danger:hover,
                    .btn-danger.focus, 
                    .btn-danger:focus,
                    .btn-danger.active, 
                    .btn-danger:active, 
                    .open>.dropdown-toggle.btn-danger,
                    .btn-danger.active:hover, 
                    .btn-danger:active.focus, 
                    .btn-danger:active:focus, 
                    .btn-danger:active:hover,
                    fieldset[disabled] .btn-danger, 
                    fieldset[disabled] .btn-danger.active, 
                    fieldset[disabled] .btn-danger.focus, 
                    fieldset[disabled] .btn-danger:active, 
                    fieldset[disabled] .btn-danger:focus, 
                    fieldset[disabled] .btn-danger:hover',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-danger:hover|background-color',
                    'example' => '<a href="#" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>'
                ),
                array(
                    'name' => 'Create New on Header',
                    'id' => 'quick-links',
                    'target' => '.quick-links #dropdownQuickLinks',
                    'css' => 'background-color',
                    'additional_selectors' => '',//'.btn-info|border-color',
                    'example' => '<ul class="nav navbar-nav"><li class="quick-links"><a href="javascript:void(0);" class="dropdown-toggle" id="dropdownQuickLinks"><i class="fa fa-plus-square" aria-hidden="true"></i> &nbsp; Create New</a></li></ul>'
                ),
                array(
                    'name' => 'Icons',
                    'id' => 'fa',
                    'target' => '.lead-tool-block .tabs-bordered .nav-tabs a i, .countdown-amount,.pinned,.favorite',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<div class="leaddashboard lead-tool-block"><div class="tabs tabs-bordered "><ul class="nav nav-tabs" style="margin-bottom:-25px;"> <li class="nav-item1"><a class="nav-link" href="#" style="height:60px; margin-top: -20px;"> <i class="fa fa-clock-o" style="display:unset"></i></a></li></ul></div></div>'
                )
            )
        );
    } else {
        $areas = array(
            'general' => array(
                array(
                    'name' => '<a href="#" onclick="return false;">Links</a> Color (href)',
                    'id' => 'links-color',
                    'target' => 'a',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Links Hover/Focus Color',
                    'id' => 'links-hover-focus',
                    'target' => 'a:hover,a:focus',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Table Headings Color',
                    'id' => 'table-headings',
                    'target' => 'table.dataTable thead .sorting, table.dataTable thead .sorting_asc, table.dataTable thead .sorting_desc, table.dataTable thead .sorting_asc_disabled, table.dataTable thead .sorting_desc_disabled',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<table class="table dataTable"><thead><tr><th style="border-bottom: 1px solid #f0f0f0" class="sorting">Example Heading 1</th><th style="border-bottom: 1px solid #f0f0f0" class="sorting">Example Heading 2</th></tr></thead></table>'
                ),
                array(
                    'name' => 'Items Table Headings Background Color',
                    'id' => 'table-items-heading',
                    'target' => '.table.items thead',
                    'css' => 'background',
                    'additional_selectors' => '',
                    'example' => '<table class="table items"><thead><tr><th>Example Heading 1</th><th>Example Heading 2</th></tr></thead></table>'
                ),
                array(
                    'name' => 'Admin Login Background',
                    'id' => 'admin-login-background',
                    'target' => 'body.login_admin',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Text Muted',
                    'id' => 'text-muted',
                    'target' => '.text-muted',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<p>Example <span class="bold text-muted">text muted</span></p>'
                ),
                array(
                    'name' => 'Text Danger',
                    'id' => 'text-danger',
                    'target' => '.text-danger',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<p>Example <span class="bold text-danger">text danger</span></p>'
                ),
                array(
                    'name' => 'Text Warning',
                    'id' => 'text-warning',
                    'target' => '.text-warning',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<p>Example <span class="bold text-warning">text warning</span></p>'
                ),
                array(
                    'name' => 'Text Info',
                    'id' => 'text-info',
                    'target' => '.text-info',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<p>Example <span class="bold text-info">text info</span></p>'
                ),
                array(
                    'name' => 'Text Success',
                    'id' => 'text-success',
                    'target' => '.text-success',
                    'css' => 'color',
                    'additional_selectors' => '',
                    'example' => '<p>Example <span class="bold text-success">text success</span></p>'
                )
            ),
            'admin' => array(
                array(
                    'name' => 'Background Color',
                    'id' => 'admin-menu',
                    'target' => '.admin #side-menu,.admin #setup-menu',
                    'css' => 'background',
                    'additional_selectors' => 'body|background+#setup-menu-wrapper|background'
                ),
                array(
                    'name' => 'Submenu Open Background Color',
                    'id' => 'admin-menu-submenu-open',
                    'target' => '.admin #side-menu li .nav-second-level li,.admin #setup-menu li .nav-second-level li',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Links Color',
                    'id' => 'admin-menu-links',
                    'target' => '.admin #side-menu li a,.admin #setup-menu li a',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Active Item Background Color',
                    'id' => 'admin-menu-active-item',
                    'target' => '
                    .admin #side-menu li.active > a,
                    .admin #setup-menu li.active > a,
                    #side-menu.nav > li > a:hover,
                    #side-menu.nav > li > a:focus,
                    #setup-menu > li > a:hover,
                    #setup-menu > li > a:focus',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Active Item Color',
                    'id' => 'admin-menu-active-item-color',
                    'target' => '
                    .admin #side-menu li.active > a,
                    .admin #setup-menu li.active > a',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Active Subitem Background Color',
                    'id' => 'admin-menu-active-subitem',
                    'target' => '.admin #side-menu li .nav-second-level li.active a,.admin #setup-menu li .nav-second-level li.active a',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Submenu links color',
                    'id' => 'admin-menu-submenu-links',
                    'target' => '.admin #side-menu li .nav-second-level li a,#setup-menu li .nav-second-level li a',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Top Header Background Color',
                    'id' => 'top-header',
                    'target' => '.admin #header',
                    'css' => 'background-color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Top Header Links Color',
                    'id' => 'top-header-links',
                    'target' => '.admin .navbar-nav > li > a, ul.mobile-icon-menu>li>a,.mobile-menu-toggle, .open-customizer-mobile',
                    'css' => 'color',
                    'additional_selectors' => ''
                )//,
                // array(
                //     'name' => 'Top Header Links Border Color (border right)',
                //     'id' => 'top-header-border',
                //     'target' => '.admin #header li.icon',
                //     'css' => 'border-right-color',
                //     'additional_selectors' => '#top_search_button button|border-right-color+.hide-menu|border-right-color'
                // )

            ),
            'customers' => array(
                array(
                    'name' => 'Navigation Background Color',
                    'id' => 'customers-navigation',
                    'target' => '.customers .navbar-default',
                    'css' => 'background-color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Navigation Links Color',
                    'id' => 'customers-navigation-links',
                    'target' => '.customers .navbar-default .navbar-nav>li>a',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Invoice/Estimate HTML View Top Header Background Color',
                    'id' => 'html-view-top-header',
                    'target' => '.viewinvoice .page-pdf-html-logo,.viewestimate .page-pdf-html-logo',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Proposal view (right side background color)',
                    'id' => 'proposal-view',
                    'target' => '.proposal-view .proposal-right',
                    'css' => 'background',
                    'additional_selectors' => ''
                )
            ),

            'tabs' => array(
                array(
                    'name' => 'Tabs Background Color',
                    'id' => 'tabs-bg',
                    'target' => '.nav-tabs',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Tabs Links Color',
                    'id' => 'tabs-links',
                    'target' => '.nav-tabs>li>a',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Tabs Link Active/Hover Color',
                    'id' => 'tabs-links-active-hover',
                    'target' => '.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover, .nav-tabs>li>a:focus, .nav-tabs>li>a:hover',
                    'css' => 'color',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Tabs Border Color',
                    'id' => 'tabs-border',
                    'target' => '.nav-tabs',
                    'css' => 'border-color',
                    'additional_selectors' => '.navbar-pills.nav-tabs>li>a:focus,.navbar-pills.nav-tabs>li>a:hover|border-color'
                ),
                array(
                    'name' => 'Tabs Active Border Color',
                    'id' => 'tabs-active-border',
                    'target' => '.nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover, .nav-tabs>li>a:focus, .nav-tabs>li>a:hover',
                    'css' => 'border-bottom-color',
                    'additional_selectors' => ''
                )
            ),
            'modals' => array(
                array(
                    'name' => 'Heading Background',
                    'id' => 'modal-heading',
                    'target' => '.modal-header',
                    'css' => 'background',
                    'additional_selectors' => ''
                ),
                array(
                    'name' => 'Heading Color',
                    'id' => 'modal-heading-color',
                    'target' => '.modal-header .modal-title',
                    'css' => 'color',
                    'additional_selectors' => ''
                )
            ),
            'buttons' => array(
                array(
                    'name' => 'Button Default',
                    'id' => 'btn-default',
                    'target' => '.btn-default',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-default|border-color',
                    'example' => '<button type="button" class="btn btn-default">Button Default</button>'
                ),
                array(
                    'name' => 'Button Info',
                    'id' => 'btn-info-admin',
                    'target' => '.btn-info',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-info|border-color',
                    'example' => '<button type="button" class="btn btn-info">Button Info</button>'
                ),
                array(
                    'name' => 'Button Success',
                    'id' => 'btn-orange',
                    'target' => '.btn-orange',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-orange|border-color',
                    'example' => '<button type="button" class="btn btn-orange">Button Success</button>'
                ),
                array(
                    'name' => 'Button Danger',
                    'id' => 'btn-danger',
                    'target' => '.btn-danger',
                    'css' => 'background-color',
                    'additional_selectors' => '.btn-danger|border-color',
                    'example' => '<button type="button" class="btn btn-danger">Button Danger</button>'
                )
            )
        );
    }

    $CI =& get_instance();
    $tags = $CI->db->get('tbltags')->result_array();

    $areas['tags'] = array();

    foreach ($tags as $tag) {
        array_push($areas['tags'], array(
            'name' => $tag['name'],
            'id' => 'tag-' . $tag['id'],
            'target' => '.tag-id-' . $tag['id'],
            'css' => 'color',
            'additional_selectors' => '.tag-id-' . $tag['id'] . '|border-color+ul.tagit li.tagit-choice-editable.tag-id-' . $tag['id'] . '|border-color+ul.tagit li.tagit-choice.tag-id-' . $tag['id'] . ' .tagit-label:not(a)|color',
            'example' => '<span class="label label-tag tag-id-' . $tag['id'] . '">' . $tag['name'] . '</span>'
        ));
    }

    $areas = do_action('get_styling_areas', $areas);

    if (!is_array($type)) {
        return $areas[$type];
    } else {
        $_areas = array();
        foreach ($type as $t) {
            $_areas[] = $areas[$t];
        }
        return $_areas;
    }
}
/**
 * Will fetch from database the stored applied styles and return
 * @return object
 */
function get_applied_styling_area()
{
    /**
     * Added By : Vaidehi
     * Dt : 10/23/2017
     * to display custom theme style for all users except sido admin and super admin
     */
    $session_data = get_session_data();
    $is_sido_admin = isset($session_data['is_sido_admin']) ? $session_data['is_sido_admin'] : 2;
    $is_admin = isset($session_data['is_admin']) ? $session_data['is_admin'] : 2;

    if($is_sido_admin == 0 && $is_admin == 0) {
        $theme_style = get_brand_option('theme_style');
    } else {
        $theme_style = get_option('theme_style');
    }

    if ($theme_style == '') {
        return array();
    }
    $theme_style = json_decode($theme_style);

    return $theme_style;
}
/**
 * Function that will parse and render the applied styles
 * @param  string $type
 * @return void
 */
function render_custom_styles($type)
{
    $theme_style   = get_applied_styling_area();
    $styling_areas = get_styling_areas($type);


    foreach ($styling_areas as $type => $area) {
        foreach ($area as $_area) {
            foreach ($theme_style as $applied_style) {
                if ($applied_style->id == $_area['id']) {

                    echo '<style class="custom_style_' . $_area['id'] . '">' . PHP_EOL;
                    echo $_area['target'] . '{' . PHP_EOL;
                    echo $_area['css'] . ':' . $applied_style->color . ';' . PHP_EOL;
                    echo '}' . PHP_EOL;

                    if($_area['id'] == 'btn-info' || $_area['id'] == 'btn-orange' || $_area['id'] == 'btn-default' || $_area['id'] == 'btn-danger') {
                        if (_startsWith($_area['target'], '.btn')) {
                            echo '
                            ' . $_area['target'] . ':focus,' . $_area['target'] . '.focus,' . $_area['target'] . ':hover,' . $_area['target'] . ':active,
                            ' . $_area['target'] . '.active,
                            .open > .dropdown-toggle' . $_area['target'] . ',' . $_area['target'] . ':active:hover,
                            ' . $_area['target'] . '.active:hover,
                            .open > .dropdown-toggle' . $_area['target'] . ':hover,
                            ' . $_area['target'] . ':active:focus,
                            ' . $_area['target'] . '.active:focus,
                            .open > .dropdown-toggle' . $_area['target'] . ':focus,
                            ' . $_area['target'] . ':active.focus,
                            ' . $_area['target'] . '.active.focus,
                            .open > .dropdown-toggle' . $_area['target'] . '.focus,
                            ' . $_area['target'] . ':active,
                            ' . $_area['target'] . '.active,
                            .open > .dropdown-toggle' . $_area['target'] . '{color:' . adjust_color_brightness($applied_style->color, 50) . '}';
                            echo '
                            ' . $_area['target'] . '.disabled,
                            ' . $_area['target'] . '[disabled],
                            fieldset[disabled] ' . $_area['target'] . ',
                            ' . $_area['target'] . '.disabled:hover,
                            ' . $_area['target'] . '[disabled]:hover,
                            fieldset[disabled] ' . $_area['target'] . ':hover,
                            ' . $_area['target'] . '.disabled:focus,
                            ' . $_area['target'] . '[disabled]:focus,
                            fieldset[disabled] ' . $_area['target'] . ':focus,
                            ' . $_area['target'] . '.disabled.focus,
                            ' . $_area['target'] . '[disabled].focus,
                            fieldset[disabled] ' . $_area['target'] . '.focus,
                            ' . $_area['target'] . '.disabled:active,
                            ' . $_area['target'] . '[disabled]:active,
                            fieldset[disabled] ' . $_area['target'] . ':active,
                            ' . $_area['target'] . '.disabled.active,
                            ' . $_area['target'] . '[disabled].active,
                            fieldset[disabled] ' . $_area['target'] . '.active {
                                color: ' . adjust_color_brightness($applied_style->color, 50) . '}';
                        }
                    }

                    if($_area['id'] == 'btn-info-background' || $_area['id'] == 'btn-orange-background' || $_area['id'] == 'btn-default-background' || $_area['id'] == 'btn-danger-background') {
                        if (_startsWith($_area['target'], '.btn')) {
                            echo '
                            ' . $_area['target'] . ':focus,' . $_area['target'] . '.focus,' . $_area['target'] . ':hover,' . $_area['target'] . ':active,
                            ' . $_area['target'] . '.active,
                            .open > .dropdown-toggle' . $_area['target'] . ',' . $_area['target'] . ':active:hover,
                            ' . $_area['target'] . '.active:hover,
                            .open > .dropdown-toggle' . $_area['target'] . ':hover,
                            ' . $_area['target'] . ':active:focus,
                            ' . $_area['target'] . '.active:focus,
                            .open > .dropdown-toggle' . $_area['target'] . ':focus,
                            ' . $_area['target'] . ':active.focus,
                            ' . $_area['target'] . '.active.focus,
                            .open > .dropdown-toggle' . $_area['target'] . '.focus,
                            ' . $_area['target'] . ':active,
                            ' . $_area['target'] . '.active,
                            .open > .dropdown-toggle' . $_area['target'] . '{background-color:' . adjust_color_brightness($applied_style->color, -50) . ';}';
                            echo '
                            ' . $_area['target'] . '.disabled,
                            ' . $_area['target'] . '[disabled],
                            fieldset[disabled] ' . $_area['target'] . ',
                            ' . $_area['target'] . '.disabled:hover,
                            ' . $_area['target'] . '[disabled]:hover,
                            fieldset[disabled] ' . $_area['target'] . ':hover,
                            ' . $_area['target'] . '.disabled:focus,
                            ' . $_area['target'] . '[disabled]:focus,
                            fieldset[disabled] ' . $_area['target'] . ':focus,
                            ' . $_area['target'] . '.disabled.focus,
                            ' . $_area['target'] . '[disabled].focus,
                            fieldset[disabled] ' . $_area['target'] . '.focus,
                            ' . $_area['target'] . '.disabled:active,
                            ' . $_area['target'] . '[disabled]:active,
                            fieldset[disabled] ' . $_area['target'] . ':active,
                            ' . $_area['target'] . '.disabled.active,
                            ' . $_area['target'] . '[disabled].active,
                            fieldset[disabled] ' . $_area['target'] . '.active {
                                background-color: ' . adjust_color_brightness($applied_style->color, 50) . ';}';
                        }
                    }

                    if($_area['id'] == 'btn-info-hover' || $_area['id'] == 'btn-orange-hover' || $_area['id'] == 'btn-default-hover' || $_area['id'] == 'btn-danger-hover') {
                        if (_startsWith($_area['target'], '.btn')) {
                            echo '
                            ' . $_area['target'] . ':focus,' . $_area['target'] . '.focus,' . $_area['target'] . ':hover,' . $_area['target'] . ':active,
                            ' . $_area['target'] . '.active,
                            .open > .dropdown-toggle' . $_area['target'] . ',' . $_area['target'] . ':active:hover,
                            ' . $_area['target'] . '.active:hover,
                            .open > .dropdown-toggle' . $_area['target'] . ':hover,
                            ' . $_area['target'] . ':active:focus,
                            ' . $_area['target'] . '.active:focus,
                            .open > .dropdown-toggle' . $_area['target'] . ':focus,
                            ' . $_area['target'] . ':active.focus,
                            ' . $_area['target'] . '.active.focus,
                            .open > .dropdown-toggle' . $_area['target'] . '.focus,
                            ' . $_area['target'] . ':active,
                            ' . $_area['target'] . '.active,
                            .open > .dropdown-toggle' . $_area['target'] . '{color:' . adjust_color_brightness($applied_style->color, 50) . '}';
                            echo '
                            ' . $_area['target'] . '.disabled,
                            ' . $_area['target'] . '[disabled],
                            fieldset[disabled] ' . $_area['target'] . ',
                            ' . $_area['target'] . '.disabled:hover,
                            ' . $_area['target'] . '[disabled]:hover,
                            fieldset[disabled] ' . $_area['target'] . ':hover,
                            ' . $_area['target'] . '.disabled:focus,
                            ' . $_area['target'] . '[disabled]:focus,
                            fieldset[disabled] ' . $_area['target'] . ':focus,
                            ' . $_area['target'] . '.disabled.focus,
                            ' . $_area['target'] . '[disabled].focus,
                            fieldset[disabled] ' . $_area['target'] . '.focus,
                            ' . $_area['target'] . '.disabled:active,
                            ' . $_area['target'] . '[disabled]:active,
                            fieldset[disabled] ' . $_area['target'] . ':active,
                            ' . $_area['target'] . '.disabled.active,
                            ' . $_area['target'] . '[disabled].active,
                            fieldset[disabled] ' . $_area['target'] . '.active {
                                color: ' . adjust_color_brightness($applied_style->color, 50) . '}';
                        }
                    }

                    if($_area['id'] == 'btn-info-hover-background' || $_area['id'] == 'btn-orange-hover-background' || $_area['id'] == 'btn-default-hover-background' || $_area['id'] == 'btn-danger-hover-background') {
                        if (_startsWith($_area['target'], '.btn')) {
                            echo '
                            ' . $_area['target'] . ':focus,' . $_area['target'] . '.focus,' . $_area['target'] . ':hover,' . $_area['target'] . ':active,
                            ' . $_area['target'] . '.active,
                            .open > .dropdown-toggle' . $_area['target'] . ',' . $_area['target'] . ':active:hover,
                            ' . $_area['target'] . '.active:hover,
                            .open > .dropdown-toggle' . $_area['target'] . ':hover,
                            ' . $_area['target'] . ':active:focus,
                            ' . $_area['target'] . '.active:focus,
                            .open > .dropdown-toggle' . $_area['target'] . ':focus,
                            ' . $_area['target'] . ':active.focus,
                            ' . $_area['target'] . '.active.focus,
                            .open > .dropdown-toggle' . $_area['target'] . '.focus,
                            ' . $_area['target'] . ':active,
                            ' . $_area['target'] . '.active,
                            .open > .dropdown-toggle' . $_area['target'] . '{background-color:' . adjust_color_brightness($applied_style->color, -50) . ';}';
                            echo '
                            ' . $_area['target'] . '.disabled,
                            ' . $_area['target'] . '[disabled],
                            fieldset[disabled] ' . $_area['target'] . ',
                            ' . $_area['target'] . '.disabled:hover,
                            ' . $_area['target'] . '[disabled]:hover,
                            fieldset[disabled] ' . $_area['target'] . ':hover,
                            ' . $_area['target'] . '.disabled:focus,
                            ' . $_area['target'] . '[disabled]:focus,
                            fieldset[disabled] ' . $_area['target'] . ':focus,
                            ' . $_area['target'] . '.disabled.focus,
                            ' . $_area['target'] . '[disabled].focus,
                            fieldset[disabled] ' . $_area['target'] . '.focus,
                            ' . $_area['target'] . '.disabled:active,
                            ' . $_area['target'] . '[disabled]:active,
                            fieldset[disabled] ' . $_area['target'] . ':active,
                            ' . $_area['target'] . '.disabled.active,
                            ' . $_area['target'] . '[disabled].active,
                            fieldset[disabled] ' . $_area['target'] . '.active {
                                background-color: ' . adjust_color_brightness($applied_style->color, 50) . ';}';
                        }
                    }

                    if ($_area['additional_selectors'] != '') {
                        $additional_selectors = explode('+', $_area['additional_selectors']);
                        foreach ($additional_selectors as $as) {
                            $_temp = explode('|', $as);
                            echo $_temp[0] . ' {' . PHP_EOL;
                            echo $_temp[1] . ':' . $applied_style->color . ';' . PHP_EOL;
                            echo '}' . PHP_EOL;
                        }
                    }
                    echo '</style>' . PHP_EOL;
                }
            }
        }
    }

}
/**
 * Get selected value for some styling area for the Theme style feature
 * @param  string $type
 * @param  string $selector
 * @return string
 */
function get_custom_style_values($type, $selector)
{
    $value         = '';
    $theme_style   = get_applied_styling_area();
    $styling_areas = get_styling_areas($type);
    foreach ($styling_areas as $area) {
        if ($area['id'] == $selector) {
            foreach ($theme_style as $applied_style) {
                if ($applied_style->id == $selector) {
                    $value = $applied_style->color;
                    break;
                }
            }
        }
    }
    return $value;
}
