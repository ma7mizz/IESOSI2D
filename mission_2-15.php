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

$dsn = 'データベース名';
$username = 'ユーザー名';
$password = 'パスワード';
$table_name = 'bbs_5';

try{
    $pdo = new PDO($dsn,$username,$password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql ="CREATE table IF NOT EXISTS {$table_name}(
    id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
    name VARCHAR(32) NOT NULL,
    comment VARCHAR(1000) NOT NULL,
    pass VARCHAR(32),
    time VARCHAR(50));";
    $pdo->exec($sql);
    
    }catch(PDOException $e){
    echo '接続に失敗しました：'. $e->getMessage();
}
                        
    
    
?>
    

<html>
<head>
    <?php header("Content-Type: text/html;charset=UTF-8");?>
<title>mission_2-15.php</title>
</head>
    <body>
        <h2>新投稿フォーム</h2>
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
        
        <?php
        
            if(isset($_POST["send"]) == true){//コメント送信時
                if($name == "") $err_msg1 = "名前を入力してください";
                if($comment == "") $err_msg2 = "コメントを入力してください";
                if($pass == "") $err_msg3 = "パスワードを入力してください";
                if($err_msg1=="" && $err_msg2=="" &&       $err_msg3 == ""){//正しく入力された時

                try{
    $pdo = new PDO($dsn,$username,$password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);        
                    $sql = "insert into {$table_name} ( name, comment, time, pass) VALUES ( :name, :comment, :time, :pass)";
                    $result = $pdo->prepare($sql);
                        
                    if($result == true){
                        
                        $result->bindParam(':name',$name,PDO::PARAM_STR);
                        $result->bindParam(':comment',$comment,PDO::PARAM_STR);
                        $result->bindParam(':time',date("Y/m/d G:i:s"),PDO::PARAM_STR);
                        $result->bindParam(':pass',$pass,PDO::PARAM_STR);
                        $result->execute();
                    }else{
                        echo '投稿できませんでした<br>';
                    }
                }catch(PDOException $e){
                    exit('ERROR!_1 : '.$e->getMessage());
                }
                $message = "書き込みに成功しました";
                }
            }
        ?>
        
        <h2>削除フォーム</h2>
        <form method="post" action="<?php echo($_SERVER['PHP_SELF']) ?>">
    削除するコメントの行番号: <br><input name="delete_res" type="number"><br>
    
    パスワード: <br><input type="text" name="delete_pass"> 
    <input type="submit" name="delete_btn" value="送信">
        </form>
        
<?php        
        // コメント削除
if (isset($_POST['delete_btn'])){
    $dlt_n = $_POST['delete_res'];
}
if(isset($_POST['delete_pass'])){
    $dlt_p = $_POST['delete_pass'];
}

if($dlt_n > 0){
    
    $sql_pass = "select * from {$table_name} where id=:id"; //まずパスワードの取得
    $result_pass = $pdo->prepare($sql_pass);
    if($result_pass == true){
        $result_pass->bindValue(':id',$dlt_n,PDO::PARAM_INT);
        $result_pass->execute();
        while($row = $result_pass->fetch(PDO::FETCH_ASSOC)){
            $passcheck = $row['pass'];
        }
    }else{
        echo 'パスワードが違います<br>';
    }
    if($passcheck == $dlt_p){//ここから削除
                    $sql_dlt = "delete from {$table_name} where id=:id";
                    $result_dlt = $pdo->prepare($sql_dlt);
                    if($result_dlt == true){
                        $result_dlt->bindValue(':id',$dlt_n,PDO::PARAM_INT);
                        $result_dlt->execute();
                        echo '削除しました<br>';
                    }else{
                        echo '削除できませんでした<br>';
                    }
                }else{
                    echo 'パスワードが違います<br>';
    }
}

?>
        
        <h2>編集フォーム</h2>
        <form method="post" action="<?php echo($_SERVER['PHP_SELF']) ?>">
    編集するコメントの行番号:<br><input type="text" name="change_n" placeholder="例)1" value="<?= isset($_POST['change_n']) ? $_POST['change_n'] : null ?>">
  <input type="submit"  name="change_btn" value="編集する">
  <input type="hidden"  name="change" value="hensyu">
