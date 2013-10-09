<div class="container-fluid">
  <div class="row-fluid">
    <h3><a href="<?=base_url('user')?>">評分表管理</a> / <?=$action?>評分表</h3>
  </div>
  <form class="form-inline" action="<?=base_url('template/ajax_create') ?>">
  <h4>● 基本資料</h4>
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
  <h4>● 需填表者</h4>
  <?php foreach( $users as $u ) : ?> 
  	<div>
 	<label class="checkbox">
 		<input type="checkbox" name="template[users][]" value="<?=$u->user_id?>"> <?= $u->name ?>-<span class="muted"><?= $u->department_name?></span>
  	</label>
  	</div>
  <?php endforeach; ?>
  <h4>● 評分表內容</h4>

  </form>
</div>