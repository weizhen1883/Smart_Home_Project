<?php
	session_start();
	if(!isset($_SESSION['username'])){
        header("location:index.php");
    }else if($_SESSION['permission']!=1){
    	header("location:index.php");
    }else{
    	$host="localhost";
		$mysql_username="root";
		$mysql_password="1qaz2wsx";
		$db_name="Smart_Home";
		$table_users="users";
		$table_groups="groups";

		mysql_connect("$host","$mysql_username","$mysql_password")or die("cannot connect");
		mysql_select_db("$db_name")or die("cannot select DB");
    }
?>
<html>
	<head>
		<title>USER SYSTEM</title>
	</head>
	<body>
		<?php echo "hi, ". $_SESSION['username'];?>&nbsp;&nbsp;<button type="button" onclick="location.href='logout.php'">Logout</button><br>
		<table width="100%" height="650px">
			<tr>
				<td></td>
				<td width="700px" align="center" valign="top">
					<table width="700px" height="650px">
						<tr><td height="25px" align="center">ADD USER</td></tr>
						<tr><td height="25px" align="center">
							<form name="adduser" action="users_edit.php?added" method="post">
								username:&nbsp;<input type="text" name="username" id="username">&nbsp;&nbsp;&nbsp;
								password:&nbsp;<input type="password" name="psw" id="psw">&nbsp;&nbsp;&nbsp;
								groups:&nbsp;<input type="radio" name="group" id="group" value="administrator">administrator&nbsp;
								<input type="radio" name="group" id="group" value="user">user&nbsp;&nbsp;&nbsp;
								<input type="submit" name="add" value="add">
							</form>
						</td></tr>
						<tr><td height="25px" align="center">
							<?php
								if(isset($_GET['added'])){
									$username=$_POST['username'];
									$username=stripslashes($username);
									$username=mysql_real_escape_string($username);

									$sql="SELECT * FROM $table_users WHERE username='$username'";
									$result=mysql_query($sql);
									$count=mysql_num_rows($result);

									if($count!=0){echo '<span style="color:red;">username exist, please try again.</span>';}else{
										$password=$_POST['psw'];
										$group=$_POST['group'];
										$password=stripslashes($password);
										$group=stripslashes($group);
										$password=mysql_real_escape_string($password);
										$group=mysql_real_escape_string($group);

										$sql="SELECT * FROM $table_groups WHERE group_name='$group'";
										$result=mysql_query($sql);
										$row=mysql_fetch_row($result);
										$group_id=$row[0];

										$sql="INSERT INTO $table_users (username, group_id, passhash) VALUE ('$username', $group_id, '$password')";
										$result=mysql_query($sql);
										header("location:users_edit.php");
									}
								}
							?>
						</td></tr>
						<tr><td height="25" align="center">USERS</td></tr>
						<tr><td height="500px" align="center" valign="top">
							<table border="1" width="750px" align="center">
								<tr height="25px"><th width="50px">ID</th><th width="200px">Username</th><th width="200px">Password</th><th width="200px">Group</th><th width="50px"></th><th width="50px"></th></tr>
								<?php
									$sql="SELECT * FROM $table_users";
									$result=mysql_query($sql);
									$count=mysql_num_rows($result);

									while($row=mysql_fetch_array($result,MYSQL_ASSOC)) {
										$group_id=$row['group_id'];
										$sql_group="SELECT * FROM $table_groups WHERE id=$group_id";
										$result_group=mysql_query($sql_group);
										$group_row=mysql_fetch_row($result_group);
										$group_name=$group_row[1];
										$i=$row['id'];
										$address="delete_user.php?id=$i";

										printf("<tr height='25px' align='center'><td width='50px'>%d</td><td width='200px'>%s</td><td width='200px'>%s</td><td width='200px'>%s</td>",$row['id'],$row['username'],$row['passhash'],$group_name);
										echo "<td width='50px'><button type=\"button\" onclick=\"location.href='edit_user.php?id=". $i ."'\">Edit</button></td>";
										if($i==1){
											echo "<td width='50px'></td></tr>";
										}else{
											echo "<td width='50px'><button type=\"button\" onclick=\"location.href='delete_user.php?id=". $i ."'\">Delete</button></td></tr>";
										}
									}
								?>
							</table>
						</td></tr>
						<tr><td align="center"><button type="button" onclick="location.href='login_success.php'">Go Back</button></td></tr>
						<tr></tr>
					</table>
				</td>
				<td></td>
			</tr>
		</table>
	</body>
</html>