<?php init_head(); ?>
<div id="wrapper">
    <div class="content tasks-manage-statuses-page">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span>Task Status</span>
                </div>
                <h1 class="pageTitleH1"><i class="fa fa-list-alt"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (has_permission('lists', '', 'create')) { ?>
                                <a href="#" onclick="new_status(); return false;"
                                   class="btn btn-info pull-left display-block">
                                    <?php echo _l('task_new_status'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <?php if (count($statuses) > 0) { ?>
                            <table class="table sdtheme dt-table scroll-responsive">
                                <thead>
                                <th class="wd"><?php echo _l('tasks_status_add_edit_order'); ?></th>
                                <th><?php echo _l('tasks_status_table_name'); ?></th>
                                <th><?php echo _l('tasks_table_total'); ?></th>
                                <th><?php echo _l('tasks_status_color'); ?></th>
                                <th><?php echo _l(''); ?></th>
                                </thead>
                                <tbody>
                                <?php foreach ($statuses as $status) { ?>
                                    <tr>
                                        <td><?php echo $status['statusorder']; ?></td>
                                        <td><?php echo $status['name']; ?><br/>
                                            <span class="text-muted">
											<?php echo _l('tasks_table_total', total_rows('tblstafftasks', array('status' => $status['id']))); ?></span>
                                        </td>
                                        <td>
                                            <span class="text-muted"><?php echo total_rows('tblstafftasks', array('status' => $status['id'])); ?></span>
                                        </td>
                                        <td>
                                            <span style="padding:5px;background-color:<?php echo $status['color']; ?>; display: inline-block;"></span>
                                            &nbsp; <?php echo $status['color']; ?></td>
                                        <td>
                                            <?php if (has_permission('lists', '', 'edit') || has_permission('lists', '', 'delete')) { ?>
                                                <div class="text-right mright10"><a class='show_act'
                                                                                    href='javascript:void(0)'><i
                                                                class='fa fa-ellipsis-v' aria-hidden='true'></i></a>
                                                </div>
                                                <div class='table_actions'>
                                                    <ul>
                                                        <?php if (has_permission('lists', '', 'edit')) { ?>
                                                            <li><a href="#"
                                                                   onclick="edit_status(this,<?php echo $status['id']; ?>);return false;"
                                                                   data-color="<?php echo $status['color']; ?>"
                                                                   data-name="<?php echo $status['name']; ?>"
                                                                   data-order="<?php echo $status['statusorder']; ?>"
                                                                   class=""><i
                                                                            class="fa fa-pencil-square-o"></i>Edit</a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if ($status['name'] != "Pending" && $status['name'] != "Completed" && $status['name'] != "Past Due") { ?>
                                                            <?php if (has_permission('lists', '', 'delete')) { ?>
                                                                <li>
                                                                    <a href="<?php echo admin_url('tasks/delete_status/' . $status['id']); ?>"
                                                                       class="_delete"><i
                                                                                class="fa fa-remove"></i>Delete</a></li>
                                                            <?php } ?>
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
                            <p class="no-margin"><?php echo _l('task_statuses_not_found'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once(APPPATH . 'views/admin/tasks/status.php'); ?>
<?php init_tail(); ?>
</body>
</html>