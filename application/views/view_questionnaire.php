
<div class="container-fluid">
  <div class="alert alert-error default-hide">
    <button type="button" class="close">&times;</button>
    <span id="alert-msg"></span>
  </div>
  <form class="form-inline" action="questionnaire/complete" id="questionnaire-update-form" method="post">
  <div>
      <h4> <?= $questionnaire->year ?>年<?= $questionnaire->month ?>月份<?= $questionnaire->title ?>
      <a class="btn btn-info btn-mini no-print" href='javascript:window.print()' >友善列印</a>
      </h4>
      <div style="overflow: auto;">
        <div style="float:left; width:50%; font-size:9px;">
          <ul>
            <li><strong>被稽核單位</strong>：<?= $questionnaire->target_department->department_name ?> </li>  
            <?php if(  !$is_encrypted  ): ?> 
              <li><strong>負責人</strong>：<?= $questionnaire->assigned_user->name ?> </li>
              <li><strong>訪員姓名</strong>：<?= $questionnaire->executor?> </li>
            <?php endif;?>
            <li><strong>稽核時間</strong>：<?= trim_sec( $questionnaire->from_date )?> ～<?= trim_sec( $questionnaire->to_date) ?> </li>
          </ul>
        </div>
        <div style="float:left; width:50%">
          <table class="score_table">
            <tr>
              <th>評核內容</th><th>優良</th><th>調整</th><th>總數</th><th>分數</th>
            </tr>
              <?php foreach( $questionnaire->sections as $section ): ?>
                <?php if( $section->is_analyzed ){ ?>
                  <tr>
                    <td><?= $section->section_title ?></td>
                    <td><?= cml_section_pos($section) ?> </td>
                    <td><?= cml_section_neg($section) ?> </td>
                    <td><?= cml_section_pos($section) + cml_section_neg($section) ?></td>
                    <td><?php
                          $score = cml_section_score($section);
                          if( is_null($score)){
                            echo "-";
                          }else{
                            echo sprintf("%.2f%%" , 100* $score) ; 
                          }
                        ?>
                    </td>
                  </tr>
                <?php } ?>
              <?php endforeach; ?>
            <tr>
              <td><strong>總計</strong></td>
              <td><?= cml_questionnaire_pos( $questionnaire ) ?></td>
              <td><?= cml_questionnaire_neg( $questionnaire ) ?></td>
              <td><?= cml_questionnaire_pos( $questionnaire) + cml_questionnaire_neg( $questionnaire)  ?></td>
              <td><?php
                $score= cml_questionnaire_score($questionnaire);
                if(is_null($score)){
                  echo "-";
                } else{
                  echo sprintf("%.2f%%", 100* $score); 
                }?>
              </td>
            </tr>
          </table>
        </div>
      </div>
      
      <?php $i=0; ?>
      <?php $section_i = 1;?>
        <?php foreach( $questionnaire->sections as $section ): ?> 
          <?php $number_i = 1;?>
          <strong>
            <?= to_chinese_number($section_i++) ?>、 <?= $section->section_title ?>
          </strong>
          <?php if( !$section->is_all_comment ){ ?>
            <table class="detail_table">
            <tr><th>編號</th><th>評分</th><th>問題</th><th>評語</th></tr>
              <?php foreach( $section->questions as $question ) : ?>
                    <tr <?php if($section->is_analyzed){ ?> class="<?=score2class( $question->score) ?>"<?php }?>>
                      <td class="print_short center_td"><?= $number_i++ ?></td>
                      <td class="print_short center_td">
                        <?php if( $question->has_score && !$question->score->is_null ){ ?> 
                          <?= score2symbol( $question->score->score ) ?> 
                        <?php }else{ ?> 
                          --
                        <?php } ?>  
                      </td>
                      <td class="print_large"><span id='row-<?= $i ?>-question'><?= $question->question_title?></span></td>

                      <td class="print_medium"><?php if( $question->has_comment){ ?>
                                          <?= $question->score->comment?>
                                          <?php }else{ ?>
                                          <?php } ?>
                      </td>
                    </tr>
              <?php $i++; endforeach; ?>
            </table>
          <?php }else{ ?>
            <table class="detail_table">
            <tr><th colspan="2">評語</th></tr>
              <?php foreach( $section->questions as $question ) : ?>
                    <tr <?php if($section->is_analyzed){ ?> class="<?=score2class( $question->score) ?>"<?php }?>>
                      <td class="print_short"><span id='row-<?= $i ?>-question'><?= $question->question_title?></span></td>

                      <td class="print_large"><?php if( $question->has_comment){ ?>
                                          <?= $question->score->comment?>
                                          <?php }else{ ?>
                                          <?php } ?>
                      </td>
                    </tr>
              <?php $i++; endforeach; ?>
            </table>
          <?php }; ?>
        <?php endforeach; ?> 
      

    </div>
  </form>

 </div>

