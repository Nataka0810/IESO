<!DOCTYPE html>
<html lang="en">
<html>

<head>
	<meta charset="UTF-8"/>
	<title>thanks</title>
</head>

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

	$name = $_SESSION['join']['name'];
	$password = $_SESSION['join']['password'];

	unset($_SESSION['join']);

	$sql = "SELECT id, name, password, email, flg FROM $table WHERE name = \"$name\" AND password = \"$password\"";
	//echo $sql;
	
	foreach ($dbh->query($sql) as $row) {
		$id = $row['id'];
		$name = $row['name'];
		$password = $row['password'];
		$email = $row['email'];
		$flg = $row['flg'];
	}
	unset($row);
	
	$dbh = null;
?>

<body>
<h1>新規登録画面</h1>
<p>ユーザー本登録が完了しました</p>
<dl>
	<dt>ユーザーID</dt>
	<dd>
		<?php echo $id; ?>
	</dd>
	<dt>ユーザー名</dt>
	<dd>
		<?php echo $name; ?>
	</dd>	
	<dt>パスワード</dt>
	<dd>
		<?php echo "【表示されません】"; ?>
	</dd>
	<dt>メールアドレス</dt>
	<dd>
		<?php echo $email; ?>
	</dd>
</dl>

<p><a href="../login/login.php">ログインする</a></p>

</body>

</html>