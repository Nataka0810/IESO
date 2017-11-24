<!DOCTYPE html>
<html lang="en">
<html>

<head>
	<meta charset="UTF-8"/>
	<title>掲示板</title>
</head>

<body>
<!-- 前処理 -->
<?php
	header('X-Content-Type-Options: nosniff');

	session_start();

	if(!isset($_SESSION['enter'])) {
		header('Location: ../login/login.php');
		exit();
	}

	if($_SERVER['REQUEST_METHOD'] === 'POST') {
		header('Location: MessageBoard.php');
	}

	echo "ユーザー情報"."<br>\n";
	echo $_SESSION['enter']['id']."<br>";
	//echo $_SESSION['enter']['password']."<br>";

	$dsn = 'データベース名'; //データべ―ス名とホスト名
	$user = 'ユーザー名'; //ユーザー名
	$password = 'パスワード'; //パスワード

	try {
		$dbh = new PDO($dsn, $user, $password); //データベースに接続

		//print('接続成功<br>');

		$user = 'users';	//ユーザーテーブル

		$table = 'messageBoard'; //メッセージテーブル

		$image = 'image'; //イメージテーブル
	}
	catch (PDOException $e) {
		print('Error:'.$e->getMessage());
		die();
	}

	$sql = "CREATE TABLE IF NOT EXISTS $table (id int(11) not null primary key auto_increment,
		name varchar(255) NOT NULL,
		comment varchar(1000),
		date varchar(25) NOT NULL
		) DEFAULT CHARACTER SET utf8";	//テーブル作成

	$dbh->query($sql); //SQL文

	$sql = "CREATE TABLE IF NOT EXISTS $image (id int(11) not null,
		name varchar(255) NOT NULL,
		type varchar(10) NOT NULL,
		ext varchar(15) NOT NULL,
		raw_data mediumblob NOT NULL,
		thumb_data blob
		) DEFAULT CHARACTER SET utf8";

	$dbh->query($sql); //SQL文

	//echo "ユーザー情報"
	$sql = "SELECT * FROM $user WHERE id = \"".$_SESSION['enter']['id']."\"";
	//echo "SQL文 : ".$sql."<br>";

	$result = $dbh->query($sql);
	if (!$result) {
	    die('クエリ―が失敗しました。'.mysql_error());
	}

	foreach ($result as $data) {
		$_SESSION['name'] = $data['name'];
	}

	echo "投稿"."<br>\n";
	if(isset($_POST['name']) && !isset($_POST['edit'])) {
		if (empty($_POST['name'])) {
			echo "名前が未入力です。"."<br>\n";
		}
		else {
			if($_SESSION['name'] == $_POST['name']) {
				$name = $_POST['name'];
				$comment = $_POST['comment'];
				$date = $date = date("Y/m/d H:i:s");
				unset($_POST['name']);
				unset($_POST['comment']);
				
				$sql = 'INSERT INTO '.$table.'(name, comment, date)
				 	VALUES("'.$name.'", "'.$comment.'", "'.$date.'")';
				//echo "SQL文 : ".$sql."<br>";

				$result = $dbh->query($sql);
				if (!$result) {
				    die('クエリ―が失敗しました。'.mysql_error());
				}

				echo "INSERT 成功<br>";

				//アップローダ
				if($_FILES)
				{
					$name = $_FILES['upfile']['name'];
					//echo $name."<br>";
					$type = $_FILES['upfile']['type'];
					//echo $type."<br>";
					$size = $_FILES['upfile']['size'];
					//echo $size."<br>";
					$tmp_name = $_FILES['upfile']['tmp_name'];
					//echo $tmp_name."<br>";
					$error = $_FILES['upfile']['error'];
					//echo $error."<br>";

					switch($_FILES['upfile']['type']) {
						case 'image/jpeg': $ext = 'jpg'; break;
						case 'image/gif':  $ext = 'gif'; break;
						case 'image/png':  $ext = 'png'; break;
						case 'image/tiff': $ext = 'tif'; break;
						case 'video/mp4':  $ext = 'mp4'; break;
						default:	   $ext = '';	 break;
					}
					if($ext) {
						$sql = 'select LAST_INSERT_ID() id';
						$result = $dbh->query($sql);
						foreach ($result as $data) {
						    $id = $data['id'];
						}
						$id = intval($id);

						$imgdat = file_get_contents($tmp_name);
						$imgdat = mysql_real_escape_string($imgdat);

						$sql = 'INSERT INTO '.$image.'(id, name, type, ext, raw_data)
						 	VALUES('.$id.', "'.$name.'", "'.$type.'", "'.$ext.'", "'.$imgdat.'")';
						//echo "SQL文 : ".$sql."<br>";

						$result = $dbh->query($sql);
						if(!result) {
							die('クエリ―が失敗しました。'.mysql_error());
						}
					}else {
						echo "このタイプはアップロードできません。";
					}
					unset($_FILES['upfile']);
					unset($_FILES);
				}
				else {
					echo "ファイルは未入力です。";
				}
			}
			else {
				echo "ユーザー名が一致しません。<br>";
			}
		}
	}

	echo "<br>削除対象"."<br>\n";
	if(isset($_POST['number'])) {
		if (empty($_POST['number'])) {
			echo "番号が未入力です。"."<br>\n";
		}
		else {			
			$num = intval($_POST['number']);
			unset($_POST['number']);

			$sql = "SELECT * FROM $table WHERE id = $num";
			echo "SQL文 : ".$sql."<br>";

			$result = $dbh->query($sql);
			if (!$result) {
			    die('クエリ―が失敗しました。'.mysql_error());
			}

			foreach ($result as $data) {
				$name = $data['name'];
			}

			if($_SESSION['name'] == $name) {
				$sql = "DELETE FROM $table WHERE id = $num";
				
				echo "SQL文 : ".$sql."<br>";
				
				$result = $dbh->query($sql);
				if (!$result) {
				    die('クエリ―が失敗しました。'.mysql_error());
				}

				$sql = "DELETE FROM $image WHERE id = $num";
				
				echo "SQL文 : ".$sql."<br>";
				
				$result = $dbh->query($sql);
				if (!$result) {
				    die('クエリ―が失敗しました。'.mysql_error());
				}

				echo "DELETE 成功<br>";
			}
			else {
				echo "あなたが投稿したものではありません。<br>";
				echo "DELETE 失敗<br>";
			}
			unset($name);
		}
	}

	echo "<br>編集対象"."<br>\n";
	if(isset($_POST['number2'])) { //編集対象番号
		if (empty($_POST['number2'])) {
			echo "番号が未入力です。"."<br>\n";
		}
		else {
			$num = intval($_POST['number2']); //編集対象番号
			unset($_POST['number2']);

			$sql = "SELECT * FROM $table WHERE id = $num";
			echo "SQL文 : ".$sql."<br>";

			$result = $dbh->query($sql);
			if (!$result) {
			    die('クエリ―が失敗しました。'.mysql_error());
			}

			foreach ($result as $data) {
				$name = $data['name'];
			}

			if($_SESSION['name'] == $name) {
				$sql = "SELECT * FROM $table WHERE id = $num";
				echo "SQL文 : ".$sql."<br>";

				$result = $dbh->query($sql);
				if (!$result) {
				    die('クエリ―が失敗しました。'.mysql_error());
				}

				foreach ($result as $data) {
				    $comment = $data['comment'];
				}
				
				$_SESSION['comment'] = $comment;
				$_SESSION['number'] = $num;
				unset($comment);
				unset($num);
			}
			else {
				echo "あなたが投稿したものではありません。<br>";
				echo "UPDATE 失敗<br>";
			}
			unset($name);
		}
	}

	echo "<br>編集作業"."<br>\n";
	if(isset($_POST['edit'])) { //入力済みとしてセッションに記憶
		$_SESSION['edit'] = "true";
	}
	
	if ($_SESSION['edit'] == "true") {
		$num = intval($_SESSION['number']); //編集対象番号
		$name = $_POST['name'];
		$comment = $_POST['comment'];
		$date = date("Y/m/d H:i:s");
		unset($_POST['name']);
		unset($_POST['comment']);
		$_SESSION['edit'] = "";
		$_SESSION['comment'] = "";
		$_SESSION['number'] = "";
		
		$sql = 'UPDATE '.$table.' SET 
		name="'.$name.'",
		comment="'.$comment.'",
		date="'.$date.'"
		WHERE id="'.$num.'"';
		
		echo "SQL文 : ".$sql."<br>";
		
		$result = $dbh->query($sql);
		if (!$result) {
		    die('クエリ―が失敗しました。'.mysql_error());
		}
		
		echo "UPDATE 成功<br>";
	}

	unset($_POST);
