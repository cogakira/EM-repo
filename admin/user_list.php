<?php

// 認証処理のinclude
require_once('./auth.php');

// セッション内に「エラー情報のフラグ」が入っていたら取り出す
$view_data = array();
if (true === isset($_SESSION['output_buffer'])) {
    $view_data = $_SESSION['output_buffer'];
}
// 確認
//var_dump($view_data);

// (二重に出力しないように)セッション内の「出力用情報」を削除する
unset($_SESSION['output_buffer']);


// 一覧の取得

// DBハンドルの取得
$dbh = get_dbh();
// SQL文の作成
$sql = 'SELECT * FROM mdata ORDER BY updated;';
$pre = $dbh->prepare($sql);
// 値のバインド
// XXX なし
// SQLの実行
$r = $pre->execute(); // XXX
// データの取得
$data = $pre->fetchAll(PDO::FETCH_ASSOC);

// role表示用配列の作成
$role_print = array(
    '0' => '閲覧のみ',
    '1' => '仮会員',
    '2' => '本会員',
    '3' => 'マネージャー',
    '4' => '管理者管理',
);

// CSRFトークンの取得
$csrf_token = create_csrf_token_admin();


?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>DB講座上級 管理画面</title>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>

<body>
<div class="container">

  <h1>一覧</h1>
  <a href="./top.php">topに戻る</a><br>
  <hr>

<?php if ( (isset($view_data['register_success']))&&(true === $view_data['register_success']) ) : ?>
    <span class="text-success">管理者を新たに登録しました。<br></span>
<?php endif; ?>
<?php if ( (isset($view_data['delete_success']))&&(true === $view_data['delete_success']) ) : ?>
    <span class="text-success">管理者を一件、削除しました。<br></span>
<?php endif; ?>

<?php if ( (isset($view_data['error_csrf']))&&(true === $view_data['error_csrf']) ) : ?>
    <span class="text-danger">CSRFトークンでエラーが起きました。正しい遷移を、５分以内に操作してください。<br></span>
<?php endif; ?>

  <table class="table table-hover">
  <tr>
    <th>ID
    <th>名前
    <th>role
    <th>作成日

  <?php foreach($data as $datum): ?>
  <tr>
    <td><?php echo h($datum['id']); ?>
    <td><?php echo h($datum['mname']); ?>
    <td><?php echo h($role_print[$datum['role']]); ?>
    <td><?php echo h($datum['created']); ?>
    <td><form action="./admin_password_change.php" method="get">
            <input type="hidden" name="id" value="<?php echo h($datum['id']); ?>">
            <button class="btn btn-nomal">パスワード上書き</button>
        </form>
    <td><form action="./user_delete.php" method="post">
            <input type="hidden" name="id" value="<?php echo h($datum['id']); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
            <button class="btn btn-danger" onClick="return confirm('本当に削除しますか？');">削除する</button>
        </form>
  <?php endforeach; ?>

  </table>

  <hr>
  <a href="./logout.php">ログアウト</a>
  <hr>
</div>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>
