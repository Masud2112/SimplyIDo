<?php $this->load->view('authentication/includes/head.php'); ?>
<body class="authentication">
 <div class="container">
  <div class="row">
   <div class="col-md-4 col-md-offset-4 authentication-form-wrapper">
    
   <div class="mtop40 authentication-form">
   <div class="company-logo">
     <?php echo get_company_logo(); ?>
   </div>
    <p>Enter your email address and we'll send you an email with instructions to reset your password.

</p>
    <?php echo form_open($this->uri->uri_string()); ?>
    <?php echo validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
    <?php $this->load->view('authentication/includes/alerts'); ?>
    <?php echo render_input('email','admin_auth_forgot_password_email',set_value('email'),'email'); ?>
    <div class="form-group">
      <button type="submit" class="btn btn-info btn-lg btn-block"><?php echo _l('admin_auth_forgot_password_button'); ?></button>
    </div>
    <?php echo form_close(); ?>
    <div class="backlink"  >Back to  <a  href="<?php echo site_url('authentication'); ?>"><?php echo _l('back_to_login'); ?></a></div>
   
  </div>
</div>
</div>
</div>
</body>
</html>
