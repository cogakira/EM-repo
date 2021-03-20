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
  <title>モニター登録</title>
</head>

<body>

<?php if ((isset($view_data['error_csrf'])) && (true === $view_data['error_csrf'])): ?>
    <span class="error">CSRFトークンでエラーが起きました。正しい遷移を、5分以内に操作してください<br></span>
<?php endif;?>

<div id="wrap">
  <div id="head">
    <h1>モニター登録</h1>
  </div>
  <div id="content">
    <div id="lead">
      <p>各項目を記入し登録してからログインしてください。</p>
      <!-- <p>本会員入会手続きがまだの方はこちらからどうぞ。</p> -->
      <!-- <p>&raquo;<a href="join/">本会員入会手続きはこちらへ</a></p> -->
    </div>
    <form action="./mdata_form_insert_fin.php" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">

      <dl>
        <dt>モニター番号(メールでお知らせした番号）</dt>
        <dd>
        <?php if ((isset($view_data['error_must_mcode'])) && (true === $view_data['error_must_mcode'])): ?>
          <span class = "error" >モニター番号が未入力です<br></span>
        <?php endif;?>
          <input type="text" name="mcode" size="35" maxlength="255" style="width:100px"; value="<?php echo h($view_data['mcode']); ?>" />
        </dd>
        <dt>名前</dt>
        <dd>
        <?php if ((isset($view_data['error_must_mname'])) && (true === $view_data['error_must_mname'])): ?>
          <span class = "error" >名前が未入力です<br></span>
        <?php endif;?>
          <input type="text" name="mname" size="35" maxlength="255" style="width:200px"; value="<?php echo h($view_data['mname']); ?>" />
        </dd>
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
        <dt>番地</dt>
        <dd>
        <?php if ((isset($view_data['error_must_addr2'])) && (true === $view_data['error_must_addr2'])): ?>
          <span class = "error" >番地が未入力です<br></span>
        <?php endif;?>
          <input type="text" name="addr2" size="35" maxlength="255" value="<?php echo h($view_data['addr2']); ?>" />
        </dd>
        <dt>携帯電話('-'なし)</dt>
        <dd>
        <?php if ((isset($view_data['error_must_tel'])) && (true === $view_data['error_must_tel'])): ?>
          <span class = "error" >電話番号が未入力です<br></span>
        <?php endif;?>
          <input type="text" name="tel" size="35" maxlength="13" style="width:130px"; value="<?php echo h($view_data['tel']); ?>" />
        </dd>
        <dt>email</dt>
        <dd>
        <?php if ((isset($view_data['error_must_email'])) && (true === $view_data['error_must_email'])): ?>
          <span class = "error" >emailが未入力です<br></span>
        <?php endif;?>
          <input type="text" name="email" size="35" maxlength="100" value="<?php echo h($view_data['email']); ?>" />
        </dd>
        <dt>誕生日 0000/00/00</dt>
        <dd>
        <?php if ((isset($view_data['error_must_birth'])) && (true === $view_data['error_must_birth'])): ?>
          <span class = "error" >誕生日が未入力です<br></span>
        <?php endif;?>
        <input type="date" name="birth" value="<?php echo h($view_data['birth']); ?>">
        </dd>
        <dt>パスワード</dt>
        <dd>
        <?php if ((isset($view_data['error_must_password'])) && (true === $view_data['error_must_password'])): ?>
          <span class = "error" >passwordが未入力です<br></span>
        <?php endif;?>

          <input type="password" name="password" size="35" maxlength="255" style="width:200px"; value="<?php echo h($view_data['password']); ?>" />
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