<br /><a href="user.php">return to user center</a><br /><br />

<?php  
	//a php for view all available notes
	
	include_once('conn.php');  
	session_start();    
	if(!isset($_SESSION['userid']))
	{
		header("Location:login.html");  
		exit();
	}
	$userid = $_SESSION['userid'];  	
	
	date_default_timezone_set("EST");
	$nowdate=date('Y-m-d',time()); 
	$nowtime=date('H:i:s',time());
	$latitude=116.0;
	$longitude=39.0;
	$content="";

	if (!empty($_POST)) //repost with "search" button
	{
		$nowdate=$_POST['nowdate'];
		$nowtime=$_POST['nowtime'];
		$latitude=mysqli_real_escape_string($conn, $_POST['latitude']);
		$longitude=mysqli_real_escape_string($conn, $_POST['longitude']);
		$content=mysqli_real_escape_string($conn, $_POST['content']);
	}
?>

	Please set your current information
	<form name="check_note_form" method="post" action="viewnote.php">
		<table>
			<tr>
				<td>date</td><td><input type='date' name='nowdate' value='<?php echo $nowdate ?>'/></td>
				<td>time</td><td><input type='time' name='nowtime' value='<?php echo $nowtime ?>'/></td>
			</tr>
			<tr>
				<td>latitude</td><td><input type='text' name='latitude' style="width:140px" value='<?php echo htmlspecialchars($latitude) ?>' maxlength='40'></td>
				<td>longiude</td><td><input type='text' name='longitude' style="width:140px" value='<?php echo htmlspecialchars($longitude) ?>' maxlength='40'></td>
			</tr>
			<tr>
				<td>search content</td>
				<td><input type='text' name='content' style="width:200px" value='<?php echo htmlspecialchars($content) ?>' maxlength='40'></td>
			</tr>
			<tr>
				<td><input type="submit" name="search" value="search"/></td>
			</tr>
		</table>
	</form>
	<br /><br />

