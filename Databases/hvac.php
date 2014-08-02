<?php
	session_start();
	if(!isset($_SESSION['username'])){
		header("location:index.php");
    }else{
    	$host="localhost";
		$mysql_username="root";
		$mysql_password="1qaz2wsx";
		$db_name="Smart_Home";
		$table_devices="devices";
		$table_settings="HVAC_settings";
		$table_status="HVAC_status";

		mysql_connect("$host","$mysql_username","$mysql_password")or die("cannot connect");
		mysql_select_db("$db_name")or die("cannot select DB");

		$sql="SELECT * FROM $table_status WHERE device_name='HVAC' and device_group='HVAC_CTRL'";
		$result=mysql_query($sql);
		$row=mysql_fetch_array($result,MYSQL_ASSOC);
		$AC_status=$row['AC_status'];
		$mode_status=$row['mode_status'];
		$fan_status=$row['fan_status'];

		$username=$_SESSION['username'];
		$device_group="HVAC_ROOM_CTRL";
		$username=stripslashes($username);
		$device_group=stripslashes($device_group);
		$username=mysql_real_escape_string($username);
		$device_group=mysql_real_escape_string($device_group);

		$can_use=0;
		$sql="SELECT * FROM $table_devices WHERE device_name='HVAC' and device_group='HVAC_CTRL'";
		$result=mysql_query($sql);
		while($row=mysql_fetch_array($result,MYSQL_ASSOC)){
			if($username==$row['device_user']){
				$can_use=1;
				$HVAC_id=$row['id'];
				break;
			}
		}

		if(isset($_GET['control_set'])){
			$AC_status=$_POST['on_off'];
			$AC_status=stripslashes($AC_status);
			$AC_status=mysql_real_escape_string($AC_status);
			$mode_status=$_POST['mode'];
			$mode_status=stripslashes($mode_status);
			$mode_status=mysql_real_escape_string($mode_status);
			$fan_status=$_POST['fan'];
			$fan_status=stripslashes($fan_status);
			$fan_status=mysql_real_escape_string($fan_status);

			$sql="UPDATE $table_settings SET AC_status_setting='$AC_status', mode_setting='$mode_status', fan_setting='$fan_status' WHERE device_name='HVAC' and device_group='HVAC_CTRL'";
			$result=mysql_query($sql);
			$sql="UPDATE $table_status SET AC_status='$AC_status', mode_status='$mode_status', fan_status='$fan_status' WHERE device_name='HVAC' and device_group='HVAC_CTRL'";
			$result=mysql_query($sql);
		}
    }
