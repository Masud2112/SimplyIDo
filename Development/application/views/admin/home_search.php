<?php init_head(); ?>

<div id="wrapper">
  <div class="content manage-search-page">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
           
            <?php //$brandid = get_user_session(); echo $brandid; ?>

            <?php
                  $searchword_new = isset($searchword) ? $searchword : "";
            	$total_lead_count = isset($lead_search_result) ? count($lead_search_result) : 0;
            	$total_project_count = isset($project_search_result) ? count($project_search_result) : 0;
            	$total_tasks_count = isset($tasks_search_result) ? count($tasks_search_result) : 0;
            	$total_files_count = isset($files_search_result) ? count($files_search_result) : 0;
            	$total_meetings_count = isset($meetings_search_result) ? count($meetings_search_result) : 0;
            	$total_address_count = isset($addressbook_search_result) ? count($addressbook_search_result) : 0;
            	$total_messages_count = isset($messages_search_result) ? count($messages_search_result) : 0;
            	$total_proposals_count = isset($proposals_search_result) ? count($proposals_search_result) : 0;
            	$total_agreements_count = isset($agreements_search_result) ? count($agreements_search_result) : 0;
            	$total_paymentschedule_count = isset($paymentschedules_search_result) ? count($paymentschedules_search_result) : 0;
            	
            	$total_count = $total_lead_count + $total_project_count + $total_tasks_count + $total_files_count + $total_meetings_count + $total_address_count + $total_messages_count + $total_proposals_count + $total_agreements_count + $total_paymentschedule_count ;
            ?>

            <h3 class="page-title"><?php echo _l('home_search_text_title'); ?> "<b><?php echo $searchword_new; ?></b>" (<?php echo _l('home_search_total_text_title'); ?> <?php echo $total_count; ?> <?php echo _l('home_search_result_text_title'); ?>)</h3>
