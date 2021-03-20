<?php

/*
 * (管理画面想定)１件のform情報の編集完了処理
 */
// セッションの開始
ob_start();
session_start();

require_once '../teiki_form.php';
// 日付関数(date)を使うのでタイムゾーンの設定
date_default_timezone_set('Asia/Tokyo');
$role=$_SESSION['auth']['role'];

// idは別途取得しておく
$id = (int)@$_POST['id'];
$t_id = $_SESSION['user']['t_id'];

// $rolekaiin = (int)@$_POST['role'];

//  ユーザ入力情報を保持する配列を準備する
$mdata_edit_data = [];

// 「パラメタの一覧」を把握
    $params = ['mcode','hjunkyocd', 'rackno', 'shohincd','keikucd'];

// データを取得する
foreach ($params as $p) {
    $teiki_edit_data[$p] = (string) @$_POST[$p];
}
// 確認
// var_dump($teiki_edit_data);

// ユーザ入力のvalidate
// --------------------------------------
// 基本のエラーチェック
$error_detail = validate_teiki_form($teiki_edit_data);

// 編集用、追加のエラーチェック
// 必須チェックを実装
// 空文字(未入力)なら


// CSRFチェック
if (false === is_csrf_token_admin()) {
    // 「CSRFトークンエラー」であることを配列に格納しておく
    $error_detail["error_csrf"] = true;
}


// var_dump($teiki_edit_data);

// エラーが出たら入力ページに遷移する
if (false === empty($error_detail)) {
    // エラー情報をセッションに入れて持ちまわる
    $_SESSION['output_buffer'] = $error_detail;

    // 入力値をセッションに入れて持ちまわる
    // XXX 「keyが重複しない」はずなので、加算演算子でOK
    $_SESSION['output_buffer'] += $teiki_edit_data;

    // 編集ページに遷移する
    header('Location: ./monitor_kaiin_data_update.php?id=' . rawurlencode($id));
    exit;
}

// DBハンドルの取得
$dbh = get_dbh();

// INSERT文の作成と発行
// ------------------------------
// 準備された文(プリペアドステートメント)の用意
$sql = 'UPDATE teiki SET mcode = :mcode,hjunkyocd=:hjunkyocd,rackno=:rackno,shohincd=:shohincd,keikucd=:keikucd
where t_id = :t_id ;';
$pre = $dbh->prepare($sql);

// 値のバインド
$pre->bindValue(':t_id', $t_id, PDO::PARAM_STR);
$pre->bindValue(':mcode', $teiki_edit_data['mcode'], PDO::PARAM_STR);
$pre->bindValue(':hjunkyocd', $teiki_edit_data['hjunkyocd'], PDO::PARAM_STR);
$pre->bindValue(':rackno', $teiki_edit_data['rackno'], PDO::PARAM_STR);
$pre->bindValue(':shohincd', $teiki_edit_data['shohincd'], PDO::PARAM_STR);
$pre->bindValue(':keikucd', $teiki_edit_data['keikucd'], PDO::PARAM_STR);


// SQLの実行
$r = $pre->execute();
if (false === $r) {
    // XXX 本当はもう少し丁寧なエラーページを出力する
    echo 'システムでエラーが起きました';
    exit;
}

// 正常に終了したので、セッション内の「出力用情報」を削除する
unset($_SESSION['output_buffer']);

?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" href="../styles/style.css" />
  <title>データ変更</title>
  <style type="text/css">
    .error { color: red; }
  </style>
</head>

<body>
<div id="wrap">
<div id="head" >
  <h1>修正しました。</h1>
</div>
  <br>
  <div id="content">
<div>
  <?php if(true===isset($_SESSION['auth']['id'])): ?>
  <button><a href="./monitor_kaiin_list.php" style="text-decoration:none;">一覧に戻る</a></button>
  <?php endif ; ?>
</div>
<div style="margin-top:20px">
  <button style="color:white;font-size:15px;text-decoration:none;">
          <?php if($role === 2) : ?>
          <a style="text-decoration:none;" href="../psw_login/top2.php">
          <?php elseif($role === 3) : ?>
          <a style="text-decoration:none;" href="../psw_login/top3.php">
          <?php elseif($role === 4) :?>
          <a style="text-decoration:none;" href="../psw_login/top4.php">
          <?php else : ?>
          <a style="text-decoration:none;" href="../psw_login/top5.php">
          <?php endif; ?>
          マイページに戻る</a></button>
</div>

  </div>
</div>
</body>
</html>

