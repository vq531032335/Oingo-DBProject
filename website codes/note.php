<br /><a href="user.php">return to user center</a><br /><br />

<?php  
	//a php for a certain note
	
	include_once('conn.php');  
	session_start();
	
	if(empty($_GET))//return to login without noteid
	{
		header("Location:login.html");  
	}
	
	//get userid
	$userid="";
	if(isset($_SESSION['userid']))
	{
		$userid = $_SESSION['userid'];  
	}
	
	//get note info
	$noteid=mysqli_real_escape_string($conn, $_GET['noteid']);
	$sql_note="select * from note,user 
	where user.userid=note.userid and noteid='$noteid';";
	$query_note = mysqli_query($conn, $sql_note);
	$result_note = mysqli_fetch_assoc($query_note);
	if(!$result_note)
	{
		header("Location:user.php"); 
	}
	$noteuserid=$result_note['userid'];
	$noteusername=$result_note['username'];
	$title=$result_note['title'];
	$text=$result_note['words'];
	$latitude=$result_note['latitude'];
	$longitude=$result_note['longitude'];
	$radius=$result_note['radius'];
	$comAllow=$result_note['comAllow'];
	$accessRes=$result_note['accessRes'];
	
	$tag="";
	$sql_tag="select tag.tagname from note_tag, tag where note_tag.noteid='$noteid' and note_tag.tagid=tag.tagid;";
	$query_tag = mysqli_query($conn, $sql_tag);
	while($result_tag = mysqli_fetch_assoc($query_tag))
	{
		$tag=$tag.$result_tag['tagname'].' ';
	}
	$tag=rtrim($tag, " ");
	
	if($accessRes==0 && $noteuserid!=$userid)
	{
		exit('This note is not allowed for others to view!');
	}
	
	//handle comments function
	if(!empty($_POST))
	{
		if($userid!="")
		{
			if($comAllow==1)
			{
				$comwords=mysqli_real_escape_string($conn, $_POST['words']);
				date_default_timezone_set("EST");
				$currenttime=date('Y-m-d H:i:s',time());
				mysqli_query($conn, "INSERT INTO `comments` VALUES ('$userid', '$noteid', '$currenttime', '$comwords');");
				echo 'comment successful!';
			}
			else
			{
				echo 'comment not allowed!';
			}
		}
		else
		{
			header("Location:login.html");  
			exit();
		}

	}
	
?>



<table border='1' width='300'>
	<tr>
		<td>by:<?php echo htmlspecialchars($noteusername) ?><br />title: <?php echo htmlspecialchars($title) ?><br />tag: <?php echo htmlspecialchars($tag) ?><br /><br />content: <br /><?php echo htmlspecialchars($text) ?></td>
	</tr>
</table><br /><br />comments:


<?php
	// show comment list
	$sql_comments ="select user.username, comments.words 
		from comments,user 
		where user.userid=comments.userid and comments.noteid='$noteid' 
		order by comments.comtime;";		

	$query_comments = mysqli_query($conn, $sql_comments);	
	while($result_comments = mysqli_fetch_assoc($query_comments))
	{
		$comusername=$result_comments['username'];
		$comtext=$result_comments['words'];
		echo "<table border='1' width='300'>";
		echo "<tr><td>$comusername<br /><br />content: <br />".htmlspecialchars($comtext)."</td></tr>";
		echo "</table><br /><br />";
	}
		

?>

<form name="mm" method="post" action="note.php?noteid=<?php echo $noteid?>">
	<textarea name="words" value="" rows="5" cols="40"  maxlength="50" onchange="this.value=this.value.substring(0, 50)" 
				onkeydown="this.value=this.value.substring(0, 50)" onkeyup="this.value=this.value.substring(0, 50)"></textarea><br />
	<input type="submit" name="comment" value="comment"/>
</form>
