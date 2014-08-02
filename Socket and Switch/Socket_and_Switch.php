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
		exec("/var/www/cgi-bin/Socket_and_Switch $addr 1",$out,$return);
		if ($return == "1") {
			exec("/var/www/cgi-bin/Socket_and_Switch $addr 1",$out,$return);
			if ($return == "1") $GLOBALS['status'] = 1;
			else checkStatus($addr);
        } else if ($return == "0") {
        	exec("/var/www/cgi-bin/Socket_and_Switch $addr 1",$out,$return);
			if ($return == "0") $GLOBALS['status'] = 0;
			else checkStatus($addr);
        } else { 
        	checkStatus($addr);
        }
	}
	function sendMessage($addr, $mesg) {
		exec("/var/www/cgi-bin/Socket_and_Switch $addr 0 $mesg");
	}

	if (isset($_GET['address'])) {
		$Tx_addr = $_GET['address'];
		if (isset($_GET['check'])) {
			checkStatus($Tx_addr);
		}
		if (isset($_GET['onOff'])) {
			$onOff = $_GET['onOff'];
			sendMessage($Tx_addr, $onOff);
			if ($onOff == 1) $status = 1;
			else $status = 0;
		}
	}
?>
</head>
<body>
	<div style="width: 200px; margin: 0px auto;">
		<div style="width: 100px; float: left;">
			<p>Light: 
			  <?php
			     if ($status == 1) echo "ON";
			     else echo "OFF";
			   ?>
			</p>
			<button type="button" onclick="location.href='Socket_and_Switch.php?address=<?php print $Tx_addr; ?>&onOff=1'">ON</button>
			<button type="button" onclick="location.href='Socket_and_Switch.php?address=<?php print $Tx_addr; ?>&onOff=0'">OFF</button>
			<button type="button" onclick="location.href='Socket_and_Switch.php?address=<?php print $Tx_addr; ?>&check'">check</button>
		</div>
	</div>
</body>
</html>
