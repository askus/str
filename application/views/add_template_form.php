<div class="container-fluid">

  <div class="alert alert-error default-hide">
    <button type="button" class="close">&times;</button>
    <span id="alert-msg"></span>
  </div>


  <div class="row-fluid">
    <h3><a href="<?=base_url('user')?>">評分表管理</a> / <?=$action?>評分表</h3>
  </div>
  <form class="form-inline" action="template/ajax_add" method="post">
  <button type="submit" id="add-btn" class="btn btn-primary" data-loading-text="處理中..."> 儲存變更</button>
  <h4>● 基本資料</h4>
    <input name="template[template_id]" type="hidden" value="<?=$template->template_id?>" ></input>
  	<div>
  	<label class="control-label" for="inputTitle">標題
  		<input id="inputTitle" name="template[title]" type="text"  class="span5" value="<?=(isset($template))?$template->title:'服務稽核評分表' ?>"></input>
	</label>
	</div>
  	<div>
  	<label class="control-label" for="inputYear">年份
  		<select name="template[year]" id="inputYear" class="span2">
  			<?php foreach( next_k_year(5) as $y ) :  ?>
  				<option <?php if( isset($template) && $y == $template->year ):?>selected<?php endif;?> ><?= $y?></option>
  			<?php  endforeach; ?>
  		</select>
  	</label>
  	</div>
  	<div>
  	<label class="control-label" for="inputMonth">月份
  		<select name="template[month]" id="inputMonth" class="span2">
  			<?php foreach( months() as $m ) : ?>
  				<option <?php if( isset($template) && $m == $template->month ):?>selected<?php endif;?> ><?= $m?></option>
  			<?php endforeach; ?>
  		</select>
  	</label>
  	</div>
  <h4>● 工作分配</h4>
  <table class="table">
  <tr><th>稽核人員</th><th>被稽核單位</th></tr>
  <?php foreach( $users as $u ) : ?> 
  <tr>
    <td><?= $u->name?>-<span class="muted"><?= $u->department_name ?></span><input type="hidden" name="labor_division[assigned_user_ids][]" value="<?= $u->user_id?>"></input></td>
    <td><?= department_menu( $departments, "labor_division[target_department_ids][]", 0 ) ?></td>
  </tr>
  <?php endforeach; ?>
  </tr>
  </table>
  <h4>● 評分表內容</h4>
  <div>
    <ul>
      <?php foreach( $template->sections as $section ): ?> 
        <input type="hidden" name="sections[section_id][]" value="<?= $section->section_id ?>">
        <input type="hidden" name="sections[section_title][]" value="<?= $section->section_title ?>">
        <input type="hidden" name="sections[section_order][]" value="<?= $section->section_order ?>">

        <li><strong><?= $section->section_title ?></strong></li>
        <table class="table">
        <tr><th>順序</th><th>內容</th><th>評分</th><th>評語</th></tr>
          <?php foreach( $section->questions as $question ) : ?>
              <tr>
                <input type="hidden" name="questions[question_id][]" value="<?= $question->question_id?>"></input>
                <input type="hidden" name="questions[section_id][]" value="<?= $section->section_id?>"></input>
                <input type="hidden" name="questions[section_order][]" value="<?= $section->section_order?>"></input>
                <td class="span1"><input type="text" name="questions[question_order][]" value="<?= $question->question_order?>" class="span1" readonly> </input></td>
                <td class="span9"><input type="text" name="questions[question_title][]" value="<?= $question->question_title?>" class="span8" readonly></input> </td>
                <td class="span1"><input type="checkbox" name="questions[has_score][]" value=1 <?php if( $question->has_score): ?>checked<?php endif; ?> readonly></input><input type="hidden" name="questions[has_score][]" value=-1 > </td>
                <td class="span1"><input type="checkbox" name="questions[has_comment][]" value=1 <?php if( $question->has_comment): ?>checked <?php endif;?> readonly><input type="hidden" name="questions[has_comment][]" value=-1 ></input></td>
              </tr>
          <?php endforeach; ?>
        </table>
      <?php endforeach; ?> 
    </ul>
  </div>
  </form>
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