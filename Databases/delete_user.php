<?php
	session_start();
	if(isset($_GET['id'])){
	    $host="localhost";
		$mysql_username="root";
		$mysql_password="1qaz2wsx";
		$db_name="Smart_Home";
		$table_users="users";
		$table_groups="groups";

		mysql_connect("$host","$mysql_username","$mysql_password")or die("cannot connect");
		mysql_select_db("$db_name")or die("cannot select DB");

		$sql="SELECT * FROM $table_users";
		$result=mysql_query($sql);
		$count=mysql_num_rows($result);

		$id=$_GET['id'];
		$sql="SELECT * FROM $table_users WHERE id=$id";
		$result=mysql_query($sql);
		$row=mysql_fetch_row($result);
		$username=$row[1];

		$sql="DELETE FROM $table_users WHERE id=$id";
		$result=mysql_query($sql);

		if($id!=$count){
			$sql="UPDATE $table_users SET id=$id WHERE id=$count";
			$result=mysql_query($sql);
		}

		$sql="ALTER TABLE $table_users AUTO_INCREMENT = $count";
		$result=mysql_query($sql);

		if($_SESSION['username']==$username){
			header("location:index.php");
		}else{
			header("location:users_edit.php");
		}
	}
?>