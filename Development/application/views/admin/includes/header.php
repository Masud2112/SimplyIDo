<?php
ob_start();
?>
<?php $base_url = base_url(); ?>
<!--<li id="top_search" class="dropdown" data-toggle="tooltip" data-placement="bottom" data-title="<?php //echo _l('search_by_tags'); ?>">-->

<!--
* Added By : Sanjay
* Dt: 01/01/2017
* Gloabal Search Form
-->
<style>
    .menu-submenu li {
        margin: 10px !important;
    }
</style>
<script type="text/javascript">
    function filter_this(page_limit) {
        if (document.getElementById('search_input').value != '') {
            limit = document.getElementById('search_input').value;
            window.location = '<?php echo $base_url;?>admin/home/search/?search_input=' + limit;
        } else {
            window.location = '<?php echo $base_url;?>admin/home/search/?search_input=';
        }
    }
</script>

<li id="top_search" class="dropdown">
    <i class="fa fa-search"></i>
    <form action="<?php echo base_url() . "admin/home/search"; ?>" method="get" name="search_form">
        <form method="get" name="search_form" onsubmit="filter_this(); return false;">
            <input type="search" id="search_input" name="search_input" class="form-control"
                   placeholder="<?php echo _l('top_search_placeholder'); ?>"
                   value="<?php if (isset($_GET['search_input'])) {
                       echo $_GET['search_input'];
                   } ?>">
            <!--  <input type="submit" style="display: none;"/> -->
            <div id="search_results"></div>
        </form>
</li>
<li id="top_search_button">
    <button class="btn"><i class="fa fa-search"></i></button>
</li>
<?php
$top_search_area = ob_get_contents();
ob_end_clean();
$total_qa_removed = 0;
$quickActions = $this->perfex_base->get_quick_actions_links();
foreach ($quickActions as $key => $item) {
    if (isset($item['permission'])) {
        if (!has_permission($item['permission'], '', 'create')) {
            $total_qa_removed++;
        }
    }
}

