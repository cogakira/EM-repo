<?php

// define('DB_DATABASE','cog_sys');
define('DB_DATABASE','cog');
define('DB_USERNAME','root');
define('DB_PASSWORD','root');
define('PDO_DSN','mysql:host=localhost;dbname=' . DB_DATABASE);

$opt = array (
  PDO::ATTR_EMULATE_PREPARES => false,
);

try {
  $db = new PDO(PDO_DSN,DB_USERNAME,DB_PASSWORD,$opt);
  // $db=new PDO('mysql:dbname=cog_sys;host=localhost;charset=utf-8','root','root');
}catch (PDOException $e){
  print('接続エラー:' . $e->getMessage());
}

?>