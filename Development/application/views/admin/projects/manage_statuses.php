<?php init_head(); ?>
<div id="wrapper">
    <div class="content projects-manage-statues-page">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span>Project Status</span>
                </div>

                <h1 class="pageTitleH1"><i class="fa fa-certificate"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (has_permission('lists', '', 'create')) { ?>
                                <a href="#" onclick="new_project_status(); return false;"
                                   class="btn btn-info pull-left display-block">
                                    <?php echo _l('projects_new_status'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>


                        <table class="table sdtheme dt-table scroll-responsive">
                            <thead>
                            <th class="wd"><?php echo _l('projects_status_add_edit_order'); ?></th>
                            <th><?php echo _l('projects_status_table_name'); ?></th>
                            <th><?php echo _l('projects_table_total'); ?></th>
                            <th><?php echo _l('projects_status_color'); ?></th>
                            <th><?php echo _l(''); ?></th>
                            </thead>
                            <tbody>
                            <?php if (count($statuses) > 0) { ?>
                                <?php foreach ($statuses as $status) { ?>
                                    <tr>
                                        <td><?php echo $status['statusorder']; ?></td>
                                        <td><a href="#" data-color="<?php echo $status['color']; ?>"
                                               data-name="<?php echo $status['name']; ?>"
                                               data-order="<?php echo $status['statusorder']; ?>"><?php echo $status['name']; ?></a><br/>
                                        </td>
                                        <td><span class="text-muted">
											<?php echo total_rows('tblprojects', ' deleted=0 and brandid=' . get_user_session() . ' and status= ' . $status['id'] . ' and parent = 0'); ?></span>
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
                                                                   onclick="edit_project_status(this,<?php echo $status['id']; ?>);return false;"
                                                                   data-color="<?php echo $status['color']; ?>"
                                                                   data-name="<?php echo $status['name']; ?>"
                                                                   data-order="<?php echo $status['statusorder']; ?>"
                                                                   class=""><i
                                                                            class="fa fa-pencil-square-o"></i>Edit</a>
                                                            </li>
                                                        <?php } ?>
                                                        <?php if ($status['isdefault'] != 1) { ?>
                                                            <?php if (has_permission('lists', '', 'delete')) { ?>
                                                                <li>
                                                                    <a href="<?php echo admin_url('projects/delete_status/' . $status['id']); ?>"
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
                            <?php } else { ?>
                                <tr>
                                    <td colspan="6" align="center"><?php echo _l('projects_statuses_not_found'); ?></td>
                                </tr>
                                <!--<p class="no-margin"><?php /*echo _l('projects_statuses_not_found'); */ ?></p>-->
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once(APPPATH . 'views/admin/projects/status.php'); ?>
<?php init_tail(); ?>
</body>
</html>
