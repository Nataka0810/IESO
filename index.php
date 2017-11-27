<!DOCTYPE html>
<html lang="en">
<html>

<head>
	<meta charset="UTF-8"/>
	<title>index</title>
</head>

<body>
<!-- 前処理 -->
<?php
	session_start();

	if(!empty($_POST)) {
		//エラー項目の確認
		if($_POST['name'] == '') {
			$error['name'] = 'blank';
		}
		if($_POST['mail'] == '') {
			$error['mail'] = 'blank';
		}
		if(strlen($_POST['password']) < 4) {
			$error['password'] = 'length';
		}
		
		if(empty($error)) {
			$_SESSION['join'] = $_POST;
			header('Location: check.php');
			exit();
		}
	} 
	else {
		//書き直し
		if ($_REQUEST['action'] == 'rewrite') {
			$_POST = $_SESSION['join'];
		}
	}
?>

<h1>新規登録画面</h1>
<p>必要事項を記入してください</p>
<form action = "" method = "post" enctype="multipart/form-data">
	<dl>
		<dt>ユーザー名<?php echo $_POST['name']; ?></dt>
		<dd>
			<input type = "text" name = "name" size = "35" maxlength = "255" 
				value = "<?php echo $_POST['name']; ?>">
			<?php if(!empty($error['name']) and $error['name'] == 'blank'): ?>
			<p><font color="red">* ユーザ名を入力してください</font></p>
			<?php endif; ?>
		</dd>
		<dt>メールアドレス<?php echo $_POST['mail']; ?></dt>
		<dd>
			<input type = "text" name = "mail" size = "35" maxlength = "255" 
				value = "<?php echo $_POST['mail']; ?>">
			<?php if(!empty($error['mail']) and $error['mail'] == 'blank'): ?>
			<p><font color="red">* メールアドレスを入力してください</font></p>
			<?php endif; ?>
		</dd>
		<dt>パスワード</dt>
		<dd>
			<input type = "password" name = "password" size = "35" maxlength="255" 
				value = "<?php echo $_POST['password']; ?>">
			<?php if(!empty($error['password']) and $error['password'] == 'blank'): ?>
			<p><font color="red">* パスワードを入力してください</font></p>
			<?php endif; ?>
			<?php if(!empty($error['password']) and $error['password'] == 'length'): ?>
			<p><font color="red">* パスワードは4文字以上で入力してください</font></p>
			<?php endif; ?>
		</dd>
	</dl>

	<div>
		<input type = "submit" value = "入力内容を確認">
	</div>
</form>

<!-- 後処理 -->
<?php
?>
</body>

</html>
