<?php
	session_start();

	if(!isset($_SESSION['username'])){
        header("location:index.php");
    }else{
		if(isset($_POST['change'])){
			$host="localhost";
			$mysql_username="root";
			$mysql_password="1qaz2wsx";
			$db_name="Smart_Home";
			$table_users="users";
			$table_groups="groups";

			mysql_connect("$host","$mysql_username","$mysql_password")or die("cannot connect");
			mysql_select_db("$db_name")or die("cannot select DB");

			$username=$_SESSION['username'];
			$password=$_POST['oldpsw'];

			$username=stripslashes($username);
			$password=stripslashes($password);
			$username=mysql_real_escape_string($username);
			$password=mysql_real_escape_string($password);
			$sql="SELECT * FROM $table_users WHERE username='$username' and passhash='$password'";
			$result=mysql_query($sql);

			$count=mysql_num_rows($result);

			if($count==1){
				if($_POST['newpsw']==$_POST['confirmpsw']){
					$password=$_POST['newpsw'];
					$password=stripslashes($password);
					$password=mysql_real_escape_string($password);
					
					$sql="UPDATE $table_users SET passhash='$password' WHERE username='$username'";
					$result=mysql_query($sql);
			
					$_SESSION['password']=$password;
					header("location:login_success.php");
				}
			}
		}
		if(isset($_POST['cancel'])){
			header("location:login_success.php");
		}	
	}
?>
<html>
	<head>
		<title>Change Password</title>
	</head>
	<body>
		<form name="passwordChange" action="password_change.php?changed" method="post">
			Changing your password<br>
			<?php echo "username:     ". $_SESSION['username']; ?><br>
			<?php if(isset($_GET['changed'])){if($_POST['oldpsw']!=$_SESSION['password']){echo '<span style="color:red;">Password is wrong, please check it again.</span>';}} 
				  if($_POST['newpsw']!=$_POST['confirmpsw']){echo '<span style="color:red;">Please check your new password again.</span>';}
			?><br>
			<table>
				<tr><td>old password:</td><td><input type="password" name="oldpsw" id="oldpsw"></td></tr>
				<tr><td>new password:</td><td><input type="password" name="newpsw" id="newpsw"></td></tr>
				<tr><td>confirm password:</td><td><input type="password" name="confirmpsw" id="confirmpsw"></td></tr>
				<tr><td><input type="submit" name="change" value="Change"></td><td><input type="submit" name="cancel" value="Cancel"></td></tr>
			</table>
		</form>
	</body>
</html>