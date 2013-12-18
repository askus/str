<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <base href="<?=base_url()?>">
  <meta charset="utf-8">
  <title><?= $template->year ?>年<?= $template->month ?>月份<?= $template->title ?></title>
  <link href="/favicon.ico" rel="icon">
  <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
  <link href="css/screen.css" media="screen, projection" rel="stylesheet">
  <link href="css/print.css" media="print" rel="stylesheet">
  <!--[if lte IE 7]><link href="css/ie.css" media="screen, projection" rel="stylesheet"><![endif]-->
  <?php if (isset($css)) : foreach ($css as $cs) : ?>
  <link href="<?=base_url('css/'. $cs)?>" media="screen, projection" rel="stylesheet">
  <?php endforeach; endif; ?>
  <?php if (isset($print_css)) : foreach ($print_css as $cs) : ?>
  <link href="<?=base_url('css/'. $cs)?>" media="print" rel="stylesheet">
  <?php endforeach; endif; ?>
</head>

<body>
<div class="container">
    <div class="container-fluid">
      <div class="alert alert-error default-hide">
        <button type="button" class="close">&times;</button>
        <span id="alert-msg"></span>
      </div>
      <form class="form-inline" action="template/complete" id="template-update-form" method="post">
      <div>
          <h4> <?= $template->year ?>年<?= $template->month ?>月份<?= $template->title ?>
          <a class="btn btn-info btn-mini no-print" href='javascript:window.print()' >友善列印</a>
          </h4>
          <div style="overflow: auto;">
            <div style="float:left; width:50%; font-size:9px;">
              <ul>
                <li><strong>受稽核單位</strong>：&nbsp;  </li>  
                  <li><strong>負責人</strong>：&nbsp; </li>
                  <li><strong>訪員姓名</strong>：&nbsp;   </li>
                <li><strong>稽核時間</strong>：&nbsp;&nbsp;/&nbsp;&nbsp;/&nbsp; :  ～&nbsp; :  </li>
              </ul>
            </div>
            <div style="float:left; width:50%">
              <table class="score_table">
                <tr>
                  <th>評核內容</th><th>優良</th><th>調整</th><th>總數</th><th>分數</th>
                </tr>
                  <?php foreach( $template->sections as $section ): ?>
                    <?php if( $section->is_analyzed ){ ?>
                      <tr>
                        <td><?= $section->section_title ?></td>
                        <td>&nbsp;  </td>
                        <td>&nbsp;  </td>
                        <td>&nbsp;  </td>
                        <td>&nbsp;  </td>
                      </tr>
                    <?php } ?>
                  <?php endforeach; ?>
                <tr>
                  <td><strong>總計</strong></td>
                  <td>&nbsp;  </td>
                  <td>&nbsp;  </td>
                  <td>&nbsp;  </td>
                  <td>&nbsp; 
                  </td>
                </tr>
              </table>
            </div>
          </div>
          
          <?php $i=0; ?>
          <?php $section_i = 1;?>
            <?php foreach( $template->sections as $section ): ?> 
              <?php $number_i = 1;?>
              <strong>
                <?= to_chinese_number($section_i++) ?>、 <?= $section->section_title ?>
              </strong>
              <?php if( !$section->is_all_comment ){ ?>
                <table class="detail_table">
                <tr><th>編號</th><th>評分</th><th>問題</th><th>評語</th></tr>
                  <?php foreach( $section->questions as $question ) : ?>
                        <tr>
                          <td class="print_short center_td"><?= $number_i++ ?></td>
                          <td class="print_short center_td">&nbsp;</td>
                          <td class="print_large"><span id='row-<?= $i ?>-question'><?= $question->question_title?></span></td>
                          <td class="print_medium">&nbsp;</td>
                        </tr>
                  <?php $i++; endforeach; ?>
                </table>
              <?php }else{ ?>
                <table class="detail_table">
                <tr><th colspan="2">評語</th></tr>
                  <?php foreach( $section->questions as $question ) : ?>
                        <tr >
                          <td class="print_short"><span id='row-<?= $i ?>-question'><?= $question->question_title?></span></td>
                          <td class="print_large">&nbsp;
                          </td>
                        </tr>
                  <?php $i++; endforeach; ?>
                </table>
              <?php }; ?>
            <?php endforeach; ?> 
          
        </div>
      </form>

    </div>
</div>
</body>
</html>