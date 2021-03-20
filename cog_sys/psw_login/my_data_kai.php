<?php
ob_start();
session_start();

// 共通関数のinclude
require_once '../common_function.php';
require_once '../mdata_form.php';

// 認証処理のinclude
require_once('auth.php');


$id=$_SESSION['auth']['id'];
$mname=$_SESSION['auth']['mname'];
$role=$_SESSION['auth']['role'];

if(true === isset($_SESSION['auth']['id'])){
  $id=$_SESSION['auth']['id'];
}else{
  header('Location: ./top2.php');
  exit;
}
// データの取得
$datum = get_mdata_form($id);
if (true === empty($datum)) {
    header('Location: ./login.php');
    exit;
}

// $_SESSION['output_buffer']にデータがある場合は、情報を上書きする
// XXX 配列の「加算演算子による結合」では先に出したほうが優先されるので、セッション情報を先に書く
if (true === isset($_SESSION['output_buffer'])) {
  $datum = $_SESSION['output_buffer'] + $datum;
}
// var_dump($datum);
// (二重に出力しないように)セッション内の「出力用情報」を削除する
unset($_SESSION['output_buffer']);

// CSRFトークンの取得
$csrf_token = create_csrf_token_admin();

?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>profile</title>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
<div class="container">
  <h1>ニックネームとメールアドレス確認</h1>
<?php if ( (isset($datum['error_csrf']))&&(true === $datum['error_csrf']) ) : ?>
    <span class="text-danger">CSRFトークンでエラーが起きました。正しい遷移を、５分以内に操作してください。<br></span>
<?php endif; ?>

<?php if ((isset($datum['error_must_mname'])) && (true === $datum['error_must_mname'])): ?>
    <span class="text-danger">名前が未入力です<br></span>
<?php endif;?>
<?php if ((isset($datum['error_must_furigana'])) && (true === $datum['error_must_furigana'])): ?>
    <span class="text-danger">フリガナが未入力です<br></span>
<?php endif;?>

<?php if ((isset($datum['error_must_yubin'])) && (true === $datum['error_must_yubin'])): ?>
    <span class="text-danger">郵便番号が未入力です<br></span>
<?php endif;?>
<?php if ((isset($datum['error_format_yubin'])) && (true === $datum['error_format_yubin'])): ?>
    <span class="text-danger">郵便番号の書式に誤りがあります<br></span>
<?php endif;?>

<?php if ((isset($datum['error_must_addr1'])) && (true === $datum['error_must_addr1'])): ?>
    <span class="text-danger">住所1が未入力です<br></span>
<?php endif;?>
<?php if ((isset($datum['error_must_addr2'])) && (true === $datum['error_must_addr2'])): ?>
    <span class="text-danger">住所2が未入力です<br></span>
<?php endif;?>
<?php if ((isset($datum['error_must_tel'])) && (true === $datum['error_must_tel'])): ?>
    <span class="text-danger">携帯電話が未入力です<br></span>
<?php endif;?>
<?php if ((isset($datum['error_must_email'])) && (true === $datum['error_must_email'])): ?>
    <span class="text-danger">emailが未入力です<br></span>
<?php endif;?>

<?php if ((isset($datum['error_must_birth'])) && (true === $datum['error_must_birth'])): ?>
    <span class="text-danger">誕生日が未入力です<br></span>
<?php endif;?>
<?php if ((isset($datum['error_format_birth'])) && (true === $datum['error_format_birth'])): ?>
    <span class="text-danger">誕生日の書式に誤りがあります<br></span>
<?php endif;?>
<?php if ((isset($datum['error_must_role'])) && (true === $datum['error_must_role']) && $role > 3): ?>
    <span class="text-danger">区分が未入力です<br></span>
<?php endif;?>


  <input type="hidden" name="id" value="<?php echo h($datum['id']); ?>">
  <input type="hidden" name="mcode" value="<?php echo h($datum['mcode']); ?>">
  <input type="hidden" name="role" value="<?php echo h($datum['role']); ?>">
  <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">

  <table class="table table-hover">
  <tr>
    <td>名前
    <td><?php echo h($datum['mname']); ?>
  <tr>
    <td>email
    <td><?php echo h($datum['email']); ?>
   
  </table>
<a href="../psw_login/top.php">マイページに戻る</a>

</div>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>