?>

<div align="left">
<h1>掲示板 mission3_10</h1>
<form enctype = "multipart/form-data" action = "MessageBoard.php" method = "post">
	<table border="0" cellspacing="2" cellpadding="2">
		<tr>
		<td>名前<?php if(!empty($_SESSION['number'])) { echo "(編集中)"."<br>\n"; } ?></td>
		<td><input type = "text" name = "name" size = "20" value = "<?php echo $_SESSION['name']; ?>"></td>
		</tr>
		<tr>
			<td valign = "top">コメント<?php if(!empty($_SESSION['number'])) { echo "(編集中)"."<br>\n"; } ?></td>
			<td><textarea name="comment" cols="23" rows="5"><?php echo $_SESSION['comment']; ?></textarea></td>
		</tr>
			    <?php if(!empty($_SESSION['number'])) { echo '<input type = "hidden" name = "edit" value = "1">'; } ?>
		<tr>
			<td>ファイル</td>
			<td><input type = "file" name = "upfile" size = "30" <?php if(isset($_POST['number2'])) { echo "disabled"; } ?>></td>
			    <input type = "hidden" name="MAX_FILE_SIZE" value = "300000">
		</tr>
		<tr>
			<td><input type = "submit" value = "<?php if(!empty($_SESSION['number'])) { echo '編集'; } else { echo '投稿'; } ?>"></td>
		</tr>
	</table>
