<div class="container-fluid">
  <div class="row-fluid">
    <h3>評分表 / 填寫評分表</h3>
  </div>

  <div class="alert alert-error default-hide">
    <button type="button" class="close">&times;</button>
    <span id="alert-msg"></span>
  </div>


  <form class="form-inline" action="questionnaire/complete" id="questionnaire-update-form" method="post">
    <input type="hidden" name="questionnaire[questionnaire_id]" value="<?=$questionnaire->questionnaire_id ?>"></input>
    <input type="hidden" name="questionnaire[template_id]" value="<?= $questionnaire->template_id?> "></input>
    <input type="hidden" name="questionnaire[target_department_id]" value="<?= $questionnaire->target_department_id?>"></input>
    <input type="hidden" name="questionnaire[last_modified_user_id]" value="<?= $last_modified_user_id?>"></input>
    <div>
      <p>
        <button type="button"  class="btn tmp-save-btn" data-loading-text="處理中..."> 暫時儲存</button>
        <input type="submit" class="btn btn-primary cmlp-btn" data-loading-text="處理中..." value="完成"></input>
      </p>


      <h4> <?= $questionnaire->year ?>年<?= $questionnaire->month ?>月份<?= $questionnaire->title ?> </h4>
      <ul>
      <li><p><strong>負責人</strong>：<?= $questionnaire->assigned_user->name ?></p></li>
      <li><p><strong>被稽核單位</strong>：<?= $questionnaire->target_department->department_name ?></p></li>
      <li><p><strong>訪員姓名</strong>：<input type="text" name="questionnaire[executor]" id="questionnaire-executor" value="<?= $questionnaire->executor?>" class="span2"></input> </p></li>
      <li> <p>
       <strong>稽核時間</strong>：
        <span  class= "datetimepicker1 input-append date">
         <input id="questionnaire-from-date" data-format="yyyy-MM-dd hh:mm" name="questionnaire[from_date]" type="text" value="<?= trim_sec( $questionnaire->from_date) ?>"></input>
          <span class="add-on">
            <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
          </span>
        </span>
        ～
        <span class="datetimepicker1 input-append date">
         <input id="questionnaire-to-date" data-format="yyyy-MM-dd hh:mm" name="questionnaire[to_date]" type="text" value="<?= trim_sec( $questionnaire->to_date ) ?>"></input>
          <span class="add-on">
            <i data-time-icon="icon-time" data-date-icon="icon-calendar"></i>
          </span>
        </span>
        </p>
      </li>
      </ul>
      <ul>
      <?php $i=0; ?>
        <?php foreach( $questionnaire->sections as $section ): ?> 
          <li><strong><?= $section->section_title ?></strong></li>
          <table class="table">
          <?php if( !$section->is_all_comment ) { ?>
            <tr><th></th><th>編號</th><th>問題</th><th>評語</th></tr>
              <?php  $q_od = 1; ?> 
              <?php foreach( $section->questions as $question ) : ?>
                  <tr id="row-<?= $i ?>-tr" class="<?= score2class( $question->score)?>">
                    <input type="hidden" name="questionnaire_score[question_id][]" value="<?= $question->question_id?>"></input>
                    
                    <td class="span1">  <?= null_true_false('questionnaire_score[true_false_null]', $question->score, $i ) ?></td>
                    <td id="row-<?= $i ?>-serial"><?= $q_od++ ?> </td>
                    <td class="span7"><p id='row-<?= $i ?>-question'><?= $question->question_title?></p></td>
                    <td class="span4"><?php if( $question->has_comment){ ?>
                                        <textarea id="row-<?= $i ?>-comment" type="text" class="span4" rows="3" name="questionnaire_score[comment][]"<?php if($question->score->is_null): ?>  readonly <?php endif;?> ><?= $question->score->comment?></textarea>
                                        <?php }else{ ?>
                                          <input type="hidden" name="questionnaire_score[comment][]" value="-1">
                                        <?php } ?>
                    </td>
                    <input type="hidden" name="questionnaire_score[is_not_null][]" value='-1' >
                  </tr>
              <?php $i++; endforeach; ?>
            
            <?php } else { ?>
              <tr><th></th><th></th></tr>
              <?php foreach( $section->questions as $question ): ?>
                <tr>
                  <input type="hidden" name="questionnaire_score[question_id][]" value="<?= $question->question_id?>"></input>
                  <input type="hidden" name="questionnaire_score[true_false_null][]" value="1"></input>
                  <ipnut type="hidden" name="questionnaire_score[is_null][]" value="1"></ipnut> 
                  <input type="hidden" name="questionnaire_score[score][]" value= "0"></input>
                  <td class="span1"><p><?= $question->question_title?></p></td>
                  <td><textarea id="row-<?= $i ?>-comment" type="text" class="span6" rows="3" name="questionnaire_score[comment][]" ><?= $question->score->comment?></textarea>
</td>
                </tr>
              <?php endforeach; ?>   
            <?php } ?>
            </table>
          </table>
        <?php endforeach; ?> 
      </ul>
      <p>
        <button type="button" class="btn tmp-save-btn" data-loading-text="處理中..."> 暫時儲存</button>
        <input type="submit" class="btn btn-primary cmpl-btn" data-loading-text="處理中..." value="完成"></button>
      </p>
    </div>
  </form>

 </div>



