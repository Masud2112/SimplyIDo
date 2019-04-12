<?php init_head(); ?>
<div id="wrapper">
    <div class="content projects-manage-eventtypes-page">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span><?php echo _l('project_type'); ?></span>
                </div>
                <h1 class="pageTitleH1"><i class="fa fa-handshake-o"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php
                            $session_data = get_session_data();
                            $is_sido_admin = $session_data['is_sido_admin'];
                            if (($is_sido_admin == 0 && has_permission('lists', '', 'create')) || $is_sido_admin == 1) { ?>
                                <a href="#" onclick="new_event_type(); return false;"
                                   class="btn btn-info pull-left display-block"><?php echo _l('project_type'); ?></a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <form id="event_type" method="post" action="">
                            <table class="table">
                                <thead>
                                <th><?php echo _l('project_type'); ?></th>
                                <th><?php //echo _l('options'); ?></th>
                                </thead>
                                <tbody id="event_types">
                                <?php
                                if (count($eventtypes) > 0) { ?>
                                    <?php foreach ($eventtypes as $key => $eventtype) { ?>
                                        <tr style="width: 100% !important;">
                                            <td width="70%"><a href="#"
                                                               data-name="<?php echo $eventtype['eventtypename']; ?>"><?php echo $eventtype['eventtypename']; ?></a>
                                                <input type="hidden" name="eventtypes[<?php echo $key ?>][eventtypeid]"
                                                       value="<?php echo $eventtype['eventtypeid']; ?>">
                                                <input class="event_type_order" type="hidden"
                                                       name="eventtypes[<?php echo $key ?>][order]"
                                                       value="<?php echo $eventtype['order']; ?>">
                                            </td>
                                            <td width="30%">
                                                <?php if (has_permission('lists', '', 'edit') || has_permission('lists', '', 'delete')) { ?>
                                                    <div class="projecteventtypesAction">
                                                        <div class='text-right mright10'>
                                                            <a class='show_act' href='javascript:void(0)'><i
                                                                        class='fa fa-ellipsis-v' aria-hidden='true'></i></a>

                                                        </div>
                                                        <div class='table_actions'>
                                                            <ul>
                                                                <?php if (has_permission('lists', '', 'edit')) { ?>
                                                                    <li><a href="#"
                                                                           onclick="edit_eventtype(this,<?php echo $eventtype['eventtypeid']; ?>); return false"
                                                                           data-name="<?php echo $eventtype['eventtypename']; ?>"
                                                                           class=""><i
                                                                                    class="fa fa-pencil-square-o"></i>Edit</a>
                                                                    </li>
                                                                <?php } ?>
                                                                <?php if (has_permission('lists', '', 'delete')) { ?>
                                                                    <li>
                                                                        <a href="<?php echo admin_url('projects/delete_eventtype/' . $eventtype['eventtypeid']); ?>"
                                                                           class="_delete"><i
                                                                                    class="fa fa-remove"></i>Delete</a>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="6"
                                            align="center"><?php echo _l('project_type_s_not_found'); ?></td>
                                    </tr>
                                    <!--<p class="no-margin"><?php /*echo _l('eventtypes_not_found'); */ ?></p>-->
                                <?php } ?>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="eventtype" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('projects/eventtype'), array('id' => 'eventtype-form')); ?>
        <?php echo form_hidden('eventtypeid'); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_project_type_s'); ?></span>
                    <span class="add-title"><?php echo _l('new_project_type_s'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('eventtypename', 'project_type_s_add_edit_name'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php init_tail(); ?>
<script>
    $(function () {
        function manage_eventtype(form) {
            var data = $(form).serialize();
            var url = form.action;
            $.post(url, data).done(function (response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    window.location.reload();
                    alert_float('success', response.message);
                } else {
                    if (response.message != '') {
                        alert_float('warning', response.message);
                    }
                }
                window.location.reload();
            });
            return false;
        }

        _validate_form($('#eventtype-form'), {
                eventtypename: {
                    required: true,
                    remote: {
                        url: admin_url + "projects/eventtype_name_exists",
                        type: 'post',
                        data: {
                            eventtypeid: function () {
                                return $('input[name="eventtypeid"]').val();
                            }
                        }
                    }
                }
            },
            manage_eventtype);

        $('#eventtype').on('hidden.bs.modal', function (event) {
            $('#additional').html('');
            $('#eventtype input').val('');
            $('.add-title').removeClass('hide');
            $('.edit-title').removeClass('hide');
            $('.form-group').removeClass('has-error');
            $('.form-group').find('p.text-danger').remove();
        });

        $('#event_types').sortable({
            stop: function (event, ui) {
                var clas = ui.item.attr("id");
                count = 0;
                var option = [];
                $("#event_types tr").each(function () {
                    $('.event_type_order', this).val(count);
                    count++;
                });
                $.ajax({
                    url: admin_url + 'projects/reorderprojecttype/',
                    method: "post",
                    data: $('form#event_type').serialize(),
                    success: function (result) {
                        if (result > 0) {
                        } else {
                        }
                    }
                });
            }
        });
    });

    function new_event_type() {
        $('#eventtype').modal('show');
        $('.edit-title').addClass('hide');
    }

    function edit_eventtype(invoker, id) {
        var name = $(invoker).data('name');
        $('#additional').append(hidden_input('eventtypeid', id));
        $('#eventtype input[name="eventtypename"]').val(name);
        $('#eventtype').modal('show');
        $('.add-title').addClass('hide');
    }
</script>
</body>
</html>
