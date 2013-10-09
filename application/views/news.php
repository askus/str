<div class="container-fluid">
  <div class="row-fluid">
    <?php if ( $is_addable) : ?>
    <div class="pull-right" style="padding-top:16px">
      <button type="button" id="news-add-btn" class="btn"><i class="icon-pencil"></i> 新增公告</button>
    </div>
    <?php endif; ?>
    <h3>系統公告</h3>
  </div>

  <?php //if ($claim == true) : ?>
  <!--
  <div class="row-fluid">
    <div class="alert alert-error">
      仍有報表尚未填寫完成，請於每月15日前填寫完畢。
    </div>
  </div>
  -->
  <?php //endif; ?>

  <div id="news-add-block" class="row-fluid text-center default-hide">
    <form method="post" action="<?=base_url('news/ajax_add_news')?>">
      <label>公告訊息內容</label>
      <textarea rows="5" class="span6" name="inputContent"></textarea>
      <div>
        <input type="hidden" id="nid" name="newsID">
        <button type="submit" id="save-btn" class="btn btn-primary" data-loading-text="處理中...">儲存</button>&nbsp;
        <button type="button" id="cancel-btn" class="btn">取消</button>
      </div>
    </form>
  </div>

  <div class="row-fluid clearfix">
    <table id="news-table" class="table table-bordered table-striped table-hover">
      <thead>
        <tr>
          <th class="span2 text-center">公告日期</th>
          <th class="text-center">訊息內容</th>
          <?php if ( $is_addable) : ?>
          <th class="action-column">功能</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php if (count($news) > 0) : foreach ($news as $n) : ?>
        <tr id="row-<?=$n->news_id?>">
          <td class="text-center"><?=date_to_chinese($n->date)?></td>
          <td><?=nl2br($n->content)?></td>
          <?php if ( $is_addable ) : ?>
          <td class="action-column">
            <button type="button" class="btn btn-mini btn-info edit-btn" data-news-id="<?=$n->news_id?>">編輯</button>
            <button type="button" class="btn btn-mini btn-danger del-btn" data-news-id="<?=$n->news_id?>">刪除</button>
          </td> 
          <?php endif; ?>
        </tr>
        <?php endforeach; else : ?>
        <tr>
          <td colspan="3" class="text-center">目前沒有任何公告</td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
$(function() {
  $('#news-add-btn').click(showAddForm);
  $('#cancel-btn').click(function() { showAddForm('close'); });

  // 編輯公告
  $('#news-table .edit-btn').click(function() {
    var nid = $(this).attr('data-news-id');

    $.post("<?=base_url('news/ajax_get_news')?>", { newsID: nid }, function(res) {
      if (res.status == 'ok') {
        $('#news-add-block textarea').val(res.content);
        $('#nid').val(res.nid);
        window.scrollTo(0, 0);
        showAddForm();
      } else {
        alert('取得公告發生錯誤');
      }
    }, 'json');
  });


  // 刪除公告
  $('#news-table .del-btn').click(function() {
    if (confirm('確定要刪除此筆公告嗎？')) {
      var nid = $(this).attr('data-news-id');

      $.post("<?=base_url('news/ajax_delete_news')?>", { newsID: nid }, function(res) {
        if (res == 'ok') {
          $('#row-'+nid).fadeOut(250, function() {
            $(this).remove();
          });
        } else {
          alert('刪除公告發生錯誤');
        }
      });
    }
  });

  // 儲存變更
  $(document).on('submit', '#news-add-block form', function(e) {
    var nid = $('#nid').val();
    var content = $('#news-add-block textarea').val();

    if ($.trim(content) == '') {
      alert('請輸入公告訊息內容');
    } else {
      $.post($(this).attr('action'), $(this).serialize(), function(res) {
        if (res == 'ok') {
          location.reload();
        } else {
          alert('儲存公告發生錯誤');
        }
      });
    }

    e.preventDefault();
  });

  // Ajax 提示
  $(document).ajaxStart(function() {
    $('#save-btn').button('loading');
  }).ajaxStop(function() {
    $('#save-btn').button('reset');
  });
});

// 顯示表單
function showAddForm(act) {
  if (act == 'close') {
    $('#news-add-block').slideUp(250);
    $('#news-add-block textarea').val('');
    $('#nid').removeAttr('value');
  } else {
    $('#news-add-block').slideDown(250);
  }
}
</script>
