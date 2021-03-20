
<?php
ini_set("display_errors", 1);
error_reporting(-1);
error_reporting(E_ALL & ~E_NOTICE);

/*
 * (管理画面想定)情報の一覧
 */
// セッションの開始
ob_start();
session_start();
$id=$_SESSION['auth']['id'];
$mcode=$_SESSION['auth']['mcode'];
$mname=$_SESSION['auth']['mname'];
$role=$_SESSION['auth']['role'];

// 共通関数のinclude
require_once '../common_function.php';
$mcode=$_SESSION['auth']['mcode'];

// unset($_SESSION['auth']);
// 設定値の設定：本来なら、configなどに吐き出したほうがよりよい場合が多い
$contents_per_page = 5; // 1Pageあたりの出力数


// XXX 管理画面であれば、本来はこのあたり(ないしもっと手前)で認証処理を行う

// ページ数の取得
if (false === isset($_GET['p'])) {
  $page_num = 1;
} else {
  $page_num = intval($_GET['p']);
  // 1より小さいページ数が指定されたら、1にそろえる
  if (1 > $page_num) {
      $page_num = 1;
  }
}
// 確認
// var_dump($page_num);


// ソートパラメタの取得
$sort = (string)@$_GET['sort'];
// デフォルトの設定
if ('' === $sort) {
    $sort = 'id';
}
// 確認
// var_dump($sort);

// 検索パラメタの取得
// (第一種)ホワイトリストの準備
$search_list = array (
  'search_mcode',
  'search_mname',
  'search_furigana',
  'search_email',
  'search_tel',
  'search_birth_from',
  'search_birth_to',
  'search_created',
  'search_like_mname',
  'search_like_furigana',
 );
// データの取得
$search = array();
foreach($search_list as $p) {
  if ((true === isset($_POST[$p]))&&('' !== $_POST[$p]) ) {
      $search[$p] = $_POST[$p];
  }
}

/*
// XXX 以下のようなコードは「セキュリティホールを生む」可能性が出てくるので、基本的には避けるのが望ましい
// XXX 「これならホワイトリストをいちいち作らなくても楽だから！」という理由から、発案に至る可能性があるので
// データの取得
$search = array();
foreach($_POST as $k => $v) {
    if ((0 === strncmp($k, 'search_', strlen('search_')))&&('' !== $_POST[$k])) {
        $search[$k] = $v;
    }
}
*/
// 確認
// var_dump($search);




// DBハンドルの取得
$dbh = get_dbh();

// SELECT文の作成と発行
// ------------------------------
// 準備された文(プリペアドステートメント)の用意
// $sql = 'SELECT * FROM mdata';
// XXX countと通常と２種類のSQLを発行する必要があるので、"SELECT *"部分を一端切り取る
// $sql = 'FROM mdata where mcode = 1010 and role = 2';
// $mcode = 121;
$sql = 'FROM mdata where role = 2 and mcode = ' . $mcode ;


