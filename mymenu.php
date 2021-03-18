<?php
ob_start();

session_start();
// 共通関数のinclude
require_once('../common_function.php');
require_once('common_auth.php');
$email = $_SESSION['email'];


      $vps=$_SESSION['pswd'];
  if($vps == $_POST['one_pswd']){ 

    
    // TopPage(認証後トップページ)に遷移させる
    // header('Location: ./top.php');
    
    switch($_SESSION['auth']['role']){
        case 1:header('Location: ./top.php');
        break;
        case 2:header('Location: ./top2.php');
        break;
        case 3:header('Location: ./top3.php');
        break;
        case 4:header('Location: ./top4.php');
        break;
        case 5:header('Location: ./top5.php');
        break;
        case 7:header('Location: ./top7.php');
        break;
        default:
        header('Location: ./login.php');
        break;
    }
  }else{
    print('やり直してください');
    header('Location:index.php');
    exit();
	}
?>
