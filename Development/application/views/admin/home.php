<?php init_head(); ?>
<div id="wrapper">
    <div class="content dashboard-page">
        <div class="row">
            <div class="col-md-12">

                <div class="main-banners">
                    <?php
                    $banner = get_brand_option('banner');
                    $src = base_url('uploads/company/banner.jpg');
                    $csrc = base_url('uploads/company/banner.jpg');
                    ?>
                    <?php if ($banner != '') {
                        $path = get_upload_path_by_type('brands') . '/' . $banner;
                        if (file_exists($path)) {
                            $path = get_upload_path_by_type('brands') . '/croppie_' . $banner;
                            $src = base_url('uploads/brands/' . $banner);
                            if (file_exists($path)) {
                                $src = base_url('uploads/brands/croppie_' . $banner);
                            }
                        }
                    }

                    if (@$package_type->name == "Paid" && !empty($banner)) { ?>
                        <img src="<?php echo $src; ?>" style="width: 100%;">
                    <?php } else { ?>
                        <img src="<?php echo $csrc; ?>" style="width: 100%;">
                    <?php } ?>
                    <a class="banner_edit" href="<?php echo admin_url('brand_settings') . '?pg=home' ?>">
                        <i class="fa fa-pencil"></i>
                    </a>
                </div>
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <!--<i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span>Leads</span>-->
                </div>

                <?php $hour = date('H');
                if (date('H') < 12) {
                    $greeting = "Good morning";
                } elseif (date('H') >= 12 and date('H') < 17) {
                    $greeting = "Good afternoon";
                } else {
                    $greeting = "Good evening";
                }
                ?>
                <div class="welcomeUser"><?php echo $greeting . " " . $current_user->firstname ?>!
                    <a href="<?php echo admin_url("home/config"); ?>">
                        <i class="fa fa-cogs menu-icon mleft4"></i>
                    </a>
                </div>
                <!--<div class="welcomeUser">Welcome back <?php /*echo $current_user->firstname */ ?>!
                    <a href="<?php /*echo admin_url("home/config"); */ ?>">
                        <i class="fa fa-cogs menu-icon"></i>
                    </a>
                </div>-->
                <h1 class="pageTitleH1"><i class="fa fa-home"></i> Home</h1>
                <?php
                if (!empty($widget_data)) {
                $widget_data = (array)$widget_data;
                $all_data = $widget_data['widget_type'];
                $quick_link_list = $widget_data['quick_link_type'];

                $exp_val = explode(',', $all_data);
                $single_quick_link_list = explode(',', $quick_link_list);

                $all_data = json_decode($widget_data['order'], true);
                ?>
                <input type="hidden" name="tagid" value="<?php echo $this->session->userdata['staff_user_id']; ?>">
                <div class="clearfix"></div>
                <div class="row">
                    <?php foreach ($all_data as $json_order) {
                        if ($json_order['widget_name'] == "getting_started" || $json_order['widget_name'] == "lead_pipeline") {
                            ?>
                            <div id="<?php echo $json_order['widget_name'] ?>_widget" class="col-sm-12 option"
                                 data-class='option' data-id="<?php echo $json_order['widget_name'] ?>"
                                 data-order="<?php echo $json_order['order'] ?>">
                                <?php
                                if ($json_order['widget_name'] == "getting_started") {
                                    if (in_array('getting_started', $exp_val)) {
                                        $this->load->view('admin/home/getting_started');
                                    }
                                }
                                if ($json_order['widget_name'] == "lead_pipeline") {
                                    if (in_array('lead_pipeline', $exp_val)) {
                                        if (has_permission('leads', '', 'create', true)) {
                                            $this->load->view('admin/home/lead_pipeline');
                                        }
                                    }
                                } ?>
                            </div>
                        <?php }
                    } ?>
                </div>
                <?php
                unset($all_data[0]);
                unset($all_data[1]);
                ?>
                <div class="row row15">
                    <div id="sortable1" class="col-md-6 sortable_config_item">
                        <?php foreach ($all_data as $json_order) {
                            if ($json_order['widget_name'] != "getting_started" || $json_order['widget_name'] != "lead_pipeline") {
                                if ($json_order['order'] % 2 == 0) { ?>
                                    <div id="<?php echo $json_order['widget_name'] ?>_widget"
                                         class="row col-sm-12 option" data-class='option'
                                         data-id="<?php echo $json_order['widget_name'] ?>"
                                         data-order="<?php echo $json_order['order'] ?>">
                                        <?php
                                        if ($json_order['widget_name'] == "calendar") {
                                            if (in_array('calendar', $exp_val)) {
                                                $this->load->view('admin/home/calendar');
                                            }
                                        }
                                        ?>

                                        <?php
                                        if ($json_order['widget_name'] == "pinned_item") {
                                            if (in_array('pinned_item', $exp_val)) {
                                                $this->load->view('admin/home/pinned_items');
                                            }
                                        }
                                        ?>

                                        <?php
                                        if ($json_order['widget_name'] == "quick_link") {
                                            if (in_array('quick_link', $exp_val)) {
                                                $links['single_quick_link_list'] = $single_quick_link_list;
                                                $this->load->view('admin/home/quick_links', $links);
                                            }
                                        }
                                        ?>

                                        <?php
                                        if ($json_order['widget_name'] == "upcoming_project") {
                                            if (in_array('upcoming_project', $exp_val)) {
                                                if (is_admin() || is_sido_admin()) {
                                                    $visible = true;
                                                } else {
                                                    if (has_permission('projects', '', 'create', true)) {
                                                        $visible = true;
                                                    } else {
                                                        $visible = false;
                                                    }
                                                }
                                                if ($visible) {
                                                    $this->load->view('admin/home/upcoming_project');
                                                }
                                            }
                                        }
                                        ?>

                                        <?php
                                        if ($json_order['widget_name'] == "contacts") {
                                            if (in_array('contacts', $exp_val)) {
                                                if (has_permission('addressbook', '', 'view')) {
                                                    $this->load->view('admin/home/contacts');
                                                }
                                            }
                                        }
                                        if ($json_order['widget_name'] == "messages") {
                                            if (in_array('messages', $exp_val)) {
                                                if (has_permission('messages', '', 'create', true)) {
                                                    $this->load->view('admin/home/messages');
                                                }
                                            }
                                        }
                                        if ($json_order['widget_name'] == "task_list") {
                                            if (in_array('task_list', $exp_val)) {
                                                $this->load->view('admin/home/task_list');
                                            }
                                        }
                                        ?>
                                    </div>
                                <?php } ?>

                            <?php }
                        } ?>
                    </div>
                    <div id="sortable2" class="col-md-6 sortable_config_item">
                        <?php foreach ($all_data as $json_order) {
                            if ($json_order['widget_name'] != "getting_started" || $json_order['widget_name'] != "lead_pipeline") {
                                if ($json_order['order'] % 2 != 0) {
                                    ?>
                                    <div id="<?php echo $json_order['widget_name'] ?>_widget"
                                         class="row col-sm-12 option" data-class='option'
                                         data-id="<?php echo $json_order['widget_name'] ?>"
                                         data-order="<?php echo $json_order['order'] ?>">
                                        <?php
                                        if ($json_order['widget_name'] == "calendar") {
                                            if (in_array('calendar', $exp_val)) {
                                                $this->load->view('admin/home/calendar');
                                            }
                                        }
                                        ?>

                                        <?php
                                        if ($json_order['widget_name'] == "pinned_item") {
                                            if (in_array('pinned_item', $exp_val)) {
                                                $this->load->view('admin/home/pinned_items');
                                            }
                                        }
                                        ?>

                                        <?php
                                        if ($json_order['widget_name'] == "quick_link") {
                                            if (in_array('quick_link', $exp_val)) {
                                                $links['single_quick_link_list'] = $single_quick_link_list;
                                                $this->load->view('admin/home/quick_links', $links);
                                            }
                                        }
                                        ?>

                                        <?php
                                        if ($json_order['widget_name'] == "upcoming_project") {
                                            if (in_array('upcoming_project', $exp_val)) {
                                                if (is_admin() || is_sido_admin()) {
                                                    $visible = true;
                                                } else {
                                                    if (has_permission('projects', '', 'create', true)) {
                                                        $visible = true;
                                                    } else {
                                                        $visible = false;
                                                    }
                                                }
                                                if ($visible) {
                                                    $this->load->view('admin/home/upcoming_project');
                                                }
                                            }
                                        }
                                        ?>

                                        <?php
                                        if ($json_order['widget_name'] == "contacts") {
                                            if (in_array('contacts', $exp_val)) {
                                                if (has_permission('addressbook', '', 'view')) {
                                                    $this->load->view('admin/home/contacts');
                                                }
                                            }
                                        }
                                        if ($json_order['widget_name'] == "messages") {
                                            if (in_array('messages', $exp_val)) {
                                                if (has_permission('messages', '', 'create', true)) {
                                                    $this->load->view('admin/home/messages');
                                                }
                                            }
                                        }
                                        if ($json_order['widget_name'] == "task_list") {
                                            if (in_array('task_list', $exp_val)) {
                                                $this->load->view('admin/home/task_list');
                                            }
                                        }
                                        ?>
                                    </div>
                                <?php }
                            }
                        }
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--
  * Added by: Sanjay
  * Date: 02-05-2018
  * Popup to display option for duplicate product & service in current brand or existing brands.
  -->
<div class="modal fade" id="dashboard_create_list" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    Create new
                </h4>
            </div>
            <div class="modal-body">
                <?php if (has_permission('items', '', 'create')) { ?>
                    <div class="row">
                        <form action="" novalidate="1" id="dashboard_calender_list_form" method="post"
                              accept-charset="utf-8">
                            <div id="additional"></div>
                            <div class="form-group">


                                <div class="col-md-12">
                                    <div class="radio radio-primary radio-inline">
                                        <input type="radio" id="calender_list" name="calender_list" class="task"
                                               value="task">
                                        <label for="tasks">Tasks</label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="radio radio-primary radio-inline">
                                        <input type="radio" id="calender_list" name="calender_list" class="meetings"
                                               value="meeting">
                                        <label for="meeting">Meeting</label>
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="radio radio-primary radio-inline">
                                        <input type="radio" id="calender_list" name="calender_list" class="leads"
                                               value="lead">
                                        <label for="leads">Leads</label>
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="radio radio-primary radio-inline">
                                        <input type="radio" id="calender_list" name="calender_list" class="invoices"
                                               value="invoice">
                                        <label for="invoices">Invoices</label>
                                    </div>
                                </div>


                                <div class="col-md-12">
                                    <div class="radio radio-primary radio-inline">
                                        <input type="radio" id="calender_list" name="calender_list" class="projects"
                                               value="project">
                                        <label for="projects">Projects</label>
                                    </div>
                                </div>
                                <input type="hidden" name="current_date" id="current_date"
                                       value="<?php echo date('m/d/Y'); ?>">
                            </div>
                    </div>

                <?php } ?>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-info" id="add_subcategory"><?php echo _l('Create'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css"/>


<script type="text/javascript">
    /**
     * Added By: Sanjay
     * Dt: 03/07/2018
     * for all listing load more functionality
     */
    $(function () {

        /*$(".upcoming_item_master_list_content").slice(0, 5).show();
        $("#all_my_upcoming_loadMore").on('click', function (e) {
            e.preventDefault();
            $(".upcoming_item_master_list_content:hidden").slice(0, 5).slideDown();
            if ($(".upcoming_item_master_list_content:hidden").length == 0) {
                $("#load").fadeOut('slow');
            }
        });

        $(".upcoming_item_all_master_list_content").slice(0, 5).show();
        $("#all_master_upcoming_loadMore").on('click', function (e) {
            e.preventDefault();
            $(".upcoming_item_all_master_list_content:hidden").slice(0, 5).slideDown();
            if ($(".upcoming_item_all_master_list_content:hidden").length == 0) {
                $("#load").fadeOut('slow');
            }
        });*/

        /*$(".all_msg_master_list_content").slice(0, 5).show();
        $("#all_unread_msg_loadMore").on('click', function (e) {
            e.preventDefault();
            $(".all_msg_master_list_content:hidden").slice(0, 5).slideDown();
            if ($(".all_msg_master_list_content:hidden").length == 0) {
                $("#load").fadeOut('slow');
            }
        });*/
    });
</script>


<script>
    var c_leadid = '<?php echo $leadid; ?>';
    $(function () {

        $("#sortable1, #sortable2").sortable({
            handle: ".handle",
            cursor: "all-scroll",
            placeholder: 'highlight',
            connectWith: ".sortable_config_item",
            stop: function (event, ui) {
                var clas = ui.item.attr("data-class");

                order = 2;
                count = 0;
                var option = [];
                var option_val1 = {
                    'widget_name': 'getting_started',
                    'order': 0,
                };
                var option_val2 = {
                    'widget_name': 'lead_pipeline',
                    'order': 1,
                };
                option.push(option_val1);
                option.push(option_val2);
                $("#sortable1 ." + clas).each(function () {
                    var id = $(this).attr('data-id');
                    //order = $(this).attr('data-order');
                    order = order;
                    var option_val = {
                        'widget_name': id,
                        'order': order,
                    };

                    $(this).attr('data-order', count);
                    option.push(option_val);
                    count++;
                    order += 2;
                });
                order = 3;
                $("#sortable2 ." + clas).each(function () {
                    var id = $(this).attr('data-id');
                    //order = $(this).attr('data-order');
                    order = order;
                    var option_val = {
                        'widget_name': id,
                        'order': order,
                    };

                    $(this).attr('data-order', count);
                    option.push(option_val);
                    count++;
                    order += 2;
                });
                option = JSON.stringify(option);
                /*alert(option);
                exite;*/
                var url = "<?php echo admin_url('home/ajax_widget_order_update'); ?>";

                $.ajax({
                    method: "POST",
                    url: url,
                    data: "options=" + option,
                }).done(function () {
                });
            }
        });
        $("#sortable1, #sortable2").disableSelection();
    });
</script>
<script>


    /*Calling pin function for dashboar project pins*/
    $('body').on('click', '.dashboard-page .project-pin', function () {
        var project_id = $(this).attr('project_id');
        $.ajax({
            type: 'POST',
            data: {
                project_id: project_id
            },
            url: admin_url + "projects/pinproject"
        }).done(function (response) {
            if (response == 'added') {
                $('#' + project_id).addClass('pinned');
            } else {
                $('#' + project_id).removeClass('pinned');
            }
            //$('.pin_items_container').load(document.URL + ' .pin_items_container');
        });

    });

    /*Calling pin function for dashboard lead pins*/
    $('body').on('click', '.dashboard-page .lead-pin', function () {
        var lead_id = $(this).attr('lead_id');
        $.ajax({
            type: 'POST',
            data: {
                lead_id: lead_id
            },
            url: admin_url + "leads/pinlead"
        }).done(function (response) {
            if (response == 'added') {
                $('#' + lead_id).addClass('pinned');
            } else {
                $('#' + lead_id).removeClass('pinned');
            }
            //$('.pin_items_container').load(document.URL + ' .pin_items_container');
        });
    });

    /*Calling pin function for dashboard task pins*/
    $('body').on('click', '.dashboard-page .task-pin', function () {
        var task_id = $(this).attr('task_id');
        $.ajax({
            type: 'POST',
            data: {
                task_id: task_id
            },
            url: admin_url + "tasks/pintask"
        }).done(function (response) {
            if (response == 'added') {
                $('#' + task_id).addClass('pinned');
            } else {
                $('#' + task_id).removeClass('pinned');
            }
            //$('.pin_items_container').load(document.URL + ' .pin_items_container');
        });
    });

    /**
     * Added By: Sanjay
     * Dt: 03/07/2018
     * for pinned message
     */
    $('body').on('click', '.dashboard-page .message-pin', function () {
        var message_id = $(this).attr('message_id');
        $.ajax({
            type: 'POST',
            data: {
                message_id: message_id
            },
            url: admin_url + "messages/pinmessage"
        }).done(function (response) {
            if (response == 'added') {
                $('#' + message_id).addClass('pinned');
            } else {
                $('#' + message_id).removeClass('pinned');
            }
            //$('.pin_items_container').load(document.URL + ' .pin_items_container');
        });
    });

    /**
     * Added By: Sanjay
     * Dt: 03/07/2018
     * for pinned contact
     */
    $('body').on('click', '.dashboard-page .contact-pin', function () {
        var contact_id = $(this).attr('contact_id');
        var new_all_count = $('.all_pin_cont_count').text() - 1;
        var new_contact_count = $('.nav-item .contact_count').text() - 1;

        $('.all_pin_cont_count').text(new_all_count);
        $('.nav-item .contact_count').text(new_contact_count);
        $(".contact_" + contact_id).remove();
        /*$.ajax({
            type: 'POST',
            data: {
                contact_id: contact_id
            },
            url: admin_url + "addressbooks/pincontact"
        }).done(function (response) {
            if (response == 1) {
                $('#' + contact_id).addClass('pinned');
            } else {
                $('#' + contact_id).removeClass('pinned');
            }
            //$('.pin_contact_data_container').load(document.URL + ' .pin_contact_data_container');
        });*/
    });


    /**
     * Added By: Sanjay
     * Dt: 03/07/2018
     * for pinned venue
     */
    $('body').on('click', '.dashboard-page .venue-pin', function () {
        var id = $(this).attr('id');

        var venue_id = $(this).attr('venue_id');

        var new_all_count = $('.all_pin_cont_count').text() - 1;
        var new_venue_count = $('.nav-item .venue_count').text() - 1;

        $('.all_pin_cont_count').text(new_all_count);
        $('.nav-item .venue_count').text(new_venue_count);
        $(".venue_" + venue_id).remove();

        /*$.ajax({
            type: 'POST',
            data: {
                venue_id: venue_id
            },
            url: admin_url + "venues/pinvenue"
        }).done(function (response) {
            if (response == 'added') {
                $('#' + venue_id).addClass('pinned');
            } else {
                $('#' + venue_id).removeClass('pinned');
            }

            //console.log(id);

            //$( '.pinned_venues_list_content i'+id ).hide();
            //$('.pin_contact_data_container').load(document.URL + ' .pin_contact_data_container');
            //$(".unique_pinned_contact_widget").load(location.href + " .unique_pinned_contact_widget");
            //$("#unique_pinned_contact_widget").reload(location.href+" #unique_pinned_contact_widget>*","");
            //$(".pin_contact_data_container").load();
        });*/
    });


    /*$('body').on('click', '.dashboard-page .pinned_venues_list_content', function () {
        var v_id = $(this).attr('id');
        //$('#' + v_id).hide();
        var old_ven_val = $('.venue_count').html() - 1;
        $('.venue_count').html(old_ven_val);
        var old_total_pin_count_val = $('.all_pin_cont_count').html() - 1;
        //$('.all_pin_cont_count').html(old_total_pin_count_val);
        //location.reload();
    });*/


    /*$('body').on('click', '.dashboard-page .pinned_contact_list_content', function () {
        var c_id = $(this).attr('id');
        //$('#' + c_id).hide();
        var old_con_val = $('.contact_count').html() - 1;
        $('.contact_count').html(old_con_val);
        var old_total_pin_count_val = $('.all_pin_cont_count').html() - 1;
        //$('.all_pin_cont_count').html(old_total_pin_count_val);
        //location.reload();
    });*/

    /*$('body').on('click', '.dashboard-page .pinned_all_contact_list_content', function () {
        var a_id = $(this).attr('id');
        //$('#' + a_id).hide();
        var old_con_val = $('.all_master_list_count').html() - 1;
        $('.all_master_list_count').html(old_con_val);
        var old_total_pin_count_val = $('.all_pin_cont_count').html() - 1;
        //$('.all_pin_cont_count').html(old_total_pin_count_val);
        //location.reload();
    });*/

    /*$('body').on('click', '.dashboard-page .pinned_all_contact_list_content.only_ven_sec', function () {
        var a_id = $(this).attr('id');
        $('#' + a_id).hide();
    });*/

    $('body').on('click', '.dashboard-page .pinned_item_msg_list_content', function () {
        var c_id = $(this).attr('id');
        //$('#' + c_id).hide();
        var old_con_val = $('.pin_msg_count').html() - 1;
        $('.pin_msg_count').html(old_con_val);
        var old_total_pin_count_val = $('.all_msg_only_count').html() - 1;
        //$('.all_msg_only_count').html(old_total_pin_count_val);
        var master_pin_items_cnt = $('.master_pin_items_cnt').html() - 1;
        //$('.master_pin_items_cnt').html(master_pin_items_cnt);
        location.reload();
    });

    $('body').on('click', '.dashboard-page .pinned_item_task_list_content', function () {
        var c_id = $(this).attr('id');
        //$('#' + c_id).hide();
        var old_con_val = $('.pin_task_count').html() - 1;
        //$('.pin_task_count').html(old_con_val);
        var old_total_pin_count_val = $('.all_task_only_count').html() - 1;
        //$('.all_task_only_count').html(old_total_pin_count_val);
        var master_pin_items_cnt = $('.master_pin_items_cnt').html() - 1;
        // $('.master_pin_items_cnt').html(master_pin_items_cnt);
        //location.reload();
    });

    $('body').on('click', '.dashboard-page .pinned_item_lead_list_content', function () {
        var c_id = $(this).attr('id');
        //$('#' + c_id).hide();
        var old_con_val = $('.pin_lead_count').html() - 1;
        //$('.pin_lead_count').html(old_con_val);
        var old_total_pin_count_val = $('.all_lead_only_count').html() - 1;
        //$('.all_lead_only_count').html(old_total_pin_count_val);
        var master_pin_items_cnt = $('.master_pin_items_cnt').html() - 1;
        //$('.master_pin_items_cnt').html(master_pin_items_cnt);
        //location.reload();
    });

    $('body').on('click', '.dashboard-page .pinned_item_project_list_content', function () {
        var c_id = $(this).attr('id');
        //$('#' + c_id).hide();
        var old_con_val = $('.pin_proj_count').html() - 1;
        //$('.pin_proj_count').html(old_con_val);
        var old_total_pin_count_val = $('.all_proj_only_count').html() - 1;
        //$('.all_proj_only_count').html(old_total_pin_count_val);
        var master_pin_items_cnt = $('.master_pin_items_cnt').html() - 1;
        //$('.master_pin_items_cnt').html(master_pin_items_cnt);
        //location.reload();
    });


    $('body').on('click', '.dashboard-page .pinned_item_master_list_content', function () {
        var c_id = $(this).attr('id');
        //$('#' + c_id).hide();
        var old_total_pin_count_val = $('.all_pin_data_only_count').html() - 1;
        //$('.all_pin_data_only_count').html(old_total_pin_count_val);
        var master_pin_items_cnt = $('.master_pin_items_cnt').html() - 1;
        //$('.master_pin_items_cnt').html(master_pin_items_cnt);
        //location.reload();
    });

    <?php if(is_mobile()) { ?>
    $(".toggle_control_cutton").click(function () {
        var pid = $(this).attr('data-pid');
        $(pid + ' .widget-body').slideToggle();
        $('i', this).toggleClass('fa-caret-down fa-caret-up');
    });
    <?php }else{ ?>
    $('body').on('click', '.toggle_control_cutton', function () {
        var pid = $(this).attr('data-pid');
        $(pid + ' .widget-body').slideToggle();
        $('i', this).toggleClass('fa-caret-down fa-caret-up');
    });
    <?php } ?>

    $(function () {
        //setTimeout(function(){ $('td.fc-today').trigger('click'); }, 2000);
        //slideToggle('.leads-overview');

        leads_kanban();
        $('input[name="view_inquireddate"]').daterangepicker({
            locale: {
                format: 'MM/DD/YYYY'
            },
            clearBtn: true
        });

        $('input[name="view_eventdate"]').daterangepicker({
            locale: {
                format: 'MM/DD/YYYY'
            }
        });

        $(".message_list li a").click(function () {
            $(".message_list_content .tab-pane").toggleClass("active");
        });

        $(".contact_list li a").click(function () {
            var id = $(this).attr('href');
            $(".contact_list_content .tab-pane").removeClass("active");
            $(".contact_list_content " + id).addClass("active");
        });

        $(".all_items li a").click(function () {
            var id = $(this).attr('href');
            $(".all_items_content .tab-pane").removeClass("active");
            $(".all_items_content " + id).addClass("active");
        });

        /*$('[data-toggle="popover"]').popover({

            placement: "top",
            trigger: "hover",
            content: $("#popover-content").parent().html()
        });*/

        $('[rel="popover"]').popover({
            placement: "top",
            trigger: "hover",
            container: 'body',
            html: true,
            content: function () {
                var clone = $($(this).data('popover-content')).clone(true).removeClass('hide');
                return clone;
            }
        }).click(function (e) {
            e.preventDefault();
        });

        $('.popooveContact').popover({
            placement: "left",
            trigger: "click",
            container: 'body',
            html: true,
            content: function () {
                var clone = $($(this).data('popover-content')).clone(true).removeClass('hide');
                return clone;
            }
        }).click(function (e) {
            e.preventDefault();
        });
    });

    /*
   ** Added By Sanjay on 02/14/2018
   ** Redirecting from dashboard to lead pipeline for filtered lead data
   */
    function filterstatus(id) {
        window.location = admin_url + "leads?type=" + id + "&pg=home";
    }
</script>
<script type="text/javascript">
    calendar_selector = $('#dashboard_calendar');

    if (calendar_selector.length > 0) {
        validate_calendar_form();
        var calendar_settings = {
            height: 550,
            customButtons: {},
            header: {
                /*left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay,viewFullCalendar,calendarFilter'*/
                left: 'prev',
                center: 'title',
                right: 'next'
            },
            footer: {
                center: 'viewFullCalendar',
            },
            editable: false,
            //eventLimit: parseInt(app_calendar_events_limit) + 1,
            eventLimit: 2,

            views: {
                day: {
                    eventLimit: false
                }
            },
            defaultView: app_default_view_calendar,
            isRTL: (isRTL == 'true' ? true : false),
            eventStartEditable: false,
            timezone: app_timezone,
            firstDay: parseInt(app_calendar_first_day),
            year: moment.tz(app_timezone).format("YYYY"),
            month: moment.tz(app_timezone).format("M"),
            date: moment.tz(app_timezone).format("DD"),
            loading: function (isLoading, view) {
                if (!isLoading) { // isLoading gives boolean value
                    $('.dt-loader').addClass('hide');
                } else {
                    $('.dt-loader').removeClass('hide');
                }
            },
            eventSources: [{
                url: admin_url + 'calendar/get_calendar_data',
                data: function () {
                    var filterParams = {};
                    $('#calendar_filters').find('input:checkbox:checked').map(function () {
                        filterParams[$(this).attr('name')] = true;
                    }).get();
                    if (!jQuery.isEmptyObject(filterParams)) {
                        filterParams['calendar_filters'] = true;
                        return filterParams;
                    }
                },
                type: 'POST',
                error: function () {
                    console.error('There was error fetching calendar data');
                },
            },],
            eventLimitClick: function (cellInfo, jsEvent) {
                $('#calendar').fullCalendar('gotoDate', cellInfo.date);
                $('#calendar').fullCalendar('changeView', 'basicDay');
            },
            eventRender: function (event, element) {
                element.attr('title', event._tooltip);
                element.attr('onclick', event.onclick);
                element.attr('data-toggle', 'tooltip');
                element.attr('data-html', 'true');
                if (!event.url) {
                    element.click(function () {
                        view_event(event.eventid);
                    });
                }
            },

            dayClick: function (date, start, allDay, jsEvent, view) {
                $(this).addClass("disabled");
                var date = moment.utc($(this).data("date"));
                var formattedDate = new Date(date);
                var d = formattedDate.getDate();
                var m = formattedDate.getMonth() + 1;
                var y = formattedDate.getFullYear();
                var ndt = m + "/" + d + "/" + y;
                $('#current_date').val(ndt);
                var events = calendar_selector.fullCalendar("clientEvents", function (event) {
                    return event.start.startOf("day").isSame(date);
                });
                $("#event_list").empty();
                $(".fc-event-container");
                console.log('lenth: ' + events.length);
                $.each(events, function (key, val) {
                    title = '<div class="mtop10 eventlistrow"><div class="icon_section mright10"><i class="fa fa-calendar menu-icon"></i></div><div class="eRow"><div class="eTitle"> ' + val.title + '</div>';
                    var currentdate = new Date(val.date);
                    var am_pm = currentdate.getHours() >= 12 ? "PM" : "AM";
                    var exac_hr = currentdate.getHours() > 12 ? currentdate.getHours() - 12 : currentdate.getHours();
                    var datetime = (currentdate.getMonth() < 9 ? '0' : '') + (currentdate.getMonth() + 1) + "-"
                        + (currentdate.getDate()) + "-"
                        + currentdate.getFullYear() + " at "
                        + exac_hr + ":"
                        + currentdate.getMinutes() + " " + am_pm;
                    date = '<div class="eDate">' + datetime + '</div></div></div>';
                    //$("#event_list").append(title + date);
                    var data = {'id': val.id, 'type': val.type};
                    $.ajax({

                        type: 'POST',
                        url: admin_url + 'calendar/get_calendar_event_data',
                        data: data,
                        success: function (result) {
                            $("#event_list").append(result);

                        }
                    });
                });
                /*});*/
            },

            eventAfterAllRender: function () {
                var date = moment.utc($('td.fc-today').data("date"));
                var events = calendar_selector.fullCalendar("clientEvents", function (event) {
                    return event.start.startOf("day").isSame(date);
                });
                $.each(events, function (key, val) {
                    title = '<div class="mtop10 eventlistrow"><div class="icon_section mright10"><i class="fa fa-calendar menu-icon"></i></div><div class="eRow"><div class="eTitle"> ' + val.title + '</div>';
                    var currentdate = new Date(val.date);
                    var am_pm = currentdate.getHours() >= 12 ? "PM" : "AM";
                    var exac_hr = currentdate.getHours() > 12 ? currentdate.getHours() - 12 : currentdate.getHours();
                    var datetime = (currentdate.getMonth() < 9 ? '0' : '') + (currentdate.getMonth() + 1) + "-"
                        + (currentdate.getDate()) + "-"
                        + currentdate.getFullYear() + " at "
                        + exac_hr + ":"
                        + currentdate.getMinutes() + " " + am_pm;
                    date = '<div class="eDate">' + datetime + '</div></div></div>';
                    //$("#event_list").append(title + date);
                    var data = {'id': val.id, 'type': val.type};
                    $.ajax({
                        type: 'POST',
                        url: admin_url + 'calendar/get_calendar_event_data',
                        data: data,
                        success: function (result) {
                            $("#event_list").append(result);

                        }
                    });
                });
            }
        }
        if ($('body').hasClass('home')) {
            calendar_settings.customButtons.viewFullCalendar = {
                text: "calendar",
                click: function () {
                    window.location.href = admin_url + 'calendar/index';
                }
            }
        }

        if (is_staff_member == 1) {
            if (google_api != '') {
                calendar_settings.googleCalendarApiKey = google_api;
            }
            if (calendarIDs != '') {
                calendarIDs = JSON.parse(calendarIDs);
                if (calendarIDs.length != 0) {
                    if (google_api != '') {
                        for (var i = 0; i < calendarIDs.length; i++) {
                            var _gcal = {};
                            _gcal.googleCalendarId = calendarIDs[i];
                            calendar_settings.eventSources.push(_gcal);
                        }
                    } else {
                        console.error('You have setup Google Calendar IDs but you dont have specified Google API key. To setup Google API key navigate to Setup->Settings->Misc');
                    }
                }
            }
        }
        // Init calendar
        calendar_selector.fullCalendar(calendar_settings);
        var new_event = get_url_param('new_event');
        if (new_event) {
            $('#newEventModal').modal('show');
            $("input[name='start'].datetimepicker").val(get_url_param('date'));
        }
    }
</script>
</body>
</html>
