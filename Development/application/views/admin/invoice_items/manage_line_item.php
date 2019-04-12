<?php init_head(); ?>
<div id="wrapper">
    <div class="content manage-lineitem-category">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('setup'); ?>"><?php echo _l('breadcrum_setting_label'); ?></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('invoice_items'); ?>"><?php echo _l('breadcrum_product_service_label'); ?></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span><?php echo _l('breadcrum_product_service_category_label'); ?></span>
                </div>

                <h1 class="pageTitleH1"><i class="fa fa-list-ul "></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <?php if (has_permission('items', '', 'create')) { ?>
                            <div class="_buttons">
                                <a href="#" class="btn btn-info pull-left mleft5" data-toggle="modal"
                                   data-target="#line_item_category"><?php echo _l('add_category_button_label'); ?></a>
                                <a href="#" class="btn btn-info pull-left mleft5" data-toggle="modal"
                                   data-target="#line_item_sub_category"><?php echo _l('add_sub_category_button_label'); ?></a>
                            </div>
                            <div class="clearfix"></div>
                        <?php } ?>
                        <?php if (is_mobile()) {
                            echo '<a class="btn btn-primary  filter_btn_search"><i class="glyphicon glyphicon-search"></i></a>';
                        } ?>
                        <table class="table dt-table scroll-responsive" data-order-col="0" data-order-type="asc">
                            <thead>
                            <th><?php echo _l('expense_add_edit_name'); ?></th>
                            <th></th>
                            </thead>
                            <tbody>
                            <?php foreach ($product_service_groups as $pcategory_data) {
                                $all_category_data = get_line_item_sub_category_list($pcategory_data['id']); ?>
                                <tr id="<?php echo $pcategory_data['id']; ?>">
                                    <td >
                                        <?php echo $pcategory_data['name']; ?>
                                        <div class="group_edit hide">
                                            <div class="input-group">
                                                <input type="text" class="form-control">
                                                <span class="input-group-btn">
                            <button class="btn btn-info p7 update-line-item-category"
                                    type="button" data-id="<?php echo $pcategory_data['id'] ?>">
                                <?php echo _l('submit'); ?>
                            </button>
                          </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class='text-right mright10'><a class='show_act'
                                                                            href='javascript:void(0)'><i
                                                        class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div>
                                        <div class='table_actions'>
                                            <ul>
                                                <?php if (has_permission('items', '', 'edit')) { ?>
                                                    <li>
                                                        <a href="javascript:void(0)" type="button"
                                                           class="btn-icon edit-pitem-group"
                                                           data-id="<?php echo $pcategory_data['id'] ?>"
                                                           data-name="<?php echo $pcategory_data['name'] ?>">
                                                            <i class="fa fa-pencil-square-o"></i>Edit
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                                <?php if (has_permission('items', '', 'delete')) { ?>
                                                    <li><a
                                                            href="<?php echo admin_url('invoice_items/delete_line_item_category/' . $pcategory_data['id']); ?>"
                                                            class="delete-item-group _delete">
                                                        <i class="fa fa-remove"></i>Delete</a></li><?php } ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php foreach ($all_category_data as $category_data) { ?>
                                    <tr>
                                        <td>
                                            <?php //echo $category_data['parent_category']; ?>
                                            <?php echo $pcategory_data['name']; ?>
                                            <b class="mright10 mleft10"> >> </b> <?php echo $category_data['name']; ?>
                                        </td>
                                        <td>
                                            <div class='text-right mright10'><a class='show_act'
                                                                                href='javascript:void(0)'><i
                                                            class='fa fa-ellipsis-v' aria-hidden='true'></i></a></div>
                                            <div class='table_actions'>
                                                <ul>
                                                    <li><a href="#"
                                                           onclick="edit_status(this,<?php echo $category_data['id']; ?>);return false;"
                                                           data-id="<?php echo $category_data['id']; ?>"
                                                           data-name="<?php echo $category_data['name']; ?>"
                                                           data-maincat="<?php echo $category_data['parent_id']; ?>"
                                                           class=" btn-icon"><i
                                                                    class="fa fa-pencil-square-o"></i>Edit</a>
                                                    </li>
                                                    <li>
                                                        <a href="<?php echo admin_url('invoice_items/delete_category_status/' . $category_data['id']); ?>"
                                                           class=" btn-icon _delete"><i
                                                                    class="fa fa-remove"></i>Delete</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--
* Added by: Sanjay
* Date: 02-05-2018
* Popup for adding Line item master category
-->
<div class="modal fade" id="line_item_category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('add_category_button_label'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <?php if (has_permission('items', '', 'create')) { ?>
                    <div class="row">
                        <?php echo form_open('admin/invoice_items/add_line_item_category', array('id' => 'category_form')); ?>
                        <?php echo form_hidden('id'); ?>
                        <div class="col-md-11">
                            <label for="add_product_service"><?php echo _l('add_category_button_label'); ?>
                                <small class="req text-danger">*</small>
                            </label>
                            <input type="text" name="name" id="name" class="form-control"
                                   placeholder="<?php echo _l('item_group_name'); ?>">
                        </div>
                        <div class="col-md-1">
                            <label for="add"></label>
                            <span class="pull-right mtop30">
                  <button class="btn btn-info p9" type="submit" id="new-line-item-category-insert">Add</button>
                </span>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                    <hr/>
                <?php } ?>
                <div class="row">
                    <div class="container-fluid">
                        <table class="table table-striped dt-table table-items-groups" data-order-col="0"
                               data-order-type="asc">
                            <thead>
                            <tr>
                                <th><?php echo _l('item_group_name'); ?></th>
                                <th><?php echo _l(''); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($product_service_groups as $group) { ?>
                                <tr data-group-row-id="<?php echo $group['id']; ?>">
                                    <td data-order="<?php echo $group['name']; ?>">
                                        <span class="group_name_plain_text"><?php echo $group['name']; ?></span>
                                        <div class="group_edit hide">
                                            <div class="input-group">
                                                <input type="text" class="form-control">
                                                <span class="input-group-btn">
                            <button class="btn btn-info p7 update-line-item-category"
                                    type="button"><?php echo _l('submit'); ?></button>
                          </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td align="right">
                                        <div class='text-right mright10'>
                                            <a class='show_act' href='javascript:void(0)'>
                                                <i class='fa fa-ellipsis-v' aria-hidden='true'></i>
                                            </a>
                                        </div>
                                        <div class='table_actions'>
                                            <ul>
                                                <?php if (has_permission('items', '', 'edit')) { ?>
                                                    <li><a href="javascript:void(0)" type="button"
                                                           class="btn-icon edit-item-group1">
                                                            <i class="fa fa-pencil-square-o"></i>Edit
                                                        </a></li>
                                                <?php } ?>
                                                <?php if (has_permission('items', '', 'delete')) { ?>
                                                    <li><a
                                                            href="<?php echo admin_url('invoice_items/delete_line_item_category/' . $group['id']); ?>"
                                                            class="delete-item-group _delete">
                                                        <i class="fa fa-remove"></i>Delete</a></li><?php } ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>


<!--
* Added by: Sanjay
* Date: 02-05-2018
* Popup for adding Line item sub category
-->
<div class="modal fade" id="line_item_sub_category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('add_sub_category_button_label'); ?>
                </h4>
            </div>

            <?php echo form_open(admin_url('invoice_items/update_line_item_sub_category/'), array('novalidate' => true, 'id' => 'sub_cat_form')); ?>
            <div class="modal-body">
                <?php if (has_permission('items', '', 'create')) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="additional"></div>
                            <label for="option_parent_category"><?php echo _l('choose_category_option_label'); ?>
                                <small class="req text-danger">*</small>
                            </label>
                            <div class="form-group">
                                <select class="selectpicker col-md-12" name="parent_id" id="parent_id">
                                    <option value=""><?php echo _l('line_item_cat_subcategory_option_title'); ?></option>
                                    <?php foreach ($product_service_groups as $group) { ?>
                                        <option value="<?php echo $group['id']; ?>"><?php echo $group['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <label for="line_item_subcategory"><?php echo _l('choose_sub_category_option_label'); ?>
                                <small class="req text-danger">*</small>
                            </label>
                            <input type="text" name="name" id="name" class="form-control"
                                   placeholder="<?php echo _l('item_group_name'); ?>">
                        </div>
                        <div class="col-md-2">
                <span class="pull-right mtop25">
                  <button type="submit" class="btn btn-info" id="add_subcategory"><?php echo _l('submit'); ?></button>
                </span>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php echo form_close(); ?>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
<script>
    function manage_category(form) {
        var data = $('#category_form').serialize();
        var url = form.action;

        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            //alert(response);
            if (response.success == true) {
                alert_float('success', response.message);
            } else {
                if (response.message != '') {
                    alert_float('warning', response.message);
                }
            }
            $('#line_item_category').modal('hide');
        });
        return false;
    }

    /*
    * Added by: Sanjay
    * Date: 02-05-2018
    * Passing data to edit product & services
    */
    function edit_status(invoker, id) {
        var id = $(invoker).data('id');
        var name = $(invoker).data('name');
        var maincat = $(invoker).data('maincat');

        $('#additional').append(hidden_input('id', id));

        var groups = <?php echo json_encode($product_service_groups); ?>;

        var $el = $("#parent_id");
        $el.empty(); // remove old options
        $.each(groups, function (key, value) {
            if (maincat == value.id) {
                $el.append($("<option></option>")
                    .attr("value", value.id).text(value.name)).attr('selected', 'selected');
            } else {
                $el.append($("<option></option>")
                    .attr("value", value.id).text(value.name));
            }
        });

        //Get the text using the value of select
        var text = $("select[name=parent_id] option[value='" + maincat + "']").text();
        //We need to show the text inside the span that the plugin show
        $('.bootstrap-select .filter-option').text(text);
        //Check the selected attribute for the real select
        $('select[name=parent_id]').val(maincat);

        $('#line_item_sub_category input[name="name"]').val(name);
        $('#line_item_sub_category').modal('show');
        $("#add_subcategory").html('Update');
        $("#line_item_sub_category #myModalLabel").html('Edit Sub Category');
        $('.add-title').addClass('hide');
    }

    $(function () {

        initDataTable('.table-invoice-items-cat', window.location.href, [], [], '', [0, "ASC"]);

        if (get_url_param('groups_modal')) {
            // Set time out user to see the message
            setTimeout(function () {
                $('#groups').modal('show');
            }, 1000);
        }

        if (get_url_param('line_item_category')) {
            // Set time out user to see the message
            setTimeout(function () {
                $('#line_item_category').modal('show');
            }, 1000);
        }

        $('#new-item-group-insert').on('click', function () {
            var group_name = $('#item_group_name').val();
            if (group_name != '') {
                $.post(admin_url + 'invoice_items/add_group', {name: group_name}).done(function () {
                    window.location.href = admin_url + 'invoice_items?groups_modal=true';
                });
            }
        });

        $('body').on('click', '.edit-item-group', function () {
            var tr = $(this).parents('tr'),
                group_id = tr.attr('data-group-row-id');
            tr.find('.group_name_plain_text').toggleClass('hide');
            tr.find('.group_edit').toggleClass('hide');
            tr.find('.group_edit input').val(tr.find('.group_name_plain_text').text());
        });

        $('body').on('click', '.edit-pitem-group', function () {
            var tr = $(this).parents('tr'),
                group_id = $(this).data('id');
            group_name = $(this).data('name');
            tr.find('.group_name_plain_text').toggleClass('hide');
            tr.find('.group_edit').toggleClass('hide');
            tr.find('.group_edit input').val(group_name);
        });

        $('body').on('click', '.update-item-group', function () {
            var tr = $(this).parents('tr');
            var group_id = tr.attr('data-group-row-id');
            var name = tr.find('.group_edit input').val();
            if (name != '') {
                $.post(admin_url + 'invoice_items/update_group/' + group_id, {name: name}).done(function () {
                    window.location.href = admin_url + 'invoice_items?groups_modal=true';
                });
            }
        });

        /*_validate_form($('#category_form'), {
            name: {
                required: true,
                remote: {
                    url: admin_url + "invoice_items/category_name_exists",
                    type: 'post',
                    data: {
                        id: function () {
                            return $('input[name="id"]').val();
                        }
                    }
                }
            }
        });*/

        /*_validate_form($('#sub_cat_form'), {
            name: {required: true},
            parent_id: {required: true},
        });*/

        /*$('#lineitem_cat_save').on('click', function () {
            var name = $('#name').val();
            if (name != '') {
                $.post(admin_url + 'invoice_items/add_line_item_category', {name: name}).done(function () {
                    /!*window.location.href = admin_url + 'invoice_items/view_line_item_category?line_item_category=true';*!/
                    window.location.href = admin_url + 'invoice_items/view_line_item_category';
                });
            }
        });*/

        $('body').on('click', '.edit-item-group1', function () {
            var tr = $(this).parents('tr'),
                group_id = tr.attr('data-group-row-id');
            tr.find('.group_name_plain_text').toggleClass('hide');
            tr.find('.group_edit').toggleClass('hide');
            tr.find('.group_edit input').val(tr.find('.group_name_plain_text').text());
        });

        $('body').on('click', '.update-line-item-category', function () {
            var tr = $(this).parents('tr');
            var product_group_id = tr.attr('id');
            var name = tr.find('.group_edit input').val();
            if (name != '') {
                $.post(admin_url + 'invoice_items/update_line_item_category/' + product_group_id, {name: name}).done(function () {
                    window.location.href = admin_url + 'invoice_items/view_line_item_category';
                });
            }
        });

        $('#new-line-item-sub-category-insert').on('click', function () {
            var name = $('#name').val();
            var parent_id = $("#parent_id option:selected").val();

            if (name != '') {
                var postData = {
                    'name': name,
                    'parent_id': parent_id
                };
                $.post(admin_url + 'invoice_items/update_line_item_sub_category', postData).done(function () {
                    window.location.href = admin_url + 'invoice_items/view_line_item_category?line_item_sub_category=true';
                });
            }
        });

        $('body').on('click', '.edit-line-item-group', function () {
            var tr = $(this).parents('tr'),
                group_id = tr.attr('data-group-row-id');
            tr.find('.line_item_name_plain_text').toggleClass('hide');
            tr.find('.group_edit').toggleClass('hide');
            tr.find('.group_edit input').val(tr.find('.line_item_name_plain_text').text());
        });

        $('body').on('click', '.update-line-item-sub-category', function () {
            var tr = $(this).parents('tr');
            var product_group_id = tr.attr('data-group-row-id');
            var name = tr.find('.group_edit input').val();
            if (name != '') {
                $.post(admin_url + 'invoice_items/update_line_item_sub_category/' + product_group_id, {name: name}).done(function () {
                    window.location.href = admin_url + 'invoice_items/view_line_item_category?line_item_sub_category=true';
                });
            }
        });
    });

</script>
</body>
</html>