$brandid = get_user_session();
?>
<div id="header">
    <div class="hide-menu"><i class="fa fa-bars"></i></div>
    <div id="logo">
        <?php
        /**
         * Added By : Vaidehi
         * Dt: 10/14/2017
         * udpate logo based on login
         */
        if (isset($brands) && $brands != "") {
            get_brand_logo('admin', '', $brandid);
        } else {
            get_company_logo('admin');
        }
        ?>
    </div>
    <nav>
        <div class="small-logo">
		<span class="text-primary">
		  <?php
          /**
           * Added By : Vaidehi
           * Dt: 10/14/2017
           * udpate logo based on login
           */
          if (isset($brands) && $brands != "") {
              get_brand_logo('admin', '', $brandid);
          } else {
              get_company_logo('admin');
          }
          ?>
		</span>
        </div>
        <div class="mobile-menu header-user-profile">
            <button type="button" class="navbar-toggle visible-md visible-sm visible-xs mobile-menu-toggle collapsed"
                    data-toggle="collapse" data-target="#mobile-collapse" aria-expanded="false">
                <i class="fa fa-chevron-down"></i>
            </button>
            <button type="button"
                    class="navbar-toggle visible-md visible-sm visible-xs mobile-menu-toggle collapsed nav-more"
                    data-toggle="collapse" data-target="#mobile-collapse2" aria-expanded="false">
                <i class="fa fa-ellipsis-v"></i>
            </button>
            <ul class="mobile-icon-menu">
                <?php
                // To prevent not loading the timers twice
                if (is_mobile()) { ?>
                    <li class="dropdown notifications-wrapper header-notifications">
                        <?php $this->load->view('admin/includes/notifications'); ?>
                    </li>
                    <li class="header-timers">
                    <a href="#" class="dropdown-toggle top-timers<?php if (count($startedTimers) > 0) {
                        echo ' text-success';
                    } ?>" data-toggle="dropdown"><i class="fa fa-clock-o"></i></a>
                    <ul class="dropdown-menu animated fadeIn started-timers-top width300" id="started-timers-top">
                        <?php $this->load->view('admin/tasks/started_timers', array('startedTimers' => $startedTimers)); ?>
                    </ul>
                    <?php if (is_staff_member()) { ?>
                        <li class="header-newsfeed">
                            <a href="#" class="open_newsfeed"><i class="fa fa-commenting" aria-hidden="true"></i></a>
                        </li>
                    <?php } ?>
                <?php } ?>
            </ul>
            <div class="mobile-navbar collapse" id="mobile-collapse" aria-expanded="false" style="height: 0px;"
                 role="navigation">
                <ul class="nav navbar-nav animated fadeIn" id="side-menutop">
                    <li class="dropdown-header"><?php echo $current_user->firstname ?>
                        <i class="fa fa-power-off top-left-logout pull-right" data-toggle="tooltip" data-title="Logout"
                           data-placement="right" onclick="logout(); return false;"></i>
                    </li>
                    <?php
                    /**
                     * Added By : Vaidehi
                     * Dt: 10/12/2017
                     * get all brands for account owner and team member
                     */
                    if (isset($brands) && $brands != "") { ?>
                        <!--<li class="dropdown-header">Brands</li>-->
                        <?php foreach ($brands as $brand) {
                            $default = "";
                            if ($brand['isdefault'] == 1) {
                                $default = "isdefault";
                            }
                            ?>
                            <li class="brand <?php echo($brand['brandid'] == $brandid ? 'active' : ''); ?>">
                                <a data-id="<?php echo $brand['brandid']; ?>" href="javascript: void(0);"
                                   onclick="changeBrand(this);"><?php echo $brand['name']; ?>
                                </a>
                                <a class="defaultBrand <?php echo $default; ?>"
                                   data-id="<?php echo $brand['brandid']; ?>" href="javascript: void(0);">
                                    <i class="fa fa-home" aria-hidden="true"></i>
                                </a>
                            </li>
                        <?php } ?>
                        <li class="divider"></li>
                        <?php if (has_permission('account_setup', '', 'view') == true) { ?>
                            <li class="header-edit-profile"><a href="<?php echo admin_url('brand_settings'); ?>"><i
                                            class="fa fa-gear"></i><?php echo _l('brand_settings'); ?></a></li>
                        <?php } ?>
                        <?php if (has_permission('account_setup', '', 'edit') == true) { ?>
                            <li class="header-edit-profile"><a href="<?php echo admin_url('brands/brand'); ?>"><i
                                            class="fa fa-plus"></i><?php echo _l('new_brand'); ?></a></li>
                        <?php } ?>
                    <?php } ?>
                    <li class="header-edit-profile"><a
                                href="<?php echo admin_url('staff/edit_profile'); ?>"><?php echo _l('nav_edit_profile'); ?></a>
                    </li>
                    <li class="header-logout"><a href="#"
                                                 onclick="logout(); return false;"><?php echo _l('nav_logout'); ?></a>
                    </li>
                </ul>
            </div>
            <div class="mobile-otheroptions collapse" id="mobile-collapse2" aria-expanded="false" style="height: 0px;"
                 role="navigation">
                <ul class="nav navbar-nav animated fadeIn" id="side-menutop">
                    <li id="top_search2" class="dropdown2">
                        <form method="get" name="search_form" onsubmit="filter_this(); return false;">
                            <input type="search" id="search_input" name="search_input" class="form-control"
                                   placeholder="<?php echo _l('top_search_placeholder'); ?>"
                                   value="<?php if (isset($_GET['search_input'])) {
                                       echo $_GET['search_input'];
                                   } ?>">
                            <div id="search_results"></div>
                            <button class="btn"><i class="fa fa-search"></i></button>
                        </form>
                    </li>
                    <li class="mob-sub-2">
                        <a href="<?php echo admin_url('todo'); ?>" class=""><i class="fa fa-list-ul"
                                                                               aria-hidden="true"></i> TODO</a>
                    </li>
                    <li class="mob-sub-3">
                        <?php $this->load->view('admin/includes/notifications'); ?>
                    </li>
                </ul>
            </div>
        </div>
        <!--<a href="#" class="btn btn-outline-primary mr-l-20 btn-sm btn-rounded hidden-xs hidden-sm ripple" target="_blank">Invited Events</a>-->
        <ul class="nav navbar-nav navbar-right">
            <?php
            //if(!is_mobile()){
            echo $top_search_area;
            //} ?>
            <?php do_action('after_render_top_search'); ?>

            <!-- <li class="icon header-business-news">
    <a href="<?php echo admin_url('business_news'); ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo _l('business_news'); ?>"><i class="fa fa-newspaper-o"></i></a>
