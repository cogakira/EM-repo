<?php

ob_start();

// セッションの開始
session_start();

// 共通関数のinclude
require_once '../common_function.php';
require_once '../teiki_form.php';

$dbh = get_dbh();

$id = (string)@$_GET['id'];
$sqlname = 'select * from mdata where id = :id;';
$prename = $dbh->prepare($sqlname);
$prename->bindValue(':id', $id, PDO::PARAM_INT);
$rname = $prename->execute();
if (false === $rname) {
    // XXX 本当はもう少し丁寧なエラーページを出力する
    echo 'システムでエラーが起きました';
    exit;
}
$dataname = $prename->fetch(0);
$mcode = $dataname['mcode'];
$kainame = $dataname['mname'];
$_SESSION['user']['id'] = $id;
$_SESSION['user']['name'] = $dataname['mname'];
$id = $dataname['id'];
$_SESSION['auth']['mcode'] = $mcode;
// 確認
// var_dump($kainame);

// データの取得
$datum = get_teiki_form($id);
if (true === empty($datum)) {
  //定期商品登録無しの場合
  // header('Location: ./top2.php');
  // header('Location: ./monitor_kaiin_list.php');
  header('Location: ../monitor/teiki_in_kai.php');
    // exit;
}

$_SESSION['user']['t_id']=$datum['t_id'];
$t_id=$datum['t_id'];
$shohincd = $datum['shohincd'];
$hjunkyocd = $datum['hjunkyocd'];

// $_SESSION['output_buffer']にデータがある場合は、情報を上書きする
// XXX 配列の「加算演算子による結合」では先に出したほうが優先されるので、セッション情報を先に書く
if (true === isset($_SESSION['output_buffer'])) {
    $datum = $_SESSION['output_buffer'] + $datum;
}
//var_dump($datum);
// (二重に出力しないように)セッション内の「出力用情報」を削除する
unset($_SESSION['output_buffer']);

// CSRFトークンの取得
$csrf_token = create_csrf_token_admin();
//----------------------select--------
$dbh = get_dbh();
$sqlshohin = 'select * from shohinms where shohincd = :shohincd order by shohincd;';
$stmtsho=$dbh->prepare($sqlshohin);
$stmtsho->bindValue(':shohincd',$shohincd,PDO::PARAM_INT);
$s=$stmtsho->execute();
$shohinlst = $stmtsho->fetch();
$shohinmei = $shohinlst['shohinmei'];

$sqljun = 'select * from junkyoms where hjunkyocd = :hjunkyocd order by junjo,hjunkyocd;';
$stmtjun=$dbh->prepare($sqljun);
$stmtjun->bindParam(':hjunkyocd',$hjunkyocd,PDO::PARAM_INT);
$hjun=$stmtjun->execute();

  $junkyolst = $stmtjun -> fetch();
  $hjunkyomei = $junkyolst['hjunkyomei'];
  // var_dump($junkyolst);


  $error_gou = $_SESSION['output_buffer']['error_must_gou'];
  unset($_SESSION['output_buffer']);
  // var_dump($_SESSION['output_buffer']);




?>


<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title定期変更・確認画面</title>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>
<div class="container">
  <h1 style="color:green;"><?php echo($kainame); ?> さんの定期内容修正</h1>
  
<?php if ( (isset($datum['error_csrf']))&&(true === $datum['error_csrf']) ) : ?>
    <span class="text-danger">CSRFトークンでエラーが起きました。正しい遷移を、５分以内に操作してください。<br></span>
<?php endif; ?>

<?php if ((isset($datum['error_must_id'])) && (true === $datum['error_must_id'])): ?>
    <span class="text-danger">idが未入力です<br></span>
<?php endif;?>
<?php if ((isset($datum['error_must_mcode'])) && (true === $datum['error_must_mcode'])): ?>
    <span class="text-danger">モニターコードが未入力です<br></span>
<?php endif;?>

<?php if ((isset($datum['error_must_hjunkyocd'])) && (true === $datum['error_must_hjunkyocd'])): ?>
    <span class="text-danger">準拠コードが未入力です<br></span>
<?php endif;?>
<?php if ((isset($datum['error_must_rackno'])) && (true === $datum['error_must_rackno'])): ?>
    <span class="text-danger">ラックナンバー<br></span>
<?php endif;?>
<?php if ((isset($datum['error_must_shohincd'])) && (true === $datum['error_must_shohincd'])): ?>
    <span class="text-danger">商品コード<br></span>
<?php endif;?>
<?php if ((isset($datum['error_must_keikucd'])) && (true === $datum['error_must_keikucd'])): ?>
    <span class="text-danger">契約区分が未入力です<br></span>
<?php endif;?>

  <form action="./teiki_update_fin.php" method="post">

  <input type="hidden" name="id" value="<?php echo h($datum['id']); ?>">
  <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">

  <table class="table table-hover">
  <p>(モニターコードを変更すると変更先のモニターの定期商品となります)</p>
  <tr>
    <td width="120">ID
    <td width="120"> <?php echo h($t_id); ?>
  <tr>
    <td width="120">会員ID
    <td width="120"> <?php echo h($id); ?>
  <tr>
    <td width="120">モニターコード
    <td width="120"><input name="mcode" size = "6" value="<?php echo h($datum['mcode']); ?>">
  <tr>
    <td width="120">準拠コード
    <td width="120"><input name="hjunkyocd" size = "8" value="<?php echo h($datum['hjunkyocd']); ?>">
    <td><?php echo($hjunkyomei); ?>
  <tr>
    <td>ラックナンバー
    <td  width="120"><input name="rackno" size = "3"value="<?php echo h($datum['rackno']); ?>">
  <tr>
    <td>商品コード
    <td><input name="shohincd" size = "5" value="<?php echo h($datum['shohincd']); ?>">
    <td><?php echo($shohinmei); ?>
  <tr>
    <td>契約区分
    <td><input name="keikucd" size = "2"value="<?php echo h($datum['keikucd']); ?>">
  </table>

  <button>情報を修正する</button>
  <button><a style="text-decoration:none;" href="../monitor/monitor_kaiin_list.php">戻る</a></button>

  </form>
</div>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>