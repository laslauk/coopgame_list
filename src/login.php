<?php
if($_POST)
	{
		//Form Validation
		function clean_input($data) 
		{
		  $data = trim($data);
		  $data = stripslashes($data);
		  $data = htmlspecialchars($data);
		  return $data;
		}

		$password = clean_input($_POST['password']);
		$username = clean_input($_POST['username']);


		if(strlen($password) >= 500 OR strlen($username) >= 500)
		{
			 $error_message .= "Field too long"; 
		}

		if(!$username) 
		{
			$error_message .= "Username is required.<br>"; 
		}

		if (!$password)
		{
			$error_message .= "Password is required.<br>";
		}

		if (!preg_match("/^[a-zA-Z ]*$/",$username)) {
			$error_message .= "Only letters and whitespace is allowed.<br>"; 
		}

		//check if no errors in input
		if($error_message == "")
		{

			$link = mysqli_connect("LOGIN PARAMS HERE");

			if(mysqli_connect_errno())
			{
				echo "Error connecting to database";
				exit();
			}
			$query = "SELECT `password` FROM `users` WHERE `username` = '".mysqli_escape_string($link,$username)."'";	
			$result = mysqli_query($link,$query);

			if(mysqli_num_rows($result) > 0 )
			{
				$row = mysqli_fetch_array($result);
				if(array_key_exists("password", $row))
				{
					$passhash = $row['password'];
					$inputpassword = mysqli_escape_string($link,$password);

					if (password_verify(mysqli_real_escape_string($link,$inputpassword), $passhash))
					{
						$_SESSION['username'] = mysqli_real_escape_string($link,$username);

						
					}
					else
					 {
						echo 'Invalid password.';
					}

				}
			}
			else
			{
				echo "Bad login data";
			}
		}
	}

			else
			{
				$error_message = "Error(s) in your form: ".$error_message;
			}

?>