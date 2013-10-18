<div class="container-fluid">
  <div class="row-fluid">
    <h3>評分表 / 填寫評分表</h3>
  </div>

  <div class="alert alert-error default-hide">
    <button type="button" class="close">&times;</button>
    <span id="alert-msg"></span>
  </div>

  <div>
    <ul>
      <?php foreach( $questionnaire->sections as $section ): ?> 
        <input type="hidden" name="sections[section_id][]" value="<?= $section->section_id ?>">
        <input type="hidden" name="sections[section_title][]" value="<?= $section->section_title ?>">
        <input type="hidden" name="sections[section_order][]" value="<?= $section->section_order ?>">

        <li><strong><?= $section->section_title ?></strong></li>
        <table class="table">
        <tr><th>內容</th><th>評分</th><th>評語</th></tr>
          <?php foreach( $section->questions as $question ) : ?>
              <tr>
                <input type="hidden" name="questions[question_id][]" value="<?= $question->question_id?>"></input>
                <input type="hidden" name="questions[section_id][]" value="<?= $section->section_id?>"></input>
                <input type="hidden" name="questions[section_order][]" value="<?= $section->section_order?>"></input>
                <td class="span9"><p><?= $question->question_title?></p></td>
                <td class="span1"><?php if( $question->has_score): ?><input type="text" name="question[score][]" value="<?= $question->score->score ?>"></input><?php endif; ?>  </td>
                <td class="span1"><?php if( $question->has_comment): ?><input type="text" name="questions[comment][]" value="<?= $question->score->comment?>" ></input> <?php endif; ?></td>
              </tr>
          <?php endforeach; ?>
        </table>
      <?php endforeach; ?> 
    </ul>
  </div>


 </div>