</li>-->
            <?php //if(is_staff_member()){ ?>
            <!-- <li class="icon header-newsfeed">
                <a href="#" class="open_newsfeed"><i class="fa fa-commenting" aria-hidden="true"></i></a>
            </li> -->
            <?php //} ?>

            <!--<li class="icon header-newsfeed">
    <a href="<?php /*echo admin_url('brand_settings?group=search_filter'); */ ?>"><i class="fa fa-sliders"></i></a>
</li>-->
            <li class="quick-links">
                <a href="javascript:void(0);" class="dropdown-toggle" id="dropdownQuickLinks" data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="true">
                    <i class="fa fa-plus-square" aria-hidden="true"></i> &nbsp; New
                </a>
                <div class="dropdown-menu dropdown-left animated flipInY">
                    <span class="dropdown-header">Create new ...</span>
                    <?php
                    foreach ($quickActions as $key => $item) {
                        $url = '';
                        if (isset($item['permission'])) {
                            if (!has_permission($item['permission'], '', 'create')) {
                                continue;
                            }
                        }
                        if (isset($item['custom_url'])) {
                            $url = $item['url'];
                        } else {
                            $url = admin_url('' . $item['url']);
                        }
                        $href_attributes = '';
                        if (isset($item['href_attributes'])) {
                            foreach ($item['href_attributes'] as $key => $val) {
                                $href_attributes .= $key . '=' . '"' . $val . '"';
                            }
                        }
                        ?>
                        <a class="dropdown-item" href="<?php echo $url; ?>" <?php echo $href_attributes; ?>>
                            <?php echo $item['name']; ?></a>
                    <?php } ?>
                </div>
            </li>
            <?php
            /**
             * Added By : Vaidehi
             * Dt : 10/31/2017
             * show todo menu and notification count to account owner and team member only
             */
            if (isset($brandid) && $brandid > 0) { ?>
                <li class="icon header-todo">
                    <a href="<?php echo admin_url('todo'); ?>" data-toggle="tooltip"
                       title="<?php echo _l('nav_todo_items'); ?>" data-placement="bottom"><i class="fa fa-list-ul"></i>
                        <?php $_unfinished_todos = total_rows('tbltodoitems', array('finished' => 0, 'staffid' => get_staff_user_id(), 'brandid' => $brandid)); ?>
                        <span class="label label-warning icon-total-indicator nav-total-todos<?php if ($_unfinished_todos == 0) {
                            echo ' hide';
                        } ?>"><?php echo $_unfinished_todos; ?></span>
                    </a>
                </li>
            <?php } ?>
            <!-- <li class="icon header-timers">
    <a href="#" class="dropdown-toggle top-timers<?php //if(count($startedTimers) > 0){echo ' text-success';} ?>" data-toggle="dropdown"><span data-placement="bottom" data-toggle="tooltip" data-title="<?php //echo _l('project_timesheets'); ?>"><i class="fa fa-clock-o"></i></span></a>
    <ul class="dropdown-menu animated fadeIn started-timers-top width350" id="started-timers-top">
        <?php //$this->load->view('admin/tasks/started_timers',array('startedTimers'=>$startedTimers)); ?>
    </ul>
