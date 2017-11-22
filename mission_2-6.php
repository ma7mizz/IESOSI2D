<?php
$time = time();
$filename = 'kadai2-6.txt';
$err_msg1 = "";
$err_msg2 = "";
$err_msg3 = "";
$message = "";
$name = (isset($_POST["name"]) == true) ? $_POST["name"]: "";
$comment = (isset($_POST["comment"]) == true) ? trim($_POST["comment"]) : "";
$pass = (isset($_POST["pass"]) == true) ? $_POST["pass"]: "";
$content = file("kadai2-6.txt");

if(isset($_POST["send"]) == true){//コメント送信時
    if($name == "") $err_msg1 = "名前を入力してください";
    if($comment == "") $err_msg2 = "コメントを入力してください";
    if($pass == "") $err_msg3 = "パスワードを入力してください";
    if($err_msg1=="" && $err_msg2=="" && $err_msg3 == ""){//正しく入力された時
            $lines = file($filename);
            $count = count($lines)-1;
            $boards = explode("◇",$lines[$count]);
            $numb = $boards[0]+1;
            $fp = fopen($filename,'a');
            fwrite($fp,$numb);
            fwrite($fp,"◇");
            fwrite($fp,$_POST["name"]);
            fwrite($fp,"◇");
            fwrite($fp,$_POST["comment"]);
            fwrite($fp,"◇");
            fwrite($fp,date("Y/m/d G:i:s"),$time);
            fwrite($fp, "◇");
            fwrite($fp, $_POST["pass"]);
            fwrite($fp, "◇");
            fwrite($fp, "\n");
            fclose($fp);
        
        $message = "書き込みに成功しました";
    }
        
}

// コメントを配列に格納する
$fp = fopen($filename,"r");

$dataArr = array();
while($res = fgets($fp)){
    $boards = explode("◇",$res);
    $arr = array("numb"=>$boards[0],"name"=>$boards[1],"comment"=>$boards[2],"time"=>$boards[3],"pass"=>$boards[4]);
    $dataArr[] = $arr;
}

// コメント削除
$dlt_n = -1;
if (isset($_POST['delete_btn'])){
    $dlt_n = $_POST['delete_res'];
//    echo "削除したい番号：";
//    echo $dlt_n;
//    echo "\n";
}
if(($_POST['delete_pass'])){
    $dlt_p = $_POST['delete_pass'];
//    echo "削除したい番号のパスワード：";
//    echo $dlt_p;
//    echo "\n";
}
    $lines = file($filename);
    $numb = count($lines);

if($dlt_n > 0){
    
    $fp2 = fopen($filename,'w+');
    for($i=0; $i<$numb; $i++){
        $boards = explode("◇",$lines[$i]);
        $l_num = $boards[0];
        $l_pas = $boards[4];
    //    echo "番号:"; echo $l_num; echo "\n";
    //    echo "パス:"; echo $l_pas; echo "\n";
        if($l_num == $dlt_n){
    //        echo "削除したい番号の照らし合わせ(ok)\n";
            if($dlt_p == $l_pas){
    //            echo "パスワードの照らし合わせ(ok)\n";
                fwrite($fp2, $lines[$i]);
                unset($lines[$l_num-1]);
                file_put_contents($filename, $lines);
            }else{
                echo "パスワードが違います。<br>";
                fwrite($fp2, $lines[$i]);
                
            }
        }
        /*    fwrite($fp, $lines[$i]);
            unset($lines[$n-1]);
            file_put_contents($filename, $lines);
            */
        
    }
    fclose($fp2);


}


//変更
if(isset($_POST["submit"]) && isset($_POST["id"])){
    //echo $_POST["id"];
  $contents = file('kadai2-6.txt');
  $fp1 = fopen($filename,'w');
  $change_n =  $_POST["id"];
  foreach($lines as $content) {
    $parts = explode("◇", $content);
    if($parts[0]==$change_n && $parts[4]==$_POST['c_p']){
      $name = $_POST["name"];
      $comment = $_POST["comment"];
      $timestamp = date("Y/m/d H時i分s秒");
        
        fwrite($fp1, $change_n);
        fwrite($fp1,"◇");
        fwrite($fp1, $_POST["new_name"]);
        fwrite($fp1, "◇");
        fwrite($fp1, $_POST["new_comment"]);
        fwrite($fp1, "◇");
        fwrite($fp1, date("Y/m/d G:i:s\n"),$time);

    } else if($parts[0]==$change_n && $parts[4]!=$_POST['c_p']){
        echo "パスワードが違います。<br>";
        echo $parts[4];//正しいパスワード
        echo $_POST['c_p'];//入力したパスワード
    } else {
        
      fwrite($fp1, "$content");
        
    }
  }
  fclose($fp1);
}

  if(isset($_POST["change_btn"])){  //編集ボタンが押されたら
    if($_POST["change_n"]){
      $change_n =  $_POST["change_n"];
      foreach ($lines as $content){
        $parts = explode("◇", $content);
        if($parts[0] == $change_n){
?>
<form action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post">
  <input type="hidden" name="id" value="<?= $parts[0] ?>">
  <table>
    <tr><td>名前:</td>
    <td><input type="text" name="new_name" value="<?= $parts[1] ?>"></td></tr>
    <tr><td>コメント:</td>
    <td><textarea name="new_comment" cols="30" rows="5"><?= $parts[2] ?></textarea></td></tr>
    <tr><td>パスワード:</td>
    <td><input type="text" name="c_p"></td></tr>
    <tr><td><input type="submit" name="submit" value="送信"></td></tr>
  </table>
</form>
<?php
        }
      }
    } else {
      echo "編集する番号を入力してください！";
    }
  }

?>


<html>
<head>
    <?php header("Content-Type: text/html;charset=UTF-8");?>
<title>mission_2-6.php</title>
</head>
    <body>
    <?php echo $message; ?>
    <form method = "post" action="<?php echo($_SERVER['PHP_SELF']) ?>">
        名前: <br><input type="text" name="name" value="<?php echo $name; ?>">
        <?php echo $err_msg1; ?><br>
        コメント: <br><textarea name="comment" rows="4" cols="40"><?php echo $comment; ?></textarea>
        <?php echo $err_msg2; ?><br>
        パスワード: <br><input type="text" name="pass" value="<?php echo $pass; ?>">
        <?php echo $err_msg3; ?><br>
        <br>
        
        <input type="submit" name="send" value="送信">
        </form>
        
        <form method="post" action="<?php echo($_SERVER['PHP_SELF']) ?>">
    削除するコメントの行番号: <br><input name="delete_res" type="number"><br>
    
    パスワード: <br><input type="text" name="delete_pass"> 
    <input type="submit" name="delete_btn" value="送信">
        </form>
        
        <form method="post" action="<?php echo($_SERVER['PHP_SELF']) ?>">
    編集するコメントの行番号:<br><input type="text" name="change_n" placeholder="例)1" value="<?= isset($_POST['change_n']) ? $_POST['change_n'] : null ?>">
  <input type="submit"  name="change_btn" value="編集する">
  <input type="hidden"  name="change" value="hensyu">
</form>
        
        <dl>
            <?php 
            $lines = file($filename);
            for($i=0; $i<count($lines); $i++){
                $boards = explode("◇", $lines[$i]);
                $number = $boards[0];
                $name = $boards[1];
                $cm = $boards[2];
                $tim = $boards[3];
                echo "投稿番号：$number<br/>";
                echo "投稿者　：$name<br/>";
                echo "コメント：$cm<br/>";
                echo "投稿時間：$tim<br/><br/>";
            }
            ?>
        </dl>
    </body>
</html>