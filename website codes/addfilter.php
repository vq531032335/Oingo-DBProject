<?php  
	//a php for add/update filter
	
	include_once('conn.php');  
	session_start();    
	if(!isset($_SESSION['userid']))
	{
		header("Location:login.html");  
		exit();
	}
	$userid = $_SESSION['userid'];  

		
	$filterid=-1;
	$mode='add';
	$filtername="";
	$state="";
	$tag="";
	$latitude="";
	$longitude="";
	$radius="";
	date_default_timezone_set("EST");
	$startdate=date("Y-m-d");
	$starttime=date("H:i:s");
	$enddate=date("2018-12-25");
	$endtime=date("23:59:59");
	$scheduletype=1;
	$weekday=0;
	if (!empty($_POST)) 
	{//read filter data when update
		//var_dump($_POST);
		if($_POST['handle']=='update')
		{
			$filterid = mysqli_real_escape_string($conn, $_POST['id']);
			$mode='update';
						
			//get filter info
			$filter_query = mysqli_query($conn, "select * from filter where filterid='$filterid' LIMIT 1;");
			$result = mysqli_fetch_assoc($filter_query);
			$filtername=$result['filtername'];
			$state=$result['state'];
			$latitude=$result['latitude'];
			$longitude=$result['longitude'];
			$radius=$result['radius'];
			
			//get tag text of the filter
			$sql="select tag.tagname from filter_tag, tag where filter_tag.filterid='$filterid' and filter_tag.tagid=tag.tagid;";
			$tag_query = mysqli_query($conn, $sql);
			while($result = mysqli_fetch_assoc($tag_query))
			{
				$tag=$tag.$result['tagname'].' ';
			}
			$tag=rtrim($tag, " ");
			
			//get all schedules of the filter
			$schedule_query = mysqli_query($conn, "select * from filterSchedule where filterid='$filterid';");
			if($result=mysqli_fetch_assoc($schedule_query))
			{
				$startdt=$result['starttime'];
				$enddt=$result['endtime'];
				$scheduletype=$result['type'];
				$weekday=$result['weekday'];
				$start=explode(" ",$startdt);
				$end=explode(" ",$enddt);
				$startdate=$start[0];
				$starttime=$start[1];
				$enddate=$end[0];
				$endtime=$end[1];
			}
		}
    }	


?>

<br /><a href="filter.php">return to filter list</a><br /><br />
<form name="add_filter_form" method="post" action="filter.php">
	<table>
		<tr>
			<td>filtername</td>
			<td><input type="text" name="filtername"  value="<?php echo htmlspecialchars($filtername) ?>"/></td>
		</tr>
		<tr>
			<td>state</td>
			<td><input type="text" name="state"  value="<?php echo htmlspecialchars($state) ?>"/></td>
		</tr>
		<tr>
			<td>tag</td>
			<td><input type="text" name="tag"  value="<?php echo htmlspecialchars($tag) ?>"/></td>
		</tr>
		<tr>
			<td>latitude</td>
			<td><input type="text" name="latitude" value="<?php echo htmlspecialchars($latitude) ?>"/></td>
		</tr>
		<tr>
			<td>longitude</td>
			<td><input type="text" name="longitude" value="<?php echo htmlspecialchars($longitude) ?>"/></td>
		</tr>
		<tr>
			<td>radius</td>
			<td><input type="text" name="radius" value="<?php echo htmlspecialchars($radius) ?>"/></td>
		</tr>
		
		<tr>
			<td>schedule type</td>
			<td><select id="schtype" name="schtype">
				<option value="1"<?php if($scheduletype=="1"){ echo "selected";}?>>always</option>
				<option value="2"<?php if($scheduletype=="2"){ echo "selected";}?>>time period</option>
				<option value="3"<?php if($scheduletype=="3"){ echo "selected";}?>>weekdays</option>
			</select></td>
			
			<td>    weekday</td>
			<td><select id="weekday" name="weekday">
				<option value="0"<?php if($weekday=="0"){ echo "selected";}?>>Monday</option>
				<option value="1"<?php if($weekday=="1"){ echo "selected";}?>>Tuesday</option>
				<option value="2"<?php if($weekday=="2"){ echo "selected";}?>>Wednesday</option>
				<option value="3"<?php if($weekday=="3"){ echo "selected";}?>>Thursday</option>
				<option value="4"<?php if($weekday=="4"){ echo "selected";}?>>Friday</option>
				<option value="5"<?php if($weekday=="5"){ echo "selected";}?>>Saturday</option>
				<option value="6"<?php if($weekday=="6"){ echo "selected";}?>>Sunday</option>
			</select></td>
		</tr>
		<tr>
			<td>start time</td>
			<td><input type="date" name="startdate" value="<?php echo $startdate ?>"/></td>
			<td><input type="time" name="starttime" value="<?php echo $starttime ?>"/></td>
		</tr>
		<tr>
			<td>end time</td>
			<td><input type="date" name="enddate" value="<?php echo $enddate ?>"/></td>
			<td><input type="time" name="endtime" value="<?php echo $endtime ?>"/></td>
		</tr>
	</table>

	<input type="hidden" name="handle" value="<?php echo $mode ?>"/>
	<input type="hidden" name="id" value="<?php echo $filterid ?>"/>
	<input type="submit" name="submit" value="<?php echo $mode ?>"/>	
</form>
	
	