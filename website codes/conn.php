<?php      

	$conn = mysqli_connect('localhost','root','') or die("connect failed".mysql_error());
		
	//set utf
	mysqli_set_charset($conn,'utf8');
		
	//use database
	mysqli_select_db($conn,'oingo'); 
	
	function trySQLs($con, $SQLArray)
	{
		mysqli_begin_transaction($con);
		$num= 1;
		foreach($SQLArray as $SQL)
		{
			$res=mysqli_query($con, $SQL);  	
			if(!$res)
			{
				mysqli_query($con, "ROLLBACK");
				$num=0;
				break;
			}
		}
		mysqli_commit($con);
		if($num)
		{
			return ' successful!';
		}
		else
		{
			return ' failure!';
		}
	}

?>