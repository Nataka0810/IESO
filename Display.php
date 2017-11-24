<?php
// DB接続
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';

	try {
		$dbh = new PDO($dsn, $user, $password);

		$table = 'messageBoard';

		$image = 'image';
	}
	catch (PDOException $e) {
		print('Error:'.$e->getMessage());
		die();
	}

	$id = $_GET['id'];

	$sql = "SELECT type, raw_data FROM $image WHERE id = $id";
	//echo $sql;
	$result = $dbh->query($sql);
	if (!$result) {
	    die('クエリ―が失敗しました。'.mysql_error());
	}
	foreach ($result as $data) {
		$type = $data['type'];
		$raw_data = $data['raw_data'];
	}

//echo "Content-Type: $type";
header("Content-Type: $type");

echo $raw_data;
print("<img src=\"$raw_data\">");

?>