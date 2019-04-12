<?php
/**
* Added By : Vaidehi
* Dt : 05/01/2018
* For Settings Module
*/
function render_theme_styling_picker($id, $value, $target,$css,$additional = ''){
    echo '<div class="input-group mbot15 colorpicker-component" data-target="'.$target.'" data-css="'.$css.'" data-additional="'.$additional.'">
    <input type="text" value="'.$value.'" name="settings[theme_style]['.$id.']" data-id="'.$id.'" class="form-control" />
    <span class="input-group-addon"><i></i></span>
</div>';
}
?>
<div>
	<?php echo form_hidden('settings[customer_settings]','true'); ?>
    <div class="row">
        <div class="col-md-10">
        	<div class="form-group">
        		<label for="clients_default_theme" class="control-label"><?php echo _l('settings_clients_default_theme'); ?></label>
        		<select name="settings[clients_default_theme]" id="clients_default_theme" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
        			<?php foreach(get_all_client_themes() as $theme){ ?>
        			   <?php if($theme != 'perfex') { ?>
        			    <option value="<?php echo $theme; ?>" <?php if(active_clients_theme() == $theme){echo 'selected';} ?>><?php echo ucfirst($theme); ?></option>
        			   <?php } ?>
        			<?php } ?>
        		</select>
        	</div>
        </div>
    </div>
	<div class="tabs tabs-bordered">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
              <a href="#general" aria-controls="general" role="tab" data-toggle="tab"><?php echo _l('settings_theme_general_heading'); ?></a>
            </li>
            <li role="presentation">
              <a href="#side_menu" aria-controls="side_menu" role="tab" data-toggle="tab"><?php echo _l('settings_theme_menu_heading'); ?></a>
            </li>
            <li role="presentation">
              <a href="#buttons" aria-controls="buttons" role="tab" data-toggle="tab"><?php echo _l('settings_theme_button_heading'); ?></a>
            </li>
            
        </ul>
        <!-- /.nav-tabs -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="general">
                <div class="col-md-12">
                    <?php 
                        foreach(get_styling_areas('general') as $area) { ?>
                            <div class="col-md-6">
                                <label class="bold mbot10 inline-block"><b><?php echo $area['name']; ?></b></label>
                                <?php render_theme_styling_picker($area['id'], get_custom_style_values('general',$area['id']),$area['target'],$area['css'],$area['additional_selectors']); ?>
                                <?php if(isset($area['example'])){echo $area['example'];} ?>
                                <hr/>
                            </div>
                    <?php  } ?>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="side_menu">
                <div class="col-md-12">
                	<?php
                        foreach(get_styling_areas('admin') as $area) { ?>
                            <div class="col-md-6">
                            	<label class="bold mbot10 inline-block"><b><?php echo $area['name']; ?></b></label>
                            	<?php render_theme_styling_picker($area['id'], get_custom_style_values('admin',$area['id']),$area['target'],$area['css'],$area['additional_selectors']); ?>
                                <hr/>
                            </div>                
                    <?php  } ?>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="buttons">
                <div class="col-md-12">
                    <?php 
                    	foreach(get_styling_areas('buttons') as $area) { ?>
                             <div class="col-md-6">
                            	<label class="bold mbot10 inline-block"><b><?php echo $area['name']; ?></b></label>
                            	<?php render_theme_styling_picker($area['id'], get_custom_style_values('buttons',$area['id']),$area['target'],$area['css'],$area['additional_selectors']); ?>
                            	<?php if(isset($area['example'])){echo $area['example'];} ?>
                            	<div class="clearfix"></div>
                                <hr/>
                    		</div>
                    <?php  } ?>
                </div>
            </div>
            
        </div>
        <!-- /.tab-content -->
    </div>
</div>