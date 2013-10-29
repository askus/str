

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
        <a href="#" data-template-id="<?= $template->template_id ?>" class="template-dropdown" >
    			<i id="temlpate-<?= $template->template_id ?>-icon" class="icon-chevron-right"></i>
  	   		<?= $template->year ?>年<?= $template->month ?>月份<?= $template->title ?> 
        </a>
  			<a data-template-id="<?= $template->template_id ?>" data-template-title="<?=$template->year?>年<?=$template->month?>月份<?=$template->title?>"   class="template-del btn btn-mini btn-danger"> <i class="icon-trash icon-white"></i></a>
  		</h4>
  		<table id="template-<?= $template->template_id?>-table" class="table" style="display:none;">
  		<tr><th class="span1">進度</th><th class="span1">負責人</th><th class="span2">被稽核單位</th><th>最近更新時間</th><th>修改人</th><th class="span4"></th></tr>
  		<?php foreach( $template->questionnaires as $questionnaire ): ?>
  			<tr id="questionnaire-<?= $questionnaire->questionnaire_id ?>">
  				<td> <?= status_menu( $questionnaire->status ) ?></td>
  				<td> <?= $questionnaire->assigned_user->name ?> </td>
  				<td> <?= $questionnaire->target_department->department_name ?></td>
          <td> <?php if( !is_null($questionnaire->last_modified_datetime) ): ?> <?= $questionnaire->last_modified_datetime ?> <?php endif;?> </td>
          <td> <?php if( !is_null( $questionnaire->last_modified_user ) ): ?> <?= $questionnaire->last_modified_user->name ?> <?php endif;?> </td>
  				<td> <a class="btn btn-mini btn-info" href="<?= base_url('questionnaire/view/'.$questionnaire->questionnaire_id ) ?>" >檢視</a> 
               <a href="<?= base_url('questionnaire/edit' ) ?>/<?= $questionnaire->questionnaire_id ?>" class="btn btn-mini btn-info btn-edit">編輯</a> 
               <a class="btn btn-mini btn-danger questionnaire-del" data-questionnaire-id="<?= $questionnaire->questionnaire_id ?>" data-questionnaire-title="<?=$questionnaire->assigned_user->name?>的<?=$template->year?>年<?=$template->month?>月份<?=$template->title?>" >刪除</a> 
          </td>	
 			</tr>	
  		<?php endforeach; ?>
  		<tr id="template-<?= $template->template_id ?>-lastrow">
        <td colspan="6"><a href="#" class="btn btn-info btn-mini btn-add-questionnaire" data-template-id="<?= $template->template_id ?>"><i class="icon-plus icon-white"></i>新增負責人</a></td>
      </tr>
      </table>
  	</div>
  <?php endforeach; ?>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<script>
  function show_new_questionnaire( template_id ){
    var html="";
  }

  function del_questionnaire( questionnaire_id ){
    $.post("<?= base_url('questionnaire/ajax_delete')?>", {questionnaire_id: questionnaire_id}, function(data){
      var res = $.parseJSON( data );
      if( res.status == 'ok'){
        $('#questionnaire-'+questionnaire_id).fadeOut(250, function() {
          $(this).remove();
        });
      }else{
        alert('刪除評分表發生錯誤');
      }
    });
  }

  function del( template_id ){
    $.post("<?=base_url('template/ajax_delete')?>", { template_id: template_id }, function(data ) {
      var res = $.parseJSON( data );
      if(res.status == 'ok'){
        $('#template-'+template_id).fadeOut(250, function() {
          $(this).remove();
        });
      }else{
        alert('刪除評分表發生錯誤');
      }
    });
  }


  $(function() {
    $("a.btn-add-questionnaire").click( function(e){
      e.preventDefault();
      var template_id = $(this).attr('data-template-id');

    });

    $("a.template-dropdown").click( function(e){
      e.preventDefault();
      var template_id = $(this).attr('data-template-id');

      //console.log( $("#template-"+template_id+"-table").css('display') ) ;
      if( $("#template-"+template_id+"-table").css('display') == "none" ){
         $("#template-"+template_id+"-table").fadeIn(250); 
         $("#temlpate-"+template_id+"-icon").attr('class','icon-chevron-down');
      }else{
         $("#template-"+template_id+"-table").css('display', "none" ); 
         $("#temlpate-"+template_id+"-icon").attr('class','icon-chevron-right');
      }
    });

    $("a.questionnaire-del").click( function(){
      var questionnaire_id = $(this).attr('data-questionnaire-id');
      var questionnaire_title = $(this).attr('data-questionnaire-title');
      if( confirm('要刪除\"'+questionnaire_title+'\"嗎？')){
        del_questionnaire( questionnaire_id );
      }
    });

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