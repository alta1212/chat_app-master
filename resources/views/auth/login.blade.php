<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Tyno-chat</title>
  <link href="https://fonts.googleapis.com/css?family=Montserrat:300, 400, 500" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/meyer-reset/2.0/reset.min.css">
<link rel="stylesheet" href="{{asset('distlog/style.css')}}">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>

<section class="user">
    
  <div class="user_options-container">
    <div class="user_options-text">
      <div class="user_options-unregistered">
      @if(Session::get('loi'))
            <div id="error" class="alert alert-danger">{{ Session::get('loi') }}</div>
        @endif
        <h2 class="user_unregistered-title">New user ?</h2>
        <p class="user_unregistered-text">Sign up now to connect to your friend</p>
        <button class="user_unregistered-signup" id="signup-button">Sign Up</button>
      </div>

      <div class="user_options-registered">
        <h2 class="user_registered-title">Allready user ?</h2>
        <p class="user_registered-text">login now to keep connect to you fiend</p>
        <button class="user_registered-login" id="login-button">Log in</button>
      </div>
    </div>
    
    <div class="user_options-forms" id="user_options-forms">
      <div class="user_forms-login">
        <h2 class="forms_title">log in</h2>
        <form  id="login"  method="POST" action="{{route('auth.doLogin')}}" class="forms_form">
        @csrf
          <fieldset class="forms_fieldset">
            <div  class="forms_field">
              <input  placeholder="Email" name="email" class="forms_field-input"  autofocus />
            </div>
            <div class="forms_field">
              <input type="password" placeholder="Password" name="password" class="forms_field-input"  />
            </div>
          </fieldset>
          <div class="forms_buttons">
           
            <button type="button" class="forms_buttons-forgot">Forget Password ?</button>
            <input type="submit" value="Đăng nhập" class="forms_buttons-action">
           
          </div>
        
        </form>
      </div>
      <div class="user_forms-signup">
        <h2 class="forms_title">Sign up</h2>
        <form id="signup" method="POST" action="{{route('auth.createUser')}}" class="forms_form">
        @csrf
          <fieldset class="forms_fieldset">
            <div class="forms_field">
              <input type="text" placeholder="Full Name" name="name" class="forms_field-input"  />
            </div>
            <div class="forms_field">
              <input type="text" placeholder="Email" name="email" class="forms_field-input"  />
            </div>
            <div class="forms_field">
              <input  type="password" name="password" placeholder="Password" class="forms_field-input"  />
            </div>
          </fieldset>
          <div class="forms_buttons">
            <input type="submit" value="Sign up" class="forms_buttons-action">
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

  <script  src="{{asset('distlog/script.js')}}"></script>
  <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">

</body>
</html>
