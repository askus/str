

<div class="container-fluid">
  <div class="alert alert-error default-hide">
    <button type="button" class="close">&times;</button>
    <span id="alert-msg"></span>
  </div>
  <div class="row-fluid">
    <h3><a href="<?=base_url('template')?>">評分表管理</a> / 檢視評分表</h3>
  </div>
  <div class="row-fluid">
    <form action="<?= base_url('template')?>" method="post" class="form-inline">
      查詢年分：<?= year_menu( $years, $selected_year, 'selected_year' , 'span1', "selected-year") ?> <input class="btn btn-search" value="查詢" type="submit"></input>
    </form>
  </div>

  <?php foreach( $templates as $template ) : ?>
  	<div id="template-<?= $template->template_id ?>">
  		<h4>
        <a href="#" data-template-id="<?= $template->template_id ?>" id="template-<?=$template->template_id?>-title" class="template-dropdown" >
          <i id="template-<?= $template->template_id ?>-icon" class="icon-chevron-right"></i>
  	   		<?= $template->year ?>年<?= $template->month ?>月份<?= $template->title ?> 
        </a>
        (<?= show_template_completenness($template) ?>)
        <a href='<?= base_url("analysis/gen/{$template->template_id}") ?>' class="btn btn-mini btn-success"><i class="icon-circle-arrow-down icon-white"></i>下載報表</a>
        <a data-template-id="<?= $template->template_id ?>" data-template-title="<?=$template->year?>年<?=$template->month?>月份<?=$template->title?>"   class="template-del btn btn-mini btn-danger"> <i class="icon-trash icon-white"></i>刪除</a>
      </h4>
  		<table id="template-<?= $template->template_id?>-table" class="table" style="display:none;">
  		<tr><th class="span1">進度</th><th class="span1">負責人</th><th class="span2">受測單位</th><th>最近更新時間</th><th>修改人</th><th class="span4"></th></tr>
  		<?php foreach( $template->questionnaires as $questionnaire ): ?>
  			<tr id="questionnaire-<?= $questionnaire->questionnaire_id ?>">
  				<td> <?= status_menu( $questionnaire->status ) ?></td>
  				<td> <?= $questionnaire->assigned_user->name ?> </td>
  				<td> <?= $questionnaire->target_department->department_name ?></td>
          <td> <?php if( !is_null($questionnaire->last_modified_datetime) ): ?> <?= $questionnaire->last_modified_datetime ?> <?php endif;?> </td>
          <td> <?php if( !is_null( $questionnaire->last_modified_user ) ): ?> <?= $questionnaire->last_modified_user->name ?> <?php endif;?> </td>
  				<td> <a class="btn btn-mini btn-info" href="<?= base_url('questionnaire/view/'.$questionnaire->questionnaire_id ) ?>" >檢視</a> 
               <a href="<?= base_url('questionnaire/edit' ) ?>/<?= $questionnaire->questionnaire_id ?>" class="btn btn-mini btn-info btn-edit">填寫</a> 
               <a class="btn btn-mini btn-danger questionnaire-del" data-questionnaire-id="<?= $questionnaire->questionnaire_id ?>" data-questionnaire-title="<?=$questionnaire->assigned_user->name?>的<?=$template->year?>年<?=$template->month?>月份<?=$template->title?>" >刪除</a> 
          </td>	
 			</tr>	
  		<?php endforeach; ?>
      <tr id="template-<?= $template->template_id?>-new-assigned" class="info" style="display:none;" >
        <td colspan="6">
          稽核負責人：<?= user_menu( $users, "new_assigned_user_id", "new-assigned-user-id", "new-assigned-user-id-".$template->template_id ) ?>
          受測單位：<?= department_menu( $departments, "new_target_department_id" , "new-target-department-id", "new-target-department-id-".$template->template_id ) ?>
          <button name="add_new_questionnaire" class="btn btn-mini btn-primary btn-add-new-questionnaire" data-template-id="<?= $template->template_id ?>">新增</button>  
          <button name="reset" class="btn btn-mini btn-reset" data-template-id="<?= $template->template_id?>">取消</button>
        </td>
      </tr>
  		<tr id="template-<?= $template->template_id ?>-lastrow">
        <td colspan="6"><button class="btn btn-info btn-mini btn-to-add-questionnaire" data-template-id="<?= $template->template_id ?>"><i class="icon-plus icon-white"></i>新增負責人</button></td>
      </tr>
      </table>
  	</div>
  <?php endforeach; ?>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<script>
  var selected_year = <?= $selected_year ?>;
  var selected_template_id = <?= $selected_template_id ?>;
  
  function add_labor_division( template_id, assigned_user_id, target_department_id  ){
    $.post( "<?= base_url('questionnaire/ajax_add') ?>", 
      {template_id: template_id, assigned_user_id: assigned_user_id, target_department_id: target_department_id },
      function( data){
        var res= $.parseJSON(data );
        if( res.status =="ok"){
          alert('新增成功');
          location.href = "<?=base_url('template/index')?>"+"/"+selected_year+"/"+template_id;
        }else{
          alert('新增失敗');
        }
     });
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
  function drop_down_template( template_id ){
    $("#template-"+template_id+"-table").fadeIn(250); 
    $("#template-"+template_id+"-icon").attr('class','icon-chevron-down');
  }
  function drop_up_template( template_id ){
    $("#template-"+template_id+"-table").css('display', "none" ); 
    $("#template-"+template_id+"-icon").attr('class','icon-chevron-right');
  }

  $(function() {


    drop_down_template( selected_template_id );

    $("input.btn-search").click( function( e) {
      e.preventDefault();
      selected_year = $("#selected-year").val();
      location.href= "<?= base_url('template/index')?>/"+selected_year+"/"+selected_template_id;
    });

    $("button.btn-add-new-questionnaire").click( function( e){
      var template_id = $(this).attr('data-template-id');
      var new_assigned_user_id = $("#new-assigned-user-id-"+template_id ).val();
      var target_department_id = $("#new-target-department-id-"+template_id).val();
      if( new_assigned_user_id <= 0 ){
        alert('請選擇負責人');
        return ; 
      }else if( target_department_id <= 0 ){
        alert('請選擇受測單位');
        return ;
      }
      add_labor_division( template_id, new_assigned_user_id, target_department_id  );
    });

    $("button.btn-reset").click( function(e){
      var template_id = $(this).attr('data-template-id');
      $("#template-"+template_id+"-new-assigned").hide();
      $("#template-"+template_id+"-lastrow").show();
    });

    $("button.btn-to-add-questionnaire").click( function(e){
      var template_id = $(this).attr('data-template-id');
      $("#template-"+template_id+"-new-assigned").fadeIn(250);
      $("#template-"+template_id+"-lastrow").hide();
    });

    $("a.template-dropdown").click( function(e){
      e.preventDefault();
      var template_id = $(this).attr('data-template-id');
      //console.log( $("#template-"+template_id+"-table").css('display') ) ;
      if( $("#template-"+template_id+"-table").css('display') == "none" ){
        drop_down_template( template_id );
      }else{
        drop_up_template( template_id);
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