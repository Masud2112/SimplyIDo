<div class="bgimage"></div>
<div class="login-center">
    <div class="navbar-header text-center logo mx-auto">
        <a href="index.html">
            <img alt="Simply I Do" src="{{logoSrc}}">
        </a>
    </div>
    <div id="msg"></div>
    <!-- /.navbar-header -->
    <form id="login-form" onsubmit="return false;" data-parsley-validate class="form-material">
        <div class="form-group">
            <input type="text" name="username" id="field-userName" class="form-control form-control-line" autocapitalize="off" autocorrect="off" tabindex="1" required data-parsley-required-message="Please enter valid email address">
            <label for="example-email">{{translate 'Email Address'}}</label>
        </div>
        <div class="form-group">
            <input type="password" name="password" id="field-password" class="form-control form-control-line" tabindex="2" required data-parsley-required-message="Please enter password">
            <label>{{translate 'Password'}}</label>
        </div>
        <div class="form-group">
             <button type="submit" class="btn btn-block btn-lg btn-color-scheme ripple" id="btn-login" tabindex="3">{{translate 'Login' scope='User'}}</button>
        </div>
        <div class="form-group no-gutters mb-0">
            <div class="col-md-12 d-flex">
                <div class="checkbox checkbox-info mr-auto">
                </div>
                <a href="javascript:" class="my-auto pb-2 text-right" data-action="passwordChangeRequest" tabindex="4"><i class="fa fa-lock mr-1"></i>{{translate 'Forgot Password?' scope='User'}}</a>                
            </div>
            <!-- /.col-md-12 -->
        </div>
        <!-- /.form-group -->
    </form>
    <hr>
    <div class="row btn-list social-btn">
      <a href="javascript:" data-action="socialSignUpRequest" tabindex="4" id="socialSignup"></a> 
      <div class="col-md-4">
        <button type="button" class="btn btn-block btn-facebook ripple" id="btnfb" data-toggle="tooltip" data-placement="top" title="Login with Facebook"><i class="social-icons list-icon">facebook</i> Facebook</button>
      </div>
      <div class="col-md-4">
        <button type="button" class="btn btn-block btn-twitter ripple" data-toggle="tooltip" data-placement="top" title="Login with Twitter"><i class="social-icons list-icon">twitterbird</i> Twitter</button>
      </div>
      <div class="col-md-4">
        <button class="g-signin btn btn-block btn-googleplus ripple" 
            data-scope="https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email"
            data-requestvisibleactions="http://schemas.google.com/AddActivity"
            data-clientId="93979330445-91cjr76o7jg9jd1aqkmnmp50rhd8m0ul.apps.googleusercontent.com"
            data-accesstype="offline"
            data-callback="mycoddeSignIn"
            data-theme="dark"
            data-cookiepolicy="single_host_origin"
            data-toggle="tooltip" data-placement="top" title="Login with Google">
            <i class="social-icons list-icon">googleplus</i> Google
        </button>
      </div>
    </div>
    <!-- /.btn-list -->
    <!--<footer class="col-sm-12 text-center">
      <hr>
      <p>Don't have an account? <a href="javascript:" data-action="registerRequest" class="text-primary m-l-5"><b>Sign Up</b></a>
      </p>
    </footer>-->
</div>
<script type="text/javascript">
  $('#login-form').parsley();
  var el = $('input:not([type=checkbox]):not([type=radio]), textarea');
 
  el.each(function() {
    var $this = $(this),
        self = this;

    var hasValueFunction = function() {
      if( self.value.length > 0 ) {
        self.parentNode.classList.add('input-has-value');
        $(self).closest('.form-group').addClass('input-has-value');
      }
      else {
        self.parentNode.classList.remove('input-has-value');
        $(self).closest('.form-group').removeClass('input-has-value');
      }
    };

    hasValueFunction(this);
    $this.on('input', hasValueFunction);

    $this.focusin(function() {
      this.parentNode.classList.add('input-focused');
      $this.closest('.form-group').addClass('input-focused');
    });
    $this.focusout(function() {
      this.parentNode.classList.remove('input-focused');
      $this.closest('.form-group').removeClass('input-focused');
    });

    $this.find('.remove-focus').on('click',function() {
      $this.emit('focusout');
    });
  });
