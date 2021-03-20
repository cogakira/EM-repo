<?php

/*
 * (管理画面想定)１件のform情報の編集完了処理
 */
// セッションの開始
ob_start();
session_start();

require_once '../mdata_form.php';
// 日付関数(date)を使うのでタイムゾーンの設定
date_default_timezone_set('Asia/Tokyo');

// idは別途取得しておく
$id = (int)@$_POST['id'];

$role = (int)@$_POST['role'];

//  ユーザ入力情報を保持する配列を準備する
$mdata_edit_data = [];

// 「パラメタの一覧」を把握
if ($role === 1) {
    $params = ['mname', 'email', 'role'];
} else {
    $params = ['mcode', 'mname', 'furigana', 'yubin', 'addr1', 'addr2', 'tel', 'email', 'birth', 'role'];
}
// データを取得する
foreach ($params as $p) {
    $mdata_edit_data[$p] = (string) @$_POST[$p];
}
// 確認
// var_dump($mdata_edit_data);

// ユーザ入力のvalidate
// --------------------------------------
// 基本のエラーチェック
$error_detail = validate_mdata_form($mdata_edit_data);

// 編集用、追加のエラーチェック
// 必須チェックを実装
// 空文字(未入力)なら

if ($role > 1) {
    if ('' === $mdata_edit_data['birth']) {
        // 「必須情報の未入力エラー」であることを配列に格納しておく
        $error_detail["error_must_birth"] = true;
    }
    // 誕生日
    // 一端フォーマットを整える
    // XXX strtotime() 関数はある程度「如何様にも」解釈をしてくれる関数だが、管理画面なので一端「ざっくりと」確認、程度にしておく
    $t = strtotime($mdata_edit_data['birth']);
    if (false === $t) {
        // 「誕生日のフォーマットエラー」であることを配列に格納しておく
        $error_detail["error_format_birth"] = true;
    } else {
        // 文字列に置きなおして
        $s = date('Y-m-d', $t);
        // 年、月、日に分解
        list($yy, $mm, $dd) = explode('-', $s);

        // PHPの標準関数を使って日付の妥当性をチェックする
        if (false === checkdate($mm, $dd, $yy)) {
            // 「誕生日のフォーマットエラー」であることを配列に格納しておく
            $error_detail["error_format_birth"] = true;
        }
    }

}
// CSRFチェック
if (false === is_csrf_token_admin()) {
    // 「CSRFトークンエラー」であることを配列に格納しておく
    $error_detail["error_csrf"] = true;
}


// var_dump($mdata_edit_data);

// エラーが出たら入力ページに遷移する
if (false === empty($error_detail)) {
    // エラー情報をセッションに入れて持ちまわる
    $_SESSION['output_buffer'] = $error_detail;

    // 入力値をセッションに入れて持ちまわる
    // XXX 「keyが重複しない」はずなので、加算演算子でOK
    $_SESSION['output_buffer'] += $mdata_edit_data;

    // 編集ページに遷移する
    header('Location: ./manager_data_update.php?id=' . rawurlencode($id));
    exit;
}

// DBハンドルの取得
$dbh = get_dbh();

// INSERT文の作成と発行
// ------------------------------
// 準備された文(プリペアドステートメント)の用意
if($role > 1 ){
$sql = 'UPDATE mdata SET mcode = :mcode,mname=:mname,furigana=:furigana,yubin=:yubin,addr1=:addr1,addr2=:addr2,tel=:tel,email=:email,birth=:birth,role=:role WHERE id = :id;';
$pre = $dbh->prepare($sql);

// 値のバインド
$pre->bindValue(':id', $id, PDO::PARAM_INT);
$pre->bindValue(':mcode', $mdata_edit_data['mcode'], PDO::PARAM_STR);
$pre->bindValue(':mname', $mdata_edit_data['mname'], PDO::PARAM_STR);
$pre->bindValue(':furigana', $mdata_edit_data['furigana'], PDO::PARAM_STR);
$pre->bindValue(':yubin', format_yubin($mdata_edit_data['yubin']), PDO::PARAM_STR);
$pre->bindValue(':addr1', $mdata_edit_data['addr1'], PDO::PARAM_STR);
$pre->bindValue(':addr2', $mdata_edit_data['addr2'], PDO::PARAM_STR);
$pre->bindValue(':tel', $mdata_edit_data['tel'], PDO::PARAM_STR);
$pre->bindValue(':email', $mdata_edit_data['email'], PDO::PARAM_STR);
$pre->bindValue(':birth', $mdata_edit_data['birth'], PDO::PARAM_STR);
$pre->bindValue(':role', $mdata_edit_data['role'], PDO::PARAM_STR);
// $pre->bindValue(':updated', date(DATE_ATOM), PDO::PARAM_STR);
}else{
    $sql = 'UPDATE mdata SET mname=:mname,email=:email,role=:role WHERE id = :id;';
$pre = $dbh->prepare($sql);

// 値のバインド
$pre->bindValue(':id', $id, PDO::PARAM_INT);
$pre->bindValue(':mname', $mdata_edit_data['mname'], PDO::PARAM_STR);
$pre->bindValue(':email', $mdata_edit_data['email'], PDO::PARAM_STR);
$pre->bindValue(':role', $mdata_edit_data['role'], PDO::PARAM_STR);
// $pre->bindValue(':updated', date(DATE_ATOM), PDO::PARAM_STR);
}

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
  <title>データ変更更新</title>
  <style type="text/css">
    .error { color: red; }
  </style>
</head>

<body>
  修正しました。<br>
  <br>
  <?php if(true===isset($_SESSION['auth']['id'])): ?>
  <a href="../psw_login/top2.php">会員ページに戻る</a>
  <?php else : ?>
  <a href="./manager_data_list.php">一覧に戻る</a>
  <?php endif; ?>
</body>
</html>

