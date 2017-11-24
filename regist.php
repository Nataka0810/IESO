<!DOCTYPE html>
<html lang="en">
<html>

<head>
	<meta charset="UTF-8"/>
	<title>regist</title>
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

	if($_SERVER['REQUEST_METHOD'] == 'GET') {
		$id = $_GET['id'];

		$sql = "UPDATE $table SET flg = \"false\" WHERE id = \"$id\"";
		echo $sql;
		$result = $dbh->query($sql); //SQL文

		if (!$result) {
		    die('クエリ―が失敗しました。'.mysql_error());
		}

		//登録処理をする

		$dbh = null;

		//unset($_SESSION['join']);

		header('Location: thanks.php');
		exit();
	}

	$dbh = null;

?>

<h1>新規登録画面</h1>
<p>確認画面</p>
<form action = "" method = "post">
	<dl>
		<dt>ユーザーID</dt>
		<dd>
			<?php echo $_GET['id']; ?>
		</dd>
		<dt>ユーザー名</dt>
		<dd>
			<?php echo $_GET['name']; ?>
		</dd>	
		<dt>メールアドレス</dt>
		<dd>
			<?php echo $_GET['mail']; ?>
		</dd>
		<dt>パスワード</dt>
		<dd>
			【表示されません】
		</dd>
	</dl>
	<div>
		<a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a>
		<input type = "submit" value = "登録する">
	</div>
</form>

<!-- 後処理 -->
<?php
?>

</body>

</html>