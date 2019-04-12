<?php
/**
 * Added By : Vaidehi
 * Dt : 10/12/2017
 * For Brand Settings Module
 */
?>
<?php if(isset($packagename) && $packagename != "Paid") { ?>
    <div class="no-access">
        <h2><?php echo _l('brand_no_access'); ?></h2><br/>
        <p><?php echo _l('brand_settings_no_access'); ?></p>
    </div>
    <div class="overlay"></div>
<?php } ?>
<div <?php echo ((isset($packagename) && $packagename != "Paid") ? 'class="settings-noaccess"' : '');?>>
    <!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
    <input  type="text" class="fake-autofill-field hide" name="fakeusernameremembered" value=''/>
    <input  type="password" class="fake-autofill-field hide" name="fakepasswordremembered" value=''/>
    <!-- <h4><?php echo _l('settings_smtp_settings_heading'); ?></h4>
    <p class="text-muted"><?php echo _l('settings_smtp_settings_subheading'); ?></p>
    <hr /> -->
    <input type="hidden" name="email-setup" id="email-setup" value="0">
    <span id="email-setup-error"></span>
    <!-- div class="form-group mtop15" id="gmail-authorize">
			<button id="authorize-button" type="button"></button>
			<input type="hidden" name="settings[gmail_email]" id="gmail_email" value="<?php //echo get_brand_option('gmail_email'); ?>">
			<input type="hidden" name="settings[gmail_id]" id="gmail_id" value="<?php //echo get_brand_option('gmail_id'); ?>">
			<div id="account-link">
				<h4>Connected Account</h4>
				<span id="account"><?php //echo get_brand_option('gmail_email'); ?></span>
				<button class="btn btn-default" id="btn-remove">Remove</button>
			</div>
			<a id="smtp-button" type="button" href="javascript: void(0);" onclick="fnUseSMTP();">Use SMTP</a>
		</div>
		<div id="email-settings" class=""> -->
    <div class="col-md-6">
        <div class="form-group">
            <label for="email_protocol"><?php echo _l('email_protocol'); ?></label><br />
            <select name="settings[email_protocol]" id="email_protocol" class="form-control">
                <option value="gmail" <?php if('gmail' == get_brand_option('email_protocol')){echo 'selected="selected"';} ?>>Gmail</option>
                <option value="yahoo" <?php if('yahoo' == get_brand_option('email_protocol')){echo 'selected="selected"';} ?>>Yahoo</option>
                <option value="aol" <?php if('aol' == get_brand_option('email_protocol')){echo 'selected="selected"';} ?>>AOL</option>
                <option value="hotmail" <?php if('hotmail' == get_brand_option('email_protocol')){echo 'selected="selected"';} ?>>Hotmail</option>
                <option value="godaddy" <?php if('godaddy' == get_brand_option('email_protocol')){echo 'selected="selected"';} ?>>Godaddy</option>
                <option value="smtp" <?php if('smtp' == get_brand_option('email_protocol')){echo 'selected="selected"';} ?>>Other</option>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group" id="div-smtp-encryption">
            <label for="smtp_encryption"><?php echo _l('smtp_encryption'); ?></label><br />
            <select name="settings[smtp_encryption]" id="smtp_encryption" class="selectpicker" data-width="100%">
                <option value="" <?php if(get_brand_option('smtp_encryption') == ''){echo 'selected';} ?>><?php echo _l('smtp_encryption_none'); ?></option>
                <option value="ssl" <?php if(get_brand_option('smtp_encryption') == 'ssl'){echo 'selected';} ?>>SSL</option>
                <option value="tls" <?php if(get_brand_option('smtp_encryption') == 'tls'){echo 'selected';} ?>>TLS</option>
            </select>
        </div>
    </div>
    <div class="col-md-6">
    <div class="form-group" id="div-smtp-host">
        <label for="settings[smtp_host]" class="control-label"><?php echo _l('settings_email_host'); ?></label>
        <input type="text" id="smtp_host" name="settings[smtp_host]" class="form-control" value="<?php echo get_brand_option('smtp_host'); ?>">
    </div>
    </div>
    <div class="col-md-6">
    <div class="form-group" id="div-smtp-port">
        <label for="settings[smtp_port]" class="control-label"><?php echo _l('settings_email_port'); ?></label>
        <input type="text" id="smtp_port" name="settings[smtp_port]" class="form-control" value="<?php echo get_brand_option('smtp_port'); ?>">
    </div>
    </div>
   
    <?php //echo render_input('settings[smtp_host]','settings_email_host',get_brand_option('smtp_host'), 'text', array(), array('id' => 'div-smtp-host')); ?>
    <?php //echo render_input('settings[smtp_port]','settings_email_port',get_brand_option('smtp_port'), 'text', array(), array('id' => 'div-smtp-port')); ?>
    
    <div class="col-md-6">
        <?php echo render_input('settings[smtp_email]','settings_email',get_brand_option('smtp_email'), 'email', array(), array('id' => 'div-smtp-email')); ?>
    </div>
    <!--<i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php //echo _l('smtp_username_help'); ?>"></i>-->
    <?php //echo render_input('settings[smtp_username]','smtp_username',get_brand_option('smtp_username'), 'text', array(), array('id' => 'div-smtp-username')); ?>
    
    <div class="col-md-6">
    <?php
    $ps = get_brand_option('smtp_password');
    if(!empty($ps)){
        if(false == $this->encryption->decrypt($ps)){
            $ps = $ps;
        } else {
            $ps = $this->encryption->decrypt($ps);
        }
    }
    echo render_input('settings[smtp_password]','settings_email_password',$ps,'password',array('autocomplete'=>'off'), array('id' => 'div-smtp-password')); ?>
    </div>

    <?php //echo render_input('settings[smtp_email_charset]','settings_email_charset',get_brand_option('smtp_email_charset'), 'text', array(), array('id' => 'div-smtp-email-charset')); ?>

    <div class="col-md-12">
        <?php echo render_textarea('settings[email_signature]','settings_email_signature',get_brand_option('email_signature'), array(), array('id' => 'div-smtp-email-signature')); ?>
    </div>
    <!--<hr />-->
    <?php //echo render_textarea('settings[email_header]','email_header',get_brand_option('email_header'),array('rows'=>15)); ?>
    <?php //echo render_textarea('settings[email_footer]','email_footer',get_brand_option('email_footer'),array('rows'=>15)); ?>
    <!--</div>-->


    <!-- <h4><?php //echo _l('settings_send_test_email_heading'); ?></h4>
    <p class="text-muted"><?php //echo _l('settings_send_test_email_subheading'); ?></p> -->
    <div class="col-md-6">
        <div class="form-group">
                <label for="" class="control-label"><?php echo _l('settings_send_test_email_heading'); ?></label>
            <div class="input-group">
                <input type="email" class="form-control" name="test_email" data-ays-ignore="true" placeholder="<?php echo _l('settings_send_test_email_string'); ?>">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default test_email p7">Test</button>
                </div>
            </div>
        </div>
</div>
</div>