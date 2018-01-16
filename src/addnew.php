<?php
	
	session_start();
	if(array_key_exists('username',$_SESSION))
	{
		print_r($_SESSION);
	}
	else
	{
		header("Location: index.php");
	}

?>