<?php
	session_start();
	if(!isset($_SESSION['username'])){
		header("location:index.php");
    }else{
    	$host="localhost";
		$mysql_username="root";
		$mysql_password="1qaz2wsx";
		$db_name="Smart_Home";
		if(isset($_GET['sockets'])){
			$table_checking="socket";
			$device_group="sockets";
		}elseif(isset($_GET['switches'])){
			$table_checking="switch";
			$device_group="switches";
		}
		$table_devices="devices";
		$username=$_SESSION['username'];

		mysql_connect("$host","$mysql_username","$mysql_password")or die("cannot connect");
		mysql_select_db("$db_name")or die("cannot select DB");
    }
?>
<html>
	<head>
		<title>SWITCH&SOCKET CONTROL</title>
	</head>
	<body>
		<?php echo "hi, ". $_SESSION['username'];?>&nbsp;&nbsp;<button type="button" onclick="location.href='logout.php'">Logout</button><br>
		<table width="100%" height="650px">
			<tr>
				<td></td>
				<td width="900px" align="center" valign="top">
					<table width="900px" height="650px">
						<?php 
							if ($_SESSION['permission']==1) {
								echo "<tr height=\"25px\"><td align=\"center\">";
								if(isset($_GET['sockets'])){
									echo "Add Socket";
								}elseif(isset($_GET['switches'])){
									echo "Add Switch";
								}
								echo "</td></tr>";
								echo "<tr height=\"25px\"><td>";
								echo "<table border=\"1\" width=\"900px\" height=\"25px\">";
								echo "<form name=\"addDevice\" action=\"socket_and_switch.php?". $device_group ."&add\" method=\"post\">";
								echo "<tr height=\"25px\">";
								echo "<td align=\"center\">name: <input type=\"text\" name=\"devicename\" id=\"devicename\"></td>";
								echo "<td align=\"center\">address: <input type=\"text\" name=\"deviceaddress\" id=\"deviceaddress\"></td>";
								echo "<td align=\"center\"><input type=\"submit\" name=\"add\" value=\"ADD\"></td>";
								echo "</tr>";
								echo "</form>";
								echo "</table>";
								echo "</td></tr>";
							}else{
								echo "<tr height=\"50px\"><td align=\"center\"></td></tr>";
							}
							echo "<tr height=\"25px\"><td align=\"center\">";

							if(isset($_GET['add'])){
								$devicename=$_POST['devicename'];
								$deviceaddress=$_POST['deviceaddress'];
								$devicename=stripslashes($devicename);
								$deviceaddress=stripslashes($deviceaddress);
								$devicename=mysql_real_escape_string($devicename);
								$deviceaddress=mysql_real_escape_string($deviceaddress);

								$checking_sql="SELECT * FROM $table_devices WHERE device_name='$devicename' or device_address='$deviceaddress'";
								$checking_result=mysql_query($checking_sql);
								$count=mysql_num_rows($checking_result);

								if($count==0){
									$insert_sql="INSERT INTO $table_devices (device_name, device_group, device_address, device_user) value ('$devicename', '$device_group','$deviceaddress', '$username')";
									$insert_result=mysql_query($insert_sql);
									$insert_sql="INSERT INTO $table_checking (devicename, port1_status, port2_status) value ('$devicename', 'OFF', 'OFF')";
									$insert_result=mysql_query($insert_sql);
								}else{
									echo '<span style="color:red;">device exist, please try again.</span>';
								}
							}

							echo "</td></tr>";
						?>
						<tr><td>
							<table border="1" width="900px" height="525px">
								<?php
									$sql="SELECT * FROM $table_devices WHERE device_user='$username' and device_group='$device_group'";
									$result=mysql_query($sql);
									$count=mysql_num_rows($result);
									$rows=0;
									$i=1;
									while($row=mysql_fetch_array($result,MYSQL_ASSOC)){
										$name=$row['device_name'];
										$id=$row['id'];
										$address=$row['device_address'];
										$device_check_sql="SELECT * FROM $table_checking WHERE devicename='$name'";
										$device_check_result=mysql_query($device_check_sql);
										$status=mysql_fetch_array($device_check_result,MYSQL_ASSOC);
										$port1_status=$status['port1_status'];
										$port2_status=$status['port2_status'];
										if(isset($_GET['ctrl_'. $i .''])){
											if(isset($_GET['port1'])){
												if($port1_status=="ON"){
													$device_update_sql="UPDATE $table_checking SET port1_status='OFF' WHERE devicename='$name'";
													$port1_status="OFF";
												}else{
													$device_update_sql="UPDATE $table_checking SET port1_status='ON' WHERE devicename='$name'";
													$port1_status="ON";
												}
												$device_update_result=mysql_query($device_update_sql);
											}

											if(isset($_GET['port2'])){
												if($port2_status=="ON"){
													$device_update_sql="UPDATE $table_checking SET port2_status='OFF' WHERE devicename='$name'";
													$port2_status="OFF";
												}else{
													$device_update_sql="UPDATE $table_checking SET port2_status='ON' WHERE devicename='$name'";
													$port2_status="ON";
												}
												$device_update_result=mysql_query($device_update_sql);
											}

										}
										if($i%4==1){
											echo "<tr height=\"75px\">";
										}
										echo "<td width=\"225px\" align=\"center\" valign=\"middle\"><table width=\"225px\" height=\"75px\">";
										echo "<tr height=\"25px\"><td width=\"225px\" align=\"center\" valign=\"middle\">". $name ."</td></tr>";
										echo "<tr height=\"25px\"><td width=\"225px\" align=\"center\" valign=\"middle\"><table width=\"225px\" height=\"25px\">";
										echo "<tr height=\"25px\"><td align=\"center\" valign=\"middle\">";
										echo "<button type=\"button\" onclick=\"location.href='socket_and_switch.php?". $device_group ."&ctrl_". $i ."&port1'\">port1 ". $port1_status ."</button></td>";
										echo "<td align=\"center\" valign=\"middle\">|</td>";
										echo "<td align=\"center\" valign=\"middle\">";
										echo "<button type=\"button\" onclick=\"location.href='socket_and_switch.php?". $device_group ."&ctrl_". $i ."&port2'\">port2 ". $port2_status ."</button></td>";
										echo "</td></tr>";
										echo "</table></td></tr>";
										echo "<tr height=\"25px\"><td width=\"225px\" align=\"center\" valign=\"middle\">";
										if($_SESSION['permission']==1){echo "<button type=\"button\" onclick=\"location.href='device_edit2.php?id=$id&device_group=$device_group'\">edit</button>";}
										echo "</td></tr>";
										echo "</table></td>";
										if($i/4==1){
											echo "</tr>";
											$rows++;
										}
										$i++;
									}
									if($i%4!=1){
										while($i%4!=1){
											echo "<td width=\"225px\" align=\"center\" valign=\"middle\"></td>";
											$i++;
										}
										echo "</tr>";
										$rows++;
									}
									for($i=$rows;$i<7;$i++){
										echo "<tr height=\"75px\"><td width=\"225px\" align=\"center\" valign=\"middle\"></td><td width=\"225px\" align=\"center\" valign=\"middle\"></td><td width=\"225px\" align=\"center\" valign=\"middle\"></td><td width=\"225px\" align=\"center\" valign=\"middle\"></td></tr>";
									}
								?>
							</table>
						</td></tr>
						<tr height="25px"><td></td></tr>
						<tr height="25px"><td align="center"><button type="button" onclick="location.href='login_success.php?socket_and_switch'">Go Back</button></td></tr>
					</table>
				</td>
				<td></td>
			</tr>
		</table>
	</body>
</html>