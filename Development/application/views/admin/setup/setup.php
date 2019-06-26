<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div id="Content">
            <div class="section-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="box" id="Overview">
                            <h2>

                                <i class="fa fa-cogs fa-fw"></i>

                                Account Setup
                            </h2>
                            <ul class="list-unstyled withIcons">
                                <?php if (is_sido_admin()) { ?>
                                    <li>
                                        <div class="setNav">
                                            <a href="<?php echo admin_url('settings'); ?>">
                                            </a>
                                            <i class="fa fa-cog fa-fw"></i>
                                            <span>Settings</span>
                                        </div>
                                    </li>
                                <?php } else { ?>
                                    <li>
                                        <div class="setNav">
                                            <a href="<?php echo admin_url('brand_settings'); ?>">
                                            </a>
                                            <i class="fa fa-cog fa-fw"></i>
                                            <span>Current Brand</span>
                                        </div>
                                    </li>
                                <?php } ?>

                                <?php //if($is_sido_admin != 1) { ?>
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('teams'); ?>">
                                        </a>
                                        <i class="fa fa-group fa-fw"></i>
                                        <span>Teams</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('roles'); ?>">
                                        </a>
                                        <i class="fa fa-tasks fa-fw"></i>
                                        <span>Roles</span>
                                    </div>
                                </li>
                                <?php //} ?>
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('staff'); ?>">
                                        </a>
                                        <i class="fa fa-group fa-fw"></i>
                                        <span>Team Members</span>
                                    </div>
                                </li>
                                <?php if ($is_sido_admin != 1) { ?>
                                    <li>
                                        <div class="setNav">
                                            <a href="<?php echo admin_url('paymentmodes'); ?>">
                                            </a>
                                            <i class="fa fa-money fa-fw"></i>
                                            <span>Offline Payment Modes</span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="setNav">
                                            <a href="<?php echo admin_url('brand_settings?group=online_payment_modes'); ?>">
                                            </a>
                                            <i class="fa fa-money fa-fw"></i>
                                            <span>Online Payment Modes</span>
                                        </div>
                                    </li>
                                <?php } ?>
                                <?php if ($is_sido_admin == 1 || $is_admin == 1) { ?>
                                    <li>
                                        <div class="setNav">
                                            <a href="<?php echo admin_url('packages'); ?>">
                                            </a>
                                            <i class="fa fa-usd fa-fw"></i>
                                            <span>Packages</span>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php if ($is_sido_admin != 1) { ?>
                            <?php if (has_permission('subscription', '', 'view')) { ?>
                                <div class="box">
                                    <h2>
                                        <i class="fa fa-retweet fa-fw"></i> Subscription
                                    </h2>
                                    <ul class="list-unstyled withIcons">
                                        <li>
                                            <div class="setNav">
                                                <a href="<?php echo admin_url('subscription'); ?>">
                                                </a>
                                                <i class="fa fa-rocket fa-fw"></i>
                                                <span>Subscription Overview</span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="setNav">
                                                <a href="<?php echo admin_url('subscription/manage_subscription'); ?>">
                                                </a>
                                                <i class="fa fa-retweet fa-fw"></i>
                                                <span>Manage Subscription</span>
                                            </div>
                                        </li>

                                    </ul>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                    <div class="col-md-4">
                        <div class="box" id="Templates">
                            <h2><i class="fa fa-files-o fa-fw"></i><?php echo _l('templates'); ?>
                            </h2>
                            <ul class="list-unstyled withIcons">
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('proposaltemplates'); ?>">
                                        </a>
                                        <i class="fa fa-file-text-o fa-fw"></i>
                                        <span>Proposals</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('agreements'); ?>">
                                        </a>
                                        <i class="fa fa-files-o fa-fw"></i>
                                        <span>Agreements</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('emails'); ?>">
                                        </a>
                                        <i class="fa fa-envelope-o fa-fw"></i>
                                        <span>Email Templates</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('paymentschedules'); ?>">
                                        </a>
                                        <i class="fa fa-calendar fa-fw"></i>
                                        <span>Payment Schedules</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('invoice_items'); ?>">
                                        </a>
                                        <i class="fa fa-money fa-fw"></i>
                                        <span>Product & Services</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('invoice_items/packages'); ?>">
                                        </a>
                                        <i class="fa fa-usd fa-fw"></i>
                                        <span>Packages</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('leadcaptureforms'); ?>">
                                        </a>
                                        <i class="fa fa-list-ul fa-fw"></i>
                                        <span>Forms</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box" id="Lists">
                            <h2><i class="fa fa-list fa-fw"></i><?php echo _l('lists') ?></h2>
                            <ul class="list-unstyled withIcons">
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('leads/sources'); ?>">
                                        </a>
                                        <i class="fa fa-anchor fa-fw"></i>
                                        <span>Lead Source</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('leads/statuses'); ?>">
                                        </a>
                                        <i class="fa fa-certificate fa-fw"></i>
                                        <span>Lead Status</span>
                                    </div>
                                </li>

                                <!--
                                   -- Added By : Vaidehi
                                   -- Dt : 12/18/2017
                                   -- for project status
                                -->
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('projects/statuses'); ?>">
                                        </a>
                                        <i class="fa fa-certificate fa-fw"></i>
                                        <span>Project Status</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('tasks/statuses'); ?>">
                                        </a>
                                        <i class="fa fa-list-alt fa-fw"></i>
                                        <span>Task Status</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('tags'); ?>">
                                        </a>
                                        <i class="fa fa-tags fa-fw"></i>
                                        <span>Tags</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('taxes'); ?>">
                                        </a>
                                        <i class="fa fa-money fa-fw"></i>
                                        <span>Taxes</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('invoice_items/view_income_category'); ?>">
                                        </a>
                                        <i class="fa fa-list-alt fa-fw"></i>
                                        <span>Income Category</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('invoice_items/view_expense_category'); ?>">
                                        </a>
                                        <i class="fa fa-list-alt fa-fw"></i>
                                        <span>Expense Category</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="setNav">
                                        <a href="<?php echo admin_url('event_types'); ?>">
                                        </a>
                                        <i class="fa fa-handshake-o fa-fw"></i>
                                        <span><?php echo _l('project_type_s'); ?></span>
                                    </div>
                                </li>
                                <?php if ($is_sido_admin == 1 || $is_admin == 1) { ?>
                                    <li>
                                        <div class="setNav">
                                            <a href="<?php echo admin_url('services'); ?>">
                                            </a>
                                            <i class="fa fa-cubes fa-fw"></i>
                                            <span>Services</span>
                                        </div>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
</body>
</html>