<?php
	session_start();
	if(!isset($_SESSION['username'])){
		header("location:index.php");
    }else if($_SESSION['permission']!=1){
    	header("location:index.php");
    }else{
    	if(isset($_GET['id'])&&isset($_GET['device_group'])){
	    	$host="localhost";
			$mysql_username="root";
			$mysql_password="1qaz2wsx";
			$db_name="Smart_Home";
			$table_users="users";
			$table_devices="devices";
			$table_settings="HVAC_settings";
			$table_status="HVAC_status";

			$group_name=$_GET['device_group'];
			$device_id=$_GET['id'];

			mysql_connect("$host","$mysql_username","$mysql_password")or die("cannot connect");
			mysql_select_db("$db_name")or die("cannot select DB");

			$sql="SELECT * FROM $table_devices WHERE id=$device_id";
			$result=mysql_query($sql);
			$row=mysql_fetch_array($result,MYSQL_ASSOC);
			$device_name=$row['device_name'];
			$device_address=$row['device_address'];

			if(isset($_POST['edit'])){
				if(isset($_POST['devicename'])&&$device_name!=$_POST['devicename']){
					$device_name_temp=$_POST['devicename'];
					$update_sql="UPDATE $table_settings SET device_name='$device_name_temp' WHERE device_name='$device_name' and device_group='$group_name'";
					$result=mysql_query($update_sql);
					$update_sql="UPDATE $table_status SET device_name='$device_name_temp' WHERE device_name='$device_name' and device_group='$group_name'";
					$result=mysql_query($update_sql);
					$update_sql="UPDATE $table_devices SET device_name='$device_name_temp' WHERE device_name='$device_name' and device_group='$group_name'";
					$result=mysql_query($update_sql);
					$device_name=$device_name_temp;
				}

				if (isset($_POST['deviceaddress'])&&$device_address!=$_POST['deviceaddress']) {
					$device_address=$_POST['deviceaddress'];
					$update_sql="UPDATE $table_devices SET device_address='$device_address' WHERE device_name='$device_name' and device_group='$group_name'";
					$result=mysql_query($update_sql);
				}

				if(isset($_POST['active_users'])){
					$users=$_POST['active_users'];
					
					if(empty($users)){
						$sql="SELECT * FROM $table_devices WHERE device_name='$device_name' AND device_group='$group_name'";
						$result=mysql_query($sql);
						while($row=mysql_fetch_array($result,MYSQL_ASSOC)){
							$select_sql="SELECT * FROM $table_settings";
							$select=mysql_query($select_sql);
							$count=mysql_num_rows($select);
							$select_sql="SELECT * FROM $table_settings WHERE device_name='$device_name' AND device_group='$group_name'";
							$select=mysql_query($select_sql);
							$select=mysql_fetch_array($select,MYSQL_ASSOC);
							$id=$select['id'];
							$delete_sql="DELETE FROM $table_settings WHERE device_name='$device_name' AND device_group='$group_name'";
							$delete=mysql_query($delete_sql);
							if($id!=$count){
								$update_sql="UPDATE $table_settings SET id=$id WHERE id=$count";
								$result=mysql_query($update_sql);
							}
							$reset_id_sql="ALTER TABLE $table_settings AUTO_INCREMENT = $count";
							$reset_id=mysql_query($reset_id_sql);

							$select_sql="SELECT * FROM $table_status";
							$select=mysql_query($select_sql);
							$count=mysql_num_rows($select);
							$select_sql="SELECT * FROM $table_status WHERE device_name='$device_name' AND device_group='$group_name'";
							$select=mysql_query($select_sql);
							$select=mysql_fetch_array($select,MYSQL_ASSOC);
							$id=$select['id'];
							$delete_sql="DELETE FROM $table_status WHERE device_name='$device_name' AND device_group='$group_name'";
							$delete=mysql_query($delete_sql);
							if($id!=$count){
								$update_sql="UPDATE $table_status SET id=$id WHERE id=$count";
								$result=mysql_query($update_sql);
							}
							$reset_id_sql="ALTER TABLE $table_status AUTO_INCREMENT = $count";
							$reset_id=mysql_query($reset_id_sql);

							$id=$row['id'];
							$select_sql="SELECT * FROM $table_devices";
							$select=mysql_query($select_sql);
							$count=mysql_num_rows($select);
							$delete_sql="DELETE FROM $table_devices WHERE device_name='$device_name' AND device_group='$group_name'";
							$delete=mysql_query($delete_sql);
							if($id!=$count){
								$update_sql="UPDATE $table_devices SET id=$id WHERE id=$count";
								$result=mysql_query($update_sql);
							}
							$reset_id_sql="ALTER TABLE $table_devices AUTO_INCREMENT = $count";
							$reset_id=mysql_query($reset_id_sql);
						}
					}else{
						$Do_Delete=0;
						$Num_users=count($users);
						$sql="SELECT * FROM $table_devices WHERE device_name='$device_name' and device_group='$group_name'";
						$result=mysql_query($sql);
						while($row=mysql_fetch_array($result,MYSQL_ASSOC)){
							$hasuser=0;
							$user=$row['device_user'];
							$id=$row['id'];
							for($i=0; $i<$Num_users; $i++){ 
								if($user==$users[$i]){
									$hasuser=1;
									break;
								}
							}
							if($hasuser==0){
								$Delete_User[$Do_Delete]=$user;
								$Do_Delete++;
							}
						}
						if ($Do_Delete!=0) {
							for ($i=0; $i<$Do_Delete; $i++) { 
								$user=$Delete_User[$i];
								$select_sql="SELECT * FROM $table_devices";
								$select=mysql_query($select_sql);
								$count=mysql_num_rows($select);
								$delete_sql="DELETE FROM $table_devices WHERE device_name='$device_name' AND device_group='$group_name' AND device_user='$user'";
								$delete=mysql_query($delete_sql);
								if($id!=$count){
									$update_sql="UPDATE $table_devices SET id=$id WHERE id=$count";
									$result=mysql_query($update_sql);
								}
								$reset_id_sql="ALTER TABLE $table_devices AUTO_INCREMENT = $count";
								$reset_id=mysql_query($reset_id_sql);
							}
						}

						for($i=0; $i<$Num_users; $i++){ 
							$hasuser=0;
							$user=$users[$i];
							$sql="SELECT * FROM $table_devices WHERE device_name='$device_name' and device_group='$group_name'";
							$result=mysql_query($sql);
							while($row=mysql_fetch_array($result,MYSQL_ASSOC)){
								if($user==$row['device_user']){
									$hasuser=1;
									break;
								}
							}
							if($hasuser==0){
								$insert_sql="INSERT INTO $table_devices (device_name, device_group, device_address, device_user) value ('$device_name', '$group_name','$device_address', '$user')";
								$insert=mysql_query($insert_sql);
							}
						}
					}
				}
			}

			if(isset($_POST['delete'])){
				$sql="SELECT * FROM $table_devices WHERE device_name='$device_name' AND device_group='$group_name'";
				$result=mysql_query($sql);
				while($row=mysql_fetch_array($result,MYSQL_ASSOC)){
					$select_sql="SELECT * FROM $table_settings";
					$select=mysql_query($select_sql);
					$count=mysql_num_rows($select);
					$select_sql="SELECT * FROM $table_settings WHERE device_name='$device_name' AND device_group='$group_name'";
					$select=mysql_query($select_sql);
					$select=mysql_fetch_array($select,MYSQL_ASSOC);
					$id=$select['id'];
					$delete_sql="DELETE FROM $table_settings WHERE device_name='$device_name' AND device_group='$group_name'";
					$delete=mysql_query($delete_sql);
					if($id!=$count){
						$update_sql="UPDATE $table_settings SET id=$id WHERE id=$count";
						$result=mysql_query($update_sql);
					}
					$reset_id_sql="ALTER TABLE $table_settings AUTO_INCREMENT = $count";
					$reset_id=mysql_query($reset_id_sql);

					$select_sql="SELECT * FROM $table_status";
					$select=mysql_query($select_sql);
					$count=mysql_num_rows($select);
					$select_sql="SELECT * FROM $table_status WHERE device_name='$device_name' AND device_group='$group_name'";
					$select=mysql_query($select_sql);
					$select=mysql_fetch_array($select,MYSQL_ASSOC);
					$id=$select['id'];
					$delete_sql="DELETE FROM $table_status WHERE device_name='$device_name' AND device_group='$group_name'";
					$delete=mysql_query($delete_sql);
					if($id!=$count){
						$update_sql="UPDATE $table_status SET id=$id WHERE id=$count";
						$result=mysql_query($update_sql);
					}
					$reset_id_sql="ALTER TABLE $table_status AUTO_INCREMENT = $count";
					$reset_id=mysql_query($reset_id_sql);

					$id=$row['id'];
					$select_sql="SELECT * FROM $table_devices";
					$select=mysql_query($select_sql);
					$count=mysql_num_rows($select);
					$delete_sql="DELETE FROM $table_devices WHERE device_name='$device_name' AND device_group='$group_name'";
					$delete=mysql_query($delete_sql);
					if($id!=$count){
						$update_sql="UPDATE $table_devices SET id=$id WHERE id=$count";
						$result=mysql_query($update_sql);
					}
					$reset_id_sql="ALTER TABLE $table_devices AUTO_INCREMENT = $count";
					$reset_id=mysql_query($reset_id_sql);
				}

				header("location:hvac.php");
			}
			
		}
	}
