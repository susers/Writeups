$(function () {
   $('button[type=submit]').click(function () {
       var username = $('#username').val();
       var password = $('#password').val();
       var flag = 1;
       if (username.length < 1) {
           $('#usernameError').removeAttr('style');
           flag = 0;
       }
       if (password.length < 1) {
           $('#passwordError').removeAttr('style');
           flag = 0;
       }
       if (!flag) return;
       $.post("/loginCheck.php", {username: username, password: password}, function (data, status) {
           if (data === '登录成功') {
               window.location.href = './index.php';

           } else {
               var i = $('<span class="notice">'+data+'</span>');
               var x = $(document).width()/2-250;
               var y = 100;
               i.css({
                   top: y,
                   left: x,
               });
               $('body').append(i);
               i.animate({
                   top:y-100,
                   left:x,
               }, 1500, function () {
                   i.remove();
               })
           }
       });
    });
});