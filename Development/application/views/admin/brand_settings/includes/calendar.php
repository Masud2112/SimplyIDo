<?php
/**
* Added By : Vaidehi
* Dt : 10/12/2017
* For Brand Settings Module
*/
?>


<div class="tab-content mtop30">
  <div role="tabpanel" class="tab-pane active" id="general">   

      <div class="bshead">
        <h4><?php echo _l('settings_group_general'); ?> setting</h4>
      </div>
      <div class="bsBody">
        <div class="col-md-6">
          <div class="form-group">
            <label for="settings[default_view_calendar]"><?php echo _l('default_view'); ?></label>
            <br />
            <select class="selectpicker" data-width="100%" name="settings[default_view_calendar]" id="default_view_calendar">
              <option value="month"<?php if(get_brand_option('default_view_calendar') == 'month'){echo ' selected';} ?>><?php echo _l('month'); ?></option>
              <option value="basicWeek""<?php if(get_brand_option('default_view_calendar') == 'basicWeek'){echo ' selected';} ?>><?php echo _l('week'); ?></option>
              <option value="basicDay""<?php if(get_brand_option('default_view_calendar') == 'basicDay'){echo ' selected';} ?>><?php echo _l('day'); ?></option>
              <option value="agendaWeek""<?php if(get_brand_option('default_view_calendar') == 'agendaWeek'){echo ' selected';} ?>><?php echo _l('agenda'); ?> <?php echo _l('week'); ?></option>
              <option value="agendaDay""<?php if(get_brand_option('default_view_calendar') == 'agendaDay'){echo ' selected';} ?>><?php echo _l('agenda'); ?> <?php echo _l('day'); ?></option>
            </select>
          </div>
        </div>
        <div class="col-md-6">
          <label><?php echo _l('calendar_first_day'); ?></label>
          <select name="settings[calendar_first_day]" class="selectpicker" data-width="100%">
            <?php
            $weekdays = get_weekdays();
            end($weekdays);
            $last = key($weekdays);
            foreach($weekdays as $key=>$val){
              if($key == $last){
                $key = 0;
              } else {
                $key = $key + 1;
              }
              ?>
              <option value="<?php echo $key; ?>" <?php if(get_brand_option('calendar_first_day') == $key){echo 'selected';} ?>><?php echo $val; ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
      <div class="clearfix"></div>

      <div class="bshead">
        <h4><?php echo _l('show_on_calendar'); ?></h4>
      </div>
      <div class="bsBody">
        <div class="row">
            <div class="col-md-4">
              <div class="calSet_blk">
                <div class="calaCond"><?php render_yes_no_option('show_invoices_on_calendar','show_invoices_on_calendar'); ?></div>
                <div class="calaColor">
                <?php echo render_color_picker('settings[calendar_invoice_color]',_l('settings_calendar_color',_l('invoice')),get_brand_option('calendar_invoice_color')); ?></div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="calSet_blk">
                <div class="calaCond"><?php render_yes_no_option('show_proposals_on_calendar','show_proposals_on_calendar'); ?></div>
                <div class="calaColor">
                <?php echo render_color_picker('settings[calendar_proposal_color]',_l('settings_calendar_color',_l('proposal')),get_brand_option('calendar_proposal_color')); ?></div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="calSet_blk">
                <div class="calaCond"><?php render_yes_no_option('show_projects_on_calendar','show_projects_on_calendar'); ?>  </div>
                <div class="calaColor">
                <?php echo render_color_picker('settings[calendar_project_color]',_l('settings_calendar_color',_l('project')),get_brand_option('calendar_project_color')); ?></div>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-4">
              <div class="calSet_blk">
                <div class="calaCond"><?php render_yes_no_option('show_tasks_on_calendar','show_tasks_on_calendar'); ?></div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="calSet_blk">
                <div class="calaCond"><?php render_yes_no_option('show_meetings_on_calendar','show_meetings_on_calendar'); ?></div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="calSet_blk">
                <div class="calaCond"><?php render_yes_no_option('show_lead_on_calendar','show_lead_on_calendar'); ?></div>
              </div>
            </div>

        </div>
      </div>
 <!--    
<?php //echo render_color_picker('settings[calendar_estimate_color]',_l('settings_calendar_color',_l('estimate')),get_brand_option('calendar_estimate_color')); ?>
<?php //echo render_color_picker('settings[calendar_reminder_color]',_l('settings_calendar_color',_l('reminder')),get_brand_option('calendar_reminder_color')); ?>
<?php //echo render_color_picker('settings[calendar_contract_color]',_l('settings_calendar_color',_l('contract')),get_brand_option('calendar_contract_color')); ?> -->
</div>
  
