<!DOCTYPE html>
<?php
    session_start();
    if(isset($_SESSION['username'])){
        $username = $_SESSION['username'];
    }else{
        header("location:index.php");
    }
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
                                <img src="source/Logo.png" height="65px"> ROBOLUTION TECH LLC
                            </td>
    						<td></td>
    					</tr>
    					<tr>
    						<td width="35%">
    							<table width="100%" height="100%">
    								<tr height="30%"></tr>
    								<tr height="70%">
                                        <td align="left" valign="top">
                                            <p><?php echo "Welcome $username"; ?> 
                                                <button type="button" onclick="location.href='logout.php'" style="float:right">Logout</button>
                                            </p>
                                            <hr>
                                            <?php
                                                if(isset($_GET['devices'])){
                                                    include("device_menu.php");
                                                }else if(isset($_GET['socket_and_switch'])){
                                                    include("socket_switch_menu.php");
                                                }else{
                                                    if($_SESSION['permission']==1){
                                                        include("administrator_menu.php"); 
                                                    }else{
                                                        include("user_menu.php");
                                                    }
                                                }
                                            ?>
                                        </td>
    								</tr>
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

