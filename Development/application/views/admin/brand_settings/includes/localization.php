<?php
/**
* Added By : Vaidehi
* Dt : 10/12/2017
* For Brand Settings Module
*/
?>
<?php
$date_formats = get_available_date_formats();
?>
<div class="localization_sett_blk">
    <div class="col-md-4">
        <div class="form-group">
            <label for="dateformat" class="control-label"><?php echo _l('settings_localization_date_format'); ?></label>
            <div class="input-group">
              <span class="input-group-addon" id="basic-addon1"><i class="fa fa-calendar-o" aria-hidden="true"></i></span>
              <select name="settings[dateformat]" id="dateformat" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                    <?php foreach($date_formats as $key => $val){ ?>
                    <option value="<?php echo $key; ?>" <?php if($key == get_brand_option('dateformat')){echo 'selected';} ?>><?php echo $val; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label for="time_format" class="control-label"><?php echo _l('time_format'); ?></label>
            <div class="input-group">
              <span class="input-group-addon" id="basic-addon1"><i class="fa fa-clock-o" aria-hidden="true"></i></span>
              <select name="settings[time_format]" id="time_format" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                    <option value="24" <?php if('24' == get_brand_option('time_format')){echo 'selected';} ?>><?php echo _l('time_format_24'); ?></option>
                    <option value="12" <?php if('12' == get_brand_option('time_format')){echo 'selected';} ?>><?php echo _l('time_format_12'); ?></option>
                </select>
            </div>
            
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="timezones" class="control-label"><?php echo _l('settings_localization_default_timezone'); ?></label>
            <div class="input-group">
              <span class="input-group-addon" id="basic-addon1"><i class="fa fa-map-marker" aria-hidden="true"></i></span>
              <select name="settings[default_timezone]" id="timezones" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-live-search="true">
                <?php //foreach(get_timezones_list() as $key => $timezones){ ?>
                <!--<optgroup label="<?php //echo $key; ?>">-->
                    <?php foreach(get_timezones_list() as $key => $timezone){ ?>
                    <option value="<?php echo $key; ?>" <?php if(get_brand_option('default_timezone') == $key){echo 'selected';} ?>><?php echo $timezone; ?></option>
                    <?php } ?>
                <!--</optgroup>-->
                <?php //} ?>
            </select>
            </div>
            
        </div>
    </div>
</div>