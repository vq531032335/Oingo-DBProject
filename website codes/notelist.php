<br /><a href="user.php">return to user center</a><br /><br />

<?php  
	//a php for user's note list
	
	include_once('conn.php');  
	session_start();    
	if(!isset($_SESSION['userid']))
	{
		header("Location:login.html");  
		exit();
	}
	$userid = $_SESSION['userid'];  
	
	
	//repost functions
	if (!empty($_POST)) {
		$noteid = mysqli_real_escape_string($conn, $_POST['id']);
		//remove old note
		if($_POST['handle']=='delete' || $_POST['handle']=='update')
		{			
			$sql1 = "DELETE FROM `note_tag` where noteid='$noteid';";
			$sql2 = "DELETE FROM `schedule` where noteid='$noteid';";
			$sql3 = "DELETE FROM `comments` where noteid='$noteid';";
			$sql4 = "DELETE FROM `note` where noteid='$noteid';";

			trySQLs($conn, [$sql1, $sql2, $sql3, $sql4]);		

		}
		//insert new note
		if($_POST['handle']=='add' || $_POST['handle']=='update')
		{
			//$name=$_POST['notename'];
			//$state=$_POST['state'];
			$title=mysqli_real_escape_string($conn, $_POST['title']);
			$text=mysqli_real_escape_string($conn, $_POST['words']);
			$latitude=mysqli_real_escape_string($conn, $_POST['latitude']);
			$longitude=mysqli_real_escape_string($conn, $_POST['longitude']);
			$radius=mysqli_real_escape_string($conn, $_POST['radius']);
			$tagtext=mysqli_real_escape_string($conn, $_POST['tag']);
			$comAllow=0;
			if(isset($_POST['comAllow']))
			{
				$comAllow=1;
			}
			$accessRes=0;
			if(isset($_POST['accessRes']))
			{
				$accessRes=1;
			}
			if($tagtext!="")
			{
				$sql = "INSERT INTO `note` VALUES ('$noteid', '$userid', '$title', '$text', '$latitude', '$longitude', '$radius','$comAllow','$accessRes');";
				if(mysqli_query($conn, $sql))
				{
					echo ($_POST['handle']." successful!");
					mysqli_begin_transaction($conn);
					
					//add all the related tags if not exist yet
					$tag_list=preg_split("/[\s,]+/",$tagtext);
					foreach($tag_list as $tag)
					{
						$tag_query=mysqli_query($conn, "select tagid from tag where tagname='$tag' LIMIT 1");
						if($result=mysqli_fetch_assoc($tag_query))//tag already exist
						{
							$tagid=$result['tagid'];
						}
						else
						{
							mysqli_query($conn, "INSERT INTO `tag` VALUES (null, '$tag')");
							$tagid=mysqli_insert_id($conn);
						}
						mysqli_query($conn, "INSERT INTO `note_tag` VALUES ($tagid,$noteid);");
					}					
					//associate schedule with note
					$schtype=$_POST['schtype'];
					$weekday=$_POST['weekday'];
					$startdate=$_POST['startdate'];
					$starttime=$_POST['starttime'];
					$enddate=$_POST['enddate'];
					$endtime=$_POST['endtime'];
					if($schtype==1)
					{
						mysqli_query($conn, "INSERT INTO `schedule` VALUES ('$noteid', 1, '2018-10-1 0:00:00', '2018-10-1 23:59:59', 0);");
					}
					if($schtype==2)
					{
						mysqli_query($conn, "INSERT INTO `schedule` VALUES ('$noteid', 2, '$startdate $starttime', '$enddate $endtime', 0);");
					}
					if($schtype==3)
					{
						mysqli_query($conn, "INSERT INTO `schedule` VALUES ('$noteid', 3, '2018-10-1 $starttime', '2018-10-1 $endtime', $weekday);");
					}							
					mysqli_commit($conn);
				}
				else
				{
					echo ($_POST['handle']." failure! Your note does not exist due to wrong inputs.");
				}
			}
			else
			{
				echo ($_POST['handle']." failure! Your note does not exist due to no tags.");
			}
			
		}
		else
		{
			echo ($_POST['handle']." successful!");
		}
    }
	echo ("<br /><br />");

	//show note list
	echo"your note list:";
	echo "<table width='400' border='1' style='text-align:center'>";
	echo "<tr><td>title</td><td>content</td><td></td><td></td></tr>";
	$note_query = mysqli_query($conn, "select * from note where userid = '$userid'"); 
	$num = 0;
	while($result = mysqli_fetch_assoc($note_query))
	{
		$num+=1;
		echo "<tr>";
			echo "<td><a href='note.php?noteid=". $result['noteid']."'>".htmlspecialchars($result['title'])."</a></td>";
			echo "<td>".htmlspecialchars($result['words'])."</td>";
			echo '<td><form action="addnote.php" method="POST">
			<input type="submit" name="handle" value="update"/>
			<input type="hidden" name="id" value="'.$result['noteid'].'"/>
			</form></td>';
			echo '<td><form action="notelist.php" method="POST">
			<input type="submit" name="handle" value="delete"/>
			<input type="hidden" name="id" value="'.$result['noteid'].'"/>
			</form></td>';
		echo "</tr>";
		echo "<br />";
	}
	echo "</table>";
	if($num==0)
	{
		echo('You have no notes.<br />');
	}
	echo '<a href="addnote.php">add new note</a>';  

?>