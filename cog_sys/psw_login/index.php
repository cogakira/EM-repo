<?php
// セッションの開始
ob_start();
session_start();

// 共通関数のinclude
require_once('../common_function.php');

// セッションに入っている情報を確認する
//var_dump($_SESSION);

// セッション内に「エラー情報のフラグ」が入っていたら取り出す
$view_data = array();
if (true === isset($_SESSION['output_buffer'])) {
    $view_data = $_SESSION['output_buffer'];
}
// 確認
// var_dump($view_data);

// (二重に出力しないように)セッション内の「出力用情報」を削除する
unset($_SESSION['output_buffer']);

?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="style.css" />
  <title>login</title>
  <style type="text/css">
    .error { color: red; }
  </style>
</head>

<body>
<div id="wrap">
  <div id="head">
      <h1>メールアドレスとパスワードを入力してログインしてください。</h1>
  </div> 
  <div id="content">

        <form action="./login.php" method="post">

      <?php if ( (isset($view_data['error_invalid_login']))&&(true === $view_data['error_invalid_login']) ) : ?>
          <span class="error">メールアドレスまたはパスワードに誤りがあります<br></span>
      <?php endif; ?>

      <?php if ( (isset($view_data['error_must_email']))&&(true === $view_data['error_must_email']) ) : ?>
          <span class="error">メールアドレスが未入力です<br></span>
      <?php endif; ?>
          メールアドレス：<input type="text" name="email" size="30" value="<?php echo h(@$view_data['email']); ?>"><br>

      <?php if ( (isset($view_data['error_must_password']))&&(true === $view_data['error_must_password']) ) : ?>
          <span class="error">パスワードが未入力です<br></span>
      <?php endif; ?>
          パスワード：<input type="password" name="password" value=""><br>

          <br>
          <button type="submit">ログイン</button><br>
          <div style="background-color:#ccc;
                      width:130px;
                      margin:0 auto;
                      mouse:pointer;
                      border:solid 3px blue;
                      ">
          <a href="https://www.cogoc.net" style="text-decoration:none">ホームへ戻る</a>
          </div>
        </form>
          <div style="margin:30px 0;"><button><a href="../buf/reminder_input.php" style="text-decoration:none">パスワード再設定</a></button></div>
       
    <div id="lead">
      <p>仮会員入会手続きがまだの方はこちらからどうぞ。</p>
      <p>&raquo;<a href="../buf/user_register_kk.php">仮会員入会手続きはこちらへ</a></p>
    </div>
    <div>
    <hr>
      <p>本会員入会手続きがまだの方はこちらからどうぞ。</p>
      <p>&raquo;<a href="../buf/user_register_k.php">本会員入会手続きはこちらへ</a></p>
    </div>
    <div>
    <hr>
      <p>モニター登録手続きがまだの方はこちらからどうぞ。（モニター番号がない場合は登録できません)</p>
      <p>&raquo;<a href="../buf/user_register_mo.php">モニター登録手続きはこちらへ</a></p>
    </div>
    <hr>



  </div>
</div>
</body>
</html>