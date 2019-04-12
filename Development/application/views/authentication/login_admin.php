<?php
$this->load->view('authentication/includes/head.php'); ?>
<body class="login_admin"<?php if(is_rtl()){ echo ' dir="rtl"'; } ?>>
<div class="container">
  <div class="row">
    <div class="col-md-6 col-md-offset-3 authentication-form-wrapper">
      <div class="mtop40 authentication-form">
        <div class="company-logo">
          <?php get_company_logo(); ?>
        </div>
        <?php $this->load->view('authentication/includes/alerts'); ?>
        <div id="msg"></div>
        <?php echo form_open($this->uri->uri_string()); ?> <?php echo validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>
        <div class="form-group">
          <label for="email" class="control-label"><?php echo _l('admin_auth_login_email'); ?></label>
          <input type="email" id="email" name="email" class="form-control" autofocus value="<?php if(isset($_COOKIE['cook_admin_username']) && $_COOKIE['cook_admin_username']!=""){echo $_COOKIE['cook_admin_username']; }?>">
        </div>
        <div class="form-group">
          <label for="password" class="control-label"><?php echo _l('admin_auth_login_password'); ?></label>
          <input type="password" id="password" name="password" class="form-control" value="<?php if(isset($_COOKIE['cook_admin_password']) && $_COOKIE['cook_admin_password']!=""){echo $_COOKIE['cook_admin_password']; }?>">
        </div>
        <div class="checkbox">
          <input type="checkbox" id="remember" name="remember" <?php if(isset($_COOKIE['Admin_RememberMe']) && $_COOKIE['Admin_RememberMe']!=""){echo "checked='checked'"; }?> value="1">
          <label for="remember"><?php echo _l('admin_auth_login_remember_me'); ?> </label>
        </div>
        <div class="form-group">
          <button class="btn btn-info btn-lg btn-block"><?php echo _l('admin_auth_login_button'); ?></button>
        </div>
        <div class="form-group"> <a href="<?php echo site_url('authentication/forgot_password'); ?>"><?php echo _l('admin_auth_login_fp'); ?></a> </div>
          <div class="form-group">Not registered? <a href="#" class="" data-toggle="modal" data-target="#signup">Create an account</a></div>
        <?php if(get_option('recaptcha_secret_key') != '' && get_option('recaptcha_site_key') != ''){ ?>
        <div class="g-recaptcha" data-sitekey="<?php echo get_option('recaptcha_site_key'); ?>"></div>
        <?php } ?>
        <?php echo form_close(); ?>
        <hr>
        <div class="row btn-list logSocialList">
          <div class="col-md-4 col-xs-4">
            <button type="button" class="btn btn-primary btn-lg col-sm-12 btn-facebook" data-toggle="tooltip" data-placement="top" title="Login with Facebook"><i class="fa fa-facebook" aria-hidden="true"></i> Facebook</button>
          </div>
          <div class="col-md-4 col-xs-4">
            <button type="button" class="btn btn-primary btn-lg col-sm-12 btn-twitter" data-toggle="tooltip" data-placement="top" title="Login with Twitter"><i class="fa fa-twitter" aria-hidden="true"></i> Twitter</button>
          </div>
          <div class="col-md-4 col-xs-4">
            <button type="button" class="btn btn-primary btn-lg col-sm-12 btn-google" data-toggle="tooltip" data-placement="top" title="Login with Google"><i class="fa fa-google" aria-hidden="true"></i> Google</button>
          </div>
          <form id="frmsocial" style="display: hidden" action="<?php echo base_url();?>socialregister" method="POST" id="form">
            <input type="hidden" id="firstName" name="firstname" value=""/>
            <input type="hidden" id="socialemail" name="socialemail" value=""/>
            <input type="hidden" id="twitter" name="twitter" value=""/>
            <input type="hidden" id="facebook" name="facebook" value=""/>
            <input type="hidden" id="google" name="google" value=""/>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $this->load->view('authentication/includes/scripts.php'); ?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.4.0/firebase.js"></script>

<div id="signup" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <?php
            $data['brandtypes'] = $brandtypes;
            $data['packages'] = $packages;
            $this->load->view('register2.php',$data); ?>
            <!--<div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <p>Some text in the modal.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>-->
        </div>

    </div>
</div>

