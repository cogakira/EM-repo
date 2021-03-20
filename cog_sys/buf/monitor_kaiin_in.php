<?php
// セッションの開始
ob_start();
session_start();
$role=$_SESSION['auth']['role'];
error_reporting(E_ALL & ~E_NOTICE);

// 共通関数のinclude
require_once '../common_function.php';
$_SESSION['kubun']=2; 
// var_dump($_SESSION['kubun']);

$_SESSION['kubun'] = 2;
$kubun = 2;

// セッションに入っている情報を確認する
// var_dump($_SESSION['kubun']);

// セッション内に「エラー情報のフラグ」が入っていたら取り出す
$view_data = array();
if (true === isset($_SESSION['output_buffer'])) {
    $view_data = $_SESSION['output_buffer'];
}
// 確認
// var_dump($view_data);
// (二重に出力しないように)セッション内の「出力用情報」を削除する
unset($_SESSION['output_buffer']);

// CSRFトークンの取得
$csrf_token = create_csrf_token();

?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="../styles/style.css" />
	<!-- <link rel="stylesheet" href="monitor.css" /> -->
  <title>会員登録</title>
  <style type="text/css">
    .error { color: red; }
    .role1 {display:none;}
  </style>
</head>

<body>
<?php if ((isset($view_data['error_csrf'])) && (true === $view_data['error_csrf'])): ?>
    <span class="error">CSRFトークンでエラーが起きました。正しい遷移を、５分以内に操作してください。<br></span>
<?php endif;?>

<div id="wrap">
        <div id="head">
          <h1>会員登録</h1>
        </div>
<div id="content">
        <form action="./monitor_kaiin_in_fin.php" method="post">
                      <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
  <dl>
      <dt>名前</dt>
      <dd>
      <?php if ((isset($view_data['error_must_mname'])) && (true === $view_data['error_must_mname'])): ?>
          <span class="error">名前が未入力です<br></span>
      <?php endif;?>
          <input type="text" name="mname" value="<?php echo h(@$view_data['mname']); ?>"><br>
      </dd>
      <div id="target2">

      <dt>フリガナ</dt>
      <dd>
      <?php if ((isset($view_data['error_must_furigana'])) && (true === $view_data['error_must_furigana'])): ?>
                <span class = "error" >フリガナが未入力です<br></span>
              <?php endif;?>
                <input type="text" name="furigana" size="35" maxlength="255" style="width:200px"; value="<?php echo h($view_data['furigana']); ?>" />
      </dd>
      <dt>郵便番号</dt>
      <dd>
      <?php if ((isset($view_data['error_must_yubin'])) && (true === $view_data['error_must_yubin'])): ?>
                <span class = "error" >郵便番号が未入力です<br></span>
              <?php endif;?>
              <?php if ((isset($view_data['error_format_yubin'])) && (true === $view_data['error_format_yubin'])): ?>
                <span class = "error" >郵便番号の書式が違います<br></span>
              <?php endif;?>
                <input type="text" name="yubin" size="35" maxlength="255" style="width:100px"; value="<?php echo h($view_data['yubin']); ?>" />
      </dd>
      <dt>住所１</dt>
      <dd>
      <?php if ((isset($view_data['error_must_addr1'])) && (true === $view_data['error_must_addr1'])): ?>
                <span class = "error" >住所が未入力です<br></span>
              <?php endif;?>
                <input type="text" name="addr1" size="35" maxlength="255" value="<?php echo h($view_data['addr1']); ?>" />
      </dd>
      <dt>住所2</dt>
      <dd>
      <?php if ((isset($view_data['error_must_addr2'])) && (true === $view_data['error_must_addr2'])): ?>
                <span class = "error" >番地が未入力です<br></span>
              <?php endif;?>
                <input type="text" name="addr2" size="35" maxlength="255" value="<?php echo h($view_data['addr2']); ?>" />
      </dd>
      <dt>携帯番号('-'なし)</dt>
      <dd>
      <?php if ((isset($view_data['error_must_tel'])) && (true === $view_data['error_must_tel'])): ?>
                <span class = "error" >携帯番号が未入力です<br></span>
              <?php endif;?>
                <input type="text" name="tel" size="35" maxlength="13" style="width:130px"; value="<?php echo h($view_data['tel']); ?>" />
      </dd>
      </div>

  </dl>
          <br>
          <button type="submit">データ登録</button>
<div>
    <hr>

    <?php if($role === 2) : ?>
      <button><a style="text-decoration:none;" href="../psw_login/top2.php">マイページに戻る</a></button>
    <?php elseif($role === 3): ?>
          <button><a style="text-decoration:none;" href="../psw_login/top3.php">マイページに戻る</a></button>
    <?php elseif($role === 4) : ?>
      <button><a style="text-decoration:none;" href="../psw_login/top4.php">マイページに戻る</a></button>
    <?php elseif($role === 5): ?>
          <button><a style="text-decoration:none;" href="../psw_login/top5.php">マイページに戻る</a></button>
    <?php elseif($role === 6): ?>
          <button><a style="text-decoration:none;" href="../psw_login/top6.php">マイページに戻る</a></button>
    <?php elseif($role === 7): ?>
          <button><a style="text-decoration:none;" href="../psw_login/top7.php">マイページに戻る</a></button>
    <?php elseif($role === 10): ?>
          <button><a style="text-decoration:none;" href="../psw_login/top10.php">マイページに戻る</a></button>
    <?php endif; ?>

    </div>
        </form>
</div>
<div id="foot">
    <p><img src="" width="136" height="15" alt="cog_akira web_system" /></p>
  </div>
  </div>
</div>
<!-- <script src="main.js"></script> -->
</body>
</html>