</form>
        <?php 
  if(isset($_POST["change_btn"])){  //編集ボタンが押されたら
    if($_POST["change_n"]){
        $change_n =  $_POST["change_n"];
        $sql = "select * from {$table_name} where id=:id";
        $result = $pdo->prepare($sql);
        if($result == true){
            $result->bindValue(':id',$change_n,PDO::PARAM_INT);
            $result->execute();
            while($row = $result->fetch(PDO::FETCH_ASSOC)){
            if($change_n == $row['id']){
?>
<form action="<?php echo($_SERVER['PHP_SELF']) ?>" method="post">
  <input type="hidden" name="change_n" value="<?= $row['id'] ?>">
  <table>
    <tr><td>名前:</td>
    <td><input type="text" name="new_name" value="<?= $row['name'] ?>"></td></tr>
    <tr><td>コメント:</td>
    <td><textarea name="new_comment" cols="30" rows="5"><?= $row['comment'] ?></textarea></td></tr>
    <tr><td>パスワード:</td>
    <td><input type="text" name="c_p"></td></tr>
    <tr><td><input type="submit" name="change_sub" value="送信"></td></tr>
  </table>
</form>
<?php
        }
      }
    } else {
      echo "編集する番号を入力してください！";
    }
  }
  }
 
 
 if (isset($_POST['change_sub'])){
    $change_n = $_POST['change_n'];
}
if(isset($_POST['c_p'])){
    $change_p = $_POST['c_p'];
}

if($change_n > 0){
    $new_name = $_POST['new_name'];
    $new_comment = $_POST['new_comment'];
    echo $new_name.'<br>';
    
    $sql_pass = "select * from {$table_name} where id=:id"; //まずパスワードの取得
    $result_pass = $pdo->prepare($sql_pass);
    if($result_pass == true){
        $result_pass->bindValue(':id',$change_n,PDO::PARAM_INT);
        $result_pass->execute();
        while($row = $result_pass->fetch(PDO::FETCH_ASSOC)){
            $passcheck = $row['pass'];
        }
    }else{
        echo 'パスワードが違います<br>';
    }
 
    if($passcheck == $change_p){//ここから編集
        $sql_change = "update {$table_name} set id=:id, name=:name, comment=:comment, time=:time, pass=:pass where id=:id";
        $result_change = $pdo->prepare($sql_change);
        if($result_change == true){
            //echo $new_name.'<br>';
            $result_change->bindValue(':id',$change_n,PDO::PARAM_INT);
            $result_change->bindParam(':name', $new_name, PDO::PARAM_STR);
            $result_change->bindParam(':comment', $new_comment, PDO::PARAM_STR);
            $result_change->bindParam(':time', date("Y/m/d G:i:s"),PDO::PARAM_STR);
            $result_change->bindParam(':pass',$change_p,PDO::PARAM_STR);
            $result_change->execute();
            echo '編集されました<br>';
        }else{
            //echo '!<br>';
        }

    }else{
        //echo 'パスワードが違います<br>';
        //echo '正しい：'.$passcheck.'<br>';
        //echo '間違え：'.$_POST["c_p"].'<br>';
    }
    }else{
        //echo'change_n= '.$change_n.'<br>';
    }

              
              ?>
        
        <h1>BBS</h1>
        <?php
        try{
            $pdo = new PDO($dsn, $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "select * from {$table_name} order by id desc";
            $result = $pdo->query($sql);
            if($result == true){
                while($row = $result->fetch(PDO::FETCH_ASSOC)){
                    echo 'No.'.$row['id']." name: ".$row['name'].'<br>';
                    echo $row['comment'].'<br>';
                    echo $row['time'].'<br>';
                    //echo $row['pass'].'<br>';
                }
            }else{
            echo '表示を失敗しました<br>';
            }
        }catch(PDOException $e){
            exit('ERROR! : '.$e->detMessage());
        }
        ?>
        
    </body>
</html>