<?php
	session_start();
	if(!isset($_SESSION['username'])){
		header("location:index.php");
    }else if($_SESSION['permission']!=1){
    	header("location:index.php");
    }else{
		if(isset($_GET['id'])){
		    $host="localhost";
			$mysql_username="root";
			$mysql_password="1qaz2wsx";
			$db_name="Smart_Home";
			$table_users="users";
			$table_groups="groups";

			mysql_connect("$host","$mysql_username","$mysql_password")or die("cannot connect");
			mysql_select_db("$db_name")or die("cannot select DB");

			$id=$_GET['id'];
			$sql="SELECT * FROM $table_users WHERE id=$id";
			$result=mysql_query($sql);
			$row=mysql_fetch_row($result);
			$username=$row[1];
			$group_id=$row[2];
			$password=$row[3];

			$sql="SELECT * FROM $table_groups WHERE id=$group_id";
			$result=mysql_query($sql);
			$row=mysql_fetch_row($result);
			$group=$row[1];

			$edit_username=$_POST['username'];
			$edit_username=stripslashes($edit_username);
			$edit_username=mysql_real_escape_string($edit_username);
			$sql="SELECT * FROM $table_users WHERE username='$edit_username'";
			$result=mysql_query($sql);
			$count=mysql_num_rows($result);
			if($count==1&&$username!=$edit_username){
				header("location:edit_user.php?id=$id&editfail");
			}else if(isset($_GET['edit'])){
				if(isset($_POST['edit_username'])||isset($_POST['editALL'])){
					$username=$_POST['username'];
					$username=stripslashes($username);
					$username=mysql_real_escape_string($username);
					if($_SESSION['username']==$username){
						$_SESSION['username']=$username;
					}
					$sql="UPDATE $table_users SET username='$username' WHERE id=$id";
					$result=mysql_query($sql);
					header("location:edit_user.php?id=$id");
				}

				if(isset($_POST['edit_password'])||isset($_POST['editALL'])){
					$password=$_POST['psw'];
					$password=stripslashes($password);
					$password=mysql_real_escape_string($password);
					if($_SESSION['username']==$username){
						$_SESSION['password']=$password;
					}
					$sql="UPDATE $table_users SET passhash='$password' WHERE id=$id";
					$result=mysql_query($sql);
					header("location:edit_user.php?id=$id");
				}

				if($id!=1){
					if(isset($_POST['edit_group'])||isset($_POST['editALL'])){
						$group_id=$_POST['group'];
						if ($_SESSION['username']==$username) {
							$sql="SELECT * FROM $table_groups WHERE id=$group_id";
							$result=mysql_query($sql);
							$row=mysql_fetch_row($result);
							$_SESSION['permission']=$row[2];
						}
						$sql="UPDATE $table_users SET group_id=$group_id WHERE id=$id";
						$result=mysql_query($sql);
						header("location:edit_user.php?id=$id");
					}
				}

				if(isset($_POST['editALL'])) {
					header("location:users_edit.php");
				}
			}
		}
	}
?>
<html>
	<head></head>
	<body>
		<form name="edituser" action="edit_user.php?id=<?php echo $id;?>&edit" method="post">
			<table width="400px" height="125px">
				<tr><td>username:&nbsp;</td><td><?php echo $username;?></td><td><input type="text" name="username" id="username" value="<?php echo $username;?>"></td><td><input type="submit" name="edit_username" value="edit"></td></tr>
				<tr><td>password:&nbsp;</td><td><?php echo $password;?></td><td><input type="password" name="psw" id="psw" value="<?php echo $password;?>"></td><td><input type="submit" name="edit_password" value="edit"></td></tr>
				<?php
					if($id!=1){
						echo "<tr><td>&nbsp;&nbsp;&nbsp;group:&nbsp;</td><td>";
						echo $group;
						echo "</td><td><select name=\"group\" id=\"group\">";
						
						$sql="SELECT * FROM $table_groups WHERE id=$group_id";
						$result=mysql_query($sql);
						$row=mysql_fetch_row($result);
						echo "<option value='". $row[0] ."'>". $row[1] ."</option>";

						$sql="SELECT * FROM $table_groups";
						$result=mysql_query($sql);
						while($row=mysql_fetch_array($result,MYSQL_ASSOC)){
							if($row['id']!=$group_id){
								echo "<option value='". $row['id'] ."'>". $row['group_name'] ."</option>";
							}
						}	
						echo "</select></td><td><input type=\"submit\" name=\"edit_group\" value=\"edit\"></td></tr></tr>";
					}
				?>
				<tr><td></td><td></td><td align="center"><input type="submit" name="editALL" value="Edit ALL"></td><td></td></tr>
			</table>
		</form>
		<?php if(isset($_GET['editfail'])){echo '<span style="color:red;">username exist, please try again.</span>';} ?>
	</body>
</html>