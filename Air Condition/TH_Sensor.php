<html>
<head>
<title>BeagleBone LED Changer</title>
<style type="text/css">
	p { display: table-cell; }
	button { width: 75px; margin: 2px auto; }
</style>
<?php
	include("menu.php");
	function checkStatus($addr) {
		exec("/var/www/cgi-bin/TH_Sensor $addr 1",$out,$GLOBALS['Temperature']);
		exec("/var/www/cgi-bin/TH_Sensor $addr 2",$out,$GLOBALS['Humidity']);
		if (!isset($_POST['set'])) {
			exec("/var/www/cgi-bin/TH_Sensor $addr 3",$out,$GLOBALS['TSet']);
			exec("/var/www/cgi-bin/TH_Sensor $addr 4",$out,$GLOBALS['HSet']);
			exec("/var/www/cgi-bin/TH_Sensor $addr 5",$out,$GLOBALS['FanSet']);
			exec("/var/www/cgi-bin/TH_Sensor $addr 6",$out,$GLOBALS['ModelSet']);
			exec("/var/www/cgi-bin/TH_Sensor $addr 7",$out,$GLOBALS['VentSet']);
		}
		
	}
	function sendMessage($addr, $T, $H, $fan_on_or_auto, $cooling_or_heater, $vent_status) {
		exec("/var/www/cgi-bin/TH_Sensor $addr 0 $T $H $fan_on_or_auto $cooling_or_heater $vent_status");
	}

	$cooling_status = 'unchecked';
	$heater_status = 'unchecked';
	$fan_status_on = 'unchecked';
	$fan_status_auto = 'unchecked';
	$temperature = $_POST['temperature'];
	$humidity = $_POST['humidity'];
	$selection_cool_heater = $_POST['AC_Status'];
	$fan_status = $_POST['FAN_Status'];
	$vent_status = $_POST['VENT_Status'];

	if (isset($_POST['set'])) {
		if ($selection_cool_heater == 'cooling') {
			$cooling_status = 'checked';
			$cooling_or_heater = 1;
		} else if ($selection_cool_heater == 'heater') {
			$heater_status = 'checked';
			$cooling_or_heater = 0;
		}

		if ($fan_status == 'on') {
			$fan_status_on = 'checked';
			$fan_on_or_auto = 1;
		} else if ($fan_status == 'auto') {
			$fan_status_auto = 'checked';
			$fan_on_or_auto = 0;
		}
	}

	if (isset($_GET['address'])) {
		$Tx_addr = $_GET['address'];
		if (isset($_GET['check'])) {
			checkStatus($Tx_addr);
		}
		if (isset($_GET['send'])) {
			sendMessage($Tx_addr, $temperature, $humidity, $fan_on_or_auto, $cooling_or_heater, $vent_status);
			sleep(3);
			checkStatus($Tx_addr);
		}
	}
?>
</head>
<body>
	<div style="width: 200px; margin: 0px auto;">
		<div style="width: 200px; float: left;">
			<p>Temperature: 
			  	<?php
			    	echo $GLOBALS['Temperature'];
			   	?>
			   	&#176;C
			<br>
				Humidity: 
				<?php
					echo $GLOBALS['Humidity'];
					echo "%";
				?>
			</p>
		</div>
		<form name="setting" action="TH_Sensor.php?address=<?php print $Tx_addr; ?>&send" method="post">
			T: <input type="number" name="temperature" min="16" max="36" value="<?php if(isset($_POST['set'])) {print $temperature;} else{print $GLOBALS['TSet'];} ?>">
			H: <input type="number" name="humidity" min="45" max="65" value="<?php if(isset($_POST['set'])) {print $humidity;} else{print $GLOBALS['HSet'];} ?>">
			<br>
			<Input type="Radio" name="AC_Status" value="cooling" <?php if(isset($_POST['set'])) {print $cooling_status;} else {if($GLOBALS['ModelSet'] == ord("1")) {print "checked";}}  ?>>COOL
			<Input type="Radio" name="AC_Status" value="heater" <?php if(isset($_POST['set'])) {print $heater_status;} else {if($GLOBALS['ModelSet'] == ord("0")) {print "checked";}}  ?>>HEAT
			<br>
			FAN: 
			<Input type="Radio" name="FAN_Status" value="on" <?php if(isset($_POST['set'])) {print $fan_status_on;} else {if($GLOBALS['FanSet'] == ord("1")) {print "checked";}}  ?>>ON
			<Input type="Radio" name="FAN_Status" value="auto" <?php if(isset($_POST['set'])) {print $fan_status_auto;} else {if($GLOBALS['FanSet'] == ord("0")) {print "checked";}} ?>>AUTO
			<br>
			<br>
			<?php echo "min<--VENT-->max"; ?>
			<br>
			<input type="range" name="VENT_Status" min="1" max="10" value="<?php if(isset($_POST['set'])) {print $vent_status;} else{print $GLOBALS['VentSet'];} ?>">
			<br>
			<input type="submit" name="set" value="Setting">
		</form>
		<div style="width: 100px; float: left;">
			<button type="button" onclick="location.href='TH_Sensor.php?address=<?php print $Tx_addr; ?>&check'">check</button>
		</div>
	</div>
</body>
</html>
