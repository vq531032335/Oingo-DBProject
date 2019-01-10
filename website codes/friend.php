<br /><a href="user.php">return to user center</a><br /><br />

<?php
	//a php for user's friend list

	include_once('conn.php');
	session_start();
	
	if(!isset($_SESSION['userid']))
	{
		header("Location:login.html");  
		exit();
	}
	$userid = $_SESSION['userid'];

	
	if (!empty($_POST))//handle different post functions 
	{
		$targetid = $_POST['id'];	
		
		//delete a friend
		if($_POST['handle']=='delete')
		{
			$sql1 = "DELETE FROM `friendship` where userid='$targetid' and userid_receiver='$userid';";
			$sql2 = "DELETE FROM `friendship` where userid='$userid' and userid_receiver='$targetid';";
			trySQLs($conn, [$sql1,$sql2]);
		}
		
		//accept or reject a friend request
		if($_POST['handle']=='accept'||$_POST['handle']=='reject')
		{
			$sql = "DELETE FROM `friendrequest` where userid_sender='$targetid' and userid_receiver='$userid';";
			$sql1 = "INSERT INTO `friendship` VALUES ('$userid', '$targetid');";
			$sql2 = "INSERT INTO `friendship` VALUES ('$targetid', '$userid');";
			if($_POST['handle']=='accept')
			{
				echo'accept friend request'.trySQLs($conn, [$sql, $sql1,$sql2]);
			}
			else//reject
			{
				echo'reject friend request'.trySQLs($conn, [$sql]);
			}
		}
    }
	echo'<br /><br />';
	
	//show friend list
	echo"your friend list:";
	echo "<table width='250' border='1' style='text-align:center'>";
	echo "<tr><td>userid</td><td>username</td><td></td></tr>";
	$sql_friends = "select user.userid, user.username from user,friendship where friendship.userid = '$userid' and friendship.userid_receiver=user.userid;";
	$query_friends = mysqli_query($conn, $sql_friends);  
	$num = 0;
	while($result_friends = mysqli_fetch_assoc($query_friends))
	{
		$num+=1;
		echo "<tr>";
			echo "<td>{$result_friends['userid']}</td>";
			echo "<td><a href='user.php?userid=". $result_friends['userid']."'>".htmlspecialchars($result_friends['username'])."</a></td>";
			echo '<td><form action="friend.php" method="POST">
			<input type="submit" name="handle" value="delete"/>
			<input type="hidden" name="id" value="'.$result_friends['userid'].'"/>
			</form></td>';
		echo "</tr><br />";
	}
	echo "</table>";
	if($num==0)
	{
		echo('You have no friends.<br />');
	}
	
	//add friends button
	echo '<a href="addfriend.php">add new friend</a><br /><br /><br />';  
	

	//show friend request list
	echo "your friendship request list:<br />";
	echo "<table width='500' height='50' border='1' style='text-align:center'>";
	echo "<tr><td>userid</td><td>username</td><td width='200'>text</td><td></td><td></td></tr>";
	$sql_requests = "select user.userid, user.username, friendrequest.words from user,friendrequest 
			where friendrequest.userid_receiver ='$userid' and user.userid=friendrequest.userid_sender;";
	$query_requests = mysqli_query($conn, $sql_requests);  
	$num = 0;
	while($result_requests = mysqli_fetch_assoc($query_requests))
	{
		$num+=1;
		echo "<tr>";
			echo "<td>{$result_requests['userid']}</td>";
			echo "<td><a href='user.php?userid=". $result_requests['userid']."'>".htmlspecialchars($result_requests['username'])."</a></td>";
			echo "<td>".htmlspecialchars($result_requests['words'])."</td>";
			echo '<td><form action="friend.php" method="POST">
			<input type="submit" name="handle" value="accept"/>
			<input type="hidden" name="id" value="'.$result_requests['userid'].'"/>
			</form></td>';
			
			echo '<td><form action="friend.php" method="POST">
			<input type="submit" name="handle" value="reject"/>
			<input type="hidden" name="id" value="'.$result_requests['userid'].'"/>
			</form></td>';			
		echo "</tr><br />";
	}
	echo "</table>";
	if($num==0)
	{
		echo('You have no friend request.');
	}
?>