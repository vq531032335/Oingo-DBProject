<?php  
	//a php for add/update note
	
	include_once('conn.php');  
	session_start();    
	if(!isset($_SESSION['userid']))
	{
		header("Location:login.html");  
		exit();
	}
	$userid = $_SESSION['userid'];  

		
	$noteid=-1;
	$mode='add';
	$title="";
	$text="";

	$latitude="";
	$longitude="";
	$radius="";
	$comAllow=0;
	$accessRes=0;
	date_default_timezone_set("EST");
	$startdate=date("Y-m-d");
	$starttime=date("H:i:s");
	$enddate=date("2018-12-25");
	$endtime=date("23:59:59");
	$scheduletype=1;
	$weekday=0;
	if (!empty($_POST)) 
	{//read note data when update
		//var_dump($_POST);
		if($_POST['handle']=='update')
		{
			$noteid = mysqli_real_escape_string($conn, $_POST['id']);
			$mode='update';
						
			//get note info
			$note_query = mysqli_query($conn, "select * from note where noteid='$noteid' LIMIT 1;");
			$result = mysqli_fetch_assoc($note_query);
			$title=$result['title'];
			$text=$result['words'];
			$latitude=$result['latitude'];
			$longitude=$result['longitude'];
			$radius=$result['radius'];
			$comAllow='checked="on"';
			if($result['comAllow']==0){$comAllow='';}
			$accessRes='checked="on"';
			if($result['accessRes']==0){$accessRes='';}
			//get tag text of the note
			$tag="";
			$sql_tag="select tag.tagname from note_tag, tag where note_tag.noteid='$noteid' and note_tag.tagid=tag.tagid;";
			$query_tag = mysqli_query($conn, $sql_tag);
			while($result_tag = mysqli_fetch_assoc($query_tag))
			{
				$tag=$tag.$result_tag['tagname'].' ';
			}
			$tag=rtrim($tag, " ");
			
			//get all schedules of the note
			$schedule_query = mysqli_query($conn, "select * from schedule where noteid='$noteid';");
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

<br /><a href="notelist.php">return to note list</a><br /><br />
<form name="add_note_form" method="post" action="notelist.php">
	<table>
		<tr>
			<td>title</td>
			<td><input type="text" name="title"  value="<?php echo htmlspecialchars($title) ?>"/></td>
		</tr>
		<tr>
			<td>content</td>
			<td><textarea name="words" value="<?php echo htmlspecialchars($text) ?>" rows="5" cols="22"  maxlength="50" onchange="this.value=this.value.substring(0, 50)" 
				onkeydown="this.value=this.value.substring(0, 50)" onkeyup="this.value=this.value.substring(0, 50)"><?php echo $text ?></textarea></td>
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
			<td>allow comments</td>
			<td><input type="checkbox" name="comAllow" <?php echo $comAllow ?>/></td>
		</tr>
		<tr>
			<td>allow others view</td>
			<td><input type="checkbox" name="accessRes" <?php echo $accessRes ?>/></td>
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
	<input type="hidden" name="id" value="<?php echo $noteid ?>"/>
	<input type="submit" name="submit" value="<?php echo $mode ?>"/>	
</form>
	
	