// 「検索条件がある」場合の検索条件の付与
$bind_array = array();
if (false === empty($search)) {
    //
    $where_list = array();

    // 値を把握する
    //
    if (true === isset($search['search_mcode'])) {
        // WHERE句に入れる文言を設定する
        $where_list[] = 'mcode = :mcode';
        // BINDする値を設定する
        $bind_array[':mcode'] = $search['search_mcode'];
    }
    if (true === isset($search['search_mname'])) {
        // WHERE句に入れる文言を設定する
        $where_list[] = 'mname = :mname';
        // BINDする値を設定する
        $bind_array[':mname'] = $search['search_mname'];
    }
    if (true === isset($search['search_furigana'])) {
        // WHERE句に入れる文言を設定する
        $where_list[] = 'furigana = :furigana';
        // BINDする値を設定する
        $bind_array[':furigana'] = $search['search_furigana'];
    }
    if (true === isset($search['search_email'])) {
        // WHERE句に入れる文言を設定する
        $where_list[] = 'email = :email';
        // BINDする値を設定する
        $bind_array[':email'] = $search['search_email'];
    }
    if (true === isset($search['search_tel'])) {
        // WHERE句に入れる文言を設定する
        $where_list[] = 'tel = :tel';
        // BINDする値を設定する
        $bind_array[':tel'] = $search['search_tel'];
    }
    //
    if (true === isset($search['search_birth_from'])) {
        // WHERE句に入れる文言を設定する
        $where_list[] = 'birth >= :birth_from';
        // 日付を簡単に整える
        $search['search_birth_from'] = date('Y-m-d', strtotime($search['search_birth_from']));
        // BINDする値を設定する
        $bind_array[':birth_from'] = $search['search_birth_from'];
    }
    //
    if (true === isset($search['search_birth_to'])) {
        // WHERE句に入れる文言を設定する
        $where_list[] = 'birth <= :birth_to';
        // 日付を簡単に整える
        $search['search_birth_to'] = date('Y-m-d', strtotime($search['search_birth_to']));
        // BINDする値を設定する
        $bind_array[':birth_to'] = $search['search_birth_to'];
    }
    //
    if (true === isset($search['search_created'])) {
        // WHERE句に入れる文言を設定する
        $where_list[] = 'created BETWEEN :created_from AND :created_to';
        // 日付を簡単に整える
        $search['search_created'] = date('Y-m-d', strtotime($search['search_created']));
        // BINDする値を設定する
        $bind_array[':created_from'] = $search['search_created'] . ' 00:00:00';
        $bind_array[':created_to'] = $search['search_created'] . ' 23:59:59';
    }

    //
    // like句
    if (true === isset($search['search_like_mname'])) {
      // WHERE句に入れる文言を設定する
      $where_list[] = 'mname LIKE :like_mname';
      //$bind_array[':like_name'] = '%' . $search['search_like_name'] . '%'; // 部分一致の場合
      $bind_array[':like_mname'] = '%' . like_escape($search['search_like_mname']) . '%'; // 部分一致、%や_はエスケープ、の場合
      }
    if (true === isset($search['search_like_furigana'])) {
      // WHERE句に入れる文言を設定する
      $where_list[] = 'furigana LIKE :like_furigana';
      //$bind_array[':like_post'] = '%' . $search['search_like_post'] . '%'; // 部分一致の場合
      $bind_array[':like_furigana'] = '%' . like_escape($search['search_like_furigana']) . '%'; // 部分一致、%や_はエスケープ、の場合
      }
  //
  

    // WHERE句を合成してSQL文につなげる
    $sql = $sql . ' WHERE ' . implode(' AND ', $where_list);

    // XXX 「sort条件」は現在指定の値を持越し。「何かデフォルトでリセットしたい」ような場合はここで$sort変数に適切な値を代入する
}


// ソート条件の付与
// (第一種)ホワイトリストによるチェック
$sql_sort = '';
$sort_list = array (
    'id' => 'id',
    'id_desc' => 'id DESC',
    'mcode' => 'mcode',
    'mcode_desc' => 'mcode DESC',
    'furigana' => 'furigana',
    'furigana_desc' => 'furigana DESC',
    'tel' => 'tel',
    'tel_desc' => 'tel DESC',
    'birth' => 'birth',
    'birth_desc' => 'birth DESC',
    'created' => 'created',
    'created_desc' => 'created DESC',
    'modified' => 'modified',
    'modified_desc' => 'modified DESC',
    'role' => 'role',
    'role_desc' => 'role DESC',
);
if (true === isset($sort_list[$sort])) {
  $sql_sort = ' ORDER BY ' . $sort_list[$sort];
} else {
  // いつまでも「無駄な条件」を持っていても意味がないので、消しておく
  $sort = '';
}

// (検索がない場合は)ページング処理をする
$sql_limit_string = '';

if (true === empty($search)) {
  //
  $sql_limit_string = ' LIMIT :start_page, :contents_per_page';
  $bind_array[':start_page'] = ($page_num - 1) * $contents_per_page; // [ページ数 - 1] * 1Pageあたりの出力数
  $bind_array[':contents_per_page'] = $contents_per_page;
}



// var_dump($sql);
// count用と通常用と、２つのSQLを作成する + SQLを閉じる
$sql_count = 'SELECT count(id) ' . $sql . ';';
$sql_main = 'SELECT * ' . $sql . $sql_sort . $sql_limit_string . ';';
// 確認
// var_dump($sql_count);
// var_dump($sql_main);


$pre_count = $dbh->prepare($sql_count);
$pre_main = $dbh->prepare($sql_main);


