<!DOCTYPE html>
<html lang="en">
<html>

<head>
	<meta charset="UTF-8"/>
	<title>check</title>
</head>

<body>
<!-- 前処理 -->
<?php
	session_start();

	if(!isset($_SESSION['join'])) {
		header('Location: index.php');
		exit();
	}

	$dsn = 'データベース名'; //データベース名とホスト名
	$user = 'ユーザー名'; //ユーザー名
	$password = 'パスワード'; //パスワード

	try {
		$dbh = new PDO($dsn, $user, $password); //データベースに接続

		//print('接続成功<br>');

		$table = 'users'; //テーブル名
	}
	catch (PDOException $e) {
		print('Error:'.$e->getMessage());
		die();
	}

	$sql = "CREATE TABLE IF NOT EXISTS $table(id varchar(11) not null primary key, password varchar(255), name varchar(255), email varchar(255), flg varchar(10), date timestamp not null default current_timestamp)"; //テーブル作成

	$dbh->query($sql); //SQL文

	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		//登録処理をする
		$id=substr(md5(uniqid(rand(),1)), 9, 9);
		$name=$_SESSION['join']['name'];
		$mail=$_SESSION['join']['mail'];
		$password=$_SESSION['join']['password'];
		$flg="true";
		$sql = "INSERT INTO $table(id, name, password, email, flg) values(\"$id\", \"$name\", \"$password\", \"$mail\", \"$flg\")";
		//echo $sql."<br>";
		$dbh->query($sql); //SQL文

		$dbh = null;

		//unset($_SESSION['join']);

		mb_language("Japanese");
		mb_internal_encoding("UTF-8");
		$to = $mail;
		$subject = 'e-mail confirm';
		$message = "http://(サーバーのURL)/BulletinBoard/newRegister/regist.php?id=$id&name=$name&mail=$mail";
		//echo $message;
		$headers = 'From: http://(サーバーのURL)/BulletinBoard/newRegister';

		if(mb_send_mail($to, $subject, $message, $header)) {
			echo "メールを送信しました。";
		}
		else {
			echo "メールの送信に失敗しました。";
		}

		//header('Location: thanks.php');
		//exit();
	}

	$dbh = null;
?>

<h1>新規登録画面 mission_3-10</h1>
<p>ユーザー仮登録画面</p>
<form action = "" method = "post">
	<dl>
		<dt>ユーザー名</dt>
		<dd>
			<?php echo $_SESSION['join']['name']; ?>
		</dd>	
		<dt>メールアドレス</dt>
		<dd>
			<?php echo $_SESSION['join']['mail']; ?>
		</dd>
		<dt>パスワード</dt>
		<dd>
			【表示されません】
		</dd>
	</dl>
	<div>
		<a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a>
		<input type = "submit" value = "メール送信">
	</div>
</form>

<!-- 後処理 -->
<?php
?>

</body>

</html>