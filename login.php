<!DOCTYPE html>
<html lang="en">
<html>

<head>
	<meta charset="UTF-8"/>
	<title>ログイン</title>
</head>

<body>
<!-- 前処理 -->
<?php
	session_start();

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

	$sql = "DELETE FROM $table WHERE flg = \"true\" AND date <= (SELECT NOW() - INTERVAL 1 DAY)";
	echo $sql."<br>";
	$result = $dbh->query($sql);

	if(!empty($_POST)) {
		//ログインの処理
		if($_POST['id'] != '' and $_POST['password'] != '') {
			$id = $_POST['id'];
			$password = $_POST['password'];
			$sql = "SELECT * FROM $table WHERE id = \"$id\" AND password = \"$password\"";
			echo $sql."<br>";
			$result = $dbh->query($sql);
			$dbh = null;

			$login = false;
			
			while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
				$login = true;
			}
			unset($result);
			unset($row);
			
			if($login == true) {
				if(empty($error)) {
					$_SESSION['enter'] = $_POST;
					header('Location: ../messageBoard/MessageBoard.php');
					exit();
				}
			}
			else {
				echo "ログイン失敗";
			}
		}
	}
	$dbh = null;

?>

<div align="center">
<h1>ログイン mission_3-9</h1>
<p>メールアドレスとパスワードを記入してログインしてください。</p>
<p>入会手続きがまだの方はこちらからどうぞ。</p>
<p>&raquo;<a href="../newRegister/index.php">入会手続きをする</a></p>
<form action = "" method = "post">
	<table border="1" cellspacing="0" cellpadding="0">
	<tr>
	<td>ユーザーID</td><td><input type = "text" name = "id" size = "35" maxlength="255" value = ""></td>
	</tr>
	<tr>	
	<td>パスワード</td><td><input type = "password" name = "password" size = "35" maxlength="255" value = ""></td>
	</tr>
	<tr>
	<td colspan="2" align="center"><input type = "submit" value = "ログインする"></td>
	</tr>
	</table>
</form>
</div>

<!-- 後処理 -->
<?php
?>

</body>

</html>