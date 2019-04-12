<?php init_head(); ?>
<div id="wrapper">
    <div class="content leads-manage-sources-page">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span>Lead Source</span>
                </div>
                <h1 class="pageTitleH1"><i class="fa fa-anchor"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">

                        <div class="_buttons">
                            <?php if (has_permission('lists', '', 'create')) { ?>
                                <a href="#" onclick="new_source(); return false;"
                                   class="btn btn-info pull-left display-block"><?php echo _l('lead_new_source'); ?></a>
                            <?php } ?>

                        </div>
                        <div class="clearfix"></div>
                        <?php if (count($sources) > 0) { ?>
                            <table class="table sdtheme dt-table scroll-responsive">
                                <thead>
                                <th><?php echo _l('leads_sources_table_name'); ?></th>
                                <th><?php echo _l('leads_table_total'); ?></th>
                                <th><?php //echo _l('options'); ?></th>
                                </thead>
                                <tbody>
                                <?php foreach ($sources as $source) { ?>
                                    <tr>
                                        <td><a href="#"
                                               data-name="<?php echo $source['name']; ?>"><?php echo $source['name']; ?></a>
                                        </td>
                                        <td>
                                    <span class="text-muted">
                                        <?php echo total_rows('tblleads', array('source' => $source['id'])); ?>
                                    </span>
                                        </td>
                                        <td>
                                            <?php if (has_permission('lists', '', 'edit') || has_permission('lists', '', 'delete')) { ?>
                                                <div class="text-right mright10">
                                                    <a class='show_act' href='javascript:void(0)'><i
                                                                class='fa fa-ellipsis-v' aria-hidden='true'></i></a>
                                                </div>
                                                <div class='table_actions'>
                                                    <ul>
                                                        <?php if (has_permission('lists', '', 'edit')) { ?>
                                                            <li><a href="#"
                                                                   onclick="edit_source(this,<?php echo $source['id']; ?>); return false"
                                                                   data-name="<?php echo $source['name']; ?>"
                                                                   class=""><i
                                                                            class="fa fa-pencil-square-o"></i>Edit</a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if (has_permission('lists', '', 'delete')) { ?>
                                                            <li>
                                                                <a href="<?php echo admin_url('leads/delete_source/' . $source['id']); ?>"
                                                                   class=" _delete"><i
                                                                            class="fa fa-remove"></i>Delete</a>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <p class="no-margin"><?php echo _l('leads_sources_not_found'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="source" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('leads/source'), array('class' => 'leadsources-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_source'); ?></span>
                    <span class="add-title"><?php echo _l('lead_new_source'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('name', 'leads_source_add_edit_name'); ?>
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
        _validate_form($('.leadsources-form'), {
            name: {
                required: true,
                remote: {
                    url: admin_url + "leads/leadsource_name_exists",
                    type: 'post',
                    data: {
                        id: function () {
                            return $('input[name="id"]').val();
                        }
                    }
                }
            }
        }, manage_leads_sources);
        $('#source').on('hidden.bs.modal', function (event) {
            $('#additional').html('');
            $('#source input').val('');
            $('.add-title').removeClass('hide');
            $('.edit-title').removeClass('hide');
            $('.form-group').removeClass('has-error');
            $('.form-group').find('p.text-danger').remove();
        });
    });

    function manage_leads_sources(form) {
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

    function new_source() {
        $('#source').modal('show');
        $('.edit-title').addClass('hide');
    }

    function edit_source(invoker, id) {
        var name = $(invoker).data('name');
        $('#additional').append(hidden_input('id', id));
        $('#source input[name="name"]').val(name);
        $('#source').modal('show');
        $('.add-title').addClass('hide');
    }
</script>
</body>
</html>
