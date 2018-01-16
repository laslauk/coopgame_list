<?php
	session_start();
	if(array_key_exists("logout", $_GET))
		{
			unset($_SESSION['username']);

			//setcookie("username","",time()-60*60);
			//$_COOKIE['username'] = "";
			header("Location: index.php");
		}


?>