<?php init_head(); 
$get_session_data = get_session_data();
?>
<div id="wrapper">
  <div class="content manage-subscription-page">
    <div class="row">
      <div class="col-md-12">         
		  <div class="breadcrumb">
			  <a href="<?php echo admin_url(); ?>"><i class="fa fa-home"></i></a>
			  <i class="fa fa-angle-right breadcrumb-arrow"></i>
			  <a href="<?php echo admin_url('setup'); ?>">Settings</a>
			  <i class="fa fa-angle-right breadcrumb-arrow"></i>
			  <span>Subscription Overview</span>
		  </div>         
		  <h1 class="pageTitleH1"><i class="fa fa-rocket"></i><?php echo $title; ?></h1>
          <div class="clearfix"></div>
        <div class="panel_s pricing_table_container show btmbrd">
          <div class="panel-body">
            <div class="clearfix"></div>
            <div class="row subscription-overview">                        
              <div class="table-responsive">
                <div class="membership-pricing-table">
                  <?php 
                    $session_data = get_session_data(); 
                    $package_type_id = $session_data['package_type_id'];
                  ?>
                  <table>
                    <tbody>
                      <tr>
                        <th></th>
                        <?php 
                          foreach ($packages as $key => $package) { 
                            $user_package = $session_data['package_id'];
                        ?>
                            <th class="plan-header <?php if($package['packageid']==$user_package) { ?>plan-header-standard<?php } else { ?>plan-header-blue<?php } ?>">
                              <div class="header-plan-inner">
                                <div class="pricing-plan-name"><?php echo $package['name']; ?></div>
                                  <div class="pricing-plan-price">
                                    <sup>$</sup><?php echo $package['price']; ?><span><!-- .00 --></span>
                                    <div class="pricing-plan-period">
                                        / <?php echo (@$package['package_permission'][$key]['trial_period'] != "" ? @$package['package_permission'][$key]['trial_period'] : '0'); ?>
                                      days
                                    </div>
                                  </div>
                                </div>
                              </th>
                        <?php } ?>
                      </tr>
                      <tr>
                        <td></td>
                        <?php foreach ($packages as $package) { 
                          $packagetypeid = $package['package_permission'][0]['packagetypeid'];
                        ?>
                          <td class="action-header">
                            <?php if($package['packageid'] == $user_package) { ?>
                              <a class="btn btn-danger _delete btn-icon" href="<?php echo admin_url('subscription/cancel_subscription/'.$package["packageid"]); ?>">UNSUBSCRIBE</a>
                            <?php } else { 
                              if($get_session_data['user_type'] == 1 && $packagetypeid >= $package_type_id) {
                            ?>
                              <a class="btn btn-info" onclick="subscription_payment(<?php echo $package['packageid']; ?>);" href="#">
                                  Subscribe
                              </a>
                            <?php } }?>
                          </td>
                        <?php } ?>
                      </tr>
                      <?php foreach ($permissions as $permission) { ?>
                        <tr>
                          <td><?php echo $permission['name']; ?></td>
                          <?php foreach ($packages as $package) { ?>
                            <td>
                              <?php if(in_array($permission['permissionid'], array_column($package['package_permission'], 'permissionid'))) {?>
                                <span class="fa fa-check"></span>
                              <?php } else { ?>
                                <span class="fa fa-times"></span>
                              <?php } ?>
                            </td>
                          <?php } ?>
                          </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12 small-table-right-col">
          <div id="subscription_section" class="hide">
          </div>
        </div>
      </div>
    </div>
  </div>
  
</div>
<?php init_tail(); ?>

<script type="text/javascript">
  function subscription_payment(id) {
      if (typeof(id) == 'undefined' || id == '') {
          return;
      }
      $('#subscription_section').load(admin_url + 'subscription/record_subscription_payment_ajax/' + id);
      $('.pricing_table_container').toggleClass('show hide');
      $('#subscription_section').toggleClass('show hide');
  }
  function init_subscription()
  {
    $('.pricing_table_container').toggleClass('show hide');
    $('#subscription_section').toggleClass('show hide');
  }

  function unsubscribe(packageid) {
    $.ajax({
      method: 'post',
      async: false,
      url: '<?php echo admin_url(); ?>subscription/cancel_subscription',
      data: 'packageid='+packageid,
      success: function(data) {
        response = JSON.parse(data);
        if (response.success == true) {
          window.location.reload();
          alert_float('success', response.message);
        } else {
          if(response.message != ''){
            alert_float('warning', response.message);
          }
        }
        window.location.reload();
      }
    });
    return false;
  }
</script>
</body>
</html>