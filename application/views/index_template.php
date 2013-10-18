

<div class="container-fluid">

  <div class="alert alert-error default-hide">
    <button type="button" class="close">&times;</button>
    <span id="alert-msg"></span>
  </div>
  <div class="row-fluid">
    <h3><a href="<?=base_url('template')?>">評分表管理</a> / 檢視評分表</h3>
  </div>
  <?php foreach( $templates as $template ) : ?>
  	<div id="template-<?= $template->template_id ?>">
  		<h4>
  			<i class="icon-chevron-down"></i>
  			<?= $template->year ?>年<?= $template->month ?>月份<?= $template->title ?> 
  			<a data-template-id="<?= $template->template_id ?>" data-template-title="<?=$template->year?>年<?=$template->month?>月份<?=$template->title?>"   class="template-del btn btn-mini btn-danger"> <i class="icon-trash icon-white"></i></a>
  		</h4>
  		
  		<ul>
  		<table class="table">
  		<tr><th class="span1">進度</th><th class="span1">負責人</th><th class="span2">被稽核單位</th><th class="span4"></th></tr>
  		<?php foreach( $template->questionnaires as $questionnaire ): ?>
  			<tr>
  				<td> <?= status_menu( $questionnaire->status ) ?></td>
  				<td> <?= $questionnaire->assigned_user->name ?> </td>
  				<td> <?= $questionnaire->target_department->department_name ?></td>
  				<td> <a href="#" class="btn btn-mini btn-info">檢視</a> <a href="<?= base_url('questionnaire/edit' ) ?>/<?= $questionnaire->questionnaire_id ?>" class="btn btn-mini btn-info btn-edit">編輯</a> <a class="btn btn-mini btn-danger btn-del" >刪除</a> </td>	
 			</tr>	
  		<?php endforeach; ?>
  		</table>
  		</ul>
  	</div>
  <?php endforeach; ?>

</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<script>
  function del( template_id ){


    $.post("<?=base_url('template/ajax_delete')?>", { template_id: template_id }, function(data ) {
      var res = $.parseJSON( data );

      if (res.status == 'ok') {
        $('#template-'+template_id).fadeOut(250, function() {
          $(this).remove();
        });
      } else {
        alert('刪除評分表發生錯誤');
      }
    });
  }


  $(function() {
    $("a.template-del").click( function(){
        var template_id = $(this).attr('data-template-id');
        var template_title = $(this).attr('data-template-title');
        if(confirm('要刪除'+template_title+'嗎？')) {
          del( template_id );
          return false; 
        } 
      }
    );
  });

</script>