<hr class="hr-panel-heading">
			<?php if(!empty($lead_search_result)) { ?>      		
      		<div class="lead_search_section col-md-12 mbot20">
      			<?php if(isset($lead_search_result)) { ?>
      			<h4><?php echo _l('settings_general_search_filter_tag_leads_label'); ?> (<?php echo count($lead_search_result); ?>)</h4>
      			<?php foreach ($lead_search_result as $lead_data) { ?>
	      			<div class="label-tag single_lead">
	      				<a href="<?php echo base_url()."admin/leads/dashboard/".$lead_data->id; ?>"><?php echo $lead_data->name; ?></a>
	      			</div>
	      		<?php } }?>
      		</div>
      		<hr class="hr-panel-heading">
      		<?php } ?>


      		<?php if(!empty($project_search_result)) { ?> 
      		<div class="project_search_section col-md-12 mbot20">
      			<?php if(isset($project_search_result)) { ?>
      			<h4><?php echo _l('settings_general_search_filter_tag_projects_label'); ?> (<?php echo count($project_search_result); ?>)</h4>
      			<?php foreach ($project_search_result as $project_data) { ?>
	      			<div class="label-tag single_project">
	      				<a href="<?php echo base_url()."admin/projects/dashboard/".$project_data->id; ?>"><?php echo $project_data->name; ?></a>
	      			</div>
	      		<?php } }?>
      		</div>
      		<hr class="hr-panel-heading">
      		<?php } ?>

      		
      		<?php if(!empty($tasks_search_result)) { ?> 
      		<div class="tasks_search_section col-md-12 mbot20">
      			<?php if(isset($tasks_search_result)) { ?>
      			<h4><?php echo _l('settings_general_search_filter_tag_task_label'); ?> (<?php echo count($tasks_search_result); ?>)</h4>
      			<?php foreach ($tasks_search_result as $tasks_data) { ?>
	      			<div class="label-tag single_task">
	      				<a href="<?php echo base_url()."admin/tasks/dashboard/".$tasks_data->id; ?>"><?php echo $tasks_data->name; ?></a>
	      			</div>
	      		<?php } }?>
      		</div>
      		<hr class="hr-panel-heading">
      		<?php } ?>

      		<?php if(!empty($files_search_result)) { ?> 
      		<div class="files_search_section col-md-12 mbot20">
      			<?php if(isset($files_search_result)) { ?>
      			<h4><?php echo _l('settings_general_search_filter_tag_files_label'); ?> (<?php echo count($files_search_result); ?>)</h4>
      			<?php foreach ($files_search_result as $file_data) { ?>
	      			<div class="label-tag single_file">
	      				<a href="<?php echo base_url()."admin/files"; ?>"><?php echo $file_data->file_name; ?></a>
	      			</div>
	      		<?php } }?>
      		</div>
      		<hr class="hr-panel-heading">
      		<?php } ?>


      		<?php if(!empty($meetings_search_result)) { ?> 
      		<div class="meeting_search_section col-md-12 mbot20">
      			<?php if(isset($meetings_search_result)) { ?>
      			<h4><?php echo _l('settings_general_search_filter_tag_meetings_label'); ?> (<?php echo count($meetings_search_result); ?>)</h4>
      			<?php foreach ($meetings_search_result as $meeting_data) { ?>
	      			<div class="label-tag single_meeting">
	      				<a href="<?php echo base_url()."admin/meetings/meeting/".$meeting_data->meetingid; ?>"><?php echo $meeting_data->name; ?></a>
	      			</div>
	      		<?php } }?>
      		</div>
      		<hr class="hr-panel-heading">
      		<?php } ?>


      		<?php if(!empty($addressbook_search_result)) { ?>
      		<div class="addressbook_search_section col-md-12 mbot20">
      			<?php if(isset($addressbook_search_result)) { ?>
      			<h4><?php echo _l('settings_general_search_filter_tag_addressbook_label'); ?> (<?php echo count($addressbook_search_result); ?>)</h4>
      			<?php foreach ($addressbook_search_result as $address_data) { ?>
                           <?php if($address_data->firstname != ""){ ?>
	      			<div class="label-tag single_meeting">
	      				<a href="<?php echo base_url()."admin/addressbooks/view/".$address_data->addressbookid; ?>"><?php echo $address_data->firstname." ".$address_data->lastname; ?> <?php if($address_data->emailaddress != ""){ ?> (<?php echo $address_data->emailaddress; ?>)<?php } ?></a>
	      			</div>
                           <?php } ?>
	      		<?php } }?>
      		</div>
      		<hr class="hr-panel-heading">
      		<?php } ?>


      		<?php if(!empty($messages_search_result)) { ?>
      		<div class="message_search_section col-md-12 mbot20">
      			<?php if(isset($messages_search_result)) { ?>
      			<h4><?php echo _l('settings_general_search_filter_tag_messages_label'); ?> (<?php echo count($messages_search_result); ?>)</h4>
      			<?php foreach ($messages_search_result as $message_data) { ?>
	      			<div class="label-tag single_meeting">
	      				<a href="<?php echo base_url()."admin/messages/view/".$message_data->id; ?>"><?php echo $message_data->subject; ?></a>
	      			</div>
	      		<?php } }?>
      		</div>
      		<hr class="hr-panel-heading">
      		<?php } ?>


      		<?php if(!empty($proposals_search_result)) { ?>
      		<div class="proposals_search_section col-md-12 mbot20">
      			<?php if(isset($proposals_search_result)) { ?>
      			<h4><?php echo _l('settings_general_search_filter_tag_proposals_label'); ?> (<?php echo count($proposals_search_result); ?>)</h4>
      			<?php foreach ($proposals_search_result as $proposal_data) { ?>
	      			<div class="label-tag single_meeting">
	      				<a href="<?php echo base_url()."admin/proposaltemplates/proposal/".$proposal_data->templateid; ?>"><?php echo $proposal_data->name; ?></a>
	      			</div>
	      		<?php } }?>
      		</div>
      		<hr class="hr-panel-heading">
      		<?php } ?>


      		<?php if(!empty($agreements_search_result)) { ?>
      		<div class="agreements_search_section col-md-12 mbot20">
      			<?php if(isset($agreements_search_result)) { ?>
      			<h4><?php echo _l('settings_general_search_filter_tag_agreements_label'); ?> (<?php echo count($agreements_search_result); ?>)</h4>
      			<?php foreach ($agreements_search_result as $agreement_data) { ?>
	      			<div class="label-tag single_meeting">
	      				<a href="<?php echo base_url()."admin/agreements/agreement/".$agreement_data->templateid; ?>"><?php echo $agreement_data->name; ?></a>
	      			</div>
	      		<?php } }?>
      		</div>
      		<hr class="hr-panel-heading">
      		<?php } ?>


      		<?php if(!empty($paymentschedules_search_result)) { ?>
      		<div class="payment_search_section col-md-12 mbot20">
      			<?php if(isset($paymentschedules_search_result)) { ?>
      			<h4><?php echo _l('settings_general_search_filter_tag_payment_schedules_label'); ?> (<?php echo count($paymentschedules_search_result); ?>)</h4>
      			<?php foreach ($paymentschedules_search_result as $payment_data) { ?>
	      			<div class="label-tag single_meeting">
	      				<a href="<?php echo base_url()."admin/paymentschedules/paymentschedule/".$payment_data->templateid; ?>"><?php echo $payment_data->name; ?></a>
	      			</div>
	      		<?php } }?>
      		</div>
      		<?php } ?>

                  
        </div>
      </div>
    </div>
  </div>
</div>
</div>


<?php init_tail(); ?>
</body>
</html>
