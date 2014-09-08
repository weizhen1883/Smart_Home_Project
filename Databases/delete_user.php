<?php
	session_start();
	if(isset($_GET['id'])){
	    $host="localhost";
		$mysql_username="root";
		$mysql_password="1qaz2wsx";
		$db_name="Smart_Home";
		$table_users="users";
		$table_groups="groups";
		$table_devices="devices";
		$table_settings="HVAC_settings";
		$table_status="HVAC_status";

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

		$sql="SELECT * FROM $table_devices WHERE device_user='$username'";
		$result=mysql_query($sql);
		while ($row=mysql_fetch_array($result,MYSQL_ASSOC)) {
			$device_name=$row['device_name'];
			$device_group=$row['device_group'];
			$check_device_sql="SELECT * FROM $table_devices WHERE device_name='$device_name' AND device_group='$device_group'";
			$check_result=mysql_query($check_device_sql);
			$check_result_count=mysql_num_rows($check_result);
			if($check_result_count==1){
				$select_sql="SELECT * FROM $table_settings";
				$select=mysql_query($select_sql);
				$count=mysql_num_rows($select);
				$select_sql="SELECT * FROM $table_settings WHERE device_name='$device_name' AND device_group='$device_group'";
				$select=mysql_query($select_sql);
				$select=mysql_fetch_array($select,MYSQL_ASSOC);
				$id=$select['id'];
				$delete_sql="DELETE FROM $table_settings WHERE id=$id";
				$delete=mysql_query($delete_sql);
				if($id!=$count){
					$update_sql="UPDATE $table_settings SET id=$id WHERE id=$count";
					$update_result=mysql_query($update_sql);
				}
				$reset_id_sql="ALTER TABLE $table_settings AUTO_INCREMENT = $count";
				$reset_id=mysql_query($reset_id_sql);

				$select_sql="SELECT * FROM $table_status";
				$select=mysql_query($select_sql);
				$count=mysql_num_rows($select);
				$select_sql="SELECT * FROM $table_status WHERE device_name='$device_name' AND device_group='$device_group'";
				$select=mysql_query($select_sql);
				$select=mysql_fetch_array($select,MYSQL_ASSOC);
				$id=$select['id'];
				$delete_sql="DELETE FROM $table_status WHERE id=$id";
				$delete=mysql_query($delete_sql);
				if($id!=$count){
					$update_sql="UPDATE $table_status SET id=$id WHERE id=$count";
					$update_result=mysql_query($update_sql);
				}
				$reset_id_sql="ALTER TABLE $table_status AUTO_INCREMENT = $count";
				$reset_id=mysql_query($reset_id_sql);

				$select=mysql_query($sql);
				$select=mysql_fetch_array($select,MYSQL_ASSOC);
				$id=$select['id'];
				$select_sql="SELECT * FROM $table_devices";
				$select=mysql_query($select_sql);
				$count=mysql_num_rows($select);
				$delete_sql="DELETE FROM $table_devices WHERE id=$id";
				$delete=mysql_query($delete_sql);
				if($id!=$count){
					$update_sql="UPDATE $table_devices SET id=$id WHERE id=$count";
					$update_result=mysql_query($update_sql);
				}
				$reset_id_sql="ALTER TABLE $table_devices AUTO_INCREMENT = $count";
				$reset_id=mysql_query($reset_id_sql);
			}else{
				$select=mysql_query($sql);
				$select=mysql_fetch_array($select,MYSQL_ASSOC);
				$id=$select['id'];
				$select_sql="SELECT * FROM $table_devices";
				$select=mysql_query($select_sql);
				$count=mysql_num_rows($select);
				$delete_sql="DELETE FROM $table_devices WHERE id=$id";
				$delete=mysql_query($delete_sql);
				if($id!=$count){
					$update_sql="UPDATE $table_devices SET id=$id WHERE id=$count";
					$update_result=mysql_query($update_sql);
				}
				$reset_id_sql="ALTER TABLE $table_devices AUTO_INCREMENT = $count";
				$reset_id=mysql_query($reset_id_sql);
			}
		}

		if($_SESSION['username']==$username){
			header("location:index.php");
		}else{
			header("location:users_edit.php");
		}
	}
?>