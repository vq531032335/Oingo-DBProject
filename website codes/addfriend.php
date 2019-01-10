<br /><a href="friend.php">return to friendlist</a><br /><br />

<?php
	//a php for search friend
	
    include_once('conn.php');
	session_start();
	if(!isset($_SESSION['userid']))
	{
		header("Location:login.html");  
		exit();
	}
	$userid = $_SESSION['userid'];
	
	
	//handle different post functions 
	$search = "";
	if (!empty($_POST)) {
		
		//search button
		if(isset($_POST["searchName"]))
		{
			$search = mysqli_real_escape_string($conn, $_POST["searchName"]);
		}
		
		//send friend request
		if(isset($_POST["target"]))
		{
			$targetid = mysqli_real_escape_string($conn, $_POST['target']);
			$text = mysqli_real_escape_string($conn, $_POST['text']);
			$sql = "INSERT INTO `friendrequest` VALUES ($userid, $targetid, '$text');";
			mysqli_query($conn, $sql);
			echo 'sent friend request successful!';
		}
    }
	echo'<br /><br />';
	
	//search UI
	echo '<form action="addfriend.php" method="POST">';
	echo 'input  <input type="text" name="searchName" class="input" placeholder='.htmlspecialchars($search).'>';
	echo '<input type="submit" name="search" value="search"/>';
	echo '</form>';	

	//do the search
	$sql = "select userid, username 
			from user 
			where (user.userid='$search' or user.username like '%$search%') and userid!='$userid' and userid not in(
			select userid_receiver 
			from friendship 
			where userid='$userid'
			);";
	$query_search = mysqli_query($conn, $sql);  
	
	//show the search result as a user list
	echo "<br /><table width='400' border='1'>";	
	echo "<tr><td> userid </td><td> username </td><td></td></tr>";	
	while($result_search = mysqli_fetch_assoc($query_search))
	{
		echo "<tr>";
		echo "<td>{$result_search['userid']}</td>";
		echo "<td><a href='user.php?userid=". $result_search['userid']."'>".htmlspecialchars($result_search['username'])."</a></td>";
		echo '<td><a href="friendrequest.php?target='.$result_search['userid'].'">add friend</a></td>';
		echo "</tr>";
	}
	echo "</table>";
?>




