<?php init_head(); ?>
<div id="wrapper">
<div class="content email-templates">
<h1 class="pageTitleH1"><i class="fa fa-envelope-o "></i><?php echo _l('email_templates'); ?></h1>              
            
                <div class="breadcrumb pull-right">
                    <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <a href="<?php echo admin_url('setup'); ?>">Settings</a>
                    <i class="fa fa-angle-right breadcrumb-arrow"></i>
                    <span>Email Templates</span>
                </div>
            <div class="clearfix"></div>
			<div class="row">
        <div class="col-md-12">            
            <div class="panel_s btmbrd">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- <h4 class="bold well email-template-heading"><?php //echo _l('email_template_ticket_fields_heading'); ?></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php //echo _l('email_templates_table_heading_name'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php //foreach($tickets as $ticket_template){ ?>
                                        <tr>
                                            <td class="<?php //if($ticket_template['active'] == 0){echo 'text-throught';} ?>">
                                                <a href="<?php //echo admin_url('emails/email_template/'.$ticket_template['emailtemplateid']); ?>"><?php //echo $ticket_template['name']; ?></a>
                                                <?php //if(ENVIRONMENT !== 'production'){ ?>
                                                    <br/><small><?php //echo $ticket_template['slug']; ?></small>
                                                <?php //} ?>
                                            </td>
                                        </tr>
                                        <?php //} ?>
                                    </tbody>
                                </table>
                            </div> -->
                        </div>
                        <div class="col-md-12">
                            <h4 class="bold well email-template-heading"><?php echo _l('meetings'); ?></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('email_templates_table_heading_name'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($meetings as $meetings_template){ ?>
                                            <?php //if($meetings_template['active'] == 1){ ?>
                                                <tr>
                                                    <td class="<?php if($meetings_template['active'] == 0){echo 'text-throught';} ?>">
                                                        <a href="<?php echo admin_url('emails/email_template/'.$meetings_template['emailtemplateid']); ?>"><?php echo $meetings_template['name']; ?></a>
                                                        <?php //if(ENVIRONMENT !== 'production'){ ?>
                                                        <?php if(ENVIRONMENT === 'development') { ?>
                                                            <br/><small><?php echo $meetings_template['slug']; ?></small>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php //} ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="display: none;">
                            <h4 class="bold well email-template-heading"><?php echo _l('estimates'); ?></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('email_templates_table_heading_name'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($estimate as $estimate_template){ ?>
                                            <?php //if($estimate_template['active'] == 1){ ?>
                                                <tr>
                                                    <td class="<?php if($estimate_template['active'] == 0){echo 'text-throught';} ?>">
                                                        <a href="<?php echo admin_url('emails/email_template/'.$estimate_template['emailtemplateid']); ?>"><?php echo $estimate_template['name']; ?></a>
                                                        <?php //if(ENVIRONMENT !== 'production'){ ?>
                                                        <?php if(ENVIRONMENT === 'development') { ?>
                                                            <br/><small><?php echo $estimate_template['slug']; ?></small>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php //} ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12" style="display: none;">
                            <h4 class="bold well email-template-heading"><?php echo _l('email_template_contracts_fields_heading'); ?></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('email_templates_table_heading_name'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($agreements as $agreement_template){ ?>
                                            <?php //if($agreement_template['active'] == 1){ ?>
                                                <tr>
                                                    <td class="<?php if($agreement_template['active'] == 0){echo 'text-throught';} ?>">
                                                        <a href="<?php echo admin_url('emails/email_template/'.$agreement_template['emailtemplateid']); ?>"><?php echo $agreement_template['name']; ?></a>
                                                        <?php //if(ENVIRONMENT !== 'production'){ ?>
                                                        <?php if(ENVIRONMENT === 'development') { ?>
                                                            <br/><small><?php echo $agreement_template['slug']; ?></small>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php //} ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <h4 class="bold well email-template-heading"><?php echo _l('email_template_invoices_fields_heading'); ?></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('email_templates_table_heading_name'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($invoice as $invoice_template){ ?>
                                            <?php //if($invoice_template['active'] == 1){ ?>
                                                <tr>
                                                    <td class="<?php if($invoice_template['active'] == 0){echo 'text-throught';} ?>">
                                                        <a href="<?php echo admin_url('emails/email_template/'.$invoice_template['emailtemplateid']); ?>"><?php echo $invoice_template['name']; ?></a>
                                                        <?php //if(ENVIRONMENT !== 'production'){ ?>
                                                        <?php if(ENVIRONMENT === 'development') { ?>
                                                            <br/><small><?php echo $invoice_template['slug']; ?></small>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php //} ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <h4 class="bold well email-template-heading"><?php echo _l('tasks'); ?></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('email_templates_table_heading_name'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($tasks as $task_template){ ?>
                                            <?php //if($task_template['active'] == 1){ ?>
                                                <tr>
                                                    <td class="<?php if($task_template['active'] == 0){echo 'text-throught';} ?>">
                                                        <a href="<?php echo admin_url('emails/email_template/'.$task_template['emailtemplateid']); ?>"><?php echo $task_template['name']; ?></a>
                                                        <?php //if(ENVIRONMENT !== 'production'){ ?>
                                                        <?php if(ENVIRONMENT === 'development') { ?>
                                                            <br/><small><?php echo $task_template['slug']; ?></small>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php //} ?> 
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <h4 class="bold well email-template-heading"><?php echo _l('email_template_clients_fields_heading'); ?></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('email_templates_table_heading_name'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($client as $client_template){ ?>
                                            <?php //if($client_template['active'] == 1){ ?>
                                                <tr>
                                                    <td class="<?php if($client_template['active'] == 0){echo 'text-throught';} ?>">
                                                        <a href="<?php echo admin_url('emails/email_template/'.$client_template['emailtemplateid']); ?>"><?php echo $client_template['name']; ?></a>
                                                        <?php //if(ENVIRONMENT !== 'production'){ ?>
                                                        <?php if(ENVIRONMENT === 'development') { ?>
                                                            <br/><small><?php echo $client_template['slug']; ?></small>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php //} ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <h4 class="bold well email-template-heading"><?php echo _l('email_template_proposals_fields_heading'); ?></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('email_templates_table_heading_name'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($proposals as $proposal_template){ ?>
                                            <?php //if($proposal_template['active'] == 1){ ?>
                                                <tr>
                                                    <td class="<?php if($proposal_template['active'] == 0){echo 'text-throught';} ?>">
                                                        <a href="<?php echo admin_url('emails/email_template/'.$proposal_template['emailtemplateid']); ?>"><?php echo $proposal_template['name']; ?></a>
                                                        <?php //if(ENVIRONMENT !== 'production'){ ?>
                                                        <?php if(ENVIRONMENT === 'development') { ?>
                                                            <br/><small><?php echo $proposal_template['slug']; ?></small>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php //} ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12" style="display: none;">
                            <h4 class="bold well email-template-heading"><?php echo _l('projects'); ?></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('email_templates_table_heading_name'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($projects as $project_template){ ?>
                                            <?php //if($project_template['active'] == 1){ ?>
                                                <tr>
                                                    <td class="<?php if($project_template['active'] == 0){echo 'text-throught';} ?>">
                                                        <a href="<?php echo admin_url('emails/email_template/'.$project_template['emailtemplateid']); ?>"><?php echo $project_template['name']; ?></a>
                                                        <?php //if(ENVIRONMENT !== 'production'){ ?>
                                                        <?php if(ENVIRONMENT === 'development') { ?>
                                                                <br/><small><?php echo $project_template['slug']; ?></small>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                            <?php //} ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <h4 class="bold well email-template-heading"><?php echo _l('staff_members'); ?></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('email_templates_table_heading_name'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($staff as $staff_template){ ?>
                                            <?php //if($staff_template['active'] == 1){ ?>
                                                <tr>
                                                    <td class="<?php if($staff_template['active'] == 0){echo 'text-throught';} ?>">
                                                        <a href="<?php echo admin_url('emails/email_template/'.$staff_template['emailtemplateid']); ?>"><?php echo $staff_template['name']; ?></a>
                                                        <?php //if(ENVIRONMENT !== 'production'){ ?>
                                                        <?php if(ENVIRONMENT === 'development') { ?>
                                                            <br/><small><?php echo $staff_template['slug']; ?></small>
                                                        <?php } ?>                               
                                                    </td>
                                                </tr>
                                            <?php //} ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <h4 class="bold well email-template-heading"><?php echo _l('leads'); ?></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('email_templates_table_heading_name'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($leads as $lead_template){ ?>
                                            <?php //if($lead_template['active'] == 1){ ?>
                                                <tr>
                                                    <td class="<?php if($lead_template['active'] == 0){echo 'text-throught';} ?>">
                                                        <a href="<?php echo admin_url('emails/email_template/'.$lead_template['emailtemplateid']); ?>"><?php echo $lead_template['name']; ?></a>
                                                        <?php //if(ENVIRONMENT !== 'production'){ ?>
                                                        <?php if(ENVIRONMENT === 'development') { ?>
                                                            <br/><small><?php echo $lead_template['slug']; ?></small>
                                                        <?php } ?> 
                                                    </td>
                                                </tr>
                                            <?php //} ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <h4 class="bold well email-template-heading"><?php echo _l('messages'); ?></h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('email_templates_table_heading_name'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($messages as $message_template){ ?>
                                            <?php //if($message_template['active'] == 1){ ?>
                                                <tr>
                                                    <td class="<?php if($message_template['active'] == 0){echo 'text-throught';} ?>">
                                                        <a href="<?php echo admin_url('emails/email_template/'.$message_template['emailtemplateid']); ?>"><?php echo $message_template['name']; ?></a>
                                                        <?php //if(ENVIRONMENT !== 'production'){ ?>
                                                        <?php if(ENVIRONMENT === 'development') { ?>
                                                            <br/><small><?php echo $message_template['slug']; ?></small>
                                                        <?php } ?> 
                                                    </td>
                                                </tr>
                                            <?php //} ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="clearfix"></div>
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
