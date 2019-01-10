<br /><a href="addfriend.php">return to addfriend list</a><br /><br />

<?php
	//a php for friend request
	
    include_once('conn.php');
	session_start();
	if(!isset($_SESSION['userid']))
	{
		header("Location:login.html");  
		exit();
	}
	$userid = $_SESSION['userid'];
	
	
	if (empty($_GET)) {
		header("Location:addfriend.php");  
		exit();
	}
	
	//check if the same request exits
	$targetid = mysqli_real_escape_string($conn, $_GET['target']);	
	$sql_req = "select * 
			from friendrequest 
			where friendrequest.userid_sender='$userid' and friendrequest.userid_receiver='$targetid';";
	$query_req = mysqli_query($conn, $sql_req);  
	
	if(mysqli_fetch_assoc($query_req))
	{
		echo('already sent a request!<br /><a href="addfriend.php">return</a>');
	}
	else
	{	
		//sign up UI
		echo '<br /><form name="request_form" method="post" action="addfriend.php">';
		echo '<table>';
		echo "<tr><td>targetid:</td><td>$targetid</td></tr>";
		echo '<tr><td>input text</td><td><textarea name="text" rows="5"  maxlength="50" onchange="this.value=this.value.substring(0, 50)" 
							onkeydown="this.value=this.value.substring(0, 50)" onkeyup="this.value=this.value.substring(0, 50)"></textarea></td></tr>';
		echo '<tr><td><input type="submit" name="request" class="left" /></td></tr>';
		echo '<input type="hidden" name="target" value="'.$targetid.'" /> ';
		echo'</table>';
		echo'</form>';
	}
?>