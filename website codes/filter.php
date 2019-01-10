<br /><a href="user.php">return to user center</a><br /><br />

<?php  
	//a php for user's filter list
	
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
		$filterid = mysqli_real_escape_string($conn, $_POST['id']);
		
		//remove old filter
		if($_POST['handle']=='delete' || $_POST['handle']=='update')
		{			
			$sql1 = "DELETE FROM `filter_tag` where filterid='$filterid';";
			$sql2 = "DELETE FROM `filterSchedule` where filterid='$filterid';";
			$sql3 = "DELETE FROM `filter` where filterid='$filterid';";
			trySQLs($conn, [$sql1,$sql2,$sql3]);
		}
		//insert new filter
		if($_POST['handle']=='add' || $_POST['handle']=='update')
		{
			$name=mysqli_real_escape_string($conn, $_POST['filtername']);
			$state=mysqli_real_escape_string($conn, $_POST['state']);
			$latitude=mysqli_real_escape_string($conn, $_POST['latitude']);
			$longitude=mysqli_real_escape_string($conn, $_POST['longitude']);
			$radius=mysqli_real_escape_string($conn, $_POST['radius']);
			$tagtext=mysqli_real_escape_string($conn, $_POST['tag']);
			if($tagtext!="")
			{
				$sql = "INSERT INTO `filter` VALUES ('$filterid', '$userid', '$name', '$state', '$latitude', '$longitude', '$radius');";
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
						mysqli_query($conn, "INSERT INTO `filter_tag` VALUES ($filterid, $tagid);");
					}
					
					//associate schedule with filter
					$schtype=$_POST['schtype'];
					$weekday=$_POST['weekday'];
					$startdate=$_POST['startdate'];
					$starttime=$_POST['starttime'];
					$enddate=$_POST['enddate'];
					$endtime=$_POST['endtime'];
					if($schtype==1)
					{
						mysqli_query($conn, "INSERT INTO `filterSchedule` VALUES ('$filterid', 1, '2018-10-1 0:00:00', '2018-10-1 23:59:59', 0);");
					}
					if($schtype==2)
					{
						mysqli_query($conn, "INSERT INTO `filterSchedule` VALUES ('$filterid', 2, '$startdate $starttime', '$enddate $endtime', 0);");
					}
					if($schtype==3)
					{
						mysqli_query($conn, "INSERT INTO `filterSchedule` VALUES ('$filterid', 3, '2018-10-1 $starttime', '2018-10-1 $endtime', $weekday);");
					}							
					mysqli_commit($conn);
				}
				else
				{
					echo ($_POST['handle']." failure! Your filter does not exist due to wrong inputs.");
				}
			}
			else
			{
					echo ($_POST['handle']." failure! Your filter does not exist due to no tags.");
			}
		}
		else
		{
			echo ($_POST['handle']." successful!");
		}
    }
	echo ("<br /><br />");

	//show filter list
	echo"your filter list:";
	echo "<table width='400' border='1' style='text-align:center'>";
	echo "<tr><td>filtername</td><td>state</td><td></td><td></td></tr>";
	$filter_query = mysqli_query($conn, "select * from filter where userid = '$userid'"); 
	$num = 0;
	while($result = mysqli_fetch_assoc($filter_query))
	{
		$num+=1;
		echo "<tr>";
			echo "<td>".htmlspecialchars($result['filtername'])."</td>";
			echo "<td>".htmlspecialchars($result['state'])."</td>";
			echo '<td><form action="addfilter.php" method="POST">
			<input type="submit" name="handle" value="update"/>
			<input type="hidden" name="id" value="'.$result['filterid'].'"/>
			</form></td>';
			echo '<td><form action="filter.php" method="POST">
			<input type="submit" name="handle" value="delete"/>
			<input type="hidden" name="id" value="'.$result['filterid'].'"/>
			</form></td>';
		echo "</tr>";
		echo "<br />";
	}
	echo "</table>";
	if($num==0)
	{
		echo('You have no filters.<br />');
	}
	echo '<a href="addfilter.php">add new filter</a>';  

?>