<script>
  // Initialize Firebase
  var config = {
    apiKey: "AIzaSyDxGDGI9C8KzO3mZvW7teRG_sIukmrFRRs",
    authDomain: "simply-i-do.firebaseapp.com",
    databaseURL: "https://simply-i-do.firebaseio.com",
    projectId: "simply-i-do",
    storageBucket: "simply-i-do.appspot.com",
    messagingSenderId: "941976528963"
  };
  firebase.initializeApp(config);
</script>
<script type="text/javascript">
  var rootRef = firebase.database().ref();
  var auth = firebase.auth();

  $(".btn-twitter").click(function(){
    var provider = new firebase.auth.TwitterAuthProvider();

    auth.signInWithPopup(provider).then(function(result) {
      var email = result.additionalUserInfo.profile.email;
      var strname = result.additionalUserInfo.profile.name;          

      if(email != "") {
        $("#socialemail").val(email);
        $("#firstname").val(strname);
        $("#twitter").val(1);
        $("#facebook").val(0);
        $("#google").val(0);
        $("#frmsocial").submit();
      } else {
        $("#msg").html('Please provide valid data');
        $("#msg").addClass('alert alert-danger alert-dismissable');
      }
      
      firebase.auth().signOut().then(function() {
        // Sign-out successful.
        console.log('Signed Out');
      }).catch(function(error) {
        // An error happened.
        console.error('Sign Out Error', error);
      });
    }).catch(function(error) {
      // Handle Errors here.
      var errorCode = error.code;
      var errorMessage = error.message;
      
      $("#msg").html(errorMessage);
      $("#msg").addClass('alert alert-danger alert-dismissable');

      firebase.auth().signOut().then(function() {
        // Sign-out successful.
        console.log('Signed Out');
      }).catch(function(error) {
        // An error happened.
        console.error('Sign Out Error', error);
      });
    });
  });

  $(".btn-google").click(function(){
    var provider = new firebase.auth.GoogleAuthProvider();

    firebase.auth().signInWithPopup(provider).then(function(result) {
      // This gives you a Google Access Token. You can use it to access the Google API.
      var token = result.credential.accessToken;
      // The signed-in user info.
      var user = result.user;          
      
      if(user.email != "") {
        $("#socialemail").val(user.email);
        $("#firstname").val(user.displayName);
        $("#twitter").val(0);
        $("#facebook").val(0);
        $("#google").val(1);
        $("#frmsocial").submit();
      } else {
        $("#msg").html('Please provide valid data');
        $("#msg").addClass('alert alert-danger alert-dismissable');
      }

      firebase.auth().signOut().then(function() {
        // Sign-out successful.
        console.log('Signed Out');
      }).catch(function(error) {
        // An error happened.
        console.error('Sign Out Error', error);
      });
    }).catch(function(error) {
      // Handle Errors here.
      var errorCode = error.code;
      var errorMessage = error.message;
      
      $("#msg").html(credential);
      $("#msg").addClass('alert alert-danger alert-dismissable');

      firebase.auth().signOut().then(function() {
        // Sign-out successful.
        console.log('Signed Out');
      }).catch(function(error) {
        // An error happened.
        console.error('Sign Out Error', error);
      });
    });
  });

  $(".btn-facebook").click(function(){
    var provider = new firebase.auth.FacebookAuthProvider();

    firebase.auth().signInWithPopup(provider).then(function(result) {
      // This gives you a Google Access Token. You can use it to access the Google API.
      var token = result.credential.accessToken;
      // The signed-in user info.
      var user = result.user;          
      
      if(user.email != "") {
        $("#socialemail").val(user.email);
        $("#firstname").val(user.displayName);
        $("#twitter").val(0);
        $("#facebook").val(1);
        $("#google").val(0);
        $("#frmsocial").submit();
      } else {
        $("#msg").html('Please provide valid data');
        $("#msg").addClass('alert alert-danger alert-dismissable');
      }

      firebase.auth().signOut().then(function() {
        // Sign-out successful.
        console.log('Signed Out');
      }).catch(function(error) {
        // An error happened.
        console.error('Sign Out Error', error);
      });
    }).catch(function(error) {
      // Handle Errors here.
      var errorCode = error.code;
      var errorMessage = error.message;
      
      $("#msg").html(errorMessage);
      $("#msg").addClass('alert alert-danger alert-dismissable');
      
      firebase.auth().signOut().then(function() {
        // Sign-out successful.
        console.log('Signed Out');
      }).catch(function(error) {
        // An error happened.
        console.error('Sign Out Error', error);
      });
    });
  });
</script>
</body>
</html>