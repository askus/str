<div class="container-fluid">
  <div class="row-fluid">
    <h3>評分表管理 / 檢視本科室評分表</h3>
  </div>
  <table class="table">
  <tr><th>科室</th><th>年份</th><th>月份</th><th>標題</th><th></th></tr>
  <?php foreach( $questionnaires as $questionnaire ): ?>
  	<tr>
  		<td><?= $department->department_name ?></td>
	  	<td><?= $questionnaire->year ?></td>
	  	<td><?= $questionnaire->month ?></td>
	  	<td><?= $questionnaire->title ?></td>
	  	<td>
	  		<a href="<?= base_url('questionnaire/view_encrypted/'.$questionnaire->questionnaire_id)?>" class="btn btn-mini btn-info">檢視</a>
	  		
	  	<td>
  	</tr>
  <?php endforeach;?>
  </table>



</div>