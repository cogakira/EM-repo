
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <select name = "shimebi" form="hinichi">
    <?php for($i=2;$i>=-4;$i--): ?>
            <option value=<?php echo(date('Y-m-d', mktime(0, 0, 0, date('m')   +   ${i}  , 0, date('Y')))); ?>><?php echo(date('Y-m-d', mktime(0, 0, 0, date('m')   +   ${i}  , 0, date('Y')))); ?> </option>
    <?php endfor;   ?>
    </select>
    <form action="test2.php" method=post id="hinichi">

<p><input type="submit" value="送信"></p>
</form>
</body>
</html>