</form>
<br><br>
<form action = "MessageBoard.php" method = "post">
	<table border="0" cellspacing="2" cellpadding="2">
		<tr>
			<td>削除対象番号</td>
			<td><input type = "text" name = "number" <?php if(isset($_POST['number2'])) { echo "disabled"; } ?>></td>
		</tr>
		<tr>
			<td><input type = "submit" value = "削除"></td>
		</tr>
	</table>
</form>
<br><br>
<form action = "MessageBoard.php" method = "post">
	<table border="0" cellspacing="2" cellpadding="2">
	<tr>
	<td>編集対象番号</td>
	<td><input type = "text" name = "number2" <?php if(isset($_POST['number2'])) { echo "disabled"; } ?>></td>
	</tr>
	<tr>
	<td><input type = "submit" value = "編集"></td>
	</tr>
	</table>
</form>
</div>

<!-- 後処理 -->
<?php

	echo "<br>掲示板"."<br>\n";
	$sql = "SELECT * FROM $table";
	echo "SQL文 : ".$sql."<br>";

	$result = $dbh->query($sql);
	if (!$result) {
	    die('クエリ―が失敗しました。'.mysql_error());
	}

	echo "<table border='1' cellpadding='3' cellspacing='0'>";
	echo "<tr><td>Num</td><td>Name</td><td>Comment</td><td>Date</td><td>Image</td></tr>";
	foreach ($result as $data) {
	    $num = $data['id'];
	    $name = $data['name'];
	    $comment = $data['comment'];
	    $date = $data['date'];

	    //画像があるかどうか
	    $sql = "SELECT id FROM $image WHERE id = $num";
	    //echo "SQL文 : ".$sql."<br>";
	    $result2 = $dbh->query($sql);
	    if (!$result2) {
	    	die('クエリ―が失敗しました。'.mysql_error());
	    }
	    $display = "";
	    foreach ($result2 as $data2) {
		 $display = $data2['id'];
	    }
	    if(empty($display)) {
		echo "<tr><td>$num</td><td>$name</td><td>$comment</td><td>$date</td><td>No Data</td></tr>";
	    } else {
		$url = "http://co-960.it.99sv-coco.com/mission_3-10/messageBoard/Display.php?id=$display";
		echo "<tr><td>$num</td><td>$name</td><td>$comment</td><td>$date</td><td><a href=\"$url\" target=\"right\">$url</a></td></tr>";
	    }
	    //echo $data['num']." : ".$data['name']." : ".$data['comment']." : ".$data['password']." : ".$data['date']."<br>";
	}
	echo "</table>";

	$dbh = null; //MySQLをクローズ

?>

</body>

</html>