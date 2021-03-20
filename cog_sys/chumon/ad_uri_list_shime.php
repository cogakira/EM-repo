<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~ E_DEPRECATED);

require_once '../common_function.php';


// DBハンドルの取得
$dbh = get_dbh();
$mcode = $_SESSION['auth']['mcode'];

$shimebi = $_POST['shimebi'];
//未納品分含む
$stmt = $dbh->prepare('select sum(seikyu) as seikyu from uriage where mcode = :mcode && shimebi = :shimebi and shukko = 4');
$stmt->bindParam(':mcode', $mcode, PDO::PARAM_STR);
$stmt->bindParam(':shimebi', $shimebi, PDO::PARAM_STR);
$stmt->execute();
if ($row = $stmt->fetch()) {
    $kei = $row['seikyu'];
}


// SELECT文の作成と発行
// ------------------------------
// 準備された文(プリペアドステートメント)の用意
$sql = 'SELECT * FROM uriage where mcode = :mcode && shimebi = :shimebi and shukko = 4 order by chumonbi desc,koshinbi desc;';
// $sql_sum = 'SELECT sum(seikyu) FROM orders where mcode_id = :mcode_id ;';
// ここ
$pre = $dbh->prepare($sql);
// $pre_sum = $dbh->prepare($sql_sum);
$pre->bindValue(':mcode', $mcode, PDO::PARAM_INT);
$pre->bindParam(':shimebi', $shimebi, PDO::PARAM_STR);
// $pre_sum->bindValue(':mcode_id', $mcode_id, PDO::PARAM_INT);

// 値のバインド
// XXX 今回はプレースホルダがないので、バインドはなし

// SQLの実行
$r = $pre->execute();
// $r_sum = $pre_sum->execute();
if (false === $r) {
    // XXX 本当はもう少し丁寧なエラーページを出力する
    echo 'システムでエラーが起きました';
    exit;
}

// データをまとめて取得
$data = $pre->fetchAll(PDO::FETCH_ASSOC);
$count = $pre->rowCount();

// var_dump($data);

?>
<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>個人取引画面</title>
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

</head>

<body>
<div class="container">
        <h1>取引内容一覧</h1>
                        <?php

$mname = $_SESSION['auth']['mname'];
echo $mcode . ' ' . $mname;
// echo $name;
echo ' 様 <br />';
echo $count . ' 件 <br />';
echo $kei . '円';
?>
                <div style="text-align:right">
                    <button ><a style="text-decoration:none" href="../psw_login/page_fee_en.php">戻る</a></button>
                </div> 
        <table class="table table-hover">
                <thead>
                        <tr>
                                <th>注文番号</th>
                                <!-- <th>連番</th> -->
                                <th>商品名</th>
                                <th>区分</th>
                                <th>採択名</th>
                                <th>月号</th>
                                <th>数</th>
                                <th>請求</th>
                                <th>契約</th>
                                <th>締日</th>
                                <th>状態</th>
                                <th>担当</th>
                                <th>注文受付日</th>
                                <!-- <th>発送日</th> -->
                                <th>発送日時</th>
                        </tr>
                </thead>
                <?php foreach ($data as $datum): ?>
                <tr>
                <td><?php echo h($datum['id']); ?>

        <?php
$sql1 = 'SELECT * FROM shohinms where shohincd = :shohincd;';
$shohindata = $dbh->prepare($sql1);
$shohindata->bindValue(':shohincd', $datum['shohincd'], PDO::PARAM_INT);
$r = $shohindata->execute();

$shona = $shohindata->fetch();
$shohinmei = $shona['shohinmei'];

$kubuncd = $datum['kubuncd'];
$sql2 = 'select * from kubunms where kubuncd = :kubuncd;';
$kubun = $dbh->prepare($sql2);
$kubun->bindValue(':kubuncd', $kubuncd, PDO::PARAM_INT);
$k = $kubun->execute();

$kubunzen = $kubun->fetch();
$kubunmei = $kubunzen['kubunmei'];
// var_dump($kubuncd);

?>
                <td><?php echo h($shona['shohinmei']); ?>
                <td><?php echo h($kubunmei); ?>
        <?php
$sql2 = "SELECT * FROM junkyoms where hjunkyocd = $datum[hjunkyocd];";

$junkyodata = $dbh->prepare($sql2);
$j = $junkyodata->execute();

$junna = $junkyodata->fetch();
?>
                <td><?php echo h($junna['hjunkyomei']); ?>
                <td><?php echo h($datum['gou']); ?>
                <td><?php echo h($datum['chumonsu']); ?>
                <td align="right"><?php echo h($datum['seikyu'] . '円'); ?>
                <?php if($datum['keikubun'] === 1) : ?>
                        <td><?php echo '年契'; ?>
                <?php else: ?>
                        <td><?php echo ''; ?>
                <?php endif  ;?>
                <td><?php echo h($datum['shimebi']); ?>
                <!-- <td><?php echo h($datum['shukko']); ?> -->
                <?php switch($datum['shukko']): ?>
<?php case 0:$shori='未処理'; ?>
<?php break; ?>
<?php case 1:$shori='受付済'; ?>
<?php break;?>
<?php case 2:$shori='入荷待'; ?>
<?php break; ?>
<?php case 3:$shori='発送待'; ?>
<?php break;?>
<?php case 4:$shori='発送済'; ?>
<?php break; ?>
<?php endswitch; ?>
<!-- <td><?php echo h($datum['shukko']); ?> -->
        <?php if($shori==='未処理') :?>
<td style="color:red;"><?php echo $shori; ?>
        <?php else: ?>
<td><?php echo $shori; ?>
        <?php endif; ?>
                <td><?php echo h($datum['tantomcode']); ?>
                <td><?php echo h($datum['chumonbi']); ?>
                <td><?php echo h($datum['koshinbi']); ?>
                <?php endforeach;?>

        </table>
</div>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>


</body>
</html>