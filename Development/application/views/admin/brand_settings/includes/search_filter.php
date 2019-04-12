<?php
/**
 * Added By : Sanjay
 * Dt : 01/05/2018
 * For Global Search - Search filter tags settings
 */
?>
<div class="row">
    <div class="col-md-12 search_filters">
        <div id="all_tags_section">
            <div class="col-md-12">
                <div class="form-group">
                    <?php
                    $all_tags =  get_brand_option('filter_tags');
                    $exp_val = explode(',', $all_tags);
                    ?>
                    <div class="col-md-3">
                        <div class="checkbox">
                            <input type="checkbox" class="filter_group" name="settings[filter_tags][]" id="leads" <?php if(in_array('leads', $exp_val)){echo 'checked';}; ?> value="leads">
                            <label for="leads"><?php echo _l('settings_general_search_filter_tag_leads_label'); ?></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="checkbox">
                            <input type="checkbox" class="filter_group" name="settings[filter_tags][]" id="projects" <?php if(in_array('projects', $exp_val)){echo 'checked';}; ?> value="projects">
                            <label for="projects"><?php echo _l('settings_general_search_filter_tag_projects_label'); ?></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="checkbox">
                            <input type="checkbox" class="filter_group" name="settings[filter_tags][]" id="tasks" <?php if(in_array('tasks', $exp_val)){echo 'checked';}; ?> value="tasks">
                            <label for="tasks"><?php echo _l('settings_general_search_filter_tag_task_label'); ?></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="checkbox">
                            <input type="checkbox" class="filter_group" name="settings[filter_tags][]" id="files" <?php if(in_array('files', $exp_val)){echo 'checked';}; ?> value="files">
                            <label for="files"><?php echo _l('settings_general_search_filter_tag_files_label'); ?></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="checkbox">
                            <input type="checkbox" class="filter_group" name="settings[filter_tags][]" id="meetings" <?php if(in_array('meetings', $exp_val)){echo 'checked';}; ?> value="meetings">
                            <label for="meetings"><?php echo _l('settings_general_search_filter_tag_meetings_label'); ?></label>
                        </div>
                    </div>
                
                    <div class="col-md-3">
                        <div class="checkbox">
                            <input type="checkbox" class="filter_group" name="settings[filter_tags][]" id="addressbook" <?php if(in_array('addressbook', $exp_val)){echo 'checked';}; ?> value="addressbook">
                            <label for="addressbook"><?php echo _l('settings_general_search_filter_tag_addressbook_label'); ?></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="checkbox">
                            <input type="checkbox" class="filter_group" name="settings[filter_tags][]" id="messages" <?php if(in_array('messages', $exp_val)){echo 'checked';}; ?> value="messages">
                            <label for="messages"><?php echo _l('settings_general_search_filter_tag_messages_label'); ?></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="checkbox">
                            <input type="checkbox" class="filter_group" name="settings[filter_tags][]" id="proposals" <?php if(in_array('proposals', $exp_val)){echo 'checked';}; ?> value="proposals">
                            <label for="proposals"><?php echo _l('settings_general_search_filter_tag_proposals_label'); ?></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="checkbox">
                            <input type="checkbox" class="filter_group" name="settings[filter_tags][]" id="agreements" <?php if(in_array('agreements', $exp_val)){echo 'checked';}; ?> value="agreements">
                            <label for="agreements"><?php echo _l('settings_general_search_filter_tag_agreements_label'); ?></label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="checkbox">
                            <input type="checkbox" class="filter_group" name="settings[filter_tags][]" id="paymentschedules" <?php if(in_array('paymentschedules', $exp_val)){echo 'checked';}; ?> value="paymentschedules">
                            <label for="paymentschedules"><?php echo _l('settings_general_search_filter_tag_payment_schedules_label'); ?></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>	