<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-datetimepicker.min.js"></script>
<script src="js/bootstrap-datetimepicker.zh-TW.js"></script>

<script>

   $(function() {
    $('.datetimepicker1').datetimepicker({
      language: 'zh-TW'
    });
  });


  $(document).on('submit', '#questionnaire-update-form', function(e) {

    var errMsg = new Array();

    var executor = $("#questionnaire-executor").val();
    if( $.trim( executor ) == ""){
      errMsg.push("請填寫訪員姓名。");
    }

    var regExp = /^\d{4}-\d{1,2}-\d{1,2} \d{1,2}:\d{1,2}$/;
    var from_date = $("#questionnaire-from-date").val();
    if( !from_date.match( regExp ) ){
      errMsg.push("稽核開始時間格式錯誤。請照下列格式填寫：年-月-日 時:分");
    }

    var to_date = $("#questionnaire-to-date").val();
    if( !to_date.match(regExp)) {
      errMsg.push("稽核離開時間格式錯誤。請照下列格式填寫：年-月-日 時:分");
    }

    if( errMsg.length > 0){
      $("#alert-msg").html( errMsg.join("<br>"));
      $(".alert").hide().fadeIn(50);
      e.preventDefault();
    }

  });

  $('.tmp-save-btn').click( function(){
    $.post( 'questionnaire/temp_save', $('#questionnaire-update-form').serialize(), function( data){
      ret  = $.parseJSON( data );
      if( ret.status == 'ok' ){
        alert('已暫時儲存。');
      }else{
        errMsg= ret.errMsg; 
        $('#alert-msg').html(errMsg);
        $('.alert').hide().fadeIn(50);
      }
    });
  });

  $

  $(document).ajaxStart(function() {
    $('.btn').button('loading');
  }).ajaxStop(function() {
    $('.btn').button('reset');
  });

  $(".true_false_null").change( function(){
    var row = $(this).attr('data-row');
    //var question = $('#row-'+row+'-question').html();
    var value = $(this).val();
    if( value == -1 ){
        $("#row-"+row+"-score").attr('readonly', true) ; 
        $("#row-"+row+"-comment").attr('readonly', true) ;
        $("#row-"+row+"-score").val('') ;
        $("#row-"+row+"-comment").val('');
    }else{
      $("#row-"+row+"-score").attr('readonly', null) ;
      $("#row-"+row+"-comment").attr('readonly', null);
    }

    if( value == 0){
      $("#row-"+row+"-tr").attr("class", "error");
    }else if( value ==1 ){
      $("#row-"+row+"-tr").attr("class", "success");
    }else{
      $("#row-"+row+"-tr").attr("class", "null");
    }

  });

      /*
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
    */

</script>