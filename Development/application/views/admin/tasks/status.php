    <div class="modal fade" id="status" tabindex="-1" role="dialog">
        <div class="modal-dialog">
        <?php echo form_open(admin_url('tasks/status'),array('id'=>'tasks-status-form')); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <span class="edit-title"><?php echo _l('edit_task_status'); ?></span>
                        <span class="add-title"><?php echo _l('task_new_status'); ?></span>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="additional"></div>
                            <?php echo render_input('name','tasks_status_add_edit_name'); ?>
                            <?php echo render_color_picker('color',_l('tasks_status_color')); ?>
                            <?php echo render_input('statusorder','tasks_status_add_edit_order','','number'); //total_rows('tbltasksstatus', ' deleted=0 and brandid=' . get_user_session()) + 1,
                            ?>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('cancel'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
            </div><!-- /.modal-content -->
            <?php echo form_close(); ?>
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