?>
<html>
	<head></head>
	<body>
		<?php echo "hi, ". $_SESSION['username'];?>&nbsp;&nbsp;<button type="button" onclick="location.href='logout.php'">Logout</button><br>
		<table width="100%" height="650px">
			<tr height="650px">
				<td></td>
				<td width="900px" align="center" valign="middle">
					<form name="device_edit" action="device_edit.php?id=<?php print $device_id;?>&device_group=<?php print $group_name;?>" method="post">
						<table width="900px" height="650px">
							<tr height="25px"><td align="center" valign="middle">DEVICE:&nbsp;<?php echo "$device_name";?></td></tr>
							<tr height="1px"><td><hr></td></tr>
							<tr height="480px"><td align="center" valign="top">
								<table width="480px" height="500px">
									<tr height="20px" align="center" valign="middle"><td width="160px">Device Name:</td><td width="160px"><?php echo "$device_name";?></td><td width="160px"><input type="text" name="devicename" id="devicename" value="<?php echo $device_name;?>"></td></tr>
									<tr height="20px" align="center" valign="middle"><td>Device address:</td><td><?php echo "$device_address";?></td><td><input type="text" name="deviceaddress" id="deviceaddress" value="<?php echo $device_address;?>"></td></tr>
									<tr height="20px" align="center" valign="middle"><td></td><td>users</td><td></td></tr>
									<tr height="1px" align="center" valign="middle"><td><hr></td><td><hr></td><td><hr></td></tr>
									<?php
										$sql="SELECT * FROM $table_users";
										$result=mysql_query($sql);
										$i=1;
										$rows=0;
										while($row=mysql_fetch_array($result,MYSQL_ASSOC)){
											if($i%3==1){echo "<tr height=\"20px\" align=\"center\" valign=\"middle\">";}
											echo "<td><input type=\"checkbox\" name=\"active_users[]\" value=\"". $row['username'] ."\" ";
											$sql="SELECT device_user FROM $table_devices WHERE device_name='$device_name' and device_group='$group_name'";
											$users_check_result=mysql_query($sql);
											$checked=0;
											while($users=mysql_fetch_array($users_check_result,MYSQL_ASSOC)){
												if($users['device_user']==$row['username']){
													$checked=1;
													break;
												}
											}
											if($checked==1){print "checked";} else{print "unchecked";}
											echo ">". $row['username'] ."</td>";
											if($i/3==1){echo "</tr>";$rows++;}
											$i++;
										}
										if($i%3!=1){
											while($i%3!=1){
												echo "<td></td>";
												$i++;
											}
											echo "</tr>";
											$rows++;
										}
										for($i=$rows;$i<10;$i++){
											echo "<tr height=\"20px\"><td></td><td></td><td></td></tr>";
										}
									?>
									<tr height="1px" align="center" valign="middle"><td><hr></td><td><hr></td><td><hr></td></tr>
									<tr height=\"25px\" align="center" valign="middle"><td><input type="submit" name="edit" value="Edit"></td><td></td><td><input type="submit" name="delete" value="Delete"></td></tr>
									<tr></tr>
								</table>
							</td></tr>
							<tr height="1px"><td><hr></td></tr>
							<tr height="25px"><td align="center" valign="middle"><button type="button" onclick="location.href='hvac.php'">Go Back</button></td></tr>
							<tr></tr>
						</table>
					</form>
				</td>
				<td></td>
			</tr>
		</table>
	</body>
</html>