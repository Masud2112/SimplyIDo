<div class="onlinePayMode">
<?php
/**
* Added By : Vaidehi
* Dt : 10/12/2017
* For Brand Settings Module
*/
//if(is_sido_admin()) {
  if (isset($_GET['code'])) {?>
    <input type="hidden" name="settings[oauth_code]" id="settings[oauth_code]" value="<?php echo $_GET['code']; ?>"/>
<?php
  }
//}
?>
<ul class="nav nav-tabs" role="tablist">
<?php
foreach($payment_gateways as $gateway){
  $class_name = $gateway['id'].'_gateway';
  $classb = "";
  if($gateway['id'] == "stripe"){
    $classb = "active";
  }
  ?>
  <li role="presentation" class="<?php echo  $classb; ?>">
    <a href="#online_payments_<?php echo $gateway['id']; ?>_tab" aria-controls="online_payments_paypal_tab" role="tab" data-toggle="tab"><?php echo $this->$class_name->get_name(); ?></a>
  </li>
  <?php } ?>
</ul>
<div class="tab-content mtop30">
<?php
foreach($payment_gateways as $gateway){
  $class_name = $gateway['id'].'_gateway';
  $classa = "";
  if($gateway['id'] == "stripe"){
    $classa = "active";
  }
?>
  <div role="tabpanel" class="tab-pane <?php echo $classa; ?>" id="online_payments_<?php echo $gateway['id']; ?>_tab">
   <!-- <h4><?php echo $this->$class_name->get_name(); ?></h4> -->
   <?php //do_action('before_render_payment_gateway_settings',$gateway); ?>
   <!-- <hr /> -->
   <?php $settings = $this->$class_name->get_settings();
   foreach($settings as $option){
    $value = get_brand_option($option['name']);
    $value = isset($option['encrypted']) && $option['encrypted'] == true ? $this->encryption->decrypt($value) : $value;
    if(!isset($option['type'])){$option['type'] = 'input';};
    if($option['type'] == 'yes_no'){
      render_yes_no_option($option['name'], $option['label']);
    } else if($option['type'] == 'input') {
      echo render_input('settings['.$option['name'].']', $option['label'],$value);
    } else if($option['type'] == 'textarea') {
      echo render_textarea('settings['.$option['name'].']', $option['label'],$value);
    } else {
      echo '<p>Input Type For This Option Not Specific</p>';
    }
  }
  ?>
</div>
<?php } ?>
</div>
</div>