// 値のバインドfatal errorがでる
if (false === empty($bind_array)) {
  foreach($bind_array as $k => $v) {
      $pre_count->bindValue($k, $v); // デフォルトのSTRとしておく：「数値が入る」可能性が出てきたら、is_int関数で調べて…という実装もよい
      $pre_main->bindValue($k, $v); // デフォルトのSTRとしておく：「数値が入る」可能性が出てきたら、is_int関数で調べて…という実装もよい
}
}
// 値の確認
//var_dump($bind_array);
// count側、SQL実行
$r = $pre_count->execute();
if (false === $r) {
        // XXX 本当はもう少し丁寧なエラーページを出力する
        echo 'システムでエラーが起きました';
        exit;
}
// データを取得
$data = $pre_count->fetch();
// 確認
// var_dump($data);
// var_dump($data[0]);

// 「全体の件数」を取得
$total_contents_num = $data[0];
// var_dump($total_contents_num);

// 最大ページ数を把握
// ceil ( 全体でn件 ÷ 1Pageあたりm件 )
$max_page_num = (int)ceil($total_contents_num / $contents_per_page);
//var_dump($max_page_num);

// XXX 指定されたPageが「最大ページ数を超える」場合は、最大ページとする
if ($page_num > $max_page_num) {
    $page_num = $max_page_num;
    // 値のバインドをし直す
    $pre_main->bindValue(':start_page', ($page_num - 1) * $contents_per_page);
}
//var_dump($page_num);

// main側、SQLの実行
$r = $pre_main->execute();
if (false === $r) {
    // XXX 本当はもう少し丁寧なエラーページを出力する
    echo 'システムでエラーが起きました';
    exit;
}

// データをまとめて取得
$data = $pre_main->fetchAll(PDO::FETCH_ASSOC);

//var_dump($data);
// $_SESSION['output_buffer']にデータがある場合は、情報を取得する
if (true === isset($_SESSION['output_buffer'])) {
  $output_buffer = $_SESSION['output_buffer'];
} else {
  $output_buffer = array();
}
//var_dump($output_buffer);

// (二重に出力しないように)セッション内の「出力用情報」を削除する
unset($_SESSION['output_buffer']);

// CSRFトークンの取得
$csrf_token = create_csrf_token_admin();

// sortのAエレメント出力用関数
function a_tag_print($type, $out) {
  if ($type === $GLOBALS['sort']) {
      echo "<a class='bg-danger text-danger' href='./monitor_kaiin_list.php?sort={$type}'>{$out}</a>";
  } else {
      echo "<a class='text-muted' href='./monitor_kaiin_list.php?sort={$type}'>{$out}</a>";
  }
}

?>

<!DOCTYPE HTML>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>会員一覧</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

</head>

<body>
<h2 style="font-size:20px;margin-left:30px;" ><?php echo $mcode . ' ' . $mname . ' さんのページ' . PHP_EOL;?></h2>

<div>
    <hr>
    <?php if($role === 2) : ?>
      <button><a style="text-decoration:none;" href="../psw_login/top2.php">マイページに戻る</a></button>
    <?php elseif($role === 3): ?>
          <button><a style="text-decoration:none;" href="../psw_login/top3.php">マイページに戻る</a></button>
    <?php elseif($role === 4) : ?>
      <button><a style="text-decoration:none;" href="../psw_login/top4.php">マイページに戻る</a></button>
    <?php elseif($role === 5): ?>
          <button><a style="text-decoration:none;" href="../psw_login/top5.php">マイページに戻る</a></button>
    <?php elseif($role === 6): ?>
          <button><a style="text-decoration:none;" href="../psw_login/top6.php">マイページに戻る</a></button>
    <?php elseif($role === 7): ?>
          <button><a style="text-decoration:none;" href="../psw_login/top7.php">マイページに戻る</a></button>
    <?php elseif($role === 10): ?>
          <button><a style="text-decoration:none;" href="../psw_login/top10.php">マイページに戻る</a></button>
    <?php endif; ?>
    </div>
<h1 style='text-align:center;border:solid 2px;width:400px;margin:0 auto;'>会員一覧</h1>
<div style='height:10px;margin-top:30px;'></div>


<div class="container">
<?php if ( (isset($output_buffer['error_csrf']))&&(true === $output_buffer['error_csrf']) ) : ?>
    <span class="text-danger">CSRFトークンでエラーが起きました。正しい遷移を、５分以内に操作してください。<br></span>
<?php endif; ?>

