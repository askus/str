<div class="container-fluid">
  <div class="row-fluid">
    <h3><a href="<?=base_url('user')?>">帳號管理</a> / <?=$action?>帳號</h3>
  </div>

  <div class="row-fluid">
    <div id="user-add-block">
      <div class="alert alert-error default-hide">
        <button type="button" class="close">&times;</button>
        <span id="alert-msg"></span>
      </div>

      <form id="user-add-form" class="form-horizontal" method="post" action="<?=base_url('user/ajax_add_user')?>">
        <div class="control-group">
          <label class="control-label" for="inputAccount">*帳號名稱</label>
          <div class="controls">
            <input type="text" id="inputAccount" name="user[account]" value="<?=(isset($user))?$user->account:''?>"<?php if(isset($user))echo' readonly'?>>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="inputName">*使用者姓名</label>
          <div class="controls">
            <input type="text" id="inputName" name="user[name]" value="<?=(isset($user))?$user->name:''?>">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="inputEmail">電子信箱</label>
          <div class="controls">
            <input type="text" id="inputEmail" name="user[email]" value="<?=(isset($user))?$user->email:''?>">
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="inputDep">*所屬單位</label>
          <div class="controls">
            <select id="inputDep" name="user[department_id]">
              <?php foreach ($departments as $dep) : if (isset($user) && ($user->department_id == $dep->department_id)) : ?>
              <option value="<?=$dep->department_id?>" selected><?=$dep->department_name?></option>
              <?php else : ?>
              <option value="<?=$dep->department_id?>"><?=$dep->department_name?></option>
              <?php endif; endforeach; ?>
            </select>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="inputRole">*管理權限</label>
          <div class="controls">
            <select id="inputRole" name="user[role_id]">
              <?php foreach ($roles as $role) : if (isset($user) && ($user->role_id == $role->role_id)) : ?>
              <option value="<?=$role->role_id?>" selected><?=$role->role_name?></option>
              <?php else : ?>
              <option value="<?=$role->role_id?>"><?=$role->role_name?></option>
              <?php endif; endforeach; ?>
            </select>
          </div>
        </div>
        <div class="control-group">
          <label class="control-label" for="inputPassword">*<?=(isset($user))?'變更密碼':'登入密碼'?></label>
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
          <input type="hidden" name="user[user_id]" value="<?=(isset($user))?$user->user_id:''?>">
          <button type="submit" id="add-btn" class="btn btn-primary" data-loading-text="處理中...">確定<?=$action?></button>&nbsp;
          <button type="button" id="cancel-btn" class="btn">取消<?=$action?></button>
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
    location.href = "<?=base_url('user')?>";
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
          location.href = "<?=base_url('user')?>";
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