</li> -->
            <li class="dropdown notifications-wrapper header-notifications">
                <?php $this->load->view('admin/includes/notifications'); ?>
            </li>
            <li class="icon header-user-profile">
                <a href="#" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="false">
                    <?php echo staff_profile_image($current_user->staffid, array('img', 'img-responsive', 'staff-profile-image-small', 'pull-left')); ?>
                    <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu animated fadeIn" id="side-menutop">
                    <li class="dropdown-header"><?php echo $current_user->firstname ?> <i
                                class="fa fa-power-off top-left-logout pull-right" ata-title="Logout"
                                data-placement="right" onclick="logout(); return false;"></i>
                    </li>
                    <?php
                    /**
                     * Added By : Vaidehi
                     * Dt: 10/12/2017
                     * get all brands for account owner and team member
                     */
                    if (isset($brands) && $brands != "") { ?>
                        <!--<li class="dropdown-header">Brands</li>-->
                        <?php
                        foreach ($brands as $brand) {
                            $default = "";
                            if ($brand['isdefault'] == 1) {
                                $default = "isdefault";
                            }
                            ?>
                            <li class="brand <?php echo($brand['brandid'] == $brandid ? 'active' : ''); ?>"><a
                                        data-id="<?php echo $brand['brandid']; ?>" href="javascript: void(0);"
                                        onclick="changeBrand(this);"><?php echo $brand['name']; ?></a>
                                <a class="defaultBrand <?php echo $default; ?>"
                                   data-id="<?php echo $brand['brandid']; ?>" href="javascript: void(0);">
                                    <i class="fa fa-home" aria-hidden="true"></i>
                                </a>
                            </li>
                        <?php } ?>
                        <li class="divider"></li>
                        <?php if (has_permission('account_setup', '', 'edit') == true) { ?>
                            <li class="header-edit-profile"><a href="<?php echo admin_url('brand_settings'); ?>"><i
                                            class="fa fa-gear"></i><?php echo _l('brand_settings'); ?></a></li>
                        <?php } ?>
                        <?php if (has_permission('account_setup', '', 'edit') == true) { ?>
                            <li class="header-edit-profile"><a href="<?php echo admin_url('brands/brand'); ?>"><i
                                            class="fa fa-plus"></i><?php echo _l('new_brand'); ?></a></li>
                        <?php } ?>
                    <?php } ?>
                    <!-- <li class="header-my-profile"><a href="<?php //echo admin_url('profile'); ?>"><?php //echo _l('nav_my_profile'); ?></a></li>
        <li class="header-my-timesheets"><a href="<?php //echo admin_url('staff/timesheets'); ?>"><?php //echo _l('my_timesheets'); ?></a></li> -->
                    <li class="header-edit-profile"><a href="<?php echo admin_url('staff/edit_profile'); ?>"><i
                                    class="fa fa-pencil"></i><?php echo _l('nav_edit_profile'); ?></a></li>
                    <?php if (get_option('disable_language') == 0) { ?>
                        <li class="dropdown-submenu pull-left header-languages">
                            <a href="#" tabindex="-1"><?php echo _l('language'); ?></a>
                            <ul class="dropdown-menu dropdown-menu-left">
                                <li class="<?php if ($current_user->default_language == "") {
                                    echo 'active';
                                } ?>">
                                    <a href="<?php echo admin_url('staff/change_language'); ?>"><?php echo _l('system_default_string'); ?></a>
                                </li>
                                <?php foreach ($this->perfex_base->get_available_languages() as $user_lang) { ?>
                                    <li <?php if ($current_user->default_language == $user_lang) {
                                        echo 'class="active"';
                                    } ?>>
                                        <a href="<?php echo admin_url('staff/change_language/' . $user_lang); ?>"><?php echo ucfirst($user_lang); ?></a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        </ul>
    </nav>
</div>
<?php
if ($_SESSION['package_type_id'] == 1) {
    $trial_period = $_SESSION['trial_period'];
    $signupdate = $_SESSION['signupdate'];
    $date1 = date_create($signupdate);
    $date2 = date_create();
    $diff = date_diff($date1, $date2);
    $days_diff = $diff->days;
    $remaining_days = $trial_period - $days_diff;
    if ($remaining_days > 7) {
        $fcolor = "#bc181f";
        $bgcolor = "#f7e7e7";
        $class = "";
    } else {
        $fcolor = "#ff9800";
        $bgcolor = "#fdeed8";
        $class = "danger";
    }
    ?>
    <div id="wrapper" class="trial_messge_wrapper">
        <div id="trial_messge" class="trialmessage <?php echo $class; ?>">
            <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            <span><?php echo _l('trial_subscription', $remaining_days) ?></span>
            <a href="<?php echo admin_url('subscription') ?>" class="subscribe_now btn btn-info">
                <?php echo _l('subscribe_now') ?>
            </a>
        </div>
    </div>

<?php } ?>