<div class="row">
  <form action="./monitor_kaiin_list.php" method="post">
    <div>
      <!-- <span class="col-md-4">検索する「mcode」<input name="search_mcode" value="<?php echo h(@$search['search_mcode']); ?>"></span> -->
      <span class="col-md-4">検索する「名前」<input name="search_mname" value="<?php echo h(@$search['search_mname']); ?>"></span>
      <span class="col-md-4">検索する「フリガナ」<input name="search_furigana" value="<?php echo h(@$search['search_furigana']); ?>"></span>
    </div>
    <div>
      <span class="col-md-4">検索する「email」<input name="search_email" value="<?php echo h(@$search['search_email']); ?>"></span>
    </div>
    <div>
      <span class="col-md-4">検索する「tel」<input name="search_tel" value="<?php echo h(@$search['search_tel']); ?>"></span>
    </div>
    <div>
      <span class="col-md-8">検索する「誕生日(YYYY-MM-DD)」<input name="search_birth_from" value="<?php echo h(@$search['search_birth_from']); ?>">～<input name="search_birth_to" value="<?php echo h(@$search['search_birth_to']); ?>"></span>
    </div>
    <div>
      <span class="col-md-12">検索する「入力日(YYYY-MM-DD)」<input name="search_created" value="<?php echo h(@$search['search_created']); ?>"></span>
    </div>
    <div>
      <span class="col-md-6">検索する「名前」(部分一致検索)<input name="search_like_mname" value="<?php echo h(@$search['search_like_mname']); ?>"></span>
    </div>
    <div>
      <span class="col-md-6">検索する「フリガナ」(部分一致検索)<input name="search_like_furigana" value="<?php echo h(@$search['search_like_furigana']); ?>"></span>
    </div>
    <span class="col-md-12"><button class="btn btn-default">検索する</button></span>
  </form>
  </div>

  <?php if (false === empty($search)) : ?>
    現在、以下の項目で検索をかけています。<br>
    <?php
        foreach($search as $k => $v) {
            echo h($k), ': ', h($v), "<br>\n";
        }
    ?>
    <br>
    <a class="btn btn-default" href="./monitor_kaiin_list.php">検索項目をクリアする</a>
<?php endif;?>

  <h2>一覧</h2>


<table class = "table table-hover" border= 'solid 3px'>
<!-- <tr>
    <th>フォームID
    <th>名前
    <th>誕生日
    <th>入力日
    <th>修正日 -->

<tr>

    <td><?php a_tag_print('id', '▲'); ?>　<?php a_tag_print('id_desc', '▼'); ?>
    <!-- <td><?php a_tag_print('mcode', '▲'); ?>　<?php a_tag_print('mcode_desc', '▼'); ?> -->
    <td>
    <td><?php a_tag_print('furigana', '▲'); ?>　<?php a_tag_print('furigana_desc', '▼'); ?>
    <td><?php a_tag_print('tel', '▲'); ?>　<?php a_tag_print('tel_desc', '▼'); ?>
    <td>
    <td><?php a_tag_print('birth', '▲'); ?>　<?php a_tag_print('birth_desc', '▼'); ?>
    <td><?php a_tag_print('created', '▲'); ?>　<?php a_tag_print('created_desc', '▼'); ?>
    <td><?php a_tag_print('modified', '▲'); ?>　<?php a_tag_print('modified_desc', '▼'); ?>
    <td><?php a_tag_print('role', '▲'); ?>　<?php a_tag_print('role_desc', '▼'); ?>

<!-- <tr><th>id</th><th>mcode</th><th>名前</th><th>フリガナ</th><th>tel</th><th>email</th><th>生年月日</th><th>入力日時</th><th>更新日時</th><th>区分</th></tr> -->
<tr><th>id</th><th>名前</th><th>フリガナ</th><th>tel</th><th>email</th><th>生年月日</th><th>入力日時</th><th>更新日時</th><th>区分</th></tr>

<?php foreach ($data as $datum): ?>
<tr>
  <td width="20"><?php echo h($datum['id']); ?>
<?php $id = h($datum['id']); ?>

<?php $sqlteiki = 'select * from teiki where id = :id ;'; ?>
<?php $teikis=$dbh->prepare($sqlteiki); ?>
<?php $teikis -> bindValue(':id',$id,PDO::PARAM_INT); ?>
<?php $t = $teikis -> execute(); ?>
<?php if(false === $t) : ?>
  <?php echo 'no'; ?>
