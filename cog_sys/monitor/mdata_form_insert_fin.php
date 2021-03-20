<?php
/*
 * ユーザからのform情報の取得とDBへのINSERT
 */

// ユーザ入力情報の取得
// --------------------------------------
// HTTP responseヘッダを出力する可能性があるので、バッファリングしておく
ob_start();

session_start();

// 共通関数のinclude
require_once('../common_function.php');
require_once('../user_data.php');

// ユーザ入力情報を保持する配列を準備する
// $user_input_data = array();
$user_input_data = [];



// 「パラメタの一覧」を把握
$params = ['mname', 'furigana','yubin','addr1','addr2','tel','email','birth','password'];
// データを取得する
foreach ($params as $p) {
    $user_input_data[$p] = (string)@$_POST[$p];
}
// 確認
// var_dump($user_input_data);

$error_flg = false;
$error_detail = [];
//必須チェック
$validate_params = ['mname', 'furigana','yubin','addr1','addr2','tel','email','birth','password'];
foreach ($validate_params as $p) {
    if ($user_input_data[$p] === '') {
        $error_detail["error_must_{$p}"] = true;
        $error_flg = true;
    }
}

//以下型チェック
//郵便番号と誕生日
// 型チェックを実装
// 郵便番号
/*
    \A: 行頭
    [0-9]{3}： [0から9までのいずれかの文字]を３回繰り返す
    [- ]?： [ハイフン、スペースのいずれかの文字]を０回ないし１回繰り返す
    [0-9]{4}： [0から9までのいずれかの文字]を４回繰り返す
    \z: 行末
*/
if (1 !== preg_match('/\A[0-9]{3}[- ]?[0-9]{4}\z/', $user_input_data['yubin'])) {
    // 「郵便番号のフォーマットエラー」であることを配列に格納しておく
    $error_detail["error_format_yubin"] = true;
    // エラーフラグを立てる
    $error_flg = true;
}


// CSRFチェック
if (false === is_csrf_token()) {
    // 「CSRFトークンエラー」であることを配列に格納しておく
    $error_detail["error_csrf"] = true;
    // エラーフラグを立てる
    $error_flg = true;
}

if (true === $error_flg) {
    //エラー情報と入力情報をoutput_bufferに抱き合わせる
    $_SESSION['output_buffer'] = $error_detail;
    $_SESSION['output_buffer'] += $user_input_data;
    header('Location:./mdata_form_insert.php');
    exit;
}

// DBハンドルの取得
$dbh = get_dbh();

// INSERT文の作成と発行
// ------------------------------
// 準備された文(プリペアドステートメント)の用意
$validate_params = ['mname', 'furigana','yubin','addr1','addr2','tel','email','birth','password'];

$sql = 'INSERT INTO mdata(mname,furigana,yubin,addr1,addr2,tel,email,birth,password,created,role)
             VALUES (:mname, :furigana,:yubin,:addr1,:addr2,:tel,:email,:birth,:password,:created, :role);';
$pre = $dbh->prepare($sql);

// 値のバインド
$pre->bindValue(':mname', $user_input_data['mname'], PDO::PARAM_STR);
$pre->bindValue(':furigana', $user_input_data['furigana'], PDO::PARAM_STR);
$pre->bindValue(':yubin', format_yubin($user_input_data['yubin']), PDO::PARAM_STR);
$pre->bindValue(':addr1', $user_input_data['addr1'], PDO::PARAM_STR);
$pre->bindValue(':addr2', $user_input_data['addr2'], PDO::PARAM_STR);
$pre->bindValue(':tel', $user_input_data['tel'], PDO::PARAM_STR);
$pre->bindValue(':email', $user_input_data['email'], PDO::PARAM_STR);
$pre->bindValue(':birth', $user_input_data['birth'], PDO::PARAM_STR);
$pre->bindValue(':password', sha1($user_input_data['password']), PDO::PARAM_STR);
//
// $birthday = "{$user_input_data['birthday_yy']}-{$user_input_data['birthday_mm']}-{$user_input_data['birthday_dd']}";
// $pre->bindValue(':birthday', $birthday, PDO::PARAM_STR);
$pre->bindValue(':created', date(DATE_ATOM), PDO::PARAM_STR);
$pre->bindValue(':role', 3, PDO::PARAM_INT);
// $pre->bindValue(':updated', date(DATE_ATOM), PDO::PARAM_STR);

// SQLの実行
$r = $pre->execute();
if (false === $r) {
        // XXX 本当はもう少し丁寧なエラーページを出力する
        echo 'システムでエラーが起きました';
        exit;
}

// echo 'OK';

// 正常に終了したので、セッション内の「出力用情報」を削除する
unset($_SESSION['output_buffer']);

?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>thank</title>
  <style type="text/css">
    .error { color: red; }
  </style>
</head>

<body>
  入力いただきましてありがとうございました。
</body>
</html>

