<?php
	//a php for sign up

    if (empty($_POST)) {
		header("Location:signup.html");  
		exit();
    }
	
	include_once('conn.php');
	$userName = mysqli_real_escape_string($conn, $_POST['userName']);
	$password = mysqli_real_escape_string($conn, $_POST['password']);
	$confirmPassword = mysqli_real_escape_string($conn, $_POST['confirmPassword']);
	
	if ($password != $confirmPassword) //check whether 2 passwords are same
	{
		exit('<br />password not same!<br /><a href="javascript:history.back(-1);">return</a>'); 
	}

	//create new user
	if(mysqli_query($conn, "INSERT INTO `user` VALUES (null, '$userName','$password', null);"))
	{
		$userID = mysqli_insert_id($conn);

		echo("<br />Your userID is $userID.	(please remember it!)<br />");
		session_start();  
		$_SESSION['userid'] = $userID;  
		$_SESSION['username'] = $userName;  
		echo('<a href="user.php">user center</a><br />');
		echo('<a href="login.html">return to login</a><br />');
	}
	else
	{
		exit('<br />database error!<br /><a href="javascript:history.back(-1);">return</a>'); 
	}

?>