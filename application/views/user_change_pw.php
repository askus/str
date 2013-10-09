<div class="container-fluid">
  <div class="row-fluid">
    <h3>更改密碼</h3>
  </div>

  <div class="row-fluid">
    <div id="user-add-block">
      <div class="alert alert-error default-hide">
        <button type="button" class="close">&times;</button>
        <span id="alert-msg"></span>
      </div>

      <form id="user-add-form" class="form-horizontal" method="post" action="<?=base_url('user/ajax_change_password')?>">
        <div class="control-group">
          <label class="control-label" for="inputAccount">*帳號名稱</label>
          <div class="controls">
            <input type="text" id="inputAccount" name="user[account]" value="<?=$user->account?>" readonly>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="inputName">*使用者姓名</label>
          <div class="controls">
            <input type="text" id="inputName" name="user[name]" value="<?=$user->name?>">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="inputEmail">電子信箱</label>
          <div class="controls">
            <input type="text" id="inputEmail" name="user[email]" value="<?=$user->email?>">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="inputDep">*所屬單位</label>
          <div class="controls">
            <select id="inputDep" name="user[department_id]" readonly>
              <option value="<?=$user->department_id?>" selected><?=$user->department_name?></option>
            </select>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="inputPassword">*變更密碼</label>
          <div class="controls">
            <input type="password" id="inputPassword">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="inputRePassword">*確認密碼</label>
          <div class="controls">
            <input type="password" id="inputRePassword" name="user[password]">
          </div>
        </div>
        <div class="form-actions" style="background:none">
          <input type="hidden" name="user[user_id]" value="<?=$user->user_id?>">
          <button type="submit" id="add-btn" class="btn btn-primary" data-loading-text="處理中...">儲存變更</button>&nbsp;
          <button type="button" id="cancel-btn" class="btn">取消返回</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
$(function() {
  $('.alert .close').on('click', function() {
    $(this).parent().hide();
  });

  $(document).ajaxStart(function() {
    $('#add-btn').button('loading');
  }).ajaxStop(function() {
    $('#add-btn').button('reset');
  });

  $('#cancel-btn').click(function() {
    history.go(-1);
  });

  $(document).on('submit', '#user-add-form', function(e) {
    var errMsg = '';
    var account = $.trim($('#inputAccount').val());
    var name = $.trim($('#inputName').val());
    var email = $.trim($('#inputEmail').val());
    var password = $('#inputPassword').val();
    var repassword = $('#inputRePassword').val();

    if (account == '') errMsg += '● 帳號名稱不可為空白<br>';
    if (name == '') errMsg += '● 使用者名稱不可為空白<br>';
    if (email.length > 0) {
      var regExp = /^[^@^\s]+@[^\.@^\s]+(\.[^\.@^\s]+)+$/;
      if (!email.match(regExp)) errMsg += '● 電子信箱格式不正確<br>';
    }
    <?php if (!isset($user)) : ?> 
    if ((password.length == 0) || (repassword.length == 0)) errMsg += '● 登入密碼不可為空白<br>';
    <?php endif; ?>
    if (password != repassword) errMsg += '● 兩次密碼輸入不相同';

    if (errMsg.length > 0) {
      $('#alert-msg').html(errMsg);
      $('.alert').hide().fadeIn(50);

    } else {
      $.post($(this).attr('action'), $(this).serialize(), function(res) {
        if (res == 'ok') {
          alert('變更已儲存');
          $('#inputPassword').val('');
          $('#inputRePassword').val('');
        } else if (res == 'exist') {
          $('#alert-msg').html('● 帳號名稱已經存在');
          $('.alert').hide().fadeIn(50);
        } else {
          $('#alert-msg').html('● 新增使用者發生錯誤');
          $('.alert').hide().fadeIn(50);
        }
      });
    }
    e.preventDefault();
  });
});
</script>
