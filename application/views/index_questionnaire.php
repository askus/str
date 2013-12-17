<div class="container-fluid">

  <div class="alert alert-error default-hide">
    <button type="button" class="close">&times;</button>
    <span id="alert-msg"></span>
  </div>
  <div class="row-fluid">
    <h3><a href="<?=base_url('template')?>">評分表管理</a> / 填寫評分表</h3>
  </div>
  <table class="table">
  <tr><th>進度</th><th>年份</th><th>月份</th><th>標題</th><th></th></tr>
  <?php foreach( $questionnaires as $questionnaire ): ?>
  	<tr>
	  	<td> <?= status_menu( $questionnaire->status ) ?></td>
	  	<td><?= $questionnaire->year ?></td>
	  	<td><?= $questionnaire->month ?></td>
	  	<td><?= $questionnaire->title ?></td>
	  	<td>
	  		<a href="<?= base_url('questionnaire/view/'.$questionnaire->questionnaire_id)?>" class="btn btn-mini btn-info">檢視</a>
	  		<a href="<?= base_url('questionnaire/edit/'.$questionnaire->questionnaire_id)?>" class="btn btn-mini btn-info">填寫</a>
	  	<td>
  	</tr>
  <?php endforeach;?>
  </table>
</div>