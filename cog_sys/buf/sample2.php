<?php
ob_start();
session_start();
$abc = $_SESSION['kubun'];
var_dump($abc);


$cont=$_POST['kubun'];
var_dump($cont);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="monitor.css">
    <title>1回目の変更ですテスト</title>
</head>
<body>
    <p class="monitor">asdf<?= $cont ?></p>
 
</body>
</html>