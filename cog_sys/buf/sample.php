<?php
ob_start();
session_start();
unset($_SESSION['kubun']);

$kubun = 1;
$_SESSION['kubun'] = $kubun;
echo($_SESSION['kubun']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="monitor.css">
    <title>akira</title>
</head>
<body>
    <p class="monitor">aiueo</p>
 <form action="./sample2.php" method="post">
     <input type=text name='kubun'>
</form>
</body>
</html>