</script>

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
  $(".btn-twitter").click(function(){
    var rootRef = firebase.database().ref();

    // Reference
    /*var key = ref.key;
    var rootRef = ref.root;
    var parentRef = ref.parent;

    // Query
    var queryRef = query.ref;

    // DataSnapshot
    ref.on("value", function(snapshot) {
      var dataRef = snapshot.ref;
      var dataKey = snapshot.key;
    });*/

    var auth = firebase.auth();

    var provider = new firebase.auth.TwitterAuthProvider();
    auth.signInWithPopup(provider).then(function(result) {
      var email = result.additionalUserInfo.profile.email;
      var strname = result.additionalUserInfo.profile.name;     
      /*var ret = strname.split(" ");
      var firstName = ret[0];
      var lastName = ret[1];*/
      
      if(email != "") {
        var userobj = {};
        userobj.email = email;
        userobj.name = strname;
        
        $('#socialSignup').trigger("click", [userobj]);
      } else {
        $("#msg").html('Please provide valid data');
        $("#msg").addClass('alert alert-danger alert-dismissable');
      }
    }).catch(function(error) {
      // Handle Errors here.
      var errorCode = error.code;
      var errorMessage = error.message;
      // The email of the user's account used.
      var email = error.email;
      // The firebase.auth.AuthCredential type that was used.
      var credential = error.credential;
      // ...
      $("#msg").html(credential);
      $("#msg").addClass('alert alert-danger alert-dismissable');
    });
  });

  var gpclass = (function(){
    //Defining Class Variables here
    var response = undefined;
    return {
      //Class functions / Objects
      mycoddeSignIn:function(response){
        // The user is signed in
        if (response['access_token']) {
          //Get User Info from Google Plus API
          gapi.client.load('plus','v1',this.getUserInformation);
            
        } else if (response['error']) {
          $("#msg").html(response['error']);
          $("#msg").addClass('alert alert-danger alert-dismissable');
        }
      },
        
      getUserInformation: function(){
        var request = gapi.client.plus.people.get( {'userId' : 'me'} );
        request.execute( function(profile) {
          var email = profile['emails'].filter(function(v) {
            return v.type === 'account'; // Filter out the primary email
          })[0].value;

          var fName = profile.displayName;
          if(email != "") {
            var userobj = {};
            userobj.email = email;
            userobj.name = fName;
            
            $('#socialSignup').trigger("click", [userobj]);
          } else {
            $("#msg").html('Please provide valid data');
            $("#msg").addClass('alert alert-danger alert-dismissable');
          } 
        });
      }
      }; //End of Return
  })();
    
  function mycoddeSignIn(gpSignInResponse){
    gpclass.mycoddeSignIn(gpSignInResponse);
  }

  $(".btn-facebook").click(function() {
    if(this.id == 'btnfb') {
      fbLogin();
    }
  });

  window.fbAsyncInit = function() {
    // FB JavaScript SDK configuration and setup
    FB.init({
      appId      : '170452413514741', // FB App ID
      cookie     : true,  // enable cookies to allow the server to access the session
      xfbml      : true,  // parse social plugins on this page
      version    : 'v2.8' // use graph api version 2.8
    });
    
    // Check whether the user already logged in
    FB.getLoginStatus(function(response) {
      if (response.status === 'connected') {
          //display user data
          getFbUserData();
      }
    });
  };

  // Load the JavaScript SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

  // Facebook login with JavaScript SDK
  function fbLogin() {
    console.log("biutt click");
    FB.login(function (response) {
      if (response.authResponse) {
        // Get and display the user profile data
        getFbUserData();
      } else {
        $("#msg").html('User cancelled login or did not fully authorize.');
      }
    }, {scope: 'email'});
  }

  // Fetch the user profile data from facebook
  function getFbUserData(){
    FB.api('/me', {locale: 'en_US', fields: 'id,first_name,last_name,email,link,gender,locale,picture'},
    function (response) {
      if(response.email != "") {
        var userobj = {};
        userobj.email = response.email;
        userobj.name = response.first_name + " " + response.last_name;
        
        $('#socialSignup').trigger("click", [userobj]);
      } else {
        $("#msg").html('Please provide valid data');
        $("#msg").addClass('alert alert-danger alert-dismissable');
      } 
    });
  }
</script>
<footer class="container">{{{footer}}}</footer>