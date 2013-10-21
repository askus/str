<div class="container-fluid">
  <div class="row-fluid">
    <h3>評分表 / 填寫評分表</h3>
  </div>

  <div class="alert alert-error default-hide">
    <button type="button" class="close">&times;</button>
    <span id="alert-msg"></span>
  </div>


  <form class="form-inline" action="questionnaire/ajax_update" id="template-update-form" method="post">
    <input type="hidden" name="questionnaire[questionnaire_id]" value="<?=$questionnaire->questionnaire_id ?>"></input>
    <input type="hidden" name="questionnaire[template_id]" value="<?= $questionnaire->template_id?> "></input>
    <input type="hidden" name="questionnaire[date]" value="<?= $questionnaire->date?>" ></input>
    <input type="hidden" name="questionnaire[target_department_id]" value="<?= $questionnaire->target_department_id?>"></input>
    <input type="hidden" name="questionnaire[last_modified_user_id]" value="<?= $last_modified_user_id?>"></input>
    <div>
      <p>
        <button type="button" id="add-btn" class="btn " data-loading-text="處理中..."> 暫時儲存</button>
        <button type="button" id="add-btn" class="btn btn-primary" data-loading-text="處理中..."> 完成</button>
      </p>


      <h4> <?= $questionnaire->year ?>年<?= $questionnaire->month ?>月份<?= $questionnaire->title ?> </h4>
      <p>負責人：<?= $questionnaire->assigned_user->name ?></p>
      <p>被稽核單位：<?= $questionnaire->target_department->department_name ?></p>
      <p>稽核人：<input type="text" name="questionnaire[executor]" value="<?= $questionnaire->executor?>" class="span2"></input></p>

      <ul>
      <?php $i=0; ?>
        <?php foreach( $questionnaire->sections as $section ): ?> 
          <input type="hidden" name="sections[section_id][]" value="<?= $section->section_id ?>">
          <input type="hidden" name="sections[section_title][]" value="<?= $section->section_title ?>">
          <input type="hidden" name="sections[section_order][]" value="<?= $section->section_order ?>">
          
          <li><strong><?= $section->section_title ?></strong></li>
          <table class="table">
          <tr><th>填答</th><th>內容</th><th>評分</th><th>評語</th></tr>
            
            <?php foreach( $section->questions as $question ) : ?>
                <tr>
                  <input type="hidden" name="questionnaire_score[question_id][]" value="<?= $question->question_id?>"></input>
                 
                  <td class="span1"><input row='<?= $i ?>' class="is_null_checkbox" type="checkbox" name="questionnaire_score[is_null][]" <?php if($question->score->is_null): ?> checkbox<?php endif;?> ></input></td>
                  <td class="span7"><p id='row-<?= $i ?>-question'><?= $question->question_title?></p></td>
                  <td class="span1"><?php if( $question->has_score): ?><input id="row-<?= $i ?>-score" type="text" class="span1" name="questionnaire_score[score][]" value="<?= $question->score->score ?>" readonly></input><?php endif; ?>  </td>
                  <td class="span4"><?php if( $question->has_comment): ?><textarea id="row-<?= $i ?>-comment" type="text" class="span4" rows="3" name="questionnaire_score[comment][]" value="<?= $question->score->comment?>" readonly></textarea> <?php endif; ?></td>
                  <input type="hidden" name="questionnaire_score[is_null][]" value='-1' >
                </tr>

            <?php $i++; endforeach; ?>
          </table>
        <?php endforeach; ?> 
      </ul>
      <p>
        <button type="button" id="add-btn" class="btn" data-loading-text="處理中..."> 暫時儲存</button>
        <button type="button" id="add-btn" class="btn btn-primary" data-loading-text="處理中..."> 完成</button>
      </p>
    </div>
  </form>

 </div>



<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>

<script>
  $(".is_null_checkbox").click( function(){
    var row = $(this).attr('row');
    var question = $('#row-'+row+'-question').html();
      if( $(this).prop("checked") ){
        $("#row-"+row+"-score").attr('readonly', null) ;
        $("#row-"+row+"-comment").attr('readonly', null);
      }else{
        if( confirm("\""+question+"\"\n\n 即將清除評分與評語，您確定要放棄填答嗎？") ){
          $("#row-"+row+"-score").attr('readonly', true) ; 
          $("#row-"+row+"-comment").attr('readonly', true) ;
          $("#row-"+row+"-score").val('') ;
          $("#row-"+row+"-comment").val('');
        }else{
          $(this).prop('checked', true );
        }
      }
    }
  );
</script>