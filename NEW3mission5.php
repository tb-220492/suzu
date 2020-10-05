<!DOCTYPE html>
<html lang="ja">
    
<head>
    <meta chaeset="UTF-8">
    <title>mission3-04</title>
    <style>
        form{
            margin-top:10px;
        }
    </style>
</head>

<body>
    
    <?php
    
     //発生するエラーをtry-catch構文で検知し、エラーが起こった場合に所定の処理を行なわせる 	
    try{
        //mission4-1 データベース設定
        $dsn = 'mysql:dbname=tb220492db;host=localhost';
        $user = 'tb-220492';
    	$pass = 'rpDKWZT68f';
    	$pdo = new PDO($dsn, $user, $pass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        // 	array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        // 	→データベース操作でエラーが発生した場合に警告（Worning: ）として表示するために設定するオプション
        
        // $pdoが接続できなかったら
         if($pdo != true){
            echo"データベースの接続に失敗しました";
         }
    
        // mission4-2  このデータベースサーバに、データを登録するための「テーブル」を作成
        // 	「 IF NOT EXISTS 」は「もしまだこのテーブルが存在しないなら」という意味
    	$sql = "CREATE TABLE IF NOT EXISTS lesson"
    	." ("
        // 	以下"カラム名 型"
       // 	id ・自動で登録されていうナンバリング
	    . "id INT AUTO_INCREMENT PRIMARY KEY,"
        // 	name ・名前を入れる。文字列、半角英数で32文字まで
	    . "name char(32),"
        // 	comment ・コメントを入れる。文字列、長めの文章も入る
	    . "comment TEXT,"
	   // 日付
	    . "date DATETIME,"
	   // パスワード
	    ."password varchar(8)"
	    .");";
	   // データベース管理システムに対する問合せ(＝クエリ)に$pdoを格納したものを$stmtとします.
	   //$stmt = とすべきは、実行後にSQLの実行結果に関する情報を得たい場合であり、
	   //ただSQLを実行するだけであれば$db->query($sql);のように書けばよい
	    $stmt = $pdo->query($sql);
	
    // $eはException（例外）を受けるための任意の変数	
    } catch ( PDOException $e ) {
        print( "接続エラー:". $e->getMessage() );
        die();
    }
    
    
    // 新規入力
    if(!empty($_POST['name']) && !empty($_POST['comment']) && empty($_POST['edit_num'])){
        // 入力フォームからデータを取得
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $date = date("Y/m/d H:i:s");
        $pass1 = $_POST["pass1"];
        
        // パスワードが入力されたら
        if(!empty($pass1)){
            // データベースに書き込み
            $sql = $pdo -> prepare("INSERT INTO lesson (name, comment, date, password) 
                                    VALUES (:name, :comment, :date, :password)");
	        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
	        $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	        $sql -> bindParam(':date', $date, PDO::PARAM_STR);
	        $sql -> bindParam(':password', $pass1, PDO::PARAM_STR);
    	    $sql -> execute();  
    	    
    	 // パスワードが入力されなかったら
        }elseif(empty($pass1)){
            print"パスワードが入力されていません";
        }
        
    // 削除
    }elseif(!empty($_POST['delete']) && !empty($_POST["pass2"])){
        
        // 変数の定義
        $delete = $_POST['delete'];
        $pass2 = $_POST["pass2"];
        $id = $delete;
        
        // データを選択
        $sql = 'SELECT * FROM lesson where id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
        $stmt -> execute();
        $results = $stmt -> fetchAll();
        
        // テーブル内のデータからパスワードだけ引っ張ってくる
        foreach($results as $row){
            $DEL_pass = $row["password"];
        }
        
        // 削除パスワードが引っ張ってきたパスワードと等しいとき
        if($pass2 == $DEL_pass){
            // 任意のレコードを削除するため、WHERE句に条件式を設定
            $sql = 'DELETE FROM lesson where id = :id';
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
            $stmt -> execute();
            
        // 削除パスワードが引っ張ってきたパスワードと等しくなかったら
        }elseif($DEL_pass != $pass2){
            print"パスワードが間違っています";
        }
        
        
    // 編集
    }elseif (!empty($_POST['edit']) && !empty($_POST["pass3"])){
        
        $edit = $_POST['edit'];
        $pass3 = $_POST["pass3"];
        $id = $edit;
        
        // データを選択
        $sql = 'SELECT * FROM lesson where id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
        $stmt -> execute();
        $results = $stmt -> fetchAll();
        
        
        // テーブル内のデータからパスワードだけ引っ張ってくる
        foreach($results as $row){
            $EDIT_name = $row["name"];
            $EDIT_comment = $row["comment"];
            // $edit_date = $row["date"];
            $EDIT_pass = $row["password"];
            $EDIT_num = $row["id"];
        }
        
        // ?編集パスワードが引っ張ってきたパスワードと等しいとき
        if($pass3 == $EDIT_pass){
            $edit_name = $EDIT_name;
            $edit_comment = $EDIT_comment;
            $edit_pass = $EDIT_pass;
            $edit_num = $EDIT_num;
            
        // 編集パスワードが引っ張ってきたパスワードと等しくなかったら
        }elseif($EDIT_pass != $pass3){
            print"パスワードが間違っています";
        }
        
        
    // ?編集投稿
    }if(!empty($_POST['name']) && !empty($_POST['comment'])
        && !empty($_POST['edit_num']) && !empty($_POST["pass1"])){
        
        $name = $_POST["name"];
        $comment = $_POST["comment"];
        $date = date("Y/m/d H:i:s");
        $pass1 = $_POST["pass1"];
        $id = $_POST["edit_num"];
        
        $sql = "UPDATE lesson SET name =:name,comment =:comment,date =:date,password=:password where id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
        $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
        $stmt -> bindParam(':password', $password, PDO::PARAM_STR);
        $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
        $stmt -> execute(); 
            // $new_name = $_POST['name'];
            // $new_comment = $_POST['comment'];
            // $new_num = $_POST['edit_num'];
            // $date = date("Y/m/d H:i:s");
            // $new_pass1 = $_POST["pass1"];
            // $edit_contents = $new_num.'<>'.$new_name.'<>'.$new_comment.'<>'.$date."<>".$new_pass1."<>";
    }
    
    
    
        

    ?>
    
    
    <!--入力フォーム-->
    <form action="" method="post" name="write">
        <input type="text" name="name" placeholder="名前" 
               value="<?php if(isset($edit_name)){echo $edit_name;} ?>"><br>
        <input type="text" name="comment" placeholder="コメント"  size="50" 
               value="<?php if(isset($edit_comment)){echo $edit_comment;} ?>"><br>
        <input type="hidden" name="edit_num" 
               value="<?php if(isset($edit_num)){echo $edit_num;} ?>">
        <input type="password" name="pass1" placeholder="パスワード">
        <input type="submit" name="submit">    
    </form>
    <!--削除フォーム-->
    <form action="" method="post">
        <input type="number" name="delete" placeholder="削除対象番号"><br>
        <input type="password" name="pass2" placeholder="パスワード">
        <input type="submit" name="submit2" value="削除">
    </form>
    <!--編集フォーム-->
    <form action="" method="post">
        <input type="number" name="edit" placeholder="編集対象番号"><br>
        <input type="password" name="pass3" placeholder="パスワード">
        <input type="submit" name="submit3" value="編集">
    </form>
    
    <?php
        // ブラウザに表示するもの(mission4-6)
        $sql = 'SELECT * FROM lesson';
	    $stmt = $pdo->query($sql);
	    $results = $stmt->fetchAll();
	    foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		    echo $row['id'].'<br>';
		    echo $row['name'].'<br>';
		    echo $row['comment'].'<br>';
		    echo $row['date'].'<br>';
	        echo "<hr>";
	    }
    ?>
 
 </body>
 </html>