<?php
	if (!empty($_POST)) //show search result
	{
		//create search part of sql
		$additionSearch='';
		# $additionSearch=  and (note.title REGEXP concat_ws('|' ,"buy" ,"food") or note.words REGEXP concat_ws('|' ,"buy" ,"food"))
		if($content!="")
		{
			$contents=explode(" ",$content);
			$additionSearch=" and (note.title REGEXP concat_ws('|' ";
			$additionSearch_part2="";
			foreach($contents as $w)
			{
				$additionSearch_part2.=',"'.$w.'"';
			}
			$additionSearch.=$additionSearch_part2.')';
			$additionSearch.=" or note.words REGEXP concat_ws('|' " . $additionSearch_part2.'))';
		}
		
		//get all availble notes through filters
		$sql_view_result="
		select distinct user.username,note.noteid, note.title, note.words, note.latitude, note.longitude 
		from (
			select distinct tag.tagid
			from filter, filterSchedule, user , filter_tag, tag 
			where 
				filter_tag.filterid=filter.filterid and tag.tagid=filter_tag.tagid 
				and filter.userid=user.userid and filter.userid=$userid and filter.filterid=filterSchedule.filterid 
				and (user.state=filter.state or filter.state = '') 
				and (filterSchedule.type=1 
				or (filterSchedule.type=2 and '$nowdate $nowtime' between filterSchedule.starttime and filterSchedule.endtime)
				or (filterSchedule.type=3 and weekday('$nowdate $nowtime')=filterSchedule.weekday and time('$nowdate $nowtime') between time(filterSchedule.starttime) and time(filterSchedule.endtime))
				)
				and (filter.radius=0 or ROUND(6378.138*2*ASIN(SQRT(POW(SIN((filter.latitude*PI()/180-$latitude*PI()/180)/2),2)+COS(filter.latitude*PI()/180)*COS($latitude*PI()/180)*POW(SIN((filter.longitude*PI()/180-$longitude*PI()/180)/2),2)))*1000)<filter.radius)
		)F, note, schedule ,user ,tag, note_tag 
		where 
			user.userid= note.userid 
			and note.noteid=note_tag.noteid and tag.tagid=note_tag.tagid and tag.tagid=F.tagid 
			and note.noteid=schedule.noteid $additionSearch 
			and (note.accessRes!=0 or note.userid=$userid)
			and (schedule.type=1 
			or (schedule.type=2 and '$nowdate $nowtime' between schedule.starttime and schedule.endtime)
			or (schedule.type=3 and weekday('$nowdate $nowtime')=schedule.weekday and time('$nowdate $nowtime') between time(schedule.starttime) and time(schedule.endtime))
			)
			and (note.radius=0 or ROUND(6378.138*2*ASIN(SQRT(POW(SIN((note.latitude*PI()/180-$latitude*PI()/180)/2),2)+COS(note.latitude*PI()/180)*COS($latitude*PI()/180)*POW(SIN((note.longitude*PI()/180-$longitude*PI()/180)/2),2)))*1000)<note.radius)
		;";
		
		//show note list
		echo"available note list:";
		echo "<table width='400' border='1' style='text-align:center'>";
		echo "<tr><td>user</td><td>title</td><td>content</td></tr>";
		
		$query_view_result = mysqli_query($conn, $sql_view_result); 
		if($query_view_result)
		{
			$num = 0;
			while($result = mysqli_fetch_assoc($query_view_result))
			{
				$num+=1;
				echo "<tr>";
					echo "<td>".htmlspecialchars($result['username'])."</td>";
					#echo "<td>u</td>";
					echo "<td><a href='note.php?noteid=". $result['noteid']."'>".htmlspecialchars($result['title'])."</a></td>";
					#echo "<td>t</td>";
					echo "<td>".htmlspecialchars($result['words'])."</td>";

				echo "</tr>";
			}
			echo "</table>";
			if($num==0)
			{
				echo('<br />No available notes.<br />');
			} 	
		}
		else
		{
			echo "</table>";
			echo 'error';
		}
		
		
	}
	
	//note list for test
	echo"<br/><br/>every note's information for testing:";
	echo "<table border='1' style='text-align:center'>";
	echo "<tr><td>noteid</td><td>userid</td><td>title</td><td>content</td><td>schedule</td><td>location</td><td>tag</td></tr>";
	$sql_view_test="select * from note, user where note.userid=user.userid order by note.noteid;";
	$query_view_test = mysqli_query($conn, $sql_view_test);

	if($query_view_test)
	{
		$num = 0;
		while($result_view_test = mysqli_fetch_assoc($query_view_test))
		{
			$num+=1;
			echo "<tr>";
				echo "<td>".htmlspecialchars($result_view_test['noteid'])."</td>";
				echo "<td>".htmlspecialchars($result_view_test['userid'])."</td>";
				echo "<td>".htmlspecialchars($result_view_test['title'])."</td>";
				echo "<td>".htmlspecialchars($result_view_test['words'])."</td>";
				//show schedule
				$sql_view_test_schedule="select * from note, schedule where schedule.noteid=note.noteid and note.noteid={$result_view_test['noteid']} ;";
				$query_view_test_schedule = mysqli_query($conn, $sql_view_test_schedule); 
				$result_view_test_schedule = mysqli_fetch_assoc($query_view_test_schedule);
				$schedule_test="";
				if($result_view_test_schedule['type']==1)
				{
					$schedule_test="always";
				}
				else if($result_view_test_schedule['type']==2)
				{
					$schedule_test="from ".$result_view_test_schedule['starttime']." to ".$result_view_test_schedule['endtime'];
				}
				else
				{
					$XQ = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday','Sunday' );
					$schedule_test.="each ".$XQ[intval($result_view_test_schedule['weekday'])];
					$ss=explode(" ",$result_view_test_schedule['starttime'])[1];
					$ee=explode(" ",$result_view_test_schedule['endtime'])[1];
					$schedule_test.=" from ".$ss." to ".$ee;
				}
				echo "<td>".htmlspecialchars($schedule_test)."</td>";
				
				echo "<td>(".htmlspecialchars($result_view_test['latitude'])." , ". htmlspecialchars($result_view_test['longitude']).")</td>";
				
				//show tag
				$tag="";
				$sql_tag="select tag.tagname from note_tag, tag where note_tag.noteid={$result_view_test['noteid']} and note_tag.tagid=tag.tagid;";
				$query_tag = mysqli_query($conn, $sql_tag);
				while($result_tag = mysqli_fetch_assoc($query_tag))
				{
					$tag=$tag.$result_tag['tagname'].' ';
				}
				$tag=rtrim($tag, " ");
				echo "<td>".htmlspecialchars($tag)."</td>";
			echo "</tr>";
		}
		echo "</table>";
		if($num==0)
		{
			echo('<br />No available notes.<br />');
		} 	
	}
	else
	{
		echo "</table>";
		echo 'error';
	}
?>