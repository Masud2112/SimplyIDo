<?php init_head(); ?>
<div id="wrapper">
    <div class="content leads-manage-statues-page">
        <div class="row">
            <div class="col-md-12">
                <div class="breadcrumb">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span>Leads Status</span>
                </div>

                <h1 class="pageTitleH1"><i class="fa fa-certificate"></i><?php echo $title; ?></h1>
                <div class="clearfix"></div>
                <div class="panel_s btmbrd">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (has_permission('lists', '', 'create')) { ?>
                                <a href="#" onclick="new_status(); return false;"
                                   class="btn btn-info pull-left display-block">
                                    <?php echo _l('lead_new_status'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <?php if (count($statuses) > 0) { ?>
                            <table class="table sdtheme dt-table scroll-responsive">
                                <thead>
                                <th class="wd"><?php echo _l('leads_status_add_edit_order'); ?></th>
                                <th><?php echo _l('leads_status_table_name'); ?></th>
                                <th><?php echo _l('leads_table_total'); ?></th>
                                <th><?php echo _l('leads_status_color'); ?></th>
                                <th><?php echo _l(''); ?></th>
                                </thead>
                                <tbody>
                                <?php foreach ($statuses as $status) { ?>
                                    <tr>
                                        <td><?php echo $status['statusorder']; ?></td>
                                        <td><a href="#" data-color="<?php echo $status['color']; ?>"
                                               data-name="<?php echo $status['name']; ?>"
                                               data-order="<?php echo $status['statusorder']; ?>"><?php echo $status['name']; ?></a><br/>
                                        </td>
                                        <td><span class="text-muted">
											<?php echo total_rows('tblleads', array('status' => $status['id'])); ?></span>
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
                                                        <?php if (has_permission('lists', '', 'delete')) {
                                                            if ($status['isdeleteable'] == 1) {
                                                                ?>
                                                                <li>
                                                                    <a href="<?php echo admin_url('leads/delete_status/' . $status['id']); ?>"
                                                                       class="_delete"><i class="fa fa-remove"></i>Delete</a>
                                                                </li>

                                                            <?php }
                                                        } ?>
                                                    </ul>
                                                </div>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <p class="no-margin"><?php echo _l('lead_statuses_not_found'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once(APPPATH . 'views/admin/leads/status.php'); ?>
<?php init_tail(); ?>
</body>
</html>
