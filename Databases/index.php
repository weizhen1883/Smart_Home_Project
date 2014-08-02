<!DOCTYPE html>
<?php
    session_start();
    session_destroy();
?>
<html>
    <head>
    	<title>Smart Home System</title>
    	<style type="text/css">
    	</style>
    </head>

    <body>
    	<table width="100%" height = "100%">
    		<tr>
    			<td></td>
    			<td width="1024px" height="650px" >
    				<table width="100%" height="100%" background="source/background.jpg">
    					<tr height="10%">
    						<td width="35%" style="background-color:white">
                                <img src="source/Logo.png" height="65px"> ROBOLUTION LLC
                            </td>
    						<td></td>
    					</tr>
    					<tr>
    						<td width="35%">
    							<table width="100%" height="100%">
    								<tr height="40%"></tr>
    								<tr height="40%">
    									<td align="center">
    										<form name="userLogin" action="checklogin.php" method="post">
    											username: <input type="text" name="username" id="username"><br>
    											password: <input type="password" name="psw" id="psw"><br>
    											<?php
    												if(isset($_GET['Login_fail'])){
    													echo '<span style="color:red; text-align:center;">wrong username or password!</span>';
    												}
    											?><br>
    											<input type="submit" name="Login" value="Login">
    										</form>
    									</td>
    								</tr>
    								<tr></tr>
    							</table>
    						</td>
    						<td></td>
    					</tr>
    				</table>
    			</td>
    			<td></td>
    		</tr>
    	</table>
    </body>
</html>

