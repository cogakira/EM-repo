<?php
ob_start();
session_start();
require_once '../common_function.php';

$view_data = [];
if (true === isset($_SESSION['output_buffer'])) {
    $view_data = $_SESSION['output_buffer'];
}
unset($_SESSION['output_buffer']);

//csrfトークンの取得
$csrf_token = create_csrf_token();

?>

<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="style.css" />
  <title>仮会員登録</title>
</head>

<body>

<?php if ((isset($view_data['error_csrf'])) && (true === $view_data['error_csrf'])): ?>
    <span class="error">CSRFトークンでエラーが起きました。正しい遷移を、5分以内に操作してください<br></span>
<?php endif;?>

<div id="wrap">
  <div id="head">
    <h1>仮会員登録</h1>
  </div>
  <div id="content">
    <div id="lead">
      <p>お名前とメールアドレスとパスワードを記入してログインしてください。</p>
      <p>本会員入会手続きがまだの方はこちらからどうぞ。</p>
      <p>&raquo;<a href="../kaiin/kaiin_form_insert.php">本会員入会手続きはこちらへ</a></p>
    </div>
    <form action="./kkaiin_form_insert_fin.php" method="post">
      <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">

      <dl>
        <dt>名前</dt>
        <dd>
        <?php if ((isset($view_data['error_must_mname'])) && (true === $view_data['error_must_mname'])): ?>
          <span class = "error" >名前が未入力です<br></span>
        <?php endif;?>
          <input type="text" name="mname" size="35" maxlength="255" value="<?php echo h($view_data['mname']); ?>" />
        </dd>
        <dt>email</dt>
        <dd>
        <?php if ((isset($view_data['error_must_email'])) && (true === $view_data['error_must_email'])): ?>
          <span class = "error" >emailが未入力です<br></span>
        <?php endif;?>

          <input type="text" name="email" size="35" maxlength="255" value="<?php echo h($view_data['email']); ?>" />
        </dd>
        <dt>パスワード</dt>
        <dd>
        <?php if ((isset($view_data['error_must_password'])) && (true === $view_data['error_must_password'])): ?>
          <span class = "error" >passwordが未入力です<br></span>
        <?php endif;?>

          <input type="password" name="password" size="35" maxlength="255" value="<?php echo h($view_data['password']); ?>" />
        </dd>
        <dt>ログイン情報の記録</dt>
        <dd>
          <input id="save" type="checkbox" name="save" value="on">
          <label for="save">次回からは自動的にログインする</label>
        </dd>
      </dl>
      <div>
      <button type="submit">データ登録</button>
      </div>
    </form>
  </div>
  <div id="foot">
    <p><img src="" width="136" height="15" alt="cog_akira web_system" /></p>
  </div>
</div>
</body>
</html>