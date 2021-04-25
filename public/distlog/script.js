/**
 * Variables
 */

const signupButton = document.getElementById('signup-button'),
    loginButton = document.getElementById('login-button'),
    userForms = document.getElementById('user_options-forms')

/**
 * Add event listener to the "Sign Up" button
 */
signupButton.addEventListener('click', () => {
  userForms.classList.remove('bounceRight')
  userForms.classList.add('bounceLeft')
  $("#error").remove()
  //https://2.pik.vn/2021b4dd60b3-93ee-4c78-9a86-8becafab0af2.jpg
  $("body").css("background","url(https://i.ibb.co/myZBgtC/jerry.webp) no-repeat center center fixed");
  $("body").css("background-size","cover");
}, false)

/**
 * Add event listener to the "Login" button
 */
loginButton.addEventListener('click', () => {
  userForms.classList.remove('bounceLeft')
  userForms.classList.add('bounceRight')
  $("#error").remove();
  $("body").css("background","url(https://i.ibb.co/NKr0fLv/tom.png) no-repeat center center fixed");
  $("body").css("background-size","cover");
}, false)



$("#signup").submit(function() {
  event.preventDefault();
  var data = $("#signup").serialize();
  
  $.ajax({
    url:"/createUser",
    type:"post",
    data: data,
    
    success : function (e){
      toastr.success("Đăng ký thành công");
      loginButton.click();
      console.log(e);
    },
    error : function (e){    
      console.log(e)
     var k=e.responseJSON;
     for(var j in k.errors)
      toastr.error(k.errors[j][0]);
    }
});
});

$("#login").submit(function() {

  var data = $("#login").serialize();

  $.ajax({
    url:"/doLogin",
    type:"post",
    data: data,
    
    success : function (e){
  
      
    },
    error : function (e){    
     var k=e.responseJSON;
     for(var j in k.errors)
      toastr.error(k.errors[j][0]);
    }
});
});

