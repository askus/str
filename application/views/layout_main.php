<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <base href="<?=base_url()?>">
  <meta charset="utf-8">
  <title>臺中市政府社會局服務稽核評分系統</title>
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
  <div class="navbar navbar-static-top navbar-inverse">
   <div class="navbar-inner">
      <a class="brand" href="<?=base_url()?>">臺中市政府社會局服務稽核評分系統</a>
      <ul class="nav pull-right">
        <li class="divider-vertical"></li>
        <li><a href="<?=base_url('user/profile')?>" role="button" style="font-size:13px">更改密碼</a></li>
        <li class="divider-vertical"></li>
        <li><a href="<?=base_url('login/logout')?>" role="button" style="font-size:13px">登出</a></li>
      </ul>
    </div>
  </div>

  <div id="sidebar">
    <div class="profile">
      <p><strong><?=$this->session->userdata('department_name')?></strong><br>
      <a><?=$this->session->userdata('user_name')?></a></p>
    </div>
    <?=show_menu()?>
  </div>

  <div id="main">
    <?=$content_for_layout?>
  </div>
</body>
</html>