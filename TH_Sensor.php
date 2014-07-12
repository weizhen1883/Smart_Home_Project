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
	}
	function sendMessage($addr, $T, $H, $cooling_or_heater) {
		exec("/var/www/cgi-bin/TH_Sensor $addr 0 $T $H $cooling_or_heater");
	}

	$cooling_status = 'unchecked';
	$heater_status = 'unchecked';
	$temperature = $_POST['temperature'];
	$humidity = $_POST['humidity'];
	$selection_cool_heater = $_POST['AC_Status'];

	if (isset($_POST['set'])) {
		if ($selection_cool_heater == 'cooling') {
			$cooling_status = 'checked';
			$cooling_or_heater = 1;
		} else if ($selection_cool_heater == 'heater') {
			$heater_status = 'checked';
			$cooling_or_heater = 0;
		}
	}

	if (isset($_GET['address'])) {
		$Tx_addr = $_GET['address'];
		if (isset($_GET['check'])) {
			checkStatus($Tx_addr);
		}
		if (isset($_GET['send'])) {
			sendMessage($Tx_addr, $temperature, $humidity, $cooling_or_heater);
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
		<form name="setting" action="TH_Sensor.php?address=0xDEADBEEF02&send" method="post">
			T: <input type="number" name="temperature" min="16" max="36" value="<?php print $temperature; ?>">
			H: <input type="number" name="humidity" min="45" max="65" value="<?php print $humidity; ?>">
			<br>
			<Input type = 'Radio' Name ='AC_Status' value= 'cooling' <?PHP print $cooling_status; ?>>COOL
			<Input type = 'Radio' Name ='AC_Status' value= 'heater' <?PHP print $heater_status; ?>>HEAT
			<br>
			<input type="submit" name="set" value="Setting">
		</form>
		<div style="width: 100px; float: left;">
			<button type="button" onclick="location.href='TH_Sensor.php?address=0xDEADBEEF02&check'">check</button>
		</div>
	</div>
</body>
</html>