<?php else: ?>
  <?php $teiki = $teikis -> fetch(); ?>
  <?php $t_id = $teiki['t_id']; ?>
  <?php $_SESSION['user']['t_id'] = $t_id; ?>
  <?php $shohincd = $teiki['shohincd']; ?>
  <?php $hjunkyocd = $teiki['hjunkyocd']; ?>
  <?php $rackno = $teiki['rackno']; ?>

  <?php $sqlshohin = 'select * from shohinms where shohincd = :shohincd ;'; ?>
  <?php $shohins=$dbh->prepare($sqlshohin); ?>
  <?php $shohins -> bindValue(':shohincd',$shohincd,PDO::PARAM_INT); ?>
  <?php $t = $shohins -> execute(); ?>
  <?php $shohin = $shohins -> fetch(); ?>
  <?php $shohinmei = $shohin['shohinmei']; ?>
  
  <?php $sqljunkyo = 'select * from junkyoms where hjunkyocd = :hjunkyocd ;'; ?>
  <?php $junkyos=$dbh->prepare($sqljunkyo); ?>
  <?php $junkyos -> bindValue(':hjunkyocd',$hjunkyocd,PDO::PARAM_INT); ?>
  <?php $j = $junkyos -> execute(); ?>
  <?php $junkyo = $junkyos -> fetch(); ?>
  <?php $hjunkyomei = $junkyo['hjunkyomei']; ?>
  

  <?php endif ; ?>



  <!-- <td width="40"><?php echo h($datum['mcode']); ?> -->
  <td width="120"><?php echo h($datum['mname']); ?>
  <td width="120"><?php echo h($datum['furigana']); ?>
  <td width="220"><?php echo h($datum['tel']); ?>
  <td width="220"><?php echo h($datum['email']); ?>
  <td width="220"><?php echo h($datum['birth']); ?>
  <td width="160"><?php echo h($datum['created']); ?>
  <td width="160"><?php echo h($datum['modified']); ?>
  <td width="40"><?php echo h($datum['role']); ?>
  <td width="30"><a class="btn btn-default" href="./monitor_kaiin_data_detail.php?id=<?php echo rawurlencode($datum['id']); ?>">詳細</a>
  <td width="30"><a class="btn btn-default" href="./monitor_kaiin_data_update.php?id=<?php echo rawurlencode($datum['id']); ?>">修正</a>
  <td width="30"><a class="btn btn-default" href="../monitor/teiki.php?id=<?php echo rawurlencode($datum['id']); ?>">定期作成・変更</a>
  <td width="120"><?php echo h($shohinmei); ?>
  <td width="120"><?php echo h($hjunkyomei); ?>
  <td width="120"><?php echo h($rackno); ?>
  <td width="30"><form action="./monitor_kaiin_data_delete.php" method="post">
            <input type="hidden" name="id" value="<?php echo h($datum['id']); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo h($csrf_token); ?>">
            <button class="btn btn-danger" onClick="return confirm('本当に削除しますか？');">削除</button>
        </form>
</tr>
<?php endforeach;?>

</table>

<div class="row">
    <?php if (1 != $page_num): ?>
      <a class="btn btn-default" href="./monitor_kaiin_list.php?p=<?php echo ($page_num - 1); ?>&sort=<?php echo rawurlencode($sort); ?>">＜＜前</a>
    <?php endif; ?>

    <?php if ($max_page_num > $page_num): ?>
      <a class="btn btn-default" href="./monitor_kaiin_list.php?p=<?php echo ($page_num + 1); ?>&sort=<?php echo rawurlencode($sort); ?>">次＞＞</a>
    <?php endif; ?>
</div>

<div class="row">
    <ul class="pagination">
    <?php for($i = 1; $i <= $max_page_num; ++$i): ?>
        <?php if($i === $page_num): ?>
            <li class="active"><a href="#" ><?php echo $i; ?></a></li>
        <?php else: ?>
            <li><a href="./monitor_kaiin_list.php?p=<?php echo $i; ?>&sort=<?php echo rawurlencode($sort); ?>"><?php echo $i; ?></a></li>
        <?php endif; ?>
    <?php endfor; ?>
    </ul>
  </div>
  </div>
</div>

</div>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

</body>
</html>