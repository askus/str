<div class="container-fluser_id">
  <div class="row-fluser_id">
    <div class="pull-right" style="padding-top:16px">
      <button type="button" id="add-user-btn" class="btn"><i class="icon-pencil"></i> 新增帳號</button>
    </div>
    <h3>帳號管理</h3>
  </div>

  <div class="row-fluser_id">
    <table id="user-list-table" class="table table-bordered table-striped table-hover">
      <thead>
        <tr>
          <th class="span2">使用者編號</th>
          <th>帳號名稱</th>
          <th>使用者姓名</th>
          <th>所屬單位</th>
          <th>電子信箱</th>
          <th>權限身分</th>
          <th class="action-column">功能</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user) : ?>
        <tr id="row-<?=$user->user_id?>">
          <td><?=$user->user_id?></td>
          <td><?=$user->account?></td>
          <td><?=$user->name?></td>
          <td><?=$user->department_name?></td>
          <td><?=(!empty($user->email))?$user->email:'無'?></td>
          <td><?=$user->role_name?></td>
          <td class="action-column">
            <button class="btn btn-mini btn-info edit-btn" type="button" data-user-id="<?=$user->user_id?>">修改</button>
            <button class="btn btn-mini btn-danger del-btn" type="button" data-user-id="<?=$user->user_id?>">刪除</button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
  $(function() {
    $('#add-user-btn').click(function() {
      location.href = "<?=base_url('user/add')?>";
    });

    $('#user-list-table .edit-btn').click(function() {
      var userID = $(this).attr('data-user-id');
      var uri = 'user/edit/' + userID;
      location.href = "<?=base_url()?>" + uri;
    });

    $('#user-list-table .del-btn').click(function() {
      if (confirm('確定要刪除這位使用者嗎？')) {
        var userID = $(this).attr('data-user-id');
        del(userID);
      }
    });
  });

  function del(user_id) {
    $.post("<?=base_url('user/ajax_delete_user')?>", { userID: user_id }, function(res) {
      if (res == 'ok') {
        $('#row-'+user_id).fadeOut(250, function() {
          $(this).remove();
        });
      } else {
        alert('刪除使用者發生錯誤');
      }
    });
  }
</script>
