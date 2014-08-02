<?php
	ob_start();
	$host="localhost";
	$mysql_username="root";
	$mysql_password="1qaz2wsx";
	$db_name="Smart_Home";
	$table_users="users";
	$table_groups="groups";

	mysql_connect("$host","$mysql_username","$mysql_password")or die("cannot connect");
	mysql_select_db("$db_name")or die("cannot select DB");

	$username=$_POST['username'];
	$password=$_POST['psw'];

	$username=stripslashes($username);
	$password=stripslashes($password);
	$username=mysql_real_escape_string($username);
	$password=mysql_real_escape_string($password);
	$sql="SELECT * FROM $table_users WHERE username='$username' and passhash='$password'";
	$result=mysql_query($sql);

	$count=mysql_num_rows($result);

	if($count==1){
		$row_user=mysql_fetch_row($result);
		$group_id=$row_user[2];
		$sql="SELECT * FROM $table_groups WHERE id='$group_id'";
		$result=mysql_query($sql);
		$row_group=mysql_fetch_row($result);
		session_start();
		$_SESSION['username']=$username;
		$_SESSION['password']=$password;
		$_SESSION['permission']=$row_group[2];
		header("location:login_success.php");
	} else {
		header("location:index.php?Login_fail");
	}
	ob_end_flush();
?>