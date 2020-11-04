<!DOCTYPE html> 
<html lang="ja"> 
    <head> 
        <meta charset="UTF-8"> 
            <title>mission5-1</title> 
    </head> 
<body> 
 
 
<?php 
 //記入例；以下は (?php から ?)で挟まれるPHP領域に記載すること。
	//4-2以降でも毎回接続は必要。
	//$dsnの式の中にスペースを入れないこと！

	// 【サンプル】
	// ・データベース名：tb220734db
	// ・ユーザー名：tb-220734
	// ・パスワード：3DZFFkvEvQ
	// の学生の場合：
	
 // DB接続設定 
 $dsn = 'mysql:dbname=tb220734db;host=localhost'; 
 $user = 'tb-220734'; 
 $password = '3DZFFkvEvQ'; 
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING)); 
  
  //テーブル作成    
    $sql = "CREATE TABLE IF NOT EXISTS KEIJIBANN2" 
 ." (" 
 . "id INT AUTO_INCREMENT PRIMARY KEY," 
 . "name char(32)," //名前は32文字まで
    . "comment TEXT," //コメントはテキスト表示
    . "datetime timestamp"//日時の表示
 .");"; 
 $stmt = $pdo->query($sql); 
 
    //最初に変数に代入させるとなぜかエラーが出るため、if文内で代入するようにした。 
     
//「投稿フォーム」 
    if(!empty($_POST["name"])&& !empty($_POST["comment"])&& !empty($_POST["pass1"])){//送信されたものがあり、中身が空でないときに以下の処理を行う。 
        $pass= $_POST["pass1"]; 
        if($pass="pass"){ 
            $name = $_POST["name"]; 
            $comment = $_POST["comment"];  
            $datetime = date("Y/m/d H:i:s");  
            if(!empty($_POST["editflag"])){           
                $id = $_POST["editflag"]; //変更する投稿番号を指定する         
                $sql = 'UPDATE KEIJIBANN2 SET name=:name,comment=:comment,datetime=:datetime WHERE id=:id';//日時も更新する 
                $stmt = $pdo->prepare($sql); 
                $stmt->bindParam(':name', $name, PDO::PARAM_STR); 
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR); 
                $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
                $stmt-> bindParam(':datetime', $datetime, PDO::PARAM_STR);                  
                $stmt->execute();                         
            }else{//普通投稿 
                $sql = $pdo -> prepare("INSERT INTO KEIJIBANN2 (name, comment, datetime) VALUES (:name, :comment, :datetime)"); 
                $sql -> bindParam(':name', $name, PDO::PARAM_STR); 
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR); 
                $sql -> bindParam(':datetime', $datetime, PDO::PARAM_STR);                                   
                $sql -> execute(); 
            } 
        } 
    }    
     
     
//「削除フォーム」 
    if (!empty($_POST["delete"])&& !empty($_POST["pass2"])) {//送信されたものがあり、中身が空でないときに以下の処理を行う。 
        $pass= $_POST["pass2"]; 
        if($pass="pass"){   
            $id = $_POST["delete"]; 
            $sql = 'delete from KEIJIBANN2 where id=:id'; 
            $stmt = $pdo->prepare($sql); 
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
            $stmt->execute();                    
        } 
    }  
 
//「編集フォーム」（投稿フォームへ飛ばす）    
    if (!empty($_POST["edit"])&& !empty($_POST["pass3"])) {//送信されたものがあり、中身が空でないときに以下の処理を行う。 
        $pass= $_POST["pass3"]; 
        if($pass="pass"){ 
            $edit=$_POST["edit"];//名前、コメントを飛ばすのは、投稿フォームで行うことにした。改善の余地あり。                                           
        }         
    } 
 
?>   
 
<form method= "post" action="mission5-1-1.php"> 
【投稿フォーム】<br> 
<input type="text" name="name" placeholder="名前"   
value="<?php  
if(isset($edit)){$id = $edit ; //投稿表示機能のコードを範囲を狭めて入れた（mission4-6補足参照）。改善の余地あり。 
$sql = 'SELECT * FROM KEIJIBANN2 WHERE id=:id '; 
$stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、 
$stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、 
$stmt->execute();                             // ←SQLを実行する。 
$results = $stmt->fetchAll();  
 foreach ($results as $row){ 
  echo $row['name']; 
    } 
}?>" ><br> 
 
<input type="text" name="comment" placeholder="コメント"
value="<?php  
if(isset($edit)){$id = $edit ; 
$sql = 'SELECT * FROM KEIJIBANN2 WHERE id=:id '; 
$stmt = $pdo->prepare($sql);                  
$stmt->bindParam(':id', $id, PDO::PARAM_INT); 
$stmt->execute();                             
$results = $stmt->fetchAll();  
 foreach ($results as $row){ 
  echo $row['comment']; 
    } 
}?>" > 
<!--編集用の見えないテキストボックス--> 
<input type="hidden" name="editflag" value="<?php if(isset($edit)){echo $edit;}?>" ><br> 
 
<input type="text" name="pass1" placeholder="パスワード" value="" ><br> 
 <input type="submit" value="送信"> 
 </form> 
  
 <form method= "post" action="mission5-1-1.php"> 
<br>【  削除フォーム  】<br> 
  <input type = "text" name = "delete" placeholder="投稿番号" ><br> 
  <input type="text" name="pass2" placeholder="パスワード" value="" ><br> 
  <input type = "submit" value="削除" ><br> 
</form> 
 
 <form method= "post" action="mission5-1-1.php"> 
<br>【  編集フォーム  】<br> 
  <input type = "text" name = "edit" placeholder="編集番号" ><br> 
  <input type="text" name="pass3"  placeholder="パスワード" value="" ><br> 
  <input type = "submit" value="編集" ><br><br> 
</form> 
 
 
【投稿一覧】<br> 
 
<?php 
    //データベースに書き込まれた全ての投稿をブラウザに表示 
 $sql = 'SELECT * FROM KEIJIBANN2'; 
 $stmt = $pdo->query($sql); 
 $results = $stmt->fetchAll(); 
 foreach ($results as $row){ 
  echo $row['id'].','; 
  echo $row['name'].','; 
        echo $row['comment'].','; 
        echo $row['datetime']; 
     echo "<hr>"; 
 } 
?>   
 
 
 
</body>
</html>