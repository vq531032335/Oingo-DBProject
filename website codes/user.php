<?php  

//关于note_tag和filter_tag中db顺序问题
	//a php for user's control center
	
		
	include_once('conn.php');  
	session_start();  
	
	$userid = 0;
	$state="";
	
	$visitormode = 0;
	$visitorid = "";
	$visitusername = "";
	$visitstate = "";
	if(isset($_POST['userid'])&& isset($_POST['password']))//first login from 'login.html'
	{  
		$userid = mysqli_real_escape_string($conn, $_POST['userid']);  
		$password = mysqli_real_escape_string($conn, $_POST['password']); 
		
		//check user id and password
		$sql_check_user="select userid from user where userid='$userid';";
		$query_check_user = mysqli_query($conn, $sql_check_user); 
		if(mysqli_fetch_assoc($query_check_user))	//check whether userid exists
		{
			$sql_check_pwd="select username from user where userid='$userid' and userpwd='$password';";
			$query_check_pwd = mysqli_query($conn, $sql_check_pwd); 
			if($result_check_pwd = mysqli_fetch_assoc($query_check_pwd))//check whether password is correct 
			{
				$_SESSION['userid'] = $userid;  
				$_SESSION['username'] = $result_check_pwd['username'];
				
				$currenttime=date('Y-m-d H:i:s',time());
				mysqli_query($conn, "INSERT INTO `stamps` VALUES ('$userid', '$currenttime', 0,0);"); //caution
				
				
				echo '<br />'.htmlspecialchars($_SESSION['username']).' welcome! <br />';
				$visitormode = 0;//visit user's own page
			} else {  
				exit('<br />wrong password!<br /><a href="javascript:history.back(-1);"><br />return to login</a>');  
			} 
		}
		else {
			exit('<br />user not exist!<br /><a href="javascript:history.back(-1);"><br />return to login</a>');
		}
		
	}
	else
	{
		if(isset($_SESSION['userid']))// a user
		{
			if(isset($_GET['userid']))//visit a certain user page
			{
				if($_SESSION['userid']==$_GET['userid'])//visit user's own page
				{
					$visitormode = 0;
				}
				else//visit other's page
				{
					$visitormode = 1;
					$visitorid = $_GET['userid'];
					echo '<a href="friend.php"><br />return to friend list</a>';
				}
			}
			else
			{
				$visitormode = 0;
			}
		}
		else// a visitor
		{
			if(isset($_GET['userid']))//visit a certain user page
			{	
				$visitormode = 1;
				$visitorid = $_GET['userid'];
			}
			else//need to login first
			{
				header("Location:login.html");  
				exit();  
			}
		}
	}
	
	//get user information for visitor mode
	if($visitormode==1)
	{
		$query_visit = mysqli_query($conn, "select * from user where userid = ".$visitorid." limit 1;");
		if($result_visit = mysqli_fetch_assoc($query_visit))
		{
			$visitusername=$result_visit['username'];
			$visitstate=$result_visit['state'];
		}
		else
		{
			exit('<br />userid does not exist!<br /><a href="javascript:history.back(-1);"><br />return to login</a>'); 
		}
	}
	
	//get state
	if (isset($_POST['change'])) // use change state button from 'user.php'
	{
		$state = mysqli_real_escape_string($conn,$_POST['state']);

		if(mysqli_query($conn, "UPDATE user SET state='$state' where userid = '{$_SESSION['userid']}';"))
		{	
			
			echo "change state to '".htmlspecialchars($state)."' successful!";
		}
		else
		{
			echo "fail to change state!";
		}
	}
	else //get state from db
	{
		if(isset( $_SESSION['userid']))
		{
			if($query_state = mysqli_query($conn, "select state from user where userid = '{$_SESSION['userid']}' limit 1;"))
			{
				$result = mysqli_fetch_assoc($query_state);
				$state=$result['state'];	
			}
		}
	}
	echo "<br /><br />";
	
?>


<table>
	<tr>
		<td>user information:</td>
	</tr>
	<tr>
		<td>user ID:</td><td><?php if($visitormode==1){echo $visitorid;}else{echo $_SESSION['userid'];} ?></td>
	</tr>
	<tr>
		<td>user name:</td><td><?php if($visitormode==1){echo htmlspecialchars($visitusername);}else{echo htmlspecialchars($_SESSION['username']);} ?></td>
	</tr>
	<tr>
		<td>user state:</td>
		<?php
			if($visitormode==1)
			{				
				echo "<td>".htmlspecialchars($visitstate)."</td>";
				exit();
			}
		?>
		<form action="user.php" method="POST">
			<td><input type="text" style="width:100px; height:20px;" maxlength="40" name="state" value="<?php echo htmlspecialchars($state) ?>"/></td>
			<td><input type="submit" name="change" value="change"/></td>
		</form>
	</tr>
</table>
<br/><br/>
<table border='1' width='200' style='text-align:center'>
	<tr>
		<td>all functions</td>
	</tr>
	<tr>
		<td><a href="friend.php">friend list</a></td>
	</tr>
	<tr>
		<td><a href="filter.php">filter list</a></td>
	</tr>
	<tr>
		<td><a href="notelist.php">note list</a></td>
	</tr>
	<tr>
		<td><a href="viewnote.php">view all available notes</a></td>
	</tr>
</table>
<br/><a href="logout.php">log out</a>
	
	
	
	
	
	
	