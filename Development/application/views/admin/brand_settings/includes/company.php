<?php
/**
 * Added By : Vaidehi
 * Dt : 10/12/2017
 * For Brand Settings Module
 */
?>
<div role="tabpanel" class="tab-pane" id="company_info">
    <p class="text-muted">
        <?php echo _l('settings_sales_company_info_note'); ?>
    </p>
    <div class="col-md-6">
        <div class="bshead">
            <h4 class="pull-left">Name</h4>
        </div>
        <div class="bsBody">
            <?php echo render_input('settings[invoice_company_name]', 'settings_sales_company_name', get_brand_option('invoice_company_name')); ?>
        </div>
    </div>

    <div class="col-md-6">
        <div class="bshead">
            <h4 class="pull-left">Phone</h4>
        </div>
        <div class="bsBody">
            <div class="row">
                <?php echo render_input('settings[invoice_company_phonenumber]', 'settings_sales_company_phone', get_brand_option('invoice_company_phonenumber'), '', array(), array(), 'col-xs-9 col-sm-10', 'companyphone'); ?>

                <?php echo render_input('settings[invoice_company_phone_ext]', 'settings_sales_company_phone_ext', get_brand_option('invoice_company_phone_ext'), '', array(), array(), 'col-xs-3 col-sm-2', 'companyphoneext'); ?>

            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="bshead">
            <h4 class="pull-left">Email</h4>
        </div>
        <div class="bsBody">
        <?php echo render_input('settings[invoice_company_email]', 'email', get_brand_option('invoice_company_email'), 'email', array(), array(), '', 'companyemail'); ?>
        </div>
        <div class="bshead">
            <h4 class="pull-left">Address</h4>
        </div>
        <div class="bsBody">
            <?php echo render_input('settings[invoice_company_address]', 'settings_sales_address', get_brand_option('invoice_company_address')); ?>
            <div class="row">
                <div class="col-md-4"><?php echo render_input('settings[invoice_company_city]', 'settings_sales_city', get_brand_option('invoice_company_city')); ?></div>
                <div class="col-md-4"><?php echo render_input('settings[company_state]', 'billing_state', get_brand_option('company_state')); ?></div>
                <div class="col-md-4"><?php echo render_input('settings[invoice_company_postal_code]', 'settings_sales_postal_code', get_brand_option('invoice_company_postal_code')); ?></div>
            </div>
        </div>
    </div>

    <!-- <div class="col-md-6">
        <div class="bshead">
            <h4 class="pull-left">Name</h4>
        </div>
        <div class="bsBody">
        </div>
    </div> -->

    <div class="col-md-6">
        <div class="bshead">
            <h4 class="pull-left">Company Info</h4>
        </div>
        <div class="bsBody">
            <?php echo render_textarea('settings[company_info_format]', 'company_info_format', clear_textarea_breaks(get_brand_option('company_info_format')), array('rows' => 8, 'style' => 'line-height:20px;')); ?>
            <p><a href="#" class="settings-textarea-merge-field" data-to="company_info_format">{company_name}</a><a
                        href="#" class="settings-textarea-merge-field" data-to="company_info_format">{address}</a>,<a
                        href="#" class="settings-textarea-merge-field" data-to="company_info_format">{city}</a>,<a
                        href="#" class="settings-textarea-merge-field" data-to="company_info_format">{state}</a>,<a
                        href="#" class="settings-textarea-merge-field" data-to="company_info_format">{zip_code}</a></p>
        </div>
    </div>

    <?php $custom_company_fields = get_company_custom_fields();
    if (count($custom_company_fields) > 0) {
        echo '<hr />';
        echo '<p><b>' . _l('custom_fields') . '</b></p>';
        echo '<ul class="list-group">';
        foreach ($custom_company_fields as $field) {
            echo '<li class="list-group-item"><b>' . $field['name'] . '</b>: ' . '<a href="#" class="settings-textarea-merge-field" data-to="company_info_format">{cf_' . $field['id'] . '}</a></li>';
        }
        echo '</ul>';
        echo '<hr />';
    }

    $selectedservices = get_brand_option('brandtypes');
    $selectedservices = unserialize($selectedservices);
    if (empty($selectedservices)) {
        $selectedservices = array();
    }
    ?>

    <div class="col-md-12">
        <div class="bshead">
            <h4 class="pull-left"><?php echo _l('service_types') ?></h4>
            <div class="pull-right">
                <a href="#" class="btn btn-info" data-toggle="modal" data-target="#new_brand_type" id="add_new_brand">
                    <i class="fa fa-plus-square mright5"></i>
                    <?php echo _l('new') ?>
                </a>
            </div>
        </div>
        <div class="bsBody">
            <div class="row">
                <?php foreach ($brandtypes as $brandtype) { ?>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <input id="<?php echo $brandtype['brandtypeid'] ?>" type="checkbox" class="checkbox"
                                   name="settings[brandtypes][]"
                                   value="<?php echo $brandtype['brandtypeid'] ?>" <?php echo in_array($brandtype['brandtypeid'], $selectedservices) ? "checked" : "" ?> >
                            <label for="<?php echo $brandtype['brandtypeid'] ?>"><?php echo $brandtype['name'] ?></label>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

</div>