?>
<html>
	<head>
		<title>HVAC CONTROL</title>
	</head>
	<body>
		<?php echo "hi, ". $_SESSION['username'];?>&nbsp;&nbsp;<button type="button" onclick="location.href='logout.php'">Logout</button><br>
		<table width="100%" height="650px">
			<tr>
				<td></td>
				<td width="900px" align="center" valign="top">
					<table width="900px" height="650px">
						<tr height="100px"><td width="900px" align="center" valign="middle">
							<table width="900px" height="100px">
								<td width="300px" height="100px" align="center" valign="middle">
									<form name="ac_control" action="hvac.php?control_set" method="post">
										<table width="300px" height="100px">
											<tr height="25px">
												<?php
													if($can_use==1){
														echo "<td width=\"150px\" align=\"left\" valign=\"middle\">";
														echo "<input type=\"radio\" name=\"on_off\" value=\"ON\" ";
														if($AC_status=="ON"){print "checked";}else{print "unchecked";}
														echo ">&nbsp;&nbsp;ON";
														echo "</td>";
														echo "<td width=\"150px\" align=\"left\" valign=\"middle\">";
														echo "<input type=\"radio\" name=\"on_off\" value=\"OFF\" ";
														if($AC_status=="OFF"){print "checked";}else{print "unchecked";}
														echo ">&nbsp;&nbsp;OFF";
														echo "</td>";
													}else{
														echo "<td width=\"300px\" align=\"center\" valign=\"middle\">";
														if($AC_status=="ON"){echo "AC ON";}else{echo "AC OFF";}
														echo "</td>";
													}
												?>
											</tr>
											<tr height="25px">
												<?php
													if($can_use==1){
														echo "<td width=\"150px\" align=\"left\" valign=\"middle\">";
														echo "<input type=\"radio\" name=\"mode\" value=\"COOL\" ";
														if($mode_status=="COOL"){print "checked";}else{print "unchecked";}
														echo ">&nbsp;&nbsp;COOL";
														echo "</td>";
														echo "<td width=\"150px\" align=\"left\" valign=\"middle\">";
														echo "<input type=\"radio\" name=\"mode\" value=\"HEAT\" ";
														if($mode_status=="HEAT"){print "checked";}else{print "unchecked";}
														echo ">&nbsp;&nbsp;HEAT";
														echo "</td>";
													}else{
														echo "<td width=\"300px\" align=\"center\" valign=\"middle\">";
														if($mode_status=="COOL"){echo "MODE: COOL";}else if($mode_status=="HEAT"){echo "MODE: HEAT";}
														echo "</td>";
													}
												?>
											</tr>
											<tr height="25px">
												<?php
													if($can_use==1){
														echo "<td width=\"150px\" align=\"left\" valign=\"middle\">";
														echo "<input type=\"radio\" name=\"fan\" value=\"ON\" ";
														if($fan_status=="ON"){print "checked";}else{print "unchecked";}
														echo ">&nbsp;&nbsp;FAN ON";
														echo "</td>";
														echo "<td width=\"150px\" align=\"left\" valign=\"middle\">";
														echo "<input type=\"radio\" name=\"fan\" value=\"AUTO\" ";
														if($fan_status=="AUTO"){print "checked";}else{print "unchecked";}
														echo ">&nbsp;&nbsp;FAN AUTO";
														echo "</td>";
													}else{
														echo "<td width=\"300px\" align=\"center\" valign=\"middle\">";
														if($fan_status=="ON"){echo "FAN: ON";}else if($fan_status=="AUTO"){echo "FAN: AUTO";}
														echo "</td>";
													}
												?>
											</tr>
											<tr height="25px">
												<?php
													if($can_use==1){
														if ($_SESSION['permission']==1) {
															echo "<td width=\"150px\" align=\"center\"><input type=\"submit\" name=\"ac_set\" value=\"  SET  \"></td>";
															echo "<td width=\"150px\" align=\"left\"><input type=\"button\" name=\"ac_edit\" value=\"  EDIT  \" onclick=\"location.href='device_edit.php?id=$HVAC_id&device_group=HVAC_CTRL'\"></td>";
														}else{
															echo "<td width=\"150px\" align=\"right\"><input type=\"submit\" name=\"ac_set\" value=\"     SET     \"></td><td width=\"150px\"></td>";
														}
													}else{
														echo "<td></td>";
													}
												?>
											</tr>
										</table>
									</form>
								</td>
								<td>
									<table border="1" width="300px" height="100px" aligh="center" valign="middle">
										<tr><td></td></tr>
									</table>
								</td>
								<td width="300px" align="center" valign="middle">
									<?php
										if($_SESSION['permission']==1){
											echo "ADD ROOM CONTROL DEVICE <br>";
											echo "<form name=\"addDevice\" action=\"hvac.php?add_device\" method=\"post\">";
											echo "<table width=\"200px\" height=\"100px\">";
											echo "<tr height=\"25px\"><td align=\"center\">name:</td><td><input type=\"text\" name=\"devicename\" id=\"devicename\"></td></tr>";
											echo "<tr height=\"25px\"><td align=\"center\">address:</td><td><input type=\"text\" name=\"deviceaddress\" id=\"deviceaddress\"></td></tr>";
											echo "<tr height=\"25px\"><td></td><td align=\"left\"><input type=\"submit\" name=\"add\" value=\"ADD\"></td></tr>";
											echo "</table>";
											echo "</form>";

											if(isset($_GET['add_device'])){
												$devicename=$_POST['devicename'];
												$deviceaddress=$_POST['deviceaddress'];
												$devicename=stripslashes($devicename);
												$deviceaddress=stripslashes($deviceaddress);
												$devicename=mysql_real_escape_string($devicename);
												$deviceaddress=mysql_real_escape_string($deviceaddress);

												$sql="SELECT * FROM $table_devices WHERE device_name='$devicename' or device_address='$deviceaddress'";
												$result=mysql_query($sql);
												$count=mysql_num_rows($result);

												if($count==0){
													$sql="INSERT INTO $table_devices (device_name, device_group, device_address, device_user) value ('$devicename', '$device_group','$deviceaddress', '$username')";
													$result=mysql_query($sql);
													$sql="INSERT INTO $table_status (device_name, device_group, temperature_status, humidity_status, vent_status) value ('$devicename', '$device_group', 26, 45, 0)";
													$result=mysql_query($sql);
													$sql="INSERT INTO $table_settings (device_name, device_group, temperature_setting, humidity_setting, vent_setting) value ('$devicename', '$device_group', 26, 45, 0)";
													$result=mysql_query($sql);
												}else{
													echo '<span style="color:red;">device exist, please try again.</span>';
												}
											}
										}
									?>
								</td>
							</table>
						</td></tr>
						<tr height="500px"><td width="900px" align="center" valign="middle">
							<?php
								$sql="SELECT * FROM $table_devices WHERE device_user='$username' and device_group='$device_group'";
								$result=mysql_query($sql);
								$count=mysql_num_rows($result);
								$rows=0;
								$i=1;
								echo "<table border=\"1\" width=\"900px\" height=\"500px\">";
								while($row=mysql_fetch_array($result,MYSQL_ASSOC)){
									$device_name=$row['device_name'];
									$device_id=$row['id'];

									if(isset($_GET['set_device_'. $i .''])){
										if(isset($_POST['set_'. $i .''])){
											$temperature=$_POST['temperature_'. $i .''];
											$humidity=$_POST['humidity_'. $i .''];
											$vent=$_POST['vent_'. $i .''];

											$sql="UPDATE $table_settings SET temperature_setting=$temperature, humidity_setting=$humidity, vent_setting=$vent WHERE device_name='$device_name' and device_group='$device_group'";
											$change_sql_result=mysql_query($sql);
											$sql="UPDATE $table_status SET temperature_status=$temperature, humidity_status=$humidity, vent_status=$vent WHERE device_name='$device_name' and device_group='$device_group'";
											$change_sql_result=mysql_query($sql);
										}
									}
									
									$sql="SELECT * FROM $table_status WHERE device_name='$device_name' and device_group='$device_group'";
									$device_status=mysql_query($sql);
									$status=mysql_fetch_array($device_status,MYSQL_ASSOC);
									$temperature_status=$status['temperature_status'];
									$humidity_status=$status['humidity_status'];
									$vent_status=$status['vent_status'];

									$sql="SELECT * FROM $table_settings WHERE device_name='$device_name' and device_group='$device_group'";
									$device_settings=mysql_query($sql);
									$settings=mysql_fetch_array($device_settings,MYSQL_ASSOC);
									$temperature_setting=$settings['temperature_setting'];
									$humidity_setting=$settings['humidity_setting'];
									$vent_setting=$settings['vent_setting'];
									

									if($i%4==1){
										echo "<tr height=\"125px\">";
									}
									echo "<td width=\"225px\" align=\"center\" valign=\"middle\">";
									echo "<form name=\"ctrl". $i ."\" action=\"hvac.php?set_device_". $i ."\" method=\"post\">";
									echo "<table width=\"225px\" height=\"125px\">";
									echo "<tr height=\"25px\"><td width=\"225px\" align=\"center\" valign=\"middle\">". $row['device_name'] ."</td></tr>";
									echo "<tr height=\"75px\"><td width=\"225px\" align=\"center\" valign=\"middle\">";
									echo "<table width=\"225px\" height=\"50px\">";
									echo "<tr height=\"25px\"><td width=\"225px\" align=\"center\" valign=\"middle\"><table width=\"225px\" height=\"25px\"><tr height=\"25px\" align=\"center\" valign=\"middle\"><td width=\"25px\">T:</td><td width=\"100px\">$temperature_status&nbsp;&#176;C</td><td width=\"100px\"><input type=\"number\" name=\"temperature_". $i ."\" id=\"temperature_". $i ."\" min=\"16\" max=\"36\" value=\"$temperature_setting\">&nbsp;&#176;C</td></tr></table></td></tr>";
									echo "<tr height=\"25px\"><td width=\"225px\" align=\"center\" valign=\"middle\"><table width=\"225px\" height=\"25px\"><tr height=\"25px\" align=\"center\" valign=\"middle\"><td width=\"25px\">H:</td><td width=\"100px\">$humidity_status&nbsp;%&nbsp;</td><td width=\"100px\"><input type=\"number\" name=\"humidity_". $i ."\" id=\"humidity_". $i ."\" min=\"35\" max=\"65\" value=\"$humidity_setting\">&nbsp;%&nbsp;</td></tr></table></td></tr>";
									echo "<tr height=\"25px\"><td width=\"225px\" align=\"center\" valign=\"middle\"><input type=\"range\" name=\"vent_". $i ."\" id=\"vent_". $i ."\" min=\"0\" max=\"10\" value=\"$vent_setting\">";
									if($vent_status==0){
										echo "&nbsp;&nbsp;OFF";
									}else{
										echo "&nbsp;&nbsp;$vent_status";
									}
									echo "</td></tr>";
									echo "</table>";
									echo "</td></tr>";
									echo "<tr height=\"25px\"><td width=\"225px\" align=\"center\" valign=\"middle\">";
									echo "<table width=\"225px\" height=\"25px\">";
									echo "<tr height=\"25px\"><td align=\"center\" valign=\"middle\"><input type=\"submit\" name=\"set_". $i ."\" value=\"SET\"></td><td align=\"center\" valign=\"middle\">";
									if($_SESSION['permission']==1){
										echo "<input type=\"button\" name=\"edit\" value=\"Edit\" onclick=\"location.href='device_edit.php?id=$device_id&device_group=$device_group'\">";
									}
									echo "</td></tr>";
									echo "</table>";
									echo "</td></tr>";
									echo "</table>";
									echo "</form>";
									echo "</td>";
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
								for($i=$rows;$i<4;$i++){
									echo "<tr height=\"125px\"><td width=\"225px\" align=\"center\" valign=\"middle\"></td><td width=\"225px\" align=\"center\" valign=\"middle\"></td><td width=\"225px\" align=\"center\" valign=\"middle\"></td><td width=\"225px\" align=\"center\" valign=\"middle\"></td></tr>";
								}
								echo "</table>";
							?>
						</td></tr>
						<tr><td align="center"><button type="button" onclick="location.href='login_success.php?devices'">Go Back</button></td></tr>
						<tr></tr>
					</table>
				</td>
				<td></td>
			</tr>
		</table>
	</body>
</html>