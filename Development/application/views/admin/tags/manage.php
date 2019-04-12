<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span>Tags</span>
                </div>
                <h1 class="pageTitleH1"><i class="fa fa-tags"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (has_permission('lists', '', 'create')) { ?>
                                <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#tag_modal">
                                    <?php echo _l('new_tag'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <div class="clearfix"></div>
                        <?php render_datatable(array(
                            _l('tag_dt_name'),
                            _l('tag_dt_color'),
                            //_l('options')
                            _l('')
                        ), 'tags'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="tag_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('tag_edit_title'); ?></span>
                    <span class="add-title"><?php echo _l('tag_add_title'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/tags/manage', array('id' => 'tag_form')); ?>
            <?php echo form_hidden('tagid'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('name', 'tag_add_edit_name'); ?>
                        <?php echo render_color_picker('color', _l('Tag Color', 'tag_add_edit_color')); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal"><?php echo _l('cancel'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    /* TAG MANAGE FUNCTIONS */
    function manage_tag(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            response = JSON.parse(response);
            if (response.success == true) {
                $('.table-tags').DataTable().ajax.reload();
                alert_float('success', response.message);
            } else {
                if (response.message != '') {
                    alert_float('warning', response.message);
                }
            }
            $('#tag_modal').modal('hide');
        });
        return false;
    }

    $(function () {
        initDataTable('.table-tags', window.location.href, [2], [2]);
        _validate_form($('#tag_form'), {
            name: {
                required: true,
                remote: {
                    url: admin_url + "tags/tag_name_exists",
                    type: 'post',
                    data: {
                        tagid: function () {
                            return $('input[name="tagid"]').val();
                        }
                    }
                }
            },
            color: {required: true}
        }, manage_tag);

        // don't allow | charachter in tag name
        // is used for tag name and tag rate separations!
        $('#tag_modal input[name="name"]').on('change', function () {
            var val = $(this).val();
            if (val.indexOf('|') > -1) {
                val = val.replace('|', '');
                // Clean extra spaces in case this char is in the middle with space
                val = val.replace(/ +/g, ' ');
                $(this).val(val);
            }
        });

        $('#tag_modal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var id = button.data('id');
            $('#tag_modal input').val('').prop('disabled', false);
            $('#tag_modal .add-title').removeClass('hide');
            $('#tag_modal .edit-title').addClass('hide');
            if (typeof (id) !== 'undefined') {
                $('input[name="tagid"]').val(id);
                var name = $(button).parents('tr').find('td').eq(0).text();
                var color = $(button).parents('tr').find('td').eq(1).text();

                $('#tag_modal .add-title').addClass('hide');
                $('#tag_modal .edit-title').removeClass('hide');
                $('#tag_modal input[name="name"]').val(name);
                $('#tag_modal .colorpicker-input').colorpicker('setValue', color);
            }
        });

        $('#tag_modal').on('hidden.bs.modal', function () {
            $('.form-group').removeClass('has-error');
            $('.form-group').find('p.text-danger').remove();
        });
    });

    /* END TAG MANAGE FUNCTIONS */
</script>
</body>
</html>
