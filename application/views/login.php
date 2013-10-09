<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <meta charset="utf-8">
  <title>臺中市政府社會局服務稽核評分系統</title>
  <link href="css/bootstrap.min.css" rel="stylesheet">
  <link href="css/login.css" media="screen, projection" rel="stylesheet">
  <!--[if lt IE 8]><link href="css/ie.css" media="screen, projection" rel="stylesheet"><![endif]-->
  <!--[if lt IE 7]><script>alert('請升級您的瀏覽器至IE7以上版本，以確保系統正常運作。');</script><![endif]-->
</head>

<body>
  <div class="container">
    <form id="login-form"  class="form-horizontal" method="post" action='<?=base_url("login/ajax_check_login")?>'>
      <fieldset>
        <legend><strong>臺中市政府社會局服務稽核評分系統</strong></legend>
        <div class="alert alert-error">
          <button type="button" class="close">&times;</button>
          <strong>錯誤!</strong> <span id="alert-msg"></span>
        </div>

        <div class="control-group">
          <label class="control-label" for="inputUsername">帳號</label>
          <div class="controls">
            <div class="input-prepend">
              <span class="add-on"><i class="icon-user"></i></span>
              <input class="span3" name="inputUsername" type="text" placeholder="請輸入帳號" tabindex="1">
            </div>
          </div>
        </div>

        <div class="control-group">
          <label class="control-label" for="inputPassword">密碼</label>
          <div class="controls">
            <div class="input-prepend">
              <span class="add-on"><i class="icon-lock"></i></span>
              <input class="password span3" name="inputPassword" type="password" placeholder="請輸入密碼" tabindex="2">
            </div>
          </div>
        </div>

        <div class="control-group">
          <div class="btn-right">
            <a href="javascript:void(0)" class="forgetPassword">忘記密碼？</a>　
            <button type="submit" class="btn btn-primary" data-loading-text="登入中..." tabindex="3">登入</button>
          </div>
        </div>
      </fieldset>
    </form>
  </div>

  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.placeholder.min.js"></script>
  <script>
    $(function() {
      $(document).on('submit', '#login-form', function(e) {
        $.post($(this).attr('action'), $(this).serialize(), function(res) {
          if (res.status == 'ok') {
            location.href = '<?=base_url()?>';
          } else {
            $('#alert-msg').text(res.message);
            $('.alert').hide().fadeIn(50);
          }
        }, 'json');

        e.preventDefault();
      });

      $('.alert .close').on('click', function() {
        $(this).parent().hide();
      });

      $('input').placeholder();

      $(document).ajaxStart(function() {
        $('.btn').button('loading');
      }).ajaxStop(function() {
        $('.btn').button('reset');
      });

      $('.forgetPassword').click(function() {
        alert('請聯絡系統管理員重設密碼\n\n綜合企劃科\n朱先生\n分機 37108');
      });
    });
  </script>
</body>
</html>